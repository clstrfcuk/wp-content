//Handle the file uploads
jQuery(document).ready(function($) {
    'use strict';
    
    /**
     * Function called when the File upload button is called
     * We upload the files before we submit the forms
     */
    function tco_woo_file_upload(){
        $(document).on('click', 'button.tco_woo_file_upload', function(e) {
            e.preventDefault();
            var sending = false;
            var elem = $(this).prev();
            elem.click();
            elem.on('change', function() {
                if(!sending){
                    var data = new FormData();
                    var id = $(this).attr('id');
                    
                    data.append('action', 'tco_woo_handle_file_upload');
                    data.append('security', $(id+'_secret').val());
                    data.append('tco_woo_file', $(this).prop('files')[0]);
					
					var $button = $(this);
					var $formElm = $button.parent(),
				        tempHtml = $formElm.html();
						
					$formElm.html('<img src="' + tco_woo_js.loading_image+'" alt="' + tco_woo_js.processing + '" />');
					
                    $.ajax({
                        type: 'POST',               
                        processData: false, // important
                        contentType: false, // important
                        data: data,
                        url: tco_woo_js.ajaxurl,
                        success: function(jsonData){
							$formElm.fadeOut(2000, function(){
                                $formElm.html(tempHtml).fadeIn('fast');
                                $button.html(jsonData.message);
                            });
                            if(!jsonData.error){
                                $('#'+id+'_file').val(jsonData.url);
                            }
                           
                        },

                        error : function(){
							$formElm.fadeOut(2000, function(){
                                $formElm.html(tempHtml).fadeIn('fast');
                            });
                            alert(tco_woo_js.error); 
                        }
                    }); 
                }
                sending = true;
            });
        });
    }

    tco_woo_file_upload(); //Call put function

});