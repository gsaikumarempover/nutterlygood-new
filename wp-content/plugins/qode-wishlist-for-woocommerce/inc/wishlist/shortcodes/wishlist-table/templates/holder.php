<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div <?php qode_wishlist_for_woocommerce_class_attribute( $holder_classes ); ?> <?php qode_wishlist_for_woocommerce_inline_attrs( $table_data ); ?>>
	<?php
	// Print WooCommerce notice.
	if ( function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) {
		wc_print_notices();
	}

	// Check wishlist table visibility.
	if ( $is_visible ) {
		?>
		<?php
		// Set additional hook for 3rd party elements.
		do_action( 'qode_wishlist_for_woocommerce_action_before_wishlist_table_inner', $params );
		?>
		<div class="qwfw-m-inner">
			<?php
			// Include table content.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters(
				'qode_wishlist_for_woocommerce_filter_wishlist_table_content',
				qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/table', '', $params ),
				$params
			);
			?>
		</div>
		<?php
		// Set additional hook for 3rd party elements.
		do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_table_inner', $params );
		?>
		<?php
	} else {
		esc_html_e( 'You don\'t have permission to see this wishlist table', 'qode-wishlist-for-woocommerce' );
	}
	?>
</div>
