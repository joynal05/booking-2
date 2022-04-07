<?php

class Transfers_Plugin_Of_Default_Fields extends Transfers_BaseSingleton {

	protected function __construct() {
    // our parent class might contain shared code in its constructor
    parent::__construct();
  }

  public function init() {

	}

	public static function merge_fields_and_defaults($values, $default_values) {

		if (!is_array( $values ) || count($values) == 0) {

			return $default_values;

		} else {

			foreach ($default_values as $default_field_array) {

				$default_found = false;

				for ($i=0;$i<count($values); $i++) {
					$field_array = $values[$i];

					if (isset($default_field_array['id']) && isset($field_array['id'])) {
						if ($default_field_array['id'] == $field_array['id']) {
							$default_found = true;
							if (isset($default_field_array['desc'])) {
								$values[$i]['desc'] = $default_field_array['desc'];
							}
						}
					}
				}

				if (!$default_found) {
					$values[] = $default_field_array;
				}
			}

			return $values;
		}
	}

	function get_default_form_fields_array($option_id) {

		global 	$default_booking_form_fields;

		$default_values = array();

		if ($option_id == 'booking_form_fields') {
			$default_values = $default_booking_form_fields;
		}

		return $default_values;
	}
}

// store the instance in a variable to be retrieved later and call init
$transfers_plugin_of_default_fields = Transfers_Plugin_Of_Default_Fields::get_instance();
$transfers_plugin_of_default_fields->init();

global $repeatable_field_types;
$repeatable_field_types = array(
	'text' => esc_html__('Text box', 'transfers'),
	'textarea' => esc_html__('Text area', 'transfers'),
	'image' => esc_html__('Image selector', 'transfers')
);

global $form_field_types;
$form_field_types = array(
	'text' => esc_html__('Text', 'transfers'),
	'email' => esc_html__('Email', 'transfers'),
	'textarea' => esc_html__('Text area', 'transfers'),
);

$destination_name_label = esc_html__('Destination name', 'transfers');
global $default_destination_extra_fields;
$default_destination_extra_fields = array(
  array('label' => $destination_name_label, 'id' => 'destination_name', 'type' => 'text', 'hide' => 0, 'required' => 0),
);

$first_name_label = esc_html__('First name', 'transfers');
$last_name_label = esc_html__('Last name', 'transfers');
$email_label = esc_html__('Email', 'transfers');
$phone_label = esc_html__('Phone', 'transfers');
$company_label = esc_html__('Company', 'transfers');
$address_label = esc_html__('Address', 'transfers');
$address_2_label = esc_html__('Address 2', 'transfers');
$city_label = esc_html__('City', 'transfers');
$postcode_label = esc_html__('Zip', 'transfers');
$country_label = esc_html__('Country', 'transfers');
$state_label = esc_html__('State', 'transfers');

global 	$default_booking_form_fields;
$default_booking_form_fields = array(
	array('label' => $first_name_label, 'id' => 'first_name', 'type' => 'text', 'hide' => 0, 'required' => 1),
	array('label' => $last_name_label, 'id' => 'last_name', 'type' => 'text', 'hide' => 0, 'required' => 1),
	array('label' => $company_label, 'id' => 'company', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $email_label, 'id' => 'email', 'type' => 'email', 'hide' => 0, 'required' => 1),
	array('label' => $phone_label, 'id' => 'phone', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $address_label, 'id' => 'address', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $address_2_label, 'id' => 'address_2', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $city_label, 'id' => 'town', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $postcode_label, 'id' => 'zip', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $state_label, 'id' => 'state', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $country_label, 'id' => 'country', 'type' => 'text', 'hide' => 0, 'required' => 0)
);
