import { error } from "@sveltejs/kit";
// import { Song } from "$lib/song.js";

export async function load({ fetch, params }) {
  const response = await fetch(`/songs/${params.song}/song.json`);
  if (!response.ok)
    throw error(404);

  const song = await response.json();
  console.log(song);

  
  // const song = songs.find((song) => params.song == song.name);

  // if (!song) throw error(404);

  return { song };
}
