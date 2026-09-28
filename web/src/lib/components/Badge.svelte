<script lang="ts">
  let {
    kind = "neutral",
    children,
  }: { kind?: "intact" | "version" | "weak" | "breached" | "neutral"; children: () => any } = $props();
</script>

<span class="gp-badge gp-badge--{kind}">{@render children()}</span>

<style>
  .gp-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: var(--gp-radius-pill);
    font-size: 11px;
    font-weight: 600;
    line-height: 14px;
    white-space: nowrap;
  }
  .gp-badge--intact {
    background: var(--gp-success-soft);
    color: var(--gp-success);
  }
  .gp-badge--version {
    background: var(--gp-primary-soft);
    color: var(--gp-primary);
  }
  .gp-badge--weak {
    background: var(--gp-warn-soft);
    color: var(--gp-warn);
  }
  .gp-badge--breached {
    background: var(--gp-danger-soft);
    color: var(--gp-danger);
  }
  .gp-badge--neutral {
    background: var(--gp-surface-alt);
    color: var(--gp-text-muted);
  }
</style>
