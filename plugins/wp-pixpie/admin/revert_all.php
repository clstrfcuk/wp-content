<div class="wrap">

    <h1><?= WPPP_PLUGIN_NAME ?>
        <small> &mdash; Revert All Images</small>
    </h1>

	<?php

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
			'dashboard payment response: ' . print_r ( $response, true ),
			0, '', '', 'dashboard_payment_check'
		);

		$action_available_status = json_decode ( $response['body'] ) -> actionAvailableStatus;

		$response_status = json_decode ( $response['body'] ) -> responseMessage;
		$response_agree_form = json_decode ( $response['body'] ) -> shortResponseMessage;

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
			'change action status : ' . $response_status,
			0, '', '', 'dashboard_payment_check'
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

	?>

    <hr/>

	<?php
	$action = $_GET["action"];
	if ( isset( $action ) && ( 'revert' == $action ) ) {

		wppp_log_info ( 'Revert All started', 0, '', '', 'revert-all' );

		$compressed = wppp_get_all_images_ids ();
		$comp_count = count ( $compressed );
		wppp_log_trace ( 'Revert All - ' . $comp_count . ' images to revert', 0, '', '', 'revert-all'
		);

		foreach ( $compressed as $image_id ) {
			wppp_revert_image ( $image_id );
		}
		wppp_log_info ( 'Revert All finished', 0, '', '', 'revert-all' );

		?>
        <p>Done! All images reverted.</p>
		<?php

	} else {

		$compressed = wppp_get_all_images ();
		$comp_count = count ( $compressed );

		?>
        <p>You have <?php if($comp_count<=10000){
		        echo $comp_count;
            }else{
            echo 'more than 10000';
            } ?> compressed images</p>

        <p>You can <b>roll back</b> your images to uncompressed original versions.</p>

        <p>All compressed images will be deleted.</p>

		<?php

		if ( wppp_is_plugin_activated_without_auth () ) {

			?>

            <a href=" <?php echo ( get_admin_url () . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_REVERT_ALL ); ?>&action=revert"
               class="button-primary">Revert all images
            </a>

			<?php

		} else {

			?>

            <hr/>

            <b>Plese set up the plugin settings first.</b>

			<?php
		}
		?>
		<?php

	}
	?>

</div>
