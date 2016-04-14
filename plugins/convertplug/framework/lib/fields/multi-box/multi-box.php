<?php
// Add new input type "switch"
if ( function_exists('smile_add_input_type'))
{
	smile_add_input_type('multi_box' , 'multi_box_settings_field' );
}

add_action('admin_enqueue_scripts','smile_multi_box_scripts');
function smile_multi_box_scripts($hook){
	$cp_page = strpos( $hook, 'plug_page');
	$data  =  get_option( 'convert_plug_debug' );
	wp_enqueue_script( 'jquery-ui-sortable' );

	if( $cp_page == 7 || isset( $_GET['view'] )){
		if( isset( $data['cp-dev-mode'] ) && $data['cp-dev-mode'] == '1' ) {
			wp_enqueue_style('multi-box',plugins_url('multi-box.css',__FILE__));
			wp_enqueue_script('multi-box',plugins_url('multi-box.js',__FILE__),array('jquery','cp-swal-js'),'1.0.0',true);
		}
	}
}

/**
* Function to handle new input type "multi box"
*
* @param $settings		- settings provided when using the input type "multi box"
* @param $value			- holds the default / updated value
* @return string/html 	- html output generated by the function
*/
function available_form_input_types() {
	$array = array(
		'textfield',
		'email',
		'textarea',
		'number',
		'dropdown',
		'hidden'
	);
	return $array;
}


function render_multi_box($uniq, $value) {
	$output = '';

	$input_types = array();
	$input_types = available_form_input_types();

	$uniq = uniqid($uniq);

	// remove backslashes
	$value = preg_replace('/\\\\/', '', $value);

	$value_mix_array = explode('|', $value);
	$_value_array = array();
	if(!empty($value_mix_array)) {
		foreach ($value_mix_array as $key => $value_mix_string) {
			$_array_temp = explode('->', $value_mix_string);
			if(!empty($_array_temp)) {
				$_name = (isset($_array_temp[0])) ? $_array_temp[0] : '';
				$_value = (isset($_array_temp[1])) ? $_array_temp[1] : '';
				if($_name !== '') {
					$_value_array[$_name] = $_value;
				}
			}
		}
	}

	$current_input_name_val = (isset($_value_array['input_name'])) ? $_value_array['input_name'] : 'CP_FIELD_' . rand(00, 99);
	$current_input_label_val = (isset($_value_array['input_label'])) ? $_value_array['input_label'] : '';

	$accordion_label = ($current_input_label_val != '') ? $current_input_label_val : $current_input_name_val;

	$is_hidden = $is_dropdown = $need_placeholder = false;

	$output .= '<div class="multi-box">';
		$output .= '<div class="toggle-accordion-head">
						<span class="mb-mini-box accordion-head-label">'.$accordion_label.'</span>
						<span class="mb-mini-box multi-box-delete"><i class="dashicons dashicons-no-alt"></i></span>
						<!--<span class="mb-mini-box multi-box-handle"><i class="dashicons dashicons-sort"></i></span>
						<span class="mb-mini-box"><i class="dashicons dashicons-arrow-down"></i></span>-->
					</div>';
		$output .= '<div class="toggle-accordion-content">';
			$output .= '<div class="multi-box-field">';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Type attribute specifies the type of &lt; input &gt; element to display." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<label>'.__('Field Type', 'smile').'</label>';
				$output .= '<select class="cp_mb_select" name="input_type" id="mb-input_type-'.$uniq.'">';
					//$output .= '<option value="">Select input type</option>';
					$current_input_val = (isset($_value_array['input_type'])) ? $_value_array['input_type'] : '';
					if(!empty($input_types)) :
						foreach ($input_types as $key => $type) :
							$selected = ($current_input_val === $type) ? 'selected="selected"' : '';
							$output .= '<option value="'.$type.'" '.$selected.'>'.ucfirst($type).'</option>';
							if($current_input_val === $type && $type === 'hidden') {
								$is_hidden = true;
							}
							else if($current_input_val === $type && $type === 'dropdown') {
								$is_dropdown = true;
							}
							else if($current_input_val === $type && ($type === 'textfield' || $type === 'email' || $type === 'number')) {
								$need_placeholder = true;
							}
						endforeach;
					endif;
				$output .= '</select>';
			$output .= '</div>';

			$output .= '<div class="multi-box-field">';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Label defines a label for an &lt; input &gt; element." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<label>'.__('Field Label', 'smile').'</label>';
				$output .= '<input type="text" class="cp_mb_input" id="mb-input_label-'.$uniq.'" name="input_label" value="'.$current_input_label_val.'"/>';
			$output .= '</div>';

			$output .= '<div class="multi-box-field">';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Name attribute specifies the name of &lt; input &gt; element. This attribute is used to reference form data after a form is submitted. <br/><br/>Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<label>'.__('Field Name (Required)', 'smile').'</label>';
				$output .= '<input type="text" class="cp_mb_input" id="mb-input_name-'.$uniq.'" name="input_name" value="'.$current_input_name_val.'"/>';
			$output .= '</div>';

			$current_input_placeholder = (isset($_value_array['input_placeholder'])) ? $_value_array['input_placeholder'] : '';
			$placeholder_style = ($need_placeholder) ? 'style="display:block"' : 'style="display:none"';

			$output .= '<div class="multi-box-field" '.$placeholder_style.'>';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The placeholder attribute specifies a short hint that describes the expected value of an input field (e.g. a sample value or a short description of the expected format)." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<label>'.__('Placeholder', 'smile').'</label>';
				$output .= '<input type="text" class="cp_mb_input" id="mb-input_placeholder-'.$uniq.'" name="input_placeholder" value="'.$current_input_placeholder.'"/>';
			$output .= '</div>';

			$dropdown_style_for_options = ($is_dropdown) ? 'style="display:block"' : 'style="display:none"';
			$current_dropdown_options = (isset($_value_array['dropdown_options'])) ? $_value_array['dropdown_options'] : __( 'Enter Your Options Here', 'smile' );

			$output .= '<div class="multi-box-field" '.$dropdown_style_for_options.'>';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="Enter the options for your dropdown list. Enter each option on new line." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<label>'.__('Dropdown Choice Options', 'smile').'</label>';
				$output .= '<textarea class="cp_mb_input" id="mb-dropdown_options-'.$uniq.'" name="dropdown_options">'.$current_dropdown_options.'</textarea>';
			$output .= '</div>';

			$hidden_style_for_require = ($is_hidden) ? 'style="display:none"' : '';

			$output .= '<div class="multi-box-field" '.$hidden_style_for_require.'>';
				$current_input_val = (isset($_value_array['input_require'])) ? $_value_array['input_require'] : 'true';
				$checked = ($current_input_val === 'true' || $current_input_val === true) ? 'checked="checked"' : '';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="When Required Field is checked, it specifies that an input field must be filled out before submitting the form." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<input type="checkbox" class="cp_mb_checkbox" id="mb-input_require-'.$uniq.'" name="input_require" value="" '.$checked.'/> <label for="mb-input_require-'.$uniq.'">'.__('Required Field','smile').'</label>';
			$output .= '</div>';

			$hidden_style_for_hidden = ($is_hidden) ? 'style="display:block"' : 'style="display:none"';
			$current_hidden_val = (isset($_value_array['hidden_value'])) ? $_value_array['hidden_value'] : '';

			$output .= '<div class="multi-box-field" '.$hidden_style_for_hidden.'>';
				$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Value attribute specifies the value for your hidden element." style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
				$output .= '<label>'.__('Field Value', 'smile').'</label>';
				$output .= '<input type="text" class="cp_mb_input" id="mb-hidden_value-'.$uniq.'" name="hidden_value" value="'.$current_hidden_val.'"/>';
			$output .= '</div>';

		$output .= '</div> <!-- toggle-accordion-content -->';
	$output .= '</div> <!-- multi-box -->';
	return $output;
}

