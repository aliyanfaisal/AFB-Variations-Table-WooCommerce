<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://aliyanfaisal.urbansofts.com
 * @since             1.0.0
 * @package           Afb_Variation_Table
 *
 * @wordpress-plugin
 * Plugin Name:       AFB Variation Table WooCommerce
 * Plugin URI:        https://https://aliyanfaisal.urbansofts.com
 * Description:       This plugin changes the Default Variations Dropdown into a Nice Table view. 
 * Version:           1.0.0
 * Author:            Aliyan Faisal
 * Author URI:        https://https://aliyanfaisal.urbansofts.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       afb-variation-table
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
define( 'AFB_VARIATION_TABLE_VERSION', '1.0.0' );
define("AFB_base_path",plugin_dir_path( __FILE__ ) );
define("AFB_base_url",plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-afb-variation-table-activator.php
 */
function activate_afb_variation_table() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-afb-variation-table-activator.php';
	Afb_Variation_Table_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-afb-variation-table-deactivator.php
 */
function deactivate_afb_variation_table() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-afb-variation-table-deactivator.php';
	Afb_Variation_Table_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_afb_variation_table' );
register_deactivation_hook( __FILE__, 'deactivate_afb_variation_table' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-afb-variation-table.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_afb_variation_table() {

	$plugin = new Afb_Variation_Table();
	$plugin->run();

}
run_afb_variation_table();
