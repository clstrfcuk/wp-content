<?php

/**
 * Element Definition: "Product Grid"
 */

class EACS_Product_Grid {

	public function ui() {
		return array(
			'name'        => 'eacs-product-grid',
     		'title' => __( 'EA Product Grid', 'essential-addons-cs' ),
     		'icon_group' => 'essential-addons-cs',
     		// 'icon_id' => 'eacs-product-grid'
    );
	}

	public function flags() {
		return array(
			'dynamic_child' => false
		);
	}

}
