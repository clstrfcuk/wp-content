<?php
/*
Plugin Name: Essential Addons for Cornerstone
Plugin URI: https://essential-addons.com/cornerstone/
Description: Ultimate elements library for Cornerstone and Pro page builder for WordPress. <a href="https://essential-addons.com/cornerstone/buy.php">Get Premium Version</a>
Author: Codetic
Author URI: http://www.codetic.net/
Version: 1.3.0
Text Domain: essential-addons-cs
*/

define( 'ESSENTIAL_ADDONS_CS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ESSENTIAL_ADDONS_CS_URL', plugin_dir_url( __FILE__ ) );

add_action( 'wp_enqueue_scripts', 'essential_addons_cs_enqueue' );
add_action( 'cornerstone_register_elements', 'essential_addons_cs_register_elements' );
add_filter( 'cornerstone_icon_map', 'essential_addons_cs_icon_map' );

require_once( ESSENTIAL_ADDONS_CS_PATH.'admin/settings.php' );

function essential_addons_cs_register_elements() {
	$eacs_default_settings = array(
	   'logo-carousel'      => true,
	   'logo-carousel-item' => true,
	   'post-grid'     		=> true,
	   'post-carousel'      => true,
	   'product-carousel'   => true,
	   'product-grid'       => true,
	   'team-members'       => true,
	   'team-members-item'  => true,
	   'testimonial-item'   => true,
	   'testimonial-slider' => true,
	);
	$is_component_active = get_option( 'eacs_save_settings', $eacs_default_settings );
	if( $is_component_active['logo-carousel'] ) {
		cornerstone_register_element( 'EACS_Logo_Carousel', 'eacs-logo-carousel', ESSENTIAL_ADDONS_CS_PATH . 'includes/logo-carousel' );
		cornerstone_register_element( 'EACS_Logo_Carousel_Item', 'eacs-logo-carousel-item', ESSENTIAL_ADDONS_CS_PATH . 'includes/logo-carousel-item' );
	}
	if( $is_component_active['testimonial-slider'] ) {
		cornerstone_register_element( 'EACS_Testimonial_Slider', 'eacs-testimonial-slider', ESSENTIAL_ADDONS_CS_PATH . 'includes/testimonial-slider' );
		cornerstone_register_element( 'EACS_Testimonial_Item', 'eacs-testimonial-item', ESSENTIAL_ADDONS_CS_PATH . 'includes/testimonial-item' );
	}
	if( $is_component_active['team-members'] ) {
		cornerstone_register_element( 'EACS_Team_Members', 'eacs-team-members', ESSENTIAL_ADDONS_CS_PATH . 'includes/team-members' );
		cornerstone_register_element( 'EACS_Team_Item', 'eacs-team-item', ESSENTIAL_ADDONS_CS_PATH . 'includes/team-members-item' );
	}
	if( $is_component_active['post-grid'] ) {
		cornerstone_register_element( 'EACS_Post_Grid', 'eacs-post-grid', ESSENTIAL_ADDONS_CS_PATH . 'includes/post-grid' );
	}
	if( $is_component_active['post-carousel'] ) {
		cornerstone_register_element( 'EACS_Post_Carousel', 'eacs-post-carousel', ESSENTIAL_ADDONS_CS_PATH . 'includes/post-carousel' );
	}
	if( $is_component_active['product-carousel'] ) {
		cornerstone_register_element( 'EACS_Product_Carousel', 'eacs-product-carousel', ESSENTIAL_ADDONS_CS_PATH . 'includes/product-carousel' );
	}
	if( $is_component_active['product-grid'] ) {
		cornerstone_register_element( 'EACS_Product_Grid', 'eacs-product-grid', ESSENTIAL_ADDONS_CS_PATH . 'includes/product-grid' );
	}
}

function essential_addons_cs_enqueue() {
	$is_component_active = get_option( 'eacs_save_settings' );
	if( $is_component_active['logo-carousel'] || $is_component_active['post-carousel'] || $is_component_active['team-members'] || $is_component_active['testimonial-slider'] ) {
		wp_enqueue_script( 'essential_addons_cs-slick-js', ESSENTIAL_ADDONS_CS_URL . 'assets/slick/slick.min.js', array('jquery'), null, true );
	}	
	if( $is_component_active['post-grid'] ) {
		wp_enqueue_script( 'essential_addons_cs-masonry-js', ESSENTIAL_ADDONS_CS_URL . 'assets/js/masonry.min.js', array('jquery'), null, true );
	}
	wp_enqueue_style( 'essential_addons_cs-styles', ESSENTIAL_ADDONS_CS_URL . 'assets/styles/essential-addons-cs.css', array(), '1.0.0' );
	wp_enqueue_style( 'essential_addons_cs-slick', ESSENTIAL_ADDONS_CS_URL . 'assets/slick/slick.css', array(), '1.0.0' );
}

function essential_addons_cs_icon_map( $icon_map ) {
	$icon_map['essential-addons-cs'] = ESSENTIAL_ADDONS_CS_URL . 'assets/svg/icons.svg';
	return $icon_map;
}


// Action menus

function eacs_add_settings_link( $links ) {
    $settings_link = sprintf( '<a href="admin.php?page=eacs-settings">' . __( 'Settings' ) . '</a>' );
    $go_pro_link = sprintf( '<a href="https://essential-addons.com/cornerstone/buy.php" target="_blank" style="color: #39b54a; font-weight: bold;">' . __( 'Go Pro' ) . '</a>' );
    array_push( $links, $settings_link, $go_pro_link );
   return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'eacs_add_settings_link' );



// Redirect to options page

register_activation_hook(__FILE__, 'eacs_activate');
add_action('admin_init', 'eacs_redirect');

function eacs_activate() {
    add_option('eacs_do_activation_redirect', true);
}

function eacs_redirect() {
    if (get_option('eacs_do_activation_redirect', false)) {
        delete_option('eacs_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("admin.php?page=eacs-settings");
        }
    }
}

