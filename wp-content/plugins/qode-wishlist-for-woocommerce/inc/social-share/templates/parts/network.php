<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div class="qwfw-m-social-item qwfw-e <?php echo esc_attr( $icon_classes ); ?>">
	<?php if ( in_array( $network, array( 'email', 'whatsapp' ), true ) ) { ?>
		<a class="qwfw-e-link" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( 'whatsapp' === $network ? '_blank' : '_self' ); ?>" rel="nofollow">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo qode_wishlist_for_woocommerce_get_social_network_icon( $network );
			?>
		</a>
	<?php } else { ?>
		<a class="qwfw-e-link" href="#" onclick="<?php echo esc_attr( $link ); ?>" rel="noopener noreferrer">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo qode_wishlist_for_woocommerce_get_social_network_icon( $network );
			?>
		</a>
	<?php } ?>
</div>
