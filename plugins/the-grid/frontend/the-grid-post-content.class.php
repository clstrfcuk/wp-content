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
 
/***********************************************
* THE GRID CONTENT Class
***********************************************/

class The_Grid_Item_Post_Content {

	/**
	 * Plugin slug & prefix
	 * @since 1.0.0
	 */
	protected $plugin_slug = TG_SLUG;
	protected $grid_prefix = TG_PREFIX;
	protected $grid_base;
	protected $grid_data;
	protected $grid_style;
	protected $item_format;
	protected $post_type;
	protected $post_format;
	
	/**
	 * Custom link/target
	 * @since 1.0.5
	 */
	private $custom_link   = '';
	private $custom_target = '_self';
	
	/**
	 * Main var for this instance
	 * @since 1.0.0
	 */
	private $iframe_poster;
	private $media_poster   = true;
	private $video_lightbox = false;
	private $allowed_format = array('gallery','video','audio');
	private $icons = array(
		'link'       => '<i class="tg-icon-link"></i>',
		'comment'    => '',
		'image'      => '<i class="tg-icon-zoom-in"></i>',
		'audio'      => '<i class="tg-icon-play"></i>',
		'video'      => '<i class="tg-icon-play"></i>',
		'vimeo'      => '<i class="tg-icon-play"></i>',
		'wistia'     => '<i class="tg-icon-play"></i>',
		'youtube'    => '<i class="tg-icon-play"></i>',
		'soundcloud' => '<i class="tg-icon-play"></i>'
	);
	private $excerpt_length  = 280;
	private $excerpt_tag     = '...';
	private $read_more       = '';
	private $date_format     = '';
	private $get_terms       = true;
	private $term_color      = 'none';
	private $term_link       = true;
	private $term_separator  = '';
	private $item_colors     = array();
	private $author_prefix   = '';
	private $avatar          = false;

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
	* to initialize a The_Grid_Item_Content object
	* @since 1.0.0
	*/
	static public function getInstance() {
		if(self::$instance == null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	* Check meta options
	* @since: 1.0.0
	*/
	public function getMeta($key,$default = ''){
		$meta = get_post_meta(get_the_ID(), $this->grid_prefix.$key, true);
		$meta = (isset($meta) && !empty($meta)) ? $meta : $default;
		return $meta;
	}
		
	/**
	* Initialize the plugin by retrieving and settings all grid options & data
	* @since: 1.0.0
	*/
	public function process($options) {
		
		// set main item/grid vars
		$this->set_var($options);

		// get media content (for all formats retieve markup for image/gallery, video, audio)
		$media_content = $this->get_media();
		
		// get quote/link content
		$quote_link = $this->get_quote_link_markup();
		
		// get center/content wrapper markup
		$content['center_wrapper_start']  = $this->center_wrapper_start();
		$content['center_wrapper_end']    = $this->center_wrapper_end();
		$content['content_wrapper_start'] = $this->content_wrapper_start();
		$content['content_wrapper_end']   = $this->content_wrapper_end();
		
		// get main content
		$content['overlay']      = $this->overlay_markup();
		$content['content']      = $this->get_excerpt();
		$content['title']        = $this->get_title();
		$content['date']         = $this->get_date();
		$content['terms']        = $this->get_terms();
		$content['comments']     = $this->get_comments();
		$content['author']       = $this->get_author();
		$content['duration']     = $this->get_duration();
		$content['views']        = $this->get_views();
		$content['read_more']    = $this->get_read_more();
		$content['post_like']    = $this->get_post_like();
		$content['social_links'] = $this->get_social_share();
		$content['colors']       = $this->item_colors;
		
		// get link
		$content['permalink'] = $this->custom_link;
		$content['target']    = $this->custom_target;
		
		// get woocommerce data
		$content['product_price']  = $this->get_product_price();
		$content['product_rating'] = $this->get_product_rating();
		$content['product_sale']   = $this->get_product_on_sale();
		$content['product_cart_button'] = $this->get_product_cart_button();
		$content['product_wishlist'] = $this->get_product_wishlist();
		
		// merge content + media content
		$content = $media_content + $quote_link + $content;

		// return awesome array of all necessary info to build an element
		return $content;
	}
	
	/**
	* Assign & set main variables
	* @since: 1.0.0
	*/
	public function set_var($options) {
		
		// retrieve grid data settings
		global $tg_grid_data, $tg_skins_preview;
		$grid_base = new The_Grid();
		
		// get the post type
		$this->post_type = ($tg_skins_preview) ? 'post' : get_post_type();
		
		// get the post format
		$this->post_format = $this->getMeta('item_format', '');
		$this->post_format = (empty($this->post_format)) ? get_post_format() : $this->post_format;
		
		// re-assign grid data for current class
		$this->grid_base = $grid_base;
		$this->grid_data = $tg_grid_data;
		
		// set the custom link/target
		$default_url = ($this->post_type != 'attachment') ? $this->get_the_permalink() : null;
		$this->custom_link   = $this->getMeta('item_custom_link', $default_url);
		$this->custom_target = $this->getMeta('item_custom_link_target', '_self');
		
		// Set main var from current grid data
		$this->video_lightbox = $this->grid_data['video_lightbox'];
		$this->allowed_format = (isset($this->grid_data['items_format'])) ? $this->grid_data['items_format'] : $this->allowed_format;
		$this->grid_style     = $this->grid_data['style'];
		// Set var to get or not video/youtube/soundcloud posters (depends of the skin)
		$this->media_poster   = (isset($options['poster'])) ? $options['poster'] : $this->media_poster;
		$this->iframe_poster  = ($this->media_poster) ? true : false;
		// Set media icons
		$icons = (isset($options['icons'])) ? $options['icons'] : null;
		$this->icons['image'] = (isset($icons['image']) && !empty($icons['image'])) ? $icons['image'] : $this->icons['image'];
		$this->icons['audio'] = (isset($icons['audio']) && !empty($icons['audio'])) ? $icons['audio'] : $this->icons['audio'];
		$this->icons['video'] = (isset($icons['video']) && !empty($icons['video'])) ? $icons['video'] : $this->icons['video'];
		$this->icons['vimeo'] = (isset($icons['vimeo']) && !empty($icons['vimeo'])) ? $icons['vimeo'] : $this->icons['vimeo'];
		$this->icons['wistia'] = (isset($icons['wistia']) && !empty($icons['wistia'])) ? $icons['wistia'] : $this->icons['wistia'];
		$this->icons['youtube'] = (isset($icons['youtube']) && !empty($icons['youtube'])) ? $icons['youtube'] : $this->icons['youtube'];
		$this->icons['soundcloud'] = (isset($icons['soundcloud']) && !empty($icons['soundcloud'])) ? $icons['soundcloud'] : $this->icons['soundcloud'];
		// Set link icon
		$this->link_icon = (isset($icons['link']) && !empty($icons['link'])) ? $icons['link'] : $this->icons['link'];
		// Set comment icon
		$this->comment_icon = (isset($icons['comment']) && !empty($icons['comment'])) ? $icons['comment'] : $this->icons['comment'];
		// Set excerpt length & more tag
		$this->excerpt_length  = (isset($options['excerpt_length'])) ? $options['excerpt_length'] : $this->excerpt_length;
		$this->excerpt_tag     = (isset($options['excerpt_tag']) && !empty($options['excerpt_tag'])) ? $options['excerpt_tag'] : $this->excerpt_tag;
		// Set read more text/icon
		$this->read_more       = (isset($options['read_more']) && !empty($options['read_more'])) ? $options['read_more'] : __( 'Read More', 'tg-text-domain' );
		// Set date format
		$this->date_format     = (isset($options['date_format']) && !empty($options['date_format'])) ? $options['date_format'] : $this->date_format;
		// Retrieve categories
		$this->get_terms       = (isset($options['get_terms'])) ? $options['get_terms'] : $this->get_terms;
		// Set terms color
		$this->term_color      = (isset($options['term_color']) && !empty($options['term_color'])) ? $options['term_color'] : $this->term_color;
		// Set link in terms
		$this->term_link       = (isset($options['term_link'])) ? $options['term_link'] : $this->term_link;
		// Set terms separator
		$this->term_separator  = (isset($options['term_separator'])) ? $options['term_separator'] : $this->term_separator;
		// Set avatar
		$this->author_prefix   = (isset($options['author_prefix'])) ? $options['author_prefix'] : $this->author_prefix;
		// Set avatar
		$this->avatar = (isset($options['avatar'])) ? $options['avatar'] : $this->avatar;
		// Set item colors
		$this->set_colors();
		
	}
	
	/**
	* Set default colors from item/skin grid settings
	* @since: 1.0.0
	*/
	public function set_colors() {
		
		// content colors
		$content_co_skin = $this->grid_data['skin_content_color'];
		$content_co_item = $this->getMeta('item_content_color');		
		$content_bg_skin = $this->grid_data['skin_content_background'];
		$content_bg_item = $this->getMeta('item_content_background');
		$content_co      = (empty($content_bg_item)) ? $content_co_skin : $content_co_item;
		$content_bg      = (empty($content_bg_item)) ? $content_bg_skin : $content_bg_item;
		
		// overlay colors
		$overlay_co_skin = $this->grid_data['skin_overlay_color'];
		$overlay_co_item = $this->getMeta('item_overlay_color');		
		$overlay_bg_skin = $this->grid_data['skin_overlay_background'];
		$overlay_bg_item = $this->getMeta('item_overlay_background');
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
		
		// defined color array
		$this->item_colors = array(
			'content' => array(
				'background' => $content_bg,
				'class' => $content_co,
				'title' => get_option('the_grid_'.$content_co.'_title', $def_colors[$content_co.'_title']),
				'text'  => get_option('the_grid_'.$content_co.'_text', $def_colors[$content_co.'_text']),
				'span'  => get_option('the_grid_'.$content_co.'_span', $def_colors[$content_co.'_span'])
			),
			'overlay' => array(
				'background' => $overlay_bg,
				'class' => $overlay_co,
				'title' => get_option('the_grid_'.$overlay_co.'_title', $def_colors[$overlay_co.'_title']),
				'text'  => get_option('the_grid_'.$overlay_co.'_text', $def_colors[$overlay_co.'_text']),
				'span'  => get_option('the_grid_'.$overlay_co.'_span', $def_colors[$overlay_co.'_span'])
			)
		);	
	
	}

	/**
	* Main function to get formated media (used for skin)
	* return an array of data (url,id,size,type) for each media and the markup
	* @since: 1.0.0
	*/
	public function get_media() {
		
		$post_id = get_the_ID();
		$format  = $this->post_format;
		
		// check allowed formats from grid settings (Gallery, Video or Audio). Force std format if not authorized
		$format  = (!empty($format) && in_array($format,$this->allowed_format)) ? $format : 'standard';
		
		$media = null;
		
		// serach for the media content depend on of the format
		switch ($format) {
			case 'gallery':
				$media = $this->gallery_format();
				break;
			case 'audio':
				$media = $this->audio_format();
				break;
			case 'video':
				$media = $this->video_format();
				break;
			default:
				$media = $this->image_format();
				break;
		}

		// set the post format
		$media = array('post_format'=>$format) + $media;
		// set the real content type (not necessary format if missing video or audio,...)
		$media['media_type'] = (!empty($media['media_data'])) ? $media['media_type'] : 'none';
		$this->item_format   = $media['media_type'];
		// retrieve the media poster (Youtube, Vimeo, SoundCloud, Video, Audio)
		$media['media_poster'] = $this->media_poster($media);
		// retrieve the media markup
		$media['media_markup'] = $this->get_media_markup($media,$media['media_poster']);
		
		// lightBox/Play button conditions
		// Add the lightbox if a poster exist or if it's an image
		$poster_exist   = $media['media_poster']['markup'];        // if there is a poster
		$media_image    = (($media['media_type'] == 'image' || $media['media_type'] == 'gallery')); // if it's an image/gallery
		$lightbox_video = ($this->video_lightbox);           // if video in lightbox
		$media['media_button'] = ($poster_exist || $media_image  || $lightbox_video && $poster_exist || $this->grid_style == 'grid') ? $this->get_lightbox($media) : null;
		$media['link_button']  = ($media['media_button']) ? $this->get_post_link() : null;
		
		// get media wrapper markup
		$media['media_wrapper_start'] = ($media['media_markup']) ? $this->media_wrapper_start() : null;
		$media['media_wrapper_end']   = ($media['media_markup']) ? $this->media_wrapper_end()   : null;

		return $media;

	}
	
	/**
	* Get Quote link Markup
	* return an array of data
	* @since: 1.0.0
	*/
	public function get_quote_link_markup() {
		
		$post_id = get_the_ID();
		$format  = $this->post_format;
		
		// check allowed formats from grid settings (Gallery, Video or Audio). Force std format if not authorized
		$format  = (!empty($format) && in_array($format,$this->allowed_format)) ? $format : 'standard';
	
		$content = null;
		$data = array();
		
		// serach for the media content depend on of the format
		switch ($format) {
			case 'quote':
				$content = $this->quote_format();
				break;
			case 'link':
				$content = $this->link_format();
				break;
		}
		
		if (!empty($content[$format.'_data'])) {
			$this->item_format = $format;
			$function = $format.'_markup';
			$data[$format.'_data'] = (!empty($content)) ? $content : null;
			$data[$format.'_markup'] = (!empty($content)) ? $this->$function($content) : null;
		}

		return $data;
	}

	/**
	* Media holder markup start 
	* @since: 1.0.0
	*/
	public function media_wrapper_start() {	
		$class = $this->item_colors['overlay']['class'];
		return '<div class="tg-item-media-holder '.esc_attr($class).'">';
	}
	
	/**
	* Media holder markup end 
	* @since: 1.0.0
	*/
	public function media_wrapper_end() {	
		return '</div>';
	}
	
	/**
	* Content holder markup start (masonry)
	* @since: 1.0.0
	*/
	public function content_wrapper_start() {
		$class   = $this->item_colors['content']['class'];
		$format  = $this->item_format.'-format';
		$bg_skin = $this->grid_data['skin_content_background'];
		$bg_item = $this->item_colors['content']['background'];
		$background = ($bg_skin != $bg_item) ? 'style="background-color:'.esc_attr($bg_item).'"' : null;
		return '<div class="tg-item-content-holder '.esc_attr($class).' '.esc_attr($format).'" '.$background.'>';
	}
	
	/**
	* Content holder markup end  (masonry)
	* @since: 1.0.0
	*/
	public function content_wrapper_end() {	
		return '</div>';
	}
	
	/**
	* Center markup Start
	* @since: 1.0.0
	*/
	public function center_wrapper_start() {	
		$html  = '<div class="tg-center-outer">';
			$html .= '<div class="tg-center-inner">';
		return $html;
	}
	
	/**
	* Center markup End
	* @since: 1.0.0
	*/
	public function center_wrapper_end() {	
			$html  = '</div>';
		$html .= '</div>';
		return $html;
	}
	
	/**
	* Overlay markup
	* @since: 1.0.0
	*/
	public function overlay_markup() {
		$bg_skin = $this->grid_data['skin_overlay_background'];
		$bg_item = $this->item_colors['overlay']['background'];
		$background = ($bg_skin != $bg_item) ? 'style="background-color:'.esc_attr($bg_item).'"' : null;
		$html  = '<div class="tg-item-overlay" '.$background.'></div>';
		return $html;
	}
	
	/**
	* Comment number
	* @since: 1.0.0
	*/
	public function get_comments() {
		if ($this->post_type != 'attachment') {
			global $tg_skins_preview;
			$icon = $this->comment_icon;
			$num_comments  = (!$tg_skins_preview) ? get_comments_number() : 1;
			$comments_open = (!$tg_skins_preview) ? comments_open() : 1;
			$write_comments = null;
			$nonCom  = __( 'No comment', 'tg-text-domain' );
			$oneCom  = __( 'comment', 'tg-text-domain' );
			$sevCom  = __( 'Comments', 'tg-text-domain' );
			$dataCom = 'data-comment=\'{"no":"'.$nonCom.'","one":"'.$oneCom.'","plus":"'.$sevCom.'"}\'';
			if ($comments_open) {
				if (!$icon) {
					if ($num_comments == 0) {
						$comments = $nonCom;
					} else if ($num_comments == 1) {
						$comments = $num_comments .' '. $oneCom;
					} else {
						$comments = $num_comments .' '. $sevCom;
					}
					$write_comments = '<a class="tg-item-comment" '.$dataCom.' data-comment-id="'.get_the_id().'" href="' . get_comments_link() .'">'. $comments.'</a>';
				} else {
					$write_comments = '<a class="tg-item-comment" data-comment-id="'.get_the_id().'" href="' . get_comments_link() .'">'.$icon.$num_comments.'</a>';
				}
			}
			$comment['markup'] = $write_comments;
			$comment['number'] = $num_comments;
			return $comment;
		}
	}
	
	/**
	* Get author
	* @since: 1.0.0
	*/
	public function get_author() {
		
		global $tg_skins_preview;
		
		$author = (!$tg_skins_preview) ? get_the_author() : 'Themeone';
		$author_url = (!$tg_skins_preview) ? get_author_posts_url(get_the_author_meta('ID')) : null;
		$author_prefix = '<span>'.esc_html($this->author_prefix).'</span>';
		
		if (!empty($author)) {
		
			if ($this->avatar) {
				$html  = '<div class="tg-item-author-holder">';
				$html .= '<span class="tg-item-avatar">'.get_avatar(get_the_author_meta('ID'), '46').'</span>';
				$html .= '<span class="tg-item-author">'.$author_prefix.'<a href="'. esc_url($author_url) .'">'.$author.'</a></span>';
				$html .= '</div>';
			} else {
				$html  = '<span class="tg-item-author">'.$author_prefix.'<a href="'. esc_url($author_url) .'">'.$author.'</a></span>';
			}
			
			return $html;
		
		}
		
	}
	
	/**
	* Get duration
	* @since: 1.0.0
	*/
	public function get_duration() {
		
		global $tg_skins_preview;
		
		if ($tg_skins_preview) {
			$duration = '06:25';
			$html = '<div class="tg-item-duration">'.$duration.'</div>';
			return $html;
		}
		
	}
	
	/**
	* Get views
	* @since: 1.0.0
	*/
	public function get_views() {
		
		global $tg_skins_preview;
		
		if ($tg_skins_preview) {
			$views = '12.5k';
			$html = '<span class="tg-item-views">'.$views.' '.__( 'views', 'tg-text-domain' ) .'</span>';
			return $html;
		}
		
	}
	
	/**
	* Get read more
	* @since: 1.0.0
	*/
	public function get_read_more() {
		$url    = $this->custom_link;
		$target = $this->custom_target;
		if (!empty($url)) {
			$html  = '<div class="tg-item-read-more">';
			$html .= '<a href="'.esc_url($url).'" target="'.esc_attr($target).'">';
			$html .= $this->read_more;
			$html .= '</a>';
			$html .= '</div>';
			return $html;
		}
	}
	
	/**
	* Media title 
	* @since: 1.0.0
	*/
	public function get_post_link() {	
		$url    = $this->custom_link;
		$target = $this->custom_target;
		if (!empty($url)) {
			return '<a class="tg-link-button" href="'.esc_url($url).'" target="'.esc_attr($target).'">'. $this->link_icon .'</a>';
		}
	}
	
	/**
	* Retrieve title
	* @since: 1.0.0
	*/
	public function get_title() {
		$title = get_the_title();
		if (!empty($title)) {
			$url    = $this->custom_link;
			$target = $this->custom_target;
			$html  = '<h2 class="tg-item-title">';
			$html .= (!empty($url)) ? '<a href="'.esc_url($url).'" target="'.esc_attr($target).'">' : null;
			$html .= $title;
			$html .= (!empty($url)) ? '</a>' : null;
			$html .= '</h2>';
			return $html;
		}
	}
	
	/**
	* Get the permalink
	* @since: 1.0.5
	*/
	public function get_the_permalink() {
		global $tg_skins_preview;
		$permalink = ($tg_skins_preview) ? 'the-post-link.com' : get_the_permalink();
		return $permalink;
	}
	
	/**
	* Retrieve excerpt/content
	* @since: 1.0.0
	*/
	public function get_excerpt() {
		$charlength = $this->excerpt_length;
		if (!empty($charlength)) {
			$more_tag   = $this->excerpt_tag;
			$excerpt    = get_the_excerpt();
			$charlength++;
			if (mb_strlen($excerpt) > $charlength) {
				$subex   = mb_substr($excerpt, 0, $charlength - 5);
				$exwords = explode( ' ', $subex );
				$excut   = - (mb_strlen($exwords[count($exwords) - 1]));
				if ($excut < 0) {
					$content = mb_substr($subex, 0, $excut);
				} else {
					$content = $subex;
				}
			} else {
				$content = $excerpt;
			}
			if (!empty($content)) {
				return '<p class="tg-item-excerpt">'.$content.esc_html($more_tag).'</p>';
			}
		}
	}
	
	/**
	* Retrieve the date
	* @since: 1.0.0
	*/
	public function get_date() {
		return '<span class="tg-item-date">'.get_the_date($this->date_format).'</span>';
	}
	
	/**
	* Retrieve post like
	* @since: 1.0.0
	*/
	public function get_post_like() {
		return TO_get_post_like();
	}
	
	/**
	* Retrieve social share
	* @since: 1.0.0
	*/
	public function get_social_share() {
		
		$social['facebook']  = '<a href="#" class="tg-facebook"><i class="tg-icon-facebook"></i></a>';
		$social['twitter']   = '<a href="#" class="tg-twitter"><i class="tg-icon-twitter"></i></a>';
		$social['google+']   = '<a href="#" class="tg-google1"><i class="tg-icon-google-plus"></i></a>';
		$social['pinterest'] = '<a href="#" class="tg-pinterest"><i class="tg-icon-pinterest"></i></a>';
		
		return $social;
		
	}
	
	
	/**
	* Retrieve Woocommerce price
	* @since: 1.0.0
	*/
	public function get_product_price() {
		
		global $tg_skins_preview;
			
		$html = null;
			
		if (class_exists('WooCommerce') && !$tg_skins_preview && $this->post_type == 'product') {
			
			global $product;
			
			$price = $product->get_price_html();
			
			if ($price) {
				$html  = '<div class="tg-item-price">';
				$html .= $price;
				$html .= '</div>';
			}
				
		} else if ($tg_skins_preview) {
			
			$html  = '<div class="tg-item-price">';
			$html .= '<span class="amount">99$</span>';
			$html .= '</div>';
				
		}
	
		return $html;
		
	}
	
	/**
	* Retrieve Woocommerce rating
	* @since: 1.0.0
	*/
	public function get_product_rating() {
		
		global $tg_skins_preview;
		
		$html = null;
		
		if (class_exists('WooCommerce') && !$tg_skins_preview && $this->post_type == 'product') {
		
			global $product;
			
			$html  = '<div class="tg-item-rating">';
			$html .= preg_replace('#(<span.*?>).*?(</span>)#', '$1$2', $product->get_rating_html());
			$html .= '</div>';
			
		} else if ($tg_skins_preview) {
		
			$html  = '<div class="tg-item-rating">';
			$html .= '<div class="star-rating">';
			$html .= '<span style="width:90%">';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '</div>';
			
		}

		return $html;
		
	}
	
	/**
	* Retrieve Woocommerce sale status
	* @since: 1.0.0
	*/
	public function get_product_on_sale() {
		
		global $tg_skins_preview;
		
		$html = null;

		if (class_exists('WooCommerce') && !$tg_skins_preview && $this->post_type == 'product') {
		
			global $post, $product;
			
			if ($product->is_on_sale()) {
				
				$html  = '<div class="tg-item-on-sale light">';
				$html .= apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>', $post, $product );
				$html .= '</div>';
				
			}
			
		}

		return $html;
		
	}

	
	/**
	* Retrieve Woocommerce add to cart button
	* @since: 1.0.0
	*/
	public function get_product_cart_button() {
		
		global $tg_skins_preview;
		
		$html = null;
		
		if (class_exists('WooCommerce') && !$tg_skins_preview && $this->post_type == 'product') {
		
			global $product;
				
			$html  = '<div class="tg-item-cart-button">';
			$html .= apply_filters( 'woocommerce_loop_add_to_cart_link',
						sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
							esc_url( $product->add_to_cart_url() ),
							esc_attr( $product->id ),
							esc_attr( $product->get_sku() ),
							esc_attr( isset( $quantity ) ? $quantity : 1 ),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							esc_attr( $product->product_type ),
							esc_html( $product->add_to_cart_text() )
						),
					$product );
			$html .= '</div>';
			
		} else if ($tg_skins_preview) {
			$html  = '<div class="tg-item-cart-button">';
			$html .= '<a href="" rel="nofollow" data-product_id="-1" data-product_sku="" data-quantity="0" class="button add_to_cart_button product_type_simple">'.__( 'Add to cart', 'tg-text-domain' ).'</a>';
			$html .= '</div>';
		}

		return $html;
		
	}
	
	/**
	* Retrieve Woocommerce YITH Whislist
	* @since: 1.0.0
	*/
	public function get_product_wishlist() {
		
		global $tg_skins_preview;
		
		$html = null;

		if (class_exists('WooCommerce') && !$tg_skins_preview && $this->post_type == 'product') {
		
			global $yith_wcwl;

			if ($yith_wcwl) {
				$html = do_shortcode('[yith_wcwl_add_to_wishlist]');
				$html = preg_replace('#<div class="clear">(.*?)</div>#', '', $html);
			}
			
		}

		return $html;
		
	}
	
	/**
	* Retrieve categories
	* @since: 1.0.0
	*/
	public function get_terms() {
		
		if ($this->get_terms) {
		
			global $wp_rewrite, $tg_skins_preview;
			
			$cat1 = array(
				'taxonomy' => 'the_grid_taxo',
				'term_id' => -1,
				'name' => 'Category'
			);
			$cat1 = (object) $cat1;
			$tg_category = array('taxo' => $cat1);
	
			$terms = array();
			$post_id    = get_the_id();
			$taxonomies = get_object_taxonomies($this->post_type, 'objects');
			foreach ($taxonomies as $taxonomy_slug => $taxonomy){
				$new_terms = get_the_terms($post_id, $taxonomy_slug);
				if(!empty($new_terms) && $taxonomy_slug != 'product_type' && $taxonomy_slug != 'post_format'){
					$terms = array_merge($terms, $new_terms);
				}
			}
			
			$terms = (!$tg_skins_preview) ? $terms : $tg_category;
	
			$cat = null;
			if (!empty($terms)) {
				$i = 0;
				$separator = $this->term_separator;
				$rel_attr  = (is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks()) ? 'rel="category tag"' : 'rel="category"';
				
				foreach ($terms as $term) {
					
					$color = null;
					if ($this->term_color == 'background' || $this->term_color == 'color') { 

						$term_ID    = $term->term_id;
						$term_data  = get_option($term->taxonomy.'_'.$term_ID);
						$term_color = (isset($term_data['the_grid_term_color']) && !empty($term_data['the_grid_term_color'])) ? $term_data['the_grid_term_color'] : null;
						
						if ($this->term_color == 'background') {
							$base = new The_Grid_Base();
							$brightness = $base->brightness($term_color);
							$color = ($brightness == 'bright') ? '#000000' : '#ffffff';
							$color = (!empty($term_color)) ? ' style="background:'.$term_color.';color:'.$color.'"' : null;
						} else {
							$color = (!empty($term_color)) ? ' style="color:'.$term_color.'"' : null;
						}

					}
					
					if ($i > 0 && !empty($separator)) {
						$cat .= '<span>'.$separator.'</span>';
					}
					if ($this->term_link && !$tg_skins_preview) {
						$cat .= '<a class="'. $term->taxonomy .'" href="' . esc_url(get_term_link($term)) . '" ' . $rel_attr  . '><span'.$color.'>' . $term->name.'</span></a>';
					} else {
						$cat .= '<span class="'. $term->taxonomy .'"'.$color.'>'. $term->name .'</span>';
					}
					++$i;
				}
			}
			
			if (!empty($cat)) {
				$html  = '<div class="tg-cats-holder">';
				$html .= $cat;
				$html .= '</div>';
				return $html;
			}
		
		}
		
	}
	
	/**
	* search for the right image
	* @since: 1.0.0
	*/
	public function image_format() {
		
		$image   = null;
		// image content source order
		$source_order = array(
			'alternative_image',   // meta data the grid
			'thumbnail_image',     // standard feature thumbnail
			'first_content_media', // first image in text content
			'default_image'        // default image set in Mobius Grid Settings
		);
		
		foreach($source_order as $content) {
			$image = $this->$content();
			if (!empty($image['media_data'])) break;
		}

		$image = array('media_type'=>'image') + $image;
		
		// get right image size and image data
		$image['media_data'] = (!empty($image['media_data'])) ? $this->image_data($image['media_data']) : null;

		return $image;
	}
	
	/**
	* search for the link data
	* @since: 1.0.0
	*/
	public function quote_format() {
		
		$quote = null;
		// quote content source order
		$source_order = array(
			'alternative_quote',   // meta data the grid
			'first_content_media'  // first quote in text content
		);
		
		foreach($source_order as $content) {
			$quote = $this->$content();
			if (!empty($quote['quote_data'])) break;
		}

		return $quote;
	}
	
	/**
	* search for the right link data
	* @since: 1.0.0
	*/
	public function link_format() {
		
		$link = null;
		// link content source order
		$source_order = array(
			'alternative_link',    // meta data the grid
			'first_content_media'  // first link in text content
		);
		
		foreach($source_order as $content) {
			$link = $this->$content();
			if (!empty($link['link_data'])) break;
		}

		return $link;
	}
	
	/**
	* search for the right gallery data
	* @since: 1.0.0
	*/
	public function gallery_format() {
		
		$gallery = null;
		// image content source order
		$source_order = array(
			'alternative_gallery', // meta data the grid
			'first_content_media'  // first gallery in text content
		);
		
		foreach($source_order as $content) {
			$gallery_IDs = $this->$content();
			if (!empty($gallery_IDs['media_data'])) break;
		}

		// if no gallery check for image
		if ($gallery_IDs['media_type'] != 'gallery') {
			return $this->image_format();
		}
		
		$gallery['media_data']['type'] = 'gallery';
		// get right gallery image sizes and image data
		foreach($gallery_IDs['media_data'] as $gallery_ID) {
			$gallery['media_data']['images'][] = $this->image_data($gallery_ID);
		}
		
		$gallery = array('media_type'=>'gallery') + $gallery;

		return $gallery;
	}
	
	/**
	* search for the right audio data
	* @since: 1.0.0
	*/
	public function audio_format() {
		
		$audio_data = null;
		// audio content source order
		$source_order = array(
			'alternative_audio',  // meta data the grid
			'first_content_media' // first audio content in text content
		);
		
		foreach($source_order as $content) {
			$audio = $this->$content();
			if (!empty($audio['media_data'])) break;
		}

		// if no audio check for image
		if ($audio['media_type'] != 'audio') {
			return $this->image_format();
		}
		
		$audio = array('media_type'=>'audio') + $audio;

		return $audio;
	}
	
	/**
	* search for the right video data
	* @since: 1.0.0
	*/
	public function video_format() {
		
		$video_data = null;
		// image content source order
		$source_order = array(
			'alternative_video',  // meta data the grid
			'first_content_media' // first video in text content
		);
		
		foreach($source_order as $content) {
			$video = $this->$content();
			if (!empty($video['media_data'])) break;
		}

		// if no video check for image
		if ($video['media_type'] != 'video') {
			return $this->image_format();
		}

		$video = array('media_type'=>'video') + $video;
		
		return $video;
	}
	
	/**
	* get first content image (run Themeone plugin To_First_Media)
	* @since: 1.0.0
	*/
	public function first_content_media() {
		$media = To_First_Media();
		return $media;
	}

	/**
	* get alternative image (post type metadata)
	* @since: 1.0.0
	*/
	public function alternative_image() {
		$img_ID['media_data'] = $this->getMeta('item_image');
		return $img_ID;
	}
	
	/**
	* get thumbnail image of current post
	* @since: 1.0.0
	*/
	public function thumbnail_image() {
		$thumb_ID = ($this->post_type == 'attachment') ? get_the_ID() : get_post_thumbnail_id();
		$img_ID['media_data'] = $thumb_ID;
		return $img_ID;
	}
	
	/**
	* get default image set in grid option
	* @since: 1.0.0
	*/
	public function default_image() {
		global $tg_skins_preview;
		$img_ID['media_data'] = ($this->grid_style == 'grid' || $this->grid_style == 'justified' || $tg_skins_preview == true) ? $this->grid_data['default_image'] : null;
		return $img_ID;
	}
	
	/**
	* get alternative gallery (post type metadata)
	* @since: 1.0.0
	*/
	public function alternative_gallery() {
		
		$gallery_IDs = $this->getMeta('item_gallery');
		
		$gallery_arr['media_type'] = 'gallery';
		$gallery_arr['media_data'] = (!empty($gallery_IDs)) ? explode(',', $gallery_IDs) : null;
		
		return $gallery_arr;
	}
	
	/**
	* get alternative quote (post type metadata)
	* @since: 1.0.0
	*/
	public function alternative_quote() {
		
		$content  = $this->getMeta('item_quote_content');
		$author   = $this->getMeta('item_quote_author');
		
		$quote = null;

		if (!empty($content)) {
			$quote['quote_data']['content'] = (!empty($content)) ? $content : null;
			$quote['quote_data']['author']  = (!empty($author)) ? $author : null;
		}
		
		return $quote;
	}
	
	/**
	* get alternative link (post type metadata)
	* @since: 1.0.0
	*/
	public function alternative_link() {
		
		$content = $this->getMeta('item_link_content');
		$url = $this->getMeta('item_link_url');
		
		$link = null;

		if (!empty($url)) {
			$link['link_data']['content']  = (!empty($url)) ? $content : null;
			$link['link_data']['url'] = (!empty($url)) ? $url : null;
		}
		
		return $link;
	}
	
	/**
	* get alternative audio (post type metadata)
	* @since: 1.0.0
	*/
	public function alternative_audio() {
		
		$audio = null;
		
		$mp3   = $this->getMeta('item_mp3');
		$ogg   = $this->getMeta('item_ogg');
		$soundcloud  = $this->getMeta('item_soundcloud');
		
		$audio['media_type'] = 'audio';
		
		if (!empty($mp3) || !empty($ogg)) {
			$audio['media_data']['type'] = 'audio';
			if (!empty($mp3)) {$audio['media_data']['mp3']['url'] = $mp3;}
			if (!empty($ogg)) {$audio['media_data']['ogg']['url'] = $ogg;}
		} else if (!empty($soundcloud)) {
			$audio['media_data']['type'] = 'soundcloud';
			$audio['media_data']['ID'] = $soundcloud;
		}
		
		return $audio;
	}
	
	/**
	* get alternative audio (post type metadata)
	* @since: 1.0.0
	*/
	public function alternative_video() {
		
		$video   = null;
		
		$mp4     = $this->getMeta('item_mp4');
		$ogv     = $this->getMeta('item_ogv');
		$webm    = $this->getMeta('item_webm');
		$youtube = $this->getMeta('item_youtube');
		$vimeo   = $this->getMeta('item_vimeo');
		$wistia  = $this->getMeta('item_wistia');
		
		$video['media_type'] = 'video';
		
		if (!empty($mp4) || !empty($ogv) || !empty($webm)) {
			$video['media_data']['type'] = 'video';
			if (!empty($mp4))  {$video['media_data']['mp4']['url']  = $mp4;}
			if (!empty($webm)) {$video['media_data']['webm']['url'] = $webm;}
			if (!empty($ogv))  {$video['media_data']['ogv']['url']  = $ogv;}
		} else if (!empty($youtube)) {
			$video['media_data']['type'] = 'youtube';
			$video['media_data']['ID'] = $youtube;
		} else if (!empty($vimeo)) {
			$video['media_data']['type'] = 'vimeo';
			$video['media_data']['ID'] = $vimeo;
		} else if (!empty($wistia)) {
			$video['media_data']['type'] = 'wistia';
			$video['media_data']['ID'] = $wistia;
		}
		
		return $video;
	}

	/**
	* Setup the markup for each format type
	* @since: 1.0.0
	*/
	public function get_media_markup($content,$poster) {
		
		$markup = null;
		
		$type = $content['media_type'];

		// Force video format to image format and set image thanks to poster if video in lightbox
		if ($this->video_lightbox && in_array($type,array('youtube','vimeo','wistia','video'))) {
			$content['media_data'] = $poster;
			$type = 'image';
		}

		switch ($type) {
			case 'image':
				$markup = $this->image_markup($content,$poster);
				break;
			case 'gallery':
				$markup = $this->gallery_markup($content,$poster);
				break;
			case 'video':
				$type = $content['media_data']['type'];
				switch ($type) {
					case 'youtube':
						$markup = $this->youtube_markup($content,$poster);
						break;
					case 'vimeo':
						$markup = $this->vimeo_markup($content,$poster);
						break;
					case 'wistia':
						$markup = $this->wistia_markup($content,$poster);
						break;
					case 'video':
						$markup = $this->video_markup($content,$poster);
						break;
				}	
				break;
			case 'audio':
				$type = $content['media_data']['type'];
				switch ($type) {
					case 'soundcloud':
						$markup = $this->soundcloud_markup($content,$poster);
						break;
					case 'audio':
						$markup = $this->audio_markup($content,$poster);
						break;
				}
				break;
		}
		
		return $markup;
	}
	
	/**
	* Image markup
	* @since: 1.0.0
	*/
	public function image_markup($content,$poster) {
		
		global $tg_skins_preview;
		
		$markup = null;
		
		$url = esc_url($content['media_data']['url']);
		
		if ($this->grid_style == 'grid') {
			$markup .= '<div class="tg-item-image" style="background-image: url('.$url.')"></div>';
		} else {
			$alt    = (isset($content['media_data']['alt'])) ? esc_attr($content['media_data']['alt']) : '';
			$width  = (isset($content['media_data']['alt'])) ? esc_attr($content['media_data']['width']) : '';
			$height = (isset($content['media_data']['alt'])) ? esc_attr($content['media_data']['height']) : '';
			$markup .= '<img class="tg-item-image" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" src="'.$url.'">';
		}
		
		// get first gallery image product
		if (class_exists('WooCommerce') && !$tg_skins_preview && $this->post_type == 'product') {
			global $product;
			$attachment_ids = $product->get_gallery_attachment_ids();
			$image_link = (!empty($attachment_ids)) ? $this->image_data($attachment_ids[0]) : null;
			$markup .= (!empty($image_link)) ? '<div class="tg-item-image tg-alternative-product-image" style="background-image: url('.$image_link['url'].')"></div>' : null;
		}
		
		return $markup;
	}
	
	/**
	* Gallery markup
	* @since: 1.0.0
	*/
	public function gallery_markup($content,$poster) {
		
		$markup = '<div class="tg-item-gallery-holder">';
		$first_img = ' first-image show';
		
		if ($this->grid_style == 'grid') {
			foreach($content['media_data']['images'] as $image) {
				$url = esc_url($image['url']);
				$markup .= '<div class="tg-item-image'.$first_img.'" style="background-image: url('.$url.')"></div>';
				$first_img = null;
			}				
		} else {
			foreach($content['media_data']['images'] as $image) {
				$url = esc_url($image['url']);
				$alt = esc_attr($image['alt']);	
				if ($first_img) {
					$width  = esc_attr($image['width']);
					$height = esc_attr($image['height']);	
					$markup .= '<img class="tg-item-image'.$first_img.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" src="'.$url.'">';
				} else {
					$markup .= '<div class="tg-item-image" style="background-image: url('.$url.')"></div>';
				}
				$first_img = null;
			}
		}
		
		$markup .= '</div>';
		
		return $markup;
	}
	
	/**
	* Quote markup
	* @since: 1.0.0
	*/
	public function quote_markup($content) {

		$quote  = $content['quote_data']['content'];
		$author = $content['quote_data']['author'];
		
		$markup  = '<h2 class="tg-quote-content tg-item-title"><a href="'.get_permalink().'">'.esc_html($quote).'</a></h2>';
		$markup .= (!empty($author)) ? '<span class="tg-quote-author">'.esc_html($author).'</span>' : null;
		
		return $markup;
	}
	
	/**
	* Link markup
	* @since: 1.0.0
	*/
	public function link_markup($content) {

		$link = $content['link_data']['content'];
		$url  = $content['link_data']['url'];
		
		$markup  = '<h2 class="tg-link-content tg-item-title"><a href="'.get_permalink().'">'.esc_html($link).'</a></h2>';
		$markup .= (!empty($url)) ? '<a class="tg-link-url" href="'.get_permalink().'">'.esc_url($url).'</a>' : null;
		
		return $markup;
	}
	
	/**
	* Youtube markup
	* @since: 1.0.0
	*/
	public function youtube_markup($content,$poster) {
		$YT_ID   = $content['media_data']['ID'];
		$HTML_ID = 'YT-'.uniqid();
		$YT_URL  = 'https://www.youtube.com/embed/'.$content['media_data']['ID'].'?version=3&amp;enablejsapi=1&amp;html5=1&amp;controls=1&amp;autohide=1&amp;rel=0&amp;showinfo=0';
		$ratio   = $this->getMeta('item_youtube_ratio','4:3');
		$markup  = '<div class="tg-item-media-inner" data-ratio="'.esc_attr($ratio).'">';
		if ($this->iframe_poster) {
			$markup .= '<iframe class="tg-item-youtube tg-item-media" src="about:blank" data-src="'.esc_url($YT_URL).'" data-api="'.$this->iframe_poster.'" id="'.esc_attr($HTML_ID).'" allowfullscreen></iframe>';
		} else {
			$markup .= '<iframe class="tg-item-youtube tg-item-media" src="'.esc_url($YT_URL).'" id="'.esc_attr($HTML_ID).'" allowfullscreen></iframe>';
		}
		$markup .= '</div>';
		$markup .= $poster['markup'];
		return $markup;
	}
	
	/**
	* Vimeo markup
	* @since: 1.0.0
	*/
	public function vimeo_markup($content,$poster) {
		$VM_ID   = $content['media_data']['ID'];
		$HTML_ID = 'VM-'.uniqid(); //dechex(microtime(true)) more unique finally than uniqid() when browser history...
		$VM_URL  = 'https://player.vimeo.com/video/'.$content['media_data']['ID'].'?title=0&amp;byline=0&amp;portrait=0&amp;api=1&amp;player_id='.$HTML_ID;
		$ratio   = $this->getMeta('item_vimeo_ratio','4:3');
		$markup  = '<div class="tg-item-media-inner" data-ratio="'.esc_attr($ratio).'">';
		if ($this->iframe_poster) {
			$markup .= '<iframe class="tg-item-vimeo tg-item-media" src="about:blank" data-src="'.esc_url($VM_URL).'" data-api="'.$this->iframe_poster.'" id="'.esc_attr($HTML_ID).'" allowfullscreen></iframe>';
		} else {
			$markup .= '<iframe class="tg-item-vimeo tg-item-media" src="'.esc_url($VM_URL).'" id="'.esc_attr($HTML_ID).'" allowfullscreen></iframe>';
		}
		$markup .= '</div>';
		$markup .= $poster['markup'];
		return $markup;
	}
	
	/**
	* Wistia markup
	* @since: 1.0.7
	*/
	public function wistia_markup($content,$poster) {
		$WT_ID   = $content['media_data']['ID'];
		$HTML_ID = 'WT-'.uniqid();
		$WT_URL  = 'https://fast.wistia.net/embed/iframe/'.$content['media_data']['ID'].'?version=3&enablejsapi=1&html5=1&controls=1&autohide=1&rel=0&showinfo=0';
		$ratio   = $this->getMeta('item_wistia_ratio','4:3');
		$markup  = '<div class="tg-item-media-inner" data-ratio="'.esc_attr($ratio).'">';
		if ($this->iframe_poster) {
			$markup .= '<iframe class="tg-item-wistia wistia_embed tg-item-media" src="about:blank" data-src="'.esc_url($WT_URL).'" data-api="'.$this->iframe_poster.'" id="'.esc_attr($HTML_ID).'" allowfullscreen></iframe>';
		} else {
			$markup .= '<iframe name="wistia_embed" class="tg-item-wistia wistia_embed tg-item-media" src="'.esc_url($WT_URL).'" id="'.esc_attr($HTML_ID).'" allowfullscreen></iframe>';
		}
		$markup .= '</div>';
		$markup .= $poster['markup'];
		return $markup;
	}
	
	/**
	* Video markup
	* @since: 1.0.0
	*/
	public function video_markup($content,$poster) {
		$ratio   = $this->getMeta('item_video_ratio','4:3');
		$markup  = '<div class="tg-item-media-inner" data-ratio="'.esc_attr($ratio).'">';
		$markup .= '<video class="tg-item-video-player tg-item-media" poster="'.$poster['url'].'" controls style="width:100%;height:100%">';
		foreach($content['media_data'] as $key => $value ){
			if (in_array($key,array('mp4','webm','ogv'))) {
				$key = ($key == 'ogv') ? 'ogg' : $key;
				$markup .= '<source src="'.esc_url($value['url']).'" type="video/'.esc_attr($key).'">';  
			}
		}
		$markup .= '</video>';
		$markup .= '</div>';
		$markup .= $poster['markup'];
		return $markup;
	}
	
	/**
	* SoundCloud markup
	* @since: 1.0.0
	*/
	public function soundcloud_markup($content,$poster) {
		$SC_ID   = 'SC-'.uniqid();
		$SC_URL  = '//w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$content['media_data']['ID'].
		'&amp;auto_play=false&amp;hide_related=true&amp;visual=true&amp;show_artwork=true&amp;color=white_transparent';
		$markup  = '<div class="tg-item-media-inner" data-ratio="4:3">';
		if ($this->iframe_poster) {
			$markup .= '<iframe id="'.esc_attr($SC_ID).'" class="tg-item-soundcloud tg-item-media" data-api="'.$this->iframe_poster.'" src="about:blank" data-src="'.esc_url($SC_URL).'"></iframe>';
		} else {
			$markup .= '<iframe id="'.esc_attr($SC_ID).'" class="tg-item-soundcloud tg-item-media" src="'.esc_url($SC_URL).'"></iframe>';
		}
		$markup .= '</div>';
		$markup .= '<div class="tg-item-media-poster tg-item-media-soundcloud tg-item-button-play"></div>';
		$markup .= $poster['markup'];
		return $markup;
	}
	
	/**
	* Audio markup
	* @since: 1.0.0
	*/
	public function audio_markup($content,$poster) {
		
		$markup  = null;
		
		$class = (!empty($poster['url'])) ? ' has-media-poster' : null;
		$class = (!$this->media_poster) ? $class.' no-index' : $class;
		
		$markup .= '<div class="tg-item-media-inner'.esc_attr($class).'">';
		$markup .= '<audio class="tg-item-audio-player tg-item-media" controls>';

		foreach($content['media_data'] as $key => $value ){
			if (in_array($key,array('mp3','ogg'))) {
				$markup .= '<source src="'.esc_url($value['url']).'" type="audio/'.esc_attr($key).'">';  
			}
		}
		
		$markup .= '</audio>';
		
		if ($this->grid_style == 'masonry') {
			$markup .= (!empty($poster['url'])) ? '<img class="tg-item-audio-poster" width="'.$poster['width'].'" height="'.$poster['height'].'" alt="'.$poster['alt'].'" src="'.$poster['url'].'">' : null;
		} else {
			$markup .= (!empty($poster['markup'])) ? str_replace('tg-item-media-poster','tg-item-audio-poster',$poster['markup']) : null;
		}
		
		$markup .= '</div>';
		
		return $markup;
	}
	
	/**
	* Media Poster markup
	* @since: 1.0.0
	*/
	public function media_poster($content) {
		
		$type  = $content['media_data']['type'];
		$media_poster['markup'] = null;
		$media_poster['url']    = null;
		
		if ($this->media_poster || $this->video_lightbox || ($type == 'audio' && $this->grid_style == 'grid')) {
			
			$alt_img = $this->alternative_image();

			if (!empty($alt_img['media_data'])) {
				$poster = $this->image_data($alt_img['media_data']);
			} else if (in_array($type,array('youtube','vimeo','soundcloud','wistia'))) {
				$poster['url'] = $this->embed_thumbnail($content);
			} else if ($type == 'audio') {
				$default_img   = $this->default_image();
				$default_img   = (!empty($default_img['media_data'])) ? $this->image_data($default_img['media_data']) : null;
				$poster['url'] = $default_img['url'];
			}
			if (isset($poster['url']) && !empty($poster['url'])) {
				$media_poster['url']    = $poster['url'];
				$media_poster['width']  = (isset($poster['width'])) ? $poster['width'] : 500;
				$media_poster['height'] = (isset($poster['height'])) ? $poster['height'] : 500;
				$media_poster['alt']    = (isset($poster['alt'])) ? $poster['alt'] : null;
				$media_poster['markup'] = (!empty($poster)) ? '<div class="tg-item-media-poster" style="background-image: url('.esc_url($poster['url']).')"></div>' : null;
			}
			
		} else {
			// for html5 video poster
			$alt_img = $this->alternative_image();
			$poster  = $this->image_data($alt_img['media_data']);
			$media_poster['url']    = $poster['url'];
			$media_poster['width']  = (isset($poster['width'])) ? $poster['width'] : 500;
			$media_poster['height'] = (isset($poster['height'])) ? $poster['height'] : 500;
			$media_poster['alt']    = (isset($poster['alt'])) ? $poster['alt'] : null;
		}
		
		return $media_poster;
	}	
	
	/**
	* Get Youtube/Vimeo thumbnail
	* @since: 1.0.0
	*/
	public function embed_thumbnail($content) {
		
		$poster = null;
		
		$video_type = $content['media_data']['type'];
		$video_ID   = $content['media_data']['ID'];
		
		switch ($video_type) {
			case 'vimeo':
				$thumbnail = wp_remote_get( 'http://vimeo.com/api/v2/video/'.$video_ID.'.json' );
				if ($thumbnail) {
					$body   = json_decode( $thumbnail['body'] );
					$poster = $body[0]->thumbnail_large;
				}
				break;
			case 'wistia':
				$thumbnail = wp_remote_get( 'http://fast.wistia.com/oembed?url=http%3A%2F%2Fhome.wistia.com%2Fmedias%2F'.$video_ID.'.json');
				if ($thumbnail) {
					$body   = json_decode( $thumbnail['body'] );
					$poster = $body->thumbnail_url;
				}
				break;
			case 'youtube':
				$poster = '//img.youtube.com/vi/'.$video_ID.'/sddefault.jpg';
				break;
			case 'soundcloud':
				$client_ID = '226a27261125c8452c8b002d5731f5ca'; // general & public client ID from Themeone
				$thumbnail = wp_remote_get('http://api.soundcloud.com/tracks/'.$content['media_data']['ID'].'?client_id='.$client_ID);
				if ($thumbnail) {
					$body   = json_decode( $thumbnail['body'] );
					$poster = $body->artwork_url;
					$poster = str_replace('large', 't500x500', $poster);
				}
		}
		
		return $poster;
	}
	
	/**
	* Smart image size detection based on max value for column_width/window_width
	* @since: 1.0.0
	*/
	public function column_size() {
		
		// build width/col array
		$grid_cols = array(
			$this->grid_data['columns'][0][0]/$this->grid_data['columns'][0][1],
			$this->grid_data['columns'][1][0]/$this->grid_data['columns'][2][1],
			$this->grid_data['columns'][2][0]/$this->grid_data['columns'][3][1],
			$this->grid_data['columns'][3][0]/$this->grid_data['columns'][4][1],
			$this->grid_data['columns'][4][0]/$this->grid_data['columns'][5][1],
			1920/$this->grid_data['columns'][5][1],
		);
		
		// get maximum width based on colNb and window width
		$col_width = round(max($grid_cols));
		
		// Get image ratio
		$item_x_ratio = $this->grid_data['item_x_ratio'];
		$item_y_ratio = $this->grid_data['item_y_ratio'];
		$item_ratio   = number_format((float)$item_x_ratio/$item_y_ratio, 2, '.', '');
		
		// calculate height based on width & ratio
		$col_height   = round($col_width/$item_ratio);
		
		$col_size['height'] = $col_height;
		$col_size['width']  = $col_width;
		
		return $col_size;
	}
	
	/**
	* Get image data (url,width,height,type,alt,title) for html5/SEO
	* @since: 1.0.0
	*/
	public function image_data($img_ID) {
		
		if (empty($img_ID)) {
			return false;
		}

		$grid_base  = $this->grid_base;
		$aq_resizer = $this->grid_data['aqua_resizer'];

		if (is_numeric($img_ID)) {

			if ($aq_resizer == true) {
				
				$img_full = wp_get_attachment_url($img_ID,'full');
				$col_size = $this->column_size();
				
				$grid_style      = $this->grid_data['style'];
				$item_force_size = $this->grid_data['item_force_size'];
				
				if ($item_force_size == true) {
					$img_height = $col_size['height']*$this->grid_data['items_row'];
					$img_width  = $col_size['width']*$this->grid_data['items_col'];
				} else {
					$img_height = $col_size['height']*$this->getMeta('item_row');
					$img_width  = $col_size['width']*$this->getMeta('item_col');
				}
				
				// use aqua_resizer to resize on fly
				if ($grid_style == 'grid') {
					$img_info = tgaq_resize($img_full,$img_width,$img_height,true,false);
				} else {
					$img_info = tgaq_resize($img_full,$img_width,99999,false,false);
				}
				
				if (empty($img_info)) {
					$img_info = wp_get_attachment_image_src($img_ID, 'full');
				}

			} else {
				$img_size = $this->grid_data['image_size'];
				$img_info = wp_get_attachment_image_src($img_ID, $img_size); 
			}
			$img_original      = wp_get_attachment_image_src($img_ID, 'full');
			$img_original      = $img_original[0];
			$img_data['alt']   = get_post_meta($img_ID, '_wp_attachment_image_alt', true);
			$img_data['title'] = get_the_title($img_ID);
			
		} else {
			$img_original = $img_ID;
			$img_info[0]  = $img_ID;
			$img_info[1]  = 500;
			$img_info[2]  = 500;
			$img_data['alt'] = null;
			$img_data['title'] = null;
		}

		// format array info media
		if (!empty($img_info[0])) {
			$img_data['type']   = pathinfo($img_info[0], PATHINFO_EXTENSION);
			$img_data['url']    = $img_info[0];
			$img_data['lb_url'] = $img_original;
			$img_data['width']  = $img_info[1];
			$img_data['height'] = $img_info[2];
		} else {
			$img_data = null;
		}
		
		return $img_data;

	}
	
	/**
	* Get lightbox markup fo each type of media and different lighbox plugin
	* @since: 1.0.0
	* @modified 1.0.7
	*/
	public function get_lightbox($media) {
		
		$type       = get_option('the_grid_lightbox', 'the_grid');
		$media_type = $media['media_data']['type'];
		$lightbox   = null;
		
		// if no poster then no lightbox except for image
		if ($this->media_poster || $this->video_lightbox || !in_array($media_type,array('youtube','vimeo','wistia','video','soundcloud','audio'))) {
			
			switch ($media_type) {
				case 'youtube':
					if ($this->video_lightbox) {
						$lightbox = $this->get_youtube_lightbox($type,$media);
					} else {
						$lightbox = '<div class="tg-media-button tg-item-button-play">'. $this->icons[$media_type] .'</div>';
					}
					break;
				case 'vimeo':
					if ($this->video_lightbox) {
						$lightbox = $this->get_vimeo_lightbox($type,$media);
					} else {
						$lightbox = '<div class="tg-media-button tg-item-button-play">'. $this->icons[$media_type] .'</div>';
					}
					break;
				case 'wistia':
					if ($this->video_lightbox) {
						$lightbox = $this->get_wistia_lightbox($type,$media);
					} else {
						$lightbox = '<div class="tg-media-button tg-item-button-play">'. $this->icons[$media_type] .'</div>';
					}
					break;
				case 'video':
					if ($this->video_lightbox) {
						$lightbox = $this->get_video_lightbox($type,$media);
					} else {
						$lightbox = '<div class="tg-media-button tg-item-button-play">'. $this->icons[$media_type] .'</div>';
					}
					break;
				case 'soundcloud':
					$lightbox = '<div class="tg-media-button tg-item-button-play">'. $this->icons[$media_type] .'</div>';
					break;
				case 'audio':
					$lightbox = '<div class="tg-media-button tg-item-button-play">'. $this->icons[$media_type] .'</div>';
					break;
				default:
					$lightbox = $this->get_image_lightbox($type,$media);
					break;
			}
			
		}
		return $lightbox;
	}
	
	/**
	* Get youtube lightbox markup
	* @since: 1.0.0
	* @modified 1.0.7
	*/
	public function get_youtube_lightbox($type,$media) {
		
		$lightbox   = null;
		
		$icons = $this->icons['youtube'];
		$youtube_ID = esc_attr($media['media_data']['ID']);
		
		switch ($type) {
			case 'prettyphoto':
				$lightbox = '<a class="tg-media-button" href="http://www.youtube.com/watch?v='.$youtube_ID.'" rel="prettyPhoto" title="">'.$icons.'</a>';
				break;
			case 'fancybox':
				$lightbox = '<a class="tg-media-button fancybox iframe" href="http://www.youtube.com/embed/'.$youtube_ID.'?autoplay=0&wmode=opaque">'.$icons.'</a>';
				break;
			case 'foobox':
				$lightbox = '<a class="tg-media-button foobox" href="http://www.youtube.com/embed/'.$youtube_ID.'?autoplay=0&wmode=opaque" rel="foobox">'.$icons.'</a>';
				break;
			case 'the_grid':
				$lightbox = '<div class="tg-media-button" data-tolb-src="'.$youtube_ID.'" data-tolb-type="youtube" data-tolb-alt="">'.$icons.'</div>';
				break;
		}
		
		return $lightbox;
	}
	
	/**
	* Get vimeo lightbox markup
	* @since: 1.0.0
	* @modified 1.0.7
	*/
	public function get_vimeo_lightbox($type,$media) {
		
		$lightbox = null;
		
		$icons = $this->icons['vimeo'];
		$vimeo_ID = esc_attr($media['media_data']['ID']);
		
		switch ($type) {
			case 'prettyphoto':
				$lightbox = '<a class="tg-media-button" href="http://vimeo.com/'.$vimeo_ID.'" rel="prettyPhoto">'.$icons.'</a>';
				break;
			case 'fancybox':
				$lightbox = '<a class="tg-media-button fancybox iframe" href="//player.vimeo.com/video/'.$vimeo_ID.'">'.$icons.'</a>';
				break;
			case 'foobox':
				$lightbox = '<a class="tg-media-button foobox" href="http://vimeo.com/'.$vimeo_ID.'" rel="foobox">'.$icons.'</a>';
				break;
			case 'the_grid':
				$lightbox = '<div class="tg-media-button" data-tolb-src="'.$vimeo_ID.'" data-tolb-type="vimeo" data-tolb-alt="">'.$icons.'</div>';
				break;
		}
		
		return $lightbox;
		
	}
	
	/**
	* Get wistia lightbox markup
	* @since: 1.0.7
	*/
	public function get_wistia_lightbox($type,$media) {
		
		$lightbox = null;
		
		$icons = $this->icons['wistia'];
		$wistia_ID = esc_attr($media['media_data']['ID']);

		switch ($type) {
			case 'prettyphoto':
				$lightbox = '<div class="tg-media-button" data-tolb-src="'.$wistia_ID.'" data-tolb-type="wistia" data-tolb-alt="">'.$icons.'</div>';
				break;
			case 'fancybox':
				$lightbox = '<a class="tg-media-button fancybox iframe" href="https://fast.wistia.net/embed/iframe/'.$wistia_ID.'">'.$icons.'</a>';
				break;
			case 'foobox':
				$lightbox = '<div class="tg-media-button" data-tolb-src="'.$wistia_ID.'" data-tolb-type="wistia" data-tolb-alt="">'.$icons.'</div>';
				break;
			case 'the_grid':
				$lightbox = '<div class="tg-media-button" data-tolb-src="'.$wistia_ID.'" data-tolb-type="wistia" data-tolb-alt="">'.$icons.'</div>';
				break;
		}
		
		return $lightbox;
		
	}
	
	/**
	* Get html5 video lightbox markup
	* @since: 1.0.0
	* @modified 1.1.0
	*/
	public function get_video_lightbox($type,$media) {
		
		$lightbox = null;
		
		$icons = $this->icons['video'];
		$mp4  = (isset($media['media_data']['mp4']['url'])  && !empty($media['media_data']['mp4']['url']))  ? esc_url($media['media_data']['mp4']['url'])  : null;
		$ogv  = (isset($media['media_data']['ogv']['url'])  && !empty($media['media_data']['ogv']['url']))  ? esc_url($media['media_data']['ogv']['url'])  : null;
		$webm = (isset($media['media_data']['webm']['url']) && !empty($media['media_data']['webm']['url'])) ? esc_url($media['media_data']['webm']['url']) : null;

		switch ($type) {
			case 'prettyphoto':
				$HTML_ID   = 'V'.uniqid();
				$lightbox  = '<a class="tg-media-button" href="#'.$HTML_ID.'" rel="prettyPhoto">'.$icons.'</a>';
				$lightbox .= '<div style="display:none" id="'.$HTML_ID.'">';
				$lightbox .= '<video controls style="height:280px;width:500px">';
				$lightbox .= ($mp4)  ? '<source src="'.$mp4.'" type="video/mp4">'   : null;
				$lightbox .= ($webm) ? '<source src="'.$webm.'" type="video/webm">' : null;
				$lightbox .= ($ogv)  ? '<source src="'.$ogv.'" type="video/ogg">'   : null;
				$lightbox .= '</video>';
				$lightbox .= '</div>';
				break;
			case 'fancybox':
				$video = ($mp4) ? $mp4 : null;
				$video = ($ogv  && $mp4) ? $ogv : $video;
				$video = ($webm && $ogv) ? $webm : $video;
				$lightbox  = '<a class="tg-media-button fancybox iframe" href="'.$video.'">'.$icons.'</a>';		
				break;
			case 'foobox':
				$HTML_ID   = 'V'.uniqid();
				$lightbox  = '<a class="tg-media-button foobox" target="foobox" href="#'.$HTML_ID.'" data-height="360" data-width="640">'.$icons.'</a>';
				$lightbox .= '<div style="display:none" id="'.$HTML_ID.'">';
				$lightbox .= '<video controls>';
				$lightbox .= ($mp4)  ? '<source src="'.$mp4.'" type="video/mp4">'   : null;
				$lightbox .= ($webm) ? '<source src="'.$webm.'" type="video/webm">' : null;
				$lightbox .= ($ogv)  ? '<source src="'.$ogv.'" type="video/ogg">'   : null;
				$lightbox .= '</video>';
				$lightbox .= '</div>';
				break;
			case 'the_grid':
				$source = array();
				$mp4  = ($mp4)  ? array_push($source, '[{"type":"mp4","source":"'.$mp4.'"}]')   : null;
				$ogv  = ($ogv)  ? array_push($source, '[{"type":"ogg","source":"'.$ogv.'"}]')   : null;
				$webm = ($webm) ? array_push($source, '[{"type":"webm","source":"'.$webm.'"}]') : null;
				$source = ($source) ? implode(',', $source) : null;
				$poster = (isset($media['media_poster']['url']) && !empty($media['media_poster']['url'])) ? ' data-tolb-poster="'.$media['media_poster']['url'].'"' : null;
				$lightbox = '<div class="tg-media-button" data-tolb-src=\'['.$source.']\' data-tolb-type="'.$media['media_data']['type'].'" data-tolb-alt=""'.$poster.'>'.$icons.'</div>';
				break;
		}
		
		return $lightbox;
		
	}
	
	/**
	* Get image/gallery lightbox markup
	* @since: 1.0.0
	* @modified 1.0.7
	*/
	public function get_image_lightbox($type,$media) {
		
		global $tg_skins_preview;
		
		$lightbox = null;
		
		$icons = $this->icons['image'];
		$image = ($media['media_data']['type'] == 'gallery') ? esc_url($media['media_data']['images'][0]['lb_url']) : esc_url($media['media_data']['lb_url']);
		$title = ($media['media_data']['type'] == 'gallery') ? esc_attr($media['media_data']['images'][0]['title']) : esc_attr($media['media_data']['title']);
		$alt   = ($media['media_data']['type'] == 'gallery') ? esc_attr($media['media_data']['images'][0]['alt']) : esc_attr($media['media_data']['alt']);
		$alt   = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
		
		$type  = ($tg_skins_preview) ? 'the_grid' : $type;
		$class = ($tg_skins_preview) ? ' tolb-disabled' : null;
		
		switch ($type) {
			case 'prettyphoto':
				$lightbox = '<a class="tg-media-button" href="'.$image.'" rel="prettyPhoto" title="'.$alt.'">'.$icons.'</a>';
				break;
			case 'fancybox':
				$lightbox = '<a class="tg-media-button fancybox" href="'.$image.'">'.$icons.'</a>';
				if ($media['media_data']['type'] == 'gallery' && count($media['media_data']['images']) > 1) {
					for ($i = 1; $i < count($media['media_data']['images']); $i++) {
						$image = esc_url($media['media_data']['images'][$i]['lb_url']);
						$lightbox .= '<a class="tg-hidden-tag fancybox" href="'.$image.'"></a>';
					}
				}
				break;
			case 'foobox':
				$lightbox = '<a class="tg-media-button foobox" href="'.$image.'" title="'.$alt.'" rel="">'.$icons.'</a>';
				if ($media['media_data']['type'] == 'gallery' && count($media['media_data']['images']) > 1) {
					for ($i = 1; $i < count($media['media_data']['images']); $i++) {
						$image = esc_url($media['media_data']['images'][$i]['lb_url']);
						$title = esc_attr($media['media_data']['images'][$i]['title']);
						$alt   = esc_attr($media['media_data']['images'][$i]['alt']);
						$alt   = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
						$lightbox .= '<a class="tg-hidden-tag foobox" href="'.$image.'" title="'.$alt.'" rel=""></a>';
					}
				}
				break;
			case 'the_grid':
				$lightbox = '<div class="tg-media-button'.$class.'" data-tolb-src="'.$image.'" data-tolb-type="image" data-tolb-alt="'.$alt.'">'.$icons.'</div>';
				if ($media['media_data']['type'] == 'gallery' && count($media['media_data']['images']) > 1) {
					for ($i = 1; $i < count($media['media_data']['images']); $i++) {
						$image = esc_url($media['media_data']['images'][$i]['lb_url']);
						$title = esc_attr($media['media_data']['images'][$i]['title']);
						$alt   = esc_attr($media['media_data']['images'][$i]['alt']);
						$alt   = (!empty($alt)) ? ucfirst($alt) : ucfirst($title);
						$lightbox .= '<div class="tg-hidden-tag" data-tolb-src="'.$image.'" data-tolb-type="image" data-tolb-alt="'.$alt.'"></div>';
					}
				}
				break;
		}
		
		return $lightbox;
	}

}

if(!function_exists('The_Grid_Item_Content')) {
	/**
	* Tiny wrapper function
	* @since 1.0.0
	*/
	function The_Grid_Item_Content($options = '') {
		//$to_Item_Content = The_Grid_Item_Content::getInstance();
		//return $to_Item_Content->process($options);
		global $tg_grid_data;
		
		if ($tg_grid_data['source_type'] == 'post_type') {
			$to_Item_Content = The_Grid_Item_Post_Content::getInstance();
		} else {
			$to_Item_Content = The_Grid_Item_Social_Content::getInstance();
		}
		
		return $to_Item_Content->process($options);
	}
	
}