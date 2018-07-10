<?php
/**
 * License class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */
namespace Envira\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Admin\Notices;

class License {

	/**
	 * Holds the license key.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Holds any license error messages.
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $errors = array();

	/**
	 * Holds any license success messages.
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $success = array();

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		// Don't run during an ajax request.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Don't run during a cron request.
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		// Don't run during a WP-CLI request.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return;
		}

		// Possibly verify the key.
		$this->maybe_verify_key();

		// Add potential admin notices for actions around the admin.
		add_action( 'admin_notices', array( $this, 'notices' ) );

		// Grab the license key. If it is not set (even after verification), return early.
		$this->key = envira_get_license_key();
		if ( ! $this->key ) {
			return;
		}

		// Possibly handle validating, deactivating and refreshing license keys.
		$this->maybe_validate_key();
		$this->maybe_deactivate_key();
		$this->maybe_refresh_key();

	}

	/**
	 * Maybe verifies a license key entered by the user.
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if the key fails to be verified.
	 */
	public function maybe_verify_key() {

		if ( ! $this->is_verifying_key() ) {
			return;
		}

		if ( ! $this->verify_key_action() ) {
			return;
		}

		$this->verify_key();

	}

	/**
	 * Verifies a license key entered by the user.
	 *
	 * @since 1.7.0
	 */
	public function verify_key() {

		// Perform a request to verify the key.
		$verify = $this->perform_remote_request( 'verify-key', array( 'tgm-updater-key' => $_POST['envira-license-key'] ) );

		// If it returns false, send back a generic error message and return.
		if ( ! $verify ) {
			$this->errors[] = __( 'There was an error connecting to the remote key API. Please try again later.', 'envira-gallery' );
			return;
		}

		// If an error is returned, set the error and return.
		if ( ! empty( $verify->error ) ) {
			$this->errors[] = $verify->error;
			return;
		}

		// Otherwise, our request has been done successfully. Update the option and set the success message.
		$option                  = get_option( 'envira_gallery' );
		$option['key']           = $_POST['envira-license-key'];
		$option['type']          = isset( $verify->type ) ? $verify->type : $option['type'];
		$option['is_expired']    = false;
		$option['is_disabled']   = false;
		$option['is_invalid']    = false;
		$this->success[]         = isset( $verify->success ) ? $verify->success : __( 'Congratulations! This site is now receiving automatic updates.', 'envira-gallery' );

		update_option( 'envira_gallery', $option );

		wp_clean_plugins_cache( true );

	}

	/**
	 * Flag to determine if a key is being verified.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if being verified, false otherwise.
	 */
	public function is_verifying_key() {

		return isset( $_POST['envira-license-key'] ) && isset( $_POST['envira-gallery-verify-submit'] );

	}

	/**
	 * Verifies nonces that allow key verification.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if nonces check out, false otherwise.
	 */
	public function verify_key_action() {

		return isset( $_POST['envira-gallery-verify-submit'] ) && wp_verify_nonce( $_POST['envira-gallery-key-nonce'], 'envira-gallery-key-nonce' );

	}

	/**
	 * Maybe validates a license key entered by the user.
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if the transient has not expired yet.
	 */
	public function maybe_validate_key() {

		// Only run every 12 hours.
		$timestamp = get_option( 'envira_gallery_license_updates' );

		if ( ! $timestamp ) {

			$timestamp = strtotime( '+24 hours' );

			update_option( 'envira_gallery_license_updates', $timestamp );

			$this->validate_key();

		} else {

			$current_timestamp = time();

			if ( $current_timestamp < $timestamp ) {
				return;

			} else {

				update_option( 'envira_gallery_license_updates', strtotime( '+24 hours' ) );

				$this->validate_key();
			}

		}

	}

