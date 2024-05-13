import type Song from "./song";

export const songs: Song[] = [
  {
    name: "owner",
    long_name: "Owner of a lonely heart",
    score_file: "owner.pdf",
    tracks: {
      sop: {
        id: "sop",
        name: "Sopran",
        filename: "owner-sop.mp3",
      },
      bass: {
        id: "bass",
        name: "Bass",
        filename: "owner-bass.mp3",
      },
    },
  },
];
