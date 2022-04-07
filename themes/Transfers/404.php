<?php
/**
/* 404 page template
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */
global $main_element_class;
$main_element_class = 'error';
 
get_header();  
get_sidebar('under-header');

global $transfers_theme_globals;

$allowed = array(
    'a' => array(
        'href' => array(),
        'title' => array()
    ),
    'br' => array(),
    'em' => array(),
    'strong' => array(),
);
?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php esc_html_e('Error 404', 'transfers') ?></h1>
				<?php 
				$allowed_tags = Transfers_Theme_Utils::get_allowed_breadcrumbs_tags_array();
				$breadcrumbs_html = $transfers_theme_globals->get_breadcrumbs();
				echo wp_kses($breadcrumbs_html, $allowed_tags); 
				?>
			</div>
		</div>
	</header>
	<!-- //Page info -->		
	<div class="wrap">
		<div class="row">
			<!--- Content -->
			<div class="content one-half right textongreyRight">
				<h2><?php esc_html_e('404 Page not found', 'transfers') ?></h2>
				<p><?php esc_html_e('The page you have requested could not be found or was removed from our database.', 'transfers') ?></p>
				<p><?php echo sprintf(wp_kses(__('If you believe that this is an error, please kindly <a href="%s">contact us</a>. Thank you!', 'transfers'), $allowed), esc_url($transfers_theme_globals->get_contact_page_url())) ?></p>
				<p><?php echo sprintf(wp_kses(__('You can go <a href="%s">back home</a> or try using the search.', 'transfers'), $allowed), esc_url( home_url('/')) ) ?></p>
			</div>
			<!--- //Content -->
		</div>
	</div>
<?php 
get_footer();