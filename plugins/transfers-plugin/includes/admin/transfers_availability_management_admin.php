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

class Transfers_Availability_Management_Admin extends Transfers_BaseSingleton {
	
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

			add_action( 'admin_menu' , array( $this, 'availability_admin_page' ) );
			add_filter( 'set-screen-option', array( $this, 'availability_set_screen_options' ), 10, 3);
			add_action( 'admin_head', array( $this, 'availability_admin_head' ) );
		}
	}
	
	function availability_admin_page() {

		$hook = add_menu_page( __('Transfers Availability Management', 'transfers'), __('Transfers Availability', 'transfers'), 'edit_posts', basename(__FILE__), array( $this, 'availability_admin_display'), null, 4);
		add_action( "load-$hook", array( $this, 'availability_add_screen_options') );
	}
	
	function availability_set_screen_options($status, $option, $value) {
		if ( 'availability_per_page' == $option ||
			'StartDateTime' == $option ||
			'EndDateTime' == $option ||
			'DestinationFrom' == $option ||
			'DestinationTo' == $option ||
			'TransportType' == $option ||
			'AvailableVehicles' == $option ||
			'SlotMinutes' == $option) 
			return $value;
		return $status;
	}
	
	function availability_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'transfers_availability_management_admin.php' != $page )
			return;

		$this->availability_admin_styles();
	}
	
	function availability_admin_styles() {
?>
		<script>
			window.adminAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
			window.datepickerDateFormat = '<?php echo Transfers_Plugin_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format')) ?>';
			window.datepickerAltFormat = '<?php echo TRANSFERS_DATEPICKER_ALT_DATE_FORMAT; ?>';

			(function($){

				$(document).ready(function () {
					tfAvailabilityAdmin.init();
				});
				
				var tfAvailabilityAdmin = {

					init: function () {
						if ($.fn.datepicker) {
							if (typeof($("#datepicker_start_datetime")) != 'undefined') {
								$("#datepicker_start_datetime").datepicker({
									dateFormat: window.datepickerDateFormat,
									altFormat: window.datepickerAltFormat,
									altField: "#start_datetime",
								});
								if (typeof(window.datepickerStartDateValue) != 'undefined' && window.datepickerStartDateValue.length > 0) {
									$('#datepicker_start_datetime').datepicker("setDate", window.datepickerStartDateValue);
								}
							}
							
							if (typeof($("#datepicker_end_datetime")) != 'undefined') {
								$("#datepicker_end_datetime").datepicker({
									dateFormat: window.datepickerDateFormat,
									altFormat: window.datepickerAltFormat,
									altField: "#end_datetime",
								});
								if (typeof(window.datepickerEndDateValue) != 'undefined' && window.datepickerEndDateValue.length > 0) {
									$('#datepicker_end_datetime').datepicker("setDate", window.datepickerEndDateValue);
								}
							}
						}
					}
				}
			})(jQuery);
		</script>
		<style type="text/css">
			.wp-list-table .column-Id { width: 30px; }
			.wp-list-table .column-SeasonName { width: 200px; }
		</style>			
<?php
	}
		
	function availability_add_screen_options() {
	
		global $wp_availability_admin_table;
		$option = 'per_page';
		$args = array('label' => esc_html__('Availability', 'transfers'),'default' => 50,'option' => 'availability_per_page');
		add_screen_option( $option, $args );
		$wp_availability_admin_table = new Availability_Admin_List_Table();
	}

	function availability_admin_display() {
	
		echo '</pre><div class="wrap">';
		echo '<h2>' . esc_html__('Transfers availability management', 'transfers') . '</h2>';
		
		global $wp_availability_admin_table;
		$entry_id = $wp_availability_admin_table->handle_form_submit();
		
		if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {
			$wp_availability_admin_table->render_entry_form($entry_id); 
		} else {	
			$wp_availability_admin_table->prepare_items(); 
			
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
						<a href="admin.php?page=transfers_availability_management_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Availability', 'transfers') ?></a>
					</div>
				</div>
				<form method="get" action="<?php echo esc_url($form_uri); ?>">
					<input type="hidden" name="paged" value="1">
					<input type="hidden" name="page" value="transfers_availability_management_admin.php">
					<?php
					$wp_availability_admin_table->search_box( 'search', 'search_id' );
					?>
				</form>
				<?php
					$wp_availability_admin_table->display(); 
				?>
				<div class="tablenav bottom">	
					<div class="alignleft actions">
						<a href="admin.php?page=transfers_availability_management_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Availability', 'transfers') ?></a>
					</div>
				</div>
				<?php
				} 
			}
		}
	}
}

