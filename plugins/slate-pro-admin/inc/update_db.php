<?php
function slate_pro_update_db() {

	set_time_limit( 0 );

	// This sets the original db version. Multisite was introduced later, so it starts at 4.
	if ( is_multisite() && is_main_site() ) {
		if ( ! get_site_option( 'slate_pro_db' ) ) {
			$current_db_ver = '4';
		} else {
			$current_db_ver = get_site_option( 'slate_pro_db' );
		}
	} else {
		if ( ! get_option( 'slate_pro_db' ) ) {
			$current_db_ver = '1';
		} else {
			$current_db_ver = get_option( 'slate_pro_db' );
		}
	}

	$target_db_ver = SLATE_PRO_DB;

	while ( $current_db_ver < $target_db_ver ) {

		$current_db_ver ++;

		$function = "slate_pro_update_{$current_db_ver}";
		if ( function_exists( $function ) ) {
			call_user_func( $function );
		}

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_db', $current_db_ver );
		} else {
			update_option( 'slate_pro_db', $current_db_ver );
		}
	}

}

function slate_pro_update_2() {
	global $slate_pro_settings;
	if ( is_array( $slate_pro_settings ) ) {
		date_default_timezone_set( 'America/Los_Angeles' );
		$date = date( 'Y-m-d H:i:s' );

		$slate_pro_settings['licenseDate'] = $date;
		$slate_pro_settings['contentHideScreenOptions'] = '';

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_settings', $slate_pro_settings );
		} else {
			update_option( 'slate_pro_settings', $slate_pro_settings );

		}
	}
}

function slate_pro_update_3() {
	global $slate_pro_settings;
	if ( is_array( $slate_pro_settings ) ) {

		$slate_pro_settings['loginLinkTitle'] = '';
		$slate_pro_settings['loginLinkUrl'] = '';

		if ( isset( $slate_pro_settings['adminMenu'] ) ) {
			foreach ( $slate_pro_settings['adminMenu'] as $menuOrder => $menuItem ) {
				foreach ( $menuItem as $menuTitle => $menuSlugArray ) {
					foreach ( $menuSlugArray as $menuSlug => $menuHide ) {
						if ( '0' !== $menuHide ) {
							$theMenuItem = base64_encode( serialize( array(
								'Sort'  => esc_attr( $menuOrder ),
								'Title' => esc_attr( $menuTitle ),
								'Slug'  => esc_attr( $menuSlug )
							) ) );
							$theMenu[ $theMenuItem ] = 'on';
						}
					}
				}
			}
			$slate_pro_settings['adminMenu'] = '';
			if ( isset( $theMenu ) ) {
				$slate_pro_settings['adminMenu'] = $theMenu;
			}
		}

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_settings', $slate_pro_settings );
		} else {
			update_option( 'slate_pro_settings', $slate_pro_settings );
		}
	}
}

function slate_pro_update_4() {
	global $slate_pro_settings;
	if ( is_array( $slate_pro_settings ) ) {

		$slate_pro_settings['loginLogoHide'] = '';

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_settings', $slate_pro_settings );
		} else {
			update_option( 'slate_pro_settings', $slate_pro_settings );
		}
	}
}

function slate_pro_update_5() {
	global $slate_pro_settings;
	if ( is_array( $slate_pro_settings ) ) {

		$slate_pro_settings['noticeHideAllUpdates'] = '';

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_settings', $slate_pro_settings );
		} else {
			update_option( 'slate_pro_settings', $slate_pro_settings );
		}
	}
}

function slate_pro_update_6() {
	global $slate_pro_settings;
	if ( is_array( $slate_pro_settings ) ) {

		$slate_pro_settings['customLogin'] = '';
		$slate_pro_settings['customLoginURL'] = '';

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_settings', $slate_pro_settings );
		} else {
			update_option( 'slate_pro_settings', $slate_pro_settings );
		}
	}
}

function slate_pro_update_7() {
	global $slate_pro_settings;
	if ( is_array( $slate_pro_settings ) ) {

		$slate_pro_settings['contentHideWPTitle'] = '';

		if ( is_multisite() && is_main_site() ) {
			update_site_option( 'slate_pro_settings', $slate_pro_settings );
		} else {
			update_option( 'slate_pro_settings', $slate_pro_settings );
		}
	}
}

function slate_pro_update_8() {
	if ( is_multisite() && is_main_site() ) {
		add_site_option( 'slate_pro_version', '1.1' );
	} else {
		add_option( 'slate_pro_version', '1.1' );
	}
}