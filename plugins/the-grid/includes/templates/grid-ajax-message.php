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

$ajax_method = $tg_grid_data['ajax_method'];
		
if ($ajax_method == 'on_scroll') {

	$loading = esc_attr($tg_grid_data['ajax_button_loading']);
			
	if (!empty($loading)) {
		
		$no_more = ' data-no-more="'.esc_attr($tg_grid_data['ajax_button_no_more']).'"';
		
		$ajax_scroll  = '<!-- The Grid Ajax Scroll -->';
		$ajax_scroll .= '<div class="tg-ajax-scroll-holder">';
		$ajax_scroll .= '<div class="tg-ajax-scroll" '.$no_more.'>'.esc_attr($loading).'</div>';
		$ajax_scroll .= '</div>';
		
		echo $ajax_scroll;
	}
			
}