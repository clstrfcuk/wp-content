<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/MIXINS_SETUP/BAR.PHP
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

$group       = ( isset( $settings['group'] )       ) ? $settings['group']       : 'bar';
$group_title = ( isset( $settings['group_title'] ) ) ? $settings['group_title'] : __( 'Bar', '__x__' );
$condition   = ( isset( $settings['condition'] )   ) ? $settings['condition']   : array();



// Groups
// =============================================================================

$group_bar_setup  = $group . ':setup';
$group_bar_design = $group . ':design';



// Conditions
// =============================================================================

$conditions                       = x_module_conditions( $condition );
$condition_bar_bg                 = array( 'bar_bg_advanced' => true );
$condition_bar_is_t               = array( '_region' => 'top' );
$condition_bar_is_t_and_sticky    = array( array( '_region' => 'top' ), array( 'bar_sticky' => true ) );
$condition_bar_is_t_and_absolute  = array( array( '_region' => 'top' ), array( 'bar_position_top' => 'absolute' ) );
$condition_bar_is_lr              = array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'left', 'right' ) );
$condition_bar_is_tbf             = array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'top', 'bottom', 'footer' ) );
$condition_bar_height_is_auto     = array( 'bar_height' => 'auto' );
$condition_bar_height_is_not_auto = array( 'key' => 'bar_height', 'op' => '!=', 'value' => 'auto' );



// Options
// =============================================================================

$options_bar_base_font_size = array(
  'available_units' => array( 'px', 'em', 'rem' ),
  'valid_keywords'  => array( 'calc' ),
  'fallback_value'  => '16px',
  'ranges'          => array(
    'px'  => array( 'min' => 10,  'max' => 24,  'step' => 1    ),
    'em'  => array( 'min' => 0.5, 'max' => 1.5, 'step' => 0.01 ),
    'rem' => array( 'min' => 0.5, 'max' => 1.5, 'step' => 0.01 ),
  ),
);

$options_bar_z_index = array(
  'unit_mode'      => 'unitless',
  'fallback_value' => '9999',
);

$options_bar_position_top = array(
  'choices' => array(
    array( 'value' => 'relative', 'label' => __( 'Relative', '__x__' ) ),
    array( 'value' => 'absolute', 'label' => __( 'Absolute', '__x__' ) ),
  ),
);

$options_bar_offset = array(
  'available_units' => array( 'px' ),
  'valid_keywords'  => array( 'calc' ),
  'fallback_value'  => '0px',
);

$options_bar_width = array(
  'available_units' => array( 'px', 'em', 'rem', 'vw', 'vh' ),
  'valid_keywords'  => array( 'calc' ),
  'fallback_value'  => '210px',
  'ranges'          => array(
    'px'  => array( 'min' => '30',  'max' => '300', 'step' => '1'    ),
    'em'  => array( 'min' => '1.5', 'max' => '15',  'step' => '0.01' ),
    'rem' => array( 'min' => '1.5', 'max' => '15',  'step' => '0.01' ),
    'vw'  => array( 'min' => '1',   'max' => '100', 'step' => '1'    ),
    'vh'  => array( 'min' => '1',   'max' => '100', 'step' => '1'    ),
  ),
);

$options_bar_height = array(
  'available_units' => array( 'px', 'em', 'rem', 'vw', 'vh' ),
  'valid_keywords'  => array( 'calc', 'auto' ),
  'fallback_value'  => '100px',
  'ranges'          => array(
    'px'  => array( 'min' => '30',  'max' => '150', 'step' => '1'    ),
    'em'  => array( 'min' => '1.5', 'max' => '7.5', 'step' => '0.01' ),
    'rem' => array( 'min' => '1.5', 'max' => '7.5', 'step' => '0.01' ),
    'vw'  => array( 'min' => '1',   'max' => '100', 'step' => '1'    ),
    'vh'  => array( 'min' => '1',   'max' => '100', 'step' => '1'    ),
  ),
);

