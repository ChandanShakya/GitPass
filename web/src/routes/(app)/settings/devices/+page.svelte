<script lang="ts">
  import Button from "$lib/components/Button.svelte";
  import Badge from "$lib/components/Badge.svelte";
  import { api } from "$lib/api";
  import { onMount } from "svelte";

  interface Device {
    id: string;
    device_name: string;
    created_at: number;
    last_seen_at: number;
  }
  let devices = $state<Device[]>([]);
  onMount(async () => {
    const res = await api.get<{ devices: Device[] }>("/devices");
    devices = res.devices;
  });
  async function revoke(id: string) {
    await api.del(`/devices/${id}`);
    devices = devices.filter((d) => d.id !== id);
  }
</script>

<div class="gp-screen">
  <h1 class="gp-h1">Devices and activity</h1>
  <div class="gp-list">
    {#each devices as d, i (d.id)}
      <div class="gp-row">
        <div class="gp-text">
          <span class="gp-body-strong">{d.device_name}</span>
          <span class="gp-caption">last active {new Date(d.last_seen_at).toLocaleString()}</span>
        </div>
        {#if i === 0}<Badge kind="intact">This device</Badge>{:else}<Button variant="danger" onclick={() => revoke(d.id)}>Sign out</Button>{/if}
      </div>
    {/each}
  </div>
</div>

<style>
  .gp-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .gp-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: var(--gp-surface);
    border: 1px solid var(--gp-border);
    border-radius: var(--gp-radius-lg);
  }
  .gp-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
  }
</style>
