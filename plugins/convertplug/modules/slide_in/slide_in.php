<?php
require_once('functions/functions.php');
if(!class_exists('Smile_Slide_Ins')){
	class Smile_Slide_Ins extends Convert_Plug{
		public static $settings = array();
		public static $options = array();
		function __construct(){
			add_action( 'wp_enqueue_scripts',array($this,'enqueue_front_scripts' ), 100);
			add_action( 'admin_menu',array($this,'add_admin_menu_page' ), 999);
			add_action( 'wp_head',array($this,'load_customizer_scripts' ) );
			add_action( 'wp_footer', array( $this, 'load_slide_in_globally' ) );
			add_action( 'init', array( $this, 'register_theme_templates') );
			add_filter( 'admin_body_class', array( $this, 'cp_admin_body_class') );
		}

		function cp_admin_body_class( $classes ) {

			if( isset( $_GET['style-view']) && $_GET['style-view'] == "new" ){
				$classes = str_replace( "cp-add-new-style", "", $classes );
	        	$classes .= 'cp-add-new-style';
	        }
			return $classes;
		}

		function register_theme_templates(){
			$dir = plugin_dir_path( __FILE__ );
			$themes = glob($dir . 'themes/*.php');
			foreach( $themes as $theme ){
				require_once( $theme );
			}
		}

		function add_admin_menu_page(){
			$page = add_submenu_page(
				'convertplug',
				'Slide In Designer',
				'Slide In',
				'administrator',
				'smile-slide_in-designer',
				array($this,'slide_in_dashboard') );
			$obj = new parent;
			add_action( 'admin_print_scripts-' . $page, array($obj,'convert_admin_scripts'));
			add_action( 'admin_print_scripts-' . $page, array($this,'slide_in_admin_scripts'));
			add_action( 'admin_footer-'. $page, array($this,'cp_admin_footer') );
		}

		function slide_in_admin_scripts(){
			if( ( isset( $_GET['style-view'] ) && ( $_GET['style-view'] == "edit" || $_GET['style-view'] == "variant" ) ) || !isset( $_GET['style-view'] ) ) {
				wp_enqueue_style( 'smile-bootstrap-datetimepicker',	plugins_url( '../assets/css/bootstrap-datetimepicker.min.css', __FILE__ ) );
				wp_enqueue_script( 'smile-slide_in-receiver', 			plugins_url( 'assets/js/receiver.js',__FILE__) );
				wp_enqueue_script( 'smile-moment-with-locales', 	plugins_url( '../assets/js/moment-with-locales.js',__FILE__) );
				wp_enqueue_script( 'smile-bootstrap-datetimepicker',plugins_url( '../assets/js/bootstrap-datetimepicker.js', __FILE__ ) );
				wp_enqueue_style( 'cp-contacts', 					plugins_url( '../../admin/contacts/css/cp-contacts.css', __FILE__ ) );
				wp_enqueue_media();
				wp_enqueue_script( 'smile-slide_in-importer', 			plugins_url( '../assets/js/admin-media.js',__FILE__),array( 'jquery' ),'',true);


			}

			if( isset($_GET['style-view']) && $_GET['style-view'] == 'analytics' ) {
				wp_enqueue_style( 'smile-bootstrap-datetimepicker',	plugins_url( '../assets/css/bootstrap-datetimepicker.min.css', __FILE__ ) );
				wp_enqueue_script( 'smile-moment-with-locales',		plugins_url( '../assets/js/moment-with-locales.js', __FILE__ ) );
				wp_enqueue_script( 'smile-bootstrap-datetimepicker',plugins_url( '../assets/js/bootstrap-datetimepicker.js', __FILE__ ) );
				wp_enqueue_style( 'css-select2',					plugins_url( '../../admin/assets/select2/select2.min.css', __FILE__ ) );
				wp_enqueue_script( 'convert-select2',				plugins_url( '../../admin/assets/select2/select2.min.js', __FILE__ ) );
				wp_enqueue_script( 'bsf-charts-js',					plugins_url( '../../admin/assets/js/chart.js', __FILE__ ) );
				wp_enqueue_script( 'bsf-charts-bar-js',				plugins_url( '../../admin/assets/js/chart.bar.js', __FILE__ ) );
				wp_enqueue_script( 'bsf-charts-donut-js',			plugins_url( '../../admin/assets/js/chart.donuts.js', __FILE__ ) );
				wp_enqueue_script( 'bsf-charts-line-js',			plugins_url( '../../admin/assets/js/Chart.Line.js', __FILE__ ) );
				wp_enqueue_script( 'bsf-charts-polararea-js',		plugins_url( '../../admin/assets/js/Chart.PolarArea.js', __FILE__ ) );
				wp_enqueue_script( 'bsf-style-analytics-js',		plugins_url( 'assets/js/style-analytics.js', __FILE__ ) );
			}
		}

		function slide_in_dashboard(){
			$page = isset($_GET['style-view']) ? $_GET['style-view'] : 'main';

			// load default option set
			require_once('functions/functions.options.php');

			switch($page){
				case 'main':
					require_once('views/main.php');
					break;
				case 'new':
					$default_google_fonts = array (
						"Lato",
						"Open Sans",
						"Libre Baskerville",
						"Montserrat",
						"Neuton",
						"Raleway",
						"Roboto",
						"Sacramento",
						"Varela Round",
						"Pacifico",
						"Bitter"
					);
					$gfonts = implode( ",", $default_google_fonts );
					require_once('functions/functions.php');
					if( function_exists( "cp_enqueue_google_fonts" ) ){
						cp_enqueue_google_fonts( $gfonts );
					}
					require_once('views/new-style.php');
					break;
				case 'edit':
					require_once('views/edit.php');
					break;
				case 'variant':
					require_once('views/variant.php');
					break;
				case 'analytics':
					require_once('views/analytics.php');
					break;
			}
		}

		function load_slide_in_globally(){

			if(!isset($_GET['hidemenubar'])){

				?>
	            <script type="text/javascript" id="test">
				jQuery('body').attr('onload','startclock()');
				function stopclock (){
				  if(timerRunning) clearTimeout(timerID);
				  timerRunning = false;
				  document.cookie="time=0";
				}

				function showtime () {
				  var now = new Date();
				  var my = now.getTime() ;
				  now = new Date(my-diffms) ;
				  document.cookie="time="+now.toLocaleString();
				  timerID = setTimeout('showtime()',10000);
				  timerRunning = true;
				}

				function startclock () {
				  stopclock();
				  showtime();
				}
				var timerID = null;
				var timerRunning = false;
				var x = new Date() ;
				var now = x.getTime() ;
				var gmt = <?php echo time(); ?> * 1000 ;
				var diffms = (now - gmt) ;
				</script>
	            <?php
				$slide_in_style = $slide_in_style_delay = $slide_in_cookie_delay = '';
				$live_styles = smile_get_live_slide_ins();
				$prev_styles = get_option('smile_slide_in_styles');
				$smile_variant_tests = get_option('slide_in_variant_tests');

				if(is_array($live_styles)){
					global $post;
					$slide_in_arrays = $live_styles;
					$post_id = ( !is_404() && !is_search() && !is_archive() && !is_home() ) ? @$post->ID : '';
					$category = get_queried_object_id();
					$cat_ids = wp_get_post_categories( $post_id );

					$post_type = get_post_type( $post );
					$taxonomies = get_post_taxonomies( $post );

					foreach( $slide_in_arrays as $key => $slide_in_array ){
						$display = false;
						$settings_encoded = '';

						$style_settings = array();
						$global_display = $pages_to_exclude = $cats_to_exclude = $exclusive_pages = $exclusive_cats = $show_for_logged_in = '';
						$settings_array = unserialize($slide_in_array[ 'style_settings' ]);
						foreach($settings_array as $key => $setting){
							$style_settings[$key] = apply_filters( 'smile_render_setting',$setting );
						}
						if(is_array($style_settings) && !empty($style_settings)){
							$slide_in_style = $style_settings[ 'style' ];

							$settings = unserialize( $slide_in_array[ 'style_settings' ] );
							$style_id = $slide_in_array[ 'style_id' ];

							$global_display		= isset($settings['global']) ? apply_filters('smile_render_setting', $settings['global']) : '';

							$exclude_from 		= isset($settings['exclude_from']) ? apply_filters('smile_render_setting', $settings['exclude_from']) : '';
							$exclude_from		= str_replace( "post-", "", $exclude_from );
							$exclude_from		= str_replace( "tax-", "", $exclude_from );
							$exclude_from		= str_replace( "special-", "", $exclude_from );
							$exclude_from 		= ( !$exclude_from == "" ) ? explode( ",", $exclude_from ) : '';

							$exclusive_on 		= isset($settings[ 'exclusive_on' ]) ? apply_filters('smile_render_setting', $settings[ 'exclusive_on' ]) : '';
							$exclusive_on		= str_replace( "post-", "", $exclusive_on );
							$exclusive_on		= str_replace( "tax-", "", $exclusive_on );
							$exclusive_on		= str_replace( "special-", "", $exclusive_on );
							$exclusive_on 		= ( !$exclusive_on == "" ) ? explode( ",", $exclusive_on ) : '';


							$exclude_cpt 		= isset($settings[ 'exclude_post_type' ]) ? apply_filters('smile_render_setting', $settings[ 'exclude_post_type' ]) : '';
							$exclude_cpt		= str_replace( "post-", "", $exclude_cpt );
							$exclude_cpt		= str_replace( "tax-", "", $exclude_cpt );
							$exclude_cpt		= str_replace( "special-", "", $exclude_cpt );
							$exclude_cpt 		= ( !$exclude_cpt == "" ) ? explode( ",", $exclude_cpt ) : '';

							$exclusive_cpt 		= isset($settings[ 'exclusive_post_type' ]) ? apply_filters('smile_render_setting', $settings[ 'exclusive_post_type' ]) : '';
							$exclusive_cpt		= str_replace( "post-", "", $exclusive_cpt );
							$exclusive_cpt		= str_replace( "tax-", "", $exclusive_cpt );
							$exclusive_cpt		= str_replace( "special-", "", $exclusive_cpt );
							$exclusive_cpt 		= ( !$exclusive_cpt == "" ) ? explode( ",", $exclusive_cpt ) : '';


							$exclude_post_type 	= isset($settings[ 'exclude_post_type' ]) ? apply_filters('smile_render_setting', $settings[ 'exclude_post_type' ]) : '';
							$exclude_post_type	= str_replace( "post-", "", $exclude_post_type );
							$exclude_post_type	= str_replace( "tax-", "", $exclude_post_type );
							$exclude_post_type	= str_replace( "special-", "", $exclude_post_type );
							$exclude_post_type 	= ( !$exclude_post_type == "" ) ? explode( ",", $exclude_post_type ) : '';

							$exclusive_tax 		= isset($settings[ 'exclusive_tax' ]) ? apply_filters('smile_render_setting', $settings[ 'exclusive_tax' ]) : '';
							$exclusive_tax		= str_replace( "post-", "", $exclusive_tax );
							$exclusive_tax		= str_replace( "tax-", "", $exclusive_tax );
							$exclusive_tax		= str_replace( "special-", "", $exclusive_tax );
							$exclusive_tax 		= ( !$exclusive_tax == "" ) ? explode( ",", $exclusive_tax ) : '';

							$exclusive_cats 	= isset($settings[ 'exclusive_cats' ]) ? apply_filters('smile_render_setting', $settings[ 'exclusive_cats' ]) : '';
							$exclusive_cats		= str_replace( "post-", "", $exclusive_cats );
							$exclusive_cats		= str_replace( "tax-", "", $exclusive_cats );
							$exclusive_cats		= str_replace( "special-", "", $exclusive_cats );
							$exclusive_cats 	= ( !$exclusive_cats == "" ) ? explode( ",", $exclusive_cats ) : '';

							$exclude_tax 		= isset($settings[ 'exclude_tax' ]) ? apply_filters('smile_render_setting', $settings[ 'exclude_tax' ]) : '';
							$exclude_tax		= str_replace( "post-", "", $exclude_tax );
							$exclude_tax		= str_replace( "tax-", "", $exclude_tax );
							$exclude_tax		= str_replace( "special-", "", $exclude_tax );
							$exclude_tax 		= ( !$exclude_tax == "" ) ? explode( ",", $exclude_tax ) : '';

							if( !$global_display ){
								if( !$settings['enable_custom_class'] ) {
									$settings['custom_class'] = 'priority_slidein';
									$settings['enable_custom_class'] = true;
								} else {
									$settings['custom_class'] = $settings['custom_class'].',priority_slidein';
								}
							}

							$show_for_logged_in = isset($settings['show_for_logged_in'] ) ? $settings['show_for_logged_in'] : '';

							$all_users = isset($settings['all_users'] ) ? $settings['all_users'] : '';
							$css = isset( $settings['custom_css'] ) ? urldecode($settings['custom_css']) : '';

							$settings = serialize( $settings );
							$settings_encoded 	= base64_encode( $settings );

							if( $all_users ){
								$show_for_logged_in = 0;
							}
						}

						if( $global_display ) {
							$display = true;
							if( is_404() ){
								if( is_array( $exclude_from ) && in_array( '404', $exclude_from ) ){
									$display = false;
								}
							}
							if( is_search() ){
								if( is_array( $exclude_from ) && in_array( 'search', $exclude_from ) ){
									$display = false;
								}
							}
							if( is_front_page() ){
								if( is_array( $exclude_from ) && in_array( 'front_page', $exclude_from ) ){
									$display = false;
								}
							}
							if( is_home() ){
								if( is_array( $exclude_from ) && in_array( 'blog', $exclude_from ) ){
									$display = false;
								}
							}
							if( is_author() ){
								if( is_array( $exclude_from ) && in_array( 'author', $exclude_from ) ){
									$display = false;
								}
							}
							if( is_archive() ){
								$obj = get_queried_object();
								$term_id = $obj->term_id;
								if( in_array( $term_id, $exclude_from ) ){
									$display = false;
								} elseif( is_array( $exclude_from ) && in_array( 'archive', $exclude_from ) ){
									$display = false;
								}
							}
							if( $post_id ) {
								if( is_array( $exclude_from ) && in_array( $post_id, $exclude_from ) ){
									$display = false;
								}
							}
							if( !empty( $cat_ids ) ) {
								foreach( $cat_ids as $cat_id ){
									if( is_array( $exclude_from ) && in_array( $cat_id, $exclude_from ) ){
										$display = false;
									}
								}
							}
							if( $post_type ) {
								if( is_array( $exclude_cpt ) && in_array( $post_type, $exclude_cpt ) ){
									foreach( $exclude_cpt as $cpt ){
										switch( $cpt ){
											case 'post':
												if( !is_archive() && !is_home() ){
													$display = false;
												}
												break;
											default:
												$display = false;
												break;
										}
									}
								}
							}
							if( !empty( $exclude_tax ) && is_array( $exclude_tax ) ){
								foreach( $exclude_tax as $taxonomy ) {
									$taxonomy = str_replace( "cp-", "", $taxonomy );
									//if( is_array( $taxonomies ) && in_array( $taxonomy, $taxonomies ) ){
									switch( $taxonomy ){
										case 'category':
											if( is_category() ){
												$display = false;
											}
											break;
										case 'post_tag':
											if( is_tag() ){
												$display = false;
											}
											break;
										default:
											if( is_archive( $taxonomy ) ){
												$display = false;
											}
											break;
									}
								}
							}
						} else {
							$display = false;

							if( is_array( $exclusive_on ) && !empty( $exclusive_on ) ){
								foreach( $exclusive_on as $page ){
									if( is_page( $page ) ){
										$display = true;
									}
								}
							}
							if( is_404() ){
								if( is_array( $exclusive_on ) && in_array( '404', $exclusive_on ) ){
									$display = true;
								}
							}
							if( is_search() ){
								if( is_array( $exclusive_on ) && in_array( 'search', $exclusive_on ) ){
									$display = true;
								}
							}
							if( is_front_page() ){
								if( is_array( $exclusive_on ) && in_array( 'front_page', $exclusive_on ) ){
									$display = true;
								}
							}
							if( is_home() ){
								if( is_array( $exclusive_on ) && in_array( 'blog', $exclusive_on ) ){
									$display = true;
								}
							}
							if( is_author() ){
								if( is_array( $exclusive_on ) && in_array( 'author', $exclusive_on ) ){
									$display = true;
								}
							}
							if( is_archive() ){
								if( is_archive() ){
									$obj = get_queried_object();
									$term_id = $obj->term_id;
									if( in_array( $term_id, $exclusive_on ) ){
										$display = true;
									} elseif( is_array( $exclusive_on ) && in_array( 'archive', $exclusive_on ) ){
										$display = true;
									}
								}
							}
							if( $post_id ) {
								if( is_array( $exclusive_on ) && in_array( $post_id, $exclusive_on ) ){
									$display = true;
								}
							}
							if( !empty( $cat_ids ) ) {
								foreach( $cat_ids as $cat_id ){
									if( is_array( $exclusive_on ) && in_array( $cat_id, $exclusive_on ) ){
										$display = true;
									}
								}
							}
							if( $post_type ) {
								if( is_array( $exclusive_cpt) && in_array( $post_type, $exclusive_cpt ) ){
									foreach( $exclusive_cpt as $cpt ){
										switch( $cpt ){
											case 'post':
												if( !is_archive() && !is_home() ){
													$display = true;
												}
												break;
											default:
												$display = true;
												break;
										}
									}
								}
							}
							if( !empty( $exclusive_tax ) ){
								foreach( $exclusive_tax as $taxonomy ) {
									$taxonomy = str_replace( "cp-", "", $taxonomy );
									//if( in_array( $taxonomy, $taxonomies ) ){
									switch( $taxonomy ){
										case 'category':
											if( is_category() ){
												$display = true;
											}
											break;
										case 'post_tag':
											if( is_tag() ){
												$display = true;
											}
											break;
										default:
											if( is_archive( $taxonomy ) ){
												$display = true;
											}
											break;
									}
								}
							}
						}

						if( !$show_for_logged_in ){
							if( is_user_logged_in() )
								$display = false;
						}

						if($display){
							//	Generate style ID
							$id = $slide_in_style . '-' . $style_id;

							//	Individual Style Path
							$file_name = '/assets/demos/'. $slide_in_style . '/' . $slide_in_style . '.min.css';
							$url = plugins_url( $file_name , __FILE__ );

							//	Check file exist or not - and append to the head
							echo '<link rel="stylesheet" id="'.$id.'" href="' . $url .'" type="text/css" media="all" />';

							echo '<!-- slide_in Shortcode -->';
							echo do_shortcode('[smile_slide_in style_id = '.$style_id.' style="'.$slide_in_style.'" settings_encoded="' . $settings_encoded . ' "][/smile_slide_in]');
							$css = isset( $settings['custom_css'] ) ? urldecode($settings['custom_css']) : '';
							apply_filters('cp_custom_css',$style_id, $css);
						}
					}
				}
			}
		}

		function load_customizer_scripts(){

			if( isset( $_GET['hidemenubar'] ) && isset( $_GET['module'] ) && $_GET['module'] == "slide_in" ){
				wp_enqueue_style( 'cp-perfect-scroll-style', plugins_url('../../admin/assets/css/perfect-scrollbar.min.css',__FILE__) );
				wp_enqueue_script( 'cp-perfect-scroll-js-back', plugins_url( '../../admin/assets/js/perfect-scrollbar.jquery.js', __FILE__ ), array( "jquery" ) );
				wp_enqueue_script( 'cp-common-functions-js' );
				wp_enqueue_script( 'cp-admin-customizer-js', plugins_url( 'assets/js/admin.customizer.js', __FILE__ ) );
				wp_enqueue_script( 'smile-slide_in-editor', plugins_url( '../assets/js/ckeditor/ckeditor.js', __FILE__), array('smile-customizer-js') );

			}

		}

		function enqueue_front_scripts(){

			//	Add 'Theme Name' as a class to <html> tag
			//	To provide theme compatibility
			$theme_name = wp_get_theme();
			$theme_name = $theme_name->get( "Name" );
			$theme_name = strtolower( preg_replace("/[\s_]/", "-", $theme_name ) );

			wp_localize_script( 'jquery', 'cp_active_theme', array( 'slug' => $theme_name ) );
			wp_localize_script( 'jquery', 'slide_in', array( 'demo_dir' => plugins_url('/assets/demos', __FILE__ ) ) );

			if( isset( $_GET['module'] ) && $_GET['module'] == "slide_in" ) {
				wp_register_script( 'smile-slide_in-common', plugins_url( 'assets/js/slide_in.common.js', __FILE__), array( 'jquery' ), null, true );
				wp_register_script( 'cp-common-functions-js', plugins_url( 'assets/js/functions-common.js', __FILE__ ), 'smile-slide_in-common', null, true );
			}

			$live_styles = smile_get_live_slide_ins();

			// if any style is live or slide_in is in live preview mode then only enqueue scripts and styles
			if( ( $live_styles && count($live_styles) > 0 ) || isset($_GET['hidemenubar']) ) {
				wp_enqueue_script( 'smile-tooltip-min', plugins_url('../../admin/assets/js/frosty.js',__FILE__) ,array( 'jquery' ),'',true);
				wp_enqueue_style( 'smile-slide_in', plugins_url( 'assets/css/slide_in.min.css', __FILE__) );
			}

			if( !isset( $_GET['hidemenubar'] ) && ( $live_styles && count($live_styles) > 0 ) ) {

				wp_register_script( 'smile-slide_in-common', plugins_url( 'assets/js/slide_in.common.js', __FILE__), array( 'jquery' ), null, true );
				wp_register_script( 'cp-common-functions-js', plugins_url( 'assets/js/functions-common.js', __FILE__ ), 'smile-slide_in-common', null, true );
				wp_enqueue_script( 'smile-slide_in', plugins_url( 'assets/js/slide_in.min.js', __FILE__), array( 'jquery' ), null, null, true );
				wp_localize_script( 'smile-slide_in', 'smile_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
			}

			wp_enqueue_script( 'smile-slide_in-common' );
			wp_localize_script( 'smile-slide_in-common', 'smile_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

			wp_enqueue_style( 'cp-perfect-scroll-style', plugins_url('../../admin/assets/css/perfect-scrollbar.min.css',__FILE__) );
			wp_enqueue_script( 'cp-perfect-scroll-js-back', plugins_url( '../../admin/assets/js/perfect-scrollbar.jquery.js', __FILE__ ), array( "jquery" ) );
		}

	}
	$Smile_Slide_Ins = new Smile_Slide_Ins;
}

if (!function_exists('smile_slide_in_popup')) {
	function smile_slide_in_popup( $atts, $content = null ) {
		$style = '';
		extract(shortcode_atts(array(
			'style' 				=> '',
			'style_name'			=> '',
		), $atts));
		$output = '';
		$func = 'slide_in_theme_'.$style;
		if( function_exists( $func ) ) {
			$output = $func( $atts );
		}
		echo $output;
	}
	add_shortcode('smile_slide_in', 'smile_slide_in_popup');
}


if (!function_exists('cp_slide_in_custom')) {
	function cp_slide_in_custom( $atts, $content = null ) {
		ob_start();
		$id = $display = '';
		extract(shortcode_atts(array(
			'id' 				=> '',
			'display'			=> '',
		), $atts));
		$live_styles = smile_get_live_slide_ins();
		$live_array = $settings = '';
		foreach( $live_styles as $key => $slide_in_array ){
			$style_id = $slide_in_array[ 'style_id' ];
			if( $id == $style_id )
			{
				$live_array = $slide_in_array;
				$settings = unserialize( $slide_in_array[ 'style_settings' ] );
				$settings_array = unserialize($slide_in_array[ 'style_settings' ]);
				foreach($settings_array as $key => $setting){
					$style_settings[$key] = apply_filters( 'smile_render_setting',$setting );
				}
				$slide_in_style = $style_settings[ 'style' ];
				$global = $style_settings[ 'global' ];
				if( $display ){
					$global = false;
				} else {
					$style_settings[ 'global' ] = true;
				}
				$style_settings[ 'display' ] = $display;
				$style_settings['custom_class'] .= isset( $style_settings['custom_class']) ? $style_settings['custom_class'].',cp-trigger-'.$style_id : 'cp-trigger-'.$style_id;
				$encode_settings = serialize( $style_settings );
				$settings_encoded = base64_encode( $encode_settings );

				//	Individual Style Path
				$file_name = '/assets/demos/'. $slide_in_style . '/' . $slide_in_style . '.min.css';
				$url = plugins_url( $file_name , __FILE__ );

				//	Check file exist or not - and append to the head
				echo '<link rel="stylesheet" id="'.$id.'" href="' . $url .'" type="text/css" media="all" />';

				echo '<span class="cp-trigger-shortcode cp-trigger-'.$style_id.' cp-'.$style_id.'">'.do_shortcode( $content ).'</span>';
				if( !$global ){
					echo do_shortcode('[smile_slide_in style_id = '.$style_id.' style="'.$slide_in_style.'" settings_encoded="' . $settings_encoded . ' "][/smile_slide_in]');
					$css = isset( $settings['custom_css'] ) ? urldecode($settings['custom_css']) : '';
					apply_filters('cp_custom_css',$style_id, $css);
				}
				break;
			}
		}
		return ob_get_clean();
	}
	add_shortcode('cp_slide_in', 'cp_slide_in_custom');
}