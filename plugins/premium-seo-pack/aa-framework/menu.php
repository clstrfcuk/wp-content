<?php
/**
 * AA-Team - http://www.aa-team.com
 * ===============================+
 *
 * @package		pspAdminMenu
 * @author		Andrei Dinca
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('pspAdminMenu') != true) {
	class pspAdminMenu {
		
		/*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		private $the_menu = array();
		private $current_menu = '';
		private $ln = '';
		
		private $menu_depedencies = array();

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
            global $psp;
        	$this->the_plugin = $psp;
			$this->ln = $this->the_plugin->localizationName;
			
			// update the menu tree
			$this->the_menu_tree();
			
			return $this;
        }

		/**
	    * Singleton pattern
	    *
	    * @return pspDashboard Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
		
		private function the_menu_tree()
		{
			if ( isset($this->the_plugin->cfg['modules']['depedencies']['folder_uri'])
				&& !empty($this->the_plugin->cfg['modules']['depedencies']['folder_uri']) ) {
				$this->menu_depedencies['depedencies'] = array( 
					'title' => __( 'Plugin depedencies', 'psp' ),
					'url' => admin_url("admin.php?page=psp"),
					'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
					'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-dashboard"></i>'
				);
				
				$this->clean_menu();			
				$this->capabilities();
				return true;
			}

			$this->the_menu['dashboard'] = array( 
				'title' => __( 'Dashboard', 'psp' ),
				'url' => admin_url("admin.php?page=psp#dashboard"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-dashboard"></i>'
			);
			
			/*$this->the_menu['settings'] = array( 
				'title' => __( 'Main Settings', 'psp' ),
				'url' => admin_url("admin.php?page=psp#settings"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-mass_optimization"><span class="path1"></span><span class="path2"></span></span>',
			);*/
			
			
			$this->the_menu['monitoring'] = array( 
				'title' => __( 'Monitor', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-monitorize"></i>',
				'submenu' => array(
					'Google_Analytics' => array(
						'title' => __( 'Google Analytics', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Google_Analytics"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Google_Analytics']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-google_analytics"></i>',
						'submenu' => array(
							'Google_Analytics_Settings' => array(
								'title' => __( 'Google Analytics Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Google_Analytics")
							),
						)
					),
					
					'serp' => array(
						'title' => __( 'SERP Tracking', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_SERP"),
						'folder_uri' => $this->the_plugin->cfg['modules']['serp']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-serp"><span class="path1"></span><span class="path2"></span></span>',
						'submenu' => array(
							'SERP_Settings' => array(
								'title' => __( 'SERP Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#serp")
							),
						)
					),

					'google_pagespeed' => array(
						'title' => __( 'PageSpeed Insights', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_PageSpeedInsights"),
						'folder_uri' => $this->the_plugin->cfg['modules']['google_pagespeed']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-pagespeed_insights"></i>',
						'submenu' => array(
							'google_pagespeed_settings' => array(
								'title' => __( 'PageSpeed Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#google_pagespeed")
							),
						)
					),
					
					'monitor_404' => array(
						'title' => __( '404 Monitor', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_mass404Monitor"),
						'folder_uri' => $this->the_plugin->cfg['modules']['monitor_404']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-404"></i>',
					),

					'Link_Redirect' => array(
						'title' => __( 'Link Redirect', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Link_Redirect"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Link_Redirect']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-link_redirect"></i>',
						'submenu' => array(
							'Link_Redirect_Settings' => array(
								'title' => __( 'Link Redirect Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Link_Redirect")
							),
						)
					),

					'Alexa_Rank' => array(
						'title' => __( 'Alexa Rank', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Alexa_Rank"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Alexa_Rank']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-checks-alexa"></i>',
					),
				)
			);
			
			$this->the_menu['on_page_optimization'] = array( 
				'title' => __( 'On-page Optimization', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<i class="psp-icon-on_page_optimization"></i>',
				'submenu' => array(
					'on_page_optimization' => array(
						'title' => __( 'Mass Optimization', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_massOptimization"),
						'folder_uri' => $this->the_plugin->cfg['modules']['on_page_optimization']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-mass_optimization"><span class="path1"></span><span class="path2"></span></span>',
						'submenu' => array(
							'on_page_optimization_Settings' => array(
								'title' => __( 'Mass Optimization Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#on_page_optimization")
							),
						)
					),
					
					/*'title_meta_format' => array(
						'title' => __( 'Title & Meta Format', 'psp' ),
						'url' => admin_url("admin.php?page=psp#title_meta_format"),
						'folder_uri' => $this->the_plugin->cfg['modules']['title_meta_format']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-title_meta"><span class="path1"></span><span class="path2"></span></span>',
					),*/
					
					'title_meta_format--__tab1' => array(
						'title' => __( 'Title & Meta Format', 'psp' ),
						'url' => admin_url("admin.php?page=psp#title_meta_format#tab:__tab1"),
						'folder_uri' => $this->the_plugin->cfg['modules']['title_meta_format']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-title_meta"><span class="path1"></span><span class="path2"></span></span>',
						'submenu' => array(
							'title_meta_format--__tab6' => array(
								'title' => __( 'Facebook Social Meta', 'psp' ),
								'url' => admin_url("admin.php?page=psp#title_meta_format#tab:__tab6"),
							),
							'title_meta_format--__tab7' => array(
								'title' => __( 'Twitter Cards', 'psp' ),
								'url' => admin_url("admin.php?page=psp#title_meta_format#tab:__tab7"),
							),
						)
					),
					
					'sitemap--__tab1' => array(
						'title' => __( 'Sitemap', 'psp' ),
						'url' => admin_url("admin.php?page=psp#sitemap#tab:__tab1"),
						'folder_uri' => $this->the_plugin->cfg['modules']['sitemap']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-sitemap"></i>',
					),
					
					'sitemap--__tab8' => array(
						'title' => __( 'Video Sitemap', 'psp' ),
						'url' => admin_url("admin.php?page=psp#sitemap#tab:__tab8"),
						'folder_uri' => $this->the_plugin->cfg['modules']['sitemap']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-sitemap"></i>',
					),
					
					'misc--__tab2' => array(
						'title' => __( 'SEO Slug Optimizer', 'psp' ),
						'url' => admin_url("admin.php?page=psp#misc#tab:__tab2"),
						'folder_uri' => $this->the_plugin->cfg['modules']['misc']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-slug_optimize"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
					),
					
					'seo_friendly_images--__images' => array(
						'title' => __( 'SEO Friendly Images', 'psp' ),
						'url' => admin_url("admin.php?page=psp#seo_friendly_images#tab:__images"),
						'folder_uri' => $this->the_plugin->cfg['modules']['seo_friendly_images']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-seo_friendly_images"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
					),
					
					'seo_friendly_images--__links' => array(
						'title' => __( 'SEO Friendly Links', 'psp' ),
						'url' => admin_url("admin.php?page=psp#seo_friendly_images#tab:__links"),
						'folder_uri' => $this->the_plugin->cfg['modules']['seo_friendly_images']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-seo_friendly_images"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
					),
					
					'local_seo' => array(
						'title' => __( 'Local SEO', 'psp' ),
						'url' => admin_url("admin.php?page=psp#local_seo"),
						'folder_uri' => $this->the_plugin->cfg['modules']['local_seo']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-local_seo"><span class="path1"></span><span class="path2"></span></span>',
					),
					
					'google_authorship' => array(
						'title' => __( 'Google Publisher', 'psp' ),
						'url' => admin_url("admin.php?page=psp#google_authorship"),
						'folder_uri' => $this->the_plugin->cfg['modules']['google_authorship']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-google_authorship"></i>',
					)
				)
			);
			
			$this->the_menu['off_page_optimization'] = array( 
				'title' => __( 'Off-page Optimization', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<i class="psp-icon-off_page_optimization"></i>',
				'submenu' => array(
					'Backlink_Builder' => array(
						'title' => __( 'Backlink Builder', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Backlink_Builder"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Backlink_Builder']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-backlink_builder"><span class="path1"></span><span class="path2"></span></span>',
					),
					
					'Social_Stats' => array(
						'title' => __( 'Social Stats', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Social_Stats"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Social_Stats']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-social_stats"></i>',
						'submenu' => array(
							'Social_Stats--pspsocialstats' => array(
								'title' => __( 'Social Stats Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Social_Stats#pspsocialstats")
							),
							'Social_Stats--pspsocialsharing' => array(
								'title' => __( 'Social Toolbars Sharing', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Social_Stats#pspsocialsharing")
							),
						)
					),
					
					'Link_Builder' => array(
						'title' => __( 'Link Builder', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_Link_Builder"),
						'folder_uri' => $this->the_plugin->cfg['modules']['Link_Builder']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-Link_Builder"><span class="path1"></span><span class="path2"></span></span>',
						'submenu' => array(
							'Link_Builder_Settings' => array(
								'title' => __( 'Link Builder Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#Link_Builder")
							),
						)
					),
				)
			);

			$this->the_menu['misc--__tab1'] = array( 
				'title' => __( 'Miscellaneous', 'psp' ),
				'url' => admin_url("admin.php?page=psp#misc#tab:__tab1"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-miscellaneous"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
			);

			$this->the_menu['advanced_setup'] = array( 
				'title' => __( 'Advanced Setup', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<span class="psp-icon-advanced_setup"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
				'submenu' => array(
					'file_edit' => array(
						'title' => __( 'File Editor', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_massFileEdit"),
						'folder_uri' => $this->the_plugin->cfg['modules']['file_edit']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-files_edit"></i>',
					),

					'W3C_HTMLValidator' => array(
						'title' => __( 'W3C Validator', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_HTMLValidator"),
						'folder_uri' => $this->the_plugin->cfg['modules']['W3C_HTMLValidator']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-W3C"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
					),

					'misc--__tab3' => array(
						'title' => __( 'SEO Insert Code', 'psp' ),
						'url' => admin_url("admin.php?page=psp#misc#tab:__tab3"),
						'folder_uri' => $this->the_plugin->cfg['modules']['misc']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-miscellaneous"></i>',
					),

					'facebook_planner' => array(
						'title' => __( 'Facebook Planner', 'psp' ),
						'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_facebook_planner"),
						'folder_uri' => $this->the_plugin->cfg['modules']['facebook_planner']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-facebook_planner"></i>',
						'submenu' => array(
							'fb_planner_settings' => array(
								'title' => __( 'FB Planner Settings', 'psp' ),
								'url' => admin_url("admin.php?page=psp#facebook_planner")
							),
						)
					),

                    'tiny_compress' => array(
                        'title' => __( 'Tiny Compress', 'psp' ),
                        'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_tiny_compress"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['tiny_compress']['folder_uri'],
                        'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-tiny_compress"></i>',
                        'submenu' => array(
                            'Tiny_Compress_Settings' => array(
                                'title' => __( 'Tiny Compress Settings', 'psp' ),
                                'url' => admin_url("admin.php?page=psp#tiny_compress")
                            ),
                        )
                    ),
					
                    'Minify' => array(
                        'title' => __( 'Minify', 'psp' ),
                        'url' => admin_url("admin.php?page=psp#Minify"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['Minify']['folder_uri'],
                        'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-minify"></i>',
                    ),

				)
			);
			
			$this->the_menu['general'] = array( 
				'title' => __( 'Plugin Settings', 'psp' ),
				'url' => "#",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<i class="psp-icon-plugin_settings"></i>',
				'submenu' => array(
					'modules_manager' => array(
						'title' => __( 'Modules Manager', 'psp' ),
						'url' => admin_url("admin.php?page=psp#modules_manager"),
						'folder_uri' => $this->the_plugin->cfg['modules']['modules_manager']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-modules_manager"></i>',
					),
					
					'capabilities' => array(
						'title' => __( 'Capabilities', 'psp' ),
						'url' => admin_url("admin.php?page=psp_capabilities"),
						'folder_uri' => $this->the_plugin->cfg['modules']['capabilities']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-capabilities"></i>',
					),
					
					'setup_backup' => array(
						'title' => __( 'Setup / Backup', 'psp' ),
						'url' => admin_url("admin.php?page=psp#setup_backup"),
						'folder_uri' => $this->the_plugin->cfg['modules']['setup_backup']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-setup_backup"></i>',
					),
					
					'server_status' => array(
						'title' => __( 'Server Status', 'psp' ),
						'url' => admin_url("admin.php?page=psp_server_status"),
						'folder_uri' => $this->the_plugin->cfg['modules']['server_status']['folder_uri'],
						'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-icon-server_status"></i>',
					),

                    'cronjobs' => array(
                        'title' => __( 'Plugin Cronjobs', $this->ln ),
                        'url' => admin_url("admin.php?page=psp#cronjobs"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['cronjobs']['folder_uri'],
                        'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-checks-cron"></i>',
                    ),
					
					/*'remote_support' => array(
						'title' => __( 'Remote Support', 'psp' ),
						'url' => admin_url("admin.php?page=psp_remote_support"),
						'folder_uri' => $this->the_plugin->cfg['modules']['remote_support']['folder_uri'],
						'menu_icon' => '<span class="' . ( $this->the_plugin->alias ) . '-icon-support"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
					),*/
				)
			);
			
			$this->the_menu['collapse_menu'] = array( 
				'title' => __( 'Collapse menu', 'psp' ),
				'url' => '#',
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => '<i class="' . ( $this->the_plugin->alias ) . '-checks-arrow-left"></i>',
		
			);

  

			$this->clean_menu();			
			$this->capabilities();
		}
		
		public function capabilities() {
			foreach ($this->the_menu as $k=>$v) { // menu
				if ( isset($v['submenu']) && !empty($v['submenu']) ) {
					foreach ($v['submenu'] as $sk=>$sv) { // submenu
						$module = $sk;
						if ( //!in_array($module, $this->the_plugin->cfg['core-modules']) &&
						!$this->the_plugin->capabilities_user_has_module($module) )
							unset($this->the_menu["$k"]['submenu']["$sk"]);
					}
				} else {
					$module = $k;
					if ( //!in_array($module, $this->the_plugin->cfg['core-modules']) &&
					!$this->the_plugin->capabilities_user_has_module($module) )
						unset($this->the_menu["$k"]);
				}
			}
			
			foreach ($this->the_menu as $k=>$v) { // menu
				if ( isset($v['submenu']) && empty($v['submenu']) ) {
					unset($this->the_menu["$k"]);
				}
			}
		}

		public function clean_menu() {
			foreach ($this->the_menu as $key => $value) {
				if( isset($value['submenu']) ){
					foreach ($value['submenu'] as $kk2 => $vv2) {
						$kk2orig = $kk2;
						// fix to support same module multiple times in menu
						$kk2 = substr( $kk2, 0, (($t = strpos($kk2, '--'))!==false ? $t : strlen($kk2)) );
  
						if( ($kk2 != 'synchronization_log')
							&& !in_array( $kk2, array_keys($this->the_plugin->cfg['activate_modules'])) ) {
							unset($this->the_menu["$key"]['submenu']["$kk2orig"]);
						}
					}
				}
			}

			foreach ($this->the_menu as $k=>$v) { // menu
				if ( isset($v['submenu']) && empty($v['submenu']) ) {
					unset($this->the_menu["$k"]);
				}
			}
		}

		public function show_menu( $pluginPage='' )
		{
			$plugin_data = psp_get_plugin_data(); //$this->the_plugin->details;
 
			$html = array();
			
			$html[] = '<aside class="' . ( $this->the_plugin->alias ) . '-sidebar">';
			$html[] = '<div class="' . ( $this->the_plugin->alias ) . '-title logo">';
			$html[] = '<img src="' . (  $this->the_plugin->cfg['paths']['freamwork_dir_url'] . 'images/logo.png' ) . '" id="' . ( $this->the_plugin->alias ) . '-full-logo" /> <span><i>V</i> ' . ( $plugin_data['version'] ) . '</span>';
			$html[] = '</div>';
			$html[] = '<img src="' . ( $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'logo-small.png' ) . '" style="display:none;" id="' . ( $this->the_plugin->alias ) . '-collapsed-logo" alt="Premium Seo Pack"/>';
			$html[] = '<div class="' . ( $this->the_plugin->alias ) . '-responsive-menu hide">Menu <i class="fa fa-bars" aria-hidden="true"></i></div>';	
			$html[] = '<nav class="' . ( $this->the_plugin->alias ) . '-nav">';
								
			if ( $pluginPage == 'depedencies' ) {
				$menu = $this->menu_depedencies;
				$this->current_menu = array(
					0 => 'depedencies',
					1 => 'depedencies'
				);
			} else {
				$menu = $this->the_menu;
			}

			//var_dump('<pre>',$this->current_menu ,'</pre>');
			//:: MENU
			$currentnav = '';
			if ( ! empty($this->current_menu) && is_array($this->current_menu) && count($this->current_menu) >= 2 ) {
				unset($this->current_menu[0]);
				$currentnav = implode('#', $this->current_menu);
			}
			$html[] = '<ul data-currentnav="' . $currentnav . '">';
			foreach ($menu as $key => $value) {

					$_id = 'psp-nav-' . $key;
					$_sect = 'psp-section-' . $key;
					$_is_active = isset($this->current_menu[0]) && ( $key == $this->current_menu[0] ) ? 'active' : '';

					$html[] = '<li id="' . $_id . '" class="' . ($_sect.' '.$_is_active) . '">';
					
					if( $value['url'] == "#" ){
						$value['url'] = 'javascript: void(0)';
					}
					$html[] = '<a href="' . ( $value['url'] ) . '" ' . ( $key != 'collapse_menu' ? 'data-toggle="tipsy"' : '' ) . ' title="' . $value['title'] . '">'; 
					if( isset($value['menu_icon']) ){
						$html[] = $value['menu_icon'];
					}
					$html[] = '<span class="psp-sidebar-menu-item-title">' . $value['title'] . '</span>';
					$html[] = '</a>';

					//:: SUB-MENU
					if( isset($value['submenu']) ){

						$_sect = 'psp-sub-menu';
						$_is_active = '';

						$html[] = '<ul class="' . ($_sect.' '.$_is_active) . '">';

						foreach ($value['submenu'] as $kk2 => $vv2) {
	
							$_id = 'psp-sub-nav-' . $kk2;
							$_sect = 'psp-section-' . $kk2;
							$_is_active = isset($this->current_menu[1]) && $kk2 == $this->current_menu[1] ? 'active' : '';

							$html[] = '<li id="' . $_id . '" class="' . ($_sect.' '.$_is_active) . '">';

							$html[] = '<a href="' . ( $vv2['url'] ) . '">';
							if( isset($vv2['menu_icon']) ){
								$html[] = $vv2['menu_icon'];
							}
							$html[] = $vv2['title'];
							$html[] = '</a>'; 

							//:: SUB-SUB-MENU
							if( isset($vv2['submenu']) ){
							
								$_sect = 'psp-sub-sub-menu';
								$_is_active = '';

								$html[] = '<ul class="' . ($_sect.' '.$_is_active) . '">';

								foreach ($vv2['submenu'] as $kk3 => $vv3) {

									$_id = 'psp-sub-sub-nav-' . $kk3;
									$_sect = 'psp-section-' . $kk3;
									$_is_active = isset($this->current_menu[2]) && $kk2 == $this->current_menu[2] ? 'active' : '';

									$html[] = '<li id="' . $_id . '" class="' . ($_sect.' '.$_is_active) . '">';

									$html[] = '<a href="' . ( $vv3['url'] ) . '">';
									$html[] = '<i class="psp-icon-submenu_icon"></i>';
									$html[] = $vv3['title'];
									$html[] = '</a>';

									$html[] = '</li>';
								} // end foreach

								$html[] = '</ul>';
							}

							$html[] = '</li>';
						} // end foreach

						$html[] = '</ul>';
					}

					$html[] = '</li>';
			} // end foreach
			$html[] = '</ul>';

			$html[] = '</nav>';
    		$html[] = '</aside>';
			
			echo implode("\n", $html);
		}

		public function make_active( $section='' )
		{
			$this->current_menu = explode("|", $section);
			return $this;
		}
	}
}