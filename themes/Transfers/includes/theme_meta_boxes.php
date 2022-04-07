<?php

class Transfers_Theme_Meta_Boxes extends Transfers_BaseSingleton {

	private $page_sidebars_custom_meta_fields;
  private $page_blog_index_custom_meta_fields;
  private $page_list_custom_meta_fields;

	private $page_sidebars_meta_box;
  private $page_blog_index_meta_box;
  private $page_list_index_meta_box;

	protected function __construct() {

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

		add_action( 'admin_init', array($this, 'pages_meta_box_admin_init' ) );
	}

	function pages_meta_box_admin_init() {

		global $transfers_theme_globals;

		$page_sidebars = array();
		$page_sidebars[] = array('value' => '', 'label' => esc_html__('No sidebar', 'transfers'));
		$page_sidebars[] = array('value' => 'left', 'label' => esc_html__('Left sidebar', 'transfers'));
		$page_sidebars[] = array('value' => 'right', 'label' => esc_html__('Right sidebar', 'transfers'));
		$page_sidebars[] = array('value' => 'both', 'label' => esc_html__('Left and right sidebars', 'transfers'));

		$this->page_sidebars_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Select sidebar positioning', 'transfers'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'page_sidebar_positioning', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $page_sidebars
			)
		);

		$this->page_sidebars_meta_box = new Transfers_Add_Meta_Box( 'page_sidebars_custom_meta_fields', esc_html__('Sidebar selection', 'transfers'), $this->page_sidebars_custom_meta_fields, 'page' );
		remove_action( 'add_meta_boxes', array( $this->page_sidebars_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'page_sidebar_add_meta_boxes' ) );

		$this->page_blog_index_custom_meta_fields = array(
			array( // taxonomy select box
				'label'	=> esc_html__('Filter by categories', 'transfers'), // <label>
				// the description is created in the callback function with a link to manage the taxonomy terms
				'id'	=> 'category', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes' // type of field
			)
    );

    $sort_by_columns = array();
    $sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Post title', 'eagle'));
    $sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Post ID', 'eagle'));
    $sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'eagle'));
    $sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'eagle'));
    $sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'eagle'));

		$this->page_blog_index_meta_box = new Transfers_Add_Meta_Box( 'page_blog_index_custom_meta_fields', esc_html__('Filters', 'transfers'), $this->page_blog_index_custom_meta_fields, 'page' );
		remove_action( 'add_meta_boxes', array( $this->page_blog_index_meta_box, 'add_box' ) );
    add_action('add_meta_boxes', array( $this, 'page_blog_index_add_meta_boxes' ) );

		$this->page_list_custom_meta_fields = array(
			array( // taxonomy select box
				'label'	=> esc_html__('Sort descending?', 'transfers'), // <label>
				// the description is created in the callback function with a link to manage the taxonomy terms
				'id'	=> 'list_sort_descending', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'checkbox' // type of field
      ),
      array( // Taxonomy Select box
				'label'	=> esc_html__('Sort by column', 'transfers'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'list_sort_by', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $sort_by_columns
			)
		);

		$this->page_list_index_meta_box = new Transfers_Add_Meta_Box( 'page_list_custom_meta_fields', esc_html__('Filters', 'transfers'), $this->page_list_custom_meta_fields, 'page' );
		remove_action( 'add_meta_boxes', array( $this->page_list_index_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'page_list_add_meta_boxes' ) );
	}

	function page_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-faq-list.php' || $template_file == 'page-service-list.php' || $template_file == 'page-destination-list.php') {
			add_meta_box( $this->page_list_index_meta_box->id, $this->page_list_index_meta_box->title, array( $this->page_list_index_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}

	function page_blog_index_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'home.php') {
			add_meta_box( $this->page_blog_index_meta_box->id, $this->page_blog_index_meta_box->title, array( $this->page_blog_index_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}

	function page_sidebar_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file != 'page-user-register.php' &&
			$template_file != 'page-user-login.php' &&
			$template_file != 'page-user-forgot-pass.php' &&
			$template_file != 'blog.php' &&
			$template_file != 'home.php' &&
			!Transfers_Theme_Utils::is_a_woocommerce_page()) {

			add_meta_box( $this->page_sidebars_meta_box->id, $this->page_sidebars_meta_box->title, array( $this->page_sidebars_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}

}

global $transfers_theme_meta_boxes;
// store the instance in a variable to be retrieved later and call init
$transfers_theme_meta_boxes = Transfers_Theme_Meta_Boxes::get_instance();
$transfers_theme_meta_boxes->init();
