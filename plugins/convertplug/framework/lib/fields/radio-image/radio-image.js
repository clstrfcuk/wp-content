jQuery(document).ready(function(){
	var rm_container = jQuery(".smile-radio-image-holder");
	rm_container.click(function(){
		var $this = jQuery(this);
		jQuery.each(rm_container,function(index,element){
			jQuery(this).removeClass('selected');
		});
		$this.addClass('selected');
		$this.find('input.smile-radio-image').trigger('change');
	});
});