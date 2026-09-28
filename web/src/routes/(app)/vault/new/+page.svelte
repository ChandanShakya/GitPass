<script lang="ts">
  import TextField from "$lib/components/TextField.svelte";
  import PasswordField from "$lib/components/PasswordField.svelte";
  import Button from "$lib/components/Button.svelte";
  import { vaultStore } from "$lib/vaultStore.svelte";
  import { goto } from "$app/navigation";

  let title = $state("");
  let username = $state("");
  let password = $state("");
  let url = $state("");
  let notes = $state("");
  let busy = $state(false);

  function generate() {
    const chars = "ABCDEFGHJKMNPQRSTVWXYZabcdefghijkmnpqrstvwxyz23456789!@#$%^&*";
    password = Array.from(crypto.getRandomValues(new Uint32Array(16)), (n) => chars[n % chars.length]).join("");
  }

  async function submit(e: Event) {
    e.preventDefault();
    busy = true;
    try {
      const id = await vaultStore.create({ title, username, password, url, notes });
      goto(`/vault/${id}`);
    } finally {
      busy = false;
    }
  }
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Add new entry</h1>
  <form onsubmit={submit} class="gp-form">
    <TextField label="Website" placeholder="https://github.com" bind:value={url} />
    <TextField label="Title" placeholder="GitHub" bind:value={title} />
    <TextField label="Username or email" placeholder="chandan@example.com" bind:value={username} autocomplete="username" />
    <PasswordField label="Password" bind:value={password} autocomplete="new-password" />
    <Button variant="secondary" onclick={generate}>Generate a strong password</Button>
    <TextField label="Notes" placeholder="Add a note. Only you can read it." bind:value={notes} />
    <Button type="submit" variant="primary" disabled={busy || !title}>{busy ? "Saving…" : "Save (v1)"}</Button>
  </form>
</div>

<style>
  .gp-form {
    display: flex;
    flex-direction: column;
    gap: var(--gp-space-4);
  }
</style>
