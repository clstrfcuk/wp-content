<div class="wrap">

    <h1><?= WPPP_PLUGIN_NAME ?> <small> &mdash; Compress All Images</small></h1>

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

		$response_status = json_decode ( $response ['body'] ) -> responseMessage;

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

		if ( get_option ( 'wppp_action_available_status' ) != 'AVAILABLE' && strlen ( get_option ( 'wppp_action_available_status' ) ) > 0 ) {

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
	}

	?>

    <hr/>

	<?php

	global $post, $wpdb;

	$total_img = $wpdb -> get_results ( "SELECT COUNT(*) FROM `wp_posts` WHERE post_status = 'inherit' AND post_type = 'attachment' AND (post_mime_type = 'image/jpeg' OR post_mime_type = 'image/png')" );

	$total_compress_img = $wpdb -> get_results ( "SELECT COUNT(*) FROM `wp_wppp_converted_images` " );


	$COUNT_wpdb = 'COUNT(*)';

	$COUNT = $total_img[0] -> $COUNT_wpdb;

	$COUNT_compressed = $total_compress_img[0] -> $COUNT_wpdb;


	$images_to_convert = wppp_get_all_images_to_convert ();
	$unprocessed_count = count ( $images_to_convert );
	//	$compressed_count = $COUNT - $unprocessed_count;

	// count all thumbnails sizes (max)
	$unprocessed_resolutions_count = 0;
	if ( $unprocessed_count > 0 ) {
		$all_sizes = wppp_get_image_sizes ();
		$resolutions_count = count_original_resolutions ( $all_sizes );
		$unprocessed_resolutions_count = $unprocessed_count * $resolutions_count;
	}
	?>

    <p>You have <span style="font-weight: bold" id="unprocessedNumber" ><?php echo ($COUNT - $COUNT_compressed); ?></span> unprocessed image(s)</p>


    <div class="wppp_optimize">
        <div class="wppp_progressbar" id="wppp_compression-progress-bar">
            <div id="wppp_progress-size" class="wppp_progress
                <?php if( $unprocessed_count > 0 ) echo 'wppp_animate_progress' ?>" >
            </div>
            <div class="wppp_numbers">
                <span id="wppp_optimized-current"><?php echo $COUNT_compressed ?></span>
                /
                <span id="wppp_optimized-total"><?php echo $COUNT ?></span>
                <span id="wppp_percentage">0%</span>
            </div>
        </div>
    </div>


	<?php if ( 0 < $unprocessed_count ) { ?>

		<?php if ( wppp_is_plugin_activated () && ! get_option ( 'wppp_overlimit' ) && get_option ( 'wppp_action_available_status' ) == 'AVAILABLE' ) { ?>

            <p>Overall number of different image sizes might be up to
                <b><?php echo $unprocessed_resolutions_count ?></b></p>

            <p>If you start the process &mdash; you could be charged up to
                <b><?php echo $unprocessed_resolutions_count ?></b> compressions.<br/></p>

            <p style="color: red; font-weight: bold;">
                The button below will start compression of all images, that are uploaded in media library. Be careful, it can take a lot of time to process all images.
            </p>

            <a href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_CONVERT_ALL ); ?>&action=convert&total_todo=<?php echo $unprocessed_count ?>" class="button-primary" id="convertAllImgages">Compress all existing images</a>

            <span id="admin" style="display: none;"><?php echo WPPP_PLUGIN_PAGE_ID_CONVERT_ALL ?></span>

            <span id="total_todo" style="display: none;"><?php echo $unprocessed_count ?></span>

            <div id="answer"></div>

		<?php } else { ?>

            <b>Plese set up the plugin settings first.</b>

		<?php } ?>

	<?php } // end if > 0 ?>
</div>


