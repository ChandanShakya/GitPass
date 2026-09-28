<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import Button from "$lib/components/Button.svelte";
  import { goto } from "$app/navigation";
</script>

<div class="gp-app">
  <div class="gp-screen">
    <div class="gp-logo-row">
      <BrandLogo size={40} />
      <span class="gp-h2">GitPass</span>
    </div>

    <div class="gp-illustration">
      {#each [["v3", "Changed password", true], ["v2", "Added a note", false], ["v1", "Created entry", false]] as [v, msg, current]}
        <div class="gp-commit-row">
          <span class="gp-dot" class:current></span>
          <span class="gp-label" style="color:var(--gp-primary)">{v}</span>
          <span class="gp-body">{msg}</span>
        </div>
      {/each}
    </div>

    <h1 class="gp-h1">Your vault. Your key. Your history.</h1>
    <p class="gp-body" style="color:var(--gp-text-muted)">
      Store passwords the safe way. GitPass keeps every change so you can always go back — and locks everything
      on your device before it is saved.
    </p>

    <Button variant="primary" onclick={() => goto("/register")}>Create my vault</Button>
    <Button variant="ghost" onclick={() => goto("/login")}>I already have an account</Button>
  </div>
</div>

<style>
  .gp-logo-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .gp-illustration {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 20px;
    border-radius: var(--gp-radius-lg);
    background: var(--gp-primary-soft);
  }
  .gp-commit-row {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .gp-dot {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: var(--gp-border);
  }
  .gp-dot.current {
    background: var(--gp-primary);
  }
</style>
