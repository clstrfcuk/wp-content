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

class The_Grid_Elements {
	
	/**
	* Grid Data
	*
	* @since 1.0.0
	* @access public
	*
	* @var array
	*/
	public $grid_data;
	
	/**
	* Grid items
	*
	* @since 1.0.0
	* @access public
	*
	* @var array
	*/
	public $grid_items;
	
	/**
	* Grid items
	*
	* @since 1.0.0
	* @access public
	*
	* @var array
	*/
	public $item_colors;
	
	/**
	* Base class
	*
	* @since 1.0.0
	* @access private
	*
	* @var objet
	*/
	private $base;
	
	/**
	* The singleton instance
	*
	* @since 1.0.0
	* @access private
	*
	* @var objet
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
	* _construct disabled
	* @since 1.0.0
	*/
	public function __construct() {		
	}
	
	/**
	* To initialize a The_Grid_Elements object
	* @since 1.0.0
	*/
	static public function getInstance() {
		
		if(self::$instance == null) {
			self::$instance = new self;
		}
		
		return self::$instance;
		
	}
	
	/**
	* To initialize a The_Grid_Elements object
	* @since 1.0.0
	*/
	public function init() {
		
		// set the grid base helper class
		$this->base = new The_Grid_Base();
		// retrieve grid data
		$this->grid_data = tg_get_grid_data();
		// retrieve grid item data
		$this->grid_item = tg_get_grid_item();
		// retrieve item colors
		$this->item_colors = $this->get_colors();

	}
	
	/**
	* Content clear
	* @since: 1.6.0
	*/
	public function get_content_clear() {	
	
		return '<div class="tg-item-clear"></div>';
		
	}

	/**
	* Content line break
	* @since: 1.6.0
	*/
	public function get_line_break() {	
	
		return '<div class="tg-item-line-break"></div>';
		
	}
	
	/**
	* Media holder markup start 
	* @since: 1.0.0
	*/
	public function get_media_wrapper_start($class = '') {
		
		$class   = ($class) ? ' '.$class : null;
		$color = $this->item_colors['overlay']['class'];
		
		return '<div class="tg-item-media-holder '.esc_attr($color).esc_attr($class).'">';
		
	}
	
	/**
	* Media holder markup end 
	* @since: 1.0.0
	*/
	public function get_media_wrapper_end() {	
	
		return '</div>';
		
	}
	
	/**
	* Content holder markup start (masonry)
	* @since: 1.0.0
	*/
	public function get_content_wrapper_start($class = '', $position = '') {
		
		$class      = ($class) ? ' '.$class : null;
		$color      = $this->item_colors['content']['class'];
		$format     = ' '.$this->get_item_format().'-format';
		$bg_skin    = $this->grid_data['skin_content_background'];
		$bg_item    = $this->item_colors['content']['background'];
		$background = ($bg_skin != $bg_item) ? ' style="background-color:'.esc_attr($bg_item).'"' : null;
		$position   = ($position) ? ' data-position="'.$position.'"' : null;
		
		return '<div class="tg-item-content-holder '.esc_attr($color).esc_attr($format).esc_attr($class).'"'.$position.$background.'>';
		
	}
	
	/**
	* Content holder markup end  (masonry)
	* @since: 1.0.0
	*/
	public function get_content_wrapper_end() {	
	
		return '</div>';
		
	}
	
	/**
	* Center markup Start
	* @since: 1.0.0
	*/
	public function get_center_wrapper_start() {	
	
		$html  = '<div class="tg-center-outer">';
			$html .= '<div class="tg-center-inner">';
		return $html;
		
	}
	
	/**
	* Center markup End
	* @since: 1.0.0
	*/
	public function get_center_wrapper_end() {	
	
			$html  = '</div>';
		$html .= '</div>';
		return $html;
		
	}
	
	/**
	* Icon element
	* @since: 1.6.0
	*/
	public function get_icon_element($args = '', $class ='') {	
	
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		$icon  = $this->base->getVar($args, 'icon');
		
		return ($icon) ? '<i class="'.esc_attr($icon.$class).'"></i>' : null;
		
	}
	
	/**
	* HTML element
	* @since: 1.6.0
	*/
	public function get_html_element($args = '', $class ='') {	
	
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		$html  = $this->base->getVar($args, 'html');
		
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

		return '<div class="'.esc_attr($class).'">'.wp_kses($html, $allowed).'</div>';
	
	}
	
	/**
	* Overlay markup
	* @since: 1.0.0
	*/
	public function get_overlay($class = '', $position = '') {
		
		$bg_skin = $this->grid_data['skin_overlay_background'];
		$bg_item = $this->item_colors['overlay']['background'];
		$background = ($bg_skin != $bg_item) ? ' style="background-color:'.esc_attr($bg_item).'"' : null;
		$position   = ($position) ? ' data-position="'.$position.'"' : null;
		
		$html  = '<div class="tg-item-overlay"'.$background.$position.'></div>';
		return $html;
		
	}
	
	/**
	* Get item format
	* @since: 1.0.0
	*/
	public function get_item_format() {
		
		$format = $this->base->getVar($this->grid_item, 'format');
		$format = (!empty($format) && in_array($format,$this->grid_data['items_format'])) ? $format : 'image';
		
		$image = $this->base->getVar($this->grid_item, 'image');
		$image = $this->base->getVar($image, 'url');
		if ($format == 'image' && !$image) {
			$format = 'standard';
		}
		
		return esc_attr($format);
	}
	
	/**
	* Get item data array
	* @since: 1.0.0
	*/
	public function get_item_data() {
		
		return $this->grid_item;
	}
	
	/**
	* Get item ID
	* @since: 1.0.0
	*/
	public function get_item_ID() {
		
		return esc_attr($this->base->getVar($this->grid_item, 'ID'));
	}
	
	/**
	* Get attachment url
	* @since: 1.0.0
	*/
	public function get_attachement_url() {
		
		return esc_url($this->base->getVar($this->grid_item['image'], 'url'));
	}
	
	/**
	* Get the permalink
	* @since: 1.0.0
	*/
	public function get_the_permalink() {
		
		return esc_url($this->base->getVar($this->grid_item, 'url'));
	}
	
	/**
	* Get the permalink target
	* @since: 1.0.0
	*/
	public function get_the_permalink_target() {
		
		return esc_attr($this->base->getVar($this->grid_item, 'url_target'));
	}
	
	/**
	* Get item metadata
	* @since: 1.0.0
	*/
	public function get_item_meta($meta_key = '', $class = '') {
		
		global $tg_skins_preview;
		
		if (!$tg_skins_preview && !empty($meta_key)) {
			$ID = $this->base->getVar($this->grid_item, 'ID');
			$meta_data  = $this->base->getVar($this->grid_item, 'meta_data');
			$meta_value = $this->base->getVar($meta_data, $meta_key);
			return $meta_value;
		} else {
			return '_metadata "'.$meta_key.'"';
		}
		
	}
	
	/**
	* The title
	* @since: 1.0.0
	*/
	public function the_title() {
	
		return esc_html($this->base->getVar($this->grid_item, 'title'));
	
	}

