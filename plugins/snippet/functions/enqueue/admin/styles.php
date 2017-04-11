<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/ADMIN/STYLES.PHP
// -----------------------------------------------------------------------------
// Enqueue admin styles for the plugin. This file is included within the
// 'admin_enqueue_scripts' action.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Styles
// =============================================================================

// Enqueue Admin Styles
// =============================================================================

$screen = get_current_screen();

wp_enqueue_style( $plugin_title . '-jquery-timepicker-css', $plugin_url . '/js/jquery-timepicker/jquery.timepicker.css', NULL, NULL, 'all' );

if ( $screen->id == 'addons_page_x-extensions-snippet' ) {

  wp_enqueue_style( $plugin_title . '-admin-css', $plugin_url . '/css/admin/style.css', NULL, NULL, 'all' );

} else {

  wp_enqueue_style( $plugin_title . '-metaboxes-css', $plugin_url . '/css/admin/style-metaboxes.css', NULL, NULL, 'all' );

}
