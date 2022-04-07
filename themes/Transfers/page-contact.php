<?php
/**
/* Template Name: Contact Page
 * The template for displaying the contact us page.
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

$enc_key = $transfers_theme_globals->get_enc_key();
$add_captcha_to_forms = $transfers_theme_globals->add_captcha_to_forms();

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

$c_val_1 = mt_rand(1, 20);
$c_val_2 = mt_rand(1, 20);

$c_val_1_str = transfers_encrypt($c_val_1, $enc_key);
$c_val_2_str = transfers_encrypt($c_val_2, $enc_key);
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
	}
	?>	
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
				<!-- Form -->
				<div class="thankyou box" style="display:none">
					<a id="thankyou"></a>
					<h6><?php esc_html_e('Email sent successfully.', 'transfers'); ?></h6>
					<p><?php esc_html_e('Thank you for contacting us. We will get back to you as soon as we can.', 'transfers'); ?></p>
				</div>
				<div class="error" style="display:none">
					<p><?php esc_html_e('Errors were encountered when attempting to submit the contact form. Please correct your errors and submit again.', 'transfers'); ?></p>
				</div>
				<form method="post" action="<?php echo esc_url(transfers_get_current_page_url()); ?>" name="form-contact" id="form-contact">
					<div class="f-row">
						<div class="one-half">
							<label for="contact_name"><?php esc_html_e('Name and surname', 'transfers'); ?></label>
							<input type="text" id="contact_name" name="contact_name" />
						</div>
						<div class="one-half">
							<label for="contact_email"><?php esc_html_e('Email address', 'transfers'); ?></label>
							<input type="email" id="contact_email" name="contact_email" />
						</div>
					</div>
					<div class="f-row">
						<div class="full-width">
							<label for="contact_message"><?php esc_html_e('Message', 'transfers'); ?></label>
							<textarea id="contact_message" name="contact_message"></textarea>
						</div>
					</div>
					<?php if ($add_captcha_to_forms) { ?>
					<div class="f-row captcha">
						<div class="full-width">
							<label><?php echo sprintf(__('How much is %d + %d', 'transfers'), $c_val_1, $c_val_2) ?>?</label>
							<input type="text" id="c_val_s_con" name="c_val_s" class="required" />
							<input type="hidden" name="c_val_1" id="c_val_1_con" value="<?php echo esc_attr($c_val_1_str); ?>" />
							<input type="hidden" name="c_val_2" id="c_val_2_con" value="<?php echo esc_attr($c_val_2_str); ?>" />
						</div>
					</div>
					<?php } ?>
					<?php wp_nonce_field('contact_form','contact_form_nonce'); ?>
					<div class="f-row">
						<input type="submit" value="<?php esc_attr_e('Submit', 'transfers'); ?>" id="submit-contact" name="submit-contact" class="btn color medium right" />
					</div>
				</form>
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