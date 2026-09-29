<script lang="ts">
  const stats = [
    { n: "12,847", label: "Total users" },
    { n: "128,004", label: "Encrypted entries" },
    { n: "99.98%", label: "Sync success (7d)" },
    { n: "1", label: "Open incidents" },
  ];
  const errors = [
    { id: "usr_8f2a1c", msg: "Conflict on item_versions — resolved by client retry", when: "2 min ago" },
    { id: "usr_1b77e0", msg: "Timeout during delta sync", when: "18 min ago" },
    { id: "usr_c40912", msg: "Device token expired", when: "1 hr ago" },
  ];
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Admin dashboard</h1>
  <div class="gp-banner">
    <span aria-hidden="true">🛡</span>
    <p class="gp-caption" style="color:var(--gp-text)">
      Admins can never view, change, or export vault passwords. Everything below is metadata only.
    </p>
  </div>

  <div class="gp-grid">
    {#each stats as s}
      <div class="gp-stat">
        <span class="gp-display">{s.n}</span>
        <span class="gp-caption">{s.label}</span>
      </div>
    {/each}
  </div>

  <h2 class="gp-h2">Recent sync errors</h2>
  <div class="gp-list">
    {#each errors as e}
      <div class="gp-row">
        <div class="gp-text">
          <span class="gp-mono">{e.id}</span>
          <span class="gp-caption">{e.msg}</span>
        </div>
        <span class="gp-caption">{e.when}</span>
      </div>
    {/each}
  </div>
  <p class="gp-caption">Sample data — not wired to a live metrics source yet. See docs/FEATURES.md.</p>
</div>

<style>
  .gp-banner {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-primary-soft);
  }
  .gp-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
  }
  @media (min-width: 640px) {
    .gp-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  @media (min-width: 1000px) {
    .gp-grid {
      grid-template-columns: repeat(4, 1fr);
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
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .gp-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
  }
  .gp-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
</style>