global $transfers_availability_management_admin;
$transfers_availability_management_admin = Transfers_Availability_Management_Admin::get_instance();

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
class Availability_Admin_List_Table extends WP_List_Table {

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
			'singular'=> 'availability', // Singular label
			'plural' => 'availabilities', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		) );		
	}	
	
	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}	
	
	function column_Action($item) {
		return "<a href='admin.php?page=transfers_availability_management_admin.php&sub=manage&edit=" . $item->Id . "'>" . esc_html__('Edit', 'transfers') . "</a> | 		
				<form method='post' name='delete_availability_" . $item->Id . "' id='delete_availability_" . $item->Id . "' style='display:inline;'>
					<input type='hidden' name='delete_availability' id='delete_availability' value='" . $item->Id . "' />
					<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_availability_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'transfers') . "\");'>" . esc_html__('Delete', 'transfers') . "</a>
				</form>";
	}

	function column_SeasonName( $item ) {
	
		$entry_type = '';
		
		switch ($item->entry_type) {
			case "byminute" :
				$entry_type = esc_html__('Every X minutes','transfers');
				break;
			case "daily" :
				$entry_type = esc_html__('Every day','transfers');
				break;
			case "weekly" :
				$entry_type = esc_html__('Once a week','transfers');
				break;
			case "monthly" :
				$entry_type = esc_html__('Once a month','transfers');
				break;
		}
		
		return $item->season_name . (!empty($entry_type) ? '<br /> (' . $entry_type . ')' : '');
	}
	
	function column_DestinationFrom( $item ) {
		return isset($item->destination_from) && $item->destination_from !== 'ANY' ? $item->destination_from : __('Any', 'transfers');
	}
	
	function column_DestinationTo( $item ) {
		return isset($item->destination_to) && $item->destination_to !== 'ANY' ? $item->destination_to : __('Any', 'transfers');
	}
	
	function column_TransportType( $item ) {
		return isset($item->transport_type) && $item->transport_type !== 'ANY' ? $item->transport_type : __('Any', 'transfers');
	}
	
	function column_AvailableVehicles( $item ) {
		return $item->available_vehicles;
	}
	
	function column_SlotMinutes( $item ) {
	
		if ($item->entry_type != 'byminute') {
			$h = $item->slot_minutes / 60;
			$m = $item->slot_minutes % 60;
			$hours = sprintf("%02d", $h);
			$minutes = sprintf("%02d", $m);
			return "$hours:$minutes";	
		} else {
			$m = $item->slot_minutes;
			return sprintf(esc_html__("Every %d minutes", "transfers"), $m);	
		}	
	}
	
	function column_Price( $item ) {
		global $transfers_plugin_globals;
		
		$currency_symbol = $transfers_plugin_globals->get_default_currency_symbol();
		if ($transfers_plugin_globals->show_currency_symbol_after())
			return ($item->price_private . $currency_symbol) . ($transfers_plugin_globals->enable_shared_transfers() ? ' / ' . ($item->price_share . $currency_symbol) : '');
		else
			return ($currency_symbol . $item->price_private) . ($transfers_plugin_globals->enable_shared_transfers() ? ' / ' . ($currency_symbol . $item->price_share)  : '');
	}
	
	function column_StartDateTime( $item ) {
		return date($this->date_format, strtotime($item->start_datetime));	
	}
	
	function column_EndDateTime( $item ) {
		return date($this->date_format, strtotime($item->end_datetime));	
	}
	
	function extra_tablenav( $which ) {
		if ( $which == "top" ){	
		
			if (function_exists('transfers_extra_tables_exist')) {
			
				if (!transfers_extra_tables_exist()) {
					_e('Transfers custom tables do not exist. Please navigate to Theme options and click the Create tables button in the Database tab to create them.', 'transfers');
				}
			}
		
			//The code that goes before the table is here
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
		}
	}		
		
	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		global $transfers_plugin_globals;	
		return $columns= array(
			'Id'=>__('Id', 'transfers'),
			'SeasonName'=>__('Name', 'transfers'),
			'StartDateTime'=>__('Start', 'transfers'),
			'EndDateTime'=>__('End', 'transfers'),
			'DestinationFrom'=>__('From', 'transfers'),
			'DestinationTo'=>__('To', 'transfers'),
			'TransportType'=>__('Transport', 'transfers'),
			'AvailableVehicles'=>__('Available', 'transfers'),
			'SlotMinutes'=>__('Time', 'transfers'),
			'Price'=> ($transfers_plugin_globals->enable_shared_transfers() ? __('Prices (private/share)', 'transfers') : __('Prices', 'transfers')),
			'Action'=>__('Action', 'transfers'),
		);
	}	
		
	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'Id'=> array( 'Id', true ),
			'SeasonName'=> array( 'season_name', true ),
			'StartDateTime'=> array( 'start_datetime', true ),
			'EndDateTime'=> array( 'end_datetime', true ),
			'DestinationFrom'=> array( 'destinations1.post_title', true ),
			'DestinationTo'=> array( 'destinations2.post_title', true ),
			'TransportType'=> array( 'transport_types.post_title', true ),
			'SlotMinutes'=> array( 'slot_minutes', true ),
			'AvailableVehicles'=> array( 'available_vehicles', true ),
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
		$hidden = get_hidden_columns( $screen );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);		
		
		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? wp_kses($_GET["orderby"], '') : 'Id';
		$order = !empty($_GET["order"]) ? wp_kses($_GET["order"], '') : 'ASC';
		
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
		
		$availability_results = $transfers_plugin_post_types->list_availability_entries($paged, $per_page, $orderby, $order, $search_term, false);

		//Number of elements in your table?
		$totalitems = $availability_results['total']; //return the total number of affected rows

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
		$this->items = $availability_results['results'];
	}
	
	function handle_form_submit() {
	
		global $transfers_plugin_post_types;
		
		if (isset($_POST['delete_availability'])) {
			$entry_id = absint($_POST['delete_availability']);
			
			$transfers_plugin_post_types->delete_availability_entry($entry_id);
			
			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted entry!', 'transfers') . '</p>';
			echo '</div>';
			
			return 0;			
		} else if (isset($_POST['insert']) || isset($_POST['update'])) {
		
			$entry_id = isset($_POST['entry_id']) ? intval(wp_kses($_POST['entry_id'], '')) : 0;

			$destination_from_id = isset($_POST['destination_from_id']) ? intval(wp_kses($_POST['destination_from_id'], '')) : 0;	
			$destination_to_id = isset($_POST['destination_to_id']) ? intval(wp_kses($_POST['destination_to_id'], '')) : 0;	
			$transport_type_id = isset($_POST['transport_type_id']) ? intval(wp_kses($_POST['transport_type_id'], '')) : 0;	
			
			$destination_from_obj = new transfers_destination((int)$destination_from_id);
			$destination_to_obj = new transfers_destination((int)$destination_to_id);
			$transport_type_obj = new transfers_destination((int)$transport_type_id);
			
			$start_datetime = sanitize_text_field($_POST['start_datetime']);
			$end_datetime = sanitize_text_field($_POST['end_datetime']);
			
			$user_id = get_current_user_id();		
			
			$available_vehicles = intval(wp_kses($_POST['available_vehicles'], ''));
			$duration_minutes = intval(wp_kses($_POST['duration_minutes'], ''));
			
			$price_private = floatval(wp_kses($_POST['price_private'], ''));
			$price_share = isset($_POST['price_share']) ? floatval(wp_kses($_POST['price_share'], '')) : 0;
			
			$season_name = sanitize_text_field($_POST['season_name']);
			$entry_type = sanitize_text_field($_POST['entry_type']);
			
			$day_index = null;
			if ($entry_type == "weekly")
				$day_index = isset($_POST['day_index_week']) ? intval(wp_kses($_POST['day_index_week'], '')) : 0;
			else if ($entry_type == "monthly")
				$day_index = isset($_POST['day_index_month']) ? intval(wp_kses($_POST['day_index_month'], '')) : 0;
				
			if ($entry_type != 'byminute') {
				$slot_minutes = sanitize_text_field($_POST['slot_minutes']);
			} else {
				$slot_minutes = sanitize_text_field($_POST['slot_minutes_byminute']);
			}
			
			if (isset($_POST['insert']) && check_admin_referer('availability_entry_form_nonce')) {
				
				$entry_id = $transfers_plugin_post_types->create_availability_entry($entry_type, $season_name, $destination_from_id, $destination_to_id, $transport_type_id, $slot_minutes, $available_vehicles, $price_private, $price_share, $duration_minutes, $day_index, $start_datetime, $end_datetime);
				
				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully inserted new availability entry!', 'transfers') . '</p>';
				echo '</div>';				
			} else if (isset($_POST['update']) && check_admin_referer('availability_entry_form_nonce')) {
				
				$transfers_plugin_post_types->update_availability_entry($entry_id, $entry_type, $season_name, $destination_from_id, $destination_to_id, $transport_type_id, $slot_minutes, $available_vehicles, $price_private, $price_share, $duration_minutes, $day_index, $start_datetime, $end_datetime);

				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully updated availability entry!', 'transfers') . '</p>';
				echo '</div>';				
			}
			
			return $entry_id;
		}
	}
			
	function render_entry_form($entry_id) {
		
		global $transfers_plugin_post_types;
		
		$availability_object = null;
		
		$edit = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
		if ($entry_id > 0)
			$edit = $entry_id;
		
		if (!empty($edit)) {
			$availability_object = $transfers_plugin_post_types->get_availability_entry($edit);
		}
		
		$slot_minutes = null;
		
		$entry_type = 'daily';
		if (isset($_POST['entry_type']))
			$entry_type = sanitize_text_field($_POST['entry_type']);
		else if ($availability_object != null) {
			$entry_type = $availability_object->entry_type;
		}
		
		if ($entry_type != 'byminute') {
			if (isset($_POST['slot_minutes']))
				$slot_minutes = sanitize_text_field($_POST['slot_minutes']);
			else if ($availability_object != null) {
				$slot_minutes = $availability_object->slot_minutes;
			}
			if (isset($slot_minutes))
				$slot_minutes = intval( $slot_minutes );
		} else {
			if (isset($_POST['slot_minutes_byminute']))
				$slot_minutes = sanitize_text_field($_POST['slot_minutes_byminute']);
			else if ($availability_object != null) {
				$slot_minutes = $availability_object->slot_minutes;
			}
			if (isset($slot_minutes))
				$slot_minutes = intval( $slot_minutes );
		}
			
		$season_name = '';
		if (isset($_POST['season_name']))
			$season_name = sanitize_text_field($_POST['season_name']);
		else if ($availability_object != null) {
			$season_name = $availability_object->season_name;
		}
		
		$start_datetime = null;
		if (isset($_POST['start_datetime']))
			$start_datetime = sanitize_text_field($_POST['start_datetime']);
		else if ($availability_object != null) {
			$start_datetime = $availability_object->start_datetime;
		}
			
		$end_datetime = null;
		if (isset($_POST['end_datetime']))
			$end_datetime = sanitize_text_field($_POST['end_datetime']);
		else if ($availability_object != null) {
			$end_datetime = $availability_object->end_datetime;
		}
		
		$day_index = 0;
		if (isset($_POST['day_index']))
			$day_index = intval(wp_kses($_POST['day_index'], ''));
		else if ($availability_object != null) {
			$day_index = $availability_object->day_index;
		}
		
		$available_vehicles = 1;
		if (isset($_POST['available_vehicles']))
			$available_vehicles = intval(wp_kses($_POST['available_vehicles'], ''));
		else if ($availability_object != null) {
			$available_vehicles = $availability_object->available_vehicles;
		}
		
		$price_private = 0;
		if (isset($_POST['price_private']))
			$price_private = floatval(wp_kses($_POST['price_private'], ''));
		else if ($availability_object != null) {
			$price_private = $availability_object->price_private;
		}
		
		global $transfers_plugin_globals;
		
		$price_share = 0;
		if ($transfers_plugin_globals->enable_shared_transfers()) {
			if (isset($_POST['price_share']))
				$price_share = floatval(wp_kses($_POST['price_share'], ''));
			else if ($availability_object != null) {
				$price_share = $availability_object->price_share;
			}
		}
		
		$duration_minutes = 1;
		if (isset($_POST['duration_minutes']))
			$duration_minutes = intval(wp_kses($_POST['duration_minutes'], ''));
		else if ($availability_object != null) {
			$duration_minutes = $availability_object->duration_minutes;
		}
				
		$destination_from_id = -1;
		if (isset($_POST['destination_from_id'])) {
			$destination_from_id = intval($_POST['destination_from_id']);
		} else if ($availability_object != null) {
			$destination_from_id = $availability_object->destination_from_id;
		}
		
		$destination_to_id = -1;
		if (isset($_POST['destination_to_id'])) {
			$destination_to_id = intval($_POST['destination_to_id']);
		} else if ($availability_object != null) {
			$destination_to_id = $availability_object->destination_to_id;
		}
		
		$transport_type_id = -1;
		if (isset($_POST['transport_type_id'])) {
			$transport_type_id = intval($_POST['transport_type_id']);
		} else if ($availability_object != null) {
			$transport_type_id = $availability_object->transport_type_id;
		}

		if ($availability_object)
			echo '<h3>' . esc_html__('Update Availability Entry', 'transfers') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Availability Entry', 'transfers') . '</h3>';

		echo '<form id="availability_entry_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';
		
		echo wp_nonce_field('availability_entry_form_nonce');	
		
		echo '<table cellpadding="3" class="form-table"><tbody>';

		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season name', 'transfers') . '</th>';
		echo '	<td><input type="text" name="season_name" id="season_name" value="' . $season_name . '" /></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season start', 'transfers') . '</th>';
		echo '	<td>';
		if (isset($start_datetime)) {
			echo '		<script>';
			echo '			window.datepickerStartDateValue = "' . date($this->date_format, strtotime($start_datetime)) . '";';
			echo '  	</script>';
		}
		echo '  	<input class="datepicker" type="text" name="datepicker_start_datetime" id="datepicker_start_datetime" />';
		echo '		<input type="hidden" name="start_datetime" id="start_datetime" value="' . $start_datetime . '" />';
		echo '	</td>';	
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season end', 'transfers') . '</th>';
		echo '	<td>';
		if (isset($end_datetime)) {
			echo '		<script>';
			echo '			window.datepickerEndDateValue = "' . date($this->date_format, strtotime($end_datetime)) . '";';
			echo '  	</script>';	
		}
		echo '  	<input class="datepicker" type="text" name="datepicker_end_datetime" id="datepicker_end_datetime" />';
		echo '		<input type="hidden" name="end_datetime" id="end_datetime" value="' . $end_datetime . '" />';
		echo '	</td>';	
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Entry type', 'transfers') . '</th>';
		echo '	<td>';
		echo '		<select name="entry_type" id="entry_type">';
		echo "			<option value='byminute'" . ($entry_type == 'byminute' ? "selected" : "") . ">" . esc_html__('Every X minutes','transfers') . '</option>';
		echo "			<option value='daily'" . ($entry_type == 'daily' ? "selected" : "") . ">" . esc_html__('Every day','transfers') . '</option>';
		echo "			<option value='weekly'" . ($entry_type == 'weekly' ? "selected" : "") . ">" . esc_html__('Once a week','transfers') . '</option>';
		echo "			<option value='monthly'" . ($entry_type == 'monthly' ? "selected" : "") . ">" . esc_html__('Once a month','transfers') . '</option>';
		echo "		</select>";
		echo '</td>';
		echo '</tr>';
		
		echo '<tr id="by_minute_row" ' . (!empty($entry_type) && $entry_type != 'byminute' ? "style='display:none'" : "") . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('Every how many minutes?', 'transfers') . '</th>';
		echo '	<td>';
		echo '		<select name="slot_minutes_byminute" id="slot_minutes_byminute">';
		
		echo "<option value='1'" . ($slot_minutes == 1 ? " selected='selected'" : "") . ">1</option>";
		echo "<option value='5'" . ($slot_minutes == 5 ? " selected='selected'" : "") . ">5</option>";
		echo "<option value='10'" . ($slot_minutes == 10 ? " selected='selected'" : "") . ">10</option>";
		echo "<option value='20'" . ($slot_minutes == 20 ? " selected='selected'" : "") . ">20</option>";
		echo "<option value='30'" . ($slot_minutes == 30 ? " selected='selected'" : "") . ">30</option>";
		echo "<option value='45'" . ($slot_minutes == 45 ? " selected='selected'" : "") . ">45</option>";
		echo "<option value='60'" . ($slot_minutes == 60 ? " selected='selected'" : "") . ">60</option>";
		echo "<option value='90'" . ($slot_minutes == 90 ? " selected='selected'" : "") . ">90</option>";
		echo "<option value='120'" . ($slot_minutes == 120 ? " selected='selected'" : "") . ">120</option>";
		echo "<option value='240'" . ($slot_minutes == 240 ? " selected='selected'" : "") . ">240</option>";
		echo "<option value='360'" . ($slot_minutes == 360 ? " selected='selected'" : "") . ">360</option>";

		echo '		</select>';
		echo '	</td>';	
		echo '</tr>';
		
		echo '<tr id="not_by_minute_row" ' . (empty($entry_type) || $entry_type == 'byminute' ? "style='display:none'" : "") . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('Time slot', 'transfers') . '</th>';
		echo '	<td>';
		echo '		<select name="slot_minutes" id="slot_minutes">';
		
		$increment = $transfers_plugin_globals->get_time_slot_increment();
		$day_minutes = 24*60;
		
		for ($mi = 0; $mi < $day_minutes; $mi += $increment) {
			$h = $mi / 60;
			$m = $mi % 60;
			$hours = sprintf("%02d", $h);
			$minutes = sprintf("%02d", $m);
			echo "<option value='$mi'" . ($slot_minutes == $mi ? " selected='selected'" : "") . ">$hours:$minutes</option>";
		}
		echo '		</select>';
		echo '	</td>';	
		echo '</tr>';
		
		echo '<tr id="day_of_week_row" ' . ($entry_type == 'weekly' ? '' : 'style="display:none"') . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('Day of week', 'transfers') . '</th>';
		echo '	<td>';
		echo '		<select name="day_index_week" id="day_index_week">';
		$days_of_week = Transfers_Plugin_Utils::get_days_of_week();
		foreach ($days_of_week as $key => $value) {
			echo "			<option value='$key'" . ($key == $day_index ? " selected" : "") . ">" . $value . "</option>";
		}
		echo '		</select>';
		echo '</td>';
		echo '</tr>';
			
		echo '<tr id="day_of_month_row" ' . ($entry_type == 'monthly' ? '' : 'style="display:none"') . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('Day of month', 'transfers') . '</th>';
		echo '	<td>';
		echo '		<select name="day_index_month" id="day_index_month">';
		for ($i = 1;$i<=31;$i++) {
			echo "			<option value='$i'" . ($i == $day_index ? " selected" : "") . ">" . $i . "</option>";
		}
		echo '		</select>';
		echo '	</td>';
		echo '</tr>';	
		
		$select_destination_options = '<option value="">' . esc_html__('Select pickup location', 'transfers') . '</option>';
		$select_destination_options .= '<option ' . ($destination_from_id == 0 ? "selected" : "") . ' value="0">' . esc_html__('Any', 'transfers') . '</option>';
		$select_destination_options .= Transfers_Plugin_Utils::build_destination_select_recursively(null, $destination_from_id);
		$select_pickup_location = '<select id="destination_from_id" name="destination_from_id">' . $select_destination_options . '</select>';

		$select_destination_options = '<option value="">' . esc_html__('Select drop-off location', 'transfers') . '</option>';
		$select_destination_options .= '<option ' . ($destination_to_id == 0 ? "selected" : "") . ' value="0">' . esc_html__('Any', 'transfers') . '</option>';
		$select_destination_options .= Transfers_Plugin_Utils::build_destination_select_recursively(null, $destination_to_id, 0, false);
		$select_dropoff_location = '<select id="destination_to_id" name="destination_to_id">' . $select_destination_options . '</select>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Select destination from', 'transfers') . '</th>';
		echo '	<td>' . $select_pickup_location . '</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Select destination to', 'transfers') . '</th>';
		echo '	<td>' . $select_dropoff_location . '</td>';
		echo '</tr>';
		

		global $transfers_transport_types_post_type;
		$transport_type_results = $transfers_transport_types_post_type->list_transport_types(0, -1, 'post_title', 'ASC');
				
		$select_transport_type_options = '';
		$select_transport_type_options .= '<option value="">' . esc_html__('Select transport type', 'transfers') . '</option>';
		
		if ( count($transport_type_results) > 0 && $transport_type_results['total'] > 0 ) {
			
			foreach ($transport_type_results['results'] as $transport_type_result) {

				$transport_type = $transport_type_result;
				$transfers_transport_type = new transfers_transport_type($transport_type->ID);
				$select_transport_type_options .= '<option value="' . $transport_type->ID . '" ' . ($transport_type_id == $transport_type->ID ? "selected" : "") . '>' . $transfers_transport_type->get_title() . '</option>';
			}
		}
		
		$select_transport_type = '<select id="transport_type_id" name="transport_type_id">' . $select_transport_type_options . '</select>';
				
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Select transport type', 'transfers') . '</th>';
		echo '	<td>' . $select_transport_type . '</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Available vehicles (count)', 'transfers') . '</th>';
		echo '	<td><input type="number" step="1" name="available_vehicles" id="available_vehicles" value="' . $available_vehicles . '" /></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Price (private)', 'transfers') . '</th>';
		echo '	<td><input type="number" step="any" name="price_private" id="price_private" value="' . $price_private . '" /></td>';
		echo '</tr>';
		
		echo '<tr ' . ($transfers_plugin_globals->enable_shared_transfers() ? '' : ' style="display:none"') . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('Price (share)', 'transfers') . '</th>';
		echo '	<td><input type="number" step="any" name="price_share" id="price_share" value="' . $price_share . '" /></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Duration (minutes)', 'transfers') . '</th>';
		echo '	<td><input type="number" step="1" name="duration_minutes" id="duration_minutes" value="' . $duration_minutes . '" /></td>';
		echo '</tr>';
		
		echo '</table>';
		echo '<p>';
		echo '<a href="admin.php?page=transfers_availability_management_admin.php" class="button-secondary">' . esc_html__('Cancel', 'transfers') . '</a>&nbsp;';

		if ($availability_object) {
			echo '<input id="entry_id" name="entry_id" value="' . $edit . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Availability', 'transfers') . '"/>';
		} else {
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Availability', 'transfers') . '"/>';
		}
		echo '</p>';
		echo '</form>';			
	}
	
}