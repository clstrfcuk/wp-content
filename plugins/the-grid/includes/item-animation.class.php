<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}
 
class The_Grid_Item_Animation {
	
	private $animations = array();
	
	function __construct() {
		$this->animations = apply_filters('tg_add_item_animation', $this->animations);
	}
		
	// get custom animation array
	function get_animation_name() {
		return $this->animations;
	}
	
	// get custom animation array
	function get_animation_arr() {
		$anim_arr = array();
		$animation = $this->animations;
		foreach($animation as $slug=>$name) {
			$anim_arr[$slug] = $name[0]['name'];
		}
		return $anim_arr;
	}
}

new The_Grid_Item_Animation();

add_filter('tg_add_item_animation', 'tg_built_in_animation');
function tg_built_in_animation($animation) {
	

	$animation['none'] = array(
		array('name' => __('None', 'tg-text-domain')),
		array('visible' => ''),
		array('hidden '=> '')
	);
	$animation['fade_in'] = array(
		array('name' => __('Fade in', 'tg-text-domain')),
		array('visible' => ''),
		array('hidden' => '')
	);
	$animation['zoom_in'] = array(
		array('name' => __('Zoom in', 'tg-text-domain')),
		array('visible' => 'scale(1)'),
		array('hidden' => 'scale(0.001)')
	);
	$animation['zoom_out'] = array(
		array('name' => __('Zoom out', 'tg-text-domain')),
		array('visible' => 'scale(1)'),
		array('hidden' => 'scale(1.5)')
	);
	$animation['from_bottom'] = array(
		array('name' => __('From Bottom', 'tg-text-domain')),
		array('visible' => 'translateY(0)'),
		array('hidden' => 'translateY(100px)')
	);
	$animation['from_top'] = array(
		array('name' => __('From Top', 'tg-text-domain')),
		array('visible' => 'translateY(0)'),
		array('hidden' => 'translateY(-100px)')
	);
	$animation['from_left'] = array(
		array('name' => __('From Left', 'tg-text-domain')),
		array('visible' => 'translateX(0)'),
		array('hidden' => 'translateX(-100px)')
	);
	$animation['from_right'] = array(
		array('name' => __('From Right', 'tg-text-domain')),
		array('visible' => 'translateX(0)'),
		array('hidden' => 'translateX(100px)')
	);
	$animation['from_top_left'] = array(
		array('name' => __('From Top Left', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(-100px) translateX(-100px)')
	);
	$animation['from_top_right'] = array(
		array('name' => __('From Top Right', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(-100px) translateX(100px)')
	);
	$animation['from_bottom_left'] = array(
		array('name' => __('From Bottom left', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(100px) translateX(-100px)')
	);
	$animation['from_bottom_right'] = array(
		array('name' => __('From Bottom Right', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(100px) translateX(100px')
	);
	$animation['slide_bottom'] = array(
		array('name' => __('Slide Bottom', 'tg-text-domain')),
		array('visible' => 'translateY(0)'),
		array('hidden' => 'translateY(200%)')
	);
	$animation['slide_top'] = array(
		array('name' => __('Slide Top', 'tg-text-domain')),
		array('visible' => 'translateY(0)'),
		array('hidden' => 'translateY(-200%)')
	);
	$animation['slide_left'] = array(
		array('name' => __('Slide Left', 'tg-text-domain')),
		array('visible' => 'translateX(0)'),
		array('hidden' => 'translateX(-200%)')
	);
	$animation['slide_right'] = array(
		array('name' => __('Slide Right', 'tg-text-domain')),
		array('visible' => 'translateX(0)'),
		array('hidden' => 'translateX(200%)')
	);
	$animation['slide_top_left'] = array(
		array('name' => __('Slide Top Left', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(-200%) translateX(-200%)')
	);
	$animation['slide_top_right'] = array(
		array('name' => __('Slide Top Right', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(-200%) translateX(200%)')
	);
	$animation['slide_bottom_left'] = array(
		array('name' => __('Slide Bottom Left', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(200%) translateX(-200%)')
	);
	$animation['slide_bottom_right'] = array(
		array('name' => __('Slide Bottom Right', 'tg-text-domain')),
		array('visible' => 'translateY(0) translateX(0)'),
		array('hidden' => 'translateY(200%) translateX(200%)')
	);
	$animation['flip_x'] = array(
		array('name' => __('Flip X', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) rotate3d(1,0,0,0deg) scale(1)'),
		array('hidden' => 'rotate3d(1,0,0,90deg) scale(0.8)')
	);
	$animation['flip_y'] = array(
		array('name' => __('Flip Y', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) rotate3d(0,1,0,0deg) scale(1)'),
		array('hidden' => 'rotate3d(0,1,0,90deg) scale(0.8)')
	);
	$animation['flip_z'] = array(
		array('name' => __('Flip Z', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) rotate3d(0,0,1,0deg) scale(1)'),
		array('hidden' => 'perspective(2000px) rotate3d(0,0,1,45deg) scale(0.2)')
	);
	$animation['from_bottom_flip_x'] = array(
		array('name' => __('From Bottom Flip X', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(1,0,0,0deg) scale(1)'),
		array('hidden' => 'translateY(100px) rotate3d(1,0,0,90deg) scale(0.8)')
	);
	$animation['from_bottom_flip_y'] = array(
		array('name' => __('From Bottom Flip Y', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(0,1,0,0deg) scale(1)'),
		array('hidden' => 'translateY(100px) rotate3d(0,1,0,90deg) scale(0.8)')
	);
	$animation['from_bottom_flip_z'] = array(
		array('name' => __('From Bottom Flip Z', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(0,0,1,0deg) scale(1)'),
		array('hidden' => 'perspective(2000px) translateY(100px) rotate3d(0,0,1,45deg) scale(0.2)')
	);
	$animation['from_top_flip_x'] = array(
		array('name' => __('From Top Flip X', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(1,0,0,0deg) scale(1)'),
		array('hidden' => 'translateY(-100px) rotate3d(1,0,0,90deg) scale(0.8)')
	);
	$animation['from_top_flip_y'] = array(
		array('name' => __('From Top Flip Y', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(0,1,0,0deg) scale(1)'),
		array('hidden' => 'translateY(-100px) rotate3d(0,1,0,90deg) scale(0.8)')
	);
	$animation['from_top_flip_z'] = array(
		array('name' => __('From Top Flip Z', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(0,0,1,0deg) scale(1)'),
		array('hidden' => 'perspective(2000px) translateY(-100px) rotate3d(0,0,1,45deg) scale(0.2)')
	);
	$animation['from_left_flip_x'] = array(
		array('name' => __('From Left Flip X', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateX(0) rotate3d(1,0,0,0deg) scale(1)'),
		array('hidden' => 'translateX(-100px) rotate3d(1,0,0,90deg) scale(0.8)')
	);
	$animation['from_left_flip_y'] = array(
		array('name' => __('From Left Flip Y', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateX(0) rotate3d(0,1,0,0deg) scale(1)'),
		array('hidden' => 'translateX(-100px) rotate3d(0,1,0,90deg) scale(0.8)')
	);
	$animation['from_left_flip_z'] = array(
		array('name' => __('From Left Flip Z', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateX(0) rotate3d(0,0,1,0deg) scale(1)'),
		array('hidden' => 'perspective(2000px) translateX(-100px) rotate3d(0,0,1,45deg) scale(0.2)')
	);
	$animation['from_right_flip_x'] = array(
		array('name' => __('From Right Flip X', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(1,0,0,0deg) scale(1)'),
		array('hidden' => 'translateX(100px) rotate3d(1,0,0,90deg) scale(0.8)')
	);
	$animation['from_right_flip_y'] = array(
		array('name' => __('From Right Flip Y', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(0,1,0,0deg) scale(1)'),
		array('hidden' => 'translateX(100px) rotate3d(0,1,0,90deg) scale(0.8)')
	);
	$animation['from_right_flip_z'] = array(
		array('name' => __('From Right Flip Z', 'tg-text-domain')),
		array('visible' => 'perspective(2000px) translateY(0) rotate3d(0,0,1,0deg) scale(1)'),
		array('hidden' => 'perspective(2000px) translateX(100px) rotate3d(0,0,1,45deg) scale(0.2)')
	);
	$animation['perspective_x'] = array(
		array('name' => __('Perspective X', 'tg-text-domain')),
		array('visible' => 'perspective(2000px)'),
		array('hidden' => 'perspective(2000px) rotateX(45deg)')
	);
	$animation['perspective_y'] = array(
		array('name' => __('Perspective Y', 'tg-text-domain')),
		array('visible' => 'perspective(2000px)'),
		array('hidden' => 'perspective(2000px) rotateY(45deg)')
	);
	$animation['perspective_z'] = array(
		array('name' => __('Perspective Z', 'tg-text-domain')),
		array('visible' => 'matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)'),
		array('hidden' => 'matrix3d(0.70592, 0.02465, 0.37557, -0.00062, -0.06052, 0.79532, 0.06156, -0.0001, -0.46435, -0.10342, 0.87958, -0.00146, -21.42566, 4.13698, 4.81749, 0.99197085)')
	);
	$animation['falling_rotate'] = array(
		array('name' => __('Falling Rotate', 'tg-text-domain')),
		array('visible' => 'matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)'),
		array('hidden' => 'matrix3d(0.71,0.71,0.00,0,-0.71,0.71,0.00,0,0,0,1,0,-50,-250,0,1)')
	);
		
	return $animation;

}