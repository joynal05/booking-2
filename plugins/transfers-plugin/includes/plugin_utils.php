<?php

class Transfers_Plugin_Utils {

	public static function custom_array_search($array, $key, $value)
	{
		$results = array();

		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}

			foreach ($array as $subarray) {
				$results = array_merge($results, Transfers_Plugin_Utils::custom_array_search($subarray, $key, $value));
			}
		}

		return $results;
	}

	public static function transfers_wpml_translate_post($original_post_id, $post_type, $language_code) {
	 
		if (transfers_is_wpml_active()) {
		
			// Include WPML API
			include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );		
		
			global $wpdb, $sitepress;
			
			// Get default language
			$default_lang_code = wpml_get_default_language();

			if ($language_code != $default_lang_code) {			
		
				// Define title of translated post
				$translated_post = get_post( $original_post_id );
				$translated_post->post_title = $translated_post->post_title . ' (' . $language_code . ')';
				$translated_post->post_content = $translated_post->post_content . ' (' . $language_code . ')';
				$translated_post->icl_post_language = $language_code;
				$translated_post->ID = null;
			 
				// Create translated post
				$post_translated_id = wp_insert_post( $translated_post );
			 
				wpml_update_translatable_content( 'post_'. $post_type, $post_translated_id, $language_code ); 	// update language code
				
				$post_meta_keys = get_post_custom_keys($original_post_id);
				if (!empty($post_meta_keys))
				{
					foreach ($post_meta_keys as $meta_key) {
						$meta_values = get_post_custom_values($meta_key, $original_post_id);
						foreach ($meta_values as $meta_value) {
							$meta_value = maybe_unserialize($meta_value);
							add_post_meta($post_translated_id, $meta_key, $meta_value);
						}
					}				
				}
				
				$trid = wpml_get_content_trid('post_'. $post_type, $original_post_id); 	// get trid 
			 
				$sitepress->set_element_language_details($post_translated_id, 'post_'. $post_type, $trid, $language_code, $default_lang_code);				
			 
				// Return translated post ID
				return $post_translated_id;
			}
		}
		
		return -1;
	}


	public static function display_hours_and_minutes($time, $format = "%02d:%02d") {
		settype($time, 'integer');
		if ($time < 0) {
			return;
		}
		$hours = date('H', mktime(0,$time));
		$minutes = date('i', mktime(0,$time));
		return sprintf($format, $hours, $minutes);
	}

	public static function get_day_of_week_index($time_stamp) {
		return (date("w", $time_stamp) + 6) % 7;
	}

	public static function get_days_of_week() {

		$days_of_week = array();
		$days_of_week[0] = esc_html__('Monday', 'transfers');
		$days_of_week[1] = esc_html__('Tuesday', 'transfers');
		$days_of_week[2] = esc_html__('Wednesday', 'transfers');
		$days_of_week[3] = esc_html__('Thursday', 'transfers');
		$days_of_week[4] = esc_html__('Friday', 'transfers');
		$days_of_week[5] = esc_html__('Saturday', 'transfers');
		$days_of_week[6] = esc_html__('Sunday', 'transfers'); 
		
		return $days_of_week;
	}

	/**
	 * 	Function that renders all extra fields tied to an custom post, as labeled field in the form of
	 * 	<div class="container_css_class">
	 *		<span class="label_css_class">$label_text</span> $field_value
	 *	</div>
	 */
	public static function render_extra_fields($option_id, $extra_fields, $entity_obj, $container_class = "text-wrap", $label_is_header = true, $id_is_css_class = false, $container_is_tr = false) {
		
		global $transfers_plugin_of_custom;
		
		if (is_array($extra_fields)) {
		
			foreach ($extra_fields as $extra_field) {
		
				$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
				
				if (!$field_is_hidden) {
				
					$field_id = isset($extra_field['id']) ? $extra_field['id'] : '';
					$field_label = isset($extra_field['label']) ? $extra_field['label'] : '';
					$field_label = $transfers_plugin_of_custom->get_translated_dynamic_string($transfers_plugin_of_custom->get_option_id_context($option_id) . ' ' . $field_label, $field_label);
					$field_type = isset($extra_field['type']) ? $extra_field['type'] : ''; 
										
					if ($field_type == 'text' ||$field_type == 'textarea') {
					
						if (!empty($field_id) && !empty($field_label)) {
							if ($id_is_css_class)
								$container_class = $field_id;
							if ($label_is_header)
								Transfers_Plugin_Utils::render_field($container_class, 	"", "", $entity_obj->get_custom_field($field_id), $field_label, false, true, $container_is_tr);
							else
								Transfers_Plugin_Utils::render_field($container_class, 	"", $field_label, $entity_obj->get_custom_field($field_id), "", false, true, $container_is_tr);
						}
					} elseif ($field_type == 'image') {
					
						$field_image_uri = $entity_obj->get_custom_field_image_uri($field_id, 'medium');

						if (isset($field_image_uri) && !empty($field_image_uri))
							echo '<img src="' . $field_image_uri . '" alt="' . $field_label . '" />';
					}
				}
			}
		}
	}
	
	public static function render_field($container_css_class, $label_css_class, $label_text, $field_value, $header_text = '', $paragraph = false, $hide_empty = false, $container_is_tr = false) {

		$render = !empty($field_value) || (!empty($label_text) && !$hide_empty);
		
		if ($render) {

			$ret_val = '';
		
			if (!empty($header_text) && !$container_is_tr)
				$ret_val = sprintf("<h3>%s</h3>", $header_text);
			
			if (!empty($container_css_class)) {
				if ($container_is_tr)
					$ret_val .= sprintf("<tr class='%s'>", $container_css_class);
				else
					$ret_val .= sprintf("<div class='%s'>", $container_css_class);
			}
				
			if ($paragraph && !$container_is_tr)
				$ret_val .= '<p>';

			if (!empty($label_text) || !empty($label_css_class)) {
				if ($container_is_tr)
					$ret_val .= sprintf("<th class='%s'>%s</th>", $label_css_class, $label_text);
				else 
					$ret_val .= sprintf("<span class='%s'>%s</span>", $label_css_class, $label_text);
			}

			if (!empty($field_value)) {
				if ($container_is_tr)
					$ret_val .= sprintf('<td>%s</td>', $field_value);
				else
					$ret_val .= $field_value;
			} else {
				if ($container_is_tr)
					$ret_val .= '<td></td>';
			}
			
			if ($paragraph && !$container_is_tr)
				$ret_val .= '</p>';
				
			if (!empty($container_css_class)) {
				if ($container_is_tr)
					$ret_val .= '</tr>';
				else
					$ret_val .= '</div>';
			}

			$ret_val = apply_filters('transfers_render_field', $ret_val, $container_css_class, $label_css_class, $label_text, $field_value, $header_text, $paragraph);

			$allowedtags = transfers_get_allowed_form_tags_array();
			
			echo wp_kses($ret_val, $allowedtags);
		}
	}
	
	public static function build_destination_select_recursively($destination_id=null, $selected_destination_id = null, $level = 0, $is_departure = true) {
	
		global $transfers_destinations_post_type;
		
		$return_string = '';
		
		if (isset($destination_id) && $destination_id > 0) {
		
			$transfers_destination = new transfers_destination($destination_id);
			$current_destination_id = $destination_id;
			
			if ($transfers_destination->is_parent() == '1')
				$return_string .= '<optgroup label="' .  $transfers_destination->get_title() . '">';
			else
				$return_string .= '<option value="' . $transfers_destination->get_id() . '" ' . ($selected_destination_id == $current_destination_id ? "selected" : "") . '>' . $transfers_destination->get_title() . '</option>';
		}
		
		$destination_results = $transfers_destinations_post_type->list_destinations(0, -1, 'title', 'ASC', $destination_id);
		
		if ( count($destination_results) > 0 && $destination_results['total'] > 0 ) {
			
			foreach ($destination_results['results'] as $destination_result) {

				$destination = $destination_result;
				$transfers_sub_destination = new transfers_destination($destination->ID);
				$current_sub_destination_id = $destination->ID;
				
				if ($transfers_sub_destination->is_parent() == '1') {
					$new_level = $level + 1;
					$return_string .= Transfers_Plugin_Utils::build_destination_select_recursively($current_sub_destination_id, $selected_destination_id, $new_level);
				} else {
					$return_string .= '<option value="' . $transfers_sub_destination->get_id() . '" ' . ($selected_destination_id == $current_sub_destination_id ? "selected" : "") . '>' . $transfers_sub_destination->get_title() . '</option>';
				}
			}		
		}
		
		if (isset($destination_id)) {
		
			$transfers_destination = new transfers_destination($destination_id);
			
			if ($transfers_destination->is_parent() == '1') {
				$return_string .= '</optgroup>';
			}				
		}
		
		return $return_string;
	}
	
	/*
	 * from: http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 */
	public static function dateformat_PHP_to_jQueryUI($php_format)
	{
		$SYMBOLS_MATCHING = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => ''
		);
		$jqueryui_format = "";
		$escaping = false;
		for($i = 0; $i < strlen($php_format); $i++)
		{
			$char = $php_format[$i];
			if($char === '\\') // PHP date format escaping character
			{
				$i++;
				if($escaping) $jqueryui_format .= $php_format[$i];
				else $jqueryui_format .= '\'' . $php_format[$i];
				$escaping = true;
			}
			else
			{
				if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
				if(isset($SYMBOLS_MATCHING[$char]))
					$jqueryui_format .= $SYMBOLS_MATCHING[$char];
				else
					$jqueryui_format .= $char;
			}
		}
		return $jqueryui_format;
	}
}

if (!class_exists('Transfers_BaseSingleton')) { 
	//
	// http://scotty-t.com/2012/07/09/wp-you-oop/
	//
	abstract class Transfers_BaseSingleton {
		private static $instance = array();
		protected function __construct() {}
		
		public static function get_instance() {
			$c = get_called_class();
			if (!isset(self::$instance[$c])) {
				self::$instance[$c] = new $c();
				self::$instance[$c]->init();
			}

			return self::$instance[$c];
		}

		abstract public function init();
	}
}