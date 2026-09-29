<script lang="ts">
  import Badge from "$lib/components/Badge.svelte";
  import Button from "$lib/components/Button.svelte";

  // Sample data — no admin API/role system exists yet. See docs/FEATURES.md.
  const incidents = [
    { kind: "breached" as const, sev: "Critical", msg: "Repeated failed logins (12x)", id: "usr_0a55b7", when: "4 min ago", resolved: false },
    { kind: "weak" as const, sev: "Warning", msg: "Rate limit triggered for IP 203.0.113.4", id: "—", when: "22 min ago", resolved: false },
    { kind: "weak" as const, sev: "Warning", msg: "Session token expired unexpectedly", id: "usr_1b77e0", when: "1 hr ago", resolved: true },
    { kind: "neutral" as const, sev: "Info", msg: "Scheduled D1 backup completed", id: "—", when: "6 hr ago", resolved: true },
  ];
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Incidents</h1>
  <div class="gp-banner">
    <span aria-hidden="true">⚠</span>
    <p class="gp-caption" style="color:var(--gp-text)">System and security events. None of these can reveal vault contents.</p>
  </div>
  <div class="gp-list">
    {#each incidents as inc}
      <div class="gp-row">
        <Badge kind={inc.kind}>{inc.sev}</Badge>
        <div class="gp-text">
          <span class="gp-body-strong">{inc.msg}</span>
          <span class="gp-caption">{inc.id} · {inc.when}</span>
        </div>
        {#if inc.resolved}
          <Badge kind="neutral">Resolved</Badge>
        {:else}
          <Button variant="secondary" fullWidth={false}>Resolve</Button>
        {/if}
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
    background: var(--gp-warn-soft);
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
