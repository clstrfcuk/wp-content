/*!
 * Justified Gallery / Envira Extensions and Overrides - v3.6.2
 * Copyright (c) 2016 David Bisset, Benjamin Rojas
 * Licensed under the MIT license.
 */

(function ($) {

	var justifiedGallery =  $.fn.justifiedGallery,
		EnviraJustifiedGallery = {};

	$.fn.enviraJustifiedGallery = function () {

		var obj = justifiedGallery.apply(this, arguments);
			EnviraJustifiedGallery = obj.data('jg.controller');

			if (EnviraJustifiedGallery !== undefined) {

				EnviraJustifiedGallery.displayEntryCaption = function ($entry) {

				var $image = this.imgFromEntry($entry);

				if ($image !== null && this.settings.captions) {

					var $imgCaption = this.captionFromEntry($entry);

					// Create it if it doesn't exists
					if ($imgCaption === null) {

						var caption = $image.data('caption'),
							revised_caption = '';

						if ( caption !== undefined && typeof caption === 'string' ) {

							caption = caption.replace('<', '&lt;');
							revised_caption = $('<textarea />').html(caption).text();

						}

						if ( revised_caption !== undefined ) {
							if (this.isValidCaption(revised_caption)) { // Create only we found something
								$imgCaption = $('<div class="caption">' + revised_caption + '</div>');
								$image.after($imgCaption);
								$entry.data('jg.createdCaption', true);
							}
						}
		            }

		  // Create events (we check again the $imgCaption because it can be still inexistent)
		  if ($imgCaption !== null) {
			if (!this.settings.cssAnimation) $imgCaption.stop().fadeTo(0, this.settings.captionSettings.nonVisibleOpacity);
			// Adjust the positioning of overlay buttons so that it doesn't overlap the caption
			var imgCaptionHeight = $imgCaption.css('height');
			$entry.find('.envira-gallery-position-overlay.envira-gallery-bottom-left').css('bottom', imgCaptionHeight );
			$entry.find('.envira-gallery-position-overlay.envira-gallery-bottom-right').css('bottom', imgCaptionHeight );
			this.addCaptionEventsHandlers($entry);
		  }
		} else {
		  this.removeCaptionEventsHandlers($entry);
		}
	};

	return EnviraJustifiedGallery;

	}

  };
})(jQuery);