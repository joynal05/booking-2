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







							<label for="departure-date"><i class="fa-light fa-calendar-clock"></i>&nbsp;<?php esc_html_e('Flight Arrival', 'transfers'); ?></label>







							<input readonly type="text" class="departure-date" id="departure-date" value="<?php esc_html_e('Select date and time', 'transfers'); ?>">







							<input type="hidden" name="dep" id="dep" value="<?php echo (isset($departure_date) ? esc_attr($departure_date) : ''); ?>" />







							<?php if (isset($departure_date)) { ?>







								<script>







								window.datepickerDepartureDateValue = '<?php echo esc_js(date(TRANSFERS_PHP_DATE_FORMAT, strtotime($departure_date))); ?>';







								</script>







							<?php } ?>







						</div>







						<div class="form-group select one-third">







							<label><i class="fa-light fa-plane-arrival"></i>&nbsp;<?php esc_html_e('Arrival airport', 'transfers'); ?></label>







							<?php







							$allowedtags = transfers_get_allowed_form_tags_array();







						  //	echo wp_kses($select_pickup_location1, $allowedtags); ?>







                          <?php echo __('<select id="pickup1" name="p1">







      <option value="" disabled selected hidden>Select airport</option>







      <optgroup label="Gran Canaria">







      <option value="298">Gran Canaria Airport</option>







      <optgroup label="Tenerife">







      <option value="340">Tenerife North Airport</option>







      <option value="338">Tenerife South Airport</option>







      </optgroup></select>','transfers'); ?>















						</div>







						<div class="form-group select one-third">







							<label><i class="fa-light fa-location-dot"></i>&nbsp;<?php esc_html_e('Drop off location', 'transfers'); ?></label>







						   	<?php //echo wp_kses($select_drop_off_location1, $allowedtags); ?>







<select id="dropoff1" name="d1"><option value="">Seleccione lugar de destino</option>



<optgroup label="Gran Canaria"><option value="317">Agaete</option><option value="318">Agüimes</option><option value="320">Amadores</option><option value="321">Arguineguín</option><option value="532">Bahía Feliz</option><option value="538">Campo de Golf International</option><option value="534">Campo de Golf Maspalomas</option><option value="536">Campo de Golf Meloneras</option><option value="540">Costa Meloneras</option><option value="542">Las Palmas de Gran Canaria</option><option value="544">Maspalomas</option><option value="546">Mogán</option><option value="548">Patalvaca</option><option value="550">Playa del Aguila</option><option value="552">Playa del Cura</option><option value="554">Playa del Inglés</option><option value="556">Puerto Rico</option><option value="558">Salobre Golf Resort</option><option value="560">San Agustín</option><option value="564">Sonnenland</option><option value="567">Taurito</option><option value="569">Tauro</option><option value="571">Tejeda</option></optgroup><optgroup label="Tenerife"><option value="767">Abades</option><option value="777">Abama</option><option value="785">Acantilados de Los Gigantes</option><option value="1084">Adeje</option><option value="1088">Alcalá</option><option value="799">Amarilla Golf</option><option value="803">Arafo</option><option value="1248">Arico</option><option value="812">Arona</option><option value="813">Bahía Príncipe Tenerife</option><option value="817">Bajamar</option><option value="821">Benijo</option><option value="826">Buenavista del Norte</option><option value="830">Callao Salvaje</option><option value="834">Candelaria</option><option value="838">Chayofa</option><option value="842">Chío</option><option value="846">Costa Adeje</option><option value="850">Costa del Silencio</option><option value="1096">El Abrigo</option><option value="854">El Médano</option><option value="860">EL Salto</option><option value="864">El Sauzal</option><option value="868">El Tanque</option><option value="872">Fañabé</option><option value="876">Frontos</option><option value="880">Garachico</option><option value="884">Golf de Las Américas</option><option value="888">Golf del Sur</option><option value="892">Granadilla de Abona</option><option value="896">Guamasa</option><option value="900">Guargacho</option><option value="905">Guía de Isora</option><option value="909">Güimar</option><option value="916">La Caleta de Adeje</option><option value="918">La Esperanza</option><option value="922">La Laguna</option><option value="926">La Matanza de Acentejo</option><option value="930">La Orotava</option><option value="935">Las Caletillas</option><option value="939">Las Cañadas del Teide</option><option value="945">Las Galletas</option><option value="949">Los Cristianos</option><option value="954">Los Cristianos Puerto</option><option value="958">Los Realejos</option><option value="1100">Marazul</option><option value="962">Masca</option><option value="966">Palm Mar</option><option value="986">Playa de Fañabé</option><option value="990">Playa de la Arena</option><option value="994">Playa de Las Américas</option><option value="1104">Playa del Duque</option><option value="970">Playa Floral</option><option value="977">Playa Paraíso</option><option value="978">Playa San Juan</option><option value="982">Playa San Marcos</option><option value="1002">Puerto de La Cruz</option><option value="998">Puerto de Santiago</option><option value="1006">Punta del Hidalgo</option><option value="1010">San Andrés</option><option value="1014">San Bernardo</option><option value="1108">San Cristóbal de La Laguna</option><option value="1018">San Eugenio</option><option value="1023">San Isidro</option><option value="1027">San Juan de La Rambla</option><option value="1031">San Miguel</option><option value="1035">Santa Cruz (Puerto)</option><option value="1039">Santa Cruz de Tenerife</option><option value="1043">Santa Ursula</option><option value="1047">Santiago del Teide</option><option value="1051">Tacoronte</option><option value="1055">Tamaimo</option><option value="1059">Taucho</option><option value="1067">Tegueste</option><option value="1068">Tejina de Guía</option><option value="1072">Torviscas</option><option value="1076">Valle de Guerra</option><option value="1083">Vilaflor</option></optgroup></select>







						</div>







					</div>







					<!-- //Row -->







					<!-- Row -->







					<div class="f-row f-row-return" <?php echo wp_kses(($trip != 2 ? ' style="display: none;"' : '') , array('style' => array())); ?>>







						<div class="form-group datepicker one-third">







							<label for="return-date"><i class="fa-light fa-calendar-clock"></i>&nbsp;<?php esc_html_e('Flight Departure', 'transfers'); ?></label>







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







							<?php // echo wp_kses($select_pickup_location2, $allowedtags); ?>







                            <select id="pickup2" name="d2"><option value="">Seleccione lugar de destino</option>







                            <optgroup label="Gran Canaria"><option value="317">Agaete</option><option value="318">Agüimes</option><option value="320">Amadores</option><option value="321">Arguineguín</option><option value="532">Bahía Feliz</option><option value="538">Campo de Golf International</option><option value="534">Campo de Golf Maspalomas</option><option value="536">Campo de Golf Meloneras</option><option value="540">Costa Meloneras</option><option value="542">Las Palmas de Gran Canaria</option><option value="544">Maspalomas</option><option value="546">Mogán</option><option value="548">Patalvaca</option><option value="550">Playa del Aguila</option><option value="552">Playa del Cura</option><option value="554">Playa del Inglés</option><option value="556">Puerto Rico</option><option value="558">Salobre Golf Resort</option><option value="560">San Agustín</option><option value="564">Sonnenland</option><option value="567">Taurito</option><option value="569">Tauro</option><option value="571">Tejeda</option></optgroup><optgroup label="Tenerife"><option value="767">Abades</option><option value="777">Abama</option><option value="785">Acantilados de Los Gigantes</option><option value="1084">Adeje</option><option value="1088">Alcalá</option><option value="799">Amarilla Golf</option><option value="803">Arafo</option><option value="1248">Arico</option><option value="812">Arona</option><option value="813">Bahía Príncipe Tenerife</option><option value="817">Bajamar</option><option value="821">Benijo</option><option value="826">Buenavista del Norte</option><option value="830">Callao Salvaje</option><option value="834">Candelaria</option><option value="838">Chayofa</option><option value="842">Chío</option><option value="846">Costa Adeje</option><option value="850">Costa del Silencio</option><option value="1096">El Abrigo</option><option value="854">El Médano</option><option value="860">EL Salto</option><option value="864">El Sauzal</option><option value="868">El Tanque</option><option value="872">Fañabé</option><option value="876">Frontos</option><option value="880">Garachico</option><option value="884">Golf de Las Américas</option><option value="888">Golf del Sur</option><option value="892">Granadilla de Abona</option><option value="896">Guamasa</option><option value="900">Guargacho</option><option value="905">Guía de Isora</option><option value="909">Güimar</option><option value="916">La Caleta de Adeje</option><option value="918">La Esperanza</option><option value="922">La Laguna</option><option value="926">La Matanza de Acentejo</option><option value="930">La Orotava</option><option value="935">Las Caletillas</option><option value="939">Las Cañadas del Teide</option><option value="945">Las Galletas</option><option value="949">Los Cristianos</option><option value="954">Los Cristianos Puerto</option><option value="958">Los Realejos</option><option value="1100">Marazul</option><option value="962">Masca</option><option value="966">Palm Mar</option><option value="986">Playa de Fañabé</option><option value="990">Playa de la Arena</option><option value="994">Playa de Las Américas</option><option value="1104">Playa del Duque</option><option value="970">Playa Floral</option><option value="977">Playa Paraíso</option><option value="978">Playa San Juan</option><option value="982">Playa San Marcos</option><option value="1002">Puerto de La Cruz</option><option value="998">Puerto de Santiago</option><option value="1006">Punta del Hidalgo</option><option value="1010">San Andrés</option><option value="1014">San Bernardo</option><option value="1108">San Cristóbal de La Laguna</option><option value="1018">San Eugenio</option><option value="1023">San Isidro</option><option value="1027">San Juan de La Rambla</option><option value="1031">San Miguel</option><option value="1035">Santa Cruz (Puerto)</option><option value="1039">Santa Cruz de Tenerife</option><option value="1043">Santa Ursula</option><option value="1047">Santiago del Teide</option><option value="1051">Tacoronte</option><option value="1055">Tamaimo</option><option value="1059">Taucho</option><option value="1067">Tegueste</option><option value="1068">Tejina de Guía</option><option value="1072">Torviscas</option><option value="1076">Valle de Guerra</option><option value="1083">Vilaflor</option></optgroup></select>







						</div>







						<div class="form-group select one-third">







							<label><i class="fa-light fa-plane-departure"></i>&nbsp;<?php esc_html_e('Departure airport', 'transfers'); ?></label>







							<?php // echo wp_kses($select_drop_off_location2, $allowedtags); ?>







                            <?php echo __('<select id="dropoff2" name="p2">







      <option value="" disabled selected hidden>Select airport</option>







      <optgroup label="Gran Canaria">







      <option value="298">Gran Canaria Airport</option>







      <optgroup label="Tenerife">







      <option value="340">Tenerife North Airport</option>







      <option value="338">Tenerife South Airport</option>







      </optgroup></select>','transfers'); ?>







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