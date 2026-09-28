/**
 * Minimal HMAC-SHA256 JWT (sign + verify only, no external dependency —
 * this is the entire spec surface the API needs). Access tokens are short
 * lived; refresh tokens are opaque random strings stored hashed in
 * `sessions`, not JWTs, so they can be revoked server-side.
 */
const enc = new TextEncoder();

function b64url(bytes: Uint8Array): string {
  let bin = "";
  for (const b of bytes) bin += String.fromCharCode(b);
  return btoa(bin).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
}
function b64urlDecode(s: string): Uint8Array {
  const pad = s.length % 4 === 0 ? "" : "=".repeat(4 - (s.length % 4));
  const bin = atob(s.replace(/-/g, "+").replace(/_/g, "/") + pad);
  const out = new Uint8Array(bin.length);
  for (let i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
  return out;
}

async function hmacKey(secret: string): Promise<CryptoKey> {
  return crypto.subtle.importKey("raw", enc.encode(secret), { name: "HMAC", hash: "SHA-256" }, false, [
    "sign",
    "verify",
  ]);
}

export interface AccessTokenPayload {
  sub: string; // user id
  exp: number; // unix seconds
  iat: number;
}

export async function signAccessToken(userId: string, secret: string, ttlSeconds = 900): Promise<string> {
  const header = b64url(enc.encode(JSON.stringify({ alg: "HS256", typ: "JWT" })));
  const now = Math.floor(Date.now() / 1000);
  const payload: AccessTokenPayload = { sub: userId, iat: now, exp: now + ttlSeconds };
  const body = b64url(enc.encode(JSON.stringify(payload)));
  const signingInput = `${header}.${body}`;
  const sig = await crypto.subtle.sign("HMAC", await hmacKey(secret), enc.encode(signingInput));
  return `${signingInput}.${b64url(new Uint8Array(sig))}`;
}

export async function verifyAccessToken(token: string, secret: string): Promise<AccessTokenPayload | null> {
  const parts = token.split(".");
  if (parts.length !== 3) return null;
  const [header, body, sig] = parts;
  const ok = await crypto.subtle.verify(
    "HMAC",
    await hmacKey(secret),
    b64urlDecode(sig),
    enc.encode(`${header}.${body}`),
  );
  if (!ok) return null;
  const payload = JSON.parse(new TextDecoder().decode(b64urlDecode(body))) as AccessTokenPayload;
  if (payload.exp < Math.floor(Date.now() / 1000)) return null;
  return payload;
}

/** Opaque refresh token: random, stored hashed — never a JWT, so it's revocable by deleting its hash. */
export function generateRefreshToken(): string {
  const bytes = crypto.getRandomValues(new Uint8Array(32));
  return b64url(bytes);
}

export async function sha256Hex(input: string): Promise<string> {
  const digest = await crypto.subtle.digest("SHA-256", enc.encode(input));
  return Array.from(new Uint8Array(digest)).map((b) => b.toString(16).padStart(2, "0")).join("");
}
