<script lang="ts">
  import Player from "$lib/player.js";

  export let playing: boolean = false;
  export let player: Player;

  function togglePlayer() {
    if (player) {
      player.toggle();
      playing = player.playing;
    }
  }

  function stopPlayer() {
    if (player) {
      player.stop();
      playing = player.playing;
    }
  }
</script>

<div class="inline-flex overflow-hidden rounded-lg border bg-white shadow-sm">
  <button
    class="inline-block border-e p-3 hover:bg-gray-200 focus:relative"
    on:click={togglePlayer}
  >
    {#if playing}
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6"
      >
        <title>Pause</title>
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M15.75 5.25v13.5m-7.5-13.5v13.5"
        />
      </svg>
    {:else}
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6"
      >
        <title>Play</title>
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"
        />
      </svg>
    {/if}
  </button>
  <button
    class="inline-block border-e p-3 hover:bg-gray-200 focus:relative"
    on:click={stopPlayer}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke-width="1.5"
      stroke="currentColor"
      class="w-6 h-6"
    >
      <title>Stop</title>
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"
      />
    </svg>
  </button>
</div>
