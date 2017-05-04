<?php

// =============================================================================
// FUNCTIONS/BARS/DECORATORS.PHP
// -----------------------------------------------------------------------------
// Bar module decorators.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Mixin: Base
//   02. Mixin: Anchor
//   03. Mixin: Flex Layout Attr
//   04. Mixin: Image
//   05. Mixin: Toggle
//   06. Mixin: Dropdown
//   07. Decorator: Bar
//   08. Decorator: Container
//   09. Decorator: Content Area
//   10. Decorator: Content Area (Dropdown)
//   11. Decorator: Content Area (Modal)
//   12. Decorator: Content Area (Off Canvas)
//   13. Decorator: Image
//   14. Decorator: Text
//   15. Decorator: Headline
//   16. Decorator: Nav (Inline)
//   17. Decorator: Nav (Dropdown)
//   18. Decorator: Nav (Collapsed)
//   19. Decorator: Nav (Modal)
//   20. Decorator: Button
//   21. Decorator: Social
//   22. Decorator: Search (Inline)
//   23. Decorator: Search (Modal)
//   24. Decorator: Login (Modal)
//   25. Decorator: Third Party (bbPress: Dropdown)
//   26. Decorator: Third Party (BuddyPress: Dropdown)
//   27. Decorator: Third Party (WooCommerce Cart: Dropdown)
//   28. Decorator: Third Party (WooCommerce Cart: Modal)
//   29. Decorator: Third Party (WooCommerce Cart: Off Canvas)
// =============================================================================

// Mixin: Base
// =============================================================================

function x_module_decorator_base( $module ) {

  if ( ! isset( $module['_region'] ) ) {
    $module['_region'] = 'top';
  }

  $class_prefix = ( $module['_region'] === 'footer' ) ? 'fm' : 'hm';

  $module['mod_id'] = $class_prefix . $module['_id'];

  if ( ! empty( $module['hide_bp'] ) ) {
    $hide_bps = explode( ' ', $module['hide_bp'] );
    foreach ( $hide_bps as $bp ) {
      if ( $bp == 'none' ) {
        continue;
      }
      $module['class'] .= ' x-hide-' . $bp;
    }
  }

  return $module;

}



// Mixin: Anchor
// =============================================================================

function x_module_decorator_mixin_anchor( $anchor_href ) {

  $decorations = array(
    'anchor_href' => $anchor_href,
  );

  return $decorations;

}



// Mixin: Flex Layout Attr
// =============================================================================

function x_module_decorator_flex_layout_attr( $_region, $flex_attr_prefix = '', $row_flex_attr, $col_flex_attr ) {

  $flex_layout_attr = ( $_region == 'left' || $_region == 'right' ) ? $col_flex_attr          : $row_flex_attr;
  $k_pre            = ( ! empty( $flex_attr_prefix )              ) ? $flex_attr_prefix . '_' : '';

  $decorations = array(
    $k_pre . 'flex_layout_attr' => $flex_layout_attr
  );

  return $decorations;

}



// Mixin: Toggle
// =============================================================================

function x_module_decorator_mixin_toggle( $toggle_target ) {

  $decorations = array(
    'toggle_target' => $toggle_target,
  );

  return $decorations;

}



// Mixin: Dropdown
// =============================================================================

function x_module_decorator_mixin_dropdown( $dropdown_is_list = true ) {

  $decorations = array(
    'dropdown_is_list' => $dropdown_is_list,
  );

  return $decorations;

}



// Decorator: Bar
// =============================================================================

// Notes
// -----
// 01. Previous flex layout attribute mixin:
//     x_module_decorator_flex_layout_attr( $_region, 'bar', $bar_row_flex_layout_attr, $bar_col_flex_layout_attr )

function x_module_decorator_bar( $module ) {

  extract( $module );

  $bar_position = 'fixed';

  if ( $_region === 'top' ) {
    if ( $bar_sticky === true && $bar_sticky_hide_initially === true ) {
      $bar_position = 'absolute';
    } else {
      $bar_position = $bar_position_top;
    }
  } else if ( $_region === 'footer' ) {
    $bar_position = 'relative';
  }

  $module = array_merge(
    $module,
    array(
      'bar_position' => $bar_position
    )
    // 01
  );

  return $module;

}



// Decorator: Container
// =============================================================================

// Notes
// -----
// 01. Previous flex layout attribute mixin:
//     x_module_decorator_flex_layout_attr( $_region, 'bar', $bar_row_flex_layout_attr, $bar_col_flex_layout_attr )

function x_module_decorator_container( $module ) {

  extract( $module );

  $module = array_merge(
    $module
    // 01
  );

  return $module;

}



// Decorator: Content Area
// =============================================================================

function x_module_decorator_content_area( $module ) {

  extract( $module );

  $module = array_merge(
    $module
  );

  return $module;

}



// Decorator: Content Area (Dropdown)
// =============================================================================

function x_module_decorator_content_area_dropdown( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-dropdown" )
  );

  return $module;

}



