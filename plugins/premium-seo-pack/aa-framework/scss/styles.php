<?php 
$current_folder_path = realpath(dirname(__FILE__)) . "/";
$main_file = file_get_contents( $current_folder_path . "styles.scss" );

$files = array();
if(preg_match_all('/@import (url\(\"?)?(url\()?(\")?(.*?)(?(1)\")+(?(2)\))+(?(3)\")/i', $main_file, $matches)){
  	foreach($matches[4] as $url){

  		if( file_exists( $current_folder_path . "_" . $url . '.scss') ){ 
  			$files[] = $current_folder_path . '_' . $url . '.scss';
  		}
		if( file_exists( $current_folder_path . "" . $url . '.scss') ){
  			$files[] = $current_folder_path . '' . $url . '.scss';
  		}
  	}
}

$load_style = 'http://dev.aa-team.com/scss/load-style.php?files=' . implode( ",", $files );