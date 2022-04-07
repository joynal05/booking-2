<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */
global $transfers_theme_globals;
?>
	</main>
	<?php get_sidebar('above-footer'); ?>
	<!-- //Main -->
	<!-- Footer -->
	<footer class="footer black">
		<div class="wrap">
			<?php get_sidebar('footer'); ?>
			<div class="copy">
				<p><?php echo esc_html($transfers_theme_globals->get_copyright_footer()); ?></p>
				<!--footer navigation-->				
				<?php if ( has_nav_menu( 'footer-menu' ) ) {
					wp_nav_menu( array( 
						'theme_location' => 'footer-menu', 
						'container' => 'nav', 
						'container_class' => 'foot-nav',
						'menu_class' => ''
					) ); 
				} else { ?>
				<nav class="foot-nav">
					<ul>
						<li class="menu-item"><a href="<?php echo esc_url( home_url('/')); ?>"><?php esc_html_e('Home', "transfers"); ?></a></li>
						<li class="menu-item"><a href="<?php echo esc_url( admin_url('nav-menus.php') ); ?>"><?php esc_html_e('Configure', "transfers"); ?></a></li>
					</ul>
				</nav>
				<?php } ?>
				<!--//footer navigation-->
			</div>
		</div>
	</footer>
	<!-- //Footer -->
	
	<?php wp_footer(); ?>
</body>
</html>