<?php
/**
/* Template Name: Page
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

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
	$section_class = 'three-fourth';
?>
	<?php  if ( have_posts() ) : the_post(); ?>
	<!-- Page info -->
	<header class="site-title color">
		<div class="wrap">
			<div class="container">
				<h1><?php the_title(); ?></h1>
				<?php $transfers_theme_globals->get_breadcrumbs(); ?>
			</div>
		</div>
	</header>
	
	<div class="wrap">
		<div class="row">
			<!--- Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<div class="content <?php echo esc_attr($section_class); ?>">
				<article <?php post_class("static-content post single"); ?>>
					<?php if ( has_post_thumbnail() ) { ?>
					<figure class="entry-featured">						
						<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE, array('title' => '')); ?>
						<div class="overlay">
							<a href="<?php esc_url(get_the_permalink()) ?>" class="expand">+</a>
						</div>
					</figure>
					<?php } ?>
					<div class="entry-content">
						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
						<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
					</div>
				</article>
				<?php comments_template( '', true ); ?>						
			</div>
			<!--- // Content -->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
	</div>
	
	<?php endif; ?>	
<?php 
get_footer();