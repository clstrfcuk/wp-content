<?php
include( __DIR__ . '/../inc/colors.php' );

// Check to see if the user selected an admin color in their profile.
if ( 'fresh' == slate_pro_get_user_admin_color() || '' == slate_pro_get_user_admin_color() ) {
	if ( 'default' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorDefault;
	} else if ( 'light' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorLight;
	} else if ( 'blue' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorBlue;
	} else if ( 'coffee' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorCoffee;
	} else if ( 'ectoplasm' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorEctoplasm;
	} else if ( 'midnight' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorMidnight;
	} else if ( 'ocean' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorOcean;
	} else if ( 'sunrise' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorSunrise;
	} else if ( 'custom' == $slate_pro_settings['colorScheme'] ) {
		$colorSelected = $colorCustom;
	} else {
		$colorSelected = $colorDefault;
	}
} else if ( 'light' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorLight;
} else if ( 'blue' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorBlue;
} else if ( 'coffee' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorCoffee;
} else if ( 'ectoplasm' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorEctoplasm;
} else if ( 'midnight' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorMidnight;
} else if ( 'ocean' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorOcean;
} else if ( 'sunrise' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorSunrise;
} else if ( 'custom' == slate_pro_get_user_admin_color() ) {
	$colorSelected = $colorCustom;
} else {
	$colorSelected = $colorDefault;
}
?>

