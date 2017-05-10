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

function x_add_meta_box_mailchimp( ) {

  $meta_box = array(
    'id'          => 'email-forms-mailchimp',
    'title'       => __( 'MailChimp specifics', '__x__' ),
    'description' => __( 'Mailchimp options used only on .', '__x__' ),
    'page'        => 'email-forms',
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'name'    => __( 'Skip Double Opt-In (MailChimp only)', '__x__' ),
        'desc'    => __( 'You can skip the double opt-in process if you wish. This only applies to forms generated in this plugin.', '__x__' ),
        'id'      => 'email-forms_double_opt_in',
        'type'    => 'radio',
        'std'     => 'No',
        'options' => array( 'Yes', 'No' )
      ),
    ),
  );

  $callback = create_function( '$post,$meta_box', 'x_create_meta_box_mailchimp( $post, $meta_box["args"] );' );

  add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['page'], $meta_box['context'], $meta_box['priority'], $meta_box );

}

// Create Entry Meta
// =============================================================================

function x_create_meta_box_mailchimp( $post, $meta_box ) {

  x_create_meta_box( $post, $meta_box );

  $email_forms_options  = get_option('email_forms');
  $groups                        = array();
  $tmp_groups                    = get_post_meta( $post->ID, 'email_forms_mailchimp_groups');
  $email_forms_mailchimp_groups = array();
  if (count($tmp_groups) > 0) {
    foreach ($tmp_groups[0] as $tmp ) {
      $email_forms_mailchimp_groups[] = $tmp;
    }
  }
  ?>
  <p><?php _e( 'Add groups for your form.', '__x__' ) ?></p>

  <div id="email-forms-mailchimp-groups-add-div">
    <select id="email-forms-mailchimp-groups-add-select">
      <option value=""><?php _e( '-- Select a group to add --', '__x__' ) ?></option>
    </select>
    <button type="button" id="email-forms-mailchimp-groups-add"><?php _e( 'Add', '__x__' ) ?></button>
  </div>

  <table class="form-table" id="email-forms-mailchimp-groups-list">
    <thead>
      <tr>
        <tr>
          <th><strong><?php _e( 'Title', '__x__' ); ?></strong></th>
          <th><strong><?php _e( 'Type', '__x__' ); ?></strong></th>
          <th><strong><?php _e( 'Choices', '__x__' ); ?></strong></th>
          <th><strong><?php _e( 'Action', '__x__' ); ?></strong></th>
        </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script type="text/html" id="email-forms-mailchimp-groups-template">
    <tr>
      <td>
        {title_label}
        <input type="hidden" name="x_meta[email_forms_mailchimp_groups][{index}][id]" value="{id}" />
        <input type="hidden" name="x_meta[email_forms_mailchimp_groups][{index}][type]" value="{type}" />
        <input type="hidden" name="x_meta[email_forms_mailchimp_groups][{index}][title]" value="{title}" />
      </td>
        <td>{type}</td>
      <td>{interests}</td>
      <td>
        <a href="#" class="email-forms-mailchimp-group-delete" data-id="{index}"><?php _e( 'Delete', '__x__'); ?></a>
      </td>
    </tr>
  </script>

  <script type="text/html" id="email-forms-mailchimp-groups-empty-template">
    <tr>
      <td colspan="5">
        <?php _e( 'No groups yet.', '__x__'); ?>
        <input type="hidden" name="x_meta[email_forms_mailchimp_groups][]" value="" /></td>
      </td>
    </tr>
  </script>

  <script type="text/javascript">
  var email_forms_mailchimp_groups = <?php echo is_array( $email_forms_mailchimp_groups ) ? json_encode( $email_forms_mailchimp_groups ) : '[]' ?>;
  console.log('MailChimp groups', email_forms_mailchimp_groups)
  </script>

  <?php

}
