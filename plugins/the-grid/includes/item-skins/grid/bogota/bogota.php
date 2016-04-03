<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => ' ', // Button link icon
		'comment'    => '<i class="tg-icon-chat-4"></i>', // Button link icon
		'image'      => ' ', // Ligthbox icon
		'audio'      => ' ', // Audio icon
		'video'      => ' ', // HTML Video icon
		'vimeo'      => ' ', // Vimeo icon
		'youtube'    => ' ', // Youtube icon
		'soundcloud' => ' ', // SoundCloud icon
	),
	'excerpt_length'  => 0,       // Excerpt character length
	'excerpt_tag'     => '',      // Excerpt more tag
	'read_more'       => '',      // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'color', // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => true,    // Add link to term
	'term_separator'  => ', ',    // Term separator
	'author_prefix'   => '',      // Author prefix like 'By',...
	'avatar'          => true     // Add author avatar
);

// If function do not exists, then return immediately
if (!function_exists('The_Grid_Item_Content')) {
	return;
}

// Main Func/Class to retrieve all necessary item content/markup
$content = The_Grid_Item_Content($options);

$html = null;

$media_button = $content['media_button'];
$like_button  = preg_replace('/(<span\b[^><]*)>/i', '$1 style="color:'.$content['colors']['overlay']['title'].'">', $content['post_like']);
$like_button  = preg_replace('/(<path\b[^><]*)>/i', '$1 style="stroke:'.$content['colors']['overlay']['title'].' !important;fill:'.$content['colors']['overlay']['title'].' !important">', $like_button);
$comments = preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$content['colors']['overlay']['title'].'">', $content['comments']['markup']);
$comments = preg_replace('/(<i\b[^><]*)>/i', '$1 style="color:'.$content['colors']['overlay']['title'].'">', $comments);
		
$html .= $content['media_wrapper_start'];
	$html .= $content['media_markup'];
	$html .= ($content['media_type'] == 'video') ? '<i class="tg-icon-play"></i>' : null;
	$html .= $content['overlay'];
	$html .= $content['center_wrapper_start'];
		$html .= $like_button;
		$html .= $comments;
	$html .= $content['center_wrapper_end'];
	$html .= $media_button;
$html .= $content['media_wrapper_end'];
		
return $html;