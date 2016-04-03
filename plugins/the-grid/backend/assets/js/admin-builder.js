
jQuery.noConflict();

(function($) {
				
	"use strict";
	
	$(document).ready(function(e) {
		
		$('.tg-container-toggle, .tg-container-title').on('click', function() {
			var $content = $(this).closest('.tg-container-header').next('.tg-container-content');
			$('.tg-container-content').not($content).slideUp(300);
			$content.slideToggle(300);
		});
		
		/*** get animation data ***/
		var tg_anim = $('.tg-data-amin').data('item-anim');
		
		/*** elements in item selected ***/
		var tg_element_name = '';
				
		/*** unvalid css rule for element ***/
		var unvalid_rules = [
			'positions-unit',
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
			'text-shadow-unit',
			'text-shadow-color',
			'text-shadow-horizontal',
			'text-shadow-vertical',
			'text-shadow-blur',
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
			'custom-rules'
		];
		
		/*** Init Drag/drop elements in item ***/
		function init_draggable_element() {
			
			/*** drop new element into item builder areas ***/
			$('.tg-element-draggable').draggable({
				connectToSortable: '.tg-skin-build-inner .tg-area-droppable',
				helper: 'clone',
				zIndex: 3,
				opacity: '1',
				appendTo: 'body',
			}).disableSelection();
			
			/*** Sort element in item builder areas ***/
			$('.tg-area-droppable').sortable({
				connectWith: '.tg-skin-build-inner .tg-area-droppable',
				revert: true,
				zIndex: 3,
				opacity: '1',
				appendTo: 'body',
				update: function( event, ui ) {
					var el = $(ui.item);
					check_dropped_element($(this));
					if (!el.hasClass('tg-element-init')) {
						var name     = el.data('name'),
							length   = $('.tg-skin-build-inner .tg-element-draggable').TG_filterByData('initial-name',name).length,
							new_name = name+'-'+length;
						tg_element_name = new_name;
						el.data('initial-name',name)
						  .data('name',new_name)
						  .addClass('tg-element-init '+new_name)
					}
				}
			}).disableSelection();
			
		}
		init_draggable_element();
		
		/*** Check element in droppable area ***/
		function check_dropped_element(el) {
			var count = el.find('.tg-element-draggable').length;
			if (count > 0) {
				el.addClass('tg-area-filled');
			} else {
				el.removeClass('tg-area-filled');
			}
		}
		
		/*** select elements ***/
		$(document).on('mouseup', '.tg-element-draggable', function() {
			
			$('.tg-item-settings-holder .tg-container-content').slideUp(300);
			$('.tg-element-settings-holder .tg-container-content').slideDown(300);
			
			if (!$(this).hasClass('tg-element-selected')) {

				tg_element_name = $(this).data('name');
				$('.tg-element-draggable').removeClass('tg-element-selected');
				$(this).addClass('tg-element-selected');
				
				var settings = $('.tg-element-draggable.'+tg_element_name).data('settings'),
					$element = $('.tg-element-settings-holder');
	
				if (settings) {
					for (var item in settings) {
						$('[name="'+item+'"]').val(settings[item]);
					}
					update_select($element);
					update_colors($element);
					update_sliders($element);
					update_image($element);
					update_switch($element);
				}
				
			}

		});
		
		/*** change select on element click ***/
		function update_select(el) {
			el.find('.tomb-select-holder').each(function() {
				var $this = $(this),
					value = $this.find('select').val();
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
		
		/*** change colors on element click ***/
		function update_colors(el) {
			el.find('.tomb-colorpicker').each(function() {
				$(this).addClass('no-change').wpColorPicker('color', $(this).val()).removeClass('no-change');
			});
		}
		
		/*** change sliders on element click ***/
		function update_sliders(el) {
			el.find('.tomb-slider-range').each(function() {
				var $this = $(this),
					input = $this.closest('.tomb-type-slider').find('input'),
					value = $this.closest('.tomb-type-slider').find('.tomb-slider-input').val();
				$(this).add(input).addClass('no-change');
				$this.slider('value', value);
				$(this).add(input).removeClass('no-change');
			});
		}
		
		/*** change switch on element click ***/
		function update_switch(el) {
			el.find('.tomb-checkbox').each(function() {
				var $this = $(this),
					value = ($this.val() == 'true') ? true : false;
				$this.prop('checked', value);
			});
		}
		
		/*** change image field on element click ***/
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
		
		/*** update styles on color change ***/
		$('.tomb-colorpicker').wpColorPicker({
			change: function(event, ui){
				/*** prevent trigger change on element click (preserve performance) ***/
				if (!$(this).hasClass('no-change')) {
					var color = ui.color.toString();
					$(this).closest('.tomb-row').find('.tomb-colorpicker').val(color);
					if (!$(this).closest('[data-element]').data('element')) {
						save_element_settings();
					}
					style_change($(this), tg_element_name);
				}
			},
			clear: function() {
				/*** prevent trigger change on element click (preserve performance) ***/
				if (!$(this).hasClass('no-change')) {
					if (!$(this).closest('[data-element]').data('element')) {
						save_element_settings();
					}
					style_change($(this), tg_element_name);
				}
			},
		});
		
		function save_element_settings() {
			if (tg_element_name) {
				var arr_data  = {};
				$('.tg-element-settings-holder input, .tg-element-settings-holder select, .tg-element-settings-holder textarea').each(function(index, element) {
					var name  = $(this).attr('name');
					var value = (!$(this).is(':checkbox')) ? $(this).val() : $(this).prop('checked');
					arr_data[name] = value;  
				});
				$('.tg-element-draggable.'+tg_element_name).data('settings', arr_data);
			}
		}
		
		/*** preview animation ***/
		$('#tg-item-preview').on('click', function() {
			$(this).toggleClass('is-previewed');
			$('.tg-skin-build-inner').toggleClass('tg-item');
		});
		
		/*** save element settings ***/
		$(document).on('input change', '.tg-element-settings-holder input:not(.no-change, .tomb-slider), .tg-element-settings-holder select, .tg-element-settings-holder textarea', function() {
			save_element_settings();
		});
				
		/*** supdate element styles ***/
		$(document).on('click', '[name="the_grid_hover-state-activate"]', function() {
			style_change($(this), tg_element_name);
		}).on('input change', '.tg-element-styles input:not(.no-change, .tomb-slider), .tg-element-styles select, .tg-element-styles textarea', function() {
			style_change($(this), tg_element_name);
		}).on('click', '.tomb-select-clear', function() {
			save_element_settings();
			style_change($(this), tg_element_name);
		});
		
		/*** add item styles ***/
		$(document).on('input change', '.tg-item-styles input, .tg-item-styles select, .tg-item-styles textarea', function() {
			style_change($(this), 'tg-item-inner');
		});
		
		/*** add overlay styles ***/
		$(document).on('input change', '.tg-overlay-styles input, .tg-overlay-styles select, .tg-overlay-styles textarea', function() {
			style_change($(this), 'tg-item-overlay');
		});

		/*** add content styles ***/
		$(document).on('input change', '.tg-content-styles input, .tg-content-styles select, .tg-content-styles textarea', function() {
			style_change($(this), 'tg-item-content');
		});
		
		/*** save/apply item(s) css styles ***/
		function style_change(el, element) {
	
			var fields   = el.closest('[data-state]'),
				element  = (fields.data('element')) ? fields.data('element') : element,
				state    = fields.data('state'),
				pseudo   = (state == 'hover-state') ? ':hover' : '',
				selector = '[data-state="'+state+'"]',
				str_css  = '',
				arr_css  = [],
				shadows  = [];
			
			if (element) {
				
				if ((state === 'hover-state' && !$('[name="the_grid_hover-state-activate"]').is(':checked'))) {
					$('style#'+element+selector).remove();
					return false;
				}

				fields.find('.tomb-row input, .tomb-row select, .tomb-row textarea').each(function() {
					
					var $this  = $(this),
						unit   = $(this).closest('.tomb-row').find('.tg-css-unit').val(),
						hidden = $this.closest('.tomb-row').css('display'),
						value  = $this.val(),
						name   = $this.attr('name');
						name   = (name) ? name.replace('element_idle_','') : '';
						name   = (name) ? name.replace('element_hover_','') : '';
						name   = (name) ? name.replace('item_','') : '';
						name   = (name) ? name.replace('content_','') : '';
						name   = (name) ? name.replace('overlay_','') : '';
						
					if (name && value && $.inArray(name,unvalid_rules) < 0 && hidden != 'none') {
						unit = (unit) ? unit : '',
						str_css += name+':'+value+unit+';';	
					}
					
					arr_css[name] = value; 

				});
				
				str_css += (state === 'idle-state') ? process_position(arr_css) : '';
				str_css += (state === 'idle-state') ? process_position_absolute(arr_css) : '';
				str_css += process_box_shadows(arr_css);
				str_css += process_text_shadows(arr_css);
				str_css += process_background_image(arr_css);
				str_css += process_custom_rules(arr_css);
				
				if (str_css) {	
					var $styles = $('style#'+element+selector);
					if ($styles.length) {
						$styles.html('.'+element+pseudo+'{'+str_css+'}');
					} else {
						$('.tg-skin-elements-css').append('<style type="text/css" id="'+element+'" data-state="'+state+'">.'+element+pseudo+'{'+str_css+'}</style>');
					}
				}

			}
			
		}
		
		/*** process position rule for important (prevent css issue with jquery draggable) ***/
		function process_position(arr_css) {
			return (arr_css['position'] != undefined && arr_css['position']) ? 'position:'+arr_css['position']+' !important;' : '';
		}
		
		/*** process position absolute rule for important (prevent css issue with jquery draggable) ***/
		function process_position_absolute(arr_css) {
			var position = '';
			var ps_un = (arr_css['positions-unit'] != undefined) ? arr_css['positions-unit']+';' : 'px;';
			/*position += (arr_css['top'] != undefined && arr_css['top'] != '') ? 'top:'+arr_css['top']+ps_un : 'top:auto;';
			position += (arr_css['bottom'] != undefined && arr_css['bottom'] != '') ? 'bottom:'+arr_css['bottom']+ps_un : 'bottom:auto;';
			position += (arr_css['left'] != undefined && arr_css['left'] != '') ? 'left:'+arr_css['left']+ps_un : 'left:auto;';
			position += (arr_css['right'] != undefined && arr_css['right'] != '') ? 'right:'+arr_css['right']+ps_un : 'right:auto;';*/
			position += (arr_css['top'] != undefined && arr_css['top'] != '') ? 'top:'+arr_css['top']+ps_un : '';
			position += (arr_css['bottom'] != undefined && arr_css['bottom'] != '') ? 'bottom:'+arr_css['bottom']+ps_un : '';
			position += (arr_css['left'] != undefined && arr_css['left'] != '') ? 'left:'+arr_css['left']+ps_un : '';
			position += (arr_css['right'] != undefined && arr_css['right'] != '') ? 'right:'+arr_css['right']+ps_un : '';
			return position;
		}

		/*** process box-shadow rules ***/
		function process_box_shadows(arr_css) {
			
			var css_rule = '';
			
			if (Object.keys(arr_css).length > 0) {
				var sd_un  = (arr_css['box-shadow-unit'] != undefined) ? arr_css['box-shadow-unit'] : 'px',
					sd_hz  = (arr_css['box-shadow-horizontal'] != undefined && arr_css['box-shadow-horizontal']) ? arr_css['box-shadow-horizontal']+sd_un+' ' : '0 ',
					sd_vc  = (arr_css['box-shadow-vertical'] != undefined && arr_css['box-shadow-vertical']) ? arr_css['box-shadow-vertical']+sd_un+' ' : '0 ',
					sd_bl  = (arr_css['box-shadow-blur'] != undefined && arr_css['box-shadow-blur']) ? arr_css['box-shadow-blur']+sd_un+' ' : '0 ',
					sd_sz  = (arr_css['box-shadow-size'] != undefined && arr_css['box-shadow-size']) ? arr_css['box-shadow-size']+sd_un+' ' : '0 ',
					sd_co  = (arr_css['box-shadow-color'] != undefined && arr_css['box-shadow-color']) ? arr_css['box-shadow-color'] : 'rgba(0,0,0,0)';
				if (parseInt(sd_hz) || parseInt(sd_vc) || parseInt(sd_bl) || parseInt(sd_sz)) {
					arr_css = sd_hz+sd_vc+sd_bl+sd_sz+sd_co;
					css_rule += '-webkit-box-shadow:'+arr_css+';';
					css_rule += '-moz-box-shadow:'+arr_css+';';
					css_rule += 'box-shadow:'+arr_css+';';
				}
			} 
			
			return css_rule;
			
		}
		
		/*** process text-shadow rules ***/
		function process_text_shadows(arr_css) {
			
			var css_rule = '';
			
			if (Object.keys(arr_css).length > 0) {
				var sd_un  = (arr_css['text-shadow-unit'] != undefined) ? arr_css['text-shadow-unit'] : 'px',
					sd_hz  = (arr_css['text-shadow-horizontal'] != undefined && arr_css['text-shadow-horizontal']) ? arr_css['text-shadow-horizontal']+sd_un+' ' : '0 ',
					sd_vc  = (arr_css['text-shadow-vertical'] != undefined && arr_css['text-shadow-vertical']) ? arr_css['text-shadow-vertical']+sd_un+' ' : '0 ',
					sd_bl  = (arr_css['text-shadow-blur'] != undefined && arr_css['text-shadow-blur']) ? arr_css['text-shadow-blur']+sd_un+' ' : '0 ',
					sd_co  = (arr_css['text-shadow-color'] != undefined && arr_css['text-shadow-color']) ? arr_css['text-shadow-color'] : 'rgba(0,0,0,0)';
				if (parseInt(sd_hz) || parseInt(sd_vc) || parseInt(sd_bl)) {
					arr_css  = sd_hz+sd_vc+sd_bl+sd_co;
					css_rule = 'text-shadow:'+arr_css+';';
				}
			}
			
			return css_rule;
			
		}
		
		/*** process background-image rule ***/
		function process_background_image(arr_css) {
			return (arr_css['background-image'] != undefined && arr_css['background-image']) ? 'background-image:url('+arr_css['background-image']+');' : '';
		}
		
		/*** process custom rules ***/
		function process_custom_rules(arr_css) {
			return (arr_css['custom-rules'] != undefined && arr_css['custom-rules']) ? arr_css['custom-rules'] : '';
		}
		
		/*** process element animation ***/
		$('.tg-element-animations select, .tg-element-animations input').on('input change', function() {
			pre_process_animation('element',tg_element_name);
		});
		
		/*** process media animation ***/
		$('.tg-media-animation select, .tg-media-animation input').on('input change', function() {
			pre_process_animation('media','tg-item-media-holder');
		});
		
		/*** process overlay animation ***/
		$('.tg-overlay-animation select, .tg-overlay-animation input').on('input change', function() {
			pre_process_animation('overlay','tg-item-overlay');
		});
		
		function pre_process_animation(prefix,element) {
			var value      = $('.'+prefix+'_animation select').val(),
				state      = $('.'+prefix+'_animation_state select').val(),
				easing     = $('.'+prefix+'_transition_function select').val(),
				bezier     = $('.'+prefix+'_transition_bezier input').val(),
				transition = $('.'+prefix+'_transition_duration input').val(),
				delay      = $('.'+prefix+'_transition_delay input').val();
			process_animation(element,value,state,easing,bezier,transition,delay);
		}
		
		/*** add animation on element ***/
		function process_animation(element,value,state,easing,bezier,transition,delay) {
			
			if (value) {
				
				var visible    = tg_anim[value][1].visible,
					hidden     = tg_anim[value][2].hidden,
					easing     = (easing == 'cubic-bezier') ? bezier : easing;
				
				$('style#'+element+'[data-state="animate"]').remove();

				if (value && value !== 'none') {
					
					var hover_animation = (state == 'show') ? visible : hidden,
						hover_opacity   = (state == 'show') ? 1 : 0.001,
						idle_animation  = (state == 'show') ? hidden  : visible,
						idle_opacity    = (state == 'show') ? 0.001  : 1;
						
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
					str_css_idle += 'opacity:'+idle_opacity+' !important;';
					
					if (value !== 'fade_in') {
						str_css_over += '-webkit-transform:'+hover_animation+';';
						str_css_over += '-moz-transform:'+hover_animation+';';
						str_css_over += '-ms-transform:'+hover_animation+';';
						str_css_over += 'transform:'+hover_animation+';';
					}
					str_css_over += 'opacity:'+hover_opacity+' !important;';
					
					$('.tg-skin-elements-css').append('<style type="text/css" id="'+element+'" data-state="animate">.tg-item .'+element+'{'+str_css_idle+'}</style>');
					$('.tg-skin-elements-css').append('<style type="text/css" id="'+element+'" data-state="animate">.tg-item:hover .'+element+'{'+str_css_over+'}</style>');
					
				}
				
			}
		}
		
		function check_skin_style() {
			var style = $('.item_style.tomb-type-radio input:checked').val();
			if (style === 'grid') {
				$('.tg-item-content').hide();
			} else {
				check_content_position();
			}
		}
		check_skin_style();
		
		function check_content_position() {
			$('.tg-item-content').hide();
			var position = $('select[name="item_content"]').val();
			switch (position) {
				case 'both':
        			$('.tg-item-content[data-position="top"]').show();
					$('.tg-item-content[data-position="bottom"]').show();
        			break;
    			case 'top':
        			$('.tg-item-content[data-position="top"]').show();
        			break;
				case 'bottom':
					$('.tg-item-content[data-position="bottom"]').show();
					break;
			}
		}
		check_content_position();
		
		/*** hide/show content base on current skin style (masonry/grid) ***/
		$('input[name="item_style"]').on('change', function() {
			check_skin_style();
		});
		
		/*** item content holder position for masonry style ***/
		$('select[name="item_content"]').on('change', function() {
			check_content_position();
		});
		
		/*** item content holder position for masonry style ***/
		$('#tg-element-remove').on('click', function() {
			var el = $('.tg-skin-builder-holder .tg-element-draggable').TG_filterByData('name',tg_element_name),
				area = el.closest('.tg-area-droppable');
			el.remove();
			check_dropped_element(area);
			$('.tg-skin-builder-holder style#'+tg_element_name).remove();
			$('.tg-skin-builder-holder .tg-element-draggable').first().trigger('mouseup');
		});
	
	});
	
	
	// Save current skin
	$(document).on('click','#tg_skin_save', function() {
		
		var $this   = $(this),
			$styles = $('.tg-skin-elements-css style');
			
		var css = '';
		$styles.each(function() {
            css += $(this).html();
        });
		css = TG_unminify(css);
		
		var json = {};
		$('.tg-skin-build-inner .tg-element-draggable ').each(function() {
			var $this = $(this);
			json[$this.data('name')] = $this.data('settings');
        });

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				nonce  : tg_admin_global_var.nonce,
				action : 'backend_grid_ajax',
				func   : $this.data('action'),
				name   : 'my-skinxx',
				style  : $('.item_style.tomb-field input:checked').val(),
				css    : css,
				json   : JSON.stringify(json)
			},
			beforeSend: function(){

			},
			success: function(data){
				console.log(data);
			},
			error: function(data) {
				console.log(data);
			}
		});
	});	
	
	function TG_unminify(code) {
		
		code = code
			.split('\t').join('    ')
			.replace(/\s*{\s*/g, ' {\n    ')
			.replace(/;\s*/g, ';\n    ')
			.replace(/,\s*/g, ', ')
			.replace(/[ ]*}\s*/g, '}\n')
			.replace(/\}\s*(.+)/g, '}\n$1')
			.replace(/\n    ([^:]+):\s*/g, '\n    $1: ')
			.replace(/([A-z0-9\)])}/g, '$1;\n}');
	
		code  = code.replace(/\n    /g, '\n\t');
	
		return code;
		
	}
		
})(jQuery);

(function ($) {


	/*** Helper to get element with new data ***/
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
	
})(window.jQuery);