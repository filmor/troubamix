import { error } from "@sveltejs/kit";
import { songs } from "$lib/data.js";

export async function load({ params }) {
  const song = songs.find((song) => params.song == song.name);

  if (!song) throw error(404);

  return { song };
}
