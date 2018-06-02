<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/MIXINS_ELEMENTS/BAR.PHP
// -----------------------------------------------------------------------------
// V2 element mixins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Control
//   02. Control Groups
//   03. Values
// =============================================================================

// Control
// =============================================================================

function x_controls_element_bar( $adv = false ) {

  include( X_TEMPLATE_PATH . '/cornerstone/includes/elements/mixins_setup/_.php' );

  if ( $adv ) {

    $controls = array_merge(
      x_controls_bar_adv( array( 'adv' => $adv ) ),
      x_controls_omega()
    );

  } else {

    $controls = array_merge(
      x_controls_bar_std_design_setup(),
      x_controls_bar_std_design_colors(),
      x_controls_omega( $settings_std_customize )
    );

  }

  return $controls;

}



// Control Groups
// =============================================================================

function x_control_groups_element_bar( $adv = false ) {

  include( X_TEMPLATE_PATH . '/cornerstone/includes/elements/mixins_setup/_.php' );

  if ( $adv ) {

    $control_groups = array_merge(
      x_control_groups_bar(),
      x_control_groups_omega()
    );

  } else {

    $control_groups = x_control_groups_std( array( 'group_title' => __( 'Bar', '__x__' ), 'no_content' => true ) ); 

  }

  return $control_groups;

}



// Values
// =============================================================================

function x_values_element_bar( $settings = array() ) {

  $values = array_merge(
    x_values_bar(),
    x_values_omega()
  );

  return x_bar_mixin_values( $values, $settings );

}
