<?php
require_once ( '../../../../wp-load.php' );
require_once ( ABSPATH . 'wp-admin/includes/file.php' );

$clickTime = date ( "U" ) + 86400;

update_option( 'wppp_hide_time', $clickTime );

echo 'Hide message';