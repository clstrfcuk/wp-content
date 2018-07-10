<?php
/**
 * Widgets class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author	Thomas Griffin
 */
namespace Envira\Widgets;

 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget extends \ WP_Widget {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		// Widget Name
		$widget_name = __( 'Envira Gallery', 'envira-gallery' );
		$widget_name = apply_filters( 'envira_gallery_widget_name', $widget_name );

		// Opts
		$widget_ops = array(
			'classname'	  => 'envira-gallery',
			'description' => __( 'Place an Envira gallery into a widgetized area.', 'envira-gallery' )
		);
		$widget_ops = apply_filters( 'envira_gallery_widget_ops', $widget_ops );

		// Control Options
		$control_ops = array(
			'id_base' => 'envira-gallery',
			'height'  => 350,
			'width'	  => 225
		);
		$control_ops = apply_filters( 'envira_gallery_widget_control_ops', $control_ops );

		// Init
		parent::__construct( 'envira-gallery', $widget_name, $widget_ops, $control_ops );

		add_action( 'wp_ajax_envira_widget_get_galleries', array( $this, 'widget_get_galleries' ) );

	}

	/**
	 * Get galleries for widget.
	 *
	 * @since 1.0.0
	 */
	public function widget_get_galleries() {

		$galleries	= envira_get_galleries( false );
		$gallery_array = array();

		if ( is_array( $galleries ) ) {
			foreach ( $galleries as $gallery ) {

				if ( !isset($gallery['id']) || !$gallery['id'] ) {
					continue;
				}

				// Instead of pulling the title from config, attempt to pull it from the gallery post first
				$gallery_post = get_post( $gallery['id'] );

				if ( $gallery_post && ! empty( $gallery_post->post_title ) ) {
					$title = $gallery_post->post_title;
				} else if ( ! empty( $gallery['config']['title'] ) ) {
					$title = $gallery['config']['title'];
				} else if ( ! empty( $gallery['config']['slug'] ) ) {
					$title = $gallery['config']['slug'];
				} else {
					$title = sprintf( __( 'Gallery ID #%s', 'envira-gallery' ), $gallery['id'] );
				}

				$gallery_array[] = array( 'gallery_title' => $title, 'gallery_id' => '' . $gallery['id'] . '' );

			}
		}

		$string = array( 'galleries' => $gallery_array );
		
		echo json_encode($string);
		exit;
	}

   /**
	 * Outputs the widget within the widgetized area.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args	  The default widget arguments.
	 * @param array $instance The input settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {

		// Extract arguments into variables.
		extract( $args );

		$gallery_id = $title = false;

		if ( isset( $instance['title'] ) ) {
			$title		= apply_filters( 'widget_title', $instance['title'] );
		}
		if ( isset( $instance['envira_gallery_id'] ) ) {
			$gallery_id = $instance['envira_gallery_id'];
		}

		if ( !$gallery_id ) { return; }

		do_action( 'envira_gallery_widget_before_output', $args, $instance );

		echo $before_widget;

		do_action( 'envira_gallery_widget_before_title', $args, $instance );

		// If a title exists, output it.
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		do_action( 'envira_gallery_widget_before_gallery', $args, $instance );

		// If a gallery has been selected, output it.
		if ( $gallery_id ) {
			envira_gallery( $gallery_id );
		}

		do_action( 'envira_gallery_widget_after_gallery', $args, $instance );

		echo $after_widget;

		do_action( 'envira_gallery_widget_after_output', $args, $instance );

	}

	/**
	 * Sanitizes and updates the widget.
	 *
	 * @since 1.7.0
	 *
	 * @param array $new_instance The new input settings for the current widget instance.
	 * @param array $old_instance The old input settings for the current widget instance.
	 */
	public function update( $new_instance, $old_instance ) {

		// Set $instance to the old instance in case no new settings have been updated for a particular field.
		$instance = $old_instance;

		// Sanitize user inputs.
		$instance['title']			   = trim( $new_instance['title'] );
		$instance['envira_gallery_id'] = absint( $new_instance['envira_gallery_id'] );

		return apply_filters( 'envira_gallery_widget_update_instance', $instance, $new_instance );

	}

	/**
	 * Outputs the widget form where the user can specify settings.
	 *
	 * @since 1.7.0
	 *
	 * @param array $instance The input settings for the current widget instance.
	 */
	public function form( $instance ) {

		// Get all available galleries and widget properties.
		$galleries	= _envira_get_galleries( false );

		$title		= isset( $instance['title'] ) ? $instance['title'] : '';
		$gallery_id = isset( $instance['envira_gallery_id'] ) ? $instance['envira_gallery_id'] : false;

		do_action( 'envira_gallery_widget_before_form', $instance );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'envira-gallery' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%;" />
		</p>
		<?php do_action( 'envira_gallery_widget_middle_form', $instance ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'envira_gallery_id' ); ?>"><?php _e( 'Gallery', 'envira-gallery' ); ?></label>

			<select class="form-control" id="<?php echo esc_attr( $this->get_field_id( 'envira_gallery_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'envira_gallery_id' ) ); ?>" style="width: 100%;">
			<!--<select class="form-control" name="choices-single-remote-fetch" id="choices-single-remote-fetch">-->
				<?php 

				if ( is_array( $galleries ) ) {
					foreach ( $galleries as $gallery ) {
						if ( isset( $gallery['id'] ) ) {
							$title = get_the_title( $gallery['id'] );
							if ( $gallery_id && $gallery['id'] == $gallery_id ) { 
								echo '<option selected="selected" value="' . absint( $gallery['id'] ) . '">' . $title . '</option>'; 
							} else {
								echo '<option value="' . absint( $gallery['id'] ) . '">' . $title . '</option>'; 
							}
						}
					}
				}

				?>
			</select>

			<?php /* <select id="<?php echo esc_attr( $this->get_field_id( 'envira_gallery_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'envira_gallery_id' ) ); ?>" style="width: 100%;">
				<?php
				if ( is_array( $galleries ) ) {
					foreach ( $galleries as $gallery ) {

						// Instead of pulling the title from config, attempt to pull it from the gallery post first
						$gallery_post = get_post( $gallery['id'] );

						if ( ! empty( $gallery_post->post_title ) ) {
							$title = $gallery_post->post_title;
						} else if ( ! empty( $gallery['config']['title'] ) ) {
							$title = $gallery['config']['title'];
						} else if ( ! empty( $gallery['config']['slug'] ) ) {
							$title = $gallery['config']['title'];
						} else {
							$title = sprintf( __( 'Gallery ID #%s', 'envira-gallery' ), $gallery['id'] );
						}

						echo '<option value="' . absint( $gallery['id'] ) . '"' . selected( absint( $gallery['id'] ), $gallery_id, false ) . '>' . $title . '</option>';
					}
				}
				?>
			</select> */ ?>
		</p>
		<?php
		do_action( 'envira_gallery_widget_after_form', $instance );

	}

}