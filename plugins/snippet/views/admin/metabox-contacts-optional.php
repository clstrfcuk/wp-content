<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-CONTACT-OPTIONAL.PHP
// -----------------------------------------------------------------------------
// Optional contact settings.
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
  <?php _e( 'Optional (and useful!) info about the main contact of your organization.', '__x__' ); ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_contact_option'; ?>">
        <strong><?php _e( 'Option', '__x__' ); ?></strong>
        <span><?php _e( 'Select if your telephone service supports the hearing impaired or toll free options.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" id="<?php echo $plugin_slug . '_contact_option'; ?>">
        <?php if ( empty( $contact_option_list ) ) : ?>
          <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
        <?php else : ?>
          <option value="" <?php echo ( '' == $contact_option ) ? 'selected' : ''; ?>><?php echo _e( '-- Select an option --', '__x__' ); ?></option>
          <?php foreach ( $contact_option_list as $value => $label ) : ?>
            <option value="<?php echo $value; ?>">
              <?php echo _e( $label, '__x__' ); ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_contact_area_served'; ?>">
        <strong><?php _e( 'Area Served', '__x__' ); ?></strong>
        <span><?php _e( 'Which area/country is supported by this contact.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" id="<?php echo $plugin_slug . '_contact_area_served'; ?>">
        <?php if ( empty( $country_list ) ) : ?>
          <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
        <?php else : ?>
          <option value="" <?php echo ( '' == $contact_area_served ) ? 'selected' : ''; ?>><?php echo _e( '-- Select a country --', '__x__' ); ?></option>
          <?php foreach ( $country_list as $value => $label ) : ?>
            <option value="<?php echo $value; ?>">
              <?php echo $label; ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_contact_available_language'; ?>">
        <strong><?php _e( 'Language', '__x__' ); ?></strong>
        <span><?php _e( 'Language supported by this contact.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" id="<?php echo $plugin_slug . '_contact_available_language'; ?>">
        <?php if ( empty( $language_list ) ) : ?>
          <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
        <?php else : ?>
          <option value="" <?php echo ( '' == $contact_available_language ) ? 'selected' : ''; ?>><?php echo _e( '-- Select a language --', '__x__' ); ?></option>
          <?php foreach ( $language_list as $value ) : ?>
            <option value="<?php echo $value; ?>">
              <?php echo _e( $value, '__x__' ); ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_contact_hours_available'; ?>">
        <strong><?php _e( 'Hours Available', '__x__' ); ?></strong>
        <span><?php _e( 'Enter what times this contact is available to be contacted by the public. Use a 24hr format.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <ul id="snippet_hours_list"></ul>
      <div id="snippet_hours_add_widget">
        from <input type="text" class="small-text snippet-time" id="snippet_hours_start" />
        to <input type="text" class="small-text snippet-time" id="snippet_hours_end" /> on:<br/>
        <input type="checkbox" class="snippet_hours_weekday" value="Mo" />Mon
        <input type="checkbox" class="snippet_hours_weekday" value="Tu" />Tue
        <input type="checkbox" class="snippet_hours_weekday" value="We" />Wed
        <input type="checkbox" class="snippet_hours_weekday" value="Th" />Thu
        <input type="checkbox" class="snippet_hours_weekday" value="Fr" />Fri
        <input type="checkbox" class="snippet_hours_weekday" value="Sa" />Sat
        <input type="checkbox" class="snippet_hours_weekday" value="Su" />Sun<br/>
        <input type="hidden" class="large-text" id="snippet_hours_id'; ?>" />
        <a href="#" id="snippet_hours_add">Add</a>
      </div>
     <input type="hidden" class="large-text" id="<?php echo $plugin_slug . '_contact_hours_available'; ?>" value="" />
    </td>
  </tr>

</table>

<hr/>
<div id="snippet-contact-save-div" style="display:none; clear:both; text-align: center">
  <input id="snippet-contact-save" class="button button-primary" style="text-align:center;" value="Update contact">
</div>
