<?php


function wppp_call_auth_api( $post ) {

    $error_message = null;

    wppp_log_trace(
        'wppp_call_auth_api started, post params: ' . print_r( $post, true ),
        0, '', '', 'wppp_call_auth_api'
    );

    $auth_url = WPPP_API_URL . 'authentication/token/server_sdk';
    wppp_log_trace(
        'auth url: ' . $auth_url,
        0, '', '', 'wppp_call_auth_api'
    );

    $args = array(
        'body' => $post,
        'timeout' => '120',
        'redirection' => '10',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'cookies' => array()
    );
    wppp_log_trace(
        'args: ' . print_r( $args, true),
        0, '', '', 'wppp_call_auth_api'
    );

    $response = wp_remote_post( $auth_url, $args );
    wppp_log_trace(
        '$response: ' . print_r( $response, true ),
        0, '', '', 'wppp_call_auth_api'
    );

    wppp_log_trace(
        'response: ' . print_r( $response['response'], true ),
        0, '', '', 'wppp_call_auth_api'
    );

    $code = $response['response']['code'];

    if ( 200 == $code ) {

        wppp_log_debug(
            '200 - OK: ',
            0, '', '', 'wppp_call_auth_api'
        );

        echo ('<div class="updated">Settings updated. Successfully authenticated.</div>');
        update_option( WPPP_OPTION_NAME_STATUS, 'active' );

        wppp_sent_auth_success();

    } else {

        wppp_log_error(
            'message: ' . print_r( $response['response']['message'], true),
            0, '', '', 'wppp_call_auth_api'
        );

        if ( ( 403 == $code ) || ( 404 == $code ) ) {

            $error_message = 'Invalid Bundle ID or/and Secret Key. Please check the <a href="https://pixpie.atlassian.net/wiki/display/DOC/Create+application" target="_blank">https://pixpie.atlassian.net/wiki/display/DOC/Create+application</a> page';

            wppp_sent_auth_failure(
                'Credentials error in auth - ' .
                wppp_get_option_no_slashes( WPPP_OPTION_NAME_BUNDLE_ID ),
                'Authentication error; URL:'. $auth_url . ' POST parameters: ' . print_r( $post, true )
            );

        } elseif ( ( 400 == $code ) || ( 500 == $code ) ) {

            $error_message =
                'Server error while authenticating. Please retry.';

            wppp_sent_auth_failure(
                'Server error while auth - ' .
                wppp_get_option_no_slashes( WPPP_OPTION_NAME_BUNDLE_ID ),
                'Server Error; URL:'. $auth_url . ' POST parameters: ' . print_r( $post, true )
            );

        } else {

            $error_message = 'Unknown error while trying to authenticate';

            wppp_sent_auth_failure(
                'Unknown error while auth - ' .
                wppp_get_option_no_slashes( WPPP_OPTION_NAME_BUNDLE_ID ),
                'Unknown Error; URL:'. $auth_url . ' POST parameters: ' . print_r( $post, true )
            );

        }


    }

    return $error_message;
}


/**
 * @param $api_call_url - should be already correctly encoded
 * @param $file_to_send_path
 * @param $image_file - where to save to
 * @param $filename  - for logging purposes
 * @param $size_name - for logging purposes
 * @param $attachment_id - for logging purposes
 * @return bool
 */
