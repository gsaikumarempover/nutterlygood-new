<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! empty( trim( $table_title ) ) || apply_filters( 'qode_wishlist_for_woocommerce_filter_show_wishlist_table_section_heading_actions', false ) ) {
	?>
	<div class="qwfw-m-section-heading">
		<?php if ( ! empty( trim( $table_title ) ) ) { ?>
			<div class="qwfw-m-section-heading-inner">
				<<?php qode_wishlist_for_woocommerce_escape_title_tag( $table_title_tag ); ?> class="qwfw-m-heading">
					<?php echo wp_kses_post( $table_title ); ?>
				</<?php qode_wishlist_for_woocommerce_escape_title_tag( $table_title_tag ); ?>>
			</div>
		<?php } ?>
		<div class="qwfw-m-section-heading-actions">
			<?php
			/**
			 * Hook: qode_wishlist_for_woocommerce_action_wishlist_table_section_heading_actions.
			 *
			 * @hooked Social Share module - add_social_share - 10
			 */
			do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_section_heading_actions', $params );
			?>
		</div>
	</div>
<?php } ?>
