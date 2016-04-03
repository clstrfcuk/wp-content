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

class The_Grid_Base {
	
	/**
	* Get all grid names
	* @since 1.0.0
	*/
	public static function get_all_grid_names() {
		$post_args = array(
			'post_type'      => 'the_grid',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);
		$grids = get_posts($post_args); 
		if(!empty($grids)){
			foreach($grids as $grid){
				$grid_name[$grid->post_title] = $grid->ID;
			}
			return($grid_name);
		}
	}
	
	/**
	* Get grid list names
	* @since 1.0.0
	*/
	public static function get_grid_list_names() {
		$grid_list  = null;
		$grid_names = self::get_all_grid_names();
		if (isset($grid_names) && !empty($grid_names)) {
			foreach ($grid_names as $grid_name => $grid_ID) {
				$grid_list .= $grid_name.':'.$grid_ID.',';
			}
		}
		$grid_list = rtrim($grid_list, ',');
		return $grid_list;
	}
	
	/**
	* Get users/authors
	* @since 1.0.0
	*/
	public static function get_all_users() {
		$args  = array(
			'orderby' => 'display_name'
		);
		$wp_user_query = new WP_User_Query($args);
		$authors = $wp_user_query->get_results();
		$authors_ID = array();
		if (!empty($authors)) {
			foreach ($authors as $author) {
				$author_info = get_userdata($author->ID);
				$authors_ID[$author->ID] = $author_info->user_nicename;
			}
		}
		return $authors_ID;
	}

	/**
	* Get post types & categories.
	* @since 1.0.0
	*/
	public static function get_post_types_and_categories(){	
		$post_types = self::get_all_categories(true);
		$counter = 0; // prevent to override already set disable option / name category
		$post_types_taxonomies = array();
		foreach($post_types as $post_type => $categories) {
			$taxonomies = array();
			foreach($categories as $taxonomy) {	
				$counter++;
				$category_count = count($taxonomy['cats']);
				$taxonomy_name  = $taxonomy['name'];
				$taxonomy_title = $taxonomy['title'];
				$taxonomies['post_type:'.$post_type.', taxonomy:'.$taxonomy_name.', option: option_disabled'.$counter] = $taxonomy_title.' ('.$category_count.')';
				foreach($taxonomy['cats'] as $category_ID => $category_title) {
					$taxonomies['post_type:'.$post_type.', taxonomy:'.$taxonomy_name.', id:'.$category_ID] = $category_title;
				}	
			}
			$post_types_taxonomies[$post_type] = $taxonomies;
		}
		return($post_types_taxonomies);
	}
	
	/**
	* Get post types with associated categories
	* @since 1.0.0
	*/
	public static function get_all_categories() {
		$post_types = self::get_all_taxonomies();
		$post_types_categories = array();
		foreach($post_types as $name => $taxonomy_array){
			$taxonomies = array();
			foreach($taxonomy_array as $taxonomy_name => $taxonomy_title){
				$categories = self::get_associated_categories($taxonomy_name);
				if(!empty($categories)) {
					$taxonomies[] = array(
						"name"  => $taxonomy_name,
						"title" => $taxonomy_title,
						"cats"  => $categories
					);
				}
			}
			$post_types_categories[$name] = $taxonomies;
		}
		return($post_types_categories);
	}
	
	/**
	* Get post types array with taxomonies
	* @since 1.0.0
	*/
	public static function get_all_taxonomies() {
		$post_types = self::get_all_post_types();
		foreach($post_types as $post_type => $title){
			$taxomonies = self::get_taxomonies_post_type($post_type);
			$post_types[$post_type] = $taxomonies;
		}
		return($post_types);
	}
	
	/**
	* Get post type with taxomonies names
	* @since 1.0.0
	*/
	public static function get_taxomonies_post_type($post_types) {
		$taxonomies = get_object_taxonomies(
			array('post_type' => $post_types),
			'objects'
		);	
		$names = array();
		foreach($taxonomies as $key => $values) {
			$names[$values->name] = $values->labels->name;
		}
		return($names);
	}
	
