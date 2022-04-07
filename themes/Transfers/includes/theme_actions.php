<?php

class Transfers_Theme_Actions extends Transfers_BaseSingleton {

	protected function __construct() {

        // our parent class might contain shared code in its constructor
        parent::__construct();

    }

    public function init() {

		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );
		add_action( 'register_form', array( $this, 'password_register_fields'), 10, 1 );
		add_action( 'login_form_login', array($this, 'disable_wp_login' ) );
	}

	/**
	 * Disable WP login if option enabled in Theme settings
	 */
	function disable_wp_login(){

		global $transfers_theme_globals;
		if ($transfers_theme_globals->override_wp_login()) {
			$login_page_url = $transfers_theme_globals->get_login_page_url();
			$redirect_to_after_logout_url = $transfers_theme_globals->get_redirect_to_after_logout_url();
			if (!empty($login_page_url) && !empty($redirect_to_after_logout_url)) {
				if( isset( $_GET['loggedout'] ) ){
					wp_redirect( $transfers_theme_globals->get_redirect_to_after_logout_url() );
				} else{
					wp_redirect( $transfers_theme_globals->get_login_page_url() );
				}
			}
		}
	}

	/**
	 * Add password fields to wordpress registration form if option for users to set their own password is enabled in Theme settings.
	 */
	function password_register_fields(){

		global $transfers_theme_globals;

		$let_users_set_pass = $transfers_theme_globals->let_users_set_pass();

		if ($let_users_set_pass) {
	?>
		<div class="f-row twins">
			<div class="one-half">
				<label for="password"><?php esc_html_e('Password', 'transfers'); ?></label>
				<input tabindex="3" id="password" class="input" type="password" tabindex="30" size="25" value="" name="password" />
			</div>
			<div class="one-half">
				<label for="repeat_password"><?php esc_html_e('Repeat password', 'transfers'); ?></label>
				<input tabindex="4" id="repeat_password" class="input" type="password" tabindex="40" size="25" value="" name="repeat_password" />
			</div>
		</div>
	<?php
		}
	}

	 /**
	 * Sets up theme defaults and registers the various WordPress features that
	 * Transfers supports.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
	 * 	custom background, and post formats.
	 * @uses register_nav_menu() To add support for navigation menus.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Transfers 1.0
	 */
	function setup() {

		add_editor_style( trailingslashit( get_stylesheet_directory_uri() ) . 'css/editor-style.css' );
		add_theme_support( 'custom-logo' );
		/*
		 * Transfers available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Transfers, use a find and replace
		 * to change 'transfers' to the name of your theme in all the template files.
		 */

		load_theme_textdomain( 'transfers', get_template_directory() . '/languages' );

		// This theme uses wp_nav_menu() in three locations.
		register_nav_menus( array(
			'primary-menu' => esc_html__( 'Primary Menu', 'transfers' ),
			'footer-menu' => esc_html__( 'Footer Menu', 'transfers' )
		) );

		// This theme uses a custom image size for featured images, displayed on "standard" posts.
		add_theme_support( 'post-thumbnails' );

		// This theme is woocommerce compatible
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

		add_theme_support( 'automatic-feed-links' );

		add_theme_support( 'title-tag' );

		if ( ! isset( $content_width ) ) {
			$content_width = 870;
		}

		set_post_thumbnail_size( 870, 653, false);

		add_image_size(TRANSFERS_CONTENT_IMAGE_SIZE, 870, 653, false);
		add_image_size(TRANSFERS_FULL_IMAGE_SIZE, 1920, 1280, false);
		add_image_size(TRANSFERS_THUMB_IMAGE_SIZE, 400, 267, true);

		//Left Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Left Sidebar', 'transfers'),
			'id'=>'left',
			'description' => esc_html__('This Widget area is used for the left sidebar', 'transfers'),
			'before_widget' => '<li id="%1$s" class="widget widget-sidebar %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));

		// Right Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Right Sidebar', 'transfers'),
			'id'=>'right',
			'description' => esc_html__('This Widget area is used for the right sidebar', 'transfers'),
			'before_widget' => '<li id="%1$s" class="widget widget-sidebar %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));

		// Under Header Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Under Header Sidebar', 'transfers'),
			'id'=>'under-header',
			'description' => esc_html__('This Widget area is placed under the website header', 'transfers'),
			'before_widget' => '<li id="%1$s" class="widget widget-sidebar %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));

		// Under Header Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Above Footer Sidebar', 'transfers'),
			'id'=>'above-footer',
			'description' => esc_html__('This Widget area is placed above the website footer', 'transfers'),
			'before_widget' => '<li id="%1$s" class="widget widget-sidebar %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

		// Footer Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Footer Sidebar', 'transfers'),
			'id'=>'footer',
			'description' => esc_html__('This Widget area is used for the footer area', 'transfers'),
			'before_widget' => '<li id="%1$s" class="widget widget-sidebar %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h6>',
			'after_title' => '</h6>',
		));

		// Home Content Widget area
		register_sidebar(array(
			'name'=> esc_html__('Home Content Widget Area', 'transfers'),
			'id'=>'home-content',
			'description' => esc_html__('This Widget area is used for the home page main content area', 'transfers'),
			'before_widget' => '<li id="%1$s" class="widget widget-sidebar %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

	}

	function get_google_fonts_uri() {

		$fonts_url = '';

		/* Translators: If there are characters in your language that are not
		* supported by Montserrat, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$montserrat = _x( 'on', 'Montserrat font: on or off', 'transfers' );

		/* Translators: If there are characters in your language that are not
		* supported by Raleway, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$raleway = _x( 'on', 'Raleway font: on or off', 'transfers' );

		if ( 'off' !== $montserrat || 'off' !== $raleway ) {

			$font_families = array();

			if ( 'off' !== $montserrat ) {
				$font_families[] = 'Montserrat:400,700';
			}

			if ( 'off' !== $raleway ) {
				$font_families[] = 'Raleway:400,500,600,700';
			}

			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( 'latin,latin-ext' ),
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return esc_url_raw( $fonts_url );
	}

	/**
	 * Enqueues scripts and styles for front-end.
	 *
	 * @since Transfers 1.0
	 */
	function enqueue_scripts_styles() {

		global $wp_styles;
		global $transfers_theme_globals;
		$language_code = transfers_get_current_language_code();

		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

		/*
		 * Adds JavaScript for various theme features
		 */

		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');

		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-effects-core');

		wp_enqueue_script( 'transfers-jquery-validate', transfers_get_file_uri ('/js/jquery.validate.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'transfers-jquery-uniform', transfers_get_file_uri ('/js/jquery.uniform.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'transfers-respond', transfers_get_file_uri ('/js/respond.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'transfers-slicknav', transfers_get_file_uri ('/js/jquery.slicknav.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'transfers-scripts', transfers_get_file_uri ('/js/scripts.js'), array('jquery'), '1.0', true );
		wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/e808bf9397.js', array('jquery'), '1.0', true );

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

		/*
		 * Loads our main stylesheets.
		 */
		$google_fonts_uri = $this->get_google_fonts_uri();
		if (!empty($google_fonts_uri)) {
			wp_enqueue_style( 'transfers-font-style', $google_fonts_uri);
		}
		wp_enqueue_style( 'transfers-style-main', transfers_get_file_uri('/css/style.css'), array('transfers-font-style'), '1.0', "all");
		wp_enqueue_style( 'transfers-style', get_stylesheet_uri(), array('transfers-style-main'), '1.0', "all" );
		wp_enqueue_style( 'transfers-style-ui', transfers_get_file_uri('/css/jquery-ui.theme.min.css'), array('transfers-style'), '1.0', "all");

		if ($transfers_theme_globals->enable_rtl()) {
			wp_enqueue_style( 'transfers-style-rtl', transfers_get_file_uri('/css/style-rtl.css'), array('transfers-style'), '1.0', "all");
		}

		wp_enqueue_style( 'transfers-fonts', transfers_get_file_uri('/css/fonts.css'), array('transfers-style'), '1.0', "all");

		/*
		 * Load the color scheme sheet if set in set in options.
		 */
		$color_scheme_style_sheet = $transfers_theme_globals->get_color_scheme_style_sheet();
		if (!empty($color_scheme_style_sheet)) {
			wp_enqueue_style('transfers-style-color',  transfers_get_file_uri('/css/' . $color_scheme_style_sheet . '.css'), array('transfers-style-ui'), '1.0', "all");
		}

		if (is_page()) {

			$page_id     = get_queried_object_id();

			$google_maps_key = $transfers_theme_globals->get_google_maps_key();

			$template_file = get_post_meta($page_id,'_wp_page_template',true);
			if ($template_file == 'page-contact.php' || $template_file == 'page-contact-form-7.php') {

				wp_register_script('transfers-google-maps','//maps.google.com/maps/api/js?key=' . $google_maps_key,	'jquery','1.0',true);
				wp_enqueue_script( 'transfers-google-maps' );
				wp_register_script('transfers-infobox', transfers_get_file_uri('/js/infobox.js'), array('jquery', 'transfers-google-maps'),'1.0',true);
				wp_enqueue_script( 'transfers-infobox' );
				wp_register_script(	'transfers-contact', transfers_get_file_uri('/js/contact.js'), array('jquery', 'transfers-google-maps', 'transfers-infobox', 'transfers-jquery-validate'), '1.0',true);
				wp_enqueue_script( 'transfers-contact' );
			} else if ($template_file == 'page-user-account.php') {
				wp_register_script(	'transfers-account', transfers_get_file_uri('/js/account.js'), array('jquery', 'transfers-jquery-validate'), '1.0',true);
				wp_enqueue_script( 'transfers-account' );
			} else if ($template_file == 'page-booking-form.php') {
				wp_register_script(	'transfers-booking', transfers_get_file_uri('/js/booking.js'), array('jquery', 'transfers-jquery-validate'), '1.0',true);
				wp_enqueue_script( 'transfers-booking' );
			}
		}

		do_action('transfers_enqueue_scripts_styles');
	}

	/**
	 * Enqueues scripts and styles for admin.
	 *
	 * @since Transfers 1.0
	 */
	function enqueue_admin_scripts_styles() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-effects-core');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-sortable');

		wp_enqueue_style('transfers-admin-ui-css', transfers_get_file_uri('/css/jquery-ui.min.css'), false);
		wp_enqueue_style('transfers-admin-css', transfers_get_file_uri('/css/admin-custom.css'), false);

		do_action('transfers_enqueue_admin_scripts_styles');
	}

}

// store the instance in a variable to be retrieved later and call init
$transfers_theme_actions = Transfers_Theme_Actions::get_instance();
$transfers_theme_actions->init();
