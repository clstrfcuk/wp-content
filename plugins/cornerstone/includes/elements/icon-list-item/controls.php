<?php

/**
 * Element Controls: Icon List Item
 */

return array(

	'title' => array(
		'type' => 'title',
		'context' => 'content',
		'suggest' => __( 'New Item', csl18n() ),
	),

	'type' => array(
		'type' => 'icon-choose',
		'ui'   => array(
			'title'   => __( 'Icon', csl18n() ),
      'tooltip' => __( 'Specify the icon you would like to use as the bullet for your Icon List Item.', csl18n() ),
		)
	),

);