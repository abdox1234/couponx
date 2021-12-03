<?php
$mts_options = get_option(MTS_THEME_NAME);

$mts_homepage_blog_title = $mts_options['mts_homepage_blog_title'];

$mts_blog_num = empty ( $mts_options['mts_blog_count_home'] ) ? '3' : $mts_options['mts_blog_count_home'];
$mts_home_blog_col = $mts_options['mts_home_blog_col'];
$cols = ' one-third';
if ( '1' == $mts_home_blog_col ) {
	$cols = ' one-fourth';
} ?>
<div id="blog-feed-home" class="section clearfix">
	<div id="page">
		<?php if ( !empty( $mts_homepage_blog_title ) ) { ?>
			<h2 class="section-title"><span><?php echo $mts_homepage_blog_title; ?></span></h2>
		<?php }?>
		<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn-archive-link"><?php _e( 'All Blog Posts', 'coupon' ); ?></a>
		<div class="grid">
		<?php
		$query = new WP_Query();
		$query->query('&ignore_sticky_posts=1&posts_per_page='.$mts_blog_num);
		while ( $query->have_posts() ) : $query->the_post(); ?>
			<article class="home-blog-item grid-box<?php echo $cols; ?>">
				<div class="grid-inner home-blog-box-inner">
					<a class="blog-image" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'coupon-featured', array('title' => '')); ?>
						<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
					</a>
					<h3 class="project-title title"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					<div class="project-description description"><?php echo mts_excerpt(18); ?></div>
				</div>
			</article>
		<?php endwhile;
		wp_reset_query(); ?>
		</div>
	</div>
</div>