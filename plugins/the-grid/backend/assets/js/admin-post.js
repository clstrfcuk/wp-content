/*global jQuery:false*/

jQuery.noConflict();

(function($) {
				
	"use strict";

	$(document).ready(function() {

	// ======================================================
	// Retrieve all taxonomy per category and atuo sort filters
	// ======================================================

		var $category = $('.the_grid_categories option'),
			$filter_sort1 = $('#tg-filter-sort1'),
			post_type_arr = [],
			filter_arr = [],
			information,
			post_type,
			taxonomy,
			disabled,
			value,
			parent,
			id,
			i;

		if ($category.length) {
			for (i = 0; i < $category.length; i++) { 
				disabled    = '';
				value       = $category[i].text;
				information = $category[i].value;
				information = information.split(',');
				post_type   = information[0].split(':');
				post_type   = post_type[1];
				taxonomy    = information[1].split(':');
				taxonomy    = taxonomy[1];
				id          = information[2].split(':');
				id          = id[1];
				parent      = (information[3]) ? information[3].split(':') : '';
				parent      = (parent[1]) ? parent[1] : '';
				if (id.indexOf('option_disabled') > -1){
					disabled = 'disabled="disabled"';
				}
				if (post_type) {
					if (!post_type_arr.hasOwnProperty(post_type)) {
						post_type_arr[post_type] = [];
						filter_arr[post_type] = [];
					}
					post_type_arr[post_type].push('<option value="'+taxonomy+':'+id+'" '+disabled+'>'+value+'</option>');
					if (!disabled) {
						var name   = $.trim(value.replace(/\(.*?\)/, ''));
						var regExp = /\(([^)]+)\)/;
						var number = regExp.exec(value);
						number = number[1];
						filter_arr[post_type].push('<li class="tg-state-default" data-parent="'+parent+'" value="'+id+'" data-taxonomy="'+taxonomy+'" data-name="'+name+'" data-number="'+number+'"><span class="tg-filte-icon dashicons"></span>'+value+'</li>');
					}
				}
			}
		}
		
		$('[name="the_grid_filters_order_1"]').addClass('tg_sort_filter_list');
		$('.tg-add-filters').on('click',function() {
			appendFilters();
		});
		// rebuilt filter holder
		$('.tg-filter-holder-number > div').each(function() {
			appendFilters();
			$(this).find('input').each(function() {
				var input = $(this).data('input');
				var value = $(this).val();
				if ($('[name="'+input+'"]').attr('type') === 'checkbox' && value) {
					$('[name="'+input+'"]').prop('checked', !$('[name="'+input+'"]').prop('checked'));
				}
				$('[name="'+input+'"]').val(value).trigger('change');
			});
			$(this).find('ul').trigger('change');
		});

		if (post_type_arr[post_type]) {
			
			var $eventSelect = $('.the_grid_post_type select'),
				meta_value   = ($('#tg-cat-val').data('meta')) ? $('#tg-cat-val').data('meta').split(',') : '',
				meta_value2  = ($('#tg-filter-load').data('meta')) ? $('#tg-filter-load').data('meta').split(',') : '',
				selected     = $('.the_grid_post_type select').val();
				selected     = (selected) ? selected : [$('.the_grid_post_type select option:first').val()];
			
			$filter_sort1.find('li').remove();
			$('.the_grid_categories select').find('option').remove();
			$('.the_grid_filter_onload select').find('option').remove();
			
			$eventSelect.on('change', function () {
				$filter_sort1.find('li').remove();
				$('.tg-filter-sort2').find('li').remove();
				$('.the_grid_categories select').find('option').remove();
				$('.the_grid_filter_onload select').find('option').remove();
				var selected = $(this).val();
				if (selected) {
					autoFillOptions(selected);
					autoFillFilters();
					checkFilterList();
					$('.the_grid_filters_holder').each(function(i) {
						sortFilterList($(this),$(this).find('.tg_sort_filter_list').val());
					});
					updateFilterList();
				}
				$('.the_grid_categories select').trigger('change');
				$('.the_grid_filter_onload select').trigger('change');
			});
			
			$('.the_grid_categories select').on('change', function () {
				var selected = $(this).val();
				$('.the_grid_filters_holder').each(function() {
					$(this).find('ul li').remove();
				});
				autoFillFilters(selected);
				checkFilterList();
				updateFilterList();
			});	
			
			$('.the_grid_post_type select').val(selected).trigger('change');
			$('.the_grid_categories select').val(meta_value).trigger('change');	
			$('.the_grid_filter_onload select').val(meta_value2).trigger('change');
			
		}
		
		// ======================================================
		// Sort Filter Functionnality
		// ======================================================
		
		$(document).on('click', '.tg-delete-filters', function() {
			var msg = $('#tg-available-filters-holder').data('delete-msg');
			var result = confirm(msg);
			if (!result) {
				return false;
			}
			var $holder = $(this).closest('.the_grid_filters_holder');
			var li = $holder.find('ul li').clone();
			var ID = $holder.find('.tg-filter-name-area').html();
			var nb = $('.tg-layout-filter').length;
			$filter_sort1.append(li);
			$holder.add($('[data-func="the_grid_get_filters_'+ID+'"]')).remove();
			if (ID < nb) {
				for (var a = ID; a <= nb; a++) {
					var new_ID = a-1;
					$('[data-func="the_grid_get_filters_'+a+'"]').find('.tg-filter-func-nb').html(new_ID);
					$('[data-func="the_grid_get_filters_'+a+'"]').data('func','the_grid_get_filters_'+new_ID);
					$('[data-func="the_grid_get_filters_'+a+'"]').attr('data-func','the_grid_get_filters_'+new_ID);
				}
			}
			$('.the_grid_filters_holder').each(function(i) {
				checkFiltersNb(i+1);
			});
			update_area_items();
		});

		function appendFilters() {
			var $this = $('.the_grid_filters_holder');
			var filters_number = $this.length;
			var last_holder    = $(document).find('.the_grid_filters_holder').eq(filters_number-1);
			var filters_holder = $(document).find('.the_grid_filters_holder').eq(0).clone();
			filters_holder.find('h3').append($('<div class="tg-delete-filters tg-button"><i class="dashicons dashicons-no-alt"></i></div>'));
			filters_holder.find('ul li').remove();
			filters_holder.find('input').val('');
			var paste = filters_holder.insertAfter(last_holder);
			checkFiltersNb(filters_number+1);
			paste.find('select').each(function() {
				var $this = $(this);
				$this.val('');
				if (!$this.data('clear')) {
					$this.find('option:first-child').prop('selected', true);
				}
				$this.trigger('change');
            });	
			addFiltersLayout(filters_number+1);
			sortFilters();
		}
		
		function addFiltersLayout(ID) {
			var $tg_filters = $('#tg-layout-wrapper').find('[data-func="the_grid_get_filters_'+ID+'"]');
			if ($tg_filters.length === 0) {
				$('#tg-layout-blocs-holder').append(
					$('<li class="tg-layout-bloc tg-layout-filter dashicons-admin-settings" data-func="the_grid_get_filters_'+ID+'">'+
					'Filter - <span class="tg-filter-func-nb">'+ID+'</span>'+
					'</li>')
				);
			} else {
				$tg_filters.find('.tg-filter-func-nb').html(ID);
				$tg_filters.data('func','the_grid_get_filters_'+ID);
				$tg_filters.attr('data-func','the_grid_get_filters_'+ID);
			}
		}	
		
		function checkFiltersNb(ID) {
			var $this = $('.the_grid_filters_holder').eq(ID-1);
			$this.find('.tg-filter-name-area').html(ID);
			$this.find('input, select').each(function() {
				var name = $(this).attr('name');
				if (name) {
					$(this).closest('.tomb-row').removeClass(name);
					if ($.isNumeric(name.substr(name.length-1))) {
						name = name.slice(0,-2);
					}
					name = name+'_'+ID;
					$(this).attr('name',name);
					$(this).closest('.tomb-row').addClass(name);
					var requiered = $('.'+name).data('required');
					if(requiered) {
						requiered = requiered.split(',');
						var field = requiered[0];
						field = $.isNumeric(field.substr(field.length-1)) ? field.slice(0,-2) : field;
						field = field+'_'+ID;
						$('.'+name).data('required',field+','+requiered[1]+','+requiered[2]);
						$('.'+name).attr('data-required',field+','+requiered[1]+','+requiered[2]);
					}
				}
			});
			$('[name="the_grid_filters_number"]').val($('.the_grid_filters_holder').length);
			sortFilters();
		}
		
		function sortFilters() {
			$filter_sort1.sortable({
				connectWith: '.connectedSortable',
			}).disableSelection();
			
			$('.tg-filter-sort2').sortable({
				connectWith: '.connectedSortable',
				update: function() {
					updateFilterList();
					$(this).closest('.the_grid_filters_holder').find('.tg_sort_filter_list').val('').trigger('change');
				}
			}).disableSelection();
		}
		sortFilters();
		
		
		$(document).on('click', '.tg-filter-button-add', function() {
			var $this = $(this);
			$('#tg-filter-sort1 li').each(function() {
				var current_filter = $(this);
				var active_filter  = current_filter.clone();
				current_filter.remove();
				$this.prevAll('.tg-filter-sort2 ').append(active_filter);
				updateFilterList();
			});
		});
		$(document).on('click', '.tg-filter-button-remove', function() {
			var $this = $(this).prevAll('.tg-filter-sort2 ').find('li');
			$this.each(function() {
				var current_filter = $(this);
				var active_filter  = current_filter.clone();
				current_filter.remove();
				$filter_sort1.append(active_filter);
				updateFilterList();
			});
		});
		
		$(document).on('change','.tg_sort_filter_list',function() {
			var sort_val = $(this).val();
			var $this    = $(this).closest('.the_grid_filters_holder');
			sortFilterList($this,sort_val);
			updateFilterList();
		});
		
		function autoFillOptions(selected) {
			$.each(selected, function(a, $s) {
				if (post_type_arr[$s]) {
					$.each(post_type_arr[$s], function(b, $v) {
						$('.the_grid_categories select').append($v);
						$('.the_grid_filter_onload select').append($v);
					});
				}
			});	
		}
		
		function sortFilterList($this,sort_val) {
			switch(sort_val) {
				case 'number_asc':
					$this.find('ul').append($this.find('ul li').sort(sort_nb_asc));
					break;
				case 'number_desc':
					$this.find('ul').append($this.find('ul li').sort(sort_nb_desc));
					break;
				case 'alphabetical_asc':
					$this.find('ul').append($this.find('ul li').sort(sort_alpha_asc));
					break;
				case 'alphabetical_desc':
					$this.find('ul').append($this.find('ul li').sort(sort_alpha_desc));
					break;
			}
		}
		
		function checkFilterList() {
			$('.the_grid_filters_holder').each(function(index, element) {
             	var active_filters = $.parseJSON($(this).find('ul').closest('.tomb-row').find('input').val());
				if (active_filters) {
					for (i = 0; i < active_filters.length; i++) {
						var current_filter = $('#tg-filter-sort1 li[value="'+active_filters[i].id+'"]');
						var active_filter  = current_filter.clone();
						current_filter.remove();
						$(this).find('ul').append(active_filter);
					}
				}   
            });	
		}
		
		function autoFillFilters() {
			$filter_sort1.find('li').remove();
			var selected    = $('.the_grid_post_type select').val();
			var category    = $('.the_grid_categories select').val();
			var allow_child = $('input[type="checkbox"]#the_grid_categories_child').is(':checked');
			if (selected) {
				$.each(selected, function(a, $s) {
					if (filter_arr[$s]) {
						if (category && category.length > 0) {
							$.each(filter_arr[$s], function(b, $v) {
								var data  = $('<div/>').html($v).contents().data('taxonomy')+':'+$('<div/>').html($v).contents().val(),
									child = (allow_child) ? $('<div/>').html($v).contents().data('taxonomy')+':'+$('<div/>').html($v).contents().data('parent') : '';
								if ($.inArray(data, category) > -1 || $.inArray(child, category) > -1) {
									$filter_sort1.append($v);
								}
							});
						} else {
							$.each(filter_arr[$s], function(b, $v) {
								$filter_sort1.append($v);
							});
						}
					}
				});
			}
		}
		
		$('input[type="checkbox"]#the_grid_categories_child').on('change', function() {
			$('.the_grid_filters_holder').each(function() {
				$(this).find('ul li').remove();
			});
			autoFillFilters();
			checkFilterList();
			updateFilterList();
		});
		
		function updateFilterList() {
			$('.the_grid_filters_holder').each(function(i) {
				var $this  = $(this).find('.tg-filter-sort2');
				var $input = $this.closest('.tomb-row').find('input');
				var filter_active = [];
				$this.find('li').each(function(i) {
					filter_active[i] = {};
					filter_active[i].id = $(this).val();
					filter_active[i].taxonomy = $(this).data('taxonomy');
				});
				$input.val(JSON.stringify(filter_active)); 
			});  
		}
		
		function sort_nb_asc(a, b){
			return ($(b).data('number')) < ($(a).data('number')) ? 1 : -1;    
		}
		function sort_nb_desc(a, b){
			return ($(b).data('number')) < ($(a).data('number')) ? -1 : 1;    
		}
		function sort_alpha_asc(a, b){
			return ($(b).data('name').toString().toLowerCase()) < ($(a).data('name').toString().toLowerCase()) ? 1 : -1;    
		}
		function sort_alpha_desc(a, b){
			return ($(b).data('name').toString().toLowerCase()) < ($(a).data('name').toString().toLowerCase()) ? -1 : 1;    
		}

		// ======================================================
		// Meta query fields Functionnality
		// ======================================================

		var metakey_name      = 'the_grid_metakey';
		var metakey_compare   = 'the_grid_metakey_compare';
		var metakey_value     = 'the_grid_metakey_value';
		var metakey_type      = 'the_grid_metakey_type';
		var metakey_relation  = 'the_grid_metakey_relation';
		var metakey_wrapper   = '.the_grid_metakey-wrapper';
		var $metakey_name     = $('.'+metakey_name+'.tomb-row');
		var $metakey_compare  = $('.'+metakey_compare+'.tomb-row');
		var $metakey_value    = $('.'+metakey_value+'.tomb-row');
		var $metakey_type     = $('.'+metakey_type+'.tomb-row');
		var $metakey_relation = $('.'+metakey_relation+'.tomb-row');
		var compare_array     = ['IN','NOT IN','BETWEEN','NOT BETWEEN'];
		
		$metakey_relation.addClass('first the_grid_metakey-wrapper');
		$metakey_relation = $('.the_grid_metakey_relation');
		
		$metakey_name.add($metakey_compare).add($metakey_value).add($metakey_type)
		.andSelf().wrapAll('<div class="the_grid_metakey-wrapper first"/>');
		
		$metakey_name.find('input').attr('class',$metakey_name.find('input').attr('name')).removeAttr('name');
		$metakey_compare.find('select').attr('class',$metakey_compare.find('select').attr('name')+' tomb-select').removeAttr('name').hide();
		$metakey_value.find('input').attr('class',$metakey_value.find('input').attr('name')).removeAttr('name');
		$metakey_type.find('select').attr('class',$metakey_type.find('select').attr('name')+' tomb-select').removeAttr('name').hide();
		$metakey_relation.find('select').attr('class',$metakey_relation.find('select').attr('name')+' tomb-select').removeAttr('name').hide();
		$metakey_name     = $('.'+metakey_name);
		$metakey_compare  = $('.'+metakey_compare);
		$metakey_value    = $('.'+metakey_value);
		$metakey_type     = $('.'+metakey_type);
		$metakey_relation = $('.'+metakey_relation);

		var margin     = false;
		var meta_query = $('input[name="the_grid_meta_query"]').data('metakey');
		if (meta_query && meta_query[1]) {
			$metakey_relation.val(meta_query[0].relation);
			$metakey_name.val(meta_query[1].key);
			$metakey_compare.val(meta_query[1].compare).trigger('change');
			$metakey_value.val(meta_query[1].value);
			$metakey_type.val(meta_query[1].type).trigger('change');
			$(metakey_wrapper).last().css('margin-left',70);
			for (i = 2; i < meta_query.length; i++) {
				if (meta_query[i].relation) {
					clone_meta('.the_grid_metakey_relation','.the_grid_metakey-wrapper','');
					$(metakey_wrapper).last().find('.'+metakey_relation).val(meta_query[i].relation).trigger('change');
				} else {
					clone_meta('.the_grid_metakey-wrapper','.the_grid_metakey-wrapper','.the_grid_metakey_relation');
					$(metakey_wrapper).last().find('.'+metakey_name).val(meta_query[i].key);
					$(metakey_wrapper).last().find('.'+metakey_compare).val(meta_query[i].compare).trigger('change');
					$(metakey_wrapper).last().find('.'+metakey_value).val(meta_query[i].value);
					$(metakey_wrapper).last().find('.'+metakey_type).val(meta_query[i].type).trigger('change');
				}
				checkMetaFieldMargin($(metakey_wrapper).last(),i);
			}
		} else {
			$(metakey_wrapper).last().css('margin-left',70);
		}

		$(document).on('change keyup','.the_grid_metakey-wrapper input, .the_grid_metakey-wrapper select', function() {
			autoFillMeta();
		});
		
		$('#tg-add-relation').on('click', function() {
			clone_meta('.the_grid_metakey_relation','.the_grid_metakey-wrapper','');
			autoFillMeta();
		});
		
		$('#tg-add-metakey').on('click', function() {
			clone_meta('.the_grid_metakey-wrapper','.the_grid_metakey-wrapper','.the_grid_metakey_relation');
			autoFillMeta();
		});
		
		$(document).on('click','.tg-remove-metakey', function() {
			$(this).closest(metakey_wrapper).remove();
			autoFillMeta();
		});
		
		$('#section_metakey_start .tomb-section-content').sortable({
			items: '.the_grid_metakey-wrapper:not(.first)',
			placeholder: 'the_grid_metakey-wrapper-highlight',
			stop: function() {
				autoFillMeta();
			},
		});
		
		function clone_meta(wrapper1,wrapper2,not) {
			var $cloned = $(wrapper1+'.first').not(not).clone().addClass('cloned').removeClass('first').insertBefore($('.the_grid_meta_query'));
			$cloned.append($('<div class="tg-button tg-remove-metakey"><i class="dashicons dashicons-no-alt"></i></div>'));
			$(wrapper1+'.cloned').find('input').val('');
			$(wrapper1+'.cloned select').each(function() {
				var el = $(this);
				if (el.data('clear')) {
					el.val('').trigger('change');
				} else {
					el.val('').find('option:first-child').prop('selected', true).trigger('change');
				}
            });
			$(wrapper1+'.cloned').removeClass('cloned');
		}
		
		// construct meta query array to save for php (for preserving performance on front end)
		function autoFillMeta() {
			var i = 0,
				meta_data = [],
				meta_query = [];
			margin = false;
			$('.the_grid_metakey-wrapper').each(function(k) {
				var $this = $(this);
				if ($this.is('.'+metakey_relation)) {
					meta_query[i] = {};
					meta_query[i].relation = $this.find('select.'+metakey_relation).val();
					i++;
				} else {
					var metakey_name_value    = $this.find('.tomb-row .'+metakey_name).val();
					var metakey_value_value   = $this.find('.tomb-row .'+metakey_value).val();
					var metakey_compare_value = $this.find('.tomb-row .'+metakey_compare).val();
					if (metakey_name_value && metakey_value_value) {
						meta_query[i] = {};
						meta_query[i].key = metakey_name_value;
						if ($.inArray(metakey_compare_value, compare_array) > -1) {
							meta_query[i].value = metakey_value_value.split(',');	
						} else {
							meta_query[i].value = metakey_value_value;
						}
						meta_query[i].compare  = metakey_compare_value;
						meta_query[i].type     = $this.find('.tomb-row .'+metakey_type).val();
						i++;
					}
				}
				checkMetaFieldMargin($this,k);
			});
			
			if (meta_query.length > 1) {
				meta_data.push(meta_query);
				$('input[name="the_grid_meta_query"]').attr('value',JSON.stringify(meta_data[0]));
				$('input[name="the_grid_meta_query"]').data('metakey',meta_data[0]);
			} else {
				$('input[name="the_grid_meta_query"]').attr('value','');
				$('input[name="the_grid_meta_query"]').data('metakey','');
			}	
		}
		
		// ======================================================
		// Meta query preview
		// ======================================================
		
		function checkMetaFieldMargin($this,i) {
			if ($this.is('.'+metakey_relation) && i>1) {
				margin = true;
				$this.css('margin-left',70);
			} else if (margin === true && !$this.is('.'+metakey_relation)) {
				$this.css('margin-left',140);
			} else if (i>1) {
				$this.css('margin-left',70);
			}
		}
		
		// ======================================================
		// Item Animations preview
		// ======================================================
		
		var tg_anim = $('.tg-data-amin').data('item-anim');
		$('.tg-data-amin').removeAttr('data-item-anim');
		
		$('select[name="the_grid_animation"], input[name="the_grid_transition"]').on('change', function() {
			animation_preview();
		});
		
		$('#tg-animation-preview-button').on('click', function() {
			animation_preview();
		});
		
		function animation_preview() {
			var value = $('select[name="the_grid_animation"]').val();
			if (value) {
				var visible    = tg_anim[value].visible,
					hidden     = tg_anim[value].hidden,
					transition = $('input[name="the_grid_transition"]').val();
				if (value !== 'none') {
					$('#tg-animation-preview').attr('data-animation',value);
					$('#tg-animation-preview').css({'opacity':0,'transition': 'none','transform':hidden});
					setTimeout(function() {
						$('#tg-animation-preview').css('transform',hidden).css({'transition': 'all '+transition+'ms ease','transform':visible,'opacity':1});
					}, 150);
				}
			}
		}

		// ======================================================
		// Auto input value and length (Shortcode name)
		// ======================================================

		if ($('input#the_grid_name').length) {
			var wrong = $('#tg-shortcode-wrong-name').text();
			$('input#the_grid_name').autoGrowInput({minWidth:220,comfortZone:10});
			$('input#the_grid_shortcode').css({'width':340,'min-width':340});
			$('input#the_grid_name').on('keypress keydown keyup', function() {
				var name = $(this).val();
				if (!name) {
					name = wrong;	
				} else {
					name = '[the_grid name="'+name+'"]';
				}
				$('input#the_grid_shortcode').val(name);
				$('input#the_grid_shortcode').width($('input#the_grid_name').width()+120);
			});
		}
		
		// ======================================================
		// The grid layout
		// ======================================================	

		var current_input;
				
		$('.tg-layout-connected').sortable({
			connectWith: '.tg-layout-connected',
			revert: 'valid',
			zIndex: 3,
			opacity: '0.9',
			appendTo: 'body',
			receive: function(ev, ui) {
				if( ( ui.item.hasClass('tg-layout-pagination') && $('#tg-layout-blocs-entries .tg-layout-load-more').length ) ||
				    ( ui.item.hasClass('tg-layout-load-more') && $('#tg-layout-blocs-entries .tg-layout-pagination').length ) ) {
					ui.sender.sortable('cancel');
				}
			},
			update: function(event, ui) {
				if (this === ui.item.parent()[0]) {
					update_area_items();
				}
			}
		}).disableSelection();
		
		$('.tg-layout-connected.tg-exclude').sortable({
			connectWith: '.tg-layout-connected',
			receive: function(ev, ui) {
				if(!ui.item.hasClass('tg-bloc-center') || !ui.item.hasClass('tg-bloc-center')) {
					ui.sender.sortable('cancel');
				}
			},
		}).disableSelection();

		
		function update_area_items() {
			$('.tg-layout-area').each(function() {
				var funcs  = [];
				var $this  = $(this);
				var func   = $this.find('li');
				var input  = $this.find('input');
				var array  = input.data('value');
				func.each(function() {
					if ($(this).css('display') === 'inline-block'){
						funcs.push($(this).data('func'));
					}
				});
				array.functions = funcs;
				input.val(JSON.stringify(array)).data('value',array);
				hide_area_title($this);
			});
		}
		
		function hide_area_title($this) {
			if ($this.find('li').css('display') === 'inline-block') {
				$this.find('span').fadeOut(300);
			} else {
				$this.find('span').fadeIn(300);
			}
		}
		
		$('.tg-layout-area input').each(function() {
			var $this = $(this);
			var blocs = $this.closest('.tg-layout-area');
			var data  = $this.data('value');
			if (data !== '') {
				var func  = data.functions;
				$.each(func, function(i, name){
					var $that  = $('#tg-layout-blocs-holder').find('[data-func="'+name+'"]');
					var cloned = $that.clone();
					$that.remove();
					blocs.find('ul').append(cloned);
				});
				var txt_align = data.styles['text-align'];
				if (txt_align) {
					blocs.find('.tg-layout-connected').css('text-align',txt_align);
					blocs.find('[data-val="'+txt_align+'"]').addClass('active');
				} else {
					blocs.find('[data-val="left"]').addClass('active');
				}
			} else {
				var array = {};
				array.styles    = {};
				array.functions = [];
				$this.val(JSON.stringify(array)).data('value',array);
				blocs.find('[data-val="left"]').addClass('active');
			}
			hide_area_title(blocs);
		});

		$('.tg-layout-setting-buttons div').on('click', function() {
			if (!$(this).is('.tg-layout-area-settings')) {
				var align = $(this).data('val');
				$(this).closest('.tg-layout-area').find('.tg-layout-setting-buttons div').removeClass('active');
				$(this).addClass('active');
				$(this).closest('.tg-layout-area').find('ul').css('text-align',align);
				var array = $(this).closest('.tg-layout-area').find('input').data('value');
				array.styles['text-align'] = align;
				var input = $(this).closest('.tg-layout-area').find('input');
				input.val(JSON.stringify(array)).data('value',array);
			}
		});
		
		$('#tg-layout-styles-box #tg-button-save-styles').on('click', function() {
			var input = current_input;
			var array = input.data('value');
			$('#tg-layout-styles-box input[data-name]').each(function() {		
				array.styles[$(this).data('name')] = $(this).val();	
			});
			input.val(JSON.stringify(array)).data('value',array);
		});

		
		$('#tg-layout-styles-box').draggable();
		$('body, #tg-layout-styles-box .dashicons-no-alt, #tg-button-save-styles').on('click', function(){
			$('#tg-layout-styles-box').fadeOut(300);
		});
		
		$('.tg-layout-area-settings, #tg-layout-styles-box').on('click', function(e){
			if ($(e.target).is('.tg-layout-area-settings')) {
				current_input = $(this).closest('.tg-layout-area').find('input');
				var array = current_input.data('value');
				$('#tg-layout-styles-box input[data-name]').each(function() {
					var value = array.styles[$(this).data('name')];
					if ($(this).data('name') == 'background') {	
						if (value) {
							$('#tg-layout-styles-box .wp-color-result').css('background-color',value+'!important');
						} else {
							$('#tg-layout-styles-box .wp-color-result').css('background-color','');
						}
					} else {
						value = (value) ? value : '0';
					}
					$(this).val(value);	
				});
			}
			if (!$(e.target).is('#tg-layout-styles-box .dashicons-no-alt, #tg-button-save-styles')) {
				e.stopPropagation();
				$('#tg-layout-styles-box').fadeIn(300);
			}
		});
		
		$('.the_grid_layout input').on('change', function() {
			hide_layout_area();
			update_area_items();
		});
		
		$('.the_grid_source_type input').on('change', function() {
			hide_layout_area();
			update_area_items();
		});
		
		function hide_layout_area() {
			var val = $('.the_grid_layout').find('input:checked').val();
			if (val === 'vertical') {
				$('.tg-layout-arrow-left, .tg-layout-arrow-right, .tg-layout-slider-bullets').addClass('hidden-block');
				$('#tg-layout-center-area .tg-layout-area, .tg-layout-arrow-left, .tg-layout-arrow-right, .tg-layout-slider-bullets').attr('style','display:none !important');
			} else {
				$('.tg-layout-arrow-left, .tg-layout-arrow-right, .tg-layout-slider-bullets').removeClass('hidden-block');
				$('#tg-layout-center-area .tg-layout-area, .tg-layout-arrow-left, .tg-layout-arrow-right, .tg-layout-slider-bullets').attr('style','');
			}
			
			val = $('.the_grid_source_type input:checked').val();
			if (val === 'instagram') {
				$('.tg-layout-instagram-header').removeClass('hidden-block');
			} else {
				$('.tg-layout-instagram-header').addClass('hidden-block');
			}
			if (val === 'youtube') {
				$('.tg-layout-youtube-header').removeClass('hidden-block');
			} else {
				$('.tg-layout-youtube-header').addClass('hidden-block');
			}
			if (val === 'vimeo') {
				$('.tg-layout-vimeo-header').removeClass('hidden-block');
			} else {
				$('.tg-layout-vimeo-header').addClass('hidden-block');
			}
		}
		
		hide_layout_area();

		// ======================================================
		//  Justified layout force image size (no Aqua Resizer)
		// ======================================================

		$('.the_grid_style input').on('change', function() {
			var $img_size = $('.the_grid_image_size');
			if ($(this).val() === 'justified') {
				$img_size.removeClass('required').show();
			} else {
				$img_size.addClass('required');
			}
		});

		// ======================================================
		//  Preloader style color/size
		// ======================================================	

		$('.the_grid_preloader_style select').on('change', function() {
			var style = $(this).val();
			$('.the_grid_preloader_preview').find('.tg-grid-preloader-inner').addClass('hide').removeClass('show');
			$('.the_grid_preloader_preview').find('.'+style).removeClass('hide').addClass('show');
		});
		$('.the_grid_preloader_color .tomb-colorpicker').wpColorPicker({
  			change: function(event, ui){
				var color = ui.color.toString();
				$('.tg-grid-preloader-inner > div').css('background-color', color);
				$('.tg-grid-preloader-inner.pacman > div,.tg-grid-preloader-inner.ball-clip-rotate > div').css('border-color', color);
			}
		});
		
		$('input[name="the_grid_preloader_size"]').on('change', function() {
			$('.tg-grid-preloader-scale').css('transform', 'scale('+$(this).val()+')');
		});
		
		$('.preloader-styles').removeAttr('scoped');
		
		// ======================================================
		// WPML language switcher
		// ======================================================
		
		$(document).on('click','.the_grid_language span', function() {	
			var $this = $(this);
			$this.closest('.tomb-field').find('.tomb-image-holder, input, img').removeAttr('data-checked checked');
			$this.attr('data-checked',1);
			$this.find('img').attr('data-checked',1);
			$this.find('input').attr('checked',1);
				
			var lang = $this.find('input').val();
			$('#tg_post_save').click();
			$(document).ajaxSuccess(function() {
				setGetParameter('lang',lang);
			});
		});
	
		function setGetParameter(paramName, paramValue) {
			var url = window.location.href;
			if (url.indexOf(paramName + "=") >= 0) {
				var prefix = url.substring(0, url.indexOf(paramName));
				var suffix = url.substring(url.indexOf(paramName));
				suffix = suffix.substring(suffix.indexOf("=") + 1);
				suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
				url = prefix + paramName + "=" + paramValue + suffix;
			} else {
				if (url.indexOf("?") < 0) {
					url += "?" + paramName + "=" + paramValue;
				} else {
				 url += "&" + paramName + "=" + paramValue;
				}
			}
			window.location.href = url;
		}
		
		// show grid settings
		$('#wpbody').fadeIn(800);

	});

})(jQuery);

