<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => __( 'Read More', 'tg-text-domain' ),  // Button link icon
		'comment'    => '',                                   // Button link icon
		'image'      => '<i class="tg-icon-add"></i>',        // Ligthbox icon
		'audio'      => __( 'Play Song', 'tg-text-domain' ),  // Audio icon
		'video'      => __( 'Play Video', 'tg-text-domain' ), // HTML Video icon
		'vimeo'      => __( 'Play Video', 'tg-text-domain' ), // Vimeo icon
		'wistia'     => __( 'Play Video', 'tg-text-domain' ), // Wistia icon
		'youtube'    => __( 'Play Video', 'tg-text-domain' ), // Youtube icon
		'soundcloud' => __( 'Play Song', 'tg-text-domain' ),  // SoundCloud icon
	),
	'excerpt_length'  => 200,     // Excerpt character length
	'excerpt_tag'     => '...',   // Excerpt more tag
	'read_more'       => __( 'Read More', 'tg-text-domain' ), // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'background', // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => true,    // Add link to term
	'term_separator'  => '',      // Term separator
	'author_prefix'   => __( 'By', 'tg-text-domain' ).' ', // Author prefix like 'By',...
	'avatar'          => false    // Add author avatar
);

// If function do not exists, then return immediately
if (!function_exists('The_Grid_Item_Content')) {
	return;
}

// Main Func/Class to retrieve all necessary item content/markup
$content = The_Grid_Item_Content($options);

$html = null;

// background image for quote/link
$bg_img = (isset($content['media_data']['url'])) ? '<div class="tg-item-image" style="background-image: url('.$content['media_data']['url'].')"></div>' : null;

// change color of author & comments
$author   = preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $content['author']);
$comments = preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $content['comments']['markup']);
$comments = preg_replace('/(<i\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $comments);
	
if (isset($content['quote_markup'])) {
	
	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-quote-icon tg-icon-quote" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['quote_markup'];
	$html .= '<div class="tg-item-footer">';
	$html .= $content['date'];
	$html .= '<span>/</span>';
	$html .= $comments;
	$html .= '</div>';
	$html .= $content['content_wrapper_end'];
	
} else if (isset($content['link_markup'])) {
	
	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-link-icon tg-icon-link" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['link_markup'];
	$html .= '<div class="tg-item-footer">';
	$html .= $content['date'];
	$html .= '<span>/</span>';
	$html .= $comments;
	$html .= '</div>';
	$html .= $content['content_wrapper_end'];
	
} else {
	
	$media_button = $content['media_button'];
	$link_button  = $content['link_button'];

	$html .= $content['media_wrapper_start'];
		$html .= $content['media_markup'];
		$html .= (!empty($media_button)) ? $content['overlay'] : null;
		$html .= (!empty($media_button)) ? $content['center_wrapper_start'] : null;
			$html .= ($content['media_type'] !== 'image' && $content['media_type'] !== 'gallery') ? $media_button : null; 
			$html .= ($content['media_type']  == 'image' || $content['media_type']  == 'gallery') ? $link_button  : null;
		$html .= (!empty($media_button)) ? $content['center_wrapper_end'] : null;
	$html .= $content['media_wrapper_end'];
	
	$html .= $content['content_wrapper_start'];
		$html .= $content['terms'];
		$html .= $content['title'];
		$html .= $content['content'];
		$html .= $content['read_more'];
		$html .= '<div class="tg-item-footer">';
		$html .= $content['date'];
		$html .= (!empty($comments)) ? '<span>/</span>' : null;
		$html .= $comments;
		$html .= '</div>';
	$html .= $content['content_wrapper_end'];
	
}
		
return $html;