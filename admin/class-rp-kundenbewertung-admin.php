<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.rp-datentechnik.com
 * @since      1.0.0
 *
 * @package    RP_Kundenbewertung
 * @subpackage rp-kundenbewertung/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    RP_Kundenbewertung
 * @subpackage rp-kundenbewertung/admin
 * @author     RP-Datentechnik Hamburg oHG <kontakt@rp-datentechnik.com>
*/
class RP_Kundenbewertung_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	*/
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rp-kundenbewertung-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	*/
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rp-kundenbewertung-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
 	 * Register the settings page in admin area.
 	 *
 	 * @since    1.0.0
 	*/
	public function add_settings_page(){

  		add_plugins_page(
    	'RP-Kundenbewertung Settings',
    	'RP-Kundenbewertung',
    	'manage_options',
    	$this->plugin_name,
    	array( $this, 'display_settings_page' ) );

	}

	/**
 	 * Display the settings page in admin area.
 	 *
 	 * @since    1.0.0
 	*/
	public function display_settings_page(){

		global $wpdb;

		$tab_save 		= sanitize_text_field($_POST['tab']);
		$valueID		= sanitize_text_field($_POST['valueID']);
		$richtung		= sanitize_text_field($_POST['richtung']);
		$antwort_sid	= sanitize_text_field($_POST['antwort_sid']);

		// Limit value for the answerlist
		if (trim($valueID) == "" || !is_numeric($valueID)) {

			$valueID = 0;

		}
		else
		{
			if (1 == $richtung) {

				$valueID += 25;

			}

			if (0 == $richtung && $valueID > 1) {

				$valueID -= 25;

			}
		}

		// delete answer from the database table
		if ($antwort_sid) {

			$results_del = $wpdb->delete($wpdb->prefix . 'rp_kundenbewertung', ['sessionID' => esc_sql($antwort_sid)]);

		}

  	?>
  		<div class='rp_admin_div'>
  			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php settings_errors(); ?>
            <br /><br />

			<div class="tabbed">
            	<input checked="checked" id="tab1" type="radio" name="tabs" />
                <input id="tab2" type="radio" name="tabs" />

                <nav>
                	<label for="tab1">Fragebogen</label>
                    <label for="tab2">Antworten</label>
                </nav>

                <figure>
                	<?php
				        /*
                        *
                        * Tab 1 - create questionnaire
                        *
                        */
                    ?>
                	<div class="tab1" id="tab1">
                        Legen Sie hier Ihren Fragebogen an.<br />
                        Der Shortcode zum Einbinden im Frontend lautet: [rp_kundenbewertung]
                        <br /><br />

                        <form action="options.php" method="post">
                        <?php
                            settings_fields( 'rp_kundenbewertung_settings_group' );
                            do_settings_sections( 'rp_kundenbewertung_settings_group' );

                            submit_button( 'Fragen speichern' );
                        ?>
                        </form>
                    </div>

					<?php
				        /*
                        *
                        * Tab 2 - show Answers
                        *
                        */
                    ?>
                    <div class="tab2" id="tab2">
                    	Hier sehen die eingegangenen Antworten
                        <br /><br />
                        <h2>Antworten</h2>
                        <?php

                        	// Message - answer deleted
                        	if ($antwort_sid && $results_del > 0) {

                        		echo '<div class="rp_success_box">';
                                echo 'Der Datensatz wurde erfolgreich entfernt';
                                echo '</div>';

                        	}

                        	// load data and show
                        	$zeile = 1;
                        	$frage_anz = 1;

							$results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'rp_kundenbewertung group by sessionID, datum order by ID DESC LIMIT ' . esc_sql($valueID) . ', 25');

							foreach($results as $values)
                            {
                            	$data_sid	= $values->sessionID;
                                $data_dat	= $values->datum;
                                $data_dat	= substr($data_dat, 0, 16);

                                echo '<details style="border:1px solid #505050;">';
                                if ($zeile%2) {
                                   echo '<summary style="background-color:#D2D2D2; padding:5px; cursor:pointer;">';
                                }
                                else {
                                	echo '<summary style="background-color:#F2F2F2; padding:5px; cursor:pointer;">';
                                }
                                echo esc_html($data_dat) . ' Uhr</summary>';

                                $results2 = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'rp_kundenbewertung where sessionID = "' . esc_sql($data_sid) . '" order by ID');

							    foreach($results2 as $values2)
                                {
                                    $data_frage = $values2->frage;
                                    $data_art   = $values2->art;
                                    $data_antw  = $values2->antwort;

                                    echo '<p style="padding:10px;">';
                                    echo '<b>' . esc_html($data_frage) . '</b>';

									// free text and yes/no answer
                                    if ($data_art == 1 || $data_art == 2) {

										echo '<ul style="padding-left:30px; margin-top:0 !important; list-style-type:disc;">';
				                    	echo '<li>' . esc_html($data_antw) . '</li>';
				                    	echo '</ul>';

                                    }

									// Rating
                                    if ($data_art == 3) {

										echo '<br /><br />';
                                        echo '<span class="rp_skala_all" style="margin-top:20px;">';

                                        echo '<span class="rp_skala_1"><input type="radio" name="antwort_' . esc_attr($frage_anz) . '" disabled="disabled"';
                                        if ($data_antw == 1) {
                                        	echo ' checked="checked"';
                                        }
                                        echo '> 1</span>';

                                        echo '<span class="rp_skala_2"><input type="radio" name="antwort_' . esc_attr($frage_anz) . '" disabled="disabled"';
                                        if ($data_antw == 2) {
                                        	echo ' checked="checked"';
                                        }
                                        echo '> 2</span>';

                                        echo '<span class="rp_skala_3"><input type="radio" name="antwort_' . esc_attr($frage_anz) . '" disabled="disabled"';
                                        if ($data_antw == 3) {
                                        	echo ' checked="checked"';
                                        }
                                        echo '> 3</span>';

                                        echo '<span class="rp_skala_4"><input type="radio" name="antwort_' . esc_attr($frage_anz) . '" disabled="disabled"';
                                        if ($data_antw == 4) {
                                        	echo ' checked="checked"';
                                        }
                                        echo '> 4</span>';

                                        echo '<span class="rp_skala_5"><input type="radio" name="antwort_' . esc_attr($frage_anz) . '" disabled="disabled"';
                                        if ($data_antw == 5) {
                                        	echo ' checked="checked"';
                                        }
                                        echo '> 5</span>';

                                        echo '</span>';

                                    }

                                    echo '</p>';

                                    $frage_anz++;

                                }

								// Button delete answer
                                echo '<div style="width:98%; text-align:right; padding-bottom:20px;">';
                                echo '<form id="formDelete_' . esc_attr($data_sid) . '" action="plugins.php?page=rp-kundenbewertung" method="POST">';
                                echo '<input type="hidden" name="antwort_sid" value="' . esc_attr($data_sid) . '">';
                                echo '<input type="hidden" name="tab" value="1">';
                                echo '<input type="button" name="loeschen" value="entfernen" class="button-primary" onClick="checkDelete(\'' . esc_attr($data_sid) . '\')">';
                                echo '</form>';
                                echo '</div>';

								$zeile++;
  								echo '</details>';
                            }

							// no answers available
                            if ($zeile == 1) {

                            	echo 'Es sind keine daten vorhanden';

                            }

                            echo '<br /><br />';

							// button newer ansers
							if ($valueID > 0) {

                                echo '<form action="plugins.php?page=rp-kundenbewertung" method="POST">';
                                echo '<input type="hidden" name="tab" value="1">';
                                echo '<input type="hidden" name="valueID" value="' . esc_attr($valueID) . '">';
                                echo '<input type="hidden" name="richtung" value="0">';
                                echo '<input type="submit" name="zurueck" value="&laquo; Neuer" class="button-primary" style="float:left; margin-right:10px;">';
                                echo '</form>';
							}

							// button later answers
                            if ($zeile != 1) {

                                echo '<form action="plugins.php?page=rp-kundenbewertung" method="POST">';
                                echo '<input type="hidden" name="tab" value="1">';
                                echo '<input type="hidden" name="valueID" value="' . esc_attr($valueID) . '">';
                                echo '<input type="hidden" name="richtung" value="1">';
                                echo '<input type="submit" name="weiter" value="&Auml;lter &raquo;" class="button-primary" style="margin-right:10px;">';
                                echo '</form>';

							}

							echo '<div style="clear:both;"></div>';

							// open answer tab
                            if ($tab_save == 1) {

                            	echo '<script>document.getElementById("tab2").click();</script>';

                            }
                        ?>
                    </div>
                </figure>
            </div>
  		</div>
  	<?php

	}

	/**
     * Register all the settings, fields and sections.
     *
     * @since    1.0.0
    */
	public function add_settings(){

    	// Create the option first if not done already.
    	add_option( 'rp_kundenbewertung_settings_group' );

    	add_settings_section(
      		'rp_kundenbewertung_posttype_section',
      		'Fragebogen',
      		array( $this, 'render_settings_section' ),
      		'rp_kundenbewertung_settings_group'
    	);

    	// register all sections and fields in WP
    	register_setting(
      	    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_settings_questions',
    	);

    	// add Checkbox show Link
		add_settings_field(
			'banner_1',
  			'Danke Link anzeigen',
  			array( $this, 'render_checkbox_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'banner_1'
  			)
		);

    	// add Question 1
		add_settings_field(
			'qst_1',
  			'Frage 1',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_1'
  			)
		);

		// add Question Type 1
		add_settings_field(
			'qst_1_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_1_type'
			)
		);

		// add Questing 2
		add_settings_field(
			'qst_2',
  			'Frage 2',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_2'
  			)
		);

		// add Question Type 2
		add_settings_field(
			'qst_2_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_2_type'
			)
		);

		// add Questing 3
		add_settings_field(
			'qst_3',
  			'Frage 3',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_3'
  			)
		);

		// add Question Type 3
		add_settings_field(
			'qst_3_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_3_type'
			)
		);

		// add Questing 4
		add_settings_field(
			'qst_4',
  			'Frage 4',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_4'
  			)
		);

		// add Question Type 4
		add_settings_field(
			'qst_4_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_4_type'
			)
		);

		// add Questing 5
		add_settings_field(
			'qst_5',
  			'Frage 5',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_5'
  			)
		);

		// add Question Type 5
		add_settings_field(
			'qst_5_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_5_type'
			)
		);

		// add Questing 6
		add_settings_field(
			'qst_6',
  			'Frage 6',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_6'
  			)
		);

		// add Question Type 6
		add_settings_field(
			'qst_6_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_6_type'
			)
		);

		// add Questing 7
		add_settings_field(
			'qst_7',
  			'Frage 7',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_7'
  			)
		);

		// add Question Type 7
		add_settings_field(
			'qst_7_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_7_type'
			)
		);

		// add Questing 8
		add_settings_field(
			'qst_8',
  			'Frage 8',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_8'
  			)
		);

		// add Question Type 8
		add_settings_field(
			'qst_8_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_8_type'
			)
		);

		// add Questing 9
		add_settings_field(
			'qst_9',
  			'Frage 9',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_9'
  			)
		);

		// add Question Type 9
		add_settings_field(
			'qst_9_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_9_type'
			)
		);

		// add Questing 10
		add_settings_field(
			'qst_10',
  			'Frage 10',
  			array( $this, 'render_text_input' ),
  		    'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
  			array(
    			'option'    => 'qst_10'
  			)
		);

		// add Question Type 10
		add_settings_field(
			'qst_10_type',
			'Art der Frage',
			array( $this, 'render_select_input' ),
			'rp_kundenbewertung_settings_group',
            'rp_kundenbewertung_posttype_section',
			array(
				'option'    => 'qst_10_type'
			)
		);
	}

	/**
	 * Renders additional content for settings section.
	 *
	 * @since    1.0.0
	*/
	public function render_settings_section() {

		//echo additional content between section header and content

	}

	/**
     * Renders input for a text field.
     *
     * This uses the parameter exposed to the callback of add_settings_field to get the option name.
     *
     * @since    1.0.0
    */
	public function render_text_input( $args ) {

  		$options = get_option( 'rp_kundenbewertung_settings_questions' );
  		$value = ( isset( $options[ $args['option'] ] )? $options[ $args['option'] ] : '' );

  		$html = '<input type="text" class="rp_txt_admin" id=rp_kundenbewertung_settings_questions['. esc_attr($args['option']) .']" name="rp_kundenbewertung_settings_questions['. esc_attr($args['option']) .']" value="'. esc_attr($value) .'"/>';

  		echo $html;

	}

	/**
     * Renders select.
     *
     * This uses the parameter exposed to the callback of add_settings_field to get the option name.
     *
     * @since    1.0.0
    */
	public function render_select_input( $args ) {

  		$options = get_option( 'rp_kundenbewertung_settings_questions' );
  		$value = ( isset( $options[ $args['option'] ] )? $options[ $args['option'] ] : '' );

  		$html = '<select id=rp_kundenbewertung_settings_questions['. esc_attr($args['option']) .']" name="rp_kundenbewertung_settings_questions['. esc_attr($args['option']) .']" class="rp_txt_admin_sel">';
  		$html .= '<option value="1" ';
  		if ($value == 1) {
  			$html .= 'selected="selected"';
  		}
  		$html .= '>Freitext Antwort</option>';
  		$html .= '<option value="2" ';
  		if ($value == 2) {
  			$html .= 'selected="selected"';
  		}
  		$html .= '>Ja/Nein Antwort</option>';
  		$html .= '<option value="3" ';
  		if ($value == 3) {
  			$html .= 'selected="selected"';
  		}
  		$html .= '>Bewertungsskala</option>';
  		$html .= '</select>';

  		echo $html;

	}

	/**
	 * Renders input for a checkbox field.
	 *
	 * This uses the parameter exposed to the callback of add_settings_field to get the option name.
	 *
	 * @since    1.0.0
	*/
	public function render_checkbox_input( $args ) {

		$options = get_option( 'rp_kundenbewertung_settings_questions' );
		$value = ( isset( $options[ $args['option'] ] )? $options[ $args['option'] ] : '0' );
		$html = '<input type="checkbox" id="rp_kundenbewertung_settings_questions['. esc_attr($args['option']) .']" name="rp_kundenbewertung_settings_questions['. esc_attr($args['option']) .']" value="1" '.checked(1, esc_attr($value), false).' class="rp_txt_admin_cb">';

		echo $html;
	}
}
