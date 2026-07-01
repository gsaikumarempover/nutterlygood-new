<?php
if ( function_exists( 'nuttergood_farmley_is_product_list_empty_context' ) && nuttergood_farmley_is_product_list_empty_context() ) {
	nuttergood_farmley_render_empty_products_state();
	return;
}
?>
<p class="qodef-m-posts-not-found qodef-grid-item"><?php esc_html_e( 'No posts were found for provided query parameters.', 'nuttergood' ); ?></p>
