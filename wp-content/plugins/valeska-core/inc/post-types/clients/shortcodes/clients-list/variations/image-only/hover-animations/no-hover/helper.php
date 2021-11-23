<?php

if ( ! function_exists( 'valeska_core_filter_clients_list_image_only_no_hover' ) ) {
    /**
     * Function that add variation layout for this module
     *
     * @param array $variations
     *
     * @return array
     */
    function valeska_core_filter_clients_list_image_only_no_hover( $variations ) {
        $variations['no-hover'] = esc_html__( 'No Hover', 'valeska-core' );

        return $variations;
    }

    add_filter( 'valeska_core_filter_clients_list_image_only_animation_options', 'valeska_core_filter_clients_list_image_only_no_hover' );
}