function multi_box_settings_field($name, $settings, $value)
{
	//var_dump($value);
	$input_name = $name;
	$type = isset($settings['type']) ? $settings['type'] : '';
	$class = isset($settings['class']) ? $settings['class'] : '';

	$uniq = uniqid();

	$output = '<div class="multi-box-wrapper" id="mb-wrapper-'.$uniq.'" data-id="'.$uniq.'">';
		$output .= '<textarea id="multi-box-input-'.$uniq.'" class="content cp-hidden form-control smile-input smile-'.$type.' '.$input_name.' '.$type.' '.$class.'" name="' . $input_name . '" rows="6" cols="6" style="display:block !important">'.$value.'</textarea>';
		$output .= '<div class="multi-box-inner">';

			$boxes = explode(';', $value);
			if(!empty($boxes)) {
				foreach ($boxes as $key => $box_value) {
					$output .= render_multi_box($uniq, $box_value);
				}
			}

		$output .= '</div> <!-- multi-box-inner -->';
		$output .= '<div class="multi-box-add-new">Add New Field<i class="dashicons dashicons-plus"></i></div>';
	$output .= '</div>';
	return $output;
}

add_action( 'wp_ajax_repeat_multi_box', 'repeat_multi_box_callback' );
function repeat_multi_box_callback() {
	$uniq = $_POST['id'];

	if($uniq === '') {
		$response['type'] = 'error';
		$response['message'] = 'No wrapper ID found';
		echo json_encode($response);
		die();
	}

	$value = '';
	$output = render_multi_box($uniq, $value);

	$response['type'] = 'success';
	$response['message'] = $output;
	echo json_encode($response);
	die();
}

// function to convert dropdown string to array
function mb_dropdown_string_to_array($string) {
	$lines = explode(PHP_EOL, $string);
	$array = array();
	foreach ($lines as $key => $line) {
		$line = trim($line);
		if($line === '')
			continue;
		$temp = array();
		$line_to_array = explode('+', $line);
		$label = (isset($line_to_array[0])) ? ucfirst($line_to_array[0]) : ucfirst($line);
		$value = (isset($line_to_array[1])) ? $line_to_array[1] : $line;
		$temp['label'] = trim($label);
		$temp['value'] = trim($value);
		array_push($array, $temp);
	}
	return $array;
}
