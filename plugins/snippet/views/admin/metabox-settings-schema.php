<?php

// =============================================================================
// VIEWS/ADMIN/METABOX-SETTINGS-SCHEMA.PHP
// -----------------------------------------------------------------------------
// Define default schemas for each post
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
  <?php _e( 'Define your output type of a Website, Organization or both, set your default Schema type and then set a Schema type per post type or disable Schema for specific post types. ', '__x__' ); ?>
</p>

<table class="form-table">

  <tr class="metabox-schema-select">
    <th>
      <label for="<?php echo $plugin_slug . '_default_schema'; ?>">
        <strong><?php _e( 'Output', '__x__' ); ?></strong>
        <span><?php _e( 'What do you want to output for Structure Data (JSON-LD).', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <?php if ( empty( $schema_list ) ) : ?>
        <?php _e( 'No Lists Found', '__x__' ); ?>
      <?php else : ?>
        <input type="checkbox" id="<?php echo $plugin_slug . '_output_all' ?>" 'value="1" > <?php _e( 'Check / Uncheck All', '__x__' ); ?><br/>
        <?php foreach ( $output_list as $output_type ) : ?>
          <hr/>
          <?php foreach ( $output_type as $key => $label ) : ?>
            <input type="checkbox" name="<?php echo $plugin_slug; ?>[output][<?php echo $key ?>]"
            id="<?php echo $plugin_slug . '_output_' . $key; ?>" class="snippet-output"
            value="1" <?php checked( '1', (isset($output[$key]) ? $output[$key] : '') ) ?>> <?php echo $label; ?><br/>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </td>
  </tr>


  <tr class="metabox-schema-select">
    <th>
      <label for="<?php echo $plugin_slug . '_default_schema'; ?>">
        <strong><?php _e( 'Global Default Schema', '__x__' ); ?></strong>
        <span><?php _e( 'If there is no schema set as default for a post type, use this one.', '__x__' ); ?></span>
      </label>
    </th>
    <td>
      <select class="select" name="<?php echo $plugin_slug; ?>[default_schema]" id="<?php echo $plugin_slug . '_default_schema'; ?>">
        <?php if ( empty( $schema_list ) ) : ?>
          <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
        <?php else : ?>
          <option value=" <?php selected( $value, $default_schema ) ?>"><?php _e( '-- Select a schema --', '__x__' ); ?></option>
          <?php foreach ( $schema_list as $value => $label ) : ?>
            <option value="<?php echo $value; ?>" <?php selected( $value, $default_schema ) ?>><?php echo $label; ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </td>
  </tr>

  <?php
  $post_types = get_post_types( array('public' => true), 'object');
  foreach ( $post_types  as $post_type ) : ?>
    <tr>
     <th>
       <label for="<?php echo $plugin_slug . '_schema_' . $post_type->name; ?>">
         <strong><?php _e( $post_type->label, '__x__' ); ?></strong>
         <span><?php _e( 'Set Schema type for ', '__x__' ); ?> <?php _e( $post_type->label, '__x__' ); ?></span>
       </label>
     </th>
     <td>
       <select class="select" name="<?php echo $plugin_slug; ?>[schema][<?php echo $post_type->name; ?>]" id="<?php echo $plugin_slug . '_schema_' . $post_type->name; ?>">
         <?php if ( empty( $schema_list ) ) : ?>
           <option><?php _e( 'No Lists Found', '__x__' ); ?></option>
         <?php else : ?>
           <option value="default" <?php selected( 'default', $schema[ $post_type->name ] ) ?>><?php echo _e( '-- Use default --', '__x__' ); ?></option>
           <option value="disabled" <?php selected( 'disabled', $schema[ $post_type->name ] ) ?>><?php echo _e( '-- disabled --', '__x__' ); ?></option>
           <?php foreach ( $schema_list as $value => $label ) : ?>
             <option value="<?php echo $value; ?>" <?php selected( $value, $schema[ $post_type->name ] ) ?>><?php echo $label; ?></option>
           <?php endforeach; ?>
         <?php endif; ?>
       </select>
     </td>
    </tr>
  <?php endforeach; ?>

</table>
