<?php
/*
	Plugin Name: Transfers Plugin
	Plugin URI: http://themeenergy.com/themes/wordpress/transfers/
	Description: A plugin that works in conjunction with the Transfers WordPress theme and makes the theme fully functional.
	Version: 1.34
	Author: ThemeEnergy
	Author URI: http://www.themeenergy.com
	License: GNU General Public License
	License URI: http://themeforest.net/wiki/support/legal-terms/licensing-terms/
*/

global $wpdb;

// require_once(ABSPATH . 'wp-includes/pluggable.php');

if ( ! function_exists( 'of_get_option' ) ) :

	function of_get_option( $name, $default = false ) {
		$config = get_option( 'optionsframework' );
	
		if ( ! isset( $config['id'] ) ) {
			return $default;
		}
	
		$options = get_option( $config['id'] );
	
		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}
	
		return $default;
	}
	
endif;

if ( ! defined( 'TRANSFERS_PLUGIN_VERSION' ) )
    define( 'TRANSFERS_PLUGIN_VERSION', '1.34' );

define( 'TRANSFERS_PLUGIN', __FILE__ );

define( 'TRANSFERS_PLUGIN_BASENAME', plugin_basename( TRANSFERS_PLUGIN ) );

define( 'TRANSFERS_PLUGIN_NAME', trim( dirname( TRANSFERS_PLUGIN_BASENAME ), '/' ) );

define( 'TRANSFERS_PLUGIN_DIR', untrailingslashit( dirname( TRANSFERS_PLUGIN ) ) );

define( 'TRANSFERS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'TRANSFERS_AVAILABILITY_TABLE' ) )
    define( 'TRANSFERS_AVAILABILITY_TABLE', $wpdb->prefix . 'transfers_availability' );

if ( ! defined( 'TRANSFERS_BOOKING_TABLE' ) )
    define( 'TRANSFERS_BOOKING_TABLE', $wpdb->prefix . 'transfers_booking' );

if ( ! defined( 'TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE' ) )
    define( 'TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE', $wpdb->prefix . 'transfers_booking_extra_items' );

global $transfers_installed_plugin_version;
$transfers_installed_plugin_version = get_option('transfers_plugin_version', null);

require_once TRANSFERS_PLUGIN_DIR . '/includes/themeenergy_common.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_utils.php';

require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_actions.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_ajax.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_of_custom.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/metaboxes/meta_box.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_globals.php';

require_once TRANSFERS_PLUGIN_DIR . '/includes/classes/abstracts/transfers_entity.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/classes/service.class.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/classes/faq.class.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/classes/destination.class.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/classes/transport_type.class.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/classes/extra_item.class.php';

require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_of_default_fields.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_post_types.php';

if ( version_compare( $transfers_installed_plugin_version, TRANSFERS_PLUGIN_VERSION, '<' ) || null == $transfers_installed_plugin_version ) {
	update_option('transfers_plugin_version', TRANSFERS_PLUGIN_VERSION);
}

require_once TRANSFERS_PLUGIN_DIR . '/includes/plugin_woocommerce.php';

require_once TRANSFERS_PLUGIN_DIR . '/includes/admin/transfers_availability_management_admin.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/admin/transfers_bookings_management_admin.php';

require_once TRANSFERS_PLUGIN_DIR . '/includes/widgets/widget-featured-services.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/widgets/widget-advanced-search.php';

function is_transfers_plugin_active() {
	$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
 	if (is_array ($active_plugins) && in_array('transfers-plugin/transfers-plugin.php', $active_plugins)) {
		return true;
	}
	$network_active_plugins = get_site_option('active_sitewide_plugins'); 
	if (is_array ($network_active_plugins) && array_key_exists('transfers-plugin/transfers-plugin.php', $network_active_plugins)) {
		return true;
	}
	return false;
}

function transfers_extra_tables_exist() {

	global $transfers_plugin_post_types;
	return $transfers_plugin_post_types->extra_tables_exist();
}
