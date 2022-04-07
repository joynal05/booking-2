<?php
/**
 * Archive page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */

get_header();  
get_sidebar('under-header');

global $transfers_theme_globals;

$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
	'paged'			   => $page,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'post',
	'post_status'      => 'publish'); 
    
if (is_category()) {
    $queried_object = get_queried_object();
    $category_id = $queried_object->cat_ID;
    $args['cat'] = $category_id;
} else if (is_tag()) {
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $args['tax_query'] = array(
		array(
			'taxonomy' => 'post_tag',
			'terms' => $term_id,
			'field' => 'term_id'
		)
    );
}
	
?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php single_cat_title(); ?></h1>
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
			<div class="content three-fourth">
				<div class="row">
					<?php 
					query_posts( $args );
					if (have_posts()) : while (have_posts()) : the_post(); ?>	
				
					<div class="full-width">
						<article id="post-<?php the_ID(); ?>" <?php post_class(""); ?>>
							<?php if ( has_post_thumbnail() ) { ?>
							<figure class="entry-featured">						
								<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE); ?>
								<div class="overlay">
									<a href="<?php esc_url(get_the_permalink()) ?>" class="expand">+</a>
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
										<a href="<?php esc_url(get_comments_link()); ?>" rel="nofollow">
											<?php comments_number( __('No comments', 'transfers'), __('1 Comment', 'transfers'), __('% Comments', 'transfers')); ?>
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
							global $wp_query;
							Transfers_Theme_Utils::display_pager($wp_query->max_num_pages); 
						?>
						</div>
					</nav>
					<!--//bottom navigation-->
					<?php endif; ?>
				</div>
			</div>
			<?php get_sidebar('right'); ?>				
			<!--- //Content -->
		</div>
	</div>
<?php 
get_footer();