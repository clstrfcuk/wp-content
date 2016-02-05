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
               
                s.css({'display':'', 'line-height': lh });              

                check_responsive_font_sizes();

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

        // box shadow for all form style 
        apply_boxshaddow();

        
        if(jQuery(".slidein-overlay").length > 0){              
         Ps.initialize(document.getElementById("slide-in-animate"));
        }

    });

    jQuery(window).resize(function(){        

    	/*  = Responsive Typography
        *-----------------------------------------------------------*/
        CPAutoResponsiveResize();

        // hide image for small devices
        hide_image_on_smalldevice();

        CP_slide_in_height();

        //set color for li tags 
       // cp_color_for_list_tag();

       // set_optin_widget_bottom();
    });

    /**
      *	 This function will hide image on small devices
    */
    function hide_image_on_smalldevice(){
        jQuery(".slidein-overlay").each(function() {
            var vw          = jQuery(window).innerWidth();
            var flag        = jQuery(this).data('image-position');
            var hidewidth   = jQuery(this).data('hide-img-on-mobile');                          
            if(hidewidth){
                if(vw <= hidewidth ){
                    jQuery(this).find('.cp-image-container').addClass('cp-hide-image');
                } else {
                    jQuery(this).find('.cp-image-container').removeClass('cp-hide-image');
                }
            }
        });
    }

})(jQuery);


/**
 *  Check inner span has set font size
 */
check_responsive_font_sizes();
function check_responsive_font_sizes() {

    //  Apply font sizes
    jQuery(".cp_responsive[data-font-size-init]").each(function(index, el) {

        if( jQuery( el ).find('.cp_font').length ) {
            //  Added class `cp-no-responsive` to over ride the init font size of all - elements i.e. - cp-title, .cp-description etc.
            //  Add for only parents not inner child's
            if( !jQuery( el ).hasClass('.cp_font, .cp_line_height') ) {
                jQuery( el ).addClass('cp-no-responsive');
            }
        } else {
            //  If child element not found class - `cp_font` then remove class `cp-no-responsive`
            jQuery( el ).removeClass('cp-no-responsive');
        }
    });
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

    check_responsive_font_sizes();

    //  Slide In height
    CP_slide_in_height();
}


/**
  * This function adjust height for Slide In
  * Loop for all live Slide In's
  * 
  */
function CP_slide_in_height() {

    setTimeout(function() {

        //  Loop all live Slide In's
        jQuery('.cp-slidein-popup-container').each(function(index, element) {

            var t                       = jQuery(element),
                slidein                 = t.find('.cp-slidein'),
                cp_slidein              = t.find('.cp-slidein'),
                slide_overlay           = t.find('.slidein-overlay'),
                slide_overlay_height    = t.find('.cp-slidein').outerHeight(),
                slidein_body_height     = t.find('.cp-slidein-body').outerHeight(),
                ww                      = jQuery(window).width();

            if( ( slidein_body_height > jQuery(window).height() ) ) {
                slidein.addClass('cp-slidein-exceed');
                slide_overlay.each(function( i, el ) {
                    if( jQuery(el).hasClass('si-open') ) {
                        jQuery('html').addClass('cp-exceed-vieport');

                    }
                });
                slidein.css('height', slidein_body_height );
            } else {
                slidein.removeClass('cp-slidein-exceed');
                jQuery('html').removeClass('cp-exceed-vieport');
                slidein.css('height', '' );
            }

        });
    }, 1200);
}


// function to change color for list type according to span color
function cp_color_for_list_tag(){     
    jQuery(".slidein-overlay").each(function() { 
        var moadal_style = jQuery(this).find(".cp-slidein-body").attr('class').split(' ')[1]; 
		var is_responsive_cls = jQuery(this).parents(".cp_responsive").length;
		if( is_responsive_cls ) {
        jQuery(this).find("li").each(function() { 
                var parent_li   = jQuery(this).parents(".cp_responsive").attr('class').split(' ')[0],
                    cnt         = jQuery(this).index()+1,  
                    font_size   = jQuery(this).find(".cp_font").css("font-size"),                           
                    color       = jQuery(this).find("span").css("color"),
                    list_type   = jQuery(this).parent();
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
                     if( spancolor.length > 0 ){
                            color = spancolor;
                     }
                });
                
                var font_style =''; 
                jQuery(".cp-li-color-css-"+cnt).remove();
                jQuery(".cp-li-font-css-"+cnt).remove();
                if(font_size){
                   font_style='font-size:'+font_size;
                   jQuery('head').append('<style class="cp-li-font-css'+cnt+'">.'+moadal_style+' .'+parent_li+' li:nth-child('+cnt+'){ '+font_style+'}</style>');
                }   
                if(color){
                  jQuery('head').append('<style class="cp-li-color-css'+cnt+'">.'+moadal_style+' .'+parent_li+' li:nth-child('+cnt+'){ color: '+color+';}</style>');
                }      
          });
		}
    });
}


