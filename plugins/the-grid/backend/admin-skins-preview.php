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

class The_Grid_Skins_Preview extends The_Grid {
	
	/**
	* Grid styles
	*
	* @since 1.0.0
	* @access public
	*
	* @var array
	*/
	protected $grid_styles = array('grid', 'masonry');
	
	/**
	* Grid style
	*
	* @since 1.0.0
	* @access public
	*
	* @var string
	*/
	protected $grid_style;
	
	/**
	* Available The Grid Skins
	*
	* @since 1.0.0
	* @access public
	*
	* @var array
	*/
	protected $skins = array();
	
	/**
	* Skins for 
	*
	* @since 1.0.0
	* @access public
	*
	* @var array
	*/
	protected $grid_skins = array();
	
	/**
	* Current grid style setting
	*
	* @since 1.0.0
	* @access public
	*
	* @var string
	*/
	protected $current_style;
	
	/**
	* Custom skins
	*
	* @since 1.0.0
	* @access public
	*
	* @var string
	*/
	protected $custom_skins;
	
	/**
	* The singleton instance
	* @since 1.0.0
	*/
	static private $instance = null;
	
	/**
	* Cloning disabled
	* @since 1.0.0
	*/
	private function __clone() {
	}
	
	/**
	* Serialization disabled
	* @since 1.0.0
	*/
	private function __sleep() {
	}
	
	/**
	* De-serialization disabled
	* @since 1.0.0
	*/
	private function __wakeup() {
	}
	
	/**
	* No initialization allowed
	* @since 1.0.0
	*/
	public function __construct() {
	}
	
