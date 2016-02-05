<?php $cp_addon_list = Smile_Framework::$addon_list; ?>
<div class="wrap about-wrap bsf-connect bsf-connect-new-list bend">
  <div class="wrap-container">
    <div class="bend-heading-section bsf-connect-header bsf-cnlist-header">
      <h1><?php _e( "Create New Campaign", "smile" ); ?></h1>
    </div>
    <!-- bend-heading section -->

    <div class="msg"></div>

    <div class="bend-content-wrap">
    	<div class="smile-absolute-loader">
    		<div class="smile-loader" style="transform: none !important;top: 120px !important;">
				<div class="smile-loading-bar"></div>
				<div class="smile-loading-bar"></div>
				<div class="smile-loading-bar"></div>
				<div class="smile-loading-bar"></div>
			</div>
		</div>
      <hr class="bsf-extensions-lists-separator" style="margin: -20px 0px 45px 0px;">
      </hr>
      <div class="container bsf-cnlist-content">
        <div class="bsf-cnlist-form col-sm-6 col-sm-offset-3">

			<div class="cp-wizard-progress">
				<div class="cp-wizard-progress-bar"></div>
			</div>

            <form id="bsf-cnlist-contact-form">
            	<div class="container">
            		<div class="col-sm-12">
	            		<div class="bsf-cnlist-form-row">
							<input type="hidden" name="action" value="smile_add_list" />
							<input type="hidden" name="date" value="<?php echo esc_attr( date("j-n-Y") ); ?>" />
						</div>
		            	<div class="step-1 bsf-cnlist-form-wizard in active">
		            		<div class="steps-section">
								<div class="bsf-cnlist-form-row bsf-cnlist-list-name" >
									<label for="bsf-cnlist-list-name" >
									  <?php _e( "Campaign Name", "smile" ); ?>
									</label>
									<input type="text" id="bsf-cnlist-list-name" name="list-name" autofocus="autofocus"/>
									<span class="cp-validation-error"></span>
								</div>
								<div class="bsf-cnlist-form-row bsf-cnlist-list-provider" >
										<label for="bsf-cnlist-list-provider" >
										  <?php _e( "Do you want to sync connects with any third party software?", "smile" ); ?>
										</label>
										<select id="bsf-cnlist-list-provider" class="bsf-cnlist-select" name="list-provider">
										  	<option value="Convert Plug">No</option>
											<?php
											if( !empty( $cp_addon_list ) ) {
												foreach( $cp_addon_list as $slug => $setting ){
													echo '<option value="' . $slug . '">' . $setting['name'] . '</option>';
												}
											}
											?>
										</select>
										<div class="bsf-cnlist-list-provider-spinner"></div>
								</div>

					            <div class="bsf-cnlist-form-row short-description" >
					              <p class="description">
					                <?php _e( "Your connects can be synced to CRM & Mailer softwares like HubSpot, MailChimp, etc. if you choose any from above.", "smile" ); ?>
					              </p>
					            </div>
					        </div><!-- .steps-section -->
						</div>
						<!-- .step-1    -->

						<div class="step-2 bsf-cnlist-form-wizard" >
							<div class="steps-section">
								<div class="col-sm-12">
						            <div class="bsf-cnlist-form-row bsf-cnlist-mailer-data" style="display:none;"></div>
						            <div class="bsf-cnlist-mailer-help">
						            	<a href="http://documentation.dev/mailer/" target="_blank"><?php _e( "Where to find this?", "smile" ); ?></a>
						            </div><!-- .bsf-cnlist-mailer-help -->
					            </div>
					        </div><!-- .steps-section -->
			            </div>
		            	<!-- .step-2    -->
	            	</div>
	        	</div>

	            <div class="container bsf-new-list-wizard">
	            	<div class="col-sm-6">
	            		<button class="wizard-prev button button-primary disabled" type="button">
	            			<?php _e( "Previous", "smile" ); ?>
	            		</button>
	        		</div>
	        		<div class="col-sm-6">
	        			<div class="bsf-cnlist-save-btn" >
							<button id="save-btn" class="wizard-save button button-primary" data-provider="">
								<?php _e( "Create Campaign", "smile" ); ?>
							</button>
				        </div>
				        <div class="bsf-cnlist-next-btn" style="display:none;">
	            			<button class="wizard-next button button-primary" type="button" style="display: inline-block;">
	            				<?php _e( "Next", "smile" ); ?>
	            			</button>
	            		</div>
	            	</div>
	            </div><!-- .bsf-new-list-wizard -->
            </form>
        </div>
        <!-- .bsf-cnlist-form -->
      </div>
      <!-- .container -->
    </div>
    <!-- .bend-content-wrap -->
  </div>
  <!-- .wrap-container -->
