<?php
/**
/* Template Name: 100 wide page
/* 100 wide page template
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

?>

<?php  if ( have_posts() ) : the_post(); ?>
	<div class="wrap">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'transfers' ) ); ?>
		<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
	</div>
<?php endif; ?>	
<?php 
get_footer();