<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.rp-datentechnik.com
 * @since      1.0.0
 *
 * @package    RP_Kundenbewertung
*/

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	exit;

}

$option_name 	= 'rp_kundenbewertung_settings_questions';
$option_name2	= 'rp_kundenbewertung_settings_group';

delete_option($option_name);
delete_option($option_name2);

// for site options in Multisite
delete_site_option($option_name);
delete_site_option($option_name2);

// drop the answer-table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rp_kundenbewertung");
