<?php
/**
 * The template for displaying the comments.
 *
 * This contains both the comments and the comment form.
 */

// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ( __('Please do not load this page directly. Thanks!', 'coupon' ) );
 
if ( post_password_required() ) { ?>
<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'coupon' ); ?></p>
<?php
return;
}
?>
<!-- You can start editing here. -->
<?php if ( have_comments() ) : ?>
	<div id="comments">
		<h4 class="total-comments"><?php comments_number(__('No Responses', 'coupon' ), __('Total Comments <span>(1)</span>', 'coupon' ),  __('Total Comments <span>(%)</span>', 'coupon' ) );?></h4>
		<ol class="commentlist">
			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // are there comments to navigate through ?>
				<div class="navigation">
					<div class="alignleft"><?php previous_comments_link() ?></div>
					<div class="alignright"><?php next_comments_link() ?></div>
				</div>
			<?php }
			
			wp_list_comments('callback=mts_comments');
			
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // are there comments to navigate through ?>
				<div class="navigation">
					<div class="alignleft"><?php previous_comments_link() ?></div>
					<div class="alignright"><?php next_comments_link() ?></div>
				</div>
			<?php } ?>
		</ol>
	</div>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
	<div id="commentsAdd">
		<div id="respond" class="box m-t-6">
			<?php global $aria_req; 
			$consent  = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
			$comments_args = array(
					'title_reply'=>'<h4><span>'.__('Reply', 'coupon' ).'</span></h4>',
					'comment_notes_before' => '',
					'comment_notes_after' => '',
					'label_submit' => __( 'Post Comment', 'coupon' ),
					'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="'.__('Comment Text*', 'coupon' ).'"></textarea></p>',
					'fields' => apply_filters( 'comment_form_default_fields',
						array(
							'author' => '<p class="comment-form-author">'
							.( $req ? '' : '' ).'<input id="author" name="author" type="text" placeholder="'.__('Name*', 'coupon' ).'" value="'.esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
							'email' => '<p class="comment-form-email">'
							.($req ? '' : '' ) . '<input id="email" name="email" type="text" placeholder="'.__('Email*', 'coupon' ).'" value="' . esc_attr(  $commenter['comment_author_email'] ).'" size="30"'.$aria_req.' /></p>',
							'url' => '<p class="comment-form-url"><input id="url" name="url" type="text" placeholder="'.__('Website', 'coupon' ).'" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30" /></p>',
						) 
					)
				); 
			comment_form($comments_args); ?>
		</div>
	</div>
<?php endif; // if you delete this the sky will fall on your head ?>
