import {
  deriveMasterSecret,
  splitAuthAndKek,
  deriveChildKey,
  generateDek,
  generateRowKey,
  wrapKey,
  unwrapKey,
  encrypt,
  decrypt,
  fieldAad,
  toB64,
  fromB64,
  randomBytes,
  PBKDF2_ITERATIONS,
} from "@gitpass/crypto";

const CROCKFORD = "0123456789ABCDEFGHJKMNPQRSTVWXYZ"; // no I, L, O, U — matches the Figma recovery-key look

/** A GitPass recovery key: 20 random bytes, shown as `K7QM-2XPD-9TFA-HN4C-XW8Z`. */
export function generateRecoveryKey(): { raw: Uint8Array; formatted: string } {
  const raw = randomBytes(20);
  let bits = "";
  for (const b of raw) bits += b.toString(2).padStart(8, "0");
  let out = "";
  for (let i = 0; i + 5 <= bits.length; i += 5) out += CROCKFORD[parseInt(bits.slice(i, i + 5), 2)];
  const groups = out.match(/.{1,4}/g) ?? [];
  return { raw, formatted: groups.join("-") };
}

export interface UnlockKeys {
  authKey: Uint8Array;
  kek: Uint8Array;
}

/** Password + salt -> {authKey, kek}. Same call for register and login. */
export async function deriveUnlockKeys(password: string, salt: Uint8Array, iterations = PBKDF2_ITERATIONS): Promise<UnlockKeys> {
  const secret = await deriveMasterSecret(password, salt, iterations);
  return splitAuthAndKek(secret);
}

export interface NewVaultKeys {
  authKey: string; // base64, goes to the server
  kek: Uint8Array; // stays local
  dek: Uint8Array; // stays local, unwrapped
  wrappedDek: string; // goes to the server
  wrappedDekRecovery: string; // goes to the server
  recoveryKeyFormatted: string; // shown to the user once, never sent to the server
  salt: string; // base64, goes to the server (not secret — it's a KDF input, not a key)
}

/** The whole registration key setup: derive from password, mint a DEK, wrap it two ways. */
export async function setupNewVault(password: string): Promise<NewVaultKeys> {
  const salt = randomBytes(16);
  const { authKey, kek } = await deriveUnlockKeys(password, salt);
  const dek = generateDek();
  const recovery = generateRecoveryKey();
  const recoveryWrapKey = await deriveChildKey(recovery.raw, "recovery-wrap");

  const wrappedDek = await wrapKey(dek, kek, "dek");
  const wrappedDekRecovery = await wrapKey(dek, recoveryWrapKey, "dek-recovery");

  return {
    authKey: toB64(authKey),
    kek,
    dek,
    wrappedDek,
    wrappedDekRecovery,
    recoveryKeyFormatted: recovery.formatted,
    salt: toB64(salt),
  };
}

export function parseRecoveryKey(formatted: string): Uint8Array {
  const clean = formatted.replace(/[^0-9A-Z]/gi, "").toUpperCase();
  let bits = "";
  for (const ch of clean) {
    const v = CROCKFORD.indexOf(ch);
    if (v === -1) throw new Error("invalid recovery key character");
    bits += v.toString(2).padStart(5, "0");
  }
  const byteLen = Math.floor(bits.length / 8);
  const raw = new Uint8Array(byteLen);
  for (let i = 0; i < byteLen; i++) raw[i] = parseInt(bits.slice(i * 8, i * 8 + 8), 2);
  return raw;
}

/** Unwraps the DEK from a recovery key + the server's wrappedDekRecovery blob. */
export async function unlockWithRecoveryKey(formatted: string, wrappedDekRecovery: string): Promise<Uint8Array> {
  const raw = parseRecoveryKey(formatted);
  const recoveryWrapKey = await deriveChildKey(raw, "recovery-wrap");
  return unwrapKey(wrappedDekRecovery, recoveryWrapKey, "dek-recovery");
}

// ---------- entry-level encryption ----------

export interface EntryFields {
  title: string;
  username: string;
  password: string;
  url: string;
  notes: string;
}

export interface EncryptedEntry {
  wrappedRowKey: string;
  titleBlob: string;
  bodyBlob: string;
}

export async function encryptEntry(userId: string, entryId: string, dek: Uint8Array, fields: EntryFields): Promise<EncryptedEntry> {
  const rowKey = generateRowKey();
  const wrappedRowKey = await wrapKey(rowKey, dek, fieldAad(userId, entryId, "vault_items", "row_key"));
  const titleBlob = await encrypt(fields.title, rowKey, fieldAad(userId, entryId, "vault_items", "title"));
  const bodyBlob = await encrypt(
    JSON.stringify({ username: fields.username, password: fields.password, url: fields.url, notes: fields.notes }),
    rowKey,
    fieldAad(userId, entryId, "vault_items", "body"),
  );
  return { wrappedRowKey, titleBlob, bodyBlob };
}

export async function decryptEntry(
  userId: string,
  entryId: string,
  dek: Uint8Array,
  encrypted: { wrappedRowKey: string; titleBlob: string; bodyBlob: string },
): Promise<EntryFields> {
  const rowKey = await unwrapKey(encrypted.wrappedRowKey, dek, fieldAad(userId, entryId, "vault_items", "row_key"));
  const title = await decrypt(encrypted.titleBlob, rowKey, fieldAad(userId, entryId, "vault_items", "title"));
  const bodyJson = await decrypt(encrypted.bodyBlob, rowKey, fieldAad(userId, entryId, "vault_items", "body"));
  const body = JSON.parse(bodyJson) as { username: string; password: string; url: string; notes: string };
  return { title, ...body };
}

// Commit messages ("Rotated after breach") are encrypted directly under a
// DEK-derived key, scoped per entry — independent of the entry's row key,
// since messages are metadata about changes, not the changing data itself.
async function messageKey(userId: string, entryId: string, dek: Uint8Array): Promise<Uint8Array> {
  return deriveChildKey(dek, `${userId}:${entryId}:message`);
}
export async function encryptMessage(userId: string, entryId: string, dek: Uint8Array, message: string, version: number): Promise<string> {
  const key = await messageKey(userId, entryId, dek);
  return encrypt(message, key, fieldAad(userId, entryId, "item_versions", `v${version}`));
}
export async function decryptMessage(userId: string, entryId: string, dek: Uint8Array, blob: string, version: number): Promise<string> {
  const key = await messageKey(userId, entryId, dek);
  return decrypt(blob, key, fieldAad(userId, entryId, "item_versions", `v${version}`));
}

export { toB64, fromB64 };
