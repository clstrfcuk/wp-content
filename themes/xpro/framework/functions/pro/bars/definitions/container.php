<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/DEFINITIONS/CONTAINER.PHP
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
  'title'  => __( 'Container', '__x__' ),
  'values' => x_values_element_container(),
);



// Builder Setup
// =============================================================================

function x_element_builder_setup_container() {
  return array(
    'controls'           => x_controls_element_container(),
    'controls_adv'       => x_controls_element_container( true ),
    'control_groups'     => x_control_groups_element_container(),
    'control_groups_adv' => x_control_groups_element_container( true ),
  );
}



// Register Module
// =============================================================================

cornerstone_register_element( 'container', x_element_base( $data ) );
