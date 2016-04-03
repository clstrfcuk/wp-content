<?php
/*
* Preview Style
*/
require_once('functions/functions.options.php');

$style = $_GET['style'];
$options = Smile_Slide_Ins::$options;
$style_options = $options[$style]['options'];
$settings = array();
$settings['style'] = 'preview';
foreach( $style_options as $key => $value ) {
	$settings[$value['name']] = $value['opts']['value'];
}
$settings['affiliate_setting'] = false;
$settings_encoded = base64_encode( serialize( $settings ) );

echo do_shortcode('[smile_slide_in style="'.$style.'" settings_encoded="' . $settings_encoded . ' "][/smile_slide_in]');
?>
<script type="text/javascript">
jQuery(document).ready(function(e) {
    jQuery(".slidein-overlay").addClass("si-open");
	jQuery("body").on("click",".slidein-overlay", function(){
		jQuery(this).removeClass("si-open");
		jQuery("#TB_ajaxContent").remove();
		jQuery("#TB_window").remove();
		jQuery("#TB_overlay").trigger("click");
		jQuery("body").removeClass("modal-open");
		jQuery("#TB_overlay").remove();
	});
	jQuery("body").on("click",".cp-slidein-content",function(e){
		e.preventDefault();
		e.stopPropagation();
	});
});
</script>