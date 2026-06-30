<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div class="qodef-custom-qode-products-page qodef-options-admin qodef-page-v4-quick-view">
	<?php qode_quick_view_for_woocommerce_framework_template_part( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc', 'admin-pages/options-custom-pages/qode-products', 'templates/parts/header', '' ); ?>
	<?php qode_quick_view_for_woocommerce_framework_template_part( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc', 'admin-pages/options-custom-pages/qode-products', 'templates/parts/plugins', '' ); ?>
	<?php qode_quick_view_for_woocommerce_framework_template_part( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc', 'admin-pages/options-custom-pages/qode-products', 'templates/parts/all-products', '' ); ?>
</div>
<?php qode_quick_view_for_woocommerce_framework_template_part( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc', 'admin-pages/options-custom-pages/qode-products', 'templates/parts/footer', '' ); ?>
