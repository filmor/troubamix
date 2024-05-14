<script lang="ts">
  import { onMount } from "svelte";
  import Player from "$lib/player.js";
  import Sliders from "./Sliders.svelte";
  import PlayerControls from "./PlayerControls.svelte";

  export let data;

  $: song = data.song;
  let player: Player;

  onMount(async () => {
    const ctx = new window.AudioContext();
    player = await Player.create(ctx, song);
    window.player = player;
  });
</script>

<h2 class="font-bold text-xl">Mix {song.long_name}</h2>

<div class="flex-col">
  {#if player}
    <PlayerControls {player} />
    {#each Object.entries(song.tracks) as [id, track]}
      <Sliders {track} state={player.states[id]} />
    {/each}
  {:else}
    Loading ...
  {/if}
</div>
