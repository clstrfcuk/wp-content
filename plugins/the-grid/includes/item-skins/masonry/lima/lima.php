<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => '<i class="tg-icon-link"></i>', // Button link icon
		'comment'    => '<i class="tg-icon-chat"></i>', // Button link icon
		'image'      => '<i class="tg-icon-arrows-out-2"></i>',  // Ligthbox icon
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
$link_button  = $content['link_button'];

// background image for quote/link
$bg_img = (isset($content['media_data']['url'])) ? '<div class="tg-item-image" style="background-image: url('.$content['media_data']['url'].')"></div>' : null;

if (isset($content['quote_markup'])) {

	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-quote-icon tg-icon-quote" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['quote_markup'];
	$html .= $content['post_like'];
	$html .= $content['content_wrapper_end'];
	
} else if (isset($content['link_markup'])) {

	$html .= $bg_img;
	$html .= $content['content_wrapper_start'];
	$html .= '<i class="tg-link-icon tg-icon-link" style="color:'.$content['colors']['content']['title'].'"></i>';
	$html .= $content['link_markup'];
	$html .= $content['post_like'];
	$html .= $content['content_wrapper_end'];
	
} else {

	$image = false;
	if ($content['media_type'] == 'image') {
		$image = (isset($content['media_data']['url'])) ? true : false;
	} else if ($content['media_type'] == 'gallery') {
		$image = (isset($content['media_data']['images'][0]['url'])) ? true : false;
	} else if ($content['media_type'] == 'video' || $content['media_type'] == 'audio') {
		$image = (isset($content['media_poster']['url'])) ? true : false;
	} else if ($content['media_type'] == 'none') {
		$image = false;
	}
			
	$html .= $content['media_wrapper_start'];
		$html .= $content['media_markup'];
		$html .= (!empty($media_button)) ? $content['overlay'] : null;
		$html .= '<div class="tg-buttons-holder">';
			$html .= (!empty($media_button)) ? $media_button : null;  
			$html .= (!empty($media_button)) ? $link_button : null;
		$html .= '</div>';
	$html .= $content['media_wrapper_end'];
		
	$html .= $content['content_wrapper_start'];
		$html .= $content['title'];
		$html .= ($image == false) ? $content['content'] : null;
		$html .= $content['terms'];
		$html .= $content['post_like'];
	$html .= $content['content_wrapper_end'];

}
		
return $html;