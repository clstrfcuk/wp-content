(function($) {

	/**
     *  1. FitText.js 1.2 - (http://sam.zoy.org/wtfpl/)
     *-----------------------------------------------------------*/
    (function( $ ){
      $.fn.fitText = function( kompressor, options ) {
        // Setup options
        var compressor = kompressor || 1,
            settings = $.extend({
              'minFontSize' : Number.NEGATIVE_INFINITY,
              'maxFontSize' : Number.POSITIVE_INFINITY
            }, options);
        return this.each(function(){
          // Store the object
          var $this = $(this);
          // Resizer() resizes items based on the object width divided by the compressor * 10
          var resizer = function () {
            $this.css('font-size', Math.max(Math.min($this.width() / (compressor*10), parseFloat(settings.maxFontSize)), parseFloat(settings.minFontSize)));
          };
          // Call once to set.
          resizer();
          // Call on resize. Opera debounces their resize by default.
          $(window).on('resize.fittext orientationchange.fittext', resizer);
        });
      };
    })( jQuery );

    /**
     *  2. CP Responsive - (Required - FitText.js)
     *
     *  Required to call on READY & LOAD
     *-----------------------------------------------------------*/
    function CPApplyFlatText(s, fs) {
        if( s.hasClass('cp-description') || s.hasClass('cp-short-description') || s.hasClass('cp-info-container') ) {
            s.fitText(1.7, {  minFontSize: '12px', maxFontSize: fs } );
        } else {
            s.fitText(1.2, {  minFontSize: '16px', maxFontSize: fs } );
        }
    }
    function CPAutoResponsiveResize() {
        jQuery('.cp_responsive').each(function(index, el) {
            var lh              = '',
                ww              = jQuery(window).width(),
                s               = jQuery(el),
                fs              = s.css( 'font-size' ),
                CKE_FONT        = s.attr( 'data-font-size' ),
                Def_FONT        = s.attr( 'data-font-size-init' ),
                CKE_LINE_HEIGHT = s.attr( 'data-line-height' ),
                Def_LineHeight  = s.attr( 'data-line-height-init' );

            if( CKE_FONT ) {
                fs = CKE_FONT;          //  1. CKEditor font sizes from editor
            } else if( Def_FONT ) {
                fs = Def_FONT;          //  2. Initially stored font size
            }

            //  Initially set empty line height
            if( CKE_LINE_HEIGHT ) {
                lh = CKE_LINE_HEIGHT;          //  1. CKEditor font sizes from editor
            } else if( Def_LineHeight ) {
                lh = Def_LineHeight;          //  2. Initially stored font size
            }

            if( ww <= 800 ) {
                //  Apply default line-height - If it does not contain class - `cp_line_height`
                s.css({'display':'block', 'line-height':'1.15em'});
                CPApplyFlatText(s, fs);
            } else {
                //  Apply default line-height - If it does not contain class - `cp_line_height`
                s.css({'display':'', 'line-height': lh });
        
                //  Apply `fit-text` for all CKEditor elements - ( .cp-title,  .cp-description etc. )
                s.fitText(1.2, {  minFontSize: fs, maxFontSize: fs } );
            }
        });
    }

    jQuery(document).ready(function() {

    	//  Set normal values in data attribute to reset these on window resize
        setTimeout(function() {
            CPResponsiveTypoInit();

            //for link color change 
            cp_color_for_list_tag();

         }, 1500 );

        // hide image for small devices
    	hide_image_on_smalldevice();

    	// hide image for optin to win style
    	optin_to_win_hide_img();

		// hide image for direct download style
    	direct_download_hide_img();

    	// hide image for free book style
    	free_ebook_download_hide_img();

        // box shaddow for all form style 
        apply_boxshaddow();

    });

    jQuery(window).resize(function(){

    	//  Model height
    	CPModelHeight();
        
    	/*  = Responsive Typography
        *-----------------------------------------------------------*/
         // setTimeout(function() {
            CPAutoResponsiveResize();
        // }, 400);

        jQuery(".cp-onload").each(function(t) {
            var class_id    = jQuery(this).data("class-id");
            var modal       = jQuery('.'+class_id);
            if( modal.hasClass('cp-window-size')){
                modal.windowSize();
            }
        });

        // hide image for small devices
        hide_image_on_smalldevice();

        // hide image for optin to win style
        optin_to_win_hide_img();

        // hide image for direct download style
        direct_download_hide_img();

        // hide image for free book style
        free_ebook_download_hide_img();

        // Equalize two columns content vertically center
        cp_column_equilize();

        set_affiliate_link();

        //set color for li tags 
       // cp_color_for_list_tag();
    });

    jQuery(window).load(function() {
        set_affiliate_link();
    });

    jQuery.fn.windowSize = function(){
        var cp_content_container= this.find(".cp-content-container"),
            cp_modal            = this.find(".cp-modal"),
            cp_modal_content    = this.find(".cp-modal-content"),
            cp_modal_body       = this.find(".cp-modal-body");

        cp_modal.removeAttr('style');
        cp_modal_content.removeAttr('style');
        cp_content_container.removeAttr('style');
        cp_modal_body.removeAttr('style');
        var ww = jQuery(window).width() + 30;
        var wh = jQuery(window).height();
        jQuery(this).find("iframe").css("width",ww);

        cp_content_container.css({'max-width':ww+'px','width':'100%','height':wh+'px','padding':'0','margin':'0 auto'});
        cp_modal_content.css({'max-width':ww+'px','width':'100%'});
        cp_modal.css({'max-width':ww+'px','width':'100%','left':'0','right':'0'});
        cp_modal_body.css({'max-width':ww+'px','width':'100%','height':wh+'px'});
    }

    /**
      *	 This function will hide image on small devices
    */
    function hide_image_on_smalldevice(){
        jQuery(".cp-overlay").each(function() {
            var vw          = jQuery(window).innerWidth();
            var flag        = jQuery(this).data('image-position');
            var hidewidth   = jQuery(this).data('hide-img-on-mobile');                          
            if( hidewidth ) {
                if( vw <= hidewidth ){
                    jQuery(this).find('.cp-image-container').addClass('cp-hide-image');
                } else {
                    jQuery(this).find('.cp-image-container').removeClass('cp-hide-image');
                }
            }
        });
    }

    /**
      *	 This function will hide image for optin to win style
    */
    function optin_to_win_hide_img(){
        jQuery(".cp-overlay").each(function() {

            if( jQuery(this).find('.cp-modal-body').hasClass("cp-optin-to-win") ) {
                var vw = jQuery(window).innerWidth();
                var flag = jQuery(this).data('image-position');
                var hidewidth = jQuery(this).data('hide-img-on-mobile');

                if( vw <= hidewidth ){
                    if( hidewidth >= 768 ){
                        jQuery(this).find('.cp-text-container').removeClass('col-lg-7 col-md-7 col-sm-7').addClass('col-lg-12 col-md-12 col-sm-12  cp-bigtext-container');
                    }
                } else {
                    jQuery(this).find('.cp-text-container').removeClass('col-lg-12 col-md-12 col-sm-12  cp-bigtext-container').addClass('col-lg-7 col-md-7 col-sm-7 ');
                }

           }
        });
    }

    /**
      *	 This function will hide image for direct download style
    */
    function direct_download_hide_img(){
        jQuery(".cp-overlay").each(function() {
            if( jQuery(this).find('.cp-modal-body').hasClass("cp-direct-download") ) {
                var vw = jQuery(window).width();
                var flag = jQuery(this).data('image-position');
                //for hide on mobile below width
                var hidewidth = jQuery(this).data('hide-img-on-mobile');
                if( vw <= hidewidth ){
                    if( hidewidth >= 768 ){
                        jQuery(this).find('.cp-text-container').removeClass('col-lg-7 col-md-7 col-sm-7').addClass('col-lg-12 col-md-12 col-sm-12  cp-bigtext-container');
                    }
                } else {
                    jQuery(this).find('.cp-text-container').removeClass('col-lg-12 col-md-12 col-sm-12  cp-bigtext-container').addClass('col-lg-7 col-md-7 col-sm-7 ');
                }
           }
        });
    }

    /**
      *	 This function will hide image for ebook style
    */
    function free_ebook_download_hide_img(){
        jQuery(".cp-overlay").each(function() {
            if( jQuery(this).find('.cp-modal-body').hasClass("cp-free-ebook") ) {

                var vw = jQuery(window).outerWidth();
                var flag = jQuery(this).data('image-position');
                //for hide on mobile bellow width
                var hidewidth = jQuery(this).data('hide-img-on-mobile');
                if( vw <= hidewidth ){
                    if( hidewidth >= 768 ){
                        jQuery(this).find('.cp-text-container').removeClass('col-lg-7 col-md-7 col-sm-7').addClass('col-lg-12 col-md-12 col-sm-12  cp-bigtext-container');
                    }
                } else {
                    jQuery(this).find('.cp-text-container').removeClass('col-lg-12 col-md-12 col-sm-12  cp-bigtext-container').addClass('col-lg-7 col-md-7 col-sm-7 ');
                }
           }
        });
    }

})(jQuery);

