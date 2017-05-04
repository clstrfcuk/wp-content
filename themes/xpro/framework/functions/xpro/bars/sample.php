<?php

// =============================================================================
// FUNCTIONS/BARS/SAMPLE.PHP
// -----------------------------------------------------------------------------
// Includes all bar sample data (presets, templates, navigations, et cetera).
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Element Presets
//   02. Header Templates
//   03. Footer Templates
//   04. Navigations
// =============================================================================

// Element Presets
// =============================================================================

function x_bars_element_presets( $presets ) {

  $stacks  = include( X_TEMPLATE_PATH . '/framework/functions/xpro/bars/sample/presets.php' );
  $presets = array_merge( $presets, $stacks );

  return $presets;

}

add_filter( 'cornerstone_element_presets', 'x_bars_element_presets' );



// Header Templates
// =============================================================================

function x_bars_header_templates( $templates ) {

  $template_path = X_TEMPLATE_PATH . '/framework/functions/xpro/bars/sample/templates';

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

  $template_path = X_TEMPLATE_PATH . '/framework/functions/xpro/bars/sample/templates';

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



// Navigations
// =============================================================================

// Notes
// -----
// 01. Sample navigation values and labels.
// 02. Sample navigation setup.

function x_bars_sample_navigations( $choices ) { // 01

  $samples = array(
    array(
      'value' => 'sample:default',
      'label' => __( 'Sample', '__x__' ),
    ),
    array(
      'value' => 'sample:default_no_dropdowns',
      'label' => __( 'Sample (No Dropdowns)', '__x__' ),
    ),
    array(
      'value' => 'sample:default_split_1',
      'label' => __( 'Sample (Split #1)', '__x__' ),
    ),
    array(
      'value' => 'sample:default_split_2',
      'label' => __( 'Sample (Split #2)', '__x__' ),
    ),
  );

  return array_merge( $choices, $samples );

}

add_filter( 'cornerstone_menu_choices', 'x_bars_sample_navigations' );


function x_pre_wp_nav_menu( $menu, $args ) { // 02

  if ( isset( $args->sample_menu ) ) {
    return x_wp_nav_menu_fallback( array_merge( (array) $args, array( 'echo' => false ) ) );
  }

  return $menu;

}

add_filter( 'pre_wp_nav_menu', 'x_pre_wp_nav_menu', 10, 2 );


function x_wp_nav_menu_fallback( $args ) { // 02

  $fallback = new X_Generated_Nav_Menu( $args );

  return $fallback->output();

}


class X_Generated_Nav_Menu { // 02

  protected $args;
  protected $walker;

  public function __construct( $args ) {
    $this->args = $args;
    $this->walker = ( is_a( $args['walker'], 'X_Walker_Nav_Menu' ) ) ? $args['walker'] : new X_Walker_Nav_Menu;
  }

  protected function get_nav_items() {

    $samples = include( X_TEMPLATE_PATH . '/framework/functions/xpro/bars/sample/navigation.php');
    $samples = apply_filters('x_sample_menus', $samples );

    if ( isset( $this->args['sample_menu'] ) && isset( $samples[$this->args['sample_menu']] ) ) {
      $items = $samples[$this->args['sample_menu']];
    } else {
      $items = $this->default_nav_items();
    }

    return $this->normalize_menu_items( $items );
  }

  public function default_nav_items() {
    return array(
      array(
        'title' => __( 'Create a Menu', '__x__' ),
        'url'   => admin_url( 'nav-menus.php' )
      )
    );
  }

  public function output() {

    $items = $this->get_nav_items();

    if ( empty( $items ) ) {
      return false;
    }

    $item_output = '';

    if ( is_array( $items ) ) {
      foreach ( $items as $item ) {
        call_user_func_array( array( $this, 'display_nested_element' ), array( &$item_output, $item, -1 ) );
      }
    }

    $class  = $this->args['menu_class'] ? esc_attr( $this->args['menu_class'] ) : '';
    $output = sprintf( $this->args['items_wrap'], '', $class, $item_output );

    if ( $this->args['echo'] ) {
      echo $output;
    }

    return $output;

  }

  public function display_nested_element( &$output, $element, $depth ) {

    $depth++;

    call_user_func_array( array( $this->walker, 'start_el' ), array( &$output, $element, $depth, $this->args ) );
    $max_depth = ( isset( $this->args['depth'] ) && $this->args['depth'] === $depth + 1 );
    if ( ! $max_depth && isset( $element->children ) && ! empty( $element->children ) ) {
      call_user_func_array( array( $this->walker, 'start_lvl' ), array( &$output, $depth, $this->args ) );
      foreach ( $element->children as $child ) {
        call_user_func_array( array( $this, 'display_nested_element' ), array( &$output, $child, $depth, $this->args ) );
      }
      call_user_func_array( array( $this->walker, 'end_lvl' ), array( &$output, $depth, $this->args ));
    }
    call_user_func_array( array( $this->walker, 'end_el' ), array( &$output, $element, $depth, $this->args ));

    return $output;

  }

  public function normalize_menu_items( $items ) {

    if ( empty( $items ) ) {
      return array();
    }

    static $id_counter = 0;

    $defaults = array(
      'ID'          => 'sample',
      'title'       => '',
      'description' => '',
      'attr_title'  => '',
      'target'      => '',
      'xfn'         => '',
      'url'         => '',
      'type'        => 'sample',
      'object_id'   => 'sample',
      'classes'     => array(),
      'meta'        => array()
    );

    $default_classes  = array( 'menu-item', 'menu-item-type-custom', 'menu-item-object-custom' );
    $normalized_items = array();

    foreach ( $items as $item ) {

      $normalized            = wp_parse_args($item, $defaults);
      $normalized['ID']     .= '-' . $id_counter++ ;
      $normalized['classes'] = array_merge( $normalized['classes'], $default_classes );

      if ( isset( $normalized['children'] ) ) {
        $normalized['children']  = $this->normalize_menu_items( $normalized['children'] );
        $normalized['classes'][] = 'menu-item-has-children';
      }

      $normalized_items[] = (object) $normalized;

    }

    return $normalized_items;

  }

}
