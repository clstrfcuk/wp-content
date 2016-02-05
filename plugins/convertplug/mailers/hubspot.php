<?php
if(!class_exists('Smile_Mailer_Hubspot')){
	class Smile_Mailer_Hubspot{
	
		private $slug;
		private $setting;
		function __construct(){

			require_once('api/hubspot/class.lists.php');
			require_once('api/hubspot/class.contacts.php');
			add_action( 'wp_ajax_get_hubspot_data', array($this,'get_hubspot_data' ));
			add_action( 'wp_ajax_update_hubspot_authentication', array($this,'update_hubspot_authentication' ));
			add_action( 'wp_ajax_disconnect_hubspot', array($this,'disconnect_hubspot' ));
			add_action( 'wp_ajax_hubspot_add_subscriber', array($this,'hubspot_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_hubspot_add_subscriber', array($this,'hubspot_add_subscriber' ));
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'HubSpot',
				'parameters' => array( 'api_key' ),
				'where_to_find_url' => 'http://help.hubspot.com/articles/KCS_Article/Integrations/How-do-I-get-my-HubSpot-API-key'
			);
			$this->slug = 'hubspot';
		}
		
		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}

		// retrieve mailer info data
		function get_hubspot_data(){
			$isKeyChanged = false;
			$connected = false;
			ob_start();
			$hubspot_api = get_option($this->slug.'_api');
			if( $hubspot_api != '' ) {
				try{
					$listsObj = new HubSpot_Lists($hubspot_api);
					$lists = $listsObj->get_static_lists(null);
				} catch ( Exception $ex ) {
					$formstyle = '';
					$isKeyChanged = true;
				}
				if( isset( $lists->status ) ){
					if( $lists->status == 'error' ) {
						$formstyle = '';
						$isKeyChanged = true;
					}
				} else {
					$formstyle = 'style="display:none;"';
				}
            	 
			} else {
            	$formstyle = '';
			}
            ?>
			
			<div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
				<label for="cp-list-name"><?php _e( $this->setting['name']." API Key", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_api_key" name="<?php echo $this->slug; ?>-api-key" value="<?php echo esc_attr( $hubspot_api ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
	            <?php
	            if($hubspot_api != ''  && !$isKeyChanged) {
	            	$hs_lists = $this->get_hubspot_lists($hubspot_api);
	            	if( !empty( $hs_lists ) ){
						$connected = true;
					?>
						<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
						<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
					<?php
						foreach($hs_lists as $id => $name) {
					?>
							<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
					<?php
						}
						?>
						</select>
					<?php
					} else {
					?>
						<label for="<?php echo $this->slug; ?>-list"><?php echo __( "You need at least one list added in " . $this->setting['name'] . " before proceeding.", "smile" ); ?></label>
					<?php
					}
	            }
	            ?>	
            </div>

            <div class="bsf-cnlist-form-row">


            	<?php if( $hubspot_api == "" ) { ?>
	            	<button id="auth-<?php echo $this->slug; ?>" class="button button-secondary auth-button" disabled><?php _e( "Authenticate ".$this->setting['name'], "smile" ); ?></button><span class="spinner" style="float: none;"></span>
	            <?php } else {
	            		if( $isKeyChanged ) {
	            ?>
	            	<div id="update-<?php echo $this->slug; ?>" class="update-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Your credentials seems to be changed.</br>Use different '" . $this->setting['name'] . " credentials?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
	            <?php
	            		} else {
	            ?>
	            	<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
	            <?php
	            		}
	            ?>
	            <?php } ?>

	        </div>

            <?php
            $content = ob_get_clean();
            
            $result['data'] = $content;
            $result['helplink'] = $this->setting['where_to_find_url'];
            $result['isconnected'] = $connected;
            echo json_encode($result);
            exit();

		}
				
		function hubspot_add_subscriber(){
			$post = $_POST;
			$data = array();
			$email = isset( $post['email'] ) ? $post['email'] : '';
			$only_conversion = isset( $post['only_conversion'] ) ? true : false;
			$this->api_key = get_option($this->slug.'_api');
			$name = isset( $_POST['name'] ) ? $_POST['name'] : '';			
			$email = $post['email'];			
			$list = $post['list_id'];
			$on_success = isset( $post['message'] ) ? 'message' : 'redirect';
			$msg_wrong_email = ( isset( $post['msg_wrong_email']  )  && $post['msg_wrong_email'] !== '' ) ? $post['msg_wrong_email'] : __( 'Please enter correct email address.', 'smile' );
			$msg = isset( $_POST['message'] ) ? $_POST['message'] : __( 'Thank you.', 'smile' );

			if($on_success == 'message'){
				$action	= 'message';
				$url	= 'none';
			} else {
				$action	= 'redirect';
				$url	= $post['redirect'];
			}

			$contact = array();
			$contact['name'] = $name;
			$contact['email'] = $email;
			$contact['date'] = date("j-n-Y");

			//	Check Email in MX records
			if( !$only_conversion ){
				$email_status = apply_filters('cp_valid_mx_email', $email );
			} else {
				$email_status = false;
			}
			if($email_status) {

				$status = 'success';
			    try {
			    	$contacts = new HubSpot_Contacts($this->api_key);
				    //Create Contact
				    $params =  array('email' => $email, 'firstname' => $name );

				    $createdContact = $contacts->create_contact($params);

				    if(isset($createdContact->{'status'}) && $createdContact->{'status'} == 'error'){
				    	$contactProfile = $createdContact->identityProfile;
				    	$contactID = $contactProfile->vid;
				    	$contacts->update_contact($contactID,$params);				    	
				    } else {
				    	$contactID = $createdContact->{'vid'};
				    }   

				    $lists = new HubSpot_Lists($this->api_key);
				   	$contacts_to_add = array($contactID);
				   	$add_res = $lists->add_contacts_to_list($contacts_to_add,$list);
				   	$add_res = json_decode( $add_res );
				   	
				   	if( isset( $add_res->status ) ) {
					   	if( $add_res->status == 'error' ) {
					   		print_r(json_encode(array(
								'action' => $action,
								'email_status' => $email_status,
								'status' => 'error',
								'message' => __( "Something went wrong. Please try again.", "smile" ),
								'url' => $url,
							)));
							exit();
					   	}
					}
				} catch (Exception $e) {

					print_r(json_encode(array(
						'action' => $action,
						'email_status' => $email_status,
						'status' => 'error',
						'message' => __( "Something went wrong. Please try again.", "smile" ),
						'url' => $url,
					)));
					exit();
				}
				
				$style_id = $_POST['style_id'];
				$option = $_POST['option'];

				if( function_exists( "cp_add_subscriber_contact" ) ){
					$isuserupdated = cp_add_subscriber_contact( $option ,$contact );
				}

				if ( !$isuserupdated ) {  // if user is updated dont count as a conversion
						// update conversions 
						smile_update_conversions($style_id);
				}

			} else {
				if( $only_conversion ){
					// update conversions 
					$status = 'success';
					smile_update_conversions( $style_id );
				} else {
					$msg = $msg_wrong_email;
					$status = 'error';
				}
			}
			
			if($on_success == 'message'){
				$action	= 'message';
				$url	= 'none';
			} else {
				$action	= 'redirect';
				$url	= $post['redirect'];
			}

			print_r(json_encode(array(
				'action' => $action,
				'email_status' => $email_status,
				'status' => $status,
				'message' => $msg,
				'url' => $url,
			)));
			exit();
		}

		function update_hubspot_authentication(){
			$post = $_POST;

			$data = array();
			$HAPIKey = $post['api_key'];

			if( $post['api_key'] == '' ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please provide valid API Key for your " . $this->setting['name'] . " account.", "smile" )
				)));
				exit();
			}

			try{
				$listsObj = new HubSpot_Lists($HAPIKey);
				$lists = $listsObj->get_static_lists(null);
			} catch( Exception $ex ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Something went wrong. Please try again.", "smile" )
				)));
				exit();
			}
			
			if( isset( $lists->status ) ){
				if( $lists->status == 'error' ) {
					print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Failed to authenticate. Please check API Key", "smile" )
					)));
					exit();
				}
			}
			
			if( is_array( $lists->lists ) && empty( $lists->lists ) ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "You have zero static lists in your HubSpot account. You must have at least one static list before integration." , "smile" )
				)));
				exit();
			}
        	ob_start();
			$hs_lists = array();
			$html = $query = '';
			?>
			<label for="<?php echo $this->slug; ?>-list"  >Select List</label>
			<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
			<?php
			foreach($lists->lists as $offset => $list) {
			?>
				<option value="<?php echo $list->listId; ?>"><?php echo $list->name; ?></option>
			<?php
				$query .= $list->listId.'|'.$list->name.',';
				$hs_lists[$list->listId] = $list->name;
			}
			?>
			</select>
			<input type="hidden" id="mailer-all-lists" value="<?php echo $query; ?>"/>
			<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
			<input type="hidden" id="mailer-list-api" value="<?php echo $HAPIKey; ?>"/>

			<div class="bsf-cnlist-form-row">
				<div id='disconnect-<?php echo $this->slug; ?>' class='disconnect-mailer' data-mailerslug='<?php echo $this->setting['name'] ?>' data-mailer='<?php echo $this->slug; ?>'>
					<span>
						<?php echo _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?>
					</span>
				</div>
				<span class='spinner' style='float: none;'></span>
			</div>
			<?php 
			$html .= ob_get_clean();
			update_option($this->slug.'_api',$HAPIKey);
			update_option($this->slug.'_lists',$hs_lists);	

			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));
			
			exit();
		}
		
		
		function disconnect_hubspot(){
			delete_option( $this->slug.'_api' );
			delete_option( $this->slug.'_lists' );
			
			$smile_lists = get_option('smile_lists');			
			if( !empty( $smile_lists ) ){ 
				foreach( $smile_lists as $key => $list ) {
					$provider = $list['list-provider'];
					if( strtolower( $provider ) == strtolower( $this->slug ) ){
						$smile_lists[$key]['list-provider'] = "Convert Plug";
						$contacts_option = "cp_" . $this->slug . "_" . preg_replace( '#[ _]+#', '_', strtolower( $list['list-name'] ) );
                        $contact_list = get_option( $contacts_option );
                        $deleted = delete_option( $contacts_option );
                        $status = update_option( "cp_connects_" . preg_replace( '#[ _]+#', '_', strtolower( $list['list-name'] ) ), $contact_list );
					}
				}
				update_option( 'smile_lists', $smile_lists );
			}
			
			print_r(json_encode(array(
                'message' => "disconnected",
			)));
			exit();
		}

		/*
		 * Function Name: get_hubspot_lists
		 * Function Description: Get HubSpot Mailer Campaign list
		 */

		function get_hubspot_lists( $api_key = '' ) {
			if( $api_key != '' ) {
				try{
					$listsObj = new HubSpot_Lists($api_key);
					$lists = $listsObj->get_static_lists(null);
				} catch ( Exception $ex ) {
					return array();
				}
					
				if( isset( $lists->status ) ){
					if( $lists->status == 'error' ) {
						return array();
					}
				} else {
					$hs_lists = array();
					foreach($lists->lists as $offset => $list) {
						$hs_lists[$list->listId] = $list->name;
					}
					return $hs_lists;
				}
			} else {
				return array();
			}
		}
	}
	new Smile_Mailer_Hubspot;	
}