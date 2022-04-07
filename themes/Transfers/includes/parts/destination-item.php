<?php
	global $post, $destination_class, $display_mode, $transfers_theme_globals, $transfers_plugin_globals, $transfers_destinations_post_type;

	$post_id = $post->ID;
	$post_obj = new transfers_service($post);
	$base_id = $post_obj->get_base_id();

	$post_image = $post_obj->get_main_image(TRANSFERS_CONTENT_IMAGE_SIZE);
	if (empty($post_image)) {
		$post_image = transfers_get_file_uri('/images/uploads/img.jpg');
	}
?>
<!-- Item -->
<article class="one-half">
	<?php if (!empty($post_image)) { ?>
	<figure class="featured-image">
		<img src="<?php echo esc_url($post_image); ?>" alt="<?php the_title(); ?>" />
		<div class="overlay">
			<a href="<?php echo esc_url(get_permalink($post_id)); ?>" title="<?php the_title(); ?>" class="expand">+</a>
		</div>
	</figure>
	<?php } ?>
	<div class="description">
		<div>
			<h3><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($post_obj->get_title()); ?></a></h3>
			<?php
				$child_destination_results = $transfers_destinations_post_type->list_destinations(0, 5, 'post_title', 'ASC', $post_id);

				if (is_array($child_destination_results) && count($child_destination_results) > 0 && $child_destination_results['total'] > 0 ) {
					foreach ($child_destination_results['results'] as $child_destination) {
						$transfers_child_destination = new transfers_destination($child_destination->ID);
					?>
			<p><a href="<?php echo esc_url(get_permalink($transfers_child_destination->get_id())); ?>"><?php echo esc_html($transfers_child_destination->get_title()); ?></a></p>
			<?php
					}
				}
			?>
		</div>
		<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="more"><?php esc_html_e('See all', 'transfers'); ?></a>
	</div>
</article>
<!-- //Item -->