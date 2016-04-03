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

class The_Grid {

	protected $plugin_slug = TG_SLUG;
	protected $grid_prefix = TG_PREFIX;
	protected $grid_data;
	
	/**
	* _construct
	* @since 1.0.0
	*/
	public function __construct() {}
	
	/**
	* Output The Grid Markup (Main core function)
	* @since 1.0.0
	*/
	public function output($grid_name) {
		
		$cache   = get_option('the_grid_caching', false);
		$page    = (get_query_var('paged')) ? max(1, get_query_var('paged')) : max(1, get_query_var('page'));
		$content = $this->get_cache($grid_name, $page, $cache);
		
		if (!empty($content)) {
			if (strpos($content,'tg-grid-wrapper') !== false) {
				return $content;
			} else {
				$content = null;
			}
		}
		
		if (empty($content)) {
			$this->get_data($grid_name);
			if ($this->grid_data != false) {
				ob_start();
				$this->the_grid_content();
				$content = ob_get_contents();
				ob_clean();
				ob_end_clean();
				$grid_ID = str_replace('grid-', '', $this->grid_data['ID']);
				$this->save_cache($grid_ID, $page, $cache, $content);
				return $content;
			}	
		}
		
	}
	
	/**
	* Retrieve current grid data
	* @since 1.0.0
	*/
	public function get_data($grid_name) {
		
		$grid_info = get_page_by_title(html_entity_decode($grid_name), 'OBJECT', 'the_grid');
		
		if (!isset($grid_info->ID)) {
			return false;
		}
		
		$grid_id = $grid_info->ID;
		$this->grid_data['ID'] = 'grid-'.$grid_id;
		
		$meta_keys = get_post_custom_keys($grid_id);
		if (empty($meta_keys)) {
			return;
		}

		foreach ($meta_keys as $meta_key) {
			$meta_values = get_post_custom_values($meta_key, $grid_id);
			if (strrpos($meta_key, $this->plugin_slug) !== false) {
				foreach ($meta_values as $meta_value) {
					$meta_value = maybe_unserialize($meta_value);
					$meta_key = str_replace('the_grid_', '', $meta_key);
					$this->grid_data[$meta_key] = $meta_value;
				}
			}
		}

		$this->normalize_data($this->grid_data);

	}
	
	/**
	* Retrieve grid cache content if exists
	* @since 1.0.0
	*/
	public function get_cache($grid_name, $page, $cache) {
		
		global $tg_is_ajax;
		
		$time_start = (!$tg_is_ajax) ? microtime(true) : null;
		$cache_msg  = null;
		$content    = null;
		
		if ($cache == true && !wp_is_mobile()) {
			
			$base = new The_Grid_Base();
			
			$grid_info = get_page_by_title(html_entity_decode($grid_name), 'OBJECT', 'the_grid');
			$grid_ID   = (isset($grid_info->ID)) ? $grid_info->ID : null;
			
			if ($grid_ID) {
			
				$source  = get_post_meta($grid_ID, 'the_grid_source_type', true);
				$source  = (empty($source)) ? 'post_type' : $source;
				$orderby = get_post_meta($grid_ID, 'the_grid_orderby', true);
				$orderby = (!empty($orderby)) ? $orderby : array();
				$grid_transient = 'tg_grid_transient_'.$grid_ID.'_page_'.$page;
				
				// disable grid cache while using W3 Total cache plugin
				$base->disable_W3_Total_Cache($grid_transient);
				
				$data_timeout = get_option('_transient_timeout_' . $grid_transient);
				
				if ($data_timeout >= time() && !in_array('rand',$orderby) && $source == 'post_type') {
					$data = get_transient($grid_transient);
					if (isset($data['content']) && !empty($data['content'])) {
						$content = $data['content'];
						if (!$tg_is_ajax) {
							$time_end   = microtime(true);
							$cache_date = (isset($data['cache_date'])) ? $data['cache_date'] : null;
							$cache_msg  = '<!-- The Grid Cache Enabled - Execution Time: '.round($time_end - $time_start,5).'s - Date: '.$cache_date.' -->';
							$content    = preg_replace('~<!-- The Grid Plugin Version.+?-->~s', '<!-- The Grid Plugin Version '.TG_VERSION.' -->', $content);
						}
						$content = $cache_msg.$this->preserve_post_like($content);
					} else {
						$content = null;
					}
				}
				
				// enable grid cache while using W3 Total cache plugin
				$base->enable_W3_Total_Cache($grid_transient);
			
			}

		}
		
		return $content;
	
	}
	
	/**
	* Save grid content in cache
	* @since 1.0.0
	*/
	public function save_cache($grid_ID, $page, $cache, $content) {
		
		$source  = get_post_meta($grid_ID, 'the_grid_source_type', true);
		$source  = (empty($source)) ? 'post_type' : $source;
		$orderby = get_post_meta($grid_ID, 'the_grid_orderby', true);
		$orderby = (!empty($orderby)) ? $orderby : array();
		
		if ($cache == true && !in_array('rand',$orderby) && !wp_is_mobile() && $source == 'post_type') {
			$grid_transient = 'tg_grid_transient_'.$grid_ID.'_page_'.$page; // set a transitien per page even if ajax (simulate page)
			$data = array(
				'cache_date' => date('m/d/y, h:i:s A'),
				'content' => $content
			);
			set_transient($grid_transient, $data, apply_filters('tg_transient_expiration', 60*60*24*7));
		}
		
	}
	
	/**
	* Preserve post like (number & state) and comments if cache enable
	* @since 1.0.0
	*/
	public function preserve_post_like($content){
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->encoding = 'utf-8';
		$dom->loadHTML(utf8_decode($content));
		//$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		$finder = new DOMXPath($dom);
		$nodes  = $finder->query('//span[@data-post-id] | //a[@data-comment-id]');
		foreach ($nodes as $node) {
			$ID  = $node->getAttribute('data-post-id');
			if ($ID) {
				$like = TO_get_post_like($ID);
				$dom1 = new DOMDocument();
				$dom1->loadHTML($like);
				$finder1 = new DOMXPath($dom1);
				$classname1 = 'to-post-like';
				$classname2 = 'to-like-count';
				$nodes1  = $finder1->query("//*[contains(@class, '$classname1')]");
				$nodes2  = $finder1->query("//*[contains(@class, '$classname2')]");
				$classes = $nodes1->item(0)->getAttribute('class');
				$like_nb = $nodes2->item(0)->textContent;
				$node->setAttribute('class', $classes);
				$node->childNodes->item(1)->nodeValue = $like_nb;
			}
			$ID = $node->getAttribute('data-comment-id');
			if ($ID) {
				$text = $node->getAttribute('data-comment');
				$text = json_decode($text);
				$coms = get_comments_number($ID);
				$coms = ($coms < 1  && isset($text->no)) ? $text->no : $coms;
				$coms = ($coms == 1 && isset($text->one)) ? $coms.' '.$text->one : $coms;
				$coms = ($coms > 1  && isset($text->plus)) ? $coms.' '.$text->plus : $coms;
				if ($node->childNodes->item(1)) {
					$node->childNodes->item(1)->nodeValue = $coms;
				} else if ($node->childNodes->item(0)) {
					$node->childNodes->item(0)->nodeValue = $coms;
				}
			}
		}
		$content = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>', '</source>'), array('', '', '', '', ''), $dom->saveHTML()));
		return $content;
	}

