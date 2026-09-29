<script lang="ts">
  // Sample data — no admin API/role system exists yet. See docs/FEATURES.md.
  let require2fa = $state(false);
  let alertOnFailedLogins = $state(true);
  let maintenanceMode = $state(false);
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Platform settings</h1>
  <p class="gp-caption">Operational controls — never vault settings. These never touch anyone's encrypted data.</p>

  <span class="gp-label" style="color:var(--gp-text-muted)">Platform</span>
  <div class="gp-list">
    <label class="gp-row">
      <div class="gp-text"><span class="gp-body-strong">Require 2FA for admin accounts</span><span class="gp-caption">Off</span></div>
      <input type="checkbox" bind:checked={require2fa} />
    </label>
    <label class="gp-row">
      <div class="gp-text"><span class="gp-body-strong">Alert on repeated failed logins</span><span class="gp-caption">On, after 10 attempts in 15 min</span></div>
      <input type="checkbox" bind:checked={alertOnFailedLogins} />
    </label>
    <label class="gp-row">
      <div class="gp-text"><span class="gp-body-strong">Maintenance mode</span><span class="gp-caption">Off — the API stays live</span></div>
      <input type="checkbox" bind:checked={maintenanceMode} />
    </label>
  </div>

  <span class="gp-label" style="color:var(--gp-text-muted)">Limits</span>
  <div class="gp-list">
    <div class="gp-row"><div class="gp-text"><span class="gp-body-strong">Rate limit window</span><span class="gp-caption">15 minutes, 10 attempts per key</span></div></div>
    <div class="gp-row"><div class="gp-text"><span class="gp-body-strong">Session refresh token lifetime</span><span class="gp-caption">30 days</span></div></div>
    <div class="gp-row"><div class="gp-text"><span class="gp-body-strong">Access token lifetime</span><span class="gp-caption">15 minutes</span></div></div>
  </div>
</div>

<style>
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 8px;
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
  }
  input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: var(--gp-primary);
  }
</style>
