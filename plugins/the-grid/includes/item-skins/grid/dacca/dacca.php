<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => ' ', // Button link icon
		'comment'    => ' ',                             // Button link icon
		'image'      => ' ',  // Ligthbox icon
		'audio'      => ' ', // Audio icon
		'video'      => ' ', // HTML Video icon
		'vimeo'      => ' ', // Vimeo icon
		'wistia'     => ' ', // Wistia icon
		'youtube'    => ' ', // Youtube icon
		'soundcloud' => ' ', // SoundCloud icon
	),
	'excerpt_length'  => 0,       // Excerpt character length
	'excerpt_tag'     => '...',   // Excerpt more tag
	'read_more'       => '', // Read more text
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
				
$content_class = $content['colors']['overlay']['class'];

$html  = '<div class="tg-atv-anim">';	
	$html .= '<div class="tg-atv-shadow"></div>';
	$html .= $content['media_wrapper_start'];
		$html .= $content['media_markup'];
	$html .= $content['media_wrapper_end'];
	$html .= $content['overlay'];	
	$html .= '<div class="tg-item-content-holder '.$content_class.' tg-item-atv-layer">';
		$html .= '<div class="tg-item-content-inner">';	
			$html .= $content['center_wrapper_start'];
				$html .= $content['title'];
				$html .= $content['terms'];
			$html .= $content['center_wrapper_end'];
		$html .= '</div>';
	$html .= '</div>';
	$html .= $content['media_button'];
	$html .= (!empty($content['permalink'])) ? '<a class="tg-item-link" href="'.$content['permalink'].'" target="'.$content['target'].'"></a>' : null;
$html .= '</div>';
		
return $html;