<?php





class Transfers_Destinations_Post_Type extends Transfers_BaseSingleton {



	private $enable_destinations;

	private $destination_custom_meta_fields;

	private $destination_list_custom_meta_fields;

	private $destination_list_meta_box;	



	protected function __construct() {

	

		global $post, $transfers_plugin_globals;

		

		$this->enable_destinations = $transfers_plugin_globals->enable_destinations();



		if ($this->enable_destinations) {

		

			$this->destination_custom_meta_fields = array(

			);

			

			global $default_destination_extra_fields;

			$destination_extra_fields = $transfers_plugin_globals->get_destination_extra_fields();

			if (!is_array($destination_extra_fields) || count($destination_extra_fields) == 0) {

				$destination_extra_fields = $default_destination_extra_fields;

			}



			foreach ($destination_extra_fields as $destination_extra_field) {

				$field_is_hidden = isset($destination_extra_field['hide']) ? intval($destination_extra_field['hide']) : 0;

				

				if (!$field_is_hidden) {

					$extra_field = null;

					$field_label = isset($destination_extra_field['label']) ? $destination_extra_field['label'] : '';

					$field_id = isset($destination_extra_field['id']) ? $destination_extra_field['id'] : '';

					$field_type = isset($destination_extra_field['type']) ? $destination_extra_field['type'] :  '';

					if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {

						$extra_field = array(

							'label'	=> $field_label,

							'desc'	=> '',

							'id'	=> '_destination_' . $field_id,

							'type'	=> $field_type

						);

					}



					if ($extra_field) 

						$this->destination_custom_meta_fields[] = $extra_field;

				}

			}

		}

		

        // our parent class might

        // contain shared code in its constructor

        parent::__construct();	

	}

	

