<?php
/**
/* Template Name: Contact Form 7 Page
 * The template for displaying the contact us page using a contact form 7 form.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */
 
get_header();  
get_sidebar('under-header');

global $post, $transfers_theme_globals;

$contact_address_latitude = $transfers_theme_globals->get_contact_address_latitude();
$contact_address_longitude = $transfers_theme_globals->get_contact_address_longitude();
$contact_company_name = $transfers_theme_globals->get_contact_company_name();
$contact_address = $transfers_theme_globals->get_contact_address();

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
	<script>
		window.contactAddressLatitude = <?php echo json_encode($contact_address_latitude); ?>;
		window.contactAddressLongitude = <?php echo json_encode($contact_address_longitude); ?>;
		window.contactCompanyName = <?php echo json_encode($contact_company_name); ?>;
		window.contactAddress = <?php echo json_encode($contact_address); ?>;
	</script>
	<?php  if ( have_posts() ) : the_post(); ?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php the_title(); ?></h1>
				<?php 
				$allowed_tags = Transfers_Theme_Utils::get_allowed_breadcrumbs_tags_array();
				$breadcrumbs_html = $transfers_theme_globals->get_breadcrumbs();
				echo wp_kses($breadcrumbs_html, $allowed_tags); 
				?>
			</div>
		</div>
	</header>
	
	<?php if (!empty($contact_address_latitude) && !empty($contact_address_longitude)) { 
		$google_maps_key = $transfers_theme_globals->get_google_maps_key();
		$allowed_tags = transfers_get_allowed_content_tags_array();
		if (!empty($google_maps_key)) {
	?>
	<!--- Google map -->
	<div id="map_canvas" class="gmap"></div>
	<!--- //Google map -->
	<?php 
		} else {?>
	<div class="wrap">
		<div class="row">	
			<div class="<?php echo esc_attr($section_class); ?>">
				<p><?php echo wp_kses(__('Before using google maps you must go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google maps api console</a> and get an api key. After you do, please proceed to Appearance -> Theme options -> Configuration settings and enter your key in the field labeled "Google maps api key"', 'transfers'), $allowed_tags); ?></p>
			</div>
		</div>
	</div>
		<?php }	
	} ?>	
	<div class="wrap">
		<div class="row">
			<!--- Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<div class="<?php echo esc_attr($section_class); ?>">
				<div class="textongrey content">
					<?php if ( has_post_thumbnail() ) { ?>
					<figure class="entry-featured">						
						<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => '')); ?>
						<div class="overlay">
							<a href="<?php esc_url(get_the_permalink()) ?>" class="expand">+</a>
						</div>
					</figure>
					<?php } ?>
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
					<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				</div>
			</div>
			<!-- //Form -->
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