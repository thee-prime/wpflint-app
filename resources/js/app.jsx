/**
 * React application entry point.
 *
 * Mounts <App> into the #{{TEXT_DOMAIN}}-app container.
 * WordPress passes initial data via wp_localize_script as window.wpData.
 */
import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';

const container = document.getElementById( '{{TEXT_DOMAIN}}-app' );

if ( container ) {
  const root = createRoot( container );
  root.render(
    <React.StrictMode>
      <App data={ window.wpData || {} } />
    </React.StrictMode>
  );
}
