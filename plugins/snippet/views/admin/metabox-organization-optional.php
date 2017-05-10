<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-ORGANIZATION-OPTIONAL.PHP
// -----------------------------------------------------------------------------
// Optional Organization settings.
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
  <?php _e( 'The below fields are optional, but if you fill them in more information about your organization will display in search results. ', '__x__' ); ?>
</p>

<table class="form-table">

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_organization_logo'; ?>">
        <strong><?php _e( 'Logo URL', '__x__' ); ?></strong>
        <span><?php _e( 'URL for the logo image of your organization.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <input type="text" class="large-text" name="<?php echo $plugin_slug; ?>[organization_logo]"
      id="<?php echo $plugin_slug . '_organization_logo'; ?>"
      value="<?php echo esc_attr( $organization_logo ); ?>">
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . 'organization_additional_type'; ?>">
        <strong><?php _e( 'Additional Types', '__x__' ); ?></strong>
        <span><?php
        $help = 'Add additional types from Schema.org or URLs for <a href="http://www.productontology.org">Product Ontology</a>. This allows you to add extension schemas using WikiPedia definitions, so if you want<a href="https://en.wikipedia.org/wiki/Songwriter">https://en.wikipedia.org/wiki/Songwriter</a> use this format in Additional Type field: <a href="http://www.productontology.org/id/Singer">http://www.productontology.org/id/Singer</a>';
        _e( $help, '__x__' );
        ?></span>
      </label>
    </th>
    <td>
      <textarea class="large-text" name="<?php echo $plugin_slug; ?>[organization_additional_type]" placeholder="https://en.wikipedia.org/wiki/Songwriter&#10;MusicGroup"
      id="<?php echo $plugin_slug . '_organization_additional_type'; ?>"><?php echo esc_attr( $organization_additional_type ); ?></textarea>
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_organization_description'; ?>">
        <strong><?php _e( 'Description', '__x__' ); ?></strong>
        <span><?php _e( 'A short description of your organization.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <textarea class="large-text" name="<?php echo $plugin_slug; ?>[organization_description]"
      id="<?php echo $plugin_slug . '_organization_description'; ?>"><?php echo esc_attr( $organization_description ); ?></textarea>
    </td>
  </tr>

  <tr>
    <th>
      <label for="<?php echo $plugin_slug . '_organization_operation_hours'; ?>">
        <strong><?php _e( 'Hours Of Operation', '__x__' ); ?></strong>
        <span><?php _e( 'What times your organization is open to the public. Use a 24hr format.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <div id="snippet_hours_add_widget">
        From <input type="text" class="small-text snippet-time" id="snippet_hours_start" />
        to <input type="text" class="small-text snippet-time" id="snippet_hours_end" /><br/>
        on: <input type="checkbox" class="snippet_hours_weekday" value="Mo" />Mon
        <input type="checkbox" class="snippet_hours_weekday" value="Tu" />Tue
        <input type="checkbox" class="snippet_hours_weekday" value="We" />Wed
        <input type="checkbox" class="snippet_hours_weekday" value="Th" />Thu
        <input type="checkbox" class="snippet_hours_weekday" value="Fr" />Fri
        <input type="checkbox" class="snippet_hours_weekday" value="Sa" />Sat
        <input type="checkbox" class="snippet_hours_weekday" value="Su" />Sun
        <input type="hidden" class="large-text" id="snippet_hours_id'; ?>" />
        <a href="#" class="button button-primary" id="snippet_hours_add" style="margin-top: -10px;">Add Entry</a>
      </div>
      <hr/>
      <ul id="snippet_hours_list">
      </ul>
      <input type="hidden" class="large-text" name="<?php echo $plugin_slug; ?>[organization_operation_hours]" placeholder="Mo-Fr 09:00-17:00&#10;Mo,We,Fr 18:00-22:00"
      id="<?php echo $plugin_slug . '_organization_operation_hours'; ?>" value="<?php echo esc_attr( $organization_operation_hours ); ?>" />
    </td>
  </tr>

</table>

<script type="text/javascript">
var snippet_hours = [
<?php  if ( $organization_operation_hours ) :
  $hours = explode("|", $organization_operation_hours);
?>
  <?php foreach ( $hours as $key => $value) : ?>
      "<?php echo esc_attr( $value ) ?>"<?php echo $key < count( $hours ) ? ',' : ''; ?>

  <?php endforeach; ?>
<?php endif;?>
];
var snippet_hours_field = 'snippet_organization_operation_hours';
</script>
