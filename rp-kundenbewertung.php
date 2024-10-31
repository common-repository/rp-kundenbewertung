<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.rp-datentechnik.com
 * @since             1.0.0
 * @package           RP_Kundenbewertung
 *
 * @wordpress-plugin
 * Plugin Name:       RP-Kundenbewertung
 * Plugin URI:        https://www.kundenbewertung.online/wp-plugin.php
 * Description:       Eine Kundenzufriedenheitsbefragung erstellen und im Frontend integrieren
 * Version:           1.0.0
 * Author:            RP-Datentechnik Hamburg oHG
 * Author URI:        https://www.rp-datentechnik.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
*/
define( 'RP_KUNDENBEWERTUNG_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
*/
function activate_rp_kundenbewertung() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rp-kundenbewertung-activator.php';
	RP_Kundenbewertung_Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
*/
function deactivate_rp_kundenbewertung() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rp-kundenbewertung-deactivator.php';
	RP_Kundenbewertung_Deactivator::deactivate();

}

register_activation_hook( __FILE__, 'activate_rp_kundenbewertung' );
register_deactivation_hook( __FILE__, 'deactivate_rp-kundenbewertung' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
*/
require plugin_dir_path( __FILE__ ) . 'includes/class-rp-kundenbewertung.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
*/
function run_rp_kundenbewertung() {

	$plugin = new RP_Kundenbewertung();
	$plugin->run();

}
run_rp_kundenbewertung();

/**
* Register the stylesheets for the frontend.
 *
 * @since    1.0.0
*/
function rp_kundenbewertung_css() {

	wp_enqueue_style( 'rp-plugin-style', plugin_dir_url( __FILE__ ) . '/assets/css/rp-kundenbewertung.css' );

}

add_action( 'wp_enqueue_scripts', 'rp_kundenbewertung_css' );

/**
 * Show in Frontend
 *
 * @since    1.0.0
*/
function rp_kundenbewertung_front() {

	global $wpdb;

	// Load Questions
	$results = $wpdb->get_results("SELECT option_value FROM " . $wpdb->prefix . "options WHERE option_name = 'rp_kundenbewertung_settings_questions'");

	foreach($results as $values)
    {
      	$data = $values->option_value;
      	$array = unserialize($data);
    }

	// Show Thank you link?
    $banner_anzeigen = $array["banner_1"];
    $banner_anzeigen = substr($banner_anzeigen, 0, 1);

    $content .= '<div class="rp_fragebogen_formular">';

	// Check if answer is given
	$antwort_vorhanden = 0;

	for ($av = 1; $av <= 10; $av++) {

		if(isset($_POST['antwort_' . $av])) {
			$antwort_vorhanden = 1;
		}

	}

	// Get the Captcha from user
	$captcha_code = sanitize_text_field($_POST['captcha']);
	$web = sanitize_text_field($_POST['web']);
	$absenden_ok = 0;

	// Message - Success
	if (1 == $antwort_vorhanden && is_numeric($captcha_code) && 2 == strlen($captcha_code) && trim($web) == "") {

	    $content .= '<div class="rp_success_box">';
        $content .= 'Vielen Dank f&uuml;r die Teilnahme';
        $content .= '</div>';

        $absenden_ok = 1;

	}

	// Message - Captcha Error
	if (1 == $antwort_vorhanden && !is_numeric($captcha_code) || 1 == $antwort_vorhanden && 2 != strlen($captcha_code) || 1 == $antwort_vorhanden && trim($web) != "") {

		$content .= '<div class="rp_alert_box">';
        $content .= 'Die Sicherheitsabfrage ist fehlerhaft. Bitte erneut versuchen.';
        $content .= '</div>';

	}

	// Show Formular
    $content .= '<form action="" method="POST">';

	for ($i = 1; $i <= 10; $i++) {

		$frage 		= $array["qst_" . $i];
		$frage_type	= $array["qst_" . $i . "_type"];

		if (trim($frage) != "") {

			$content .= '<div class="rp_fragebogen">';
			$content .= '<b>' . esc_html($frage) . '</b><br />';
			$content .= '<input type="hidden" name="frage_' . $i . '" value="' . esc_html($frage) . '">';
			$content .= '<input type="hidden" name="art_' . $i . '" value="' . esc_html($frage_type) . '">';

			// Get answer on error
			if ( 1 != $absenden_ok ) {

				$user_antwort = sanitize_text_field($_POST['antwort_' . $i]);

			}

			// Free text answer
			if ($frage_type == 1) {

				$content .= '<textarea name="antwort_' . esc_attr($i) . '" class="rp_freitext" rows="3">' . esc_textarea($user_antwort) . '</textarea>';

			}

			// Yes / No answer
			if ($frage_type == 2) {

				$content .= '<input type="radio" name="antwort_' . esc_attr($i) . '" class="rp_radio" value="ja"';
				if ( 'ja' == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> Ja';

				$content .= '<br />';

				$content .= '<input type="radio" name="antwort_' . esc_attr($i) . '" class="rp_radio" value="nein"';
				if ( 'nein' == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> Nein';

			}

			// Rating
			if ($frage_type == 3) {

				$content .= '<div class="rp_skala_div">';
				$content .= '<span class="rp_skala_all">';

				$content .= '<span class="rp_skala_1"><input type="radio" name="antwort_' . esc_attr($i) . '" value="1"';
				if ( 1 == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> 1</span>';

				$content .= '<span class="rp_skala_2"><input type="radio" name="antwort_' . esc_attr($i) . '" value="2"';
				if ( 2 == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> 2</span>';

				$content .= '<span class="rp_skala_3"><input type="radio" name="antwort_' . esc_attr($i) . '" value="3"';
				if ( 3 == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> 3</span>';

				$content .= '<span class="rp_skala_4"><input type="radio" name="antwort_' . esc_attr($i) . '" value="4"';
				if ( 4 == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> 4</span>';

				$content .= '<span class="rp_skala_5"><input type="radio" name="antwort_' . esc_attr($i) . '" value="5"';
				if ( 5 == $user_antwort ) {
					$content .= ' checked';
				}
				$content .= '> 5</span>';

				$content .= '</span>';
				$content .= '</div>';

			}
			$content .= '</div>';
		}
	}

	// Captcha & Honeypot
	$content .= '<div class="rp_fragebogen_no">';
	$content .= '<b>Feld bitte frei lassen</b><br />';
	$content .= '<input type="text" name="web" class="rp_freitext" autocomplete="off">';
	$content .= '</div>';

	$content .= '<div class="rp_fragebogen">';
	$content .= '<b>Bitte eine beliebige zweistellige Zahl in das Textfeld eintragen</b><br />';
	$content .= '<input type="text" name="captcha" class="rp_captcha_txt">';
	$content .= '</div>';


	// Submit
	$content .= '<input type="hidden" name="action" value="rp_kundenbewertung_user_save">';
	$content .= '<input type="submit" value="Absenden" name="form_sub" />';
	$content .= '</form>';
	$content .= '</div>';

	// Show thank you link if selected in Backend
	if ( 1 == $banner_anzeigen ){

		$content .= '<div class="danke_kbo">Ein Service von <a href="https://www.kundenbewertung.online" target="_blank">www.kundenbewertung.online</a></div>';

	}

	// Save answer if exisiting
	if( 1 == $antwort_vorhanden ) {

		global $wpdb;
		$date = new DateTimeImmutable();

		// only save with correct captcha
		if (is_numeric($captcha_code) && 2 == strlen($captcha_code) && trim($web) == "") {

		    // get user entries
            for ($i = 1; $i <= 10; $i++)
            {
                $antwort    = sanitize_text_field($_POST['antwort_' . $i]);
                $frage      = sanitize_text_field($_POST['frage_' . $i]);
                $art        = sanitize_text_field($_POST['art_' . $i]);
                $art        = substr($art, 0, 1);
                $session    = substr(md5(time()), 0, 40);
                $datum      = date("Y-m-d H:i:s",$date->getTimestamp());

                // save in Database
                if (trim($antwort) != "")
                {
                	$results = $wpdb->get_results("INSERT INTO " . $wpdb->prefix . "rp_kundenbewertung (frage, art, antwort, datum, sessionID) values ('" . esc_sql($frage) . "', " . esc_sql($art) . ", '" . esc_sql($antwort) . "', '" . esc_sql($datum) . "', '" . esc_sql($session) . "')");
                }
            }
        }
	}

	// Return data
	return $content;

}

// add Shortcode
add_shortcode( 'rp_kundenbewertung', 'rp_kundenbewertung_front' );
