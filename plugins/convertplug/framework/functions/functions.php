<?php
require_once('functions.admin.php');
add_action( 'wp_ajax_framework_update_options', 'framework_update_options');
add_action( 'wp_ajax_framework_update_preview_data', 'framework_update_preview_data');

// function to return style settings array
if(!function_exists('smile_get_style_settings')){
	function smile_get_style_settings($option,$style){
		$prev_styles = get_option($option);
		$styles = array();
		foreach($prev_styles as $key => $settings){
			if($settings['style_id'] == $style){
				$styles = unserialize($prev_styles[$key]['style_settings']);
			}
		}
		
		$style_settings = array();
		foreach($styles as $key => $setting){
			$style_settings[$key] = apply_filters('smile_render_setting',$setting);;
		}
		return $style_settings;
	}
}