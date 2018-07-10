<?php
class Envira_Gallery_License{
	
	public static $_instance = null;
	public function __construct(){
		
	}
	
	public function notices(){
		//nothing to see here, just a fix for X theme
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