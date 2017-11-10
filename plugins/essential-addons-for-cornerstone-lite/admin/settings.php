<?php 
/**
 * Admin Settings Page
 */

if( ! defined( 'ABSPATH' ) ) exit(); // Exit if accessed directly

class Eacs_Admin_Settings {
	protected $is_pro = FALSE;
	private $eacs_default_settings = array(
	   'logo-carousel'      => true,
	   'post-grid'      	=> true,
	   'post-carousel'      => true,
	   'product-grid'  		=> true,
	   'product-carousel'   => true,
	   'product-grid'       => true,
	   'team-members'       => true,
	   'testimonial-slider' => true,
	);

	private $eacs_settings;
	private $eacs_get_settings;

	/**
	 * Initializing all default hooks and functions
	 * @param 
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'create_eacs_admin_menu' ) );	
		add_action( 'init', array( $this, 'enqueue_eacs_admin_scripts' ) );
		add_action( 'wp_ajax_save_settings_with_ajax', array( $this, 'eacs_save_settings_with_ajax' ) );
		add_action( 'wp_ajax_nopriv_save_settings_with_ajax', array( $this, 'eacs_save_settings_with_ajax' ) );

	}

	/**
	 * Loading all essential scripts
	 * @param
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_eacs_admin_scripts() {

		if( isset( $_GET['page'] ) && $_GET['page'] == 'eacs-settings' ) {
			wp_enqueue_style( 'essential_addons_elementor-admin-css', plugins_url( '/', __FILE__ ).'assets/css/admin.css' );
			wp_enqueue_style( 'font-awesome-css', plugins_url( '/', __FILE__ ).'assets/vendor/font-awesome/css/font-awesome.min.css' );
			wp_enqueue_style( 'essential_addons_elementor-sweetalert2-css', plugins_url( '/', __FILE__ ).'assets/vendor/sweetalert2/css/sweetalert2.min.css' );

			wp_enqueue_script( "jquery-ui-tabs" );
			wp_enqueue_script( 'essential_addons_elementor-admin-js', plugins_url( '/', __FILE__ ).'assets/js/admin.js', array( 'jquery', 'jquery-ui-tabs' ), '1.0', true );
			wp_enqueue_script( 'essential_addons_core-js', plugins_url( '/', __FILE__ ).'assets/vendor/sweetalert2/js/core.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'essential_addons_sweetalert2-js', plugins_url( '/', __FILE__ ).'assets/vendor/sweetalert2/js/sweetalert2.min.js', array( 'jquery', 'essential_addons_core-js' ), '1.0', true );
		}

	}

	/**
	 * Create an admin menu.
	 * @param 
	 * @return void
	 * @since 1.0.0 
	 */
	public function create_eacs_admin_menu() {

		add_menu_page( 
			'Essential Addons Cornerstone', 
			'Essential Addons Cornerstone', 
			'manage_options', 
			'eacs-settings', 
			array( $this, 'eacs_admin_settings_page' ), 
			plugins_url( '/', __FILE__ ).'/assets/images/ea-icon.png',
			199 
		);

	}

	/**
	 * Create settings page.
	 * @param 
	 * @return void
	 * @since 1.0.0
	 */
	public function eacs_admin_settings_page() {

		$js_info = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'essential_addons_elementor-admin-js', 'settings', $js_info );

	   /**
	    * This section will handle the "eacs_save_settings" array. If any new settings options is added
	    * then it will matches with the older array and then if it founds anything new then it will update the entire array.
	    */
	   $this->eacs_get_settings = get_option( 'eacs_save_settings', $this->eacs_default_settings );
	   $eacs_new_settings = array_diff_key( $this->eacs_default_settings, $this->eacs_get_settings );
	   if( ! empty( $eacs_new_settings ) ) {
	   	$eacs_updated_settings = array_merge( $this->eacs_get_settings, $eacs_new_settings );
	   	update_option( 'eacs_save_settings', $eacs_updated_settings );
	   }
	   $this->eacs_get_settings = get_option( 'eacs_save_settings', $this->eacs_default_settings );
		?>
		<div class="wrap">
		  	<h2><?php _e( 'Essential Addons for Cornerstone & Pro Settings', 'essential-addons-cs' ); ?></h2> <hr>
		  	<div class="response-wrap"></div>
		  	<form action="" method="POST" id="eacs-settings" name="eacs-settings">
			  	<div class="eacs-settings-tabs">
			    	<ul>
				      <li><a href="#general"><i class="fa fa-cogs"></i> General</a></li>
				      <li><a href="#elements"><i class="fa fa-cubes"></i> Elements</a></li>
				      <li><a href="#go-pro"><i class="fa fa-magic"></i> Go Premium</a></li>
				      <li><a href="#support"><i class="fa fa-ticket"></i> Support</a></li>
			    	</ul>
			    	<div id="general" class="eacs-settings-tab">
						<div class="row general-row">

