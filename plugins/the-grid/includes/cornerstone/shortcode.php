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

if (!empty($name)) {
	$grid = new The_Grid();
	$content = $grid->output($name);
	if (empty($content)) {
		echo '<div class="tg-no-post">'.__('No grid was found for:', 'tg-text-domain' ).' '.$name.'</div>';
	} else {
		echo ($content);
	}
} else {
	echo '<div class="tg-no-post">'.__('The shortcode doesn\'t contain any grid name', 'tg-text-domain' ).'</div>';
}