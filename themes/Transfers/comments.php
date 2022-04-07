<?php 
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) { ?>
<p class="nocomments"><?php esc_html__('This post is password protected. Enter the password to view comments.', 'transfers') ?></p>
<?php
return;
}
?><!--comments-->
<div class="comments" id="comments">
	<?php if ( have_comments() ) : ?>
	<h3><?php comments_number( __('No comments', 'transfers'), __('One comment', 'transfers'), __('% comments', 'transfers') );?></h3>
	<ol class="comment-list">
	<?php wp_list_comments('type=comment&callback=transfers_comment&end-callback=transfers_comment_end'); ?>
	</ol>
 	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'transfers' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'transfers' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'transfers' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php else : // this is displayed if there are no comments so far ?>
	 
	<?php if ('open' == $post->comment_status) : ?>
	<!-- If comments are open, but there are no comments. -->
	 
	<?php else : // comments are closed ?>
	<!-- If comments are closed. -->
	<p class="nocomments"></p>
	 
	<?php endif; ?>
	<?php endif; ?>
	
	<?php if ('open' == $post->comment_status) : ?>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p><?php echo sprintf(wp_kses(__('You must be <a href="%s/wp-login.php?redirect_to=%s">logged in</a> to post a comment.', 'transfers'), array('a' => array('href' => array()))), esc_url( home_url('/')), esc_url(get_permalink())); ?></p>
	<?php else : ?>

	<?php 
	
	$args = array();
	$args['logged_in_as'] = "<p>" . sprintf(wp_kses(__('Logged in as <a href="%s/wp-admin/profile.php">%s</a>.', 'transfers'), array('a' => array('href' => array(), 'title' => array()))), esc_url( home_url('/')), $user_identity) . ' ' . sprintf(wp_kses(__('<a href="%s" title="Log out of this account">Log out &raquo;</a>', 'transfers'), array('a' => array('href' => array(), 'title' => array()))), wp_logout_url(get_permalink())) . '</p>';

	ob_start();
	?>
		<p><?php echo wp_kses(__('<strong>Note:</strong> Comments on the web site reflect the views of their authors, and not necessarily the views of the transfers internet portal. Requested to refrain from insults, swearing and vulgar expression. We reserve the right to delete any comment without notice explanations.', 'transfers'), array('strong' => array())) ?></p>
		<p><?php echo wp_kses(__('Your email address will not be published. Required fields are signed with <span class="req">*</span>', 'transfers'), array('span' => array()))	?></p>
	<?php
	$args['comment_notes_before'] = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	?>
		<div class="f-row">
			<div class="full-width">
				<label for="comment"><?php esc_html_e('Your comment', 'transfers'); ?> <?php if ($req) echo "*"; ?></label>
				<textarea id="comment" name="comment" rows="10" cols="10"></textarea>
			</div>
		</div>
	<?php
	$args['comment_field'] = ob_get_contents();
	ob_end_clean();
	
	$fields =  array();
	
	ob_start();
	?>
		<div class="f-row">
			<div class="one-half">
				<label for="author"><?php esc_html_e('Name', 'transfers'); ?> <?php if ($req) echo "*"; ?></label>			
				<input type="text" id="author" name="author" value="<?php echo esc_attr($comment_author); ?>" />
			</div>

	<?php
	$fields['author'] = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	?>
			<div class="one-half">
				<label for="email"><?php esc_html_e('Email', 'transfers'); ?> <?php if ($req) echo "*"; ?></label>
				<input type="email" id="email" name="email" value="<?php echo esc_attr($comment_author_email); ?>" />
			</div>
		</div>
	<?php
	$fields['email'] = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	?>
		<div class="f-row">
			<div class="full-width">
				<label for="url"><?php esc_html_e('Website', 'transfers'); ?></label>
				<input type="text" id="url" name="url" value="<?php echo esc_attr($comment_author_url); ?>" />
			</div>
		</div>
	<?php
	$fields['url'] = ob_get_contents();
	ob_end_clean();
	
	$args['fields'] = $fields;

	ob_start(); 	
	comment_form($args);
	$form_html = ob_get_clean(); 
	$form_html = str_replace('class="submit"','class="btn color medium right"', $form_html);
	$form_html = str_replace('class="form-submit"','class="f-row"', $form_html);
	$form_html = str_replace('class="comment-respond"','class="comment-respond box"', $form_html);	
	?>

	<?php
		$allowedtags = transfers_get_allowed_form_tags_array();
	
		echo wp_kses($form_html, $allowedtags);
	?>	
	<?php endif; /* if (get_option('comment_registration')... */ ?>	
	<?php endif; /* if ('open'... */ ?>
	
</div><!--comments-->
<!--bottom navigation-->