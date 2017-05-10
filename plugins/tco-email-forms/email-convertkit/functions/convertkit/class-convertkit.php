<?php

// =============================================================================
// EMAIL-CONVERTKIT/FUNCTIONS/CONVERTKIT/CLASS-CONVERTKIT.PHP
// -----------------------------------------------------------------------------
// ConvertKit Interface
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class
// =============================================================================

// Class
// =============================================================================

class ConvertKit {

  //
  // Properties
  //

  protected $api_url_base  = 'https://api.convertkit.com/v3/';
  protected $api_key       = '';
  protected $resource_name = null;

  //
  // Constructor
  //

  function __construct( $api_key ) {
    $this->api_key     = $api_key;
  }

  //
  // Get request proxy method
  //

  function get( $path, $args = array() ) {
    return $this->query( $path, $args, 'GET');
  }

  //
  // Post request proxy method
  //

  function post( $path, $args = array() ) {
    return $this->query( $path, $args, 'POST');
  }

  //
  // Exposed query
  //

  function query ( $path, $args = array(), $method = 'GET' ) {

    $results = $this->_do_query( $path, $args, $method );

    if ( is_wp_error( $results ) ) {
      return $results;
    }

		if ( 200 == wp_remote_retrieve_response_code( $results ) ) {
			$results = wp_remote_retrieve_body( $results );
			return json_decode( $results );
		}

  	$body = wp_remote_retrieve_body( $results );

		if( is_string( $body ) && is_object( $json = json_decode( $body ) ) ){
			$body = (array) $json;
		}

		if( isset( $body['error'] ) && ! empty( $body[ 'error' ] ) ){
			return $body;
		}

		return wp_remote_retrieve_response_code( $results );
  }

  //
  // Private query - prepare args and execute wp_remote_*
  //

  function _do_query ( $path, $args = array(), $method = 'GET' ) {
    $args = array_merge( $args, array('api_key' => $this->api_key ) );
    switch ( $method ) {
      case 'POST':
      case 'post':
        $args = array(
          'method' => 'POST',
          'body'   => $args,
        );
        $result = wp_remote_post( $this->api_url_base . $path, $args );
        break;

      default:
      case 'GET':
      case 'get':
        $url = add_query_arg( $args, $this->api_url_base . $path );
        $result = wp_remote_get( $url );
        break;
    }
    
    return $result;
  }
}
