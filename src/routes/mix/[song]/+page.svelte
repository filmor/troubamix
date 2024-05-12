<script lang="ts">
import { onMount } from "svelte";

export let data;
$: song = data.song;

onMount(async () => {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    const ctx = new AudioContext();

    const controls = {};

    await Promise.all(song.tracks.map(async (track) => {
        const src = ctx.createBufferSource();
        const buffer = await ctx.decodeAudioData(track.data);
        src.buffer = buffer;
        const gain = ctx.createGain();
        const pan = ctx.createStereoPanner();

        src.connect(gain).connect(pan).connect(ctx.destination);

        controls[track.id] = {
            src,
            gain,
            pan
        };
    }));

    window.controls = controls;
});
</script>

<h1>Mix {song.long_name}</h1>