function wppp_call_convert_api( $api_call_url, $file_to_send_path, $image_file, $filename, $size_name, $attachment_id ) {

    $result = false;

    $file = @fopen( $file_to_send_path, 'r' );
    $file_size = filesize( $file_to_send_path );
    $file_data = fread( $file, $file_size );

    $local_file = $file_to_send_path;
    $post_fields = array (
        'name' => 'value'
    );

    $boundary = '';
    for($i = 0; $i < 24; $i++) {
        $boundary .= mt_rand(1, 9);
    }

    $headers  = array(
        'content-type' => 'multipart/form-data; charset=utf-8; boundary=' . $boundary,
    );
    $payload = '';
    // First, add the standard POST fields:
    foreach ( $post_fields as $name => $value ) {
            $payload .= '--' . $boundary;
            $payload .= "\r\n";
            $payload .= 'Content-Disposition: form-data; name="' . $name .
                '"' . "\r\n\r\n";
            $payload .= $value;
            $payload .= "\r\n";
        }
    // Upload the file
    if ( $local_file ) {
        $payload .= '--' . $boundary;
        $payload .= "\r\n";
        $payload .= 'Content-Disposition: form-data; name="' . 'image' .
            '"; filename="' . basename( $local_file ) . '"' . "\r\n";
        //        $payload .= 'Content-Type: image/jpeg' . "\r\n";
        $payload .= "\r\n";
        $payload .= file_get_contents( $local_file );
        $payload .= "\r\n";
    }
    $payload .= '--' . $boundary . '--';

    $post_data = $payload;

    wppp_log_trace(
        'headers: ' . print_r( $headers, true),
        0, '', '', 'wppp_call_convert_api'
    );

    $args = array(
        'body' => $post_data,
        'timeout' => '45',
        'redirection' => '10',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => $headers,
        'cookies' => array(),
    );

    $response = wp_remote_post( $api_call_url, $args );
    wppp_log_trace(
        'response: ' . substr(print_r( $response, true),0,400),
        0, '', '', 'wppp_call_convert_api'
    );

    if ( is_wp_error( $response ) ) {

        $error_message = $response->get_error_message();
        wppp_log_error(
            'Error in response: ' . print_r( $error_message, true ),
            0, '', '', 'wppp_call_convert_api'
        );

    } else {

        wppp_log_trace(
            'response width no errors',
            0, '', '', 'wppp_call_convert_api'
        );

        $code = $response['response']['code'];

        if ( isset( $code ) && ( 200 == $code ) ) {

            wppp_log_trace(
                'code 200',
                0, '', '', 'wppp_call_convert_api'
            );

            $contents = $response['body'];

            wppp_log_trace(
                'response headers: ' . print_r( $response['headers'], true),
                0, '', '', 'wppp_call_convert_api'
            );

            $savefile = fopen( $image_file, 'wb' );

            fwrite( $savefile, $contents );

            fclose( $savefile );

            $result = true;

        } else {

            $error = print_r( $response['response'], true );

            $error_message = 'Error while getting converted image: <br/>' . $error;
            wppp_send_error_by_email( 'Converting error ' . $image_file, $error_message );

            wppp_log_error(
                'CURL error: ' . print_r( $error, true ),
                $attachment_id, $filename, $size_name, 'download'
            );

        }

    }

    wppp_log_trace(
        'Download done',
        $attachment_id, $filename, $size_name, 'download'
    );

    return $result;
}



function wppp_call_sign_up_api( $email, $password ) {

    $error_message = null;
    $result = null;

    wppp_log_trace(
        'wppp_call_sign_up_api started',
        0, '', '', 'wppp_call_sign_up_api'
    );

    $site_domain = str_replace( 'http://', '', get_option('siteurl') );
    $site_domain = str_replace( 'https://', '', $site_domain );

    $backlink = get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS;
    $api_url = 'https://cloud.pixpie.co/registration/prepare';
    wppp_log_trace(
        'api url: ' . $api_url,
        0, '', '', 'wppp_call_sign_up_api'
    );

    $post_fields = array (
        'email' => $email,
        'password' => $password,
        'domain' => $site_domain,
        'back_link' => $backlink,
    );
    wppp_log_trace(
        'post fields: ' . print_r( $post_fields, true ),
        0, '', '', 'wppp_call_sign_up_api'
    );

    $args = array(
        'body' => $post_fields,
        'timeout' => '120',
        'redirection' => '10',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'cookies' => array()
    );
    wppp_log_trace(
        'args: ' . print_r( $args, true ),
        0, '', '', 'wppp_call_sign_up_api'
    );

    $response = wp_remote_post( $api_url, $args );
    wppp_log_trace(
        'response: ' . print_r( $response, true ),
        0, '', '', 'wppp_call_sign_up_api'
    );

    $body = wp_remote_retrieve_body( $response );
    wppp_log_trace(
        'response body: ' . print_r( $body, true ),
        0, '', '', 'wppp_call_sign_up_api'
    );

    $code = $response['response']['code'];

    if ( 200 == $code ) {
        wppp_log_debug(
            '200 - OK',
            0, '', '', 'wppp_call_sign_up_api'
        );
        $result = array(
            'type' => 'OK',
            'data' => $body,
        );
    } else {
        wppp_log_error(
            'message: ' . print_r( $response['response']['message'], true),
            0, '', '', 'wppp_call_sign_up_api'
        );
        $result = array(
            'type' => 'NOK',
            'error' => print_r( $response['response']['message'], true),
        );
    }
    return $result;
}