	/**
	* to initialize a The_Grid_Skins_Preview object
	* @since 1.0.0
	*/
	static public function getInstance() {
		if(self::$instance == null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	* Render grid skins preview
	* @since 1.0.0
	*/
	public function render_preview($post_ID, $custom_skins) {
		
		global $tg_skins_preview;
		
		// set skin preview mode
		$tg_skins_preview = true;
		
		// set custom skins if exists
		$this->custom_skins = $custom_skins;
		
		// add filter to inject grid skin name and filter areas
		add_filter('tg_after_grid_wrapper_start', array($this, 'grid_filters_area'), 10, 2);
		// add filter to change grid item filter
		add_filter('tg_grid_item_data', array($this, 'add_item_filter'));
		// add filter to name to each skin
		if (is_array($custom_skins) && !empty($custom_skins)) {
			add_filter('tg_after_grid_item_start', array($this, 'add_skin_edit'), 10, 2);
		} else {
			add_filter('tg_after_grid_item_start', array($this, 'add_skin_name'), 10, 2);
		}
		
		// prepare output
		$output = null;
		
		// retrieve current grid style from grid settings
		$this->get_current_style($post_ID);
		
		// get all registered skins 
		$this->get_skins($custom_skins);

		if (!empty($this->skins)) {

			// build a grid for each grid style
			foreach($this->grid_styles as $style) {

				// set current grid style
				$this->grid_style = $style;
				// retrieve skins for current grid style
				$this->get_grid_skins();
				
				if ($this->grid_skins) {
					
					// build grid data according to grid style
					$this->set_grid_data();
					// normalize data to prevent any error
					$this->normalize_data();
					// Retrieve the grid style
					$this->get_styles();
					// Build grid item
					$this->get_items();
					// get the grid layout
					$grid = $this->get_layout();	
					// disabled lightbox
					$grid = str_replace('class="tg-media-button','class="tg-media-button tolb-disabled', $grid);
					// output grids skin
					$output .= $grid;
					
				} else {
					
					$class = ($this->current_style != $this->grid_style) ? ' skin-hidden' : null;
					$output .= '<div class="tg-grid-wrapper tg-error-msg'.$class.'" id="tg-grid-'.$this->grid_style.'-skin">';
						$output .= sprintf( __( 'Sorry, no %s skin was found ', 'tg-text-domain' ), $this->grid_style );
					$output .= '</div>';
					
				}
				
			}
		
		}
		
		// unset skin preview mode
		$tg_skins_preview = false;
		
		return $output;
		
	}
	
	/**
	* Get grid skins
	* @since 1.0.0
	*/
	public function get_grid_skins() {
		
		$this->grid_skins = array_filter($this->skins, array($this, 'filter_skin'));

	}
	
	/**
	* Retrieve/filter skins corresponding to a grid style
	* @since 1.0.0
	*/
	public function filter_skin($var) {
		return ($var['type'] == $this->grid_style);
	}
	
	/**
	* Get current grid style
	* @since 1.0.0
	*/
	public function get_current_style($post_ID) {
		
		$this->current_style = get_post_meta($post_ID, 'the_grid_style', true);
		$this->current_style = (empty($this->current_style) || $this->current_style == 'justified') ? 'grid' : $this->current_style;

	}
	
	/**
	* Get skins types
	* @since 1.0.0
	*/
	public function get_skins($custom_skins) {
			
		if (is_array($custom_skins) && !empty($custom_skins)) {
			
			$this->skins = $custom_skins;
			
		} else {
			
			$item_base = new The_Grid_Item_Skin();
			$this->skins = $item_base->get_skin_names();
			$this->custom_skins_settings();

		}
		
		
	}
	
	/**
	* Set custom skins settings
	* @since 1.0.0
	*/
	public function custom_skins_settings() {
		
		$custom_skin_settings = get_option('tg_custom_skins_settings');
		
		if (is_array($custom_skin_settings) && !empty($custom_skin_settings)) {
			
			$this->skins = array_replace_recursive($this->skins, $custom_skin_settings);
			
		}
		
	}

	/**
	* Retrieve grid data
	* @since 1.0.0
	*/
	public function set_grid_data() {
		
		$skins = array();
		
		foreach($this->grid_skins as $skin => $data) {
			if ($data['type'] == $this->grid_style) {
				$skins[] = $data['slug'];
			}
		}
		
		if ($skins) {

			$this->grid_data = array(
				'ID'                      => 'tg-grid-'.$this->grid_style.'-skin',
				'name'                    => 'tg_grid_skins_preview',
				'css_class'               => ($this->current_style != $this->grid_style) ? ' skin-hidden' : null,
				'source_type'             => 'post_type',
				'post_type'               => array('post'),
				'style'                   => $this->grid_style,
				'items_format'            => array('video'),
				'item_ratio_x'            => 1,
				'item_ratio_y'            => 1,
				'default_image'           => TG_PLUGIN_URL . 'backend/assets/images/skin-placeholder.jpg',
				'skin_content_background' => '#ffffff',
				'skin_overlay_background' => 'rgba(52, 73, 94, 0.75)',
				'skin_content_color'      => 'dark',
				'skin_overlay_color'      => 'light',
				'navigation_style'        => 'tg-nav-bg',
				'navigation_color'        => (!$this->custom_skins) ? '#444444' : '#ffffff',
				'navigation_accent_color' => '#ffffff',
				'navigation_bg'           => (!$this->custom_skins) ? '#ffffff' : '#2c3e50',
				'navigation_accent_bg'    => '#4ECDC4',
				'lightbox_type'           => 'the_grid',
				'skins'                   => '{"post":"'.$skins[0].'"}',
				'desktop_large'           => 4,
				'desktop_medium'          => 3,
				'desktop_small'           => 2,
				'tablet'                  => 1,
				'tablet_small'            => 1,
				'mobile'                  => 1,
				'gutter'                  => 28,
				'desktop_medium_gutter'   => 28,
				'desktop_small_gutter'    => 28,
				'tablet_gutter'           => 28,
				'tablet_small_gutter'     => 28,
				'mobile_gutter'           => 28,
				'desktop_medium_width'    => 1480,
				'desktop_small_width'     => 1200,
				'tablet_width'            => 768,
				'tablet_small_width'      => 480,
				'mobile_width'            => 320,
				'layout'                  => (!$this->custom_skins) ? 'horizontal' : 'vertical',
				'row_nb'                  => ($this->grid_style == 'grid') ? 2 : 1,
				'slider_itemNav'          => 'basic',
				'area_bottom2'            => '{"styles":"","functions":["the_grid_get_slider_bullets"]}',
				'transition'              => 600,
				'item_skins'              => $skins,
				'video_lightbox'          => true
			);
		
		}
		
	}
	
	/**
	* Normalize grid data
	* @since 1.0.0
	*/
	public function normalize_data() {
		
		try {
			
			// get grid data
			$data_class = new The_Grid_Data('tg-grid-'.$this->grid_style.'-skin');
			$this->grid_data = $data_class->normalize_data($this->grid_data);
			
		} catch (Exception $e) {
			
			// show error message if throw
			throw new Exception($e->getMessage());
			
		}
		
	}
	
	/**
	* Retrieve grid items
	* @since 1.0.0
	*/
	public function get_items() {
		
		$this->grid_items = array();

		// sort the skins by alphabetical asc order
		usort($this->grid_skins, function ($a,$b){ return strcmp($a['slug'], $b['slug']); });

		foreach($this->grid_skins as $skin) {

			$this->grid_items[] = array(
				'ID'              => 'fake',
				'date'            => current_time('timestamp'),
				'post_type'       => 'post',
				'sticky'          => null,
				'format'          => (!in_array($skin['slug'],array('vaduz','victoria','podgorica'))) ? 'image' : 'video',
				'url'             => 'javascript:;',
				'title'           => 'The post title',
				'excerpt'         => 'Actique exilium principis is in nullos Constantio et absolutum quorum movebantur ita intendebantur ubi gladii sub coopertos facile uncosque in poenales coopertos ubi eculei quemquam sceleste ex alii Constantio parabat principis Paulus exilium deiectos movebantur intend.',
				'terms'           => array(
					array(
						'ID'       => '0',
						'slug'     => 'category',
						'name'     => 'Category',
						'taxonomy' => 'category',
						'url'      => 'javascript:;',
						'color'    => null
					)
				),
				'author'          => array(
					'name'   => 'Themeone',
					'url'    => 'javascript:;',
					'avatar' => TG_PLUGIN_URL . 'backend/assets/images/avatar.png'
				),
				'likes_number'    => '1',
				'comments_number' => '1',
				'views_number'    => '12500',
				'image'           => array(
					'url'    => TG_PLUGIN_URL . 'backend/assets/images/skin-placeholder.jpg',
					'lb_url' => TG_PLUGIN_URL . 'backend/assets/images/skin-placeholder.jpg',
					'width'  => 800,
					'height' => 460,
					'alt'    => ''
				),
				'gallery'         => null,
				'video'           => array(
					'duration' => '06:25',
					'type'   => 'youtube',
						'source' => array(
							'ID'  => 'about:blank',
						),
				),
				'audio'           => null,
				'quote'           => null,
				'link'            => null,
				'meta_data'       => array(
					'the_grid_item_skin_id' => $skin['id'],
					'the_grid_item_filter'  => $skin['filter'],
					'the_grid_item_skin'    => $skin['slug'],
					'the_grid_item_name'    => $skin['name'],
					'the_grid_item_col'     => $skin['col'],
					'the_grid_item_row'     => $skin['row']
				),
				'product' => array(
					'price'         => '<span class="amount">179$</span>',
					'full_price'    => '<del><span class="amount">$179</span></del> <ins><span class="amount">$99</span></ins>',
					'regular_price' => '<span class="amount">179$</span>',
					'sale_price'    => '<span class="amount">99$</span>',
					'rating'        => '<div class="star-rating"><span style="width:90%"></span></div>',
					'text_rating'   => __( '4.5 out of 5', 'tg-text-domain' ),
					'on_sale'       => '<span class="onsale">'.__( 'Sale!', 'tg-text-domain' ).'</span>',
					'cart_button'   => '<a href="" rel="nofollow" data-product_id="-1" data-product_sku="" data-quantity="0" class="button add_to_cart_button product_type_simple">'.__( 'Add to cart', 'tg-text-domain' ).'</a>',
					'wishlist'    => ''
				)
			);
		}	

	}

	/**
	* Retrieve grid styles
	* @since 1.0.0
	*/
	public function get_styles() {
		
		try {
			
			// get grid styles
			$styles_class = new The_Grid_Styles($this->grid_data);
			$this->grid_data = $styles_class->styles_processing();
			
		} catch (Exception $e) {
			
			// show error message if throw
			throw new Exception($e->getMessage());
			
		}
		
	}
	
	/**
	* Retrieve grid layout
	* @since 1.0.0
	*/
	public function get_layout() {
		
		try {
			
			// retrive entire grid layout
			$layout_class = new The_Grid_Layout($this->grid_data, $this->grid_items);
			return $layout_class->output();
		
		} catch (Exception $e) {
			
			// show error message if throw
			throw new Exception($e->getMessage());
			
		}
	
	}
	
	/**
	* Grid skin header and filters
	* @since 1.0.0
	*/
	public function grid_filters_area($html, $args) {
		
		$grid_filters = array();
		
		// if screen id is skin builder
		if (!$this->custom_skins) {
			
			// add skin preview title
			$title   = ($this->grid_style == 'grid') ? ' / Justified' : null;
			echo '<div class="tg-grid-skin-type">'.esc_attr(ucfirst($this->grid_style).$title).' '.__( 'Skins', 'tg-text-domain' ).'</div>';
		
		
			// filter for selected skin
			$grid_filters[] = array(
				'taxo' => 'skin_filter',
				'id'   => '.selected',
				'name' => __( 'Selected Skin', 'tg-text-domain' )
			);
		
		}
		
		// retrieve all filter for current grid style
		foreach($this->skins as $skin => $data) {
			if ($data['type'] == $this->grid_style) {
				$item_filter = esc_attr($data['filter']);
				if(!in_array($data['filter'], $grid_filters, true) && $data['filter']){
					$grid_filters[$data['filter']] = array(
						'taxo' => 'skin_filter',
						'id'   => '.'.sanitize_key($data['filter']),
						'name' => ucwords($data['filter'])
					);
				}
			}
		}

		// setup arguments for filter buttons template
		$args = array(
			'filter_all_text'       => __( 'All', 'tg-text-domain' ),
			'filter_count'          => 'inline',
			'filters'               => $grid_filters,
			'filter_dropdown_title' => '',
			'active_filters'        => array()
		);
		
		if (count($grid_filters) > 0) {
			// generate filters from template
			tg_get_template_part('filter', 'buttons', true, $args);
		}

	}
		
	/**
	* Add Grid item filter
	* @since 1.0.0
	*/
	public function add_item_filter($data) {

		$filter = $data['meta_data']['the_grid_item_filter'];
		$data['class'] = (isset($data['class'])) ? str_replace(' f0', '', $data['class']).' '.sanitize_key($filter) : null;
		return $data;
	
	}
	
	/**
	* Add Grid item name/slug
	* @since 1.0.0
	*/
	public function add_skin_name($output, $args) {

		$slug = $args['meta_data']['the_grid_item_skin'];
		$name = $args['meta_data']['the_grid_item_name'];
		$name = (!$name) ? $slug : $name;

		$output = '<div class="tg-item-skin-name" data-slug="'.esc_attr($slug).'">';
			$output .= '<i class="dashicons dashicons-yes"></i>';
			$output .= '<span>'.esc_html(ucfirst($name)).'</span>';
			$output .= '<span class="tg-select-skin">'.__( 'Select this skin', 'tg-text-domain' ).'</span>';
		$output .= '</div>';
		
		return $output;
		
	}
	
	/**
	* Add edit button for custom skins
	* @since 1.0.0
	*/
	public function add_skin_edit($output, $args) {
		
		$id   = $args['meta_data']['the_grid_item_skin_id'];
		$slug = $args['meta_data']['the_grid_item_skin'];
		$name = $args['meta_data']['the_grid_item_name'];
		$name = (!$name) ? $slug : $name;

		$output = '<div class="tg-item-custom-skin-name" data-id="'.esc_attr($id).'">';
			$output .= '<span>'.esc_html(ucfirst($name)).'</span>';
			$output .= '<div class="tg-button" id="tg-delete-skin" data-action="tg_delete_skin" data-id="'.esc_attr($id).'"><i class="dashicons dashicons-trash"></i></div>';		
			$output .= '<div class="tg-button" id="tg-clone-skin" data-action="tg_clone_skin" data-id="'.esc_attr($id).'"><i class="dashicons dashicons-images-alt2"></i></div>';
			$output .= '<a class="tg-button" id="tg-edit-skin" href="'.admin_url( 'admin.php?page=the_grid_skin_builder&id='.esc_attr($id)).'"><i class="dashicons dashicons-admin-tools"></i></a>';
		$output .= '</div>';
		
		return $output;
		
	}
	
}

if(!function_exists('The_Grid_Skins_Preview')) {
	
	/**
	* Tiny wrapper function
	* @since 1.0.0
	*/
	function The_Grid_Skins_Preview($post_ID = '', $custom_skins = '') {
		
		try {
			
			// render skins preview
			$skins_preview = The_Grid_Skins_Preview::getInstance();
			return $skins_preview->render_preview($post_ID, $custom_skins);
			
		} catch (Exception $e) {
			
			// show error message if throw
			throw new Exception($e->getMessage());
			
		}

	}
	
}