<?php

/**
 * Element Controls : Product Grid
 */

return array(


	// Presets

	'preset_style' => array(
		'type' => 'select',
		'ui'   => array(
			'title' => __( 'Set Style Preset', 'essential-addons-cs' ),
      'tooltip' => __( 'Select the preset style or use theme style', 'essential-addons-cs' ),
		),
		'options' => array(
			'choices' => array(
        array( 'value' => 'preset-theme-1',    'label' => __( 'Simple Style', 'essential-addons-cs' ) ),
        array( 'value' => 'preset-theme-2',    'label' => __( 'Reveal Style', 'essential-addons-cs' ) ),
        array( 'value' => 'preset-theme-3',    'label' => __( 'Overlay Style', 'essential-addons-cs' ) ),
        array( 'value' => 'preset-theme-4',    'label' => __( 'None (Use Theme Style)', 'essential-addons-cs' ) )
      ),
		),
	),

	// Number of Columns

	'product_columns' => array(
		'type' => 'select',
		'ui'   => array(
			'title' => __( 'Number of Columns', 'essential-addons-cs' ),
      'tooltip' => __( 'Set the column numbers for products', 'essential-addons-cs' ),
		),
		'options' => array(
			'choices' => array(
        array( 'value' => 'single_column',      'label' => __( 'Single Column', 'essential-addons-cs' ) ),
        array( 'value' => 'two_columns',        'label' => __( 'Two Columns', 'essential-addons-cs' ) ),
        array( 'value' => 'three_columns',      'label' => __( 'Three Columns', 'essential-addons-cs' ) ),
        array( 'value' => 'four_columns',       'label' => __( 'Four Columns', 'essential-addons-cs' ) )
      ),
		),
	),


	// Product Background/Overlay Color


	'product_bg_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Product Background/Overlay Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set background color for product', 'essential-addons-cs' ),
	    )

	),


	// Product Text Color


	'product_text_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Product Text Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set color for product title, price etc.', 'essential-addons-cs' ),
	    )

	),

	// Cart button Background Color


	'cart_bg_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Cart Button Background Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set background color for add to cart', 'essential-addons-cs' ),
	    )

	),

	// Cart button border Color


	'cart_border_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Cart Button Border Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set border color for add to cart', 'essential-addons-cs' ),
	    )

	),

	// Cart button text Color


	'cart_text_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Cart Text Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set text color for add to cart', 'essential-addons-cs' ),
	    )

	),

	// Product Count

	'max_product_count' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __( 'Product Count', 'essential-addons-cs' ),
			'tooltip' => __( 'Set how many product you want to display.', 'essential-addons-cs' ),
		),
    'suggest' => __( '12', 'essential-addons-cs' ),
	),

	// Category

	'category' => array(
	'type' => 'text',
	'ui' => array(
	  'title' => __( 'Category', 'essential-addons-cs' ),
	  'tooltip' => __( 'To filter your products by category, enter in the slug of your desired category. To filter by multiple categories, enter in your slugs separated by a comma.', 'essential-addons-cs' )
	),
	'context' => 'category',
	'suggest' => ''
	),


	// Add border

	'add_border' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Add border to items?', 'essential-addons-cs' ),
			'tooltip' => __( 'Add border to each item', 'essential-addons-cs' ),
		)
	),

	// Border width

	'product_border_width' => array(
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

	'product_border_color' => array(
	    'type' => 'color',
	    'ui' => array(
	        'title'   => __( 'Border Color', 'essential-addons-cs' ),
	        'tooltip' => __( 'Set border color', 'essential-addons-cs' ),
	    ),

		'condition' => array(
      'add_border' => true
    )

	),

	// Show product rating

	'show_rating' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __( 'Show Product Rating', 'essential-addons-cs' ),
			'tooltip' => __( 'Show or hide the product ratings', 'essential-addons-cs' ),
		)
	),


	//
	// Visibility
	//

	'visibility' => array(
		'type' => 'multi-choose',
		'ui' => array(
			'title' => __( 'Hide based on screen width', 'essential-addons-cs' ),
			'toolip' => __( 'Hide this element at different screen widths. Keep in mind that the &ldquo;Extra Large&rdquo; toggle is 1200px+, so you may not see your element disappear if your preview window is not large enough.', 'cornerstone' )
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