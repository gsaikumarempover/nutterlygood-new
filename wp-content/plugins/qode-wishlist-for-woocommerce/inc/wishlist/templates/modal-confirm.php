<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div class="qwfw-confirm-modal qwfw-m qwfw--opened">
	<div id="qwfw-confirm-modal-overlay" class="qwfw-m-overlay"></div>
	<div class="qwfw-m-content">
		<a id="qwfw-confirm-close-icon" class="qwfw-m-close" href="#" rel="noopener noreferrer"><?php qode_wishlist_for_woocommerce_svg_icon( 'close' ); ?></a>
		<div class="qwfw-m-form-wrapper">
			<p class="qwfw-m-form-title"></p>
			<div class="qwfw-m-form-actions">
				<button id="qwfw-confirm-button-false" class="qwfw-m-form-button button qwfw--no"><?php esc_html_e( 'Cancel', 'qode-wishlist-for-woocommerce' ); ?></button>
				<button id="qwfw-confirm-button-true" class="qwfw-m-form-button button qwfw--yes"><?php esc_html_e( 'Delete', 'qode-wishlist-for-woocommerce' ); ?></button>
			</div>
		</div>
	</div>
</div>
