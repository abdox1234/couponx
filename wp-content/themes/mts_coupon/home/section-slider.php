<?php $mts_options = get_option(MTS_THEME_NAME);
if ( !is_paged() && is_home() && !empty( $mts_options['mts_custom_slider'] ) ) { ?>

	<section id="slider" class="clearfix">
		<div class="container primary-slider-container clearfix loading">
			<div id="slider" class="primary-slider">
				<?php $nav = '';
				foreach( $mts_options['mts_custom_slider'] as $slide ) : ?>
					<div class="item">
						<?php if($slide['mts_custom_slider_link']) : ?>
						<a href="<?php echo esc_url( $slide['mts_custom_slider_link'] ); ?>">
						<?php endif;
						$img_srcset = wp_get_attachment_image_srcset($slide['mts_custom_slider_image'], 'coupon-slider');
						$img_src = wp_get_attachment_image_src( $slide['mts_custom_slider_image'], 'coupon-slider' );
						?>
						<img src="<?php echo $img_src[0]; ?>" srcset="<?php echo $img_srcset; ?>" />
						<?php
						if($slide['mts_custom_slider_link']) : ?>
						</a>
						<?php endif; ?>
					</div>
					<?php $nav.= '<div class="slider-nav-item">';
						if ( !empty( $slide['mts_custom_slider_title'] ) ) $nav.= '<h2 class="slider-title">'.esc_html( $slide['mts_custom_slider_title'] ).'</h2>';
						$nav.= '</div>';
				endforeach; ?>
			</div><!-- .primary-slider -->
			<?php echo '<div id="slider-nav">'.$nav.'</div>'; ?>
		</div><!-- .primary-slider-container -->
	</section>

<?php } ?>