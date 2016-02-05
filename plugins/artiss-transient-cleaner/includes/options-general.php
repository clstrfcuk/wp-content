<?php
/**
* General Options Page
*
* Screen for specifying general options for the plugin
*
* @package	Artiss-Transient-Cleaner
* @since	1.2
*/
?>
<div class="wrap">

<?php
global $wp_version;
if ( ( float ) $wp_version >= 4.3 ) { $heading = '1'; } else { $heading = '2'; }
?>
<h<?php echo $heading; ?>><?php _e( 'Transient Cleaner Options', 'artiss-transient-cleaner' ); ?></h<?php echo $heading; ?>>

<?php
if ( ( ( !empty( $_POST[ 'Options' ] ) ) or ( !empty( $_POST[ 'Upgrade' ] ) ) or ( !empty( $_POST[ 'Clean' ] ) ) ) && ( check_admin_referer( 'transient-cleaner-options' , 'transient_cleaner_options_nonce' ) ) ) {

	// Assign variable contents to options array

	if ( isset( $_POST[ 'clean_enable' ] ) ) { $options[ 'clean_enable' ] = $_POST[ 'clean_enable' ]; }
	if ( isset( $_POST[ 'clean_optimize' ] ) ) { $options[ 'clean_optimize' ] = $_POST[ 'clean_optimize' ]; }
	if ( isset( $_POST[ 'upgrade_enable' ] ) ) { $options[ 'upgrade_enable' ] = $_POST[ 'upgrade_enable' ]; }
	if ( isset( $_POST[ 'upgrade_optimize' ] ) ) { $options[ 'upgrade_optimize' ] = $_POST[ 'upgrade_optimize' ]; }

	// Update the options

	update_option( 'transient_clean_options', $options );

	// Run any transient housekeeping, if requested

	if ( !empty( $_POST[ 'Clean' ] ) ) { $deleted = atc_transient_delete( false ); }
	if ( !empty( $_POST[ 'Upgrade' ] ) ) { $deleted = atc_transient_delete( true ); }

	// Write out an appropriate message

	$text = __( 'Options Saved.', 'artiss-transient-cleaner' );
	if ( ( !empty( $_POST[ 'Clean' ] ) ) or ( !empty( $_POST[ 'Upgrade' ] ) ) ) {
		$text .= ' ' . __( 'Transients cleared.', 'artiss-transient-cleaner' );
	}

	echo '<div class="updated fade"><p><strong>' . $text . '</strong></p></div>' . "\n";
}

$options = atc_get_options();
?>

<form method="post" action="<?php echo get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=atc-options' ?>">

<p><?php _e( 'These are the current transient cleaning settings.', 'artiss-transient-cleaner' ); ?></p>

<?php

// Show current number of transients, including number of expired

global $wpdb;
$total_transients = $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '%_transient_timeout_%'" );
$text =  sprintf( __( 'There are currently %s timed transients in the database.', 'artiss-transient-cleaner' ), $total_transients );

if ( $total_transients > 0 ) {

	$expired_transients = $wpdb -> get_var( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '%_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()" );
	$text .= ' ' . sprintf( __( '%s have expired.', 'artiss-transient-cleaner' ), $expired_transients );

}

echo '<p>' . $text . '</p>';
?>

<h3><?php _e( 'Clear Expired Transients', 'artiss-transient-cleaner' ); ?></h3>

<?php _e( 'Housekeeping of expired transients, scheduled to run regularly (usually daily).', 'artiss-transient-cleaner' ); ?>

<table class="form-table">

<tr>
<th scope="row"><?php _e( 'Enable', 'artiss-transient-cleaner' ); ?></th>
<td><input type="checkbox" name="clean_enable" value="1"<?php if ( isset( $options[ 'clean_enable' ] ) && ( $options[ 'clean_enable' ] ) ) { echo ' checked="checked"'; } ?>/></td>
</tr>

<tr>
<th scope="row"><?php _e( 'Optimize afterwards', 'artiss-transient-cleaner' ); ?></th>
<td><input type="checkbox" name="clean_optimize" value="1"<?php if ( isset( $options[ 'clean_optimize' ] ) && ( $options[ 'clean_optimize' ] ) ) { echo ' checked="checked"'; } ?>/>&nbsp;<span class="description"><?php _e( 'Not recommended', 'artiss-transient-cleaner' ); ?></span></td>
</tr>

</table>

<?php

// Show when expired transients were last cleared down and how many of them were

$array = get_option( 'transient_clean_expired' );

if ( $array !== false ) {
	$text = sprintf( __( 'Expired transients were removed on %s at %s.', 'artiss-transient-cleaner' ), date( 'l, jS F Y', $array[ 'timestamp' ] ), date( 'H:i', $array[ 'timestamp' ] ) );
} else {
	$text = __( 'No expired transients have yet been removed.', 'artiss-transient-cleaner' );
}
echo '<p>' . $text . '</p>';
?>

<input type="submit" name="Clean" class="button-secondary" value="<?php _e( 'Run Now', 'artiss-transient-cleaner' ); ?>"/></p>

<h3><?php _e( 'Remove All Transients', 'artiss-transient-cleaner' ); ?></h3>

<?php _e( 'Removal of all transients whenever a database upgrade occurs.', 'artiss-transient-cleaner' ); ?>

<table class="form-table">

<tr>
<th scope="row"><?php _e( 'Enable', 'artiss-transient-cleaner' ); ?></th>
<td><input type="checkbox" name="upgrade_enable" value="1"<?php if ( isset( $options[ 'upgrade_enable' ] ) && ( $options[ 'upgrade_enable' ] ) ) { echo ' checked="checked"'; } ?>/></td>
</tr>

<tr>
<th scope="row"><?php _e( 'Optimize afterwards', 'artiss-transient-cleaner' ); ?></th>
<td><input type="checkbox" name="upgrade_optimize" value="1"<?php if ( isset( $options[ 'upgrade_optimize' ] ) && ( $options[ 'upgrade_optimize' ] ) ) { echo ' checked="checked"'; } ?>/></td>
</tr>

</table>

<?php

// Show when transients were last cleared down and how many

$array = get_option( 'transient_clean_all' );

if ( $array !== false ) {
	echo '<p>' . sprintf( __( 'All transients were cleared on %s at %s.', 'artiss-transient-cleaner' ), date( 'l, jS F Y', $array[ 'timestamp' ] ), date( 'H:i', $array[ 'timestamp' ] ) ) . '</p>';
} else {
	echo '<br/>';
}
?>

<input type="submit" name="Upgrade" class="button-secondary" value="<?php _e( 'Run Now', 'artiss-transient-cleaner' ); ?>"/></p>

<?php wp_nonce_field( 'transient-cleaner-options', 'transient_cleaner_options_nonce', true, true ); ?>

<input type="submit" name="Options" class="button-primary" value="<?php _e( 'Save Settings', 'artiss-transient-cleaner' ); ?>"/>

</form>

</div>