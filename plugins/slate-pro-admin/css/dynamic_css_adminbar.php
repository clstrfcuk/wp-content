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
}
?>
<style type="text/css" media="screen">

	/* *********************** */
	/* Admin Bar */
	/* *********************** */
	#wpadminbar {
		background-color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarBgColor'] ); ?>;
	}
	/* Admin Bar Hover Bg Color */
	#wpadminbar .ab-top-menu>li.hover>.ab-item,
	#wpadminbar .ab-top-menu>li:hover>.ab-item,
	#wpadminbar .ab-top-menu>li>.ab-item:focus,
	#wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus,
	#wpadminbar .menupop .ab-sub-wrapper,
	#wpadminbar .quicklinks .menupop ul.ab-sub-secondary,
	#wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu,
	.wp-responsive-open #wpadminbar #wp-admin-bar-menu-toggle a,
	#wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item,
	#wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus {
		background: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarBgHoverColor'] ); ?>;
	}
	/* Top Level Tex Color */
	#wpadminbar #adminbarsearch:before,
	#wpadminbar .ab-icon:before,
	#wpadminbar .ab-item:before,
	#wpadminbar a.ab-item,
	#wpadminbar > #wp-toolbar span.ab-label,
	#wpadminbar > #wp-toolbar span.noticon,
	.wp-responsive-open #wpadminbar #wp-admin-bar-menu-toggle .ab-icon:before {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarTopLevelColor'] ); ?>;
	}
	/* Top Level Text Color Hover */
	#wpadminbar li .ab-item:focus:before,
	#wpadminbar li a:focus .ab-icon:before,
	#wpadminbar li.hover .ab-icon:before,
	#wpadminbar li.hover .ab-item:before,
	#wpadminbar li:hover #adminbarsearch:before,
	#wpadminbar li:hover .ab-icon:before,
	#wpadminbar li:hover .ab-item:before,
	#wpadminbar > #wp-toolbar a:focus span.ab-label,
	#wpadminbar > #wp-toolbar li.hover span.ab-label,
	#wpadminbar > #wp-toolbar li:hover span.ab-label,
	#wpadminbar .ab-top-menu>li.hover>.ab-item,
	#wpadminbar .ab-top-menu>li:hover>.ab-item,
	#wpadminbar .ab-top-menu>li.hover>.ab-item,
	#wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus,
	#wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item,
	#wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus,
	#wpadminbar:not(.mobile)>#wp-toolbar a:focus span.ab-label,
	#wpadminbar:not(.mobile)>#wp-toolbar li:hover span.ab-label,
	#wpadminbar>#wp-toolbar li.hover span.ab-label {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarTopLevelHoverColor'] ); ?>;
	}
	/* Admin Bar Submenu Text Color */
	#wpadminbar .ab-submenu .ab-item,
	#wpadminbar .quicklinks .menupop ul li a,
	#wpadminbar .quicklinks .menupop ul li a strong,
	#wpadminbar .quicklinks .menupop.hover ul li a,
	#wpadminbar.nojs .quicklinks .menupop:hover ul li a {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarSubmenuTextColor'] ); ?>;
	}
	/* Admin Bar Submenu Text Hover Color */
	#wpadminbar .quicklinks .menupop ul li a:focus,
	#wpadminbar .quicklinks .menupop ul li a:focus strong,
	#wpadminbar .quicklinks .menupop ul li a:hover,
	#wpadminbar .quicklinks .menupop ul li a:hover strong,
	#wpadminbar .quicklinks .menupop.hover ul li a:focus,
	#wpadminbar .quicklinks .menupop.hover ul li a:hover,
	#wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus,
	#wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarSubmenuTextHoverColor'] ); ?>;
	}

	/* Custom Admin Bar Settings for Specific Plugins */
	#wpcontent #wp-admin-bar-root-default #wp-admin-bar-WPML_ALS {
		color: <?php echo slate_pro_sanitize_hex( $colorSelected['adminBarTopLevelColor'] ); ?>;
		padding: 14px 10px 15px 10px;
	}
	.folded #wpcontent #wp-admin-bar-root-default #wp-admin-bar-WPML_ALS {
		padding: 2px 7px 2px 7px;
	}
	@media screen and (max-width: 960px) {
		#wpcontent #wp-admin-bar-root-default #wp-admin-bar-WPML_ALS {
			padding: 2px 7px 2px 7px;
		}
	}
	@media screen and (max-width: 782px) {
		#wpcontent #wp-admin-bar-root-default #wp-admin-bar-WPML_ALS,
		.folded #wpcontent #wp-admin-bar-root-default #wp-admin-bar-WPML_ALS {
			padding: 0;
		}
	}

</style>