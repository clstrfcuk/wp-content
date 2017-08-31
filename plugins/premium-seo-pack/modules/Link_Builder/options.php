<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function psp_postTypes_get( $builtin=true ) {
	global $psp;

	$pms = array(
		'public'   => true,
	);
	if ( $builtin === true || $builtin === false  ) {
		$pms = array_merge($pms, array(
			'_builtin' => $builtin, // exclude post, page, attachment
		));
	}
	$post_types = get_post_types($pms, 'objects');
	unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);

	$ret = array();
	foreach ( $post_types as $key => $post_type ) {
		$value = $post_type->label;
		$ret["$key"] = $value;
	}
	return $ret;
}

global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'Link_Builder' => array(
				'title' 	=> __('Link Builder', 'psp'),
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
							<h2>Link Builder</h2>
							<ul>
								<li>Link Builder can automatically link chosen phrases in your posts and pages. You can include comments also.</li>
							</ul>', 'psp')
					),
					
					/*'max_replacements' => array(
						'type' 		=> 'select',
						'std' 		=> '10',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Max replacements:', 'psp'),
						'desc' 		=> __('Default maximum allowed replacement of phrase in content. If > 10 you\'re content is penalized.', 'psp'),
						'options'	=> $psp->doRange( range(1, 10, 1) )
					),*/
						
					'case_sensitive' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use case sensitive:', 'psp'),
						'desc' 		=> __('If you choose YES, the phrase will be searched as case sensitive, otherwise the default is case insensitive.', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
						
					),
					
					'is_comment' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Replace in comments:', 'psp'),
						'desc' 		=> __('Replace phrase in comments also.', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
					),

					'allow_future_linking' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Allow future linking:', 'psp'),
						'desc' 		=> __('If you choose YES, you are allowed to enter phrases which aren\'t found yet in any of your current posts content at the moment of adding phrase.<br/>The phrase will appear when some post content contains it.<br/>You can consider this like an automatically future linking.', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
						
					),

					'template_format' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '<a href="{url}" title="{attr_title}" rel="{rel}" target="{target}">{title}</a>',
						'size' 		=> 'large',
						'force_width'=> '650',
						'title' 	=> __('Link HTML Template:', 'psp'),
						'desc' 		=> __('You can use the following format tags:<br/>
							<ul>
								<li><code>{phrase}</code> : the setted phrase</li>
								<li><code>{url}</code> : the setted URL for the phrase</li>
								<li><code>{title}</code> : the setted replacement text for the phrase (if empty then the {phrase} will be used)</li>
								<li><code>{rel}</code> : the setted rel attribute for the URL</li>
								<li><code>{target}</code> : the setted target attribute for the URL</li>
								<li><code>{attr_title}</code> : the setted title attribute for the URL</li>
							</ul>', 'psp')
					),

					'post_types' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array('post', 'page'),
						'size' 		=> 'large',
						'rows_visible'	=> 8,
						'force_width'=> '300',
						'title' 	=> __('Select Post Types:', $psp->localizationName),
						'desc' 		=> __('Choose Post Types which you want to be searched and used for link building.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Post Types list', $psp->localizationName),
							'right' => __('Your chosen Post Types from list', $psp->localizationName),
						),
						'options' 	=> psp_postTypes_get( 'both' )
					),

					'exclude_posts_ids' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'small',
						//'force_width'=> '400',
						'title' 	=> __('Exclude posts, pages, post types:', 'psp'),
						'desc' 		=> __('Exclude posts, pages, post types from searches and link building ( <span style="color: red; font-weight: bold;">LIST OF <u>IDs</u>, SEPARATED BY COMMA</span> )', 'psp'),
						'height'	=> '200px',
						'width'		=> '100%'
					),					
				)
			)
		)
	)
);