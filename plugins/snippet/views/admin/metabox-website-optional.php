<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-WEBSITE-OPTIONAL.PHP
// -----------------------------------------------------------------------------
// Optional website settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Metabox
// =============================================================================

// Metabox
// =============================================================================

?>

<p>
  <?php _e( 'The below fields are optional, you don’t need to fill in them, but if you do, your website will have more useful information within search results', '__x__' ); ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_website_name'; ?>">
        <strong><?php _e( 'Site Name', '__x__' ); ?></strong>
        <span><?php _e( 'Name of your website - default to "Site title" on Settings -> General Settings.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text" name="<?php echo $plugin_slug; ?>[website_name]"
      id="<?php echo $plugin_slug . '_website_name'; ?>"
      value="<?php echo esc_attr( $website_name ); ?>">
    </td>
  </tr>


  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_alternate_name'; ?>">
        <strong><?php _e( 'Site Alternate Name', '__x__' ); ?></strong>
        <span><?php _e( 'Alternate name of your website - extra name to enhace searching.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text" name="<?php echo $plugin_slug; ?>[website_alternate_name]"
      id="<?php echo $plugin_slug . '_alternate_name'; ?>"
      value="<?php echo esc_attr( $website_alternate_name ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_website_sitelinks'; ?>">
        <strong><?php _e( 'Enable Site Link Search Box', '__x__' ); ?></strong>
        <span><?php _e( 'Redirects refined searchs on Google to Wordpress query (To see if your site is eligible, check Google Webmaster Tools).', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <fieldset>
        <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
        <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[website_sitelinks]" <?php echo checked( ( $website_sitelinks == 'yes' ) ); ?> value="yes"> <span><?php _e( 'Yes', '__x__' ); ?></span></label><br>
        <label class="radio-label"><input type="radio" class="radio" name="<?php echo $plugin_slug; ?>[website_sitelinks]" <?php echo checked( ( $website_sitelinks == 'no' ) ); ?> value="no"> <span><?php _e( 'No', '__x__' ); ?></span></label>
      </fieldset>
    </td>
  </tr>

</table>
