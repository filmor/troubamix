<script lang="ts">
  import { onMount } from "svelte";
  import { Player } from "$lib/player";
  import Sliders from "./Sliders.svelte";
  import PlayerControls from "./PlayerControls.svelte";
  import type { Song } from "$lib/song";

  let props: {data: Song} = $props();
  let song: Song = $derived(props.data);
  let player: Player = $state() as Player;

  onMount(async () => {
    const ctx = new window.AudioContext();
    player = await Player.create(ctx, song);
    // window.player = player;
  });
</script>

<h2 class="font-bold text-xl">Mix {song.long_name}</h2>

<div class="flex flex-col rounded-md border">
  {#if player}
    <PlayerControls {player} />
    <div class="flex flex-row">
    {#each Object.entries(song.tracks) as [id, track]}
      <Sliders {track} state={player.states[id]} />
    {/each}
    </div>
    <!-- <PdfViewer url="/{song.name}/{song.score_file}" /> -->
  {:else}
    Loading ...
  {/if}
</div>
