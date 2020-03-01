<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.edisonave.com
 * @since             1.0.0
 * @package           Wppromexp
 *
 * @wordpress-plugin
 * Plugin Name:       WP Prometheus Exporter
 * Plugin URI:        http://www.edisonave.com/wp-prometheus-exporter
 * Description:       Exports Wordpresss Statistics Suitable for scrapping by Promethius.
 * Version:           1.0.0 
 * Author:            Tom Printy
 * Author URI:        http://www.edisonave.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wppromexp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPPROMEXP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wppromexp-activator.php
 */
function activate_wppromexp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppromexp-activator.php';
	Wppromexp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wppromexp-deactivator.php
 */
function deactivate_wppromexp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppromexp-deactivator.php';
	Wppromexp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wppromexp' );
register_deactivation_hook( __FILE__, 'deactivate_wppromexp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wppromexp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wppromexp() {

	$plugin = new Wppromexp();
	$plugin->run();

}
run_wppromexp();
