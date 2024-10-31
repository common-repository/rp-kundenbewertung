<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.rp-datentechnik.com
 * @since      1.0.0
 *
 * @package    RP_Kundenbewertung
 * @subpackage rp-kundenbewertung/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    RP_Kundenbewertung
 * @subpackage rp-kundenbewertung/includes
 * @author     RP-Datentechnik Hamburg oHG <kontakt@rp-datentechnik.com>
 */
class RP_Kundenbewertung_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'rp-kundenbewertung',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
