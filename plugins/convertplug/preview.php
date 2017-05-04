<?php

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

$module = isset( $_GET['module'] ) ? esc_attr( $_GET['module'] ) : '';
$theme = isset( $_GET['theme'] ) ? esc_attr( $_GET['theme'] ) : '';
$class = isset( $_GET['class'] ) ? esc_attr( $_GET['class'] ) : '';

if( $module !== '' ) {

	if( file_exists( CP_BASE_DIR.'/modules/'.$module.'/functions/functions.options.php' ) ) {
		
		require_once( CP_BASE_DIR.'/modules/'.$module.'/functions/functions.options.php' );

		$settings = $class::$options;
		foreach( $settings as $style => $options ) {
			if( $style == $theme ) {
				$demo_html = $options['demo_url'];
				$demo_dir = $options['demo_dir'];
				$customizer_js = $options['customizer_js'];
			}
		}

		$handle = fopen( $demo_dir, "r" );
		$post_content = fread( $handle, filesize($demo_dir) );
		print_r($post_content);
	}
}