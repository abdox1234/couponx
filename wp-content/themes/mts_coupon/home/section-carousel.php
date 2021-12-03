<?php $mts_options = get_option(MTS_THEME_NAME);
if ( !is_paged() && !empty( $mts_options['mts_custom_carousel'] ) ) { ?>
	<section id="carousel" class="clearfix">
		<div class="container coupon-carousel-container loading">
			<?php if( isset( $mts_options['mts_carousel_title'] ) && !empty( $mts_options['mts_carousel_title'] ) ) : ?>
			<h3 class="featured-category-title"><?php echo $mts_options['mts_carousel_title']; ?></h3>
			<?php endif; ?>
			<div id="slider" class="coupon-carousel">
				<?php $count = 0; foreach( $mts_options['mts_custom_carousel'] as $slide ) : ?>
					<div class="owl-item-carousel count-<?php echo ++$count; ?>">
						<?php if($slide['mts_custom_carousel_link']) : ?>
						<a href="<?php echo esc_url( $slide['mts_custom_carousel_link'] ); ?>">
						<?php endif; ?>
							<div class="coupon-carousel-wrapper clearfix">
								<?php if($slide['mts_custom_carousel_image']) { ?>
									<img src="<?php echo $slide['mts_custom_carousel_image']; ?>" alt="<?php $slide['mts_custom_carousel_title']; ?>" width="auto" height="26">
								<?php } ?>
								<div class="slide-caption">
									<div class="slide-caption-inner">
										<h2 class="slide-title"><?php echo esc_html( mts_truncate( $slide['mts_custom_carousel_description'], 35 ) ) ?></h2>
									</div>
									<!-- slide-caption-inner -->
								</div>
								<!-- slide-caption -->
							</div>
						<?php if($slide['mts_custom_carousel_link']) : ?>
						</a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div><!-- .coupon-carousel -->
		</div><!-- .coupon-carousel-container -->
	</section>
<?php } ?>