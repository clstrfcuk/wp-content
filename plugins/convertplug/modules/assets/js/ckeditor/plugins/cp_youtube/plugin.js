/**
 * @file Plugin for inserting Drupal embeded media
 */
( function($) {
 
  // All CKEditor plugins are created by using the CKEDITOR.plugins.add function
  // The plugin name here needs to be the same as in hook_ckeditor_plugin()
  // or hook_wysiwyg_plugin()
  CKEDITOR.plugins.add( 'cp_youtube',
  {
    // the init() function is called upon the initialization of the editor instance
    init: function (editor) {
 
      // Register the dialog. The actual dialog definition is below.
      CKEDITOR.dialog.add('youtubeDialog', ytDialogDefinition);
 
      // Now that CKEditor knows about our dialog, we can create a
      // command that will open it
      editor.addCommand('youtubeDialogCmd', new CKEDITOR.dialogCommand( 'youtubeDialog' ));
 
      // Finally we can assign the command to a new button that we'll call youtube
      // Don't forget, the button needs to be assigned to the toolbar. Note that
      // we're CamelCasing the button name (YouTube). This is just because other
      // CKEditor buttons are done this way (JustifyLeft, NumberedList etc.)
      editor.ui.addButton( 'YouTube',
        {
          label : 'You Tube',
          command : 'youtubeDialogCmd',
          icon: this.path + 'images/icon.gif'
        }
      );
 
    }
  });
 
  /*
    Our dialog definition. Here, we define which fields we want, we add buttons
    to the dialog, and supply a "submit" handler to process the user input
    and output our youtube iframe to the editor text area.
  */
  var ytDialogDefinition = function (editor) {
 
    var dialogDefinition =
    {
      title : 'YouTube Embed',
      minWidth : 390,
      minHeight : 130,
      contents : [
        {
          // To make things simple, we're just going to have one tab
          id : 'tab1',
          label : 'Settings',
          title : 'Settings',
          expand : true,
          padding : 0,
          elements :
          [
            {
              // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.definition.vbox.html
              type: 'vbox',
              widths : [ null, null ],
              styles : [ 'vertical-align:top' ],
              padding: '5px',
              children: [
                {
                  // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.definition.html.html
                  type : 'html',
                  padding: '5px',
                  html : 'You can find the youtube video id in the url of the video. <br/> e.g. http://www.youtube.com/watch?v=<strong>VIDEO_ID</strong>.'
                },
                {
                  // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.definition.textInput.html
                  type : 'text',
                  id : 'txtVideoId',
                  label: 'YouTube Video ID',
                  style: 'margin-top:5px;',
                  'default': '',
                  validate: function() {
                    // Just a little light validation
                    // 'this' is now a CKEDITOR.ui.dialog.textInput object which
                    // is an extension of a CKEDITOR.ui.dialog.uiElement object
                    var value = this.getValue();
                    value = value.replace(/http:.*youtube.*?v=/, '');
                    this.setValue(value);
                  },
                  // The commit function gets called for each form element
                  // when the dialog's commitContent Function is called.
                  // For our dialog, commitContent is called when the user
                  // Clicks the "OK" button which is defined a little further down
                  commit: commitValue
                },
              ]
            },
            {
              // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.definition.hbox.html
              type: 'hbox',
              widths : [ null, null ],
              styles : [ 'vertical-align:top' ],
              padding: '5px',
              children: [
                {
                  type : 'text',
                  id : 'txtWidth',
                  label: 'Width',
                  // We need to quote the default property since it is a reserved word
                  // in javascript
                  'default': 500,
                  validate : function() {
                    var pass = true,
                      value = this.getValue();
                    pass = pass && CKEDITOR.dialog.validate.integer()( value )
                      && value > 0;
                    if ( !pass )
                    {
                      alert( "Invalid Width" );
                      this.select();
                    }
                    return pass;
                  },
                  commit: commitValue
                },
                {
                  type : 'text',
                  id : 'txtHeight',
                  label: 'Height',
                  'default': 300,
                  validate : function() {
                    var pass = true,
                      value = this.getValue();
                    pass = pass && CKEDITOR.dialog.validate.integer()( value )
                      && value > 0;
                    if ( !pass )
                    {
                      alert( "Invalid Height" );
                      this.select();
                    }
                    return pass;
                  },
                  commit: commitValue
                },
                {
                  // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.definition.checkbox.html
                  type : 'checkbox',
                  id : 'chkAutoplay',
                  label: 'Autoplay',
                  commit: commitValue
                }
              ]
            }
          ]
        }
      ],
 
      // Add the standard OK and Cancel Buttons
      buttons : [ CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton ],
 
      // A "submit" handler for when the OK button is clicked.
      onOk : function() {
 
        // A container for our field data
        var data = {};
 
        // Commit the field data to our data object
        // This function calls the commit function of each field element
        // Each field has a commit function (that we define below) that will
        // dump it's value into the data object
        this.commitContent( data );
 
        if (data.info) {
          var info = data.info;
          // Set the autoplay flag
          var autoplay = info.chkAutoplay ? 'autoplay=1': 'autoplay=0';
          // Concatenate our youtube embed url for the iframe
          var src = 'http://youtube.com/embed/' + info.txtVideoId + '?' + autoplay;
          // Create the iframe element
          var iframe = new CKEDITOR.dom.element( 'iframe' );
          // Add the attributes to the iframe.
          iframe.setAttributes({
            'width': info.txtWidth,
            'height': info.txtHeight,
            'type': 'text/html',
            'src': src,
            'frameborder': 0
          });
          // Finally insert the element into the editor.
          editor.insertElement(iframe);
        }
 
      }
    };
 
    return dialogDefinition;
  };
 
  // Little helper function to commit field data to an object that is passed in:
  var commitValue = function( data ) {
    var id = this.id;
    if ( !data.info )
      data.info = {};
    data.info[id] = this.getValue();
  };
 
 
})(jQuery);