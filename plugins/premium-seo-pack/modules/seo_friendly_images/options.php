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
			'seo_friendly_images' => array(
				'title' 	=> __('SEO Friendly Images', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
                // tabs
                'tabs'  => array(
                    '__general'    => array(__('General', 'psp'), '__setup_html'),
                    '__images'    => array(__('Images', 'psp'), '__help_images_alt, image_alt_isactive, image_alt, keep_default_alt, where_new_alt, __help_images_title, image_title_isactive, image_title, keep_default_title, where_new_title'),
                    '__links'    	=> array(__('Links', 'psp'), '__help_links, image_link_title_isactive, link_title, link_keep_default_title, link_where_new_title'),
                ),

				// create the box elements array
				'elements'	=> array(
					'__setup_html' => array(
						'type' 		=> 'message',
						'status'    => 'info',
						'html' 		=> __('
							<h3 style="margin: 0px 0px 5px 0px;">Setup</h3>
							<p>Automatically adds alt and title attributes to all your images in all your website posts/pages.</p>
							<p><strong>Now you can automatically optimize links title attribute too.</strong></p>
							<p>&nbsp;</p>
							<p>Alt Rewriter tags</p>
							<ul>
								<li><code>{focus_keyword}</code> : replaces with your Focus Keyword (if you have one setted)</li>
								<li><code>{title}</code> : replaces with your post/page title</li>
								<li><code>{image_name}</code> : replaces with image name (without extension) (not available for links)</li>
								<li><code>{nice_image_name}</code> : replaces with a nicely formatted image name, replacing special characters with spaces (not available for links)</li>

								<li><code>{keywords}</code> : replaces with the the post|page keywords already defined</li>
								<li><code>{site_title}</code> : replaces with the website\'s title</li>
								<li><code>{date}</code> : replaces with the page|post date</li>
								<li><code>{short_description}</code> : replaces with the page|post excerpt or if excerpt does not exist, 200 character maximum are retrieved from description</li>
								<li><code>{author}</code> : replaces with the page|post author name</li>
								<li><code>{author_username}</code> : replaces with the page|post author username</li>
								<li><code>{author_nickname}</code> : replaces with the page|post author nickname</li>
								<li><code>{author_description}</code> : replaces with the page|post author biographical Info</li>
								<li><code>{categories}</code> : replaces with the post categories names list separated by comma</li>
								<li><code>{tags}</code> : replaces with the post tags names list separated by comma</li>
								<li><code>{terms}</code> : replaces with the post custom taxonomies terms names list separated by comma</li>
								<li><code>{category}</code> : replaces with the post first found category name</li>
								<li><code>{category_description}</code> : replaces with the post first found category description</li>
								<li><code>{tag}</code> : replaces with the post first found tag name</li>
								<li><code>{tag_description}</code> : replaces with the post first found tag description</li>
								<li><code>{term}</code> : replaces with the post first found custom taxonomy term name</li>
								<li><code>{term_description}</code> : replaces with the post first found custom taxonomy term description</li>
							</ul>
							<br /><span style="background-color: white; padding: 5px 10px;"><a href="https://support.google.com/webmasters/answer/114016?hl=en" target="_blank">Google Image publishing guidelines</a></span>
							<!-- || focus_keyword: {focus_keyword} || title: {title} || image_name: {image_name} || nice_image_name: {nice_image_name} || site_title: {site_title} || short_description: {short_description} || author: {author} || author_username: {author_username} || author_nickname: {author_nickname} || author_description: {author_description} || categories: {categories} || tags: {tags} || terms: {terms} || category: {category} || category_description: {category_description} || tag: {tag} || tag_description: {tag_description} || term: {term} || term_description: {term_description} || keywords: {keywords} || -->', 'psp'),
					),

					// image ALT
                    '__help_images_alt' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Image ALT</h3>
                            <p>Your images alternate text attribute.</p>
                        ', 'psp')
                    ),
                    
					'image_alt_isactive' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Activate Image ALT:', 'psp'),
						'desc' 		=> 'Choose "YES" if you want to use "Image Alternate text" provided in this module.',
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),

					'image_alt' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '420',
						'title' 	=> __('Image Alternate text:', 'psp'),
						'desc' 		=> __('Your images alternate text attribute. &lt;img src=&quot;images/&quot; width=&quot;&quot; height=&quot;&quot; <strong>alt=&quot;<u>your_alt</u>&quot;</strong>&gt;<br/>We recommend you to use: <span style="color: red;"><em>{focus_keyword} - {nice_image_name} - {title}</em></span>', 'psp')
					),
					
					'keep_default_alt' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Keep image alt:', 'psp'),
						'desc' 		=> __('Choose if you want to keep the current images alternate text attribute.', 'psp'),
						'options'	=> array(
							'yes' => __('YES', 'psp'),
							'no' => __('NO', 'psp')
						)
					),

					'where_new_alt' => array(
						'type' 		=> 'select',
						'std' 		=> 'append',
						'size' 		=> 'large',
						'force_width'=> '360',
						'title' 	=> __('Where to put new alt:', 'psp'),
						'desc' 		=> __('If you choose to keep the current images alternate text attribute (<span style="color:red;">Keep image alt = YES</span>) then this option will let you select where to put your Image New Alternate text.', 'psp'),
						'options'	=> array(
							'append' => __('Append to current image alternate text attribute', 'psp'),
							'prepend' => __('Prepend before current image alternate text attribute', 'psp')
						)
					),
					
					// image TITLE
                    '__help_images_title' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Image TITLE</h3>
                            <p>Your images title text attribute.</p>
                        ', 'psp')
                    ),
                    
					'image_title_isactive' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Activate Image TITLE:', 'psp'),
						'desc' 		=> 'Choose "YES" if you want to use "Image Title text" provided in this module.',
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'image_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '420',
						'title' 	=> __('Image  Title text:', 'psp'),
						'desc' 		=> __('Your images title text attribute. &lt;img src=&quot;images/&quot; width=&quot;&quot; height=&quot;&quot; <strong>title=&quot;<u>your_title</u>&quot;</strong>&gt;<br/>We recommend you to use: <span style="color: red;"><em>{focus_keyword} - {nice_image_name} - {title} - photo</em></span>', 'psp')
					),
					
					'keep_default_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Keep image title:', 'psp'),
						'desc' 		=> __('Choose if you want to keep the current images title text attribute.', 'psp'),
						'options'	=> array(
							'yes' => __('YES', 'psp'),
							'no' => __('NO', 'psp')
						)
					),
					
					'where_new_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'append',
						'size' 		=> 'large',
						'force_width'=> '360',
						'title' 	=> __('Where to put new title:', 'psp'),
						'desc' 		=> __('If you choose to keep the current images title text attribute (<span style="color:red;">Keep image title = YES</span>) then this option will let you select where to put your Image New Title text.', 'psp'),
						'options'	=> array(
							'append' => __('Append to current image title text attribute', 'psp'),
							'prepend' => __('Prepend before current image title text attribute', 'psp')
						)
					),
					
					// LINK TITLE
                    '__help_links' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;" id="links">Link TITLE</h3>
                            <p>Your links title text attribute.</p>
                        ', 'psp')
                    ),
                    
					'image_link_title_isactive' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Activate Link TITLE:', 'psp'),
						'desc' 		=> 'Choose "YES" if you want to use "Link Title text" provided in this module.',
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'link_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '420',
						'title' 	=> __('Link Title text:', 'psp'),
						'desc' 		=> __('Your links title text attribute. &lt;a href=&quot;#&quot; <strong>title=&quot;<u>your_title</u>&quot;</strong>&gt;<br/>We recommend you to use: <span style="color: red;"><em>{focus_keyword} - {title}</em></span>', 'psp')
					),
					
					'link_keep_default_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Keep link title:', 'psp'),
						'desc' 		=> __('Choose if you want to keep the current links title text attribute.', 'psp'),
						'options'	=> array(
							'yes' => __('YES', 'psp'),
							'no' => __('NO', 'psp')
						)
					),
					
					'link_where_new_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'append',
						'size' 		=> 'large',
						'force_width'=> '360',
						'title' 	=> __('Where to put new title:', 'psp'),
						'desc' 		=> __('If you choose to keep the current links title text attribute (<span style="color:red;">Keep link title = YES</span>) then this option will let you select where to put your Link New Title text.', 'psp'),
						'options'	=> array(
							'append' => __('Append to current link title text attribute', 'psp'),
							'prepend' => __('Prepend before current link title text attribute', 'psp')
						)
					),
				)
			)
		)
	)
);