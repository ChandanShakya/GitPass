<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import Button from "$lib/components/Button.svelte";
  import TextField from "$lib/components/TextField.svelte";
  import PasswordField from "$lib/components/PasswordField.svelte";
  import { deriveUnlockKeys, toB64 } from "$lib/vaultCrypto";
  import { api, ApiError } from "$lib/api";
  import { session } from "$lib/session.svelte";
  import { fromB64 } from "@gitpass/crypto";
  import { goto } from "$app/navigation";

  let email = $state("");
  let password = $state("");
  let busy = $state(false);
  let error = $state("");

  async function submit(e: Event) {
    e.preventDefault();
    error = "";
    busy = true;
    try {
      // Login is two round trips: first fetch the KDF params (salt,
      // iterations) for this email, then derive locally and send only the
      // resulting authKey — the password itself never leaves the device.
      const info = await api.get<{ salt: string; kdfIterations: number }>(
        `/auth/kdf-params?email=${encodeURIComponent(email)}`,
      );
      const { authKey, kek } = await deriveUnlockKeys(password, fromB64(info.salt), info.kdfIterations);
      const res = await api.post<{
        accessToken: string;
        refreshToken: string;
        userId: string;
        wrappedDek: string;
      }>("/auth/login", { email, authKey: toB64(authKey), deviceName: navigator.userAgent.slice(0, 40) });

      session.setAuth(res.userId, res.accessToken, res.refreshToken);
      sessionStorage.setItem("gp.email", email);
      const { unwrapKey } = await import("@gitpass/crypto");
      const dek = await unwrapKey(res.wrappedDek, kek, "dek");
      session.setUnlocked(dek);
      goto("/vault");
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
    <h1 class="gp-h1">Welcome back</h1>
    <p class="gp-body" style="color:var(--gp-text-muted)">Enter your master password to unlock your vault.</p>

    <form onsubmit={submit} class="gp-form">
      <TextField label="Email" type="email" placeholder="you@example.com" bind:value={email} autocomplete="email" />
      <PasswordField label="Master password" hint="Only you know it. We cannot reset it." bind:value={password} autocomplete="current-password" />
      {#if error}<p class="gp-caption" style="color:var(--gp-danger)">{error}</p>{/if}
      <Button type="submit" variant="primary" disabled={busy}>{busy ? "Unlocking…" : "Unlock vault"}</Button>
      <Button variant="ghost" onclick={() => goto("/recovery-key/restore")}>Use a passkey or recovery key</Button>
    </form>

    <p class="gp-body-strong" style="color:var(--gp-primary)">
      <a href="/register" style="text-decoration:none;color:inherit">New to GitPass? Create an account</a>
    </p>
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
