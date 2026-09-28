# Deployment (Cloudflare free tier, $0/month)

Everything here fits inside Cloudflare's free tier at personal/small-scale use:

| Service | Free limit | What GitPass uses |
|---|---|---|
| Cloudflare Pages | Unlimited requests & bandwidth, 500 builds/month | 1 static SvelteKit site |
| Cloudflare Workers | 100,000 requests/day | The API — small JSON handlers, well under the 10ms CPU/request cap |
| D1 | 5GB storage, 5M row reads/day, 100k row writes/day | Ciphertext blobs — each vault edit is one write |

No Workers KV, no paid add-on, no credit card charge at any point in this guide.

## Prerequisites

- A free [Cloudflare account](https://dash.cloudflare.com/sign-up).
- Node.js 20+ and this repo cloned, `web` branch checked out.
- `npm install` run once at the repo root (installs `wrangler` as a dev dependency — no global install needed, use `npx wrangler`).

## 1. Log in to Cloudflare

```bash
npx wrangler login
```

Opens a browser window to authorize the CLI. One-time.

## 2. Create the D1 database

```bash
cd api
npx wrangler d1 create gitpass
```

This prints a `database_id`. Copy it into `api/wrangler.toml`, replacing `REPLACE_WITH_YOUR_D1_DATABASE_ID`.

## 3. Run the schema migration

```bash
npm run db:migrate:remote
```

Applies `api/migrations/0001_init.sql` to the real (remote) D1 database. Re-run this command whenever a new migration file is added.

## 4. Set the JWT secret

```bash
npx wrangler secret put JWT_SECRET
```

Paste a long random string when prompted (e.g. `openssl rand -hex 32`). This signs access tokens — never commit it, never reuse it across environments.

## 5. Deploy the API

```bash
npm run deploy
```

Wrangler prints the Worker's URL, e.g. `https://gitpass-api.<your-subdomain>.workers.dev`. Copy it.

## 6. Point the web app at the API

Back at the repo root:

```bash
cd ../web
```

Create `web/.env` (not committed — see `.gitignore`):

```
PUBLIC_API_URL=https://gitpass-api.<your-subdomain>.workers.dev
```

## 7. Deploy the web app to Pages

The first deploy needs a Pages project created once:

```bash
npx wrangler pages project create gitpass --production-branch=web
```

Then, from the repo root:

```bash
npm run deploy:web
```

This builds the SvelteKit app and deploys `.svelte-kit/cloudflare` to Pages. Wrangler prints the live URL, e.g. `https://gitpass.pages.dev`.

For `PUBLIC_API_URL` to be baked into the Pages build (not just local dev), also set it as a Pages environment variable: Cloudflare dashboard → Workers & Pages → gitpass → Settings → Environment variables → add `PUBLIC_API_URL` for the Production environment, then redeploy.

## Local development (no Cloudflare account needed to start)

Wrangler simulates Workers and D1 locally with no login required:

```bash
# terminal 1 — API, with a local D1 database
cd api
npx wrangler d1 execute gitpass --local --file=migrations/0001_init.sql
echo "JWT_SECRET=dev-secret-$(openssl rand -hex 16)" > .dev.vars
npm run dev            # http://127.0.0.1:8787

# terminal 2 — web app
cd web
npm run dev            # http://localhost:5173, talks to the local API by default
```

`web/src/lib/config.ts` defaults `PUBLIC_API_URL` to `http://127.0.0.1:8787` when unset, so local dev needs no `.env` file.

## Verifying it's really free

Cloudflare's dashboard (Workers & Pages → your Worker → Metrics) shows request counts against the free-tier caps in real time. At personal or small-team scale, GitPass stays orders of magnitude under all three limits — the 100k-writes/day D1 cap is the one to watch if the vault ever sees heavy automated traffic, since every save is one write.

## Updating after code changes

```bash
npm run deploy:api     # after any api/ change
npm run deploy:web     # after any web/ change
npm run db:migrate:remote   # after adding a new migrations/NNNN_*.sql file
```
