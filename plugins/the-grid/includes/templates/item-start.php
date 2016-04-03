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

$classes    = $tg_grid_data['item_classes'];
$attributes = $tg_grid_data['item_attributes'];

$item_start  = '<article class="tg-item'.$classes.'"'.$attributes.'>';
	$item_start .= '<div class="tg-item-inner">';

echo $item_start;
