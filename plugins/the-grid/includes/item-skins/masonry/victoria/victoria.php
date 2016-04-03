<?php

global $tg_skins_preview;

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => '<i class="tg-icon-link"></i>', // Button link icon
		'comment'    => '<i class="tg-icon-chat"></i>', // Button link icon
		'image'      => ($tg_skins_preview) ? '<i class="tg-icon-play"></i>' : '<i class="tg-icon-arrows-out-2"></i>',  // Ligthbox icon
		'audio'      => '<i class="tg-icon-play"></i>', // Audio icon
		'video'      => '<i class="tg-icon-play"></i>', // HTML Video icon
		'vimeo'      => '<i class="tg-icon-play"></i>', // Vimeo icon
		'wistia'     => '<i class="tg-icon-play"></i>', // Wistia icon
		'youtube'    => '<i class="tg-icon-play"></i>', // Youtube icon
		'soundcloud' => '<i class="tg-icon-play"></i>', // SoundCloud icon
	),
	'excerpt_length'  => 0,       // Excerpt character length
	'excerpt_tag'     => '',      // Excerpt more tag
	'read_more'       => '',      // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'color', // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => true,    // Add link to term
	'term_separator'  => ', ',    // Term separator
	'author_prefix'   => __( 'By', 'tg-text-domain' ).' ', // Author prefix like 'By',...
	'avatar'          => false     // Add author avatar
);

// If function do not exists, then return immediately
if (!function_exists('The_Grid_Item_Content')) {
	return;
}

// Main Func/Class to retrieve all necessary item content/markup
$content = The_Grid_Item_Content($options);

$html = null;

$media_button = $content['media_button'];

// background image for quote/link
$bg_img = (isset($content['media_data']['url'])) ? '<div class="tg-item-image" style="background-image: url('.$content['media_data']['url'].')"></div>' : null;
$author = preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['content']['span'].'">', $content['author']);

if (isset($content['quote_markup'])) {

	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-quote-icon tg-icon-quote" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['quote_markup'];
	$html .= $content['content_wrapper_end'];
	
} else if (isset($content['link_markup'])) {

	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-link-icon tg-icon-link" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['link_markup'];
	$html .= $content['content_wrapper_end'];
	
} else {
		
	$html .= $content['media_wrapper_start'];
		$html .= $content['media_markup'];
		$html .= (!empty($media_button)) ? $content['center_wrapper_start'] : null; // Wrapper Start to center content inside
			$html .= $content['overlay'].$media_button;  // Lightbox/Play button
		$html .= (!empty($media_button)) ? $content['center_wrapper_end'] : null;   // Wrapper End to center content inside
		$html .= (isset($content['duration'])) ? $content['duration'] : null;
	$html .= $content['media_wrapper_end'];
		
	$html .= $content['content_wrapper_start'];
		$html .= $content['title'];
		$html .= $author;
		$html .= $content['date'];
		$html .= (isset($content['views'])) ? $content['views'] : null;
	$html .= $content['content_wrapper_end'];

}
		
return $html;