$options_bar_outer_spacing = array(
  'available_units' => array( 'px', 'em', 'rem' ),
  'valid_keywords'  => array( 'calc' ),
  'fallback_value'  => '35px',
  'ranges'          => array(
    'px'  => array( 'min' => '0', 'max' => '50',  'step' => '1'    ),
    'em'  => array( 'min' => '0', 'max' => '2.5', 'step' => '0.01' ),
    'rem' => array( 'min' => '0', 'max' => '2.5', 'step' => '0.01' ),
  ),
);

$options_bar_content_length = array(
  'available_units' => array( '%' ),
  'valid_keywords'  => array( 'calc', 'auto' ),
  'fallback_value'  => '100%',
  'ranges'          => array( '%' => array( 'min' => '60', 'max' => '100', 'step' => '1' ) ),
);

$options_bar_content_max_length = array(
  'available_units' => array( 'px', 'em', 'rem' ),
  'valid_keywords'  => array( 'calc', 'none' ),
  'fallback_value'  => 'none',
  'ranges'          => array(
    'px'  => array( 'min' => '500', 'max' => '1200', 'step' => '1'   ),
    'em'  => array( 'min' => '25',  'max' => '60',   'step' => '0.1' ),
    'rem' => array( 'min' => '25',  'max' => '60',   'step' => '0.1' ),
  ),
);

$options_bar_sticky_trigger_offset = array(
  'unit_mode'      => 'unitless',
  'fallback_value' => '0',
  'min'            => '0',
  'max'            => '150',
  'step'           => '1',
);

$options_bar_sticky_shrink = array(
  'unit_mode'      => 'unitless',
  'fallback_value' => 1,
  'min'            => 0.33,
  'max'            => 1,
  'step'           => 0.001,
);



// Settings
// =============================================================================

$settings_bar_bg = array(
  'group'     => $group_bar_design,
  'condition' => $condition_bar_bg,
);

$settings_bar_flex_css_row = array(
  'k_pre'     => 'bar_row',
  'group'     => $group_bar_design,
  'condition' => $condition_bar_is_tbf,
);

$settings_bar_flex_css_col = array(
  'k_pre'     => 'bar_col',
  'group'     => $group_bar_design,
  'condition' => $condition_bar_is_lr,
);

$settings_bar_design = array(
  'k_pre' => 'bar',
  'group' => $group_bar_design
);

$settings_bar_std_design = array(
  'k_pre' => 'bar',
  'group' => $group_std_design
);



// Individual Controls
// =============================================================================

$control_bar_base_font_size = array(
  'key'     => 'bar_base_font_size',
  'type'    => 'unit',
  'options' => $options_bar_base_font_size,
);

$control_bar_z_index = array(
  'key'     => 'bar_z_index',
  'type'    => 'unit',
  'options' => $options_bar_z_index,
);

$control_bar_base_font_size_and_z_index = array(
  'type'     => 'group',
  'title'    => __( 'Font Size &amp; Z-Index', '__x__' ),
  'controls' => array(
    $control_bar_base_font_size,
    $control_bar_z_index,
  ),
);

$control_bar_position_top = array(
  'key'       => 'bar_position_top',
  'type'      => 'choose',
  'title'     => __( 'Initial Position', '__x__' ),
  'condition' => $condition_bar_is_t,
  'options'   => $options_bar_position_top,
);

$control_bar_scroll = array(
  'key'       => 'bar_scroll',
  'type'      => 'choose',
  'label'     => __( 'Content Scrolling', '__x__' ),
  'condition' => $condition_bar_height_is_not_auto,
  'options'   => $options_choices_off_on_bool,
);

$control_bar_sticky = array(
  'key'       => 'bar_sticky',
  'type'      => 'choose',
  'label'     => __( 'Sticky Bar', '__x__' ),
  'condition' => array( '_region' => 'top' ),
  'options'   => $options_choices_off_on_bool,
);

