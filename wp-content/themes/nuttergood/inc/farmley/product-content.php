<?php
/**
 * Clean imported product HTML (Odoo review blocks, recommended products, etc.).
 */

if ( ! function_exists( 'nuttergood_farmley_clean_product_html' ) ) {
	/**
	 * @param string $content Raw HTML.
	 */
	function nuttergood_farmley_clean_product_html( $content ) {
		if ( '' === trim( (string) $content ) ) {
			return '';
		}

		$patterns = array(
			'/<div[^>]*class="[^"]*o_shop_discussion_rating[^"]*"[^>]*>.*?<\/div>\s*<\/div>/is',
			'/<section[^>]*id="o_product_page_reviews"[^>]*>.*?<\/section>/is',
			'/<a[^>]*class="[^"]*o_product_page_reviews[^"]*"[^>]*>.*?<\/a>/is',
			'/<div[^>]*class="[^"]*o_website_rating_static[^"]*"[^>]*>.*?<\/div>/is',
			'/<div[^>]*id="oe_structure_website_sale_recommended_products"[^>]*>.*?<\/div>\s*<\/div>/is',
			'/<section[^>]*data-snippet="s_dynamic_snippet_products"[^>]*>.*?<\/section>/is',
			'/<div[^>]*class="[^"]*oe_structure_website_sale[^"]*"[^>]*>.*?<\/div>/is',
			'/<table\b[^>]*>.*?<\/table>/is',
			'/<dl\b[^>]*>.*?<\/dl>/is',
		);

		foreach ( $patterns as $pattern ) {
			$content = preg_replace( $pattern, '', $content );
		}

		// Strip Elementor CSS blobs accidentally stored in descriptions.
		$content = preg_replace( '/\/\*! elementor.*?}\s*/is', '', $content );

		return trim( $content );
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_product_content' ) ) {
	function nuttergood_farmley_filter_product_content( $content ) {
		if ( ! is_singular( 'product' ) ) {
			return $content;
		}

		if ( function_exists( 'nuttergood_farmley_strip_specs_from_html' ) ) {
			return nuttergood_farmley_strip_specs_from_html( $content );
		}

		return nuttergood_farmley_clean_product_html( $content );
	}
	add_filter( 'the_content', 'nuttergood_farmley_filter_product_content', 99 );
}