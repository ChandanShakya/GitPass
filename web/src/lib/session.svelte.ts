/**
 * In-memory-only session state: the unlocked DEK never touches disk or
 * `localStorage`. A page reload clears this (same as the Figma auto-lock
 * screen) — the access/refresh tokens are the only thing persisted, in
 * `sessionStorage`, so a reload can re-authenticate without re-deriving
 * keys, but the vault stays locked until the master password is re-entered.
 */
let userId = $state<string | null>(null);
let accessToken = $state<string | null>(null);
let dek = $state<Uint8Array | null>(null); // set only after unlock

const REFRESH_KEY = "gp.refreshToken";

export const session = {
  get userId() {
    return userId;
  },
  get accessToken() {
    return accessToken;
  },
  get dek() {
    return dek;
  },
  get isUnlocked() {
    return dek !== null;
  },
  get isAuthenticated() {
    return accessToken !== null;
  },

  setAuth(newUserId: string, newAccessToken: string, refreshToken: string) {
    userId = newUserId;
    accessToken = newAccessToken;
    sessionStorage.setItem(REFRESH_KEY, refreshToken);
  },
  setUnlocked(newDek: Uint8Array) {
    dek = newDek;
  },
  lock() {
    dek = null;
  },
  signOut() {
    userId = null;
    accessToken = null;
    dek = null;
    sessionStorage.removeItem(REFRESH_KEY);
  },
  getRefreshToken(): string | null {
    return sessionStorage.getItem(REFRESH_KEY);
  },
};
