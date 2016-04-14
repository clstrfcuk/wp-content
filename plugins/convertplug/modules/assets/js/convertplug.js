(function($) {
    "use strict";

    /**
     * Helper Functions
     *
     * 1. Render - HTML - DropDown - Extract all dropdown options
     * 2. Build ConvertPlug Form
     */

    /**
     * 1. DropDown - Extract all dropdown options
     */
    function get_dropdown_options( string ) {
        var lines = string.split("\n");
        var all_options = '';
        jQuery.each( lines , function(index, val) {
            if( '' != val && 'undefined' != val && null != val ) {
                var option = val.split('+');
                all_options += '<option value="'+option[0].toLowerCase()+'">' + option[0] + '</option>';
            }
        });
        return all_options;
    }

    /**
     * 2. Build ConvertPlug Form
     *
     * @attribute   data        Get all variables
     */
    function design_form( data ) {

        /**
         *  Hide unwanted things from - INFO_BAR
         * 1. Disable 1 & 2 Layouts
         * 2. Form Label options
         * 3. Submit button alignment
         */
        if( jQuery('html').hasClass('cp-customizer-info_bar') ) {
            jQuery('.form_layout-cp-form-layout-1', window.parent.document).closest('.smile-radio-image-holder').addClass('cp-hidden');
            jQuery('.form_layout-cp-form-layout-2', window.parent.document).closest('.smile-radio-image-holder').addClass('cp-hidden');
            jQuery('#smile_form_lable_visible', window.parent.document).closest('.smile-element-container').addClass('cp-hidden');
            jQuery('#smile_form_lable_color', window.parent.document).closest('.smile-element-container').addClass('cp-hidden');
            jQuery('#smile_form_label_font', window.parent.document).closest('.smile-element-container').addClass('cp-hidden');
            jQuery('#smile_form_lable_font_size', window.parent.document).closest('.smile-element-container').addClass('cp-hidden');
            jQuery('#smile_form_submit_align', window.parent.document).closest('.smile-element-container').addClass('cp-hidden');
        }

        //  Set variables
        var cp_submit           = jQuery(".cp-submit"),
            cp_submit_wrap      = jQuery(".cp-submit-wrap"),
            cp_all_inputs_wrap  = jQuery('.cp-all-inputs-wrap');
        var form_submit_align   = data.form_submit_align || '',
            form_grid_structure = data.form_grid_structure,
            fields              = data.form_fields;


        /**
         * Define Form Layout Classes
         */
        var form_layout = data.form_layout;
        var class_fields = '';
        var class_submit = '';
        var class_cp_all_inputs_wrap = 'col-xs-12';
        var all_fields = fields.split(";");
        switch ( form_layout ) {

            case 'cp-form-layout-1':    class_fields = ' col-md-12 col-lg-12 col-sm-12 col-xs-12';
                                        class_submit = ' col-md-12 col-lg-12 col-sm-12 col-xs-12';
                                        jQuery('.cp-all-inputs-wrap').show();                          //  Show Input form
                                        jQuery('.cp-section[data-section-id="submission"]', window.parent.document).show();
                break;

            case 'cp-form-layout-2':    class_fields = ' col-md-6 col-lg-6 col-sm-6 col-xs-12';
                                        class_submit = ' col-md-12 col-lg-12 col-sm-12 col-xs-12';
                                        jQuery('.cp-all-inputs-wrap').show();                          //  Show Input form
                                        jQuery('.cp-section[data-section-id="submission"]', window.parent.document).show();
                break;

            case 'cp-form-layout-3':    //  Grid structure for All Input Wrap & Submit
                                        switch( form_grid_structure ) {
                                            case 'cp-form-grid-structure-1':                class_submit    = ' col-md-6 col-lg-6 col-sm-6 col-xs-12 ';
                                                                                class_cp_all_inputs_wrap    = ' col-md-6 col-lg-6 col-sm-6 col-xs-12 ';
                                                break;
                                            case 'cp-form-grid-structure-2':                class_submit    = ' col-md-4 col-lg-4 col-sm-4 col-xs-12 ';
                                                                                class_cp_all_inputs_wrap    = ' col-md-8 col-lg-8 col-sm-8 col-xs-12 ';
                                                break;
                                            case 'cp-form-grid-structure-3':
                                            default:                class_submit    = ' col-md-3 col-lg-3 col-sm-3 col-xs-12 ';
                                                        class_cp_all_inputs_wrap    = ' col-md-9 col-lg-9 col-sm-9 col-xs-12 ';
                                                break;
                                        }

                                        if( all_fields.length > 0 ) {

                                            //  Remove hidden fields from count
                                            var no_of_hiddens = (fields.match(/input_type->hidden/g) || []).length;
                                            var fields_count = all_fields.length;
                                            if( no_of_hiddens != 'NaN' && no_of_hiddens != 'undefined' && no_of_hiddens != null ) {
                                                fields_count = all_fields.length - no_of_hiddens;
                                            }

                                            switch( fields_count ) {
                                                case 1:
                                                            class_fields = 'col-md-12 col-lg-12 col-sm-12 col-xs-12';
                                                    break;
                                                case 2:
                                                            class_fields = 'col-md-6 col-lg-6 col-sm-6 col-xs-12';
                                                    break;
                                                case 3:
                                                            class_fields = 'col-md-4 col-lg-4 col-sm-4 col-xs-12';
                                                    break;
                                                case 4:
                                                case 5:
                                                            class_fields = 'col-md-3 col-lg-3 col-sm-3 col-xs-12';
                                                    break;
                                                case 6:
                                                case 7:
                                                            class_fields = 'col-md-2 col-lg-2 col-sm-2 col-xs-12';
                                                    break;
                                            }
                                        }
                                        jQuery('.cp-all-inputs-wrap').show();                          //  Show Input form
                                        jQuery('.cp-section[data-section-id="submission"]', window.parent.document).show();

                break;

            case 'cp-form-layout-4':    cp_all_inputs_wrap.removeClass('col-md-9 col-lg-9 col-sm-9');
                                        jQuery('.cp-all-inputs-wrap').hide();                          //  Hide Input form
                                        jQuery('.cp-section[data-section-id="submission"]', window.parent.document).hide();
                break;

            break;
        }

        //  Remove all classes
        var allColClasses = 'col-lg-1 col-md-1 col-sm-1 col-lg-2 col-md-2 col-sm-2 col-lg-3 col-md-3 col-sm-3 col-lg-4 col-md-4 col-sm-4 col-lg-5 col-md-5 col-sm-5 col-lg-6 col-md-6 col-sm-6 col-lg-7 col-md-7 col-sm-7  col-lg-8 col-md-8 col-sm-8 col-lg-9 col-md-9 col-sm-9 col-lg-10 col-md-10 col-sm-10 col-lg-11 col-md-11 col-sm-11 col-lg-12 col-md-12 col-sm-12';
        jQuery('.cp-form-field').removeClass( allColClasses );
        cp_submit_wrap.removeClass( allColClasses );
        cp_all_inputs_wrap.removeClass( allColClasses );

        //  Add classes for Submit, All Input Wrapper
        cp_submit_wrap.addClass( class_submit );
        cp_all_inputs_wrap.addClass( class_cp_all_inputs_wrap );

        if( typeof form_submit_align != 'undefined' && form_submit_align != '' ) {
            //  Remove all classes
            cp_submit_wrap.removeClass( "cp-submit-wrap-center cp-submit-wrap-left cp-submit-wrap-right cp-submit-wrap-full" );
            cp_submit_wrap.addClass( form_submit_align );
        }

        
        /**
         * Create HTML structures
         *
         * 1+ For Inputs
         * 2+ For CKEditor button only
         */

        var HTML = '';
        var HIDDEN_FIELDS = '';

        //  Extract ALL - field
        var all = fields.split(";");
        $.each( all , function( index, val ) {

            //  Empty Fields
            var name = '';
            var require = '';
            var placeholder = '';
            var label = '';
            var type = '';
            var dropdown_options = '';

            //  Extract SINGLE - all
            var single = val.split("|");
            $.each( single , function( i, v ) {
                var s = v.split("->");
                switch( s[0] ) {
                    case 'input_label':         label = s[1];
                        break;
                    case 'input_name':          name = s[1];
                        break;
                    case 'input_placeholder':   placeholder = s[1];
                        break;
                    case 'input_require':       require = ( s[1] === 'true' ) ? ' required ' : '';
                        break;
                    case 'input_type':          type = s[1];
                        break;
                    case 'dropdown_options':    dropdown_options = s[1];
                        break;
                }

            });


            //  For ONLY hidden field
            if( type == 'hidden' ) {
                //  Hidden
                HIDDEN_FIELDS += '<input class="cp-input cp-' + type + '"'
                    + ' type="' + type + '"'
                    + ' name="' + name + '"'
                    + ' placeholder="' + placeholder + '" ' + require + ' />';
            } else {

                /**
                 * Build HTML structure for inputs
                 */
                HTML    += '<div class="cp-form-field '+class_fields+'"><label>'+label+'</label><div>';
    
                switch( type ) {
                    case 'email':
                    case 'textfield':       //  Text
                                            HTML    += '<input class="cp-input cp-' + type + '"'
                                                + ' type="' + type + '"'
                                                + ' name="' + name + '"'
                                                + ' placeholder="' + placeholder + '" ' + require + ' />';
                        break;
                    case 'textarea':        //  Textarea
                                            HTML    += '<textarea class="cp-input cp-' + type + '"' + require
                                                + ' name="' + name + '" placeholder="' + placeholder + '"></textarea>';
                        break;
                    case 'number':
                                            HTML += '<input type="number" min="" max="" step="" value="" class="cp-' + type + '"'
                                                + ' name="' + name + '"'
                                                + ' placeholder="' + placeholder + '" ' + require + ' />';
                    case 'dropdown':
                                            if( '' != dropdown_options && null != dropdown_options && 'undefined' != dropdown_options ) {
                                                HTML += '<select class="cp-' + type + '"' + ' name="' + name + '"' + require + ' >'
                                                +   get_dropdown_options(dropdown_options)
                                                + '</select>';
                                            }
    
    
                        break;
                    // case 'search':
                    //                         HTML += '<input type="search" name="search" class="cp-' + type + '"'
                    //                             + ' name="' + name + '"'
                    //                             + ' placeholder="' + placeholder + '" ' + require + ' >';
                    // case 'url':
                    //                         HTML += '<input type="url" name="url" class="cp-' + type + '"'
                    //                             + ' name="' + name + '"'
                    //                             + ' placeholder="' + placeholder + '" ' + require + ' >';
                    // case 'tel':
                    //                         HTML += '<input type="tel" name="tel" class="cp-' + type + '"'
                    //                             + ' name="' + name + '"'
                    //                             + ' placeholder="' + placeholder + '" ' + require + ' >';
                    // case 'range':
                    //                         HTML += '<input type="range" min="" max="" value="" class="cp-' + type + '"'
                    //                             + ' name="' + name + '"'
                    //                             + ' placeholder="' + placeholder + '" ' + require + ' >';
                    // case 'date':
                    //                         HTML += '<input type="date" class="cp-' + type + '"'
                    //                             + ' name="' + name + '"'
                    //                             + ' placeholder="' + placeholder + '" ' + require + ' >';
                    // case 'color':
                    //                         HTML += '<input type="color" class="cp-' + type + '"'
                    //                             + ' name="' + name + '"'
                    //                             + ' placeholder="' + placeholder + '" ' + require + ' >';
                }
    
                HTML    += '</div></div><!-- .cp-form-field -->';
            }
        });

        //  ConCat ALL_FILEDS & HIDDEN_FIELDS
        HTML = HTML + HIDDEN_FIELDS;

        //  Append to All Inputs Wrap
        jQuery('.cp-all-inputs-wrap').html( HTML );
        // console.log('HTML: ' + HTML);
    }

    /**
     * 3. Append CSS
     */
    function append_form_css( data ) {

        var mailer              = data.mailer,
        form_layout             = data.form_layout,
        form_lable_visible      = data.form_lable_visible,
        form_lable_color        = data.form_lable_color,
        form_lable_font_size    = data.form_lable_font_size,
        default_form            = jQuery(".default-form"),
        custom_form             = jQuery(".custom-html-form"),
        custom_html_form        = data.custom_html_form,
        btn_border_radius       = data.btn_border_radius,
        btn_border_color        = data.btn_border_color,
        btn_bg_color            = data.button_bg_color,
        btn_style               = data.btn_style,
        btn_shadow              = data.btn_shadow,
        button_txt_hover_color  = data.button_txt_hover_color,
        cp_form_container       = jQuery(".cp-form-container");

        if( mailer == "custom-form" ) {
            /* For InfoBar we use the display: flex !important */
            default_form.attr('style','display: none !important');
            custom_form.html(custom_html_form).show();
        } else {
            default_form.attr('style','display: block');
            custom_form.css('display','none');
        }

        //  Set form layout class
        default_form.removeClass( ' cp-form-layout-1 cp-form-layout-2 cp-form-layout-3 cp-form-layout-4 ' );
        default_form.addClass( form_layout );

        /** = Submit Button - CSS
         *-----------------------------------------------------------*/
        var cp_submit = jQuery('.cp-submit');
        var shadow = '';
        var style = '';

        // StyleID                     = data.uid_class || '';
        var form_input_align           = data.form_input_align || 'left';
        var form_input_font            = data.form_input_font || '';
        var form_label_font            = data.form_label_font || '';
        var form_input_color           = data.form_input_color || '';
        var form_input_bg_color        = data.form_input_bg_color || '';
        var form_input_border_color    = data.form_input_border_color || '';
        var form_input_font_size       = data.form_input_font_size || '15';
        var form_input_padding_tb      = data.form_input_padding_tb || '10';
        var form_input_padding_lr      = data.form_input_padding_lr || '25';
        var submit_button_tb_padding   = data.submit_button_tb_padding || '10';
        var submit_button_lr_padding   = data.submit_button_lr_padding || '25';


        //  CSS - Placeholders
        style   +=  "::-webkit-input-placeholder { /* WebKit, Blink, Edge */";
        style   +=  "    font-family: inherit;";
        style   +=  "}";
        style   +=  ":-moz-placeholder { /* Mozilla Firefox 4 to 18 */";
        style   +=  "   font-family: inherit;";
        style   +=  "}";
        style   +=  "::-moz-placeholder { /* Mozilla Firefox 19+ */";
        style   +=  "   font-family: inherit;";
        style   +=  "}";
        style   +=  ":-ms-input-placeholder { /* Internet Explorer 10-11 */";
        style   +=  "   font-family: inherit;";
        style   +=  "}";
        style   +=  ":placeholder-shown { /* Standard (https://drafts.csswg.org/selectors-4/#placeholder) */";
        style   +=  "  font-family: inherit;";
        style   +=  "}";

        //  Hide Labels?
        if( 'undefined' == form_lable_visible || '' == form_lable_visible || '0' == form_lable_visible ) {
            style   +=  ".cp-form-container label {";
            style   +=  "   display:none;";
            style   +=  "}";
        }
        //  CSS - Label
        style   +=  ".cp-form-container label { ";
        style   +=  "   color: " + form_lable_color + ";";
        style   +=  "   font-size: " + form_lable_font_size + "px;";
        style   +=  "   text-align: " + form_input_align + ";";
        style   +=  "   font-family: " + form_label_font + ";";
        style   +=  "} ";

        //  CSS - Select align using 'direction: rtl;'
        //  Cause, Text align for select not working.
        style   +=  ".cp-form-container .cp-form-field select { ";
        style   +=  "   text-align-last: " + form_input_align + ";";
        style   +=  "}";

        //  CSS - Inputs
        style   +=  ".cp-form-container .cp-form-field button, ";
        style   +=  ".cp-form-container .cp-form-field input, ";
        style   +=  ".cp-form-container .cp-form-field select, ";
        style   +=  ".cp-form-container .cp-form-field textarea { ";
        style   +=  "   font-size: " + form_input_font_size + "px;";
        style   +=  "   text-align: " + form_input_align + ";";
        style   +=  "   font-family: " + form_input_font + ";";
        style   +=  "   color: " + form_input_color + ";";
        style   +=  "   background-color: " + form_input_bg_color + ";";
        style   +=  "   border-color: " + form_input_border_color + ";";
        style   +=  "   padding-top: " + form_input_padding_tb + "px;";
        style   +=  "   padding-bottom: " + form_input_padding_tb + "px;";
        style   +=  "   padding-left: " + form_input_padding_lr + "px;";
        style   +=  "   padding-right: " + form_input_padding_lr + "px;";
        style   +=  "}";

        //  CSS - Submit
        style   +=  ".cp-form-container .cp-submit { ";
        style   +=  "   padding-top: " + submit_button_tb_padding + "px;";
        style   +=  "   padding-bottom: " + submit_button_tb_padding + "px;";
        style   +=  "   padding-left: " + submit_button_lr_padding + "px;";
        style   +=  "   padding-right: " + submit_button_lr_padding + "px;";
        style   +=  "}";

        //  Remove all classes
        var classList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
        jQuery.each(classList, function(i, v){
           cp_submit.removeClass(v);
        });
        cp_submit.addClass( btn_style );

        //  Remove all classes
        var classList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
        jQuery.each(classList, function(i, v){
           cp_submit.removeClass(v);
        });
        cp_submit.addClass( btn_style );

        var c_normal    = btn_bg_color;
        var c_hover     = darkerColor( c_normal, .05 );
        var light       = lighterColor( c_normal, .3 );

        cp_submit.css('background', c_normal);
        //  Apply box shadow to submit button - If its set & equals to - 1
        var shadow = '';
        var radius = '';
        if( btn_shadow == 1 ) {
            shadow += 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
        }
        //  Add - border-radius
        if( btn_border_radius != '' ) {
            radius += 'border-radius: ' + btn_border_radius + 'px;';
        }

        jQuery('head').append('<div id="cp-temporary-inline-css"></div>');
        switch( btn_style ) {
            case 'cp-btn-flat':         jQuery('#cp-temporary-inline-css').html('<style>'
                                            + '.cp-modal .' + btn_style + '.cp-submit{ background: '+c_normal+'!important;' + shadow + radius + '; } '
                                            + '.cp-modal .' + btn_style + '.cp-submit:hover { background: '+c_hover+'!important; } '
                                            + '</style>');
                break;
            case 'cp-btn-3d':           jQuery('#cp-temporary-inline-css').html('<style>'
                                            + '.cp-modal .' + btn_style + '.cp-submit {background: '+c_normal+'!important; '+radius+' position: relative ; box-shadow: 0 6px ' + c_hover + ';} '
                                            + '.cp-modal .' + btn_style + '.cp-submit:hover {background: '+c_normal+'!important;top: 2px; box-shadow: 0 4px ' + c_hover + ';} '
                                            + '.cp-modal .' + btn_style + '.cp-submit:active {background: '+c_normal+'!important;top: 6px; box-shadow: 0 0px ' + c_hover + ';} '
                                            + '</style>');
                break;
            case 'cp-btn-outline':      jQuery('#cp-temporary-inline-css').html('<style>'
                                            + '.cp-modal .' + btn_style + '.cp-submit { background: transparent!important;border: 2px solid ' + c_normal + ';color: inherit ;' + shadow + radius + '}'
                                            + '.cp-modal .' + btn_style + '.cp-submit:hover { background: ' + c_hover + '!important;border: 2px solid ' + c_hover + ';color: ' + button_txt_hover_color + ' ;' + '}'
                                            + '.cp-modal .' + btn_style + '.cp-submit:hover span { color: inherit !important ; } '
                                            + '</style>');
                break;
            case 'cp-btn-gradient':     //  Apply box shadow to submit button - If its set & equals to - 1
                                        jQuery('#cp-temporary-inline-css').html('<style>'
                                            + '.cp-modal .' + btn_style + '.cp-submit {'
                                            + '     border: none ;'
                                            +       shadow + radius
                                            + '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
                                            + '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
                                            + '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
                                            + '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
                                            + '}'
                                            + '.cp-modal .' + btn_style + '.cp-submit:hover {'
                                            + '     background: ' + c_normal + ' !important;'
                                            + '}'
                                            + '</style>');
                break;
        }

        //  Set either 10% darken color for 'HOVER'
        //  Or 0.10% darken color for 'GRADIENT'
        jQuery('#smile_button_bg_hover_color', window.parent.document).val( c_hover );
        jQuery('#smile_button_bg_gradient_color', window.parent.document).val( light );

        //  Append CSS code
        jQuery('#cp-customizer-form-css').html( '<style>' + style + '</style>');
    }

    //function for social media design
    function cp_social_media( data ){
        var cp_social_icon_style            = data.cp_social_icon_style,
            cp_social_icon_shape            = data.cp_social_icon_shape,
            cp_social_icon_effect           = data.cp_social_icon_effect,
            cp_social_icon_column           = data.cp_social_icon_column,
            cp_social_enable_icon_color     = data.cp_social_enable_icon_color,
            icon_color                      = data.cp_social_icon_color,
            icon_bgcolor                    = data.cp_social_icon_bgcolor,
            icon_hover                      = data.cp_social_icon_hover,
            icon_bghover                    = data.cp_social_icon_bghover,
            cp_social_share_count           = data.cp_social_share_count,
            cp_display_nw_name              = data.cp_display_nw_name,
            cp_social_remove_icon_spacing   = data.cp_social_remove_icon_spacing,
            cp_social_icon                  = data.cp_social_icon,
            social_min_count                = data.social_min_count,
            cp_social_networks              = jQuery(".cp_social_networks"),
            cp_social_media_wrapper         = jQuery(".cp_social_media_wrapper"),
            social_icon_border              = data.social_icon_border,
            social_container_border         = data.social_container_border,
            cp_social_icon_hover_effect     = data.cp_social_icon_hover_effect,   
            cp_social_icon_align            = data.cp_social_icon_align,
            cp_social_text_hover_color      = data.cp_social_text_hover_color,
            cp_social_text_color            = data.cp_social_text_color,       
            social_html                     = '',
            cp_social_icon_column_class     = '',
            social_style                    = '',
            icon_style                      = '';

         var c_hover     = darkerColor( icon_bghover, .05 );
         var light       = darkerColor( icon_bgcolor, .05 );

       //console.log(cp_social_icon);
        //remove html structure of wrapper
         cp_social_media_wrapper.empty();

        //apply no of column to container
        if(cp_social_icon_column == 'auto'){
            cp_social_icon_column_class = 'autowidth';
        }else{
            cp_social_icon_column_class = 'col_'+cp_social_icon_column;
        }

        /**
         * Build HTML structure for Social_icon
         */

        social_html += '<div class="cp_social_networks cp_social_'+cp_social_icon_column_class+' cp_social_left cp_social_withcounts cp_social_withnetworknames '+ cp_social_icon_style +'">';

        social_html += ' <ul class="cp_social_icons_container">';

        var cp_fileds = cp_social_icon.split(";");
         //console.log(cp_fileds);
        var array = [];
        $.each( cp_fileds , function( index, val ) {
            var single = val.split("|");
             var ItemArray = [];
            $.each( single , function( i, v ) {
                var s = v.split(":");
                ItemArray[s[0]] = s[1];

            });
            array.push(ItemArray);
        });

        $.each( array , function( index, val ) {

            var input_type = val['input_type'].toLowerCase();
            var network_name = val['input_type'];
            var newnw = val['network_name'];
             if(newnw!==''){
                network_name = newnw;
             }
           
            social_html += '<li class="cp_social_'+input_type+'">'
                     + '<a href="javascript:void(0)" class="cp_social_share cp_social_display_count">'
                     + '<i class="cp_social_icon cp_social_icon_'+input_type+'"></i>';

            //display label
            if( cp_display_nw_name == 1 || cp_social_share_count == 1 ){
                social_html += '<div class="cp_social_network_label">';
            }
            //display network name
            if( cp_display_nw_name == 1 ){
               social_html += '<div class="cp_social_networkname">'+network_name+'</div>';
            }

            //display share count
            if( cp_social_share_count == 1 ){
                if(social_min_count !== ''){
                    social_html += '<div class="cp_social_count"><span>'+social_min_count+'</span></div>';
                }
            }
            //close label div
            if( cp_display_nw_name == 1 || cp_social_share_count == 1 ){
                social_html += '</div>';
            }

            //icon effect
            if(cp_social_icon_effect == 'gradient' ){
                social_html += '<div class="cp_social_overlay"></div>';
            }

            social_html += '</a>'
                        + ' </li>';
        });

        social_html += '</ul>';   /*--end of cp_social_icons_container --*/
        social_html += '</div>';/*--end of cp_social_networks--*/

        //append html to social media wrapper
        cp_social_media_wrapper.append( social_html );

        //  Model height
        if( typeof function_name == 'CPModelHeight' ) {
             CPModelHeight();
        }

        // Equalize blank style content vertically center
        if( typeof function_name == 'cp_row_equilize' ) {
            //setTimeout( function() {
                cp_row_equilize();
            //}, 300 );   
        }

        jQuery('#cp-social-icon-css').remove();

        //apply css
        jQuery('head').append('<div id="cp-social-icon-css"></div>');

        //to use user defined color for icon
        if(cp_social_enable_icon_color == 1){

             social_style += '.cp_social_networks li ,'
                          +'  .cp_social_networks.cp_social_simple li .cp_social_icon ,'
                          +'  .cp_social_networks.cp_social_circle li .cp_social_icon {'
                          +'    background:'+ icon_bgcolor
                          +' }'
                          +' .cp_social_networks li:hover {'
                          +'    background:'+ icon_bghover
                          +' }'
                          +'  .cp_social_networks li .cp_social_icon ,'
                          +'  .cp_social_networks.cp_social_simple li .cp_social_icon ,'
                          +'  .cp_social_networks.cp_social_circle li .cp_social_icon {'
                          +'     color:'+ icon_color
                          +' }'
                          +' .cp_social_networks li:hover .cp_social_icon{'
                          +'      color: '+icon_hover
                          +' }'
                          +' .cp_social_networks.cp_social_simple li:hover .cp_social_icon ,'
                          +' .cp_social_networks.cp_social_circle li:hover .cp_social_icon {'
                          +'    background:'+ icon_bghover+'!important'
                          +' }';

            if(cp_social_icon_effect == '3D'){
             social_style += '.cp_3D li,'
                          +'  .cp_social_networks.cp_social_simple.cp_3D li i ,'
                          +'  .cp_social_networks.cp_social_circle.cp_3D li i{'
                          +'    box-shadow: 0 4px '+light+'!important;'
                          +' }'
                          + '.cp_3D li:hover,'
                          +'  .cp_social_networks.cp_social_simple.cp_3D li:hover i ,'
                          +'  .cp_social_networks.cp_social_circle.cp_3D li:hover i {'
                          +'    box-shadow: 0 4px '+c_hover+'!important;'
                          +' }';
                 if( cp_social_icon_shape == 'square' && cp_social_icon_style == 'cp-icon-style-simple'){
                 social_style += '.cp_3D .cp_social_share {'
                          +'     padding: 5px;'
                          +' }'
                 }

            }
                 //if icon style==normal
            social_style +=  '.cp-icon-style-simple.cp-normal i,'
                          +'  .cp_social_networks.cp_social_simple.cp-icon-style-simple.cp-normal i {'
                          +'    color:'+icon_color+'!important;'
                          +'    background-color:transparent!important;'
                          +' }';
            social_style +=  '.cp-icon-style-simple.cp-normal li:hover i ,'
                          +'  .cp_social_networks.cp_social_simple.cp-icon-style-simple.cp-normal li:hover i {'
                          +'    color:'+icon_hover+'!important;'
                          +'    background-color:transparent!important;'
                          +' }';
            //text color
            social_style  += '.cp_social_networks .cp_social_network_label, '
                          +'  .cp_social_networks .cp_social_networkname,'
                          +'  .cp_social_networks .cp_social_count {'
                          +'     color: '+cp_social_text_color+'!important;'
                          +' }'; 
            social_style  += '.cp_social_networks li:hover .cp_social_network_label,'
                          +'  .cp_social_networks li:hover .cp_social_networkname,'
                          +'  .cp_social_networks li:hover .cp_social_count{'
                          +'     color: '+cp_social_text_hover_color+'!important;'
                          +' }';             

         }else{
            if( (cp_social_icon_effect == '3D' && cp_social_icon_shape == 'square') && (cp_social_icon_style == 'cp-icon-style-simple') ){
                    social_style += '.cp_3D .cp_social_share {'
                          +'     padding: 5px;'
                          +' }';
                 }
         }

         //if icon shape is custom
         if(cp_social_icon_shape == 'border_radius'){
            social_style += '.cp_social_networks.cp_social_left i.cp_social_icon {'
                          +'     border-radius: '+social_icon_border+'px;'
                          +' }';
         }


         //if apply border-radius to container
         if( cp_social_icon_style !== 'cp-icon-style-simple' && social_container_border !== '' ){
            social_style += '.cp_social_networks.cp_social_left li {'
                          +'     border-radius: '+social_container_border+'px;'
                          +' }';
         }
         //console.log(cp_social_icon_align);
          //apply no of column to container
        if( cp_social_icon_column == 'auto' ){
           
            social_style += ' .cp_social_networks .cp_social_icons_container {'
                          +'     margin-bottom: -15px!important;'
                          +' }';

             social_style += ' .cp_social_networks.cp_social_autowidth .cp_social_icons_container {'
                          +'     text-align: '+cp_social_icon_align+';'
                          +' }';

        }



        //  Set either 10% darken color for 'HOVER'
        //  Or 0.10% darken color for 'GRADIENT'
        jQuery('#smile_social_lighten', window.parent.document).val( light );
        jQuery('#smile_social_darken', window.parent.document).val( c_hover );

        //console.log(social_style);
        jQuery('#cp-social-icon-css').html('<style>'+ social_style +'</style>');

       //style class       
        var class_icon_hover_effect = '';
        if( cp_social_icon_hover_effect == 'slide'){
            switch( cp_social_icon_style ) {
                case 'cp-icon-style-simple': 
                            class_icon_hover_effect = 'cp_social_slide';
                    break;

                case 'cp-icon-style-rectangle': 
                            class_icon_hover_effect = 'cp_social_slide';
                    break;

                case 'cp-icon-style-right': 
                            class_icon_hover_effect = 'cp_social_flip';
                    break;

                case 'cp-icon-style-left': 
                            class_icon_hover_effect = 'cp_social_flip';
                    break;
            }
        }
      
        if(cp_social_icon_style == 'cp-icon-style-simple' ){           
           jQuery(".cp_social_networks").addClass('cp_social_simple');
           jQuery(".cp_social_networks").addClass( class_icon_hover_effect );
           jQuery(".cp_social_networks").removeClass('cp_social_flip');
        }else { 
            if(cp_social_icon_style == 'cp-icon-style-rectangle' ){  
                jQuery(".cp_social_networks").addClass( class_icon_hover_effect );
                jQuery(".cp_social_networks").removeClass('cp_social_flip');
            }else{
                jQuery(".cp_social_networks").addClass( class_icon_hover_effect )
                jQuery(".cp_social_networks").removeClass('cp_social_slide ');
            }
            jQuery(".cp_social_networks").removeClass('cp_social_simple');
        }



        //spacing
        if(cp_social_remove_icon_spacing == 1){
            jQuery(".cp_social_networks").addClass('cp-no-spacing');
        }else{
            jQuery(".cp_social_networks").removeClass('cp-no-spacing');
        }

        //manage spacing when column is 1
        jQuery('#cp-social-icon-space-css').remove();

        //apply css if column width = 1; remove margin for li
        jQuery('head').append('<div id="cp-social-icon-space-css"></div>');
        var icon_space_style ='';
        if(cp_social_icon_column == '1' ){
            icon_space_style += '.cp-modal-body ol, .cp-modal-body ol li, .cp-modal-body ul, .cp-modal-body ul li {margin: 2% 0 0 0;}';
         }
        jQuery('#cp-social-icon-space-css').html( '<style>' + icon_space_style + '</style>');

        //remove class
        var classList = ['cp-circle', 'cp-square', 'cp-border_radius','cp-normal'];
        jQuery.each(classList, function(i, v){
           jQuery(".cp_social_networks").removeClass(v);
        });
        jQuery(".cp_social_networks").addClass( 'cp-'+cp_social_icon_shape );


        //  Remove all classes for icon effect
        var effectList = ['cp-flat', 'cp-3d', 'cp-gradient'];
        jQuery.each(effectList, function(i, v){
           jQuery(".cp_social_networks").removeClass(v);
        });
        jQuery(".cp_social_networks").addClass('cp_'+cp_social_icon_effect );

         //if count and nw name is not present
        var no_count ='';       
        if( cp_social_icon_style == 'cp-icon-style-rectangle' && cp_social_icon_effect =='gradient' && cp_display_nw_name !== '1' && cp_social_share_count !== '1' ){
            jQuery(".cp_social_networks").addClass('cp-no-count-no-share');
        }else{
            jQuery(".cp_social_networks").removeClass('cp-no-count-no-share');
        }

    }

//function for start counter
function start_count_timer(data){

    var date_time_picker    = data.date_time_picker,
     defaultCountdown       = jQuery('#cp_defaultCountdown'),
     counter_bg_color       = data.counter_bg_color,
     digit_text_color       = data.counter_digit_text_color,
     timer_text_color       = data.counter_timer_text_color,
     digit_border_color     = data.counter_digit_border_color,
     digit_text_size        = data.counter_digit_text_size,
     timer_text_size        = data.counter_timer_text_size,
     counter_font           = data.counter_font,
     counter_option         = data.counter_option,
     cp_gmt_offset          = data.cp_gmt_offset,
     cp_counter_timezone    = data.cp_counter_timezone,
     disable_datepicker     = data.disable_datepicker,
     datepicker_advance_option = data.datepicker_advance_option,
     cp_countdown_amount    = jQuery(".cp_countdown-amount"),
     countupto              = "",
     counter_timer          = "",
     counter_main           = "",
     format                 = "",
     layoutopt              = "",
     layers                 = "",
     counter_digit          = "",
     labelsname             = ['Year','Month','Weeks','Days','Hours','Minutes','Seconds'] ,
     layouutformat          = "",
     vw                     = jQuery(window).width();

    if ( counter_option.length > 0 ) {
     counter_option = counter_option.split("|");
     jQuery.each(counter_option, function(i,v){
          format += v; 
      });
    } else{     
        format = "YOWDHMS";
    }

    if(counter_font == '' && counter_font =='undefined'){
        counter_font ='inherit';
    }

    for (var i = 0, len = format.length; i < len; i++) {
        var  lower = format[i].toLowerCase();
        layouutformat += '{'+lower+'n}';
        
        if(i+1!== len){         
            layouutformat += ' {'+lower+'l}, '; 
        }else{
           layouutformat += ' {'+lower+'l}';
        }
    }

    
    if(disable_datepicker != '1'){
        defaultCountdown.cp_countdown('destroy');
        defaultCountdown.hide();
    }else{
        defaultCountdown.show();
    }


    if(datepicker_advance_option !== 'style_2'){  
        layoutopt = layouutformat ;
    }else{
        var lt = format.length ;
        //if counter digit greater than 4 then compress labels
        if(vw <=610 && lt >=4 ){
            labelsname = ['Y','M','W','D','H','Mn','S'];
        }
    }

    defaultCountdown.cp_countdown('destroy');
    countupto = new Date(date_time_picker); 

    //timezone
    if(cp_counter_timezone=='wordpress'){
            defaultCountdown.cp_countdown({until: countupto , format: format , timezone: cp_gmt_offset , layout: layoutopt , labels:labelsname});
    }else{
        defaultCountdown.cp_countdown({until: countupto , format: format , layout: layoutopt, labels:labelsname});
    }
    
    jQuery('#cp-count-timer-css').remove();
    var count_timer_css ='';
    //apply css
    jQuery('head').append('<div id="cp-count-timer-css"></div>');

    switch( datepicker_advance_option ) {
        case 'style_1': 
                        counter_digit = '';
                        counter_digit += 'background: transparent;';
                        counter_digit += 'color: ' + digit_text_color + ';';
                        counter_digit += 'font-family:' + counter_font + ';';
                        counter_digit += 'font-size:' + digit_text_size +'px!important;';

                        count_timer_css  += '#cp_defaultCountdown{'+ counter_digit +'; } '; 
            break;

         case 'style_2': 
                        counter_digit = '';
                        counter_digit += 'background:' + counter_bg_color + ';';
                        counter_digit += 'color:' + digit_text_color + ';';
                        counter_digit += 'font-family:' + counter_font + ';';
                        counter_digit += 'font-size:' + digit_text_size +'px;';
                        counter_digit  += 'border-color: ' + digit_border_color + ';';

                        //timer text css
                        counter_timer += 'color: ' + timer_text_color + ';';
                        counter_timer += 'font-size: ' + timer_text_size + 'px;';
                        counter_timer += 'font-family: ' + counter_font + ';';

                        count_timer_css  += '#cp_defaultCountdown  .cp_countdown-amount {  '+ counter_digit +'; } '
                                      + '#cp_defaultCountdown  .cp_countdown-period { '+ counter_timer +'; } '  ;                        
            break;    

    }

          //console.log(social_style);
        jQuery('#cp-count-timer-css').html('<style>'+ count_timer_css +'</style>');

}

    /**
     * Initialize CKEditor for submit button
     */
    jQuery(document).ready(function($) {       

        //  Highlight MultiField
        jQuery("body").on("click", ".cp-form-field", function(e){ parent.setFocusElement('form_fields'); e.stopPropagation(); });

        //  Add div for from CSS
        jQuery('head').append('<div id="cp-customizer-form-css"></div>');

        //  1. Initialize CKEditor for submit button
        if( jQuery("#cp_button_editor").length ) {

            // Turn off automatic editor creation first.
            CKEDITOR.disableAutoInline = true;
            CKEDITOR.inline( 'cp_button_editor' );
            CKEDITOR.instances.cp_button_editor.config.toolbar = 'Small';

            //  1+ Add class 'cp-no-responsive' to manage the line height of cp-highlight
            CKEDITOR.instances.cp_button_editor.on('instanceReady',function(){
                var data = CKEDITOR.instances.cp_button_editor.getData();
            });

            CKEDITOR.instances.cp_button_editor.on( 'change', function() {
                var data = CKEDITOR.instances.cp_button_editor.getData();
                parent.updateHTML(data,'smile_button_title');
            } );

            // Use below code to 'reinitialize' CKEditor
            // IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
            CKEDITOR.instances.cp_button_editor.on( 'instanceReady', function( ev ) {
                var editor = ev.editor;
                    editor.setReadOnly( false );
            } );

        }

        jQuery("body").on("click", ".cp_social_media_wrapper", function(e){ parent.setFocusElement('cp_social_icon'); e.stopPropagation(); });


    });


    /**
     *  Customizer
     *
     * Use this trigger for Store & Retrieve the LIVE changes from customizer
     */
    jQuery(document).on('smile_data_received',function(e,data){

        var cp_submit           = jQuery(".cp-submit");
        var button_title        = data.button_title,
            fields              = data.form_fields,
            cp_social_icon      = data.cp_social_icon,
            disable_datepicker = data.disable_datepicker;

        // Update Submit button HTML
        button_title = htmlEntities(button_title);

        cp_submit.html(button_title);
        if( button_title !== "" && typeof button_title !== "undefined" && jQuery("#cp_button_editor").length ){
            CKEDITOR.instances.cp_button_editor.setData(button_title);
        }

        if( typeof cp_social_icon !=='undefined' && cp_social_icon!=='' ){

            //social media style
            cp_social_media( data );

        }


        if( typeof fields != 'undefined' && fields != null ) {

            // [CALL] - CP Form
            design_form( data );

            // [CALL] - Append CSS
            append_form_css(data);

        }

       
      if( typeof disable_datepicker !=='undefined' && disable_datepicker !== null ){
            //count down             
            start_count_timer( data );
       }


    });

})(jQuery);