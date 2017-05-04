<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

if ( ! function_exists('__metaRobotsList') ) {
function __metaRobotsList() {
	return array(
		'noindex'	=> 'noindex', //support by: Google, Yahoo!, MSN / Live, Ask
		'nofollow'	=> 'nofollow', //support by: Google, Yahoo!, MSN / Live, Ask
		'noarchive'	=> 'noarchive', //support by: Google, Yahoo!, MSN / Live, Ask
		'noodp'		=> 'noodp' //support by: Google, Yahoo!, MSN / Live
	);
}
}
$__metaRobotsList = __metaRobotsList();

if ( ! function_exists('psp_OpenGraphTypes') ) {
function psp_OpenGraphTypes( $istab = '', $is_subtab='', $what='' ) {
	global $psp;
	
	ob_start();

	if ( 'posttype' == $what ) {
		$uniqueKey = 'social_opengraph_default';
		$post_types = get_post_types(array(
			'public'   => true
		));
		//$post_types['attachment'] = __('Images', 'psp');
		//unset media - images | videos are treated as belonging to post, pages, custom post types
		unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
	}
	else {
		$uniqueKey = 'social_opengraph_default_taxonomy';
		$post_types = array(
			'category' 		=> __('Category', 'psp'),
			'post_tag' 	=> __('Tag', 'psp'),
			'_custom_taxonomy' 	=> __('Custom Taxonomy', 'psp')
		);
	}
	
	$options = $psp->get_theoption('psp_title_meta_format');
?>
<div class="panel-body psp-panel-body psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label class="psp-form-label"><?php _e('Default OpenGraph Type:', 'psp'); ?>	</label>
	<div class="psp-form-item large">
	<!--<span class="formNote">&nbsp;</span>-->
	<?php
	if (1) {
			//https://developers.facebook.com/docs/reference/opengraph/object-type/object/
			$opengraph_defaults = array(
				'Internet' 	=> array(
					'article'				=> __('Article', 'psp'),
					'blog'					=> __('Blog', 'psp'),
					'profile'				=> __('Profile', 'psp'),
					'website'				=> __('Website', 'psp')
				),
				'Products' 	=> array(
					'book'					=> __('Book', 'psp')
				),
				'Music' 	=> array(
					'music.album'			=> __('Album', 'psp'),
					'music.playlist'		=> __('Playlist', 'psp'),
					'music.radio_station'	=> __('Radio Station', 'psp'),
					'music.song'			=> __('Song', 'psp')
				),
				'Videos' => array(
					'video.movie'			=> __('Movie', 'psp'),
					'video.episode'			=> __('TV Episode', 'psp'),
					'video.tv_show'			=> __('TV Show', 'psp'),
					'video.other'			=> __('Video', 'psp')
				),
				'Object' => array(
					'object'			=> __('Object', 'psp')
				)
			);
	}
	foreach ($post_types as $key => $value){
		
		$val = ( 'posttype' == $what ? 'article' : 'object' );
		if( isset($options["$uniqueKey"]) && isset($options["$uniqueKey"][$key]) ){
			$val = $options["$uniqueKey"][$key];
		}
		?>
		<div class="psp-socialmeta-opengraph-miniwrap">
		<label for="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" name="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" style="width:120px;">
			<option value="none"><?php _e('None', 'psp'); ?></option>
			<?php
			foreach ($opengraph_defaults as $k => $v){
				echo '<optgroup label="' . $k . '">';
				foreach ($v as $kk => $vv){
					echo 	'<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
				}
				echo '</optgroup>';
			}
			?>
		</select>
		</div>
		<?php
	}
	?>
	</div>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
}

if ( ! function_exists('psp_TwitterCardsTypes') ) {
function psp_TwitterCardsTypes( $istab = '', $is_subtab='', $what='' ) {
	global $psp;
	
	ob_start();

	if ( 'posttype' == $what ) {
		$uniqueKey = 'psp_twc_cardstype_default';
		$post_types = get_post_types(array(
			'public'   => true
		));
		//$post_types['attachment'] = __('Images', 'psp');
		//unset media - images | videos are treated as belonging to post, pages, custom post types
		unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
	}
	else {
		$uniqueKey = 'psp_twc_cardstype_default_taxonomy';
		$post_types = array(
			'category' 		=> __('Category', 'psp'),
			'post_tag' 	=> __('Tag', 'psp'),
			'_custom_taxonomy' 	=> __('Custom Taxonomy', 'psp')
		);
	}
	
	$options = $psp->get_theoption('psp_title_meta_format');
?>
<div class="panel-body psp-panel-body psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label class="psp-form-label"><?php _e('Default Twitter Cards Type:', 'psp'); ?>	</label>
	<div class="psp-form-item large">
	<!--<span class="formNote">&nbsp;</span>-->
	<?php
	if (1) {
			$opengraph_defaults = array(
				'summary'				=> __('Summary Card', 'psp'),
				'summary_large_image'		=> __('Summary Card with Large Image', 'psp'),
				//'photo'					=> __('Photo Card', 'psp'),
				//'gallery'				=> __('Gallery Card', 'psp'),
				'player'				=> __('Player Card', 'psp'),
				//'product'				=> __('Product Card', 'psp')
			);
	}
	foreach ($post_types as $key => $value){
		
		$val = 'summary';
		if( isset($options["$uniqueKey"]) && isset($options["$uniqueKey"][$key]) ){
			$val = $options["$uniqueKey"][$key];
		}
		if ( ! in_array($val, array('none', 'summary', 'summary_large_image', 'player')) ) {
			$val = 'summary';
		}
		?>
		<div class="psp-tw-cardstype-miniwrap">
		<label for="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" name="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" style="width:200px;">
			<option value="none"><?php _e('None', 'psp'); ?></option>
			<?php
			foreach ($opengraph_defaults as $k => $v){
				echo 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
			}
			?>
		</select>
		</div>
		<?php
	}
	?>
	</div>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
}

if ( ! function_exists('psp_AppTypeUse') ) {
function psp_AppTypeUse( $istab = '', $is_subtab='', $what=''  ) {
	global $psp;
	
	ob_start();

	if ( 'posttype' == $what ) {
		$uniqueKey = 'psp_twc_apptype_default';
		$post_types = get_post_types(array(
			'public'   => true
		));
		//$post_types['attachment'] = __('Images', 'psp');
		//unset media - images | videos are treated as belonging to post, pages, custom post types
		unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
	}
	else {
		$uniqueKey = 'psp_twc_apptype_default_taxonomy';
		$post_types = array(
			'category' 		=> __('Category', 'psp'),
			'post_tag' 	=> __('Tag', 'psp'),
			'_custom_taxonomy' 	=> __('Custom Taxonomy', 'psp')
		);
	}
	
	$options = $psp->get_theoption('psp_title_meta_format');
?>
<div class="panel-body psp-panel-body psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label class="psp-form-label"><?php _e('Use App Card Type:', 'psp'); ?>	</label>
	<div class="psp-form-item large">
	<!--<span class="formNote">&nbsp;</span>-->
	<?php
	if (1) {
			$opengraph_defaults = array(
				'yes'			=> __('Yes', 'psp'),
				'no'			=> __('No', 'psp'),
			);
	}
	foreach ($post_types as $key => $value){
		
		$val = 'no';
		if( isset($options["$uniqueKey"]) && isset($options["$uniqueKey"][$key]) ){
			$val = $options["$uniqueKey"][$key];
		}
		?>
		<div class="psp-tw-cardstype-miniwrap">
		<label for="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" style="display:inline;float:none;"><?php echo ucfirst(str_replace('_', ' ', $value));?>:</label>
		&nbsp;
		<select id="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" name="<?php echo $uniqueKey; ?>[<?php echo $key;?>]" style="width:200px;">
			<?php
			foreach ($opengraph_defaults as $k => $v){
				echo 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
			}
			?>
		</select>
		</div>
		<?php
	}
	?>
	</div>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
}

if ( ! function_exists('psp_TwitterCardsOptions') ) {
function psp_TwitterCardsOptions( $istab = '', $is_subtab='', $type='' ) {
	global $psp;
	
	$options = $psp->get_theoption('psp_title_meta_format');

	ob_start();
?>
	<div class="psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>" id="<?php echo $type=='home' ? 'psp-twittercards-home-response' : 'psp-twittercards-app-response'; ?>" style="position:relative;"></div>
	<script>
// Initialization and events code for the app
pspTwitterCards_modoptions = (function ($) {
	"use strict";
	
	// public
	var debug_level = 0;
	var maincontainer = null;
	var loading = null;
	
	var ajaxurl		= '<?php echo admin_url('admin-ajax.php');?>',
		  type		= '<?php echo $type; ?>';
	
	var ajaxBox = ( type=='home' ? $('#psp-twittercards-home-response') : $('#psp-twittercards-app-response') );
	//console.log( ajaxBox ); 
	
	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $("#psp-wrapper");
			loading = maincontainer.find("#main-loading");
	
			triggers();
		});
	})();
	
	function ajaxLoading()
	{
		var loading = $('<div id="psp-ajaxLoadingBox" class="psp-panel-widget">loading</div>');
		// append loading
		ajaxBox.html(loading);
	}
	
	function get_options( type ) {
			var __type = type || '';
			if ( $.trim(__type)=='' ) return false;
			
			ajaxLoading();

			var theTrigger = ( __type=='home' ? $('#psp_twc_home_type') : $('#psp_twc_site_app') ), theTriggerVal = theTrigger.val();
			var theResp = ajaxBox;

			if ( $.inArray(theTriggerVal, ['none', 'no']) > -1 ) {
				theResp.html('').hide();
				return false;
			}

			$.post(ajaxurl, {
				'action' 		: 'pspTwitterCards',
				'sub_action'		: 'getCardTypeOptions',
				'card_type'		: __type=='home' ? $('#psp_twc_home_type').val() : 'app',
				'page'			: __type=='home' ? 'home' : 'app'
			}, function(response) {

				if ( response.status == 'valid' ) {
					theResp.html( response.html ).show();
					pspFreamwork.makeTabs();
					
					$('#psp-twittercards-app-response').find('input#box_id, input#box_nonce').remove();
					$('#psp-twittercards-home-response').find('input#box_id, input#box_nonce').remove();
					return true;
				}
				return false;
			}, 'json');		
	}
	
	// triggers
	function triggers()
	{
		get_options( type );

		if ( type=='home' ) {
			$('#psp_twc_home_type').on('change', function (e) {
				e.preventDefault();
	
				get_options( type );
			});
		} else {
			$('#psp_twc_site_app').on('change', function (e) {
				e.preventDefault();
	
				get_options( type );
			});
		}
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

if ( ! function_exists('psp_TwitterCardsImageFind') ) {
function psp_TwitterCardsImageFind( $istab = '', $is_subtab='', $what='' ) {
	global $psp;
	
	ob_start();

	if ( 'posttype' == $what ) {
		$uniqueKey = 'psp_twc_image_find';
		$uniqueKey_cf = 'psp_twc_image_customfield';
	}
	else {
		$uniqueKey = 'psp_twc_image_find_taxonomy';
		$uniqueKey_cf = 'psp_twc_image_customfield_taxonomy';
	}

	$options = $psp->get_theoption('psp_title_meta_format');

	$val = ( 'posttype' == $what ? 'featured' : 'customfield' );
	if ( isset($options["$uniqueKey"]) ) {
		$val = $options["$uniqueKey"];
	}
?>
<div class="panel-body psp-panel-body psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label class="psp-form-label"><?php _e('How to choose Image:', 'psp'); ?></label>
	<div class="psp-form-item large">
	<!--<span class="formNote">&nbsp;</span>-->
		<select id="<?php echo $uniqueKey; ?>" name="<?php echo $uniqueKey; ?>" style="width:350px;">
			<?php
			if ( 'posttype' == $what ) {
				$image_find = array(
					'content'				=> __('Choose first image from the post | page content', 'psp'),
					'featured'				=> __('Featured image for the post | page', 'psp'),
					'customfield'			=> __('Choose a custom field for image', 'psp')
				);
			}
			else {
				$image_find = array(
					'content'				=> __('Choose first image from the category | tag content', 'psp'),
					//'featured'				=> __('Featured image for the post | page', 'psp'),
					'customfield'			=> __('Choose a custom field for image', 'psp')
				);
			}
			foreach ($image_find as $k => $v){
				echo 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
			}
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
	</div>
	<div class="psp-form-item small" style="margin-top:5px;">
		<span class=""><?php echo __('Choose custom field:', 'psp'); ?></span>&nbsp;
		<input id="<?php echo $uniqueKey_cf; ?>" type="text" value="" name="<?php echo $uniqueKey_cf; ?>">
	</div>
</div>
	<script>
// Initialization and events code for the app
pspTwitterCards_image_find = (function ($) {
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
		
		if ( val =='customfield' ) {
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

if ( ! function_exists('psp_CustomPosttypeTaxonomyMeta') ) {
function psp_CustomPosttypeTaxonomyMeta( $istab = '', $is_subtab='', $params=array() ) {
	global $psp;
	
	$fields_name = array(
		'title'			=> array(
			'name'			=> __('Title Format', 'psp'),
			'std'				=> array(
				'posttype'		=> '', //'{title} | {site_title}',
				'taxonomy'	=> '', //'{title} | {site_title}',
			),
		),
		'desc'		=> array(
			'name'			=> __('Meta Description', 'psp'),
			'std'				=> array(
				'posttype'		=> '', //'{short_description} | {site_description}',
				'taxonomy'	=> '', //'{term_description}',
			),
		),
		'kw'			=> array(
			'name'			=> __('Meta Keywords', 'psp'),
			'std'				=> array(
				'posttype'		=> '', //'{keywords}',
				'taxonomy'	=> '', //'{keywords}',
			),
		),
		'robots'		=> array(
			'name'			=> __('Meta Robots', 'psp'),
			'std'				=> array(
				'posttype'		=> array(),
				'taxonomy'	=> array(),
			),
		)
	);
	
	$params = array_merge(array(
		'builtin'			=> false,
		'what'			=> '',
		'field'				=> '',
	), $params);
	extract( $params );
	
	ob_start();
	
	$pms = array(
		'public'   => true,
	);
	if ( $builtin === true || $builtin === false  ) {
		$pms = array_merge($pms, array(
			'_builtin' => $builtin, // exclude post, page, attachment
		));
	}

	if ( 'posttype' == $what ) {
		$uniqueKey = 'posttype_custom';
		$post_types = get_post_types($pms, 'objects');
		//unset media - images | videos /they are treated as belonging to post, pages, custom post types
		unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
		
		$field_desc = __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>';
	}
	else {
		$uniqueKey = 'taxonomy_custom';
		$post_types = get_taxonomies($pms, 'objects');
		unset($post_types['post_format'], $post_types['nav_menu'], $post_types['link_category']);
		
		$field_desc = __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {term} {term_description}' . '</span>';
	}
	
	if ( 'robots' == $field ) {
		$field_desc = __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp');
	}
	
	$options = $psp->get_theoption('psp_title_meta_format');

	foreach ($post_types as $key => $value) {
		$field_label = $value->labels->name;
		$field_label = $psp->get_taxonomy_nice_name( $field_label );
		$field_label = str_replace('_', ' ', $field_label);
		$field_label = ucfirst($field_label);
		$field_label = $field_label . '<br/><span>' . $fields_name["$field"]['name'] . ':</span>';
?>

<div class="panel-body psp-panel-body psp-form-row<?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">
	<label class="psp-form-label psp-metatags-tagtitle"><?php echo $field_label; ?></label>
	<div class="psp-form-item large">

<?php
		//:: start current value
		$val = '';
		if ( isset($fields_name["$field"], $fields_name["$field"]['std'], $fields_name["$field"]['std']["$what"]) ) {
			$val = $fields_name["$field"]['std']["$what"];
		}
		// compatibility with old version, where exists the following keys: product_(title|desc|kw|robots)
		if ( ( 'product' == $key ) && isset($options["product_{$field}"]) && ! empty($options["product_{$field}"]) ) {
			$val = $options["product_{$field}"];
		}
		if( isset($options["$uniqueKey"]) && isset($options["$uniqueKey"][$field]) && isset($options["$uniqueKey"][$field][$key]) ){
			$val = $options["$uniqueKey"][$field][$key];
		}
		//:: end current value
		
		if ( 'title' == $field || 'kw' == $field ) {
		?>
			<input type="text" id="<?php echo $uniqueKey; ?>[<?php echo $field;?>][<?php echo $key;?>]" name="<?php echo $uniqueKey; ?>[<?php echo $field;?>][<?php echo $key;?>]" value="<?php echo $val; ?>" style="width:400px;">
		<?php
		} else if ( 'desc' == $field ) {
		?>
			<textarea id="<?php echo $uniqueKey; ?>[<?php echo $field;?>][<?php echo $key;?>]" name="<?php echo $uniqueKey; ?>[<?php echo $field;?>][<?php echo $key;?>]" style="height:120px;" cols="120"><?php echo $val; ?></textarea>
		<?php
		} else if ( 'robots' == $field ) {
		?>
			<select multiple="multiple" size="6" id="<?php echo $uniqueKey; ?>[<?php echo $field;?>][<?php echo $key;?>]" name="<?php echo $uniqueKey; ?>[<?php echo $field;?>][<?php echo $key;?>][]" style="width:400px;">
				<?php
					foreach (array('noindex', 'nofollow', 'noarchive', 'noodp') as $metarobot_val) {
						$is_selected = in_array($metarobot_val, (array) $val) ? 'selected="selected"' : '';
						echo '<option value="'.$metarobot_val.'" '.$is_selected.'>'.$metarobot_val.'</option>';
					}
				?>
			</select>
		<?php
		}
		?>
		<span class="psp-form-note"><?php echo $field_desc; ?></span>

	</div>
</div>
		
<?php
	} // end foreach post_types
	?>

<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
}


global $psp;

if ( $psp->is_buddypress() ) {
require_once('buddypress.options.php');
}

//echo json_encode(
$__psp_mfo = 
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'title_meta_format' => array(
				'title' 	=> __('Title & Meta Formats', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// tabs
				'tabs'	=> array(
					'__tab1'	=> array(__('Format Tags List', 'psp'), 'help_format_tags'),
					'__tab2'	=> array(__('Title Format', 'psp'), 'force_title,home_title,post_title,page_title,posttype_title,product_title,category_title,tag_title,taxonomy_title,archive_title,author_title,search_title,404_title,pagination_title,use_pagination_title'),
					'__tab3'	=> array(__('Meta Description', 'psp'), 'home_desc,post_desc,page_desc,posttype_desc,product_desc,category_desc,tag_desc,taxonomy_desc,archive_desc,author_desc,pagination_desc,use_pagination_desc'),
					'__tab4'	=> array(__('Meta Keywords', 'psp'), 'home_kw,post_kw,page_kw,posttype_kw,product_kw,category_kw,tag_kw,taxonomy_kw,archive_kw,author_kw,pagination_kw,use_pagination_kw'),
					'__tab5'	=> array(__('Meta Robots', 'psp'), 'home_robots,post_robots,page_robots,posttype_robots,product_robots,category_robots,tag_robots,taxonomy_robots,archive_robots,author_robots,search_robots,404_robots,pagination_robots,use_pagination_robots, help_meta_robots'),
					'__tab6'	=> array(__('Social Meta', 'psp'), 'social_use_meta,social_include_extra,social_validation_type,social_site_title,social_default_img,social_home_title,social_home_desc,social_home_img,social_home_type,social_opengraph_default,social_fb_app_id, help_psp_social_home, social_customfield_post, social_opengraph_default_taxonomy, social_customfield_taxonomy'),
					'__tab7'	=> array(__('Twitter Cards', 'psp'), 'psp_twc_use_meta,psp_twc_website_account,psp_twc_website_account_id,psp_twc_creator_account,psp_twc_creator_account_id,psp_twc_default_img,psp_twc_cardstype_default,psp_twc_apptype_default,psp_twc_home_app,psp_twc_home_type,psp_twc_site_app,help_psp_twc_post,help_psp_twc_home,help_psp_twc_app,psp_twc_image_find,psp_twc_thumb_sizes,psp_twc_thumb_crop, help_psp_twc_taxonomy,psp_twc_cardstype_default_taxonomy,psp_twc_apptype_default_taxonomy,psp_twc_image_find_taxonomy')
				),
				
				// tabs
				'subtabs'	=> array(
					'__tab1'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'help_format_tags')),
					'__tab2'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_title,post_title,page_title,category_title,tag_title,archive_title,author_title,search_title,404_title,pagination_title,use_pagination_title'),
						'__subtab2' => array(
							__('Custom Post Type', 'psp'), 'posttype_title, product_title'),
						'__subtab3' => array(
							__('Custom Taxonomy', 'psp'), 'taxonomy_title')),
					'__tab3'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_desc,post_desc,page_desc,category_desc,tag_desc,archive_desc,author_desc,pagination_desc,use_pagination_desc'),
						'__subtab2' => array(
							__('Custom Post Type', 'psp'), 'posttype_desc, product_desc'),
						'__subtab3' => array(
							__('Custom Taxonomy', 'psp'), 'taxonomy_desc')),
					'__tab4'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_kw,post_kw,page_kw,category_kw,tag_kw,archive_kw,author_kw,pagination_kw,use_pagination_kw'),
						'__subtab2' => array(
							__('Custom Post Type', 'psp'), 'posttype_kw, product_kw'),
						'__subtab3' => array(
							__('Custom Taxonomy', 'psp'), 'taxonomy_kw')),
					'__tab5'	=> array(
						'__subtab1' => array(
							__('Wordpress', 'psp'), 'home_robots,post_robots,page_robots,category_robots,tag_robots,archive_robots,author_robots,search_robots,404_robots,pagination_robots,use_pagination_robots, help_meta_robots'),
						'__subtab2' => array(
							__('Custom Post Type', 'psp'), 'posttype_robots, product_robots, help_meta_robots'),
						'__subtab3' => array(
							__('Custom Taxonomy', 'psp'), 'taxonomy_robots, help_meta_robots')),
					'__tab6'	=> array(
						'__subtab1' => array(
							__('General', 'psp'), 'social_use_meta,social_include_extra,social_validation_type,social_site_title,social_default_img,social_fb_app_id'),
						'__subtab2' => array(
							__('Posts, Pages', 'psp'), 'social_opengraph_default, social_customfield_post'),
						'__subtab3' => array(
							__('Categories, Tags', 'psp'), 'social_opengraph_default_taxonomy, social_customfield_taxonomy'),
						'__subtab4' => array(
							__('Homepage - default', 'psp'), 'social_home_title,social_home_desc,social_home_img,social_home_type, help_psp_social_home')),
					'__tab7'	=> array(
						'__subtab1' => array(
							__('General', 'psp'), 'psp_twc_use_meta,psp_twc_website_account,psp_twc_website_account_id,psp_twc_creator_account,psp_twc_creator_account_id,psp_twc_default_img,psp_twc_thumb_sizes,psp_twc_thumb_crop'),
						'__subtab2' => array(
							__('Posts, Pages', 'psp'), 'help_psp_twc_post,psp_twc_cardstype_default,psp_twc_apptype_default,psp_twc_image_find'),
						'__subtab3' => array(
							__('Categories, Tags', 'psp'), 'help_psp_twc_taxonomy,psp_twc_cardstype_default_taxonomy,psp_twc_apptype_default_taxonomy,psp_twc_image_find_taxonomy'),
						'__subtab4' => array(
							__('Generic App Card Type for website', 'psp'), 'psp_twc_site_app,help_psp_twc_app'),
						'__subtab5' => array(
							__('Homepage - default', 'psp'), 'psp_twc_home_app,psp_twc_home_type,help_psp_twc_home'))
				),
				
				// create the box elements array
				'elements'	=> array(

                    //=============================================================
                    //== General options
                    'force_title' => array(
                        'type'      => 'select',
                        'std'       => 'yes',
                        'size'      => 'large',
                        'force_width'=> '220',
                        'title'     => __('Force Title Meta tag: ', 'psp'),
                        'desc'      => __('force title meta tag (in some cases where you don\'t see the meta title you\'ve set for you post|page, you need to try and see which one of these 2 options works)', 'psp'),
                        'options'   => array(
                            'yes'   => __('parse page content and replace', 'psp'),
                            'no'    => __('use wp_title wordpress hook', 'psp')
                        )
                    ),

					//=============================================================
					//== help
					'help_format_tags' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Basic Setup</h2>
							<p>You can set the custom page title using defined formats tags.</p>
							<h3>Available Format Tags</h3>
							<ul>
								<li><code>{site_title}</code> : the website\'s title (global availability)</li>
								<li><code>{site_description}</code> : the website\'s description (global availability)</li>
								<li><code>{current_date}</code> : current date (global availability)</li>
								<li><code>{current_time}</code> : current time (global availability)</li>
								<li><code>{current_day}</code> : current day (global availability)</li>
								<li><code>{current_year}</code> : current year (global availability)</li>
								<li><code>{current_month}</code> : current month (global availability)</li>
								<li><code>{current_week_day}</code> : current day of the week (global availability)</li>


								<li><code>{title}</code> : the page|post title (global availability)</li>
								<li><code>{id}</code> : the page|post id (specific availability)</li>
								<li><code>{date}</code> : the page|post date (specific availability)</li>
								<li><code>{description}</code> : the page|post full description (specific availability)</li>
								<li><code>{short_description}</code> : the page|post excerpt or if excerpt does not exist, 200 character maximum are retrieved from description (specific availability)</li>
								<li><code>{parent}</code> : the page|post parent title (specific availability)</li>
								<li><code>{author}</code> : the page|post author name (specific availability)</li>
								<li><code>{author_username}</code> : the page|post author username (specific availability)</li>
								<li><code>{author_nickname}</code> : the page|post author nickname (specific availability)</li>
								<li><code>{author_description}</code> : the page|post author biographical Info (specific availability)</li>
								<li><code>{categories}</code> : the post categories names list separated by comma (specific availability)</li>
								<li><code>{tags}</code> : the post tags names list separated by comma (specific availability)</li>
								<li><code>{terms}</code> : the post custom taxonomies terms names list separated by comma (specific availability)</li>
								<li><code>{category}</code> : the category name or the post first found category name (specific availability)</li>
								<li><code>{category_description}</code> : the category description or the post first found category description (specific availability)</li>
								<li><code>{tag}</code> : the tag name or the post first found tag name (specific availability)</li>
								<li><code>{tag_description}</code> : the tag description or the post first found tag description (specific availability)</li>
								<li><code>{term}</code> : the term name or the post first found custom taxonomy term name (specific availability)</li>
								<li><code>{term_description}</code> : the term description or the post first found custom taxonomy term description (specific availability)</li>
								<li><code>{search_keyword}</code> : the word(s) used for search (specific availability)</li>
								<li><code>{keywords}</code> : the post|page keywords already defined (specific availability)</li>
								<li><code>{focus_keywords}</code> : the post|page focus keywords already defined (specific availability)</li>
								<li><code>{totalpages}</code> : the total number of pages (if pagination is used), default value is 1 (specific availability)</li>
								<li><code>{pagenumber}</code> : the page number (if pagination is used), default value is 1 (specific availability)</li>
							</ul><br />
							', 'psp')
					),

// 							<p>Info: when use {keywords}, if for a specific post|page {focus_keywords} is found then it is used, otherwise {keywords} remains active</p>

					//=============================================================
					//== title format
					'home_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '{site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'post_title'			=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Post <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					'page_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Page <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					'category_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Category <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {category} {category_description}' . '</span>'
					),
					'tag_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Tag <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {tag} {tag_description}' ,'</span>'
					),
					'archive_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} ' . __('Archives', 'psp') . ' | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Archives <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {date} ' . '</span>' . __('- is based on archive type: per year or per month,year or per day,month,year', 'psp')
					),
					'author_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {author} {author_username} {author_nickname}' . '</span>'
					),
					'search_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> __('Search for ', 'psp') . '{search_keyword} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Search <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {search_keyword}' . '</span>'
					),
					'404_title'		=> array(
						'type' 		=> 'text',
						'std' 		=> __('404 Page Not Found |', 'psp') . ' {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('404 Page Not Found <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'pagination_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} ' . __('- Page', 'psp') . ' {pagenumber} ' . __('of', 'psp') . ' {totalpages} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Pagination <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {totalpages} {pagenumber}' . '</span>'
					),
					'use_pagination_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Title Format in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),

					'posttype_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					/*'product_title'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),*/
					'posttype_custom_title_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab2', '__subtab2', array(
							'what'		=> 'posttype',
							'field'			=> 'title',
						))
					),

					'taxonomy_title'=> array(
						'type' 		=> 'text',
						'std' 		=> '{title} | {site_title}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Title Format:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {term} {term_description}' . '</span>'
					),
					'taxonomy_custom_title_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab2', '__subtab3', array(
							'what'		=> 'taxonomy',
							'field'			=> 'title',
						))
					),
					
					//=============================================================
					//== meta description
					'home_desc' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'post_desc'			=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{short_description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Post <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					'page_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{short_description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Page <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					'category_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{category_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Category <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {category} {category_description}' . '</span>'
					),
					'tag_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{tag_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Tag <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {tag} {tag_description}' . '</span>'
					),
					'archive_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Archives <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {date} ' . '</span>' . __('- is based on archive type: per year or per month,year or per day,month,year', 'psp')
					),
					'author_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {author} {author_username} {author_nickname} {author_description}' . '</span>'
					),
					'pagination_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> __('Page {pagenumber}', 'psp'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Pagination <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {totalpages} {pagenumber}' . '</span>'
					),
					'use_pagination_desc' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Meta Description in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'posttype_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{short_description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					/*'product_desc'	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{short_description} | {site_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),*/
					'posttype_custom_desc_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab3', '__subtab2', array(
							'what'		=> 'posttype',
							'field'			=> 'desc',
						))
					),

					'taxonomy_desc'=> array(
						'type' 		=> 'textarea',
						'std' 		=> '{term_description}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Meta Description:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {term} {term_description}' . '</span>'
					),
					'taxonomy_custom_desc_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab3', '__subtab3', array(
							'what'		=> 'taxonomy',
							'field'			=> 'desc',
						))
					),
					
					//=============================================================
					//== meta keywords
					'home_kw' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags', 'psp')
					),
					'post_kw'			=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Post <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					'page_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Page <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					'category_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Category <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {category} {category_description}' . '</span>'
					),
					'tag_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Tag <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {tag} {tag_description}' . '</span>'
					),
					'archive_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Archives <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {date} ' . '</span>' . __('- is based on archive type: per year or per month,year or per day,month,year', 'psp')
					),
					'author_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {author} {author_username} {author_nickname}' . '</span>'
					),
					'pagination_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Pagination <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {totalpages} {pagenumber}' . '</span>'
					),
					'use_pagination_kw' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Meta Keywords in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'posttype_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),
					/*'product_kw'	=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {id} {date} {description} {short_description} {parent} {author} {author_username} {author_nickname} {categories} {tags} {terms} {category} {category_description} {tag} {tag_description} {term} {term_description} {keywords} {focus_keywords}' . '</span>'
					),*/
					'posttype_custom_kw_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab4', '__subtab2', array(
							'what'		=> 'posttype',
							'field'			=> 'kw',
						))
					),

					'taxonomy_kw'=> array(
						'type' 		=> 'text',
						'std' 		=> '{keywords}',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Meta Keywords:</span>', 'psp'),
						'desc' 		=> __('Available here: (global availability) tags; (specific availability) tags:<span class="psp-tags-specific-availability">', 'psp') . ' {term} {term_description}' . '</span>'
					),
					'taxonomy_custom_kw_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab4', '__subtab3', array(
							'what'		=> 'taxonomy',
							'field'			=> 'kw',
						))
					),
					
					//=============================================================
					//== meta robots
					'help_meta_robots' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>What it means:</h2>
							<ul>
								<li><code>NOINDEX</code><span> : tag tells Google not to index a specific page</span></li>
								<li><code>NOFOLLOW</code><span> : tag tells Google not to follow the links on a specific page</span></li>
								<li><code>NOARCHIVE</code><span> : tag tells Google not to store a cached copy of your page</span></li>
								<li><code>NOODP</code><span> : tag can prevent Google from using the meta-title and description for this page in <a href="http://www.dmoz.org/" target="_blank">DMOZ</a> (Open Directory Project) as the snippet for your page in the search results.<br/><span style="color: red;">But as of Mar 17, 2017, DMOZ is no longer available, so this tag is considered deprecated and we\'ll remove it in a future plugin version.</span></span></li>
								<!--<li><code>NOSNIPPET</code><span> : tag tells Google not to show a snippet (description) under your Google listing, it will also not show a cached link in the search results</span></li>-->
							</ul>
							', 'psp')
					),

					'home_robots' 	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Homepage <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'post_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Post <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'page_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Page <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'category_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Category <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'tag_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Tag <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'archive_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Archives <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'author_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Author <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'search_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Search <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'404_robots'		=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('404 Page Not Found <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'pagination_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('noindex','nofollow','noarchive','noodp'),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Pagination <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'use_pagination_robots' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Pagination:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Pagination Meta Robots in pages where it can be applied!', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'posttype_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					/*'product_robots'	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Product <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),*/
					'posttype_custom_robots_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab5', '__subtab2', array(
							'what'		=> 'posttype',
							'field'			=> 'robots',
						))
					),

					'taxonomy_robots'=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('--Generic-- <br/><span>Meta Robots:</span>', 'psp'),
						'desc' 		=> __('if you do not select "noindex", then "index" is by default active; if you do not select "nofollow", then "follow" is by default active', 'psp'),
						'options'	=> $__metaRobotsList
					),
					'taxonomy_custom_robots_html' => array(
						'type' 		=> 'html',
						'html' 		=> psp_CustomPosttypeTaxonomyMeta( '__tab5', '__subtab3', array(
							'what'		=> 'taxonomy',
							'field'			=> 'robots',
						))
					),
				
					//=============================================================
					//== social tags

					// social general
					'social_use_meta' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Social Meta Tags:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Facebook Open Graph Social Meta Tags in all your pages! If you choose No, you can still activate tags for a post or page in it\'s meta box.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					'social_include_extra' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Include extra tags:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to include the following &lt;article:published_time&gt;, &lt;article:modified_time&gt;, &lt;article:author&gt; tags for your posts.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					'social_validation_type' => array(
						'type' 		=> 'select',
						'std' 		=> 'opengraph',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Code Validation Type:', 'psp'),
						'desc' 		=> '',
						'options'	=> array(
							'opengraph' 	=> 'opengraph',
							'xhtml' 		=> 'xhtml',
							'html5'			=> 'html5'
						)
					),
					
					'social_fb_app_id' => array(
						'type' 		=> 'text',
						'std' 		=> '966242223397117', //default facebook app id
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Facebook App ID:', 'psp'),
						'desc' 		=> __('You need to specify a "Facebook App ID" if you want to remove the following warning from facebook debugger:<br /><span style="color: red;">The "fb:app_id" property should be explicitly provided, Specify the app ID so that stories shared to Facebook will be properly attributed to the app. Alternatively, app_id can be set in url when open the share dialog.</span><br />
						If you want to create a Facebook app for your website, please follow the steps from this page: <a href="https://developers.facebook.com/docs/apps/register" target="_blank">https://developers.facebook.com/docs/apps/register</a>', 'psp')
					),

					'social_site_title' => array(
						'type' 		=> 'text',
						'std' 		=> get_bloginfo('name'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Site Name:', 'psp'),
						'desc' 		=> sprintf( __('Current site name:<br/> %s', 'psp'), '<span style="font-weight: bold;">' . get_bloginfo('name') . '</span>' )
					),
					'social_default_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Default Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> __('Here you can specify an image URL or an image from your media library to use as a default image in the event that there is no image otherwise specified for a given webpage on your site.', 'psp'),
					),
					
					// social homepage
					'help_psp_social_home' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<ul class="psp-social-helper">
								<li><span style="color: red;">If in Wordpress Settings / Reading Settings / option "Front page displays" you choose "A static page" and for "Front Page" you select a page, then the values you\'ve completed on it\'s edit details "Page SEO Settings" box / Social Settings, will override the settings bellow.</span></li>
							</ul><br />
							', 'psp')
					),
					
					'social_home_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '', //get_bloginfo('name')
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Title:', 'psp'),
						'desc' 		=> sprintf( __('Current site name:<br/> %s', 'psp'), '<span style="font-weight: bold;">' . get_bloginfo('name') . '</span>' )
					),
					'social_home_desc' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '', //get_bloginfo('description')
						'size' 		=> 'small',
						'force_width'=> '400',
						'title' 	=> __('Homepage Description:', 'psp'),
						'desc' 		=> sprintf( __('Current site name:<br/> %s', 'psp'), '<span style="font-weight: bold;">' . get_bloginfo('description') . '</span>' )
					),
					'social_home_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Homepage Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> '&nbsp;',
					),
					'social_home_type' => array(
						'type' 		=> 'select',
						'std' 		=> 'website',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Homepage OpenGraph Type:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							'blog'					=> __('Blog', 'psp'),
							'profile'				=> __('Profile', 'psp'),
							'website'				=> __('Website', 'psp')
						)
					),
					
					// social posts, pages
					'social_opengraph_default' => array(
						'type' 		=> 'html',
						'html' 		=> psp_OpenGraphTypes( '__tab6', '__subtab2', 'posttype' )
					),
					
					'social_customfield_post' 	=> array(
						'type' 		=> 'text',
						'std' 			=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Choose custom field:', 'psp'),
						'desc' 		=> '
							<span>We choose the post, page, custom post type, og:image metatag based on this priority fallback queue (order by importance from top to bottom - most important are first):</span>
							<ul>
								<li>1. the image set in "Page SEO Settings" box / Social Settings</li>
								<li>2. the post featured image</li>
								<li style="color: red;">3. we try to find the image from <strong>custom field</strong> (if you choose to set this field)</li>
								<li>4. we try to use the first image in the post (page or custom post type) content (with shortcodes too)
								<li>5. the default image from "Title & Meta Format" module / Social Meta / General / "Default Image" option</li>
							</ul>
						'
					),
					
					// social categories, tags
					'social_opengraph_default_taxonomy' => array(
						'type' 		=> 'html',
						'html' 		=> psp_OpenGraphTypes( '__tab6', '__subtab3', 'taxonomy' )
					),
					
					'social_customfield_taxonomy' 	=> array(
						'type' 		=> 'text',
						'std' 			=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Choose custom field:', 'psp'),
						'desc' 		=> '
							<span>We choose the category, tag, custom taxonomy, og:image metatag based on this priority fallback queue (order by importance from top to bottom - most important are first):</span>
							<ul>
								<!--<li>1. the image set in "Page SEO Settings" box / Social Settings</li>-->
								<li style="color: red;">1. we try to find the image from <strong>custom field</strong> (if you choose to set this field) - As of Wordpress 4.4 a new "term meta" functionality was introduced which allows you to create custom fields for categories, tags, taxonomies</li>
								<li>2. we try to use the first image in the category (tag or custom taxonomy) content (with shortcodes too)
								<li>3. the default image from "Title & Meta Format" module / Social Meta / General / "Default Image" option</li>
							</ul>
						'
					),
					
					//=============================================================
					//== twitter cards
					
					// twitter general
					'psp_twc_use_meta' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Use Twitter Cards Meta Tags:', 'psp'),
						'desc' 		=> __('Choose Yes if you want to use Twitter Cards Meta Tags in all your pages! If you choose No, you can still activate tags for a post or page in it\'s meta box.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),

					'psp_twc_website_account' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Website Twitter Account:', 'psp'),
						'desc' 		=> '(optional) <twitter:site> @username for the website used in the card footer.'
					),
					
					'psp_twc_website_account_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Website Twitter Account Id:', 'psp'),
						'desc' 		=> '(optional) <twitter:site:id> the website\'s Twitter user ID instead of @username. Note that user ids never change, while @usernames can be changed by the user.'
					),
					
					'psp_twc_creator_account' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Content Creator Twitter Account:', 'psp'),
						'desc' 		=> '(optional) <twitter:creator> @username for the content creator / author.'
					),
					
					'psp_twc_creator_account_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Content Creator Twitter Account Id:', 'psp'),
						'desc' 		=> '(optional) <twitter:creator:id> the Twitter user\'s ID for the content creator / author instead of @username.'
					),
					
					'psp_twc_default_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Default Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> __('Here you can specify an image URL or an image from your media library to use as a default image in the event that there is no image otherwise specified for a given webpage on your site.', 'psp'),
					),
					
					'psp_twc_thumb_sizes' => array(
						'type' 		=> 'select',
						'std' 		=> 'none',
						'size' 		=> 'large',
						'force_width'  => '450',
						'title' 		=> __('Image Thumbnails sizes:', 'psp'),
						'desc' 		=> '<span style="color: red;">this option has effect on: posts, pages, custom post types, categories, tags, custom taxonomies.</span>',
						'options'	=> array(
							'none'		=> __('Don\'t make a thumbnail from the image', 'psp'),
							'435x375' => __('Web: height is 375px, width is 435px', 'psp'),
							'280x375' => __('Mobile (non-retina displays): height is 375px, width is 280px', 'psp'),
							'560x750' => __('Mobile (retina displays): height is 750px, width is 560px', 'psp'),
							'280x150' => __('Small: height is 150px, width is 280px', 'psp'),
							'120x120' => __('Smallest: height is 120px, width is 120px', 'psp')
						)
					),
					
					'psp_twc_thumb_crop' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'	=> '120',
						'title' 	=> __('Force Crop on card type Image?', 'psp'),
						'desc' 		=> __('Choose Yes if you want to force crop on your twitter card type chosen image.<br/><span style="color: red;">this option has effect on: posts, pages, custom post types, categories, tags, custom taxonomies.</span>', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),

					// twitter posts, pages, custom post types
					'help_psp_twc_post' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<ul class="psp-tw-helper">
								<li>- For the following twitter card types (<strong>Summary Card, Summary Card with Large Image</strong>), the twitter card meta tags are filled by default with values from the post or page or custom post type, title, content, featured image. If you complete the values in Title, Description, Image fields from the "Page Seo Setting" box / Twitter Cards tab, these values will override the existent default meta tags values.</li>
								<li>- For the following twitter card type (<strong>Player Card</strong>), you need to complete the mandatory fields for the card type per every post or page which you want to be relationated with twitter - these fields cannot be auto filled.</li>
								<li>- The following twitter card types are <a href="https://twittercommunity.com/t/deprecating-the-photo-gallery-and-product-cards/38961" target="_blank">deprecated and aren\'t available anymore</a>: <strong>Photo Card, Gallery Card, Product Card</strong>.</li>
							</ul><br />
							', 'psp')
					),

					'psp_twc_cardstype_default' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsTypes( '__tab7', '__subtab2', 'posttype' )
					),
					
					'psp_twc_apptype_default' => array(
						'type' 		=> 'html',
						'html' 		=> psp_AppTypeUse( '__tab7', '__subtab2', 'posttype' )
					),
					
					'psp_twc_image_find' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsImageFind( '__tab7', '__subtab2', 'posttype' )
					),
					
					// twitter categories, tags, custom taxonomies
					'help_psp_twc_taxonomy' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<ul class="psp-tw-helper">
								<li>- For the following twitter card types (<strong>Summary Card, Summary Card with Large Image</strong>), the twitter card meta tags are filled by default with values from the category or tag or custom taxonomy, title, content, featured image. If you complete the values in Title, Description, Image fields from the "Page Seo Setting" box / Twitter Cards tab, these values will override the existent default meta tags values.</li>
								<li>- For the following twitter card type (<strong>Player Card</strong>), you need to complete the mandatory fields for the card type per every post or page which you want to be relationated with twitter - these fields cannot be auto filled.</li>
								<li>- The following twitter card types are <a href="https://twittercommunity.com/t/deprecating-the-photo-gallery-and-product-cards/38961" target="_blank">deprecated and aren\'t available anymore</a>: <strong>Photo Card, Gallery Card, Product Card</strong>.</li>
							</ul><br />
							', 'psp')
					),

					'psp_twc_cardstype_default_taxonomy' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsTypes( '__tab7', '__subtab3', 'taxonomy' )
					),
					
					'psp_twc_apptype_default_taxonomy' => array(
						'type' 		=> 'html',
						'html' 		=> psp_AppTypeUse( '__tab7', '__subtab3', 'taxonomy' )
					),
					
					'psp_twc_image_find_taxonomy' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsImageFind( '__tab7', '__subtab3', 'taxonomy' )
					),
					
					// twitter generic app card type
					'help_psp_twc_app' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<ul class="psp-tw-helper">
								<li>Here you can create a Generic App Card Type for your website.</li>
							</ul><br />
							', 'psp')
					),
					
					'psp_twc_site_app' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Create Generic App Card Type: ', 'psp'),
						'desc' 		=> __('Create a Generic Twitter App Card Type for your website', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'psp_twc_site_app_options' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsOptions( '__tab7', '__subtab4', 'app' )
					),
					
					// twitter homepage
					'help_psp_twc_home' => array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<ul class="psp-tw-helper">
								<li><span style="color: red;">If in Wordpress Settings / Reading Settings / option "Front page displays" you choose "A static page" and for "Front Page" you select a page, then the values you\'ve completed on it\'s edit details "Page SEO Settings" box / Social Settings, will override the settings bellow.</span></li>
							</ul><br />
							', 'psp')
					),
					
					'psp_twc_home_app' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Add App Card Type: ', 'psp'),
						'desc' 		=> __('Add Twitter App Card Type to your homepage (if "YES", then the "Generic App Card Type for website" will be used)', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'psp_twc_home_type' => array(
						'type' 		=> 'select',
						'std' 		=> 'summary',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Homepage Twitter Card Type:', 'psp'),
						'desc' 		=> '&nbsp;',
						'options'	=> array(
							'none'					=> __('None', 'psp'),
							'summary'				=> __('Summary Card', 'psp'),
							'summary_large_image'		=> __('Summary Card with Large Image', 'psp'),
							//'photo'					=> __('Photo Card', 'psp'),
							//'gallery'				=> __('Gallery Card', 'psp'),
							'player'				=> __('Player Card', 'psp'),
							//'product'				=> __('Product Card', 'psp')
						)
					),
					
					'psp_twc_home_options' => array(
						'type' 		=> 'html',
						'html' 		=> psp_TwitterCardsOptions( '__tab7', '__subtab5', 'home' )
					),
					
					/*'psp_twc_home_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> get_bloginfo('name'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Title:', 'psp'),
						'desc' 		=> 'Title should be concise and will be truncated at 70 characters.'
					),
					'psp_twc_home_desc' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> get_bloginfo('description'),
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Homepage Description:', 'psp'),
						'desc' 		=> 'A description that concisely summarizes the content of the page, as appropriate for presentation within a Tweet. Do not re-use the title text as the description, or use this field to describe the general services provided by the website. Description text will be truncated at the word to 200 characters.'
					),
					'psp_twc_home_img' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> __('Homepage Image:', 'psp'),
						'value' 	=> __('Upload image', 'psp'),
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> 'Image must be less than 1MB in size.',
					),*/

				)
			)
		)
	)
