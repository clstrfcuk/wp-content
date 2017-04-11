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

$screen = get_current_screen();

wp_enqueue_script( $plugin_title . '-jquery-timepicker-js', $plugin_url . '/js/jquery-timepicker/jquery.timepicker.js', array( 'jquery' ), NULL, true );

if ( $screen->id == 'addons_page_x-extensions-snippet' || $screen->id == 'snippet' ) {

  wp_enqueue_script( $plugin_title . '-admin-js', $plugin_url . '/js/admin/main.js', array( 'jquery' ), NULL, true );
  wp_enqueue_media();

} else {

  wp_enqueue_script( $plugin_title . '-metaboxes-js', $plugin_url . '/js/admin/main-metaboxes.js', array( 'jquery' ), NULL, true );
  wp_enqueue_media();

}
