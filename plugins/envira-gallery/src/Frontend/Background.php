<?php
/**
 * Handles all background proccessing interactions for the Envira Gallery plugin.
 *
 * @since 1.7.0
 *
 * @package Envira Gallery
 * @author  Envira Team
 */
namespace Envira\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Utils\Cropping;

class Background{

	/**
	 * namespace
	 *
	 * (default value: 'envira')
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 * @access public
	 */
	public $domain  = 'envira-background';

	/**
	 * version
	 *
	 * (default value: 'v1')
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 * @access public
	 */
	public $version     = 'v1';

	/**
	 * request
	 *
	 * (default value: null)
	 *
	 * @var mixed
	 * @access public
	 */
	public $request = null;

	/**
	 * Main constructor.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	function __construct(){

		//Actions
		add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );

	}

	/**
	 * Register API Routes used for background proccessing.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	public function register_api_routes(){

		$name    = $this->domain;
		$version = $this->version;

		register_rest_route(
			$name .'/' . $version ,
			'/insert-gallery',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'maybe_insert_gallery' ),
			)
		);
		register_rest_route(
			$name .'/' . $version ,
			'/insert-album',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'maybe_insert_album' ),
			)
		);
		register_rest_route(
			$name .'/' . $version ,
			'/insert-image',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'insert_image' ),
			)
		);
		register_rest_route(
			$name .'/' . $version ,
			'/crop-images',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'crop_images' ),
			)
		);
		register_rest_route(
			$name .'/' . $version ,
			'/resize-image',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'resize' ),
			)
		);

		do_action( 'envira_gallery_routes', $name, $version );

	}

	/**
	 * insert_gallery function.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	public function maybe_insert_gallery( \ WP_REST_Request $request ){

		 //Set the request.
		$this->request = $request;

		//Get the body
        $body = $request->get_body_params();

        //Validate the request
        $valid = $this->validate_request( $request );

        //Setup the ID Var
        $post_id = '';

        //Return if request not valid
        if ( ! $valid ){

				return;

        }

        $defaults = array(
            'ID'          => 0,
            'post_type'     => 'envira',
            'post_status' => 'publish',
            'post_title'    => ''
        );

        $post_args = wp_parse_args( $body['data']['gallery'], $defaults );

        if ( isset( $body['data']['id'] ) ){

            $post_id = get_post( $body['data']['id'] );

        }

        if ( ! $post_id ){

            $post = wp_insert_post( $post_args );

        } else {

            $post = wp_update_post( $post_args );

        }
        //make a request to insert images is
        if ( is_array( $body['data'][ 'images' ] ) ){

				$images = $body['data'][ 'images' ];

				foreach( $images as $image => $data ){

			        //Build the Image Data
			        $image_data = array(
				        'gallery' => $post,
				        'image'   => $data,
				  );

			        //Make the background request for inserting each image.
			        $this->background_request( $image_data, 'insert-image' );

				}

        }

	}

	/**
	 * Background request to insert images into a gallery.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	public function insert_image( \ WP_REST_Request $request ){

		 //Set the request.
		$this->request = $request;

		//Get the body
        $body = $request->get_body_params();

        //Validate the request
        $valid = $this->validate_request( $request );

        //Return if request not valid
        if ( ! $valid ){

				return;

        }

		//Require if the function doesnt exist
		if( ! function_exists( 'wp_handle_sideload' ) ){

			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			include( ABSPATH . 'wp-admin/includes/image.php' );

		}

        $post_id    = $body['data']['gallery'];
        $image      = $body['data']['image'];

        //Check that $post_id is a envira post_type
        if( get_post_type( $post_id ) != 'envira' ){

            return false;

		}

		$in_gallery =    get_post_meta( $post_id, '_eg_in_gallery', true );

		 if ( empty( $in_gallery ) ) {

			 $in_gallery = array();

		 }

		 $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );

		 //If Gallery Data is emptyy prepare it.
		 if ( empty( $gallery_data ) ) {

			 $gallery_data = array();
		 }

		 //Set the File name from the API
		$new_attachment = $image['title'];

		//Grab the Upload Directory
		$upload_dir = wp_upload_dir();

		//Set the upload path
		$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

		//Decode the returned image
		$image_upload = file_put_contents( $upload_path . $new_attachment, file_get_contents( $image['src'] ) );

		//Prep the new file
		$file               = array();
		$file['error']      = '';
		$file['tmp_name']   = $upload_path . $new_attachment;
		$file['name']       = $new_attachment;
		$file['type']       = $gallery_data;
		$file['size']       = filesize( $upload_path . $new_attachment );

		$file_return        = wp_handle_sideload( $file, array( 'test_form' => false ) );

		//Setup the Attachment Data
		$attachment = array(
			'post_type'         => 'attachment',
			'post_mime_type'    => 'image/jpeg',
			'post_title'        => preg_replace( '/\.[^.]+$/', '', basename( $new_attachment ) ),
			'post_content'      => '',
			'post_status'       => 'inherit',
			'post_parent'        => $post_id
		);

		//Insert new attachment - check
		$attachment_id = wp_insert_attachment( $attachment, $file_return['file'], $post_id );

		//Generate Attachment Metadata
		$meta_data = wp_generate_attachment_metadata( $attachment_id, $file_return['file'] );

		//Update Attachments metadata
		$update_data = wp_update_attachment_metadata( $attachment_id,    $meta_data );

		// Update the attachment image post meta first.
		$has_slider = get_post_meta( $attachment_id, '_eg_has_gallery', true );

		if ( empty( $has_slider ) ) {

			$has_slider = array();

		}

		$has_slider[] = $post_id;

		// Now add the image to the slider for this particular post.
		$in_slider[] = $attachment_id;
		$slider_data = envira_prepare_gallery_data( $gallery_data, $attachment_id );

		// Update the slider data.
		update_post_meta( $attachment_id, '_eg_has_gallery', $has_slider );
		update_post_meta( $post_id, '_eg_in_gallery', $in_slider );
		update_post_meta( $post_id, '_eg_gallery_data', $slider_data );

	}

	/**
	 * maybe_insert_album function.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @param WP_REST_Request $request
	 * @return void
	 */
	public function maybe_insert_album( \ WP_REST_Request $request ){
		 //Set the request.
		$this->request = $request;

		//Get the body
        $body = $request->get_body_params();

        //Validate the request
        $valid = $this->validate_request( $request );

        //Setup the ID Var
        $post_id = '';

        //Return if request not valid
        if ( ! $valid ){

				return;

        }

        $defaults = array(
            'ID'            => 0,
            'post_type'     => 'envira-album',
            'post_status'   => 'publish',
            'post_title'    => ''
        );

        $post_args = wp_parse_args( $body['data']['album'], $defaults );

        if ( isset( $body['data']['id'] ) ){

            $post_id = get_post( $body['data']['id'] );

        }

        if ( ! $post_id ){

            $post = wp_insert_post( $post_args );

        } else {

            $post = wp_update_post( $post_args );

        }

        //make a request to insert images is
        if ( is_array($body['data'][ 'galleries' ] ) ){

				$images = $body['data'][ 'galleries' ];

				foreach( $galleries as $gallery => $data ){

			        //Build the Image Data
			        $image_data = array(
				        'album' => $post,
				        'galleries'   => $data,
				  );

			        //Make the background request for inserting each image.
			        $this->background_request( $image_data, 'insert-gallery' );

				}

        }
	}

