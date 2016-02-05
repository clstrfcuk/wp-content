<?php
if(!class_exists('Smile_Mailer_iContact')){
	class Smile_Mailer_iContact{

		private $slug;
		private $setting;
		function __construct(){

			require_once('api/icontact/iContactApi.php');
			add_action( 'wp_ajax_get_icontact_data', array($this,'get_icontact_data' ));
			add_action( 'wp_ajax_update_icontact_authentication', array($this,'update_icontact_authentication' ));
			add_action( 'wp_ajax_disconnect_icontact', array($this,'disconnect_icontact' ));
			add_action( 'wp_ajax_icontact_add_subscriber', array($this,'icontact_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_icontact_add_subscriber', array($this,'icontact_add_subscriber' ));
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'iContact',
				'parameters' => array( 'app_id', 'email', 'pass' ),
				'where_to_find_url' => 'http://www.icontact.com/developerportal/documentation/register-your-app/'
			);
			$this->slug = 'icontact';
		}

		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}

		/*
		* retrieve mailer info
		* @Since 1.0
		*/
		function get_icontact_data(){
			$isKeyChanged = false;
			$connected = false;
			ob_start();
			$ic_app_id = get_option('icontact_app_id');
			$ic_app_user = get_option('icontact_app_user');
			$ic_app_pass = get_option('icontact_app_pass');

			if( $ic_app_id != '' ) {
				iContactApi::getInstance()->setConfig(array(
					'appId'       => $ic_app_id,
					'apiPassword' => $ic_app_pass,
					'apiUsername' => $ic_app_user
				));

				$oiContact = iContactApi::getInstance();
				try {
					// try to get all lists
					$lists = $oiContact->getLists();
					$formstyle = 'style="display:none;"';

				} catch (Exception $oException) { // Catch any exceptions
					// Dump errors
					$isKeyChanged = true;
					$formstyle = '';
				}
			} else {
            	$formstyle = '';
			}
            ?>

			<div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-list-name" ><?php _e( $this->setting['name']." App ID", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_app_id" name="<?php echo $this->slug; ?>_app_id" value="<?php echo esc_attr( $ic_app_id ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-email"><?php _e( $this->setting['name']." App Username", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_email" name="<?php echo $this->slug; ?>-username" value="<?php echo esc_attr( $ic_app_user ); ?>"/>
	        </div>

	        <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-password"><?php _e( $this->setting['name']." App Password", "smile" ); ?></label>
	            <input type="password" autocomplete="off" id="<?php echo $this->slug; ?>_pass" name="<?php echo $this->slug; ?>-password" value="<?php echo esc_attr( $ic_app_pass ); ?>"/>
	        </div>

	        <div class="bsf-cnlist-form-row">
	            <div class="<?php echo $this->slug; ?>-list">
	            <?php
	            if($ic_app_id != '' && !$isKeyChanged) {
	            	$ic_lists = $this->get_icontact_lists($ic_app_id,$ic_app_user,$ic_app_pass);

	            	if( !empty( $ic_lists ) ){
						$connected = true;
					?>
						<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
						<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
					<?php
						foreach($ic_lists as $id => $name) {
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
	        </div>

	        <div class="bsf-cnlist-form-row">

	        	<?php if( $ic_app_id == "" ) { ?>
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

		function icontact_add_subscriber(){
			$post = $_POST;
			$data = array();
			$email = isset( $post['email'] ) ? $post['email'] : '';
			$only_conversion = isset( $post['only_conversion'] ) ? true : false;
			$appID = get_option($this->slug.'_app_id');
			$appPass = get_option($this->slug.'_app_pass');
			$appUsername = get_option($this->slug.'_app_user' );

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
				// Give the API your information
				iContactApi::getInstance()->setConfig(array(
					'appId'       => $appID,
					'apiPassword' => $appPass,
					'apiUsername' => $appUsername
				));

				$oiContact = iContactApi::getInstance();

				try {

					$result = $oiContact->addContact($email, null, null, $name, '');
					$contact = $result->contactId;
					$result = $oiContact->subscribeContactToList($contact, $list, 'normal');
					if( empty( $result ) ) {
						print_r(json_encode(array(
							'action' => $action,
							'email_status' => $email_status,
							'status' => 'error',
							'message' => __( "Something went wrong. Please try again.", "smile" ),
							'url' => $url,
						)));
						exit();
					}
				} catch (Exception $oException) {

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
			exit();
		}

		function update_icontact_authentication(){

			$appID = $_POST['appID'];
			$appUsername = $_POST['appUser'];
			$appPass = $_POST['appPass'];

			if($appID == '' || $appUsername === '' || $appPass === '') {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please enter all credentails from above fields", "smile" )
				)));
				exit();
			}

			// Give the API your information
			iContactApi::getInstance()->setConfig(array(
				'appId'       => $appID,
				'apiPassword' => $appPass,
				'apiUsername' => $appUsername
			));

			$oiContact = iContactApi::getInstance();

			try {
				// try to get all lists
				$lists = $oiContact->getLists();

			} catch (Exception $oException) { // Catch any exceptions
				// Dump errors

				$errors = $oiContact->getErrors();
				print_r(json_encode(array(
					'status' => "error",
					'message' => $errors[0]
				)));

				exit();

			}

			if( empty($lists) ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "You have zero lists in your " . $this->setting['name'] . " account. You must have at least one list before integration." , "smile" )
				)));
				exit();
			}
			ob_start();
			$ic_lists = array();
			$html = $query = '';
			if( !empty($lists) ) {
			?>
				<label for="<?php echo $this->slug; ?>-list"  >Select List</label>
				<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
				<?php
				foreach($lists as $offset => $list) {
				?>
					<option value="<?php echo $list->listId; ?>"><?php echo $list->name; ?></option>
				<?php
					$query .= $list->listId.'|'.$list->name.',';
					$ic_lists[$list->listId] = $list->name;
				}
				?>
				</select>
				<input type="hidden" id="mailer-all-lists" value="<?php echo $query; ?>"/>
				<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
				<input type="hidden" id="mailer-list-api" value="<?php echo $appID; ?>"/>
			<?php
			} else {
			?>
				<label for="<?php echo $this->slug; ?>-list"><?php echo __( "You need at least one list added in " . $this->setting['name'] . " before proceeding.", "smile" ); ?></label>
			<?php
			}
			?>
			<div class="bsf-cnlist-form-row">
				<div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>">
					<span>
						<?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?>
					</span>
				</div>
				<span class="spinner" style="float: none;"></span>
			</div>
			<?php
			$html .= ob_get_clean();

			update_option($this->slug.'_app_id',$appID);
			update_option($this->slug.'_app_user',$appUsername);
			update_option($this->slug.'_app_pass',$appPass);
			update_option($this->slug.'_lists', $ic_lists );

			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));

			exit();
		}


		function disconnect_icontact(){
			delete_option( $this->slug.'_app_id' );
			delete_option( $this->slug.'_app_user' );
			delete_option( $this->slug.'_app_pass' );
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
		 * Function Name: get_icontact_lists
		 * Function Description: Get iContact Mailer Campaign list
		 */

		function get_icontact_lists( $ic_app_id = '', $ic_app_user = '', $ic_app_pass = '' ) {
			if( $ic_app_id != '' && $ic_app_user != '' && $ic_app_pass != '' ) {
				// Give the API your information
				iContactApi::getInstance()->setConfig(array(
					'appId'       => $ic_app_id,
					'apiPassword' => $ic_app_pass,
					'apiUsername' => $ic_app_user
				));

				$oiContact = iContactApi::getInstance();

				try {
					// try to get all lists
					$lists = $oiContact->getLists();

				} catch (Exception $oException) { // Catch any exceptions
					// Dump errors
					return array();
					exit();
				}

				$ic_lists = array();
				foreach($lists as $offset => $list) {
					$ic_lists[$list->listId] = $list->name;
				}
				return $ic_lists;
			} else {
				return array();
			}
		}
	}
	new Smile_Mailer_iContact;
}