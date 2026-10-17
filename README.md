# Troubamix

Troubamix is a browser-based mixer for the individual vocal tracks of a song.
It is available as a Nextcloud app and can also be run as the original
standalone SvelteKit site.

## Requirements

- Nextcloud 28 or later
- PHP 8.1 or later
- A modern browser with Web Audio API support

To build the Nextcloud frontend, install Node.js 18 or later and pnpm.

## Install in Nextcloud

1. Install the frontend dependencies and build the app assets:

   ```sh
   pnpm install --frozen-lockfile
   pnpm run build:nextcloud
   ```

2. Copy the complete `troubamix` directory, including the generated
   `js/troubamix.js` and `css/troubamix.css`, into Nextcloud's `apps/` directory.
   The directory name must be `troubamix`.
3. Enable the app from the Nextcloud app settings or run:

   ```sh
   occ app:enable troubamix
   ```

4. Open **Troubamix** from the Nextcloud app navigation to browse available
   mixes, or use the file action **Open with Troubamix** on a `troubamix.json`
   file in the Files app. The action also works for song folders shared with
   your account. Use the play, pause, and stop controls and the per-track gain
   and pan sliders.

The app has no database migrations or per-user settings. It finds song folders
in the signed-in user's Nextcloud files by searching for a `troubamix.json`
marker. The marker contains the song metadata (`name`, `long_name`, and
`tracks`, with each track containing an `id` and `name`). For example:

```json
{
  "name": "owner",
  "long_name": "Owner of a lonely heart",
  "tracks": [
    { "id": "sop", "name": "Soprano" },
    { "id": "alto", "name": "Alto" }
  ]
}
```

Place the matching MP3 files in the same folder, named
`{name}-{track-id}.mp3` (for example, `owner-sop.mp3` and `owner-alto.mp3`).
Troubamix only serves the track files declared by the marker. The app uses
Nextcloud's file APIs and current-user permissions to discover and stream these
files.

## Development

Install dependencies with `pnpm install --frozen-lockfile`. Run
`pnpm run check` to type-check the Svelte project and
`pnpm run build:nextcloud` to rebuild the assets loaded by Nextcloud. Copy the
updated generated assets into your local Nextcloud app directory and reload
Troubamix to test changes.

The original standalone SvelteKit site remains available with `pnpm run dev`
and `pnpm run build`.