	/**
	* getVar for MetaData
	* @since 1.0.0
	*/
	public function getVar($arr, $key, $default = ''){
		$val = (isset($arr[$key]) && !empty($arr[$key])) ? $arr[$key] : $default;
		return($val);
	}
	
	/**
	* Normalize all settings from the grid settings panel
	* @since 1.0.0
	*/
	public function normalize_data($data) {

		// Default general settings
		$options[] = array(
			'ID'        => '',
			'name'      => '',
			'css_class' => ''
		);
		
		// Default source settings
		$options[] = array(
			'source_type'      => 'post_type',
			'item_number'      => get_option('posts_per_page'),
			'post_type'        => array('post'),
			'gallery'          => '',
			'post_status'      => array('publish'),
			'categories'       => array(),
			'categories_child' => '',
			'pages_id'         => array(),
			'author'           => array(''),
			'post_not_in'      => '',
			'order'            => '',
			'orderby'          => array(),
			'orderby_id'       => '',
			'meta_key'         => '',
			'meta_query'       => ''
		);
		
		// Social media attribute default settings
		$options[] = array(
			'social_data' => ''
		);
		
		// Instagram default settings
		$options[] = array(
			'instagram_username' => '',
			'instagram_hashtag'  => ''
		);
		
		// Youtube default settings
		$options[] = array(
			'youtube_order'    => 'date',
			'youtube_source'   => 'channel',
			'youtube_channel'  => '',
			'youtube_playlist' => '',
			'youtube_videos'   => ''
		);
		
		// Vimeo default settings
		$options[] = array(
			'vimeo_sort'    => '',
			'vimeo_order'   => 'desc',
			'vimeo_source'  => 'user',
			'vimeo_user'    => '',
			'vimeo_album'   => '',
			'vimeo_group'   => '',
			'vimeo_channel' => ''
		);
		
		// Default media settings
		$options[] = array(
			'default_image'      => '',
			'aqua_resizer'       => '',
			'image_size'         => 'full',
			'items_format'       => array(),
			'gallery_slide_show' => '',
			'video_lightbox'     => ''
		);
		
		// Default grid settings
		$options[] = array(
			'style'                => 'grid',
			'item_x_ratio'         => 4,
			'item_y_ratio'         => 3,
			'item_force_size'      => '',
			'items_col'            => 1,
			'items_row'            => 1,
			// grid/masonry style
			'desktop_large'        => 6,
			'desktop_medium'       => 5,
			'desktop_small'        => 4,
			'tablet'               => 3,
			'tablet_small'         => 2,
			'mobile'               => 1,
			// justified style
			'desktop_large_row'    => 240,
			'desktop_medium_row'   => 240,
			'desktop_small_row'    => 220,
			'tablet_row'           => 220,
			'tablet_small_row'     => 200,
			'mobile_row'           => 200,
			// items gutters
			'gutter'               => 0,
			'desktop_medium_gutter'=> -1,
			'desktop_small_gutter' => -1,
			'tablet_gutter'        => -1,
			'tablet_small_gutter'  => -1,
			'mobile_gutter'        => -1,
			// browser window widths
			'desktop_medium_width' => 1200,
			'desktop_small_width'  => 980,
			'tablet_width'         => 768,
			'tablet_small_width'   => 480,
			'mobile_width'         => 320
		);
		
		// Default filter/sort settings
		$options[] = array(
			'filter_onload'           => array(),
			'filter_combination'      => '',
			'filter_logic'            => 'AND',
			'sort_by'                 => array(),
			'sort_by_onload'          => '',
			'sort_order_onload'       => '',
			'sort_by_text'            => __('Sort By', 'tg-text-domain'),
			'search_text'             => ''
		);
		
		// handle dynamic generated filter area options
		$grid_data_key = array_keys($data);
		$filter_areas  = preg_grep('/filters_\d/i', $grid_data_key);
		$filter_number = count($filter_areas);
		for ($i = 1; $i <= $filter_number; $i++) {
			$options[] = array(
				'filters_order_'.$i         => '',
				'filter_type_'.$i           => 'button',
				'filter_dropdown_title_'.$i => __('Filter Categories', 'tg-text-domain'),
				'filter_all_text_'.$i       => '',
				'filter_count_'.$i          => 'none',
				'filters_'.$i               => '',
			);
		}	
		
		// Default pagination settings
		$options[] = array(
			'ajax_pagination'      => '',
			'pagination_type'      => 'number',
			'pagination_prev_next' => '',
			'pagination_show_all'  => '',
			'pagination_mid_size'  => 2,
			'pagination_end_size'  => 1,
			'pagination_prev'      => __('&#171; Prev', 'tg-text-domain'),
			'pagination_next'      => __('Next &#187;', 'tg-text-domain')
		);
		
		// Default layout settings
		$options[] = array(
			'area_top1'        => '',
			'area_top2'        => '',
			'area_left'        => '',
			'area_right'       => '',
			'area_bottom1'     => '',
			'area_bottom2'     => '',
			'layout'           => 'vertical',
			'rtl'              => '',
			'wrap_marg_left'   => 0,
			'wrap_marg_top'    => 0,
			'wrap_marg_right'  => 0,
			'wrap_marg_bottom' => 0,
			'grid_background'  => '',
			'full_width'       => '',
			'full_height'      => ''	
		);
		
		// Default slider settings
		$options[] = array(
			'row_nb'               => 1,
			'slider_swingSpeed'    => 0.1,
			'slider_itemNav'       => 'null',
			'slider_startAt'       => 1,
			'slider_autoplay'      => '',
			'slider_cycleInterval' => 5000
		);
		
		// Default skin settings
		$options[] = array(
			'skins'                   => array(),
			'social_skin'             => '',
			'skin_content_background' => '',
			'skin_content_color'      => 'dark',
			'skin_overlay_background' => '',
			'skin_overlay_color'      => 'light',
			'navigation_style'        => 'tg-txt',
			'navigation_color'        => '#999999',
			'navigation_accent_color' => '#ff6863',
			'navigation_bg'           => '#999999',
			'navigation_accent_bg'    => '#ff6863',
			'dropdown_color'          => '#777777',
			'dropdown_bg'             => '',
			'dropdown_hover_color'    => '#444444',
			'dropdown_hover_bg'       => '',
			'navigation_arrows_color' => '',
			'navigation_arrows_bg'    => '',
			'navigation_bullets_color' => '#dddddd',
			'navigation_bullets_color_active' => '#59585b'
		);
		
		// Default animations settings
		$options[] = array(
			'animation'  => 'fade_in',
			'transition' => 0
		);
		
		// Default load/ajax settings
		$options[] = array(
			'ajax_method'         => '',
			'ajax_button_text'    => __( 'Load More', 'tg-text-domain' ),
			'ajax_button_loading' => __('Loading...', 'tg-text-domain'),
			'ajax_button_no_more' => __( 'No more item', 'tg-text-domain' ),
			'ajax_items_remain'   => '',
			'ajax_item_number'    => 4,
			'ajax_item_delay'     => 0,
			'preloader'           => '',
			'preloader_style'     => 'square-grid-pulse',
			'preloader_color'     => '#34495e',
			'preloader_size'      => '1',
			'item_delay'          => 0,
			'custom_css'          => ''
		);
		
		// Default css/js settings
		$options[] = array(
			'custom_js'  => '',
			'custom_css' => ''
		);
		
		// merge all options array
		$options = call_user_func_array('array_merge', $options);

		// loop through each settings and assign default value if missing
		foreach($options as $option => $value) {
			$this->grid_data[$option] = $this->getVar($data, $option, $value);
		}

		// check current settings to reassign right values depending of current settings
		$this->check_data();

		// redefined global and return normalized data (for ajax)
		global $tg_grid_data;
		$tg_grid_data = $this->grid_data;

	}
	
