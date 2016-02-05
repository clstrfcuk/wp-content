<?php
if(!class_exists('Smile_Mailer_MadMimi')){
	class Smile_Mailer_MadMimi{

		private $slug;
		private $setting;
		function __construct(){

			require_once('api/madmimi/MadMimi.class.php');
			add_action( 'wp_ajax_get_madmimi_data', array($this,'get_madmimi_data' ));
			add_action( 'wp_ajax_update_madmimi_authentication', array($this,'update_madmimi_authentication' ));
			add_action( 'wp_ajax_disconnect_madmimi', array($this,'disconnect_madmimi' ));
			add_action( 'wp_ajax_madmimi_add_subscriber', array($this,'madmimi_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_madmimi_add_subscriber', array($this,'madmimi_add_subscriber' ));
			add_action( 'admin_init', array( $this,'init' ) );
			$this->setting  = array(
				'name' => 'Mad Mimi',
				'parameters' => array( 'api_key', 'email' ),
				'where_to_find_url' => 'http://help.madmimi.com/where-can-i-find-my-api-key/'
			);
			$this->slug = 'madmimi';
		}

		//Init function
		function init(){
			if( function_exists( 'cp_register_addon' ) ) {
				cp_register_addon( $this->slug, $this->setting );
			}
		}

		/*
		* retrieve mailer info data
		* @Since 1.0
		*/
		function get_madmimi_data(){
			$isKeyChanged = false;

			$connected = false;
			ob_start();
			$mm_api = get_option('madmimi_api');
			$mm_email = get_option('madmimi_email');
			if( $mm_api != '' ) {
				try{
					$mimi = new MadMimi($mm_email, $mm_api);
					$listsEncoded = $mimi->Lists();
				} catch( Exception $ex ) {
					$formstyle = '';
					$isKeyChanged = true;
				}
				if( $listsEncoded == 'Unable to authenticate' ) {
					$formstyle = '';
					$isKeyChanged = true;
				} else {
					$formstyle = 'style="display:none;"';
				}
			} else {
            	$formstyle = '';
			}
            ?>
            <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
				<label for="cp-email"><?php _e( "Email OR Username", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_email" name="<?php echo $this->slug; ?>-username" value="<?php echo esc_attr( $mm_email ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
	            <label for="cp-list-name" ><?php _e( $this->setting['name']." API Key", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_api_key" name="<?php echo $this->slug; ?>_api_key" value="<?php echo esc_attr( $mm_api ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
            <?php
            if( $mm_api != '' && !$isKeyChanged ) {
            	$mm_lists = ($mm_api != '' && !$isKeyChanged) ? $this->get_madmimi_lists($mm_email,$mm_api) : array();
				if( !empty( $mm_lists ) ){
					$connected = true;
				?>
					<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
					<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
				<?php
					foreach($mm_lists as $id => $name) {
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

            	<?php if( $mm_api == "" ) { ?>
	            	<button id="auth-<?php echo $this->slug; ?>" class="button button-secondary auth-button" disabled><?php _e( "Authenticate ".$this->setting['name'], "smile" ); ?></button><span class="spinner" style="float: none;"></span>
	            <?php } else {
	            		if( $isKeyChanged ) {
	            ?>
	            	<div id="update-<?php echo $this->slug; ?>" class="update-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Your credentials seems to be changed.</br>Use different '".$this->setting['name']."' credentials?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
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


		/*
		* Add subscriber to list
		* @Since 1.0
		*/
		function madmimi_add_subscriber(){
			$post = $_POST;
			$data = array();
			$email = isset( $post['email'] ) ? $post['email'] : '';
			$only_conversion = isset( $post['only_conversion'] ) ? true : false;

			$this->api_key = get_option('madmimi_api');
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
				$madmimiUser = get_option('madmimi_email');
				try{
					$mimi = new MadMimi($madmimiUser, $this->api_key);
					$addData = array(
							'first_name' => $name
					);

					$result = $mimi->AddMembership($list,$email,$addData);
					if( $result == 'Member could not be added to your auexitnce' || strpos( $result, 'does not exist' ) !== false ) {

						print_r(json_encode(array(
							'action' => $action,
							'email_status' => $email_status,
							'status' => 'error',
							'message' => __( "Something went wrong. Please try again.", "smile" ),
							'url' => $url,
						)));
						exit();
					}
				} catch( Exception $ex ) {
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
		* Authentication
		* @Since 1.0
		*/
		function update_madmimi_authentication(){
			$post = $_POST;
			$email = $_POST['email'];
			$data = array();
			$this->api_key = $post['authentication_token'];
			if( $post['authentication_token'] == "" ){
                print_r(json_encode(array(
                    'status' => "error",
                    'message' => __( "Please provide valid API Key for your ".$this->setting['name']." account.", "smile" )
                )));
                exit();
            }

			try{
				$mimi = new MadMimi($email, $this->api_key);

				if( $mimi->Promotions() === 'Unable to authenticate' ) {
					print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Unable to authenticate. Please check Username and API key", "smile" )
					)));
					exit();
				}

				$listsEncoded = $mimi->Lists();

				$lists = json_decode( $listsEncoded );
			} catch( Exception $ex ) {
				print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Something went wrong. Please try again.", "smile" )
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
			$mm_lists = array();
			$html = $query = '';
			?>
			<label for="<?php echo $this->slug; ?>-list" ><?php echo __( "Select List", "smile" ); ?></label>
			<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
			<?php
			foreach($lists as $offset => $list) {
			?>
				<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
				$query .= $list->id.'|'.$list->name.',';
				$mm_lists[$list->id] = $list->name;
			}
			?>
			</select>
			<input type="hidden" id="mailer-all-lists" value="<?php echo $query; ?>"/>
			<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
			<input type="hidden" id="mailer-list-api" value="<?php echo $this->api_key; ?>"/>

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
			update_option($this->slug.'_api',$this->api_key);
			update_option($this->slug.'_email',$email);
			update_option($this->slug.'_lists',$mm_lists);

			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));

			exit();
		}

		/*
		* Disconnect Mailer
		* @Since 1.0
		*/
		function disconnect_madmimi(){
			delete_option( 'madmimi_api' );
			delete_option( 'madmimi_email' );
			delete_option( 'madmimi_lists' );

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
		 * Function Name: get_madmimi_lists
		 * Function Description: Get MadMimi Mailer Campaign list
		 */

		function get_madmimi_lists( $email = '', $api_key = '' ) {
			if( $email != '' && $api_key != '' ) {
				try{
					$mimi = new MadMimi($email, $api_key);
					$listsEncoded = $mimi->Lists();
				} catch( Exception $ex ) {
					return array();
				}
				if( $listsEncoded == 'Unable to authenticate' ) {
					return array();
				}
				$lists = json_decode( $listsEncoded );

				if( $lists === NULL ) {
					return array();
					exit;
				}

				$mm_lists = array();
				foreach($lists as $offset => $list) {
					$mm_lists[$list->id] = $list->name;
				}
				return $mm_lists;
			} else {
				return array();
			}
		}
	}
	new Smile_Mailer_MadMimi;
}