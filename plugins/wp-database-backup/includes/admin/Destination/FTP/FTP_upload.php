<?php

add_action('wp_db_backup_completed', array('WPDBBackupFTP', 'wp_db_backup_completed'));

class WPDBBackupFTP {

    public static function wp_db_backup_completed(&$args) {
        include plugin_dir_path(__FILE__) . 'preflight.php';
        $filename=$filename = $args[0];        
        include plugin_dir_path(__FILE__) . 'sendaway.php';
    }
}
