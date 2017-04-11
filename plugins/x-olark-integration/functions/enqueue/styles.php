<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Styles
// =============================================================================

// Enqueue Admin Styles
// =============================================================================

function x_olark_integration_enqueue_admin_styles( $hook ) {

  if ( $hook == 'addons_page_x-extensions-olark-integration' ) {

    wp_enqueue_style( 'x-olark-integration-admin-css', X_OLARK_INTEGRATION_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'x_olark_integration_enqueue_admin_styles' );