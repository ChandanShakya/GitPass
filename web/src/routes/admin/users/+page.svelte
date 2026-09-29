<script lang="ts">
  import Badge from "$lib/components/Badge.svelte";
  import Button from "$lib/components/Button.svelte";
  import { goto } from "$app/navigation";

  // Sample data — no admin API/role system exists yet. See docs/FEATURES.md.
  const users = [
    { id: "usr_8f2a1c", entries: 42, devices: 3, age: "128 days", status: "Active" },
    { id: "usr_1b77e0", entries: 12, devices: 1, age: "40 days", status: "Active" },
    { id: "usr_c40912", entries: 88, devices: 2, age: "301 days", status: "Active" },
    { id: "usr_0a55b7", entries: 5, devices: 1, age: "3 days", status: "Disabled" },
  ];
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Users</h1>
  <div class="gp-banner">
    <span aria-hidden="true">🛡</span>
    <p class="gp-caption" style="color:var(--gp-text)">Anonymized ids only. No email, name, or vault content is ever shown here.</p>
  </div>
  <div class="gp-list">
    {#each users as u}
      <div class="gp-row">
        <div class="gp-text">
          <span class="gp-mono">{u.id}</span>
          <span class="gp-caption">{u.entries} entries · {u.devices} devices · {u.age} old</span>
        </div>
        <Badge kind={u.status === "Active" ? "intact" : "neutral"}>{u.status}</Badge>
        <Button variant="secondary" fullWidth={false} onclick={() => goto(`/admin/users/${u.id}`)}>View</Button>
      </div>
    {/each}
  </div>
</div>

<style>
  .gp-banner {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-primary-soft);
    margin-bottom: 4px;
  }
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .gp-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border-radius: var(--gp-radius-lg);
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
  }
  .gp-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
  }
</style>
