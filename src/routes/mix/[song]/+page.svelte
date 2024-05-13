<script lang="ts">
  import { onMount } from "svelte";
  import Player from "$lib/player.js";

  export let data;
  $: song = data.song;

  onMount(async () => {
    const ctx = new window.AudioContext();
    const player = await Player.create(ctx, song);

    window.player = player;
  });
</script>

<h1>Mix {song.long_name}</h1>

<button on:click={window.player ? window.player.play() : null}>Play</button>
