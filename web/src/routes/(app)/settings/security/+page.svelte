<script lang="ts">
  import { session } from "$lib/session.svelte";
  import { goto } from "$app/navigation";

  function signOut() {
    session.signOut();
    goto("/login");
  }
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Security</h1>
  <div class="gp-list">
    <div class="gp-row"><span class="gp-body-strong">Auto-lock after idle</span><span class="gp-caption">Not wired yet — see docs/WEB_PLAN.md</span></div>
    <div class="gp-row"><span class="gp-body-strong">Two-factor authentication</span><span class="gp-caption">Not implemented yet</span></div>
  </div>
  <button class="gp-signout" onclick={signOut}>Sign out</button>
</div>

<style>
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .gp-row {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 14px 16px;
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
    border-radius: var(--gp-radius-lg);
  }
  .gp-signout {
    margin-top: 12px;
    background: none;
    border: none;
    color: var(--gp-danger);
    font-weight: 700;
    text-align: left;
    padding: 12px 0;
  }
</style>