// ======================================================
//  AutoGrow input plugin
// ======================================================

(function($){

    $.fn.autoGrowInput = function(o) {

        o = $.extend({
            maxWidth: 1000,
            minWidth: 0,
            comfortZone: 70
        }, o);

        this.filter('input:text').each(function(){

            var minWidth = o.minWidth || $(this).width(),
                val = '',
                input = $(this),
                testSubject = $('<tester/>').css({
                    position: 'absolute',
                    top: -9999,
                    left: -9999,
                    width: 'auto',
                    fontSize: input.css('fontSize'),
                    fontFamily: input.css('fontFamily'),
                    fontWeight: input.css('fontWeight'),
                    letterSpacing: input.css('letterSpacing'),
                    whiteSpace: 'nowrap'
                }),
                check = function() {

                    if (val === (val = input.val())) {return;}

                    // Enter new content into testSubject
                    var escaped = val.replace(/&/g, '&amp;').replace(/\s/g,'&nbsp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    testSubject.html(escaped);

                    // Calculate new width + whether to change
                    var testerWidth = testSubject.width(),
                        newWidth = (testerWidth + o.comfortZone) >= minWidth ? testerWidth + o.comfortZone : minWidth,
                        currentWidth = input.width(),
                        isValidWidthChange = (newWidth < currentWidth && newWidth >= minWidth) || (newWidth > minWidth && newWidth < o.maxWidth);

                    // Animate width
                    if (isValidWidthChange) {
                        input.width(newWidth);
                    }

                };

            testSubject.insertAfter(input);

            $(this).on('keyup keydown blur update change', check);

        });

        return this;

    };

})(jQuery);