<?php



/*-----------------------------------------------------------------------------------



	Plugin Name: Transfers Advanced Search Widget



-----------------------------------------------------------------------------------*/





// Add function to widgets_init that'll load our widget.

add_action( 'widgets_init', 'transfers_advanced_search_widgets' );



// Register widget.

function transfers_advanced_search_widgets() {

	register_widget( 'transfers_Advanced_Search_Widget' );

	add_action('transfers_plugin_enqueue_scripts_styles', 'transfers_advanced_search_enqueue');

}



function transfers_advanced_search_enqueue() {



	$language_code = transfers_get_current_language_code();

	

	wp_register_script(	'transfers-timepicker', TRANSFERS_PLUGIN_URI . '/js/jquery-ui-timepicker-addon.js', array('jquery', 'jquery-ui-datepicker'), '1.0',true);

	wp_enqueue_script( 'transfers-timepicker' );

	wp_register_script(	'transfers-search', TRANSFERS_PLUGIN_URI . '/js/search.js', array('jquery', 'transfers-jquery-validate', 'transfers-timepicker'), '1.0',true);

	wp_enqueue_script( 'transfers-search' );	

	

	if ($language_code != "en" && transfers_does_file_exist('/js/i18n/jquery-ui-timepicker-' . $language_code . '.js')) {

		wp_register_script(	'transfers-timepicker-' . $language_code, TRANSFERS_PLUGIN_URI . 'js/i18n/jquery-ui-timepicker-' . $language_code . '.js', array('jquery', 'transfers-timepicker'), '1.0',true);

		wp_enqueue_script( 'transfers-timepicker-' . $language_code );

	}

}



/*-----------------------------------------------------------------------------------*/

/* Widget class

/*-----------------------------------------------------------------------------------*/

class transfers_advanced_search_widget extends WP_Widget {



	/*-----------------------------------------------------------------------------------*/

	/*	Widget Setup

	/*-----------------------------------------------------------------------------------*/

		

	function __construct() {

	

		/* Widget settings. */

		$widget_ops = array('classname' => 'transfers_advanced_search_widget', 'description' => esc_html__('Transfers: Advanced Search Widget', 'transfers') );



		/* Widget control settings. */

		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_advanced_search_widget' );



		/* Create the widget. */

		parent::__construct( 'transfers_advanced_search_widget', esc_html__('Transfers: Advanced Search Widget', 'transfers'), $widget_ops, $control_ops );

	}

	

	/*-----------------------------------------------------------------------------------*/

	/*	Display Widget

	/*-----------------------------------------------------------------------------------*/

	