//function for box shadow for form field

function apply_boxshaddow (data) {
    jQuery(".slidein-overlay").each(function() { 
      
        var border_color   = jQuery(this).find(".cp-form-container").find(".cp-email").css("border-color"),       
            moadal_style   = jQuery(this).find(".cp-slidein-body").attr('class').split(' ')[1],
            classname      = jQuery(this).data("class"),
            cont_class     = jQuery(this).data("class");

        if( jQuery(this).hasClass('ps-container') ){
            cont_class  = jQuery(this).data("ps-id");
            border_color = data;
            classname = 'slidein-overlay';
        }

        jQuery(".cp-box-shaddow-"+cont_class).remove();
        jQuery('head').append('<style class="cp-box-shaddow-'+cont_class+'">.'+classname+' .cp-slidein .'+moadal_style+' input.cp-email:focus,  .'+classname+' .cp-slidein .'+moadal_style+' input.cp-name:focus {  box-shadow: 0 0 4px '+border_color+';}</style>');

    });
}


/* Toggle Slide In on click of button */
jQuery("body").on( 'click', '.cp-slidein-head .cp-slidein-toggle' , function(e){
    e.preventDefault();

    jQuery(this).toggleClass('cp-widget-open');

    var slidein_container = jQuery(this).closest('.cp-slidein'),
        border_width      = slidein_container.find('.cp-slidein-content').css("border-bottom-width");

    if( jQuery(this).hasClass('cp-widget-open') ) {
        slidein_container.animate({ 
            'bottom' :  0 
        }, 600 );
    } else {

        if(slidein_container.hasClass('cp-slidein-exceed')){
            var cp_slidein_body_ht = slidein_container.height();
        }else{
             var cp_slidein_body_ht = jQuery(this).closest('.cp-slidein-body').outerHeight();
        }

        var cp_slidein_header_ht = jQuery(this).closest('.cp-slidein-head').outerHeight();
        var bottomCss = cp_slidein_body_ht - cp_slidein_header_ht + 2;

        if(typeof border_width !=='undefined' && border_width !==''){
            border_width  = border_width.replace('-', 'px');
            border_width  = parseInt(border_width);
            if(slidein_container.hasClass('cp-slidein-exceed')){
                bottomCss     = bottomCss - border_width  ;
            }else{
                bottomCss     = border_width + bottomCss ;
            }
        }
        
        slidein_container.animate({ 
            'bottom' : '-'+bottomCss + 'px' 
        }, 600 );
    }
    
    e.stopPropagation();

});


jQuery(this).on('smile_data_received',function(e,data){
         set_optin_widget_bottom();
    });

function set_optin_widget_bottom(){
    
    setTimeout(function() {
        jQuery('.cp-slidein-popup-container').each(function() {

            if( jQuery(this).find('.cp-slidein-toggle').length > 0 ) {

                var slidein_container = jQuery(this).find('.cp-slidein');

                if( jQuery(this).find('.cp-slidein-toggle').hasClass('cp-widget-open') ) {
                    
                    slidein_container.animate({ 
                        'bottom' :  0 
                    }, 600 );
                } else {
                   
                    if(slidein_container.hasClass('cp-slidein-exceed')){
                        var cp_slidein_body_ht = slidein_container.height();
                    }else{
                        var cp_slidein_body_ht = jQuery(this).find('.cp-slidein-body').outerHeight();
                    }
                    
                    var cp_slidein_header_ht = jQuery(this).find('.cp-slidein-head').outerHeight();
                    var bottomCss = cp_slidein_body_ht - cp_slidein_header_ht + 2;
                    var border_width      = slidein_container.find('.cp-slidein-content').css("border-bottom-width");
                   
                    if(typeof border_width !=='undefined' && border_width !==''){
                        border_width  = border_width.replace('-', 'px');
                        border_width  = parseInt(border_width);
                         if(slidein_container.hasClass('cp-slidein-exceed')){
                            bottomCss     = bottomCss - border_width  ;
                         }else{
                             bottomCss     = border_width + bottomCss ;
                         }
                    }
                   
                    slidein_container.animate({ 
                        'bottom' : '-'+bottomCss + 'px' 
                    }, 600 );


                }
            }
        });
    }, 200);
}