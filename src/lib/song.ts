import type { Track } from "./track";

export interface Song {
  name: string;
  long_name: string;
  score?: string;
  tracks: Track[];
}
