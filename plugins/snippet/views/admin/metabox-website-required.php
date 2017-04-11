<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-WEBSITE-REQUIRED.PHP
// -----------------------------------------------------------------------------
// Required website settings.
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
  <?php _e( 'This below field is required to properly generate the Schema information for your website. ', '__x__' ); ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_website_url'; ?>">
        <strong><?php _e( 'Website URL', '__x__' ); ?></strong>
        <span><?php _e( 'URL address of your website - default to "WordPress Address (URL)" on Settings -> General Settings.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="url" class="large-text" name="<?php echo $plugin_slug; ?>[website_url]"
      id="<?php echo $plugin_slug . '_website_url'; ?>"
      value="<?php echo esc_attr( $website_url ); ?>">
    </td>
  </tr>

</table>
