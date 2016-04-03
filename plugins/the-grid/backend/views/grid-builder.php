<?php 
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}

// set prefix for metabox fields
$prefix = TG_PREFIX;

function tg_css_multiple_input($prefix,$type,$values,$units,$def = '', $min = '', $max = '') {
	
	$html = null;
	
	foreach($values as $value) {
		if (count($values) > 1) {
			$html .= '<span class="tg-filter-tooltip-holder">';
				$html .= '<input type="number" class="tomb-text number mini" name="'.$prefix.$value.'" value="'.$def.'" step="1" min="'.$min.'" max="'.$max.'" > ';
				$html .= '<span class="tg-filter-tooltip">'.ucfirst(str_replace('-', ' ',$value)).'</span>';
			$html .= '</span>';
		} else {
			$html .= '<input type="number" class="tomb-text number mini" name="'.$prefix.$value.'" value="'.$def.'" step="1" min="'.$min.'" max="'.$max.'" > ';
		}
	}
	
	$html .= tg_css_unit($prefix,$type, $units);
	
	return $html;
}

function tg_css_unit($prefix,$type, $units) {
	
	$html = '<div class="tomb-select-holder" data-noresult="'. __( 'No results found', 'tg-text-domain' ) .'" data-clear="" style="width:65px;vertical-align:top;margin: 1px;">';
		$html .= '<div class="tomb-select-fake">';
			$html .= '<span class="tomb-select-value">px</span>';
			$html .= '<span class="tomb-select-arrow"><i></i></span>';
		$html .= '</div>';
		$html .= '<select class="tomb-select tg-css-unit" name="'.$prefix.$type.'-unit" data-clear="">';
			foreach($units as $unit) {
				$html .= '<option value="'.$unit.'">'.$unit.'</option>';
			}
		$html .= '</select>';
	$html .= '</div>';
	
	return $html;
}
function css_position($prefix) {
	return array(
		'id'   => $prefix.'position',
		'name' => __( 'Position', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'options' => array(
			'relative' => __( 'Relative', 'tg-text-domain' ),
			'absolute' => __( 'Absolute', 'tg-text-domain' )
		),
		'std' => 'relative',
		'tab' => __( 'Position', 'tg-text-domain' )
	);
}
function css_display($prefix) {
	return array(
		'id'   => $prefix.'display',
		'name' => __( 'Display', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'options' => array(
			'inline-block' => __( 'Inline-block', 'tg-text-domain' ),
			'block' => __( 'Block', 'tg-text-domain' )
		),
		'std' => 'inline-block',
		'tab' => __( 'Position', 'tg-text-domain' ),
		'required' => array(
			array($prefix.'position', '==', 'relative')
		)
	);
}
function css_overflow($prefix) {
	return array(
		'id'   => $prefix.'overflow',
		'name' => __( 'Overflow', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'visible' => __( 'Visible', 'tg-text-domain' ),
			'hidden' => __( 'Hidden', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Position', 'tg-text-domain' )
	);
}
function css_float($prefix) {
	return array(
		'id'   => $prefix.'float',
		'name' => __( 'Float', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'none'  => __( 'None', 'tg-text-domain' ),
			'left'  => __( 'Left', 'tg-text-domain' ),
			'right' => __( 'right', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Position', 'tg-text-domain' ),
		'required' => array(
			array($prefix.'position', '==', 'relative')
		)
	);
}
function css_clear($prefix) {
	return array(
		'id'   => $prefix.'clear',
		'name' => __( 'Clear', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'none'  => __( 'None', 'tg-text-domain' ),
			'left'  => __( 'Left', 'tg-text-domain' ),
			'right' => __( 'right', 'tg-text-domain' ),
			'both'  => __( 'both', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Position', 'tg-text-domain' ),
		'required' => array(
			array($prefix.'position', '==', 'relative')
		)
	);
}
function css_positions($prefix) {
	return array(
		'id'   => $prefix.'positions',
		'name' => __( 'Positions', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'positions',array('top','right','bottom','left'),array('px','em','%'), '', '', ''),
		'tab' => __( 'Position', 'tg-text-domain' ),
		'required' => array(
			array($prefix.'position', '==', 'absolute')
		)
	);
}
function css_margin($prefix) {
	return array(
		'id'   => $prefix.'margin',
		'name' => __( 'Margin', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'margin',array('margin-top','margin-right','margin-bottom','margin-left'),array('px','em','%')),
		'tab' => __( 'Position', 'tg-text-domain' )
	);
}
function css_padding($prefix) {
	return array(
		'id'   => $prefix.'padding',
		'name' => __( 'Paddings', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'padding',array('padding-top','padding-right','padding-bottom','padding-left'),array('px','em','%'), '', 0, ''),
		'tab' => __( 'Position', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-editor-expand"></i>'
	);
}
function css_font_size($prefix) {
	return array(
		'id'   => $prefix.'font-size',
		'name' => __( 'Font Size', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'font-size',array('font-size'),array('px','em'), 13, 0, 120),
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_line_height($prefix) {
	return array(
		'id'   => $prefix.'line-height',
		'name' => __( 'Line Height', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'line-height',array('line-height'),array('px','em'), 16, 0, 150),
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_color($prefix) {
	return array(
		'id' => $prefix . 'color',
		'name' => __('Font Color', 'tg-text-domain'),
		'desc' => '',
		'sub_desc' => '',
		'type' => 'color',
		'rgba' => false,
		'std' => '#444444',
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_font_weight($prefix) {
	return array(
		'id'   => $prefix.'font-weight',
		'name' => __( 'Font Weight', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 120,
		'options' => array(
			'100' => __( '100', 'tg-text-domain' ),
			'200' => __( '200', 'tg-text-domain' ),
			'300' => __( '300', 'tg-text-domain' ),
			'400' => __( '400', 'tg-text-domain' ),
			'500' => __( '500', 'tg-text-domain' ),
			'600' => __( '600', 'tg-text-domain' ),
			'700' => __( '700', 'tg-text-domain' ),
			'800' => __( '800', 'tg-text-domain' ),
			'900' => __( '900', 'tg-text-domain' )
		),
		'std' => '400',
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_font_style($prefix) {
	return array(
		'id'   => $prefix.'font-style',
		'name' => __( 'Font Style', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'normal'  => __( 'Normal', 'tg-text-domain' ),
			'inherit' => __( 'Inherit', 'tg-text-domain' ),
			'initial' => __( 'initial', 'tg-text-domain' ),
			'italic'  => __( 'italic', 'tg-text-domain' ),
			'oblique' => __( 'Oblique', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_text_decoration($prefix) {
	return array(
		'id'   => $prefix.'text-decoration',
		'name' => __( 'Text Decoration', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'none'         => __( 'None', 'tg-text-domain' ),
			'underline'    => __( 'Underline', 'tg-text-domain' ),
			'overline'     => __( 'Overline', 'tg-text-domain' ),
			'line-through' => __( 'Line Through', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_text_transform($prefix) {
	return array(
		'id'   => $prefix.'text-transform',
		'name' => __( 'Text Transform', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'none'       => __( 'None', 'tg-text-domain' ),
			'capitalize' => __( 'Capitalize', 'tg-text-domain' ),
			'uppercase'  => __( 'Uppercase', 'tg-text-domain' ),
			'lowercase'  => __( 'Lowercase', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_text_align($prefix) {
	return array(
		'id'   => $prefix.'text-align',
		'name' => __( 'Text Alignment', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'none'    => __( 'None', 'tg-text-domain' ),
			'center'  => __( 'Center', 'tg-text-domain' ),
			'left'    => __( 'Left', 'tg-text-domain' ),
			'right'   => __( 'Right', 'tg-text-domain' ),
			'justify' => __( 'Justify', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_text_shadow($prefix) {
	return array(
		'id'   => $prefix.'text-shadow',
		'name' => __( 'Text Shadow', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'text-shadow',array('text-shadow-horizontal','text-shadow-vertical','text-shadow-blur'),array('px','em'), '', 0, ''),
		'tab' => __( 'Font', 'tg-text-domain' )
	);
}
function css_text_shadow_color($prefix) {
	return array(
		'id' => $prefix . 'text-shadow-color',
		'name' => __('Text Shadow Color', 'tg-text-domain'),
		'desc' => '',
		'sub_desc' => '',
		'type' => 'color',
		'rgba' => true,
		'std' => 'rgba(0,0,0,0.8)',
		'tab' => __( 'Font', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-editor-paragraph"></i>'
	);
}
function css_border($prefix) {
	return array(
		'id'   => $prefix.'border',
		'name' => __( 'Border', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'border',array('border-top','border-right','border-bottom','border-left'),array('px','em'), '', 0, ''),
		'tab' => __( 'Border', 'tg-text-domain' )
	);
}
function css_border_radius($prefix) {
	return array(
		'id'   => $prefix.'border-radius',
		'name' => __( 'Border Radius', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'border-radius',array('border-top-left-radius','border-top-right-radius','border-bottom-right-radius','border-bottom-left-radius'),array('px','em', '%'), '', 0, ''),
		'tab' => __( 'Border', 'tg-text-domain' )
	);
}
function css_border_style($prefix) {
	return array(
		'id'   => $prefix.'border-style',
		'name' => __( 'Border Style', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 160,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'none'   => __( 'None', 'tg-text-domain' ),
			'solid'  => __( 'Solid', 'tg-text-domain' ),
			'dotted' => __( 'Dotted', 'tg-text-domain' ),
			'dashed' => __( 'Dashed', 'tg-text-domain' ),
			'double' => __( 'Double', 'tg-text-domain' ),
			'groove' => __( 'Groove', 'tg-text-domain' ),
			'ridge'  => __( 'Ridge', 'tg-text-domain' ),
			'inset'  => __( 'inset', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Border', 'tg-text-domain' )
	);
}
function css_border_color($prefix) {
	return array(
		'id' => $prefix . 'border-color',
		'name' => __('Border Color', 'tg-text-domain'),
		'desc' => '',
		'sub_desc' => '',
		'type' => 'color',
		'rgba' => true,
		'std' => '',
		'tab' => __( 'Border', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-align-center"></i>'
	);
}
function css_shadow($prefix) {
	return array(
		'id'   => $prefix.'shadow',
		'name' => __( 'Shadow', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'box-shadow',array('box-shadow-horizontal','box-shadow-vertical','box-shadow-blur','box-shadow-size'),array('px','rem')),
		'tab' => __( 'Shadow', 'tg-text-domain' )
	);
}
function css_shadow_color($prefix) {
	return array(
		'id' => $prefix . 'box-shadow-color',
		'name' => __('Shadow Color', 'tg-text-domain'),
		'desc' => '',
		'sub_desc' => '',
		'type' => 'color',
		'rgba' => true,
		'std' => 'rgba(0,0,0,0.8)',
		'tab' => __( 'Shadow', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-format-gallery"></i>'
	);
}
function css_background_color($prefix) {
	return array(
		'id' => $prefix . 'background-color',
		'name' => __('Background Color', 'tg-text-domain'),
		'desc' => '',
		'sub_desc' => '',
		'type' => 'color',
		'rgba' => true,
		'std' => '',
		'tab' => __( 'BG', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-format-image"></i>'
	);
}
function css_background_image($prefix) {
	return array(
		'id' => $prefix . 'background-image',
		'name' => __('Background Image', 'tg-text-domain'),
		'desc' => '',
		'sub_desc' => '',
		'type' => 'image',
		'frame_title' => __( 'Select a background image', 'tg-text-domain' ),
		'frame_button' => __( 'Add background image', 'tg-text-domain' ),
		'button_upload' => __( 'Add image', 'tg-text-domain' ),
		'button_remove' => __( 'Remove', 'tg-text-domain' ),
		'std' => '',
		'tab' => __( 'BG', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-format-image"></i>'
	);
}
function css_background_size($prefix) {
	return array(
		'id'   => $prefix.'background-size',
		'name' => __( 'Background Size', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 121,
		'placeholder' => __( 'Select size', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'initial' => __( 'Initial', 'tg-text-domain' ),
			'contain' => __( 'Contain', 'tg-text-domain' ),
			'cover'   => __( 'Cover', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'BG', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-align-center"></i>'
	);
}
function css_background_repeat($prefix) {
	return array(
		'id'   => $prefix.'background-repeat',
		'name' => __( 'Background Repeat', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 121,
		'placeholder' => __( 'Select repeat', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'no-repeat' => __( 'No Repeat', 'tg-text-domain' ),
			'repeat'    => __( 'Repeat', 'tg-text-domain' ),
			'repeat-x'  => __( 'Repeat X', 'tg-text-domain' ),
			'repeat-Y'  => __( 'Repeat Y', 'tg-text-domain' ),
			'round'     => __( 'Round', 'tg-text-domain' ),
			'space'     => __( 'Space', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'BG', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-align-center"></i>'
	);
}
function css_background_position_x($prefix) {
	return array(
		'id'   => $prefix.'background-position-x',
		'name' => __( 'Background Position X', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'background-position-x',array('background-position-x'),array('px','%'), '', '', ''),
		'tab' => __( 'BG', 'tg-text-domain' )
	);
}
function css_background_position_y($prefix) {
	return array(
		'id'   => $prefix.'background-position-y',
		'name' => __( 'Background Position Y', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'custom',
		'options' => tg_css_multiple_input($prefix,'background-position-y',array('background-position-y'),array('px','%'), '', '', ''),
		'tab' => __( 'BG', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-format-image"></i>'
	);
}
function css_visibility($prefix) {
	return array(
		'id'   => $prefix.'visibility',
		'name' => __( 'Visibility', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'select',
		'width' => 137,
		'placeholder' => __( 'Select a value', 'tg-text-domain' ),
		'clear' => true,
		'options' => array(
			'visible'  => __( 'Visible', 'tg-text-domain' ),
			'hidden'   => __( 'Hidden', 'tg-text-domain' )
		),
		'std' => '',
		'tab' => __( 'Visibility', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-visibility"></i>'
	);
}
function css_opacity($prefix) {
	return array(
		'id'   => $prefix.'opacity',
		'name' => __( 'Opacity', 'tg-text-domain' ),
		'desc' => '',
		'sub_desc' =>  '',
		'type' => 'slider',
		'label' => '',
		'min' => 0,
		'max' => 1,
		'step' => 0.01,
		'sign' => '',
		'std' => 1,
		'tab' => __( 'Visibility', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-visibility"></i>'
	);
}
function css_visibility_desc($prefix) {
	return array(
		'id'   => $prefix.'visibility_desc',
		'name' => '',
		'options' => '<br><p>'.__( 'Visibility and opacity values will not be applied directly on the item otherwise you will not be able to see it or select it anymore.', 'tg-text-domain' ).'</p>',
		'sub_desc' =>  '',
		'type' => 'custom',
		'tab' => __( 'Visibility', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-visibility"></i>'
	);
}
function css_custom($prefix) {
	return array(
		'id'   => $prefix.'custom-rules',
		'name' => __( 'Custom rules', 'tg-text-domain' ),
		'options' => '',
		'sub_desc' =>  '',
		'type' => 'textarea',
		'cols' => 80,
		'rows' => 15,
		'tab' => __( 'Custom', 'tg-text-domain' )
	);
}
function css_custom_desc($prefix) {
	return array(
		'id'   => $prefix.'custom_desc',
		'name' => '',
		'options' => __( 'In this section, you can add your custom css rules if you need to extend the basis rules available in other panels.', 'tg-text-domain' ).'<br><strong>* '.__( 'e.g.: margin-left: auto; margin-right: auto; width: 150px;', 'tg-text-domain' ).'</strong>',
		'sub_desc' =>  '',
		'type' => 'custom',
		'tab' => __( 'Custom', 'tg-text-domain' ),
		'tab_icon' => '<i class="tomb-icon dashicons dashicons-admin-appearance"></i>'
	);
}

	
// build item styles idle state
$idle_pre = 'element_idle_';
$element_idle = array(
		'id'    => 'element_styles_idle',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'class' => 'tomb-tab-vertical',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			css_position($idle_pre),
			css_position($idle_pre),
			css_display($idle_pre),
			css_overflow($idle_pre),
			css_float($idle_pre),
			css_clear($idle_pre),
			css_positions($idle_pre),
			css_margin($idle_pre),
			css_padding($idle_pre),
			css_visibility($idle_pre),
			css_opacity($idle_pre),
			css_visibility_desc($idle_pre),
			css_font_size($idle_pre),
			css_line_height($idle_pre),
			css_color($idle_pre),
			css_font_weight($idle_pre),
			css_font_style($idle_pre),
			css_text_decoration($idle_pre),
			css_text_transform($idle_pre),
			css_text_align($idle_pre),
			css_text_shadow($idle_pre),
			css_text_shadow_color($idle_pre),
			css_border($idle_pre),
			css_border_radius($idle_pre),
			css_border_style($idle_pre),
			css_border_color($idle_pre),
			css_shadow($idle_pre),
			css_shadow_color($idle_pre),
			css_background_color($idle_pre),
			css_background_image($idle_pre),
			css_background_size($idle_pre),
			css_background_repeat($idle_pre),
			css_background_position_x($idle_pre),
			css_background_position_y($idle_pre),
			css_custom($idle_pre),
			css_custom_desc($idle_pre)
	),
);

// build item styles hover state
$hover_pre = 'element_hover_';
$element_hover = array(
		'id'    => 'element_styles_hover',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'class' => 'tomb-tab-vertical',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(	
			css_visibility($hover_pre),
			css_opacity($hover_pre),
			css_visibility_desc($hover_pre),
			css_font_size($hover_pre),
			css_line_height($hover_pre),
			css_color($hover_pre),
			css_font_weight($hover_pre),
			css_font_style($hover_pre),
			css_text_decoration($hover_pre),
			css_text_transform($hover_pre),
			css_text_align($hover_pre),
			css_text_shadow($hover_pre),
			css_text_shadow_color($hover_pre),
			css_border($hover_pre),
			css_border_radius($hover_pre),
			css_border_style($hover_pre),
			css_border_color($hover_pre),
			css_shadow($hover_pre),
			css_shadow_color($hover_pre),
			css_background_color($hover_pre),
			css_background_image($hover_pre),
			css_background_size($hover_pre),
			css_background_repeat($hover_pre),
			css_background_position_x($hover_pre),
			css_background_position_y($hover_pre),
			css_custom($hover_pre),
			css_custom_desc($hover_pre)	
	),
);

// build item source
$element_source = array(
		'id'    => 'element_source',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(	
			array(
				'id'   => 'element_source_type',
				'name' => __( 'Source', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'select',
				'width' => 160,
				'options' => array(
					'post' => __( 'Post Data', 'tg-text-domain' ),
					'icon' => __( 'Icon', 'tg-text-domain' ),
					'html' => __( 'Text/html tags', 'tg-text-domain' )
				),
				'std' => 'post'
			),
			array(
				'id'   => 'element_post_content',
				'name' => __( 'Content', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'select',
				'width' => 160,
				'options' => array(
					'title'     => __( 'Title', 'tg-text-domain' ),
					'excerpt'   => __( 'Excerpt', 'tg-text-domain' ),
					'date'      => __( 'Date', 'tg-text-domain' ),
					'author'    => __( 'Author Name', 'tg-text-domain' ),
					'comment'   => __( 'Nb of comment', 'tg-text-domain' ),
					'post-like' => __( 'Nb of like', 'tg-text-domain' ),
					'terms'     => __( 'Terms List', 'tg-text-domain' ),
				),
				'std' => 'title',
				'required' => array(
					array('element_source_type', '==', 'post')
				)
			),
			array(
				'id'   => 'element_html_content',
				'name' => __( 'HTML Content', 'tg-text-domain' ),
				'options' => '',
				'sub_desc' =>  '',
				'type' => 'textarea',
				'cols' => 80,
				'rows' => 6,
				'required' => array(
					array($prefix.'source-type', '==', 'html')
				)
			),
	),
);

$animation_name  = new The_Grid_Item_Animation();
$element_animation = array(
		'id'    => 'element_animation',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			array(
				'id'   => 'element_animation_data',
				'name' => '',
				'desc' => '',
				'type' => 'custom',
				'options' => '<div style="display:none" class="tg-data-amin" data-item-anim=\''.json_encode($animation_name->get_animation_name()).'\'></div>',
			),
			array(
				'id'   => 'element_animation',
				'name' => __( 'Animation Style', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'placeholder' => __( 'Select an animation', 'tg-text-domain' ),
				'width' => 180,
				'options' => $animation_name->get_animation_arr(),
				'std' => ''
			),
			array(
				'id'   => 'element_animation_state',
				'name' => __( 'Animation State', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'width' => 180,
				'options' => array(
					'show' => __( 'Show on Hover', 'tg-text-domain' ),
					'hide' => __( 'Hide on Hover', 'tg-text-domain' )
				),
				'std' => 'show'
			),
			array(
				'id'   => 'element_transition_function',
				'name' => __( 'Transition function', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'width' => 180,
				'options' => array(
					'ease'         => __( 'Ease', 'tg-text-domain' ),
					'linear'       => __( 'Linear', 'tg-text-domain' ),
					'ease-in'      => __( 'Ease In', 'tg-text-domain' ),
					'ease-out'     => __( 'Ease Out', 'tg-text-domain' ),
					'ease-in-out'  => __( 'Ease In Out', 'tg-text-domain' ),
					'cubic-bezier' => __( 'Cubic Bezier', 'tg-text-domain' )
				),
				'std' => 'ease'
			),
			array(
				'id'   => 'element_transition_bezier',
				'name' => __( 'Transition Cubic Bezier', 'tg-text-domain' ),
				'options' => '',
				'sub_desc' =>  '',
				'type' => 'text',
				'std' => 'cubic-bezier(.39,1.89,.55,1.45)',
				'required' => array(
					array('element_transition_function', '==', 'cubic-bezier')
				)
			),
			array(
				'id'   => 'element_transition_duration',
				'name' => __( 'Transition duration', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'slider',
				'label' => '',
				'min' => 0,
				'max' => 3000,
				'step' => 10,
				'sign' => 'ms',
				'std' => 700
			),
			array(
				'id'   => 'element_transition_delay',
				'name' => __( 'Transition delay', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'slider',
				'label' => '',
				'min' => 0,
				'max' => 5000,
				'step' => 10,
				'sign' => 'ms',
				'std' => 0
			)
		)
);

$item_layout = array(
		'id'    => 'item_layout',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			array(
				'id'   => 'item_style',
				'name' => __( 'Skin style', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'radio',
				'options' => array(
					'masonry' => __( 'Masonry', 'tg-text-domain' ),
					'grid'    => __( 'Grid', 'tg-text-domain' )
				),
				'std' => 'masonry'
			),
			array(
				'id'   => 'item_content',
				'name' => __( 'Content position', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'width' => 180,
				'options' => array(
					'none'   => __( 'None', 'tg-text-domain' ),
					'bottom' => __( 'Bottom', 'tg-text-domain' ),
					'top'    => __( 'Top', 'tg-text-domain' ),
					'both'   => __( 'Top & Bottom', 'tg-text-domain' )
				),
				'std' => 'bottom',
				'required' => array(
					array('item_style', '==', 'masonry')
				)
			),
		)
);

$full_item = array(
		'id'    => 'item_layout',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			array(
				'id' => 'item_background-color',
				'name' => __('Background Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => '',
			),
			array(
				'id'   => 'item_padding',
				'name' => __( 'Paddings', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('item_','padding',array('padding-top','padding-right','padding-bottom','padding-left'),array('px','em','%'), '', 0, '')
			),
			array(
				'id'   => 'item_border',
				'name' => __( 'Border', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('item_','border',array('border-top','border-right','border-bottom','border-left'),array('px','em'), '', 0, ''),
			),
			array(
				'id'   => 'item_border-radius',
				'name' => __( 'Border Radius', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('item_','border-radius',array('border-top-left-radius','border-top-right-radius','border-bottom-right-radius','border-bottom-left-radius'),array('px','em', '%'), '', 0, '')
			),
			array(
				'id'   => 'item_border-style',
				'name' => __( 'Border Style', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'select',
				'width' => 160,
				'placeholder' => __( 'Select a value', 'tg-text-domain' ),
				'clear' => true,
				'options' => array(
					'none'   => __( 'None', 'tg-text-domain' ),
					'solid'  => __( 'Solid', 'tg-text-domain' ),
					'dotted' => __( 'Dotted', 'tg-text-domain' ),
					'dashed' => __( 'Dashed', 'tg-text-domain' ),
					'double' => __( 'Double', 'tg-text-domain' ),
					'groove' => __( 'Groove', 'tg-text-domain' ),
					'ridge'  => __( 'Ridge', 'tg-text-domain' ),
					'inset'  => __( 'inset', 'tg-text-domain' )
				),
				'std' => ''
			),
			array(
				'id' => 'item_border-color',
				'name' => __('Border Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => ''
			),
			array(
				'id'   => 'item_box-shadow',
				'name' => __( 'Box Shadow', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('item_','box-shadow',array('box-shadow-horizontal','box-shadow-vertical','box-shadow-blur','box-shadow-size'),array('px','rem'))
			),
			array(
				'id' => 'item_box-shadow-color',
				'name' => __('Box Shadow Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => 'rgba(0,0,0,0.8)'
			),
			array(
				'id'   => 'item_overflow',
				'name' => __( 'Overflow', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'select',
				'width' => 160,
				'placeholder' => __( 'Select a value', 'tg-text-domain' ),
				'clear' => true,
				'options' => array(
					'visible' => __( 'Visible', 'tg-text-domain' ),
					'hidden' => __( 'Hidden', 'tg-text-domain' )
				),
				'std' => ''
			)
		)
);

$media_layout = array(
		'id'    => 'media_layout',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			array(
				'id' => 'overlay_background-color',
				'name' => __('Background Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => '',
			),
			array(
				'id'   => 'overlay_positions',
				'name' => __( 'Positions', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('overlay_','positions',array('top','right','bottom','left'),array('px','em','%'), '', '', ''),
			),
			array(
				'id'   => 'overlay_border',
				'name' => __( 'Border', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('overlay_','border',array('border-top','border-right','border-bottom','border-left'),array('px','em'), '', 0, ''),
			),
			array(
				'id'   => 'overlay_border-radius',
				'name' => __( 'Border Radius', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('overlay_','border-radius',array('border-top-left-radius','border-top-right-radius','border-bottom-right-radius','border-bottom-left-radius'),array('px','em', '%'), '', 0, '')
			),
			array(
				'id'   => 'overlay_border-style',
				'name' => __( 'Border Style', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'select',
				'width' => 160,
				'placeholder' => __( 'Select a value', 'tg-text-domain' ),
				'clear' => true,
				'options' => array(
					'none'   => __( 'None', 'tg-text-domain' ),
					'solid'  => __( 'Solid', 'tg-text-domain' ),
					'dotted' => __( 'Dotted', 'tg-text-domain' ),
					'dashed' => __( 'Dashed', 'tg-text-domain' ),
					'double' => __( 'Double', 'tg-text-domain' ),
					'groove' => __( 'Groove', 'tg-text-domain' ),
					'ridge'  => __( 'Ridge', 'tg-text-domain' ),
					'inset'  => __( 'inset', 'tg-text-domain' )
				),
				'std' => ''
			),
			array(
				'id' => 'overlay_border-color',
				'name' => __('Border Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => ''
			),
			array(
				'id'   => 'overlay_box-shadow',
				'name' => __( 'Box Shadow', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('item_','box-shadow',array('box-shadow-horizontal','box-shadow-vertical','box-shadow-blur','box-shadow-size'),array('px','rem'))
			),
			array(
				'id' => 'overlay_box-shadow-color',
				'name' => __('Box Shadow Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => 'rgba(0,0,0,0.8)'
			)
		)
);

$content_layout = array(
		'id'    => 'content_layout',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			array(
				'id' => 'content_background-color',
				'name' => __('Background Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => '#ffffff',
			),
			array(
				'id'   => 'content_margin',
				'name' => __( 'Margin', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('content_','margin',array('margin-top','margin-right','margin-bottom','margin-left'),array('px','em','%'), '', -500, '')
			),
			array(
				'id'   => 'content_padding',
				'name' => __( 'Paddings', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('content_','padding',array('padding-top','padding-right','padding-bottom','padding-left'),array('px','em','%'), '', 0, '')
			),
			array(
				'id'   => 'content_border',
				'name' => __( 'Border', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('content_','border',array('border-top','border-right','border-bottom','border-left'),array('px','em'), '', 0, ''),
			),
			array(
				'id'   => 'content_border-radius',
				'name' => __( 'Border Radius', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('content_','border-radius',array('border-top-left-radius','border-top-right-radius','border-bottom-right-radius','border-bottom-left-radius'),array('px','em', '%'), '', 0, '')
			),
			array(
				'id'   => 'content_border-style',
				'name' => __( 'Border Style', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'select',
				'width' => 160,
				'placeholder' => __( 'Select a value', 'tg-text-domain' ),
				'clear' => true,
				'options' => array(
					'none'   => __( 'None', 'tg-text-domain' ),
					'solid'  => __( 'Solid', 'tg-text-domain' ),
					'dotted' => __( 'Dotted', 'tg-text-domain' ),
					'dashed' => __( 'Dashed', 'tg-text-domain' ),
					'double' => __( 'Double', 'tg-text-domain' ),
					'groove' => __( 'Groove', 'tg-text-domain' ),
					'ridge'  => __( 'Ridge', 'tg-text-domain' ),
					'inset'  => __( 'inset', 'tg-text-domain' )
				),
				'std' => ''
			),
			array(
				'id' => 'content_border-color',
				'name' => __('Border Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => ''
			),
			array(
				'id'   => 'content_box-shadow',
				'name' => __( 'Box Shadow', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'custom',
				'options' => tg_css_multiple_input('item_','box-shadow',array('box-shadow-horizontal','box-shadow-vertical','box-shadow-blur','box-shadow-size'),array('px','rem'))
			),
			array(
				'id' => 'content_box-shadow-color',
				'name' => __('Box Shadow Color', 'tg-text-domain'),
				'desc' => '',
				'sub_desc' => '',
				'type' => 'color',
				'rgba' => true,
				'std' => 'rgba(0,0,0,0.8)'
			)
		)
);

function animation_setting($prefix) {
	
	$animation_name  = new The_Grid_Item_Animation();
	
	$item_animation = array(
		'id'    => $prefix.'_animations',
		'title' => '',
		'icon' => '',
		'color' => '#f1f1f1',
		'background' => '#e74c3c',
		'pages' => array('the_grid'),
		'type' => 'page',
		'fields' => array(
			array(
				'id'   => $prefix.'_animation',
				'name' => __( 'Animation Style', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'placeholder' => __( 'Select an animation', 'tg-text-domain' ),
				'width' => 180,
				'options' => $animation_name->get_animation_arr(),
				'std' => ''
			),
			array(
				'id'   => $prefix.'_animation_state',
				'name' => __( 'Animation State', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'width' => 180,
				'options' => array(
					'show' => __( 'Show on Hover', 'tg-text-domain' ),
					'hide' => __( 'Hide on Hover', 'tg-text-domain' )
				),
				'std' => 'show'
			),
			array(
				'id'   => $prefix.'_transition_function',
				'name' => __( 'Transition function', 'tg-text-domain'  ),
				'sub_desc' => '',
				'desc' => '',
				'type' => 'select',
				'width' => 180,
				'options' => array(
					'ease'         => __( 'Ease', 'tg-text-domain' ),
					'linear'       => __( 'Linear', 'tg-text-domain' ),
					'ease-in'      => __( 'Ease In', 'tg-text-domain' ),
					'ease-out'     => __( 'Ease Out', 'tg-text-domain' ),
					'ease-in-out'  => __( 'Ease In Out', 'tg-text-domain' ),
					'cubic-bezier' => __( 'Cubic Bezier', 'tg-text-domain' )
				),
				'std' => 'ease'
			),
			array(
				'id'   => $prefix.'_transition_bezier',
				'name' => __( 'Transition Cubic Bezier', 'tg-text-domain' ),
				'options' => '',
				'sub_desc' =>  '',
				'type' => 'text',
				'std' => 'cubic-bezier(.39,1.89,.55,1.45)',
				'required' => array(
					array($prefix.'_transition_function', '==', 'cubic-bezier')
				)
			),
			array(
				'id'   => $prefix.'_transition_duration',
				'name' => __( 'Transition duration', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'slider',
				'label' => '',
				'min' => 0,
				'max' => 3000,
				'step' => 10,
				'sign' => 'ms',
				'std' => 700
			),
			array(
				'id'   => $prefix.'_transition_delay',
				'name' => __( 'Transition delay', 'tg-text-domain' ),
				'desc' => '',
				'sub_desc' =>  '',
				'type' => 'slider',
				'label' => '',
				'min' => 0,
				'max' => 5000,
				'step' => 10,
				'sign' => 'ms',
				'std' => 0
			)
		)
	);
	
	new TOMB_Metabox($item_animation);
	
}

echo '<div class="tg-settings-holder">';

	echo '<div class="tg-item-settings-holder">';
	
		echo '<div class="tg-container-header">';
			echo '<div class="tg-container-title">'.__( 'Item Layout Settings', 'tg-text-domain' ).'</div>';
			echo '<div class="tg-container-toggle"></div>';
		echo '</div>';
		
		echo '<div class="tg-container-content">';
		
			echo '<ul class="tomb-tabs-holder tomb-tabs-item-setttings">';
				echo '<li class="tomb-tab selected" data-target="tg-item-layout"><i class="tomb-icon dashicons dashicons-editor-kitchensink"></i>'.__( 'Layout', 'tg-text-domain' ).'</li>';
				echo '<li class="tomb-tab" data-target="tg-item-styles"><i class="tomb-icon dashicons dashicons-align-center"></i>'.__( 'Full Item', 'tg-text-domain' ).'</li>';
				echo '<li class="tomb-tab" data-target="tg-overlay-styles"><i class="tomb-icon dashicons dashicons-format-image"></i>'.__( 'Overlay', 'tg-text-domain' ).'</li>';
				echo '<li class="tomb-tab" data-target="tg-content-styles"><i class="tomb-icon dashicons dashicons-menu"></i>'.__( 'Content', 'tg-text-domain' );
				echo '<li class="tomb-tab" data-target="tg-item-animations"><i class="tomb-icon dashicons dashicons-format-video"></i>'.__( 'Animation', 'tg-text-domain' );
			echo '</ul>';
			
			
			echo '<div class="tomb-tab-content tg-item-layout">';
				new TOMB_Metabox($item_layout);
			echo '</div>';
			
			echo '<div class="tomb-tab-content tg-item-styles" data-state="idle-state" data-element="tg-item-inner">';
				new TOMB_Metabox($full_item);
			echo '</div>';
			
			echo '<div class="tomb-tab-content tg-overlay-styles" data-state="idle-state" data-element="tg-item-overlay">';
				new TOMB_Metabox($media_layout);
			echo '</div>';
			
			echo '<div class="tomb-tab-content tg-content-styles" data-state="idle-state" data-element="tg-item-content">';
				new TOMB_Metabox($content_layout);
			echo '</div>';
			
			echo '<div class="tomb-tab-content tg-item-animations">';
				echo '<ul class="tomb-tabs-holder">';
					echo '<li class="tomb-tab selected" data-target="tg-media-animation">'.__( 'Media', 'tg-text-domain' ).'</li>';
					echo '<li class="tomb-tab" data-target="tg-overlay-animation">'.__( 'Overlay', 'tg-text-domain' ).'</li>';
				echo '</ul>';
				echo '<div class="tomb-tab-content tg-media-animation">';
					animation_setting('media');
				echo '</div>';
				echo '<div class="tomb-tab-content tg-overlay-animation">';
					animation_setting('overlay');
				echo '</div>';
			echo '</div>';
		
		echo '</div>';
	
	echo '</div>';
	
	echo '<div class="tg-element-settings-holder">';
	
		echo '<div class="tg-container-header">';
			echo '<div class="tg-container-title">'.__( 'Element Settings', 'tg-text-domain' ).'</div>';
			echo '<div class="tg-container-toggle"></div>';
		echo '</div>';
	
		echo '<div class="tg-container-content">';
	
			echo '<ul class="tomb-tabs-holder tomb-tabs-element-setttings">';
				echo '<li class="tomb-tab selected" data-target="tg-element-sources"><i class="tomb-icon dashicons dashicons-portfolio"></i>'.__( 'Source', 'tg-text-domain' ).'</li>';
				echo '<li class="tomb-tab" data-target="tg-element-styles"><i class="tomb-icon dashicons dashicons-art"></i>'.__( 'Styles', 'tg-text-domain' ).'</li>';
				echo '<li class="tomb-tab" data-target="tg-element-animations"><i class="tomb-icon dashicons dashicons-format-video"></i>'.__( 'Animations', 'tg-text-domain' );
			echo '</ul>';
			
			echo '<div class="tomb-tab-content tg-element-sources">';
				new TOMB_Metabox($element_source);
			echo '</div>';
			
			echo '<div class="tomb-tab-content tg-element-styles">';
		
				echo '<ul class="tomb-tabs-holder tomb-tabs-element-styles">';
					echo '<li class="tomb-tab selected" data-target="idle-state"><i class="tomb-icon dashicons dashicons-admin-post"></i>'.__( 'Idle Sate', 'tg-text-domain' ).'</li>';
					echo '<li class="tomb-tab" data-target="hover-state"><i class="tomb-icon dashicons dashicons-flag"></i>'.__( 'Hover State', 'tg-text-domain' );
					echo '<div class="tomb-switch tg-filter-tooltip-holder" style="float:right">';
						echo '<span class="tg-filter-tooltip">'.__( 'Apply styles on mouseover (Hover State)', 'tg-text-domain' ).'</span>';
						echo '<input type="checkbox" class="tomb-checkbox" name="the_grid_hover-state-activate" data-state="hover-state">';
						echo '<label for="the_grid_hover-state-activate"></label>';
					echo '</div>';
					echo '</li>';
				echo '</ul>';
				
				echo '<div class="tomb-tab-content tg-styles-holder-idle idle-state" data-state="idle-state">';
					new TOMB_Metabox($element_idle);
				echo '</div>';
					
				echo '<div class="tomb-tab-content tg-styles-holder-hover hover-state" data-state="hover-state">';
					new TOMB_Metabox($element_hover);
				echo '</div>';
			
			echo '</div>';
			
			echo '<div class="tomb-tab-content tg-element-animations">';
				new TOMB_Metabox($element_animation);
			echo '</div>';
			
			echo '<div class="tg-container-footer">';
				echo '<div class="tg-button" id="tg-element-remove"><i class="dashicons dashicons-trash"></i>'.__( 'Remove element', 'tg-text-domain').'</div>';
			echo '</div>';
			
		echo '</div>';

	echo '</div>';

echo '</div>';


echo '<div class="tg-skin-builder-holder">';

	echo '<div class="tg-container-header">';
		echo '<div class="tg-container-title">'.__( 'Item Layout', 'tg-text-domain' ).'</div>';
		echo '<div class="tg-button" id="tg-item-preview"><i class="dashicons dashicons-visibility"></i>'.__( 'Preview', 'tg-text-domain' ).'</div>';
	echo '</div>';
	
	$zone_name = __( 'DROP ZONE', 'tg-text-domain' );
	
	echo '<div class="tg-skin-build-inner">';
		echo '<div class="tg-item-inner">';
			echo '<div class="tg-item-content" data-position="top">';
				echo '<div class="tg-skin-body-content tg-area-droppable" data-name="'.$zone_name.'"></div>';
			echo '</div>';
			echo '<div class="tg-item-media-wrapper">';
				echo '<div class="tg-item-media-holder"></div>';
				echo '<div class="tg-item-overlay"></div>';
				echo '<div class="tg-item-overlay-content tg-area-droppable" data-name="'.$zone_name.'"></div>';
			echo '</div>';
			echo '<div class="tg-item-clear"></div>';
			echo '<div class="tg-item-content" data-position="bottom">';
				echo '<div class="tg-skin-body-content tg-area-droppable" data-name="'.$zone_name.'"></div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="tg-skin-elements-css"></div>';
	
echo '</div>';

echo '<div class="tg-skin-elements-holder">';

	echo '<div class="tg-container-header">';
		echo '<div class="tg-container-title">'.__( 'Available Elements', 'tg-text-domain' ).'</div>';
	echo '</div>';
	
	echo '<div class="tg-skin-elements-inner tg-skin-element-sortable">';
		echo '<div class="tg-element-draggable" data-name="tg-element-title">'.__( 'Title', 'tg-text-domain' ).'</div>';
		echo '<div class="tg-element-draggable" data-name="tg-element-button">'.__( 'Button', 'tg-text-domain' ).'</div>';
		echo '<div class="tg-element-draggable" data-name="tg-element-excerpt">'.__( 'Actique exilium principis is in nullos Constantio et absolutum quorum movebantur ita intendebantur ubi gladii sub coopertos facile uncosque in poenales coopertos ubi eculei quemquam sceleste ex alii Constantio parabat principis Paulus ...', 'tg-text-domain' ).'</div>';
	echo '</div>';
	
echo '</div>';