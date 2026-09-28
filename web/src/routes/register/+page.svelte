<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import Button from "$lib/components/Button.svelte";
  import TextField from "$lib/components/TextField.svelte";
  import PasswordField from "$lib/components/PasswordField.svelte";
  import { setupNewVault } from "$lib/vaultCrypto";
  import { api, ApiError } from "$lib/api";
  import { goto } from "$app/navigation";

  let email = $state("");
  let password = $state("");
  let confirm = $state("");
  let busy = $state(false);
  let error = $state("");

  const strength = $derived(scorePassword(password));
  function scorePassword(pw: string): { score: number; label: string; color: string } {
    let score = 0;
    if (pw.length >= 12) score++;
    if (pw.length >= 20) score++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    if (/[0-9]/.test(pw) || /\W/.test(pw)) score++;
    const labels = [
      { label: "Too short", color: "var(--gp-danger)" },
      { label: "Weak. Add more words.", color: "var(--gp-danger)" },
      { label: "Fair. A little longer helps.", color: "var(--gp-warn)" },
      { label: "Strong.", color: "var(--gp-success)" },
      { label: "Very strong. Would take centuries to guess.", color: "var(--gp-success)" },
    ];
    return { score, ...labels[score] };
  }

  async function submit(e: Event) {
    e.preventDefault();
    error = "";
    if (password.length < 8) {
      error = "Use at least 8 characters — longer is much stronger.";
      return;
    }
    if (password !== confirm) {
      error = "Passwords don't match.";
      return;
    }
    busy = true;
    try {
      const keys = await setupNewVault(password);
      await api.post("/auth/register", {
        email,
        authKey: keys.authKey,
        salt: keys.salt,
        kdfIterations: 600000,
        wrappedDek: keys.wrappedDek,
        wrappedDekRecovery: keys.wrappedDekRecovery,
      });
      sessionStorage.setItem("gp.pendingRecoveryKey", keys.recoveryKeyFormatted);
      sessionStorage.setItem("gp.pendingEmail", email);
      goto("/recovery-key");
    } catch (err) {
      error = err instanceof ApiError ? err.message : "Something went wrong. Try again.";
    } finally {
      busy = false;
    }
  }
</script>

<div class="gp-app">
  <div class="gp-screen">
    <div class="gp-logo-row"><BrandLogo size={36} /><span class="gp-h2">GitPass</span></div>
    <p class="gp-label" style="color:var(--gp-primary)">STEP 1 OF 3</p>
    <h1 class="gp-h1">Create your master password</h1>
    <p class="gp-body" style="color:var(--gp-text-muted)">One strong password protects everything. Tip: use 4 or more random words.</p>

    <form onsubmit={submit} class="gp-form">
      <TextField label="Email" type="email" placeholder="you@example.com" bind:value={email} autocomplete="email" />
      <PasswordField label="Master password" bind:value={password} autocomplete="new-password" />
      {#if password}
        <div class="gp-strength">
          <div class="gp-strength-bars">
            {#each [0, 1, 2, 3] as i}
              <span class="gp-bar" style="background:{i <= strength.score ? strength.color : 'var(--gp-border)'}"></span>
            {/each}
          </div>
          <span class="gp-caption" style="color:{strength.color}">{strength.label}</span>
        </div>
      {/if}
      <PasswordField label="Confirm master password" bind:value={confirm} autocomplete="new-password" />

      <div class="gp-callout">
        <span aria-hidden="true">⚠️</span>
        <p class="gp-caption" style="color:var(--gp-text)">
          We cannot recover this password. If you forget it, your data is gone. Next you will save a recovery key.
        </p>
      </div>

      {#if error}<p class="gp-caption" style="color:var(--gp-danger)">{error}</p>{/if}
      <Button type="submit" variant="primary" disabled={busy}>{busy ? "Creating…" : "Continue"}</Button>
    </form>
  </div>
</div>

<style>
  .gp-logo-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .gp-form {
    display: flex;
    flex-direction: column;
    gap: var(--gp-space-4);
  }
  .gp-strength {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: -8px;
  }
  .gp-strength-bars {
    display: flex;
    gap: 4px;
  }
  .gp-bar {
    flex: 1;
    height: 6px;
    border-radius: 3px;
  }
  .gp-callout {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--gp-radius-md);
    background: var(--gp-warn-soft);
  }
</style>
