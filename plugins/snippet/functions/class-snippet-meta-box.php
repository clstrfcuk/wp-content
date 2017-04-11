<?php

// =============================================================================
// FUNCTIONS/CLASS-SNIPPET-META-BOX.PHP
// -----------------------------------------------------------------------------
// This metaboxes for all schemas
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class Snippet_Meta_Box {

  private $post_type = null;

  private $schemas = array();

  private $prefix  = '_snippet';

  function __construct() {

    $schemas = require( SNIPPET_PATH . '/functions/includes/schema-metaboxes-list.php' );
    foreach ( $schemas as $key => $schema ) {
      $this->schemas[ $key ] = array (
        'id'        => $this->prefix . '_' . $key,
        'label'     => $schema,
        'metaboxes' => require( SNIPPET_PATH . "/functions/includes/schema-metaboxes-{$key}.php" ),
      );
    }

  }

  //
  // Check if a key exists and is not empty in a deep array
  //
  // Using: $this->array_deep_key_exists( $my_array, 'key_level_1', 'key_level_2' , 'key_level_n' )

  function array_deep_key_exists ( $array ) {

    $numargs = func_num_args();
    $args    = func_get_args();

    if ( $numargs === 1 ) {
      return false;
    }

    if ( ! is_array( $array ) ) {
      return false;
    }

    if ( is_array( $args[1] ) ) {
      $args = array_merge( $args[1] );
      unset( $args[1] );
    }

    if ( ! is_string( $args[1] ) && ! is_integer( $args[1] ) ) {
      return false;
    }

    if ( $numargs === 2 ) {
      return ( array_key_exists( $args[1], $array ) && ! empty( $array[ $args[1] ] ) ) ;
    }

    $args[0] = $array[ $args[1] ];
    unset($args[1]);

    return call_user_func_array( array(  $this, 'array_deep_key_exists'), $args );

  }

  function add( $post_type ) {

    $public_post_types = get_post_types( array('public' => true), 'object');
    $allowed = array() ;
    foreach ( $public_post_types as $pt ) {
      $allowed[] = $pt->name;
    }
    if ( ! in_array( $post_type, $allowed ) ) {
      return;
    }

    $data = get_option('snippet');

    if ( $this->array_deep_key_exists ( $data, 'schema', $post_type )
      && $data['schema'][ $post_type ] === 'disabled'
    ) {
      return;
    }

    $this->post_type = $post_type;

    add_meta_box(
      $this->prefix . $post_type,
      __( 'X-Theme Snippet', '__x__' ),
      array(&$this, 'html'),
      $post_type
    );
  }

  function html( $post )
  {
    $value = get_post_meta($post->ID, $this->prefix, true);

    if ( empty( $value ) ) {
      $value = array();
    }

    if (
      ! array_key_exists( 'post_type_schema', $value )
      || ( array_key_exists( 'post_type_schema', $value ) && empty ($value['post_type_schema']) )
    ) {
      $data  = get_option('snippet');
      $value['post_type_schema'] = $data['default_schema'];
      if ( $this->array_deep_key_exists( $data, 'schema', $post_type )
        && $data['schema'][ $this->post_type ] !== 'default'
      ) {
        $value['post_type_schema'] = $data['schema'][ $this->post_type ];
      }
    }

    ?>

    <p>
      <?php _e( 'Select a schema and fill relevant info for snippet data.', '__x__' ); ?>
    </p>

    <table class="form-table x-form-table">

      <tr class="metabox-schema-select">
        <th>
          <label for="<?php echo $this->prefix ?>_post_type_schema">
            <strong><?php _e( 'Schema for this Post/Page', '__x__' ); ?></strong>
            <span><?php _e( 'Select a schema to display its fields.', '__x__' ); ?></span>
          </label>
        </th>
        <td>
          <select name="_snippet[post_type_schema]" id="<?php echo $this->prefix ?>_post_type_schema" class="postbox">
            <option value=""><?php _e( '-- select a schema --', '__x__' ); ?></option>
            <?php foreach ( $this->schemas as $schema_key => $schema ) : ?>
            <option value="<?php echo $schema_key; ?>" <?php selected($value['post_type_schema'], $schema_key); ?>><?php echo $schema['label']; ?></option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>

      <?php foreach ( $this->schemas as $schema_key => $schema ) : ?>
        <?php foreach ( $schema['metaboxes'] as $metabox ) :
          if ( $metabox['type'] !== 'type' && ! ( array_key_exists( 'hide', $metabox ) && $metabox['hide'] === true )  ) :
          ?>

          <tr class="metabox-schema metabox-schema-<?php echo $schema_key ?>">
            <th>
              <label for="<?php echo $metabox['id']; ?>">
                <strong><?php echo $metabox['label']; ?></strong>
                <span><?php echo $metabox['description']; ?></span>
              </label>
            </th>
            <td>
              <?php $this->renderMeta( $schema_key, $metabox, $value, $post->ID ) ?>
            </td>
          </tr>

          <?php endif; ?>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </table>

    <?php
  }


  function renderMeta( $schema_key, $metabox, $value, $post_id ) {

    $metabox_value = ( $this->array_deep_key_exists ( $value, $schema_key, $metabox['name'] ) )
                   ? $value[ $schema_key ][ $metabox['name'] ]
                   : null;

    if ( empty ( $metabox_value) && array_key_exists( 'default_value', $metabox ) ) {
      $metabox_value = $this->get_default_value( $metabox['default_value'], $post_id );
    }

    $tag_name = "_snippet[{$schema_key}][{$metabox['name']}]";
    $tag_id   = $metabox['id'];

    switch ( $metabox['type'] ) {

      //
      // Ignored, used only on saving
      //
      case 'type':
        break;

      case 'date-published':
      case 'date-modified': ?>
        <?php echo date_i18n($metabox_value) ?><br/><span class="x-span-help"><?php _e( 'This value is got from post data.', '__x__'); ?></span>
      <?php
        break;

      case 'rating': ?>
        <input type="radio" name="<?php echo $tag_name; ?>" id="<?php echo $metabox['id']; ?>" value="1" class="postbox" <?php checked($metabox_value, '1'); ?>>1
        <input type="radio" name="<?php echo $tag_name; ?>" id="<?php echo $metabox['id']; ?>" value="2" class="postbox" <?php checked($metabox_value, '2'); ?>>2
        <input type="radio" name="<?php echo $tag_name; ?>" id="<?php echo $metabox['id']; ?>" value="3" class="postbox" <?php checked($metabox_value, '3'); ?>>3
        <input type="radio" name="<?php echo $tag_name; ?>" id="<?php echo $metabox['id']; ?>" value="4" class="postbox" <?php checked($metabox_value, '4'); ?>>4
        <input type="radio" name="<?php echo $tag_name; ?>" id="<?php echo $metabox['id']; ?>" value="5" class="postbox" <?php checked($metabox_value, '5'); ?>>5
      <?php
        break;

      case 'currency':
      case 'availability':
      case 'item-condition':
      $list  = require( SNIPPET_PATH . "/functions/includes/{$metabox['type']}-list.php" );
      ?>
      <select class="select" name="<?php echo $tag_name; ?>" id="<?php echo $metabox['id']; ?>">
          <option value="" <?php selected($metabox_value, ''); ?>><?php _e( '-- Select an option --', '__x__' ); ?></option>
          <?php foreach ( $list as $key => $label ) : ?>
            <option value="<?php echo $key ?>" <?php selected($metabox_value, $key); ?>><?php echo $label ?></option>
          <?php endforeach; ?>
      </select>
      <?php
        break;

      case 'datetime': ?>
      <input type="text" class="medium-text x-date-picker" name="<?php echo $tag_name; ?>[date]"
      id="<?php echo $metabox['id']; ?>_date" placeholder="yyyy-mm-dd"
      value="<?php echo esc_attr( $metabox_value['date'] ); ?>" class="postbox">
      <input type="text" class="medium-text x-time-picker" name="<?php echo $tag_name; ?>[time]"
      id="<?php echo $metabox['id']; ?>_time" placeholder="hh:mm"
      value="<?php echo esc_attr( $metabox_value['time'] ); ?>" class="postbox">
      <?php
        break;

      // @todo hour availability
      case 'houravailability':
        break;

      case 'place':
        $list  = require( SNIPPET_PATH . "/functions/includes/country-list.php" );
        ?>
        <input type="text" class="small-text" name="<?php echo $tag_name; ?>[name]"
        id="<?php echo $metabox['id']; ?>_name" placeholder="<?php _e( 'Place Name', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['name'] ); ?>" class="postbox">

        <input type="text" class="large-text" name="<?php echo $tag_name; ?>[postaladdress][streetAddress]"
        id="<?php echo $metabox['id']; ?>_streee_address" placeholder="<?php _e( 'Street Address', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['streetAddress'] ); ?>" class="postbox">

        <input type="text" class="medium-text" name="<?php echo $tag_name; ?>[postaladdress][addressLocality]"
        id="<?php echo $metabox['id']; ?>_address_locality" placeholder="<?php _e( 'City', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['addressLocality'] ); ?>" class="postbox">

        <input type="text" class="small-text" name="<?php echo $tag_name; ?>[postaladdress][addressRegion]"
        id="<?php echo $metabox['id']; ?>_address_region" placeholder="<?php _e( 'State/County (county in the UK)', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['addressRegion'] ); ?>" class="postbox">

        <input type="text" class="medium-text" name="<?php echo $tag_name; ?>[postaladdress][postalCode]"
        id="<?php echo $metabox['id']; ?>_postal_code" placeholder="<?php _e( 'Zip Code/Postal Code', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['postalCode'] ); ?>" class="postbox">

        <select class="select" name="<?php echo $tag_name; ?>[postaladdress][addressCountry]" id="<?php echo $metabox['id']; ?>_address_country">
          <option value="" <?php selected($metabox_value['postaladdress']['addressCountry'], ''); ?>><?php _e( '-- Select an country --', '__x__' ); ?></option>
          <?php foreach ( $list as $key => $label ) : ?>
            <option value="<?php echo $key ?>" <?php selected($metabox_value['postaladdress']['addressCountry'], $key); ?>><?php echo $label ?></option>
          <?php endforeach; ?>
        </select>

        <input type="number" step="0.0000001" class="medium-text" name="<?php echo $tag_name; ?>[geo][latitude]"
        id="<?php echo $metabox['id']; ?>_latitude" placeholder="<?php _e( 'Latitude', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['geo']['latitude'] ); ?>" class="postbox">

        <input type="number" step="0.0000001" class="medium-text" name="<?php echo $tag_name; ?>[geo][longitude]"
        id="<?php echo $metabox['id']; ?>_longitude" placeholder="<?php _e( 'Longitude', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['geo']['longitude'] ); ?>" class="postbox">

        <?php
          break;

      case 'postaladdress':
        $list  = require( SNIPPET_PATH . "/functions/includes/country-list.php" );
        ?>
        <input type="text" class="large-text" name="<?php echo $tag_name; ?>[postaladdress][streetAddress]"
        id="<?php echo $metabox['id']; ?>_streee_address" placeholder="<?php _e( 'Street Address', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['streetAddress'] ); ?>" class="postbox">

        <input type="text" class="medium-text" name="<?php echo $tag_name; ?>[postaladdress][addressLocality]"
        id="<?php echo $metabox['id']; ?>_address_locality" placeholder="<?php _e( 'City', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['addressLocality'] ); ?>" class="postbox">

        <input type="text" class="small-text" name="<?php echo $tag_name; ?>[postaladdress][addressRegion]"
        id="<?php echo $metabox['id']; ?>_address_region" placeholder="<?php _e( 'State/County (county in the UK)', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['addressRegion'] ); ?>" class="postbox">

        <input type="text" class="medium-text" name="<?php echo $tag_name; ?>[postaladdress][postalCode]"
        id="<?php echo $metabox['id']; ?>_postal_code" placeholder="<?php _e( 'Zip Code/Postal Code', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['postaladdress']['postalCode'] ); ?>" class="postbox">

        <select class="select" name="<?php echo $tag_name; ?>[postaladdress][addressCountry]" id="<?php echo $metabox['id']; ?>_address_country">
          <option value="" <?php selected($metabox_value['postaladdress']['addressCountry'], ''); ?>><?php _e( '-- Select an country --', '__x__' ); ?></option>
          <?php foreach ( $list as $key => $label ) : ?>
            <option value="<?php echo $key ?>" <?php selected($metabox_value['postaladdress']['addressCountry'], $key); ?>><?php echo $label ?></option>
          <?php endforeach; ?>
        </select>
        <?php
          break;

      case 'offer':
        $currency_list       = require( SNIPPET_PATH . "/functions/includes/currency-list.php" );
        $availability_list   = require( SNIPPET_PATH . "/functions/includes/availability-list.php" );
        $item_condition_list = require( SNIPPET_PATH . "/functions/includes/item-condition-list.php" );
        ?>
        <select class="select" name="<?php echo $tag_name; ?>[priceCurrency]" id="<?php echo $metabox['id']; ?>_price_currency">
            <option value="" <?php selected($metabox_value['priceCurrency'], ''); ?>><?php _e( '-- Select an currency --', '__x__' ); ?></option>
            <?php foreach ( $currency_list as $key => $label ) : ?>
              <option value="<?php echo $key ?>" <?php selected($metabox_value['priceCurrency'], $key); ?>><?php echo $label ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" step="0.01" class="medium-text" name="<?php echo $tag_name; ?>[price]"
        id="<?php echo $metabox['id']; ?>_price" placeholder="<?php _e( 'Price (0.00)', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['price'] ); ?>" class="postbox">

        <input type="text" class="small-text" name="<?php echo $tag_name; ?>[url]"
        id="<?php echo $metabox['id']; ?>_url" placeholder="<?php _e( 'URl for the offer (to buy)', '__x__' ) ?>"
        value="<?php echo esc_attr( $metabox_value['url'] ); ?>" class="postbox">

        <select class="select" name="<?php echo $tag_name; ?>[availability]" id="<?php echo $metabox['id']; ?>_availability">
            <option value="" <?php selected($metabox_value['availability'], ''); ?>><?php _e( '-- Select an availability --', '__x__' ); ?></option>
            <?php foreach ( $availability_list as $key => $label ) : ?>
              <option value="<?php echo $key ?>" <?php selected($metabox_value['availability'], $key); ?>><?php echo $label ?></option>
            <?php endforeach; ?>
        </select>

        <select class="select" name="<?php echo $tag_name; ?>[itemCondition]" id="<?php echo $metabox['id']; ?>_item_condition">
            <option value="" <?php selected($metabox_value['itemCondition'], ''); ?>><?php _e( '-- Select an item condition --', '__x__' ); ?></option>
            <?php foreach ( $item_condition_list as $key => $label ) : ?>
              <option value="<?php echo $key ?>" <?php selected($metabox_value['itemCondition'], $key); ?>><?php echo $label ?></option>
            <?php endforeach; ?>
        </select>

        <?php
          break;

      case 'media':
        $thumb = ! empty( $metabox_value )
           ? "<div class=\"x-uploader-image\"><img src=\"{$metabox_value}\" alt=\"\" /></div>"
           : '';
        ?>
          <input type="text" class="file" name="<?php echo $tag_name; ?>"
          id="<?php echo $metabox['id']; ?>"
          value="<?php echo esc_attr( $metabox_value ); ?>" class="postbox">
          <input type="button" id="<?php echo $metabox['id']; ?>_upload_btn"
          data-id="<?php echo $metabox['id']; ?>" class="button-secondary x-upload-btn" value="Upload Image">
          <div class="x-meta-box-img-thumb-wrap" id="<?php echo $metabox['id']; ?>_thumb"><?php echo $thumb; ?> </div>
      <?php
        break;

      case 'textarea': ?>
      <textarea class="large-textarea" name="<?php echo $tag_name; ?>"
      id="<?php echo $metabox['id']; ?>"><?php echo esc_attr( $metabox_value ); ?></textarea>
      <?php
        break;

      case 'integer': ?>
      <input type="number" class="large-text" name="<?php echo $tag_name; ?>"
      id="<?php echo $metabox['id']; ?>"
      value="<?php echo esc_attr( $metabox_value ) ?>" class="postbox">
      <?php
        break;

      case 'money': ?>
      <input type="number" step="0.01" class="medium-text" name="<?php echo $tag_name; ?>"
      id="<?php echo $metabox['id']; ?>" placeholder="0.00"
      value="<?php echo esc_attr( $metabox_value ) ?>" class="postbox">
      <?php
        break;

      default: ?>
      <input type="text" class="large-text" name="<?php echo $tag_name; ?>"
      id="<?php echo $metabox['id']; ?>"
      value="<?php echo esc_attr( $metabox_value ); ?>" class="postbox">
      <?php
        break;

    }

  }

  function get_default_value( $config, $post_id ) {

    if ( ! is_array( $config ) && count( $config ) !== 2 ) {
      return null;
    }

    $source = $config[0];
    $field  = $config[1];

    switch ( $source ) {
      case 'snippet':
        $data  = get_option('snippet');
        $value = array_key_exists($field, $data) ? $data[ $field ] : null;
        break;
      case 'post':
        $post  = get_post( $post_id );
        $value = $post->$field;
        break;
      case 'author':
        $post   = get_post( $post_id );
        $author = get_userdata( $post->post_author);
        $value = $author->$field;
        break;
      case 'post_method':
        $value = strip_tags($field( $post_id ));
        break;
      default:
        $value = null;
    }

    return $value;

  }

  function save( $post_id ) {

    if ( $this->array_deep_key_exists( $_POST, $this->prefix, 'post_type_schema' ) ) {

      $schema_key = $_POST[ $this->prefix ]['post_type_schema'];

      $post  = get_post( $post_id );
      $value = array(
        'post_type_schema' => $schema_key
      );
      $post_values = $_POST[ $this->prefix ][ $schema_key ];

      foreach ( $this->schemas[ $schema_key ]['metaboxes'] as $metabox ) {

        //
        // Special fields saved using post data
        //
        if ( in_array( $metabox['type'], array( 'date-modified', 'date-published' ) ) ) {

          switch ( $metabox['type'] ) {
            case 'date-published':
              $metabox_value = $post->post_date;
              break;
            case 'date-modified':
              $metabox_value = $post->post_modified;
              break;
            default:
              continue;
          }
          $value[ $schema_key ][ $metabox['name'] ]  = $metabox_value;
          continue;
        }

        //
        // Check if metabox exists and sanitize
        //
        if ( array_key_exists( $metabox['name'], $post_values ) && ! empty( $post_values[ $metabox['name'] ] ) ) {

          $value[ $schema_key ][ $metabox['name'] ] = $this->deep_array_sanitize ( $post_values[ $metabox['name'] ] );

        } else if ( array_key_exists( $metabox['name'], $post_values ) && empty( $post_values[ $metabox['name'] ] ) ) {

          unset($post_values[ $metabox['name'] ]);

        } else if ( $metabox['type'] === 'type' ) {

          $value[ $schema_key ][ $metabox['name'] ] = $this->schemas[ $schema_key ]['label'];

        }
      }

      update_post_meta( $post_id, $this->prefix, $value );
    }

  }

  function deep_array_sanitize ( $value ) {
    if ( ! is_array( $value ) ) {
      return sanitize_text_field( $value );
    }
    foreach ( $value as $k => $v ) {
      if ( ! empty( $v ) ) {
          $value[ $k ] = $this->deep_array_sanitize( $v );
      }
    }

    return $value;
  }

  function run () {
    add_action('add_meta_boxes', array(&$this, 'add'));
    add_action('save_post', array(&$this, 'save'));
  }

}

$snippet_meta_box = new Snippet_Meta_Box();

$snippet_meta_box->run();
