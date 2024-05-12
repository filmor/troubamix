import type { Song, Track } from "./song";

export const songs: [Song] = [
  {
    name: "owner",
    long_name: "Owner of a lonely heart",
    score_file: "owner.pdf",
    tracks: [
      {
        id: "sop",
        name: "Sopran",
        file: "owner-sop.mp3"
      },
      {
        id: "bass",
        name: "Bass",
        file: "owner-bass.mp3"
      },
    ]
  }
];
