<?php
/**
 * Plugin Name: WooCommerce Custom Product Tab Manager
 * Plugin URI: https://www.wprealizer.com/wprealizer-plugins/wc-custom-product-tab-manager/
 * Description: Using this plugin you can create and manage custom products tabs on your WooCommerce per category or products wise.
 * Version: 1.0.0
 * Author: WPRealizer
 * Author URI: https://wprealizer.com
 * Text Domain: wc-custom-product-tab-manager
 * WC requires at least: 3.0
 * WC tested up to: 5.1.0
 * Domain Path: /languages/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
 * Copyright (c) 2021 WP Realizer (email: wprealizer@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPR_WC_Custom_Product_Tab_Manager final class
 *
 * @class WPR_WC_Custom_Product_Tab_Manager The
 * class that holds the entire plugin
 */
final class WPR_WC_Custom_Product_Tab_Manager {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Instance of self
     *
     * @var WPR_WC_Custom_Product_Tab_Manager
     */
    private static $instance = null;

    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.6.0';

    /**
     * Holds various class instances
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the WPR_WC_Custom_Product_Tab_Manager class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses add_action()
     */
    public function __construct() {
        require_once __DIR__ . '/vendor/autoload.php';

        // Define all constant
        $this->define_constant();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Initializes the WPR_WC_Custom_Product_Tab_Manager() class
     *
     * Checks for an existing WPR_WC_Custom_Product_Tab_Manager() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 1.0.0
     *
     * @param string $prop
     *
     * @return Class Instance
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     *
     * @since 1.0.0
     */
    public function activate() {
        if ( ! $this->has_woocommerce() ) {
            set_transient( 'wc_custom_product_tab_manager_wc_missing_notice', true );
        }

        $installer = new \WPRealizer\WCCustomProductTabManager\Install\WCPTM_Installer();
        $installer->prepare_install();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     *
     * @since 1.0.0
     */
    public function deactivation() {
        delete_transient( 'wc_custom_product_tab_manager_wc_missing_notice', true );
    }

    /**
     * Defined constant
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define_constant() {
        define( 'WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_VERSION', $this->version );
        define( 'WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_FILE', __FILE__ );
        define( 'WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_DIR', __DIR__ );
        define( 'WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_PATH', dirname( WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_FILE ) );
        define( 'WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_ASSETS', plugins_url( '/assets', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_FILE ) );
        define( 'WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_INC', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_PATH . '/includes' );
    }

    /**
     * Load the plugin
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_plugin() {
        //includes file
        $this->includes();

        // init actions and filter
        $this->init_hooks();

        do_action( 'wc_custom_product_tab_manager_loaded', $this );
    }

    /**
     * Includes all files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require_once WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_INC . '/functions.php';
    }

    /**
     * Init all filters
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_hooks() {
        add_action( 'init', [ $this, 'localization_setup' ] );
        add_action( 'init', [ $this, 'init_classes' ], 1 );
    }

    /**
     * Init all the classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_classes() {
        if ( is_admin() ) {
            new \WPRealizer\WCCustomProductTabManager\Admin\WCPTM_Admin();
        }

        new \WPRealizer\WCCustomProductTabManager\WCPTM_Assets();

        $this->container['product_tabs']        = new \WPRealizer\WCCustomProductTabManager\WCPTM_ProductTabs();
        $this->container['product_tabs_groups'] = new \WPRealizer\WCCustomProductTabManager\Admin\WCPTM_ProductTabsGroups();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new \WPRealizer\WCCustomProductTabManager\WCPTM_Ajax();
        }
    }

    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php() {
        if ( version_compare( PHP_VERSION, $this->min_php, '<=' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return apply_filters( 'wc_custom_product_tab_manager_template_path', 'wc-custom-product-tab-manager/' );
    }

    /**
     * Initialize plugin for localization
     *
     * @since 1.0.0
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'wc-custom-product-tab-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Check whether woocommerce is installed or not
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function has_woocommerce() {
        return class_exists( 'WooCommerce' );
    }
}

/**
 * Load WPR_WC_Custom_Product_Tab_Manager
 *
 * @return WPR_WC_Custom_Product_Tab_Manager
 */
function wpr_wc_custom_product_tab_manager() {
    return WPR_WC_Custom_Product_Tab_Manager::init();
}

wpr_wc_custom_product_tab_manager();