<style type="text/css" media="screen">

	/* *********************** */
	/* Slate Pro */
	/* *********************** */
	#slate__colorSchemes .premadeColors label {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?>;
	}
	#slate__colorSchemes .premadeColors label.selected,
	#slate__colorSchemes .premadeColors label:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?>;
	}


	/* *********************** */
	/* Login Page */
	/* *********************** */
	body.login {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginBgColor'] ); ?>;
	}
	#loginform {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormBgColor'] ); ?>;
	}
	.login h1 a {
		<?php if ( $colorDefault == $colorSelected || $colorCoffee == $colorSelected || $colorEctoplasm == $colorSelected || $colorMidnight == $colorSelected || $colorSunrise == $colorSelected ) { ?>
			background-image: url(<?php echo admin_url(); ?>/images/w-logo-white.png?ver=20131202);
			background-image: none,url(<?php echo admin_url(); ?>/images/wordpress-logo-white.svg?ver=20131107);
		<?php	} ?>
	}
	#loginform label {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormTextColor'] ); ?>;
	}
	#loginform input {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormInputBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormInputTextColor'] ); ?>;
	}
	#loginform input:focus {
		border: 1px solid <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormInputFocusColor'] ); ?>;
	}
	#loginform input[type="checkbox"]:checked::before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormInputTextColor'] ); ?>;
	}
	.login #backtoblog a, 
	.login #nav a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormLinkColor'] ); ?>;
	}
	.login #backtoblog a:hover, 
	.login #nav a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginFormLinkHoverColor'] ); ?>;
	}
	#loginform .button-primary {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginButtonBgColor'] ); ?>;
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginButtonBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginButtonTextColor'] ); ?>;
	}
	#loginform .button-primary:hover {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginButtonHoverBgColor'] ); ?>;
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginButtonHoverBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['loginButtonHoverTextColor'] ); ?>;
	}

	/* *********************** */
	/* Admin Menu */
	/* *********************** */
	/* Background */
	#adminmenuback {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminMenuBgColor'] ); ?>;
		<?php if ('on' == $slate_pro_settings['colorsHideShadows']) { ?>
			background-image: none;
		<?php	} ?>
	}
	@media only screen and (max-width: 782px) {
		#adminmenuwrap {
			background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminMenuBgColor'] ); ?>;
		}
	}
	/* Divider Line */
	#adminmenu li.wp-menu-separator,
	#adminmenu #collapse-menu {
		border-top-color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminMenuDividerColor'] ); ?>;
	}
	/* Top Level Menu Color */
	#adminmenu a,
	#adminmenu div.wp-menu-image:before,
	#collapse-menu, 
	#collapse-menu #collapse-button div:after {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelTextColor'] ); ?>;
	}
	/* Top Level Menu Hover */
	#adminmenu .wp-submenu a:focus, 
	#adminmenu .wp-submenu a:hover, 
	#adminmenu a:hover, 
	#adminmenu li.menu-top>a:focus,
	#adminmenu li.opensub>a.menu-top,
	#adminmenu li:hover div.wp-menu-image:before,
	#adminmenu li.opensub a,
	#collapse-menu:hover, 
	#collapse-menu:hover #collapse-button div:after,
	#adminmenu li a:focus div.wp-menu-image:before,
	#adminmenu li.opensub div.wp-menu-image:before,
	#adminmenu li:hover div.wp-menu-image:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelTextHoverColor'] ); ?>;
	}
	/* Selected Top Level Menu Color */
	#adminmenu li.current a.menu-top,
	#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
	#adminmenu .current div.wp-menu-image:before, 
	#adminmenu .wp-has-current-submenu div.wp-menu-image:before, 
	#adminmenu a.current:hover div.wp-menu-image:before, 
	#adminmenu a.wp-has-current-submenu:hover div.wp-menu-image:before, 
	#adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedTextColor'] ); ?>;
	}
	/* Folded Top Menu Text Color */
	#adminmenu .wp-submenu .wp-submenu-head,
	.folded #adminmenu .wp-submenu .wp-submenu-head {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelFoldedTextColor'] ); ?>;
	}
	#adminmenu .wp-has-current-submenu.opensub .wp-submenu .wp-submenu-head,
	.folded #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedTextColor'] ); ?>;
	}
	/* Folded Top Menu and Submenu Background Color */
	.folded #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedBg'] ); ?>;
	}
	@media only screen and (max-width: 960px) {
		.auto-fold #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
		#adminmenu .wp-has-current-submenu.opensub .wp-submenu {
			background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedBg'] ); ?>;
		}
	}

	/* Folded Top Menu Icon Color */
	.folded #adminmenu .current div.wp-menu-image:before, 
	.folded #adminmenu .wp-has-current-submenu div.wp-menu-image:before, 
	.folded #adminmenu a.current:hover div.wp-menu-image:before, 
	.folded #adminmenu a.wp-has-current-submenu:hover div.wp-menu-image:before, 
	.folded #adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedIconColor'] ); ?>;
	}
	@media only screen and (max-width: 960px) {
		.auto-fold #adminmenu .current div.wp-menu-image:before, 
		.auto-fold #adminmenu .wp-has-current-submenu div.wp-menu-image:before, 
		.auto-fold #adminmenu a.current:hover div.wp-menu-image:before, 
		.auto-fold #adminmenu a.wp-has-current-submenu:hover div.wp-menu-image:before, 
		.auto-fold #adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedIconColor'] ); ?>;
		}
	}
	@media only screen and (max-width: 782px) {
		.auto-fold #adminmenu .current div.wp-menu-image:before, 
		.auto-fold #adminmenu .wp-has-current-submenu div.wp-menu-image:before, 
		.auto-fold #adminmenu a.current:hover div.wp-menu-image:before, 
		.auto-fold #adminmenu a.wp-has-current-submenu:hover div.wp-menu-image:before, 
		.auto-fold #adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedTextColor'] ); ?>;
		}
	}
	/* Open Submenu Color */
	#adminmenu .wp-submenu a,
	#adminmenu .wp-has-current-submenu .wp-submenu a,
	#adminmenu .wp-has-current-submenu.opensub .wp-submenu a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminOpenSubmenuTextColor'] ); ?>;
	}
	/* Open Submenu Hover Color */
	#adminmenu .wp-submenu a:hover,
	#adminmenu .wp-has-current-submenu .wp-submenu a:hover,
	#adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminOpenSubmenuTextHoverColor'] ); ?>;
	}
	/* Open Selected Submenu Color */
	#adminmenu .opensub .wp-submenu li.current a, 
	#adminmenu .wp-submenu li.current, 
	#adminmenu .wp-submenu li.current a, 
	#adminmenu .wp-submenu li.current a:focus, 
	#adminmenu .wp-submenu li.current a:hover, 
	#adminmenu a.wp-has-current-submenu:focus+.wp-submenu li.current a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminOpenSubmenuTextSelectedColor'] ); ?>;
	}
	/* Floating Submenu Background */
	#adminmenu .wp-submenu {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFloatingSubmenuBgColor'] ); ?>;
	}
	/* Submenu Arrow Color */
	#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after {
		border-right-color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFloatingSubmenuBgColor'] ); ?>;
	}
	/* Floating Submenu Text Color */
	#adminmenu .opensub .wp-submenu a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFloatingSubmenuTextColor'] ); ?>;
	}
	/* Floating Submenu Text Hover Color */
	#adminmenu .opensub .wp-submenu a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFloatingSubmenuTextHoverColor'] ); ?>;
	}
	/* Folded Floating Submenu Hover Color */
	.folded #adminmenu .wp-has-current-submenu a:hover,
	.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuTextHoverColor'] ); ?>;
	}
	/* Folded Floating Submenu Text Color */
	.folded.sticky-menu #adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuTextHoverColor'] ); ?>;
		}
	@media only screen and (max-width: 960px) {
		.sticky-menu #adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuTextHoverColor'] ); ?>;
		}
	}
	/* Folded Selected Floating Unselected Submenu Color */
	.folded #adminmenu .wp-has-current-submenu a,
	.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuTextColor'] ); ?>;
	}
	.folded.sticky-menu #adminmenu .wp-has-current-submenu.opensub .wp-submenu a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuTextColor'] ); ?>;
	}
	@media only screen and (max-width: 960px) {
		.sticky-menu #adminmenu .wp-has-current-submenu.opensub .wp-submenu a {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuTextColor'] ); ?>;
		}
	}
	@media only screen and (max-width: 782px) {
		.folded #adminmenu .wp-has-current-submenu a,
		.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu a {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminOpenSubmenuTextColor'] ); ?>;
		}
		.folded #adminmenu .wp-has-current-submenu a:hover,
		.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminOpenSubmenuTextHoverColor'] ); ?>;
		}
	}
	/* Folded Selected Floating Selected Submenu Color */
	.folded #adminmenu .wp-submenu li.current, 
	.folded #adminmenu .wp-submenu li.current a,
	.folded #adminmenu .opensub .wp-submenu li.current, 
	.folded #adminmenu .opensub .wp-submenu li.current a, 
	.folded #adminmenu .opensub .wp-submenu li.current a:focus, 
	.folded #adminmenu .opensub .wp-submenu li.current a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuSelectedTextColor'] ); ?>;
	}
	@media only screen and (max-width: 960px) {
		#adminmenu .wp-submenu li.current a,
		#adminmenu .opensub .wp-submenu li.current,
		#adminmenu .opensub .wp-submenu li.current a, 
		#adminmenu .opensub .wp-submenu li.current a:focus, 
		#adminmenu .opensub .wp-submenu li.current a:hover {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminOpenSubmenuTextSelectedColor'] ); ?>;
		}
		.sticky-menu #adminmenu .wp-submenu li.current a,
		.sticky-menu #adminmenu .opensub .wp-submenu li.current,
		.sticky-menu #adminmenu .opensub .wp-submenu li.current a, 
		.sticky-menu #adminmenu .opensub .wp-submenu li.current a:focus, 
		.sticky-menu #adminmenu .opensub .wp-submenu li.current a:hover {
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminFoldedFloatingSubmenuSelectedTextColor'] ); ?>;
		}
	}
	/* Folded Floating Submenu Background and Color */
	/* Auto-collapsed */
	@media only screen and (max-width: 960px) {
		.sticky-menu #adminmenu .wp-has-current-submenu.opensub.wp-menu-open .wp-submenu,
		.sticky-menu #adminmenu .wp-menu-open.opensub .wp-submenu,
		.sticky-menu #adminmenu a.wp-has-current-submenu.wp-menu-open:focus+.wp-submenu, 
		.sticky-menu .no-js li.wp-has-current-submenu.wp-menu-open:hover .wp-submenu {
			background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedBg'] ); ?>;
		}
	}
	.folded #adminmenu .wp-has-current-submenu .wp-submenu,
	.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu,
	.folded #adminmenu a.wp-has-current-submenu:focus+.wp-submenu,
	.folded #adminmenu a.wp-has-current-submenu.opensub:focus+.wp-submenu,
	.folded #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
	.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu .wp-submenu-head, 
	.folded #adminmenu .wp-menu-arrow, 
	.folded #adminmenu .wp-menu-arrow div, 
	.folded #adminmenu li.current a.menu-top, 
	.folded #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, 
	.folded #adminmenu li.current.menu-top, 
	.folded #adminmenu li.wp-has-current-submenu {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedBg'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedTextColor'] ); ?>;
	}
	.folded #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
	.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu .wp-submenu-head {
		background: none;
	}
	@media only screen and (max-width: 782px) {
		.folded #adminmenu .wp-has-current-submenu .wp-submenu,
		.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu,
		.folded #adminmenu a.wp-has-current-submenu:focus+.wp-submenu,
		.folded #adminmenu a.wp-has-current-submenu.opensub:focus+.wp-submenu,
		.folded #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
		.folded #adminmenu .wp-has-current-submenu.opensub .wp-submenu .wp-submenu-head,
		.folded #adminmenu .wp-menu-arrow, 
		.folded #adminmenu .wp-menu-arrow div, 
		.folded #adminmenu li.current a.menu-top, 
		.folded #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, 
		.folded #adminmenu li.current.menu-top, 
		.folded #adminmenu li.wp-has-current-submenu {
			background: none;
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedTextColor'] ); ?>;
		}
	}
	/* Folded Floating Top Level Menu Background and Color */
	@media only screen and (max-width: 960px) {
		#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
		#adminmenu .wp-menu-arrow, 
		#adminmenu .wp-menu-arrow div, 
		#adminmenu li.current a.menu-top, 
		.folded #adminmenu li.current.menu-top, 
		.folded #adminmenu li.wp-has-current-submenu {
			background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedBg'] ); ?>;
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedFoldedTextColor'] ); ?>;
		}
	}
	@media only screen and (max-width: 782px) {
		#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
		#adminmenu .wp-menu-arrow, 
		#adminmenu .wp-menu-arrow div, 
		#adminmenu li.current a.menu-top,
		.wp-responsive-open #adminmenu li.current a.menu-top,
		.folded #adminmenu .wp-has-current-submenu .wp-submenu,
		.folded #adminmenu a.wp-has-current-submenu:focus+.wp-submenu,
		.folded #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
		.folded #adminmenu li.current.menu-top, 
		.folded #adminmenu li.wp-has-current-submenu {
			background: none;
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminTopLevelSelectedTextColor'] ); ?>;
		}
	}
	/* Update Notices */
	#adminmenu .awaiting-mod, 
	#adminmenu .update-plugins, 
	#sidemenu li a span.update-plugins,
	#adminmenu li a.wp-has-current-submenu .update-plugins, 
	#adminmenu li.current a .awaiting-mod {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminNoticeBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminNoticeColor'] ); ?>;
	}

	/* *********************** */
	/* Footer */
	/* *********************** */
	#wpfooter {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['footerBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['footerTextColor'] ); ?>;
	}
	#wpfooter a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['footerLinkColor'] ); ?>;
	}
	#wpfooter a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['footerLinkHoverColor'] ); ?>;
	}

	/* *********************** */
	/* Content Colors */
	/* *********************** */

	/*sidebarTextColor*/
	/* Primary Link Color */
	a,
	.view-switch a.current:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentLinkColor'] ); ?>;
	}
	a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentLinkHoverColor'] ); ?>;
	}
	/* Text Color */
	#poststuff #post-body.columns-2 #side-sortables, 
	.comment-php #submitdiv,
	#postbox-container-2,
	.howto,
	.ac_match, 
	.subsubsub a.current {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTextColor'] ); ?>;
	}
	/* Icon Colors */ 
	span.wp-media-buttons-icon:before,
	.post-format-icon:before, 
	.post-state-format:before,
	input[type=radio]:checked+label:before,
	input[type=checkbox]:checked:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTextColor'] ); ?>;
	}
	.insert-media.add_media:hover span.wp-media-buttons-icon:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonTextHoverColor'] ); ?>;
	}
	input[type=radio]:checked:before {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTextColor'] ); ?>;
	}
	/* Arrow Color */
	.accordion-section-title:after, 
	.handlediv, 
	.item-edit, 
	.sidebar-name-arrow, 
	.widget-action {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTextColor'] ); ?>;
	}
	/* Heading Color */
	.wrap h2,
	#poststuff h3,
	.welcome-panel-content h3,
	#dashboard-widgets-wrap h3,
	.widefat tfoot tr th, .widefat thead tr th,
	th.manage-column a, 
	th.sortable a:active, 
	th.sortable a:focus, 
	th.sortable a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentHeadingTextColor'] ); ?>;
	}
	/* Form Focus Color */
	input[type=checkbox]:focus, 
	input[type=color]:focus, 
	input[type=date]:focus, 
	input[type=datetime-local]:focus, 
	input[type=datetime]:focus, 
	input[type=email]:focus, 
	input[type=month]:focus, 
	input[type=number]:focus, 
	input[type=password]:focus, 
	input[type=radio]:focus, 
	input[type=search]:focus, 
	input[type=tel]:focus, 
	input[type=text]:focus, 
	input[type=time]:focus, 
	input[type=url]:focus, 
	input[type=week]:focus, 
	select:focus, 
	textarea:focus {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentLinkHoverColor'] ); ?>;
	}
	/* Table Nav */
	.tablenav .tablenav-pages a:focus, 
	.tablenav .tablenav-pages a:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentLinkHoverColor'] ); ?>;
		color: #fff;
	}

	/* Sidebar, accent, etc */
	#side-sortablesback, 
	.comment-php #submitdiv-back,
	#poststuff #post-body.columns-2 #side-sortables, 
	.comment-php #submitdiv,
	#normal-sortables .postbox,
	#dashboard-widgets-wrap #normal-sortables .postbox,
	#dashboard-widgets-wrap #side-sortables .postbox,
	#dashboard-widgets-wrap #column3-sortables .postbox, 
	#dashboard-widgets-wrap #column4-sortables .postbox, 
	#dashboard-widgets-wrap #column5-sortables .postbox,
	#contextual-help-link-wrap, 
	#screen-options-link-wrap,
	.edit-tags-php #col-left,
	#col-leftback {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarBgColor'] ); ?>;
		<?php if ('on' == $slate_pro_settings['colorsHideShadows']) { ?>
			background-image: none;
		<?php	} ?>
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarTextColor'] ); ?>;
	}
	@media only screen and (max-width: 850px) {
		.postbox {
			background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarBgColor'] ); ?>;
			<?php if ('on' == $slate_pro_settings['colorsHideShadows']) { ?>
				background-image: none;
			<?php	} ?>
			color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarTextColor'] ); ?>;
		}
	}
	/* Tables */
	.wp-list-table tr:hover,
	table.comments tr:hover,
	.edit-tags-php #col-left,
	#col-leftback {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTableRowBgHoverColor'] ); ?>;
	}
	#poststuff h3 {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarHeadingColor'] ); ?>;
	}
	/* Icon Colors */
	.postbox #misc-publishing-actions label[for=post_status]:before,
	#post-body .postbox #visibility:before, 
	#post-body .postbox .misc-pub-revisions:before,
	.postbox .curtime #timestamp:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarIconColor'] ); ?>;
	}
	.postbox .howto,
	.postbox input[type=radio]:checked:before,
	.postbox input[type=checkbox]:checked:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarTextColor'] ); ?>;
	}
	/* Links */
	.postbox a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarLinkColor'] ); ?>;
	}
	.postbox a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarLinkHoverColor'] ); ?>;
	}
	/* Inputs */
	.postbox input[type=checkbox]:focus, 
	.postbox input[type=color]:focus, 
	.postbox input[type=date]:focus, 
	.postbox input[type=datetime-local]:focus, 
	.postbox input[type=datetime]:focus, 
	.postbox input[type=email]:focus, 
	.postbox input[type=month]:focus, 
	.postbox input[type=number]:focus, 
	.postbox input[type=password]:focus, 
	.postbox input[type=radio]:focus, 
	.postbox input[type=search]:focus, 
	.postbox input[type=tel]:focus, 
	.postbox input[type=text]:focus, 
	.postbox input[type=time]:focus, 
	.postbox input[type=url]:focus, 
	.postbox input[type=week]:focus, 
	.postbox select:focus, 
	.postbox textarea:focus {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarLinkColor'] ); ?>;
	}
	/* Arrow Color */
	.postbox .accordion-section-title:after, 
	.postbox .handlediv, 
	.postbox .item-edit, 
	.postbox .sidebar-name-arrow, 
	.postbox .widget-action {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarHeadingColor'] ); ?>;
	}
	.postbox .accordion-section-title:after:hover, 
	.postbox .handlediv:hover, 
	.postbox .item-edit:hover, 
	.postbox .sidebar-name-arrow:hover, 
	.postbox .widget-action:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarLinkHoverColor'] ); ?>;
	}
	/* Divider */
	.postbox {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarDividerColor'] ); ?>;
	}

	/* Content Buttons */
	.wp-core-ui .button.button-primary {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonBgColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonTextColor'] ); ?>;
	}
	.wp-core-ui .button.button-primary:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonBgHoverColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonBgHoverColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonTextHoverColor'] ); ?>;
	}
	.wp-core-ui .button, 
	.wp-core-ui .button-secondary,
	.comment-php #minor-publishing .button {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonBgColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonTextColor'] ); ?>;
	}
	.comment-php #minor-publishing .button:hover,
	.wp-core-ui .button-secondary:focus, 
	.wp-core-ui .button-secondary:hover, 
	.wp-core-ui .button.focus, 
	.wp-core-ui .button.hover, 
	.wp-core-ui .button:focus, 
	.wp-core-ui .button:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonBgHoverColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonBgHoverColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonTextHoverColor'] ); ?>;
	}
	/* TinyMCE Tabs */
	.wp-switch-editor.switch-html,
	.wp-switch-editor:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?> !important;
	}
	.html-active .switch-html, 
	.tmce-active .switch-tmce {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentStandardButtonBgColor'] ); ?> !important;
	}
	/* Add New Button */
	.wrap .add-new-h2, 
	.wrap .add-new-h2:active {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonTextColor'] ); ?>;
	}
	.wrap .add-new-h2:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonBgHoverColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentPrimaryButtonTextHoverColor'] ); ?>;
	}

	/* Sidebar Buttons */
	.wp-core-ui .postbox .button.button-primary {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarPrimaryButtonBgColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarPrimaryButtonBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarPrimaryButtonTextColor'] ); ?>;
	} 
	.wp-core-ui .postbox .button.button-primary:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarPrimaryButtonBgHoverColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarPrimaryButtonBgHoverColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarPrimaryButtonTextHoverColor'] ); ?>;
	}
	.wp-core-ui .postbox .button, 
	.wp-core-ui .postbox .button-secondary, 
	.comment-php .postbox #minor-publishing .button {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarStandardButtonBgColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarStandardButtonBgColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarStandardButtonTextColor'] ); ?>;
	}
	.wp-core-ui .postbox .button:hover, 
	.comment-php .postbox #minor-publishing .button:hover,
	.wp-core-ui .postbox .button-secondary:focus, 
	.wp-core-ui .postbox .button-secondary:hover, 
	.wp-core-ui .postbox .button.focus, 
	.wp-core-ui .postbox .button.hover, 
	.wp-core-ui .postbox .button:focus, 
	.wp-core-ui .postbox .button:hover {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarStandardButtonBgHoverColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarStandardButtonBgHoverColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['sidebarStandardButtonTextHoverColor'] ); ?>;
	}

	/* Meta Link Tabs */
	#screen-meta-links a,
	#screen-meta-links a.show-settings {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentMetaTextColor'] ); ?>;
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentMetaBgColor'] ); ?> !important;
	}
	#screen-meta-links a:hover,
	#screen-meta-links a.show-settings:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentMetaTextHoverColor'] ); ?>;
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentMetaBgHoverColor'] ); ?> !important;
	}
	#screen-meta-links a:after {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentMetaTextColor'] ); ?>;
	}
	#screen-meta-links a:hover:after {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentMetaTextHoverColor'] ); ?>;
	}

	/* Dividing Line Colors */
	.wrap h2,
	#post-body-content > h2,
	.widefat thead th,
	.widefat tfoot th,
	.slate-settings .pageSection section,
	#slate__colorSchemes .colorNav ul,
	#welcome-panel,
	#wp-content-editor-tools,
	.wp-editor-expand #wp-content-editor-tools {
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?>;
	}

	/* Nav Tabs */
	.nav-tab {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?>;
		border-color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentDividerColor'] ); ?>;
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTextColor'] ); ?>;
	}
	.nav-tab:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['contentTextColor'] ); ?>;
	}

</style>