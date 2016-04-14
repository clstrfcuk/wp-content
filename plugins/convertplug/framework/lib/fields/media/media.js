jQuery(document).ready(function(jQuery){
	jQuery('.smile-upload-media').click(function(e) {
		_wpPluploadSettings['defaults']['multipart_params']['admin_page']= 'customizer';
		var button = jQuery(this);
		var id = 'smile_'+button.attr('id');
		var uid = button.data('uid');
		var rmv_btn = 'remove_'+button.attr('id');
		var img_container = button.attr('id')+'_container';
		//Extend wp.media object
		 var uploader = wp.media.frames.file_frame = wp.media({
			title: 'Select or Upload Image',
			button: {
				text: 'Choose Image'
			},
			library: { 
				type: 'image' 
			},
			multiple: false,
		});
		uploader.on('select', function(props, attachment){
			attachment = uploader.state().get('selection').first().toJSON();
			var data = attachment.id+"|"+attachment.url;
			var sz = jQuery(".cp-media-"+uid).val();
			var val = attachment.id+"|"+sz;

			jQuery("#"+id).val(val);
			jQuery("#"+id).attr('value',val);
			jQuery(".cp-media-"+uid).attr('data-id',attachment.id);
			jQuery(".cp-media-"+uid).parents(".cp-media-sizes").removeClass("hide-for-default");
			jQuery("."+img_container).html('<img src="'+attachment.url+'"/>');
			jQuery("#"+rmv_btn).show();

			button.text('Change Image');
			jQuery("#"+id).trigger('change');
		});
		uploader.open(button);
		return false;
	});
 	
	jQuery('.smile-remove-media').on('click', function(e){
		e.preventDefault();
		var button = jQuery(this);
		var id = button.attr('id').replace("remove_","smile_");
		var upload = button.attr('id').replace("remove_","");
		var img_container = button.attr('id').replace("remove_","")+'_container';
		jQuery("#"+id).attr('value','');
		
		var html = '<p class="description">No Image Selected</p>';
		jQuery("."+img_container).html(html);
		
		button.hide();
		jQuery("#"+upload).text('Select Image');
		
		jQuery("#"+id).trigger('change');
	});
	
	jQuery('.smile-default-media').on('click', function(e){
		e.preventDefault();
		var button = jQuery(this);
		var id = button.attr('id').replace("default_","smile_");
		var upload = button.attr('id').replace("default_","");
		var img_container = button.attr('id').replace("default_","")+'_container';
		var container = jQuery(this).parents('.content');
		var default_img = jQuery(this).data('default');
		jQuery("#"+id).attr('value',default_img);
		
		var html = '<p class="description">No Image Selected</p>';
		jQuery("."+img_container).html('<img src="'+default_img+'"/>');
				
		jQuery("#"+id).trigger('change');
		container.find(".cp-media-sizes").hide().addClass('hide-for-default');
	});

	jQuery(".cp-media-size").on("change", function(e){
		var img_id = jQuery(this).attr('data-id');
		var input = 'smile_'+jQuery(this).parents('.cp-media-sizes').data('name');
		var val = "";
		if( img_id !== '' ) {
			val = img_id+"|"+jQuery(this).val();
		}
		jQuery("#"+input).val(val);
		jQuery("#"+input).attr('value',val);
	});
});