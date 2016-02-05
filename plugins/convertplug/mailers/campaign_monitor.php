<?php
if(!class_exists('Smile_Mailer_CampaignMonitor')){
	class Smile_Mailer_CampaignMonitor{
		private $slug;
		private $setting;
		function __construct(){

			require_once('api/campaign_api/csrest_general.php');
			require_once('api/campaign_api/csrest_clients.php');
			require_once('api/campaign_api/csrest_subscribers.php');
			add_action( 'wp_ajax_get_campaignmonitor_data', array($this,'get_campaignmonitor_data' ));
			add_action( 'wp_ajax_update_campaignmonitor_authentication', array($this,'update_campaignmonitor_authentication' ));
			add_action( 'wp_ajax_disconnect_campaignmonitor', array($this,'disconnect_campaignmonitor' ));
			add_action( 'wp_ajax_campaignmonitor_add_subscriber', array($this,'campaignmonitor_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_campaignmonitor_add_subscriber', array($this,'campaignmonitor_add_subscriber' ));
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'Campaign Monitor',
				'parameters' => array( 'client_id', 'api_key' ),
				'where_to_find_url' => 'https://www.campaignmonitor.com/api/getting-started/?&_ga=1.18810747.338212664.1439118258#clientid'
			);
			$this->slug = 'campaignmonitor';
		}

		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}

		/*
		 * Function Name: get_campaignmonitor_data
		 * Function Description: Get canpaign monitor input fields
		 */

		function get_campaignmonitor_data(){

			$connected = false;
			$isKeyChanged = false;
			ob_start();
			$campaignmonitor_api = get_option($this->slug.'_api');
			$campaignmonitor_client_id = get_option($this->slug.'_client_id');

			if( $campaignmonitor_api != '' ) {
				try {
					$auth = array( 'api_key' => $campaignmonitor_api );
					$wrap = new CS_REST_General( $auth );
					$res = $wrap->get_clients();
					if(!$res->was_successful()) {
						$isKeyChanged = true;
						$formstyle = '';
					} else {
						$formstyle = 'style="display:none;"';
					}
				} catch( Exception $ex ) {
					$isKeyChanged = true;
					$formstyle = '';
				}	
			} else {
            	$formstyle = '';
            }
            ?>

			<div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
				<label for="<?php echo $this->slug; ?>_client_id"><?php _e( "Client ID", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_client_id" name="<?php echo $this->slug; ?>_client_id" value="<?php echo esc_attr( $campaignmonitor_client_id ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
				<label for="campaignmonitor_api_key"><?php _e( $this->setting['name']." API Key", "smile" ); ?></label>
				<input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_api_key" name="<?php echo $this->slug; ?>_api_key" value="<?php echo esc_attr( $campaignmonitor_api ); ?>"/>
			</div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
            <?php
            if( $campaignmonitor_api != '' && !$isKeyChanged ) {
            	$cm_lists = ( $campaignmonitor_api != '' && !$isKeyChanged ) ? $this->get_campaignmonitor_lists( $campaignmonitor_api, $campaignmonitor_client_id ) : array();

				if( !empty( $cm_lists ) ) {
					$connected = true;
				?>
					<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
					<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
				<?php
					foreach($cm_lists as $id => $name) {
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
	            <?php if( $campaignmonitor_api == "" ) { ?>
	            	<button id="auth-<?php echo $this->slug; ?>" class="button button-secondary auth-button" disabled><?php _e( "Authenticate " . $this->setting['name'], "smile" ); ?></button><span class="spinner" style="float: none;"></span>
	            <?php } else {
	            		if( $isKeyChanged ) {
	            ?>
	            	<div id="update-<?php echo $this->slug; ?>" class="update-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Your credentials seems to be changed.</br>Use different '" . $this->setting['name'] . "' credentials?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
	            <?php
	            		} else {
	            ?>
	            	<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer button button-secondary" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Use different '" . $this->setting['name'] . "' account?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
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

		/*
		 * Function Name: campaignmonitor_add_subscriber
		 * Function Description: Add subscriber
		 */

		function campaignmonitor_add_subscriber(){
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
				try{
					$auth = array('api_key' => $this->api_key);
					$wrap = new CS_REST_Subscribers($list, $auth);

					$result = $wrap->add(array(
					    'EmailAddress' => $email,
					    'Name' => $name,
					    'Resubscribe' => true
					));

					if (!$result->was_successful()) {

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

						if( function_exists( "cp_add_subscriber_contact" ) ){
							$isuserupdated = cp_add_subscriber_contact( $option ,$contact );
						}

						if ( !$isuserupdated ) {  // if user is updated dont count as a conversion
								// update conversions
								smile_update_conversions($style_id);
						}
					}
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


		/*
		 * Function Name: update_campaignmonitor_authentication
		 * Function Description: Update Campaign Monitor values to ConvertPlug
		 */

		function update_campaignmonitor_authentication(){
			$post = $_POST;

			if( $post[$this->slug . '_api_key'] == "" ){
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please provide valid API Key for your " . $this->setting['name'] . " account.", "smile" )
				)));
				exit();
			}
			if( $post[$this->slug . '_client_id'] == "" ){
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please provide valid Client ID for your " . $this->setting['name'] . " account.", "smile" )
				)));
				exit();
			}

			$data = array();
			$campaignmonitor_api_key = $post[$this->slug . '_api_key'];
			$campaignmonitor_client_id = $post[$this->slug . '_client_id'];

			$this->api_key = $campaignmonitor_api_key;
			try{
				$auth = array('api_key' => $this->api_key);
				$wrap = new CS_REST_General($auth);
				$result = $wrap->get_clients();

				if(!$result->was_successful()) {
					print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Unable to authenticate. Please check client ID and API key.", "smile" )
					)));
					exit();
				}

				$wrap = new CS_REST_Clients( $campaignmonitor_client_id, $auth );
				$lists = $wrap->get_lists();

				if( $lists->http_status_code != 200 ) {
					print_r(json_encode(array(
						'status' => "error",
						'message' => __( $lists->response->Message, "smile" )
					)));
					exit();
				}
				$lists = $lists->response;

			} catch( Exception $ex ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Something went wrong. Please try again.", "smile" )
				)));
				exit();
			}

			
			$cm_lists = array();
			$html = $query = '';

			if( count( $lists ) < 1 ) {
                print_r(json_encode(array(
                    'status' => "error",
                    'message' => __( "You have zero lists in your " . $this->setting['name'] . " account. You must have at least one list before integration." , "smile" )
                )));
                exit();
            }
			ob_start();
			if( count( $lists ) > 0 ) {
			?>
				<label for="<?php echo $this->slug; ?>-list">Select List</label>
				<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
				<?php
				foreach($lists as $offset => $list) {
				?>
					<option value="<?php echo $list->ListID; ?>"><?php echo $list->Name; ?></option>
				<?php
					$query .= $list->ListID.'|'.$list->Name.',';
					$cm_lists[$list->ListID] = $list->Name;
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
			<input type="hidden" id="mailer-all-lists" value="<?php echo $query; ?>"/>
			<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
			<input type="hidden" id="mailer-list-api" value="<?php echo $this->api_key; ?>"/>

			<div class="bsf-cnlist-form-row">
				<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>">
					<span>
						<?php _e( "Use different '" . $this->setting['name'] . "' account?", "smile" ); ?>
					</span>
				</div>
				<span class="spinner" style="float: none;"></span>
			</div>
			<?php
			$html .= ob_get_clean();

			update_option($this->slug.'_client_id',$campaignmonitor_client_id);
			update_option($this->slug.'_api',$campaignmonitor_api_key);
			update_option($this->slug.'_lists',$cm_lists);

			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));

			exit();
		}

		/*
		 * Function Name: disconnect_campaignmonitor
		 * Function Description: Disconnect current Campaign Monitor from wp instance
		 */

		function disconnect_campaignmonitor(){
			delete_option( $this->slug . '_api' );
			delete_option( $this->slug . '_client_id' );
			delete_option( $this->slug . '_lists' );

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
                'message' => __( "disconnected", "smile" )
			)));
			exit();
		}

		/*
		 * Function Name: get_campaignmonitor_lists
		 * Function Description: Get Campaign Monitor Mailer Campaign list
		 */

		function get_campaignmonitor_lists( $api_key = '', $client_id = '' ) {
			if( $api_key != '' && $client_id != '' ) {
				try{
					$auth = array('api_key' => $api_key);
					$wrap = new CS_REST_General($auth);
					$result = $wrap->get_clients();

					if(!$result->was_successful()) {
						return array();
					}
					$wrap = new CS_REST_Clients($client_id, $auth);
					$lists = $wrap->get_lists();
					if( $lists->http_status_code != 200 ) {
						return array();
					}
					$lists = $lists->response;
				} catch( Exception $ex ){
					return array();
				}
				if( count( $lists ) > 0 ) {
					$cm_lists = array();
					foreach($lists as $offset => $list) {
						$cm_lists[$list->ListID] = $list->Name;
					}
					return $cm_lists;
				} else {
					return array();
				}
			} else {
				return array();
			}
		}
	}
	new Smile_Mailer_CampaignMonitor;
}