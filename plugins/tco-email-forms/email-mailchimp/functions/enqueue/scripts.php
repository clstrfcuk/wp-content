<?php

// =============================================================================
// EMAIL-MAILCHIMP/FUNCTIONS/ENQUEUE/SCRIPTS.PHP
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

function email_forms_mailchimp_enqueue_admin_scripts( $hook ) {

  if ( get_post_type() === 'email-forms' ) {

    wp_enqueue_script( 'x-email-mailchimp-admin-js', X_EMAIL_MAILCHIMP_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'email_forms_mailchimp_enqueue_admin_scripts' );
