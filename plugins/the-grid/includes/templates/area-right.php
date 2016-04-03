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

$elements = $tg_grid_data['area_right_elements'];

$area  = '<!-- The Grid Area Right -->';
$area .= '<div class="tg-grid-area-right">';
	$area .= '<div class="tg-grid-area-inner">';
		$area .= '<div class="tg-grid-area-wrapper">';
			foreach($elements as $element) {
				$area .= $element;
			}
		$area  .= '</div>';
	$area  .= '</div>';
$area .= '</div>';
	
echo $area;