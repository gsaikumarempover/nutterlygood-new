<?php if ( defined( 'greenpath_core_VERSION' ) && class_exists( 'GreenPathCore_Social_Share_Shortcode' ) ) { ?>
	<div class="qodef-e-info-item qodef-e-info-social-share">
		<?php
		$params['layout']               = 'list';
		//$params['predefined_svg_icons'] = 'no';
		$params['icon_font']            = 'font-awesome';
		$params['title']                = esc_html__( 'Share:', 'nuttergood' );

		echo GreenPathCore_Social_Share_Shortcode::call_shortcode( $params );
		?>
	</div>
<?php } ?>
