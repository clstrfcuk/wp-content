<?php

// Theme Constants
// =============================================================================

define( 'X_VERSION', '2.1.6' );
define( 'X_SLUG', 'pro' );
define( 'X_TITLE', 'Pro' );
define( 'X_I18N_PATH', X_TEMPLATE_PATH . '/framework/functions/pro/i18n');



// App Environment Data
// =============================================================================

function pro_cornerstone_app_env( $env ) {
  $env['product'] = 'pro';
  $env['title'] = X_TITLE;
  $env['version'] = X_VERSION;
  $env['productKey'] = esc_attr( get_option( 'x_product_validation_key', '' ) );
  return $env;
}

add_filter( '_cornerstone_app_env', 'pro_cornerstone_app_env' );


add_theme_support( 'cornerstone_regions' );

function pro_load_cornerstone() {

  $cs_path = X_TEMPLATE_PATH . '/cornerstone';

  if ( ! file_exists( "$cs_path/includes/boot.php" ) ) {
    return;
  }

  if ( class_exists('Cornerstone_Plugin') ) {
    add_action('admin_init', 'pro_deactivate_cornerstone');
    return;
  }

  require_once("$cs_path/includes/boot.php");
  cornerstone_boot( "$cs_path/cornerstone.php",
    X_TEMPLATE_PATH . '/framework/functions/pro/i18n',
    X_TEMPLATE_URL . '/cornerstone/'
  );

}

pro_load_cornerstone();


function pro_deactivate_cornerstone() {
  if ( function_exists('deactivate_plugins') ) {
    deactivate_plugins('cornerstone/cornerstone.php');
  }
}


if ( ! function_exists( 'x_body_class_version' ) ) :
  function x_body_class_version( $output ) {

    $output[] = 'pro-v' . str_replace( '.', '_', X_VERSION );
    return $output;

  }
  add_filter( 'body_class', 'x_body_class_version', 10000 );
endif;


function pro_load_preinit() {
  require_once X_TEMPLATE_PATH . '/framework/functions/pro/migration.php';
  require_once X_TEMPLATE_PATH . '/framework/functions/pro/bars/setup.php';
}

add_action('x_boot_preinit', 'pro_load_preinit' );



function pro_scandir_exclusions( $exclusions ) {
  $exclusions[] = 'cornerstone';
  return $exclusions;
}

add_filter('theme_scandir_exclusions', 'pro_scandir_exclusions' );
