<script type="ts">
  import { onMount } from "svelte";

  onMount(() => {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    const ctx = new AudioContext();

    const sop = document.getElementById("song-sop");
    const bass = document.getElementById("song-bass");

    const sopTrack = ctx.createMediaElementSource(sop);
    const bassTrack = ctx.createMediaElementSource(bass);

    const sopGain = ctx.createGain();
    const bassGain = ctx.createGain();

    const sopPan = ctx.createStereoPanner();
    const bassPan = ctx.createStereoPanner();

    sopTrack.connect(sopGain).connect(sopPan).connect(ctx.destination);
    bassTrack.connect(bassGain).connect(bassPan).connect(ctx.destination);

    window.tracks = [sopTrack, bassTrack];
  });
</script>

<audio id="song-sop" src="owner/owner-sop.mp3"></audio>
<audio id="song-bass" src="owner/owner-bass.mp3"></audio>

<h1 class="text-3xl font-bold underline">Hello world 2!</h1>

<a href="/mix/owner">Owner</a>

<style lang="postcss">
  :global(html) {
    background-color: theme(colors.gray.100);
  }
</style>
