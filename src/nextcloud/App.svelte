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
  let songs: Song[] = $state([]);
  let player: Player | undefined = $state();
  let error: string | null = $state(null);

  onMount(async () => {
    try {
      if (!config.song) {
        const response = await fetch(config.songsUrl);
        if (!response.ok) throw new Error("Song folders could not be loaded");
        songs = (await response.json()) as Song[];
        return;
      }

      const response = await fetch(
        `${config.songsUrl}/${encodeURIComponent(config.song)}`,
      );
      if (!response.ok) throw new Error("Song metadata could not be loaded");
      song = (await response.json()) as Song;
      player = await Player.create(
        new window.AudioContext(),
        song,
        `${config.songsUrl}/${encodeURIComponent(config.song)}/files`,
      );
    } catch {
      error = config.song
        ? "This mix could not be loaded."
        : "Song folders could not be loaded.";
    }
  });
</script>

{#if config.song}
  {#if song}
    <h2 class="font-bold text-xl">
      Mix {song.long_name || song.folderName || song.name}
    </h2>
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
  {#if error}
    <p>{error}</p>
  {:else if songs.length > 0}
    <ul class="list-dics">
      {#each songs as availableSong (availableSong.id)}
        <li>
          <a href="{config.appUrl}mix/{encodeURIComponent(availableSong.id ?? '')}">
            {availableSong.long_name || availableSong.folderName || availableSong.name}
          </a>
        </li>
      {/each}
    </ul>
  {:else}
    <p>No mixes found. Add a <code>troubamix.json</code> file to a song folder in your Nextcloud files.</p>
  {/if}
{/if}
