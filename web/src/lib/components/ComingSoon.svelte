<script lang="ts">
  let { title, description }: { title: string; description: string } = $props();
</script>

<div class="gp-screen">
  <h1 class="gp-h1">{title}</h1>
  <div class="gp-stub">
    <p class="gp-body" style="color:var(--gp-text-muted)">{description}</p>
    <p class="gp-caption">Designed in Figma, not wired up yet — see docs/WEB_PLAN.md milestones 4–5.</p>
  </div>
</div>

<style>
  .gp-stub {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 20px;
    border-radius: var(--gp-radius-lg);
    background: var(--gp-surface);
    border: 1px dashed var(--gp-border);
  }
</style>
