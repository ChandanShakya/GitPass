// Set PUBLIC_API_URL at build/deploy time (Cloudflare Pages env var).
// Falls back to the local wrangler dev port for `npm run dev`.
export const API_URL = import.meta.env.PUBLIC_API_URL ?? "http://127.0.0.1:8787";
