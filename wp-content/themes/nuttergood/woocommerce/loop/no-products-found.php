<?php
/**
 * Empty product loop — branded Farmley empty state.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="woocommerce-no-products-found">
	<?php
	if ( function_exists( 'nuttergood_farmley_render_empty_products_state' ) ) {
		nuttergood_farmley_render_empty_products_state( array( 'grid_item' => false ) );
	} else {
		wc_print_notice( esc_html__( 'No products were found matching your selection.', 'woocommerce' ), 'notice' );
	}
	?>
</div>
