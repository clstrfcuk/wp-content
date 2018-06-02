<?php

// =============================================================================
// FUNCTIONS/BARS/SETUP.PHP
// -----------------------------------------------------------------------------
// Bar functionality.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Setup - Views
//   02. Setup - Modules
//   03. Setup - Styles
//   04. Setup - Preview
//   05. Setup - Bar Spaces
//   06. Setup - Elements
//   07. Setup - Page Templates
// =============================================================================

// Setup - Views
// =============================================================================

function x_bars_register_post_types() {

  register_post_type( 'cs_header', array(
    'public'          => false,
    'capability_type' => 'page',
    'supports'        => false,
    'labels'          => array(
      'name'          => __( 'Pro Headers', '__x__' ),
      'singular_name' => __( 'Pro Header', '__x__' ),
    )
  ) );

  register_post_type( 'cs_footer', array(
    'public'          => false,
    'capability_type' => 'page',
    'supports'        => false,
    'labels'          => array(
      'name'          => __( 'Pro Footers', '__x__' ),
      'singular_name' => __( 'Pro Footer', '__x__' ),
    )
  ) );

}

add_action( 'init', 'x_bars_register_post_types' );



// Setup - Views
// =============================================================================

function x_bars_setup_views() {

  if ( apply_filters( 'x_output_header', true ) ) {
    x_set_view( 'x_after_site_begin', 'header', 'masthead', '' );
  }

  if ( apply_filters( 'x_output_footer', true ) ) {
    x_set_view( 'x_before_site_end', 'footer', 'colophon', '' );
  }

}

add_action( 'template_redirect', 'x_bars_setup_views' );



// Setup - Modules
// =============================================================================

function x_bars_setup_modules() {

  if ( ! function_exists( 'cornerstone_get_header_data' ) ) {
    return;
  }

  $header_data = apply_filters( 'x_output_header', true ) ? cornerstone_get_header_data() : null;
  $footer_data = apply_filters( 'x_output_footer', true ) ? cornerstone_get_footer_data() : null;

  if ( ! is_null( $header_data ) || ! is_null( $footer_data ) ) {
    add_action( 'x_bar', 'x_render_bar_modules', 10, 2 );
    add_action( 'x_bar_container', 'x_render_bar_modules', 10, 2 );
  }

  if ( ! is_null( $header_data ) ) {
    add_filter( 'x_legacy_cranium_headers', '__return_false' );
    $header_data['global'] = array();

    cornerstone_register_element_styles( $header_data['id'], $header_data['modules'] );

    x_bars_setup_header_spaces( $header_data );
    x_action_defer( 'x_masthead', 'x_render_bar_modules', array( $header_data['modules'], $header_data['global'] ), 10, true );
    if ( isset( $header_data['settings']['customJS'] ) && $header_data['settings']['customJS'] ) {
      cornerstone_enqueue_custom_script( 'x-header-custom-scripts', $header_data['settings']['customJS'] );
    }
  }

  if ( ! is_null( $footer_data ) ) {
    add_filter( 'x_legacy_cranium_footers', '__return_false' );
    $footer_data['global'] = array();
    cornerstone_register_element_styles( $footer_data['id'], $footer_data['modules'] );

    x_action_defer( 'x_colophon', 'x_render_bar_modules', array( $footer_data['modules'], $footer_data['global'] ), 10, true );

    if ( isset( $footer_data['settings']['customJS'] ) && $footer_data['settings']['customJS'] ) {
      cornerstone_enqueue_custom_script( 'x-footer-custom-scripts', $footer_data['settings']['customJS'] );
    }

  }

  if ( ! is_null( $header_data ) ) {
    if ( isset( $header_data['settings']['customCSS'] ) && $header_data['settings']['customCSS'] ) {
      cornerstone_register_styles( $header_data['id'] . '-custom', $header_data['settings']['customCSS'] );
    }
  }

  if ( ! is_null( $footer_data ) ) {
    if ( isset( $footer_data['settings']['customCSS'] ) && $footer_data['settings']['customCSS'] ) {
      cornerstone_register_styles( $footer_data['id'] . '-custom', $footer_data['settings']['customCSS'] );
    }
  }

}

add_action( 'x_late_template_redirect', 'x_bars_setup_modules' );



// Setup - Preview
// =============================================================================

function x_bars_setup_preview() {
  add_filter( 'x_legacy_cranium_headers', '__return_false' );
  add_filter( 'x_legacy_cranium_footers', '__return_false' );
  remove_action( 'x_late_template_redirect', 'x_bars_setup_modules' );
  add_action( 'x_bar', 'cornerstone_preview_container_output' );
  add_action( 'x_bar_container', 'cornerstone_preview_container_output' );
}

add_action( 'cs_bar_preview_setup', 'x_bars_setup_preview' );
add_action( 'cs_element_rendering_regions', 'x_bars_setup_preview' );



// Setup - Bar Spaces
// =============================================================================
// 01. Hook in left and right bar spaces which are output earlier than
//     their bars.

function x_bars_setup_header_spaces( $header_data ) {

  $bar_space_actions = array( // 01
    'left'  => 'x_before_site_begin',
    'right' => 'x_before_site_begin',
  );

  $index = 0;

  foreach ( $header_data['modules'] as $bar ) {
    if ( isset( $bar_space_actions[ $bar['_region']] ) ) {
      unset( $bar['_modules'] );
      x_set_view( $bar_space_actions[ $bar['_region'] ], 'elements', 'bar', 'space', x_module_decorate( $bar ) );
    }
  }

}



// Setup - Elements
// =============================================================================

function x_bars_element_setup() {

  $path = X_TEMPLATE_PATH . '/framework/functions/pro/bars';

  require_once( $path . '/decorators.php' );
  require_once( $path . '/mixins-includes.php' );
  require_once( $path . '/mixins-elements.php' );
  require_once( $path . '/definitions.php' );

}

add_action( 'cs_register_elements', 'x_bars_element_setup', 5 );



// Setup - Elements
// =============================================================================

function x_output_header_page_templates( $output_header ) {
  $output_header = ! x_is_blank( 3 ) && ! x_is_blank( 6 ) && ! x_is_blank( 7 ) && ! x_is_blank( 8 );
  return $output_header;
}

add_filter( 'x_output_header', 'x_output_header_page_templates' );


function x_output_footer_page_templates( $output_footer ) {
  $output_footer = ! x_is_blank( 2 ) && ! x_is_blank( 3 ) && ! x_is_blank( 5 ) && ! x_is_blank( 6 );
  return $output_footer;
}

add_filter( 'x_output_footer', 'x_output_footer_page_templates' );
