import type Track from "./track";

export default interface Song {
  name: string;
  long_name: string;
  score_file: string;
  tracks: Map<string, Track>;
}
