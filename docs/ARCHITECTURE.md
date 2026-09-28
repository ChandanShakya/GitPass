# Architecture

## System

```mermaid
flowchart LR
    subgraph Client["Browser (mobile-first, any device)"]
        UI["SvelteKit app"]
        Crypto["@gitpass/crypto<br/>(runs client-side)"]
        UI --> Crypto
    end

    subgraph CF["Cloudflare, free tier"]
        Pages["Cloudflare Pages<br/>static SvelteKit build"]
        Worker["Cloudflare Worker<br/>Hono API"]
        D1[("D1<br/>ciphertext blobs only")]
        Worker --> D1
    end

    UI -- "serves static assets" --- Pages
    UI -- "HTTPS, JSON, ciphertext only" --> Worker
```

Nothing here costs money at this scale: Pages (unlimited bandwidth), Workers (100k requests/day), D1 (5GB, 100k writes/day). See [DEPLOYMENT.md](DEPLOYMENT.md) for the exact numbers and how to deploy it.

## Request flow: reading the vault

```mermaid
sequenceDiagram
    participant U as User
    participant W as SvelteKit app
    participant C as @gitpass/crypto
    participant A as Worker API
    participant D as D1

    U->>W: enters master password
    W->>C: deriveUnlockKeys(password, salt)
    C-->>W: {authKey, kek}
    W->>A: POST /auth/login {email, authKey}
    A->>D: SELECT auth_hash, wrapped_dek WHERE email=?
    D-->>A: row
    A->>A: SHA256(authKey+serverSalt) == auth_hash?
    A-->>W: {accessToken, refreshToken, wrappedDek}
    W->>C: unwrapKey(wrappedDek, kek)
    C-->>W: DEK (kept in memory only)
    W->>A: GET /vault/items?since=0
    A->>D: SELECT * WHERE user_id=? AND updated_at>?
    D-->>A: rows (ciphertext blobs)
    A-->>W: rows
    W->>C: decryptEntry(dek, row) for each row
    C-->>W: plaintext fields
    W-->>U: renders the vault list
```

The server (`A`, `D`) only ever handles `authKey` (not the password) and ciphertext blobs. It cannot answer "what does this user's vault contain" even with full database access — see [CRYPTO_SPEC.md](CRYPTO_SPEC.md) for the key chain that makes this true.

## Data model

```mermaid
erDiagram
    users ||--o{ sessions : has
    users ||--o{ vault_items : owns
    vault_items ||--o{ item_versions : has

    users {
        text id PK
        text email
        text auth_hash
        text salt
        text wrapped_dek
        text wrapped_dek_recovery
    }
    sessions {
        text id PK
        text user_id FK
        text refresh_hash
        text device_name
    }
    vault_items {
        text id PK
        text user_id FK
        text wrapped_row_key
        text title_blob
        text body_blob
        int current_version
        int deleted_at
    }
    item_versions {
        text id PK
        text item_id FK
        int version
        text body_blob
        text message_blob
        text prev_hash
        text hash
    }
```

Every `*_blob` and `wrapped_*` column is ciphertext. `deleted_at` implements Trash (soft delete, 30-day window); `item_versions` implements the git-like history — see [CRYPTO_SPEC.md](CRYPTO_SPEC.md#tamper-evident-history) for the hash chain.

## Code layout

```
packages/crypto/   Zero-dependency Web Crypto wrapper: KDF, HKDF, AES-GCM, hash chain.
                    Shared by web today; by a future extension/mobile client later.
api/                Cloudflare Worker (Hono). Auth, vault CRUD, devices — all D1-backed.
web/                SvelteKit app, mobile-first, adapter-cloudflare (Pages).
docs/               This file, CRYPTO_SPEC.md, DEPLOYMENT.md, FEATURES.md, WEB_PLAN.md.
```

`packages/crypto` has no build step — `web` and `api` both import its `.ts` source directly (Vite and Wrangler each compile TypeScript on the fly), so there's one implementation, not one-per-consumer.

## Why this stack

See [WEB_PLAN.md](WEB_PLAN.md) for the full reasoning (Cloudflare vs. Laravel, PBKDF2 vs. Argon2id, Svelte vs. React). Short version: everything here is free at this scale, and the API is a thin ciphertext blob-store — a job Workers/D1/Hono do natively, with no server to keep warm or pay for.
