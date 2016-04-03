<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => '<i class="tg-icon-link"></i>', // Button link icon
		'comment'    => '',                             // Button link icon
		'image'      => '<i class="tg-icon-arrows-out-2"></i>',  // Ligthbox icon
		'audio'      => '<i class="tg-icon-play"></i>', // Audio icon
		'video'      => '<i class="tg-icon-play"></i>', // HTML Video icon
		'vimeo'      => '<i class="tg-icon-play"></i>', // Vimeo icon
		'wistia'     => '<i class="tg-icon-play"></i>', // Wistia icon
		'youtube'    => '<i class="tg-icon-play"></i>', // Youtube icon
		'soundcloud' => '<i class="tg-icon-play"></i>', // SoundCloud icon
	),
	'excerpt_length'  => 0,       // Excerpt character length
	'excerpt_tag'     => '...',   // Excerpt more tag
	'read_more'       => '',      // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => false,   // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'none',  // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => false,   // Add link to term
	'term_separator'  => ', ',    // Term separator
	'author_prefix'   => '',      // Author prefix like 'By',...
	'avatar'          => false    // Add author avatar
);

// If function do not exists, then return immediately
if (!function_exists('The_Grid_Item_Content')) {
	return;
}

// Main Func/Class to retrieve all necessary item content/markup
$content = The_Grid_Item_Content($options);
		
$html  = null;

$bg     = 'style="background:'.$content['colors']['overlay']['title'].'"';
$loader = '<div class="tg-woo-loading"><span class="dot1" '.$bg.'></span><span class="dot2" '.$bg.'></span></div>';
$cart   = str_replace('</div>',$loader.'</div>',$content['product_cart_button']);

$html .= $content['media_wrapper_start'];
	$html .= $content['media_markup'];
	$html .= (!empty($content['media_button']) && !empty($content['permalink'])) ? '<a class="tg-woo-link" href="'.$content['permalink'].'" target="'.$content['target'].'"></a>' : null;
	$html .= (!empty($content['media_button'])) ? $content['product_sale'] : null;
	$html .= (!empty($content['media_button'])) ? $content['product_rating'] : null;
	$html .= (!empty($content['media_button']) && !empty($cart)) ? '<div class="tg-item-cart-holder">' : null;
	$html .= (!empty($content['media_button']) && !empty($cart)) ? $content['overlay'] : null;
	$html .= (!empty($content['media_button']) && !empty($cart)) ? preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['overlay']['title'].'">', $cart) : null;
	$html .= (!empty($content['media_button']) && !empty($cart)) ? '</div>' : null;
$html .= $content['media_wrapper_end'];

$html .= $content['content_wrapper_start'];
	$html .= $content['title'];
	$html .= preg_replace('/(<span\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['title'].'">', $content['product_price']);	
	$html .= preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $content['product_wishlist']);
$html .= $content['content_wrapper_end'];
		
return $html;