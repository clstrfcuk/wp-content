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

class The_Grid_Skins_Preview {
	
	/**
	* declare main protected var
	* @since 1.0.0
	*/
	protected $post_ID;
	
	protected $skins = array();
	protected $skins_types = array();

	protected $skin_php;
	protected $skin_name;
	protected $skin_slug;
	protected $skin_type;
	protected $skin_filter;
	protected $skin_col;
	protected $skin_row;
	
	protected $grid_filters;
	
	/**
	* The singleton instance
	* @since 1.0.0
	*/
	static private $instance = null;
	
	/**
	* No initialization allowed
	* @since 1.0.0
	*/
	private function __construct() {}
	
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
	* Process skins preview
	* @since 1.0.0
	*/
	public function process($post_ID) {
		
		$skins = null;
		
		$this->post_ID = $post_ID;
		
		$this->set_preview_mode();
		$this->set_virtual_post();
		$this->get_skins();
		if (!empty($this->skins)) {
			$this->get_skins_types();
			$skins = $this->retrieve_skins();
		}
		$this->unset_preview_mode();
		$this->reset_virtual_post();
		
		echo $skins;	
		
	}
	
	/**
	* Set in Global we are in preview mode
	* @since 1.0.0
	*/
	public function set_preview_mode() {
		
		global $tg_skins_preview;
		$tg_skins_preview = true;
		
	}
	
	/**
	* Unset in Global preview mode
	* @since 1.0.0
	*/
	public function unset_preview_mode() {
		
		global $tg_skins_preview;
		$tg_skins_preview = false;
		
	}
	
	/**
	* Set virtual post for the custom loop
	* @since 1.0.0
	*/
	public function set_virtual_post() {
		
		$virtual_post = new The_Grid_Skin_Post();
		$virtual_post->virtual_post();
		
	}
	
	/**
	* Reset virtual post for the custom loop
	* @since 1.0.0
	*/
	public function reset_virtual_post() {
		
		$virtual_post = new The_Grid_Skin_Post();
		$virtual_post->reset_virtual_post();
		
	}
	
	/**
	* Set global grid data
	* @since 1.0.0
	*/
	public function set_global_grid_data() {
		
		global $tg_grid_data;
				
		$tg_grid_data = array(
			'source_type' => 'post_type',
			'style' => $this->skin_type,
			'item_ratio_x' => 1,
			'item_ratio_y' => 1,
			'default_image' => TG_PLUGIN_URL . 'backend/assets/images/skin-placeholder.jpg',
			'skin_content_background' => '#ffffff',
			'skin_overlay_background' => 'rgba(52, 73, 94, 0.75)',
			'skin_content_color' => 'dark',
			'skin_overlay_color' => 'light'
		);
		
	}
	
	/**
	* Retrieve skin names array
	* @since 1.0.0
	*/
	public function get_skins() {
		
		$item_base  = new The_Grid_Item_Skin();
		$this->skins = $item_base->get_skin_names();
		// arrange skins name by alphabetical order
		uasort($this->skins,  function ($a,$b){ return strcmp($a['name'], $b['name']); });
		
	}
	
	/**
	* Get skins types
	* @since 1.0.0
	*/
	public function get_skins_types() {
		
		$skins_types = array();
		foreach($this->skins as $skin => $data) {
			if (!in_array($data['type'], $skins_types)) {
				array_push($skins_types, $data['type']);
			}
		}
		
		$this->skins_types = $skins_types;
		
	}
	
	/**
	* Get skins css
	* @since 1.0.0
	*/
	public function get_skins_css() {
		
		$css    = null;
		$colors = $this->set_skin_colors();

		foreach($this->skins as $item_skin => $skin) {
			ob_start();
			include $skin['css'];
			$file_content  = ob_get_contents();
			ob_end_clean();
			$css .= $file_content;
		}
		
		// compress css
		$base = new The_Grid_Base();
		$css  = $base->compress_css($css.$colors);
		
		return '<style id="tg-grid-skin-css">'.$css.'</style>';
			
	}
	