	function widget( $args, $instance ) {

	

		extract( $args );

		

		$date_format = Transfers_Plugin_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format')) . ' H:i';

		

		global $transfers_plugin_globals;



		/* Our variables from the widget settings. */

		$reverse_color_scheme = isset($instance['reverse_color_scheme']) ? (bool)$instance['reverse_color_scheme'] : false;

		$allowed_search_start_time = $transfers_plugin_globals->get_allowed_search_start_time();



		$allowedtags = transfers_get_allowed_widgets_tags_array();		

		

		/* Before widget (defined by themes). */

		echo wp_kses($before_widget, $allowedtags);



		$outer_class = 'color';

		if ((isset($widget_reverse_color_scheme) && $widget_reverse_color_scheme) || $reverse_color_scheme) {

			$outer_class = 'grey';

		}

		

		$form_action = '';

		$current_page_url = transfers_get_current_page_url();

		$advanced_search_url = get_permalink(transfers_get_current_language_page_id($transfers_plugin_globals->get_advanced_search_page_id()));

		

		if (isset($instance['search_results_page']))

			$form_action = get_permalink(transfers_get_current_language_page_id($instance['search_results_page']));

		elseif (!empty($advanced_search_url))

			$form_action = $advanced_search_url;

		else

			$form_action = $current_page_url;

					

		$destination1_from_id = null;

		if (isset($_GET['p1']) && !empty($_GET['p1']))

			$destination1_from_id = intval(wp_kses($_GET['p1'], ''));

		$destination1_to_id = null;

		if (isset($_GET['d1']) && !empty($_GET['d1']))

			$destination1_to_id = intval(wp_kses($_GET['d1'], ''));

			

		$destination2_from_id = null;

		if (isset($_GET['p2']) && !empty($_GET['p2']))

			$destination2_from_id = intval(wp_kses($_GET['p2'], ''));

		$destination2_to_id = null;

		if (isset($_GET['d2']) && !empty($_GET['d2']))

			$destination2_to_id = intval(wp_kses($_GET['d2'], ''));

			

		$people = null;

		if (isset($_GET['ppl']) && !empty($_GET['ppl']))

			$people = intval(wp_kses($_GET['ppl'], ''));

				

		$return_date = null;

		if (isset($_GET['ret']) && !empty($_GET['ret']))

			$return_date = isset($_GET['ret']) && !empty($_GET['ret']) ? date(TRANSFERS_PHP_DATE_FORMAT, strtotime(wp_kses($_GET['ret'], ''))) : null;

		

		$departure_date = null;

		if (isset($_GET['dep']) && !empty($_GET['dep']))

			$departure_date = isset($_GET['dep']) && !empty($_GET['dep']) ? date(TRANSFERS_PHP_DATE_FORMAT, strtotime(wp_kses($_GET['dep'], ''))) : null;

/*		else

			$departure_date = date(TRANSFERS_PHP_DATE_FORMAT);*/

			

		$trip = null;

		if (isset($_GET['trip']) && !empty($_GET['trip']))

			$trip = intval(wp_kses($_GET['trip'], ''));

		

		// $queried_destination_id = 0;

		// $queried_object = get_queried_object();

		// if (is_single() && isset($queried_object) && $queried_object->post_type == 'destination') {

			// $queried_destination_id = $queried_object->ID;

		// }			

		

		$select_destination_options = '<option value="">' . esc_html__('Select pickup location', 'transfers') . '</option>';

		$select_destination_options .= Transfers_Plugin_Utils::build_destination_select_recursively(null, $destination1_from_id);

		$select_pickup_location1 = '<select id="pickup1" name="p1">' . $select_destination_options . '</select>';

			

		$select_destination_options = '<option value="">' . esc_html__('Select drop-off location', 'transfers') . '</option>';

		$select_destination_options .= Transfers_Plugin_Utils::build_destination_select_recursively(null, $destination1_to_id, 0, false);

		$select_drop_off_location1 = '<select id="dropoff1" name="d1">' . $select_destination_options . '</select>';



		$select_destination_options = '<option value="">' . esc_html__('Select pickup location', 'transfers') . '</option>';

		$select_destination_options .= Transfers_Plugin_Utils::build_destination_select_recursively(null, $destination2_from_id);

		$select_pickup_location2 = '<select' . ($trip != 2 ? " disabled" : "") . ' id="pickup2" name="p2">' . $select_destination_options . '</select>';

			

		$select_destination_options = '<option value="">' . esc_html__('Select drop-off location', 'transfers') . '</option>';

		$select_destination_options .= Transfers_Plugin_Utils::build_destination_select_recursively(null, $destination2_to_id, 0, false);

		$select_drop_off_location2 = '<select' . ($trip != 2 ? " disabled" : "") . ' id="dropoff2" name="d2">' . $select_destination_options . '</select>';

			

		/* Display Widget */

		?>

		<!-- Advanced search -->

		<div class="advanced-search <?php echo esc_attr($outer_class); ?>" id="booking">

			<div class="wrap">

				<form role="form" action="<?php echo esc_url($form_action); ?>" method="get">

					<!-- Row -->

					<script>

						window.allowedSearchStartTime = <?php echo json_encode($allowed_search_start_time); ?>;

					</script>

					<div class="f-row f-row-repart">

						<div class="form-group datepicker one-third">

							<label for="departure-date"><i class="fa-light fa-calendar-clock"></i>&nbsp;<?php esc_html_e('Departure date and time', 'transfers'); ?></label>

							<input readonly type="text" class="departure-date" id="departure-date" value="<?php esc_html_e('Select date and time', 'transfers'); ?>">

						    <input type="hidden" name="dep" id="dep" value="<?php echo (isset($departure_date) ? esc_attr($departure_date) : ''); ?>" />

							<?php if (isset($departure_date)) { ?>

								<script>

							   	window.datepickerDepartureDateValue = '<?php echo esc_js(date(TRANSFERS_PHP_DATE_FORMAT, strtotime($departure_date))); ?>';

								</script>

							<?php } ?>

						</div>

						<div class="form-group select one-third">

							<label><i class="fa-light fa-location-dot"></i>&nbsp;<?php esc_html_e('Pick up location', 'transfers'); ?></label>

							<?php 

							$allowedtags = transfers_get_allowed_form_tags_array();

							echo wp_kses($select_pickup_location1, $allowedtags); ?>

						</div>

						<div class="form-group select one-third">

							<label><i class="fa-light fa-location-dot"></i>&nbsp;<?php esc_html_e('Drop off location', 'transfers'); ?></label>

							<?php echo wp_kses($select_drop_off_location1, $allowedtags); ?>

						</div>

					</div>

					<!-- //Row -->

					<!-- Row -->

					<div class="f-row f-row-return" <?php echo wp_kses(($trip != 2 ? ' style="display: none;"' : '') , array('style' => array())); ?>>

						<div class="form-group datepicker one-third">

							<label for="return-date"><i class="fa-light fa-calendar-clock"></i>&nbsp;<?php esc_html_e('Return date and time', 'transfers'); ?></label>

							<input readonly type="text" class="return-date" id="return-date" value="<?php esc_html_e('Select date and time', 'transfers'); ?>" <?php echo ($trip != 2 ? " disabled" : ""); ?>>

							<input type="hidden" name="ret" id="ret" <?php echo ($trip != 2 ? " disabled" : ""); ?> value="<?php echo (isset($return_date) ? esc_attr($return_date) : ''); ?>" />

							<?php if (isset($return_date)) { ?>

								<script>

								window.datepickerReturnDateValue = '<?php echo esc_js(date(TRANSFERS_PHP_DATE_FORMAT, strtotime($return_date))); ?>';

								</script>

							<?php } ?>

						</div>

						<div class="form-group select one-third">

							<label><i class="fa-light fa-location-dot"></i>&nbsp;<?php esc_html_e('Pick up location', 'transfers'); ?></label>

							<?php echo wp_kses($select_pickup_location2, $allowedtags); ?>

						</div>

						<div class="form-group select one-third">

							<label><i class="fa-light fa-location-dot"></i>&nbsp;<?php esc_html_e('Drop off location', 'transfers'); ?></label>

							<?php echo wp_kses($select_drop_off_location2, $allowedtags); ?>

						</div>

					</div>

					<!-- Row -->

					<div class="f-row">

						<div class="form-group spinner">

							<label for="people"><?php echo wp_kses(__('How many people <small>(including children)</small>?', 'transfers'), array('small' => array())) ?></label>

							<input type="number" id="people" name="ppl" min="1" class="uniform-input number" value="<?php echo (isset($people) ? esc_attr($people) : ''); ?>">

						</div>

						<div class="form-group radios">

							<div>

								<div class="radio" id="uniform-return"><span <?php echo wp_kses(($trip == 2 ? ' class="checked"' : ''), array('class' => array())); ?>><input type="radio" name="trip" id="return" value="2" <?php echo esc_html($trip == 2 ? 'checked' : ''); ?>></span></div>

								<label for="return"><?php esc_html_e('Return', 'transfers'); ?></label>

							</div>

							<div>

								<div class="radio" id="uniform-oneway"><span <?php echo wp_kses(($trip != 2 ? ' class="checked"' : ''), array('class' => array())); ?>><input type="radio" name="trip" id="oneway" value="1" <?php echo esc_html($trip != 2 ? 'checked' : ''); ?>></span></div>

								<label for="oneway"><?php esc_html_e('One way', 'transfers'); ?></label>

							</div>

						</div>

						<div class="form-group right">

							<button type="submit" class="btn large black"><?php esc_html_e('Find a transfer', 'transfers'); ?></button>

						</div>

					</div>

					<!--// Row -->

				</form>

			</div>

		</div>

		<!-- // Advanced search -->

		<?php

		/* After widget (defined by themes). */

		echo wp_kses($after_widget, $allowedtags);

	}





/*-----------------------------------------------------------------------------------*/

/*	Update Widget

/*-----------------------------------------------------------------------------------*/

	

