<?php

if ( ! function_exists( 'greenpath_core_filter_clients_list_image_only_no_hover' ) ) {
    /**
     * Function that add variation layout for this module
     *
     * @param array $variations
     *
     * @return array
     */
    function greenpath_core_filter_clients_list_image_only_no_hover( $variations ) {
        $variations['no-hover'] = esc_html__( 'No Hover', 'greenpath-core' );

        return $variations;
    }

    add_filter( 'greenpath_core_filter_clients_list_image_only_animation_options', 'greenpath_core_filter_clients_list_image_only_no_hover' );
}