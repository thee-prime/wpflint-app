import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { readdirSync } from 'fs';
import { resolve } from 'path';

// Auto-discover every *.css in resources/css/ as a separate entry point.
// PHP's ViteAssets::register_css_dir() reads these from the manifest automatically.
const cssEntries = {};
try {
  readdirSync('resources/css').forEach((file) => {
    if (file.endsWith('.css')) {
      cssEntries[`resources/css/${file}`] = resolve(__dirname, 'resources/css', file);
    }
  });
} catch (_) {}

export default defineConfig({
  plugins: [
    react(),
    // __TAILWIND_PLUGIN__
  ],

  build: {
    outDir: 'dist',
    // manifest.json lets PHP resolve hashed filenames at runtime.
    manifest: true,
    rollupOptions: {
      input: {
        'resources/js/app.jsx': resolve(__dirname, 'resources/js/app.jsx'),
        ...cssEntries,
      },
    },
    // Clean dist/ before every production build.
    emptyOutDir: true,
  },

  server: {
    // Allow WordPress (served on a different port/domain) to load assets.
    cors: true,
    port: 5173,
    // Required for HMR when WordPress is served over HTTPS locally.
    strictPort: true,
  },
});
