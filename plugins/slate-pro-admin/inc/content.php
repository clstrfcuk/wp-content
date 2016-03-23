<?php
include( __DIR__ . '/variables.php' );
include( __DIR__ . '/colors.php' );
?>

<div class="wrap slate-settings">

	<?php
	if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) {
		$slate_pro_license = get_site_option( 'slate_pro_license' );
	} else {
		$slate_pro_license = get_option( 'slate_pro_license' );
	}
	$statuses = array( '', 'removed', 'failed', 'used', 'invalid', 'oops' );
	if ( in_array( $slate_pro_license['licenseStatus'], $statuses ) ) {
		date_default_timezone_set( 'America/Los_Angeles' );
		$expire_date = time() - ( 60 * 60 * 24 * 3 );
		if ( $expire_date > strtotime( $slate_pro_settings['licenseDate'] ) ) { ?>
		<div class="slate-error">
			<h4><?php _e( 'It looks like you’re using', 'slate-pro' ); ?> Slate Pro <?php _e( 'without a license.', 'slate-pro' ); ?></h4>
			<p><?php _e( 'If you like', 'slate-pro' ); ?> Slate Pro <?php _e( 'please consider', 'slate-pro' ); ?> <a href="admin.php?page=slate_pro_license"><?php _e( 'entering a license key', 'slate-pro' ); ?></a> <?php _e( 'to help support us and continue its development. You’ll also receive free updates and technical support!', 'slate-pro' ); ?> <a href="http://sevenbold.com/wordpress/slate-pro/" target="_blank"><?php _e( 'Visit', 'slate-pro' ); ?> Seven Bold <?php _e( 'for purchasing information.', 'slate-pro' ); ?></a></p>
		</div>
		<?php }
	} ?>

	<?php if ( isset( $_GET['settings-updated'] ) || isset( $_GET['updated'] ) ) { ?>
		<div class="updated">
			<p><strong><?php _e( 'Settings saved.' ) ?></strong></p>
		</div>
		<div class="wrap"><h2 style="display:none;"></h2></div><!-- WordPress Hack to show Update Notice -->

	<?php } ?>
	<form method="post" action="<?php if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) { ?>edit.php?action=slate_pro_network<?php } else { ?>options.php<?php } ?>">

		<?php if ( is_multisite() && is_plugin_active_for_network('slate-pro-admin/slate-pro-admin.php') ) { } else { settings_fields( 'slate_pro_settings' ); } ?>

		<div id="slate__colorSchemes" class="pageSection <?php if ( 'slate_pro_color_schemes' !== $page ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Color Schemes', 'slate-pro' ); ?></h2>

			<section class="premadeColors">
				<div class="colorDefault">
					<label <?php if ( 'default' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="default" <?php if ( 'default' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Default', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorDefault['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorDefault['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorDefault['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorDefault['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorLight">
					<label <?php if ( 'light' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="light" <?php if ( 'light' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Light', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorLight['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorLight['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorLight['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorLight['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorBlue">
					<label <?php if ( 'blue' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="blue" <?php if ( 'blue' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Blue', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorBlue['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorBlue['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorBlue['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorBlue['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorCoffee">
					<label <?php if ( 'coffee' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="coffee" <?php if ( 'coffee' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Coffee', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorCoffee['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorCoffee['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorCoffee['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorCoffee['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorEctoplasm">
					<label <?php if ( 'ectoplasm' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="ectoplasm" <?php if ( 'ectoplasm' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Ectoplasm', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorEctoplasm['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorEctoplasm['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorEctoplasm['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorEctoplasm['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorMidnight">
					<label <?php if ( 'midnight' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="midnight" <?php if ( 'midnight' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Midnight', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorMidnight['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorMidnight['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorMidnight['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorMidnight['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorOcean">
					<label <?php if ( 'ocean' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="ocean" <?php if ( 'ocean' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Ocean', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorOcean['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorOcean['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorOcean['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorOcean['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorSunrise">
					<label <?php if ( 'sunrise' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="sunrise" <?php if ( 'sunrise' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Sunrise', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorSunrise['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorSunrise['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorSunrise['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorSunrise['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
				<div class="colorCustom">
					<label <?php if ( 'custom' == $slate_pro_settings['colorScheme'] ) { ?> class="selected"<?php } ?>>
						<input type="radio" name="slate_pro_settings[colorScheme]" value="custom" <?php if ( 'custom' == $slate_pro_settings['colorScheme'] ) { ?> checked="checked"<?php } ?>> <?php _e( 'Custom', 'slate-pro' ); ?>
						<div><span style="background:<?php echo slate_pro_sanitize_hex( $colorCustom['adminMenuBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorCustom['adminBarBgColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorCustom['adminTopLevelSelectedTextColor'] ) ?>;"></span><span style="background: <?php echo slate_pro_sanitize_hex( $colorCustom['adminTopLevelTextHoverColor'] ) ?>;"></span></div>
					</label>
				</div>
			</section>

			<!-- Color Nav -->
			<div class="colorNav">
				<h3><?php _e( 'Custom Color Options', 'slate-pro' ); ?></h3>
				<ul>
					<li class="loginPageColors"><a class="nav-tab selected" href="#"><?php _e( 'Login Page', 'slate-pro' ); ?></a></li>
					<li class="adminMenuColors"><a class="nav-tab" href="#"><?php _e( 'Admin Menu', 'slate-pro' ); ?></a></li>
					<li class="adminBarColors"><a class="nav-tab" href="#"><?php _e( 'Admin Bar', 'slate-pro' ); ?></a></li>
					<li class="adminFooterColors"><a class="nav-tab" href="#"><?php _e( 'Admin Footer', 'slate-pro' ); ?></a></li>
					<li class="adminContentColors"><a class="nav-tab" href="#"><?php _e( 'Content', 'slate-pro' ); ?></a></li>
					<li class="adminSidebarColors"><a class="nav-tab" href="#"><?php _e( 'Sidebar/Sortables', 'slate-pro' ); ?></a></li>
				</ul>
			</div>
			<!-- Login Page -->
			<section class="colorSection loginPageColors" <?php if ( 'custom' == $slate_pro_settings['colorScheme'] ) { ?> style="display: block;"<?php } ?>>
				<?php colorSection( $colorSectionLoginPage, $colorCustom ) ?>
			</section>

			<!-- Admin Menu -->
			<section class="colorSection adminMenuColors">
				<?php colorSection( $colorSectionAdminMenu, $colorCustom ) ?>
			</section>

			<!-- Admin Bar -->
			<section class="colorSection adminBarColors">
				<?php colorSection( $colorSectionAdminBar, $colorCustom ) ?>
			</section>

			<!-- Footer -->
			<section class="colorSection adminFooterColors">
				<?php colorSection( $colorSectionAdminFooter, $colorCustom ) ?>
			</section>

			<!-- Content -->
			<section class="colorSection adminContentColors">
				<?php colorSection( $colorSectionContent, $colorCustom ) ?>
			</section>

			<!-- Sidebar -->
			<section class="colorSection adminSidebarColors">
				<?php colorSection( $colorSectionSidebar, $colorCustom ) ?>
			</section>

			<section>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[colorsHideUserProfileColors]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['colorsHideUserProfileColors'] ), 'on' ); ?>>
							<?php _e( 'Hide “Admin Color Scheme” Options on User Profile Pages', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[colorsHideShadows]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['colorsHideShadows'] ), 'on' ); ?>>
							<?php _e( 'Hide Admin Menu and Sidebar Shadows', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<div id="slate__branding" class="pageSection <?php if ( 'slate_pro_branding' !== $page ) { echo 'hide'; } ?>">
			<h2><?php _e( 'Branding', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Login Page Logo Link Title and Address', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label><?php _e( 'Link Title', 'slate-pro' ); ?> <input type="text" name="slate_pro_settings[loginLinkTitle]" value="<?php echo esc_attr( $loginLinkTitle ); ?>"></label>
					</li>
					<li>
						<label><?php _e( 'Link Address', 'slate-pro' ); ?> <input type="text" name="slate_pro_settings[loginLinkUrl]" value="<?php echo esc_attr( $loginLinkUrl ); ?>"></label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Login Page Logo', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<div id="slate__loginLogoImage" class="imageContainer">
							<?php echo wp_kses_post( $loginLogo ); ?>
						</div>
						<input type="text" class="imageValue" id="slate__loginLogo" name="slate_pro_settings[loginLogo]" value="<?php echo esc_url( $slate_pro_settings['loginLogo'] ); ?>" placeholder="Image Address" />
					</li>
					<li class="slate__selectLoginLogo">
						<a href="#" class="button imageSelect"><?php _e( 'Select Image', 'slate-pro' ); ?></a>
						<a href="#" class="imageDelete" <?php echo wp_kses_post( $loginLogoDelete ); ?>><?php _e( 'Delete Image', 'slate-pro' ); ?></a>
					</li>
					<li class="slate__description">
						<?php _e( 'Make sure the image is no greater than 320 pixels wide by 80 pixels high.', 'slate-pro' ); ?>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[loginLogoHide]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['loginLogoHide'] ), 'on' ); ?>>
							<?php _e( 'Hide the Login Logo', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Login Page Background Image', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<div id="slate__loginBgImage" class="imageContainer">
							<?php echo wp_kses_post( $loginBgImage ); ?>
						</div>
						<input type="text" class="imageValue" id="slate__loginBg" name="slate_pro_settings[loginBgImage]" value="<?php echo esc_url( $slate_pro_settings['loginBgImage'] ); ?>" placeholder="Image Address" />
					</li>
					<li class="slate__selectLoginBg">
						<a href="#" class="button imageSelect"><?php _e( 'Select Image', 'slate-pro' ); ?></a>
						<a href="#" class="imageDelete" <?php echo wp_kses_post( $loginBgImageDelete ); ?>><?php _e( 'Delete Image', 'slate-pro' ); ?></a>
					</li>
					<li>
						<!-- <p class="slate__description"><?php _e( 'Your logo should be no larger than 320px by 80px or else it will be resized on the login screen.', 'slate-pro' ); ?></p> -->
					</li>
					<li>
						<label>
							<select name="slate_pro_settings[loginBgPosition]">
								<?php
								$lbp = array(
									'left top' => 'Left Top',
									'left center' => 'Left Center',
									'left bottom' => 'Left Bottom',
									'center top' => 'Center Top',
									'center center' => 'Center Center',
									'center bottom' => 'Center Bottom',
									'right top' => 'Right Top',
									'right center' => 'Right Center',
									'right bottom' => 'Right Bottom',
									);
								foreach ( $lbp as $key => $value ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"<?php if ( ( $loginBgPosition ) == $key ) { ?> selected="selected"<?php } ?>><?php echo esc_attr( $value ); ?></option>
									<?php
								}
								?>
							</select>
							<?php _e( 'Background Position', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<select name="slate_pro_settings[loginBgRepeat]">
								<?php
								$lbr = array(
									'no-repeat' => 'No Repeat',
									'repeat' => 'Repeat',
									'repeat-x' => 'Repeat Only Horizontally',
									'repeat-y' => 'Repeat Only Vertically',
									);
								foreach ( $lbr as $key => $value ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"<?php if ( $loginBgRepeat == $key ) { ?> selected="selected"<?php } ?>><?php echo esc_attr( $value ); ?></option>
									<?php
								}
								?>
							</select>
							<?php _e( 'Background Repeat', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[loginBgFull]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['loginBgFull'] ), 'on' ); ?>>
							<?php _e( 'Make the Background Image Fill the Page', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Full Width Menu Logo', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<div id="slate__adminLogoImage" class="imageContainer">
							<?php echo wp_kses_post( $adminLogo ); ?>
						</div>
						<input type="text" class="imageValue" id="slate__adminLogo" name="slate_pro_settings[adminLogo]" value="<?php echo esc_url( $slate_pro_settings['adminLogo'] ); ?>" placeholder="Image Address" />
					</li>
					<li class="slate__selectAdminLogo">
						<a href="#" class="button imageSelect"><?php _e( 'Select Image', 'slate-pro' ); ?></a>
						<a href="#" class="imageDelete" <?php echo wp_kses_post( $adminLogoDelete ); ?>><?php _e( 'Delete Image', 'slate-pro' ); ?></a>
					</li>
					<li class="slate__description">
						<?php _e( 'Make sure the image is no wider than 200 pixels. Double it for high resolution.', 'slate-pro' ); ?>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Collapsed Menu Logo', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<div id="slate__adminLogoFoldedImage" class="imageContainer">
							<?php echo wp_kses_post( $adminLogoFolded ); ?>
						</div>
						<input type="text" class="imageValue" id="slate__adminLogoFolded" name="slate_pro_settings[adminLogoFolded]" value="<?php echo esc_url( $slate_pro_settings['adminLogoFolded'] ); ?>" placeholder="Image Address" />
					</li>
					<li class="slate__selectAdminLogoFolded">
						<a href="#" class="button imageSelect"><?php _e( 'Select Image', 'slate-pro' ); ?></a>
						<a href="#" class="imageDelete" <?php echo wp_kses_post( $adminLogoFoldedDelete ); ?>><?php _e( 'Delete Image', 'slate-pro' ); ?></a>
					</li>
					<li class="slate__description">
						<?php _e( 'Make sure the image is no wider than 36 pixels. Double it for high resolution.', 'slate-pro' ); ?>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Favicon', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<div id="slate__adminFaviconImage" class="imageContainer">
							<?php echo wp_kses_post( $adminFavicon ); ?>
						</div>
						<input type="hidden" class="imageValue" id="slate__adminFavicon" name="slate_pro_settings[adminFavicon]" value="<?php echo esc_url( $slate_pro_settings['adminFavicon'] ); ?>" placeholder="Image Address" />
					</li>
					<li class="slate__selectAdminFavicon">
						<a href="#" class="button imageSelect"><?php _e( 'Select Image', 'slate-pro' ); ?></a>
						<a href="#" class="imageDelete" <?php echo wp_kses_post( $adminFaviconDelete ); ?>><?php _e( 'Delete Image', 'slate-pro' ); ?></a>
					</li>
					<li class="slate__description">
						<?php _e( 'Make sure the image exactly 16 pixels high and 16 pixels wide.', 'slate-pro' ); ?>
					</li>
				</ul>
			</section>
			<?php submit_button(); ?>

		</div>

		<div id="slate__dashboard" class="pageSection <?php if ( 'slate_pro_dashboard' !== $page ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Dashboard', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Welcome Message', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[dashboardHideWelcome]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardHideWelcome'] ), 'on' ); ?>>
							<?php _e( 'Hide the Dashboard Welcome Message', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Custom Widget', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[dashboardCustomWidget]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardCustomWidget'] ), 'on' ); ?>>
							<?php _e( 'Show a Custom Widget on the Dashboard', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label><?php _e( 'Widget Title', 'slate-pro' ); ?> <input type="text" name="slate_pro_settings[dashboardCustomWidgetTitle]" value="<?php echo esc_attr( $dashboardCustomWidgetTitle ); ?>"></label>
					</li>
					<li>
						<label><?php _e( 'Widget Content (HTML Allowed)', 'slate-pro' ); ?>
							<textarea name="slate_pro_settings[dashboardCustomWidgetText]"><?php echo wp_kses_post( force_balance_tags( $dashboardCustomWidgetText ) ); ?></textarea>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Hide Widgets', 'slate-pro' ); ?> <span class="slate__select"><a href="#" class="slate__selectAll"><?php _e( 'Select All', 'slate-pro' ); ?></a> / <a href="#" class="slate__selectNone"><?php _e( 'Select None', 'slate-pro' ); ?></a></span></h3>
				<ul>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardHideActivity]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardHideActivity]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardHideActivity'] ), 'on' ); ?>>
							<?php _e( 'Activity', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardHideNews]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardHideNews]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardHideNews'] ), 'on' ); ?>>
							<?php _e( 'WordPress News', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardRightNow]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardRightNow]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardRightNow'] ), 'on' ); ?>>
							<?php _e( 'At a Glance', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardRecentComments]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardRecentComments]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardRecentComments'] ), 'on' ); ?>>
							<?php _e( 'Recent Comments', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardQuickPress]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardQuickPress]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardQuickPress'] ), 'on' ); ?>>
							<?php _e( 'Quick Press', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardRecentDrafts]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardRecentDrafts]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardRecentDrafts'] ), 'on' ); ?>>
							<?php _e( 'Recent Drafts', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardIncomingLinks]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardIncomingLinks]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardIncomingLinks'] ), 'on' ); ?>>
							<?php _e( 'Incoming Links', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input type="hidden" name="slate_pro_settings[dashboardWidgets][dashboardPlugins]" value="0">
							<input name="slate_pro_settings[dashboardWidgets][dashboardPlugins]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['dashboardWidgets']['dashboardPlugins'] ), 'on' ); ?>>
							<?php _e( 'Plugins', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<div id="slate__adminMenu" class="pageSection <?php if ( 'slate_pro_admin_menu' !== $page ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Admin Menu', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Hide the following Menu Items', 'slate-pro' ); ?> <span class="slate__select"><a href="#" class="slate__selectAll"><?php _e( 'Select All', 'slate-pro' ); ?></a> / <a href="#" class="slate__selectNone"><?php _e( 'Select None', 'slate-pro' ); ?></a></span></h3>

				<ul>
					<?php

					$theMenu = slate_pro_admin_menus();

					if ( isset( $slate_pro_settings['adminMenu'] ) && '' !== $slate_pro_settings['adminMenu'] ) {
						foreach ( $slate_pro_settings['adminMenu'] as $menuItem => $menuHide ) {

							$menuItem = unserialize( base64_decode( $menuItem ) );

							if ( 'on' == $menuHide ) {
								$savedMenu[] = array(
									'Sort' => $menuItem['Sort'],
									'Title' => $menuItem['Title'],
									'Slug' => $menuItem['Slug'],
									);
							}
						}
					}
					if ( ! isset( $savedMenu ) ) {
						$savedMenu = array();
					}

					foreach ( $slate_pro_settings['adminMenuPermissions'] as $userName => $userHide ) {
						if ( 'on' == $userHide ) {
							$adminMenuActive = true;
						}
					}

					if ( isset( $adminMenuActive ) && true == $adminMenuActive ) {
						$theMenu = array_merge( $theMenu, $savedMenu );

						function compare_sort( $a, $b ) {
							if ( $a['Sort'] == $b['Sort'] ) {
								return 0;
							}

							return ( $a['Sort'] < $b['Sort'] ) ? - 1 : 1;
						}

						usort( $theMenu, 'compare_sort' );
					}

					foreach ( $theMenu as $key => $menuItem ) {
						$theMenuItem = base64_encode( serialize( array(
							'Sort' => esc_attr( $menuItem['Sort'] ),
							'Title' => esc_attr( $menuItem['Title'] ),
							'Slug' => esc_attr( $menuItem['Slug'] )
							) ) ); ?>
						<li>
							<label>
								<input name="slate_pro_settings[adminMenu][<?php echo $theMenuItem; ?>]" type="checkbox" <?php if ( isset( $slate_pro_settings['adminMenu'][ $theMenuItem ] ) ) { checked( esc_attr( $slate_pro_settings['adminMenu'][ $theMenuItem ] ), 'on' ); } ?>> <?php echo esc_attr( $menuItem['Title'] ); ?>
							</label>
						</li> <?php
					} ?>
				</ul>

			</section>

			<section>
				<h3><?php _e( 'Apply to the following Users', 'slate-pro' ); ?> <span class="slate__select"><a href="#" class="slate__selectAll"><?php _e( 'Select All', 'slate-pro' ); ?></a> / <a href="#" class="slate__selectNone"><?php _e( 'Select None', 'slate-pro' ); ?></a></span></h3>
				<?php $users = get_users();
				if ( ! ( $users[0] instanceof WP_User) ) {
					return;
				} ?>
				<ul>
					<?php foreach ( $users as $key => $value ) {
						$user_role = $users[$key]->roles;
						$user_id = $users[$key]->ID;
						$username = $users[$key]->user_login;
						$user_first_name = $users[$key]->first_name;
						$user_last_name = $users[$key]->last_name;
						if ( user_can( $user_id, 'edit_posts' ) ) { ?>
						<li>
							<label>
								<input type="hidden" name="slate_pro_settings[adminMenuPermissions][<?php echo $username ?>]" value="0">
								<input name="slate_pro_settings[adminMenuPermissions][<?php echo $username ?>]" type="checkbox" <?php if ( isset($slate_pro_settings['adminMenuPermissions'][ $username ] ) ) { checked( esc_attr( $slate_pro_settings['adminMenuPermissions'][ $username ] ), 'on' ); } ?>> <?php echo $user_first_name ?> <?php echo $user_last_name ?> <?php if ( !empty( $user_first_name ) || !empty( $user_last_name ) ) { ?>(<?php } ?><?php echo $username ?><?php if ( !empty( $user_first_name ) || !empty( $user_last_name ) ) { ?>)<?php } ?>
							</label>
						</li> <?php	}
					} ?>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<div id="slate__adminBar" class="pageSection <?php if ( $page !== 'slate_pro_admin_bar_footer' ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Admin Bar &amp; Footer', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Admin Bar', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[adminBarHideWP]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['adminBarHideWP'] ), 'on' ); ?>>
							<?php _e( 'Hide the WordPress Logo', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[adminBarHide]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['adminBarHide'] ), 'on' ); ?>>
							<?php _e( 'Hide the Admin Bar', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Admin Footer', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[footerTextShow]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['footerTextShow'] ), 'on' ); ?>>
							<?php _e( 'Display Custom Footer Text', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label><?php _e( 'Footer Text (HTML Allowed)', 'slate-pro' ); ?>
							<textarea class="customFooterText" name="slate_pro_settings[footerText]"><?php echo wp_kses_post( force_balance_tags( $footerText ) ); ?></textarea>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[footerVersionHide]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['footerVersionHide'] ), 'on' ); ?>>
							<?php _e( 'Hide Version Number', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[footerHide]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['footerHide'] ), 'on' ); ?>>
							<?php _e( 'Hide the Admin Footer', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<div id="slate__contentNotices" class="pageSection <?php if ( $page !== 'slate_pro_content_notices' ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Content &amp; Notices', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Title', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[contentHideWPTitle]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['contentHideWPTitle'] ), 'on' ); ?>>
							<?php _e( 'Hide "WordPress" in Page Titles', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Tabs', 'slate-pro' ); ?></h3>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[contentHideHelp]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['contentHideHelp'] ), 'on' ); ?>>
							<?php _e( 'Hide the Help Tab', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[contentHideScreenOptions]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['contentHideScreenOptions'] ), 'on' ); ?>>
							<?php _e( 'Hide the Screen Options Tab', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Disable Notices', 'slate-pro' ); ?></h3>
				<p><?php _e( 'Depending on the number of themes and plugins you have installed, the options below may slow down the WordPress admin. If you’re concerned about speed, see the “Hide Notices” option below.', 'slate-pro' ); ?></p>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[noticeWPUpdate]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['noticeWPUpdate'] ), 'on' ); ?>>
							<?php _e( 'Disable WordPress Core Update Notices', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[noticeThemeUpdate]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['noticeThemeUpdate'] ), 'on' ); ?>>
							<?php _e( 'Disable WordPress Theme Update Notices', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label>
							<input name="slate_pro_settings[noticePluginUpdate]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['noticePluginUpdate'] ), 'on' ); ?>>
							<?php _e( 'Disable WordPress Plugin Update Notices', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<section>
				<h3><?php _e( 'Hide Notices', 'slate-pro' ); ?></h3>
				<p><?php _e( 'This is an alternative to completely disabling the notices. This option won’t slow down the admin, but if the user has access to the following pages, they may still see updates are available: /wp-admin/update-core.php, /wp-admin/themes.php, and /wp-admin/plugins.php.', 'slate-pro' ); ?></p>
				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[noticeHideAllUpdates]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['noticeHideAllUpdates'] ), 'on' ); ?>>
							<?php _e( 'Hide All WordPress Update Notices', 'slate-pro' ); ?>
						</label>
					</li>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<div id="slate__permissions" class="pageSection <?php if ( $page !== 'slate_pro_permissions' ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Permissions', 'slate-pro' ); ?></h2>
			<section>
				<p><?php _e( 'Below you can choose which users will see the', 'slate-pro' ); ?> Slate Pro <?php _e( 'plugin. Keep in mind that if you', 'slate-pro' ); ?> <span style='color: #c00;'><?php _e( 'check your own name, you’ll no longer be able to access these settings', 'slate-pro' ); ?></span>.<?php _e( 'If that happens, you’ll need to', 'slate-pro' ); ?> <a href="admin.php?page=slate_pro_permissions"><?php _e( 'bookmark this page', 'slate-pro' ); ?></a> <?php _e( 'or deactivate and reactivate the plugin. Make sure to keep an up-to-date', 'slate-pro' ); ?> <a href="admin.php?page=slate_pro_import_export"><?php _e( 'backup of your settings', 'slate-pro' ); ?></a>. <?php _e( 'Only users who already have permission to access plugins are shown below.', 'slate-pro' ); ?></p>
			</section>
			<?php
			$users = get_users();
			if ( !($users[0] instanceof WP_User) ) {
				return;
			} ?>
			<section>
				<h3><?php _e( 'Hide', 'slate-pro' ); ?> Slate Pro <?php _e( 'from the following Users', 'slate-pro' ); ?></h3>
				<ul>
					<?php foreach ($users as $key => $value) {
						$user_role = $users[$key]->roles;
						$user_id = $users[$key]->ID;
						$username = $users[$key]->user_login;
						$user_first_name = $users[$key]->first_name;
						$user_last_name = $users[$key]->last_name;
						if ( user_can( $user_id, 'activate_plugins' ) ) { ?>
						<li>
							<label>
								<input type="hidden" name="slate_pro_settings[userPermissions][<?php echo $username ?>]" value="0">
								<input name="slate_pro_settings[userPermissions][<?php echo $username ?>]" type="checkbox" <?php if ( isset($slate_pro_settings['userPermissions'][$username] ) ) { checked( esc_attr( $slate_pro_settings['userPermissions'][$username] ), 'on' ); } ?>> <?php echo $user_first_name ?> <?php echo $user_last_name ?> <?php if ( !empty( $user_first_name ) || !empty( $user_last_name ) ) { ?>(<?php } ?><?php echo $username ?><?php if ( !empty( $user_first_name ) || !empty( $user_last_name ) ) { ?>)<?php } ?>
							</label>
						</li>
						<?php	}
					} ?>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<div id="slate__settings" class="pageSection <?php if ( $page !== 'slate_pro_settings' ) { echo 'hide'; } ?>">

			<h2><?php _e( 'Settings', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Custom Login Address', 'slate-pro' ); ?></h3>

				<p><?php _e( 'If you use a third party plugin that changes the WordPress Login page from /wp-login.php to something else, you may need to enter your custom login page address below so that Slate Pro can work on the Login page.', 'slate-pro' ); ?></p>

				<p><?php _e( 'The Login Page Address should be what comes after your domain name. If your login page was at http://yourdomain.com/mycustomlogin, you would enter "/mycustomlogin". If it was at http://yourdomain.com/subdirectory/mycustomlogin, you would enter "/subdirectory/mycustomLogincustomlogin".', 'slate-pro' ); ?></p>

				<ul>
					<li>
						<label>
							<input name="slate_pro_settings[customLogin]" type="checkbox" <?php checked( esc_attr( $slate_pro_settings['customLogin'] ), 'on' ); ?>>
							<?php _e( 'Enable Slate Pro on a Custom Login Page', 'slate-pro' ); ?>
						</label>
					</li>
					<li>
						<label><?php _e( 'Login Page Address', 'slate-pro' ); ?> <input type="text" name="slate_pro_settings[customLoginURL]" value="<?php echo esc_attr( $customLoginURL ); ?>"></label>
					</li>
				</ul>
			</section>

			<?php submit_button(); ?>

		</div>

		<!-- Misc Hidden Inputs -->
		<input type="hidden" name="slate_pro_settings[licenseDate]" value="<?php echo esc_attr( $slate_pro_settings['licenseDate'] ); ?>" />
		<input type="hidden" name="slate_pro_settings[currentPage]" value="<?php echo $page; ?>" />

	</form>

	<div id="slate__importExport" class="pageSection <?php if ( $page !== 'slate_pro_import_export' ) { echo 'hide'; } ?>">
		<form action="" method="post">

			<h2><?php _e( 'Import / Export', 'slate-pro' ); ?></h2>

			<section>
				<h3><?php _e( 'Import', 'slate-pro' ); ?></h3>
				<?php
				global $slate_pro_import_success;
				if ( isset( $slate_pro_import_success ) && true == $slate_pro_import_success ) { ?>
				<!-- <script type="text/javascript">location.reload();</script> -->
				<div class="importSuccess"><?php _e( 'The Import was Successful!', 'slate-pro' ); ?></div>
				<?php } else if ( isset( $slate_pro_import_success ) && false == $slate_pro_import_success ) { ?>
				<div class="importFail"><?php _e( 'Oops, the import didn’t work.', 'slate-pro' ); ?></div>
				<?php } ?>
				<textarea class="slateProImportExport" name="slate_pro_import_settings"></textarea>
				<p class="slate__description">
					<?php _e( 'Paste your settings above and click “Save Changes” to import. It should look like the text in the Export field below.', 'slate-pro' ); ?>
				</p>

				<input type="submit" name="slate_pro_import" class="button button-primary" value="Import Settings">
			</section>


			<section>
				<h3><?php _e( 'Export', 'slate-pro' ); ?></h3>
				<textarea class="slateProImportExport"><?php echo serialize($slate_pro_settings); ?></textarea>
				<p class="slate__description">
					<?php _e( 'Copy and save the text above to backup your', 'slate-pro' ); ?> Slate Pro <?php _e( 'settings.', 'slate-pro' ); ?>
				</p>
			</section>


		</form>
	</div>

	<div id="slate__about" class="pageSection <?php if ( $page !== 'slate_pro_about' ) { echo 'hide'; } ?>">

		<h2><?php _e( 'About', 'slate-pro' ); ?> Slate Pro</h2>
		<section>
			<p><?php _e( 'If you need product support, please leave a comment on the', 'slate-pro' ); ?> <a href="http://codecanyon.net/item/slate-pro-a-white-label-wordpress-admin-theme/9722528?ref=sevenbold" target="_blank"><?php _e( 'CodeCanyon product page', 'slate-pro' ); ?></a>. <?php _e( 'Remember that we’re not able to support third party plugins that might conflict with', 'slate-pro' ); ?> Slate Pro.</p>
			<p>Slate Pro <?php _e( 'was made by', 'slate-pro' ); ?> <a href="http://sevenbold.com" target="_blank">Seven Bold</a>. <?php _e( 'If you’re interested in customizing', 'slate-pro' ); ?> Slate Pro <?php _e( 'or any other web design and development projects, please contact us through the', 'slate-pro' ); ?> <a href="http://sevenbold.com" target="_blank">Seven Bold <?php _e( 'website.', 'slate-pro' ); ?></a></p>
		</section>
		<section>
			<h3><?php _e( 'Email Opt-in', 'slate-pro' ); ?></h3>
			<ul>
				<li><p><?php _e( 'Stay up to date regarding', 'slate-pro' ); ?> Slate Pro <?php _e( 'and other', 'slate-pro' ); ?> Seven Bold <?php _e( 'products.', 'slate-pro' ); ?></p></li>
				<li>
					<!-- Begin MailChimp Signup Form -->
					<div id="mc_embed_signup">
						<form action="//sevenbold.us9.list-manage.com/subscribe/post?u=fb6f314c3674cc509e4e1fcf5&amp;id=e2a990023d" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>

							<div class="wrapper">
								<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Enter Your Email Address">
								<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
								<div style="position: absolute; left: -5000px;"><input type="text" name="b_fb6f314c3674cc509e4e1fcf5_e2a990023d" tabindex="-1" value=""></div>
								<input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="button">
							</div>

						</form>
					</div>
					<!--End mc_embed_signup-->
				</li>
			</ul>
		</section>

	</div>

	<div id="slate__license" class="pageSection <?php if ( $page !== 'slate_pro_license' ) { echo 'hide'; } ?>">
		<?php
		if ( isset( $_POST['slate_pro_license']['licenseKey'] ) && !(empty ( $_POST['slate_pro_license']['licenseKey'] ) ) ) {
			$licenseKey = esc_attr( $_POST['slate_pro_license']['licenseKey'] );

			if ( isset( $_POST['licenseKeyActivate'] ) ) {
				$licenseReply = slate_pro_licensing( esc_attr( $licenseKey ), '0' );
				$licenseReply = $licenseReply['body'];
				if ( is_multisite() && is_plugin_active_for_network('slate-pro-admin/slate-pro-admin.php') ) {
					update_site_option( 'slate_pro_license', array(
						'licenseKey' => esc_attr( $licenseKey ),
						'licenseStatus' => esc_attr( $licenseReply )
						)
					);
				} else {
					update_option( 'slate_pro_license', array(
						'licenseKey' => esc_attr( $licenseKey ),
						'licenseStatus' => esc_attr( $licenseReply )
						)
					);
				}
			} else if ( isset( $_POST['licenseKeyDeactivate'] )  ) {
				$licenseReply = slate_pro_licensing( esc_attr( $licenseKey ), '1' );
				$licenseReply = $licenseReply['body'];
				if ( is_multisite() && is_plugin_active_for_network('slate-pro-admin/slate-pro-admin.php') ) {
					update_site_option( 'slate_pro_license', array(
						'licenseKey' => esc_attr( $licenseKey ),
						'licenseStatus' => esc_attr( $licenseReply )
						)
					);
				} else {
					update_option( 'slate_pro_license', array(
						'licenseKey' => esc_attr( $licenseKey ),
						'licenseStatus' => esc_attr( $licenseReply )
						)
					);
				}
			}
		} else {
			$licenseReply = '';
		}

		if ( is_multisite() && is_plugin_active_for_network('slate-pro-admin/slate-pro-admin.php') ) {
			$slate_pro_license = get_site_option( 'slate_pro_license' );
		} else {
			$slate_pro_license = get_option( 'slate_pro_license' );
		}
		$licenseStatus = esc_attr( $slate_pro_license['licenseStatus'] );
		if ('success' == $licenseStatus || 'active' == $licenseStatus ) {
			$licenseState = 'active';
		} else {
			$licenseState = 'inactive';
		}
		?>
		<form action="" method="post">

			<h2><?php _e( 'License', 'slate-pro' ); ?></h2>
			<p><?php _e( 'Enter your license key to qualify for premium customer support, receive updates via the WordPress Plugins page, access future updates, and other added benefits. Your license key is the same as the “Purchase Code” you received in your CodeCanyon.net purchase receipt email.', 'slate-pro' ); ?></p>
			<?php if ( isset($expire_date) && $expire_date > strtotime( $slate_pro_settings['licenseDate'] ) ) { ?>
			<p><?php _e( 'If you need a license key,', 'slate-pro' ); ?> <a href="http://sevenbold.com/wordpress/slate-pro/" target="_blank"><?php _e( 'please visit', 'slate-pro' ); ?> Seven Bold <?php _e( 'for info on how to buy', 'slate-pro' ); ?> Slate Pro</a>.</p>
			<?php } ?>
			<section>
				<ul>
					<li>
						<label><?php _e( 'License Key', 'slate-pro' ); ?> <input type="text" name="slate_pro_license[licenseKey]" value="<?php if ( 'active' == $licenseState ) { echo esc_attr( $slate_pro_license['licenseKey'] ); } ?>" <?php if ( 'active' == $licenseState ) {?> readonly="readonly"<?php } ?>></label>
						<div class="slate__licenseStatus">
							<?php if ( 'success' == $licenseReply ) { ?>
							<span class="slate__licenseSuccess"><?php _e( 'Your License Key was successfully activated!', 'slate-pro' ); ?></span>
							<?php	} else if ( 'current' == $licenseReply ) { ?>
							<span class="slate__licenseSuccess"><?php _e( 'Awesome, your license is valid and active!', 'slate-pro' ); ?></span>
							<?php	} else if ( 'invalid' == $licenseReply ) { ?>
							<span class="slate__licenseInvalid"><?php _e( 'Shoot, it looks like your License Key isn’t valid.', 'slate-pro' ); ?></span>
							<?php	} else if ( 'used' == $licenseReply ) { ?>
							<span class="slate__licenseActive"><?php _e( 'Looks like this key is already activated. It needs to be deactivated before you can use it.', 'slate-pro' ); ?></span>
							<?php	} else if ( 'removed' == $licenseReply ) { ?>
							<span class="slate__licenseRemoved"><?php _e( 'Your license key was successfully removed.', 'slate-pro' ); ?></span>
							<?php	} else if ( 'failed' == $licenseReply ) { ?>
							<span class="slate__licenseFailed"><?php _e( 'The license key you’re deactivating doesn’t match the website its activated on. Please deactivate from the proper website.', 'slate-pro' ); ?></span>
							<?php	} else if ( 'server' == $licenseReply ) { ?>
							<span class="slate__licenseServer"><?php _e( 'Oops, we couldn’t connect to the licensing server.', 'slate-pro' ); ?></span>
							<?php } ?>
						</div>
					</li>
					<?php if ( 'active' !== $licenseState ) { ?>
					<li>
						<input type="submit" name="licenseKeyActivate" id="licenseKeyActivate" class="button button-primary" value="Activate License Key">
					</li>
					<?php } else { ?>
					<li>
						<input type="submit" name="licenseKeyDeactivate" id="licenseKeyDeactivate" class="button" value="Deactivate License Key">
					</li>
					<?php } ?>
				</ul>
			</section>

		</form>

	</div>

</div>

<?php
// Setup each section of colors
function colorSection( $theSection, $colorCustom ) { ?>
<?php foreach ( $theSection as $section => $names ) { ?>
<h4><?php _e( $section, 'slate-pro' ); ?></h4>
<ul>
	<?php foreach ( $names as $field => $name ) { ?>
	<?php if ( is_array( $name ) ) { ?>
	<li><h5><?php _e( $field, 'slate-pro' ); ?></h5></li>
	<?php foreach ( $name as $field => $name ) { ?>
	<li>
		<label class="colorpickerToggle">
			<input type="text" class="slate__colorpicker" value="<?php echo slate_pro_sanitize_hex( $colorCustom[$field] ) ?>">
			<?php _e( $name, 'slate-pro' ); ?>
		</label>
		<input class="customColorsInput" type="hidden" name="slate_pro_settings[colorSchemeCustomColors][<?php echo $field;?>]" value="<?php echo slate_pro_sanitize_hex( $colorCustom[$field] ) ?>">
	</li>
	<?php } ?>
	<?php } else { ?>
	<li>
		<label class="colorpickerToggle">
			<input type="text" class="slate__colorpicker" value="<?php echo slate_pro_sanitize_hex( $colorCustom[$field] ) ?>">
			<?php _e( $name, 'slate-pro' ); ?>
		</label>
		<input class="customColorsInput" type="hidden" name="slate_pro_settings[colorSchemeCustomColors][<?php echo $field;?>]" value="<?php echo slate_pro_sanitize_hex( $colorCustom[$field] ) ?>">
	</li>
	<?php } ?>
	<?php } ?>
</ul>
<?php }
}