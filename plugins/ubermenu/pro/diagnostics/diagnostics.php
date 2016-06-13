<?php

add_action( 'wp_footer' , 'ubermenu_diagnostics_loader' , 20 );
function ubermenu_diagnostics_loader(){

	//Only load for admins
	if( !current_user_can( 'manage_options' ) ) return;

	//Only if Diagnostics are enabled
	if( ubermenu_op( 'diagnostics' , 'general' ) != 'on' ) return;

	?>
	<script type="text/javascript">
	(function($){
		var ubermenu_diagnostics_initialized = false;
		window.ubermenu_diagnostics_present = false;

		jQuery(function($) {
			ubermenu_init_diagnostics();
		});

		$( window ).load( function(){
			ubermenu_init_diagnostics();
			//setTimeout( function(){ ubermenu_init_diagnostics(); } , 200 );
		});
	
		function ubermenu_init_diagnostics(){
			
			if( ubermenu_diagnostics_initialized ) return;
			ubermenu_diagnostics_initialized = true;
			$( '.ubermenu-diagnostics-loader-button' ).on( 'click' , function(e){
				e.preventDefault();
				//Load script once
				if( !window.ubermenu_diagnostics_present ){
					ubermenu_load_diagnostics();
				}
			});
		}
		function ubermenu_load_diagnostics(){	
			$.getScript( '<?php echo UBERMENU_URL.'pro/diagnostics/diagnostics.js'; ?>' );
			$('head').append('<link rel="stylesheet" type="text/css" href="<?php echo UBERMENU_URL.'pro/diagnostics/diagnostics.css'; ?>">');
		}

		//Testing
		//setTimeout( ubermenu_load_diagnostics , 300 ); 
	})(jQuery);
	</script>
	<?php
}


function ubermenu_diagnostics_get_item_settings( $item_id ){
	$settings = get_post_meta( $item_id , UBERMENU_MENU_ITEM_META_KEY , true );
	if( $settings ){
		$settings = apply_filters( 'ubermenu_item_settings' , $settings , $item_id );
	}
	else{
		$settings = ubermenu_menu_item_setting_defaults();
		$settings['defaults'] = 1;
	}
	

	
	
	return $settings;
}
function ubermenu_diagnostics_item_info_callback(){

	if( ubermenu_op( 'diagnostics' , 'general' ) != 'on' ) die();

	if( isset( $_POST['menu_item_id'] ) ){
		$item_id = $_POST['menu_item_id'];
		$settings = ubermenu_diagnostics_get_item_settings( $item_id );
		//print_r( $settings );
		echo json_encode( $settings );
	}
	die();	
}
add_action( 'wp_ajax_ubermenu_diagnostics' , 'ubermenu_diagnostics_item_info_callback' );



function ubermenu_diagnostics_tool_residualstyling_callback(){
	require_once( UBERMENU_DIR . '/pro/diagnostics/diagnostics.tool.residualstyling.php' );
	die();
}
add_action( 'wp_ajax_ubermenu_diagnostics_tool_residualstyling' , 'ubermenu_diagnostics_tool_residualstyling_callback' );

