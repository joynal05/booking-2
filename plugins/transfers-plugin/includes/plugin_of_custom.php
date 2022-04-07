<?php

class Transfers_Plugin_Of_Custom extends Transfers_BaseSingleton {

	protected function __construct() {

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

		add_filter( 'optionsframework_repeat_extra_field', array( $this, 'repeat_extra_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_form_field', array( $this, 'repeat_form_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_link_button_field', array( $this, 'link_button_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_dummy_text', array( $this, 'dummy_text_option_type' ), 10, 3 );
		add_filter( 'of_sanitize_repeat_extra_field', array( $this, 'sanitize_repeat_extra_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_form_field', array( $this, 'sanitize_repeat_form_field' ), 10, 2 );
		add_action( 'optionsframework_custom_scripts', array( $this, 'of_transfers_options_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_optionsframework_scripts_styles' ) );
	}

	function enqueue_admin_optionsframework_scripts_styles() {
		wp_register_script('transfers-optionsframework-custom', TRANSFERS_PLUGIN_URI . 'includes/admin/optionsframework_custom.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.0.0');
		wp_enqueue_script('transfers-optionsframework-custom');
	}

	public static function of_element_exists($element_array, $element_id) {

		$exists = false;
		foreach ($element_array as $element) {
			if (isset($element['id']) && $element['id'] == $element_id) {
				$exists = true;
				break;
			}
		}
		return $exists;
	}

	public function register_dynamic_string_for_translation($name, $value) {

		if (function_exists('icl_register_string')) {
			icl_register_string('Transfers Theme', $name, $value);
		}
	}

	public function get_translated_dynamic_string($name, $value) {

		if (function_exists('icl_t')) {
			return icl_t('Transfers Theme', $name, $value);
		}
		return $value;
	}

	function repeat_form_field_option_type( $option_name, $option, $values ) {

		global $transfers_plugin_of_default_fields, $form_field_types;

		$max_field_index = -1;

		$counter = 0;

		$default_values = $transfers_plugin_of_default_fields->get_default_form_fields_array($option['id']);

		$values = Transfers_Plugin_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);

		$form_type = '';
		if ($option['id'] == 'booking_form_fields') {
			$form_type = 'booking';
		}

		$used_indices = array();

		$output = '<div class="of-repeat-loop">';
		$output .= '<ul class="sortable of-repeat-form-fields">';

		if ( is_array( $values ) ) {

			foreach ( (array)$values as $key => $value ) {

				if (isset($value['label']) && isset($value['id'])) {

					$field_label 	= $value['label'];
					$field_id		= $value['id'];
					$field_hidden 	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$field_required = isset($value['required']) && $value['required'] == '1' ? true : false;
					$field_index 	= isset($value['index']) ? intval($value['index']) : $counter;
					if (in_array($field_index, $used_indices)) {
						$field_index = $this->find_available_index($field_index, $used_indices);
					}
					$used_indices[] = $field_index;
					$field_type		= $value['type'];

					$is_default = (count(Transfers_Plugin_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);

					$output .= '<li class="ui-state-default of-repeat-group">';

					$output .= '<div class="of-input-wrap">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][label]" class="label-field-label">' . esc_html__('Field name', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field label', 'transfers'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
					$output .= '</div>';
					$output .= '<div class="of-input-wrap of-modify-id">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][id]" class="label-field-id">' . esc_html__('Field ID', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-' . $form_type . '-form-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'transfers'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';
					$output .= '</div>';

					$output .= '<div class="checboxes">';
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'transfers'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'transfers'), ($field_hidden ? 'checked' : ''));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][required]', 'label-field-required', 'checkbox-field-required', esc_html__('Is Required?', 'transfers'), ($field_required ? 'checked' : ''));
					$output .= '</div>';

					$output .= '<div class="field-type-div">';
					$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][type]', 'label-field-type', 'select-field-type', esc_html__('Field type', 'transfers'), $field_type, $form_field_types);
					$output .= '</div>';
					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';

					if (count(Transfers_Plugin_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';

					$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;

					$counter++;
				}
			}
		}

		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
		$output .= '<a href="#" class="docopy_form_field button icon add">' . esc_html__('Add form field', 'transfers') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}

	function repeat_extra_field_option_type( $option_name, $option, $values ){

		global $transfers_plugin_of_default_fields, $repeatable_field_types, $default_destination_extra_fields;

		$default_values = array();

		if ($option['id'] == 'destination_extra_fields') {
			$default_values = $default_destination_extra_fields;
		}

		$values = Transfers_Plugin_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);

		$output = '<div class="of-repeat-loop">';

		$output .= '<ul class="sortable of-repeat-extra-fields  of-repeat-form-fields">';

		$counter = 0;
		$max_field_index = -1;
		$used_indices = array();

		if( is_array( $values ) ) {

			foreach ( (array)$values as $key => $value ){

				if (isset($value['label']) && isset($value['type']) && isset($value['id'])) {

					$field_label 	= $value['label'];
					$field_type 	= $value['type'];
					$field_id 		= $value['id'];
					$field_index 	= isset($value['index']) ? $value['index'] : $counter;
					$field_hidden 	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
					if (in_array($field_index, $used_indices)) {
						$field_index = $this->find_available_index($field_index, $used_indices);
					}
					$used_indices[] = $field_index;
					$is_default = (count(Transfers_Plugin_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);

					$output .= '<li class="ui-state-default of-repeat-group">';
					$output .= '	<div class="of-input-wrap">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][label]" class="label-field-label">' . esc_html__('Field name', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field label', 'transfers'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
					$output .= '	</div>';
					$output .= '<div class="of-input-wrap of-modify-id">';
					$output .= '<label for="' . $option_name . '[' . $option['id'] . ']' . '['.$field_index.'][id]" class="label-field-id">' . esc_html__('Field ID', 'bookyourtravel') . '</label>';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'transfers'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '	</div>';

					$output .= '<div class="checboxes">';
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'transfers'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'transfers'), ($field_hidden ? 'checked' : ''));
					$output .= '	</div>';

					$output .= '<div class="field-type-div">';
					$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][type]', 'label-field-type', 'select-field-type', esc_html__('Field type', 'transfers'), $field_type, $repeatable_field_types);
					$output .= '	</div>';

					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';

					if (count(Transfers_Plugin_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}

					$output .= '</li><!--.of-repeat-group-->';

					$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;

					$counter++;
				}
			}
		}

		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
		$output .= '<a href="#" class="docopy_field button icon add">' . esc_html__('Add field', 'transfers') . '</a>';

		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}

	function find_available_index($current_index, $indexes) {
		if (!in_array($current_index, $indexes)) {
			return $current_index;
		}
		$current_index++;
		return $this->find_available_index($current_index, $indexes);
	}

	function render_dynamic_checkbox($option_name, $option, $name_postfix, $label_css, $input_css, $label_text, $extra_input_attributes = '') {

		$output = '';

		$output .= '<div class="of-check-wrap">';
		$output .= $this->render_dynamic_field_label($option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);
		$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-checkbox ' . $input_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', 'checkbox', '1', '', $extra_input_attributes);
		$output .= '</div>';

		return $output;
	}

	function render_dynamic_field_select( $data_rel, $css_class, $name, $id, $selected_value, $options_array, $text_key = '', $value_key = '' ) {

		$output = '<select class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" data-rel="' . esc_attr( $data_rel ) . '">';

		if (is_array($options_array) && count($options_array)) {

			if (!empty($text_key) && !empty($value_key)) {
				foreach($options_array as $option) {

					$option_text = isset($option[$text_key]) ? trim($option[$text_key]) : '';
					$option_value = isset($option[$value_key]) ? trim($option[$value_key]) : '';

					if (!empty($option_text) && !empty($option_value)) {
						$output .= '<option value="' . $option_value . '" ' . ($option_value == $selected_value ? 'selected' : '') . '>' . $option_text . '</option>';
					}
				}
			} else {
				foreach($options_array as $key => $text) {
					$output .= '<option value="' . $key . '" ' . ($key == $selected_value ? 'selected' : '') . '>' . $text . '</option>';
				}
			}

		}

		$output .= '</select>';

		return $output;
	}

	function render_dynamic_select($option_name, $option, $name_postfix, $label_css, $select_css, $label_text, $selected_value, $option_array, $text_key = '', $value_key = '') {

		$output = '';
		$output .= '<div class="of-select-wrap">';
		$output .= $this->render_dynamic_field_label(  $option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);
		$output .= $this->render_dynamic_field_select( $option_name . '[' . $option['id'] . ']', 'of-select ' . $select_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', $selected_value, $option_array, $text_key, $value_key );
		$output .= '</div>';

		return $output;
	}

	function render_dynamic_field_label( $data_rel, $css_class, $for, $text ) {
		return '<label data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" for="' . esc_attr( $for ) . '">' . $text . '</label>';
	}

	function render_dynamic_field_input( $data_rel, $css_class, $name, $id, $type, $value, $placeholder_text = '', $extra_attributes = '' ) {
		return '<input ' . (!empty($placeholder_text) ? (' placeholder="' . esc_attr($placeholder_text). '"') : '') . ' data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" type="' . esc_attr($type) . '" value="' . esc_attr( $value ) . '" ' . $extra_attributes . ' />';
	}

	function link_button_field_option_type ( $option_name, $option, $values) {

		$button_text = $option['name'];
		if (isset($option['text'])) {
			$button_text = $option['text'];
		}

		$output = '<div class="of-input">';
		$output .= '	<a href="#" class="button-secondary of-button-field ' . $option['id'] . '">' . $button_text . '</a>';
		$output .= '</div>';

		return $output;
	}

	function dummy_text_option_type ( $option_name, $option, $values) {

		$output = '';
		return $output;
	}

	function get_option_id_context($option_id) {

		$option_id_context = '';

		if ($option_id == 'destination_extra_fields')
			$option_id_context = 'Destination extra field';
		elseif ($option_id == 'booking_form_fields')
			$option_id_context = 'Booking form field';

		return $option_id_context;
	}

	/*
	 * Sanitize Repeat inputs
	 */
	function sanitize_repeat_extra_field( $fields, $option ){

		$results = array();
		if (is_array($fields)) {
			foreach ($fields as $field) {

				$field_id = isset($field['id']) ? $field['id'] : '';
				$field_label = isset($field['label']) ? $field['label'] : '';
				$field_index = isset($field['index']) ? $field['index'] : 0;

				$field_id = trim($field_id);
				if (empty($field_id) && !empty($field_label)) {
					$field_id = URLify::filter($field_label . '-' . $field_index);
					$field_id = str_replace("-", "_", $field_id);
					$field['id'] = $field_id;
				}

				if (isset($field['label']))
					$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);

				$results[] = $field;
			}
		}

		return $results;
	}

	/*
	 * Sanitize Repeat inputs
	 */
	function sanitize_repeat_form_field( $fields, $option ) {

		$results = array();

		if (is_array($fields)) {

			foreach ($fields as $field) {

				$field_id = isset($field['id']) ? trim($field['id']) : '';
				$field_label = isset($field['label']) ? $field['label'] : '';
				$field_index = isset($field['index']) ? $field['index'] : 0;

				if (empty($field_id) && !empty($field_label)) {
					$field_id = URLify::filter($field_label . '-' . $field_index);
					$field_id = str_replace("-", "_", $field_id);
					$field['id'] = $field_id;
				}

				if (isset($field['label'])) {
					$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
				}

				$results[] = $field;
			}
		}
		return $results;
	}

	/*
	 * Custom repeating field scripts
	 * Add and Delete buttons
	 */
	function of_transfers_options_script(){
		global $transfers_plugin_globals;?>
		<style>
			#optionsframework .to-copy {display: none;}
		</style>
		<script type="text/javascript">
		<?php
			echo '	window.adminAjaxUrl = "' . admin_url('admin-ajax.php') . '";';
			echo '  window.adminSiteUrl = "' . admin_url('themes.php?page=options-framework') . '";';
		?>
		</script>
	<?php
	}
}

// store the instance in a variable to be retrieved later and call init
$transfers_plugin_of_custom = Transfers_Plugin_Of_Custom::get_instance();
$transfers_plugin_of_custom->init();
