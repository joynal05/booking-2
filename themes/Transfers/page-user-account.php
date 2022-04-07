<?php
/**
/* Template Name: User Account
 * The template for displaying the User Account page.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */
 
if (!is_user_logged_in()) {
	wp_redirect(home_url('/'));
	exit;
}

global $current_user;
if (!isset($current_user))
	$current_user = wp_get_current_user();

$user_info = get_userdata($current_user->ID);
	
global $transfers_theme_globals;

$login_page_url = $transfers_theme_globals->get_login_page_url();
$register_page_url = $transfers_theme_globals->get_register_page_url();
$redirect_to_after_login_url = $transfers_theme_globals->get_redirect_to_after_login_page_url();
$reset_password_page_url = $transfers_theme_globals->get_reset_password_page_url();
$user_account_page_url = $transfers_theme_globals->get_user_account_page_url(); 

get_header();  
get_sidebar('under-header');

global $post;

$page_id = $post->ID;
$page_custom_fields = get_post_custom($page_id);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'three-fourth';
if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
	$section_class = 'one-half';
	
$countries = $transfers_theme_globals->get_countries();
?>
	<?php  while (have_posts()) : the_post(); ?>
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
	<div class="wrap">
		<div class="row">
			<?php $content = get_the_content(); 
			if (!empty($content)) { ?>
			<!--- Content -->
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
			<!--- // Content -->
			<?php } ?>
			<?php
			global $exclude_left_sidebar_wrap;
			$exclude_left_sidebar_wrap = true;
			// this page is a special case, we always show the left sidebar because of the menu listing the service element titles
			?>
			<!--- Content -->
			<div class="row">
				<!--- Sidebar -->
				<aside class="one-fourth sidebar left">
					<ul>
						<li>
							<!-- Widget -->
							<div class="widget">
								<ul class="categories">
									<li class="active"><a href="#tab1" class="anchor"><?php esc_html_e('General', 'transfers'); ?></a></li>
									<li><a href="#tab2" class="anchor"><?php esc_html_e('Security', 'transfers'); ?></a></li>
									<li><a href="#tab3" class="anchor"><?php esc_html_e('Billing', 'transfers'); ?></a></li>
								</ul>
							</div>
							<!-- //Widget -->
						</li>
						<?php
						if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
							get_sidebar('left');
						?>
					</ul>
				</aside>
				<!--- // Sidebar -->
				<div class="content <?php echo esc_attr($section_class); ?>">
					<!-- Tab 1 -->
					<div class="success box" style="display:none">
						<a id="success"></a>
						<p><?php esc_html_e('Your changes were saved successfully!', 'transfers'); ?></p>
					</div>
					<div class="error" style="display:none">
						<p><?php esc_html_e('Errors were encountered when attempting to submit the form. Please correct your errors and submit again.', 'transfers'); ?></p>
					</div>
					<article class="single" id="tab1">
						<div class="box">
							<h2><?php esc_html_e('General settings', 'transfers') ?></h2>
							<form method="post" action="<?php echo esc_url(transfers_get_current_page_url()); ?>" name="form-account-general" id="form-account-general">
								<fieldset>
									<div class="f-row">
										<div class="one-half">
											<label for="username"><?php esc_html_e('Username', 'transfers') ?></label>
											<input type="text" id="username" disabled value="<?php echo esc_attr($user_info->user_login); ?>" />
										</div>
										<div class="one-half">
											<label for="user_email"><?php esc_html_e('Email', 'transfers') ?></label>
											<input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($user_info->user_email); ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-half">
											<label for="first_name"><?php esc_html_e('First name', 'transfers') ?></label>
											<input type="text" id="first_name" name="first_name" value="<?php echo isset($user_info->first_name) ? $user_info->first_name : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="last_name"><?php esc_html_e('Last name', 'transfers') ?></label>
											<input type="text" id="last_name" name="last_name" value="<?php echo isset($user_info->last_name) ? $user_info->last_name : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-half">
											<label for="user_url"><?php esc_html_e('Website', 'transfers') ?></label>
											<input type="text" id="user_url" name="user_url" value="<?php echo isset($user_info->user_url) ? $user_info->user_url : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="nickname"><?php esc_html_e('Nickname', 'transfers') ?></label>
											<input type="text" id="nickname" name="nickname" value="<?php echo isset($user_info->nickname) ? $user_info->nickname : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-third">
											<label for="googleplus"><?php esc_html_e('Google+', 'transfers') ?></label>
											<input type="text" id="googleplus" name="googleplus" value="<?php echo isset($user_info->googleplus) ? $user_info->googleplus : ''; ?>" />
										</div>
										<div class="one-third">
											<label for="twitter"><?php esc_html_e('Twitter', 'transfers') ?></label>
											<input type="text" id="twitter" name="twitter" value="<?php echo isset($user_info->twitter) ? $user_info->twitter : ''; ?>" />
										</div>
										<div class="one-third">
											<label for="facebook"><?php esc_html_e('Facebook', 'transfers') ?></label>
											<input type="text" id="facebook" name="facebook" value="<?php echo isset($user_info->facebook) ? $user_info->facebook : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="full-width">
											<label for="description"><?php esc_html_e('Bio', 'transfers') ?></label>
											<textarea id="description" name="description" rows="5"><?php echo isset($user_info->description) ? $user_info->description : ''; ?></textarea>
										</div>
									</div>
									<div class="f-row">
										<input type="submit" value="<?php esc_attr_e('Save Changes', 'transfers') ?>" id="save-general" name="save-general" class="btn color medium" />
									</div>
								</fieldset>
							</form>
						</div>
					</article>
					
					<article class="single" id="tab2" style="display:none">					
						<div class="box">						
							<h2><?php esc_html_e('Security settings', 'transfers') ?></h2>
							<form method="post" action="<?php echo esc_url(transfers_get_current_page_url()); ?>" name="form-account-security" id="form-account-security">
								<fieldset>
									<div class="f-row">
										<div class="one-third">
											<label for="old_password"><?php esc_html_e('Current password', 'transfers') ?></label>
											<input type="password" id="old_password" name="old_password" />
										</div>
										<div class="one-third">
											<label for="new_password"><?php esc_html_e('New password', 'transfers') ?></label>
											<input type="password" id="new_password" name="new_password" />
										</div>
										<div class="one-third">
											<label for="confirm_password"><?php esc_html_e('Confirm new password', 'transfers') ?></label>
											<input type="password" id="confirm_password" name="confirm_password" />
										</div>
									</div>							
									<div class="f-row">
										<input type="submit" value="<?php esc_attr_e('Save Changes', 'transfers') ?>" id="save-security" name="save-security" class="btn color medium" />
									</div>
								</fieldset>
							</form>
						</div>
					</article>				
					<article class="single" id="tab3" style="display:none">				
						<div class="box">
							<h2><?php esc_html_e('Billing settings', 'transfers') ?></h2>
							<form method="post" action="<?php echo esc_url(transfers_get_current_page_url()); ?>" name="form-account-billing" id="form-account-billing">
								<fieldset>
									<div class="f-row">
										<div class="one-half">
											<label for="billing_first_name"><?php esc_html_e('First name', 'transfers') ?></label>
											<input type="text" id="billing_first_name" name="billing_first_name" value="<?php echo isset($user_info->billing_first_name) ? $user_info->billing_first_name : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="billing_last_name"><?php esc_html_e('Last name', 'transfers') ?></label>
											<input type="text" id="billing_last_name" name="billing_last_name" value="<?php echo isset($user_info->billing_last_name) ? $user_info->billing_last_name : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-half">
											<label for="billing_company"><?php esc_html_e('Company name', 'transfers') ?></label>
											<input type="text" id="billing_company" name="billing_company" value="<?php echo isset($user_info->billing_company) ? $user_info->billing_company : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="billing_email"><?php esc_html_e('Email address', 'transfers') ?></label>
											<input type="email" id="billing_email" name="billing_email" value="<?php echo isset($user_info->billing_email) ? $user_info->billing_email : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-half">
											<label for="billing_phone"><?php esc_html_e('Telephone', 'transfers') ?></label>
											<input type="text" id="billing_phone" name="billing_phone" value="<?php echo isset($user_info->billing_phone) ? $user_info->billing_phone : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="billing_address_1"><?php esc_html_e('Street address', 'transfers') ?></label>
											<input type="text" id="billing_address_1" name="billing_address_1" value="<?php echo isset($user_info->billing_address_1) ? $user_info->billing_address_1 : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-half">
											<label for="billing_city"><?php esc_html_e('City', 'transfers') ?></label>
											<input type="text" id="billing_city" name="billing_city" value="<?php echo isset($user_info->billing_city) ? $user_info->billing_city : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="billing_postcode"><?php esc_html_e('Post code', 'transfers') ?></label>
											<input type="text" id="billing_postcode" name="billing_postcode" value="<?php echo isset($user_info->billing_postcode) ? $user_info->billing_postcode : ''; ?>" />
										</div>
									</div>
									<div class="f-row">
										<div class="one-half">
											<label for="billing_state"><?php esc_html_e('State', 'transfers') ?></label>
											<input type="text" id="billing_state" name="billing_state" value="<?php echo isset($user_info->billing_state) ? $user_info->billing_state : ''; ?>" />
										</div>
										<div class="one-half">
											<label for="billing_country"><?php esc_html_e('Country', 'transfers') ?></label>
											<select id="billing_country" name="billing_country">
											<?php									
												foreach ($countries as $short_country => $long_country) {
													echo sprintf("<option value='%s'>%s</option>", $short_country, $long_country);
												}
											?>
											</select>
										</div>
									</div>
									<div class="f-row">
										<input type="submit" value="<?php esc_attr_e('Save Changes', 'transfers') ?>" id="save-billing" name="save-billing" class="btn color medium" />
									</div>
								</fieldset>
							</form>
						</div>
					
					</article>
				</div>
				<?php
				if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
					get_sidebar('right');
				?>
			</div>
			<!--- // Content -->

		</div>
	</div>	
	<?php endwhile; ?>	
<?php 
get_footer();