<?php $mts_options = get_option(MTS_THEME_NAME);
get_header();

if( isset($mts_options['mts_carousel_pages']['coupon-archive']) == 1 ) : ?>
	<div id="inner">
		<?php get_template_part('home/section', 'carousel'); ?>
	</div>
<?php endif; ?>

<div id="page">
	<div class="article coupon-cat">
		<div id="content_box">
			<div class="cat-offers-header">
				<h3 class="featured-category-title"><span><?php single_term_title(); ?></span> <?php echo $mts_options['mts_top_coupon_title']; ?></h3>
				<?php if( term_description() ) : ?>
					<div class="rewards"><?php echo term_description(); ?></div>
				<?php endif; ?>
			</div>

			<?php
			$queried_object = get_queried_object();
			$current_catname = $queried_object->name;
			$j = 0; $k = 0; 
			if ( have_posts() ) : while ( have_posts() ) : the_post();
				get_template_part( 'coupon-loop' );
			endwhile; endif; ++$j;
			if ( $j !== 0 ) { // No pagination if there is no results
				mts_pagination('', 3, 'mts_coupon_pagenavigation_type');
			} ?>

		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>