    public function init() {



		if ($this->enable_destinations) {

		

			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );

			add_filter( 'manage_edit-destination_columns', array( $this, 'manage_edit_destination_columns'), 10, 1);	

			add_action( 'transfers_plugin_initialize_post_types', array( $this, 'initialize_post_type' ), 0);

			add_action( 'admin_init', array( $this, 'destination_admin_init' ) );

		}

	}

		

	function remove_unnecessary_meta_boxes() {



	}

	

	function manage_edit_destination_columns($columns) {

	

		return $columns;

	}

	

	function destination_admin_init() {

		new Transfers_Add_Meta_Box( 'destination_custom_meta_fields', esc_html__('Extra information', 'transfers'), $this->destination_custom_meta_fields, 'destination' );

		



		if ($this->enable_destinations) {



			$sort_by_columns = array();

			$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Destination title', 'transfers'));

			$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Destination ID', 'transfers'));

			$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'transfers'));

			$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'transfers'));



			$this->destination_list_custom_meta_fields = array(

				array( // Select box

					'label'	=> esc_html__('Sort by field', 'transfers'), // <label>

					// the description is created in the callback function with a link to Manage the taxonomy terms

					'id'	=> 'list_sort_by', // field id and name, needs to be the exact name of the taxonomy

					'type'	=> 'select', // type of field

					'options' => $sort_by_columns

				),

				array( // Post ID select box

					'label'	=> esc_html__('Sort descending?', 'transfers'), // <label>

					'desc'	=> esc_html__('If checked, will sort destinations in descending order', 'transfers'), // description

					'id'	=> 'list_sort_descending', // field id and name

					'type'	=> 'checkbox', // type of field

				),				



			);

		}

		

		$this->destination_list_meta_box = new Transfers_Add_Meta_Box( 'destination_list_custom_meta_fields', esc_html__('Extra information', 'transfers'), $this->destination_list_custom_meta_fields, 'page' );	

		remove_action( 'add_meta_boxes', array( $this->destination_list_meta_box, 'add_box' ) );

		add_action('add_meta_boxes', array( $this, 'destination_list_add_meta_boxes' ) );	

	}

	

	function destination_list_add_meta_boxes() {



		global $post;

		$template_file = get_post_meta($post->ID,'_wp_page_template',true);

		if ($template_file == 'page-destination-list.php') {

			add_meta_box( $this->destination_list_meta_box->id, $this->destination_list_meta_box->title, array( $this->destination_list_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );

		}	

	}

			

	function initialize_post_type() {

	

		$this->register_destination_post_type();

	}

		

	function register_destination_post_type() {

			

		global $transfers_plugin_globals;

		$destinations_permalink_slug = $transfers_plugin_globals->get_destinations_permalink_slug();

		

		$destination_list_page_id = $transfers_plugin_globals->get_destination_list_page_id();

		

		if ($destination_list_page_id > 0) {



			add_rewrite_rule(

				"{$destinations_permalink_slug}$",

				"index.php?post_type=page&page_id={$destination_list_page_id}", 'top');

		

			add_rewrite_rule(

				"{$destinations_permalink_slug}/page/?([1-9][0-9]*)",

				"index.php?post_type=page&page_id={$destination_list_page_id}&paged=\$matches[1]", 'top');

		

		}

		

		add_rewrite_rule(

			"{$destinations_permalink_slug}/([^/]+)/page/?([1-9][0-9]*)",

			"index.php?post_type=destination&name=\$matches[1]&paged-tf=\$matches[2]", 'top');

			

		add_rewrite_tag('%paged-tf%', '([1-9][0-9]*)');		

		

		$labels = array(

			'name'                => esc_html__( 'Transfers', 'transfers' ),

			'singular_name'       => esc_html__( 'Transfer', 'transfers' ),

			'menu_name'           => esc_html__( 'Destinations', 'transfers' ),

			'all_items'           => esc_html__( 'All Destinations', 'transfers' ),

			'view_item'           => esc_html__( 'View Destination', 'transfers' ),

			'add_new_item'        => esc_html__( 'Add New Destination', 'transfers' ),

			'add_new'             => esc_html__( 'New Destination', 'transfers' ),

			'edit_item'           => esc_html__( 'Edit Destination', 'transfers' ),

			'update_item'         => esc_html__( 'Update Destination', 'transfers' ),

			'search_items'        => esc_html__( 'Search Destinations', 'transfers' ),

			'not_found'           => esc_html__( 'No Destinations found', 'transfers' ),

			'not_found_in_trash'  => esc_html__( 'No Destinations found in Trash', 'transfers' ),

		);

		$args = array(

			'label'               => esc_html__( 'Destination', 'transfers' ),

			'description'         => esc_html__( 'Destination information pages', 'transfers' ),

			'labels'              => $labels,

			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'page-attributes' ),

			'taxonomies'          => array( ),

			'hierarchical'        => true,

			'public'              => true,

			'show_ui'             => true,

			'show_in_menu'        => true,

			'show_in_nav_menus'   => true,

			'show_in_admin_bar'   => true,

			'can_export'          => true,

			'has_archive'         => true,

			'exclude_from_search' => false,

			'publicly_queryable'  => true,

			'capability_type'     => 'page',

			'rewrite' 			  =>  array('slug' => $destinations_permalink_slug),

		);

		

		register_post_type( 'destination', $args );	

	}

		

	function list_destinations($paged = 0, $per_page = -1, $orderby = '', $order = '', $parent_id = null, $author_id = null, $include_private = false, $count_only = false ) {

	

		$args = array(

			'post_type'         => 'destination',

			'post_status'       => array('publish'),

			'posts_per_page'    => $per_page,

			'paged' 			=> $paged, 

			'orderby'           => $orderby,

			'suppress_filters' 	=> false,

			'order'				=> $order,

		);



		if (isset($parent_id) && $parent_id > 0) {

			$args['post_parent'] = $parent_id;

		} else {

			$args['post_parent'] = 0;

		}

		

		$meta_query = array('relation' => 'AND');

		

		if ($include_private) {

			$args['post_status'][] = 'private';

		}

		

		if (isset($author_id)) {

			$author_id = intval($author_id);

			if ($author_id > 0) {

				$args['author'] = $author_id;

			}

		}

		

		$args['meta_query'] = $meta_query;

	

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

	

	public function list_leaf_destinations($parent_id=null, &$leaves) {

		

		$destination_results = $this->list_destinations(0, -1, 'title', 'ASC', $parent_id);

		

		if ( count($destination_results) > 0 && $destination_results['total'] > 0 ) {

			

			foreach ($destination_results['results'] as $destination_result) {



				$transfers_sub_destination = new transfers_destination($destination_result->ID);

				

				if ($transfers_sub_destination->is_parent() == '1') {

					$this->list_leaf_destinations($transfers_sub_destination->get_id(), $leaves);

				} else {

					$leaves[] = $transfers_sub_destination;

				}

			}		

		}

	}

}



global $transfers_destinations_post_type;

// store the instance in a variable to be retrieved later and call init

$transfers_destinations_post_type = Transfers_Destinations_Post_Type::get_instance();

$transfers_destinations_post_type->init();