<?php



/*

*******************************************************************************

************************** LOAD THE BASE CLASS *******************************

*******************************************************************************

* The WP_List_Table class isn't automatically available to plugins,

* so we need to check if it's available and load it if necessary.

*/

 

if(!class_exists('WP_List_Table')){

	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}



class Transfers_Bookings_Management_Admin extends Transfers_BaseSingleton {

	

	private $enable_destinations;

	private $enable_transport_types;

	

	protected function __construct() {

	

		global $transfers_plugin_globals;

		

		$this->enable_destinations = $transfers_plugin_globals->enable_destinations();

		$this->enable_transport_types = $transfers_plugin_globals->enable_transport_types();



        // our parent class might

        // contain shared code in its constructor

        parent::__construct();

	}



    public function init() {



		if ($this->enable_destinations && $this->enable_transport_types) {



			add_action( 'admin_menu' , array( $this, 'bookings_admin_page' ) );

			add_filter( 'set-screen-option', array( $this, 'bookings_set_screen_options' ), 10, 3);

			add_action( 'admin_head', array( $this, 'bookings_admin_head' ) );

		}

	}

	

	function bookings_admin_page() {



		$hook = add_menu_page(__('Transfers Bookings Management', 'transfers'), esc_html__('Transfers Bookings', 'transfers'), 'edit_posts', basename(__FILE__), array( $this, 'bookings_admin_display'), null, 3);

		add_action( "load-$hook", array( $this, 'bookings_add_screen_options') );

	}

	

	function bookings_set_screen_options($status, $option, $value) {

		if ( 'bookings_per_page' == $option ) 

			return $value;

	}

	

	function bookings_admin_head() {

		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;

		if( 'transfers_bookings_management_admin.php' != $page )

			return;



		$this->bookings_admin_styles();

	}

	

	function bookings_admin_styles() {

?>

		<script>

			window.adminAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

			window.datepickerDateFormat = '<?php echo Transfers_Plugin_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format')) ?>';

			window.datepickerAltFormat = '<?php echo TRANSFERS_DATEPICKER_ALT_DATE_FORMAT; ?>';



			(function($){



				$(document).ready(function () {

					tfBookingsAdmin.init();

				});

				

				var tfBookingsAdmin = {



					init: function () {

					

						if ($.fn.datetimepicker) {

							if (typeof($("#datepicker_booking_datetime")) != 'undefined') {

								$("#datepicker_booking_datetime").datetimepicker({

									dateFormat: window.datepickerDateFormat,

									altFormat: window.datepickerAltFormat,

									timeFormat: 'HH:mm',									

									altField: "#booking_datetime",

									altFieldTimeOnly: false,									

									showMillisec: false,

									showMicrosec: false,

									showTimezone: false,	

									addSliderAccess: true, 

									sliderAccessArgs: { touchonly: true }									

								});

								if (typeof(window.datepickerBookingDateValue) != 'undefined' && window.datepickerBookingDateValue.length > 0) {

									$('#datepicker_booking_datetime').datetimepicker("setDate", window.datepickerBookingDateValue);

									$('#datepicker_booking_datetime').datetimepicker("setTime", window.datepickerBookingDateValue);

								}

							}

							

						}

					}

				}

			})(jQuery);

		</script>

		<style type="text/css">

			.wp-list-table .column-Id { width: 100px; }

		</style>			

