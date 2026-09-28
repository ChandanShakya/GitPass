<script lang="ts">
  import EntryCard from "$lib/components/EntryCard.svelte";
  import Button from "$lib/components/Button.svelte";
  import { vaultStore } from "$lib/vaultStore.svelte";
  import { goto } from "$app/navigation";
  import { onMount } from "svelte";

  let search = $state("");
  onMount(() => {
    vaultStore.load();
  });

  const filtered = $derived(
    vaultStore.entries.filter(
      (e) => !search || e.title.toLowerCase().includes(search.toLowerCase()) || e.username.toLowerCase().includes(search.toLowerCase()),
    ),
  );
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Your vault</h1>

  {#if vaultStore.loading}
    <p class="gp-caption">Loading…</p>
  {:else if vaultStore.entries.length === 0}
    <div class="gp-empty">
      <div class="gp-empty-icon" aria-hidden="true">🔒</div>
      <h2 class="gp-h1" style="text-align:center">Your vault is empty</h2>
      <p class="gp-body" style="color:var(--gp-text-muted);text-align:center">
        Add your first password. Everything is locked on your device before it is saved.
      </p>
      <Button variant="primary" onclick={() => goto("/vault/new")}>Add my first password</Button>
    </div>
  {:else}
    <input class="gp-search" type="search" placeholder="Search your vault" bind:value={search} />
    <p class="gp-caption">{vaultStore.entries.length} entries · decrypted on this device</p>
    <div class="gp-list">
      {#each filtered as entry (entry.id)}
        <EntryCard title={entry.title} subtitle={entry.username} initial={entry.title[0]?.toUpperCase() ?? "?"} onclick={() => goto(`/vault/${entry.id}`)} />
      {/each}
    </div>
  {/if}
</div>

<style>
  .gp-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--gp-space-4);
    padding: var(--gp-space-6) 0;
  }
  .gp-empty-icon {
    width: 96px;
    height: 96px;
    border-radius: 999px;
    background: var(--gp-primary-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
  }
  .gp-search {
    height: 48px;
    padding: 0 16px;
    border-radius: var(--gp-radius-md);
    border: 1px solid var(--gp-border);
    background: var(--gp-surface);
    font-size: 14px;
  }
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
</style>