	/**
	* Get the title
	* @since: 1.0.0
	*/
	public function get_the_title($args = '', $class ='') {
		
		// retrieve title data
		$title  = $this->base->getVar($this->grid_item, 'title');
		$url    = $this->base->getVar($this->grid_item, 'url');
		$target = $this->base->getVar($this->grid_item, 'url_target');
		
		$title_tag   = $this->base->getVar($args, 'tag', 'h2');
		$title_link  = (!isset($args['link'])) ? true : $args['link'];
		$link_target = (!isset($args['target']) || empty($args['target'])) ? $target : $args['target'];
		
		if (!empty($title)) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
	
			$html  = '<'.$title_tag.' class="tg-item-title'.esc_attr($class).'">';
				$html .= (!empty($url) && $title_link) ? '<a href="'.esc_url($url).'" target="'.esc_attr($link_target).'">' : null;
					$html .= $title;
				$html .= (!empty($url) && $title_link) ? '</a>' : null;
			$html .= '</'.$title_tag.'>';
			
			return $html;
			
		}
	
	}
	
	/**
	* Get read more button
	* @since: 1.0.0
	*/
	public function get_read_more_button($args = '', $class ='') {
		
		$url = $this->base->getVar($this->grid_item, 'url');
		$url_target = $this->base->getVar($this->grid_item, 'url_target');
		
		if (!empty($url)) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
			// read more text
			$text = ($this->base->getVar($args, 'text', __( 'Read More', 'tg-text-domain' )));
			
			$output  = '<div class="tg-item-read-more">';
				$output .= '<a href="'.esc_url($url).'" target="'.esc_attr($url_target).'">';
					$output .= $text;
				$output .= '</a>';
			$output .= '</div>';
			
			return $output;
			
		}
		
	}
	
	/**
	* Get the excerpt
	* @since: 1.0.0
	*/
	public function the_excerpt() {

		return esc_html($this->base->getVar($this->grid_item, 'excerpt'));
	
	}
	
	/**
	* Get the excerpt
	* @since: 1.0.0
	*/
	public function get_the_excerpt($args = '', $class ='') {
		
		// retrieve excerpt data
		$excerpt = $this->base->getVar($this->grid_item, 'excerpt');
		$length  = $this->base->getVar($args, 'length', 240);
		$suffix  = $this->base->getVar($args, 'suffix', '...');
		
		if ($length > 0 && $excerpt) {
	
			$length++;
	
			if (mb_strlen($excerpt) > $length) {
				$subex   = mb_substr($excerpt, 0, $length - 5);
				$exwords = explode( ' ', $subex );
				$excut   = - (mb_strlen($exwords[count($exwords) - 1]));
				if ($excut < 0) {
					$excerpt = mb_substr($subex, 0, $excut);
				} else {
					$excerpt = $subex;
				}
			}
			
		}
		
		if (!empty($excerpt)) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
			return '<p class="tg-item-excerpt'.esc_attr($class).'">'.esc_html($excerpt.$suffix).'</p>';
			
		}
	
	}
	
	/**
	* the date
	* @since: 1.0.0
	*/
	public function the_date() {
		
		return esc_html($this->base->getVar($this->grid_item, 'date'));
	
	}
	
	/**
	* Get the date
	* @since: 1.0.0
	*/
	public function get_the_date($args = '', $class ='') {
		
		$date = $this->base->getVar($this->grid_item, 'date');
		
		if ($date) {
			
			// date format
			$date_format = $this->base->getVar($args, 'format');
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
		
			if ($this->grid_data['source_type'] != 'post_type' || $date_format == 'ago' ) {
				
				$date = sprintf( _x( '%s ago', '%s = human-readable time difference', 'tg-text-domain' ), human_time_diff($date, date_i18n('U')));
				
			} else {
				
				$date_format = ($date_format) ? $date_format : $this->grid_data['date_format'];
				$date = date_i18n($date_format, $date);
				
			}
			
			return '<span class="tg-item-date'.esc_attr($class).'">'.esc_html($date).'</span>';
			
		}
		
	}
	
	/**
	* Get social share button
	* @since: 1.0.0
	*/
	public function get_social_share_links() {
		
		if ($this->grid_data['source_type'] == 'post_type' && !in_array('attachment', $this->grid_data['post_type'])) {
		
			return array(
				'facebook'  => '<a href="#" class="tg-facebook"><i class="tg-icon-facebook"></i></a>',
				'twitter'   => '<a href="#" class="tg-twitter"><i class="tg-icon-twitter"></i></a>',
				'google+'   => '<a href="#" class="tg-google1"><i class="tg-icon-google-plus"></i></a>',
				'pinterest' => '<a href="#" class="tg-pinterest"><i class="tg-icon-pinterest"></i></a>'
			);
		
		}
		
	}
	
	/**
	* The terms
	* @since: 1.0.0
	*/
	public function the_terms() {
	
		return $this->base->getVar($this->grid_item, 'terms');
	
	}
	
	/**
	* Get the terms
	* @since: 1.0.0
	*/
	public function get_the_terms($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		// retrieve terms data
		$terms           = $this->base->getVar($this->grid_item, 'terms');
		$terms_link      = (!isset($args['link'])) ? true : $args['link'];
		$terms_color     = $this->base->getVar($args, 'color');
		$terms_separator = $this->base->getVar($args, 'separator');
		
		$cat = null;
			
		if (!empty($terms)) {
				
			global $wp_rewrite;
			
			$i = 0;
			$rel_attr  = (is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks()) ? 'rel="category tag"' : 'rel="category"';
					
			foreach ($terms as $term) {
						
				$color = null;
				if ($terms_color == 'background' || $terms_color == 'color') { 
					
					$term_color = $term['color'];
						
					if ($terms_color == 'background') {
						$brightness = $this->base->brightness($term_color);
						$color = ($brightness == 'bright') ? '#000000' : '#ffffff';
						$color = (!empty($term_color)) ? ' style="background:'.esc_attr($term_color).';color:'.esc_attr($color).'"' : null;
					} else {
						$color = (!empty($term_color)) ? ' style="color:'.esc_attr($term_color).'"' : null;
					}
	
				}
						
				if ($i > 0 && !empty($terms_separator)) {
					$cat .= '<span>'.esc_html($terms_separator).'</span>';
				}
				if ($terms_link) {
					$cat .= '<a class="'. esc_attr($term['taxonomy']) .'" href="' . esc_url($term['url']) . '" ' . $rel_attr  . '><span class="tg-item-term" '.$color.'>'. esc_html($term['name']) .'</span></a>';
				} else {
					$cat .= '<span class="tg-item-term '. esc_attr($term['taxonomy']) .'"'.$color.'>'. esc_html($term['name']) .'</span>';
				}
				++$i;
			}
		}
				
		if (!empty($cat)) {
			$html  = '<div class="tg-cats-holder'.esc_attr($class).'">';
				$html .= $cat;
			$html .= '</div>';
			return $html;
		}
	
	}
	
	/**
	* The comments number
	* @since: 1.0.0
	*/
	public function the_comments_number() {
		
		$cache_date = $this->base->getVar($this->grid_data, 'cache_date');
		if (!empty($cache_date) && $this->grid_data['source_type'] == 'post_type') {
			$item_ID = $this->get_item_ID();
			$comments_number = get_comments_number($item_ID);
		} else {
			$comments_number = $this->base->getVar($this->grid_item, 'comments_number', 0);
		}
		
		return esc_attr($this->base->shorten_number_format($comments_number));
		
	}
	
	/**
	* Get the comments number
	* @since: 1.0.0
	*/
	public function get_the_comments_number($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		// retrieve terms data
		$cache_date      = $this->base->getVar($this->grid_data, 'cache_date');
		$item_id         = $this->base->getVar($this->grid_item, 'ID');
		$url             = $this->base->getVar($this->grid_item, 'url');
		$url_target      = $this->base->getVar($this->grid_item, 'url_target');
		$comments_number = $this->base->getVar($this->grid_item, 'comments_number');
		$comments_number = (empty($cache_date)) ? $comments_number : get_comments_number($item_id);
		$comments_icon   = $this->base->getVar($args, 'icon');
	
		// translatable string
		$nonCom  = __( 'No comment', 'tg-text-domain' );
		$oneCom  = __( 'comment', 'tg-text-domain' );
		$sevCom  = __( 'Comments', 'tg-text-domain' );
		
		if (!$comments_icon) {
			
			if ($comments_number == 0) {
				$comments = $nonCom;
			} else if ($comments_number == 1) {
				$comments = $comments_number .' '. $oneCom;
			} else {
				$num_comments = $this->base->shorten_number_format($comments_number);
				$comments = $comments_number .' '. $sevCom;
			}
			$comments = '<a class="tg-item-comment'.esc_attr($class).'" href="'.esc_url($url).'"  target="'.esc_attr($url_target).'">'.esc_html($comments).'</a>';
			
		} else {
			
			$comments_number = $this->base->shorten_number_format($comments_number);
			$comments = '<a class="tg-item-comment'.esc_attr($class).'" href="'.esc_url($url).'"  target="'.esc_attr($url_target).'">'.$comments_icon.'<span>'.esc_html($comments_number).'</span></a>';
			
		}
		
		return $comments;
		
	}
	
	/**
	* The likes number
	* @since: 1.0.0
	*/
	public function the_likes_number() {
		
		$cache_date   = $this->base->getVar($this->grid_data, 'cache_date');
		$source_type  = $this->base->getVar($this->grid_data, 'source_type');
		$likes_number = $this->base->getVar($this->grid_item, 'likes_number');
		
		if ($source_type == 'post_type' && !is_numeric($likes_number)) {
			if (empty($cache_date)) {
				$meta_data = $this->base->getVar($this->grid_item, 'meta_data');
				$likes_number = $this->base->getVar($meta_data, '_post_like_count');
			} else {
				$ID = $this->base->getVar($this->grid_item, 'ID');
				$likes_number = get_post_meta($ID, '_post_like_count', true);
			}
		}
		
		return esc_attr($likes_number);
	
	}
	
	/**
	* Get the likes number
	* @since: 1.0.0
	*/
	public function get_the_likes_number($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$source_type = $this->base->getVar($this->grid_data, 'source_type');
		$likes = $this->base->getVar($this->grid_item, 'likes_number');
		
		if ($source_type == 'post_type' && !is_numeric($likes)) {
			
			$cache_date = $this->base->getVar($this->grid_data, 'cache_date');
			if (empty($cache_date)) {
				$output = str_replace('to-post-like', 'to-post-like'.$class, $likes);
			} else {
				$ID = $this->base->getVar($this->grid_item, 'ID');
				$output = str_replace('to-post-like', 'to-post-like'.$class, TO_get_post_like($ID));
			}
			
		} else {
			
			$url = $this->base->getVar($this->grid_item, 'url');

			$heart = '<span class="to-heart-icon">';
				$heart .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 64 64">';
					$heart .= '<g transform="translate(0, 0)">';
						$heart .= '<path stroke-width="6" stroke-linecap="square" stroke-miterlimit="10" d="M1,21c0,20,31,38,31,38s31-18,31-38 c0-8.285-6-16-15-16c-8.285,0-16,5.715-16,14c0-8.285-7.715-14-16-14C7,5,1,12.715,1,21z"></path>';
					$heart .= '</g>';
				$heart .= '</svg>';
			$heart .= '</span>';
			
			$title  = $this->base->getVar($this->grid_item, 'likes_title');
			$title  = ($title) ? ' title="'.esc_attr($title).'"' : null;
			$target = $this->base->getVar($this->grid_item, 'url_target');
			
			$output = '<span class="no-ajaxy to-post-like to-post-like-unactive  empty-heart'.esc_attr($class).'"'.$title.'>';
				$output .= ($url && $source_type != 'post_type') ? '<a href="'.esc_url($url).'" target="'.esc_attr($target).'">' : null;
					$output .= $heart;
					$output .= '<span class="to-like-count">';
						$output .= esc_attr($this->base->shorten_number_format($likes));
					$output .= '</span>';
				$output .= ($url && $source_type != 'post_type') ? '</a>' : null;
			$output .= '</span>';
		
		}
		
		return $output;
	}
	
	/**
	* The duration
	* @since: 1.0.0
	*/
	public function the_duration() {
	
		$video_data = $this->base->getVar($this->grid_item, 'video');
		$duration   = $this->base->getVar($video_data, 'duration');
		return esc_attr($duration);
	
	}
	
	/**
	* Get the duration
	* @since: 1.0.0
	*/
	public function get_the_duration($args = '', $class ='') {
		
		$video_data = $this->base->getVar($this->grid_item, 'video');
		$duration   = $this->base->getVar($video_data, 'duration');
		
		if (!empty($duration)) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
			
			return '<div class="tg-item-duration'.esc_attr($class).'">'.esc_html($duration).'</div>';
			
		}
		
	}
	
	/**
	* The views number
	* @since: 1.0.0
	*/
	public function the_views_number() {
		
		return eac_attr($this->base->getVar($this->grid_item, 'views_number'));
		
	}
	
	/**
	* Get the views number
	* @since: 1.0.0
	*/
	public function get_the_views_number($args = '', $class ='') {
		
		$views = $this->base->getVar($this->grid_item, 'views_number');
		
		if (!empty($views)) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
			
			$views = $this->base->shorten_number_format($views);
			return '<span class="tg-item-views'.esc_attr($class).'">'.esc_html($views).' '.__( 'views', 'tg-text-domain' ) .'</span>';
			
		}
		
	}
	
	/**
	* The author
	* @since: 1.0.0
	*/
	public function the_author() {
		
		return esc_html($this->base->getVar($this->grid_item, 'author'));
		
	}
	
	/**
	* Get the author
	* @since: 1.0.0
	*/
	public function get_the_author($args = '', $class ='') {
		
		$author        = $this->base->getVar($this->grid_item, 'author');
		$author_prefix = $this->base->getVar($args, 'prefix');
		$author_name   = $this->base->getVar($author, 'name');
		$author_url    = $this->base->getVar($author, 'url');
		$author_avatar = ((int)$this->base->getVar($args, 'avatar', false) == true) ? $this->base->getVar($author, 'avatar') : null;
		$url_target    = $this->base->getVar($this->grid_item, 'url_target');
		
		if ($author_name) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
			
			$output  = ($author_avatar) ? '<div class="tg-item-author-holder'.esc_attr($class).'">' : null;
				$output .= ($author_avatar) ? '<span class="tg-item-avatar"><img src="'.esc_url($author_avatar).'"/></span>' : null;
				$output .= '<span class="tg-item-author'.((!$author_avatar) ? esc_attr($class) : null).'">';
					$output .= ($author_prefix) ? '<span>'.esc_html($author_prefix).'</span>' : null;
					$output .= ($author_url) ? '<a href="'. esc_url($author_url) .'" target="'.esc_attr($url_target).'">' : null;
					$output .= (!$author_url) ? '<span class="tg-item-author-name">' : null;
						$output .= $author_name;
					$output .= (!$author_url) ? '</span>' : null;
					$output .= ($author_url) ? '</a>' : null;
				$output .= '</span>';
			$output .= ($author_avatar) ? '</div>' : null;
			
			return $output;
			
		}
		
	}
	
	/**
	* Get quote markup
	* @since: 1.0.0
	*/
	public function get_the_quote_format() {
		
		$url = $this->base->getVar($this->grid_item, 'url');
		$url_target = $this->base->getVar($this->grid_item, 'url_target');
		
		$source = $this->base->getVar($this->grid_item['quote'], 'source');
		$quote  = $this->base->getVar($source, 'content');
		$author = $this->base->getVar($source, 'author');
		
		if ($quote) {
			$output  = '<h2 class="tg-quote-content tg-item-title"><a href="'.esc_url($url).'" target="'.esc_attr($url_target).'">'.esc_html($quote).'</a></h2>';
			$output .= (!empty($author)) ? '<span class="tg-quote-author">'.esc_html($author).'</span>' : null;
			return $output;
		}
		
	}
	
	/**
	* Get link markup
	* @since: 1.0.0
	*/
	public function get_the_link_format() {
		
		$url_item   = $this->base->getVar($this->grid_item, 'url');
		$url_target = $this->base->getVar($this->grid_item, 'url_target');
		
		$link    = $this->base->getVar($this->grid_item,'link');
		$source  = $this->base->getVar($link,'source');
		$content = $this->base->getVar($source, 'content');
		$url     = $this->base->getVar($source, 'url');
		
		if ($link) {
			$output  = '<h2 class="tg-link-content tg-item-title"><a href="'.esc_url($url_item).'" target="'.esc_attr($url_target).'">'.esc_html($content).'</a></h2>';
			$output .= (!empty($url)) ? '<a class="tg-link-url" href="'.esc_url($url_item).'" target="'.esc_attr($url_target).'">'.esc_url($url).'</a>' : null;
			return $output;
		}
		
	}
	
	/**
	* Get Woocommerce price
	* @since: 1.6.0
	*/
	public function get_product_price($args = '', $class ='') {

		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product = $this->base->getVar($this->grid_item, 'product');
		$price   = $this->base->getVar($product, 'price');
		
		return ($price) ? '<div class="tg-item-price'.esc_attr($class).'">'.$price.'</div>' : null;
		
	}
	
	/**
	* Get Woocommerce full price
	* @since: 1.6.0
	*/
	public function get_product_full_price($args = '', $class ='') {

		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product = $this->base->getVar($this->grid_item, 'product');
		$price   = $this->base->getVar($product, 'full_price');
		
		return ($price) ? '<div class="tg-item-price'.esc_attr($class).'">'.$price.'</div>' : null;
		
	}
	
	/**
	* Get Woocommerce regular price
	* @since: 1.6.0
	*/
	public function get_product_regular_price($args = '', $class ='') {

		// prepare additional class
		$class = ($class) ? ' '.$class : null;

		$product = $this->base->getVar($this->grid_item, 'product');
		$price   = $this->base->getVar($product, 'regular_price');
		
		return ($price) ? '<div class="tg-item-price'.esc_attr($class).'">'.$price.'</div>' : null;
		
	}
	
	/**
	* Get Woocommerce sale price
	* @since: 1.6.0
	*/
	public function get_product_sale_price($args = '', $class ='') {

		// prepare additional class
		$class = ($class) ? ' '.$class : null;

		$product = $this->base->getVar($this->grid_item, 'product');
		$price   = $this->base->getVar($product, 'sale_price');
		
		return ($price) ? '<div class="tg-item-price'.esc_attr($class).'">'.$price.'</div>' : null;
		
	}
	
	/**
	* Get Woocommerce star rating
	* @since: 1.0.0
	*/
	public function get_product_rating($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product = $this->base->getVar($this->grid_item, 'product');
		$rating  = $this->base->getVar($product, 'rating');
		
		return ($rating) ? '<div class="tg-item-rating'.esc_attr($class).'">'.$rating.'</div>' : null;
		
	}
	
	/**
	* Get Woocommerce text rating
	* @since: 1.6.0
	*/
	public function get_product_text_rating($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product = $this->base->getVar($this->grid_item, 'product');
		$rating  = $this->base->getVar($product, 'text_rating');
		
		return ($rating) ? '<span class="tg-item-text-rating'.esc_attr($class).'">'.$rating.'</span>' : null;
		
	}
	
	/**
	* Get Woocommerce sale status
	* @since: 1.0.0
	*/
	public function get_product_on_sale($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product = $this->base->getVar($this->grid_item, 'product');
		$on_sale = $this->base->getVar($product, 'on_sale');
		
		return ($on_sale) ? '<div class="tg-item-on-sale light'.esc_attr($class).'">'.$on_sale.'</div>' : null;
		
	}
	
	/**
	* Get Woocommerce add to cart url
	* @since: 1.6.0
	*/
	public function get_product_add_to_cart_url($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product   = $this->base->getVar($this->grid_item, 'product');
		$cart_url  = $this->base->getVar($product, 'add_to_cart_url');
		$cart_text = $this->base->getVar($args, 'text', __( 'Add to Cart', 'tg-text-domain' ));
		
		return ($cart_url) ? '<a class="tg-item-add-to-cart-url'.esc_attr($class).'" href="'.esc_url($cart_url).'">'.esc_html($cart_text).'</a>' : null;
		
	}
	
	/**
	* Get Woocommerce add to cart button
	* @since: 1.0.0
	*/
	public function get_product_cart_button($args = '', $class ='') {
		
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$product = $this->base->getVar($this->grid_item, 'product');
		$cart_button = $this->base->getVar($product, 'cart_button');
		
		if ($this->base->getVar($args, 'cart_icon')) {
			$icon_simple = $this->base->getVar($args, 'icon_simple', '<i class="tg-icon-shop-bag"></i>');
			$icon_variation = $this->base->getVar($args, 'icon_variable', '<i class="tg-icon-settings"></i>');
			$cart_icon = (strpos($cart_button, 'product_type_simple') !== false) ? $icon_simple : $icon_variation;
			$cart_button = ($cart_icon) ? preg_replace('#(<a.*?>).*?(</a>)#', '$1'.$cart_icon.'$2', $cart_button) : $cart_button;
		}
		
		return ($cart_button) ? '<div class="tg-item-cart-button'.esc_attr($class).'">'.$cart_button.'</div>' : null;
		
	}
	
	/**
	* Retrieve Woocommerce YITH Whislist
	* @since: 1.0.0
	*/
	public function get_product_wishlist() {
		
		$product = $this->base->getVar($this->grid_item, 'product');
		return $this->base->getVar($product, 'wishlist');
	
	}

	/**
	* Get media content (image/gallery/audio/video)
	* @since: 1.0.0
	*/
	public function get_media() {
		
		// get item format
		$format = $this->get_item_format();

		// get media content depending on of format
		switch ($format) {
			case 'gallery':
				$content = $this->gallery_markup();
				break;
			case 'audio':
				$content = $this->audio_markup();
				break;
			case 'video':
				$content = $this->video_type();
				break;
			default:
				$content = $this->image_markup();
				break;
		}

		return $content;

	}
	
	/**
	* Search for the right video data
	* @since: 1.0.0
	*/
	public function video_type() {
		
		$type   = $this->base->getVar($this->grid_item['video'], 'type');
		$format = (!$this->grid_data['video_lightbox']) ? $type : null;

		switch ($format) {
			case 'youtube':
				$content = $this->youtube_markup();
				break;
			case 'vimeo':
				$content = $this->vimeo_markup();
				break;
			case 'wistia':
				$content = $this->wistia_markup();
				break;
			case 'video':
				$content = $this->video_markup();
				break;
			default:
				$content = $this->image_markup();
				break;
		}
		
		return $content;
		
	}
	
	/**
	* Image markup
	* @since: 1.0.0
	*/
	public function image_markup() {
		
		$url         = $this->base->getVar($this->grid_item['image'], 'url');
		$source_type = $this->grid_data['source_type'];
		$grid_style  = $this->grid_data['style'];
		$lightbox    = $this->grid_data['video_lightbox'];

		if (!empty($url)) {;
			if ($grid_style == 'masonry' && $lightbox && in_array($source_type, array('youtube','vimeo','wistia'))) {
				$output  = '<div class="tg-item-media-inner" data-ratio="16:9"></div>';
				$output .= $this->get_media_poster();
			} else if ($grid_style == 'grid') {
				$output = '<div class="tg-item-image" style="background-image: url('.esc_url($url).')"></div>';
			} else {
				$alt    = $this->base->getVar($this->grid_item['image'], 'alt');
				$width  = $this->base->getVar($this->grid_item['image'], 'width');
				$height = $this->base->getVar($this->grid_item['image'], 'height');
				$output = '<img class="tg-item-image" alt="'.esc_attr($alt).'" width="'.esc_attr($width).'" height="'.esc_attr($height).'" src="'.esc_url($url).'">';
			}
			
			// add woocommerce first gallery image if product
			$product_image = $this->base->getVar($this->grid_item, 'product_image');
			$product_image = $this->base->getVar($product_image, 'url');
			if ($product_image) {
				$output .= '<div class="tg-item-image tg-alternative-product-image" style="background-image: url('.esc_url($product_image).')"></div>';
			}
			
			return $output;
		
		}

	}
	
	/**
	* Gallery markup
	* @since: 1.0.0
	*/
	public function gallery_markup() {
		
		
		$images    = $this->base->getVar($this->grid_item, 'gallery');
		$style     = $this->grid_data['style'];
		$slideshow = $this->grid_data['gallery_slide_show'];
		
		if ($images) {
			
			$class  = ' first-image show';
			$output = '<div class="tg-item-gallery-holder">';
			
			if ($style == 'grid') {
				foreach($images as $image) {
					if ($class || ($slideshow && !$class)) {
						$output .= '<div class="tg-item-image'.esc_attr($class).'" style="background-image: url('.esc_url($image['url']).')"></div>';
					}
					$class = null;
				}
			} else {
				
				foreach($images as $image) {
					if ($class) {	
						$output .= '<img class="tg-item-image'.esc_attr($class).'" alt="'.esc_attr($image['alt']).'" width="'.esc_attr($image['width']).'" height="'. esc_attr($image['height']).'" src="'.esc_url($image['url']).'">';
					} else if ($slideshow) {
						$output .= '<div class="tg-item-image" style="background-image: url('.esc_url($image['url']).')"></div>';
					}
					$class = null;
				}
			}
			
			$output .= '</div>';
			return $output;
		
		}
		
	}
	
	/**
	* Audio markup
	* @since: 1.0.0
	*/
	public function audio_markup() {
		
		$type = $this->base->getVar($this->grid_item['audio'], 'type');
		
		if ($type == 'soundcloud') {
			return $this->soundcloud_markup();
		} else {
			return $this->html_audio_markup();
		}
	
	}
	
	/**
	* SoundCloud markup
	* @since: 1.0.0
	*/
	public function soundcloud_markup() {
		
		$class  = ($this->base->getVar($this->grid_item['image'],'url')) ? ' has-media-poster' : null;
		$source = $this->base->getVar($this->grid_item['audio'],'source',array());
		$SC_ID  = $this->base->getVar($source,'ID');
		
		if ($SC_ID) {
			$SC_URL  = '//w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.esc_attr($SC_ID).'&amp;auto_play=false&amp;hide_related=true&amp;visual=true&amp;show_artwork=true&amp;color=white_transparent';
			$output  = '<div class="tg-item-media-inner'.esc_attr($class).'" data-ratio="4:3">';
			$output .= '<iframe id="SC-'.uniqid().'" class="tg-item-soundcloud tg-item-media" data-api="1" src="about:blank" data-src="'.esc_url($SC_URL).'"></iframe>';
			$output .= '</div>';
			$output .= '<div class="tg-item-media-poster tg-item-media-soundcloud tg-item-button-play"></div>';
			$output .= $this->get_media_poster();
			return $output;
		}
		
	}
	
	/**
	* HTML audio markup
	* @since: 1.0.0
	*/
	public function html_audio_markup() {
		
		$class   = ($this->base->getVar($this->grid_item['image'],'url')) ? ' has-media-poster' : null;
		$sources = $this->base->getVar($this->grid_item['audio'],'source');
		
		if ($sources) {
			$output  = '<div class="tg-item-media-inner'.esc_attr($class).'">';
			$output .= '<audio class="tg-item-audio-player tg-item-media" controls preload="none">';
				foreach($sources as $type => $src ){
					if (in_array($type,array('mp3','ogg')) && !empty($src)) {
						$output .= '<source src="'.esc_url($src).'" type="audio/'.esc_attr($type).'">';  
					}
				}
			$output .= '</audio>';
			$output .= $this->get_media_poster();
			$output .= '</div>';
			return $output;
		}
		
	}

	/**
	* Youtube markup
	* @since: 1.0.0
	*/
	public function youtube_markup() {
		
		$class  = ($this->base->getVar($this->grid_item['image'],'url')) ? ' has-media-poster' : null;
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		$YT_ID  = $this->base->getVar($source,'ID');
		
		if ($YT_ID) {
			$YT_URL  = 'https://www.youtube.com/embed/'.esc_attr($YT_ID).'?version=3&amp;enablejsapi=1&amp;html5=1&amp;controls=1&amp;autohide=1&amp;rel=0&amp;showinfo=0';
			$ratio   = $this->base->getVar($this->grid_item['meta_data'], 'the_grid_item_youtube_ratio', '16:9');
			$output  = '<div class="tg-item-media-inner'.esc_attr($class).'" data-ratio="'.esc_attr($ratio).'">';
			$output .= '<iframe class="tg-item-youtube tg-item-media" src="about:blank" data-src="'.esc_url($YT_URL).'" data-api="1" id="YT-'.uniqid().'" allowfullscreen></iframe>';
			$output .= '</div>';
			$output .= $this->get_media_poster();
			return $output;
		}
		
	}

	/**
	* Vimeo markup
	* @since: 1.0.0
	*/
	public function vimeo_markup() {
		
		$class  = ($this->base->getVar($this->grid_item['image'],'url')) ? ' has-media-poster' : null;
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		$VM_ID  = $this->base->getVar($source,'ID');
		
		if ($VM_ID) {
			$HTML_ID = 'VM-'.uniqid();
			$VM_URL  = 'https://player.vimeo.com/video/'.esc_attr($VM_ID).'?title=0&amp;byline=0&amp;portrait=0&amp;api=1&amp;player_id='.$HTML_ID;
			$ratio   = $this->base->getVar($this->grid_item['meta_data'], 'the_grid_item_vimeo_ratio', '16:9');
			$output  = '<div class="tg-item-media-inner'.esc_attr($class).'" data-ratio="'.esc_attr($ratio).'">';
			$output .= '<iframe class="tg-item-vimeo tg-item-media" src="about:blank" data-src="'.esc_url($VM_URL).'" data-api="1" id="'.$HTML_ID.'" allowfullscreen></iframe>';
			$output .= '</div>';
			$output .= $this->get_media_poster();
			return $output;
		}
		
	}
	
	/**
	* Wistia markup
	* @since: 1.0.7
	*/
	public function wistia_markup() {
		
		$class  = ($this->base->getVar($this->grid_item['image'],'url')) ? ' has-media-poster' : null;
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		$WT_ID  = $this->base->getVar($source,'ID');
		
		if ($WT_ID) {
			$WT_URL  = 'https://fast.wistia.net/embed/iframe/'.esc_attr($WT_ID).'?version=3&enablejsapi=1&html5=1&controls=1&autohide=1&rel=0&showinfo=0';
			$ratio   = $this->base->getVar($this->grid_item['meta_data'], 'the_grid_item_wistia_ratio', '16:9');
			$output  = '<div class="tg-item-media-inner'.esc_attr($class).'" data-ratio="'.esc_attr($ratio).'">';
			$output .= '<iframe class="tg-item-wistia wistia_embed tg-item-media" src="about:blank" data-src="'.esc_url($WT_URL).'" data-api="1" id="WT-'.uniqid().'" allowfullscreen></iframe>';
			$output .= '</div>';
			$output .= $this->get_media_poster();
			return $output;
		
		}
	}
	
	/**
	* Video markup
	* @since: 1.0.0
	*/
	public function video_markup() {
		
		$class  = ($this->base->getVar($this->grid_item['image'],'url')) ? ' has-media-poster' : null;
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		
		if ($source) {
			$poster_url = $this->base->getVar($this->grid_item['image'],'url');
			$ratio   = $this->base->getVar($this->grid_item['meta_data'], 'the_grid_item_video_ratio', '16:9');
			$output  = '<div class="tg-item-media-inner'.esc_attr($class).'" data-ratio="'.esc_attr($ratio).'">';
			$output .= '<video class="tg-item-video-player tg-item-media" poster="'.esc_url($poster_url).'" controls preload="none" style="width:100%;height:100%">';
			foreach($source as $type => $src ){
				if (in_array($type,array('mp4','webm','ogv')) && !empty($src)) {
					$output .= '<source src="'.esc_url($src).'" type="video/'.esc_attr($type).'">';  
				}
			} 
			$output .= '</video>';
			$output .= '</div>';
			$output .= $this->get_media_poster();
			return $output;
		}
		
	}
	
	/**
	* Media poster markup
	* @since: 1.0.0
	*/
	public function get_media_poster() {
		
		$poster_url  = $this->base->getVar($this->grid_item['image'],'url');
		$item_format = $this->base->getVar($this->grid_item, 'format');
		$audio_type  = $this->base->getVar($this->grid_item['audio'], 'type');
		$grid_style  = $this->grid_data['style'];

		if ($poster_url) {
			
			if ($grid_style == 'masonry' && $item_format == 'audio' && $audio_type == 'audio') {
				$alt    = $this->base->getVar($this->grid_item['image'], 'alt');
				$width  = $this->base->getVar($this->grid_item['image'], 'width');
				$height = $this->base->getVar($this->grid_item['image'], 'height');
				return '<img class="tg-item-audio-poster" width="'.esc_attr($width).'" height="'.esc_attr($height).'" alt="'.esc_attr($alt).'" src="'.esc_url($poster_url).'">';
			} else if ($item_format == 'audio' && $audio_type == 'audio') {
				return '<div class="tg-item-audio-poster" style="background-image: url('.esc_url($poster_url).')"></div>';
			} else {
				return '<div class="tg-item-media-poster" style="background-image: url('.esc_url($poster_url).')"></div>';
			}
			
		}	
	
	}
	
	/**
	* Get link button
	* @since: 1.0.0
	*/
	public function get_link_button($args = '', $class ='') {	
	
		// prepare additional class
		$class = ($class) ? ' '.$class : null;
		
		$icon = $this->base->getVar($args, 'icon', '<i class="tg-icon-link"></i>');
	
		$url = $this->base->getVar($this->grid_item, 'url');
		$url_target = $this->base->getVar($this->grid_item, 'url_target');
		
		if (!empty($url)) {
			return '<a class="tg-link-button'.esc_attr($class).'" href="'.esc_url($url).'" target="'.esc_attr($url_target).'">'.$icon.'</a>';
		}
		
	}
	
	/**
	* Get lightbox markup fo each media type and for each lighbox plugins
	* @since: 1.0.0
	*/
	public function get_media_button($args = '', $class = '') {

		$format         = $this->get_item_format();
		$grid_style     = $this->grid_data['style'];
		$video_lightbox = $this->grid_data['video_lightbox'];
		$media_poster   = $this->base->getVar($this->grid_item['image'],'url');

		// Get media type
		$media_type = ($format == 'video') ? $this->grid_item['video']['type'] : $format;
		$media_type = ($format == 'audio') ? $this->grid_item['audio']['type'] : $media_type;

		if ($media_poster || $media_type == 'gallery' || (in_array($grid_style, array('grid', 'justified')) && in_array($media_type, array('video', 'audio')))) {
			
			// prepare additional class
			$class = ($class) ? ' '.$class : null;
			
			// get custom icons or default icons
			$icons = $this->base->getVar($args, 'icons');
			$icons = array (
				'image' => $this->base->getVar($icons, 'image', '<i class="tg-icon-add"></i>'),
				'audio' => $this->base->getVar($icons, 'audio', '<i class="tg-icon-play"></i>'),
				'video' => $this->base->getVar($icons, 'video', '<i class="tg-icon-play"></i>')
			);

			switch ($media_type) {
				case 'youtube':
					if ($video_lightbox) {
						$output = $this->get_youtube_lightbox($icons['video'], $class);
					} else {
						$output = '<div class="tg-media-button tg-item-button-play'.esc_attr($class).'">'.$icons['video'].'</div>';
					}
					break;
				case 'vimeo':
					if ($video_lightbox) {
						$output = $this->get_vimeo_lightbox($icons['video'], $class);
					} else {
						$output = '<div class="tg-media-button tg-item-button-play'.esc_attr($class).'">'.$icons['video'].'</div>';
					}
					break;
				case 'wistia':
					if ($video_lightbox) {
						$output = $this->get_wistia_lightbox($icons['video'], $class);
					} else {
						$output = '<div class="tg-media-button tg-item-button-play'.esc_attr($class).'">'.$icons['video'].'</div>';
					}
					break;
				case 'video':
					if ($video_lightbox) {
						$output = $this->get_video_lightbox($icons['video'], $class);
					} else {
						$output = '<div class="tg-media-button tg-item-button-play'.esc_attr($class).'">'.$icons['video'].'</div>';
					}
					break;
				case 'soundcloud':
					$output = '<div class="tg-media-button tg-item-button-play'.esc_attr($class).'">'.$icons['audio'].'</div>';
					break;
				case 'audio':
					$output = '<div class="tg-media-button tg-item-button-play'.esc_attr($class).'">'.$icons['audio'].'</div>';
					break;
				case 'gallery':
					$output = $this->get_gallery_lightbox($icons['image'], $class);
					break;
				default:
					$output = $this->get_image_lightbox($icons['image'], $class);
					break;
			}
				
			return $output;
		
		}
	
	}
	
	/**
	* Get Youtube lightbox markup
	* @since: 1.0.0
	*/
	public function get_youtube_lightbox($icon, $class) {
		
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		$YT_ID  = $this->base->getVar($source,'ID');
		
		if ($YT_ID) {
			
			$lightbox_type = $this->grid_data['lightbox_type'];
		
			switch ($lightbox_type) {
				case 'prettyphoto':
					return '<a class="tg-media-button'.esc_attr($class).'" href="//www.youtube.com/watch?v='.esc_attr($YT_ID).'" rel="prettyPhoto[pp_gal]" title="">'.$icon.'</a>';
					break;
				case 'fancybox':
					return '<a class="tg-media-button fancybox iframe'.esc_attr($class).'" rel="tg_group" href="//www.youtube.com/embed/'.esc_attr($YT_ID).'?autoplay=0&wmode=opaque">'.$icon.'</a>';
					break;
				case 'foobox':
					return '<a class="tg-media-button foobox'.esc_attr($class).'" href="//www.youtube.com/embed/'.esc_attr($YT_ID).'?autoplay=0&wmode=opaque" rel="foobox">'.$icon.'</a>';
					break;
				case 'the_grid':
					return '<div class="tg-media-button'.esc_attr($class).'" data-tolb-src="'.esc_attr($YT_ID).'" data-tolb-type="youtube" data-tolb-alt="">'.$icon.'</div>';
					break;
			}
		
		}

	}
	
	/**
	* Get Vimeo lightbox markup
	* @since: 1.0.0
	*/
	public function get_vimeo_lightbox($icon, $class) {
		
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		$VM_ID  = $this->base->getVar($source,'ID');
		
		if ($VM_ID) {
			
			$lightbox_type = $this->grid_data['lightbox_type'];
		
			switch ($lightbox_type) {
				case 'prettyphoto':
					return '<a class="tg-media-button'.esc_attr($class).'" href="https://vimeo.com/'.esc_attr($VM_ID).'" rel="prettyPhoto[pp_gal]">'.$icon.'</a>';
					break;
				case 'fancybox':
					return '<a class="tg-media-button fancybox iframe'.esc_attr($class).'" rel="tg_group" href="//player.vimeo.com/video/'.esc_attr($VM_ID).'">'.$icon.'</a>';
					break;
				case 'foobox':
					return '<a class="tg-media-button foobox'.esc_attr($class).'" href="//vimeo.com/'.esc_attr($VM_ID).'" rel="foobox">'.$icon.'</a>';
					break;
				case 'the_grid':
					return '<div class="tg-media-button'.esc_attr($class).'" data-tolb-src="'.esc_attr($VM_ID).'" data-tolb-type="vimeo" data-tolb-alt="">'.$icon.'</div>';
					break;
			}
		
		}
		
	}
	
	/**
	* Get Wistia lightbox markup
	* @since: 1.0.7
	*/
	public function get_wistia_lightbox($icon, $class) {
		
		$source = $this->base->getVar($this->grid_item['video'],'source',array());
		$WT_ID  = $this->base->getVar($source,'ID');
		
		if ($WT_ID) {
			
			$lightbox_type = $this->grid_data['lightbox_type'];

			switch ($lightbox_type) {
				case 'prettyphoto':
					$HTML_ID = 'V'.uniqid();
					$output  = '<a class="tg-media-button'.esc_attr($class).'" href="#'.esc_attr($HTML_ID).'" rel="prettyPhoto[pp_gal]">'.$icon.'</a>';
					$output .= '<div style="display:none" id="'.$HTML_ID.'">';
					$output .= '<iframe src="//fast.wistia.net/embed/iframe/'.esc_attr($WT_ID).'?videoFoam=true" width="500" height="344" frameborder="no" class="wistia_embed" name="wistia_embed"></iframe>';
					$output .= '</div>';
					return $output;
					break;
				case 'fancybox':
					return '<a class="tg-media-button fancybox iframe'.esc_attr($class).'" rel="tg_group" href="//fast.wistia.net/embed/iframe/'.esc_attr($WT_ID).'">'.$icon.'</a>';
					break;
				case 'foobox':
					return '<a class="tg-media-button foobox'.esc_attr($class).'" href="//fast.wistia.net/embed/iframe/'.esc_attr($WT_ID).'" rel="foobox">'.$icon.'</a>';
					break;
				case 'the_grid':
					return '<div class="tg-media-button'.esc_attr($class).'" data-tolb-src="'.esc_attr($WT_ID).'" data-tolb-type="wistia" data-tolb-alt="">'.$icon.'</div>';
					break;
			}
			
		}
		
	}
	
	/**
	* Get html5 video lightbox markup
	* @since: 1.0.0
	*/
	public function get_video_lightbox($icon, $class) {
		
		$mp4  = (isset($this->grid_item['video']['source']['mp4'])  && !empty($this->grid_item['video']['source']['mp4']))  ? esc_url($this->grid_item['video']['source']['mp4'])  : null;
		$ogv  = (isset($this->grid_item['video']['source']['ogv'])  && !empty($this->grid_item['video']['source']['ogv']))  ? esc_url($this->grid_item['video']['source']['ogv'])  : null;
		$webm = (isset($this->grid_item['video']['source']['webm']) && !empty($this->grid_item['video']['source']['webm'])) ? esc_url($this->grid_item['video']['source']['webm']) : null;
		
		if ($mp4 || $ogv || $webm) {
			
			$lightbox_type = $this->grid_data['lightbox_type'];

			switch ($lightbox_type) {
				case 'prettyphoto':
					$HTML_ID = 'V'.uniqid();
					$output  = '<a class="tg-media-button'.esc_attr($class).'" href="#'.esc_attr($HTML_ID).'" rel="prettyPhoto[pp_gal]">'.$icon.'</a>';
					$output .= '<div style="display:none" id="'.$HTML_ID.'">';
					$output .= '<video controls style="height:280px;width:500px">';
					$output .= ($mp4)  ? '<source src="'.esc_url($mp4).'" type="video/mp4">'   : null;
					$output .= ($webm) ? '<source src="'.esc_url($webm).'" type="video/webm">' : null;
					$output .= ($ogv)  ? '<source src="'.esc_url($ogv).'" type="video/ogg">'   : null;
					$output .= '</video>';
					$output .= '</div>';
					return $output;
					break;
				case 'fancybox':
					$video = ($mp4) ? $mp4 : null;
					$video = (!$video) ? $ogv : $video;
					$video = (!$video) ? $webm : $video;
					return '<a class="tg-media-button fancybox iframe'.esc_attr($class).'" rel="tg_group" href="'.$video.'">'.$icon.'</a>';		
					break;
				case 'foobox':
					$video = ($mp4) ? esc_url($mp4) : null;
					$video = ($video && $ogv) ? $video.','.esc_url($ogv) : $video.esc_url($ogv);
					$video = ($video && $webm) ? $video.','.esc_url($webm) : $video.esc_url($webm);
					return '<a class="tg-media-button foobox'.esc_attr($class).'" href="'.$video.'" rel="foobox">'.$icon.'</a>';
					break;
				case 'the_grid':
					$source = array();
					$mp4    = ($mp4)  ? array_push($source, '[{"type":"mp4","source":"'.esc_url($mp4).'"}]')   : null;
					$ogv    = ($ogv)  ? array_push($source, '[{"type":"ogg","source":"'.esc_url($ogv).'"}]')   : null;
					$webm   = ($webm) ? array_push($source, '[{"type":"webm","source":"'.esc_url($webm).'"}]') : null;
					$source = ($source) ? implode(',', $source) : null;
					$poster_url = $this->base->getVar($this->grid_item['image'], 'url');
					$poster = ($poster_url) ? ' data-tolb-poster="'.esc_url($poster_url).'"' : null;
					return ($source) ? '<div class="tg-media-button'.esc_attr($class).'" data-tolb-src=\'['.$source.']\' data-tolb-type="'.esc_attr($this->grid_item['format']).'" data-tolb-alt=""'.$poster.'>'.$icon.'</div>' : null;
					break;
			}

		}

	}
	
	/**
	* Get gallery lightbox markup
	* @since: 1.0.0
	*/
	public function get_gallery_lightbox($icon, $class) {

		$gallery = $this->base->getVar($this->grid_item, 'gallery');

		if ($gallery) {
			
			$lightbox_type = $this->grid_data['lightbox_type'];
			
			$output = null;
		
			switch ($lightbox_type) {
				case 'prettyphoto':
					for ($i = 0; $i < count($gallery); $i++) {
						$image = $gallery[$i]['lb_url'];
						$title = $gallery[$i]['title'];
						$alt   = $gallery[$i]['alt'];
						$alt    = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
						if ($i == 0) {
							$output .= '<a class="tg-media-button'.esc_attr($class).'" href="'.esc_url($image).'" rel="prettyPhoto[pp_gal]" title="'.esc_attr($alt).'">'.$icon.'</a>';
						} else {
							$output .= '<a class="tg-hidden-tag'.esc_attr($class).'" href="'.esc_url($image).'" rel="prettyPhoto[pp_gal]" title="'.esc_attr($alt).'"></a>';
						}
					}
					break;
				case 'fancybox':
					for ($i = 0; $i < count($gallery); $i++) {
						$image = $gallery[$i]['lb_url'];
						if ($i == 0) {
							$output .= '<a class="tg-media-button fancybox'.esc_attr($class).'" rel="tg_group" href="'.esc_url($image).'">'.$icon.'</a>';
						} else {
							$output .= '<a class="tg-hidden-tag fancybox" rel="tg_group" href="'.esc_url($image).'"></a>';
						}
					}
					break;
				case 'foobox':
					for ($i = 0; $i < count($gallery); $i++) {
						$image = $gallery[$i]['lb_url'];
						$title = $gallery[$i]['title'];
						$alt   = $gallery[$i]['alt'];
						$alt    = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
						if ($i == 0) {
							$output .= '<a class="tg-media-button foobox'.esc_attr($class).'" href="'.esc_url($image).'" title="'.esc_attr($alt).'" rel="">'.$icon.'</a>';
						} else {
							$output .= '<a class="tg-hidden-tag foobox" href="'.esc_url($image).'" title="'.esc_attr($alt).'" rel=""></a>';
						}
					}
					break;
				case 'the_grid':
					for ($i = 0; $i < count($gallery); $i++) {
						$image = $gallery[$i]['lb_url'];
						$title = $gallery[$i]['title'];
						$alt   = $gallery[$i]['alt'];
						$alt   = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
						if ($i == 0) {
							$output .= '<div class="tg-media-button'.esc_attr($class).'" data-tolb-src="'.esc_url($image).'" data-tolb-type="image" data-tolb-alt="'.esc_attr($alt).'">'.$icon.'</div>';
						} else {
							$output .= '<div class="tg-hidden-tag" data-tolb-src="'.esc_url($image).'" data-tolb-type="image" data-tolb-alt="'.esc_attr($alt).'"></div>';
						}
					}
					break;
			}
			
			return $output;
		
		}
		
		
	}
	
	/**
	* Get image lightbox markup
	* @since: 1.0.0
	*/
	public function get_image_lightbox($icon, $class) {

		$image = $this->base->getVar($this->grid_item['image'], 'lb_url');
		$image = (!$image) ? $this->base->getVar($this->grid_item['image'], 'url') : $image;
		
		if ($image) {
			
			$lightbox_type = $this->grid_data['lightbox_type'];
			$title = $this->base->getVar($this->grid_item['image'], 'title');
			$alt   = $this->base->getVar($this->grid_item['image'], 'alt');
			$alt   = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
			
			switch ($lightbox_type) {
				case 'prettyphoto':
					return '<a class="tg-media-button'.esc_attr($class).'" href="'.esc_url($image).'" rel="prettyPhoto[pp_gal]" title="'.esc_attr($alt).'">'.$icon.'</a>';
					break;
				case 'fancybox':
					return '<a class="tg-media-button fancybox'.esc_attr($class).'" href="'.esc_url($image).'">'.$icon.'</a>';
					break;
				case 'foobox':
					return '<a class="tg-media-button foobox'.esc_attr($class).'" href="'.esc_url($image).'" title="'.esc_attr($alt).'" rel="">'.$icon.'</a>';
					break;
				case 'the_grid':
					return '<div class="tg-media-button'.esc_attr($class).'" data-tolb-src="'.esc_url($image).'" data-tolb-type="image" data-tolb-alt="'.esc_attr($alt).'">'.$icon.'</div>';
					break;
			}
		
		}
		
	}
	
	/**
	* Set default colors from item/skin grid settings
	* @since: 1.0.0
	*/
	public function get_colors() {
			
		// get meta data
		$meta_data = $this->base->getVar($this->grid_item, 'meta_data', array());
			
		// content colors
		$content_co_skin = $this->grid_data['skin_content_color'];
		$content_co_item = $this->base->getVar($meta_data, 'the_grid_item_content_color');		
		$content_bg_skin = $this->grid_data['skin_content_background'];
		$content_bg_item = $this->base->getVar($meta_data, 'the_grid_item_content_background');
		$content_co      = (empty($content_bg_item)) ? $content_co_skin : $content_co_item;
		$content_bg      = (empty($content_bg_item)) ? $content_bg_skin : $content_bg_item;
			
		// overlay colors
		$overlay_co_skin = $this->grid_data['skin_overlay_color'];
		$overlay_co_item = $this->base->getVar($meta_data, 'the_grid_item_overlay_color');		
		$overlay_bg_skin = $this->grid_data['skin_overlay_background'];
		$overlay_bg_item = $this->base->getVar($meta_data, 'the_grid_item_overlay_background');
		$overlay_co      = (empty($overlay_bg_item)) ? $overlay_co_skin : $overlay_co_item;
		$overlay_bg      = (empty($overlay_bg_item)) ? $overlay_bg_skin : $overlay_bg_item;

		// defaults colors
		$def_colors = array(
			'dark_title'  => '#444444',
			'dark_text'   => '#777777',
			'dark_span'   => '#999999',
			'light_title' => '#ffffff',
			'light_text'  => '#eeeeee',
			'light_span'  => '#dddddd'
		);
			
		$grid_colors = $this->base->getVar($this->grid_data, 'grid_colors', array());
		$grid_colors_content_co = $this->base->getVar($grid_colors, $content_co, array());
		$grid_colors_overlay_co = $this->base->getVar($grid_colors, $overlay_co, array());
			
		// defined color array
		return array(
			'content' => array(
				'background' => $content_bg,
				'class' => $content_co,
				'title' => $this->base->getVar($grid_colors_content_co,'title',$def_colors[$content_co.'_title']),
				'text'  => $this->base->getVar($grid_colors_content_co,'text',$def_colors[$content_co.'_title']),
				'span'  => $this->base->getVar($grid_colors_content_co,'span',$def_colors[$content_co.'_title']),
			),
			'overlay' => array(
				'background' => $overlay_bg,
				'class' => $overlay_co,
				'title' => $this->base->getVar($grid_colors_overlay_co,'title',$def_colors[$overlay_co.'_title']),
				'text'  => $this->base->getVar($grid_colors_overlay_co,'text',$def_colors[$overlay_co.'_title']),
				'span'  => $this->base->getVar($grid_colors_overlay_co,'span',$def_colors[$overlay_co.'_title']),
			)
		);

	}
	
}

if(!function_exists('The_Grid_Elements')) {
	
	/**
	* Tiny wrapper function
	* @since 1.0.0
	*/
	function The_Grid_Elements() {
		$to_Item_Content = The_Grid_Elements::getInstance();	
		$to_Item_Content->init();
		return $to_Item_Content;
	}
	
}