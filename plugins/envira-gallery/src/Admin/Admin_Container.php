<?php
/**
 * Admin Container
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author	Envira Gallery Team
 */
namespace Envira\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Admin\Settings;
use Envira\Admin\Metaboxes;
use Envira\Admin\Addons;
use Envira\Admin\Importers;
use Envira\Admin\Table;
use Envira\Admin\Posttype;
use Envira\Admin\Editor;
use Envira\Admin\License;
use Envira\Admin\Notices;
use Envira\Admin\Media_View;

// use Envira\Admin\Debug;

class Admin_Container{
	
	/**
	 * Holds all dismissed notices
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	public $notices;	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		
		// Actions
		add_action( 'admin_init', array( $this, 'add_capabilities' ) );
		add_filter( 'wp_handle_upload', array( $this, 'fix_image_orientation' ) );
		
		// Handle any necessary DB upgrades.
		add_action( 'admin_init', array( $this, 'db_upgrade' ) );

		// Load admin assets.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_head', array( $this, 'custom_admin_style' ), 1 );

		// Delete any gallery association on attachment deletion. Also delete any extra cropped images.
		add_action( 'delete_attachment', array( $this, 'delete_gallery_association' ) );
	  //  add_action( 'delete_attachment', array( $this, 'delete_cropped_image' ) );

		// Ensure gallery display is correct when trashing/untrashing galleries.
		add_action( 'wp_trash_post', array( $this, 'trash_gallery' ) );
		add_action( 'untrash_post', array( $this, 'untrash_gallery' ) );

		// Delete attachments, if setting enabled, when a gallery is permanently deleted
		add_action( 'before_delete_post', array( $this, 'delete_gallery' ) );
	   
		// Prevent plugins from breaking Envira in admin
		add_action( 'wp_print_scripts', array( $this, 'plugin_humility' ), 1 );
	   
		// Populate $notices
		$this->notices = get_option( 'envira_gallery_notices' );
		if ( ! is_array( $this->notices ) ) {
			$this->notices = array();
		}
			
		$posttype 		= new Posttype;		
		$settings 		= new Settings;
		$metaboxes 		= new Metaboxes;
		$importers 		= new Importers;
		$table 			= new Table;
		$editor 		= new Editor;
		$license 		= new License;
		$license 		= new Notices;
		$addons 		= new Addons;
		$media_view 	= new Media_View;
		
	}



	/**
	 * Checks if a given notice has been dismissed or not
	 *
	 * @since 1.3.5
	 *
	 * @param string $notice Programmatic Notice Name
	 * @return bool Notice Dismissed
	 */

