<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Plugin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Scripts
// =============================================================================

// Enqueue Admin Scripts
// =============================================================================

function x_olark_integration_enqueue_admin_scripts( $hook ) {

  if ( $hook == 'addons_page_x-extensions-olark-integration' ) {

    wp_enqueue_script( 'x-olark-integration-admin-js', X_OLARK_INTEGRATION_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_olark_integration_enqueue_admin_scripts' );