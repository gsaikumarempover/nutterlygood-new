<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div id="qcfw-confirm-modal-overlay" class="qcfw-m-overlay"></div>
<div class="qcfw-m-dialog-content">
	<div class="qcfw-m-form-wrapper">
		<p class="qcfw-m-form-title"></p>
		<div class="qcfw-m-form-actions">
			<?php
			if ( isset( $single_button ) && $single_button ) {
				?>
				<a id="qcfw-confirm-button-false" class="qcfw-m-form-button button qcfw--no"><?php esc_attr_e( 'Ok', 'qode-compare-for-woocommerce' ); ?></a>
				<?php
			} else {
				?>
				<a id="qcfw-confirm-button-true" class="qcfw-m-form-button button qcfw--yes"><?php esc_attr_e( 'Remove', 'qode-compare-for-woocommerce' ); ?></a>
				<a id="qcfw-confirm-button-false" class="qcfw-m-form-button button qcfw--no"><?php esc_attr_e( 'Cancel', 'qode-compare-for-woocommerce' ); ?></a>
				<?php
			}
			?>
		</div>
	</div>
</div>
