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

GLOBAL $x_disqus_comments_options;

if ( isset( $_POST['x_disqus_comments_form_submitted'] ) ) {
  if ( sanitize_text_field( $_POST['x_disqus_comments_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    $x_disqus_comments_exclude_post_types_post = ( isset( $_POST['x_disqus_comments_exclude_post_types'] ) ) ? $_POST['x_disqus_comments_exclude_post_types'] : array();
    $x_disqus_comments_options['x_disqus_comments_enable']             = ( isset( $_POST['x_disqus_comments_enable'] ) ) ? sanitize_text_field( $_POST['x_disqus_comments_enable'] ) : '';
    $x_disqus_comments_options['x_disqus_comments_shortname']          = sanitize_text_field( $_POST['x_disqus_comments_shortname'] );
    $x_disqus_comments_options['x_disqus_comments_lazy_load']          = sanitize_text_field( $_POST['x_disqus_comments_lazy_load'] );
    $x_disqus_comments_options['x_disqus_comments_exclude_post_types'] = array_map( 'sanitize_text_field', $x_disqus_comments_exclude_post_types_post );

    update_option( 'x_disqus_comments', $x_disqus_comments_options );

  }
}



// Get Options
// =============================================================================

$x_disqus_comments_options = apply_filters( 'x_disqus_comments_options', get_option( 'x_disqus_comments' ) );

if ( $x_disqus_comments_options != '' ) {

  $x_disqus_comments_enable             = $x_disqus_comments_options['x_disqus_comments_enable'];
  $x_disqus_comments_shortname          = $x_disqus_comments_options['x_disqus_comments_shortname'];
  $x_disqus_comments_lazy_load          = isset( $x_disqus_comments_options['x_disqus_comments_lazy_load'] ) ? $x_disqus_comments_options['x_disqus_comments_lazy_load'] : 'normal';
  $x_disqus_comments_exclude_post_types = $x_disqus_comments_options['x_disqus_comments_exclude_post_types'];

}
