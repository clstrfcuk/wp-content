<div class="wrap wppp-settings-page">

	<h1><?= WPPP_PLUGIN_NAME ?> <small> &mdash; Settings</small></h1>

	<hr/>

	<?php

    $do_activation = false;
    $did_not_activate_reason = null;

    /*
    Save updated settings from POST
    */
    if (
		isset ( $_POST[ WPPP_OPTION_NAME_BUNDLE_ID ] ) &&
		isset ( $_POST[ WPPP_OPTION_NAME_SECRET_KEY ] )
    )
	{

        // Check nonce
        if ( isset( $_POST['_wpnonce'] ) && ( wp_verify_nonce($_POST['_wpnonce'], 'save_settings') ) ) {

            wppp_log_trace(
                'nonce OK: ',
                0, '', '', 'settings'
            );

            // Sanitize
            $new_bundle_id = sanitize_text_field( $_POST[ WPPP_OPTION_NAME_BUNDLE_ID ] );
            $new_secret_key = sanitize_text_field( $_POST[ WPPP_OPTION_NAME_SECRET_KEY ] );
            $new_keep_original = sanitize_text_field( $_POST[ WPPP_OPTION_NAME_KEEP_ORIGINAL ] );

            update_option( WPPP_OPTION_NAME_BUNDLE_ID, $new_bundle_id );
            update_option( WPPP_OPTION_NAME_SECRET_KEY, $new_secret_key );
            update_option( WPPP_OPTION_NAME_KEEP_ORIGINAL, $new_keep_original );

            $do_activation = true;

        } else {
            wppp_log_warning(
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
    )
    {

        // Sanitize
        $new_bundle_id = sanitize_text_field( $_GET['bundle_id'] );
        $new_secret_key = sanitize_text_field( $_GET['secret_key'] );

        update_option( WPPP_OPTION_NAME_BUNDLE_ID, $new_bundle_id );
        update_option( WPPP_OPTION_NAME_SECRET_KEY, $new_secret_key );

        $do_activation = true;

    }


    /*
     * If there were new settings - activate
     */
	if ( $do_activation ) {

        if ( ! wppp_is_option_empty(WPPP_OPTION_NAME_BUNDLE_ID ) &&
            ! wppp_is_option_empty( WPPP_OPTION_NAME_SECRET_KEY )
        ) {

            // if not active try to activate
            $plugin_status = get_option( WPPP_OPTION_NAME_STATUS );

            // try to activate
            $salt = WPPP_API_AUTH_SALT;
            wppp_log_trace(
                'salt: ' . $salt,
                0, '', '', 'settings'
            );

            $timestamp = time();
            wppp_log_trace(
                'timestamp: ' . $timestamp,
                0, '', '', 'settings'
            );

            $secret_key = wppp_get_option_no_slashes( WPPP_OPTION_NAME_SECRET_KEY );
            wppp_log_trace(
                'secret_key: ' . $secret_key,
                0, '', '', 'settings'
            );

            $secret = $secret_key . $salt . $timestamp;
            wppp_log_trace(
                'secret: ' . $secret,
                0, '', '', 'settings'
            );

            $hash = hash( 'sha256', $secret );
            wppp_log_trace(
                'hash: ' . $hash,
                0, '', '', 'settings'
            );

            $reverse_url = wppp_get_option_no_slashes( WPPP_OPTION_NAME_BUNDLE_ID );
            wppp_log_trace(
                'reverse_url: ' . $reverse_url,
                0, '', '', 'settings'
            );

            $post = array(
                'reverseUrlId' => $reverse_url,
                'timestamp' => $timestamp,
                'hash' => $hash,
                'serverSdkType' => 3,
                'sdkVersion' => '1.0.0'
            );

            $error_message = wppp_call_auth_api( $post );
            wppp_log_trace(
                'error_message: ' . $error_message,
                0, '', '', 'settings'
            );


            if ( isset( $error_message ) ) {

                wppp_log_debug(
                    'auth failed',
                    0, '', '', 'settings'
                );

                update_option( WPPP_OPTION_NAME_STATUS, 'failed' );

                ?>

                <div class="error notice wp-pixpie-plugin-auth-failed-error" >
                    <p>
                        <?php echo $error_message; ?>
                    </p>
                </div>

                <?php

            } else {

                wppp_log_debug(
                    'auth OK',
                    0, '', '', 'settings'
                );

            }

        } else {
            update_option( WPPP_OPTION_NAME_STATUS, 'inactive' );
            $did_not_activate_reason = 'BundleID or Secret Key was empty';
        }
    }


    if ( isset( $did_not_activate_reason ) && ( $did_not_activate_reason != null ) ) {
        wppp_sent_auth_failure(
            'Cannot authenticate',
            'Reason: ' . $did_not_activate_reason
        );
    }


    if (
        ( wppp_is_option_empty( WPPP_OPTION_NAME_BUNDLE_ID ) ) ||
        ( wppp_is_option_empty( WPPP_OPTION_NAME_SECRET_KEY ) )
    ) {

        ?>

        <div class="notice error wppp-settings-get-credentials-admin-notice" >
            <p>
                Looks like you haven’t authenticated your plugin in Pixpie yet.<br/>
                If you haven't created Pixpie account before - fill the form below and start using the plugin in 2 clicks.<br/>
                If you are already registered - check WP plugin documentation page to add your Bundle ID and Secret key:<br/>
            </p>
            <p>
                <a href="https://pixpie.atlassian.net/wiki/display/DOC/Wordpress+plugin#WordPressplugin-activation"
                   target="_blank">
                    https://pixpie.atlassian.net/wiki/display/DOC/Wordpress+plugin#WordPressplugin-activation
                </a>
            </p>
        </div>

        <?php
    }


    // if all credentials empty - wrap in no-account class element
    if (
    ( wppp_is_option_empty( WPPP_OPTION_NAME_BUNDLE_ID ) ) &&
    ( wppp_is_option_empty( WPPP_OPTION_NAME_SECRET_KEY ) )
    ) {

    ?>

    <form
            method="POST"
            action="<?php echo ( get_admin_url() . 'admin-post.php' ); ?>"
    >

        <?php echo wp_nonce_field( 'sign_up_form' ); ?>

        <input type='hidden' name='action' value='submit-sign-up-form' />

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
                </td>
            </tr>
        </table>
        <p>
            <input type="submit" name="" value="Sign Up" class="button-primary">
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
            <b class="wppp-settings-plugin-status-<?php echo wppp_get_option_no_slashes( WPPP_OPTION_NAME_STATUS ); ?>">
                <?php echo wppp_get_option_no_slashes( WPPP_OPTION_NAME_STATUS ); ?>
            </b>
        </i>
	</p>

	<form method="POST" action="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS ); ?>">
		<table class="form-table wppp-settings-table" width="100%">

            <?php echo wp_nonce_field( 'save_settings' ); ?>

			<tr>
				<th>
					<label for="<?php echo WPPP_OPTION_NAME_BUNDLE_ID ?>">Bundle ID</label>
				</th>
				<td>
					<input class="bundle-id" type="text" id="<?php echo WPPP_OPTION_NAME_BUNDLE_ID ?>" value="<?= stripslashes( get_option( WPPP_OPTION_NAME_BUNDLE_ID ) ); ?>" name="<?php echo WPPP_OPTION_NAME_BUNDLE_ID ?>" >
				</td>
			</tr>
			<tr>
				<th>
					<label for="<?php echo WPPP_OPTION_NAME_SECRET_KEY ?>">Secret Key</label>
				</th>
				<td>
					<input class="secret-key" type="text" id="<?php echo WPPP_OPTION_NAME_SECRET_KEY ?>" value="<?= stripslashes( get_option( WPPP_OPTION_NAME_SECRET_KEY ) ); ?>" name="<?php echo WPPP_OPTION_NAME_SECRET_KEY ?>" >
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
						value="1" <?php checked( 1, get_option( WPPP_OPTION_NAME_KEEP_ORIGINAL ), true ); ?> 
					/> 				
				</td>
			</tr>
		</table>
		<p>
			<input type="submit" name="" value="Save Settings" class="button-primary">
		</p>
	</form>



    <?php

    // if all credentials empty - wrap in no-account class element (closing div)
    if (
    ( wppp_is_option_empty( WPPP_OPTION_NAME_BUNDLE_ID ) ) &&
    ( wppp_is_option_empty( WPPP_OPTION_NAME_SECRET_KEY ) )
    ) {

    ?>

            </div> <!-- end of .accordion > div -->
        </div> <!-- end of .accordion -->
    </div> <!-- closing of .no-account -->

    <?php

    } // end of no-account closing

    ?>



</div>
