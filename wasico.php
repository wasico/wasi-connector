<?php
/**
 * Wasi Connector plugin
 *
 * @link              wasi.co
 * @since             1.1.5
 * @package           Wasi_Connector
 *
 * @wordpress-plugin
 * Plugin Name:       Wasi Connector
 * Plugin URI:        https://api.wasi.co/
 * Description:       Plugin to convert your website into a Real Estate Listing site using your properties on Wasi.co
 * Version:           2.0.0
 * Author:            WasiCo, Inc
 * Author URI:        wasi.co
 * License:           Commercial
 * Text Domain:       wasico
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'WASICO_VERSION', '2.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in init/class-wasi-connector-activator.php
 */
function activate_wasi_connector() {
	require_once plugin_dir_path( __FILE__ ) . 'init/class-wasi-connector-activator.php';
	Wasi_Connector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in init/class-wasi-connector-deactivator.php
 */
function deactivate_wasi_connector() {
	require_once plugin_dir_path( __FILE__ ) . 'init/class-wasi-connector-deactivator.php';
	Wasi_Connector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wasi_connector' );
register_deactivation_hook( __FILE__, 'deactivate_wasi_connector' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'init/class-wasi-connector.php';

$wasi_plugin = new Wasi_Connector();

/**
 * Fetch the specified property type.
 *
 * @param integer|string $id_type The property type ID.
 *
 * @return string The property type name.
 */
function get_wasi_property_type( $id_type ) {
	global $wasi_plugin;
	return $wasi_plugin->plugin_public->get_api_client()->get_property_type( $id_type );
}