/**
  * This function will apply height to cp-columns-equalized class
  */
function cp_column_equilize() {
    setTimeout(function() {
        jQuery(".cp-columns-equalized").each(function() {

            // if modal is open then only apply equalize properties 
            if( jQuery(this).closest('.cp-overlay').hasClass('cp-open') ) {    
                var wh = jQuery(window).width();

                var childClasses = Array();
                jQuery(this).children('.cp-column-equalized-center').each(function () {
                    var contHeight = jQuery(this).outerHeight();
                    childClasses.push(contHeight);
                });

                var maxHeight = Math.max.apply(Math, childClasses);

                if( wh > 768 ) {
                    jQuery(this).css( 'height', maxHeight );
                } else {
                    jQuery(this).css( 'height', 'auto' );
                }
            }
        });
    }, 800 );
}

/**
 *  Set normal values in data attribute to reset these on window resize
 */
function CPResponsiveTypoInit() {

    //  1. Add font size attribute
    jQuery('.cp_responsive').each(function(index, el) {
        var s = jQuery(el);

        //  Add attribute `data-line-height-init` for all `cp_responsive` classes. Except `.cp_line_height` which is added from editor.
        if( !s.hasClass('cp_line_height') ) {
            //  Set `init` font size data attribute
            var fs      = s.css('font-size');
            var hasData = s.attr('data-font-size');
            if(!hasData) {
                s.attr('data-font-size-init', fs);
            }
        }

        //  Add attribute `data-line-height-init` for all `cp_responsive` classes. Except `.cp_font` which is added from editor.
        if( !s.hasClass('cp_font') ) {
            //  Set `init` line height data attribute
            var lh      = s.css('line-height');
            var hasData = s.attr('data-line-height');
            if(!hasData) {
                s.attr('data-line-height-init', lh);
            }
        }

    });

    //  Model height
    CPModelHeight();
}


