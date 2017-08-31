<?php
/*
* Define class pspLinkRedirect
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspLinkRedirect') != true) {
    class pspLinkRedirect
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
		
		private $settings = array();

		static protected $_instance;

		private $redirect_types = array();
		private $regexp_matches = array();

		private $orig_url; // slug changed in quick edit term box

		
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Link_Redirect/';
			$this->module = $this->the_plugin->cfg['modules']['Link_Redirect'];

			$this->settings = $this->the_plugin->getAllSettings( 'array', 'Link_Redirect' );

			$this->redirect_types = $this->the_plugin->get_redirect_types();

			if ( $this->the_plugin->is_admin === true ) {
	            add_action('admin_menu', array( $this, 'adminMenu' ));

				// ajax handler
				add_action('wp_ajax_pspGetUpdateDataRedirect', array( $this, 'ajax_request' ));
				add_action('wp_ajax_pspAddToRedirect', array( $this, 'addToRedirect' ));
				add_action('wp_ajax_pspUpdateToRedirect', array( $this, 'updateToRedirect' ));
				//add_action('wp_ajax_pspRemoveFromRedirect', array( $this, 'removeFromRedirect' ));
				
				//delete bulk rows!
				//add_action('wp_ajax_pspLinkRedirect_do_bulk_delete_rows', array( $this, 'delete_rows' ));
			}

			//if ( $this->the_plugin->capabilities_user_has_module('Link_Redirect') )
			if ( !$this->the_plugin->verify_module_status( 'Link_Redirect' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin !== true ) {
					add_action('wp', array( $this, 'redirect_header' ), 0);
				}
				else {
					// monitor only if permalink is enabled
					if ( get_option( 'permalink_structure' ) ) {
						add_action( 'admin_init', array( $this, 'admin_init_monitor' ) );
					}
				}
			}

			// init module!
			//$this->init();
        }
        
		private function init() {
			//$this->createTable();
		}

		public function admin_init_monitor() {

            $minEnabled = array('post_slug', 'term_slug');
            if ( isset($this->settings['enable_monitor']) && !empty($this->settings['enable_monitor']) ) {
                $minEnabled = (array) $this->settings['enable_monitor'];
            }
            $minEnabled = array_unique( array_filter( $minEnabled ) );
            //if ( empty($minEnabled) ) return;

			//:: POST
			if ( in_array('post_slug', $minEnabled) ) {
				// add post original url to post edit screen
				add_action( 'edit_form_advanced', array( $this, 'slug_add_post_orig_url' ), 10, 1 );
				add_action( 'edit_page_form', array( $this, 'slug_add_post_orig_url' ), 10, 1 );

				// check if post slug was changed considering the original url
				add_action( 'post_updated', array( $this, 'slug_check_post_slug_changed' ), 20, 3 );
			}

			//:: TERM
			if ( in_array('term_slug', $minEnabled) ) {
				// get taxonomies
				$taxonomies = get_taxonomies();

				// loop through taxonomies
				if ( ! empty($taxonomies) ) {
					foreach ( $taxonomies as $taxonomy ) {
						// add term original url to term edit screen
						add_action( $taxonomy . '_edit_form_fields', array( $this, 'slug_add_term_orig_url' ), 10, 2 );
					}
				}

				// quick edit term box
				add_action( 'wp_ajax_inline-save-tax', array( $this, 'slug_add_term_orig_url_quick_edit' ), 1 );

				// check if term slug was changed considering the original url
				add_action( 'edited_term', array( $this, 'slug_check_term_slug_changed' ), 10, 3 );
			}
		}

		/**
	    * Singleton pattern
	    *
	    * @return pspLinkRedirect Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }

		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{
    		if ( $this->the_plugin->capabilities_user_has_module('Link_Redirect') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Link Redirect', 'psp'),
		            __('Link Redirect', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_Link_Redirect",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('Link_Redirect') ) {
				$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		
		/**
		 * FRONTEND
		 *
		 */
		public function redirect_header() {
			global $wpdb, $wp;

			// is NOT frontend?
			if ( $this->the_plugin->is_admin === true ) {
				return;
			}

			// is all 404 pages redirect?
			$is_all_404_redirect = $this->the_plugin->is_all_404_redirect(array(
				'settings'		=> $this->settings,
			));
			if ( $this->the_plugin->is_404_valid() && ($is_all_404_redirect !== false) ) {
				$this->do_all_404_redirect( $is_all_404_redirect );
				return;
			}

			// get page url
			$url = array();
			$url[] = home_url(add_query_arg(array(), $wp->request));
			if ( isset($_SERVER['REQUEST_URI']) && ! empty($_SERVER['REQUEST_URI']) ) {
				$url[] = $this->the_plugin->get_current_page_url(array());
			}

			// filter page url
			foreach ($url as $key => $val) {
				$val = $this->get_clean_url( $val );
				$url["$key"] = $val;
			}
			$url = array_unique( array_filter( $url ) );

			// try to find redirect url for page url
			$redirect = $this->find_redirect_url( $url );
			//var_dump('<pre>', $redirect , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			$is_valid_found = $this->is_valid_found_redirect( $redirect );
			if ( ! $is_valid_found ) {
				return;
			}

			// NOW WE CAN DO THE REDIRECTION
			$this->do_redirect( $redirect );
		}

		private function is_valid_found_redirect( $target=array() ) {
			// redirect url not found
			if ( $target===false || is_null($target) || ! is_array($target) || ! isset($target['id']) ) {
				return false;
			}
			// prevent redirect loops
			if ( $target['url_redirect'] == $target['url'] ) {
				return false;
			}
			return true;
		}

		private function find_redirect_url( $url ) {
			//:: custom url
			$redirect = $this->search_by_custom_url( $url );
			//var_dump('<pre>', $redirect , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			$is_valid_found = $this->is_valid_found_redirect( $redirect );
			if ( $is_valid_found ) {
				return $redirect;
			}

			//:: regexp
			$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
			$url = $this->get_clean_url( $url );
			$url = array( $url );

			$found_urls = $this->get_all_regexp_rules();
			//var_dump('<pre>', $found_urls , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			foreach ($found_urls as $regexp_data) {
				foreach ($url as $url_str) {

					$redirect = $this->search_by_regexp( $url_str, $regexp_data );
					//var_dump('<pre>', $redirect , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

					$is_valid_found = $this->is_valid_found_redirect( $redirect );
					if ( $is_valid_found ) {
						//var_dump('<pre>', $redirect , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
						return $redirect;
					}
				}
			}
			return false;
		}

		private function search_by_custom_url( $url=array() ) {
			global $wpdb;

			if ( empty($url) || ! is_array($url) ) return false;

			$url2 = $url;
			foreach ($url2 as $key => $val) {
				$val = $this->the_plugin->escape_mysql_regexp( $val );
				$val = $val . '/?';
				$val = '(' . $val . ')';
				$url2["$key"] = $val;
			}
			$url2 = implode('|', $url2);
			$url2 = '^(' . $url2 . ')$';

			//$sql = "SELECT a.id, a.url_redirect from " . $wpdb->prefix . "psp_link_redirect as a WHERE 1=1 and a.url=%s;";
			//$sql = $wpdb->prepare( $sql, $url );
			$sql = "SELECT a.* from {$wpdb->prefix}psp_link_redirect as a WHERE 1=1 and a.publish='Y' and a.url regexp '$url2' order by a.id desc limit 1;";
			//var_dump('<pre>', $sql , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$res = $wpdb->get_row( $sql, ARRAY_A );
			
			return $res;
		}

		private function get_all_regexp_rules() {
			global $wpdb;

			$sql = "SELECT a.* from {$wpdb->prefix}psp_link_redirect as a WHERE 1=1 and a.publish='Y' and a.redirect_rule = 'regexp' order by a.id desc;";
			//var_dump('<pre>', $sql , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$res = $wpdb->get_results( $sql, ARRAY_A );
			if ( empty($res) ) return array();

			return $res;
		}

		private function search_by_regexp( $page_url, $regexp_data ) {
			$regexp = str_replace( '~', '\\~', $regexp_data['url'] );

			$this->regexp_matches = array();
			$found = @preg_match( "~{$regexp}~", $page_url, $this->regexp_matches );

			//:: DEBUG
			//if ( 46 == $regexp_data['id'] ) {
			//	var_dump('<pre>', $regexp_data, $page_url, $found, $this->regexp_matches , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			//}

			if ( ! empty( $found ) ) {
				// replace the $0-9 vars in the target
				$url_redirect = preg_replace_callback(
					'/\$[0-9]+/',
					array(
						$this,
						'regexp_replace_callback_get_url',
					),
					$regexp_data['url_redirect']
				);
				$regexp_data['url_redirect'] = $url_redirect;
				return $regexp_data;
			}
			return false;
		}

		private function regexp_replace_callback_get_url( $matches ) {
			// remove the $ prefix
			$key = isset($matches[0]) ? substr($matches[0], 1) : '';

			if ( ! empty($key) && isset($this->regexp_matches[$key]) ) {
				return $this->regexp_matches[$key];
			}
			return '';
		}

		private function do_redirect( $target=array() ) {
			// is it safe redirect?
			$safe_redirect = isset($this->settings['safe_redirect']) && 'yes' == $this->settings['safe_redirect']
				? true : false;

			// redirect type
			$redirect_type = $this->the_plugin->get_redirect_type(array(
				'settings'		=> $this->settings,
				'row'			=> $target,
			));
			$redirect_type = isset($redirect_type['key']) ? $redirect_type['key'] : '301';

			if ( '410' == $redirect_type ) {
				$this->mark_410();
				return;
			}
			if ( '451' == $redirect_type ) {
				$this->mark_451();
				return;
			}

			$target_id = isset($target['id']) ? $target['id'] : 0;
			$is_regexp = isset($target['redirect_rule']) && ('regexp' == $target['redirect_rule']) ? true : false;
			$has_status_code = isset($target['target_status_code']) && ('' != trim($target['target_status_code'])) ? true : false;

			$target = $target['url_redirect'];
			$target = $this->build_absolute_url( $target );
			$target = $this->get_clean_url( $target );

			if ( ! $this->is_valid_url( $target ) ) {
				return;
			}

			// store if is 404 page
			$this->the_plugin->store_new_404_log();

			// update hits!
			if ( $target_id ) {
				$this->updateUrlHits( $target_id );
			}

			// update regexp redirect!
			if ( $is_regexp && ! $has_status_code ) {
				$status_code = $this->get_status_code(array(
					'url'			=> $target,
				));
				$this->update_status_code(array(
					'itemid'		=> $target_id,
					'status_code'	=> $status_code,
				));
			}

			if ( ! function_exists( 'wp_redirect' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );
			}

			if ( $safe_redirect ) {
				wp_safe_redirect( $target, $redirect_type );
			}
			else {
				wp_redirect( $target, $redirect_type );
			}
			exit();
		}

		private function updateUrlHits( $id=0 ) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . "psp_link_redirect";
			$query_update = "UPDATE " . ($table_name) . " set
						hits=hits+1
						where id='$id'";
			$wpdb->query($query_update);
		}

		private function mark_404() {
			global $wp_query;

			if ( is_object($wp_query) ) {
				$wp_query->is_404 = true;
			}
		}

		private function mark_410() {
			$this->mark_404();
			status_header( 410, '410 Gone - Content Deleted' );
		}

		private function mark_451() {
			$this->mark_404();
			status_header( 451, '451 Content Unavailable For Legal Reasons' );
		}

		private function do_all_404_redirect( $url ) {
			$this->do_redirect(array(
				'url_redirect'			=> $url,
				'is_all_404_redirect'	=> true,
			));
		}


		/**
		 * Redirect Status Code & Message
		 *
		*/
		public function get_status_code( $pms=array() ) {
			$pms = array_replace_recursive(array(
				'url'		=> '',
			), $pms);
			extract( $pms );

			$ret = array(
				'status' 	=> 'invalid',
				'last_check_at'	=> '',
				'resp_code'	=> '',
				'resp_msg' 	=> '',
			);

			$last_check_at = date('Y-m-d H:i:s');
			$ret['last_check_at'] = $last_check_at;

			$target = $url;
			$target = $this->build_absolute_url( $target );
			$target = $this->get_clean_url( $target );

			// verify string?
			if ( ! $this->is_valid_url( $target ) ) {
				$ret = array_replace_recursive($ret, array(
					'resp_code'	=> 'invalid_string',
					'resp_msg'	=> __('target url string is invalid.', 'psp'),
				));
				return $ret;
			}

			// could we solve request to target url?
			$resp = wp_remote_head( $target, array( 'sslverify' => false ) );

			if ( is_wp_error( $resp ) ) {
				$ret = array_replace_recursive($ret, array(
					'resp_code'	=> 'unable_to_resolve',
					'resp_msg'	=> __('unable to resolve request to target url.', 'psp'),
				));
				return $ret;
			}

			$resp_code = wp_remote_retrieve_response_code( $resp );

			// target url is temporary?			
			if ( $this->is_status_temporary( $resp_code ) ) {
				$ret = array_replace_recursive($ret, array(
					'resp_code'	=> 'is_temporary',
					'resp_msg'	=> sprintf( __('target url returns a %s (temporary) http status code. please check manually if it\'s a valid redict.', 'psp'), $resp_code ),
				));
				return $ret;
			}

			// target url is error?
			if ( $this->is_status_error( $resp_code ) ) {
				$ret = array_replace_recursive($ret, array(
					'resp_code'	=> 'is_error',
					'resp_msg'	=> sprintf( __('target url returns a %s http status code (error). please check manually if it\'s a valid redict.', 'psp'), $resp_code ),
				));
				return $ret;
			}

			// target url is 301 Moved Permanently?
			if ( 301 === $resp_code ) {
				$ret = array_replace_recursive($ret, array(
					'resp_code'	=> 'is_301',
					'resp_msg'	=> sprintf( __('target url returns a %s http status code (moved permanently). please check manually if it\'s a valid redict.', 'psp'), $resp_code ),
				));
				return $ret;
			}

			// target url is Other non OK http code?
			if ( 200 !== $resp_code ) {
				$ret = array_replace_recursive($ret, array(
					'resp_code'	=> 'is_not_ok',
					'resp_msg'	=> sprintf( __('target url returns a %s http status code (which is not 200 OK). please check manually if it\'s a valid redict.', 'psp'), $resp_code ),
				));
				return $ret;
			}

			$ret = array_replace_recursive($ret, array(
				'status'	=> 'valid',
				'resp_code'	=> 'is_ok',
				'resp_msg'	=> sprintf( __('target url returns a %s valid http status code.', 'psp'), $resp_code ),
			));
			return $ret;
		}

		public function update_status_code( $pms=array() )
		{
			global $wpdb;

			$pms = array_replace_recursive(array(
				'itemid'		=> 0,
				'get_row' 		=> false,
				'status_code'	=> array(),
			), $pms);
			extract( $pms );

			$ret = array(
				'status' 	=> 'invalid',
				'html'		=> '',
				'msg'		=> '',
			);

			$row = array();
			$row_id = $itemid;
			if ( $itemid ) {
				if ( $get_row ) {
					$row = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_link_redirect WHERE id = '" . $itemid . "'", ARRAY_A );
					$row_id = (int)$row['id'];
				}
			}

			if ( ! $itemid || ! $row_id ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> 'itemid is empty.',
				));

				return $ret;
			}

			// do the operation
			{
				{
					// do update
					if (1) {
						$wpdb->update( 
							$wpdb->prefix . "psp_link_redirect", 
							array( 
								'target_status_code'		=> $status_code['status'],
								'target_status_details'		=> serialize($status_code),
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s',
								'%s',
							), 
							array( '%d' ) 
						);
					}

					$ret = array_replace_recursive($ret, array(
						'status' 	=> 'valid'
					));
				}
			}
			return $ret;
		}


		/**
		 * POSTS WITH MODIFIED SLUG
		 *
		 */
		//:: POST
		// add post original url as an hidden input to post edit screen, to be used in post_update wp hook
		public function slug_add_post_orig_url( $post ) {

			if ( is_null($post) || empty($post) ) {
				return false;
			}
			if ( ! isset($post->ID) || ! $post->ID ) {
				return false;
			}
			$post_id = (int) $post->ID;

			$url = $this->get_post_urlpath( $post_id );
			
			echo  '<input type="hidden" name="psp_orig_post_url" value="' . esc_attr( $url ) . '"/>';
		}

		public function slug_get_post_orig_url( $post, $post_before ) {
			// $_POST filter to find our hidden input field
			$orig_url = filter_input( INPUT_POST, 'psp_orig_post_url' );

			// we have the hidden input field defined
			if ( ! empty($orig_url) ) {
				return $orig_url;
			}

			// $_POST filter to find page current action
			$action = filter_input( INPUT_POST, 'action' );

			// we are in an inline action screen (from quick edit or bulk) and we hit save?
			// also new slug must be different from old slug?
			if ( ! empty($action) && ($action === 'inline-save') ) {
				if ( $post->post_name !== $post_before->post_name ) {
					//return '/' . $post_before->post_name . '/';

					$post_id = isset($post->ID) && $post->ID ? (int) $post->ID : 0;
					$__ = $this->get_post_urlpath( $post_id );
					$__ = str_replace($post->post_name, $post_before->post_name, $__);
					$__ = rtrim($__, '/') . '/';
					return $__;
				}
			}
			return false;
		}

		// check if post slug was changed considering the original url
		public function slug_check_post_slug_changed( $post_id, $post, $post_before ) {

			$can_monitor = $this->slug_can_monitor_post( $post_id, $post, $post_before );
			//var_dump('<pre>', $can_monitor , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			if ( ! isset($can_monitor['orig_url']) || ! isset($can_monitor['new_url']) ) {
				return false;
			}
			extract($can_monitor);

			// can create new redirect row
			$can_create_redirect = $this->slug_can_create_redirect( $orig_url, $new_url );
			//var_dump('<pre>', $can_create_redirect , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			if ( ! $can_create_redirect ) {
				return false;
			}

			//var_dump('<pre>', $orig_url, $new_url , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$orig_url = $this->get_post_urlabs( $orig_url );
			$new_url = $this->get_post_urlabs( $new_url );
			//var_dump('<pre>', $orig_url, $new_url , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			// try to add new redirect row
			$this->slug_add_redirect_row(array(
				'group_id'		=> 2,
				'post_id'		=> $post_id,
				'orig_url'		=> $orig_url,
				'new_url'		=> $new_url,
			));
		}

		public function slug_add_redirect_row( $pms=array() ) {
			extract($pms);

			$is_found = $this->isFoundUrlRedirect( $orig_url );
			if ( $is_found['id'] ) {

				// update row
				$_REQUEST['return'] = 'array';
				$_REQUEST['force_save'] = 'yes';
				$_REQUEST['sub_action'] = '';
				$_REQUEST['itemid'] = $is_found['id'];

				$_REQUEST['post_id'] = $post_id;
				$_REQUEST['group_id'] = $group_id;

				$_REQUEST['new_url2'] = $orig_url;
				$_REQUEST['new_url_redirect2'] = $new_url;
				$_REQUEST['redirect_type2'] = '';

				$this->updateToRedirect();
			}
			else {
				// insert row
				$_REQUEST['return'] = 'array';
				$_REQUEST['force_save'] = 'yes';

				$_REQUEST['post_id'] = $post_id;
				$_REQUEST['group_id'] = $group_id;

				$_REQUEST['new_url'] = $orig_url;
				$_REQUEST['new_url_redirect'] = $new_url;
				$_REQUEST['redirect_type'] = '';
				$_REQUEST['redirect_rule'] = 'custom_url';

				$this->addToRedirect();
			}
		}

		public function slug_can_monitor_post( $post_id, $post, $post_before ) {
			// post is a revision?
			if ( wp_is_post_revision( $post_before ) && wp_is_post_revision( $post ) ) {
				return false;
			}

			// get post original url
			$orig_url = $this->slug_get_post_orig_url( $post, $post_before );
			if ( ! $orig_url ) {
				return false;
			}

			// status must be published
			$post_now_id = isset($post->ID) && $post->ID ? $post->ID : 0;
			$post_now_valid = $this->is_public_post_status( $post_now_id );

			$post_before_id = isset($post_before->ID) && $post_before->ID ? $post_before->ID : 0;
			$post_before_valid = $this->is_public_post_status( $post_before_id );

			if ( ! $post_before_valid || ! $post_now_valid ) {
				return false;
			}

			// must not be hierarchical post
			$post_type = isset($post->post_type) ? $post->post_type : '';
			if ( empty($post_type) || is_post_type_hierarchical( $post_type ) ) {
				return false;
			}

			// get post new url
			$new_url = $this->get_post_urlpath( $post_id );
			//var_dump('<pre>',$orig_url, $new_url ,'</pre>');

			return array(
				'orig_url'	=> $orig_url,
				'new_url'	=> $new_url,
			);
		}

		public function slug_can_create_redirect( $orig_url, $new_url ) {
			$site_path = $this->get_site_path();

			$rules = array();
			$rules[] = $orig_url !== $new_url;
			$rules[] = $orig_url !== '/';
			$rules[] = $orig_url !== $site_path;

			foreach ($rules as $rule) {
				if ( ! $rule ) {
					return false;
				}
			}
			return true;
		}

		public function get_post_urlpath( $post_id ) {
			$url = get_permalink( $post_id );
			$url = parse_url( $url, PHP_URL_PATH );

			return $url;
		}

		public function get_site_path() {
			$site_url = get_site_url();
			$site_path = parse_url( $site_url, PHP_URL_PATH );

			if ( $site_path ) {
				return rtrim($site_path, '/') . '/';
			}
			return '/';
		}

		public function get_post_urlabs( $url_path ) {
			$site_url = get_site_url();
			$site_path = parse_url( $site_url, PHP_URL_PATH );

			$target = $site_url;
			if ( $site_path ) {
				$target = str_replace($site_path, '', $site_url);
			}
			$target = rtrim($target, '/');
			$target .= $url_path;
			return $target;

			//$target = $this->build_absolute_url( $target );
			//$target = $this->get_clean_url( $target );
			//return $target;
		}

		public function is_public_post_status( $post_id ) {
			$allowed = array('publish');
			$post_status = get_post_status( $post_id );
			if ( in_array($post_status, $allowed) ) {
				return true;
			}
			return false;
		}

		//:: TERM
		// add term original url as an hidden input to term edit screen, to be used in edited_term wp hook
		public function slug_add_term_orig_url( $term, $taxonomy ) {

			if ( is_null($term) || empty($term) ) {
				return false;
			}

			$url = $this->get_term_urlpath( $term, $taxonomy );
			
			echo  '<input type="hidden" name="psp_orig_term_url" value="' . esc_attr( $url ) . '"/>';
		}

		public function slug_add_term_orig_url_quick_edit() {
			$url = $this->get_taxonomy_permalink();

			if ( ! is_wp_error($url) ) {
				$url = parse_url( $url, PHP_URL_PATH );
				$this->orig_url = $url;
			}
		}

		public function slug_get_term_orig_url() {
			// $_POST filter to find our hidden input field
			$orig_url = filter_input( INPUT_POST, 'psp_orig_term_url' );

			// we have the hidden input field defined
			if ( ! empty($orig_url) ) {
				return $orig_url;
			}

			if ( ! empty($this->orig_url) ) {
				return $this->orig_url;
			}
			return false;
		}

		// check if term slug was changed considering the original url
		public function slug_check_term_slug_changed( $term_id, $tt_id, $taxonomy ) {

			$can_monitor = $this->slug_can_monitor_term( $term_id, $tt_id, $taxonomy );
			//var_dump('<pre>', $can_monitor , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			if ( ! isset($can_monitor['orig_url']) || ! isset($can_monitor['new_url']) ) {
				return false;
			}
			extract($can_monitor);

			// can create new redirect row
			$can_create_redirect = $this->slug_can_create_redirect( $orig_url, $new_url );
			//var_dump('<pre>', $can_create_redirect , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			if ( ! $can_create_redirect ) {
				return false;
			}

			//var_dump('<pre>', $orig_url, $new_url , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$orig_url = $this->get_post_urlabs( $orig_url );
			$new_url = $this->get_post_urlabs( $new_url );
			//var_dump('<pre>', $orig_url, $new_url , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			// try to add new redirect row
			$this->slug_add_redirect_row(array(
				'group_id'		=> 3,
				'post_id'		=> $term_id,
				'orig_url'		=> $orig_url,
				'new_url'		=> $new_url,
			));
		}

		public function slug_can_monitor_term( $term_id, $tt_id, $taxonomy ) {
			// get term original url
			$orig_url = $this->slug_get_term_orig_url();
			if ( ! $orig_url ) {
				return false;
			}

			// get post new url
			$new_url = $this->get_term_urlpath( $term_id, $taxonomy );
			//var_dump('<pre>',$orig_url, $new_url ,'</pre>');

			return array(
				'orig_url'	=> $orig_url,
				'new_url'	=> $new_url,
			);
		}

		public function get_term_urlpath( $term, $taxonomy ) {
			$url = get_term_link( $term, $taxonomy );
			$url = parse_url( $url, PHP_URL_PATH );

			return $url;
		}

		public function get_taxonomy_permalink() {
			$tax_ID = filter_input( INPUT_POST, 'tax_ID' );
			$taxonomy = filter_input( INPUT_POST, 'taxonomy' );
			$term = get_term( $tax_ID, $taxonomy );
			$term_link = get_term_link( $term, $taxonomy );

			return $term_link;
		}


		/**
		 * backend methods: build the admin interface
		 *
		 */
		private function createTable() {
			global $wpdb;
			
			// check if table exist, if not create table
			$table_name = $wpdb->prefix . "psp_link_redirect";
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

				$sql = "
					CREATE TABLE IF NOT EXISTS " . $table_name . " (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `hits` int(10) DEFAULT '0',
					  `url` varchar(150) DEFAULT NULL,
					  `url_redirect` varchar(150) DEFAULT NULL,
					  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`),
					  UNIQUE INDEX `unique` (`url`,`url_redirect`)
					);
					";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				dbDelta($sql);
			}
		}

		public function isFoundUrlRedirect( $url ) {

		}

		//addToRedirect: add new row into link redirect table
		public function addToRedirect()
		{
			global $wpdb;

			$request = array(
				//'itemid' 		=> isset($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : $itemid
				'force_save'	=> isset($_REQUEST['force_save']) ? trim($_REQUEST['force_save']) : 'no',
				'return'		=> isset($_REQUEST['return']) ? trim($_REQUEST['return']) : '',

				'url' 			=> isset($_REQUEST['new_url']) ? trim($_REQUEST['new_url']) : '',
				'url_redirect'	=> isset($_REQUEST['new_url_redirect']) ? trim($_REQUEST['new_url_redirect']) : '',
				'hits' 			=> isset($_REQUEST['new_hits']) ? trim($_REQUEST['new_hits']) : '0',
				'redirect_type'	=> isset($_REQUEST['redirect_type']) ? trim($_REQUEST['redirect_type']) : '',
				'redirect_rule'	=> isset($_REQUEST['redirect_rule']) ? trim($_REQUEST['redirect_rule']) : 'custom_url',

				'post_id' 		=> isset($_REQUEST['post_id']) ? (int) $_REQUEST['post_id'] : 0,
				'group_id' 		=> isset($_REQUEST['group_id']) ? (int) $_REQUEST['group_id'] : 1,
			);

			$can_force_save = false;
			$ret = array(
				'status' 	=> 'invalid',
				'html'		=> '',
				'msg'		=> '',
				'can_force_save' => $can_force_save ? 'yes' : 'no',
			);

			$is_regexp = isset($request['redirect_rule']) && ('regexp' == $request['redirect_rule']) ? true : false;

			$msg = ''; $is_valid = true;
			if ( $is_valid && ($request['url']=='' || $request['url_redirect']=='') ) {
				$is_valid = false;
				$msg = __('You didn\'t complete the necessary fields!', 'psp');
			}
			if ( $is_valid && ($request['url'] == $request['url_redirect']) ) {
				$is_valid = false;
				$msg = __('URL & URL Redirect fields are identical!', 'psp');
			}

			$status_code = array();
			if ( ! $is_regexp && $is_valid ) {
				$status_code = $this->get_status_code(array(
					'url'		=> $request['url_redirect'],
				));
			}
			if ( ! $is_regexp && $is_valid ) {
				if ( 'invalid' == $status_code['status'] ) {
					$is_valid = false;
					if ( 'invalid_string' != $status_code['resp_code'] ) {
						$can_force_save = true;
					}
					$msg = $status_code['resp_msg'];
				}
			}

			if ( ! $is_valid && ('yes' != $request['force_save']) ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> $msg,
					'can_force_save' => $can_force_save ? 'yes' : 'no',
				));

				if ( $request['return'] == 'array' ) {
					return $ret;
				}
				die(json_encode($ret));
			}

			if (1) {
				$request['url'] = str_replace("\\\\", "\\", $request['url']);
				$wpdb->insert(
					$wpdb->prefix . "psp_link_redirect", 
					array( 
						'url' 						=> $request['url'],
						'url_redirect' 				=> $request['url_redirect'],
						'hits'						=> $request['hits'],
						'redirect_type' 			=> $request['redirect_type'],
						'redirect_rule' 			=> $request['redirect_rule'],
						'target_status_code'		=> isset($status_code['status']) ? $status_code['status'] : '',
						'target_status_details'		=> isset($status_code['status']) ? serialize($status_code) : '',
						'post_id'					=> $request['post_id'],
						'group_id'					=> $request['group_id'],
					), 
					array( 
						'%s',
						'%s',
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
					)
				);
				$insert_id = $wpdb->insert_id;
				if ($insert_id<=0) {
					$ret = array_replace_recursive($ret, array(
						'msg' 	=> 'error at inserting into db.',
					));

					if ( $request['return'] == 'array' ) {
						return $ret;
					}
					die(json_encode($ret));
				}
			}

			//keep page number & items number per page
			$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);

			// return for ajax
			$list_table = $this->ajax_list_table_rows();

			$ret = array_replace_recursive($ret, array(
				'status' => 'valid',
				'html'	 => $list_table['html'],
			));

			if ( $request['return'] == 'array' ) {
				return $ret;
			}
			die(json_encode($ret));
		}

		//updateToRedirect: update row from link redirect table
		public function updateToRedirect()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
				'sub_action' 	=> isset($_REQUEST['sub_action']) ? trim($_REQUEST['sub_action']) : '',
				'force_save'	=> isset($_REQUEST['force_save']) ? trim($_REQUEST['force_save']) : 'no',
				'return'		=> isset($_REQUEST['return']) ? trim($_REQUEST['return']) : '',

				'url' 			=> isset($_REQUEST['new_url2']) ? trim($_REQUEST['new_url2']) : '',
				'url_redirect'	=> isset($_REQUEST['new_url_redirect2']) ? trim($_REQUEST['new_url_redirect2']) : '',
				'redirect_type'	=> isset($_REQUEST['redirect_type2']) ? trim($_REQUEST['redirect_type2']) : '',

				'post_id' 		=> isset($_REQUEST['post_id']) ? (int) $_REQUEST['post_id'] : 0,
				'group_id' 		=> isset($_REQUEST['group_id']) ? (int) $_REQUEST['group_id'] : 1,
			);

			$can_force_save = false;
			$ret = array(
				'status' 	=> 'invalid',
				'html'		=> '',
				'msg'		=> '',
				'can_force_save' => $can_force_save ? 'yes' : 'no',
			);

			if ( $request['itemid'] ) {
				$row = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_link_redirect WHERE id = '" . ( $request['itemid'] ) . "'", ARRAY_A );
				$row_id = (int)$row['id'];
			}

			if ( ! $request['itemid'] || ! $row_id ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> 'itemid is empty.',
				));

				if ( $request['return'] == 'array' ) {
					return $ret;
				}
				die(json_encode($ret));
			}

			$is_regexp = isset($row['redirect_rule']) && ('regexp' == $row['redirect_rule']) ? true : false;

			$msg = ''; $is_valid = true;
			if ( $is_valid && ($request['url_redirect']=='') ) {
				$is_valid = false;
				$msg = __('You didn\'t complete the necessary fields!', 'psp');
			}
			if ( $is_valid && ($request['url'] == $request['url_redirect']) ) {
				$is_valid = false;
				$msg = __('URL & URL Redirect fields are identical!', 'psp');
			}

			$status_code = array();
			if ( ! $is_regexp && $is_valid ) {
				$status_code = $this->get_status_code(array(
					'url'		=> $request['url_redirect'],
				));
			}
			if ( ! $is_regexp && $is_valid ) {
				if ( 'invalid' == $status_code['status'] ) {
					$is_valid = false;
					if ( 'invalid_string' != $status_code['resp_code'] ) {
						$can_force_save = true;
					}
					$msg = $status_code['resp_msg'];
				}
			}

			if ( ! $is_valid && ('yes' != $request['force_save']) ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> $msg,
					'can_force_save' => $can_force_save ? 'yes' : 'no',
				));

				if ( $request['return'] == 'array' ) {
					return $ret;
				}
				die(json_encode($ret));
			}

			// do the operation
			{
				{
					// publish/unpublish
					if ( $request['sub_action']=='publish' ) {
						$wpdb->update( 
							$wpdb->prefix . "psp_link_redirect", 
							array( 
								'publish'		=> $row['publish']=='Y' ? 'N' : 'Y'
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
					}
					// update row info!
					else {
						$new_code = $row['target_status_code'];
						if ( $is_regexp ) {
							// if regexp => reset this field
							$new_code = '';
						}
						else if ( isset($status_code['status']) ) {
							$new_code = $status_code['status'];	
						}

						$new_code_details = $row['target_status_details'];
						if ( $is_regexp ) {
							// if regexp => reset this field
							$new_code_details = '';
						}
						else if ( isset($status_code['status']) ) {
							$new_code_details = serialize($status_code);
						}

						$wpdb->update( 
							$wpdb->prefix . "psp_link_redirect", 
							array( 
								'url_redirect'			=> $request['url_redirect'],
								'redirect_type'			=> $request['redirect_type'],
								'target_status_code' 	=> $new_code,
								'target_status_details' => $new_code_details,
								'post_id'				=> $request['post_id'],
								'group_id'				=> $request['group_id'],
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s',
								'%s',
								'%s',
								'%s',
								'%d',
								'%d',
							), 
							array( '%d' ) 
						);
					}

					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);

					$list_table = $this->ajax_list_table_rows();

					$ret = array_replace_recursive($ret, array(
						'status' => 'valid',
						'html'	 => $list_table['html'],
					));

					if ( $request['return'] == 'array' ) {
						return $ret;
					}
					die(json_encode($ret));
				}
			}

			$ret = array_replace_recursive($ret, array(
				'msg' 	=> 'itemid is empty.',
			));

			if ( $request['return'] == 'array' ) {
				return $ret;
			}
			die(json_encode($ret));
		}

		/*
		public function removeFromRedirect()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			if( $request['itemid'] > 0 ) {
				$wpdb->delete( 
					$wpdb->prefix . "psp_link_redirect", 
					array( 'id' => $request['itemid'] ) 
				);
				
				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
				
				die(json_encode(array(
					'status' => 'valid'
				)));
			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		public function delete_rows() {
			global $wpdb; // this is how you get access to the database
			
			$request = array(
				'id' 			=> isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : 0
			);

			if ($request['id']!=0) {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (int) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}

			$table_name = $wpdb->prefix . "psp_link_redirect";
			if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {

				// delete record
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_delete);
				
				//$query_update = "UPDATE " . ($table_name) . " set
				//		deleted=1
				//		where id in (" . ($request['id']) . ");";
				//$__stat = $wpdb->query($query_update);
				
				if ($__stat!== false) {
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);

					die( json_encode(array(
						'status' => 'valid',
						'msg'	 => '' //$query_delete
					)) );
				}
			}
			
			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => ''
			)) );
		}
		*/
		
		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
			$url_server = $this->the_plugin->get_current_page_url(array(
				'exclude_request_uri'	=> true,
			));

			$use_regexp_notice = sprintf( __('
				ATTENTION<br/>
				Be carefull, because you can break your website using Regexp Redirects<br/>
				Use Regexp Redirects only if you know what you\'re doing!<br/>
				Keep in mind:<br/>
				<span>URL: must be a relative url, starting with / and must not include <span>%s</span></span><br/>
				<span>URL Redirect: can be an absolute url, but if you want it relative, must start with / and must not include <span>%s</span></span><br/>
				<span>Ex.: URL: /prods-old-category/([\w_-]+)/ TO URL Redirect: /prods-new-category/$1/</span>
			', 'psp'), $url_server, $url_server );

			$redirect_types = $this->the_plugin->get_redirect_types();

			$redirect_statuses = $this->the_plugin->get_redirect_status_codes();

			$redirect_groups = $this->the_plugin->get_redirect_groups();

			$redirect_rules = array(
				'custom_url' => __('Custom URL', 'psp'),
				'regexp' => __('Regexp', 'psp'),
			);
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		
		<div class="<?php echo $this->the_plugin->alias; ?> psp-mod-link-redirect">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content">
				
				<?php
				// show the top menu
				pspAdminMenu::getInstance()->make_active('off_page_optimization|Link_Redirect')->show_menu();
				?>
				
				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main">
						
					<?php 
					echo psp()->print_section_header(
						$this->module['Link_Redirect']['menu']['title'],
						$this->module['Link_Redirect']['description'],
						$this->module['Link_Redirect']['help']['url']
					);
					?>
					
					<div class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel">
				
						<div id="psp-lightbox-overlay">
							<div id="psp-lightbox-container">
								<h1 class="psp-lightbox-headline">
									<span id="link-title-add"><?php _e('Add new link:', 'psp');?></span>
									<span id="link-title-upd"><?php _e('Update link:', 'psp');?></span>
									<a href="#" class="psp-close-page-detail psp-close-btn">
										<i class="psp-checks-cross2"></i>
									</a>
								</h1>
			
								<div class="psp-seo-status-container">
									<div id="psp-lightbox-seo-report-response">
										<form class="psp-add-link-form">
											<table width="100%">
												<tr>
													<td width="180"><label><?php _e('URL:', 'psp');?></label></td>
													<td><input type="text" id="new_url" name="new_url" value="" class="psp-add-link-field psp-redirect-rule-url" /></td>
												</tr>
												<tr>
													<td><label><?php _e('URL Redirect:', 'psp');?></label></td>
													<td><input type="text" id="new_url_redirect" name="new_url_redirect" value="" class="psp-add-link-field psp-redirect-rule-url-target" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Redirect Type:', 'psp');?></label></td>
													<td>
														<select id="redirect_type" name="redirect_type">
															<?php
																echo '<option value="">' . 'Default' . '</option>';
																foreach ($redirect_types as $key => $value) {
																	echo '<option value="' . ( $key ) . '">' . ( $value ) . '</option>';
																}
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td><label><?php _e('Redirect Rule:', 'psp');?></label></td>
													<td>
														<select id="redirect_rule" name="redirect_rule" class="psp-redirect-rule-sel">
															<?php
																//echo '<option value="">' . 'Default' . '</option>';
																foreach ($redirect_rules as $key => $value) {
																	echo '<option value="' . ( $key ) . '">' . ( $value ) . '</option>';
																}
															?>
														</select>
														<div class="psp-use-regexp-redirects-notice"><?php echo $use_regexp_notice; ?></div>
													</td>
												</tr>
												<tr>
													<td></td>
													<td>
														<input type="button" class="psp-button green" value="<?php _e('Add this new link', 'psp'); ?>" id="psp-submit-to-builder">
													</td>
												</tr>
											</table>
											
										</form>
									</div>
									
									<div id="psp-lightbox-seo-report-response2">
										<form class="psp-update-link-form">
											<input type="hidden" id="upd-itemid" name="upd-itemid" value="" />
											<table width="100%">
												<tr>
													<td width="180"><label><?php _e('URL:', 'psp');?></label></td>
													<td><input type="text" id="new_url2" name="new_url2" value="" class="psp-add-link-field psp-redirect-rule-url" readonly disabled="disabled" /></td>
												</tr>
												<tr>
													<td><label><?php _e('URL Redirect:', 'psp');?></label></td>
													<td><input type="text" id="new_url_redirect2" name="new_url_redirect2" value="" class="psp-add-link-field psp-redirect-rule-url-target" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Redirect Type:', 'psp');?></label></td>
													<td>
														<select id="redirect_type2" name="redirect_type2">
															<?php
																echo '<option value="">' . 'Default' . '</option>';
																foreach ($redirect_types as $key => $value) {
																	echo '<option value="' . ( $key ) . '">' . ( $value ) . '</option>';
																}
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td><label><?php _e('Redirect Rule:', 'psp');?></label></td>
													<td>
														<select id="redirect_rule2" name="redirect_rule2" readonly disabled="disabled" class="psp-redirect-rule-sel">
															<?php
																//echo '<option value="">' . 'Default' . '</option>';
																foreach ($redirect_rules as $key => $value) {
																	echo '<option value="' . ( $key ) . '">' . ( $value ) . '</option>';
																}
															?>
														</select>
														<div class="psp-use-regexp-redirects-notice"><?php echo $use_regexp_notice; ?></div>
													</td>
												</tr>
												<tr>
													<td></td>
													<td>
														<input type="button" class="psp-button green" value="<?php _e('Update link info', 'psp'); ?>" id="psp-submit-to-builder2">
													</td>
												</tr>
											</table>
											
										</form>
									</div>
									<div style="clear:both"></div>
								</div>
							</div>
						</div>
			
						<!-- Main loading box -->
						<div id="psp-main-loading">
							<div id="psp-loading-overlay"></div>
							<div id="psp-loading-box">
								<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
								<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
							</div>
						</div>

						<div class="panel-heading psp-panel-heading">
							<h2><?php _e('Link Redirect', 'psp');?></h2>
						</div>
	
						<div class="panel-body <?php echo $this->the_plugin->alias; ?>-panel-body">
							
							<!-- Container -->
							<div class="psp-container clearfix">
			
								<!-- Main Content Wrapper -->
								<div id="psp-content-wrap" class="clearfix">
									
	                        		<div class="psp-panel">
			                        		
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspLinkRedirect',
													'custom_table'		=> "psp_link_redirect",
													//'deleted_field'		=> true,
													//'force_publish_field' 	=> false,
													'show_header' 		=> true,
													'show_header_buttons' => true,
													'items_per_page' 	=> '10',
													//'post_statuses' 	=> 'all',
													'filter_fields'		=> array(
														'publish'  => array(
															'title' 			=> __('Published', $this->the_plugin->localizationName),
															'options_from_db' 	=> false,
															'include_all'		=> true,
															'options'			=> array(
																'Y'			=> __('Published', $this->the_plugin->localizationName),
																'N'			=> __('Unpublished', $this->the_plugin->localizationName),
															),
															'display'			=> 'links',
														),
														'redirect_rule'  => array(
															'title' 			=> __('Redirect rule', $this->the_plugin->localizationName),
															'options_from_db' 	=> false,
															'include_all'		=> true,
															'options'			=> $redirect_rules,
															'display'			=> 'links',
														),
														'redirect_type'  => array(
															'title' 			=> __('Redirect type', $this->the_plugin->localizationName),
															'options_from_db' 	=> false,
															'include_all'		=> true,
															'options'			=> (array('' => 'Global Default') + $redirect_types),
															'display'			=> 'links',
														),
														'target_status_code'  => array(
															'title' 			=> __('Status', $this->the_plugin->localizationName),
															'options_from_db' 	=> false,
															'include_all'		=> true,
															'options'			=> (array('' => 'Never Checked') + $redirect_statuses),
															'display'			=> 'links',
														),
														'group_id'  => array(
															'title' 			=> __('Group', $this->the_plugin->localizationName),
															'options_from_db' 	=> false,
															'include_all'		=> true,
															'options'			=> (array() + $redirect_groups),
															'display'			=> 'links',
														),
													),
													'search_box'		=> array(
														'title' 	=> __('Search', $this->the_plugin->localizationName),
														'fields'	=> array('url', 'url_redirect'),
													),
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),

														'id'		=> array(
															'th'	=> __('ID', 'psp'),
															'td'	=> '%id%',
															'width' => '20'
														),

														'hits'		=> array(
															'th'	=> __('Hits', 'psp'),
															'td'	=> '%hits%',
															'width' => '15'
														),

														'url'		=> array(
															'th'	=> __('URL', 'psp'),
															'td'	=> '%linkred_url%',
															'align' => 'left',
															//'width' => '35%',
															'class'	=> 'psp-url-orig',
															'css'	=> array(
																//'border'		=> '1px solid blue',
																//'max-width'		=> '50rem !important',
																//'overflow-wrap'	=> 'break-word !important', //break-word
															),
														),

														'url_redirect'		=> array(
															'th'	=> __('URL Redirect', 'psp'),
															'td'	=> '%linkred_url_redirect%',
															'align' => 'left',
															//'width' => '35%',
															'class' => 'psp-url-redirect',
														),

														/*'redirect_type'		=> array(
															'th'	=> __('Redirect Type', 'psp'),
															'td'	=> '%redirect_type%',
															'width' => '80',
														),
														'redirect_rule'		=> array(
															'th'	=> __('Redirect Rule', 'psp'),
															'td'	=> '%redirect_rule%',
															'width' => '80',
														),*/
														'redirect_type'		=> array(
															'th'	=> __('Redirect', 'psp'),
															'td'	=> '%redirect_type_and_rule%',
															'width' => '90',
														),

														'last_check_status'		=> array(
															'th'	=> __('Last check', 'psp'),
															'td'	=> '%last_check_status%',
															'align' => 'center',
															'width' => '100'
														),
														
														'created'		=> array(
															'th'	=> __('Creation Date', 'psp'),
															'td'	=> '%created%',
															'width' => '115'
														),

														'publish_btn' => array(
															'th'	=> __('Operations', 'psp'),
															'td'	=> '%buttons_group%',
															'option' => array(
																array(
																	'value' => __('Unpublish', 'psp'),
																	'value_change' => __('Publish', 'psp'),
																	'action' => 'do_item_publish',
																	'color'	=> 'warning',
																	'icon' => '<i class="fa fa-eye-slash"></i>',
																	'icon_change' => '<i class="fa fa-eye"></i>'
																),
																array(
																	'value' => __('Update', 'psp'),
																	'action' => 'do_item_update',
																	'color'	=> 'info',
																	'icon' => '<i class="fa fa-edit"></i>',
																),
																array(
																	'value' => __('Delete', 'psp'),
																	'action' => 'do_item_delete',
																	'color'	=> 'danger',
																	'icon' => '<i class="fa fa-times"></i>',
																),
																array(
																	'value' => __('Verify URL Redirect Status', 'psp'),
																	'action' => 'do_item_verify',
																	'color'	=> 'info',
																	'icon' => '<i class="fa fa-refresh"></i>',
																)
															),
															'width' => '130',
														),
														/*
														'update_btn' => array(
															'th'	=> __('Update', 'psp'),
															'td'	=> '%button%',
															'option' => array(
																'value' => __('Update', 'psp'),
																'action' => 'do_item_update',
																'color'	=> 'info',
															),
															'width' => '30'
														),
														'delete_btn' => array(
															'th'	=> __('Delete', 'psp'),
															'td'	=> '%button%',
															'option' => array(
																'value' => __('Delete', 'psp'),
																'action' => 'do_item_delete',
																'color'	=> 'danger',
															),
															'width' => '30'
														)
														*/
													),
													'mass_actions' 	=> array(
														'add_new_link' => array(
															'value' => __('Add new link', 'psp'),
															'action' => 'do_add_new_link',
															'color' => 'info'
														),
														'delete_all_rows' => array(
															'value' => __('Delete selected rows', 'psp'),
															'action' => 'do_bulk_delete_rows',
															'color' => 'danger'
														)
													)
												))
												->print_html();
								            ?>
								            </div>
								            <div>
								            	<ul>
								            		<li><?php _e('<strong>search</strong> = search in url, url redirect.', 'psp'); ?></li>
								            		<li><?php _e('<strong>*</strong> = the global default value from module settings.', 'psp'); ?></li>
								            		<li><?php _e('<strong>**</strong> = we cannot verify regexp redirects, but this column value might be updated when such a redirect occures in the frontend (also we reset this value for regexp if you update the row).', 'psp'); ?></li>
								            	</ul>
								            </div>
							            </form>
				            		</div>
				            		
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
<?php
		}
		
		private function prepareForInList($v) {
			return "'".$v."'";
		}

		private function build_absolute_url( $url ) {
			$is_rel = $this->is_relative_url( $url );
			if ( 0 === $is_rel) {
				$protocol = $this->the_plugin->is_ssl() ? 'https' : 'http';
				$url = $protocol . '://' . $url;
			}
			else if ( $is_rel ) {
				//$url = home_url( $url );
				$url_server = $this->the_plugin->get_current_page_url(array(
					'exclude_request_uri'	=> true,
				));
				$url = $url_server . $url;
			}
			return $url;
		}

		private function is_relative_url( $url ) {
			if ( preg_match('/^http(s?):\/\//i', $url) > 0 ) {
				return false;
			}

			$url_scheme = parse_url( $url, PHP_URL_SCHEME );
			if ( ! empty($url_scheme) ) {
				return false;
			}

			// fix to make parse_url work with links like www.example.com
			$url = 'http://'.$url;
			$url_host = parse_url( $url, PHP_URL_HOST );
			if ( ! empty($url_host) ) {
				return 0; // has the host but it doesn't have the protocol
			}
			return true;
		}

		private function get_clean_url( $url ) {
			return $this->the_plugin->get_clean_url( $url );
		}

		private function is_valid_url( $url ) {
			return $this->the_plugin->is_valid_url( $url );
		}

		private function is_status_temporary( $resp_code ) {
			$resp_code = trim($resp_code);
			if ( '' == $resp_code ) {
				return 0;
			}

			// 302 Moved Temporarily & 307 Temporary Redirect
			if ( in_array($resp_code, array(302, 307)) ) {
				return true;
			}
			return false;
		}

		private function is_status_error( $resp_code ) {
			$resp_code = trim($resp_code);
			if ( '' == $resp_code ) {
				return 0;
			}

			// 4xx client errors & 5xx server errors
			if ( in_array(substr($resp_code, 0, 2), array(40, 50)) ) {
				return true;				
			}
			return false;
		}


		/**
		 * AJAX
		 *
		 */
		public function ajax_request()
		{
			global $wpdb;

			$request = array(
				'action' 		=> isset($_REQUEST['sub_action']) ? trim($_REQUEST['sub_action']) : '',
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
			);
			extract( $request );

			$ret = array(
				'status'		=> 'invalid',
				'data'			=> '',
			);

			if ( $action == 'get_details') {
				$sql = "SELECT * from " . $wpdb->prefix . "psp_link_redirect WHERE 1=1 and id=" . ( $request['itemid'] ) . ";";
				$ret = array_replace_recursive($ret, array(
					'status'		=> 'valid',
					'data'			=> $wpdb->get_row( $sql ),
				));
			}
			else if ( $action == 'get_status_code') {
				$sql = "SELECT * from " . $wpdb->prefix . "psp_link_redirect WHERE 1=1 and id=" . ( $request['itemid'] ) . ";";
				$row = $wpdb->get_row( $sql );

				$status_code = array();
				if ( isset($row->id) ) {
					$status_code = $this->get_status_code(array(
						'url'		=> $row->url_redirect,
					));
					$this->update_status_code(array(
						'itemid'		=> $request['itemid'],
						'status_code'	=> $status_code,
					));
				}
				$ret = array_replace_recursive($ret, $status_code);

				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);

				// return for ajax
				$list_table = $this->ajax_list_table_rows();

				$ret = array_replace_recursive($ret, array(
					'html'		=> $list_table['html'],
				));
			}
			die(json_encode($ret));
		}

		private function ajax_list_table_rows() {
			return pspAjaxListTable::getInstance( $this->the_plugin )->list_table_rows( 'return', array() );
		}
    }
}

// Initialize the pspLinkRedirect class
$pspLinkRedirect = pspLinkRedirect::getInstance();