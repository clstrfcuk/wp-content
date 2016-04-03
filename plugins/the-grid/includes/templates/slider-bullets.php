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

$grid_layout = $tg_grid_data['layout'];
		
if ($grid_layout == 'horizontal') {
	
	$bullets  = '<!-- The Grid Slider Bullets -->';
	$bullets .= '<div class="tg-slider-bullets-holder">';
		$bullets .= '<div class="tg-slider-bullets"></div>';
	$bullets .= '</div>';
		
	echo $bullets;
	
}