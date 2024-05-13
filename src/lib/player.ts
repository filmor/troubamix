import type Song from "./song";

class TrackState {
  public playing = false;
  public gain: GainNode;
  public pan: StereoPannerNode;
  context: BaseAudioContext;
  source: AudioBufferSourceNode;

  constructor(
    private output: AudioNode,
    private buffer: AudioBuffer,
  ) {
    this.context = output.context;

    this.gain = this.context.createGain();
    this.pan = this.context.createStereoPanner();
    this.source = this.context.createBufferSource();

    this.gain.connect(this.pan).connect(this.output);
  }

  reset() {
    this.source = this.context.createBufferSource();
    this.source.buffer = this.buffer;
    this.source.connect(this.gain);
  }

  public play(when: number, offset: number): void {
    this.reset();
    this.source.start(when, offset);
    this.playing = true;
  }

  public stop(when: number): void {
    if (this.playing) {
      this.source.stop(when);
      this.playing = false;
    }
  }
}

export default class Player {
  // schedule offset in milliseconds
  static SCHED_OFFSET = 50;

  playing = false;
  lastStart = -1;
  position = 0.0;
  gain: GainNode;
  public states = new Map<string, TrackState>();

  public static async create(
    context: AudioContext,
    song: Song,
  ): Promise<Player> {
    const buffers = new Map<string, AudioBuffer>();
    await Promise.all(
      (function* () {
        for (const [id, track] of song.tracks.entries()) {
          yield async () => {
            const url = `/${song.name}/${track.filename}`;
            const resp = await fetch(url);
            const buf = await resp.arrayBuffer();
            const decoded = await context.decodeAudioData(buf);

            buffers.set(id, decoded);
          };
        }
      })(),
    );

    return new Player(context, song, buffers);
  }

  constructor(
    private readonly context: AudioContext,
    private readonly song: Song,
    readonly buffers: Map<string, AudioBuffer>,
  ) {
    this.gain = this.context.createGain();
    this.gain.connect(context.destination);
    for (const id in this.song.tracks) {
      const st = new TrackState(this.gain, buffers.get(id)!);
      this.states.set(id, st);
    }
  }

  public play(offset?: number): void {
    if (this.playing) return;

    if (offset === undefined) offset = 0;

    const startTime = this.context.currentTime + Player.SCHED_OFFSET;

    for (const st of this.states.values()) {
      st.play(startTime, offset + this.position);
    }

    this.playing = true;
    this.lastStart = startTime;
  }

  public pause(): void {
    const stopTime = this.context.currentTime + Player.SCHED_OFFSET;

    for (const st of this.states.values()) {
      st.stop(stopTime);
    }

    this.playing = false;
    this.position += stopTime - this.lastStart;
  }

  public stop(): void {
    this.pause();
    this.position = 0.0;
  }
}
