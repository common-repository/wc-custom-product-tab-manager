<?php

namespace WPRealizer\WCCustomProductTabManager;

/**
 * WCPTM_Ajax handler
 *
 * @since 1.0.0
 */
class WCPTM_Ajax {

    /**
     * Ajax class constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_filter( 'wp_ajax_wcptm_add_new_product_tab', [ $this, 'add_new_product_tab' ] );
    }

    /**
     * Add new product tab by ajax
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_new_product_tab() {
        check_ajax_referer( 'wc-custom-product-tab-manager-nonce', 'security' );

        if ( ! isset( $_POST['num_of_items'] ) ) {
            die();
        }

        echo wc_custom_product_tab_manager_get_template_part(
            'product-single-new-tab',
            '',
            array(
                'post_id'      => (int) $_POST['post_id'],
                'num_of_items' => (int) $_POST['num_of_items'],
            )
        );

        die();
    }
}
