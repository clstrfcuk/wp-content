<?php

/**
 * Element Shortcode: Icon List Item
 */

$atts = cs_atts( array(
	'id' => $id,
	'class' => trim( 'x-li-icon ' . $class ),
	'style' => $style
) );

$icon_atts = cs_atts( array(
	'class' => 'x-icon-' . $type,
	'data-x-icon' => fa_entity( $type ),
	'aria-hidden' => 'true'
) );

echo "<li {$atts} ><i {$icon_atts} ></i>" . do_shortcode( $content ) . "</li>";