import { svelte } from "@sveltejs/vite-plugin-svelte";
import { fileURLToPath } from "node:url";
import { defineConfig } from "vite";

const projectRoot = fileURLToPath(new URL(".", import.meta.url));

export default defineConfig({
  plugins: [svelte()],
  resolve: {
    alias: {
      $lib: `${projectRoot}src/lib`,
    },
  },
  publicDir: false,
  build: {
    lib: {
      entry: `${projectRoot}src/nextcloud/main.ts`,
      name: "Troubamix",
      formats: ["iife"],
      fileName: () => "js/troubamix.js",
      cssFileName: "troubamix",
    },
    outDir: projectRoot,
    emptyOutDir: false,
    rollupOptions: {
      output: {
        assetFileNames: "css/[name][extname]",
      },
    },
  },
});
