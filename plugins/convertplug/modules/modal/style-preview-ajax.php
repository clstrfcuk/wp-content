<?php
/*
* Preview Style
*/
require_once('functions/functions.options.php');

$style = $_GET['style'];
$options = Smile_Modals::$options;
$style_options = $options[$style]['options'];
$settings = array();
$settings['style'] = 'preview';
foreach( $style_options as $key => $value ) {
	$settings[$value['name']] = $value['opts']['value'];
}
$settings['affiliate_setting'] = false;
$settings_encoded = base64_encode( serialize( $settings ) );

echo do_shortcode('[smile_modal style="'.$style.'" settings_encoded="' . $settings_encoded . ' "][/smile_modal]');
?>
<script type="text/javascript">
jQuery(document).ready(function(e) {
    jQuery(".cp-overlay").addClass("cp-open");
    jQuery("#TB_ajaxContent").appendTo("body");
	jQuery("#TB_overlay").remove();
	jQuery("body").on("click",".cp-overlay", function(){
		jQuery(this).removeClass("cp-open");
		jQuery("#TB_ajaxContent").remove();
		jQuery("#TB_window").remove();
		jQuery("#TB_overlay").trigger("click");
		jQuery("body").removeClass("modal-open");
	});
	jQuery("body").on("click",".cp-modal-content",function(e){
		e.preventDefault();
		e.stopPropagation();
	});
});

jQuery(document).ready(function(){
	jQuery(document).bind('keydown', function(e) { 
			if (e.which == 27) {
				var cp_overlay = jQuery(".cp-open");
				var modal = cp_overlay;
				modal.fadeOut('slow').remove();
				jQuery("#TB_ajaxContent").remove();
				jQuery("#TB_window").remove();
				jQuery("#TB_overlay").remove();
			}
	});
});

</script>