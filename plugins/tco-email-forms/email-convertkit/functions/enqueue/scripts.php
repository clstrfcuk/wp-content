<?php

// =============================================================================
// EMAIL-CONVERTKIT/FUNCTIONS/ENQUEUE/SCRIPTS.PHP
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

function x_email_convertkit_enqueue_admin_scripts( $hook ) {

  if (
    ($hook == 'addons_page_x-extensions-email-forms' || $hook == 'email-forms' || $hook == 'theme_page_x-extensions-email-forms' || $hook == 'x-pro_page_x-extensions-email-forms')
    && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'convertkit' )
  ) {

    // wp_enqueue_script( 'x-email-convertkit-admin-js', X_EMAIL_CONVERTKIT_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_email_convertkit_enqueue_admin_scripts' );
