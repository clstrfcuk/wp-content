<div class="wrap">

    <h1><?= WPPP_PLUGIN_NAME ?>
        <small> &mdash; Billing</small>
    </h1>

    <hr/>

	<?php

	if ( ! get_option ( 'WPPP_OPTION_NAME_BUNDLE_ID' ) && ! get_option ( 'WPPP_OPTION_NAME_SECRET_KEY' ) ) {


		$auth_url = 'http://' . WPPP_SAFE_REDIRECT_CLOUD_HOST . 'payment/status';
		wppp_log_trace (
			'dashboard payment auth url: ' . $auth_url,
			0, '', '', 'settings_payment_check'
		);

		$headers = wppp_get_common_headers ();

		wppp_log_trace (
			'dashboard payment header: ' . print_r ( $headers, true ),
			0, '', '', 'settings_payment_check'
		);

		$args = array(
			'body'        => '',
			'timeout'     => '120',
			'redirection' => '10',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'cookies'     => array()
		);

		$response = wp_remote_post ( $auth_url, $args );

		wppp_log_trace (
			'dashboard payment response: ' . print_r ( $response, true ),
			0, '', '', 'dashboard_payment_check'
		);

		$action_available_status = json_decode ( $response['body'] ) -> actionAvailableStatus;

		$response_status = json_decode ( $response['body'] ) -> responseMessage;

		update_option ( 'wppp_action_available_status', $action_available_status );

		wppp_log_trace (
			'dashboard payment response: ' . $action_available_status,
			0, '', '', 'dashboard_payment_check'
		);

		wppp_log_trace (
			'change action status : ' . $action_available_status,
			0, '', '', 'dashboard_payment_check'
		);

		wppp_log_trace (
			'action response : ' . $response_status,
			0, '', '', 'dashboard_payment_check'
		);
	}

	$payment = get_option ( 'wppp_update_payment' );

	if ( get_option ( 'wppp_action_available_status' ) != 'AVAILABLE' && strlen ( get_option ( 'wppp_action_available_status' ) ) > 0 && empty( $payment ) ) {

		$auth_url = 'http://' . WPPP_SAFE_REDIRECT_CLOUD_HOST . 'payment/status';
		wppp_log_trace (
			'dashboard payment auth url: ' . $auth_url,
			0, '', '', 'settings_payment_check'
		);

		$headers = wppp_get_common_headers ();

		wppp_log_trace (
			'dashboard payment header: ' . print_r ( $headers, true ),
			0, '', '', 'settings_payment_check'
		);

		$args = array(
			'body'        => '',
			'timeout'     => '120',
			'redirection' => '10',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'cookies'     => array()
		);

		$response = wp_remote_post ( $auth_url, $args );

		wppp_log_trace (
			'renew response: ' . print_r ( $response, true ),
			0, '', '', 'renew_check'
		);

		$action_available_status = json_decode ( $response['body'] ) -> actionAvailableStatus;

		$response_status = json_decode ( $response['body'] ) -> responseMessage;
		$response_agree_form = json_decode ( $response['body'] ) -> shortResponseMessage;


		update_option ( 'wppp_action_available_status', $action_available_status );

		wppp_log_trace (
			'renew payment response: ' . $action_available_status,
			0, '', '', 'renew_payment_check'
		);

		wppp_log_trace (
			'change action status : ' . $action_available_status,
			0, '', '', 'renew_payment_check'
		);

		wppp_log_trace (
			'change action status : ' . $response_status,
			0, '', '', 'renew_payment_check'
		);

		?>

        <div class="error" id="ErrorPixpie">
			<?php
			if ( strlen ( $response_status ) > 0 ) {
				echo $response_status;
			} else {
				echo 'Your subscription is not active.';
			}
			?>

        </div>

        <div id="wpppAgree" >
            <h3><?php echo $response_agree_form;?></h3>
            <span class="wpppYes">Agree</span>
            <span class="wpppNo">Dismiss</span>
        </div>

		<?php
	}

	if ( get_option ( 'wppp_action_available_status' ) == 'AVAILABLE' && get_option ( WPPP_OPTION_NAME_STATUS ) == 'active' ) {


		?>

        <div class="updated">
			<?php
			if ( strlen ( $response_status ) > 0 ) {
				echo $response_status;
			} else {
				echo 'Your subscription is active';
			}
			?>

        </div>

		<?php
	}

	$auth_url = 'http://' . WPPP_SAFE_REDIRECT_CLOUD_HOST . 'payment/sign-on';
	wppp_log_trace (
		'invoice auth url: ' . $auth_url,
		0, '', '', 'invoice'
	);

	$headers = wppp_get_common_headers_invoice ();

	$args = array(
		'body'        => '',
		'timeout'     => '120',
		'redirection' => '10',
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => $headers,
		'cookies'     => array()
	);

	$response = wp_remote_post ( $auth_url, $args );

	$signOnLink = json_decode ( $response['body'] ) -> signOnLink;

	wppp_log_trace (
		'change action status : ' . $signOnLink,
		0, '', '', 'settings_payment_check'
	);


	?>
    <div>
        You can always manage or cancel your current subscription - <a href="<?php echo $signOnLink ?>">Manage/Cancel</a>.
    </div>

	<?php if ( get_option ( 'wppp_action_available_status' ) == 'SUBSCRIPTION_NOT_EXIST' ) { ?>
        <a class="button" id="renewSubscription">Renew</a>
	<?php } ?>
</div>