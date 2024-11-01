<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap woocommerce">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br/></div>
	<h2><?php esc_html_e( 'Product Custom Tabs', 'wc-custom-product-tab-manager' ); ?> <a href="<?php echo add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=wc-custom-product-tab-manager' ) ); ?>" class="add-new-h2"><?php esc_html_e( 'Create New', 'wc-custom-product-tab-manager' ); ?></a></h2><br/>
	<table id="global-wcptm-tabs-table" class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Tab Group Name', 'wc-custom-product-tab-manager' ); ?></th>
				<th><?php esc_html_e( 'Products and Categories', 'wc-custom-product-tab-manager' ); ?></th>
				<th><?php esc_html_e( 'Number of Tabs', 'wc-custom-product-tab-manager' ); ?></th>
			</tr>
		</thead>
		<tbody id="the-list">
			<?php
			$global_tabs = wpr_wc_custom_product_tab_manager()->product_tabs_groups->get_all_global_groups();

			if ( $global_tabs ) {
				foreach ( $global_tabs as $global_tab ) {
					?>
					<tr>
						<td><a href="<?php echo add_query_arg( 'edit', $global_tab['id'], admin_url( 'edit.php?post_type=product&page=wc-custom-product-tab-manager' ) ); ?>"><?php echo $global_tab['name']; ?></a>
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo esc_url( add_query_arg( 'edit', $global_tab['id'], admin_url( 'edit.php?post_type=product&page=wc-custom-product-tab-manager' ) ) ); ?>">
										<?php esc_html_e( 'Edit', 'wc-custom-product-tab-manager' ); ?>
									</a> | 
								</span>
								<span class="delete">
									<a class="delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'delete', $global_tab['id'], admin_url( 'edit.php?post_type=product&page=wc-custom-product-tab-manager' ) ), 'delete_wcptm_tab' ) ); ?>">
										<?php esc_html_e( 'Delete', 'wc-custom-product-tab-manager' ); ?>
									</a>
								</span>
							</div>
						</td>
						<td>
						<?php
						$all_products = '1' === get_post_meta( $global_tab['id'], '_wcptm_global_all_products', true ) ? true : false;
						$restrict_to_categories = $global_tab['restrict_to_categories'];

						if ( $all_products ) {
							esc_html_e( 'All Products', 'wc-custom-product-tab-manager' );
						} elseif ( 0 === count( $restrict_to_categories ) ) {
							esc_html_e( 'No Products Assigned', 'wc-custom-product-tab-manager' );
						} else {
							$objects    = array_keys( $restrict_to_categories );
							$term_names = array_values( $restrict_to_categories );
							$term_names = apply_filters( 'wcptm_global_tabs_display_term_names', $term_names, $objects );
							echo implode( ', ', $term_names );
						}
						?>
						</td>
						<td>
							<?php echo get_post_meta( $global_tab['id'], '_wcptm_number_of_tabs_global', true ); ?>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="5">
						<?php esc_html_e( 'No Custom Tabs Found,', 'wc-custom-product-tab-manager' ); ?> 
						<a href="<?php echo add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=wc-custom-product-tab-manager' ) ); ?>">
							<?php esc_html_e( 'Create Product Custom Tabs', 'wc-custom-product-tab-manager' ); ?>
						</a>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<p class="wc-pao-doc-link">
		<span class="dashicons dashicons-editor-help"></span>
		<?php
		echo sprintf( __( 'Need help with Custom Tabs for WooCommerce Products? <a href="%s" target="_blank">Click here Visit the Documentation</a>' ), esc_url( 'https://www.wprealizer.com/wprealizer-plugins/custom-product-tabs-manager-for-woocommerce/' ) );
		?>
	</p>
</div>
