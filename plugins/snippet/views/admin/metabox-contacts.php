<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-CONTACTS.PHP
// -----------------------------------------------------------------------------
// Required contact settings.
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
  <?php _e( 'At least one entry is required to properly generate the schema for the contact of your organization.', '__x__' ); ?>
   - <a href="#" id="snippet-contacts-add" class="button button-primary"><?php _e( 'Add', '__x__'); ?></a>
</p>

<table class="form-table" id="snippet-contacts-list">
  <thead>
    <tr>
      <tr>
        <th><strong><?php _e( 'Type', '__x__' ); ?></strong></th>
        <th><strong><?php _e( 'Telephone', '__x__' ); ?></strong></th>
        <th><strong><?php _e( 'Option', '__x__' ); ?></strong></th>
        <th><strong><?php _e( 'Area Served', '__x__' ); ?></strong></th>
        <th><strong><?php _e( 'Language', '__x__' ); ?></strong></th>
        <th><strong><?php _e( 'Action', '__x__' ); ?></strong></th>
      </tr>
  </thead>
  <tbody></tbody>
</table>

<script type="text/html" id="snippet-contacts-template">
  <tr>
    <td>{type}<input type="hidden" name="snippet[contacts][{id}][type]" value="{type}" /></td>
    <td>{telephone}<input type="hidden" name="snippet[contacts][{id}][telephone]" value="{telephone}" /></td>
    <td>{option}<input type="hidden" name="snippet[contacts][{id}][option]" value="{option}"" /></td>
    <td>{areaServed}<input type="hidden" name="snippet[contacts][{id}][areaServed]" value="{areaServed}" /></td>
    <td>{availableLanguage}<input type="hidden" name="snippet[contacts][{id}][availableLanguage]" value="{availableLanguage}" /></td>
    <td>{hoursAvailableDisplay}<input type="hidden" name="snippet[contacts][{id}][hoursAvailable]" value="{hoursAvailable}" /></td>
    <td>
      <a href="#" class="snippet-contact-edit" data-id="{id}"><?php _e( 'Edit', '__x__'); ?></a>
      <a href="#" class="snippet-contact-delete" data-id="{id}"><?php _e( 'Delete', '__x__'); ?></a>
    </td>
  </tr>
</script>
<script type="text/javascript">
var snippet_contacts = [
<?php if ( $contacts ) : ?>
  <?php foreach ( $contacts as $key => $contact) : ?>
    {
      <?php foreach( $contact as $field => $value ) : ?>
      <?php echo $field; ?>: "<?php echo esc_attr( $value ) ?>",
      <?php endforeach; ?>
    }<?php echo $key < count( $contacts ) ? ',' : ''; ?>
  <?php endforeach; ?>
<?php endif; ?>
];
var snippet_hours = [];
var snippet_hours_field = 'snippet_contact_hours_available';
</script>
