<?php
/*
* Define class pspSEOImages
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSEOImages') != true) {
    class pspSEOImages
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';
		
		private $settings = array();
		private $mysettings = array();

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct() {
        	global $psp;
			
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/seo_friendly_images/';
			$this->module = $this->the_plugin->cfg['modules']['seo_friendly_images'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'seo_friendly_images' );
			
			if ( !$this->the_plugin->verify_module_status( 'seo_friendly_images' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin !== true ) {

					// settings & default values
					$this->mysettings['image_alt_isactive'] = isset($this->settings['image_alt_isactive'])
						&& 'no' == $this->settings['image_alt_isactive'] ? false : true;
					$this->mysettings['image_title_isactive'] = isset($this->settings['image_title_isactive'])
						&& 'no' == $this->settings['image_title_isactive'] ? false : true;
					$this->mysettings['image_link_title_isactive'] = isset($this->settings['image_link_title_isactive'])
						&& 'no' == $this->settings['image_link_title_isactive'] ? false : true;

					// add image title and alt to post content
					add_filter('the_content', array($this, 'add_images_tags'), 1000, 1);
					
					// add image title and alt to post thumbnails
					//add_filter( 'wp_get_attachment_image_attributes', array($this, 'add_images_tags_thumbs'), 10, 2 );
					add_filter( 'post_thumbnail_html', array( $this, 'add_images_tags_thumbs2' ), 600 );
				}
			}
        }
		
		// add image title and alt to post content
		public function add_images_tags( $the_content ) {
			// use in this way for work with the shortcodes too
			//$the_content = $this->the_plugin->do_shortcode( $the_content );

			if ( trim($the_content) == "" ) {
				return $this->the_plugin->do_shortcode($the_content);
			}

			// setup the default settings
			$set_img_alt = isset($this->settings["image_alt"]) ? (string) trim($this->settings["image_alt"]) : '';
			$set_img_title = isset($this->settings["image_title"]) ? (string) trim($this->settings["image_title"]) : '';
			$set_link_title = isset($this->settings["link_title"]) ? (string) trim($this->settings["link_title"]) : '';
			$special_tags = $this->special_tags( $set_img_alt, $set_img_title, $set_link_title );
			//var_dump('<pre>', $special_tags, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			
			$__replace = $this->build_replacements(array(
				'special_tags' => $special_tags
			));

			// php query class
			require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );

			//if ( trim($the_content) != "" ) {
				if ( !empty($this->the_plugin->charset) )
					$doc = pspphpQuery::newDocument( $the_content, $this->the_plugin->charset );
				else
					$doc = pspphpQuery::newDocument( $the_content );

				// loop through Images
				if ( $this->mysettings['image_alt_isactive'] || $this->mysettings['image_title_isactive'] ) {
					foreach( pspPQ('img') as $img ) {
	
						// cache the img object
						$img = pspPQ($img); 
						$img = $this->image_replace( $img, array(
							'__replace'			=> $__replace,
							'special_tags'			=> $special_tags,
							'set_img_alt'			=> $set_img_alt,
							'set_img_title'		=> $set_img_title,
						));
	
				    } // end pspPQ('img')
				}
				
				// loop through Links
				if ( $this->mysettings['image_link_title_isactive'] ) {
					foreach( pspPQ('a') as $link ) {
	
						// cache the link object
						$link = pspPQ($link);
						$link = $this->link_replace( $link, array(
							'__replace'			=> $__replace,
							'special_tags'			=> $special_tags,
							'set_link_title'		=> $set_link_title,
						));
						
				    } // end pspPQ('img')
				}

			    //echo __FILE__ . ":" . __LINE__;die . PHP_EOL;   
				return $this->the_plugin->do_shortcode($doc->html());

			//}else{
			//	return $this->the_plugin->do_shortcode($the_content);
			//}
		}

		// add image title and alt to post thumbnails
		public function add_images_tags_thumbs( $attr, $attachment = null ) {
			//var_dump('<pre>', $attr, '</pre>'); echo __FILE__ . ":" . __LINE__;

			// setup the default settings
			$set_img_alt = isset($this->settings["image_alt"]) ? (string) trim($this->settings["image_alt"]) : '';
			$set_img_title = isset($this->settings["image_title"]) ? (string) trim($this->settings["image_title"]) : '';
			$set_link_title = ''; //isset($this->settings["link_title"]) ? (string) trim($this->settings["link_title"]) : '';
			$special_tags = $this->special_tags( $set_img_alt, $set_img_title, $set_link_title );
			//var_dump('<pre>', $special_tags, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			$__replace = $this->build_replacements(array(
				'special_tags' => $special_tags
			));

			$attr = $this->image_replace( $attr, array(
				'__replace'			=> $__replace,
				'special_tags'			=> $special_tags,
				'set_img_alt'			=> $set_img_alt,
				'set_img_title'		=> $set_img_title,
			));
			return $attr;
		}

		public function add_images_tags_thumbs2( $html ) {
			//var_dump('<pre>', $html, '</pre>');
			
			if ( trim($html) == "" ) {
				return $html;
			}
			
			// setup the default settings
			$set_img_alt = isset($this->settings["image_alt"]) ? (string) trim($this->settings["image_alt"]) : '';
			$set_img_title = isset($this->settings["image_title"]) ? (string) trim($this->settings["image_title"]) : '';
			$set_link_title = ''; //isset($this->settings["link_title"]) ? (string) trim($this->settings["link_title"]) : '';
			$special_tags = $this->special_tags( $set_img_alt, $set_img_title, $set_link_title );
			//var_dump('<pre>', $special_tags, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			$__replace = $this->build_replacements(array(
				'special_tags' => $special_tags
			));

			// version I - with regexp
			if (0) {			
				$img_tag = false !== ( $found = preg_match( '/<img[^>]+>/imu', $html, $m ) ) ? $m[0] : '';
				//var_dump('<pre>', $img_tag, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
				$img = array(
					'src'			=> ( $found = preg_match( '/(?:src=(?:"|\'))(.*?)(?:"|\')/i', $img_tag, $m ) ) && ! empty($found) ? (string) $m[1] : '',
					'alt'			=> ( $found = preg_match( '/(?:alt=(?:"|\'))(.*?)(?:"|\')/i', $img_tag, $m ) ) && ! empty($found) ? (string) $m[1] : '',
					'title'			=> ( $found = preg_match( '/(?:title=(?:"|\'))(.*?)(?:"|\')/i', $img_tag, $m ) ) && ! empty($found) ? (string) $m[1] : '',
				);
				//var_dump('<pre>', $img, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
	
				$attr = $this->image_replace( $img, array(
					'__replace'			=> $__replace,
					'special_tags'			=> $special_tags,
					'set_img_alt'			=> $set_img_alt,
					'set_img_title'		=> $set_img_title,
				));
				//var_dump('<pre>', $attr, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
	
				$img_tag2 = $img_tag;
				if ( ! empty($img['alt']) ) {
					$img_tag2 = preg_replace( '/(alt=")(.*?)(")/i', '$1' . $attr['alt'] . '$3', $img_tag2 );
				} else {
					$img_tag2 = preg_replace( '/(<img)/imu', '$1 alt="' . $attr['alt'] . '"', $img_tag2 );
				}
				if ( ! empty($img['title']) ) {
					$img_tag2 = preg_replace( '/(title=")(.*?)(")/i', '$1' . $attr['title'] . '$3', $img_tag2 );
				} else {
					$img_tag2 = preg_replace( '/(<img)/imu', '$1 title="' . $attr['title'] . '"', $img_tag2 );
				}
				$html = preg_replace( '/(<img[^>]+>)/imu', $img_tag2, $html );
				//var_dump('<pre>', $html, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL; 
			}
			// version II - with phpQuery
			else {
				// php query class
				require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
	
				if ( trim($html) != "" ) {
					if ( !empty($this->the_plugin->charset) )
						$doc = pspphpQuery::newDocument( $html, $this->the_plugin->charset );
					else
						$doc = pspphpQuery::newDocument( $html );
	
					// loop through Images
					foreach( pspPQ('img') as $img ) {
	
						// cache the img object
						$img = pspPQ($img); 
						$img = $this->image_replace( $img, array(
							'__replace'			=> $__replace,
							'special_tags'			=> $special_tags,
							'set_img_alt'			=> $set_img_alt,
							'set_img_title'		=> $set_img_title,
						));
	
				    } // end pspPQ('img')
				    
				    //echo __FILE__ . ":" . __LINE__;die . PHP_EOL;   
					$html = $this->the_plugin->do_shortcode($doc->html());
				}
			}
			return $html;
		}

		private function image_replace( $img, $pms=array() ) {
			extract($pms);
			$iso = is_object($img) ? true : false;

			if (1) {
			    	$url = $iso ? $img->attr('src') : (isset($img['src']) ? $img['src'] : '');
					$url = trim($url);
					$image_name = '';
					if( $url != "" ){
						$image_name = explode( '/', $url );
						$image_name = explode( '.', end( $image_name ) );
						$image_name = $image_name[0]; 
					}

					$__replace = array_merge($__replace, array(
						'image_name'			=> $image_name,
						'nice_image_name'	=> $this->nice_image_name( $image_name ),
					));

					// image current attributes text
			    	$alt = $iso ? $img->attr('alt') : (isset($img['alt']) ? $img['alt'] : '');
			    	$title = $iso ? $img->attr('title') : (isset($img['title']) ? $img['title'] : '');

					// setup the default settings
					$new_alt = $set_img_alt;
					$new_title = $set_img_title;

					if( isset($this->settings['keep_default_alt']) && trim($this->settings['keep_default_alt']) != "no" ){
						$whereto = isset($this->settings['where_new_alt']) ? (string) $this->settings['where_new_alt'] : 'append';
						$new_alt = ( 'append' == $whereto ? $alt . ' ' . $new_alt : $new_alt . ' ' . $alt );
					}
					if( isset($this->settings['keep_default_title']) && trim($this->settings['keep_default_title']) != "no" ){
						$whereto = isset($this->settings['where_new_title']) ? (string) $this->settings['where_new_title'] : 'append';
						$new_title = ( 'append' == $whereto ? $title . ' ' . $new_title : $new_title . ' ' . $title );
					}

					// make the replacements
					foreach (array('alt', 'title') as $key) {
						foreach ($special_tags["$key"] as $tag) {
							$_replace_val = isset($__replace["$tag"]) ? $__replace["$tag"] : '';
							${'new_' . $key} = str_replace( '{'.$tag.'}', $_replace_val, ${'new_' . $key} );
						}
					} // end foreach make the replacements
					//var_dump('<pre>ALT',$image_name, $alt, $new_alt,'</pre>');  
					//var_dump('<pre>TITLE',$image_name, $title, $new_title,'</pre>');

					// if the alt / title was changed
					if( $new_alt != $alt && ! empty( $new_alt ) && $this->mysettings['image_alt_isactive'] ) {
						if ($iso) {
							$img->attr( 'alt', trim($new_alt) );
							//$img->attr( '_psp_replace_alt', 1 );
						} else {
							$img['alt'] = trim($new_alt);
							//$img['_psp_replace_alt'] = 1;
						}
					}

					if( $new_title != $title && ! empty( $new_title ) && $this->mysettings['image_title_isactive'] ) {
						if ($iso) {
							$img->attr( 'title', trim($new_title) );
							//$img->attr( '_psp_replace_title', 1 );
						} else {
							$img['title'] = trim($new_title);
							//$img['_psp_replace_title'] = 1;
						}
					}
			}
			return $img;
		}

		private function link_replace( $link, $pms=array() ) {
			extract($pms);
			$iso = is_object($link) ? true : false;

			if (1) {
			    	$url = $iso ? $link->attr('href') : (isset($link['href']) ? $link['href'] : '');

					// link current attributes text
			    	$link_title = $iso ? $link->attr('title') : (isset($link['title']) ? $link['title'] : '');

					// setup the default settings
					$new_link_title = $set_link_title;

					if( isset($this->settings['link_keep_default_title']) && trim($this->settings['link_keep_default_title']) != "no" ){
						$whereto = isset($this->settings['link_where_new_title']) ? (string) $this->settings['link_where_new_title'] : 'append';
						$new_link_title = ( 'append' == $whereto ? $link_title . ' ' . $new_link_title : $new_link_title . ' ' . $link_title );
					}

					// make the replacements
					foreach (array('link_title') as $key) {
						foreach ($special_tags["$key"] as $tag) {
							$_replace_val = isset($__replace["$tag"]) ? $__replace["$tag"] : '';
							${'new_' . $key} = str_replace( '{'.$tag.'}', $_replace_val, ${'new_' . $key} );
						}
					} // end foreach make the replacements
					//var_dump('<pre>TITLE',$url, $link_title, $new_link_title,'</pre>');

					// if the title was changed
					if( $new_link_title != $link_title && ! empty( $new_link_title ) && $this->mysettings['image_link_title_isactive'] ) {
						if ($iso) {
							$link->attr( 'title', trim($new_link_title) );
						} else {
							$link['alt'] = trim($new_link_title);
						}
					}
			}
			return $link;
		}

		private function build_replacements( $pms=array() ) {
			global $wp_query;
			
			extract($pms);

			$__page = 'home'; //default page!
        	$page_type = $this->the_plugin->get_wp_pagetype();
			if ( in_array($page_type, array('admin', 'feed')) ){
				return array();
			}
			$type = $page_type;
			//$type = $type == 'product' ? 'post' : $type;
			
 			$__post = null;
 			$__author = null;

			if (1) {
 				global $post;
	 			if (is_object($post) && isset($post->ID) && !is_null($post->ID) && $post->ID>0)
	 				$__post = $post;
	 			else
	 				$__post = $wp_query->get_queried_object(); //get the post!
	 			//var_dump('<pre>', $__post, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

				$__wpquery = $wp_query->get_queried_object();
				
				if (is_object($__post) && isset($__post->ID) && !is_null($__post->ID) && $__post->ID>0) {
					$type = 'post';
				}
			}

 			$__defaults = array( //default params!
 				'site_title'			=> get_bloginfo('name'), //website name
 				//'site_description'		=> get_bloginfo('description'), //website description
 				//'current_date'			=> date( get_option('date_format') ), //current date
 				//'current_time'			=> date( get_option('time_format') ), //current time
				//'current_day'   		=> date( 'j' ), //current day
				//'current_year'  		=> date( 'Y' ), //current year
				//'current_month' 		=> __( date( 'F' ), 'psp' ), //current month
				//'current_week_day'		=> __( date( 'l' ), 'psp' ), //current week day

 				'id'					=> '',
 				'title'					=> '',
 				'date'					=> '',
 				//'description'			=> '',
 				'short_description'		=> '',
 				//'parent'				=> '',

 				'author'				=> '',
 				'author_username'		=> '',
 				'author_nickname'		=> '',
 				'author_description'	=> '',
 				
 				'categories'			=> '',
 				'tags'					=> '',
 				'terms'					=> '',

 				'category'				=> '',
 				'category_description'	=> '',
 				'tag'					=> '',
 				'tag_description'		=> '',
 				'term'					=> '',
				'term_description'		=> '',

 				//'search_keyword'		=> '',

 				'keywords'				=> '',
 				'focus_keyword'		=> '',
 				
 				//'totalpages'			=> '',
 				//'pagenumber'			=> ''
 				
				'image_name'			=> '',
				'nice_image_name'	=> '',
 			);
			
 			//to be replaced params
 			$__replace = array_merge($__defaults, array(
 				//'title'				=> get_bloginfo('name')
 			));
			
 			$__postClean = $__defaults;
 			$__authorClean = $__defaults;
 			$__taxonomyClean = $__defaults;

 			//loop through all page types and set some info!
 			//::

 			//page type is: post or page (or attachment)
 			if (in_array($type, array('post', 'page', 'posttype'))) {
 				/*
 				global $post;
	 			if (is_object($post) && isset($post->ID) && !is_null($post->ID) && $post->ID>0) 
	 				$__post = $post;
	 			else
	 				$__post = $wp_query->get_queried_object(); //get the post!
	 			//var_dump('<pre>', $__post, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

				$__wpquery = $wp_query->get_queried_object();
				*/

 				$__postClean['id'] = $__post->ID;

				if ( empty($__postClean['id']) ) {
					return $__replace;
				}

 				//if ( isset($__postClean['id']) && !is_null($__postClean['id']) && $__postClean['id']>0 ) {
 					//post title
					$__postClean['title'] = strip_tags( apply_filters( 'single_post_title', $__post->post_title ) );

 					//post date
 					if ( isset($__post->post_date) && !empty($__post->post_date) ) {
 						$__postClean['date'] = mysql2date( get_option( 'date_format' ), $__post->post_date );
 					}

					/* 					
 					//post description
					$__postClean['description'] = strip_shortcodes( $__post->post_content );
					*/ 

 					//post short description!
 					if ( !empty($__post->post_excerpt) ) {
 						$__postClean['short_description'] = strip_tags( $__post->post_excerpt );
 					} else {
 						global $shortcode_tags;
 						$__postClean['short_description'] = wp_html_excerpt( strip_shortcodes( $__post->post_content ), 200 );
 					}

					/*
 					//post parent
 					if ($__parentId = $__post->post_parent) {
 						$__parent = get_post($__parentId);
 						$__postClean['parent'] = strip_tags( apply_filters( 'single_post_title', $__parent->post_title ) );
 					}
					*/

					$exists = array_intersect(
 						array('author', 'author_username', 'author_nickname', 'author_description'),
 						$special_tags['all']
					);
 					if ( ! empty( $exists ) ) {
 						//post author
						global $authordata;
		 				$__author = $authordata; //get the post author!
					}

					$exists = array_intersect(
 						array('categories', 'tags', 'terms', 'category', 'category_description', 'tag', 'tag_description', 'term', 'term_description'),
 						$special_tags['all']
					);
 					if ( ! empty( $exists ) ) {
	 					//post categories | tags | taxonomies
		 				$__taxonomyClean = array_merge($__taxonomyClean, 
		 					$this->get_taxonomy('post', $__wpquery)
		 				);
					}

					$exists = array_intersect(
 						array('focus_keyword', 'keywords'),
 						$special_tags['all']
					);
					if ( ! empty( $exists ) ) {
		 				//post custom - keywords & focus keyword!
		 				$__tmpKeywords = $this->the_plugin->get_psp_meta( $__postClean['id'] );
		 				$__postClean['keywords'] = isset($__tmpKeywords['keywords']) ? $__tmpKeywords['keywords'] : '';
						$__postClean['focus_keyword'] = isset($__tmpKeywords['focus_keyword']) ? $__tmpKeywords['focus_keyword'] : '';
						//if ( empty($__postClean['focus_keyword']) ) {
			 			//	$__postClean['focus_keyword'] = (string) get_post_meta( $__postClean['id'], 'psp_kw', true );
						//}
						if (empty($__postClean['keywords']) && !empty($__postClean['focus_keyword'])) {
							$__postClean['keywords'] = $__postClean['focus_keyword'];
						}
					}
 				//}
 			}

 			//page type is: category | tag | taxonomy
 			if (in_array($type, array('category', 'tag', 'taxonomy'))) {
				$__wpquery = $wp_query->get_queried_object();

 				$__taxonomyClean = array_merge($__taxonomyClean, 
 					$this->get_taxonomy($type, $__wpquery)
 				);
 			}

 			//page type is: author
 			if ($type=='author') {
 				$__author = $wp_query->get_queried_object(); //get the post author!
 			}
 			
 			//page type is: archive
 			if ($type=='archive') {
 				$__date = '';
				if ( is_month() )
					$__date = single_month_title( ' ', false );
				else if ( is_year() )
					$__date = get_query_var( 'year' );
				else if ( is_day() )
					$__date = get_the_date();
 			}
 			
 			//::
 			//end loop through all page types and set some info!
			
 			//author info
 			if (!is_null($__author) && isset($__author->ID)) {
	 			$__authorClean = array_merge($__authorClean, array(
	 				'title'				=> $__author->display_name,
		 			'author'			=> $__author->display_name,
		 			'author_username'	=> $__author->user_login,
		 			'author_nickname'	=> get_the_author_meta( 'nickname', $__author->ID ),
		 			'author_description'=> get_the_author_meta( 'description', $__author->ID )
	 			));
 			}

			switch ($type) {
 				case 'home' 	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> get_bloginfo('name'),
 						//'description'			=> get_bloginfo('description')
 					));
 					$__page = 'home';
 					break;

 				case 'post'		:
 					$__page = 'post';
 				case 'page'		:
 					$__page = 'page';
 				case 'posttype'	:
 					$__page = 'posttype';
 				case 'post'		:
 				case 'page'		:
 				case 'posttype'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__postClean['title'],
 						'id'					=> $__postClean['id'],
 						'date'					=> $__postClean['date'],
 						//'description'			=> $__postClean['description'],
 						'short_description'		=> $__postClean['short_description'],
 						//'parent'				=> $__postClean['parent'],

 						'author'				=> $__authorClean['author'],
	 					'author_username'		=> $__authorClean['author_username'],
	 					'author_nickname'		=> $__authorClean['author_nickname'],
	 					'author_description'	=> $__authorClean['author_description'],
	 					
 						'categories'			=> $__taxonomyClean['categories'],
	 					'tags'					=> $__taxonomyClean['tags'],
	 					'terms'					=> $__taxonomyClean['terms'],

 						'category'				=> $__taxonomyClean['category'],
 						'category_description'	=> $__taxonomyClean['category_description'],
 						'tag'					=> $__taxonomyClean['tag'],
 						'tag_description'		=> $__taxonomyClean['tag_description'],
 						'term'					=> $__taxonomyClean['term'],
 						'term_description'		=> $__taxonomyClean['term_description'],
 						
 						'keywords'				=> $__postClean['keywords'],
 						'focus_keyword'		=> $__postClean['focus_keyword']
 					));
 					break;

				case 'category'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__taxonomyClean['title'],
 						'category'				=> $__taxonomyClean['category'],
 						'category_description'	=> $__taxonomyClean['category_description']
 					));
 					$__page = 'category';
 					break;
 					
				case 'tag'		:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__taxonomyClean['title'],
 						'tag'					=> $__taxonomyClean['tag'],
 						'tag_description'		=> $__taxonomyClean['tag_description']
 					));
 					$__page = 'tag';
 					break;
 					
				case 'taxonomy'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__taxonomyClean['title'],
 						'term'					=> $__taxonomyClean['term'],
 						'term_description'		=> $__taxonomyClean['term_description']
 					));
 					$__page = 'taxonomy';
 					break;
 					
				case 'archive'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__date,
 						'date'					=> $__date
 					));
 					$__page = 'archive';
 					break;
 					
 				case 'author'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> $__authorClean['title'],
 						'author'				=> $__authorClean['author'],
	 					'author_username'		=> $__authorClean['author_username'],
	 					'author_nickname'		=> $__authorClean['author_nickname'],
	 					'author_description'	=> $__authorClean['author_description']
 					));
 					$__page = 'author';
 					break;
 					
 				case 'search'	:
 					$__replace = array_merge($__replace, array(
 						'title'					=> esc_html( $wp_query->query_vars['s'] ),
 						//'search_keyword'		=> esc_html( $wp_query->query_vars['s'] )
 					));
 					$__page = 'search';
					break;
					
 				case '404'		:
 					$__replace = array_merge($__replace, array(
 					));
 					$__page = '404';
 					break;

 				default			:
 					break;
 			}
			//var_dump('<pre>', $__replace, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

			return $__replace;
		}

		private function special_tags( $img_alt, $img_title, $link_title ) {
			$ret = array('alt' => array(), 'title' => array(), 'link_title' => array(), 'all' => array());
			foreach (array('alt' => $img_alt, 'title' => $img_title, 'link_title' => $link_title) as $key => $val) {
				$found = preg_match_all('/(?:\{)(\w+)(?:\})/imu', $val, $m);
				//var_dump('<pre>', $key, $val, $found, $m, '</pre>');
				if ( false !== $found && ! empty($m) ) {
					$ret["$key"] = $m[1];
				}
			}
			$ret['all'] = array_unique( array_merge($ret['alt'], $ret['title'], $ret['link_title']) );
			//var_dump('<pre>', $ret, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			return $ret; 
		}
		
		private function nice_image_name( $image_name ) {
			$image_name = preg_replace("/[^a-zA-Z0-9\s]/", " ", $image_name);
			return $image_name;
		}


        /**
         * get taxonomy info per pagetype: post | page | category | tag | taxonomy
         *
         */
        protected function get_taxonomy($type, $obj) {
        	global $wp_query;
        	$__taxonomyClean = array();
        	
        	if (in_array($type, array('category', 'tag', 'taxonomy'))) {
				//$__postType = $this->getPostType();
				//if ( !empty($__postType) ) $post = $this->post;

	        	$tmpTitle = '';
	        	if ( function_exists( 'single_term_title' ) ) { //Since: 3.1.0 WP version
	        		$tmpTitle = single_term_title( '', false );
	        		//if ( $__postType == 'term' ) $tmpTitle = '';
	        	}
	        	$tmpDesc = '';
	        	if ( function_exists( 'term_description' ) ) { //Since: 2.8.0 WP version
	        		$tmpDesc = term_description();
	        		//if ( $__postType == 'term' ) $tmpDesc = '';
	        	}
	        	if ($type=='category') {
	        		$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : single_cat_title( '', false );
	        		$__taxonomyClean['category_description'] = $tmpDesc!='' ? $tmpDesc : category_description();
					/*
	        		if ( $__postType == 'term' ) {
	        			$__category = get_the_category(); $__categ = array('name' => '', 'desc' => '');
	        			if ($__category[0]) {
	        				$__categ['name'] = $__category[0]->cat_name;
	        				$__categ['desc'] = $__category[0]->description;
	        			}
	        			
	        			$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : $__categ['name'];
	        			$__taxonomyClean['category_description'] = $tmpDesc!='' ? $tmpDesc : $__categ['desc'];
	        		}
					*/
	        		$__taxonomyClean['category'] = $__taxonomyClean['title'];
	        	} else if ($type=='tag') {
	        		$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : single_tag_title( '', false );
	        		$__taxonomyClean['tag_description'] = $tmpDesc!='' ? $tmpDesc : tag_description();
					/*
	        		if ( $__postType == 'term' ) {
	        			$__category = get_tags(); $__categ = array('name' => '', 'desc' => '');
	        			if ($__category[0]) {
	        				$__categ['name'] = $__category[0]->name;
	        				$__categ['desc'] = $__category[0]->description;
	        			}
	        			$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : $__categ['name'];
	        			$__taxonomyClean['tag_description'] = $tmpDesc!='' ? $tmpDesc : $__categ['desc'];
	        		}
					*/
	        		$__taxonomyClean['tag'] = $__taxonomyClean['title'];
	        	} else {
	        		$__taxonomyClean['title'] = $tmpTitle!='' ? $tmpTitle : $obj->name;
	        		$__taxonomyClean['term_description'] = $tmpDesc!='' ? $tmpDesc : $obj->description;
	        		$__taxonomyClean['term'] = $__taxonomyClean['title'];
	        	}
        	}
        	if (in_array($type, array('post', 'page', 'posttype'))) {
	        	if ( function_exists( 'get_the_terms' ) ) { //Since: 2.5.0 WP version
	        		$categories  = get_the_terms( $obj->ID, 'category' );
	        		$tags  = get_the_terms( $obj->ID, 'post_tag' );

	        		// get post type taxonomies
	        		$__taxonomies = get_object_taxonomies( $obj->post_type, 'objects' );
	        		$taxonomies = '';
	        		foreach ( $__taxonomies as $taxonomy_slug => $taxonomy ){
	        			if (in_array($taxonomy_slug, array('category', 'post_tag', 'post_format'))) continue 1;
	        			$taxonomies = get_the_terms( $obj->ID, $taxonomy_slug );
	        		}

	        		$__taxonomyClean = array(	
		 				'categories'			=> $this->getTaxonomyItems( $categories ),
		 				'tags'					=> $this->getTaxonomyItems( $tags ),
		 				'taxonomies'			=> $this->getTaxonomyItems( $taxonomies ),

		 				'category'				=> $this->getTaxonomyItems( $categories, true ),
		 				'category_description'	=> $this->getTaxonomyItems( $categories, true, 'description' ),
		 				'tag'					=> $this->getTaxonomyItems( $tags, true ),
		 				'tag_description'		=> $this->getTaxonomyItems( $tags, true, 'description' ),
		 				'term'					=> $this->getTaxonomyItems( $taxonomies, true ),
						'term_description'		=> $this->getTaxonomyItems( $taxonomies, true, 'description' )
					);
        		}
        	}
        	return $__taxonomyClean;
        }
        
        protected function getTaxonomyItems($items, $first=false, $field='name') {
        	if (is_array($items) && count($items)>0) ;
        	else return '';

        	$__list = array();
        	foreach ( $items as $k=>$v ) {
        		if ($field=='name') $value = $v->name;
        		else if ($field=='description') $value = $v->description;
				else $value = $v->name; //default return name!

        		if ($first) return $value;
        		$__list[] = $value;
        	}
        	return implode(', ', $__list);
        }


		/**
	    * Singleton pattern
	    *
	    * @return pspSEOImages Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
    }
}

// Initialize the pspSEOImages class
//$pspSEOImages = new pspSEOImages();
$pspSEOImages = pspSEOImages::getInstance();