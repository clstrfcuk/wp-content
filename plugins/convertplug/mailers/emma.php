<?php
if(!class_exists('Smile_Mailer_Emma')){
	class Smile_Mailer_Emma{
	
		private $slug;
		private $setting;
		
		function __construct(){

			require_once('api/emma/Emma.php');
			add_action( 'wp_ajax_get_emma_data', array($this,'get_emma_data' ));
			add_action( 'wp_ajax_update_emma_authentication', array($this,'update_emma_authentication' ));
			add_action( 'wp_ajax_disconnect_emma', array($this,'disconnect_emma' ));
			add_action( 'wp_ajax_emma_add_subscriber', array($this,'emma_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_emma_add_subscriber', array($this,'emma_add_subscriber' ));
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'MyEmma',
				'parameters' => array( 'pub_api', 'priv_api', 'acc_id' ),
				'where_to_find_url' => 'http://api.myemma.com/_images/api_key.png'
			);
			$this->slug = 'emma';
		}
		
		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}

		// retrieve mailer info data
		function get_emma_data(){
			$isKeyChanged = false;
			$connected = false;
			ob_start();
			$emma_pub_api = get_option('emma_public_key');
			$emma_priv_api = get_option('emma_priv_key');
			$emma_acc_id = get_option('emma_acc_id');

			if( $emma_pub_api != '' ) {
				try {
					$emma = new Emma($emma_acc_id, $emma_pub_api, $emma_priv_api);
					$res = $emma->myGroups();
					$formstyle = 'style="display:none;"'; 
				} catch( Exception $oException ) {
					// Dump errors
					$isKeyChanged = true;
					$formstyle = '';
				}

			} else {
            	$formstyle = '';
			}
            ?>
			
			<div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-list-name" ><?php _e( "Public API key", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_pub_api" name="<?php echo $this->slug; ?>-pub-key" value="<?php echo esc_attr( $emma_pub_api ); ?>"/>
	        </div>

	        <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-email"><?php _e( "Private API key", "smile" ); ?></label>            
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_priv_api" name="<?php echo $this->slug; ?>-priv-key" value="<?php echo esc_attr( $emma_priv_api ); ?>"/>
	        </div>

	        <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-password"><?php _e( "Account ID", "smile" ); ?></label>            
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_acc_id" name="<?php echo $this->slug; ?>-acc-id" value="<?php echo esc_attr( $emma_acc_id ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
	            <?php
	            if( $emma_pub_api != '' && !$isKeyChanged ) {
				 	$emma_lists = $this->get_emma_lists($emma_acc_id,$emma_pub_api,$emma_priv_api);

					if( !empty( $emma_lists ) ){
						$connected = true;
					?>
						<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
						<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
					<?php
						foreach($emma_lists as $id => $name) {
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

            	<?php if( $emma_pub_api == "" ) { ?>
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
				
		function emma_add_subscriber(){		
			$post = $_POST;
			$data = array();
			
			$email = isset( $post['email'] ) ? $post['email'] : '';
			$only_conversion = isset( $post['only_conversion'] ) ? true : false;	

			$public_key = get_option($this->slug.'_public_key');
			$private_key = get_option($this->slug.'_priv_key');
			$account_id = get_option($this->slug.'_acc_id' );	
					 
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

			//	Check Email in MX records
			if( !$only_conversion ){
				$email_status = apply_filters('cp_valid_mx_email', $email );
			} else {
				$email_status = false;
			}
			if($email_status) {

				$status = 'success';
				try { 

					// Give the API your information
					$member = array();
					$member['email'] = $email;
					$member['fields'] = array('first_name' => $name, 'last_name' => '');
					$member['status_to'] = 'a';
					
					$emma = new Emma($account_id, $public_key, $private_key);

					// Add member to contacts
					$res = $emma->membersAddSingle($member);

					$memberData = json_decode($res);
					$memberID = $memberData->member_id;

					// Add Member to group
					$group = array('group_ids' => array($list));

					$add_res = $emma->membersGroupsAdd($memberID,$group);
					if( strpos( $add_res, $list ) ) {
						print_r(json_encode(array(
							'action' => $action,
							'email_status' => $email_status,
							'status' => 'error',
							'message' => __( "Something went wrong. Please try again.", "smile" ),
							'url' => $url,
						)));
						exit();
					}

				} catch(Emma_Invalid_Response_Exception $e) {

					print_r(json_encode(array(
						'action' => $action,
						'email_status' => $email_status,
						'status' => 'error',
						'message' => __( "Something went wrong. Please try again.", "smile" ),
						'url' => $url,
					)));
					exit();
				}	

				$contact = array();
				$contact['name'] = $name;
				$contact['email'] = $email;
				$contact['date'] = date("j-n-Y");			
		
				$style_id = $_POST['style_id'];
				$option = $_POST['option'];

				if( function_exists( "cp_add_subscriber_contact" ) ){
					$isuserupdated = cp_add_subscriber_contact( $_POST['option'] ,$contact );
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

			print_r(json_encode(array(
				'action' => $action,
				'email_status' => $email_status,
				'status' => $status,
				'message' => $msg,
				'url' => $url,
			)));

			exit();
		}

		function update_emma_authentication(){

			$public_key = $_POST['public_key'];
			$private_key = $_POST['priv_key'];
			$account_id = $_POST['accID'];

			if($public_key == '' || $private_key === '' || $account_id === '') {
				print_r(json_encode(array(
					'status'  => "error",
					'message' => __( "Please enter all credentails from above fields", "smile" )
				)));
				exit();
			}
			
				

			// Returns an array of all members
			try {
				// Give the API your information
				$emma = new Emma($account_id, $public_key, $private_key);

				$result = $emma->myGroups();	

			} catch( Emma_Invalid_Response_Exception $e ) {

				print_r(json_encode(array(
					'status'  => "error",
					'message' => __( "Invalid credentials .", "smile" )
				)));
				exit();
			}	

			$lists = json_decode($result);	
			$emma_lists = array();
			$html = $query = '';

			if( count( $lists ) < 1 ) {
                print_r(json_encode(array(
                    'status' => "error",
                    'message' => __( "You have zero lists in your " . $this->setting['name'] . " account. You must have at least one list before integration." , "smile" )
                )));
                exit();
            }
            
			ob_start();
			if( !empty( $lists ) ) {
			?>
				<label for="<?php echo $this->slug; ?>-list">Select List</label>
				<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
			<?php
				foreach($lists as $offset => $list) {
				?>
					<option value="<?php echo $list->member_group_id; ?>"><?php echo $list->group_name; ?></option>
				<?php
					$query .= $list->member_group_id.'|'.$list->group_name.',';
					$emma_lists[$list->member_group_id] = $list->group_name;
				}
				?>
				</select>
				<input type="hidden" id="mailer-all-lists" value="<?php echo $query; ?>"/>
				<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
				<input type="hidden" id="mailer-list-api" value="<?php echo $public_key; ?>"/>
			<?php
			} else {
			?>
				<label for="<?php echo $this->slug; ?>-list"><?php echo __( "You need at least one list added in " . $this->setting['name'] . " before proceeding.", "smile" ); ?></label>
			<?php
			}
			?>
			<div class="bsf-cnlist-form-row">
				<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="Emma" data-mailer="emma">
					<span>
						<?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?>
					</span>
				</div>
				<span class="spinner" style="float: none;"></span>
			</div>
			<?php 
			$html .= ob_get_clean();

			update_option($this->slug.'_public_key',$public_key);
			update_option($this->slug.'_priv_key',$private_key);
			update_option($this->slug.'_acc_id',$account_id);
			update_option($this->slug.'_lists', $emma_lists );		
			
			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));
			
			exit();
		}
		
		
		function disconnect_emma(){
			delete_option( $this->slug.'_public_key' );
			delete_option( $this->slug.'_priv_key' );
			delete_option( $this->slug.'_acc_id' );
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
                'message' => "disconnected"
			)));
			exit();
		}

		/*
		 * Function Name: get_emma_lists
		 * Function Description: Get MyEmma Mailer Campaign list
		 */

		function get_emma_lists( $account_id = '' , $public_key = '', $private_key = '' ) {
			if( $account_id != '' && $public_key != '' && $private_key != '' ) {
				// Give the API your information
				try {
					$emma = new Emma($account_id, $public_key, $private_key);
					
					if(!is_object($emma)) {
						return array();
						exit();
					}

					// Returns an array of all members
					$result = $emma->myGroups();
				} catch ( Exception $ex ) {
					return array();
				}
						

				$lists = json_decode($result);	

				$emma_lists = array();
				foreach($lists as $offset => $list) {
					$emma_lists[$list->member_group_id] = $list->group_name;
				}
				return $emma_lists;
			} else {
				return array();
			}
		}
	}
	new Smile_Mailer_Emma;	
}