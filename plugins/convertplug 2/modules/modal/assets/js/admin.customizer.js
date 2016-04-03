jQuery(document).ready(function() {

	//	Added the class '.cp-customizer' to <html> to override the themes CSS issues in customizer
	//	Also added class '.cp-customizer-modal' for this individual module.
	//	Also added class '.cp-THEME_NAME' to provide the theme compatibility 
	jQuery('html').addClass('cp-customizer cp-customizer-modal cp-' + cp_active_theme.slug );

});

/**		Google Fonts
 *
 *	Add Selected - Google Fonts to CKEditor
 *-------------------------------------------------------------------*/
function cp_append_gfonts(Fonts) {
	jQuery('head').append('<link id="cp-google-fonts" rel="stylesheet" href="https://fonts.googleapis.com/css?family='+Fonts+'" type="text/css" media="all">');
}
function cp_append_to_ckeditor(CKFonts) {
	if( typeof CKFonts != 'undefined' && CKFonts != null && CKFonts != '') {		
		CKEDITOR.config.font_names = CKFonts;
	}
}
function cp_get_gfonts(GFonts) {

	var Fonts = CKFonts = '';

	if(typeof GFonts != 'undefined' && GFonts != null && GFonts != '' ) {

		//	for multiple fonts
		if(GFonts.indexOf(',') >= 0) {

			var basicFonts = [ "Arial",
					"Arial Black",			
					"Comic Sans MS",
					"Courier New",
					"Georgia",
					"Impact",
					"Lucida Sans Unicode",
					"Palatino Linotype",					
					"Tahoma",
					"Times New Roman",
					"Trebuchet MS",			
					"Verdana"
			];

			//	Extract Added Google Fonts
			var pairs = GFonts.split(',');
			pairs.forEach(function(pair) {
				if( typeof pair != 'undefined' && pair != null && pair != '') {
					if( jQuery.inArray( pair , basicFonts ) < 0 ) {
						Fonts += pair.replace(' ','+') + '|';	
					}
					CKFonts += pair+';';
				}
			});

			//	append google fonts
			cp_append_gfonts(Fonts);

			//	Append selected google fonts to - CKEditor
			cp_append_to_ckeditor(CKFonts);

		} else {
		
			//	for single font
			Fonts += GFonts.replace(' ','+') + '|';
			CKFonts += GFonts+';';

			//	append google fonts
			cp_append_gfonts(Fonts);

			//	Append selected google fonts to - CKEditor
			cp_append_to_ckeditor(CKFonts);
		}
	}
}

function htmlEntities(str) {
    return String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
}
function generateBorderCss(string){
	var pairs = string.split('|');

	var result = {};
	pairs.forEach(function(pair) {			
		pair = pair.split(':');
		result[pair[0]] = decodeURIComponent(pair[1]);
	});
	
	var cssCode1 = '', cssCode2 = '';
    cssCode1 += result.br_tl + 'px ' + result.br_tr + 'px ' + result.br_br + 'px ';
    cssCode1 += result.br_bl + 'px';	
	var text = '';
	text += 'border-radius: ' + cssCode1 +';';
    text += '-moz-border-radius: ' + cssCode1+';';
    text += '-webkit-border-radius: ' + cssCode1+';';
    text += 'border-style: ' + result.style +';';
    text += 'border-color: ' + result.color +';';
    text += 'border-top-width:'+ result.bw_t +'px;';
    text += 'border-left-width:'+ result.bw_l +'px;';
    text += 'border-right-width:'+ result.bw_r +'px;';
    text += 'border-bottom-width:'+ result.bw_b +'px;';
	
	return text;
}

function generateBoxShadow(string){
	var pairs = string.split('|');
	var result = {};
	pairs.forEach(function(pair) {			
		pair = pair.split(':');
		result[pair[0]] = decodeURIComponent(pair[1]);
	});
	
    res = '';
    if (result.type !== 'outset')
        res += result.type + ' ';
	
    res += result.horizontal + 'px ';
    res += result.vertical + 'px ';
    res += result.blur + 'px ';
    res += result.spread + 'px ';
    res += result.color;
	
	var style = 'box-shadow:'+res;
	
	if (result.type == 'none')
		style = '';
	
    return style+";";
}

