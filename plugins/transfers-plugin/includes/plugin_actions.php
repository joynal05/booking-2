<?php

class Transfers_Plugin_Actions extends Transfers_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();
		
    }

    public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );
	}	
	
	/**
	 * Enqueues scripts and styles for front-end.
	 *
	 * @since Transfers 1.0
	 */
	function enqueue_scripts_styles() {
	
		global $wp_styles;
		global $transfers_plugin_globals;
		$language_code = transfers_get_current_language_code();
	
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-button');
		wp_enqueue_script('jquery-ui-datepicker');
		
		wp_enqueue_script( 'transfers-jquery-validate', TRANSFERS_PLUGIN_URI . 'js/jquery.validate.min.js', array('jquery'), '1.0', true );
		
		if ($language_code != "en" && transfers_does_file_exist('/js/i18n/datepicker-' . $language_code . '.js')) {
			wp_register_script(	'transfers-datepicker-' . $language_code, TRANSFERS_PLUGIN_URI . 'js/i18n/datepicker-' . $language_code . '.js', array('jquery', 'jquery-ui-datepicker'), '1.0',true);
			wp_enqueue_script( 'transfers-datepicker-' . $language_code );
		}
		
		wp_enqueue_script( 'transfers-jquery-slider-access', TRANSFERS_PLUGIN_URI . 'js/jquery-ui-sliderAccess.js', array('jquery', 'jquery-ui-button'), '1.0', true );
		
		$ajaxurl = admin_url( 'admin-ajax.php' );
	
		global $sitepress;
		if ($sitepress) {
			$lang = $sitepress->get_current_language();
			$ajaxurl = admin_url( 'admin-ajax.php?lang=' . $lang );
		}
		
		wp_localize_script( 'transfers-scripts', 'TransfersAjax', array( 
		   'ajaxurl' => $ajaxurl,
		   'nonce'   => wp_create_nonce('transfers-ajax-nonce') 
		) );
		
		if (is_page()) {
		
			$page_id     = get_queried_object_id();		
		
			$template_file = get_post_meta($page_id,'_wp_page_template',true);
			
			if ($template_file == 'page-booking-form.php') {
				wp_register_script(	'transfers-booking', TRANSFERS_PLUGIN_URI . 'js/booking.js', array('jquery', 'transfers-jquery-validate'), '1.0',true);
				wp_enqueue_script( 'transfers-booking' );
			}
		}	

		do_action('transfers_plugin_enqueue_scripts_styles');
	}
	
	function enqueue_admin_scripts_styles() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-button');
		wp_enqueue_script('jquery-ui-datepicker');
	
		wp_register_script(	'transfers-timepicker', TRANSFERS_PLUGIN_URI . '/js/jquery-ui-timepicker-addon.js', array('jquery', 'jquery-ui-datepicker'), '1.0',true);
		wp_enqueue_script( 'transfers-timepicker' );	
		
		$language_code = transfers_get_current_language_code();		
		
		if ($language_code != "en" && transfers_does_file_exist('/js/i18n/jquery-ui-timepicker-' . $language_code . '.js')) {
			wp_register_script(	'transfers-timepicker-' . $language_code, TRANSFERS_PLUGIN_URI . 'js/i18n/jquery-ui-timepicker-' . $language_code . '.js', array('jquery', 'transfers-timepicker'), '1.0',true);
			wp_enqueue_script( 'transfers-timepicker-' . $language_code );
		}		
		
		wp_enqueue_script( 'transfers-jquery-slider-access', TRANSFERS_PLUGIN_URI . 'js/jquery-ui-sliderAccess.js', array('jquery', 'jquery-ui-button'), '1.0', true );		
	
		wp_register_script(	'transfers-admin', TRANSFERS_PLUGIN_URI . 'includes/admin/admin.js', array('jquery'), '1.0',true);
		wp_enqueue_script( 'transfers-admin' );
		
		do_action('transfers_plugin_enqueue_admin_scripts_styles');
	}
}

// store the instance in a variable to be retrieved later and call init
$transfers_plugin_actions = Transfers_Plugin_Actions::get_instance();
$transfers_plugin_actions->init();