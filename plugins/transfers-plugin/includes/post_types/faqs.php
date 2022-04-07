<?php

class Transfers_Faqs_Post_Type extends Transfers_BaseSingleton {

	private $enable_faqs;

	protected function __construct() {
	
		global $transfers_plugin_globals;
		
		$this->enable_faqs = $transfers_plugin_globals->enable_faqs();
	
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();	
	}
	
    public function init() {
	
		if ($this->enable_faqs) {

			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter('manage_edit-faq_columns', array( $this, 'manage_edit_faq_columns'), 10, 1);	
			add_action( 'transfers_plugin_initialize_post_types', array( $this, 'initialize_post_type' ), 0);		
		}		
	
	}
	
	function remove_unnecessary_meta_boxes() {

	}	
	
	function manage_edit_faq_columns($columns) {
	
		//unset($columns['taxonomy-faq_type']);
		return $columns;
	}
	
	function initialize_post_type() {
	
		$this->register_faq_category_taxonomy();
		$this->register_faq_post_type();
	}
	
	function register_faq_category_taxonomy() {
	
		global $transfers_plugin_globals;
		
		$labels = array(
				'name'              			=> esc_html__( 'Categories', 'transfers' ),
				'singular_name'     			=> esc_html__( 'Category', 'transfers' ),
				'search_items'      			=> esc_html__( 'Search categories', 'transfers' ),
				'all_items'         			=> esc_html__( 'All categories', 'transfers' ),
				'parent_item'                	=> null,
				'parent_item_colon'          	=> null,
				'edit_item'         			=> esc_html__( 'Edit Category', 'transfers' ),
				'update_item'       			=> esc_html__( 'Update Category', 'transfers' ),
				'add_new_item'      			=> esc_html__( 'Add New Category', 'transfers' ),
				'new_item_name'     			=> esc_html__( 'New Category Name', 'transfers' ),
				'separate_items_with_commas'	=> esc_html__( 'Separate categories with commas', 'transfers' ),
				'add_or_remove_items'       	=> esc_html__( 'Add or remove categories', 'transfers' ),
				'choose_from_most_used'      	=> esc_html__( 'Choose from the most used categories', 'transfers' ),
				'not_found'                  	=> esc_html__( 'No categories found.', 'transfers' ),
				'menu_name'        				=> esc_html__( 'Categories', 'transfers' ),
			);
			
		$args = array(
				'hierarchical'      			=> true,
				'labels'            			=> $labels,
				'show_ui'           			=> true,
				'show_admin_column' 			=> true,
				'query_var'         			=> false,
				'update_count_callback' 		=> '_update_post_term_count',
				'rewrite'           			=> false,
			);
		
		register_taxonomy( 'faq_category', array( 'faq' ), $args );
	}
	
	function register_faq_post_type() {
		
		global $transfers_plugin_globals;
		
		$labels = array(
			'name'                => esc_html__( 'Faqs', 'transfers' ),
			'singular_name'       => esc_html__( 'Faq', 'transfers' ),
			'menu_name'           => esc_html__( 'Faqs', 'transfers' ),
			'all_items'           => esc_html__( 'All Faqs', 'transfers' ),
			'view_item'           => esc_html__( 'View Faq', 'transfers' ),
			'add_new_item'        => esc_html__( 'Add New Faq', 'transfers' ),
			'add_new'             => esc_html__( 'New Faq', 'transfers' ),
			'edit_item'           => esc_html__( 'Edit Faq', 'transfers' ),
			'update_item'         => esc_html__( 'Update Faq', 'transfers' ),
			'search_items'        => esc_html__( 'Search Faqs', 'transfers' ),
			'not_found'           => esc_html__( 'No Faqs found', 'transfers' ),
			'not_found_in_trash'  => esc_html__( 'No Faqs found in Trash', 'transfers' ),
		);
		$args = array(
			'label'               => esc_html__( 'faq', 'transfers' ),
			'description'         => esc_html__( 'Faq information pages', 'transfers' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'rewrite' 			  => false,
		);
		
		register_post_type( 'faq', $args );	
	}
	
	function list_faqs($paged = 0, $per_page = -1, $orderby = '', $order = '', $category_id = 0, $author_id = null, $include_private = false, $count_only = false ) {
	
		$args = array(
			'post_type'         => 'faq',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged, 
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);
		
		if ($include_private) {
			$args['post_status'][] = 'private';
		}
		
		if (isset($author_id)) {
			$author_id = intval($author_id);
			if ($author_id > 0) {
				$args['author'] = $author_id;
			}
		}
		
		$tax_query = array();
		if ($category_id > 0) {
			$tax_query[] = array(
					'taxonomy' => 'faq_category',
					'field' => 'term_id',
					'terms' => array($category_id),
					'operator'=> 'IN'
			);
		}
		
		if (count($tax_query) > 0) {
			$args['tax_query'] = $tax_query;
		}
	
		$posts_query = new WP_Query($args);
		
		if ($count_only) {
			$results = array(
				'total' => $posts_query->found_posts,
				'results' => null
			);	
		} else {
			$results = array();
			
			if ($posts_query->have_posts() ) {
				while ( $posts_query->have_posts() ) {
					global $post;
					$posts_query->the_post(); 
					$results[] = $post;
				}
			}
		
			$results = array(
				'total' => $posts_query->found_posts,
				'results' => $results
			);
		}
		
		wp_reset_postdata();
		
		return $results;
	}
	
}

global $transfers_faqs_post_type;
// store the instance in a variable to be retrieved later and call init
$transfers_faqs_post_type = Transfers_Faqs_Post_Type::get_instance();
$transfers_faqs_post_type->init();