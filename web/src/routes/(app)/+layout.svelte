<script lang="ts">
  import AppShell from "$lib/components/AppShell.svelte";
  import { session } from "$lib/session.svelte";
  import { goto } from "$app/navigation";
  import { onMount } from "svelte";

  let { children }: { children: () => any } = $props();

  onMount(() => {
    // No unlocked DEK in memory (fresh reload, or never signed in) -> back to unlock/login.
    if (!session.isUnlocked) {
      goto(session.isAuthenticated ? "/unlock" : "/login");
    }
  });
</script>

{#if session.isUnlocked}
  <AppShell>
    {@render children()}
  </AppShell>
{/if}
