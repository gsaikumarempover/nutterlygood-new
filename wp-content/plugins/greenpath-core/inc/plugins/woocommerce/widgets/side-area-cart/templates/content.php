<?php if ( is_object( WC()->cart ) ) { ?>
	<div class="qodef-widget-side-area-cart-content">
		<div class="qodef-side-area-cart-top">
			<div class="qodef-side-area-cart-logo">
				<?php
				// Add logo image
				greenpath_core_get_header_logo_image( array( 'only_main' => true ) );

				// Add close icon
				greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/close' );
				?>
			</div>
			<h6 class="qodef-side-area-cart-heading">
				<?php esc_html_e( 'My Shopping Cart', 'greenpath-core' ); ?>
			</h6>
		</div>
		<?php
		// Hook to include additional content before cart items
		do_action( 'greenpath_core_action_woocommerce_before_side_area_cart_content' );

		if ( ! WC()->cart->is_empty() ) {
			greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/loop' );

			greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/button' );
		} else {
			// Include posts not found
			greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/posts-not-found' );
		}
		?>
	</div>
<?php } ?>
