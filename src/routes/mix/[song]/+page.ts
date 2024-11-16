import { error } from "@sveltejs/kit";
import type { Song } from "$lib/song";

export async function load({ fetch, params }): Song {
  const response = await fetch(`/songs/${params.song}/song.json`);
  if (!response.ok)
    throw error(404);

  const song = await response.json() as Song;
  console.log(song);
  
  // const song = songs.find((song) => params.song == song.name);

  // if (!song) throw error(404);

  return song;
}
