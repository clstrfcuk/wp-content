<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}

$support_icon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="38px" height="36px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M11,48C5.477,48,1,43.523,1,38s4.477-10,10-10h2v20
	H11z"></path><path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M53,28c5.523,0,10,4.477,10,10s-4.477,10-10,10h-2
	V28H53z"></path><path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M13,31v-9c0,0,0-16,19-16s19,16,19,16v6"></path><polyline fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" points="51,48 51,53 36,59 28,59 28,55 36,55 
	36,58 "></polyline></svg>';

$update_icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="38px" height="38px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
<path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M24,32c0,4.418,3.582,9,8,9h4"/>
<path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8
	c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41"/>
<polyline fill="none" stroke="#777777" stroke-width="2" stroke-linejoin="bevel" stroke-miterlimit="10" points="33,45 36,41 
	33,37 "/>
<path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M42,32c0-4.418-3.582-9-8-9h-4"/>
<polyline fill="none" stroke="#777777" stroke-width="2" stroke-linejoin="bevel" stroke-miterlimit="10" points="33,19 30,23 
	33,27 "/>
</svg>';

$heart_icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="38px" height="34px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
<path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M1,21c0,20,31,38,31,38s31-18,31-38
	c0-8.285-6-16-15-16c-8.285,0-16,5.715-16,14c0-8.285-7.715-14-16-14C7,5,1,12.715,1,21z"/>
</svg>';

$card_icon ='<svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="38px" height="38px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
<rect x="1" y="11" fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" width="62" height="42"/>
<line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="1" y1="17" x2="63" y2="17"/>
<line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="1" y1="25" x2="63" y2="25"/>
<line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="6" y1="47" x2="10" y2="47"/>
<line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="12" y1="47" x2="41" y2="47"/>
</svg>';

$ticket_icon = '<svg style="transform: scaleX(-1)" version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="38px" height="38px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><g><polygon fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" points="25,1 63,39 39,63 1,25 1,1"/><circle fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" cx="17" cy="17" r="6"/></g></svg>';
	 
$info_icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="38px" height="38px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M53.92,10.081c12.107,12.105,12.107,31.732,0,43.838 c-12.106,12.108-31.734,12.108-43.84,0c-12.107-12.105-12.107-31.732,0-43.838C22.186-2.027,41.813-2.027,53.92,10.081z"/><line stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="32" y1="47" x2="32" y2="25"/><line stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="32" y1="21" x2="32" y2="17"/></svg>';

$doc_icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="38px" height="34px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><g><polygon fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" points="23,1 55,1 55,63 9,63 9,15 	"/><polyline fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" points="9,15 23,15 23,1"/><line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="32" y1="14" x2="46" y2="14"/><line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="18" y1="24" x2="46" y2="24"/><line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="18" y1="34" x2="46" y2="34"/><line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="18" y1="44" x2="46" y2="44"/><line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="18" y1="54" x2="46" y2="54"/></g></svg>';

$info_icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="38px" height="38px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><polyline fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" points="5,41 11,1 53,1 59,41 "/><path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M21,41c0,6.075,4.925,11,11,11s11-4.925,11-11h16v22 H5V41H21z"/><polyline fill="none" stroke="#777777" stroke-width="2" stroke-linejoin="bevel" stroke-miterlimit="10" points="23,22 30,29 43,16 "/></svg>';

$info_icon2 = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="38px" height="38px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><polyline fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" points="5,41 11,1 53,1 59,41 "/><path fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" d="M21,41c0,6.075,4.925,11,11,11s11-4.925,11-11h16v22 H5V41H21z"/><polyline fill="none" stroke="#777777" stroke-width="2" stroke-linejoin="bevel" stroke-miterlimit="10" points="40,25 32,33 24,25 "/><g><line fill="none" stroke="#777777" stroke-width="2" stroke-miterlimit="10" x1="32" y1="33" x2="32" y2="13"/></g></svg>';




