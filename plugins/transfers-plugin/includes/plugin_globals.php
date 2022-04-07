<?php

class Transfers_Plugin_Globals extends Transfers_BaseSingleton {

	protected function __construct() {
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {
	
    }
	
	public function get_booking_form_fields() {
		
		global $default_booking_form_fields;
		$booking_form_fields = of_get_option('booking_form_fields');
		if (!is_array($booking_form_fields) || count($booking_form_fields) == 0)
			$booking_form_fields = $default_booking_form_fields;
			
		return $booking_form_fields;
	}	
	
	public function enable_shared_transfers() {
		return of_get_option('enable_shared_transfers', true);
	}	
	
	public function get_completed_order_woocommerce_statuses() {
		return of_get_option('completed_order_woocommerce_statuses', '');
	}
	
	public function get_time_slot_increment() {
		$increment = (int)transfers_plugin_of_get_option('time_slot_increment', 5);
		if ($increment == 0)
			return 5;
		return $increment;
	}

	public function get_allowed_search_start_time() {
		return transfers_plugin_of_get_option('allowed_search_start_time', 'now');
	}
	
	public function get_search_results_sort_by() {
		$sortby = (int)of_get_option('search_results_sort_by', 'byminute');
		if (empty($sortby))
			return 'byminute';
		return $sortby;
	}
	
	public function get_search_results_by_minute_count() {
		$search_results_by_minute_count = (int)transfers_plugin_of_get_option('search_results_by_minute_count', 5);
		if ($search_results_by_minute_count == 0)
			return 5;
		return $search_results_by_minute_count;
	}
	
	public function get_price_decimal_places() {
		return (int)transfers_plugin_of_get_option('price_decimal_places', 0);
	}
	
	public function get_search_time_slot_offset() {
		return (int)transfers_plugin_of_get_option('search_time_slot_offset', 0);
	}

	public function get_default_currency_symbol() {
		return transfers_plugin_of_get_option('default_currency_symbol', '$');
	}
	
	public function show_currency_symbol_after() {
		return (int)transfers_plugin_of_get_option('show_currency_symbol_after', 0);
	}

	public function enable_transport_types() {
		return transfers_plugin_of_get_option('enable_transport_types', true);
	}
	
	public function enable_destinations() {
		return transfers_plugin_of_get_option('enable_destinations', true);
	}
	
	public function enable_extra_items() {
		return transfers_plugin_of_get_option('enable_extra_items', true);
	}
	
	public function get_destinations_archive_posts_per_page() {
		return transfers_plugin_of_get_option('destinations_archive_posts_per_page', 12);
	}
	
	public function get_destination_extra_fields() {
		
		global $default_destination_extra_fields;
		$destination_extra_fields = transfers_plugin_of_get_option('destination_extra_fields');
		if (!is_array($destination_extra_fields) || count($destination_extra_fields) == 0)
			$destination_extra_fields = $default_destination_extra_fields;
			
		return $destination_extra_fields;
	}
	
	public function get_booking_form_page_url () {		
		$page_id = transfers_get_current_language_page_id($this->get_booking_form_page_id());
		if ($page_id > 0)
			return get_permalink($page_id);
		else
			return "#";
	}
	
	public function get_destination_list_page_url () {		
		$page_id = transfers_get_current_language_page_id($this->get_destination_list_page_id());
		if ($page_id > 0)
			return get_permalink($page_id);
		else
			return "#";
	}
	
	public function get_advanced_search_page_url () {		
		$page_id = transfers_get_current_language_page_id($this->get_advanced_search_page_id());
		if ($page_id > 0)
			return get_permalink($page_id);
		else
			return "#";
	}

	public function get_advanced_search_page_id() {

		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-advanced-search.php'
		));

		$page = null;
		if (count($pages) > 0) {
			$page = $pages[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}
	
	public function get_booking_form_page_id() {

		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-booking-form.php'
		));

		$page = null;
		if (count($pages) > 0) {
			$page = $pages[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}
	
	public function get_destination_list_page_id() {

		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-destination-list.php'
		));

		$page = null;
		if (count($pages) > 0) {
			$page = $pages[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}
	
	public function get_destinations_permalink_slug() {
		return transfers_plugin_of_get_option('destinations_permalink_slug', 'destinations');
	}
	
	public function enable_faqs() {
		return transfers_plugin_of_get_option('enable_faqs', true);
	}
	
	public function get_faq_list_page_url () {		
		$page_id = transfers_get_current_language_page_id($this->get_faq_list_page_id());
		if ($page_id > 0)
			return get_permalink($page_id);
		else
			return "#";
	}
		
	public function get_faq_list_page_id() {

		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-faq-list.php'
		));

		$page = null;
		if (count($pages) > 0) {
			$page = $pages[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}
	
	public function enable_services() {
		return transfers_plugin_of_get_option('enable_services', true);
	}
	
	public function get_service_list_page_url () {		
		$page_id = transfers_get_current_language_page_id($this->get_service_list_page_id());
		if ($page_id > 0)
			return get_permalink($page_id);
		else
			return "#";
	}
	
	public function get_service_list_page_id() {

		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-service-list.php'
		));

		$page = null;
		if (count($pages) > 0) {
			$page = $pages[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}

	public function add_captcha_to_forms() {
		return (int)transfers_plugin_of_get_option('add_captcha_to_forms', true);
	}
	
	public function get_enc_key() {
		return preg_replace('{/$}', '', $_SERVER['SERVER_NAME']);
	}
	
	public function get_terms_page_url() {
		$terms_page_url_id = transfers_get_current_language_page_id(transfers_plugin_of_get_option('terms_page_url', ''));
		return get_permalink($terms_page_url_id);
	}
	
	public function get_woocommerce_product_placeholder_image() {
		$product_placeholder_image = transfers_plugin_of_get_option( 'woocommerce_product_placeholder_image', '' );
		if (empty($product_placeholder_image)) {
			$product_placeholder_image = trailingslashit(get_stylesheet_directory_uri()) . 'images/uploads/img.jpg';
		}
		return $product_placeholder_image;
	}
	
	public function use_woocommerce_for_checkout() {
	
		$use_woocommerce_for_checkout = 0;
		
		if (transfers_is_woocommerce_active()) {
			$use_woocommerce_for_checkout = (int)transfers_plugin_of_get_option('use_woocommerce_for_checkout', 0);
		}
		
		return $use_woocommerce_for_checkout;
	}
}

global $transfers_plugin_globals;
// store the instance in a variable to be retrieved later and call init
$transfers_plugin_globals = Transfers_Plugin_Globals::get_instance();
$transfers_plugin_globals->init();