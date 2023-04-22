<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://aliyanfaisal.urbansofts.com
 * @since      1.0.0
 *
 * @package    Afb_Variation_Table
 * @subpackage Afb_Variation_Table/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Afb_Variation_Table
 * @subpackage Afb_Variation_Table/includes
 * @author     Aliyan Faisal <aliyanfaisal15@gmail.com>
 */
class Afb_Variation_Table_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'afb-variation-table',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
