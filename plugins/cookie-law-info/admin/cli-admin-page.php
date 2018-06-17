<?php
/*
  ===============================================================================

  Copyright 2018  Markwt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/** Displays admin page within WP dashboard */
function cookielawinfo_print_admin_page() {

    // Lock out non-admins:
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permission to perform this operation','cookie-law-info'));
    }

    // Get options:
    $the_options = cookielawinfo_get_admin_settings();

    // Check if form has been set:
    if (isset($_POST['update_admin_settings_form'])) {
        // Check nonce:
        check_admin_referer('cookielawinfo-update-' . CLI_SETTINGS_FIELD);
        foreach ($the_options as $key => $value) {
            if (isset($_POST[$key . '_field'])) {
                // Store sanitised values only:
                $the_options[$key] = cookielawinfo_sanitise($key, $_POST[$key . '_field']);
            }
        }
        update_option(CLI_SETTINGS_FIELD, $the_options);
        echo '<div class="updated"><p><strong>';
        echo __('Settings Updated.','cookie-law-info');
        echo '</strong></p></div>';
    } else if (isset($_POST['delete_all_settings'])) {
        // Check nonce:
        check_admin_referer('cookielawinfo-update-' . CLI_SETTINGS_FIELD);
        cookielawinfo_delete_settings();
        $the_options = cookielawinfo_get_admin_settings();
    } else if (isset($_POST['revert_to_previous_settings'])) {
        if (!cookielawinfo_copy_old_settings_to_new()) {
            echo '<h3>';
            echo __('ERROR MIGRATING SETTINGS (ERROR: 2)','cookie-law-info');
            echo '</h3>';
        }
        $the_options = cookielawinfo_get_admin_settings();
    }

    // Print form here:


    echo '<div class="wrap">';
    ?>
    <h2><?php echo __('Cookie Law Settings','cookie-law-info'); ?></h2>


    <?php
    // Migration controller:
    if (isset($_POST['cli-migration-button'])) {
        if (isset($_POST['cli-migration_field'])) {
            switch ($_POST['cli-migration_field']) {
                case '2':
                    // Migrate but keep
                    if (!cookielawinfo_migrate_to_new_version()) {
                        echo '<h3>';
                        echo __('ERROR MIGRATING SETTINGS (ERROR: 2)','cookie-law-info');
                        echo '</h3>';
                    }
                    break;
                case '3':
                    // Just use this version
                    cookielawinfo_update_to_latest_version_number();
                    break;
                default:
                    // Form error, ignore
                    echo '<h3>';
                    echo __('Error processing migration request (ERROR: 4)','cookie-law-info');
                    echo '</h3>';
                    break;
            }
        }
        $the_options = cookielawinfo_get_admin_settings();
    }
    echo '<form method="post" action="' . esc_url($_SERVER["REQUEST_URI"]) . '">';

    // Set nonce:
    if (function_exists('wp_nonce_field'))
        wp_nonce_field('cookielawinfo-update-' . CLI_SETTINGS_FIELD);
    ?>

    <div class="cli-plugin-container">
        <div class="cli-plugin-left-col width-62">
            <div class="pad-10">

                <!-- Toolbar -->
                <div class="cli-plugin-toolbar top">
                    <div class="left">

    <?php
    // Outputs the "cookie bar is on/off" message in the header
    $img_tag = '<img id="cli-plugin-status-icon" src="' . CLI_PLUGIN_URL . 'images/';
    $span_tag = '<span id="header_on_off_alert">';
    if ($the_options['is_on'] == true) {
        $img_tag .= 'tick.png" alt="tick icon" />';
        $span_tag .= __('Your Cookie Law Info bar is switched on','cookie-law-info');
        $span_tag .= '</span>';
    } else {
        $img_tag .= 'cross.png" alt="cross icon" />';
        $span_tag .= __('Your Cookie Law Info bar is switched off','cookie-law-info');
        $span_tag .= '</span>';
    }
    echo $img_tag . $span_tag;
    ?>

                    </div>
                    <div class="right">
                        <input type="submit" name="update_admin_settings_form" value="Update Settings" class="button-primary" />
                    </div>
                </div>

                <style>
                    /* http://css-tricks.com/snippets/jquery/simple-jquery-accordion/  ...with custom CSS */
                    dl.accordion dt {
                        background: #fff;
                        border: 1px #ccc solid;
                        color: #333;
                        font-size: 12px;
                        margin-bottom: 10px;
                        padding: 8px;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        -khtml-border-radius: 5px;
                    }
                </style>

                <!-- Accordion -->
                <dl class="accordion">


                    <dt class="ui-icon ui-icon-triangle-1-s"><a href="#"><?php echo __('Settings','cookie-law-info'); ?></a></dt>
                    <dd id="accordion_default">
                        <h4><?php echo __('The Cookie Bar','cookie-law-info'); ?></h4>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="is_on_field"><?php echo __('Cookie Bar is currently:','cookie-law-info'); ?></label></th>
                                <td>

                                    <input type="radio" id="is_on_field_yes" name="is_on_field" class="styled" value="true" <?php echo ( $the_options['is_on'] == true ) ? ' checked="checked" />' : ' />'; ?> On
                                           <input type="radio" id="is_on_field_no" name="is_on_field" class="styled" value="false" <?php echo ( $the_options['is_on'] == false ) ? ' checked="checked" />' : ' />'; ?> Off
                                           <span id="header_on_off_field_warning"></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="is_reject_on_field"><?php echo __('Reject Button:','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="is_reject_on_field_yes" name="is_reject_on_field" class="styled" value="true" <?php echo ( $the_options['is_reject_on'] == true ) ? ' checked="checked" />' : ' />'; ?> On
                                           <input type="radio" id="is_reject_on_field_no" name="is_reject_on_field" class="styled" value="false" <?php echo ( $the_options['is_reject_on'] == false ) ? ' checked="checked" />' : ' />'; ?> Off
                                           <!--<span id="header_on_off_field_warning"></span>-->
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="notify_position_vertical_field"><?php echo __('Cookie Bar will be show in:','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="notify_position_vertical_field" class="vvv_combobox">
    <?php
    if ($the_options['notify_position_vertical'] == "top") {
        echo '<option value="top" selected="selected">';
        echo __('Header','cookie-law-info');
        echo '</option>';
        echo '<option value="bottom">';
        echo __('Footer','cookie-law-info');
        echo '</option>';
    } else {
        echo '<option value="top">';
        echo __('Header','cookie-law-info');
        echo '</option>';
        echo '<option value="bottom" selected="selected">';
        echo __('Footer','cookie-law-info');
        echo '</option>';
    }
    ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- header_fix code here -->
                            <tr valign="top">
                                <th scope="row"><label for="header_fix_field"><?php echo __('Fix Cookie Bar to Header?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="header_fix_field_yes" name="header_fix_field" class="styled" value="true" <?php echo ( $the_options['header_fix'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="iheader_fix_field_no" name="header_fix_field" class="styled" value="false" <?php echo ( $the_options['header_fix'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                           <span class="cli-plugin-example"><?php echo __('If you select "Header" then you can optionally stick the cookie bar to the header. Will not have any effect if you select "Footer".','cookie-law-info'); ?></span>
                                </td>
                            </tr>
                            <!-- /header_fix -->

                            <tr valign="top">
                                <th scope="row"><label for="notify_animate_show_field"><?php echo __('On load','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="notify_animate_show_field" class="vvv_combobox">
    <?php
    if ($the_options['notify_animate_show'] == true) {
        echo '<option value="true" selected="selected">';
        echo __('Animate','cookie-law-info');
        echo '</option>';
        echo '<option value="false">';
        echo __('Sticky','cookie-law-info');
        echo '</option>';
    } else {
        echo '<option value="true">';
        echo __('Animate','cookie-law-info');
        echo '</option>';
        echo '<option value="false" selected="selected">';
        echo __('Sticky','cookie-law-info');
        echo '</option>';
    }
    ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="notify_animate_hide_field"><?php echo __('On hide','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="notify_animate_hide_field" class="vvv_combobox">
                                        <?php
                                        if ($the_options['notify_animate_hide'] == true) {
                                            echo '<option value="true" selected="selected">Animate</option>';
                                            echo '<option value="false">Disappear</option>';
                                        } else {
                                            echo '<option value="true">Animate</option>';
                                            echo '<option value="false" selected="selected">Disappear</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- SHOW ONCE / TIMER -->
                            <tr valign="top" class="hr-top">
                                <th scope="row"><label for="show_once_yn_field"><?php echo __('Auto-hide cookie bar after delay?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="show_once_yn_yes" name="show_once_yn_field" class="styled" value="true" <?php echo ( $the_options['show_once_yn'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="show_once_yn_no" name="show_once_yn_field" class="styled" value="false" <?php echo ( $the_options['show_once_yn'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="show_once_field"><?php echo __('Milliseconds until hidden','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="show_once_field" value="<?php echo $the_options['show_once'] ?>" />
                                    <span class="cli-plugin-example"><?php echo __('Specify milliseconds (not seconds) e.g.','cookie-law-info'); ?> <em>8000 = 8 seconds</em></span>
                                </td>
                            </tr>

                            <!-- NEW: CLOSE ON SCROLL -->
                            <tr valign="top" class="hr-top">
                                <th scope="row"><label for="scroll_close_field"><?php echo __('Auto-hide cookie bar if the user scrolls?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="scroll_close_yes" name="scroll_close_field" class="styled" value="true" <?php echo ( $the_options['scroll_close'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="scroll_close_no" name="scroll_close_field" class="styled" value="false" <?php echo ( $the_options['scroll_close'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top" class="">
                                <th scope="row"><label for="scroll_close_reload_field"><?php echo __('Reload after "scroll accept" event?','cookie-law-info'); ?></label></th>
                                <td>
                                        <!-- <input type="text" name="scroll_close_reload_field" value="<?php echo $the_options['scroll_close_reload'] ?>" />
                                        <span class="cli-plugin-example">If the user accepts, do you want to reload the page? This feature is mostly for Italian users who have to deal with a very specific interpretation of the cookie law.</span>
                                    -->


                                    <input type="radio" id="scroll_close_reload_yes" name="scroll_close_reload_field" class="styled" value="true" <?php echo ( $the_options['scroll_close_reload'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="scroll_close_reload_no" name="scroll_close_reload_field" class="styled" value="false" <?php echo ( $the_options['scroll_close_reload'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top" class="hr-bottom">
                                <th scope="row"><label for="accept_close_reload_field"><?php echo __('Reload on Accept button click','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="accept_close_reload_yes" name="accept_close_reload_field" class="styled" value="true" <?php echo ( $the_options['accept_close_reload'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="accept_close_reload_no" name="accept_close_reload_field" class="styled" value="false" <?php echo ( $the_options['accept_close_reload'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>

                        </table>

                        <h4><?php echo __('The Show Again Tab','cookie-law-info'); ?></h4>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="showagain_tab_field"><?php echo __('Use Show Again Tab?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="showagain_tab_field_yes" name="showagain_tab_field" class="styled" value="true" <?php echo ( $the_options['showagain_tab'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="showagain_tab_field_no" name="showagain_tab_field" class="styled" value="false" <?php echo ( $the_options['showagain_tab'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="notify_position_horizontal_field"><?php echo __('Tab Position','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="notify_position_horizontal_field" class="vvv_combobox">
    <?php
    if ($the_options['notify_position_horizontal'] == "right") {
        echo '<option value="right" selected="selected">';
        echo __('Right','cookie-law-info');
        echo '</option>';
        echo '<option value="left">';
        echo __('Left','cookie-law-info');
        echo '</option>';
    } else {
        echo '<option value="right">';
        echo __('Right','cookie-law-info');
        echo '</option>';
        echo '<option value="left" selected="selected">';
        echo __('Left','cookie-law-info');
        echo '</option>';
    }
    ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="showagain_x_position_field"><?php echo __('From Left Margin','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="showagain_x_position_field" value="<?php echo $the_options['showagain_x_position'] ?>" />
                                    <span class="cli-plugin-example">Specify px&nbsp;or&nbsp;&#37;, e.g. <em>"100px" or "30%"</em></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="showagain_text"><?php echo __('Show More Text','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="showagain_text_field" value="<?php echo $the_options['showagain_text'] ?>" />

                                </td>
                            </tr>
                        </table>

                    </dd>


                    <dt><a href="#"><?php echo __('Cookie Law Message Bar','cookie-law-info'); ?></a></dt>
                    <dd>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="notify_message_field"><?php echo __('Message','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<textarea name="notify_message_field" class="vvv_textbox">';
    echo apply_filters('format_to_edit', stripslashes($the_options['notify_message'])) . '</textarea>';
    ?>
                                    <span class="cli-plugin-example"><?php echo __('Shortcodes allowed: see settngs section "Using the Shortcodes".','cookie-law-info'); ?> <br /><em><?php echo __('Examples: "We use cookies on this website','cookie-law-info'); ?> [cookie_accept] <?php echo __('to find out how to delete cookies','cookie-law-info'); ?> [cookie_link]."</em></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="background_field"><?php echo __('Cookie Bar Colour','cookie-law-info'); ?></label></th>
                                <td>
                                    <?php
                                    /** RICHARDASHBY EDIT */
                                    //echo '<input type="text" name="background_field" id="cli-colour-background" value="' .$the_options['background']. '" />';
                                    echo '<input type="text" name="background_field" id="cli-colour-background" value="' . $the_options['background'] . '" class="my-color-field" data-default-color="#fff" />';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="text_field"><?php echo __('Text Colour','cookie-law-info'); ?></label></th>
                                <td>
                                    <?php
                                    /** RICHARDASHBY EDIT */
                                    echo '<input type="text" name="text_field" id="cli-colour-text" value="' . $the_options['text'] . '" class="my-color-field" data-default-color="#000" />';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="border_on_field"><?php echo __('Show Border?','cookie-law-info'); ?></label></th>
                                <td>
                                    <!-- Border on/off -->
                                    <input type="radio" id="border_on_field_yes" name="border_on_field" class="styled" value="true" <?php echo ( $the_options['border_on'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="border_on_field_no" name="border_on_field" class="styled" value="false" <?php echo ( $the_options['border_on'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="border_field"><?php echo __('Border Colour','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<input type="text" name="border_field" id="cli-colour-border" value="' . $the_options['border'] . '" class="my-color-field" />';
    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="font_family_field"><?php echo __('Font','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="font_family_field" class="vvv_combobox">
    <?php cookielawinfo_print_combobox_options(cookielawinfo_get_fonts(), $the_options['font_family']) ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </dd>


                    <dt><a href="#"><?php echo __('Customise Buttons','cookie-law-info'); ?></a></dt>
                    <dd>

                        <h3 style="margin-bottom:30px;"> <?php echo __('Accept Button','cookie-law-info'); ?> </h3>

                        <h4><?php echo __('Main Button','cookie-law-info'); ?> <code>[cookie_button]</code></h4>
                        <p><?php echo __('This button/link can be customised to either simply close the cookie bar, or follow a link. You can also customise the colours and styles, and show it as a link or a button.','cookie-law-info'); ?></p>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="button_1_text_field"><?php echo __('Link Text','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="button_1_text_field" value="<?php echo stripslashes($the_options['button_1_text']) ?>" />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_1_action_field"><?php echo __('Action','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="button_1_action_field" id="cli-plugin-button-1-action" class="vvv_combobox">
    <?php cookielawinfo_print_combobox_options(cookielawinfo_get_js_actions(), $the_options['button_1_action']) ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top" class="cli-plugin-row">
                                <th scope="row"><label for="button_1_url_field"><?php echo __('Link URL','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="button_1_url_field" id="button_1_url_field" value="<?php echo $the_options['button_1_url'] ?>" />
                                    <span class="cli-plugin-example"><em><?php echo __('Button will only link to URL if','cookie-law-info'); ?> Action = Show URL</em></span>
                                </td>
                            </tr>

                            <tr valign="top" class="cli-plugin-row">
                                <th scope="row"><label for="button_1_new_win_field"><?php echo __('Open link in new window?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="button_1_new_win_field_yes" name="button_1_new_win_field" class="styled" value="true" <?php echo ( $the_options['button_1_new_win'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="button_1_new_win_field_no" name="button_1_new_win_field" class="styled" value="false" <?php echo ( $the_options['button_1_new_win'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_1_link_colour_field"><?php echo __('Link colour','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<input type="text" name="button_1_link_colour_field" id="cli-colour-link-button-1" value="' . $the_options['button_1_link_colour'] . '" class="my-color-field" />';
    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_1_as_button_field"><?php echo __('Show as button?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="button_1_as_button_field_yes" name="button_1_as_button_field" class="styled" value="true" <?php echo ( $the_options['button_1_as_button'] == true ) ? ' checked="checked" />' : ' />'; ?> Button
                                           <input type="radio" id="button_1_as_button_field_no" name="button_1_as_button_field" class="styled" value="false" <?php echo ( $the_options['button_1_as_button'] == false ) ? ' checked="checked" />' : ' />'; ?> Link
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_1_button_colour_field"><?php echo __('Button colour','cookie-law-info'); ?></label></th>
                                <td>
                                    <?php
                                    echo '<input type="text" name="button_1_button_colour_field" id="cli-colour-btn-button-1" value="' . $the_options['button_1_button_colour'] . '" class="my-color-field" />';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_1_button_size_field"><?php echo __('Button Size','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="button_1_button_size_field" class="vvv_combobox">
    <?php cookielawinfo_print_combobox_options(cookielawinfo_get_button_sizes(), $the_options['button_1_button_size']); ?>
                                    </select>
                                </td>
                            </tr>
                        </table><!-- end custom button -->

                        <hr>

                        <h3 style="margin-top:50px;"> <?php echo __('Reject Button','cookie-law-info'); ?> </h3>

                        <table class="form-table" >
                            <tr valign="top">
                                <th scope="row"><label for="button_3_text_field"><?php echo __('Link Text','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="button_3_text_field" value="<?php echo stripslashes($the_options['button_3_text']) ?>" />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_3_action_field"><?php echo __('Action','cookie-law-info');?></label></th>
                                <td>
                                    <select name="button_3_action_field" id="cli-plugin-button-3-action" class="vvv_combobox">
    <?php
    $action_list['Close Header'] = '#cookie_action_close_header_reject';
    $action_list['Open URL'] = 'cookie_action_open_url_reject';
    cookielawinfo_print_combobox_options($action_list, $the_options['button_3_action'])
    ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top" class="cli-plugin-row">
                                <th scope="row"><label for="button_3_url_field"><?php echo __('Link URL','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="button_3_url_field" id="button_3_url_field" value="<?php echo $the_options['button_3_url'] ?>" />
                                    <span class="cli-plugin-example"><em><?php echo __('Button will only link to URL if ','cookie-law-info');?> Action = Show URL</em></span>
                                </td>
                            </tr>

                            <tr valign="top" class="cli-plugin-row">
                                <th scope="row"><label for="button_3_new_win_field"><?php echo __('Open link in new window?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="button_3_new_win_field_yes" name="button_3_new_win_field" class="styled" value="true" <?php echo ( $the_options['button_3_new_win'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="button_3_new_win_field_no" name="button_3_new_win_field" class="styled" value="false" <?php echo ( $the_options['button_3_new_win'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_3_link_colour_field"><?php echo __('Link colour','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<input type="text" name="button_3_link_colour_field" id="cli-colour-link-button-3" value="' . $the_options['button_3_link_colour'] . '" class="my-color-field" />';
    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_3_as_button_field"><?php echo __('Show as button?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="button_3_as_button_field_yes" name="button_3_as_button_field" class="styled" value="true" <?php echo ( $the_options['button_3_as_button'] == true ) ? ' checked="checked" />' : ' />'; ?> Button
                                           <input type="radio" id="button_3_as_button_field_no" name="button_3_as_button_field" class="styled" value="false" <?php echo ( $the_options['button_3_as_button'] == false ) ? ' checked="checked" />' : ' />'; ?> Link
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_3_button_colour_field"><?php echo __('Button colour','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<input type="text" name="button_3_button_colour_field" id="cli-colour-btn-button-3" value="' . $the_options['button_3_button_colour'] . '" class="my-color-field" />';
    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_3_button_size_field"><?php echo __('Button Size','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="button_3_button_size_field" class="vvv_combobox">
    <?php cookielawinfo_print_combobox_options(cookielawinfo_get_button_sizes(), $the_options['button_3_button_size']); ?>
                                    </select>
                                </td>
                            </tr>
                        </table><!-- end custom button -->

                        <hr>
                        <h4><?php echo __('Read More Link','cookie-law-info'); ?> <code>[cookie_link]</code></h4>
                        <p><?php echo __('This button/link can be used to provide a link out to your Privacy & Cookie Policy. You can customise it any way you like.','cookie-law-info'); ?></p>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label for="button_2_text_field"><?php echo __('Link Text','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="button_2_text_field" value="<?php echo stripslashes($the_options['button_2_text']) ?>" />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_2_url_field"><?php echo __('Link URL','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="text" name="button_2_url_field" id="button_2_url_field" value="<?php echo $the_options['button_2_url'] ?>" />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_2_link_colour_field"><?php echo __('Link colour','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<input type="text" name="button_2_link_colour_field" id="cli-colour-link-button-1" value="' . $the_options['button_2_link_colour'] . '" class="my-color-field" />';
    ?>
                                </td>
                            </tr>


                            <tr valign="top">
                                <th scope="row"><label for="button_2_new_win_field"><?php echo __('Open link in new window?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="button_2_new_win_field_yes" name="button_2_new_win_field" class="styled" value="true" <?php echo ( $the_options['button_2_new_win'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                           <input type="radio" id="button_2_new_win_field_no" name="button_2_new_win_field" class="styled" value="false" <?php echo ( $the_options['button_2_new_win'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_2_as_button_field"><?php echo __('Show as button?','cookie-law-info'); ?></label></th>
                                <td>
                                    <input type="radio" id="button_2_as_button_field_yes" name="button_2_as_button_field" class="styled" value="true" <?php echo ( $the_options['button_2_as_button'] == true ) ? ' checked="checked" />' : ' />'; ?> Button
                                           <input type="radio" id="button_2_as_button_field_no" name="button_2_as_button_field" class="styled" value="false" <?php echo ( $the_options['button_2_as_button'] == false ) ? ' checked="checked" />' : ' />'; ?> Link
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_2_button_colour_field"><?php echo __('Button colour','cookie-law-info'); ?></label></th>
                                <td>
    <?php
    echo '<input type="text" name="button_2_button_colour_field" id="cli-colour-btn-button-1" value="' . $the_options['button_2_button_colour'] . '" class="my-color-field" />';
    ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="button_2_button_size_field"><?php echo __('Button Size','cookie-law-info'); ?></label></th>
                                <td>
                                    <select name="button_2_button_size_field" class="vvv_combobox">
    <?php cookielawinfo_print_combobox_options(cookielawinfo_get_button_sizes(), $the_options['button_2_button_size']); ?>
                                    </select>
                                </td>
                            </tr>
                        </table><!-- end custom button -->

                    </dd>

                    <dt><a href="#"><?php echo __('Using the Shortcodes','cookie-law-info'); ?></a></dt>
                    <dd class="cli-help">
                        <h4><?php echo __('Cookie bar shortcodes','cookie-law-info'); ?></h4>
                        <p><?php echo __('You can enter the shortcodes in the','cookie-law-info'); ?> "message" <?php echo __('field of the Cookie Law Info bar. They add nicely formatted buttons and/or links into the cookie bar, without you having to add any HTML','cookie-law-info'); ?>.</p>
                        <p>The shortcodes are:</p>

                        <pre>[cookie_accept]</pre><span><?php echo __('If you just want a standard green','cookie-law-info'); ?> "Accept" <?php echo __("button that closes the header and nothing more, use this shortcode. It is already styled, you don't need to customise it.","cookie-law-info"); ?></span>

                        <pre>[cookie_accept colour="red"]</pre><span><?php echo __('Alternatively you can add a colour value. Choose from: red, blue, orange, yellow, green or pink.','cookie-law-info'); ?><br /><em><?php echo __('Careful to use the British spelling of ','cookie-law-info'); ?>"colour" <?php echo __('for the attribute.','cookie-law-info'); ?></em></span>

                        <pre>[cookie_button]</pre><span><?php echo __('This is the','cookie-law-info'); ?> "main button" <?php echo __('you customise above.','cookie-law-info'); ?></span>

                        <pre>[cookie_link]</pre><span><?php echo __('This is the','cookie-law-info'); ?> "read more" <?php echo __('link you customise above.','cookie-law-info'); ?></span>

                        <h4><?php echo __('Other shortcodes','cookie-law-info'); ?></h4>
                        <p><?php echo __('These shortcodes can be used in pages and posts on your website. It is not recommended to use these inside the cookie bar itself.','cookie-law-info'); ?></p>

                        <pre>[cookie_audit]</pre><span><?php echo __('This prints out a nice table of cookies, in line with the guidance given by the ICO.','cookie-law-info'); ?> <em><?php echo __('You need to enter the cookies your website uses via the Cookie Law Info menu in your WordPress dashboard.','cookie-law-info'); ?></em></span>

                        <pre>[delete_cookies]</pre><span><?php echo __('This shortcode will display a normal HTML link which when clicked, will delete the cookie set by Cookie Law Info (this cookie is used to remember that the cookie bar is closed).','cookie-law-info'); ?></span>
                        <pre>[delete_cookies text="Click here to delete"]</pre><span><?php echo __('Add any text you like- useful if you want e.g. another language to English.','cookie-law-info'); ?></span>


                    </dd>


                    <dt><a href="#"><?php echo __('Advanced','cookie-law-info'); ?></a></dt>
                    <dd>
                        <p><?php echo __('Sometimes themes apply settings that clash with plugins. If that happens, try adjusting these settings.','cookie-law-info'); ?></p>

                        <table class="form-table">
                            <!--
                            <tr valign="top">
                                    <th scope="row"><label for="use_colour_picker_field">Use colour picker on this page?</label></th>
                                    <td>
                                            <input type="radio" id="use_colour_picker_field_yes" name="use_colour_picker_field" class="styled" value="true" <?php echo ( $the_options['use_colour_picker'] == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                                            <input type="radio" id="use_colour_picker_field_no" name="use_colour_picker_field" class="styled" value="false" <?php echo ( $the_options['use_colour_picker'] == false ) ? ' checked="checked" />' : ' />'; ?> No
                                            <span class="cli-plugin-example"><em>You will need to refresh your browser once the page re-loads in order to show the colour pickers.</em></span>
                                    </td>
                            </tr>
                            -->
                            <tr valign="top">
                                <th scope="row"><?php echo __('Reset all values','cookie-law-info'); ?></th>
                                <td>
                                    <input type="submit" name="delete_all_settings" value="Delete settings and reset" class="button-secondary" onclick="return confirm('Are you sure you want to delete all your settings?');" />
                                    <span class="cli-plugin-example"><em><?php echo __('Warning: this will actually delete your current settings.','cookie-law-info'); ?></em></span>
                                </td>
                            </tr>
                            <!--
                            <tr valign="top">
                                    <th scope="row">Revert to previous version's settings</th>
                                    <td>
                                            <input type="submit" name="revert_to_previous_settings" value="Revert to old settings" class="button-secondary" onclick="return confirm('You will lose your current settings. Are you sure?');" />
                                            <span class="cli-plugin-example"><em>Warning: this will actually delete your current settings.</em></span>
                                    </td>
                            </tr>
                            -->
                        </table>

                    </dd>

                </dl><!-- end of cookielawinfo-accordion -->


                <!-- Second save button -->
                <div class="cli-plugin-toolbar bottom">
                    <div class="left">

                    </div>
                    <div class="right">
                        <input type="submit" name="update_admin_settings_form" value="Update Settings" class="button-primary" />
                    </div>
                </div>


                </form><!-- end of main settings form -->


            </div><!-- end of pad-5 -->
        </div><!-- end of cli-plugin-left-col (62%) -->

        <!-- Dashboard Sidebar -->
        <div class="cli-plugin-right-col width-38">
            <div class="pad-10">


                <div id="cli-plugin-migrate">
                    <h3><?php echo __('Where did my settings go?','cookie-law-info'); ?></h3>
                    <p><?php echo __('Cookie Law Info version 0.9 has been updated and has new settings.','cookie-law-info'); ?> <strong><?php echo __('Your previous settings are safe.','cookie-law-info'); ?></strong></p>
                    <p><?php echo __('You can either copy over your old settings to this version, or use the new default values.','cookie-law-info'); ?> </p>
                    <form method="post" action="<?php esc_url($_SERVER["REQUEST_URI"]) ?>">
                        <p><label for="cli-migration"><?php echo __('Would you like to:','cookie-law-info'); ?></label></p>
                        <ul>
                            <li><input type="radio" id="cli-migration_field_yes" name="cli-migration_field" class="styled" value="2" /> <?php echo __('Use previous settings','cookie-law-info'); ?></li>
                            <li><input type="radio" id="cli-migration_field_yes" name="cli-migration_field" class="styled" value="3" checked="checked" /> <?php echo __('Start afresh with the new version','cookie-law-info'); ?></li>
                        </ul>
                        <input type="submit" name="cli-migration-button" value="Update" class="button-secondary" onclick="return confirm('Are you sure you want to migrate settings?');" />
                    </form>
                    <p><?php echo __('If you want to go back to the previous version you can always download it again from','cookie-law-info'); ?> <a href="http://www.cookielawinfo.com">CookieLawInfo.com.</a></p>
                </div>

                <h3><?php echo __('GDPR Cookie Consent Pro','cookie-law-info'); ?></h3>
                <p>
                    <ul>
                        <li><?php echo __('*  Manage list of cookies ( Name, CookieID, Description, Duration, Type, Category, Header Script, Footer Script)','cookie-law-info'); ?></li>
                        <li><?php echo __('*  Manage Cookie Categories','cookie-law-info'); ?></li>
                        <li><?php echo __('*  Allow to display Cookie Settings popup where site visitors can opt-in or give consent to Cookie Categories','cookie-law-info'); ?></li>
                        <li><?php echo __("*  Fully customisable to look just like your own website's style: customise the colours, styles and fonts","cookie-law-info"); ?></li>
                        <li><?php echo __('*  Put the cookie bar in either the header or the footer','cookie-law-info'); ?></li>
                        <li><?php echo __('*  (Optional) accept cookie policy if the user scrolls','cookie-law-info'); ?></li>
                        <li><?php echo __('*  (Optional) automatically close the cookie bar after a delay (delay is configurable)','cookie-law-info'); ?></li>
                        <li><?php echo __('*  (Optional) cookie bar can be permanently dismissed or accessible through a "show again" tab','cookie-law-info');?></li>
                        <li><?php echo __('*  "Cookie Audit" shortcode to construct a nicely-styled "Privacy & Cookie Policy"','cookie-law-info'); ?></li>
                        <li><?php echo __('*  qTranslate support','cookie-law-info'); ?></li>
                    </ul><br/>
                <a href="https://www.webtoffee.com/product/gdpr-cookie-consent/" target="_blank" class="cli-button cli-button-go-pro"><?php echo __('Upgrade to GDPR Pro','cookie-law-info'); ?></a>
                <style>
                        .cli-button-go-pro {
                            box-shadow: none;
                            border: 0;
                            text-shadow: none;
                            padding: 10px 15px;
                            height: auto;
                            font-size: 18px;
                            border-radius: 4px;
                            font-weight: 600;
                            background: #00cb95;
                            margin-top: 20px;
                        }

                        .cli-button {
                            margin-bottom: 20px;
                            color: #fff;
                        }
                        .cli-button:hover, .cli-button:visited {
                            color: #fff;
                        }
                    </style>
                
                </p>
                <br />

                <h3><?php echo __('Like this plugin?','cookie-law-info'); ?></h3>
                <p><?php echo __('If you find this plugin useful please show your support and rate it','cookie-law-info'); ?> <a href="http://wordpress.org/support/view/plugin-reviews/cookie-law-info?filter=5" target="_blank">★★★★★</a><?php echo __(' on','cookie-law-info'); ?> <a href="http://wordpress.org/support/view/plugin-reviews/cookie-law-info?filter=5" target="_blank">WordPress.org</a> -<?php echo __('  much appreciated!','cookie-law-info'); ?> :)</p>
                <br />

                <h3><?php echo __('Help','cookie-law-info'); ?></h3>
                <ul>
                    <li><a href="http://cookielawinfo.com/support/"><?php echo __('Help and Support','cookie-law-info'); ?></a></li>
                    <li><a href="http://wordpress.org/support/plugin/cookie-law-info/"><?php echo __('Report a Bug','cookie-law-info'); ?></a></li>
                    <li><a href="http://cookielawinfo.com/contact/"><?php echo __('Suggest a Feature','cookie-law-info'); ?></a></li>
                    <li><a href="http://cookielawinfo.com"><?php echo __('About the law','cookie-law-info'); ?></a></li>
                </ul>
                <br />

                <!--				<div>
                                                        <form action="http://cookielawinfo.us5.list-manage.com/subscribe/post?u=b32779d828ef2e37e68e1580d&amp;id=71af66b86e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                                                                <h3><label for="mce-EMAIL">Subscribe to our mailing list</label></h3>
                                                                <p>Occasional updates on plugin updates, compliance requirements, who's doing what and industry best practice.</p>
                                                                <input type="email" value="" name="EMAIL" class="vvv_textfield" id="mce-EMAIL" placeholder="email address" required>
                                                                <div class="">
                                                                        <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button-secondary">
                                                                </div>
                                                                <p>We will not send you spam or pass your details to 3rd Parties.</p>
                                                        </form>
                                                </div>-->
                <!--End mc_embed_signup-->

            </div>
        </div><!-- end of cli-plugin-right-col (38%) -->

    </div><!-- end of cli-plugin-container -->


    <script type="text/javascript">
        (function ($) {

            var allPanels = $('.accordion > dd').hide();
            $('#accordion_default').show();

            $('.accordion > dt > a').click(function () {
                allPanels.slideUp();
                $(this).parent().next().slideDown();
                return false;
            });

        })(jQuery);
    </script>


    <?php
    if (!cookielawinfo_has_migrated()) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#cli-plugin-migrate').slideDown();
            });
        </script>
        <?php
    }

    //DEBUG:
    echo cookielawinfo_debug_admin_settings(true);
    echo cookielawinfo_debug_echo(cookielawinfo_get_json_settings());

    echo '</div><!-- end wrap -->';
}

function cookielawinfo_print_thirdparty_page() {
    ?>

    <h2><?php echo __('Non-necessary Cookie Settings','cookie-law-info'); ?></h2>
    <?php
    $options = array('thirdparty_on_field',
        'thirdparty_head_section',
        'thirdparty_body_section',
//            'thirdparty_footer_section',
    );
    // Get options:
    $stored_options = get_option('cookielawinfo_thirdparty_settings', array(
        'thirdparty_on_field' => false,
        'thirdparty_head_section' => '',
        'thirdparty_body_section' => '',
//            'thirdparty_footer_section' => '',
    ));

    // Check if form has been set:
    if (isset($_POST['update_thirdparty_settings_form'])) {

        // Check nonce:
        check_admin_referer('cookielawinfo-update-thirdparty');

        foreach ($options as $key) {

            if (isset($_POST[$key])) {

                // Store sanitised values only:

                $stored_options[$key] = wp_unslash($_POST[$key]);
            }
        }

        update_option('cookielawinfo_thirdparty_settings', $stored_options);
        echo '<div class="updated"><p><strong>';
        echo __('Settings Updated.','cookie-law-info');
        echo '</strong></p></div>';
    }

    $stored_options = get_option('cookielawinfo_thirdparty_settings', array(
        'thirdparty_on_field' => false,
        'thirdparty_head_section' => '',
        'thirdparty_body_section' => '',
//            'thirdparty_footer_section' => '',
    ));
    ?>

    <style>
        .vvv_textbox{
            height: 150px;
            width: 80%;
        }
    </style>
    <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>">
    <?php wp_nonce_field('cookielawinfo-update-thirdparty'); ?>
        <table class="form-table" >

            <tr valign="top">
                <th scope="row" style="width:25%;"><label for="thirdparty_on_field"><?php echo __('Enable Non-necessary Cookie','cookie-law-info'); ?></label></th>
                <td>
                    <!-- Border on/off -->
                    <input type="radio" id="thirdparty_on_field_yes" name="thirdparty_on_field" class="styled" value="true" <?php echo ( filter_var($stored_options['thirdparty_on_field'], FILTER_VALIDATE_BOOLEAN) == true ) ? ' checked="checked" />' : ' />'; ?> Yes
                           <input type="radio" id="thirdparty_on_field_no" name="thirdparty_on_field" class="styled" value="false" <?php echo ( filter_var($stored_options['thirdparty_on_field'], FILTER_VALIDATE_BOOLEAN) == false ) ? ' checked="checked" />' : ' />'; ?> No
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:25%;"><label for="thirdparty_head_section"><?php echo __('This script will be added to the page HEAD section if the above settings is enabled and user has give consent.','cookie-law-info');?></label></th>
                <td>
    <?php
    echo '<textarea name="thirdparty_head_section" class="vvv_textbox">';
    echo apply_filters('format_to_edit', stripslashes($stored_options['thirdparty_head_section'])) . '</textarea>';
    ?>
                    <div class="clearfix"></div>
                    <span class="cli-plugin-example"><em><?php echo __('Print scripts in the head tag on the front end if above cookie settings is enabled and user has given consent.','cookie-law-info'); ?> eg:- &lt;script&gt;console.log("header script");&lt;/script&gt ?></em></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:25%;"><label for="thirdparty_body_section"><?php echo __('This script will be added right after the BODY section if the above settings is enabled and user has given consent.','cookie-law-info'); ?></label></th>
                <td>
    <?php
    echo '<textarea name="thirdparty_body_section" class="vvv_textbox">';
    echo apply_filters('format_to_edit', stripslashes($stored_options['thirdparty_body_section'])) . '</textarea>';
    ?>
                    <div class="clearfix"></div>
                    <span class="cli-plugin-example"><em><?php echo __('Print scripts before the closing body tag on the front end if above cookie settings is enabled and user has given consent.','cookie-law-info'); ?> eg:- &lt;script&gt;console.log("body script");&lt;/script&gt; </em></span>
                </td>
            </tr>
    <!--						<tr valign="top">
                    <th scope="row"><label for="thirdparty_footer_section">Thi script will be added to the page FOOTER section if user enables this cookie.</label></th>
                    <td>
    <?php
    //echo '<textarea name="thirdparty_footer_section" class="vvv_textbox">';
    //echo apply_filters('format_to_edit', stripslashes($stored_options['thirdparty_footer_section'])) . '</textarea>';
    ?>
                        <div class="clearfix"></div>
                            <span class="cli-plugin-example"><?php echo __('Shortcodes allowed: see settings section "Using the Shortcodes".','cookie-law-info'); ?> <br /><em>Examples: "We use cookies on this website [cookie_accept] to find out how to delete cookies [cookie_link]."','cookie-law-info')</em></span>
                    </td>
            </tr>-->

        </table>
        <input type="submit" name="update_thirdparty_settings_form" value="Save Settings" class="button-primary" />
    </form>


    <?php
}
?>