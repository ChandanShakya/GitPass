<script lang="ts">
  let {
    variant = "primary",
    type = "button",
    disabled = false,
    fullWidth = true,
    onclick,
    children,
  }: {
    variant?: "primary" | "secondary" | "ghost" | "danger";
    type?: "button" | "submit";
    disabled?: boolean;
    /** false for a compact inline button next to other content in a row (e.g. a list row's action) — default true matches the design's full-width form/detail buttons. */
    fullWidth?: boolean;
    onclick?: (e: MouseEvent) => void;
    children: () => any;
  } = $props();
</script>

<button class="gp-btn gp-btn--{variant}" class:gp-btn--auto={!fullWidth} {type} {disabled} {onclick}>
  {@render children()}
</button>

<style>
  .gp-btn {
    width: 100%;
    flex-shrink: 0;
    min-height: 48px;
    padding: 12px 24px;
    border-radius: var(--gp-radius-md);
    border: none;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.15s ease;
  }
  .gp-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  .gp-btn:not(:disabled):active {
    opacity: 0.85;
  }
  .gp-btn--primary {
    background: var(--gp-primary);
    color: var(--gp-on-primary);
  }
  .gp-btn--secondary {
    background: var(--gp-primary-soft);
    color: var(--gp-primary);
  }
  .gp-btn--ghost {
    background: transparent;
    color: var(--gp-primary);
    border: 1px solid var(--gp-border);
  }
  .gp-btn--danger {
    background: var(--gp-danger-soft);
    color: var(--gp-danger);
  }
  .gp-btn--auto {
    width: auto;
    padding: 10px 16px;
    min-height: 40px;
  }
</style>
