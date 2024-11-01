<?php
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get template part implementation for wedocs Looks at the theme directory first
 *
 * @since 1.0.0
 *
 * @return void
 */
function wc_custom_product_tab_manager_get_template_part( $slug, $name = '', $args = [] ) {
    $defaults = [
        'pro' => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $template = '';

    $template = locate_template( [ wpr_wc_custom_product_tab_manager()->template_path() . "{$slug}-{$name}.php", wpr_wc_custom_product_tab_manager()->template_path() . "{$slug}.php" ] );

    $template_path = apply_filters( 'wc_custom_product_tab_manager_set_template_path', wpr_wc_custom_product_tab_manager()->plugin_path() . '/templates', $template, $args );

    if ( ! $template && $name && file_exists( $template_path . "/{$slug}-{$name}.php" ) ) {
        $template = $template_path . "/{$slug}-{$name}.php";
    }

    if ( ! $template && ! $name && file_exists( $template_path . "/{$slug}.php" ) ) {
        $template = $template_path . "/{$slug}.php";
    }

    $template = apply_filters( 'wc_custom_product_tab_manager_get_template_part', $template, $slug, $name );

    if ( $template ) {
        include $template;
    }
}

/**
 * Get other templates (e.g. array data) passing data and including the file
 *
 * @since 1.0.0
 *
 * @param mixed  $template_name
 * @param array  $args
 * @param string $template_path
 * @param string $default_path
 *
 * @return void
 */
function wc_custom_product_tab_manager_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $located = wc_custom_product_tab_manager_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), '2.1' );

        return;
    }

    do_action( 'wc_custom_product_tab_manager_before_template_part', $template_name, $template_path, $located, $args );

    include $located;

    do_action( 'wc_custom_product_tab_manager_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion
 *
 * @since 1.0.0
 *
 * @param mixed  $template_name
 * @param string $template_path
 * @param string $default_path
 *
 * @return string
 */
function wc_custom_product_tab_manager_locate_template( $template_name, $template_path = '', $default_path = '', $pro = false ) {
    if ( ! $template_path ) {
        $template_path = wpr_wc_custom_product_tab_manager()->template_path();
    }

    if ( ! $default_path ) {
        $default_path = wpr_wc_custom_product_tab_manager()->plugin_path() . '/templates/';
    }

    $template = locate_template(
        [
            trailingslashit( $template_path ) . $template_name,
        ]
    );

    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    return apply_filters( 'wc_custom_product_tab_manager_locate_template', $template, $template_name, $template_path );
}
