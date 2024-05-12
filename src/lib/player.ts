import type Song from "./song";

export default class Player {

    public static async create(context: AudioContext, song: Song): Promise<Player>
    {
        const buffers = new Map<string, AudioBuffer>();
        await Promise.all(song.tracks.map(async (track) => {
            const url = `/${song.name}/${track.filename}`;
            const resp = await fetch(url);
            const buf = await resp.arrayBuffer();
            const decoded = await context.decodeAudioData(buf);

            buffers.set(track.id, decoded);
        }));

        return new Player(context, song, buffers);
    }

    constructor(
        private readonly context: AudioContext,
        private readonly song: Song,
        private readonly buffers: Map<string, AudioBuffer>) {
    }

        //
        // const src = ctx.createBufferSource();
        // src.buffer = buffer;
        // const gain = ctx.createGain();
        // const pan = ctx.createStereoPanner();
        //
        // src.connect(gain).connect(pan).connect(ctx.destination);

}
