<?php $mts_options = get_option(MTS_THEME_NAME);
if ( !empty( $mts_options['mts_store_group'] ) ) { ?>

	<section id="stores" class="popular-store-container" style="<?php echo mts_get_background_styles( 'mts_store_background' ); ?>">
		<div class="container clearfix">
			<?php if( isset( $mts_options['mts_store_title'] ) && !empty( $mts_options['mts_store_title'] ) ) : ?>
			<h3 class="featured-category-title"><?php echo $mts_options['mts_store_title']; ?></h3>
			<?php endif; ?>
			<div class="popular-store clearfix">
				<ul>
					<?php $i = 1;
					foreach ( $mts_options['mts_store_group'] as $section ) { ?>
						<li class="popular-cat popular-cat-<?php echo $i; $i++; ?>">
							<div class="cat-img">
								<div class="cat-caption">
									<?php
										echo '<a href="' . $section['mts_store_item_link'] . '" title="' . $section['mts_store_item_link'] . '">';
										echo '<div class="post-count">'.$section['mts_store_item_hover_text'];
										echo '</div></a>';
									?>
								</div>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>

		</div>
	</section>

<?php } ?>