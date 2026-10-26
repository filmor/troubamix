import type { Track } from "./track";

export interface Song {
  id?: string;
  folderName?: string;
  name: string;
  long_name: string;
  score?: string;
  tracks: Track[];
}
