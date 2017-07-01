<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'misc' => array(
				'title' 	=> __('Settings', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

                // tabs
                'tabs'  => array(
                    '__tab1'    => array(__('General', 'psp'), '__help_validators, validators_html'),
                    '__tab2'    => array(__('SEO Slug Optimizer', 'psp'), '__help_seo_slug_optimizer, slug_isactive, slug_stop_words, slug_min_chars'),
                    '__tab3'    => array(__('SEO Insert Code', 'psp'), '__help_seo_insert_code, insert_code_isactive, insert_code_head, insert_code_footer'),
					'__tab4'    => array(__('Fixes', 'psp'), 'fix_use_wp_do_shortcode'),
                ),

				// create the box elements array
				'elements'	=> array(

					/* Validators */
                    '__help_validators' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Tools</h3>
                            <p>Some useful tools</p>
                        ', 'psp')
                    ),

					'validators_html' => array(
						'type' 		=> 'html',
						'html' 		=> 
							'<div class="panel-body psp-panel-body psp-form-row __tab1">
								<label class="psp-form-label" for="site-items">' . __('Validators', 'psp') . '</label>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://www.google.com/webmasters/tools/richsnippets" style="position: relative;bottom: -6px;">Google Rich Snippets Testing Tool</a>
								</div>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://linter.structured-data.org/" style="position: relative;bottom: -6px;">Structured Data Linter</a>
								</div>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="https://developers.facebook.com/tools/debug/sharing/" style="position: relative;bottom: -6px;">Facebook Sharing Debugger</a>
								</div>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="https://cards-dev.twitter.com/validator" style="position: relative;bottom: -6px;">Twitter Card Validator</a>
								</div>
							</div>'
					),

					/* SEO Slug Optimizer */
					'__help_seo_slug_optimizer' => array(
						'type' 		=> 'message',
						'status'    => 'info',
						'html' 		=> __('
							<h3 style="margin: 0px 0px 5px 0px;">SEO Slug Optimizer</h3>
							<ul>
								<li>- <i>removes common words from the slug of a post or page</i></li>
								<li>- <i>post or page slug definition:</i> the part of post or page URL that is based on its title; in WordPress edit page, the slug is the yellow highlighted part of the permalink just under the title textbox.</li>
								<li>- <i>why use Slug Optimizer:</i> because it increases keyword potency because there are less words in your URLs so their relevance is greater.</li>
								<li>- <i>keep slug unchanged:</i> if every word in your post or page title is in the list of words to be removed or doesn\'n have the necessary limit of minimum characters (but this is a rare case), PSP Slug Optimizer will not remove the words, because you would end up with a blank slug.</li>
								<li>- <i>manually edit slug:</i> PSP Slug Optimizer will not remove words from a manually edited slug.</li>
								<li>- <i>revert to optimized slug:</i> if after editing your slug, you want to come back to the optimized slug (made from the post title), you must erase the content of the slug textbox and click save.</li>
							</ul>
						', 'psp'),
					),
					'slug_isactive' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Activate Slug Optimizer:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					'slug_stop_words' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Stop Words List:', 'psp'),
						'desc' 		=> __('The list of stop words (comma separated)', 'psp'),
						'height'	=> '200px'
					),
					'slug_min_chars'=> array(
						'type' 		=> 'text',
						'std' 		=> '3',
						'size' 		=> 'large',
						'force_width'=> '50',
						'title' 	=> __('Slug part min chars:', 'psp'),
						'desc' 		=> __('The minimum number of characters for every slug part!', 'psp')
					),

					/* SEO Insert Code */
					'__help_seo_insert_code' => array(
						'type' 		=> 'message',
						'status'    => 'info',
						'html' 		=> __('
							<h3 style="margin: 0px 0px 5px 0px;">SEO Insert Code</h3>
							<p>- it insert the code in the WP Header of Footer of your page.</p>
						', 'psp'),
					),
					'insert_code_isactive' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Activate Insert Code:', 'psp'),
						'desc' 		=> '<span style="color: red;">You must set this option to "YES" if you want to have the bellow codes inserted into your page.</span>',
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					'insert_code_head' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Insert code in &lt;head&gt;:', 'psp'),
						'desc' 		=> __('Insert code in &lt;head&gt;', 'psp'),
						'height'	=> '200px'
					),
					'insert_code_footer' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Insert code in wp footer:', 'psp'),
						'desc' 		=> __('Insert code in wp footer', 'psp'),
						'height'	=> '200px'
					),

					/* Fixes */
					'fix_use_wp_do_shortcode' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Fix/ Use WP do_shortcode:', 'psp'),
						'desc' 		=> '<span style="color: red;">(plugin developers only): choose yes if you want to use the do_shortcode wp function - cause some issues with malformed html.</span>',
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
				)
			)
			
		)
	)
);