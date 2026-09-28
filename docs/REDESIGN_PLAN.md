# GitPass v2: changes from the current site, design system, and extension design

## 1. What changes versus the current PHP site

| Area | Current | v2 |
|---|---|---|
| Crypto | MySQL `AES_ENCRYPT` (ECB), key derived by PBKDF2 (10k), key kept in `$_SESSION`, login check is a decryptable ciphertext | Client-side envelope encryption: Argon2id/PBKDF2 (600k+) then HKDF into auth key and KEK. KEK wraps a random DEK. Each entry has a row key wrapped by the DEK. AES-256-GCM with AAD `user_id:entry_id:table:column` |
| Server | Renders HTML, sees plaintext during requests | Stateless JSON API that stores ciphertext blobs only |
| Auth | PHP session cookie, no CSRF | Bearer access token (short-lived) plus rotating refresh token, device list, revocation |
| Authorization | Missing on `delete.php`, `favorite.php`, `add_tags.php`, `delete_tag.php` | Ownership check on every endpoint (policy plus global scope) |
| Data model | `social_accounts`, `social_account_tags`, `social_account_metadata`, `password_history`, `login_track` | `users`, `devices`, `vault_items` (blob), `item_versions` (blob, hash-chained), `sessions`, `audit_log` (metadata only) |
| History | `password_history` table, decrypted on `history.php` | Every edit is a signed, hash-chained encrypted commit. Restore adds a new version. Trash keeps 30 days |
| UI | Server-rendered MDB Bootstrap pages | SPA (Svelte or Solid) with an encrypted IndexedDB cache, in-memory search, optimistic writes, offline queue |
| Clients | Web only | Web SPA, MV3 extension, mobile app, all sharing `packages/crypto` |
| Ops | `config.php` (root, empty password) does not match Docker; schema not auto-loaded | Env-based config, migrations, CI with crypto test vectors |

Fix first if the PHP code stays for now: add auth and ownership checks to the four unauthenticated endpoints, call `session_start` in `update.php`, and stop displaying the password in `profile.php`.

## 2. Design system (built in Figma as `gp/*` variables and text styles)

- Font: Nunito (Regular, SemiBold, Bold, ExtraBold). Mono: Roboto Mono.
- Color tokens (light / dark): bg `#FAFAFA / #0F0E17`, surface `#FFFFFF / #1A1930`, surface-alt `#F7F7FE / #232244`, primary `#4137B0 / #8B84F5`, primary-soft `#F1EFFF / #2D2A5C`, text `#363535 / #F2F2F7`, text-muted `#6B6A7A / #A6A5B8`, border `#E6E4F2 / #33314F`, success, warn, danger, each with a `-soft` background.
- Spacing 4, 8, 12, 16, 24, 32. Radius 8, 12, 16, pill.
- Type scale: Display 28, H1 22, H2 18, Body 14, Caption 12, Label 12, Button 15, Micro 11.
- Components: Button (Primary, Secondary, Ghost, Danger), Badge (Intact, Version, Weak, Breached, Neutral), Input/Text, Input/Password, EntryCard, HistoryRow.
- Breakpoints: mobile 375 (bottom tabs with center Add), tablet 768 (icon rail, 2-column list), desktop 1440 (sidebar, list plus detail pane).
- Implementation mapping: tokens become CSS variables (`--gp-primary` etc.), components map 1:1 to Svelte/Solid components; layout uses CSS grid with the three breakpoints above.

## 3. Screen map (each in mobile, tablet, desktop)

A. Onboarding and sign-in: Get started, Sign in, Create account (strength meter, "we cannot recover this" warning), Recovery key, Quick unlock (PIN pad on mobile, password on tablet/desktop).
B. Vault: list (chips, Intact badge, sync pill), empty first run with setup checklist, entry detail (masked password, history teaser), add/edit (generator, "ask master password first" toggle).
C. Git-like history: history log (timeline of versions with messages), compare (field-level diff, passwords masked), restore, trash (30-day undo).
D. Tools and safety: generator, health check (weak, reused, breached, old), import.
E. Settings: security (auto-lock, quick unlock, clipboard clear), devices and activity, appearance, "How your data is protected" plain-language page.
F. Admin (metadata only): counts, sync errors, anonymized users with reset/disable. Admin can never view, change, or export vault content.

