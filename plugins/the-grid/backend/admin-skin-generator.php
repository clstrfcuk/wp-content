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

class The_Grid_Skin_Generator {
	
	/**
	* The Grid base class
	*
	* @since 1.0.0
	* @access protected
	*
	* @var object
	*/
	protected $base;
	
	/**
	* skins settings
	*
	* @since 1.0.0
	* @access protected
	*
	* @var array
	*/
	protected $skins_settings;
	
	/**
	* skin slug/style
	*
	* @since 1.0.0
	* @access protected
	*
	* @var string
	*/
	protected $skin_slug;
	
	/**
	* skin data
	*
	* @since 1.0.0
	* @access protected
	*
	* @var array
	*/
	protected $skin_elements = array();
	
	/**
	* skin settings
	*
	* @since 1.0.0
	* @access protected
	*
	* @var array
	*/
	protected $skin_settings = array();
	
	/**
	* skin css
	*
	* @since 1.0.0
	* @access protected
	*
	* @var string
	*/
	protected $skin_css;
	
	/**
	* skin php
	*
	* @since 1.0.0
	* @access protected
	*
	* @var string
	*/
	protected $skin_php;
	
	/**
	* skin css file path
	*
	* @since 1.0.0
	* @access protected
	*
	* @var string
	*/
	protected $skin_css_file;
	
	/**
	* skin php file path
	*
	* @since 1.0.0
	* @access protected
	*
	* @var string
	*/
	protected $skin_php_file;

	/**
	* unvalid css rules
	*
	* @since 1.0.0
	* @access protected
	*
	* @var array
	*/
	protected $invalid_rules = array(
		'positions-unit',
		'float',
		'opacity',
		'width',
		'height',
		'width-unit',
		'height-unit',
		'margin-unit',
		'margin-top',
		'margin-right',
		'margin-bottom',
		'margin-left',
		'padding-unit',
		'padding-top',
		'padding-right',
		'padding-bottom',
		'padding-left',
		'border-unit',
		'border-top',
		'border-right',
		'border-bottom',
		'border-left',
		'border-radius-unit',
		'border-top-left-radius',
		'border-top-right-radius',
		'border-bottom-right-radius',
		'border-bottom-left-radius',
		'box-shadow-unit',
		'box-shadow-color',
		'box-shadow-horizontal',
		'box-shadow-vertical',
		'box-shadow-blur',
		'box-shadow-size',
		'box-shadow-inset',
		'text-shadow-unit',
		'text-shadow-color',
		'text-shadow-horizontal',
		'text-shadow-vertical',
		'text-shadow-blur',
		'letter-spacing-unit',
		'word-spacing-unit',
		'background-color-important',
		'background-position-x-unit',
		'background-position-y-unit',
		'top','bottom','left','right',
		'line-height-unit',
		'font-size-unit',
		'background-image',
		'custom-rules'
	);
	
	/**
	* Animation settings
	*
	* @since 1.0.0
	* @access protected
	*
	* @var array
	*/
	private $animation_data;
	
	
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
		
