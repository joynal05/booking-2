<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
// function optionsframework_option_name() {
   // $themename = get_option( 'stylesheet' );
   // $themename = preg_replace( "/\W/", "_", strtolower( $themename ) );
   // return $themename;
// }

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'theme-textdomain'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {
	
	$page_sidebars = array(
		'' => esc_html__('No sidebar', 'transfers'),
		'left' => esc_html__('Left sidebar', 'transfers'),
		'right' => esc_html__('Right sidebar', 'transfers'),
		'both' => esc_html__('Left and right sidebars', 'transfers'),
	);
	
	$sort_by_columns = array();
	$sort_by_columns['title'] = esc_html__('Title', 'transfers');
	$sort_by_columns['ID'] = esc_html__('ID', 'transfers');
	$sort_by_columns['date'] =  esc_html__('Publish date', 'transfers');
	$sort_by_columns['rand'] =  esc_html__('Random', 'transfers');
	$sort_by_columns['comment_count'] =  esc_html__('Comment count', 'transfers');

	$color_scheme_array = array(
		'theme-pink' => esc_html__('Default (pink)', 'transfers'),
		'theme-beige' => esc_html__('Beige', 'transfers'),
		'theme-dblue' => esc_html__('Dark blue', 'transfers'),
		'theme-dgreen' => esc_html__('Dark green', 'transfers'),
		'theme-grey' => esc_html__('Grey', 'transfers'),
		'theme-lblue' => esc_html__('Light blue', 'transfers'),
		'theme-lgreen' => esc_html__('Light green', 'transfers'),
		'theme-lime' => esc_html__('Lime', 'transfers'),
		'theme-orange' => esc_html__('Navy', 'transfers'),
		'theme-peach' => esc_html__('Peach', 'transfers'),
		'theme-purple' => esc_html__('Purple', 'transfers'),
		'theme-red' => esc_html__('Red', 'transfers'),
		'theme-teal' => esc_html__('Teal', 'transfers'),
		'theme-turquoise' => esc_html__('Turquoise', 'transfers'),
		'theme-yellow' => esc_html__('Yellow', 'transfers'),		
	);

	$pages = get_pages(); 
	$pages_array = array();
	$pages_array[0] = esc_html__('Select page', 'transfers');
	foreach ( $pages as $page ) {
		$pages_array[$page->ID] = $page->post_title;
	}
	
	$price_decimals_array = array(
		'0' => esc_html__('Zero (e.g. $200)', 'transfers'),
		'1' => esc_html__('One  (e.g. $200.0)', 'transfers'),
		'2' => esc_html__('Two (e.g. $200.00)', 'transfers'),
	);

	$options = array();

	$options[] = array(
		'name' => esc_html__( 'General Settings', 'transfers' ),
		'type' => 'heading'
	);
		
	$options[] = array(
		'name' => esc_html__('Select color scheme', 'transfers'),
		'desc' => esc_html__('Select website color scheme.', 'transfers'),
		'id' => 'color_scheme_select',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $color_scheme_array);
	
	$options[] = array(
		'name' => esc_html__('Company name', 'transfers'),
		'desc' => esc_html__('Company name displayed on the contact us page.', 'transfers'),
		'id' => 'contact_company_name',
		'std' => 'Transfers LLC',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => esc_html__('Contact address', 'transfers'),
		'desc' => esc_html__('Contact address displayed on the contact us page.', 'transfers'),
		'id' => 'contact_address',
		'std' => '1293 Delancey Street, NY',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => esc_html__('Contact address latitude', 'transfers'),
		'desc' => esc_html__('Enter your address latitude to use for contact form map', 'transfers'),
		'id' => 'contact_address_latitude',
		'std' => '49.47216',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact address longitude', 'transfers'),
		'desc' => esc_html__('Enter your address longitude to use for contact form map', 'transfers'),
		'id' => 'contact_address_longitude',
		'std' => '-123.76307',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => esc_html__('Footer copyright notice', 'transfers'),
		'desc' => esc_html__('Copyright notice in footer.', 'transfers'),
		'id' => 'copyright_footer',
		'std' => '&copy; transfers.com 2018. All rights reserved.',
		'type' => 'text');
	
	$options[] = array(
		'name' => esc_html__('Configuration Settings', 'transfers'),
		'type' => 'heading');

	$options[] = array(
		'name' => esc_html__('Google maps api key', 'transfers'),
		'desc' => esc_html__('Google maps now requires you to provide an api key when using their maps api. As a result of this you must go to their <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">site</a> and get a key. After you do, enter it below.', 'transfers'),
		'id' => 'google_maps_key',
		'std' => '',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');				

	if (function_exists('is_transfers_plugin_active') && is_transfers_plugin_active()) {		

		$options[] = array(
			'name' => esc_html__('Enable shared transfers?', 'transfers'),
			'desc' => esc_html__('If this option is checked, shared transfers are enabled. Otherwise only private (entire car) transfers are enabled.', 'transfers'),
			'id' => 'enable_shared_transfers',
			'std' => '1',
			'type' => 'checkbox');	
	
		$sort_array = array();
		$sort_array['byminute'] = esc_html__('By minute', 'transfers');
		$sort_array['byprice'] = esc_html__('By price', 'transfers');
	
		$options[] = array(
			'name' => esc_html__('Sort search results by', 'transfers'),
			'desc' => esc_html__('What do you want search results to be sorted by?', 'transfers'),
			'id' => 'search_results_sort_by',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'std'	=> '1',
			'options' => $sort_array);

		$search_times_array = array();
		$search_times_array['now'] = esc_html__('Immediate', 'transfers');
		$search_times_array['1hour'] = esc_html__('One hour from now', 'transfers');
		$search_times_array['6hours'] = esc_html__('Six hours from now', 'transfers');
		$search_times_array['12hours'] = esc_html__('Twelve hours from now', 'transfers');
		$search_times_array['1day'] = esc_html__('One day from now', 'transfers');
		$search_times_array['2days'] = esc_html__('Two days from now', 'transfers');
		$search_times_array['1week'] = esc_html__('One week from now', 'transfers');

		$options[] = array(
			'name' => esc_html__('Allowed search start times', 'transfers'),
			'desc' => esc_html__('What time do you want searches to start from? This setting can be used to prevent late bookings.', 'transfers'),
			'id' => 'allowed_search_start_time',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'std'	=> 'now',
			'options' => $search_times_array);			
	
		$slots_array = array();
		$slots_array['1'] = esc_html__('1 minute', 'transfers');
		$slots_array['5'] = esc_html__('5 minutes', 'transfers');
		$slots_array['10'] = esc_html__('10 minutes', 'transfers');
		$slots_array['30'] = esc_html__('30 minutes', 'transfers');
		$slots_array['60'] = esc_html__('60 minutes', 'transfers');
			
		$options[] = array(
			'name' => esc_html__('Transfer time slot increment', 'transfers'),
			'desc' => esc_html__('Time slot increment is used when creating availability time slots for transfers. The increment determines what values the time slot dropdown contains when creating daily, weekly, month entries.', 'transfers'),
			'id' => 'time_slot_increment',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'std'	=> '1',
			'options' => $slots_array);
			
		$hour_offset_array = array();
		$hour_offset_array['0'] = esc_html__('0 hours', 'transfers');
		$hour_offset_array['1'] = esc_html__('1 hour', 'transfers');
		$hour_offset_array['2'] = esc_html__('2 hours', 'transfers');
		$hour_offset_array['3'] = esc_html__('3 hours', 'transfers');
		$hour_offset_array['4'] = esc_html__('4 hours', 'transfers');
		$hour_offset_array['5'] = esc_html__('5 hours', 'transfers');
	
		$options[] = array(
			'name' => esc_html__('Search backwards time slot offset', 'transfers'),
			'desc' => esc_html__('When showing search results, show results that include starting times X (this offset) hours before searched-for time', 'transfers'),
			'id' => 'search_time_slot_offset',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'std'	=> '1',
			'options' => $hour_offset_array);
			
		$search_results_by_minute_array = array();
		$search_results_by_minute_array['1'] = esc_html__('1 result', 'transfers');
		$search_results_by_minute_array['2'] = esc_html__('2 results', 'transfers');
		$search_results_by_minute_array['3'] = esc_html__('3 results', 'transfers');
		$search_results_by_minute_array['4'] = esc_html__('4 results', 'transfers');
		$search_results_by_minute_array['5'] = esc_html__('5 results', 'transfers');
		$search_results_by_minute_array['6'] = esc_html__('6 results', 'transfers');
		$search_results_by_minute_array['7'] = esc_html__('7 results', 'transfers');
		$search_results_by_minute_array['8'] = esc_html__('8 results', 'transfers');
		$search_results_by_minute_array['9'] = esc_html__('9 results', 'transfers');
		$search_results_by_minute_array['10'] = esc_html__('10 results', 'transfers');
			
		$options[] = array(
			'name' => esc_html__('Search results by minute count', 'transfers'),
			'desc' => esc_html__('If displaying availabilities by minute (running availabilities) this setting determines how many of these you want to show. e.g. if transport is available every 10 minutes, and you set this value to 10, you will show 10 results from now incremenenting by 10 minutes.', 'transfers'),
			'id' => 'search_results_by_minute_count',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'std'	=> '1',
			'options' => $search_results_by_minute_array);
	
		$options[] = array(
			'name' => esc_html__('Price decimal places', 'transfers'),
			'desc' => esc_html__('Number of decimal places to show for prices', 'transfers'),
			'id' => 'price_decimal_places',
			'std' => '0',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $price_decimals_array);
			
		$options[] = array(
			'name' => esc_html__('Default currency symbol', 'transfers'),
			'desc' => esc_html__('What is your default currency symbol', 'transfers'),
			'id' => 'default_currency_symbol',
			'std' => '$',
			'class' => 'mini', //mini, tiny, small
			'type' => 'text');
	
		$options[] = array(
			'name' => esc_html__('Show currency symbol after price?', 'transfers'),
			'desc' => esc_html__('If this option is checked, currency symbol will show up after the price, instead of before (e.g. 150 $ instead of $150).', 'transfers'),
			'id' => 'show_currency_symbol_after',
			'std' => '0',
			'type' => 'checkbox');
	}
	
	$options[] = array(
		'name' => esc_html__('Enable RTL', 'transfers'),
		'desc' => esc_html__('Enable right-to-left support', 'transfers'),
		'id' => 'enable_rtl',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Show preloader', 'transfers'),
		'desc' => esc_html__('Show preloader on pages while pages are loading', 'transfers'),
		'id' => 'show_preloader',
		'std' => '1',
		'type' => 'checkbox');
	
	$options[] = array(
		'name' => esc_html__('Add captcha to forms', 'transfers'),
		'desc' => esc_html__('Add simple captcha implemented inside transfers theme to forms (login, register, book, inquire, contact etc)', 'transfers'),
		'id' => 'add_captcha_to_forms',
		'std' => '1',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Users specify password', 'transfers'),
		'desc' => esc_html__('Let users specify their password when registering', 'transfers'),
		'id' => 'let_users_set_pass',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Override wp-login.php', 'transfers'),
		'desc' => esc_html__('Override wp-login.php and use custom login, register, forgot password pages', 'transfers'),
		'id' => 'override_wp_login',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Page Settings', 'transfers'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Login page', 'transfers'),
		'desc' => esc_html__('Login page url', 'transfers'),
		'id' => 'login_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Redirect to after login', 'transfers'),
		'desc' => esc_html__('Page to redirect to after login', 'transfers'),
		'id' => 'redirect_to_after_login',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Redirect to after logout', 'transfers'),
		'desc' => esc_html__('Page to redirect to after logout', 'transfers'),
		'id' => 'redirect_to_after_logout',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Register page', 'transfers'),
		'desc' => esc_html__('Register page url', 'transfers'),
		'id' => 'register_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Reset password page', 'transfers'),
		'desc' => esc_html__('Reset password page url', 'transfers'),
		'id' => 'reset_password_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Terms &amp; conditions page url', 'transfers'),
		'desc' => esc_html__('Terms &amp; conditions page url', 'transfers'),
		'id' => 'terms_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Contact page', 'transfers'),
		'desc' => esc_html__('Contact page url', 'transfers'),
		'id' => 'contact_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Blog index sidebar position', 'transfers'),
		'desc' => esc_html__('Select the position (if any) of sidebars to appear on the blog index page of your website.', 'transfers'),
		'id' => 'blog_index_sidebar_position',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $page_sidebars);
		
	$options[] = array(
		'name' => esc_html__('Blog index sort by column', 'transfers'),
		'desc' => esc_html__('Select the column you want blog posts to be sorted in blog index.', 'transfers'),
		'id' => 'blog_index_sort_by_column',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $sort_by_columns);	
		
	$options[] = array(
		'name' => esc_html__("Blog index sort descending", 'transfers'),
		'desc' => esc_html__("Sort posts in descending order on blog index page", 'transfers'),
		'id' => 'blog_index_sort_descending',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__("Blog index show posts in grid view", 'transfers'),
		'desc' => esc_html__("Show posts in grid view on blog index page", 'transfers'),
		'id' => 'blog_index_show_grid_view',
		'std' => '0',
		'type' => 'checkbox');		
			
	if (function_exists('is_transfers_plugin_active') && is_transfers_plugin_active()) {
	
		$options[] = array(
			'name' => esc_html__('Services', 'transfers'),
			'type' => 'heading');
			
		$options[] = array(
			'name' => esc_html__("Enable 'Services'", 'transfers'),
			'desc' => esc_html__("Enable the 'Services' data type", 'transfers'),
			'id' => 'enable_services',
			'std' => '1',
			'type' => 'checkbox');		

		$default_classes = "icon-themeenergy_user\nicon-themeenergy_user-2\nicon-themeenergy_user-3\nicon-themeenergy_user-add\nicon-themeenergy_user-add-3\nicon-themeenergy_user-button\nicon-themeenergy_user-delete\nicon-themeenergy_user-delete-3\nicon-themeenergy_user-profile\nicon-themeenergy_user-remove\nicon-themeenergy_user-remove-3\nicon-themeenergy_users\nicon-themeenergy_balloons\nicon-themeenergy_bible\nicon-themeenergy_bow\nicon-themeenergy_bride-dress\nicon-themeenergy_cake\nicon-themeenergy_candle\nicon-themeenergy_candles\nicon-themeenergy_candy\nicon-themeenergy_champagne\nicon-themeenergy_chat-love\nicon-themeenergy_church\nicon-themeenergy_costume\nicon-themeenergy_diamond\nicon-themeenergy_diamond-ring\nicon-themeenergy_digital-camera\nicon-themeenergy_female\nicon-themeenergy_filming\nicon-themeenergy_flower\nicon-themeenergy_flowers\nicon-themeenergy_heart\nicon-themeenergy_heart-lock\nicon-themeenergy_heart-ring\nicon-themeenergy_hearts\nicon-themeenergy_hearts-2\nicon-themeenergy_hearts-3\nicon-themeenergy_hearts-4\nicon-themeenergy_i-love-you\nicon-themeenergy_love-birds\nicon-themeenergy_love-bow\nicon-themeenergy_love-cake\nicon-themeenergy_love-coffee\nicon-themeenergy_love-drinks\nicon-themeenergy_love-flower\nicon-themeenergy_love-heart\nicon-themeenergy_love-key\nicon-themeenergy_love-message\nicon-themeenergy_love-message-2\nicon-themeenergy_love-shopping\nicon-themeenergy_love-target\nicon-themeenergy_male\nicon-themeenergy_marriage-date\nicon-themeenergy_meeting-point\nicon-themeenergy_neclare\nicon-themeenergy_piano\nicon-themeenergy_present\nicon-themeenergy_single-balloon\nicon-themeenergy_st-valentine\nicon-themeenergy_wedding-letter\nicon-themeenergy_wine-bottle\nicon-themeenergy_wine-glass-love\nicon-themeenergy_celsius-symbol\nicon-themeenergy_cloud\nicon-themeenergy_clouds-lightning\nicon-themeenergy_cloudy\nicon-themeenergy_cloudy-lightning\nicon-themeenergy_cloudy-rain\nicon-themeenergy_cloudy-snow-rain\nicon-themeenergy_cloudy-sun\nicon-themeenergy_east\nicon-themeenergy_fahrenheit-symbol\nicon-themeenergy_freezing-temperature\nicon-themeenergy_half-moon\nicon-themeenergy_half-moon-rise\nicon-themeenergy_half-moon-set\nicon-themeenergy_half-sun\nicon-themeenergy_hurricane\nicon-themeenergy_hurricane-2\nicon-themeenergy_ice-flake\nicon-themeenergy_lake\nicon-themeenergy_lightning\nicon-themeenergy_moon\nicon-themeenergy_moon-cloud-snow\nicon-themeenergy_moonset\nicon-themeenergy_moonset-2\nicon-themeenergy_moon-stars\nicon-themeenergy_night-clouds-moon\nicon-themeenergy_north\nicon-themeenergy_northeast\nicon-themeenergy_northwest\nicon-themeenergy_partly-suny\nicon-themeenergy_planet\nicon-themeenergy_rain\nicon-themeenergy_rain-3\nicon-themeenergy_rainbow\nicon-themeenergy_rain-drop\nicon-themeenergy_rain-drops\nicon-themeenergy_rainy-day\nicon-themeenergy_rainy-night\nicon-themeenergy_sea\nicon-themeenergy_sleeping-moon\nicon-themeenergy_snow-clouds\nicon-themeenergy_snow-clouds-2\nicon-themeenergy_snow-flake\nicon-themeenergy_snow-flakes\nicon-themeenergy_snow-water-flakes\nicon-themeenergy_south\nicon-themeenergy_southeast\nicon-themeenergy_southwest\nicon-themeenergy_sun\nicon-themeenergy_sunny-coulds-snow\nicon-themeenergy_sunrise-3\nicon-themeenergy_sunrise-4\nicon-themeenergy_sunset\nicon-themeenergy_sunset-2\nicon-themeenergy_sunset-3\nicon-themeenergy_sunset-4\nicon-themeenergy_temperature-drop\nicon-themeenergy_temperature-drop-2\nicon-themeenergy_temperature-hot\nicon-themeenergy_temperature-increase\nicon-themeenergy_temperature-raising\nicon-themeenergy_thermometer\nicon-themeenergy_thermometer-2\nicon-themeenergy_thermometer-3\nicon-themeenergy_umbrella\nicon-themeenergy_umbrella-rain\nicon-themeenergy_umbrella-snow\nicon-themeenergy_west\nicon-themeenergy_wind\nicon-themeenergy_wind-cloudy\nicon-themeenergy_apple\nicon-themeenergy_apple-2\nicon-themeenergy_avocado\nicon-themeenergy_banana\nicon-themeenergy_basilico\nicon-themeenergy_cabbage\nicon-themeenergy_carrot\nicon-themeenergy_cherries\nicon-themeenergy_chestnuts\nicon-themeenergy_chilli-pepper\nicon-themeenergy_corn\nicon-themeenergy_eggplant\nicon-themeenergy_garlic\nicon-themeenergy_grapes\nicon-themeenergy_hazelnut\nicon-themeenergy_lemon\nicon-themeenergy_lemon-2\nicon-themeenergy_lettuce\nicon-themeenergy_onion\nicon-themeenergy_onions\nicon-themeenergy_peach\nicon-themeenergy_pear\nicon-themeenergy_pear-2\nicon-themeenergy_peas\nicon-themeenergy_pepper\nicon-themeenergy_pineapple\nicon-themeenergy_plum\nicon-themeenergy_pomegrade\nicon-themeenergy_potato\nicon-themeenergy_radish\nicon-themeenergy_strawberry\nicon-themeenergy_tomato\nicon-themeenergy_watermelon\nicon-themeenergy_watermelon-2\nicon-themeenergy_watermelon-slice\nicon-themeenergy_airplane\nicon-themeenergy_anchor\nicon-themeenergy_ancient-column\nicon-themeenergy_bed\nicon-themeenergy_binoculars\nicon-themeenergy_boat\nicon-themeenergy_camcorder\nicon-themeenergy_cigarette\nicon-themeenergy_cigarette-2\nicon-themeenergy_clothes-hanger-1\nicon-themeenergy_clothes-hanger-2\nicon-themeenergy_compass\nicon-themeenergy_credit-card\nicon-themeenergy_credit-card-2\nicon-themeenergy_credit-cards\nicon-themeenergy_digital-camera2\nicon-themeenergy_door-hanger\nicon-themeenergy_door-hanger-2\nicon-themeenergy_door-hanger-3\nicon-themeenergy_earth\nicon-themeenergy_earth-2\nicon-themeenergy_fish\nicon-themeenergy_flag\nicon-themeenergy_hot-air-balloon\nicon-themeenergy_hotel\nicon-themeenergy_ice-cream\nicon-themeenergy_id-card\nicon-themeenergy_lighthouse\nicon-themeenergy_map\nicon-themeenergy_map-route\nicon-themeenergy_mountains\nicon-themeenergy_passport\nicon-themeenergy_passport-2\nicon-themeenergy_pin\nicon-themeenergy_pin-2\nicon-themeenergy_pin-3\nicon-themeenergy_pin-4\nicon-themeenergy_pin-5\nicon-themeenergy_pin-6\nicon-themeenergy_pin-7\nicon-themeenergy_pin-8\nicon-themeenergy_pin-9\nicon-themeenergy_pin-10\nicon-themeenergy_pin-11\nicon-themeenergy_pin-on-map\nicon-themeenergy_place-on-map\nicon-themeenergy_road-sign\nicon-themeenergy_route-signs\nicon-themeenergy_sea-ball\nicon-themeenergy_shipweel\nicon-themeenergy_shower\nicon-themeenergy_ski-lift\nicon-themeenergy_slippers\nicon-themeenergy_sun2\nicon-themeenergy_sun-2\nicon-themeenergy_sunglasses\nicon-themeenergy_sun-umbrella-2\nicon-themeenergy_sun-umbrella-3\nicon-themeenergy_swimming-pool\nicon-themeenergy_tent\nicon-themeenergy_ticket\nicon-themeenergy_tickets\nicon-themeenergy_travel-bag\nicon-themeenergy_travel-bag-2\nicon-themeenergy_underwater-goggles\nicon-themeenergy_airplane2\nicon-themeenergy_airplane-1\nicon-themeenergy_airplane-2\nicon-themeenergy_airplane-3\nicon-themeenergy_bicycle\nicon-themeenergy_bike\nicon-themeenergy_bus\nicon-themeenergy_car-1\nicon-themeenergy_car-2\nicon-themeenergy_cargo-ship\nicon-themeenergy_city-train\nicon-themeenergy_different-ways\nicon-themeenergy_electric-bike\nicon-themeenergy_empty-gas-gauge\nicon-themeenergy_gas-gauge\nicon-themeenergy_gas-pump\nicon-themeenergy_gas-pump-2\nicon-themeenergy_gauge\nicon-themeenergy_helicopter\nicon-themeenergy_hot-air-balloon2\nicon-themeenergy_hot-air-balloon-1\nicon-themeenergy_intersection\nicon-themeenergy_learn-driving\nicon-themeenergy_military-suv\nicon-themeenergy_military-truck\nicon-themeenergy_old-plane\nicon-themeenergy_road\nicon-themeenergy_ship\nicon-themeenergy_small-bike\nicon-themeenergy_steering-wheel\nicon-themeenergy_suv\nicon-themeenergy_tractor\nicon-themeenergy_traffic-light\nicon-themeenergy_traffic-light-1\nicon-themeenergy_traffic-light-2\nicon-themeenergy_trailer\nicon-themeenergy_train\nicon-themeenergy_truck\nicon-themeenergy_truck-1\nicon-themeenergy_truck-2\nicon-themeenergy_aerobic-trimmer\nicon-themeenergy_badminton\nicon-themeenergy_ball\nicon-themeenergy_baseball\nicon-themeenergy_baseball-2\nicon-themeenergy_baseball-arena\nicon-themeenergy_basketball\nicon-themeenergy_basketball-arena\nicon-themeenergy_basketball-cup\nicon-themeenergy_basketball-table\nicon-themeenergy_beach-volley\nicon-themeenergy_billiard\nicon-themeenergy_billiard-2\nicon-themeenergy_billiard-8-ball\nicon-themeenergy_billiard-balls\nicon-themeenergy_billiard-table\nicon-themeenergy_bowling\nicon-themeenergy_bowling-2\nicon-themeenergy_bowling-3\nicon-themeenergy_box-glove\nicon-themeenergy_casino-tip\nicon-themeenergy_casino-tips\nicon-themeenergy_chess\nicon-themeenergy_chess-bishop\nicon-themeenergy_chess-king\nicon-themeenergy_chess-knight\nicon-themeenergy_chess-pawn\nicon-themeenergy_chess-queen\nicon-themeenergy_chess-table\nicon-themeenergy_clocks-2\nicon-themeenergy_cup\nicon-themeenergy_cup-2\nicon-themeenergy_cup-3\nicon-themeenergy_cup-4\nicon-themeenergy_dice\nicon-themeenergy_dice-2\nicon-themeenergy_fencing\nicon-themeenergy_first-place\nicon-themeenergy_flag2\nicon-themeenergy_flag-2\nicon-themeenergy_flag-3\nicon-themeenergy_football-arena\nicon-themeenergy_football-ball\nicon-themeenergy_football-goalpost\nicon-themeenergy_golf-2\nicon-themeenergy_golf-4\nicon-themeenergy_golf-ball\nicon-themeenergy_golf-ball-3\nicon-themeenergy_hockey\nicon-themeenergy_hockey-ball\nicon-themeenergy_hockey-sticks\nicon-themeenergy_king\nicon-themeenergy_medal\nicon-themeenergy_medal-2\nicon-themeenergy_medal-3\nicon-themeenergy_medal-4\nicon-themeenergy_medal-5\nicon-themeenergy_medal-6\nicon-themeenergy_medal-7\nicon-themeenergy_medal-8\nicon-themeenergy_medal-first-place\nicon-themeenergy_medal-second-place\nicon-themeenergy_medal-third-place\nicon-themeenergy_ping-pong\nicon-themeenergy_podium\nicon-themeenergy_poker-card-1\nicon-themeenergy_poker-card-2\nicon-themeenergy_praise-card\nicon-themeenergy_roulette-wheel\nicon-themeenergy_rugby\nicon-themeenergy_score-table\nicon-themeenergy_score-table-2\nicon-themeenergy_sharpshooting\nicon-themeenergy_skateboard\nicon-themeenergy_swimming\nicon-themeenergy_target-3\nicon-themeenergy_tennis-2\nicon-themeenergy_tennis-3\nicon-themeenergy_tennis-arena\nicon-themeenergy_tennis-ball\nicon-themeenergy_tennis-ball-2\nicon-themeenergy_ticket2\nicon-themeenergy_volleyball\nicon-themeenergy_weight-lifting\nicon-themeenergy_whistle\nicon-themeenergy_astronaut-helmet\nicon-themeenergy_astronaut-suit\nicon-themeenergy_big-bang\nicon-themeenergy_black-hole\nicon-themeenergy_falling-star\nicon-themeenergy_galaxy\nicon-themeenergy_meteor-crash\nicon-themeenergy_meteorite\nicon-themeenergy_milky-way\nicon-themeenergy_moon2\nicon-themeenergy_moon-landing\nicon-themeenergy_observatory\nicon-themeenergy_observatory-2\nicon-themeenergy_planet2\nicon-themeenergy_planet-2\nicon-themeenergy_planet-stars\nicon-themeenergy_satellite\nicon-themeenergy_satellite-dish\nicon-themeenergy_space-capsule\nicon-themeenergy_space-robot\nicon-themeenergy_spaceship\nicon-themeenergy_spaceship-2\nicon-themeenergy_space-vehicle\nicon-themeenergy_stars\nicon-themeenergy_telescope\nicon-themeenergy_ufo-face\nicon-themeenergy_ufo-spaceship\nicon-themeenergy_ufo-spaceship-2\nicon-themeenergy_ufo-takeoff\nicon-themeenergy_universe\nicon-themeenergy_abs\nicon-themeenergy_add-engine-oil\nicon-themeenergy_battery\nicon-themeenergy_brakes\nicon-themeenergy_broken-engine\nicon-themeenergy_broken-engine-2\nicon-themeenergy_car-pedals\nicon-themeenergy_check-engine\nicon-themeenergy_clean-window\nicon-themeenergy_cooling-fan\nicon-themeenergy_engine\nicon-themeenergy_engine-gears\nicon-themeenergy_engine-oil\nicon-themeenergy_engine-pistons\nicon-themeenergy_engine-service\nicon-themeenergy_engine-warning\nicon-themeenergy_eps\nicon-themeenergy_flash-lights\nicon-themeenergy_front-cracked-glass\nicon-themeenergy_front-glass\nicon-themeenergy_fuel-small-tank\nicon-themeenergy_gear\nicon-themeenergy_gear-2\nicon-themeenergy_gear-3\nicon-themeenergy_gear-4\nicon-themeenergy_gear-5\nicon-themeenergy_gears\nicon-themeenergy_gears-2\nicon-themeenergy_heat--engine\nicon-themeenergy_light-bulb-on\nicon-themeenergy_lights-off\nicon-themeenergy_lights-on\nicon-themeenergy_oil--engine\nicon-themeenergy_open-doors\nicon-themeenergy_parking-brake\nicon-themeenergy_parking-on\nicon-themeenergy_piston-engine\nicon-themeenergy_power-engine\nicon-themeenergy_power-engine-2\nicon-themeenergy_side-window\nicon-themeenergy_side-window-close\nicon-themeenergy_side-window-open\nicon-themeenergy_steering-wheel2\nicon-themeenergy_temperature\nicon-themeenergy_transmission\nicon-themeenergy_transmission-2\nicon-themeenergy_turbo\nicon-themeenergy_warning-triangle\nicon-themeenergy_wheel\nicon-themeenergy_window-hot-air\nicon-themeenergy_add-hyperlink\nicon-themeenergy_bug\nicon-themeenergy_campaign-research\nicon-themeenergy_check\nicon-themeenergy_check-list\nicon-themeenergy_click-target\nicon-themeenergy_cloud2\nicon-themeenergy_cloud-links\nicon-themeenergy_cloud-stats\nicon-themeenergy_computer-stats\nicon-themeenergy_cup2\nicon-themeenergy_delete-hyperlink\nicon-themeenergy_email-marketing\nicon-themeenergy_favorite-stats\nicon-themeenergy_high-rankings\nicon-themeenergy_hyperlink\nicon-themeenergy_keyword-search\nicon-themeenergy_keyword-search-2\nicon-themeenergy_magic-trick\nicon-themeenergy_medal2\nicon-themeenergy_niche\nicon-themeenergy_page-search\nicon-themeenergy_page-settings\nicon-themeenergy_pie-chart\nicon-themeenergy_pie-charts-3\nicon-themeenergy_puzzle\nicon-themeenergy_pyramid-rankings\nicon-themeenergy_rank-1\nicon-themeenergy_remove-hyperlink\nicon-themeenergy_search\nicon-themeenergy_search-completed\nicon-themeenergy_search-spy\nicon-themeenergy_servers\nicon-themeenergy_settings\nicon-themeenergy_share\nicon-themeenergy_statistics-2\nicon-themeenergy_statistics-3\nicon-themeenergy_stats\nicon-themeenergy_support\nicon-themeenergy_tags\nicon-themeenergy_target\nicon-themeenergy_target-money\nicon-themeenergy_target-money-campaign\nicon-themeenergy_top-rankings\nicon-themeenergy_web-multimedia\nicon-themeenergy_web-optimization\nicon-themeenergy_web-page-settings\nicon-themeenergy_web-settings\nicon-themeenergy_website-code\nicon-themeenergy_xls-extract\nicon-themeenergy_alarm\nicon-themeenergy_alarm-2\nicon-themeenergy_brick-wall\nicon-themeenergy_bullet\nicon-themeenergy_check-point\nicon-themeenergy_door-alarm\nicon-themeenergy_enter-pin\nicon-themeenergy_eye\nicon-themeenergy_fingerprint\nicon-themeenergy_fingerprint-detection\nicon-themeenergy_fingerprint-symbol\nicon-themeenergy_fire-extinguisher\nicon-themeenergy_handcuffs\nicon-themeenergy_jail\nicon-themeenergy_keyhole\nicon-themeenergy_keylock\nicon-themeenergy_lockpad\nicon-themeenergy_lockpad-2\nicon-themeenergy_lockpad-3\nicon-themeenergy_lock-unlock\nicon-themeenergy_money-box\nicon-themeenergy_password\nicon-themeenergy_pin2\nicon-themeenergy_safe-shield\nicon-themeenergy_safe-shield-2\nicon-themeenergy_safe-shield-3\nicon-themeenergy_safe-shield-4\nicon-themeenergy_safe-shield-5\nicon-themeenergy_safe-shield-6\nicon-themeenergy_safe-shield-confirm\nicon-themeenergy_safe-shield-danger\nicon-themeenergy_safe-tower\nicon-themeenergy_security-camera\nicon-themeenergy_security-camera-2\nicon-themeenergy_sheriff-medal\nicon-themeenergy_sirine\nicon-themeenergy_spy-watch\nicon-themeenergy_target2\nicon-themeenergy_target-2\nicon-themeenergy_thief\nicon-themeenergy_3d-box\nicon-themeenergy_astronaut\nicon-themeenergy_atom\nicon-themeenergy_atom-2\nicon-themeenergy_biohazard\nicon-themeenergy_bio-hazard\nicon-themeenergy_bomb\nicon-themeenergy_bulb\nicon-themeenergy_candle2\nicon-themeenergy_chemical\nicon-themeenergy_chemical-2\nicon-themeenergy_danger\nicon-themeenergy_dna\nicon-themeenergy_dna-2\nicon-themeenergy_galaxy2\nicon-themeenergy_injection\nicon-themeenergy_laboratory\nicon-themeenergy_magnets\nicon-themeenergy_medicine\nicon-themeenergy_meteorite2\nicon-themeenergy_microscope\nicon-themeenergy_nanosomes\nicon-themeenergy_observatory2\nicon-themeenergy_physics\nicon-themeenergy_planet-earth\nicon-themeenergy_planets\nicon-themeenergy_robot\nicon-themeenergy_satellite2\nicon-themeenergy_science-book\nicon-themeenergy_signal\nicon-themeenergy_space-ship\nicon-themeenergy_spaceship-22\nicon-themeenergy_space-vehicle2\nicon-themeenergy_stars2\nicon-themeenergy_synthesis\nicon-themeenergy_telescope2\nicon-themeenergy_temperature2\nicon-themeenergy_test-drop\nicon-themeenergy_test-tube\nicon-themeenergy_test-tube-2\nicon-themeenergy_time\nicon-themeenergy_ufo\nicon-themeenergy_ufo-spaceship2\nicon-themeenergy_virus\nicon-themeenergy_virus-search\nicon-themeenergy_add-camera\nicon-themeenergy_all-points\nicon-themeenergy_aperture\nicon-themeenergy_bulb-white-balance\nicon-themeenergy_camera-auto-focusing\nicon-themeenergy_camera-bag\nicon-themeenergy_camera-connected\nicon-themeenergy_camera-flash\nicon-themeenergy_center-weighted-metering\nicon-themeenergy_delete\nicon-themeenergy_delete-camera\nicon-themeenergy_diffuser-umbrella\nicon-themeenergy_digital-camera3\nicon-themeenergy_digital-camera-2\nicon-themeenergy_digital-camera-3\nicon-themeenergy_exposure-2\nicon-themeenergy_exposure-compensation\nicon-themeenergy_exposure-control\nicon-themeenergy_face-detection\nicon-themeenergy_favorite-camera\nicon-themeenergy_favorite-camera-2\nicon-themeenergy_fiew-points\nicon-themeenergy_film\nicon-themeenergy_flash\nicon-themeenergy_flash-battery\nicon-themeenergy_flash-fire\nicon-themeenergy_focus-area\nicon-themeenergy_full-battery\nicon-themeenergy_image\nicon-themeenergy_image-2\nicon-themeenergy_image-add\nicon-themeenergy_image-confirm\nicon-themeenergy_image-copy\nicon-themeenergy_image-crop\nicon-themeenergy_image-delete\nicon-themeenergy_image-element\nicon-themeenergy_image-favorites\nicon-themeenergy_image-favorites-2\nicon-themeenergy_image-files\nicon-themeenergy_image-paste\nicon-themeenergy_image-remove\nicon-themeenergy_images\nicon-themeenergy_large-aperture\nicon-themeenergy_lens\nicon-themeenergy_low-battery\nicon-themeenergy_matrix-metering\nicon-themeenergy_micro-sd\nicon-themeenergy_move-camera\nicon-themeenergy_move-camera-2\nicon-themeenergy_no-flash\nicon-themeenergy_octagon-softbox\nicon-themeenergy_old-camera\nicon-themeenergy_old-camera-2\nicon-themeenergy_old-camera-3\nicon-themeenergy_on-flash\nicon-themeenergy_pictures-2\nicon-themeenergy_place-on-map2\nicon-themeenergy_red-eye\nicon-themeenergy_remove-camera\nicon-themeenergy_retro-camera\nicon-themeenergy_rgb\nicon-themeenergy_rule-of-thirds\nicon-themeenergy_sd-card\nicon-themeenergy_self-timer\nicon-themeenergy_settings2\nicon-themeenergy_shade-white-balance\nicon-themeenergy_single-point\nicon-themeenergy_small-aperture\nicon-themeenergy_softbox\nicon-themeenergy_spot-metering\nicon-themeenergy_sunlight-white-balance\nicon-themeenergy_tripod\nicon-themeenergy_tripod-2\nicon-themeenergy_waterproof-camera\nicon-themeenergy_white-balance-point\nicon-themeenergy_wireless-camera\nicon-themeenergy_wireless-controller\nicon-themeenergy_wireless-controller-2\nicon-themeenergy_zoom-in\nicon-themeenergy_zoum-out\nicon-themeenergy_add-call\nicon-themeenergy_add-phone\nicon-themeenergy_call\nicon-themeenergy_call-speaker\nicon-themeenergy_call-symbol\nicon-themeenergy_call-symbol-2\nicon-themeenergy_delete-phone\nicon-themeenergy_drop-call\nicon-themeenergy_full-signal\nicon-themeenergy_hold-call\nicon-themeenergy_incoming-call-2\nicon-themeenergy_incoming-calls\nicon-themeenergy_incoming-outgoing-calls\nicon-themeenergy_keypad\nicon-themeenergy_lost-call-2\nicon-themeenergy_lost-call-3\nicon-themeenergy_lost--calls\nicon-themeenergy_low-signal\nicon-themeenergy_medium-signal\nicon-themeenergy_mic\nicon-themeenergy_miss-call\nicon-themeenergy_mute-mic\nicon-themeenergy_no-signal\nicon-themeenergy_outgoing-calls\nicon-themeenergy_phone-battery\nicon-themeenergy_phone-battery-2\nicon-themeenergy_phone-battery-empty\nicon-themeenergy_phone-battery-full\nicon-themeenergy_phone-battery-half\nicon-themeenergy_phone-battery-pin\nicon-themeenergy_phone-battery-share\nicon-themeenergy_phone-charging\nicon-themeenergy_phone-chat\nicon-themeenergy_phone-chat-2\nicon-themeenergy_phone-chat-3\nicon-themeenergy_phone-cloud\nicon-themeenergy_phone-confirm\nicon-themeenergy_phone-contacts\nicon-themeenergy_phone-favorites\nicon-themeenergy_phone-favorites-2\nicon-themeenergy_phone-incoming\nicon-themeenergy_phone-loading\nicon-themeenergy_phone-loading-2\nicon-themeenergy_phone-lock\nicon-themeenergy_phone-messages\nicon-themeenergy_phone-multimedia\nicon-themeenergy_phone-music\nicon-themeenergy_phone-outgoing\nicon-themeenergy_phone-password\nicon-themeenergy_phone-phone-call\nicon-themeenergy_phone-search\nicon-themeenergy_phone-send-message\nicon-themeenergy_phone-settings\nicon-themeenergy_phone-shopping\nicon-themeenergy_phone-signal\nicon-themeenergy_phone-signal-2\nicon-themeenergy_phone-sound\nicon-themeenergy_phone-vibrating\nicon-themeenergy_phone-web\nicon-themeenergy_record-mic\nicon-themeenergy_remove-call\nicon-themeenergy_remove-phone\nicon-themeenergy_smartphone\nicon-themeenergy_smartphone-2\nicon-themeenergy_very-low-signal\nicon-themeenergy_add-database\nicon-themeenergy_antena\nicon-themeenergy_antena-2\nicon-themeenergy_cloud3\nicon-themeenergy_cloud-add\nicon-themeenergy_cloud-confirm\nicon-themeenergy_cloud-delete\nicon-themeenergy_cloud-download\nicon-themeenergy_cloud-favorites\nicon-themeenergy_cloud-loading\nicon-themeenergy_cloud-lock\nicon-themeenergy_cloud-network\nicon-themeenergy_cloud-network-2\nicon-themeenergy_cloud-refresh\nicon-themeenergy_cloud-remove\nicon-themeenergy_cloud-safety\nicon-themeenergy_cloud-upload\nicon-themeenergy_connected\nicon-themeenergy_connection-points\nicon-themeenergy_connection-ports\nicon-themeenergy_connection-ports-2\nicon-themeenergy_connection-types\nicon-themeenergy_connection-types-2\nicon-themeenergy_connection-types-3\nicon-themeenergy_database\nicon-themeenergy_database-center\nicon-themeenergy_database-confrim\nicon-themeenergy_delete-database\nicon-themeenergy_earth2\nicon-themeenergy_hard-disc\nicon-themeenergy_hard-disc-cloud\nicon-themeenergy_hard-disc-lock\nicon-themeenergy_hard-disc-multimedia\nicon-themeenergy_hard-disc-safety\nicon-themeenergy_home-network\nicon-themeenergy_hub\nicon-themeenergy_hub-connection\nicon-themeenergy_infranet\nicon-themeenergy_lock-database\nicon-themeenergy_lock-server\nicon-themeenergy_network-computers\nicon-themeenergy_network-connections\nicon-themeenergy_network-connections-2\nicon-themeenergy_network-disconnect\nicon-themeenergy_network-lock\nicon-themeenergy_network-routers\nicon-themeenergy_network-routers-2\nicon-themeenergy_network-server\nicon-themeenergy_network-servers\nicon-themeenergy_no-wifi\nicon-themeenergy_phone-cable\nicon-themeenergy_phone-jack\nicon-themeenergy_remove-database\nicon-themeenergy_router\nicon-themeenergy_router-2\nicon-themeenergy_safe-database\nicon-themeenergy_safe-network\nicon-themeenergy_safe-server\nicon-themeenergy_search-hard-disc\nicon-themeenergy_search-server\nicon-themeenergy_server-2\nicon-themeenergy_server-3\nicon-themeenergy_share2\nicon-themeenergy_signal2\nicon-themeenergy_signal-2\nicon-themeenergy_signal-3\nicon-themeenergy_signal-4\nicon-themeenergy_signal-5\nicon-themeenergy_signal-lost\nicon-themeenergy_signal-wave\nicon-themeenergy_wifi\nicon-themeenergy_wifi-2\nicon-themeenergy_wifi-3\nicon-themeenergy_wireless-cloud\nicon-themeenergy_wireless-hard-disc\nicon-themeenergy_air\nicon-themeenergy_butterfly\nicon-themeenergy_cactus\nicon-themeenergy_deep-water\nicon-themeenergy_easter-bug\nicon-themeenergy_fire\nicon-themeenergy_flower2\nicon-themeenergy_flower-2\nicon-themeenergy_flower-3\nicon-themeenergy_grass\nicon-themeenergy_honey\nicon-themeenergy_hurricane2\nicon-themeenergy_leaf\nicon-themeenergy_leaf-2\nicon-themeenergy_leaf-3\nicon-themeenergy_leaf-4\nicon-themeenergy_mushroom\nicon-themeenergy_nature\nicon-themeenergy_plant\nicon-themeenergy_plant-2\nicon-themeenergy_sea2\nicon-themeenergy_sun3\nicon-themeenergy_sunset2\nicon-themeenergy_tree\nicon-themeenergy_tree-2\nicon-themeenergy_tree-3\nicon-themeenergy_tree-4\nicon-themeenergy_tree-5\nicon-themeenergy_tree-6\nicon-themeenergy_tree-7\nicon-themeenergy_tree-8\nicon-themeenergy_tree-9\nicon-themeenergy_volcano\nicon-themeenergy_water-drop\nicon-themeenergy_water-plant\nicon-themeenergy_add-headphones\nicon-themeenergy_add-mic\nicon-themeenergy_add-sound-folder\nicon-themeenergy_antena2\nicon-themeenergy_app-settings\nicon-themeenergy_app-sound-settings\nicon-themeenergy_audio-jack\nicon-themeenergy_backward-button\nicon-themeenergy_burn-disc\nicon-themeenergy_cassette\nicon-themeenergy_confirm-mic\nicon-themeenergy_delete-sound-folder\nicon-themeenergy_digital-sound\nicon-themeenergy_disc\nicon-themeenergy_disc-cover\nicon-themeenergy_disc-cover-2\nicon-themeenergy_eject\nicon-themeenergy_expand\nicon-themeenergy_fast-backward\nicon-themeenergy_fast-forward\nicon-themeenergy_favorites\nicon-themeenergy_forward-button\nicon-themeenergy_guitar\nicon-themeenergy_headphones\nicon-themeenergy_headphones-connected\nicon-themeenergy_headphones-mic\nicon-themeenergy_knob\nicon-themeenergy_lyrics\nicon-themeenergy_memory-card\nicon-themeenergy_mic2\nicon-themeenergy_microphone\nicon-themeenergy_minimize\nicon-themeenergy_mp3-player\nicon-themeenergy_multimedia-player\nicon-themeenergy_music-app-store\nicon-themeenergy_note\nicon-themeenergy_note-2\nicon-themeenergy_note-3\nicon-themeenergy_note-4\nicon-themeenergy_pause-button\nicon-themeenergy_piano2\nicon-themeenergy_pick-up-vinyl\nicon-themeenergy_pin3\nicon-themeenergy_play-button\nicon-themeenergy_pop-up-windows\nicon-themeenergy_power-off\nicon-themeenergy_radio\nicon-themeenergy_record-button\nicon-themeenergy_remove-headphones\nicon-themeenergy_remove-headphones-2\nicon-themeenergy_remove-mic\nicon-themeenergy_remove-mic-2\nicon-themeenergy_repeat\nicon-themeenergy_search2\nicon-themeenergy_search-sound-folder\nicon-themeenergy_share-audio\nicon-themeenergy_shuffle\nicon-themeenergy_shuffle-2\nicon-themeenergy_song-favorites\nicon-themeenergy_song-file\nicon-themeenergy_song-info\nicon-themeenergy_song-lyrics\nicon-themeenergy_song-lyrics-2\nicon-themeenergy_sound-bass\nicon-themeenergy_sound-control\nicon-themeenergy_sound-favorites\nicon-themeenergy_sound-folder\nicon-themeenergy_sound-folder-2\nicon-themeenergy_sound-waves\nicon-themeenergy_speaker\nicon-themeenergy_speaker-1\nicon-themeenergy_speaker-2\nicon-themeenergy_speaker-volume-1\nicon-themeenergy_speaker-volume-2\nicon-themeenergy_speaker-volume-3\nicon-themeenergy_speaker-volume-decrease\nicon-themeenergy_speaker-volume-increase\nicon-themeenergy_speaker-volume-mute\nicon-themeenergy_stop-button\nicon-themeenergy_top-favorites\nicon-themeenergy_ambulance\nicon-themeenergy_blood-drop\nicon-themeenergy_blood-drop-2\nicon-themeenergy_blood-drop-3\nicon-themeenergy_cross\nicon-themeenergy_cross-2\nicon-themeenergy_cross-3\nicon-themeenergy_diagnose-heart\nicon-themeenergy_doctor-bag\nicon-themeenergy_doctor-notes\nicon-themeenergy_drug\nicon-themeenergy_gauze\nicon-themeenergy_heart2\nicon-themeenergy_heart-cross\nicon-themeenergy_helicopter-passageway\nicon-themeenergy_home-care\nicon-themeenergy_hospital-bed\nicon-themeenergy_hospital-bed-2\nicon-themeenergy_hospital-point\nicon-themeenergy_medical-note\nicon-themeenergy_medical-sign\nicon-themeenergy_medicine-drug\nicon-themeenergy_nurse\nicon-themeenergy_nurse-hat\nicon-themeenergy_patient-wheelchair\nicon-themeenergy_pill\nicon-themeenergy_pills\nicon-themeenergy_plaster\nicon-themeenergy_plaster-2\nicon-themeenergy_pulse\nicon-themeenergy_pulse-histogram\nicon-themeenergy_ribbon\nicon-themeenergy_sterthooscope\nicon-themeenergy_syringe\nicon-themeenergy_temperature3\nicon-themeenergy_add-mail\nicon-themeenergy_attach\nicon-themeenergy_attach-mail\nicon-themeenergy_delete-mail\nicon-themeenergy_empty-inbox\nicon-themeenergy_favorite-mail\nicon-themeenergy_favorite-mail-3\nicon-themeenergy_inbox\nicon-themeenergy_inbox-add\nicon-themeenergy_inbox-confirm\nicon-themeenergy_inbox-cut\nicon-themeenergy_inbox-delete\nicon-themeenergy_inbox-income\nicon-themeenergy_inbox-letter\nicon-themeenergy_inbox-move\nicon-themeenergy_inbox-outcome\nicon-themeenergy_inbox-receive\nicon-themeenergy_inbox-remove\nicon-themeenergy_inbox-send\nicon-themeenergy_letter-details\nicon-themeenergy_lock-mail\nicon-themeenergy_mail\nicon-themeenergy_mail-2\nicon-themeenergy_mail-address\nicon-themeenergy_mail-confirm\nicon-themeenergy_mail-cut\nicon-themeenergy_mail-delete\nicon-themeenergy_mail-favorites\nicon-themeenergy_mail-images\nicon-themeenergy_mail-letter\nicon-themeenergy_mail-move\nicon-themeenergy_mail-receive\nicon-themeenergy_mail-send\nicon-themeenergy_mail-support\nicon-themeenergy_mail-unknown\nicon-themeenergy_mail-videos\nicon-themeenergy_post-box\nicon-themeenergy_post-box-2\nicon-themeenergy_post-box-3\nicon-themeenergy_print-mail\nicon-themeenergy_read-mail\nicon-themeenergy_receive-mail-2\nicon-themeenergy_receive-mail-3\nicon-themeenergy_remove-mail\nicon-themeenergy_search-mail\nicon-themeenergy_secure-mail\nicon-themeenergy_send-mail\nicon-themeenergy_send-mail-2\nicon-themeenergy_send-mail-3\nicon-themeenergy_spam-mail\nicon-themeenergy_stamp\nicon-themeenergy_stamp-2\nicon-themeenergy_stamp-3\nicon-themeenergy_stamp-4\nicon-themeenergy_urgent-mail\nicon-themeenergy_30c\nicon-themeenergy_40c\nicon-themeenergy_50c\nicon-themeenergy_60c\nicon-themeenergy_70c\nicon-themeenergy_95c\nicon-themeenergy_any-solvent\nicon-themeenergy_any-solvent-except-tetrachlorethylene\nicon-themeenergy_bleach-if-needed\nicon-themeenergy_clean-clothes\nicon-themeenergy_detergent\nicon-themeenergy_dirty-clothes\nicon-themeenergy_do-not-bleach\nicon-themeenergy_do-not-dryclean\nicon-themeenergy_do-not-tumble-dry\nicon-themeenergy_do-not-wash\nicon-themeenergy_do-not-wring\nicon-themeenergy_drip-dry\nicon-themeenergy_dry\nicon-themeenergy_dryclean\nicon-themeenergy_dry-flat\nicon-themeenergy_dry-in-the-shade\nicon-themeenergy_dry-normal-high-heat\nicon-themeenergy_dry-normal-low-heat\nicon-themeenergy_dry-normal-medium-heat\nicon-themeenergy_gentle-delicate\nicon-themeenergy_hand-wash\nicon-themeenergy_hang-to-dry\nicon-themeenergy_iron\nicon-themeenergy_iron-max-no-steam\nicon-themeenergy_iron-max-steam\nicon-themeenergy_iron-max-temp-110\nicon-themeenergy_iron-max-temp-150\nicon-themeenergy_iron-max-temp-200\nicon-themeenergy_l\nicon-themeenergy_low-heat\nicon-themeenergy_m\nicon-themeenergy_machine-wash\nicon-themeenergy_machine-wash-gentle-delicate\nicon-themeenergy_machine-wash-permanent-press\nicon-themeenergy_no-detergent\nicon-themeenergy_no-iron\nicon-themeenergy_non-chlorine\nicon-themeenergy_non-chlorine-2\nicon-themeenergy_no-steam-finishing\nicon-themeenergy_permanent-press\nicon-themeenergy_petroleum-solvent-only\nicon-themeenergy_reduced-moisture\nicon-themeenergy_s\nicon-themeenergy_short-cycle\nicon-themeenergy_tumble-dry\nicon-themeenergy_wash30c\nicon-themeenergy_wash-40c\nicon-themeenergy_wash-50c\nicon-themeenergy_wash-60c\nicon-themeenergy_wash-70c\nicon-themeenergy_wash-80c\nicon-themeenergy_wash-90c\nicon-themeenergy_washing-machine\nicon-themeenergy_washing-machine-2\nicon-themeenergy_wet-cleaning\nicon-themeenergy_wring\nicon-themeenergy_xl\nicon-themeenergy_xs\nicon-themeenergy_xxl\nicon-themeenergy_bee-box\nicon-themeenergy_brush-cutter\nicon-themeenergy_container\nicon-themeenergy_containers\nicon-themeenergy_crane\nicon-themeenergy_crane-2\nicon-themeenergy_dam\nicon-themeenergy_eggs\nicon-themeenergy_electrical-grid\nicon-themeenergy_electrical-grid-2\nicon-themeenergy_factory\nicon-themeenergy_factory-2\nicon-themeenergy_farmer\nicon-themeenergy_gas-deposit\nicon-themeenergy_honey2\nicon-themeenergy_honey-2\nicon-themeenergy_liquid-deposit\nicon-themeenergy_liquid-tanker\nicon-themeenergy_manufacturing-robot\nicon-themeenergy_manufacturing-line\nicon-themeenergy_metal-bucket\nicon-themeenergy_metal-bucket-2\nicon-themeenergy_milk-bottle\nicon-themeenergy_milk-bucket\nicon-themeenergy_nature2\nicon-themeenergy_new-plant\nicon-themeenergy_offshore-drilling\nicon-themeenergy_oil-barrel\nicon-themeenergy_oil-barrel-2\nicon-themeenergy_pipe\nicon-themeenergy_pipe-2\nicon-themeenergy_pipes\nicon-themeenergy_rake\nicon-themeenergy_rake-shovel\nicon-themeenergy_refinery\nicon-themeenergy_sack\nicon-themeenergy_scarey-man\nicon-themeenergy_trolley\nicon-themeenergy_trolley-loaded\nicon-themeenergy_warehouse\nicon-themeenergy_watering-can\nicon-themeenergy_Wheelbarrow\nicon-themeenergy_Wheelbarrow-loaded\nicon-themeenergy_wood-fence\nicon-themeenergy_wood-fence-2\nicon-themeenergy_apple2\nicon-themeenergy_badge\nicon-themeenergy_balance\nicon-themeenergy_baseball2\nicon-themeenergy_baseball-ball\nicon-themeenergy_biceps\nicon-themeenergy_bicycle2\nicon-themeenergy_bread\nicon-themeenergy_burger\nicon-themeenergy_carrot2\nicon-themeenergy_checklist\nicon-themeenergy_clock\nicon-themeenergy_diet-pills\nicon-themeenergy_fish2\nicon-themeenergy_foot\nicon-themeenergy_fork-spoon\nicon-themeenergy_goal-achieved\nicon-themeenergy_golf-ball2\nicon-themeenergy_grain\nicon-themeenergy_gym\nicon-themeenergy_hand-grip\nicon-themeenergy_heart3\nicon-themeenergy_heart-2\nicon-themeenergy_heart-impulse\nicon-themeenergy_juice\nicon-themeenergy_list\nicon-themeenergy_list-2\nicon-themeenergy_lungs\nicon-themeenergy_measurement-tape\nicon-themeenergy_medal3\nicon-themeenergy_milk\nicon-themeenergy_mp3-player2\nicon-themeenergy_no-smoking\nicon-themeenergy_peper\nicon-themeenergy_pills2\nicon-themeenergy_pills-2\nicon-themeenergy_plate\nicon-themeenergy_scale\nicon-themeenergy_scale-2\nicon-themeenergy_sea3\nicon-themeenergy_sis-kebab\nicon-themeenergy_sneakers\nicon-themeenergy_soup\nicon-themeenergy_stationary-bike\nicon-themeenergy_stomach\nicon-themeenergy_stopwatch\nicon-themeenergy_tape\nicon-themeenergy_target3\nicon-themeenergy_tennis\nicon-themeenergy_tracking-watch\nicon-themeenergy_treadmill\nicon-themeenergy_water-bottle\nicon-themeenergy_weight-control\nicon-themeenergy_weight-lifting2\nicon-themeenergy_weight-loss-belt\nicon-themeenergy_finger-swipe-around-2\nicon-themeenergy_finger-swipe-around-3\nicon-themeenergy_finger-swipe-center\nicon-themeenergy_finger-swipe-left\nicon-themeenergy_finger-swipe-left-2\nicon-themeenergy_finger-swipe-right\nicon-themeenergy_finger-swipe-right-2\nicon-themeenergy_finger-swipe-right-left\nicon-themeenergy_finger-symbol\nicon-themeenergy_finger-touch-point\nicon-themeenergy_finger-touch-point-2\nicon-themeenergy_hand-symbol\nicon-themeenergy_hand-touch-1\nicon-themeenergy_swipe-around\nicon-themeenergy_swipe-around-2\nicon-themeenergy_swipe-down\nicon-themeenergy_swipe-expand\nicon-themeenergy_swipe-left\nicon-themeenergy_swipe-minimize\nicon-themeenergy_swipe-move-hand\nicon-themeenergy_swipe-point\nicon-themeenergy_swipe-point-left\nicon-themeenergy_swipe-point-right\nicon-themeenergy_swipe-point-right-left\nicon-themeenergy_swipe-refresh\nicon-themeenergy_swipe-right\nicon-themeenergy_swipe-right-left\nicon-themeenergy_swipe-scroll-up-down\nicon-themeenergy_swipe-time\nicon-themeenergy_swipe-up\nicon-themeenergy_swipe-up-down\nicon-themeenergy_touch\nicon-themeenergy_touch-click\nicon-themeenergy_touch-finger-1\nicon-themeenergy_touch-fingers\nicon-themeenergy_touch-move\nicon-themeenergy_touch-point-click\nicon-themeenergy_touch-point-left\nicon-themeenergy_touch-point-right\nicon-themeenergy_touch-swipe-left-1\nicon-themeenergy_bomb2\nicon-themeenergy_console\nicon-themeenergy_console-2\nicon-themeenergy_crown\nicon-themeenergy_flag3\nicon-themeenergy_gear2\nicon-themeenergy_gold-bars\nicon-themeenergy_handheld-console\nicon-themeenergy_handheld-console-2\nicon-themeenergy_headphones2\nicon-themeenergy_health-bar\nicon-themeenergy_joystick\nicon-themeenergy_joystick-2\nicon-themeenergy_joystick-3\nicon-themeenergy_joystick-4\nicon-themeenergy_joystick-5\nicon-themeenergy_joystick-symbols\nicon-themeenergy_keyboard\nicon-themeenergy_laptop\nicon-themeenergy_life\nicon-themeenergy_lifes\nicon-themeenergy_microphone2\nicon-themeenergy_monitor\nicon-themeenergy_mouse\nicon-themeenergy_old-joystick\nicon-themeenergy_pac-man\nicon-themeenergy_pac-man-2\nicon-themeenergy_pc-computer\nicon-themeenergy_racing-joystick\nicon-themeenergy_rip-tomb\nicon-themeenergy_settings3\nicon-themeenergy_share3\nicon-themeenergy_shield\nicon-themeenergy_target4\nicon-themeenergy_target-22\nicon-themeenergy_tetris\nicon-themeenergy_tic-tac-toe\nicon-themeenergy_time2\nicon-themeenergy_webcam\nicon-themeenergy_win-flag\nicon-themeenergy_bar\nicon-themeenergy_cards\nicon-themeenergy_clover\nicon-themeenergy_clover-card\nicon-themeenergy_coin\nicon-themeenergy_diamond2\nicon-themeenergy_diamond-card\nicon-themeenergy_diamond-symbol\nicon-themeenergy_dice-1\nicon-themeenergy_dice-22\nicon-themeenergy_dice-3\nicon-themeenergy_dice-4\nicon-themeenergy_dice-6\nicon-themeenergy_dices\nicon-themeenergy_dices-2\nicon-themeenergy_dices-3\nicon-themeenergy_drink\nicon-themeenergy_fruit\nicon-themeenergy_fruit-2\nicon-themeenergy_fruit-3\nicon-themeenergy_fruit-4\nicon-themeenergy_heart4\nicon-themeenergy_heart-card\nicon-themeenergy_lucky-seven\nicon-themeenergy_money-bag\nicon-themeenergy_number\nicon-themeenergy_pike\nicon-themeenergy_pike-card\nicon-themeenergy_poker-table\nicon-themeenergy_roulette\nicon-themeenergy_slot-machine\nicon-themeenergy_tips\nicon-themeenergy_tips-2\nicon-themeenergy_tips-3\nicon-themeenergy_tips-4\nicon-themeenergy_armchair\nicon-themeenergy_armchair-2\nicon-themeenergy_baby-bed\nicon-themeenergy_baby-bed-2\nicon-themeenergy_bathtub\nicon-themeenergy_bed2\nicon-themeenergy_bed-2\nicon-themeenergy_bed-3\nicon-themeenergy_bedside\nicon-themeenergy_bedside-2\nicon-themeenergy_bedside-3\nicon-themeenergy_bin\nicon-themeenergy_book-shelf\nicon-themeenergy_chair\nicon-themeenergy_chair-2\nicon-themeenergy_chair-3\nicon-themeenergy_closed-door\nicon-themeenergy_closet\nicon-themeenergy_coat-stand\nicon-themeenergy_couch\nicon-themeenergy_couch-2\nicon-themeenergy_desk\nicon-themeenergy_desk-2\nicon-themeenergy_desk-chair\nicon-themeenergy_desk-lamp\nicon-themeenergy_double-bed\nicon-themeenergy_floor-lamp\nicon-themeenergy_light-fixture\nicon-themeenergy_mirror\nicon-themeenergy_open-door\nicon-themeenergy_oval-table\nicon-themeenergy_picture\nicon-themeenergy_sink\nicon-themeenergy_small-table\nicon-themeenergy_stairs\nicon-themeenergy_table\nicon-themeenergy_table-2\nicon-themeenergy_table-3\nicon-themeenergy_table-4\nicon-themeenergy_table-chairs\nicon-themeenergy_table-lamp\nicon-themeenergy_table-lamp-2\nicon-themeenergy_tv-stand\nicon-themeenergy_tv-stand-2\nicon-themeenergy_twin-bed\nicon-themeenergy_window\nicon-themeenergy_window-2\nicon-themeenergy_window-3\nicon-themeenergy_window-4\nicon-themeenergy_wood-fence2\nicon-themeenergy_baguette\nicon-themeenergy_barbecue-grill\nicon-themeenergy_beef\nicon-themeenergy_beer\nicon-themeenergy_biscuit\nicon-themeenergy_bread2\nicon-themeenergy_bread-2\nicon-themeenergy_bread-3\nicon-themeenergy_bread-slice\nicon-themeenergy_burger2\nicon-themeenergy_burger-2\nicon-themeenergy_burger-coke\nicon-themeenergy_champagne2\nicon-themeenergy_chef-hat\nicon-themeenergy_cherries2\nicon-themeenergy_coffee\nicon-themeenergy_coke\nicon-themeenergy_coke-pizza\nicon-themeenergy_doughnut\nicon-themeenergy_drink2\nicon-themeenergy_drink-2\nicon-themeenergy_drink-3\nicon-themeenergy_drink-4\nicon-themeenergy_egg\nicon-themeenergy_fried-potatoes\nicon-themeenergy_fried-potatoes-coke\nicon-themeenergy_grill-spoon\nicon-themeenergy_hot-dog\nicon-themeenergy_hot-dog-coke\nicon-themeenergy_ice-cream2\nicon-themeenergy_ice-cream-2\nicon-themeenergy_kebab\nicon-themeenergy_ketchup\nicon-themeenergy_ketchup-2\nicon-themeenergy_margarita\nicon-themeenergy_meat\nicon-themeenergy_mustard\nicon-themeenergy_pasta\nicon-themeenergy_pasta-2\nicon-themeenergy_pepperoni\nicon-themeenergy_pizza-slice\nicon-themeenergy_plate2\nicon-themeenergy_sausage\nicon-themeenergy_sausage-2\nicon-themeenergy_serving-plate\nicon-themeenergy_sis-kebab2\nicon-themeenergy_soup2\nicon-themeenergy_watermelon2\nicon-themeenergy_wine-bottle2\nicon-themeenergy_wine-bottle-2\nicon-themeenergy_add-folder\nicon-themeenergy_closed-folder\nicon-themeenergy_cloud-folder\nicon-themeenergy_compressed-folder\nicon-themeenergy_confirm-folder\nicon-themeenergy_delete-folder\nicon-themeenergy_disallow-folder\nicon-themeenergy_download-folder\nicon-themeenergy_edit-folder\nicon-themeenergy_favorite-folder\nicon-themeenergy_favorite-folder-2\nicon-themeenergy_favorite-open-folder\nicon-themeenergy_file-folder\nicon-themeenergy_hyperlink-folder\nicon-themeenergy_image-folder\nicon-themeenergy_important-folder\nicon-themeenergy_locked-folder\nicon-themeenergy_move-folder\nicon-themeenergy_move-folder-2\nicon-themeenergy_multimedia-folder\nicon-themeenergy_music-folder\nicon-themeenergy_open-folder\nicon-themeenergy_remove-folder\nicon-themeenergy_search-folder\nicon-themeenergy_search-open-folder\nicon-themeenergy_secure-folder\nicon-themeenergy_trash-folder\nicon-themeenergy_upload-folder\nicon-themeenergy_video-folder\nicon-themeenergy_web-folder\nicon-themeenergy_bank\nicon-themeenergy_banknote\nicon-themeenergy_banknotes-2\nicon-themeenergy_barcode\nicon-themeenergy_bid\nicon-themeenergy_calculator\nicon-themeenergy_calculator-2\nicon-themeenergy_cents\nicon-themeenergy_charts\nicon-themeenergy_charts-2\nicon-themeenergy_charts-3\nicon-themeenergy_charts-4\nicon-themeenergy_charts-5\nicon-themeenergy_charts-6\nicon-themeenergy_charts-7\nicon-themeenergy_credit-card2\nicon-themeenergy_credit-card-22\nicon-themeenergy_credit-card-3\nicon-themeenergy_credit-card-accepted\nicon-themeenergy_credit-card-add\nicon-themeenergy_credit-card-declined\nicon-themeenergy_credit-card-lock\nicon-themeenergy_credit-card-phone\nicon-themeenergy_credit-card-processor\nicon-themeenergy_credit-card-remove\nicon-themeenergy_date\nicon-themeenergy_discount-percentage\nicon-themeenergy_dollar\nicon-themeenergy_dollar-2\nicon-themeenergy_dollars\nicon-themeenergy_euro\nicon-themeenergy_euro-2\nicon-themeenergy_invoice\nicon-themeenergy_load-box\nicon-themeenergy_money-bag2\nicon-themeenergy_money-bag-2\nicon-themeenergy_open-wallet\nicon-themeenergy_percentage\nicon-themeenergy_pound\nicon-themeenergy_pyramid\nicon-themeenergy_safe-box\nicon-themeenergy_savings\nicon-themeenergy_scan-barcode\nicon-themeenergy_shipping-box\nicon-themeenergy_shipping-box-2\nicon-themeenergy_shopping-bag\nicon-themeenergy_shopping-bag-2\nicon-themeenergy_shopping-bag-3\nicon-themeenergy_shopping-basket\nicon-themeenergy_shopping-basket-2\nicon-themeenergy_shopping-basket-3\nicon-themeenergy_shopping-basket-5\nicon-themeenergy_shopping-basket-6\nicon-themeenergy_shopping-basket-add\nicon-themeenergy_shopping-basket-confirm\nicon-themeenergy_shopping-basket-delete\nicon-themeenergy_shopping-basket-favorite\nicon-themeenergy_shopping-basket-fill\nicon-themeenergy_shopping-basket-load\nicon-themeenergy_shopping-basket-love\nicon-themeenergy_shopping-basket-refresh\nicon-themeenergy_shopping-basket-remove\nicon-themeenergy_shopping-basket-unload\nicon-themeenergy_statistics\nicon-themeenergy_statistics-22\nicon-themeenergy_statistics-32\nicon-themeenergy_statistics-4\nicon-themeenergy_stats2\nicon-themeenergy_wallet\nicon-themeenergy_yuan\nicon-themeenergy_ai-file\nicon-themeenergy_apk-file\nicon-themeenergy_app-file\nicon-themeenergy_attach-file\nicon-themeenergy_avi-file\nicon-themeenergy_blank-file\nicon-themeenergy_blank-files\nicon-themeenergy_copy-file\nicon-themeenergy_css-file\nicon-themeenergy_csv-file\nicon-themeenergy_doc-file\nicon-themeenergy_edit-file\nicon-themeenergy_eps-file\nicon-themeenergy_exe-file\nicon-themeenergy_file\nicon-themeenergy_file-2\nicon-themeenergy_file-3\nicon-themeenergy_file-accepted\nicon-themeenergy_file-add\nicon-themeenergy_file-delete\nicon-themeenergy_file-disapprove\nicon-themeenergy_file-download\nicon-themeenergy_file-favorite\nicon-themeenergy_file-favorite-2\nicon-themeenergy_file-important\nicon-themeenergy_file-link\nicon-themeenergy_file-locked\nicon-themeenergy_file-move-1\nicon-themeenergy_file-move-2\nicon-themeenergy_file-remove\nicon-themeenergy_file-secure\nicon-themeenergy_file-stats\nicon-themeenergy_file-upload\nicon-themeenergy_flv-file\nicon-themeenergy_gif-file\nicon-themeenergy_html-file\nicon-themeenergy_image-file\nicon-themeenergy_iso-file\nicon-themeenergy_jpg-file\nicon-themeenergy_jsp-file\nicon-themeenergy_mark-file\nicon-themeenergy_mov-file\nicon-themeenergy_mp3-file\nicon-themeenergy_mp4-file\nicon-themeenergy_mpg-file\nicon-themeenergy_music-file\nicon-themeenergy_pdf-file\nicon-themeenergy_png-file\nicon-themeenergy_psd-file\nicon-themeenergy_rar-file\nicon-themeenergy_raw-file\nicon-themeenergy_search-file\nicon-themeenergy_split-file\nicon-themeenergy_sql-file\nicon-themeenergy_txt-file\nicon-themeenergy_video-file\nicon-themeenergy_wav-file\nicon-themeenergy_xls-file\nicon-themeenergy_xml-file\nicon-themeenergy_zip-file\nicon-themeenergy_angel\nicon-themeenergy_angry\nicon-themeenergy_cry\nicon-themeenergy_cry-2\nicon-themeenergy_cute-smile\nicon-themeenergy_cuttie\nicon-themeenergy_devil\nicon-themeenergy_gasp\nicon-themeenergy_gasp-2\nicon-themeenergy_gasp-3\nicon-themeenergy_gasp-4\nicon-themeenergy_grandpa\nicon-themeenergy_grin\nicon-themeenergy_grumpy\nicon-themeenergy_grumpy-2\nicon-themeenergy_grumpy-3\nicon-themeenergy_grumpy-4\nicon-themeenergy_grumpy-5\nicon-themeenergy_grumpy-6\nicon-themeenergy_grumpy-7\nicon-themeenergy_grumpy-8\nicon-themeenergy_happy\nicon-themeenergy_kiki\nicon-themeenergy_lady\nicon-themeenergy_laughing\nicon-themeenergy_love\nicon-themeenergy_love-2\nicon-themeenergy_pain\nicon-themeenergy_pain-2\nicon-themeenergy_sad\nicon-themeenergy_sad-2\nicon-themeenergy_sad-3\nicon-themeenergy_sad-angry\nicon-themeenergy_sceptical\nicon-themeenergy_sceptical-2\nicon-themeenergy_sensational\nicon-themeenergy_shocked\nicon-themeenergy_shocked-2\nicon-themeenergy_silent\nicon-themeenergy_silent-2\nicon-themeenergy_smile\nicon-themeenergy_smile-2\nicon-themeenergy_smile-3\nicon-themeenergy_smile-4\nicon-themeenergy_smile-glasses\nicon-themeenergy_smile-sunglasses\nicon-themeenergy_smile-sunglasses-2\nicon-themeenergy_smile-sunglasses-3\nicon-themeenergy_smile-teeth\nicon-themeenergy_surprise\nicon-themeenergy_tongue\nicon-themeenergy_tongue-2\nicon-themeenergy_tongue-3\nicon-themeenergy_tongue-kiki\nicon-themeenergy_tongue-smile\nicon-themeenergy_unhappy\nicon-themeenergy_unhappy-2\nicon-themeenergy_unsure\nicon-themeenergy_upset\nicon-themeenergy_wink\nicon-themeenergy_air-condition\nicon-themeenergy_air-purifier\nicon-themeenergy_blender\nicon-themeenergy_bread-maker\nicon-themeenergy_calculator2\nicon-themeenergy_camcorder2\nicon-themeenergy_digital-camera4\nicon-themeenergy_digital-clock\nicon-themeenergy_digital-scale\nicon-themeenergy_digital-scale-2\nicon-themeenergy_dvd-player\nicon-themeenergy_electric-heater\nicon-themeenergy_espresso-machine\nicon-themeenergy_fan\nicon-themeenergy_fridge\nicon-themeenergy_hair-dryer\nicon-themeenergy_halogen-heater\nicon-themeenergy_hand-mixer\nicon-themeenergy_heater-2\nicon-themeenergy_large-mixer\nicon-themeenergy_meat-grinder\nicon-themeenergy_microwave\nicon-themeenergy_mixer\nicon-themeenergy_monitor2\nicon-themeenergy_monitor-2\nicon-themeenergy_monitor-3\nicon-themeenergy_monitor-not-working\nicon-themeenergy_monitors\nicon-themeenergy_oven\nicon-themeenergy_oven-2\nicon-themeenergy_projector\nicon-themeenergy_sewing-machine\nicon-themeenergy_shaver\nicon-themeenergy_socket\nicon-themeenergy_socket-2\nicon-themeenergy_socket-plug\nicon-themeenergy_socket-plug-add\nicon-themeenergy_socket-plug-removed\nicon-themeenergy_toaster\nicon-themeenergy_tooth-brush\nicon-themeenergy_vacuum\nicon-themeenergy_vacuum-2\nicon-themeenergy_walkie-talkie\nicon-themeenergy_washing-machine2\nicon-themeenergy_water-boiler\nicon-themeenergy_biology\nicon-themeenergy_bio-symbol\nicon-themeenergy_bulb2\nicon-themeenergy_bulb-2\nicon-themeenergy_bulb-3\nicon-themeenergy_bulb-4\nicon-themeenergy_earth3\nicon-themeenergy_eco-symbol\nicon-themeenergy_energy\nicon-themeenergy_factory2\nicon-themeenergy_gas-station\nicon-themeenergy_glass-flower\nicon-themeenergy_gmo-symbol\nicon-themeenergy_green-bulb\nicon-themeenergy_green-socket\nicon-themeenergy_home-electricity\nicon-themeenergy_nature3\nicon-themeenergy_plant2\nicon-themeenergy_plant-symbol\nicon-themeenergy_pot-flower\nicon-themeenergy_rain2\nicon-themeenergy_recycling\nicon-themeenergy_recycling-2\nicon-themeenergy_renew\nicon-themeenergy_renew-2\nicon-themeenergy_renew-3\nicon-themeenergy_solar-panel\nicon-themeenergy_solar-panel-2\nicon-themeenergy_temperature4\nicon-themeenergy_tree2\nicon-themeenergy_tree-22\nicon-themeenergy_wall-socket\nicon-themeenergy_wall-socket-2\nicon-themeenergy_waterdrop\nicon-themeenergy_water-hand\nicon-themeenergy_water-tap\nicon-themeenergy_water-tap-2\nicon-themeenergy_wind-turbine\nicon-themeenergy_wind-turbine-2\nicon-themeenergy_wind-turbine-3\nicon-themeenergy_computer\nicon-themeenergy_computer-keyboard\nicon-themeenergy_computer-laptop\nicon-themeenergy_computer-phone\nicon-themeenergy_computers\nicon-themeenergy_computer-tablet\nicon-themeenergy_connectivity-cord\nicon-themeenergy_delete-phone2\nicon-themeenergy_disc2\nicon-themeenergy_fan2\nicon-themeenergy_floppy-disc\nicon-themeenergy_hard-drive\nicon-themeenergy_hdd\nicon-themeenergy_keyboard2\nicon-themeenergy_laptop2\nicon-themeenergy_laptop-2\nicon-themeenergy_laptop-3\nicon-themeenergy_monitor3\nicon-themeenergy_mouse2\nicon-themeenergy_notebook\nicon-themeenergy_notebooks\nicon-themeenergy_pc\nicon-themeenergy_pcb-board\nicon-themeenergy_pc-tower\nicon-themeenergy_pc-towers\nicon-themeenergy_pda\nicon-themeenergy_phone-not-allower\nicon-themeenergy_phone-orientation\nicon-themeenergy_phone-transfer\nicon-themeenergy_power-cord\nicon-themeenergy_power-socket\nicon-themeenergy_power-socket-2\nicon-themeenergy_printer\nicon-themeenergy_printer-2\nicon-themeenergy_processor\nicon-themeenergy_processor-2\nicon-themeenergy_ram\nicon-themeenergy_router2\nicon-themeenergy_router-signal\nicon-themeenergy_sd-memory-card\nicon-themeenergy_serial-pinout\nicon-themeenergy_smartphone2\nicon-themeenergy_tablet\nicon-themeenergy_tablet-2\nicon-themeenergy_tablet-orientation\nicon-themeenergy_tablet-phone\nicon-themeenergy_usb-icon\nicon-themeenergy_usb-stick\nicon-themeenergy_usb-stick-add\nicon-themeenergy_usb-stick-confirm\nicon-themeenergy_usb-stick-delete\nicon-themeenergy_usb-stick-remove\nicon-themeenergy_webcam2\nicon-themeenergy_wireless-controller2\nicon-themeenergy_wireless-stick\nicon-themeenergy_aperture2\nicon-themeenergy_arch-shape\nicon-themeenergy_brush\nicon-themeenergy_brush-2\nicon-themeenergy_bucket\nicon-themeenergy_bucket-drop\nicon-themeenergy_cmyk\nicon-themeenergy_compass2\nicon-themeenergy_crop\nicon-themeenergy_eraser\nicon-themeenergy_eyedropper\nicon-themeenergy_files\nicon-themeenergy_graphic-tablet\nicon-themeenergy_guides\nicon-themeenergy_knife\nicon-themeenergy_line-shape\nicon-themeenergy_magnet\nicon-themeenergy_marker\nicon-themeenergy_paintboard\nicon-themeenergy_paintbrush\nicon-themeenergy_palette\nicon-themeenergy_pen\nicon-themeenergy_pencil\nicon-themeenergy_rectangular-shape\nicon-themeenergy_rgb2\nicon-themeenergy_roller\nicon-themeenergy_ruler\nicon-themeenergy_ruler-2\nicon-themeenergy_ruler-pencil\nicon-themeenergy_rulers\nicon-themeenergy_ruler-stylus\nicon-themeenergy_scissor\nicon-themeenergy_spray\nicon-themeenergy_stylus\nicon-themeenergy_vector-shape\nicon-themeenergy_vector-shape-2\nicon-themeenergy_vector-shape-pen\nicon-themeenergy_zoom-tool\nicon-themeenergy_zoom-tool-enlarge\nicon-themeenergy_zoom-tool-minimize\nicon-themeenergy_axe\nicon-themeenergy_barricade\nicon-themeenergy_barricade-2\nicon-themeenergy_brick-wall2\nicon-themeenergy_bulldozer\nicon-themeenergy_chisel\nicon-themeenergy_cone\nicon-themeenergy_cone-2\nicon-themeenergy_crane2\nicon-themeenergy_crane-22\nicon-themeenergy_crane-3\nicon-themeenergy_drill\nicon-themeenergy_excavator\nicon-themeenergy_hammer\nicon-themeenergy_hammer-2\nicon-themeenergy_helmet\nicon-themeenergy_helmet-2\nicon-themeenergy_ladder\nicon-themeenergy_metre\nicon-themeenergy_nail\nicon-themeenergy_nivel\nicon-themeenergy_paint-brush\nicon-themeenergy_paint-roller\nicon-themeenergy_paint-scraper\nicon-themeenergy_pick\nicon-themeenergy_power-saw\nicon-themeenergy_road2\nicon-themeenergy_rotary-hammer\nicon-themeenergy_saw\nicon-themeenergy_scraper\nicon-themeenergy_scraper-2\nicon-themeenergy_scraper-3\nicon-themeenergy_screwdriver\nicon-themeenergy_screwdriver-2\nicon-themeenergy_shovel\nicon-themeenergy_shovel-2\nicon-themeenergy_spirit-level\nicon-themeenergy_steamroller\nicon-themeenergy_tape2\nicon-themeenergy_truck2\nicon-themeenergy_truck-22\nicon-themeenergy_warning\nicon-themeenergy_work-jacket\nicon-themeenergy_wrench\nicon-themeenergy_wrench-2\nicon-themeenergy_belt\nicon-themeenergy_bikini\nicon-themeenergy_blouse\nicon-themeenergy_blouse-2\nicon-themeenergy_bow2\nicon-themeenergy_bow-2\nicon-themeenergy_bow-3\nicon-themeenergy_bra\nicon-themeenergy_cap\nicon-themeenergy_cap-2\nicon-themeenergy_cap-3\nicon-themeenergy_cap-4\nicon-themeenergy_cap-5\nicon-themeenergy_cardigan\nicon-themeenergy_dreass-2\nicon-themeenergy_dress\nicon-themeenergy_hanger\nicon-themeenergy_jacket\nicon-themeenergy_lady-bag\nicon-themeenergy_lady-bag-2\nicon-themeenergy_night-dress\nicon-themeenergy_pants\nicon-themeenergy_pants-2\nicon-themeenergy_scarf\nicon-themeenergy_shirt\nicon-themeenergy_shirt-2\nicon-themeenergy_shirt-polo\nicon-themeenergy_short-pants\nicon-themeenergy_short-pants-2\nicon-themeenergy_short-pants-3\nicon-themeenergy_skirt\nicon-themeenergy_skirt-2\nicon-themeenergy_socks\nicon-themeenergy_tie\nicon-themeenergy_t-shirt\nicon-themeenergy_tuxedo\nicon-themeenergy_underwear\nicon-themeenergy_underwear-2\nicon-themeenergy_underwear-3\nicon-themeenergy_underwear-4\nicon-themeenergy_underwear-5\nicon-themeenergy_vest\nicon-themeenergy_vest-set\nicon-themeenergy_wallet2\nicon-themeenergy_watch\nicon-themeenergy_angel2\nicon-themeenergy_cake2\nicon-themeenergy_candle3\nicon-themeenergy_candy2\nicon-themeenergy_candy-cane\nicon-themeenergy_christmas-ball\nicon-themeenergy_christmas-bell\nicon-themeenergy_christmas-cupcake\nicon-themeenergy_christmas-hat\nicon-themeenergy_christmas-sock\nicon-themeenergy_christmas-tree\nicon-themeenergy_christmas-tree-2\nicon-themeenergy_christmas-tree-3\nicon-themeenergy_cookie-man\nicon-themeenergy_cross2\nicon-themeenergy_desserts\nicon-themeenergy_discounts\nicon-themeenergy_fireworks\nicon-themeenergy_gift-bag\nicon-themeenergy_gift-card\nicon-themeenergy_lollipop\nicon-themeenergy_lollipop-desserts\nicon-themeenergy_present2\nicon-themeenergy_sales\nicon-themeenergy_scarf2\nicon-themeenergy_serving-plate2\nicon-themeenergy_shopping-bag2\nicon-themeenergy_sled\nicon-themeenergy_snowflake\nicon-themeenergy_snow-man\nicon-themeenergy_star\nicon-themeenergy_star-2\nicon-themeenergy_star-3\nicon-themeenergy_turkey\nicon-themeenergy_wreath\nicon-themeenergy_chat\nicon-themeenergy_chat-2\nicon-themeenergy_chat-3\nicon-themeenergy_chat-bubble\nicon-themeenergy_chat-bubble-2\nicon-themeenergy_chat-bubble-add\nicon-themeenergy_chat-bubble-confirm\nicon-themeenergy_chat-bubble-cute\nicon-themeenergy_chat-bubble-delete\nicon-themeenergy_chat-bubble-happy\nicon-themeenergy_chat-bubble-laughing\nicon-themeenergy_chat-bubble-loading\nicon-themeenergy_chat-bubble-remove\nicon-themeenergy_chat-bubbles\nicon-themeenergy_chat-bubbles-2\nicon-themeenergy_chat-bubbles-3\nicon-themeenergy_chat-bubbles-4\nicon-themeenergy_chat-bubble-sad\nicon-themeenergy_chat-bubble-smile\nicon-themeenergy_chat-bubble-smiling\nicon-themeenergy_chat-bubble-surprise\nicon-themeenergy_chat-bubble-tongue\nicon-themeenergy_chat-bubble-writing\nicon-themeenergy_chat-small-bubble\nicon-themeenergy_chat-small-bubble-2\nicon-themeenergy_axe2\nicon-themeenergy_binoculars2\nicon-themeenergy_boots\nicon-themeenergy_camera\nicon-themeenergy_camping-bag\nicon-themeenergy_camping-table\nicon-themeenergy_compass3\nicon-themeenergy_cup3\nicon-themeenergy_eyeglasses\nicon-themeenergy_favorite-place\nicon-themeenergy_fire2\nicon-themeenergy_first-aid-bag\nicon-themeenergy_fishing\nicon-themeenergy_flashlight\nicon-themeenergy_gas-stove\nicon-themeenergy_information-sign\nicon-themeenergy_kayak\nicon-themeenergy_knife2\nicon-themeenergy_lantern\nicon-themeenergy_life-vest\nicon-themeenergy_lighter\nicon-themeenergy_liquid-bottle\nicon-themeenergy_map2\nicon-themeenergy_map-point\nicon-themeenergy_mountains2\nicon-themeenergy_parking-sign\nicon-themeenergy_radio2\nicon-themeenergy_road-sign2\nicon-themeenergy_route\nicon-themeenergy_sean-moon\nicon-themeenergy_sea-sun\nicon-themeenergy_shower2\nicon-themeenergy_skull-cap\nicon-themeenergy_surfboard\nicon-themeenergy_tent2\nicon-themeenergy_trailer2\nicon-themeenergy_trees\nicon-themeenergy_wall-socket2\nicon-themeenergy_wooden-house\nicon-themeenergy_wooden-table\nicon-themeenergy_beef2\nicon-themeenergy_blender2\nicon-themeenergy_broad-beans\nicon-themeenergy_broccoli\nicon-themeenergy_cake3\nicon-themeenergy_catalog\nicon-themeenergy_champagne3\nicon-themeenergy_chef-hat2\nicon-themeenergy_coffe-7\nicon-themeenergy_coffee2\nicon-themeenergy_coffee-2\nicon-themeenergy_coffee-3\nicon-themeenergy_coffee-4\nicon-themeenergy_coffee-5\nicon-themeenergy_coffee-6\nicon-themeenergy_coffee-bean\nicon-themeenergy_coffee-bean-2\nicon-themeenergy_coffee-bean-3\nicon-themeenergy_coffee-bean-4\nicon-themeenergy_coffee-frother\nicon-themeenergy_coffee-machine\nicon-themeenergy_coffee-take-go\nicon-themeenergy_cold-coffe-2\nicon-themeenergy_cold-coffee\nicon-themeenergy_croissant\nicon-themeenergy_cup4\nicon-themeenergy_cupcake\nicon-themeenergy_drink3\nicon-themeenergy_drink-22\nicon-themeenergy_fish3\nicon-themeenergy_fork-knife\nicon-themeenergy_hot-coffee\nicon-themeenergy_ice-cream3\nicon-themeenergy_margarita2\nicon-themeenergy_mushroom2\nicon-themeenergy_pan\nicon-themeenergy_pasta2\nicon-themeenergy_pizza\nicon-themeenergy_pizza-2\nicon-themeenergy_roasted-chicken\nicon-themeenergy_roasted-chicken-2\nicon-themeenergy_scliced-mushroom\nicon-themeenergy_serving-plate3\nicon-themeenergy_soup3\nicon-themeenergy_soup-2\nicon-themeenergy_sushi\nicon-themeenergy_tea\nicon-themeenergy_tea-bag\nicon-themeenergy_watermelon3\nicon-themeenergy_wine\nicon-themeenergy_asian-temple\nicon-themeenergy_berlin-tower\nicon-themeenergy_big-ben\nicon-themeenergy_building\nicon-themeenergy_building-2\nicon-themeenergy_castle\nicon-themeenergy_castle-2\nicon-themeenergy_chrysler-tower\nicon-themeenergy_church2\nicon-themeenergy_city-hall\nicon-themeenergy_coloseum\nicon-themeenergy_column\nicon-themeenergy_door\nicon-themeenergy_dubai-tower\nicon-themeenergy_eiffel-tower\nicon-themeenergy_el-castillo\nicon-themeenergy_factory3\nicon-themeenergy_garage\nicon-themeenergy_garden-door\nicon-themeenergy_guard-tower\nicon-themeenergy_hospital\nicon-themeenergy_house\nicon-themeenergy_japanese-gate\nicon-themeenergy_large-office\nicon-themeenergy_lighthouse2\nicon-themeenergy_london-eye\nicon-themeenergy_military-base\nicon-themeenergy_museum\nicon-themeenergy_office\nicon-themeenergy_opera-house\nicon-themeenergy_pantheon\nicon-themeenergy_pentagon\nicon-themeenergy_pisa-tower\nicon-themeenergy_police-station\nicon-themeenergy_post-office\nicon-themeenergy_pyramids\nicon-themeenergy_school\nicon-themeenergy_school-2\nicon-themeenergy_shop\nicon-themeenergy_small-house\nicon-themeenergy_space-needle\nicon-themeenergy_station\nicon-themeenergy_statue-of-liberty\nicon-themeenergy_taj-mahal\nicon-themeenergy_temple\nicon-themeenergy_tent3\nicon-themeenergy_tower\nicon-themeenergy_tower-2\nicon-themeenergy_windmill\nicon-themeenergy_window2\nicon-themeenergy_blind-eye\nicon-themeenergy_bone\nicon-themeenergy_bones\nicon-themeenergy_brain\nicon-themeenergy_broken-heart\nicon-themeenergy_closed-eye\nicon-themeenergy_closed-eyes\nicon-themeenergy_diagnose-tooth\nicon-themeenergy_dna2\nicon-themeenergy_ear\nicon-themeenergy_elbow\nicon-themeenergy_eye2\nicon-themeenergy_eyes\nicon-themeenergy_finger\nicon-themeenergy_foot-1\nicon-themeenergy_foot-2\nicon-themeenergy_foot-3\nicon-themeenergy_hair\nicon-themeenergy_hand\nicon-themeenergy_head\nicon-themeenergy_heart-22\nicon-themeenergy_heart5\nicon-themeenergy_heart-impulse2\nicon-themeenergy_hearts2\nicon-themeenergy_knee\nicon-themeenergy_lips\nicon-themeenergy_lung-2\nicon-themeenergy_lungs2\nicon-themeenergy_mustache\nicon-themeenergy_mustache-1\nicon-themeenergy_nose\nicon-themeenergy_pancreas\nicon-themeenergy_skull\nicon-themeenergy_stomach2\nicon-themeenergy_throat\nicon-themeenergy_toes\nicon-themeenergy_tooth\nicon-themeenergy_tooth-2\nicon-themeenergy_tooth-3\nicon-themeenergy_tooth-4\nicon-themeenergy_badge-1\nicon-themeenergy_badge-2\nicon-themeenergy_badge-3\nicon-themeenergy_badge-4\nicon-themeenergy_badge-5\nicon-themeenergy_badge-6\nicon-themeenergy_badge-7\nicon-themeenergy_badge-8\nicon-themeenergy_badge-9\nicon-themeenergy_badge-10\nicon-themeenergy_badge-11\nicon-themeenergy_letter-stamp\nicon-themeenergy_mail-badge\nicon-themeenergy_post-stamp\nicon-themeenergy_post-stamp-1\nicon-themeenergy_post-stamp-2\nicon-themeenergy_post-stamp-3\nicon-themeenergy_post-stamp-4\nicon-themeenergy_retro-badge\nicon-themeenergy_ribbon2\nicon-themeenergy_ribbon-2\nicon-themeenergy_ribbon-3\nicon-themeenergy_shield-badge\nicon-themeenergy_stamp-badge\nicon-themeenergy_text-stamp\nicon-sharpicons\nicon-themeenergy_arrow-lop\nicon-themeenergy_arrow-right-over\nicon-themeenergy_arrow-right-over-1\nicon-themeenergy_arrows-refresh\nicon-themeenergy_arrow-turn-all-sides\nicon-themeenergy_arrow-turn-left\nicon-themeenergy_arrow-turn-right\nicon-themeenergy_back-left-arrow\nicon-themeenergy_back-right-arrow\nicon-themeenergy_both-way-arrows\nicon-themeenergy_bottom-align\nicon-themeenergy_bottom-align-1\nicon-themeenergy_bottom-arrow-2\nicon-themeenergy_bottom-big-arrow\nicon-themeenergy_bottom-big-arrow-1\nicon-themeenergy_bottom-right-corner\nicon-themeenergy_double-arrow-down\nicon-themeenergy_double-arrow-left\nicon-themeenergy_double-arrow-right\nicon-themeenergy_double-arrow-up\nicon-themeenergy_down\nicon-themeenergy_down-circle\nicon-themeenergy_down-sign\nicon-themeenergy_expand2\nicon-themeenergy_expand-1\nicon-themeenergy_expand-2\nicon-themeenergy_expand-3\nicon-themeenergy_forbidden-down\nicon-themeenergy_forbidden-left\nicon-themeenergy_forbidden-right\nicon-themeenergy_forbidden-up\nicon-themeenergy_frame\nicon-themeenergy_full-expand\nicon-themeenergy_full-expand-1\nicon-themeenergy_go-boith-ways-1\nicon-themeenergy_go-both-ways\nicon-themeenergy_go-down-left\nicon-themeenergy_go-downright\nicon-themeenergy_go-right-left\nicon-themeenergy_go-top-left\nicon-themeenergy_go-top-right\nicon-themeenergy_left\nicon-themeenergy_left-align\nicon-themeenergy_left-align-1\nicon-themeenergy_left-arrow-2\nicon-themeenergy_left-big-arrow\nicon-themeenergy_left-big-arrow-1\nicon-themeenergy_left-big-arrow-2\nicon-themeenergy_left-circle\nicon-themeenergy_left-inside\nicon-themeenergy_left-right\nicon-themeenergy_left-side\nicon-themeenergy_left-sign\nicon-themeenergy_minimize2\nicon-themeenergy_minimize-frame\nicon-themeenergy_program-window\nicon-themeenergy_reload-1\nicon-themeenergy_reload-3\nicon-themeenergy_reload-4\nicon-themeenergy_repeat-arrows\nicon-themeenergy_right\nicon-themeenergy_right-align\nicon-themeenergy_right-align-1\nicon-themeenergy_right-arrow-2\nicon-themeenergy_right-big-arrow\nicon-themeenergy_right-big-arrow-1\nicon-themeenergy_right-big-arrow-2\nicon-themeenergy_right-circle\nicon-themeenergy_right-corner\nicon-themeenergy_right-inside\nicon-themeenergy_right-left-arrow-2\nicon-themeenergy_right-side\nicon-themeenergy_right-sign\nicon-themeenergy_start-end-points\nicon-themeenergy_step-back\nicon-themeenergy_step-down\nicon-themeenergy_step-forward\nicon-themeenergy_step-up\nicon-themeenergy_top-align\nicon-themeenergy_top-align-1\nicon-themeenergy_top-arrow-2\nicon-themeenergy_top-big-arrow\nicon-themeenergy_top-big-arrow-1\nicon-themeenergy_top-circle\nicon-themeenergy_turn-left\nicon-themeenergy_turn-left-right-arrows\nicon-themeenergy_turn-right\nicon-themeenergy_two-way-left\nicon-themeenergy_two-way-right\nicon-themeenergy_up\nicon-themeenergy_up-down\nicon-themeenergy_up-down--arrow-2\nicon-themeenergy_up-down-right-left\nicon-themeenergy_up-sign\nicon-themeenergy_Aligator\nicon-themeenergy_Bat\nicon-themeenergy_Bear\nicon-themeenergy_bear3\nicon-themeenergy_Bee\nicon-themeenergy_Bird\nicon-themeenergy_Bug\nicon-themeenergy_Bull\nicon-themeenergy_Butterfly\nicon-themeenergy_Camel\nicon-themeenergy_Cat\nicon-themeenergy_Cheetah\nicon-themeenergy_Cow\nicon-themeenergy_crab\nicon-themeenergy_Deer\nicon-themeenergy_Dog\nicon-themeenergy_Dolphin\nicon-themeenergy_Donkey\nicon-themeenergy_Dove\nicon-themeenergy_Duck\nicon-themeenergy_Eagle\nicon-themeenergy_Elephant\nicon-themeenergy_elephant-2\nicon-themeenergy_Fish\nicon-themeenergy_Fox\nicon-themeenergy_Frog\nicon-themeenergy_Giraffe\nicon-themeenergy_Hippo\nicon-themeenergy_hippo-2\nicon-themeenergy_Horse\nicon-themeenergy_Jellyfish\nicon-themeenergy_Koala\nicon-themeenergy_koala-2\nicon-themeenergy_Leopard\nicon-themeenergy_Lion\nicon-themeenergy_Monkey\nicon-themeenergy_Mouse\nicon-themeenergy_octopus\nicon-themeenergy_Orca\nicon-themeenergy_Owl\nicon-themeenergy_Panda\nicon-themeenergy_Penguin\nicon-themeenergy_Pig\nicon-themeenergy_Rabbit\nicon-themeenergy_Rhinoceros\nicon-themeenergy_Rooster\nicon-themeenergy_sea-horse\nicon-themeenergy_sea-star\nicon-themeenergy_Sheep\nicon-themeenergy_Shell\nicon-themeenergy_Snake\nicon-themeenergy_Spider\nicon-themeenergy_Tiger\nicon-themeenergy_Turtle\nicon-themeenergy_Wolf\nicon-themeenergy_align-all\nicon-themeenergy_align-all-1\nicon-themeenergy_align-center\nicon-themeenergy_align-center-point-horizontal\nicon-themeenergy_align-center-point-vertical\nicon-themeenergy_align-justify-all\nicon-themeenergy_align-justify-center\nicon-themeenergy_align-justify-left\nicon-themeenergy_align-justify-right\nicon-themeenergy_align-left\nicon-themeenergy_align-right\nicon-themeenergy_column-row\nicon-themeenergy_grid-align\nicon-themeenergy_horizontal-align-bottom-1\nicon-themeenergy_horizontal-align-bottom-2\nicon-themeenergy_horizontal-align-center-left-1\nicon-themeenergy_horizontal-align-center-right-1\nicon-themeenergy_horizontal-align-top-1\nicon-themeenergy_horizontal-align-top-2\nicon-themeenergy_indent-left--margin\nicon-themeenergy_indent-right-margin\nicon-themeenergy_letter-align-1\nicon-themeenergy_text-align\nicon-themeenergy_vertical-align-botton\nicon-themeenergy_vertical-align-center-1\nicon-themeenergy_vertical-align-left\nicon-themeenergy_vertical-align-left-1\nicon-themeenergy_vertical-align-right\nicon-themeenergy_vertical-align-right-1\nicon-themeenergy_vertical-align-top";
		
		$options[] = array(
			'name' => esc_html__('Iconic Features Widget classes', 'transfers'),
			'desc' => esc_html__('The css classes used for features icons in Iconic Features Widget on home page and in other sidebars', 'transfers'),
			'id' => 'iconic_features_widget_classes',
			'std' => $default_classes,
			'class' => '', //mini, tiny, small
			'type' => 'textarea');
	
		$options[] = array(
			'name' => esc_html__('Faqs', 'transfers'),
			'type' => 'heading');
			
		$options[] = array(
			'name' => esc_html__("Enable 'Faqs'", 'transfers'),
			'desc' => esc_html__("Enable the 'Faqs' data type", 'transfers'),
			'id' => 'enable_faqs',
			'std' => '1',
			'type' => 'checkbox');
			
		$options[] = array(
			'name' => esc_html__('Transfers', 'transfers'),
			'type' => 'heading');
			
		$options[] = array(
			'name' => esc_html__("Enable 'Destinations'", 'transfers'),
			'desc' => esc_html__("Enable the 'Destinations' data type", 'transfers'),
			'id' => 'enable_destinations',
			'std' => '1',
			'type' => 'checkbox');
			
		$options[] = array(
			'name' => esc_html__('Single destination permalink slug', 'transfers'),
			'desc' => esc_html__('The permalink slug used for creating a single destination (by default it is set to "destinations". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'transfers'),
			'id' => 'destinations_permalink_slug',
			'std' => 'destination',
			'type' => 'text');
			
		$options[] = array(
			'name' => esc_html__('Destinations archive posts per page', 'transfers'),
			'desc' => esc_html__('Number of destinations to display on destinations archive page', 'transfers'),
			'id' => 'destinations_archive_posts_per_page',
			'std' => '12',
			'type' => 'text');
			
		$options[] = array(
			'name' => esc_html__('Extra fields displayed on single destination page.', 'transfers'),
			'desc' => esc_html__('Extra fields displayed on single destination page if set.', 'transfers'),
			'id' => 'destination_extra_fields',
			'std' => 'Default field label',
			'type' => 'repeat_extra_field');
			
		$options[] = array(
			'name' => esc_html__('Transport Types', 'transfers'),
			'type' => 'heading');
			
		$options[] = array(
			'name' => esc_html__("Enable 'Transport types'", 'transfers'),
			'desc' => esc_html__("Enable the 'Transport types' data type", 'transfers'),
			'id' => 'enable_transport_types',
			'std' => '1',
			'type' => 'checkbox');
			
		$options[] = array(
			'name' => esc_html__("Enable 'Extra items'", 'transfers'),
			'desc' => esc_html__("Enable the 'Extra items' data type", 'transfers'),
			'id' => 'enable_extra_items',
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'name' => esc_html__('Booking Form', 'transfers'),
			'type' => 'heading');
			
		$options[] = array(
			'name' => esc_html__('Booking form fields', 'transfers'),
			'desc' => esc_html__('Booking form fields for transfers.', 'transfers'),
			'id' => 'booking_form_fields',
			'std' => 'Default form field label',
			'type' => 'repeat_form_field');			

		if (transfers_is_woocommerce_active()) {		
			$options[] = array(
				'name' => esc_html__('WooCommerce Settings', 'transfers'),
				'type' => 'heading');		
			
			$options[] = array(
				'name' => esc_html__('Use WooCommerce for checkout', 'transfers'),
				'desc' => esc_html__('Use WooCommerce to enable payment after booking', 'transfers'),
				'id' => 'use_woocommerce_for_checkout',
				'std' => '0',
				'type' => 'checkbox');
				
			$options[] = array(
				'name' => esc_html__('Product placeholder image', 'transfers'),
				'desc' => esc_html__('Upload a custom product placeholder image to go in place of default product image used in WooCommerce cart.', 'transfers'),
				'id' => 'woocommerce_product_placeholder_image',
				'type' => 'upload');
				
			$status_array = array (
				'pending' => esc_html__('Pending', 'transfers'),
				'on-hold' => esc_html__('On hold', 'transfers'),
				'completed' => esc_html__('Completed', 'transfers'),
				'processing' => esc_html__('Processing', 'transfers'),
				'cancelled' => esc_html__('Cancelled', 'transfers'),
				'initiated' => esc_html__('Initiated', 'transfers'),				
			);
			
			$options[] = array(
				'name' => esc_html__('Completed order WooCommerce statuses', 'transfers'),
				'desc' => esc_html__('Which WooCommerce statuses do you want to consider as booked so that transfer is no longer seen as available?', 'transfers'),
				'id' => 'completed_order_woocommerce_statuses',
				'options' => $status_array,
				'std' => 'completed',
				'class' => '', //mini, tiny, small
				'type' => 'multicheck');
		
			$options[] = array(
				'name' => esc_html__('WooCommerce pages sidebar position', 'transfers'),
				'desc' => esc_html__('Select the position (if any) of sidebars to appear on all WooCommerce-specific pages of your website.', 'transfers'),
				'id' => 'woocommerce_pages_sidebar_position',
				'std' => 'three',
				'type' => 'select',
				'class' => 'mini', //mini, tiny, small
				'options' => $page_sidebars);
		}
		
		if (function_exists('transfers_extra_tables_exist')) {
		
			if (!transfers_extra_tables_exist()) {

				$options[] = array(
					'name' => esc_html__('Database', 'transfers'),
					'type' => 'heading');

				$options[] = array(
					'text' => __('Create tables', 'transfers'),
					'name' => __('The Transfers Theme database tables don\'t exist!', 'transfers'),
					'desc' => __('The Transfers Theme database tables need creation. Click the button above to create them.', 'transfers'),
					'id' => 'create_transfers_tables',
					'std' => 'Default',
					'type' => 'link_button_field');
			}
		}
	}

	return $options;
}