$ = jQuery;
var smile_panel_id = '';
jQuery(document).ready( function() {
	jQuery(document).on('smile_panel_loaded',function(e,smile_panel,id){
		var smile_panel = jQuery(".customize").data('style');
		smile_panel_id = smile_panel;
		function Border (options) {
		
			this.htmlElement = options['htmlElement'] || jQuery('#accordion-'+ smile_panel_id +' #border-radius-panel');
			this.htmlCode = jQuery('#accordion-'+ smile_panel_id +' #border-code');
			this.br_all = ( typeof options['br_all'] !== 'undefined' ? options['br_all'] : 10 );
			this.br_tl = ( typeof options['br_tl'] !== 'undefined' ? options['br_tl'] : 10 );
			this.br_tr = ( typeof options['br_tr'] !== 'undefined' ? options['br_tr'] : 10 );
			this.br_bl = ( typeof options['br_bl'] !== 'undefined' ? options['br_bl'] : 10 );
			this.br_br = ( typeof options['br_br'] !== 'undefined' ? options['br_br'] : 10 );
			this.style = options['style'] || 'none';
			this.color = options['color'] ||'#000000';
			this.br_type = options['br_type'] || 0;
			this.bw_type = options['bw_type'] || 0;
			this.bw_all = ( typeof options['bw_all'] !== 'undefined' ? options['bw_all'] : 10 );
			this.bw_t = ( typeof options['bw_t'] !== 'undefined' ? options['bw_t'] : 10 );
			this.bw_l = ( typeof options['bw_l'] !== 'undefined' ? options['bw_l'] : 10 );
			this.bw_r = ( typeof options['bw_r'] !== 'undefined' ? options['bw_r'] : 10 );
			this.bw_b = ( typeof options['bw_b'] !== 'undefined' ? options['bw_b'] : 10 );
			return this;
		}
		
		Border.prototype.refresh = function () {	
			var inputCode = 'br_type:'+this.br_type+'|';
			inputCode += 'br_all:'+this.br_all+'|';
			inputCode += 'br_tl:'+this.br_tl+'|';
			inputCode += 'br_tr:'+this.br_tr+'|';
			inputCode += 'br_br:'+this.br_br+'|';
			inputCode += 'br_bl:'+this.br_bl+'|';
			inputCode += 'style:'+this.style+'|';
			inputCode += 'color:'+this.color+'|';
			inputCode += 'bw_type:'+this.bw_type+'|';
			inputCode += 'bw_all:'+this.bw_all+'|';
			inputCode += 'bw_t:'+this.bw_t+'|';
			inputCode += 'bw_l:'+this.bw_l+'|';
			inputCode += 'bw_r:'+this.bw_r+'|';
			inputCode += 'bw_b:'+this.bw_b;
		
			this.htmlCode.html(inputCode);
			this.htmlCode.trigger('change');
		}
		
		Border.prototype.setAllCorners = function (border) {
			this.br_all = border;
			this.br_tl = border;
			this.br_tr = border;
			this.br_bl = border;
			this.br_br = border;		
		}

		Border.prototype.setAllSides = function (width) {
			this.bw_all = width;
			this.bw_t = width;
			this.bw_l = width;
			this.bw_r = width;
			this.bw_b = width;
		}
		
		function _getAllValuesFromPanelBorder() {
			var options = {};
			options['br_all'] = parseFloat(jQuery("#accordion-"+ smile_panel_id +" #all-corners").val());
			options['br_tl'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #top-left').val());
			options['br_tr'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #top-right').val());
			options['br_bl'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #bottom-left').val());
			options['br_br'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #bottom-right').val());
			options['style'] = jQuery('#accordion-'+ smile_panel_id +' #select-border :selected').val();
			options['color'] = jQuery('#accordion-'+ smile_panel_id +' #br-color').val();
			options['br_type'] = jQuery('#accordion-'+ smile_panel_id +' #smile_adv_border_opt').val();
			options['bw_all'] = parseFloat(jQuery("#accordion-"+ smile_panel_id +" #width-allsides").val());
			options['bw_t'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #width-top').val());
			options['bw_l'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #width-left').val());
			options['bw_r'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #width-right').val());
			options['bw_b'] = parseFloat(jQuery('#accordion-'+ smile_panel_id +' #width-bottom').val());
			options['bw_type'] = jQuery('#accordion-'+ smile_panel_id +' #smile_adv_borderwidth_opt').val();
			return options;
		}
		
		function _getFromFieldBorder(value, min, max, elem) {
			var val = parseFloat(value);
			if (isNaN(val) || val < min) {
				val = 0;
			} else if (val > max) {
				val = max;
			}
		
			if (elem)
				elem.val(val);
		
			return val;
		}
	
		var opts = _getAllValuesFromPanelBorder();
		var border = new Border(opts);
		border.refresh();	

		/* border type */ 
		jQuery('#accordion-'+ smile_panel +' #smile_adv_border_opt').on('change', function () {
			var val = jQuery(this).val();
			if( val == 0 ) {
				jQuery(".border-container.param-advanced-block").slideUp();
				jQuery(".border-container.param-basic-block").slideDown();
			} else {
				jQuery(".border-container.param-basic-block").slideUp();
				jQuery(".border-container.param-advanced-block").slideDown();
			}
			border.br_type = val;			
			border.refresh();
		});


		/* border width type */ 
		jQuery('#accordion-'+ smile_panel +' #smile_adv_borderwidth_opt').on('change', function () {
			var val = jQuery(this).val();
			if( val == 0 ) {
				jQuery(".borderwidth-container.param-advanced-block").slideUp();
				jQuery(".borderwidth-container.param-basic-block").slideDown();
			} else {
				jQuery(".borderwidth-container.param-basic-block").slideUp();
				jQuery(".borderwidth-container.param-advanced-block").slideDown();
			}
			border.bw_type = val;
			border.refresh();
		});
	
		/* Border Style */
		jQuery('#accordion-'+ smile_panel +' #select-border').on('change', function () {
			var val = jQuery(this).val();
			if( val == 'none' ) {
				jQuery(".borderwidth-block").closest(".setting-block").slideUp();
				jQuery(".bordercolor-block").closest(".setting-block").slideUp();
			} else {
				jQuery(".borderwidth-block").closest(".setting-block").slideDown();
				jQuery(".bordercolor-block").closest(".setting-block").slideDown();
			}
			border.style = val;
			border.refresh();
		});
	
		/* Border Radius */
		jQuery('#accordion-'+ smile_panel +' #slider-all-corners').slider({
			value: jQuery('#accordion-'+ smile_panel +' #all-corners').val(),
			min: 0,
			max: 500,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500);
				border.setAllCorners(val);
	
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
	
				jQuery('#accordion-'+ smile_panel +' #all-corners').val(val);
				jQuery('#accordion-'+ smile_panel +' #top-left').val(val);
				jQuery('#accordion-'+ smile_panel +' #top-right').val(val);
				jQuery('#accordion-'+ smile_panel +' #bottom-left').val(val);
				jQuery('#accordion-'+ smile_panel +' #bottom-right').val(val);
	
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-top-left').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-top-right').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-bottom-left').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-bottom-right').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
	
				border.refresh();
			},
			stop: function( event, ui ) {
				border.refresh();
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-top-left').slider({
			value: jQuery('#accordion-'+ smile_panel +' #top-left').val(),
			min: 0,
			max: 500,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #top-left'));
				border.br_tl = val;
				border.refresh();
	
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-top-right').slider({
			value: jQuery('#accordion-'+ smile_panel +' #top-right').val(),
			min: 0,
			max: 500,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #top-right').val(val));
				border.br_tr = val;
				border.refresh();
	
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-bottom-left').slider({
			value: jQuery('#accordion-'+ smile_panel +' #bottom-left').val(),
			min: 0,
			max: 500,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #bottom-left'));
				border.br_bl = val;
				border.refresh();
	
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-bottom-right').slider({
			value: jQuery('#accordion-'+ smile_panel +' #bottom-right').val(),
			min: 0,
			max: 500,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #bottom-right'));
				border.br_br = val;
				border.refresh();
	
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
	
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #all-corners').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #all-corners'));
			border.setAllCorners(val);
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-all-corners').slider('value', val);
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-all-corners').find('.ui-slider-handle').css('left');
	
			jQuery('#accordion-'+ smile_panel +' #slider-all-corners').find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #top-left').val(val);
			jQuery('#accordion-'+ smile_panel +' #top-right').val(val);
			jQuery('#accordion-'+ smile_panel +' #bottom-left').val(val);
			jQuery('#accordion-'+ smile_panel +' #bottom-right').val(val);
			jQuery('#accordion-'+ smile_panel +' #slider-top-left').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-top-right').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-bottom-left').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-bottom-right').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		jQuery('#top-left').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #top-left'));
			border.br_tl = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-top-left').slider('value', val);
	
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-top-left').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-top-left').find('.range-quantity').css('width',leftMarginToSlider);
	
		});
	
		jQuery('#accordion-'+ smile_panel +' #top-right').on('keyup change', function () {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #top-right'));
			border.br_tr = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-top-right').slider('value', val);
			 var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-top-right').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-top-right').find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		jQuery('#accordion-'+ smile_panel +' #bottom-left').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #bottom-left'));
			border.br_bl = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-bottom-left').slider('value', val);
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-bottom-left').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-bottom-left').find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		jQuery('#accordion-'+ smile_panel +' #bottom-right').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #bottom-right'));
			border.br_br = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-bottom-right').slider('value', val);
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-bottom-right').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-bottom-right').find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		/* Color (Border and background) */
		jQuery('#accordion-'+ smile_panel +' #br-color').on('change', function () {
			border.color = jQuery(this).val();
			border.refresh();
			jQuery('#accordion-'+ smile_panel +' #br-color-button').css('background-color', '#' + jQuery(this).val());
		});
	
		jQuery('#accordion-'+ smile_panel +' #br-background-color').on('change', function () {
			border.borderBackground = jQuery(this).val();
			border.refresh();
			jQuery('#accordion-'+ smile_panel +' #br-background-color-button').css('background-color', '#' + jQuery(this).val());
		});

	
		/* Border Width */
		jQuery('#accordion-'+ smile_panel +' #slider-width-allsides').slider({
			value: jQuery('#accordion-'+ smile_panel +' #width-allsides').val(),
			min: 0,
			max: 50,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500);
				border.setAllSides(val);       
	
				jQuery('#accordion-'+ smile_panel +' #width-allsides').val(val);
				jQuery('#accordion-'+ smile_panel +' #width-top').val(val);
				jQuery('#accordion-'+ smile_panel +' #width-left').val(val);
				jQuery('#accordion-'+ smile_panel +' #width-right').val(val);
				jQuery('#accordion-'+ smile_panel +' #width-bottom').val(val);
	
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-width-allsides').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-width-top').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-width-left').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-width-right').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
				jQuery('#accordion-'+ smile_panel +' #slider-width-bottom').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
	
				border.refresh();
				
			},
			stop: function( event, ui ) {
				border.refresh();
			},
			create: function( event, ui ) {
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-width-top').slider({
			value: jQuery('#accordion-'+ smile_panel +' #width-top').val(),
			min: 0,
			max: 50,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #width-top'));
				border.bw_t = val;
				border.refresh();
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-width-left').slider({
			value: jQuery('#accordion-'+ smile_panel +' #width-left').val(),
			min: 0,
			max: 50,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #width-left'));
				border.bw_l = val;
				border.refresh();
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ){
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-width-right').slider({
			value: jQuery('#accordion-'+ smile_panel +' #width-right').val(),
			min: 0,
			max: 50,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #width-right'));
				border.bw_r = val;
				border.refresh();
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ) {
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #slider-width-bottom').slider({
			value: jQuery('#accordion-'+ smile_panel +' #width-bottom').val(),
			min: 0,
			max: 50,
			step: 1,
			slide: function(event, ui) {
				var val = _getFromFieldBorder(ui.value, 0, 500, jQuery('#accordion-'+ smile_panel +' #width-bottom'));
				border.bw_b = val;
				border.refresh();
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			},
			create: function( event, ui ) {
				var leftMarginToSlider = jQuery( this ).find('.ui-slider-handle').css('left');
				jQuery( this ).find('.range-quantity').css('width',leftMarginToSlider);
			}
		});
	
		jQuery('#accordion-'+ smile_panel +' #width-allsides').on('keyup change', function() {
	
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #all-sides'));
			border.setAllSides(val);
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-allsides').slider('value', val);
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-width-allsides').find('.ui-slider-handle').css('left');
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-allsides').find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #width-top').val(val);
			jQuery('#accordion-'+ smile_panel +' #width-left').val(val);
			jQuery('#accordion-'+ smile_panel +' #width-right').val(val);
			jQuery('#accordion-'+ smile_panel +' #width-bottom').val(val);
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-allsides').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-width-top').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-width-left').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-width-right').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
			jQuery('#accordion-'+ smile_panel +' #slider-width-bottom').slider('value', val).find('.range-quantity').css('width',leftMarginToSlider);
		
		});
	
		jQuery('#width-top').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #width-top'));
			border.bw_t = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-top').slider('value', val);
	
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-width-top').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-width-top').find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		jQuery('#accordion-'+ smile_panel +' #width-left').on('keyup change', function () {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #width-left'));
			border.bw_l = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-left').slider('value', val);
	
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-width-left').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-width-left').find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		jQuery('#accordion-'+ smile_panel +' #width-right').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #width-right'));
			border.bw_r = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-right').slider('value', val);
	
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-width-right').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-width-right').find('.range-quantity').css('width',leftMarginToSlider);
		});
	
		jQuery('#accordion-'+ smile_panel +' #width-bottom').on('keyup change', function() {
			var val = _getFromFieldBorder(jQuery(this).val(), 0, 500, jQuery('#accordion-'+ smile_panel +' #width-bottom'));
			border.bw_b = val;
			border.refresh();
	
			jQuery('#accordion-'+ smile_panel +' #slider-width-bottom').slider('value', val);
	
			var leftMarginToSlider = jQuery('#accordion-'+ smile_panel +' #slider-width-bottom').find('.ui-slider-handle').css('left');
			jQuery('#accordion-'+ smile_panel +' #slider-width-bottom').find('.range-quantity').css('width',leftMarginToSlider);
		});	

	});
});