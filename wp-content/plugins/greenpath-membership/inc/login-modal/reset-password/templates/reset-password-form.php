<form id="qodef-membership-reset-password-modal-part" class="qodef-m" method="POST">
	<div class="qodef-m-fields">
		<label><?php esc_html_e( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'greenpath-membership' ); ?></label>
		<input type="text" class="qodef-m-user-login" name="user_login" placeholder="<?php esc_attr_e( 'User name or email', 'greenpath-membership' ); ?>" value="" required/>
	</div>
	<div class="qodef-m-action">
		<?php
		$reset_button_params = array(
			'custom_class' => 'qodef-m-action-button',
			'html_type'    => 'submit',
			'text'         => esc_html__( 'Reset Password', 'greenpath-membership' ),
		);

		echo GreenPathCore_Button_Shortcode::call_shortcode( $reset_button_params );

		greenpath_membership_template_part( 'login-modal', 'templates/parts/spinner' );
		?>
	</div>
	<?php greenpath_membership_template_part( 'login-modal', 'templates/parts/response' ); ?>
	<?php greenpath_membership_template_part( 'login-modal', 'templates/parts/hidden-fields', '', array( 'response_type' => 'reset-password' ) ); ?>
</form>
