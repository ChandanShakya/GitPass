<script lang="ts">
  let {
    variant = "primary",
    type = "button",
    disabled = false,
    onclick,
    children,
  }: {
    variant?: "primary" | "secondary" | "ghost" | "danger";
    type?: "button" | "submit";
    disabled?: boolean;
    onclick?: (e: MouseEvent) => void;
    children: () => any;
  } = $props();
</script>

<button class="gp-btn gp-btn--{variant}" {type} {disabled} {onclick}>
  {@render children()}
</button>

<style>
  .gp-btn {
    width: 100%;
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
</style>
