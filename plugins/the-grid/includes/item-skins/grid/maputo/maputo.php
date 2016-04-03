<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => '<i></i>', // Button link icon
		'comment'    => '<i class="tg-icon-chat-2"></i>', // Button link icon
		'image'      => '<i class="tg-icon-search3"></i>', // Ligthbox icon
		'audio'      => '<i class="tg-icon-play-2"></i>', // Audio icon
		'video'      => '<i class="tg-icon-play-2"></i>', // HTML Video icon
		'vimeo'      => '<i class="tg-icon-play-2"></i>', // Vimeo icon
		'wistia'     => '<i class="tg-icon-play-2"></i>',   // Wistia icon
		'youtube'    => '<i class="tg-icon-play-2"></i>', // Youtube icon
		'soundcloud' => '<i class="tg-icon-play-2"></i>', // SoundCloud icon
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
$media_button = preg_replace('/(<div\b[^><]*)>/i', '$1 style="background-color:'.$content['colors']['overlay']['background'].'">', $content['media_button']);
$link  = ($content['permalink']);
$link  = $link ? '<a class="tg-item-comment" href="'.$content['permalink'].'" target="'.$content['target'].'" style="background-color:'.$content['colors']['overlay']['background'].'">' : null;
$like_button  = preg_replace('/(<span\b[^><]*)>/i', '$1 style="background-color:'.$content['colors']['overlay']['background'].';color:'.$content['colors']['overlay']['title'].'">', $content['post_like']);
$like_button  = preg_replace('/(<path\b[^><]*)>/i', '$1 style="stroke:'.$content['colors']['overlay']['title'].' !important">', $like_button);
		
$html .= $content['media_wrapper_start'];
	$html .= $content['media_markup'];
	$html .= $content['overlay'];;
	$html .= $content['center_wrapper_start'];
		$html .= $media_button;
		$html .= ($link) ? $link : '<div class="tg-item-comment" style="background-color:'.$content['colors']['overlay']['background'].'">';
		$html .= '<i class="tg-icon-chat-2"></i>';
		$html .= '<span style="color:'.$content['colors']['overlay']['title'].'">'.$content['comments']['number'].'</span>';
		$html .= ($link) ? '</a>' : '</div>';
		$html .= $like_button;
	$html .= $content['center_wrapper_end'];
$html .= $content['media_wrapper_end'];

		
return $html;