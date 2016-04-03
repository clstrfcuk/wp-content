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
		
if ($tg_grid_data['layout'] == 'horizontal') {
	
	$arrow = '<div class="tg-left-arrow tg-nav-color tg-nav-font">';
		$arrow .= '<i class="tg-icon-left-arrow tg-nav-color tg-nav-border tg-nav-font"></i>';
	$arrow .= '</div>';
	
	echo $arrow;
	
}