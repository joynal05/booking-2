<?php

class Transfers_Plugin_Ajax extends Transfers_BaseSingleton {
	
	protected function __construct() {
	
	    // our parent class might contain shared code in its constructor
        parent::__construct();
    }
	
    public function init() {
	
		add_action( 'wp_ajax_number_format_i18n_request', array( $this, 'number_format_i18n_request' ) );		
		add_action( 'wp_ajax_nopriv_number_format_i18n_request', array( $this, 'number_format_i18n_request' ) );		
		add_action( 'wp_ajax_book_transfer_ajax_request', array( $this, 'book_transfer_ajax_request') );
		add_action( 'wp_ajax_nopriv_book_transfer_ajax_request', array( $this, 'book_transfer_ajax_request') );
		
		add_action( 'wp_ajax_transfers_extra_tables_ajax_request', array( $this, 'transfers_extra_tables_ajax_request' ) );		
		add_action( 'wp_ajax_nopriv_transfers_extra_tables_ajax_request', array( $this, 'transfers_extra_tables_ajax_request' ) );		
		add_action( 'wp_ajax_generate_unique_dynamic_element_id', array( $this, 'generate_unique_dynamic_element_id' ) );		
	}

	function generate_unique_dynamic_element_id() {
		
		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if ( wp_verify_nonce( $nonce, 'optionsframework-options' ) ) {

				global $transfers_plugin_globals;
				
				$element_type = sanitize_text_field($_REQUEST['element_type']);
				$parent = sanitize_text_field($_REQUEST['parent']);
				$element_id = trim(sanitize_text_field($_REQUEST['element_id']));
				
				if (empty($element_id) && $element_type == 'field') {
					$element_id = 'f';
				} else if (empty($element_id) && $element_type == 'booking_form_field') {
					$element_id = 'bff';
				}
				
				$elements = null;
				if ($parent == 'destination_extra_fields') {
					$elements = $transfers_plugin_globals->get_destination_extra_fields();
				} else if ($parent == 'booking_form_fields') {
					$elements = $transfers_plugin_globals->get_booking_form_fields();
				}

				$exists_count = 1;
				$new_element_id = $element_id;
				$exists = Transfers_Plugin_Of_Custom::of_element_exists($elements, $element_id);
				if ($exists) {
					while ($exists) {
						$new_element_id = $element_id . '_' . $exists_count;
						$exists = Transfers_Plugin_Of_Custom::of_element_exists($elements, $new_element_id);
						$exists_count++;
					}
				}
				
				echo json_encode($new_element_id);
			}
		}
		
