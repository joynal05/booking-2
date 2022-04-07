<?php

class Transfers_Theme_Ajax extends Transfers_BaseSingleton {
	
	protected function __construct() {
	
	    // our parent class might contain shared code in its constructor
        parent::__construct();
    }
	
    public function init() {
		add_action( 'wp_ajax_contact_form_ajax_request', array( $this, 'contact_form_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_contact_form_ajax_request', array( $this, 'contact_form_ajax_request' ) );
		add_action( 'wp_ajax_account_ajax_save_general_request', array( $this, 'account_ajax_save_general_request' ) );		
		add_action( 'wp_ajax_nopriv_account_ajax_save_general_request', array( $this, 'account_ajax_save_general_request' ) );		
		add_action( 'wp_ajax_account_ajax_save_security_request', array( $this, 'account_ajax_save_security_request' ) );		
		add_action( 'wp_ajax_nopriv_account_ajax_save_security_request', array( $this, 'account_ajax_save_security_request' ) );
		add_action( 'wp_ajax_account_ajax_save_billing_request', array( $this, 'account_ajax_save_billing_request' ) );		
		add_action( 'wp_ajax_nopriv_account_ajax_save_billing_request', array( $this, 'account_ajax_save_billing_request' ) );		
	}
	
	function account_ajax_save_general_request() {
	
		if ( isset($_REQUEST) ) {
		
			global $transfers_theme_globals;
			
			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'transfers-ajax-nonce' ) ) {
			
				$user_id = intval(wp_kses($_REQUEST['user_id'], ''));	
				$user_login = sanitize_text_field($_REQUEST['user_login']);	
				$user_email = sanitize_text_field($_REQUEST['user_email']);

				$user = get_user_by( 'id', $user_id );
				
				if (isset($user) && $user_login == $user->data->user_login) {

					if ($user_email != $user->data->user_email) {						
						wp_update_user( array ( 'ID' => $user_id, 'user_email' => $user_email ) ) ;						
					}
					
					wp_update_user( array ( 'ID' => $user_id, 'first_name' => sanitize_text_field($_REQUEST['first_name']) ) ) ;
					wp_update_user( array ( 'ID' => $user_id, 'last_name' => sanitize_text_field($_REQUEST['last_name'] ) ) ) ;
					wp_update_user( array ( 'ID' => $user_id, 'user_url' => sanitize_text_field($_REQUEST['user_url'] ) ) ) ;
					wp_update_user( array ( 'ID' => $user_id, 'nickname' => sanitize_text_field($_REQUEST['nickname'] ) ) ) ;
					wp_update_user( array ( 'ID' => $user_id, 'description' => sanitize_text_field($_REQUEST['description'] ) ) ) ;
					
					update_user_meta( $user_id, 'googleplus', sanitize_text_field($_REQUEST['googleplus'] ) );
					update_user_meta( $user_id, 'twitter', sanitize_text_field($_REQUEST['twitter'] ) );
					update_user_meta( $user_id, 'facebook', sanitize_text_field($_REQUEST['facebook'] ) );
					
					echo '1';
				} else {
					echo '0';
				}	
			} else {
				echo '-1';
			}
		} else {
			echo '-2';
		}
			
