<?php
/**
/* Template Name: Blog index page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */

get_header();
get_sidebar('under-header');

global $post, $transfers_theme_globals;

$page_title = __('Blog', 'transfers');
$transfers_blog_index_id = get_option('page_for_posts');
if ($transfers_blog_index_id > 0) {
	$transfers_blog_index = get_post($transfers_blog_index_id);
	$page_title = $transfers_blog_index->post_title;
}

global $transfers_theme_globals;

$page_sidebar_positioning = $transfers_theme_globals->get_blog_index_sidebar_position();
$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;

$section_class = '';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right')
	$section_class = 'three-fourth';
else
	$section_class = 'full-width';

$blog_index_sort_by = $transfers_theme_globals->get_blog_index_sort_by_column();
$blog_index_sort_descending = $transfers_theme_globals->blog_index_sort_descending();
$blog_index_show_grid_view = $transfers_theme_globals->blog_index_show_grid_view();

$pager_page = (get_query_var('paged')) ? get_query_var('paged') : 1;

$page_id = $post->ID;
if ($transfers_blog_index_id > 0) {
	$page_id = $transfers_blog_index_id;
}
$categories = wp_get_post_terms($transfers_blog_index_id, 'category', array("fields" => "ids"));

$args = array(
	'paged'			   => $pager_page,
	'orderby'          => $blog_index_sort_by,
	'order'            => ($blog_index_sort_descending ? 'ASC' : 'DESC'),
	'post_type'        => 'post',
	'post_status'      => 'publish');

if (is_array($categories) && count($categories) > 0) {
	$args['category__in'] = $categories;
}

?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php echo esc_html($page_title); ?></h1>
				<?php
				$allowed_tags = Transfers_Theme_Utils::get_allowed_breadcrumbs_tags_array();
				$breadcrumbs_html = $transfers_theme_globals->get_breadcrumbs();
				echo wp_kses($breadcrumbs_html, $allowed_tags);
				?>
			</div>
		</div>
	</header>
	<!-- //Page info -->
	<div class="wrap">
		<div class="row">
			<!--- Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left') {
				get_sidebar('left');
			}
			?>
			<div class="content <?php echo esc_attr($section_class); ?>">
				<div class="row">
					<?php
					$post_class = 'full-width';
					if ($blog_index_show_grid_view)
						$post_class = 'one-half';

					$query = new WP_Query($args);
					if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>

					<div class="<?php echo esc_attr($post_class); ?>">
						<article id="post-<?php the_ID(); ?>" <?php post_class(""); ?>>
							<?php if ( has_post_thumbnail() ) { ?>
							<figure class="entry-featured">
								<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => get_the_title())); ?>
								<div class="overlay">
									<a href="<?php echo esc_url(get_the_permalink()) ?>" class="expand">+</a>
								</div>
							</figure>
							<?php } ?>
							<div class="entry-content">
								<h2><a href="<?php echo esc_url(get_the_permalink()) ?>"><?php the_title(); ?></a></h2>
								<p class="entry-meta">
									<span class="date"><?php esc_html_e('Date', 'transfers');?>: <?php the_time(get_option('date_format')); ?></span>
									<span class="author"><?php esc_html_e('By ', 'transfers'); the_author_posts_link(); ?></span>
									<span class="categories"><?php esc_html_e('Categories', 'transfers'); ?>: <?php the_category(' ') ?></span>
									<span class="tags"><?php the_tags(); ?></span>
									<span class="comments">
										<a href="<?php echo esc_url(get_comments_link()); ?>" rel="nofollow">
											<?php comments_number(esc_html__('No comments', 'transfers'), esc_html__('1 Comment', 'transfers'), esc_html__('% Comments', 'transfers')); ?>
										</a>
									</span>
								</p>
								<?php the_excerpt(); ?>
								<a href="<?php echo esc_url(get_the_permalink()) ?>" class="more" rel="nofollow"><?php esc_html_e('Read More...', 'transfers'); ?></a>
							</div>
						</article>
					</div>
					<?php endwhile; ?>
					<!--bottom navigation-->
					<nav class="page-navigation bottom-nav">
						<div class="pager">
						<?php
							Transfers_Theme_Utils::display_pager($query->max_num_pages);
						?>
						</div>
					</nav>
					<!--//bottom navigation-->
					<?php endif; ?>
				</div>
			</div>
			<!--- //Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right') {
				get_sidebar('right');
			}
			?>
		</div>
	</div>
<?php
get_footer();