/**
  *	This function adjust height for modal
  * Loop for all live modal's
  * 
  */
function CPModelHeight() {

    setTimeout(function() {

        //  Loop all live modal's
        jQuery('.cp-modal-popup-container').each(function(index, element) {
            
            var t           = jQuery(element),
                modal       = t.find('.cp-modal'),
                overlay     = t.find('.cp-overlay'),
                overlay_height     = t.find('.cp-overlay').outerHeight(),
                modal_body_height  = t.find('.cp-modal-body').outerHeight(),
                ww          = jQuery(window).width();

            if( !jQuery( this ).hasClass( 'cp-inline-modal-container' ) ){
                if( ( modal_body_height > overlay_height ) ) {
                    modal.addClass('cp-modal-exceed');
                    overlay.each(function( i, el ) {
                        if( jQuery(el).hasClass('cp-open') ) {
                            jQuery('html').addClass('cp-exceed-vieport');
                        }
                    });
                    //modal.css('height', modal_body_height );
                } else {
                    modal.removeClass('cp-modal-exceed');
                    jQuery('html').removeClass('cp-exceed-vieport');
                    modal.css('height', '' );
                }
            }

            set_affiliate_link();

        });
    }, 1200);
}

// function to reinitialize affiliate
function set_affiliate_link(data){   
        jQuery(".cp-overlay").each(function() {           
            var modal_size           = jQuery(this).find(".cp-modal").hasClass('cp-modal-window-size'),
                affiliate_setting    = jQuery(this).data('affiliate_setting');
                vw                   = jQuery(window).width(),
                cp_affilate_link     = jQuery(this).find(".cp-affilate-link"),
                cp_animate_container = jQuery(this).find(".cp-animate-container"),
                cp_overlay           = jQuery(this);

               if(jQuery(this).hasClass('ps-container')){
                 var affiliate_setting = data;
               }  

           if( affiliate_setting == '1' ){
                if( !modal_size ){                  
                    if( vw <= 768 ){
                        cp_affilate_link.appendTo(cp_animate_container);
                        cp_affilate_link.addClass('cp-afl-for-smallscreen');                
                    } else {
                        cp_affilate_link.removeClass('cp-afl-for-smallscreen')  
                        cp_affilate_link.appendTo(cp_overlay);              
                    }
                } else {                      
                    if( vw <= 768 ){                                               
                        cp_affilate_link.addClass('cp-afl-for-smallscreen');
                        cp_affilate_link.appendTo(cp_animate_container); 
                        var ht = jQuery(this).find(".cp-modal-content").outerHeight() - 40; 
                    } else {
                        cp_affilate_link.removeClass('cp-afl-for-smallscreen');
                        cp_affilate_link.appendTo(cp_overlay);  
                        cp_affilate_link.css( 'top', '' );  
                    }                                 
                }
            }
        });
    }

