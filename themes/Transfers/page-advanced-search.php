<?php
/**
/* Template Name: Advanced Search Results
 * The Advanced search results template file.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */

if (!function_exists('is_transfers_plugin_active') || !is_transfers_plugin_active()) {
	wp_redirect(home_url('/'));
	exit;
}

get_header();
get_sidebar('under-header');

global $post, $search_results_sort_by, $transfers_theme_globals, $transfers_plugin_globals, $date_format;
$date_format = get_option('date_format');
$date_format_with_time = $date_format . ' H:i';

$search_results_by_minute_count = $transfers_theme_globals->get_search_results_by_minute_count();
$search_results_sort_by = $transfers_theme_globals->get_search_results_sort_by();

$destination1_from_id = 0;
if (isset($_GET['p1']) && !empty($_GET['p1']))
	$destination1_from_id = intval(wp_kses($_GET['p1'], ''));
$destination1_to_id = 0;
if (isset($_GET['d1']) && !empty($_GET['d1']))
	$destination1_to_id = intval(wp_kses($_GET['d1'], ''));

$destination2_from_id = 0;
if (isset($_GET['p2']) && !empty($_GET['p2']))
	$destination2_from_id = intval(wp_kses($_GET['p2'], ''));
$destination2_to_id = 0;
if (isset($_GET['d2']) && !empty($_GET['d2']))
	$destination2_to_id = intval(wp_kses($_GET['d2'], ''));

$people = 0;
if (isset($_GET['ppl']) && !empty($_GET['ppl']))
	$people = intval(wp_kses($_GET['ppl'], ''));

$departure_date = null;
if (isset($_GET['dep']) && !empty($_GET['dep']))
	$departure_date = isset($_GET['dep']) && !empty($_GET['dep']) ? date(TRANSFERS_PHP_DATE_FORMAT, strtotime(wp_kses($_GET['dep'], ''))) : null;
else
	$departure_date = date(TRANSFERS_PHP_DATE_FORMAT);

$return_date = null;
if (isset($_GET['ret']) && !empty($_GET['ret'])) {
	$return_date = isset($_GET['ret']) && !empty($_GET['ret']) ? date(TRANSFERS_PHP_DATE_FORMAT, strtotime(wp_kses($_GET['ret'], ''))) : null;

	$return_date_seconds = strtotime($return_date);
	// round to nearest 10 minutes (600 seconds);
	$return_date_seconds = Transfers_Theme_Utils::round_to_nearest_anything($return_date_seconds, 600);
	// $return_date = date(TRANSFERS_PHP_DATE_FORMAT, $return_date_seconds);
}

$booking_form_page_url = $transfers_plugin_globals->get_booking_form_page_url();
?>
<script>
	window.onSearchPage = true;
	window.bookingRequest = {};
	window.bookingRequest.departureDate = '<?php echo esc_js(isset($departure_date) ? date($date_format_with_time, strtotime($departure_date)) : 'null'); ?>';
	window.bookingRequest.departureDateAlt = '<?php echo esc_js(isset($departure_date) ? date(TRANSFERS_PHP_DATE_FORMAT, strtotime($departure_date)): 'null'); ?>';
	window.bookingRequest.returnDate = '<?php echo esc_js(isset($return_date) ? date($date_format_with_time, strtotime($return_date)) : 'null'); ?>';
	window.bookingRequest.returnDateAlt = '<?php echo esc_js(isset($return_date) ? date(TRANSFERS_PHP_DATE_FORMAT, strtotime($return_date)) : 'null'); ?>';
	window.bookingRequest.people = <?php echo esc_js($people > 1 ? $people : 1); ?>;
	window.bookingRequest.departureFromId = <?php echo esc_js(isset($destination1_from_id) ? $destination1_from_id : 0); ?>;
	window.bookingRequest.departureToId = <?php echo esc_js(isset($destination1_to_id) ? $destination1_to_id : 0); ?>;
	window.bookingRequest.returnFromId = <?php echo esc_js(isset($destination2_from_id) ? $destination2_from_id : 0); ?>;
	window.bookingRequest.returnToId = <?php echo esc_js(isset($destination2_to_id) ? $destination2_to_id : 0); ?>;
	window.bookingRequest.departureAvailabilityId = 0;
	window.bookingRequest.returnAvailabilityId = 0;
	window.bookingRequest.departureIsPrivate = false;
	window.bookingRequest.returnIsPrivate = false;
	window.bookingFormPageUrl = '<?php echo esc_js($booking_form_page_url); ?>';
