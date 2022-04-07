<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */
 global $transfers_theme_globals, $current_user ;
 ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php echo esc_attr(get_bloginfo( 'charset' )); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' )); ?>" />
	<?php 
	if ( ! function_exists( 'get_site_icon_url' ) ) {		
		$favicon_src = $transfers_theme_globals->get_theme_favicon_src(); 
		if (!empty($favicon_src)) {?>
		<link rel="shortcut icon" href="<?php echo esc_url($favicon_src); ?>" />	
		<?php } 
	}
	?>
	<script type="text/javascript">
		window.themePath = '<?php echo esc_js( get_template_directory_uri() ); ?>';
		window.siteUrl = '<?php echo esc_js( $transfers_theme_globals->get_site_url() ); ?>';
<?php 	if ($current_user->ID > 0){	?>
		window.currentUserId = '<?php echo esc_js($current_user->ID);?>';
		window.currentUserLogin = '<?php echo esc_js($current_user->user_login);?>';
<?php 	} else { ?>	
		window.currentUserId = 0;
		window.currentUserLogin = null;
<?php 	} ?>
		window.currentLanguage = '<?php echo transfers_get_current_language_code(); ?>';
		<?php 		if (function_exists('is_transfers_plugin_active') && is_transfers_plugin_active()) { ?>		
		window.datepickerDateFormat = "<?php echo Transfers_Plugin_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format')) ?>";
		<?php } ?>
		window.datepickerAltFormat = "<?php echo TRANSFERS_DATEPICKER_ALT_DATE_FORMAT ?>";
		window.timezoneOffset = <?php echo get_option('gmt_offset'); ?>;
	</script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo esc_url ( transfers_get_file_uri('/js/html5shiv.min.js') ); ?>"></script>
    <script src="<?php echo esc_url ( transfers_get_file_uri('/js/respond.min.js') ); ?>"></script>
    <![endif]-->
<?php
	
	global $main_element_class;

	wp_head(); 
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-122483202-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-122483202-1');
</script>
</head>  
<body <?php body_class(); ?>>
	<?php if ($transfers_theme_globals->show_preloader()) { ?>
	<!-- Preloader -->
	<div class="preloader">
		<div id="followingBallsG">
			<div id="followingBallsG_1" class="followingBallsG"></div>
			<div id="followingBallsG_2" class="followingBallsG"></div>
			<div id="followingBallsG_3" class="followingBallsG"></div>
			<div id="followingBallsG_4" class="followingBallsG"></div>
		</div>
	</div>
	<!-- //Preloader -->
	<?php } ?>
    <!-- Header -->
	<header class="header">
		<div class="wrap">
			<?php $logo_title = get_bloginfo('name') . ' | ' . ( is_home() || is_front_page() ? get_bloginfo('description') : wp_title('', false)); ?>
			<!-- Logo -->
			<div class="logo">
				<a href="<?php echo esc_url( home_url('/') ); ?>" title="<?php echo esc_attr($logo_title); ?>"><img src="<?php echo esc_url( $transfers_theme_globals->get_theme_logo_src() ); ?>" alt="<?php echo esc_attr($logo_title); ?>" /></a>
			</div>
			<!-- //Logo -->
			<!--primary navigation-->
			<?php  if ( has_nav_menu( 'primary-menu' ) ) {
				wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => 'nav', 'container_class' => 'main-nav', 'container_id' => 'nav', 'menu_class' => '') );
			} else { ?>
			<nav class="main-nav">
				<ul>
					<li class="menu-item"><a href="<?php echo esc_url ( home_url('/') ); ?>"><?php esc_html_e('Home', "transfers"); ?></a></li>
					<li class="menu-item"><a href="<?php echo esc_url ( admin_url('nav-menus.php') ); ?>"><?php esc_html_e('Configure', "transfers"); ?></a></li>
				</ul>
			</nav>
			<?php } ?>
			<!--//primary navigation-->
		</div>
	</header>
	<!-- //Header -->
	<!-- Main -->
	<main class="main <?php echo esc_attr($main_element_class); ?>">