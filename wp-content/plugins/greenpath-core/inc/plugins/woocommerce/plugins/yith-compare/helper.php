<?php

if ( ! function_exists( 'greenpath_core_include_yith_compare_plugin_is_installed' ) ) {
	/**
	 * Function that set case is installed element for framework functionality
	 *
	 * @param bool $installed
	 * @param string $plugin - plugin name
	 *
	 * @return bool
	 */
	function greenpath_core_include_yith_compare_plugin_is_installed( $installed, $plugin ) {
		if ( 'yith-compare' === $plugin ) {
			return defined( 'YITH_WOOCOMPARE' );
		}

		return $installed;
	}

	add_filter( 'qode_framework_filter_is_plugin_installed', 'greenpath_core_include_yith_compare_plugin_is_installed', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_yith_compare_popup_style' ) ) {
	function greenpath_core_yith_compare_popup_style() {
		$disable_popup_styles = greenpath_core_get_post_value_through_levels( 'qodef_enable_woo_yith_compare_predefined_style' );

		if ( 'yes' === $disable_popup_styles ) {
			print '<link rel="stylesheet" href="' . GREENPATH_CORE_ASSETS_URL_PATH . '/css/greenpath-core.min.css' . '" type="text/css" />';
		}
	}

	add_action( 'yith_woocompare_popup_head', 'greenpath_core_yith_compare_popup_style' );
}

if ( ! function_exists( 'greenpath_core_yith_compare_popup_class' ) ) {
	function greenpath_core_yith_compare_popup_class( $classes ) {
		$disable_popup_styles = greenpath_core_get_post_value_through_levels( 'qodef_enable_woo_yith_compare_predefined_style' );

		if ( 'yes' === $disable_popup_styles ) {
			$classes[] = 'qodef-popup-compare';
		}

		return $classes;
	}

	add_filter( 'body_class', 'greenpath_core_yith_compare_popup_class' );
}

if ( ! function_exists( 'greenpath_core_yith_compare_popup_layout' ) ) {
	function greenpath_core_yith_compare_popup_layout() {
		$index = 0;

		global $yith_woocompare;
		$products = $yith_woocompare->obj->get_products_list(); ?>

		<div class="qodef-pl-holder qodef-pli-separator-layout qodef-standard-layout qodef-pli-standard-excerpt qodef-large-space qodef-three-columns">
		<div class="qodef-pl-outer-popup qodef-outer-space clearfix">

		<?php if ( ! empty( $products ) ) {

			foreach ( $products as $product_id => $product ) { ?>
				<?php
				$params['product_id'] = $product_id;
				$params['product']    = $product;
				$product_class = 'product_' . $product_id;

				$itemClasses = '';

				if ( ! $product->is_in_stock() ) {
					$itemClasses = 'qodef-pli-out-of-stock-holder';
				}
				?>

				<div class="qodef-pli-popup qodef-item-space <?php echo esc_attr( $itemClasses ); ?>">
					<div class="qodef-pli-holder-popup">
						<div class="qodef-pli-inner">
							<div class="qodef-pli-image-popup">
								<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/image', '', $params ); ?>
							</div>
							<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/link', '', $params ); ?>
						</div>
						<div class="qodef-pli-text-wrapper-popup">
							<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/title', '', $params ); ?>
							<div class="qodef-pli-text-left-holder-popup">
								<div itemprop="description" class="qodef-pli-excerpt-popup">
									<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/excerpt', '', $params ); ?>
								</div>
								<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/stock', '', $params ); ?>
								<div class="qodef-pli-text-price-wrapper">
									<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/rating', '', $params ); ?>
									<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/price', '', $params ); ?>
								</div>
							</div>
							<?php greenpath_core_template_part( 'plugins/woocommerce/plugins/yith-compare', 'templates/add-to-cart', '', $params ); ?>
						</div>
						<div class="remove">
							<div class="<?php echo esc_attr( $product_class ); ?> remove-popup-inner">
								<a href="<?php echo add_query_arg( '', '', $yith_woocompare->obj->remove_product_url( $product_id ) ) ?>"
								   data-product_id="<?php echo esc_attr( $product_id ); ?>">
									<?php greenpath_core_render_svg_icon('close')?>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
			</div>
		<?php }
	}

	add_action( 'yith_woocompare_before_main_table', 'greenpath_core_yith_compare_popup_layout' );
}
