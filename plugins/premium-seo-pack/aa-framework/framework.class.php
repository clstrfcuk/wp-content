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
		
		public $plugin_tables = array('psp_link_builder', 'psp_link_redirect', 'psp_monitor_404', 'psp_web_directories', 'psp_serp_reporter', 'psp_serp_reporter2rank', 'psp_post_planner_cron', 'psp_alexa_rank');
		
		public $title_meta_format_default = array();

		// old = use old facebook sdk || fbv4 = use new facebook sdk (only authorization is implemented )
		public $facebook_sdk_version = 'fbv4'; // old | fbv4
		public $facebook_sdk_settings = array( // made for fbv4
			'default_graph_version' 	=> 'v2.4',
			'persistent_data_handler'	=> 'session'
		);
		
		public $updater_dev = null;


		/**
		 * The constructor
		 */
		public function __construct($here = __FILE__)
		{
			$this->is_admin = is_admin() === true ? true : false;

			// get the globals utils
			global $wpdb;

			// store database instance
			$this->db = $wpdb;

			$miscSettings = $this->getAllSettings( 'array', 'misc' );
			$this->use_wp_do_shortcode = isset($miscSettings['fix_use_wp_do_shortcode'])
				&& 'no' == $miscSettings['fix_use_wp_do_shortcode'] ? false : true;

			// admin css cache time ( 0 = no caching )
			$this->ss['css_cache_time'] = 86400; // seconds  (86400 seconds = 24 hours)
			if( defined('PSP_DEV_STYLE') ){
				$this->ss['css_cache_time'] = (int) PSP_DEV_STYLE; // seconds
			}
			
			if( defined('UPDATER_DEV') ) {
				$this->updater_dev = (string) UPDATER_DEV;
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
				'misc',
				'cronjobs',
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
				'additional' => 'css/additional.css',
				'sweetalert' => 'css/sweetalert.css'
			));

			// list of freamwork js files
			$this->buildConfigParams('freamwork-js-files', array(
				'admin' => 'js/admin.js',
				'hashchange' => 'js/hashchange.js',
				'ajaxupload' => 'js/ajaxupload.js',
				'tipsy'	=> 'js/tooltip.js',
				'sweetalert'	=> 'js/sweetalert.min.js',
				'google_analytics'	=> '../modules/Google_Analytics/app.class.js',
				//'menu-tooltip'	=> 'js/menu-tooltip.js',
				'percentageloader-0.1' => 'js/jquery.percentageloader-0.1.min.js',
				'flot-2.0' => 'js/jquery.flot/jquery.flot.min.js',
				'flot-tooltip' => 'js/jquery.flot/jquery.flot.tooltip.min.js',
				'flot-stack' => 'js/jquery.flot/jquery.flot.stack.min.js',
				'flot-pie' => 'js/jquery.flot/jquery.flot.pie.min.js',
				'flot-time' => 'js/jquery.flot/jquery.flot.time.js',
				'chart-bundle' => 'js/chart-bundle.js',
				'utils' => 'js/utils.js',
				'analyser' => 'js/analyser.js',
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

			// admin ajax action
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'modules/cronjobs/cronjobs.core.php' );
			new pspCronjobs( $this );
			//pspCronjobs::getInstance();
			
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
					if ( $_POST['ispspreq'] == 'post' ) {
						add_filter( 'the_content', array( $this, 'mark_content' ), 0, 1 );
					} else if ( $_POST['ispspreq'] == 'tax' ) {
						add_filter( 'term_description', array( $this, 'do_shortcode' ) );
						add_filter( 'term_description', array( $this, 'mark_content' ), 0, 1 );
					}
					add_action( 'wp', array( $this, 'clean_header' ) );
				}
			}

			$is_installed = get_option( $this->alias . "_is_installed" );
			if( $this->is_admin && $is_installed === false ) {
				add_action( 'admin_print_styles', array( $this, 'admin_notice_install_styles' ) );
			}
			
			// product updater
			add_action( 'admin_init', array($this, 'product_updater') );
		}
		
		/**
		 * Gets updater instance.
		 *
		 * @return AATeam_Product_Updater
		 */
		public function product_updater() {
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/class-updater.php' );
			
			if( class_exists('PSP_AATeam_Product_Updater') ){
				$product_data = get_plugin_data( $this->cfg['paths']['plugin_dir_path'] . 'plugin.php', false ); 
				new PSP_AATeam_Product_Updater( $this, $product_data['Version'], 'premium-seo-pack', 'premium-seo-pack/plugin.php' );
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
			$buffer = str_replace( '#module_url/', $this->cfg['paths']['freamwork_dir_url'], $buffer );
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
		
		public function do_shortcode( $content ) {
			if ( $this->use_wp_do_shortcode ) {
				$content = do_shortcode( $content );
			}
			return $content;
		}

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
			//var_dump('<pre>', $content , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			//$pattern = "/\[pspmark\].*\[\/pspmark\]/imu";
			//$ret = preg_match($pattern, $content, $matches);

			// php query class
			require_once( $this->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
			if ( !empty($this->charset) )
				$doc = pspphpQuery::newDocument( $content, $this->charset );
			else
				$doc = pspphpQuery::newDocument( $content );

			$content = pspPQ('#psp-content-mark');
			//var_dump('<pre>', $content , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$content = $content->html();
			//var_dump('<pre>', $content , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			//$title = pspPQ('h1:first');
			//$title = $title->html();
			//$title = '<h1 id="psp-title-mark">'.$title.'</h1>';
			//$content = $title . $content;

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
			$opt = get_option( $option_name );
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
					//add_action('pspwplannerhourlyevent', array( $this, 'facebook_wplanner_do_this_hourly' ));
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
					// Derive the current path and load up psp_aaInterfaceTemplates
					$plugin_path = dirname(__FILE__) . '/';
					if(class_exists('psp_aaInterfaceTemplates') != true) {
						require_once($plugin_path . 'settings-template.class.php');

						// Initalize the your psp_aaInterfaceTemplates
						$psp_aaInterfaceTemplates = new psp_aaInterfaceTemplates($this->cfg);

						// then build the html, and return it as string
						$html = $psp_aaInterfaceTemplates->bildThePage($options, $this->alias, $tryed_module);
						 
						// fix some URI
						$html = str_replace('{plugin_folder_uri}', $tryed_module['folder_uri'], $html);

						if(trim($html) != "") {
							$headline = $tryed_module[$request['section']]['menu']['title'] . "<span class='psp-section-info'> " . ( $tryed_module[$request['section']]['description'] ) . "</span>";
							
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
			wp_enqueue_style( $this->alias . '-seo-checks',   $this->cfg['paths']['freamwork_dir_url'] . 'css/seo-checks.css' );

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
				if( in_array( 'jquery-ui-button', $javascript ) ) wp_enqueue_script( 'jquery-ui-button' );
				if( in_array( 'jquery-ui-accordion', $javascript ) ) wp_enqueue_script( 'jquery-ui-accordion' );
				
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
			// Derive the current path and load up psp_aaInterfaceTemplates
			$plugin_path = dirname(__FILE__) . '/';
			if(class_exists('psp_aaInterfaceTemplates') != true) {
				require_once($plugin_path . 'settings-template.class.php');

				// Initalize the your psp_aaInterfaceTemplates
				$psp_aaInterfaceTemplates = new psp_aaInterfaceTemplates($this->cfg);

				// try to init the interface
				$psp_aaInterfaceTemplates->printBaseInterface();
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
				$post_title = $post->name;
				if( $post_content == 'empty' ){
					$post_content = $this->getPageContent( $post, $post->description, true );
				}
				if ( empty($post_content) ) {
					$post_content = $post->description;
				}

				$psp_current_taxseo = $this->__tax_get_post_meta( null, $post );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$post_metas = $this->get_psp_meta( $post, $psp_current_taxseo );
			}
			else {
				$post = get_post($post_id);
				if ( isset($post) && is_object($post) ) {
					$post_id = (int) $post->ID;
					$post_title = $post->post_title;
					if( $post_content == 'empty' ){
						$post_content = $this->getPageContent( $post, $post->post_content );
					}
					if ( empty($post_content) ) {
						$post_content = $post->post_content;
					}
				} else {
					$post_id = 0;
					$post_content = '';
					$post_title = '';
				}
				$post_metas = $this->get_psp_meta( $post_id );

				if ( is_array($post_metas) && ! empty($post_metas) ) {
					$post_metas['sitemap_isincluded'] = get_post_meta( $post_id, 'psp_sitemap_isincluded', true );
				}
			}

			$post_metas = array_merge(array(
				'title'					=> '',
				'description'			=> '',
				'keywords'				=> '',
				'focus_keyword'			=> '',
				'canonical'				=> '',
				'robots_index'			=> '',
				'robots_follow'			=> '',
				'priority'				=> '',
				'sitemap_isincluded' 	=> '',
			), (array) $post_metas);

			if ( is_null($seo) || !is_object($seo) ) {
				//use to generate meta keywords, and description for your requested item
				require_once( $this->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
				$seo = pspSeoCheck::getInstance();
			}

			// meta description
			$first_paragraph = $seo->get_first_paragraph( $post_content );
			$get_meta_desc = $seo->get_meta_desc( $first_paragraph );

			// meta keywords
			$get_meta_keywords = array();
			// focus keyword add to keywords is implemented in js file!
			//if ( !empty($post_metas['focus_keyword']) ) {
			//	$get_meta_keywords[] = $post_metas['focus_keyword'];
			//}
			$__tmp = $seo->get_meta_keywords( $post_content );
			if ( !empty($__tmp) ) {
				//$get_meta_keywords[] = $__tmp;
				$get_meta_keywords[] = implode(", ", $__tmp );
			}
			$get_meta_keywords = implode(', ', $get_meta_keywords);
			
			$post_metas['robots_index'] = isset($post_metas['robots_index']) && !empty($post_metas['robots_index'])
				? $post_metas['robots_index'] : 'default' ;
			$post_metas['robots_follow'] = isset($post_metas['robots_follow']) && !empty($post_metas['robots_follow'])
				? $post_metas['robots_follow'] : 'default';

			$post_metas['priority'] = isset($post_metas['priority']) && !empty($post_metas['priority'])
				? $post_metas['priority'] : '-' ;
			$post_metas['sitemap_isincluded'] = isset($post_metas['sitemap_isincluded']) && !empty($post_metas['sitemap_isincluded'])
				? $post_metas['sitemap_isincluded'] : 'default';

			$html = array();
			$html[] = '<div class="psp-post-title">' . $post_title . '</div>';
			$html[] = '<div class="psp-post-gen-desc">' . $get_meta_desc . '</div>';
			$html[] = '<div class="psp-post-gen-keywords">' . $get_meta_keywords . '</div>';
			$html[] = '<div class="psp-post-meta-title">' . $post_metas['title'] . '</div>';
			$html[] = '<div class="psp-post-meta-description">' . $post_metas['description'] . '</div>';
			$html[] = '<div class="psp-post-meta-keywords">' . $post_metas['keywords'] . '</div>';
			$html[] = '<div class="psp-post-meta-focus-kw">' . $post_metas['focus_keyword'] . '</div>';
			$html[] = '<div class="psp-post-meta-canonical">' . $post_metas['canonical'] . '</div>';
			$html[] = '<div class="psp-post-meta-robots-index">' . $post_metas['robots_index'] . '</div>';
			$html[] = '<div class="psp-post-meta-robots-follow">' . $post_metas['robots_follow'] . '</div>';
			$html[] = '<div class="psp-post-priority-sitemap">' . $post_metas['priority'] . '</div>';
			$html[] = '<div class="psp-post-include-sitemap">' . $post_metas['sitemap_isincluded'] . '</div>';

			$fieldsParams = array(
				'mfocus_keyword' => isset($post_metas['mfocus_keyword']) ? $post_metas['mfocus_keyword'] : ''
			);
			$html[] = '<div class="psp-post-meta-fields-params" style="display: none;">' . htmlentities(json_encode( $fieldsParams )). '</div>';

			// post default - placeholder
			$postDefault = $this->get_post_metatags( $post ); // add meta placeholder
			if ( ! empty($postDefault) ) {
				foreach ( $postDefault as $key => $val) {
					$html[] = '<div class="psp-post-default-' . $key . '">' . $val . '</div>';
				}
			}

			return implode(PHP_EOL, $html);
		}
		
		public function edit_post_inline_boxtpl() {

			// sitemap priority
			$sitemap_priority = array();
			$__range = range(0, 1, 0.1);
			$__range2 = array();
			for ($i=(count($__range)-1); $i>=0; $i--) {
				$__range2[] = $__range[ $i ];
			}
			foreach ($__range2 as $kk => $vv) {
				$__priorityText = '';
				$vv = (string) $vv;
				if ( $vv=='1' )
					$__priorityText = ' - ' . __('Highest priority', 'psp');
				else if ( $vv=='0.5' )
					$__priorityText = ' - ' . __('Medium priority', 'psp');
				else if ( $vv=='0.1' )
					$__priorityText = ' - ' . __('Lowest priority', 'psp');
					
				$sitemap_priority[] = '<option value="' . ( $vv ) . '">' . ( $vv . $__priorityText ) . '</option>';
			}
			$sitemap_priority = implode(PHP_EOL, $sitemap_priority);

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
				<th>' . __('Meta Description', $this->localizationName) . '</th>
				<th>' . __('Meta Keywords', $this->localizationName) . '</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="49.99%">
					<div>
						<textarea name="psp-editpost-meta-description" id="psp-editpost-meta-description" rows="3" class="large-text"></textarea>
					</div>
				</td>
				<td width="49.99%">
					<div>
						<textarea name="psp-editpost-meta-keywords" id="psp-editpost-meta-keywords" rows="3" class="large-text"></textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan=3>
					<table class="form-table" style="border: 1px solid #dadada;">
						<tbody>
							<tr>
								<td colspan=2 width="50%">
									<div>
										<span>' . __('Meta Title:', $this->localizationName) . '</span>
										<input type="text" class="" style="" value="" name="psp-editpost-meta-title" id="psp-editpost-meta-title">
									</div>
								</td>
								<td colspan=2 width="50%">
									<div>
										<span>' . __('Canonical URL:', $this->localizationName) . '</span>
										<input type="text" class="" style="" value="" name="psp-editpost-meta-canonical" id="psp-editpost-meta-canonical">
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="psp-inline-meta-wrapp">
										<div>' . __('Meta Robots Index:', $this->localizationName) . '</div>
										<select name="psp-editpost-meta-robots-index" id="psp-editpost-meta-robots-index">
											<option value="default" selected="true">' . __('Default Setting', $this->localizationName) . '</option>
											<option value="index">' . __('Index', $this->localizationName) . '</option>
											<option value="noindex">' . __('NO Index', $this->localizationName) . '</option>
										</select>
									</div>
								</td>
								<td>
									<div class="psp-inline-meta-wrapp">
										<div>' . __('Meta Robots Follow:', $this->localizationName) . '</div>
										<select name="psp-editpost-meta-robots-follow" id="psp-editpost-meta-robots-follow">
											<option value="default" selected="true">Default Setting</option>
											<option value="follow">Follow</option>
											<option value="nofollow">NO Follow</option>
										</select>
									</div>
								</td>
								<td>
									<div class="psp-inline-meta-wrapp">
										<div>' . __('Include in Sitemap:', $this->localizationName) . '</div>
										<select name="psp-editpost-include-sitemap" id="psp-editpost-include-sitemap">
											<option value="default" selected="true">' . __('Default Setting', $this->localizationName) . '</option>
											<option value="always_include">' . __('Always include', $this->localizationName) . '</option>
											<option value="never_include">' . __('Never include', $this->localizationName) . '</option>
										</select>
									</div>
								</td>
								<td>
									<div class="psp-inline-meta-wrapp">
										<div>' . __('Sitemap Priority:', $this->localizationName) . '</div>
										<select name="psp-editpost-priority-sitemap" id="psp-editpost-priority-sitemap">
											<option value="-" selected="true">Automatic</option>
											' . $sitemap_priority . '
										</select>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
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
				else if (
					//Unauthorized
					isset($resp['response'], $resp['response']['code'])
					&& (401 == (int) $resp['response']['code'])
				) {
					$body = false;
					//401 Unauthorized: the page you were trying to access cannot be loaded until you first log in with a valid user ID and password
					$err = '401 Unauthorized: also verify if your website requires .htpasswd authorization.';
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

			if ( !$show_sizes ){
				return $ret;
			}

			// no media sizes
			if ( !isset($meta['sizes']) || empty($meta['sizes']) ) {
				return $ret;
			}

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
            if ( isset($timeout) && $timeout!== false ) curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            
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

        public function get_current_page_url( $pms=array() ) {
			$pms = array_replace_recursive(array(
				'exclude_request_uri'	=> false, // url will not include $_SERVER['REQUEST_URI']
				'force_include_port'	=> false, // always include port, independent of 'include_port' value
				'include_port'			=> true, // will include port but not if: (NOT SSL & 80) | (SSL & 443)
			), $pms);
			extract( $pms );

        	$s 			= $_SERVER;
			$ssl		= $this->is_ssl();
			$port 		= $this->get_port();

			$sp 		= isset($s['SERVER_PROTOCOL']) ? strtolower( $s['SERVER_PROTOCOL'] ) : '';
			$protocol 	= substr( $sp, 0, strpos( $sp, '/' ) ) . ( $ssl ? 's' : '' );

			// include port?
			$inc_port = $force_include_port;
			if ( ! $force_include_port && $include_port ) {
				if ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) {
					$inc_port = false;
				}
			}

			// build url
			$url = array();
			$url[] = $protocol . '://';
			$url[] = $this->get_host( $inc_port );
			if ( isset($_SERVER['REQUEST_URI']) && ! $exclude_request_uri ) {
				$url[] = $_SERVER['REQUEST_URI'];
			}

			$url = implode('', $url);
			return $url;
        }

		// verbose translation from Symfony
		public function get_host( $include_port=false ) {
		    $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
		    $sourceTransformations = array(
		    	// since PHP 4 >= 4.0.1, PHP 5, PHP 7
		        "HTTP_X_FORWARDED_HOST" => create_function('$value', '$elements = explode(",", $value); return trim(end($elements));'),

				// since PHP 5.3.0 (anonymous function)
		        //"HTTP_X_FORWARDED_HOST" => function($value) {
		        //    $elements = explode(',', $value);
		        //    return trim(end($elements));
		        //},
		    );

		    $host = '';
		    foreach ($possibleHostSources as $source) {
		        if (!empty($host)) break;
		        if (!isset($_SERVER[$source]) || empty($_SERVER[$source])) continue;

		        $host = $_SERVER[$source];
		        if (array_key_exists($source, $sourceTransformations)) {
		            $host = $sourceTransformations[$source]($host);
		        } 
		    } // end foreach

        	$s 			= $_SERVER;
			$ssl		= $this->is_ssl();
			$port 		= $this->get_port();

		    // Remove port number from host
		    if ( !$include_port ) {
		    	$host = preg_replace('/:\d+$/', '', $host);
			}
			// Include Port
			else {
				$found = preg_match('/:\d+$/', $host);
				if ( empty($found) && !empty($port) ) {
					$host .= ':'.$port;
				}
			}

			$host = trim($host);
			//$host = strtolower($host);
		    return $host;
		}

		// get current port
		public function get_port() {
			// CHECK PROXY
			if ( isset($_SERVER['HTTP_X_FORWARDED_PORT']) ) {
				$port = (string) $_SERVER['HTTP_X_FORWARDED_PORT'];
				return $port;
			}

			if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ) {
				$port = (string) $_SERVER['HTTP_X_FORWARDED_PROTO'];
				if ( in_array(strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']), array('https')) ) {
					$port = '443';
					return $port;
				}
			}

			// SERVER PORT
			$port = isset($s['SERVER_PORT']) ? $s['SERVER_PORT'] : '';
			return $port;
		}

		// Determine if SSL is used.
		public function is_ssl() {
			// CHECK PROXY: HTTP_X_FORWARDED_PROTO: a de facto standard for identifying the originating protocol of an HTTP request, since a reverse proxy (load balancer) may communicate with a web server using HTTP even if the request to the reverse proxy is HTTPS
			if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ) {
				if ( in_array(strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']), array('on', '1', 'https', 'ssl')) )
					return true;
			}
			if ( isset($_SERVER['HTTP_X_FORWARDED_SSL']) ) {
				if ( in_array(strtolower($_SERVER['HTTP_X_FORWARDED_SSL']), array('on', '1', 'https', 'ssl')) )
					return true;
			}

			if ( isset($_SERVER['HTTPS']) ) {
				if ( in_array(strtolower($_SERVER['HTTPS']), array('on', '1', 'https', 'ssl')) )
					return true;
			}
			else if ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
				return true;
			}

			return false;
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
			$current_db_version = get_option( 'psp_db_version' );
			//$current_db_version = !empty($current_db_version) ? (string) $current_db_version : '1.0';

			// default db structure - integrity verification is done in function
			$this->check_if_table_exists( $force );

			$need_check_alter_tables = $this->plugin_integrity_need_verification('check_alter_tables');

			// installed version less than 2.0.4 / ex. 2.0.3.8
			//if ( version_compare( $current_db_version, '2.0.4', '<' ) ) {
			if (1) {
				// if need_check_alter_tables
				if ( $need_check_alter_tables['status'] || $force
					|| ( ! empty($current_db_version) && version_compare( $current_db_version, '2.1.1', '<' ) )
				) {

					// installed version less than 2.1.1 / ex. 2.0.4
					$table_name = $this->db->prefix . "psp_link_builder";
					if ( $this->db->get_var("show tables like '$table_name'") == $table_name ) {
						$this->_update_db_tables(array(
							'operation'		=> $table_name,
							'table'			=> $table_name,
							'queries'		=> array(
								'attr_title'				=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `attr_title` TEXT NULL;",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'attr_title';",
									'field_name'	=> 'attr_title',
									'field_type'	=> 'text',
								),
							),
						));
					}

					$table_name = $this->db->prefix . "psp_link_redirect";
					if ( $this->db->get_var("show tables like '$table_name'") == $table_name ) {
						$this->_update_db_tables(array(
							'operation'		=> $table_name,
							'table'			=> $table_name,
							'queries'		=> array(
								// columns
								'redirect_type'				=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `redirect_type` VARCHAR(25) NULL DEFAULT '';",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'redirect_type';",
									'field_name'	=> 'redirect_type',
									'field_type'	=> 'varchar(25)',
								),
								'redirect_rule'				=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `redirect_rule` VARCHAR(25) NULL DEFAULT 'custom_url';",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'redirect_rule';",
									'field_name'	=> 'redirect_rule',
									'field_type'	=> 'varchar(25)',
								),
								'target_status_code'		=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `target_status_code` VARCHAR(25) NULL DEFAULT '';",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'target_status_code';",
									'field_name'	=> 'target_status_code',
									'field_type'	=> 'varchar(25)',
								),
								'target_status_details'		=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `target_status_details` TEXT NULL;",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'target_status_details';",
									'field_name'	=> 'target_status_details',
									'field_type'	=> 'text',
								),
								'group_id'		=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `group_id` INT(5) NULL DEFAULT '1';",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'group_id';",
									'field_name'	=> 'group_id',
									'field_type'	=> 'int(5)',
								),
								'post_id'		=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `post_id` INT(10) NULL DEFAULT '0';",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'post_id';",
									'field_name'	=> 'post_id',
									'field_type'	=> 'int(10)',
								),
								'publish'					=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s COLUMN `publish` CHAR(1) DEFAULT 'Y';",
									'verify'		=> "SHOW COLUMNS FROM " . $table_name . " LIKE 'publish';",
									'field_name'	=> 'publish',
									'field_type'	=> 'char(1)',
								),
							),
							// !!!must be after queries to be sure that all columns exists!
							// index_name, index_type, index_cols: all are mandatory
							'indexes'		=> array(
								'url_redirect'			=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`url_redirect`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'url_redirect';",
									'index_name'	=> 'url_redirect',
									'index_type'	=> 'key',
									'index_cols'	=> array('url_redirect'),
								),
								'redirect_type'			=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`redirect_type`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'redirect_type';",
									'index_name'	=> 'redirect_type',
									'index_type'	=> 'key',
									'index_cols'	=> array('redirect_type'),
								),
								'redirect_rule'			=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`redirect_rule`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'redirect_rule';",
									'index_name'	=> 'redirect_rule',
									'index_type'	=> 'key',
									'index_cols'	=> array('redirect_rule'),
								),
								'target_status_code'	=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`target_status_code`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'target_status_code';",
									'index_name'	=> 'target_status_code',
									'index_type'	=> 'key',
									'index_cols'	=> array('target_status_code'),
								),
								'group_id'			=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`group_id`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'group_id';",
									'index_name'	=> 'group_id',
									'index_type'	=> 'key',
									'index_cols'	=> array('group_id'),
								),
								'post_id'			=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`post_id`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'post_id';",
									'index_name'	=> 'post_id',
									'index_type'	=> 'key',
									'index_cols'	=> array('post_id'),
								),
								'publish'			=> array(
									'main' 			=> "ALTER TABLE " . $table_name . " %s (`publish`);",
									'verify'		=> "SHOW INDEX FROM " . $table_name . " WHERE 1=1 and Key_name LIKE 'publish';",
									'index_name'	=> 'publish',
									'index_type'	=> 'key',
									'index_cols'	=> array('publish'),
								),
							),
						));
					}

				} // end if need_check_alter_tables
				
				$this->update_db_version('2.0.4');
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
			// queries columns
			foreach ( (array) $queries as $skey => $sql ) {
				if ( ! isset($sql['main']) ) continue 1;

				$do_main = 'add';
				if ( isset($sql['verify']) ) {
					$status = $wpdb->get_row( $sql['verify'], ARRAY_A );
					if ( ! empty($status) && isset($status['Field'], $status['Type']) ) {

						//'image_sizes' == strtolower($status['Field'])
						if ( isset($sql['field_type']) ) {
							if ( strtolower($sql['field_type']) == strtolower( $status['Type'] ) )
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

			// queries indexes
			//ADD KEY newkeyname | DROP KEY oldkeyname, ADD KEY newkeyname
			if ( isset($indexes) ) { foreach ( (array) $indexes as $skey => $sql ) {
				if ( ! isset($sql['main']) ) continue 1;

				$index_name = isset($sql['index_name']) ? $sql['index_name'] : $skey;
				$index_type = isset($sql['index_type']) ? $sql['index_type'] : 'key';
				$index_cols = isset($sql['index_cols']) ? $sql['index_cols'] : array();

				$do_main = 'add';
				if ( isset($sql['verify']) ) {
					$status = $wpdb->get_results( $sql['verify'], ARRAY_A );

					$cols = array();
					if ( ! empty($status) ) {
						foreach ($status as $idxKey => $idxVal) {
							$cols[] = $idxVal['Column_name'];
						}
						$cols = array_unique( array_filter( $cols) );
						$diff = array_diff($index_cols, $cols);

						if ( ! empty($diff) )
							$do_main = 'modify';
						else
							$do_main = false;
					}
				} // end if verify

				if ( !empty($do_main) ) {
					$do_main2 = array();
					if ( 'modify' == $do_main ) {
						$do_main2[] = 'DROP ' . strtoupper($index_type) . ' ' . $index_name;
					}
					$do_main2[] = 'ADD ' . strtoupper($index_type) . ' ' . $index_name;
					$do_main = implode(', ', $do_main2);

					$sql['main'] = sprintf( $sql['main'], $do_main );
					$status = $wpdb->query( $sql['main'] );
					//var_dump('<pre>', $sql, $status, '</pre>');
				}
			} } // end foreach & if

			//if ( $this->db->prefix . "psp_link_redirect" == $operation ) {
			//	echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			//}

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
		 * 2017 march - may
		 */
		public function get_post_metatags( $post ) {
			$postDefault = array(
					'the_title'								=> '',
					'the_meta_description'			=> '',
					'the_meta_keywords'			=> '',
			);
			if ( is_array($post) ) {
				$post = (object) $post; 
			}
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
			//var_dump('<pre>', $postDefault, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
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

		public function get_content_analyzing_rules() {
			$ret = array(
				'title'						=> 'SEO Title: verify the allowed number of characters and if seo title contains/begins with your focus keyword',
				'title_enough_words'		=> 'SEO Title Words: verify seo title minimum number of words',
				'page_title'				=> 'Page Title: verify the allowed number of characters and if seo title contains/begins with your focus keyword',
				'meta_description'			=> 'Meta Description: verify the allowed number of characters and if meta description contains/begins with your focus keyword',
				'meta_keywords'				=> 'Meta Keywords: verify if any exists and if meta keywords contains with your focus keyword',
				'permalink'					=> 'Permalink: verify if post permalink contains your focus keyword',
				'first_paragraph'			=> 'First Paragraph: verify if first paragraph of the page content contains with your focus keyword',
				'embedded_content'			=> 'Embedded Content: verify if the page content contains frames, iframes, flash or video objects',
				'enough_words'				=> 'Enought Words: verify the page content minimum number of words',
				'images_alt'				=> 'Images: verify if the posts content has images and if they contains your focus keyword in alt attribute',
				'html_bold'					=> 'Mark as Bold: verify if the page content has at least one bold element',
				'html_italic'				=> 'Mark as Italic: verify if the page content has at least one italic element',
				'html_underline'			=> 'Mark as Underline: verify if the page content has at least one underlined element',
				'subheadings'				=> 'Subheading Tags: verify if the page content has any subheading tags (h1, h2, h3) and if they contains your focus keyword (also verify if h1 begins with your focus keyword)',
				'first100words'				=> 'First 100 Words: verify if the page content contains your focus keyword in the first 100 words',
				'last100words'				=> 'Last 100 Words: verify if the page content contains your focus keyword in the last 100 words',
				'links_external'			=> 'External Links: verify if the page content has any external links (and if they have nofollow rel attribute)',
				'links_internal'			=> 'Internal Links: verify if the page content has any internal links (and if they have nofollow rel attribute)',
				'links_competing'			=> 'Competing Links: verify if the page content has any potential competing links (which contains your focus keyword)',
				'kw_density'				=> 'Keyword density: verify the allowed number of focus keyword occurences in the content compared to the number of words in the content',
			);
			return $ret;
		}

		public function get_content_analyzing_allowed_rules( $pms=array() ) {
			if ( ! isset($pms['settings']) || empty($pms['settings']) ) {
				$pms['settings'] = $this->get_theoption('psp_on_page_optimization');
			}

			$pms = array_replace_recursive(array(
				'settings'	=> array(),
				'istax'		=> false,
			), $pms);
			extract($pms);

			$what = 'post_allowed_rules';
			if ( $istax ) {
				$what = 'category_allowed_rules';
			}

			$rules_allowed = array_keys( $this->get_content_analyzing_rules() );

			$allowed = isset($settings["$what"]) && ! empty($settings["$what"])
				? $settings["$what"] : array();
			$allowed = array_filter( array_unique( $allowed ) );

			if ( ! empty($allowed) ) {
				$rules_allowed = $allowed;
			}
			return $rules_allowed;
		}

		public function build_score_html_container( $score=0, $pms=array() ) {
			$pms = array_replace_recursive(array(
				'show_score'		=> true,
				'css_style'			=> '',
			), $pms);
			extract( $pms );

			$_css_style = ( '' != $css_style ? ' ' . $css_style : '' );

			$size_class = 'size_';
			if ( $score >= 20 && $score < 40 ) {
				$size_class .= '20_40';
			}
			else if ( $score >= 40 && $score < 60 ) {
				$size_class .= '40_60';
			}
			else if ( $score >= 60 && $score < 80 ) {
				$size_class .= '60_80';
			}
			else if ( $score >= 80 && $score <= 100 ) {
				$size_class .= '80_100';
			}
			else {
				$size_class .= '0_20';
			}

			$html = array();
			$html[] = '<div class="psp-progress"' . $_css_style . '>';
			$html[] = 		'<div class="psp-progress-bar ' . ( $size_class ) . '" style="width:' . ( $score ) . '%"></div>';
			if ( $show_score ) {
				$html[] =	'<div class="psp-progress-score">' . ( $score ) . '%</div>';
			}
			$html[] = '</div>';
			return implode('', $html);
		}

		public function xml_entities($string, $encoding) {
			//return htmlspecialchars($string, ENT_QUOTES | ENT_XML1, $encoding); // only >= PHP 5.4
			return htmlspecialchars($string, ENT_QUOTES, $encoding);

		    return strtr(
		        $string, 
		        array(
		            "<" => "&lt;",
		            ">" => "&gt;",
		            '"' => "&quot;",
		            "'" => "&apos;",
		            "&" => "&amp;",
		        )
		    );
		}


		/**
		 * FACEBOOK
		 */
		// Facebook: cronjob
		//public function facebook_wplanner_do_this_hourly() {
		//	// Plugin cron class loading
		//	require_once ( $this->cfg['paths']['plugin_dir_path'] . 'modules/facebook_planner/app.cron.class.php' );
		//}

		// Facebook: save operation last status
		public function facebook_planner_last_status( $pms=array() ) {
			extract($pms);

			$fb_details = $this->getAllSettings('array', 'facebook_planner');
			//var_dump('<pre>', $fb_details , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			if ( 'exception' == $status ) {
				$e = $msg;
				if (isset($e->faultcode)) { // error occured!
					$msg = $e->faultcode .  ' : ' . (isset($e->faultstring) ? $e->faultstring : $e->getMessage());
				} else {
					$msg = $e->getMessage();
				}
			}
			else if ( in_array($status, array('success', 'error')) ) {
				if ( is_object($msg) ) {
					$msg = (array) $msg;
				}
			}
			$msg = serialize( $msg );

			$last_status = array('last_status' => array(
				'status'	=> $status,
				'data'		=> date("Y-m-d H:i:s"),
				'from_file' => $from_file,
				'from_func' => $from_func,
				'from_line' => $from_line,
				'msg'		=> $msg,
			));
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

		// Facebook: load new SKD
		public function facebook_load_sdk() {
			if ( !defined('FACEBOOK_SDK_V4_SRC_DIR') ) {
				define( 'FACEBOOK_SDK_V4_SRC_DIR', $this->cfg['paths']['scripts_dir_path'] . '/facebook-v5-5.0.0/' );
			}
			require_once( $this->cfg['paths']['scripts_dir_path'] . '/facebook-v5-5.0.0/autoload.php' );
		}

		// Facebook: call default params
		public function facebook_call_default_params( $pms=array() ) {
			if ( ! isset($pms['fb_details']) || empty($pms['fb_details']) ) {
				$pms['fb_details'] = $this->getAllSettings('array', 'facebook_planner');
			}
			if ( ! isset($pms['facebook']) || is_null($pms['facebook']) ) {
				$fb_details = $pms['fb_details'];
				if( (isset($fb_details['app_id']) && trim($fb_details['app_id']) != '') && ( isset($fb_details['app_secret']) && trim($fb_details['app_secret']) != '') ) {

					$fbInitParams = array_replace_recursive(array(
						'app_id' 					=> $fb_details['app_id'],
						'app_secret' 				=> $fb_details['app_secret'],
					), $this->facebook_sdk_settings);
					$facebook = new Facebook\Facebook( $fbInitParams );
					$pms['facebook'] = $facebook;
				}
			}
			return $pms;
		}

		public function facebook_call_plugin_default() {
			$def = array();
			$def['plugin_url'] = admin_url('admin.php?page=psp#facebook_planner');
			$def['plugin_url_'] = '<a href="'.$def['plugin_url'].'" class="psp-form-button-small psp-form-button-info">' . __('Go Back to the plugin facebook planner module and try again.', $this->localizationName) . '</a><br />';
			return $def;
		}

		// Facebook: get authorization url 
		public function facebook_get_authorization_url( $pms=array() ) {
			$pms = $this->facebook_call_default_params($pms);
			$pms = array_merge(array(
				'facebook'			=> null,
				'fb_details'		=> array(),
				'psp_redirect_url'	=> '',
				'text'				=> __('Authorize app', $this->localizationName),
			), $pms);
			extract($pms);

			$ret = array(
				'html'				=> '',
				'url'				=> '#something-is-wrong',
			);

			if( isset($facebook) && ! is_null($facebook) ) {
				$fb_helper = $facebook->getRedirectLoginHelper();
				$fb_permissions = ['email','publish_actions','manage_pages','publish_pages', 'user_managed_groups']; // optional

				$fb_loginUrl = admin_url('admin-ajax.php?action=psp_facebookAuth%s');
				if ( $psp_redirect_url != '' ) {
					$fb_loginUrl = sprintf( $fb_loginUrl, '&psp_redirect_url='.$psp_redirect_url );
				} else {
					$fb_loginUrl = sprintf( $fb_loginUrl, '' );
				}
				$fb_loginUrl = $fb_helper->getLoginUrl($fb_loginUrl, $fb_permissions);

				$ret['url'] = $fb_loginUrl;
			}

			$ret['html'] = '<a href="' . $ret['url'] . '" class="psp-form-button psp-form-button-info pspStressTest inline" data-saveform="no">' . $text . '</a>';

			return $ret;
		}

		// Facebook: Authorization STEP 1 - new SDK
		// used in /aa-framework/settings-template.class.php , option 'authorization_button_fbv4'
		// used in /modules/facebook_planner/init.php , method 'makeoAuthLogin_fbv4'
		// used also here in this file
		public function facebook_do_authorization( $pms=array() ) {
			$pms = $this->facebook_call_default_params($pms);
			$pms = array_merge(array(
				'facebook'			=> null,
				'fb_details'		=> array(),
				'psp_redirect_url'	=> '',
			), $pms);
			extract($pms);

			$validAuth = false;
			$html = array();
			$user_profile = array();

			$ret = array(
				'validAuth'			=> $validAuth,
				'html'				=> $html,
				'user_profile'		=> $user_profile,
			);

			// load the facebook SDK
			$this->facebook_load_sdk();

			// :: Facebook Authorization STEP 1 - new SDK
			// see modules/facebook_planner/init.php , method 'fbAuth_fbv4' for step 2
			if( isset($facebook) && ! is_null($facebook) ) {
				$dbToken = get_option('psp_fb_planner_token');
				//var_dump('<pre>', $dbToken, '</pre>'); die('debug...');

				$getAuthUrl = $this->facebook_get_authorization_url(array(
					'facebook'			=> $facebook,
					'fb_details'		=> $fb_details,
					'psp_redirect_url'	=> $psp_redirect_url,
					'text'				=> __('Authorize app', $this->localizationName),
				));
				$fb_loginUrl = $getAuthUrl['url'];

				if ( !empty($dbToken) ) {
					$facebook->setDefaultAccessToken($dbToken);

					$fb_response = null;
					try {
						// Returns a `Facebook\FacebookResponse` object
						$fb_response = $facebook->get('/me?fields=id,name,link');
					} catch(Facebook\Exceptions\FacebookResponseException $e) {
						$html[] = '<p>Graph returned an error: ' . $e->getMessage() . '</p>';

						$this->facebook_planner_last_status(array(
							'status' 	=> 'exception',
							'msg' 		=> $e,
							'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
							'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
							'from_line' => __LINE__,
						));
					} catch(Facebook\Exceptions\FacebookSDKException $e) {
						$html[] = '<p>Facebook SDK returned an error: ' . $e->getMessage() . '</p>';
						
						$this->facebook_planner_last_status(array(
							'status' 	=> 'exception',
							'msg' 		=> $e,
							'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
							'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
							'from_line' => __LINE__,
						));
					}

					if ( !empty($fb_response) ) {
						$user_profile = $fb_response->getGraphUser();
						if (count($user_profile) > 0){
							$validAuth = true;
								
							$html[] = '<p>This plugin is <b>authorized</b> for: <a target="_blank" href="' . ( $user_profile['link'] ) . '">' . $user_profile['name'] . '</a></p>';
								
							$html[] = '<a href="' . ($fb_loginUrl) . '" style="width: 133px;" class="psp-form-button-small psp-form-button-info">'. (__( 'Authorize this app again', $this->localizationName )) .'</a>';
							
							$this->facebook_planner_last_status(array(
								'status' 	=> 'success',
								'msg' 		=> $user_profile,
								'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
								'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
								'from_line' => __LINE__,
							));
						} else {
							$this->facebook_planner_last_status(array(
								'status' 	=> 'error',
								'msg' 		=> $user_profile,
								'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
								'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
								'from_line' => __LINE__,
							));
						}
					} else {
						$this->facebook_planner_last_status(array(
							'status' 	=> 'error',
							'msg' 		=> 'empty response when /me?fields=id,name,link',
							'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
							'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
							'from_line' => __LINE__,
						));
					}
				}

				if( $validAuth == false ) {
					$html[] = '<a href="' . ($fb_loginUrl) . '" style="width: 84px;" type="button" class="psp-form-button-small psp-form-button-info">'. (__( 'Authorizate app', $this->localizationName )) .'</a>';
				}
			}

			$ret = array(
				'validAuth'			=> $validAuth,
				'html'				=> implode('', $html),
				'user_profile'		=> $user_profile,
			);
			return $ret;
		}

		// Facebook: Authorization STEP 2 - new SDK
		// used in /modules/facebook_planner/init.php , method 'fbAuth_fbv4' for step 2
		public function facebook_do_login( $pms=array() ) {
			$def = $this->facebook_call_plugin_default();
			$pms = $this->facebook_call_default_params($pms);
			$pms = array_merge(array(
				'facebook'			=> null,
				'fb_details'		=> array(),
				'plugin_url'		=> $def['plugin_url'],
				'plugin_url_'		=> $def['plugin_url_'],
				//'dbToken'			=> '',
			), $pms);
			extract($pms);

			$ret = array(
				'opStatus'		=> false,
				'opMsg'			=> '',
				'facebook'		=> null,
				'accessToken'	=> '',
			);

			if( isset($facebook) && ! is_null($facebook) ) {
				//if ( $dbToken != '' ) {
				//	$facebook->setDefaultAccessToken($dbToken);
				//}
				$fb_helper = $facebook->getRedirectLoginHelper();
			}
			else {
				$ret['opMsg'] = 'Invalid Facebook object!';
				return $ret;
			}
 
			try {
				$accessToken = $fb_helper->getAccessToken();
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$this->facebook_planner_last_status(array(
					'status' 	=> 'exception',
					'msg' 		=> $e,
					'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
					'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
					'from_line' => __LINE__,
				));

				// When Graph returns an error
				$ret['opMsg'] = $plugin_url_.'<br/>' . 'Graph returned an error: ' . $e->getMessage();
				return $ret;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$this->facebook_planner_last_status(array(
					'status' 	=> 'exception',
					'msg' 		=> $e,
					'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
					'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
					'from_line' => __LINE__,
				));

				// When validation fails or other local issues
				$ret['opMsg'] = $plugin_url_.'<br/>' . 'Facebook SDK returned an error: ' . $e->getMessage();
				return $ret;
			}

			if ( !isset($accessToken) ) {
				$is_error = $fb_helper->getError() || $fb_helper->getErrorCode();
				if ($is_error) {
					$error_details = array(
						'error'				=> $fb_helper->getError(),
						'error_code'		=> $fb_helper->getErrorCode(),
						'error_reason'		=> $fb_helper->getErrorReason(),
						'error_desc'		=> $fb_helper->getErrorDescription() || (isset($_GET['error_message']) ? $_GET['error_message']  : null),
					);
					$error_details = array_filter($error_details);

					$this->facebook_planner_last_status(array(
						'status' 	=> 'error',
						'msg' 		=> $error_details,
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					//header('HTTP/1.0 401 Unauthorized');
					$ret['opMsg'] = $plugin_url_.'<br/>' . 'HTTP/1.0 401 Unauthorized';
					return $ret;
				} else {
					$this->facebook_planner_last_status(array(
						'status' 	=> 'error',
						'msg' 		=> 'Bad request',
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					//header('HTTP/1.0 400 Bad Request');
					$ret['opMsg'] = $plugin_url_.'<br/>' . 'HTTP/1.0 400 Bad Request';
					return $ret;
				}
			}

			// Logged in
			//echo '<h3>Access Token</h3>';
			//var_dump($accessToken->getValue());
			
			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $facebook->getOAuth2Client();
			
			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken( $accessToken );
			//echo '<h3>Metadata</h3>';
			//var_dump('<pre>',$accessToken,$tokenMetadata,'</pre>');
			  
			// Validation (these will throw FacebookSDKException's when they fail)
			$tokenMetadata->validateAppId( $fb_details['app_id']);
			// If you know the user ID this access token belongs to, you can validate it here
			//$tokenMetadata->validateUserId('123');
			//$tokenMetadata->validateExpiration();
 
			if ( !$accessToken->isLongLived() ) {
				// Exchanges a short-lived access token for a long-lived one
				try {
					$accessToken = $oAuth2Client->getLongLivedAccessToken( $accessToken );
				} catch (Facebook\Exceptions\FacebookSDKException $e) {
					$this->facebook_planner_last_status(array(
						'status' 	=> 'exception',
						'msg' 		=> $e,
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					$ret['opMsg'] = $plugin_url_.'<br/>' . 'Error getting long-lived access token: ' . $e->getMessage();
					return $ret;
				}
				//echo '<h3>Long-lived</h3>';
				//var_dump($accessToken->getValue());
			}

			// SUCCESS
			// User is logged in with a long-lived access token.
			// You can redirect them to a members-only page.

			$this->facebook_planner_last_status(array(
				'status' 	=> 'success',
				'msg' 		=> 'Successfull login.',
				'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
				'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
				'from_line' => __LINE__,
			));

			// saving offline session into DB
			update_option('psp_fb_planner_token', $accessToken);
			$facebook->setDefaultAccessToken($accessToken);

			$ret['opMsg'] = 'Successfull login.';
			$ret['opStatus'] = true;
			$ret['facebook'] = $facebook;
			$ret['accessToken'] = $accessToken;
			return $ret;
		}

		// Facebook: is user loggedin? if not, you need re-authorization
		public function facebook_is_loggedin( $pms=array() ) {
			$fbAuth = $this->facebook_do_authorization($pms);
			$validAuth = $fbAuth['validAuth'];
			return isset($validAuth) && $validAuth ? true : false;
		}

		// Facebook: get user profile
		public function facebook_get_user_profile( $pms=array() ) {
			$result = array();
			$ret = array(
				'opStatus'		=> false,
				'opMsg'			=> '',
				'result'		=> $result,
			);

			$fbAuth = $this->facebook_do_authorization($pms);
			$validAuth = $fbAuth['validAuth'];
			$is_loggedin = isset($validAuth) && $validAuth ? true : false;

			$ret['opStatus'] = $fbAuth['validAuth'];
			$ret['opMsg'] = $fbAuth['html'];
			$ret['result'] = $fbAuth['user_profile'];

			if ( ! $is_loggedin ) {
				//$ret['opMsg'] = 'User is not loggedin or app authorized yet.';
			}
			return $ret;
		}

		// Facebook: get user pages / groups
		public function facebook_get_user_pages( $pms=array() ) {
			$def = $this->facebook_call_plugin_default();
			$pms = $this->facebook_call_default_params($pms);
			$pms = array_merge(array(
				'facebook'			=> null,
				'fb_details'		=> array(),
				'plugin_url'		=> $def['plugin_url'],
				'plugin_url_'		=> $def['plugin_url_'],
				'do_authorize'		=> false,
				'what'				=> 'pages' // pages | groups
			), $pms);
			extract($pms);

			$result = array();
			$ret = array(
				'opStatus'		=> false,
				'opMsg'			=> '',
				'result'		=> $result,
			);

			if ( $do_authorize ) {
				$is_loggedin = $this->facebook_is_loggedin($pms);
				if ( ! $is_loggedin ) {
					$ret['opMsg'] = 'User is not loggedin or app authorized yet.';
					return $ret;
				}
			}

			$fbReq = '';
			$logMsg = '';
			switch( $what ) {
				case 'pages':
					$fbReq = '/me/accounts';
					$logMsg = 'User Pages - ';
					break;

				case 'groups':
					$fbReq = '/me/groups';
					$logMsg = 'User Groups - ';
					break;
			}

			if (1) {
				try {
					// Returns a `Facebook\FacebookResponse` object
					$response = $facebook->get( $fbReq );
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
					$this->the_plugin->facebook_planner_last_status(array(
						'status' 	=> 'exception',
						'msg' 		=> $e,
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					$ret['opMsg'] = $plugin_url_.'<br/>' . $logMsg.'Graph returned an error: ' . $e->getMessage();
					return $ret;
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					$this->the_plugin->facebook_planner_last_status(array(
						'status' 	=> 'exception',
						'msg' 		=> $e,
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					$ret['opMsg'] = $plugin_url_.'<br/>' . $logMsg.'Facebook SDK returned an error: ' . $e->getMessage();
					return $ret;
				}
 
				$feedEdge = $response->getGraphEdge(); // Page 1
				$cc = 0;
				do {
					foreach ($feedEdge as $status) {
						$status = $status->asArray();

						$cond = 1;
						if ( 'pages' == $what ) {
							$cond = 1//'app page' == strtolower($status['category']) && (isset($status['perms'])
								&& in_array('CREATE_CONTENT', $status['perms']);
						}
						if ( $cond ) {
							$result[] = $status;
						}
					} // end foreach

					$feedEdge = $facebook->next($feedEdge); // Next Page
					$cc++;
				}
				while( $feedEdge );
			}

			$this->facebook_planner_last_status(array(
				'status' 	=> 'success',
				'msg' 		=> 'Successfull operation.',
				'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
				'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
				'from_line' => __LINE__,
			));

			$ret['opMsg'] = 'Successfull operation.';
			$ret['opStatus'] = true;
			$ret['result'] = $result;
			return $ret;
		}

		// Facebook: publish to wall (pages, groups, profile)
		public function facebook_publish( $pms=array() ) {
			$def = $this->facebook_call_plugin_default();
			$pms = $this->facebook_call_default_params($pms);
			$pms = array_merge(array(
				'facebook'			=> null,
				'fb_details'		=> array(),
				'plugin_url'		=> $def['plugin_url'],
				'plugin_url_'		=> $def['plugin_url_'],
				'do_authorize'		=> false,
				'wall'				=> '',
				'fields'			=> array(),
			), $pms);
			extract($pms);

			$result = array();
			$ret = array(
				'opStatus'		=> false,
				'opMsg'			=> '',
				'result'		=> $result,
			);

			if ( $do_authorize ) {
				$is_loggedin = $this->facebook_is_loggedin($pms);
				if ( ! $is_loggedin ) {
					$ret['opMsg'] = 'User is not loggedin or app authorized yet.';
					return $ret;
				}
			}

			// access token - if you want to post on a page as it's owner/admin - messager are in the page center as on the all
			// othewise they are displaye in a bottom right box named "visitors posts"
			$access_token = '';
			if ( isset($fields['access_token']) && ('' != $fields['access_token']) ) {
				$access_token = $fields['access_token'];
				unset( $fields['access_token'] );
			}

			if (1) {
				try {
					// Returns a `Facebook\FacebookResponse` object
					if ( '' != $access_token ) {
						$response = $facebook->post( $wall, $fields, $access_token );
					}
					else {
						$response = $facebook->post( $wall, $fields );
					}
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
					$this->facebook_planner_last_status(array(
						'status' 	=> 'exception',
						'msg' 		=> $e,
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					$ret['opMsg'] = $plugin_url_.'<br/>' . 'Graph returned an error: ' . $e->getMessage();
					return $ret;
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					$this->facebook_planner_last_status(array(
						'status' 	=> 'exception',
						'msg' 		=> $e,
						'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
						'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
						'from_line' => __LINE__,
					));

					$ret['opMsg'] = $plugin_url_.'<br/>' . 'Facebook SDK returned an error: ' . $e->getMessage();
					return $ret;
				}
			}

			$graphNode = $response->getGraphNode();

			$this->facebook_planner_last_status(array(
				'status' 	=> 'success',
				'msg' 		=> 'Posted successfull with id: ' . $graphNode['id'],
				'from_file' => str_replace($this->cfg['paths']['plugin_dir_path'], '', __FILE__),
				'from_func' => __FUNCTION__ != __METHOD__ ? __METHOD__ : __FUNCTION__,
				'from_line' => __LINE__,
			));

			$ret['opMsg'] = 'Posted successfull with id: ' . $graphNode['id'];
			$ret['opStatus'] = true;
			$ret['result'] = $graphNode['id'];
			return $ret;
		}


		/**
		 * Multi Keywords
		 */
		public function mkw_get_keywords( $str ) {
			if ( is_array($str) ) {
				return $str;
			}

			$str = trim($str);
			if ( '' == $str ) {
				return array();
			}

			$__ = explode("\n", $str);
			if ( is_array($__) ) {
				$__ = array_map('trim', $__);
				$__ = array_map('strtolower', $__);
				$__ = array_map('strip_tags', $__);
				$__ = array_map('stripslashes', $__);

				$__ = array_filter($__);
				$__ = array_unique($__);
				return $__;
			}
			return array();
		}

		public function mkw_get_main_keyword( $str ) {
			$__ = $this->mkw_get_keywords( $str );
			if ( empty($__) || ! is_array($__) ) {
				return '';
			}
			return $__[0];
		}

		public function get_psp_meta_default( $psp_meta=array() ) {
			if ( ! is_array($psp_meta) ) {
				$psp_meta = array();
			}

			if ( ! isset($psp_meta['mfocus_keyword']) || empty($psp_meta['mfocus_keyword']) ) {
				$psp_meta['mfocus_keyword'] = '';
				if ( isset($psp_meta['focus_keyword']) ) { // || empty($psp_meta['mfocus_keyword'])
					$psp_meta['mfocus_keyword'] = $psp_meta['focus_keyword']; //$focus_kw
				}
			}
			return $psp_meta;
		}

		public function get_psp_meta( $post, $current_taxseo=array() ) {
			if ( $this->__tax_istax($post) ) {
				$psp_meta = $this->__tax_get_post_meta( $current_taxseo, $post, 'psp_meta' );
			}
			else {
				$psp_meta = get_post_meta( $post, 'psp_meta', true);
			}

			$psp_meta = $this->get_psp_meta_default( $psp_meta );

			return $psp_meta;
		}

		public function fk_missing_message( $str, $type='short', $markup=true ) {
			$str = trim($str);
			if ( '' == $str ) {
				if ( 'short' == $type ) {
					$str = __('missing focus keyword', $this->localizationName);
				}
				else {
					$str = __('missing focus keyword - you must create one...', $this->localizationName);
				}
				
				if ( $markup) {
					$str = '<span style="color: red;">' . $str . '</span>';
				}
			}
			return $str;
		}


		/**
		 * Social Stats | Providers
		 */
		public function social_get_providers( $pms=array() ) {
			$pms = array_replace_recursive(array(), $pms);
			extract( $pms );

			$prv = array(
				// title, color, items_label : mandatory for all providers
				'facebook'		=> array(
					'title'			=> __('Facebook', 'psp'),
					'color'			=> '#3c5b9b',
					'items_label'	=> __('shares', 'psp'),
					'counts'		=> array(
						'share_count'		=> array(
							'items_label'		=> __("shares", 'psp'),
							'icon'				=> 'facebook-icon.png',
						),
						'like_count'		=> array(
							'items_label'		=> __("likes", 'psp'),
							'icon'				=> 'facebook-like-icon.png',
						),
						'comment_count'		=> array(
							'items_label'		=> __("comments", 'psp'),
							'icon'				=> 'facebook-comments-icon.png',
						),
						//'click_count'		=> array(
						//	'items_label'		=> __("clicks", 'psp'),
						//	'icon'				=> 'facebook-icon.png',
						//),
					),
				),
				'twitter'		=> array(
					'title'			=> __('Twitter', 'psp'),
					'color'			=> '#00aced',
					'items_label'	=> __('retweets', 'psp'),
				),
				'google'		=> array(
					'title'			=> __('Google +1', 'psp'),
					'color'			=> '#d23e2b',
					'items_label'	=> __('shares', 'psp'),
				),
				'pinterest'		=> array(
					'title'			=> __('Pinterest', 'psp'),
					'color'			=> '#ca4638',
					'items_label'	=> __('pins', 'psp'),
				),
				'stumbleupon'		=> array(
					'title'			=> __('Stumbleupon', 'psp'),
					'color'			=> '#3fbd46',
					'items_label'	=> __('views', 'psp'),
				),
				'linkedin'		=> array(
					'title'			=> __('Linkedin', 'psp'),
					'color'			=> '#007ab9',
					'items_label'	=> __('backlinks', 'psp'),
				),
				'delicious'		=> array(
					'title'			=> __('Delicious', 'psp'),
					'color'			=> '#2c2c2c',
					'items_label'	=> __('posts', 'psp'),
				),
				'buffer'		=> array(
					'title'			=> __('Buffer', 'psp'),
					'color'			=> '#2c2c2c',
					'items_label'	=> __('posts', 'psp'),
				),
				'reddit'		=> array(
					'title'			=> __('Reddit', 'psp'),
					'color'			=> '#2c2c2c',
					'items_label'	=> __('posts', 'psp'),
				),
				'flattr'		=> array(
					'title'			=> __('Flattr', 'psp'),
					'color'			=> '#2c2c2c',
					'items_label'	=> __('posts', 'psp'),
				),
			);

			return $prv;
		}

		public function social_get_allowed_providers( $pms=array() ) {
			$pms = array_replace_recursive(array(), $pms);
			extract( $pms );

			//2017-june not working anymore: 'twitter', 'delicious', 'digg', 'flattr', 'reddit'
			$def = array('facebook', 'google', 'pinterest', 'stumbleupon', 'linkedin', 'buffer');

			$prv = $this->social_get_providers();
			
			$selected = $this->get_theoption( $this->alias . '_social', true );
			$selected = is_array($selected) && isset($selected['services']) && is_array($selected['services'])
				? $selected['services'] : $def;
			$selected = array_unique( array_filter( $selected ) );

			$final = array_intersect_key($prv, array_flip($selected));

			foreach (array('twitter', 'delicious', 'digg', 'flattr', 'reddit') as $service) {
				if ( isset($final["$service"]) ) {
					unset( $final["$service"] );
				}
			}
			return $final;
		}

		public function social_get_stats( $pms=array() ) {
			$def = array_keys( $this->social_get_allowed_providers() );

			$pms = array_replace_recursive(array(
				'providers'				=> $def,
				'from'					=> 'listing',
				'cache_force_refresh'	=> false,
				'cache_life_time'		=> 600, // in seconds
				'website_url'			=> '',
				'postid'				=> 0,
			), $pms);
			extract( $pms );

			//:: DEBUG
			/*
			$__ = array(
				'https://www.instagram.com/',
				'http://facebook.com',
				'http://mashable.com',
				'http://themeforest.net',
				'http://www.stackoverflow.com',
			);
			shuffle($__); $website_url = $__[0];
			*/

			$the_db_cache = array();
			if ( 'dashboard' == $from ) {
				$the_db_cache = $this->get_theoption( "psp_dashboard_social_statistics" );
			}
			else if ( 'listing' == $from ) {
				$the_db_cache = get_post_meta( $postid, '_psp_social_stats', true );
			}
			else if ( 'toolbar' == $from ) {
				$the_db_cache = get_post_meta( $postid, 'psp_socialsharing_count', true );
			}

			// check if cache NOT expires 
			if (
				isset($the_db_cache['_cache_date'])
				&& ( time() <= ( $the_db_cache['_cache_date'] + $cache_life_time ) )
				&& $cache_force_refresh == false
			) {
				if ( in_array($from, array('listing', 'toolbar')) ) {
					if ( isset($the_db_cache['facebook']['share_count']) ) {
						$the_db_cache['facebook'] = $the_db_cache['facebook']['share_count'];
					}
					if ( isset($the_db_cache['google']) ) {
						$the_db_cache['plusone'] = $the_db_cache['google'];
					}
				}
				return $the_db_cache;
			}

			$db_cache = array();
			$db_cache['_cache_date'] = time();

			//:: Alexa rank
			if ( 'dashboard' == $from ) {
				$apiQuery = 'http://data.alexa.com/data?cli=10&dat=snbamz&url='. $website_url;
				$alexa_data = $this->social_get_remote( $apiQuery, false );
				$xml = simplexml_load_string($alexa_data);
				$json = json_encode($xml);
				$array = json_decode($json,TRUE);

				$db_cache['alexa'] = isset($array['SD'][1]['POPULARITY']["@attributes"]['TEXT'])
					? $array['SD'][1]['POPULARITY']["@attributes"]['TEXT'] : 0;
			}


			//:: Facebook
			if ( in_array('facebook', $providers) ) {
				// deactivated
				//$fql  = "SELECT url, normalized_url, share_count, like_count, comment_count, ";
				//$fql .= "total_count, commentsbox_count, comments_fbid, click_count FROM ";
				//$fql .= "link_stat WHERE url = '{$website_url}'";
				//$apiQuery = "https://api.facebook.com/method/fql.query?format=json&query=" . urlencode($fql);
				//$fb_data = $this->social_get_remote( $apiQuery );
	 			//$fb_data = isset($fb_data[0]) ? $fb_data[0] : array();

				// 2017-june new method
				$apiQuery = "http://graph.facebook.com/?fields=id,share,og_object{engagement{count},likes.summary(true).limit(0),comments.limit(0).summary(true)}&id=" . urlencode($website_url);
				$fb_data = $this->social_get_remote( $apiQuery );

				$share_count = isset($fb_data['share'], $fb_data['share']['share_count'])
						? (int) $fb_data['share']['share_count'] : 0;
				$comment_count = isset($fb_data['share'], $fb_data['share']['comment_count'])
						? (int) $fb_data['share']['comment_count'] : 0;
				$like_count = 0;
				if ( isset($fb_data['og_object']) ) {
					if ( empty($share_count) ) {
						$share_count = isset($fb_data['og_object']['engagement']['count'])
							? (int) $fb_data['og_object']['engagement']['count'] : 0;
					}
					if ( empty($comment_count) ) {
						$comment_count = isset($fb_data['og_object']['comments']['summary']['total_count'])
							? (int) $fb_data['og_object']['comments']['summary']['total_count'] : 0;
					}
					if ( empty($like_count) ) {
						$like_count = isset($fb_data['og_object']['likes']['summary']['total_count'])
							? (int) $fb_data['og_object']['likes']['summary']['total_count'] : 0;
					}
				}

				$share_count_ = (int) ( $share_count - $like_count - $comment_count );
				$share_count_ = $share_count_ > 0 ? $share_count_ : 0;

				$db_cache['facebook'] = array(
					'share_count' 		=> $share_count_,
					'comment_count' 	=> $comment_count,
					'like_count' 		=> $like_count,
					'click_count' 		=> 0
				);
			}


			//:: Twitter - 2017-june not working anymore - needs an api key!
			//if ( in_array('twitter', $providers) ) {
			//	$apiQuery = "http://urls.api.twitter.com/1/urls/count.json?url=" . $website_url;
			//	$apiQuery = "https://api.twitter.com/1.1/search/tweets.json?q=" . urlencode($website_url);
			//	$tw_data = (array) $this->social_get_remote( $apiQuery );

			//	$db_cache['twitter'] = isset($tw_data['count']) ? $tw_data['count'] : 0;
			//}


			//:: Google Plus
			if ( in_array('google', $providers) ) {
				$apiQuery = "https://plusone.google.com/_/+1/fastbutton?bsv&size=tall&hl=it&url=" . $website_url;
				$go_data = $this->social_get_remote( $apiQuery, false );

				require_once( $this->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
				if ( !empty($this->charset) ) {
					$html = pspphpQuery::newDocumentHTML( $go_data, $this->charset );
				}
				else {
					$html = pspphpQuery::newDocumentHTML( $go_data );
				}
				$go_data = $html->find("#aggregateCount")->text();

				$db_cache['google'] = $go_data;
			}


			//:: Pinterest
			if ( in_array('pinterest', $providers) ) {
				$apiQuery = "http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=" . $website_url;
				$pn_data = (array) $this->social_get_remote( $apiQuery );

				$db_cache['pinterest'] = isset($pn_data['count']) ? $pn_data['count'] : 0;
			}


			//:: StumbledUpon
			if ( in_array('stumbleupon', $providers) ) {
				$apiQuery = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=" . $website_url;
				$st_data = (array) $this->social_get_remote( $apiQuery );

				$db_cache['stumbleupon'] = isset($st_data['result']['views']) ? $st_data['result']['views'] : 0;
			}


			//:: LinkedIn
			if ( in_array('linkedin', $providers) ) {
				$apiQuery = "http://www.linkedin.com/countserv/count/share?format=json&url=" . $website_url;
				$ln_data = (array) $this->social_get_remote( $apiQuery );

				$db_cache['linkedin'] = isset($ln_data['count']) ? $ln_data['count'] : 0;
			}


			//:: Delicious - 2017-june not working anymore
			//if ( in_array('delicious', $providers) ) {
			//	$apiQuery = "http://feeds.delicious.com/v2/json/urlinfo/data?url=" . $website_url;
			//	$de_data = $this->social_get_remote( $apiQuery );
			//	$de_data = isset($de_data[0]) ? $de_data[0] : array();

			//	$db_cache['delicious'] = isset($de_data['total_posts']) ? $de_data['total_posts'] : 0;
			//}

			// Tumblr
			// api: http://www.tumblr.com/docs/en/api/v2#blog-likes
			// @info: needs an api key!
			
			// Digg
			// @info: no valid api found!
			
			// Xing
			// api: https://dev.xing.com/docs
			// @info: needs an api key! - can't find the number of likes/bookmarks!

			// Buffer
			if ( in_array('buffer', $providers) ) {
				$apiQuery = "https://api.bufferapp.com/1/links/shares.json?url=" . $website_url;
				$buffer_data = $this->social_get_remote( $apiQuery );

				$db_cache['buffer'] = isset($buffer_data['shares']) ? $buffer_data['shares'] : 0;
			}
			
			// Reddit - 2017-june not working anymore: seems to return invalid info!
			//if ( in_array('reddit', $providers) ) {
			//	$apiQuery = "http://www.reddit.com/api/info.json?url=" . $website_url;
			//	$reddit_data = $this->social_get_remote( $apiQuery );
			//	if ( isset($reddit_data['data']['children'][0]['data']) ) {
			//		$reddit_data = $reddit_data['data']['children'][0]['data'];
			//	}
			//	else {
			//		$reddit_data = array('score' => 0);
			//	}

			//	$db_cache['reddit'] = isset($reddit_data['score']) ? $reddit_data['score'] : 0;
			//}
			
			// Flattr - 2017-june not working anymore
			//if ( in_array('flattr', $providers) ) {
			//	$apiQuery = "https://api.flattr.com/rest/v2/things/lookup/?url=" . $website_url;
			//	$flattr_data = $this->social_get_remote( $apiQuery );

			//	if ( isset($flattr_data['message']) && $flattr_data['message'] != 'found' ) {
			//		$flattr_data['flattrs'] = 0;
			//	}

			//	$db_cache['flattr'] = isset($flattr_data['flattrs']) ? $flattr_data['flattrs'] : 0;
			//}

			//var_dump('<pre>', $db_cache , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			if ( !empty($db_cache) ) {
				foreach ($db_cache as $k => $v) {
					if ( $k == 'plusone' ) ;
					else if ( $k == 'facebook') {
						foreach ($v as $key => $value) {
							$db_cache["$k"]["$key"] = (int) $value;							
						}
					}
					else {
						$db_cache["$k"] = (int) $v;
					}
				}
			}

			// create a DB cache of this
			if ( 'dashboard' == $from ) {
				$this->save_theoption( 'psp_dashboard_social_statistics', $db_cache );
			}
			else if ( 'listing' == $from ) {
				update_post_meta( $postid, '_psp_social_stats', $db_cache );
			}
			else if ( 'toolbar' == $from ) {
				update_post_meta( $postid, 'psp_socialsharing_count', $db_cache );
			}

			if ( in_array($from, array('listing', 'toolbar')) ) {
				if ( isset($db_cache['facebook']['share_count']) ) {
					$db_cache['facebook'] = $db_cache['facebook']['share_count'];
				}
				if ( isset($db_cache['google']) ) {
					$db_cache['plusone'] = $db_cache['google'];
				}
			}
			return $db_cache;  
		}

		public function social_get_remote( $the_url, $parse_as_json=true ) { 
			$response = wp_remote_get($the_url, array(
				'user-agent' 	=> "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0",
				'timeout' 		=> 10
			));

            if ( is_wp_error( $response ) ) {
            	return array(
					'status' => 'invalid'
				);
            }
        	$body = wp_remote_retrieve_body( $response );
			
			if ( $parse_as_json == true ) {
				// trick for pinterest
				if( preg_match('/receiveCount/i', $body)){
					$body = str_replace("receiveCount(", "", $body);
					$body = str_replace(")", "", $body);
				}
	        	return json_decode( $body, true );
			}
			return $body;
		}

		public function social_get_htmlbox( $pms=array() ) {
			$pms = array_replace_recursive(array(
				'from'			=> 'listing',
				'img_src'		=> '',
				'ssKey'			=> '',
				'ssVal'			=> array(),
				'socialData'	=> array(),
				'postid'		=> 0,
				'only_counts'	=> array(), //facebook
			), $pms);
			extract( $pms );

			$ssValCounts = isset($ssVal['counts']) && is_array($ssVal['counts'])
				? $ssVal['counts'] : array('_count_' => $ssVal);

			if ( ! empty($only_counts) && is_array($only_counts) ) {
				if ( in_array($ssKey, $only_counts) ) {
					$ssValCounts = array('_count_' => $ssVal);
				}
			}

			foreach ($ssValCounts as $ssKey2 => $ssVal2) {
				$ssAlias = isset($ssVal['alias']) && ! empty($ssVal['alias']) ? $ssVal['alias'] : $ssKey;

				$ssItems = isset($socialData["$ssAlias"]) ? $socialData["$ssAlias"] : 0;
				$ssItems = isset($ssItems["$ssKey2"]) ? $ssItems["$ssKey2"] : $ssItems;
				$ssItems = (int) $ssItems;
				$ssItems = isset($socialData["$ssAlias"]) ? number_format($ssItems, 0) : '&ndash;';

				$ssColor = $ssVal['color'];
				$ssColor = isset($ssVal2["color"]) ? $ssVal2["color"] : $ssColor;

				$ssLabel = $ssVal['items_label'];
				$ssLabel = isset($ssVal2["items_label"]) ? $ssVal2["items_label"] : $ssLabel;

				$ssIcon = $ssAlias . '-icon.png';
				$ssIcon = isset($ssVal2["icon"]) ? $ssVal2["icon"] : $ssIcon;

				if ( 'listing' == $from ) {
					$html[] = '<div class="psp-social-status" style="color: ' . $ssColor . '">';
					$html[] = 	'<img src="' . $img_src . $ssIcon . '" class="psp-lists-icon">';
					$html[] = 	'<label>' . $ssLabel . '</label>';
					$html[] = 	'<span>' . $ssItems . '</span>';
					$html[] = '</div>';
				}
				else {
					$html[] = '<li style="color: ' . $ssColor . '">';
					$html[] = 	'<img src="' . $img_src . $ssIcon . '" class="psp-lists-icon">';
					$html[] = 	'<span>' . $ssItems . '</span>';
					$html[] = 	'<label>' . $ssLabel . '</label>';
					$html[] = '</li>';
				}
			} // end foreach

			return implode('', $html);
		}


		/**
		 * Redirect Types
		 */
		public function escape_mysql_regexp( $str ) {
			$str = preg_quote($str);
			$str = str_replace('\\', '\\\\', $str);
			//$str = str_replace('?', '\\?', $str);
			//$str = str_replace('\:', ':', $str);
			return $str;
		}

		public function get_redirect_types() {
			$redirect_type = array(
				301 => '301 Moved Permanently',
				302 => '302 Found (was: Moved Temporarily)',
				303 => '303 See Other',
				307 => '307 Temporary Redirect',
				//308 => 'Permanent Redirect',
				403 => '403 Forbidden',
				404 => '404 Page Not Found',
				410	=> '410 Gone - Content Deleted',
				451 => '451 Content Unavailable For Legal Reasons',
			);
			return $redirect_type;
		}

		public function get_redirect_status_codes() {
			/*$redirect_status = array(
				'is_ok'	=> 'status: Valid 200 OK http code',
				'invalid_string' => 'status: url string is invalid',
				'unable_to_resolve' => 'status: unable to resolve request',
				'is_temporary' => 'status: temporary http code',
				'is_error' => 'status: error http code',
				'is_301' => 'status: 301 http code',
				'is_not_ok' => 'status: not 200 OK http code',
			);*/
			$redirect_status = array(
				'valid'		=> 'Valid Status',
				'invalid' 	=> 'Invalid Status',
			);
			return $redirect_status;
		}

		public function get_redirect_groups() {
			$redirect_groups = array(
				'1'		=> 'Default',
				'2'		=> 'Posts - Slug Modified',
				'3'		=> 'Terms - Slug Modified',
			);
			return $redirect_groups;
		}

		public function get_redirect_type( $pms=array() ) {
			$def = array();
			if ( ! isset($pms['settings']) || empty($pms['settings']) || ! is_array($pms['settings']) ) {
				$def = $this->get_theoption( $this->alias . '_Link_Redirect', true );
			}
			$pms = array_replace_recursive(array(
				'settings'		=> $def,
				'row'			=> array(),
			), $pms);
			extract( $pms );

			$all = $this->get_redirect_types();

			$is_specific = false;
			$rd = isset($def['redirect_type']) && ! empty($def['redirect_type']) ? $def['redirect_type'] : '301';
			if ( isset($row['redirect_type']) && ! empty($row['redirect_type']) ) {
				$rd = $row['redirect_type'];
				$is_specific = true;
			}

			$title = isset($all["$rd"]) ? $all["$rd"] : $rd;
			if ( ! $is_specific ) {
				$title = '* ' . $title;
			}
			return array(
				'key'			=> $rd,
				'title'			=> $title,
				'is_specific'	=> $is_specific,
			);
		}

		public function is_valid_url( $url ) {
			//$url = filter_var($url, FILTER_SANITIZE_URL);

			$url = trim( $url );
			if ( '' == $url ) {
				return false;
			}

			$is_valid = filter_var($url, FILTER_VALIDATE_URL) === false ? false : true;
			return $is_valid;
		}

		public function get_clean_url( $url ) {
			//$url = filter_var($url, FILTER_SANITIZE_URL);
			//$url = preg_replace('/\+{2,}/imu', '+', $url);
			$url = trim( $url );
			if ( '' != $url ) {
				$url = rawurldecode( $url );
			}
			return $url;
		}

		public function is_404_valid() {
			if ( ! is_404() ) {
				return false;
			}

			//:: we are in a 404 error page, but we must be sure it's valid

			// Request URI
			$visitor_request_uri = $this->get_current_page_url(array());

			// escape if it's the cron!
			$is_valid_404 = preg_match('/doing_wp_cron/i', $visitor_request_uri) == false;
			if ( $is_valid_404 ) {
				return true;
			}
			return false;
		}

		public function is_all_404_redirect( $pms=array() ) {
			$def = array();
			if ( ! isset($pms['settings']) || empty($pms['settings']) || ! is_array($pms['settings']) ) {
				$def = $this->get_theoption( $this->alias . '_Link_Redirect', true );
			}
			$pms = array_replace_recursive(array(
				'settings'		=> $def,
			), $pms);
			extract( $pms );

			$selected = isset($settings['all_404_pages_to']) ? $settings['all_404_pages_to'] : '';
			$is_valid = in_array($selected, array('homepage', 'custom_url')) ? true : false;
			if ( $is_valid && ( 'custom_url' == $selected ) ) {
				$custom_url = isset($settings['all_404_pages_to_custom']) ? $settings['all_404_pages_to_custom'] : '';
				$custom_url = $this->get_clean_url( $custom_url );

				if ( ! $this->is_valid_url( $custom_url ) ) {
					$is_valid = false;
				}
			}

			if ( ! $is_valid ) {
				return false;
			}

			$redirect_to = false;
			if ( 'homepage' == $selected ) {
				$redirect_to = trailingslashit( get_home_url() );
				$redirect_to = $this->get_clean_url( $redirect_to );
			}
			else if ( 'custom_url' == $selected ) {
				$redirect_to = $custom_url;
			}
			return $redirect_to;
		}

		public function store_new_404_log() {
			if ( $this->is_admin === true ) {
				return false;
			}

			//module is inactive
			if ( ! $this->verify_module_status( 'monitor_404' ) ) {
				return false;
			}

			if ( ! $this->is_404_valid() ) {
				return false;
			}

			global $wpdb;

			//:: collect data for insert into DB

			// Request URI
			$visitor_request_uri = $this->get_current_page_url(array());

			// Referer
			$visitor_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

			// user agent
			$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

			if (1) {
				$table_name = $wpdb->prefix . "psp_monitor_404";

				// old ways of prepare: mysql_real_escape_string | $wpdb->_real_escape	
				$query = $wpdb->prepare(
					"INSERT IGNORE INTO $table_name (url, referrers, user_agents) VALUES (%s, %s, %s)",
						$visitor_request_uri,
						$visitor_referer,
						$user_agent
					);
				if ( $wpdb->query($query) == 0 ) {
					// record already exist, update hits
					$query_update = "UPDATE $table_name SET
						hits = hits+1,
						referrers = CONCAT(referrers, '\n$visitor_referer'),
						user_agents = CONCAT(user_agents, '\n$user_agent')
						WHERE url = '$visitor_request_uri';";
					$wpdb->query($query_update);
				}
				return true;
			}
			return false;
		}
	}
}
// __DIR__ - uses PHP 5.3 or higher
// require_once( __DIR__ . '/functions.php');
require_once( dirname(__FILE__) . '/functions.php');