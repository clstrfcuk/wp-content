<?php

/**
 * Element Controls : Post Grid
 */

return array(

	// Post Type

	'post_type' => array(
		'type' => 'select',
		'ui'   => array(
			'title' => __( 'Post Type', 'essential-addons-cs' ),
      'tooltip' => __( 'Choose between standard posts or portfolio posts.', 'essential-addons-cs' ),
		),
		'options' => array(
			'choices' => array(
        array( 'value' => 'post',        'label' => __( 'Post', 'essential-addons-cs' ) ),
        array( 'value' => 'portfolio',    'label' => __( 'Portfolio', 'essential-addons-cs' ) )
      ),
		),
	),


	// Post Count

	'max_post_count' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __( 'Post Count', 'essential-addons-cs' ),
			'tooltip' => __( 'Set how many post you want to display.', 'essential-addons-cs' ),
		),
    'suggest' => __( '4', 'essential-addons-cs' ),
	),


	// Column Number

	'post_grid_column' => array(
		'type' => 'select',
		'ui'   => array(
			'title' => __( 'Number of Columns', 'essential-addons-cs' ),
      'tooltip' => __( 'Set the column number for layout', 'essential-addons-cs' ),
		),
		'options' => array(
			'choices' => array(
        array( 'value' => 'eacs-col-1',        'label' => __( '1', 'essential-addons-cs' ) ),
        array( 'value' => 'eacs-col-2',    'label' => __( '2', 'essential-addons-cs' ) ),
        array( 'value' => 'eacs-col-3',    'label' => __( '3', 'essential-addons-cs' ) ),
        array( 'value' => 'eacs-col-4',    'label' => __( '4', 'essential-addons-cs' ) )
      ),
		),
	),


	// Offset

	'offset' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __( 'Offset', 'essential-addons-cs' ),
			'tooltip' => __( 'Enter a number to offset initial starting post of your Recent Posts', 'essential-addons-cs' ),
		),
    'suggest' => __( '4', 'essential-addons-cs' ),
	),

	// Spacing

	'item_spacing' => array(
	 	'type' => 'dimensions',
	 	'ui' => array(
			'title'   => __( 'Spacing between items (px)',  'essential-addons-cs' ),
			'tooltip' => __( 'Set spacing between the post items.', 'essential-addons-cs' ),
		)
	),


	// Show Excerpt

	'show_excerpt' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Show Excerpt?', 'essential-addons-cs' ),
			'tooltip' => __( 'Show or hide excerpt', 'essential-addons-cs' ),
		)
	),


	// Excerpt length

	'excerpt_length' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __( 'Excerpt Length', 'essential-addons-cs' ),
			'tooltip' => __( 'Enter the number of words you want to show as excerpts', 'essential-addons-cs' ),
		),
		'condition' => array(
      'show_excerpt' => true
    ),
    'suggest' => __( '20', 'essential-addons-cs' ),
	),

	// Hide Featured Image

	'hide_featured_image' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Hide Featured Image', 'essential-addons-cs' ),
			'tooltip' => __( 'Hide or Show featured image', 'essential-addons-cs' ),
		)
	),


	// Hide Meta

	'hide_post_meta' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Hide Meta Info', 'essential-addons-cs' ),
			'tooltip' => __( 'Hide or Show post meta information', 'essential-addons-cs' ),
		)
	),

	// Meta Position

	'meta_position' => array(
		'type' => 'select',
		'ui'   => array(
			'title' => __( 'Meta Position', 'essential-addons-cs' ),
      'tooltip' => __( 'Choose where to display meta info', 'essential-addons-cs' ),
		),
		'options' => array(
			'choices' => array(
        array( 'value' => 'entry-header',        'label' => __( 'Entry Header', 'essential-addons-cs' ) ),
        array( 'value' => 'entry-footer',    'label' => __( 'Entry Footer', 'essential-addons-cs' ) )
      ),
		),
		'condition' => array(
      'hide_post_meta' => false
    ),
	),


	// Category

	'category' => array(
	'type' => 'text',
	'ui' => array(
	  'title' => __( 'Category', 'essential-addons-cs' ),
	  'tooltip' => __( 'To filter your posts by category, enter in the slug of your desired category. To filter by multiple categories, enter in your slugs separated by a comma.', 'essential-addons-cs' )
	),
	'context' => 'category',
	'suggest' => ''
	),



	//
	// Text Align
	//

	'post_alignment' => array(
		'type' => 'choose',
		'ui' => array(
			'title' => __( 'Set Alignment', 'essential-addons-cs' ),
			'tooltip' =>__( 'Set a alignment for the text and image',  'essential-addons-cs' ),
		),
		'options' => array(
			'columns' => '4',
			'offValue' => '',
			'choices' => array(
				array( 'value' => 'eacs-post-align-default', 'icon' => fa_entity( 'ban' ),    'tooltip' => __( 'Default', 'essential-addons-cs' ) ),
				array( 'value' => 'eacs-post-align-left', 'icon' => fa_entity( 'align-left' ),    'tooltip' => __( 'Left', 'essential-addons-cs' ) ),
				array( 'value' => 'eacs-post-align-centered', 'icon' => fa_entity( 'align-center' ),  'tooltip' => __( 'Center', 'essential-addons-cs' ) ),
				array( 'value' => 'eacs-post-align-right', 'icon' => fa_entity( 'align-right' ),   'tooltip' => __( 'Right', 'essential-addons-cs' ) )
			)
		)
	),

	// Post Background Color


	'post_background_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Post Background Color)', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set background color for post', 'essential-addons-cs' ),
	    )
	),

	// Thumbnail Overlay color


	'thumbnail_overlay_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Thumbnail Overlay Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set overlay color that will show on hover', 'essential-addons-cs' ),
	    )
	),

	// Post Title Color


	'post_title_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Post Title Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set Post Title Color', 'essential-addons-cs' ),
	    )
	),

	// Post Title hover Color


	'post_title_hover_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Post Title Hover Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set Post Title Hover Color', 'essential-addons-cs' ),
	    )
	),

	// Post title font size

	'post_title_font_size' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __( 'Post Title Font Size (px)', 'essential-addons-cs' ),
			'tooltip' => __( 'Set the font-size for post title', 'essential-addons-cs' ),
		),
    'suggest' => __( '18', 'essential-addons-cs' ),
	),


	// Post Excerpt Color

	'post_excerpt_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Post Excerpt Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set Post Excerpt Color', 'essential-addons-cs' ),
	    )

	),

	// Post Meta Color

	'post_meta_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Post Meta Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set Post Meta Color', 'essential-addons-cs' ),
	    )

	),


	//
	// Visibility
	//

	'visibility' => array(
		'type' => 'multi-choose',
		'ui' => array(
			'title' => __( 'Hide based on screen width', 'essential-addons-cs' ),
			'toolip' => __( 'Hide this element at different screen widths. Keep in mind that the &ldquo;Extra Large&rdquo; toggle is 1200px+, so you may not see your element disappear if your preview window is not large enough.', 'essential-addons-cs' )
		),
		'options' => array(
			'columns' => '5',
			'choices' => array(
				array( 'value' => 'xl', 'icon' => fa_entity( 'desktop' ), 'tooltip' => __( 'XL', 'essential-addons-cs' ) ),
				array( 'value' => 'lg', 'icon' => fa_entity( 'laptop' ),  'tooltip' => __( 'LG', 'essential-addons-cs' ) ),
				array( 'value' => 'md', 'icon' => fa_entity( 'tablet' ),  'tooltip' => __( 'MD', 'essential-addons-cs' ) ),
				array( 'value' => 'sm', 'icon' => fa_entity( 'tablet' ),  'tooltip' => __( 'SM', 'essential-addons-cs' ) ),
				array( 'value' => 'xs', 'icon' => fa_entity( 'mobile' ),  'tooltip' => __( 'XS', 'essential-addons-cs' ) ),
			)
		)
	)

);