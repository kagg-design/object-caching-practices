<?php
/**
 * Object Caching Practices Demo Plugin
 *
 * @package           ocp-demo
 * @author            KAGG Design
 * @license           GPL-2.0-or-later
 * @wordpress-plugin
 *
 * Plugin Name:       Object Caching Practices Demo Plugin
 * Plugin URI:        https://kagg.eu/en/
 * Description:       Demonstrates Object Caching Practices for WordPress Meetup.
 * Version:           1.0.0
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            KAGG Design
 * Author URI:        https://kagg.eu/en/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ocp
 * Domain Path:       /languages/
 */

namespace KAGG\OCP;

if ( ! defined( 'ABSPATH' ) ) {
	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

if ( defined( 'OCP_VERSION' ) ) {
	return;
}

/**
 * Plugin version.
 */
define( 'OCP_VERSION', '1.0.0' );

/**
 * Path to the plugin dir.
 */
define( 'OCP_PATH', __DIR__ );

/**
 * Plugin dir url.
 */
define( 'OCP_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Main plugin file.
 */
define( 'OCP_FILE', __FILE__ );

/**
 * Init plugin on plugin load.
 */
require_once constant( 'OCP_PATH' ) . '/vendor/autoload.php';

( new Main() )->init();