</div>
<!-- .wrap -->
<script type="text/javascript">
var provider = jQuery("#bsf-cnlist-list-provider");
jQuery(document).ready(function(){
	var val = provider.val().toLowerCase();
	jQuery("#save-btn").attr('data-provider',val);
	provider.change(function(e){
		if( jQuery(this).val() == 'Convert Plug' ) {
			jQuery(".bsf-cnlist-save-btn").show();
			jQuery(".bsf-cnlist-next-btn").hide();
			jQuery("#save-btn").removeAttr('disabled');
		} else {
			jQuery(".bsf-cnlist-save-btn").hide();
			jQuery("#save-btn").attr('disabled', 'disabled');
			jQuery(".bsf-cnlist-next-btn").show();
		}
	});
});

jQuery(document).on( "click", ".update-mailer", function(){
	jQuery('.bsf-cnlist-mailer-data input[type="text"]').val('');
	jQuery(this).replaceWith('<button id="auth-'+jQuery(this).attr('data-mailer')+'" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate ' + jQuery(this).attr('data-mailerslug') + '", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
});

jQuery("#save-btn").click(function(e){

	e.preventDefault();

	if( jQuery("#bsf-cnlist-list-name").val() == "" ){
		jQuery('html, body').animate({ scrollTop: jQuery(".bsf-cnlist-list-name").offset().top - 100 }, 500);
		jQuery("#bsf-cnlist-list-name").focus();
		jQuery("#bsf-cnlist-list-name").addClass('connect-new-list-required');
		return false;
	}

	var isCampaignExists = false;
	var campaignName = jQuery("#bsf-cnlist-list-name").val();
	jQuery.ajax({
		url: ajaxurl,
		data: {
			campaign: campaignName,
			action: 'isCampaignExists'
		},
		async: false,
		method: "POST",
		dataType: "JSON",
		success: function(result){
			if( result.status == 'error' ) {
				jQuery(".cp-validation-error").show();
				jQuery(".cp-validation-error").html(result.message);
				isCampaignExists = true;
			} else {
				jQuery(".cp-validation-error").html('');
			}
		},
		error: function(err){
			console.log(err);
		}
	});

	if( isCampaignExists ) {
		return false;
	}

	var data = jQuery("#bsf-cnlist-contact-form").serialize();
	var provider = jQuery(this).data('provider');

	if( provider == "madmimi" ) {
		var mailer_list_name = 	jQuery("#madmimi-list option:selected").text();
		var mailer_list_id = jQuery("#madmimi-list option:selected").text();
		data += "&list="+mailer_list_id+"&provider_list="+mailer_list_name;
	} else if( provider == "sendy" ){
		var mailer_list_name = 	jQuery( '#sendy_list_ids' ).val();
		var mailer_list_id = jQuery( '#sendy_list_ids' ).val();
		data += "&list="+mailer_list_id+"&provider_list="+mailer_list_name;
	} else if( provider == "infusionsoft" ){
		var lists_arr = new Array();
		var mailer_list_id = '';
		var mailer_list_name = '';
		var selected_id = '';
		var name = '';
		if( jQuery( "#"+provider+"-list option:selected" ).text() != '' ) {
			jQuery( "#"+provider+"-list option:selected" ).each(function(){
				selected_id = jQuery(this).val();
	            name = jQuery(this).text();
				lists_arr.push("{\""+selected_id+"\" : \""+name+"\"}");
			});
			
		} else {
			selected_id = -1;
			name = -1;
			lists_arr.push("{\""+selected_id+"\" : \""+name+"\"}");
		}
		mailer_list_id = JSON.stringify(lists_arr);
		mailer_list_name = 	JSON.stringify(lists_arr);
		
		var infusionsoft_action_id = jQuery('#infusionsoft_action_id').val();
		data += "&list="+mailer_list_id+"&provider_list="+mailer_list_name+"&infusionsoft_action_id="+infusionsoft_action_id;
	} else {
		var mailer_list_id = jQuery("#"+provider+"-list ").val();
		var mailer_list_name = 	jQuery("#"+provider+"-list option:selected").text();
		data += "&list="+mailer_list_id+"&provider_list="+mailer_list_name;
	}
	var loading = jQuery(this).next(".spinner");
	var msg = jQuery(".msg");
	loading.css('visibility','visible');
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		method: "POST",
		dataType: "JSON",
		success: function(result){

			if( result.status == 'error' ) {
				jQuery(".cp-validation-error").show();
				jQuery(".cp-validation-error").html(result.message);
				return false;
			} else{
				 jQuery(".cp-validation-error").html('');
			}

			if( result.message == "added" ){
				swal({
					title: "<?php _e( "Added!", "smile" ); ?>",
					text: "<?php _e( "The campaign you just created, is added to the list.", "smile" ); ?>",
					type: "success",
					timer: 2000,
					showConfirmButton: false
				});
			} else {
				swal({
					title: "<?php _e( "Error!", "smile" ); ?>",
					text: "<?php _e( "Error adding the campaign to the list. Please try again.", "smile" ); ?>",
					type: "error",
					timer: 2000,
					showConfirmButton: false
				});
			}
			setTimeout( function(){
				document.location = 'admin.php?page=contact-manager';
			}, 600 );
		},
		error: function(err){
			swal({
				title: "<?php _e( "Error!", "smile" ); ?>",
				text: "<?php _e( "Error adding the campaign to the list. Please try again.", "smile" ); ?>",
				type: "error",
				timer: 2000,
				showConfirmButton: false
			});
		}
	});
});

