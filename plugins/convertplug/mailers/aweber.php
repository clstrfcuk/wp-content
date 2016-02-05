<?php
if(!class_exists('Smile_Mailer_Aweber')){
	class Smile_Mailer_Aweber{
		private $slug;
		private $setting;
		private $appID;
		private $consumerKey;
		private $consumerSecret;

		function __construct(){

			add_action( 'wp_ajax_get_aweber_data', array($this,'get_aweber_data' ));
			add_action( 'wp_ajax_update_aweber_authentication', array($this,'update_aweber_authentication' ));
			add_action( 'wp_ajax_aweber_add_subscriber', array($this,'aweber_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_aweber_add_subscriber', array($this,'aweber_add_subscriber' ));
			add_action( 'wp_ajax_disconnect_aweber', array( $this, 'disconnect_aweber' ) );
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'Aweber',
				'parameters' => array( 'url', 'api_key' ),
				'where_to_find_url' => 'https://help.aweber.com/hc/en-us/articles/204031226-How-Do-I-Authorize-an-App'
			);
			$this->slug = 'aweber';
			$this->consumerSecret = "djL9NIUkfau3rteOhg4grrwRBfqx1lYzGQxVIyCb";
			$this->consumerKey = "AkyOnVQuJi9x5qKleb3JRgAV";
			$this->appID = 'f6c84f48';
		}

		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}

		// retrieve mailer info data
		function get_aweber_data(){

			$connected = false;
			ob_start();
			$credentials = get_option($this->slug.'_credentials');
			# prompt user to go to authorization URL

			?>
            	<div class="<?php echo $this->slug; ?>-auth" style="display:<?php echo ( !empty( $credentials ) ) ? 'none' : 'block'; ?>">
	                <div class="bsf-cnlist-form-row">
	                	<button class="button button-secondary auth-button auth-<?php echo $this->slug; ?>" onclick="window.open('https://auth.aweber.com/1.0/oauth/authorize_app/<?php echo $this->appID;?>','name','width=800,height=480')" ><?php _e( "Authenticate ".$this->setting['name'], "smile" ); ?></button>
	                </div>
	                <div class="bsf-cnlist-form-row">
		                <label for="authentication_token"><?php _e( "Enter the authorization  code:", "smile" ); ?></label>
		                <input type="text" autocomplete="off" id="authentication_token"/>
		            </div>
                </div>

                <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
	                <?php
	                if ( $credentials !== '' ) {
	                	$aweber_lists = $this->get_aweber_lists();

	                	if( !empty( $aweber_lists ) ){
		                	$connected = true;
		                ?>
		                    <label for="<?php $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
		                    <select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
		                <?php
		                    foreach($aweber_lists as $id => $name) {
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
					<?php if( $credentials ) { ?>
						<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?></span></div>
					    <span class="spinner" style="float: none;"></span>
					<?php } else { ?>
						<button class="button button-secondary auth-button get_aweber_data" disabled><?php _e( "Connect to ".$this->setting['name'], "smile" ); ?></button>
					    <span class="spinner" style="float: none;"></span>
					<?php } ?>
				</div>

			<?php
            $content = ob_get_clean();

            $result['data'] = $content;
            $result['helplink'] = $this->setting['where_to_find_url'];
            $result['isconnected'] = $connected;
            echo json_encode($result);
            die();

		}

		function aweber_add_subscriber(){
			$data = $_POST;
			$email = isset( $data['email'] ) ? $data['email'] : '';
			$only_conversion = isset( $data['only_conversion'] ) ? true : false;

			require_once(AWEBER_API_URI.'aweber_api.php');
			$credentials = get_option($this->slug.'_credentials');

			$consumerKey    = $credentials[0]; # put your credentials here
			$consumerSecret = $credentials[1]; # put your credentials here
			$accessKey      = $credentials[2]; # put your credentials here
			$accessSecret   = $credentials[3]; # put your credentials here
			$list_id        = $data['list_id']; # put the List ID here

			$aweber = new AWeberAPI($consumerKey, $consumerSecret);
			$account = $aweber->getAccount($accessKey, $accessSecret);
			$listURL = $account->url."/lists/{$list_id}";
			$on_success = isset( $_POST['message'] ) ? 'message' : 'redirect';
			$msg_wrong_email = ( isset( $_POST['msg_wrong_email']  )  && $_POST['msg_wrong_email'] !== '' ) ? $_POST['msg_wrong_email'] : __( 'Please enter correct email address.', 'smile' );
			$msg = isset( $data['message'] ) ? $data['message'] : __( 'Thank you.', 'smile' );

			if($on_success == 'message'){
				$action	= 'message';
				$url	= 'none';
			} else {
				$action	= 'redirect';
				$url	= $data['redirect'];
			}

			$contact = array();
			$contact['name'] = isset( $data['name'] ) ? $data['name'] : '';
			$contact['email'] = $data['email'];
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

					$list = $account->loadFromUrl($listURL);

					# create a subscriber
					$name = isset( $data['name'] ) ? $data['name'] : '';
					$params = array(
						'email' => $data['email'],
						'name' => $name,
					);

					$subscribers = $list->subscribers;
					$new_subscriber = $subscribers->create($params);

				} catch(AWeberAPIException $exc) {

					print_r(json_encode(array(
						'action' => $action,
						'email_status' => $email_status,
						'status' => 'error',
						'message' => __( "Something went wrong. Please try again.", "smile"),
						'url' => $url,
					)));
					die();
				}

				$style_id = $data['style_id'];
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

			print_r(json_encode(array(
				'action' => $action,
				'email_status' => $email_status,
				'status' => $status,
				'message' => $msg,
				'url' => $url,
			)));

			die();
		}

		function update_aweber_authentication(){
			$data = $_POST;

			require_once(AWEBER_API_URI.'aweber_api.php');
			// Replace with the keys of your application
			// NEVER SHARE OR DISTRIBUTE YOUR APPLICATIONS'S KEYS!
			//$consumerKey    = "AkyOnVQuJi9x5qKleb3JRgAV";
			//$consumerSecret = "djL9NIUkfau3rteOhg4grrwRBfqx1lYzGQxVIyCb";

			$code = $data['authentication_token'];

			try{
				$this->application = new AWeberAPI($this->consumerKey, $this->consumerSecret);
				$credentials = AWeberAPI::getDataFromAweberID($code);

				$account = $this->application->getAccount($credentials[2], $credentials[3]);
				$html = $query = '';
				$aweber_lists = array();

				if( count( $account->lists ) < 1 ) {
	                print_r(json_encode(array(
	                    'status' => "error",
	                    'message' => __( "You have zero lists in your " . $this->setting['name'] . " account. You must have at least one list before integration." , "smile" )
	                )));
	                exit();
	            }
				ob_start();
				if( count( $account->lists ) > 0 ){
				?>
					<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
					<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
					<?php
					foreach($account->lists as $offset => $list) {
					?>
						<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
					<?php
						$query .= $list->id.'|'.$list->name.',';
						$aweber_lists[$list->id] = $list->name;
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
				<input type="hidden" id="mailer-list-action" value="update_aweber_list"/>
				<div class="bsf-cnlist-form-row">
					<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>">
						<span>
							<?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?></span>
					</div>
					<span class="spinner" style="float: none;"></span>
				</div>
				<?php
				$html .= ob_get_clean();

				update_option($this->slug.'_credentials',$credentials);
				update_option($this->slug.'_lists',$aweber_lists);

				print_r(json_encode(array(
					'status' => "success",
					'message' => $html
				)));

			} catch(AWeberAPIException $exc) {
				print_r(json_encode(array(
						'status'  => "error",
						'message' => __( "Please provide valid authorization code for connecting to Aweber.", "smile" )
				)));
			}
			die();
		}


		function disconnect_aweber(){
			delete_option( 'aweber_credentials' );
			delete_option( 'aweber_lists' );

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
                'message' => 'disconnected'
			)));
			die();
		}

		/*
		 * Function Name: get_aweber_lists
		 * Function Description: Get aweber Mailer Campaign list
		 */

		function get_aweber_lists() {
			require_once(AWEBER_API_URI.'aweber_api.php');
			//$consumerKey    = "AkyOnVQuJi9x5qKleb3JRgAV";
			//$consumerSecret = "djL9NIUkfau3rteOhg4grrwRBfqx1lYzGQxVIyCb";

			$application = new AWeberAPI($this->consumerKey, $this->consumerSecret);

			$credentials = get_option('aweber_credentials');

			try{
				$account = $application->getAccount($credentials[2], $credentials[3]);
				$html = $query = '';
				$aweber_lists = array();
				foreach($account->lists as $offset => $list) {
					$aweber_lists[$list->id] = $list->name;
				}

				return $aweber_lists;
			} catch(AWeberAPIException $exc) {
				return false;
			}
			return array();
		}
	}
	new Smile_Mailer_Aweber;
}