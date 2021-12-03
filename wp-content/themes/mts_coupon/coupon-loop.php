<?php 
$mts_options = get_option(MTS_THEME_NAME);
$postid = get_the_ID();
$coupon_extra_rewards = get_post_meta( $postid, 'mts_coupon_extra_rewards', true );
$expired_class = implode(' ', mts_expired_coupon_class()); ?>

<article class="latestPost excerpt coupon <?php echo $expired_class; ?>">
	<?php echo mts_get_coupon_featured_wrapper($postid); ?>
	<div class="right-content">
		<header>
			<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
			<?php if( !empty( $coupon_extra_rewards ) ) : ?>
				<div class="coupon_extra_rewards"><?php echo $coupon_extra_rewards; ?></div>
			<?php endif; ?>
			<?php mts_the_postinfo('coupon'); ?>
		</header>
		<div class="front-view-content">
			<?php echo mts_excerpt(50); ?>
		</div>
	</div>
</article>