	/**
	* Set skin css colors
	* @since 1.0.0
	*/
	public function set_skin_colors() {
		
		// declare may tags for color scheme
		$schemes    = array('dark','light');
		$title_tags = array('h2','h2 a','h3','h3 a','a','a.tg-link-url','i');
		$para_tags  = array('p');
		$span_tags  = array('span','.no-liked .to-heart-icon path','.empty-heart .to-heart-icon path');
		$tags       = array('title' => $title_tags, 'text'  => $para_tags, 'span'  => $span_tags);
		
		// default colors from Global settings
		$default = array(
			'dark_title'  => '#444444',
			'dark_text'   => '#777777',
			'dark_span'   => '#999999',
			'light_title' => '#ffffff',
			'light_text'  => '#f5f5f5',
			'light_span'  => '#f6f6f6',
		);
		
		// loop throught each tag and apply right css 
		$colors = null;
		foreach ($schemes as $scheme) {
			foreach ($tags as $tag => $classes) {
				$classes   = implode(',.tg-item .'.$scheme.' ', $classes);
				$def_color = $default[$scheme.'_'.$tag];
				$color     = get_option('the_grid_'.$scheme.'_'.$tag, $def_color);
				$colors   .= '#tg-grid-skins .tg-item .'.$scheme.' '.$classes.'{color:'.$color.';fill:'.$color.';stroke:'.$color.'}';
			}
		}
		
		// for bg white color for skin preview only
		$colors .= '#tg-grid-skins .tg-item .tg-item-content-holder {background-color: #ffffff;}
				    #tg-grid-skins .tg-item .tg-item-overlay {background-color: rgba(52, 73, 94, 0.75);}';
				   
		return $colors;
		
	}
	
	/**
	* Get grid filters
	* @since 1.0.0
	*/
	public function get_grid_filters() {
		
		$grid_filters = array();
		foreach($this->skins as $skin => $data) {
			if ($data['type'] == $this->skin_type) {
				$item_filter = esc_attr($data['filter']);
				if(!in_array($data['filter'], $grid_filters, true)){
					array_push($grid_filters, $data['filter']);
				}
			}
		}
		
		return $grid_filters;
		
	}
	
	/**
	* Build Skin selector
	* @since 1.0.0
	*/
	public function retrieve_skins() {
		
		$markup = $this->get_skins_css();
		
		foreach($this->skins_types as $type) {
			
			$this->skin_type = $type;
			
			$this->set_global_grid_data();
			
			$markup .= $this->grid_wrapper_start();
			
			foreach($this->skins as $skin => $data) {
				
				if ($data['type'] == $type) {
				
					$this->skin_name = $data['name'];
					$this->skin_slug = $data['slug'];
					$this->skin_php  = $data['php'];
					$this->skin_col  = $data['col'];
					$this->skin_row  = $data['row'];
					$this->skin_filter = $data['filter'];
					
					$markup .= $this->build_skin_item();
					
				}
			}
			
			$markup .= $this->grid_wrapper_end();
			
		}
		
		return $markup;

	}
	
	/**
	* Grid skin wrapper start
	* @since 1.0.0
	*/
	public function grid_wrapper_start() {
		
		$data_attr = $this->grid_skin_attribute();
		
    	$id = $this->post_ID;
		
		$style = get_post_meta($id, 'the_grid_style', true);
		$style = (empty($style) || $style == 'justified') ? 'grid' : $style;
		$class = ($style != $this->skin_type) ? ' skin-hidden' : null;
		
		$wrapper_start  = '<div class="tomb-clearfix"></div>';
		$wrapper_start .= '<div class="tg-grid-wrapper'.$class.'" id="tg-grid-'.$this->skin_type.'-skin">';
			$justified = ($this->skin_type == 'grid') ? ' / Justified' : null;
			$wrapper_start .= '<div class="tg-grid-skin-type">'.esc_attr(ucfirst($this->skin_type).$justified).' '.__( 'Skins', 'tg-text-domain' ).'</div>';
			$wrapper_start .= '<div class="tg-grid-sizer"></div>';
			$wrapper_start .= '<div class="tg-gutter-sizer"></div>';
			$wrapper_start .= $this->grid_filters_area();
			$wrapper_start .= '<div class="tg-grid-slider">';
				$wrapper_start .= '<div class="tg-grid-holder tg-layout-'.esc_attr($this->skin_type).'" '.$data_attr.'>';
		
		return $wrapper_start;
		
	}
	
	/**
	* Retrieve all skins
	* @since 1.0.0
	*/
	public function grid_skin_attribute() {
		
		$type  = $this->skin_type;
		$rowNb = ($type == 'grid') ? 2 : 1;
		
		$data_attr  = ' data-name="tg-grid-'.$type.'-demo"';
		$data_attr .= ' data-style="'.$type.'"';
		$data_attr .= ' data-row="'.$rowNb.'"';
		$data_attr .= ' data-layout="horizontal"';
		$data_attr .= ' data-ratio="1.33"';
		$data_attr .= ' data-cols="[[320,1],[480,2],[768,2],[1200,2],[1480,3],[9999,4]]"';
		$data_attr .= ' data-gutters="[[320,28],[480,28],[768,28],[1200,28],[1480,28],[9999,28]]"';
		$data_attr .= ' data-transition="600ms"';
		$data_attr .= ' data-preloader="true"';
		$data_attr .= ' data-rtl="false"';
		$data_attr .= ' data-slider=\'{"itemNav":"basic","swingSpeed":0.1,"cycleBy":"null","cycle":5000,"startAt":1}\'';
		
		return $data_attr;
		
	}
	
