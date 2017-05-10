<?php
// =============================================================================
// EMAIL-INTEGRATION/FUNCTIONS/CUSTOM-FIELDS-METABOX.PHP
// -----------------------------------------------------------------------------
// Add custom fields metabox for all providers
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
// 01. Add Entry Meta
// 02. Create Entry Meta
// =============================================================================

// Add Entry Meta
// =============================================================================

function x_add_meta_box_custom_field( ) {

  $meta_box = array(
    'id'          => 'email-forms-custom-fields',
    'title'       => __( 'Custom Fields', '__x__' ),
    'description' => __( 'Add custom fields from your provider.', '__x__' ),
    'page'        => 'email-forms',
    'context'     => 'normal',
    'priority'    => 'high',
  );

  $callback = create_function( '$post,$meta_box', 'x_create_meta_box_custom_field( $post, $meta_box["args"] );' );

  add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['page'], $meta_box['context'], $meta_box['priority'], $meta_box );

}

// Create Entry Meta
// =============================================================================

function x_create_meta_box_custom_field( $post, $meta_box ) {

  $email_forms_options       = get_option('email_forms');
  $lists                              = array();
  $tmp_custom_fields                  = get_post_meta( $post->ID, 'email_forms_custom_fields');
  $email_forms_custom_fields = array();
  if (count($tmp_custom_fields) > 0) {
  foreach ($tmp_custom_fields[0] as $tmp ) {
    $email_forms_custom_fields[] = $tmp;
  }
  }

  $caches = array('mc_list_cache', 'ck_list_cache', 'gr_list_cache');

  foreach ( $caches as $cache) {
    if ( array_key_exists( $cache, $email_forms_options ) && is_array( $email_forms_options[ $cache ] ) ) {
      foreach ( $email_forms_options[ $cache ] as $key => $list ) {
        $lists["{$list['provider']}_{$list['id']}"] = $list;
      }
    }
  }
  ?>
      <p><?php _e( 'Add custom fields from your provider.', '__x__' ) ?></p>

      <div id="email-forms-custom-fields-add-div">
        <select id="email-forms-custom-fields-add-select">
          <option value=""><?php _e( '-- Select a field --', '__x__' ) ?></option>
        </select>
        <button type="button" id="email-forms-custom-fields-add"><?php _e( 'Add', '__x__' ) ?></button>
      </div>

      <div id="email-forms-custom-fields-add-nothing">
        <?php _e( 'No custom fields do add on this list.', '__x__' ); ?>
      </div>

      <table class="form-table" id="email-forms-custom-fields-list">
        <thead>
          <tr>
            <tr>
              <th><strong><?php _e( 'Name', '__x__' ); ?></strong></th>
              <th><strong><?php _e( 'Type', '__x__' ); ?></strong></th>
              <th><strong><?php _e( 'Label', '__x__' ); ?></strong></th>
              <th><strong><?php _e( 'Choices', '__x__' ); ?></strong></th>
              <th><strong><?php _e( 'Action', '__x__' ); ?></strong></th>
            </tr>
        </thead>
        <tbody></tbody>
      </table>

      <script type="text/html" id="email-forms-custom-fields-template">
        <tr>
          <td>{name_label}<input type="hidden" name="x_meta[email_forms_custom_fields][{id}][name]" value="{name}" /></td>
          <td>{type_label}<input type="hidden" name="x_meta[email_forms_custom_fields][{id}][type]" value="{type}" /></td>
          <td>{label_label}<input type="hidden" name="x_meta[email_forms_custom_fields][{id}][label]" value="{label}" /></td>
          <td>{choices_label}<input type="hidden" name="x_meta[email_forms_custom_fields][{id}][choices]" value="{choices}" /></td>
          <td>
            <a href="#" class="email-forms-custom-field-delete" data-id="{id}"><?php _e( 'Delete', '__x__'); ?></a>
          </td>
        </tr>
      </script>

      <script type="text/html" id="email-forms-custom-fields-empty-template">
        <tr>
          <td colspan="5">
            <?php _e( 'No custom fields yet.', '__x__'); ?>
            <input type="hidden" name="x_meta[email_forms_custom_fields][]" value="" /></td>
          </td>
        </tr>
      </script>

      <script type="text/javascript">
      var email_forms_lists = <?php
        if ( count( $lists ) > 0 ) {
          echo json_encode( $lists );
        }
      ?>;
      var email_forms_custom_fields = <?php echo is_array( $email_forms_custom_fields ) ? json_encode( $email_forms_custom_fields ) : '[]' ?>;
      </script>
  <?php
}
