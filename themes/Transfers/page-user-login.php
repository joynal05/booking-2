<?php
/**
/* Template Name: User Login
 * The template for displaying the User Login page.
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

$login = null;
if (isset($_POST['log']) && isset($_POST['transfers_login_form_nonce']) && wp_verify_nonce($_POST['transfers_login_form_nonce'], 'transfers_login_form')){

	$is_ssl = is_ssl();
	$login = wp_signon(
		array(
			'user_login' => sanitize_text_field($_POST['log']),
			'user_password' => sanitize_text_field($_POST['pwd']),
			'remember' =>((isset($_POST['rememberme']) && $_POST['rememberme']) ? true : false)
		),
		$is_ssl
	);

	if (!is_wp_error($login)) {
		wp_redirect($redirect_to_after_login_url);
		exit;
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
          <?php
          if (is_wp_error( $login ) && isset($login->errors['too_many_retries'])) {
			      echo '<div class="error message-box">' . esc_html__('Too many login attempts. Please try again later.', 'bookyourtravel') . '</div>';
		      } else {
                  ?>
						<form action="<?php echo esc_url(get_permalink()); ?>" method="post">
							<?php if (is_wp_error($login)) { ?>
								<div class="f-row error">
									<p><?php esc_html_e('Incorrect username or password. Please try again.', 'transfers') ?></p>
								</div>
							<?php } ?>
							<div class="f-row">
								<div class="full-width">
									<label for="log"><?php esc_html_e('Your username', 'transfers'); ?></label>
									<input type="text" name="log" id="log" value="<?php echo isset($_POST['log']) ? sanitize_text_field($_POST['log']) : ''; ?>" />
								</div>
							</div>
							<div class="f-row">
								<div class="full-width">
									<label for="pwd"><?php esc_html_e('Your password', 'transfers'); ?></label>
									<input type="password" name="pwd" id="pwd" />
								</div>
							</div>
							<div class="f-row">
								<div class="full-width check">
									<input type="checkbox"  name="rememberme" id="rememberme" <?php echo isset($_POST['rememberme']) ? 'checked' : ''; ?> />
									<label for="checkbox"><?php esc_html_e('Remember me next time', 'transfers'); ?></label>
								</div>
							</div>
							<?php wp_nonce_field('transfers_login_form', 'transfers_login_form_nonce') ?>
							<input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />
							<div class="f-row">
								<div class="full-width">
									<input type="submit" id="login" name="login" value="<?php esc_attr_e('Login', 'transfers'); ?>" class="btn color medium full" />
								</div>
							</div>
							<p><a href="<?php echo esc_url($reset_password_page_url); ?>"><?php esc_html_e('Forgotten your password?', 'transfers'); ?></a> <?php esc_html_e('Don\'t have an account yet?', 'transfers'); ?> <a href="<?php echo esc_url($register_page_url); ?>"><?php esc_html_e('Sign up', 'transfers'); ?></a>.</p>
						</form>
          <?php
              }
          ?>
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
