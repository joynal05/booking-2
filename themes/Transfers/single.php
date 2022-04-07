<?php
/**
 * Single blog post
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Transfers
 * @since Transfers 1.0
 */

get_header();  
get_sidebar('under-header');

global $post, $transfers_theme_globals;
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
			<!--- Content -->
			<div class="content three-fourth">
				<article id="post-<?php the_ID(); ?>" <?php post_class("single"); ?>>
					<?php if ( has_post_thumbnail() ) { ?>
					<figure class="entry-featured">						
						<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => get_the_title())); ?>
						<div class="overlay">
							<a href="<?php esc_url(get_the_permalink()) ?>">+</a>
						</div>
					</figure>
					<?php } ?>
					<div class="entry-content">
						<p class="entry-meta">
							<span class="date"><?php esc_html_e('Date', 'transfers');?>: <?php the_time(get_option('date_format')); ?></span> 
							<span class="author"><?php esc_html_e('By ', 'transfers'); the_author_posts_link(); ?></span> 
							<span class="categories"><?php esc_html_e('Categories', 'transfers'); ?>: <?php the_category(' ') ?></span>
							<span class="tags"><?php the_tags(); ?></span>
							<span class="comments">
								<a href="<?php esc_url(get_comments_link()); ?>" rel="nofollow">
									<?php comments_number(__('No comments', 'transfers'), __('1 Comment', 'transfers'), __('% Comments', 'transfers')); ?>
								</a>
							</span>
						</p>
						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
						<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
					</div>
				</article>				
				<?php comments_template( '', true ); ?>		
			</div>
			<!--- // Content -->
			<?php get_sidebar('right');	?>
		</div>
	</div>
	<?php endif; ?>	
<?php 
get_footer();