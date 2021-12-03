<?php
$mts_options = get_option(MTS_THEME_NAME);
$exclude_expired = 1;
if( isset( $mts_options['mts_home_expired_coupons'] ) && !empty( $mts_options['mts_home_expired_coupons'] )) {
	$exclude_expired = $mts_options['mts_home_expired_coupons'];
}
if ( !empty( $mts_options['mts_tab_cat_select'] ) ) : ?>
	<section id="tabs" class="tabs-container clearfix">
		<div class="container">
			<ul class="tabs-menu clearfix">
				<?php $first = 0; foreach( $mts_options['mts_tab_cat_select'] as $tab ) :
					if ( 'latest' === $tab['mts_tab_cat'] ) : ?>
						<li class="<?php echo ( ++$first == 1 ) ? 'current' : ''; ?>"><a href="#tab-latest"><?php esc_html_e( 'Latest', 'coupon' ); ?></a></li>
					<?php else:
						$terms = get_term( $tab['mts_tab_cat'] ); ?>
						<li class="<?php echo ( ++$first == 1 ) ? 'current' : ''; ?>"><a href="#tab-<?php echo esc_attr( $terms->term_id ); ?>"><?php echo $terms->name; ?></a></li>
					<?php endif;
				endforeach; ?>
			</ul>
			<div class="tab clearfix">
				<?php $firstcontent = 0; foreach( $mts_options['mts_tab_cat_select'] as $tab ) :

					$cat = $tab['mts_tab_cat'];
					$args = array(
						'post_type' => 'coupons',
						'posts_per_page' => $mts_options['mts_tab_postsnum'],
						'orderby' => 'date',
						'order' => 'DESC',
						'no_found_rows' => true,
					);
					if ( 'latest' === $cat ) {
						$terms = new stdClass();
						$terms->term_id = 'latest';
					} else {
						$terms = get_term( $cat );
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'mts_coupon_categories',
								'terms' => $terms->term_id,
							)
						);
					}
					$meta_query = array();
					if ( $exclude_expired ) {
						$meta_query[] = array(
						   'relation' => 'or',
				        array(
				              'key' => 'mts_coupon_expired',
				              'compare' => 'NOT EXISTS',
				        ),
				        array(
				              'key' => 'mts_coupon_expired',
				              'value' => '0',
				              'type' => 'numeric'
				        )
					  );
					}
					if( count( $meta_query ) > 0 ){
						$args['meta_query'] = $meta_query;
					}
					$tabposts = new WP_Query($args); ?>
					<div id="tab-<?php echo $terms->term_id; ?>" class="tab-content" <?php echo ( ++$firstcontent == 1 ) ? 'style="display: block;"' : ''; ?>>
					<?php while ( $tabposts->have_posts() ) { $tabposts->the_post();
					$coupon_button_type = get_post_meta( get_the_ID(), 'mts_coupon_button_type', 1 ); ?>
						<div class="tab-post">
							<?php if( has_post_thumbnail() ) : ?>
								<div class="tab-post-logo">
									<img <?php if ($firstcontent == 1) : ?>src="<?php the_post_thumbnail_url('full'); ?>"<?php else : ?>data-src="<?php the_post_thumbnail_url('full'); ?>" src=""<?php endif; ?> width="auto" height="45" alt="<?php the_title(); ?>">
								</div>
							<?php endif; ?>
							<p class="tab-text"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
							<?php if( $coupon_button_type == 'deal' ) : ?>
								<a href="<?php the_permalink(); ?>" class="dashed-button"><?php _e('Get this Offer', 'coupon'); ?></a>
							<?php elseif( $coupon_button_type == 'coupon' ) : ?>
								<a href="<?php the_permalink(); ?>" class="dashed-button"><?php _e('Show Coupon Code', 'coupon'); ?></a>
							<?php endif; ?>
						</div>
					<?php }
					wp_reset_postdata(); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
