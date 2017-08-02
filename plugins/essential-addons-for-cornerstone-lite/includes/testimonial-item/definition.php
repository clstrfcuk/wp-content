<?php

/**
 * Element Definition: "Testimonial Item"
 */

class EACS_Testimonial_Item {

	public function ui() {
		return array(
      'title' => __( 'Testimonial Item', 'essential-addons-cs' )
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