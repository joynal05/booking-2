<?php

require_once TRANSFERS_PLUGIN_DIR . '/includes/post_types/services.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/post_types/faqs.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/post_types/destinations.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/post_types/transport_types.php';
require_once TRANSFERS_PLUGIN_DIR . '/includes/post_types/extra_items.php';

class Transfers_Plugin_Post_Types extends Transfers_BaseSingleton {

	protected function __construct() {

        // our parent class might contain shared code in its constructor
        parent::__construct();

    }

    public function init() {
		add_action( 'init', array($this, 'initialize_post_types' ) );
		add_filter( 'rearrange_menu_order', array( $this, 'rearrange_menu_order' ) ); // Activate custom_menu_order
		add_filter( 'menu_order', array( $this, 'rearrange_menu_order' ) );
    }

	// Rearrange the admin menu
	function rearrange_menu_order($menu_ord) {
		if (!$menu_ord) return true;
		return array(
			'index.php', // Dashboard
			'edit.php?post_type=destination', // Custom type four
			'edit.php?post_type=extra_item', // Custom type one
			'edit.php?post_type=transport_type', // Custom type three
			'edit.php?post_type=faq', // Custom type five
			'edit.php?post_type=service', // Custom type two
		);
	}

	function initialize_post_types() {

		do_action('transfers_plugin_initialize_post_types');

		$this->create_extra_tables();
	}

	public function extra_tables_exist() {

		global $wpdb;

		$exist = true;

		if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s'", TRANSFERS_AVAILABILITY_TABLE)) != TRANSFERS_AVAILABILITY_TABLE) {
			$exist = false;
		}

