<?php
/**
 * Product tab single item
 *
 * @since 1.0.0
 *
 * @package wc-custom-product-tab-manager
 */
?>
<div class="wcptm-product-tabs-item wcptm-product-tabs-has-item">
    <div class="wcptm-product-tab-item wcptm-product-tab-has-item">
        <div class="wcptm-product-tab-header">
            <div class="wcptm-product-tab-col1">
                <span class="wcptm-product-tab-sort-handle dashicons dashicons-menu"></span>
                <small class="wcptm-product-tab-type"><?php echo esc_attr( $tab_title ); ?></small>
            </div>
            <div class="wcptm-product-tab-col2">
                <button type="button" class="wcptm-product-tab-remove-addon wcptm-btn wcptm-btn-theme wcptm-btn-sm button"><?php esc_html_e( 'Remove', 'wc-custom-product-tab-manager' ); ?></button>
                <span class="wcptm-item-details-tab-toggle" data-wcptm_tab_id="<?php echo esc_attr( $tab_position ); ?>">
                    <span class="dashicons dashicons-arrow-down-alt2 details-tab-toggle-sort-desc"></span>
                </span>
                <input type="hidden" name="wcptm_product_tab_position[]" class="wcptm-product-tab-position" value="<?php echo esc_attr( $tab_position ); ?>">
            </div>
        </div>
        <div class="wcptm-product-tab-inner-content wcptm-hide tab_body_id_<?php echo esc_attr( $tab_position ); ?>">
            <div class="wcptm-form-group">
                <label for="custom_tab_title" class="form-label">
                    <strong><?php echo esc_html_e( 'Tab Title', 'wc-custom-product-tab-manager' ); ?></strong>
                </label>
                <input type="text" class="wcptm-form-control" style="" name="custom_tab[]" id="custom_tab_title" value="<?php echo esc_attr( $tab_title ); ?>" placeholder="<?php echo esc_html_e( 'Custom Tab Title', 'wc-custom-product-tab-manager' ); ?>">
            </div>
            <div class="wcptm-form-group">
                <label for="_custom_tab_content" class="form-label">
                    <strong><?php esc_html_e( 'Tab Content', 'wc-custom-product-tab-manager' ); ?></strong>
                </label>
                <?php
                wp_editor(
                    $tab_content, 'custom_tab_content_' . $loop, apply_filters(
                        'wcptm_product_tab_content', array(
                            'editor_height'    => 150,
                            'textarea_name'    => 'custom_tab_content[]',
                            'quicktags'        => true,
                            'media_buttons'    => true,
                            'teeny'            => true,
                            'drag_drop_upload' => true,
                            'wp_more'          => true,
                            'editor_class'     => 'custom_tab_content',
                        )
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
