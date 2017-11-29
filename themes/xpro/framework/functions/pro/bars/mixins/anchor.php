<?php

// =============================================================================
// FUNCTIONS/BARS/MIXINS/ANCHOR.PHP
// -----------------------------------------------------------------------------
// Bar control mixins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Controls
//       a. Setup - General
//       b. Setup - Conditions
//       c. Setup - Settings
//       d. Setup - Options
//       e. Setup - Controls
//       f. Controls - Setup
//       g. Controls - Design
//       h. Controls - Text
//       i. Controls - Graphic
//       j. Controls - Sub Indicator
//       k. Controls - Interactions
//   02. Control Groups
//   03. Values
// =============================================================================

// Controls
// =============================================================================

function x_controls_anchor( $settings = array() ) {

  // Setup - General
  // ---------------
  // 01. Available types:
  //     -- 'button'
  //     -- 'menu-item'
  //     -- 'toggle'

  $t_pre            = ( isset( $settings['t_pre'] )            ) ? $settings['t_pre']            : '';
  $k_pre            = ( isset( $settings['k_pre'] )            ) ? $settings['k_pre'] . '_'      : '';
  $group            = ( isset( $settings['group'] )            ) ? $settings['group']            : 'anchor';
  $condition        = ( isset( $settings['condition'] )        ) ? $settings['condition']        : array();
  $type             = ( isset( $settings['type'] )             ) ? $settings['type']             : 'menu-item'; // 01
  $lr_only          = ( isset( $settings['lr_only'] )          ) ? $settings['lr_only']          : false;
  $tb_only          = ( isset( $settings['tb_only'] )          ) ? $settings['tb_only']          : false;
  $tbf_only         = ( isset( $settings['tbf_only'] )         ) ? $settings['tbf_only']         : false;
  $has_template     = ( isset( $settings['has_template'] )     ) ? $settings['has_template']     : true;
  $has_link_control = ( isset( $settings['has_link_control'] ) ) ? $settings['has_link_control'] : false;

  $group_setup         = $group . ':setup';
  $group_design        = $group . ':design';
  $group_text          = $group . ':text';
  $group_graphic       = $group . ':graphic';
  $group_sub_indicator = $group . ':sub_indicator';
  $group_interactions  = $group . ':interactions';


  // Setup - Conditions
  // ------------------

  $lr_only  = ( $lr_only )  ? array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'left', 'right' ) )           : array();
  $tb_only  = ( $tb_only )  ? array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'top', 'bottom' ) )           : array();
  $tbf_only = ( $tbf_only ) ? array( 'key' => '_region', 'op' => 'IN', 'value' => array( 'top', 'bottom', 'footer' ) ) : array();

  $conditions                    = array( $condition, $lr_only, $tb_only, $tbf_only ); // x_module_conditions( $condition )
  $conditions_text               = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_text' => true ) );
  $conditions_secondary_text     = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_text' => true ), array( 'key' => $k_pre . 'anchor_text_secondary_content', 'op' => 'NOT IN', 'value' => array( '' ) ) );
  $conditions_graphic_main       = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_graphic' => true ) );
  $conditions_graphic_icon       = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_graphic' => true ), array( $k_pre . 'anchor_graphic_type' => 'icon' ) );
  $conditions_graphic_icon_alt   = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_graphic' => true ), array( $k_pre . 'anchor_graphic_type' => 'icon' ), array( $k_pre . 'anchor_graphic_icon_alt_enable' => 'icon' ) );
  $conditions_graphic_image      = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_graphic' => true ), array( $k_pre . 'anchor_graphic_type' => 'image' ) );
  $conditions_graphic_toggle     = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_graphic' => true ), array( $k_pre . 'anchor_graphic_type' => 'toggle' ) );
  $conditions_sub_indicator      = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_sub_indicator' => true ) );
  $conditions_primary_particle   = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_primary_particle' => true ) );
  $conditions_secondary_particle = array( $condition, $lr_only, $tb_only, $tbf_only, array( $k_pre . 'anchor_secondary_particle' => true ) );


  // Setup - Settings
  // ----------------

  $settings_anchor_link = array(
    'k_pre'     => $k_pre . 'anchor',
    't_pre'     => __( $t_pre, '__x__' ),
    'group'     => $group_setup,
    'condition' => $conditions,
    'info'      => true,
    'blank'     => true,
    'nofollow'  => true,
  );

  $settings_anchor_design = array(
    'k_pre'     => $k_pre . 'anchor',
    't_pre'     => __( $t_pre, '__x__' ),
    'group'     => $group_design,
    'condition' => $conditions,
    'alt_color' => true,
    'options'   => array(
      'color' => array(
        'label'     => __( 'Base', '__x__' ),
        'alt_label' => __( 'Interaction', '__x__' ),
      ),
    ),
  );

  $settings_anchor_design_no_options = array(
    'k_pre'     => $k_pre . 'anchor',
    't_pre'     => __( $t_pre, '__x__' ),
    'group'     => $group_design,
    'condition' => $conditions,
  );

  $settings_anchor_flex_css = array(
    'k_pre'     => $k_pre . 'anchor',
    't_pre'     => __( $t_pre . ' Content', '__x__' ),
    'group'     => $group_design,
    'condition' => $conditions,
  );

  $settings_anchor_primary_text = array(
    'k_pre'     => $k_pre . 'anchor_primary',
    't_pre'     => __( $t_pre . ' Primary', '__x__' ),
    'group'     => $group_text,
    'condition' => $conditions_text,
    'alt_color' => true,
    'options'   => array(
      'color' => array(
        'label'     => __( 'Base', '__x__' ),
        'alt_label' => __( 'Interaction', '__x__' ),
      ),
    ),
  );

  $settings_anchor_secondary_text = array(
    'k_pre'     => $k_pre . 'anchor_secondary',
    't_pre'     => __( $t_pre . ' Secondary', '__x__' ),
    'group'     => $group_text,
    'condition' => $conditions_secondary_text,
    'alt_color' => true,
    'options'   => array(
      'color' => array(
        'label'     => __( 'Base', '__x__' ),
        'alt_label' => __( 'Interaction', '__x__' ),
      ),
    ),
  );


  // Setup - Options
  // ---------------

  $options_anchor_graphic_type_choices = array(
    array( 'value' => 'icon',  'icon' => 'flag' ),
    array( 'value' => 'image', 'icon' => 'picture-o' ),
  );

  if ( $type === 'toggle' ) {
    $options_anchor_graphic_type_choices[] = array( 'value' => 'toggle', 'icon' => 'bars' );
  }

  $options_anchor_base_font_size = array(
    'available_units' => array( 'px', 'em', 'rem' ),
    'valid_keywords'  => array( 'calc' ),
    'fallback_value'  => '1em',
    'ranges'          => array(
      'px'  => array( 'min' => 10,  'max' => 36, 'step' => 1    ),
      'em'  => array( 'min' => 0.5, 'max' => 4,  'step' => 0.01 ),
      'rem' => array( 'min' => 0.5, 'max' => 4,  'step' => 0.01 ),
    ),
  );

  $options_anchor_width_and_height = array(
    'available_units' => array( 'px', 'em', 'rem', '%' ),
    'fallback_value'  => 'auto',
    'valid_keywords'  => array( 'auto', 'calc' ),
  );

  $options_anchor_min_width_and_height = array(
    'available_units' => array( 'px', 'em', 'rem', '%' ),
    'fallback_value'  => '0px',
    'valid_keywords'  => array( 'calc' ),
  );

  $options_anchor_max_width_and_height = array(
    'available_units' => array( 'px', 'em', 'rem', '%' ),
    'fallback_value'  => 'auto',
    'valid_keywords'  => array( 'none', 'calc' ),
  );

  $options_anchor_graphic_and_sub_indicator_width_and_height = array(
    'available_units' => array( 'px', 'em', 'rem' ),
    'fallback_value'  => '1em',
    'valid_keywords'  => array( 'auto' ),
  );


  // Setup - Controls
  // ----------------

  $controls_text = array(
    array(
      'key'     => $k_pre . 'anchor_text',
      'type'    => 'choose',
      'label'   => __( 'Enable', '__x__' ),
      'options' => array(
        'choices' => array(
          array( 'value' => false, 'label' => __( 'Off', '__x__' ) ),
          array( 'value' => true,  'label' => __( 'On', '__x__' ) ),
        ),
      ),
    ),
    array(
      'keys' => array(
        'text_overflow' => $k_pre . 'anchor_text_overflow',
      ),
      'type'       => 'checkbox-list',
      'label'      => __( 'Text Overflow', '__x__' ),
      'conditions' => $conditions_text,
      'options'    => array(
        'list' => array(
          array( 'key' => 'text_overflow', 'label' => __( 'Enable', '__x__' ) ),
        ),
      ),
    ),
  );

  if ( $has_template && $type !== 'menu-item' ) {

    $controls_text[] = array(
      'key'        => $k_pre . 'anchor_text_primary_content',
      'type'       => 'text',
      'label'      => __( 'Primary Text', '__x__' ),
      'conditions' => $conditions_text,
      'options'    => array(
        'placeholder' => __( 'No Output When Empty', '__x__' ),
      ),
    );

    $controls_text[] = array(
      'key'        => $k_pre . 'anchor_text_secondary_content',
      'type'       => 'text',
      'label'      => __( 'Secondary Text', '__x__' ),
      'conditions' => $conditions_text,
      'options'    => array(
        'placeholder' => __( 'No Output When Empty', '__x__' ),
      ),
    );

  } else if ( $has_template && $type === 'menu-item' ) {

    $controls_text[] = array(
      'key'        => $k_pre . 'anchor_text_primary_content',
      'type'       => 'choose',
      'label'      => __( 'Primary Text', '__x__' ),
      'conditions' => $conditions_text,
      'options'    => array(
        'choices' => array(
          array( 'value' => '',   'label' => __( 'Off', '__x__' ) ),
          array( 'value' => 'on', 'label' => __( 'On', '__x__' ) ),
        ),
      ),
    );

    $controls_text[] = array(
      'key'        => $k_pre . 'anchor_text_secondary_content',
      'type'       => 'choose',
      'label'      => __( 'Secondary Text', '__x__' ),
      'conditions' => $conditions_text,
      'options'    => array(
        'choices' => array(
          array( 'value' => '',   'label' => __( 'Off', '__x__' ) ),
          array( 'value' => 'on', 'label' => __( 'On', '__x__' ) ),
        ),
      ),
    );

  }

  $controls_text[] = array(
    'type'       => 'group',
    'label'      => __( 'Text Order &amp; Spacing', '__x__' ),
    'conditions' => $conditions_secondary_text,
    'controls'   => array(
      array(
        'keys' => array(
          'text_reverse' => $k_pre . 'anchor_text_reverse',
        ),
        'type'    => 'checkbox-list',
        'label'   => __( 'Order', '__x__' ),
        'options' => array(
          'list' => array(
            array( 'key' => 'text_reverse', 'label' => __( 'Reverse', '__x__' ) ),
          ),
        ),
      ),
      array(
        'key'     => $k_pre . 'anchor_text_spacing',
        'type'    => 'unit',
        'label'   => __( 'Spacing', '__x__' ),
        'options' => array(
          'available_units' => array( 'px', 'em', 'rem' ),
          'fallback_value'  => '5px',
        ),
      ),
    ),
  );


  // Controls - Setup
  // ----------------

  $controls = array(
    array(
      'type'       => 'group',
      'title'      => __( $t_pre . ' Setup', '__x__' ),
      'group'      => $group_setup,
      'conditions' => $conditions,
      'controls'   => array(
        array(
          'key'     => $k_pre . 'anchor_base_font_size',
          'type'    => 'slider',
          'label'   => __( 'Base Font Size', '__x__' ),
          'options' => $options_anchor_base_font_size,
        ),
        array(
          'type'     => 'group',
          'title'    => __( 'Width &amp;</br>Height', '__x__' ),
          'controls' => array(
            array(
              'key'     => $k_pre . 'anchor_width',
              'type'    => 'unit',
              'options' => $options_anchor_width_and_height,
            ),
            array(
              'key'     => $k_pre . 'anchor_height',
              'type'    => 'unit',
              'options' => $options_anchor_width_and_height,
            ),
          ),
        ),
        array(
          'type'     => 'group',
          'title'    => __( 'Min Width</br>&amp; Height', '__x__' ),
          'controls' => array(
            array(
              'key'     => $k_pre . 'anchor_min_width',
              'type'    => 'unit',
              'options' => $options_anchor_min_width_and_height,
            ),
            array(
              'key'     => $k_pre . 'anchor_min_height',
              'type'    => 'unit',
              'options' => $options_anchor_min_width_and_height,
            ),
          ),
        ),
        array(
          'type'     => 'group',
          'title'    => __( 'Max Width</br>&amp; Height', '__x__' ),
          'controls' => array(
            array(
              'key'     => $k_pre . 'anchor_max_width',
              'type'    => 'unit',
              'options' => $options_anchor_max_width_and_height,
            ),
            array(
              'key'     => $k_pre . 'anchor_max_height',
              'type'    => 'unit',
              'options' => $options_anchor_max_width_and_height,
            ),
          ),
        ),
        array(
          'keys' => array(
            'value' => $k_pre . 'anchor_bg_color',
            'alt'   => $k_pre . 'anchor_bg_color_alt',
          ),
          'type'    => 'color',
          'label'   => __( 'Background', '__x__' ),
          'options' => array(
            'label'     => __( 'Base', '__x__' ),
            'alt_label' => __( 'Interaction', '__x__' ),
          ),
        ),
      ),
    )
  );


  // Controls - Setup (Link)
  // -----------------------

  if ( $has_link_control ) {
    $controls = array_merge(
      $controls,
      x_control_link( $settings_anchor_link )
    );
  }


  // Controls - Design
  // -----------------

  if ( $has_template ) {
    $controls = array_merge(
      $controls,
      x_control_flex_layout_css( $settings_anchor_flex_css )
    );
  }

  $controls = array_merge(
    $controls,
    x_control_margin( $settings_anchor_design_no_options ),
    x_control_padding( $settings_anchor_design ),
    x_control_border( $settings_anchor_design ),
    x_control_border_radius( $settings_anchor_design ),
    x_control_box_shadow( $settings_anchor_design )
  );


  // Controls - Text
  // ---------------

  if ( $has_template ) {
    $controls = array_merge(
      $controls,
      array(
        array(
          'type'       => 'group',
          'title'      => __( $t_pre . ' Text Setup', '__x__' ),
          'group'      => $group_text,
          'conditions' => $conditions,
          'controls'   => $controls_text,
        ),
      )
    );
  }

  $controls = array_merge(
    $controls,
    x_control_margin( array(
      'k_pre'     => $k_pre . 'anchor_text',
      't_pre'     => __( $t_pre . ' Text', '__x__' ),
      'group'     => $group_text,
      'condition' => $conditions_text,
    ) ),
    x_control_text_format( $settings_anchor_primary_text ),
    x_control_text_style( $settings_anchor_primary_text ),
    x_control_text_shadow( $settings_anchor_primary_text )
  );

  if ( $has_template ) {
    $controls = array_merge(
      $controls,
      x_control_text_format( $settings_anchor_secondary_text ),
      x_control_text_style( $settings_anchor_secondary_text ),
      x_control_text_shadow( $settings_anchor_secondary_text )
    );
  }


  // Controls - Graphic
  // ------------------

  if ( $type !== 'menu-item' ) {

    $graphic_control_icon_primary = array(
      'key'        => $k_pre . 'anchor_graphic_icon',
      'type'       => 'icon',
      'group'      => $group_graphic,
      'label'      => __( 'Primary Icon', '__x__' ),
      'conditions' => $conditions_graphic_icon,
    );

    $graphic_control_icon_secondary = array(
      'key'        => $k_pre . 'anchor_graphic_icon_alt',
      'type'       => 'icon',
      'group'      => $group_graphic,
      'label'      => __( 'Secondary Icon', '__x__' ),
      'conditions' => $conditions_graphic_icon_alt,
    );

    $graphic_control_image_primary_non_menu_item = array(
      'keys' => array(
        'img_source' => $k_pre . 'anchor_graphic_image_src',
        'is_retina'  => $k_pre . 'anchor_graphic_image_retina',
        'width'      => $k_pre . 'anchor_graphic_image_width',
        'height'     => $k_pre . 'anchor_graphic_image_height',
      ),
      'type'       => 'image',
      'title'      => __( $t_pre . ' Primary Graphic Image', '__x__' ),
      'group'      => $group_graphic,
      'conditions' => $conditions_graphic_image,
    );

    $graphic_control_image_secondary_non_menu_item = array(
      'type'       => 'group',
      'title'      => __( $t_pre . ' Secondary Graphic Image', '__x__' ),
      'group'      => $group_graphic,
      'conditions' => $conditions_graphic_image,
      'controls'   => array(
        array(
          'keys' => array(
            'image_alt_on' => $k_pre . 'anchor_graphic_image_alt_enable',
          ),
          'type'    => 'checkbox-list',
          'label'   => __( 'Enable', '__x__' ),
          'options' => array(
            'list' => array(
              array( 'key' => 'image_alt_on', 'label' => __( 'Secondary', '__x__' ) ),
            ),
          ),
        ),
        array(
          'key'       => $k_pre . 'anchor_graphic_image_src_alt',
          'type'      => 'image-source',
          'label'     => __( 'Source', '__x__' ),
          'condition' => array( $k_pre . 'anchor_graphic_image_alt_enable' => true ),
          'options'   => array(
            'height' => '4',
          ),
        ),
      ),
    );

    $graphic_controls_image_menu_item = NULL;

  } else {

    $graphic_control_icon_primary                  = NULL;
    $graphic_control_icon_secondary                = NULL;
    $graphic_control_image_primary_non_menu_item   = NULL;
    $graphic_control_image_secondary_non_menu_item = NULL;
    $graphic_controls_image_menu_item              = array(
      'keys' => array(
        'graphic_image_retina'     => $k_pre . 'anchor_graphic_image_retina',
        'graphic_image_alt_enable' => $k_pre . 'anchor_graphic_image_alt_enable',
      ),
      'type'       => 'checkbox-list',
      'label'      => __( 'Image Options', '__x__' ),
      'conditions' => $conditions_graphic_image,
      'options'    => array(
        'list' => array(
          array( 'key' => 'graphic_image_retina',     'label' => __( 'Retina Ready', '__x__' ), 'half' => true ),
          array( 'key' => 'graphic_image_alt_enable', 'label' => __( 'Secondary', '__x__' ),    'half' => true ),
        ),
      ),
    );

  }

  if ( $has_template ) {
    $controls = array_merge(
      $controls,
      array(
        array(
          'type'       => 'group',
          'title'      => __( $t_pre . ' Graphic Setup', '__x__' ),
          'group'      => $group_graphic,
          'conditions' => $conditions,
          'controls'   => array(
            array(
              'key'     => $k_pre . 'anchor_graphic',
              'type'    => 'choose',
              'label'   => __( 'Enable', '__x__' ),
              'options' => array(
                'choices' => array(
                  array( 'value' => false, 'label' => __( 'Off', '__x__' ) ),
                  array( 'value' => true,  'label' => __( 'On', '__x__' ) ),
                ),
              ),
            ),
            array(
              'key'        => $k_pre . 'anchor_graphic_type',
              'type'       => 'choose',
              'label'      => __( 'Type', '__x__' ),
              'conditions' => $conditions_graphic_main,
              'options'    => array(
                'choices'  => $options_anchor_graphic_type_choices,
              ),
            ),
            $graphic_controls_image_menu_item,
          ),
        ),
      ),
      x_control_margin( array(
        'k_pre'     => $k_pre . 'anchor_graphic',
        't_pre'     => __( $t_pre . ' Graphic', '__x__' ),
        'group'     => $group_graphic,
        'condition' => $conditions_graphic_main,
      ) ),


      // Icon
      // ----

      array(
        array(
          'type'       => 'group',
          'title'      => __( $t_pre . ' Graphic Icon', '__x__' ),
          'group'      => $group_graphic,
          'conditions' => $conditions_graphic_icon,
          'controls'   => array(
            array(
              'type'     => 'group',
              'title'    => __( 'Font Size &amp; Secondary', '__x__' ),
              'controls' => array(
                array(
                  'key'     => $k_pre . 'anchor_graphic_icon_font_size',
                  'type'    => 'unit',
                  'options' => array(
                    'available_units' => array( 'px', 'em', 'rem' ),
                    'fallback_value'  => '1em',
                  ),
                ),
                array(
                  'keys' => array(
                    'icon_alt_on' => $k_pre . 'anchor_graphic_icon_alt_enable',
                  ),
                  'type'    => 'checkbox-list',
                  'options' => array(
                    'list' => array(
                      array( 'key' => 'icon_alt_on', 'label' => __( 'Secondary', '__x__' ) ),
                    ),
                  ),
                ),
              ),
            ),
            array(
              'type'     => 'group',
              'title'    => __( 'Width &amp; Height', '__x__' ),
              'controls' => array(
                array(
                  'key'     => $k_pre . 'anchor_graphic_icon_width',
                  'type'    => 'unit',
                  'options' => $options_anchor_graphic_and_sub_indicator_width_and_height,
                ),
                array(
                  'key'     => $k_pre . 'anchor_graphic_icon_height',
                  'type'    => 'unit',
                  'options' => $options_anchor_graphic_and_sub_indicator_width_and_height,
                ),
              ),
            ),
            $graphic_control_icon_primary,
            $graphic_control_icon_secondary,
            array(
              'keys' => array(
                'value' => $k_pre . 'anchor_graphic_icon_color',
                'alt'   => $k_pre . 'anchor_graphic_icon_color_alt',
              ),
              'type'      => 'color',
              'label'     => __( 'Color', '__x__' ),
              'options'   => array(
                'label'     => __( 'Base', '__x__' ),
                'alt_label' => __( 'Interaction', '__x__' ),
              ),
            ),
          ),
        ),
      ),
      x_control_text_shadow( array(
        'k_pre'     => $k_pre . 'anchor_graphic_icon',
        't_pre'     => __( $t_pre . ' Graphic Icon', '__x__' ),
        'group'     => $group_graphic,
        'condition' => $conditions_graphic_icon,
        'alt_color' => true,
        'options'   => array(
          'color' => array(
            'label'     => __( 'Base', '__x__' ),
            'alt_label' => __( 'Interaction', '__x__' ),
          ),
        ),
      ) ),


      // Image
      // -----

      array(
        $graphic_control_image_primary_non_menu_item,
        $graphic_control_image_secondary_non_menu_item,
      )
    );


    // Toggle
    // ------

    if ( $type === 'toggle' ) {
      $controls = array_merge(
        $controls,
        x_controls_toggle( array(
          't_pre'     => __( $t_pre . ' Graphic', '__x__' ),
          'group'     => $group_graphic,
          'condition' => $conditions_graphic_toggle,
        ) )
      );
    }
  }


  // Controls - Sub Indicator
  // ------------------------

  if ( $has_template && $type === 'menu-item' ) {
    $controls = array_merge(
      $controls,
      array(
        array(
          'type'     => 'group',
          'title'    => __( $t_pre . ' Sub Indicator Setup', '__x__' ),
          'group'    => $group_sub_indicator,
          'controls' => array(
            array(
              'key'     => $k_pre . 'anchor_sub_indicator',
              'type'    => 'choose',
              'label'   => __( 'Enable', '__x__' ),
              'options' => array(
                'choices' => array(
                  array( 'value' => false, 'label' => __( 'Off', '__x__' ) ),
                  array( 'value' => true,  'label' => __( 'On', '__x__' ) ),
                ),
              ),
            ),
            array(
              'key'        => $k_pre . 'anchor_sub_indicator_font_size',
              'type'       => 'slider',
              'label'      => __( 'Font Size', '__x__' ),
              'conditions' => $conditions_sub_indicator,
              'options'    => array(
                'available_units' => array( 'px', 'em', 'rem' ),
                'fallback_value'  => '1em',
              ),
            ),
            array(
              'type'       => 'group',
              'title'      => __( 'Width &amp; Height', '__x__' ),
              'conditions' => $conditions_sub_indicator,
              'controls'   => array(
                array(
                  'key'     => $k_pre . 'anchor_sub_indicator_width',
                  'type'    => 'unit',
                  'options' => $options_anchor_graphic_and_sub_indicator_width_and_height,
                ),
                array(
                  'key'     => $k_pre . 'anchor_sub_indicator_height',
                  'type'    => 'unit',
                  'options' => $options_anchor_graphic_and_sub_indicator_width_and_height,
                ),
              ),
            ),
            array(
              'key'        => $k_pre . 'anchor_sub_indicator_icon',
              'type'       => 'icon',
              'label'      => __( 'Icon', '__x__' ),
              'conditions' => $conditions_sub_indicator,
            ),
            array(
              'keys' => array(
                'value' => $k_pre . 'anchor_sub_indicator_color',
                'alt'   => $k_pre . 'anchor_sub_indicator_color_alt',
              ),
              'type'       => 'color',
              'label'      => __( 'Color', '__x__' ),
              'conditions' => $conditions_sub_indicator,
              'options'    => array(
                'label'     => __( 'Base', '__x__' ),
                'alt_label' => __( 'Interaction', '__x__' ),
              ),
            ),
          ),
        ),
      ),
      x_control_margin( array(
        'k_pre'     => $k_pre . 'anchor_sub_indicator',
        't_pre'     => __( $t_pre . ' Sub Indicator', '__x__' ),
        'group'     => $group_sub_indicator,
        'condition' => $conditions_sub_indicator,
      ) ),
      x_control_text_shadow( array(
        'k_pre'     => $k_pre . 'anchor_sub_indicator',
        't_pre'     => __( $t_pre . ' Sub Indicator', '__x__' ),
        'group'     => $group_sub_indicator,
        'condition' => $conditions_sub_indicator,
        'alt_color' => true,
        'options'   => array(
          'color' => array(
            'label'     => __( 'Base', '__x__' ),
            'alt_label' => __( 'Interaction', '__x__' ),
          ),
        ),
      ) )
    );
  }


  // Controls - Interactions
  // -----------------------

  if ( $has_template ) {
    $controls = array_merge(
      $controls,
      array(
        array(
          'type'       => 'group',
          'title'      => __( $t_pre . ' Interactions Setup', '__x__' ),
          'group'      => $group_interactions,
          'conditions' => $conditions,
          'controls'   => array(
            // array(
            //   'key'     => $k_pre . 'anchor_interaction_duration',
            //   'type'    => 'choose',
            //   'label'   => __( 'Interaction Duration', '__x__' ),
            //   'options' => array(
            //     'choices' => array(
            //       array( 'value' => false, 'label' => __( 'Off', '__x__' ) ),
            //       array( 'value' => true,  'label' => __( 'On', '__x__' ) ),
            //     ),
            //   ),
            // ),
            array(
              'key'     => $k_pre . 'anchor_text_interaction',
              'type'    => 'select',
              'label'   => __( 'Text', '__x__' ),
              'options' => array(
                'choices' => array(
                  array( 'value' => 'none',                  'label' => __( 'None', '__x__' )         ),
                  array( 'value' => 'x-anchor-slide-top',    'label' => __( 'Slide Top', '__x__' )    ),
                  array( 'value' => 'x-anchor-slide-left',   'label' => __( 'Slide Left', '__x__' )   ),
                  array( 'value' => 'x-anchor-slide-right',  'label' => __( 'Slide Right', '__x__' )  ),
                  array( 'value' => 'x-anchor-slide-bottom', 'label' => __( 'Slide Bottom', '__x__' ) ),
                  array( 'value' => 'x-anchor-scale-up',     'label' => __( 'Scale Up', '__x__' )     ),
                  array( 'value' => 'x-anchor-scale-down',   'label' => __( 'Scale Down', '__x__' )   ),
                  array( 'value' => 'x-anchor-flip-x',       'label' => __( 'Flip X', '__x__' )       ),
                  array( 'value' => 'x-anchor-flip-y',       'label' => __( 'Flip Y', '__x__' )       ),
                )
              ),
            ),
            array(
              'key'     => $k_pre . 'anchor_graphic_interaction',
              'type'    => 'select',
              'label'   => __( 'Graphic', '__x__' ),
              'options' => array(
                'choices' => array(
                  array( 'value' => 'none',                'label' => __( 'None', '__x__' )       ),
                  array( 'value' => 'x-anchor-scale-up',   'label' => __( 'Scale Up', '__x__' )   ),
                  array( 'value' => 'x-anchor-scale-down', 'label' => __( 'Scale Down', '__x__' ) ),
                  array( 'value' => 'x-anchor-flip-x',     'label' => __( 'Flip X', '__x__' )     ),
                  array( 'value' => 'x-anchor-flip-y',     'label' => __( 'Flip Y', '__x__' )     ),
                )
              ),
            ),
            array(
              'keys' => array(
                'primary_particle'   => $k_pre . 'anchor_primary_particle',
                'secondary_particle' => $k_pre . 'anchor_secondary_particle',
              ),
              'type'    => 'checkbox-list',
              'label'   => __( 'Particles', '__x__' ),
              'options' => array(
                'list' => array(
                  array( 'key' => 'primary_particle',   'label' => __( 'Primary', '__x__' ),   'half' => true ),
                  array( 'key' => 'secondary_particle', 'label' => __( 'Secondary', '__x__' ), 'half' => true ),
                ),
              ),
            ),
          ),
        ),
      ),
      x_controls_particle( array(
        't_pre'     => __( 'Primary', '__x__' ),
        'k_pre'     => $k_pre . 'anchor_primary',
        'group'     => $group_interactions,
        'condition' => $conditions_primary_particle,
      ) ),
      x_controls_particle( array(
        't_pre'     => __( 'Secondary', '__x__' ),
        'k_pre'     => $k_pre . 'anchor_secondary',
        'group'     => $group_interactions,
        'condition' => $conditions_secondary_particle,
      ) )
    );
  }

  return $controls;

}



