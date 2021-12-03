<?php $mts_options = get_option(MTS_THEME_NAME);
get_header(); ?>

<div id="page">
	<div class="article coupon-cat">
		<div id="content_box">
			<div class="cat-offers-header">
				<h3 class="featured-category-title"><span><?php _e("Search Results for:", 'coupon' ); ?></span> <?php the_search_query(); ?></h3>
			</div>
 <?php
			$queried_object = get_queried_object();
			$current_catname = $queried_object->name;
			$j = 0; $k = 0; 
			if ( have_posts() ) : while ( have_posts() ) : the_post();
				get_template_part( 'coupon-loop' );
				endwhile;
			else: ?>
				<div class="no-results">
					<h2><?php _e('We apologize for any inconvenience, there are no coupons related to this search, please hit back on your browser or use the search form below.', 'coupon' ); ?></h2>
					<?php get_search_form(); ?>
				</div><!--noResults-->
			<?php endif; ++$j;
			if ( $j !== 0 ) { // No pagination if there is no results
				mts_pagination('', 3, 'mts_coupon_pagenavigation_type');
			} ?>

		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>
