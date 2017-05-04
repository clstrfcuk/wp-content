<?php
/**
 * AA-Team freamwork class
 * http://www.aa-team.com
 * =======================
 *
 * @package		psp
 * @author		Andrei Dinca, AA-Team
 * @version		2.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('psp') != true) {
	class psp {
		
		public $version = null; // see version method for details

		const VERSION = 1.0;
        
        // The time interval for the remote XML cache in the database (21600 seconds = 6 hours)
        const NOTIFIER_CACHE_INTERVAL = 21600;

		public $alias = 'psp';
		public $details = array();
		public $localizationName = 'psp';
		
		public $dev = '';
		public $debug = false;
		public $is_admin = false;

		/**
		 * configuration storage
		 *
		 * @var array
		 */
		public $cfg = array();
		
		/**
		 * logic storage
		 *
		 * @var string
		 */
		public $is_plugin_page = false;

		/**
		 * plugin modules storage
		 *
		 * @var array
		 */
		public $modules = null;

		/**
		 * errors storage
		 *
		 * @var object
		 */
		private $errors = null;

		/**
		 * DB class storage
		 *
		 * @var object
		 */
		public $db = array();

		public $facebookInstance = null;
		public $fb_user_profile = null;
		public $fb_user_id = null;

		public $plugin_hash = null;
		public $v = null;
		
		public $utf8;
		
		public $jsFiles = array();
        
        public $wp_filesystem = null;
		
		public $charset = '';
		
		public $pluginDepedencies = null;
		public $pluginName = 'Premium SEO Pack';
		
		public $feedback_url = "http://aa-team.com/feedback/index.php?app=%s&refferer_url=%s";
		
		public $buddypress_utils = null;
        
        public $app_settings = array(); // all plugin settings
        
        public $u; // utils function object!
        public $pu; // utils function object!
        
        // New Settings / february 2016
        public $plugin_details = array(); // see constructor
        public $ss = array(
			// admin css cache time ( 0 = no caching )
			'css_cache_time'							=> 86400, // 1day / seconds  (86400 seconds = 24 hours)

			// timeout to verify if all plugin tables are installed right!
			'check_integrity'							=> array(
				// seconds  (86400 seconds = 24 hours)
				'check_tables'									=> 259200, // 3 days
				'check_alter_tables'							=> 259200, // 3 days
			),
			
			'add_meta_placeholder'					=> true,
		);
		private static $plugin_row_meta = array(
			'buy_url'			=> 'http://codecanyon.net/item/premium-seo-pack-wordpress-plugin/6109437',
			'docs_url'			=> 'http://docs.aa-team.com/products/premium-seo-pack/',
			'support_url'		=> 'http://support.aa-team.com/',
			'latest_ver_url'	=> 'http://cc.aa-team.com/apps-versions/index.php?app=',
			'portfolio'         => 'http://codecanyon.net/user/aa-team/portfolio',
		);
		
		public $plugin_tables = array('psp_link_builder', 'psp_link_redirect', 'psp_monitor_404', 'psp_web_directories', 'psp_serp_reporter', 'psp_serp_reporter2rank', 'psp_post_planner_cron');
		
		public $title_meta_format_default = array();


		/**
		 * The constructor
		 */
		public function __construct($here = __FILE__)
		{
			$this->is_admin = is_admin() === true ? true : false;

			// admin css cache time ( 0 = no caching )
			$this->ss['css_cache_time'] = 86400; // seconds  (86400 seconds = 24 hours)
			if( defined('PSP_DEV_STYLE') ){
				$this->ss['css_cache_time'] = (int) PSP_DEV_STYLE; // seconds
			}
            
            add_action('wp_ajax_PSP_framework_style', array( $this, 'framework_style') );
            add_action('wp_ajax_nopriv_PSP_framework_style', array( $this, 'framework_style') );
			
			//$current_url = $_SERVER['HTTP_REFERER'];
			$current_url = $this->get_current_page_url();
			$this->feedback_url = sprintf($this->feedback_url, $this->alias, rawurlencode($current_url));

			$this->setIniConfiguration();
            
            // load WP_Filesystem 
            include_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
            global $wp_filesystem;
            $this->wp_filesystem = $wp_filesystem;

			$this->update_developer();
			
			$this->plugin_hash = get_option('psp_hash');

			// set the freamwork alias
			$this->buildConfigParams('default', array( 'alias' => $this->alias ));

			// get the globals utils
			global $wpdb;

			// store database instance
			$this->db = $wpdb;

			// instance new WP_ERROR - http://codex.wordpress.org/Function_Reference/WP_Error
			$this->errors = new WP_Error();

			// charset
			$optimizeSettings = $this->getAllSettings( 'array', 'on_page_optimization' );
			if ( isset($optimizeSettings['charset']) && !empty($optimizeSettings['charset']) ) {
			    $this->charset = $optimizeSettings['charset'];
            }
  
			// plugin root paths
			$this->buildConfigParams('paths', array(
				// http://codex.wordpress.org/Function_Reference/plugin_dir_url
				'plugin_dir_url' => str_replace('aa-framework/', '', plugin_dir_url( (__FILE__)  )),

				// http://codex.wordpress.org/Function_Reference/plugin_dir_path
				'plugin_dir_path' => str_replace('aa-framework/', '', plugin_dir_path( (__FILE__) ))
			));

			// add plugin lib design paths and url
			$this->buildConfigParams('paths', array(
				'design_dir_url' => $this->cfg['paths']['plugin_dir_url'] . 'lib/design',
				'design_dir_path' => $this->cfg['paths']['plugin_dir_path'] . 'lib/design'
			));

			// add plugin scripts paths and url
			$this->buildConfigParams('paths', array(
				'scripts_dir_url' => $this->cfg['paths']['plugin_dir_url'] . 'lib/scripts',
				'scripts_dir_path' => $this->cfg['paths']['plugin_dir_path'] . 'lib/scripts'
			));

			// add plugin admin paths and url
			$this->buildConfigParams('paths', array(
				'freamwork_dir_url' => $this->cfg['paths']['plugin_dir_url'] . 'aa-framework/',
				'freamwork_dir_path' => $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/'
			));

			// add core-modules alias
			$this->buildConfigParams('core-modules', array(
				'dashboard',
				'modules_manager',
				'setup_backup',
				'support',
				'remote_support',
				'frontend',
				'server_status',
				'misc'
			));

			// list of freamwork css files
			$this->buildConfigParams('freamwork-css-files', array(
				'core' => 'css/core.css',
				'panel' => 'css/panel.css',
				'form-structure' => 'css/form-structure.css',
				'form-elements' => 'css/form-elements.css',
				'form-message' => 'css/form-message.css',
				'button' => 'css/button.css',
				'table' => 'css/table.css',
				'tipsy' => 'css/tooltip.css',
				'additional' => 'css/additional.css'
			));

			// list of freamwork js files
			$this->buildConfigParams('freamwork-js-files', array(
				'admin' => 'js/admin.js',
				'hashchange' => 'js/hashchange.js',
				'ajaxupload' => 'js/ajaxupload.js',
				'tipsy'	=> 'js/tooltip.js',
				'percentageloader-0.1' => 'js/jquery.percentageloader-0.1.min.js',
				'flot-2.0' => 'js/jquery.flot/jquery.flot.min.js',
				'flot-tooltip' => 'js/jquery.flot/jquery.flot.tooltip.min.js',
				'flot-stack' => 'js/jquery.flot/jquery.flot.stack.min.js',
				'flot-pie' => 'js/jquery.flot/jquery.flot.pie.min.js',
				'flot-time' => 'js/jquery.flot/jquery.flot.time.js',
				'flot-resize' => 'js/jquery.flot/jquery.flot.resize.min.js'
			));

			// plugin folder in wp-content/plugins/
			$plugin_folder = explode('wp-content/plugins/', $this->cfg['paths']['plugin_dir_path']);
			$plugin_folder = end($plugin_folder);
			$this->plugin_details = array(
				'folder'		=> $plugin_folder,
				'folder_index'	=> $plugin_folder . 'plugin.php',
			);
            
            // utils functions
            require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/utils.php' );
            if( class_exists('psp_Utils') ){
                // $this->u = new psp_Utils( $this );
                $this->u = psp_Utils::getInstance( $this );
            }
			
			// plugin utils functions
            require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/plugin_utils.php' );
            if( class_exists('psp_PluginUtils') ){
                // $this->pu = new psp_PluginUtils( $this );
                $this->pu = psp_PluginUtils::getInstance( $this );
            }
			
            // get plugin text details
            $this->get_plugin_data();

            if ( $this->is_admin ) {

    			// Validation - mandatory step, try to load the validation file
    			$v_file_path = $this->cfg['paths']['plugin_dir_path'] . 'validation.php';
    			if ( $this->verifyFileExists($v_file_path) ) {
    				if ( $this->doPluginValidation( $v_file_path ) ) {
    					require_once( $v_file_path );
    					$this->v = new psp_Validation();
    					$this->v->isReg($this->plugin_hash);
    				}
    			}
			
                // load menu
    			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/menu.php' );
    			
    			// Run the plugins section load method
    			add_action('wp_ajax_pspLoadSection', array( $this, 'load_section' ));
    			
    			// Plugin Depedencies Verification!
    			if ( get_option('psp_depedencies_is_valid', false) ) {
    				require_once( $this->cfg['paths']['scripts_dir_path'] . '/plugin-depedencies/plugin_depedencies.php' );
    				$this->pluginDepedencies = new aaTeamPluginDepedencies( $this );
    
    				// activation redirect to depedencies page
    				if ( get_option('psp_depedencies_do_activation_redirect', false) ) {
    					add_action('admin_init', array($this->pluginDepedencies, 'depedencies_plugin_redirect'));
    					return false;
    				}
       
       				// verify plugin library depedencies
    				$depedenciesStatus = $this->pluginDepedencies->verifyDepedencies();
    				if ( $depedenciesStatus['status'] == 'valid' ) {
    					// go to plugin license code activation!
    					add_action('admin_init', array($this->pluginDepedencies, 'depedencies_plugin_redirect_valid'));
    				} else {
    					// create depedencies page
    					add_action('init', array( $this->pluginDepedencies, 'initDepedenciesPage' ), 5);
    					return false;
    				}
    			}
			} // end is_admin
			
			// Run the plugins initialization method
			add_action('init', array( $this, 'initThePlugin' ), 5);
			add_action('init', array( $this, 'session_start' ), 1);

            if ( $this->is_admin ) {

    			// Run the plugins section options save method
    			add_action('wp_ajax_pspSaveOptions', array( $this, 'save_options' ));
    
    			// Run the plugins section options save method
    			add_action('wp_ajax_pspModuleChangeStatus', array( $this, 'module_change_status' ));
    			
    			// Run the plugins section options save method
    			add_action('wp_ajax_pspModuleChangeStatus_bulk_rows', array( $this, 'module_bulk_change_status' ));
    
    			// Run the plugins section options save method
    			add_action('wp_ajax_pspInstallDefaultOptions', array( $this, 'install_default_options' ));
    
    			// W3CValidate helper
    			add_action('wp_ajax_pspW3CValidate', array( $this, 'pspW3CValidate' ));
    
    			// W3CValidate helper
    			add_action('wp_ajax_pspUpload', array( $this, 'upload_file' ));
    			add_action('wp_ajax_pspWPMediaUploadImage', array( $this, 'wp_media_upload_image' ));
				add_action('wp_ajax_pspDismissNotice', array( $this, 'dismiss_notice' ));
            } // end is_admin
			
			require_once( $this->cfg['paths']['scripts_dir_path'] . '/utf8/utf8.php' );
			$this->utf8 = pspUtf8::getInstance();
			
			// admin ajax action
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/action_admin_ajax.php' );
			new pspActionAdminAjax( $this );
			
            if ( $this->is_admin ) {
    			// import seo data
    			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/import_seodata.php' );
    			new pspImportSeoData( $this );
            }
			
			// buddy press utils
			if ( $this->is_buddypress() ) {
				require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/buddypress.php' );
				$this->buddypress_utils = new pspBuddyPress( $this );
			}
			
			add_action('admin_init', array($this, 'plugin_redirect'));
			
			if( $this->debug == true ){
				add_action('wp_footer', array($this, 'print_psp_usages') );
				add_action('admin_footer', array($this, 'print_psp_usages') );
			}

            if ( $this->is_admin ) {
                //add_action( 'admin_bar_menu', array($this->pu, 'update_notifier_bar_menu'), 1000 );
                //add_action( 'admin_menu', array($this->pu, 'update_plugin_notifier_menu'), 1000 );
				
				// add additional links below plugin on the plugins page
				//add_filter( 'plugin_row_meta', array($this->pu, 'plugin_row_meta_filter'), 10, 2 );
		
				// alternative API to check updating for the filter transient
				//add_filter( 'pre_set_site_transient_update_plugins', array( $this->pu, 'update_plugins_overwrite' ), 10, 1 );
   
				// alternative response with plugin details for admin thickbox tab
				//add_filter( 'plugins_api', array( $this->pu, 'plugins_api_overwrite' ), 10, 3 );
				
				// message on wp plugins page with updating link
				//add_action( 'in_plugin_update_message-'.$this->plugin_details['folder_index'], array($this->pu, 'in_plugin_update_message'), 10, 2 );
            }
			
            if ( $this->is_admin ) {
                require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/ajax-list-table.php' );
                new pspAjaxListTable( $this );
            }
			
			// shortcodes
			require_once($this->cfg['paths']['plugin_dir_path'] . 'aa-framework/shortcodes/shortcodes.init.php');
			new aafShortcodes( $this );
			
			// clean cronjobs
			$this->cronjobs_clean_fix();
			
			// fix bugs
			$this->fix_backlinkbuilder_linklist();
			
			if ( !$this->is_admin ) {
				if ( isset($_POST['ispspreq']) && in_array( $_POST['ispspreq'], array('tax', 'post') ) ) {
					if ( $_POST['ispspreq'] == 'post' )
						add_filter( 'the_content', array( $this, 'mark_content' ), 0, 1 );
					else if ( $_POST['ispspreq'] == 'tax' ) {
						add_filter( 'term_description', 'do_shortcode' );
						add_filter( 'term_description', array( $this, 'mark_content' ), 0, 1 );
					}
					add_action( 'wp', array( $this, 'clean_header' ) );
				}
			}

			$is_installed = get_option( $this->alias . "_is_installed" );
			if( $this->is_admin && $is_installed === false ) {
				add_action( 'admin_print_styles', array( $this, 'admin_notice_install_styles' ) );
			}
		}

		public function framework_style()
        {
            $start = microtime(true);
    		
			require $this->cfg['paths']['scripts_dir_path'] . "/scssphp/scss.inc.php";

            $scss = new scssc();

			$main_file = $this->wp_filesystem->get_contents( $this->cfg['paths']['freamwork_dir_path'] . "/scss/styles.scss" );
			if( !$main_file ){
				$main_file = file_get_contents( $this->cfg['paths']['freamwork_dir_path'] . "/scss/styles.scss" );
			}

            $files = array();
            if(preg_match_all('/@import (url\(\"?)?(url\()?(\")?(.*?)(?(1)\")+(?(2)\))+(?(3)\")/i', $main_file, $matches)){
                    foreach($matches[4] as $url){
                        if( file_exists( $this->cfg['paths']['freamwork_dir_path'] . "/scss/_" . $url . '.scss') ){ 
                            $files[] = '_' . $url . '.scss';
                        }
                    if( file_exists( $this->cfg['paths']['freamwork_dir_path'] . "/scss/" . $url . '.scss') ){
                            $files[] = $url . '.scss';
                        }
                    }
            }
            
            $buffer = '';
            if( count($files) > 0 ){
                foreach ($files as $scss_file) {
                    if( 0 ){ 
                        $buffer .= "\n" .   "/****-------------------------------\n";
                        $buffer .= "\n" .   " IN FILE: $scss_file \n";
                        $buffer .= "\n" .   "------------------------------------\n";
                        $buffer .= "\n***/\n";
                    }
                    
					$has_wrote = $this->wp_filesystem->get_contents( $this->cfg['paths']['freamwork_dir_path'] . "/scss/" . $scss_file );
					if ( !$has_wrote ) {
						$has_wrote = file_get_contents( $this->cfg['paths']['freamwork_dir_path'] . "/scss/" . $scss_file );
					}
					$buffer .= $has_wrote;
                }
            } 
                        
            $buffer = $scss->compile( $buffer );
			
			#$buffer = str_replace( "fonts/", $this->cfg['paths']['freamwork_dir_url'] . "fonts/", $buffer );
			$buffer = str_replace( '#framework_url/', $this->cfg['paths']['freamwork_dir_url'], $buffer );
			$buffer = str_replace( '#plugin_url', $this->cfg['paths']['plugin_dir_url'], $buffer );
			//$buffer = str_replace( "images/", $this->cfg['paths']['freamwork_dir_url'] . "images/", $buffer );
            
            
            $time_elapsed_secs = microtime(true) - $start;
            $buffer .= "\n\n/*** Compile time: $time_elapsed_secs */";
            
            // Enable caching
            header('Cache-Control: public');

            // Expire in one day
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

            // Set the correct MIME type, because Apache won't set it for us
            header("Content-type: text/css");

            // Write everything out
            echo $buffer;
			
			$buffer = str_replace( $this->cfg['paths']['freamwork_dir_url'], '', $buffer );

			$has_wrote = $this->wp_filesystem->put_contents( $this->cfg['paths']['freamwork_dir_path'] . 'main-style.css', $buffer );
			if ( !$has_wrote ) {
				$has_wrote = file_put_contents( $this->cfg['paths']['freamwork_dir_path'] . 'main-style.css', $buffer );
			}

            die;
        }

        public function print_section_header( $title='', $desc='', $docs_url='')
		{
			$html = array();

			$html[] = '<div class="panel panel-default ' . ( $this->alias ) . '-panel ' . ( $this->alias ) . '-section-header">';
			$html[] =   '<div class="panel-heading ' . ( $this->alias ) . '-panel-heading">';
			if( trim($title) != "" )    $html[] =       '<h1 class="panel-title ' . ( $this->alias ) . '-panel-title">' . ( $title ) . '</h1>';
			if( trim($desc) != "" )     $html[] =       $desc;
			$html[] =   '</div>';
			$html[] =   '<div class="panel-body ' . ( $this->alias ) . '-panel-body ' . ( $this->alias ) . '-no-padding" >';
			
			
			if( trim($docs_url) != "" ) $html[] =       '<a href="' . ( $docs_url ) . '" target="_blank" class="' . ( $this->alias ) . '-tab"><span class="psp-icon-support"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span> Documentation</a>';
			$html[] =       '<a href="' . ( $this->plugin_row_meta( 'portfolio' ) ) . '?ref=AA-Team" target="_blank" class="' . ( $this->alias ) . '-tab"><i class="' . ( $this->alias ) . '-icon-heart"></i> More AA-Team Products</a>';
			$html[] =   '</div>';
			$html[] = '</div>';

			return implode(PHP_EOL, $html);
		}

		public function session_start() {
            $session_id = isset($_COOKIE['PHPSESSID']) ? session_id($_COOKIE['PHPSESSID']) : ( isset($_REQUEST['PHPSESSID']) ? $_REQUEST['PHPSESSID'] : session_id() );
            if(!$session_id) {
                // session isn't started
                session_start();
            }
			//!isset($_SESSION['aateam_sess_dbg']) ? $_SESSION['aateam_sess_dbg'] = 0 : $_SESSION['aateam_sess_dbg']++;
			//var_dump('<pre>',$_SESSION['aateam_sess_dbg'],'</pre>');  			
		}
        public function session_close() {
            session_write_close(); // close the session
        }
		
		public function dismiss_notice()
		{
			update_option( $this->alias . "_dismiss_notice" , "true" );
			header( 'Location: ' . sprintf( admin_url('admin.php?page=%s'), $this->alias ) );
			die;
		}

		public function notifier_cache_interval() {
			return self::NOTIFIER_CACHE_INTERVAL;
		}
		
		public function plugin_row_meta($what='') {
			if ( !empty($what) && isset(self::$plugin_row_meta["$what"]) ) {
				return self::$plugin_row_meta["$what"];
			}
			return self::$plugin_row_meta;
		}


		/**
		 * Utils
		 */
		/*
		public function lang_init() 
		{ 
		    //load_plugin_textdomain( $this->alias, false, $this->cfg['paths']["plugin_dir_path"] . '/languages/');
		} 
		*/
		
		public function mark_content( $content ) 
		{
			return '<div id="psp-content-mark">' . $content . '</div>';
		}
		
		public function getPageContent( $post=null, $oldcontent='', $istax=false ) 
		{
			$optimizeSettings = $this->getAllSettings( 'array', 'on_page_optimization' );
			
			if ( !isset($optimizeSettings['parse_shortcodes']) 
				|| ( isset($optimizeSettings['parse_shortcodes']) && $optimizeSettings['parse_shortcodes'] != 'yes' ) ) {
				return $oldcontent;
			} 

			//if ( !is_singular() ) return false;
			if ( !$this->is_admin ) return $oldcontent;
			if ( is_null($post) || ( !is_object($post) && !is_array($post) ) ) return $oldcontent;
			if ( $istax ) {
				if ( is_object($post) && !isset($post->term_id) ) return $oldcontent;
				if ( is_array($post) && !isset($post['term_id']) ) return $oldcontent;
			} else {
				if ( is_object($post) && !isset($post->ID) ) return $oldcontent;
				if ( is_array($post) && !isset($post['ID']) ) return $oldcontent;
			}

			if ( $istax ) {
				//return $oldcontent; // unnecessary for taxonomy!
				if ( is_object($post) ) {
					$id = (int) $post->term_id;
				} else if ( is_array($post) ) {
					$id = (int) $post['term_id'];
					$post = (object) $post;
				}
				$url = get_term_link($post);
			} else {
				$id = isset($post) && is_object($post) ? (int) $post->ID : 0;
				$url = wp_get_shortlink($id);
			}
			//$url .= "&ispspreq=yes";

			/*$content = $this->remote_get( $url, 'default', array() );
			//$content = file_get_contents( $url );
			if ( !isset($content) || $content['status'] === 'invalid' ) return $oldcontent;
			$content = $content['body'];*/
			
			//var_dump('<pre>',$url,'</pre>'); die;  
			
			// check if will be redirected
			$headers = @get_headers( $url, 1 );
			if(isset($headers['Location'])) {
        		$url = $headers['Location']; // string
			}
			
			$resp = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 10,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array( 'ispspreq' => ( $istax ? 'tax' : 'post' ) ),
				'cookies' => array()
			));

			if ( is_wp_error( $resp ) ) { // If there's error
				//$err = htmlspecialchars( implode(';', $resp->get_error_messages()) );
				return $oldcontent;
			}
			$content = wp_remote_retrieve_body( $resp );
  
			//$pattern = "/\[pspmark\].*\[\/pspmark\]/imu";
			//$ret = preg_match($pattern, $content, $matches);
  
			// php query class
			require_once( $this->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
			if ( !empty($this->charset) )
				$doc = pspphpQuery::newDocument( $content, $this->charset );
			else
				$doc = pspphpQuery::newDocument( $content );
			
			$content = pspPQ('#psp-content-mark');
			$content = $content->html();
  
			return $content;
		}

		public function clean_header() 
		{

            remove_action('wp_head', 'feed_links_extra', 3); // This is the main code that removes unwanted RSS Feeds
            remove_action('wp_head', 'feed_links', 2); // Removes Post and Comment Feeds
            remove_action('wp_head', 'rsd_link'); // Removes link to RSD + XML
            remove_action('wp_head', 'wlwmanifest_link'); // Removes the link to Windows manifest
            remove_action('wp_head', 'index_rel_link'); // Removes the index link
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); // Remove relational links for the posts adjacent to the current post.
            remove_action('wp_head', 'wp_generator'); // Remove the XHTML generator link
            remove_action('wp_head', 'rel_canonical'); // Remov canonical url
            remove_action('wp_head', 'start_post_rel_link', 10, 0); // Remove start link
            remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Remove previous/next link
            remove_action('wp_head', 'locale_stylesheet'); // Remove local stylesheet from theme
		}

		public function clean_footer() 
		{
			echo ''; 
		}
		
		private function doPluginValidation( $file = '' ) 
		{
			$lines = file( $file );
			
			if ( $lines===false ) return false;
			if ( !is_array($lines) || count($lines) <=1 ) return false;

			if ( trim( $lines[7] ) != "! defined( 'ABSPATH' ) and exit;" ) return false;
			if ( trim( $lines[9] ) != 'if(class_exists(\'psp_Validation\') != true) {' ) return false;
			if ( trim( $lines[29] ) != 'add_action(\'wp_ajax_\' . ( self::ALIAS ) . \'TryActivate\', array( $this, \'aaTeamServerValidate\' ));' ) return false;
			if ( trim( $lines[78] ) != 'function isReg ( $hash )' ) return false;
			if ( trim ( $lines[38] ) != '$input = wp_remote_request( \'http://cc.aa-team.com/validation/validate.php?ipc=\' .' ) return false;
			return true;
		}

		public function admin_notice_install_styles()
		{
			if( !wp_style_is($this->alias . '-activation') ) {
				wp_enqueue_style( $this->alias . '-activation', $this->cfg['paths']['freamwork_dir_url'] . 'css/activation.css');
			}

			add_action( 'admin_notices', array( $this, 'admin_install_notice' ) );
		}

		public function admin_install_notice()
		{
		?>
		<div id="message" class="updated aaFrm-message_activate wc-connect">
			<div class="squeezer">
				<h4><?php _e( sprintf( '<strong>%s</strong> &#8211; You\'re almost ready :)', $this->pluginName ), $this->localizationName ); ?></h4>
				<p class="submit"><a href="<?php echo admin_url( 'admin.php?page=' . $this->alias ); ?>#setup_backup" class="button-primary"><?php _e( 'Install Default Config', $this->localizationName ); ?></a></p>
				
				<a href="<?php echo admin_url("admin.php?page=psp&disable_activation");?>" class="aaFrm-dismiss"><?php _e('Dismiss This Message', $this->localizationName); ?></a>
			</div>
		</div>
		<?php	
		}
		
		public function update_developer()
		{
		    return true;
			if ( in_array($_SERVER['REMOTE_ADDR'], array('86.124.69.217', '86.124.76.250')) ) {
				$this->dev = 'andrei';
			}
			else{
				$this->dev = 'gimi';
			}
		}


        /**
         * Some Plugin Status Info
         */
		public function plugin_redirect() {
			
			$req = array(
				'disable_activation'		=> isset($_REQUEST['disable_activation']) ? 1 : 0, 
				'page'						=> isset($_REQUEST['page']) ? (string) $_REQUEST['page'] : '',
			);
			extract($req);

			if ( $disable_activation && $this->alias == $page ) {
            	update_option( $this->alias . "_is_installed", true );
            	wp_redirect( get_admin_url() . 'admin.php?page=psp' );
            }

			if ( get_option('psp_do_activation_redirect', false) ) {
				delete_option('psp_do_activation_redirect');
				wp_redirect( get_admin_url() . 'admin.php?page=psp' );
			}
		}

		public function activate()
		{
			add_option('psp_do_activation_redirect', true);
			add_option('psp_depedencies_is_valid', true);
			add_option('psp_depedencies_do_activation_redirect', true);
			$this->plugin_integrity_check();
		}

        public function get_plugin_status ()
        {
			return $this->v->isReg( get_option($this->alias.'_hash') );
        }

        public function get_plugin_data()
        {
            $this->details = $this->pu->get_plugin_data();
            return $this->details;  
        }

		public function get_latest_plugin_version($interval) {
			return $this->pu->get_latest_plugin_version($interval);
		}


		// add admin js init
		public function createInstanceFreamwork ()
		{
			echo "
			<script type='text/javascript'>
				var psp = pspFacebookPage();
			</script>";
		}

		/**
		 * Create plugin init
		 *
		 *
		 * @no-return
		 */
		public function initThePlugin()
		{
		    $is_admin = $this->is_admin;
			$loadPluginData = false;

			// If the user can manage options, let the fun begin!
			$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : ''; 
			if ( $is_admin /*&& current_user_can( 'manage_options' )*/ ) {
				if ( (stripos($page,'codestyling') === false) ) {
					// Adds actions to hook in the required css and javascript
					add_action( "admin_print_styles", array( $this, 'admin_load_styles') );
					add_action( "admin_print_scripts", array( $this, 'admin_load_scripts') );
					
					// get fatal errors
					add_action ( 'admin_notices', array( $this, 'fatal_errors'), 10 );
	
					// get fatal errors
					add_action ( 'admin_notices', array( $this, 'admin_warnings'), 10 );
				}
				
				// create dashboard page
				add_action( 'admin_menu', array( $this, 'createDashboardPage' ) );
				
				$loadPluginData = true;
			} else if ( !$is_admin ) {
				$loadPluginData = true;
			}

			if ( $loadPluginData ) {
				// keep the plugin modules into storage
				$this->load_modules();

				// SEO rules class
				require_once( $this->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
			}
		}

		public function fixPlusParseStr ( $input=array(), $type='string', $sign='' )
		{
			$ret = '';
			if($type == 'array'){
				if(count($input) > 0){
					$ret_arr = array();
					foreach ($input as $key => $value){
						$ret = str_replace("###", '+', $value);
						if ('&' == $sign) {
							$ret = str_replace("#1#", '&', $value);
						}
						$ret_arr[$key] = $ret;
					}

					return $ret_arr;
				}

				return $input;
			}else{
				$ret = str_replace('+', '###', $input);
				if ('&' == $sign) {
					$ret = str_replace("%26", '#1#', $input);
				}
				return $ret;
			}
		}

		// saving the options
		public function save_options ()
		{
			// remove action from request
			unset($_REQUEST['action']);
  
			// unserialize the request options
			$serializedData = $_REQUEST['options'];

			$serializedData = $this->fixPlusParseStr($serializedData, 'string', '&');

			$serializedData = urldecode($serializedData);
			$serializedData = $this->fixPlusParseStr($serializedData, 'string');
    
			$savingOptionsArr = array();

			parse_str($serializedData, $savingOptionsArr);

			$savingOptionsArr = $this->fixPlusParseStr( $savingOptionsArr, 'array');
			$savingOptionsArr = $this->fixPlusParseStr( $savingOptionsArr, 'array', '&');
    
			// create save_id and remote the box_id from array
			$save_id = $savingOptionsArr['box_id'];
			unset($savingOptionsArr['box_id']);

			// Verify that correct nonce was used with time limit.
			if( ! wp_verify_nonce( $savingOptionsArr['box_nonce'], $save_id . '-nonce')) die ('Busted!');
			unset($savingOptionsArr['box_nonce']);

			// special cases! - local seo
			if ( $save_id == 'psp_local_seo' && isset($savingOptionsArr['slug']) ) {
				$savingOptionsArr['slug'] = sanitize_title( $savingOptionsArr['slug'] );
			}
			if ( $save_id == 'psp_socialsharing' /*&& isset($savingOptionsArr['toolbar']) && $savingOptionsArr['toolbar']!='none'*/ ) {
				$__old_saving = get_option('psp_socialsharing', true);
				$__old_saving = maybe_unserialize($__old_saving);
				$__old_saving = (array) $__old_saving;
    
				//foreach (array('floating', 'content_horizontal', 'content_vertical') as $k=>$v) {
					if ( isset($savingOptionsArr['toolbar']) ) {
					foreach (array('-pages', '-exclude-categ') as $kk=>$vv) {
						$__key =  $savingOptionsArr['toolbar'] . $vv;
						if ( !array_key_exists($__key, $savingOptionsArr) )
							$savingOptionsArr["$__key"] = $__old_saving["$__key"] = array();
					}
					}
				//}
				$savingOptionsArr = array_replace_recursive( $__old_saving, $savingOptionsArr );
			}
            if ( $save_id == 'psp_Minify' ) {
                $__old_saving = get_option('psp_Minify', true);
                $__old_saving = maybe_unserialize($__old_saving);
                $__old_saving = (array) $__old_saving;
                
				if ( isset($__old_saving["cache"]) ) {
                	$savingOptionsArr["cache"] = $__old_saving["cache"];
				}
            }
			
			// options NOT saved to db from options panel!
			$opt_nosave = isset($_REQUEST['opt_nosave']) ? (array) $_REQUEST['opt_nosave'] : array();
			if ( !empty($opt_nosave) ) {
				$__old_saving = get_option($save_id, true);
				$__old_saving = maybe_unserialize($__old_saving);
				$__old_saving = (array) $__old_saving;

				foreach ($opt_nosave as $kk=>$vv) {
					// unset( $savingOptionsArr["$vv"] );
					if ( isset($__old_saving["$vv"]) )
						$savingOptionsArr["$vv"] = $__old_saving["$vv"];
				}
			}

			//var_dump('<pre>', $savingOptionsArr, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			// prepare the data for DB update
			$savingOptionsArr = stripslashes_deep($savingOptionsArr);
     
			$saveIntoDb = serialize( $savingOptionsArr );

			// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
			update_option( $save_id, $saveIntoDb );

			die(json_encode( array(
				'status' => 'ok',
				'html' 	 => __('Options updated successfully', $this->localizationName)
			)));
		}
		
		public function save_theoption( $option_name, $option_value ) {
			$save_id = $option_name;

			// we receive unserialized option_value
			$savingOptionsArr = $option_value;
			$savingOptionsArr = $this->fixPlusParseStr( $savingOptionsArr, 'array');
			
			// prepare the data for DB update
			$savingOptionsArr = stripslashes_deep($savingOptionsArr);
			$saveIntoDb = serialize( $savingOptionsArr );

			// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
			update_option( $save_id, $saveIntoDb );
		}
		
		public function get_theoption( $option_name ) {
			$opt = get_option( $option_name);
			if ( $opt === false ) return false;
			$opt = maybe_unserialize($opt);
			return $opt;
		}

		// saving the options
		public function install_default_options ()
		{
			// remove action from request
			unset($_REQUEST['action']);

			// unserialize the request options
			$serializedData = urldecode($_REQUEST['options']);

			$savingOptionsArr = array();
			parse_str($serializedData, $savingOptionsArr);
			
			// fix for setup
			if ( $savingOptionsArr['box_id'] == 'psp_setup_box' ) {
				$serializedData = preg_replace('/box_id=psp_setup_box&box_nonce=[\w]*&install_box=/', '', $serializedData);
				$savingOptionsArr['install_box'] = $serializedData;
				$savingOptionsArr['install_box'] = str_replace( "\\'", "\\\\'", $savingOptionsArr['install_box']);
			}
  
			// create save_id and remove the box_id from array
			$save_id = $savingOptionsArr['box_id'];
			unset($savingOptionsArr['box_id']);

			// Verify that correct nonce was used with time limit.
			if( ! wp_verify_nonce( $savingOptionsArr['box_nonce'], $save_id . '-nonce')) die ('Busted!');
			unset($savingOptionsArr['box_nonce']);
			
			// default sql - tables & tables data!
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'modules/setup_backup/default-sql.php');
			if ( $save_id != 'psp_setup_box' ) {
				$savingOptionsArr['install_box'] = str_replace( '\"', '"', $savingOptionsArr['install_box']);
			}

			// convert to array
			$pullOutArray = json_decode( $savingOptionsArr['install_box'], true );
			if(count($pullOutArray) == 0){
				die(json_encode( array(
					'status' => 'error',
					'html' 	 => __("Invalid install default json string, can't parse it!", $this->localizationName)
				)));
			}else{
   
				foreach ($pullOutArray as $key => $value){

					// prepare the data for DB update
					$saveIntoDb = ( $value );
					
					if( $saveIntoDb === true ){
						$saveIntoDb = 'true';
					} else if( $saveIntoDb === false ){
						$saveIntoDb = 'false';
					}
					
					//special case - it's not double serialized!
					if ($key=='psp_taxonomy_seo') {
						$saveIntoDb = $value;
						continue 1;
					}

					// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
					update_option( $key, $saveIntoDb );
				}
				
				// update is_installed value to true 
				update_option( $this->alias . "_is_installed", 'true');

				die(json_encode( array(
					'status' => 'ok',
					'html' 	 => __('Install default successful', $this->localizationName)
				)));
			}
		}

		public function submatch ($sub_match) {
			return '\u00' . dechex(ord($sub_match[1]));
		}

		public function options_validate ( $input )
		{
			//var_dump('<pre>', $input  , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		}

		public function module_change_status ( $resp='ajax' )
		{
			// remove action from request
			unset($_REQUEST['action']);

			// update into DB the new status
			$db_alias = $this->alias . '_module_' . $_REQUEST['module'];
			update_option( $db_alias, $_REQUEST['the_status'] );
			
			if ( $_REQUEST['module'] == 'facebook_planner' ) {
				if ( $_REQUEST['the_status'] == 'true' ) {

					// @at plugin/module activation - setup cron
					//wp_schedule_event(time(), 'hourly', 'pspwplannerhourlyevent');
					//add_action('pspwplannerhourlyevent', array( $this, 'fb_wplanner_do_this_hourly' ));
				} else if ( $_REQUEST['the_status'] == 'false' ) {

					// @at plugin/module deactivation - clean the scheduler on plugin deactivation
					//wp_clear_scheduled_hook('pspwplannerhourlyevent');
				}
			}

			if ( !isset($resp) || empty($resp) || $resp == 'ajax' ) {
				die(json_encode(array(
					'status' => 'ok'
				)));
			}
		}
		
		public function module_bulk_change_status ()
		{
			global $wpdb; // this is how you get access to the database

			$request = array(
				'id' 			=> isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : ''
			);
 
			if (trim($request['id'])!='') {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (string) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}

			if (is_array($__rq2) && count($__rq2)>0) {
				foreach ($__rq2 as $kk=>$vv) {
					$_REQUEST['module'] = $vv;
					$this->module_change_status( 'non-ajax' );
				}
				
				die( json_encode(array(
					'status' => 'valid',
					'msg'	 => 'valid module change status Bulk'
				)) );
			}

			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => 'invalid module change status Bulk'
			)) );
		}

		// loading the requested section
		public function load_section ()
		{
			$request = array(
				'section' 		=> isset($_REQUEST['section']) ? strip_tags($_REQUEST['section']) : false,
				'subsection' 	=> isset($_REQUEST['subsection']) ? strip_tags($_REQUEST['subsection']) : false
			);

			// get module if isset
			if(!in_array( $request['section'], $this->cfg['activate_modules'])) die(json_encode(array('status' => 'err', 'msg' => __('invalid section want to load!', $this->localizationName))));

			$tryed_module = $this->cfg['modules'][$request['section']];
			if( isset($tryed_module) && count($tryed_module) > 0 ){
				if (1) {
					// Turn on output buffering
					ob_start();
	   
					$opt_file_path = $tryed_module['folder_path'] . 'options.php';
					if( is_file($opt_file_path) ) {
						// I believe there is a bug which load a module multiple times - for title & meta format module I needed to load options.php file multiple times 
						if ( 'title_meta_format' == $request['section'] ) {
							require( $opt_file_path  );
						} else {
							require_once( $opt_file_path  );
						}
					}
					$options = ob_get_contents(); //copy current buffer contents into $message variable and delete current output buffer
					ob_end_clean();
				}
				  
				if(trim($options) != "") {
					$options = json_decode($options, true);
					 
					// Derive the current path and load up aaInterfaceTemplates
					$plugin_path = dirname(__FILE__) . '/';
					if(class_exists('aaInterfaceTemplates') != true) {
						require_once($plugin_path . 'settings-template.class.php');

						// Initalize the your aaInterfaceTemplates
						$aaInterfaceTemplates = new aaInterfaceTemplates($this->cfg);

						// then build the html, and return it as string
						$html = $aaInterfaceTemplates->bildThePage($options, $this->alias, $tryed_module);
						 
						// fix some URI
						$html = str_replace('{plugin_folder_uri}', $tryed_module['folder_uri'], $html);

						if(trim($html) != "") {
							$headline = $tryed_module[$request['section']]['menu']['title'] . "<span class='psp-section-info'>" . ( $tryed_module[$request['section']]['description'] ) . "</span>";
							
							$has_help = isset($tryed_module[$request['section']]['help']) ? true : false;
							if( $has_help === true ){
								
								$help_type = isset($tryed_module[$request['section']]['help']['type']) && $tryed_module[$request['section']]['help']['type'] ? 'remote' : 'local';
								if( $help_type == 'remote' ){
									if ( is_array($tryed_module[$request['section']]['help']['url']) ) {
										if ( !empty($request['subsection']) )
											$docRemoteUrl = $tryed_module[$request['section']]['help']['url']["{$request['subsection']}"];
										else {
											reset( $tryed_module[$request['section']]['help']['url'] );
											$firstElem = key( $tryed_module[$request['section']]['help']['url'] );
											$docRemoteUrl = $tryed_module[$request['section']]['help']['url']["$firstElem"];
										}
									} else {
										$docRemoteUrl = $tryed_module[$request['section']]['help']['url'];
									} 
									$headline .= '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $docRemoteUrl ) . '">HELP</a>';
								} 
							}
   
							die( json_encode(array(
								'status' 	=> 'ok',
								'headline'	=> $headline,
								'html'		=> 	$html
							)) );
						}

						die(json_encode(array('status' => 'err', 'msg' => 'invalid html formatter!')));
					}
				}
			}
		}

		public function fatal_errors()
		{
			// print errors
			if(is_wp_error( $this->errors )) {
				$_errors = $this->errors->get_error_messages('fatal');

				if(count($_errors) > 0){
					foreach ($_errors as $key => $value){
						echo '<div class="error"> <p>' . ( $value ) . '</p> </div>';
					}
				}
			}
		}

		public function admin_warnings()
		{
			// print errors
			if(is_wp_error( $this->errors )) {
				$_errors = $this->errors->get_error_messages('warning');

				if(count($_errors) > 0){
					foreach ($_errors as $key => $value){
						echo '<div class="updated"> <p>' . ( $value ) . '</p> </div>';
					}
				}
			}
		}

		/**
		 * Builds the config parameters
		 *
		 * @param string $function
		 * @param array	$params
		 *
		 * @return array
		 */
		protected function buildConfigParams($type, array $params)
		{
			// check if array exist
			if(isset($this->cfg[$type])){
				$params = array_merge( $this->cfg[$type], $params );
			}

			// now merge the arrays
			$this->cfg = array_merge(
				$this->cfg,
				array(	$type => array_merge( $params ) )
			);
		}

		/*
		* admin_load_styles()
		*
		* Loads admin-facing CSS
		*/
		public function admin_get_frm_style() {
			$css = array();

			if( isset($this->cfg['freamwork-css-files'])
				&& is_array($this->cfg['freamwork-css-files'])
				&& !empty($this->cfg['freamwork-css-files'])
			) {
				foreach ($this->cfg['freamwork-css-files'] as $key => $value){
					if( is_file($this->cfg['paths']['freamwork_dir_path'] . $value) ) {
						
						$cssId = $this->alias . '-' . $key;
						$css["$cssId"] = $this->cfg['paths']['freamwork_dir_path'] . $value;
						// wp_enqueue_style( $this->alias . '-' . $key, $this->cfg['paths']['freamwork_dir_url'] . $value );
					} else {
						$this->errors->add( 'warning', __('Invalid CSS path to file: <strong>' . $this->cfg['paths']['freamwork_dir_path'] . $value . '</strong>. Call in:' . __FILE__ . ":" . __LINE__ , $this->localizationName) );
					}
				}
			}
			return $css;
		}
		public function admin_load_styles()
		{
			global $wp_scripts;
			$protocol = is_ssl() ? 'https' : 'http';

			$javascript = $this->admin_get_scripts();
			
			wp_enqueue_style( $this->alias . '-google-Roboto',  $protocol . '://fonts.googleapis.com/css?family=Roboto:400,500,400italic,500italic,700,700italic' );
			wp_enqueue_style( $this->alias . '-font-awesome',   $protocol . '://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css' );
			wp_enqueue_style( $this->alias . '-admin-font',   $this->cfg['paths']['freamwork_dir_url'] . 'css/font.css' );

			$main_style = admin_url('admin-ajax.php?action=PSP_framework_style');
            $main_style_cached = $this->cfg['paths']['freamwork_dir_path'] . 'main-style.css';
            if( is_file( $main_style_cached ) ) {
                if( (filemtime($main_style_cached) + $this->ss['css_cache_time']) > time() ) {  
                    $main_style = $this->cfg['paths']['freamwork_dir_url'] . 'main-style.css';
                }
            }

            // !!! debug - please in the future, don't forget to comment it after you're finished with debugging
			//$main_style = admin_url('admin-ajax.php?action=PSP_framework_style');
            
            wp_enqueue_style( $this->alias . '-main-style', $main_style, array( $this->alias . '-font-awesome' ) );

            /*$style_url = $this->cfg['paths']['freamwork_dir_url'] . 'load-styles.php';
            if ( is_file( $this->cfg['paths']['freamwork_dir_path'] . 'load-styles.css' ) ) {
                $style_url = str_replace('.php', '.css', $style_url);
            }
			wp_enqueue_style( 'psp-aa-framework-styles', $style_url );*/
			
			if( in_array( 'jquery-ui-core', $javascript ) ) {
				$ui = $wp_scripts->query('jquery-ui-core');
				if ($ui) {
					$uiBase = "//code.jquery.com/ui/{$ui->ver}/themes/smoothness";
					wp_register_style('jquery-ui-core', "$uiBase/jquery-ui.css", FALSE, $ui->ver);
					wp_enqueue_style('jquery-ui-core');
				}
			}
			if( in_array( 'thickbox', $javascript ) ) wp_enqueue_style('thickbox');
		}

		/*
		* admin_load_scripts()
		*
		* Loads admin-facing CSS
		*/
		public function admin_get_scripts() {
			$javascript = array();
			
			$current_url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
			$current_url = explode("wp-admin/", $current_url);
			if( count($current_url) > 1 ){ 
				$current_url = "/wp-admin/" . $current_url[1];
			}else{
				$current_url = "/wp-admin/" . $current_url[0];
			}
			
			if ( isset($this->cfg['modules'])
				&& is_array($this->cfg['modules']) && !empty($this->cfg['modules'])
			) {
			foreach( $this->cfg['modules'] as $alias => $module ){

				if( isset($module[$alias]["load_in"]['backend']) && is_array($module[$alias]["load_in"]['backend']) && count($module[$alias]["load_in"]['backend']) > 0 ){
					// search into module for current module base on request uri
					foreach ( $module[$alias]["load_in"]['backend'] as $page ) {
  
						$delimiterFound = strpos($page, '#');
						$page = substr($page, 0, ($delimiterFound!==false && $delimiterFound > 0 ? $delimiterFound : strlen($page)) );
						$urlfound = preg_match("%^/wp-admin/".preg_quote($page)."%", $current_url);
						if(
							// $current_url == '/wp-admin/' . $page
							( ( $page == '@all' ) || ( $current_url == '/wp-admin/admin.php?page=psp' ) || ( !empty($page) && $urlfound > 0 ) )
							&& isset($module[$alias]['javascript']) ) {
  
							$javascript = array_merge($javascript, $module[$alias]['javascript']);
						}
					}
				}
			}
			} // end if

			$this->jsFiles = $javascript;
			return $javascript;
		}
		public function admin_load_scripts()
		{
			// very defaults scripts (in wordpress defaults)
			wp_enqueue_script( 'jquery' );
			
			$javascript = $this->admin_get_scripts();
			
			if( count($javascript) > 0 ){
				$javascript = @array_unique( $javascript );
  
				if( in_array( 'jquery-ui-core', $javascript ) ) wp_enqueue_script( 'jquery-ui-core' );
				if( in_array( 'jquery-ui-widget', $javascript ) ) wp_enqueue_script( 'jquery-ui-widget' );
				if( in_array( 'jquery-ui-mouse', $javascript ) ) wp_enqueue_script( 'jquery-ui-mouse' );
				if( in_array( 'jquery-ui-accordion', $javascript ) ) wp_enqueue_script( 'jquery-ui-accordion' );
				if( in_array( 'jquery-ui-autocomplete', $javascript ) ) wp_enqueue_script( 'jquery-ui-autocomplete' );
				if( in_array( 'jquery-ui-slider', $javascript ) ) wp_enqueue_script( 'jquery-ui-slider' );
				if( in_array( 'jquery-ui-tabs', $javascript ) ) wp_enqueue_script( 'jquery-ui-tabs' );
				if( in_array( 'jquery-ui-sortable', $javascript ) ) wp_enqueue_script( 'jquery-ui-sortable' );
				if( in_array( 'jquery-ui-draggable', $javascript ) ) wp_enqueue_script( 'jquery-ui-draggable' );
				if( in_array( 'jquery-ui-droppable', $javascript ) ) wp_enqueue_script( 'jquery-ui-droppable' );
				if( in_array( 'jquery-ui-datepicker', $javascript ) ) wp_enqueue_script( 'jquery-ui-datepicker' );
				if( in_array( 'jquery-ui-resize', $javascript ) ) wp_enqueue_script( 'jquery-ui-resize' );
				if( in_array( 'jquery-ui-dialog', $javascript ) ) wp_enqueue_script( 'jquery-ui-dialog' );
				if( in_array( 'jquery-ui-button', $javascript ) ) wp_enqueue_script( 'jquery-ui-button' );
				
				if( in_array( 'thickbox', $javascript ) ) wp_enqueue_script( 'thickbox' );
	
				// date & time picker
				if( !wp_script_is('jquery-timepicker') ) {
					if( in_array( 'jquery-timepicker', $javascript ) ) wp_enqueue_script( 'jquery-timepicker' , $this->cfg['paths']['freamwork_dir_url'] . 'js/jquery.timepicker.v1.1.1.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
				}
				
				// star rating - rateit
				if( !wp_script_is('jquery-rateit-js') ) {
					if( in_array( 'jquery-rateit-js', $javascript ) ) {
						
						if( !wp_style_is('jquery-rateit-css') )
							wp_enqueue_style( 'jquery-rateit-css' , $this->cfg['paths']['freamwork_dir_url'] . 'js/rateit/rateit.css' );
						wp_enqueue_script( 'jquery-rateit-js' , 	$this->cfg['paths']['freamwork_dir_url'] . 'js/rateit/jquery.rateit.min.js', array( 'jquery' ) );
					}
				}
			}

			if( count($this->cfg['freamwork-js-files']) > 0 ){
				foreach ($this->cfg['freamwork-js-files'] as $key => $value){

					if( is_file($this->cfg['paths']['freamwork_dir_path'] . $value) ){
						if( in_array( $key, $javascript ) ) wp_enqueue_script( $this->alias . '-' . $key, $this->cfg['paths']['freamwork_dir_url'] . $value . '?' . time() );
					} else {
						$this->errors->add( 'warning', __('Invalid JS path to file: <strong>' . $this->cfg['paths']['freamwork_dir_path'] . $value . '</strong> . Call in:' . __FILE__ . ":" . __LINE__ , $this->localizationName) );
					}
				}
			}
		}

		/*
		 * Builds out the options panel.
		 *
		 * If we were using the Settings API as it was likely intended we would use
		 * do_settings_sections here. But as we don't want the settings wrapped in a table,
		 * we'll call our own custom wplanner_fields. See options-interface.php
		 * for specifics on how each individual field is generated.
		 *
		 * Nonces are provided using the settings_fields()
		 *
		 * @param array $params
		 * @param array $options (fields)
		 *
		 */
		public function createDashboardPage ()
		{
			if ( $this->capabilities_user_has_module('dashboard') ) {
			//if( $psp->can_manage('view_seo_dashboard') ){
				add_menu_page(
					__( 'Premium SEO Pack - Dashboard', $this->localizationName ),
					__( 'Premium SEO', $this->localizationName ),
					'read',
					$this->alias,
					array( $this, 'manage_options_template' ),
					$this->cfg['paths']['plugin_dir_url'] . 'icon_16.png'
				);
			//}
			}
		}

		public function display_index_page()
		{
			echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		}

		public function manage_options_template()
		{
			// Derive the current path and load up aaInterfaceTemplates
			$plugin_path = dirname(__FILE__) . '/';
			if(class_exists('aaInterfaceTemplates') != true) {
				require_once($plugin_path . 'settings-template.class.php');

				// Initalize the your aaInterfaceTemplates
				$aaInterfaceTemplates = new aaInterfaceTemplates($this->cfg);

				// try to init the interface
				$aaInterfaceTemplates->printBaseInterface();
			}
		}

		/**
		 * Getter function, plugin config
		 *
		 * @return array
		 */
		public function getCfg()
		{
			return $this->cfg;
		}

		/**
		 * Getter function, plugin all settings
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getAllSettings( $returnType='array', $only_box='' )
		{
			$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "options where 1=1 and option_name REGEXP '" . ( $this->alias) . "_([a-z_]*)$';"; // ORDER BY option_name asc
			if (trim($only_box) != "") {
				$allSettingsQuery = "SELECT option_value, option_name FROM " . $this->db->prefix . "options where option_name = '" . ( $this->alias . '_' . $only_box) . "'";
			}
  
			$results = $this->db->get_results( $allSettingsQuery, ARRAY_A);
  
			// prepare the return
			$return = array();
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					
					//special case - it's not double serialized!
					if ($value['option_name']=='psp_taxonomy_seo') {
						$return[$value['option_name']] = @unserialize($value['option_value']);
						continue 1;
					}
					
					if($value['option_value'] == 'true'){
						$return[$value['option_name']] = true;
					}else{
						//$return[$value['option_name']] = @unserialize(@unserialize($value['option_value']));
						$return[$value['option_name']] = maybe_unserialize($value['option_value']);
						$return[$value['option_name']] = maybe_unserialize($return[$value['option_name']]);
					}
				}
			}

			if(trim($only_box) != "" && isset($return[$this->alias . '_' . $only_box])){
				$return = $return[$this->alias . '_' . $only_box];
			}
   
			if($returnType == 'serialize'){
				return serialize($return);

			}else if( $returnType == 'array' ){
				return $return;
			}else if( $returnType == 'json' ){
				return json_encode($return);
			}

			return false;
		}

		/**
		 * Getter function, all products
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getAllProductsMeta( $returnType='array', $key='' )
		{
			$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "postmeta where 1=1 and meta_key='" . ( $key ) . "'";
			$results = $this->db->get_results( $allSettingsQuery, ARRAY_A);
			// prepare the return
			$return = array();
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					if(trim($value['meta_value']) != ""){
						$return[] = $value['meta_value'];
					}
				}
			}

			if($returnType == 'serialize'){
				return serialize($return);
			}
			else if( $returnType == 'text' ){
				return implode("\n", $return);
			}
			else if( $returnType == 'array' ){
				return $return;
			}
			else if( $returnType == 'json' ){
				return json_encode($return);
			}

			return false;
		}

		/*
		* GET modules lists
		*/
		public function load_modules( $pluginPage='' )
		{
			$folder_path = $this->cfg['paths']['plugin_dir_path'] . 'modules/';
			$cfgFileName = 'config.php';
			
			// static usage, modules menu order
			$menu_order = array();
			
			$modules_list = glob($folder_path . '*/' . $cfgFileName);
			$nb_modules = count($modules_list);
			if ( $nb_modules > 0 ) {
				foreach ($modules_list as $key => $mod_path ) {

					$dashboard_isfound = preg_match("/modules\/dashboard\/config\.php$/", $mod_path);
					$depedencies_isfound = preg_match("/modules\/depedencies\/config\.php$/", $mod_path);
					
					if ( $pluginPage == 'depedencies' ) {
						if ( $depedencies_isfound!==false && $depedencies_isfound>0 ) ;
						else continue 1;
					} else {
						if ( $dashboard_isfound!==false && $dashboard_isfound>0 ) {
							unset($modules_list[$key]);
							$modules_list[$nb_modules] = $mod_path;
						}
					}
				}
			}
  
			foreach($modules_list as $module_config ){
				$module_folder = str_replace($cfgFileName, '', $module_config);
  
				// Turn on output buffering
				ob_start();

				if( is_file( $module_config ) ) {
					require_once( $module_config  );
				}
				$settings = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer
				
				if(trim($settings) != "") {
					
					//var_dump('<pre>',$settings,'</pre>');  
					$settings = json_decode($settings, true);
					$__settings = array_keys($settings); // e-strict solve!
					$alias = (string)end($__settings);

					// create the module folder URI
					// fix for windows server
					$module_folder = str_replace( DIRECTORY_SEPARATOR, '/',  $module_folder );

					$__tmpUrlSplit = explode("/", $module_folder);
					$__tmpUrl = '';
					$nrChunk = count($__tmpUrlSplit);
					if($nrChunk > 0) {
						foreach ($__tmpUrlSplit as $key => $value){
							if( $key > ( $nrChunk - 4) && trim($value) != ""){
								$__tmpUrl .= $value . "/";
							}
						}
					}

					// get the module status. Check if it's activate or not
					$status = false;

					// default activate all core modules
					if ( $pluginPage == 'depedencies' ) {
						if ( $alias != 'depedencies' ) continue 1;
						else $status = true;
					} else {
						if ( $alias == 'depedencies' ) continue 1;
						
						if(in_array( $alias, $this->cfg['core-modules'] )) {
							$status = true;
						}else{
							// activate the modules from DB status
							$db_alias = $this->alias . '_module_' . $alias;
	
							if(get_option($db_alias) == 'true'){
								$status = true;
							}
						}
					}
					
					// push to modules array
					$this->cfg['modules'][$alias] = array_merge(array(
						'folder_path' 	=> $module_folder,
						'folder_uri' 	=> $this->cfg['paths']['plugin_dir_url'] . $__tmpUrl,
						'db_alias'		=> $this->alias . '_' . $alias,
						'alias' 		=> $alias,
						'status'		=> $status
					), $settings );

					// add to menu order array http://cc.aa-team.com/wp-plugins/smart-seo-v2/wp-admin/admin-ajax.php?action=pspLoadSection&section=Social_Stats
					if(!isset($this->cfg['menu_order'][(int)$settings[$alias]['menu']['order']])){
						$this->cfg['menu_order'][(int)$settings[$alias]['menu']['order']] = $alias;
					}else{
						// add the menu to next free key
						$this->cfg['menu_order'][] = $alias;
					}

					// add module to activate modules array
					if($status == true){
						$this->cfg['activate_modules'][$alias] = true;
					}

					// load the init of current loop module
					$time_start = microtime(true);
					$start_memory_usage = (memory_get_usage());
					
					// in backend
					if( $this->is_admin === true && isset($settings[$alias]["load_in"]['backend']) ){
						
						$need_to_load = false;
						if( is_array($settings[$alias]["load_in"]['backend']) && count($settings[$alias]["load_in"]['backend']) > 0 ){
						
							$current_url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
							$current_url = explode("wp-admin/", $current_url);
							if( count($current_url) > 1 ){ 
								$current_url = "/wp-admin/" . $current_url[1];
							}else{
								$current_url = "/wp-admin/" . $current_url[0];
							}
							foreach ( $settings[$alias]["load_in"]['backend'] as $page ) {

								$delimiterFound = strpos($page, '#');
								$page = substr($page, 0, ($delimiterFound!==false && $delimiterFound > 0 ? $delimiterFound : strlen($page)) );
								$urlfound = preg_match("%^/wp-admin/".preg_quote($page)."%", $current_url);
								
								$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
								$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
								if(
									// $current_url == '/wp-admin/' . $page ||
									( ( $page == '@all' ) || ( $current_url == '/wp-admin/admin.php?page=psp' ) || ( !empty($page) && $urlfound > 0 ) )
									|| ( $action == 'pspLoadSection' && $section == $alias )
									|| substr($action, 0, 3) == 'psp'
								){
									$need_to_load = true;  
								}
							}
						}
  
						if( $need_to_load == false ){
							continue;
						}  
					}
					
					if( $this->is_admin === false && isset($settings[$alias]["load_in"]['frontend']) ){
						
						$need_to_load = false;
						if( $settings[$alias]["load_in"]['frontend'] === true ){
							$need_to_load = true;
						}
						if( $need_to_load == false ){
							continue;
						}  
					}  
					
					if( $status == true && isset( $settings[$alias]['module_init'] ) ){
						if( is_file($module_folder . $settings[$alias]['module_init']) ){
							//if( $this->is_admin ) {
								$current_module = array($alias => $this->cfg['modules'][$alias]); 
								require_once( $module_folder . $settings[$alias]['module_init'] );
								
								$time_end = microtime(true);
								$this->cfg['modules'][$alias]['loaded_in'] = $time_end - $time_start;
								
								$this->cfg['modules'][$alias]['memory_usage'] = (memory_get_usage() ) - $start_memory_usage;
								if( (float)$this->cfg['modules'][$alias]['memory_usage'] < 0 ){
									 
									$this->cfg['modules'][$alias]['memory_usage'] = 0.0;
								}
							//}
						}
					}
				}
			}

			// order menu_order ascendent
			ksort($this->cfg['menu_order']);
		}

		public function print_psp_usages()
		{
			$html = array();
			
			$html[] = '<style>
				.psp-bench-log {
					border: 1px solid #ccc; 
					width: 450px; 
					position: absolute; 
					top: 92px; 
					right: 2%;
					background: #95a5a6;
					color: #fff;
					font-size: 12px;
					z-index: 99999;
					
				}
					.psp-bench-log th {
						font-weight: bold;
						background: #34495e;
					}
					.psp-bench-log th,
					.psp-bench-log td {
						padding: 4px 12px;
					}
				.psp-bench-title {
					position: absolute; 
					top: 55px; 
					right: 2%;
					width: 425px; 
					margin: 0px 0px 0px 0px;
					font-size: 20px;
					background: #ec5e00;
					color: #fff;
					display: block;
					padding: 7px 12px;
					line-height: 24px;
					z-index: 99999;
				}
			</style>';
			
			$html[] = '<h1 class="psp-bench-title">PSP: Benchmark performance</h1>';
			$html[] = '<table class="psp-bench-log">';
			$html[] = 	'<thead>';
			$html[] = 		'<tr>';
			$html[] = 			'<th>Module</th>';
			$html[] = 			'<th>Loading time</th>';
			$html[] = 			'<th>Memory usage</th>';
			$html[] = 		'</tr>';
			$html[] = 	'</thead>';
			
			
			$html[] = 	'<tbody>';
			
			$total_time = 0;
			$total_size = 0;
			foreach ($this->cfg['modules'] as $key => $module ) {

				$html[] = 		'<tr>';
				$html[] = 			'<td>' . ( $key ) . '</td>';
				$html[] = 			'<td>' . ( number_format($module['loaded_in'], 4) ) . '(seconds)</td>';
				$html[] = 			'<td>' . (  $this->formatBytes($module['memory_usage']) ) . '</td>';
				$html[] = 		'</tr>';
			
				$total_time = $total_time + $module['loaded_in']; 
				$total_size = $total_size + $module['memory_usage']; 
			}

			$html[] = 		'<tr>';
			$html[] = 			'<td colspan="3">';
			$html[] = 				'Total time: <strong>' . ( $total_time ) . '(seconds)</strong><br />';			
			$html[] = 				'Total Memory: <strong>' . ( $this->formatBytes($total_size) ) . '</strong><br />';			
			$html[] = 			'</td>';
			$html[] = 		'</tr>';

			$html[] = 	'</tbody>';
			$html[] = '</table>';
			
			//echo '<script>jQuery("body").append(\'' . ( implode("\n", $html ) ) . '\')</script>';
			echo implode("\n", $html );
		}

		public function check_secure_connection ()
		{

			$secure_connection = false;
			if(isset($_SERVER['HTTPS']))
			{
				if ($_SERVER["HTTPS"] == "on")
				{
					$secure_connection = true;
				}
			}
			return $secure_connection;
		}


		/*
			helper function, image_resize
			// use timthumb
		*/
		public function image_resize ($src='', $w=100, $h=100, $zc=2)
		{
			// in no image source send, return no image
			if( trim($src) == "" ){
				$src = $this->cfg['paths']['freamwork_dir_url'] . '/images/no-product-img.jpg';
			}

			if( is_file($this->cfg['paths']['plugin_dir_path'] . 'timthumb.php') ) {
				return $this->cfg['paths']['plugin_dir_url'] . 'timthumb.php?src=' . $src . '&w=' . $w . '&h=' . $h . '&zc=' . $zc;
			}
		}

		/*
			helper function, upload_file
		*/
		public function upload_file ()
		{
			$slider_options = '';
			 // Acts as the name
            $clickedID = $_POST['clickedID'];
            // Upload
            if ($_POST['type'] == 'upload') {
                $override['action'] = 'wp_handle_upload';
                $override['test_form'] = false;
				$filename = $_FILES [$clickedID];

                $uploaded_file = wp_handle_upload($filename, $override);
                if (!empty($uploaded_file['error'])) {
                    echo json_encode(array("error" => "Upload Error: " . $uploaded_file['error']));
                } else {
                		
                    die( json_encode(array(
							"url" => $uploaded_file['url'],
							"thumb" => $this->image_resize( $uploaded_file['url'], $_POST['thumb_w'], $_POST['thumb_h'], $_POST['thumb_zc'] )
						)
					) );
                } // Is the Response
            }else{
				echo json_encode(array("error" => "Invalid action send" ));
			}

            die();
		}
		
		public function wp_media_upload_image()
		{
			$image = wp_get_attachment_image_src( (int)$_REQUEST['att_id'], 'thumbnail' );
			die(json_encode(array(
				'status' 	=> 'valid',
				'thumb'		=> $image[0]
			)));
		}

		/**
		 * Getter function, shop config
		 *
		 * @params $returnType
		 * @return array
		 */
		public function setConfig( $section='', $key='' ) {
            if( !is_array($this->app_settings) || empty($this->app_settings) ){
                $this->app_settings = $this->getAllSettings();
            }
		}
		public function getConfig( $section='', $key='', $returnAs='echo' )
		{
		    $this->setConfig( $section, $key );
			if( isset($this->app_settings[$this->alias . "_" . $section])) {
				if( isset($this->app_settings[$this->alias . "_" . $section][$key])) {
					if( $returnAs == 'echo' ) echo $this->app_settings[$this->alias . "_" . $section][$key];

					if( $returnAs == 'return' ) return $this->app_settings[$this->alias . "_" . $section][$key];
				}
			}
		}

		public function download_image( $file_url='', $pid=0, $action='insert' )
		{
			if(trim($file_url) != ""){

				// Find Upload dir path
				$uploads = wp_upload_dir();
				$uploads_path = $uploads['path'] . '';
				$uploads_url = $uploads['url'];

				$fileExt = end(explode(".", $file_url));
				$filename = uniqid() . "." . $fileExt;

				// Save image in uploads folder
				$response = wp_remote_get( $file_url );

				if( !is_wp_error( $response ) ){
					$image = $response['body'];
					file_put_contents( $uploads_path . '/' . $filename, $image );

					$image_url = $uploads_url . '/' . $filename; // URL of the image on the disk
					$image_path = $uploads_path . '/' . $filename; // Path of the image on the disk

					// Add image in the media library - Step 3
					$wp_filetype = wp_check_filetype( basename( $image_path ), null );
					$attachment = array(
					   'post_mime_type' => $wp_filetype['type'],
					   'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_path ) ),
					   'post_content'   => '',
					   'post_status'    => 'inherit'
					);

					$attach_id = wp_insert_attachment( $attachment, $image_path, $pid  );
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $image_path );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					return array(
						'attach_id' => $attach_id,
						'image_path' => $image_path
					);
				}
			}
		}

		public function remove_gallery($content) {
		    return str_replace('[gallery]', '', $content);
		}

		public function pspW3CValidate()
		{
			require_once( $this->cfg['modules']['W3C_HTMLValidator']['folder_path'] . 'app.class.php' );
			$pspW3C_HTMLValidator = new pspW3C_HTMLValidator($this->cfg, $module);
			$pspW3C_HTMLValidator->validateLink();
		}

		/**
	    * HTML escape given string
	    *
	    * @param string $text
	    * @return string
	    */
	    public function escape($text)
	    {
	        $text = (string) $text;
	        if ('' === $text) return '';

	        $result = @htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
	        if (empty($result)) {
	            $result = @htmlspecialchars(utf8_encode($text), ENT_COMPAT, 'UTF-8');
	        }

	        return $result;
	    }
		
		public function get_page_meta( $url='' )
		{
			$data = array();
			
			if( trim($url) != "" ){
				// try to get page meta 
				$response = wp_remote_get( $url, array( 'timeout' => 15 ) ); 
            
	            // If there's error
	            if ( is_wp_error( $response ) )
	                return $data;
            
            	$html_data = wp_remote_retrieve_body( $response );
				if( trim($html_data) != "" ){
					require_once( $this->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
					if ( !empty($this->charset) )
						$doc = pspphpQuery::newDocument( $html_data, $this->charset );
					else
						$doc = pspphpQuery::newDocument( $html_data );
					
					// try to get the page title
					$data['page_title'] = $doc->find('title')->text();
					
					// try to get the page meta description
					$data['page_meta_description'] = $doc->find('meta[name="description"]')->attr('content');
					
					// try to get the page meta keywords
					$data['page_meta_keywords'] = $doc->find('meta[name="keywords"]')->attr('content');
				}
				
				return $data;
			}
		}
		
		public function verify_module_status( $module='' ) {
			if ( empty($module) ) return false;

			$mod_active = get_option( 'psp_module_'.$module );
			if ( $mod_active != 'true' )
				return false; //module is inactive!
			return true;
		}
		
		public function edit_post_inline_data( $post_id, $seo=null, $tax=false, $post_content='empty' ) {
  
			if ( $this->__tax_istax( $tax ) ) { //taxonomy data!

				$post = $tax;

				$post_id = (int) $post->term_id;
				if( $post_content == 'empty' ){
					$post_content = $this->getPageContent( $post, $post->description, true );
				}
				$post_title = $post->name;
				
				$psp_current_taxseo = $this->__tax_get_post_meta( null, $post );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$post_metas = $this->__tax_get_post_meta( $psp_current_taxseo, $post, 'psp_meta' );
			} else {

				// global $post;
				$post = get_post($post_id);
				if ( isset($post) && is_object($post) ) {
					$post_id = (int) $post->ID;
					if( $post_content == 'empty' ){
						$post_content = $this->getPageContent( $post, $post->post_content );
					}
					$post_title = $post->post_title;
				} else {
					$post_id = 0;
					$post_content = '';
					$post_title = '';
				}
				$post_metas = get_post_meta( $post_id, 'psp_meta', true );
			}
			//$post = get_post( $post_id, ARRAY_A);
			//$post_metas = get_post_meta( $post_id, 'psp_meta', true);
			//$post_title = $post['post_title'];
			//$post_content = $post['post_content'];
			//$post_content = $this->getPageContent( $post, $post['post_content'] );
			
			$post_metas = array_merge(array(
				'title'			=> '',
				'description'		=> '',
				'keywords'		=> '',
				'focus_keyword'	=> '',
				'canonical'		=> '',
				'robots_index'	=> '',
				'robots_follow'	=> ''
			), (array) $post_metas);

			if ( is_null($seo) || !is_object($seo) ) {
				//use to generate meta keywords, and description for your requested item
				require_once( $this->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
				$seo = pspSeoCheck::getInstance();
			}

  
			// meta description
			$first_ph = $seo->get_first_paragraph( $post_content );
			$gen_meta_desc = $seo->gen_meta_desc( $first_ph );

			// meta keywords
			$gen_meta_keywords = array();
			//if ( !empty($post_metas['focus_keyword']) )
			//	$gen_meta_keywords[] = $post_metas['focus_keyword'];
			// focus keyword add to keywords is implemented in js file!
			$__tmp = $seo->gen_meta_keywords( $post_content );
			if ( !empty($__tmp) )
				$gen_meta_keywords[] = $__tmp;
			$gen_meta_keywords = implode(', ', $gen_meta_keywords);
			
			$post_metas['robots_index'] = isset($post_metas['robots_index']) && !empty($post_metas['robots_index'])
				? $post_metas['robots_index'] : 'default' ;
			$post_metas['robots_follow'] = isset($post_metas['robots_follow']) && !empty($post_metas['robots_follow'])
				? $post_metas['robots_follow'] : 'default';

			$postDefault = $this->get_post_metatags( $post ); // add meta placeholder

			$html = array();
			$html[] = '<div class="psp-post-title">' . $post_title . '</div>';
			$html[] = '<div class="psp-post-gen-desc">' . $gen_meta_desc . '</div>';
			$html[] = '<div class="psp-post-gen-keywords">' . $gen_meta_keywords . '</div>';
			$html[] = '<div class="psp-post-meta-title">' . $post_metas['title'] . '</div>';
			$html[] = '<div class="psp-post-meta-description">' . $post_metas['description'] . '</div>';
			$html[] = '<div class="psp-post-meta-keywords">' . $post_metas['keywords'] . '</div>';
			$html[] = '<div class="psp-post-meta-focus-kw">' . $post_metas['focus_keyword'] . '</div>';
			$html[] = '<div class="psp-post-meta-canonical">' . $post_metas['canonical'] . '</div>';
			$html[] = '<div class="psp-post-meta-robots-index">' . $post_metas['robots_index'] . '</div>';
			$html[] = '<div class="psp-post-meta-robots-follow">' . $post_metas['robots_follow'] . '</div>';
			
			if ( ! empty($postDefault) ) {
				foreach ( $postDefault as $key => $val) {
					$html[] = '<div class="psp-post-default-' . $key . '">' . $val . '</div>';
				}
			}

			return implode(PHP_EOL, $html);
		}
		
		public function edit_post_inline_boxtpl() {
			/*
					<div>
						<span>Focus Keyword: </span>
						<input type="text" class="large-text" style="width: 300px;" value="" name="psp-editpost-meta-focus-kw" id="psp-editpost-meta-focus-kw">
					</div>
			*/
			$html = '
	<table class="psp-inline-edit-post form-table" style="border: 1px solid #dadada;">
		<thead>
			<tr>
				<th width="45%"><strong>PSP Quick SEO Edit</strong></th>
				<th width="30%">' . __('Meta Description', $this->localizationName) . '</th>
				<th width="25%">' . __('Meta Keywords', $this->localizationName) . '</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="45%">
					<div>
						<span>' . __('Meta Title:', $this->localizationName) . '</span>
						<input type="text" class="" style="" value="" name="psp-editpost-meta-title" id="psp-editpost-meta-title">
					</div>
					<div>
						<span>' . __('Canonical URL:', $this->localizationName) . '</span>
						<input type="text" class="" style="" value="" name="psp-editpost-meta-canonical" id="psp-editpost-meta-canonical">
					</div>
					<div>
						<span>' . __('Meta Robots Index:', $this->localizationName) . '</span>
						<select name="psp-editpost-meta-robots-index" id="psp-editpost-meta-robots-index">
							<option value="default" selected="true">' . __('Default Setting', $this->localizationName) . '</option>
							<option value="index">' . __('Index', $this->localizationName) . '</option>
							<option value="noindex">' . __('NO Index', $this->localizationName) . '</option>
						</select>
					</div>
					<div>
						<span>' . __('Meta Robots Follow:', $this->localizationName) . '</span>
						<select name="psp-editpost-meta-robots-follow" id="psp-editpost-meta-robots-follow">
							<option value="default" selected="true">Default Setting</option>
							<option value="follow">Follow</option>
							<option value="nofollow">NO Follow</option>
						</select>
					</div>
				</td>
				<td>
					<textarea name="psp-editpost-meta-description" id="psp-editpost-meta-description" rows="3" class="large-text"></textarea>
				</td>
				<td>
					<textarea name="psp-editpost-meta-keywords" id="psp-editpost-meta-keywords" rows="3" class="large-text"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan=3>
					<div style="float:left; width:100%;">
						<input type="button" value="' . __('Cancel', $this->localizationName) . '" id="psp-inline-btn-cancel" class="psp-button gray" style="float:left;">
						<input type="button" value="' . __('Save', $this->localizationName) . '" id="psp-inline-btn-save" class="psp-button blue psp-form-button-small psp-form-button-info" style="float:right;">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
			';
			return $html;
		}
		
	    /**
	     * Taxonomy meta box methods!
	     */
	    
	    // wp get_post_meta - for taxonomy 
	    public function __tax_get_post_meta( $post_meta=null, $post=null, $key='' ) {
	    	if ( !$this->__tax_istax( $post ) )
	    		return null;

			$psp_taxonomy_seo = $post_meta;
	    	if ( is_null($post_meta) ) {
	    		$psp_taxonomy_seo = get_option( 'psp_taxonomy_seo' );
	    		if ( $psp_taxonomy_seo===false )
	    			return null;
	    	}
	    	if ( is_null($psp_taxonomy_seo) )
	    		return null;
	    	if ( empty($psp_taxonomy_seo) )
				return null;

			if ( is_null($post_meta) ) {
				if ( isset($psp_taxonomy_seo[ "{$post->taxonomy}" ],
					$psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ]) )
					$psp_current_taxseo = $psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ];
				else return null;
			}
			else
				$psp_current_taxseo = $post_meta;

    		if ( !isset($psp_current_taxseo) || !is_array($psp_current_taxseo) )
	    			return null;

	    	if ( $key=='' )
	    		return $psp_current_taxseo;

	    	if ( isset($psp_current_taxseo[ "$key" ]) )
	    		return $psp_current_taxseo[ "$key" ];
	    	return null;
	    }

	    // wp update_post_meta - for taxonomy 
	    public function __tax_update_post_meta( $post=null, $keyval=array() ) {
	    	if ( !$this->__tax_istax( $post ) )
	    		return false;
	    		
	    	$psp_taxonomy_seo = get_option( 'psp_taxonomy_seo' );
	    	if ( $psp_taxonomy_seo===false )
	    		$psp_taxonomy_seo = array();
	    		
			if ( !is_array($keyval) || empty($keyval) ) // mandatory array of (key, value) pairs!
				return false;

	    	if ( empty($psp_taxonomy_seo) ) {
				$psp_taxonomy_seo = array();
			}

			$psp_current_taxseo = array();
			if ( isset($psp_taxonomy_seo[ "{$post->taxonomy}" ], $psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ]) ) {
	    		$psp_current_taxseo = $psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ];
			}

    		if ( !is_array($psp_current_taxseo) )
    			$psp_current_taxseo = array();

			foreach ( $keyval as $key => $value ) {
				if ( isset($psp_current_taxseo[" $key "]) )
					unset( $psp_current_taxseo[" $key "] );
				$psp_current_taxseo[ "$key" ] = $value;
			}

			$psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ] = $psp_current_taxseo;				
			update_option( 'psp_taxonomy_seo', $psp_taxonomy_seo );
	    }

	    // wp get_post - for taxonomy 
	    public function __tax_get_post( $post=null, $output='OBJECT', $filter='raw' ) {
			if ( !$this->__tax_istax( $post ) )
	    		return null;

			//$__post = get_term_by( 'id', $post->term_id, $post->taxonomy, $output, $filter );
			$__post = get_term( $post->term_id, $post->taxonomy, $output, $filter );
			return $__post!==false ? $__post : null;
	    }
	    
	    // verify a taxonomy is used!
	    public function __tax_istax( $post=null ) {
			$__istax = false; // default is post | page | custom post type edit page!
			if ( is_object($post) && count((array) $post)>=2
				&& isset($post->term_id) && isset($post->taxonomy)
				&& $post->term_id > 0 && !empty($post->taxonomy) )
				$__istax = true; // is category | tag | custom taxonomy edit page!
			return $__istax;
	    }
	    
	    
		/**
	     * remote_get - alternative to wp_remote_get by proxy!
	     */
		// return one random of the most common user agents
		public function fakeUserAgent()
		{
			$userAgents = array(
				'Mozilla/5.0 (Windows; U; Win95; it; rv:1.8.1) Gecko/20061010 Firefox/2.0',
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-HK; rv:1.8.1.7) Gecko Firefox/2.0',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15',
				'Mozilla/5.0 (Windows; U; Windows NT 6.1; es-AR; rv:1.9) Gecko/2008051206 Firefox/3.0',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6 ; nl; rv:1.9) Gecko/2008051206 Firefox/3.0',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; es-AR; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11',
				'Mozilla/5.0 (X11; U; Linux x86_64; cy; rv:1.9.1b3) Gecko/20090327 Fedora/3.1-0.11.beta3.fc11 Firefox/3.1b3',
				'Mozilla/5.0 (Windows; U; Windows NT 6.1; ja; rv:1.9.2a1pre) Gecko/20090403 Firefox/3.6a1pre',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; Win64; x64; SV1)',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; .NET CLR 1.1.4322)',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 1.1.4322; InfoPath.2; .NET CLR 3.5.21022)',
				'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET CLR 1.1.4322; Tablet PC 2.0; OfficeLiveConnector.1.3; OfficeLivePatch.1.3; MS-RTC LM 8; InfoPath.3)',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; FDM; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 1.1.4322)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC; en) Opera 9.00',
				'Mozilla/5.0 (X11; Linux i686; U; en) Opera 9.00',
				'Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC; en) Opera 9.00',
				'Opera/9.00 (Nintindo Wii; U; ; 103858; Wii Shop Channel/1.0; en)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.0; pt-br) Opera 9.25',
				'Opera/9.50 (Macintosh; Intel Mac OS X; U; en)',
				'Opera/9.61 (Windows NT 6.1; U; zh-cn) Presto/2.1.1',
				'Mozilla/5.0 (Windows NT 5.0; U; en-GB; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 9.61',
				'Opera/10.00 (X11; Linux i686; U; en) Presto/2.2.0',
				'Mozilla/5.0 (Macintosh; PPC Mac OS X; U; en; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 10.00',
				'Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686 ; en) Opera 10.00',
				'Opera/9.80 (Windows NT 6.0; U; fi) Presto/2.2.0 Version/10.00',
				'Mozilla/5.0 (Windows; U; Windows NT 6.1; da) AppleWebKit/522.15.5 (KHTML, like Gecko) Version/3.0.3 Safari/522.15.5',
				'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_4_11; ar) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.1 Safari/525.18',
				'Mozilla/5.0 (Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0_1 like Mac OS X; hu-hu) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5G77 Safari/525.20',
				'Mozilla/5.0 (iPod; U; CPU iPhone OS 2_2_1 like Mac OS X; es-es) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5H11 Safari/525.20',
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; he-IL) AppleWebKit/528.16 (KHTML, like Gecko) Version/4.0 Safari/528.16',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_1; zh-CN) AppleWebKit/530.19.2 (KHTML, like Gecko) Version/4.0.2 Safari/530.19'
			);
			
			// rondomize user agents
			shuffle( $userAgents );
			return $userAgents[0];
		}
	
		// requestType : default | proxy | noproxy
		public function remote_get( $url, $requestType='default', $headers=array() ) { 
			$ret = array(
				'status'	=> 'invalid',
				'body'		=> '',
				'msg'		=> ''
			);

			$err = '';

			if ( $requestType == 'default' ) {

				if ( isset($headers) && !empty($headers) )
					$resp = wp_remote_get( $url, $headers );
				else
					$resp = wp_remote_get( $url );

				if ( is_wp_error( $resp ) ) { // If there's error
					$body = false;
					$err = htmlspecialchars( implode(';', $resp->get_error_messages()) );
				}
				else {
					$body = wp_remote_retrieve_body( $resp );
				}
				//$body = file_get_contents( $url );
				
			}
			else if ( $requestType == 'noproxy' ) { // no Proxy!

				$args = array(
					'user-agent' => $this->fakeUserAgent(),
					'timeout' => 20
				);
				if ( isset($headers) && !empty($headers) ) {
					$args = array_merge($args, $headers);
				}
				$resp = wp_remote_get( $url, $args );
				if ( is_wp_error( $resp ) ) { // If there's error
					$body = false;
					$err = htmlspecialchars( implode(';', $resp->get_error_messages()) );
				}
				else {
					$body = wp_remote_retrieve_body( $resp );
				}

			}

			if (is_null($body) || !$body || trim($body)=='') { //status is Invalid!
				$ret = array_merge($ret, array(
					'msg'		=> trim($err) != '' ? $err : 'empty body response retrieved!'
				)); //couldn't retrive data!
				return $ret;
			}
			$ret = array_merge($ret, array( //status is valid!
				'status'	=> 'valid',
				'body'		=> $body
			));
			return $ret;
		}
		
		// smushit
		public function smushit_show_sizes_msg_details( $meta=array(), $show_sizes=true ) {

			$ret = array();
            
            // get only selected sizes!
            $selected_sizes = $this->smushit_tinify_option('image_sizes');
			//if ( !isset($meta['psp_smushit']) || empty($meta['psp_smushit']) ) return $ret;

            // original file should be smushed
            if ( in_array('__original', $selected_sizes) ) {
                $ret[] = $meta['psp_smushit']['msg'];
            }

			if ( !$show_sizes )
			return $ret;

			// no media sizes
			if ( !isset($meta['sizes']) || empty($meta['sizes']) )
			return $ret;

			foreach ( $meta['sizes'] as $key => $val ) {
                // current size should be smushed
                if ( !in_array($key, $selected_sizes) ) continue 1;

				$ret[] = $val['psp_smushit']['msg'];
			}
			return $ret;
		}
        public function smushit_tinify_option($opt, $settings=array()) {
            if (empty($settings)) {
                $settings = (array) $this->get_theoption( 'psp_tiny_compress' );
            }

            $ret = null;
            if (!empty($settings) && is_array($settings) && isset($settings["$opt"])) {
                $ret = $settings["$opt"];
            }

            if ( $opt == 'image_sizes' ) {
                $ret = array_merge( array('__original' => '__original'), (array) $ret );
            }
            return $ret;
        }
        
		// rich snippets
		public function loadRichSnippets( $section='init' ) {

			if ( !in_array($section, array('init', 'options')) ) return false;

			$folder_path = $this->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/shortcodes/';

			if ( $section=='options') {
				$cfgFileName = 'options.php';
				$retOpt = array();
			}
			else if ( $section=='init') {
				$cfgFileName = 'init.php';
			}

			foreach(glob($folder_path . '*/' . $cfgFileName) as $module_config ){
				$module_folder = str_replace($cfgFileName, '', $module_config);

				if ( $section=='init') {

					if( $this->verifyFileExists( $module_config ) ) {
						require_once( $module_config  );
					}
				} else if ( $section=='options') {

					if( $this->verifyFileExists( $module_config ) ) {
						// Turn on output buffering
						ob_start();

						require( $module_config  );

						$options = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer

						if(trim($options) != "") {
							$options = json_decode($options, true);

							if ( is_array($options) && !empty($options) > 0 ) {
								$retOpt = array_merge( $retOpt, $options[0] );
							}
						}
					}
				}
			} // end foreach!

			if ( $section=='options')
				return array( $retOpt );
			else if ( $section=='init')
				return true;
		}

		public function generateRandomString( $length = 10 ) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}

		// Cron - facebook post planner
		public function fb_wplanner_do_this_hourly() {
			// Plugin cron class loading
			require_once ( $this->cfg['paths']['plugin_dir_path'] . 'modules/facebook_planner/app.cron.class.php' );
		}
		
		/**
		 * User Roles - Capabilities
		 */
		public function capabilities_current_user_role() {
			// current user role
			$current_user = wp_get_current_user();
			$roles = $current_user->roles;
			$user_role = array_shift($roles);
			return $user_role;
		}
		public function capabilities_user_has_module( $module='' ) {
			$user_role = $this->capabilities_current_user_role();
			
			// super admin or admin => has Full access to modules!
			if ( in_array($user_role, array('super_admin', 'administrator')) ) {
				return true;
			}
			
			// verify user has module!
			$capabilitiesRoles = $this->get_theoption('psp_capabilities_roles');
			if ( is_null($capabilitiesRoles) || !$capabilitiesRoles ) { // no capabilities for any user role defined!
				return true;
			}
			if ( isset($capabilitiesRoles["$user_role"]) && !is_null($capabilitiesRoles["$user_role"]) && is_array($capabilitiesRoles["$user_role"]) ) {
				$userModules = $capabilitiesRoles["$user_role"];
				$module = strtolower($module);
				$userModules = array_map('strtolower', $userModules);
				if ( in_array($module, $userModules) ) return true;
			}
			return false;
		}
		
		/**
		 * Cron Jobs - clean fix
		 */
		private function cronjobs_clean_fix() {
			$alreadyCleaned = get_option('psp_cronjobs_clean');
			
			$doit = false;
			if ( !isset($alreadyCleaned) || is_null($alreadyCleaned) || $alreadyCleaned===false || $alreadyCleaned!='done') {
				$doit = true;
			}
			
			// clean cronjobs
			if ( $doit ) {
				$this->cronjobs_clear_all_crons('pspwplannerhourlyevent');
				$this->cronjobs_clear_all_crons('psp_wplanner_hourly_event');
				$this->cronjobs_clear_all_crons('psp_start_cron_serp_check');
				
				update_option( 'psp_cronjobs_clean', 'done' );
			}
		}
		public function cronjobs_clear_all_crons( $hook ) {
			$crons = _get_cron_array();
			if ( empty( $crons ) ) {
				return;
			}
			foreach( $crons as $timestamp => $cron ) {
				if ( !empty( $cron[$hook] ) )  {
					unset( $crons[$timestamp][$hook] );
				}
				if ( empty($crons[$timestamp]) ) {
					unset($crons[$timestamp]);
				}
			}
			_set_cron_array( $crons );
		}
		
		/**
		 * Backlink builder - links list fix
		 */
		private function fix_backlinkbuilder_linklist() {
			$alreadyCleaned = get_option('psp_fix_backlinkbuilder');
			
			$doit = false;
			if ( !isset($alreadyCleaned) || is_null($alreadyCleaned) || $alreadyCleaned===false || $alreadyCleaned!='done') {
				$doit = true;
			}

			// clean cronjobs
			if ( $doit ) {
				global $wpdb;
				
				// delete record
				$table_name = $wpdb->prefix . "psp_web_directories";
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in ('278');";
				$__stat = $wpdb->query($query_delete);
				if ($__stat!== false) {
				}

				update_option( 'psp_fix_backlinkbuilder', 'done' );
			}
		}
		
		private function setIniConfiguration() {
			if ( ($memory_limit = ini_get('memory_limit')) !== false ) {
				if ( (int) $memory_limit < 256) {
					ini_set('memory_limit', '512M');
				}
			}
		}
		
		
		/**
		 * Social Sharing
		 */
		public function admin_notice_details() {
			$isPremium = false;
			if ( $this->is_plugin_active( 'psp' ) ) {
				$__moduleIsActive = get_option('psp_module_Social_Stats');
				$__submoduleSocialShare = get_option('psp_socialsharing');
				if ( isset($__moduleIsActive) && $__moduleIsActive=='true'
				&& isset($__submoduleSocialShare) && $__submoduleSocialShare!==false )
					$isPremium = true;
			}
			
			if ( !$isPremium ) return false;

			if( !wp_style_is($this->alias . '-activation') ) {
				wp_enqueue_style( $this->alias . '-activation', $this->cfg['paths']['freamwork_dir_url'] . 'css/activation.css');
			}

			add_action( 'admin_notices', array( $this, 'admin_notice_text' ) );
		}

		public function admin_notice_text()
		{
		?>
		<div id="message" class="updated aaFrm-message_activate wc-connect">
			<div class="squeezer">
				<h4><?php _e( 'AA Social Share notice: you already use Premium SEO Pack - Social Stats module, Social Sharing section', $this->localizationName ); ?></h4>
			</div>
		</div>
		<?php	
		}
		
		
		/**
		 * Usefull
		 */
		
		//format right (for db insertion) php range function!
		public function doRange( $arr ) {
			$newarr = array();
			if ( is_array($arr) && count($arr)>0 ) {
				foreach ($arr as $k => $v) {
					$newarr[ $v ] = $v;
				}
			}
			return $newarr;
		}

		//verify if file exists!
		public function verifyFileExists($file, $type='file') {
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
		
		// Return current Unix timestamp with microseconds
 		// Simple function to replicate PHP 5 behaviour
		public function microtime_float()
		{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}

		public function formatBytes($bytes, $precision = 2) {
			$units = array('B', 'KB', 'MB', 'GB', 'TB');

			$bytes = max($bytes, 0);
			$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
			$pow = min($pow, count($units) - 1);

			// Uncomment one of the following alternatives
			// $bytes /= pow(1024, $pow);
			$bytes /= (1 << (10 * $pow));

			return round($bytes, $precision) . ' ' . $units[$pow];
		}

		public function prepareForInList($v) {
			return "'".$v."'";
		}
		public function prepareForDbClean($v) {
			return trim($v);
		}
		
		public function print_module_error( $module=array(), $error_number, $title="" )
		{
			$html = array();
			if( count($module) == 0 ) return true;
  
			$html[] = '<div class="psp-grid_4 psp-error-using-module psp-message psp-error">';
			$html[] = 	'<div class="psp-panel">';
			$html[] = 		'<div class="psp-panel-header">';
			$html[] = 			'<span class="psp-panel-title">';
			$html[] = 				__( $title, $this->localizationName );
			$html[] = 			'</span>';
			$html[] = 		'</div>';
			$html[] = 		'<div class="psp-panel-content">';
			
			$error_msg = isset($module[$module['alias']]['errors'][$error_number]) ? $module[$module['alias']]['errors'][$error_number] : '';
			
			$html[] = 			'<div class="psp-error-details">' . ( $error_msg ) . '</div>';
			$html[] = 		'</div>';
			$html[] = 	'</div>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
		public function convert_to_button( $button_params=array() )
		{
			$button = array();
			$button[] = '<a';
			if(isset($button_params['url'])) 
				$button[] = ' href="' . ( $button_params['url'] ) . '"';
			
			if(isset($button_params['target'])) 
				$button[] = ' target="' . ( $button_params['target'] ) . '"';
			
			$button[] = ' class="psp-button';
			
			if(isset($button_params['color'])) 
				$button[] = ' ' . ( $button_params['color'] ) . '';
				
			$button[] = '"';
			$button[] = '>';
			
			$button[] =  $button_params['title'];
		
			$button[] = '</a>';
			
			return implode("", $button);
		}


		/**
		 * Various
		 */ 
		public function get_wp_type() {
			global $blog_id;

			$wp_type = 'default';
			if( defined( 'SITE_ID_CURRENT_SITE' ) ) {
				if ( $blog_id != SITE_ID_CURRENT_SITE ) {
					$wp_type = 'multi';
				}
			}
			return apply_filters( 'psp_wp_type', $wp_type );
		}

        public function get_wp_pagetype() {
        	
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


        	$page_type = array(
        		'type' => ''
			);

			// loop through all page types!
			if ( is_admin() ) {
				$page_type['type'] = 'admin';
			}
			else if ( is_feed() ) {
				$page_type['type'] = 'feed';
			}
            else if ( is_search() ) {
				$page_type['type'] = 'search';
            }
   			else if ( is_home() || is_front_page() ) {
            	$page_type['type'] = 'home';
            }
			// start is_singular() : is_singular tag is equivalent to ( is_page || is_attachment || is_single )
			else if ( is_singular() ) {
	            if ( is_single() ) {
	            	// it will check posts and any of your custom post types other then pages or attachments
	            	$page_type['type'] = 'post';
	            }
	            else if ( is_page() ) {
	            	$page_type['type'] = 'page';
	            }
	            else if ( is_attachment() ) {
					// treated like a page!
	            	$page_type['type'] = 'page';
	            }
				else {
					// default case
	            	$page_type['type'] = 'post';
	            }
				
				// custom post type
				if ( ( 'post' == $page_type['type'] ) && ! empty($post_type) && ! in_array($post_type, array('post', 'page', 'attachment')) ) {
					$page_type['type'] = 'posttype';
				}
			}
			// end is_singular()
            else if ( is_category() ) {
				$page_type['type'] = 'category';
            }
            else if ( is_tag() ) {
            	$page_type['type'] = 'tag';
            }
            else if ( is_tax() ) {
            	$page_type['type'] = 'taxonomy';
            }
            else if ( is_author() ) {
            	// is_author must be called is_archive, because they are both for archives pages 
            	$page_type['type'] = 'author';
            }
            else if ( is_archive() ) {
            	$page_type['type'] = 'archive';
            }
            else if ( is_404() ) { 
				$page_type['type'] = '404';
            }

			// woocommerce
			if ( $this->is_shop() ) {
				$page_type['type'] = 'page'; // archive
			}

			$page_type = $page_type['type'];
			return apply_filters( 'premiumseo_seo_pagetype', $page_type );
        }

		public function get_wp_list_pagetypes() {

			$arr = array('home', 'post', 'page', 'posttype', 'category', 'tag', 'taxonomy', 'archive', 'author', 'search', '404');
			return apply_filters( 'premiumseo_seo_list_pagetypes', $arr );
		}
        
		/**
		 * is woocommerce shop page?
		 * Products / Display / Shop & Product Page / "Shop Page" option
		 * wp_options / get_option('woocommerce_shop_page_id') 
		 */
		public function is_shop() {
			//if ( class_exists('Woocommerce') ) {
			if ( function_exists('is_woocommerce') && function_exists('is_product') ) {
        		if ( is_shop() ) {
					return true;
				}
			}
			return false;
		}

        public function get_wp_user_roles( $translate = false ) {
            global $wp_roles;
            if ( !isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }
            
            $roles = $wp_roles->get_names();
            
            if ( $translate ) {
                foreach ($roles as $k => $v) {
                    $roles[$k] = __($v, 'psp');
                }
                asort($roles);
                // translation to be implemented!
                return $roles;
            } else {
                //$roles = array_keys($roles);
                foreach ($roles as $k => $v) {
                    $roles[$k] = ucfirst($k);
                }
                asort($roles);
                return $roles;
            }
        }


		/**
		 * Buddy Press
		 */
		public function is_buddypress() {
			if ( !defined( 'BP_VERSION' ) ){
				return false;
			}

	        global $bp;
	        if( $bp->maintenance_mode == 'install' ){
	            if( $_GET['page'] == 'psp' ){
	                $this->errors = __( 'The Buddypress installation it\'s not finished!', $this->localizationName );
	                add_action( 'admin_notices', array($this, 'bp_warning_box'), 1 );
	            }
	            return false;
	        }else{
	            return true;
	        }
		}
		public function is_buddypress_section() {
			if ( !$this->is_buddypress() ) return false;

			global $bp;
			$current_component = bp_current_component();
			$current_action = bp_current_action();

			$ret = array(
				'component' 	=> '',
				'action'		=> ''
			);
			if ( !empty($current_component) ) {
				
				$ret['component'] = $current_component;
				if ( !empty($current_action) ) {
					$ret['action'] = $current_action;
				}
				return $ret;
			}
			return false;
		}

		public function bp_warning_box(){
		?>
		<div class="updated"> <p><?php _e( '<strong>Premium SEO Pack</strong> | ', $this->localizationName ); ?><?php echo $this->errors; ?></p> </div>
        <?php
    	}
		
		// current page is : the site static front page
		public function _is_static_front_page() {
			return ( is_front_page() && 'page' == get_option( 'show_on_front' ) && is_page( get_option( 'page_on_front' ) ) );
		}
		
		// current page is : the blog posts index page as homepage and shows posts
		public function _is_home_blog_posts_page() {
			return ( is_home() && 'page' != get_option( 'show_on_front' ) );
		}

		// current page is : the blog posts index page and it's not homepage
		public function _is_blog_posts_page() {
			return ( is_home() && 'page' == get_option( 'show_on_front' ) );
		}
		
		public function get_php_ini_bool($value) {
			$value = (string) $value;
			$value = strtolower($value);
			return in_array($value, array('+', '1', 'y', 'on', 'yes', 'true', 'enabled')) ?
				true : in_array($value, array('-', '0', 'n', 'off', 'no', 'false', 'disabled')) ?
					false : (boolean) $value;
		}
	
        
        /**
         * cURL / Send http requests with curl
         */
        public static function curl($url, $input_params=array(), $output_params=array(), $debug=false) {
            $ret = array('status' => 'invalid', 'http_code' => 0, 'data' => '');

            // build curl options
            $ipms = array_replace_recursive(array(
                'userpwd'                   => false,
                'htaccess'                  => false,
                'post'                      => false,
                'postfields'                => array(),
                'httpheader'				=> false,
                'verbose'                   => false,
                'ssl_verifypeer'            => false,
                'ssl_verifyhost'            => false,
                'httpauth'                  => false,
                'failonerror'               => false,
                'returntransfer'            => true,
                'binarytransfer'            => false,
                'header'                    => false,
                'cainfo'                    => false,
                'useragent'                 => false,
            ), $input_params);
            extract($ipms);
            
            $opms = array_replace_recursive(array(
                'resp_is_json'              => false,
                'resp_add_http_code'        => false,
                'parse_headers'             => false,
            ), $output_params);
            extract($opms);
            
            //var_dump('<pre>', $ipms, $opms, '</pre>'); die('debug...'); 

            // begin curl
            $url = trim($url);
            if (empty($url)) return (object) $ret;
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            
            if ( !empty($userpwd) ) {
                curl_setopt($curl, CURLOPT_USERPWD, $userpwd);
            }
            if ( !empty($htaccess) ) {
                $url = preg_replace( "/http(|s):\/\//i", "http://" . $htaccess . "@", $url );
            }
            if (!$post && !empty($postfields)) {
                $url = $url . "?" . http_build_query($postfields);
            }

            if ($post) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
            }
			
			if ( !empty($httpheader) ) {
				curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
			}
            
            curl_setopt($curl, CURLOPT_VERBOSE, $verbose);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_verifypeer);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl_verifyhost);
            if ( $httpauth!== false ) curl_setopt($curl, CURLOPT_HTTPAUTH, $httpauth);
            curl_setopt($curl, CURLOPT_FAILONERROR, $failonerror);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $returntransfer);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, $binarytransfer);
            curl_setopt($curl, CURLOPT_HEADER, $header);
            if ( $cainfo!== false ) curl_setopt($curl, CURLOPT_CAINFO, $cainfo);
            if ( $useragent!== false ) curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
            if ( $timeout!== false ) curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            
            $data = curl_exec($curl);
            $http_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            $ret = array_merge($ret, array('http_code' => $http_code));
            if ($debug) {
                $ret = array_merge($ret, array('debug_details' => curl_getinfo($curl)));
            }
            if ( $data === false || curl_errno($curl) ) { // error occurred
                $ret = array_merge($ret, array(
                    'data' => curl_errno($curl) . ' : ' . curl_error($curl)
                ));
            } else { // success
            
                if ( $parse_headers ) {
                    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                    $headers = self::__parse_headers( substr($data, 0, $header_size) ); // response begin with the headers
                    $data = substr($data, $header_size);
                    $ret = array_merge($ret, array('headers' => $headers));
                }
        
                // Add the status code to the json data, useful for error-checking
                if ( $resp_add_http_code && $resp_is_json ) {
                    $data = preg_replace('/^{/', '{"http_code":'.$http_code.',', $data);
                }
                
                $ret = array_merge($ret, array(
                    'status'    => 'valid',
                    'data'       => $data
                ));
            }

            curl_close($curl);
            return $ret;
        }
        private static function __parse_headers($headers) {
            if (!is_array($headers)) {
                $headers = explode("\r\n", $headers);
            }
            $ret = array();
            foreach ($headers as $header) {
                $header = explode(":", $header, 2);
                if (count($header) == 2) {
                    $ret[$header[0]] = trim($header[1]);
                }
            }
            return $ret;
        }

        // php.net/manual/en/function.urlencode.php
        // urlencode function and rawurlencode are mostly based on RFC 1738. however, since 2005 the current RFC in use for URIs standard is RFC 3986.
        public function urlencode($string) {
            $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
            $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
            return str_replace($entities, $replacements, urlencode($string));
        }

        
        /**
         * Utils
         */
        public function is_gzip( $setting=false, $force=array()) {
            $ret = true;
            if ( $setting!==false ) {
                $ret = (string) $setting == 'yes' ? true : false;
            }
  
            // do gzip only if everything it's fine
            if(
                !$ret // compressing not activated yet
                || empty($_SERVER['HTTP_ACCEPT_ENCODING']) // no encoding support
                || ( // no gzip
                    strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === false
                    && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'x-gzip') === false
                )
                || !function_exists("gzwrite") // no PHP gzip support
                || headers_sent() // headers already sent
                || ( ( !isset($force['ob_get_level']) || (isset($force['ob_get_level']) && $force['ob_get_level']) ) && ob_get_contents() ) // already some output...
                || in_array('ob_gzhandler', ob_list_handlers()) // other plugins (or PHP) is already using gzipp
                || $this->get_php_ini_bool(ini_get("zlib.output_compression")) // zlib compression in php.ini enabled
                || ( ( !isset($force['ob_get_level']) || (isset($force['ob_get_level']) && $force['ob_get_level']) )
                     && ( ob_get_level() > ( !$this->get_php_ini_bool(ini_get("output_buffering")) ? 0 : 1 ) ) ) // another output buffer  is already active, beside the default one*/
            ) {
                $ret = false;
            }
            return $ret;
        }
    
    
        /**
         * Client Utils
         */
        public function get_client_ip() {
            $ipaddress = '';

            if ($_SERVER['REMOTE_ADDR'])
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else if ($_SERVER['HTTP_CLIENT_IP'])
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if ($_SERVER['HTTP_X_FORWARDED'])
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if ($_SERVER['HTTP_FORWARDED_FOR'])
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if( $_SERVER['HTTP_FORWARDED'])
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

            return $ipaddress;
        }

        public function get_current_page_url() {
            $url = (!empty($_SERVER['HTTPS']))
                ?
                "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']
                :
                "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']
            ;
            return $url;
        }
        
        public function get_client_country() {
            $get_user_location = wp_remote_get( 'http://api.hostip.info/get_json.php?ip=' . $this->get_client_ip() );

            if ( is_wp_error( $get_user_location ) ) { // If there's error
                $body = false;
                // $err = htmlspecialchars( implode(';', $get_user_location->get_error_messages()) );
            } else {
                $body = wp_remote_retrieve_body( $get_user_location );
            }
            if (is_null($body) || !$body || trim($body)=='') { //status is Invalid!
                return false;
            } else {
                $body = json_decode($body);
                $user_country = array(
                    'name'      => $body->country_name,
                    'code'      => $body->country_code
                );
            }

            return $user_country;
        }
        
        public function get_client_utils() {
            $utils = array();

            $client_ip = $this->get_client_ip();
            $current_url = $this->get_current_page_url();
            // $current_date = $_SERVER['REQUEST_TIME']; // Available since PHP 5.1.0
            $current_date = strtotime( date('Y-m-d H:i') );
            //$get_current_country = $this->get_client_country();
            //if ( !empty($get_current_country) && isset($get_current_country['name']) ) {
            //    $current_country = $get_current_country['name'];
            //    $current_country_code = $get_current_country['code'];
            //} else {
            //    $current_country = '';
            //    $current_country_code = '';
            //}
            $current_country = '';
            $current_country_code = '';

            $utils = compact('client_ip', 'current_url', 'current_date', 'current_country', 'current_country_code');
  
            // mobile
            require_once( $this->cfg['paths']["scripts_dir_path"] . '/mobile-detect/Mobile_Detect.php' );
            $mobileDetect = new aaMobile_Detect();

            $utils['isMobile'] = $mobileDetect->isMobile();
            $utils['device_type'] = $mobileDetect->type();
            if ( $utils['device_type'] == 'tablet') $utils['device_type'] = 'mobile';
            
            return $utils;
        }
    
		/**
		 * pms = array(
		 * 		status		: (string) success | error
		 * 		step		: (string)
		 * 		msg			: (string)
		 * )
		 */
		public function facebook_planner_last_status( $pms=array() ) {
			extract($pms);

			$fb_details = $this->getAllSettings('array', 'facebook_planner');
		
			$msg_ = $msg;
			if ( is_object($msg) ) {
				$msg_ = (array) $msg;
			}
			if ( is_array($msg) ) {
				$msg_ = serialize( $msg );
			}
			
			//$msg_ = substr($msg_, 0, 150);
			$msg_ = serialize( $msg_ );	
			$last_status = array('last_status' => array('status' => $status, 'step' => $step, 'data' => date("Y-m-d H:i:s"), 'msg' => $msg_));
			$this->save_theoption( $this->alias . '_facebook_planner_last_status', $last_status );

			$extra_info = array();
			if ( isset($msg) && is_array($msg) ) {

				if ( isset($msg['link'], $msg['name']) ) {
					$extra_info = array(
						'auth_foruser_link' 	=> $msg['link'],
						'auth_foruser_name' 	=> $msg['name']
					);
				}
			}
			$this->save_theoption( $this->alias . '_facebook_planner', array_merge( (array) $fb_details, $last_status, $extra_info ) );
			
			return 'success' == $status ? 'valid' : 'invalid';
		}
	
		public function load_woocommerce_taxonomies() {
			if ( !$this->is_woocommerce_installed() ) return;
			if( class_exists( 'WooCommerce' ) ) {
				$wc_path = WC()->plugin_path();
				$wc_path_full = $wc_path . '/includes/class-wc-post-types.php';
				require_once( $wc_path_full );
				WC_Post_types::register_taxonomies();
				WC_Post_types::register_post_types();
			}
		}


		/** 
		 * Plugin is ACTIVE
		 */
		// verify plugin is ACTIVE (the right way)
		public function is_plugin_active( $plugin_name, $pms=array() ) {
			$pms = array_replace_recursive(array(
				'verify_active_for_network_only'		=> false,
				'verify_network_only_plugin'				=> false,
				'plugin_file'										=> array(), // verification is made by OR between items
				'plugin_class'										=> array(), // verification is made by OR between items
			), $pms);
			extract( $pms );

			switch ( strtolower($plugin_name) ) {
				case 'woocommerce':
					$plugin_file = array( 'woocommerce/woocommerce.php', 'envato-wordpress-toolkit/woocommerce.php' );
					$plugin_class = array( 'WooCommerce' );
					break;

				case 'woozone':
					$plugin_file = array( 'woozone/plugin.php' );
					$plugin_class = array( 'WooZone' );
					break;
					
				case 'psp':
					$plugin_file = array( 'premium-seo-pack/plugin.php' );
					$plugin_class = array( 'psp' );
					break;
					
				default:
					break;
			}

			$is_active = array();

			// verify plugin is active base on plugin main file 
			if ( ! empty($plugin_file) ) {
				if ( ! is_array($plugin_file) )
					$plugin_file = array( $plugin_file );

				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				$cc = false;
				foreach ($plugin_file as $_plugin_file) {
					// check if a plugin is site wide or network active only
					if ( $verify_active_for_network_only ) {
						if ( is_plugin_active_for_network( $_plugin_file ) )
							$cc = true;
					}
					// check if a plugin is a Network-Only-Plugin
					else if ( $verify_network_only_plugin ) {
						if ( is_network_only_plugin( $_plugin_file ) )
							$cc = true;
					}
					// check if a plugin is active (the right way)
					else {
						if ( is_plugin_active( $_plugin_file ) )
							$cc = true;
					}
				}
				$is_active[] = $cc;
			}

			// verify plugin class exists!
			if ( ! empty($plugin_class) ) {
				if ( ! is_array($plugin_class) )
					$plugin_class = array( $plugin_class );

				$cc = false;
				foreach ($plugin_class as $_plugin_class) {
					if ( class_exists( $_plugin_class ) )
						$cc = true;
				}
				$is_active[] = $cc;
			}

			// final verification
			if ( empty($is_active) ) return false;
			foreach ($is_active as $_is_active) {
				if ( ! $_is_active ) return false;
			}
			return true;
		}
		public function is_plugin_active_for_network_only( $plugin_name, $pms=array() ) {
			$pms = array_replace_recursive(array(
				'verify_active_for_network_only'		=> true,
			), $pms);
			return $this->is_plugin_active( $plugin_name, $pms );
		}
		public function is_plugin_network_only_plugin( $plugin_name, $pms=array() ) {
			$pms = array_replace_recursive(array(
				'verify_network_only_plugin'				=> true,
			), $pms);
			return $this->is_plugin_active( $plugin_name, $pms );
		}
		
		public function is_woocommerce_installed() {
			if ( in_array( 'envato-wordpress-toolkit/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || is_multisite() )
			{
				return true;
			} else {
				$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
				if ( !empty($active_plugins) && is_array($active_plugins) ) {
					foreach ( $active_plugins as $key => $val ) {
						if ( ($status = preg_match('/^woocommerce[^\/]*\/woocommerce\.php$/imu', $val))!==false && $status > 0 ) {
							return true;
						}
					}
				}
				return false;
			}
		}


		/**
		 * Plugin Version
		 */
		// latest code version
		public function version() {
			if ( defined('PSP_VERSION') ) {
				$this->version = (string) PSP_VERSION;
				return $this->version;
			}

			$path = $this->cfg['paths']['plugin_dir_path'] . 'plugin.php';
			if ( function_exists('get_plugin_data') ) {
				$plugin_data = get_plugin_data( $path );
			}
			else {
				$plugin_data = psp_get_plugin_data();
			}

			$latest_version = '1.0';
			if( isset($plugin_data) && is_array($plugin_data) && !empty($plugin_data) ){
				if ( isset($plugin_data['Version']) ) {
					$latest_version = (string)$plugin_data['Version'];
				}
				else if ( isset($plugin_data['version']) ) {
					$latest_version = (string)$plugin_data['version'];
				}
			}

			$this->version = $latest_version;
			return $this->version;
		}

		private function check_if_table_exists( $force=false ) {
			$need_check_tables = $this->plugin_integrity_need_verification('check_tables');
			if ( ! $need_check_tables['status'] && ! $force ) {
				return true; // don't need verification yet!
			}

			// default sql - tables & tables data!
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'modules/setup_backup/default-sql.php' );

			// retrieve all database tables & clean prefix
			$dbTables = $this->db->get_results( "show tables;", OBJECT_K );
			$dbTables = array_keys( $dbTables );
			if ( empty($dbTables) || ! is_array($dbTables) ) {

				$this->plugin_integrity_update_time('check_tables', array(
					'status'		=> 'invalid',
					'html'		=> __('Check plugin tables: error requesting tables list.', $this->localizationName),
				));
				return false; //something was wrong!
			}

			$dbTables_ = array();
			foreach ((array) $dbTables as $table) {
				$table_noprefix = str_replace($this->db->prefix, '', $table);
				$dbTables_[] = $table_noprefix;
			}

			// our plugin tables
			$dbTables_own = $this->plugin_tables;
			
			// did we find all our plugin tables?
			$dbTables_found = (array) array_intersect($dbTables_, $dbTables_own);
			$dbTables_missing = array_diff($dbTables_own, $dbTables_found);
			//var_dump('<pre>', $dbTables_own, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			if ( ! $dbTables_missing ) {

				$this->plugin_integrity_update_time('check_tables', array(
					'timeout'	=> time(),
					'status'		=> 'valid',
					'html'		=> __('Check plugin tables: all installed ( ' . implode(', ', $dbTables_found) . ' ).', $this->localizationName),
				));
				return true; // all is fine!
			}

			$this->plugin_integrity_update_time('check_tables', array(
				'status'		=> 'invalid',
				'html'		=> __('Check plugin tables: missing ( ' . implode(', ', $dbTables_missing) . ' ).', $this->localizationName),
			));
			return false; //something was wrong!
		}

		private function update_db_version( $version=null ) {
			delete_option( 'psp_db_version' );
			$version = empty($version) ? $this->version() : $version;
			add_option( 'psp_db_version', $version );
		}
		
		public function update_db( $force=false )  {
			// current installed db version
			//$current_db_version = get_option( 'psp_db_version' );
			//$current_db_version = !empty($current_db_version) ? (string)$current_db_version : '1.0';

			// default db structure - integrity verification is done in function
			$this->check_if_table_exists( $force );

			//$need_check_alter_tables = $this->plugin_integrity_need_verification('check_alter_tables');

			// installed version less than 2.0.4 / ex. 2.0.3.8
			//if ( version_compare( $current_db_version, '2.0.4', '<' ) ) {
			if (1) {
				//if ( $need_check_alter_tables['status'] || $force ) {
				//}
				
				$this->update_db_version('9.0');
			}

			// update installed version to latest
			$this->update_db_version();
			return true;
		}

		public function _update_db_tables( $pms=array() )  {
			//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			//$status = dbDelta($sql);
			
			extract( $pms );

			global $wpdb;
			foreach ( (array) $queries as $skey => $sql ) {
				if ( ! isset($sql['main']) ) continue 1;

				$do_main = 'add';
				if ( isset($sql['verify']) ) {
					$status = $wpdb->get_row( $sql['verify'], ARRAY_A );
					if ( ! empty($status) && isset($status['Field'], $status['Type']) ) {

						//'image_sizes' == strtolower($status['Field'])
						if ( isset($sql['field_type']) ) {
							if ( $sql['field_type'] == strtolower( $status['Type'] ) )
								$do_main = false;
							else
								$do_main = 'modify';
						}
					}
				} // end if verify

				if ( !empty($do_main) ) {
					$sql['main'] = sprintf( $sql['main'], strtoupper( $do_main ) );
					$status = $wpdb->query( $sql['main'] );
					//var_dump('<pre>', $sql, $status, '</pre>');
				}
			} // end foreach
			
			$this->plugin_integrity_update_time('check_alter_tables', array(
				'timeout'	=> time(),
				'status'		=> 'valid',
				'html'		=> __('Check plugin tables (alter): OK.', $this->localizationName),
			));
		}


		/**
		 * check plugin integrity: 2017-feb-28
		 */
		// what: check_database (includes: check_tables, check_alter_tables)
		public function plugin_integrity_check( $what='all', $force=false ) {
			$what = ! is_array($what) ? array('check_database') : $what;

			if ( in_array('check_database', $what) ) {
				$this->update_db( $force );
			}
		}

		public function plugin_integrity_get_last_status( $what ) {
			$ret = array(
				'status'				=> true,
				'html'				=> '',
			);

			// verify plugin integrity
			$plugin_integrity = get_option( 'psp_integrity_check', array() );
			$plugin_integrity = is_array($plugin_integrity) ? $plugin_integrity : array();

			$_status = true; $_html = array();
			if ( isset($plugin_integrity[ "$what" ]) && ! empty($plugin_integrity[ "$what" ]) ) {
				$__ = $plugin_integrity[ "$what" ];
				$_status = isset($__['status']) && 'valid' == $__['status'] ? true : false;
				$_html[] = $__['html'];
			}
			else {
				if ( 'check_database' == $what ) {
					foreach ($plugin_integrity as $key => $val) {
						if ( ! in_array($key, array('check_tables', 'check_alter_tables')) ) {
							continue 1;
						}

						$_status = $_status && ( isset($val['status']) && 'valid' == $val['status'] ? true : false );
						if ( ! empty($val['html']) ) {
							$_html[] = $val['html'];
						}
					}
				}
			}

			//$html = '<div><div>' . implode('</div><div>', $_html) . '</div></div>';
			$html = implode('&nbsp;&nbsp;&nbsp;&nbsp;', $_html);
			$ret = array_merge( $ret, array('status' => $_status, 'html' => $html) );
			return $ret;
		}

		// what: check_tables, check_alter_tables
		public function plugin_integrity_need_verification( $what ) {
			$ret = array(
				'status'				=> false,
				'data'				=> array(),
			);

			// verify plugin integrity
			$plugin_integrity = get_option( 'psp_integrity_check', array() );
			$plugin_integrity = is_array($plugin_integrity) ? $plugin_integrity : array();
			$ret = array_merge( $ret, array('data' => $plugin_integrity) );

			if ( isset($plugin_integrity[ "$what" ]) && ! empty($plugin_integrity[ "$what" ]) ) {
				if ( ( $plugin_integrity[ "$what" ]['timeout'] + $this->ss['check_integrity'][ "$what" ] ) > time() ) {
					$ret = array_merge( $ret, array('status' => false) ); // don't need verification yet!
					//var_dump('<pre>',$ret,'</pre>'); 
					return $ret;
				}
			}

			$ret = array_merge( $ret, array('status' => true) );
			return $ret;
		}

		public function plugin_integrity_update_time( $what, $data=array() ) {
			$plugin_integrity = get_option( 'psp_integrity_check', array() );
			$plugin_integrity = is_array($plugin_integrity) ? $plugin_integrity : array();

			$data = ! is_array($data) ? array() : $data;

			if ( ! isset($plugin_integrity[ "$what" ]) ) {
				$plugin_integrity[ "$what" ] = array(
					'timeout'	=> time(),
					'status'		=> 'invalid',
					'html'		=> '',
				);
			}
			$plugin_integrity[ "$what" ] = array_replace_recursive($plugin_integrity[ "$what" ], $data);
			update_option( 'psp_integrity_check', $plugin_integrity );
		}
	
	
		/**
		 * 2017 march - april
		 */
		public function get_post_metatags( $post ) {
			$postDefault = array();
			if ( ! is_object($post) ) {
				return $postDefault;
			}
			
			$modStatus = $this->verify_module_status( 'title_meta_format' ); //module is inactive

			if ( $this->ss['add_meta_placeholder'] && $modStatus ) {
				require_once( $this->cfg['paths']['plugin_dir_path'] . 'modules/title_meta_format/init.php');
				$info = new pspTitleMetaFormat();

				$info->setPostInfo( $post );

				$shareInfo = (object) array(
					'info'			=> $info,
					//'infoFb'		=> isset($infoFb) ? $infoFb : array(),
					//'infoTw'		=> isset($infoTw) ? $infoTw : array()
				);

				$postDefault = array(
					'the_title'								=> $shareInfo->info->get_the_title(),
					'the_meta_description'			=> $shareInfo->info->get_the_meta_description(),
					'the_meta_keywords'			=> $shareInfo->info->get_the_meta_keywords(),
				);
			} // end if
			return $postDefault;
		}

		public function get_taxonomy_nice_name($categ_name) {
			$ret = $categ_name;

			//$special = array('DVD' => 'DVD', 'MP3Downloads' => 'MP3 Downloads', 'PCHardware' => 'PC Hardware', 'VHS' => 'VHS');
			$special = array('MP3Downloads' => 'MP3 Downloads');
			if ( ! in_array($categ_name, array_keys($special)) ) {
				//$ret = preg_replace('/([A-Z])/', ' $1', $categ_name);
				$ret = preg_replace('/([A-Z])(?:[^A-Z ])/', ' $0', $categ_name);
			} else {
				$ret = $special["$categ_name"];
			}

			$ret = preg_replace('/\s\s+/i', ' ', $ret);
			$ret = trim($ret);
			return $ret;
		}
	}
}
// __DIR__ - uses PHP 5.3 or higher
// require_once( __DIR__ . '/functions.php');
require_once( dirname(__FILE__) . '/functions.php');