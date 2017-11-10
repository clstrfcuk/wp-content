<?php

/**
 * Element Definition: "Logo Carousel"
 */

class EACS_Logo_Carousel {

	public function ui() {
		return array(
			'name'        => 'eacs-logo-carousel',
      		'title'	=> __( 'EA Logo Carousel', 'essential-addons-cs' ),
      		'icon_group' => 'essential-addons-cs'
    );
	}

	public function flags() {
		return array(

			// dynamic_child allows child elements to render individually, but may cause
			// styling or behavioral issues in the page builder depending on how your
			// shortcodes work. If you have trouble with element presentation, try
			// removing this flag.
			'dynamic_child' => true,

			// Add this in addition to dynamic_child to trigger child preview renders
			// when parent attributes are updated.
			'linked_child' => true
		);
	}

}