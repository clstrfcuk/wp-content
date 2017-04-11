<?php

/*
 This item does not produce any output, it just rearranges this children
 so that their output is set up appropriately
 */
class UberMenuItemMenuSegment extends UberMenuItem{

	protected $type = 'menu_segment';
	//protected $auto_child = 'toggle';
	//protected $alter_structure = true;

	function getSetting( $key , $beyondgp = false ){

		//Keys that should be grabbed from the grandparent item instead
		$kickup = array( 'submenu_column_default' , 'submenu_item_layout' , 'submenu_item_content_alignment' );

		$val = '';
		if( in_array( $key , $kickup ) && ( $gi = $this->walker->grandparent_item() ) ){	//$this->depth > 1 && 

			//Check beyond the grandparent item if the grandparent is a menu segment
			//This will happen if using Dynamic Terms, for example
			if( $beyondgp && $gi->getType() == 'menu_segment' ){
				$gi = $this->walker->ancestor_item( 4 );
			}

			//Prevent infinite loop
			if( $gi->getType() == 'menu_segment' ){
				return false;
			}
			else{
				$val = $gi->getSetting($key);
			}
		}
		else $val = isset( $this->settings[$key] ) ? $this->settings[$key] : $this->walker->setting_defaults[$key];

		return $val;
	}

	function get_start_el(){

		// if( is_numeric( $this->getSetting( 'submenu_autocolumns' ) ) ){
		// 	echo '<br/>' . $this->ID.' autocols :' . $this->getSetting( 'submenu_autocolumns' );
		// }


		$transient_key = $transient_expiry = '';
		//up( $this->settings );
		$menu_segment = $this->getSetting( 'menu_segment' );

		$html = "<!-- begin Segment: Menu ID $menu_segment -->";


		//prevent infinite looping
		if( isset( $this->args->menu ) && $this->args->menu ){
			if( $this->args->menu == $menu_segment ){
				$html.= '<!-- Prevented infinite loop with segment nesting -->';
				return $html;
			}
		}



		if( $menu_segment == '_none' || !$menu_segment ){
			$html.= '<li>'.ubermenu_admin_notice( 'Please set a segment for <strong>'.$this->item->title .' ('.$this->ID.')</strong>', false ).'</li>';
			return $html.='<!-- no menu set -->';
		}

		$menu_object = wp_get_nav_menu_object( $menu_segment );
		if( !$menu_object ){
			$html.= '<li>'.ubermenu_admin_notice( 'No menu with ID '.$menu_segment.' for menu item: <strong>'.$this->item->title .' ('.$this->ID.')</strong>', false ).'</li>';
			return $html.'<!-- no menu with ID "'.$menu_segment.'" -->';
		}



		$segment_html = false;

		if( $this->getSetting( 'segment_transient_cache' ) == 'on' ){
			$transient_key = 'ubertk_mseg_'.$this->ID; //$this->get_transient_key( 'mseg_' );
			$transient_expiry_hours = $this->getSetting( 'segment_transient_cache_expiry' );
			if( !is_numeric( $transient_expiry_hours ) ) $transient_expiry_hours = 12;
			$transient_expiry = $transient_expiry_hours * HOUR_IN_SECONDS;
			$segment_html = get_transient( $transient_key );
		}

		//If we're not using transients, no transient set, or transient has expired
		if ( false === ( $segment_html ) ) {

			//Submenus of this item should defer to parent
			if( $this->depth > 0 ){
				//If this is top level, we don't need to set
				$this->settings['submenu_type_calc'] = $this->walker->parent_item()->getSetting('submenu_type_calc');
			}

			//Set Depth offset for segment
			$current_depth_offset = $this->walker->get_offset_depth();
			$this->walker->set_offset_depth( $this->depth );

			$menu_segment_args = array( 
				'menu' 			=> $menu_segment, 
				'menu_class'	=> 'na',	//just to prevent PHP notice
				'echo' 			=> false ,
				'container' 	=> false,
				'items_wrap'	=> '%3$s',
				'walker'		=> $this->walker, 
				'depth'			=> 0,
				'uber_instance'	=> $this->args->uber_instance,
				'uber_segment'	=> $this->ID,
			);

			//Autocolumn handling
			$menu_segment_args['uber_segment_data'] = array(
				'submenu_autocolumns' => $this->getSetting( 'submenu_autocolumns' ),
			);
			add_filter( 'wp_nav_menu_objects' , 'ubermenu_menu_segment_objects_filter' , 10 , 2 );

			//Record the settings so we can easily replace when force-filtering
			$menu_segment_args['uber_segment_args'] = $menu_segment_args;	

			//Generate the menu HTML
			$segment_html = wp_nav_menu( $menu_segment_args );

			//Reset depth offset
			$this->walker->set_offset_depth( $current_depth_offset );

			set_transient( $transient_key , $segment_html , $transient_expiry );
		}
		else{
			$html.= "<!-- cached segment $transient_expiry_hours hours / Transient Key: $transient_key -->";
		}


		$html.= $segment_html;

			

		return $html;
	}
	function get_end_el(){
		//$this->resetAutoChild();
		$menu_segment = $this->getSetting( 'menu_segment' );
		return "<!-- end Segment: $menu_segment -->";
	}


