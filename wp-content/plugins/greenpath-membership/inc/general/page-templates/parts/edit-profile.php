<?php

if ( ! function_exists( 'greenpath_membership_dashboard_edit_profile_fields' ) ) {
	/**
	 * Function that display edit profile page form
	 *
	 * @param array $params
	 */
	function greenpath_membership_dashboard_edit_profile_fields( $params ) {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'type'         => 'front-end',
				'slug'         => 'edit-profile-page',
				'title'        => esc_html__( 'Edit Profile', 'greenpath-membership' ),
				'form_id'      => 'qodef-membership-edit-profile',
				'name'         => 'edit_profile_form',
				'method'       => 'POST',
				'button_label' => esc_html__( 'Update Profile', 'greenpath-membership' ),
				'button_args'  => array(
					'data-updating-text' => esc_html__( 'Updating Profile', 'greenpath-membership' ),
					'data-updated-text'  => esc_html__( 'Profile Updated', 'greenpath-membership' ),
					'data-rest-route'    => 'updateUserRestRoute',
					'data-rest-nonce'    => 'restNonce',
				),
			)
		);

		if ( $page ) {

			if ( apply_filters( 'greenpath_membership_filter_edit_profile_full_name_visibility', true ) ) {
				$page->add_field_element(
					array(
						'field_type' => 'text',
						'name'       => 'first_name',
						'title'      => esc_html__( 'First Name', 'greenpath-membership' ),
					)
				);

				$page->add_field_element(
					array(
						'field_type' => 'text',
						'name'       => 'last_name',
						'title'      => esc_html__( 'Last Name', 'greenpath-membership' ),
					)
				);
			}

			$page->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'user_email',
					'title'         => esc_html__( 'Email', 'greenpath-membership' ),
					'default_value' => $params['email'],
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'image',
					'name'       => 'qodef_user_image',
					'title'      => esc_html__( 'Profile image:', 'greenpath-membership' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'user_url',
					'title'         => esc_html__( 'Website', 'greenpath-membership' ),
					'default_value' => $params['website'],
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'description',
					'title'      => esc_html__( 'Description', 'greenpath-membership' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'password',
					'name'       => 'user_password',
					'title'      => esc_html__( 'Password', 'greenpath-membership' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'user_confirm_password',
					'title'      => esc_html__( 'Repeat Password', 'greenpath-membership' ),
				)
			);

			do_action( 'greenpath_membership_action_after_dashboard_edit_profile_fields', $page, $params );

			$page->render();
		}
	}
}
?>
<div class="qodef-m-content-inner qodef--<?php echo esc_attr( isset( $action ) && ! empty( $action ) ? $action : 'dashboard' ); ?>">
	<div id="qodef-page" class="qodef-options-front-end">
		<?php greenpath_membership_dashboard_edit_profile_fields( $params ); ?>
	</div>
</div>
