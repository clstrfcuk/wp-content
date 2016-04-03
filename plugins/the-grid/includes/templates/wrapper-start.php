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

// retrieve all grid settings
global $tg_grid_data;

$grid_ID    = $tg_grid_data['ID'];
$grid_class = $tg_grid_data['wrapper_css_class'];
$grid_css   = $tg_grid_data['grid_css'];

$wrapper_start  = '<!-- The Grid Plugin Version '.TG_VERSION.' -->';
$wrapper_start .= '<!-- The Grid Wrapper Start -->';
$wrapper_start .= '<div class="tg-grid-wrapper '.esc_attr($grid_class).'" id="'.esc_attr($grid_ID).'">';
	if (!empty($grid_css)) {
		// ('. floor(mb_strlen($grid_css, '8bit')/1000) .'Ko)
		$wrapper_start .= '<!-- The Grid Styles -->';
		$wrapper_start .= '<style class="tg-grid-styles" type="text/css" scoped>'.$grid_css .'</style>';
	}
	$wrapper_start .= '<!-- The Grid Item Sizer -->';
	$wrapper_start .= '<div class="tg-grid-sizer"></div>';
	$wrapper_start .= '<!-- The Grid Gutter Sizer -->';
	$wrapper_start .= '<div class="tg-gutter-sizer"></div>';

echo $wrapper_start;
