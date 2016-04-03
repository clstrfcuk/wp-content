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

$elements = $tg_grid_data['area_top1_elements'];

if (!empty($elements)) {

	$area  = '<!-- The Grid Area Top 1 -->';
	$area .= '<div class="tg-grid-area-top1">';
		foreach($elements as $element) {
			$area .= $element;
		}
	$area .= '</div>';
	
	echo $area;

}
