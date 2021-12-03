<?php
/**
 * Template Name: Coupons Page
 */
$mts_options = get_option(MTS_THEME_NAME);
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
				<?php if( !empty( $mts_options['mts_top_coupon_description'] ) ) : ?>
					<div class="rewards"><?php echo $mts_options['mts_top_coupon_description']; ?></div>
				<?php endif; ?>
			</div>

			<?php
			$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
			$args = array(
				'post_type' => 'coupons',
				'post_status' => 'publish',
				'paged' => $paged,
				'orderby' => 'post_date',
				'coupons_template' => 1,
			);
			$latest_coupons_query = new WP_Query( $args );

			global $wp_query;
			// Put default query object in a temp variable
			$tmp_query = $wp_query;
			// Now wipe it out completely
			$wp_query = null;
			// Re-populate the global with our custom query
			$wp_query = $latest_coupons_query;
			$j = 0; $k = 0; 
			if ( $latest_coupons_query->have_posts() ) : while ( $latest_coupons_query->have_posts() ) : $latest_coupons_query->the_post();
				get_template_part( 'coupon-loop' );
			endwhile; endif; ++$j;
			if ( $j !== 0 ) { // No pagination if there is no results
				mts_pagination('', 3, 'mts_coupon_pagenavigation_type');
			}
			// Restore original query object
			$wp_query = $tmp_query;
			// Be kind; rewind
			wp_reset_postdata();
			?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>