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


}