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

class The_Grid_Item extends The_Grid {
	
	/**
	* @vars string/array
	* @since 1.0.0
	*/
	protected $grid_data;
	protected $item_count;
	protected $skin_slug;
	protected $skin_php;
	
	/**
	* The Grid Item  Constructor
	* @since 1.0.0
	*/
	public function __construct() {
		
		// set global data
		global $tg_grid_data, $tg_item_count;
		$this->grid_data  = $tg_grid_data;
		$this->item_count = ($tg_item_count) ? $tg_item_count : 1;
		
		// run the loop to retrieve items
		$this->the_grid_loop();
		
	}
	
	/**
	* Custom Loop through items in The Grid
	* @since 1.0.0
	*/
	public function the_grid_loop() {
		
		// switch from different source type (post_type/social media)
		if ($this->grid_data['source_type'] == 'post_type') {
			$this->post_type_loop();
		} else {
			$this->social_media_loop();
		}
	
	}
	
	/**
	* Post type loop
	* @since 1.0.0
	*/
	public function post_type_loop() {

		global $tg_grid_query;

		// start loop with custom query
		while ($tg_grid_query->have_posts()) : $tg_grid_query->the_post();
			$this->item_loop();				
		endwhile;
		
		// save last item number for later ajax call
		$this->save_count_item();	
	
	}
	
	/**
	* Instagram loop
	* @since 1.0.0
	*/
	public function social_media_loop() {
		
		global $tg_social_items;

		// start loop with instagram data
		foreach ($tg_social_items as $item) {
			global $tg_social_item;
			$tg_social_item = $item;
			$this->item_loop();			
		}
		
		// save last item number for later ajax call
		$this->save_count_item();	
	
	}
	
	/**
	* Item loop
	* @since 1.0.0
	*/
	public function item_loop() {
		
		$this->item_comment_number();
		$this->item_output();
		$this->update_count_item();	
	
	}
	
	/**
	* Display comment number item in the loop
	* @since 1.0.0
	*/
	public function item_comment_number() {
		$comment = '<!-- The Grid item #'.$this->item_count.' -->';
		echo $comment;
	}
	
	/**
	* Output item in the grid
	* @since 1.0.0
	*/
	public function item_output() {
		
		// prepare data for current item
		$this->item_skin();
		$this->item_classes();
		$this->item_attributes();
		$this->item_settings();
		
		// build item markup
		tg_get_template_part('item','start');
		echo $this->skin_php;
		tg_get_template_part('item','end');

	}
	
	/**
	* Count number of item in the loop
	* @since 1.0.0
	*/
	public function update_count_item() {
		$this->item_count++;
	}
	
	/**
	* Save number of item after the loop
	* @since 1.0.0
	*/
	public function save_count_item() {
		global $tg_item_count;
		$tg_item_count = $this->item_count;
	}

	/**
	* Get item skin
	* @since 1.0.0
	*/
	public function item_skin() {
		
		// current source type (post, instagram,...)
		$source_type = $this->grid_data['source_type'];
		
		// current grid style
		$grid_style  = ($this->grid_data['style'] === 'justified') ? 'grid' : $this->grid_data['style'];
		
		// skin(s) set for the current grid
		$item_skins = $this->grid_data['skins'];
		$item_skins = json_decode($item_skins, TRUE);
		
		// social skin
		$social_skin = $this->grid_data['social_skin'];
		
		// Get all registered skin names
		$skin_base = new The_Grid_Item_Skin();
		$get_skins = $skin_base->get_skin_names();
		
		// check if current post have a skin set in metadata
		if ($source_type == 'post_type') {
		
			// current post type info
			$post_ID   = get_the_ID();
			$post_type = get_post_type();
			// current post skin
			$item_skin = get_post_meta($post_ID, 'the_grid_item_skin', true);
			// if current post have a skin set and if it exists in registered skins
			if (!empty($item_skin) && isset($get_skins[$item_skin]) && $get_skins[$item_skin]['type'] == $grid_style) {
				// then reassign right skin for current post
				$item_skins[$post_type] = $item_skin;
			}
		
		} else {
			// recreate items skin for social media only
			$post_type = 'social';
			$item_skins = array();
			$item_skins[$post_type] = $social_skin;
		}

		// if current skin do not exist then assign default skin available from registered skins
		if (!isset($item_skins[$post_type]) || !array_key_exists($item_skins[$post_type],$get_skins)) {
			$base = new The_Grid_Base();
			$skin = $base->default_skin($grid_style);
			if (!$skin) {
				return false;
			}
		} else {
			$skin  = $item_skins[$post_type];
		}

		// get slug & content
		$this->skin_slug = $get_skins[$skin]['slug'];
		$this->skin_php  = include($get_skins[$skin]['php']);	
		
	}
	
