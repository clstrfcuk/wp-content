<?php
/**
 * Envira Serialization Functions.
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
 * Fix a serialized string
 *
 * @since 1.3.1.6
 *
 * @param string $string Serialized string to fix
 * @return array Unserialized data
 */
function envira_fix_serialized_string( $string ) {

	// Check string is serialised and if it already works return it
	if ( !preg_match( '/^[aOs]:/', $string ) ) {
		return $string;
	}
	if ( @unserialize( $string ) !== false ) {
		return @unserialize( $string );
	}

	// String needs fixing - fix it
	$string = preg_replace_callback( '/\bs:(\d+):"(.*?)"/', array( $this, 'fix_str_length' ), $string );

	return unserialize( $string );

}

/**
 * Callback function for replacing the string's length paramter on a broken
 * serialized string
 *
 * @since 1.3.1.6
 *
 * @param array $matches preg_replace matches
 * @return string Replacement string
 */
function envira_fix_str_length( $matches ) {

	$string = $matches[2];
	$right_length = strlen( $string );

	return 's:' . $right_length . ':"' . $string . '"';

}