</script>
<?php
$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right')
	$section_class = 'three-fourth';
?>
	<?php  if ( have_posts() ) : the_post(); ?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php the_title(); ?></h1>
				<?php $transfers_theme_globals->get_breadcrumbs(); ?>
			</div>
		</div>
	</header>
	<ul>
		<?php
		$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h3>', 'after_title'   => '</h3>' );
		the_widget('transfers_Advanced_Search_Widget', null, $widget_args);
		?>
	</ul>
	<div class="wrap">
		<div class="row">
			<?php $content = get_the_content();
			if (!empty($content)) { ?>
			<!--- Content -->
			<div class="content textongrey full-width">
				<?php if ( has_post_thumbnail() ) { ?>
				<figure class="entry-featured">
					<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => '')); ?>
					<div class="overlay">
						<a href="<?php esc_url(get_the_permalink()) ?>" class="expand">+</a>
					</div>
				</figure>
				<?php } ?>
				<div class="entry-content">
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
					<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				</div>
			</div>
			<?php } ?>
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<div class="content <?php echo esc_attr($section_class); ?> search-results-top">
			<?php
				$search_results = null;
				if ($destination1_from_id > 0 && $destination1_to_id > 0 && $destination1_from_id != $destination1_to_id) {
					$search_results = $transfers_plugin_post_types->list_available_transfers($departure_date, $destination1_from_id, $destination1_to_id, $people);
				}

				if ($search_results && count($search_results) > 0) { ?>
					<h2 class="select-departure-header"><?php esc_html_e('Select transfer for your DEPARTURE', 'transfers'); ?></h2>
					<div class="results">
						<?php

						global $availability_result, $destination_class, $transfer_is_return, $slot_minutes, $slot_minutes_number, $transport_type_price, $transport_type_is_private;

						$availabilities = array();
						$found_results = 0;

						foreach ($search_results as $result) {

							$transfer_is_return = false;
							$availability_result = $result;
							$destination_class = 'one-fourth';

							if ($availability_result->entry_type == 'byminute') {

								$slot_minutes_interval = $availability_result->slot_minutes;

								$departure_date_seconds = strtotime($departure_date);

								$departure_date_start_day_date = date(TRANSFERS_PHP_DATE_FORMAT_ZERO_TIME, strtotime($departure_date));

								$minutes_diff = (strtotime($departure_date) - strtotime($departure_date_start_day_date)) / 60;
								$rounded_minutes_diff = Transfers_Theme_Utils::round_to_nearest_anything($minutes_diff, $slot_minutes_interval);
								$departure_date = date(TRANSFERS_PHP_DATE_FORMAT, strtotime(sprintf('+%d minutes', $rounded_minutes_diff), strtotime($departure_date_start_day_date)));
								$departure_date_seconds = strtotime($departure_date);

								// round to nearest 10 minutes (600 seconds);
								//$departure_date_seconds = Transfers_Theme_Utils::round_to_nearest_anything($departure_date_seconds / 60, $slot_minutes_interval) * 60;
								//$departure_date = date(TRANSFERS_PHP_DATE_FORMAT, $departure_date_seconds);

								$date_minutes = date("i", $departure_date_seconds);
								$date_hours = date("H", $departure_date_seconds);
								$real_date_minutes = $date_minutes + ($date_hours * 60);

								$found_results = 0;

								for ($i=0; $i < $search_results_by_minute_count; $i++) {

									$slot_minutes_number = ($slot_minutes_interval * $i) + $real_date_minutes;

									$slot_time_stamp = ($slot_minutes_interval * $i * 60) + $departure_date_seconds;
									$slot_date = date('Y-m-d H:i', $slot_time_stamp);

									$slot_minute_part = date("i", $slot_time_stamp);
									$slot_hour_part = date("H", $slot_time_stamp);

									$availability = $transfers_plugin_post_types->get_availability_entry($availability_result->Id, $availability_result->duration_minutes, $slot_date);

									if ($availability) {
										$availability->slot_minutes = Transfers_Plugin_Utils::display_hours_and_minutes($slot_minutes_number);
										$availability->slot_minutes_number = $slot_minutes_number;
										$availabilities[] = $availability;
									}
								}

							} else {
								$result->slot_minutes_number = $result->slot_minutes;
								$availabilities[] = $result;
							}
						}

						usort($availabilities, function($a, $b) {

							global $search_results_sort_by;
							if ($search_results_sort_by == 'byprice') {
								if (isset($a->price_private) && isset($b->price_private)) {
									if ($b->price_private == $a->price_private)
										return 0;

									return $b->price_private < $a->price_private ? 1 : -1;
								} else if (isset($a->price_share) && isset($b->price_share)) {
									if ($b->price_share == $a->price_share)
										return 0;

									return $b->price_share < $a->price_share ? 1 : -1;
								} else {
									return -1;
								}
							} else {
								return $b->slot_minutes_number < $a->slot_minutes_number ? 1 : -1;
							}
						});

						foreach ($availabilities  as $availability_result) {

							if ($availability_result != null) {

								$slot_minutes_number = $availability_result->slot_minutes_number;

								if ($availability_result->entry_type == 'byminute') {
									$slot_minutes = $availability_result->slot_minutes;
								} else {
									$slot_minutes = Transfers_Plugin_Utils::display_hours_and_minutes($availability_result->slot_minutes);
								}

								if ($availability_result->price_private > 0 && $availability_result->available_full_vehicles > 0 && $availability_result->available_seats >= $people ) {

									$transport_type_price = $availability_result->price_private;
									$transport_type_is_private = true;
									get_template_part('includes/parts/transfer', 'item');
									$found_results++;
								}

								if ($transfers_plugin_globals->enable_shared_transfers() && $availability_result->price_share > 0 && $availability_result->available_seats >= $people) {

									$transport_type_price = $availability_result->price_share;
									$transport_type_is_private = false;
									get_template_part('includes/parts/transfer', 'item');
									$found_results++;
								}
							}
						}

						if ($found_results == 0) {
						?>
						<script>
							(function($){
								"use strict";
								$(document).ready(function () {
									$('.select-departure-header').hide();
								});
							})(jQuery);
						</script>
						<h2><?php esc_html_e('No results found for your departure', 'transfers'); ?></h2>
						<p><?php esc_html_e('Please try different search parameters in the search form above.', 'transfers'); ?></p>
						<?php
						}
						?>
					</div>
					<?php
					if (isset($return_date)) {
						$search_results = null;
						if ($destination2_from_id > 0 && $destination2_to_id > 0 && $destination2_from_id != $destination2_to_id) {
							$search_results = $transfers_plugin_post_types->list_available_transfers($return_date, $destination2_from_id, $destination2_to_id, $people);
						}

						if ($search_results && is_array($search_results) && count($search_results) > 0) { ?>
						<h2 id="returnHeading" class="select-return-header"><?php esc_html_e('Select transfer for your RETURN', 'transfers'); ?></h2>
						<div class="results">
						<?php

							global $availability_result, $destination_class, $transfer_is_return, $slot_minutes, $slot_minutes_number, $transport_type_price, $transport_type_is_private;

							$availabilities = array();
							$found_results = 0;

							foreach ($search_results as $result) {

								$transfer_is_return = true;
								$availability_result = $result;
								$destination_class = 'one-fourth';

								if ($availability_result->entry_type == 'byminute') {

									$slot_minutes_interval = $availability_result->slot_minutes;

									$return_date_seconds = strtotime($return_date);

									$return_date_start_day_date = date(TRANSFERS_PHP_DATE_FORMAT_ZERO_TIME, strtotime($return_date));

									$minutes_diff = (strtotime($return_date) - strtotime($return_date_start_day_date)) / 60;
									$rounded_minutes_diff = Transfers_Theme_Utils::round_to_nearest_anything($minutes_diff, $slot_minutes_interval);
									$return_date = date(TRANSFERS_PHP_DATE_FORMAT, strtotime(sprintf('+%d minutes', $rounded_minutes_diff), strtotime($return_date_start_day_date)));
									$return_date_seconds = strtotime($return_date);

									$return_date_seconds = Transfers_Theme_Utils::round_to_nearest_anything($return_date_seconds, $slot_minutes_interval * 60);

									$return_date = date(TRANSFERS_PHP_DATE_FORMAT, $return_date_seconds);

									$date_minutes = date("i", $return_date_seconds);
									$date_hours = date("H", $return_date_seconds);

									$real_date_minutes = $date_minutes + ($date_hours * 60);

									for ($i=0; $i < $search_results_by_minute_count; $i++) {

										$slot_minutes_interval = $availability_result->slot_minutes;

										$slot_minutes_number = ($slot_minutes_interval * $i) + $real_date_minutes;

										$slot_time_stamp = ($slot_minutes_interval * $i * 60) + $return_date_seconds;
										$slot_date = date('Y-m-d H:i', $slot_time_stamp);

										$slot_minute_part = date("i", $slot_time_stamp);
										$slot_hour_part = date("H", $slot_time_stamp);

										$availability = $transfers_plugin_post_types->get_availability_entry($availability_result->Id, $availability_result->duration_minutes, $slot_date);
										if ($availability) {
											$availability->slot_minutes = Transfers_Plugin_Utils::display_hours_and_minutes($slot_minutes_number);
											$availability->slot_minutes_number = $slot_minutes_number;
											$availabilities[] = $availability;
										}
									}

								} else {
									$result->slot_minutes_number = $result->slot_minutes;
									$availabilities[] = $result;
								}
							}

							usort($availabilities, function($a, $b) {

								global $search_results_sort_by;
								if ($search_results_sort_by == 'byprice') {
									if (isset($a->price_private) && isset($b->price_private)) {
										if ($b->price_private == $a->price_private)
											return 0;

										return $b->price_private < $a->price_private ? 1 : -1;
									} else if (isset($a->price_share) && isset($b->price_share)) {
										if ($b->price_share == $a->price_share)
											return 0;

										return $b->price_share < $a->price_share ? 1 : -1;
									} else {
										return -1;
									}
								} else {
									return $b->slot_minutes_number < $a->slot_minutes_number ? 1 : -1;
								}
							});

							foreach ($availabilities  as $availability_result) {

								if ($availability_result != null) {

									$slot_minutes_number = $availability_result->slot_minutes_number;

									if ($availability_result->entry_type == 'byminute') {
										$slot_minutes = $availability_result->slot_minutes;
									} else {
										$slot_minutes = Transfers_Plugin_Utils::display_hours_and_minutes($availability_result->slot_minutes);
									}

									if ($availability_result->price_private > 0 && $availability_result->available_full_vehicles > 0 && $availability_result->available_seats >= $people ) {

										$transport_type_price = $availability_result->price_private;
										$transport_type_is_private = true;
										get_template_part('includes/parts/transfer', 'item');
										$found_results++;
									}

									if ($transfers_plugin_globals->enable_shared_transfers() && $availability_result->price_share > 0 && $availability_result->available_seats >= $people) {

										$transport_type_price = $availability_result->price_share;
										$transport_type_is_private = false;
										get_template_part('includes/parts/transfer', 'item');
										$found_results++;
									}
								}
							}

							if ($found_results == 0) {
							?>
							<script>
								(function($){
									"use strict";
									$(document).ready(function () {
										$('.select-return-header').hide();
									});
								})(jQuery);
							</script>
							<h2><?php esc_html_e('No results found for your return trip', 'transfers'); ?></h2>
							<p><?php esc_html_e('Please try different search parameters in the search form above.', 'transfers'); ?></p>
							<?php
							}

						} else { ?>
						<h2><?php esc_html_e('No results found for your return trip', 'transfers'); ?></h2>
						<p><?php esc_html_e('Please try different search parameters in the search form above.', 'transfers'); ?></p>
						<?php }	?>
						</div>
						<?php
					} ?>
					<div class="proceed-to-booking actions"  style="display:none" >
						<a href="#" class="btn grey huge right" id="book-transfers"><?php esc_html_e('book transfers', 'transfers') ?></a>
					</div>
				<?php } else { ?>
					<h2><?php esc_html_e('No search results found', 'transfers'); ?></h2>
					<p><?php esc_html_e('Please try different search parameters in the search form above.', 'transfers'); ?></p>
			<?php } ?>
			</div>
			<!--- // Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
	</div>

	<?php endif; ?>
<?php
get_footer();
