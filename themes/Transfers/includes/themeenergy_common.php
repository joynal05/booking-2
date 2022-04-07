<?php

if ( !function_exists('transfers_is_translatable') ) {	
	function transfers_is_translatable($post_type) {
		$is_translatable = false;						
		global $sitepress;
		if ($sitepress && function_exists('is_post_type_translated')) {
			$is_translatable = is_post_type_translated($post_type);
		}
		return $is_translatable;
	}
}

if ( !function_exists('transfers_encrypt') ) {	
	function transfers_encrypt($pure_string, $encryption_key) {
		return $pure_string;		
	}
}

if ( !function_exists('transfers_decrypt') ) {	
	function transfers_decrypt($encrypted_string, $encryption_key) {
		return $encrypted_string;		
	}
}

if ( !function_exists('transfers_sanitize_array') ) {
	function transfers_sanitize_array($in = array()) {

		if (!is_array($in) || !count($in)) {
			return array();
		}
		foreach ($in as $k => $v) {
			if (!is_array($v) && !is_object($v)) {
				$in[$k] = htmlspecialchars(trim($v));
			}
			if (is_array($v)) {
				$in[$k] = transfers_sanitize_array($v);
			}
		}
		return $in;
	}
}

if ( !function_exists('transfers_strip_tags_and_shorten') ) {
	function transfers_strip_tags_and_shorten($content, $character_count) {
		if (function_exists('mb_strlen') && function_exists('mb_substr')) {
			$content = wp_strip_all_tags($content);
			return (mb_strlen($content) > $character_count) ? mb_substr($content, 0, $character_count).' ' : $content;
		}
		return $content;
	}
}
	
if ( !function_exists('transfers_strip_tags_and_shorten_by_words') ) {
	function transfers_strip_tags_and_shorten_by_words($content, $words) {
		$content = wp_strip_all_tags($content);
		return implode(' ', array_slice(explode(' ', $content), 0, $words));
	}
}

if ( !function_exists('transfers_get_allowed_content_tags_array') ) {
	function transfers_get_allowed_content_tags_array() {
			
		global $allowedtags;
		
		$allowedtags = array(
			'a' => array(
				'class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array()
			),
			'div' => array(
				'class' => array(), 'id' => array(), 'style' => array()
			),
			'span' => array(
				'class' => array(), 'id' => array(), 'style' => array()
			),
			'ul' => array(
				'id' => array(),			
				'class' => array()
			),			
			'li' => array(
				'class' => array(),	
			),	
			'p' => array(
				'class' => array(),
			),
			'b' => array(
				'class' => array(),
			),
			'i' => array(
				'class' => array(),
			),
			'h1' => array(
				'class' => array(),
			),
			'h2' => array(
				'class' => array(),
			),
			'h3' => array(
				'class' => array(),
			),
			'h4' => array(
				'class' => array(),
			),
			'h5' => array(
				'class' => array(),
			),
			'h6' => array(
				'class' => array(),
			),
			'em' => array(),
			'strong' => array(),
			'img' => array(
				'src' => array(),
				'title' => array(),
				'alt' => array()						
			)				
		);	
	
		return apply_filters( 'transfers_allowed_content_tags', $allowedtags );
	}
}	

if ( !function_exists( 'transfers_does_file_exist' ) ) {
	function transfers_does_file_exist($relative_path_to_file) {
		return (file_exists(TRANSFERS_PLUGIN_DIR . $relative_path_to_file) || file_exists(get_stylesheet_directory() . $relative_path_to_file) || file_exists(get_template_directory() . $relative_path_to_file));
	}
}
	
if ( !function_exists( 'transfers_get_file_path' ) ) {
	function transfers_get_file_path($relative_path_to_file) {
		if (is_child_theme()) {
			if (file_exists(get_stylesheet_directory() . $relative_path_to_file))
				return get_stylesheet_directory() . $relative_path_to_file;
			else
				return get_template_directory() . $relative_path_to_file;
		}
		return get_template_directory() . $relative_path_to_file;
	}
}

