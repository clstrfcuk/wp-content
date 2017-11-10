<?php

/**
 * Element Controls: Logo Carousel Item
 */

return array(

	// Title

	'heading' => array(
		'type' => 'title',
		'suggest' => __( 'Carousel Item', 'essential-addons-cs' ),
	),

	// Content

	'image' => array(
		'type' => 'image',
		'ui' => array(
			'title' => __( 'Image', 'essential-addons-cs' ),
      'tooltip' => __( 'Choose a logo image to display on carousel.', 'essential-addons-cs' ),
		)
  ),

	'image_padding' => array(
	 	'type' => 'dimensions',
	 	'ui' => array(
			'title'   => __( 'Image Padding', 'essential-addons-cs' )
		)
	),

	// URL

	'logo_url' => array(
	'type' => 'text',
	'ui' => array(
	  'title' => __( 'Link URL', 'essential-addons-cs' ),
	  'tooltip' => __( 'Provide URL to link the logo', 'essential-addons-cs' )
	),
	'context' => 'content',
	'suggest' => __( 'https://codetic.net', 'essential-addons-cs' )
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

	// Open in New window

	'link_target' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Open in new window', 'essential-addons-cs' ),
			'tooltip' => __( 'Enable if you want to open the link in new tab', 'essential-addons-cs' ),
		)
	),
);