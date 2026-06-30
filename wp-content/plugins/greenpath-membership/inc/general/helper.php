<?php

if ( ! function_exists( 'greenpath_membership_get_dashboard_navigation_pages' ) ) {
	/**
	 * Function that return main dashboard page navigation items
	 *
	 * @return array
	 */
	function greenpath_membership_get_dashboard_navigation_pages() {
		$dashboard_url = greenpath_membership_get_dashboard_page_url();

		$items = array(
			'profile'      => array(
				'url'         => esc_url( add_query_arg( array( 'user-action' => 'profile' ), $dashboard_url ) ),
				'text'        => esc_html__( 'Profile', 'greenpath-membership' ),
				'user_action' => 'profile',
				'icon'        => '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" class="svg-inline--fa fa-user fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>',
			),
			'edit-profile' => array(
				'url'         => esc_url( add_query_arg( array( 'user-action' => 'edit-profile' ), $dashboard_url ) ),
				'text'        => esc_html__( 'Edit Profile', 'greenpath-membership' ),
				'user_action' => 'edit-profile',
				'icon'        => '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog" class="svg-inline--fa fa-cog fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"></path></svg>',
			),
		);

		$items = apply_filters( 'greenpath_membership_filter_dashboard_navigation_action_pages', $items, $dashboard_url );

		$items['log-out'] = array(
			'url'         => wp_logout_url( greenpath_membership_get_membership_redirect_url() ),
			'text'        => esc_html__( 'Log Out', 'greenpath-membership' ),
			'user_action' => 'log-out',
			'icon'        => '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="arrow-circle-right" class="svg-inline--fa fa-arrow-circle-right fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8c137 0 248 111 248 248S393 504 256 504 8 393 8 256 119 8 256 8zm-28.9 143.6l75.5 72.4H120c-13.3 0-24 10.7-24 24v16c0 13.3 10.7 24 24 24h182.6l-75.5 72.4c-9.7 9.3-9.9 24.8-.4 34.3l11 10.9c9.4 9.4 24.6 9.4 33.9 0L404.3 273c9.4-9.4 9.4-24.6 0-33.9L271.6 106.3c-9.4-9.4-24.6-9.4-33.9 0l-11 10.9c-9.5 9.6-9.3 25.1.4 34.4z"></path></svg>',
		);

		$items = apply_filters( 'greenpath_membership_filter_dashboard_navigation_pages', $items, $dashboard_url );

		return $items;
	}
}

if ( ! function_exists( 'greenpath_membership_get_dashboard_pages' ) ) {
	/**
	 * Function that return content for main dashboard page item
	 *
	 * @return string that contains html of content
	 */
	function greenpath_membership_get_dashboard_pages() {
		$action = isset( $_GET['user-action'] ) && ! empty( $_GET['user-action'] ) ? sanitize_text_field( $_GET['user-action'] ) : 'profile';

		$params = array();
		if ( 'profile' === $action || 'edit-profile' === $action ) {
			$params = greenpath_membership_get_user_params( $action );
		}

		switch ( $action ) {
			case 'edit-profile':
				$html = greenpath_membership_get_template_part( 'general', 'page-templates/parts/edit-profile', '', $params );
				break;
			default:
				$html = greenpath_membership_get_template_part( 'general', 'page-templates/parts/profile', '', $params );
				break;
		}

		return apply_filters( 'greenpath_membership_filter_dashboard_page', $html, $action );
	}
}

if ( ! function_exists( 'greenpath_membership_get_user_params' ) ) {
	/**
	 * Function that return user attributes for main dashboard page
	 *
	 * @param string $action
	 *
	 * @return array
	 */
	function greenpath_membership_get_user_params( $action ) {
		$params = array();

		$user    = wp_get_current_user();
		$user_id = $user->data->ID;

		$custom_user_image = get_user_meta( $user_id, 'qodef_user_image', true );
		$avatar_size       = 96;

		$params['user']          = $user;
		$params['first_name']    = get_the_author_meta( 'first_name', $user_id );
		$params['last_name']     = get_the_author_meta( 'last_name', $user_id );
		$params['email']         = get_the_author_meta( 'user_email', $user_id );
		$params['website']       = get_the_author_meta( 'user_url', $user_id );
		$params['description']   = get_the_author_meta( 'description', $user_id );
		$params['profile_image'] = ! empty( $custom_user_image ) ? qode_framework_generate_thumbnail( intval( $custom_user_image ), $avatar_size, $avatar_size ) : get_avatar( $user_id, $avatar_size );
		$params['action']        = $action;

		return apply_filters( 'greenpath_membership_filter_user_params', $params );
	}
}