// Decorator: Content Area (Modal)
// =============================================================================

function x_module_decorator_content_area_modal( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-modal" )
  );

  return $module;

}



// Decorator: Content Area (Off Canvas)
// =============================================================================

function x_module_decorator_content_area_off_canvas( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-off-canvas" ),
    x_module_decorator_mixin_dropdown( false )
  );

  return $module;

}



// Decorator: Image
// =============================================================================

function x_module_decorator_image( $module ) {

  extract( $module );

  $module = array_merge(
    $module
  );

  return $module;

}



// Decorator: Text
// =============================================================================

function x_module_decorator_text( $module ) {

  extract( $module );

  $module = array_merge(
    $module
  );

  return $module;

}



// Decorator: Headline
// =============================================================================

function x_module_decorator_headline( $module ) {

  extract( $module );

  $module = array_merge(
    $module
  );

  return $module;

}



// Decorator: Nav (Inline)
// =============================================================================

// Notes
// -----
// 01. Previous flex layout attribute mixin:
//     x_module_decorator_flex_layout_attr( $_region, 'bar', $bar_row_flex_layout_attr, $bar_col_flex_layout_attr )

function x_module_decorator_nav_inline( $module ) {

  extract( $module );

  $module = array_merge(
    $module
    // 01
  );

  return $module;

}



// Decorator: Nav (Dropdown)
// =============================================================================

function x_module_decorator_nav_dropdown( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-dropdown" ),
    x_module_decorator_mixin_dropdown( true )
  );

  return $module;

}



// Decorator: Nav (Collapsed)
// =============================================================================

function x_module_decorator_nav_collapsed( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-bar-nav-collapsed" )
  );

  return $module;

}



// Decorator: Nav (Modal)
// =============================================================================

function x_module_decorator_nav_modal( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-modal" )
  );

  return $module;

}



// Decorator: Button
// =============================================================================

function x_module_decorator_button( $module ) {

  extract( $module );

  $module = array_merge(
    $module
  );

  return $module;

}



// Decorator: Social
// =============================================================================

function x_module_decorator_social( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-bar-social .x-bar-social-networks" )
  );

  return $module;

}



// Decorator: Search (Inline)
// =============================================================================

function x_module_decorator_search_inline( $module ) {

  extract( $module );

  $module = array_merge(
    $module
  );

  return $module;

}



// Decorator: Search (Modal)
// =============================================================================

function x_module_decorator_search_modal( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-modal" )
  );

  return $module;

}



// Decorator: Login (Modal)
// =============================================================================

function x_module_decorator_login_modal( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-modal" )
  );

  return $module;

}



// Decorator: Third Party (bbPress: Dropdown)
// =============================================================================

function x_module_decorator_tp_bbp_dropdown( $module ) {

  extract( $module );

  $anchor_href = get_post_type_archive_link( bbp_get_forum_post_type() );
  $anchor_href = '';

  $module = array_merge(
    $module,
    x_module_decorator_mixin_anchor( $anchor_href ),
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-dropdown" ),
    x_module_decorator_mixin_dropdown( true )
  );

  return $module;

}



// Decorator: Third Party (BuddyPress: Dropdown)
// =============================================================================

function x_module_decorator_tp_bp_dropdown( $module ) {

  extract( $module );

  if ( bp_is_active( 'activity' ) ) {
    $logged_out_link = bp_get_activity_directory_permalink();
  } else if ( bp_is_active( 'groups' ) ) {
    $logged_out_link = bp_get_groups_directory_permalink();
  } else {
    $logged_out_link = bp_get_members_directory_permalink();
  }

  $anchor_href = ( is_user_logged_in() ) ? bp_loggedin_user_domain() : $logged_out_link;

  $module = array_merge(
    $module,
    x_module_decorator_mixin_anchor( $anchor_href ),
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-dropdown" ),
    x_module_decorator_mixin_dropdown( true )
  );

  return $module;

}



// Decorator: Third Party (WooCommerce Cart: Dropdown)
// =============================================================================

function x_module_decorator_tp_wc_cart_dropdown( $module ) {

  extract( $module );

  if ( function_exists( 'wc_get_cart_url' ) ) {
    $href = wc_get_cart_url();
  } else {
    $href = '';
  }

  $module = array_merge(
    $module,
    x_module_decorator_mixin_anchor( $href ),
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-dropdown" ),
    x_module_decorator_mixin_dropdown( false )
  );

  return $module;

}



// Decorator: Third Party (WooCommerce Cart: Modal)
// =============================================================================

function x_module_decorator_tp_wc_cart_modal( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-modal" )
  );

  return $module;

}



// Decorator: Third Party (WooCommerce Cart: Off Canvas)
// =============================================================================

function x_module_decorator_tp_wc_cart_off_canvas( $module ) {

  extract( $module );

  $module = array_merge(
    $module,
    x_module_decorator_mixin_toggle( ".hm{$_id}.x-off-canvas" )
  );

  return $module;

}
