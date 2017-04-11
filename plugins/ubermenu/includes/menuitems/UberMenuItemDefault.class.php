<?php

/*
 * A regular menu item
 */
class UberMenuItemDefault extends UberMenuItem{

	protected $type = 'default';


	function get_term_id(){
		if( $this->item->type == 'taxonomy' ){
			return $this->item->object_id;
		}
		return false;
	}


	function get_new_column( $output ){

		if( $this->depth >= 2 ){
			if( $this->getSetting( 'new_column' ) == 'on' ){			//New Columns
				$cols = $this->walker->parent_item()->getSetting( 'columns' );
				//if( $cols == 'auto' ) $cols = $this->walker->grandparent_item()->getSetting( 'submenu_column_default' );
				$output.= '</ul></li>';
				$output.= '<li class="ubermenu-item ubermenu-column ubermenu-column-'.$cols.' ubermenu-item-header ubermenu-newcol">'.
							'<span class="ubermenu-target">&nbsp;</span><ul class="ubermenu-submenu ubermenu-submenu-type-stack">';
			}
		}

		return $output;
	}


	function get_start_el(){
		
		//Variable Initialization
		$item_output = '';
		$class_names = $value = '';

		$item_output = $this->get_new_column( $item_output );

		//Setup Classes
		$this->add_class_item_defaults();
		$this->add_class_id();
		$this->prefix_classes();
		$this->add_class_item_display();
		$this->add_class_level();
		$this->add_class_layout_columns();
		$this->add_class_alignment();
		$this->add_class_mini_item();
		$this->add_class_submenu();
		$this->add_class_rtl_sub();
		$this->add_class_disable_padding();
		$this->add_class_responsive();

		$class_names = $this->filter_item_classes();

		//Setup ID
		$id = $this->filter_item_id();

		//Setup Trigger
		$this->setup_trigger();

		//Atts
		$atts = ' ';
		foreach( $this->item_atts as $att => $val ){
			$atts.= $att.'="'.$val.'" ';
		}

		//Item LI
		$item_output .= '<li' . $id . $value . $class_names . $atts.'>';

		//Anchor
		$atts = $this->anchor_atts(); //Attributes
		$item_output .= $this->get_anchor( $atts );

		//Custom Content
		$item_output .= $this->get_custom_content();

		//Widget
		$item_output .= $this->get_widget_area();

		return $item_output;
	}

	function alter_structure(){
		//if( $this->getSetting( ''))
		if( is_numeric( $this->getSetting( 'submenu_autocolumns' ) ) ){
			return $this->alter_structure = true;
		}
		return $this->alter_structure;
	}


	function alter( &$children ){

		$autocolumns = $this->getSetting( 'submenu_autocolumns' );
		if( $autocolumns && 													//Value is set
			$autocolumns != 'disabled' && 										//Value is not disabled
			isset( $children[$this->ID] ) ){									//Children exist (otherwise, there are no columns to autocreate, and this will cause an issue)

			$item_count = count( $children[$this->ID] );						//Total original child items

			$new_children = array();											//New child items to be added to $children array, keyed by item IDs
			$column_map = array();												//Map of Column Index => Item Count

			$remainder = $item_count % $autocolumns;							//If 0, items divide evenly into columns, making things simple
			$items_per_column = ceil( $item_count / $autocolumns );				//Items per column if they were all filled 100%

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
		

			$child_index = 0;													//A pointer to the current item we're copying in the original item's child array
			foreach( $column_map as $_col => $column_count ){

				//Create Dummy Column Item, which will be a child of the original item
				$column_id = $this->ID . '-col-' . $_col;
				$new_children[$this->ID][] = new UberMenu_dummy_item( 
							$column_id , 
							'column' , 
							'Auto Column' , 
							$this->ID,
							array( 'columns' => ( $autocolumns == 1 ? 'full' : '1-'.$autocolumns ) ),
							array( 'ubermenu-autocolumn' )
						);

				//Set up the children for this item, taking the proper indexes from the original children array
				//Take the next $column_count items from the original children array and add it to this column's children array
				$column_children = array();
				for( $k = 0; $k < $column_count ; $k++ ){
					$column_children[] = $children[$this->ID][$child_index];
					$child_index++;
				}

				//Add this column's children to the $children array
				$new_children[$column_id] = $column_children;

			}

			//Add all the new children back into the $children array, overriding the item's original children
			foreach( $new_children as $item_id => $kiddos ){
				$children[$item_id] = $kiddos;
			}
		}

	}

}