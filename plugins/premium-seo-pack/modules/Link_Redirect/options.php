<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

if ( ! function_exists('psp_All404PagesRedirectTo') ) {
function psp_All404PagesRedirectTo( $istab = '', $is_subtab='', $what='' ) {
	global $psp;

	$istab = ''; $is_subtab = '';

	$home_url = trailingslashit( get_home_url() );

	$uniqueKey = 'all_404_pages_to';
	$uniqueKey_cf = 'all_404_pages_to_custom';

	$options = $psp->getAllSettings( 'array', 'Link_Redirect' );

	$val = '';
	if ( isset($options["$uniqueKey"]) ) {
		$val = $options["$uniqueKey"];
	}

	$val_cf = '';
	if ( isset($options["$uniqueKey_cf"]) ) {
		$val_cf = $options["$uniqueKey_cf"];
	}

	ob_start();
?>
<div class="panel-body psp-panel-body psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label class="psp-form-label"><?php _e('Redirect all 404 pages to:', 'psp'); ?></label>
	<div class="psp-form-item large">
	<!--<span class="formNote">&nbsp;</span>-->
		<select id="<?php echo $uniqueKey; ?>" name="<?php echo $uniqueKey; ?>" style="width:350px;">
			<?php
			if (1) {
				$whereto = array(
					''						=> __('Disabled', 'psp'),
					'homepage'				=> __('Homepage', 'psp'),
					'custom_url'			=> __('Custom URL', 'psp')
				);
			}
			foreach ($whereto as $k => $v){
				echo 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
			}
			?>
		</select>
		<span class="psp-form-note">
		Disabled = if you need to, you must redirect your 404 pages yourself (a posibility is by using .htaccess rules) or with our 404 Monitor module.
		<br/>
		Homepage = <span style="font-weight: bold; color: green;"><?php echo $home_url; ?></span>
		</span>
	</div>
	<div class="psp-form-item small" style="margin-top:5px;">
		<span class=""><?php echo __('Enter custom URL:', 'psp'); ?></span>&nbsp;
		<input id="<?php echo $uniqueKey_cf; ?>" name="<?php echo $uniqueKey_cf; ?>" type="text" value="<?php echo $val_cf; ?>">
	</div>
</div>
	<script>
// Initialization and events code for the app
psp_All404PagesRedirectTo = (function ($) {
	"use strict";
	
	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			triggers();
		});
	})();
	
	function custom_field(val) {
		var cf = $('#<?php echo $uniqueKey_cf; ?>'), cfp = cf.parent();
		
		if ( val =='custom_url' ) {
			cfp.show();
		} else {
			cfp.hide();
		}
	}
	
	// triggers
	function triggers()
	{
		custom_field( $('#<?php echo $uniqueKey; ?>').val() );
		
		$('#<?php echo $uniqueKey; ?>').on('change', function (e) {
			e.preventDefault();
	
			custom_field( $(this).val() );
		});
	}
	
	// external usage
	return {
	}
})(jQuery);
	</script>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
}

global $psp;

echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'Link_Redirect' => array(
				'title' 	=> __('Link Redirect', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Link Redirect</h2>
							<ul>
								<li>The Link Redirect Module gives you an easy way of redirecting requests to other pages on your site or anywhere else on the web.</li>
							</ul>', 'psp')
					),
					
					'safe_redirect' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Safe Redirect:', 'psp'),
						'desc' 		=> __('If you choose YES, we\'ll use the wp_safe_redirect wordpress function which can prevent malicious redirects which redirect to another host and also other plugins can set or remove allowed host(s) to or from the list via allowed_redirect_hosts filter hook.<br/>Otherwise we\'ll just use the wp_redirect wordpress function.', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
					),

					'redirect_type' => array(
						'type' 		=> 'select',
						'std' 		=> '301',
						'size' 		=> 'large',
						'force_width'=> '250',
						'title' 	=> __('Default Redirect Type: ', 'psp'),
						'desc' 		=> __('Here you can choose the default redirect type for your urls. You can choose a specific redirect type for each url in the Link Redirect module / Add | Update Link.', 'psp'),
						'options'	=> $psp->get_redirect_types()
					),

					'html_all_404_pages_to' => array(
						'type' 		=> 'html',
						'html' 		=> psp_All404PagesRedirectTo()
					),

                    'enable_monitor'    => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array('post_slug', 'term_slug'),
                        'size'      => 'large',
                        'rows_visible'  => 2,
                        'force_width'=> '300',
                        'title'     => __('Select what to monitor', $psp->localizationName),
                        'desc'      => __('Choose what to monitor from the list. You can choose to monitor if a post or term slug is changed and we make an auto redirect.', $psp->localizationName),
                        'info'      => array(
                            'left' => __('Selected items to be monitored from the list', $psp->localizationName),
                            'right' => __('The items that you chose to be monitored from the list', $psp->localizationName),
                        ),
                        'options'   => array('post_slug' => 'Posts Slug Modified', 'term_slug' => 'Terms Slug Modified')
                    ),
				)
			)
		)
	)
);