<?php

namespace WPRealizer\WCCustomProductTabManager\Admin;

/**
 * WCPTM_Admin Class
 *
 * @since 1.0.0
 */
class WCPTM_Admin {

    /**
     * Admin Class constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'init', [ $this, 'init_post_types' ], 20 );
        add_action( 'admin_menu', [ $this, 'admin_menu' ], 10 );
        add_action( 'admin_notices', [ $this, 'render_missing_woocommerce_notice' ] );
    }

    /**
     * Register custom post type
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_post_types() {
        register_post_type(
            'wpr_wcptm_tabs',
            array(
                'public'              => false,
                'show_ui'             => false,
                'capability_type'     => 'product',
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'hierarchical'        => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => array( 'title' ),
                'show_in_nav_menus'   => false,
            )
        );

        register_taxonomy_for_object_type( 'product_cat', 'wpr_wcptm_tabs' );
    }

    /**
     * Load admin menus pages
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_menu() {
        add_submenu_page( 'edit.php?post_type=product', __( 'Custom Tabs', 'wc-custom-product-tab-manager' ), __( 'Custom Tabs', 'wc-custom-product-tab-manager' ), 'manage_woocommerce', 'wc-custom-product-tab-manager', [ $this, 'global_tabs_admin' ] );
    }

    /**
     * Controls the global tabs admin page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function global_tabs_admin() {
        if ( ! empty( $_GET['add'] ) || ! empty( $_GET['edit'] ) ) {
            if ( $_POST ) {
                if ( ! isset( $_POST['wcptm_manage_tabs_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wcptm_manage_tabs_nonce'] ), 'wcptm_manage_tabs' ) ) {
                    return;
                }

                $edit_id = $this->save_global_tabs( $_POST );

                if ( $edit_id ) {
                    echo '<div class="updated"><p>' . esc_html__( 'Tabs saved successfully', 'wc-custom-product-tab-manager' ) . '</p></div>';
                }

                $reference    = isset( $_POST['tabs-reference'] ) ? sanitize_text_field( wp_unslash( $_POST['tabs-reference'] ) ) : '';
                $priority     = isset( $_POST['tabs-priority'] ) ? absint( $_POST['tabs-priority'] ) : 10;
                $objects      = ! empty( $_POST['tabs-objects'] ) ? array_map( 'absint', $_POST['tabs-objects'] ) : array();
                $product_tabs = array_filter( (array) get_post_meta( $edit_id, '_wcptm_custom_tabs_global', true ) );
            }

            if ( ! empty( $_GET['edit'] ) ) {
                $edit_id     = absint( $_GET['edit'] );
                $global_tabs = get_post( $edit_id );

                if ( ! $global_tabs ) {
                    echo '<div class="error">' . esc_html__( 'Error: Tabs not found', 'wc-custom-product-tab-manager' ) . '</div>';
                    return;
                }

                $reference      = $global_tabs->post_title;
                $priority       = get_post_meta( $global_tabs->ID, '_priority', true );
                $objects        = (array) wp_get_post_terms( $global_tabs->ID, apply_filters( 'wc_custom_product_tab_manager_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
                $product_tabs   = array_filter( (array) get_post_meta( $global_tabs->ID, '_wcptm_custom_tabs_global', true ) );

                if ( (int) get_post_meta( $global_tabs->ID, '_wcptm_global_all_products', true ) === 1 ) {
                    $objects[] = 0;
                }
            } elseif ( ! empty( $edit_id ) ) {
                $global_tabs  = get_post( $edit_id );
                $reference    = $global_tabs->post_title;
                $priority     = get_post_meta( $global_tabs->ID, '_priority', true );
                $objects      = (array) wp_get_post_terms( $global_tabs->ID, apply_filters( 'wcptm_tabs_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
                $product_tabs = array_filter( (array) get_post_meta( $global_tabs->ID, '_wcptm_custom_tabs_global', true ) );

                if ( (int) get_post_meta( $global_tabs->ID, '_wcptm_global_all_products', true ) === 1 ) {
                    $objects[] = 0;
                }
            } else {
                $global_addons_count = wp_count_posts( 'wpr_wcptm_tabs' );
                $reference           = __( 'Product Tab Group', 'wc-custom-product-tab-manager' ) . ' #' . ( $global_addons_count->publish + 1 );
                $priority            = 10;
                $objects             = array( 0 );
                $product_tabs        = array();
            }

            wc_custom_product_tab_manager_get_template_part(
                'admin/html-product-tabs-global-admin-manage',
                '',
                array(
                    'edit_id'      => isset( $edit_id ) ? $edit_id : '',
                    'global_tabs'  => isset( $global_tabs ) ? $global_tabs : '',
                    'reference'    => $reference,
                    'priority'     => $priority,
                    'objects'      => $objects,
                    'product_tabs' => $product_tabs,
                )
            );
        } else {
            if ( ! empty( $_GET['delete'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'delete_wcptm_tab' ) ) {
                wp_delete_post( absint( $_GET['delete'] ), true );
                echo '<div class="updated"><p>' . esc_html__( 'Tabs deleted successfully', 'wc-custom-product-tab-manager' ) . '</p></div>';
            }

            wc_custom_product_tab_manager_get_template_part( 'admin/html-product-tabs-global-admin' );
        }
    }

    /**
     * Save global custom tabs
     *
     * @since 1.0.0
     *
     * @param array $postdata
     *
     * @return int $edit_id
     */
    public function save_global_tabs( $postdata ) {
        $edit_id     = ! empty( $postdata['edit_id'] ) ? absint( $postdata['edit_id'] ) : '';
        $reference   = wc_clean( $postdata['tabs-reference'] );
        $objects     = ! empty( $postdata['tabs-objects'] ) ? array_map( 'absint', $postdata['tabs-objects'] ) : array();
        $enable_tabs = isset( $postdata['enable-global-tabs'] ) ? sanitize_text_field( $postdata['enable-global-tabs'] ) : '';

        if ( ! $reference ) {
            $global_addons_count = wp_count_posts( 'wpr_wcptm_tabs' );
            $reference           = __( 'Product Tab Group', 'wc-custom-product-tab-manager' ) . ' #' . ( $global_addons_count->publish + 1 );
        }

        if ( $edit_id ) {
            $edit_post               = array();
            $edit_post['ID']         = $edit_id;
            $edit_post['post_title'] = $reference;

            wp_update_post( $edit_post );
            wp_set_post_terms( $edit_id, $objects, 'product_cat', false );

            do_action( 'wc_custom_product_tab_manager_tabs_global_edit_addons', $edit_post, $objects );
        } else {
            $edit_id = wp_insert_post(
                apply_filters(
                    'wc_custom_product_tab_manager_tabs_global_insert_post_args', array(
                        'post_title'  => $reference,
                        'post_status' => 'publish',
                        'post_type'   => 'wpr_wcptm_tabs',
                        'tax_input'   => array(
                            'product_cat' => $objects,
                        ),
                    ), $reference, $objects
                )
            );
        }

        if ( in_array( 0, $objects, true ) ) {
            update_post_meta( $edit_id, '_wcptm_global_all_products', 1 );
        } else {
            update_post_meta( $edit_id, '_wcptm_global_all_products', 0 );
        }

        update_post_meta( $edit_id, '_wcptm_enable_global_tabs', $enable_tabs );

        $this->save_product_tabs_admin_data( $edit_id, $postdata );

        return $edit_id;
    }

