<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$table_title       = 'no' !== qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_table_title' );
$table_title_label = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_table_title_label' );
$title_classes     = apply_filters( 'qode_compare_for_woocommerce_filter_table_title', array( 'qcfw-table-title' ) );
$title_tag         = apply_filters( 'qode_compare_for_woocommerce_filter_table_title_tag', 'h4' );

if ( $table_title && ! empty( $table_title_label ) ) {
	?>
	<<?php qode_compare_for_woocommerce_escape_title_tag( $title_tag ); ?> <?php qode_compare_for_woocommerce_class_attribute( $title_classes ); ?>><?php echo esc_html( $table_title_label ); ?></<?php qode_compare_for_woocommerce_escape_title_tag( $title_tag ); ?>>
	<?php
}