function ubermenu_diagnostics_search_theme_callback(){

	//if( ubermenu_op( 'diagnostics' , 'general' ) != 'on' ) die();

	if( !current_user_can( 'edit_files' ) ) die( 'Not authorized' );

	$reply = array();

	//$results = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), 'class="topmenu"' );
	//$results = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), 'wp_nav_menu(' );

	if( wp_verify_nonce( $_POST['uber_nonce'] , 'ubermenu_theme_search' ) ){

		$result_sets = array();

		$theme = get_template_directory();
		$child_theme = get_stylesheet_directory();

		//First search for wrappers by ID, class
		$found = false;
		foreach( $_POST['wrappers'] as $el ){
			
			//Check ID, if ID exists, search for that
			if( isset( $el['id'] ) ){
				$res = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), $el['id'] );
				if( $res ){
					$found = true;
					$result_sets[] = array(
						'search_string' => $el['id'],
						'search_type'	=> 'Wrapper ID',
						'results'		=> $res,
					);
				}
			}
			//If 
			if( !$found && isset( $el['class'] ) ){
				$res = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), $el['class'] );
				if( $res ){
					$found = true;
					$result_sets[] = array(
						'search_string' => $el['class'],
						'search_type'	=> 'Wrapper Class',
						'results'		=> $res,
					);
				}
			}
		}

		//TODO
		// $result_sets = array(
		// 	'search_string'
		// 	'search_type' //class, ID
		// 	'results'
		// );

		//Next, search for wp_nav_menu(
		$wp_nav_menu_results = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), 'wp_nav_menu(' );
		$result_sets[] = array(
			'search_string' => 'wp_nav_menu(',
			'search_type'	=> 'WordPress menu function (look in these results if the above don\'t help you locate the menu',
			'results'		=> $wp_nav_menu_results,
		);


		$rstr = '';

		foreach( $result_sets as $s ){
			$rstr.= '<div class="umd-tool-rs-results">';
			$rstr.= 	'<h4>Found: <strong>'.$s['search_string'].'</strong></h4>';
			$rstr.=		'<h5>Searching for '.$s['search_type'].'</h5>';
			
			foreach( $s['results'] as $file => $lines ){
				$rstr.= 	'<div class="umd-tool-rs-result">';
				$rstr.=			'<h6 class="umd-tool-rs-result-file">'.$file.'</h6>';
				foreach( $lines as $num => $line ){
					$rstr.= 	'<div class="umd-tool-rs-result-line"><span class="umd-tool-rs-result-line-num">Line '.$num . '</span><code>' . esc_html( trim( $line ) ).'</code></div>';
				}
				$rstr.= 	'</div>';
			}

			$rstr.= '</div>';
		}

		$reply['html'] = $rstr;

		//echo $rstr;


		$reply['results'] = $wp_nav_menu_results;
		$reply['echo'] = 'echo echo';
		//$reply['wrappers'] = $_POST['wrappers'];

	}
	else{
		$reply['error'] = 'Nonce doesn\'t check out';
	}
	echo json_encode( $reply );
	die();	
}
add_action( 'wp_ajax_ubermenu_diagnostics_search' , 'ubermenu_diagnostics_search_theme_callback' );


/*
 * Recursively glob into subdirectories to find matching patterns
 */
function ubermenu_glob_recursive( $pattern, $flags = 0 ){

   $files = glob( $pattern, $flags );

   foreach( glob( dirname( $pattern ).'/*', GLOB_ONLYDIR|GLOB_NOSORT ) as $dir ){
      $files = array_merge( $files, ubermenu_glob_recursive( $dir.'/'.basename( $pattern ), $flags ) );
   }
   return $files;
}

/* 
 * Search the contents of a file ($path) for a string ($needle)
 */
function ubermenu_search_file( $path , $needle ){
	$handle = fopen( $path , 'r' );
	$found = false; // init as false
	$line = 0;
	$lines = array();
	while( ($buffer = fgets( $handle ) ) !== false ) {
		$line++;
		if( strpos( $buffer, $needle ) !== false ) {
			$found = true;
			//echo 'FOUND['; echo $buffer; echo ']';
			$lines[$line] = $buffer;
			//break;
		}  
	}
	fclose( $handle );
	//return $found;
	return $found == true ? $lines : false;
}

/*
 * For each path and file match, search the file for the string match
 */
function ubermenu_search_files( $paths = array() , $file_matches = array() , $string_match ){

	$results = array();

	foreach( $paths as $path ){

		foreach( $file_matches as $file_match ){
		 
			//echo $path . $file_match;

			$files = ubermenu_glob_recursive( $path.'/'.$file_match );

			foreach( $files as $file ){

				$lines = ubermenu_search_file( $file , $string_match );

				if( false !== $lines ){
					$short_file = substr( $file , strlen( dirname( $path ) ) );
					//echo '<br/><strong>'.$short_file.'</strong><br/>';
					$results[$short_file] = array();
					foreach( $lines as $line_num => $content ){
						$results[$short_file][$line_num] = $content;
						//echo $line_num . ' :: <code>'. esc_html( $content ).'</code></br>';
					}
				}
			}
		}
	}

	return $results;
}

function ubermenu_find_theme_menu(){
	$time_start = microtime(true); 

	$theme = get_template_directory();
	$child_theme = get_stylesheet_directory();

	//echo '<h3>Search class="topmenu"</h3>';
	$results = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), 'class="topmenu"' );
	//echo '<h3>Search wp_nav_menu(</h3>';
	$results = ubermenu_search_files( array( $theme , $child_theme ) , array( '*.php' ), 'wp_nav_menu(' );


	$time_end = microtime(true);

	//dividing with 60 will give the execution time in minutes other wise seconds
	$execution_time = ($time_end - $time_start);

	//execution time of the script
	echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
}