	/**
	* Check important data and reassign right settings values
	* @since 1.0.0
	*/
	public function check_data() {
		// media image size
		$aqua_resizer       = $this->grid_data['aqua_resizer'];
		// get the grid main data
		$grid_style         = $this->grid_data['style'];
		$grid_layout        = $this->grid_data['layout'];
		$item_number        = $this->grid_data['item_number'];
		$full_height        = $this->grid_data['full_height'];
		$slider_rownb       = $this->grid_data['row_nb'];
		$slider_itemNav     = $this->grid_data['slider_itemNav'];
		$slider_startAt     = $this->grid_data['slider_startAt'];
		$slider_autoplay    = $this->grid_data['slider_autoplay'];
		$ajax_method        = $this->grid_data['ajax_method'];
		// grid filter on load
		$filter_onload      = $this->grid_data['filter_onload'];
		// columns number
		$col_desktop_large  = $this->grid_data['desktop_large'];
		$col_desktop_medium = $this->grid_data['desktop_medium'];
		$col_desktop_small  = $this->grid_data['desktop_small'];
		$col_tablet         = $this->grid_data['tablet'];
		$col_tablet_small   = $this->grid_data['tablet_small'];
		$col_mobile         = $this->grid_data['mobile'];
		// rows height
		$row_desktop_large  = (int) $this->grid_data['desktop_large_row'];
		$row_desktop_medium = (int) $this->grid_data['desktop_medium_row'];
		$row_desktop_small  = (int) $this->grid_data['desktop_small_row'];
		$row_tablet         = (int) $this->grid_data['tablet_row'];
		$row_tablet_small   = (int) $this->grid_data['tablet_small_row'];
		$row_mobile         = (int) $this->grid_data['mobile_row'];
		// items gutter
		$gutter_desktop_large  = (int) $this->grid_data['gutter'];
		$gutter_desktop_medium = (int) $this->grid_data['desktop_medium_gutter'];
		$gutter_desktop_small  = (int) $this->grid_data['desktop_small_gutter'];
		$gutter_tablet         = (int) $this->grid_data['tablet_gutter'];
		$gutter_tablet_small   = (int) $this->grid_data['tablet_small_gutter'];
		$gutter_mobile         = (int) $this->grid_data['mobile_gutter'];
		// smart gutter (to match previous gutter value if empty or <= -1 values, preserve 0 value)
		$gutter_desktop_medium = ((empty($gutter_desktop_medium) || $gutter_desktop_medium <= -1) && $gutter_desktop_medium != 0) ? $gutter_desktop_large : $gutter_desktop_medium;
		$gutter_desktop_small  = ((empty($gutter_desktop_small) || $gutter_desktop_small <= -1) && $gutter_desktop_small != 0) ? $gutter_desktop_medium : $gutter_desktop_small;
		$gutter_tablet         = ((empty($gutter_tablet) || $gutter_tablet <= -1) && $gutter_tablet != 0) ? $gutter_desktop_small : $gutter_tablet;
		$gutter_tablet_small   = ((empty($gutter_tablet_small) || $gutter_tablet_small <= -1) && $gutter_tablet_small != 0) ? $gutter_tablet : $gutter_tablet_small;
		$gutter_mobile         = ((empty($gutter_mobile) || $gutter_mobile <= -1) && $gutter_mobile != 0) ? $gutter_tablet_small :  $gutter_mobile;
		
		// columns/rows window widths
		$ww_desktop_medium  = (int) $this->grid_data['desktop_medium_width'];
		$ww_desktop_small   = (int) $this->grid_data['desktop_small_width'];
		$ww_tablet          = (int) $this->grid_data['tablet_width'];
		$ww_tablet_small    = (int) $this->grid_data['tablet_small_width'];
		$ww_mobile          = (int) $this->grid_data['mobile_width'];
		// ajax item animation delay
		$ajax_item_delay = $this->grid_data['ajax_item_delay'];
		
		// filter on load (set new param)
		$active_filters = array();
		foreach ($filter_onload as $filter => $value) {
			$filter = explode(':', $value);
			array_push($active_filters, $filter[1]);
		}
		$this->grid_data['active_filters'] = $active_filters;
		// Remove random orderby if load more or ajax pagination
		array_filter($this->grid_data, array($this, 'find_random_orderby'));
		// media image size disabled aqua resizer if justified layout
		$this->grid_data['aqua_resizer'] = ($grid_style != 'justified') ? $aqua_resizer : false;	
		// grid full height mode (horizontal)
		$this->grid_data['full_height'] = ($grid_layout == 'horizontal' && $grid_style == 'grid') ? $full_height : 'null';
		// redefined row number
		$this->grid_data['row_nb']  = ($grid_style == 'masonry') ? 1 : $slider_rownb;
		// slider start position
		$this->grid_data['slider_startAt'] = ($slider_itemNav != 'null') ? $slider_startAt : 1;
		// slider cycle by (new created option based on autoplay option)
		$this->grid_data['slider_cycleBy'] = (!empty($slider_autoplay)) ? 'pages'  : 'null';
		// redfined ajax method
		$this->grid_data['ajax_method']   = ($item_number != '-1' && $grid_layout != 'horizontal') ? $ajax_method : '';
		// build columns/widths array
		$this->grid_data['columns'] = array(
			array($ww_mobile, $col_mobile),
			array($ww_tablet_small, $col_tablet_small),
			array($ww_tablet, $col_tablet),
			array($ww_desktop_small, $col_desktop_small),
			array($ww_desktop_medium, $col_desktop_medium),
			array(9999, $col_desktop_large)
		);
		// build gutters array
		$this->grid_data['gutters'] = array(
			array($ww_mobile, $gutter_mobile),
			array($ww_tablet_small, $gutter_tablet_small),
			array($ww_tablet, $gutter_tablet),
			array($ww_desktop_small, $gutter_desktop_small),
			array($ww_desktop_medium, $gutter_desktop_medium),
			array(9999, $gutter_desktop_large)
		);
		// build rows/widths array
		$this->grid_data['rows_height'] = array(
			array($ww_mobile, $row_mobile),
			array($ww_tablet_small, $row_tablet_small),
			array($ww_tablet, $row_tablet),
			array($ww_desktop_small, $row_desktop_small),
			array($ww_desktop_medium, $row_desktop_medium),
			array(9999, $row_desktop_large)
		);
		
	}
	
