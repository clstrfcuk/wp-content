<?php
/*
* Define class pspTwitterCards
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspTwitterCards') != true) {
    class pspTwitterCards
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		private $plugin_settings = array();

		static protected $_instance;
		
		public $cardTypes = array();
		private static $prefixFields = 'psp_twc_';
		
		private $localizationName;
		
	
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $parent )
        {
			$this->the_plugin = $parent;
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_title_meta_format' );
			
			$this->localizationName = $this->the_plugin->localizationName;
			
			$this->card_types();
        }
        
		/**
	    * Singleton pattern
	    *
	    * @return pspFileEdit Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
	        return self::$_instance;
	    }
	    
		/**
		 * Twitter Cards
		 */
		public function card_types() {
			$twc = array();
			
			// Summary
			$twc['summary'] = array(
				'required' 	=> array('title', 'description'),
				'title'		=> 'Summary Card',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/types/summary-card',
				'fields'		=> array(
					'title'			=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:title',
						'maxlen' 	=> 70,
						'title'		=> __('Title', $this->localizationName),
						'desc'		=> __('Title should be concise and will be truncated at 70 characters.', $this->localizationName)
					),
					'description'		=> array(
						'type' 		=> 'textarea',
						'meta'		=> 'twitter:description',
						'maxlen' 	=> 200,
						'title'		=> __('Description', $this->localizationName),
						'desc'		=> __('A description that concisely summarizes the content of the page, as appropriate for presentation within a Tweet. Do not re-use the title text as the description, or use this field to describe the general services provided by the website. Description text will be truncated at the word to 200 characters.', $this->localizationName)
					),
					'image'		=> array(
						'type'		=> 'upload_image',
						'meta'		=> 'twitter:image',
						'minsize' 	=> '120x120', // width X height
						'filesize' 	=> '1048576', // bytes
						'title'		=> __('Image', $this->localizationName),
						'desc'		=> __('URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. The image must be a minimum size of 120x120px. Images larger than 120x120px will be resized and cropped square based on its longest dimension. Images must be less than 1MB in size.', $this->localizationName)
					)
				)
			);

			// Summary Card with Large Image
			$twc['summary_large_image'] = array_replace_recursive($twc['summary'], array(
				'required' 	=> array('title', 'description'),
				'title'		=> 'Summary Card with Large Image',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/large-image-summary-card',
				'fields'		=> array(
					//'image_src'		=> array(
					//	'type'		=> 'upload_image',
					//	'meta'		=> 'twitter:image', //'twitter:image:src',
					//	'minsize' 	=> '280x150', // width X height
					//	'filesize' 	=> '1048576', // bytes
					//	'title'		=> __('Image', $this->localizationName),
					//	'desc'		=> __('URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images for this Card should be at least 280px in width, and at least 150px in height. Image must be less than 1MB in size.', $this->localizationName)
					//)
					'image'		=> array(
						'minsize' 	=> '280x150', // width X height
						'filesize' 	=> '1048576', // bytes
						'desc'		=> __('URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images for this Card should be at least 280px in width, and at least 150px in height. Image must be less than 1MB in size.', $this->localizationName)
					)
				)
			));
			//unset($twc['summary_large_image']['fields']['image']);
			
			// Photo Card
			$twc['photo'] = array_replace_recursive($twc['summary'], array(
				'required' 	=> array('image'),
				'title'		=> 'Photo Card',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/types/photo-card',
				'fields'		=> array(
					'title'			=> array(
						'desc'		=> __('The title of your content as it should appear in the card. You may specify an empty string if you wish no title to render.', $this->localizationName)
					),
					'image'		=> array(
						'meta'		=> 'twitter:image',
						'minsize' 	=> '280x150', // width X height
						'maxsize' 	=> array(
							'435x375' => 'Web: maximum height of 375px, maximum width of 435px',
							'280x375' => 'Mobile (non-retina displays): maximum height of 375px, maximum width of 280px',
							'560x750' => 'Mobile (retina displays): maximum height of 750px, maximum width of 560px'
						),
						'desc'		=> __('A URL to the image representing the content. Image must be less than 1MB in size.', $this->localizationName)
					),
					'image_width'		=> array(
						'type'		=> 'text',
						'meta'		=> 'twitter:image:width',
						'title'		=> __('Image Width', $this->localizationName),
						'desc'		=> __('Providing width in px helps us more accurately preserve the aspect ratio of the image when resizing.', $this->localizationName)
					),
					'image_height'	=> array(
						'type'		=> 'text',
						'meta'		=> 'twitter:image:height',
						'title'		=> __('Image Height', $this->localizationName),
						'desc'		=> __('Providing height in px helps us more accurately preserve the aspect ratio of the image when resizing.', $this->localizationName)
					)
				)
			));
			unset($twc['photo']['fields']['description']);
			
			// Gallery Card
			$twc['gallery'] = array_replace_recursive($twc['summary'], array(
				'required' 	=> array('image0', 'image1', 'image2', 'image3'),
				'title'		=> 'Gallery Card',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/types/gallery-card',
				'fields'		=> array(
					'title'			=> array(
						'desc'		=> __('The title of your content as it should appear in the card. You may specify an empty string if you wish no title to render.', $this->localizationName)
					),
					'image0'		=> array(
						'type'		=> 'upload_image',
						'meta'		=> 'twitter:image0',
						'minsize' 	=> '120x120', // width X height
						'filesize' 	=> '1048576', // bytes
						'title'		=> __('Image 0', $this->localizationName),
						'desc'		=> __('A URL to the image representing the first photo in your gallery. Image must be less than 1MB in size.', $this->localizationName)
					),
					'image1'		=> array(
						'type'		=> 'upload_image',
						'meta'		=> 'twitter:image1',
						'minsize' 	=> '120x120', // width X height
						'filesize' 	=> '1048576', // bytes
						'title'		=> __('Image 1', $this->localizationName),
						'desc'		=> __('A URL to the image representing the first photo in your gallery. Image must be less than 1MB in size.', $this->localizationName)
					),
					'image2'		=> array(
						'type'		=> 'upload_image',
						'meta'		=> 'twitter:image2',
						'minsize' 	=> '120x120', // width X height
						'filesize' 	=> '1048576', // bytes
						'title'		=> __('Image 2', $this->localizationName),
						'desc'		=> __('A URL to the image representing the first photo in your gallery. Image must be less than 1MB in size.', $this->localizationName)
					),
					'image3'		=> array(
						'type'		=> 'upload_image',
						'meta'		=> 'twitter:image3',
						'minsize' 	=> '120x120', // width X height
						'filesize' 	=> '1048576', // bytes
						'title'		=> __('Image 3', $this->localizationName),
						'desc'		=> __('A URL to the image representing the first photo in your gallery. Image must be less than 1MB in size.', $this->localizationName)
					)
				)
			));
			unset($twc['gallery']['fields']['image']);
			
			// Player Card
			$twc['player'] = array_replace_recursive($twc['summary'], array(
				'required' 	=> array('title', 'description', 'image', 'player', 'player_width', 'player_height'),
				'title'		=> 'Player Card',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/types/player-card',
				'fields'		=> array(
					'image'		=> array(
						'minsize' 	=> array('262x262', '350x196'), // width X height
						'desc'		=> __('Image to be displayed in place of the player on platforms that don\'t support iframes or inline players. You should make this image the same dimensions as your player. Images with fewer than 68,600 pixels (a 262x262 square image, or a 350x196 16:9 image) will cause the player card not to render. Image must be less than 1MB in size.', $this->localizationName)
					),
					'player'		=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:player',
						'title'		=> __('Player', $this->localizationName),
						'desc'		=> __('HTTPS URL to iFrame player. This must be a HTTPS URL which does not generate active mixed content warnings in a web browser. The audio or video player must not require plugins such as Adobe Flash.', $this->localizationName)
					),
					'player_width'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:player:width',
						'title'		=> __('Player width', $this->localizationName),
						'desc'		=> __('Width of IFRAME specified in twitter:player in pixels', $this->localizationName)
					),
					'player_height'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:player:height',
						'title'		=> __('Player height', $this->localizationName),
						'desc'		=> __('Height of IFRAME specified in twitter:player in pixels', $this->localizationName)
					),
					'player_stream'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:player:stream',
						'title'		=> __('Player stream', $this->localizationName),
						'desc'		=> __('URL to raw stream that will be rendered
in Twitter\'s mobile applications directly. If provided, the stream must be delivered in the MPEG-4 container format (the .mp4 extension). The container can store a mix of audio and video with the following codecs:
Video: H.264, Baseline Profile (BP), Level 3.0, up to 640 x 480 at 30 fps.
Audio: AAC, Low Complexity Profile (LC)', $this->localizationName)
					),
					'player_stream_content_type'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:player:stream:content_type',
						'title'		=> __('Player stream content type', $this->localizationName),
						'desc'		=> __('The MIME type/subtype combination that describes the content contained in twitter:player:stream. Takes the form specified in RFC 6381. Currently supported content_type values are those defined in RFC 4337 (MIME Type Registration for MP4).', $this->localizationName)
					)
				)
			));
			
			// Product Card
			$twc['product'] = array_replace_recursive($twc['summary'], array(
				'required' 	=> array('title', 'description', 'image', 'data1', 'label1', 'data2', 'label2'),
				'title'		=> 'Product Card',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/types/product-card',
				'fields'		=> array(
					'image'		=> array(
						'desc'		=> __('A URL to the image representing the content. Image must be less than 1MB in size.', $this->localizationName)
					),
					'image_width'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:image:width',
						'title'		=> __('Image width', $this->localizationName),
						'desc'		=> __('Providing width in px helps us more accurately preserve the the aspect ratio of the image when resizing.', $this->localizationName)
					),
					'image_height'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:image:height',
						'title'		=> __('Image height', $this->localizationName),
						'desc'		=> __('Providing height in px helps us more accurately preserve the the aspect ratio of the image when resizing.', $this->localizationName)
					),
					'data1'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:data1',
						'title'		=> __('Data 1', $this->localizationName),
						'desc'		=> __('This field expects a string, and you can specify values for labels such as price, items in stock, sizes, etc.', $this->localizationName)
					),
					'label1'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:label1',
						'title'		=> __('Label 1', $this->localizationName),
						'desc'		=> __('This field also expects a string, and allows you to specify the types of data you want to offer (price, country, etc.).', $this->localizationName)
					),
					'data2'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:data2',
						'title'		=> __('Data 2', $this->localizationName),
						'desc'		=> __('This field expects a string, and you can specify values for labels such as price, items in stock, sizes, etc.', $this->localizationName)
					),
					'label2'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:label2',
						'title'		=> __('Label 2', $this->localizationName),
						'desc'		=> __('This field also expects a string, and allows you to specify the types of data you want to offer (price, country, etc.).', $this->localizationName)
					)
				)
			));
			
			// App Card
			$twc['app'] = array_replace_recursive($twc['summary'], array(
				'required' 	=> array('app_id_iphone', 'app_id_ipad', 'app_id_googleplay'),
				'title'		=> 'App Card',
				'dev_url'	=> 'https://dev.twitter.com/docs/cards/types/app-card',
				'fields'		=> array(
					'app_description'		=> array(
						'type' 		=> 'textarea',
						'meta'		=> 'twitter:description',
						'maxlen' 	=> 200,
						'title'		=> __('Description', $this->localizationName),
						'desc'		=> __('You can use this as a more concise description than what you may have on the app store. This field has a maximum of 200 characters.', $this->localizationName)
					),
					'app_name_iphone'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:name:iphone',
						'title'		=> __('App Name Iphone', $this->localizationName),
						'desc'		=> __('Your app\'s name.', $this->localizationName)
					),
					'app_name_ipad'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:name:ipad',
						'title'		=> __('App Name Ipad', $this->localizationName),
						'desc'		=> __('Your app\'s name.', $this->localizationName)
					),
					'app_name_googleplay'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:name:googleplay',
						'title'		=> __('App Name Googleplay', $this->localizationName),
						'desc'		=> __('Your app\'s name.', $this->localizationName)
					),
					'app_id_iphone'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:id:iphone',
						'title'		=> __('App Id Iphone', $this->localizationName),
						'desc'		=> __('String value, and should be the numeric representation of your app ID in the App Store (.i.e. "307234931")..', $this->localizationName)
					),
					'app_id_ipad'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:id:ipad',
						'title'		=> __('App Id Ipad', $this->localizationName),
						'desc'		=> __('String value, should be the numeric representation of your app ID in the App Store (.i.e. "307234931").	.', $this->localizationName)
					),
					'app_id_googleplay'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:id:googleplay',
						'title'		=> __('App Id Googleplay', $this->localizationName),
						'desc'		=> __('String value, and should be the numeric representation of your app ID in Google Play (.i.e. "com.android.app").', $this->localizationName)
					),
					'app_url_iphone'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:url:iphone',
						'title'		=> __('App Url Iphone', $this->localizationName),
						'desc'		=> __('Your app\'s custom URL scheme (you must include "://" after your scheme name).', $this->localizationName)
					),
					'app_url_ipad'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:url:ipad',
						'title'		=> __('App Url Ipad', $this->localizationName),
						'desc'		=> __('Your app\'s custom URL scheme.', $this->localizationName)
					),
					'app_url_googleplay'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:url:googleplay',
						'title'		=> __('App Url Googleplay', $this->localizationName),
						'desc'		=> __('Your app\'s custom URL scheme.', $this->localizationName)
					),
					'app_country'	=> array(
						'type' 		=> 'text',
						'meta'		=> 'twitter:app:country',
						'title'		=> __('Country', $this->localizationName),
						'desc'		=> __('If your application is not available in the US App Store, you must set this value to the two-letter country code for the App Store that contains your application.', $this->localizationName)
					)
				)
			));
			unset($twc['app']['fields']['description']);
			unset($twc['app']['fields']['title']);
			unset($twc['app']['fields']['image']);

			$this->cardTypes = $twc;
			return $twc;
		}
		
		public function set_options( $defaults=array(), $pms=array() ) {
			if( !is_array($defaults) ) $defaults = array();

			extract($pms);
			
			if ( empty($card_type) ) $card_type = 'summary';
			$currentCard = $this->cardTypes["$card_type"];
			$currentCardFields = $currentCard['fields'];
			
			$boxId = 'title_meta_format'; $boxTitle = '';
			switch ($page) {
				case 'home':
					$boxId = 'title_meta_format';
					$boxTitle = 'Homepage';
					break;
					
				case 'app':
					$boxId = 'title_meta_format';
					$boxTitle = 'App';
					break;
					
				case 'post':
				case 'post-app':
					$boxId = 'twittercards_meta'; // change box ID so that settings file will not use db option values!
					$boxTitle = 'Box';
					break;
			}
			
			$__options = array();
			foreach ($currentCardFields as $k => $v) {

				$key = self::$prefixFields . $k;
				$__options["$key"] = array(
					'type' 		=> $v['type'],
					'size' 		=> 'large',
					'title' 		=> $v['title'] . ':',
					'std'		=> '',
					'desc' 		=> (in_array($k, $currentCard['required']) ? '(<strong>required</strong>) ' : '') . $v['desc']
				);
				if ( $v['type'] == 'upload_image' ) {
					$__imgSize = array(0, 0);
					if ( isset($v['minsize']) ) $__imgSize = explode('x', (string) $v['minsize'][0]);

					$__options["$key"] = array_merge($__options["$key"], array(
						'value' 	=> __('Upload image', $this->localizationName),
						'thumbSize' 	=> array(
							'w' => isset($__imgSize[0]) && $__imgSize[0] > 0 ? "'".$__imgSize[0]."'" : '100',
							'h' => isset($__imgSize[1]) && $__imgSize[1] > 0 ? "'".$__imgSize[1]."'" : '100',
							'zc' => '2',
						)
					));
				}
			}

			$options = array(
				array(
					/* define the form_sizes  box */
					$boxId => array(
						'title' 	=> '<div style="position:relative;">' . __( $boxTitle . ' Twitter Card Options', $this->localizationName) . '<div style="position:absolute; top:0; right:0;"><a href="' . $currentCard['dev_url'] . '" target="_blank">' . __('Twitter Cards: ', $this->localizationName) . $currentCard['title'] . '</a></div></div>',
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> false, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> $__options
					)
				)
			);

			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box) {
						//if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$elm_id];
								}
							}
						//}
					}
				}

				// than update the options for returning
				$options = array( $option );
			}

			return $options;
		}
		
		public function build_options($pms=array()) {
			extract($pms);

			// load the settings template class
			require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your psp_aaInterfaceTemplates
			$psp_aaInterfaceTemplates = new psp_aaInterfaceTemplates($this->the_plugin->cfg);

			$options = array();
			switch ($page) {
				case 'home':
					$options = $this->plugin_settings;
					break;

				case 'app':
					$options = $this->plugin_settings;
					break;

				case 'post':
					$box_taxonomy 	= isset($box_taxonomy) ? (string) $box_taxonomy : 'post';
					$box_termid			= isset($box_termid) ? (int) $box_termid : 0;
					if ( 'post' != $box_taxonomy ) {
						$__objTax = (object) array('term_id' => $box_termid, 'taxonomy' => $box_taxonomy);

						$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $__objTax );
						if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
							$psp_current_taxseo = array();

						$post_meta = $this->the_plugin->get_psp_meta( $__objTax, $psp_current_taxseo );
					}
					else {
						$post_meta = $this->the_plugin->get_psp_meta( $post_id );
					}
					$options = $post_meta;
					break;

				case 'post-app':
					$box_taxonomy 	= isset($box_taxonomy) ? (string) $box_taxonomy : 'post';
					$box_termid			= isset($box_termid) ? (int) $box_termid : 0;
					if ( 'post' != $box_taxonomy ) {
						$__objTax = (object) array('term_id' => $box_termid, 'taxonomy' => $box_taxonomy);

						$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $__objTax );
						if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
							$psp_current_taxseo = array();

						$post_meta = $this->the_plugin->get_psp_meta( $__objTax, $psp_current_taxseo );
					}
					else {
						$post_meta = $this->the_plugin->get_psp_meta( $post_id );
					}
					// if ( is_array($post_meta) && !empty($post_meta) && isset($post_meta['psp_twc_app_description']) )
						$options = $post_meta;
					break;
			}

			// then build the html, and return it as string
			$html_options = $psp_aaInterfaceTemplates->bildThePage( $this->set_options( $options, $pms ) , $this->the_plugin->alias, array(), false);
			$html_options = str_replace('<div class="psp-message" id="psp-status-box" style="display:none;"></div>', '', $html_options);
			return $html_options;
		}
		
		public function save_meta($pms=array()) {
			extract($pms);
			
			$card_type = isset($_REQUEST['psp_twc_post_cardtype']) ? strtolower($_REQUEST['psp_twc_post_cardtype']) : 'none';
			$app_card_type = isset($_REQUEST['psp_twc_app_isactive']) ? strtolower($_REQUEST['psp_twc_app_isactive']) : 'no';
			$thumbsize = isset($_REQUEST['psp_twc_post_thumbsize']) ? $_REQUEST['psp_twc_post_thumbsize'] : 'none';
			
			$post_cardtypes = array();
			if ( isset($card_type) && !empty($card_type) && !in_array($card_type, array('default', 'none')) ) {
				$post_cardtypes[] = $card_type;
			}
			if ( isset($app_card_type) && !empty($app_card_type) && $app_card_type=='yes' ) {
				$post_cardtypes[] = 'app';
			}

			$__options = array();
			$__options['psp_twc_post_cardtype'] = $card_type;
			$__options['psp_twc_app_isactive'] = $app_card_type;
			$__options['psp_twc_post_thumbsize'] = $thumbsize;
			
			if ( empty($post_cardtypes) ) return $__options;

			foreach ($post_cardtypes as $kk=>$vv) {

				$currentCard = $this->cardTypes["$vv"];
				$currentCardFields = $currentCard['fields'];
				
				foreach ($currentCardFields as $k => $v) {
	
					$key = self::$prefixFields . $k;
					$__options["$key"] = trim( $_REQUEST["$key"] );
				}
			}
			return $__options;
		}

		public function get_frontend_meta($meta=array(), $default=array(), $page='', $post=null) {

			$opt = $this->plugin_settings; //$this->the_plugin->get_theoption('psp_title_meta_format')
			
			$__options = array();

			$app_changemeta = false;
			switch ($page) {
				case 'home':
					$card_type = isset($meta['psp_twc_home_type']) ? $meta['psp_twc_home_type'] : ''; //wp_options psp_title_meta_format
					$app_card_type = isset($meta['psp_twc_home_app']) ? $meta['psp_twc_home_app'] : ''; //wp_options psp_title_meta_format
					break;

				case 'post':
				case 'taxonomy':
					if ( 'post' == $page ) {
						$post_type = is_object($post) && isset($post->post_type) ? $post->post_type : '';

						$key_cardtype = 'psp_twc_cardstype_default';
						$key_app = 'psp_twc_apptype_default';
					}
					else {
						$post_type = is_object($post) && isset($post->taxonomy) ? $post->taxonomy : '';
						$post_type = in_array($post_type, array('category', 'post_tag')) ? $post_type : '_custom_taxonomy';

						$key_cardtype = 'psp_twc_cardstype_default_taxonomy';
						$key_app = 'psp_twc_apptype_default_taxonomy';
					}

					$card_type = isset($meta['psp_twc_post_cardtype']) ? $meta['psp_twc_post_cardtype'] : ''; //wp_postmeta psp_meta
					if ( empty($card_type) || ( 'default' == $card_type ) ) {
						$card_type = 'none';
						if ( isset($opt["$key_cardtype"], $opt["$key_cardtype"]["$post_type"]) && !empty($opt["$key_cardtype"]) ) { //wp_options psp_title_meta_format
							$card_type = $opt["$key_cardtype"]["$post_type"];
						}
					}

					$app_card_type = isset($meta['psp_twc_app_isactive']) ? $meta['psp_twc_app_isactive'] : ''; //wp_postmeta psp_meta
					if ( empty($app_card_type) || in_array($app_card_type, array('default', 'default2')) ) {
						//default = Use Website Generic App Twitter Card Type / compatibility with old version!
						if ( 'default' == $app_card_type ) {
							$app_card_type = 'no';
							// we've created a Generic App Twitter Card Type
							if ( isset($opt['psp_twc_site_app']) && !empty($opt['psp_twc_site_app']) && $opt['psp_twc_site_app']=='yes' ) { //wp_options psp_title_meta_format
								$app_card_type = 'yes';
							}
						}
						// the new and real default!
						else {
							$app_card_type = 'no';
							if ( isset($opt["$key_app"], $opt["$key_app"]["$post_type"]) && !empty($opt["$key_app"]) ) { //wp_options psp_title_meta_format
								$app_card_type = $opt["$key_app"]["$post_type"];
							}
						}

						// default & allowed => override wp_postmeta psp_meta with wp_options psp_title_meta_format
						if ( 'yes' == $app_card_type ) {
							$app_changemeta = true;
						}
					}
					break;
			}
			//var_dump('<pre>',$card_type, $app_card_type,'</pre>');  

			$post_cardtypes = array();
			if ( isset($card_type) && !empty($card_type) && $card_type!='none' ) {
				$post_cardtypes[] = $card_type;
				$__options['twitter:card'] = $card_type;
			}
			if ( isset($app_card_type) && !empty($app_card_type) && ( 'yes' == $app_card_type ) ) {
				$post_cardtypes[] = 'app';
				if ( !isset($__options['twitter:card']) || empty($__options['twitter:card']) ) {
					$__options['twitter:card'] = 'app';
				}
			}

			if ( empty($post_cardtypes) ) return $__options;

			$cc = 0;
			foreach ($post_cardtypes as $kk=>$vv) {

				$currentCard = $this->cardTypes["$vv"];
				$currentCardFields = $currentCard['fields'];

				$theMeta = $meta;
				if ( $vv=='app' && $app_changemeta ) {
					$theMeta = $opt;
				}

				foreach ($currentCardFields as $k => $v) {

					$key = self::$prefixFields . $k;
					$meta_key = $v['meta'];

					// don't overwrite with app tag if tag is already set
					if ( $vv=='app' && isset($__options["$meta_key"]) && !empty($__options["$meta_key"]) ) ;
					// do it
					else {
						$__options["$meta_key"] = isset($theMeta["$key"]) ? trim( $theMeta["$key"] ) : '';
						if ( $cc && ( 'psp_twc_app_description' == $key ) ) {
							$__options["$meta_key"] = '';
						}
					}
				}
				$cc++;
			}
			//var_dump('<pre>', $__options, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;    
			return $__options;
		}
    }
}

// Initialize the pspTwitterCards class
//$pspTwitterCards = new pspTwitterCards();
