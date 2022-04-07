<?php
/**
/* Template Name: User Register
 * The template for displaying the User Register page.
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
$terms_page_url = $transfers_theme_globals->get_terms_page_url();
$add_captcha_to_forms = $transfers_theme_globals->add_captcha_to_forms();
$let_users_set_pass = $transfers_theme_globals->let_users_set_pass();
$enc_key = $transfers_theme_globals->get_enc_key();

$errors = array();

if (isset($_POST['user_login']) &&  isset($_POST['email']) && isset($_POST['transfers_register_form_nonce']) && wp_verify_nonce($_POST['transfers_register_form_nonce'], 'transfers_register_form')){

	// user data array
	$register_userdata = array(
		'user_login' => sanitize_text_field($_POST['user_login']),
		'user_email' => sanitize_text_field($_POST['email']),
		'email' => sanitize_text_field($_POST['email']),
		'first_name' => '',
		'last_name' => '',
		'user_url' => '',
		'description' => ''
	);

	if ($let_users_set_pass) {
		$register_userdata['user_pass'] = sanitize_text_field($_POST['password']);
		$register_userdata['confirm_pass'] = sanitize_text_field($_POST['repeat_password']);
	} else {
		$register_userdata['user_pass'] = wp_generate_password(10, false);
		$register_userdata['confirm_pass'] = $register_userdata['user_pass'];
	}

	// custom user meta array
	$register_usermeta = array(
		'agree' =>((isset($_POST['checkboxagree']) && !empty($_POST['checkboxagree'])) ? '1' : '0'),
		'user_activation_key' => wp_generate_password(20, false)
	);

	// validation
	// validate username
	if (trim($register_userdata['user_login']) == '') {
		$errors['user_login'] = esc_html__('Username is required.', 'transfers');
	}
	else if (strlen($register_userdata['user_login']) < 6) {
		$errors['user_login'] = esc_html__('Sorry, username must be 6 characters or more.', 'transfers');
	}
	else if (!validate_username($register_userdata['user_login'])) {
		$errors['user_login'] = esc_html__('Sorry, the username you provided is invalid.', 'transfers');
	}
	else if (username_exists($register_userdata['user_login'])) {
		$errors['user_login'] = esc_html__('Sorry, that username already exists.', 'transfers');
	}

	if ($let_users_set_pass) {
		// validate password
		if (trim($register_userdata['user_pass']) == '') {
			$errors['user_pass'] = esc_html__('Password is required.', 'transfers');
		}
		else if (strlen($register_userdata['user_pass']) < 6) {
			$errors['user_pass'] = esc_html__('Sorry, password must be 6 characters or more.', 'transfers');
		}
		else if ($register_userdata['user_pass'] !== $register_userdata['confirm_pass']) {
			$errors['confirm_pass'] = esc_html__('Password and confirm password fields must match.', 'transfers');
		}
	}

	// validate user_email
	if (!is_email($register_userdata['user_email'])) {
		$errors['user_email'] = esc_html__('You must enter a valid email address.', 'transfers');
	}
	else if (email_exists($register_userdata['user_email'])) {
		$errors['user_email'] = esc_html__('Sorry, that email address is already in use.', 'transfers');
	}

	// validate agree
	if ($register_usermeta['agree'] == '0'){
		$errors['agree'] = esc_html__('You must agree to our terms &amp; conditions to sign up.', 'transfers');
	}

	// validate captcha
	if ($add_captcha_to_forms) {
		if (isset($_POST['c_val_s_reg']) && isset($_POST['c_val_1_reg']) && isset($_POST['c_val_2_reg'])) {
			$c_val_s = intval(wp_kses($_POST['c_val_s_reg'], ''));
			$c_val_1 = intval(transfers_decrypt(wp_kses($_POST['c_val_1_reg'], ''), $enc_key));
			$c_val_2 = intval(transfers_decrypt(wp_kses($_POST['c_val_2_reg'], ''), $enc_key));

			if ($c_val_s != ($c_val_1 + $c_val_2)) {
				$errors['captcha'] = esc_html__('You must input the correct captcha answer.', 'transfers');
			}
		} else {
			$errors['captcha'] = esc_html__('You must input a valid captcha answer.', 'transfers');
		}
	}

	if (empty($errors)){

		// insert new user
		$new_user_id = wp_insert_user($register_userdata);

		$new_user = get_userdata($new_user_id);

		$user_obj = new WP_User($new_user_id);

		// Temporarily save plaintext pass
		update_user_meta($new_user_id, 'user_pass', $register_userdata['user_pass']);

		$user_obj->set_role('pending');

		// update custom user meta
		foreach ($register_usermeta as $key => $value) {
			update_user_meta($new_user_id, $key, $value);
		}

		// send notification
		Transfers_Theme_Utils::send_activation_notification($new_user_id);

		// refresh
		wp_redirect(esc_url_raw (add_query_arg(array('action' => 'registered'), get_permalink())));
		exit;
	}
}

$c_val_1_reg = mt_rand(1, 20);
$c_val_2_reg = mt_rand(1, 20);

$c_val_1_reg_str = transfers_encrypt($c_val_1_reg, $enc_key);
$c_val_2_reg_str = transfers_encrypt($c_val_2_reg, $enc_key);

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
					if (isset($_GET['action']) && $_GET['action'] == 'registered') {
					?>
					<p class="success">
						<?php esc_html_e('Account was successfully created. Please click the activation link in the email we just sent you to complete the registration process.', 'transfers') ?>
					</p>
					<?php
					} else if (isset($_GET['action']) && $_GET['action'] == 'activate' && isset($_GET['user_id']) && isset($_GET['activation_key'])) {
						if (Transfers_Theme_Utils::activate_user(wp_kses($_GET['user_id'], ''), wp_kses($_GET['activation_key'], ''))) {
						?>
						<p class="success">
							<?php echo sprintf(__('User account successfully activated. Please proceed to the <a href="%s">login</a> page to login in.', 'transfers'), $login_page_url); ?>
						</p>
						<?php
						} else {
						?>
						<p class="error">
							<?php esc_html_e('An error was encountered when attempting to activate your account.', 'transfers') ?>
						</p>
						<?php
						}
					} else if (isset($_GET['action']) && $_GET['action'] == 'sendactivation' && isset($_GET['user_id'])) {
						if (Transfers_Theme_Utils::send_activation_notification(wp_kses($_GET['user_id'], '', ''))) {
						?>
						<p class="success">
							<?php esc_html_e('Activation link was successfully sent.', 'transfers') ?>
						</p>
						<?php
						} else { ?>
						<p class="error">
							<?php esc_html_e('An error was encountered when attempting to send the activation link. Please try again later.', 'transfers') ?>
						</p>
						<?php
						}
					} else {
			?>
						<form action="<?php echo esc_url( get_permalink() ); ?>" method="post">
							<?php if (is_array($errors) && count($errors) > 0) { ?>
							<div class="error f-row">
								<p><?php esc_html_e( 'Errors were encountered when processing your registration request.', 'transfers' ) ?></p>
								<?php
								foreach ($errors as $error) {
									echo '<p>' . $error . '</p>';
								}
								?>
							</div>
							<?php } ?>
							<div class="f-row">
								<div class="full-width">
									<label for="user_login"><?php esc_html_e('Your username', 'transfers') ?></label>
									<input tabindex="1" type="text" id="user_login" name="user_login" value="<?php echo isset($register_userdata) && isset($register_userdata['user_login']) ? $register_userdata['user_login'] : ''; ?>" />
								</div>
							</div>
							<div class="f-row">
								<div class="full-width">
									<label for="email"><?php esc_html_e('Your email address', 'transfers') ?></label>
									<input tabindex="2" type="email" id="user_email" name="email" value="<?php echo isset($register_userdata) && isset($register_userdata['user_email']) ? $register_userdata['user_email'] : ''; ?>" />
								</div>
							</div>
							<?php do_action('register_form'); ?>
							<?php if ($add_captcha_to_forms) { ?>
							<div class="f-row captcha">
								<div class="full-width">
									<label><?php echo sprintf(__('How much is %d + %d', 'transfers'), $c_val_1_reg, $c_val_2_reg) ?>?</label>
									<input tabindex="5" type="text" required="required" id="c_val_s_reg" name="c_val_s_reg" />
									<input type="hidden" name="c_val_1_reg" id="c_val_1_reg" value="<?php echo esc_attr($c_val_1_reg_str); ?>" />
									<input type="hidden" name="c_val_2_reg" id="c_val_2_reg" value="<?php echo esc_attr($c_val_2_reg_str); ?>" />
								</div>
							</div>
							<?php } ?>
							<div class="f-row">
								<div class="full-width check">
									<input tabindex="6" type="checkbox" id="checkboxagree" name="checkboxagree" />
									<label for="checkbox"><?php echo sprintf(__('I agree to the <a href="%s">terms &amp; conditions</a>.', 'transfers'), esc_url($terms_page_url)); ?></label>
								</div>
							</div>
							<?php wp_nonce_field( 'transfers_register_form', 'transfers_register_form_nonce' ) ?>
							<div class="f-row">
								<div class="full-width">
									<input tabindex="7" type="submit" id="register" name="register" value="<?php esc_attr_e('Create an account', 'transfers'); ?>" class="btn color medium full" />
								</div>
							</div>
						</form>
					<?php } ?>
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