<?php

	}

		

	function bookings_add_screen_options() {

	

		global $wp_bookings_admin_table;

		$option = 'per_page';

		$args = array('label' => esc_html__('Bookings', 'transfers'),'default' => 50,'option' => 'bookings_per_page');

		add_screen_option( $option, $args );

		$wp_bookings_admin_table = new Bookings_Admin_List_Table();

	}



	function bookings_admin_display() {

	

		echo '</pre><div class="wrap">';

		echo '<h2>' . esc_html__('Transfers bookings management', 'transfers') . '</h2>';

		

		global $wp_bookings_admin_table;

		$entry_id = $wp_bookings_admin_table->handle_form_submit();

		

		if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {

			$wp_bookings_admin_table->render_entry_form($entry_id);

		} else {	

			$wp_bookings_admin_table->prepare_items();

			

			if (!empty($_REQUEST['s']))

				$form_uri = esc_url( add_query_arg( 's', $_REQUEST['s'], $_SERVER['REQUEST_URI'] ));

			else 

				$form_uri = esc_url($_SERVER['REQUEST_URI']);	

				

			if (function_exists('transfers_extra_tables_exist')) {

			

				if (!transfers_extra_tables_exist()) {

					echo wp_kses(sprintf(__('<strong>Warning:</strong> Transfers custom tables do not exist. Please navigate to <a href="%s">Theme options</a> and click the <strong>Create tables</strong> button in the Database tab to create them.', 'transfers'), admin_url('themes.php?page=options-framework')), array('strong' => array(), 'a' => array('href' => array())));

				} else {

					?>

					<div class="tablenav top">

						<div class="alignleft actions">

							<a href="admin.php?page=transfers_bookings_management_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Booking', 'transfers') ?></a>

						</div>

					</div>

					<form method="get" action="<?php echo esc_url($form_uri); ?>">
                    <!-- ordenar con ID -->

						<input type="hidden" name="paged" value="1">

						<input type="hidden" name="page" value="transfers_bookings_management_admin.php">

						<?php

						$wp_bookings_admin_table->search_box( 'search', 'search_id' );

						?>

					</form>

					<?php $wp_bookings_admin_table->display(); ?>

					<div class="tablenav bottom">

						<div class="alignleft actions">

							<a href="admin.php?page=transfers_bookings_management_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Booking', 'transfers') ?></a>

						</div>

					</div>

					<?php

				}

			}

		}

	}

}



global $transfers_bookings_management_admin;

$transfers_bookings_management_admin = Transfers_Bookings_Management_Admin::get_instance();



/************************** CREATE A PACKAGE CLASS *****************************

 *******************************************************************************

 * Create a new list table package that extends the core WP_List_Table class.

 * WP_List_Table contains most of the framework for generating the table, but we

 * need to define and override some methods so that our data can be displayed

 * exactly the way we need it to be.

 * 

 * To display this on a page, you will first need to instantiate the class,

 * then call $yourInstance->prepare_items() to handle any data manipulation, then

 * finally call $yourInstance->display() to render the table to the page.

 */

class Bookings_Admin_List_Table extends WP_List_Table {



	private $options;

	private $lastInsertedID;

	private $date_format;

	

	/**

	* Constructor, we override the parent to pass our own arguments.

	* We use the parent reference to set some default configs.

	*/

	function __construct() {

	

		global $status, $page;	

		$this->date_format = get_option('date_format');

	

		 parent::__construct( array(

			'singular'=> 'booking', // Singular label

			'plural' => 'bookings', // plural label, also this well be one of the table css class

			'ajax'	=> false // We won't support Ajax for this table

		) );		

	}	

	

	function column_default( $item, $column_name ) {

		return $item->$column_name;

	}	

	

	function column_Action($item) {

		return "<a href='admin.php?page=transfers_bookings_management_admin.php&sub=manage&edit=" . $item->Id . "'>" . esc_html__('Edit', 'transfers') . "</a> | 		

				<form method='post' name='delete_booking_" . $item->Id . "' id='delete_booking_" . $item->Id . "' style='display:inline;'>

					<input type='hidden' name='delete_booking' id='delete_booking' value='" . $item->Id . "' />

					<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_booking_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'transfers') . "\");'>" . esc_html__('Delete', 'transfers') . "</a>

