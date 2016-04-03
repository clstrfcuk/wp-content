
jQuery.noConflict();

(function($) {
	
	"use strict";
	
	$(document).ready(function() {
		
		$(document).on('click', '.tg-grid-list-wrapper[data-multi-select=""] .tg-grid-list-holder li', function() {
			var $this = $(this);
			var value = $this.data('name');
			$('.tg-grid-list-holder li').removeClass('selected');
			$this.addClass('selected');
			$this.closest('ul').next('input').val(value);
		});
		
		$(document).on('keyup','.tg-grid-list-search', function() {
			var val = $(this).val();
			tg_search_grid(val);
		});
		
		function tg_search_grid(val) {
			$('.tg-grid-list-holder li').each(function(index, element) {
				var $this = $(this);
				var grid = $this.text();
				if (grid.toLowerCase().indexOf(val) >= 0) {
                	$this.show();
				} else {
					$this.hide();
				}
            });
		}
	});

})(jQuery);
