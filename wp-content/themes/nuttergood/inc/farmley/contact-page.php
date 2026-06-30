<?php
/**
 * Contact page — themed form + map.
 */

if ( ! function_exists( 'nuttergood_farmley_is_contact_page' ) ) {
	function nuttergood_farmley_is_contact_page() {
		return is_page( 'contact' ) || (int) get_queried_object_id() === 3437;
	}
}

if ( ! function_exists( 'nuttergood_farmley_contact_details' ) ) {
	function nuttergood_farmley_contact_details() {
		$info = function_exists( 'nuttergood_farmley_contact_info' )
			? nuttergood_farmley_contact_info()
			: array();

		return array(
			'company' => $info['company'] ?? 'Nutterly Good',
			'email'   => $info['email'] ?? 'contact@nutterlygood.com',
			'phone'   => $info['phone'] ?? '+91 74162 85566',
			'address' => $info['address'] ?? 'CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075',
			'hours'   => $info['hours'] ?? 'All Days: 11:00 AM – 9:00 PM IST',
			'map_lat' => $info['map_lat'] ?? '17.3921',
			'map_lng' => $info['map_lng'] ?? '78.3396',
			'map_url' => $info['map_url'] ?? '',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_contact_map_src' ) ) {
	function nuttergood_farmley_contact_map_src() {
		$details = nuttergood_farmley_contact_details();
		return 'https://www.google.com/maps?q=' . rawurlencode( $details['map_lat'] . ',' . $details['map_lng'] ) . '&hl=en&z=15&output=embed';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_contact_map' ) ) {
	function nuttergood_farmley_render_contact_map() {
		if ( ! nuttergood_farmley_is_contact_page() ) {
			return;
		}
		?>
		<div class="ng-farmley-contact-map-strip" aria-label="<?php esc_attr_e( 'Nutterly Good location map', 'nuttergood' ); ?>">
			<iframe
				title="<?php esc_attr_e( 'Nutterly Good location map', 'nuttergood' ); ?>"
				src="<?php echo esc_url( nuttergood_farmley_contact_map_src() ); ?>"
				width="100%"
				height="380"
				style="border:0;"
				allowfullscreen=""
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
			></iframe>
		</div>
		<?php
	}
	add_action( 'greenpath_action_before_page_inner', 'nuttergood_farmley_render_contact_map', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_render_contact_page' ) ) {
	function nuttergood_farmley_render_contact_page() {
		$details = nuttergood_farmley_contact_details();
		?>
		<div class="ng-farmley-contact">
			<div class="ng-farmley-contact__intro">
				<p class="ng-farmley-contact__lead"><?php esc_html_e( 'Questions about orders, bulk gifting, or product recommendations? Our team is happy to help.', 'nuttergood' ); ?></p>
			</div>
			<div class="ng-farmley-contact__grid">
				<div class="ng-farmley-contact__panel ng-farmley-contact__panel--info">
					<h2 class="ng-farmley-contact__heading"><?php esc_html_e( 'Reach us', 'nuttergood' ); ?></h2>
					<ul class="ng-farmley-contact__list">
						<li>
							<span class="ng-farmley-contact__label"><?php esc_html_e( 'Email', 'nuttergood' ); ?></span>
							<a href="mailto:<?php echo esc_attr( $details['email'] ); ?>"><?php echo esc_html( $details['email'] ); ?></a>
						</li>
						<li>
							<span class="ng-farmley-contact__label"><?php esc_html_e( 'Phone', 'nuttergood' ); ?></span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $details['phone'] ) ); ?>"><?php echo esc_html( $details['phone'] ); ?></a>
						</li>
						<li>
							<span class="ng-farmley-contact__label"><?php esc_html_e( 'Address', 'nuttergood' ); ?></span>
							<span>
								<?php echo esc_html( $details['company'] ); ?><br />
								<?php
								$info = nuttergood_farmley_contact_info();
								echo esc_html( $info['address_line1'] ); ?><br />
								<?php echo esc_html( $info['address_line2'] ); ?><br />
								<?php echo esc_html( $info['address_city'] ); ?>
							</span>
						</li>
						<li>
							<span class="ng-farmley-contact__label"><?php esc_html_e( 'Hours', 'nuttergood' ); ?></span>
							<span><?php echo esc_html( $details['hours'] ); ?></span>
						</li>
					</ul>
				</div>
				<div class="ng-farmley-contact__panel ng-farmley-contact__panel--form">
					<h2 class="ng-farmley-contact__heading"><?php esc_html_e( 'Send a message', 'nuttergood' ); ?></h2>
					<form class="ng-farmley-contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'ng_farmley_contact', 'ng_farmley_contact_nonce' ); ?>
						<input type="hidden" name="action" value="ng_farmley_contact_submit" />
						<div class="ng-farmley-contact__field-row">
							<div class="ng-farmley-contact__field">
								<label for="ng-contact-name"><?php esc_html_e( 'Your name', 'nuttergood' ); ?></label>
								<input type="text" id="ng-contact-name" name="ng_contact_name" required autocomplete="name" />
							</div>
							<div class="ng-farmley-contact__field">
								<label for="ng-contact-email"><?php esc_html_e( 'Email address', 'nuttergood' ); ?></label>
								<input type="email" id="ng-contact-email" name="ng_contact_email" required autocomplete="email" />
							</div>
						</div>
						<div class="ng-farmley-contact__field">
							<label for="ng-contact-subject"><?php esc_html_e( 'Subject', 'nuttergood' ); ?></label>
							<input type="text" id="ng-contact-subject" name="ng_contact_subject" required />
						</div>
						<div class="ng-farmley-contact__field">
							<label for="ng-contact-message"><?php esc_html_e( 'Message', 'nuttergood' ); ?></label>
							<textarea id="ng-contact-message" name="ng_contact_message" rows="5" required></textarea>
						</div>
						<button type="submit" class="ng-farmley-contact__submit"><?php esc_html_e( 'Send message', 'nuttergood' ); ?></button>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_setup_contact_template' ) ) {
	function nuttergood_farmley_setup_contact_template() {
		if ( get_option( 'ng_farmley_contact_layout_v1' ) ) {
			return;
		}

		$page = get_page_by_path( 'contact' );
		if ( ! $page ) {
			return;
		}

		update_post_meta( $page->ID, '_wp_page_template', 'page-contact.php' );
		delete_post_meta( $page->ID, '_elementor_edit_mode' );
		delete_post_meta( $page->ID, '_elementor_data' );
		delete_post_meta( $page->ID, '_elementor_css' );

		update_option( 'ng_farmley_contact_layout_v1', 1, false );
	}
	add_action( 'init', 'nuttergood_farmley_setup_contact_template', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_handle_contact_submit' ) ) {
	function nuttergood_farmley_handle_contact_submit() {
		if ( ! isset( $_POST['ng_farmley_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ng_farmley_contact_nonce'] ) ), 'ng_farmley_contact' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'nuttergood' ) );
		}

		$name    = isset( $_POST['ng_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ng_contact_name'] ) ) : '';
		$email   = isset( $_POST['ng_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['ng_contact_email'] ) ) : '';
		$subject = isset( $_POST['ng_contact_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['ng_contact_subject'] ) ) : '';
		$message = isset( $_POST['ng_contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ng_contact_message'] ) ) : '';

		$to      = nuttergood_farmley_contact_details()['email'];
		$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' );
		$body    = "Name: {$name}\nEmail: {$email}\nSubject: {$subject}\n\n{$message}";
		$sent    = wp_mail( $to, '[Nutterly Good] ' . $subject, $body, $headers );

		$redirect = add_query_arg( 'contact', $sent ? 'sent' : 'error', get_permalink( 3437 ) );
		wp_safe_redirect( $redirect );
		exit;
	}
	add_action( 'admin_post_nopriv_ng_farmley_contact_submit', 'nuttergood_farmley_handle_contact_submit' );
	add_action( 'admin_post_ng_farmley_contact_submit', 'nuttergood_farmley_handle_contact_submit' );
}

if ( ! function_exists( 'nuttergood_farmley_contact_notice' ) ) {
	function nuttergood_farmley_contact_notice() {
		if ( ! nuttergood_farmley_is_contact_page() ) {
			return;
		}

		if ( isset( $_GET['contact'] ) && 'sent' === $_GET['contact'] ) {
			echo '<div class="ng-farmley-contact__notice ng-farmley-contact__notice--success qodef-content-grid">' . esc_html__( 'Thank you — your message has been sent.', 'nuttergood' ) . '</div>';
		} elseif ( isset( $_GET['contact'] ) && 'error' === $_GET['contact'] ) {
			echo '<div class="ng-farmley-contact__notice ng-farmley-contact__notice--error qodef-content-grid">' . esc_html__( 'Sorry, we could not send your message. Please email us directly.', 'nuttergood' ) . '</div>';
		}
	}
	add_action( 'greenpath_action_before_page_content', 'nuttergood_farmley_contact_notice', 5 );
	add_action( 'nuttergood_farmley_before_contact_content', 'nuttergood_farmley_contact_notice', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_contact_assets' ) ) {
	function nuttergood_farmley_contact_assets() {
		if ( ! nuttergood_farmley_is_contact_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-contact.css';
		if ( file_exists( $css ) ) {
			wp_enqueue_style( 'nuttergood-farmley-contact', $uri . '/assets/css/farmley-contact.css', array( 'greenpath-style' ), filemtime( $css ) );
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_contact_assets', 35 );
}