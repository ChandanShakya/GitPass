/**
 * GitPass zero-knowledge crypto core.
 *
 * Runs identically in the browser, a Web Worker, and Cloudflare Workers —
 * everything here uses only the standard Web Crypto API (`crypto.subtle`),
 * no WASM, no npm crypto dependency. See docs/CRYPTO_SPEC.md for the blob
 * format and the key chain this implements.
 *
 * KDF note: the design calls for Argon2id long-term. This ships PBKDF2
 * (native to Web Crypto, zero dependencies) with a high iteration count as
 * the v1 KDF — it is a straight drop-in swap later (see `deriveMasterSecret`)
 * once an audited WASM Argon2id build is worth the dependency weight.
 */

const subtle = globalThis.crypto.subtle;

export const BLOB_VERSION = 1;
export const PBKDF2_ITERATIONS = 600_000;

// ---------- encoding ----------

export function toB64(bytes: Uint8Array): string {
  let bin = "";
  for (const b of bytes) bin += String.fromCharCode(b);
  return btoa(bin);
}

export function fromB64(b64: string): Uint8Array {
  const bin = atob(b64);
  const bytes = new Uint8Array(bin.length);
  for (let i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
  return bytes;
}

export function randomBytes(n: number): Uint8Array {
  return globalThis.crypto.getRandomValues(new Uint8Array(n));
}

const enc = new TextEncoder();
const dec = new TextDecoder();

// ---------- KDF: password -> intermediate secret ----------

/** PBKDF2-SHA256(password, salt) -> 32 raw bytes. Swap point for Argon2id. */
export async function deriveMasterSecret(
  password: string,
  salt: Uint8Array,
  iterations = PBKDF2_ITERATIONS,
): Promise<Uint8Array> {
  const passKey = await subtle.importKey("raw", enc.encode(password), "PBKDF2", false, ["deriveBits"]);
  const bits = await subtle.deriveBits(
    { name: "PBKDF2", salt: salt as BufferSource, iterations, hash: "SHA-256" },
    passKey,
    256,
  );
  return new Uint8Array(bits);
}

// ---------- HKDF: split/expand a secret into purpose-scoped keys ----------

async function hkdfExpand(ikm: Uint8Array, info: string, length: number): Promise<Uint8Array> {
  const key = await subtle.importKey("raw", ikm as BufferSource, "HKDF", false, ["deriveBits"]);
  const bits = await subtle.deriveBits(
    { name: "HKDF", hash: "SHA-256", salt: new Uint8Array(0), info: enc.encode(info) },
    key,
    length * 8,
  );
  return new Uint8Array(bits);
}

export interface AuthAndKek {
  /** Sent to the server; the server re-hashes it and never learns the KEK. */
  authKey: Uint8Array;
  /** Never leaves the client. Wraps the vault's DEK. */
  kek: Uint8Array;
}

/** Master secret -> {authKey, kek}, two independent 32-byte keys via HKDF. */
export async function splitAuthAndKek(masterSecret: Uint8Array): Promise<AuthAndKek> {
  const [authKey, kek] = await Promise.all([
    hkdfExpand(masterSecret, "gitpass:auth", 32),
    hkdfExpand(masterSecret, "gitpass:kek", 32),
  ]);
  return { authKey, kek };
}

/** Derives a child key from a parent key for a named purpose (row key, field key, ...). */
export function deriveChildKey(parentKey: Uint8Array, purpose: string): Promise<Uint8Array> {
  return hkdfExpand(parentKey, "gitpass:child:" + purpose, 32);
}

// ---------- AES-256-GCM: the one encryption primitive everything else uses ----------

async function importAesKey(rawKey: Uint8Array): Promise<CryptoKey> {
  return subtle.importKey("raw", rawKey as BufferSource, "AES-GCM", false, ["encrypt", "decrypt"]);
}

/**
 * Encrypts `plaintext` under `rawKey`, binding `aad` (additional authenticated
 * data — e.g. `${userId}:${entryId}:${table}:${column}`) so the ciphertext
 * cannot be moved to a different row without decryption failing.
 * Output: version(1) | nonce(12) | ciphertext+tag, base64-encoded.
 */
export async function encrypt(plaintext: string, rawKey: Uint8Array, aad: string): Promise<string> {
  const key = await importAesKey(rawKey);
  const nonce = randomBytes(12);
  const ct = await subtle.encrypt(
    { name: "AES-GCM", iv: nonce as BufferSource, additionalData: enc.encode(aad) },
    key,
    enc.encode(plaintext),
  );
  const out = new Uint8Array(1 + 12 + ct.byteLength);
  out[0] = BLOB_VERSION;
  out.set(nonce, 1);
  out.set(new Uint8Array(ct), 13);
  return toB64(out);
}

/** Inverse of {@link encrypt}. Throws if the AAD, key, or ciphertext don't match (tampered or wrong row). */
export async function decrypt(blob: string, rawKey: Uint8Array, aad: string): Promise<string> {
  const bytes = fromB64(blob);
  const version = bytes[0];
  if (version !== BLOB_VERSION) throw new Error(`unsupported blob version ${version}`);
  const nonce = bytes.slice(1, 13);
  const ct = bytes.slice(13);
  const key = await importAesKey(rawKey);
  const pt = await subtle.decrypt(
    { name: "AES-GCM", iv: nonce as BufferSource, additionalData: enc.encode(aad) },
    key,
    ct as BufferSource,
  );
  return dec.decode(pt);
}

/** Wraps a raw key (e.g. a DEK or row key) the same way as any other secret — AES-GCM under the parent key. */
export async function wrapKey(rawKey: Uint8Array, wrappingKey: Uint8Array, aad: string): Promise<string> {
  return encrypt(toB64(rawKey), wrappingKey, aad);
}

export async function unwrapKey(blob: string, wrappingKey: Uint8Array, aad: string): Promise<Uint8Array> {
  return fromB64(await decrypt(blob, wrappingKey, aad));
}

// ---------- high-level vault key chain ----------

/** A fresh 256-bit data-encryption key for a vault. */
export function generateDek(): Uint8Array {
  return randomBytes(32);
}

/** A fresh 256-bit row key for one vault entry. */
export function generateRowKey(): Uint8Array {
  return randomBytes(32);
}

/** AAD builder — keep this call the same everywhere so encrypt/decrypt always agree. */
export function fieldAad(userId: string, entryId: string, table: string, column: string): string {
  return `${userId}:${entryId}:${table}:${column}`;
}

// ---------- hash chain for tamper-evident history ----------

/** SHA-256(prevHash || ciphertextBlob), hex-encoded. First version uses prevHash = "" (genesis). */
export async function chainHash(prevHash: string, ciphertextBlob: string): Promise<string> {
  const bytes = enc.encode(prevHash + ciphertextBlob);
  const digest = await subtle.digest("SHA-256", bytes);
  return Array.from(new Uint8Array(digest)).map((b) => b.toString(16).padStart(2, "0")).join("");
}
