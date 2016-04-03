<?php

// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => '<i class="tg-icon-add"></i>'.__( 'Read More', 'tg-text-domain' ),  // Button link icon
		'comment'    => '<i class="tg-icon-chat"></i>',       // Button link icon
		'image'      => '<i class="tg-icon-add"></i>',        // Ligthbox icon
		'audio'      => '<i class="tg-icon-play"></i>'.__( 'Play Song', 'tg-text-domain' ),  // Audio icon
		'video'      => '<i class="tg-icon-play"></i>'.__( 'Play Video', 'tg-text-domain' ), // HTML Video icon
		'vimeo'      => '<i class="tg-icon-play"></i>'.__( 'Play Video', 'tg-text-domain' ), // Vimeo icon
		'wistia'     => '<i class="tg-icon-play"></i>'.__( 'Play Video', 'tg-text-domain' ), // Wistia icon
		'youtube'    => '<i class="tg-icon-play"></i>'.__( 'Play Video', 'tg-text-domain' ), // Youtube icon
		'soundcloud' => '<i class="tg-icon-play"></i>'.__( 'Play Song', 'tg-text-domain' ),  // SoundCloud icon
	),
	'excerpt_length'  => 200,     // Excerpt character length
	'excerpt_tag'     => '',      // Excerpt more tag
	'read_more'       => '',      // Read more text
	'date_format'     => '' ,     // Date format (https://codex.wordpress.org/Formatting_Date_and_Time)
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'none',  // Get terms color (option: 'color', 'background', 'none'); default 'none'
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

$base  = new The_Grid_Base();
$color = $content['colors']['overlay']['background'];
$gradient = null;
if (!empty($color)) {
	$color = str_replace(array('#','(',')','rgba','rgb'), array('','','','',''), $color);
	if (preg_match("/^([a-f0-9]{3}|[a-f0-9]{6})$/i",$color)) {
		$color3 = $color;
		$color  = $base->HEX2RGB($color,$alpha=1);
		$color1 = $color['red'].','.$color['green'].','.$color['blue'];
		$color2 = $color1.',1';	
	} else {
		$color = explode(',', $color);
		$alpha = (isset($color[3])) ? $color[3] : 1;
		$color1 = $color[0].','.$color[1].','.$color[2];
		$color2 = $color1.','.$alpha;
		$color3 = $base->RGB2HEX($color);	
	}
	
	$gradient = 'style="background:transparent;background: linear-gradient(top, rgba('.$color1.',0) 0%, rgba('.$color2.') 100%);background: -moz-linear-gradient(top, rgba('.$color1.',0) 0%, rgba('.$color2.') 100%);background: -ms-linear-gradient(top, rgba('.$color1.',0) 0%, rgba('.$color2.') 100%);background: -o-linear-gradient( top, rgba('.$color1.',0) 0%, rgba('.$color2.') 100%);background: -webkit-linear-gradient( top, rgba('.$color1.',0) 0%, rgba('.$color2.') 100%);-ms-filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#00'.$color3.', endColorstr=#ff'.$color3.');filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#00'.$color3.', endColorstr=#ff'.$color3.');"';
}

$content_class = $content['colors']['overlay']['class'];

$html .='<div class="tg-panZ">';
	
	$html .= $content['media_wrapper_start'];	
		$html .= $content['media_markup'];
	$html .= $content['media_wrapper_end'];
	$html .= '<div class="tg-item-overlay" '.$gradient.'></div>';
		
	$html .= str_replace(array('tg-item-content-holder light','tg-item-content-holder dark'),array('tg-item-content-holder '.$content_class,'tg-item-content-holder '.$content_class), $content['content_wrapper_start']);	
		$html .= '<div class="tg-item-content-inner">';
			$html .= $content['title'];
			$html .= ($content['media_type'] !== 'image' && $content['media_type'] !== 'gallery') ? $content['media_button'] : null;
			$html .= ($content['media_type']  == 'image' || $content['media_type']  == 'gallery') ? $content['link_button'] : null;
		$html .= '</div>';	
	$html .= $content['content_wrapper_end'];

$html .= '</div>';

return $html;