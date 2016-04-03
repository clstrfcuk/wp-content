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

$form_export  = '<div class="metabox-holder tg-export">';
	$form_export .= '<div class="postbox">';
		$form_export .= '<div class="tg-box-side">';
			$form_export .= '<h3>'. __( 'Exporter', 'tg-text-domain' ) .'</h3>';
			$form_export .= '<i class="tg-info-box-icon dashicons dashicons-upload"></i>';
		$form_export .= '</div>';
		$form_export .= '<div class="inside tg-box-inside">';
			$form_export .= '<h3>'. __( 'Export Grid(s)', 'tg-text-domain' ) .'</h3>';
			$base = new The_Grid_Base();
			$settings['param_name'] = 'name';
			$list = $base->get_grid_list($settings, $value = '', $multi = true);
			$form_export .= $list;
		$form_export .= '</div>';
	$form_export .= '</div>';
$form_export .= '</div>';

echo $form_export;


