<script lang="ts">
  import "../lib/styles/tokens.css";
  import "../lib/styles/global.css";
  let { children }: { children: () => any } = $props();
</script>

{@render children()}