	/**
	* Grid skin wrapper start
	* @since 1.0.0
	*/
	public function grid_wrapper_end() {
		
				$wrapper_end = '</div>';
			$wrapper_end .= '</div>';
			$wrapper_end .= '<div class="tg-slider-bullets-holder">';
				$wrapper_end .= '<div class="tg-slider-bullets"></div>';
			$wrapper_end .= '</div>';
		$wrapper_end .= '</div>';
		
		return $wrapper_end;
		
	}
	
	/**
	* Grid skin wrapper start
	* @since 1.0.0
	*/
	public function grid_filters_area() {
		
		$grid_filters = $this->get_grid_filters();
		
		$filter_area  = '<div class="tg-filters-holder">';
		$filter_area .= '<div class="tg-filter tg-show-filter tg-filter-active tg-filter-all" data-filter="*"><span class="tg-filter-name">'.__( 'All', 'tg-text-domain' ).' (<span class="tg-filter-count"></span>)</span></div>';
		$filter_area .= '<div class="tg-filter tg-show-filter" data-filter=".selected"><span class="tg-filter-name">'.__( 'Selected Skin', 'tg-text-domain' ).' (1)</span></div>';
		if (count($grid_filters) > 1) {
			
			if(($key = array_search('Standard', $grid_filters)) !== false) {
    			unset($grid_filters[$key]);
			}
			sort($grid_filters);
			
			$filter_area .= '<div class="tg-filter tg-show-filter" data-filter=".standard">';
				$filter_area .= '<span class="tg-filter-name">'.__( 'Standard', 'tg-text-domain' ).' (<span class="tg-filter-count"></span>)</span>';
			$filter_area .= '</div>';
			
			foreach($grid_filters as $grid_filter) {	
				$filter_area .= '<div class="tg-filter tg-show-filter" data-filter=".'.esc_attr(sanitize_key($grid_filter)).'">';
					$filter_area .= '<span class="tg-filter-name">'.esc_attr(ucwords($grid_filter)).' (<span class="tg-filter-count"></span>)</span>';
				$filter_area .= '</div>';
			}
		}
		$filter_area .= '</div>';
		
		return $filter_area;
		
	}
	
	/**
	* Build skin item
	* @since 1.0.0
	*/
	public function build_skin_item() {

		// skin filter
		$skin_filter  = sanitize_key($this->skin_filter);
		
		// skin classes
		$skin_classes = esc_attr($this->skin_slug).' '.esc_attr($skin_filter);
		
		// skin slug
		$skin_slug = 'data-slug="'.esc_attr($this->skin_slug).'"';
		
		// set skin column number attribute
		$col_nb = $this->skin_col;
		$col_nb = (isset($row_nb) && !empty($col_nb) && $col_nb != 0) ? $col_nb : 1;
		$col_nb = ' data-col="'.esc_attr($col_nb).'"';
		
		// set skin row number attribute
		$row_nb = $this->skin_row;
		$row_nb = (isset($row_nb) && !empty($row_nb) && $row_nb != 0) ? $row_nb : 1;
		$row_nb = ' data-row="'.esc_attr($row_nb).'"';
		
		// build item skin markup
		$item_html  = '<article class="tg-item '.$skin_classes.'" '.$col_nb.$row_nb.'>';
			$item_html .= '<div class="tg-item-skin-name" '.$skin_slug.'>';
				$item_html .= '<i class="dashicons dashicons-yes"></i>';
				$item_html .= '<span>'.esc_html(ucfirst($this->skin_name)).'</span>';
				$item_html .= '<span class="tg-select-skin">'.__( 'Select this skin', 'tg-text-domain' ).'</span>';
			$item_html .= '</div>';
			$item_html .= '<div class="tg-item-inner">';
		
				// retireve current skin markup
				ob_start();
				$item_html .= include($this->skin_php);	
				ob_end_clean();
		
			$item_html .= '</div>';
		$item_html .= '</article>';
		
		return $item_html;
		
	}
	
}

if(!function_exists('The_Grid_Skins_Preview')) {
	/**
	* Tiny wrapper function
	* @since 1.0.0
	*/
	function The_Grid_Skins_Preview($post_ID = '') {
		$skins_preview = The_Grid_Skins_Preview::getInstance();
		return $skins_preview->process($post_ID);
	}
	
}