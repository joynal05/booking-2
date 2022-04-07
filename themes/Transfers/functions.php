<?php
/**
 * Transfers functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 *
 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */

if ( ! defined( 'TRANSFERS_VERSION' ) )
    define( 'TRANSFERS_VERSION', '1.35' );

if ( ! defined( 'TRANSFERS_DATEPICKER_ALT_DATE_FORMAT' ) )
    define( 'TRANSFERS_DATEPICKER_ALT_DATE_FORMAT', 'yy-mm-dd' );

if ( ! defined( 'TRANSFERS_PHP_DATE_FORMAT' ) )
    define( 'TRANSFERS_PHP_DATE_FORMAT', 'Y-m-d H:i' );

if ( ! defined( 'TRANSFERS_PHP_DATE_FORMAT_ZERO_TIME' ) )
    define( 'TRANSFERS_PHP_DATE_FORMAT_ZERO_TIME', 'Y-m-d 00:00' );

if ( ! defined( 'TRANSFERS_PHP_DATE_FORMAT_NO_TIME' ) )
    define( 'TRANSFERS_PHP_DATE_FORMAT_NO_TIME', 'Y-m-d' );

if ( ! defined( 'TRANSFERS_CONTENT_IMAGE_SIZE' ) )
    define( 'TRANSFERS_CONTENT_IMAGE_SIZE', 'transfers-content-image' );

if ( ! defined( 'TRANSFERS_FULL_IMAGE_SIZE' ) )
    define( 'TRANSFERS_FULL_IMAGE_SIZE', 'transfers-full-image' );

if ( ! defined( 'TRANSFERS_THUMB_IMAGE_SIZE' ) )
    define( 'TRANSFERS_THUMB_IMAGE_SIZE', 'transfers-thumb-image' );

// Shim to fix the late load of the WooCommerce autoloader.
if ( defined( 'WC_PLUGIN_FILE' ) && ! function_exists( 'wc_get_loop_prop' ) ) {
	$woocommerce_file = dirname( WC_PLUGIN_FILE ) . '/includes/wc-template-functions.php';
	if ( file_exists( $woocommerce_file ) ) {
		require_once $woocommerce_file;
	}
}

require_once get_template_directory() . '/includes/plugins/urlify/URLify.php';
require_once get_template_directory() . '/includes/themeenergy_common.php';
require_once get_template_directory() . '/includes/theme_utils.php';

global $wpdb, $transfers_multi_language_count, $transfers_installed_version;

$transfers_multi_language_count = 1;
global $sitepress;
if ($sitepress) {
	$active_languages = $sitepress->get_active_languages();
	$sitepress_settings = $sitepress->get_settings();
	$hidden_languages = array();
  if (isset($sitepress_settings['hidden_languages'])) {
      $hidden_languages = $sitepress_settings['hidden_languages'];
  }
  if (is_array($active_languages) && is_array($hidden_languages)) {
      $transfers_multi_language_count = count($active_languages) + count($hidden_languages);
  }
}

$transfers_installed_version = get_option('transfers_version', null);

if ( version_compare( $transfers_installed_version, TRANSFERS_VERSION, '<' ) && null !== $transfers_installed_version && $transfers_installed_version != 0 ) {
	update_option( '_transfers_needs_update', 1 );
	update_option( '_transfers_version_before_update', $transfers_installed_version );
}

if ( version_compare( $transfers_installed_version, TRANSFERS_VERSION, '<' ) || null == $transfers_installed_version ) {
	update_option('transfers_version', TRANSFERS_VERSION);
}


if(!function_exists('optionsframework_option_name')) {
    function optionsframework_option_name() {

		// This gets the theme name from the stylesheet (lowercase and without spaces)
		$themename = get_option( 'stylesheet' );
		$themename = preg_replace( "/\W/", "_", strtolower( $themename ) );

        $optionsframework_settings = get_option('optionsframework');
        $optionsframework_settings['id'] = $themename;
        update_option('optionsframework', $optionsframework_settings);
    }
}

if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/includes/framework/' );
	require_once transfers_get_file_path('/includes/framework/options-framework.php');
}

