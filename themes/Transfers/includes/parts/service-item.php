<?php
	global $post, $post_class, $display_mode, $transfers_theme_globals, $transfers_plugin_globals;
	
	$post_id = $post->ID;
	$post_obj = new transfers_service($post);
	$base_id = $post_obj->get_base_id();
	
	$post_image = $post_obj->get_main_image(TRANSFERS_CONTENT_IMAGE_SIZE);	
	if (empty($post_image)) {
		$post_image = transfers_get_file_uri('/images/uploads/img.jpg');
	}
	
	$service_list_page_url = $transfers_plugin_globals->get_service_list_page_url();

?>
<!-- Item -->
<article class="<?php echo esc_attr($post_class); ?> fadeIn">
	<?php if (!empty($post_image)) { ?>
	<figure class="featured-image">
		<img src="<?php echo esc_url($post_image); ?>" alt="<?php the_title(); ?>" />
		<div class="overlay">
			<a href="<?php echo esc_url($service_list_page_url); ?>" title="<?php the_title(); ?>" class="expand">+</a>
		</div>
	</figure>
	<?php } ?>
	<div class="details">
		<h4><a href="<?php echo esc_url($service_list_page_url); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
		<p>
		<?php 
			$allowed_tags = transfers_get_allowed_content_tags_array();
			echo wp_kses($post_obj->get_excerpt(), $allowed_tags); 
		?>		
		</p>
		<a href="<?php echo esc_url($service_list_page_url); ?>" title="<?php esc_attr_e('Read more', 'transfers'); ?>" class="more"><?php esc_html_e('Read more', 'transfers'); ?></a>
	</div>
</article>
<!-- //Item -->