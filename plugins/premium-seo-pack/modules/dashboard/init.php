<?php
/*
* Define class pspDashboard
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspDashboard') != true) {
    class pspDashboard
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
		
		public $ga = null;
		public $ga_params = array();
		
		public $boxes = array();

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/dashboard/';
			//$this->module = $this->the_plugin->cfg['modules']['dashboard'];
			
			if (is_admin()) {
				add_action( "admin_print_scripts", array( &$this, 'admin_load_scripts') );
			}
			   
			// load the ajax helper
			if ( $this->the_plugin->is_admin === true ) {
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/dashboard/ajax.php' );
				new pspDashboardAjax( $this->the_plugin );
			}
			
			if ( $this->the_plugin->is_admin === true ) {
				// add the boxes
				$this->addBox( 'plugin_dashboard_msg', '', $this->plugin_dashboard_msg(), array(
					'size' => 'grid_4'
				) );
				
				$this->addBox( 'dashboard_links', '', $this->links(), array(
					'size' => 'grid_4'
				) );
				
				$this->addBox( 'social', 'Social Statistics', $this->social(), array(
					'size' => 'grid_4'
				) );
				
				$this->addBox( 'audience_overview', 'Audience Overview', $this->audience_overview(), array(
					'size' => 'grid_4'
				) );
				
				$this->addBox( 'support', 'Need AA-Team Support?', $this->support() );
				$this->addBox( 'changelog', 'Changelog', $this->changelog() );
			}
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
	    
		public function admin_load_scripts()
		{
			wp_enqueue_script( 'psp-DashboardBoxes', $this->module_folder . 'app.class.js', array(), '1.0', true );
		}
		
		public function getBoxes()
		{
			$ret_boxes = array();
			if( count($this->boxes) > 0 ){
				foreach ($this->boxes as $key => $value) { 
					$ret_boxes[$key] = $value;
				}
			}
 
			return $ret_boxes;
		}
		
		private function formatAsFreamworkBox( $html_content='', $atts=array() )
		{
			return array(
				'size' 		=> isset($atts['size']) ? $atts['size'] : 'grid_4', // grid_1|grid_2|grid_3|grid_4
	            'header' 	=> isset($atts['header']) ? $atts['header'] : false, // true|false
	            'toggler' 	=> false, // true|false
	            'buttons' 	=> isset($atts['buttons']) ? $atts['buttons'] : false, // true|false
	            'style' 	=> isset($atts['style']) ? $atts['style'] : 'panel-widget', // panel|panel-widget
	            
	            // create the box elements array
	            'elements' => array(
	                array(
	                    'type' => 'html',
	                    'html' => $html_content
	                )
	            )
			);
		}
		
		private function addBox( $id='', $title='', $html='', $atts=array() )
		{ 
			// check if this box is not already in the list
			if( isset($id) && trim($id) != "" && !isset($this->boxes[$id]) ){
				
				$box = array();
				
				$box[] = '<div class="psp-dashboard-status-box panel panel-default psp-panel psp-dashboard-box-' . ( $id ) . ' ' . ( isset($atts['size']) ? 'dashboard_' . $atts['size'] : '' ) . ' ' . ( isset($atts['noright']) ? 'dashboard_box_noright' : '' ) . '">';
				if( isset($title) && trim($title) != "" ){
					$box[] = 	'<div class="panel-heading psp-panel-heading">';
					$box[] = 		'<h2>' . ( $title ) . '</h2>';
					$box[] = 	'</div>';
				}
				$box[] = 	$html;
				$box[] = '</div>';
				
				$this->boxes[$id] = $this->formatAsFreamworkBox( implode("\n", $box), $atts );
				
			}
		}
		
		public function formatRow( $content=array() )
		{
			$html = array();
			
			$html[] = '<div class="psp-dashboard-status-box-row">';
			if( isset($content['title']) && trim($content['title']) != "" ){
				$html[] = 	'<h2>' . ( isset($content['title']) ? $content['title'] : 'Untitled' ) . '</h2>';
			}
			if( isset($content['ajax_content']) && $content['ajax_content'] == true ){
				$html[] = '<div class="psp-dashboard-status-box-content is_ajax_content">';
				$html[] = 	'{' . ( isset($content['id']) ? $content['id'] : 'error_id_missing' ) . '}';
				$html[] = '</div>';
			}
			else{
				$html[] = '<div class="psp-dashboard-status-box-content is_ajax_content">';
				$html[] = 	( isset($content['html']) && trim($content['html']) != "" ? $content['html'] : '!!! error_content_missing' );
				$html[] = '</div>';
			}
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
		public function plugin_dashboard_msg()
		{
			$html = array();
			
			$html[] = '<div class="panel-heading ' . ( $this->the_plugin->alias ) . '-panel-heading">';
			$html[] = 	'<h1>Dashboard</h1>';
			$html[] = 	'Dashboard Area - Here you will find useful shortcuts to different modules inside the plugin.';
			$html[] = 	''; // extra content here ...
			$html[] = '</div>';
			$html[] = '<div class="panel-body ' . ( $this->the_plugin->alias ) . '-panel-body ' . ( $this->the_plugin->alias ) . '-no-padding" >
						<a href="http://docs.aa-team.com/products/premium-seo-pack/" target="_blank" class="' . ( $this->the_plugin->alias ) . '-tab"><span class="psp-icon-support"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span> Documentation</a>
						<a href="http://codecanyon.net/user/aa-team/portfolio" target="_blank" class="' . ( $this->the_plugin->alias ) . '-tab"><i class="' . ( $this->the_plugin->alias ) . '-icon-heart"></i> More AA-Team Products</a>
					</div>';

			return implode("\n", $html);
		}
		
		public function support()
		{
			$html = array();
			$html[] = '<div class="panel-body psp-panel-body psp-changelog">';
			$html[] = '<a href="http://support.aa-team.com" target="_blank"><img src="' . ( $this->module_folder ) . 'assets/support_banner.jpg"></a>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		public function social()
		{
			$html = array();
			$html[] = '<div class="panel-body psp-panel-body">';
			$html[] = $this->formatRow( array( 
				'id' 			=> 'social_impact',
				'title' 		=> '',
				'html'			=> '',
				'ajax_content' 	=> true
			) );
			$html[] = '</div>';
 
			return implode("\n", $html);
		}
		
		public function audience_overview()
		{
			$html = array();
			$html[] = '<div class="panel-body psp-panel-body">';
			$html[] = '<div class="psp-audience-graph" id="psp-audience-visits-graph" data-fromdate="' . ( date('Y-m-d', strtotime("-1 week")) ) . '" data-todate="' . ( date('Y-m-d') ) . '"></div>';
			$html[] = '</div>';

			return  implode("\n", $html);
		}
		
		public function links()
		{
			$html = array();
			$html[] = '<div class="panel-body psp-panel-body psp-dashboard-icons">';
			$html[] = '<ul class="psp-summary-links">';
			
			// get all active modules
			foreach ($this->the_plugin->cfg['modules'] as $key => $value) {
 
				if( !in_array( $key, array_keys($this->the_plugin->cfg['activate_modules'])) ) continue;
				
				$module = $key;
				if ( //!in_array($module, $this->the_plugin->cfg['core-modules']) &&
				!$this->the_plugin->capabilities_user_has_module($module) ) {
					continue 1;
				}
				 
				$in_dashboard = isset($value[$key]['in_dashboard']) ? $value[$key]['in_dashboard'] : array();
				//var_dump('<pre>',$value[$key]['in_dashboard'], $key,'</pre>');  
				if( count($in_dashboard) > 0 ){
			
					$html[] = '
						<li>
							<a href="' . ( $in_dashboard['url'] ) . '">
								'. ( $value[$key]['menu']['icon'] ) .'
								<span class="text">' . ( $value[$key]['menu']['title'] ) . '</span>
							</a>
						</li>';
				}
			}
			
			$html[] = '</ul>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
		public function changelog()
		{
			$html = array();

			$html[] = '<div class="panel-body psp-panel-body psp-changelog">';
			$html[] = 	'<article>';
			$changelog_file = 	file_get_contents( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/changelog.txt' );

			$re = "/(##.*\\n)/"; 
			preg_match_all($re, $changelog_file, $matches);
			if( isset($matches[0]) && count($matches) > 0 ){
				foreach ($matches[0] as $str) {
					//$str = trim($str);
					$changelog_file = str_replace( $str, "<h3>" . ( $str ) . "</h3>", $changelog_file );
				}
			}
			
			$html[] = nl2br($changelog_file);
			$html[] = 	'</article>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
    }
}

// Initialize the pspDashboard class
$pspDashboard = new pspDashboard();
//$pspDashboard = pspDashboard::getInstance();