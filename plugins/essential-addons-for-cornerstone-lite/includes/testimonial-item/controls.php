<?php

/**
 * Element Controls: Testimonial Item
 */

return array(

	// Title

	'heading' => array(
		'type' => 'title',
		'suggest' => __( 'Testimonial Item', 'essential-addons-cs' ),
	),


	//
	// Background Color
	//

	'slide_bg_color' => array( 'mixin' => 'background_color' ),


	// User Avatar

	'show_avatar' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Show Avatar?', 'essential-addons-cs' ),
			'tooltip' => __( 'Disable avatar for minimal slider', 'essential-addons-cs' ),
		)
	),

	'image' => array(
		'type' => 'image',
		'ui' => array(
			'title' => __( 'User Avatar (squared)', 'essential-addons-cs' ),
      'tooltip' => __( 'Choose a avatar image for user, use squared image', 'essential-addons-cs' ),
		),
		'condition' => array(
      'show_avatar' => true
    )

  ),

	// ALt Tag

	'alt_tag' => array(
	'type' => 'text',
	'ui' => array(
	  'title' => __( 'ALT Tag', 'essential-addons-cs' ),
	  'tooltip' => __( 'Provide ALT Tag for Image', 'essential-addons-cs' )
	),
	'context' => 'content',
	'suggest' => __( '', 'essential-addons-cs' )
	),
	
	'image_margin' => array(
	 	'type' => 'dimensions',
	 	'ui' => array(
			'title'   => __( 'Image Margin', 'essential-addons-cs' )
		),
		'condition' => array(
      'show_avatar' => true
    )
		
	),

	'image_width' => array(
		'type'    => 'text',
		'ui' => array(
			'title'   => __( 'Image width', 'essential-addons-cs' ),
			'tooltip' => __( 'Set a width for user avatar.', 'essential-addons-cs' ),
		),
		'context' => 'content',
    'suggest' => __( '150px', 'essential-addons-cs' ),
		'condition' => array(
      'show_avatar' => true
    )
		
	),


	'rounded_image' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Rounded Avatar?', 'essential-addons-cs' ),
			'tooltip' => __( 'Make the image rounded', 'essential-addons-cs' ),
		),
		'condition' => array(
      'show_avatar' => true
    )
	),

	// Border width

	'image_border_width' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __( 'Border width', 'essential-addons-cs' ),
			'tooltip' => __( 'Set border width in pixel value', 'essential-addons-cs' ),
		),
    'suggest' => __( '1', 'essential-addons-cs' ),

		'condition' => array(
      'show_avatar' => true
    )
	),

	// Border Color

	'image_border_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Border Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set border color', 'essential-addons-cs' ),
	    ),

		'condition' => array(
      'show_avatar' => true
    )

	),


	// User name 

	'testimonial_user_name' => array(
		'type'    => 'text',
		'ui' => array(
			'title'   => __( 'User name', 'essential-addons-cs' ),
			'tooltip' => __( 'Provide User name.', 'essential-addons-cs' ),
		),
		'context' => 'content',
    'suggest' => __( 'John Doe', 'essential-addons-cs' )
	),

	//
	// Text Color
	//

	'testimonial_user_text_color' => array(
	 	'type' => 'color',
	 	'ui' => array(
			'title'   => __( 'User Text Color', 'essential-addons-cs' )
		)
	),

	// User company 

	'testimonial_user_company' => array(
		'type'    => 'text',
		'ui' => array(
			'title'   => __( 'User Company name', 'essential-addons-cs' ),
			'tooltip' => __( 'Provide User company.', 'essential-addons-cs' ),
		),
		'context' => 'content',
    'suggest' => __( 'Envato', 'essential-addons-cs' )
	),

	// Company Text Color

	'testimonial_user_company_text_color' => array(
	 	'type' => 'color',
	 	'ui' => array(
			'title'   => __( 'User Company Text Color', 'essential-addons-cs' )
		)
	),

	// Quote mark Color

	'testimonial_quotation_mark_color' => array(
	 	'type' => 'color',
	 	'ui' => array(
			'title'   => __( 'Quotation Mark Color', 'essential-addons-cs' )
		)
	),

	// Content

	'content' => array(
		'type'    => 'editor',

		'context' => 'content',
		'suggest' => __( 'Click to inspect, then add testimonial content here.', 'essential-addons-cs' ),
	),




);