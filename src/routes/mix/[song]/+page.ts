import { error } from "@sveltejs/kit";
import { songs } from "$lib/data.ts";

export async function load({ fetch, params }) {
    const song = songs.find((song) => params.song == song.name)

    if (!song)
        throw error(404);

    const trackData = await Promise.all(song.tracks.map(async (track) => {
        const url = `/${song.name}/${track.file}`;
        const resp = await fetch(url);
        const buf = await resp.arrayBuffer();
        return buf;
    }));

    console.log(trackData);

    for (const i in song.tracks) {
        const t = song.tracks[i];
        t.data = trackData[i];
    }

    return { song };
}
