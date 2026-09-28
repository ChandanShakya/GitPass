-- GitPass D1 schema. Every column that could hold vault content is an
-- opaque ciphertext blob (base64 TEXT) — see packages/crypto for the format.
-- Only ids, timestamps, and foreign keys are plaintext, because joins and
-- cascade deletes need them; nothing here reveals what a row is *about*.

CREATE TABLE users (
  id            TEXT PRIMARY KEY,           -- uuid
  email         TEXT NOT NULL UNIQUE,
  auth_hash     TEXT NOT NULL,              -- Argon2id/PBKDF2 hash of the client's authKey (never the master password)
  salt          TEXT NOT NULL,              -- base64, used by the client to re-derive authKey/KEK
  kdf_iterations INTEGER NOT NULL DEFAULT 600000,
  wrapped_dek   TEXT NOT NULL,              -- DEK wrapped under the client's KEK; server can never unwrap it
  wrapped_dek_recovery TEXT NOT NULL,       -- same DEK, wrapped under the one-time recovery key instead
  created_at    INTEGER NOT NULL,
  failed_logins INTEGER NOT NULL DEFAULT 0,
  locked_until  INTEGER
);

CREATE TABLE sessions (
  id            TEXT PRIMARY KEY,           -- uuid, the refresh token's jti
  user_id       TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  device_name   TEXT NOT NULL,              -- plaintext, user-supplied label ("Chrome on Windows") — not secret
  refresh_hash  TEXT NOT NULL,              -- SHA-256 of the refresh token, so a DB read alone can't forge one
  created_at    INTEGER NOT NULL,
  last_seen_at  INTEGER NOT NULL,
  revoked_at    INTEGER
);
CREATE INDEX idx_sessions_user ON sessions(user_id);

CREATE TABLE vault_items (
  id            TEXT PRIMARY KEY,
  user_id       TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  category      TEXT NOT NULL DEFAULT 'login',
  favorite      INTEGER NOT NULL DEFAULT 0,
  wrapped_row_key TEXT NOT NULL,            -- this entry's row key, wrapped under the DEK
  title_blob    TEXT NOT NULL,              -- site/service name, encrypted
  body_blob     TEXT NOT NULL,              -- {username, password, url, notes} JSON, encrypted as one field
  current_version INTEGER NOT NULL DEFAULT 1,
  created_at    INTEGER NOT NULL,
  updated_at    INTEGER NOT NULL,
  deleted_at    INTEGER                     -- soft delete -> Trash; purged 30 days after this is set
);
CREATE INDEX idx_vault_items_user ON vault_items(user_id, deleted_at);

CREATE TABLE item_versions (
  id            TEXT PRIMARY KEY,
  item_id       TEXT NOT NULL REFERENCES vault_items(id) ON DELETE CASCADE,
  version       INTEGER NOT NULL,
  body_blob     TEXT NOT NULL,              -- encrypted snapshot at this version
  message_blob  TEXT NOT NULL,              -- encrypted short commit-style message ("Rotated after breach")
  prev_hash     TEXT NOT NULL,              -- '' for version 1
  hash          TEXT NOT NULL,              -- sha256(prev_hash || body_blob), see chainHash() in @gitpass/crypto
  device_name   TEXT NOT NULL,
  created_at    INTEGER NOT NULL,
  UNIQUE (item_id, version)
);
CREATE INDEX idx_item_versions_item ON item_versions(item_id);

CREATE TABLE login_attempts (
  key           TEXT PRIMARY KEY,           -- `${ip}:${email}` — cheap in-D1 rate limiting, no KV needed
  count         INTEGER NOT NULL DEFAULT 0,
  window_start  INTEGER NOT NULL
);
