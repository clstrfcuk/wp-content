<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => __( 'Read More', 'tg-text-domain' ),  // Button link icon
		'comment'    => '<i class="tg-icon-chat"></i>',       // Button link icon
		'image'      => '<i class="tg-icon-add"></i>',        // Ligthbox icon
		'audio'      => __( 'Play Song', 'tg-text-domain' ),  // Audio icon
		'video'      => __( 'Play Video', 'tg-text-domain' ), // HTML Video icon
		'vimeo'      => __( 'Play Video', 'tg-text-domain' ), // Vimeo icon
		'wistia'     => __( 'Play Video', 'tg-text-domain' ), // Wistia icon
		'youtube'    => __( 'Play Video', 'tg-text-domain' ), // Youtube icon
		'soundcloud' => __( 'Play Song', 'tg-text-domain' ),  // SoundCloud icon
	),
	'excerpt_length'  => 240,     // Excerpt character length
	'excerpt_tag'     => '...',   // Excerpt more tag
	'read_more'       => __( 'Read More', 'tg-text-domain' ).'<i class="tg-icon-arrow-next-thin"></i>', // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'color', // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => true,    // Add link to term
	'term_separator'  => ', ',    // Term separator
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
$author = preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['title'].'">', $content['author']);
$comments = preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $content['comments']['markup']);
$comments = preg_replace('/(<i\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $comments);
		
if (isset($content['quote_markup'])) {
	
	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-quote-icon tg-icon-quote" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['date'];
	$html .= $content['quote_markup'];
	$html .= '<div class="tg-item-footer">';
	$html .= $author;
	$html .= $content['post_like'];
	$html .= $comments;
	$html .= '</div>';
	$html .= $content['content_wrapper_end'];
	
} else if (isset($content['link_markup'])) {
	
	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-link-icon tg-icon-link" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['date'];
	$html .= $content['link_markup'];
	$html .= '<div class="tg-item-footer">';
	$html .= $author;
	$html .= $content['post_like'];
	$html .= $comments;
	$html .= '</div>';
	$html .= $content['content_wrapper_end'];
	
} else {
	
	$media_button = $content['media_button'];
	$link_button  = $content['link_button'];

	$html .= $content['content_wrapper_start'];
		$html .= $content['title'];
		$html .= $content['date'];
		$html .= $content['terms'];
	$html .= $content['content_wrapper_end'];
		
	$html .= $content['media_wrapper_start'];
		$html .= $content['media_markup'];
		$html .= (!empty($media_button)) ? $content['overlay'] : null;
		$html .= (!empty($media_button)) ? $content['center_wrapper_start'] : null;
			$html .= ($content['media_type'] !== 'image' && $content['media_type'] !== 'gallery') ? $media_button : null;
			$html .= ($content['media_type']  == 'image' || $content['media_type']  == 'gallery') ? $link_button  : null;
		$html .= (!empty($media_button)) ? $content['center_wrapper_end'] : null;
		
		if (!empty($content['social_links'])) {
			$html .= (!empty($media_button)) ? '<div class="tg-share-icons">' : null;
			$html .= (!empty($media_button)) ? $content['social_links']['facebook'] : null;
			$html .= (!empty($media_button)) ? $content['social_links']['twitter'] : null;
			$html .= (!empty($media_button)) ? $content['social_links']['google+'] : null;
			$html .= (!empty($media_button)) ? $content['social_links']['pinterest'] : null;
			$html .= (!empty($media_button)) ? '</div>' : null;
		}
		
	$html .= $content['media_wrapper_end'];
	
	$padding = ($content['media_type'] != 'audio') ? 'no-media-before' : null;
	
	$html .= (empty($media_button)) ? str_replace('tg-item-content-holder', 'tg-item-content-holder '.$padding, $content['content_wrapper_start']) : $content['content_wrapper_start'];
		$html .= $content['content'];
		$html .= '<div class="tg-item-footer">';
		$html .= $author;
		$html .= $content['post_like'];
		$html .= $comments;
		$html .= '</div>';
	$html .= $content['content_wrapper_end'];
	
}
		
return $html;