	/**
	* Find if random value for orderBy exists
	* @since 1.4.5
	*/
	public function find_random_orderby($s) {
		// if load more or pagination exists then unset random order to preserve correct post order when load more
		if (is_string($s) && (strpos($s, 'get_ajax_button') !== false || strpos($s, 'get_pagination') !== false || $this->grid_data['ajax_method'] == 'on_scroll')) {
			if (($key = array_search('rand', $this->grid_data['orderby'])) !== false) {
				unset($this->grid_data['orderby'][$key]);
			}
		}
	}
	
	/**
	* Data processing (fetch/build options)
	* @since 1.0.0
	*/
	public function data_processing() {
		
		// build post query for post type only
		if ($this->grid_data['source_type'] == 'post_type') {
			$this->build_query();
		}
		
		$this->run_query();
		$this->grid_classes_processing();
		$this->grid_styles_processing();
		$this->grid_areas_processing();
		$this->grid_data_processing();
		
	}
	
	/**
	* Build the Main Query array
	* @since 1.0.0
	*/
	public function build_query() {
		
		$base = new The_Grid_Base();
		
		// item number on load
		$posts_per_page = $this->grid_data['item_number'];
		// post type and associated categories
		$post_type = $this->grid_data['post_type'];
		// Attachment images ID for gallery
		$gallery_img = explode(',', $this->grid_data['gallery']);
		$gallery_img = (count($gallery_img) == 1 && $gallery_img[0] == 0) ? null : $gallery_img;
		// post type status
		$post_status = $this->grid_data['post_status'];
		// associated categories
		$post_cats = $this->grid_data['categories'];
		$post_cats_child = $this->grid_data['categories_child'];
		
		// prepare taxonomy array
		$taxonomies = array();
		// Build taxonomy query from selected cats/tags for post types
		$i = 0;
		if ($post_cats) {
			foreach($post_cats as $taxonomy) {
				$key   = explode(':', $taxonomy);
				$tax   = $key[0];
				$terms = $key[1];
				$taxonomies[$tax]['include_children'] = $post_cats_child;
				$taxonomies[$tax]['taxonomy'] = $tax;
				if (function_exists('icl_get_languages')) {
					$taxonomies[$tax]['field'] = 'term_id';
				}
				$taxonomies[$tax]['terms'][]  = $terms;
				$taxonomies[$tax]['operator'] = 'IN';
				$i++;
			}
		}
			
		// Add tax query and relation or for everything
		$tax_query['relation'] = 'OR';
		foreach($taxonomies as $query) {
			$tax_query[] = $query;
		}
		
		// Get post order and orderby key
		$post_order   = $this->grid_data['order'];
		$post_orderby = $this->grid_data['orderby'];
		
		$post_orderby_val = null;
		if ($post_orderby) {
			$gap = '';
			foreach($post_orderby as $orderby) {
				$post_orderby_val .= $orderby.$gap;
				$gap = ' ';
			}
		}

		// Grab custom post ID to preserve post orderby
		$post_orderby_id = $this->grid_data['orderby_id'];
		$post_orderby_id = !empty($post_orderby_id) ? explode(',', $post_orderby_id) : array();
	
		//get all page ids
		$all_page = array();
		if (in_array('page', $post_type)) {
			$all_pages = $base->get_all_page_id();
			$all_page  = array();
			foreach ($all_pages as $ID => $pages) {
				$all_page[] = $ID;
			}
		}
		$pages_id = $this->grid_data['pages_id'];
		$pages_id = !empty($pages_id) ? $pages_id : $all_page;
		
		// excluded items
		$excluded_items = $this->grid_data['post_not_in'];
		$excluded_items = (!empty($excluded_items)) ? explode(', ', $excluded_items) : array();

		// revert process : declare page not__in, to get page and post at same time
		$post_not_in = array_diff($all_page, $pages_id);
		$post_not_in = array_merge ($post_not_in, $excluded_items);

		// preserve post ID order : merge existing page IDs with selected post IDs
		if (!empty($post_orderby_id) && in_array('post__in', $post_orderby)) {
			$post_in = array_merge($post_orderby_id, $pages_id);
		} else {
			$post_in = array();
		}
		
		// most recently viewed woocommerce product from use cookie
		if (class_exists('WooCommerce') && strpos($post_orderby_val, 'woocommerce_recently_viewed') !== false) {
			global $woocommerce;
			$viewed_products = !empty($_COOKIE['woocommerce_recently_viewed']) ? (array) explode('|', $_COOKIE['woocommerce_recently_viewed']) : array();
			$viewed_products = array_filter(array_map('absint', $viewed_products ));
			$post_in = array_merge($post_in, $viewed_products);
		}

		// If Attachment force post order and image order from drag & drop image gallery field
		if (in_array('attachment',$post_type)) {
			
			// add post statut inherit to post_statut options to retrieve attachments
			array_push($post_status, 'inherit');
			
			if (sizeof($post_type) > 1 && $gallery_img) {
				$ids = get_posts(array(
					'post_type'      => 'attachment', 
					'post_mime_type' => 'image', 
					'post_status'    => 'inherit', 
					'posts_per_page' => -1,
					'fields'         => 'ids',
				));
				$img_ids = array();
				foreach ($ids as $id) {
					$img_ids[] = $id;
				}
				$img_ids = array_diff($img_ids,$gallery_img);
				$post_not_in = array_merge($post_not_in,$img_ids);
			} else {
				$post_orderby_val = 'post__in';
				$post_in = $gallery_img;
			}
			
		}

		// remove filter category and force post_in ids of all cat to keep selected page
		if ($post_cats && in_array('page', $post_type)) {
			$post_ids_cat = $base->get_post_ids_by_cat($post_type,$tax_query,$post_cats_child,$terms,$tax);
			$post_in      = array_merge($pages_id, $post_orderby_id, $post_ids_cat);
			$tax_query    = null;
		}
		
		//retrieve meta_key to order by meta_value
		$meta_key = null;
		if ($base->strpos_array($post_orderby_val,array('meta_value','meta_value_num')) !== false) {
			$meta_key = $this->grid_data['meta_key'];
		}
	
		// get authors filter
		$author  = null;
		$authors = $this->grid_data['author'];
		$author  = (is_array($authors) && count($authors) > 1) ? implode(',', array_map(function($item) { return $item; }, $authors)) : $authors[0];
		
		// get meta query
		$meta_query = $this->meta_query();
		
		// retrieve current page
		if (isset($grid_page)) {
			$paged = $grid_page;	
		} else {
			$paged = (get_query_var('paged')) ? max(1, get_query_var('paged')) : max(1, get_query_var('page'));
		}
		
		// get query args
		global $tg_query_args;
		// setup the query args
		// WordPress already takes care of the necessary sanitization in querying the database
		$tg_query_args = array( 
			'post_type'      => $post_type, 
			'posts_per_page' => $posts_per_page, 
			'post_status'    => $post_status,
			'author'         => $author,
			'paged'          => $paged,
			'post__in'       => $post_in,
			'post__not_in'   => $post_not_in,
			'order'          => $post_order,
			'orderby'        => $post_orderby_val,
			'tax_query'      => $tax_query,
			'meta_key'       => $meta_key,
			'meta_query'     => $meta_query,
			'suppress_filters' => false
		);

		return $tg_query_args;
		
	}
	