				</form>";

	}

	

	function column_Name( $item ) {

		return $item->first_name . ' ' . $item->last_name;

	}

	

	function column_WooStatus( $item ) {

		return $item->woo_status;

	}

	

	function column_DestinationFrom( $item ) {

		return $item->destination_from;

	}

	

	function column_DestinationTo( $item ) {

		return $item->destination_to;

	}

	

	function column_TransportType( $item ) {

		return $item->transport_type;

	}

	

	function column_BookingDate( $item ) {

		return date($this->date_format, strtotime($item->booking_datetime));	

	}

	

	function column_BookingTime( $item ) {

		return date('H:i', strtotime($item->booking_datetime));	

	}

	

	function column_TotalPrice( $item ) {

		global $transfers_plugin_globals;

		

		$currency_symbol = $transfers_plugin_globals->get_default_currency_symbol();

		if ($transfers_plugin_globals->show_currency_symbol_after())

			return $item->total_price . $currency_symbol;

		else

			return $currency_symbol . $item->total_price;

	}

	

	function column_IsPrivate( $item ) {

		return $item->is_private == '1' ? esc_html__('yes', 'transfers') : esc_html__('no', 'transfers');

	}

	

	function extra_tablenav( $which ) {

		if ( $which == "top" ){	

			//The code that goes before the table is here

		}

		if ( $which == "bottom" ){

			//The code that goes after the table is there

		}

	}



	function column_WooOrder( $item ) {

		return isset($item->woo_order_id) ? "<a href='post.php?post_type=shop_order&post={$item->woo_order_id}&action=edit'>" . esc_html__('Woo Order', 'transfers') . "</a>" : '';

	}

		

	/**

	 * Define the columns that are going to be used in the table

	 * @return array $columns, the array of columns to use with the table

	 */

	function get_columns() {

	

		global $transfers_plugin_globals;

		

		$columns= array(

			'Id'=>__('Id', 'transfers'),

			'Name'=>__('Customer Name', 'transfers'),

			'DestinationFrom'=>__('From', 'transfers'),

			'DestinationTo'=>__('To', 'transfers'),

			'TransportType'=>__('Transport', 'transfers'),

			'BookingDate'=>__('Date', 'transfers'),

			'BookingTime'=>__('Time', 'transfers'),

			'TotalPrice'=>__('Total Price', 'transfers'),

			'IsPrivate'=>__('Is Private?', 'transfers'),

		);

		

		$use_woo_commerce_for_checkout = $transfers_plugin_globals->use_woocommerce_for_checkout();

		if ($use_woo_commerce_for_checkout) {

			$columns['WooOrder'] = esc_html__('Woo Order', 'transfers');

			$columns['WooStatus'] = esc_html__('Woo Status', 'transfers');

		}

		

		$columns['Action'] = esc_html__('Action', 'transfers');

		

		return $columns;

	}	

		

	/**

	 * Decide which columns to activate the sorting functionality on

	 * @return array $sortable, the array of columns that can be sorted by the user

	 */

	public function get_sortable_columns() {

		$sortable_columns = array(

			'Id'=> array( 'Id', true ),

			'DestinationFrom'=> array( 'destinations1.post_title', true ),

			'DestinationTo'=> array( 'destinations2.post_title', true ),

			'TransportType'=> array( 'transport_types.post_title', true ),

			'BookingDate'=> array( 'booking_datetime', true ),

			'TotalPrice'=> array( 'total_price', true ),

			'IsPrivate'=> array( 'is_private', true ),

		);

		return $sortable_columns;

	}	

	

	/**

	 * Prepare the table with different parameters, pagination, columns and table elements

	 */

	function prepare_items() {

	

		global $transfers_destinations_post_type, $transfers_transport_types_post_type, $transfers_plugin_post_types;

		global $_wp_column_headers;

		

		$screen = get_current_screen();

		$user = get_current_user_id();

		$option = $screen->get_option('per_page', 'option');

		$per_page = get_user_meta($user, $option, true);

		if ( empty ( $per_page) || $per_page < 1 ) {

			$per_page = $screen->get_option( 'per_page', 'default' );

		}	



		$search_term = '';

		if (!empty($_REQUEST['s'])) {

			$search_term = wp_kses(strtolower($_REQUEST['s']), '');

		}



		$columns = $this->get_columns(); 

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);		

		

		/* -- Ordering parameters -- */

		//Parameters that are going to be used to order the result

		$orderby = !empty($_GET["orderby"]) ? wp_kses($_GET["orderby"], '') : 'Id';

		$order = !empty($_GET["order"]) ? wp_kses($_GET["order"], '') : 'DESC';

		

		/* -- Pagination parameters -- */

		//How many to display per page?

		//Which page is this?

		$paged = !empty($_GET["paged"]) ? wp_kses($_GET["paged"], '') : '';

		//Page Number

		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

		//How many pages do we have in total?



		$author_id = null;

		if (!is_super_admin()) {

			$author_id = get_current_user_id();

		}

		

		$bookings_results = $transfers_plugin_post_types->list_booking_entries($paged, $per_page, $orderby, $order, $search_term, false);



		//Number of elements in your table?

		$totalitems = $bookings_results['total']; //return the total number of affected rows



		$totalpages = ceil($totalitems/$per_page);



		/* -- Register the pagination -- */

		$this->set_pagination_args( array(

			"total_items" => $totalitems,

			"total_pages" => $totalpages,

			"per_page" => $per_page,

		) );

		//The pagination links are automatically built according to those parameters



		/* -- Register the Columns -- */

		$columns = $this->get_columns();

		$_wp_column_headers[$screen->id]=$columns;



		/* -- Fetch the items -- */

		$this->items = $bookings_results['results'];

	}

	

	function handle_form_submit() {

	

		global $transfers_plugin_post_types, $transfers_plugin_globals;

		

		if (isset($_POST['delete_booking'])) {

			$entry_id = absint($_POST['delete_booking']);

			

			$transfers_plugin_post_types->delete_booking_entry($entry_id);

			

			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';

			echo '<p>' . esc_html__('Successfully deleted entry!', 'transfers') . '</p>';

			echo '</div>';

			

			return 0;			

		} else if (isset($_POST['insert']) || isset($_POST['update'])) {

		

			$user_id = get_current_user_id();

			$booking_datetime = sanitize_text_field($_POST['booking_datetime']);

			$availability_id = intval(wp_kses($_POST['availability_id'], ''));

			

			$people_count = intval(wp_kses($_POST['people_count'], ''));

			$total_price = isset($_POST['total_price']) ? floatval(wp_kses($_POST['total_price'], '')) : 0;

			$is_private = isset($_POST['is_private']) ? intval(wp_kses($_POST['is_private'], '')) : '0';

			

			$booking_args = array(			

				'user_id' => $user_id,

				'booking_datetime' => $booking_datetime,

				'availability_id' => $availability_id,

				'people_count' => $people_count,

				'is_private' => $is_private,

				'total_price' => $total_price,			

			);

			

			$availability_entry = $transfers_plugin_post_types->get_availability_entry($availability_id);

			$booking_args['from_id'] = $availability_entry->destination_from_id;

			$booking_args['to_id'] = $availability_entry->destination_to_id;

			

			$booking_form_fields = $transfers_plugin_globals->get_booking_form_fields();

			

			$booking_args['first_name'] = '';

			$booking_args['last_name'] = '';

			$booking_args['company'] = '';

			$booking_args['email'] = '';

			$booking_args['phone'] = '';

			$booking_args['address'] = '';

			$booking_args['address_2'] = '';

			$booking_args['town'] = '';

			$booking_args['zip'] = '';

			$booking_args['state'] = '';

			$booking_args['country'] = '';

			$booking_args['other_fields'] = array();

						

			foreach ($booking_form_fields as $form_field) {

			

				$field_id = $form_field['id'];

				

				if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) { 

					

					$field_value = sanitize_text_field($_REQUEST[$field_id]);



					switch ($field_id) {

						

						case 'first_name' 			: { $booking_args['first_name'] = $field_value; break; }

						case 'last_name' 			: { $booking_args['last_name'] = $field_value; break; }

						case 'company' 				: { $booking_args['company'] = $field_value; break; }						

						case 'email' 				: { $booking_args['email'] = $field_value; break; }

						case 'phone' 				: { $booking_args['phone'] = $field_value; break; }

						case 'address' 				: { $booking_args['address'] = $field_value; break; }

						case 'address_2' 			: { $booking_args['address_2'] = $field_value; break; }

						case 'town' 				: { $booking_args['town'] = $field_value; break; }

						case 'zip' 					: { $booking_args['zip'] = $field_value; break; }

						case 'state' 				: { $booking_args['state'] = $field_value; break; }

						case 'country' 				: { $booking_args['country'] = $field_value; break; }

						default : {

							$booking_args['other_fields'][$field_id] = $field_value;

							break;

						}

					}

				}

			}			

			

			if (isset($_POST['insert']) && check_admin_referer('booking_entry_form_nonce')) {

				

				$extra_items_array = array();

				$booking_args['extra_items'] = $extra_items_array;

				$entry_id = $transfers_plugin_post_types->create_booking_entry($booking_args);

				

				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';

				echo '<p>' . esc_html__('Successfully inserted new booking entry!', 'transfers') . '</p>';

				echo '</div>';				

			} else if (isset($_POST['update']) && check_admin_referer('booking_entry_form_nonce')) {



				$entry_id = isset($_POST['entry_id']) ? intval(wp_kses($_POST['entry_id'], '')) : 0;

				

				$booking_object = $transfers_plugin_post_types->get_booking_entry($entry_id);

				

				$booking_args['entry_id'] = $entry_id;

				$booking_args['total_price'] = $booking_object->total_price;

				

				$extra_items_array = $transfers_plugin_post_types->get_booking_entry_extra_items($entry_id);

				$booking_args['extra_items'] = $extra_items_array;

				

				$transfers_plugin_post_types->update_booking_entry($entry_id, $booking_args);



				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';

				echo '<p>' . esc_html__('Successfully updated booking entry!', 'transfers') . '</p>';

				echo '</div>';				

			}

			

			return $entry_id;

		}

	}

			

	function render_entry_form($entry_id) {

		

		global $transfers_plugin_post_types, $transfers_plugin_globals;

		

		$booking_object = null;

		

		$edit = isset($_GET['edit']) ? absint($_GET['edit']) : 0;

		if ($entry_id > 0)

			$edit = $entry_id;

		

		if (!empty($edit)) {

			$booking_object = $transfers_plugin_post_types->get_booking_entry($edit);

		}

		

		$booking_datetime = null;

		if (isset($_POST['booking_datetime']))

			$booking_datetime = sanitize_text_field($_POST['booking_datetime']);

		else if ($booking_object != null) {

			$booking_datetime = $booking_object->booking_datetime;

		}

		

		$availability_id = 0;

		if (isset($_POST['availability_id']))

			$availability_id = intval(wp_kses($_POST['availability_id'], ''));

		else if ($booking_object != null) {

			$availability_id = $booking_object->availability_id;

		}

		

		$people_count = 1;

		if (isset($_POST['people_count']))

			$people_count = intval(wp_kses($_POST['people_count'], ''));

		else if ($booking_object != null) {

			$people_count = $booking_object->people_count;

		}

		

		$total_price = 0;

		if (isset($_POST['total_price']))

			$total_price = floatval(wp_kses($_POST['total_price'], ''));

		else if ($booking_object != null) {

			$total_price = $booking_object->total_price;

		}

		

		$is_private = 0;

		if (isset($_POST['is_private']))

			$is_private = intval(wp_kses($_POST['is_private'], ''));

		else if ($booking_object != null) {

			$is_private = $booking_object->is_private;

		}



		if ($booking_object)

			echo '<h3>' . esc_html__('Update Booking Entry', 'transfers') . '</h3>';

		else

			echo '<h3>' . esc_html__('Add Booking Entry', 'transfers') . '</h3>';



		echo '<form id="booking_entry_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';

		

		echo wp_nonce_field('booking_entry_form_nonce');	

		

		echo '<table cellpadding="3" class="form-table"><tbody>';



		$availability_select = "<select name='availability_id' id='availability_id'>";

		$availability_entries = $transfers_plugin_post_types->list_availability_entries(null, 0, 'season_name', 'asc');

		if (count($availability_entries) && $availability_entries['total'] > 0) {

			foreach ($availability_entries['results'] as $availability) {

				$availability_select .= "<option value='$availability->Id'" . ($availability->Id == $availability_id ? " selected" : "") . ">$availability->season_name</option>";

			}		

		}

		$availability_select .= "</select>";



		echo '<tr>';

		echo '	<th scope="row" valign="top">' . esc_html__('Availability entry', 'transfers') . '</th>';

		echo '	<td>' . $availability_select . '</td>';

		echo '</tr>';





		$booking_object_other_fields = isset($booking_object->other_fields) ? unserialize($booking_object->other_fields) : array();

		$booking_form_fields = $transfers_plugin_globals->get_booking_form_fields();

		

		foreach ($booking_form_fields as $booking_field) {

		

			$field_type = $booking_field['type'];

			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;

			$field_id = $booking_field['id'];

			$field_required = isset($booking_field['required']) && $booking_field['required'] == '1' ? true : false;

			

			$field_value = '';

			

			if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'email' || $field_id == 'phone' || $field_id == 'address' || $field_id == 'town' || $field_id == 'zip' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {

				$field_value = isset($booking_object->{$field_id}) ? $booking_object->{$field_id} : '';

			} else {

				if (isset($booking_object_other_fields[$field_id]))

					$field_value = $booking_object_other_fields[$field_id];

			}



			if (!$field_hidden) {			

			

				echo '<tr>';

				echo '	<th scope="row" valign="top">' . esc_html($booking_field['label']) . '</th>';

				echo '	<td>';



				if ($field_type == 'email') {

					echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="email" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';

				} else if ($field_type == 'textarea') {

					echo '<textarea ' . ($field_required ? 'data-required' : '') . ' name="' . esc_attr($field_id) . '" id="' . esc_attr($field_id) . '" rows="5" cols="50" >' . esc_html($field_value) . '</textarea>';

				} else {

					echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';

				}

			}

			

			echo '  </td>';

			echo '</tr>';

		}		

		

		echo '<tr>';

		echo '	<th scope="row" valign="top">' . esc_html__('Booking date', 'transfers') . '</th>';

		echo '	<td>';

		if (isset($booking_datetime)) {

			echo '		<script>';

			echo '			window.datepickerBookingDateValue = "' . date( $this->date_format . ' H:i', strtotime( $booking_datetime ) ) . '";';

			echo '  	</script>';	

		}		

		echo '  	<input class="datepicker" type="text" name="datepicker_booking_datetime" id="datepicker_booking_datetime" />';

		echo '		<input type="hidden" name="booking_datetime" id="booking_datetime" value="' . $booking_datetime . '" />';

		echo '	</td>';	

		echo '</tr>';

		

		echo '<tr>';

		echo '	<th scope="row" valign="top">' . esc_html__('People count', 'transfers') . '</th>';

		echo '	<td><input type="number" step="1" name="people_count" id="people_count" value="' . $people_count . '" /></td>';

		echo '</tr>';

		

		echo '<tr>';

		echo '	<th scope="row" valign="top">' . esc_html__('Is private', 'transfers') . '</th>';

		echo '	<td><input type="checkbox" name="is_private" id="is_private" value="1" ' . ($is_private == '1' ? 'checked' : '') . ' /></td>';

		echo '</tr>';



		global $transfers_plugin_globals;

		$price_decimal_places = $transfers_plugin_globals->get_price_decimal_places();

		$default_currency_symbol = $transfers_plugin_globals->get_default_currency_symbol();

		$show_currency_symbol_after = $transfers_plugin_globals->show_currency_symbol_after();

		

		if ($edit) {



			$extra_items_array = $transfers_plugin_post_types->get_booking_entry_extra_items($edit);

			

			if (count($extra_items_array) && $transfers_plugin_globals->enable_extra_items()) {

				echo '<tr>';

				echo '	<th scope="row" valign="top">' . esc_html__('Extra items', 'transfers') . '</th>';

				echo '  <td>';



				echo '  	<table>';

				echo '<tr>';

				echo '	<th valign="top">' . esc_html__('Item', 'transfers') . '</th>';

				echo '	<th valign="top">' . esc_html__('Quantity', 'transfers') . '</th>';

				echo '	<th valign="top">' . esc_html__('Price', 'transfers') . '</th>';

				echo '</tr>';

				

				foreach ($extra_items_array as $extra_item) {

				

					$formatted_item_price = number_format_i18n( $extra_item->item_price, $price_decimal_places );

				

					echo '<tr>';

					echo '<td>' . $extra_item->extra_item . '</td>';

					echo '<td>' . $extra_item->quantity . '</td>';

					echo '<td>';

					if ($show_currency_symbol_after) {

						echo esc_html($formatted_item_price . ' ' . $default_currency_symbol . '');

					} else {

						echo esc_html('' . $default_currency_symbol . ' ' . $formatted_item_price);

					}

					echo '</td>';

					echo '</tr>';

				

				}

				echo '  	</table>';

				echo '  </td>';

				echo '</tr>';

			}			



			echo '<tr>';

			echo '	<th scope="row" valign="top">' . esc_html__('Total price', 'transfers') . '</th>';

			echo '	<td>';

			$formatted_total_price = number_format_i18n( $total_price, $price_decimal_places );

			if ($show_currency_symbol_after) {

				echo esc_html($formatted_total_price . ' ' . $default_currency_symbol . '');

			} else {

				echo esc_html('' . $default_currency_symbol . ' ' . $formatted_total_price);

			}

			echo '</td>';

			echo '</tr>';

		} else {

			echo '<tr>';

			echo '	<th scope="row" valign="top">' . esc_html__('Total price', 'transfers') . '</th>';

			echo '	<td><input type="number" step="any" name="total_price" id="total_price" value="' . $total_price . '" /></td>';

			echo '</tr>';

		}			

		

		echo '</table>';

		echo '<p>';

		echo '<a href="admin.php?page=transfers_bookings_management_admin.php" class="button-secondary">' . esc_html__('Cancel', 'transfers') . '</a>&nbsp;';



		if ($booking_object) {

			echo '<input id="entry_id" name="entry_id" value="' . $edit . '" type="hidden" />';

			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Booking', 'transfers') . '"/>';

		} else {

			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Booking', 'transfers') . '"/>';

		}

		echo '</p>';

		

		echo '</form>';			

	}

	

}