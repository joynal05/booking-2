<?php

require_once transfers_get_file_path('/includes/post_types/posts.php');

class Transfers_Theme_Post_Types extends Transfers_BaseSingleton {

	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();
		
    }
	
    public function init() {
		add_action( 'init', array($this, 'initialize_post_types' ) );
    }
		
	function initialize_post_types() {
	
		do_action('transfers_initialize_post_types');
	}
}

global $transfers_theme_post_types;
// store the instance in a variable to be retrieved later and call init
$transfers_theme_post_types = Transfers_Theme_Post_Types::get_instance();
$transfers_theme_post_types->init();