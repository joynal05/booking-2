<?php

class Transfers_Theme_WooCommerce extends Transfers_BaseSingleton {

	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {
	
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		add_action('woocommerce_before_main_content', array($this, 'woocommerce_before_main_content'), 30);
		add_action('woocommerce_after_main_content', array($this, 'woocommerce_after_main_content'), 30);		
		
		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
		add_action('woocommerce_before_main_content', array($this, 'transfers_woocommerce_breadcrumb'), 10, 0);		
		
		add_filter('loop_shop_columns', array($this, 'loop_shop_columns' ));
		add_filter('post_class', array($this, 'post_class'));

	}	
	
	function post_class($classes) {
	
		if (in_array('product', $classes) && !is_single()) {

			global $transfers_theme_globals;

			$page_sidebar_positioning = $transfers_theme_globals->get_woocommerce_pages_sidebar_position();
			$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
		
			if ($page_sidebar_positioning == 'both')
				$classes[] = 'one-half';
			else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
				$classes[] = 'one-third';
			else
				$classes[] = 'one-fourth';			
		}
	
		return $classes;
	}
	
	function loop_shop_columns() {
	
		global $transfers_theme_globals;

		$page_sidebar_positioning = $transfers_theme_globals->get_woocommerce_pages_sidebar_position();
		$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
	
		if ($page_sidebar_positioning == 'both')
			return 2;
		else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
			return 3;
		return 4; // 4 products per row
	}
	
	function woocommerce_before_main_content() {
	
		global $transfers_theme_globals;

		$page_sidebar_positioning = $transfers_theme_globals->get_woocommerce_pages_sidebar_position();
		$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;

		$section_class = 'full-width';
		if ($page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
			$section_class = 'three-fourth';
			
		?>
		<!--row-->
		<div class="wrap">
			<div class="row">
		<?php			
		if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left') {
			get_sidebar('left');
		}
		?>		
				<section class="content <?php echo esc_attr($section_class); ?>">
		<?php
	}
	
	function woocommerce_after_main_content() {
	
		global $transfers_theme_globals;

		$page_sidebar_positioning = $transfers_theme_globals->get_woocommerce_pages_sidebar_position();
		$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;

		$section_class = 'full-width';
		if ($page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
			$section_class = 'three-fourth';
			
		?>
				</section>
		<?php			
		if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right') {
			get_sidebar('right');	
		} ?>
			</div><!--row-->
		</div><!--wrap-->
		<?php
	}	
	
	function transfers_woocommerce_breadcrumb() {
		$breadcrumb_args = array(
				'delimiter' => '',
				'before' => '<li>',
				'after' => '</li>',
				'wrap_before' => '<header class="site-title color"><div class="wrap"><div class="container"><nav role="navigation" class="breadcrumbs" itemprop="breadcrumb"><ul>',
				'wrap_after' => '</ul></nav></div></div></header>',
		);
		woocommerce_breadcrumb($breadcrumb_args);
	}
	
}

// store the instance in a variable to be retrieved later and call init
$transfers_theme_woocommerce = Transfers_Theme_WooCommerce::get_instance();
$transfers_theme_woocommerce->init();
