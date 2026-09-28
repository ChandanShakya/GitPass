<script lang="ts">
  import Badge from "./Badge.svelte";
  let {
    title,
    subtitle,
    initial,
    status,
    onclick,
  }: {
    title: string;
    subtitle: string;
    initial: string;
    status?: "weak" | "breached";
    onclick?: () => void;
  } = $props();
</script>

<button class="gp-entry-card" {onclick}>
  <span class="gp-avatar">{initial}</span>
  <span class="gp-entry-text">
    <span class="gp-body-strong">{title}</span>
    <span class="gp-caption">{subtitle}</span>
  </span>
  {#if status}<Badge kind={status}>{status === "weak" ? "Weak" : "Breached"}</Badge>{/if}
</button>

<style>
  .gp-entry-card {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 14px 16px;
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
    border-radius: var(--gp-radius-lg);
    text-align: left;
  }
  .gp-avatar {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 999px;
    background: var(--gp-primary-soft);
    color: var(--gp-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
  }
  .gp-entry-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .gp-entry-text > * {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
</style>