$update = '<div class="tg-row">';
	
	$update .= '<div class="tomb-spacer" style="height: 30px"></div>';
	
	$plugin_info = get_option('the_grid_plugin_info', '');
	$envato_api_token = get_option('the_grid_envato_api_token', '');
	$current_version = TG_VERSION;
	$last_version = (isset($plugin_info['version'])) ? $plugin_info['version'] : __( 'You must be registered to know available version', 'tg-text-domain' );
	$updated_at = (isset($plugin_info['updated_at'])) ? ' ('.date('m/d/Y',strtotime($plugin_info['updated_at'])).')' : null;
	$license = (isset($plugin_info['license'])) ? $plugin_info['license'] : null;
	$purchase_code = (isset($plugin_info['purchase_code'])) ? $plugin_info['purchase_code'] : null;

	$supported_until = (isset($plugin_info['supported_until'])) ? $plugin_info['supported_until'] : null;
	if ($supported_until) {
		$date= strtotime($supported_until);
		$diff  = $date-time();
		$supported_until = floor($diff/(60*60*24));
	}

	$update .= '<div class="tg-col tg-col-3">';
		$update .= '<div class="tg-container">';
			$update .= '<div class="tg-container-header">';
				$update .= '<div class="tg-container-title">'. __( 'Plugin Activation', 'tg-text-domain' ) .'</div>';
				$update .= ($purchase_code) ? '<div class="tg-button tg-container-button tg-button-active"><i class="dashicons dashicons-yes"></i>'. __( 'Plugin Activated', 'tg-text-domain' ) .'</div>' : null;
				$update .= (!$purchase_code) ? '<div class="tg-button tg-container-button tg-button-disable"><i class="dashicons dashicons-warning"></i>'. __( 'Not Activated', 'tg-text-domain' ) .'</div>' : null;
			$update .= '</div>';
			
			
			if ($purchase_code) {
			
				$update .= '<div class="tg-container-inner tg-container-register">';
					$update .= '<div class="tg-text-icon">';
						$update .= $card_icon;
						$update .= '<div class="tg-text-icon-title">'. __( 'Purchase Code', 'tg-text-domain' ) .' ('.$license.')</div>';
						$update .= '<div class="tg-purchase-code">'.$purchase_code.'</div>';
					$update .= '</div>';
					if ($supported_until > 0) {
						$update .= '<div class="tg-text-icon">';
							$update .= $ticket_icon;
							$update .= '<div class="tg-text-icon-title">'. __( 'Premium Ticket Support', 'tg-text-domain' ) .' ('.$supported_until.' '.__( 'days left', 'tg-text-domain' ) .')</div>';
							$update .= '<div class="tg-text-icon-desc">'. __( 'Direct help from our qualified support team', 'tg-text-domain' ) .'</div>';
							$update .= '<a class="tg-button" target="_blank" href="https://themeoneticket.ticksy.com/">'.__( 'Open a ticket', 'tg-text-domain' ) .'</a>';
						$update .= '</div>';
					} else {
						$update .= '<div class="tg-text-icon">';
							$update .= $ticket_icon;
							$update .= '<div class="tg-text-icon-title">'. __( 'Premium Ticket Support (Expired)', 'tg-text-domain' ) .'</div>';
							$update .= '<div class="tg-text-icon-desc">'. __( 'Direct help from our qualified support team', 'tg-text-domain' ) .'</div>';
							$update .= '<a class="tg-button" target="_blank" href="http://codecanyon.net/item/the-grid-responsive-grid-builder-for-wordpress/13306812">'.__( 'Extend support', 'tg-text-domain' ) .'</a>';
						$update .= '</div>';
					}
					$update .= '<div class="tomb-spacer" style="height: 14px"></div>';
					$update .= '<div><span class="tg-button tg-button-register">'. __( 'Change your personal Token', 'tg-text-domain' ) .'</span></div>';
				$update .= '</div>';
			
			} else {
				
				$update .= '<div class="tg-container-inner tg-container-register">';
					$update .= '<div class="tg-text-icon">';
						$update .= $update_icon;
						$update .= '<div class="tg-text-icon-title">'. __( 'Live Updates', 'tg-text-domain' ) .'</div>';
						$update .= '<div class="tg-text-icon-desc">'. __( 'Fresh versions directly to your admin', 'tg-text-domain' ) .'</div>';
					$update .= '</div>';
					$update .= '<div class="tg-text-icon">';
						$update .= $support_icon;
						$update .= '<div class="tg-text-icon-title">'. __( 'Premium Ticket Support', 'tg-text-domain' ) .'</div>';
						$update .= '<div class="tg-text-icon-desc">'. __( 'Direct help from our qualified support team', 'tg-text-domain' ) .'</div>';
					$update .= '</div>';
					$update .= '<div class="tg-text-icon">';
						$update .= $doc_icon;
						$update .= '<div class="tg-text-icon-title">'. __( 'Online Documentation', 'tg-text-domain' ) .'</div>';
						$update .= '<div class="tg-text-icon-desc">'. __( 'The best start for The Grid beginners', 'tg-text-domain' ) .'</div>';
					$update .= '</div>';
					$update .= '<div class="tomb-spacer" style="height: 15px"></div>';
					$update .= '<div><span class="tg-button tg-button-register">'. __( 'Register The Grid', 'tg-text-domain' ) .'</span></div>';
				$update .= '</div>';

			}

			$update .= '<div class="tg-container-inner tg-container-register-token">';
				$update .= '<div class="tg-text-icon-title">'. __( 'Global OAuth Personal Token', 'tg-text-domain' ) .'</div>';
				$update .= '<p>'. __( 'OAuth is a protocol that lets external apps request authorization to private details in a user\'s Envato Market account without entering their password.', 'tg-text-domain' ) .'</p>';
				$update .= '<input name="the_grid_envato_api_token" type="text" class="tomb-text" style="width:320px" value="'.$envato_api_token.'" placeholder="'. __( 'Enter your Envato API Personal Token', 'tg-text-domain' ) .'">';
				$update .= '<p><em>'. __( 'You will need to', 'tg-text-domain' ) .' <a target="_blank" href="https://build.envato.com/create-token/?purchase:download=t&purchase:verify=t&purchase:list=t">'.  __( 'generate a personal token', 'tg-text-domain' ) .'</a>, '.  __( 'and then insert it above.', 'tg-text-domain' ) .'</em></p>';
				$update .= '<div class="tomb-spacer" style="height: 10px"></div>';
				$update .= '<div><span id="tg-grid-save-envato-api-token" class="tg-button tg-button-register-token">'. __( 'Save Changes', 'tg-text-domain' ) .'</span><div class="spinner"></div><strong></strong></span></div>';
			$update .= '</div>';
			
		$update .= '</div>';
	$update .= '</div>';
	
	$update .= '<div class="tg-col tg-col-3">';
		$update .= '<div class="tg-container">';
			$update .= '<div class="tg-container-header">';
				$update .= '<div class="tg-container-title">'. __( 'Plugin Updates', 'tg-text-domain' ) .'</div>';
				$update .= (version_compare($last_version, $current_version) <=  0 && version_compare( $last_version, '0.0.1', '>=' )) ? '<div class="tg-button tg-container-button tg-button-active"><i class="dashicons dashicons-yes"></i>'. __( 'Plugin up to date', 'tg-text-domain' ) .'</div>' : null;
				$update .= (version_compare($last_version, $current_version) >  0) ? '<div class="tg-button tg-container-button tg-button-disable"><i class="dashicons dashicons-update"></i>'. __( 'Update Available', 'tg-text-domain' ) .'</div>' : null;
			$update .= '</div>';
			$update .= '<div class="tg-container-inner">';
				$update .= '<div class="tg-text-icon">';
					$update .= $info_icon;
					$update .= '<div class="tg-text-icon-title">'. __( 'Installed Version', 'tg-text-domain' ) .'</div>';
					$update .= '<div class="tg-text-icon-desc">v'. $current_version .'</div>';
				$update .= '</div>';
				$update .= '<div class="tg-text-icon">';
					$update .= $info_icon2;
					$update .= '<div class="tg-text-icon-title">'. __( 'Last Available Version', 'tg-text-domain' ) .'</div>';
					$version = (version_compare( $last_version, '0.0.1', '>=' )) ? 'v'.$last_version : $last_version;
					$update .= '<div class="tg-text-icon-desc">'. $version .'</div>';
				$update .= '</div>';
				$update .= '<div class="tomb-spacer" style="height: 64px"></div>';
				if (version_compare( $last_version, '0.0.1', '<' ))  {
					$update .= '<div><span class="tg-button tg-button-live-no-update">'. __( 'Register to Access Update', 'tg-text-domain' ) .'</span></div>';
				} else if ((version_compare($last_version, $current_version) >  0) && current_user_can('update_plugins')) {
					
					// plugin slug
					$name = 'The Grid';
					$slug = 'the-grid/the-grid.php';
					// Upgrade link.
					$upgrade_link = add_query_arg( array(
						'action' => 'upgrade-plugin',
						'plugin' => $slug,
					), self_admin_url( 'update.php' ) );
					// update link
					$update .= sprintf(
						'<a class="update-now tg-button tg-button-live-update" href="%1$s" aria-label="%2$s" data-name="%3$s %6$s" data-plugin="%4$s" data-slug="%5$s" data-version="%6$s">%7$s</a>',
						wp_nonce_url( $upgrade_link, 'upgrade-plugin_' . $slug ),
						esc_attr__( 'Update %s now', 'envato-market' ),
						esc_attr( $name ),
						esc_attr( $slug ),
						sanitize_key( dirname( $slug ) ),
						esc_attr( $last_version ),
						esc_html__( 'Update Now', 'envato-market' )
					);
					$update .= '</span><div class="spinner"></div><strong></strong>';
				} else if ((version_compare($last_version, $current_version) ==  0)) {
					$update .= '<div><span class="tg-button tg-button-live-update" id="tg-check-update">'. __( 'Check for updates', 'tg-text-domain' ) .'</span><div class="spinner"></div><strong></strong></div>';
				}
			$update .= '</div>';
		$update .= '</div>';
	$update .= '</div>';
	
	// icons php info
	$true  = '<i class="tg-php-info-icon dashicons dashicons-yes"></i>';
	$false = '<i class="tg-php-info-icon dashicons dashicons-no"></i>';
	$recommended = '<i class="tg-php-info-icon dashicons dashicons-no tg-recommended-icon"></i>';
	// Wordpress version 
	global $wp_version;
	$wp_version_bool = (version_compare($wp_version,  '4.4.0') >=  0) ? true : false;
	$wp_version_icon = ($wp_version_bool) ? $true : $false;
	// Visual Composer version 
	$vc_version = (defined('WPB_VC_VERSION')) ? WPB_VC_VERSION : null;
	$vc_version_bool = (class_exists('Vc_Manager') && version_compare($vc_version,  '4.7.4') >=  0) ? true : false;
	$vc_version_icon = ($vc_version_bool) ? $true : $false;
	// php memory limit
	$mem_limit = ini_get('memory_limit');
	$mem_limit_bool = ((int) $mem_limit >= 64)  ? true : false;
	$mem_limit_icon = ($mem_limit_bool) ? $true : $recommended;
	// php max upload file size
	$upload_max_filesize = ini_get('upload_max_filesize');
	$upload_max_filesize_bool = ((int) $upload_max_filesize >= 64) ? true : false;
	$upload_max_filesize_icon = ($upload_max_filesize_bool) ? $true : $recommended;
	// php mmax post size
	$post_max_size = ini_get('post_max_size');
	$post_max_size_bool = ((int) $post_max_size >= 64) ? true : false;
	$post_max_size_icon = ($post_max_size_bool) ? $true : $recommended;
	// php version
	$php_version = PHP_VERSION;
	$php_version_bool = (version_compare($php_version,  '5.3.0') >=  0) ? true : false;
	$php_version_icon = ($php_version_bool) ? $true : $false;
	
	if (!$wp_version_bool || !$php_version_bool || (!$vc_version_bool && class_exists('Vc_Manager'))) {
		$system_state = '<div class="tg-button tg-container-button tg-button-disable"><i class="dashicons dashicons-warning"></i>'. __( 'Problem Found', 'tg-text-domain' ) .'</div>';
	} else if (!$mem_limit_bool || !$upload_max_filesize_bool || !$post_max_size_bool) {
		$system_state = '<div class="tg-button tg-container-button tg-button-recommended"><i class="dashicons dashicons-warning"></i>'. __( 'No Problems', 'tg-text-domain' ) .'</div>';
	} else {
		$system_state = '<div class="tg-button tg-container-button tg-button-active"><i class="dashicons dashicons-yes"></i>'. __( 'No Problems', 'tg-text-domain' ) .'</div>';
	}
	
	$msg_reco = '<span class="tg-info-recommended"> ('.__( 'recommended', 'tg-text-domain' ).')</span>';
	$msg_mand = '<span class="tg-info-needed"> ('.__( 'required', 'tg-text-domain' ).')</span>';
	
	$update .= '<div class="tg-col tg-col-3">';
		$update .= '<div class="tg-container">';
			$update .= '<div class="tg-container-header">';
				$update .= '<div class="tg-container-title">'. __( 'System Requirements', 'tg-text-domain' ) .'</div>';
				$update .= $system_state;
			$update .= '</div>';
			$update .= '<div class="tg-container-inner">';
				$update .= $wp_version_icon.'<div class="tg-php-info">'. __( 'Wordpress Version', 'tg-text-domain' ) .'</div><span class="tg-php-info-value">v'. $wp_version .'</span>';
				$update .= (!$wp_version_bool) ?'<span class="tg-php-info-value-needed">v4.4.X</span>'.$msg_mand : '';
				$update .= '<div class="tomb-spacer" style="height: 7px"></div>';
				$update .= (class_exists('Vc_Manager')) ? $vc_version_icon.'<div class="tg-php-info">'. __( 'Visual Composer Version', 'tg-text-domain' ) .'</div><span class="tg-php-info-value">v'. $vc_version .'</span>' : '';
				$update .= (class_exists('Vc_Manager') && !$vc_version_bool) ? '<span class="tg-php-info-value-needed">v4.7.4</span>'.$msg_mand : '';
				$update .= (class_exists('Vc_Manager')) ? '<div class="tomb-spacer" style="height: 7px"></div>' : '';
				$update .= $mem_limit_icon.'<div class="tg-php-info">'. __( 'Memory Limit', 'tg-text-domain' ) .'</div><span class="tg-php-info-value">'. $mem_limit .'</span>';
				$update .= (!$mem_limit_bool) ?'<span class="tg-php-info-value-recommended">64M</span>'.$msg_reco : '';
				$update .= '<div class="tomb-spacer" style="height: 7px"></div>';
				$update .= $upload_max_filesize_icon.'<div class="tg-php-info">'. __( 'Upload Max. Filesize', 'tg-text-domain' ) .'</div><span class="tg-php-info-value">'. $upload_max_filesize .'</span>';
				$update .= (!$upload_max_filesize_bool) ?'<span class="tg-php-info-value-recommended">64M</span>'.$msg_reco : '';
				$update .= '<div class="tomb-spacer" style="height: 7px"></div>';
				$update .= $post_max_size_icon.'<div class="tg-php-info">'. __( 'Max. Post Size', 'tg-text-domain' ) .'</div><span class="tg-php-info-value">'. $post_max_size .'</span>';
				$update .= (!$post_max_size_bool) ?'<span class="tg-php-info-value-recommended">64M</span>'.$msg_reco : '';
				$update .= '<div class="tomb-spacer" style="height: 7px"></div>';
				$update .= $php_version_icon.'<div class="tg-php-info">'. __( 'PHP version', 'tg-text-domain' ) .'</div><span class="tg-php-info-value">v'. $php_version .'</span>';
				$update .= (!$php_version_bool) ?'<span class="tg-php-info-value-needed">v5.4.0</span>'.$msg_mand : '';
			$update .= '</div>';
		$update .= '</div>';
	$update .= '</div>';

$update .= '</div>';

echo $update;


