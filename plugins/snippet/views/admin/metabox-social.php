<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-GENERAL.PHP
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
  <?php _e( 'Enter any Social Profiles you have in the fields below to be used by Google Search. ', '__x__' ); ?>
</p>

<table class="form-table">

  <?php if ( empty( $social_list ) ) : ?>
    <tr>
      <td>
        <?php _e( 'No Social List Found', '__x__' ); ?>
      </td>
    </tr>
  <?php else : ?>
    <?php foreach ($social_list as $field => $label) : ?>
      <tr>
        <th>
          <label for="<?php echo $plugin_slug . '_' . $field; ?>">
            <strong><?php echo $label; ?></strong>
            <span><?php echo sprintf(__( 'URL for your %s account.', '__x__' ), $label); ?></span>
          </label>
        </th>
        <td>
          <input type="text" class="large-text" name="<?php echo $plugin_slug; ?>[<?php echo $field; ?>]"
          id="<?php echo $plugin_slug . '_' . $field; ?>'; ?>"
          value="<?php echo esc_attr( $$field ); ?>">
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>

</table>
