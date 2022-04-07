<?php

class Transfers_Theme_Utils {

	public static function round_to_nearest_anything($value, $anything)
	{
		if ($anything > 0) {
		
			return (int)(($value + ($anything - 1))/$anything) * $anything;
		
			// $mod = $value % $anything;
			
			// echo 'value ' . $value . 'mod ' . $mod . ' anything ' . $anything;
			
			// $rounded = $value + ($mod < ($anything) ? $mod : $anything + $mod);
			// echo 'rounded ' . $rounded;
			// return $rounded;
		}
		return $value;
	}

	public static function send_activation_notification($user_id) {

		global $transfers_theme_globals;

		$user = get_userdata($user_id);
		if (!$user) 
			return false;

		$site_name 			= get_bloginfo('name');
		$admin_email 		= get_option('admin_email');
		$register_page_url 	= $transfers_theme_globals->get_register_page_url();
		$php_version 		= phpversion();
		
		$user_activation_key = get_user_meta($user_id, 'user_activation_key', true);
		if (empty($user_activation_key))
			return false;
		
		$activation_url = esc_url_raw (
			add_query_arg (
				array (
					'action' 			=> 'activate',
					'user_id' 			=> $user->ID,
					'activation_key' 	=> $user_activation_key
				),
				$register_page_url
			)
		);
		
		$subject 	= sprintf(__("%s - User Activation", "transfers"), $site_name);
		$body 		= sprintf(__("To activate your user account, please click the activation link below: \n\n%s", "transfers"), $activation_url);
		
		$headers = "Content-Type: text/html\r\n";
		if (!empty($admin_email)) {
			$headers .= "Reply-To: " . $site_name . " <" . $admin_email . ">\r\n";
		}			
		
		$ret = wp_mail($user->user_email, $subject, $body, $headers);
		
		if (!$ret) {
			global $phpmailer;
			if (isset($phpmailer) && WP_DEBUG) {
				var_dump($phpmailer->ErrorInfo);
			}
		}
		
		return $ret;
	}
	
	public static function activate_user($user_id, $activation_key){
	
		$user = get_userdata($user_id);
		if (!$user) 
			return false;
			
		$user_activation_key = get_user_meta($user_id, 'user_activation_key', true);

		if ($user && !empty($user_activation_key) && $user_activation_key === $activation_key) {
		
			$userdata = array('ID' => $user->ID);		
			$userdata['role'] = get_option('default_role');
			wp_update_user($userdata);
			
			delete_user_meta($user_id, 'user_activation_key');
			
			Transfers_Theme_Utils::activation_success_notification($user_id);
			
			return true;
		}
		
		return false;
	}
	
	public static function activation_success_notification($user_id){

		global $transfers_theme_globals;
		
		$user = get_userdata($user_id);
		if (!$user) 
			return false;
		
		$redirect_to_after_login_url 	= $transfers_theme_globals->get_redirect_to_after_login_page_url();
		$login_url 						= $transfers_theme_globals->get_login_page_url();
		$let_users_set_pass 			= $transfers_theme_globals->let_users_set_pass();
		$site_name 						= get_bloginfo('name');
		$admin_email 					= get_option('admin_email');
		$php_version 					= phpversion();
		$user_email 					= $user->user_email;
		$user_login 					= $user->user_login;
		
		$subject = sprintf(esc_html__('%s - User Activation Success ', 'transfers'), $site_name);

		if ($let_users_set_pass) {
			$body = esc_html__('Thank you for activating your account! You may now log in using the credentials you supplied when you created your account.', 'transfers');
		} else {
			$new_password = get_user_meta($user_id, 'user_pass', true);
			$body = sprintf(esc_html__('Thank you for activating your account. You may now log in using the following credentials:\n\nUsername: %s\n\nPassword: %s\n\nLogin url: %s', 'transfers'), $user_login, $new_password, $login_url);
		}
		
		// Delete plaintext pass
		delete_user_meta($user_id, 'user_pass');

		$headers = "Content-Type: text/plain\r\n";
		$headers .= "From: " . $site_name . " <" . $admin_email . ">\r\n";
		
		$ret = wp_mail($user_email, $subject, $body, $headers);
		
		if (!$ret) {
			global $phpmailer;
			if (isset($phpmailer) && WP_DEBUG) {
				var_dump($phpmailer->ErrorInfo);
			}
		}		

		return $ret;
	}
	
	public static function resetpassword( $user_id, $resetpassword_key ){
		
		$user = get_userdata($user_id);
		if (!$user) 
			return false;

		if ($user->user_resetpassword_key && $user->user_resetpassword_key === $resetpassword_key) {

			if(!$user->user_resetpassword_datetime || strtotime( $user->user_resetpassword_datetime ) < time() - ( 24 * 60 * 60 )) 
				return false;

			$userdata = array(
				'ID' => $user->ID,
				'user_pass' => wp_generate_password( 8, false )
			);

			wp_update_user( $userdata );
			delete_user_meta( $user->ID, 'user_resetpassword_key' );
			
			return $userdata['user_pass'];
		}

		return false;
	}
	
