<?php

namespace WPRealizer\WCCustomProductTabManager;

/**
 * WCPTM_Assets class
 *
 * @since 1.0.0
 */
class WCPTM_Assets {

    /**
     * Assets class construct
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_styles' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
    }

    /**
     * Admin register styles
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_admin_styles( $hook ) {
        if ( 'product_page_wc-custom-product-tab-manager' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        wp_register_style( 'wcptm-admin-styles', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_ASSETS . '/css/wcptm-admin.css', false, WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_VERSION, 'all' );
        wp_enqueue_style( 'wcptm-admin-styles' );

        if ( 'product_page_wc-custom-product-tab-manager' === $hook ) {
            wp_register_style( 'wcptm-styles-select2', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_ASSETS . '/vendors/select2/select2.css', false, WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_VERSION, 'all' );
            wp_enqueue_style( 'wcptm-styles-select2' );
        }

        $admin_localized = $this->get_admin_localized_scripts();

        wp_localize_script( 'wcptm-admin-scripts', 'wc_custom_product_tab_manager', $admin_localized );
    }

    /**
     * Admin register scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_admin_scripts( $hook ) {
        if ( 'product_page_wc-custom-product-tab-manager' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        wp_register_script( 'wcptm-admin-scripts', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_ASSETS . '/js/wcptm-admin.js', array( 'jquery-core' ), WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_VERSION, true );
        wp_enqueue_script( 'wcptm-admin-scripts' );

        if ( 'product_page_wc-custom-product-tab-manager' === $hook ) {
            wp_register_script( 'wcptm-script-select2', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_ASSETS . '/vendors/select2/select2.full.min.js', array( 'jquery-core' ), WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_VERSION, true );
            wp_enqueue_script( 'wcptm-script-select2' );
        }

        $admin_localized = $this->get_admin_localized_scripts();

        wp_localize_script( 'wcptm-admin-scripts', 'wc_custom_product_tab_manager', $admin_localized );
    }

    /**
     * Admin script localize
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_admin_localized_scripts() {
        $admin_scripts = apply_filters(
            'wc_custom_product_tab_manager_admin_localized_scripts', array(
                'alert_sure_message' => __( 'Are you want to remove this tab?', 'wc-custom-product-tab-manager' ),
                'ajaxurl'            => admin_url( 'admin-ajax.php' ),
                'wcptm_tabs_nonce'   => wp_create_nonce( 'wc-custom-product-tab-manager-nonce' ),
            )
        );

        return $admin_scripts;
    }
}
