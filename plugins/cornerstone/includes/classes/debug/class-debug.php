<?php

class Cornerstone_Debug extends Cornerstone_Plugin_Component {

  public function setup() {
    add_action( 'parse_request', array( $this, 'detect_load' ) );
  }


  public function content() {

    echo '<div style="position:absolute;top:100px; left: 200px; right: 200px; bottom: 100px;">';
    wp_editor( '%%PLACEHOLDER%%','cswpeditor', array(
      'quicktags' => false,
      'tinymce'=> array(
        'toolbar1' => 'bold,italic,strikethrough,underline,bullist,numlist,forecolor,wp_adv',
        'toolbar2' => 'link,unlink,alignleft,aligncenter,alignright,alignjustify,outdent,indent',
        'toolbar3' => 'formatselect,pastetext,removeformat,charmap,undo,redo'
      ),
      'editor_class' => 'cs-wp-editor',
      'drag_drop_upload' => true
    ) );
    echo '</div>';
  }

  public function detect_load( $wp ) {

    if ( ! ( current_user_can('manage_options') && isset( $_GET['cs-debug'] ) && '1' === $_GET['cs-debug'] ) ) {
      return;
    }

    add_filter( 'template_include', '__return_empty_string', 999999 );

    remove_all_actions( 'wp_enqueue_scripts' );
    remove_all_actions( 'wp_print_styles' );
    remove_all_actions( 'wp_print_head_scripts' );

    global $wp_styles;
    global $wp_scripts;

    $wp_styles = new WP_Styles();
    $wp_scripts = new WP_Scripts();

    if ( !class_exists('WP_Admin_Bar') ) {
      _wp_admin_bar_init();
    }

    $this->show_admin_bar = true;
    nocache_headers();

    $this->boilerplate();

    exit;

  }

  public function boilerplate() { ?>

    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
    <title>CS Debug</title>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    wp_enqueue_scripts();
    wp_print_styles();
    wp_print_head_scripts();
    ?>
    </head>
    <body<?php $this->body_classes(); ?>>
    <?php
      $this->content();
      wp_print_footer_scripts();
      wp_admin_bar_render();
    ?>
    </body>
    </html>
    <?php
  }

  public function body_classes() {

    $classes = array( 'no-customize-support' );

    if ( is_rtl() ) {
      $classes[] = 'rtl';
    }

    if ( $this->show_admin_bar ) {
      $classes[] = 'admin-bar';
    }

    if ( empty( $classes ) ) {
      return;
    }

    $classes = array_map( 'esc_attr', array_unique( $classes ) );
    $class = join( ' ', $classes );
    echo " class=\"$class\"";

  }
}

// add_action('init', function(){
//
//   return;
//   $stack = 'Renew';
//
//   $name = strtolower($stack);
//   $data = json_decode( file_get_contents("/Users/rohmann/Desktop/presets-$name-cs-header.json"), true );
//
//   // $header = new Cornerstone_Header( 57 );
//   //
//   // $header->set_regions( $elements['regions'] );
//   // $header->save();
//   //
//   // var_dump($header->serialize());die();
//
//
//   $presets_file = "/Users/rohmann/repos/themeco/x/x/framework/functions/bars/sample/presets.php";
//
//   $presets = file_exists($presets_file) ? include($presets_file) : null;
//
//   if ( ! is_array( $presets ) ) {
//     $presets = array();
//   }
//
//   $unique_presets = array();
//   foreach ($presets as $preset) {
//     $key = $preset['element'] . ':' . $preset['title'];
//     $unique_presets[$key] = $preset;
//   }
//
//
//   $manager = CS()->loadComponent('Element_Manager');
//
//   $types = $manager->sort_into_types( $data['regions']['top'] );
//
//   foreach ($types as $type => $elements) {
//     if ( 'container' === $type ) {
//       continue;
//     }
//
//     $definition = $manager->get_element($type);
//     $defaults = $definition->get_defaults();
//     $count = 0;
//
//     foreach ($elements as $element) {
//
//       unset( $element['_type'] );
//       unset( $element['_region'] );
//       foreach ($element as $key => $value) {
//         if ( ! isset($defaults[$key]) || $defaults[$key] === $value ) {
//           unset( $element[$key] );
//         }
//       }
//       $unique_presets["$type:$stack"] = array(
//         'element' => $type,
//         'title' => $stack,
//         'atts'  => json_encode( $element )
//       );
//     }
//   }
//
//
//   $output_presets = array_values( $unique_presets);
//   $total = count( $output_presets );
//
// 	ob_start();
//   echo '<?php return array(';
//
//     foreach ($output_presets as $index => $preset) {
//       echo "\n";
//       var_export($preset);
//       if ( $total > $index + 1 ) echo ',';
//     }
//
//   echo ');';
//   $output = ob_get_clean();
//   file_put_contents($presets_file, $output );
//   var_dump($output);die();
// },9999);
