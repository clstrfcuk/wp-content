<?php
/*
Plugin Name: ConvertPlug
Plugin URI: http://convertplug.in/
Author: Brainstorm Force
Author URI: https://www.brainstormforce.com
Version: 1.1.1
Description: Welcome to ConvertPlug - the easiest WordPress plugin to convert website traffic into leads. ConvertPlug will help you build email lists, drive traffic, promote videos, offer coupons and much more!
Text Domain: smile
*/
if( !defined( 'CP_VERSION' ) ) {
	define( 'CP_VERSION', '1.1.1');
}

if( !defined( 'CP_BASE_DIR' ) ) {
	define( 'CP_BASE_DIR', plugin_dir_path( __FILE__ ));
}

if( !defined( 'CP_DIR_NAME' ) ){
	define( 'CP_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
}

register_activation_hook( __FILE__, 'on_cp_activate' );
/*
* Function for activation hook
*
* @Since 1.0
*/
function on_cp_activate() {
	update_option( 'convert_plug_redirect', true );
	global $wp_version;
	$wp = '3.5';
	$php = '5.3.2';
	$test_php = '5.2.17';
    if ( version_compare( PHP_VERSION, $php, '<' ) )
        $flag = 'PHP';
    elseif
        ( version_compare( $wp_version, $wp, '<' ) )
        $flag = 'WordPress';
    else
        return;
    $version = 'PHP' == $flag ? $php : $wp;
    deactivate_plugins( basename( __FILE__ ) );
    wp_die('<p><strong>ConvertPlug </strong> requires <strong>'.$flag.'</strong> version <strong>'.$version.'</strong> or greater. Please contact your host.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=> TRUE ) );
}

if(!class_exists( 'Convert_Plug' )){
	// include Smile_Framework class
	require_once( 'framework/Smile_Framework.php' );

	class Convert_Plug extends Smile_Framework{
		public static $options = array();
		var $paths = array();
		function __construct(){

			$this->paths = wp_upload_dir();
			$this->paths['fonts'] 	= 'smile_fonts';
			$this->paths['fonturl'] = set_url_scheme( trailingslashit( $this->paths['baseurl'] ).$this->paths['fonts'] );
			add_action( 'admin_menu', array( $this,'add_admin_menu' ), 99 );
			add_action( 'admin_menu', array( $this,'add_admin_menu_rename' ), 9999 );
			add_filter( 'custom_menu_order', array($this,'cp_submenu_order') );
			add_action( 'wp_enqueue_scripts', array( $this,'enqueue_front_scripts' ), 100);
			add_action( 'admin_print_scripts', array( $this, 'cp_admin_css' ) );
			add_action( 'admin_footer', array( $this, 'smile_get_shortcodes' ) );
			add_action( 'admin_enqueue_scripts', array( $this,'cp_admin_scripts' ), 100);
			add_action( 'admin_enqueue_scripts', array( $this,'convert_admin_styles' ), 100);
			add_filter( 'bsf_core_style_screens', array( $this, 'cp_add_core_styles' ));
			add_action( 'admin_init', array($this,'cp_redirect_on_activation'));
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'cp_action_links' ), 10, 5);
			add_action( 'wp_ajax_cp_display_preview_modal', array( $this, 'cp_display_preview_modal' ) );
			add_action( 'wp_ajax_cp_display_preview_info_bar', array( $this, 'cp_display_preview_info_bar' ) );
			add_action( 'wp_ajax_cp_display_preview_slide_in', array( $this, 'cp_display_preview_slide_in' ) );
			add_action( 'plugins_loaded', array( $this, 'cp_load_textdomain' ) );
			add_action( 'the_content', array( $this, 'addContent' ) );
			require_once( 'mailers/config.php' );
			require_once( 'admin/ajax-actions.php' );
		}

		/**
		 * Add a class at the end of the post for after content trigger
		 *
		 * @since 1.0.3
		 */
		function addContent( $content ) {
			if( is_single() || is_page() ){
				$content .= '<span class="cp-load-after-post"></span>';
			}
			return $content;
		}

		/**
		 * Load plugin textdomain.
		 *
		 * @since 1.0.0
		 */
		function cp_load_textdomain() {
		  load_plugin_textdomain( 'smile', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
		}


		/**
		 * Handle style preview ajax request for modal
		 *
		 * @since 1.0.0
		 */
		function cp_display_preview_modal(){
			require_once( 'modules/modal/style-preview-ajax.php' );
			die();
		}

		/**
		 * Handle style preview ajax request for info bar
		 *
		 * @since 1.0.0
		 */
		function cp_display_preview_info_bar(){
			require_once( 'modules/info_bar/style-preview-ajax.php' );
			die();
		}

		/**
		 * Ajax Callback for slide in style preview
		 *
		 * @since 1.0.0
		 */
		function cp_display_preview_slide_in(){
			require_once( 'modules/slide_in/style-preview-ajax.php' );
			die();
		}

		/**
		 * Adds settings link in plugins action
		 * @param  array $actions
		 * @Since 1.0
		 * @return array
		 */
		function cp_action_links( $actions, $plugin_file ) {
		    static $plugin;

			if ( !isset($plugin) )
				$plugin = plugin_basename(__FILE__);
			if ( $plugin == $plugin_file ) {
				$settings = array('settings' => '<a href="' . admin_url( 'admin.php?page=convertplug&view=settings' ) . '">Settings</a>');
				$actions = array_merge($settings, $actions);
			}
			return $actions;
		}

		/* smile_get_shortcodes
		 * Creates shortcode object
		 */
		function smile_get_shortcodes() {

			$screen = get_current_screen();
			$screen_id = $screen->base;

			if( $screen_id == 'post-new' || $screen_id == 'post' ){
				echo '<script type="text/javascript">
				var shortcodes_buttons = new Object();';

				$shortcode_button_tags = array();

				$shortcode_button_tags = apply_filters('smile_shortcode_buttons', $shortcode_button_tags);

				if ( !empty($shortcode_button_tags) ) {
					foreach ( $shortcode_button_tags as $tag => $code ) {
						$code = implode(",",$code);
						echo "shortcodes_buttons['{$tag}'] = '{$code}';";
					}
				}
				echo '</script>';
			}
		}

		/*
		* Enqueue scripts and styles for insert shortcode popup
		* @Since 1.0
		*/
		function cp_admin_scripts($hook) {

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
			$screen = get_current_screen();
			$screen_id = $screen->base;

			if ( strpos( $screen_id , 'convertplug' ) !== false ) {
				wp_enqueue_style( 'cp-connects-icon', plugins_url('modules/assets/css/connects-icon.css',__FILE__) );
			}

			if( isset( $_GET['style-view'] ) && ( $_GET['style-view'] == "edit" || $_GET['style-view'] == 'variant' ) ) {
				wp_enqueue_script( 'cp-perfect-scroll-js', plugins_url( 'admin/assets/js/perfect-scrollbar.jquery.js', __FILE__ ), array( "jquery" ) );
				wp_enqueue_style( 'cp-perfect-scroll-style', plugins_url('admin/assets/css/perfect-scrollbar.min.css',__FILE__) );
				wp_enqueue_style( 'cp-animate', plugins_url( 'modules/assets/css/animate.css', __FILE__ ) );

				// ace editor files
				wp_enqueue_script( 'cp-ace', plugins_url( 'admin/assets/js/ace.js', __FILE__ ) , array( "jquery" ) );
				wp_enqueue_script( 'cp-ace-mode-css', plugins_url( 'admin/assets/js/mode-css.js', __FILE__ ) , array( "jquery" ) );
				wp_enqueue_script( 'cp-ace-mode-xml', plugins_url( 'admin/assets/js/mode-xml.js', __FILE__ ) , array( "jquery" ) );
				wp_enqueue_script( 'cp-ace-worker-css', plugins_url( 'admin/assets/js/worker-css.js', __FILE__ ) , array( "jquery" ) );
				wp_enqueue_script( 'cp-ace-worker-xml', plugins_url( 'admin/assets/js/worker-xml.js', __FILE__ ) , array( "jquery" ) );
			}
			if( $screen_id == 'convertplug_page_contact-manager' ) {
				wp_enqueue_style( 'cp-contacts', plugins_url('admin/contacts/css/cp-contacts.css',__FILE__) );
				if(isset($_GET['view']) && $_GET['view'] == 'analytics' ) {

					wp_enqueue_style( 'smile-bootstrap-datetimepicker', plugins_url('modules/assets/css/bootstrap-datetimepicker.min.css',__FILE__) );

					wp_enqueue_script( 'smile-moment-with-locales', plugins_url( 'modules/assets/js/moment-with-locales.js', __FILE__), false, false, true );
					wp_enqueue_script( 'smile-bootstrap-datetimepicker', plugins_url('modules/assets/js/bootstrap-datetimepicker.js',__FILE__), false, false, true );

					wp_enqueue_script( 'bsf-charts-js', plugins_url('admin/assets/js/chart.js',__FILE__), false, false, true );
					wp_enqueue_script( 'bsf-charts-bar-js', plugins_url('admin/assets/js/chart.bar.js',__FILE__), false, false, true );
					wp_enqueue_script( 'bsf-charts-donut-js', plugins_url('admin/assets/js/chart.donuts.js',__FILE__), false, false, true );
					wp_enqueue_script( 'bsf-charts-line-js', plugins_url('admin/assets/js/Chart.Line.js',__FILE__), false, false, true );
					wp_enqueue_script( 'bsf-charts-polararea-js', plugins_url('admin/assets/js/Chart.PolarArea.js',__FILE__), false, false, true );
					wp_enqueue_script( 'bsf-charts-scripts', plugins_url('admin/contacts/js/connect-analytics.js',__FILE__), false, false, true );
				}
				if( isset($_GET['page']) && $_GET['page'] == 'contact-manager'  ) {
					wp_enqueue_style( 'css-tootip', plugins_url('admin/assets/css/frosty.css',__FILE__) );
					wp_enqueue_script( 'convert-tooltip', plugins_url('admin/assets/js/frosty.js',__FILE__),array( 'jquery' ),'',true);
				}
				wp_enqueue_style( 'css-select2', plugins_url('admin/assets/select2/select2.min.css',__FILE__) );
				wp_enqueue_script( 'convert-select2', plugins_url('admin/assets/select2/select2.min.js',__FILE__), false, false, true );

				// sweet alert
				wp_enqueue_script( 'cp-swal-js', plugins_url('admin/assets/js/sweetalert.min.js',__FILE__), false, false, true );
				wp_enqueue_style( 'cp-swal-style', plugins_url('admin/assets/css/sweetalert.css',__FILE__) );
			}
		}

		/*
		* Enqueue font style
		* @Since 1.0
		*/
		function cp_admin_css(){
			wp_enqueue_style( 'cp-admin-css', plugins_url( 'admin/assets/css/font.css', __FILE__ ) );
		}

		/*
		* Enqueue scripts and styles on frontend
		* @Since 1.0
		*/
		function enqueue_front_scripts(){
			// $fonts = get_option('smile_fonts');
			// if(is_array($fonts))
			// {
			// 	foreach($fonts as $font => $info)
			// 	{
			// 		$style_url = $info['style'];
			// 		if(strpos($style_url, 'http://' ) !== false) {
			// 			wp_enqueue_style( 'bsf-'.$font,$info['style'] );
			// 		} else {
			// 			wp_enqueue_style( 'bsf-'.$font,trailingslashit($this->paths['fonturl']).$info['style'] );
			// 		}
			// 	}
			// }

			// nano scroll
			if(isset($_GET['hidemenubar'])){

				if( !is_user_logged_in() || ( defined( "LOGGED_IN_COOKIE" ) && empty( $_COOKIE[LOGGED_IN_COOKIE] ) ) ){
					wp_clear_auth_cookie();
					wp_logout();
					auth_redirect();
				}

				wp_enqueue_script( 'cp-nicescroll-js', plugins_url( 'admin/assets/js/jquery.nicescroll.min.js', __FILE__ ), array( "jquery" ) );
				wp_enqueue_script( 'cp-perfect-scroll-js', plugins_url( 'admin/assets/js/perfect-scrollbar.jquery.js', __FILE__ ), array( "jquery" ) );
			}

			wp_register_script( 'cp-detect-device', plugins_url( 'modules/assets/js/mdetect.js', __FILE__), array( 'jquery' ), null, null, true );
		}
		/*
		* Add main manu for ConvertPlug
		* @Since 1.0
		*/
		function add_admin_menu(){
			$page = add_menu_page( 'ConvertPlug Dashboard', 'ConvertPlug', 'administrator', 'convertplug', array($this,'admin_dashboard'), 'div' );
			add_action( 'admin_print_scripts-' . $page, array($this,'convert_admin_scripts'));
			add_action( 'admin_footer-'. $page, array($this,'cp_admin_footer') );


			if(defined('BSF_MENU_POS'))
				$required_place = BSF_MENU_POS;
			else
				$required_place = 200;

			if(function_exists('bsf_get_free_menu_position'))
				$place = bsf_get_free_menu_position($required_place,1);
			else
				$place = null;

			if( !defined ( 'BSF_MENU_POS' ) ) {
				define('BSF_MENU_POS', $place);
			}
			global $menu;
			$menuExist = false;
			foreach($menu as $item) {
				if(strtolower($item[0]) == strtolower('Brainstorm')) {
					$menuExist = true;
				}
			}

			$contacts = add_submenu_page(
				"convertplug",
				__("Connects","smile"),
				__("Connects","smile"),
				"administrator",
				"contact-manager",
				array($this, 'contacts_manager')
			);
			add_action( 'admin_footer-'. $contacts, array($this,'cp_admin_footer') );

			$resources_page = add_submenu_page(
				"convertplug",
				__("Resources","contacts_manager"),
				__("Resources","contacts_manager"),
				"administrator",
				"cp-resources",
				array($this, 'cp_resources')
			);
			add_action( 'admin_footer-'. $resources_page, array($this,'cp_admin_footer') );


			// section wise menu
			global $bsf_section_menu;
			$section_menu = array(
				'menu' => 'cp-resources',
				'is_down_arrow' => true
			);
			$bsf_section_menu[] = $section_menu;

			// $icon_manager = add_submenu_page(
			// 	"convertplug",
			// 	__("Icon Manager","smile"),
			// 	__("Icon Manager","smile"),
			// 	"administrator",
			// 	"bsf-font-icon-manager",
			// 	array($this, 'cp_icon_manager')
			// );
			// $AIO_Icon_Manager = new AIO_Icon_Manager;
			// add_action( 'admin_print_scripts-' . $icon_manager, array($AIO_Icon_Manager,'admin_scripts'));

			$google_manager = add_submenu_page(
				"convertplug",
				__("Google Font Manager","smile"),
				__("Google Fonts","smile"),
				"administrator",
				"bsf-google-font-manager",
				array($this, 'cp_font_manager')
			);
			$Ultimate_Google_Font_Manager = new Ultimate_Google_Font_Manager;
			add_action( 'admin_print_scripts-' . $google_manager, array($Ultimate_Google_Font_Manager,'admin_google_font_scripts'));
            add_action( 'admin_footer-'. $google_manager, array($this,'cp_admin_footer') );
		}

		function cp_font_manager() {
			$Ultimate_Google_Font_Manager = new Ultimate_Google_Font_Manager;
			$Ultimate_Google_Font_Manager->ultimate_font_manager_dashboard();
		}
		function add_admin_menu_rename(){
			global $menu, $submenu;
			if( isset( $submenu['convertplug'][0][0] ) ) {
			    $submenu['convertplug'][0][0] = 'Dashboard';
			}
		}
		function cp_icon_manager(){
			$AIO_Icon_Manager = new AIO_Icon_Manager;
			$AIO_Icon_Manager->icon_manager_dashboard();
		}

		function cp_resources() {
			$icon_manager = false;
			require_once(plugin_dir_path(__FILE__).'admin/resources.php');
		}

		function cp_submenu_order($menu_ord) {
			global $submenu;

		    if(!isset($submenu['convertplug']))
		    	return false;

		    $temp_resource = $temp_connect = $temp_google_font_manager = $temp_font_icon_manager = array();
		    foreach ($submenu['convertplug'] as $key => $cp_submenu) {
		    	if($cp_submenu[2] === 'cp-resources') {
		    		$temp_resource = $submenu['convertplug'][$key];
		    		unset($submenu['convertplug'][$key]);
		    	}
		    	if($cp_submenu[2] === 'contact-manager') {
		    		$temp_connect = $submenu['convertplug'][$key];
		    		unset($submenu['convertplug'][$key]);
		    	}
		    	if($cp_submenu[2] === 'bsf-font-icon-manager') {
		    		$temp_font_icon_manager = $submenu['convertplug'][$key];
		    		unset($submenu['convertplug'][$key]);
		    	}
		    	if($cp_submenu[2] === 'bsf-google-font-manager') {
		    		$temp_google_font_manager = $submenu['convertplug'][$key];
		    		unset($submenu['convertplug'][$key]);
		    	}
		    }

		    array_filter($submenu['convertplug']);

	    	if(!empty($temp_resource)) {
	    		array_push($submenu['convertplug'], $temp_resource);
	    	}
	    	if(!empty($temp_connect)) {
	    		array_push($submenu['convertplug'], $temp_connect);
	    	}
	    	if(!empty($temp_google_font_manager)) {
	    		array_push($submenu['convertplug'], $temp_google_font_manager);
	    	}
	    	if(!empty($temp_font_icon_manager)) {
	    		array_push($submenu['convertplug'], $temp_font_icon_manager);
	    	}

		    return $menu_ord;
		}

		function add_new_style_fun(){
			echo "<script>window.location = '?page=smile-modal-designer&style-view=donotdelete'; </script>";
		}

		/*
		Load admin styles on convert plug page only
		* @since 1.0
		*/
		function convert_admin_styles() {

			$screen = get_current_screen();
			$screen_id = $screen->base;

			if( isset( $_GET['style-view'] ) && $_GET['style-view'] == "new" && $screen_id == 'convertplug_page_smile-modal-designer') {
				wp_enqueue_style( 'smile-modal-css', plugins_url( 'modules/modal/assets/css/modal.min.css', __FILE__) );
			}
			if( isset( $_GET['style-view'] ) && $_GET['style-view'] == "new" && $screen_id == "convertplug_page_smile-info_bar-designer" ) {
				wp_enqueue_style( 'smile-info-bar-min', plugins_url( 'modules/info_bar/assets/css/info_bar.min.css', __FILE__) );
			}
			if( isset( $_GET['style-view'] ) && $_GET['style-view'] == "new" && $screen_id == "convertplug_page_smile-slide_in-designer" ) {
				wp_enqueue_style( 'smile-slide-in-min', plugins_url( 'modules/slide_in/assets/css/slide_in.min.css', __FILE__) );
			}

			if ( strpos( $screen_id , 'convertplug' ) !== false ) {
				if( isset( $_GET['developer'] ) ) {
					wp_enqueue_style( 'css-tootip', plugins_url('admin/assets/css/frosty.css',__FILE__) );
					wp_enqueue_style( 'convert-admin', plugins_url('admin/assets/css/admin.css',__FILE__) );
					wp_enqueue_style( 'convert-about', plugins_url('admin/assets/css/about.css',__FILE__) );
					wp_enqueue_style( 'jquery-ui-accordion', plugins_url('admin/assets/css/accordion.css',__FILE__) );
					wp_enqueue_style( 'css-select2', plugins_url('admin/assets/select2/select2.min.css',__FILE__) );
					wp_enqueue_style( 'cp-contacts', plugins_url('admin/contacts/css/cp-contacts.css',__FILE__) );
				} else {
					wp_enqueue_style( 'convert-admin-css', plugins_url('admin/assets/css/admin.min.css',__FILE__));
				}
			}
		}

		/*
		* Load scripts and styles on admin area of convertPlug
		* @Since 1.0
		*/
		function convert_admin_scripts(){
			wp_enqueue_script( 'jQuery' );
			wp_enqueue_style( 'thickbox' );
			if( isset( $_GET['developer'] ) ) {
				wp_enqueue_script( 'convert-tooltip', plugins_url('admin/assets/js/frosty.js',__FILE__),array( 'jquery' ),'',true);
				wp_enqueue_script( 'convert-accordion-widget', plugins_url('admin/assets/js/jquery.widget.min.js',__FILE__) );
				wp_enqueue_script( 'convert-accordion', plugins_url('admin/assets/js/accordion.js',__FILE__));
				wp_enqueue_script( 'convert-admin', plugins_url('admin/assets/js/admin.js',__FILE__));
				wp_enqueue_script( 'smile-jquery-modernizer', plugins_url('modules/modal/assets/js/jquery.shuffle.modernizr.js',__FILE__),'','',true);
				wp_enqueue_script( 'smile-jquery-shuffle', plugins_url('modules/modal/assets/js/jquery.shuffle.min.js',__FILE__),'','',true);
				wp_enqueue_script( 'smile-jquery-shuffle-custom', plugins_url('modules/modal/assets/js/shuffle-script.js',__FILE__),'','',true);
			} else {
				wp_enqueue_script( 'convert-admin-js', plugins_url('admin/assets/js/admin.min.js',__FILE__),'','',true);
			}
			if( ( isset( $_GET['style-view'] ) && ( $_GET['style-view'] == "edit" || $_GET['style-view'] == "variant" ) ) || !isset( $_GET['style-view'] ) ) {
				wp_enqueue_script( 'convert-select2', plugins_url('admin/assets/select2/select2.min.js',__FILE__));
			}
			// REMOVE WP EMOJI
			remove_action('wp_head', 'print_emoji_detection_script', 7);
			remove_action('wp_print_styles', 'print_emoji_styles');

			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			// $fonts = get_option('smile_fonts');
			// if(is_array($fonts))
			// {
			// 	foreach($fonts as $font => $info)
			// 	{
			// 		$style_url = $info['style'];
			// 		if(strpos($style_url, 'http://' ) !== false) {
			// 			wp_enqueue_style( 'bsf-'.$font,$info['style'] );
			// 		} else {
			// 			wp_enqueue_style( 'bsf-'.$font,trailingslashit($this->paths['fonturl']).$info['style'] );
			// 		}
			// 	}
			// }

		}
		/*
		*Add footer link for dashboar
		*Since 1.0.1
		*/
		function cp_admin_footer(){
			echo'<div id="wpfooter" role="contentinfo" class="cp_admin_footer">
				        <p id="footer-left" class="alignleft">
				        <span id="footer-thankyou">Thank you for using <a href="https://www.convertplug.com/" target="_blank" >Convertplug</a>.</span>   </p>
				    <p id="footer-upgrade" class="alignright">';
				       _e( "Version", "smile" ); echo ' '.CP_VERSION;
				        ;echo  '</p>
				    <div class="clear"></div>
				</div>';
		}


		/*
		* Load convertPlug dashboard
		* @Since 1.0
		*/
		function admin_dashboard(){
			require_once('admin/admin.php');
		}

		/*
		* Load convertPlug contacts manager
		* @Since 1.0
		*/
		function contacts_manager(){
			require_once('admin/contacts/admin.php');
		}

		function cp_add_core_styles($hooks) {

		    $contactsPage_hook = 'convertplug_page_contact-manager';
		    $cpmainPage_hook = 'toplevel_page_convertplug';
		    $modalPage_hook = 'convertplug_page_smile-modal-designer';
			$infoBarPage_hook = 'convertplug_page_smile-info_bar-designer';
		    array_push($hooks,$contactsPage_hook,$modalPage_hook,$infoBarPage_hook,$cpmainPage_hook);
		    return $hooks;
		}

		/*
		* Retrieve and store modules into the static variable $modules
		* @accepts    ->  array of modules in form of "Module Name" => "Module Main File"
		* @Since 1.0
		*/
		public static function convert_plug_store_module($modules_array){
			$result = false;
			if(!empty($modules_array)){
				self::$modules = $modules_array;
				$result = true;
			}
			return $result;
		}

		/*
		* Created default campaign on activation
		*
		* @Since 1.0
		*/
		function create_default_campaign(){

			// create default campaign
			$smile_lists = get_option('smile_lists');
			if(!$smile_lists) {
				$data = array();
				$list = array(
					"date"           => date("d-m-Y"),
					"list-name"      => "First",
					"list-provider"  => "Convert Plug",
					"list"           => "",
					"provider_list"  => ""
					);

				$data[] = $list;
				update_option('smile_lists',$data);
			}
		}

		/*
		* Redirect on activation hook
		*
		* @Since 1.0
		*/
		function cp_redirect_on_activation(){

			if( get_option('convert_plug_redirect') == true ) {
				update_option('convert_plug_redirect',false);
				$this->create_default_campaign();
				if(!is_multisite()) :
					wp_redirect(admin_url('admin.php?page=convertplug'));
				endif;
			}
		}
	}


	/*
	* Public Function to search style from multidimentional array
	* @accepts		-> array of styles and style name to be searched
	* @return		-> array key if style is found in the given array
	* @Since 1.0
	*/
	function search_style($array, $style)
	{
		foreach ($array as $key => $data)
		{
			$data_style = isset($data['style_id']) ? $data['style_id'] : '';
			if ($data_style == $style)
				return $key;
		}
	}
	/*
	* Public function for accepting requests for adding new module in the convert plug
	* @accepts    ->  array of modules in form of "Module Name" => "Module Main File"
	* @Since 1.0
	*/
	function convert_plug_add_module($modules_array){
		return Convert_Plug::convert_plug_store_module($modules_array);
	}

	function cp_editor_styles() {
    	add_editor_style( plugins_url('admin/assets/css/cp-editor.css',__FILE__) );
	}

	// load modules
	require_once('modules/config.php');

}
new Smile_Framework;
new Convert_Plug;

// load icon manager class
// require_once('framework/Ultimate_Icon_Manager.php');

// load google fonts class
require_once('framework/Ultimate_Font_Manager.php');

/// set global variables
global $cp_analytics_start_time,$cp_analytics_end_time,$colorPallet,$cp_default_dateformat;

$colorPallet = array (
		    		'rgba(26, 188, 156,1.0)',
		    		'rgba(46, 204, 113,1.0)',
		    		'rgba(52, 152, 219,1.0)',
		    		'rgba(155, 89, 182,1.0)',
		    		'rgba(52, 73, 94,1.0)',
		    		'rgba(241, 196, 15,1.0)',
		    		'rgba(230, 126, 34,1.0)',
		    		'rgba(231, 76, 60,1.0)',
					'rgba(236, 240, 241,1.0)',
					'rgba(149, 165, 166,1.0)'
);

$cp_analytics_end_time = current_time( 'd-m-Y');
$date = date_create($cp_analytics_end_time);
date_sub($date, date_interval_create_from_date_string('9 days'));
$cp_analytics_start_time = date_format($date, 'd-m-Y');

if ( get_magic_quotes_gpc() ) {
    $_POST      = array_map( 'stripslashes_deep', $_POST );
    $_GET       = array_map( 'stripslashes_deep', $_GET );
    $_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
    $_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
}
// bsf core
$bsf_core_version_file = realpath(dirname(__FILE__).'/admin/bsf-core/version.yml');
if(is_file($bsf_core_version_file)) {
	global $bsf_core_version, $bsf_core_path;
	$bsf_core_dir = realpath(dirname(__FILE__).'/admin/bsf-core/');
	$version = file_get_contents($bsf_core_version_file);
	if(version_compare($version, $bsf_core_version, '>')) {
		$bsf_core_version = $version;
		$bsf_core_path = $bsf_core_dir;
	}
}
add_action('init', 'bsf_core_load', 999);
if(!function_exists('bsf_core_load')) {
	function bsf_core_load() {
		global $bsf_core_version, $bsf_core_path;
		if(is_file(realpath($bsf_core_path.'/index.php'))) {
			include_once realpath($bsf_core_path.'/index.php');
		}
	}
}
add_filter('bsf_core_style_screens', 'cp_bsf_core_style_hooks');
function cp_bsf_core_style_hooks($hooks) {
	$resources_page_hook = 'convertplug_page_cp-resources';
	array_push($hooks, $resources_page_hook);
	return $hooks;
}
// BSF CORE commom functions
if(!function_exists('bsf_get_option')) {
	function bsf_get_option($request = false) {
		$bsf_options = get_option('bsf_options');
		if(!$request)
			return $bsf_options;
		else
			return (isset($bsf_options[$request])) ? $bsf_options[$request] : false;
	}
}
if(!function_exists('bsf_update_option')) {
	function bsf_update_option($request, $value) {
		$bsf_options = get_option('bsf_options');
		$bsf_options[$request] = $value;
		return update_option('bsf_options', $bsf_options);
	}
}
add_action( 'wp_ajax_bsf_dismiss_notice', 'bsf_dismiss_notice');
if(!function_exists('bsf_dismiss_notice')) {
	function bsf_dismiss_notice() {
		$notice = $_POST['notice'];
		$x = bsf_update_option($notice, true);
		echo ($x) ? true : false;
		die();
	}
}

add_action('admin_init', 'bsf_core_check',10);
if(!function_exists('bsf_core_check')) {
	function bsf_core_check() {
		if(!defined('BSF_CORE')) {
			if(!bsf_get_option('hide-bsf-core-notice'))
				add_action( 'admin_notices', 'bsf_core_admin_notice' );
		}
	}
}

if(!function_exists('bsf_core_admin_notice')) {
	function bsf_core_admin_notice() {
		?>
		<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(document).on( "click", ".bsf-notice", function() {
					var bsf_notice_name = $(this).attr("data-bsf-notice");
				    $.ajax({
				        url: ajaxurl,
				        method: 'POST',
				        data: {
				            action: "bsf_dismiss_notice",
				            notice: bsf_notice_name
				        },
				        success: function(response) {
				        	console.log(response);
				        }
				    })
				})
			});
		})(jQuery);
		</script>
		<div class="bsf-notice update-nag notice is-dismissible" data-bsf-notice="hide-bsf-core-notice">
            <p><?php _e( 'License registration and extensions are not part of plugin/theme anymore. Kindly download and install "BSF CORE" plugin to manage your licenses and extensins.', 'bsf' ); ?></p>
        </div>
		<?php
	}
}

if(isset($_GET['hide-bsf-core-notice']) && $_GET['hide-bsf-core-notice'] === 're-enable') {
	$x = bsf_update_option('hide-bsf-core-notice', false);
}

// end of common functions
