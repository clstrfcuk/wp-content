<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/MIXINS_SETUP/CONTAINER.PHP
// -----------------------------------------------------------------------------
// V2 element mixins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Shared
//   02. Setup
//   03. Groups
//   04. Conditions
//   05. Options
//   06. Settings
//   07. Individual Controls
//   08. Control Lists
//   09. Control Groups (Advanced)
//   10. Control Groups (Standard)
// =============================================================================

// Shared
// =============================================================================

include( X_TEMPLATE_PATH . '/cornerstone/includes/elements/mixins_setup/_.php' );



// Setup
// =============================================================================

$group       = ( isset( $settings['group'] )       ) ? $settings['group']       : 'container';
$group_title = ( isset( $settings['group_title'] ) ) ? $settings['group_title'] : __( 'Container', '__x__' );
$condition   = ( isset( $settings['condition'] )   ) ? $settings['condition']   : array();



// Groups
// =============================================================================

$group_container_setup  = $group . ':setup';
$group_container_design = $group . ':design';



// Conditions
// =============================================================================

$conditions = x_module_conditions( $condition );



// Options
// =============================================================================

$options_container_max_width = array(
  'available_units' => array( 'px', 'em', 'rem', '%' ),
  'valid_keywords'  => array( 'none' ),
  'fallback_value'  => 'none',
  'ranges'          => array(
    'px'  => array( 'min' => '0', 'max' => '300', 'step' => '1'    ),
    'em'  => array( 'min' => '0', 'max' => '30',  'step' => '0.25' ),
    'rem' => array( 'min' => '0', 'max' => '30',  'step' => '0.25' ),
    '%'   => array( 'min' => '0', 'max' => '100', 'step' => '1'    ),
  ),
);

$options_container_max_height = array(
  'available_units' => array( 'px', 'em', 'rem', '%' ),
  'valid_keywords'  => array( 'none' ),
  'fallback_value'  => 'none',
  'ranges'          => array(
    'px'  => array( 'min' => '0', 'max' => '300', 'step' => '1'    ),
    'em'  => array( 'min' => '0', 'max' => '30',  'step' => '0.25' ),
    'rem' => array( 'min' => '0', 'max' => '30',  'step' => '0.25' ),
    '%'   => array( 'min' => '0', 'max' => '100', 'step' => '1'    ),
  ),
);



// Settings
// =============================================================================

$settings_container_bg = array(
  'group'     => $group_container_design,
  'condition' => array( 'container_bg_advanced' => true )
);

$settings_container_flex_css_row = array(
  'k_pre'     => 'container_row',
  'group'     => $group_container_design,
  'condition' => array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'top', 'bottom', 'footer' ) ),
);

$settings_container_flex_css_col = array(
  'k_pre'     => 'container_col',
  'group'     => $group_container_design,
  'condition' => array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'left', 'right' ) ),
);

$settings_container_design = array(
  'k_pre' => 'container',
  'group' => $group_container_design,
);

$settings_container_std_design = array(
  'k_pre' => 'container',
  'group' => $group_std_design,
);

$settings_container_std_bg_design = array(
  'group'     => $group_std_design,
  'condition' => array( 'container_bg_advanced' => true )
);



// Individual Controls
// =============================================================================

$control_container_max_width = array(
  'key'     => 'container_max_width',
  'type'    => 'unit-slider',
  'label'   => __( 'Max Width', '__x__' ),
  'options' => $options_container_max_width,
);

$control_container_max_height = array(
  'key'     => 'container_max_height',
  'type'    => 'unit-slider',
  'label'   => __( 'Max Height', '__x__' ),
  'options' => $options_container_max_height,
);

$control_container_bg_color = array(
  'key'     => 'container_bg_color',
  'type'    => 'color',
  'label'   => __( 'Background', '__x__' ),
  'options' => array( 'label' => __( 'Select', '__x__' ) ),
);

$control_container_bg_advanced = array(
  'keys' => array(
    'bg_advanced' => 'container_bg_advanced',
  ),
  'type'    => 'checkbox-list',
  'options' => array(
    'list' => array(
      array( 'key' => 'bg_advanced', 'label' => __( 'Advanced', '__x__' ) ),
    ),
  ),
);

$control_container_background = array(
  'type'     => 'group',
  'title'    => __( 'Background', '__x__' ),
  'controls' => array(
    $control_container_bg_color,
    $control_container_bg_advanced,
  ),
);



// Control Lists
// =============================================================================

// Advanced
// --------

$control_list_container_adv_setup = array(
  $control_container_max_width,
  $control_container_max_height,
  $control_container_background,
);


// Standard
// --------

$control_list_container_std_design_setup = array(
  $control_container_max_width,
  $control_container_max_height,
);

$control_list_container_std_design_colors_base = array(
  array(
    'keys'      => array( 'value' => 'container_box_shadow_color' ),
    'type'      => 'color',
    'label'     => __( 'Box<br>Shadow', '__x__' ),
    'condition' => array( 'key' => 'container_box_shadow_dimensions', 'op' => 'NOT EMPTY' ),
    'options'   => array( 'label' => __( 'Select', '__x__' ) ),
  ),
  $control_container_bg_color,
);



// Control Groups (Advanced)
// =============================================================================

$control_group_container_adv_setup = array(
  array(
    'type'     => 'group',
    'title'    => __( 'Setup', '__x__' ),
    'group'    => $group_container_setup,
    'controls' => $control_list_container_adv_setup,
  ),
);



// Control Groups (Standard)
// =============================================================================

$control_group_container_std_design_setup = array(
  array(
    'type'     => 'group',
    'title'    => __( 'Design Setup', '__x__' ),
    'group'    => $group_std_design,
    'controls' => $control_list_container_std_design_setup,
  ),
);

$control_group_container_std_design_colors = array_merge(
  array(
    array(
      'type'     => 'group',
      'title'    => __( 'Base Colors', '__x__' ),
      'group'    => $group_std_design,
      'controls' => $control_list_container_std_design_colors_base,
    ),
  ),
  x_control_border(
    array_merge(
      $settings_container_std_design,
      array(
        'options'   => $options_color_only,
        'condition' => array(
          $condition,
          array( 'key' => 'container_border_width', 'op' => 'NOT EMPTY' ),
          array( 'key' => 'container_border_style', 'op' => '!=', 'value' => 'none' )
        ),
      )
    )
  )
);
