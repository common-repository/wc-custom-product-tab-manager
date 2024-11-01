<?php
namespace WPRealizer\WCCustomProductTabManager\Install;

/**
 * WCPTM_Installer class
 *
 * @since 1.0.0
 */
class WCPTM_Installer {

    /**
     * Prepare for install when activated plugin
     *
     * @since 1.0.0
     */
    public function prepare_install() {
        $this->update_version();
    }

    /**
     * Update plugin version
     *
     * @since 1.0.0
     */
    public function update_version() {
        update_option( '_wc_custom_product_tab_manager_version', WPR_WC_CUSTOM_PRODUCT_TAB_MANAGER_VERSION );
    }
}
