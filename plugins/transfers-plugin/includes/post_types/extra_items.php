<?php

class Transfers_Extra_Items_Post_Type extends Transfers_BaseSingleton {

	private $enable_extra_items;
	private $extra_item_custom_meta_fields;

	protected function __construct() {

		global $transfers_plugin_globals, $transfers_transport_types_post_type;

		$this->enable_extra_items = $transfers_plugin_globals->enable_extra_items();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		if ($this->enable_extra_items) {

			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter('manage_edit-extra_columns', array( $this, 'manage_edit_extra_item_columns'), 10, 1);
			add_action( 'transfers_plugin_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
			add_action( 'admin_init', array( $this, 'extra_item_admin_init' ) );
		}

	}

	function extra_item_admin_init() {


		if ($this->enable_extra_items) {

			global $transfers_plugin_globals, $transfers_transport_types_post_type;

			$transport_types = array();
			$transport_types_results = $transfers_transport_types_post_type->list_transport_types(0, -1, 'post_title', 'ASC');
			if (count($transport_types_results) > 0 && $transport_types_results['total'] > 0) {
				foreach ($transport_types_results['results'] as $transport_types_result) {
					$transport_types[] = array('value' => $transport_types_result->ID, 'label' => $transport_types_result->post_title);
				}
			}

			$this->extra_item_custom_meta_fields = array();

			if ($transfers_plugin_globals->enable_shared_transfers()) {

				$this->extra_item_custom_meta_fields[] = array( // Post ID select box
					'label'	=> esc_html__('Shared price per item?', 'transfers'), // <label>
					'desc'	=> esc_html__('What is the individual price of this extra item when transfer is shared?', 'transfers'), // description
					'id'	=> '_extra_item_price_shared', // field id and name
					'type'	=> 'number',
					'step'  => 'any'
				);

				$this->extra_item_custom_meta_fields[] = array( // Post ID select box
					'label'	=> esc_html__('Max allowed items per shared transfer per vehicle?', 'transfers'), // <label>
					'desc'	=> esc_html__('How many pieces of the extra item are allowed per shared transfer per vehicle?', 'transfers'), // description
					'id'	=> '_extra_item_max_allowed_per_shared_transfer', // field id and name
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '100',
					'step'	=> '1'
				);
			}

			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Private price per item?', 'transfers'), // <label>
				'desc'	=> esc_html__('What is the individual price of this extra item when transfer is private?', 'transfers'), // description
				'id'	=> '_extra_item_price_private', // field id and name
				'type'	=> 'number',
				'step'  => 'any'
			);

			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Max allowed items per private transfer?', 'transfers'), // <label>
				'desc'	=> esc_html__('How many pieces of the extra item are allowed per private transfer vehicle?', 'transfers'), // description
				'id'	=> '_extra_item_max_allowed_per_private_transfer', // field id and name
				'type'	=> 'slider',
				'min'	=> '1',
				'max'	=> '100',
				'step'	=> '1'
			);

			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported transport types', 'transfers'), // <label>
				'desc'	=> '', // description
				'id'	=>  'transport_types', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $transport_types // post types to display, options are prefixed with their post type
			);

			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Is this item required', 'transfers'), // <label>
				'desc'	=> esc_html__('If checked users will be forced to pay for this extra item - allowing admin to for example charge for tourist tax', 'transfers'), // description
				'id'	=> '_extra_item_is_required', // field id and name
				'type'	=> 'checkbox', // type of field
			);
		}

		new Transfers_Add_Meta_Box( 'extra_item_custom_meta_fields', esc_html__('Extra information', 'transfers'), $this->extra_item_custom_meta_fields, 'extra_item' );
	}

	function remove_unnecessary_meta_boxes() {

	}

	function manage_edit_extra_item_columns($columns) {
		return $columns;
	}

	function initialize_post_type() {

		$this->register_extra_item_post_type();
	}

	function register_extra_item_post_type() {

		global $transfers_plugin_globals;

		$labels = array(
			'name'                => esc_html__( 'Extra items', 'transfers' ),
			'singular_name'       => esc_html__( 'Extra item', 'transfers' ),
			'menu_name'           => esc_html__( 'Extra items', 'transfers' ),
			'all_items'           => esc_html__( 'All Extra items', 'transfers' ),
			'view_item'           => esc_html__( 'View Extra item', 'transfers' ),
			'add_new_item'        => esc_html__( 'Add New Extra item', 'transfers' ),
			'add_new'             => esc_html__( 'New Extra item', 'transfers' ),
			'edit_item'           => esc_html__( 'Edit Extra item', 'transfers' ),
			'update_item'         => esc_html__( 'Update Extra item', 'transfers' ),
			'search_items'        => esc_html__( 'Search Extra items', 'transfers' ),
			'not_found'           => esc_html__( 'No Extra items found', 'transfers' ),
			'not_found_in_trash'  => esc_html__( 'No Extra items found in Trash', 'transfers' ),
		);
		$args = array(
			'label'               => esc_html__( 'extra item', 'transfers' ),
			'description'         => esc_html__( 'Extra item information pages', 'transfers' ),
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

		register_post_type( 'extra_item', $args );
	}

	function list_extra_items_by_transport_type($transport_type_id) {

		$results = array();

    $transport_type_id = transfers_get_current_language_post_id($transport_type_id, 'transport_type');
    $default_transport_type_id = transfers_get_default_language_post_id($transport_type_id, 'transport_type');

    $raw_results = $this->list_extra_items(0, -1, 'post_title', 'ASC');

		if ($raw_results && $raw_results['total'] > 0) {

			foreach ($raw_results['results'] as $result) {

        $extra_id = $result->ID;
        $default_extra_id = transfers_get_default_language_post_id($extra_id, 'extra_item');

        $transport_types_meta = get_post_meta($extra_id, 'transport_types', true);

        $default_transport_types_meta = get_post_meta($default_extra_id, 'transport_types', true);

        $added_to_array = false;
				if ($transport_types_meta) {
					if (in_array($transport_type_id, $transport_types_meta)) {
            $results[] = $result;
            $added_to_array = true;
					}
        }

				if (!$added_to_array && $default_transport_types_meta) {
					if (in_array($default_transport_type_id, $default_transport_types_meta)) {
						$results[] = $result;
					}
				}
			}
    }

		return $results;
	}

	function list_extra_items($paged = 0, $per_page = -1, $orderby = '', $order = '', $author_id = null, $include_private = false, $count_only = false ) {

		$args = array(
			'post_type'         => 'extra_item',
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

global $transfers_extra_items_post_type;
// store the instance in a variable to be retrieved later and call init
$transfers_extra_items_post_type = Transfers_Extra_Items_Post_Type::get_instance();
$transfers_extra_items_post_type->init();
