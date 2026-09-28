<script lang="ts">
  import Button from "$lib/components/Button.svelte";

  let length = $state(16);
  let useUpper = $state(true);
  let useNumbers = $state(true);
  let useSymbols = $state(true);
  let password = $state("");

  function generate() {
    let chars = "abcdefghijkmnpqrstuvwxyz";
    if (useUpper) chars += "ABCDEFGHJKMNPQRSTUVWXYZ";
    if (useNumbers) chars += "23456789";
    if (useSymbols) chars += "!@#$%^&*-_=+";
    password = Array.from(crypto.getRandomValues(new Uint32Array(length)), (n) => chars[n % chars.length]).join("");
  }
  generate();

  const strength = $derived(length >= 20 ? "Very strong" : length >= 14 ? "Strong" : "Fair");
  function copy() {
    navigator.clipboard?.writeText(password);
  }
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Generator</h1>
  <div class="gp-card">
    <div class="gp-output">
      <span class="gp-mono" style="flex:1">{password}</span>
      <button class="gp-icon-btn" onclick={generate} aria-label="Regenerate">🔄</button>
    </div>
    <p class="gp-caption" style="color:var(--gp-success)">{strength}. Would take a long time to guess.</p>

    <div class="gp-length-row">
      <span class="gp-label">Length</span>
      <span class="gp-label" style="color:var(--gp-primary)">{length}</span>
    </div>
    <input type="range" min="8" max="32" bind:value={length} oninput={generate} />

    <label class="gp-toggle-row"><input type="checkbox" bind:checked={useUpper} onchange={generate} /> Uppercase letters (A-Z)</label>
    <label class="gp-toggle-row"><input type="checkbox" bind:checked={useNumbers} onchange={generate} /> Numbers (0-9)</label>
    <label class="gp-toggle-row"><input type="checkbox" bind:checked={useSymbols} onchange={generate} /> Symbols (!@#$)</label>

    <Button variant="secondary" onclick={copy}>Copy</Button>
  </div>
</div>

<style>
  .gp-card {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 24px;
    border-radius: var(--gp-radius-lg);
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
  }
  .gp-output {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-surface-alt);
    border: 1px solid var(--gp-border);
  }
  .gp-icon-btn {
    background: none;
    border: none;
    font-size: 16px;
  }
  .gp-length-row {
    display: flex;
    justify-content: space-between;
  }
  .gp-toggle-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
  }
</style>
