<?php

// =============================================================================
// FUNCTIONS/BARS/SETUP.PHP
// -----------------------------------------------------------------------------
// Bar functionality.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Path
//   02. Include Files
//   03. Setup - Views
//   04. Setup - Modules
//   05. Setup - Styles
//   06. Setup - Preview
//   07. Setup - Bar Spaces
//   08. Setup - Elements
// =============================================================================

// Set Path
// =============================================================================

$bars_path = X_TEMPLATE_PATH . '/framework/functions/xpro/bars';



// Include Files
// =============================================================================

require_once( $bars_path . '/helpers.php' );
require_once( $bars_path . '/decorators.php' );
require_once( $bars_path . '/mixins.php' );
require_once( $bars_path . '/sample.php' );



// Setup - Views
// =============================================================================

function x_bars_setup_views() {
  if ( ! x_is_blank( 3 ) && ! x_is_blank( 6 ) && ! x_is_blank( 7 ) && ! x_is_blank( 8 ) ) {
    x_set_view( 'x_after_site_begin', 'header', 'masthead', '' );
  }
  if ( ! x_is_blank( 2 ) && ! x_is_blank( 3 ) && ! x_is_blank( 5 ) && ! x_is_blank( 6 ) ) {
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

  $header_data = cornerstone_get_header_data();
  $footer_data = cornerstone_get_footer_data();

  if ( ! is_null( $header_data ) || ! is_null( $footer_data ) ) {
    add_action( 'x_bar', 'x_render_bar_modules', 10, 2 );
    add_action( 'x_bar_container', 'x_render_bar_modules', 10, 2 );
    add_action( 'x_head_css', 'x_bars_output_generated_styles' );
  }

  if ( ! is_null( $header_data ) ) {
    add_filter( 'x_legacy_cranium_headers', '__return_false' );
    $header_data['global'] = array();
    cornerstone_setup_header_styles( $header_data );
    x_bars_setup_header_spaces( $header_data );
    x_action_defer( 'x_masthead', 'x_render_bar_modules', array( $header_data['modules'], $header_data['global'] ), 10, true );
    if ( isset( $header_data['settings']['customJS'] ) && $header_data['settings']['customJS'] ) {
      cornerstone_enqueue_custom_script( 'x-header-custom-scripts', $header_data['settings']['customJS'] );
    }
  }

  if ( ! is_null( $footer_data ) ) {
    add_filter( 'x_legacy_cranium_footers', '__return_false' );
    $footer_data['global'] = array();
    cornerstone_setup_footer_styles( $footer_data );
    x_action_defer( 'x_colophon', 'x_render_bar_modules', array( $footer_data['modules'], $footer_data['global'] ), 10, true );
    if ( isset( $footer_data['settings']['customJS'] ) && $footer_data['settings']['customJS'] ) {
      cornerstone_enqueue_custom_script( 'x-footer-custom-scripts', $footer_data['settings']['customJS'] );
    }
  }

}

add_action( 'template_redirect', 'x_bars_setup_modules' );



// Setup - Styles
// =============================================================================

function x_bars_output_generated_styles() {
  echo x_get_clean_css( cornerstone_get_generated_styles() );
}



// Setup - Preview
// =============================================================================

function x_bars_setup_preview() {
  add_filter( 'x_legacy_cranium_headers', '__return_false' );
  add_filter( 'x_legacy_cranium_footers', '__return_false' );
  remove_action( 'template_redirect', 'x_bars_setup_modules' );
  add_action( 'x_bar', 'cornerstone_preview_container_output' );
  add_action( 'x_bar_container', 'cornerstone_preview_container_output' );
  cornerstone_preview_register_zones( array( 'x_after_masthead_begin', 'x_before_site_begin', 'x_before_site_end', 'x_masthead', 'x_colophon' ) );
}

add_action( 'cs_bar_preview_setup', 'x_bars_setup_preview' );



// Setup - Bar Spaces
// =============================================================================

function x_bars_setup_header_spaces( $header_data ) {

  // Hook in left and right bar spaces which are output earlier than their bars.
  $bar_space_actions = array(
    'left'  => 'x_before_site_begin',
    'right' => 'x_before_site_begin',
  );

  $index = 0;

  foreach ( $header_data['modules'] as $bar ) {
    if ( isset( $bar_space_actions[ $bar['_region']] ) ) {
      unset( $bar['_modules'] );
      x_set_view( $bar_space_actions[ $bar['_region'] ], 'bars', 'bar', 'space', x_module_decorate( $bar ) );
    }

  }

}



// Setup - Elements
// =============================================================================

function x_bars_element_setup() {
  include( 'modules.php' );
  cornerstone_setup_style_class_prefix( 'header', 'hm' );
  cornerstone_setup_style_class_prefix( 'footer', 'fm' );
}

if ( function_exists('CS') && version_compare( CS()->version(), '1.9', '>=' ) ) {
  add_action( 'init', 'x_bars_element_setup', 5 );
}

function x_bar_elements_style_template_loader( $type ) {
  return x_get_view( 'styles/bars', $type, 'css', array(), false );
}

function x_bar_elements_setup_builder( $type ) {
  $function = 'x_element_builder_setup_' . str_replace( '-', '_', $type );
  return is_callable( $function ) ? call_user_func( $function ) : array();
}

function x_bar_element_base( $data ) {
  return array_merge( array(
    'builder' => 'x_bar_elements_setup_builder',
    'style'   => 'x_bar_elements_style_template_loader',
    'render'  => 'x_render_bar_module',
    'icon'    => 'native'
  ), $data );
}
