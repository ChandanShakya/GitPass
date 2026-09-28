<script lang="ts">
  import EntryCard from "$lib/components/EntryCard.svelte";
  import { vaultStore } from "$lib/vaultStore.svelte";
  import { goto } from "$app/navigation";
  import { onMount } from "svelte";
  onMount(() => {
    if (!vaultStore.loaded) vaultStore.load();
  });
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Favorites</h1>
  <p class="gp-caption">Entries you starred, for one-tap access.</p>
  {#if vaultStore.favorites.length === 0}
    <p class="gp-body" style="color:var(--gp-text-muted)">Nothing starred yet. Star an entry from its detail page.</p>
  {:else}
    <div class="gp-list">
      {#each vaultStore.favorites as entry (entry.id)}
        <EntryCard title={entry.title} subtitle={entry.username} initial={entry.title[0]?.toUpperCase() ?? "?"} onclick={() => goto(`/vault/${entry.id}`)} />
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
</style>
