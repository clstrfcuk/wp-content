<?php

// Theme Constants
// =============================================================================

define( 'X_VERSION', '1.0.3' );
define( 'X_SLUG', 'xpro' );
define( 'X_TITLE', 'X Pro' );
define( 'X_I18N_PATH', X_TEMPLATE_PATH . '/framework/functions/xpro/i18n');


add_filter('x_pre_boot_x', '__return_false' );
add_theme_support( 'cornerstone_regions' );

function x_pro_load_cornerstone() {

	$cs_path = X_TEMPLATE_PATH . '/cornerstone';

	if ( ! file_exists( "$cs_path/includes/boot.php" ) ) {
		return;
	}

	if ( class_exists('Cornerstone_Plugin') ) {
		add_action('admin_init', 'x_pro_deactivate_cornerstone');
		return;
	}

	require_once("$cs_path/includes/boot.php");
	cornerstone_boot( "$cs_path/cornerstone.php",
		X_TEMPLATE_PATH . '/framework/functions/xpro/i18n',
		X_TEMPLATE_URL . '/cornerstone/'
	);

}

x_pro_load_cornerstone();


function x_pro_deactivate_cornerstone() {
	if ( function_exists('deactivate_plugins') ) {
		deactivate_plugins('cornerstone/cornerstone.php');
	}
}


if ( ! function_exists( 'x_body_class_version' ) ) :
	function x_body_class_version( $output ) {

		$output[] = 'xpro-v' . str_replace( '.', '_', X_VERSION );
		return $output;

	}
	add_filter( 'body_class', 'x_body_class_version', 10000 );
endif;


function x_pro_add_boot_files( $files ) {

	$files[] = X_TEMPLATE_PATH . '/framework/functions/xpro/bars/setup.php';
	return $files;
}

add_filter('x_boot_files', 'x_pro_add_boot_files' );
