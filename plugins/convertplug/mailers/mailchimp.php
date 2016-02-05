<?php
if(!class_exists('Smile_Mailer_Mailchimp')){
    class Smile_Mailer_Mailchimp{
        private $slug;
        private $setting;
        function __construct(){

            add_action( 'wp_ajax_get_mailchimp_data', array($this,'get_mailchimp_data' ));
            add_action( 'wp_ajax_update_mailchimp_authentication', array($this,'update_mailchimp_authentication' ));
            add_action( 'wp_ajax_disconnect_mailchimp', array($this,'disconnect_mailchimp' ));
            add_action( 'wp_ajax_mailchimp_add_subscriber', array($this,'mailchimp_add_subscriber' ));
            add_action( 'wp_ajax_nopriv_mailchimp_add_subscriber', array($this,'mailchimp_add_subscriber' ));
            add_action( 'admin_init', array( $this,'init' ) );
            $this->setting  = array(
                'name' => 'Mailchimp',
                'parameters' => array( 'api_key' ),
                'where_to_find_url' => 'http://kb.mailchimp.com/accounts/management/about-api-keys'
            );
            $this->slug = 'mailchimp';
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
        function get_mailchimp_data(){

            $isKeyChanged = false;

            $connected = false;
            ob_start();
            $mc_api = get_option($this->slug.'_api');

            if( $mc_api != '' ) {
                $dash_position = strpos( $mc_api, '-' );

                if( $dash_position !== false ) {
                    $api_url = 'https://' . substr( $mc_api, $dash_position + 1 ) . '.api.mailchimp.com/2.0/';
                } else {
                    return false;
                }
                $method = 'lists/list';
                $data['apikey'] = $mc_api;
                $url = $api_url . $method . '.json';

                $response = wp_remote_post( $url, array(
                    'body' => $data,
                    'timeout' => 15,
                    'headers' => array('Accept-Encoding' => ''),
                    'sslverify' => false
                    )
                );
                $body = wp_remote_retrieve_body( $response );

                $request = json_decode( $body );

                if( isset( $request->status ) ) {
                    if( $request->status == 'error' && $request->code == 104  ) {
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
                <label for="cp-list-name" ><?php _e( $this->setting['name'] . " API Key", "smile" ); ?></label>
                <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_api_key" name="<?php echo $this->slug; ?>-auth-key" value="<?php echo esc_attr( $mc_api ); ?>"/>
            </div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
                <?php
                if( $mc_api != '' && !$isKeyChanged ) {
                    $mc_lists = $this->get_mailchimp_lists( $mc_api );

                    if( !empty( $mc_lists ) ){
                        $connected = true;
                    ?>
                    <label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
                        <select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
                        <?php
                        foreach($mc_lists as $id => $name) {
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
                <?php if( $mc_api == "" ) { ?>
                    <button id="auth-<?php echo $this->slug; ?>" class="button button-secondary auth-button" disabled><?php _e( "Authenticate " . $this->setting['name'], "smile" ); ?></button><span class="spinner" style="float: none;"></span>
                <?php } else {
                        if( $isKeyChanged ) {
                ?>
                    <div id="update-<?php echo $this->slug; ?>" class="update-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Your credentials seems to be changed.</br>Use different '". $this->setting['name'] ."' credentials?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
                <?php
                        } else {
                ?>
                    <div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer button button-secondary" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
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
        * Add subscriber to mailchimp
        * @Since 1.0
        */
        function mailchimp_add_subscriber(){

            $post = $_POST;
            $data = array();
            $email = isset( $post['email'] ) ? $post['email'] : '';
            $only_conversion = isset( $post['only_conversion'] ) ? true : false;
            $api_key = get_option( 'mailchimp_api' );

            $on_success = isset( $post['message'] ) ? 'message' : 'redirect';
            $msg_wrong_email = ( isset( $post['msg_wrong_email']  )  && $post['msg_wrong_email'] !== '' ) ? $post['msg_wrong_email'] : __( 'Please enter correct email address.', 'smile' );

            $msg = isset( $_POST['message'] ) ? $_POST['message'] : __( 'Thank you.', 'smile' );

            if($on_success == 'message'){
                $action    = 'message';
                $url    = 'none';
            } else {
                $action    = 'redirect';
                $url    = $post['redirect'];
            }

            //    Check Email in MX records
            if( !$only_conversion ){
                $email_status = apply_filters('cp_valid_mx_email', $email );
            } else {
                $email_status = false;
            }
            if( $email_status ) {

                $status = 'success';

                $this->api_key = $api_key;
                $dash_position = strpos( $api_key, '-' );

                if( $dash_position !== false ) {
                    $this->api_url = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/';
                }

                $method = 'lists/subscribe';
                $data['apikey'] = $this->api_key;
                $data['id'] = $post['list_id'];
                $data['email'] = array(
                    'email' => $post['email']
                );

                if( isset( $post['name'] ) ){
                    $data['merge_vars'] = array(
                        'FNAME' => $post['name']
                    );
                }

                $contact = array();
                $contact['name'] = isset( $_POST['name'] ) ? $_POST['name'] : '';
                $contact['email'] = $_POST['email'];
                $contact['date'] = date("j-n-Y");

                $api_url = $this->api_url . $method . '.json';

                $response = wp_remote_post( $api_url, array(
                    'body' => $data,
                    'timeout' => 15,
                    'headers' => array('Accept-Encoding' => ''),
                    'sslverify' => false
                    )
                );

                // test for wp errors
                if( isset( $response['response']['code'] ) ) {
                    if( $response['response']['code'] != 200 ) {
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

                $body = wp_remote_retrieve_body( $response );
                $request = json_decode( $body );

                $style_id = $_POST['style_id'];
                $option = $_POST['option'];

                if( function_exists( "cp_add_subscriber_contact" ) ){
                    $isuserupdated = cp_add_subscriber_contact( $_POST['option'] ,$contact );
                }

                if ( !$isuserupdated ) {  // if user is updated don't count as a conversion
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
        function update_mailchimp_authentication(){
            $post = $_POST;
            $data = array();
            $api_key = $post['authentication_token'];
            $this->api_url = '';

            if( $api_key == "" ){
                print_r(json_encode(array(
                    'status' => "error",
                    'message' => __( "Please provide valid API Key for your ".$this->setting['name']." account.", "smile" )
                )));
                exit();
            }

            $this->api_key = $api_key;
            $dash_position = strpos( $api_key, '-' );

            if( $dash_position !== false ) {
                $this->api_url = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/';
            }
            $method = 'lists/list';
            $data['apikey'] = $this->api_key;
            $url = $this->api_url . $method . '.json';

            $response = wp_remote_post( $url, array(
                'body' => $data,
                'timeout' => 15,
                'headers' => array('Accept-Encoding' => ''),
                'sslverify' => false
                )
            );

            // test for wp errors
            if( is_wp_error( $response ) ) {

                print_r(json_encode(array(
                    'status' => "error",
                    'message' => "HTTP Error: " . $response->get_error_message()
                )));
                exit();
            }
            
            ob_start();
            $body = wp_remote_retrieve_body( $response );
            $request = json_decode( $body );
            
            if( isset( $request->status ) ) {
                if( $request->status == 'error' && $request->code == 104  ) {
                    print_r(json_encode(array(
                        'status' => "error",
                        'message' => $request->error
                    )));
                    exit();
                }
            }
            $lists = (array)$request->data;
            $mc_lists = array();
            $html = $query = '';
            
            if( count( $lists ) < 1 ) {
                print_r(json_encode(array(
                    'status' => "error",
                    'message' => __( "You have zero lists in your " . $this->setting['name'] . " account. You must have at least one list before integration." , "smile" )
                )));
                exit();
            }
            ?>
            
            <?php
            if( count( $lists ) > 0 ) {
            ?>
            <label for="<?php echo $this->slug; ?>-list">Select List</label>
            <select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
            <?php
                foreach($lists as $offset => $list) {
            ?>
                <option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
            <?php
                    $query .= $list->id.'|'.$list->name.',';
                    $mc_lists[$list->id] = $list->name;
                }
            ?>

            <?php
            } else {
            ?>
                <label for="<?php echo $this->slug; ?>-list"><?php echo __( "You need at least one list added in " . $this->setting['name'] . " before proceeding.", "smile" ); ?></label>
            <?php
            }
            ?>
            <div class="bsf-cnlist-form-row">
                <div id="disconnect-<?php echo $this->slug; ?>" class="disconnect-mailer" data-mailerslug="<?php echo $this->slug; ?>" data-mailer="<?php echo $this->setting['name']; ?>">
                    <span>
                        <?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?>
                    </span>
                </div>
                <span class="spinner" style="float: none;"></span>
            </div>
            <?php
            $html .= ob_get_clean();

            update_option($this->slug.'_api',$api_key);
            update_option($this->slug.'_lists',$mc_lists);

            print_r(json_encode(array(
                'status' => "success",
                'message' => $html
            )));

            exit();
        }

        /*
        * Disconnect mailchimp
        * @Since 1.0
        */
        function disconnect_mailchimp(){
            delete_option( 'mailchimp_api' );
            delete_option( 'mailchimp_lists' );

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
         * Function Name: get_mailchimp_lists
         * Function Description: Get Mailchimp list
         */

        function get_mailchimp_lists( $api_key = '' ) {
            if( $api_key != '' ) {
                $data = array();
                $dash_position = strpos( $api_key, '-' );

                if( $dash_position !== false ) {
                    $api_url = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/2.0/';
                } else {
                    return array();
                }
                $method = 'lists/list';
                $data['apikey'] = $api_key;
                $url = $api_url . $method . '.json';

                $response = wp_remote_post( $url, array(
                    'body' => $data,
                    'timeout' => 15,
                    'headers' => array('Accept-Encoding' => ''),
                    'sslverify' => false
                    )
                );

                // test for wp errors
                if( is_wp_error( $response ) ) {
                    return array();
                    exit;
                }

                $body = wp_remote_retrieve_body( $response );
                $request = json_decode( $body );
                if( isset( $request->status ) ) {
                    if( $request->status == 'error' && $request->code == 104 ){
                        return array();
                    }
                } else {
                    $lists = (array)$request->data;
                    $mc_lists = array();
                    if( count( $lists ) > 0 ) {
                        foreach($lists as $offset => $list) {
                            $mc_lists[$list->id] = $list->name;
                        }
                    }
                    return $mc_lists;
                }
            }
            return array();
        }
    }
    new Smile_Mailer_Mailchimp;
}