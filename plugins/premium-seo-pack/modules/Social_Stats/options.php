<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function psp_social_sharing_html() {
	global $psp;
	
	ob_start();
	
	$frm_folder = $psp->cfg['paths']['freamwork_dir_url'];
	$module_folder = $psp->cfg['paths']['plugin_dir_url'] . 'modules/Social_Stats/';
	
	$__toolbarTypes = array(
		'floating'			=> __('Floating Toolbar', 'psp'),
		'content_horizontal'		=> __('Content Top / Bottom Toolbar', 'psp'),
		'content_vertical'		=> __('Content Left / Right Toolbar', 'psp')
	);

	$social_sharing_opt = $psp->get_theoption( $psp->alias . '_socialsharing' );
	$__enabledHtml = array();
	foreach ($__toolbarTypes as $k=>$v) {
		$__key = $k . '-enabled';
		$__key2 = 'tab-item-' . $k;
		
		$isEnabled = false;
		if ( is_array($social_sharing_opt) && isset($social_sharing_opt[$__key]) && $social_sharing_opt[$__key]=='yes') $isEnabled = true;
		$__enabledHtml[] = '<li class="tab-item"><input type="checkbox" name="'.$__key2.'" id="'.$__key2.'" disabled ' . ($isEnabled ? 'checked' : '') . '/><span class="text">' . $v . '</span></li>';
	}
?>
	<ul class="psp-socialshare-tbl-tabs">
		<?php echo implode('', $__enabledHtml); ?>
	</ul>

	<div class="psp-form-row" id="<?php echo 'psp-socialsharing-ajax'; ?>" style="position:relative; z-index:9999;"></div>
	
	<!-- color picker -->
	<link rel='stylesheet' href='<?php echo $frm_folder; ?>js/colorpicker/colorpicker.css' type='text/css' media='all' />
	<script type="text/javascript" src="<?php echo $frm_folder; ?>js/colorpicker/colorpicker.js"></script>

	<!-- admin css/js -->
	<script type="text/javascript" src="<?php echo $module_folder; ?>social_sharing.admin.js?<?php //echo time(); ?>"></script>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function psp_social_sharing_toolbar_enabled_html() {
	global $psp;
	
	ob_start();
	
	$module_folder = $psp->cfg['paths']['plugin_dir_url'] . 'modules/Social_Stats/';
	
	$__toolbarTypes = array(
		'floating'			=> __('Floating Toolbar', 'psp'),
		'content_horizontal'		=> __('Content Top / Bottom Toolbar', 'psp'),
		'content_vertical'		=> __('Content Left / Right Toolbar', 'psp')
	);

	$social_sharing_opt = $psp->get_theoption( $psp->alias . '_socialsharing' );
	$__enabledHtml = array();
	foreach ($__toolbarTypes as $k=>$v) {
		$__key = $k . '-enabled';
		if ( is_array($social_sharing_opt) && isset($social_sharing_opt[$__key]) && $social_sharing_opt[$__key]=='yes') {
			$__enabledHtml[] = '<li data-tbtype="' . $k . '">' . $v . '</li>';
		}
	}
?>
<div class="psp-form-row" style="padding-top: 0; padding-bottom: 0;">
	<label style="margin-top: 7px;"><?php _e('Enabled Toolbars: ', 'psp'); ?></label>
	<div class="psp-form-item large">
		<ul class="toolbars-enabled">
			<?php echo implode('', $__enabledHtml); ?>
		</ul>
	</div>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'social' => array(
				'title' 	=> __('Social Stats', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(

					'__help_services' => array(
						'type' 		=> 'message',
						'status' 	=> 'info',
						'html' 		=> __('
							<a href="#" id="pspsocialstats" style="border:0px solid blue; display: block; position: absolute; top: -7.5rem;"></a>
							<h3 style="margin: 0px 0px 5px 0px;">Social Stats</h3>
							<p>Choose social services for which to view stats</p>
						', 'psp')
					),

					'services' 	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('facebook', 'google', 'pinterest', 'stumbleupon', 'linkedin', 'buffer'),
						'size' 		=> 'small',
						'force_width'=> '250',
						'title' 	=> __('Social Stats for:', 'psp'),
						'desc' 		=> __('Choose social services for which you want to view stats.<br/>Stats are displayed in the plugin Dashboard and Social Stats of your pages.', 'psp'),
						'options' 	=> array(
							'facebook' 		=> 'Facebook',
							//'twitter' 		=> 'Twitter',
							'google' 		=> 'Google +1',
							'pinterest' 	=> 'Pinterest',
							'stumbleupon' 	=> 'Stumbleupon',
							'linkedin' 		=> 'LinkedIn',
							//'delicious' 	=> 'Delicious',
							//'digg' 		=> 'Digg',
							'buffer'		=> 'Buffer',
							//'reddit'		=> 'Reddit',
							//'flattr'		=> 'Flattr',
						)
					)
					
				)
			)
			
			/* define the form_messages box */
			,'socialsharing' => array(
				'title' 	=> __('Social Toolbars Sharing', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> false, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(

					'__help_socialsharing' => array(
						'type' 		=> 'message',
						'status' 	=> 'info',
						'html' 		=> __('
							<a href="#" id="pspsocialsharing" style="border:0px solid blue; display: block; position: absolute; top: -7.5rem;"></a>
							<h3 style="margin: 0px 0px 5px 0px;">Social Toolbars Sharing</h3>
							<p>Enable our social toobars and your clients will be able to share your posts.</p>
						', 'psp')
					),

					'text_email' 	=> array(
						'type' 		=> 'text',
						'std' 		=> __('Email', 'psp'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 		=> __('Email text:', 'psp'),
						'desc' 		=> __('email text', 'psp')
					),
					'text_print' 	=> array(
						'type' 		=> 'text',
						'std' 		=> __('Print', 'psp'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 		=> __('Print text:', 'psp'),
						'desc' 		=> __('print text', 'psp')
					),
					'text_more' 	=> array(
						'type' 		=> 'text',
						'std' 		=> __('More', 'psp'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 		=> __('More text:', 'psp'),
						'desc' 		=> __('more text', 'psp')
					),
					'email' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 		=> __('Email address:', 'psp'),
						'desc' 		=> __('email address', 'psp')
					),
					'twitter_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 		=> __('Twitter Account ID:', 'psp'),
						'desc' 		=> __('Twitter Account ID', 'psp')
					),
					
                    'users_devices' => array(
                        'type'      => 'select',
                        'std'       => 'both',
                        'size'      => 'large',
                        'force_width'=> '200',
                        'title'     => __('Display on only users device:', 'psp'),
                        'desc'      => 'Display on only users device.',
                        'options'   => array(
                            'both'          => __('Mobile & Desktop', $psp->localizationName),
                            'mobile'        => __('Mobile', $psp->localizationName),
                            'desktop'       => __('Desktop', $psp->localizationName)
                        ),
                    ),

					/*'toolbar_enabled_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_social_sharing_toolbar_enabled_html()
					),
					
					'toolbar' => array(
						'type' 		=> 'select',
						'std' 		=> 'floating',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Social Sharing Toolbar:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							//'none'				=> __('None', 'psp'),
							'floating'			=> __('Floating Toolbar', 'psp'),
							'content_horizontal'		=> __('Content Top / Bottom Toolbar', 'psp'),
							'content_vertical'		=> __('Content Left / Right Toolbar', 'psp')
						)
					),*/

					'toolbar_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_social_sharing_html()
					)
				)
			)
		)
	)
);