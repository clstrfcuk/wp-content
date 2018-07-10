<?php
/**
 * Envira Cache Functions.
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
 * Helper method to flush gallery caches once a gallery is updated.
 *
 * @since 1.0.0
 *
 * @param int $post_id The current post ID.
 * @param string $slug The unique gallery slug.
 */
function envira_flush_gallery_caches( $post_id, $slug = '' ) {

	// Delete known gallery caches.
	delete_transient( '_eg_cache_' . $post_id );
	delete_transient( '_eg_cache_all' );
	delete_transient( '_eg_fragment_' . $post_id );
	delete_transient( '_eg_fragment_json_' . $post_id );

	// Possibly delete slug gallery cache if available.
	if ( ! empty( $slug ) ) {
		delete_transient( '_eg_cache_' . $slug );
	}

	// Run a hook for Addons to access.
	do_action( 'envira_gallery_flush_caches', $post_id, $slug );

}

function envira_flush_all_cache(){

	global $wpdb;

	$wpdb->query( "DELETE FROM `wp_options` WHERE `option_name` LIKE ('_transient__eg_%') OR `option_name` LIKE ('_transient_timeout__eg_%') OR `option_name` LIKE ('_transient_timeout__eg_%') ;" );

}