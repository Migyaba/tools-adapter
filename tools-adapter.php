<?php
/**
 * Plugin Name: Tools Adapter
 * Description: Extension Elementor — widgets WooCommerce (archive filtrable, catégories, prix, grille & carrousel).
 * Version:     2.0.0
 * Author:      Miguel Missetcho
 * Author URI:  https://miguelmissetcho.com/
 * Text Domain: tools-adapter
 * Domain Path: /languages
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Elementor tested up to: 3.25
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TOOLS_ADAPTER_VERSION', '2.0.0' );
define( 'TOOLS_ADAPTER_FILE', __FILE__ );
define( 'TOOLS_ADAPTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOOLS_ADAPTER_URL', plugin_dir_url( __FILE__ ) );

require_once TOOLS_ADAPTER_PATH . 'includes/i18n.php';

/**
 * Bootstrap after plugins are loaded.
 */
function tools_adapter_init() {
	// Elementor is required.
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'tools_adapter_missing_elementor_notice' );
		return;
	}

	require_once TOOLS_ADAPTER_PATH . 'includes/plugin.php';
	\ToolsAdapter\Plugin::instance();
}
add_action( 'plugins_loaded', 'tools_adapter_init' );

/**
 * Admin notice when Elementor is missing.
 */
function tools_adapter_missing_elementor_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'Tools Adapter nécessite Elementor pour fonctionner.', 'tools-adapter' );
	echo '</p></div>';
}