	function update( $new_instance, $old_instance ) {

	

		$instance = $old_instance;



		$allowed_tags = array(

			'a' => array(

				'href' => array(),

				'title' => array()

			),

			'br' => array()

		);

		

		/* Strip tags to remove HTML (important for text inputs). */

		$instance['reverse_color_scheme'] = strip_tags( $new_instance['reverse_color_scheme'] );

		$instance['search_results_page'] = strip_tags( $new_instance['search_results_page'] );

		

		return $instance;

	}

	



/*-----------------------------------------------------------------------------------*/

/*	Widget Settings

/*-----------------------------------------------------------------------------------*/

	 

	function form( $instance ) {



		/* Set up some default widget settings. */

		

		$pages = get_pages(); 

		$pages_array = array();

		$pages_array[0] = esc_html__('Select page', 'transfers');

		foreach ( $pages as $page ) {

			$pages_array[$page->ID] = $page->post_title;

		}

		

		$defaults = array(

			'reverse_color_scheme' => false

		);

		

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>



		<!-- Widget Title: Text Input -->

		

		<p>

			<label for="<?php echo esc_attr( $this->get_field_id( 'reverse_color_scheme' ) ); ?>"><?php esc_html_e('Reverse color scheme?', 'transfers') ?></label>

			<input type="checkbox" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'reverse_color_scheme' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'reverse_color_scheme' ) ); ?>" value="1" <?php echo isset($instance['reverse_color_scheme']) && $instance['reverse_color_scheme'] ? 'checked="checked"' : ''; ?> />

		</p>



		<p>

			<label for="<?php echo esc_attr( $this->get_field_id( 'search_results_page' ) ); ?>"><?php esc_html_e('Advanced search results page', 'transfers') ?></label>

			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'search_results_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'search_results_page' ) ); ?>">

			<?php

			foreach ($pages_array as $id => $title) { ?>

				<option <?php echo isset($instance['search_results_page']) && $instance['search_results_page'] == $id ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></option>

			<?php

			}

			?>

			</select>

		</p>



		

	<?php

	}

}