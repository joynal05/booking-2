<?php
/**
 * Single destination
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

global $post, $transfers_theme_globals, $transfers_plugin_globals, $current_user, $transfers_destinations_post_type, $transfers_plugin_post_types;

$price_decimal_places = $transfers_plugin_globals->get_price_decimal_places();
$default_currency_symbol = $transfers_plugin_globals->get_default_currency_symbol();
$show_currency_symbol_after = $transfers_plugin_globals->show_currency_symbol_after();

$advanced_search_url = '';

$global_advanced_search_url = get_permalink(transfers_get_current_language_page_id($transfers_plugin_globals->get_advanced_search_page_id()));
$current_page_url = transfers_get_current_page_url();

if (!empty($global_advanced_search_url))
	$advanced_search_url = $global_advanced_search_url;
else
	$advanced_search_url = $current_page_url;

if ( have_posts() ) {
	the_post(); 
	$destination_obj = new transfers_destination($post);
	$destination_extra_fields = $transfers_plugin_globals->get_destination_extra_fields();
	?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1>Transfers <?php the_title(); ?></h1>
<!--				<?php
				$allowed_tags = Transfers_Theme_Utils::get_allowed_breadcrumbs_tags_array();
				if (isset($transfers_theme_globals)) {
					$breadcrumbs_html = $transfers_theme_globals->get_breadcrumbs();

				echo wp_kses($breadcrumbs_html, $allowed_tags);

				}
				?>-->
<nav role="navigation" class="breadcrumbs"><ul><li><?php
if ( function_exists('yoast_breadcrumb') ) {
  yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
}?></li></ul></nav>
			</div>
		</div>
	</header>
	<?php
		$content_css_class = 'full-width';
		$connecting_destination_results = $transfers_plugin_post_types->list_connecting_destinations('post_title', 'ASC', $destination_obj->get_id());
		if ( count($connecting_destination_results) > 0 ) {
			$content_css_class = 'one-half';
		}
	?>
	
	<?php if ( has_post_thumbnail() ) { ?>
	<div class="keyvisual" style="background-image:url(<?php echo esc_url($destination_obj->get_main_image(TRANSFERS_FULL_IMAGE_SIZE)); ?>)"></div>
	<?php } ?>
	<?php
	$widget_args = array('before_widget'  => '', 'after_widget'  => '', 'before_title'  => '<h3>', 'after_title'   => '</h3>', 'widget_reverse_color_scheme' => true);
	the_widget('transfers_Advanced_Search_Widget', null, $widget_args); 
	?>
	
	<div class="wrap">
		<div class="row">
			<!--- Content -->
			<article id="post-<?php the_ID(); ?>" <?php post_class($content_css_class . " content textongrey"); ?>>
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
				<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				<?php Transfers_Plugin_Utils::render_extra_fields('destination_extra_fields', $destination_extra_fields, $destination_obj); ?>
			</article>		
			<!--- // Content -->
			<?php if ( count($connecting_destination_results) > 0 ) { ?>
			<div class="one-half content offset">
				<table class="hover">
					<tbody>
					<tr>
						<th><?php esc_html_e('Destinations', 'transfers') ?></th>
						<th><?php esc_html_e('Private', 'transfers') ?></th>
						<?php if ($transfers_plugin_globals->enable_shared_transfers()) {?>
						<th><?php esc_html_e('Shared', 'transfers') ?></th>
						<?php } ?>
					</tr>
					<?php
							$current_language_code = transfers_get_current_language_code();
					
							$today = strtotime(date(TRANSFERS_PHP_DATE_FORMAT_NO_TIME));
							$tomorrow = strtotime( "+1 day", $today );
							
							foreach ($connecting_destination_results as $available_result) { 
							
								$the_date = ($tomorrow > strtotime($available_result->start_datetime)) ? $tomorrow : strtotime($available_result->start_datetime);							
								
								$current_language_destination_to_id = transfers_get_current_language_post_id($available_result->destination_to_id, 'destination');
							
								$search_url = $advanced_search_url . "?ppl=1&trip=1&p1=" . $destination_obj->get_id() . "&dep=" . date(TRANSFERS_PHP_DATE_FORMAT_NO_TIME, $the_date);
								$formatted_private_price = number_format_i18n( $available_result->price_private_min, $price_decimal_places );
								$formatted_share_price = number_format_i18n( $available_result->price_share_min, $price_decimal_places );
					?>
					<tr>
						<!--<td><?php echo esc_html($available_result->destination_to); ?></td>-->  <!-- Cambio en enlaces destinos individual o Single Destination -->
                        <td><a style="color:#b2b2b2" href="<?php echo esc_url($search_url .= '&d1=' . $current_language_destination_to_id); ?>"><?php esc_html_e('Taxi from ', 'transfers') ?><?php the_title(); ?><?php esc_html_e(' to ', 'transfers') ?><br /><b style="color:#191f26 !important"><?php echo esc_html($available_result->destination_to); ?></b></a></td>
						<td>
							<a href="<?php echo esc_url($search_url .= '&d1=' . $current_language_destination_to_id); ?>">
						<?php
							
							if (!$show_currency_symbol_after)
								echo esc_html($default_currency_symbol . $formatted_private_price);
							else
								echo esc_html($formatted_private_price . $default_currency_symbol);
						?>
							</a>
						</td>
						<?php if ($transfers_plugin_globals->enable_shared_transfers()) {?>
						<td>
							<?php if ($available_result->price_share_min > 0) { ?>
							<a href="<?php echo esc_url($search_url); ?>">
							<?php
								if (!$show_currency_symbol_after)
									echo esc_html($default_currency_symbol . $formatted_share_price);
								else
									echo esc_html($formatted_share_price . $default_currency_symbol);
							?>						
							</a>
							<?php } ?>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php 
	if ($destination_obj->is_parent()) {
		$child_destination_results = $transfers_destinations_post_type->list_destinations(0, -1, 'post_title', 'ASC', $destination_obj->get_id());
		
		if ( count($child_destination_results) > 0 && $child_destination_results['total'] > 0 ) {
		?>
	<!-- Micro Locations -->
	<div class="white microlocations">
		<div class="wrap">
			<h3 class="wow fadeIn"><?php esc_html_e('Select your departure location', 'transfers'); ?></h3>
			<div class="row">		
		<?php	
			$i = 0;
			foreach ($child_destination_results['results'] as $child_destination) {
				$child_destination_obj = new transfers_destination($child_destination);
				if ($i == 0)
					echo '<div class="one-fourth wow fadeInUp">';
			?>
			<p><a href="<?php echo esc_url(get_permalink($child_destination_obj->get_id())); ?>"><?php echo esc_html($child_destination_obj->get_title()); ?></a></p>
			<?php				
				if ($i == 4) {
					echo '</div>';
					$i = 0;
				} else {
					$i++;
				}
			}
		?>
			</div>
		</div>
	</div>
	<!-- Micro Locations -->
	<?php
		}	
	}
} 

get_footer();