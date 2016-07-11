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
	* Check any Var if isset & assigned a default value
	* @since 1.0.0
	*/
	public function getVar($arr, $key, $default = ''){
		
		$val = (isset($arr[$key]) && !empty($arr[$key])) ? $arr[$key] : $default;
		return($val);
		
	}
	
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
		
		$users = get_users(array(
			'orderby' => 'display_name',
			'order' => 'DESC',
			'fields' => array('ID', 'user_nicename'),
		));
		
		if ($users) {
			$array = array();
			foreach($users as $user){
				$array[$user->ID] = $user->user_nicename;
			}
			return $array;
		}
		
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
				foreach($taxonomy['cats'] as $category_ID => $category) {
					$parent = (isset($category['parent']) && !empty($category['parent'])) ? ',parent:'.$category['parent'] : null;
					$taxonomies['post_type:'.$post_type.', taxonomy:'.$taxonomy_name.', id:'.$category['id'].$parent] = $category['name'];
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
		
		$args = array(
			'taxonomy'     => $taxonomy,
			'show_count'   => 0,
			'hide_empty'   => 0,
			'order'        => 'ASC',
			'orderby'      =>'name',
			'hierarchical' => 1
		);
		
		$cats = get_categories($args);
		$parent_cats = array();	
		$child_cats  = array();
		
		foreach($cats as $cat){
			$term = array(
				'id'     => $cat->cat_ID,
				'parent' => $cat->category_parent,
				'name'   => $cat->name.' ('.$cat->count.')'
			);
			if ($term['parent']) {
				$child_cats[] = $term;
			} else {
				$parent_cats[] = $term;
			}
		}
		
		$categories = self::format_child_categories($parent_cats, $child_cats);
		return $categories;
		
	}
	
	/**
	* Organize parent child terms order
	* @since 1.5.0
	*/
	public static function format_child_categories($parent_cats, $child_cats, $depth = 1) {
		
		$new_cats = array();
		
		if (isset($parent_cats) && !empty($parent_cats)) {
			
			foreach($parent_cats as $parent_key => $data){
				
				$parent_id = $data['id'];
				$new_cats[] = $data;	
				unset($parent_cats[$parent_key]);
				
				if (isset($child_cats) && !empty($child_cats)) {
					
					foreach($child_cats as $child_key => $child_data){
						
						if ($child_data['parent'] == $parent_id && !empty($child_data['parent'])) {
							$child_data['name'] = str_repeat('&#8212; ',$depth).$child_data['name'];
							$new_cats[] = $child_data;
							unset($child_cats[$child_key]);
							$new_cats = array_merge($new_cats,self::format_child_categories(array($child_data), $child_cats, $depth+1));
						}
						
					}
					
				}
				
			}
			
		}
		
		return $new_cats;
		
	}
	
	/**
	* Format cat name and ID array
	* @since 1.0.0
	*/
	public static function get_formated_categories() {
		
		$cat = array();
		$post_types_and_categories = self::get_post_types_and_categories();	
		
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
		$sorting['none']         = __( 'None', 'tg-text-domain'  );
		$sorting['id']           = __( 'ID', 'tg-text-domain'  );
		$sorting['date']         = __( 'Date', 'tg-text-domain'  );
		$sorting['title']        = __( 'Title', 'tg-text-domain'  );
		$sorting['excerpt']      = __( 'Excerpt', 'tg-text-domain'  );
		$sorting['author']       = __( 'Author', 'tg-text-domain'  );
		$sorting['comment']      = __( 'Number of comment', 'tg-text-domain'  );
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
		
		$col = Array(hexdec(substr($col, 1, 2)), hexdec(substr($col, 3, 2)), hexdec(substr($col, 5, 2)));
		
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
	* Set to bytes 
	* @since 1.5.0
	*/
	public function setting_to_bytes($setting) {
		
		$short = array(
			'k' => 0x400,
			'm' => 0x100000,
			'g' => 0x40000000
		);
	
		$setting = (string)$setting;
		
		if (!($len = strlen($setting))) {
			return null;
		}
		
		$last     = strtolower($setting[$len - 1]);
		$numeric  = 0 + $setting;
		$numeric *= isset($short[$last]) ? $short[$last] : 1;
		
		return $numeric;
		
	}
	
	/**
	* Shorthand css properties (margin, padding, border-width, etc...)
	* @since 1.6.0
	*/
	function shorthand($value){
		
        $values = explode(' ',$value);
		
        switch(count($values)) {
            case 4:
            	if ($values[0] == $values[1] && $values[0] == $values[2] && $values[0] == $values[3]) {
                	return $values[0];
				} else if ($values[1] == $values[3] && $values[0] == $values[2]) {
					return $values[0].' '.$values[1];
				} else if ($values[1] == $values[3]) {
					return $values[0].' '.$values[1].' '.$values[2];
				}
				break;
			case 3:
				if ($values[0] == $values[1] && $values[0] == $values[2]) {
					return $values[0];
				} else if ($values[0] == $values[2]) {
					return $values[0].' '.$values[1];
				}
            	break;
			case 2:
				if($values[0] == $values[1]) {
					return $values[0];
				}
            	break;
        }

        return $value;
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
	* Build the grid list for shortcode and export form
	* @since 1.0.7
	*/
	public function get_grid_shortcode_list($value = ''){

		$output = null;
		$list   = $this->get_grid_list();
		
		if ($list) {
			$output .= '<label class="tg-grid-list-label">'.__("Select a grid from the list", 'tg-text-domain').'</label>';
			$output .= '<div class="tg-list-item-wrapper" data-multi-select="">';
				$output .= '<div class="tg-list-item-search-holder">';
					$output .= '<input type="text" class="tg-list-item-search" placeholder="'.__("Type to Search...", 'tg-text-domain').'" />';
					$output .= '<i class="tg-list-item-search-icon dashicons dashicons-search"></i>';
				$output .= '</div>';
				$output .= '<ul class="tg-list-item-holder">';
				$output .= $list;
				$output .= '</ul>';
				$output .= '<input name="name" type="hidden" class="tg-grid-shortcode-value wpb_vc_param_value wpb-input wpb-text" value="'.$value.'"/>';
			$output .= '</div>';
		} else {
			$output .= '<p>'. __( 'Currently, you don&#39;t have any grid.', 'tg-text-domain'  );
			$output .= '<br>'. __( 'You need to add a grid in order to export it.', 'tg-text-domain'  );
			$output .= '<br>'. __( 'You can create a new grid', 'tg-text-domain'  );
			$output .= ' <a href="'.admin_url( 'post-new.php?post_type=the_grid').'">'. __( 'here.', 'tg-text-domain'  ) .'</a></p>';
		}
		
		return $output;
			
	}
	
	/**
	* Build grid list
	* @since 1.0.7
	*/
	public function get_grid_list(){
		
		$current_page = esc_html(get_admin_page_title());
		
		$WPML = new The_Grid_WPML();
		$WPML_meta_query = ($current_page != 'Import/Export') ? $WPML->WPML_meta_query() : null;
		
		$post_args = array(
			'post_type'      => 'the_grid',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'meta_query' => array(
				'relation' => 'AND',
				$WPML_meta_query
			),
			'suppress_filters' => true,
			'no_found_rows' => true,
			'cache_results' => false
		);
		
		$grids = get_posts($post_args);
		
		$grid_list = null;
		foreach($grids as $grid){
			
			$grid_title = $grid->post_title;
			$grid_id    = $grid->ID;
			$WPML_flag_data = $WPML->WPML_flag_data($grid_id);
			$WPML_flag_data = (!empty($WPML_flag_data)) ? '<img src="'.esc_url($WPML_flag_data['url']).'">' : '';
			
			$grid_list .= '<li class="tg-list-item" data-type="grid" data-name="'.esc_attr($grid_title).'" data-id="'.esc_attr($grid_id).'">';
				$grid_list .= (!empty($WPML_flag_data)) ? '<span>'.$WPML_flag_data.'</span>' : null;
				$grid_list .= '<span><b>'.esc_attr($grid_title).'</b></span>';
			$grid_list .= '</li>';

		}
		
		return $grid_list;
		
	}
	
	/**
	* Build custom skin list
	* @since 1.6.0
	*/
	public function get_skin_list(){
		
		// fetch custom skins
		$custom_skins = (array) The_Grid_Custom_Table::get_skin_params();
		
		$skin_list = null;
		foreach ($custom_skins as $custom_skin) {
			$params = json_decode($custom_skin['params'], true);
			$skin_list .= '<li class="tg-list-item" data-type="skin" data-name="'.esc_attr($params['name']).'" data-id="'.esc_attr($custom_skin['id']).'">';
				$skin_list .= '<span><b>'.esc_attr($params['name']).'</b></span>';
			$skin_list .= '</li>';
		}
		
		return $skin_list;
	
	}
	
	/**
	* Build custom element list
	* @since 1.6.0
	*/
	public function get_element_list(){
		
		// fetch custom skins
		$custom_elements = (array) The_Grid_Custom_Table::get_elements();
		
		$elem_list = null;
		foreach ($custom_elements as $custom_element) {
			$elem_list .= '<li class="tg-list-item" data-type="elem" data-name="'.esc_attr($custom_element['name']).'" data-id="'.esc_attr($custom_element['id']).'">';
				$elem_list .= '<span><b>'.esc_attr($custom_element['name']).'</b></span>';
			$elem_list .= '</li>';
		}
		
		return $elem_list;
	
	}
	
	
	/**
	* Build native/custom element
	* @since 1.6.0
	*/
	public function get_item_element($elements = array(), $is_custom = false, $ajax = false){
		
		if ($elements) {
			
			$generator = new The_Grid_Skin_Generator();
			$element_data = array();
			
			foreach ($elements as $element => $data) {
				
				$json = json_decode($data['settings'], true);
				$json['styles']['is_hover'] = false;
				$json['styles']['idle_state']['top']    = '';
				$json['styles']['idle_state']['bottom'] = '';
				$json['styles']['idle_state']['left']   = '';
				$json['styles']['idle_state']['right']  = '';
				$json['styles']['idle_state']['margin-top']    = '';
				$json['styles']['idle_state']['margin-bottom'] = '';
				$json['styles']['idle_state']['margin-left']   = '';
				$json['styles']['idle_state']['margin-right']  = '';
		
				$overlay    = null;
				$important  = $json['styles']['idle_state']['color-important'];
				$background = $json['styles']['idle_state']['background-color'];
				$color      = $json['styles']['idle_state']['color'];
				
				if ((empty($background) || $background == $color) && $important) {
					$brightness = $this->brightness($color);
					$overlay = ($brightness == 'bright') ? '<div class="tg-element-overlay" style="background:rgba(0,0,0,0.3)"></div>' : null;
				}
				
				$markup = '<div class="tg-element-holder">';
					$markup .= $overlay;
					if ($is_custom) {
						
						$markup .= '<div class="tg-element-draggable tg-element-custom" data-slug="'.$data['slug'].'">'.stripslashes($json['content']).'</div>';
						$markup .= '<div class="tg-custom-element-name">'.$data['name'].'</div>';
						$markup .= '<div class="tg-button tg-custom-element-delete" data-action="tg_delete_element" data-id="'.$data['id'].'">';
							$markup .= '<i class="dashicons dashicons-trash"></i>';
						$markup .= '</div>';
						if (!$ajax) {
							$markup .= '<script type="text/javascript">custom_element[\''.$data['slug'].'\'] = '.stripslashes($data['settings']).';</script>';
						}
						$element_data[$data['slug']]['styles'] = $generator->process_css('tg-element-draggable:not(.tg-element-init)[data-slug="'.$data['slug'].'"]', $json);
						
					} else {
						
						$markup .= '<div class="tg-element-draggable tg-element-custom" data-slug="tgdef-'.$data['slug'].'">'.stripslashes($json['content']).'</div>';
						$markup .= '<div class="tg-custom-element-name">'.$data['name'].'</div>';
						$markup .= '<script type="text/javascript">custom_element[\'tgdef-'.$data['slug'].'\'] = '.stripslashes($data['settings']).';</script>';
						$element_data['tgdef-'.$data['slug']]['styles'] = $generator->process_css('tg-element-draggable:not(.tg-element-init)[data-slug="tgdef-'.$data['slug'].'"]', $json);
						
					}
				$markup .= '</div>';
				
				if ($is_custom) {
					$element_data[$data['slug']]['markup'] = $markup;
				} else {
					$element_data['tgdef-'.$data['slug']]['markup'] = $markup;
				}

				$generator->reset_css();
				
			}
			
			return $element_data;
		
		}
	
	}

}

/**
* Get template part slug/name
* @since 1.2.0
*/
function tg_get_template_part($slug, $name = null, $load = true, $param = null) {
	
	// Execute code for this part
	do_action('get_template_part_' . $slug, $slug, $name, $param);
	 
	// Setup possible parts
	$templates = array();
	if (isset($name)) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';
	 
	// Allow template parts to be filtered
	$templates = apply_filters('tg_get_template_part', $templates, $slug, $name, $param);
	// Return the part that is found
	tg_locate_template($templates, $load, false, $param);
	
}
	
/**
* load template part
* @since 1.2.0
*/	
function tg_locate_template($template_names, $load = true, $require_once = true, $param = null) {
	
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
		
		if ($param) {
			$tg_grid_data = $param;
		}
		
		$tg_grid_data = $param;
		require $located;

	}

}