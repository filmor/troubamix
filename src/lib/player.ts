import { TrackState } from "./track_state";
import type Song from "./song";

export default class Player {
  // schedule offset in seconds
  static SCHED_OFFSET = 0.01;

  playing = false;
  lastStart = -1;
  position = 0.0;
  gain: GainNode;
  public states: Record<string, TrackState> = {};

  public static async create(
    context: AudioContext,
    song: Song,
  ): Promise<Player> {
    const buffers: Record<string, AudioBuffer> = {};
    const promises = Object.entries(song.tracks).map(async ([id, track]) => {
      const url = `/${song.name}/${track.filename}`;
      const resp = await fetch(url);
      const buf = await resp.arrayBuffer();
      const decoded = await context.decodeAudioData(buf);

      buffers[id] = decoded;
    });

    await Promise.all(promises);

    return new Player(context, song, buffers);
  }

  constructor(
    private readonly context: AudioContext,
    private readonly song: Song,
    readonly buffers: Record<string, AudioBuffer>,
  ) {
    this.gain = this.context.createGain();
    this.gain.connect(context.destination);
    for (const id in this.song.tracks) {
      this.states[id] = new TrackState(this.gain, buffers[id]);
    }
  }

  public play(offset?: number): void {
    if (this.playing) return;

    if (offset === undefined) offset = 0;

    const startTime = this.context.currentTime + Player.SCHED_OFFSET;

    for (const st of Object.values(this.states)) {
      st.play(startTime, offset + this.position);
    }

    this.playing = true;
    this.lastStart = startTime;
  }

  public pause(): void {
    const stopTime = this.context.currentTime + Player.SCHED_OFFSET;

    for (const st of Object.values(this.states)) {
      st.stop(stopTime);
    }

    this.playing = false;
    this.position += stopTime - this.lastStart - Player.SCHED_OFFSET;
  }

  public stop(): void {
    this.pause();
    this.position = 0.0;
  }

  public toggle(): void {
    if (this.playing) {
      this.pause();
    } else {
      this.play();
    }
  }
}
