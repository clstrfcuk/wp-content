<?php

add_action('wp_db_backup_completed', array('WPDBBackupDropbox', 'wp_db_backup_completed'));

class WPDBBackupDropbox {

    public static function wp_db_backup_completed(&$args) {
        $dropb_autho = get_option('dropb_autho');
        if ($dropb_autho == "yes") {
            include_once plugin_dir_path(__FILE__) . 'dropboxupload.php';
            $dropbox->UploadFile($args[1], $args[0]);
            $args[2] = $args[2]."<br> Upload Database Backup on Dropbox";
        }
    }
}