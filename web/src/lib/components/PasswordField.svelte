<script lang="ts">
  let {
    label,
    value = $bindable(""),
    hint = "",
    autocomplete = "off",
  }: { label: string; value?: string; hint?: string; autocomplete?: string } = $props();
  let visible = $state(false);
  const id = $derived("f-" + label.replace(/\s+/g, "-").toLowerCase());
</script>

<label class="gp-field" for={id}>
  <span class="gp-label">{label}</span>
  <div class="gp-pw-row">
    <input {id} type={visible ? "text" : "password"} autocomplete={autocomplete as any} bind:value />
    <button type="button" class="gp-eye" onclick={() => (visible = !visible)} aria-label={visible ? "Hide password" : "Show password"}>
      {visible ? "🙈" : "👁"}
    </button>
  </div>
  {#if hint}<span class="gp-caption">{hint}</span>{/if}
</label>

<style>
  .gp-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .gp-pw-row {
    display: flex;
    align-items: center;
    height: 48px;
    padding: 0 8px 0 16px;
    border-radius: var(--gp-radius-md);
    border: 1px solid var(--gp-border);
    background: var(--gp-surface-alt);
  }
  input {
    flex: 1;
    height: 100%;
    border: none;
    background: transparent;
    color: var(--gp-text);
    font-size: 14px;
  }
  input:focus {
    outline: none;
  }
  .gp-eye {
    background: none;
    border: none;
    font-size: 16px;
    padding: 8px;
  }
</style>
