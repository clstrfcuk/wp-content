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

class The_Grid_Ajax extends The_Grid {
	
	/**
	* Construct for Ajax
	* @since 1.0.0
	*/
	public function __construct() {
		parent::__construct();
		// ajax load more item in grid
		add_action('wp_ajax_the_grid_load_more', array($this, 'the_grid_load_more_items'));
		add_action('wp_ajax_nopriv_the_grid_load_more', array($this, 'the_grid_load_more_items'));
	}
	
	/**
	* Load more item with ajax (button or scroll)
	* @since 1.0.0
	*/
	public function the_grid_load_more_items() {
		
		$nonce  = $_POST['grid_nonce']; 
		$action = $_POST['action'];
		
		// check ajax nounce
		if (!wp_verify_nonce($nonce, $action) && is_user_logged_in()) {
			$response['success'] = false;				
			$response['message'] = __('Loading Error', 'tg-text-domain');
			$response['content'] = null;
			wp_send_json($response);				
			die();
		} else {
			
			// set ajax mode to true
			global $tg_is_ajax;
			$tg_is_ajax = true;
				
			// retrive ajax info
			$grid_page = (isset($_POST['grid_page'])) ? $_POST['grid_page'] : die();
			$grid_name = (isset($_POST['grid_name']) && !empty($_POST['grid_name'])) ? $_POST['grid_name'] : die();
			$grid_data = (isset($_POST['grid_data']) && !empty($_POST['grid_data'])) ? $_POST['grid_data'] : null;
			
			// prevent caching if in backend grid preview
			$cache = false;

			// get grid options (back end if data and front end if not)
			if ($grid_data) {
				
				$grid_name = $grid_data['the_grid_name'];
				$grid_info = get_page_by_title($grid_name, 'OBJECT', 'the_grid');
				$grid_id   = (isset($grid_info->ID)) ? $grid_info->ID : $grid_id = $grid_info;
				
				// retireve all grid settings
				foreach ($grid_data as $data => $val) {
					$data = str_replace('the_grid_', '', $data);
					$grid_data[$data] = wp_unslash($val);
				}
				
				// disable post_not_in, lightbox & pagination on preview
				$grid_data['post_not_in'] = null;
				$grid_data['video_lightbox']  = true;
				$grid_data['ajax_pagination'] = true;
				// set the grid ID and name from ajax data
				$grid_data['ID']   = 'grid-'.$grid_id;
				$grid_data['name'] = $grid_name;
				// disable preload since grid already loaded
				$grid_data['preloader'] = null;
				// we are in backend view mode
				global $tg_grid_preview;
				$tg_grid_preview = true;
				
				parent::normalize_data($grid_data);
				
			} else {

				$cache   = get_option('the_grid_caching', false);
				$content = parent::get_cache($grid_name, $grid_page+1, $cache);
				if (!empty($content)) {
					$response['success'] = true;				
					$response['message'] = __('Content correctly retrieved', 'tg-text-domain');
					$response['content'] = $content;
					wp_send_json($response);
					die();
				}

				parent::get_data($grid_name);
				
				global $tg_grid_data;
				// disable preload since grid already loaded
				$tg_grid_data['preloader'] = null;
			}
			
			// redefined main globals
			global $tg_query_args, $tg_item_count;
			
			// redefined query
			$tg_query_args = parent::build_query();
			$ajax_page_nav = $this->grid_data['ajax_pagination'];
			$ajax_item_nb  = $this->grid_data['ajax_item_number'];
			$tg_query_args['paged'] = $grid_page;
			// check if ajax pagination to keep post per page instead of nb of post to load with ajax (load more button or on scroll)
			$pagination = array_filter($this->grid_data, function($s){
				return $pagination = (is_string($s)) ? strpos($s, 'get_pagination') : false;
			});
			$tg_query_args['posts_per_page'] = ($ajax_page_nav && !empty($pagination)) ? $tg_query_args['posts_per_page'] : $ajax_item_nb;
			$tg_query_args['offset'] = $this->grid_data['item_number']+$tg_query_args['posts_per_page']*($grid_page-1);
			
			// redefined item number for ajax call (social media content)
			$this->grid_data['item_number'] = $this->grid_data['ajax_item_number'];

			//redefine item count html comment markup
			$tg_item_count = $tg_query_args['offset']+1;

			// run the custom query
			parent::run_query();
			
			// loop to get new items
			ob_start();
			new The_Grid_Item();	
			$content = ob_get_contents();
			ob_end_clean();

			// add grid ajax content to cache
			$grid_ID = str_replace('grid-', '', $this->grid_data['ID']);
			$this->save_cache($grid_ID, $grid_page+1, $cache, $content);
			
			// reset the custom query
			parent::reset_query();
			
			// send json response
			$response['success'] = true;				
			$response['message'] = __('Content correctly retrieved', 'tg-text-domain');
			$response['content'] = $content;
			$response['social']  = htmlspecialchars_decode($this->grid_data['social_data']);
			wp_send_json($response);

		}

		exit();
	}
	
}

new The_Grid_Ajax();