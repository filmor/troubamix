<script lang="ts">
  import { onMount } from "svelte";
  import { Player } from "../lib/player";
  import type { Song } from "../lib/song";
  import PlayerControls from "../routes/mix/[song]/PlayerControls.svelte";
  import Sliders from "../routes/mix/[song]/Sliders.svelte";

  type Config = {
    appUrl: string;
    songsUrl: string;
    song: string | null;
  };

  let { config }: { config: Config } = $props();
  let song: Song | null = $state(null);
  let player: Player | undefined = $state();
  let error: string | null = $state(null);

  onMount(async () => {
    if (!config.song) return;

    try {
      const response = await fetch(
        `${config.songsUrl}/${encodeURIComponent(config.song)}/song.json`,
      );
      if (!response.ok) throw new Error("Song metadata could not be loaded");

      song = (await response.json()) as Song;
      player = await Player.create(
        new window.AudioContext(),
        song,
        config.songsUrl,
      );
    } catch {
      error = "This mix could not be loaded.";
    }
  });
</script>

{#if config.song}
  {#if song}
    <h2 class="font-bold text-xl">Mix {song.long_name}</h2>
  {/if}

  <div class="flex flex-col rounded-md border">
    {#if player && song}
      <PlayerControls {player} />
      <div class="flex flex-row">
        {#each Object.entries(song.tracks) as [id, track]}
          <Sliders {track} state={player.states[id]} />
        {/each}
      </div>
    {:else if error}
      <p>{error}</p>
    {:else}
      <p>Loading ...</p>
    {/if}
  </div>
{:else}
  <h1>Troubamix</h1>

  <ul class="list-dics">
    <li><a href="{config.appUrl}mix/owner">Owner</a></li>
    <li>
      <a href="{config.appUrl}mix/pdm">Purple Disco Machine Medley</a>
    </li>
  </ul>
{/if}
