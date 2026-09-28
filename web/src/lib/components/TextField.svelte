<script lang="ts">
  let {
    label,
    value = $bindable(""),
    type = "text",
    placeholder = "",
    hint = "",
    autocomplete = "off",
  }: {
    label: string;
    value?: string;
    type?: "text" | "email";
    placeholder?: string;
    hint?: string;
    autocomplete?: string;
  } = $props();
  const id = $derived("f-" + label.replace(/\s+/g, "-").toLowerCase());
</script>

<label class="gp-field" for={id}>
  <span class="gp-label">{label}</span>
  <input {id} {type} {placeholder} autocomplete={autocomplete as any} bind:value />
  {#if hint}<span class="gp-caption">{hint}</span>{/if}
</label>

<style>
  .gp-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  input {
    height: 48px;
    padding: 0 16px;
    border-radius: var(--gp-radius-md);
    border: 1px solid var(--gp-border);
    background: var(--gp-surface-alt);
    color: var(--gp-text);
    font-size: 14px;
  }
  input:focus {
    outline: 2px solid var(--gp-primary);
    outline-offset: 1px;
  }
</style>
