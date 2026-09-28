import type { Context, MiddlewareHandler } from "hono";
import { verifyAccessToken } from "./jwt.ts";
import type { Env } from "./index.ts";

type Vars = { userId: string };
export type AuthedContext = Context<{ Bindings: Env; Variables: Vars }>;

/**
 * Verifies the access token and stashes `userId` on the context. Every route
 * behind this reads `c.get("userId")` and scopes its query to it — no route
 * ever trusts a client-supplied id for ownership (the bug the old PHP app had).
 */
export const requireAuth: MiddlewareHandler<{ Bindings: Env; Variables: Vars }> = async (c, next) => {
  const header = c.req.header("authorization");
  const token = header?.startsWith("Bearer ") ? header.slice(7) : null;
  if (!token) return c.json({ error: "unauthorized" }, 401);

  const payload = await verifyAccessToken(token, c.env.JWT_SECRET);
  if (!payload) return c.json({ error: "unauthorized" }, 401);

  c.set("userId", payload.sub);
  await next();
};
