<?php

function atc_get_options() {

	$options = get_option( 'transient_clean_options' );

	// Add defaults to array

	if ( !is_array( $options ) ) {

		$options = array(
						'clean_enable' => true,
						'clean_optimize' => false,
						'upgrade_enable' => true,
						'upgrade_optimize' => true
						);

		update_option( 'transient_clean_options', $options );
	}

	return $options;

}
?>