		die();		
	}
	
	function transfers_extra_tables_ajax_request() {
	
		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'optionsframework-options' ) ) {

				global $transfers_plugin_post_types;
				$transfers_plugin_post_types->create_extra_tables(true);	
				echo "1";
			} else {
				echo "00";
			}
			
		} else {
			echo "-1";
		}
		
		die();
	}

	function book_transfer_ajax_request() {

		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'transfers-ajax-nonce' ) ) {

				global $transfers_plugin_globals, $transfers_plugin_post_types;
				
				$enc_key = $transfers_plugin_globals->get_enc_key();
				$add_captcha_to_forms = $transfers_plugin_globals->add_captcha_to_forms();
				
				$c_val_s = 0;
				$c_val_1 = 0;
				$c_val_2 = 0;
				
				if ($add_captcha_to_forms) {
				
					$c_val_s = intval(wp_kses($_REQUEST['cValS'], ''));
					$c_val_1_str = transfers_decrypt(wp_kses($_REQUEST['cVal1'], ''), $enc_key);
					$c_val_2_str = transfers_decrypt(wp_kses($_REQUEST['cVal2'], ''), $enc_key);
					$c_val_1 = intval($c_val_1_str);
					$c_val_2 = intval($c_val_2_str);
				}

				if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {
					
					echo 'captcha_error';
					
				} else {
				
					$booking_object 				= $transfers_plugin_post_types->retrieve_booking_object_from_request();
					
					if (isset($booking_object) && isset($booking_object->departure_booking_args) && isset($booking_object->departure_booking_args['availability_id'])) {
						
						$people_count 					= $booking_object->departure_booking_args['people_count'];
						$departure_date 				= $booking_object->departure_booking_args['booking_datetime'];
						$departure_is_private			= $booking_object->departure_booking_args['is_private'];
						$departure_slot_minutes 		= $booking_object->departure_slot_minutes;				
						$departure_destination_from		= $booking_object->departure_destination_from;
						$departure_destination_to		= $booking_object->departure_destination_to;
						$departure_transport_type		= $booking_object->departure_transport_type;
						$both_legs_price 				= $booking_object->departure_booking_args['total_price'];
						$departure_extra_items_string	= $booking_object->departure_extra_items_string;

						$departure_booking_id 			= $transfers_plugin_post_types->create_booking_entry($booking_object->departure_booking_args);

						$return_booking_id 				= 0;
						$return_availability 			= null;
						$return_slot_minutes 			= '';
						$return_destination_from 		= '';
						$return_destination_to 			= '';
						$return_transport_type 			= '';
						$return_is_private 				= false;
						$return_extra_items_string		= '';
						$return_date 					= null;
						
						if (isset($booking_object->return_booking_args) && $booking_object->return_booking_args != null) {
						
							$return_booking_id 			= $transfers_plugin_post_types->create_booking_entry($booking_object->return_booking_args);
							$both_legs_price 			+= $booking_object->return_booking_args['total_price'];
							$return_slot_minutes 		= $booking_object->return_slot_minutes;
							$return_is_private			= $booking_object->return_booking_args['is_private'];
							$return_extra_items_string	= $booking_object->return_extra_items_string;
							$return_date				= $booking_object->return_booking_args['booking_datetime'];
							$return_destination_from	= $booking_object->return_destination_from;
							$return_destination_to		= $booking_object->return_destination_to;
							$return_transport_type		= $booking_object->return_transport_type;
						}

						$admin_email 					= get_bloginfo('admin_email');
						$admin_name 					= get_bloginfo('name');
						$subject 						= esc_html__('New transfer booking', 'transfers');
						
						$price_decimal_places 			= $transfers_plugin_globals->get_price_decimal_places();
						$default_currency_symbol 		= $transfers_plugin_globals->get_default_currency_symbol();
						$show_currency_symbol_after 	= $transfers_plugin_globals->show_currency_symbol_after();
						
						$formatted_both_legs_price 		= number_format_i18n( $both_legs_price, $price_decimal_places );
						
						if ($show_currency_symbol_after) {
						
							$formatted_both_legs_price 	= $formatted_both_legs_price . ' ' . $default_currency_symbol;
						} else {
							$formatted_both_legs_price 	= $default_currency_symbol . ' ' . $formatted_both_legs_price;
						}
						
						$message = esc_html__('New transfer booking:', 'transfers');
						$message .= "\n\n";

						$booking_form_fields = $transfers_plugin_globals->get_booking_form_fields();								
						$customer_email = '';								
						foreach ($booking_form_fields as $form_field) {
							
							$field_id = $form_field['id'];
							
							if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) { 

								$field_value = sanitize_text_field($_REQUEST[$field_id]);
								if ($field_id == 'email') {
									$customer_email = $field_value;
								}
								$field_label = $form_field['label'];
								
								$message .= sprintf($field_label . ": %s\n\n",  $field_value);
							}
						}						
						
						if ($booking_object->return_booking_args['availability_id'] > 0) {
							$message .= esc_html__("People: %d \n\nDeparture Date: %s \n\nDeparture Private? %s \n\nDeparture From: %s \n\nDeparture To: %s \n\nDeparture Transport type: %s \n\nDeparture Extra items: %s \n\nReturn Date: %s \n\nReturn Private? %s \n\nReturn From: %s\n\nReturn To: %s \n\nReturn Transport Type: %s \n\nReturn Extra items: %s \n\nTotal Price: %s", 'transfers');
							$message = sprintf($message, $people_count, $departure_date, ($departure_is_private ? esc_html__('Yes', 'transfers') : esc_html__('No', 'transfers')), $departure_destination_from, $departure_destination_to, $departure_transport_type, $departure_extra_items_string, $return_date, ($return_is_private ? esc_html__('Yes', 'transfers') : esc_html__('No', 'transfers')), $return_destination_from, $return_destination_to, $return_transport_type, $return_extra_items_string, $formatted_both_legs_price);
						} else {
							$message .= esc_html__("People: %d \n\nDeparture Date: %s \n\nDeparture Private? %s \n\nDeparture From: %s \n\nDeparture To: %s \n\nDeparture Transport type: %s \n\nDeparture Extra items: %s \n\nTotal Price: %s", 'transfers');
							$message = sprintf($message, $people_count, $departure_date, ($departure_is_private ? esc_html__('Yes', 'transfers') : esc_html__('No', 'transfers')), $departure_destination_from, $departure_destination_to, $departure_transport_type, $departure_extra_items_string, $formatted_both_legs_price);
						}						

						echo esc_html($departure_booking_id);
						
						$emails = array();
						if (isset($customer_email)) {
							$emails[] = $customer_email;
						}
						$emails[] = $admin_email;
						$emails = apply_filters('transfers_book_transfer_emails', $emails);
						
						$headers = "Content-Type: text/plain\r\n";
						$headers .= "From: " . $admin_name . " <" . $admin_email . ">\r\n";					
						$headers .= "Reply-To: " . $admin_name . " <" . $admin_name . ">\r\n";
						
						foreach ($emails as $e) {
							if (!empty($e)) {
								$ret = wp_mail($e, $subject, $message, $headers);	
								if (!$ret) {
									global $phpmailer;
									if (isset($phpmailer) && WP_DEBUG) {
										var_dump($phpmailer->ErrorInfo);
									}
								}							
							}
						}
					}
				}
			}
		}

		die();
	}
	
	function number_format_i18n_request() {
	
		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'transfers-ajax-nonce' ) ) {

				global $transfers_plugin_globals;
				
				$price_decimal_places = $transfers_plugin_globals->get_price_decimal_places();
				
				$number = floatval(wp_kses($_REQUEST['number'], ''));	
				
				echo number_format_i18n( $number, $price_decimal_places );

			}
		}

		// Always die in functions echoing ajax content
		die();
	}
	
}

// store the instance in a variable to be retrieved later and call init
$transfers_plugin_ajax = Transfers_Plugin_Ajax::get_instance();
$transfers_plugin_ajax->init();