		// Always die in functions echoing ajax content
		die();
	}
	
	function account_ajax_save_security_request() {
	
		if ( isset($_REQUEST) ) {
		
			global $transfers_theme_globals;
			
			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'transfers-ajax-nonce' ) ) {
			
				$user_id = intval(wp_kses($_REQUEST['user_id'], ''));	
				$user_login = sanitize_text_field($_REQUEST['user_login']);	
				
				$old_password = sanitize_text_field($_REQUEST['old_password']);
				$new_password = sanitize_text_field($_REQUEST['new_password']);
				$confirm_password = sanitize_text_field($_REQUEST['confirm_password']);
			
				$user = get_user_by( 'id', $user_id );
				
				if ($confirm_password == $new_password && isset($user) && $user_login == $user->data->user_login && wp_check_password( $old_password, $user->data->user_pass, $user->ID)) {
					
					wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $new_password ) ) ;
					echo '1';
					
				} else {
					echo '0';
				}	
			} else {
				echo '-1';
			}
		} else {
			echo '-2';
		}
			
		// Always die in functions echoing ajax content
		die();
	}	
	
	function account_ajax_save_billing_request() {
	
		if ( isset($_REQUEST) ) {
		
			global $transfers_theme_globals;
			
			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'transfers-ajax-nonce' ) ) {

				$user_id = intval(wp_kses($_REQUEST['user_id'], ''));	
				$user_login = sanitize_text_field($_REQUEST['user_login']);	
				
				$user = get_user_by( 'id', $user_id );

				if (isset($user) && $user_login == $user->data->user_login) {
				
					update_user_meta( $user_id, 'billing_first_name', sanitize_text_field($_REQUEST['billing_first_name']) );
					update_user_meta( $user_id, 'billing_last_name', sanitize_text_field($_REQUEST['billing_last_name']) );
					update_user_meta( $user_id, 'billing_email', sanitize_text_field($_REQUEST['billing_email']) );
					update_user_meta( $user_id, 'billing_company', sanitize_text_field($_REQUEST['billing_company']) );
					update_user_meta( $user_id, 'billing_phone', sanitize_text_field($_REQUEST['billing_phone']) );
					update_user_meta( $user_id, 'billing_address_1', sanitize_text_field($_REQUEST['billing_address_1']) );
					update_user_meta( $user_id, 'billing_city', sanitize_text_field($_REQUEST['billing_city']) );
					update_user_meta( $user_id, 'billing_postcode', sanitize_text_field($_REQUEST['billing_postcode']) );
					update_user_meta( $user_id, 'billing_state', sanitize_text_field($_REQUEST['billing_state']) );
					update_user_meta( $user_id, 'billing_country', sanitize_text_field($_REQUEST['billing_country']) );
					
					echo '1';
				} else {
					echo '0';
				}	
			} else {
				echo '-1';
			}
		} else {
			echo '-2';
		}
			
		// Always die in functions echoing ajax content
		die();
	}
	
	function contact_form_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			global $transfers_theme_globals;

			$enc_key = $transfers_theme_globals->get_enc_key();
			$add_captcha_to_forms = $transfers_theme_globals->add_captcha_to_forms();
			
			$contact_name = sanitize_text_field($_REQUEST['contact_name']);
			$contact_email = sanitize_text_field($_REQUEST['contact_email']);
			$contact_message = sanitize_text_field($_REQUEST['contact_message']);

			$c_val_s = intval(wp_kses($_REQUEST['c_val_s'], ''));
			$c_val_1_str = transfers_decrypt(wp_kses($_REQUEST['c_val_1'], ''), $enc_key);
			$c_val_2_str = transfers_decrypt(wp_kses($_REQUEST['c_val_2'], ''), $enc_key);
			$c_val_1 = intval($c_val_1_str);
			$c_val_2 = intval($c_val_2_str);
			
			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'transfers-ajax-nonce' ) ) {
			
				if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {
					
					echo 'captcha_error';
					die();
					
				} else {
				
					$admin_email = get_bloginfo('admin_email');
					$site_name = get_bloginfo('name');
					
					$subject = sprintf(__('Contact form submission [%s]', 'transfers'), $site_name);
					$body = sprintf(__("Name: %s\n\nEmail: %s\n\nMessage: %s", 'transfers'), $contact_name, $contact_email, $contact_message);
					
					$headers = "Content-Type: text/html\r\n";
					$headers .= "From: " . $site_name . " <" . $admin_email . ">\r\n";
					if (!empty($contact_name)) {
						$headers .= "Reply-To: " . $contact_name . " <" . $contact_email . ">\r\n";
					}							

					$ret = wp_mail($admin_email, $subject, $body, $headers);
					
					if (!$ret) {
						global $phpmailer;
						if (isset($phpmailer) && WP_DEBUG) {
							var_dump($phpmailer->ErrorInfo);
						}
					}
					
					echo json_encode($ret);
				}	
			} else {
				echo '-1';
			}
		} else {
			echo '-2';
		}
		
		// Always die in functions echoing ajax content
		die();
	}
}

// store the instance in a variable to be retrieved later and call init
$transfers_theme_ajax = Transfers_Theme_Ajax::get_instance();
$transfers_theme_ajax->init();