	/**
	* Get All Post Types (builtin and custom)
	* @since 1.0.0
	*/
	public static function get_all_post_types() {
		$builtin_post_types = array(
			'post' => 'post',
			'page' => 'page',
			'attachment' => 'Media Library'
		);
		$custom_post_types = get_post_types(
			array('_builtin' => false)
		);
		unset($custom_post_types['the_grid']);
		if ( class_exists( 'WooCommerce' ) ) {
			unset($custom_post_types['shop_order']);	
		}
		$post_types = array_merge($builtin_post_types, $custom_post_types);
		foreach($post_types as $key => $type){
			$post_type_object = get_post_type_object($type);
			if(empty($post_type_object)){
				$post_types[$key] = $type;
				continue;
			}
			$post_types[$key] = $post_type_object->labels->name;
		}
		return($post_types);
	}
	
	/**
	* Get post categories list with associated id & title
	* @since 1.0.0
	*/
	public static function get_associated_categories($taxonomy = 'category') {
		if(strpos($taxonomy,',') !== false){
			$taxonomies = explode(',', $taxonomy);
			$categories = array();
			foreach($taxonomies as $taxonomy) {
				$associated_categories = self::get_associated_categories($taxonomy);
				$categories = array_merge($categories,$associated_categories);
			}
			return($categories);
		}
		$args = array('taxonomy' => $taxonomy);
		$cats = get_categories($args);
		$subcat = array();
		$categories = array();
		foreach($cats as $cat){
			$numItems = $cat->count;
			$id       = $cat->cat_ID;
			$subcat   = array_merge(get_term_children( $cat->cat_ID, $taxonomy ),$subcat);
			if (in_array($id, $subcat)) {
				$title = '&#8212; '.$cat->name . ' ('.$numItems.')';
			} else {
				$title = $cat->name . ' ('.$numItems.')';
			}
			$categories[$id] = $title;
		}
		return($categories);
	}
	
	/**
	* Format cat name and ID array
	* @since 1.0.0
	*/
	public static function get_formated_categories() {
		$post_types_and_categories = self::get_post_types_and_categories();
		$cat = array();
		if(!empty($post_types_and_categories)){
			foreach($post_types_and_categories as $post_type => $ID) {
				$post_type_info   = get_post_type_object($post_type);
				$post_type_name   = $post_type_info->labels->name;
				$post_type_single = $post_type_info->name;
				$post_types[$post_type_single] = $post_type_name;
				foreach($ID as $id => $name) {
					$cat[$id] = $name;
				}
			}
		}
		return $cat;
	}
	
	/**
	* Retrieve all metadata
	* @since 1.0.0
	*/
	public static function get_all_meta_field() {
		$post_types = self::get_all_post_types();
		foreach( $post_types as $post_type => $value ) {
			if(post_type_exists($post_type)) { 
				$query_args = array(
					'post_type'   => $post_type,
					'numberposts' => 1,
					'post_status' => 'any'
				);
				$items = get_posts($query_args);
				if ($items) {
					foreach($items as $item) {
						$custom_field_keys = get_post_custom_keys($item->ID);
						if ($custom_field_keys) {
							foreach ($custom_field_keys as $key => $value) {
								$post_key[$post_type.':'.$value] = $value;
							}
						}
					}
				}
			}
		}
	}
	
	/**
	* Get all page title and id
	* @since 1.0.0
	*/
	public static function get_all_page_id() {
		$pages = get_pages();
		$pages_data = array();
		if (isset($pages) && !empty($pages)) {
			foreach ($pages as $page) {
				$pages_data[$page->ID] = $page->post_title;	
			} 
		}
		return $pages_data;
	}
	
	/**
	* Get post type in category
	* @since 1.0.0
	*/
	public static function get_post_ids_by_cat($post_type,$tax_query,$post_cats_child, $cat, $taxonomy='category') {
		return get_posts(array(
			'post_type'     => $post_type, 
			'numberposts'   => -1,
			'tax_query'     => $tax_query,
			'fields'        => 'ids',
		));
	}
	