			      			<div class="col-half">
			      				<a href="https://essential-addons.com/cornerstone/" target="_blank" class="button eacs-btn eacs-demo-btn">Explore Demos</a>
			      				<a href="https://essential-addons.com/cornerstone/buy.php" target="_blank" class="button eacs-btn eacs-license-btn">Update to Premium</a>

			      				<div class="eacs-notice">
			      					<h5>Troubleshooting Info</h5>
			      					<p>After update, if you see any element is not working properly, go to <strong>Elements</strong> Tab, toggle the element and save changes.</p>
			      				</div>
			    			</div>
			      			<div class="col-half">

			      				<img class="eacs-logo-admin" src="<?php echo plugins_url( '/', __FILE__ ).'assets/images/eacs-logo.png'; ?>">
			      			</div>
			    		</div>
			    	</div>
			    	<div id="elements" class="eacs-settings-tab">
			      	<div class="row">
			      		<div class="col-full">
			      			<table class="form-table">
									<tr>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Post Grid', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Post Grid', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="post-grid" name="post-grid" <?php checked( 1, $this->eacs_get_settings['post-grid'], true ); ?> >
				                        <label for="post-grid"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Logo Carousel', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Logo Carousel', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="logo-carousel" name="logo-carousel" <?php checked( 1, $this->eacs_get_settings['logo-carousel'], true ); ?> >
				                        <label for="logo-carousel"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Post Carousel', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Post Carousel', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="post-carousel" name="post-carousel" <?php checked( 1, $this->eacs_get_settings['post-carousel'], true ); ?> >
				                        <label for="post-carousel"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Product Carousel', 'essential-addons-cs' ) ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Product Carousel', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="product-carousel" name="product-carousel" <?php checked( 1, $this->eacs_get_settings['product-carousel'], true ); ?> >
				                        <label for="product-carousel"></label>
				                    	</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Team Members', 'essential-addons-cs' ) ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Team Members', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="team-members" name="team-members" <?php checked( 1, $this->eacs_get_settings['team-members'], true ); ?> >
				                        <label for="team-members"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Testimonial Slider', 'essential-addons-cs' ) ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Testimonial Slider', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="testimonial-slider" name="testimonial-slider" <?php checked( 1, $this->eacs_get_settings['testimonial-slider'], true ); ?> >
				                        <label for="testimonial-slider"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Product Grid', 'essential-addons-cs' ) ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Product Grid', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="product-grid" name="product-grid" <?php checked( 1, $this->eacs_get_settings['product-grid'], true ); ?> >
				                        <label for="product-grid"></label>
				                    	</div>
										</td>
									</tr>
					      	</table>
			      		</div>
			      		<div class="col-full">
			      			<div class="premium-elements-title">
			      				<img src="<?php echo plugins_url( '/', __FILE__ ).'assets/images/lock-icon.png'; ?>">
			      				<h2 class="section-title">Premium Elements</h2>
			      			</div>
			      			<table class="form-table">
									<tr>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Count Down', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Count Down', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="count-down" name="count-down" disabled >
				                        <label for="count-down" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Creative Button', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Creative Button', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="creative-btn" name="creative-btn" disabled >
				                        <label for="creative-btn" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Image Comparison', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Image Comparison', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="img-comparison" name="img-comparison" disabled >
				                        <label for="img-comparison" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Instagram Feed', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Instagram Feed', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="instagram-feed" name="instagram-feed" disabled >
				                        <label for="instagram-feed" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Interactive Promo', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Interactive Promo', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="interactive-promo" name="interactive-promo" disabled >
				                        <label for="interactive-promo" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Lightbox', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Lightbox', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="lightbox" name="lightbox" disabled >
				                        <label for="lightbox" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Post Block', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Post Block', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="post-block" name="post-block" disabled >
				                        <label for="post-block" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Post Timeline', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Post Timeline', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="post-timeline" name="post-timeline" disabled >
				                        <label for="post-timeline" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
										<td>
											<div class="eacs-checkbox">
												<p class="title"><?php _e( 'Social Icons', 'essential-addons-cs' ); ?></p>
												<p class="desc"><?php _e( 'Activate / Deactivate Social Icons', 'essential-addons-cs' ); ?></p>
				                        <input type="checkbox" id="social-icons" name="social-icons" disabled >
				                        <label for="social-icons" class="<?php if( (bool) $is_pro === false ) : echo 'eacs-get-pro'; endif; ?>"></label>
				                    	</div>
										</td>
									</tr>
					      	</table>
						  	<div class="eacs-save-btn-wrap">
						  		<input type="submit" value="Save settings" class="button eacs-btn"/>
						  	</div>
			      		</div>
			      	</div>
			    	</div>
			    	<div id="go-pro" class="eacs-settings-tab">
			    		<div class="row go-premium">
			      			<div class="col-half">
			      				<h4>Why upgrade to Premium Version?</h4>
			      				<p>The premium version helps us to continue development of the product incorporating even more features and enhancements.</p>

