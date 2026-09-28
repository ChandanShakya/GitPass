<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import Button from "$lib/components/Button.svelte";
  import PasswordField from "$lib/components/PasswordField.svelte";
  import { deriveUnlockKeys, toB64 } from "$lib/vaultCrypto";
  import { api, ApiError } from "$lib/api";
  import { session } from "$lib/session.svelte";
  import { fromB64, unwrapKey } from "@gitpass/crypto";
  import { goto } from "$app/navigation";
  import { onMount } from "svelte";

  let email = $state("");
  let password = $state("");
  let busy = $state(false);
  let error = $state("");

  onMount(() => {
    email = sessionStorage.getItem("gp.email") ?? "";
    if (!email) goto("/login");
  });

  async function submit(e: Event) {
    e.preventDefault();
    error = "";
    busy = true;
    try {
      const info = await api.get<{ salt: string; kdfIterations: number }>(`/auth/kdf-params?email=${encodeURIComponent(email)}`);
      const { authKey, kek } = await deriveUnlockKeys(password, fromB64(info.salt), info.kdfIterations);
      const res = await api.post<{ accessToken: string; refreshToken: string; userId: string; wrappedDek: string }>("/auth/login", {
        email,
        authKey: toB64(authKey),
        deviceName: navigator.userAgent.slice(0, 40),
      });
      session.setAuth(res.userId, res.accessToken, res.refreshToken);
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
  <div class="gp-screen gp-center">
    <div class="gp-avatar"><BrandLogo size={28} /></div>
    <h1 class="gp-h1">Welcome back</h1>
    <p class="gp-body" style="color:var(--gp-text-muted)">Enter your master password to unlock.</p>
    <form onsubmit={submit} class="gp-form">
      <PasswordField label="Master password" bind:value={password} autocomplete="current-password" />
      {#if error}<p class="gp-caption" style="color:var(--gp-danger)">{error}</p>{/if}
      <Button type="submit" variant="primary" disabled={busy}>{busy ? "Unlocking…" : "Unlock vault"}</Button>
      <Button variant="ghost" onclick={() => { session.signOut(); goto("/login"); }}>Use a different account</Button>
    </form>
  </div>
</div>

<style>
  .gp-center {
    align-items: center;
    text-align: center;
  }
  .gp-avatar {
    width: 64px;
    height: 64px;
    border-radius: 999px;
    background: var(--gp-primary-soft);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .gp-form {
    display: flex;
    flex-direction: column;
    gap: var(--gp-space-4);
    width: 100%;
  }
</style>
