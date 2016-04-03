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

// Extended class (of core grid) for grid preview backend
class The_Grid_preview extends The_Grid {
	
	protected $grid_data;
	
	/**
	* Initialization child
	* @since 1.0.0
	*/
	function __construct() {
		
        parent::__construct();
		
    }

	/**
	* Grid preview ajax
	* @since 1.0.0
	*/
	public function grid_preview_callback() {
		
		global $tg_preview_data;
		
		$meta_data = $tg_preview_data['meta_data'];
		$grid_name = $meta_data['the_grid_name'];
		$post_ID   = $tg_preview_data['post_ID'];
		
		if (!isset($post_ID) || empty($post_ID)) {
			return __( 'Sorry, an unexpected errors occurred while parsing data.', 'tg-text-domain');
		}

		foreach ($meta_data as $data => $val) {
			$data = str_replace('the_grid_', '', $data);
			$grid_data[$data] = wp_unslash($val);
		}
		
		// show all post in preview and force ajax pagination
		$grid_data['ID']   = 'grid-'.$post_ID;
		$grid_data['name'] = $grid_name;
		$grid_data['post_not_in'] = null;
		$grid_data['video_lightbox']  = true;
		$grid_data['ajax_pagination'] = true;
		
		parent::normalize_data($grid_data);

		if ($this->grid_data != false) {
			$content = parent::the_grid_content();
			echo $content;
		}
		
	}
}