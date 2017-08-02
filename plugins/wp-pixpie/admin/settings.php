<div class="wrap wppp-settings-page">

    <h1><?= WPPP_PLUGIN_NAME ?>
        <small> &mdash; Settings</small>
    </h1>

    <hr/>

	<?php

	if ( isset ( $_GET['accountCreated'] ) ) {
		wppp_log_trace (
			'redirect sign up ' . $_GET['accountCreated'],
			0, '', '', 'settings ( redirect )'
		);
	}

	$accountCreated = $_GET['accountCreated'];

	$do_activation = false;
	$did_not_activate_reason = null;

	/*
	images size to bd
	*/
	$all_sizes = wppp_get_image_sizes ();
	$all_sizes_name = [];

	foreach ( $all_sizes as $size_name => $size_val ) {
		if ( strrpos ( $size_name, 'uncomp' ) ) {
			continue;
		}
		$all_sizes_name[ $size_name ] = $size_val;
	}

	/*
	Save updated settings from POST
	*/
	if (
		isset ( $_POST[ WPPP_OPTION_NAME_BUNDLE_ID ] ) &&
		isset ( $_POST[ WPPP_OPTION_NAME_SECRET_KEY ] )
	) {

		// Check nonce
		if ( isset( $_POST['_wpnonce'] ) && ( wp_verify_nonce ( $_POST['_wpnonce'], 'save_settings' ) ) ) {

			wppp_log_trace (
				'nonce OK: ',
				0, '', '', 'settings'
			);

			// Sanitize
			$new_bundle_id = sanitize_text_field ( $_POST[ WPPP_OPTION_NAME_BUNDLE_ID ] );
			$new_secret_key = sanitize_text_field ( $_POST[ WPPP_OPTION_NAME_SECRET_KEY ] );
			$new_keep_original = sanitize_text_field ( $_POST[ WPPP_OPTION_NAME_KEEP_ORIGINAL ] );
			$new_all_size_name = $_POST[ WPPP_OPTION_IMGS_SIZE ];

			if ( get_option ( WPPP_OPTION_IMGS_SIZE ) == false ) {

				$new_all_size_name = [];

				foreach ( $all_sizes_name as $size_name => $size_val ) {
					array_push ( $new_all_size_name, $size_name );
				}

			}

			update_option ( WPPP_OPTION_NAME_BUNDLE_ID, $new_bundle_id );
			update_option ( WPPP_OPTION_NAME_SECRET_KEY, $new_secret_key );
			update_option ( WPPP_OPTION_NAME_KEEP_ORIGINAL, $new_keep_original );
			update_option ( WPPP_OPTION_IMGS_SIZE, $new_all_size_name );

			$do_activation = true;

		} else {
			wppp_log_warning (
				'nonce not valid',
				0, '', '', 'settings'
			);
		}

	} else {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			// if POST but something went wrong
			$did_not_activate_reason = 'BundleID or Secret Key POST parameters were not set';
		}
	}


	/*
	Save updated settings from URL
	*/
	if (
		isset ( $_GET['bundle_id'] ) &&
		isset ( $_GET['secret_key'] )
	) {

		// Sanitize
		$new_bundle_id = sanitize_text_field ( $_GET['bundle_id'] );
		$new_secret_key = sanitize_text_field ( $_GET['secret_key'] );

		update_option ( WPPP_OPTION_NAME_BUNDLE_ID, $new_bundle_id );
		update_option ( WPPP_OPTION_NAME_SECRET_KEY, $new_secret_key );

		$do_activation = true;

	}


	/*
	 * If there were new settings - activate
	 */
	if ( $do_activation ) {

		if ( ! wppp_is_option_empty ( WPPP_OPTION_NAME_BUNDLE_ID ) &&
			! wppp_is_option_empty ( WPPP_OPTION_NAME_SECRET_KEY )
		) {

			// if not active try to activate
			$plugin_status = get_option ( WPPP_OPTION_NAME_STATUS );

			// try to activate
			$salt = WPPP_API_AUTH_SALT;
			wppp_log_trace (
				'salt: ' . $salt,
				0, '', '', 'settings'
			);

			$timestamp = time ();
			wppp_log_trace (
				'timestamp: ' . $timestamp,
				0, '', '', 'settings'
			);

			$secret_key = wppp_get_option_no_slashes ( WPPP_OPTION_NAME_SECRET_KEY );
			wppp_log_trace (
				'secret_key: ' . $secret_key,
				0, '', '', 'settings'
			);

			$secret = $secret_key . $salt . $timestamp;
			wppp_log_trace (
				'secret: ' . $secret,
				0, '', '', 'settings'
			);

			$hash = hash ( 'sha256', $secret );
			wppp_log_trace (
				'hash: ' . $hash,
				0, '', '', 'settings'
			);

			$reverse_url = wppp_get_option_no_slashes ( WPPP_OPTION_NAME_BUNDLE_ID );
			wppp_log_trace (
				'reverse_url: ' . $reverse_url,
				0, '', '', 'settings'
			);
//            previous parameters
			/*
						'timestamp' => $timestamp,
							'hash' => $hash,
							'serverSdkType' => 3,
							'sdkVersion' => '1.0.0'*/

			$post = array(
				'reverseUrlId'  => $reverse_url,
				'secretKey'     => $secret_key,
				'pluginType'    => 1,
				'pluginVersion' => WPPP_VERSION
			);

			$error_message = wppp_call_auth_api ( $post );
			wppp_log_trace (
				'error_message: ' . $error_message,
				0, '', '', 'settings'
			);


			if ( isset( $error_message ) ) {

				wppp_log_debug (
					'auth failed',
					0, '', '', 'settings'
				);

				update_option ( WPPP_OPTION_NAME_STATUS, 'failed' );

				?>

                <div class="error notice wp-pixpie-plugin-auth-failed-error">
                    <p>
						<?php echo $error_message; ?>
                    </p>
                </div>

				<?php

			} else {

				wppp_log_debug (
					'auth OK',
					0, '', '', 'settings'
				);

			}

		} else {
			update_option ( WPPP_OPTION_NAME_STATUS, 'inactive' );
			$did_not_activate_reason = 'BundleID or Secret Key was empty';
		}
	}

	if (
	( isset( $_GET['accountCreated'] ) )
	) {

		?>

        <div class="notice notice-success wppp-settings-get-credentials-admin-notice">
            <p>
                To start using the plugin, copy the "Bundle ID" and "Secret key" values from the email that we sent you
                to the fields below.
            </p>

        </div>

		<?php
	}

	if ( get_option ( 'wppp_overlimit' ) ) {

		?>

        <div class="notice error wppp-settings-get-credentials-admin-notice">
            <p>
                You overlimit your compressions. Wait to end of mounth or change tariff plan.
            </p>

        </div>

		<?php
	}


	// if all credentials empty - wrap in no-account class element
	if (
	( wppp_is_option_empty ( WPPP_OPTION_NAME_BUNDLE_ID ) ) &&
	( wppp_is_option_empty ( WPPP_OPTION_NAME_SECRET_KEY ) ) && empty ( $_GET['accountCreated'] )
	) {

	if ( isset ( $_GET['error_email'] ) ) {
		$error_email = intval ( sanitize_text_field ( $_GET['error_email'] ) );
	}
	if ( isset ( $_GET['error_password'] ) ) {
		$error_password = intval ( sanitize_text_field ( $_GET['error_password'] ) );
	}

	?>

    <form
            method="POST"
            action="<?php echo ( get_admin_url () . 'admin-post.php' ); ?>"
    >

		<?php echo wp_nonce_field ( 'sign_up_form' ); ?>

        <input type="hidden" name="">

        <input type='hidden' name='action' value='submit-sign-up-form'/>

        <table class="form-table wppp-settings-table" width="100%">

            <tr>
                <th>
                    <label for="wppp-new-account-email">Email</label>
                </th>
                <td>
                    <input
                            class="wppp-new-account-email"
                            type="email" id=wppp-new-account-email"
                            name="email"
                            required="required"
                    />
					<?php
					if ( 1 == $error_email ) {
						echo ( '<p class="wppp-signup-form-error">Email is not valid</p>' );
					}
					?>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="wppp-new-account-password">Password</label>
                </th>
                <td>
                    <input
                            class="wppp-new-account-password"
                            type="password"
                            id="wppp-new-account-password"
                            name="password"
                            required="required"
                    />
					<?php
					if ( 1 == $error_password ) {
						echo ( '<p class="wppp-signup-form-error">Password should be at least 6 characters long and contain only numbers, latin letters or special symbols</p>' );
					}
					?>
                </td>
            </tr>
        </table>
        <p>
            <input type="submit" name="" value="Sign Up / Sing In" class="button-primary">
        </p>
    </form>

<hr/>

    <p>
        Or enter your Bundle ID and Secret Key here:
    </p>

    <div class="no-account">

        <div class="accordion">

            <a href="#" class="expand">Expand</a>

            <div class="collapsible hidden">

				<?php

				} // end of no-account opening

				?>

                <p class="wppp-settings-plugin-status">
                    <i>
                        Plugin/auth status:
                        <b class="wppp-settings-plugin-status-<?php echo wppp_get_option_no_slashes ( WPPP_OPTION_NAME_STATUS ); ?>">
							<?php echo wppp_get_option_no_slashes ( WPPP_OPTION_NAME_STATUS ); ?>
                        </b>
                    </i>
                </p>

                <form method="POST"
                      action="<?php echo ( get_admin_url () . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS ); ?>">
                    <table class="form-table wppp-settings-table" width="100%">

						<?php echo wp_nonce_field ( 'save_settings' ); ?>

                        <tr>
                            <th>
                                <label for="<?php echo WPPP_OPTION_NAME_BUNDLE_ID ?>">Bundle ID</label>
                            </th>
                            <td>
                                <input class="bundle-id" type="text" id="<?php echo WPPP_OPTION_NAME_BUNDLE_ID ?>"
                                       value="<?= stripslashes ( get_option ( WPPP_OPTION_NAME_BUNDLE_ID ) ); ?>"
                                       name="<?php echo WPPP_OPTION_NAME_BUNDLE_ID ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="<?php echo WPPP_OPTION_NAME_SECRET_KEY ?>">Secret Key</label>
                            </th>
                            <td>
                                <input class="secret-key" type="text" id="<?php echo WPPP_OPTION_NAME_SECRET_KEY ?>"
                                       value="<?= stripslashes ( get_option ( WPPP_OPTION_NAME_SECRET_KEY ) ); ?>"
                                       name="<?php echo WPPP_OPTION_NAME_SECRET_KEY ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="<?php echo WPPP_OPTION_NAME_KEEP_ORIGINAL ?>">Keep Original Files</label>
                            </th>
                            <td>
                                <input
                                        type="checkbox"
                                        id="<?php echo WPPP_OPTION_NAME_KEEP_ORIGINAL ?>"
                                        name="<?php echo WPPP_OPTION_NAME_KEEP_ORIGINAL ?>"
                                        value="1" <?php checked ( 1, get_option ( WPPP_OPTION_NAME_KEEP_ORIGINAL ), true ); ?>
                                />
                            </td>
                        </tr>
                    </table>

					<?php if ( wppp_get_option_no_slashes ( WPPP_OPTION_NAME_STATUS ) == 'active' ) { ?>

                        <div class="wppp-Check-size-img"><p class="subHeadSeattings">Check image sizes that should be
                                compressed:</p>

                            <ul class="imageSizesPiexpie">
								<?php foreach ( $all_sizes_name as $size_name => $size_val ) { ?>
                                    <li>
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    name="wppp_option_imgs_size[]"
                                                    value="<?php echo $size_name ?>"
												<?php

												if ( get_option ( WPPP_OPTION_IMGS_SIZE ) == false ) {
													?>
                                                    checked="checked"
													<?php
												}
												?>

												<?php if ( is_array ( get_option ( WPPP_OPTION_IMGS_SIZE ) ) ) {
													checked ( true, in_array ( $size_name, get_option ( WPPP_OPTION_IMGS_SIZE ) ) );
												} ?>>
											<?php echo $size_name ?>
											<?php

											if ( $size_val['width'] == 0 ) {
												echo $size_val['height'] . ' px height';
											} elseif ( $size_val['height'] == 0 ) {
												echo $size_val['width'] . ' px width';
											} else {
												echo $size_val['width'] . 'x' . $size_val['height'] . ' px';
											}

											?>
                                        </label>
                                    </li>
								<?php } ?>
                            </ul>

                            <div>
                                With these selected sizes (thumbnails) you are able to compress at least <span
                                        class="numberCompressImgs">N</span> images according to your current (500
                                compressions per month for $0.00) tariff plan.<br>
                                If you use plugins that increase the count of image sizes (like WP Retina 2x) then the
                                number of compressions could be more.<br>
                            </div>
                        </div>

					<?php } ?>
                    <p>
                        <input type="submit" name="" value="Save Settings" class="button-primary">
                    </p>
                </form>

				<?php

				// if all credentials empty - wrap in no-account class element (closing div)
				if (
				( wppp_is_option_empty ( WPPP_OPTION_NAME_BUNDLE_ID ) ) &&
				( wppp_is_option_empty ( WPPP_OPTION_NAME_SECRET_KEY ) )
				) {

				?>

            </div> <!-- end of .accordion > div -->
        </div> <!-- end of .accordion -->
    </div> <!-- closing of .no-account -->

<?php

} // end of no-account closing


