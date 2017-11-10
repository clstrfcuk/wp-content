<?php

/**
 * Element Definition: "Testimonial Slider"
 */

class EACS_Testimonial_Slider {

	public function ui() {
		return array(
			'name'        => 'eacs-testimonial-slider',
      		'title'	=> __( 'EA Testimonial Slider', 'essential-addons-cs' ),
      		'icon_group' => 'essential-addons-cs',
      		'icon_id' => 'eacs-testimonial-slider'
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