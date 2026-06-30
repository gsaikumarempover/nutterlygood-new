<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div class="qcfw-thumbnail-wrapper">
	<?php
	qode_compare_for_woocommerce_template_part( 'compare/templates', 'item-parts/remove', '', $params );
	qode_compare_for_woocommerce_template_part( 'compare/templates', 'item-parts/image', '', $params );
	?>
</div>
