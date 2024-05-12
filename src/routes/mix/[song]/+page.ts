import { error } from "@sveltejs/kit";
import { songs } from "$lib/data.js";

export async function load({ fetch, params }) {
    const song = songs.find((song) => params.song == song.name)

    if (!song)
        throw error(404);

    await Promise.all(song.tracks.map(async (track) => {
        const url = `/${song.name}/${track.filename}`;
        const resp = await fetch(url);
        const buf = await resp.arrayBuffer();
        track.data = buf;
    }));

    return { song };
}
