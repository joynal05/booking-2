<?php
/**
/* Template Name: Service List Page
 * The template for displaying the list of services.
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

global $post, $transfers_theme_globals, $transfers_services_post_type;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

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

$section_class = 'three-fourth';
if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
	$section_class = 'one-half';
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
			<!--- Content -->
			<?php } ?>
			<?php
			global $exclude_left_sidebar_wrap;
			$exclude_left_sidebar_wrap = true;
			// this page is a special case, we always show the left sidebar because of the menu listing the service element titles
			?>
			<aside id="left-sidebar" class="left-sidebar services-sidebar widget-area one-fourth sidebar left" role="complementary">
				<ul>
					<?php
					$service_results = $transfers_services_post_type->list_services(0, -1, $sort_by, $sort_order);
					if (is_array($service_results) && count($service_results) > 0 && $service_results['total'] > 0 ) {
	?>
					<!-- Widget -->
					<li>
						<div class="widget">
							<ul class="categories">
							<?php
							$i=0;
							foreach ($service_results['results'] as $service_result) {
								global $post;
								$post = $service_result;
								setup_postdata( $post );
							?>
								<li class="<?php echo esc_attr($i==0 ? 'active' : ''); ?>"><a href="#tab<?php echo esc_attr($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a></li>
							<?php
								$i++;
							} ?>
							</ul>
						</div>
					</li>
					<!-- //Widget -->
					<?php
					}
					if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
						get_sidebar('left');
					?>
				</ul>
			</aside>
			<div class="content services-list <?php echo esc_attr($section_class); ?>">
				<?php
				$i=0;
				foreach ($service_results['results'] as $service_result) {
					global $post;
					$post = $service_result;
					setup_postdata( $post );
				?>
					<article class="single hentry" id="tab<?php echo esc_html($post->ID); ?>">
						<?php if ( has_post_thumbnail() ) { ?>
						<figure class="entry-featured">
							<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE); ?>
						</figure>
						<?php } ?>
						<div class="entry-content">
							<h2><?php the_title(); ?></h2>
							<?php the_content(); ?>
						</div>
					</article>
				<?php
					$i++;
				} ?>
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
