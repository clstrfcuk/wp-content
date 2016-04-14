<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $x_facebook_comments_options;

if ( isset( $_POST['x_facebook_comments_form_submitted'] ) ) {
  if ( strip_tags( $_POST['x_facebook_comments_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    $x_facebook_comments_options['x_facebook_comments_enable']       = ( isset( $_POST['x_facebook_comments_enable'] ) ) ? strip_tags( $_POST['x_facebook_comments_enable'] ) : '';
    $x_facebook_comments_options['x_facebook_comments_app_id']       = strip_tags( $_POST['x_facebook_comments_app_id'] );
    $x_facebook_comments_options['x_facebook_comments_app_secret']   = strip_tags( $_POST['x_facebook_comments_app_secret'] );
    $x_facebook_comments_options['x_facebook_comments_number_posts'] = strip_tags( $_POST['x_facebook_comments_number_posts'] );
    $x_facebook_comments_options['x_facebook_comments_order_by']     = strip_tags( $_POST['x_facebook_comments_order_by'] );
    $x_facebook_comments_options['x_facebook_comments_color_scheme'] = strip_tags( $_POST['x_facebook_comments_color_scheme'] );

    update_option( 'x_facebook_comments', $x_facebook_comments_options );

  }

}



// Get Options
// =============================================================================

$x_facebook_comments_options = apply_filters( 'x_facebook_comments_options', get_option( 'x_facebook_comments' ) );

if ( $x_facebook_comments_options != '' ) {

  $x_facebook_comments_enable       = $x_facebook_comments_options['x_facebook_comments_enable'];
  $x_facebook_comments_app_id       = $x_facebook_comments_options['x_facebook_comments_app_id'];
  $x_facebook_comments_app_secret   = $x_facebook_comments_options['x_facebook_comments_app_secret'];
  $x_facebook_comments_number_posts = $x_facebook_comments_options['x_facebook_comments_number_posts'];
  $x_facebook_comments_order_by     = $x_facebook_comments_options['x_facebook_comments_order_by'];
  $x_facebook_comments_color_scheme = $x_facebook_comments_options['x_facebook_comments_color_scheme'];

}