<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-ADDRESS.PHP
// -----------------------------------------------------------------------------
// General email integration settings.
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
  <?php _e( 'Enter your organization\'s address below. All fields are required to properly generate the Schema markup for search engines.', '__x__' ); ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_address_street_address'; ?>">
        <strong><?php _e( 'Street Address', '__x__' ); ?></strong>
        <span><?php _e( 'Like "214 Boulevard".', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text" name="<?php echo $plugin_slug; ?>[address_street_address]"
      id="<?php echo $plugin_slug . '_address_street_address'; ?>"
      value="<?php echo esc_attr( $address_street_address ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_address_locality'; ?>">
        <strong><?php _e( 'City', '__x__' ); ?></strong>
        <span><?php _e( 'City - like "Los Angeles", "Chicago".', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text" name="<?php echo $plugin_slug; ?>[address_locality]"
      id="<?php echo $plugin_slug . '_address_locality'; ?>"
      value="<?php echo esc_attr( $address_locality ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_address_region'; ?>">
        <strong><?php _e( 'State/County', '__x__' ); ?></strong>
        <span><?php _e( 'Such as Florida or Kent.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="small-text" name="<?php echo $plugin_slug; ?>[address_region]"
      id="<?php echo $plugin_slug . '_address_region'; ?>"
      value="<?php echo esc_attr( $address_region ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_address_postal_code'; ?>">
        <strong><?php _e( 'Zip/Postal Code', '__x__' ); ?></strong>
        <span><?php _e( 'Zip Code or Postal Code in the format of your country', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="medium-text" name="<?php echo $plugin_slug; ?>[address_postal_code]"
      id="<?php echo $plugin_slug . '_address_postal_code'; ?>"
      value="<?php echo esc_attr( $address_postal_code ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_address_country'; ?>">
        <strong><?php _e( 'Country', '__x__' ); ?></strong>
        <span><?php _e( 'The country name - select in the list.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" name="<?php echo $plugin_slug; ?>[address_country]" id="<?php echo $plugin_slug . '_address_country'; ?>">
        <?php if ( empty( $country_list ) ) : ?>
          <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
        <?php else : ?>
          <option value="" <?php echo ( '' == $address_country ) ? 'selected' : ''; ?>><?php echo _e( '-- Select a country --', '__x__' ); ?></option>
          <?php foreach ( $country_list as $value => $label ) : ?>
            <option value="<?php echo $value; ?>" <?php echo ( $value == $address_country ) ? 'selected' : ''; ?>>
              <?php echo $label; ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </td>
  </tr>

</table>
