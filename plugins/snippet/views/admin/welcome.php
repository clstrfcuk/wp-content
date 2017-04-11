<?php

// =============================================================================
// VIEWS/ADMIN/WELCOME.PHP
// -----------------------------------------------------------------------------
// Welcome page
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Welcome
// =============================================================================

// Welcome
// =============================================================================

?>

<div class="wrap x-plugin <?php echo $plugin_slug; ?>" id="<?php echo $plugin_slug; ?>-wrap">
  <?php $view->show( 'admin/navigation' ); ?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">

      <?php $view->show( 'admin/required-fields-alert' ); ?>

      <div id="post-body-content">
          <div class="meta-box-sortables ui-sortable">
              <div id="meta-box-settings" class="postbox">
                <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
                <h3 class="hndle"><span><?php _e( 'Howdy!', '__x__' ); ?></span></h3>
                <div class="inside">

                  <h3><?php _e('Welcome to the Snippet Plugin!') ?></h3>

                  <p><?php _e('You’ll need to fill out some basic information to start generating data. Review the below, setup the plugin and you’ll be good to go! ') ?>:</p>

                  <div class="tco-box-content">
                    <ul class="tco-box-features">
                      <li>
                        <svg class="tco-box-feature-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo plugins_url('snippet'); ?>/img/admin/icons.svg#cog"></use></svg>
                        <div class="tco-box-feature-info">
                          <h4 class="tco-box-content-title"><?php _e('Global Settings') ?></h4>
                          <span class="tco-box-content-text"><?php _e('Define what Schema type will be the default for each post type or disable Schema completely for certain post types.') ?></span>
                        </div>
                      </li>
                      <li>
                        <svg class="tco-box-feature-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo plugins_url('snippet'); ?>/img/admin/icons.svg#lightbulb"></use></svg>
                        <div class="tco-box-feature-info">
                          <h4 class="tco-box-content-title"><?php _e('Website') ?></h4>
                          <span class="tco-box-content-text"><?php _e('Define basic info for your website here. This is where you’ll set up most of the settings and will effect the appearance of your site on search engines.') ?></span>
                        </div>
                      </li>
                      <li>
                        <svg class="tco-box-feature-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo plugins_url('snippet'); ?>/img/admin/icons.svg#lightbulb"></use></svg>
                        <div class="tco-box-feature-info">
                          <h4 class="tco-box-content-title"><?php _e('Organization') ?></h4>
                          <span class="tco-box-content-text"><?php _e('Do you want to have your company information appear in one of those fancy Google side boxes? Fill in the required info here and don’t forget to add a logo!') ?></span>
                        </div>
                      </li>
                      <li>
                        <svg class="tco-box-feature-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo plugins_url('snippet'); ?>/img/admin/icons.svg#lightbulb"></use></svg>
                        <div class="tco-box-feature-info">
                          <h4 class="tco-box-content-title"><?php _e('Address') ?></h4>
                          <span class="tco-box-content-text"><?php _e('Want a map within the side box? Fill in the address and add the co-ordinates of your business with latitude and longitude.') ?></span>
                        </div>
                      </li>
                      <li>
                        <svg class="tco-box-feature-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo plugins_url('snippet'); ?>/img/admin/icons.svg#gift"></use></svg>
                        <div class="tco-box-feature-info">
                          <h4 class="tco-box-content-title"><?php _e('Contacts') ?></h4>
                          <span class="tco-box-content-text"><?php _e('Make it easy for your customer to contact you by adding multiple contact types.') ?></span>
                        </div>
                      </li>
                      <li>
                        <svg class="tco-box-feature-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo plugins_url('snippet'); ?>/img/admin/icons.svg#gift"></use></svg>
                        <div class="tco-box-feature-info">
                          <h4 class="tco-box-content-title"><?php _e('Social Links') ?></h4>
                          <span class="tco-box-content-text"><?php _e('Add Social Profile links so your customers can follow you anywhere.') ?></span>
                        </div>
                      </li>
                    </ul>
                    <div class="tco-btn-group-horizontal">
                      <a class="tco-btn" href="https://community.theme.co/kb/" target="_blank">Knowledge Base</a><a class="tco-btn" href="https://community.theme.co/forums/group/support-center/" target="_blank">Forum</a>
                    </div>
                  </div>

                </div>
              </div>
          </div>
      </div>

      <?php $view->show( 'admin/tab-settings-sidebar' ); ?>

    </div>
    <br class="clear">
  </div>
</div>
