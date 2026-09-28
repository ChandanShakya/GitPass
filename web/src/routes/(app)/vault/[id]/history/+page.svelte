<script lang="ts">
  import Badge from "$lib/components/Badge.svelte";
  import Button from "$lib/components/Button.svelte";
  import { api } from "$lib/api";
  import { session } from "$lib/session.svelte";
  import { decryptMessage, encryptMessage } from "$lib/vaultCrypto";
  import { vaultStore } from "$lib/vaultStore.svelte";
  import { page } from "$app/stores";
  import { onMount } from "svelte";

  interface Version {
    version: number;
    message_blob: string;
    device_name: string;
    created_at: number;
    message?: string;
  }

  let versions = $state<Version[]>([]);
  let loading = $state(true);
  const entryId = $page.params.id as string;

  onMount(load);

  async function load() {
    loading = true;
    const res = await api.get<{ versions: Version[] }>(`/vault/items/${entryId}/versions`);
    versions = await Promise.all(
      res.versions.map(async (v) => ({
        ...v,
        message: await decryptMessage(session.userId!, entryId, session.dek!, v.message_blob, v.version).catch(() => "(message unavailable)"),
      })),
    );
    loading = false;
  }

  async function restore(version: number) {
    const messageBlob = await encryptMessage(session.userId!, entryId, session.dek!, `Restored to v${version}`, versions[0].version + 1);
    await api.post(`/vault/items/${entryId}/restore/${version}`, { messageBlob });
    vaultStore.reset();
    await load();
  }
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Password history</h1>
  <p class="gp-caption">Every version of this entry, newest first.</p>

  {#if loading}
    <p class="gp-caption">Loading…</p>
  {:else}
    <div class="gp-timeline">
      {#each versions as v, i (v.version)}
        <div class="gp-history-row">
          <div class="gp-rail">
            <span class="gp-dot" class:current={i === 0}></span>
            {#if i < versions.length - 1}<span class="gp-line"></span>{/if}
          </div>
          <div class="gp-card" class:current={i === 0}>
            <div class="gp-card-top">
              <Badge kind="version">v{v.version}</Badge>
              <span class="gp-body-strong">{v.message}</span>
            </div>
            <span class="gp-caption">{new Date(v.created_at).toLocaleString()} · {v.device_name}</span>
            {#if i !== 0}
              <Button variant="ghost" onclick={() => restore(v.version)}>Restore</Button>
            {/if}
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>

<style>
  .gp-timeline {
    display: flex;
    flex-direction: column;
  }
  .gp-history-row {
    display: flex;
    gap: 14px;
  }
  .gp-rail {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .gp-dot {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: var(--gp-border);
    flex-shrink: 0;
  }
  .gp-dot.current {
    background: var(--gp-primary);
  }
  .gp-line {
    width: 2px;
    flex: 1;
    background: var(--gp-border);
    min-height: 40px;
  }
  .gp-card {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 16px;
    margin-bottom: 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
    flex: 1;
  }
  .gp-card.current {
    background: var(--gp-primary-soft);
    border: none;
  }
  .gp-card-top {
    display: flex;
    align-items: center;
    gap: 8px;
  }
</style>
