<?php
$products = wc_get_products( array( 'posts_per_page' => - 1 ) );

$all_prices = array();

foreach ( $products as $product ) {
	$all_prices[] = $product->get_price();
}

$min_price = 0;
$max_price = ! empty( $all_prices ) ? max( $all_prices ) : 0;

$step = 10;

$id = isset( $mobile_id_prefix ) ? $mobile_id_prefix : '';
?>
<div class="qodef-e-options-wrapper qodef-e-price-filter">
	<div class="qodef-e-options-inner">
		<div class="qodef-price-slider-amount" data-step="<?php echo esc_attr( $step ); ?>">
			<input type="text" id="<?php echo esc_attr( $id . 'min_price' ); ?>" name="min_price" value="<?php echo esc_attr( $min_price ); ?>" data-min="<?php echo esc_attr( $min_price ); ?>" data-currency="<?php echo get_woocommerce_currency_symbol(); ?>" placeholder="<?php echo esc_attr__( 'Min price', 'greenpath-core' ); ?>"/>
			<input type="text" id="<?php echo esc_attr( $id . 'max_price' ); ?>" name="max_price" value="<?php echo esc_attr( $max_price ); ?>" data-max="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php echo esc_attr__( 'Max price', 'greenpath-core' ); ?>"/>
		</div>
		<div class="qodef-price-slider"></div>
		<div class="qodef-price-filter-bottom">
			<span class="qodef-e-amount">
				<?php echo esc_html__( 'Price:', 'greenpath-core' ); ?>
				<span class="qodef--min"><?php echo esc_html( $min_price ); ?></span>
				<span class="qodef--max"><?php echo esc_html( $max_price ); ?></span>
			</span>
			<div class="qodef-filter-button">
				<?php
				if ( class_exists( 'GreenPathCore_Button_Shortcode' ) ) {
					$button_params = array(
						'button_layout' => 'filled',
						'link'          => '#',
						'text'          => esc_html__( 'Filter', 'greenpath-core' ),
					);

					echo GreenPathCore_Button_Shortcode::call_shortcode( $button_params );
				}
				?>
			</div>
		</div>
	</div>
</div>
