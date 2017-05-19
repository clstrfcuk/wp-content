<?php

function ubermenu_searchbar( $placeholder = null , $post_type = '' ){
	if( is_null( $placeholder ) ){
		$placeholder = __( 'Search...' , 'ubermenu'  );
	}
	?>
	<!-- UberMenu Search Bar -->
	<div class="ubermenu-search">
		<form role="search" method="get" class="ubermenu-searchform" action="<?php echo home_url( '/' ); ?>">
			<input type="text" placeholder="<?php echo $placeholder; ?>" value="" name="s" class="ubermenu-search-input" />
			<?php if( $post_type ): ?>
			<input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
			<?php endif; ?>
			<input type="submit" class="ubermenu-search-submit" value="&#xf002;" />
		</form>
	</div>
	<!-- end .ubermenu-search -->
	<?php
}

function ubermenu_searchbar_shortcode( $atts , $content ){

	extract( shortcode_atts( array(
		'placeholder' => __( 'Search...' , 'ubermenu' ),
		'post_type'	=> '',
	), $atts ) );

	ob_start();
	ubermenu_searchbar( $placeholder , $post_type );
	$s = ob_get_clean();

	return $s;
}
add_shortcode( 'ubermenu-search' , 'ubermenu_searchbar_shortcode' );
