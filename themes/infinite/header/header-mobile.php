<?php
	// mobile menu template
	
	echo '<div class="infinite-mobile-header-wrap" >';

	// top bar
	$top_bar = infinite_get_option('general', 'enable-top-bar-on-mobile', 'disable');
	if( $top_bar == 'enable' ){
		get_template_part('header/header', 'top-bar');
	}

	// header
	echo '<div class="infinite-mobile-header infinite-header-background infinite-style-slide" id="infinite-mobile-header" >';
	echo '<div class="infinite-mobile-header-container infinite-container" >';
	echo infinite_get_logo(array('mobile' => true));

	echo '<div class="infinite-mobile-menu-right" >';

	// search icon
	$enable_search = (infinite_get_option('general', 'enable-main-navigation-search', 'enable') == 'enable')? true: false;
	if( $enable_search ){
		echo '<div class="infinite-main-menu-search" id="infinite-mobile-top-search" >';
		echo '<i class="fa fa-search" ></i>';
		echo '</div>';
		infinite_get_top_search();
	}

	// cart icon
	$enable_cart = (infinite_get_option('general', 'enable-main-navigation-cart', 'enable') == 'enable' && class_exists('WooCommerce'))? true: false;
	if( $enable_cart ){
		echo '<div class="infinite-main-menu-cart" id="infinite-mobile-menu-cart" >';
		echo '<i class="fa fa-shopping-cart" ></i>';
		infinite_get_woocommerce_bar();
		echo '</div>';
	}

	// mobile menu
	if( has_nav_menu('mobile_menu') ){
		infinite_get_custom_menu(array(
			'type' => infinite_get_option('general', 'right-menu-type', 'right'),
			'container-class' => 'infinite-mobile-menu',
			'button-class' => 'infinite-mobile-menu-button',
			'icon-class' => 'fa fa-bars',
			'id' => 'infinite-mobile-menu',
			'theme-location' => 'mobile_menu'
		));
	}
	echo '</div>'; // infinite-mobile-menu-right
	echo '</div>'; // infinite-mobile-header-container
	echo '</div>'; // infinite-mobile-header

	echo '</div>'; // infinite-mobile-header-wrap