<?php
  $is_cp_status = ( function_exists( "bsf_product_status" ) ) ? bsf_product_status('14058953') : '';
  $reg_menu_hide = ( (defined( 'BSF_UNREG_MENU' ) && ( BSF_UNREG_MENU === true || BSF_UNREG_MENU === 'true' )) ||
  (defined( 'BSF_REMOVE_14058953_FROM_REGISTRATION' ) && ( BSF_REMOVE_14058953_FROM_REGISTRATION === true || BSF_REMOVE_14058953_FROM_REGISTRATION === 'true' )) ) ? true : false;
  if($reg_menu_hide !== true) {
    if($is_cp_status)
      $reg_menu_hide = true;
  }
?>
<style type="text/css">
.about-cp .wp-badge:before {
  content: "\e600";
  font-family: 'ConvertPlug';
  speak: none;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-size: 72px;
  top: calc( 50% - 54px );
  position: absolute;
  left: calc( 50% - 33px );
  color: #FFF;
}
</style>
<div class="wrap about-wrap about-cp bend">
  <div class="wrap-container">
    <div class="bend-heading-section cp-about-header">
      <h1><?php _e( "ConvertPlug &mdash; Settings", "smile" ); ?></h1>
      <h3><?php _e( "Below are some global settings that are applied to the elements designed with ConvertPlug. If you're just getting started, you probably don't need to do anything here right now.
", "smile" ); ?></h3>
      <div class="bend-head-logo">
        <div class="bend-product-ver">
          <?php _e( "Version", "smile" ); echo ' '.CP_VERSION; ?>
        </div>
      </div>
    </div><!-- bend-heading section -->
    <div class="msg"></div>
    <div class="bend-content-wrap smile-settings-wrapper">
      <h2 class="nav-tab-wrapper">
        <a class="nav-tab" href="?page=convertplug" title="<?php _e( "About", "smile"); ?>"><?php echo __("About", "smile" ); ?></a>
        <a class="nav-tab" href="?page=convertplug&view=modules" title="<?php _e( "Modules", "smile" ); ?>"><?php echo __( "Modules", "smile" ); ?></a>
		    <a class="nav-tab nav-tab-active" href="?page=convertplug&view=settings" title="<?php _e( "Settings", "smile" ); ?>"><?php echo __("Settings", "smile" ); ?></a>
        <?php if($reg_menu_hide !== true) : ?>
        <a class="nav-tab" href="?page=convertplug&view=registration" title="<?php _e( "Registration", "smile"); ?>"><?php echo __("Registration", "smile" ); ?></a>
        <?php endif; ?>
        <?php if( isset( $_GET['author'] ) ){ ?>
        <a class="nav-tab" href="?page=convertplug&view=debug&author=true" title="<?php _e( "Debug", "smile" ); ?>"><?php echo __( "Debug", "smile" ); ?></a>
        <?php } ?>
      </h2>
    <div id="smile-settings">
      <div class="container cp-started-content">
        <form id="convert_plug_settings" class="cp-options-list">
            <input type="hidden" name="action" value="smile_update_settings" />

            <!-- Google Fonts -->
            <?php
              $data         =  get_option( 'convert_plug_settings' );
              $gfval        = isset($data['cp-google-fonts']) ? $data['cp-google-fonts'] : 1;
              $is_checked   = ( $gfval ) ? ' checked="checked" ' : '';
              $uniq         =  uniqid();
            ?>
            <p>
              <label for="hide-options" style="width:320px; display: inline-block;"><strong><?php _e( "Google Fonts", "smile" ); ?></strong>
                <span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php _e( "Load Google Fonts at front end.", "smile" ); ?>">
                  <i class="dashicons dashicons-editor-help"></i>
                </span>
              </label>
              <label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
                <input type="text"  id="cp-google-fonts" class="form-control smile-input smile-switch-input"  name="cp-google-fonts" value="<?php echo $gfval; ?>" />
                <input type="checkbox" <?php echo $is_checked; ?> id="smile_cp-google-fonts_btn_<?php echo $uniq; ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo $gfval; ?>" >
                <label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-google-fonts" for="smile_cp-google-fonts_btn_<?php echo $uniq; ?>"></label>
              </label>
            </p><!-- Google Fonts -->

            <p><?php

                $cp_settings = get_option('convert_plug_settings');

                $selected=$wselected=$loggedinuser='';
                $loggedinuser = explode(",",$cp_settings['cp-user-role']);
                $timezone = $cp_settings['cp-timezone'];
		            $user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '60';
                if($timezone=='system'){
                 $selected='selected';
                }
                if($timezone=='wordpress'){
                 $wselected='selected';
                } ?>
                <label for="global-timezone" style="width:320px; display: inline-block;"><strong><?php _e( "Set Timezone", "smile" ); ?></strong>
                  <span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php _e( "Depending on your selection, input will be taken for timer based features in ConvertPlug.", "smile" ); ?>">
                    <i class="dashicons dashicons-editor-help"></i>
                  </span>
                </label>
                <select id="global-timezone" name="cp-timezone">
                  <option value="wordpress" <?php _e( $wselected ); ?> ><?php _e( "WordPress Timezone", "smile" ); ?></option>
                  <option value="system" <?php _e( $selected ); ?> ><?php _e( "System Default Time", "smile" ); ?></option>
                </select>
            </p>
            <p>
                <label for="user_inactivity" style="width:320px; display: inline-block;"><strong><?php _e( "User Inactivity Time", "smile" ); ?></strong>
                  <span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php _e( "Module can be trigger for idle user on your website. This setting helps you control that idle time.", "smile" ); ?>">
                    <i class="dashicons dashicons-editor-help"></i>
                  </span>
                </label>
                <input type="number" id="user_inactivity" name="user_inactivity" min="1" max="10000" value="<?php echo $user_inactivity; ?>"/> <span class="description"><?php _e( " Seconds", "smile" ); ?></span>
            </p>
            <p>
              <table>
              	<tr>
                  <td style="vertical-align: top;padding-top: 20px;">
                  	<label for="cp-user-role" style="width:320px; display: inline-block;"><strong><?php _e( "Disable modal impression count for", "smile" ); ?></strong>
                      <span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php _e( "This setting is used while generating analytics data. For selected WordPress user roles, impressions will not be counted.", "smile" ); ?>">
                        <i class="dashicons dashicons-editor-help"></i>
                      </span>
                    </label>
                  </td>
                  <td>
                  <ul class="checkbox-grid">
                  <?php
                       global $wp_roles;
                       $roles = $wp_roles->get_names();

                          foreach ($roles as $rkey => $rvalue) {
                            if(!empty($cp_settings)){
                                        if(in_array($rkey, $loggedinuser)){
                                                   echo'<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="'.$rkey.'"  checked >'.$rvalue.'</li>';
                                                 }else{
                                                   echo'<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="'.$rkey.'" >'.$rvalue.'</li>';
                                                 }
                             }else{
                              if($rkey=='administrator'){

                                   echo'<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="'.$rkey.'"  checked >'.$rvalue.'</li>';

                                 }else{
                                    echo'<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="'.$rkey.'" >'.$rvalue.'</li>';
                                 }
                            }
                          }

                  ?>
                  </ul>
              </td>
            </tr>
          </table>
        </p>
        </form>
        <button type="button" class="button button-primary button-update-settings"><?php _e("Save Settings", "smile"); ?></button>
    </div>
</div>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
  jQuery('.has-tip').frosty();
	var form = jQuery("#convert_plug_settings");
	var btn = jQuery(".button-update-settings");
	var inactive = jQuery("#user_inactivity");
	var msg = jQuery(".msg");
	btn.click(function(){
        var ser = jQuery("[name]").not("#cp-user-role").serialize();
        var array_values = [];
    jQuery("input:checkbox").map(function(){
      if(jQuery(this).is(":checked")){
         array_values.push( $(this).val() );
      }
    });
	var arrayValues = array_values.join(',');
	ser+="&cp-user-role="+arrayValues;

	var inactive_time = inactive.val();
	ser+="&user_inactivity="+inactive_time;

    var data =ser;
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			dataType: 'JSON',
			type: 'POST',
			success: function(result){
				if(result.message == "Settings Updated!"){
					swal("<?php _e( "Updated!", "smile" ); ?>", result.message, "success");
					setTimeout(function(){
						window.location = window.location;
					},500);
				} else {
					swal("<?php _e( "Error!", "smile" ); ?>", result.message, "error");
				}
			}
		});
	});
});
</script>
