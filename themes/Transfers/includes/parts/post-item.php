<?php
	global $post, $post_class, $display_mode, $transfers_theme_globals, $transfers_posts_post_type;
	
	$post_id = $post->ID;
	$post_obj = new transfers_post($post);
	$base_id = $post_obj->get_base_id();
	
	$post_image = $post_obj->get_main_image(TRANSFERS_CONTENT_IMAGE_SIZE);	
	if (empty($post_image)) {
		$post_image = transfers_get_file_uri('/images/uploads/img.jpg');
	}
?>
<!-- Item -->
<article class="<?php echo esc_attr($post_class); ?> fadeIn">
	<?php if (!empty($post_image)) { ?>
	<figure class="featured-image">
		<img src="<?php echo esc_url($post_image); ?>" alt="<?php the_title(); ?>" />
		<div class="overlay">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="expand">+</a>
		</div>
	</figure>
	<?php } ?>
	<div class="details">
		<h4><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
		<?php the_excerpt(); ?>
		<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e('More info', 'transfers'); ?>" class="more"><?php esc_html_e('More info', 'transfers'); ?></a>
	</div>
</article>
<!-- //Item -->