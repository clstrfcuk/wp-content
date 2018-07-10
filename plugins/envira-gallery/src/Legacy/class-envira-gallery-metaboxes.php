<?php
	
class Envira_Gallery_Metaboxes{
	
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
	 * __construct function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){
		
	}
	
	/**
	 * get_config function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @param mixed $key
	 * @param bool $default (default: false)
	 * @return void
	 */
	public function get_config( $key, $default = false ) {
		global $id, $post;
		// Get the current post ID. If ajax, grab it from the $_POST variable.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && array_key_exists( 'post_id', $_POST ) ) {
			$post_id = absint( $_POST['post_id'] );
		} else {
			$post_id = isset( $post->ID ) ? $post->ID : (int) $id;
		}
		// Get config
		$settings = get_post_meta( $post_id, '_eg_gallery_data', true );
		// Check config key exists
		if ( isset( $settings['config'] ) && isset( $settings['config'][ $key ] ) ) {
			return $settings['config'][ $key ];
		} else {
			return $default ? $default : '';
		}
	}
	 /**
	 * Helper method for setting default config values.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @param string $key The default config key to retrieve.
	 * @return string Key value on success, false on failure.
	 */
	public function get_config_default( $key ) {
		return envira_get_config_default( $key );
	}
  
	/**
	 * get_gallery_item function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @param mixed $id
	 * @param mixed $item
	 * @param int $post_id (default: 0)
	 * @return void
	 */
	public function get_gallery_item( $id, $item, $post_id = 0 ) {

		$metaboxes = new Envira\Admin\Metaboxes;
		return $metaboxes->get_gallery_item( $id, $item, $post_id );

	}

	/**
	 * crop_thumbnails function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @param mixed $args
	 * @param mixed $post_id
	 * @param bool $force_overwrite (default: false)
	 * @param bool $mobile (default: false)
	 * @return void
	 */
	public function crop_thumbnails( $args, $post_id, $force_overwrite = false, $mobile = false ) {

		return false;

		// return envira_crop_thumbnails( $args, $post_id, $force_overwrite, $mobile);

	}
    /**
     * Determines the Preview metabox display based on the type of gallery selected.
	 * 
	 * __Depricated since 1.7.0.
	 *
     * @param string $type The type of display to output.
     * @param object $data Gallery Data
     */
    public function preview_display( $type = 'default', $data ) {

        // Output the display based on the type of slider available.
        switch ( $type ) {
            case 'default' :
                // Don't preview anything
                break;
            default:
                do_action( 'envira_gallery_preview_' . $type, $data );
                break;
        }

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
	
		if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof Envira_Gallery_Metaboxes ) ) {
				
			self::$_instance = new self();	
		}
	
		return self::$_instance;		
	
	}
	
}