    /**
     * Set product tabs data
     *
     * @since 1.0.0
     *
     * @param int   $post_id
     * @param array $postdata
     *
     * @return void
     */
    public function save_product_tabs_admin_data( $post_id, $postdata ) {
        if ( ! $post_id ) {
            return;
        }

        $product_custom_tabs    = array();
        $product_number_of_tabs = 0;

        if ( isset( $postdata['custom_tab'] ) && isset( $postdata['custom_tab_content'] ) && isset( $postdata['wcptm_product_tab_position'] ) && count( $postdata['custom_tab'] ) === count( $postdata['custom_tab_content'] ) ) {
            $tab_title    = $postdata['custom_tab'];
            $tab_content  = $postdata['custom_tab_content'];
            $tab_position = $postdata['wcptm_product_tab_position'];

            foreach ( $tab_title as $key => $title ) {
                if ( empty( $title ) || ! isset( $tab_content[ $key ] ) || empty( $tab_content[ $key ] ) ) {
                    continue;
                }

                $wcptm_tab_title    = sanitize_text_field( $title );
                $wcptm_tab_content  = sanitize_textarea_field( $tab_content[ $key ] );
                $wcptm_tab_position = sanitize_text_field( $tab_position[ $key ] );

                $product_custom_tabs[] = array(
                    'tab_title'    => $wcptm_tab_title,
                    'tab_content'  => $wcptm_tab_content,
                    'tab_position' => $wcptm_tab_position,
                );
                $product_number_of_tabs++;
            }
        }

        update_post_meta( $post_id, '_wcptm_custom_tabs_global', $product_custom_tabs );
        update_post_meta( $post_id, '_wcptm_number_of_tabs_global', $product_number_of_tabs );
    }

    /**
     * Render menu page content
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render_page_content() {
        wc_custom_product_tab_manager_get_template_part( 'admin-content' );
    }

    /**
     * Missing woocomerce notice
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render_missing_woocommerce_notice() {
        if ( ! get_transient( 'wc_custom_product_tab_manager_wc_missing_notice' ) ) {
            return;
        }

        if ( wpr_wc_custom_product_tab_manager()->has_woocommerce() ) {
            return delete_transient( 'wc_custom_product_tab_manager_wc_missing_notice' );
        }

        $plugin_url = self_admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' );

        /* translators: %s: wc plugin url */
        $message = sprintf( __( 'Custom Tabs for WooCommerce Products requires WooCommerce to be installed and active. You can activate <a href="%s">WooCommerce</a> here.', 'wc-custom-product-tab-manager' ), $plugin_url );

        echo wp_kses_post( sprintf( '<div class="error"><p><strong>%1$s</strong></p></div>', $message ) );
    }
}
