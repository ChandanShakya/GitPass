/**
 * D1-backed rate limiting — no Workers KV needed, keeps the whole stack on
 * the D1 free tier. Fixed 15-minute window, 10 attempts per key.
 */
const WINDOW_MS = 15 * 60 * 1000;
const MAX_ATTEMPTS = 10;

export async function checkRateLimit(db: D1Database, key: string): Promise<{ allowed: boolean }> {
  const now = Date.now();
  const row = await db.prepare("SELECT count, window_start FROM login_attempts WHERE key = ?").bind(key).first<{
    count: number;
    window_start: number;
  }>();

  if (!row || now - row.window_start > WINDOW_MS) {
    await db
      .prepare("INSERT INTO login_attempts (key, count, window_start) VALUES (?, 1, ?) ON CONFLICT(key) DO UPDATE SET count = 1, window_start = ?")
      .bind(key, now, now)
      .run();
    return { allowed: true };
  }

  if (row.count >= MAX_ATTEMPTS) return { allowed: false };

  await db.prepare("UPDATE login_attempts SET count = count + 1 WHERE key = ?").bind(key).run();
  return { allowed: true };
}
