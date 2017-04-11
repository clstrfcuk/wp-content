<?php

// =============================================================================
// VIEWS/ADMIN/REQUIRED-FIELDS-ALERT.PHP
// -----------------------------------------------------------------------------
// Welcome page
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. REQUIRED fields alert
// =============================================================================

// Welcome
// =============================================================================

$tabs = array (
  'website' => array(
    'required' => array(
      'website_url'  => 'Website URL',
    ),
    'suggested' => array (
      'website_name' => 'Site Name',
    ),
  ),
  'organization' => array(
    'required' => array(
      'organization_type' => 'Type',
      'organization_name' => 'Name / Business Name',
      'organization_url'  => 'Website URL',
    ),
    'suggested' => array (
      'organization_logo'        => 'Logo URL',
      'organization_description' => 'Description',
    ),
  ),
  'address' => array(
    'required' => array(
      'address_street_address' => 'Street Address',
      'address_locality'       => 'City',
      'address_region'         => 'State/County',
      'address_postal_code'    => 'Zip/Postal Code',
      'address_country'        => 'Country',
    ),
    'suggested' => array (),
  ),
  'contacts' => array(
    'required' => array(),
    'suggested' => array (
      'contacts' => 'Add at least one contact'
    ),
  ),
);

$required  = array();
$suggested = array();

$data = get_option('snippet');

foreach ($tabs as $tab_key => $tab)  {
  foreach ($tab['required'] as $fieldname => $label) {
    if ( empty ( $data[ $fieldname ] ) ) {
      $required[ $tab_key ][ $fieldname ] = $label;
    }
    foreach ($tab['suggested'] as $fieldname => $label) {
      if ( empty ( $data[ $fieldname ] ) ) {
        $suggested[ $tab_key ][ $fieldname ] = $label;
      }
    }
  }
}

?>
<?php if ( count( $required ) || count( $suggested ) ) : ?>
    <div class="error">
        <?php if ( count( $required ) ) : ?>
        <p>
          Don’t forget to configure the plugin!
          You need to check the following settings to start
          generating Snippet for your site.
        </p>
        <ul>
          <?php foreach ( $required as $tab_key => $tab) : ?>
            <li><?php echo sprintf( __('Check <a href="admin.php?page=snippet&tab=%s">%s tab</a> for <strong>required</strong> fields', '__x__'), $tab_key, ucwords($tab_key) ) ?>: <strong><?php echo implode(', ', $tab) ?></strong></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if ( count( $suggested ) ) : ?>
        <p>
          <strong>Heads up for better Snippet!</strong><br/>
          You can get better results filling these extra fields!
        </p>
        <ul>
          <?php foreach ( $suggested as $tab_key => $tab) : ?>
            <li><?php echo sprintf( __('Check <a href="admin.php?page=snippet&tab=%s">%s tab</a> for <em>suggested</em> fields', '__x__'), $tab_key, ucwords($tab_key) ) ?>: <strong><?php echo implode(', ', $tab) ?></strong></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>
