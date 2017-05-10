<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Plugin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
//   02. Enqueue Admin Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

function x_under_construction_enqueue_site_scripts() {

  require( X_UNDER_CONSTRUCTION_PATH . '/functions/options.php' );

  if ( isset( $x_under_construction_enable ) && $x_under_construction_enable == 1 ) {

    wp_enqueue_script( 'x-under-construction-site-js', X_UNDER_CONSTRUCTION_URL . '/js/site/countdown.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'wp_enqueue_scripts', 'x_under_construction_enqueue_site_scripts' );



// Enqueue Admin Scripts
// =============================================================================

function x_under_construction_enqueue_admin_scripts( $hook ) {
  if ( $hook == 'addons_page_x-extensions-under-construction' || $hook == 'theme_page_x-extensions-under-construction' || $hook === 'x_page_x-extensions-under-construction' || $hook == 'x-pro_page_x-extensions-under-construction' ) {

    wp_enqueue_script( 'x-under-construction-admin-js', X_UNDER_CONSTRUCTION_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );
    wp_enqueue_media();

  }

}

add_action( 'admin_enqueue_scripts', 'x_under_construction_enqueue_admin_scripts' );


// Password callback
// =============================================================================

add_action( 'wp_ajax_x_under_construction_bypass', 'x_under_construction_bypass_callback' );
add_action( 'wp_ajax_nopriv_x_under_construction_bypass', 'x_under_construction_bypass_callback' );

function x_under_construction_bypass_callback() {

  check_ajax_referer( 'x_under_construction_bypass', 'x_under_construction_ajax_nonce' );

  require( X_UNDER_CONSTRUCTION_PATH . '/functions/options.php' );

  $password = $_POST['x_under_construction_bypass_password'];

  if ( $password === $x_under_construction_bypass_password ) {
    setcookie( 'x_under_construction_bypass', 'true', time() + 1 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
    echo 'done';
    wp_die();
  } else {
    echo 'error';
    wp_die();
  }

}