// Control Groups
// =============================================================================

function x_control_groups_anchor( $settings = array() ) {

  // Setup
  // -----
  // 01. Available types:
  //     -- 'button'
  //     -- 'menu-item'
  //     -- 'toggle'

  $group            = ( isset( $settings['group'] )            ) ? $settings['group']            : 'anchor';
  $group_title      = ( isset( $settings['group_title'] )      ) ? $settings['group_title']      : __( 'Menu Item', '__x__' );
  $type             = ( isset( $settings['type'] )             ) ? $settings['type']             : 'menu-item'; // 01
  $has_template     = ( isset( $settings['has_template'] )     ) ? $settings['has_template']     : true;
  $has_link_control = ( isset( $settings['has_link_control'] ) ) ? $settings['has_link_control'] : false;

  $control_groups = array(
    $group                    => array( 'title' => $group_title ),
    $group . ':setup'         => array( 'title' => __( 'Setup', '__x__' ) ),
    $group . ':design'        => array( 'title' => __( 'Design', '__x__' ) ),
    $group . ':text'          => array( 'title' => __( 'Text', '__x__' ) ),
    $group . ':graphic'       => array( 'title' => __( 'Graphic', '__x__' ) ),
    $group . ':sub_indicator' => array( 'title' => __( 'Sub Indicator', '__x__' ) ),
    $group . ':interactions'  => array( 'title' => __( 'Interactions', '__x__' ) ),
  );

  if ( ! $has_template ) {
    unset( $control_groups[$group . ':setup'] );
    unset( $control_groups[$group . ':graphic'] );
  }

  if ( $type !== 'menu-item' ) {
    unset( $control_groups[$group . ':sub_indicator'] );
  }

  return $control_groups;

}



