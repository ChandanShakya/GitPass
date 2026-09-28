<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import Button from "$lib/components/Button.svelte";
  import TextField from "$lib/components/TextField.svelte";
  import PasswordField from "$lib/components/PasswordField.svelte";
  import { unlockWithRecoveryKey, deriveUnlockKeys, toB64 } from "$lib/vaultCrypto";
  import { api, ApiError } from "$lib/api";
  import { session } from "$lib/session.svelte";
  import { wrapKey, randomBytes } from "@gitpass/crypto";
  import { goto } from "$app/navigation";

  let email = $state("");
  let recoveryKey = $state("");
  let newPassword = $state("");
  let busy = $state(false);
  let error = $state("");
  let step = $state<"key" | "new-password">("key");
  let userId = "";
  let dek: Uint8Array | null = null;

  async function verifyKey(e: Event) {
    e.preventDefault();
    error = "";
    busy = true;
    try {
      const info = await api.get<{ userId: string; wrappedDekRecovery: string }>(`/auth/recovery-info?email=${encodeURIComponent(email)}`);
      dek = await unlockWithRecoveryKey(recoveryKey, info.wrappedDekRecovery);
      userId = info.userId;
      step = "new-password";
    } catch {
      error = "That email and recovery key don't match.";
    } finally {
      busy = false;
    }
  }

  async function setNewPassword(e: Event) {
    e.preventDefault();
    if (!dek) return;
    error = "";
    busy = true;
    try {
      const salt = randomBytes(16);
      const { authKey, kek } = await deriveUnlockKeys(newPassword, salt);
      const wrappedDek = await wrapKey(dek, kek, "dek");
      await api.post("/auth/reset-password", { userId, authKey: toB64(authKey), salt: toB64(salt), kdfIterations: 600000, wrappedDek });
      goto("/login");
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

    {#if step === "key"}
      <h1 class="gp-h1">Use your recovery key</h1>
      <p class="gp-body" style="color:var(--gp-text-muted)">Enter the email and recovery key you saved when you created your account.</p>
      <form onsubmit={verifyKey} class="gp-form">
        <TextField label="Email" type="email" bind:value={email} autocomplete="email" />
        <TextField label="Recovery key" placeholder="K7QM-2XPD-9TFA-HN4C-XW8Z" bind:value={recoveryKey} />
        {#if error}<p class="gp-caption" style="color:var(--gp-danger)">{error}</p>{/if}
        <Button type="submit" variant="primary" disabled={busy}>{busy ? "Checking…" : "Continue"}</Button>
      </form>
    {:else}
      <h1 class="gp-h1">Set a new master password</h1>
      <p class="gp-body" style="color:var(--gp-text-muted)">Your recovery key stays valid — it isn't used up by this.</p>
      <form onsubmit={setNewPassword} class="gp-form">
        <PasswordField label="New master password" bind:value={newPassword} autocomplete="new-password" />
        {#if error}<p class="gp-caption" style="color:var(--gp-danger)">{error}</p>{/if}
        <Button type="submit" variant="primary" disabled={busy || newPassword.length < 8}>{busy ? "Saving…" : "Set new password"}</Button>
      </form>
    {/if}
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
</style>