	/**
	* List all available image sizes
	* @since 1.0.0
	*/
	public static function get_image_size() {
		$new_sizes = array();
		$added_sizes = get_intermediate_image_sizes();
		foreach($added_sizes as $key => $value) {
			$new_sizes[$value] = ucfirst(str_replace('_', ' ', $value));
		}
		$std_sizes = array(
			'full'      => __('Original Size', 'tg-text-domain'),
			'thumbnail' => __('Thumbnail', 'tg-text-domain'),
			'medium'    => __('Medium', 'tg-text-domain'),
			'large'     => __('Large', 'tg-text-domain')
		);
		$new_sizes = array_merge($std_sizes,$new_sizes);
		return $new_sizes;
	}
	
	/**
	* Sorting array data grid
	* @since 1.0.0
	*/
	public static function grid_sorting() {
		$sorting = array();
		$sorting['std-disabled'] = __( 'Standard', 'tg-text-domain'  );
		$sorting['none']        = __( 'None', 'tg-text-domain'  );
		$sorting['id']      = __( 'ID', 'tg-text-domain'  );
		$sorting['date']    = __( 'Date', 'tg-text-domain'  );
		$sorting['title']   = __( 'Title', 'tg-text-domain'  );
		$sorting['excerpt'] = __( 'Excerpt', 'tg-text-domain'  );
		$sorting['author']  = __( 'Author', 'tg-text-domain'  );
		$sorting['comment'] = __( 'Number of comment', 'tg-text-domain'  );
		$sorting['popular_post'] = __( 'Popular post', 'tg-text-domain'  );
		if ( class_exists( 'WooCommerce' ) ) {
			$sorting['woo_disabled']      = 'Woocommerce';
			$sorting['woo_SKU']           = __( 'SKU', 'tg-text-domain'  );
			$sorting['woo_regular_price'] = __( 'Price', 'tg-text-domain'  );
			$sorting['woo_sale_price']    = __( 'Sale Price', 'tg-text-domain'  );
			$sorting['woo_total_sales']   = __( 'Number of sales', 'tg-text-domain'  );
			$sorting['woo_featured']      = __( 'Featured Products', 'tg-text-domain'  );
			$sorting['woo_stock']         = __( 'Stock Quantity', 'tg-text-domain'  );
		}
		// add custom meta key to sorting
		$meta_data = get_option('the_grid_custom_meta_data', '');
		if (isset($meta_data) && !empty($meta_data) && json_decode($meta_data) != null) {
			$meta_data = json_decode($meta_data, true);
			$sorting['meta_disabled'] = __( 'Custom meta data', 'tg-text-domain' );
			foreach($meta_data as $meta) {
				$sorting[$meta['key']] = $meta['name'];
			}
		}
		return $sorting;
	}
	
