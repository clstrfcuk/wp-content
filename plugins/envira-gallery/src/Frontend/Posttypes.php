<?php
/**
 * Posttype class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

namespace Envira\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class Posttypes {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_postypes' ) );

	}

	/**
	 * Register Envira Posttypes.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	public function register_postypes(){

		$whitelabel = envira_is_whitelabel();

		// Build the labels for the post type.
		$labels =    array(
			'name'               => $whitelabel ? apply_filters('envira_whitelabel_name_plural', false ) : __( 'Envira Galleries', 'envira-gallery' ),
			'singular_name'      => $whitelabel ? apply_filters('envira_whitelabel_name', false ) : __( 'Envira Gallery', 'envira-gallery' ),
			'add_new'            => __( 'Add New', 'envira-gallery' ),
			'add_new_item'       => $whitelabel ? __( 'Add New Gallery', 'envira-gallery' ) : __( 'Add New Envira Gallery', 'envira-gallery' ),
			'edit_item'          => $whitelabel ? __( 'Edit Gallery', 'envira-gallery' ) : __( 'Edit Envira Gallery', 'envira-gallery' ),
			'new_item'           => $whitelabel ? __( 'New Gallery', 'envira-gallery' ) : __( 'New Envira Gallery', 'envira-gallery' ),
			'view_item'          => $whitelabel ? __( 'View Gallery', 'envira-gallery' ) : __( 'View Envira Gallery', 'envira-gallery' ),
			'search_items'       => $whitelabel ? __( 'Search Galleries', 'envira-gallery' ) : __( 'Search Envira Galleries', 'envira-gallery' ),
			'not_found'          => $whitelabel ? __( 'No galleries found', 'envira-gallery' ) : __( 'No Envira galleries found.', 'envira-gallery' ),
			'not_found_in_trash' => $whitelabel ? __( 'No galleries found in trash.', 'envira-gallery' ) : __( 'No Envira galleries found in trash.', 'envira-gallery' ),
			'parent_item_colon'  => '',
			'menu_name'          => $whitelabel ? apply_filters('envira_whitelabel_name', false ) : __( 'Envira Gallery', 'envira-gallery' ),
		);
		$labels = apply_filters( 'envira_gallery_post_type_labels', $labels );

		// Build out the post type arguments.
		$args = array(
			'labels'                => $labels,
			'public'                => false,
			'exclude_from_search' => true,
			'show_ui'                => true,
			'show_in_menu'           => true,
			'show_in_admin_bar'      => true,
			'rewrite'                => false,
			'query_var'           => false,
			'menu_position'       => apply_filters( 'envira_gallery_post_type_menu_position', 247 ),
			'menu_icon'           => plugins_url( 'assets/css/images/menu-icon@2x.png', ENVIRA_FILE ),
			'supports'            => array( 'title' ),
			'map_meta_cap'        => true,

		);

		//Check if standalone is enabled
		if ( envira_is_standalone_enabled() ) {

			// Get slug
			$slug = envira_standalone_get_slug( 'gallery' );

			// Change the default post type args so that it can be publicly accessible.
			$args['rewrite']                    = array( 'with_front' => false, 'slug' => $slug );
			$args['query_var']              = true;
			$args['exclude_from_search']    = false;
			$args['public']                     = true;
			$args['supports'][]             = 'slug';
			$args['supports'][]             = 'author';

		}

		$args['capabilities'] = array(
			// Meta caps
			'edit_post'             => 'edit_envira_gallery',
			'read_post'             => 'read_envira_gallery',
			'delete_post'           => 'delete_envira_gallery',

			// Primitive caps outside map_meta_cap()
			'edit_posts'            => 'edit_envira_galleries',
			'edit_others_posts'     => 'edit_other_envira_galleries',
			'publish_posts'         => 'publish_envira_galleries',
			'read_private_posts'    => 'read_private_envira_galleries',

			// Primitive caps used within map_meta_cap()
			'read'                  => 'read',
			'delete_posts'          => 'delete_envira_galleries',
			'delete_private_posts'  => 'delete_private_envira_galleries',
			'delete_published_posts'=> 'delete_published_envira_galleries',
			'delete_others_posts'   => 'delete_others_envira_galleries',
			'edit_private_posts'    => 'edit_private_envira_galleries',
			'edit_published_posts'  => 'edit_published_envira_galleries',
			'edit_posts'            => 'create_envira_galleries',
		);

		// Filter arguments.
		$args = apply_filters( 'envira_gallery_post_type_args', $args );

		// Register the post type with WordPress.
		register_post_type( 'envira', $args );

	}
}