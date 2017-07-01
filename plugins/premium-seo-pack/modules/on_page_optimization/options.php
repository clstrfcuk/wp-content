<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'on_page_optimization' => array(
				'title' 	=> __('Mass Optimization', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					/*'install_box' => array(
						'type' 	=> 'app',
						'path' 	=> '{plugin_folder_path}panel.php',
					)*/
					
					/*array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Mass Optimization</h2>
							<ul>
								<li></li>
							</ul>', 'psp')
					),*/
					
					'parse_shortcodes' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Parse content shortcodes:', 'psp'),
						'desc' 		=> __('If you choose "yes", the shortcodes in the page/post content are also parsed by the optimization algorithm, but the process will be more time consuming.', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
					),
					
					'charset' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Server Charset:', 'psp'),
						'desc' 		=> __('Server Charset (used internal by the php-query class)', 'psp')
					),
					
					'meta_title_sufix' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Meta title - text append to:', $psp->localizationName),
						'desc' 		=> __('Append this text to the end of the meta title value from the database', $psp->localizationName)
					),
					
					'meta_keywords_stop_words' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> 'a, you, if',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Stop Words List:', 'psp'),
						'desc' 		=> __('Used default at optimize to auto generate <span style="font-style: bold; color: red;">Meta Keywords</span>
							<br/>The list of stop words (comma separated) which are not taken into consideration when analyzing the content. Default list: <strong>a, you, if</strong>', 'psp'),
						'height'	=> '200px'
					),
					'meta_keywords_stop_words_content' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Stop Words List - Content:', 'psp'),
						'desc' 		=> __('Choose "yes" if you want to use the "Stop Words List" for <span style="font-style: bold; color: red;">SEO Content Analysis rules</span> too (to determine keyword density and if the page content or meta seo title has enough words).', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
					),

					'word_min_chars' 	=> array(
						'type' 		=> 'select',
						'std' 		=> '4',
						'size' 		=> 'large',
						'title' 	=> __('Word Min Chars:', 'psp'),
						'force_width'=> '100',
						'desc' 		=> __('Used default at optimize to auto generate <span style="font-style: bold; color: red;">Meta Keywords</span>
							<br/>The minimum number of characters for a word to be considered valid.', 'psp'),
						'options'	=> $psp->doRange( range(0, 10, 1) )
					),
					'word_min_chars_content' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Word Min Chars - Content:', 'psp'),
						'desc' 		=> __('Choose "yes" if you want to use the "Word Min Chars" for <span style="font-style: bold; color: red;">SEO Content Analysis rules</span> too (to determine keyword density and if the page content or meta seo title has enough words).', 'psp'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO'
						)
					),

					'post_allowed_rules' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array_keys( $psp->get_content_analyzing_rules() ), //array(),
						'size' 		=> 'large',
						'rows_visible'	=> 10,
						'title' 	=> __('Post: Allowed Rules', $psp->localizationName),
						'desc' 		=> __('here you can choose which rules you want to use when analyzing content for <span style="font-style: bold; color: red;">posts, pages, custom post types</span>.<br/>to view a rule\'s full text, hover over it.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Rules list', $psp->localizationName),
							'right' => __('Your chosen rules from list', $psp->localizationName),
						),
						'options' 	=> $psp->get_content_analyzing_rules(),
					),

					'category_allowed_rules' 	=> array(
						'type' 		=> 'multiselect_left2right',
						'std' 		=> array_keys( $psp->get_content_analyzing_rules() ), //array(),
						'size' 		=> 'large',
						'rows_visible'	=> 10,
						'title' 	=> __('Category: Allowed Rules', $psp->localizationName),
						'desc' 		=> __('here you can choose which rules you want to use when analyzing content for <span style="font-style: bold; color: red;">categories, tags, custom taxonomies</span>.<br/>to view a rule\'s full text, hover over it.', $psp->localizationName),
						'info'		=> array(
							'left' => __('All Rules list', $psp->localizationName),
							'right' => __('Your chosen rules from list', $psp->localizationName),
						),
						'options' 	=> $psp->get_content_analyzing_rules(),
					)
				)
			)
		)
	)
);