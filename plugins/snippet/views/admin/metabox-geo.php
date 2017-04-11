<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-GEO.PHP
// -----------------------------------------------------------------------------
// Geo settings.
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
  <?php _e( 'The latitude and longitude of your organization for online maps. Google can get info from the address, but your organization won\'t be shown on maps unless you also fill in these co-ordinates.', '__x__' ); ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_geo_latitude'; ?>">
        <strong><?php _e( 'Latitude', '__x__' ); ?></strong>
        <span><?php _e( 'Inline help.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="medium-text" name="<?php echo $plugin_slug; ?>[geo_latitude]"
      id="<?php echo $plugin_slug . '_geo_latitude'; ?>"
      value="<?php echo esc_attr( $geo_latitude ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_geo_longitude'; ?>">
        <strong><?php _e( 'Longitude', '__x__' ); ?></strong>
        <span><?php _e( 'Inline help.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="medium-text" name="<?php echo $plugin_slug; ?>[geo_longitude]"
      id="<?php echo $plugin_slug . '_geo_longitude'; ?>"
      value="<?php echo esc_attr( $geo_longitude ); ?>">
    </td>
  </tr>

</table>
