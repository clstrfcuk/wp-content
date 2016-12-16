<?php
/*
* Define class Capabilities List
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('aaCapabilities') != true) {
	class aaCapabilities
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
		
		private $wp_user_roles = array('super_admin', 'administrator', 'editor', 'author', 'contributor', 'subscriber');

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

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct() //public function __construct($cfg)
		{
			global $psp;

			$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/capabilities/';
			$this->module = $this->the_plugin->cfg['modules']['capabilities'];

			$this->settings = $this->the_plugin->getAllSettings( 'array', 'capabilities' );
			
			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			if ( $this->the_plugin->is_admin === true ) {
				// ajax handler
				add_action('wp_ajax_pspCapabilities_changeUser', array( &$this, 'change_user' ));
				add_action('wp_ajax_pspCapabilities_saveChanges', array( &$this, 'save_changes' ));
			}
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
			if ( $this->the_plugin->capabilities_user_has_module('capabilities') ) {
				add_submenu_page(
					$this->the_plugin->alias,
					$this->the_plugin->alias . " " . __('Capabilities', 'psp'),
					__('Capabilities', 'psp'),
					'read',
					$this->the_plugin->alias . "_capabilities",
					array($this, 'display_index_page')
				);
			}

			return $this;
		}

		public function display_meta_box()
		{
			//$this->printBoxInterface();
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		public function printBaseInterface()
		{
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		
		<div class="<?php echo $this->the_plugin->alias; ?>">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content">
					
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('general|capabilities')->show_menu();
			?>
			
				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main">
					
					<?php 
					echo psp()->print_section_header(
						$this->module['capabilities']['menu']['title'],
						$this->module['capabilities']['description'],
						$this->module['capabilities']['help']['url']
					);
					?>
					
					<div class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel psp-capabilities">
						
						<!-- Main loading box -->
						<div id="psp-main-loading">
							<div id="psp-loading-overlay"></div>
							<div id="psp-loading-box">
								<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
								<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
							</div>
						</div>
	
						<div class="panel-heading psp-panel-heading">
							<h2><?php _e('Capabilities', 'psp');?></h2>
						</div>
	
						<div class="panel-body <?php echo $this->the_plugin->alias; ?>-panel-body">
							
							<!-- Container -->
							<div class="psp-container clearfix">
			
								<!-- Main Content Wrapper -->
								<div id="psp-content-wrap" class="clearfix">
									
	                        		<div class="psp-panel">
	                        			
										<form class="psp-form" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php echo implode("\n", $this->roles_html(array(
												//'user_role'		=> 'administrator'
											))); ?>
									        </div>
									    </form>
									    
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
		
		public function roles_html( $pms = array() ) {
			extract($pms);

			$html   = array();

			$user_roles = $this->get_user_roles();
			if ( !isset($user_role) || empty($user_role) ) {
				$getValues = array_values($user_roles);
				$user_role = array_shift( $getValues );
			}

			$userModules = array();
			if ( !empty($user_role) ) {
				$capabilitiesRoles = $this->the_plugin->get_theoption('psp_capabilities_roles');
				if ( isset($capabilitiesRoles["$user_role"]) && !is_null($capabilitiesRoles["$user_role"]) && is_array($capabilitiesRoles["$user_role"])) {
					$userModules = $capabilitiesRoles["$user_role"];
				}
			}
			
			$html[] = '<div id="psp-list-table-posts">';
			$html[] = '<table class="psp-table" id="' . ($this->the_plugin->cfg['default']['alias']) . '-module-manager" style="border-collapse: collapse;border-spacing: 0;">';
			$html[] = '<thead>
						<tr>
							<th width="80" colspan=5 align="left">
								<div style="float:left; padding-top:7px;"><label for="psp-item-check-all"><input type="checkbox" id="psp-item-check-all">' . __('Select all modules', 'psp') . '</label></div>
								<div style="float:right;">
								<select name="psp-filter-user-roles" class="psp-filter-post_type">';
			foreach ($user_roles as $uk => $uv) {
				$html[] = '<option value="' . $uv . '" ' . ($uv == $user_role ? 'selected' : '') . '>' . ucfirst($uv) . '</option>';
			}
			$html[] = 				'</select>
								</div>
							</th>
						</tr>
					</thead>';
			$html[] = '<tbody>';
			$cc = 0; $nb_modules = count($this->the_plugin->cfg['modules']);
			foreach ($this->the_plugin->cfg['modules'] as $key => $value) {
				$icon = '';
				if (is_file($value["folder_path"] . $value[$key]['menu']['icon'])) {
					$icon = $value["folder_uri"] . $value[$key]['menu']['icon'];
				}
				
				if ( $cc == 0 ) $html[] = '<tr class="' . ($cc % 2 ? 'odd' : 'even') . '">'; // first row
				
				$td_cssClass = 'mod-core'; $td_content = '';
				//if (!in_array($key, $this->the_plugin->cfg['core-modules'])) {
					if (in_array($key, $userModules)) {
						$td_content = '<input type="checkbox" class="psp-item-checkbox" id="psp-item-checkbox-' . ( $key ) . '" name="psp-item-checkbox-' . ( $key ) . '" checked="checked"><label for="psp-item-checkbox-' . ( $key ) . '">';
						$td_cssClass = 'mod-active';
					} else {
						$td_content = '<input type="checkbox" class="psp-item-checkbox" id="psp-item-checkbox-' . ( $key ) . '" name="psp-item-checkbox-' . ( $key ) . '"><label for="psp-item-checkbox-' . ( $key ) . '">';
						$td_cssClass = 'mod-inactive';
					}
				//} else {
				//	$td_content = ""; // core module
				//}
				
				$html[] = 	'<td align="left" style="text-align:left;" class="' . $td_cssClass . '">';
				// activate / deactivate plugin button
				
				$html[] = $td_content;
				
				if (in_array($key, $this->the_plugin->cfg['core-modules'])) {
					$html[] = '<i>' . $value[$key]['menu']['title'] . '&nbsp;' . '(core module)</i>';
				} else {
					$html[] = $value[$key]['menu']['title'] . '</label>';
				}
				$html[] = 	'</td>';

				if ( $cc % 5 == 4 || $cc == ( $nb_modules - 1 ) ) { // 5 columns or is the last module so close row
					$html[] = '</tr>';
					if ( $cc < ( $nb_modules - 1) ) $html[] = '<tr class="' . ($cc % 2 ? 'odd' : 'even') . '">'; // not last module => open new row
				}
				
				$cc++;
			}
			$html[] = '</tbody>';
			$html[] = '</table>';
			$html[] = '</div>';
			
			$html[] = 	'<input type="button" value="' . __('Save changes', 'psp') . '" id="psp-save-changes" class="psp-form-button psp-form-button-success">';
			
			return $html;
		}
		
		private function get_user_roles( $translate = false ) {
			global $wp_roles;
			if ( !isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
			
			$current_user_role = $this->the_plugin->capabilities_current_user_role();
			$roles = $wp_roles->get_names();
			
			$pos_in_allowed = array_search($current_user_role, $this->wp_user_roles);
			$allowed_roles = array();
			if ($pos_in_allowed!==false) {
				$allowed_roles = array_slice($this->wp_user_roles, $pos_in_allowed + 1);
			}
			foreach ($roles as $key => $val) {
				if ( in_array($key, $allowed_roles) || !in_array($key, $this->wp_user_roles)) ;
				else unset($roles[$key]);
			}
			
			if ( $translate ) {
				foreach ($roles as $k => $v) {
					$roles[$k] = __($v, 'psp');
				}
				asort($roles);
				// translation to be implemented!
				return $roles;
			} else {
				$roles = array_keys($roles);
				asort($roles);
				return $roles;
			}
		}
		
		/**
		 * Ajax related
		 */
		public function change_user() {
			$request = array(
				'user_role'	=> isset($_REQUEST['user_role']) && !empty($_REQUEST['user_role']) ? $_REQUEST['user_role'] : ''
			);
			
			$pms = array(
				'user_role'		=> $request['user_role']
			);
			
			$html = implode("\n", $this->roles_html($pms));
			
			die( json_encode(array(
				'status' => 'valid',
				'html'	=> $html
			)) );
		}
		
		public function save_changes() {
			$request = array(
				'user_role'	=> isset($_REQUEST['user_role']) && !empty($_REQUEST['user_role']) ? $_REQUEST['user_role'] : '',
				'modules'	=> isset($_REQUEST['modules']) && !empty($_REQUEST['modules']) ? $_REQUEST['modules'] : array()
			);
			
			$pms = array(
				'user_role'		=> $request['user_role'],
				'modules'		=> $request['modules']
			);

			$capabilitiesRoles = $this->the_plugin->get_theoption('psp_capabilities_roles');
			if ( !empty($request['user_role']) ) {
				$capabilitiesRoles["{$request['user_role']}"] = !empty($request['modules']) ? explode(',', $request['modules']) : array();
				$this->the_plugin->save_theoption('psp_capabilities_roles', $capabilitiesRoles);
			}
			
			$html = implode("\n", $this->roles_html($pms));
			
			die( json_encode(array(
				'status' => 'valid',
				'html'	=> $html
			)) );
		}
	}
}
// Initialize the your aaCapabilities
//$aaCapabilities = new aaCapabilities();
$aaCapabilities = aaCapabilities::getInstance();