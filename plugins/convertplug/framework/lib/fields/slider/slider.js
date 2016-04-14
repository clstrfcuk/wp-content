jQuery(document).ready(function(jQuery) {
	var slider_input = jQuery(".smile-slider");
	jQuery.each(slider_input,function(index,obj){
		var $this 		= jQuery(this);
		var slider_id 	= $this.attr('id').replace("smile_","slider_");
		var input_id 	= $this.attr('id');
		var val 		= $this.val();
		var minimum 	= $this.data('min');
		var maximum 	= $this.data('max');
		var step 		= $this.data('step');

		jQuery( '#'+input_id ).on('keyup change', function() {

				value = jQuery(this).val();
				jQuery( '#'+slider_id ).slider('value', value);
				var leftMarginToSlider = jQuery( '#'+slider_id ).find('.ui-slider-handle').css('left');
				jQuery( '#'+slider_id ).find('.range-quantity').css('width',leftMarginToSlider);
		});
		jQuery( '#'+slider_id ).slider({
				value : val,
				min   : minimum,
				max   : maximum,
				step  : step,
				slide : function( event, ui ) {
					jQuery( '#'+input_id ).val(ui.value).keyup(); 
					var leftMarginToSlider = jQuery( '#'+slider_id ).find('.ui-slider-handle').css('left');
					jQuery( '#'+slider_id ).find('.range-quantity').css('width',leftMarginToSlider);
				}
		});
		jQuery( '#'+input_id ).val( jQuery( '#'+slider_id ).slider( "value" ) );		
		var leftMarginToSlider = jQuery( '#'+slider_id ).find('.ui-slider-handle').css('left');
		jQuery( '#'+slider_id ).find('.range-quantity').css('width',leftMarginToSlider);
	});
});