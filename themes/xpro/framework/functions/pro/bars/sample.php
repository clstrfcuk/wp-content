<?php

// =============================================================================
// FUNCTIONS/BARS/SAMPLE.PHP
// -----------------------------------------------------------------------------
// Includes all bar sample data (presets, templates, navigations, et cetera).
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Header Templates
//   02. Footer Templates
//   04. Navigations
//   05. Breadcrumbs
// =============================================================================

// Header Templates
// =============================================================================

function x_bars_header_templates( $templates ) {

  $template_path = X_TEMPLATE_PATH . '/framework/functions/pro/bars/sample/templates';

  $header_default = json_decode( file_get_contents( "$template_path/header-default.json" ), true );
  $templates[] = array(
    'id'      => 'header-default',
    'title'   => 'Default',
    'regions' => $header_default['regions']
  );

  $header_hero = json_decode( file_get_contents( "$template_path/header-hero.json" ), true );
  $templates[] = array(
    'id'      => 'header-hero',
    'title'   => 'Hero',
    'regions' => $header_hero['regions']
  );

  $header_standard_left = json_decode( file_get_contents( "$template_path/header-standard-left.json" ), true );
  $templates[] = array(
    'id'      => 'header-standard-left',
    'title'   => 'Standard Left',
    'regions' => $header_standard_left['regions']
  );

  $header_standard_middle = json_decode( file_get_contents( "$template_path/header-standard-middle.json" ), true );
  $templates[] = array(
    'id'      => 'header-standard-middle',
    'title'   => 'Standard Middle',
    'regions' => $header_standard_middle['regions']
  );

  $header_standard_right = json_decode( file_get_contents( "$template_path/header-standard-right.json" ), true );
  $templates[] = array(
    'id'      => 'header-standard-right',
    'title'   => 'Standard Right',
    'regions' => $header_standard_right['regions']
  );

  $header_fixed_left = json_decode( file_get_contents( "$template_path/header-fixed-left.json" ), true );
  $templates[] = array(
    'id'      => 'header-fixed-left',
    'title'   => 'Fixed Left',
    'regions' => $header_fixed_left['regions']
  );

  $header_fixed_right = json_decode( file_get_contents( "$template_path/header-fixed-right.json" ), true );
  $templates[] = array(
    'id'      => 'header-fixed-right',
    'title'   => 'Fixed Right',
    'regions' => $header_fixed_right['regions']
  );

  $header_sticky_bars = json_decode( file_get_contents( "$template_path/header-sticky-bars.json" ), true );
  $templates[] = array(
    'id'      => 'header-sticky-bars',
    'title'   => 'Sticky Bars',
    'regions' => $header_sticky_bars['regions']
  );

  return $templates;

}

add_filter( 'cornerstone_header_templates', 'x_bars_header_templates' );



// Footer Templates
// =============================================================================

function x_bars_footer_templates( $templates ) {

  $template_path = X_TEMPLATE_PATH . '/framework/functions/pro/bars/sample/templates';

  $footer_simple = json_decode( file_get_contents( "$template_path/footer-default.json" ), true );
  $templates[] = array(
    'id'      => 'footer-default',
    'title'   => 'Default',
    'regions' => $footer_simple['regions']
  );

  $footer_branded = json_decode( file_get_contents( "$template_path/footer-branded.json" ), true );
  $templates[] = array(
    'id'      => 'footer-branded',
    'title'   => 'Branded',
    'regions' => $footer_branded['regions']
  );

  $footer_columns = json_decode( file_get_contents( "$template_path/footer-columns.json" ), true );
  $templates[] = array(
    'id'      => 'footer-columns',
    'title'   => 'Columns',
    'regions' => $footer_columns['regions']
  );

  return $templates;

}

add_filter( 'cornerstone_footer_templates', 'x_bars_footer_templates' );
