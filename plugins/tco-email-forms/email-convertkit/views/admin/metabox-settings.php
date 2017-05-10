<?php

// =============================================================================
// EMAIL-CONVERTKIT/VIEWS/ADMIN/METABOX-SETTINGS.PHP
// -----------------------------------------------------------------------------
// Provider email integration settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Metabox
// =============================================================================

// Metabox
// =============================================================================

?>

<?php if ( $ck_api_key ) : ?>
  <p><?php _e( 'Your site is now connected to your ConvertKit account!', '__x__' ); ?></p>
<?php else : ?>
  <p><?php _e( 'Your site is not yet linked to your ConvertKit account.', '__x__' ); ?></p>
<?php endif; ?>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_ck_api_key'; ?>">
        <strong><?php _e( 'API key', '__x__' ); ?></strong>
        <span>
        <?php if ( $ck_api_key ) : ?>
          <?php _e( 'Your API key is valid.', '__x__' ); ?>
        <?php else : ?>
          <?php _e( 'Enter your ConvertKit API key.', '__x__' ); ?>
        <?php endif; ?>
        </span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text<?php echo ( $ck_api_key ) ? ' x-input-success' : ''; ?>" name="<?php echo $plugin_slug; ?>[ck_api_key]" id="<?php echo $plugin_slug . '_ck_api_key'; ?>" value="<?php echo esc_attr( $ck_api_key ); ?>">
    </td>
  </tr>

</table>