	public static function resetpassword_notification( $user_id ){

		global $transfers_theme_globals;
		
		$user = get_userdata( $user_id );
		
		if (!$user || !$user->user_resetpassword_key) 
			return false;

		$reset_password_page_url 		= $transfers_theme_globals->get_reset_password_page_url();
		
		$site_name 						= get_bloginfo('name');
		$admin_email 					= get_option('admin_email');
		$php_version 					= phpversion();
		$user_email 					= $user->user_email;
		$user_login 					= $user->user_login;
			
		$resetpassword_url = esc_url_raw ( add_query_arg( 
			array( 
				'action' => 'resetpassword',
				'user_id' => $user->ID,
				'resetpassword_key' => $user->user_resetpassword_key
			), 
			$reset_password_page_url
		) );

		$subject 	= sprintf(esc_html__( '%s - Reset Password ', 'transfers' ), $site_name);
		$body 		= sprintf(esc_html__("To reset your password please go to the following url: \n\n%s\n\n\n\nThis link will remain valid for the next 24 hours.\n\nIn case you did not request a password reset, please ignore this email.", "transfers" ), $resetpassword_url);

		$headers = "Content-Type: text/plain\r\n";
		$headers .= "From: " . $site_name . " <" . $admin_email . ">\r\n";
		
		$ret = wp_mail($user_email, $subject, $body, $headers);
		
		if (!$ret) {
			global $phpmailer;
			if (isset($phpmailer) && WP_DEBUG) {
				var_dump($phpmailer->ErrorInfo);
			}
		}

		return $ret;		
	}
	
	public static function newpassword_notification( $user_id, $new_password ){

		global $transfers_theme_globals;
	
		$user = get_userdata( $user_id );
		if( !$user || !$new_password ) 
			return false;
			
		$reset_password_page_url 		= $transfers_theme_globals->get_reset_password_page_url();
		
		$site_name 						= get_bloginfo('name');
		$admin_email 					= get_option('admin_email');
		$php_version 					= phpversion();
		$user_email 					= $user->user_email;
		$user_login 					= $user->user_login;

		$subject 	= sprintf(esc_html__("%s - New Password ", "transfers"), $site_name);
		$body 		= sprintf(esc_html__("Your password was successfully reset. \n\n\n\nYour new password is: %s", "transfers"), $new_password);

		$headers = "Content-Type: text/plain\r\n";
		$headers .= "From: " . $site_name . " <" . $admin_email . ">\r\n";
		
		$ret = wp_mail($user_email, $subject, $body, $headers);
		
		if (!$ret) {
			global $phpmailer;
			if (isset($phpmailer) && WP_DEBUG) {
				var_dump($phpmailer->ErrorInfo);
			}
		}

		return $ret;		
	}
	
	public static function comment_end($comment, $args, $depth) {
		echo "</li>";
	}
	
	public static function comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; 
		$comment_class = comment_class('clearfix', null, null, false);

