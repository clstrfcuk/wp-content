<?php

// =============================================================================
// EMAIL-MAILCHIMP/FUNCTIONS/PROVIDER.PHP
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

class X_Email_MailChimp extends X_Email_Provider {

  //
  // Properties.
  //

  protected $api_wrapper, $api_key, $api_opts;


  //
  // Setup.
  //

  function setup() {

    $this->register_validators( array(
       'mc_api_key' => array( $this, 'validate_mc_api_key' ),
    ) );

  }


  //
  // Integration.
  //

  function settings_page() {

    $mc_list_refresh_url = add_query_arg( array( '_list_refresh_nonce' => wp_create_nonce( 'mc_list_refresh' ) ) );
    $this->plugin->set_transport( 'mc_list_refresh_url', $mc_list_refresh_url );

    if ( isset( $_REQUEST['_list_refresh_nonce'] ) ) {
      if ( wp_verify_nonce( $_REQUEST['_list_refresh_nonce'], 'mc_list_refresh' ) ) {
        $refresh = $this->refresh_list_cache();
        if ( isset( $refresh['message'] ) ) {
          $this->plugin->set_transport( 'mc_message', $refresh['message'] );
        }
      } else {
        wp_die( __( 'Permission denied. Unable to refresh list', '__x__' ) );
      }
    }

    if ( ! $this->plugin->options->get( 'mc_api_key' ) ) {
      if ( get_option('x_email_forms') ) {
        $x_email_forms = get_option('x_email_forms');
        if ( isset( $x_email_forms['mc_api_key'] ) ) {
          $this->plugin->options->set( 'mc_api_key', $x_email_forms['mc_api_key'], true );
          $this->refresh_list_cache();
        }
      } else {
        unset( $this->config['settings_metaboxes']['mc_lists'] );
      }
    }

  }


  //
  // API key validation.
  //

  function validate_mc_api_key( $value ) {

    $validate = $this->validate_api_key( $value );
    $this->mc_api_key_validate_failed = is_wp_error( $validate );

    return $this->mc_api_key_validate_failed;

  }


  //
  // Before saving.
  //

  function before_save() {

    $slug = $this->plugin->get_slug();

    if ( ( isset( $this->mc_api_key_validate_failed ) && $this->mc_api_key_validate_failed ) ) {
      $this->plugin->options->set( 'mc_api_key', '' );
      $this->plugin->options->set( 'mc_list_cache', array() );
    } elseif( $this->plugin->options->was_modified( 'mc_api_key') ) {
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

    if ( ! class_exists( '\DrewM\MailChimp\MailChimp' ) ) {
      require_once( "{$this->path}/functions/vendor/mailchimp-api/src/MailChimp.php" );
    }

    if ( $api_key == '' ) {
      $api_key = $this->plugin->options->get( 'mc_api_key' );
    }

    //
    // Use local opts if set, otherwise those declared in setup if set.
    //
    try {
      $wrapper = new \DrewM\MailChimp\MailChimp( $api_key );
      $this->api_wrapper = $wrapper;
    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );
    }

    return $this->api_wrapper;

  }


  //
  // Validate the API key
  //

  function validate_api_key( $key ) {

    try {
      $mc_api = $this->make_api_wrapper( $key );
      $result = $mc_api->get('');
      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }
    } catch ( Exception $e ) {
      $result = new WP_Error( 'x-mailchimp', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );
    }

