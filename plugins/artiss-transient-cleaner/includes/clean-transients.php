<?php
/**
* Clean Transients
*
* Functions to clear down transient data
*
* @package	Artiss-Transient-Cleaner
*/

/**
* Clean Expired Transients
*
* Hook into scheduled deletions and clear down expired transients
*
* @since	1.0
*
* @return	string		Number of transients removed
*/

function atc_clean_transients() {

	$cleaned = 0;

	// Only perform clean if enabled

	$options = atc_get_options();

	if ( $options[ 'clean_enable' ] ) { $cleaned = atc_transient_delete( false ); }

	// Return number of cleaned transients

	return $cleaned;
}

add_action( 'wp_scheduled_delete', 'atc_clean_transients' );

/**
* Clear All Transients
*
* Hook into database upgrade and clear transients
*
* @since	1.0
*
* @return	string		Number of transients removed
*/

function atc_clear_transients() {

	$cleared = 0;

	// Only perform clear if enabled

	$options = atc_get_options();

	if ( $options[ 'upgrade_enable' ] ) { $cleared = atc_transient_delete( true ); }

	// Return number of cleared transients

	return $cleared;

}

add_action( 'after_db_upgrade', 'atc_clear_transients' );

/**
* Delete Transients
*
* Shared function that will clear down requested transients
*
* @since	1.0
*
* @param	string	$expired	TRUE or FALSE, whether to clear all transients or not
* @return	string				Number of removed transients
*/

function atc_transient_delete( $clear_all ) {

	$cleaned = 0;

	global $_wp_using_ext_object_cache;

	if ( !$_wp_using_ext_object_cache ) {

		$options = atc_get_options();

		global $wpdb;

		// Build and execute required SQL

		if ( $clear_all ) {

			$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'";
			$clean = $wpdb -> query( $sql );

		} else {

			$sql = "
				DELETE
					a, b
				FROM
					{$wpdb->options} a, {$wpdb->options} b
				WHERE
					a.option_name LIKE '_transient_%' AND
					a.option_name NOT LIKE '_transient_timeout_%' AND
					b.option_name = CONCAT(
						'_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_transient_') + 1
						)
					)
				AND b.option_value < UNIX_TIMESTAMP()
			";

			$clean = $wpdb -> query( $sql );

			$sql = "
				DELETE
					a, b
				FROM
					{$wpdb->options} a, {$wpdb->options} b
				WHERE
					a.option_name LIKE '_site_transient_%' AND
					a.option_name NOT LIKE '_site_transient_timeout_%' AND
					b.option_name = CONCAT(
						'_site_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_site_transient_') + 1
						)
					)
				AND b.option_value < UNIX_TIMESTAMP()
			";

			$clean = $wpdb -> query( $sql );
		}

		// Save options field with number & timestamp

		$results[ 'timestamp' ] = time() + ( get_option( 'gmt_offset' ) * 3600 );

		$option_name = 'transient_clean_';
		if ( $clear_all ) { $option_name .= 'all'; } else { $option_name .= 'expired'; }
		update_option( $option_name, $results );

		// Optimize the table after the deletions

		if ( ( ( $options[ 'upgrade_optimize' ] ) && ( $clear_all ) ) or ( ( $options[ 'clean_optimize' ] ) && ( !$clear_all ) ) ) {
			$wpdb -> query( "OPTIMIZE TABLE $wpdb->options" );
		}
	}

	return $cleaned;
}
?>