<?php
/**
/* Template Name: Home Page
 * The Home Page template file.
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
$page_sidebar_positioning = Transfers_Theme_Utils::get_page_custom_field_string_value($page_id, 'page_sidebar_positioning', '');

$section_class = 'full-width';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
	$section_class = 'three-fourth';
	
$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}	
?>
	<!--- Content -->
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	?>
	<div class="<?php echo esc_attr($section_class); ?>">
		<?php get_sidebar('home-content'); ?>
	</div>
	<!--- //Content -->
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
		get_sidebar('right');
	?>
<?php 
get_footer();