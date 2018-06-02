<?php

// =============================================================================
// FUNCTIONS/FRONT-END/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Script output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
//   02. Output Inline Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

function x_enqueue_site_scripts() {

  wp_register_script( 'x-site', X_TEMPLATE_URL . '/framework/dist/js/site/x.js', array( 'jquery' ), X_ASSET_REV, true );
  wp_enqueue_script( 'x-site' );

  if ( is_singular() ) {
    wp_enqueue_script( 'comment-reply' );
  }

}

add_action( 'wp_enqueue_scripts', 'x_enqueue_site_scripts' );



// Output Inline Scripts
// =============================================================================

function x_output_js() {

  // Custom Scripts
  // --------------

  $x_custom_scripts = x_get_option( 'x_custom_scripts' );

  if ( $x_custom_scripts ) {
    echo "<script id=\"x-custom-js\">$x_custom_scripts</script>";
  }

  $x_design_bg_image_full = x_get_option( 'x_design_bg_image_full' );
  $bg_images_output       = '';
  $params                 = array();

  if ( $x_design_bg_image_full ) {

    $bg_images_output = '"' . x_make_protocol_relative( $x_design_bg_image_full ) . '"';
    $params = array( 'fade' => x_get_option( 'x_design_bg_image_full_fade' ) );

  }

  if ( is_singular() ) {

    $entry_id              = get_the_ID();
    $x_entry_bg_image_full = get_post_meta( $entry_id, '_x_entry_bg_image_full', true );

    if ( $x_entry_bg_image_full ) {

      $page_bg_images = explode( ',', $x_entry_bg_image_full );

      foreach ( $page_bg_images as $page_bg_image ) {
        $bg_images_output .= '"' . $page_bg_image . '", ';
      }

      $bg_images_output = trim( $bg_images_output, ', ' );

      $params = array(
        'fade'     => get_post_meta( $entry_id, '_x_entry_bg_image_full_fade', true ),
        'duration' => get_post_meta( $entry_id, '_x_entry_bg_image_full_duration', true )
      );
    }

  }

  if ( $bg_images_output ) {

    $param_output = '';

    foreach ($params as $key => $value) {
      $param_output .= "$key: $value, ";
    }

    $param_output = ',{' . trim( $param_output, ', ' ) . '}';

    ?><script>jQuery(function($){$.backstretch([<?php echo $bg_images_output; ?>] <?php echo $param_output; ?> ); });</script><?php

  }

}

add_action( 'wp_footer', 'x_output_js', 9999, 0 );
