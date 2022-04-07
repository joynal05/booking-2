<?php
/**
/* Template Name: User Reset Password
 * The template for displaying the User Reset Password page.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */

if (is_user_logged_in()) {
	wp_redirect(home_url('/'));
	exit;
}

global $transfers_theme_globals;

$login_page_url = $transfers_theme_globals->get_login_page_url();
$register_page_url = $transfers_theme_globals->get_register_page_url();
$redirect_to_after_login_url = $transfers_theme_globals->get_redirect_to_after_login_page_url();
$reset_password_page_url = $transfers_theme_globals->get_reset_password_page_url();

$errors = array();

if (isset($_POST['_wpnonce'])) {

	if (wp_verify_nonce($_POST['_wpnonce'], 'resetpassword_form')){

		if (!isset($_POST['user_login'])) {
			$errors['user_login'] = esc_html__('You must enter an email address or username to proceed.', 'transfers');
		} else {
			// user data array
			$resetpassword_userdata = array(
				'user_login' => sanitize_text_field($_POST['user_login'])
			);

			// custom user meta array
			$resetpassword_usermeta = array(
				'user_resetpassword_key' => wp_generate_password(20, false),
				'user_resetpassword_datetime' => date('Y-m-d H:i:s', time())
			);

			// validate email
			if (!is_email($resetpassword_userdata['user_login'])) {
				$user = get_user_by('login', $resetpassword_userdata['user_login']);
				if (!$user)
					$errors['user_login'] = esc_html__('You must enter a valid and existing email address or username.', 'transfers');
			} else if (!email_exists($resetpassword_userdata['user_login'])) {
				$errors['user_login'] = esc_html__('You must enter a valid and existing email address or username.', 'transfers');
			} else {
				$user = get_user_by('email', $resetpassword_userdata['user_login']);
			}

			if(empty($errors)){

				// update custom user meta
				foreach ($resetpassword_usermeta as $key => $value) {
					update_user_meta($user->ID, $key, $value);
				}

				Transfers_Theme_Utils::resetpassword_notification($user->ID);

				// refresh
				wp_redirect(esc_url_raw(add_query_arg(array('action' => 'resetpasswordnotification'), get_permalink())));
				exit;
			}
		}
	}
}

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

$section_class = 'one-half';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right')
	$section_class = 'three-fourth';

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
			<!--- Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<div class="<?php echo esc_attr($section_class); ?> modal">
				<article <?php post_class("static-content post"); ?>>
					<?php if (has_post_thumbnail()) { ?>
					<figure class="entry-featured">
						<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => '')); ?>
						<div class="overlay">
							<a href="<?php esc_url(get_the_permalink()) ?>" class="expand">+</a>
						</div>
					</figure>
					<?php } ?>
					<?php if (!empty($post->post_content)) { ?>
					<div class="entry-content">
						<?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', 'transfers')); ?>
					</div>
					<?php } ?>
					<div class="box">
						<form action="<?php echo esc_url( get_permalink() ); ?>" method="post">
							<?php if (is_array($errors) && count($errors) > 0) { ?>
							<div class="error f-row">
								<p><?php esc_html_e( 'Errors were encountered when processing your reset password request.', 'transfers' ) ?></p>
								<?php
								foreach ($errors as $error) {
									echo '<p>' . $error . '</p>';
								}
								?>
							</div>
							<?php } ?>
							<?php
							if(isset($_GET['action']) && $_GET['action'] == 'resetpasswordnotification') {
							?>
							<p class="success">
								<?php esc_html_e('Please confirm the request to reset your password by clicking the link sent to your email address.', 'transfers') ?>
							</p>
							<?php
							} else if(isset($_GET['action']) && $_GET['action'] == 'resetpassword' && isset($_GET['user_id']) && isset($_GET['resetpassword_key'])) {

								$user_id = wp_kses($_GET['user_id'], '', '');
								$resetpassword_key = wp_kses($_GET['resetpassword_key'], '', '');
								$new_password = Transfers_Theme_Utils::resetpassword($user_id, $resetpassword_key);

								if($new_password && Transfers_Theme_Utils::newpassword_notification($user_id, $new_password)) { ?>
									<p class="success">
										<?php esc_html_e('Your password was successfully reset. We have sent the new password to your email address.', 'transfers') ?>
									</p>
								<?php } else { ?>
									<p class="error">
										<?php esc_html_e('We encountered an error when attempting to reset your password. Please try again later.', 'transfers') ?>
									</p>
								<?php }
							} else { ?>
							<div class="f-row">
								<div class="full-width">
									<label for="user_login"><?php esc_html_e('Your username or email address', 'transfers'); ?></label>
									<input type="text" name="user_login" id="user_login" value="<?php echo isset($_POST['user_login']) ? sanitize_text_field($_POST['user_login']) : ''; ?>" />
								</div>
							</div>
							<?php wp_nonce_field('resetpassword_form') ?>
							<div class="f-row">
								<div class="full-width">
									<input type="submit" id="reset" name="reset" value="<?php esc_attr_e('Reset password', 'transfers'); ?>" class="btn color medium full" />
								</div>
							</div>
							<?php } ?>
						</form>
					</div>
					<!--//Login-->
				</article>
			</div>
			<!--- // Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
	</div>
	<?php endwhile; ?>
<?php
get_footer();
