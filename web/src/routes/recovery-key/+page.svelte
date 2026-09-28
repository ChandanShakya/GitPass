<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import Button from "$lib/components/Button.svelte";
  import { goto } from "$app/navigation";
  import { onMount } from "svelte";

  let recoveryKey = $state("");
  onMount(() => {
    recoveryKey = sessionStorage.getItem("gp.pendingRecoveryKey") ?? "";
    if (!recoveryKey) goto("/register");
  });

  function copyKey() {
    navigator.clipboard?.writeText(recoveryKey);
  }
  function downloadKey() {
    const blob = new Blob(
      [`GitPass recovery key\n\n${recoveryKey}\n\nKeep this somewhere safe and offline. GitPass never stores it and cannot show it again.`],
      { type: "text/plain" },
    );
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "gitpass-recovery-key.txt";
    a.click();
    URL.revokeObjectURL(a.href);
  }
  function proceed() {
    sessionStorage.removeItem("gp.pendingRecoveryKey");
    goto("/login");
  }
</script>

<div class="gp-app">
  <div class="gp-screen">
    <div class="gp-logo-row"><BrandLogo size={36} /><span class="gp-h2">GitPass</span></div>
    <p class="gp-label" style="color:var(--gp-primary)">STEP 2 OF 3</p>
    <h1 class="gp-h1">Save your recovery key</h1>
    <p class="gp-body" style="color:var(--gp-text-muted)">
      It is the only way back in if you forget your master password. Print it or store it somewhere safe, offline.
    </p>

    <div class="gp-key-box gp-mono">
      {#each [recoveryKey.split("-").slice(0, 4).join("-"), recoveryKey.split("-").slice(4).join("-")] as line}
        <div>{line}</div>
      {/each}
    </div>

    <div class="gp-row">
      <Button variant="secondary" onclick={downloadKey}>Download .txt</Button>
      <Button variant="secondary" onclick={copyKey}>Copy key</Button>
    </div>

    <div class="gp-callout">
      <span aria-hidden="true">⚠️</span>
      <p class="gp-caption" style="color:var(--gp-text)">GitPass never stores this key. Not even we can show it again.</p>
    </div>

    <Button variant="primary" onclick={proceed}>I saved it. Continue</Button>
  </div>
</div>

<style>
  .gp-logo-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .gp-key-box {
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: center;
    padding: 20px;
    border-radius: var(--gp-radius-lg);
    border: 1px solid var(--gp-border);
    background: var(--gp-surface);
    font-size: 20px;
  }
  .gp-row {
    display: flex;
    gap: 12px;
  }
  .gp-callout {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-warn-soft);
  }
</style>
