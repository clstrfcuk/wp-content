<div class="wrap">

    <h1><?= WPPP_PLUGIN_NAME ?>
        <small> &mdash; <!--Dashboard-->Stats</small>
    </h1>

    <hr/>

	<?php

	$payment = get_option ( 'wppp_update_payment' );

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
			'action response : ' . $response_status,
			0, '', '', 'dashboard_payment_check'
		);
	}



	if ( get_option ( 'wppp_action_available_status' ) != 'AVAILABLE' && strlen ( get_option ( 'wppp_action_available_status' ) ) > 0 && empty( $payment ) ) {

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

	$all_sizes = wppp_get_image_sizes ();

	$compressed = wppp_get_all_images ();
	$comp_count = count ( $compressed );

	$total_compressed_images = 0;
	$total_raw_size = 0;
	$total_compressed_size = 0;

	// calculate total for all compressed images
	if ( $comp_count > 0 ) {

		foreach ( $compressed as $comp_image ) {
			$total_compressed_images = $total_compressed_images + 1;
			$total_raw_size = $total_raw_size + $comp_image -> size_before;
			$total_compressed_size = $total_compressed_size + $comp_image -> size_after;
		}

		if ( $total_compressed_images > 0 ) {

			$total_raw_size_display = wppp_get_display_file_size ( $total_raw_size );
			$total_compressed_size_display = wppp_get_display_file_size ( $total_compressed_size );

			if ( $total_raw_size > 0 ) {
				$size_saved = 100 - ( $total_compressed_size * 100 / $total_raw_size );
				$size_saved = number_format ( (float) $size_saved, 2, '.', '' );
			}

		}

		?>
        <div class="wppp_savings">
            <div class="inner">
                <h3>Savings statistic</h3>
                <p>The savings statistic for all images available in Media library.</p>
                <style>

                    #wppp_optimization-chart svg circle.main {
                        stroke-width: 60;
                        stroke-dasharray: <?php echo ( $size_saved / 100 * 804) ?> 804;
                    }

                    #wppp_optimization-chart div.wppp_chart div.wppp_value {
                        min-width: 160px;
                    }

                    @keyframes shwoosh {
                        from {
                            stroke-dasharray: 0 804
                        }
                        to {
                            stroke-dasharray: <?php echo ( $size_saved / 100 * 804 ) ?> 804;
                        }
                    }

                    .wppp_value-table {
                        margin: 10px auto 0;
                    }

                </style>

                <div id="wppp_optimization-chart" class="wppp_chart">
                    <svg width="320" height="320">
                        <circle class="main" transform="rotate(-90, 160, 160)" r="128" cx="160" cy="160"></circle>
                        <circle class="inner" r="120" cx="160" cy="160"></circle>
                    </svg>
                    <div class="wppp_value">
                        <div class="wppp_percentage" id="wppp_savings-percentage">
                            <span><?php echo $size_saved; ?></span>%
                        </div>
                        <div class="wppp_label">savings</div>
                        <table class="wp-pixpie-plugin-dashboard-stats wppp_value-table">

                            <tr>
                                <td>Compressed images:</td>
                                <td><?php if( $total_compressed_images <= 10000 ){ echo $total_compressed_images; } else { echo '>' . $total_compressed_images; }; ?></td>
                            </tr>

							<?php
							if ( $total_compressed_images > 0 ) {
								?>

                                <tr>
                                    <td>Uncompressed size:</td>
                                    <td><?php echo $total_raw_size_display; ?></td>
                                </tr>

                                <tr>
                                    <td>Compressed size:</td>
                                    <td><?php echo $total_compressed_size_display; ?></td>
                                </tr>

								<?php
							}
							?>

                        </table>
                    </div>
                </div>
            </div>
        </div>

		<?php

	} else { ?>
        <i>No compressed images</i>
		<?php } ?>

    <div class="configDashbord">
				<span>
					<a href="<?php echo ( get_admin_url () . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS ); ?>">Settings</a>
				</span>
        <span>|</span>
        <span>
					<a href="<?php echo ( get_admin_url () . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_REVERT_ALL ); ?>">Revert All Images</a>
				</span>
    </div>
</div>


