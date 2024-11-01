<?php

namespace WPRealizer\WCCustomProductTabManager;

/**
 * WCPTM_ProductTabs Class
 *
 * @since 1.0.0
 *
 * @package wc-custom-product-tab-manager
 */
class WCPTM_ProductTabs {

    /**
     * WCPTM_ProductTabs class initiate
     *
     * @return void
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Init Hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'save_post', [ $this, 'save_product_tabs_data' ], 35, 3 );
        add_filter( 'woocommerce_product_tabs', [ $this, 'add_custom_product_tabs' ], 99 );

        // Add our Custom Product Tabs panel to the WooCommerce panel container
        add_action( 'woocommerce_product_write_panel_tabs', [ $this, 'render_custom_product_tabs' ] );
        add_action( 'woocommerce_product_data_panels', [ $this, 'product_page_custom_tabs_panel' ] );
    }

    /**
     * Adds a new tab to the Product Data postbox
     * in the admin product interface
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render_custom_product_tabs() {
        wc_custom_product_tab_manager_get_template_part(
            'product-panel-tab'
        );
    }

    /**
     * Adds the panel to the Product Data postbox in the
     * product interface
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function product_page_custom_tabs_panel() {
        global $post;

        wc_custom_product_tab_manager_get_template_part(
            'product-tabs',
            '',
            array(
                'post_id'         => $post->ID,
                'post'            => $post,
                'is_product_page' => true,
            )
        );
    }

    /**
     * Set product tabs data
     *
     * @since 1.0.0
     *
     * @param integer $post_id
     * @param obj     $post
     * @param obj     $update
     *
     * @return void
     */
    public function save_product_tabs_data( $post_id, $post, $update ) {
        if ( ! $post_id ) {
            return;
        }

        if ( 'product' !== $post->post_type ) {
            return;
        }

        if ( ! isset( $_POST['wcptm_manage_tabs_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wcptm_manage_tabs_nonce'] ), 'wcptm_manage_tabs' ) ) {
            return;
        }

        $product_custom_tabs    = array();
        $product_number_of_tabs = 0;
        $disabled_global_tabs   = isset( $_POST['wcptm-disabled-global-tabs'] ) ? sanitize_text_field( wp_unslash( $_POST['wcptm-disabled-global-tabs'] ) ) : '';
        $disabled_others_tabs   = isset( $_POST['wcptm-disabled-others-tabs'] ) ? sanitize_text_field( wp_unslash( $_POST['wcptm-disabled-others-tabs'] ) ) : '';

        if ( isset( $_POST['custom_tab'] ) && isset( $_POST['custom_tab_content'] ) && isset( $_POST['wcptm_product_tab_position'] ) && count( $_POST['custom_tab'] ) === count( $_POST['custom_tab_content'] ) ) {
            $tab_title    = wp_unslash( $_POST['custom_tab'] );
            $tab_content  = wp_unslash( $_POST['custom_tab_content'] );
            $tab_position = wp_unslash( $_POST['wcptm_product_tab_position'] );

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

        update_post_meta( $post_id, '_wcptm_product_custom_tabs', $product_custom_tabs );
        update_post_meta( $post_id, '_wcptm_product_number_of_tabs', $product_number_of_tabs );
        update_post_meta( $post_id, '_wcptm_disabled_global_tabs', $disabled_global_tabs );
        update_post_meta( $post_id, '_wcptm_disabled_others_tabs', $disabled_others_tabs );
    }

    /**
     * Add the custom product tab to the front-end single product page
     *
     * @since 1.0.0
     *
     * @param array $tabs
     *
     * @return array
     */
    public function add_custom_product_tabs( $tabs ) {
        global $product;

        $product_id      = method_exists( $product, 'get_id' ) === true ? $product->get_id() : $product->ID;
        $product_tabs    = maybe_unserialize( get_post_meta( $product_id, '_wcptm_product_custom_tabs', true ) );
        $disabled_global = get_post_meta( $product_id, '_wcptm_disabled_global_tabs', true );
        $disabled_others = get_post_meta( $product_id, '_wcptm_disabled_others_tabs', true );
        $tabs            = ( 'on' === $disabled_others ) ? array() : $tabs;

        if ( is_array( $product_tabs ) && ! empty( $product_tabs ) ) {
            $i       = 25;
            $inc     = 0;
            $tab_key = 'wcptm_cutom_product_tab_' . $inc;

            foreach ( $product_tabs as $tab ) {
                if ( empty( $tab['tab_title'] ) ) {
                    continue;
                }

                $tabs[ $tab_key ] = array(
                    'title'     => $tab['tab_title'],
                    'priority'  => $i++,
                    'callback'  => [ $this, 'custom_product_tabs_panel_content' ],
                    'content'   => $tab['tab_content'],
                );
                $inc++;
                $tab_key = 'wcptm_cutom_product_tab_' . $inc;
            }
        }

        if ( 'on' !== $disabled_global ) {
            $tabs = $this->get_global_custom_product_tabs( $tabs, $product, $product_id );
        }

        $tabs = apply_filters( 'wcptm_custom_product_tabs', $tabs, $product );

        return $tabs;
    }

