<?php
/**
 * Plugin Name: Tools Adapter
 * Description: Extension Elementor — widgets WooCommerce (archive filtrable, catégories, prix, grille & carrousel).
 * Version:     2.15.0
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

define( 'TOOLS_ADAPTER_VERSION', '2.15.0' );
define( 'TOOLS_ADAPTER_FILE', __FILE__ );
define( 'TOOLS_ADAPTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOOLS_ADAPTER_URL', plugin_dir_url( __FILE__ ) );

require_once TOOLS_ADAPTER_PATH . 'includes/i18n.php';
require_once TOOLS_ADAPTER_PATH . 'includes/admin-settings.php';

/**
 * Bootstrap after plugins are loaded.
 */
function tools_adapter_init() {
	// The settings page (activer/désactiver les widgets) works even without
	// Elementor active, so it is registered unconditionally in wp-admin.
	if ( is_admin() ) {
		new \ToolsAdapter\Admin_Settings();
	}

	// Elementor is required for the widgets themselves.
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'tools_adapter_missing_elementor_notice' );
		return;
	}

	// Boot at `init` (not plugins_loaded): the settings read here contain
	// translated labels, and WordPress 6.7+ warns when translations are loaded
	// before `init`.
	add_action( 'init', 'tools_adapter_boot', 1 );
}
add_action( 'plugins_loaded', 'tools_adapter_init' );

/**
 * Load the plugin core (widgets, assets, AJAX endpoints).
 */
function tools_adapter_boot() {
	require_once TOOLS_ADAPTER_PATH . 'includes/plugin.php';
	\ToolsAdapter\Plugin::instance();
}

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
