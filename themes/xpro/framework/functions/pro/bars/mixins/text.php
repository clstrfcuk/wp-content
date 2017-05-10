<?php

// =============================================================================
// FUNCTIONS/BARS/MIXINS/DROPDOWN.PHP
// -----------------------------------------------------------------------------
// Bar control mixins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Controls
//   02. Control Groups
//   03. Values
// =============================================================================

// Controls
// =============================================================================

function x_controls_text( $settings = array() ) {

  // Setup
  // -----
  // 01. Available types:
  //     -- 'standard'
  //     -- 'headline'

  $group       = ( isset( $settings['group'] )     ) ? $settings['group']     : 'text';
  $condition   = ( isset( $settings['condition'] ) ) ? $settings['condition'] : array();
  $type        = ( isset( $settings['type'] )      ) ? $settings['type']      : 'standard';

  $group_setup  = $group . ':setup';
  $group_design = $group . ':design';


  // Setup - Conditions
  // ------------------

  $conditions = x_module_conditions( $condition );


  // Setup - Options
  // ---------------

  $options_text_editor = array();

  if ( $type === 'headline' ) {
    $options_text_editor['mode'] = 'html';
  }


  // Setup - Controls
  // ----------------

  $controls_setup = array(
    array(
      'key'     => 'text_content',
      'type'    => 'text-editor',
      'title'   => __( 'Text', '__x__' ),
      'options' => $options_text_editor,
    ),
  );

  if ( $type === 'headline' ) {
    $controls_setup[] = array(
      'type'     => 'group',
      'label'    => __( 'Tag &amp; Text Overflow', '__x__' ),
      'controls' => array(
        array(
          'key'     => 'text_tag',
          'type'    => 'select',
          'options' => array(
            'choices' => array(
              array( 'value' => 'p',    'label' => 'p'    ),
              array( 'value' => 'h1',   'label' => 'h1'   ),
              array( 'value' => 'h2',   'label' => 'h2'   ),
              array( 'value' => 'h3',   'label' => 'h3'   ),
              array( 'value' => 'h4',   'label' => 'h4'   ),
              array( 'value' => 'h5',   'label' => 'h5'   ),
              array( 'value' => 'h6',   'label' => 'h6'   ),
              array( 'value' => 'div',  'label' => 'div'  ),
              array( 'value' => 'span', 'label' => 'span' ),
            ),
          ),
        ),
        array(
          'keys' => array(
            'text_overflow' => 'text_overflow',
          ),
          'type'    => 'checkbox-list',
          'options' => array(
            'list' => array(
              array( 'key' => 'text_overflow', 'label' => __( 'Enable', '__x__' ) ),
            ),
          ),
        ),
      ),
    );
  }

  $controls_setup[] = array(
    'type'     => 'group',
    'label'    => __( 'Width &amp; Max Width', '__x__' ),
    'controls' => array(
      array(
        'key'     => 'text_width',
        'type'    => 'unit',
        'options' => array(
          'available_units' => array( 'px', 'em', 'rem', '%' ),
          'valid_keywords'  => array( 'auto' ),
          'fallback_value'  => 'auto',
        ),
      ),
      array(
        'key'     => 'text_max_width',
        'type'    => 'unit',
        'options' => array(
          'available_units' => array( 'px', 'em', 'rem', '%' ),
          'valid_keywords'  => array( 'none' ),
          'fallback_value'  => 'none',
        ),
      ),
    ),
  );

  $controls_setup[] = array(
    'key'   => 'text_bg_color',
    'type'  => 'color',
    'title' => __( 'Background', '__x__' ),
  );


  // Returned Value
  // --------------

  $controls = array_merge(
    array(
      array(
        'type'       => 'group',
        'title'      => __( 'Setup', '__x__' ),
        'group'      => $group_setup,
        'controls'   => $controls_setup,
        'conditions' => $conditions,
      ),
    ),
    x_control_text_format( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_text_style( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_text_shadow( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_margin( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_padding( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_border( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_border_radius( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) ),
    x_control_box_shadow( array( 'k_pre' => 'text', 'group' => $group_design, 'condition' => $conditions ) )
  );

  return $controls;

}



// Control Groups
// =============================================================================

function x_control_groups_text( $settings = array() ) {

  $group       = ( isset( $settings['group'] )       ) ? $settings['group']       : 'text';
  $group_title = ( isset( $settings['group_title'] ) ) ? $settings['group_title'] : __( 'Text', '__x__' );

  $control_groups = array(
    $group             => array( 'title' => $group_title ),
    $group . ':setup'  => array( 'title' => __( 'Setup', '__x__' ) ),
    $group . ':design' => array( 'title' => __( 'Design', '__x__' ) ),
  );

  return $control_groups;

}



// Values
// =============================================================================

function x_values_text( $settings = array() ) {

  // Setup
  // -----

  $type         = ( isset( $settings['type'] ) ) ? $settings['type'] : 'standard';
  $text_content = ( $type === 'standard' ) ? __( 'Input your text here! The text element is intended for longform copy that could potentially include multiple paragraphs.', '__x__' ) : __( 'Short and Sweet Headlines are Best!', '__x__' );


  // Values
  // ------

  $values = array(
    'text_type'                   => x_module_value( $type, 'markup' ),
    'text_content'                => x_module_value( $text_content, 'markup:html' ),
    'text_width'                  => x_module_value( 'auto', 'style' ),
    'text_max_width'              => x_module_value( 'none', 'style' ),
    'text_bg_color'               => x_module_value( 'transparent', 'style:color' ),
    'text_font_family'            => x_module_value( 'fw_fallback', 'style:font-family' ),
    'text_font_weight'            => x_module_value( 'fw_fallback:400', 'style:font-weight' ),
    'text_font_size'              => x_module_value( '1em', 'style' ),
    'text_line_height'            => x_module_value( '1.4', 'style' ),
    'text_letter_spacing'         => x_module_value( '0em', 'style' ),
    'text_font_style'             => x_module_value( 'normal', 'style' ),
    'text_text_align'             => x_module_value( 'none', 'style' ),
    'text_text_decoration'        => x_module_value( 'none', 'style' ),
    'text_text_transform'         => x_module_value( 'none', 'style' ),
    'text_text_color'             => x_module_value( 'rgba(0, 0, 0, 0.35)', 'style:color' ),
    'text_text_shadow_dimensions' => x_module_value( '0px 0px 0px', 'style' ),
    'text_text_shadow_color'      => x_module_value( 'transparent', 'style:color' ),
    'text_margin'                 => x_module_value( '0em', 'style' ),
    'text_padding'                => x_module_value( '0em', 'style' ),
    'text_border_width'           => x_module_value( '0px', 'style' ),
    'text_border_style'           => x_module_value( 'none', 'style' ),
    'text_border_color'           => x_module_value( 'transparent', 'style:color' ),
    'text_border_radius'          => x_module_value( '0em', 'style' ),
    'text_box_shadow_dimensions'  => x_module_value( '0em 0em 0em 0em', 'style' ),
    'text_box_shadow_color'       => x_module_value( 'transparent', 'style:color' ),
  );

  if ( $type === 'headline' ) {
    $values = array_merge(
      $values,
      array(
        'text_tag'      => x_module_value( 'div', 'markup' ),
        'text_overflow' => x_module_value( false, 'style' ),
      )
    );
  }


  // Returned Value
  // --------------

  return x_bar_mixin_values( $values, $settings );

}
