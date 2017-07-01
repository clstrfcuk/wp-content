<?php
/*
* Define class pspSocialTags
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSocialTags') != true) {
	//https://developers.facebook.com/docs/reference/opengraph/object-type/article/
    class pspSocialTags extends pspTitleMetaFormat
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		private $plugin_settings = array();

		private $module_folder = '';

		static protected $_instance;
		
		// parsing!
		private $__dom = null;
		private $__xpath = null;

		//opengraph related
		static protected $doctype = 'html5'; //doctype: xhtml | html5
		
		//facebook locales!
		static protected $fb_locale_remoteurl = 'http://www.facebook.com/translations/FacebookLocales.xml';
		static protected $fb_locale_path = '';
		private $metatags = array(
			'og:site_name'				=> '',
			'fb:app_id'						=> '',
			'og:type'						=> '',
			'og:url'							=> '',
			'og:title'						=> '',
			'og:description'				=> '',
			'og:image'						=> '',
			'article:published_time'	=> '',
			'article:modified_time'	=> '',
			'article:author'				=> '',
			'article:section'				=> '',
			'article:tag'					=> '',
		);

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
			parent::__construct(); //init page types array!

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/title_meta_format/';
			
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_title_meta_format' );
			
			self::$fb_locale_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'fb_locale.xml';
			
			if ( isset($this->plugin_settings[ 'social_validation_type' ])
				&& !empty($this->plugin_settings[ 'social_validation_type' ]) )
				self::$doctype = $this->plugin_settings[ 'social_validation_type' ];

			if ( !$this->the_plugin->verify_module_status( 'title_meta_format' ) ) ; //module is inactive
			else {
				$this->init();
			}
        }
        

        /**
         * Head Filters & Init!
         *
         */
		public function init() {
			if ( isset($this->plugin_settings['social_use_meta']) && $this->plugin_settings['social_use_meta']=='no' )
				return true;

			add_filter( 'language_attributes', array( &$this, 'add_namespace' ), 99 );
			add_action( 'premiumseo_opengraph', array( &$this, 'opengraph_locale' ), 1 );
			add_action( 'premiumseo_opengraph', array( &$this, 'opengraph_tags' ), 5 );

			add_action( 'premiumseo_head', array( &$this, 'make_opengraph' ), 29 );
		}
		
		
		/**
		 * social networks (Facebook) tags
		 */
		public function make_opengraph() {
			wp_reset_query();
			do_action( 'premiumseo_opengraph' );
		}
		
		//tags
		public function opengraph_tags($ret=false) {
			global $wp_query;

			$metatags = $this->metatags;
			$opt = $this->plugin_settings;
			$post = $wp_query->get_queried_object();
			$is_blog_posts_page = $this->the_plugin->_is_blog_posts_page();
			$pm = array();

			$pmmeta = array(
				'isactive'				=> false,
				'type'					=> '',
				'title'						=> '',
				'description'			=> '',
				'image'					=> '',
				'image_fallback'		=> array(),
			);

			//if facebook is deactivated for social meta!
			if ( isset($opt['social_use_meta']) && $opt['social_use_meta']=='no' )
				return false;

			// focus keyword & meta info!
			if ( is_singular() || $is_blog_posts_page ) {
  
				if ( ! is_object($post) || ! isset($post->ID) ) {
					$post_id = $is_blog_posts_page ? get_option( 'page_for_posts' ) : get_the_ID();
					$post = get_post( $post_id );
				}

				$pm = $this->the_plugin->get_psp_meta( $post->ID );
				
			} else if ( is_category() || is_tag() || is_tax() ) { //taxonomy data!
				
				$__objTax = (object) array('term_id' => $post->term_id, 'taxonomy' => $post->taxonomy);

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $__objTax );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$pm = $this->the_plugin->get_psp_meta( $__objTax, $psp_current_taxseo );

			}

			$metatags[ 'og:site_name' ] = isset($opt['social_site_title']) ? $opt['social_site_title'] : get_bloginfo('name');
			
			// facebook app id
			if ( isset($opt['social_fb_app_id']) && ! empty($opt['social_fb_app_id']) ) {
				$metatags[ 'fb:app_id' ] = $opt['social_fb_app_id'];
			}
			
			if ( is_singular() || $is_blog_posts_page ) { //post|page|post_type

				//if facebook is deactivated for this post|page!
				$pmmeta['isactive'] = true;
				if ( isset($pm['facebook_isactive']) && $pm['facebook_isactive']=='no' ) {
					$pmmeta['isactive'] = false;

					if ( is_home() || is_front_page() ) ; //homepage
					else {
						return false;
					}
				}

				//extra tags
				if ( ! isset($this->plugin_settings['social_include_extra']) || $this->plugin_settings['social_include_extra']=='yes' ) {
					$metatags[ 'article:published_time' ] = get_the_time('c');
					$metatags[ 'article:modified_time' ] = get_the_modified_time('c');
 
					//don't use author for pages
					if ( ! is_page() )
						$metatags['article:author'] = get_author_posts_url( $post->post_author );
				}
				
				//url
				$metatags[ 'og:url' ] = $this->the_url();

				//type
				$post_type = isset($post->post_type) ? $post->post_type : '';
				if ( isset($opt['social_opengraph_default']) && !empty($opt['social_opengraph_default'])
					&& isset($opt['social_opengraph_default']["$post_type"]) ) {
					$ogdef  = $opt['social_opengraph_default']["$post_type"];
				}
				if ( isset($ogdef) && !empty($ogdef) ) {
					$metatags[ 'og:type' ] = $ogdef;
					if ( 'none' != $metatags[ 'og:type' ] ) {
						//$pmmeta['type'] = trim( $metatags[ 'og:type' ] );
					}
				}
				if ( isset($pm['facebook_opengraph_type']) && !empty($pm['facebook_opengraph_type'])
					&& ! in_array($pm['facebook_opengraph_type'], array('default')) ) {
					$metatags[ 'og:type' ] = $pm['facebook_opengraph_type'];
					$pmmeta['type'] = trim( $metatags[ 'og:type' ] );
				}
				if ( empty($metatags[ 'og:type' ]) )
					$metatags[ 'og:type' ] = 'article';
  
				//title
				if ( isset($pm['facebook_titlu']) && !empty($pm['facebook_titlu']) ) {
					$metatags[ 'og:title' ] = $pm['facebook_titlu'];
					$pmmeta['title'] = trim( $pm['facebook_titlu'] );
				}
				if ( empty($metatags[ 'og:title' ]) )
					$metatags[ 'og:title' ] = $this->the_title('');
					
				//description
				if ( isset($pm['facebook_desc']) && !empty($pm['facebook_desc']) ) {
					$metatags[ 'og:description' ] = $pm['facebook_desc'];
					$pmmeta['description'] = trim( $pm['facebook_desc'] );
				}
				if ( empty($metatags[ 'og:description' ]) )
					$metatags[ 'og:description' ] = $this->the_meta_description( false );
					
				//image: can be Array
				$images = array();
				if ( isset($pm['facebook_image']) && !empty($pm['facebook_image']) ) {
					$images[] = $pm['facebook_image'];
					$pmmeta['image'] = $pm['facebook_image'];
				}

				if (1) {
					$image_fallback = '';
					// featured image
					if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
						$__featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
						$__featured_image = $__featured_image[0];
						if ( isset($__featured_image) && !empty($__featured_image) ) //featured image
							$image_fallback = $__featured_image;
							$images[] = $__featured_image;
					}
					//$image_fallback = ''; // uncomment to DEBUG
					// custom field image
					if ( empty($image_fallback) ) {
						if ( isset($opt['social_customfield_post']) && ! empty($opt['social_customfield_post']) ) {
		 					$__custom_image = get_post_meta($post->ID, $opt['social_customfield_post'], true);
		
							if ( isset($__custom_image) && !empty($__custom_image) ) {
								$image_fallback = $__custom_image;
								$images[] = $__custom_image;
							}
		 				}
					}
					//$image_fallback = ''; // uncomment to DEBUG
					// first image in post content
	 				if ( empty($image_fallback) ) {
	 					$__first_image = $this->get_content_first_image($post->post_content);

						if ( isset($__first_image) && !empty($__first_image) ) {
							$image_fallback = $__first_image;
							$images[] = $__first_image;
						}
	 				}
				}
				$pmmeta['image_fallback'] = array_diff($images, array($pmmeta['image']));

				if ( isset($opt['social_default_img']) && !empty($opt['social_default_img']) ) {
					$images[] = $opt['social_default_img'];
				}
				if ( ! empty($images) ) {
					$metatags[ 'og:image' ] = $images;
				}

				// category
				$category = $this->get_post_category();
				if ( ! empty($category) ) {
					$metatags['article:section'] = $category;
				}

				// tags: can be Array
				$tags = $this->get_post_tags();
				if ( ! empty($tags) ) {
					$metatags['article:tag'] = $tags;
				}

			} else if ( is_category() || is_tag() || is_tax() ) {

				//if facebook is deactivated for this post|page!
				if ( isset($pm['facebook_isactive']) && $pm['facebook_isactive']=='no' ) {
					return false;
				}

				//url
				$metatags[ 'og:url' ] = $this->the_url();
					
				//type
				$taxonomy_type = isset($post->taxonomy) ? $post->taxonomy : '';
				$taxonomy_type = in_array($taxonomy_type, array('category', 'post_tag')) ? $taxonomy_type : '_custom_taxonomy';
				if ( isset($opt['social_opengraph_default_taxonomy']) && !empty($opt['social_opengraph_default_taxonomy'])
					&& isset($opt['social_opengraph_default_taxonomy']["$taxonomy_type"]) ) {
					$ogdef  = $opt['social_opengraph_default_taxonomy']["$taxonomy_type"];
				}
				if ( isset($ogdef) && !empty($ogdef) ) {
					$metatags[ 'og:type' ] = $ogdef;
				}
				if ( isset($pm['facebook_opengraph_type']) && !empty($pm['facebook_opengraph_type'])
					&& ! in_array($pm['facebook_opengraph_type'], array('default')) ) {
					$metatags[ 'og:type' ] = $pm['facebook_opengraph_type'];
				}
				if ( empty($metatags[ 'og:type' ]) )
					$metatags[ 'og:type' ] = 'website';

				//title
				if ( isset($pm['facebook_titlu']) && !empty($pm['facebook_titlu']) ) {
					$metatags[ 'og:title' ] = $pm['facebook_titlu'];
				}
				if ( empty($metatags[ 'og:title' ]) )
					$metatags[ 'og:title' ] = $this->the_title('');
					
				//description
				if ( isset($pm['facebook_desc']) && !empty($pm['facebook_desc']) ) {
					$metatags[ 'og:description' ] = $pm['facebook_desc'];
				}
				if ( empty($metatags[ 'og:description' ]) ) {
					$metatags[ 'og:description' ] = $this->the_meta_description( false );
				}

				//image: can be Array
				$images = array();
				if ( isset($pm['facebook_image']) && !empty($pm['facebook_image']) ) {
					$images[] = $pm['facebook_image'];
				}
				if (1) {
					$image_fallback = '';
					// custom field image
					if ( function_exists('get_term_meta') ) {
						if ( isset($opt['social_customfield_taxonomy']) && ! empty($opt['social_customfield_taxonomy']) ) {
		 					$__custom_image = get_term_meta($post->ID, $opt['social_customfield_taxonomy'], true);

							if ( isset($__custom_image) && !empty($__custom_image) ) {
								$image_fallback = $__custom_image;
								$images[] = $__custom_image;
							}
		 				}
					}
					//$image_fallback = ''; // uncomment to DEBUG
					// first image in post content
	 				if ( empty($image_fallback) ) {
	 					$__first_image = $this->get_content_first_image($post->description);

						if ( isset($__first_image) && !empty($__first_image) ) {
							$image_fallback = $__first_image;
							$images[] = $__first_image;
						}
	 				}
				}
				if ( isset($opt['social_default_img']) && !empty($opt['social_default_img']) ) {
					$images[] = $opt['social_default_img'];
				}
				if ( ! empty($images) ) {
					$metatags[ 'og:image' ] = $images;
				}
				//$metatags[ 'og:image' ] = false;

			} else {

				//author
				if ( is_author() ) {
					$metatags[ 'og:type' ] = 'profile';
				}
				// others...
				else {
					$metatags[ 'og:type' ] = 'object'; //'website'
				}

				$metatags[ 'og:url' ] = $this->the_url();
				if ( empty($metatags[ 'og:url' ]) ) {
					$metatags[ 'og:url' ] = home_url( '/' ); //don't index remaining page types: use homepage Url as canonical!
				}

				$metatags[ 'og:title' ] = $this->the_title('');
				$metatags[ 'og:description' ] = $this->the_meta_description( false );
				
				$metatags[ 'og:image' ] = false;
				if ( isset($opt['social_default_img']) && !empty($opt['social_default_img']) ) {
					$metatags[ 'og:image' ] = $opt['social_default_img'];
				}

			}

			if ( is_home() || is_front_page() ) { //homepage

				//url
				$metatags[ 'og:url' ] = $this->the_url();

				//type
				if ( empty($pmmeta['isactive']) || '' == $pmmeta['type'] ) {
					if ( isset($opt['social_home_type']) && !empty($opt['social_home_type']) ) {
						$metatags[ 'og:type' ] = $opt['social_home_type'];
					}
					if ( empty($metatags[ 'og:type' ]) || '' == $pmmeta['type'] )
						$metatags[ 'og:type' ] = 'website';
				}

				//title
				if ( empty($pmmeta['isactive']) || '' == $pmmeta['title'] ) {
					if ( isset($opt['social_home_title']) && !empty($opt['social_home_title']) ) {
						$metatags[ 'og:title' ] = $opt['social_home_title'];
					}
					if ( empty($metatags[ 'og:title' ]) )
						$metatags[ 'og:title' ] = $this->the_title('');
				}

				//description
				if ( empty($pmmeta['isactive']) || '' == $pmmeta['description'] ) {
					if ( isset($opt['social_home_desc']) && !empty($opt['social_home_desc']) ) {
						$metatags[ 'og:description' ] = $opt['social_home_desc'];
					}
					if ( empty($metatags[ 'og:description' ]) )
						$metatags[ 'og:description' ] = $this->the_meta_description( false );
				}

				//image: can be Array
				$images = array();
				if ( ! empty($pmmeta['isactive']) && ! empty($pmmeta['image']) ) {
					$images = array_merge($images, array($pmmeta['image']));
				}

				if ( isset($opt['social_home_img']) && !empty($opt['social_home_img']) )
					$images[] = $opt['social_home_img'];

				if ( ! empty($pmmeta['isactive']) && ! empty($pmmeta['image_fallback']) ) {
					$images = array_merge($images, $pmmeta['image_fallback']);
				}

				if ( isset($opt['social_default_img']) && !empty($opt['social_default_img']) ) {
					$images[] = $opt['social_default_img'];
				}
				if ( ! empty($images) ) {
					$metatags[ 'og:image' ] = $images;
				}

			}
			//var_dump('<pre>', $metatags, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			// hooks - in case may need to modify this tags!
			$metatags[ 'og:url' ] = apply_filters( 'premiumseo_opengraph_url', $metatags[ 'og:url' ] );
			$metatags[ 'og:type' ] = apply_filters( 'premiumseo_opengraph_type', $metatags[ 'og:type' ] );
			$metatags[ 'og:title' ] = apply_filters( 'premiumseo_opengraph_title', $metatags[ 'og:title' ] );
			$metatags[ 'og:description' ] = apply_filters( 'premiumseo_opengraph_description', $metatags[ 'og:description' ] );
			$metatags[ 'og:image' ] = apply_filters( 'premiumseo_opengraph_image', $metatags[ 'og:image' ] );
			
			// video.other => remove extra tags (facebook debugger recommand it)
			if ( 'video.other' == $metatags[ 'og:type' ] ) {
				foreach (array('article:published_time', 'article:modified_time', 'article:author', 'article:section', 'article:tag') as $whattag) {
					if ( isset($metatags[ "$whattag" ]) ) {
						unset($metatags[ "$whattag" ]);
					}
				}
			}

			// clean empty values
			foreach ($metatags as $__tag => $val) {
				if ( empty($val) ) unset($metatags[ "$__tag" ]);
			}

			// retrieve metatags
			if ( isset($ret) && $ret===true ) {
				return $metatags;
			}
			if ( empty($metatags) ) return false;
			
			//make Tags List
			$__listTags = array();
			foreach ($metatags as $__tag => $val) {

				if ( empty($val) ) continue 1;
				$val = ! is_array($val) ? array($val) : $val;

				if ( $__tag=='og:image' ) {
					if ( !empty($val) ) {
						foreach ($val as $idx2 => $val2) {
							$val["$idx2"] = str_replace('http__', 'http', $val2);
						}
					}
					//$val = esc_url( $val );
					$val = array_map("esc_url", $val);
				} else {
					//$val = esc_attr( $val );
					$val = array_map("esc_attr", $val);
				}

				switch (self::$doctype) {
					case 'xhtml':
						if ( !empty($val) ) {
							foreach ($val as $val2) {
								$__listTags[] = '<meta name="' . ($__tag). '" content="' . ($val2) . '"/>';
							}
						}
						break;

					case 'html5':
					default:
						if ( !empty($val) ) {
							foreach ($val as $val2) {
								$__listTags[] = '<meta property="' . ($__tag). '" content="' . ($val2) . '"/>';
							}
						}
						break;
				}
			}

			$__listTags = implode(PHP_EOL, $__listTags);
			echo $__listTags . PHP_EOL;
		}
		
		//global namespace
		public function add_namespace( $original ) {
			$res = array();
  
			$nm = $this->get_namespaces();
			
			switch (self::$doctype) {
				case 'xhtml':
					foreach ($nm as $name => $url) {
						$res[] = "xmlns:".esc_attr($name)."=\"".esc_attr($url)."\"";
					}
					$res = ' '.implode(' ', $res).' ';
					break;

				case 'html5':
				default:
					foreach ($nm as $name => $url) {
						$res[] = esc_attr($name).": ".esc_attr($url);
					}
					$res = ' prefix="'.implode(' ', $res).'" ';
					break;
			}
			//$res = ' itemscope itemtype="http://schema.org/WebSite"' . $res;
			return trim( $original . $res );
		}
		
		private function get_namespaces() {
			return array(
				'og' => 'http://ogp.me/ns#',
				'fb' => 'http://ogp.me/ns/fb#'
			);
		}
		
		//facebook locale
		public function opengraph_locale() {
			$locale = get_locale();
			$locale = apply_filters( 'premiumseo_social_locale', $locale );

			// 2 letter locales are converted!
			if ( strlen( $locale ) == 2 ) {
				$convertArr = $this->locale_convert_arr();
				
				$locale_sec = $locale;
				if ( isset($convertArr["$locale"]) )
					$locale_sec = $convertArr["$locale"];

				$locale = strtolower( $locale ) . '_' . strtoupper( $locale_sec );
			}

			// valid facebook locales
			$fb_locales = $this->get_facebook_locale();

			// if locale is not facebook valid use a default locale
			if ( !isset($fb_locales[ $locale ]) )
				$locale = 'en_US';

			$locale = esc_attr( $locale );
			echo "<meta property='og:locale' content='" . $locale . "'/>" . PHP_EOL;
		}
		
		private function get_facebook_locale() {
			$fb_locales = array();

			$filename = self::$fb_locale_path;
		
			// cache file needs refresh!
			if ( ($statCache = $this->isCacheRefresh($filename))===true || $statCache===0 || 1 ) {
				$response = wp_remote_get( self::$fb_locale_remoteurl, array( 'timeout' => 15 ) );
				if ( is_wp_error( $response ) ) ; //if there's error -> try to get old cache
				else {
					$data = wp_remote_retrieve_body( $response );
					if ( !empty($data) ) {
						$xml = $this->queryContent( $data, '/locales/locale/codes/code/standard/representation' );
						if ($xml && $xml->length>0) {
							// write new local cached file!
							$this->writeCacheFile($filename, $data);
						}
					}
				}
			}

			$cache = $this->getCacheFile($filename);
			if ( !empty($cache) ) {
				$xml = $this->queryContent( $cache, '/locales/locale/codes/code/standard/representation' );
				if ($xml->length>0)
					foreach ($xml as $v) {
						$val = (string) $v->nodeValue;
						$fb_locales[ $val ] = $val;
					}
			}
			return $fb_locales;
		}
		
		private function locale_convert_arr() {
			return array(
				'el'		=> 'GR'
			);
		}

		
		/**
	    * Singleton pattern
	    *
	    * @return pspSocialTags Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
	    
		// verify cache refresh is necessary!
		private function isCacheRefresh($filename) {
			$cache_life = 10080; // cache lifetime in minutes /1 week
	
			// cache file exists!
			if ($this->verifyFileExists($filename)) {
				$verify_time = time();
				$file_time = filemtime($filename);
				$mins_diff = ($verify_time - $file_time) / 60;
				if($mins_diff > $cache_life){
					// new cache is necessary!
					return true;
				}
				// cache is empty! => new cache is necessary!
				if (filesize($filename)<=0) return 0;
	
				// NO new cache!
				return false;
			}
			// cache file NOT exists! => new cache is necessary!
			return 0;
		}
	    
		// write content to local cached file
		private function writeCacheFile($filename, $content) {
			// load WP_Filesystem 
			include_once ABSPATH . 'wp-admin/includes/file.php';
 			WP_Filesystem();
			global $wp_filesystem;
			return $wp_filesystem->put_contents( $filename, $content );
			
			//return file_put_contents($filename, $content);
		}
	    
		// cache file
		private function getCacheFile($filename) {
			if ($this->verifyFileExists($filename)) {
				$content = file_get_contents($filename);
				return $content;
			}
			return false;
		}
	    
		// verify if file exists!
		private function verifyFileExists($file, $type='file') {
			clearstatcache();
			if ($type=='file') {
				if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
					return false;
				}
				return true;
			} else if ($type=='folder') {
				if (!is_dir($file) || !is_readable($file)) {
					return false;
				}
				return true;
			}
			// invalid type
			return 0;
		}
		
		
		// load content to DOM extension!
		private function loadContent($content) {
			$this->__dom = new DOMDocument;
			// We don't want to bother with white spaces
			$this->__dom->preserveWhiteSpace = false;
			if (@$this->__dom->loadXML($content)) {
				$this->__xpath = new DOMXPath($this->__dom);
				return $this->__xpath;
			}
			return false;
		}

		// query content with xpath
		private function queryContent($load, $query, $domcontext=false) {
			if (!empty($load)) $isLoaded = $this->loadContent($load);
			else $isLoaded = true;
			if ($isLoaded) {
				if (!empty($domcontext))
					$res = $this->__xpath->evaluate($query, $domcontext);
				else
					$res = $this->__xpath->evaluate($query);
				return $res;
			}
			return false;
		}


		/**
		 * Category & Tags
		 */
		public function get_post_category() {
			if ( ! is_singular() ) {
				return false;
			}

			$terms = get_the_category();

			if ( ! is_wp_error( $terms ) && is_array( $terms ) && ! empty($terms)  ) {
				return $terms[0]->name;
			}
			return false;
		}

		public function get_post_tags() {
			if ( ! is_singular() ) {
				return false;
			}

			$tags = get_the_tags();

			if ( ! is_wp_error( $tags ) && is_array( $tags ) && ! empty($tags) ) {
				$ret = array();
				foreach ( $tags as $tag ) {
					$ret[] = (string) $tag->name;
				}
				return $ret;
			}
			return false;
		}

		private function get_content_first_image($content) {
			if ( empty($content) ) return '';
			$content = $this->the_plugin->do_shortcode($content);

			$res = preg_match('/<img.*src=[\'"]([^\'"]+)[\'"].*\/?>/iu', $content, $matches);
			$img = isset($matches[1]) ? $matches[1] : '';
			$img = $this->the_plugin->u->rel2abs( $img, get_home_url() );
			return $img;
		}
    }
}

// Initialize the pspSocialTags class
$pspSocialTags = new pspSocialTags();