<?php
/**
 * Updater class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

namespace Envira\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Admin\License;

class Updater {

	/**
	 * Plugin name.
	 *
	 * @since 1.7.0
	 *
	 * @var bool|string
	 */
	public $plugin_name = false;

	/**
	 * Plugin slug.
	 *
	 * @since 1.7.0
	 *
	 * @var bool|string
	 */
	public $plugin_slug = false;

	/**
	 * Plugin path.
	 *
	 * @since 1.7.0
	 *
	 * @var bool|string
	 */
	public $plugin_path = false;

	/**
	 * URL of the plugin.
	 *
	 * @since 1.7.0
	 *
	 * @var bool|string
	 */
	public $plugin_url = false;

	/**
	 * Remote URL for getting plugin updates.
	 *
	 * @since 1.7.0
	 *
	 * @var bool|string
	 */
	public $remote_url = false;

	/**
	 * License key for the plugin.
	 *
	 * @since 1.7.0
	 *
	 * @var bool|string
	 */
	public $key = false;

	/**
	 * Holds the update data returned from the API.
	 *
	 * @since 2.1.3
	 *
	 * @var bool|object
	 */
	public $update = false;

	private $api_url    = 'https://enviragallery.com/';
	private $api_data   = array();
	private $name       = '';
	private $slug       = '';
	private $version    = '';
	private $wp_overide = false;
	private $beta       = false;
	private $cache_key  = '';

	/**
	 * Holds the plugin info details for the update.
	 *
	 * @since 2.1.3
	 *
	 * @var bool|object
	 */
	public $info = false;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	// public function __construct() {
	public function __construct( array $config ) {

		// If the user cannot update plugins, stop processing here.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}
		// Set class properties.
		$accepted_args = array(
			'plugin_name',
			'plugin_slug',
			'plugin_path',
			'plugin_url',
			'remote_url',
			'version',
			'key'
		);
		foreach ( $accepted_args as $arg ) {
			$this->$arg = $config[$arg];
		}
		$this->name = $config['plugin_name'];
		$this->slug = $config['plugin_slug'];
		$this->version = $config['version'];
		$this->wp_override = false;
		$this->beta = false;
		$this->key = $config['key'];
		$this->cache_key = 'eg_' . md5( serialize( $this->slug . $config['key'] ) );
		$this->plugin_path = $config['plugin_path'];

		// Load the updater hooks and filters.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
		add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );

	}


	/**
	 * Infuse plugin update details when WordPress runs its update checker.
	 *
	 * @since 1.7.0
	 *
	 * @param object $value  The WordPress update object.
	 * @return object $value Amended WordPress update object on success, default if object is empty.
	 */
	public function check_update( $_transient_data ) {

		global $pagenow;

		// If no update object exists, return early.
		if ( empty( $_transient_data ) ) {
			return $_transient_data;
		}
		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			return $_transient_data;
		}

		$version_info = $this->get_cached_info();

		// Run update check by pinging the external API. If it fails, return the default update object.
		if ( false === $version_info ) {

			$version_info = $this->perform_remote_request( 'get-plugin-update', array( 'tgm-updater-plugin' => $this->slug ) );

			$this->set_cache_info( $version_info );

		}
		// Infuse the update object with our data if the version from the remote API is newer.
		if ( isset( $version_info->new_version ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {
			// The $plugin_update object contains new_version, package, slug and last_update keys.
			$_transient_data->response[$this->plugin_path] = $version_info;
		}

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$_transient_data->response[ $this->name ] = $version_info;

			}

			$_transient_data->last_checked           = current_time( 'timestamp' );
			$_transient_data->checked[ $this->name ] = $this->version;

		}

		return $_transient_data;

	}

	/**
	 * Disables SSL verification to prevent download package failures.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args  Array of request args.
	 * @param string $url  The URL to be pinged.
	 * @return array $args Amended array of request args.
	 */
	public function http_request_args( $args, $url ) {

		// If this is an SSL request and we are performing an upgrade routine, disable SSL verification.
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'tgm-updater-action=get-plugin-update' ) ) {
			$args['sslverify'] = false;
		}

		return $args;

	}

	/**
	 * Filters the plugins_api function to get our own custom plugin information
	 * from our private repo.
	 *
	 * @since 1.7.0
	 *
	 * @param object $api    The original plugins_api object.
	 * @param string $action The action sent by plugins_api.
	 * @param array $args    Additional args to send to plugins_api.
	 * @return object $api   New stdClass with plugin information on success, default response on failure.
	 */
	public function plugins_api( $api, $action = '', $args = null ) {

		$plugin = ( 'plugin_information' == $action ) && isset( $args->slug ) && ( $this->slug == $args->slug );
		// If our plugin matches the request, set our own plugin data, else return the default response.
		if ( $plugin ) {
			return $this->set_plugins_api( $api );
		} else {
			return $api;
		}

	}

	/**
	 * Pings a remote API to retrieve plugin information for WordPress to display.
	 *
	 * @since 1.7.0
	 *
	 * @param object $default_api The default API object.
	 * @return object $api        Return custom plugin information to plugins_api.
	 */
	public function set_plugins_api( $default_api ) {

		$version_info = $this->get_cached_info();

		// Perform the remote request to retrieve our plugin information. If it fails, return the default object.
		if ( empty( $version_info ) ) {

			$version_info = $this->perform_remote_request( 'get-plugin-update', array( 'tgm-updater-plugin' => $this->slug ) );

			$this->set_cache_info( $version_info );

			if ( ! $version_info || ! empty( $version_info->error ) ) {
				$version_info = false;
				return $default_api;
			}
		}

		// Create a new stdClass object and populate it with our plugin information.
		$api                        = new \stdClass;
		$api->name                  = isset( $version_info->name )           ? $version_info->name           : '';
		$api->slug                  = isset( $version_info->slug )         ? $version_info->slug           : '';
		$api->version               = isset( $version_info->version )      ? $version_info->version        : '';
		$api->author                = isset( $version_info->author )         ? $version_info->author         : '';
		$api->author_profile        = isset( $version_info->author_profile ) ? $version_info->author_profile : '';
		$api->requires              = isset( $version_info->requires )       ? $version_info->requires       : '';
		$api->tested                = isset( $version_info->tested )         ? $version_info->tested         : '';
		$api->last_updated          = isset( $version_info->last_updated )   ? $version_info->last_updated   : '';
		$api->homepage              = isset( $version_info->homepage )       ? $version_info->homepage       : '';
		$api->sections['changelog'] = isset( $version_info->changelog )      ? $version_info->changelog      : '';
		$api->download_link         = isset( $version_info->download_link )  ? $version_info->download_link  : '';

		// Return the new API object with our custom data.
		return $api;

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
		$content_length = strlen( $body );

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Content-Type'   => 'application/x-www-form-urlencoded',
				'Content-Length' => $content_length,
			)
		);

		// Setup variable for wp_remote_post.
		$post = array(
			'headers' => $headers,
			'body'    => $body
		);

		// Perform the query and retrieve the response.
		$response      = wp_remote_post( esc_url_raw( $this->api_url ), $post );
		$response_code = wp_remote_retrieve_response_code( $response ); /* log this for API issues */
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( 200 != $response_code || is_wp_error( $response_body ) ) {
			return false;
		}

		// Return the json decoded content.
		return json_decode( $response_body );

	}

	public function get_cached_info( $cache_key = '' ) {

		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		return json_decode( $cache['value'] );

	}

	public function set_cache_info( $value = '', $cache_key = '' ){

		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+3 hours', current_time( 'timestamp' ) ),
			'value'   => json_encode( $value )
		);

		update_option( $cache_key, $data, 'no' );

	}
	public function verify_ssl(){
		return (bool) apply_filters( 'envira_api_request_verify_ssl', true, $this );
	}
}