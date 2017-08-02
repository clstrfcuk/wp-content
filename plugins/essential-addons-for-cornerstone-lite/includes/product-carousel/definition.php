<?php

/**
 * Element Definition: "Product Carousel"
 */

class EACS_Product_Carousel {

	public function ui() {
		return array(
			'name'        => 'eacs-product-carousel',
     		'title' => __( 'EA Product Carousel', 'essential-addons-cs' ),
     		'icon_group' => 'essential-addons-cs',
     		'icon_id' => 'eacs-product-carousel'
    );
	}

	public function flags() {
		// dynamic_child allows child elements to render individually, but may cause
		// styling or behavioral issues in the page builder depending on how your
		// shortcodes work. If you have trouble with element presentation, try
		// removing this flag.
		return array(
			'dynamic_child' => false
		);
	}

}