$control_bar_bg_color = array(
  'keys'    => array( 'value' => 'bar_bg_color' ),
  'type'    => 'color',
  'label'   => __( 'Background', '__x__' ),
  'options' => array( 'label' => __( 'Select', '__x__' ) ),
);

$control_bar_bg_advanced = array(
  'keys' => array(
    'bg_advanced' => 'bar_bg_advanced',
  ),
  'type'    => 'checkbox-list',
  'options' => array(
    'list' => array(
      array( 'key' => 'bg_advanced', 'label' => __( 'Advanced', '__x__' ) ),
    ),
  ),
);

$control_bar_background = array(
  'type'     => 'group',
  'title'    => __( 'Background', '__x__' ),
  'controls' => array(
    $control_bar_bg_color,
    $control_bar_bg_advanced,
  ),
);

$control_bar_sticky_options = array(
  'keys' => array(
    'sticky_keep_margins'   => 'bar_sticky_keep_margin',
    'sticky_hide_initially' => 'bar_sticky_hide_initially',
    'sticky_z_stack'        => 'bar_sticky_z_stack',
  ),
  'type'       => 'checkbox-list',
  'label'      => __( 'Options', '__x__' ),
  'options'    => array(
    'list' => array(
      array( 'key' => 'sticky_keep_margins',   'label' => __( 'Keep Margin', '__x__' ),    'half' => true ),
      array( 'key' => 'sticky_hide_initially', 'label' => __( 'Hide Initially', '__x__' ), 'half' => true ),
      array( 'key' => 'sticky_z_stack',        'label' => __( 'Z-Index Stack', '__x__' ),  'half' => true ),
    ),
  ),
);

$control_bar_sticky_trigger_selector = array(
  'key'     => 'bar_sticky_trigger_selector',
  'type'    => 'text',
  'label'   => __( 'Trigger Selector', '__x__' ),
  'options' => array( 'placeholder' => __( '#target-element (optional)', '__x__' ) ),
);

$control_bar_sticky_trigger_offset = array(
  'key'     => 'bar_sticky_trigger_offset',
  'type'    => 'unit-slider',
  'label'   => __( 'Trigger Offset', '__x__' ),
  'options' => $options_bar_sticky_trigger_offset,
);

$control_bar_sticky_shrink = array(
  'key'        => 'bar_sticky_shrink',
  'type'       => 'unit-slider',
  'label'      => __( 'Shrink Amount', '__x__' ),
  'conditions' => array( array( 'bar_sticky' => true ) ),
  'options'    => $options_bar_sticky_shrink,
);

$control_bar_width = array(
  'key'       => 'bar_width',
  'type'      => 'unit-slider',
  'title'     => __( 'Width', '__x__' ),
  'condition' => $condition_bar_is_lr,
  'options'   => $options_bar_width,
);

$control_bar_height = array(
  'key'       => 'bar_height',
  'type'      => 'unit-slider',
  'title'     => __( 'Height', '__x__' ),
  'condition' => $condition_bar_is_tbf,
  'options'   => $options_bar_height,
);

$control_bar_outer_spacing = array(
  'key'     => 'bar_outer_spacing',
  'type'    => 'unit-slider',
  'title'   => __( 'Outer Spacing', '__x__' ),
  'options' => $options_bar_outer_spacing,
);

$control_bar_content_length = array(
  'key'       => 'bar_content_length',
  'type'      => 'unit-slider',
  'title'     => __( 'Content Length', '__x__' ),
  'options'   => $options_bar_content_length,
);

$control_bar_content_max_length = array(
  'key'     => 'bar_content_max_length',
  'type'    => 'unit-slider',
  'title'   => __( 'Content Max Length', '__x__' ),
  'options' => $options_bar_content_max_length,
);

$control_bar_margin_top = array(
  'key'     => 'bar_margin_top',
  'type'    => 'unit',
  'options' => $options_bar_offset,
);