		?>							
		<!--single comment-->
		<li <?php echo wp_kses($comment_class, array('class' => array())); ?> id="comment-<?php comment_ID() ?>">
			<div class="avatar">
				<a href="<?php echo esc_url(get_comment_author_link($comment_ID)); ?>"><?php echo get_avatar( $comment->comment_author_email, 100 ); ?></a>
			</div>
			<div class="comment-box">
				<div class="comment-author meta"> 
					<?php echo wp_kses(sprintf(__('<strong>%s</strong> <span class="says">said on</span> ', 'transfers'), get_comment_author_link()), array('strong' => array(), 'span' => array('class' => array()),)) ?>
					<?php comment_time('F j, Y'); ?>
					<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
					<div class="comment-meta commentmetadata"><?php edit_comment_link(__('(Edit)', 'transfers'),'  ','') ?></div>
				</div>
				<div class="comment-text">
					<?php
					$comment_text = get_comment_text();
					$allowed_tags = transfers_get_allowed_content_tags_array();
					echo wp_kses($comment_text, $allowed_tags); 
					?>
					<?php if ($comment->comment_approved == '0') : ?>
					<em><?php esc_html_e('Your comment is awaiting moderation.', 'transfers') ?></em>
					<?php endif; ?>
				</div>
			</div> 
		<!--//single comment-->
	<?php
	}	

	public static function get_allowed_breadcrumbs_tags_array() {
			
		global $allowedtags;
		
		$allowedtags = array(
			'a' => array(
				'class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array()
			),
			'nav' => array(
				'role' => array(),
				'class' => array(),			
				'id' => array(),
			),
			'ul' => array(
				'class' => array(),			
				'id' => array(),
			),
			'li' => array(
				'class' => array(),	
			),			
			'em' => array(),
			'strong' => array(),
		);	
	
		return apply_filters( 'transfers_allowed_breadcrumbs_tags', $allowedtags );
	}	
	
	public static function get_page_custom_field_string_value($page_id, $custom_field, $default_value) {
	
		$page_custom_fields = get_post_custom( $page_id);

		$custom_field_value = $default_value;
		if (isset($page_custom_fields[$custom_field])) {
			$custom_field_value = $page_custom_fields[$custom_field][0];
			$custom_field_value = empty($custom_field_value) ? '' : $custom_field_value;
		}
	
		return $custom_field_value;
	}
	
	public static function get_page_custom_field_bool_value($page_id, $custom_field, $default_value) {
	
		$page_custom_fields = get_post_custom( $page_id);

		$custom_field_value = $default_value;
		if (isset($page_custom_fields[$custom_field])) {
			$custom_field_value = $page_custom_fields[$custom_field][0];
			$custom_field_value = empty($custom_field_value) ? false : (bool)$custom_field_value;
		}
	
		return $custom_field_value;
	}
	
	public static function display_pager($max_num_pages, $custom_transfers_paged = false) {

		$pattern = '#(www\.|https?:\/\/){1}[a-zA-Z0-9\-]{2,254}\.[a-zA-Z0-9]{2,20}[a-zA-Z0-9.?&=_/]*#i';

		$big = 999999999; // need an unlikely integer
		
		$pager_settings = array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'total' => $max_num_pages,
			'prev_text'    => esc_html__('&lt;', 'transfers'),
			'next_text'    => esc_html__('&gt;', 'transfers'),
			'type'		   => 'array'
		);
		
		if ($custom_transfers_paged) {
			$pager_settings['format'] = '?paged-tf=%#%';
			$pager_settings['current'] = max( 1, get_query_var('paged-tf') );
		} else {
			$pager_settings['format'] = '?paged=%#%';
			$pager_settings['current'] = max( 1, get_query_var('paged') );
		}
		
		$pager_links = paginate_links( $pager_settings );
		
		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		$count_links = 0;
		if (is_array($pager_links)) {
			$count_links = count($pager_links);
		}
		if ($count_links > 0) {		
			$first_link = $pager_links[0];
			$last_link = $first_link;
			preg_match_all($pattern, $first_link, $matches, PREG_PATTERN_ORDER);
			echo '<span class="first"><a href="' . get_pagenum_link(1) . '">&laquo;</a></span>';
			for ($i=0; $i<$count_links; $i++) {
				$pager_link = $pager_links[$i];
				if (!transfers_string_contains($pager_link, 'current'))
					echo '<span>' . $pager_link . '</span>';
				else {
					$pager_link = str_replace('current', 'current color', $pager_link);
					echo wp_kses($pager_link, $allowedtags);
				}
				$last_link = $pager_link;
			}
			preg_match_all($pattern, $last_link, $matches, PREG_PATTERN_ORDER);
			echo '<span class="last"><a href="' . get_pagenum_link($max_num_pages) . '">&raquo;</a></span>';
		}
	}
		
	public static function is_a_woocommerce_page () {
		if(  function_exists ( "is_woocommerce" ) && is_woocommerce()){
				return true;
		}
		$woocommerce_keys   =   array ( "woocommerce_shop_page_id" ,
										"woocommerce_terms_page_id" ,
										"woocommerce_cart_page_id" ,
										"woocommerce_checkout_page_id" ,
										"woocommerce_pay_page_id" ,
										"woocommerce_thanks_page_id" ,
										"woocommerce_myaccount_page_id" ,
										"woocommerce_edit_address_page_id" ,
										"woocommerce_view_order_page_id" ,
										"woocommerce_change_password_page_id" ,
										"woocommerce_logout_page_id" ,
										"woocommerce_lost_password_page_id" ) ;
		foreach ( $woocommerce_keys as $wc_page_id ) {
				if ( get_the_ID () == get_option ( $wc_page_id , 0 ) ) {
						return true ;
				}
		}
		return false;
	}	
}

if (!class_exists('Transfers_BaseSingleton')) { 
	//
	// http://scotty-t.com/2012/07/09/wp-you-oop/
	//
	abstract class Transfers_BaseSingleton {
		private static $instance = array();
		protected function __construct() {}
		
		public static function get_instance() {
			$c = get_called_class();
			if (!isset(self::$instance[$c])) {
				self::$instance[$c] = new $c();
				self::$instance[$c]->init();
			}

			return self::$instance[$c];
		}

		abstract public function init();
	}
}


function transfers_comment($comment, $args, $depth) {
	Transfers_Theme_Utils::comment($comment, $args, $depth);
}

function transfers_comment_end($comment, $args, $depth) {
	Transfers_Theme_Utils::comment_end($comment, $args, $depth);
}