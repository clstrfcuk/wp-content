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

$grid_style = $tg_grid_data['style'];
$grid_layout_data = $tg_grid_data['layout_data'];

$holder_start  = '<!-- The Grid Items Holder -->';
$holder_start .= '<div class="tg-grid-holder tg-layout-'.esc_attr($grid_style).'" '.$grid_layout_data.'>';

echo $holder_start;