require_once transfers_get_file_path('/includes/theme_globals.php');

/*-----------------------------------------------------------------------------------*/
/*	Load Widgets, Shortcodes, Metaboxes & Plugins
/*-----------------------------------------------------------------------------------*/
require_once transfers_get_file_path('/includes/plugins/class-tgm-plugin-activation.php');

add_action( 'tgmpa_register', 'transfers_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function transfers_register_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        array(
            'name'      => 'WooSidebars',
            'slug'      => 'woosidebars',
            'required'  => false,
        ),
        array(
            'name'      => 'WooCommerce',
            'slug'      => 'woocommerce',
            'required'  => true,
        ),
        array(
            'name'      => 'Contact Form 7',
            'slug'      => 'contact-form-7',
            'required'  => false,
        ),
		array(
			'name'               => 'Transfers Plugin', // The plugin name.
			'slug'               => 'transfers-plugin', // The plugin slug (typically the folder name).
			'source'             => transfers_get_file_path('/includes/plugins/transfers-plugin/transfers-plugin.zip'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '1.34', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
		),
        // This is an example of how to include a plugin pre-packaged with a theme.
        array(
            'name'               => 'Revolution slider', // The plugin name.
            'slug'               => 'revslider', // The plugin slug (typically the folder name).
            'source'             => transfers_get_file_path('/includes/plugins/revslider/revslider.zip'), // The plugin source.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'version'            => '6.2.23', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
        ),
    );

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => esc_html__( 'Install Required Plugins', 'transfers' ),
            'menu_title'                      => esc_html__( 'Install Plugins', 'transfers' ),
            'installing'                      => esc_html__( 'Installing Plugin: %s', 'transfers' ), // %s = plugin name.
            'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'transfers' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'transfers' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'transfers' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'transfers' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'transfers' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'transfers' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'transfers' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'transfers' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'transfers' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'transfers' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'transfers' ),
            'return'                          => esc_html__( 'Return to Required Plugins Installer', 'transfers' ),
            'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'transfers' ),
            'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'transfers' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}

require_once transfers_get_file_path('/includes/plugins/metaboxes/meta_box.php');
require_once transfers_get_file_path('/includes/theme_meta_boxes.php');
require_once transfers_get_file_path('/includes/theme_filters.php');
require_once transfers_get_file_path('/includes/theme_actions.php');
require_once transfers_get_file_path('/includes/theme_ajax.php');
require_once transfers_get_file_path('/includes/theme_woocommerce.php');

require_once transfers_get_file_path('/includes/classes/abstracts/transfers_entity.php');
require_once transfers_get_file_path('/includes/classes/post.class.php');

require_once transfers_get_file_path('/includes/theme_post_types.php');

require_once transfers_get_file_path('/includes/plugins/widgets/widget-about.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-contact.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-social.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-call-to-action.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-featured-testimonial.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-featured-posts.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-hero-unit.php');
require_once transfers_get_file_path('/includes/plugins/widgets/widget-iconic-features.php');

 /* Rename "home" in breadcrumb */
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text' );
function wcc_change_breadcrumb_home_text( $defaults ) {
    // Change the breadcrumb home text from 'Home' to 'nada'
	$defaults['home'] = '';
	return $defaults;
}

/*empty cart if user come to homepage*/
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
global $woocommerce;

if ($_SERVER['REQUEST_URI'] === '/en/') {
$woocommerce->cart->empty_cart();
}
if ($_SERVER['REQUEST_URI'] === '/') {
$woocommerce->cart->empty_cart();
}
if ($_SERVER['REQUEST_URI'] === '/de/') {
$woocommerce->cart->empty_cart();
}
}  
/* Cambiar de en-GB hasta EN */
/*global $sitepress;
add_filter( 'language_attributes', 'mycustom_language_attributes');
function mycustom_language_attributes( $output ){
if ( preg_match( '#lang="[a-z-]+"#i', $output ) ) {
if(strrpos ( $output , 'en-GB' )){
$output = preg_replace( '#lang="([a-z-]+)"#i', 'lang="en"', $output );
}
}
return $output;
}*/