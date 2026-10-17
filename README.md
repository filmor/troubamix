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

4. Open **Troubamix** from the Nextcloud app navigation. Select a mix, then use
   the play, pause, and stop controls and the per-track gain and pan sliders.

The app has no database migrations or per-user settings. Song metadata and
audio files are served from the bundled `static/songs/` directory.

## Development

Install dependencies with `pnpm install --frozen-lockfile`. Run
`pnpm run check` to type-check the Svelte project and
`pnpm run build:nextcloud` to rebuild the assets loaded by Nextcloud. Copy the
updated generated assets into your local Nextcloud app directory and reload
Troubamix to test changes.

The original standalone SvelteKit site remains available with `pnpm run dev`
and `pnpm run build`.
