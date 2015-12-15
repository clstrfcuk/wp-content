<?php
/**
 * Settings class.
 *
 * @since 1.0.0
 *
 * @package Soliloquy
 * @author  Thomas Griffin
 */
class Soliloquy_Settings {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds the submenu pagehook.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $hook;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Soliloquy::get_instance();

        // Add custom settings submenu.
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        // Add callbacks for settings tabs.
        add_action( 'soliloquy_tab_settings_general', array( $this, 'settings_general_tab' ) );
        add_action( 'soliloquy_tab_settings_addons', array( $this, 'settings_addons_tab' ) );

        // Add the settings menu item to the Plugins table.
        add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'soliloquy.php' ), array( $this, 'settings_link' ) );

        // Possibly add a callback for upgrading.
        $upgrade_lite = get_option( 'soliloquy_upgrade' );
        if ( $upgrade_lite ) {
            return;
        }

        $v1_license = get_option( 'soliloquy_license_key' );
        if ( ! $v1_license ) {
            return;
        }

        add_action( 'soliloquy_tab_settings_upgrade', array( $this, 'settings_upgrade_tab' ) );

    }

    /**
     * Register the Settings submenu item for Soliloquy.
     *
     * @since 1.0.0
     */
    public function admin_menu() {

        // Register the submenu.
        $this->hook = add_submenu_page(
            'edit.php?post_type=soliloquy',
            __( 'Soliloquy Settings', 'soliloquy' ),
            __( 'Settings', 'soliloquy' ),
            apply_filters( 'soliloquy_menu_cap', 'manage_options' ),
            $this->base->plugin_slug . '-settings',
            array( $this, 'settings_page' )
        );

        // If successful, load admin assets only on that page and check for addons refresh.
        if ( $this->hook ) {
            add_action( 'load-' . $this->hook, array( $this, 'maybe_refresh_addons' ) );
            add_action( 'load-' . $this->hook, array( $this, 'settings_page_assets' ) );
        }

    }

    /**
     * Maybe refreshes the addons page.
     *
     * @since 1.0.0
     *
     * @return null Return early if not refreshing the addons.
     */
    public function maybe_refresh_addons() {

        if ( ! $this->is_refreshing_addons() ) {
            return;
        }

        if ( ! $this->refresh_addons_action() ) {
            return;
        }

        if ( ! $this->base->get_license_key() ) {
            return;
        }

        $this->get_addons_data( $this->base->get_license_key() );

    }

    /**
     * Loads assets for the settings page.
     *
     * @since 1.0.0
     */
    public function settings_page_assets() {

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

    }

    /**
     * Register and enqueue settings page specific CSS.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_styles() {

        wp_register_style( $this->base->plugin_slug . '-settings-style', plugins_url( 'assets/css/settings.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-settings-style' );

        // Run a hook to load in custom styles.
        do_action( 'soliloquy_settings_styles' );

    }

    /**
     * Register and enqueue settings page specific JS.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_scripts() {

        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_register_script( $this->base->plugin_slug . '-settings-script', plugins_url( 'assets/js/settings.js', $this->base->file ), array( 'jquery', 'jquery-ui-tabs' ), $this->base->version, true );
        wp_enqueue_script( $this->base->plugin_slug . '-settings-script' );
        wp_localize_script(
            $this->base->plugin_slug . '-settings-script',
            'soliloquy_settings',
            array(
                'active'           => __( 'Status: Active', 'soliloquy' ),
                'activate'         => __( 'Activate', 'soliloquy' ),
                'activate_nonce'   => wp_create_nonce( 'soliloquy-activate' ),
                'activating'       => __( 'Activating...', 'soliloquy' ),
                'ajax'             => admin_url( 'admin-ajax.php' ),
                'deactivate'       => __( 'Deactivate', 'soliloquy' ),
                'deactivate_nonce' => wp_create_nonce( 'soliloquy-deactivate' ),
                'deactivating'     => __( 'Deactivating...', 'soliloquy' ),
                'inactive'         => __( 'Status: Inactive', 'soliloquy' ),
                'install'          => __( 'Install Addon', 'soliloquy' ),
                'install_nonce'    => wp_create_nonce( 'soliloquy-install' ),
                'installing'       => __( 'Installing...', 'soliloquy' ),
                'proceed'          => __( 'Proceed', 'soliloquy' ),
                'ajax'             => admin_url( 'admin-ajax.php' ),
                'redirect'         => add_query_arg( array( 'post_type' => 'soliloquy', 'soliloquy-upgraded' => true ), admin_url( 'edit.php' ) ),
                'upgrade_nonce'    => wp_create_nonce( 'soliloquy-upgrade' )
            )
        );

        // Run a hook to load in custom scripts.
        do_action( 'soliloquy_settings_scripts' );

    }

    /**
     * Callback to output the Soliloquy settings page.
     *
     * @since 1.0.0
     */
    public function settings_page() {

        ?>
        <div id="soliloquy-settings" class="wrap">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <div class="soliloquy soliloquy-clear">
                <div id="soliloquy-tabs" class="soliloquy-clear">
                    <h2 id="soliloquy-tabs-nav" class="soliloquy-clear nav-tab-wrapper">
                    <?php $i = 0; foreach ( (array) $this->get_soliloquy_settings_tab_nav() as $id => $title ) : $class = 0 === $i ? 'soliloquy-active nav-tab-active' : ''; ?>
                        <a class="nav-tab <?php echo $class; ?>" href="#soliloquy-tab-<?php echo $id; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
                    <?php $i++; endforeach; ?>
                    </h2>
                    <?php $i = 0; foreach ( (array) $this->get_soliloquy_settings_tab_nav() as $id => $title ) : $class = 0 === $i ? 'soliloquy-active' : ''; ?>
                    <div id="soliloquy-tab-<?php echo $id; ?>" class="soliloquy-tab soliloquy-clear <?php echo $class; ?>">
                        <?php do_action( 'soliloquy_tab_settings_' . $id ); ?>
                    </div>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>
        <?php

    }

    /**
     * Callback for getting all of the settings tabs for Soliloquy.
     *
     * @since 1.0.0
     *
     * @return array Array of tab information.
     */
    public function get_soliloquy_settings_tab_nav() {

        $tabs = array(
            'general' => __( 'General', 'soliloquy' ), // This tab is required. DO NOT REMOVE VIA FILTERING.
            'addons'  => __( 'Addons', 'soliloquy' ),
        );
        $tabs = apply_filters( 'soliloquy_settings_tab_nav', $tabs );

        // Possibly add a tab for upgrading.
        $upgrade_lite = get_option( 'soliloquy_upgrade' );
        if ( $upgrade_lite ) {
            return $tabs;
        }

        $v1_license = get_option( 'soliloquy_license_key' );
        if ( ! $v1_license ) {
            return $tabs;
        }

        $tabs['upgrade'] = __( 'Upgrade', 'soliloquy' );

        return $tabs;

    }

    /**
     * Callback for displaying the UI for general settings tab.
     *
     * @since 1.0.0
     */
    public function settings_general_tab() {

        ?>
        <div id="soliloquy-settings-general">
            <table class="form-table">
                <tbody>
                    <tr id="soliloquy-settings-key-box">
                        <th scope="row">
                            <label for="soliloquy-settings-key"><?php _e( 'Soliloquy License Key', 'soliloquy' ); ?></label>
                        </th>
                        <td>
                            <form id="soliloquy-settings-verify-key" method="post">
                                <input type="password" name="soliloquy-license-key" id="soliloquy-settings-key" value="<?php echo ( $this->base->get_license_key() ? $this->base->get_license_key() : '' ); ?>" />
                                <?php wp_nonce_field( 'soliloquy-key-nonce', 'soliloquy-key-nonce' ); ?>
                                <?php submit_button( __( 'Verify Key', 'soliloquy' ), 'primary', 'soliloquy-verify-submit', false ); ?>
                                <?php submit_button( __( 'Deactivate Key', 'soliloquy' ), 'secondary', 'soliloquy-deactivate-submit', false ); ?>
                                <p class="description"><?php _e( 'License key to enable automatic updates for Soliloquy.', 'soliloquy' ); ?></p>
                            </form>
                        </td>
                    </tr>
                    <?php $type = $this->base->get_license_key_type(); if ( ! empty( $type ) ) : ?>
                    <tr id="soliloquy-settings-key-type-box">
                        <th scope="row">
                            <label for="soliloquy-settings-key-type"><?php _e( 'Soliloquy License Key Type', 'soliloquy' ); ?></label>
                        </th>
                        <td>
                            <form id="soliloquy-settings-key-type" method="post">
                                <span class="soliloquy-license-type"><?php printf( __( 'Your license key type for this site is <strong>%s.</strong>', 'soliloquy' ), $this->base->get_license_key_type() ); ?>
                                <input type="hidden" name="soliloquy-license-key" value="<?php echo $this->base->get_license_key(); ?>" />
                                <?php wp_nonce_field( 'soliloquy-key-nonce', 'soliloquy-key-nonce' ); ?>
                                <?php submit_button( __( 'Refresh Key', 'soliloquy' ), 'primary', 'soliloquy-refresh-submit', false ); ?>
                                <p class="description"><?php _e( 'Your license key type (handles updates and Addons). Click refresh if your license has been upgraded or the type is incorrect.', 'soliloquy' ); ?></p>
                            </form>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php do_action( 'soliloquy_settings_general_box' ); ?>
                </tbody>
            </table>
        </div>
        <?php

    }

    /**
     * Callback for displaying the UI for addons settings tab.
     *
     * @since 1.0.0
     */
    public function settings_addons_tab() {

        // Go ahead and grab the type of license. It will be necessary for displaying Addons.
        $type = $this->base->get_license_key_type();

        // Only display the Addons information if no license key errors are present.
        if ( ! $this->base->get_license_key_errors() ) :
        ?>
        <div id="soliloquy-settings-addons">
            <?php if ( empty( $type ) ) : ?>
                <div class="error below-h2"><p><?php _e( 'In order to get access to Addons, you need to verify your license key for Soliloquy.', 'soliloquy' ); ?></p></div>
            <?php else : ?>
                <?php $addons = $this->get_addons(); if ( $addons ) : ?>
                    <form id="soliloquy-settings-refresh-addons-form" method="post">
                        <p><?php _e( 'Missing addons that you think you should be able to see? Try clicking the button below to refresh the addon data.', 'soliloquy' ); ?></p>
                        <?php wp_nonce_field( 'soliloquy-refresh-addons', 'soliloquy-refresh-addons' ); ?>
                        <?php submit_button( __( 'Refresh Addons', 'soliloquy' ), 'primary', 'soliloquy-refresh-addons-submit', false ); ?>
                    </form>
                    <div id="soliloquy-addons-area" class="soliloquy-clear">
                        <?php
                        // Let's begin outputting the addons.
                        $i = 0;
                        foreach ( (array) $addons as $i => $addon ) {
                            // Attempt to get the plugin basename if it is installed or active.
                            $plugin_basename   = $this->get_plugin_basename_from_slug( $addon->slug );
                            $installed_plugins = get_plugins();
                            $last              = ( 2 == $i%3 ) ? 'last' : '';

                            echo '<div class="soliloquy-addon ' . $last . '">';
                                echo '<img class="soliloquy-addon-thumb" src="' . esc_url( $addon->image ) . '" width="300px" height="250px" alt="' . esc_attr( $addon->title ) . '" />';
                                echo '<h3 class="soliloquy-addon-title">' . esc_html( $addon->title ) . '</h3>';

                                // If the plugin is active, display an active message and deactivate button.
                                if ( is_plugin_active( $plugin_basename ) ) {
                                    echo '<div class="soliloquy-addon-active soliloquy-addon-message">';
                                        echo '<span class="addon-status">' . __( 'Status: Active', 'soliloquy' ) . '</span>';
                                        echo '<div class="soliloquy-addon-action">';
                                            echo '<a class="button button-primary soliloquy-addon-action-button soliloquy-deactivate-addon" href="#" rel="' . esc_attr( $plugin_basename ) . '">' . __( 'Deactivate', 'soliloquy' ) . '</a><span class="spinner soliloquy-spinner"></span>';
                                        echo '</div>';
                                    echo '</div>';
                                }

                                // If the plugin is not installed, display an install message and install button.
                                if ( ! isset( $installed_plugins[$plugin_basename] ) ) {
                                    echo '<div class="soliloquy-addon-not-installed soliloquy-addon-message">';
                                        echo '<span class="addon-status">' . __( 'Status: Not Installed', 'soliloquy' ) . '</span>';
                                        echo '<div class="soliloquy-addon-action">';
                                            echo '<a class="button button-primary soliloquy-addon-action-button soliloquy-install-addon" href="#" rel="' . esc_url( $addon->url ) . '">' . __( 'Install Addon', 'soliloquy' ) . '</a><span class="spinner soliloquy-spinner"></span>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                                // If the plugin is installed but not active, display an activate message and activate button.
                                elseif ( is_plugin_inactive( $plugin_basename ) ) {
                                    echo '<div class="soliloquy-addon-inactive soliloquy-addon-message">';
                                        echo '<span class="addon-status">' . __( 'Status: Inactive', 'soliloquy' ) . '</span>';
                                        echo '<div class="soliloquy-addon-action">';
                                            echo '<a class="button button-primary soliloquy-addon-action-button soliloquy-activate-addon" href="#" rel="' . esc_attr( $plugin_basename ) . '">' . __( 'Activate', 'soliloquy' ) . '</a><span class="spinner soliloquy-spinner"></span>';
                                        echo '</div>';
                                    echo '</div>';
                                }

                                echo '<p class="soliloquy-addon-excerpt">' . esc_html( $addon->excerpt ) . '</p>';
                            echo '</div>';
                            $i++;
                        }
                        ?>
                    </div>
                <?php else : ?>
                    <form id="soliloquy-settings-refresh-addons-form" method="post">
                        <p><?php _e( 'There was an issue retrieving the addons for this site. Please click on the button below the refresh the addons data.', 'soliloquy' ); ?></p>
                        <?php wp_nonce_field( 'soliloquy-refresh-addons', 'soliloquy-refresh-addons' ); ?>
                        <?php submit_button( __( 'Refresh Addons', 'soliloquy' ), 'primary', 'soliloquy-refresh-addons-submit', false ); ?>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php else : ?>
            <div class="error below-h2"><p><?php _e( 'In order to get access to Addons, you need to resolve your license key errors.', 'soliloquy' ); ?></p></div>
        <?php
        endif;

    }

    /**
     * Callback for displaying the UI for upgrade settings tab.
     *
     * @since 1.0.0
     */
    public function settings_upgrade_tab() {

        ?>
        <div id="soliloquy-settings-upgrade">
            <p><strong><?php _e( 'You have upgraded to v2 of Soliloquy. You need to upgrade your sliders using our custom upgrading tool. Click on the button below to start the process.', 'soliloquy' ); ?></strong></p>
            <p><a class="button button-primary soliloquy-start-upgrade" href="#" title="<?php esc_attr_e( 'Click Here to Start the Upgrade Process', 'soliloquy' ); ?>"><?php _e( 'Click Here to Start the Upgrade Process', 'soliloquy' ); ?></a> <span class="spinner soliloquy-spinner"></span></p>
        </div>
        <?php

    }

    /**
     * Retrieves addons from the stored transient or remote server.
     *
     * @since 1.0.0
     *
     * @return bool|array False if no key or failure, array of addon data otherwise.
     */
    public function get_addons() {

        $key = $this->base->get_license_key();
        if ( ! $key ) {
            return false;
        }

        if ( false === ( $addons = get_transient( '_sol_addons' ) ) ) {
            $addons = $this->get_addons_data( $key );
        } else {
            return $addons;
        }

    }

    /**
     * Pings the remote server for addons data.
     *
     * @since 1.0.0
     *
     * @param string $key The user license key.
     * @return bool|array False if no key or failure, array of addon data otherwise.
     */
    public function get_addons_data( $key ) {

        $addons = Soliloquy_License::get_instance()->perform_remote_request( 'get-addons-data', array( 'tgm-updater-key' => $key ) );

        // If there was an API error, set transient for only 10 minutes.
        if ( ! $addons ) {
            set_transient( '_sol_addons', false, 10 * MINUTE_IN_SECONDS );
            return false;
        }

        // If there was an error retrieving the addons, set the error.
        if ( isset( $addons->error ) ) {
            set_transient( '_sol_addons', false, 10 * MINUTE_IN_SECONDS );
            return false;
        }

        // Otherwise, our request worked. Save the data and return it.
        set_transient( '_sol_addons', $addons, DAY_IN_SECONDS );
        return $addons;

    }

    /**
     * Flag to determine if addons are being refreshed.
     *
     * @since 1.0.0
     *
     * @return bool True if being refreshed, false otherwise.
     */
    public function is_refreshing_addons() {

        return isset( $_POST['soliloquy-refresh-addons-submit'] );

    }

    /**
     * Verifies nonces that allow addon refreshing.
     *
     * @since 1.0.0
     *
     * @return bool True if nonces check out, false otherwise.
     */
    public function refresh_addons_action() {

        return isset( $_POST['soliloquy-refresh-addons-submit'] ) && wp_verify_nonce( $_POST['soliloquy-refresh-addons'], 'soliloquy-refresh-addons' );

    }

    /**
     * Retrieve the plugin basename from the plugin slug.
     *
     * @since 1.0.0
     *
     * @param string $slug The plugin slug.
     * @return string      The plugin basename if found, else the plugin slug.
     */
    public function get_plugin_basename_from_slug( $slug ) {

        $keys = array_keys( get_plugins() );

        foreach ( $keys as $key ) {
            if ( preg_match( '|^' . $slug . '|', $key ) ) {
                return $key;
            }
        }

        return $slug;

    }

    /**
     * Add Settings page to plugin action links in the Plugins table.
     *
     * @since 1.0.0
     *
     * @param array $links  Default plugin action links.
     * @return array $links Amended plugin action links.
     */
    public function settings_link( $links ) {

        $settings_link = sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'post_type' => 'soliloquy', 'page' => 'soliloquy-settings' ), admin_url( 'edit.php' ) ), __( 'Settings', 'soliloquy' ) );
        array_unshift( $links, $settings_link );

        return $links;

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Soliloquy_Settings object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Settings ) ) {
            self::$instance = new Soliloquy_Settings();
        }

        return self::$instance;

    }

}

// Load the settings class.
$soliloquy_settings = Soliloquy_Settings::get_instance();