<?php 
/*
* Define class cronjobs panel
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
! defined( 'ABSPATH' ) and exit;

// load the modules managers class
$module_class_path = $module['folder_path'] . 'cronjobs.panel.php';
if(is_file($module_class_path)) {
	
	require_once( 'cronjobs.panel.php' );
	
	// Initalize the class
	$pspCronjobsPanel = new pspCronjobsPanel($this->cfg, $module);
	
	//$__module_is_setup_valid = $pspCronjobsPanel->moduleValidation();
	//$__module_is_setup_valid = (bool) $__module_is_setup_valid['status'];
	
	// print the lists interface 
	echo $pspCronjobsPanel->printListInterface();
}