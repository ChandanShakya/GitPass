import { Hono } from "hono";
import { cors } from "hono/cors";
import { generateDek, randomBytes, toB64 } from "@gitpass/crypto";
import { signAccessToken, verifyAccessToken, generateRefreshToken, sha256Hex } from "./jwt.ts";
import { requireAuth, type AuthedContext } from "./middleware.ts";
import { checkRateLimit } from "./rate-limit.ts";
import vault from "./routes/vault.ts";

export interface Env {
  DB: D1Database;
  JWT_SECRET: string;
  ENVIRONMENT: string;
}

const app = new Hono<{ Bindings: Env }>();

app.use("*", cors());

app.get("/health", (c) => c.json({ ok: true, env: c.env.ENVIRONMENT }));

// ---- auth ----

app.post("/auth/register", async (c) => {
  const body = await c.req.json<{
    email: string;
    authKey: string; // base64, client-derived — never the password
    salt: string; // base64, client's PBKDF2 salt
    kdfIterations: number;
    wrappedDek: string; // DEK wrapped under the client's KEK
    wrappedDekRecovery: string; // same DEK, wrapped under the one-time recovery key
  }>();
  if (!body.email || !body.authKey || !body.salt || !body.wrappedDek || !body.wrappedDekRecovery) {
    return c.json({ error: "missing fields" }, 400);
  }

  const existing = await c.env.DB.prepare("SELECT id FROM users WHERE email = ?").bind(body.email).first();
  if (existing) return c.json({ error: "email already registered" }, 409);

  const id = crypto.randomUUID();
  const serverSalt = toB64(randomBytes(16));
  const authHash = await sha256Hex(body.authKey + ":" + serverSalt);

  await c.env.DB.prepare(
    `INSERT INTO users (id, email, auth_hash, salt, kdf_iterations, wrapped_dek, wrapped_dek_recovery, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
  )
    .bind(
      id,
      body.email,
      serverSalt + "$" + authHash,
      body.salt,
      body.kdfIterations,
      body.wrappedDek,
      body.wrappedDekRecovery,
      Date.now(),
    )
    .run();

  return c.json({ userId: id }, 201);
});

// Client needs the salt + iteration count before it can derive authKey
// locally. Exposing this per email is standard (every "forgot password"
// flow does the equivalent) and reveals nothing about the password itself.
app.get("/auth/kdf-params", async (c) => {
  const email = c.req.query("email");
  if (!email) return c.json({ error: "email required" }, 400);
  const user = await c.env.DB.prepare("SELECT salt, kdf_iterations FROM users WHERE email = ?")
    .bind(email)
    .first<{ salt: string; kdf_iterations: number }>();
  if (!user) return c.json({ error: "not found" }, 404);
  return c.json({ salt: user.salt, kdfIterations: user.kdf_iterations });
});

app.post("/auth/login", async (c) => {
  const body = await c.req.json<{ email: string; authKey: string; deviceName?: string }>();
  const ip = c.req.header("cf-connecting-ip") ?? "unknown";
  const rl = await checkRateLimit(c.env.DB, `${ip}:${body.email}`);
  if (!rl.allowed) return c.json({ error: "too many attempts, try again later" }, 429);

  const user = await c.env.DB.prepare(
    "SELECT id, auth_hash, salt, kdf_iterations, wrapped_dek FROM users WHERE email = ?",
  )
    .bind(body.email)
    .first<{ id: string; auth_hash: string; salt: string; kdf_iterations: number; wrapped_dek: string }>();

  const fail = () => c.json({ error: "invalid credentials" }, 401);
  if (!user) return fail();

  const [serverSalt, expectedHash] = user.auth_hash.split("$");
  const gotHash = await sha256Hex(body.authKey + ":" + serverSalt);
  if (gotHash !== expectedHash) return fail();

  const accessToken = await signAccessToken(user.id, c.env.JWT_SECRET);
  const refreshToken = generateRefreshToken();
  const sessionId = crypto.randomUUID();
  await c.env.DB.prepare(
    `INSERT INTO sessions (id, user_id, device_name, refresh_hash, created_at, last_seen_at)
     VALUES (?, ?, ?, ?, ?, ?)`,
  )
    .bind(sessionId, user.id, body.deviceName ?? "Unknown device", await sha256Hex(refreshToken), Date.now(), Date.now())
    .run();

  return c.json({
    accessToken,
    refreshToken,
    salt: user.salt,
    kdfIterations: user.kdf_iterations,
    userId: user.id,
    wrappedDek: user.wrapped_dek,
  });
});

app.post("/auth/refresh", async (c) => {
  const { refreshToken } = await c.req.json<{ refreshToken: string }>();
  const hash = await sha256Hex(refreshToken);
  const session = await c.env.DB.prepare(
    "SELECT id, user_id FROM sessions WHERE refresh_hash = ? AND revoked_at IS NULL",
  )
    .bind(hash)
    .first<{ id: string; user_id: string }>();
  if (!session) return c.json({ error: "invalid session" }, 401);

  await c.env.DB.prepare("UPDATE sessions SET last_seen_at = ? WHERE id = ?").bind(Date.now(), session.id).run();
  const accessToken = await signAccessToken(session.user_id, c.env.JWT_SECRET);
  return c.json({ accessToken });
});

app.post("/auth/logout", async (c) => {
  const { refreshToken } = await c.req.json<{ refreshToken: string }>();
  const hash = await sha256Hex(refreshToken);
  await c.env.DB.prepare("UPDATE sessions SET revoked_at = ? WHERE refresh_hash = ?").bind(Date.now(), hash).run();
  return c.json({ ok: true });
});

app.get("/auth/recovery-info", async (c) => {
  const email = c.req.query("email");
  if (!email) return c.json({ error: "email required" }, 400);
  const user = await c.env.DB.prepare("SELECT id, wrapped_dek_recovery FROM users WHERE email = ?")
    .bind(email)
    .first<{ id: string; wrapped_dek_recovery: string }>();
  if (!user) return c.json({ error: "not found" }, 404);
  return c.json({ userId: user.id, wrappedDekRecovery: user.wrapped_dek_recovery });
});

// Sets a new master password after the client has unwrapped the DEK locally
// via the recovery key. The recovery key itself stays valid (not rotated).
app.post("/auth/reset-password", async (c) => {
  const body = await c.req.json<{
    userId: string;
    authKey: string;
    salt: string;
    kdfIterations: number;
    wrappedDek: string;
  }>();
  const serverSalt = toB64(randomBytes(16));
  const authHash = await sha256Hex(body.authKey + ":" + serverSalt);
  await c.env.DB.prepare(
    "UPDATE users SET auth_hash = ?, salt = ?, kdf_iterations = ?, wrapped_dek = ? WHERE id = ?",
  )
    .bind(serverSalt + "$" + authHash, body.salt, body.kdfIterations, body.wrappedDek, body.userId)
    .run();
  // Resetting the password invalidates every existing session.
  await c.env.DB.prepare("UPDATE sessions SET revoked_at = ? WHERE user_id = ? AND revoked_at IS NULL")
    .bind(Date.now(), body.userId)
    .run();
  return c.json({ ok: true });
});

// ---- devices ----

app.get("/devices", requireAuth, async (c: AuthedContext) => {
  const rows = await c.env.DB.prepare(
    "SELECT id, device_name, created_at, last_seen_at FROM sessions WHERE user_id = ? AND revoked_at IS NULL ORDER BY last_seen_at DESC",
  )
    .bind(c.get("userId"))
    .all();
  return c.json({ devices: rows.results });
});

app.delete("/devices/:id", requireAuth, async (c: AuthedContext) => {
  await c.env.DB.prepare("UPDATE sessions SET revoked_at = ? WHERE id = ? AND user_id = ?")
    .bind(Date.now(), c.req.param("id"), c.get("userId"))
    .run();
  return c.json({ ok: true });
});

app.route("/vault", vault);

export default app;
