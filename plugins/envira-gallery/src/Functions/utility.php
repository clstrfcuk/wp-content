<?php
/**
 * Envira Utility Functions.
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

use Envira\Frontend\Background;
use Envira\Utils\Mobile_Detect;

if (! function_exists('array_column')) :

	function array_column( array $input, $columnKey, $indexKey = null ) {

		$array = array();

		foreach ($input as $value) {

			if ( !array_key_exists($columnKey, $value)) {

				return false;

			}

			if (is_null($indexKey)) {

				$array[] = $value[$columnKey];

			} else {

				if ( !array_key_exists($indexKey, $value) || ! is_scalar($value[$indexKey]) ) {

	                return false;

				}

	            $array[$value[$indexKey]] = $value[$columnKey];

			}

		}

		return $array;

	}

endif;

/**
 * Helper Method for Size Conversions
 *
 * @author Chris Christoff
 * @since 1.7.0
 *
 * @param  unknown    $v
 * @return int|string
 */
function envira_let_to_num( $v ) {

	$l    = substr( $v, -1 );
	$ret  = substr( $v, 0, -1 );

	switch ( strtoupper( $l ) ) {

		case 'P': // fall-through
		case 'T': // fall-through
		case 'G': // fall-through
		case 'M': // fall-through
		case 'K': // fall-through

			$ret *= 1024;
			break;

		default:
		break;

	}

	return $ret;
}

/**
 * Helper function to detect mobile.
 *
 * @since 1.7.0
 *
 * @access public
 * @return void
 */
function envira_mobile_detect(){
	return new Envira\Utils\Mobile_Detect;
}
function envira_is_whitelabel(){
	return apply_filters('envira_whitelabel', false );
}
/**
 * Utility function for debugging
 *
 * @since 1.7.0
 *
 * @access public
 * @param array $array (default: array())
 * @return void
 */
function envira_pretty_print( $array = array() ){

	echo '<pre> ' . print_r( $array ) . '</pre>';

}

/**
 * Helper Method to call background requests
 *
 * @since 1.7.0
 *
 * @access public
 * @param mixed $data
 * @param mixed $type
 * @return void
 */
function envira_background_request( $data, $type ){

	if ( !is_array( $data ) || !isset( $type )  ){

		return false;

	}

	$background = new Envira\Frontend\Background;
	$background->background_request( $data, $type );

}

/**
 * Utility Function to log errors.
 *
 * @since 1.8.0
 *
 * @access public
 * @param string $content
 * @param mixed $data
 * @return void
 */
function envira_log_error( $content = null, $data = null ){

	if (  !defined('ENVIRA_DEBUG') || !ENVIRA_DEBUG ){

		return false;

	} else {

		if ( !is_array( $data ) ){

			error_log( strtoupper( $content ) . ':' . PHP_EOL . $data );

		} else {

			error_log( strtoupper( $content ) . ':' . PHP_EOL . print_r( $data, true ) );

		}

	}

}