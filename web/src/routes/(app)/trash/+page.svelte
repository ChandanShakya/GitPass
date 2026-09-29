<script lang="ts">
  import Button from "$lib/components/Button.svelte";
  import Badge from "$lib/components/Badge.svelte";
  import { api } from "$lib/api";
  import { session } from "$lib/session.svelte";
  import { decryptEntry } from "$lib/vaultCrypto";
  import { onMount } from "svelte";

  interface Item {
    id: string;
    wrapped_row_key: string;
    title_blob: string;
    body_blob: string;
    deleted_at: number | null;
  }
  let trashed = $state<{ id: string; title: string; deletedAt: number }[]>([]);
  let loading = $state(true);

  onMount(load);
  async function load() {
    loading = true;
    const res = await api.get<{ items: Item[] }>("/vault/items?since=0");
    const deleted = res.items.filter((i) => i.deleted_at);
    trashed = await Promise.all(
      deleted.map(async (i) => {
        const f = await decryptEntry(session.userId!, i.id, session.dek!, { wrappedRowKey: i.wrapped_row_key, titleBlob: i.title_blob, bodyBlob: i.body_blob });
        return { id: i.id, title: f.title, deletedAt: i.deleted_at! };
      }),
    );
    loading = false;
  }
  async function restore(id: string) {
    await api.post(`/vault/items/${id}/undelete`);
    await load();
  }
  function daysAgo(ts: number) {
    return Math.floor((Date.now() - ts) / 86_400_000);
  }
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Trash</h1>
  <p class="gp-caption">Items are kept for 30 days, then removed for good.</p>

  {#if loading}
    <p class="gp-caption">Loading…</p>
  {:else if trashed.length === 0}
    <p class="gp-body" style="color:var(--gp-text-muted)">Trash is empty.</p>
  {:else}
    <div class="gp-list">
      {#each trashed as item (item.id)}
        <div class="gp-row">
          <span class="gp-avatar">{item.title[0]?.toUpperCase() ?? "?"}</span>
          <div class="gp-text">
            <span class="gp-body-strong">{item.title}</span>
            <span class="gp-caption">deleted {daysAgo(item.deletedAt)} days ago</span>
          </div>
          <Badge kind="neutral">{30 - daysAgo(item.deletedAt)}d left</Badge>
          <Button variant="secondary" fullWidth={false} onclick={() => restore(item.id)}>Restore</Button>
        </div>
      {/each}
    </div>
  {/if}
</div>

<style>
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .gp-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
    border-radius: var(--gp-radius-lg);
  }
  .gp-avatar {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: var(--gp-primary-soft);
    color: var(--gp-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
  }
  .gp-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
  }
</style>
