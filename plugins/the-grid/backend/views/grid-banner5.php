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

$banner = '<div id="tg-banner-holder">';

	$banner .= '<div id="tg-banner" class="tg-banner-sticky">';
		$banner .= '<h2><span>The Grid</span>'. __( 'Skin Builder', 'tg-text-domain') .'</h2>';
		$banner .= '<div id="tg-buttons-holder">';
			$banner .= '<a class="tg-button" data-action="tg_save_skin" id="tg_skin_save"><i class="dashicons dashicons-yes"></i>'. __( 'Save', 'tg-text-domain') .'</a>';
		$banner .= '</div>';
	$banner .= '</div>';

$banner .= '</div>';

echo $banner;