	/**
	 * Validates a license key entered by the user.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $forced Force to set contextual messages (false by default).
	 */
	public function validate_key( $forced = false ) {

		$validate = $this->perform_remote_request( 'validate-key', array( 'tgm-updater-key' => $this->key ) );

		// If there was a basic API error in validation, only set the transient for 10 minutes before retrying.
		if ( ! $validate ) {
			// If forced, set contextual success message.
			if ( $forced ) {
				$this->errors[] = __( 'There was an error connecting to the remote key API. Please try again later.', 'envira-gallery' );
			}

			return;
		}

		// If a key or author error is returned, the license no longer exists or the user has been deleted, so reset license.
		if ( isset( $validate->key ) || isset( $validate->author ) ) {
			$option                  = get_option( 'envira_gallery' );
			$option['is_expired']  = false;
			$option['is_disabled'] = false;
			$option['is_invalid']  = true;
			update_option( 'envira_gallery', $option );
			return;
		}

		// If the license has expired, set the transient and expired flag and return.
		if ( isset( $validate->expired ) ) {
			$option                  = get_option( 'envira_gallery' );
			$option['is_expired']  = true;
			$option['is_disabled'] = false;
			$option['is_invalid']  = false;
			update_option( 'envira_gallery', $option );
			return;
		}

		// If the license is disabled, set the transient and disabled flag and return.
		if ( isset( $validate->disabled ) ) {
			$option                  = get_option( 'envira_gallery' );
			$option['is_expired']  = false;
			$option['is_disabled'] = true;
			$option['is_invalid']  = false;
			update_option( 'envira_gallery', $option );
			return;
		}

		// If forced, set contextual success message.
		if ( $forced ) {
			$this->success[] = __( 'Congratulations! Your key has been refreshed successfully.', 'envira-gallery' );
		}

		// Otherwise, our check has returned successfully. Set the transient and update our license type and flags.
		$option                 = get_option( 'envira_gallery' );
		$option['type']           = isset( $validate->type ) ? $validate->type : $option['type'];
		$option['is_expired']    = false;
		$option['is_disabled'] = false;
		$option['is_invalid']    = false;
		update_option( 'envira_gallery', $option );

	}

	/**
	 * Maybe deactivates a license key entered by the user.
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if the key fails to be deactivated.
	 */
	public function maybe_deactivate_key() {

		if ( ! $this->is_deactivating_key() ) {
			return;
		}

		if ( ! $this->deactivate_key_action() ) {
			return;
		}

		$this->deactivate_key();

	}

	/**
	 * Deactivates a license key entered by the user.
	 *
	 * @since 1.7.0
	 */
	public function deactivate_key() {

		// Perform a request to deactivate the key.
		$deactivate = $this->perform_remote_request( 'deactivate-key', array( 'tgm-updater-key' => $_POST['envira-license-key'] ) );

		// If it returns false, send back a generic error message and return.
		if ( ! $deactivate ) {
			$this->errors[] = __( 'There was an error connecting to the remote key API. Please try again later.', 'envira-gallery' );
			return;
		}

		// If an error is returned, set the error and return.
		if ( ! empty( $deactivate->error ) ) {
			$this->errors[] = $deactivate->error;
			return;
		}

		// Otherwise, our request has been done successfully. Reset the option and set the success message.
		$this->success[] = isset( $deactivate->success ) ? $deactivate->success : __( 'Congratulations! You have deactivated the key from this site successfully.', 'envira-gallery' );
		update_option( 'envira_gallery', \Envira_Gallery::default_options() );

	}

	/**
	 * Flag to determine if a key is being deactivated.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if being verified, false otherwise.
	 */
	public function is_deactivating_key() {

		return isset( $_POST['envira-license-key'] ) && isset( $_POST['envira-gallery-deactivate-submit'] );

	}

	/**
	 * Verifies nonces that allow key deactivation.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if nonces check out, false otherwise.
	 */
	public function deactivate_key_action() {

		return isset( $_POST['envira-gallery-deactivate-submit'] ) && wp_verify_nonce( $_POST['envira-gallery-key-nonce'], 'envira-gallery-key-nonce' );

	}

	/**
	 * Maybe refreshes a license key.
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if the key fails to be refreshed.
	 */
	public function maybe_refresh_key() {

		if ( ! $this->is_refreshing_key() ) {
			return;
		}

		if ( ! $this->refresh_key_action() ) {
			return;
		}

		// Refreshing is simply a word alias for validating a key. Force true to set contextual messages.
		$this->validate_key( true );

	}

	/**
	 * Flag to determine if a key is being refreshed.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if being refreshed, false otherwise.
	 */
	public function is_refreshing_key() {

		return isset( $_POST['envira-license-key'] ) && isset( $_POST['envira-gallery-refresh-submit'] );

	}

	/**
	 * Verifies nonces that allow key refreshing.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if nonces check out, false otherwise.
	 */
	public function refresh_key_action() {

		return isset( $_POST['envira-gallery-refresh-submit'] ) && wp_verify_nonce( $_POST['envira-gallery-key-nonce'], 'envira-gallery-key-nonce' );

	}

