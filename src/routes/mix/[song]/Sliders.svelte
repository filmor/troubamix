<script lang="ts">
  import type { Track } from "$lib/track";
  import { TrackState } from "$lib/track_state";

  type Props = { track: Track, state: TrackState };

  let { track, state }: Props = $props();
</script>

<div class="flex flex-col w-20">
  <h3 class="font-bold text-lg">{track.name}</h3>
  <!-- pan -->
  <input type="range" class="w-16" bind:value={state.pan} />
  <!-- gain -->
  <input
    type="range"
    class="[writing-mode:vertical-lr] [direction:rtl]"
    bind:value={state.gain}
  />
</div>
