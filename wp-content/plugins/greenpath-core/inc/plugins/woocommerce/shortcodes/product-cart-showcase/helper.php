<?php

if ( ! function_exists( 'greenpath_core_add_rest_api_product_showcase_cart_global_variables' ) ) {
    /**
     * Extend main rest api variables with new case
     *
     * @param array $global - list of variables
     * @param string $namespace - rest namespace url
     *
     * @return array
     */
    function greenpath_core_add_rest_api_product_showcase_cart_global_variables( $global, $namespace ) {
        $global['productShowcaseCartRestRoute'] = $namespace . '/get-cart-data';

        return $global;
    }

    add_filter( 'greenpath_filter_rest_api_global_variables', 'greenpath_core_add_rest_api_product_showcase_cart_global_variables', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_add_rest_api_product_showcase_cart_route' ) ) {
    /**
     * Extend main rest api routes with new case
     *
     * @param array $routes - list of rest routes
     *
     * @return array
     */
    function greenpath_core_add_rest_api_product_showcase_cart_route( $routes ) {
        $routes['get-cart-data'] = array(
            'route'    => 'get-cart-data',
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'greenpath_core_get_new_cart_data',
            'args'     => array(),
        );

        return $routes;
    }

    add_filter( 'greenpath_filter_rest_api_routes', 'greenpath_core_add_rest_api_product_showcase_cart_route' );
}

if ( ! function_exists( 'greenpath_core_get_new_cart_data' ) ) {
    /**
     * Function that load new cart data
     *
     * @return void
     */
    function greenpath_core_get_new_cart_data() {

        $currency_symbol     = get_woocommerce_currency_symbol();
        $product_price_array = array();

        $data_ids = $_POST["product-cart-showcase-ids"];

        $data_ids = explode(',', $data_ids);

        foreach ( $data_ids as $data_id ) {
            $product = wc_get_product( $data_id );
            $product_price_array[] = $product->get_price();
        }

        $new_cart_data = array(
            'add_to_cart_html'      => '',
            'remove_from_cart_html' => '',
            'cart_total_amount'     => array_sum( $product_price_array ) . $currency_symbol,
            'cart_total_quantity'   => sprintf(esc_html__('for %s item(s)', 'greenpath-core'), count($product_price_array)),
        );

        greenpath_core_get_ajax_status( 'success', esc_html__('OK', 'greenpath_core'), $new_cart_data );
    }
}


if ( ! function_exists('greenpath_core_add_rest_api_product_showcase_woo_cart_global_variables') ) {
    /**
     * Extend main rest api variables with new case
     *
     * @param array $global - list of variables
     * @param string $namespace - rest namespace url
     *
     * @return array
     */
    function greenpath_core_add_rest_api_product_showcase_woo_cart_global_variables( $global, $namespace ) {
        $global['productShowcaseWooCartRestRoute'] = $namespace . '/get-woo-cart-data';

        return $global;
    }

    add_filter('greenpath_filter_rest_api_global_variables', 'greenpath_core_add_rest_api_product_showcase_woo_cart_global_variables', 10, 2);
}

if ( ! function_exists('greenpath_core_add_rest_api_product_showcase_woo_cart_route') ) {
    /**
     * Extend main rest api routes with new case
     *
     * @param array $routes - list of rest routes
     *
     * @return array
     */
    function greenpath_core_add_rest_api_product_showcase_woo_cart_route( $routes ) {
        $routes['get-woo-cart-data'] = array(
            'route'    => 'get-woo-cart-data',
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'greenpath_core_get_new_woo_cart_data',
            'args'     => array(),
        );

        return $routes;
    }

    add_filter('greenpath_filter_rest_api_routes', 'greenpath_core_add_rest_api_product_showcase_woo_cart_route');
}

if ( ! function_exists('greenpath_core_get_new_woo_cart_data') ) {
    /**
     * Function that load new cart data
     *
     * @return void
     */
    function greenpath_core_get_new_woo_cart_data() {

        $data_ids = $_POST["product-cart-showcase-ids"];
        $data_ids = explode(',', $data_ids);
        $flag = true;

        foreach ($data_ids as $data_id) {
	        if ( is_object( WC()->cart ) ) {
		        $flag = $flag && WC()->cart->add_to_cart( $data_id );
	        }
        }

        $woo_cart_data = array(
            'woo_dropdown_opener'   => greenpath_core_get_template_part( 'plugins/woocommerce/widgets/dropdown-cart', 'templates/parts/opener' ),
            'woo_dropdown_content'  => greenpath_core_get_template_part( 'plugins/woocommerce/widgets/dropdown-cart', 'templates/content' ),
            'success_button_text'   => esc_html__('View Cart & Checkout', 'greenpath-core'),
            'success_button_url'    => wc_get_cart_url(),
        );

        greenpath_core_get_ajax_status( 'success', esc_html__( 'OK', 'greenpath-core' ), $woo_cart_data );

        if ( $flag ) {
            greenpath_core_get_ajax_status( 'success', esc_html__( 'OK', 'greenpath_core' ), $woo_cart_data );
        } else {
            greenpath_core_get_ajax_status( 'fail', esc_html__( 'Something went wrong', 'greenpath_core' ), $woo_cart_data );
        }
    }
}

