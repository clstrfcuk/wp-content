<?php

/**
 * Element Definition: "Logo Carousel Item"
 */

class EACS_Logo_Carousel_Item {

	public function ui() {
		return array(
      'title' => __( 'Logo Carousel Item', 'essential-addons-cs' )
    );
	}

	public function flags() {
		return array(
      'child' => true
    );
	}

	// public function update_build_shortcode_atts( $atts, $parent ) {

	// 	if ( ! is_null( $parent ) ) {
	// 		$atts['linked'] = $parent['linked'];
	// 	}

	// 	return $atts;

	// }


}