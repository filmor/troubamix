export interface Track {
    name: string,
    filename: string,
}

export interface Song {
    name: string,
    long_name: string,
    score_file: string,
    tracks: [Track]
};
