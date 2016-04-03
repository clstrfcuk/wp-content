( function() {
  CKEDITOR.plugins.add( 'cp_responsive', { 
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
      editor.ui.addButton( 'cp_responsive', { 
        label: 'Insert QnA', 
        command: 'insertQnA',
        icon: this.path + 'images/question.png'
      }); 
    } 
  });
})();