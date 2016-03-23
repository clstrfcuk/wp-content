<?php

global $slate_pro_settings;

// Login Page Variables
$loginLinkTitle = ( $slate_pro_settings['loginLinkTitle'] ) ? $slate_pro_settings['loginLinkTitle'] : '';
$loginLinkUrl = ( $slate_pro_settings['loginLinkUrl'] ) ? $slate_pro_settings['loginLinkUrl'] : '';
$loginLogo = ( $slate_pro_settings['loginLogo'] ) ? '<img src="' . esc_url( $slate_pro_settings['loginLogo'] ) . '" />' : '';
$loginLogoDelete = ( $slate_pro_settings['loginLogo'] ) ? '' : 'style="display: none;"';
$loginBgImage = ( $slate_pro_settings['loginBgImage'] ) ? '<img src="' . esc_url( $slate_pro_settings['loginBgImage'] ) . '" />' : '';
$loginBgImageDelete = ( $slate_pro_settings['loginBgImage'] ) ? '' : 'style="display: none;"';
$loginBgPosition = $slate_pro_settings['loginBgPosition'];
$loginBgRepeat = $slate_pro_settings['loginBgRepeat'];

// Admin Branding Variables
$adminLogo = ( $slate_pro_settings['adminLogo'] ) ? '<img src="' . esc_url( $slate_pro_settings['adminLogo'] ) . '" />' : '';
$adminLogoFolded = ( $slate_pro_settings['adminLogoFolded'] ) ? '<img src="' . esc_url( $slate_pro_settings['adminLogoFolded'] ) . '" />' : '';
$adminFavicon = ( $slate_pro_settings['adminFavicon'] ) ? '<img src="' . esc_url( $slate_pro_settings['adminFavicon'] ) . '" />' : '';
$adminLogoDelete = ( $slate_pro_settings['adminLogo'] ) ? '' : 'style="display: none;"';
$adminLogoFoldedDelete = ( $slate_pro_settings['adminLogoFolded'] ) ? '' : 'style="display: none;"';
$adminFaviconDelete = ( $slate_pro_settings['adminFavicon'] ) ? '' : 'style="display: none;"';

// Dashboard
$dashboardCustomWidgetTitle = ( $slate_pro_settings['dashboardCustomWidgetTitle'] ) ? $slate_pro_settings['dashboardCustomWidgetTitle'] : '';
$dashboardCustomWidgetText = ( $slate_pro_settings['dashboardCustomWidgetText'] ) ? $slate_pro_settings['dashboardCustomWidgetText'] : '';

// Footer Settings Variables
$footerText = ( $slate_pro_settings['footerText'] ) ? $slate_pro_settings['footerText'] : '';

// Settings
$customLoginURL = ( $slate_pro_settings['customLoginURL'] ) ? $slate_pro_settings['customLoginURL'] : '';