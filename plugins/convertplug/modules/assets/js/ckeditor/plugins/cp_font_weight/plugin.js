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

     //                var parent = selection.parent();
					// console.log('parent: ' + parent );

					/*if (el && parent.hasClass("cp_responsive")) {
						console.log('true');
					   //el.remove(true);
					   //parent.remove(true);
					} else {
						console.log('false');
					    // var save = selection.getNative();
					    // var element = CKEDITOR.dom.element.createFromHtml( '<span class="ymarker" style="background: url(&quot;.../images/ymarker_right_corner.gif&quot;) no-repeat scroll right center transparent; height: 12px; padding-right: 6px; position: relative; left: -8px; margin-right: -12px;"><span style="background: url(&quot;.../images/ymarker_left_corner.gif&quot;) no-repeat scroll 0px 6px transparent; padding: 0pt 0pt 0pt 8px;">' + save + '</span></span>' );
					    // editor.insertElement(element);
					}*/
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

// CKEDITOR.plugins.add('cp_font_weight', {    
//     requires: ['dialog'],
// 	lang : ['en'], 
//     init:function(a) {
// 		var b="cp_font_weight";
// 		var c=a.addCommand(b,new CKEDITOR.dialogCommand(b));
// 			c.modes={wysiwyg:1,source:0};
// 			c.canUndo=false;
		
// 		a.ui.addButton("cp_font_weight",{
// 			label:'CP Fonts',
// 			command:b,
// 			icon:this.path+"cp_font_weight.gif"
// 		});
// 		CKEDITOR.dialog.add(b,this.path+"dialogs/cp_font_weight.js")
// 	},
// 	exec:function(editor){
// 		var selection = editor.getSelection();
// 		var el = selection.getStartElement();
// 		var parent = el.getParent();
// 		var text = parent.getText();
// 		if (el && parent.hasClass("cp_responsive")) {
// 		   //el.remove(true);
// 		   //parent.remove(true);
// 		} else {
// 		    // var save = selection.getNative();
// 		    // var element = CKEDITOR.dom.element.createFromHtml( '<span class="ymarker" style="background: url(&quot;.../images/ymarker_right_corner.gif&quot;) no-repeat scroll right center transparent; height: 12px; padding-right: 6px; position: relative; left: -8px; margin-right: -12px;"><span style="background: url(&quot;.../images/ymarker_left_corner.gif&quot;) no-repeat scroll 0px 6px transparent; padding: 0pt 0pt 0pt 8px;">' + save + '</span></span>' );
// 		    // editor.insertElement(element);
// 		}
//   	}
// });