	public function is_dismissed( $notice ) {

		if ( ! isset( $this->notices[ $notice ] ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Marks the given notice as dismissed
	 *
	 * @since 1.3.5
	 *
	 * @param string $notice Programmatic Notice Name
	 * @return null
	 */
	public function dismiss( $notice ) {

		$this->notices[ $notice ] = true;
		update_option( 'envira_gallery_notices', $this->notices );

	}


	/**
	 * Marks a notice as not dismissed
	 *
	 * @since 1.3.5
	 *
	 * @param string $notice Programmatic Notice Name
	 * @return null
	 */
	public function undismiss( $notice ) {

		unset( $this->notices[ $notice ] );
		update_option( 'envira_gallery_notices', $this->notices );

	}

	/**
	 * Displays an inline notice with some Envira styling.
	 *
	 * @since 1.3.5
	 *
	 * @param string	$notice				Programmatic Notice Name
	 * @param string	$title				Title
	 * @param string	$message			Message
	 * @param string	$type				Message Type (updated|warning|error) - green, yellow/orange and red respectively.
	 * @param string	$button_text		Button Text (optional)
	 * @param string	$button_url			Button URL (optional)
	 * @param bool		$is_dismissible		User can Dismiss Message (default: true)
	 */ 
	public function display_inline_notice( $notice, $title, $message, $type = 'success', $button_text = '', $button_url = '', $is_dismissible = true ) {

		// Check if the notice is dismissible, and if so has been dismissed.
		if ( $is_dismissible && $this->is_dismissed( $notice ) ) {
			// Nothing to show here, return!
			return;
		}

		// Display inline notice
		?>
		<div class="envira-notice <?php echo $type . ( $is_dismissible ? ' is-dismissible' : '' ); ?>" data-notice="<?php echo $notice; ?>">
			<?php
			// Title
			if ( ! empty ( $title ) ) {
				?>
				<p class="envira-intro"><?php echo $title; ?></p>
				<?php
			}

			// Message
			if ( ! empty( $message ) ) {
				?>
				<p><?php echo $message; ?></p>
				<?php
			}
			
			// Button
			if ( ! empty( $button_text ) && ! empty( $button_url ) ) {
				?>
				<a href="<?php echo $button_url; ?>" target="_blank" class="button button-primary"><?php echo $button_text; ?></a>
				<?php
			}

			// Dismiss Button
			if ( $is_dismissible ) {
				?>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">
						<?php _e( 'Dismiss this notice', 'envira-gallery' ); ?>
					</span>
				</button>
				<?php
			}
			?>
		</div>
		<?php

	}  
	/**
	 * Registers Envira Gallery capabilities for each Role, if they don't already exist.
	 *
	 * If capabilities don't exist, they're copied from Posts. This ensures users prior to 1.3.7
	 * get like-for-like behaviour in Envira and don't notice the new capabilities.
	 *
	 * @since 1.0.0
	 */
	public function add_capabilities() {

		// Grab the administrator role, and if it already has an Envira capability key defined, bail
		// as we only need to register our capabilities once.
		$administrator = get_role( 'administrator' );
		if ( $administrator->has_cap( 'edit_other_envira_galleries' ) ) {
		   return;
		}

		// If here, we need to assign capabilities
		// Define the roles we want to assign capabilities to
		$roles = array(
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		);

		// Iterate through roles
		foreach ( $roles as $role_name ) {
			// Properly get the role as WP_Role object
			$role = get_role( $role_name );
			if ( ! is_object( $role ) ) {
				continue;
			}

			// Map this Role's Post capabilities to our Envira Gallery capabilities
			$caps = array(
				'edit_envira_gallery'				=> $role->has_cap( 'edit_posts' ),
				'read_envira_gallery'				=> $role->has_cap( 'read' ),
				'delete_envira_gallery'				=> $role->has_cap( 'delete_posts' ),

				'edit_envira_galleries'				=> $role->has_cap( 'edit_posts' ),
				'edit_other_envira_galleries'		=> $role->has_cap( 'edit_others_posts' ),
				'edit_others_envira_galleries'		=> $role->has_cap( 'edit_others_posts' ),
				'publish_envira_galleries'			=> $role->has_cap( 'publish_posts' ),
				'read_private_envira_galleries'		=> $role->has_cap( 'read_private_posts' ),

				'delete_envira_galleries'			=> $role->has_cap( 'delete_posts' ),
				'delete_private_envira_galleries'	=> $role->has_cap( 'delete_private_posts' ),
				'delete_published_envira_galleries' => $role->has_cap( 'delete_published_posts' ),
				'delete_others_envira_galleries'	=> $role->has_cap( 'delete_others_posts' ),
				'edit_private_envira_galleries'		=> $role->has_cap( 'edit_private_posts' ),
				'edit_published_envira_galleries'	=> $role->has_cap( 'edit_published_posts' ),
				'create_envira_galleries'			=> $role->has_cap( 'edit_posts' ),
			);

			// Add the above Envira capabilities to this Role
			foreach ( $caps as $envira_cap => $value ) {
				// Don't add if value is false
				if ( ! $value ) {
					continue;
				}

				$role->add_cap( $envira_cap );
			}
		}

	}	

	/**
	* Check if the EXIF orientation flag matches one of the values we're looking for
	* http://www.impulseadventure.com/photo/exif-orientation.html
	*
	* If it does, this means we need to rotate the image based on the orientation flag and then remove the flag.
	* This will ensure the image has the correct orientation, regardless of where it's displayed.
	*
	* Whilst most browsers and applications will read this flag to perform the rotation on displaying just the image, it's
	* not possible to do this in some situations e.g. displaying an image within a lightbox, or when the image is
	* within HTML markup.
	*
	* Orientation flags we're looking for:
	* 8: We need to rotate the image 90 degrees counter-clockwise
	* 3: We need to rotate the image 180 degrees
	* 6: We need to rotate the image 90 degrees clockwise (270 degrees counter-clockwise)
	*
	* @since 1.3.8.2
	*
	* @param array $file	Uploaded File
	* @return array			Uploaded File
	*/
	public function fix_image_orientation( $file ) {

		// Check we have a file
		if ( ! file_exists( $file['file'] ) ) {
			return $file;
		}

		// Check we have a JPEG
		if ( $file['type'] !== 'image/jpg' && $file['type'] !== 'image/jpeg' ) {
			return $file;
		}

		// Attempt to read EXIF data from the image
		$exif_data = wp_read_image_metadata( $file['file'] );
		if ( ! $exif_data ) {
			return $file;
		}

		// Check if an orientation flag exists
		if ( ! isset( $exif_data['orientation'] ) ) {
			return $file;
		}

		// Check if the orientation flag matches one we're looking for
		$required_orientations = array( 8, 3, 6 );
		if ( ! in_array( $exif_data['orientation'], $required_orientations ) ) {
			return $file;
		}

		// If here, the orientation flag matches one we're looking for
		// Load the WordPress Image Editor class
		$image = wp_get_image_editor( $file['file'] );
		if ( is_wp_error( $image ) ) {
			// Something went wrong - abort
			return $file;
		} 

		// Store the source image EXIF and IPTC data in a variable, which we'll write 
		// back to the image once its orientation has changed
		// This is required because when we save an image, it'll lose its metadata.
		$source_size = getimagesize( $file['file'], $image_info );

		// Depending on the orientation flag, rotate the image
		switch ( $exif_data['orientation'] ) {

			/**
			* Rotate 90 degrees counter-clockwise
			*/
			case 8:
				$image->rotate( 90 );
				break;

			/**
			* Rotate 180 degrees
			*/
			case 3:
				$image->rotate( 180 );
				break;

			/**
			* Rotate 270 degrees counter-clockwise ($image->rotate always works counter-clockwise)
			*/
			case 6:
				$image->rotate( 270 );
				break;

		}

		// Save the image, overwriting the existing image
		// This will discard the EXIF and IPTC data
		$image->save( $file['file'] );

		// Drop the EXIF orientation flag, otherwise applications will try to rotate the image
		// before display it, and we don't need that to happen as we've corrected the orientation

		// Write the EXIF and IPTC metadata to the revised image
		$result = $this->transfer_iptc_exif_to_image( $image_info, $file['file'], $exif_data['orientation'] );
		if ( ! $result ) {
			return $file;
		}
		
		// Read the image again to see if the EXIF data was preserved
		$exif_data = wp_read_image_metadata( $file['file'] );
		
		// Finally, return the data that's expected
		return $file;

	}

	/**
	* Transfers IPTC and EXIF data from a source image which contains either/both,
	* and saves it into a destination image's headers that might not have this IPTC
	* or EXIF data
	*
	* Useful for when you edit an image through PHP and need to preserve IPTC and EXIF
	* data
	*
	* @since 1.3.8.2
	*
	* @source http://php.net/iptcembed - ebashkoff at gmail dot com
	*
	* @param string $image_info				EXIF and IPTC image information from the source image, using getimagesize()
	* @param string $destination_image		Path and File of Destination Image, which needs IPTC and EXIF data
	* @param int	$original_orientation	The image's original orientation, before we changed it. 
	*										Used when we replace this orientation in the EXIF data
	* @return bool							Success
	*/
	private function transfer_iptc_exif_to_image( $image_info, $destination_image, $original_orientation ) {

		// Check destination exists
		if ( ! file_exists( $destination_image ) ) {
			return false;
		}

		// Get EXIF data from the image info, and create the IPTC segment
		$exif_data = ( ( is_array( $image_info ) && key_exists( 'APP1', $image_info ) ) ? $image_info['APP1'] : null );
		if ( $exif_data ) {
			// Find the image's original orientation flag, and change it to 1
			// This prevents applications and browsers re-rotating the image, when we've already performed that function
			$exif_data = str_replace( chr( dechex( $original_orientation ) ) , chr( 0x1 ), $exif_data );

			$exif_length = strlen( $exif_data ) + 2;
			if ( $exif_length > 0xFFFF ) {
				return false;
			}

			// Construct EXIF segment
			$exif_data = chr(0xFF) . chr(0xE1) . chr( ( $exif_length >> 8 ) & 0xFF) . chr( $exif_length & 0xFF ) . $exif_data;
		}

		// Get IPTC data from the source image, and create the IPTC segment
		$iptc_data = ( ( is_array( $image_info ) && key_exists( 'APP13', $image_info ) ) ? $image_info['APP13'] : null );
		if ( $iptc_data ) {
			$iptc_length = strlen( $iptc_data ) + 2;
			if ( $iptc_length > 0xFFFF ) {
				return false;
			}

			// Construct IPTC segment
			$iptc_data = chr(0xFF) . chr(0xED) . chr( ( $iptc_length >> 8) & 0xFF) . chr( $iptc_length & 0xFF ) . $iptc_data;
		}	 

		// Get the contents of the destination image
		$destination_image_contents = file_get_contents( $destination_image );
		if ( ! $destination_image_contents ) {
			return false;
		}
		if ( strlen( $destination_image_contents ) == 0 ) {
			return false;
		}

		// Build the EXIF and IPTC data headers
		$destination_image_contents = substr( $destination_image_contents, 2 );
		$portion_to_add = chr(0xFF) . chr(0xD8); // Variable accumulates new & original IPTC application segments
		$exif_added = ! $exif_data;
		$iptc_added = ! $iptc_data;

		while ( ( substr( $destination_image_contents, 0, 2 ) & 0xFFF0 ) === 0xFFE0 ) {
			$segment_length = ( substr( $destination_image_contents, 2, 2 ) & 0xFFFF );
			$iptc_segment_number = ( substr( $destination_image_contents, 1, 1 ) & 0x0F );	 // Last 4 bits of second byte is IPTC segment #
			if ( $segment_length <= 2 ) {
				return false;
			}
			
			$thisexistingsegment = substr( $destination_image_contents, 0, $segment_length + 2 );
			if ( ( 1 <= $iptc_segment_number) && ( ! $exif_added ) ) {
				$portion_to_add .= $exif_data;
				$exif_added = true;
				if ( 1 === $iptc_segment_number ) {
					$thisexistingsegment = '';
				}
			}

			if ( ( 13 <= $iptc_segment_number ) && ( ! $iptc_added ) ) {
				$portion_to_add .= $iptc_data;
				$iptc_added = true;
				if ( 13 === $iptc_segment_number ) {
					$thisexistingsegment = '';
				}
			}

			$portion_to_add .= $thisexistingsegment;
			$destination_image_contents = substr( $destination_image_contents, $segment_length + 2 );
		}

		// Write the EXIF and IPTC data to the new file
		if ( ! $exif_added ) {
			$portion_to_add .= $exif_data;
		}
		if ( ! $iptc_added ) {
			$portion_to_add .= $iptc_data;
		}

		$output_file = fopen( $destination_image, 'w' );
		if ( $output_file ) {
			return fwrite( $output_file, $portion_to_add . $destination_image_contents ); 
		}

		return false;
		
	}

	/**
	 * Handles any necessary DB upgrades for Envira.
	 *
	 * @since 1.0.0
	 */
	public function db_upgrade() {

		// Upgrade to allow captions (v1.1.6).
		$captions = get_option( 'envira_gallery_116' );
		if ( ! $captions ) {
			$galleries = ( class_exists( 'Envira_Gallery' ) ? \Envira_Gallery::get_instance()->_get_galleries() : \Envira_Gallery_Lite::get_instance()->_get_galleries() );
			if ( $galleries ) {
				foreach ( $galleries as $gallery ) {
					foreach ( (array) $gallery['gallery'] as $id => $item ) {
						$gallery['gallery'][$id]['caption'] = ! empty( $item['title'] ) ? $item['title'] : '';
						update_post_meta( $gallery['id'], '_eg_gallery_data', $gallery );
						envira_flush_gallery_caches( $gallery['id'], $gallery['config']['slug'] );
					}
				}
			}

			update_option( 'envira_gallery_116', true );
		}

		// 1.2.1: Convert all non-Envira Post Type galleries into Envira CPT galleries.
		$cptGalleries = get_option( 'envira_gallery_121' );
		if ( ! $cptGalleries ) {
			// Get Post Types, excluding our own
			// We don't use post_status => 'any', as this doesn't include CPTs where exclude_from_search = true.
			$postTypes = get_post_types( array(
				'public' => true,
			) );
			$excludedPostTypes = array( 'envira', 'envira_album', 'attachment' );
			foreach ( $postTypes as $key=>$postType ) {
				if ( in_array( $postType, $excludedPostTypes ) ) {
					unset( $postTypes[ $key ] );
				}
			}

			// Get all Posts that have _eg_gallery_data set
			$inPostGalleries = new \ WP_Query( array(
				'post_type'		=> $postTypes,
				'post_status'	=> 'any',
				'posts_per_page'=> -1,
				'meta_query'	=> array(
					array(
						'key'		=> '_eg_gallery_data',
						'compare'	=> 'EXISTS',
					),
				)
			) );

			// Check if any Posts with galleries exist
			if ( count( $inPostGalleries->posts ) > 0 ) {
				$migrated_galleries = 0;

				// Iterate through Posts with Galleries
				foreach ( $inPostGalleries->posts as $post ) {
					// Check if this is an Envira or Envira Album CPT
					// If so, skip it
					if ( $post->post_type == 'envira' || $post->post_type == 'envira_album' ) {
						continue;
					}

					// Get metadata
					$data = get_post_meta( $post->ID, '_eg_gallery_data', true);
					$in = get_post_meta( $post->ID, '_eg_in_gallery', true);

					// Check if there is at least one image in the gallery
					// Some Posts save Envira config data but don't have images - we don't want to migrate those,
					// as we would end up with blank Envira CPT galleries
					if ( ! isset( $data['gallery'] ) || ! is_array( $data['gallery']) ) {
						continue;
					}

					// If here, we need to create a new Envira CPT
					$cpt_args = array(
						'post_title'	=> ( !empty( $data['config']['title'] ) ? $data['config']['title'] : $post->post_title ),
						'post_status'	=> $post->post_status,
						'post_type'		=> 'envira',
						'post_author'	=> $post->post_author,
					);
					if ( ! empty( $data['config']['slug'] ) ) {
						$cpt_args['post_name'] = $data['config']['slug'];
					}
					$enviraGalleryID = wp_insert_post( $cpt_args );

					// Check gallery creation was successful
					if ( is_wp_error( $enviraGalleryID ) ) {
						// @TODO how to handle errors?
						continue;
					}

					// Get Envira Gallery Post
					$enviraPost = get_post( $enviraGalleryID );

					// Map the title and slug of the post object to the custom fields if no value exists yet.
					$data['config']['title'] = trim( strip_tags( $enviraPost->post_title ) );
					$data['config']['slug']	 = sanitize_text_field( $enviraPost->post_name );

					// Store post metadata
					update_post_meta( $enviraGalleryID, '_eg_gallery_data', $data );
					update_post_meta( $enviraGalleryID, '_eg_in_gallery', $in );
					update_post_meta( $enviraGalleryID, '_eg_gallery_old', $post->ID );
					if ( ! empty( $data['config']['slug'] ) ) {
						update_post_meta( $enviraGalleryID, '_eg_gallery_old_slug', $data['config']['slug'] );
					}

					// Remove post metadata from the original Post
					delete_post_meta( $post->ID, '_eg_gallery_data' );
					delete_post_meta( $post->ID, '_eg_in_gallery' );

					// Search for the envira shortcode in the Post content, and change its ID to the new Envira Gallery ID
					if ( has_shortcode ( $post->post_content, 'envira-gallery' ) ) {
						$pattern = get_shortcode_regex();
						if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) ) {
							foreach ( $matches[2] as $key => $shortcode ) {
								if ( $shortcode == 'envira-gallery' ) {
									// Found an envira-gallery shortcode
									// Change the ID
									$originalShortcode = $matches[0][ $key ];
									$replacementShortcode = str_replace( 'id="' . $post->ID . '"', 'id="' . $enviraGalleryID . '"', $originalShortcode );
									$post->post_content = str_replace( $originalShortcode, $replacementShortcode, $post->post_content );
									wp_update_post( $post );
								}
							}
						}
					}

					// Store a relationship between the gallery and this Post
					update_post_meta( $post->ID, '_eg_gallery_id', $enviraGalleryID );

					// Increment the counter
					$migrated_galleries++;
				}

				// Display a one time admin notice so the user knows their in-page galleries were migrated.
				if ( $migrated_galleries > 0 ) {
					add_action( 'admin_notices', array( $this, 'notice_galleries_migrated' ) );
				}
			}

			// Force the tags addon to convert any tags to the new CPT system for any galleries that have been converted to Envira post type.
			delete_option( 'envira_tags_taxonomy_migrated' );

			// Mark upgrade as complete
			update_option( 'envira_gallery_121', true );
		}
	}
	/**
	 * Displays a notice on screen when a user upgrades from Lite to Pro or Lite to Lite 1.5.x,
	 * telling them that their in-page galleries have been migrated.
	 *
	 * @since 1.5.0
	 */
	public function notice_galleries_migrated() {

		?>
		<div class="notice updated">
			<p><?php _e( '<strong>Envira Gallery:</strong> Your existing in-page Galleries can now be found by clicking on Envira Gallery in the WordPress Admin menu.', 'envira-gallery' ); ?></p>
		</div>
		<?php

	}

	/**
	 * Loads styles for all Envira-based Administration Screens.
	 *
	 * @since 1.3.1
	 *
	 * @return null Return early if not on the proper screen.
	 */
	public function admin_styles() {

		// Get current screen.
		$screen = get_current_screen();

		// If we're not on the Envira Post Type screen, only load the modal css then bail
		if ( 'envira' !== $screen->post_type && 'envira_album' !== $screen->post_type ) {

			// Load necessary admin styles.
			wp_register_style( ENVIRA_SLUG . '-admin-modal-style', plugins_url( 'assets/css/admin-modal.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
			wp_enqueue_style( ENVIRA_SLUG . '-admin-modal-style' );
			return;

		} else {

			// Proceed loading remaining admin CSS necessary admin styles.
			wp_register_style( ENVIRA_SLUG . '-admin-style', plugins_url( 'assets/css/admin.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
			wp_enqueue_style( ENVIRA_SLUG . '-admin-style' );
			wp_register_style( ENVIRA_SLUG . '-admin-modal-style', plugins_url( 'assets/css/admin-modal.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
			wp_enqueue_style( ENVIRA_SLUG . '-admin-modal-style' );

		}

		// Fire a hook to load in custom admin styles.
		do_action( 'envira_gallery_admin_styles' );

	}


	/**
	 * Force special css to load at the top of the page before most anything else
	 *
	 * @since 1.8.1
	 *
	 * @return null 
	 */
	public function custom_admin_style() {

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the Envira Post Type screen or in widgets.
		if ( 'envira' !== $screen->post_type && 'envira_album' !== $screen->post_type ) {
			return;
		}

	  echo '<style>
			#screen-meta-links {
				display: none;
			}
	  </style>';
	}

	/**
	 * Loads scripts for all Envira-based Administration Screens.
	 *
	 * @since 1.3.5
	 *
	 * @return null Return early if not on the proper screen.
	 */
	public function admin_scripts($hook) {

		// Get current screen.
		$screen = get_current_screen();

		// Include notice js, because notices might need to be displayed on pages other than Envira's
		wp_register_script( ENVIRA_SLUG . '-admin-notice-script', plugins_url( 'assets/js/min/notices-min.js', ENVIRA_FILE ), array( 'jquery' ), ENVIRA_VERSION );
		wp_enqueue_script( ENVIRA_SLUG . '-admin-notice-script' );
		wp_localize_script(
			ENVIRA_SLUG . '-admin-notice-script',
			'envira_gallery_admin',
			array(
				'ajax'					=> admin_url( 'admin-ajax.php' ),
				'dismiss_notice_nonce'	=> wp_create_nonce( 'envira-gallery-dismiss-notice' ),
			)
		);	
		wp_enqueue_style( 'envira-notice-css', plugins_url( 'assets/css/notices.css' , ENVIRA_FILE ) );

		// Bail if we're not on the Envira Post Type screen or in widgets.
		if ( 'envira' !== $screen->post_type && 'envira_album' !== $screen->post_type && 'widgets' !== $screen->base ) {
			return;
		}

		// Load necessary admin scripts
		wp_register_script( ENVIRA_SLUG . '-admin-script', plugins_url( 'assets/js/min/admin-min.js', ENVIRA_FILE ), array( 'jquery' ), ENVIRA_VERSION );
		wp_enqueue_script( ENVIRA_SLUG . '-admin-script' );

		if ( $hook == 'widgets.php' || $hook == 'post.php' || ( $hook == 'post-new.php' && $_GET['post_type'] == 'envira' ) ) {

			wp_enqueue_script( 'envira-choice-js', plugins_url( 'assets/js/lib/choices.min.js' , ENVIRA_FILE ), false, false, true );
			wp_enqueue_style( 'envira-choice-css', plugins_url( 'assets/css/choices.css' , ENVIRA_FILE ) );

		}

		// Fire a hook to load in custom admin scripts.
		do_action( 'envira_gallery_admin_scripts' );

	}

	/**
	 * Deletes the Envira gallery association for the image being deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attach_id The attachment ID being deleted.
	 */
	public function delete_gallery_association( $attach_id ) {

		$has_gallery = get_post_meta( $attach_id, '_eg_has_gallery', true );

		// Only proceed if the image is attached to any Envira galleries.
		if ( ! empty( $has_gallery ) ) {
			foreach ( (array) $has_gallery as $post_id ) {
				// Remove the in_gallery association.
				$in_gallery = get_post_meta( $post_id, '_eg_in_gallery', true );
				if ( ! empty( $in_gallery ) ) {
					if ( ( $key = array_search( $attach_id, (array) $in_gallery ) ) !== false ) {
						unset( $in_gallery[$key] );
					}
				}

				update_post_meta( $post_id, '_eg_in_gallery', $in_gallery );

				// Remove the image from the gallery altogether.
				$gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
				if ( ! empty( $gallery_data['gallery'] ) ) {
					unset( $gallery_data['gallery'][$attach_id] );
				}

				// Update the post meta for the gallery.
				update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );

				// Flush necessary gallery caches.
				envira_flush_gallery_caches( $post_id, ( ! empty( $gallery_data['config']['slug'] ) ? $gallery_data['config']['slug'] : '' ) );
			}
		}

	}

	/**
	 * Trash a gallery when the gallery post type is trashed.
	 *
	 * @since 1.0.0
	 *
	 * @param $id	The post ID being trashed.
	 * @return null Return early if no gallery is found.
	 */
	public function trash_gallery( $id ) {

		$gallery = get_post( $id );

		// Flush necessary gallery caches to ensure trashed galleries are not showing.
		envira_flush_gallery_caches( $id );

		// Return early if not an Envira gallery.
		if ( 'envira' !== $gallery->post_type ) {
			return;
		}

		// Check some gallery data exists
		$gallery_data = get_post_meta( $id, '_eg_gallery_data', true );
		if ( empty( $gallery_data ) ) {
			return;
		}

		// Set the gallery status to inactive.
		$gallery_data['status'] = 'inactive';
		update_post_meta( $id, '_eg_gallery_data', $gallery_data );

		// Allow other addons to run routines when a Gallery is trashed
		do_action( 'envira_gallery_trash', $id, $gallery_data );

	}

	/**
	 * Untrash a gallery when the gallery post type is untrashed.
	 *
	 * @since 1.0.0
	 *
	 * @param $id	The post ID being untrashed.
	 * @return null Return early if no gallery is found.
	 */
	public function untrash_gallery( $id ) {

		$gallery = get_post( $id );

		// Flush necessary gallery caches to ensure untrashed galleries are showing.
		envira_flush_gallery_caches( $id );

		// Return early if not an Envira gallery.
		if ( 'envira' !== $gallery->post_type ) {
			return;
		}

		// Set the gallery status to inactive.
		$gallery_data = get_post_meta( $id, '_eg_gallery_data', true );
		if ( empty( $gallery_data ) ) {
			return;
		}

		if ( isset( $gallery_data['status'] ) ) {
			unset( $gallery_data['status'] );
		}

		update_post_meta( $id, '_eg_gallery_data', $gallery_data );

		// Allow other addons to run routines when a Gallery is untrashed
		do_action( 'envira_gallery_untrash', $id, $gallery_data );

	}

	/**
	* Fired when a gallery is about to be permanently deleted from Trash
	*
	* Checks if the media_delete setting is enabled, and if so safely deletes
	* media that isn't being used elsewhere on the site
	*
	* @since 1.3.6.1
	*
	* @param int $post_id Post ID
	* @return null
	*/
	public function delete_gallery( $id ) {

		// Check if the media_delete setting is enabled
		$media_delete = envira_get_setting( 'media_delete' );
		if ( $media_delete != '1' ) {
			return;
		}

		// Get post
		$gallery = get_post( $id );

		// Flush necessary gallery caches to ensure untrashed galleries are showing.
		envira_flush_gallery_caches( $id );

		// Return early if not an Envira gallery.
		if ( 'envira' !== $gallery->post_type ) {
			return;
		}

		// Determine what images are inside this gallery, thanks to Envira meta-data
		$in_gallery	  = get_post_meta( $id, '_eg_in_gallery', true );
		if ( ! is_array( $in_gallery ) ) {
			return;
		}

		// Iterate through media, deleting - making sure to delete only images that aren't in another gallery
		foreach ( $in_gallery as $attach_id ) {

			$attachment		= get_post( $attach_id );
			$has_gallery	= get_post_meta( $attach_id, '_eg_has_gallery', true );

			// If post parent is the Gallery ID, and the image isn't in another gallery, we're OK to delete the image
			if ( ( $attachment->post_parent == $id || in_array( $attach_id, $in_gallery ) ) && ( count( $has_gallery ) == 1 ) ) { // the "1" should mean only one gallery - the one we are deleting
				wp_delete_attachment( $attach_id );
			}
		}

		/*
		// Get attached media
		$media = get_attached_media( 'image', $id );
		if ( ! is_array( $media ) ) {
			return;
		}

		// Iterate through media, deleting
		foreach ( $media as $image ) {
			wp_delete_attachment( $image->ID );
		}
		*/

	}

	/**
	 * I'm sure some plugins mean well, but they go a bit too far trying to reduce
	 * conflicts without thinking of the consequences.
	 *
	 * 1. Elementor doesn't play nice with instagram activation, settings, and envira things
	 *
	 * @since 1.0.0
	 */
	public function plugin_humility() {

		global $wp_scripts;

		if ( !is_admin() ) {
			return;
		}

		if ( class_exists( 'Elementor\\Admin' ) && isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'envira' ) {
			wp_dequeue_script( 'elementor-admin-app' );
		}
		
	}

}