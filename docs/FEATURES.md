# Features

Screens follow the Figma design (`docs/WEB_PLAN.md` links the file), built mobile-first: every screen is designed for a 375px phone first, then a sidebar/wider layout is added on top at 1440px — not the other way around.

## ✅ Working end-to-end today

These are real: client-side encryption, a live Cloudflare Worker API, D1 storage, and UI — not mocked. Verified by an automated end-to-end check that registers a user, logs in, creates/edits/lists/decrypts an entry, walks its version history, and confirms the server's stored rows are ciphertext (see `docs/DEPLOYMENT.md` for how to run it yourself).

- **Create account** — master password with a strength meter, "we cannot recover this" warning, one honest sentence instead of fine print.
- **Recovery key** — a 24-character key (`T54E-DBZ4-YSB4-…`) generated client-side, shown once, downloadable as a `.txt`. It independently unwraps the vault's key — losing the master password doesn't mean losing the vault.
- **Sign in / Unlock** — master password → local key derivation → server never sees the password, only a derived `authKey`.
- **Forgot password** — recovery key restores access and lets you set a new master password, without losing any data (the DEK is re-wrapped, never regenerated).
- **Vault list** — search, empty state with a clear first action, entries decrypted in the browser after fetch.
- **Add / Edit entry** — title, username, password (with a generator), URL, notes. Every save is a new version, never an overwrite.
- **Entry detail** — masked password with reveal/copy, 20-second clipboard auto-clear, link to full history.
- **History** — every version, oldest-to-newest chain hash shown, one-click Restore that creates a *new* version instead of rewriting the old one.
- **Trash** — soft-deleted entries with a days-remaining badge, Restore.
- **Password generator** — length slider, character-class toggles, fully client-side.
- **Devices** — list of signed-in sessions with a name and last-seen time, per-device sign-out (revokes that session's refresh token server-side).
- **Privacy page** — the actual plain-language "what we collect / what we can see" copy from the design.

## 🧩 Designed, not wired up yet

These have Figma screens and are on the milestone list in `docs/WEB_PLAN.md`, but the current build shows a clearly-labeled placeholder instead of pretending they work:

- **Favorites** — the star toggle and page aren't connected yet.
- **Health check** — weak/reused/breached detection (HIBP k-anonymity lookup).
- **Two-factor authentication / passkeys.**
- **Auto-lock timer** — the design calls for locking after 5 minutes idle; not implemented.
- **Admin dashboard** — metadata-only KPIs and anonymized user management.
- **Account export / delete.**
- **Browser extension, mobile apps** — web only for now, per the current scope.

## Mobile-first, for real

The default (no media query) layout in `web/src/lib/styles/global.css` *is* the 375px phone layout — bottom tab bar, single-column list, full-width buttons. Tablet (768px) widens the content column; desktop (1440px) swaps the bottom tab bar for a sidebar. Nothing is designed desktop-first and shrunk down.