	/**
	* Compress css function
	* @since 1.0.0
	*/
	public static function compress_css($styles) {
		$styles = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles);
    	$styles = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $styles);
		$styles = str_replace(' {', '{', $styles);
		$styles = str_replace('{ ', '{', $styles);
    	$styles = str_replace(' }', '}', $styles);
		$styles = str_replace( '} ', '}', $styles);
		$styles = str_replace( ';}', '}', $styles);
		$styles = str_replace( ', ', ',', $styles);
		$styles = str_replace('; ', ';', $styles);
		$styles = str_replace(': ', ':', $styles);
		return $styles;
	}
	
	/**
	* Delete specific transient name
	* @since 1.0.0
	*/
	public function delete_transient($grid_name) {
		
		global $wpdb;
		
		// transient SQL
		$sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
				FROM  $wpdb->options
				WHERE `option_name` LIKE '%transient_%'
				ORDER BY `option_name`";
				
		$results = $wpdb->get_results($sql);
		$transients = array();
		
		// loop through each transient option
		foreach ($results as $result) {
			// if transient option name matched then delete it
			if (strpos($result->name, $grid_name)) {
				$name = str_replace('_transient_','',$result->name);
				$name = str_replace('_transient_timeout_','',$result->name);
				delete_transient($name);
			}
		}	
		
	}
	
	/**
	* Lighter color function
	* @since 1.0.0
	*/
	public static function HEXLighter($col,$ratio) {
		$col = Array(hexdec(substr($col, 1, 2)), hexdec(substr($col, 3, 2)), hexdec(substr($col, 5, 2)));
		$lighter = Array(
			255-(255-$col[0])/$ratio,
			255-(255-$col[1])/$ratio,
			255-(255-$col[2])/$ratio
		);
		return "#".sprintf("%02X%02X%02X", $lighter[0], $lighter[1], $lighter[2]);
	}
	
	/**
	* Darker color function
	* @since 1.0.0
	*/
	public static function HEXDarker($col,$ratio) {
		$col = Array(hexdec(substr($col, 1, 2)), hexdec(substr($col, 3, 2)), hexdec(substr($col, 5, 2)));;
		$darker = Array(
			$col[0]/$ratio,
			$col[1]/$ratio,
			$col[2]/$ratio
		);
		return '#'.sprintf('%02X%02X%02X', $darker[0], $darker[1], $darker[2]);
	}
	
	/**
	* HEX to RGB function
	* @since 1.0.0
	*/
	public static function HEX2RGB($hex,$alpha=1) {
		$hex = str_replace("#", "", $hex);
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb['red']   = $r;
		$rgb['green'] = $g;
		$rgb['blue']  = $b;
		
		if ($alpha < 1) {
			$rgb = 'rgba('.$r.','.$g.','.$b.','.$alpha.')';
		}
		
		return $rgb;
	}
	
	/**
	* RGB to HEX function
	* @since 1.0.0
	*/
	public static function RGB2HEX($rgb) {
	   $hex  = str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex;
	}
	
	/**
	* Brightness Color function
	* @since 1.0.0
	*/
	public static function brightness($hex) {

		$rgb = self::HEX2RGB($hex);
		
		$r = ((float)$rgb['red']) / 255.0;
		$g = ((float)$rgb['green']) / 255.0;
		$b = ((float)$rgb['blue']) / 255.0;

		$maxC = max($r, $g, $b);
		$minC = min($r, $g, $b);

		$l = ($maxC + $minC) / 2.0;
		$l = (int)round(255.0 * $l);
		
		if($l > 200) {
			$brightness = 'bright';
		} else {
			$brightness = 'dark';
		}
		
		return $brightness;

	}
	
	/**
	* Search in array strpos function
	* @since 1.0.0
	*/
	public static function strpos_array($haystack, $needles, $offset = 0) {
		if (is_array($needles)) {
			foreach ($needles as $needle) {
				$pos = self::strpos_array($haystack, $needle);
				if ($pos !== false) {
					return true;
				}
			}
			return false;
		} else {
			return strpos($haystack, $needles, $offset);
		}
	}
	
	/**
	* Elaspsed date format (ago)
	* @since 1.0.0
	*/
	public function time_elapsed_string($datetime, $full = false) {

		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = (array) $now->diff($ago);

		$diff['w']  = floor($diff['d'] / 7);
		$diff['d'] -= $diff['w'] * 7;
		
		$string = array(
			'y' => array(
				's' => __('year', 'tg-text-domain'),
				'p' => __('years', 'tg-text-domain')
			),
			'm' => array(
				's' => __('month', 'tg-text-domain'),
				'p' => __('months', 'tg-text-domain')
			),
			'w' => array(
				's' => __('week', 'tg-text-domain'),
				'p' => __('weeks', 'tg-text-domain'),
			),
			'd' => array(
				's' => __('day', 'tg-text-domain'),
				'p' => __('days', 'tg-text-domain'),
			),
			'h' => array(
				's' => __('hour', 'tg-text-domain'),
				'p' => __('hours', 'tg-text-domain'),
			),
			'i' => array(
				's' => __('minute', 'tg-text-domain'),
				'p' => __('minutes', 'tg-text-domain'),
			),
			's' => array(
				's' => __('second', 'tg-text-domain'),
				'p' => __('seconds', 'tg-text-domain'),
			),
		);

		foreach ($string as $k => &$v) {
			if ($diff[$k]) {
				$v = ($diff[$k] > 1) ? $v['p'] : $v['s'];
				$v = $diff[$k] . ' ' . $v ;
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . __(' ago', 'tg-text-domain') : __('just now', 'tg-text-domain');
		
	}
	
	
	/**
	* Shorten long numbers (K/M/B) 
	* @since 1.0.0
	* @modified 1.4.5
	*/
	public function shorten_number_format($n, $precision = 1) {

		if ($n < 1000) {
			$shorten  = '';
			$n_format = $n;
		} else if ($n >= 1000 && $n <= 999999) {
			$shorten  = 'k';
			$n_format = $n / 1000;
		} else if ($n <= 1000000000) {
			$shorten  = 'M';
			$n_format = $n / 1000000;
		} else {
			$shorten  = 'B';
			$n_format = $n / 1000000000;
		}

		$whole = floor($n_format);
		$float = ($n_format - $whole > 0) ? str_replace('0.','',$n_format - $whole) : '';
		$float = (isset($float[0]) && $float[0] > 0) ? '.'.$float[0] : '';
		$n_format = (int)$n_format.$float.$shorten;
		
		
    	return $n_format;

	}
	
	/**
	* Get Default Grid Skins
	* @since 1.0.0
	*/
	public static function default_skin($style) {
		$default_skin = ($style == 'grid') ? 'brasilia' : 'kampala';
		$item_base = new The_Grid_Item_Skin();
		$get_skins = $item_base->get_skin_names();
		if (!array_key_exists($default_skin,$get_skins)){
			$default_skin = null;
			foreach($get_skins as $skin => $data) {
				if ($data['type'] == $style) {
				 	$default_skin = $data['slug'];
					break;
				}
			}
		}
		return $default_skin;
	}
	
	/**
	* Detect IE browsers
	* @since 1.0.0
	*/
	public static function is_ie() {
		
		if(isset($_SERVER) && !empty($_SERVER) && isset($_SERVER['HTTP_USER_AGENT'])) {
			if ((strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) || preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT'])) {
				return 'is-ie';
			}
		}
	
	}
	
	/**
	* Request remote data
	* @since 1.0.0
	*/
	public static function request_data($url) {

		$response = null;

		if(!empty($url)) {
			// First, we try to use wp_remote_get
			$response = wp_remote_get($url);
			if(is_wp_error($response)) {
		
				// If wp_remote_get failed try file_get_contents
				$response = file_get_contents($url);
				if(false == $response) {
					$response = null;
				}
		
			}
		
			// If response is an array, it's coming from wp_remote_get,
			if(is_array($response)) {
				$response = $response['body'];
			}
		}
	
		return $response;
	
	}
	
	/**
	* Disable W3 Total cache for the grid transient
	* @since 1.0.0
	*/
	public function disable_W3_Total_Cache($grid_transient) {
		
		add_filter( 'pre_set_transient_'.$grid_transient, array($this,'disable_linked_in_cached') );
		add_filter( 'pre_transient_'.$grid_transient, array($this,'disable_linked_in_cached') );
		add_action( 'delete_transient_'.$grid_transient, array($this,'disable_linked_in_cached') );	
		
	}
	
	/**
	* Disable W3 Total cache for the grid transient
	* @since 1.0.0
	*/
	public function disable_linked_in_cached($value=null){
		
		global $_wp_using_ext_object_cache, $w3_total_cache;
		$w3_total_cache = $_wp_using_ext_object_cache;
		$_wp_using_ext_object_cache = false;
		return $value;
		
	}
	
	/**
	* Re-enable W3 Total cache plugin
	* @since 1.0.0
	*/
	public function enable_W3_Total_Cache($grid_transient) {
		
		add_action( 'set_transient_'.$grid_transient, array($this,'disable_linked_in_cached') );
		add_filter( 'transient_'.$grid_transient, array($this,'enable_linked_in_cached') );
		add_action( 'deleted_transient_'.$grid_transient, array($this,'disable_linked_in_cached') );
		
	}
	
	/**
	* Re-enable W3 Total cache plugin
	* @since 1.0.0
	*/
	public function enable_linked_in_cached($value=null){
		
		global $_wp_using_ext_object_cache, $w3_total_cache;
		$_wp_using_ext_object_cache = $w3_total_cache;
		return $value;
		
	}
	
	
	/**
	* Build the grid list for shortcode and export form
	* @since 1.0.7
	*/
	public function get_grid_list($settings = '', $value = '', $multi = false){
		
		$current_page = esc_html(get_admin_page_title());
		
		$WPML = new The_Grid_WPML();
		$WPML_meta_query = ($current_page != 'Import/Export') ? $WPML->WPML_meta_query() : null;
		
		$post_args = array(
			'post_type'      => 'the_grid',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				$WPML_meta_query
			),
			'suppress_filters' => true 
		);
		
		$grids   = get_posts($post_args); 
		$grid_nb = count($grids);
		
		$output = null;
		if(!empty($grids)){
			
			$current_value = $value;
			$ex_form = '<p>'.__('Select the desired grid(s) to export.', 'tg-text-domain').'<br>'.__('The generated file will be a .json file compatibe with the grid importer.', 'tg-text-domain' ).'</p>';
			$sc_form =  '<label class="tg-grid-list-label">'.__("Select a grid from the list", 'tg-text-domain').'</label>';
			
			$output .= '<div class="tg-grid-list-wrapper" data-multi-select="'.$multi.'">';
			$output .= ($current_page != 'Import/Export') ? $sc_form : $ex_form;
			$output .= ($grid_nb > 5) ? '<div class="tg-grid-list-search-holder"><input type="text" class="tg-grid-list-search" placeholder="'.__("Search a grid...", 'tg-text-domain').'" /><i class="tg-grid-list-search-icon dashicons dashicons-search"></i></div>' : null;
			$output .= '<ul class="tg-grid-list-holder">';
			
			foreach($grids as $grid){
				
				$value = $grid->post_title;
				
				if ($current_value !== '' && (string) $value === (string) $current_value ) {
					$selected = ' selected';
				} else {
					$selected = null;
				}
				
				$WPML_flag_data = $WPML->WPML_flag_data($grid->ID);
				$WPML_flag_data = (!empty($WPML_flag_data)) ? '<img src="'.esc_url($WPML_flag_data['url']).'">' : '';
			
				$favorited   = get_post_meta($grid->ID, 'the_grid_favorite', true);
				$grid_post   = (array) get_post_meta($grid->ID, 'the_grid_post_type', true);
				$grid_post   = implode('/', $grid_post);
				$grid_post   = ($grid_post) ? $grid_post : 'post';
				$grid_style  = get_post_meta($grid->ID, 'the_grid_style', true);
				$grid_layout = get_post_meta($grid->ID, 'the_grid_layout', true);
				$grid_name   = get_post_meta($grid->ID, 'the_grid_name', true);
				
				$output .= '<li class="tg-grid-list-item'.$selected.'" data-name="'.esc_attr($grid->post_title).'" data-id="'.esc_attr($grid->ID).'">';
					$output .= '<i class="dashicons tg-dashicons-star-empty '.esc_attr($favorited).'"></i>';
					$output .= (!empty($WPML_flag_data)) ? '<span>'.$WPML_flag_data.'</span>' : null;
					$output .= '<span><b>'.esc_attr($grid->post_title).'</b></span>';
					$output .= '<span>('.esc_attr($grid_post).', ';
					$output .= esc_attr($grid_style).', ';
					$output .= esc_attr($grid_layout).')</span>';
				$output .= '</li>';
				
			}
			
			$output .= '</ul>';
			$output .= '<input name="'. $settings['param_name']. '" type="hidden" class="tg-grid-shortcode-value wpb_vc_param_value wpb-input wpb-text" value="'.$current_value.'"/>';
			$output .= '</div>';
			if ($current_page == 'Import/Export') {
				$output .= '<span class="tg-grid-list-add-all">'.__( 'Select all', 'tg-text-domain').'&nbsp;&nbsp;/&nbsp;&nbsp;</span>';
				$output .= '<span class="tg-grid-list-clear">'.__( 'Clear selection', 'tg-text-domain').'</span>';
				$output .= '<br><br><div class="tg-button" id="tg_post_export"><i class="tg-info-box-icon dashicons dashicons-upload"></i>'. __( 'Export Grid(s)', 'tg-text-domain' ) .'</div>';
				$output .= '<strong class="tg-export-msg"></strong>';
				$output .= '<form method="post" style="display:none"><input type="submit" name="tg_export_grids" value="" /></form>';
			}
				
		} else if ($current_page == 'Import/Export') {

			$output .= '<p>'. __( 'Currently, you don&#39;t have any grid.', 'tg-text-domain'  );
			$output .= '<br>'. __( 'You need to add a grid in order to export it.', 'tg-text-domain'  );
			$output .= '<br>'. __( 'You can create a new grid', 'tg-text-domain'  );
			$output .= ' <a href="'.admin_url( 'post-new.php?post_type=the_grid').'">'. __( 'here.', 'tg-text-domain'  ) .'</a></p>';

		} else {
			
			$output .= '<label class="tg-grid-list-label">'.__( "Currently, you do not have any grid!", "tg-text-domain").'</label>';
			$output .= '<div id="tg-sc-button-holder">';
				$output .= '<a id="tg-sc-button" href="'.admin_url("post-new.php?post_type=the_grid").'">';
					$output .= '<i class="dashicons dashicons-plus"></i>'.__( "Create a Grid", 'tg-text-domain');
				$output .= '</a>';
			$output .= '</div>';
			
		}
		
		return $output;
		
	}

}

