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
	'read_more'       => __( 'Read More', 'tg-text-domain' ), // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'color', // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => true,    // Add link to term
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
				
$media_button = $content['media_button'];
$link_button  = $content['link_button'];
		
$html  = null;

$margin = (empty($content['product_price'])) ? ' no-margin' : null;

$html .= $content['media_wrapper_start'];
	$html .= $content['media_markup'];
	$html .= $content['product_sale'];
$html .= $content['media_wrapper_end'];
$html .= '<div class="tg-item-content-inner '.$content['colors']['overlay']['class'].$margin.'">';
	$html .= $content['overlay'];
	$html .= $content['center_wrapper_start'];
		$link  = ($content['permalink']) ? '<a class="tg-item-link" href="'.$content['permalink'].'" target="'.$content['target'].'"></a>' : null;
		$html .= (!empty($content['media_button']) && !empty($content['link_button'])) ? $link : null; 
		$html .= preg_replace('/(<span\b[^><]*)>/i', '$1 style="color:'.$content['colors']['overlay']['title'].'">', $content['product_price']);
		$html .= $content['title'];
		$html .= preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['overlay']['title'].'">', $content['product_cart_button']);
	$html .= $content['center_wrapper_end'];
	$html .= $content['product_rating'];
	$html .= $content['product_wishlist'];
$html .= '</div>';	


		
return $html;