	/**
	* Build item classes
	* @since 1.0.0
	*/
	public function item_classes() {
		
		// retrieve social item data
		global $tg_social_item;
		
		// current source type (post, instagram,...)
		$source_type = $this->grid_data['source_type'];
		
		$preloader  = $this->grid_data['preloader'];
		
		// set post number class
		$post_ID     = ($source_type == 'post_type') ? get_the_ID() : ((strstr($tg_social_item['id'], '_', true)) ? strstr($tg_social_item['id'], '_', true) : $tg_social_item['id']);
		$post_class  = ' tg-post-'.$post_ID;
		$post_sticky = ($source_type == 'post_type' && is_sticky($post_ID)) ? ' sticky' : null;
		$skin_slug   = ' '.$this->skin_slug;
		$preloader   = ($preloader) ? ' tg-item-reveal' : '';
		// retrieve item terms
		$terms = ($source_type == 'post_type') ? $this->item_terms() : null;
		
		// set global for item template
		global $tg_grid_data;
		$tg_grid_data['item_classes'] = $post_class.$post_sticky.$skin_slug.$terms.$preloader;

	}
	
	/**
	* Build item data attributes
	* @since 1.0.0
	*/
	public function item_attributes() {
		
		// get data attr for sorter
		$meta_data = $this->item_sort_attribute();
		// get data attr col/row item size
		$size_data = $this->item_size();
		
		// set global for item template
		global $tg_grid_data;
		$tg_grid_data['item_attributes'] = $meta_data.$size_data;
		
	}
	
	/**
	* Item setting buttons for backend
	* @since 1.0.0
	*/
	public function item_settings() {
		
		global $tg_grid_preview;
		
		$output = null;

		// if we are in preview mode and it's post types add setting buttons
		if ($this->grid_data['source_type'] == 'post_type' && $tg_grid_preview) {
		
			$post_ID = get_the_ID();
			
			// retrieve WPML query lang to retrieve right metadat info
			$WPML = new The_Grid_WPML();
			
			// build markup for item setting and hide item buttons
			$output  = '<div class="tg-item-hidden-overlay"></div>';
			$output .= '<div class="tg-item-settings" data-id="'.esc_attr($post_ID).'" data-action="'.admin_url( 'post.php?post='.esc_attr($post_ID).'&action=edit'.$WPML->WPML_post_query_lang($post_ID)).'">';
				$output .= '<span>'.__( 'Loading', 'tg-text-domain' ).'</span>';
			$output .= '</div>';
			$output .= '<div class="tg-item-exclude" data-id="'.esc_attr($post_ID).'">';
				$output .= '<span class="tg-item-hide">'.__( 'Hide item', 'tg-text-domain' ).'</span>';
				$output .= '<span class="tg-item-show">'.__( 'Show item', 'tg-text-domain' ).'</span>';
			$output .= '</div>';

		}
		
		// set global for item template
		global $tg_grid_data;
		$tg_grid_data['item_settings'] = $output;
		
	}
	
	/**
	* Get item terms
	* @since 1.0.0
	*/
	public function item_terms() {
		
		$post_ID = get_the_ID();

		// retrieve all taxonomies for current post type
		$taxonomies = get_object_taxonomies(get_post_type());
		
		$categories = null;
		// loop throught each tax
		foreach( $taxonomies as $taxonomy ) {
			$terms = get_the_terms($post_ID, $taxonomy);
			if ($terms && !is_wp_error($terms)) {
				foreach( $terms as $term ) {
					$categories .= ' f'.$term->term_id;
				}
			}
		}
		
		return $categories;
		
	}
	
	/**
	* Get item size
	* @since 1.0.0
	*/
	public function item_size() {
		
		// main vars to retrieve item sizes
		$post_ID     = get_the_ID();
		$source_type = $this->grid_data['source_type'];
		$grid_style  = $this->grid_data['style'];
		$force_size  = $this->grid_data['item_force_size'];
		
		// if each in item have same size forced
		if ($force_size && $grid_style != 'justified') {
			
			$item_col  = $this->grid_data['items_col'];
			$item_row  = $this->grid_data['items_row'];

		// check if each item have a custom size
		} else if ($source_type == 'post_type' && !$force_size && $grid_style != 'justified') {
		
			// get meta
			$item_col = get_post_meta($post_ID, 'the_grid_item_col', true);
			$item_row = get_post_meta($post_ID, 'the_grid_item_row', true);
			// set col/row number
			$item_col = (!empty($item_col)) ? $item_col : 1;
			$item_row = (!empty($item_row)) ? $item_row : 1;
			
		} else {
			
			// assign default data
			$item_col  = 1;
			$item_row  = 1;
			
		}
		
		$data_row  = ' data-row="'.esc_attr($item_row).'"';
		$data_col  = ' data-col="'.esc_attr($item_col).'"';
		$data_size = $data_row.$data_col;
			
		return $data_size;
		
	}
	