/**
* Get template part slug/name
* @since 1.2.0
*/
function tg_get_template_part($slug, $name = null, $load = true) {
	
	// Execute code for this part
	do_action('get_template_part_' . $slug, $slug, $name);
	 
	// Setup possible parts
	$templates = array();
	if (isset($name)) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';
	 
	// Allow template parts to be filtered
	$templates = apply_filters('tg_get_template_part', $templates, $slug, $name);
	// Return the part that is found
	tg_locate_template($templates, $load, false);
	
}
	
/**
* load template part
* @since 1.2.0
*/	
function tg_locate_template($template_names, $load = true, $require_once = true) {
	
	// No file found yet
	$located = false;
	 
	// Try to find a template file
	foreach ((array)$template_names as $template_name) {
	 
		// Continue if template is empty
		if (empty($template_name)) {
			continue;
		}
	 
		// Trim off any slashes from the template name
		$template_name = ltrim($template_name, '/');
	 
		// Check child theme first
		if (file_exists(trailingslashit(get_stylesheet_directory()) . 'the-grid/templates/' . $template_name)) {
			$located = trailingslashit(get_stylesheet_directory()) . 'the-grid/templates/' . $template_name;
			break;
		// Check parent theme next
		} else if (file_exists(trailingslashit( get_template_directory()) . 'the-grid/templates/' . $template_name)) {
			$located = trailingslashit(get_template_directory()) . 'the-grid/templates/' . $template_name;
			break;
		// Check theme compatibility last
		} else if (file_exists(trailingslashit(TG_PLUGIN_PATH) . 'includes/templates/' . $template_name)) {
			$located = trailingslashit(TG_PLUGIN_PATH) . 'includes/templates/' . $template_name;
			break;
		}
	}

	if ((true == $load) && ! empty($located)) {
		load_template($located, $require_once);
	}

}