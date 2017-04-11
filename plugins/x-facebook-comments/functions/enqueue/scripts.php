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

function x_facebook_comments_enqueue_admin_scripts( $hook ) {

  if ( $hook == 'addons_page_x-extensions-facebook-comments' ) {

    wp_enqueue_script( 'x-facebook-comments-admin-js', X_FACEBOOK_COMMENTS_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_facebook_comments_enqueue_admin_scripts' );