	/**
	 * Crop and image via background request.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @param WP_REST_Request $request
	 * @return void
	 */
	public function crop_images( \ WP_REST_Request $request ){

		//Set the request.
		$this->request = $request;

		//Get the body
        $body = $request->get_body_params();

        //Validate the request
        $valid = $this->validate_request( $request );

        //Return if request not valid
        if ( ! $valid ){

            return;

        }

        $data           = $body['data'];
		$gallery_data   = _envira_get_gallery( $body['data']['id'] );
		$gallery_id     =  $body['data']['id'];
		$images         = ! empty( $gallery_data['gallery'] ) ? $gallery_data['gallery'] : false;
		$align          = envira_get_config( 'crop_position', $gallery_data );

		// Loop through the images and crop them.
		if ( $images ) {

			foreach ( $images as $id => $item ) {

				// Get the full image attachment. If it does not return the data we need, skip over it.
				$image = wp_get_attachment_image_src( $id, 'full' );

				if ( ! is_array( $image ) ) {
					continue;
				}

				// Check the image is a valid URL
				// Some plugins decide to strip the blog's URL
				if ( ! filter_var( $image[0], FILTER_VALIDATE_URL ) ) {
					$image[0] = get_bloginfo( 'url' ) . '/' . $image[0];
				}

				// If the thumbnails option is checked, crop images accordingly.
				if ( isset( $gallery_data['config']['thumbnails'] ) && $gallery_data['config']['thumbnails'] ) {

					$args = array(
						'align'    => $align,
						'width'    => apply_filters( 'envira_gallery_lightbox_thumbnail_width', $gallery_data['config']['thumbnails_width'], $gallery_data ),
						'height'   => apply_filters( 'envira_gallery_lightbox_thumbnail_height', $gallery_data['config']['thumbnails_height'], $gallery_data ),
						'quality'  => 100,
						'retina'   => false
					);

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

					$args['retina'] = true;

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

				}

				// If the mobile thumbnails option is checked, crop images accordingly.
				if ( isset( $gallery_data['config']['mobile_thumbnails'] ) && $gallery_data['config']['mobile_thumbnails'] ) {

					$args = array(
						'align'    => $align,
						'width'    => apply_filters( 'envira_gallery_mobile_lightbox_thumbnail_width', $gallery_data['config']['mobile_thumbnails_width'], $gallery_data ),
						'height'   => apply_filters( 'envira_gallery_mobile_lightbox_thumbnail_height', $gallery_data['config']['mobile_thumbnails_height'], $gallery_data ),
						'quality'  => 100,
						'retina'   => false
					);


					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

					$args['retina'] = true;

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');
				}

				// If the crop option is checked, crop images accordingly.
				if ( isset( $gallery_data['config']['crop'] ) && $gallery_data['config']['crop'] ) {

					$args = array(
						'align'    => $align,
						'width'    => envira_get_config( 'crop_width', $gallery_data ),
						'height'   => envira_get_config( 'crop_height', $gallery_data ),
						'quality'  => 100,
						'retina'   => false
					);

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

					$args['retina'] = true;

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

				}

				// If the mobile option is checked, crop images accordingly.
				if ( isset( $gallery_data['config']['mobile'] ) && $gallery_data['config']['mobile'] ) {

					$args = array(
						'align'    => $align,
						'width'    => envira_get_config( 'mobile_width', $gallery_data ),
						'height'   => envira_get_config( 'mobile_height', $gallery_data ),
						'quality'  => 100,
						'retina'   => false
					);

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

					$args['retina'] = true;

					$crop_data = array(
						'args'          => $args,
						'gallery_id'    => $gallery_id,
						'image_url'     => $image[0]
					);

					// Generate the cropped image.
					$this->background_request(  $crop_data , 'resize-image');

				}

			}

		}

	}

