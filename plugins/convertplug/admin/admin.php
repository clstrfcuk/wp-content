<?php 
if( isset( $_GET[ 'view' ] ) && $_GET[ 'view' ] == 'smile-mailer-integrations' ) {
	require_once( 'integrations.php' );
} elseif( isset( $_GET[ 'view' ] ) && $_GET[ 'view' ] == 'modules' ) {
	require_once( 'modules.php' );
} elseif( isset( $_GET['view'] ) && $_GET['view'] ==  'settings' ) {
	require_once( 'settings.php' );
} elseif( isset( $_GET['view'] ) && $_GET['view'] ==  'registration' ) {
	require_once( 'registration.php' );
} elseif( isset( $_GET['view'] ) && $_GET['view'] ==  'debug' ) {
	require_once( 'debug.php' );
}else {
	require_once('get_started.php');
}