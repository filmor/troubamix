import type Track from "./track";

export default interface Song {
  name: string;
  long_name: string;
  score?: string;
  tracks: Track[];
}