$control_bar_margin_sides = array(
  'key'     => 'bar_margin_sides',
  'type'    => 'unit',
  'options' => $options_bar_offset,
);

$control_bar_margin_top_and_sides = array(
  'type'       => 'group',
  'title'      => __( 'Margin Top &amp; Sides', '__x__' ),
  'conditions' => $condition_bar_is_t_and_absolute,
  'controls'   => array(
    $control_bar_margin_top,
    $control_bar_margin_sides,
  ),
);



// Control Lists
// =============================================================================

// Advanced
// --------

$control_list_bar_adv_setup = array(
  $control_bar_base_font_size_and_z_index,
  $control_bar_position_top,
  $control_bar_scroll,
  $control_bar_sticky,
  $control_bar_background,
);

$control_list_bar_adv_setup_sticky = array(
  $control_bar_sticky_options,
  $control_bar_sticky_trigger_selector,
  $control_bar_sticky_trigger_offset,
  $control_bar_sticky_shrink,
);

$control_list_bar_adv_dimensions = array(
  $control_bar_width,
  $control_bar_height,
  $control_bar_outer_spacing,
  $control_bar_content_length,
  $control_bar_content_max_length,
  $control_bar_margin_top_and_sides,
);


// Standard
// --------

$control_list_bar_std_design_setup = array(
  $control_bar_base_font_size_and_z_index,
  $control_bar_width,
  $control_bar_height,
  $control_bar_outer_spacing,
  $control_bar_content_length,
  $control_bar_content_max_length,
  // $control_bar_margin_top_and_sides,
);

$control_list_bar_std_design_colors_base = array(
  array(
    'keys'      => array( 'value' => 'bar_box_shadow_color' ),
    'type'      => 'color',
    'label'     => __( 'Box<br>Shadow', '__x__' ),
    'condition' => array( 'key' => 'bar_box_shadow_dimensions', 'op' => 'NOT EMPTY' ),
    'options'   => array( 'label' => __( 'Select', '__x__' ) ),
  ),
  $control_bar_bg_color,
);



// Control Groups (Advanced)
// =============================================================================

$control_group_bar_adv_setup = array(
  array(
    'type'     => 'group',
    'title'    => __( 'Setup', '__x__' ),
    'group'    => $group_bar_setup,
    'controls' => $control_list_bar_adv_setup,
  ),
);

$control_group_bar_adv_setup_sticky = array(
  array(
    'type'       => 'group',
    'title'      => __( 'Sticky Setup', '__x__' ),
    'group'      => $group_bar_design,
    'conditions' => $condition_bar_is_t_and_sticky,
    'controls'   => $control_list_bar_adv_setup_sticky,
  ),
);

$control_group_bar_adv_dimensions = array(
  array(
    'type'     => 'group',
    'title'    => __( 'Dimensions', '__x__' ),
    'group'    => $group_bar_design,
    'controls' => $control_list_bar_adv_dimensions,
  ),
);



// Control Groups (Standard)
// =============================================================================

$control_group_bar_std_design_setup = array(
  array(
    'type'     => 'group',
    'title'    => __( 'Design Setup', '__x__' ),
    'group'    => $group_std_design,
    'controls' => $control_list_bar_std_design_setup,
  ),
);

$control_group_bar_std_design_colors = array_merge(
  array(
    array(
      'type'     => 'group',
      'title'    => __( 'Base Colors', '__x__' ),
      'group'    => $group_std_design,
      'controls' => $control_list_bar_std_design_colors_base,
    ),
  ),
  x_control_border(
    array_merge(
      $settings_bar_std_design,
      array(
        'options'   => $options_color_only,
        'condition' => array(
          $condition,
          array( 'key' => 'bar_border_width', 'op' => 'NOT EMPTY' ),
          array( 'key' => 'bar_border_style', 'op' => '!=', 'value' => 'none' )
        ),
      )
    )
  )
);
