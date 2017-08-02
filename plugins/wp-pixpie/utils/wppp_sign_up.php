<?php


function wppp_process_sign_up_form ( $email, $password ) {
    wppp_log_trace(
        'started',
        0, '', '', 'wppp_process_sign_up_form'
    );
    $response = wppp_call_sign_up_api( $email, $password );

    wppp_log_trace(
        'response: ' . print_r( $response, true ),
        0, '', '', 'wppp_process_sign_up_form'
    );

    if ( 'OK' == $response['type'] ) {
	    $data = json_decode( $response['data'] );
	    if ( 200 == $data -> statusMessage ) {
		    $url = $data -> webPanelUrl;
		    wppp_log_trace(
			    'redirecting to: ' . $url,
			    0, '', '', 'wppp_process_sign_up_form'
		    );
//		    wp_safe_redirect( $url );
		    wp_redirect( $url );
		    exit();
	    } elseif ( 302 == $data -> statusMessage ) {
		    $url = $data -> webPanelUrl;
		    wppp_log_trace(
			    'redirecting to: ' . $url,
			    0, '', '', 'wppp_process_sign_up_form'
		    );
		    wp_redirect( $url );
        }else {
            wppp_log_error(
                'data->statusMessage not 200' . $data -> statusMessage ,
                0, '', '', 'wppp_process_sign_up_form'
            );
        }
    } else {
        wppp_log_error(
            'Error while sending sign up request',
            0, '', '', 'wppp_process_sign_up_form'
        );
    }

}