		if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s'", TRANSFERS_BOOKING_TABLE)) != TRANSFERS_BOOKING_TABLE) {
			$exist = false;
		}

		if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s'", TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE)) != TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE) {
			$exist = false;
		}

		return $exist;
	}

	function create_extra_tables($override = false) {

		global $transfers_installed_plugin_version;

		if ($transfers_installed_plugin_version != TRANSFERS_PLUGIN_VERSION || $override) {

			global $wpdb;

			$sql = "CREATE TABLE " . TRANSFERS_AVAILABILITY_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						entry_type varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						day_index int(11) NULL,
						season_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						start_datetime datetime NOT NULL,
						end_datetime datetime NOT NULL,
						slot_minutes int(11) NOT NULL,
						destination_from_id bigint(20) unsigned NOT NULL,
						destination_to_id bigint(20) unsigned NOT NULL,
						transport_type_id bigint(20) unsigned NOT NULL,
						available_vehicles int(11) NOT NULL DEFAULT '1',
						price_private decimal(16,2) NOT NULL DEFAULT '0.00',
						price_share decimal(16,2) NOT NULL DEFAULT '0.00',
						duration_minutes int(11) NOT NULL DEFAULT '30',
						PRIMARY KEY  (Id)
					);";

			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			global $EZSQL_ERROR;

			$EZSQL_ERROR = array();

			$sql = "CREATE TABLE " . TRANSFERS_BOOKING_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						availability_id INT NOT NULL,
						from_id INT NOT NULL,
						to_id INT NOT NULL,
						booking_datetime datetime NOT NULL,
						people_count int(11) NOT NULL DEFAULT '1',
						is_private tinyint(3) NOT NULL DEFAULT '0',
						total_price decimal(16,2) NOT NULL DEFAULT '0.00',
						user_id bigint(20) unsigned DEFAULT NULL,
						first_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						last_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						email varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						phone varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						address varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						address_2 varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						company varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						town varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						zip varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						state varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						country varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						woo_order_id bigint(20) NULL,
						cart_key VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						woo_status VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (Id)
					);";

			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			dbDelta($sql);

			$EZSQL_ERROR = array();

			$sql = "CREATE TABLE " . TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						booking_id INT NOT NULL,
						extra_item_id INT NOT NULL,
						quantity int(11) NOT NULL DEFAULT '1',
						item_price decimal(16,2) NOT NULL DEFAULT '0.00',
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (Id)
					);";

			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			dbDelta($sql);

		}
	}

	function get_availability_entry_price($entry_id, $is_private) {

		global $wpdb;

		$sql = 	"SELECT " . ($is_private ? "price_private" : "price_share") . " FROM " . TRANSFERS_AVAILABILITY_TABLE .
				" WHERE Id = %d ";

		return $wpdb->get_var($wpdb->prepare($sql, $entry_id));
	}

	function get_availability_entry($entry_id, $duration_minutes = 0, $given_date = null) {

		global $wpdb, $transfers_multi_language_count, $transfers_plugin_globals;

		$allowed_completed_statuses = $transfers_plugin_globals->get_completed_order_woocommerce_statuses();

		$completed_statuses_str = '';
		if (is_array($allowed_completed_statuses) && count($allowed_completed_statuses) > 0) {
			foreach ($allowed_completed_statuses as $status => $state) {
				if ($state == '1') {
					if ($status == 'initiated')
						$status = '';
					$completed_statuses_str .= "'" . $status . "',";
				}
			}
		}
		$completed_statuses_str = rtrim($completed_statuses_str, ",");

		$sql = "

			SELECT 	a.*,
					(max_people_per_vehicle - booked_shared_seats) available_seats,
					FLOOR(((((available_vehicles - private_bookings)*max_people_per_vehicle) - booked_shared_seats) / max_people_per_vehicle)) available_full_vehicles
			FROM
			(
				SELECT 	DISTINCT availability.*, ";

		if (isset($given_date)) {

			$sql .= $wpdb->prepare(" IFNULL(
							(
								SELECT SUM(people_count)
								FROM " . TRANSFERS_BOOKING_TABLE . "
								WHERE is_private=0 AND availability_id = availability.Id AND DATE(booking_datetime)=DATE(%s) ", $given_date);

			$sql .=				($transfers_plugin_globals->use_woocommerce_for_checkout() ? (empty($completed_statuses_str) ? '' : " AND woo_status IN (" . $completed_statuses_str . ")") : '');

			$given_date_timestamp = strtotime($given_date);
			$given_date_with_duration = $given_date;
			if ($duration_minutes > 0) {
				$given_date_timestamp_with_duration = strtotime(sprintf("-%d minutes", $duration_minutes), strtotime($given_date));
				$given_date_with_duration = date('Y-m-d H:i', $given_date_timestamp_with_duration);
			}

			if (!isset($given_hours)) {
				$given_hours = date("H", $given_date_timestamp);
			}
			if (!isset($given_minutes)) {
				$given_minutes = date("i", $given_date_timestamp);
			}

			if (isset($given_hours) && isset($given_minutes)) {
				if ($duration_minutes > 0) {
					$sql .=	$wpdb->prepare( " AND (booking_datetime BETWEEN %s AND %s) ", $given_date_with_duration, $given_date );
				} else {
					$sql .=	$wpdb->prepare( " AND (HOUR(booking_datetime) = %d AND MINUTE(booking_datetime) = %d) ", $given_hours, $given_minutes );
				}
			}

			$sql .=			"), 0
						) booked_shared_seats, ";

			$sql .= $wpdb->prepare(" IFNULL(
							(
								SELECT COUNT(*)
								FROM " . TRANSFERS_BOOKING_TABLE . "
								WHERE is_private=1 AND availability_id = availability.Id AND DATE(booking_datetime)=DATE(%s) ", $given_date);

			$sql .=				($transfers_plugin_globals->use_woocommerce_for_checkout() ? (empty($completed_statuses_str) ? '' : " AND woo_status IN (" . $completed_statuses_str . ")") : '');

			if (isset($given_hours) && isset($given_minutes)) {
				if ($duration_minutes > 0) {
					$sql .=	$wpdb->prepare( " AND (booking_datetime BETWEEN %s AND %s) ", $given_date_with_duration, $given_date );
				} else {
					$sql .=	$wpdb->prepare( " AND (HOUR(booking_datetime) = %d AND MINUTE(booking_datetime) = %d) ", $given_hours, $given_minutes );
				}
			}

			$sql .=			"), 0
						)
						private_bookings, ";

			$sql .= " IFNULL((
							SELECT MIN(transport_type_max_people_per_vehicle.meta_value)+0 max_people_per_vehicle
							FROM $wpdb->postmeta transport_type_max_people_per_vehicle
							WHERE transport_type_max_people_per_vehicle.post_id = availability.transport_type_id AND transport_type_max_people_per_vehicle.meta_key='_transport_type_max_people_per_vehicle'
						), 0) max_people_per_vehicle,";
		} else {
			$sql .= "0 private_bookings, 0 booked_shared_seats, 0 max_people_per_vehicle, ";
		}

		$sql .= "
			CASE availability.destination_from_id WHEN 0 THEN '' ELSE destinations1.post_title END as destination_from,
			CASE availability.destination_to_id WHEN 0 THEN '' ELSE destinations2.post_title END as destination_to,
			transport_types.post_title as transport_type
		FROM " . TRANSFERS_AVAILABILITY_TABLE . " availability ";

		$sql .= " INNER JOIN $wpdb->posts transport_types ON transport_types.ID = availability.transport_type_id ";
		$sql .= " LEFT JOIN $wpdb->posts destinations1 ON (destinations1.ID = availability.destination_from_id AND availability.destination_from_id != 0 AND destinations1.post_status = 'publish')";
		$sql .= " LEFT JOIN $wpdb->posts destinations2 ON (destinations2.ID = availability.destination_to_id AND availability.destination_to_id != 0 AND destinations2.post_status = 'publish') ";

		$sql .= " WHERE  transport_types.post_status = 'publish'";

		$sql .= $wpdb->prepare(" AND availability.Id = %d ", $entry_id);

		if (isset($given_date)) {

			$date_time_stamp = strtotime($given_date);
			$date_day_of_week = Transfers_Plugin_Utils::get_day_of_week_index($date_time_stamp);
			$date_day_of_month = date("d", $date_time_stamp);

			$sql .= $wpdb->prepare(" AND (DATE(%s) >= DATE(start_datetime) AND DATE(%s) <= DATE(end_datetime) )
				AND
				(
					entry_type='daily' OR
					entry_type='byminute' OR
					(entry_type='weekly' AND day_index=%d) OR
					(entry_type='monthly' AND day_index=%d)
				)
				HAVING booked_shared_seats < ((available_vehicles - private_bookings)*max_people_per_vehicle)",
				$given_date, $given_date, $date_day_of_week, $date_day_of_month);
		}

		$sql .= " ) a ";

		$availability = $wpdb->get_row($sql);

		// echo $sql;

		if(isset($availability) && defined('ICL_LANGUAGE_CODE') && (transfers_get_default_language() != ICL_LANGUAGE_CODE || $transfers_multi_language_count > 1)) {
			// wpml/translation plugin translations for the 3 titles used.
			$transport_type_id = transfers_get_current_language_post_id($availability->transport_type_id, 'transport_type', true);
			$availability->transport_type = get_the_title($transport_type_id);
			if ($availability->destination_from_id > 0) {
				$destination_from_id = transfers_get_current_language_post_id($availability->destination_from_id, 'destination', true);
				$availability->destination_from = get_the_title($destination_from_id);
			}
			if ($availability->destination_to_id > 0) {
				$destination_to_id = transfers_get_current_language_post_id($availability->destination_to_id, 'destination', true);
				$availability->destination_to = get_the_title($destination_to_id);
			}
		}

		return $availability;
	}

	function list_connecting_destinations($orderby = 'wp_posts.post_title', $order = 'ASC', $destination_from_id) {

		$args = array(
			'post_type'         => 'destination',
			'post_status'       => array('publish'),
			'posts_per_page'    => -1,
			'paged' 			=> 1,
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
		);

		$destination_from_id = transfers_get_default_language_post_id($destination_from_id, 'destination');

		$args['tf_destination_from_id'] = $destination_from_id;

		add_filter('posts_where', array($this, 'connecting_destinations_where'), 10, 2);
		add_filter('posts_groupby', array($this, 'connecting_destinations_groupby'), 10, 2 );
		add_filter('posts_fields', array($this, 'connecting_destinations_fields'), 10, 2);

		$posts_query = new WP_Query($args);

		$results = array();

		if ($posts_query->have_posts() ) {
			while ( $posts_query->have_posts() ) {
				global $post;
				$posts_query->the_post();

				$results[] = $post;
			}
		}

		global $transfers_multi_language_count;
		if(isset($results) && count($results) > 0 && defined('ICL_LANGUAGE_CODE') && (transfers_get_default_language() != ICL_LANGUAGE_CODE || $transfers_multi_language_count > 1)) {
			foreach ($results as $result) {
				// wpml/translation plugin translations for the 3 titles used.
				$destination_id = transfers_get_current_language_post_id($result->destination_to_id, 'destination', true);
				$result->destination_to = get_the_title($destination_id);
			}
		}

		wp_reset_postdata();

		remove_filter('posts_where', array($this, 'connecting_destinations_where') );
		remove_filter('posts_groupby', array($this, 'connecting_destinations_groupby') );
		remove_filter('posts_fields', array($this, 'connecting_destinations_fields') );

		return $results;
	}

	function connecting_destinations_fields( $fields, $wp_query ) {

		global $wpdb, $transfers_multi_language_count;

		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'destination' ) {

			if (isset($wp_query->query_vars['tf_destination_from_id'])) {
				$destination_from_id = $wp_query->query_vars['tf_destination_from_id'];
				$fields = $wpdb->prepare(" DISTINCT $wpdb->posts.post_title destination_to,
						(
							SELECT MIN(price_share) FROM "
							. TRANSFERS_AVAILABILITY_TABLE . " a_m_p1
							WHERE price_share IS NOT NULL AND (a_m_p1.destination_from_id = %d OR a_m_p1.destination_from_id = 0) AND (a_m_p1.destination_to_id = $wpdb->posts.ID OR a_m_p1.destination_to_id = 0)
							AND DATE(a_m_p1.end_datetime) > now() AND price_share > 0
						)	price_share_min,
						(
							SELECT MIN(price_private) FROM "
							. TRANSFERS_AVAILABILITY_TABLE . " a_m_p2
							WHERE price_private IS NOT NULL AND (a_m_p2.destination_from_id = %d OR a_m_p2.destination_from_id = 0) AND (a_m_p2.destination_to_id = $wpdb->posts.ID OR a_m_p2.destination_to_id = 0)
							AND DATE(a_m_p2.end_datetime) > now() AND price_private > 0
						)	price_private_min,
						$wpdb->posts.ID as destination_to_id ", $destination_from_id, $destination_from_id);
			}
		}

		return $fields;
	}

	function connecting_destinations_where( $where, $wp_query ) {

		global $wpdb;

		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'destination' ) {

			$destination_from_id = 0;
			if (isset($wp_query->query_vars['tf_destination_from_id'])) {
				$destination_from_id = $wp_query->query_vars['tf_destination_from_id'];

				$where .= " AND $wpdb->posts.ID <> " . $destination_from_id;
			}

			global $sitepress;
			if ($sitepress) {
				$def_lan_code = $sitepress->get_default_language();
				$where = str_replace("t.language_code = '" . ICL_LANGUAGE_CODE . "'", "t.language_code = '" . $def_lan_code . "'",$where);
			}
		}

		return $where;
	}

	function connecting_destinations_groupby( $groupby, $wp_query ) {

		global $wpdb;

		if (empty($groupby)) {
			$groupby = " {$wpdb->posts}.ID ";
		}

		if (!is_admin()) {
			if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'destination' ) {

				$groupby .= ' HAVING 1=1 ';
				$groupby .= ' AND (price_share_min > 0 OR price_private_min > 0) ';
			}
		}

		return $groupby;
	}

	function list_availability_entries($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $search_term = null, $destination_from_id = null) {

		global $wpdb, $transfers_multi_language_count;

		$destination_from_id = transfers_get_default_language_post_id($destination_from_id, 'destination');

		$sql = "SELECT 	DISTINCT availability.*,
					CASE availability.destination_from_id WHEN 0 THEN 'ANY' ELSE destinations1.post_title END as destination_from,
					CASE availability.destination_to_id WHEN 0 THEN 'ANY' ELSE destinations2.post_title END as destination_to,
					transport_types.post_title as transport_type
				FROM " . TRANSFERS_AVAILABILITY_TABLE . " availability ";

		$sql .= " INNER JOIN $wpdb->posts transport_types ON transport_types.ID = availability.transport_type_id ";

		$sql .= " LEFT JOIN $wpdb->posts destinations1 ON (destinations1.ID = availability.destination_from_id AND availability.destination_from_id != 0 AND destinations1.post_status = 'publish') ";
		$sql .= " LEFT JOIN $wpdb->posts destinations2 ON (destinations2.ID = availability.destination_to_id AND availability.destination_to_id != 0 AND destinations2.post_status = 'publish') ";

		$sql .= " WHERE  transport_types.post_status = 'publish'";

		if ($destination_from_id != null && $destination_from_id > 0) {
			$sql .= $wpdb->prepare(" AND (availability.destination_from_id = 0 OR availability.destination_from_id = %d) ", $destination_from_id);
		}

		if ($search_term != null && !empty($search_term)) {
			$search_term = "%" . $search_term . "%";
			$sql .= $wpdb->prepare(" AND
				(
					(destinations1.post_title LIKE '%s' OR destinations1.post_content LIKE '%s') OR
					(destinations2.post_title LIKE '%s' OR destinations2.post_content LIKE '%s') OR
					(transport_types.post_title LIKE '%s' OR transport_types.post_content LIKE '%s')
				) ", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
		}

		if(!empty($orderby) & !empty($order)){
			$sql .= ' ORDER BY '. $orderby . ' ' . $order;
		}

		$sql_count = $sql;

		if(!empty($paged) && !empty($per_page)){
			$offset=($paged-1)*$per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
		}

		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $wpdb->get_results($sql)
		);

		// echo $sql;

		return $results;
	}

	function delete_availability_entry($entry_id) {

		global $wpdb;

		$sql = "DELETE FROM " . TRANSFERS_AVAILABILITY_TABLE . "
				WHERE Id = %d";

		$wpdb->query($wpdb->prepare($sql, $entry_id));

		do_action('transfers_deleted_availability_entry', $entry_id);
	}

	function create_availability_entry($entry_type, $season_name, $destination_from_id, $destination_to_id, $transport_type_id, $slot_minutes, $available_vehicles, $price_private, $price_share, $duration_minutes, $day_index, $start_datetime, $end_datetime) {

		global $wpdb;

		if ($destination_from_id > 0) // allow for -1
			$destination_from_id = transfers_get_default_language_post_id($destination_from_id, 'destination');
		if ($destination_to_id > 0) // allow for -1
			$destination_to_id = transfers_get_default_language_post_id($destination_to_id, 'destination');
		if ($transport_type_id > 0) // allow for -1
			$transport_type_id = transfers_get_default_language_post_id($transport_type_id, 'transport_type');

		$sql = "INSERT INTO " . TRANSFERS_AVAILABILITY_TABLE . "
				(entry_type, season_name, destination_from_id, destination_to_id, transport_type_id, slot_minutes, available_vehicles, price_private, price_share, duration_minutes, day_index, start_datetime, end_datetime)
				VALUES
				(%s, %s, %d, %d, %d, %s, %d, %f, %f, %d, %d, %s, %s);";

		$sql = $wpdb->prepare($sql, $entry_type, $season_name, $destination_from_id, $destination_to_id, $transport_type_id, $slot_minutes, $available_vehicles, $price_private, $price_share, $duration_minutes, $day_index, $start_datetime, $end_datetime);

		$wpdb->query($sql);

		$entry_id = $wpdb->insert_id;

		do_action('transfers_inserted_availability_entry', $entry_id);

		return $entry_id;
	}

	function update_availability_entry($entry_id, $entry_type, $season_name, $destination_from_id, $destination_to_id, $transport_type_id, $slot_minutes, $available_vehicles, $price_private, $price_share, $duration_minutes, $day_index, $start_datetime, $end_datetime) {

		global $wpdb;

		if ($destination_from_id > 0) // allow for -1
			$destination_from_id = transfers_get_default_language_post_id($destination_from_id, 'destination');
		if ($destination_to_id > 0) // allow for -1
			$destination_to_id = transfers_get_default_language_post_id($destination_to_id, 'destination');
		if ($transport_type_id > 0) // allow for -1
			$transport_type_id = transfers_get_default_language_post_id($transport_type_id, 'transport_type');

		$sql = "UPDATE " . TRANSFERS_AVAILABILITY_TABLE . "
				SET entry_type=%s,
					season_name=%s,
					destination_from_id=%d,
					destination_to_id=%d,
					transport_type_id=%d,
					slot_minutes=%s,
					available_vehicles=%d,
					price_private=%f,
					price_share=%f,
					duration_minutes=%d,
					day_index=%d,
					start_datetime=%s,
					end_datetime=%s
				WHERE Id=%d";

		$sql = $wpdb->prepare($sql, $entry_type, $season_name, $destination_from_id, $destination_to_id, $transport_type_id, $slot_minutes, $available_vehicles, $price_private, $price_share, $duration_minutes, $day_index, $start_datetime, $end_datetime, $entry_id);

		$wpdb->query($sql);

		do_action('transfers_updated_availability_entry', $entry_id);
	}

	function get_booking_entry($entry_id) {

		global $wpdb, $transfers_multi_language_count;

		$sql = "SELECT 	DISTINCT bookings.*,
						CASE availability.destination_from_id WHEN 0 THEN destinations4.post_title ELSE destinations1.post_title END as destination_from,
						CASE availability.destination_to_id WHEN 0 THEN destinations5.post_title ELSE destinations2.post_title END as destination_to,
						transport_types.post_title as transport_type,
						CASE availability.destination_from_id WHEN 0 THEN bookings.from_id ELSE availability.destination_from_id END AS destination_from_id,
						CASE availability.destination_to_id WHEN 0 THEN bookings.to_id ELSE availability.destination_to_id END AS destination_to_id,
						availability.transport_type_id
				FROM " . TRANSFERS_BOOKING_TABLE . " bookings ";

		$sql .= " INNER JOIN " . TRANSFERS_AVAILABILITY_TABLE . " availability ON availability.Id=bookings.availability_id ";
		$sql .= " INNER JOIN $wpdb->posts transport_types ON transport_types.ID = availability.transport_type_id ";
		$sql .= " LEFT JOIN $wpdb->posts destinations1 ON ( destinations1.ID = availability.destination_from_id AND availability.destination_from_id != 0 AND destinations1.post_status = 'publish' ) ";
		$sql .= " LEFT JOIN $wpdb->posts destinations2 ON ( destinations2.ID = availability.destination_to_id AND availability.destination_to_id != 0 AND destinations2.post_status = 'publish' ) ";
		$sql .= " LEFT JOIN $wpdb->posts destinations4 ON destinations4.ID = bookings.from_id ";
		$sql .= " LEFT JOIN $wpdb->posts destinations5 ON destinations5.ID = bookings.to_id ";
		$sql .= " WHERE transport_types.post_status = 'publish'";

		//$sql .= $wpdb->prepare(" AND bookings.Id = %d ", $entry_id);
        $sql .= $wpdb->prepare(" AND bookings.Id = %d ORDER BY bookings.Id DESC", $entry_id);

		// echo $sql;

		$booking_entry = $wpdb->get_row($sql);

		if(isset($booking_entry) && defined('ICL_LANGUAGE_CODE') && (transfers_get_default_language() != ICL_LANGUAGE_CODE || $transfers_multi_language_count > 1)) {
			// wpml/translation plugin translations for the 3 titles used.
			$transport_type_id = transfers_get_current_language_post_id($booking_entry->transport_type_id, 'transport_type', true);
			$booking_entry->transport_type = get_the_title($transport_type_id);
			if ($booking_entry->destination_from_id > 0) {
				$destination_from_id = transfers_get_current_language_post_id($booking_entry->destination_from_id, 'destination', true);
				$booking_entry->destination_from = get_the_title($destination_from_id);
			}
			if ($booking_entry->destination_to_id > 0) {
				$destination_to_id = transfers_get_current_language_post_id($booking_entry->destination_to_id, 'destination', true);
				$booking_entry->destination_to = get_the_title($destination_to_id);
			}
		}

		return $booking_entry;
	}

	function get_booking_entry_extra_items($booking_id) {

		global $wpdb, $transfers_multi_language_count;

		$sql = "SELECT 	DISTINCT booking_extra_items.*,
						extra_items1.post_title extra_item
				FROM " . TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE . " booking_extra_items ";

		$sql .= "INNER JOIN " . TRANSFERS_BOOKING_TABLE . " bookings ON booking_extra_items.booking_id=bookings.Id ";

		$sql .= " INNER JOIN $wpdb->posts extra_items1 ON ";
		$sql .= " extra_items1.ID = booking_extra_items.extra_item_id ";

		$sql .= " WHERE extra_items1.post_status = 'publish' ";

		$sql .= $wpdb->prepare(" AND booking_extra_items.booking_id = %d ", $booking_id);

		return $wpdb->get_results($sql);
	}

	function delete_booking_entry_extra_items($booking_id) {

		global $wpdb;

		$sql = " DELETE FROM " . TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE . " WHERE 1=1 ";

		$sql .= $wpdb->prepare(" AND booking_id = %d ", $booking_id);

		do_action('transfers_deleted_booking_entry_extra_items', $booking_id);

		return $wpdb->get_results($sql);
	}

	function list_booking_entries($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $search_term = null, $destination_from_id = null) {

		global $wpdb, $transfers_multi_language_count;

		$destination_from_id = transfers_get_default_language_post_id($destination_from_id, 'destination');

		$sql = "SELECT 	DISTINCT bookings.*,
						CASE availability.destination_from_id WHEN 0 THEN destinations4.post_title ELSE destinations1.post_title END as destination_from,
						CASE availability.destination_to_id WHEN 0 THEN destinations5.post_title ELSE destinations2.post_title END as destination_to,
						transport_types.post_title as transport_type,
						CASE availability.destination_from_id WHEN 0 THEN bookings.from_id ELSE availability.destination_from_id END AS destination_from_id,
						CASE availability.destination_to_id WHEN 0 THEN bookings.to_id ELSE availability.destination_to_id END AS destination_to_id,
						availability.transport_type_id
				FROM " . TRANSFERS_BOOKING_TABLE . " bookings ";

		$sql .= " INNER JOIN " . TRANSFERS_AVAILABILITY_TABLE . " availability ON availability.Id=bookings.availability_id ";
		$sql .= " INNER JOIN $wpdb->posts transport_types ON transport_types.ID = availability.transport_type_id ";

		$sql .= " LEFT JOIN $wpdb->posts destinations1 ON ( destinations1.ID = availability.destination_from_id AND availability.destination_from_id != 0 AND destinations1.post_status = 'publish') ";
		$sql .= " LEFT JOIN $wpdb->posts destinations2 ON ( destinations2.ID = availability.destination_to_id AND availability.destination_to_id != 0 AND destinations2.post_status = 'publish') ";
		$sql .= " LEFT JOIN $wpdb->posts destinations4 ON destinations4.ID = bookings.from_id ";
		$sql .= " LEFT JOIN $wpdb->posts destinations5 ON destinations5.ID = bookings.to_id ";

		//$sql .= " WHERE transport_types.post_status = 'publish'";
        $sql .= " WHERE transport_types.post_status = 'publish' AND bookings.woo_status = 'processing' ";

		if ($destination_from_id != null && $destination_from_id > 0) {
			$sql .= $wpdb->prepare(" AND 1=1
				AND destinations1.ID = %d ", $destination_from_id);
		}

		if ($search_term != null && !empty($search_term)) {
			$search_term = "%" . $search_term . "%";
			$sql .= $wpdb->prepare(" AND 1=1
				AND
				(
					(destinations1.post_title LIKE '%s' OR destinations1.post_content LIKE '%s') OR
					(destinations2.post_title LIKE '%s' OR destinations2.post_content LIKE '%s') OR
					(transport_types.post_title LIKE '%s' OR transport_types.post_content LIKE '%s')
				) ", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
		}

		if(!empty($orderby) & !empty($order)){
			$sql .= ' ORDER BY '. $orderby . ' ' . $order;
		}

		$sql_count = $sql;

		if(!empty($paged) && !empty($per_page)){
			$offset=($paged-1)*$per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
		}

		$entries = $wpdb->get_results($sql);

		foreach ($entries as $booking_entry) {
			if(isset($booking_entry) && defined('ICL_LANGUAGE_CODE') && (transfers_get_default_language() != ICL_LANGUAGE_CODE || $transfers_multi_language_count > 1)) {
				// wpml/translation plugin translations for the 3 titles used.
				$transport_type_id = transfers_get_current_language_post_id($booking_entry->transport_type_id, 'transport_type', true);
				$booking_entry->transport_type = get_the_title($transport_type_id);
				if ($booking_entry->destination_from_id > 0) {
					$destination_from_id = transfers_get_current_language_post_id($booking_entry->destination_from_id, 'destination', true);
					$booking_entry->destination_from = get_the_title($destination_from_id);
				}
				if ($booking_entry->destination_to_id > 0) {
					$destination_to_id = transfers_get_current_language_post_id($booking_entry->destination_to_id, 'destination', true);
					$booking_entry->destination_to = get_the_title($destination_to_id);
				}
			}
		}

		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $entries
		);

		return $results;
	}

	function create_booking_entry($booking_args) {

		global $wpdb;

		extract($booking_args);

		$sql = "INSERT INTO " . TRANSFERS_BOOKING_TABLE . "
				(user_id, from_id, to_id, first_name, last_name, email, phone, address, address_2, company, town, zip, state, country, other_fields, availability_id, people_count, is_private, total_price, booking_datetime)
				VALUES
				(%d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %f, %s);";

		$wpdb->query($wpdb->prepare($sql, $user_id, $from_id, $to_id, $first_name, $last_name, $email, $phone, $address, $address_2, $company, $town, $zip, $state, $country, serialize($booking_args['other_fields']), $availability_id, $people_count, $is_private, $total_price, $booking_datetime));

		$entry_id = $wpdb->insert_id;

		$this->insert_booking_entry_extra_items($entry_id, $extra_items);

		do_action('transfers_inserted_booking', $entry_id, $booking_args);

		return $entry_id;
	}

	function insert_booking_entry_extra_items($entry_id, $extra_items_array) {

		global $wpdb;

		foreach ($extra_items_array as $extra_item) {

			$extra_item_id = $extra_item->extra_item_id;
			$default_language_extra_item_id = transfers_get_default_language_post_id($extra_item_id, 'extra_item');

			$sql = "INSERT INTO " . TRANSFERS_BOOKING_EXTRA_ITEMS_TABLE . "
					(booking_id, extra_item_id, quantity, item_price)
					VALUES
					(%d, %d, %d, %f);";

			$wpdb->query($wpdb->prepare($sql, $entry_id, $default_language_extra_item_id, $extra_item->quantity, $extra_item->item_price));
		}

		do_action('transfers_inserted_booking_entry_extra_items', $entry_id, $extra_items_array);
	}

	function update_booking_entry_woocommerce_info($entry_id, $cart_key = null, $woo_order_id = null, $woo_status = null) {

		global $wpdb;

		if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
			$sql = "UPDATE " . TRANSFERS_BOOKING_TABLE . "
					SET ";

			if (isset($cart_key))
				$sql .= $wpdb->prepare("cart_key = %s, ", $cart_key);
			if (isset($woo_order_id))
				$sql .= $wpdb->prepare("woo_order_id = %d, ", $woo_order_id);
			if (isset($woo_status))
				$sql .= $wpdb->prepare("woo_status = %s, ", $woo_status);

			$sql = rtrim($sql, ", ");
			$sql .= $wpdb->prepare(" WHERE Id = %d", $entry_id);

			return $wpdb->query($sql);
		}
		return '';
	}

	function update_booking_entry($entry_id, $booking_args) {

		global $wpdb;

		extract($booking_args);

		if (isset($extra_items)) {
			$this->delete_booking_entry_extra_items($entry_id);
			$this->insert_booking_entry_extra_items($entry_id, $extra_items);
		}

		$sql = "UPDATE " . TRANSFERS_BOOKING_TABLE . " SET ";

		$field_sql = '';

		foreach ($booking_args as $field_key => $field_value) {

			switch ($field_key) {

				case 'people_count' 				: $field_sql .= $wpdb->prepare("people_count = %d, ", $field_value); break;
				case 'booking_datetime' 			: $field_sql .= $wpdb->prepare("booking_datetime = %s, ", $field_value); break;
				case 'total_price' 					: $field_sql .= $wpdb->prepare("total_price = %f, ", $field_value); break;
				case 'availability_id' 				: $field_sql .= $wpdb->prepare("availability_id = %d, ", $field_value); break;
				case 'from_id' 						: $field_sql .= $wpdb->prepare("from_id = %d, ", $field_value); break;
				case 'to_id' 						: $field_sql .= $wpdb->prepare("to_id = %d, ", $field_value); break;
				case 'user_id' 						: $field_sql .= $wpdb->prepare("user_id = %d, ", $field_value); break;
				case 'first_name' 					: $field_sql .= $wpdb->prepare("first_name = %s, ", $field_value); break;
				case 'last_name' 					: $field_sql .= $wpdb->prepare("last_name = %s, ", $field_value); break;
				case 'company' 						: $field_sql .= $wpdb->prepare("company = %s, ", $field_value); break;
				case 'email' 						: $field_sql .= $wpdb->prepare("email = %s, ", $field_value); break;
				case 'phone' 						: $field_sql .= $wpdb->prepare("phone = %s, ", $field_value); break;
				case 'address' 						: $field_sql .= $wpdb->prepare("address = %s, ", $field_value); break;
				case 'address_2' 					: $field_sql .= $wpdb->prepare("address_2 = %s, ", $field_value); break;
				case 'town' 						: $field_sql .= $wpdb->prepare("town = %s, ", $field_value); break;
				case 'zip' 							: $field_sql .= $wpdb->prepare("zip = %s, ", $field_value); break;
				case 'state' 						: $field_sql .= $wpdb->prepare("state = %s, ", $field_value); break;
				case 'country' 						: $field_sql .= $wpdb->prepare("country = %s, ", $field_value); break;
				case 'other_fields' 				: $field_sql .= $wpdb->prepare("other_fields = %s, ", serialize($field_value)); break;
				case 'woo_order_id' 				: $field_sql .= $wpdb->prepare("woo_order_id = %d, ", $field_value); break;
				case 'cart_key' 					: $field_sql .= $wpdb->prepare("cart_key = %s, ", $field_value); break;
				case 'woo_status' 					: $field_sql .= $wpdb->prepare("woo_status = %s, ", $field_value); break;
				default : break;
			}
		}

		if (!empty($field_sql)) {

			$field_sql = rtrim($field_sql, ", ");

			$sql .= $field_sql;

			$sql .= $wpdb->prepare(" WHERE Id = %d;", $entry_id);

			$result = $wpdb->query($sql);

			do_action('transfers_updated_booking', $entry_id, $booking_args, $result);
		}
	}

	function delete_booking_entry($entry_id, $fire_actions = true) {

		global $wpdb;

		if ($fire_actions) {
			do_action('transfers_before_delete_booking', $entry_id);
		}

		$this->delete_booking_entry_extra_items($entry_id);

		$sql = "DELETE FROM " . TRANSFERS_BOOKING_TABLE . "
				WHERE Id = %d ";

		$wpdb->query($wpdb->prepare($sql, $entry_id));

		if ($fire_actions) {
			do_action('transfers_after_delete_booking', $entry_id);
		}
	}

	function list_available_transfers($date, $destination_from_id, $destination_to_id, $people) {

		global $wpdb, $transfers_plugin_globals;

		$destination_from_id = transfers_get_default_language_post_id($destination_from_id, 'destination');
		$destination_to_id = transfers_get_default_language_post_id($destination_to_id, 'destination');

		$offset_hours = $transfers_plugin_globals->get_search_time_slot_offset();
		$offset_minutes = 0;
		if ($offset_hours > 0) {
			$offset_minutes = ($offset_hours * 60);
		}

		$date_time_stamp = strtotime($date);
		$date_day_of_week = Transfers_Plugin_Utils::get_day_of_week_index($date_time_stamp);
		$date_day_of_month = date("d", $date_time_stamp);

		$date_hours = date("H", $date_time_stamp);
		$date_minutes = date("i", $date_time_stamp);

		$date_slot_minutes = ($date_hours * 60) + $date_minutes - $offset_minutes;
		if ($date_slot_minutes < 0)
			$date_slot_minutes = 0;

		$allowed_completed_statuses = $transfers_plugin_globals->get_completed_order_woocommerce_statuses();

		$completed_statuses_str = '';
		if (is_array($allowed_completed_statuses) && count($allowed_completed_statuses) > 0) {
			foreach ($allowed_completed_statuses as $status => $state) {
				if ($state == '1') {
					if ($status == 'initiated')
						$status = '';
					$completed_statuses_str .= "'" . $status . "',";
				}
			}
		}
		$completed_statuses_str = rtrim($completed_statuses_str, ",");

		// (((available_vehicles - private_bookings)*max_people_per_vehicle) - booked_shared_seats) available_seats,
		$sql = $wpdb->prepare("
				SELECT 	a.*,
						(max_people_per_vehicle - booked_shared_seats) available_seats,
						FLOOR(((((available_vehicles - private_bookings)*max_people_per_vehicle) - booked_shared_seats) / max_people_per_vehicle)) available_full_vehicles
				FROM
				(
					SELECT 	availability.*,
							(
								SELECT IFNULL(SUM(people_count), 0) FROM " . TRANSFERS_BOOKING_TABLE . "
								WHERE is_private=0 AND availability_id = availability.Id AND DATE(booking_datetime)=DATE(%s) "
								. ($transfers_plugin_globals->use_woocommerce_for_checkout() ? (empty($completed_statuses_str) ? '' : " AND woo_status IN (" . $completed_statuses_str . ")") : '') .
							" ) booked_shared_seats, ", $date);

		$sql .= $wpdb->prepare(" (
								SELECT IFNULL(COUNT(*), 0) FROM " . TRANSFERS_BOOKING_TABLE . "
								WHERE is_private=1 AND availability_id = availability.Id AND DATE(booking_datetime)=DATE(%s) "
								. ($transfers_plugin_globals->use_woocommerce_for_checkout() ? (empty($completed_statuses_str) && $transfers_plugin_globals->use_woocommerce_for_checkout() ? '' : " AND woo_status IN (" . $completed_statuses_str . ")") : '') .
							" ) private_bookings,", $date);

		$sql .= " IFNULL((
								SELECT MIN(transport_type_max_people_per_vehicle.meta_value)+0 max_people_per_vehicle
								FROM $wpdb->postmeta transport_type_max_people_per_vehicle
								WHERE transport_type_max_people_per_vehicle.post_id = availability.transport_type_id AND transport_type_max_people_per_vehicle.meta_key='_transport_type_max_people_per_vehicle'
							), 0) max_people_per_vehicle
					FROM " . TRANSFERS_AVAILABILITY_TABLE . " availability
					WHERE 1=1  ";

		$sql .= $wpdb->prepare(" AND (availability.destination_from_id=%d OR availability.destination_from_id=0) ", $destination_from_id);
		$sql .= $wpdb->prepare(" AND (availability.destination_to_id=%d OR availability.destination_to_id=0) ", $destination_to_id);
		$sql .= $wpdb->prepare(" AND ( DATE(%s) >= DATE(availability.start_datetime) AND DATE(%s) <= DATE(availability.end_datetime)) ", $date, $date);

		$sql .= " AND ( ";
		$sql .= $wpdb->prepare(" 	(availability.entry_type='daily' AND availability.slot_minutes >= %d) OR ", $date_slot_minutes);
		$sql .= " 	(availability.entry_type='byminute') OR ";
		$sql .= $wpdb->prepare(" 	(availability.entry_type='weekly' AND availability.day_index=%d AND availability.slot_minutes >= %d) OR ", $date_day_of_week, $date_slot_minutes);
		$sql .= $wpdb->prepare("	(availability.entry_type='monthly' AND availability.day_index=%d AND availability.slot_minutes >= %d) ", $date_day_of_month, $date_slot_minutes);
		$sql .= $wpdb->prepare(")
					HAVING (availability.entry_type='byminute') OR booked_shared_seats < ((availability.available_vehicles - private_bookings)*max_people_per_vehicle)
				) a
				HAVING (entry_type='byminute') OR available_seats >= %d
				ORDER BY slot_minutes ASC
				", $people);



		return $wpdb->get_results($sql);
	}

	function retrieve_booking_object_from_request() {

		$booking_object = null;
		global $transfers_plugin_globals;

		if ( isset($_REQUEST) ) {

			if (isset($_REQUEST['peopleCount']) &&
				isset($_REQUEST['departureDate']) &&
				isset($_REQUEST['departureAvailabilityId']) &&
				isset($_REQUEST['departureFromId']) &&
				isset($_REQUEST['departureToId']) &&
				isset($_REQUEST['departureIsPrivate']) &&
				isset($_REQUEST['departureSlotMinutesNumber'])) {

				$booking_object = new stdClass();
				$booking_object->departure_total_price = 0;
				$booking_object->departure_extra_items_string = '';

				$people_count = intval(wp_kses($_REQUEST['peopleCount'], ''));
				$departure_date = date(TRANSFERS_PHP_DATE_FORMAT_NO_TIME, strtotime(wp_kses($_REQUEST['departureDate'], '')));
				$departure_slot_minutes_number = intval(wp_kses($_REQUEST['departureSlotMinutesNumber'], ''));
				$departure_slot_seconds_number = $departure_slot_minutes_number * 60;
				$departure_date_time = strtotime($departure_date) + $departure_slot_seconds_number;
				$departure_date = date(TRANSFERS_PHP_DATE_FORMAT, $departure_date_time);

				$dep_availability_id = intval(wp_kses($_REQUEST['departureAvailabilityId'], ''));
				$dep_from_id = intval(wp_kses($_REQUEST['departureFromId'], ''));
				$dep_to_id = intval(wp_kses($_REQUEST['departureToId'], ''));
				$departure_is_private = intval(wp_kses($_REQUEST['departureIsPrivate'], ''));

				$booking_object->departure_availability_price = $this->get_availability_entry_price($dep_availability_id, $departure_is_private);

				if ($departure_is_private)
					$booking_object->departure_total_price = $booking_object->departure_availability_price;
				else
					$booking_object->departure_total_price = $booking_object->departure_availability_price * $people_count;

				$user_id = get_current_user_id();

				$booking_object->departure_booking_args = array(
					'user_id' => $user_id,
					'from_id' => $dep_from_id,
					'to_id' => $dep_to_id,
					'booking_datetime' => $departure_date,
					'availability_id' => $dep_availability_id,
					'people_count' => $people_count,
					'is_private' => $departure_is_private,
				);

				$booking_form_fields = $transfers_plugin_globals->get_booking_form_fields();

				$booking_object->departure_booking_args['first_name'] = '';
				$booking_object->departure_booking_args['last_name'] = '';
				$booking_object->departure_booking_args['company'] = '';
				$booking_object->departure_booking_args['email'] = '';
				$booking_object->departure_booking_args['phone'] = '';
				$booking_object->departure_booking_args['address'] = '';
				$booking_object->departure_booking_args['address_2'] = '';
				$booking_object->departure_booking_args['town'] = '';
				$booking_object->departure_booking_args['zip'] = '';
				$booking_object->departure_booking_args['state'] = '';
				$booking_object->departure_booking_args['country'] = '';
				$booking_object->departure_booking_args['other_fields'] = array();

				foreach ($booking_form_fields as $form_field) {

					$field_id = $form_field['id'];

					if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) {

						$field_value = sanitize_text_field($_REQUEST[$field_id]);

						switch ($field_id) {

							case 'first_name' 			: { $booking_object->departure_booking_args['first_name'] = $field_value; break; }
							case 'last_name' 			: { $booking_object->departure_booking_args['last_name'] = $field_value; break; }
							case 'company' 				: { $booking_object->departure_booking_args['company'] = $field_value; break; }
							case 'email' 				: { $booking_object->departure_booking_args['email'] = $field_value; break; }
							case 'phone' 				: { $booking_object->departure_booking_args['phone'] = $field_value; break; }
							case 'address' 				: { $booking_object->departure_booking_args['address'] = $field_value; break; }
							case 'address_2' 			: { $booking_object->departure_booking_args['address_2'] = $field_value; break; }
							case 'town' 				: { $booking_object->departure_booking_args['town'] = $field_value; break; }
							case 'zip' 					: { $booking_object->departure_booking_args['zip'] = $field_value; break; }
							case 'state' 				: { $booking_object->departure_booking_args['state'] = $field_value; break; }
							case 'country' 				: { $booking_object->departure_booking_args['country'] = $field_value; break; }
							default : {
								$booking_object->departure_booking_args['other_fields'][$field_id] = $field_value;
								break;
							}
						}
					}
				}

				$departure_availability = null;
				$booking_object->departure_slot_minutes = '';

				if ($booking_object->departure_booking_args['availability_id'] > 0) {
					$departure_availability = $this->get_availability_entry($booking_object->departure_booking_args['availability_id']);
					$booking_object->departure_slot_minutes = Transfers_Plugin_Utils::display_hours_and_minutes($departure_availability->slot_minutes);
					$booking_object->departure_destination_from = $departure_availability->destination_from;
					$booking_object->departure_destination_to = $departure_availability->destination_to;
					$booking_object->departure_transport_type = $departure_availability->transport_type;
				}

				$departure_extra_items_array = array();

				$departure_extra_items_raw = isset($_REQUEST['departureExtraItems']) ? transfers_sanitize_array((array)$_REQUEST['departureExtraItems']) : array();

				if (count($departure_extra_items_raw) > 0) {
					foreach ($departure_extra_items_raw as $key => $item_array) {

						$extra_item_obj = new stdClass();

						$extra_item_obj->extra_item_id = $item_array['id'];
						$extra_item_obj->item_price = $item_array['price'];
						$extra_item_obj->quantity = $item_array['quantity'];
						$extra_item_obj->title = $item_array['title'];

						$booking_object->departure_total_price += ((int)$extra_item_obj->quantity * (float)$extra_item_obj->item_price);

						$departure_extra_items_array[] = $extra_item_obj;
						$booking_object->departure_extra_items_string .= $extra_item_obj->quantity . ' x ' . $extra_item_obj->title . ', ';
					}
				}
				$booking_object->departure_extra_items_string = rtrim($booking_object->departure_extra_items_string, ", ");

				$booking_object->departure_booking_args['extra_items'] = $departure_extra_items_array;
				$booking_object->departure_booking_args['total_price'] = $booking_object->departure_total_price;

				$ret_availability_id = 0;
				$return_is_private = false;
				$return_date = null;
				$booking_object->return_booking_args = null;
				$booking_object->return_total_price = 0;
				$booking_object->return_availability_price = 0;
				$booking_object->return_extra_items_string = '';

				if (isset($_REQUEST['returnAvailabilityId'])) {

					$ret_availability_id = intval(wp_kses($_REQUEST['returnAvailabilityId'], ''));
					$ret_from_id = intval(wp_kses($_REQUEST['returnFromId'], ''));
					$ret_to_id = intval(wp_kses($_REQUEST['returnToId'], ''));

					if ($ret_availability_id > 0) {

						$return_is_private = isset($_REQUEST['returnIsPrivate']) ? intval(wp_kses($_REQUEST['returnIsPrivate'], '')) : false;
						$return_date = date(TRANSFERS_PHP_DATE_FORMAT_NO_TIME, strtotime(wp_kses($_REQUEST['returnDate'], '')));

						$return_slot_minutes_number = intval(wp_kses($_REQUEST['returnSlotMinutesNumber'], ''));
						$return_slot_seconds_number = $return_slot_minutes_number * 60;
						$return_date_time = strtotime($return_date) + $return_slot_seconds_number;
						$return_date = date(TRANSFERS_PHP_DATE_FORMAT, $return_date_time);

						$booking_object->return_booking_args = array(
							'user_id' => $user_id,
							'from_id' => $ret_from_id,
							'to_id' => $ret_to_id,
							'booking_datetime' => $return_date,
							'availability_id' => $ret_availability_id,
							'people_count' => $people_count,
							'is_private' => $return_is_private,
						);

						$booking_object->return_booking_args['first_name'] = '';
						$booking_object->return_booking_args['last_name'] = '';
						$booking_object->return_booking_args['company'] = '';
						$booking_object->return_booking_args['email'] = '';
						$booking_object->return_booking_args['phone'] = '';
						$booking_object->return_booking_args['address'] = '';
						$booking_object->return_booking_args['address_2'] = '';
						$booking_object->return_booking_args['town'] = '';
						$booking_object->return_booking_args['zip'] = '';
						$booking_object->return_booking_args['state'] = '';
						$booking_object->return_booking_args['country'] = '';
						$booking_object->return_booking_args['other_fields'] = array();

						foreach ($booking_form_fields as $form_field) {

							$field_id = $form_field['id'];

							if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) {

								$field_value = sanitize_text_field($_REQUEST[$field_id]);

								switch ($field_id) {

									case 'first_name' 			: { $booking_object->return_booking_args['first_name'] = $field_value; break; }
									case 'last_name' 			: { $booking_object->return_booking_args['last_name'] = $field_value; break; }
									case 'company' 				: { $booking_object->return_booking_args['company'] = $field_value; break; }
									case 'email' 				: { $booking_object->return_booking_args['email'] = $field_value; break; }
									case 'phone' 				: { $booking_object->return_booking_args['phone'] = $field_value; break; }
									case 'address' 				: { $booking_object->return_booking_args['address'] = $field_value; break; }
									case 'address_2' 			: { $booking_object->return_booking_args['address_2'] = $field_value; break; }
									case 'town' 				: { $booking_object->return_booking_args['town'] = $field_value; break; }
									case 'zip' 					: { $booking_object->return_booking_args['zip'] = $field_value; break; }
									case 'state' 				: { $booking_object->return_booking_args['state'] = $field_value; break; }
									case 'country' 				: { $booking_object->return_booking_args['country'] = $field_value; break; }
									default : {
										$booking_object->return_booking_args['other_fields'][$field_id] = $field_value;
										break;
									}
								}
							}
						}

						$return_availability = null;
						$booking_object->return_slot_minutes = '';
						if ($booking_object->return_booking_args['availability_id'] > 0) {
							$return_availability = $this->get_availability_entry($booking_object->return_booking_args['availability_id']);
							$booking_object->return_slot_minutes = Transfers_Plugin_Utils::display_hours_and_minutes($return_availability->slot_minutes);
							$booking_object->return_destination_from = $return_availability->destination_from;
							$booking_object->return_destination_to = $return_availability->destination_to;
							$booking_object->return_transport_type = $return_availability->transport_type;
						}

						$booking_object->return_availability_price = $this->get_availability_entry_price($ret_availability_id, $return_is_private);

						if ($return_is_private)
							$booking_object->return_total_price = $booking_object->return_availability_price;
						else
							$booking_object->return_total_price = $booking_object->return_availability_price * $people_count;

						$return_extra_items_array = array();

						$return_extra_items_raw = isset($_REQUEST['returnExtraItems']) ? transfers_sanitize_array((array)$_REQUEST['returnExtraItems']) : array();

						if (count($return_extra_items_raw) > 0) {
							foreach ($return_extra_items_raw as $key => $item_array) {

								$extra_item_obj = new stdClass();

								$extra_item_obj->extra_item_id = $item_array['id'];
								$extra_item_obj->item_price = $item_array['price'];
								$extra_item_obj->quantity = $item_array['quantity'];
								$extra_item_obj->title = $item_array['title'];

								$booking_object->return_total_price += ((int)$extra_item_obj->quantity * (float)$extra_item_obj->item_price);

								$return_extra_items_array[] = $extra_item_obj;
								$booking_object->return_extra_items_string .= $extra_item_obj->quantity . ' x ' . $extra_item_obj->title . ', ';
							}
						}

						$booking_object->return_extra_items_string = rtrim($booking_object->return_extra_items_string, ", ");

						$booking_object->return_booking_args['extra_items'] = $return_extra_items_array;
						$booking_object->return_booking_args['total_price'] = $booking_object->return_total_price;
					}
				}
			}
		}

		return $booking_object;
	}
}

global $transfers_plugin_post_types;
// store the instance in a variable to be retrieved later and call init
$transfers_plugin_post_types = Transfers_Plugin_Post_Types::get_instance();
$transfers_plugin_post_types->init();