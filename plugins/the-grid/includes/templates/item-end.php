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

global $tg_grid_data;

$item_settings = $tg_grid_data['item_settings'];

	$item_end = '</div>';	
	$item_end .= $item_settings;
$item_end .= '</article>';

echo $item_end;