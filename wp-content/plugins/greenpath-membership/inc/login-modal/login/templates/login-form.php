<form id="qodef-membership-login-modal-part" class="qodef-m" method="GET">
	<div class="qodef-m-fields">
		<input type="text" class="qodef-m-user-name" name="user_name" placeholder="<?php esc_attr_e( 'User Name *', 'greenpath-membership' ); ?>" value="" required pattern=".{3,}" autocomplete="username"/>
		<input type="password" class="qodef-m-user-password" name="user_password" placeholder="<?php esc_attr_e( 'Password *', 'greenpath-membership' ); ?>" required autocomplete="current-password"/>
	</div>
	<div class="qodef-m-links">
		<div class="qodef-m-links-remember-me">
			<input type="checkbox" id="qodef-m-links-remember" class="qodef-m-links-remember" name="remember" value="forever"/>
			<label for="qodef-m-links-remember" class="qodef-m-links-remember-label"><?php esc_html_e( 'Remember me', 'greenpath-membership' ); ?></label>
		</div>
		<?php
		$reset_button_params = array(
			'custom_class'  => 'qodef-m-links-reset-password',
			'button_layout' => 'textual',
			'link'          => '#',
			'text'          => esc_html__( 'Lost Your password?', 'greenpath-membership' ),
		);

		echo GreenPathCore_Button_Shortcode::call_shortcode( $reset_button_params );
		?>
	</div>
	<div class="qodef-m-action">
		<?php
		$login_button_params = array(
			'custom_class' => 'qodef-m-action-button',
			'html_type'    => 'submit',
			'text'         => esc_html__( 'Login', 'greenpath-membership' ),
		);

		echo GreenPathCore_Button_Shortcode::call_shortcode( $login_button_params );

		greenpath_membership_template_part( 'login-modal', 'templates/parts/spinner' );
		?>
	</div>
	<?php
	/**
	 * Hook to include additional form content
	 */
	do_action( 'greenpath_membership_action_login_form_template' );

	greenpath_membership_template_part( 'login-modal', 'templates/parts/response' );
	greenpath_membership_template_part( 'login-modal', 'templates/parts/hidden-fields', '', array( 'response_type' => 'login' ) );
	?>
</form>