		// declare The Grid base helper
		$this->base = new The_Grid_Base();
		
	}
	
	/**
	* Generate Skin
	* @since 1.0.0
	*/
	public function generate_skin($skin_data) {
			
		$this->skin_settings = $skin_data;
		$this->skin_settings = json_decode($this->skin_settings, true);
				
		if ($this->skin_settings) {
			
			// generate skin slug
			$this->generate_skin_slug();
			
			// store animation data
			$this->get_animation_data();
			
			// create main styles for inner a tags
			$this->format_a_tags();
			
			// vertical alignment for center content overlay
			$this->vertical_alignment();
			
			// generate all data fro the skin css/php
			$this->generate_item_styles();
			$this->generate_element_styles();
			$this->generate_global_custom_css();
			$this->generate_skin_php();
			
			// return new skin slug/style
			return $this->build_skin_data();
		
		} else {
			
			$error_msg = __('Sorry, an unexpected error occurs while retrieving the skin settings', 'tg-text-domain' );
			throw new Exception($error_msg);
			
		}
		
	}
	
	/**
	* Build Skin data
	* @since 1.0.0
	*/
	public function build_skin_data($skin_data) {
		
		$params = array(
			'type'   => $this->skin_settings['item']['layout']['skin_style'],
			'filter' => $this->skin_settings['item']['layout']['skin_filter'],
			'slug'   => $this->skin_slug,
			'name'   => $this->skin_settings['item']['layout']['skin_name'],
			'col'    => $this->skin_settings['item']['layout']['skin_col'],
			'row'    => $this->skin_settings['item']['layout']['skin_row'],
			'php'    => 'is_custom_skin',
			'css'    => 'is_custom_skin'
		);
		
		return array(
			'name'     => $this->skin_settings['item']['layout']['skin_name'],
			'slug'     => $this->skin_slug,
			'params'   => wp_json_encode($params), 
			'settings' => wp_json_encode($this->skin_settings),
			'elements' => wp_json_encode($this->skin_elements),
			'styles'   => $this->base->compress_css($this->skin_css),
			'php_file' => $this->skin_php,
			'css_file' => $this->skin_css
		);
					
	}
	
	/**
	* Generate skin slug
	* @since 1.0.0
	*/
	public function generate_skin_slug() {
	
		$this->skin_slug = (isset($this->skin_settings['item']['layout']['skin_name'])) ? $this->skin_settings['item']['layout']['skin_name'] : null;
		
		if (empty($this->skin_slug)) {
			
			$error_msg = __('Please enter a skin name', 'tg-text-domain' );
			throw new Exception($error_msg);
		
		}
		
		$this->skin_slug = preg_replace('/ /', '-', strtolower($this->skin_slug));
		$this->skin_slug = preg_replace('/[^-0-9a-z_-]/', '', $this->skin_slug);
		
	}
	
	/**
	* Generate php code of the skin
	* @since 1.0.0
	*/
	public function generate_skin_php() {
	
		$this->skin_php_start();
		$this->top_content_holder();
		$this->media_holder();
		$this->bottom_content_holder();
		$this->skin_php_end();
		
	}
	
	/**
	* PHP skin data header
	* @since 1.0.0
	*/
	public function skin_php_start() {
		
		$this->skin_php .= '<?php'. "\r\n";
		$this->skin_php .= '/**'. "\r\n";
		$this->skin_php .= '* @package   The_Grid'. "\r\n";
		$this->skin_php .= '* @author    Themeone <themeone.master@gmail.com>'. "\r\n";
		$this->skin_php .= '* @copyright 2015 Themeone'. "\r\n";
		$this->skin_php .= '*'. "\r\n";
		$this->skin_php .= '* Skin: '.$this->skin_settings['item']['layout']['skin_name']. "\r\n";
		$this->skin_php .= '*'. "\r\n";
		$this->skin_php .= '*/'. "\r\n\r\n";
		$this->skin_php .= '// Exit if accessed directly'. "\r\n";
		$this->skin_php .= 'if (!defined(\'ABSPATH\')) {'. "\r\n";
		$this->skin_php .= "\t". 'exit;'. "\r\n";
		$this->skin_php .= '}'. "\r\n\r\n";
		$this->skin_php .= '$tg_el = The_Grid_Elements();'. "\r\n\r\n";
		$this->skin_php .= '$image  = $tg_el->get_attachement_url();'. "\r\n";
		$this->skin_php .= '$format = $tg_el->get_item_format();'. "\r\n\r\n";
		$this->skin_php .= '$output = null;'. "\r\n\r\n";
		
	}
	
	/**
	* PHP return skin
	* @since 1.0.0
	*/
	public function skin_php_end() {
		
		$this->skin_php .= "\r\n". 'return $output;';
		
	}

	/**
	* PHP skin top content holder
	* @since 1.0.0
	*/
	public function top_content_holder() {
		
		$holder   = 'top_content_holder';
		$elements = (isset($this->skin_settings['elements']['top-content-holder'])) ? $this->skin_settings['elements']['top-content-holder'] : null;	
		
		if ($elements) {
			
			$this->skin_php .= '// Top content wrapper start'. "\r\n";
			$this->skin_php .= '$output .= $tg_el->get_content_wrapper_start(\'\', \'top\');'. "\r\n";
				foreach ($elements as $element => $data) {
					$this->skin_php .= $this->item_element($holder, $element, $data);
				}
				$this->skin_php .= "\t". '$output .= $tg_el->get_content_clear();'. "\r\n";	
				
			$this->skin_php .= '$output .= $tg_el->get_content_wrapper_end();'. "\r\n";
			$this->skin_php .= '// Top content wrapper end'. "\r\n\r\n";

		}
		
	}
	
	/**
	* PHP skin bottom content holder
	* @since 1.0.0
	*/
	public function bottom_content_holder() {
		
		$holder   = 'bottom_content_holder';
		$elements = (isset($this->skin_settings['elements']['bottom-content-holder'])) ? $this->skin_settings['elements']['bottom-content-holder'] : null;
		
		if ($elements) {
			
			$this->skin_php .= '// Bottom content wrapper start'. "\r\n";
			$this->skin_php .= '$output .= $tg_el->get_content_wrapper_start(\'\', \'bottom\');'. "\r\n";	
				foreach ($elements as $element => $data) {
					$this->skin_php .= $this->item_element('bottom_content_holder', $element, $data);
				}
				$this->skin_php .= "\t". '$output .= $tg_el->get_content_clear();'. "\r\n";
			$this->skin_php .= '$output .= $tg_el->get_content_wrapper_end();'. "\r\n";
			$this->skin_php .= '// Bottom content wrapper end'. "\r\n";
			
		}
		
	}
	
	/**
	* PHP skin media holder
	* @since 1.0.0
	*/
	public function media_holder() {
		
		$holder        = 'media_holder';
		$skin_style    = (isset($this->skin_settings['item']['layout']['skin_style'])) ? $this->skin_settings['item']['layout']['skin_style'] : null;
		$media_content = (isset($this->skin_settings['item']['layout']['media_content'])) ? $this->skin_settings['item']['layout']['media_content'] : null;

		if ($media_content || $skin_style == 'grid') {
			
			$overlay_type   = (isset($this->skin_settings['item']['layout']['overlay_type'])) ? $this->skin_settings['item']['layout']['overlay_type'] : null;
		
			$top_content    = (isset($this->skin_settings['elements']['media-holder-top']))    ? $this->skin_settings['elements']['media-holder-top']    : null;
			$center_content = (isset($this->skin_settings['elements']['media-holder-center'])) ? $this->skin_settings['elements']['media-holder-center'] : null;
			$bottom_content = (isset($this->skin_settings['elements']['media-holder-bottom'])) ? $this->skin_settings['elements']['media-holder-bottom'] : null;
			
			if ($overlay_type == 'full') {
				$this->skin_elements['media_overlay'][] = array(
					'type'    => 'function',
					'element' => '',
					'content' => 'get_overlay',
					'args'    => ''
				);
			}
			
			$this->skin_php .= '// Media wrapper start'. "\r\n";
			$this->skin_php .= '$output .= $tg_el->get_media_wrapper_start();'. "\r\n\r\n";
			$this->skin_php .= "\t". '// Media content (image, gallery, audio, video)'. "\r\n";
			$this->skin_php .= "\t". '$output .= $tg_el->get_media();'. "\r\n\r\n";
			$this->skin_php .= "\t". '// if there is an image'. "\r\n";
			$this->skin_php .= "\t". 'if ($image) {'. "\r\n\r\n";
			$this->skin_php .= ($overlay_type == 'full') ? "\t\t". '// Overlay'. "\r\n" : null;
			$this->skin_php .= ($overlay_type == 'full') ? "\t\t". '$output .= $tg_el->get_overlay();'. "\r\n" : null;
			
			// top content
			if ($top_content) {
				
				if ($overlay_type == 'content') {
					$this->skin_elements['media_holder_top'][] = array(
						'type'    => 'function',
						'element' => 'top',
						'content' => 'get_overlay',
						'args'    => ''
					);
				}
				
				$this->skin_php .= "\t\t". '// Top wrapper start'. "\r\n";
				$this->skin_php .= "\t\t". '$output .= \'<div class="tg-top-outer">\';'. "\r\n";
				$this->skin_php .= ($overlay_type == 'content') ? "\t\t\t". '// Overlay'. "\r\n" : null;
				$this->skin_php .= ($overlay_type == 'content') ? "\t\t\t". '$output .= $tg_el->get_overlay(\'\',\'top\');'. "\r\n" : null;
				foreach ($top_content as $element => $data) {
					$function = str_replace("\t", "\t\t\t", $this->item_element('media_holder_top', $element, $data));
					$this->skin_php .= $function;
				}
				$this->skin_php .= "\t\t". '$output .= \'</div>\';'. "\r\n";
				$this->skin_php .= "\t\t". '// Top wrapper end'. "\r\n\r\n";
				
			}
			
			// center content
			if ($center_content) {
				
				if ($overlay_type == 'content') {
					$this->skin_elements['media_holder_center'][] = array(
						'type'    => 'function',
						'element' => 'top',
						'content' => 'get_overlay',
						'args'    => ''
					);
				}
				
				$this->skin_php .= "\t\t". '// Center wrapper start'. "\r\n";
				$this->skin_php .= "\t\t". '$output .= $tg_el->get_center_wrapper_start();'. "\r\n";
				$this->skin_php .= ($overlay_type == 'content') ? "\t\t\t". '// Overlay'. "\r\n" : null;
				$this->skin_php .= ($overlay_type == 'content') ? "\t\t\t". '$output .= $tg_el->get_overlay(\'\',\'center\');'. "\r\n" : null;
				foreach ($center_content as $element => $data) {
					$function = str_replace("\t", "\t\t\t", $this->item_element('media_holder_center', $element, $data));
					$this->skin_php .= $function;
				}
				$this->skin_php .= "\t\t". '$output .= $tg_el->get_center_wrapper_end();'. "\r\n";
				$this->skin_php .= "\t\t". '// Center wrapper end'. "\r\n\r\n";

			}
			
			
			//bottom content
			if ($bottom_content) {
				
				if ($overlay_type == 'content') {
					$this->skin_elements['media_holder_bottom'][] = array(
						'type'    => 'function',
						'element' => 'top',
						'content' => 'get_overlay',
						'args'    => ''
					);
				}
				
				$this->skin_php .= "\t\t". '// Bottom wrapper start'. "\r\n";
				$this->skin_php .= "\t\t". '$output .= \'<div class="tg-bottom-outer">\';'. "\r\n";
				$this->skin_php .= ($overlay_type == 'content') ? "\t\t\t". '// Overlay'. "\r\n" : null;
				$this->skin_php .= ($overlay_type == 'content') ? "\t\t\t". '$output .= $tg_el->get_overlay(\'\',\'bottom\');'. "\r\n" : null;
				foreach ($bottom_content as $element => $data) {
					$function = str_replace("\t", "\t\t\t", $this->item_element('media_holder_bottom', $element, $data));
					$this->skin_php .= $function;
				}
				$this->skin_php .= "\t\t". '$output .= \'</div>\';'. "\r\n";
				$this->skin_php .= "\t\t". '// Bottom wrapper end'. "\r\n\r\n";
				
			}

			$this->skin_php .= "\t". '}'. "\r\n\r\n";
			$this->skin_php .= '$output .= $tg_el->get_media_wrapper_end();'. "\r\n";
			$this->skin_php .= '// Media wrapper end'. "\r\n\r\n";
		
		}
			
	}

	/**
	* PHP generate elments
	* @since 1.0.0
	*/
	public function item_element($holder, $element, $data) {
		
		$source_type = (isset($data['source']['source_type'])) ? $data['source']['source_type'] : null;
		
		switch ($source_type) {
			case 'post':
				return $this->post_element($holder, $element, $data);
				break;
			case 'woocommerce':
				return $this->woocommerce_element($holder, $element, $data);
				break;
			case 'icon':
				return $this->icon_element($holder, $element, $data);
				break;
			case 'html':
				return $this->html_element($holder, $element, $data);
			case 'line_break':
				$this->skin_elements[$holder][] = array(
					'type'    => 'function',
					'element' => '',
					'content' => 'get_line_break',
					'args'    => ''
				);
				return "\t". '$output .= $tg_el->get_line_break();'. "\r\n";
			break;
		}

	}
	
	/**
	* PHP post content type
	* @since 1.0.0
	*/
	public function post_element($holder, $element, $data) {
		
		$settings = (isset($data['source'])) ? $data['source'] : null;
		$function = (isset($settings['post_content'])) ? $settings['post_content'] : null;
		
		if (!$function) {
			return;
		}

		switch ($function) {
			case 'get_the_title':
				$tag    = (isset($settings['title_tag']) && $settings['title_tag']) ? $settings['title_tag'] : '';
				$link   = (isset($settings['title_link']) && !$settings['title_link']) ? 'false' : 'true';
				$target = (isset($settings['title_target'])) ? $settings['title_target'] : ''; 
				$args   = 'array(\'link\' => '.esc_attr($link).', \'target\' => \''.esc_attr($target).'\', \'tag\' => \''.esc_attr($tag).'\')';
				$args2  = array('link' => esc_attr($link), 'target' => esc_attr($target), 'tag' => esc_attr($tag));
				break;
			case 'get_the_excerpt':
				$length = (isset($settings['excerpt_length'])) ? $settings['excerpt_length'] : '';
				$suffix = (isset($settings['excerpt_suffix'])) ? $settings['excerpt_suffix'] : ''; 
				$args   = 'array(\'length\' => '.esc_attr($length).', \'suffix\' => \''.esc_attr($suffix ).'\')';
				$args2  = array('length' => esc_attr($length), 'suffix' => esc_attr($suffix ));
				break;
			case 'get_the_date':
				$format = (isset($settings['date_format'])) ? $settings['date_format'] : '';
				$args   = 'array(\'format\' => \''.esc_attr($format).'\')';
				$args2  = array('format' => esc_attr($format));
				break;
			case 'get_the_author':
				$prefix = (isset($settings['author_prefix'])) ? $settings['author_prefix'] : '';
				$args   = 'array(\'prefix\' => __(\''.esc_attr($prefix).' \', \'tg-text-domain\'))';
				$args2  = array('prefix' => __(esc_attr($prefix).' ', 'tg-text-domain'));
				break;
			case 'get_the_terms':
				$link  = (isset($settings['terms_link']) && !$settings['terms_link']) ? 'false' : 'true';
				$color = (isset($settings['terms_color'])) ? $settings['terms_color'] : '';
				$separator = (isset($settings['terms_separator'])) ? $settings['terms_separator'] : '';
				$args  = 'array(\'link\' => '.esc_attr($link).', \'color\' => \''.esc_attr($color).'\', \'separator\' => \''.esc_attr($separator).'\')';
				$args2 = array('link' => esc_attr($link), 'color' => esc_attr($color), 'separator' => esc_attr($separator));
				break;
			case 'get_the_comments_number':
				$com_icon = (isset($settings['comment_icon']) && $settings['comment_icon']) ? '<i class="'.esc_attr($settings['comment_icon']).'"></i>' : '';
				$args  = 'array(\'icon\' => \''.$com_icon.'\' )';
				$args2 = array('icon' => $com_icon);
				break;
			case 'get_the_likes_number':
				$args  = '\'\'';
				$args2 = '';
				break;
			case 'get_read_more_button':
				$string = (isset($settings['read_more_text'])) ? $settings['read_more_text'] : '';
				$args   = 'array(\'text\' => __(\''.esc_html($string).'\', \'tg-text-domain\'))';
				$args2  = array('text' => __(esc_html($string), 'tg-text-domain'));
				break;
			case 'get_media_button':
				$content_type = $settings['lightbox_content_type'];
				$img_icon = (isset($settings['lightbox_image_icon']) && $settings['lightbox_image_icon']) ? '<i class="'.($settings['lightbox_image_icon']).'"></i>' : '';
				$aud_icon = (isset($settings['lightbox_audio_icon']) && $settings['lightbox_audio_icon']) ? '<i class="'.esc_attr($settings['lightbox_audio_icon']).'"></i>' : '';
				$vid_icon = (isset($settings['lightbox_video_icon']) && $settings['lightbox_video_icon']) ? '<i class="'.esc_attr($settings['lightbox_video_icon']).'"></i>' : '';
				$image    = (isset($settings['lightbox_image_text']) && $settings['lightbox_image_text'] && $content_type == 'text') ? $settings['lightbox_image_text'] : $img_icon;
				$audio    = (isset($settings['lightbox_audio_text']) && $settings['lightbox_audio_text'] && $content_type == 'text') ? $settings['lightbox_audio_text'] : $aud_icon;
				$video    = (isset($settings['lightbox_video_text']) && $settings['lightbox_video_text'] && $content_type == 'text') ? $settings['lightbox_video_text'] : $vid_icon;
				$args  = 'array(\'icons\' => array(\'image\' => \''.$image.'\', \'audio\' => \''.$audio.'\', \'video\' => \''.$video.'\'))';
				$args2 = array('icons' => array('image' => $image, 'audio' => $audio, 'video' => $video));
				break;
			case 'get_item_meta':
				$meta  = (isset($settings['metadata_key'])) ? $settings['metadata_key'] : null;
				$args  = '\''.esc_attr($meta).'\'';
				$args2 = esc_attr($meta);
				break;
		}
		
		$this->skin_elements[$holder][] = array(
			'type'    => 'function',
			'element' => $element,
			'content' => $function,
			'args'    => $args2
		);

		return (isset($args) && $function && $element) ? "\t". '$output .= $tg_el->'. $function .'('.$args.', \''.esc_attr($element).'\');'. "\r\n" : '';
		
	}
	
	/**
	* PHP woocommerce content type
	* @since 1.0.0
	*/
	public function woocommerce_element($holder, $element, $data) {
		
		$settings = (isset($data['source'])) ? $data['source'] : null;
		$function = (isset($settings['post_content'])) ? $settings['woocommerce_content'] : $function;
		
		if (!$function) {
			return;
		}
		
		if ($function == 'get_product_add_to_cart_url') {
			$args  = 'array(\'text\' => __(\''.esc_html($settings['add_to_cart_url_text']).'\', \'tg-text-domain\'))';
			$args2 = array('text' => __(''.esc_html($settings['add_to_cart_url_text']).'', 'tg-text-domain'));
		} else if ($function == 'get_product_cart_button') {
			$cart_icon     = $settings['woocommerce_cart_icon'];
			$icon_simple   = ($settings['woocommerce_cart_icon_simple']) ? '<i class="'.esc_attr($settings['woocommerce_cart_icon_simple']).'"></i>' : null;
			$icon_variable = ($settings['woocommerce_cart_icon_variable']) ? '<i class="'.esc_attr($settings['woocommerce_cart_icon_variable']).'"></i>' : null;
			$args  = 'array(\'cart_icon\' => \''.esc_attr($cart_icon).'\', \'icon_simple\' => \''.$icon_simple.'\', \'icon_variable\' => \''.$icon_variable.'\')';
			$args2 = array('cart_icon' => esc_attr($cart_icon), 'icon_simple' => $icon_simple, 'icon_variable' => $icon_variable);
		} else {
			$args  = '\'\'';
			$args2 = '';
		}
		
		$this->skin_elements[$holder][] = array(
			'type'    => 'function',
			'element' => $element,
			'content' => $function,
			'args'    => $args2
		);
		
		return (isset($args) && $function && $element) ? "\t". '$output .= $tg_el->'. $function .'('.$args.', \''.esc_attr($element).'\');'. "\r\n" : '';
	
	}
	
	/**
	* PHP icon element
	* @since 1.0.0
	*/
	public function icon_element($holder, $element, $data) {
		
		if (isset($data['source']['element_icon']) && $data['source']['element_icon']) {
			
			$this->skin_elements[$holder][] = array(
				'type'    => 'function',
				'element' => $element,
				'content' => 'get_icon_element',
				'args'    => array('icon' => esc_attr($data['source']['element_icon']))
			);

			$args  = 'array(\'icon\' => \''.esc_attr($data['source']['element_icon']).'\')';

			return (isset($args) && $element) ? "\t". '$output .= $tg_el->get_icon_element('.$args.', \''.esc_attr($element).'\');'. "\r\n" : '';
				
		}
	
	}
	
	/**
	* PHP html element
	* @since 1.0.0
	*/
	public function html_element($holder, $element, $data) {
		
		if (isset($data['source']['html_content'])) {
			
			// get Wordpress global from main tags & attributes
			global $allowedposttags, $allowedtags;
			
			// add svg tags & attributes
			$allowedsvg = array(
				'svg' => array(
					'id'          => array(), 
					'class'       => array(), 
					'width'       => array(),
					'height'      => array(),
					'style'       => array(), 
					'viewbox'     => array(),
					'viewBox'     => array(),
					'version'     => array(),
					'xmlns'       => array(),
					'xmlns:xlink' => array()
				),
				'g' => array(
					'id'           => array(), 
					'class'        => array(), 
					'style'        => array(), 
					'stroke'       => array(),
					'stroke-width' => array(),
					'fill'         => array(),
					'fill-rule'    => array(),
					'transform'    => array()
				),
				'path'   => array(
					'd'                 => array(),
					'id'                => array(),
					'class'             => array(), 
					'style'             => array(), 
					'stroke-width'      => array(),
					'stroke-linecap'    => array(),
					'stroke-miterlimit' => array()
				),
			);
			
			$allowed = array_merge($allowedposttags, $allowedtags, $allowedsvg);
			
			$this->skin_elements[$holder][] = array(
				'type'    => 'function',
				'element' => $element,
				'content' => 'get_html_element',
				'args'    => array('html' => wp_kses($data['source']['html_content'], $allowed))
			);
			
			$args  = 'array(\'html\' => \''.wp_kses($data['source']['html_content'], $allowed).'\')';

			return (isset($args) && $element) ? "\t". '$output .= $tg_el->get_html_element('.$args.', \''.esc_attr($element).'\');'. "\r\n" : '';
				
		}
	
	}

	/**
	* Get animation data
	* @since 1.0.0
	*/
	public function get_animation_data() {
		
		$animation_class = new The_Grid_Element_Animation();
		$this->animation_data = $animation_class->get_animation_name();
			
	}
	
	/**
	* Generate Item styles
	* @since 1.0.0
	*/
	public function generate_item_styles() {
		
		// get main layout containers
		$elements = (isset($this->skin_settings['item']['containers'])) ? $this->skin_settings['item']['containers'] : null;
		
		// overlay type (none, full-size, content based)
		$overlay_type = (isset($this->skin_settings['item']['layout']['overlay_type'])) ? $this->skin_settings['item']['layout']['overlay_type'] : null;
		
		// assign relative positon to content holders
		if (isset($this->skin_settings['elements']['top-content-holder']) && $this->skin_settings['elements']['top-content-holder']) {
			$elements['tg-item-content-holder[data-position="top"]']['styles']['idle_state']['position'] = 'relative';
			$elements['tg-item-content-holder[data-position="top"]']['styles']['idle_state']['display']  = 'block';
		} else {
			unset($elements['tg-item-content-holder[data-position="top"]']);
		}
		if (isset($this->skin_settings['elements']['bottom-content-holder']) && $this->skin_settings['elements']['bottom-content-holder']) {
			$elements['tg-item-content-holder[data-position="bottom"]']['styles']['idle_state']['position'] = 'relative';
			$elements['tg-item-content-holder[data-position="bottom"]']['styles']['idle_state']['display']  = 'block';
		} else {
			unset($elements['tg-item-content-holder[data-position="bottom"]']);
		}
		
		// if overlay exists
		if ($overlay_type) {
			
			$overlay_idle = isset($elements['tg-item-overlay']['styles']['idle_state']) ? $elements['tg-item-overlay']['styles']['idle_state'] : null;
			
			// set absolute positions
			$elements['tg-item-overlay']['styles']['idle_state']['position'] = 'absolute';
			$elements['tg-item-overlay']['styles']['idle_state']['top']      = $this->base->getVar($overlay_idle, 'top', '0');
			$elements['tg-item-overlay']['styles']['idle_state']['right']    = $this->base->getVar($overlay_idle, 'right', '0');
			$elements['tg-item-overlay']['styles']['idle_state']['bottom']   = $this->base->getVar($overlay_idle, 'bottom', '0');
			$elements['tg-item-overlay']['styles']['idle_state']['left']     = $this->base->getVar($overlay_idle, 'left', '0');
			
			// reassigned animation if full overlay style
			$elements['tg-item-overlay']['animation'] = ($overlay_type == 'full') ? $elements['tg-item-overlay[data-position="center"]']['animation'] : null;
			
			// if content based overlay
			if ($overlay_type == 'content') {
				
				$this->process_css('tg-item-overlay', $elements['tg-item-overlay']);
				
				// overlays positions
				$overlays = array('top', 'center', 'bottom');
				
				// generate each overlay animation
				foreach ($overlays as $position) {
					
					$media_holder = (isset($this->skin_settings['elements']['media-holder-'.$position])) ? $this->skin_settings['elements']['media-holder-'.$position] : null;
					
					// only if content inside media holder position (top, center, bottom)
					if (!empty($media_holder)) {
						
						$element = 'tg-item-overlay[data-position="'.$position.'"]';
						$data = (isset($elements[$element])) ? $elements[$element] : null;
						
						if ($data) {
							$idle_animation = $this->get_animation_idle($element, $data);
							if ($idle_animation) {
								$this->skin_css .= '.'.$this->skin_slug.' .'. $element .' {'. "\r\n";
								$this->skin_css .= $idle_animation;
								$this->skin_css .= '}'. "\r\n";
							}
							$this->skin_css .= $this->get_animation_hover($element, $data);
						}
						
					}
					
				}
				
			} else {
				
				$this->process_css('tg-item-overlay', $elements['tg-item-overlay']);

			}
			
		}
		
		// unset overlay data to prevent applying styles twice
		unset($elements['tg-item-overlay']);
		unset($elements['tg-item-overlay[data-position="top"]']);
		unset($elements['tg-item-overlay[data-position="center"]']);
		unset($elements['tg-item-overlay[data-position="bottom"]']);
		
		// for each media holder content (top & bottom only)
		foreach (array('top', 'bottom') as $position) {
			
			$media_holder = (isset($this->skin_settings['elements']['media-holder-'.$position])) ? $this->skin_settings['elements']['media-holder-'.$position] : null;
			
			// generate top/bottom media content
			if ($media_holder) {
				
				$elements['tg-'.$position.'-outer']['styles']['idle_state']['position'] = 'absolute';
				$elements['tg-'.$position.'-outer']['styles']['idle_state'][$position]  = '0';
				$elements['tg-'.$position.'-outer']['styles']['idle_state']['left']     = '0';
				$elements['tg-'.$position.'-outer']['styles']['idle_state']['right']    = '0';
				
			}

		}
		
		// generate media animations
		if (isset($elements['tg-item-media-holder']['animation']['animation_name']) && $elements['tg-item-media-holder']['animation']['animation_name'] != 'none') {
			
			$data = (isset($elements['tg-item-media-holder'])) ? $elements['tg-item-media-holder'] : null;
			
			if ($data) {
				
				if (isset($elements['tg-item-inner']['styles']['idle_state']['overflow']) && $elements['tg-item-inner']['styles']['idle_state']['overflow'] == 'hidden') {
					$this->skin_css .= '.'.$this->skin_slug.' .tg-item-media-inner.has-media-poster {'. "\r\n";
					$this->skin_css .= "\t" . 'overflow: hidden;'. "\r\n";
					$this->skin_css .= '}'. "\r\n";
				}
			
				// generate transition for animation
				$element  = 'tg-item-media-holder .tg-item-image,'. "\r\n";
				$element .= '.'.$this->skin_slug.' .tg-item-media-holder .tg-item-media-inner.has-media-poster,'. "\r\n";
				$element .= '.'.$this->skin_slug.' .tg-item-media-holder .tg-item-media-poster';
				$this->skin_css .= '.'.$this->skin_slug.' .'. $element.' {'. "\r\n";
					$this->skin_css .= $this->get_animation_idle($element, $data);
				$this->skin_css .= '}'. "\r\n";
				
				// get css animation (hover)
				$from = (isset($elements['tg-item-media-holder']['animation']['animation_from'])) ? $elements['tg-item-media-holder']['animation']['animation_from'] : null;
				$from = ($from != 'item') ? ':not(.tg-is-playing):not(.tg-force-play) .tg-item-media-holder:hover ' : ':not(.tg-is-playing):not(.tg-force-play):hover ';
				$this->skin_css .= '.'.$this->skin_slug.$from.'.tg-item-image,'. "\r\n";
				$this->skin_css .= '.'.$this->skin_slug.$from.'.tg-item-media-inner.has-media-poster,'. "\r\n";
				$this->skin_css .= '.'.$this->skin_slug.$from.'.tg-item-media-poster {'. "\r\n";
				$this->skin_css .= $this->get_animation_hover('', $data);
				$this->skin_css .= '}'. "\r\n";
			
			}
			
			unset($elements['tg-item-media-holder']['animation']);
			
		}
		
		// set alignment of media holder content
		if (isset($this->skin_settings['item']['layout']['overlay_alignment'])) {
			
			$this->skin_css .= '.'.$this->skin_slug.' .tg-top-outer,'. "\r\n";
			$this->skin_css .= '.'.$this->skin_slug.' .tg-center-inner,'. "\r\n";
			$this->skin_css .= '.'.$this->skin_slug.' .tg-bottom-outer {'. "\r\n";
				$this->skin_css .= "\t" . 'text-align: '. $this->skin_settings['item']['layout']['overlay_alignment'] .';'. "\r\n";
			$this->skin_css .= '}'. "\r\n";
			
		}
		
		// process styles of content holders
		if ($elements) {
			
			// loop through each element in the area
			foreach ($elements as $element => $data) {
				// generate the css of the element
				$this->process_css($element, $data);	
			}
			
		}	
		
	}
	
	/**
	* Generate element styles
	* @since 1.0.0
	*/
	public function generate_element_styles() {
		
		// get all item area (top-content/media/bottom-content)
		$areas = (array) $this->base->getVar($this->skin_settings, 'elements');

		// loop through each area
		foreach ($areas as $area => $elements) {
			
			// if there are elements in the area
			if ($elements) {
				
				// loop through each element in the area
				foreach ($elements as $element => $data) {
					// generate the css of the element
					$this->process_css($element, $data);
					// generate the css for comment icon
					$this->process_comment_icon_css($element, $data);	
					// generate the css for comment icon
					$this->process_like_icon_css($element, $data);
					// generate the css for each taxonomy term
					$this->process_taxonomy_term_css($element, $data);
					// generate the css for star rating
					$this->process_star_rating($element, $data);
					// generate the css cart button
					$this->process_cart_button($element, $data);
					// generate the css full price
					$this->process_full_price($element, $data);
				}
				
			}
		}
		
	}
	
	/**
	* Generate comment icon styles
	* @since 1.0.0
	*/
	public function process_comment_icon_css($element, $data) {
		
		$source = (array) $this->base->getVar($data, 'source');
		
		if ($this->base->getVar($source, 'source_type') == 'post' && $this->base->getVar($source, 'post_content') == 'get_the_comments_number' && $this->base->getVar($source, 'comment_icon')) {
			
			$rules['float']         = $this->base->getVar($source, 'comment_icon_float');
			$rules['color']         = $this->base->getVar($source, 'comment_icon_color');
			$rules['font-size']     = $this->base->getVar($source, 'comment_icon_font-size');
			$rules['font-unit']     = $this->base->getVar($source, 'comment_icon_font-size-unit');
			$rules['margin-unit']   = $this->base->getVar($source, 'comment_icon_margin-unit');
			$rules['margin-top']    = $this->base->getVar($source, 'comment_icon_margin-top');
			$rules['margin-left']   = $this->base->getVar($source, 'comment_icon_margin-left');
			$rules['margin-bottom'] = $this->base->getVar($source, 'comment_icon_margin-bottom');
			$rules['margin-right']  = $this->base->getVar($source, 'comment_icon_margin-right');
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' {'. "\r\n";
				$this->skin_css .= "\t". 'text-decoration: none;'. "\r\n";
				$this->skin_css .= "\t". 'outline: none;'. "\r\n";
				$this->skin_css .= "\t". '-webkit-box-shadow: none;'. "\r\n";
				$this->skin_css .= "\t". 'box-shadow: none;'. "\r\n";
			$this->skin_css .= '}'. "\r\n";	
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' i {'. "\r\n";
				$this->skin_css .= "\t". 'position: relative;'. "\r\n";
				$this->skin_css .= "\t". 'display: inline-block;'. "\r\n";
				$this->skin_css .= "\t". 'padding: 0 1px;'. "\r\n";
				$this->skin_css .= ($rules['float']) ? "\t". 'float: '.$rules['float'].';'. "\r\n" :  'float: left;'. "\r\n";
				$this->skin_css .= ($rules['color']) ? "\t". 'color: '.$rules['color'].' !important;'. "\r\n" : null;
				$this->skin_css .= ($rules['font-size'] && $rules['font-unit']) ? "\t". 'font-size: '.$rules['font-size'].$rules['font-unit'].';'. "\r\n" : null;
				$this->skin_css .= $this->get_margin($rules);
			$this->skin_css .= '}'. "\r\n";	
					
		}
	
	}
	
	/**
	* Generate like icon styles
	* @since 1.0.0
	*/
	public function process_like_icon_css($element, $data) {
		
		$source = (array) $this->base->getVar($data, 'source');
		
		if ($this->base->getVar($source, 'source_type') == 'post' && $this->base->getVar($source, 'post_content') == 'get_the_likes_number') {
			
			$rules['float']         = $this->base->getVar($source, 'like_icon_float');
			$rules['color']         = $this->base->getVar($source, 'like_icon_color');
			$rules['font-size']     = $this->base->getVar($source, 'like_icon_font-size');
			$rules['font-unit']     = $this->base->getVar($source, 'like_icon_font-size-unit');
			$rules['margin-unit']   = $this->base->getVar($source, 'like_icon_margin-unit');
			$rules['margin-top']    = $this->base->getVar($source, 'like_icon_margin-top');
			$rules['margin-left']   = $this->base->getVar($source, 'like_icon_margin-left');
			$rules['margin-bottom'] = $this->base->getVar($source, 'like_icon_margin-bottom');
			$rules['margin-right']  = $this->base->getVar($source, 'like_icon_margin-right');
			
			// icon position
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' .to-heart-icon {'. "\r\n";
				$this->skin_css .= "\t". 'position: relative;'. "\r\n";
				$this->skin_css .= "\t". 'display: inline-block;'. "\r\n";
				$this->skin_css .= ($rules['float']) ? "\t". 'float: '.$rules['float'].';'. "\r\n" :  'float: left;'. "\r\n";
				$this->skin_css .= $this->get_margin($rules);
			$this->skin_css .= '}'. "\r\n";	
			
			// icon size
			if ($rules['font-size']) {
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' .to-heart-icon svg {'. "\r\n";
					$this->skin_css .= ($rules['font-size'] && $rules['font-unit']) ? "\t". 'height: '.$rules['font-size'].$rules['font-unit'].';'. "\r\n" : null;
					$this->skin_css .= "\t". 'width: '.ceil($rules['font-size']*1.071).$rules['font-unit'].';'. "\r\n";
				$this->skin_css .= '}'. "\r\n";
			}
			
			// icon color
			if ($rules['color']) {
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.'.liked .to-heart-icon svg path,'. "\r\n";
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' .to-heart-icon svg:hover path {'. "\r\n";
					$this->skin_css .= "\t". 'fill: '.$rules['color'].' !important;'. "\r\n";
					$this->skin_css .= "\t". 'stroke: '.$rules['color'].' !important;'. "\r\n";
				$this->skin_css .= '}'. "\r\n";	
			}
					
		}
	
	}
	
	/**
	* Generate terms styles
	* @since 1.0.0
	*/
	public function process_taxonomy_term_css($element, $data) {
		
		$source = (array) $this->base->getVar($data, 'source');
		
		if ($this->base->getVar($source, 'source_type') == 'post' && $this->base->getVar($source, 'post_content') == 'get_the_terms') {
			
			$rules['padding-unit']   = $this->base->getVar($source, 'terms_padding-unit');
			$rules['padding-top']    = $this->base->getVar($source, 'terms_padding-top');
			$rules['padding-left']   = $this->base->getVar($source, 'terms_padding-left');
			$rules['padding-bottom'] = $this->base->getVar($source, 'terms_padding-bottom');
			$rules['padding-right']  = $this->base->getVar($source, 'terms_padding-right');
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' .tg-item-term {'. "\r\n";
				$this->skin_css .= "\t". 'position: relative;'. "\r\n";
				$this->skin_css .= "\t". 'display: inline-block;'. "\r\n";
				$this->skin_css .= $this->get_padding($rules);
			$this->skin_css .= '}'. "\r\n";	
		
		}
	
	}
	
	/**
	* Generate star rating styles (Woocommerce)
	* @since 1.0.0
	*/
	public function process_star_rating($element, $data) {
		
		$source = (array) $this->base->getVar($data, 'source');
		
		if ($this->base->getVar($source, 'source_type') == 'woocommerce' && $this->base->getVar($source, 'woocommerce_content') == 'get_product_rating') {
			
			$rules['font-size']    = $this->base->getVar($source, 'woo_star_font-size', 13);
			$rules['font-unit']    = $this->base->getVar($source, 'woo_star_font-size-unit', 'px');
			$rules['color-empty']  = $this->base->getVar($source, 'woo_star_color_empty', '#cccccc');
			$rules['color-fill']   = $this->base->getVar($source, 'woo_star_color_fill', '#e6ae48');
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.'.tg-item-rating .star-rating {'. "\r\n";
				$this->skin_css .= "\t". 'position: relative;'. "\r\n";
				$this->skin_css .= "\t". 'display: inherit;'. "\r\n";
				$this->skin_css .= "\t". 'overflow: hidden;'. "\r\n";
				$this->skin_css .= "\t". 'margin: 0;'. "\r\n";
			$this->skin_css .= '}'. "\r\n";	
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.'.tg-item-rating .star-rating span {'. "\r\n";
				$this->skin_css .= "\t". 'position: absolute;'. "\r\n";
				$this->skin_css .= "\t". 'display: block;'. "\r\n";
				$this->skin_css .= "\t". 'overflow: hidden;'. "\r\n";
				$this->skin_css .= "\t". 'left: 0;'. "\r\n";
				$this->skin_css .= "\t". 'top: 0;'. "\r\n";
				$this->skin_css .= "\t". 'bottom: 0;'. "\r\n";
				$this->skin_css .= "\t". 'margin: 0;'. "\r\n";
			$this->skin_css .= '}'. "\r\n";	
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.'.tg-item-rating .star-rating:before,'. "\r\n";
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.'.tg-item-rating .star-rating span:before {'. "\r\n";
				$this->skin_css .= "\t". 'position: relative;'. "\r\n";
				$this->skin_css .= "\t". 'display: inline-block;'. "\r\n";
				$this->skin_css .= "\t". 'float: left;'. "\r\n";
				$this->skin_css .= "\t". 'content: "\e636\e636\e636\e636\e636";'. "\r\n";
				$this->skin_css .= "\t". 'color: '.$rules['color-empty'].';'. "\r\n";
				$this->skin_css .= "\t". 'text-align: left;'. "\r\n";
				$this->skin_css .= "\t". 'white-space: nowrap;'. "\r\n";
				$this->skin_css .= "\t". 'font-family: "the_grid";'. "\r\n";
				$this->skin_css .= "\t". 'speak: none;'. "\r\n";
				$this->skin_css .= "\t". 'font-size: '.$rules['font-size'].$rules['font-unit'].';'. "\r\n";
				$this->skin_css .= "\t". 'line-height: '.$rules['font-size'].$rules['font-unit'].';'. "\r\n";
				$this->skin_css .= "\t". 'font-style: normal;'. "\r\n";
				$this->skin_css .= "\t". 'font-weight: normal;'. "\r\n";
				$this->skin_css .= "\t". 'font-variant: normal;'. "\r\n";
				$this->skin_css .= "\t". 'text-transform: none;'. "\r\n";
			$this->skin_css .= '}'. "\r\n";	
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.'.tg-item-rating .star-rating span:before {'. "\r\n";
				$this->skin_css .= "\t". 'color: '.$rules['color-fill'].';'. "\r\n";
			$this->skin_css .= '}'. "\r\n";	

		}
	
	}
	
	/**
	* Generate cart button (Woocommerce)
	* @since 1.0.0
	*/
	public function process_cart_button($element, $data) {
		
		$source = (array) $this->base->getVar($data, 'source');
		
		if ($this->base->getVar($source, 'source_type') == 'woocommerce' && $this->base->getVar($source, 'woocommerce_content') == 'get_product_cart_button') {
			
			$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' a {'. "\r\n";
				$this->skin_css .= "\t". 'margin: 0 !important;'. "\r\n";
				$this->skin_css .= "\t". 'padding: 0 !important;'. "\r\n";
				$this->skin_css .= "\t". 'border: none !important;'. "\r\n";
				$this->skin_css .= "\t". 'outline: none !important;'. "\r\n";
				$this->skin_css .= "\t". 'background: none !important;'. "\r\n";
			$this->skin_css .= '}'. "\r\n";	
			
			if ($this->base->getVar($source, 'woocommerce_cart_icon')) {
				
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' .added_to_cart.wc-forward {'. "\r\n";
					$this->skin_css .= "\t". 'position: absolute;'. "\r\n";
					$this->skin_css .= "\t". 'display: block;'. "\r\n";
					$this->skin_css .= "\t". 'top: 0;'. "\r\n";
					$this->skin_css .= "\t". 'right: 0;'. "\r\n";
					$this->skin_css .= "\t". 'bottom: 0;'. "\r\n";
					$this->skin_css .= "\t". 'left: 0;'. "\r\n";
					$this->skin_css .= "\t". 'opacity: 0 !important;'. "\r\n";
				$this->skin_css .= '}'. "\r\n";
			
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' .added.add_to_cart_button i:before {'. "\r\n";	
					$this->skin_css .= "\t". 'content: "\e612";'. "\r\n";
				$this->skin_css .= '}'. "\r\n";	
			
			}
		
		}
	
	}
	
	/**
	* Generate full price (Woocommerce)
	* @since 1.0.0
	*/
	public function process_full_price($element, $data) {
		
		$source = (array) $this->base->getVar($data, 'source');
		
		if ($this->base->getVar($source, 'source_type') == 'woocommerce' && $this->base->getVar($source, 'woocommerce_content') == 'get_product_full_price') {
			
			$styles = (array) $this->base->getVar($data, 'styles');
			$idle_state = (array) $this->base->getVar($styles, 'idle_state');
			$color_important = $this->base->getVar($idle_state, 'color-important');
			$color = $this->base->getVar($idle_state, 'color');
			
			if ($color_important) {
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.' * {'. "\r\n";
					$this->skin_css .= "\t". 'color: '.$color.' !important;'. "\r\n";
				$this->skin_css .= '}'. "\r\n";
			}
			
			$hover_state = (array) $this->base->getVar($styles, 'hover_state');
			$color_important = $this->base->getVar($hover_state, 'color-important');
			$color = $this->base->getVar($hover_state, 'color');
			
			if ($color_important) {
				$this->skin_css .= '.'.$this->skin_slug.' .'.$element.':hover * {'. "\r\n";
					$this->skin_css .= "\t". 'color: '.$color.' !important;'. "\r\n";
				$this->skin_css .= '}'. "\r\n";
			}
		
		}
	
	}
	
	/**
	* Generate Global custom CSS
	* @since 1.0.0
	*/
	public function generate_global_custom_css() {
		
		$item = (array) $this->base->getVar($this->skin_settings, 'item');
		$custom_css = $this->base->getVar($item, 'global_css');
		
		if ($custom_css) {
			$custom_css = wp_kses($custom_css, array('\'', '\"'));
			$custom_css = str_replace ('&gt;' , '>', $custom_css);
			$this->skin_css .= $custom_css;
		}
		
	}
	
	/**
	* Retrieve all css rules & value
	* @since 1.0.0
	*/
	public function process_css($element, $data) {
		
		// get idle & hover states
		$states = (array) $this->base->getVar($data, 'styles');
		
		// for each element state (idle & hover states)
		foreach ($states as $state => $rules) {
			
			// get css animation (hover)
				if ($state == 'hover_state') {
				$this->skin_css .= $this->get_animation_hover($element, $data);
				}
			
			// only if idle state or is hover and hover state
			if (is_array($rules) && (($this->base->getVar($states, 'is_hover') && $state == 'hover_state') || $state == 'idle_state' ) && !empty($rules)) {
			
				// pseudo class for hover css rules
				$pseudo = ($state == 'hover_state') ? ':hover' : null;
								
				// declare css class+pseudo (if hover)
				$prefix_class    = ($this->skin_slug) ? '.'. $this->skin_slug.' ' : null;
				$this->skin_css .= $prefix_class.'.'. $element.$pseudo.' {'. "\r\n";
				
				// loop through each rule of the current state
				foreach ($rules as $rule => $value) {
					
					// if there is a value and the rule is valid
					if ($value != '' && !in_array($rule,$this->invalid_rules) && strpos($rule, '-important') == false) {
						$unit = (isset($rules[$rule.'-unit'])) ? $rules[$rule.'-unit'] : null;
						$important_rule  = (isset($rules[$rule.'-important']) && $rules[$rule.'-important']) ? ' !important' : null;
						$this->skin_css .= "\t". esc_attr($rule) .': '. str_replace(array('&#039;','&quot;') , "'", esc_attr($value)) . esc_attr($unit) . esc_attr($important_rule) .';'. "\r\n";	
					}
				
				}
				
				// only for idle_state
				if ($state == 'idle_state') {
					$this->skin_css .= $this->get_positions($rules);
					$this->skin_css .= $this->get_float($rules);
					$this->skin_css .= $this->get_sizes($rules);
					$this->skin_css .= $this->get_margin($rules, $data);
					$this->skin_css .= $this->get_padding($rules, $data);
					$this->skin_css .= $this->get_cursor($data);
				}
				
				// generate special css rules (shorthands)
				$this->skin_css .= $this->get_border_width($rules);
				$this->skin_css .= $this->get_border_radius($rules);
				$this->skin_css .= $this->get_opacity($rules, $state, $this->base->getVar($states, 'is_hover'));
				$this->skin_css .= $this->get_text_shadow($rules);
				$this->skin_css .= $this->get_box_shadow($rules);
				$this->skin_css .= $this->get_background_image($rules);
				$this->skin_css .= $this->get_custom_rules($rules);
				
				// generate transition for animation
				if ($state == 'idle_state') {
					$this->skin_css .= $this->get_animation_idle($element, $data);
				}
				
				// close css class
				$this->skin_css .= '}'. "\r\n";	
			
			}
			
		}
		
		// return css for manual call
		return $this->skin_css;
				
	}
	
	/**
	* Format css a tags
	* @since 1.0.0
	*/
	public function format_a_tags() {
		
		$this->skin_css .= '.'. $this->skin_slug .' a:not([class*="tg-element-"]),'. "\r\n";
		$this->skin_css .= '.'. $this->skin_slug .' a:not([class*="tg-element-"]):active,'. "\r\n";
		$this->skin_css .= '.'. $this->skin_slug .' a:not([class*="tg-element-"]):focus,'. "\r\n";
		$this->skin_css .= '.'. $this->skin_slug .' .tg-item-price ins,'. "\r\n";
		$this->skin_css .= '.'. $this->skin_slug .' .tg-media-button i {'. "\r\n";
		$this->skin_css .= "\t". 'height: auto;'. "\r\n";
		$this->skin_css .= "\t". 'width: auto;'. "\r\n";
		$this->skin_css .= "\t". 'margin: 0;'. "\r\n";
		$this->skin_css .= "\t". 'padding: 0;'. "\r\n";
		$this->skin_css .= "\t". 'color: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'text-align: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'font-size: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'font-style: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'line-height: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'font-weight: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'text-transform: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'text-decoration: inherit !important;'."\r\n";
		$this->skin_css .= "\t". 'border: none;'. "\r\n";
		$this->skin_css .= "\t". '-webkit-box-shadow: none;'. "\r\n";
		$this->skin_css .= "\t". 'box-shadow: none;'. "\r\n";
		$this->skin_css .= "\t". '-webkit-transition: opacity 0.25s ease, color 0.25s ease;'. "\r\n";
		$this->skin_css .= "\t". '-moz-transition: opacity 0.25s ease, color 0.25s ease;'. "\r\n";
		$this->skin_css .= "\t". '-ms-transition: opacity 0.25s ease, color 0.25s ease;'. "\r\n";
		$this->skin_css .= "\t". '-o-transition: opacity 0.25s ease, color 0.25s ease;'. "\r\n";
		$this->skin_css .= "\t". 'transition: opacity 0.25s ease, color 0.25s ease;'. "\r\n";
		$this->skin_css .= '}'. "\r\n";

	}
	
	/**
	* Vertical Alignment css
	* @since 1.0.0
	*/
	public function vertical_alignment() {
		
		$this->skin_css .= '.'. $this->skin_slug .' .tg-center-inner > * {'. "\r\n";
		$this->skin_css .= "\t". 'vertical-align: middle;'. "\r\n";
		$this->skin_css .= '}'. "\r\n";

	}
	
	/**
	* Get css position
	* @since 1.0.0
	*/
	public function get_positions($rules) {
		
		// only if position absolute (not exist for relative)
		if ($this->base->getVar($rules, 'position') == 'absolute') {
			
			// get important rule
			$important = ($this->base->getVar($rules, 'positions-important')) ? ' !important' : null;
			// get the unit (px/em/%)
			$unit = ($this->base->getVar($rules, 'positions-unit')) ? $rules['positions-unit'].$important : 'px'.$important;
			
			// get each position value
			$position  = (isset($rules['top']) && $rules['top'] != '')    ? "\t". 'top: '. esc_attr((int) $rules['top'] . $unit).';'. "\r\n" : null;
			$position .= (isset($rules['bottom']) && $rules['bottom'] != '') ? "\t". 'bottom: '. esc_attr((int) $rules['bottom'] . $unit).';'. "\r\n" : null;
			$position .= (isset($rules['left']) && $rules['left'] != '')   ? "\t". 'left: '. esc_attr((int) $rules['left'] . $unit).';'. "\r\n" : null;
			$position .= (isset($rules['right']) && $rules['right'] != '')  ? "\t". 'right: '. esc_attr((int) $rules['right'] . $unit).';'. "\r\n": null;
			return $position;
			
		}
			
	}
	
	/**
	* Get css float
	* @since 1.0.0
	*/
	public function get_float($rules) {
		
		// only if position absolute (not exist for relative)
		if ($this->base->getVar($rules, 'display') == 'inline-block') {
			
			// get important rule
			$important = ($this->base->getVar($rules, 'float-important')) ? ' !important' : null;
			
			// get float value
			return (isset($rules['float']) && $rules['float'])  ? "\t". 'float: '. esc_attr($rules['float'] . $important).';'. "\r\n" : '';
			
		}
			
	}
	
	/**
	* Get css sizes
	* @since 1.0.0
	*/
	public function get_sizes($rules) {
		
		// get important rule
		$width_important  = ($this->base->getVar($rules, 'width-important'))  ? ' !important' : null;
		$height_important = ($this->base->getVar($rules, 'height-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$width_unit  = ($this->base->getVar($rules, 'width-unit'))  ? $rules['width-unit'].$width_important : 'px'.$width_important;
		$height_unit = ($this->base->getVar($rules, 'height-unit')) ? $rules['height-unit'].$height_important : 'px'.$height_important;
			
		// get each position value
		$sizes  = ($this->base->getVar($rules, 'width'))  ? "\t". 'width: '. esc_attr( $rules['width'] . $width_unit).';'. "\r\n" : null;
		$sizes .= ($this->base->getVar($rules, 'width'))  ? "\t". 'min-width: '. esc_attr((int) $rules['width'] . $width_unit).';'. "\r\n" : null;
		$sizes .= ($this->base->getVar($rules, 'height')) ? "\t". 'height: '. esc_attr((int) $rules['height'] . $height_unit).';'. "\r\n" : null;
		$sizes .= ($this->base->getVar($rules, 'height')) ? "\t". 'min-height: '. esc_attr((int) $rules['height'] . $height_unit).';'. "\r\n" : null;
		return $sizes;
			
	}
	
	/**
	* Get css margins
	* @since 1.0.0
	*/
	public function get_margin($rules, $data) {
		
		// get important rule
		$important = ($this->base->getVar($rules, 'margin-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$mg_u = ($this->base->getVar($rules, 'margin-unit')) ? $rules['margin-unit'] : 'px';

		// set margin conditions
		$mg_t_c = (isset($rules['margin-top'])    && is_numeric($rules['margin-top']));
		$mg_r_c = (isset($rules['margin-right'])  && is_numeric($rules['margin-right']));
		$mg_b_c = (isset($rules['margin-bottom']) && is_numeric($rules['margin-bottom']));
		$mg_l_c = (isset($rules['margin-left'])   && is_numeric($rules['margin-left']));
		
		// get each margin values
		$mg_t = ($mg_t_c && $rules['margin-top'] != 0)    ? $rules['margin-top'].$mg_u    : 0;
		$mg_r = ($mg_r_c && $rules['margin-right'] != 0)  ? $rules['margin-right'].$mg_u  : 0;
		$mg_b = ($mg_b_c && $rules['margin-bottom'] != 0) ? $rules['margin-bottom'].$mg_u : 0;
		$mg_l = ($mg_l_c && $rules['margin-left'] != 0)   ? $rules['margin-left'].$mg_u   : 0;
		
		$source  = (array) $this->base->getVar($data, 'source');
		$content = $this->base->getVar($source, 'post_content');
		$p_h_tag = $this->base->getVar($source, 'source_type') == 'post' && ($content == 'get_the_title' || $content == 'get_the_excerpt');
		
		// if 1 value is available
		if ($mg_t_c || $mg_r_c || $mg_b_c || $mg_l_c || $p_h_tag) {
			return "\t". 'margin: '.esc_attr($this->base->shorthand($mg_t.' '.$mg_r.' '.$mg_b.' '.$mg_l).$important).';'. "\r\n";
		}

	}
	
	/**
	* Get css paddings
	* @since 1.0.0
	*/
	public function get_padding($rules, $data) {
		
		// get important rule
		$important = ($this->base->getVar($rules, 'padding-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$pd_u = ($this->base->getVar($rules, 'padding-unit')) ? $rules['padding-unit'] : 'px';
		
		// set padding conditions
		$pd_t_c = (isset($rules['padding-top'])    && is_numeric($rules['padding-top']));
		$pd_r_c = (isset($rules['padding-right'])  && is_numeric($rules['padding-right']));
		$pd_b_c = (isset($rules['padding-bottom']) && is_numeric($rules['padding-bottom']));
		$pd_l_c = (isset($rules['padding-left'])   && is_numeric($rules['padding-left']));
		
		// get each padding values
		$pd_t = ($pd_t_c && $rules['padding-top'] != 0)    ? $rules['padding-top'].$pd_u    : 0;
		$pd_r = ($pd_r_c && $rules['padding-right'] != 0)  ? $rules['padding-right'].$pd_u  : 0;
		$pd_b = ($pd_b_c && $rules['padding-bottom'] != 0) ? $rules['padding-bottom'].$pd_u : 0;
		$pd_l = ($pd_l_c && $rules['padding-left'] != 0)   ? $rules['padding-left'].$pd_u   : 0;
		
		$source  = (array) $this->base->getVar($data, 'source');
		$content = $this->base->getVar($source, 'post_content');
		$p_h_tag = $this->base->getVar($source, 'source_type') == 'post' && ($content == 'get_the_title' || $content == 'get_the_excerpt');
		
		// if 1 value is available
		if ($pd_t_c || $pd_r_c || $pd_b_c || $pd_l_c || $p_h_tag) {
			return "\t". 'padding: '.esc_attr($this->base->shorthand($pd_t.' '.$pd_r.' '.$pd_b.' '.$pd_l).$important).';'. "\r\n";
		}
	
	}
	
	/**
	* Get css border-width
	* @since 1.0.0
	*/
	public function get_border_width($rules) {
		
		// get important rule
		$important = ($this->base->getVar($rules, 'border-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$bd_u = ($this->base->getVar($rules, 'border-unit'))   ? $rules['border-unit'] : 'px';
		
		// set padding conditions
		$bd_t_c = (isset($rules['border-top'])    && is_numeric($rules['border-top']));
		$bd_r_c = (isset($rules['border-right'])  && is_numeric($rules['border-right']));
		$bd_b_c = (isset($rules['border-bottom']) && is_numeric($rules['border-bottom']));
		$bd_l_c = (isset($rules['border-left'])   && is_numeric($rules['border-left']));
		
		// get each padding values
		$bd_t = ($bd_t_c && $rules['border-top'] != 0)    ? $rules['border-top'].$bd_u    : 0;
		$bd_r = ($bd_r_c && $rules['border-right'] != 0)  ? $rules['border-right'].$bd_u  : 0;
		$bd_b = ($bd_b_c && $rules['border-bottom'] != 0) ? $rules['border-bottom'].$bd_u : 0;
		$bd_l = ($bd_l_c && $rules['border-left'] != 0)   ? $rules['border-left'].$bd_u   : 0;
		
		// if 1 value is available
		if ($bd_t_c || $bd_r_c || $bd_b_c || $bd_l_c) {
			return "\t". 'border-width: '.esc_attr($this->base->shorthand($bd_t.' '.$bd_r.' '.$bd_b.' '.$bd_l).$important).';'. "\r\n";
		}
	
	}
	
	/**
	* Get css border-radius
	* @since 1.0.0
	*/
	public function get_border_radius($rules) {
		
		// get important rule
		$important = ($this->base->getVar($rules, 'border-radius-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$bd_u  = ($this->base->getVar($rules, 'border-radius-unit')) ? $rules['border-radius-unit'] : 'px';
		
		// set padding conditions
		$bd_tl_c = (isset($rules['border-top-left-radius'])     && is_numeric($rules['border-top-left-radius']));
		$bd_tr_c = (isset($rules['border-top-right-radius'])    && is_numeric($rules['border-top-right-radius']));
		$bd_br_c = (isset($rules['border-bottom-right-radius']) && is_numeric($rules['border-bottom-right-radius']));
		$bd_bl_c = (isset($rules['border-bottom-left-radius'])  && is_numeric($rules['border-bottom-left-radius']));
		
		// get each padding values
		$bd_tl = ($bd_tl_c && $rules['border-top-left-radius'] != 0)     ? $rules['border-top-left-radius'].$bd_u     : 0;
		$bd_tr = ($bd_tr_c && $rules['border-top-right-radius'] != 0)    ? $rules['border-top-right-radius'].$bd_u    : 0;
		$bd_br = ($bd_br_c && $rules['border-bottom-right-radius'] != 0) ? $rules['border-bottom-right-radius'].$bd_u : 0;
		$bd_bl = ($bd_bl_c && $rules['border-bottom-left-radius'] != 0)  ? $rules['border-bottom-left-radius'].$bd_u  : 0;
		
		// if 1 value is available
		if ($bd_tl_c || $bd_tr_c || $bd_br_c || $bd_bl_c) {
			return "\t". 'border-radius: '.esc_attr($this->base->shorthand($bd_tl.' '.$bd_tr.' '.$bd_br.' '.$bd_bl).$important).';'. "\r\n";
		}

	
	}

	/**
	* Get css box shadow
	* @since 1.0.0
	*/
	public function get_box_shadow($rules) {
		
		// get important rule
		$important = ($this->base->getVar($rules, 'box-shadow-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$sd_un  = ($this->base->getVar($rules, 'box-shadow-unit')) ? $rules['box-shadow-unit'] : 'px';
		
		// get each box shadow value
		$sd_hz  = ($this->base->getVar($rules, 'box-shadow-horizontal')) ? $rules['box-shadow-horizontal'].$sd_un.' ' : '0 ';
		$sd_vc  = ($this->base->getVar($rules, 'box-shadow-vertical'))   ? $rules['box-shadow-vertical'].$sd_un.' ' : '0 ';
		$sd_bl  = ($this->base->getVar($rules, 'box-shadow-blur'))       ? $rules['box-shadow-blur'].$sd_un.' ' : '0 ';
		$sd_sz  = ($this->base->getVar($rules, 'box-shadow-size'))       ? $rules['box-shadow-size'].$sd_un.' ' : '0 ';
		$sd_co  = ($this->base->getVar($rules, 'box-shadow-color'))      ? $rules['box-shadow-color'] : 'rgba(0,0,0,0)';
		$sd_in  = ($this->base->getVar($rules, 'box-shadow-inset'))      ? 'inset ' : '';
		
		// is there is at least one value superior to 0
		if ($sd_hz != 0 || $sd_vc != 0 || $sd_bl != 0 || $sd_sz != 0) {
			$shadow_rule = $sd_in.$sd_hz.$sd_vc.$sd_bl.$sd_sz.$sd_co;
			$shadow  = "\t". '-webkit-box-shadow: '. esc_attr($shadow_rule.$important).';'. "\r\n";
			$shadow .= "\t". '-moz-box-shadow: '. esc_attr($shadow_rule.$important).';'. "\r\n";
			$shadow .= "\t". 'box-shadow: '. esc_attr($shadow_rule.$important).';'. "\r\n";
			return $shadow;
		}
		
	}
	
	/**
	* Get css text shadow
	* @since 1.0.0
	*/
	public function get_text_shadow($rules) {
			
		// get important rule
		$important = ($this->base->getVar($rules, 'text-shadow-important')) ? ' !important' : null;
		
		// get the unit (px/em/%)
		$sd_un  = ($this->base->getVar($rules, 'text-shadow-unit')) ? $rules['text-shadow-unit'] : 'px';
		
		// get each text shadow value
		$sd_hz  = ($this->base->getVar($rules, 'text-shadow-horizontal')) ? $rules['text-shadow-horizontal'].$sd_un.' ' : '0 ';
		$sd_vc  = ($this->base->getVar($rules, 'text-shadow-vertical'))   ? $rules['text-shadow-vertical'].$sd_un.' ' : '0 ';
		$sd_bl  = ($this->base->getVar($rules, 'text-shadow-blur'))       ? $rules['text-shadow-blur'].$sd_un.' ' : '0 ';
		$sd_co  = ($this->base->getVar($rules, 'text-shadow-color'))      ? $rules['text-shadow-color'] : 'rgba(0,0,0,0)';
		
		// is there is at least one value superior to 0
		if ($sd_hz != 0 || $sd_vc != 0 || $sd_bl != 0) {
			$shadow_rule = $sd_hz.$sd_vc.$sd_bl.$sd_co;
			$shadow = "\t". 'text-shadow: '.esc_attr($shadow_rule.$important).';'. "\r\n";
			return $shadow;
		}
			
	}

	/**
	* Get css opacity
	* @since 1.0.0
	*/
	public function get_opacity($rules, $state) {
				
		if ($this->base->getVar($rules, 'opacity')) {
			
			// if opacity < 1 & idle state or if opacity & hover state
			if (($state == 'idle_state' && $rules['opacity'] < 1) || ($state != 'idle_state') || $this->base->getVar($rules, 'opacity-important')) {
				
				// get important rule
				$important = ($this->base->getVar($rules, 'opacity-important') || $state == 'hover_state') ? ' !important' : null;
				return "\t". 'opacity: '.esc_attr($rules['opacity'].$important).';'. "\r\n";
				
			}
		}
	
	}
	
	/**
	* Get css background image
	* @since 1.0.0
	*/
	public function get_background_image($rules) {
		
		if ($this->base->getVar($rules, 'background-image')) {
			
			// get important rule
			$important = ($this->base->getVar($rules, 'background-image-important')) ? ' !important' : null;
			return ($rules['background-image']) ? "\t". 'background-image: url('.esc_url($rules['background-image']).')'.esc_attr($important).';'. "\r\n" : null;
			
		}
		
	}
	
	/**
	* Get css cursor pointer
	* @since 1.0.0
	*/
	public function get_cursor($data) {
		
		$source       = (array) $this->base->getVar($data, 'source');
		$source_type  = $this->base->getVar($source ,'source_type');
		$post_content = $this->base->getVar($source, 'post_content');
		
		if ($source && $source_type == 'post' && in_array($post_content, array('get_media_button','get_link_button'))) {
			return "\t". 'cursor: pointer;'. "\r\n";
		}
	
	}
	
	/**
	* Get custom css rules
	* @since 1.0.0
	*/
	public function get_custom_rules($rules) {
		
		// if there are custom rules
		if ($this->base->getVar($rules, 'custom-rules')) {
		
			// transform to array
			$data = explode(';', $rules['custom-rules']);
			
			if ($data) {
			
				$custom_css = null;
				
				foreach ($data as $rule) {
					// separate css rule from css value
					$rule = explode(':', $rule);
					if (isset($rule[0]) && isset($rule[1])) {
						$value = wp_kses($rule[1], array( '\'', '\"' ));
						$value = str_replace('&gt;' , '>' , $value);
						$custom_css .= "\t". esc_attr(preg_replace('/\s+/', '', $rule[0])) .': '. $value .';'. "\r\n";
					}
				}
				
				return $custom_css;
			
			}
		
		}
		
	}
	
	/**
	* Get css transition
	* @since 1.0.0
	*/
	public function get_animation_idle($element, $data) {
		
		$animation = $this->base->getVar($data, 'animation');
		
		if ($animation) {

			$animation_name  = $this->base->getVar($animation, 'animation_name');
			$animation_state = $this->base->getVar($animation, 'animation_state');
			
			if (isset($this->animation_data[$animation_name]) && $animation_name != 'none') {

				$type       = ($animation_state == 'show') ? $this->base->getVar($this->animation_data[$animation_name], 'hidden') : $this->base->getVar($this->animation_data[$animation_name], 'visible');
				$easing     = $this->base->getVar($animation, 'transition_function');
				$easing     = ($easing == 'cubic-bezier') ? $this->base->getVar($animation, 'transition_bezier') : $easing;
				$duration   = $this->base->getVar($animation, 'transition_duration').'ms';
				$delay      = ($this->base->getVar($animation, 'transition_delay')) ? ' '.$data['animation']['transition_delay'].'ms' : null;
				$opacity    = ($animation_state == 'show') ? 0  : 1;
				$visibility = ($animation_state == 'show') ? 'hidden'  : 'visible';
				$vendors    = array('-webkit-', '-moz-', '-ms-', '');
				
				$animation  = "\t". 'opacity: '.esc_attr($opacity).';'. "\r\n";
				$animation .= "\t". 'visibility: '. esc_attr($visibility) .';'. "\r\n";
				foreach ($vendors as $vendor) {
					$animation .= "\t". $vendor.'transition: all '.esc_attr($duration).' '.esc_attr($easing.$delay).';'. "\r\n";
				}
				
				if (isset($this->animation_data[$animation_name]) && $animation_name !== 'fade_in' && $type) {
					foreach ($vendors as $vendor) {
						$animation .= "\t". $vendor.'transform: '.esc_attr($type).';'. "\r\n";
					}
				}
				
				return $animation;
			
			}
		
		}
		
	}
	
	/**
	* Get css animation
	* @since 1.0.0
	*/
	public function get_animation_hover($element, $data) {
		
		$animation = $this->base->getVar($data, 'animation');
		
		if ($animation) {

			$animation_name  = $this->base->getVar($animation, 'animation_name');
			$animation_from  = $this->base->getVar($animation, 'animation_from', 'item');
			$animation_state = $this->base->getVar($animation, 'animation_state');
			
			$selector_hover = array(
				'item' => ':hover',
				'media' => ' .tg-item-media-holder:hover',
				'top-content' => ' .tg-item-content-holder[data-position="top"]:hover',
				'bottom-content' => ' .tg-item-content-holder[data-position="bottom"]:hover'
			);
			
			if ($animation_from == 'parent') {
				$animation_from  = (isset($this->skin_settings['elements']['top-content-holder'][$element])) ? 'top-content' : $animation_from;
				$animation_from  = (isset($this->skin_settings['elements']['bottom-content-holder'][$element])) ? 'bottom-content' : $animation_from;
				$animation_from  = (isset($this->skin_settings['elements']['media-holder-top'][$element])) ? 'media' : $animation_from;
				$animation_from  = (isset($this->skin_settings['elements']['media-holder-center'][$element])) ? 'media' : $animation_from;
				$animation_from  = (isset($this->skin_settings['elements']['media-holder-bottom'][$element])) ? 'media' : $animation_from;
			}
		
			if (isset($this->animation_data[$animation_name]) && $animation_name !== 'none') {
				
				$type       = ($animation_state == 'show') ? $this->base->getVar($this->animation_data[$animation_name], 'visible') : $this->base->getVar($this->animation_data[$animation_name], 'hidden');
				$opacity    = ($animation_state == 'show') ? 1  : 0;
				$not_with   = (strpos($element, 'tg-item-overlay') !== false) ? ':not(.tg-force-play):not(.tg-is-playing)' : null;
				$visibility = ($animation_state == 'show') ? 'visible'  : 'hidden';
				
				$animation  = ($element) ? '.'. $this->skin_slug. $not_with . $selector_hover[$animation_from] .' .'. $element.' {'. "\r\n" : null;
				$animation .= "\t". 'opacity: '.$opacity.';'. "\r\n";
				$animation .= "\t". 'visibility: '. esc_attr($visibility) .';'. "\r\n";
				if (isset($this->animation_data[$animation_name]) && $animation_name !== 'fade_in' && $type) {
					$animation .= "\t". '-webkit-transform: '.esc_attr($type).';'. "\r\n";
					$animation .= "\t". '-moz-transform: '.esc_attr($type).';'. "\r\n";
					$animation .= "\t". '-ms-transform: '.esc_attr($type).';'. "\r\n";
					$animation .= "\t". 'transform: '.esc_attr($type).';'. "\r\n";
				}
				$animation .= ($element) ? '}'. "\r\n" : null;
				
				return $animation;
				
			}
		
		}
		
	}
	
	/**
	* Get css animation
	* @since 1.0.0
	*/
	public function reset_css() {
		$this->skin_css = null;
	}
	
}