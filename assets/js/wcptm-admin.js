/**
 * Admin helper functions
 *
 * @package WPRealizer
 */
(function($) {
    var WCCustomProductTabManager = {
        init: function() {
        	$('.wcptm-tabs-objects-select').select2();

            $( '#wcptm-product-tabs-data' ).on( 'click', '.wcptm-product-add-new-tab', function() {
                var num_of_items = $( '.wcptm-product-tab-remove-addon' ).length;

                var data = {
                    action:       'wcptm_add_new_product_tab',
                    post_id:      $('#wcptm-edit-product-id').val(),
                    num_of_items: num_of_items,
                    security:     wc_custom_product_tab_manager.wcptm_tabs_nonce
                };

                $.post( wc_custom_product_tab_manager.ajaxurl, data, function( response ) {
                    if ( $( ".wcptm-product-tabs-groups" ).append( response ) ) {
                        tinymce.execCommand( 'mceRemoveEditor', false, 'custom_tab_content' + num_of_items );
                        tinymce.execCommand( 'mceAddEditor', false, 'custom_tab_content' + num_of_items );
                    }
                });
            });

            $( 'body' ).on( 'click', '.wcptm-item-details-tab-toggle', function() {
                var tab_id = $(this).data( 'wcptm_tab_id' );
                $('.tab_body_id_' + tab_id).toggle();
                $(this).find('span').toggleClass( 'dashicons-arrow-down-alt2 dashicons-arrow-up-alt2' );

                return false;
            });

            $( 'body' ).on( 'click', '.wcptm-product-tabs-expand-all', function() {
                $(this).toggle();
                $('.wcptm-product-tabs-close-all').show();
                $('.wcptm-product-tab-inner-content').show();
                $('.wcptm-item-details-tab-toggle').find('span').toggleClass( 'dashicons-arrow-down-alt2 dashicons-arrow-up-alt2' );
                
                return false;
            });

            $( 'body' ).on( 'click', '.wcptm-product-tabs-close-all', function() {
                $(this).toggle();
                $('.wcptm-product-tabs-expand-all').show();
                $('.wcptm-product-tab-inner-content').hide();
                $('.wcptm-item-details-tab-toggle').find('span').toggleClass( 'dashicons-arrow-down-alt2 dashicons-arrow-up-alt2' );
                
                return false;
            });

            $( '#wcptm-product-tabs-data' ).on( 'click', '.wcptm-product-tab-remove-addon', function() {
                if ( confirm( wc_custom_product_tab_manager.alert_sure_message ) ) {
                    $(this).closest('.wcptm-product-tabs-item').remove();
                }
            });
        },
    };

    $(function() {
        WCCustomProductTabManager.init();
    });
})(jQuery);
