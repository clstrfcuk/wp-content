<?php

/**
 * Element Controls: Team Member Item
 */

return array(

	// Title

	'heading' => array(
		'type' => 'title',
		'suggest' => __( 'Team Member', 'essential-addons-cs' ),
	),


	//
	// Background Color
	//

	'slide_bg_color' => array( 'mixin' => 'background_color' ),


	// member Avatar

	'image' => array(
		'type' => 'image',
		'ui' => array(
			'title' => __( 'Member Avatar', 'essential-addons-cs' ),
      'tooltip' => __( 'Choose a avatar image for member, use squared image', 'essential-addons-cs' ),
		),
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
	),

	'image_width' => array(
		'type'    => 'text',
		'ui' => array(
			'title'   => __( 'Image width', 'essential-addons-cs' ),
			'tooltip' => __( 'Set a width for member avatar.', 'essential-addons-cs' ),
		),
		'context' => 'content',
    'suggest' => __( '150px', 'essential-addons-cs' ),
		
	),


	'rounded_image' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Rounded Avatar?', 'essential-addons-cs' ),
			'tooltip' => __( 'Make the image rounded', 'essential-addons-cs' ),
		),
	),


	// Add border

	'add_border' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Add border to image?', 'essential-addons-cs' ),
			'tooltip' => __( 'Add border to team member avatar', 'essential-addons-cs' ),
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
      'add_border' => true
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
      'add_border' => true
    )
	),


	// member name 

	'team_member_name' => array(
		'type'    => 'text',
		'ui' => array(
			'title'   => __( 'member name', 'essential-addons-cs' ),
			'tooltip' => __( 'Provide member name.', 'essential-addons-cs' ),
		),
		'context' => 'content',
    'suggest' => __( 'John Doe', 'essential-addons-cs' )
	),

	//
	// Text Color
	//

	'team_member_text_color' => array(
	 	'type' => 'color',
	 	'ui' => array(
			'title'   => __( 'Member Name Color', 'essential-addons-cs' )
		)
	),

	// Member pPosition

	'team_member_position' => array(
		'type'    => 'text',
		'ui' => array(
			'title'   => __( 'Team Member Position', 'essential-addons-cs' ),
			'tooltip' => __( 'Provide team member position.', 'essential-addons-cs' ),
		),
		'context' => 'content',
    'suggest' => __( 'Software Engineer', 'essential-addons-cs' )
	),
 
	// Member Position Text Color

	'team_member_position_text_color' => array(
	 	'type' => 'color',
	 	'ui' => array(
			'title'   => __( 'Member Position Color', 'essential-addons-cs' )
		)
	),


	// Content

	'content' => array(
		'type'    => 'editor',

		'context' => 'content',
		'suggest' => __( '<ul>
	<li><a href="https://facebook.com">[x_icon type="facebook-square"]</a> </li>
	<li><a href="https://twitter.com">[x_icon type="twitter-square"]</a> </li>
	<li><a href="https://linkedin.com">[x_icon type="linkedin-square"]</a> </li>
</ul>

Add team member description here. Remove the text if not necessary.', 'essential-addons-cs' ),
	),




);