if ( ! function_exists( 'greenpath_membership_add_rest_api_update_user_meta_global_variables' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $global - list of variables
	 * @param string $namespace - rest namespace url
	 *
	 * @return array
	 */
	function greenpath_membership_add_rest_api_update_user_meta_global_variables( $global, $namespace ) {
		$global['updateUserRestRoute'] = $namespace . '/edit-profile';

		return $global;
	}

	add_filter( 'qode_framework_filter_rest_api_global_variables', 'greenpath_membership_add_rest_api_update_user_meta_global_variables', 10, 2 );
}

if ( ! function_exists( 'greenpath_membership_add_rest_api_update_user_meta_route' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function greenpath_membership_add_rest_api_update_user_meta_route( $routes ) {
		$routes['edit-profile'] = array(
			'route'               => 'edit-profile',
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'greenpath_membership_update_user_profile',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
			'args'                => array(
				'options' => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {
						// Simple solution for validation can be 'is_array' value instead of callback function
						return is_array( $param ) ? $param : (array) $param;
					},
					'description'       => esc_html__( 'Options data is array with reaction and id values', 'greenpath-membership' ),
				),
			),
		);

		return $routes;
	}

	add_filter( 'qode_framework_filter_rest_api_routes', 'greenpath_membership_add_rest_api_update_user_meta_route' );
}

if ( ! function_exists( 'greenpath_membership_update_user_profile' ) ) {
	/**
	 * Function that update user profile
	 */
	function greenpath_membership_update_user_profile() {

		if ( ! isset( $_POST['options'] ) || empty( $_POST['options'] ) || ! is_user_logged_in() ) {
			qode_framework_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'greenpath-membership' ) );
		} else {
			$options = isset( $_POST['options'] ) ? $_POST['options'] : array();

			if ( ! empty( $options ) ) {
				parse_str( $options, $options );

				$user_id = get_current_user_id();

				if ( ! empty( $user_id ) ) {
					$user_fields = array();

					if ( isset( $options['user_password'] ) && ! empty( $options['user_password'] ) ) {
						if ( $options['user_password'] === $options['user_confirm_password'] ) {
							$user_fields['user_pass'] = esc_attr( $options['user_password'] );
						} else {
							qode_framework_get_ajax_status( 'error', esc_html__( 'Password and confirm password doesn\'t match.', 'greenpath-membership' ) );
						}
					}

					if ( isset( $options['user_email'] ) && ! empty( $options['user_email'] ) ) {

						if ( ! is_email( $options['user_email'] ) ) {
							qode_framework_get_ajax_status( 'error', esc_html__( 'Please provide a valid email address.', 'greenpath-membership' ) );
						}

						$current_user_object = get_user_by( 'email', $options['user_email'] );
						if ( ! empty( $current_user_object ) && $current_user_object->ID !== $user_id && email_exists( $options['user_email'] ) ) {
							qode_framework_get_ajax_status( 'error', esc_html__( 'An account is already registered with this email address. Please fill another one.', 'greenpath-membership' ) );
						} else {
							$user_fields['user_email'] = sanitize_email( $options['user_email'] );
						}
					}

					$simple_fields = array(
						'first_name'  => array(
							'escape' => 'attr',
						),
						'last_name'   => array(
							'escape' => 'attr',
						),
						'user_url'    => array(
							'escape' => 'url',
						),
						'description' => array(
							'escape' => 'attr',
						),
					);

					foreach ( $simple_fields as $key => $value ) {
						if ( isset( $options[ $key ] ) && ! empty( $options[ $key ] ) ) {
							$escape = 'esc_' . $value['escape'];

							$user_fields[ $key ] = $escape( $options[ $key ] );
						}
					}

					$custom_fields = array(
						'qodef_user_image' => array(
							'escape'      => 'attr',
							'allow_empty' => true,
						),
					);

					do_action( 'greenpath_membership_action_update_user_profile', $options, $user_id );

					if ( ! empty( $user_fields ) ) {
						wp_update_user(
							array_merge(
								array( 'ID' => $user_id ),
								$user_fields
							)
						);

						foreach ( $custom_fields as $custom_fields_key => $custom_fields_value ) {
							$allow_empty_value = isset( $custom_fields_value['allow_empty'] ) && ! empty( $custom_fields_value['allow_empty'] );

							if ( isset( $options[ $custom_fields_key ] ) && ( ! empty( $options[ $custom_fields_key ] || $allow_empty_value ) ) ) {
								$escape = 'esc_' . $custom_fields_value['escape'];

								update_user_meta( $user_id, $custom_fields_key, $escape( $options[ $custom_fields_key ] ) );
							}
						}

						qode_framework_get_ajax_status( 'success', esc_html__( 'Your profile is successfully updated.', 'greenpath-membership' ), null, greenpath_membership_get_membership_redirect_url() );
					} else {
						qode_framework_get_ajax_status( 'error', esc_html__( 'Change your information in order to update your profile.', 'greenpath-membership' ) );
					}
				} else {
					qode_framework_get_ajax_status( 'error', esc_html__( 'You are unauthorized to perform this action.', 'greenpath-membership' ) );
				}
			} else {
				qode_framework_get_ajax_status( 'error', esc_html__( 'Data are invalid.', 'greenpath-membership' ) );
			}
		}
	}
}

if ( ! function_exists( 'greenpath_membership_change_get_default_option_type' ) ) {
	/**
	 * Function that changes type from which to take default value from
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	function greenpath_membership_change_get_default_option_type( $type ) {
		if ( isset( $_GET['user-action'] ) && 'edit-profile' === $_GET['user-action'] ) {
			$type = 'user';
		}

		return $type;
	}

	add_filter( 'qode_framework_filter_option_value_type', 'greenpath_membership_change_get_default_option_type' );
}
