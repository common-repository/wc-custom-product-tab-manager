<?php
/**
 * Product Tabs Content
 *
 * @since 1.0.0
 *
 * @package wc-custom-product-tab-manager
 */

$disabled_global_tabs = get_post_meta( $post_id, '_wcptm_disabled_global_tabs', true );
$disabled_others_tabs = get_post_meta( $post_id, '_wcptm_disabled_others_tabs', true );
?>
<?php do_action( 'wcptm_product_tabs_before', $post_id ); ?>

    <div id="wcptm-wc-product-tabs-data" class="wcptm-product-tabs wcptm-edit-row wcptm-clearfix wcptm-border-top wcptm-options-panel panel">
        <div class="wcptm-section-heading" data-togglehandler="wcptm_product_tabs">
            <h2><i class="fa fa-list-alt" aria-hidden="true"></i> <?php esc_html_e( 'Product Custom Tabs', 'wc-custom-product-tab-manager' ); ?></h2>
            <p><?php esc_html_e( 'Manage custom tabs for this product', 'wc-custom-product-tab-manager' ); ?></p>
            <div class="wcptm-clearfix"></div>

            <?php if ( isset( $is_product_page ) && $is_product_page ) : ?>
                <div class="options_group reviews">
                    <p class="form-field comment_status_field ">
                    <label for="wcptm-disabled-global-tabs"><?php esc_html_e( 'Disabled global tabs', 'wc-custom-product-tab-manager' ); ?></label>
                        <input type="checkbox" class="checkbox" <?php echo checked( $disabled_global_tabs, 'on' ); ?> name="wcptm-disabled-global-tabs" id="wcptm-disabled-global-tabs" value="on"> 
                        <span class="description"><?php esc_html_e( 'Enable this to disabled global custom tabs for this product', 'wc-custom-product-tab-manager' ); ?></span>
                    </p>       
                </div>
                <div class="options_group reviews">
                    <p class="form-field comment_status_field ">
                    <label for="wcptm-disabled-others-tabs"><?php esc_html_e( 'Disabled others tabs', 'wc-custom-product-tab-manager' ); ?></label>
                        <input type="checkbox" class="checkbox" <?php echo checked( $disabled_others_tabs, 'on' ); ?> name="wcptm-disabled-others-tabs" id="wcptm-disabled-others-tabs" value="on"> 
                        <span class="description"><?php esc_html_e( 'Enable this to disabled others tabs for this product, now show only below tabs', 'wc-custom-product-tab-manager' ); ?></span>
                    </p>       
                </div>
            <?php endif; ?>
        </div>
        <div class="wcptm-section-content">
            <div class="wcptm-clearfix wcptm-product-tabs-container">
                <div id="wcptm-product-tabs-data" class="wcptm-product-tabs-data-panel">
                    <div class="wcptm-product-tabs-header">
                        <p><strong><?php esc_html_e( 'Custom Tabs', 'wc-custom-product-tab-manager' ); ?></strong></p>
                        <p class="wcptm-product-tabs-toolbar">
                            <a href="#" class="wcptm-product-tabs-expand-all"><?php esc_html_e( 'Expand all', 'wc-custom-product-tab-manager' ); ?></a><a href="#" class="wcptm-product-tabs-close-all wcptm-hide"><?php esc_html_e( 'Close all', 'wc-custom-product-tab-manager' ); ?></a>
                        </p>
                    </div>
                    <div class="wcptm-product-tabs-groups">
                        <?php
                        $product_tabs = isset( $product_tabs ) && ! empty( $product_tabs ) && is_array( $product_tabs ) ? $product_tabs : array_filter( (array) get_post_meta( $post_id, '_wcptm_product_custom_tabs', true ) );
                        $loop         = 0;
                        
                        if ( $product_tabs && is_array( $product_tabs ) ) {
                            foreach ( $product_tabs as $product_tab ) {
                                wc_custom_product_tab_manager_get_template_part(
                                    'product-single-tab', '', array(
                                        'is_product_edit' => true,
                                        'tab_title'       => $product_tab['tab_title'],
                                        'tab_content'     => $product_tab['tab_content'],
                                        'tab_position'    => $product_tab['tab_position'],
                                        'loop'            => $loop,
                                        'pro'             => false,
                                        'post_id'         => $post_id,
                                        'post'            => $post,
                                    )
                                );

                                $loop++;
                            }
                        } else {
                            wc_custom_product_tab_manager_get_template_part(
                                'product-single-new-tab', '', array(
                                    'num_of_items' => 0,
                                )
                            );
                        }
                        ?>
                    </div>
                    <div class="wcptm-product-tabs-actions">
                        <?php wp_nonce_field( 'wcptm_manage_tabs', 'wcptm_manage_tabs_nonce' ); ?>
                        <button type="button" class="wcptm-btn wcptm-btn-theme wcptm-btn-sm wcptm-product-add-new-tab"><?php esc_html_e( 'Add New Tab', 'wc-custom-product-tab-manager' ); ?></button>
                    </div>
                </div>
            </div>
        </div><!-- .wcptm-side-right -->
    </div><!-- .wcptm-product-inventory -->

<?php do_action( 'wcptm_product_tabs_after', $post_id ); ?>
