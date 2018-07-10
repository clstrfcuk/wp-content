<?php
	
use Envira\Utils\Import;

class Envira_Gallery_Import{
		
	/**
	 * _instance
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access public
	 * @static
	 */
	public static $_instance = null;

	/**
	 * import_remote_image function.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @param mixed $src
	 * @param mixed $data
	 * @param mixed $item
	 * @param mixed $post_id
	 * @param int $id (default: 0)
	 * @param bool $stream_only (default: false)
	 * @return void
	 */
	public function import_remote_image( $src, $data, $item, $post_id, $id = 0, $stream_only = false ) {

		$import = new Import;

		return $import->import_remote_image( $src, $data, $item, $post_id, $id, $stream_only );
	}

	/**
	 * get_instance function.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @static
	 * @return instance
	 */
	public static function get_instance(){
	
		if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof Envira_Gallery_Import ) ) {
				
			self::$_instance = new self();	
		}
	
		return self::$_instance;		
	
	}
	
}