//)
;

if ( $psp->is_buddypress() ) {

	// tabs
	if ( isset($__psp_mfo_bp['tabs']) && !empty($__psp_mfo_bp['tabs']) ) {
		foreach ( $__psp_mfo_bp['tabs'] as $key => $val ) {
			if ( !empty($val) && is_array($val) ) {
				if ( count($val) == 1 ) {
					$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['tabs']["$key"][1] .= $val[0];					
				}
			}
		}
	}

	// subtabs
	if ( isset($__psp_mfo_bp['subtabs']) && !empty($__psp_mfo_bp['subtabs']) ) {
		foreach ( $__psp_mfo_bp['subtabs'] as $key => $val ) {
			if ( !empty($val) && is_array($val) ) {
				foreach ( $val as $key2 => $val2 ) {
  
					$__is_tab = (bool) ( isset($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]) && !empty($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]) );
					$__is_subtab = (bool) ( isset($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"]) && !empty($__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"]) );
 					
					if ( !$__is_subtab ) {
						if ( !$__is_tab )
							$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"] = array();

						$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"] = $val2;
					} else {
						$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['subtabs']["$key"]["$key2"] = $val2;
					}
				}
			}
		}
	}
  
	// elements
	if ( isset($__psp_mfo_bp['elements']) && !empty($__psp_mfo_bp['elements']) ) {
		foreach ( $__psp_mfo_bp['elements'] as $key => $val ) {
			if ( !empty($val) && is_array($val) ) {
				$__psp_mfo[$tryed_module['db_alias']]['title_meta_format']['elements']["$key"] = $val;
			}
		}
	}
}

//var_dump('<pre>', $__psp_mfo, '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;    
echo json_encode(
	$__psp_mfo
);