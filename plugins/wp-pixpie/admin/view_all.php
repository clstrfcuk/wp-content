<div class="wrap">

    <h1><?= WPPP_PLUGIN_NAME ?>
        <small> &mdash; View All Images</small>
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

		<?php
	}

	?>

    <hr/>

    <p>(last 50 images)</p>

	<?php

	$all_sizes = wppp_get_image_sizes ();

	$compressed = wppp_get_all_images_ids ();
	$comp_count = count ( $compressed );

	// limit to 50
	$compressed = array_slice ( $compressed, 0, 50 );

	if ( $comp_count > 0 ) {

		?>

        <table>

            <thead>

            <th></th>
            <th style="font-size: 10px;">original</th>
            <th style="font-size: 10px;">original_uncomp</th>
			<?php
			foreach ( $all_sizes as $size_name => $size_val ) {
				?>
                <th style="font-size: 10px;"><?php echo $size_name; ?></th><?php
			}
			?>

            </thead>
            <tbody>

			<?php

			// check each image
			foreach ( $compressed as $image_id ) {

				if ( wp_attachment_is_image ( $image_id ) ) {

					$meta = wp_get_attachment_metadata ( $image_id );

					wppp_view_all_print_image_title ( $meta, $all_sizes );

					wppp_view_all_print_is_compressed ( $meta );

					// used with uncomp and sizes
					$time = substr ( $meta['file'], 0, 7 ); // Extract the date in form "2015/04"
					$upload_dir = wp_upload_dir ( $time );

					// full
					$image = wp_get_attachment_image_src ( $image_id, 'full', false );
					$dimensions = $image[1] . 'x' . $image[2];

					$full_uncomp_filename = $image[0];
					$exploded_filepath = explode ( ".", $full_uncomp_filename );
					$original_file_extension = end ( $exploded_filepath );

					$uncomp_filename =
						str_replace ( ( $upload_dir['url'] . '/' ), '', $full_uncomp_filename );

					$full_uncomp_filename = $time . '/' . $uncomp_filename;

					$original_file_name = str_replace (
						( "." . $original_file_extension ),
						'',
						$uncomp_filename );

					$original_file_path =
						$upload_dir['path'] . '/' .
						$original_file_name . '.' . $original_file_extension;

					$img_in_size = wp_get_attachment_image_url ( $image_id, 'full', false );

					wppp_view_all_print_original_image ( $image_id, $dimensions, $img_in_size,
						$original_file_path );

					wppp_view_all_print_orig_uncomp_img ( $meta, $image_id, $original_file_name,
						$original_file_extension, $upload_dir );

					foreach ( $all_sizes as $size_name => $size_val ) {

						wppp_view_all_print_image_size ( $image_id, $size_name, $meta, $upload_dir );

					}

				} // end of is_image

				else {

					?>
                    <tr>
                        <td colspan="<?php echo ( count ( $all_sizes ) + 2 ); ?>">
                            <hr/>
                            &rarr; <?php echo ( wp_get_attachment_link ( $image_id ) ); ?> - Not an image
                        </td>
                    </tr>

					<?php

				}

			}

			?>

            </tbody>
        </table>

		<?php

	} else {
		?>
        <b>No converted images...</b>
		<?php
	}

	?>

</div>


