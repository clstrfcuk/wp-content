<?php
if(!class_exists('Smile_Mailer_ActiveCampaign')){
	class Smile_Mailer_ActiveCampaign{

		private $slug;
		private $setting;

		function __construct(){

			require_once('api/activecamp_api/ActiveCampaign.class.php');
			require_once('api/activecamp_api/Auth.class.php');
			add_action( 'wp_ajax_get_activecampaign_data', array($this,'get_activecampaign_data' ));
			add_action( 'wp_ajax_update_activecampaign_authentication', array($this,'update_activecampaign_authentication' ));
			add_action( 'wp_ajax_disconnect_activecampaign', array($this,'disconnect_activecampaign' ));
			add_action( 'wp_ajax_activecampaign_add_subscriber', array($this,'activecampaign_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_activecampaign_add_subscriber', array($this,'activecampaign_add_subscriber' ));
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'Active Campaign',
				'parameters' => array( 'url', 'api_key' ),
				'where_to_find_url' => 'http://www.activecampaign.com/help/using-the-api/'
			);
			$this->slug = 'activecampaign';
		}

		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}


		// retrieve mailer info data
		function get_activecampaign_data() {
			$isKeyChanged = false;
			$connected = false;
			ob_start();
			$ac_api = get_option( $this->slug . '_api' );
			$ac_url = get_option( $this->slug . '_url' );

			if( $ac_api != '' ) {
	            try {
	            	$ac = new ActiveCampaign($ac_url, $ac_api);
					if( !(int)$ac->credentials_test() ) {
						$formstyle = '';
						$isKeyChanged = true;
					} else {
						$formstyle = 'style="display:none;"';
					}
	            } catch( Exception $ex ) {
	            	$formstyle = '';
					$isKeyChanged = true;
	            }

			} else {
            	$formstyle = '';
			}
            ?>

			<div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
				<label for="<?php echo $this->slug; ?>-list-name"><?php _e( $this->setting['name'] . " API URL", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_url" name="<?php echo $this->slug; ?>_url" value="<?php echo esc_attr( $ac_url ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="<?php echo $this->slug; ?>-list-name"><?php _e( $this->setting['name']." API Key", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_api_key" name="<?php echo $this->slug; ?>-auth-key" value="<?php echo esc_attr( $ac_api ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
	        <?php
	        if( $ac_api != '' && !$isKeyChanged ) {
	            $ac_lists = ($ac_api != '' && !$isKeyChanged) ? $this->get_activecampaign_lists($ac_api,$ac_url) : array();

					if( !empty( $ac_lists ) ) {
						$connected = true;
					?>
					<label for="<?php echo $this->slug;?>-list"><?php echo __( "Select List", "smile" ); ?></label>
						<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
					<?php
						foreach($ac_lists as $id => $name) {
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

            	<?php if( $ac_api == "" ) { ?>
	            	<button id="auth-<?php echo $this->slug; ?>" class="button button-secondary auth-button" disabled><?php _e( "Authenticate ".$this->setting['name'],"smile" ); ?></button><span class="spinner" style="float: none;"></span>
	            <?php } else {
	            		if( $isKeyChanged ) {
	            ?>
	            	<div id="update-<?php echo $this->slug; ?>" class="update-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Your credentials seems to be changed.</br>Use different '" . $this->setting['name'] . " credentials?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
	            <?php
	            		} else {
	            ?>
	            	<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->slug; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Use different '" . $this->setting['name'] . "' account?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
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

		function activecampaign_add_subscriber(){

			$post = $_POST;
			$email = isset( $post['email'] ) ? $post['email'] : '';
			$only_conversion = isset( $post['only_conversion'] ) ? true : false;
			$this->api_key = get_option($this->slug.'_api');
			$campurl = get_option($this->slug.'_url');
			$name = isset( $_POST['name'] ) ? $_POST['name'] : '';
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

			$data = array(
				"email"           => $email,
				"first_name"      => $name,
				"last_name"       => "",
				"p[{$list}]"      => $list,
				"status[{$list}]" => 1, // "Active" status
			);

			//	Check Email in MX records
			if( !$only_conversion ){
				$email_status = apply_filters('cp_valid_mx_email', $email );
			} else {
				$email_status = false;
			}
			if($email_status) {

				$status = 'success';

				try {
					// Add user to contacts if MX rexord is valid
					$ac = new ActiveCampaign($campurl, $this->api_key);

					// sync contacts with mailer
					$contact_sync = $ac->api("contact/sync", $data);
				} catch( Exception $ex ) {
					print_r(json_encode(array(
						'action' => $action,
						'email_status' => $email_status,
						'status' => 'error',
						'message' => __( "Something went wrong. Please try again.", "smile"),
						'url' => $url,
					)));
					exit();
				}
					

				if( !is_object($contact_sync) || ( is_object($contact_sync) && !(int)$contact_sync->success ) ) {

					print_r(json_encode(array(
						'action' => $action,
						'email_status' => $email_status,
						'status' => 'error',
						'message' => __( "Something went wrong. Please try again.", "smile"),
						'url' => $url,
					)));
					exit();

				} else {

					$style_id = $_POST['style_id'];
					$option = $_POST['option'];

					// add user to central contacts database
					if( function_exists( "cp_add_subscriber_contact" ) ){
						$isuserupdated = cp_add_subscriber_contact( $option ,$contact );
					}

					if ( !$isuserupdated ) {  // if user is updated dont count as a conversion
							// update conversions
							smile_update_conversions($style_id);
					}
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

		function update_activecampaign_authentication(){
			$post = $_POST;
			$data = array();
			$this->api_key = $post['authentication_token'];
			$campurl = $_POST['campaingURL'];


			if( $post['authentication_token'] == "" ){
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please provide valid API Key for your " . $this->setting['name'] . " account.", "smile" )
				)));
				exit();
			}
			if( $post['campaingURL'] == "" ){
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please provide valid Campaign URL for your " . $this->setting['name'] . " account.", "smile" )
				)));
				exit();
			}

			try {
				$ac = new ActiveCampaign($campurl, $this->api_key);

				if (!(int)$ac->credentials_test()) {

					print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Access denied: Invalid credentials (URL and/or API key).", "smile" )
					)));
					exit();
				}

				$param = array(
					"api_action" => "list_list",
					"api_key"    => $this->api_key,
					"ids"   => "all",
					"full" => 0
				);

				$lists = $ac->api("list/list_", $param);
			} catch( Exception $ex ) {
				print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Something went wrong. Please try again.", "smile" )
					)));
					exit();
			}
				

			if( $lists->result_code == 0 ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "You have zero lists in your " . $this->setting['name'] . " account. You must have at least one list before integration." , "smile" )
				)));
				exit();
			}
			ob_start();
			$ac_lists = array();
			$html = $query = '';
			if( !empty( $lists ) ) {
			?>
				<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
				<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">';
				<?php
				foreach( $lists as $offset => $list ) {
					if( isset($list->id) ) {
				?>
						<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
				<?php
						$query .= $list->id.'|'.$list->name.',';
						$ac_lists[$list->id] = $list->name;
					}
				}
				?>
				</select>
			<?php
			} else {
			?>
				<label for="<?php echo $this->slug; ?>-list"><?php echo __( "You need at least one list added in " . $this->setting['name'] . " before proceeding.", "smile" ); ?></label>
			<?php
			}
			?>
				
			<input type="hidden" id="mailer-all-lists" value="<?php echo esc_attr($query); ?>"/>
			<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
			<input type="hidden" id="mailer-list-api" value="<?php echo esc_attr( $this->api_key ); ?>"/>
			<div class="bsf-cnlist-form-row">
				<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->slug; ?>" data-mailer="<?php echo $this->slug; ?>">
					<span>
						<?php _e( "Use different '" . $this->setting['name'] . "' account?", "smile" ); ?>
					</span>
				</div>
				<span class="spinner" style="float: none;"></span>
			</div>
			<?php
			$html .= ob_get_clean();

			update_option($this->slug.'_url',$campurl);
			update_option($this->slug.'_api',$this->api_key);
			update_option($this->slug.'_lists',$ac_lists);

			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));

			exit();
		}

		function disconnect_activecampaign(){
			delete_option( $this->slug.'_api' );
			delete_option( $this->slug.'_url' );
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
		 * Function Name: get_activecampaign_lists
		 * Function Description: Get ActiveCampaign Mailer Campaign list
		 */

		function get_activecampaign_lists( $api_key = '', $url = '' ) {
			if( $api_key != '' && $url != '' ) {

				try{
					$ac = new ActiveCampaign($url, $api_key);
					$param = array(
						"api_action" => "list_list",
						"api_key"    => $api_key,
						"ids"   => "all",
						"full" => 0
					);

					$lists = $ac->api("list/list_", $param);
				} catch( Exception $ex ) {
					return array();
				}
					

				$ac_lists = array();
				if( !empty( $lists ) ){
					foreach($lists as $offset => $list) {
						if(isset($list->id))
							$ac_lists[$list->id] = $list->name;
					}
					return $ac_lists;
				} else {
					return array();
				}
				
			}
			return array();
		}
	}
	new Smile_Mailer_ActiveCampaign;
}