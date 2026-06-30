<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( 'no' !== $enable_social_share ) {
	?>
	<div class="qwfw-social-share qwfw-m qwfw-layout--dropdown qwfw-dropdown--bottom">
		<a class="qwfw-m-opener" href="javascript:void(0)" rel="noopener noreferrer">
			<?php
			// Set additional hook before social share icon.
			do_action( 'qode_wishlist_for_woocommerce_action_before_social_share_opener_icon' );
			?>
			<span class="qwfw-m-opener-icon"><?php qode_wishlist_for_woocommerce_svg_icon( 'share' ); ?></span>
			<?php
			// Set additional hook after social share icon.
			do_action( 'qode_wishlist_for_woocommerce_action_after_social_share_opener_icon' );
			?>
		</a>
		<div class="qwfw-m-social-items">
			<?php
			$social_networks_list = qode_wishlist_for_woocommerce_enabled_social_networks_list();

			if ( ! empty( $social_networks_list ) ) {
				foreach ( $social_networks_list as $network => $labels ) {
					$network_icon_type = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_icon' );

					$icon_classes   = array();
					$icon_classes[] = 'qwfw--' . esc_attr( $network );
					$icon_classes[] = 'qwfw--' . esc_attr( $network_icon_type );

					$params['icon_classes'] = implode( ' ', $icon_classes );
					$params['network']      = $network;
					$params['link']         = qode_wishlist_for_woocommerce_get_social_network_share_link( $network, $table_title, $table_data['data-token'], $table_name );

					qode_wishlist_for_woocommerce_template_part( 'social-share', 'templates/parts/network', '', $params );
				}
			}
			?>
		</div>
	</div>
<?php } ?>
