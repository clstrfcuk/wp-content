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

class The_Grid_Item {
	
	/**
	* skin elements
	*
	* @since 1.6.0
	* @access protected
	*
	* @var array
	*/
	protected $skin_elements = array();
	
	/**
	* skin elements
	*
	* @since 1.6.0
	* @access protected
	*
	* @var string
	*/
	protected $skin_content;
	
	/**
	* skin slugs
	*
	* @since 1.6.0
	* @access protected
	*
	* @var array
	*/
	protected $skin_slugs = array();
	
	/**
	* skin element class
	*
	* @since 1.6.0
	* @access protected
	*
	* @var array
	*/
	protected $tg_el = array();
	
	/**
	* The singleton instance
	*
	* @since 1.6.0
	* @access private
	*
	* @var objet
	*/
	static private $instance = null;
	
	/**
	* To initialize a The_Grid_Item object
	* @since 1.6.0
	*/
	static public function getInstance() {
		
		if(self::$instance == null) {
			self::$instance = new self;
		}
		
		return self::$instance;
		
	}
	
	/**
	* Build skin
	* @since 1.6.0
	*/
	public function build_skin($slug) {
		
		$this->skin_content = null;
		$this->get_skin_settings($slug);
		$this->generate_skin();

		return $this->skin_content;
	
	}
	
	/**
	* Get skin settings
	* @since 1.6.0
	*/
	public function get_skin_settings($slug) {
		
		// if this skin was not proceed before
		if (!isset($this->skin_slugs[$slug])) {
			
			$this->skin_elements = The_Grid_Custom_Table::get_skin_elements($slug);
			$this->skin_elements = $this->skin_elements;
			$this->skin_elements = json_decode($this->skin_elements, true);
			
			// store skin settings for next time
			$this->skin_slugs[$slug] = $this->skin_elements;
		
		// if this skin was already proceeded then get directly the settings (prevent additional query)
		} else {
			
			$this->skin_elements = $this->skin_slugs[$slug];
			
		}
	
	}
	
	/**
	* Generate skin logic
	* @since 1.6.0
	*/
	public function generate_skin() {
		
		$this->tg_el = The_Grid_Elements();
		$this->content_holder('top');
		$this->media_holder();
		$this->content_holder('bottom');
		
	}
	
	/**
	* Skin content holder (top & button)
	* @since 1.6.0
	*/
	public function content_holder($position) {
		
		$elements = (isset($this->skin_elements[$position.'_content_holder'])) ? $this->skin_elements[$position.'_content_holder'] : null;
		
		if ($elements) {
			
			$elements = $this->get_element($elements);
			
			if ($elements) {

				$this->skin_content .= $this->tg_el->get_content_wrapper_start('', $position);
				
					$this->skin_content .= $elements;
					
					$this->skin_content .= $this->tg_el->get_content_clear();
					
				$this->skin_content .= $this->tg_el->get_content_wrapper_end();
			
			}

		}
	
	}
	
	/**
	* Skin media holder wrapper
	* @since 1.6.0
	*/
	public function media_holder() {
		
		$media = $this->tg_el->get_media();
		
		if ($media) {
		
			$this->skin_content .= $this->tg_el->get_media_wrapper_start();
			$this->skin_content .= $media;
			$image = $this->tg_el->get_attachement_url();
	
			if ($image) {
				
				$areas = array(
					'media_holder_top',
					'media_holder_center',
					'media_holder_bottom'
				);
				
				$content = null;
				
				foreach ($areas as $area) {
					
					$elements = (isset($this->skin_elements[$area])) ? $this->skin_elements[$area] : array();
					$content .= $this->$area($elements);
				
				}
				
				if (isset($this->skin_elements['media_overlay'])) {
					$this->skin_content .= $this->tg_el->get_overlay();
				}
				
				$this->skin_content .= $content;

			
			}
			
			$this->skin_content .= $this->tg_el->get_media_wrapper_end();
		
		}

	}
	
	/**
	* Skin media holder top
	* @since 1.6.0
	*/
	public function media_holder_top($elements) {
		
		$content  = null;
		$elements = $this->get_element($elements);
			
		if ($elements) {
		
			$content .= '<div class="tg-top-outer">';

				$content .= $elements;
					
			$content .= '</div>';
		
		}
		
		return $content;
	
	}
	
	/**
	* Skin media holder center
	* @since 1.6.0
	*/
	public function media_holder_center($elements) {
		
		$content  = null;
		$elements = $this->get_element($elements);
			
		if ($elements) {
		
			$content .= $this->tg_el->get_center_wrapper_start();
				
				$content .= $elements;
					
				$content .= $this->tg_el->get_content_clear();
					
			$content .= $this->tg_el->get_center_wrapper_end();
		
		}
		
		return $content;
	
	}
	
	/**
	* Skin media holder bottom
	* @since 1.6.0
	*/
	public function media_holder_bottom($elements) {
		
		$content  = null;
		$elements = $this->get_element($elements);
			
		if ($elements) {
		
			$content .= '<div class="tg-bottom-outer">';

				$content .= $elements;
					
			$content .= '</div>';
		
		}
		
		return $content;
	
	}
	
	/**
	* Skin element
	* @since 1.6.0
	*/
	public function get_element($elements) {
		
		$content = null;
		
		foreach ($elements as $element) {

			if (isset($element['type']) && isset($element['content']) && isset($element['element']) && $element['type'] == 'function') {
				$content .= (isset($element['args'])) ? $this->tg_el->$element['content']($element['args'], esc_attr($element['element'])) : null;
			} else {
				$content .= $element['content'];
			}
	
		}
		
		return $content;
	
	}
	
}

if (!function_exists('The_Grid_Item')) {
	
	/**
	* Tiny wrappers functions
	* @since 1.6.0
	*/
	function The_Grid_Item($slug) {
		
		$the_grid_item = The_Grid_Item::getInstance();	
		return $the_grid_item->build_skin($slug);
		
	}

}