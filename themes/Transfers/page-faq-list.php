<?php
/**
/* Template Name: Faq List Page
 * The template for displaying the list of faqs.
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

global $post, $transfers_theme_globals, $transfers_faqs_post_type;

$args = array( 'hide_empty' => false, 'fields' => 'all' );
$taxonomies = array( 'faq_category' );
$faq_categories = get_terms($taxonomies, $args);

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$sort_descending = false;
if (isset($page_custom_fields['list_sort_descending'])) {
	$sort_descending = $page_custom_fields['list_sort_descending'][0] == '1' ? true : false;
}

$sort_order = $sort_descending ? 'DESC' : 'ASC';

$sort_by = 'ID';
if (isset($page_custom_fields['list_sort_by'])) {
	$sort_by = $page_custom_fields['list_sort_by'][0];
}

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'three-fourth';
if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
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
			<?php } ?>
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<!--- Content -->
			<div class="content <?php echo esc_attr($section_class); ?>">
				<?php
				$i=0;
				foreach ($faq_categories as $faq_category) {
				?>
					<h3 id="faqs<?php echo esc_attr($faq_category->term_id); ?>"><?php echo esc_html($faq_category->name); ?></h3>
					<dl class="faqs">
				<?php
					$faq_results = $transfers_faqs_post_type->list_faqs(0, -1, $sort_by, $sort_order, $faq_category->term_id);

					foreach ($faq_results['results'] as $faq_result) {
						global $post;
						$post = $faq_result;
						setup_postdata( $post );
					?>
						<!-- Item -->
						<dt><?php the_title(); ?></dt>
						<dd>
							<?php if ( has_post_thumbnail() ) { ?>
							<figure class="entry-featured">
								<?php the_post_thumbnail(TRANSFERS_CONTENT_IMAGE_SIZE); ?>
							</figure>
							<?php } ?>
							<?php the_content(); ?>
						</dd>
						<!-- // Item -->
					<?php
						$i++;
					} ?>
					</dl>
				<?php } ?>
			</div>
			<!--- // Content -->
			<?php
			global $exclude_right_sidebar_wrap;
			$exclude_right_sidebar_wrap = true;
			// this page is a special case, we always show the left sidebar because of the menu listing the faq element titles
			?>
			<aside id="right-sidebar" class="widget-area one-fourth sidebar right" role="complementary">
				<ul>
					<?php
					if (is_array($faq_categories) && count($faq_categories) > 0 ) { ?>
					<!-- Widget -->
					<li>
						<div class="widget">
							<ul class="categories">
							<?php
							$i=0;
							foreach ($faq_categories as $faq_category) {
							?>
								<li class="<?php echo esc_attr($i==0 ? 'active' : ''); ?>"><a class="anchor" href="#faqs<?php echo esc_attr($faq_category->term_id); ?>"><?php echo esc_html($faq_category->name); ?></a></li>
							<?php
								$i++;
							} ?>
							</ul>
						</div>
					</li>
					<!-- //Widget -->
					<?php
					}
					if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
						get_sidebar('right');
					?>
				</ul>
			</aside>
		</div>
	</div>
	<?php endif; ?>
<?php
get_footer();