if ( ! get_option ( 'WPPP_OPTION_NAME_BUNDLE_ID' ) && ! get_option ( 'WPPP_OPTION_NAME_SECRET_KEY' ) ) {


	$auth_url = 'http://' . WPPP_SAFE_REDIRECT_CLOUD_HOST . 'payment/status';
	wppp_log_trace (
		'settings payment auth url: ' . $auth_url,
		0, '', '', 'settings_payment_check'
	);

	$headers = wppp_get_common_headers ();

	wppp_log_trace (
		'setting payment header: ' . print_r ( $headers, true ),
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
		'setting payment header: ' . print_r ( $response, true ),
		0, '', '', 'settings_payment_check'
	);

	$action_available_status = json_decode ( $response['body'] ) -> actionAvailableStatus;

	$response_status = json_decode ( $response['body'] ) -> responseMessage;
	$response_agree_form = json_decode ( $response['body'] ) -> shortResponseMessage;

	update_option ( 'wppp_action_available_status', $action_available_status );

	wppp_log_trace (
		'change action status : ' . $action_available_status,
		0, '', '', 'settings_payment_check'
	);

	wppp_log_trace (
		'action response : ' . $response_status,
		0, '', '', 'settings_payment_check'
	);
}

$payment = get_option ( 'wppp_update_payment' );

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
	delete_option ( 'wppp_update_payment' );

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

?>

</div>
