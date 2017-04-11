<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Under Construction
//   02. Output
// =============================================================================

// Under Construction
// =============================================================================

function x_under_construction_output( $original_template ) {

  require( X_UNDER_CONSTRUCTION_PATH . '/functions/options.php' );

  if ( isset( $x_under_construction_enable ) && $x_under_construction_enable == 1 && ! is_user_logged_in() ) {

    if ( isset( $x_under_construction_whitelist ) && !empty( $x_under_construction_whitelist ) ) {

      $allowed_ips = explode(' ', $x_under_construction_whitelist);

      if ( in_array( $_SERVER['REMOTE_ADDR'], $allowed_ips) ) {

        return $original_template;

      }

    }

    status_header( 503 );

    if ( isset( $x_under_construction_use_custom ) && $x_under_construction_use_custom == 1 ) {
      return x_under_construction_custom_output( $original_template );
    } else {
      return X_UNDER_CONSTRUCTION_PATH . '/views/site/under-construction.php';
    }

  } else {

    return $original_template;

  }

}


// Under Construction Custom Page
// =============================================================================

function x_under_construction_custom_output( $original_template ) {

  require( X_UNDER_CONSTRUCTION_PATH . '/functions/options.php' );

  $custom_post = get_post( (int) $x_under_construction_custom );

  if ( ! is_a( $custom_post, 'WP_Post' ) ) {
    return $original_template;
  }

  GLOBAL $wp_query;
  GLOBAL $post;

  $post = $custom_post;

  $wp_query->posts             = array( $post );
  $wp_query->queried_object_id = $post->ID;
  $wp_query->queried_object    = $post;
  $wp_query->post_count        = 1;
  $wp_query->found_posts       = 1;
  $wp_query->max_num_pages     = 0;
  $wp_query->is_404            = false;
  $wp_query->is_page           = true;
  $wp_query->is_singular	     = true;

  return get_page_template();
}



// Output
// =============================================================================

add_filter( 'template_include', 'x_under_construction_output' );
