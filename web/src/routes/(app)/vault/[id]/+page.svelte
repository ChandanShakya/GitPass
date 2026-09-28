<script lang="ts">
  import Badge from "$lib/components/Badge.svelte";
  import Button from "$lib/components/Button.svelte";
  import { vaultStore } from "$lib/vaultStore.svelte";
  import { page } from "$app/stores";
  import { goto } from "$app/navigation";
  import { onMount } from "svelte";

  let passwordVisible = $state(false);
  let editing = $state(false);
  let draft = $state({ title: "", username: "", password: "", url: "", notes: "" });
  let busy = $state(false);

  const entry = $derived(vaultStore.find($page.params.id as string));

  onMount(async () => {
    if (!vaultStore.loaded) await vaultStore.load();
  });

  function startEdit() {
    if (!entry) return;
    draft = { title: entry.title, username: entry.username, password: entry.password, url: entry.url, notes: entry.notes };
    editing = true;
  }

  async function saveEdit() {
    if (!entry) return;
    busy = true;
    try {
      await vaultStore.update(entry.id, draft, "Edited entry");
      editing = false;
    } finally {
      busy = false;
    }
  }

  async function copy(text: string) {
    await navigator.clipboard?.writeText(text);
    setTimeout(() => navigator.clipboard?.writeText(""), 20_000); // auto-clear per the design spec
  }

  async function remove() {
    if (!entry) return;
    await vaultStore.remove(entry.id);
    goto("/vault");
  }
</script>

<div class="gp-screen">
  {#if !entry}
    <p class="gp-caption">Loading…</p>
  {:else if editing}
    <h1 class="gp-h1">Edit entry</h1>
    <div class="gp-form">
      <label class="gp-field"><span class="gp-label">Title</span><input class="gp-input" bind:value={draft.title} /></label>
      <label class="gp-field"><span class="gp-label">Username</span><input class="gp-input" bind:value={draft.username} /></label>
      <label class="gp-field"><span class="gp-label">Password</span><input class="gp-input gp-mono" bind:value={draft.password} /></label>
      <label class="gp-field"><span class="gp-label">Website</span><input class="gp-input" bind:value={draft.url} /></label>
      <label class="gp-field"><span class="gp-label">Notes</span><input class="gp-input" bind:value={draft.notes} /></label>
      <div class="gp-row">
        <Button variant="ghost" onclick={() => (editing = false)}>Cancel</Button>
        <Button variant="primary" disabled={busy} onclick={saveEdit}>{busy ? "Saving…" : `Save (v${entry.version + 1})`}</Button>
      </div>
    </div>
  {:else}
    <div class="gp-detail-card">
      <div class="gp-detail-head">
        <span class="gp-avatar">{entry.title[0]?.toUpperCase() ?? "?"}</span>
        <div class="gp-detail-title">
          <span class="gp-h1">{entry.title}</span>
          {#if entry.url}<a class="gp-body" style="color:var(--gp-primary)" href={entry.url} target="_blank" rel="noreferrer">{entry.url}</a>{/if}
        </div>
        <Badge kind="intact">Verified</Badge>
      </div>

      <div class="gp-row-field">
        <span class="gp-label" style="color:var(--gp-text-muted)">Username</span>
        <div class="gp-row-value">
          <span class="gp-body-strong">{entry.username}</span>
          <button class="gp-icon-btn" onclick={() => copy(entry.username)} aria-label="Copy username">📋</button>
        </div>
      </div>
      <div class="gp-row-field">
        <span class="gp-label" style="color:var(--gp-text-muted)">Password</span>
        <div class="gp-row-value">
          <span class="gp-mono">{passwordVisible ? entry.password : "•".repeat(Math.min(entry.password.length, 16))}</span>
          <button class="gp-icon-btn" onclick={() => (passwordVisible = !passwordVisible)} aria-label="Toggle password visibility">👁</button>
          <button class="gp-icon-btn" onclick={() => copy(entry.password)} aria-label="Copy password">📋</button>
        </div>
      </div>
      {#if entry.notes}
        <div class="gp-row-field">
          <span class="gp-label" style="color:var(--gp-text-muted)">Notes</span>
          <span class="gp-body">{entry.notes}</span>
        </div>
      {/if}

      <a class="gp-history-teaser" href="/vault/{entry.id}/history">
        <span aria-hidden="true">🕐</span>
        <span class="gp-body-strong" style="color:var(--gp-primary)">{entry.version} version{entry.version === 1 ? "" : "s"} saved</span>
        <span aria-hidden="true">›</span>
      </a>

      <div class="gp-row">
        <Button variant="primary" onclick={startEdit}>Edit</Button>
        <Button variant="danger" onclick={remove}>Move to trash</Button>
      </div>
    </div>
  {/if}
</div>

<style>
  .gp-detail-card {
    display: flex;
    flex-direction: column;
    gap: 18px;
    padding: 16px;
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
    border-radius: var(--gp-radius-lg);
  }
  .gp-detail-head {
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .gp-detail-title {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
  }
  .gp-avatar {
    width: 56px;
    height: 56px;
    border-radius: 999px;
    background: var(--gp-primary-soft);
    color: var(--gp-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 22px;
    flex-shrink: 0;
  }
  .gp-row-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--gp-border);
  }
  .gp-row-value {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .gp-row-value > *:first-child {
    flex: 1;
  }
  .gp-icon-btn {
    background: none;
    border: none;
    font-size: 16px;
    padding: 4px;
  }
  .gp-history-teaser {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-primary-soft);
    text-decoration: none;
  }
  .gp-row {
    display: flex;
    gap: 12px;
  }
  .gp-form {
    display: flex;
    flex-direction: column;
    gap: var(--gp-space-4);
  }
  .gp-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .gp-input {
    height: 48px;
    padding: 0 16px;
    border-radius: var(--gp-radius-md);
    border: 1px solid var(--gp-border);
    background: var(--gp-surface-alt);
  }
</style>
