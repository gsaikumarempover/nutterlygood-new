<?php

if ( ! function_exists( 'greenpath_membership_add_login_opener_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_membership_add_login_opener_widget( $widgets ) {
		$widgets[] = 'GreenPathMembership_Login_Opener_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_membership_filter_register_widgets', 'greenpath_membership_add_login_opener_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathMembership_Login_Opener_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'greenpath_membership_login_opener' );
			$this->set_name( esc_html__( 'Nutterlygood Login Opener', 'greenpath-membership' ) );
			$this->set_description( esc_html__( 'Login and register membership widget', 'greenpath-membership' ) );
			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'login_opener_margin',
					'title'       => esc_html__( 'Opener Margin', 'greenpath-membership' ),
					'description' => esc_html__( 'Insert margin in format: top right bottom left', 'greenpath-membership' ),
				)
			);
		}

		public function render( $atts ) {
			$classes   = array();
			$classes[] = is_user_logged_in() ? 'qodef-user-logged--in' : 'qodef-user-logged--out';

			$styles = array();

			if ( ! empty( $atts['login_opener_margin'] ) ) {
				$styles[] = 'margin: ' . $atts['login_opener_margin'];
			}

			$dashboard_template = apply_filters( 'greenpath_membership_filter_dashboard_template_name', '' );

			if ( empty( $dashboard_template ) || ! is_page_template( $dashboard_template ) || ( is_page_template( $dashboard_template ) && is_user_logged_in() ) ) { ?>
				<div class="qodef-login-opener-widget <?php echo implode( ' ', $classes ); ?>" <?php qode_framework_inline_style( $styles ); ?>>
					<?php greenpath_membership_template_part( 'widgets/login-opener', 'templates/holder' ); ?>
				</div>
				<?php
			}
		}
	}
}
