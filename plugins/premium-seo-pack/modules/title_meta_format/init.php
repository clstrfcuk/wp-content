<?php
/*
* Define class pspTitleMetaFormat
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspTitleMetaFormat') != true) {
    class pspTitleMetaFormat
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';
		
		static protected $_instance;
		
		//custom attributes
		private $plugin_settings = array();
		private $plugin_defsettings = array();

		static protected $tplChar = '{%s}';
		protected $pageTypes = array();
		
		static private $titleForce = false;
		
		private $post = null; // retrieve info for a specific post!
		
		protected $buddypress = null;
		

        /*
         * Required __construct() function that initalizes the AA-Team Framework
         */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/title_meta_format/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/title_meta_format/';
			$this->module = $this->the_plugin->cfg['modules']['title_meta_format'];
			
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_title_meta_format' );
			if ( empty($this->plugin_settings) ) {
				$this->plugin_defsettings = $this->load_module_options();
			}

			// buddy press utils
			if ( $this->the_plugin->is_buddypress() ) {
				require_once( 'buddypress.init.php' );
				$this->buddypress = new pspBuddyPressTags( $this->the_plugin );
			}
  
            // add extra pagetypes
            add_filter('premiumseo_seo_list_pagetypes', array($this, 'add_extra_list_pagetypes'), 10, 1);
            add_filter('premiumseo_seo_pagetype', array($this, 'get_extra_pagetype'), 10, 1);

			foreach ( $this->the_plugin->get_wp_list_pagetypes() as $k=>$v ) { //page types
				foreach ( array('_title', '_desc', '_kw', '_robots') as $kk=>$vv ) { //meta tags
					$alias = $v.$vv;
					if ( isset($this->plugin_settings[ $alias ]) ) {
						$this->pageTypes[ $alias ] = $this->plugin_settings[ $alias ];
					}
					else if ( isset($this->plugin_defsettings[ $alias ], $this->plugin_defsettings[ $alias ]['std']) ) {
						$this->pageTypes[ $alias ] = $this->plugin_defsettings[ $alias ]['std'];
					}
				}
			}
			//var_dump('<pre>', $this->pageTypes, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
    
			if ( !$this->the_plugin->verify_module_status( 'title_meta_format' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin !== true )
					$this->init();
			}
        }

		public function load_module_options() {
			$def = isset($this->the_plugin->title_meta_format_default) ? $this->the_plugin->title_meta_format_default : array();
			if ( ! empty($def) ) {
				return $def;
			}

			$options = array();

			// find if we have a options.php into the same folder
			$options_file = $this->module_folder_path . 'options.php';

			clearstatcache();
			if ( file_exists($options_file) && is_file($options_file) && is_readable($options_file) ) {

				$tryed_module = $this->the_plugin->cfg['modules']['title_meta_format'];

				ob_start();
				require_once $options_file;

				$content = ob_get_contents();
				ob_clean();

				if( trim($content) != "" ){
					$options = json_decode( $content, true );
				}
				
				$options = isset($options['psp_title_meta_format'], $options['psp_title_meta_format']['title_meta_format'], $options['psp_title_meta_format']['title_meta_format']['elements']) ? $options['psp_title_meta_format']['title_meta_format']['elements'] : array();
			}
			//var_dump('<pre>', $options, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$this->the_plugin->title_meta_format_default = $options;
			return $options;
		}

        public function add_extra_list_pagetypes( $pagetypes ) {
        	$new = array(); //array('product');
        	$pagetypes = array_merge( $pagetypes, $new );
			$pagetypes = array_unique( $pagetypes );
            return $pagetypes;
        }
        public function get_extra_pagetype( $pagetype ) {
			//if ( $pagetype == 'post' && function_exists('is_woocommerce') && function_exists('is_product') ) {
			//	if ( is_woocommerce() && is_product() ) {
			//		$pagetype = 'product';
			//	}
			//}
			return $pagetype;
        }

		public function setPostInfo( $post ) {
        	$this->post = $post;
        }
        
        public function verifyPostInfo() {
			if ( isset($this->post) && !is_null($this->post) && is_object($this->post) )
				return true;
			return false;
        }

        
        /**
         * Head Filters & Init!
         *
         */
		public function init() {
			// premiumseo_head action hook is used to group all of the generated tags made by this plugin!
			// @called in the frontend module
			add_action( 'premiumseo_head', array( &$this, 'the_meta_robots' ), 5 );
			add_action( 'premiumseo_head', array( &$this, 'the_meta_description' ), 10 );
			add_action( 'premiumseo_head', array( &$this, 'the_meta_keywords' ), 11 );
			add_action( 'premiumseo_head', array( &$this, 'the_canonical' ), 19 );
   
			//if ( self::$titleForce ) {
			$titleForce = !isset($this->plugin_settings['force_title']) ||
                ( isset($this->plugin_settings['force_title']) && $this->plugin_settings['force_title'] == 'yes' ) ? true : false;
			//$titleForce = self::$titleForce; //DEBUG    
			if ( $titleForce ) {
				add_action('template_redirect', array(&$this, 'head_before'), 0);
				add_action('wp_head', array(&$this, 'head_after'), 9999);
			} else {
				add_filter( 'wp_title', array( $this, 'the_title' ), 14 );
			}
		}


		/**
		 * Force title rewrite
		 *
		 */
		public function do_title_rewrite() {
			return ( !is_admin() && !is_feed() );
		}

		public function head_before() {
			if ( $this->do_title_rewrite() ) {
				ob_start( array($this, 'head_title_tag') );
			}
		}

		public function head_after() {
			if ($this->do_title_rewrite()) {
				$handlers = ob_list_handlers();
				if ( count($handlers) > 0 && strcasecmp($handlers[ count($handlers) - 1 ], 'pspTitleMetaFormat::head_title_tag') == 0 ) {
					ob_end_flush();
				}
				else ; // "ob_list_handlers list found:\n" . print_r($handlers, true)

			}
		}

		public function head_title_tag($head) {
			$title = $this->the_title('');
			if ( !$title ) return $head;
			 
			// replace old title with the new title
			//return eregi_replace('<title>[^<]*</title>', '<title>'.$title.'</title>', $head);
			return preg_replace( '/<title.*?\/title>/i', '<title>'.$title.'</title>', $head );
			//return preg_replace('/<title>([^<]*)<\/title>/i', '<title>'.$title.'</title>', $head);
		}
		
		
		/**
		 * page URL (also use canonical if it's the case)
		 */
		public function the_url() {
			global $wp_query, $post;

			//$post = $wp_query->get_queried_object();
			if (isset($post->ID) && !is_null($post->ID) && $post->ID>0) 
				$__post = $post;
			else
				$__post = $wp_query->get_queried_object(); //get the post!


			$url = '';

			if ( is_singular() || $this->the_plugin->_is_blog_posts_page() || $this->the_plugin->is_shop() ) { //post|page|post_type

				$canonical = $this->the_canonical( false );
				if ( isset($canonical) && !empty($canonical) )
					$url = $canonical;
				if ( empty($url) )
					$url = get_permalink( $__post->ID );

			} else if ( is_home() || is_front_page() ) { //homepage

				$url = home_url( '/' );

			} else if ( is_category() || is_tag() || is_tax() ) {

				$canonical = $this->the_canonical( false );
				if ( isset($canonical) && !empty($canonical) )
					$url = $canonical;
				if ( empty($url) )
					$url = get_term_link( $__post, $__post->taxonomy );
					
			} elseif ( is_post_type_archive() ) {

				$post_type = get_query_var( 'post_type' );
				if ( is_array($post_type) ) {
					$post_type = reset($post_type); //get first element
				}
				$url = get_post_type_archive_link( $post_type );

			} elseif ( is_author() ) {

				$url = get_author_posts_url( $__post->ID, $__post->user_nicename );
				
			} elseif ( is_archive() ) {

				if ( is_date() ) {
					if ( is_day() ) {
						$url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
					}
					elseif ( is_month() ) {
						$url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
					}
					elseif ( is_year() ) {
						$url = get_year_link( get_query_var( 'year' ) );
					}
				}

			} else {

				//treat here cases for other page types!
				//if ( is_search() ) {
				//}

			}
			$url = apply_filters( 'premiumseo_seo_url', $url );
			return $url;
		}


		/**
		 * canonical URL
		 */
		public function the_canonical( $print=true ) {
			$canonical = $this->the_pagetype('canonical', 'format_canonical');
			$canonical = apply_filters( 'premiumseo_seo_canonical', $canonical );
			$canonical = esc_url( $canonical );

			if ( $print===false )
				return $canonical;
			if ( !empty($canonical) )
				echo '<link rel="canonical" href="' . $canonical . '"/>' . PHP_EOL;
		}
		
		protected function format_canonical() {
			global $wp_query, $post;
			
			//$post = $wp_query->get_queried_object();
			if (isset($post->ID) && !is_null($post->ID) && $post->ID>0) 
				$__post = $post;
			else
				$__post = $wp_query->get_queried_object(); //get the post!

			$__postType = $this->getPostType();
			if ( !empty($__postType) ) $__post = $this->post;


			$canonical = '';

        	if ( is_singular() || $this->the_plugin->_is_blog_posts_page() || $this->the_plugin->is_shop() || $__postType == 'post' ) {

				$__theMeta = $this->the_plugin->get_psp_meta( $__post->ID );
        	}
        	else if ( is_category() || is_tag() || is_tax() || $__postType == 'term' ) {

				$__objTax = (object) array('term_id' => $__post->term_id, 'taxonomy' => $__post->taxonomy);

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $__objTax );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$__theMeta = $this->the_plugin->get_psp_meta( $__objTax, $psp_current_taxseo );
        	}
        	
        	if ( is_singular() || $this->the_plugin->_is_blog_posts_page() || $this->the_plugin->is_shop()
				|| is_category() || is_tag() || is_tax() || !empty($__postType) ) {
        		
        		if ( isset($__theMeta['canonical']) ) {
					$canonical = $__theMeta['canonical'];
					if ( !empty($canonical) )
						$canonical = htmlspecialchars( $canonical );
        		}
        	}
        	return $canonical;
		}

        
		/**
		 * meta robots tags!
		 */
		public function the_meta_robots( $print=true ) {
        	$meta_robots = $this->the_pagetype('robots', 'format_robots_tags');
        	$m = $meta_robots;
        	$mi = isset($m['item']) ? (array) $m['item'] : array();
        	$mt = isset($m['generaltag']) ? (array) $m['generaltag'] : array();

        	$__meta_robots = array();
			// force to use the WP settings
			if ( '0' == get_option( 'blog_public' ) || isset($_GET['replytocom']) )
				$__meta_robots[] = 'noindex';
			else if ( in_array('index', $mi) )
				$__meta_robots[] = 'index';
        	else if ( in_array('noindex', $mi) )
				$__meta_robots[] = 'noindex';
        	else if ( in_array('index', $mt) )
				$__meta_robots[] = 'index';
        	else if ( in_array('noindex', $mt) )
				$__meta_robots[] = 'noindex';
			else
				$__meta_robots[] = 'index';
			
        	if ( in_array('follow', $mi) )
				$__meta_robots[] = 'follow';
        	else if ( in_array('nofollow', $mi) )
				$__meta_robots[] = 'nofollow';
        	else if ( in_array('follow', $mt) )
				$__meta_robots[] = 'follow';
        	else if ( in_array('nofollow', $mt) )
				$__meta_robots[] = 'nofollow';
			else
				$__meta_robots[] = 'follow';

			$__meta_robots_extra = array();
        	if ( in_array('noarchive', $mt) )
				$__meta_robots_extra[] = 'noarchive';
        	if ( in_array('noodp', $mt) )
				$__meta_robots_extra[] = 'noodp';
			$__meta_robots_extra = implode(',', $__meta_robots_extra);
			
			$__meta_robots = implode(',', $__meta_robots);
			if ( ($found = preg_match('/^index,follow/i', $__meta_robots))!==false && $found>0 )
				$__meta_robots = '';

			$__meta_robots = $__meta_robots
				. ( $__meta_robots!='' && $__meta_robots_extra!='' ? ',' : '') . $__meta_robots_extra;

			$__meta_robots = apply_filters( 'premiumseo_seo_robots', $__meta_robots );
			$__meta_robots = esc_attr( $__meta_robots );

        	if ( $print===false )
        		return $__meta_robots;
        	if ( !empty( $__meta_robots ) )
        		echo '<meta name="robots" content="' . $__meta_robots . '"/>' . PHP_EOL;
		}
		
        protected function format_robots_tags($field, $type) {
        	$__field = 'robots';
        	
        	$__robots = array(
        		'item'			=> array(),
        		'generaltag'	=> array()
        	);
        	
        	//current field value!
 			$__currentValue = $this->get_current_field( 'all' );
 			$__cv = $__currentValue;

 			//current page values: robots index, follow
 			if ( !is_null($__cv) ) {
	 			if ( isset($__cv['robots_index']) && !empty($__cv['robots_index']) && $__cv['robots_index']!='' )
	 				$__robots['item'][] = $__cv['robots_index'];
	 			if ( isset($__cv['robots_follow']) && !empty($__cv['robots_follow']) && $__cv['robots_follow']!='' )
	 				$__robots['item'][] = $__cv['robots_follow'];
 			}

 			//pagination is active in plugin & current page has pagination
 			$__use_pag = isset($this->plugin_settings[ 'use_pagination_'.$field ]) ? $this->plugin_settings[ 'use_pagination_'.$field ] : 'no';
 			$__pag = isset($this->plugin_settings[ 'pagination_'.$field ]) ? $this->plugin_settings[ 'pagination_'.$field ] : '';
 			if ( isset($__use_pag) && $__use_pag=='yes'
 				&& isset($__pag) && !empty($__pag)
 				&& $this->is_pagination() ) {

 				$__currentValue = $__pag;
 				$__robots['generaltag'] = $__currentValue;
 			} else {
	 			//current page type!
	 			$this->set_pagetypes();
				$__templateValue = $this->get_template_field( $type, $field );
	 			if ( false !== $__templateValue ) {
	 				$__currentValue = $__templateValue;
					$__robots['generaltag'] = $__currentValue;
	 			}
 			}
 			
        	return $__robots;
        }
		
       
        /**
         * title, meta description, meta keywords!
         */
        public function the_title($title) {
        	$title = $this->the_pagetype('title');
        	$title = apply_filters( 'premiumseo_seo_title', $title );
        	$title = esc_html( strip_tags( stripslashes( $title  ) ) );

        	return $title;
        }
        
        public function the_meta_description( $print=true ) {
        	$meta_desc = $this->the_pagetype('desc');
        	$meta_desc = trim( $meta_desc );
        	$meta_desc = apply_filters( 'premiumseo_seo_meta_description', $meta_desc );
        	$meta_desc = esc_attr( strip_tags( stripslashes( $meta_desc ) ) );

        	if ( $print===false )
        		return $meta_desc;
        	if ( !empty( $meta_desc ) )
        		echo '<meta name="description" content="' . $meta_desc . '"/>' . PHP_EOL;
        }
        
        public function the_meta_keywords( $print=true ) {
        	$meta_keywords = $this->the_pagetype('kw');
        	$meta_keywords = trim( $meta_keywords );
			$meta_keywords = apply_filters( 'premiumseo_seo_meta_keywords', $meta_keywords );
        	$meta_keywords = esc_attr( strip_tags( stripslashes( $meta_keywords ) ) );
        	
        	if ( $print===false )
        		return $meta_keywords;
        	if ( !empty( $meta_keywords ) )
        		echo '<meta name="keywords" content="' . $meta_keywords . '"/>' . PHP_EOL;
        }
        
        protected function the_format($field, $type) {
        	switch ($field) {
        		case 'title':
        			$__field = 'title';
        			break;
        		case 'desc':
        			$__field = 'description';
        			break;
        		case 'kw':
        			$__field = 'keywords';
        			break;
        		default:
        			$__field = 'title';
        			break;
        	}

  			//current field value!
 			$__currentValue = $this->get_current_field( $__field );
 			if ( !is_null($__currentValue) && !empty($__currentValue) && $__currentValue!='' ) {
 				$on_page_optimization = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_on_page_optimization' );
				$meta_title_sufix = isset($on_page_optimization['meta_title_sufix']) ? $on_page_optimization['meta_title_sufix'] : '';
				if ( $field == 'title' && $type != 'home' && !empty($meta_title_sufix) ) $__currentValue .= ' ' . $meta_title_sufix;
 				return $__currentValue;
			}
   			
 			//pagination is active in plugin & current page has pagination
 			$__use_pag = isset($this->plugin_settings[ 'use_pagination_'.$field ]) ? $this->plugin_settings[ 'use_pagination_'.$field ] : 'no';
 			$__pag = isset($this->plugin_settings[ 'pagination_'.$field ]) ? $this->plugin_settings[ 'pagination_'.$field ] : '';

 			if ( isset($__use_pag) && $__use_pag=='yes'
 				&& isset($__pag) && trim($__pag)!=''
 				&& $this->is_pagination() ) {
 				$__currentValue = $__pag;
 			} else {
	 			//current page type!
	 			$this->set_pagetypes();
				$__templateValue = $this->get_template_field( $type, $field );
	 			if ( false !== $__templateValue ) {
	 				$__currentValue = $__templateValue;
	 			}
 			}

 			if ( empty($__currentValue) ) return '';

 			//format the page title!
        	$__return = $this->make_format(array(), $type, $__currentValue);
			//var_dump('<pre>', $__return , '</pre>');

        	return $__return;
        }

		protected function get_template_field( $type, $field='title' ) {
			$__currentValue = false;

	 		if ( isset($this->pageTypes[ $type . '_' . $field ]) ) {
	 			$__currentValue = $this->pageTypes[ $type . '_' . $field ];
	 		}
			// start compatibility with old version : posttype => post
			else if ( ( 'posttype' == $type ) && isset($this->pageTypes[ 'post_' . $field ]) ) {
				$__currentValue = $this->pageTypes[ 'post_' . $field ];
			}
			// end compatibility with old version : posttype => post

			// NOT ( custom post type or custom taxonomy )
			if ( ! in_array($type, array('posttype', 'taxonomy')) ) {
				return $__currentValue;
			}

			// custom post type or custom taxonomy
			$__post = null;

			global $wp_query, $post;
	 		if (is_object($post) && isset($post->ID) && !is_null($post->ID) && $post->ID>0)
	 			$__post = $post;
	 		if (is_object($wp_query))
	 			$__post = $wp_query->get_queried_object(); //get the post!
	 		//var_dump('<pre>', $__post, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

	 		$post_type = '';
			if (is_object($__post) && isset($__post->post_type) && $__post->post_type != '') {
				$post_type = (string) $__post->post_type;
			}
			if (is_object($__post) && isset($__post->term_id) && isset($__post->taxonomy)) {
				$post_type = (string) $__post->taxonomy;
			}
			//var_dump('<pre>',$post_type,$__post,'</pre>');
			
			$uniqueKey = "{$type}_custom";
			$o = $this->plugin_settings;
			if ( ! empty($post_type)
				&& isset($o["$uniqueKey"], $o["$uniqueKey"]["$field"], $o["$uniqueKey"]["$field"]["$post_type"])
			) {
				$o["$uniqueKey"]["$field"]["$post_type"] = $o["$uniqueKey"]["$field"]["$post_type"];

				if ( ! empty($o["$uniqueKey"]["$field"]["$post_type"]) ) {
					$__currentValue = $o["$uniqueKey"]["$field"]["$post_type"];
				}
			}

			return $__currentValue;
		}

        protected function get_current_field( $field='title' ) {
			global $wp_query, $post;

			//$post = $wp_query->get_queried_object();
			if (isset($post->ID) && !is_null($post->ID) && $post->ID>0) 
				$__post = $post;
			else
				$__post = $wp_query->get_queried_object(); //get the post!

			//if ( ($section = $this->the_plugin->is_buddypress_section()) && !empty($section) ) {
			//	if ( isset($section['action']) && !empty($section['action']) )
			//		global $post;
			//	else
			//		$post = $wp_query->get_queried_object();
			//} else
			//	$post = $wp_query->get_queried_object();

			$__postType = $this->getPostType();
			if ( !empty($__postType) ) $__post = $this->post;

			$value = '';

			$__theMeta = array();
        	if ( is_singular() || $this->the_plugin->_is_blog_posts_page() || $this->the_plugin->is_shop() || $__postType == 'post' ) {
     
				$post_id = (int) $__post->ID;

				if ( $post_id > 0 )
					$__theMeta = $this->the_plugin->get_psp_meta( $__post->ID );
        	}
        	else if ( is_category() || is_tag() || is_tax() || $__postType == 'term' ) {

				$__objTax = (object) array('term_id' => $__post->term_id, 'taxonomy' => $__post->taxonomy);

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $__objTax );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$__theMeta = $this->the_plugin->get_psp_meta( $__objTax, $psp_current_taxseo );
        	}

        	if ( is_singular() || $this->the_plugin->_is_blog_posts_page() || $this->the_plugin->is_shop()
				|| is_category() || is_tag() || is_tax() || !empty($__postType) ) {

				if ( $field=='all' )
					$value = $__theMeta;
				else {
					$value = '';
					if ( isset($__theMeta[ "$field" ]) ) {
						$value = $__theMeta[ "$field" ];
						if ( $field=='keywords' && empty($value) ) { //special case: keywords & focus keyword
							if ( isset($__theMeta[ 'focus_keyword' ]) && !empty($__theMeta[ 'focus_keyword' ]) )
								$value = $__theMeta[ 'focus_keyword' ];
						}
						if ( !empty($value) )
							$value = htmlspecialchars( $value );
					}
				}
        	}
        	return $value;
        }

        
        /**
         * current page type
         *
         */
        public function the_pagetype($field='title', $format_func='the_format') {
        	$page_type = $this->the_plugin->get_wp_pagetype();

			if ( in_array($page_type, array('admin', 'feed')) )
				return '';

			return call_user_func( array( $this, $format_func ), $field, $page_type );
        }


        /**
         * replace shortcodes with values!
         *
         */
        public function make_format($__replace_orig=array(), $type='home', $theContent='') {
 			global $wp_query;
   
 			if ( empty($theContent) ) return '';
 			$__return = $theContent;

 			$__post = null;
 			$__author = null;

            //$type = $type == 'product' ? 'post' : $type;
 			$__page = 'home'; //default page!
 			$__defaults = array( //default params!
 				'site_title'			=> get_bloginfo('name'), //website name
 				'site_description'		=> get_bloginfo('description'), //website description
 				'current_date'			=> date( get_option('date_format') ), //current date
 				'current_time'			=> date( get_option('time_format') ), //current time
				'current_day'   		=> date( 'j' ), //current day
				'current_year'  		=> date( 'Y' ), //current year
				'current_month' 		=> __( date( 'F' ), 'psp' ), //current month
				'current_week_day'		=> __( date( 'l' ), 'psp' ), //current week day

 				'id'					=> '',
 				'title'					=> '',
 				'date'					=> '',
 				'description'			=> '',
 				'short_description'		=> '',
 				'parent'				=> '',

 				'author'				=> '',
 				'author_username'		=> '',
 				'author_nickname'		=> '',
 				'author_description'	=> '',
 				
 				'categories'			=> '',
 				'tags'					=> '',
 				'terms'					=> '',

 				'category'				=> '',
 				'category_description'	=> '',
 				'tag'					=> '',
 				'tag_description'		=> '',
 				'term'					=> '',
				'term_description'		=> '',

 				'search_keyword'		=> '',

 				'keywords'				=> '',
 				'focus_keywords'		=> '',
				'multi_focus_keywords'	=> '',
 				
 				'totalpages'			=> '',
 				'pagenumber'			=> ''
 			);
			
 			//to be replaced params
 			$__replace = array_merge($__defaults, array(
 				'title'				=> get_bloginfo('name')
 			));
			
 			$__postClean = $__defaults;
 			$__authorClean = $__defaults;
 			$__taxonomyClean = $__defaults;

 			//loop through all page types and set some info!
 			//::
 			
 			//page type is: post or page (or attachment)
 			if (in_array($type, array('post', 'page', 'posttype'))) {
 				global $post;
 				if (isset($post->ID) && !is_null($post->ID) && $post->ID>0) 
 					$__post = $post;
 				else
 					$__post = $wp_query->get_queried_object(); //get the post!
 					
				$__postType = $this->getPostType();
				if ( !empty($__postType) && $__postType == 'post' ) $__post = $this->post;
				$__wpquery = ( !empty($__postType) && $__postType == 'post' ? $this->post : $wp_query->get_queried_object() );

 				$__postClean['id'] = $__post->ID;
				
 				if ( isset($__postClean['id']) && !is_null($__postClean['id']) && $__postClean['id']>0 ) {
 					//post title
					$__postClean['title'] = strip_tags( apply_filters( 'single_post_title', $__post->post_title ) );

 					//post date
 					if ( isset($__post->post_date) && !empty($__post->post_date) ) {
 						$__postClean['date'] = mysql2date( get_option( 'date_format' ), $__post->post_date );
 					}
 					
 					//post description
					$__postClean['description'] = strip_shortcodes( $__post->post_content );

 					//post short description!
 					if ( !empty($__post->post_excerpt) ) {
 						$__postClean['short_description'] = strip_tags( $__post->post_excerpt );
 					} else {
 						$__postClean['short_description'] = wp_html_excerpt( strip_shortcodes( $__post->post_content ), 200 );
 					}

 					//post parent
 					if ($__parentId = $__post->post_parent) {
 						$__parent = get_post($__parentId);
 						$__postClean['parent'] = strip_tags( apply_filters( 'single_post_title', $__parent->post_title ) );
 					}

 					//post author
					global $authordata;
	 				$__author = $authordata; //get the post author!

 					//post categories | tags | taxonomies
	 				$__taxonomyClean = array_merge($__taxonomyClean, 
	 					$this->get_taxonomy($type, $__wpquery)
	 				);
   
	 				//post custom - keywords & focus keyword!
	 				$psp_meta = $this->the_plugin->get_psp_meta( $__postClean['id'] );

	 				$__postClean['keywords'] = isset($psp_meta['keywords']) ? $psp_meta['keywords'] : '';

					//$__postClean['focus_keywords'] = get_post_meta( $__postClean['id'], 'psp_kw', true );
					$__postClean['focus_keywords'] = isset($psp_meta['focus_keyword']) ? $psp_meta['focus_keyword'] : '';

					$__postClean['multi_focus_keywords'] = isset($psp_meta['mfocus_keyword']) ? $psp_meta['mfocus_keyword'] : '';
					$__postClean['multi_focus_keywords'] = implode(', ', $this->the_plugin->mkw_get_keywords($__postClean['multi_focus_keywords']));

					if (empty($__postClean['keywords']) && !empty($__postClean['focus_keywords'])) {
						$__postClean['keywords'] = $__postClean['focus_keywords'];
					}
 				}
 			}
 			
 			//page type is: category | tag | taxonomy
 			if (in_array($type, array('category', 'tag', 'taxonomy'))) {
				$__postType = $this->getPostType();
				$__wpquery = ( !empty($__postType) && $__postType == 'term' ? $this->post : $wp_query->get_queried_object() );

 				$__taxonomyClean = array_merge($__taxonomyClean, 
 					$this->get_taxonomy($type, $__wpquery)
 				);
 			}
 			
 			//page type is: author
 			if ($type=='author') {
 				$__author = $wp_query->get_queried_object(); //get the post author!
 			}
 			
 			//page type is: archive
 			if ($type=='archive') {
 				$__date = '';
				if ( is_month() )
					$__date = single_month_title( ' ', false );
				else if ( is_year() )
					$__date = get_query_var( 'year' );
				else if ( is_day() )
					$__date = get_the_date();
 			}
 			
 			//::
 			//end loop through all page types and set some info!

 			//author info
 			if (!is_null($__author) && isset($__author->ID)) {
	 			$__authorClean = array_merge($__authorClean, array(
	 				'title'				=> $__author->display_name,
		 			'author'			=> $__author->display_name,
		 			'author_username'	=> $__author->user_login,
		 			'author_nickname'	=> get_the_author_meta( 'nickname', $__author->ID ),
		 			'author_description'=> get_the_author_meta( 'description', $__author->ID )
	 			));
 			}
 			
 			//pagination!
			$__paged = $this->pagination_info();
			$__replace = array_merge($__replace, array(
 				'totalpages'			=> $__paged['total'],
 				'pagenumber'			=> $__paged['current']
			));

			switch ($type) {
 				case 'home' 	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> get_bloginfo('name'),
 						'description'			=> get_bloginfo('description')
 					));
 					$__page = 'home';
 					break;

 				case 'post'		:
 					$__page = 'post';
 				case 'page'		:
 					$__page = 'page';
 				case 'posttype'	:
 					$__page = 'posttype';
 				case 'post'		:
 				case 'page'		:
				case 'posttype'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__postClean['title'],
 						'id'					=> $__postClean['id'],
 						'date'					=> $__postClean['date'],
 						'description'			=> $__postClean['description'],
 						'short_description'		=> $__postClean['short_description'],
 						'parent'				=> $__postClean['parent'],

 						'author'				=> $__authorClean['author'],
	 					'author_username'		=> $__authorClean['author_username'],
	 					'author_nickname'		=> $__authorClean['author_nickname'],
	 					'author_description'	=> $__authorClean['author_description'],
	 					
 						'categories'			=> $__taxonomyClean['categories'],
	 					'tags'					=> $__taxonomyClean['tags'],
	 					'terms'					=> $__taxonomyClean['terms'],

 						'category'				=> $__taxonomyClean['category'],
 						'category_description'	=> $__taxonomyClean['category_description'],
 						'tag'					=> $__taxonomyClean['tag'],
 						'tag_description'		=> $__taxonomyClean['tag_description'],
 						'term'					=> $__taxonomyClean['term'],
 						'term_description'		=> $__taxonomyClean['term_description'],
 						
 						'keywords'				=> $__postClean['keywords'],
 						'focus_keywords'		=> $__postClean['focus_keywords'],
 						'multi_focus_keywords'	=> $__postClean['multi_focus_keywords']
 					));
 					break;

				case 'category'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__taxonomyClean['title'],
 						'category'				=> $__taxonomyClean['category'],
 						'category_description'	=> $__taxonomyClean['category_description']
 					));
 					$__page = 'category';
 					break;
 					
				case 'tag'		:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__taxonomyClean['title'],
 						'tag'					=> $__taxonomyClean['tag'],
 						'tag_description'		=> $__taxonomyClean['tag_description']
 					));
 					$__page = 'tag';
 					break;
 					
				case 'taxonomy'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__taxonomyClean['title'],
 						'term'					=> $__taxonomyClean['term'],
 						'term_description'		=> $__taxonomyClean['term_description']
 					));
 					$__page = 'taxonomy';
 					break;
 					
				case 'archive'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__date,
 						'date'					=> $__date
 					));
 					$__page = 'archive';
 					break;
 					
 				case 'author'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__authorClean['title'],
 						'author'				=> $__authorClean['author'],
	 					'author_username'		=> $__authorClean['author_username'],
	 					'author_nickname'		=> $__authorClean['author_nickname'],
	 					'author_description'	=> $__authorClean['author_description']
 					));
 					$__page = 'author';
 					break;
 					
 				case 'search'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> esc_html( $wp_query->query_vars['s'] ),
 						'search_keyword'		=> esc_html( $wp_query->query_vars['s'] )
 					));
 					$__page = 'search';
					break;
					
 				case '404'		:
 					$__replace = array_merge($__replace, array(
 					));
 					$__page = '404';
 					break;

 				default			:
 					break;
 			}

            $__replace = array_merge($__replace_orig, $__replace);
   
			$__replace = apply_filters( 'premiumseo_seo_make_format', $__replace, $type, $theContent );
  
 			//replace shortcodes with values!
 			foreach ( $__replace as $shortcode => $value ) {
 				$__return = str_replace( sprintf(self::$tplChar, $shortcode), $value, $__return );
 			}
			//var_dump('<pre>', $__replace 	, '</pre>');

			$__return = preg_replace( '/\s+/u', ' ', $__return ); //clean multiple white spaces!
			return trim( $__return );
        }
        
        
        /**
         * pagination info!
         *
         */
        protected function is_pagination() {
        	$__ret = $this->pagination_info();
        	return $__ret['ispag'];
        }
        
        protected function pagination_info() {
        	global $wp_query;

        	$ret = array(
        		'ispag'		=> false,
        		'total'		=> 1,
        		'current'	=> 1
        	);
        	
			if ( is_paged() ) {
				$ret['ispag'] = true;
				$ret = array_merge($ret, array(
					'ispag'		=> true,
					'total' 	=> abs( intval( $wp_query->max_num_pages ) ),
					'current' 	=> abs( intval( get_query_var('paged') ) )
				));
				return $ret;
			} else if ( get_query_var('page') ) {
				$ret['ispag'] = true;
				if ( is_singular() ) {
					$post = $wp_query->get_queried_object();
					$ret['total'] = count( explode( '<!--nextpage-->', $post->post_content ) );
				}
				$ret['current'] = abs( intval( get_query_var('page') ) );
			}
			
			if ( $ret['total']<=1 ) $ret['total'] = 1;
			if ( $ret['current']<=1 ) $ret['current'] = 1;
			return $ret;
        }
        
        
        /**
         * get taxonomy info per pagetype: post | page | category | tag | taxonomy
         *
         */
        protected function get_taxonomy($type, $obj) {
        	global $wp_query;
        	$__taxonomyClean = array();

        	if (in_array($type, array('category', 'tag', 'taxonomy'))) {
				$__postType = $this->getPostType();
				if ( !empty($__postType) ) $post = $this->post;

	        	$tmpTitle = '';
	        	if ( function_exists( 'single_term_title' ) ) { //Since: 3.1.0 WP version
	        		$tmpTitle = single_term_title( '', false );
	        		if ( $__postType == 'term' ) $tmpTitle = '';
	        	}
	        	$tmpDesc = '';
	        	if ( function_exists( 'term_description' ) ) { //Since: 2.8.0 WP version
	        		$tmpDesc = term_description();
	        		if ( $__postType == 'term' ) $tmpDesc = '';
	        	}
	        	if ($type=='category') {
	        		$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : single_cat_title( '', false );
	        		$__taxonomyClean['category_description'] = $tmpDesc!='' ? $tmpDesc : category_description();
	        		if ( $__postType == 'term' ) {
	        			$__category = get_the_category(); $__categ = array('name' => '', 'desc' => '');
	        			if (isset($__category[0]) && $__category[0]) {
	        				$__categ['name'] = $__category[0]->cat_name;
	        				$__categ['desc'] = $__category[0]->description;

							if ( $__categ['name']!='' ) {
								$__taxonomyClean['title'] = $__categ['name'];
							}
							if ( $__categ['desc']!='' ) {
								$__taxonomyClean['category_description'] = $__categ['desc'];
							}
	        			}
	        		}

					if ( $__taxonomyClean['title']=='' ) {
						$__taxonomyClean['title'] = $obj->name;
					}
					if ( $__taxonomyClean['category_description']=='' ) {
						$__taxonomyClean['category_description'] = $obj->description;
					}
					//var_dump('<pre>',$__category[0], single_term_title( '', false ), single_cat_title( '', false ), $obj ,'</pre>');
	        		$__taxonomyClean['category'] = $__taxonomyClean['title'];
	        	} else if ($type=='tag') {
	        		$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : single_tag_title( '', false );
	        		$__taxonomyClean['tag_description'] = $tmpDesc!='' ? $tmpDesc : tag_description();
	        		if ( $__postType == 'term' ) {
	        			$__category = get_tags(); $__categ = array('name' => '', 'desc' => '');
	        			if (isset($__category[0]) && $__category[0]) {
	        				$__categ['name'] = $__category[0]->name;
	        				$__categ['desc'] = $__category[0]->description;

							if ( $__categ['name']!='' ) {
								$__taxonomyClean['title'] = $__categ['name'];
							}
							if ( $__categ['desc']!='' ) {
								$__taxonomyClean['tag_description'] = $__categ['desc'];
							}
	        			}
	        		}

					if ( $__taxonomyClean['title']=='' ) {
						$__taxonomyClean['title'] = $obj->name;
					}
					if ( $__taxonomyClean['tag_description']=='' ) {
						$__taxonomyClean['tag_description'] = $obj->description;
					}
					//var_dump('<pre>',$__category[0], single_term_title( '', false ), single_tag_title( '', false ), $obj ,'</pre>');
	        		$__taxonomyClean['tag'] = $__taxonomyClean['title'];
	        	} else {
	        		$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : $obj->name;
	        		$__taxonomyClean['term_description'] = $tmpDesc!='' ? $tmpDesc : $obj->description;
	        		$__taxonomyClean['term'] = $__taxonomyClean['title'];
	        	}
        	}
        	if (in_array($type, array('post', 'page', 'posttype'))) {
        		if ( ! is_object($obj) || ! isset($obj->ID) ) {
        			return $__taxonomyClean;
        		}
	        	if ( function_exists( 'get_the_terms' ) ) { //Since: 2.5.0 WP version
	        		$categories  = get_the_terms( $obj->ID, 'category' );
	        		$tags  = get_the_terms( $obj->ID, 'post_tag' );

	        		// get post type taxonomies
	        		$__taxonomies = get_object_taxonomies( $obj->post_type, 'objects' );
	        		$taxonomies = '';
	        		foreach ( $__taxonomies as $taxonomy_slug => $taxonomy ){
	        			if (in_array($taxonomy_slug, array('category', 'post_tag', 'post_format'))) continue 1;
	        			$taxonomies = get_the_terms( $obj->ID, $taxonomy_slug );
	        		}

	        		$__taxonomyClean = array(
		 				'categories'			=> $this->getTaxonomyItems( $categories ),
		 				'tags'					=> $this->getTaxonomyItems( $tags ),
		 				'taxonomies'			=> $this->getTaxonomyItems( $taxonomies ),

		 				'category'				=> $this->getTaxonomyItems( $categories, true ),
		 				'category_description'	=> $this->getTaxonomyItems( $categories, true, 'description' ),
		 				'tag'					=> $this->getTaxonomyItems( $tags, true ),
		 				'tag_description'		=> $this->getTaxonomyItems( $tags, true, 'description' ),
		 				'term'					=> $this->getTaxonomyItems( $taxonomies, true ),
						'term_description'		=> $this->getTaxonomyItems( $taxonomies, true, 'description' )
					);
        		}
        	}
        	return $__taxonomyClean;
        }
        
        protected function getTaxonomyItems($items, $first=false, $field='name') {
        	if (is_array($items) && count($items)>0) ;
        	else return '';

        	$__list = array();
        	foreach ( $items as $k=>$v ) {
        		if ($field=='name') $value = $v->name;
        		else if ($field=='description') $value = $v->description;
				else $value = $v->name; //default return name!
				
        		if ($first) return $value;
        		$__list[] = $value;
        	}
        	return implode(', ', $__list);
        }
        
        
		/**
         * Get Post Type of the page
         */
        private function getPostType() {
			$__postType = '';
			if ( $this->verifyPostInfo() ) {
				$post = $this->post;

				if ( isset($post->ID) ) {
					$__postType = 'post';
				}
				else if ( isset($post->term_id) && isset($post->taxonomy) ) {
					$__postType = 'term';
				}
			}
			return $__postType;
		}

		public function get_the_url() {
			$__postType = $this->getPostType();
			if ( !empty($__postType) ) $post = $this->post;

			if ( empty($__postType) ) return false;

			$url = '';

			if ( $__postType == 'post' ) {
				
				$canonical = $this->the_canonical( false );
				if ( isset($canonical) && !empty($canonical) )
					$url = $canonical;
				if ( empty($url) )
					$url = get_permalink( $post->ID );

			}
			else if ( $__postType == 'term' ) {

				$canonical = $this->the_canonical( false );
				if ( isset($canonical) && !empty($canonical) )
					$url = $canonical;
				if ( empty($url) )
					$url = get_term_link( $post, $post->taxonomy );
			}
			
			return $url;
		}

	    public function get_the_title() {
	         $title = $this->get_the_pagetype('title');
			 $title = apply_filters( 'premiumseo_seo_title', $title );
			 $title = esc_html( strip_tags( stripslashes( $title  ) ) );

         	return $title;
		}
		
	    public function get_the_meta_description() {
	         $meta_desc = $this->get_the_pagetype('desc');
			 $meta_desc = trim( $meta_desc );
			 $meta_desc = apply_filters( 'premiumseo_seo_meta_description', $meta_desc );
			 $meta_desc = esc_attr( strip_tags( stripslashes( $meta_desc ) ) );

         	return $meta_desc;
		}
		
	    public function get_the_meta_keywords() {
        	$meta_keywords = $this->get_the_pagetype('kw');
        	$meta_keywords = trim( $meta_keywords );
			$meta_keywords = apply_filters( 'premiumseo_seo_meta_keywords', $meta_keywords );
        	$meta_keywords = esc_attr( strip_tags( stripslashes( $meta_keywords ) ) );

         	return $meta_keywords;
		}
	         
	    protected function get_the_format($field, $type) {

	        	switch ($field) {
	        		case 'title':
	        			$__field = 'title';
	        			break;
	        		case 'desc':
	        			$__field = 'description';
	        			break;
	        		case 'kw':
	        			$__field = 'keywords';
	        			break;
	        		default:
	        			$__field = 'title';
	        			break;
	        	}

	        	//current field value!
	        	$__currentValue = $this->get_current_field( $__field );
	        	if ( !is_null($__currentValue) && !empty($__currentValue) && $__currentValue!='' ) {
	 				$on_page_optimization = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_on_page_optimization' );
					$meta_title_sufix = isset($on_page_optimization['meta_title_sufix']) ? $on_page_optimization['meta_title_sufix'] : '';
					if ( $field == 'title' && $type != 'home' && !empty($meta_title_sufix) ) $__currentValue .= ' ' . $meta_title_sufix;
	 				return $__currentValue;
				}

	        	//current page type!
	        	$this->set_pagetypes();
				$__templateValue = $this->get_template_field( $type, $field );
	 			if ( false !== $__templateValue ) {
	 				$__currentValue = $__templateValue;
	 			}

	        	if ( empty($__currentValue) ) return '';
  
	        	//format the page title!
	        	$__return = $this->make_format(array(), $type, $__currentValue);
	        	//var_dump('<pre>', $__return , '</pre>');
	
	        	return $__return;
		}
	        
	    public function get_the_pagetype($field='title', $format_func='get_the_format') {
			// if ( is_admin() || is_feed() ) return '';
	
			$__postType = $this->getPostType();
			if ( !empty($__postType) ) $post = $this->post;
	
			if ( $__postType == 'post' ) {

			            if ( $post->post_type == 'page' || $post->post_type == 'attachment' ) {
			            	return call_user_func( array( $this, $format_func ), $field, 'page' );
			            }
			            else if ( $post->post_type == 'post' ) {
			            	return call_user_func( array( $this, $format_func ), $field, 'post' );
						}
						else {
	                        //if ( $post->post_type == 'product' ) {
	                        //    return call_user_func( array( $this, $format_func ), $field, 'product' );
	                        //} else {
	                        //    return call_user_func( array( $this, $format_func ), $field, 'post' );
	                        //}
                            return call_user_func( array( $this, $format_func ), $field, 'posttype' );
			            }
			}
			else if ( $__postType == 'term' ) {
	
			            if ( $post->taxonomy == 'category' ) {
							return call_user_func( array( $this, $format_func ), $field, 'category' );
			            }
			            else if ( $post->taxonomy == 'tag') {
			            	return call_user_func( array( $this, $format_func ), $field, 'tag' );
			            }
			            else if ( !in_array($post->taxonomy, array('category', 'tag')) ) {
			            	return call_user_func( array( $this, $format_func ), $field, 'taxonomy' );
			            }
			}
			return '';
		}


		/**
		 * get static & dynamic page types
		 */
		public function set_pagetypes() {
			$this->pageTypes = apply_filters('premiumseo_seo_settings', $this->pageTypes);
		}


		/**
	    * Singleton pattern
	    *
	    * @return pspSEOImages Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
    }
}

// Initialize the pspSEOImages class
//$pspTitleMetaFormat = new pspTitleMetaFormat($this->cfg, ( isset($module) ? $module : array()) );
$pspTitleMetaFormat = pspTitleMetaFormat::getInstance();

// social tags!
require_once( 'init.social.php' );

// twitter cards!
require_once( 'init.social.twitter_cards.php' );