			      				<p>You will also get world class support from our dedicated team, 24/7.</p>

			      				<a href="https://essential-addons.com/cornerstone/buy.php" target="_blank" class="button eacs-btn eacs-license-btn">Get Premium Version</a>
			      			</div>
			      			<div class="col-half">
			      				<img src="<?php echo plugins_url( '/', __FILE__ ).'assets/images/unlock-gif.gif'; ?>">
			      			</div>
			      		</div>
			    	</div>
			    	<div id="support" class="eacs-settings-tab">
			      	<div class="row">
			      		<div class="col-half">
				      		<h4>Need help? Open a support ticket!</h4>
				      		<p>You can always get support from the community.</p>
				      		<a href="https://wordpress.org/support/plugin/essential-addons-for-cornerstone-lite" target="_blank" class="button eacs-btn">Get Help</a>
				      	</div>
			      		<div class="col-half">
				      		<h4>Need Premium Support?</h4>
				      		<p>Purchasing a license entitles you to receive premium support.</p>
				      		<a href="https://essential-addons.com/cornerstone/buy.php" target="_blank" class="button eacs-btn">Get a license</a>
				      	</div>
			      	</div>

			      	<div class="row">
			      		<div class="col-half">
			      			<div class="essential-addons-community-link">
			      				<a href="https://www.facebook.com/groups/essentialaddons/" target="_blank"><i class="fa fa-facebook-official fa-2x fa-fw" aria-hidden="true"></i> <span>Join the Facebook Community</span></a>
			      			</div>
			      		</div>
			      	</div>
			    	</div>
			  	</div>
		  	</form>
		</div>
		<?php
		
	}

	/**
	 * Saving data with ajax request
	 * @param 
	 * @return  array in json
	 * @since 1.0.0 
	 */
	public function eacs_save_settings_with_ajax() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->eacs_settings = array(
				'logo-carousel' 		=> intval( $_POST['logoCarousel'] ? 1 : 0 ),
				'post-grid' 			=> intval( $_POST['postGrid'] ? 1 : 0 ),
				'post-carousel' 		=> intval( $_POST['postCarousel'] ? 1 : 0 ),
				'product-carousel' 		=> intval( $_POST['productCarousel'] ? 1 : 0 ),
				'product-grid' 			=> intval( $_POST['productGrid'] ? 1 : 0 ),
				'team-members' 			=> intval( $_POST['teamMembers'] ? 1 : 0 ),
				'testimonial-slider' 	=> intval( $_POST['testimonialSlider'] ? 1 : 0 ),
			);
			update_option( 'eacs_save_settings', $this->eacs_settings );
			return true;
			die();
		}

	}

}

new Eacs_Admin_Settings();

	


