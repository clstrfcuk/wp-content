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


}