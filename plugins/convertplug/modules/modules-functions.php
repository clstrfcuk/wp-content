<?php
if( !function_exists( 'cp_get_form_hidden_fields' ) ) {
	function cp_get_form_hidden_fields( $a ){
		/** = Form options
		 *	Mailer - We will also optimize this by filter. If in any style we need the form then apply filter otherwise nope.
		 *-----------------------------------------------------------*/
		
		$mailer 		= explode( ":",$a['mailer'] );

		$mailer_id = $list_id = '';
		if( $a['mailer'] !== '' && $a['mailer'] != "custom-form" ) {
		    $smile_lists = get_option('smile_lists');

		    $list = ( isset( $smile_lists[$a['mailer']] ) ) ? $smile_lists[$a['mailer']] : '';
		    $mailer = ( $list != '' ) ? $list['list-provider'] : '';
		    $listName = ( $list != '' ) ? str_replace(" ","_",strtolower( trim( $list['list-name'] ) ) ) : '';

		    if( $mailer == 'Convert Plug' ) {
		        $mailer_id = 'cp';
		        $list_id = $a['mailer'];
		        $data_option = "cp_connects_".$listName;
		    } else {
		        $mailer_id = strtolower($mailer);
		        $list_id = ( $list != '' ) ? $list['list'] : '';
		        $data_option = "cp_".$mailer_id."_".$listName;
		    }
		    $on_success_action 	= ($a['on_success'] == "redirect") ? $a['redirect_url'] : $a['success_message'] ;
		}
		ob_start();
		if( isset( $a['button_conversion'] ) ) {
			if( $a['button_conversion'] == 1 ) {
		?>
		<input type="hidden" name="only_conversion" value="true" />
		<?php
			}
		}
		?>
		<input type="hidden" name="list_parent_index" value="<?php echo isset( $a['mailer'] ) ? $a['mailer'] : ''; ?>" />
		<input type="hidden" name="action" value="<?php echo $mailer_id; ?>_add_subscriber" />
        <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
        <input type="hidden" name="style_id" value="<?php echo ( isset( $a['style_id'] ) ) ? $a['style_id'] : ''; ?>" />
        <input type="hidden" name="option" value="<?php echo $data_option; ?>" />
        <input type="hidden" name="date" value="<?php echo esc_attr( date("j-n-Y") ); ?>" />
        <input type="hidden" name="msg_wrong_email" value="<?php echo isset( $a['msg_wrong_email'] ) ? $a['msg_wrong_email'] : ''; ?>" />
        <input type="hidden" name="<?php echo $a['on_success']; ?>" value="<?php echo $on_success_action; ?>" />
        <?php
        $html = ob_get_clean();
        echo $html;
	}
}

add_filter( 'cp_form_hidden_fields', 'cp_get_form_hidden_fields', 10, 1 );
