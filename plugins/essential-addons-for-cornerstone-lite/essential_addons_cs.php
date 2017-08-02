<?php

/*
Plugin Name: Essential Addons for Cornerstone Lite
Plugin URI: http://codetic.net/demo/essential-addons/
Description: Essential element library for Cornerstone and Pro page builder for WordPress lite Version. <a href="https://www.codetic.net/go/get-eacs.php">Get Premium Version</a>
Author: Codetic
Author URI: http://www.codetic.net/
Version: 1.0.0
Text Domain: essential-addons-cs
*/

define( 'ESSENTIAL_ADDONS_CS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ESSENTIAL_ADDONS_CS_URL', plugin_dir_url( __FILE__ ) );

add_action( 'wp_enqueue_scripts', 'essential_addons_cs_enqueue' );
add_action( 'cornerstone_register_elements', 'essential_addons_cs_register_elements' );
add_filter( 'cornerstone_icon_map', 'essential_addons_cs_icon_map' );

function essential_addons_cs_register_elements() {

	cornerstone_register_element( 'EACS_Logo_Carousel', 'eacs-logo-carousel', ESSENTIAL_ADDONS_CS_PATH . 'includes/logo-carousel' );
	cornerstone_register_element( 'EACS_Logo_Carousel_Item', 'eacs-logo-carousel-item', ESSENTIAL_ADDONS_CS_PATH . 'includes/logo-carousel-item' );
	cornerstone_register_element( 'EACS_Testimonial_Slider', 'eacs-testimonial-slider', ESSENTIAL_ADDONS_CS_PATH . 'includes/testimonial-slider' );
	cornerstone_register_element( 'EACS_Testimonial_Item', 'eacs-testimonial-item', ESSENTIAL_ADDONS_CS_PATH . 'includes/testimonial-item' );
	cornerstone_register_element( 'EACS_Team_Members', 'eacs-team-members', ESSENTIAL_ADDONS_CS_PATH . 'includes/team-members' );
	cornerstone_register_element( 'EACS_Team_Item', 'eacs-team-item', ESSENTIAL_ADDONS_CS_PATH . 'includes/team-members-item' );
	cornerstone_register_element( 'EACS_Post_Carousel', 'eacs-post-carousel', ESSENTIAL_ADDONS_CS_PATH . 'includes/post-carousel' );
	cornerstone_register_element( 'EACS_Product_Carousel', 'eacs-product-carousel', ESSENTIAL_ADDONS_CS_PATH . 'includes/product-carousel' );
	cornerstone_register_element( 'EACS_Product_Grid', 'eacs-product-grid', ESSENTIAL_ADDONS_CS_PATH . 'includes/product-grid' );

}

function essential_addons_cs_enqueue() {
	wp_enqueue_script( 'essential_addons_cs-slick-js', ESSENTIAL_ADDONS_CS_URL . 'assets/slick/slick.min.js', array('jquery'), null, true );
	wp_enqueue_style( 'essential_addons_cs-styles', ESSENTIAL_ADDONS_CS_URL . 'assets/styles/essential-addons-cs.css', array(), '1.0.0' );
	wp_enqueue_style( 'essential_addons_cs-slick', ESSENTIAL_ADDONS_CS_URL . 'assets/slick/slick.css', array(), '1.0.0' );
}

function essential_addons_cs_icon_map( $icon_map ) {
	$icon_map['essential-addons-cs'] = ESSENTIAL_ADDONS_CS_URL . 'assets/svg/icons.svg';
	return $icon_map;
}
