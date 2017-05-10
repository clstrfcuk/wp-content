<?php

// =============================================================================
// EMAIL-CONVERTKIT/FUNCTIONS/PROVIDER.PHP
// -----------------------------------------------------------------------------
// Provides the specific logic for integration with the desired service. It
// extends the base provider class to ensure compatibility.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_ConvertKit extends X_Email_Provider {

  //
  // Properties.
  //

  protected $api_wrapper, $api_key, $api_opts;


  //
  // Setup.
  //

  function setup() {

    $this->register_validators( array(
       'ck_api_key' => array( $this, 'validate_ck_api_key' ),
    ) );

  }


  //
  // Integration.
  //

  function settings_page() {

    $ck_list_refresh_url = add_query_arg( array( '_list_refresh_nonce' => wp_create_nonce( 'ck_list_refresh' ) ) );
    $this->plugin->set_transport( 'ck_list_refresh_url', $ck_list_refresh_url );

    if ( isset( $_REQUEST['_list_refresh_nonce'] ) ) {
      if ( wp_verify_nonce( $_REQUEST['_list_refresh_nonce'], 'ck_list_refresh' ) ) {
        $refresh = $this->refresh_list_cache();
        if ( isset( $refresh['message'] ) ) {
          $this->plugin->set_transport( 'ck_message', $refresh['message'] );
        }
      } else {
        wp_die( __( 'Permission denied. Unable to refresh list', '__x__' ) );
      }
    }

    if ( ! $this->plugin->options->get( 'ck_api_key' )) {
      unset( $this->config['settings_metaboxes']['ck_lists'] );
    }

  }


  //
  // API key validation.
  //

  function validate_ck_api_key( $value ) {

    $validate = $this->validate_api_key( $value );
    $this->ck_api_key_validate_failed = is_wp_error( $validate );

    return $this->ck_api_key_validate_failed;
  }


  //
  // Before saving.
  //

  function before_save() {

    $slug = $this->plugin->get_slug();

    if ( ( isset( $this->ck_api_key_validate_failed ) && $this->ck_api_key_validate_failed ) ) {
      $this->plugin->options->set( 'ck_api_key', '' );
      $this->plugin->options->set( 'ck_list_cache', array() );
    } elseif( $this->plugin->options->was_modified( 'ck_api_key') ) {
      $this->refresh_list_cache();
    }

  }


  //
  // Get an instance of the API wrapper
  //

  function get_wrapper() {

    return ( isset( $this->api_wrapper ) ) ? $this->api_wrapper : $this->make_api_wrapper();

  }


  //
  // Code to interface with MailChimp API.
  //

  function make_api_wrapper( $api_key = '' ) {

    if ( ! class_exists( 'ConvertKit' ) ) {
      require_once( "{$this->path}/functions/convertkit/class-convertkit.php" );
    }
    if ( $api_key == '' ) {
      $api_key = $this->plugin->options->get( 'ck_api_key' );
    }
    try {
      $wrapper = new ConvertKit( $api_key );
      $this->api_wrapper = $wrapper;
    } catch ( Exception $e ) {
      return new WP_Error( 'x-convertkit', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );
    }

    return $this->api_wrapper;

  }


  //
  // Validate the API key
  //

  function validate_api_key( $key ) {

    try {
      $ck_api = $this->make_api_wrapper( $key );
      $result = $ck_api->get('forms');
      if ( array_key_exists('error', $result)) {
        throw new Exception( $result['message'], 400 );
     }
    } catch ( Exception $e ) {
      $result = new WP_Error( 'x-convertkit', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );
    }

    return $result;

  }


  //
  // Retrieve avaliable lists
  //

  function retrieve_lists() {

    try {
      $ck_api = $this->get_wrapper();
      $result = $ck_api->get( 'forms' );
      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }
    } catch ( Exception $e ) {
      return new WP_Error( 'x-convertkit', sprintf( __( 'Error attempting to retrieve ConvertKit List: [%s]', '__x__' ), $e->getMessage() ) );
    }

    return $result;

  }


  //
  // Retrieve custom fields for each list
  //

  function retrieve_custom_fields( $list_id ) {

    try {
      $mc_api = $this->get_wrapper();
      $result = $mc_api->get( "custom_fields" );

      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }

    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), $e->getMessage() ) );
    }

    return $result->custom_fields;

  }


  //
  // Force avaliable lists refreshing
  //

  function refresh_list_cache() {

    $response = array();
    $result   = $this->retrieve_lists();
    if ( is_wp_error( $result ) ) {
      $response['message'] = $result->get_error_message();
    } else {
      foreach( $result->forms as $key => $list ) {
        $result->forms[ $key ]->custom_fields = $this->retrieve_custom_fields( $list->id );
      }
      $lists = $this->reduce_list_result( $result );
      if ( empty( $lists ) ) {
        $response['message'] = __( 'Your account doesn\'t have any ConvertKit lists. You should create some from your ConvertKit admin page.', '__x__' );
      }
      if ( $lists == $this->plugin->options->get( 'ck_list_cache' ) ) {
        $response['message'] = __( 'Refresh complete. No updates at this time.', '__x__' );
      } else {
        $response['message'] = __( 'Lists updated!', '__x__' );
      }
      $this->plugin->options->set( 'ck_list_cache', $lists, true );
    }

    return $response;

  }

  //
  // Create a reduced array of avaliable lists
  //

  function reduce_list_result( $results ) {

    $lists = array();
    foreach ( $results->forms as $list ) {
      $lists[$list->id] = array(
        'id'             => $list->id,
        'provider'       => $this->config['name'],
        'provider_title' => $this->config['title'],
        'name'           => $list->name,
      );
      foreach($list->custom_fields as $cf ) {
        if ( in_array( $cf->key, array('last_name') ) ) {
          continue;
        }
        $lists[$list->id]['custom_fields'][] = array(
          'name'          => $cf->key,
          'label'         => $cf->label,
          'type'          => 'text',
          'required'      => false,
          'default_value' => '',
          'choices'       => array(),
          'options'       => array(),
        );
      }
    }

    return $lists;

  }


  //
  // Get a user-friendly array of lists from cache
  //

  function get_normalized_list() {

    $items = array();
    $cache = $this->plugin->options->get( 'ck_list_cache' );
    foreach ( $cache as $item ) {
      $items[] = array(
        'id'             => $item['id'],
        'name'           => $item['name'],
        'provider'       => $this->config['name'],
        'provider_title' => $this->config['title']
      );
    }

    return $items;

  }


  //
  // Subscribe a user to a list
  //

  function subscribe( $list_id, $user_data ) {

    try {
      $data = $this->make_subscription_vars( $user_data );
      $ck_api = $this->get_wrapper();
      $result = $ck_api->post( "forms/$list_id/subscribe", $data );
      if ( array_key_exists( 'error', $result ) && ! empty( $result['error'] ) ) {
        throw new Exception( $result['error'], 400 );
      }
    } catch ( Exception $e ) {
      return new WP_Error( 'x-convertkit', $e->getMessage() );
    }

    return $result;

  }


  //
  // Prepare vars for subscription based on POST info
  //

  function make_subscription_vars( $user_data ) {

    //
    // Main fields
    //
    $vars = array();
    $vars['email'] = sanitize_text_field( ( isset( $user_data['email_address'] ) ) ? $user_data['email_address'] : '' );
    unset( $user_data['email_address'] );

    //
    // Full name, if avaliable
    //
    if ( isset( $user_data['full_name'] ) && $user_data['full_name'] != '' ) {
      $parts = explode( ' ', trim( sanitize_text_field( $user_data['full_name'] ) ) );
      if ( count( $parts ) > 1 ) {
        $vars['first_name']           = trim( array_shift( $parts ) );
        $vars['fields']['last_name']  = trim( ( count( $parts ) > 1 ) ? implode( ' ', $parts ) : array_shift( $parts ) );
      } else {
        $vars['first_name'] = trim( sanitize_text_field( $user_data['full_name'] ) );
      }
    }
    unset( $user_data['full_name'] );

    //
    // First name, if avaliable
    //
    if ( isset( $user_data['first_name'] ) && $user_data['first_name'] != '' ) {
      $vars['first_name'] = trim( sanitize_text_field( $user_data['first_name'] ) );
    }
    unset( $user_data['first_name'] );

    //
    // Last name, if avaliable
    //
    if ( isset( $user_data['last_name'] ) && $user_data['last_name'] != '' && ! $name_set ) {
      $vars['fields']['last_name'] = trim( sanitize_text_field( $user_data['last_name'] ) );
    }
    unset( $user_data['last_name'] );

    //
    // All remaining fields, so custom fields are sent
    //
    unset( $user_data['form_id'] );
    foreach ( $user_data as $key => $value ) {
      $vars['fields'][$key] = trim( sanitize_text_field( $value ) );
    }

    return $vars;
  }

}