if ( !function_exists( 'transfers_get_file_uri' ) ) {
	function transfers_get_file_uri($relative_path_to_file) {
		if (is_child_theme()) {
			if (file_exists(get_stylesheet_directory() . $relative_path_to_file))
				return get_stylesheet_directory_uri() . $relative_path_to_file;
			else
				return get_template_directory_uri() . $relative_path_to_file;
		}
		return get_template_directory_uri() . $relative_path_to_file;
	}
}

if ( !function_exists( 'transfers_plugin_of_get_option' ) ) {
	function transfers_plugin_of_get_option($name, $default = false) {
		
		$option_name = '';
		// Gets option name as defined in the theme
		if ( function_exists( 'optionsframework_option_name' ) ) {
			$option_name = optionsframework_option_name();
		}

		// Fallback option name
		if ( '' == $option_name ) {
			$option_name = get_option( 'stylesheet' );
			$option_name = preg_replace( "/\W/", "_", strtolower( $option_name ) );
		}

		// Get option settings from database
		$options = get_option( $option_name );
			
		if ( isset($options[$name]) ) {
			return $options[$name];
		} else {
			return $default;
		}
	}
}

if (!function_exists('transfers_get_allowed_widgets_tags_array')) {
	function transfers_get_allowed_widgets_tags_array() {
			
		global $allowedtags;
		
		$allowedtags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'nav' => array(
				'role' => array(),
				'class' => array(),			
				'id' => array(),
			),
			'ul' => array(
				'class' => array(),			
				'id' => array(),
			),
			'li' => array(
				'class' => array(),	
			),			
			'em' => array(),
			'strong' => array(),
			'div' => array(
				'class' => array(),			
				'id' => array(),
			),
			'span' => array(
				'class' => array(),			
				'id' => array(),
			),
			'p' => array(
				'class' => array(),			
				'id' => array(),
			),
			'h1' => array(
				'class' => array(),			
				'id' => array(),
			),
			'h2' => array(
				'class' => array(),			
				'id' => array(),
			),
			'h3' => array(
				'class' => array(),			
				'id' => array(),
			),
			'h4' => array(
				'class' => array(),			
				'id' => array(),
			),
			'h5' => array(
				'class' => array(),			
				'id' => array(),
			),
			'h6' => array(
				'class' => array(),			
				'id' => array(),
			),
		);	
	
		return apply_filters( 'transfers_allowed_widgets_tags', $allowedtags );
	}
}

