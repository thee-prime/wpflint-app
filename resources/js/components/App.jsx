/**
 * Root application component.
 *
 * Props:
 *   data.ajaxUrl  — WordPress admin-ajax.php URL
 *   data.nonce    — Request nonce
 *   data.restUrl  — WordPress REST API root URL
 *   data.pluginUrl — Plugin directory URL
 *
 * Add more fields via the '{{TEXT_DOMAIN}}_script_data' filter in PHP.
 */
import React from 'react';

export default function App( { data } ) {
  return (
    <div className="{{TEXT_DOMAIN}}-app">
      <h2>{{PLUGIN_NAME}}</h2>
      <p>
        Edit <code>resources/js/components/App.jsx</code> to get started.
      </p>
    </div>
  );
}
