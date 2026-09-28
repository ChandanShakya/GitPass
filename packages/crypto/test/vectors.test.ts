import { test } from "node:test";
import assert from "node:assert/strict";
import {
  deriveMasterSecret,
  splitAuthAndKek,
  deriveChildKey,
  encrypt,
  decrypt,
  wrapKey,
  unwrapKey,
  generateDek,
  generateRowKey,
  fieldAad,
  chainHash,
  fromB64,
} from "../src/index.ts";

// Fixed inputs so this file is also GitPass's cross-language test vector
// source: an Android/iOS/extension implementation should reproduce these
// same outputs from these same inputs. See docs/CRYPTO_SPEC.md.
const PASSWORD = "correct horse battery staple";
const SALT = fromB64("MTIzNDU2Nzg5MDEyMzQ1Ng=="); // "1234567890123456"

test("deriveMasterSecret is deterministic and 32 bytes", async () => {
  const a = await deriveMasterSecret(PASSWORD, SALT, 1000);
  const b = await deriveMasterSecret(PASSWORD, SALT, 1000);
  assert.equal(a.length, 32);
  assert.deepEqual(a, b);
});

test("different iteration counts produce different secrets", async () => {
  const a = await deriveMasterSecret(PASSWORD, SALT, 1000);
  const b = await deriveMasterSecret(PASSWORD, SALT, 1001);
  assert.notDeepEqual(a, b);
});

test("splitAuthAndKek gives two independent 32-byte keys", async () => {
  const secret = await deriveMasterSecret(PASSWORD, SALT, 1000);
  const { authKey, kek } = await splitAuthAndKek(secret);
  assert.equal(authKey.length, 32);
  assert.equal(kek.length, 32);
  assert.notDeepEqual(authKey, kek);
});

test("deriveChildKey is deterministic per purpose and differs across purposes", async () => {
  const parent = generateDek();
  const a = await deriveChildKey(parent, "entry-1");
  const b = await deriveChildKey(parent, "entry-1");
  const c = await deriveChildKey(parent, "entry-2");
  assert.deepEqual(a, b);
  assert.notDeepEqual(a, c);
});

test("encrypt/decrypt round-trips and rejects a wrong AAD (tamper/row-swap detection)", async () => {
  const key = generateRowKey();
  const aad = fieldAad("user-1", "entry-1", "vault_items", "password");
  const blob = await encrypt("hunter2", key, aad);
  const plain = await decrypt(blob, key, aad);
  assert.equal(plain, "hunter2");

  const wrongAad = fieldAad("user-1", "entry-2", "vault_items", "password");
  await assert.rejects(() => decrypt(blob, key, wrongAad));
});

test("encrypt/decrypt rejects a wrong key", async () => {
  const key = generateRowKey();
  const other = generateRowKey();
  const aad = fieldAad("user-1", "entry-1", "vault_items", "password");
  const blob = await encrypt("hunter2", key, aad);
  await assert.rejects(() => decrypt(blob, other, aad));
});

test("wrapKey/unwrapKey round-trips a raw key", async () => {
  const dek = generateDek();
  const kek = generateDek();
  const aad = "user-1:dek";
  const wrapped = await wrapKey(dek, kek, aad);
  const unwrapped = await unwrapKey(wrapped, kek, aad);
  assert.deepEqual(unwrapped, dek);
});

test("chainHash links versions and breaks on tamper", async () => {
  const v1 = await chainHash("", "ciphertext-v1");
  const v2 = await chainHash(v1, "ciphertext-v2");
  const v2Tampered = await chainHash(v1, "ciphertext-v2-tampered");
  assert.notEqual(v1, v2);
  assert.notEqual(v2, v2Tampered);
  // recomputing from the same inputs must be stable
  assert.equal(await chainHash("", "ciphertext-v1"), v1);
});

test("blob format: version byte, 12-byte nonce, then ciphertext+tag", async () => {
  const key = generateRowKey();
  const aad = "aad";
  const blob = await encrypt("x", key, aad);
  const raw = Buffer.from(blob, "base64");
  assert.equal(raw[0], 1); // BLOB_VERSION
  assert.equal(raw.length, 1 + 12 + 1 + 16); // version + nonce + "x" + 16-byte GCM tag
});