	/**
	* Add data for sort/filter/search field
	* @since 1.0.0
	*/
	public function item_sort_attribute() {
		
		$post_ID = get_the_ID();
		$source  = $this->grid_data['source_type'];
		$sort_by = $this->grid_data['sort_by'];
		
		// remove "none" and "excerpt" from data attribute (because already present in items)
		if(($key = array_search('excerpt', $sort_by)) !== false) { unset($sort_by[$key]); }
		if(($key = array_search('none', $sort_by)) !== false) { unset($sort_by[$key]); }
		$product = (class_exists('WooCommerce')) ? get_product($post_ID) : null;
		
		// retrieve each data set in sort dropdown list
		if (isset($sort_by) && !empty($sort_by) && $source == 'post_type') {
			foreach ($sort_by as $sort) {
				switch($sort){
					case 'id':
						global $tg_social_item;
						$post_ID = (isset($tg_social_item['id'])) ? $tg_social_item['id'] : $post_ID;
						$data_attr[$sort] = $post_ID;
						break;
					case 'title':
						$data_attr[$sort] = substr(get_the_title(), 0, 12);
						break;
					case 'author':
						$data_attr[$sort] = get_the_author();
						break;
					case 'date':
						$data_attr[$sort] = get_the_date('Ymd');
						break;
					case 'comment':
						$comment_nb = wp_count_comments($post_ID)->approved;
						$data_attr[$sort] = (!empty($comment_nb)) ? $comment_nb : 0;
						break;
					case 'popular_post':
						$like_nb = get_post_meta($post_ID, '_post_like_count', true);
						$data_attr['popular-post'] = (!empty($like_nb)) ? $like_nb : 0;
						break;
					case 'woo_total_sales':
						$total_sales = get_post_meta($post_ID, 'meta_num_total_sales', true);
						$data_attr['total-sales'] = (!empty($total_sales)) ? $total_sales : 0;
						break;
					case 'woo_regular_price':
						$regular_price = $product->get_price();
						$data_attr['regular-price'] = (!empty($regular_price)) ? $regular_price : 0;
						break;
					case 'woo_sale_price':
						$sale_price = $product->get_sale_price();
						$data_attr['sale-price'] = (!empty($sale_price)) ? $sale_price : 0;
						break;
					case 'woo_featured':
						$data_attr[str_replace('woo_','', $sort)] = ($product->is_featured()) ? '1' : '0';
						break;
					case 'woo_SKU':
						$data_attr[str_replace('woo_','', $sort)] = $product->get_sku();
						break;
					case 'woo_stock':
						$stock_quantity = $product->get_stock_quantity();
						$data_attr[str_replace('woo_','', $sort)] = (!empty($stock_quantity)) ? $stock_quantity : 0;
						break;
					default:
						$meta_data = get_post_meta($post_ID, $sort, true);
						$name = str_replace('_','-',$sort);
						$name = strtolower($name[0] == '-') ? substr($name, 1) : $name;
						$data_attr[esc_attr($name)] = (!empty($meta_data)) ? $meta_data : 0;
						break;
				}
			}
		}
		
		if (isset($sort_by) && !empty($sort_by) && $source != 'post_type') {
			
			global $tg_social_item;
			
			foreach ($sort_by as $sort) {
				switch($sort){
					case 'id':
						$data_attr[$sort] = (strstr($tg_social_item['id'], '_', true)) ? strstr($tg_social_item['id'], '_', true) : $tg_social_item['id'];
						break;
					case 'title':
						$data_attr[$sort] = substr($tg_social_item['title'], 0, 12);
						break;
					case 'author':
						$data_attr[$sort] = $tg_social_item['username'];
						break;
					case 'date':
						$date = str_replace('@', '', $tg_social_item['date']);
						$data_attr[$sort] = $date;
						break;
					case 'comment':
						$data_attr[$sort] = $tg_social_item['comments'];
						break;
					case 'popular_post':
						$data_attr['popular-post'] = $tg_social_item['likes'];
						break;
				}
			}
		}
		
		// automatically add attributes from sorters
		$attr = null;
		if (isset($data_attr) && !empty($data_attr)) {
			foreach ($data_attr as $key => $val ) {
				$attr .= ' data-'.esc_attr($key).'="'.esc_attr($val).'"';
			}
		}
		
		return $attr;
	}
}