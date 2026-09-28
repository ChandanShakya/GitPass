# GitPass web rewrite — implementation plan

Branch `web` (old PHP app preserved on branch `php`, unchanged). Stack: SvelteKit on Cloudflare Pages, API on Cloudflare Workers + D1, shared crypto package. Web only for now — no extension, no mobile.

## 1. Why this stays $0

| Piece | Free tier limit | Our usage |
|---|---|---|
| Cloudflare Pages | Unlimited requests/bandwidth, 500 builds/month | 1 static SvelteKit build |
| Cloudflare Workers | 100,000 requests/day, 10ms CPU/request (free plan) | API is small JSON handlers, well under CPU cap |
| D1 | 5GB storage, 5M rows read/day, 100k rows written/day | Ciphertext blobs only; fine for personal/small-team use |
| Workers KV (optional, rate limiting) | 100k reads/day, 1k writes/day | Only if we add KV-based rate limiting; D1 alone can do it via a counter table, so KV is skippable |

No paid add-on is required for any of this. The one thing to watch: D1's 100k writes/day cap — each vault edit is one write, so this only matters at real scale, not for personal use or early users.

## 2. Repo layout (on `web` branch)

```
packages/
  crypto/          shared TS: KDF, HKDF, AES-GCM wrap/unwrap, blob encode/decode, test vectors
api/               Cloudflare Worker (Hono), D1 schema + migrations
web/               SvelteKit app (Pages), imports packages/crypto
docs/
  CRYPTO_SPEC.md   blob format, key chain, test vectors (source of truth, code must match it)
  API.md           OpenAPI-ish route list
  WEB_PLAN.md       this file
src/               old PHP app — left as-is for reference until `web` ships, then deleted
```
pnpm workspace (`pnpm-workspace.yaml`) ties `packages/crypto`, `api`, `web` together so the web app imports crypto by package name, not relative paths across folders.

## 3. Crypto (packages/crypto)

- KDF: Argon2id via `hash-wasm` (small, audited WASM build) run in a Web Worker — keeps the main thread free, hits the "snappy" goal.
- HKDF (Web Crypto native `crypto.subtle`) splits the KDF output into `authKey` (sent to server, re-hashed with Argon2id server-side) and `KEK` (never leaves the client).
- DEK: random 256-bit key, generated at sign-up, wrapped by KEK, stored wrapped.
- Per-entry row key: random 256-bit key, wrapped by DEK. Field values (username, password, notes, url) encrypted with AES-256-GCM under a key derived from the row key via HKDF, one sub-key per field so a field-level diff (the History/Compare screens) never needs the whole entry decrypted.
- AAD on every ciphertext: `${userId}:${entryId}:${table}:${column}` — binds ciphertext to its row so blobs can't be swapped between entries or users.
- Blob format: `version(1) | nonce(12) | ciphertext | tag(16)`, base64 for transport.
- `docs/CRYPTO_SPEC.md` ships fixed test vectors; `packages/crypto` has a Vitest suite asserting against them so the format can never silently drift.

## 4. API (Cloudflare Worker, `api/`)

- Router: Hono (tiny, built for Workers, typed).
- Auth: short-lived access JWT (15 min) + rotating refresh token stored in D1 (`sessions` table), so a stolen refresh token can be revoked. No third-party auth service — keeps it free and simple.
- Every route checks `userId` from the verified JWT against the row's `user_id` — no endpoint trusts a client-supplied id (this was the actual vulnerability in the old PHP app; the rewrite fixes it by construction, not by remembering to check).
- Routes (all payloads are opaque ciphertext blobs to the server):
  - `POST /auth/register`, `POST /auth/login`, `POST /auth/refresh`, `POST /auth/logout`
  - `GET /vault/items?since=<cursor>` — delta sync
  - `POST /vault/items`, `PATCH /vault/items/:id`, `DELETE /vault/items/:id` (soft delete → trash)
  - `GET /vault/items/:id/versions`, `POST /vault/items/:id/restore`
  - `GET /devices`, `DELETE /devices/:id`
- D1 schema: `users`, `sessions`, `vault_items` (blob + parent ids in plaintext for joins), `item_versions` (hash-chained: each row stores `prev_hash` + `hash` over its own ciphertext, so tampering breaks the chain), `devices`.
- Rate limiting: a `login_attempts` counter row per user/IP in D1, checked before auth routes run — no KV needed.

## 5. Web app (SvelteKit, `web/`)

- `adapter-cloudflare` (Pages), TypeScript, no CSS framework — tokens from the Figma file become CSS custom properties (`--gp-primary` etc.) in one `tokens.css`, components styled directly against them.
- Routes mirror the Figma screens 1:1: `/`, `/login`, `/register`, `/recovery-key`, `/unlock`, `/vault`, `/vault/favorites`, `/vault/[id]`, `/vault/new`, `/vault/[id]/history`, `/vault/[id]/compare`, `/trash`, `/generator`, `/health`, `/settings/security`, `/settings/devices`, `/settings/privacy`, `/admin`, `/admin/users/[id]`.
- State: encrypted IndexedDB cache (via `idb`) populated after unlock; vault list/search/filter all run in memory against the decrypted-in-worker cache — server round-trip only for sync.
- Unlock flow: master password → Argon2id (worker) → HKDF → authKey sent to `/auth/login`, KEK kept in a Svelte store backed by `sessionStorage` wrapper (cleared on tab close / explicit lock), never written to disk unencrypted.
- Auto-lock: idle timer (5 min default, matches the Figma copy) clears the in-memory KEK and DEK, redirects to `/unlock`.

## 6. Milestones (each one runnable and demoable)

1. **Scaffold** — pnpm workspace, `packages/crypto` with KDF/HKDF/AES-GCM + passing test vectors, empty Worker + empty SvelteKit app, both deployable to Cloudflare free tier, wired to real D1.
2. **Auth** — register, login, logout, refresh, session revocation. Recovery key generation and one-time display.
3. **Vault core** — create/read/update/delete an entry end-to-end encrypted, vault list screen, entry detail screen, matching the Figma design tokens/layout.
4. **History** — versioned writes, hash chain, Compare and Restore screens.
5. **Everything else** — Favorites, Trash, Generator, Health check, Settings (security/devices/privacy), Admin (metadata-only).
6. **Polish to the perf budgets** — cached cold open <300ms, search <16ms/keystroke, unlock ~0.3–0.5s, sync delta <200ms; Lighthouse/perf check in CI.

## 7. Immediate next step

Scaffold milestone 1: pnpm workspace + `packages/crypto` with real KDF/HKDF/AES-GCM code and test vectors, minimal Worker (`GET /health`) bound to a D1 database, minimal SvelteKit app with the design tokens wired in and a static Get-Started page. All three deployed to Cloudflare free tier and verified live before building auth.
