<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_title         = __( 'Create Product Custom Global Tab', 'wc-custom-product-tab-manager' );
$button_title       = __( 'Publish', 'wc-custom-product-tab-manager' );
$enable_global_tabs = 'on';

if ( isset( $_POST ) && ! empty( $_POST['save_wcptm_tab'] ) || ! empty( $_GET['edit'] ) ) {
	$page_title         = __( 'Edit Tabs', 'wc-custom-product-tab-manager' );
	$button_title       = __( 'Update', 'wc-custom-product-tab-manager' );
	$enable_global_tabs = get_post_meta( $edit_id, '_wcptm_enable_global_tabs', true );
}
?>

<div id="wcptm-admin-product-tabs-group" class="wrap woocommerce">
	<h2><?php echo esc_html( $page_title ); ?></h2>
	<div><?php esc_html_e( 'Set up tabs that apply to all products or specific product categories', 'wc-custom-product-tab-manager' ); ?></div><br />

	<form method="POST" action="">
		<table class="form-table global-tabs-form meta-box-sortables">
			<tr>
				<th>
					<label for="tabs-reference"><?php esc_html_e( 'Name', 'wc-custom-product-tab-manager' ); ?></label>
				</th>
				<td>
					<input type="text" name="tabs-reference" id="tabs-reference" style="width:50%;" value="<?php echo esc_attr( $reference ); ?>" />
					<p class="description"><?php esc_html_e( 'This name is for your reference only and will not be visible to customers.', 'wc-custom-product-tab-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="tabs-reference"><?php esc_html_e( 'Enable', 'wc-custom-product-tab-manager' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" class="checkbox" <?php echo checked( $enable_global_tabs, 'on' ); ?> name="enable-global-tabs" id="enable-global-tabs" value="on"> 
						<span class="description"><?php esc_html_e( 'Enable this to use for global tabs', 'wc-custom-product-tab-manager' ); ?></span>
					</label>
				</td>
			</tr>
			<tr>
				<th>
					<label for="tabs-objects"><?php esc_html_e( 'Product Categories', 'wc-custom-product-tab-manager' ); ?></label>
				</th>
				<td>
					<select id="tabs-objects" name="tabs-objects[]" multiple="multiple" style="width:50%;" class="wcptm-tabs-objects-select wc-pao-enhanced-select">
						<option value="all" <?php selected( in_array( 'all', $objects ), true ); ?>><?php esc_html_e( 'All Products', 'wc-custom-product-tab-manager' ); ?></option>
						<optgroup label="<?php esc_attr_e( 'Product categories', 'wc-custom-product-tab-manager' ); ?>">
							<?php
							$terms = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );

							foreach ( $terms as $term ) {
								echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( in_array( $term->term_id, $objects ), true, false ) . '>' . esc_html( $term->name ) . '</option>';
							}
							?>
						</optgroup>
						<?php do_action( 'wcptm_tabs_global_edit_objects', $objects ); ?>
					</select>
					<p class="description"><?php esc_html_e( 'Select which categories this tabs should apply to. Create tabs for a single product when editing that product.', 'wc-custom-product-tab-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<td id="poststuff" class="postbox" colspan="2">
					<?php
					wc_custom_product_tab_manager_get_template_part(
			            'product-tabs',
			            '',
			            array(
			                'post_id'      => $edit_id,
			                'post'         => $global_tabs,
			                'product_tabs' => $product_tabs,
			            )
			        );
					?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="hidden" name="edit_id" value="<?php echo ( ! empty( $edit_id ) ? esc_attr( $edit_id ) : '' ); ?>" />
			<input type="hidden" name="save_wcptm_tab" value="true" />
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( $button_title ); ?>">
		</p>
	</form>
</div>