    return $result;

  }

  //
  // Retrieve avaliable lists
  //

  function retrieve_lists() {

    try {
      $mc_api = $this->get_wrapper();
      $result = $mc_api->get( 'lists', array('offset' => 0, 'count' => 100) );
      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }

    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), $e->getMessage() ) );
    }

    return $result;

  }

  //
  // Retrieve custom fields for each list
  //

  function retrieve_custom_fields( $list_id ) {

    try {
      $mc_api = $this->get_wrapper();
      $result = $mc_api->get( "lists/{$list_id}/merge-fields" );

      if ( array_key_exists( 'status', $result ) ) {
        throw new Exception( $result['detail'], $result['status'] );
      }

    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), $e->getMessage() ) );
    }


    return $result['merge_fields'];

  }

  //
  // Retrieve custom fields for each list
  //

  function retrieve_groups( $list_id ) {

    try {
      $mc_api = $this->get_wrapper();

      $groups     = array();
      $categories = $mc_api->get( "lists/{$list_id}/interest-categories" );

      if ( array_key_exists( 'status', $categories ) ) {
        throw new Exception( $categories['detail'], $categories['status'] );
      }

      foreach ($categories['categories'] as $cat ) {
        $group = array(
          'id'        => $cat['id'],
          'title'     => $cat['title'],
          'type'      => $cat['type'],
          'interests' => array(),
        );
        $interests = $mc_api->get( "lists/{$list_id}/interest-categories/{$cat['id']}/interests" );
        if ( array_key_exists( 'status', $interests ) ) {
          throw new Exception( $interests['detail'], $interests['status'] );
        }

        foreach ($interests['interests'] as $int ) {
          $group['interests'][] = array(
            'id'   => $int['id'],
            'name' => $int['name'],
          );
        }
        $groups[ $cat['id'] ] = $group;
      }

    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), $e->getMessage() ) );
    }


    return $groups;

  }



  //
  // Force avaliable lists refreshing
  //

  function refresh_list_cache() {

    $response = array();
    $result   = $this->retrieve_lists();
    $result   = $result['lists'];

    if ( is_wp_error( $result ) ) {
      $response['message'] = $result->get_error_message();
    } else {
      foreach( $result as $key => $list ) {
        $result[ $key ]['custom_fields'] = $this->retrieve_custom_fields( $list['id'] );
        $result[ $key ]['groups']        = $this->retrieve_groups( $list['id'] );
      }
      $lists = $this->reduce_list_result( $result );
      if ( empty( $lists ) ) {
        $response['message'] = __( 'Your account doesn\'t have any MailChimp lists. You should create some from your MailChimp admin page.', '__x__' );
      }
      if ( $lists == $this->plugin->options->get( 'mc_list_cache' ) ) {
        $response['message'] = __( 'Refresh complete. No updates at this time.', '__x__' );
      } else {
        $response['message'] = __( 'Lists updated!', '__x__' );
      }

      $this->plugin->options->set( 'mc_list_cache', $lists, true );
    }

    return $response;

  }

  //
  // Create a reduced array of avaliable lists
  //

  function reduce_list_result( $results ) {

    $lists = array();
    foreach ( $results as $list ) {
      $lists[ $list['id'] ] = array(
        'id'             => $list['id'],
        'provider'       => $this->config['name'],
        'provider_title' => $this->config['title'],
        'name'           => $list['name'],
        'custom_fields'  => array(),
      );
      foreach($list['custom_fields'] as $cf ) {
        if ( in_array( $cf['tag'], array('FNAME', 'LNAME') ) ) {
          continue;
        }
        $lists[ $list['id'] ]['custom_fields'][ $cf['tag'] ] = array(
          'name'          => $cf['tag'],
          'label'         => $cf['name'],
          'type'          => $cf['type'],
          'required'      => $cf['required'],
          'default_value' => $cf['default_value'],
          'choices'       => array_key_exists( 'choices', $cf['options'] ) ? $cf['options']['choices'] : array(),
          'options'       => array(),
        );
      }
      $lists[ $list['id'] ]['groups'] = $list['groups'];
    }

    return $lists;

  }

  //
  // Get a user-friendly array of lists from cache
  //

  function get_normalized_list() {

    $items = array();
    $cache = $this->plugin->options->get( 'mc_list_cache' );

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
      $mc_api = $this->get_wrapper();
      $result = $mc_api->post( "lists/$list_id/members", $data );
      if ( array_key_exists( 'status', $result ) && ! in_array( $result['status'], array( 'subscribed', 'unsubscribed', 'cleaned', 'pending' ) ) ) {
        $error_message = $result['title'] === 'Member Exists'
          ? $result['title']
          : sprintf( __( 'MailChimp Error: %s', '__x__' ), $result['detail']);
        if ($result['detail'] === 'Your merge fields were invalid.') {
          foreach ($result['errors'] as $error) {
            if ($error['message'] === 'Please enter the date') {
              $error['message'] = 'Please enter date in YYYY-MM-DD format';
            }
            $error_message .= '<br/>- ' . $error['message'];
          }
        }
        throw new Exception( $error_message, $result['status'] );
      }
    } catch ( Exception $e ) {
      return new WP_Error( 'x-mailchimp', $e->getMessage() );
    }

    return $result;

  }

  //
  // Prepare vars for subscription based on POST info
  //

  function make_subscription_vars( $user_data ) {

    //
    // Get double-opt-in form setting
    //
    $form_data = get_post_meta( $user_data['form_id'] );
    $skip_double_opt_in = false;
    if ( array_key_exists('email_forms_double_opt_in', $form_data ) ) {
      $value = $form_data['email_forms_double_opt_in'];
      $skip_double_opt_in = (is_array( $value ) && count( $value ) <= 1 ) ? $value[0] : $value;
      $skip_double_opt_in = $skip_double_opt_in === 'Yes' ? true : false;
    }


    //
    // Main fields
    //
    $vars = array(
      'email_address' => sanitize_text_field( ( isset( $user_data['email_address'] ) ) ? $user_data['email_address'] : '' ),
      'email'         => sanitize_text_field( ( isset( $user_data['email_address'] ) ) ? $user_data['email_address'] : '' ),
      'status'        => $skip_double_opt_in ? 'subscribed' : 'pending',
    );
    unset( $user_data['email_address'] );

    //
    // Full name, if avaliable
    //
    if ( isset( $user_data['full_name'] ) && $user_data['full_name'] != '' ) {
      $parts = explode( ' ', trim( sanitize_text_field( $user_data['full_name'] ) ) );
      if ( count( $parts ) > 1 ) {
        $vars['merge_fields']['FNAME'] = trim( array_shift( $parts ) );
        $vars['merge_fields']['LNAME'] = trim( ( count( $parts ) > 1 ) ? implode( ' ', $parts ) : array_shift( $parts ) );
      } else {
        $vars['merge_fields']['FNAME'] = trim( sanitize_text_field( $user_data['full_name'] ) );
      }
    }
    unset( $user_data['full_name'] );

    //
    // First name, if avaliable
    //
    if ( isset( $user_data['first_name'] ) && $user_data['first_name'] != '' ) {
      $vars['merge_fields']['FNAME'] = trim( sanitize_text_field( $user_data['first_name'] ) );
    }
    unset( $user_data['first_name'] );

    //
    // Last name, if avaliable
    //
    if ( isset( $user_data['last_name'] ) && $user_data['last_name'] != '' && ! $name_set ) {
      $vars['merge_fields']['LNAME'] = trim( sanitize_text_field( $user_data['last_name'] ) );
    }
    unset( $user_data['last_name'] );

    //
    // Groups
    //
    if ( isset( $user_data['groups'] ) ) {
      $vars['interests'] = array();
        foreach ( $user_data['groups'] as $interest ) {
          if ( is_array( $interest ) ) {
            foreach ( $interest as $int ) {
              if ( $int !== '') {
                $vars['interests'][$int] = true;
              }
            }
          } else if ( $interest !== '') {
            $vars['interests'][$interest] = true;
          }
        }
    }
    unset( $user_data['groups'] );

    //
    // All remaining fields, so custom fields are sent
    //
    unset( $user_data['form_id'] );
    foreach ( $user_data as $key => $value ) {
      if ( is_array( $value ) ) {
        foreach( $value as $key2 => $value2 ) {
          $vars['merge_fields'][ $key ][ $key2 ] = trim( sanitize_text_field( $value2 ) );
        }
        continue;
      }
      $vars['merge_fields'][ $key ] = trim( sanitize_text_field( $value ) );
    }

    return $vars;
  }

}
