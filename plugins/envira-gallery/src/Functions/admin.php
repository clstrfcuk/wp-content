<?php
/**
 * Envira Admin Functions.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Load Admin Template Partials
 *
 * @since 1.7.0
 *
 * @access public
 * @param mixed $template
 * @param array $data (default: array())
 * @return void
 */
function envira_load_admin_partial( $template, $data = array() ){

	$dir = trailingslashit( plugin_dir_path( ENVIRA_FILE ) . 'src/Views/partials' );

	if ( file_exists( $dir . $template . '.php' ) ) {

		require_once( $dir . $template . '.php' );
		return true;
	}

	return false;

}

/**
 * envira_load_admin_field function.
 *
 * @since 1.7.1
 *
 * @access public
 * @param mixed $field
 * @param array $data (default: array())
 * @return void
 */
function envira_load_admin_field( $field, $data = array() ) {

	if ( empty( $data ) ){
		return false;
	}

	$dir = trailingslashit( plugin_dir_path( ENVIRA_FILE ) . 'src/Views/fields' );

	if ( file_exists( $dir . $field . '.php' ) ) {

		include( $dir . $field . '.php' );
		return true;
	}

	return false;

}

/**
 * Get user license Key
 *
 * @since 1.7.0
 *
 * @access public
 * @return void
 */
function envira_get_license_key(){

	$option = get_option( 'envira_gallery' );
	$key     = false;

	if ( empty( $option['key'] ) ) {

		if ( defined( 'ENVIRA_LICENSE_KEY' ) ) {

			$key = ENVIRA_LICENSE_KEY;

		}

	} else {

		$key = $option['key'];

	}

	return apply_filters( 'envira_gallery_license_key', $key );

}

/**
 * Returns the license key type for Envira.
 *
 * @since 1.7.0
 *
 * @return string $type The user's license key type for Envira.
 */
function envira_get_license_key_type() {

	$option = get_option( 'envira_gallery' );
	return $option['type'];

}

/**
 * Returns possible license key error flag.
 *
 * @since 1.7.0
 *
 * @return bool True if there are license key errors, false otherwise.
 */
function envira_get_license_key_errors() {

	$option = get_option( 'envira_gallery' );
	return isset( $option['is_expired'] ) && $option['is_expired'] || isset( $option['is_disabled'] ) && $option['is_disabled'] || isset( $option['is_invalid'] ) && $option['is_invalid'];

}

/**
 * Called whenever an upgrade button / link is displayed in Lite, this function will
 * check if there's a shareasale ID specified.
 *
 * There are three ways to specify an ID, ordered by highest to lowest priority
 * - add_filter( 'envira_gallery_shareasale_id', function() { return 1234; } );
 * - define( 'ENVIRA_GALLERY_SHAREASALE_ID', 1234 );
 * - get_option( 'envira_gallery_shareasale_id' ); (with the option being in the wp_options table)
 *
 * If an ID is present, returns the ShareASale link with the affiliate ID, and tells
 * ShareASale to then redirect to enviragallery.com/lite
 *
 * If no ID is present, just returns the enviragallery.com/lite URL with UTM tracking.
 *
 * @since 1.5.0
 */
function envira_get_upgrade_link() {

	if ( class_exists( 'Envira_Gallery' ) ) {
		// User is using Envira Gallery, so just take them to the Pricing page.
		// Note: On the Addons screen, if the user has a license, we won't hit this function,
		// as the API will tell us the direct URL to send the user to based on their license key,
		// so they see pro-rata pricing.
		return 'https://enviragallery.com/pricing/?utm_source=proplugin&utm_medium=link&utm_campaign=WordPress';
	}

	// Check if there's a constant.
	$shareasale_id = '';
	if ( defined( 'ENVIRA_GALLERY_SHAREASALE_ID' ) ) {
		$shareasale_id = ENVIRA_GALLERY_SHAREASALE_ID;
	}

	// If there's no constant, check if there's an option.
	if ( empty( $shareasale_id ) ) {
		$shareasale_id = get_option( 'envira_gallery_shareasale_id', '' );
	}

	// Whether we have an ID or not, filter the ID.
	$shareasale_id = apply_filters( 'envira_gallery_shareasale_id', $shareasale_id );

	// If at this point we still don't have an ID, we really don't have one!
	// Just return the standard upgrade URL.
	if ( empty( $shareasale_id ) ) {
		return 'http://enviragallery.com/lite/?utm_source=liteplugin&utm_medium=link&utm_campaign=WordPress';
	}

	// If here, we have a ShareASale ID
	// Return ShareASale URL with redirect.
	return 'http://www.shareasale.com/r.cfm?u=' . $shareasale_id . '&b=566240&m=51693&afftrack=&urllink=enviragallery%2Ecom%2Flite%2F';

}

/**
 * Flag to determine if the GD library has been compiled.
 *
 * @since 1.7.0
 *
 * @return bool True if has proper extension, false otherwise.
 */
function envira_has_gd_extension() {

	return extension_loaded( 'gd' ) && function_exists( 'gd_info' );

}

/**
 * Flag to determine if the Imagick library has been compiled.
 *
 * @since 1.7.0
 *
 * @return bool True if has proper extension, false otherwise.
 */
function envira_has_imagick_extension() {

	return extension_loaded( 'imagick' );

}

/**
 * Returns an Array of Registered Publishers.
 *
 * @since 1.7.0
 *
 * @access public
 * @return array
 */
function envira_get_publishers(){

	$publishers = array();

	return apply_filters( 'envira_publishers', $publishers );

}

/**
 * Returns an array of registered Importers.
 *
 * @since 1.7.0
 *
 * @access public
 * @return array
 */
function envira_get_importers(){

	$importers = array();

	return apply_filters( 'envira_importers', $importers );

}

/**
 * Returns the post types to skip for loading Envira metaboxes.
 *
 * @since 1.7.0
 *
 * @return array Array of skipped posttypes.
 */
function envira_get_skipped_posttypes() {

	$skipped_posttypes = array( 'attachment', 'revision', 'nav_menu_item', 'soliloquy', 'soliloquyv2', 'envira_album' );
	return apply_filters( 'envira_gallery_skipped_posttypes', $skipped_posttypes );

}