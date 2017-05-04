<?php

// =============================================================================
// WOOCOMMERCE/SINGLE-PRODUCT/PRODUCT-THUMBNAILS.PHP
// -----------------------------------------------------------------------------
// @version 3.0.2
// =============================================================================

// Template Changes
// ----------------
// 01. Add classes to single product image (.x-img, .x-img-link,
//     .x-img-thumbnail, and .man).

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$attachment_ids = $product->get_gallery_image_ids();

if ( $attachment_ids && has_post_thumbnail() ) {
	foreach ( $attachment_ids as $attachment_id ) {
    $full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
    $thumbnail       = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
    $image_title     = get_post_field( 'post_excerpt', $attachment_id );

		$attributes = array(
			'title'                   => $image_title,
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);

		$html  = '<div data-thumb="' . esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" class="x-img x-img-link x-img-thumbnail man">'; // 01
		$html .= wp_get_attachment_image( $attachment_id, 'shop_single', false, $attributes );
 		$html .= '</a></div>';

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
	}
}