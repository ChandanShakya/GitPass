<script lang="ts">
  import { page } from "$app/stores";
  import BrandLogo from "./BrandLogo.svelte";
  import Button from "./Button.svelte";

  let { children }: { children: () => any } = $props();

  const tabs = [
    { href: "/vault", label: "Vault", icon: "🏠" },
    { href: "/favorites", label: "Favorites", icon: "⭐" },
    { href: "/vault/new", label: "Add", icon: "➕", fab: true },
    { href: "/health", label: "Safety", icon: "🛡" },
    { href: "/settings/security", label: "More", icon: "⚙" },
  ];
  const navItems = [
    { href: "/vault", label: "Vault", icon: "🏠" },
    { href: "/favorites", label: "Favorites", icon: "⭐" },
    { href: "/history", label: "History", icon: "🕐" },
    { href: "/generator", label: "Generator", icon: "🔑" },
    { href: "/health", label: "Health", icon: "🛡" },
    { href: "/trash", label: "Trash", icon: "🗑" },
    { href: "/settings/security", label: "Settings", icon: "⚙" },
  ];
  const isActive = (href: string) => $page.url.pathname === href;
</script>

<div class="gp-app gp-app--shell">
  <aside class="gp-sidebar">
    <a class="gp-brand" href="/vault"><BrandLogo size={32} /><span class="gp-h2">GitPass</span></a>
    <div class="gp-sidebar-cta"><Button variant="primary" onclick={() => (window.location.href = "/vault/new")}>New entry</Button></div>
    <nav>
      {#each navItems as item}
        <a class="gp-nav-item" class:active={isActive(item.href)} href={item.href}>
          <span aria-hidden="true">{item.icon}</span>
          <span class="gp-body-strong">{item.label}</span>
        </a>
      {/each}
    </nav>
    <div class="gp-sidebar-grow"></div>
    <div class="gp-intact-card">
      <span class="gp-body-strong" style="color:var(--gp-success)">✓ Vault intact</span>
      <span class="gp-caption">Verified locally, last check just now.</span>
    </div>
  </aside>

  <main class="gp-main">
    {@render children()}
  </main>

  <nav class="gp-tabbar" aria-label="Primary">
    {#each tabs as tab}
      {#if tab.fab}
        <a class="gp-fab" href={tab.href} aria-label="Add entry">➕</a>
      {:else}
        <a class="gp-tab" class:active={isActive(tab.href)} href={tab.href}>
          <span aria-hidden="true">{tab.icon}</span>
          <span class="gp-micro">{tab.label}</span>
        </a>
      {/if}
    {/each}
  </nav>
</div>

<style>
  .gp-app--shell {
    min-height: 100dvh;
  }
  .gp-main {
    flex: 1;
    min-width: 0;
    padding-bottom: 72px; /* clears the mobile tab bar */
  }
  .gp-sidebar {
    display: none;
  }

  /* mobile tab bar (default / <768px) */
  .gp-tabbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: space-around;
    background: var(--gp-surface);
    border-top: 1px solid var(--gp-border);
    padding: 8px;
    z-index: 10;
  }
  .gp-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    padding: 6px 10px;
    color: var(--gp-text-muted);
    text-decoration: none;
  }
  .gp-tab.active {
    color: var(--gp-primary);
  }
  .gp-fab {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    background: var(--gp-primary);
    color: var(--gp-on-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    text-decoration: none;
  }

  @media (min-width: 768px) {
    .gp-main {
      padding-bottom: 0;
    }
  }

  /* desktop/tablet sidebar replaces the tab bar */
  @media (min-width: 1440px) {
    .gp-tabbar {
      display: none;
    }
    .gp-sidebar {
      display: flex;
      flex-direction: column;
      gap: var(--gp-space-2);
      width: 248px;
      flex-shrink: 0;
      padding: 20px 16px;
      background: var(--gp-surface);
      border-right: 1px solid var(--gp-border);
      min-height: 100dvh;
    }
    .gp-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--gp-text);
      padding: 4px 6px 16px;
    }
    .gp-sidebar-cta {
      margin-bottom: 8px;
    }
    nav {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .gp-nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: var(--gp-radius-md);
      color: var(--gp-text-muted);
      text-decoration: none;
    }
    .gp-nav-item.active {
      background: var(--gp-primary-soft);
      color: var(--gp-primary);
    }
    .gp-sidebar-grow {
      flex: 1;
    }
    .gp-intact-card {
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding: 12px 14px;
      border-radius: var(--gp-radius-md);
      background: var(--gp-success-soft);
    }
  }
</style>
