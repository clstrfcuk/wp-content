<?php

// =============================================================================
// EMAIL-CONVERTKIT/VIEWS/ADMIN/METABOX-LISTS.PHP
// -----------------------------------------------------------------------------
// Provider email lists.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Metabox
// =============================================================================

// Metabox
// =============================================================================

?>

<p><?php _e( 'Below are the email forms available from your account.', '__x__' ); ?></p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_ck_list_cache'; ?>">
        <strong><?php _e( 'Email Forms', '__x__' ); ?></strong>
        <span><?php _e( 'If you have added a new email form or custom field to your account, click the "Refresh" button to see it listed here.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select multiple disabled>
        <?php if ( empty( $ck_list_cache ) ) : ?>
          <option><?php _e( 'No lists found', '__x__' ); ?></option>
        <?php else : ?>
          <?php foreach ( $ck_list_cache as $item ) : ?>
            <option><?php echo $item['name']; ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
      <br>
      <br>
      <a href="<?php echo $ck_list_refresh_url; ?>" class="button"><?php _e( 'Refresh', '__x__' ); ?></a>
    </td>
  </tr>

</table>
