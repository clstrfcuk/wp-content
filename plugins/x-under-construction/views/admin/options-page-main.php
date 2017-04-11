<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE-MAIN.PHP
// -----------------------------------------------------------------------------
// Plugin options page main content.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Main Content
// =============================================================================

// Main Content
// =============================================================================

?>

<div id="post-body-content">
  <div class="meta-box-sortables ui-sortable">

    <!--
    ENABLE
    -->

    <div id="meta-box-enable" class="postbox">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Enable', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select the checkbox below to enable the plugin.', '__x__' ); ?></p>
        <table class="form-table">
          <tr>
            <th>
              <label for="x_under_construction_enable">
                <strong><?php _e( 'Enable Under Construction', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_under_construction_enable" id="x_under_construction_enable" value="1" <?php echo ( isset( $x_under_construction_enable ) && checked( $x_under_construction_enable, '1', false ) ) ? checked( $x_under_construction_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <!--
    CUSTOM PAGE SETTINGS
    -->

    <div id="meta-box-custom-settings" class="postbox" style="display: <?php echo ( isset( $x_under_construction_enable ) && $x_under_construction_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Custom Under Construction Page Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select if you want a custom page or not.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_under_construction_use_custom">
                <strong><?php _e( 'Use a custom Under Construction Page', '__x__' ); ?></strong>
                <span><?php _e( 'Select to choose a custom page instead of configure options on this plugin.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_under_construction_use_custom" id="x_under_construction_use_custom" value="1" <?php echo ( isset( $x_under_construction_use_custom ) && checked( $x_under_construction_use_custom, '1', false ) ) ? checked( $x_under_construction_use_custom, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

          <tr id="x_under_construction_custom_row">
            <th>
              <label for="x_under_construction_custom">
                <strong><?php _e( 'Custom Under Construction Page', '__x__' ); ?></strong>
                <span><?php _e( 'Select the page to be used in place of your site\'s standard under construction page.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <select name="x_under_construction_custom" id="x_under_construction_custom">
                <?php
                foreach ( $x_under_construction_list_entries_master as $key => $value ) {
                  if ( isset( $x_under_construction_custom ) && selected( $x_under_construction_custom, $key, false ) ) {
                    $selected = ' selected="selected"';
                  } else {
                    $selected = '';
                  }
                  echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                }
                ?>
              </select>
            </td>
          </tr>

        </table>
      </div>
    </div>


    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $x_under_construction_enable ) && $x_under_construction_enable == 1 &&  isset( $x_under_construction_use_custom ) && $x_under_construction_use_custom == 0 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_under_construction_heading">
                <strong><?php _e( 'Heading', '__x__' ); ?></strong>
                <span><?php _e( 'Enter your desired heading.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_heading" id="x_under_construction_heading" type="text" value="<?php echo ( isset( $x_under_construction_heading ) ) ? stripslashes( $x_under_construction_heading ) : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_subheading">
                <strong><?php _e( 'Subheading', '__x__' ); ?></strong>
                <span><?php _e( 'Enter your desired subheading.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_subheading" id="x_under_construction_subheading" type="text" value="<?php echo ( isset( $x_under_construction_subheading ) ) ? stripslashes( $x_under_construction_subheading ) : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_extra_text">
                <strong><?php _e( 'Extra Text', '__x__' ); ?></strong>
                <span><?php _e( 'Enter extra text to render below subheading. HTML not allowed, lines breaks will be preserved, rendered under &lt;p&gt; tag. Shortcodes allowed here, so you can add a form if you want.', '__x__' ); ?></span>
              </label>
            </th>
            <td><textarea name="x_under_construction_extra_text" id="x_under_construction_extra_text" class="large-text"><?php echo ( isset( $x_under_construction_extra_text ) ) ? stripslashes( $x_under_construction_extra_text ) : ''; ?></textarea></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_date">
                <strong><?php _e( 'Completed By', '__x__' ); ?></strong>
                <span><?php _e( 'Set the date when maintenance is expected to be complete.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_date" id="x_under_construction_date" type="text" value="<?php echo ( isset( $x_under_construction_date ) ) ? $x_under_construction_date : ''; ?>" class="large-text datepicker"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_background_image">
                <strong><?php _e( 'Background Image', '__x__' ); ?></strong>
                <span><?php _e( 'Optionally set a background image.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <input type="text" class="file large-text" name="x_under_construction_background_image" id="x_under_construction_background_image" value="<?php echo ( isset( $x_under_construction_background_image ) ) ? $x_under_construction_background_image : ''; ?>">
              <input type="button" id="_x_under_construction_background_image_image_upload_btn" data-id="x_under_construction_background_image" class="button-secondary x-upload-btn-uc" value="Upload Image">
              <div class="x-meta-box-img-thumb-wrap" id="_x_under_construction_background_image_thumb">
                  <?php if ( isset( $x_under_construction_background_image ) && ! empty( $x_under_construction_background_image ) ) : ?>
                     <div class="x-uploader-image"><img src="<?php echo $x_under_construction_background_image ?>" alt="" /></div>
                  <?php endif ?>
              </div>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_logo_image">
                <strong><?php _e( 'Logo Image', '__x__' ); ?></strong>
                <span><?php _e( 'Optionally set a logo image.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <input type="text" class="file large-text" name="x_under_construction_logo_image" id="x_under_construction_logo_image" value="<?php echo ( isset( $x_under_construction_logo_image ) ) ? $x_under_construction_logo_image : ''; ?>">
              <input type="button" id="_x_under_construction_logo_image_image_upload_btn" data-id="x_under_construction_logo_image" class="button-secondary x-upload-btn-uc" value="Upload Image">
              <div class="x-meta-box-img-thumb-wrap" id="_x_under_construction_logo_image_thumb">
                  <?php if ( isset( $x_under_construction_logo_image ) && ! empty( $x_under_construction_logo_image ) ) : ?>
                     <div class="x-uploader-image"><img src="<?php echo $x_under_construction_logo_image ?>" alt="" /></div>
                  <?php endif ?>
              </div>
            </td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_background_color">
                <strong><?php _e( 'Background', '__x__' ); ?></strong>
                <span><?php _e( 'Select your color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_background_color" id="x_under_construction_background_color" type="text" value="<?php echo ( isset( $x_under_construction_background_color ) ) ? $x_under_construction_background_color : '#34495e'; ?>" class="wp-color-picker" data-default-color="#34495e"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_heading_color">
                <strong><?php _e( 'Headings', '__x__' ); ?></strong>
                <span><?php _e( 'Select your color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_heading_color" id="x_under_construction_heading_color" type="text" value="<?php echo ( isset( $x_under_construction_heading_color ) ) ? $x_under_construction_heading_color : '#ffffff'; ?>" class="wp-color-picker" data-default-color="#ffffff"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_subheading_color">
                <strong><?php _e( 'Subheading', '__x__' ); ?></strong>
                <span><?php _e( 'Select your color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_subheading_color" id="x_under_construction_subheading_color" type="text" value="<?php echo ( isset( $x_under_construction_subheading_color ) ) ? $x_under_construction_subheading_color : '#ffffff'; ?>" class="wp-color-picker" data-default-color="#ffffff"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_date_color">
                <strong><?php _e( 'Completed By', '__x__' ); ?></strong>
                <span><?php _e( 'Select your color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_date_color" id="x_under_construction_date_color" type="text" value="<?php echo ( isset( $x_under_construction_date_color ) ) ? $x_under_construction_date_color : '#ffffff'; ?>" class="wp-color-picker" data-default-color="#ffffff"></td>
          </tr>

          <tr>
            <th>
              <label for="x_under_construction_social_color">
                <strong><?php _e( 'Social Profile Links', '__x__' ); ?></strong>
                <span><?php _e( 'Select your color.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_social_color" id="x_under_construction_social_color" type="text" value="<?php echo ( isset( $x_under_construction_social_color ) ) ? $x_under_construction_social_color : '#ffffff'; ?>" class="wp-color-picker" data-default-color="#ffffff"></td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    WHITE LIST SETTINGS
    -->

    <div id="meta-box-whitelist-settings" class="postbox" style="display: <?php echo ( isset( $x_under_construction_enable ) && $x_under_construction_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'White List Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Define some hosts to bypass Under Construction settings.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_under_construction_whitelist">
                <strong><?php _e( 'White List', '__x__' ); ?></strong>
                <span><?php _e( 'Any access from IPs on this list (space separated) will ignore Under Construction settings and access the website normally.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_under_construction_whitelist" id="x_under_construction_whitelist" type="text" value="<?php echo ( isset( $x_under_construction_whitelist ) ) ? $x_under_construction_whitelist : ''; ?>" placeholder="127.0.0.1 192.168.1.100"  class="large-text"></td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SOCIAL MEDIA SETTINGS
    -->

    <div id="meta-box-social-settings" class="postbox" style="display: <?php echo ( isset( $x_under_construction_enable ) && $x_under_construction_enable == 1 &&  isset( $x_under_construction_use_custom ) && $x_under_construction_use_custom == 0 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Social media settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Fill your social media URLs bellow.', '__x__' ); ?></p>
        <table class="form-table">

          <?php
          foreach ( $social_medias as $key => $sc ) :
            $key = "x_under_construction_{$key}";
            $sc_value = $$key;
          ?>
          <tr>
            <th>
              <label for="x_under_construction_facebook">
                <strong><?php echo sprintf( __( '%s Profile', '__x__' ), $sc['title'] ); ?></strong>
                <span><?php echo sprintf( __( 'Enter the URL to your %s profile', '__x__' ), $sc['title'] ); ?></span>
              </label>
            </th>
            <td><input name="<?php echo $key ?>" id="<?php echo $key ?>" type="text" value="<?php echo ( isset( $sc_value ) ) ? $sc_value : ''; ?>" class="large-text"></td>
          </tr>
        <?php endforeach; ?>

        </table>
      </div>
    </div>

  </div>
</div>