	/* No submenus for the Segment Item */
	function get_submenu_wrap_start(){
		return '';
	}
	function get_submenu_wrap_end(){
		return '';
	}
}

function ubermenu_menu_segment_objects_filter( $sorted_menu_items , $args ){
//Need to defer still

	if( isset( $args->uber_segment_data ) ){
		if( isset( $args->uber_segment_data['submenu_autocolumns'] ) ){
			$autocolumns = $args->uber_segment_data['submenu_autocolumns'];

			if( $autocolumns > 0 ){

				$group_id = $args->uber_segment;		//menu segment ID

				//uberp( $args );
				if( $args->uber_segment == 83 ){
					//uberp( $sorted_menu_items , 2 );

					$top_level_item_count = 0;
					//$top_level_items_map = array();
					foreach( $sorted_menu_items as $i => $item ){
						//Count Top Level Items
						if( $item->menu_item_parent == 0 ){
							$top_level_item_count++;
							//$top_level_items_map[] = $i; //$item->ID;
						}
					}

					$new_parents = array();
					$column_map = array();												//Map of Column Index => Item Count

					$remainder = $top_level_item_count % $autocolumns;					//If 0, items divide evenly into columns, making things simple
					$items_per_column = ceil( $top_level_item_count / $autocolumns );	//Items per column if they were all filled 100%

					//If things don't divide evenly, figure out how to divide them evenly
					//All columns will have $items_per_column or $items_per_column-1
					if( $remainder ){
						//Traverse each column
						for( $_k = 0; $_k < $autocolumns; $_k++ ){

							//Assume columns filled to begin with
							$column_map[$_k] = $items_per_column;

							//If the next column starts the remainder, drop an item
							if( $_k+1 == $remainder ){
								$items_per_column--;
							}
						}
					}
					//If everything divided evenly, then the map has the same value for all columns
					else{					
						for( $_k = 0; $_k < $autocolumns; $_k++ ){
							$column_map[$_k] = $items_per_column;
						}
					}

					$child_index = 1;													//A pointer to the current item we're copying in the sorted_items array (starts with 1 for some reason)
					foreach( $column_map as $_col => $column_count ){

						//Create Dummy Column Item, which will be a child of the original item
						$column_id = $group_id . '-col-' . $_col;
						$new_parents[] = new UberMenu_dummy_item( 
									$column_id , 
									'column' , 
									'Auto Column' , 
									$group_id,
									array( 'columns' => '1-'.$autocolumns ),
									array( 'ubermenu-autocolumn' )
								);

						//Set up the children for this item, taking the proper indexes from the original children array
						//Take the next $column_count items from the original children array and add it to this column's children array
						// $column_children = array();
						// for( $k = 0; $k < $column_count ; $k++ ){
						// 	//$column_children[] = $children[$this->ID][$child_index];
						// 	$column_children[] = $top_level_items[$child_index];
						// 	$child_index++;
						// }

						//Go through the first $column_count top level item and assign it a parent item that we just created
						$k = 0;
						//uberp( $sorted_menu_items );
						while( $k < $column_count ){
							//uberp( $sorted_menu_items[$child_index] );
							if( $sorted_menu_items[$child_index]->menu_item_parent == 0 ){
								$sorted_menu_items[$child_index]->menu_item_parent = $column_id;
								$k++;
							}
							$child_index++;
						}

						

					}

					//Give items parent items that we've just created at the beginning of the array
					$sorted_menu_items = array_merge( $new_parents , $sorted_menu_items );
				
					remove_filter( 'wp_nav_menu_objects' , 'ubermenu_menu_segment_objects_filter' );	
				}
			}
		}
	}
	return $sorted_menu_items;
}


//Menu Segments need to clear the transient after menu item save
add_action( 'ubermenu_after_menu_item_save' , 'ubermenu_clear_menu_segment_transient' , 10 , 1 );
function ubermenu_clear_menu_segment_transient( $menu_item_id ){
	delete_transient( 'ubertk_mseg_'.$menu_item_id );
}