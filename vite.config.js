import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { readdirSync } from 'fs';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';

// ESM-safe __dirname (not available natively in ES modules).
const __dirname = dirname( fileURLToPath( import.meta.url ) );

// Auto-discover every *.css in resources/css/ as a separate entry point.
// PHP's ViteAssets::register_css_dir() reads these from the manifest automatically.
const cssEntries = {};
try {
  readdirSync( 'resources/css' ).forEach( ( file ) => {
    if ( file.endsWith( '.css' ) ) {
      cssEntries[ `resources/css/${ file }` ] = resolve( __dirname, 'resources/css', file );
    }
  } );
} catch ( _ ) {}

export default defineConfig( {
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
        'resources/js/app.jsx': resolve( __dirname, 'resources/js/app.jsx' ),
        ...cssEntries,
      },
    },
    emptyOutDir: true,
  },

  server: {
    cors: true,
    port: 5173,
    strictPort: true,
  },
} );
