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

function x_facebook_comments_enqueue_admin_styles( $hook ) {

  if ( $hook == 'addons_page_x-extensions-facebook-comments' ) {

    wp_enqueue_style( 'x-facebook-comments-admin-css', X_FACEBOOK_COMMENTS_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'x_facebook_comments_enqueue_admin_styles' );