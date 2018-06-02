<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/MIXINS_INCLUDES/BAR.PHP
// -----------------------------------------------------------------------------
// V2 element mixins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Controls: Advanced
//   02. Controls: Standard (Design - Setup)
//   03. Controls: Standard (Design - Colors)
//   04. Control Group
//   05. Values
// =============================================================================

// Controls: Advanced
// =============================================================================

function x_controls_bar_adv( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/bar.php' );

  $controls = array_merge(

    $control_group_bar_adv_setup,
    $control_group_bar_adv_setup_sticky,
    x_controls_bg_adv( array_merge( $settings_bar_bg, array( 'adv' => true ) ) ),
    $control_group_bar_adv_dimensions,

    x_control_flex_layout_css( $settings_bar_flex_css_row ),
    x_control_flex_layout_css( $settings_bar_flex_css_col ),

    x_control_padding( array_merge( $settings_bar_design, array( 'condition' => $condition_bar_height_is_auto ) ) ),
    x_control_border( $settings_bar_design ),
    x_control_box_shadow( $settings_bar_design )

  );

  return $controls;

}



// Controls: Standard (Design - Setup)
// =============================================================================

function x_controls_bar_std_design_setup( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/bar.php' );

  $controls = array_merge(
    $control_group_bar_std_design_setup,
    x_controls_bg_std_design_setup( $settings_bar_bg )
  );

  return $controls;

}



// Controls: Standard (Design - Colors)
// =============================================================================

function x_controls_bar_std_design_colors( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/bar.php' );

  $controls = $control_group_bar_std_design_colors;

  return $controls;

}



// Control Group
// =============================================================================

function x_control_groups_bar( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/bar.php' );

  $control_groups = array(
    $group            => array( 'title' => $group_title ),
    $group_bar_setup  => array( 'title' => __( 'Setup', '__x__' ) ),
    $group_bar_design => array( 'title' => __( 'Design', '__x__' ) ),
  );

  return $control_groups;

}



// Values
// =============================================================================

function x_values_bar( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/bar.php' );

  $values = array_merge(
    array(
      'title'                       => x_module_value( '', 'attr' ),
      'bar_base_font_size'          => x_module_value( '16px', 'style' ),
      'bar_z_index'                 => x_module_value( '9999', 'style' ),
      'bar_position_top'            => x_module_value( 'relative', 'markup' ),
      'bar_margin_top'              => x_module_value( '0px', 'style' ),
      'bar_margin_sides'            => x_module_value( '0px', 'style' ),
      'bar_scroll'                  => x_module_value( false, 'markup' ),
      'bar_bg_color'                => x_module_value( '#ffffff', 'style:color' ),
      'bar_bg_advanced'             => x_module_value( false, 'all' ),

      'bar_sticky'                  => x_module_value( false, 'markup' ),
      'bar_sticky_keep_margin'      => x_module_value( false, 'markup' ),
      'bar_sticky_hide_initially'   => x_module_value( false, 'markup' ),
      'bar_sticky_z_stack'          => x_module_value( false, 'markup' ),
      'bar_sticky_trigger_selector' => x_module_value( '', 'markup' ),
      'bar_sticky_trigger_offset'   => x_module_value( '0', 'markup' ),
      'bar_sticky_shrink'           => x_module_value( '1', 'markup' ),
    ),
    x_values_bg(),
    array(
      'bar_width'                 => x_module_value( '15em', 'style' ),
      'bar_height'                => x_module_value( '6em', 'style' ),
      'bar_outer_spacing'         => x_module_value( '2em', 'style' ),
      'bar_content_length'        => x_module_value( '100%', 'style' ),
      'bar_content_max_length'    => x_module_value( 'none', 'style' ),

      'bar_row_flex_direction'    => x_module_value( 'row', 'style' ),
      'bar_row_flex_wrap'         => x_module_value( false, 'style' ),
      'bar_row_flex_justify'      => x_module_value( 'space-between', 'style' ),
      'bar_row_flex_align'        => x_module_value( 'center', 'style' ),

      'bar_col_flex_direction'    => x_module_value( 'column', 'style' ),
      'bar_col_flex_wrap'         => x_module_value( false, 'style' ),
      'bar_col_flex_justify'      => x_module_value( 'space-between', 'style' ),
      'bar_col_flex_align'        => x_module_value( 'center', 'style' ),

      'bar_padding'               => x_module_value( '0em', 'style' ),
      'bar_border_width'          => x_module_value( '0px', 'style' ),
      'bar_border_style'          => x_module_value( 'none', 'style' ),
      'bar_border_color'          => x_module_value( 'transparent', 'style:color' ),
      'bar_box_shadow_dimensions' => x_module_value( '0em 0.15em 2em', 'style' ),
      'bar_box_shadow_color'      => x_module_value( 'rgba(0, 0, 0, 0.15)', 'style:color' ),
    )

  );

  return x_bar_mixin_values( $values, $settings );

}
