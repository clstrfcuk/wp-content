<?php
class Envira_Gallery_Settings{
	
	public static $_instance = null;
	public function __construct(){
		
	}
	
	/**
	 * __Depricated since 1.7.0 use envira_get_config_defaults.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function get_setting( $setting ){
		
		return envira_get_setting( $setting );
		
	}
	public function admin_menu(){
		//nothing to see here, just a fix for X theme
	}
	/**
	 * update_setting function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public function update_setting( $key, $value ){
		
		return envira_update_setting( $key, $value );
		
	}

	/**
	 * get_instance function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function get_instance(){
	
		if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof Envira_Gallery_Settings ) ) {
				
			self::$_instance = new self();	
		}
	
		return self::$_instance;		
	
	}
	
}