if (!function_exists('transfers_get_allowed_form_tags_array')) {
	function transfers_get_allowed_form_tags_array() {
			
		global $allowedtags;
		
		$allowedtags['div'] = array('class' => array(), 'id' => array(), 'style' => array());
		$allowedtags['a'] = array('class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array());		
		$allowedtags['span'] = array('class' => array(), 'id' => array());
		$allowedtags['p'] = array('class' => array(), 'id' => array());
		$allowedtags['small'] = array('class' => array(), 'id' => array());
		$allowedtags['h2'] = array('class' => array(), 'id' => array());
		$allowedtags['h3'] = array('class' => array(), 'id' => array());
		$allowedtags['h4'] = array('class' => array(), 'id' => array());
		
		$allowedtags['table'] = array(
			'class' => array(), 
			'id' => array()
		);
		
		$allowedtags['tbody'] = array(
			'class' => array(), 
			'id' => array()
		);
			
		$allowedtags['tr'] =  array(
			'class' => array(),
			'id' => array(),
			'rowspan' => array()
		);
		
		$allowedtags['td'] = array(
			'class' => array(),
			'id' => array(),
			'colspan' => array()
		);
		
		$allowedtags['select'] = array(
			'class' => array(), 
			'id' => array(), 
			'name' => array()
		);
		
		$allowedtags['option'] = array(
			'name' => array(), 
			'value' => array(),
			'selected' => array()
		);
		
		$allowedtags['optgroup'] = array(
			'class' => array(), 
			'id' => array(), 
			'name' => array(),
			'value' => array(),
			'label' => array(),
		);
		
		$allowedtags['label'] = array(
			'class' => array(), 
			'id' => array(), 
			'for' => array()
		);
		
		$allowedtags['form'] = array(
			'action' => array(), 
			'id' => array(), 
			'name' => array(),
			'class' => array(),
			'method' => array()			
		);		
		
		$allowedtags['ul'] = array(
			'class' => array(), 
			'id' => array(), 
		);
		
		$allowedtags['li'] = array(
			'class' => array(), 
			'id' => array(), 
		);
		
		$allowedtags['img'] = array(
			'class' => array(), 
			'id' => array(), 
			'src' => array(),
			'alt' => array(),			
			'title' => array()
		);
		$allowedtags['input'] = array(
			'class' => array(), 
			'id' => array(), 
			'name' => array(),
			'value' => array(),
			'checked' => array(),
			'type' => array(),
			'placeholder' => array(),
		);
		$allowedtags['textarea'] = array(
			'class' => array(), 
			'id' => array(), 
			'name' => array(),
			'rows' => array(),
			'cols' => array()
		);	
		
		$allowedtags['script'] = array(	);

		return apply_filters( 'transfers_allowed_form_tags', $allowedtags );		
	}
}

if (!function_exists('transfers_is_wpml_active')) {
	function transfers_is_wpml_active() {
		return class_exists('SitePress');
	}		
}

if (!function_exists('transfers_is_woocommerce_active')) {
	function transfers_is_woocommerce_active() {
		return class_exists('WooCommerce');
	}		
}

if (!function_exists('transfers_get_cart_page_url')) {
	function transfers_get_cart_page_url() {
		$cart_page_url = '';
		if (function_exists('wc_get_page_id') && transfers_is_woocommerce_active()) {
			$cart_page_id = wc_get_page_id( 'cart' );
			$cart_page_id = transfers_get_current_language_page_id($cart_page_id);
			$cart_page_url = get_permalink($cart_page_id);
		}
	
		return $cart_page_url;
	}
}

if (!function_exists('transfers_get_current_language_post_id')) {
	function transfers_get_current_language_post_id($id, $post_type = 'post', $return_original_if_missing = true) {
		if(function_exists('icl_object_id')) {
			return icl_object_id($id,$post_type, $return_original_if_missing);
		} else {
			return $id;
		}
	}
}

if (!function_exists('transfers_get_current_language_page_id')) {
	function transfers_get_current_language_page_id($id) {
		if(function_exists('icl_object_id')) {
			return icl_object_id($id,'page',true);
		} else {
			return $id;
		}
	}
}

if (!function_exists('transfers_get_language_post_id')) {
	function transfers_get_language_post_id($id, $post_type, $language, $return_original_if_missing = true) {
		global $sitepress;
		if ($sitepress) {
			if(function_exists('icl_object_id')) {
				return icl_object_id($id, $post_type, $return_original_if_missing, $language);
			} else {
				return $id;
			}
		}
		return $id;	
	}
}
		
if (!function_exists('transfers_get_default_language_post_id')) {
	function transfers_get_default_language_post_id($id, $post_type) {
		global $sitepress;
		if ($sitepress) {
			$default_language = $sitepress->get_default_language();
			if(function_exists('icl_object_id')) {
				return icl_object_id($id, $post_type, true, $default_language);
			} else {
				return $id;
			}
		}
		return $id;	
	}
}

if (!function_exists('transfers_get_default_language')) {
	function transfers_get_default_language() {
		global $sitepress;
		if ($sitepress) {
			return $sitepress->get_default_language();
		} else if (defined('WPLANG')) {
			return WPLANG;
		} else
			return "en";	
	}
}

if (!function_exists('transfers_table_exists')) {
	function transfers_table_exists($table_name) {
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return false;
		}
		return true;
	}
}
	
if (!function_exists('transfers_string_contains')) {
	function transfers_string_contains($haystack, $needle) {
		if (strpos($haystack, $needle) !== FALSE)
			return true;
		else
			return false;
	}
}
	
if (!function_exists('transfers_get_current_language_code')) {
	function transfers_get_current_language_code() {
	
		global $sitepress;
		if ($sitepress) {
			$current_language = $sitepress->get_current_language();
			return $current_language;
		} else if ( defined( 'ICL_LANGUAGE_CODE' ) )
			return defined( 'ICL_LANGUAGE_CODE' );
		else 
			return substr(get_locale(), 0, 2);
	}
}

if (!function_exists('transfers_get_current_page_url')) {
	function transfers_get_current_page_url() {
		return home_url( add_query_arg( NULL, NULL ) );
	}
}