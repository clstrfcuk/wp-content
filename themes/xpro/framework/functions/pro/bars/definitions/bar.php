<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/DEFINITIONS/BAR.PHP
// -----------------------------------------------------------------------------
// V2 element definitions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Element
//   02. Builder Setup
//   03. Register Element
// =============================================================================

// Define Element
// =============================================================================

$data = array(
  'title'  => __( 'Bar', '__x__' ),
  'values' => x_values_element_bar(),
);



// Builder Setup
// =============================================================================

function x_element_builder_setup_bar() {
  return array(
    'controls'           => x_controls_element_bar(),
    'controls_adv'       => x_controls_element_bar( true ),
    'control_groups'     => x_control_groups_element_bar(),
    'control_groups_adv' => x_control_groups_element_bar( true ),
  );
}



// Register Element
// =============================================================================

cornerstone_register_element( 'bar', x_element_base( $data ) );