	/**
	* Build the meta query
	* @since 1.0.0
	*/
	public function meta_query() {
		
		// get meta query info
		$meta_query = $this->grid_data['meta_query'];
		$meta_query = json_decode($meta_query, TRUE);
		
		// loop options and rebuild query array logic
		if ($meta_query && count($meta_query) > 1) {
			$i = 0;
			$y = 0;
			$meta = array();
			$relation = false;
			foreach ($meta_query as $meta_keys=>$meta_key) {
				if (isset($meta_key['relation']) && $i == 0) {
					$meta['relation'] = $meta_key['relation'];
				} else if (isset($meta_key['relation'])) {
					$meta[$i] = array();
					$meta[$i]['relation'] = $meta_key['relation'];
					$relation = true;
					$y = 0;
					$i++;
				} else {
					if ($relation == true) {
						$meta[$i-1][$y]['key']      = $meta_key['key'];
						$meta[$i-1][$y]['value']    = $meta_key['value'];
						$meta[$i-1][$y]['compare']  = $meta_key['compare'];
						if (isset($meta_key['type'])) {
							$meta[$i-1][$y]['type'] = $meta_key['type'];
						}
						$y++;
					} else {
						$meta[$i]['key']      = $meta_key['key'];
						$meta[$i]['value']    = $meta_key['value'];
						$meta[$i]['compare']  = $meta_key['compare'];
						if (isset($meta_key['type'])) {
							$meta[$i]['type'] = $meta_key['type'];
						}
						$i++;
					}				
				}			
			}
		} else {
			$meta = null;
		}

		return $meta;
		
	}
	
	/**
	* Run the main Query for all instance (to get item in loop & pagination)
	* @since 1.0.0
	*/
	public function run_query() {
		
		// switch from different source type (post_type/social media)
		switch ($this->grid_data['source_type']) {
			case 'post_type':
				$this->post_query();
				break;
			case 'instagram':
				$this->instagram_query();
				break;
			case 'youtube':
				$this->youtube_query();
				break;
			case 'vimeo':
				$this->vimeo_query();
				break;
		}

	}
	
	/**
	* Run the main Query for all instance (to get item in loop & pagination)
	* @since 1.0.0
	*/
	public function post_query() {
		
		// retieve custom query arg and set new query
		global $tg_query_args, $tg_grid_query, $tg_is_ajax;
		
		// if no recently viewed products from woocommerce
		$no_product_viewed = false;
		if (class_exists('WooCommerce') && strpos($tg_query_args['orderby'], 'woocommerce_recently_viewed') !== false && empty($tg_query_args['post__in'])) {
			echo '<div class="tg-no-post">';
			_e( 'You have not viewed any product yet!', 'tg-text-domain' );
			echo '</div>';
			$no_product_viewed = true;
			$tg_query_args['post__in'] = array(0);
		}
		
		$tg_grid_query = new WP_Query($tg_query_args);
		
		// if no post was found
		if ($tg_grid_query->post_count == 0 && !$tg_is_ajax && !$no_product_viewed) {
			echo '<div class="tg-no-post">';
			_e( 'No post was found with your current grid settings.', 'tg-text-domain' );
			echo '<br><br>';
			_e( 'You should verify if you have posts inside the current selected post type(s) and if the meta key filter is not too much restrictive.', 'tg-text-domain' );
			echo '</div>';
			return false;
		}
		
		// check if an ajax method  is used in the current grid
		$ajax_method = array_filter($this->grid_data, function($s){
			$ajax_button = (is_string($s)) ? strpos($s,'get_pagination') : false;
			$ajax_pages  = (is_string($s)) ? strpos($s,'get_ajax_button') : false;
			$ajax_method = ($ajax_button || $ajax_pages) ? true : false;
			return $ajax_method;
		});

			
		// get all items skin if exist
		global $tg_item_skins;
			
		// if new item can be appended then get all query post
		if ($ajax_method) {
			$no_page_query = $tg_query_args;
			$no_page_query['posts_per_page'] = '-1';
			$posts = get_posts($no_page_query);
		// else get all item loaded in the grid 
		} else {
			$posts = $tg_grid_query->posts;
		}
			
		// get all skins used in the current grid
		$tg_item_skins = wp_list_pluck($posts, 'the_grid_item_skin');
		
	}
	
