<?php
/**
/* Template Name: Destination List Page
 * The template for displaying the list of destinaions.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */
 
if (!function_exists('is_transfers_plugin_active') || !is_transfers_plugin_active()) {
	wp_redirect(home_url('/'));
	exit;
}
 
get_header();  
get_sidebar('under-header');

global $post, $transfers_plugin_globals, $transfers_theme_globals, $transfers_destinations_post_type;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
} else if ( get_query_var('page') ) {
    $paged = get_query_var('page');
} else {
    $paged = 1;
}

$posts_per_page = $transfers_plugin_globals->get_destinations_archive_posts_per_page();

$sort_descending = false;
if (isset($page_custom_fields['list_sort_descending'])) {
	$sort_descending = $page_custom_fields['list_sort_descending'][0] == '1' ? true : false;
}

$sort_order = $sort_descending ? 'DESC' : 'ASC';

$sort_by = 'post_title';
if (isset($page_custom_fields['list_sort_by'])) {
	$sort_by = $page_custom_fields['list_sort_by'][0];
}

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ( $page_sidebar_positioning == 'right' || $page_sidebar_positioning == 'left')
	$section_class = 'three-fourth';
?>
	<?php  if ( have_posts() ) : the_post(); ?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php the_title(); ?></h1>
				<?php 
				$allowed_tags = Transfers_Theme_Utils::get_allowed_breadcrumbs_tags_array();
				$breadcrumbs_html = $transfers_theme_globals->get_breadcrumbs();
				echo wp_kses($breadcrumbs_html, $allowed_tags); 
				?>
			</div>
		</div>
	</header>
	<div class="wrap">
		<div class="row">
			<?php $content = get_the_content(); 
			if (!empty($content)) { ?>
			<div class="textongrey content full-width">
				<?php if ( has_post_thumbnail() ) { ?>
				<figure class="entry-featured">						
					<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => '')); ?>
					<div class="overlay">
						<a href="<?php esc_url(get_the_permalink()) ?>" class="expand">+</a>
					</div>
				</figure>
				<?php } ?>
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
				<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
			</div>		
			<?php } ?>
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<!--- Content -->
			<div class="content location-list <?php echo esc_attr($section_class); ?>">
				<div class="row">
					<?php 
					$destination_results = $transfers_destinations_post_type->list_destinations($paged, $posts_per_page, $sort_by, $sort_order);
					
					if ( count($destination_results) > 0 && $destination_results['total'] > 0 ) {
						foreach ($destination_results['results'] as $destination_result) { 
							global $post, $destination_class;
							$post = $destination_result;
							setup_postdata( $post ); 
							$destination_class = 'one-fourth';
							get_template_part('includes/parts/destination', 'item');
						}
					}
				?>
				</div>
			</div>
			<!--- // Content -->
			<!--bottom navigation-->
			<nav class="page-navigation bottom-nav">
				<div class="pager">
				<?php 	
					$total_results = $destination_results['total'];
					Transfers_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
				?>
				</div>
			</nav>
			<!--//bottom navigation-->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
	</div>
	<?php endif; ?>
<?php
get_footer();