import { Hono } from "hono";
import { chainHash } from "@gitpass/crypto";
import { requireAuth } from "../middleware.ts";
import type { Env } from "../index.ts";

type Vars = { userId: string };
const vault = new Hono<{ Bindings: Env; Variables: Vars }>();
vault.use("*", requireAuth);

// GET /vault/items?since=<ms>  — delta sync. since=0 returns everything not in trash.
vault.get("/items", async (c) => {
  const userId = c.get("userId");
  const since = Number(c.req.query("since") ?? "0");
  const rows = await c.env.DB.prepare(
    `SELECT id, category, favorite, wrapped_row_key, title_blob, body_blob, current_version, updated_at, deleted_at
     FROM vault_items WHERE user_id = ? AND updated_at > ? ORDER BY updated_at ASC`,
  )
    .bind(userId, since)
    .all();
  return c.json({ items: rows.results, syncedAt: Date.now() });
});

// POST /vault/items — create entry, version 1.
vault.post("/items", async (c) => {
  const userId = c.get("userId");
  const body = await c.req.json<{
    id?: string;
    category: string;
    wrappedRowKey: string;
    titleBlob: string;
    bodyBlob: string;
    messageBlob: string;
    deviceName: string;
  }>();
  const id = body.id ?? crypto.randomUUID();
  const now = Date.now();
  const hash = await chainHash("", body.bodyBlob);

  await c.env.DB.batch([
    c.env.DB.prepare(
      `INSERT INTO vault_items (id, user_id, category, wrapped_row_key, title_blob, body_blob, current_version, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)`,
    ).bind(id, userId, body.category, body.wrappedRowKey, body.titleBlob, body.bodyBlob, now, now),
    c.env.DB.prepare(
      `INSERT INTO item_versions (id, item_id, version, body_blob, message_blob, prev_hash, hash, device_name, created_at)
       VALUES (?, ?, 1, ?, ?, '', ?, ?, ?)`,
    ).bind(crypto.randomUUID(), id, body.bodyBlob, body.messageBlob, hash, body.deviceName, now),
  ]);

  return c.json({ id, version: 1 }, 201);
});

// PATCH /vault/items/:id — new version. Ownership enforced by the WHERE user_id clause.
vault.patch("/items/:id", async (c) => {
  const userId = c.get("userId");
  const id = c.req.param("id");
  const body = await c.req.json<{ titleBlob?: string; bodyBlob: string; messageBlob: string; deviceName: string; favorite?: boolean }>();

  const item = await c.env.DB.prepare("SELECT current_version FROM vault_items WHERE id = ? AND user_id = ?")
    .bind(id, userId)
    .first<{ current_version: number }>();
  if (!item) return c.json({ error: "not found" }, 404);

  const prevRow = await c.env.DB.prepare(
    "SELECT hash FROM item_versions WHERE item_id = ? AND version = ?",
  )
    .bind(id, item.current_version)
    .first<{ hash: string }>();
  const prevHash = prevRow?.hash ?? "";
  const newVersion = item.current_version + 1;
  const hash = await chainHash(prevHash, body.bodyBlob);
  const now = Date.now();

  await c.env.DB.batch([
    c.env.DB.prepare(
      `UPDATE vault_items SET body_blob = ?, title_blob = COALESCE(?, title_blob), favorite = COALESCE(?, favorite), current_version = ?, updated_at = ?
       WHERE id = ? AND user_id = ?`,
    ).bind(body.bodyBlob, body.titleBlob ?? null, body.favorite === undefined ? null : Number(body.favorite), newVersion, now, id, userId),
    c.env.DB.prepare(
      `INSERT INTO item_versions (id, item_id, version, body_blob, message_blob, prev_hash, hash, device_name, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
    ).bind(crypto.randomUUID(), id, newVersion, body.bodyBlob, body.messageBlob, prevHash, hash, body.deviceName, now),
  ]);

  return c.json({ id, version: newVersion });
});

// DELETE /vault/items/:id — soft delete -> Trash.
vault.delete("/items/:id", async (c) => {
  const userId = c.get("userId");
  await c.env.DB.prepare("UPDATE vault_items SET deleted_at = ?, updated_at = ? WHERE id = ? AND user_id = ?")
    .bind(Date.now(), Date.now(), c.req.param("id"), userId)
    .run();
  return c.json({ ok: true });
});

// POST /vault/items/:id/undelete — restore from Trash.
vault.post("/items/:id/undelete", async (c) => {
  const userId = c.get("userId");
  await c.env.DB.prepare("UPDATE vault_items SET deleted_at = NULL, updated_at = ? WHERE id = ? AND user_id = ?")
    .bind(Date.now(), c.req.param("id"), userId)
    .run();
  return c.json({ ok: true });
});

// GET /vault/items/:id/versions — full history, newest first.
vault.get("/items/:id/versions", async (c) => {
  const userId = c.get("userId");
  const owns = await c.env.DB.prepare("SELECT id FROM vault_items WHERE id = ? AND user_id = ?")
    .bind(c.req.param("id"), userId)
    .first();
  if (!owns) return c.json({ error: "not found" }, 404);

  const rows = await c.env.DB.prepare(
    "SELECT version, message_blob, prev_hash, hash, device_name, created_at FROM item_versions WHERE item_id = ? ORDER BY version DESC",
  )
    .bind(c.req.param("id"))
    .all();
  return c.json({ versions: rows.results });
});

// POST /vault/items/:id/restore/:version — restore an old version as a new one (never rewrites history).
vault.post("/items/:id/restore/:version", async (c) => {
  const userId = c.get("userId");
  const id = c.req.param("id");
  const targetVersion = Number(c.req.param("version"));
  const { messageBlob } = await c.req.json<{ messageBlob: string }>();

  const item = await c.env.DB.prepare("SELECT current_version FROM vault_items WHERE id = ? AND user_id = ?")
    .bind(id, userId)
    .first<{ current_version: number }>();
  if (!item) return c.json({ error: "not found" }, 404);

  const target = await c.env.DB.prepare("SELECT body_blob FROM item_versions WHERE item_id = ? AND version = ?")
    .bind(id, targetVersion)
    .first<{ body_blob: string }>();
  if (!target) return c.json({ error: "version not found" }, 404);

  const prevRow = await c.env.DB.prepare("SELECT hash FROM item_versions WHERE item_id = ? AND version = ?")
    .bind(id, item.current_version)
    .first<{ hash: string }>();
  const prevHash = prevRow?.hash ?? "";
  const newVersion = item.current_version + 1;
  const hash = await chainHash(prevHash, target.body_blob);
  const now = Date.now();

  await c.env.DB.batch([
    c.env.DB.prepare("UPDATE vault_items SET body_blob = ?, current_version = ?, updated_at = ? WHERE id = ? AND user_id = ?").bind(
      target.body_blob,
      newVersion,
      now,
      id,
      userId,
    ),
    c.env.DB.prepare(
      `INSERT INTO item_versions (id, item_id, version, body_blob, message_blob, prev_hash, hash, device_name, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, 'restore', ?)`,
    ).bind(crypto.randomUUID(), id, newVersion, target.body_blob, messageBlob, prevHash, hash, now),
  ]);

  return c.json({ id, version: newVersion });
});

export default vault;