	/**
	* Run Instagram loop
	* @since 1.0.0
	*/
	public function instagram_query() {
		
		global $tg_social_items, $tg_is_ajax;
		
		// if no access token
		$access_token = get_option('the_grid_instagram_api_key', '');
		if (empty($access_token)) {
			echo '<div class="tg-no-post">';
			_e( 'You didn\'t authorize The Grid to', 'tg-text-domain' );
			echo ' <a style="text-decoration: underline;" href="';
			echo admin_url('admin.php?page=the_grid_global_settings');
			echo '">';
			_e( 'connect to Instagram.', 'tg-text-domain' );
			echo'</a>';
			echo '</div>';
			$tg_social_items = null;
			return false;
		}

		$intagram = new The_Grid_Instagram();
		
		// set instagram value (username, hashtags, item number)
		$usernames = $this->grid_data['instagram_username']; // strings
		$hashtags  = $this->grid_data['instagram_hashtag'];  // strings
		$count     = $this->grid_data['item_number'];
		
		// retrieve corresponding media
		$instagram_data  = $intagram->get_data('media', $usernames, $hashtags, $count);
		$tg_social_items = $instagram_data['content'];
		
		// add data attribute for instagram (for ajax callback)
		$this->grid_data['social_data'] = htmlspecialchars(json_encode($instagram_data['ajax_data']), ENT_QUOTES, 'UTF-8');
		
		// if an error occurs
		if (!empty($instagram_data['error'])) {
			echo '<div class="tg-no-post">';
			_e( 'Sorry an error occurs: ', 'tg-text-domain' );
			echo $instagram_data['error'];
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
		// if no post was found
		if ((!isset($tg_social_items) || empty($tg_social_items)) && !$tg_is_ajax) {
			echo '<div class="tg-no-post">';
			_e( 'No content was found for the current ursername(s) and/or hashtag(s).', 'tg-text-domain' );
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
	}
	
	/**
	* Run Youtube loop
	* @since 1.0.0
	*/
	public function youtube_query() {
		
		global $tg_social_items, $tg_is_ajax;

		// if no access token
		$youtube_api = get_option('the_grid_youtube_api_key', '');
		if (empty($youtube_api)) {
			echo '<div class="tg-no-post">';
			_e( 'You didn\'t authorize The Grid to', 'tg-text-domain' );
			echo ' <a style="text-decoration: underline;" href="';
			echo admin_url('admin.php?page=the_grid_global_settings');
			echo '">';
			_e( 'connect to Youtube.', 'tg-text-domain' );
			echo'</a>';
			echo '</div>';
			$tg_social_items = null;
			return false;
		}

		// get youtube channel value
		$order    = $this->grid_data['youtube_order'];  // strings
		$source   = $this->grid_data['youtube_source'];   // strings
		$channel  = $this->grid_data['youtube_channel'];  // strings
		$playlist = $this->grid_data['youtube_playlist']; // strings
		$videos   = $this->grid_data['youtube_videos'];   // strings
		$count    = $this->grid_data['item_number'];      // strings
		
		// run Youtube class
		$youtube = new The_Grid_Youtube();
		$youtube_data    = $youtube->get_data($order, $source, $channel, $playlist, $videos, $count);
		$tg_social_items = $youtube_data['content'];
		
		// add data attribute for instagram (for ajax callback)
		$this->grid_data['social_data'] = htmlspecialchars(json_encode($youtube_data['ajax_data']), ENT_QUOTES, 'UTF-8');

		// if an error occurs
		if (!empty($youtube_data['error'])) {
			echo '<div class="tg-no-post">';
			_e( 'Sorry an error occurs: ', 'tg-text-domain' );
			echo $youtube_data['error'];
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
		// if no post was found
		if ((!isset($tg_social_items) || empty($tg_social_items)) && !$tg_is_ajax) {
			echo '<div class="tg-no-post">';
			_e( 'No content was found for the current Channel/Playlist/Videos.', 'tg-text-domain' );
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
	}
	
	/**
	* Run Vimeo loop
	* @since 1.0.0
	*/
	public function vimeo_query() {
		
		global $tg_social_items, $tg_is_ajax;

		// if no access token
		$vimeo_api = get_option('the_grid_vimeo_api_key', '');
		if (empty($vimeo_api)) {
			echo '<div class="tg-no-post">';
			_e( 'You didn\'t authorize The Grid to', 'tg-text-domain' );
			echo ' <a style="text-decoration: underline;" href="';
			echo admin_url('admin.php?page=the_grid_global_settings');
			echo '">';
			_e( 'connect to Vimeo.', 'tg-text-domain' );
			echo'</a>';
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
		$vimeo = new The_Grid_Vimeo();
		
		// get youtube channel value
		$sort    = $this->grid_data['vimeo_sort'];    // strings
		$order   = $this->grid_data['vimeo_order'];   // strings
		$source  = $this->grid_data['vimeo_source'];  // strings
		$user    = $this->grid_data['vimeo_user'];    // strings
		$album   = $this->grid_data['vimeo_album'];   // strings
		$group   = $this->grid_data['vimeo_group'];   // strings
		$channel = $this->grid_data['vimeo_channel']; // strings
		$count   = $this->grid_data['item_number'];   // strings
		
		$vimeo_data    = $vimeo->get_data($sort, $order, $source, $user, $album, $group, $channel, $count);
		$tg_social_items = $vimeo_data['content'];
		
		// add data attribute for instagram (for ajax callback)
		$this->grid_data['social_data'] = htmlspecialchars(json_encode($vimeo_data['ajax_data']), ENT_QUOTES, 'UTF-8');
		
		// if an error occurs
		if (!empty($vimeo_data['error'])) {
			echo '<div class="tg-no-post">';
			_e( 'Sorry an error occurs: ', 'tg-text-domain' );
			echo $vimeo_data['error'];
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
		// if no post was found
		if ((!isset($tg_social_items) || empty($tg_social_items)) && !$tg_is_ajax) {
			echo '<div class="tg-no-post">';
			_e( 'No content was found for the current User/Album/Group/Channel.', 'tg-text-domain' );
			echo '</div>';
			$tg_social_items = null;
			return false;
		}
		
	}

	/**
	* Reset the main post type query
	* @since 1.0.0
	*/
	public function reset_query() {
		
		if ($this->grid_data['source_type'] == 'post_type') {
			// reset custom query grid
			wp_reset_query();
			// clean up after the query and pagination
			wp_reset_postdata();
		}
		
	}
	
	/**
	* Get & set main options for the grid data (For JS script)
	* @since 1.0.0
	*/
	public function grid_data_processing() {
		
		global $pagenow;

		// grid name
		$grid_name = $this->grid_data['name'];
		
		// grid type (masonry/grid)
		$grid_style = $this->grid_data['style'];
		
		// grid layout (horizontal/vertical)
		$grid_layout = $this->grid_data['layout'];
		
		// grid layout RTL
		$grid_rtl = $this->grid_data['rtl'];
		
		// grid filter combination
		$filter_comb = $this->grid_data['filter_combination'];
		
		// grid filter logic
		$filter_logic = $this->grid_data['filter_logic'];
		
		// grid filter on load
		$filters        = $this->grid_data['filter_onload'];
		$filters_sep    = ($filter_logic === 'OR') ? ',' : '';
		$active_filters = array();
		$filter_onload  = null;
		foreach ($filters as $filter=>$value) {
			$filter    = explode(':', $value);
			$filter_id = (is_numeric($filter[1])) ? '.f'.$filter[1] : $filter[1];
			$filter_onload .= $filter_id.$filters_sep;
		}
		$filter_onload = rtrim($filter_onload, ',');
		
		// grid sortby on load
		$sortby_onload    = $this->grid_data['sort_by_onload'];
		$sortby_meta_data = get_option('the_grid_custom_meta_data', '');
		if (isset($meta_data) && !empty($meta_data) && json_decode($meta_data) != null) {
			$sortby_meta_data = json_decode($sortby_meta_data, true);
			foreach($sortby_meta_data as $value) {
				if (in_array($sortby_onload, $value) != false) {
					$sortby_onload = strtolower($sortby_onload[0] == '_') ? substr($sortby_onload, 1) : $sortby_onload;
				}
			}
		}

		// grid sort order on load
		$order_onload = $this->grid_data['sort_order_onload'];
		
		// grid full width mode
		$grid_full_w = $this->grid_data['full_width'];
		
		// grid full height mode (horizontal)
		$grid_full_h = $this->grid_data['full_height'];
		
		// slider row number (only horizontal mode)
		$grid_row_nb = $this->grid_data['row_nb'];

		// grid items gutter (masonry/grid/justified)
		$item_gutters = json_encode($this->grid_data['gutters'], true);
		
		// grid items ratio
		$item_x_ratio = $this->grid_data['item_x_ratio'];
		$item_y_ratio = $this->grid_data['item_y_ratio'];
		$item_ratio   = number_format((float)$item_x_ratio/$item_y_ratio, 2, '.', '');
		
		// slider attribute
		$swingSpeed = $this->grid_data['slider_swingSpeed'];
		$itemNav    = $this->grid_data['slider_itemNav'];
		$autoplay   = $this->grid_data['slider_autoplay'];
		$cycle      = $this->grid_data['slider_cycleInterval'];
		$cycleBy    = $this->grid_data['slider_cycleBy'];		
		$startAt    = $this->grid_data['slider_startAt'];

		// grid columns settings
		$grid_cols = json_encode($this->grid_data['columns'], true);
		// grid rows height settings
		$grid_rows = json_encode($this->grid_data['rows_height'], true);
		
		// Get item animation style
		$anim_name = new The_Grid_Item_Animation();
		$anim_arr  = $anim_name->get_animation_name();
		$animation = $this->grid_data['animation'];
		$animation = (isset($anim_arr[$animation])) ? $animation : 'none';
		// Get animation duration (item transition)
		$transition = $this->grid_data['transition'];
		$transition = ($transition == 0 || $animation == 'none') ? 0 : $transition.'ms';
		$animation  = json_encode($anim_arr[$animation]);
		
		// ajax functionnality
		$posts_per_page = $this->grid_data['item_number'];
		$ajax_method    = $this->grid_data['ajax_method'];
		$ajax_delay     = $this->grid_data['ajax_item_delay'];
		
		// preloader functionnality
		$preloader  = $this->grid_data['preloader'];
		$item_delay = $this->grid_data['item_delay'];
		
		// Gallery SlideShow
		$gallery = $this->grid_data['gallery_slide_show'];
		
		// check layout values
		$data_attr  = ' data-name="'.esc_attr($grid_name).'" ';
		$data_attr .= ' data-style="'.esc_attr($grid_style).'"';
		$data_attr .= ' data-row="'.esc_attr($grid_row_nb).'"';
		$data_attr .= ' data-layout="'.esc_attr($grid_layout).'"';
		$data_attr .= ' data-rtl="'.esc_attr($grid_rtl).'"';
		$data_attr .= ' data-filtercomb="'.esc_attr($filter_comb).'"';
		$data_attr .= ' data-filterlogic="'.esc_attr($filter_logic).'"';
		$data_attr .= ' data-filterload ="'.esc_attr($filter_onload).'"';
		$data_attr .= ' data-sortbyload ="'.esc_attr($sortby_onload).'"';
		$data_attr .= ' data-orderload ="'.esc_attr($order_onload).'"';
		$data_attr .= ' data-fullwidth="'.esc_attr($grid_full_w).'"';
		$data_attr .= ' data-fullheight="'.esc_attr($grid_full_h).'"';
		$data_attr .= ' data-gutters="'.esc_attr($item_gutters).'"';
		$data_attr .= ' data-slider=\'{"itemNav":"'.esc_attr($itemNav).'","swingSpeed":'.esc_attr($swingSpeed).',"cycleBy":"'.esc_attr($cycleBy).'","cycle":'.esc_attr($cycle).',"startAt":'.esc_attr($startAt).'}\'';
		$data_attr .= ' data-ratio="'.esc_attr($item_ratio).'"';
		$data_attr .= ' data-cols="'.esc_attr($grid_cols).'"';
		$data_attr .= ' data-rows="'.esc_attr($grid_rows).'"';
		$data_attr .= ' data-animation="'.esc_attr($animation).'"';
		$data_attr .= ' data-transition="'.esc_attr($transition).'"';
		$data_attr .= ' data-ajaxmethod="'.esc_attr($ajax_method).'"';
		$data_attr .= ' data-ajaxdelay="'.esc_attr($ajax_delay).'"';
		$data_attr .= ' data-preloader="'.esc_attr($preloader).'"';
		$data_attr .= ' data-itemdelay="'.esc_attr($item_delay).'"';
		$data_attr .= ' data-gallery="'.esc_attr($gallery).'"';
		// Social data attribute for ajax call if needed
		$data_attr .= ' data-social="'.$this->grid_data['social_data'].'"';

		// add data attribute for js plugin
		global $tg_grid_data;
		$tg_grid_data['layout_data'] = $data_attr;
		
	}
	
	/**
	* Generate main css class for the grid
	* @since 1.0.0
	*/
	public function grid_classes_processing() {
		
		// main settings for the grid wrapper
		$grid_ID     = $this->grid_data['ID'];
		$grid_layout = $this->grid_data['layout'];
		$grid_style  = $this->grid_data['style'];
		$fullHeight  = $this->grid_data['full_height'];
		$preloader   = $this->grid_data['preloader'];
		$css_class   = $this->grid_data['css_class'];
		$nav_class   = $this->grid_data['navigation_style'];
		
		// set main wrapper classes
		$fullHeight  = ($grid_style == 'grid' && $fullHeight) ? 'full-height' : null;
		$load_class  = ($preloader) ? 'tg-grid-loading' : null;
		$css_classes = trim($css_class.' '.$load_class.' '.$nav_class.' '.$fullHeight);
		
		// add new var to global setting
		global $tg_grid_data;
		$tg_grid_data['wrapper_css_class'] = $css_classes;
		
	}
	
	/**
	* Generate all css for the grid
	* @since 1.0.0
	*/
	public function grid_styles_processing() {
		
		// retrieve main styles from child style class
		$child_style = new The_Grid_Style();
		$styles = $child_style->styles_processing();

		// add new var to global setting
		global $tg_grid_data;
		$tg_grid_data['grid_css'] = $styles;

	}
	
	/**
	* Retrieve all elements added in each grid area (layout tab - Grid Settings)
	* @since 1.0.0
	*/
	public function grid_areas_processing() {

		// retrieve all registered areas
		$data  = array_keys($this->grid_data);
		$areas = preg_grep('/area_/i', $data);
		
		// loop through each area
		foreach($areas as $area) {
			$area_content = array();
			$data = $this->grid_data[$area];
			$data = json_decode($data, true);
			if (isset($data['functions']) && !empty($data['functions'])) {
				// build each area content
				foreach($data['functions'] as $function) {
					$index    = substr($function, -1);
					$function = str_replace('the_grid_', '', $function);
					$function = (strrpos($function, 'get_filters')!== false) ? 'get_filters' : $function;
					$content  = (method_exists($this, $function)) ? $this->$function($index) : null;
					// push area content
					if ($content) {
						array_push($area_content, $content);
					}
				}
			}
			// set global area content var
			global $tg_grid_data;
			$tg_grid_data[$area.'_elements'] = $area_content;
		}
	
	}
	
	/**
	* Build filter buttons/dropdown list
	* @since 1.0.0
	*/
	public function get_filters($index) {
		
		$filters = $this->grid_data['filters_'.$index];
		$order   = $this->grid_data['filters_order_'.$index];
		$type    = $this->grid_data['filter_type_'.$index];

		if ($this->grid_data['source_type'] == 'post_type') {
			$filters = $this->get_post_terms($filters, $order);
		} else {
			$filters = null;
		}

		if (!empty($filters) && is_array($filters)) {
			
			// register main data to build filter template
			global $tg_grid_data;
			$tg_grid_data['filter_all_text'] = $this->grid_data['filter_all_text_'.$index];
			$tg_grid_data['filter_count']    = $this->grid_data['filter_count_'.$index];
			$tg_grid_data['filters']         = $this->sort_array($filters, $order);
			$tg_grid_data['filter_dropdown_title'] = $this->grid_data['filter_dropdown_title_'.$index];
			
			switch ($type) {
				case 'button':
					$filters = $this->get_filter_buttons();

					break;
				case 'dropdown':
					$filters = $this->get_filter_dropdown_list();
					break;
			}

		}
		
		return $filters;
		
	}
	
	/**
	* Retrieve all post type terms
	* @since 1.0.0
	*/
	public function get_post_terms($terms, $order) {
			
		$i = 0;
		$filters = array();
		
		$terms = json_decode($terms);
		
		if (isset($terms) && !empty($terms)) {
			foreach ($terms as $term) {
				$name = get_term($term->id, $term->taxonomy);
				
				if (isset($name->name)) {
					$filters[$i]['id']    = $term->id;
					$filters[$i]['name']  = $name->name;
					$filters[$i]['taxo']  = $term->taxonomy;
					$filters[$i]['count'] = $name->count;
					$i++;
				}
			}
		}
		
		return $filters;
		
	}
	
	/**
	* Sort filters array
	* @since 1.0.0
	*/
	public function sort_array($array, $order) {
		
		switch ($order) {
			case 'number_asc':
				usort($array, function ($a,$b){ return $a['count'] - $b['count']; });
				break;
			case 'number_desc':
				usort($array, function ($a,$b){ return $b['count'] - $a['count']; });
				break;
			case 'alphabetical_asc':
				usort($array, function ($a,$b){ return strcmp($a['name'], $b['name']); });
				break;
			case 'alphabetical_desc':
				usort($array, function ($a,$b){ return strcmp($b['name'], $a['name']); });
				break;
		}
		
		return $array;

	}
	
	/**
	* Output main wrapper of The Grid
	* @since 1.0.0
	*/
	public function the_grid_content() {
		
		// format data
		$this->data_processing();
		
		// switch from different source type (post_type/social media)
		if ($this->grid_data['source_type'] == 'post_type') {
			$this->post_type_content();
		} else {
			$this->social_type_content();
		}
			
	}
	
	/**
	* Output the grid post type content
	* @since 1.0.0
	*/
	public function post_type_content() {
		
		global $tg_grid_query;
		
		if ($tg_grid_query->have_posts()) {
			// output grid templates
			$this->the_grid_templates();
			// reset the custom query
			$this->reset_query();
		}
			
	}
	
	/**
	* Output the grid social media content
	* @since 1.0.0
	*/
	public function social_type_content() {
		
		global $tg_social_items;
		
		if (!empty($tg_social_items)) {
			// output grid templates
			$this->the_grid_templates();
		}
		
	}
	
	/**
	* Retrieve main template to generate The Grid markup & content
	* @since 1.0.0
	*/
	public function the_grid_templates() {

		$grid_layout = $this->grid_data['layout'];
			
		// Start grid wrapper
		tg_get_template_part('wrapper','start');
		
		// Top Areas
		tg_get_template_part('area','top1');
		tg_get_template_part('area','top2');

		// Open slider wrapper
		if ($grid_layout == 'horizontal') {
			tg_get_template_part('slider','start');
		}
		
		// Grid item holder start
		tg_get_template_part('grid','holder-start');
			
		// Grid items
		new The_Grid_Item();
		
		// Onscroll ajax massage
		if ($grid_layout == 'vertical') {
			tg_get_template_part('grid','ajax-message');
		}
		
		// Grid item holder end
		tg_get_template_part('grid','holder-end');	
		
		// close slider wrapper	
		if ($grid_layout == 'horizontal') {	
			tg_get_template_part('area','left');
			tg_get_template_part('area','right');
			tg_get_template_part('slider','end');
		}
			
		// Bottom Areas
		tg_get_template_part('area','bottom1');
		tg_get_template_part('area','bottom2');
			
		// Grid custom script
		tg_get_template_part('grid','jquery');
			
		// Grid preloader
		if ($this->grid_data['preloader']) {
			tg_get_template_part('grid','preloader');
		}
			
		// Close grid wrapper
		tg_get_template_part('wrapper','end');

	}
	
	/**
	* Filter button template
	* @since 1.0.0
	*/
	public function get_filter_buttons() {
		ob_start();
		tg_get_template_part('filter','buttons');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Filter dropdown list template
	* @since 1.0.0
	*/
	public function get_filter_dropdown_list() {
		ob_start();
		tg_get_template_part('filter','dropdown-list');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Sorter dropdown list template
	* @since 1.0.0
	*/
	public function get_sorters() {
		ob_start();
		tg_get_template_part('grid','sorter');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Search field
	* @since 1.0.0
	*/
	public function get_search_field() {
		ob_start();
		tg_get_template_part('grid','search-field');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Ajax button template
	* @since 1.0.0
	*/
	public function get_ajax_button() {
		ob_start();
		tg_get_template_part('grid','load-more');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Pagination template
	* @since 1.0.0
	*/
	public function get_pagination() {
		ob_start();
		tg_get_template_part('grid','pagination');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Slider bullets template
	* @since 1.0.0
	*/
	public function get_slider_bullets() {
		ob_start();
		tg_get_template_part('slider','bullets');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Slider left arrow template
	* @since 1.0.0
	*/
	public function get_left_arrow() {
		ob_start();
		tg_get_template_part('slider','left-arrow');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Slider right arrow template
	* @since 1.0.0
	*/
	public function get_right_arrow() {
		ob_start();
		tg_get_template_part('slider','right-arrow');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Instagram header template
	* @since 1.0.0
	*/
	public function get_instagram_header() {
		ob_start();
		tg_get_template_part('header','instagram');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Youtube header template
	* @since 1.0.0
	*/
	public function get_youtube_header() {
		ob_start();
		tg_get_template_part('header','youtube');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	* Vimeo header template
	* @since 1.0.0
	*/
	public function get_vimeo_header() {
		ob_start();
		tg_get_template_part('header','vimeo');	
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

}