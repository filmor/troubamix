export class TrackState {
  public playing = false;
  gainNode: GainNode;
  _targetGain: number = 100.0;
  panNode: StereoPannerNode;
  _targetPan: number = 50.0;
  context: BaseAudioContext;
  source: AudioBufferSourceNode;

  constructor(
    private output: AudioNode,
    private buffer: AudioBuffer,
  ) {
    this.context = output.context;

    this.gainNode = this.context.createGain();
    this.panNode = this.context.createStereoPanner();
    this.source = this.context.createBufferSource();
    this.gain = 100.0;
    this.pan = 50.0;

    this.gainNode.connect(this.panNode).connect(this.output);
  }

  reset() {
    this.source = this.context.createBufferSource();
    this.source.buffer = this.buffer;
    this.source.connect(this.gainNode);
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

  public get gain(): number {
    return this._targetGain;
  }

  public set gain(value: number) {
    this._targetGain = value;
    let v = this.gainNode.gain;
    v.cancelScheduledValues(0);
    v.setTargetAtTime(value / 100.0, this.context.currentTime + 0.01, 0.1);
  }

  public get pan(): number {
    return this._targetPan;
  }

  public set pan(value: number) {
    this._targetPan = value;
    let v = this.panNode.pan;
    v.cancelScheduledValues(0);
    v.setTargetAtTime(
      (value / 100) * (v.maxValue - v.minValue) + v.minValue,
      this.context.currentTime + 0.01,
      0.1,
    );
  }
}
