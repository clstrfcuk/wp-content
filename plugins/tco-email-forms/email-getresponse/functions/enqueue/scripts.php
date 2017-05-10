<?php

// =============================================================================
// EMAIL-GETRESPONSE/FUNCTIONS/ENQUEUE/SCRIPTS.PHP
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

function x_email_getresponse_enqueue_admin_scripts( $hook ) {

  if (
    ($hook == 'addons_page_x-extensions-email-forms' || $hook == 'email-forms' || $hook == 'theme_page_x-extensions-email-forms' || $hook == 'x-pro_page_x-extensions-email-forms')
    && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'getresponse' )
  ) {

    // wp_enqueue_script( 'x-email-getresponse-admin-js', X_EMAIL_GETRESPONSE_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_email_getresponse_enqueue_admin_scripts' );
