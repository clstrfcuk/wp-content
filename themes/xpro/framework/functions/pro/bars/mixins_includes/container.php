<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/MIXINS_INCLUDES/CONTAINER.PHP
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

function x_controls_container_adv( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/container.php' );

  $controls = array_merge(

    $control_group_container_adv_setup,
    x_controls_bg_adv( array_merge( $settings_container_bg, array( 'adv' => true ) ) ),

    x_control_flex( array_merge( $settings_container_design, array( 't_pre' => __( 'Self', '__x__' ) ) ) ),
    x_control_flex_layout_css( $settings_container_flex_css_row ),
    x_control_flex_layout_css( $settings_container_flex_css_col ),

    x_control_margin( $settings_container_design ),
    x_control_padding( $settings_container_design ),
    x_control_border( $settings_container_design ),
    x_control_border_radius( $settings_container_design ),
    x_control_box_shadow( $settings_container_design )

  );

  return $controls;

}



// Controls: Standard (Design - Setup)
// =============================================================================

function x_controls_container_std_design_setup( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/container.php' );

  $controls = array_merge(
    $control_group_container_std_design_setup,
    x_control_margin( $settings_container_std_design ),
    x_controls_bg_std_design_setup( $settings_container_std_bg_design )
  );

  return $controls;

}



// Controls: Standard (Design - Colors)
// =============================================================================

function x_controls_container_std_design_colors( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/container.php' );

  $controls = $control_group_container_std_design_colors;

  return $controls;

}



// Control Group
// =============================================================================

function x_control_groups_container( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/container.php' );

  $control_groups = array(
    $group                  => array( 'title' => $group_title ),
    $group_container_setup  => array( 'title' => __( 'Setup', '__x__' ) ),
    $group_container_design => array( 'title' => __( 'Design', '__x__' ) ),
  );

  return $control_groups;

}



// Values
// =============================================================================

function x_values_container( $settings = array() ) {

  include( dirname( __FILE__ ) . '/../mixins_setup/container.php' );

  $values = array_merge(
    array(
      'title'                 => x_module_value( '', 'attr' ),
      'container_max_width'   => x_module_value( 'none', 'style' ),
      'container_max_height'  => x_module_value( 'none', 'style' ),
      'container_bg_color'    => x_module_value( 'transparent', 'style:color' ),
      'container_bg_advanced' => x_module_value( false, 'all' ),
    ),
    x_values_bg(),
    array(
      'container_flex'                  => x_module_value( '0 1 auto', 'style' ),
      'container_row_flex_direction'    => x_module_value( 'row', 'style' ),
      'container_row_flex_wrap'         => x_module_value( false, 'style' ),
      'container_row_flex_justify'      => x_module_value( 'space-between', 'style' ),
      'container_row_flex_align'        => x_module_value( 'center', 'style' ),
      'container_col_flex_direction'    => x_module_value( 'column', 'style' ),
      'container_col_flex_wrap'         => x_module_value( false, 'style' ),
      'container_col_flex_justify'      => x_module_value( 'space-between', 'style' ),
      'container_col_flex_align'        => x_module_value( 'center', 'style' ),
      'container_margin'                => x_module_value( '0px', 'style' ),
      'container_padding'               => x_module_value( '0px', 'style' ),
      'container_border_width'          => x_module_value( '0px', 'style' ),
      'container_border_style'          => x_module_value( 'none', 'style' ),
      'container_border_color'          => x_module_value( 'transparent', 'style:color' ),
      'container_border_radius'         => x_module_value( '0px', 'style' ),
      'container_box_shadow_dimensions' => x_module_value( '0em 0em 0em 0em', 'style' ),
      'container_box_shadow_color'      => x_module_value( 'transparent', 'style:color' ),
    )
  );

  return x_bar_mixin_values( $values, $settings );

}