// disconnect mailer
jQuery(document).on( "click", ".disconnect-mailer", function(){

	var mailerName = jQuery(this).data("mailerslug");
	if(confirm("<?php _e( "Are you sure? If you disconnect, your previous campaigns syncing with", "smile" ); ?> "+mailerName+" <?php _e( "will be disconnected as well.", "smile" ); ?>")) {

		var mailer = jQuery(this).data('mailer');
		var action = 'disconnect_'+mailer;
		var data = {action:action};
		jQuery(".smile-absolute-loader").css('visibility','visible');
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'JSON',
			success: function(result){

				jQuery("#save-btn").attr('disabled','true');
				if(result.message == "disconnected" && mailer == "mailchimp" ){
					jQuery("#mailchimp_api_key").val('');
					jQuery(".mailchimp-list").html('');
					//jQuery("#disconnect-mailchimp").addClass('button-secondary').html("<?php _e( "Authenticate MailChimp", "smile" ); ?>").removeClass('disconnect-mailer').attr('id','auth-mailchimp');
					jQuery("#disconnect-mailchimp").replaceWith('<button id="auth-mailchimp" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate MailChimp", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-mailchimp").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "aweber" ){
					jQuery(".aweber-list").html('');
					jQuery(".aweber-auth").show();
					jQuery("#authentication_token").val('');
					jQuery(".disconnect-mailer").addClass('button-secondary get_aweber_data').removeClass('reset_aweber_data').html('Connect to Aweber');
					jQuery('.get_aweber_data').show();
					jQuery(".disconnect-mailer").removeClass('disconnect-mailer');
					jQuery(".get_aweber_data").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "madmimi" ){
					jQuery("#madmimi_api_key").val('');
					jQuery('#madmimi_email').val('');
					jQuery(".madmimi-list").html('');
					jQuery("#disconnect-madmimi").replaceWith('<button id="auth-madmimi" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate MadMimi", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					//jQuery("#disconnect-madmimi").addClass('button-secondary').removeClass('disconnect-mailer').html("Authenticate Madmimi").attr('id','auth-madmimi');
					jQuery("#auth-madmimi").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "campaignmonitor" ){
					jQuery("#campaignmonitor_api_key").val('');
					jQuery('#campaignmonitor_client_id').val('');
					jQuery(".campaignmonitor-list").html('');
					//jQuery("#disconnect-campaignmonitor").addClass('button-secondary').html("<?php _e( "Authenticate campaignmonitor", "smile" ); ?>").removeClass('disconnect-mailer').attr('id','auth-campaignmonitor');
					jQuery("#disconnect-campaignmonitor").replaceWith('<button id="auth-campaignmonitor" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate Campaign Monitor", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-campaignmonitor").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "activecampaign" ){
					jQuery("#activecampaign_api_key").val('');
					jQuery('#activecampaign_url').val('');
					jQuery('.activecampaign-list').html('');
					//jQuery("#disconnect-activecampaign").addClass('button-secondary').html("<?php _e( "Authenticate activecampaign", "smile" ); ?>").removeClass('disconnect-mailer').attr('id','auth-activecampaign');
					jQuery("#disconnect-activecampaign").replaceWith('<button id="auth-activecampaign" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate Active Campaign", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-activecampaign").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "icontact" ){
					jQuery("#icontact_app_id").val('');
					jQuery('#icontact_email').val('');
					jQuery('#icontact_pass').val('');
					jQuery(".icontact-list").html('');
					//jQuery("#disconnect-icontact").addClass('button-secondary').html("<?php _e( "Authenticate icontact", "smile" ); ?>").removeClass('disconnect-mailer').attr('id','auth-icontact');
					jQuery("#disconnect-icontact").replaceWith('<button id="auth-icontact" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate iContact", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-icontact").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "emma" ){
					jQuery("#emma_pub_api").val('');
					jQuery('#emma_priv_api').val('');
					jQuery('#emma_acc_id').val('');
					jQuery(".emma-list").html('');
					//jQuery("#disconnect-emma").addClass('button-secondary').html("<?php _e( "Authenticate MyEmma", "smile" ); ?>").removeClass('disconnect-mailer').attr('id','auth-emma');
					jQuery("#disconnect-emma").replaceWith('<button id="auth-emma" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate MyEmma", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-emma").attr('disabled','true');
				} else if(result.message == "disconnected" && mailer == "hubspot" ){
					jQuery("#hubspot_api_key").val('');
					jQuery(".hubspot-list").html('');
					//jQuery("#disconnect-hubspot").addClass('button-secondary').html("<?php _e( "Authenticate HubSpot", "smile" ); ?>").removeClass('disconnect-mailer').attr('id','auth-hubspot');
					jQuery("#disconnect-hubspot").replaceWith('<button id="auth-hubspot" class="button button-secondary auth-button" disabled="true"><?php _e( "Authenticate HubSpot", "smile" ); ?></button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-hubspot").attr('disabled','true');
				}

				jQuery('.bsf-cnlist-form-row').fadeIn('300');
				jQuery(".bsf-cnlist-mailer-help").show();
				jQuery(".smile-absolute-loader").css('visibility','hidden');
			}
		});
	}
	else {
		return false;
	}
});


// mailchimp authentication
jQuery(document).on( "click", "#auth-mailchimp", function(e){
	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var auth_token = jQuery("#mailchimp_api_key").val();
	var action = 'update_mailchimp_authentication';
	var data = {action:action,authentication_token:auth_token};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#mailchimp_api_key").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-mailchimp").closest('.bsf-cnlist-form-row').hide();
				jQuery(".mailchimp-list").html(result.message);
			} else {
				jQuery(".mailchimp-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});

jQuery(document).on("click", ".auth-aweber" , function(e){
	e.preventDefault();
	return false;
});

// aweber authentication
jQuery(document).on("click", ".get_aweber_data", function(e){
	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var auth_token = jQuery("#authentication_token").val();
	var action = 'update_aweber_authentication';
	var data = {action:action,authentication_token:auth_token};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery("#save-btn").removeAttr('disabled');
				jQuery(".get_aweber_data").closest('.bsf-cnlist-form-row').hide();
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery(".button-secondary.auth-aweber").closest('.bsf-cnlist-form-row').hide();
				jQuery("#authentication_token").closest('.bsf-cnlist-form-row').hide();
				jQuery(".aweber-list").html(result.message);
			} else {
				jQuery(".aweber-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});

// madmimi authentication
jQuery(document).on( "click", "#auth-madmimi", function(e){
	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var email = jQuery('#madmimi_email').val();
	var auth_token = jQuery("#madmimi_api_key").val();
	var action = 'update_madmimi_authentication';
	var data = {action:action,email:email,authentication_token:auth_token};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#madmimi_email").closest('.bsf-cnlist-form-row').hide();
				jQuery("#madmimi_api_key").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-madmimi").closest('.bsf-cnlist-form-row').hide();
				jQuery(".madmimi-list").html(result.message);
			} else {
				jQuery(".madmimi-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});


// Campaign Monitor authentication
jQuery(document).on( "click", "#auth-campaignmonitor", function(e){

	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var campaignmonitor_api_key = jQuery("#campaignmonitor_api_key").val();
	var campaignmonitor_client_id = jQuery('#campaignmonitor_client_id').val();
	var action = 'update_campaignmonitor_authentication';
	var data = {action:action,campaignmonitor_client_id:campaignmonitor_client_id,campaignmonitor_api_key:campaignmonitor_api_key};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#campaignmonitor_client_id").closest('.bsf-cnlist-form-row').hide();
				jQuery("#campaignmonitor_api_key").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-campaignmonitor").closest('.bsf-cnlist-form-row').hide();
				jQuery(".campaignmonitor-list").html(result.message);
			} else {
				jQuery(".campaignmonitor-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});


// Active Campaign authentication
jQuery(document).on( "click", "#auth-activecampaign", function(e){

	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var auth_token = jQuery("#activecampaign_api_key").val();
	var campaingURL = jQuery('#activecampaign_url').val();
	var action = 'update_activecampaign_authentication';
	var data = {action:action,campaingURL:campaingURL,authentication_token:auth_token};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#activecampaign_url").closest('.bsf-cnlist-form-row').hide();
				jQuery("#activecampaign_api_key").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-activecampaign").closest('.bsf-cnlist-form-row').hide();
				jQuery(".activecampaign-list").html(result.message);
			} else {
				jQuery(".activecampaign-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});

// iContact authentication
jQuery(document).on( "click", "#auth-icontact", function(e){

	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var appID = jQuery("#icontact_app_id").val();
	var appUser = jQuery('#icontact_email').val();
	var appPass = jQuery('#icontact_pass').val();
	var action = 'update_icontact_authentication';
	var data = {action:action,appID:appID,appUser:appUser,appPass:appPass};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#icontact_app_id").closest('.bsf-cnlist-form-row').hide();
				jQuery("#icontact_email").closest('.bsf-cnlist-form-row').hide();
				jQuery("#icontact_pass").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-icontact").closest('.bsf-cnlist-form-row').hide();
				jQuery(".icontact-list").html(result.message);
			} else {
				jQuery(".icontact-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});


// MyEmma authentication
jQuery(document).on( "click", "#auth-emma", function(e){

	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var public_key = jQuery("#emma_pub_api").val();
	var priv_key = jQuery('#emma_priv_api').val();
	var accID = jQuery('#emma_acc_id').val();
	var action = 'update_emma_authentication';
	var data = {action:action,public_key:public_key,priv_key:priv_key,accID:accID};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#emma_pub_api").closest('.bsf-cnlist-form-row').hide();
				jQuery("#emma_priv_api").closest('.bsf-cnlist-form-row').hide();
				jQuery("#emma_acc_id").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-emma").closest('.bsf-cnlist-form-row').hide();
				jQuery(".emma-list").html(result.message);
			} else {
				jQuery(".emma-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});


// Hubspot authentication
jQuery(document).on( "click", "#auth-hubspot", function(e){

	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var api_key = jQuery("#hubspot_api_key").val();
	var action = 'update_hubspot_authentication';
	var data = {action:action,api_key:api_key};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#hubspot_api_key").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-hubspot").closest('.bsf-cnlist-form-row').hide();
				jQuery(".hubspot-list").html(result.message);
			} else {
				jQuery(".hubspot-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});

/************** JQuery change events *************/

jQuery(document).on("change keyup paste keydown","#mailchimp_api_key", function(e) {
		var val = jQuery(this).val();
		if( val !== "" )
			jQuery("#auth-mailchimp").removeAttr('disabled');
		else
			jQuery("#auth-mailchimp").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#madmimi_api_key", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-madmimi").removeAttr('disabled');
	else
		jQuery("#auth-madmimi").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#campaignmonitor_api_key", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-campaignmonitor").removeAttr('disabled');
	else
		jQuery("#auth-campaignmonitor").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#activecampaign_api_key", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-activecampaign").removeAttr('disabled');
	else
		jQuery("#auth-activecampaign").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#icontact_app_id", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-icontact").removeAttr('disabled');
	else
		jQuery("#auth-icontact").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#emma_pub_api", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-emma").removeAttr('disabled');
	else
		jQuery("#auth-emma").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#hubspot_api_key", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-hubspot").removeAttr('disabled');
	else
		jQuery("#auth-hubspot").attr('disabled','true');
});

jQuery(document).on("change keyup paste keydown","#authentication_token", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery(".get_aweber_data").removeAttr('disabled');
	else
		jQuery(".get_aweber_data").attr('disabled','true');
});

jQuery(document).on('click', '.wizard-next', function(e){

	var cpDesc = jQuery('.bsf-cnlist-provider-description').html();
	if( jQuery("#bsf-cnlist-list-name").val() == '' ) {
		jQuery("#bsf-cnlist-list-name").addClass('connect-new-list-required');
		jQuery("#bsf-cnlist-list-name").focus();
		return false;
	} else {

		var isCampaignExists = false;
		var campaignName = jQuery("#bsf-cnlist-list-name").val();
		jQuery.ajax({
			url: ajaxurl,
			data: {
				campaign: campaignName,
				action: 'isCampaignExists'
			},
			async: false,
			method: "POST",
			dataType: "JSON",
			success: function(result){
				if( result.status == 'error' ) {
					jQuery(".cp-validation-error").show();
					jQuery(".cp-validation-error").html(result.message);
					isCampaignExists = true;
				} else {
					jQuery(".cp-validation-error").html('');
				}
			},
			error: function(err){
				console.log(err);
			}
		});

		if(isCampaignExists) {
			return false;
		}

		jQuery(".smile-absolute-loader").css('visibility','visible');
		jQuery("#bsf-cnlist-list-name").removeClass('has-error');
		jQuery(this).addClass('disabled');
		jQuery(".wizard-prev").removeClass('disabled');
		jQuery(".bsf-cnlist-save-btn").show();
		jQuery(".wizard-next").hide();

		jQuery('.bsf-cnlist-provider-description').fadeOut(300);
		val = jQuery("#bsf-cnlist-list-provider").val().toLowerCase();
		jQuery("#save-btn").attr('data-provider',val);

		jQuery("#save-btn").attr('disabled','true');
		var action = 'get_'+val+'_data';
		var data = 'action='+action;

		jQuery.ajax({
			url: ajaxurl,
			data: data,
			method: "POST",
			dataType: "JSON",
			success: function(result){
				if( result.isconnected )  {
					jQuery(".bsf-cnlist-mailer-help").hide();
				}
				else {
					jQuery(".bsf-cnlist-mailer-help").show();
				}
				jQuery(".bsf-cnlist-mailer-help a").attr('href',result.helplink);
				jQuery('.bsf-cnlist-mailer-data').html(result.data);
				jQuery('.bsf-cnlist-mailer-data').slideDown(300);
				jQuery(".smile-absolute-loader").css('visibility','hidden');

				setTimeout(function(){
					jQuery('.bsf-cnlist-form-wizard.step-1').css('transform','translateX(-100px)');
				}, 800 );

				setTimeout(function(){
					jQuery('.bsf-cnlist-form-wizard.step-1').removeClass('active in');
					jQuery('.bsf-cnlist-form-wizard.step-2').addClass('in active').css( 'transform' ,'translateX(0px)');
				}, 1200 );

				if( jQuery("#"+val+"-list").length > 0 ) {
					jQuery("#save-btn").removeAttr('disabled');
				}
				jQuery(".select2-infusionsoft-list").select2();
			},
			error: function(err){
				console.log(err);
			}
		});
	}
});

jQuery(document).on('click', '.wizard-prev', function(e){

	if( !jQuery(this).hasClass('disabled') ) {

		setTimeout(function(){
			jQuery('.bsf-cnlist-form-wizard.step-2').css('transform','translateX(-100px)');
		}, 200 );

		setTimeout(function(){
			jQuery('.bsf-cnlist-form-wizard.step-2').removeClass('active in');
			jQuery('.bsf-cnlist-form-wizard.step-1').addClass('in active').css( 'transform' ,'translateX(0px)');
			jQuery(".wizard-next").removeClass('disabled');
			jQuery(".wizard-prev").addClass('disabled');
			jQuery(".bsf-cnlist-save-btn").hide();
			jQuery(".wizard-next").show();

		}, 600 );
	}
});

jQuery(document).on('keyup change keydown', '#bsf-cnlist-list-name', function() {
	if(jQuery(this).val() !== '') {
		jQuery(this).removeClass('connect-new-list-required');
	}
});

</script>
