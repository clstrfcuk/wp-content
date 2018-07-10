<?php
/**
 * Importers class.
 *
 * @since 1.7.0
 *
 * @package Envira Gallery
 * @author	Envira Gallery Team
 */
namespace Envira\Admin;

 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Importers {

	/**
	 * Holds the submenu pagehook.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $hook;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		// Add custom settings submenu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );

	}

	/**
	 * Register the Settings submenu item for Soliloquy.
	 *
	 * @since 1.7.0
	 */
	public function admin_menu() {


		$importers = envira_get_importers();

		if ( ! empty( $importers ) ){

			 // Register the submenu.
			 $this->hook = add_submenu_page(
				 'edit.php?post_type=envira',
				 esc_attr__( 'Envira Importers', 'envira-gallery' ),
				 esc_attr__( 'Import', 'envira-gallery' ),
				 apply_filters( 'envira_menu_cap', 'manage_options' ),
				 ENVIRA_SLUG . '-importers',
				 array( $this, 'import_page' )
			 );

			 // If successful, load admin assets only on that page and check for importers refresh.
			 if ( $this->hook ) {

				 add_action( 'load-' . $this->hook, array( $this, 'settings_page_assets' ) );

			 }

		}

	}

	 /**
	 * Outputs a WordPress style notification to tell the user their settings were saved
	 *
	 * @since 1.7.0
	 */
	public function updated_settings() {
		 ?>
		 <div class="updated">
			<p><?php esc_html_e( 'Settings updated.', 'envira-gallery' ); ?></p>
		</div>
		 <?php

	}

	/**
	 * Loads assets for the settings page.
	 *
	 * @since 1.7.0
	 */
	public function settings_page_assets() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

	}

	/**
	 * Register and enqueue settings page specific CSS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_styles() {

		wp_register_style( ENVIRA_SLUG . '-importers-style', plugins_url( 'assets/css/importers.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
		wp_register_style( ENVIRA_SLUG . '-select2', plugins_url( 'assets/css/select2.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );

		wp_enqueue_style( ENVIRA_SLUG . '-importers-style' );
		wp_enqueue_style( ENVIRA_SLUG . '-select2' );

		$active_section	 = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'general';

		if ( $active_section === 'general' ) {

			// Run a hook to load in custom scripts.
			do_action( 'envira_importers_styles' );

		} else {

			do_action( 'envira_importers_styles_' . $active_section );

		}
	}

	/**
	 * Register and enqueue settings page specific JS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_scripts() {

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_register_script( ENVIRA_SLUG . '-select2', plugins_url( 'assets/js/min/select2.full-min.js', ENVIRA_FILE ), array(), ENVIRA_VERSION, true );
		wp_enqueue_script( ENVIRA_SLUG . '-select2' );

		wp_register_script( ENVIRA_SLUG . '-importers-script', plugins_url( 'assets/js/min/importer-min.js', ENVIRA_FILE ), array( 'jquery', 'jquery-ui-tabs' ), ENVIRA_VERSION, true );
		wp_enqueue_script( ENVIRA_SLUG . '-importers-script' );



		$active_section	 = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'general';
		if ( $active_section === 'general' ) {
		// Run a hook to load in custom scripts.
		do_action( 'envira_importers_scripts' );

		} else {

			do_action( 'envira_importers_scripts_' . $active_section );

		}
	}

	/**
	 * Callback to output the Soliloquy settings page.
	 *
	 * @since 1.7.0
	 */
	public function import_page() {

		$importers = envira_get_importers();
		$active_section	 = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'general';

		do_action('envira_head');

		?>

		<?php if ( $active_section === 'general' ): ?>

		<div id="importer-heading" class="subheading clearfix">
			<h2><?php _e( 'Envira Gallery Importers', 'envira-gallery' ); ?></h2>
			<form id="add-on-search">
				<span class="spinner"></span>
				<input id="add-on-searchbox" name="envira-addon-search" value="" placeholder="<?php _e( 'Search Envira Addons', 'envira-gallery' ); ?>" />
				<select id="envira-filter-select">
					<option value="asc"><?php _e( 'Sort Ascending (A-Z)', 'envira-gallery' ); ?></option>
					<option value="desc"><?php _e( 'Sort Descending (Z-A)', 'envira-gallery' ); ?></option>
				</select>
			</form>
		</div>

		<div id="envira-importers" class="wrap">

			 <h1 class="envira-hideme"></h1>

			<div class="envira-clearfix"></div>

			<div id="envira-importers" class="envira envira-clear">

				<?php $i = 0; foreach ( (array) $importers as $id => $info ) : $class = 0 === $i ? 'envira-active' : ''; ?>

					 <div class="envira-importer" data-importer-title="Carousel importer" data-importer-status="inactive">

						 <div class="envira-importer-content">

							 <h3 class="envira-importer-title"><?php echo $info['title'] ?></h3>

							 <img class="envira-importer-thumb" src="<?php echo $info['thumb'] ?>" width="300px" height="250px" alt="<?php echo $info['title'] ?>">

							 <p class="envira-importer-excerpt"><?php echo $info['description'] ?></p>

						</div>

						<div class="envira-importer-footer">

							<div class="envira-importer-inactive envira-importer-message">

								<div class="envira-importer-action">

									<a class="envira-icon-cloud-download button button-envira-secondary envira-importer-action-button envira-activate-importer" href="<?php echo esc_url( $info['url'] ) ?>">
										<i class="envira-cloud-download"></i>
										<?php _e( 'Import', 'envira-gallery' ); ?>
									</a>

								</div>

							</div>

						</div>

					</div>

				<?php $i++; endforeach; ?>

			</div>

		</div>

		<?php
		else:

			do_action( 'envira_importer_section_' . $active_section );

		endif;

	}


}