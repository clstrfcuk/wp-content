// =============================================================================
// JS/SRC/ADMIN/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
// 01. Plugin Specific Functionality
// 02. Global Plugin Functionality
// 03. Media Uploader
// =============================================================================

// Plugin Specific Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Show/hide settings.
  //

  var $pluginEnable      = $('#x_under_construction_enable');
  var $pluginSettings    = $('#meta-box-settings');
  var $customSettings    = $('#meta-box-custom-settings');
  var $whitelistSettings = $('#meta-box-whitelist-settings');
  var $socialSettings    = $('#meta-box-social-settings');
  var $useCustom         = $('#x_under_construction_use_custom');
  var $pageCustom        = $('#x_under_construction_custom_row');

  function toggleSettings() {
    if ( $pluginEnable.is(':checked') ) {
      $pluginSettings.show();
      $customSettings.show();
      $whitelistSettings.show();
      $socialSettings.show();
    } else {
      $pluginSettings.hide();
      $customSettings.hide();
      $whitelistSettings.hide();
      $socialSettings.hide();
    }
  }

  function toggleCustomSettings() {
    if ( $useCustom.is(':checked') ) {
      $pluginSettings.hide();
      $pageCustom.show();
    } else {
      $pluginSettings.show();
      $pageCustom.hide();
    }
  }

  $pluginEnable.change(function() {
    toggleSettings();
  });

  $useCustom.change(function() {
    toggleCustomSettings();
  });

  toggleSettings();
  toggleCustomSettings();
});



// Global Plugin Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Accordion.
  //

  $('.accordion > .toggle').click(function() {
    var $this = $(this);
    if ( $this.hasClass('active') ) {
      $this.removeClass('active').next().slideUp();
    } else {
      $('.accordion > .panel').slideUp();
      $this.siblings().removeClass('active');
      $this.addClass('active').next().slideDown();
      return false;
    }
  });


  //
  // Save button.
  //

  $('#submit').click(function() {
    $(this).addClass('saving').val('Updating');
  });


  //
  // Color picker.
  //

  $('.wp-color-picker').wpColorPicker();


  //
  // Datepicker.
  //

  $('.datepicker').datepicker( {
	  dateFormat: 'yy-mm-dd'
  });


  //
  // Meta box toggle.
  //

  postboxes.add_postbox_toggles(pagenow);

});


// Media Uploader
// =============================================================================

jQuery(document).ready(function($) {
  $('.x-upload-btn-uc').click( function( e ) {
      var self = $(this);
      e.preventDefault();
      var image = wp.media({
        title: 'Upload Image',
        multiple: false
      }).open()
      .on('select', function( e ) {
          var uploaded_image = image.state().get( 'selection' ).first();
          var image_url = uploaded_image.toJSON().url;
          $('#' + self.data('id') ).val(image_url);
          console.log('#_' + self.data('id') + '_thumb')
          $('#_' + self.data('id') + '_thumb' ).html('<div class="x-uploader-image"><img src="' + image_url + '" alt="" /></div>');
      });
  });
});
