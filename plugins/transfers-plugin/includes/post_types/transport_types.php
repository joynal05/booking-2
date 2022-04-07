<?php


class Transfers_Transport_Types_Post_Type extends Transfers_BaseSingleton {

	private $enable_transport_types;
	private $transport_type_custom_meta_fields;
	private $transport_type_list_custom_meta_fields;
	private $transport_type_list_meta_box;	

	protected function __construct() {
	
		global $post, $transfers_plugin_globals, $transfers_extra_items_post_type;
		
		$this->enable_transport_types = $transfers_plugin_globals->enable_transport_types();	

		if ($this->enable_transport_types) {
			
			$this->transport_type_custom_meta_fields = array(
				array( // Post ID select box
					'label'	=> esc_html__('Max people per vehicle?', 'transfers'), // <label>
					'desc'	=> esc_html__('How many people are allowed in the vehicle?', 'transfers'), // description
					'id'	=> '_transport_type_max_people_per_vehicle', // field id and name
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '50',
					'step'	=> '1'
				)
			);
		}
		
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();	
	}
	
    public function init() {

		if ($this->enable_transport_types) {	
		
			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter( 'manage_edit-transport_type_columns', array( $this, 'manage_edit_transport_type_columns'), 10, 1);	
			add_action( 'transfers_plugin_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
			add_action( 'admin_init', array( $this, 'transport_type_admin_init' ) );
		}
	}
		
	function remove_unnecessary_meta_boxes() {

	}
	
	function manage_edit_transport_type_columns($columns) {
	
		return $columns;
	}
	
	function transport_type_admin_init() {
		new Transfers_Add_Meta_Box( 'transport_type_custom_meta_fields', esc_html__('Extra information', 'transfers'), $this->transport_type_custom_meta_fields, 'transport_type' );
	}
	
	function transport_type_list_add_meta_boxes() {

	}
			
	function initialize_post_type() {
	
		$this->register_transport_type_post_type();
	}
		
	function register_transport_type_post_type() {
			
		global $transfers_plugin_globals;
		
		$labels = array(
			'name'                => esc_html__( 'Transport types', 'transfers' ),
			'singular_name'       => esc_html__( 'Transport type', 'Post Type Singular Name', 'transfers' ),
			'menu_name'           => esc_html__( 'Transport types', 'transfers' ),
			'all_items'           => esc_html__( 'All Transport types', 'transfers' ),
			'view_item'           => esc_html__( 'View Transport type', 'transfers' ),
			'add_new_item'        => esc_html__( 'Add New Transport type', 'transfers' ),
			'add_new'             => esc_html__( 'New Transport type', 'transfers' ),
			'edit_item'           => esc_html__( 'Edit Transport type', 'transfers' ),
			'update_item'         => esc_html__( 'Update Transport type', 'transfers' ),
			'search_items'        => esc_html__( 'Search Transport types', 'transfers' ),
			'not_found'           => esc_html__( 'No Transport types found', 'transfers' ),
			'not_found_in_trash'  => esc_html__( 'No Transport types found in Trash', 'transfers' ),
		);
		$args = array(
			'label'               => esc_html__( 'Transport type', 'transfers' ),
			'description'         => esc_html__( 'Transport type information pages', 'transfers' ),
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
			'rewrite' 			  => array('slug' => 'transport'),
		);
		
		register_post_type( 'transport_type', $args );	
	}
	
	function list_transport_types($paged = 0, $per_page = -1, $orderby = '', $order = '', $author_id = null, $include_private = false, $count_only = false ) {
	
		$args = array(
			'post_type'         => 'transport_type',
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

global $transfers_transport_types_post_type;
// store the instance in a variable to be retrieved later and call init
$transfers_transport_types_post_type = Transfers_Transport_Types_Post_Type::get_instance();
$transfers_transport_types_post_type->init();