// function to change color for list type according to span color
function cp_color_for_list_tag(){   

    jQuery(".cp-overlay").each(function() {
            var ov                  = jQuery(this),
                is_responsive_cls   = jQuery(this).parents(".cp_responsive").length;        

        jQuery(this).find("li").each(function() { 
            
            var moadal_style = jQuery( ov ).find(".cp-modal-body").attr('class').split(' ')[1];

            var parent_li   = jQuery(this).parents(".cp_responsive").attr('class');

            if( parent_li !== null ){
                parent_li   = jQuery(this).parents(".cp_responsive").attr('class').split(' ')[0];
            } else {
                parent_li   = jQuery(this).parents("div").attr('class').split(' ')[0];  
            }

            var  cnt    = jQuery(this).index() + 1,  
                font_size   = jQuery(this).find(".cp_font").css("font-size"),                           
                color       = jQuery(this).find("span").css("color"),
                list_type   = jQuery(this).parent(),
                list_type   = list_type[0].nodeName.toLowerCase(),
                style_type  = '',
                style_css   = '';

            if( list_type == 'ul' ){
                style_type = jQuery(this).closest('ul').css('list-style-type');
                if( style_type == 'none' ){
                    jQuery(this).closest('ul').css( 'list-style-type', 'disc' );  
                }
            } else {
                style_type = jQuery(this).closest('ol').css('list-style-type');
                if( style_type == 'none' ){
                    jQuery(this).closest('ol').css( 'list-style-type', 'decimal' );  
                }
            }
               
            jQuery(this).find("span").each(function(){
                 var spancolor = jQuery(this).css("color");
                 if(spancolor.length > 0){
                        color = spancolor;
                 }
            });
                
            var font_style =''; 
            jQuery(".cp-li-color-css-"+cnt).remove();
            jQuery(".cp-li-font-css-"+cnt).remove();
            if( font_size ){
               font_style = 'font-size:'+font_size;
               jQuery('head').append('<style class="cp-li-font-css'+cnt+'">.'+moadal_style+' .'+parent_li+' li:nth-child('+cnt+'){ '+font_style+'}</style>');
            }   
            if( color ){
              jQuery('head').append('<style class="cp-li-color-css'+cnt+'">.'+moadal_style+' .'+parent_li+' li:nth-child('+cnt+'){ color: '+color+';}</style>');
            }      
          });
    });
}

jQuery(this).on('smile_data_received',function(e,data){
   // Equilize two columns content vertically center
    cp_column_equilize();
    addPaddingtoYoutubeFrame();
});

//function for box shadow for form field

function apply_boxshaddow (data) {
    jQuery(".cp-overlay").each(function() { 
      
        var border_color   = jQuery(this).find(".cp-form-container").find(".cp-email").css("border-color"),       
            moadal_style   = jQuery(this).find(".cp-modal-body").attr('class').split(' ')[1],
            classname      = jQuery(this).data("class"),
            cont_class     = jQuery(this).data("class");

        if(jQuery(this).hasClass('ps-container')){
            cont_class  = jQuery(this).data("ps-id");
            border_color = data;
            classname ='cp-overlay';
        }

        jQuery(".cp-box-shaddow-"+cont_class).remove();
        jQuery('head').append('<style class="cp-box-shaddow-'+cont_class+'">.'+classname+' .cp-modal .'+moadal_style+' input.cp-email:focus,  .'+classname+' .cp-modal .'+moadal_style+' input.cp-name:focus {  box-shadow: 0 0 4px '+border_color+';}</style>');

    });
}

//  Style - YouTube
//  YouTube Add padding for <iframe> if modal size is 'cp-modal-window-size'
function addPaddingtoYoutubeFrame() {
    if( jQuery('.cp-youtube-container').length ) {
        if( jQuery('.cp-modal').hasClass('cp-modal-window-size') ) {
            var oh = jQuery('.cp-form-container').outerHeight();
            jQuery('.cp-youtube-frame').css('padding-bottom', oh + 'px');
        } else {
            jQuery('.cp-youtube-frame').css('padding-bottom', '');
        }
    }
}