// Values
// =============================================================================

function x_values_anchor( $settings = array() ) {

  // Setup
  // -----
  // 01. Available types:
  //     -- 'button'
  //     -- 'menu-item'
  //     -- 'toggle'

  $k_pre            = ( isset( $settings['k_pre'] )            ) ? $settings['k_pre'] . '_'      : '';
  $type             = ( isset( $settings['type'] )             ) ? $settings['type']             : 'menu-item'; // 01
  $has_template     = ( isset( $settings['has_template'] )     ) ? $settings['has_template']     : true;
  $has_link_control = ( isset( $settings['has_link_control'] ) ) ? $settings['has_link_control'] : false;


  // Values
  // ------
  // 01. Will not change per module. Meant to be used to conditionally load
  //     output for templates and associated styles.

  $values = array(

    $k_pre . 'anchor_type'                           => x_module_value( $type, 'all' ),             // 01
    $k_pre . 'anchor_has_template'                   => x_module_value( $has_template, 'all' ),     // 01
    $k_pre . 'anchor_has_link_control'               => x_module_value( $has_link_control, 'all' ), // 01

    $k_pre . 'anchor_base_font_size'                 => x_module_value( '1em', 'style' ),
    $k_pre . 'anchor_width'                          => x_module_value( 'auto', 'style' ),
    $k_pre . 'anchor_height'                         => x_module_value( 'auto', 'style' ),
    $k_pre . 'anchor_min_width'                      => x_module_value( '0px', 'style' ),
    $k_pre . 'anchor_min_height'                     => x_module_value( '0px', 'style' ),
    $k_pre . 'anchor_max_width'                      => x_module_value( 'none', 'style' ),
    $k_pre . 'anchor_max_height'                     => x_module_value( 'none', 'style' ),
    $k_pre . 'anchor_bg_color'                       => x_module_value( 'transparent', 'style:color' ),
    $k_pre . 'anchor_bg_color_alt'                   => x_module_value( 'transparent', 'style:color' ),

    $k_pre . 'anchor_margin'                         => x_module_value( '0em', 'style' ),
    $k_pre . 'anchor_padding'                        => x_module_value( '0.575em 0.85em 0.575em 0.85em', 'style' ),
    $k_pre . 'anchor_border_width'                   => x_module_value( '0px', 'style' ),
    $k_pre . 'anchor_border_style'                   => x_module_value( 'none', 'style' ),
    $k_pre . 'anchor_border_color'                   => x_module_value( 'transparent', 'style:color' ),
    $k_pre . 'anchor_border_color_alt'               => x_module_value( 'transparent', 'style:color' ),
    $k_pre . 'anchor_border_radius'                  => x_module_value( '0em', 'style' ),

    $k_pre . 'anchor_box_shadow_dimensions'          => x_module_value( '0em 0em 0em 0em', 'style' ),
    $k_pre . 'anchor_box_shadow_color'               => x_module_value( 'transparent', 'style:color' ),
    $k_pre . 'anchor_box_shadow_color_alt'           => x_module_value( 'transparent', 'style:color' ),

    $k_pre . 'anchor_text_margin'                    => x_module_value( '5px', 'style' ),
    $k_pre . 'anchor_primary_font_family'            => x_module_value( 'fw_fallback', 'style:font-family' ),
    $k_pre . 'anchor_primary_font_weight'            => x_module_value( 'fw_fallback:400', 'style:font-weight' ),
    $k_pre . 'anchor_primary_font_size'              => x_module_value( '1em', 'style' ),
    $k_pre . 'anchor_primary_letter_spacing'         => x_module_value( '0em', 'style' ),
    $k_pre . 'anchor_primary_line_height'            => x_module_value( '1', 'style' ),
    $k_pre . 'anchor_primary_font_style'             => x_module_value( 'normal', 'style' ),
    $k_pre . 'anchor_primary_text_align'             => x_module_value( 'none', 'style' ),
    $k_pre . 'anchor_primary_text_decoration'        => x_module_value( 'none', 'style' ),
    $k_pre . 'anchor_primary_text_transform'         => x_module_value( 'none', 'style' ),
    $k_pre . 'anchor_primary_text_color'             => x_module_value( 'rgba(0, 0, 0, 0.35)', 'style:color' ),
    $k_pre . 'anchor_primary_text_color_alt'         => x_module_value( 'rgba(0, 0, 0, 0.75)', 'style:color' ),
    $k_pre . 'anchor_primary_text_shadow_dimensions' => x_module_value( '0px 0px 0px', 'style' ),
    $k_pre . 'anchor_primary_text_shadow_color'      => x_module_value( 'transparent', 'style:color' ),
    $k_pre . 'anchor_primary_text_shadow_color_alt'  => x_module_value( 'transparent', 'style:color' ),

  );

  if ( $has_link_control ) {
    $values = array_merge(
      $values,
      array(
        $k_pre . 'anchor_href'     => x_module_value( '#', 'markup' ),
        $k_pre . 'anchor_info'     => x_module_value( false, 'markup' ),
        $k_pre . 'anchor_blank'    => x_module_value( false, 'markup' ),
        $k_pre . 'anchor_nofollow' => x_module_value( false, 'markup' ),
      )
    );
  }

  if ( $has_template ) {

    $values = array_merge(
      $values,
      array(

        $k_pre . 'anchor_flex_direction'                   => x_module_value( 'row', 'style' ),
        $k_pre . 'anchor_flex_wrap'                        => x_module_value( false, 'style' ),
        $k_pre . 'anchor_flex_justify'                     => x_module_value( 'center', 'style' ),
        $k_pre . 'anchor_flex_align'                       => x_module_value( 'center', 'style' ),

        $k_pre . 'anchor_text'                             => x_module_value( true, 'all' ),
        $k_pre . 'anchor_text_overflow'                    => x_module_value( false, 'style' ),
        $k_pre . 'anchor_text_reverse'                     => x_module_value( false, 'all' ),
        $k_pre . 'anchor_text_spacing'                     => x_module_value( '0.35em', 'style' ),

        $k_pre . 'anchor_secondary_font_family'            => x_module_value( 'fw_fallback', 'style:font-family' ),
        $k_pre . 'anchor_secondary_font_weight'            => x_module_value( 'fw_fallback:400', 'style:font-weight' ),
        $k_pre . 'anchor_secondary_font_size'              => x_module_value( '0.75em', 'style' ),
        $k_pre . 'anchor_secondary_letter_spacing'         => x_module_value( '0em', 'style' ),
        $k_pre . 'anchor_secondary_line_height'            => x_module_value( '1', 'style' ),
        $k_pre . 'anchor_secondary_font_style'             => x_module_value( 'normal', 'style' ),
        $k_pre . 'anchor_secondary_text_align'             => x_module_value( 'none', 'style' ),
        $k_pre . 'anchor_secondary_text_decoration'        => x_module_value( 'none', 'style' ),
        $k_pre . 'anchor_secondary_text_transform'         => x_module_value( 'none', 'style' ),
        $k_pre . 'anchor_secondary_text_color'             => x_module_value( 'rgba(0, 0, 0, 0.35)', 'style:color' ),
        $k_pre . 'anchor_secondary_text_color_alt'         => x_module_value( 'rgba(0, 0, 0, 0.75)', 'style:color' ),
        $k_pre . 'anchor_secondary_text_shadow_dimensions' => x_module_value( '0px 0px 0px', 'style' ),
        $k_pre . 'anchor_secondary_text_shadow_color'      => x_module_value( 'transparent', 'style:color' ),
        $k_pre . 'anchor_secondary_text_shadow_color_alt'  => x_module_value( 'transparent', 'style:color' ),

        $k_pre . 'anchor_graphic'                             => x_module_value( false, 'all' ),
        $k_pre . 'anchor_graphic_type'                        => x_module_value( 'icon', 'all' ),
        $k_pre . 'anchor_graphic_margin'                      => x_module_value( '5px', 'style' ),
        $k_pre . 'anchor_graphic_icon_alt_enable'             => x_module_value( false, 'markup' ),
        $k_pre . 'anchor_graphic_icon_font_size'              => x_module_value( '1.25em', 'style' ),
        $k_pre . 'anchor_graphic_icon_width'                  => x_module_value( '1em', 'style' ),
        $k_pre . 'anchor_graphic_icon_height'                 => x_module_value( '1em', 'style' ),
        $k_pre . 'anchor_graphic_icon_color'                  => x_module_value( 'rgba(0, 0, 0, 0.35)', 'style:color' ),
        $k_pre . 'anchor_graphic_icon_color_alt'              => x_module_value( 'rgba(0, 0, 0, 0.75)', 'style:color' ),
        $k_pre . 'anchor_graphic_icon_text_shadow_dimensions' => x_module_value( '0px 0px 0px', 'style' ),
        $k_pre . 'anchor_graphic_icon_text_shadow_color'      => x_module_value( 'transparent', 'style:color' ),
        $k_pre . 'anchor_graphic_icon_text_shadow_color_alt'  => x_module_value( 'transparent', 'style:color' ),
        $k_pre . 'anchor_graphic_image_retina'                => x_module_value( true, 'markup' ),
        $k_pre . 'anchor_graphic_image_alt_enable'            => x_module_value( false, 'markup' ),

        // $k_pre . 'anchor_interaction_duration'             => x_module_value( true, 'markup' ),
        $k_pre . 'anchor_text_interaction'                 => x_module_value( 'none', 'markup' ),
        $k_pre . 'anchor_graphic_interaction'              => x_module_value( 'none', 'markup' ),
        $k_pre . 'anchor_primary_particle'                 => x_module_value( false, 'all' ),
        $k_pre . 'anchor_secondary_particle'               => x_module_value( false, 'all' ),

      ),

      x_values_particle( array( 'k_pre' => $k_pre . 'anchor_primary' ) ),
      x_values_particle( array( 'k_pre' => $k_pre . 'anchor_secondary' ) )

    );

    if ( $type === 'menu-item' ) {
      $values = array_merge(
        $values,
        array(
          $k_pre . 'anchor_text_primary_content'                 => x_module_value( 'on', 'all' ),
          $k_pre . 'anchor_text_secondary_content'               => x_module_value( '', 'all' ),
          $k_pre . 'anchor_sub_indicator'                        => x_module_value( true, 'all' ),
          $k_pre . 'anchor_sub_indicator_font_size'              => x_module_value( '1em', 'style' ),
          $k_pre . 'anchor_sub_indicator_width'                  => x_module_value( 'auto', 'style' ),
          $k_pre . 'anchor_sub_indicator_height'                 => x_module_value( 'auto', 'style' ),
          $k_pre . 'anchor_sub_indicator_icon'                   => x_module_value( 'angle-down', 'markup' ),
          $k_pre . 'anchor_sub_indicator_color'                  => x_module_value( 'rgba(0, 0, 0, 0.35)', 'style:color' ),
          $k_pre . 'anchor_sub_indicator_color_alt'              => x_module_value( 'rgba(0, 0, 0, 0.75)', 'style:color' ),
          $k_pre . 'anchor_sub_indicator_margin'                 => x_module_value( '5px', 'style' ),
          $k_pre . 'anchor_sub_indicator_text_shadow_dimensions' => x_module_value( '0px 0px 0px', 'style' ),
          $k_pre . 'anchor_sub_indicator_text_shadow_color'      => x_module_value( 'transparent', 'style:color' ),
          $k_pre . 'anchor_sub_indicator_text_shadow_color_alt'  => x_module_value( 'transparent', 'style:color' ),
        )
      );
    }

    if ( $type !== 'menu-item' ) {
      $values = array_merge(
        $values,
        array(
          $k_pre . 'anchor_text_primary_content'   => x_module_value( __( 'Learn More', '__x__' ), 'all' ),
          $k_pre . 'anchor_text_secondary_content' => x_module_value( '', 'all' ),
          $k_pre . 'anchor_graphic_icon'           => x_module_value( 'hand-pointer-o', 'markup' ),
          $k_pre . 'anchor_graphic_icon_alt'       => x_module_value( 'hand-spock-o', 'markup' ),
          $k_pre . 'anchor_graphic_image_width'    => x_module_value( 48, 'markup' ),
          $k_pre . 'anchor_graphic_image_height'   => x_module_value( 48, 'markup' ),
          $k_pre . 'anchor_graphic_image_src'      => x_module_value( '', 'markup' ),
          $k_pre . 'anchor_graphic_image_src_alt'  => x_module_value( '', 'markup' ),
        )
      );
    }

    if ( $type === 'toggle' ) {
      $values = array_merge(
        $values,
        x_values_toggle()
      );
    }

  }

  if ( ! $has_template ) {

    if ( $type === 'button' ) {
      $values = array_merge(
        $values,
        array(
          $k_pre . 'anchor_text' => x_module_value( true, 'all' ),
        )
      );
    }

  }


  // Returned Value
  // --------------

  return x_bar_mixin_values( $values, $settings );

}
