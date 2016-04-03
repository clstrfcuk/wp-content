/*********************************************************************************************************/
/**
 * cp_font_weight plugin for CKEditor
 * version:	 1.0
 * Released: On 2015
 */
/*********************************************************************************************************/
( function() {
    CKEDITOR.plugins.add( 'cp_font_weight', { 
        init: function( editor ) {
            editor.addCommand( 'insertQnA', { 
                exec : function( editor ) {    
                    if(CKEDITOR.env.ie) {
                        editor.getSelection().unlock(true); 
                            var selection = editor.getSelection().getNative().createRange().text; 
                    } else { 
                        var selection = editor.getSelection().getNative();
                    }
                    editor.insertHtml( selection );
                } 
            }); 
            editor.ui.addButton( 'cp_font_weight', { 
                label: 'Insert QnA', 
                command: 'insertQnA', 
                icon: this.path + 'cp_font_weight.gif'
            }); 
        } 
    });
})();