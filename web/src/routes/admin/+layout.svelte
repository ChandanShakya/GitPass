<script lang="ts">
  import BrandLogo from "$lib/components/BrandLogo.svelte";
  import { page } from "$app/stores";

  let { children }: { children: () => any } = $props();

  const nav = [
    { href: "/admin", label: "Overview", icon: "🏠" },
    { href: "/admin/users", label: "Users", icon: "👤" },
    { href: "/admin/incidents", label: "Incidents", icon: "⚠" },
    { href: "/admin/settings", label: "Settings", icon: "⚙" },
  ];
  const isActive = (href: string) => $page.url.pathname === href;
</script>

<div class="gp-admin-app">
  <!-- mobile-first default: a slim top bar + bottom tab bar (see .gp-admin-tabbar) -->
  <header class="gp-admin-topbar">
    <BrandLogo size={28} />
    <span class="gp-admin-badge">ADMIN</span>
  </header>

  <aside class="gp-admin-sidebar">
    <div class="gp-admin-brand"><BrandLogo size={32} /><span class="gp-h2">GitPass</span></div>
    <span class="gp-admin-badge">ADMIN</span>
    <nav>
      {#each nav as item}
        <a class="gp-admin-nav-item" class:active={isActive(item.href)} href={item.href}>
          <span aria-hidden="true">{item.icon}</span>
          <span class="gp-body-strong">{item.label}</span>
        </a>
      {/each}
    </nav>
    <div class="gp-admin-grow"></div>
    <div class="gp-admin-lockcard">
      <span class="gp-label" style="color:var(--gp-warn)">🔒 Zero-knowledge</span>
      <span class="gp-caption">You cannot view or export vault contents.</span>
    </div>
  </aside>

  <main class="gp-admin-main">
    {@render children()}
  </main>

  <nav class="gp-admin-tabbar" aria-label="Admin">
    {#each nav as item}
      <a class="gp-admin-tab" class:active={isActive(item.href)} href={item.href}>
        <span aria-hidden="true">{item.icon}</span>
        <span class="gp-micro">{item.label}</span>
      </a>
    {/each}
  </nav>
</div>

<style>
  /* ---- mobile default (no media query = the phone layout) ---- */
  .gp-admin-app {
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
  }
  .gp-admin-topbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--gp-surface);
    border-bottom: 1px solid var(--gp-border);
  }
  .gp-admin-badge {
    padding: 3px 8px;
    border-radius: var(--gp-radius-pill);
    background: var(--gp-primary-soft);
    color: var(--gp-primary);
    font-size: 11px;
    font-weight: 700;
  }
  .gp-admin-sidebar {
    display: none;
  }
  .gp-admin-main {
    flex: 1;
    min-width: 0;
    padding-bottom: 72px; /* clears the bottom tab bar */
  }
  .gp-admin-tabbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: var(--gp-surface);
    border-top: 1px solid var(--gp-border);
    padding: 8px;
    z-index: 10;
  }
  .gp-admin-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    padding: 6px 10px;
    color: var(--gp-text-muted);
    text-decoration: none;
  }
  .gp-admin-tab.active {
    color: var(--gp-primary);
  }

  /* ---- desktop/tablet: sidebar replaces the top bar + tab bar ---- */
  @media (min-width: 900px) {
    .gp-admin-app {
      flex-direction: row;
    }
    .gp-admin-topbar,
    .gp-admin-tabbar {
      display: none;
    }
    .gp-admin-main {
      padding-bottom: 0;
    }
    .gp-admin-sidebar {
      display: flex;
      flex-direction: column;
      gap: var(--gp-space-2);
      width: 240px;
      flex-shrink: 0;
      padding: 20px 16px;
      background: var(--gp-surface);
      border-right: 1px solid var(--gp-border);
    }
    .gp-admin-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding-bottom: 8px;
    }
    .gp-admin-sidebar > .gp-admin-badge {
      align-self: flex-start;
      margin-bottom: 12px;
    }
    nav {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .gp-admin-nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: var(--gp-radius-md);
      color: var(--gp-text-muted);
      text-decoration: none;
    }
    .gp-admin-nav-item.active {
      background: var(--gp-primary-soft);
      color: var(--gp-primary);
    }
    .gp-admin-grow {
      flex: 1;
    }
    .gp-admin-lockcard {
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding: 12px 14px;
      border-radius: var(--gp-radius-md);
      background: var(--gp-warn-soft);
    }
  }
</style>
