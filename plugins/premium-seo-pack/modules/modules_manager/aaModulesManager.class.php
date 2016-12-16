<?php
/*

* Define class Modules Manager List

* Make sure you skip down to the end of this file, as there are a few

* lines of code that are very important.

*/
!defined('ABSPATH') and exit;
if (class_exists('aaModulesManger') != true) {
	class aaModulesManger
	{
		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*
		*/
		public $cfg = array();

		/*
		* Store some helpers config
		*/
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';

		private $settings = array();

		static protected $_instance;
		
		/**
	    * Singleton pattern
	    *
	    * @return Singleton instance
	    */
		static public function getInstance()
		{
			if (!self::$_instance) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct() //public function __construct($cfg)
		{
			global $psp;

			$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/modules_manager/';
			$this->module = $this->the_plugin->cfg['modules']['modules_manager'];

			$this->settings = $this->the_plugin->getAllSettings( 'array', 'modules_manager' );
			
			$this->cfg = $this->the_plugin->cfg;
		}
		
		public function printListInterface()
		{
			$html   = array();
			
			$html[] = '
			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text">' . __('Loading', 'psp') . '</div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>
			';

			$html[] = '<script type="text/javascript" src="' . $this->module_folder . 'app.class.js?' . ( time() ) . '" ></script>';
			
			
			$html[] = '<div class="psp-section-modules_manager">';
			
			$cc     = 0;

			$icon = array(
			    'Backlink_Builder'		=> 		'<i class="psp-icon-backlink_builder"></i>',
			    'capabilities'			=> 		'<i class="psp-icon-capabilities"></i>',
			    'Google_Analytics' 		=> 		'<i class="psp-icon-google_analytics"></i>',
			    'Link_Builder'			=> 		'<span class="psp-icon-Link_Builder"><span class="path1"></span><span class="path2"></span></span>',
			    'Link_Redirect'			=> 		'<i class="psp-icon-link_redirect"></i>',
			    'Minify'				=> 		'<i class="psp-icon-minify"></i>',
			    'Social_Stats'			=> 		'<i class="psp-icon-social_stats"></i>',
			    'W3C_HTMLValidator'		=> 		'<span class="psp-icon-W3C"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
			    'facebook_planner'		=> 		'<i class="psp-icon-facebook_planner"></i>',
			    'file_edit'				=> 		'<i class="psp-icon-files_edit"></i>',
			    'frontend'				=> 		'<span class="psp-icon-frontend"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                </span>',
			    'google_authorship'		=> 		'<i class="psp-icon-google_authorship"></i>',
			    'google_pagespeed'		=> 		'<i class="psp-icon-pagespeed_insights"></i>',
			    'local_seo'				=> 		'<span class="psp-icon-local_seo"><span class="path1"></span><span class="path2"></span></span>',
			    'misc'					=> 		'<span class="psp-icon-slug_optimize"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
			    'modules_manager'		=> 		'<i class="psp-icon-modules_manager"></i>',
			    'monitor_404'			=> 		'<i class="psp-icon-404"></i>',
			    'on_page_optimization'	=> 		'<span class="psp-icon-mass_optimization"><span class="path1"></span><span class="path2"></span></span>',
			    'remote_support'		=> 		'<span class="psp-icon-support"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
			    'rich_snippets'			=> 		'<i class="psp-icon-rich_snippets"></i>',
			    'seo_friendly_images'	=> 		'<span class="psp-icon-seo_friendly_images"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
			    'serp'					=> 		'<span class="psp-icon-serp"><span class="path1"></span><span class="path2"></span></span>',
			    'server_status'			=> 		'<i class="psp-icon-server_status"></i>',
			    'setup_backup'			=> 		'<i class="psp-icon-setup_backup"></i>',
			    'sitemap'				=> 		'<i class="psp-icon-sitemap"></i>',
			    'tiny_compress'			=> 		'<i class="psp-icon-tiny_compress"></i>',
			    'title_meta_format'		=> 		'<span class="psp-icon-title_meta"><span class="path1"></span><span class="path2"></span></span>',
			    'dashboard'				=> 		'<i class="psp-icon-dashboard"></i>'
			);

			foreach ($this->cfg['modules'] as $key => $value) {
				$module = $key;

				// Icon
				if ( !in_array($module, $this->cfg['core-modules'])
				&& !$this->the_plugin->capabilities_user_has_module($module) ) {
					continue 1;
				}
				
				$html[] = '<div class="psp_module-menu-wrapper ' . ($value['status'] == false ? 'psp_inactive' : '') . '">';
				$html[] = $icon[$key] != "" ? '<div class="psp_icon_wrapper">' . $icon[$key] . '</div>' : '';

				$html[] = 	'<div class="psp_module_title">' . $value[$key]['menu']['title'] . '</div>';
				
				// activate / deactivate plugin button
				if ($value['status'] == true) {
					if (!in_array($key, $this->cfg['core-modules'])) {
						$html[] = '<a href="#deactivate" class="psp_action_button psp_deactivate" rel="' . ($key) . '">Deactivate</a>';
					} else {
						$html[] = "<i>" . __("Core Modules, can't be deactivated!", 'psp') . "</i>";
					}
				} else {
					$html[] = '<a href="#activate" class="psp_action_button psp_activate" rel="' . ($key) . '">' . __('Activate', 'psp') . '</a>';
				}
				
				$html[] = 	'<a href="#" class="psp_read_more">' . ( __('read more', 'psp') ) . '</a>';
				$html[] = 	'<div class="psp_module_description">' . (isset($value[$key]['description']) ? $value[$key]['description'] : '') . '<a class="psp_close_description"><span class="psp-icon-close"></span></a></div>';
				$html[] = '</div>';
				$cc++;
			}
			
			$html[] = '<div class="clear"></div>';
			$html[] = '</div>';

			return implode("\n", $html);
		}
	}
}