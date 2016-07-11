
jQuery.noConflict();

(function($) {
				
	"use strict";
	
	// ======================================================
	// Helper functions
	// ======================================================
	
	// elements main var
	var tg_element_name = '',
		tg_element_id = 1,
		elements_content = $('.tg-panel-element').data('elements-content'),
		tg_anim = $('[data-element-animations]').data('element-animations');
		
	$('.tg-panel-element').removeAttr('data-elements-content');
	$('[data-element-animations]').removeAttr('data-element-animations');
	
	var unvalid_rules = [
		'positions-unit',
		'z-index',
		'float',
		'width',
		'height',
		'width-unit',
		'height-unit',
		'margin-unit',
		'padding-unit',
		'border-unit',
		'border-radius-unit',
		'box-shadow-unit',
		'box-shadow-color',
		'box-shadow-horizontal',
		'box-shadow-vertical',
		'box-shadow-blur',
		'box-shadow-size',
		'box-shadow-inset',
		'text-shadow-unit',
		'text-shadow-color',
		'text-shadow-horizontal',
		'text-shadow-vertical',
		'text-shadow-blur',
		'letter-spacing-unit',
		'word-spacing-unit',
		'background-position-x-unit',
		'background-position-y-unit',
		'top','bottom','left','right',
		'line-height-unit',
		'font-size-unit',
		'background-image',
		'position',
		'overflow',
		'opacity',
		'visibility',
		'custom-rules',
		'margin-unit',
		'margin-top',
		'margin-right',
		'margin-bottom',
		'margin-left',
		'padding-unit',
		'padding-top',
		'padding-right',
		'padding-bottom',
		'padding-left',
		'border-unit',
		'border-top',
		'border-right',
		'border-bottom',
		'border-left',
		'border-radius-unit',
		'border-top-left-radius',
		'border-top-right-radius',
		'border-bottom-right-radius',
		'border-bottom-left-radius',
	];
		
	//Check element in droppable area
	function check_dropped_element() {
		
		$('.tg-item-inner [data-item-area]').removeClass('tg-area-filled')
			.each(function() {
			var $area  = $(this),
				$elems = $area.find('div:not(.tg-item-clear):not(.ui-sortable-helper)');
			if ($elems.length > 0) {
				$elems.each(function() {
					if ($(this).css('position') === 'relative') {
						$area.addClass('tg-area-filled');
						return false;
					}
				});
			}
		});
			
	}
	
	// auto update elment name in the layout
	function update_element_name(el) {

		if (!el.hasClass('tg-element-init')) {
			generate_unique_id();
			tg_element_name = 'tg-element-'+tg_element_id;
			el.height('auto').data('name', tg_element_name).addClass('tg-element-init '+tg_element_name);
			
			var element_settings = el.data('settings');
			if (element_settings) {
				style_change(tg_element_name, element_settings['styles']['idle_state'], 'idle_state');
				if (element_settings['styles']['is_hover']) {
					style_change(tg_element_name, element_settings['styles']['hover_state'], 'hover_state');
				}
			}
		}
			
		tg_element_name = el.data('name');	
		$('.tg-element-draggable').removeClass('tg-element-selected');
		$('.tg-panel-element .idle_state, .tg-panel-element .hover_state').data('element',tg_element_name);
		el.addClass('tg-element-selected');
		update_element_list();
				
	}
	
	function generate_unique_id() {
		
		var existing_el = [];
		
		$('.tg-item-inner .tg-element-init').each(function() {
			existing_el.push(this.className.match(/tg-element-(\d+)/)[1]);
		});
		
		existing_el.sort(function sortNumber(a,b) {return a - b;})
		existing_el[existing_el.length+1] = '';

		for (var i = 0; i < existing_el.length; i++) {
			if (existing_el[i] != i+1) {
            	tg_element_id = i+1;
				break;
			}
		}
		
		tg_element_id = (tg_element_id == existing_el.length ) ? tg_element_id+1 : tg_element_id;
		
		return tg_element_id;
	
	}
	
	
	// update element dropdown list
	function update_element_list() {
			
		var $element_list = $('.tg-element-class select');
		$element_list.html('');
			
		$('.tg-skin-build-inner .tg-element-draggable').each(function() {
			var value = $(this).data('name');
			if (value) {
				name = ($(this).hasClass('tg-line-break')) ? 'tg-line-break' : value;
				$element_list.append('<option value="'+value+'">'+name+'</option>');
			}
		});
		$element_list.val(tg_element_name);
		update_select($('.tg-element-class'));
	}
	
	
	// change select on element click
	function update_select(el) {
			
		el.find('.tomb-select-holder').each(function() {
				
			var $this = $(this),
				value = $this.find('select option:selected').text();
			$this.find('.tomb-select-value').text(value);
				
			if (value) {
				$this.find('.tomb-select-placeholder').hide();
				$this.find('.tomb-select-clear').show();
			} else {
				$this.find('.tomb-select-placeholder').show();
				$this.find('.tomb-select-clear').hide();
			}
				
		});
			
	}
		
	// change colors on element click
	function update_colors(el) {
			
		el.find('.tomb-colorpicker').each(function() {
			$(this).addClass('no-change').wpColorPicker('color', $(this).val()).removeClass('no-change');
			if (!$(this).val()) {
				$(this).closest('.tomb-row').find('.wp-color-result').attr('style', '');
			}
		});
			
	}
		
	// change sliders on element click
	function update_sliders(el) {
			
		el.find('.tomb-slider-range').each(function() {
				
			var $this = $(this),
				min   = $this.data('min'),
				max   = $this.data('max'),
				sign  = $this.data('sign'),
				input = $this.closest('.tomb-type-slider').find('input'),
				value = $this.closest('.tomb-type-slider').find('.tomb-slider-input').val(),
				percent = 100/(max-min)*(value-min)+'%';
				
			$this.add(input).addClass('no-change');
			$this.nextAll('input.tomb-slider').val(value+sign);
			$this.find('.ui-slider-range').width(percent);
			$this.find('.ui-slider-handle').css('left', percent);
			$this.add(input).removeClass('no-change');
				
		});
			
	}
		
	// change image field on element click
	function update_image(el) {
			
		el.find('.tomb-type-image').each(function() {
				
			var $this = $(this),
				value = $this.find('input').val();
					
			if (value) {
				$this.find('.tomb-img-field').css('background-image','url('+value+')').show();
				$this.find('.tomb-image-remove').css('display','inline-block');
			} else {
				$this.find('.tomb-img-field').hide();
				$this.find('.tomb-image-remove').hide();
			}
				
		});
			
	}
	
	// check item content position	
	function check_content_position() {
			
		$('.tg-item-content-holder, [data-target="tg-top-content-styles"], [data-target="tg-bottom-content-styles"]').hide();
		var position = $('select[name="content_position"]').val();
			
		switch (position) {
			case 'both':
        		$('.tg-item-content-holder[data-position="top"], [data-target="tg-top-content-styles"]').show();
				$('.tg-item-content-holder[data-position="bottom"], [data-target="tg-bottom-content-styles"]').show();
        		break;
    		case 'top':
        		$('.tg-item-content-holder[data-position="top"], [data-target="tg-top-content-styles"]').show();
        		break;
			case 'bottom':
				$('.tg-item-content-holder[data-position="bottom"], [data-target="tg-bottom-content-styles"]').show();
				break;
		}
			
	}
		
	// check item style	
	function check_skin_style() {
			
		var skin_style = $('select[name="skin_style"]').val(),
			media_content = $('input[name="media_content"]').is(':checked'),
			$content_none = $('[name="content_position"]').find('option[value="none"]');
		
		if (skin_style === 'grid') {
			$('.tg-item-content-holder').hide();
			$('[data-target="tg-top-content-styles"], [data-target="tg-bottom-content-styles"]').hide();
		} else {
			check_content_position();
		}
		
		if (skin_style === 'masonry' && !media_content) {
			$content_none.attr('disabled', true);
			$('.tg-item-media-holder').hide();
			$('.tomb-row.overlay_type').hide();
			$('.tomb-row.overlay_alignment').hide();
		} else {
			$content_none.removeAttr('disabled');
			$('.tg-item-media-holder').show();
			$('.tomb-row.overlay_type').show();
			$('.tomb-row.overlay_alignment').show();
		}
		
	}
		
	// check item layout size	
	function check_item_size() {
		
		var skin_style = $('select[name="skin_style"]').val(),
			ratio_X = (skin_style === 'grid') ? $('[name="item_x_ratio"]').val() : 4,
			ratio_Y = (skin_style === 'grid') ? $('[name="item_y_ratio"]').val() : 3,
			col_nb  = $('[name="skin_col"]').val(),
			row_nb  = (skin_style === 'grid') ? $('[name="skin_row"]').val() : 1,
			item_w  = $('.tg-item-inner').data('width');
		
		$('.tg-skin-build-inner').attr('data-col',col_nb);
		$('.tg-item-inner').width(col_nb*item_w);
		$('.tg-item-media-holder').height(row_nb*item_w*ratio_Y/ratio_X);
	
	}
	
	var $tg_overlay = $('.tg-item-overlay').clone();
	
	// check overlay type
	function check_overlay_type() {
		
		var overlay_type = $('select[name="overlay_type"]').val();
			
		$('.tg-item .tg-item-overlay').remove();
		
		if (overlay_type === 'full') {
			$('.tg-overlay-positions').hide();
			$('.tg-overlay-positions [data-target="tg-overlay-center"]').trigger('click');
			$('.tg-item-media-content').prepend($tg_overlay.attr('data-position', 'center'));
		} else if (overlay_type === 'content') {
			$('.tg-overlay-positions').show();
			$('.tg-overlay-positions [data-target="tg-overlay-top"]').trigger('click');
			$('.tg-item-overlay-content').prepend($tg_overlay);
			$('.tg-item-overlay').each(function(index, element) {
                $(this).attr('data-position', $(this).closest('.tg-item-overlay-content').data('position'));
            });
		}
	
	}
	
	// check overlay type
	function check_overlay_align() {
		
		var overlay_align = $('select[name="overlay_alignment"]').val();
		
		$('.tg-item-media-content').removeClass('tg-align-left tg-align-center tg-align-right')
		
		if (overlay_align === 'left') {
			$('.tg-item-media-content').addClass('tg-align-left');
		} else if (overlay_align === 'center') {
			$('.tg-item-media-content').addClass('tg-align-center');
		} else if (overlay_align === 'right') {
			$('.tg-item-media-content').addClass('tg-align-right');
		}
	
	}
	

	// search indexof array in array
	function containsAll(needles, haystack){ 
	
		for(var i = 0 , len1 = haystack.length; i < len1; i++){
			for(var y = 0 , len2 = needles.length; y < len2; y++){
				if (haystack[i].indexOf(needles[y]) >= 0) return true;
			}
		}
		return false;
		
	}
	
	// Helper to get element with new data
	$.fn.TG_filterByData = function (prop, val) {
		
        var $self = this;
        if (typeof val === 'undefined') {
            return $self.filter(
                function () { return typeof $(this).data(prop) !== 'undefined'; }
            );
        }
		
        return $self.filter(
            function () { return $(this).data(prop) == val; }
        );
		
    };
	
	// Sanitize css
	function sanitize_CSS(input) {
	
		var output = input.replace(/<script[^>]*?>.*?<\/script>/gi, '').
						replace(/<[\/\!]*?[^<>]*?>/gi, '').
						replace(/<style[^>]*?>.*?<\/style>/gi, '').
						replace(/<![\s\S]*?--[ \t\n\r]*>/gi, '');
						
		return output;
			
	};
	
	// Sanitize html
	function sanitize_HTML(input) {
	
		var output = input.replace(/<script[^>]*?>.*?<\/script>/gi, '').
						replace(/<script[^>]*?>/gi, '').
						replace(/<\/script[^>]*?>/gi, '').
						replace(/<style[^>]*?>/gi, '').
						replace(/<\/style[^>]*?>/gi, '').
						replace(/<style[^>]*?>.*?<\/style>/gi, '');
						
		return output;
			
	};
	
	// Sanitize string
	function sanitize_string(string) {
		
		if (string) {
			return string.toLowerCase().replace(/ /g, '-').replace(/[^-0-9a-z_-]/g,'');
		}
		
	}
	
	// update icon field
	function update_icon_field() {
		
		$('.tg-icon-holder').each(function() {
			var icon = $(this).find('input').val();
			$(this).find('i').attr('class','');
			$(this).find('i').addClass(icon);
		});
		
	}
	
	// ======================================================
	// Add important field for css
	// ======================================================
	
	// Add important option for each css rules
	$('.tg-component-styles .tomb-row').each(function() {
		
		var name,
			prefix  = $(this).closest('[data-prefix]').data('prefix'),
			classes = $(this).attr('class').split(' '),
			title = elements_content['important_string'],
			value = [
			'custom-rules',
			'custom_desc',
			'visibility_desc',
			'shadow-color',
			'shadow-inset'
		];
		
		if (!containsAll(value, classes)) {
			for (var i = 0, l = classes.length; i < l; i++) {
				if (classes[i].indexOf(prefix) >= 0) {
					name = classes[i]+'-important';
				}
			}
			$(this).append('<div class="tg-important-rule"><input title="'+title+'" name="'+name+'" type="checkbox"><span></span></div>');
		}
		
    });
	
	// ======================================================
	// Build skin function
	// ======================================================

	
	// build skin from settings
	function build_skin() {
		
		if (typeof tg_skin_settings !== 'undefined') {
			
			var settings = $.parseJSON(JSON.stringify(tg_skin_settings)),
				item_settings = (settings) ? settings.item : '',
				elements_settings = (settings) ? settings.elements : '';

			// set item settings
			for (var settings in item_settings) {
					
				var prefix = $('.tg-panel-item').find('[data-settings="'+settings+'"]').data('prefix'),
					prefix = (prefix) ? prefix : '',
					values = item_settings[settings];
				
				if (settings == 'layout') {
					
					for (var item in values) {
						var $input = $('.tg-panel-item').find('[data-settings="'+settings+'"] [name="'+prefix+item.replace(/(['"])/g, "\\$1")+'"]');
						if ($input.is(':checkbox')) {
							$input.prop('checked', values[item])
						} else {
							$input.val(values[item]);
						}
					}
					
				} else if (settings == 'containers') {
					
					for (var element in values) {
						
						for (var type in values[element]) {
							
							if (type === 'styles') {
								
								for (var state in values[element][type]) {
									
									var el = element.replace(/(['"])/g, "\\$1"),
										tp = type.replace(/(['"])/g, "\\$1"),
										st = state.replace(/(['"])/g, "\\$1");
									prefix = $('.tg-panel-item').find('[data-settings="'+el+'"] [data-settings="'+tp+'"] [data-settings="'+st+'"]').data('prefix');
									
									if (st === 'is_hover') {
										$('.tg-panel-item').find('[data-settings="'+el+'"] [name="'+st+'"]').prop('checked', values[element][type][state]);
									} else {
										
										for (var item in values[element][type][state]) {
											var $input = $('.tg-panel-item').find('[name="'+prefix+item.replace(/(['"])/g, "\\$1")+'"]');
											if ($input.is(':checkbox')) {
												$input.prop('checked', values[element][type][state][item])
											} else {
												$input.val(values[element][type][state][item]);
											}
											
										}

										if ((st == 'hover_state' && values[element][type]['is_hover']) || st == 'idle_state') {
											style_change(element, values[element][type][state], state);
										}
										
									}
								}
								
							} else if (type === 'animation') {
								
								var el = element.replace(/(['"])/g, "\\$1"),
									tp = type.replace(/(['"])/g, "\\$1");
								prefix = $('.tg-panel-item').find('[data-settings="'+tp+'"] [data-settings="'+el+'"]').data('prefix');
								
								for (var item in values[element][type]) {
									$('.tg-panel-item').find('[name="'+prefix+item.replace(/(['"])/g, "\\$1")+'"]').val(values[element][type][item]);
								}

								if (prefix) {
									pre_process_animation(prefix.replace('_', ''), element, values[element][type]);
								}
								
							}
						}
					}
				} else if (settings == 'global_css') {
					$('.tg-panel-item').find('[data-settings="'+settings+'"] [name="'+settings+'"]').val(item_settings[settings]);	
					process_global_css(item_settings[settings]);
				}
								
			}
	
			// set elements in layout
			for (var settings in elements_settings) {
				
				for (var item in elements_settings[settings]) {

					var content = elements_settings[settings][item]['content'].replace(/\\(.)/mg, "$1");
					var element = $('<div class="tg-element-draggable tg-element-init '+item+'">'+content+'</div>');
					var $area   = $('.tg-skin-build-inner [data-item-area="'+settings+'"]');
					element.data('settings', elements_settings[settings][item]).data('name', item);
					if ($area.find('.tg-item-clear').length) {
						$(element).insertBefore($area.find('.tg-item-clear'));
					} else {
						$area.append(element);
					}
					style_change(item, elements_settings[settings][item]['styles']['idle_state'], 'idle_state');
					if (elements_settings[settings][item]['styles']['is_hover']) {
						style_change(item, elements_settings[settings][item]['styles']['hover_state'], 'hover_state');
					}
					pre_process_animation('element', item, elements_settings[settings][item]['animation']);
					// set element color
					TG_element_color('.'+item, elements_settings[settings][item]['source']);
					
				}
				
			}
			
			check_dropped_element();
		
		}
		
	}
	
	// build skin
	build_skin();
	check_content_position();
	check_overlay_align();
	check_skin_style();
	check_item_size();
	update_icon_field();
	
	// prepare element settings
	$('.tg-elements-inner .tg-element-holder').each(function(index, element) {
		var $this = $(this),
			slug  = $this.find('[data-slug]').data('slug');
		$this.find('.tg-element-draggable').data('settings', custom_element[slug]);
		//$this.find('script').remove();
		// set element color
		if ([slug] in custom_element) {
			TG_element_color('[data-slug="'+slug+'"]', custom_element[slug]['source']);
		}
    });
	

	// ======================================================
	// Generate css styles
	// ======================================================
	
	function style_change(el, settings, state) {

		var fields   = (!settings) ? el.closest('[data-settings]') : '',
			element  = (!settings) ? fields.data('element') : el,
			state    = (!settings) ? fields.data('settings') : state,
			is_hover = (!settings) ? fields.find('[name="is_hover"]').is(':checked') : true,
			prefix   = (!settings) ? fields.data('prefix') : '',
			pseudo   = (state == 'hover_state') ? ':hover' : '',
			selector = '[data-settings="'+state+'"]',
			str_css  = '',
			arr_css  = [],
			shadows  = [];

		if (element || settings) {
			
			if (state === 'hover_state' && !is_hover) {
				$('style[class=\''+element+'\']'+selector).remove();
				return false;
			}
			
			if (!settings) {

				fields.find('.tomb-row input, .tomb-row select, .tomb-row textarea').each(function() {
					
					var $this  = $(this),
						unit   = $(this).closest('.tomb-row').find('.tg-css-unit').val(),
						impor  = ($(this).closest('.tomb-row').find('.tg-important-rule input').prop('checked')) ? ' !important' : '',
						hidden = $this.closest('.tomb-row').css('display'),
						value  = (!$this.is(':checkbox')) ? $this.val() : $this.prop('checked'),
						name   = $this.attr('name');
						name   = (name) ? name.replace(prefix,'') : '';

					if (name && value && $.inArray(name,unvalid_rules) < 0 && hidden != 'none' && name.indexOf('important') === -1) {
						unit = (unit) ? unit : '',
						str_css += name+':'+value+unit+impor+';';	
					}
						
					arr_css[name] = value;
					
				});
				
			} else {

				for (var name in settings) {
					
					var unit  = settings[name+'-unit'],
						impor = (settings[name+'-important']) ? ' !important' : '',
						value = settings[name];

					if (name && value && $.inArray(name,unvalid_rules) < 0 && name.indexOf('important') === -1) {
						unit = (unit) ? unit : '',
						str_css += name+':'+value+unit+impor+';';	
					}
						
					arr_css[name] = value;
				}
			
			}

			str_css += (state === 'idle_state') ? process_position(arr_css) : '';
			str_css += (state === 'idle_state') ? process_zindex(arr_css) : '';
			str_css += (state === 'idle_state') ? process_float(arr_css) : '';
			str_css += (state === 'idle_state') ? process_positions(arr_css, element) : '';
			str_css += process_visibility(element, state, arr_css);
			str_css += process_sizes(arr_css);
			str_css += process_margin(arr_css);
			str_css += process_padding(arr_css);
			str_css += process_border_width(arr_css);
			str_css += process_border_radius(arr_css);
			str_css += process_box_shadows(arr_css);
			str_css += process_text_shadows(arr_css);
			str_css += process_background_image(arr_css);
			str_css += process_custom_rules(arr_css);
			
			
			var $styles = $('style[class=\''+element+'\']'+selector),
				target = (state == 'hover_state') ? '.tg-skin-build-inner.tg-item-preview' : '.tg-skin-build-inner';
			
			if (str_css) {	
				if ($styles.length) {
					$styles.html(
						target+' .'+element+':not(.tg-line-break)'+pseudo+','+
						target+' .light .'+element+':not(.tg-line-break)'+pseudo+','+
						target+' .dark .'+element+':not(.tg-line-break)'+pseudo+'{'+
								str_css+
						'}'
					);
				} else {
					
					$('.tg-skin-elements-css').append(
						'<style type="text/css" class=\''+element+'\' data-settings="'+state+'">'+
							target+' .'+element+':not(.tg-line-break)'+pseudo+','+
							target+' .light .'+element+':not(.tg-line-break)'+pseudo+','+
							target+' .dark .'+element+':not(.tg-line-break)'+pseudo+'{'+
								str_css+
							'}'+
						'</style>'
					);
				}
			} else {
				$styles.html('');
			}

		}
			
	}
	
	// process position rule
	function process_position(arr_css) {
		
		return (arr_css['position'] != undefined && arr_css['position']) ? 'position:'+arr_css['position']+';' : '';
		
	}
	
	// process float rule
	function process_float(arr_css) {
		
		if (arr_css['display'] == 'inline-block') {
		
			var important = (arr_css['float-important']) ? ' !important' : '';
			
			return (arr_css['float'] != undefined && arr_css['float']) ? 'float:'+arr_css['float']+important+';' : '';
		
		}
		
		return '';
		
	}
	
	// process position absolute rule for important (prevent css issue with jquery draggable)
	function process_positions(arr_css, element) {
		
		var position = '',
			// get important rule
			important = (arr_css['positions-important']) ? ' !important' : '',
			// get the unit (px/em/%)
			ps_un = (arr_css['positions-unit'] != undefined) ? arr_css['positions-unit']+important+';' : 'px'+important+';';
			
		if (arr_css['position'] == 'absolute' || element === 'tg-item-overlay') {
			
			// get each position value
			position += (arr_css['top'] != undefined && arr_css['top'] != '') ? 'top:'+arr_css['top']+ps_un : '';
			position += (arr_css['bottom'] != undefined && arr_css['bottom'] != '') ? 'bottom:'+arr_css['bottom']+ps_un : '';
			position += (arr_css['left'] != undefined && arr_css['left'] != '') ? 'left:'+arr_css['left']+ps_un : '';
			position += (arr_css['right'] != undefined && arr_css['right'] != '') ? 'right:'+arr_css['right']+ps_un : '';
			return position;
		
		}
		
		return '';
		
	}
	
	// process position absolute rule for important (prevent css issue with jquery draggable)
	function process_zindex(arr_css, element) {
		
		var important = (arr_css['z-index-important']) ? ' !important' : '',
			z_index = (arr_css['z-index']);
		
		return (z_index > 0) ? 'z-index:'+z_index+important+';' : '';
	
	}
	
	// process size rules
	function process_sizes(arr_css) {
		
		var size = '',
			width  = (arr_css['width']) ? arr_css['width'] : '',
			width_unit  = (arr_css['width-unit']) ? arr_css['width-unit'] : 'px',
			width_important  = (arr_css['width-important']) ? ' !important' : '',
			height = (arr_css['height'])   ? arr_css['height']   : '',
			height_unit = (arr_css['height-unit'])   ? arr_css['height-unit']   : 'px',
			height_important  = (arr_css['height-important']) ? ' !important' : '';
			
		size += (arr_css['width']) ? 'width:'+width+width_unit+width_important+';' : '';
		size += (arr_css['width']) ? 'min-width:'+width+width_unit+width_important+';' : '';
		size += (arr_css['height']) ? 'height:'+height+height_unit+height_important+';' : '';
		size += (arr_css['height']) ? 'min-height:'+height+height_unit+height_important+';' : '';
			
		return (size) ? size : '';
	
	}
	
	// process margin rule
	function process_margin(arr_css) {
		
		// get important rule
		var important = (arr_css['margin-important']) ? ' !important' : '',
			// get the unit (px/em/%)
			mg_u = (arr_css['margin-unit'])   ? arr_css['margin-unit'] : 'px',
			// get each margin value
			mg_t = (arr_css['margin-top'])    ? arr_css['margin-top']    : 0,
			mg_r = (arr_css['margin-right'])  ? arr_css['margin-right']  : 0,
			mg_b = (arr_css['margin-bottom']) ? arr_css['margin-bottom'] : 0,
			mg_l = (arr_css['margin-left'])   ? arr_css['margin-left']   : 0;
		
		// handle margin shorthand
		if (mg_t == 0 && mg_r == 0 && mg_b == 0 && mg_l == 0) {
			return '';//'margin: 0'+important+';';
		} else if ([mg_t,mg_b,mg_l,mg_r].every(function(v) { return v === mg_t; })) {
			return 'margin: '+mg_t+mg_u+important+';';
		} else if (mg_t == mg_b && mg_l == mg_r) {
			return 'margin: '+mg_t+mg_u+' '+mg_r+mg_u+important+';';
		} else {
			return 'margin: '+mg_t+mg_u+' '+mg_r+mg_u+' '+mg_b+mg_u+' '+mg_l+mg_u+important+';';
		}
		
		return '';
		
	}
	
	// process padding rules
	function process_padding(arr_css) {
		
		// get important rule
		var important = (arr_css['padding-important']) ? ' !important' : '',
			// get the unit (px/em/%)
			pd_u = (arr_css['padding-unit'])   ? arr_css['padding-unit'] : 'px',
			// get each margin value
			pd_t = (arr_css['padding-top'])    ? arr_css['padding-top']    : 0,
			pd_r = (arr_css['padding-right'])  ? arr_css['padding-right']  : 0,
			pd_b = (arr_css['padding-bottom']) ? arr_css['padding-bottom'] : 0,
			pd_l = (arr_css['padding-left'])   ? arr_css['padding-left']   : 0;
		
		// handle padding shorthand
		if (pd_t == 0 && pd_r == 0 && pd_b == 0 && pd_l == 0) {
			return '';//'padding: 0'+important+';';
		} else if ([pd_t,pd_b,pd_l,pd_r].every(function(v) { return v === pd_t; })) {
			return 'padding: '+pd_t+pd_u+important+';';
		} else if (pd_t == pd_b && pd_l == pd_r) {
			return 'padding: '+pd_t+pd_u+' '+pd_r+pd_u+important+';';
		} else {
			return 'padding: '+pd_t+pd_u+' '+pd_r+pd_u+' '+pd_b+pd_u+' '+pd_l+pd_u+important+';';
		}
		
		return '';
		
	}
	
	// process border width
	function process_border_width(arr_css) {
			
		// get important rule
		var important = (arr_css['border-important']) ? ' !important' : '',
			// get the unit (px/em/%)
			bd_u = (arr_css['border-unit']) ? arr_css['border-unit'] : 'px',
			// get each border width value
			bd_t = (arr_css['border-top'])    ? arr_css['border-top']+bd_u+' ' : '0 ',
			bd_r = (arr_css['border-right'])  ? arr_css['border-right']+bd_u+' ' : '0 ',
			bd_b = (arr_css['border-bottom']) ? arr_css['border-bottom']+bd_u+' ' : '0 ',
			bd_l = (arr_css['border-left'])   ? arr_css['border-left']+bd_u : '0';
			
		// is there is at least one value superior to 0
		if (bd_t != 0 || bd_r != 0 || bd_b != 0 || bd_l != 0) {
			return 'border-width: '+bd_t+bd_r+bd_b+bd_l+important+';';
		}
		
		return '';

	}
	
	// process border radius
	function process_border_radius(arr_css) {
			
		// get important rule
		var important = (arr_css['border-radius-important']) ? ' !important' : '',
			// get the unit (px/em/%)
			bd_u  = (arr_css['border-radius-unit']) ? arr_css['border-radius-unit'] : 'px',
			// get each border radius value
			bd_tl = (arr_css['border-top-left-radius'])     ? arr_css['border-top-left-radius']+bd_u+' ' : '0 ',
			bd_tr = (arr_css['border-top-right-radius'])    ? arr_css['border-top-right-radius']+bd_u+' ' : '0 ',
			bd_br = (arr_css['border-bottom-right-radius']) ? arr_css['border-bottom-right-radius']+bd_u+' ' : '0 ',
			bd_bl = (arr_css['border-bottom-left-radius'])  ? arr_css['border-bottom-left-radius']+bd_u : '0 ';
			
		// is there is at least one value superior to 0
		if (bd_tl != 0 || bd_tr != 0 || bd_br != 0 || bd_bl != 0) {
			return 'border-radius: '+bd_tl+bd_tr+bd_br+bd_bl+important+';';
		}
		
		return '';
		
	}
	
	// process box-shadow rules
	function process_box_shadows(arr_css) {
		
		if (Object.keys(arr_css).length > 0) {
			
			var important = (arr_css['box-shadow-important']) ? ' !important' : '',
			
				sd_un  = (arr_css['box-shadow-unit'] != undefined) ? arr_css['box-shadow-unit'] : 'px',
				sd_hz  = (arr_css['box-shadow-horizontal'] != undefined && arr_css['box-shadow-horizontal']) ? arr_css['box-shadow-horizontal']+sd_un+' ' : '0 ',
				sd_vc  = (arr_css['box-shadow-vertical'] != undefined && arr_css['box-shadow-vertical']) ? arr_css['box-shadow-vertical']+sd_un+' ' : '0 ',
				sd_bl  = (arr_css['box-shadow-blur'] != undefined && arr_css['box-shadow-blur']) ? arr_css['box-shadow-blur']+sd_un+' ' : '0 ',
				sd_sz  = (arr_css['box-shadow-size'] != undefined && arr_css['box-shadow-size']) ? arr_css['box-shadow-size']+sd_un+' ' : '0 ',
				sd_co  = (arr_css['box-shadow-color'] != undefined && arr_css['box-shadow-color']) ? arr_css['box-shadow-color'] : 'rgba(0,0,0,0)',
				sd_in  = (arr_css['box-shadow-inset'] != undefined && arr_css['box-shadow-inset']) ? 'inset ' : '';
				
			if (parseInt(sd_hz) || parseInt(sd_vc) || parseInt(sd_bl) || parseInt(sd_sz)) {
				
				var sd_css = sd_hz+sd_vc+sd_bl+sd_sz+sd_co,
				css_rule  = '-webkit-box-shadow:'+sd_in+sd_css+important+';';
				css_rule += '-moz-box-shadow:'+sd_in+sd_css+important+';';
				css_rule += 'box-shadow:'+sd_in+sd_css+important+';';
				return css_rule;
				
			}
			
		}
		
		return '';
		
	}
	
	// process text-shadow rules
	function process_text_shadows(arr_css) {

		if (Object.keys(arr_css).length > 0) {
			
			var important = (arr_css['text-shadow-important']) ? ' !important' : '',
			
				sd_un  = (arr_css['text-shadow-unit'] != undefined) ? arr_css['text-shadow-unit'] : 'px',
				sd_hz  = (arr_css['text-shadow-horizontal'] != undefined && arr_css['text-shadow-horizontal']) ? arr_css['text-shadow-horizontal']+sd_un+' ' : '0 ',
				sd_vc  = (arr_css['text-shadow-vertical'] != undefined && arr_css['text-shadow-vertical']) ? arr_css['text-shadow-vertical']+sd_un+' ' : '0 ',
				sd_bl  = (arr_css['text-shadow-blur'] != undefined && arr_css['text-shadow-blur']) ? arr_css['text-shadow-blur']+sd_un+' ' : '0 ',
				sd_co  = (arr_css['text-shadow-color'] != undefined && arr_css['text-shadow-color']) ? arr_css['text-shadow-color'] : 'rgba(0,0,0,0)';
				
			if (parseInt(sd_hz) || parseInt(sd_vc) || parseInt(sd_bl)) {

				return 'text-shadow:'+sd_hz+sd_vc+sd_bl+sd_co+important+';';
				
			}
			
		}
		
		return '';
		
	}
	
	// process background-image rule
	function process_background_image(arr_css) {
		var important = (arr_css['background-image']) ? ' !important' : '';
		return (arr_css['background-image'] != undefined && arr_css['background-image']) ? 'background-image:url('+arr_css['background-image']+')'+important+';' : '';
	}
	
	// process custom rules
	function process_custom_rules(arr_css) {
		return (arr_css['custom-rules'] != undefined && arr_css['custom-rules']) ? sanitize_CSS(arr_css['custom-rules']) : '';
	}
		
	// process visibility rules
	function process_visibility(element, state, arr_css) {
		
		var visibility = arr_css['visibility'],
			overflow   = arr_css['overflow'],
			opacity    = arr_css['opacity'],
			str_css    = '';
			
		var visibility_important = (arr_css['visibility-important']) ? ' !important' : '',
			overflow_important = (arr_css['overflow-important']) ? ' !important' : '',
			opacity_important = (arr_css['opacity-important'] || state === 'hover_state') ? ' !important' : '';
			
		str_css += (arr_css['visibility']) ? 'visibility: '+arr_css['visibility']+visibility_important+';' : '';
		str_css += (arr_css['overflow']) ? 'overflow: '+arr_css['overflow']+overflow_important+';' : '';
		str_css += (arr_css['opacity']) ? 'opacity: '+arr_css['opacity']+opacity_important+';' : '';

		if (state === 'idle_state') {
			
			var $styles = $('style[class=\''+element+'\'][data-settings="visibility"]');
			
			if (!str_css) {
				
				$styles.html('');
				
			} else if (str_css) {
			
				if ($styles.length) {
					$styles.html('.tg-skin-build-inner.tg-item-preview .'+element+'{'+str_css+'}');
				} else {
					$('.tg-skin-elements-css').append(
						'<style type="text/css" class=\''+element+'\' data-settings="visibility">'+
						'.tg-skin-build-inner.tg-item-preview .'+element+':not(.tg-line-break){'+
							str_css+
						'}'+
						'</style>'
					);
				}
			
			}
			
		} else if (state === 'hover_state') {
			
			return str_css;
			
		}
		
		return '';
	
	}
	
	// ======================================================
	// Generate css animation
	// ======================================================
	
	function pre_process_animation(prefix, element, settings) {
			
		var value      = (!settings) ? $('.'+prefix+'_animation_name select').val() : settings['animation_name'],
			state      = (!settings) ? $('.'+prefix+'_animation_state select').val() : settings['animation_state'],
			from       = (!settings) ? $('.'+prefix+'_animation_from select').val() : settings['animation_from'],
			easing     = (!settings) ? $('.'+prefix+'_transition_function select').val() : settings['transition_function'],
			bezier     = (!settings) ? $('.'+prefix+'_transition_bezier input').val() : settings['transition_bezier'],
			transition = (!settings) ? $('.'+prefix+'_transition_duration input').val() : settings['transition_duration']+'ms',
			delay      = (!settings) ? $('.'+prefix+'_transition_delay input').val() : settings['transition_delay']+'ms';
			
		process_animation(element,value,state,from,easing,bezier,transition,delay);
		
	}
	
	/*** add animation on element ***/
	function process_animation(element,value,state,from,easing,bezier,transition,delay) {
		
		if (value) {
			
			var visible    = (tg_anim[value]) ? tg_anim[value].visible : '',
				hidden     = (tg_anim[value]) ? tg_anim[value].hidden : '',
				from       = (!from) ? 'item' : from,
				easing     = (easing == 'cubic-bezier') ? bezier : easing;
			
			$('style[class=\''+element+'\'][data-settings="animate"]').remove();
			
			var anim_selector = {
				'item': '.tg-item-inner:hover',
				'media': ' .tg-item-media-holder:hover',
				'top-content': ' .tg-item-content-holder[data-position=\"top\"]:hover',
				'bottom-content': ' .tg-item-content-holder[data-position=\"bottom\"]:hover'
			};
			
			if (from === 'parent' && element.indexOf('tg-element-') >= 0) {
				var $holder_class = $('.tg-item .'+element);
				from = ($holder_class.closest('.tg-item-media-holder').length) ? 'media' : from;
				from = ($holder_class.closest('.tg-item-content-holder[data-position="top"]').length) ? 'top-content' : from;
				from = ($holder_class.closest('.tg-item-content-holder[data-position="bottom"]').length) ? 'bottom-content' : from;
			}
			
			if (value && value !== 'none') {
				
				var hover_animation = (state == 'show') ? visible : hidden,
					hover_opacity   = (state == 'show') ? 1 : 0,
					idle_animation  = (state == 'show') ? hidden  : visible,
					idle_opacity    = (state == 'show') ? 0  : 1;
					
				var str_css_idle = '',
					str_css_over = '';
				
				str_css_idle += '-webkit-transition: all '+transition+' '+easing+' '+delay+';';
				str_css_idle += '-moz-transition: all '+transition+' '+easing+' '+delay+';';
				str_css_idle += '-ms-transition: all '+transition+' '+easing+' '+delay+';';
				str_css_idle += 'transition: all '+transition+' '+easing+' '+delay+';';
				
				if (value !== 'fade_in') {
					str_css_idle += '-webkit-transform:'+idle_animation+';';
					str_css_idle += '-moz-transform:'+idle_animation+';';
					str_css_idle += '-ms-transform:'+idle_animation+';';
					str_css_idle += 'transform:'+idle_animation+';';
				}
				str_css_idle += 'opacity:'+idle_opacity+';';
				
				if (value !== 'fade_in') {
					str_css_over += '-webkit-transform:'+hover_animation+';';
					str_css_over += '-moz-transform:'+hover_animation+';';
					str_css_over += '-ms-transform:'+hover_animation+';';
					str_css_over += 'transform:'+hover_animation+';';
				}
				str_css_over += 'opacity:'+hover_opacity+';';
				
				$('.tg-skin-elements-css').append(
					'<style type="text/css" class=\''+element+'\' data-settings="animate">'+
					'.tg-item-preview .tg-item-inner .'+element+':not(.tg-line-break){'+
						str_css_idle+
					'}'+
					'</style>'
				);
				
				$('.tg-skin-elements-css').append(
					'<style type="text/css" class=\''+element+'\' data-settings="animate">'+
					'.tg-item-preview '+anim_selector[from]+' .'+element+':not(.tg-line-break){'+
						str_css_over+
					'}'+
					'</style>'
				);
				
			}
			
		}
		
	}
	
	/*** add animation on element ***/
	function process_global_css(css) {
		$('.tg-skin-elements-css').find('[data-settings="global"]').remove();
		if (css) {
			$('.tg-skin-elements-css').append('<style type="text/css" class="global" data-settings="global">'+sanitize_CSS(css)+'</style>');
		}
	}
	
	// ======================================================
	// Handle line break element
	// ======================================================
	
	function line_break_panel() {
		
		if ($('.tg-element-draggable.'+tg_element_name).hasClass('tg-line-break')) {
			$('[data-target="tg-component-sources"]').trigger('click');
			$('.tg-panel-element .tg-component-panel ul li:not([data-target="tg-component-sources"])').hide();
		} else {
			$('.tg-panel-element .tg-component-panel ul li:not([data-target="tg-component-sources"])').show();
		}
		
	}
	
	// ======================================================
	// Handle element settings
	// ======================================================
	
	// update settings in element panel
	function select_element($this) {
		
		$('.tg-panel-element').addClass('tg-visible');
	
			if (!$this.hasClass('tg-element-selected') && $this.closest('.tg-skin-build-inner').length) {	

				update_element_name($this);
				line_break_panel();
				
				var settings = $('.tg-element-draggable.'+tg_element_name).data('settings'),
				$element = $('.tg-panel-element');
				
				if (settings) {	

					var types = ['source', 'animation', 'hover_state', 'idle_state'];
					
					for (var i = 0, l = types.length; i < l; i++) {
							
						var prefix = $element.find('[data-settings="'+types[i]+'"]').data('prefix');
						var values = (types[i].indexOf('_state') == -1) ? settings[types[i]] : settings['styles'][types[i]];
						
						var $panel = $element.find('[data-settings="'+types[i]+'"]');
						$panel.find('input[name], select[name], text[name], textarea[name]').each(function() {
							var name  = $(this).attr('name').replace(prefix, '');
							var value = (values[name]) ? values[name] : '';
							if ($(this).is(':checkbox')) {
								$(this).prop('checked', value);
							} else if ($(this).is('select')) {
								value = (!value && !$(this).data('clear')) ? $(this).find('option:first').val() : value;
								$(this).val(value);
							} else {
								$(this).val(value);
							}
                        });
						
					}
					
					$element.find('[data-settings="styles"] [name="is_hover"]').prop('checked', settings['styles']['is_hover']);
					
					update_select($element);
					update_colors($element);
					update_sliders($element);
					update_image($element);
					update_icon_field();
					// recheck for requiered fields
					TOMB_RequiredField.check();
					
				}
			
		}
		
	}
	
	// save element settings
	function save_element_settings() {
		
		if (tg_element_name) {
			
			var arr_data  = {};
			
			// get element settings
			$('.tg-panel-element > div > [data-settings]').each(function() {
				
				var prefix = $(this).data('prefix');
	
				if ($(this).find('[data-settings]').length) {
					
					var type = $(this).data('settings');
					arr_data[type] = {};
					
					arr_data['content'] = $('.tg-element-draggable.'+tg_element_name).html();
					
					$(this).find('[data-settings]').each(function() {
						prefix = $(this).data('prefix');
						arr_data[type][$(this).data('settings')] = TG_field_value($(this), prefix);
					});
					
					if (type == 'styles') {
						var is_hover = ($(this).find('[name="is_hover"]').is(':checked')) ? true : '';
						arr_data[type]['is_hover'] = is_hover;
					}
					
				} else {
					
					arr_data[$(this).data('settings')] = TG_field_value($(this), prefix);
					
				}
				
			});
			$('.tg-element-draggable.'+tg_element_name).data('settings', arr_data);
			
		}
		
	}
	
	// ======================================================
	// Helper for element content
	// ======================================================

	function format_element() {

		var source_type  = $('[name="source_type"]').val(),
			post_content = $('[name="post_content"]').val(),
			woo_content  = $('[name="woocommerce_content"]').val(),
			content;

		if (source_type === 'post') {
			switch(post_content) {
				case 'get_the_excerpt':
					content = trim_excerpt();
					break;
				case 'get_the_date':
					content = date_format();
					break;
				case 'get_media_button':
					content = lightbox_content();
					break;
				case 'get_the_author':
					content = $('[name="author_prefix"]').val()+' '+elements_content[post_content];
					break;
				case 'get_the_comments_number':
					content = comment_number();
					break;
				case 'get_the_likes_number':
					content = like_number();
					break;
				case 'get_the_terms':
					content = terms_format();
					break;
				case 'get_item_meta':
					content = '_meta: '+$('[name="metadata_key"]').val();
					break;
				default:
					content = elements_content[post_content];
			}
		} else if (source_type === 'woocommerce') {
			switch(woo_content) {
				case 'get_product_rating':
					content = rating_star();
					break;
				case 'get_product_cart_button':
					content = cart_button();
					break;
				case 'get_product_add_to_cart_url':
					content = $('[name="add_to_cart_url_text"]').val();
					content = (content.length > 0) ? content : elements_content[woo_content];
					break;
				default:
					content = elements_content[woo_content];
			}
		} else if (source_type === 'icon') {
			content = '<i class="'+$('[name="element_icon"]').val()+'"></i>';
		} else if (source_type === 'html') {
			content = sanitize_HTML($('[name="html_content"]').val());
		} else if (source_type === 'line_break') {
			content = sanitize_HTML(elements_content['line_break']);
		}
		
		// add content
		$('.tg-element-draggable.'+tg_element_name).html(content);	
		// set element color
		var element_settings = {};
		element_settings['source_type']  = source_type;
		element_settings['post_content'] = post_content;
		element_settings['woocommerce_content']  = woo_content;
		element_settings['title_tag'] = $('[name="title_tag"]').val();
		TG_element_color('.'+tg_element_name, element_settings);
		// handle line break
		line_break_panel();
		
	}
	
	function trim_excerpt() {
		
		var length  = $('[name="excerpt_length"]').val(),
			suffix  = $('[name="excerpt_suffix"]').val(),
			content = elements_content['get_the_excerpt'].substr(0, length);
			
		return content.substr(0, Math.min(content.length, content.lastIndexOf(' '))) + suffix;
		
	}
		
	function date_format() {
		
		var date_format = $('[name="date_format"]').val()
			date_format = (date_format) ? date_format : elements_content['get_the_date'];
			
		if (date_format == 'ago') {
			return '1 day ago';
		} else {
			return date_to_string(date_format);
		}
	}
	
	function comment_number() {
		
		var comment_icon = $('[name="comment_icon"]').val(),
			arr_css = [];
			
		if (comment_icon) {
			
			arr_css['margin-unit']   = $('[name="comment_icon_margin-unit"]').val();
			arr_css['margin-top']    = $('[name="comment_icon_margin-top"]').val();
			arr_css['margin-right']  = $('[name="comment_icon_margin-right"]').val();
			arr_css['margin-bottom'] = $('[name="comment_icon_margin-bottom"]').val();
			arr_css['margin-left']   = $('[name="comment_icon_margin-left"]').val();
			
			var font_size = $('[name="comment_icon_font-size"]').val(),
				font_unit = $('[name="comment_icon_font-size-unit"]').val(),
				font      = (font_size && font_unit) ? 'font-size:'+font_size+font_unit+'!important;' : '',
				float     = $('[name="comment_icon_float"]').val(),
				float     = (float) ? 'float:'+float+';' : 'float:left!important;',
				margin    = process_margin(arr_css),
				color     = ($('#comment_icon_color').val()) ? 'color:'+$('#comment_icon_color').val()+'!important;' : '',
				style     = ' style="position:relative;display:inline-block;padding: 0 1px;'+color+font+float+margin+'"';

			return '<i class="'+comment_icon+'"'+style+'></i><span>2</span>';
			
		} else {
			return elements_content['get_the_comments_number'];
		}
	}
	
	function like_number() {
		
		var arr_css = [];
			
		arr_css['margin-unit']   = $('[name="like_icon_margin-unit"]').val();
		arr_css['margin-top']    = $('[name="like_icon_margin-top"]').val();
		arr_css['margin-right']  = $('[name="like_icon_margin-right"]').val();
		arr_css['margin-bottom'] = $('[name="like_icon_margin-bottom"]').val();
		arr_css['margin-left']   = $('[name="like_icon_margin-left"]').val();
			
		var font_size = $('[name="like_icon_font-size"]').val(),
			font_unit = $('[name="like_icon_font-size-unit"]').val(),
			font      = (font_size && font_unit) ? font_size+font_unit : '',
			float     = $('[name="like_icon_float"]').val(),
			float     = (float) ? 'float:'+float+';' : 'float:left!important;',
			margin    = process_margin(arr_css),
			color     = $('#like_icon_color').val()+' !important',
			style     = 'position:relative;display:inline-block;'+float+margin;
		
		var $markup = $(elements_content['get_the_likes_number']);
		$markup.find('.to-heart-icon').attr('style',style);
		if (font) {
			$markup.find('.to-heart-icon svg').css('height', font);
		}

		if (color) {
			$markup.find('.to-heart-icon svg path').attr('style','fill:'+color+';stroke:'+color);
		}
		
		return $markup;

	}
	
	function lightbox_content() {
		
		var content_type = $('[name="lightbox_content_type"]').val();
		
		if (content_type === 'text' && $('[name="lightbox_image_text"]').val()) {
			return $('[name="lightbox_image_text"]').val();
		} else {
			return '<i class="'+$('[name="lightbox_image_icon"]').val()+'"></i>';
		}
	
	}
	
	function terms_format() {
		
		var separator = $('[name="terms_separator"]').val(),
			content   = elements_content['get_the_terms'],
			arr_css   = [];
		
		arr_css['padding-unit']   = $('[name="terms_padding-unit"]').val();
		arr_css['padding-top']    = $('[name="terms_padding-top"]').val();
		arr_css['padding-right']  = $('[name="terms_padding-right"]').val();
		arr_css['padding-bottom'] = $('[name="terms_padding-bottom"]').val();
		arr_css['padding-left']   = $('[name="terms_padding-left"]').val();
		
		var margin = process_padding(arr_css),
			color  = ($('#comment_icon_color').val()) ? 'color:'+$('#comment_icon_color').val()+'!important;' : '',
			style  = 'style="position:relative;display:inline-block;'+margin+'"';
			separator = (separator) ? '<span>'+separator+'</span>' : '';
		
		return '<span class="tg-item-term" '+style+'>'+content+'1</span>'+separator+'<span class="tg-item-term" '+style+'>'+content+'2</span>';
	
	}
	
	function rating_star() {
		
		var arr_css   = [];

		arr_css['font-size']   = ($('[name="woo_star_font-size"]').val()) ? $('[name="woo_star_font-size"]').val() : 13;
		arr_css['font-unit']   = ($('[name="woo_star_font-size-unit"]').val()) ? $('[name="woo_star_font-size-unit"]').val() : 'px';
		arr_css['color-empty'] = ($('[name="woo_star_color_empty"]').val()) ? $('[name="woo_star_color_empty"]').val() : '#cccccc';
		arr_css['color-fill']  = ($('[name="woo_star_color_fill"]').val()) ? $('[name="woo_star_color_fill"]').val() : '#e6ae48';

		
		var color_empty = 'color:'+arr_css['color-empty']+'!important;',
			color_fill  = 'color:'+arr_css['color-fill']+'!important;',
			font_size   = 'font-size:'+arr_css['font-size']+arr_css['font-unit']+'!important;',
			line_height = 'line-height:'+arr_css['font-size']+arr_css['font-unit']+'!important;';
		
		return '<div class="tg-item-rating"><div class="star-rating" title="Rated 4.5 out of 5" style="'+color_empty+font_size+line_height+'"><span style="width:90%!important;'+color_fill+'"></span></div></div>';
	
	}
	
	function cart_button() {
		
		if ($('[name="woocommerce_cart_icon"]').is(':checked')) {
			
			var icon_simple = ($('[name="woocommerce_cart_icon_simple"]').val()) ? $('[name="woocommerce_cart_icon_simple"]').val() : 'tg-icon-shop-bag';
			return '<div class="add_to_cart_button"><i class="'+icon_simple+'"></i></div>';	
		
		} else {
		
			return elements_content['get_product_cart_button'];	
			
		}
	
	}

	// ======================================================
	// Handle main tab content
	// ======================================================

	// hide tab content for style properties
	$('.tg-component-style-properties .tomb-tab').removeClass('selected');
	$('.tg-component-style-properties .tomb-tab-content').removeClass('tomb-tab-show').hide();
	
	// handle navigation in style properties
	$(document).on('click', '.tg-component-style-properties ul li', function() {
		var $this   = $(this),
			$holder = $this.closest('.tg-component-styles');
		$this.closest('.tomb-tabs-holder').hide();
		$holder.find('.tg-style-on-hover').hide();
		$holder.find('.tg-component-back').show();
		//$holder.find('.'+$this.data('target')).next('.tg-scrollbar').show();
		$holder.find('.tg-component-back span:nth-of-type(2)').html($holder.find('> ul .tomb-tab.selected').clone().children().remove().end().text());
		$holder.find('.tg-component-back span:last-child').html('<strong>'+$this.text()+'</strong>');
	});
		
	// handle back buttons in a style property
	$(document).on('click', '.tg-component-back', function() {
		var $this   = $(this),
			$holder = $this.closest('.tg-component-styles');
		$this.hide();
		//$holder.find('.tg-scrollbar').hide();
		$this.nextAll('.tomb-tab-content.tomb-tab-show').find('.tomb-tab-content').removeClass('tomb-tab-show').hide();
		$this.nextAll('.tomb-tab-content.tomb-tab-show').find('.tomb-tabs-holder').show();
		$holder.find('.tg-style-on-hover').show();
	});
	
	// handle style breadcrumb
	$(document).on('click', '.tg-component-styles > ul li', function() {
		var $this = $(this),
			$holder = $this.closest('.tg-component-styles');
		//$holder.find('.tg-scrollbar').hide();
		$holder.find('.tg-component-back').hide();
		$holder.find('.tg-style-on-hover').show();
		$holder.find('.tomb-tab-content.tomb-tab-show').find('.tomb-tab-content').removeClass('tomb-tab-show').hide();
		$holder.find('.tomb-tab-content.tomb-tab-show').find('.tomb-tabs-holder').show();
	});
	
	// skin slug from name for global custom css
	$(document).on('change input', '[name="skin_name"]', function() {
		var slug = sanitize_string($(this).val());
		handle_slug(slug)
	});

	handle_slug(sanitize_string($('[name="skin_name"]').val()))
	
	function handle_slug(slug) {
		
		var lastClass = $('.tg-skin-build-inner .tg-item').attr('class').split(' ').pop();

		if (lastClass !== 'tg-item') {
			$('.tg-skin-build-inner .tg-item').removeClass(lastClass);
		}
		
		$('.tg-skin-slug').text(slug);
		$('.tg-skin-build-inner .tg-item').addClass(slug);
		
	}

	// ======================================================
	// Handle draggable panel
	// ======================================================
	
	// Element settings draggable popup
	$('.tg-panel-element').draggable({
		handle: '.tg-container-header',
		start: function() {
			$('.tg-icons-popup').removeClass('tg-icons-popup-open');	
			$('.tg-icon-holder').removeClass('tg-icon-is-open');
		}
	});
	
	// Element settings close popup
	$('.tg-container-close').on('click', function() {
		$('.tg-panel-element').removeClass('tg-visible');
		$('.tg-element-draggable').removeClass('tg-element-selected');
	});
	 
	// ======================================================
	// Calculate scrollbar width
	// ======================================================
	
	// calculate scrollbarwidth
	function scrollbarWidth() {
	
		var body = document.body,
			box = document.createElement('div'),
			boxStyle = box.style,
			width;
	
		boxStyle.position = 'absolute';
		boxStyle.top = boxStyle.left = '-9999px';
		boxStyle.width = boxStyle.height = '100px';
		boxStyle.overflow = 'scroll';
	
		body.appendChild(box);
	
		width = box.offsetWidth - box.clientWidth;
	
		body.removeChild(box);
	
		return width;
	}
	
	var scrollbarWidth = scrollbarWidth();
	
	// ======================================================
	// On document ready init events
	// ======================================================

	$(document).ready(function(e) {
		
		check_overlay_type();
		
		// update all fields
		if (typeof tg_skin_settings !== 'undefined') {
			var $element = $('.tg-panel-item');
			update_select($element);
			update_sliders($element);
			update_image($element);
			TOMB_RequiredField.check();	
		}
		
		var panelWidth = $('.tg-panel-element').width();
		$('#element_source, .tg-panel-element .tomb-tab-content').addClass('force-show');
		$('#element_source, .tg-panel-element .tg-component-style-properties .tomb-tab-content').each(function(){
			var $this = $(this);
			$this.width(panelWidth+scrollbarWidth).css('overflow-y', 'scroll');
			if ($this.get(0).scrollHeight > $this.height()) {
				var H     = $this.height(),
					sH    = $this.get(0).scrollHeight,
					sbH   = H*H/sH;
				$('<div class="tg-scrollbar" style="height:'+(sbH-10)+'px"></div>').insertAfter($(this));
			}
		});
		$('#element_source, .tg-panel-element .tomb-tab-content').removeClass('force-show');
		
		$('#element_source, .tg-panel-element .tg-component-style-properties .tomb-tab-content').on('scroll', function(){

			var $this  = $(this),
				H      = $this.height(),
				sH     = $this.get(0).scrollHeight,
				sbH    = H*H/sH,
				offset = $this.scrollTop()/H*sbH,
				height = (sH-H > 0) ? sbH : 0;

			offset = ($(this).is('#element_source')) ? offset+50: offset;
			$(this).next('.tg-scrollbar').css('top', offset+5).height(height-10).show();

		});
		
		$('.tg-panel-element .tomb-tab-content, #element_source').on('mouseenter', function(){
			$(this).trigger('scroll');
		}).on('mouseleave', function(){
			$(this).next('.tg-scrollbar').hide();
		});

		$(document).on('input change', '.tg-panel-element .tg-component-sources [name]', function() {
			format_element();
			update_element_list();
		});
		
		$(document).on('input change', '[name="excerpt_length"], [name="excerpt_suffix"]', function() {
			$('.tg-element-draggable.'+tg_element_name).html(trim_excerpt());
		});
		
		$(document).on('input change', '[name="date_format"]', function() {
			$('.tg-element-draggable.'+tg_element_name).html(date_format());
		});
		
		var initial_width;
		/*** drop new element into item builder areas ***/
		$('.tg-element-draggable:not(.tg-element-init)').draggable({
			connectToSortable: '.tg-skin-build-inner .tg-area-droppable',
			helper: 'clone',
			zIndex: 99999,
			appendTo: 'body',
			start: function(event, ui) {
				var width  = $(ui.helper).outerWidth(),
					item_width = $('.tg-item-inner').innerWidth(),
					min_width  = (width > item_width) ? item_width : width;

				$(ui.helper).css({
					'width': width,
					'min-width': min_width,
					'max-width': item_width,
				});
				
				initial_width = $(ui.helper)[0].getBoundingClientRect().width;
				/*** get data attribute ***/
				$(ui.helper).css({'height':$(ui.helper)[0].getBoundingClientRect().height});
				$(ui.helper).data('settings', $(event.target).data('settings'));
			}
		}).disableSelection();

		/*** Sort element in item builder areas ***/
		$('.tg-area-droppable').sortable({
			connectWith: '.tg-skin-build-inner .tg-area-droppable',
			revert: true,
			zIndex: 99999,
			tolerance: 'pointer',
			items : '.tg-element-draggable:not(.tg-item-overlay)',
			placeholder: 'tg-state-highlight',
			forcePlaceholderSize: true,
			over: function(e, ui){
				check_dropped_element();
				helper_size(ui);
			},
			start: function(e, ui) {
				helper_size(ui);
			},
			stop: function(e, ui){
				initial_width = '';
				var $element = $(ui.item);
				if ($element) {
					select_element($element);
				} else {
					var el = $('.tg-item-inner .tg-element-draggable');
					if (el.length === 1) {
						select_element(el);
					}
				}
				check_dropped_element();
				pre_process_animation('element', tg_element_name);
				$element.attr('style', '');
			}
		}).disableSelection();
		
		function helper_size(ui) {
			
			var $element = $(ui.item),
				settings = $element.data('settings'),
				position = settings.styles.idle_state.position,
				display  = settings.styles.idle_state.display,
				width    = (display === 'block' && position !== 'absolute') ? $(ui.placeholder).parent().width() : $(ui.helper)[0].getBoundingClientRect().width,
				wd_unit  = settings.styles.idle_state['width-unit'],
				pd_unit  = settings.styles.idle_state['padding-unit'],
				mg_unit  = settings.styles.idle_state['margin-unit'],
				po_unit  = settings.styles.idle_state['positions-unit'];
				width    = (initial_width && display !== 'block') ? initial_width : width;

			$(ui.placeholder).css({
				'position' : position,
				'display' : display,
				'width' : width,
				'min-width' : width,
				'max-width' : (position == 'absolute') ? width : 'auto',
				'top': (position == 'absolute') ? settings.styles.idle_state.top+po_unit : 'none',
				'bottom': (position == 'absolute') ? settings.styles.idle_state.bottom+po_unit : 'none',
				'left': (position == 'absolute') ? settings.styles.idle_state.left+po_unit : 'none',
				'right': (position == 'absolute') ? settings.styles.idle_state.right+po_unit : 'none',
				'height' : $(ui.helper)[0].getBoundingClientRect().height,
				'float': settings.styles.idle_state.float,
				'paddingTop': settings.styles.idle_state['padding-top']+pd_unit,
				'paddingBottom': settings.styles.idle_state['padding-bottom']+pd_unit,
				'paddingLeft': settings.styles.idle_state['padding-left']+pd_unit,
				'paddingRight': settings.styles.idle_state['padding-right']+pd_unit,
				'marginTop': settings.styles.idle_state['margin-top']+mg_unit,
				'marginBottom': settings.styles.idle_state['margin-bottom']+mg_unit,
				'marginLeft': settings.styles.idle_state['margin-left']+mg_unit,
				'marginRight': settings.styles.idle_state['margin-right']+mg_unit
			});
			
			$element.css({
				'max-width' : width,
				'min-width' : width
			});

		}
		
		/*** select element from dropdown list element class name ***/
		$(document).on('change', '.tg-element-class', function() {
			$('.tg-panel-skin .tg-element-draggable').TG_filterByData('name',$(this).val()).trigger('click');
		});
		
		/*** Move up or down element ***/
		$(document).on('click', '.tg-element-move', function() {
			
			var $this  = $('.tg-panel-skin .tg-element-selected'),
				move   = $(this).data('move'),
				index  = $this.index(),
				length = $this.closest('.tg-area-droppable').find('.tg-element-draggable').length;

			if (move === 'down') {
				if ($this.next('.tg-element-draggable').length) {
					$this.insertAfter($this.next());
				} else {
					index = $('[data-item-area]:visible').index($this.closest('[data-item-area]'));
					var $nextArea = $('[data-item-area]:visible').eq(index+1);
					if (!$nextArea.length) {
						$nextArea = $('.tg-panel-skin [data-item-area]:visible').first();
					}
					var $nextItem = $nextArea.find('.tg-element-draggable').first();
					if (!$nextItem.length) {
						$nextArea.append($this);
					} else {
						$this.insertBefore($nextItem);
					}
				}
			} else {
				if ($this.prev('.tg-element-draggable').length) {
					$this.insertBefore($this.prev());
				} else {
					index = $('[data-item-area]:visible').index($this.closest('[data-item-area]'));
					var $prevArea = $('[data-item-area]:visible').eq(index-1);
					if (!$prevArea.length) {
						$prevArea = $('.tg-panel-skin [data-item-area]:visible').last()
					}
					var $prevItem = $prevArea.find('.tg-element-draggable').last();
					if (!$prevItem.length) {
						$prevArea.append($this);
					} else {
						$this.insertAfter($prevItem);
					}
				}
				
			}
			
			// check area if new item appended or removed
			check_dropped_element();
			update_element_list();
			
		});
	
		/*** select element on click ***/
		$(document).on('click', '.tg-element-draggable.tg-element-init', function() {
			select_element($(this));
		});

		/*** update styles on color change ***/
		$('.tomb-colorpicker').wpColorPicker({
			change: function(event, ui){
				/*** prevent trigger change on element click (preserve performance) ***/
				if (!$(this).hasClass('no-change')) {
					var color = ui.color.toString();
					$(this).closest('.tomb-row').find('.tomb-colorpicker').val(color);
					if ($(this).closest('[data-settings="source"]').length) {
						format_element();
					}
					if (!$(this).closest('[data-element]').data('element')) {
						save_element_settings();
					}
					style_change($(this));
				}
			},
			clear: function() {
				/*** prevent trigger change on element click (preserve performance) ***/	
				if (!$(this).hasClass('no-change')) {
					if ($(this).closest('[data-settings="source"]').length) {
						format_element();
					}
					if (!$(this).closest('[data-element]').data('element')) {
						save_element_settings();
					}
					style_change($(this));
				}
			},
			
		});

		/*** preview animation ***/
		$('#tg-item-preview').on('click', function() {
			$(this).toggleClass('is-previewed');
			$('#tg-3d-view').removeClass('is-previewed');
			$('.tg-skin-build-inner').removeClass('view-3d-mode')
				.toggleClass('tg-item-preview');
		});
		
		/*** 3D view ***/
		$('#tg-3d-view').on('click', function() {
			$(this).toggleClass('is-previewed');
			$('#tg-item-preview').removeClass('is-previewed');
			$('.tg-skin-build-inner').removeClass('tg-item-preview')
				.toggleClass('view-3d-mode');
		});

		/*** save on element source settings change ***/
		$(document).on('input change', '.tg-component-sources input, .tg-component-sources select:not(.tg-element-class), .tg-component-sources textarea', function() {
			save_element_settings();
		});
		
		/*** apply styles to seletect element ***/
		$(document).on('input change', '.tg-component-style-properties input, .tg-component-style-properties select, .tg-component-style-properties textarea', function() {
			style_change($(this));
			$(this).closest('.tomb-tab-content').trigger('scroll');
			if ($(this).closest('.tg-panel-element').length) {
				save_element_settings();
			}
		});
		
		/*** apply global css ***/
		$('[name="global_css"]').on('input change', function() {
			process_global_css($(this).val());
		});
		
		/*** process element animation ***/
		$('.tg-element-animation select, .tg-element-animation input').on('input change', function() {
			pre_process_animation('element',tg_element_name);
			save_element_settings();
		});
		
		/*** process media animation ***/
		$('.tg-media-animation select, .tg-media-animation input').on('input change', function() {
			pre_process_animation('media','tg-item-media-holder');
		});
		
		/*** process overlay animation ***/
		$('.tg-overlay-animation select, .tg-overlay-animation input').on('input change', function() {
			var prefix = $(this).closest('[data-prefix]').data('prefix').slice(0,-1);
			var element = $(this).closest('[data-settings]').data('settings');
			pre_process_animation(prefix, element);
		});
		
		/*** hide/show content base on current skin style (masonry/grid) ***/
		$('select[name="skin_style"], [name="media_content"]').on('change', function() {
			check_skin_style();
			check_item_size();
		});
		
		/*** Overlay position type ***/
		$('select[name="overlay_type"]').on('change', function() {
			check_overlay_type();
		});
		/*** Overlay alignment ***/
		$('select[name="overlay_alignment"]').on('change', function() {
			check_overlay_align();
		});
		
		/*** item content holder position for masonry style ***/
		$('select[name="content_position"]').on('change', function() {
			check_content_position();
		});
		
		/*** item column/row number ***/
		$('[name="skin_col"], [name="skin_row"], [name="item_x_ratio"], [name="item_y_ratio"]').on('input change', function() {
			check_item_size();
		});
		
		/*** Remove element in skin ***/
		$('#tg-element-remove').on('click', function() {
			
			var el = $('.tg-panel-skin .tg-element-draggable').TG_filterByData('name',tg_element_name),
				area = el.closest('.tg-area-droppable'),
				index = $('.tg-element-init').index(el);
			
			el.remove();
			check_dropped_element();
			
			if (!$('.tg-panel-skin .tg-element-draggable').length) {
				$('.tg-panel-element').removeClass('tg-visible');
			}
			
			$('.tg-panel-skin style[class="'+tg_element_name+'"]').remove();
			
			index = (index-1 < 0) ? 0 : index-1;
			$('.tg-panel-skin .tg-element-draggable.tg-element-init').eq(index).trigger('click');
			
		});
		
		/*** Clone element in skin ***/
		$('#tg-element-clone').on('click', function() {
			
			generate_unique_id();
			
			var $selected_element = $('.tg-element-draggable.tg-element-selected'),
				$cloned_element   = $selected_element.clone(true).removeClass(tg_element_name+' tg-element-selected'),
				element_settings  = $selected_element.data('settings');
			
			tg_element_name = 'tg-element-'+tg_element_id;
			$cloned_element.addClass(tg_element_name).data('name', tg_element_name).data('settings', element_settings);
			$cloned_element.insertAfter($selected_element);
			style_change(tg_element_name, element_settings['styles']['idle_state'], 'idle_state');
			if (element_settings['styles']['is_hover']) {
				style_change(tg_element_name, element_settings['styles']['hover_state'], 'hover_state');
			}
			pre_process_animation('element', tg_element_name, element_settings['animation']);
			select_element($cloned_element);
			
		});

		/*** assign icon ***/
		$('.tg-icons-list i').on('click', function() {
			var value  = $(this).attr('class');
			var $input = $(this).closest('.tg-icons-popup').data('input');
			value = (!$(this).hasClass('tg-icon-selected')) ? value : '';
			$input.prev('i').attr('class', value);
			$('.tg-icons-popup').removeClass('tg-icons-popup-open');
			$input.closest('.tg-icon-holder').removeClass('tg-icon-is-open');
			$input.val(value).trigger('change');
		});
		
		/*** close icons popup ***/
		$(document).on('click', function(e) {
			if (!$(e.target).is('.tg-icons-popup') && !$(e.target).is('.tg-icon-holder') && !$(e.target).is('.tg-icon-holder i')) {
				if ($('.tg-icons-popup').is(':visible')) {
					$('.tg-icons-popup').removeClass('tg-icons-popup-open');
					$('.tg-icon-holder').removeClass('tg-icon-is-open');
				}
			}
		});
		
		/*** open icons popup ***/
		$('.tg-icon-holder').on('click', function() {
			var pos   = $(this).offset();
			var h     = $('.tg-icons-popup').height();
			var w     = $('.tg-icons-popup').width();
			var value = $(this).find('input').val();
			$('.tg-icons-list').scrollTop(0);
			$('.tg-icons-popup i').removeClass('tg-icon-selected');
			$('.tg-icons-popup').css({left: pos.left, top: pos.top+58 -$(document).scrollTop()});
			$('.tg-icons-popup').data('input', $(this).find('input'));
			if ($(this).hasClass('tg-icon-is-open')) {
				$('.tg-icons-popup').removeClass('tg-icons-popup-open');
				$(this).removeClass('tg-icon-is-open');
			} else {
				$('.tg-icons-popup').addClass('tg-icons-popup-open');
				$('.tg-icon-holder').removeClass('tg-icon-is-open');
				$(this).addClass('tg-icon-is-open');
			}
			if (value) {
				$('.tg-icons-popup i.'+value).addClass('tg-icon-selected');
			}
			
		});
		
		$('.tg-loading-editor').addClass('tg-hidden');
		
	});
		
})(jQuery);

// jQuery date as php
if("undefined"==typeof date_to_string)var date_to_string=function(a,b){a=String(a),b="undefined"!=typeof b&&b instanceof Date?b:new Date;for(var c={a:function(a){return a.getHours()<12?"am":"pm"},A:function(a){return a.getHours()<12?"AM":"PM"},B:function(a){return("000"+Math.floor((60*a.getHours()*60+60*(a.getMinutes()+60+a.getTimezoneOffset())+a.getSeconds())/86.4)%1e3).slice(-3)},c:function(a){return date_to_string("Y-m-d\\TH:i:s",a)},d:function(a){return(a.getDate()<10?"0":"")+a.getDate()},D:function(a){switch(a.getDay()){case 0:return"Sun";case 1:return"Mon";case 2:return"Tue";case 3:return"Wed";case 4:return"Thu";case 5:return"Fri";case 6:return"Sat"}},e:function(a){var b=parseInt(Math.abs(a.getTimezoneOffset()/60),10),c=Math.abs(a.getTimezoneOffset()%60);return((new Date).getTimezoneOffset()<0?"+":"-")+(10>b?"0":"")+b+(10>c?"0":"")+c},F:function(a){switch(a.getMonth()){case 0:return"January";case 1:return"February";case 2:return"March";case 3:return"April";case 4:return"May";case 5:return"June";case 6:return"July";case 7:return"August";case 8:return"September";case 9:return"October";case 10:return"November";case 11:return"December"}},g:function(a){return a.getHours()>12?a.getHours()-12:a.getHours()},G:function(a){return a.getHours()},h:function(a){var b=a.getHours()>12?a.getHours()-12:a.getHours();return(10>b?"0":"")+b},H:function(a){return(a.getHours()<10?"0":"")+a.getHours()},i:function(a){return(a.getMinutes()<10?"0":"")+a.getMinutes()},I:function(a){return a.getTimezoneOffset()<Math.max(new Date(a.getFullYear(),0,1).getTimezoneOffset(),new Date(a.getFullYear(),6,1).getTimezoneOffset())?1:0},j:function(a){return a.getDate()},l:function(a){switch(a.getDay()){case 0:return"Sunday";case 1:return"Monday";case 2:return"Tuesday";case 3:return"Wednesday";case 4:return"Thursday";case 5:return"Friday";case 6:return"Saturday"}},L:function(a){return 1==new Date(a.getFullYear(),1,29).getMonth()?1:0},m:function(a){return(a.getMonth()+1<10?"0":"")+(a.getMonth()+1)},M:function(a){switch(a.getMonth()){case 0:return"Jan";case 1:return"Feb";case 2:return"Mar";case 3:return"Apr";case 4:return"May";case 5:return"Jun";case 6:return"Jul";case 7:return"Aug";case 8:return"Sep";case 9:return"Oct";case 10:return"Nov";case 11:return"Dec"}},n:function(a){return a.getMonth()+1},N:function(a){return 0==a.getDay()?7:a.getDay()},o:function(a){return a.getWeekYear()},O:function(a){var b=parseInt(Math.abs(a.getTimezoneOffset()/60),10),c=Math.abs(a.getTimezoneOffset()%60);return((new Date).getTimezoneOffset()<0?"+":"-")+(10>b?"0":"")+b+(10>c?"0":"")+c},P:function(a){var b=parseInt(Math.abs(a.getTimezoneOffset()/60),10),c=Math.abs(a.getTimezoneOffset()%60);return((new Date).getTimezoneOffset()<0?"+":"-")+(10>b?"0":"")+b+":"+(10>c?"0":"")+c},r:function(a){return date_to_string("D, d M Y H:i:s O",a)},s:function(a){return(a.getSeconds()<10?"0":"")+a.getSeconds()},S:function(a){switch(a.getDate()){case 1:case 21:case 31:return"st";case 2:case 22:return"nd";case 3:case 23:return"rd";default:return"th"}},t:function(a){return new Date(a.getFullYear(),a.getMonth()+1,0).getDate()},T:function(a){var b=String(a).match(/\(([^\)]+)\)$/)||String(a).match(/([A-Z]+) [\d]{4}$/);return b&&(b=b[1].match(/[A-Z]/g).join("")),b},u:function(a){return 1e3*a.getMilliseconds()},U:function(a){return Math.round(a.getTime()/1e3)},w:function(a){return a.getDay()},W:function(a){return a.getWeek()},y:function(a){return String(a.getFullYear()).substring(2,4)},Y:function(a){return a.getFullYear()},z:function(a){return Math.floor((a.getTime()-new Date(a.getFullYear(),0,1).getTime())/864e5)},Z:function(a){return(a.getTimezoneOffset()<0?"+":"-")+24*a.getTimezoneOffset()}},d="",e=!1,f=0;f<a.length;f++)e||"\\"!=a.substring(f,f+1)?e||"undefined"==typeof c[a.substring(f,f+1)]?(d+=String(a.substring(f,f+1)),e=!1):d+=String(c[a.substring(f,f+1)](b)):e=!0;return d};Date.prototype.getWeek=function(){var a=new Date(this.valueOf()),b=(this.getDay()+6)%7;a.setDate(a.getDate()-b+3);var c=a.valueOf();return a.setMonth(0,1),4!=a.getDay()&&a.setMonth(0,1+(4-a.getDay()+7)%7),1+Math.ceil((c-a)/6048e5)},Date.prototype.getWeekYear=function(){var a=new Date(this.valueOf());return a.setDate(a.getDate()-(this.getDay()+6)%7+3),a.getFullYear()};