	/**
	 * resize function.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @param \ WP_REST_Request $request
	 * @return void
	 */
	public function resize( \ WP_REST_Request $request ){

		//Set the request.
		$this->request = $request;

		//Validate the request
		$valid = $this->validate_request( $request );

		//Get the body
		$body = $request->get_body_params();

        //Return if request not valid
        if ( ! $valid ){

			wp_send_json_error();

        }

		$cropping = new Cropping;

        $defaults = array(
			'url'             => '',
			'id'              => '',
			'gallery_id'      => '',
			'width'           => null,
			'height'          => null,
			'crop'            => true,
			'align'           => 'c',
			'quality'         => 100,
			'retina'          => false,
			'data'            => array(),
			'force_overwrite' => false
		);

        $args = wp_parse_args( $body['data']['args'], $defaults );

		$cropped_image = $cropping->resize_image( $body['data']['image_url'], $args['width'], $args['height'], true, $args['align'], $args['quality'], $args['retina'], null, $args['force_overwrite'] );

		if ( $cropped_image ){
			wp_send_json_success($cropped_image);

		} else {
			wp_send_json_error( $cropped_image );
		}

	}

	/**
	 * Validates the API request.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate_request( $request ) {

		$body = $request->get_body_params();

		if( ! is_array($body) ){

			return false;
		}

		// Verify the request is comming from the site
		$site_url = site_url();

		if ( strpos( $body['site'], $site_url ) === false ) {

			return false;

		}

		$token = get_option( 'envira_rest_token' );

		if ( $token != $request->get_header('X-Envira-Token') ){

			return false;

		}

		do_action( 'envira_gallery_validate_bp' );

		// All checks passed.
		return true;

	}

	/**
	 * Self generated token to validate background requests.
	 *
	 * @access public
	 * @return void
	 */
	public function generate_token(){

		$rest_token = wp_generate_password( 45, false, false );

		$hash = hash( 'sha256', $rest_token );

		update_option( 'envira_rest_token', $hash );

		return $hash;

	}

	/**
	 * Helper function to call background requests.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @param mixed $data
	 * @param mixed $type
	 * @return void
	 */
	public function background_request( $data, $type ){

		//Bail if nothing set
		if ( ! is_array( $data ) || ! isset( $type ) ){
			return;
		}

		$name    = $this->domain;
		$version = $this->version;

		$rest_url = get_rest_url();
		$nonce = wp_create_nonce( 'wp_rest' );
		$token = get_option( 'envira_rest_token');

		if ( !$token ){

			$token = $this->generate_token();
		}

		$defaults = array(
			'data'      => $data,
			'site'      => get_home_url(),
			'nonce'     => $nonce,
		);

		$body = wp_parse_args( $data, $defaults );

		$headers = array(
			'X-Envira-Token' =>  $token,
		);

		//Generate the background request url
		$url = trailingslashit( $rest_url ). $name .'/' . $version . '/' . $type;

		$args    = array(
			'headers'       => $headers,
			'body'          => $body,
			'user-agent'    => 'Envira/'.ENVIRA_VERSION,
		    'timeout'       => 0.5,
		    'blocking'      => false,
		    'sslverify'     => apply_filters( 'envira_background_ssl_verify', false ),
		);

		$callit   = wp_remote_post( $url, $args );

	}

}