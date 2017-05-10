<?php

// =============================================================================
// EMAIL-GETRESPONSE/FUNCTIONS/PROVIDER.PHP
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

class X_Email_GetResponse extends X_Email_Provider {

  //
  // Properties.
  //

  protected $api_wrapper, $api_key, $api_opts;


  //
  // Setup.
  //

  function setup() {

    $this->register_validators( array(
       'gr_api_key' => array( $this, 'validate_gr_api_key' ),
    ) );

  }


  //
  // Integration.
  //

  function settings_page() {

    $gr_list_refresh_url = add_query_arg( array( '_list_refresh_nonce' => wp_create_nonce( 'gr_list_refresh' ) ) );
    $this->plugin->set_transport( 'gr_list_refresh_url', $gr_list_refresh_url );

    if ( isset( $_REQUEST['_list_refresh_nonce'] ) ) {
      if ( wp_verify_nonce( $_REQUEST['_list_refresh_nonce'], 'gr_list_refresh' ) ) {
        $refresh = $this->refresh_list_cache();
        if ( isset( $refresh['message'] ) ) {
          $this->plugin->set_transport( 'gr_message', $refresh['message'] );
        }
      } else {
        wp_die( __( 'Permission denied. Unable to refresh list', '__x__' ) );
      }
    }

    if ( ! $this->plugin->options->get( 'gr_api_key' )) {
      unset( $this->config['settings_metaboxes']['gr_lists'] );
    }

  }


  //
  // API key validation.
  //

  function validate_gr_api_key( $value ) {

    $validate = $this->validate_api_key( $value );
    $this->gr_api_key_validate_failed = is_wp_error( $validate );

    return $this->gr_api_key_validate_failed;
  }


  //
  // Before saving.
  //

  function before_save() {

    $slug = $this->plugin->get_slug();

    if ( ( isset( $this->gr_api_key_validate_failed ) && $this->gr_api_key_validate_failed ) ) {
      $this->plugin->options->set( 'gr_api_key', '' );
      $this->plugin->options->set( 'gr_list_cache', array() );
    } elseif( $this->plugin->options->was_modified( 'gr_api_key') ) {
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

    if ( ! class_exists( 'GetResponse' ) ) {
      require_once( "{$this->path}/functions/vendor/getresponse-api-php/src/GetResponseAPI3.class.php" );
    }
    if ( $api_key == '' ) {
      $api_key = $this->plugin->options->get( 'gr_api_key' );
    }
    try {
      $wrapper = new GetResponse( $api_key );
      $this->api_wrapper = $wrapper;
    } catch ( Exception $e ) {
      return new WP_Error( 'x-getresponse', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );
    }

    return $this->api_wrapper;

  }


  //
  // Validate the API key
  //

  function validate_api_key( $key ) {

    try {
      $gr_api = $this->make_api_wrapper( $key );
      $result = $gr_api->getCampaigns();
      if ( isset( $result->httpStatus ) && $result->httpStatus === 401) {
        throw new Exception( $result->message, 401 );
      }
    } catch ( Exception $e ) {
      $result = new WP_Error( 'x-getresponse', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );
    }

    return $result;

  }


  //
  // Retrieve avaliable lists
  //

  function retrieve_lists() {

    try {
      $gr_api = $this->get_wrapper();
      $result = $gr_api->getCampaigns();
      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }
    } catch ( Exception $e ) {
      return new WP_Error( 'x-getresponse', sprintf( __( 'Error attempting to retrieve GetResponse List: [%s]', '__x__' ), $e->getMessage() ) );
    }

    return (array) $result;

  }


  //
  // Retrieve custom fields for each list
  //

  function retrieve_custom_fields( ) {

    try {
      $mc_api = $this->get_wrapper();
      $result = $mc_api->getCustomFields();

      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }

    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), $e->getMessage() ) );
    }

    return (array) $result;

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
      foreach( $result as $key => $list ) {
        $result[ $key ]->custom_fields = $this->retrieve_custom_fields();
      }
      $lists = $this->reduce_list_result( $result );
      if ( empty( $lists ) ) {
        $response['message'] = __( 'Your account doesn\'t have any GetResponse lists. You should create some from your GetResponse admin page.', '__x__' );
      }
      if ( $lists == $this->plugin->options->get( 'gr_list_cache' ) ) {
        $response['message'] = __( 'Refresh complete. No updates at this time.', '__x__' );
      } else {
        $response['message'] = __( 'Lists updated!', '__x__' );
      }
      $this->plugin->options->set( 'gr_list_cache', $lists, true );
    }

    return $response;

  }

  //
  // Create a reduced array of avaliable lists
  //

  function reduce_list_result( $results ) {

    $lists = array();
    foreach ( $results as $list ) {
      $lists[$list->campaignId] = array(
        'id'             => $list->campaignId,
        'provider'       => $this->config['name'],
        'provider_title' => $this->config['title'],
        'name'           => isset( $list->profile ) && $list->profile->title !== '' ? $list->profile->title : ucwords( str_replace( '_', ' ',  $list->name) ),
      );
      foreach($list->custom_fields as $cf ) {
        $lists[$list->campaignId]['custom_fields'][] = array(
          'name'          => $cf->customFieldId,
          'label'         => ucwords( $cf->name ),
          'type'          => $cf->type,
          'required'      => false,
          'default_value' => '',
          'choices'       => $cf->values,
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
    $cache = $this->plugin->options->get( 'gr_list_cache' );
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
      $data = $this->make_subscription_vars( $list_id, $user_data );
      $gr_api = $this->get_wrapper();
      $result = $gr_api->addContact( $data );
      if ( isset( $result->httpStatus ) && $result->httpStatus === 400 ) {
        throw new Exception( $result->message, 400 );
      }
    } catch ( Exception $e ) {
      return new WP_Error( 'x-getresponse', $e->getMessage() );
    }

    return $result;

  }


  //
  // Prepare vars for subscription based on POST info
  //

  function make_subscription_vars( $list_id, $user_data ) {

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
        $last_name = trim( array_pop( $parts ) );
        $vars['name']           = trim( ( count( $parts ) > 1 ) ? implode( ' ', $parts ) : array_shift( $parts ) );
        // $vars['customFieldValues'][] = array(
        //   'customFieldId' => 'last_name',
        //   'value'         => array( $last_name ),
        // );
      } else {
        $vars['name'] = trim( sanitize_text_field( $user_data['full_name'] ) );
      }
    }
    unset( $user_data['full_name'] );

    //
    // First name, if avaliable
    //
    if ( isset( $user_data['first_name'] ) && $user_data['first_name'] != '' ) {
      $vars['name'] = trim( sanitize_text_field( $user_data['first_name'] ) );
    }
    unset( $user_data['first_name'] );

    //
    // Last name, if avaliable
    //
    if ( isset( $user_data['last_name'] ) && $user_data['last_name'] != '' && ! $name_set ) {
      $vars['customFieldValues'][] = array(
        'customFieldId' => 'last_name',
        'value'         => array( trim( sanitize_text_field( $user_data['last_name'] ) ) )
      );
    }
    unset( $user_data['last_name'] );

    // Campaign
    $vars['campaign']['campaignId'] = $list_id;

    //
    // All remaining fields, so custom fields are sent
    //
    unset( $user_data['form_id'] );
    foreach ( $user_data as $key => $value ) {

      if ( ! empty ($value) ) {
        $vars['customFieldValues'][] = array(
          'customFieldId' => $key,
          'value' => array( trim( sanitize_text_field( $value ) ) )
        );
      }
    }

    return $vars;
  }

}
