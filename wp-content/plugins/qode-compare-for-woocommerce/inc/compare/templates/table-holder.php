<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$all_items                = qode_compare_for_woocommerce_get_comparison_items( true );
$table_data['data-token'] = $all_items['token'] ?? qode_compare_for_woocommerce_generate_token();
$get_table_name           = qode_compare_for_woocommerce_get_http_request_args( false, 'table' );
$table_name               = ! empty( $get_table_name ) ? $get_table_name : 'default';
$table_data['data-table'] = esc_attr( $table_name );
$has_token                = qode_compare_for_woocommerce_get_http_request_args( true );
$holder_classes           = array();
$holder_classes[]         = 'qcfw-compare-table';
$params                   = array( 'items' => qode_compare_for_woocommerce_get_comparison_items_by_table( $table_name ) );

if ( ! empty( $has_token ) ) {
	$holder_classes[] = 'qcfw--has-token';
}

?>
	<div <?php qode_compare_for_woocommerce_class_attribute( $holder_classes ); ?> <?php qode_compare_for_woocommerce_inline_attrs( $table_data ); ?>>
		<?php
		/**
		 * Hook: qode_compare_for_woocommerce_action_before_comparison_table.
		 *
		 * @hooked Compare module - add_table_title - 10
		 */
		do_action( 'qode_compare_for_woocommerce_action_before_comparison_table' );
		?>
		<div class="qcfw-m-items">
			<?php
			// Include table content.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters(
				'qode_compare_for_woocommerce_filter_compare_table_content',
				qode_compare_for_woocommerce_get_template_part( 'compare/templates', 'table', '', $params ),
				$params
			);
			?>
		</div>
		<?php
		do_action( 'qode_compare_for_woocommerce_action_after_comparison_table' );
		?>
	</div>
<?php
