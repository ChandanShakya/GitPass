<script lang="ts">
  import Badge from "$lib/components/Badge.svelte";
  import Button from "$lib/components/Button.svelte";
  import { page } from "$app/stores";

  const id = $page.params.id as string;
  // Sample data — no admin API/role system exists yet. See docs/FEATURES.md.
  const stats = [
    { n: "42", label: "Vault entries" },
    { n: "3", label: "Devices" },
    { n: "128 days", label: "Account age" },
  ];
</script>

<div class="gp-screen">
  <div class="gp-head">
    <span class="gp-avatar">{id[4]?.toUpperCase() ?? "U"}</span>
    <div class="gp-head-text">
      <span class="gp-h1">{id}</span>
      <span class="gp-caption">Anonymized user id · no name or email shown</span>
    </div>
    <Badge kind="neutral">Active</Badge>
  </div>

  <div class="gp-grid">
    {#each stats as s}
      <div class="gp-stat">
        <span class="gp-h1">{s.n}</span>
        <span class="gp-caption">{s.label}</span>
      </div>
    {/each}
  </div>

  <div class="gp-banner">
    <span aria-hidden="true">🔒</span>
    <p class="gp-caption" style="color:var(--gp-text)">
      Vault contents are end-to-end encrypted. This page cannot show entry names, sites, or passwords.
    </p>
  </div>

  <div class="gp-row">
    <Button variant="secondary">Send password reset</Button>
    <Button variant="danger">Disable account</Button>
  </div>
</div>

<style>
  .gp-head {
    display: flex;
    align-items: center;
    gap: 16px;
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
  .gp-head-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
  }
  .gp-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
  }
  @media (min-width: 640px) {
    .gp-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  .gp-stat {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 18px;
    border-radius: var(--gp-radius-lg);
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
  }
  .gp-banner {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-primary-soft);
  }
  .gp-row {
    display: flex;
    gap: 12px;
  }
</style>
