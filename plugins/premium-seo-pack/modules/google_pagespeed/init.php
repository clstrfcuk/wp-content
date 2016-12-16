<?php
/*
* Define class pspPageSpeedInsights
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspPageSpeedInsights') != true) {
    class pspPageSpeedInsights
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

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/google_pagespeed/';
			$this->module = $this->the_plugin->cfg['modules']['google_pagespeed'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			// load the ajax helper
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/google_pagespeed/ajax.php' );
			new pspPageSpeedInsightsAjax( $this->the_plugin );
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
    		if ( $this->the_plugin->capabilities_user_has_module('google_pagespeed') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('PageSpeed Insights', 'psp'),
		            __('PageSpeed Insights', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_PageSpeedInsights",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('google_pagespeed') ) {
				$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		public function moduleValidation() {
			$ret = array(
				'status'			=> false,
				'html'				=> ''
			);
			
			// find if user makes the setup
			$module_settings = $pagespeed_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . "_pagespeed" );

			$pagespeed_mandatoryFields = array(
				'developer_key'			=> false,
				'google_language'		=> false
			);
			if ( isset($pagespeed_settings['developer_key']) && !empty($pagespeed_settings['developer_key']) ) {
				$pagespeed_mandatoryFields['developer_key'] = true;
			}
			if ( isset($pagespeed_settings['google_language']) && !empty($pagespeed_settings['google_language']) ) {
				$pagespeed_mandatoryFields['google_language'] = true;
			}
			$mandatoryValid = true;
			foreach ($pagespeed_mandatoryFields as $k=>$v) {
				if ( !$v ) {
					$mandatoryValid = false;
					break;
				}
			}
			if ( !$mandatoryValid ) {
				$error_number = 1; // from config.php / errors key
				
				$ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use PageSpeed module, yet!' );
				return $ret;
			}
			$ret['status'] = true;
			return $ret;
		}
		
		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<!--link href="<?php echo $this->module_folder;?>assets/skins/minimal/blue.css" rel="stylesheet">
		<script src="<?php echo $this->module_folder;?>assets/icheck.js"></script-->

		<div class="<?php echo $this->the_plugin->alias; ?>">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content"> 
			
				<?php
				// show the top menu
				pspAdminMenu::getInstance()->make_active('monitoring|google_pagespeed')->show_menu();
				?>
				
				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main">
					
					<?php 
					echo psp()->print_section_header(
						$this->module['google_pagespeed']['menu']['title'],
						$this->module['google_pagespeed']['description'],
						$this->module['google_pagespeed']['help']['url']
					);
					?>
					
					<div class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel">
			
						<div class="panel-heading psp-panel-heading">
							<h2><?php _e('Analyze your website with PageSpeed', 'psp');?></h2>
						</div>
						
						<div class="panel-body <?php echo $this->the_plugin->alias; ?>-panel-body">
							
							<!-- Container -->
							<div class="psp-container clearfix">
			
								<!-- Main Content Wrapper -->
								<div id="psp-content-wrap" class="<?php echo $this->the_plugin->alias; ?>-pagespeed clearfix">
									
									<!-- Page detail -->
									<div id="psp-pagespeed-detail">
										<div id="psp-pagespeed-ajaxresponse"></div>
									</div>
									
									<?php 
									// find if user makes the setup
									$moduleValidateStat = $this->moduleValidation();
									if ( !$moduleValidateStat['status'] )
										echo $moduleValidateStat['html'];
									else{ 
									?>
		                        		
									<form class="psp-form" id="1" action="#save_with_ajax">
										<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
										<?php
										$settings = $this->the_plugin->getAllSettings( 'array', 'pagespeed' );
										$attrs = array(
											'id' 				=> 'pspPageSpeed',
											'show_header' 		=> true,
											'items_per_page' 	=> '10',
											'post_statuses' 	=> 'all',
											'columns'			=> array(
												'checkbox'	=> array(
													'th'	=>  'checkbox',
													'td'	=>  'checkbox',
												),

												'id'		=> array(
													'th'	=> __('ID', 'psp'),
													'td'	=> '%ID%',
													'width' => '40'
												),

												'title'		=> array(
													'th'	=> __('Title', 'psp'),
													'td'	=> '%title%',
													'align' => 'left'
												),

												'page_speed_desktop_score'	=> array(
													'th'	=> __('Desktop Score', 'psp'),
													'td'	=> '%desktop_score%',
													'width' => '130',
													'css' 	=> array(
														'padding' => '0px',
														'background' => '#fcfcfc'
													),
													'class' => 'psp_the_desktop_score'
												),
												
												'page_speed_mobile_score'	=> array(
													'th'	=> __('Mobile Score', 'psp'),
													'td'	=> '%mobile_score%',
													'width' => '130',
													'css' 	=> array(
														'padding' => '0px',
														'background' => '#fcfcfc'
													),
													'class' => 'psp_the_mobile_score'
												),
												
												'page_speed_details'	=> array(
													'th'	=> __('View report', 'psp'),
													'td'	=> '%button%',
													'option' => array(
														'value' => __('View Report', 'psp'),
														'action' => 'do_item_view_report',
														'color' => 'info'
													),
													'width' => '80'
												),
												
												/*
												'last_check_date'		=> array(
													'th'	=> __('Last Check date', 'psp'),
													'td'	=> '%last_check_date%',
													'width' => '160'
												),
												*/
												
												'optimize_btn' => array(
													'th'	=> __('Action', 'psp'),
													'td'	=> '%button%',
													'option' => array(
														'value' => __('Test PageSpeed', 'psp'),
														'action' => 'do_item_pagespeed_test',
														'color' => 'danger'
													),
													'width' => '80'
												),
											),
											'mass_actions' 	=> array(
													'speed_test_mass' => array(
														'value' => __('Mass PageSpeed test', 'psp'),
														'action' => 'do_speed_test_mass',
														'color' => 'info'
													)
												)
										);
										
										// if report type not both 
										if( isset($settings['report_type']) && $settings['report_type'] != "both" ){
											$removeWhat = 'desktop';
											if( $settings['report_type'] == 'desktop' ){
												$removeWhat = 'mobile';
											}
											unset($attrs['columns']['page_speed_' . ( $removeWhat ) . '_score']);
										}
											
										pspAjaxListTable::getInstance( $this->the_plugin )
											->setup( $attrs )
											->print_html();
							            ?>
							            </div>
						            </form>
						            <?php } ?>
					            </div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
<?php
		}
		
		/**
	    * Singleton pattern
	    *
	    * @return pspPageSpeedInsights Singleton instance
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

// Initialize the pspPageSpeedInsights class
$pspPageSpeedInsights = pspPageSpeedInsights::getInstance();