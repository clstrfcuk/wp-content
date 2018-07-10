<?php
	
class Envira_Gallery_Shortcode{
		
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

	public $is_mobile = false;

	public function __construct(){
		
		$this->is_mobile = envira_mobile_detect()->isMobile();

	}
	/**
	 * __Depricated since 1.7.0 use envira_get_config.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @return mixed
	 */	
	public function get_config( $key, $data ){
		
		return envira_get_config( $key, $data );
		
	}
	
	/**
	 * minify function.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @param mixed $string
	 * @param bool $stripDoubleForwardslashes (default: true)
	 * @return void
	 */
	public function minify( $string, $stripDoubleForwardslashes = true	 ){
		
		return envira_minify( $string, $stripDoubleForwardslashes );
		
	}

	/**
	 * is_image function.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function is_image( $url	 ){
		
		return envira_is_image( $url );
		
	}

	/**
	 * is_mobile function.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function is_mobile(){
		
		return envira_mobile_detect()->isMobile();
		
	}
	

	/**
	 * shortcode function.
	 * 
	 * __Depricated since 1.7.0.
	 * 
	 * @access public
	 * @param mixed $atts
	 * @return void
	 */
	public function shortcode( $atts  ){
		
		$shortcode = new Envira\Frontend\Shortcode;
		return $shortcode->shortcode( $atts );
		
	}

	/**
	 * Maybe sort the gallery images, if specified in the config
	 *
	 * Note: To ensure backward compat with the previous 'random' config
	 * key, the sorting parameter is still stored in the 'random' config
	 * key.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @param		array	$data		Gallery Config
	 * @param		int		$gallery_id Gallery ID
	 * @exclusion 	array	$exclusions	Gallery IDs
	 * @return		array				Gallery Config
	 */
	public function maybe_sort_gallery( $data, $gallery_id, $exclusions = false ) {

		if ( ! empty( $this->gallery_sort[ $gallery_id ] ) && ! empty( $data['gallery'] ) ) {
			// sort using the gallery_sort order
			$data['gallery'] = array_replace( array_flip( $this->gallery_sort[ $gallery_id ] ), $data['gallery'] );
			return $data;
		}

		//Return if gallery is empty
		if ( empty( $data['gallery'] ) ){
			return $data;
		}

		// Get sorting method
		$sorting_method		= (string) $this->get_config( 'random', $data );
		$sorting_direction	= $this->get_config( 'sorting_direction', $data );

		// Sort images based on method
		switch ( $sorting_method ) {

			/**
			* Random
			* - Again, by design, to ensure backward compat when upgrading from 1.3.7.x or older
			* where we had a 'random' key = 0 or 1. Sorting was introduced in 1.3.8
			*/
			case '1':
				// Shuffle keys
				$keys = array_keys( $data['gallery'] );
				shuffle( $keys );

				// Rebuild array in new order
				$new = array();
				foreach( $keys as $key ) {
					// if one of these images is an exclusion, don't add it
					if ( !$exclusions || ( $exclusions && !in_array( $key, $exclusions ) && !array_key_exists( $key, $new ) ) ) {
						$new[ $key ] = $data['gallery'][ $key ];
					}
				}

				// Assign back to gallery
				$data['gallery'] = $new;

				break;

			/**
			* Image Meta
			*/
			case 'src':
			case 'title':
			case 'caption':
			case 'alt':
			case 'link':
				// Get metadata
				$keys = array();
				foreach ( $data['gallery'] as $id => $item ) {
					$keys[ $id ] = strip_tags( $item[ $sorting_method ] );
				}

				// Sort titles / captions
				natcasesort( $keys ); // natcasesort is case insensitive, unlike asort

				// allow override of the type of sort
				$keys = apply_filters( 'envira_gallery_sort_image_meta', $keys, $data, $sorting_method, $gallery_id );

				if ( $sorting_direction == 'DESC' ) {
					$keys = array_reverse( $keys, true ); 
				}

				// Iterate through sorted items, rebuilding gallery
				$new = array();
				foreach( $keys as $key => $title ) {
					$new[ $key ] = $data['gallery'][ $key ];
				}

				// Assign back to gallery
				$data['gallery'] = $new;
				break;

			/**
			* Published Date
			*/
			case 'date':
				// Get published date for each
				$keys = array();
				foreach ( $data['gallery'] as $id => $item ) {
					// If the attachment isn't in the Media Library, we can't get a post date - assume now
					if ( ! is_numeric( $id ) || ( false === ( $attachment = get_post( $id ) ) ) ) {
						$keys[ $id ] = date( 'Y-m-d H:i:s' );
					} else {
						$keys[ $id ] = $attachment->post_date;
					}
				}

				// Sort titles / captions
				if ( $sorting_direction == 'ASC' ) {
					asort( $keys );
				} else {
					arsort( $keys );
				}

				// Iterate through sorted items, rebuilding gallery
				$new = array();
				foreach( $keys as $key => $title ) {
					$new[ $key ] = $data['gallery'][ $key ];
				}

				// Assign back to gallery
				$data['gallery'] = $new;
				break;

			/**
			* None
			* - Do nothing
			*/
			case '0':
			case '':
				break;

			/**
			* If developers have added their own sort options, let them run them here
			*/
			default:
				$data = apply_filters( 'envira_gallery_sort_gallery', $data, $sorting_method, $gallery_id );
				break;

		}

		// Set the sort order
		if ( ! empty( $data['gallery'] ) ) {
			foreach ( $data['gallery'] as $id => $d ) {
				$this->gallery_sort[ $gallery_id ][] = $id;
			}
		}

		return $data;

	}

	/**
	 * generate_gallery_item_markup function.
	 * 
	 * __Depricated since 1.7.0.
	 *
	 * @access public
	 * @param mixed $gallery
	 * @param mixed $data
	 * @param mixed $item
	 * @param mixed $id
	 * @param mixed $i
	 * @return void
	 */
	public function generate_gallery_item_markup( $gallery, $data, $item, $id, $i ) {

		$shortcode = new Envira\Frontend\Shortcode;
		return $shortcode->generate_gallery_item_markup( $gallery, $data, $item, $id, $i );

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
	
		if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof Envira_Gallery_Shortcode ) ) {
				
			self::$_instance = new self();	
		}
	
		return self::$_instance;		
	
	}
	
}