Original design problems fixed: typos and inconsistent names (Carts, `passwrod`), forgot-password flow that cannot recover encrypted data (replaced by recovery key), `SAVE` in a history header (replaced by Restore/Compare), no tamper or sync state, no trash, no device list.

## 4. Browser extension design (popup 360 x 560)

Shared rules: same tokens, 8px grid, 48px touch targets, keyboard first (`Tab`, `Enter` fills, `Ctrl/Cmd+Shift+L` opens), no plaintext ever written to disk.

1. **Locked**: logo, "Vault locked" (Headline), master password field with eye, `Unlock` primary button, "Use passkey", footer "Locked after 5 min idle".
2. **Site match (default when unlocked)**: header row with logo, search field, lock icon button. Below: "Matches for github.com" section with up to 3 EntryCards (avatar, name, username). Each card has two actions: `Fill` (primary, small) and copy. Below that: "Recent" list (5 rows). Bottom bar: `+ New`, generator icon, open-vault icon, sync dot.
3. **Search**: field focused, results filter in memory per keystroke (target under 16 ms), matching text highlighted, arrow keys move selection.
4. **Entry detail**: back arrow, avatar, name, Intact badge. Rows: Username (copy), Password (masked, eye, copy, auto-clear note "clears in 20 s"), Website (open). Buttons `Fill` and `History`. Compact 3-row version timeline with Restore.
5. **Save prompt (after sign-in form submit)**: slim card "Save this login to GitPass?" with site, username, masked password, `Save` and `Not now`; "Update password?" variant with a v-badge showing `v4`.
6. **Generator**: mono password in a large field, strength bar, length slider, four toggles, `Copy` and `Use` buttons.
7. **Offline / error banner**: warn-soft strip at the top: "Offline. Changes will sync later." Sync dot turns amber.
8. **In-page autofill menu** (injected next to a username field): 280px dropdown, up to 3 matches with avatar and username, "Open GitPass" footer, Esc closes. Uses Shadow DOM so page CSS cannot affect it.

Security notes for the extension: unlocked keys live only in `storage.session` (memory), strict CSP with no remote code, autofill only on exact registrable-domain match, confirm before filling into iframes or cross-origin forms, clipboard cleared after 20 s.

## 5. Status of the Figma file

Built on the "Design Chandan Shakya" page: variables and text styles, the component sheet (`00 Components v2`), and section A with all five auth screens at three breakpoints. Section B (Vault) was interrupted by the Figma MCP call limit and may hold partial frames; it should be deleted and rebuilt. Sections C to F and the extension frames are not built yet. The original frames were left in place above the new sections and have not been removed.

## 6. Figma build status (new file, premium account)

File: https://www.figma.com/design/Ue9FWYkPYRSENSBava8Uxc/GitPass — page "Continue" (same content as before, duplicated to a premium education account, so page limits and variable-mode limits no longer apply here the way they did on the Starter copy).

Rebuilt from scratch in this file (the copy did not carry over local variables or text styles):
- `gp/color`, `gp/color-dark` variable collections, `gp/space` collection, `gp/*` text styles.
- `00 Components v2` section: Button, Badge, Input/Text, Input/Password, EntryCard, HistoryRow, and a `BrandMark` component built from the file's real `brand` logo component (icon + wordmark), not a placeholder.

Screens, each breakpoint as one linear horizontal row (mobile row, tablet row, desktop row), sections named `M/T/D · <letter> <name>`:
- **A — Onboarding and sign-in**: Get started, Sign in, Create account, Recovery key, Quick unlock.
- **B — Vault**: Vault list, Vault empty (first run), Entry detail, Add/edit entry.
- **C — Git-like history**: History log (timeline), Compare versions (masked-password diff), Trash.
- **D — Tools**: Generator, Health check.
- **E — Settings**: Security (auto-lock, quick unlock, clipboard clear), Devices and activity, Privacy (plain-language data page).
- **F — Admin**: Dashboard (metadata KPIs only) and Admin user (anonymized id, reset/disable only, explicit "cannot view or export vault contents" notice).
- **X — Browser extension** (360×560 popup, one row): Locked, Site match (autofill list), Entry detail, Save-login prompt, Generator, Offline banner, In-page autofill dropdown.

All screens use real component instances (Button, Badge, Input, EntryCard) and the real brand mark, so component updates propagate. Verified by screenshot at each stage.
