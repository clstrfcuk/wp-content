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
              <label for="x_olark_integration_enable">
                <strong><?php _e( 'Enable Olark Integration', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_olark_integration_enable" id="x_olark_integration_enable" value="1" <?php echo ( isset( $x_olark_integration_enable ) && checked( $x_olark_integration_enable, '1', false ) ) ? checked( $x_olark_integration_enable, '1', false ) : ''; ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $x_olark_integration_enable ) && $x_olark_integration_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_olark_integration_site_id">
                <strong><?php _e( 'Olark Site ID', '__x__' ); ?></strong>
                <span><?php _e( 'Enter your Olark Site ID.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_olark_integration_site_id" id="x_olark_integration_site_id" type="text" value="<?php echo ( isset( $x_olark_integration_site_id ) ) ? $x_olark_integration_site_id : ''; ?>" class="large-text"></td>
          </tr>

        </table>
      </div>
    </div>

  </div>
</div>