	/**
	 * Outputs any notices generated by the class.
	 *
	 * @since 1.7.0
	 */
	public function notices() {

		// Grab the option and output any nag dealing with license keys.
		$key    = envira_get_license_key();
		$option = get_option( 'envira_gallery' );
		$notices = new Notices;

		// If there is no license key, output nag about ensuring key is set for automatic updates.
		if ( ! $key ) :

			$message = sprintf( __( '<strong>Envira Gallery</strong>: No valid license key has been entered, so automatic updates for Envira Gallery have been turned off. <a href="%s">Please click here to enter your license key and begin receiving automatic updates.</a>', 'envira-gallery' ), esc_url( add_query_arg( array( 'post_type' => 'envira', 'page' => 'envira-gallery-settings' ), admin_url( 'edit.php' ) ) ) );

			$notices->display_inline_notice( 'warning-license-key', false, $message, 'error', $button_text = '', $button_url = '', true, DAY_IN_SECONDS );
			
		endif;

		// If a key has expired, output nag about renewing the key.
		if ( isset( $option['is_expired'] ) && $option['is_expired'] ) :

			$message = sprintf( __( '<strong>Envira Gallery</strong>: Your license key for Envira Gallery has expired. <a href="%s" target="_blank">Please click here to renew your license key and continue receiving automatic updates.</a>', 'envira-gallery' ), 'https://enviragallery.com/login/' );

			$notices->display_inline_notice( 'warning-invalid-license-key', false, $message, 'error', $button_text = '', $button_url = '', true, DAY_IN_SECONDS );


		endif;

		// If a key has been disabled, output nag about using another key.
		if ( isset( $option['is_disabled'] ) && $option['is_disabled'] ) :

			$message = sprintf( __( '<strong>Envira Gallery</strong>: Your license key for Envira Gallery has been disabled. Please use a different key to continue receiving automatic updates.</a>', 'envira-gallery' ) );

			$notices->display_inline_notice( 'warning-invalid-license-key', false, $message, 'error', $button_text = '', $button_url = '', true, DAY_IN_SECONDS );

		endif;

		// If a key is invalid, output nag about using another key.
		if ( isset( $option['is_invalid'] ) && $option['is_invalid'] ) :

			$message = sprintf( __( '<strong>Envira Gallery</strong>: Your license key for Envira Gallery is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.</a>', 'envira-gallery' ) );

			$notices->display_inline_notice( 'warning-invalid-license-key', false, $message, 'error', $button_text = '', $button_url = '', true, DAY_IN_SECONDS );

		endif;

		// If there are any license errors, output them now.
		if ( ! empty( $this->errors ) ) :
		?>
		<div class="error">
			<p><?php echo implode( '<br>', $this->errors ); ?></p>
		</div>
		<?php
		endif;

		// If there are any success messages, output them now.
		if ( ! empty( $this->success ) ) :
		?>
		<div class="updated">
			<p><?php echo implode( '<br>', $this->success ); ?></p>
		</div>
		<?php
		endif;

	}

	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @since 1.7.0
	 *
	 * @param string $action        The name of the $_POST action var.
	 * @param array $body           The content to retrieve from the remote URL.
	 * @param array $headers        The headers to send to the remote URL.
	 * @param string $return_format The format for returning content from the remote URL.
	 * @return string|bool          Json decoded response on success, false on failure.
	 */
	public function perform_remote_request( $action, $body = array(), $headers = array(), $return_format = 'json' ) {

		// Build the body of the request.
		$body = wp_parse_args(
			$body,
			array(
				'tgm-updater-action'     => $action,
				'tgm-updater-key'        => $this->key,
				'tgm-updater-wp-version' => get_bloginfo( 'version' ),
				'tgm-updater-referer'    => site_url()
			)
		);
		$body = http_build_query( $body, '', '&' );

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Content-Type'   => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body )
			)
		);

		// Setup variable for wp_remote_post.
		$post = array(
			'headers'   => $headers,
			'body'      => $body
		);

		// Perform the query and retrieve the response.
		$response      = wp_remote_post( 'https://enviragallery.com', $post );
		$response_code = wp_remote_retrieve_response_code( $response ); /* log this for API issues */
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( 200 != $response_code || is_wp_error( $response_body ) ) {
			return false;
		}

		// Return the json decoded content.
		return json_decode( $response_body );

	}

}