    /**
     * Add the custom product tab to the front-end single product page
     *
     * @since 1.0.0
     *
     * @param array $tabs
     *
     * @return array
     */
    public function get_global_custom_product_tabs( $tabs, $product, $product_id ) {
        $global_tabs = wpr_wc_custom_product_tab_manager()->product_tabs_groups->get_all_global_groups();

        if ( empty( $global_tabs ) ) {
            return $tabs;
        }

        foreach ( $global_tabs as $global_tab ) {
            $tab_product_id     = absint( $global_tab['id'] );
            $enable_global_tabs = get_post_meta( $tab_product_id, '_wcptm_enable_global_tabs', true );

            if ( 'on' !== $enable_global_tabs ) {
                continue;
            }

            $select_categories  = $global_tab['restrict_to_categories'];
            $product_tabs       = maybe_unserialize( get_post_meta( $tab_product_id, '_wcptm_custom_tabs_global', true ) );
            $enable_global_tabs = get_post_meta( $tab_product_id, '_wcptm_enable_global_tabs', true );
            $for_all_products   = get_post_meta( $tab_product_id, '_wcptm_global_all_products', true );
            $current_categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
            $is_usable          = false;

            if ( 1 === (int) $for_all_products ) {
                $is_usable = true;
            } elseif ( ! empty( $select_categories ) && is_array( $current_categories ) ) {
                foreach ( $current_categories as $categorie_id ) {
                    if ( array_key_exists( $categorie_id, $select_categories ) ) {
                        $is_usable = true;
                        break;
                    }
                }
            }

            if ( ! $is_usable ) {
                continue;
            }

            if ( is_array( $product_tabs ) && ! empty( $product_tabs ) ) {
                $i       = 25;
                $inc     = 0;
                $tab_key = 'wcptm_cutom_global_product_tab_' . $tab_product_id . '_' . $inc;

                foreach ( $product_tabs as $tab ) {
                    if ( empty( $tab['tab_title'] ) ) {
                        continue;
                    }

                    $tabs[ $tab_key ] = array(
                        'title'     => $tab['tab_title'],
                        'priority'  => $i++,
                        'callback'  => [ $this, 'custom_product_tabs_panel_content' ],
                        'content'   => $tab['tab_content'],
                    );
                    $inc++;
                    $tab_key = 'wcptm_cutom_global_product_tab_' . $tab_product_id . '_' . $inc;
                }
            }
        }

        $tabs = apply_filters( 'wcptm_custom_global_product_tabs', $tabs, $product );

        return $tabs;
    }

    /**
     * Render the custom product tab panel content for the given $tab
     *
     * @since 1.0.0
     *
     * @param string $key
     * @param array  $tab
     *
     * @return array
     */
    public function custom_product_tabs_panel_content( $key, $tab ) {
        if ( empty( $tab ) ) {
            return;
        }

        global $wp_embed;

        echo sprintf( '<h2>%s</h2>', $tab['title'] );

        $tab_content  = do_blocks( $tab['content'] );
        $tab_content  = $wp_embed->run_shortcode( $tab_content );
        $tab_content  = do_shortcode( $tab_content );
        $tab_content  = $wp_embed->autoembed( $tab_content );
        $tab_content  = wptexturize( $tab_content );
        $tab_content  = wpautop( $tab_content );
        $tab_content  = shortcode_unautop( $tab_content );
        $tab_content  = prepend_attachment( $tab_content );
        $content_tags = function_exists( 'wp_filter_content_tags' ) ? 'wp_filter_content_tags' : 'wp_make_content_images_responsive';
        $tab_content  = $content_tags( $tab_content );
        $tab_content  = convert_smilies( $tab_content );
        $show_content = apply_filters( 'wcptm_custom_tab_content_display', $tab_content );

        echo $show_content;
    }
}
