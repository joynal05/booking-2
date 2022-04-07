<?php

class Transfers_Theme_Filters extends Transfers_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();
		
    }
	
    public function init() {
		add_filter( 'wp_title', array($this, 'custom_wp_title'), 10, 2 );
		add_filter( 'safe_style_css', array($this, 'make_display_a_safe_style'), 10, 1 );
		add_filter( 'comment_reply_link', array($this, 'comment_reply_link'), 10, 4);
	}
	
	function comment_reply_link($link, $args, $comment, $post) {
		return str_replace("class='comment-reply-link'", "class='btn small color right'", $link);
	}
	
	function make_display_a_safe_style($styles) {
		$styles[] = 'display';
		return $styles;
	}
	
	function custom_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name
		$blog_name = get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$blog_name .= " $sep $site_description";
		}
		
		$title = $blog_name . " " . $title;

		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'transfers' ), max( $paged, $page ) );
		}

		return $title;
	}


}

// store the instance in a variable to be retrieved later and call init
$transfers_theme_filters = Transfers_Theme_Filters::get_instance();
$transfers_theme_filters->init();