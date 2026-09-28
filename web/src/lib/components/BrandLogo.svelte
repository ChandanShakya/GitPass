<script lang="ts">
  let { size = 36 }: { size?: number } = $props();
</script>

<!-- Placeholder for the Figma "brand" component (git-commit-graph mark).
     Swap for the exported SVG asset before shipping — see docs/DESIGN.md. -->
<svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="GitPass">
  <rect width="24" height="24" rx="8" fill="var(--gp-primary)" />
  <circle cx="7" cy="17" r="2" stroke="var(--gp-on-primary)" stroke-width="1.6" />
  <circle cx="17" cy="7" r="2" stroke="var(--gp-on-primary)" stroke-width="1.6" />
  <path d="M8.4 15.6L15.6 8.4" stroke="var(--gp-on-primary)" stroke-width="1.6" stroke-linecap="round" />
</svg>
