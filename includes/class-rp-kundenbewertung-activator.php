<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.rp-datentechnik.com
 * @since      1.0.0
 *
 * @package    RP_Kundenbewertung
 * @subpackage rp-kundenbewertung/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    RP_Kundenbewertung
 * @subpackage rp-kundenbewertung/includes
 * @author     RP-Datentechnik Hamburg oHG <kontakt@rp-datentechnik.com>
 */
class RP_Kundenbewertung_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		// create database table for the answers
		$table_name = $wpdb->prefix . 'rp_kundenbewertung';
     	$sql =
        	"CREATE TABLE {$table_name} (
        	ID mediumint(9) NOT NULL AUTO_INCREMENT,
        		frage text NOT NULL,
        		art mediumint(1) NOT NULL,
        		antwort text NOT NULL,
        		datum datetime NOT NULL,
        		sessionID varchar(50) NOT NULL,
        	PRIMARY KEY  (ID)
        	)
        	ENGINE = InnoDB {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
     	dbDelta( $sql );
	}

}
