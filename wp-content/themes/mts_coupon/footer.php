<?php
/**
 * The template for displaying the footer.
 */
$mts_options = get_option(MTS_THEME_NAME);

// default = 3
$first_footer_num  = empty($mts_options['mts_first_footer_num']) ? 3 : $mts_options['mts_first_footer_num'];
?>
	</div><!--#page-->
	<footer id="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
		<div class="container">
			<?php if ($mts_options['mts_first_footer']) : ?>
				<div class="footer-widgets first-footer-widgets widgets-num-<?php echo $first_footer_num; ?>">
				<?php
				for ( $i = 1; $i <= $first_footer_num; $i++ ) {
					$sidebar = ( $i == 1 ) ? 'footer-first' : 'footer-first-'.$i;
					$class = ( $i == $first_footer_num ) ? 'f-widget last f-widget-'.$i : 'f-widget f-widget-'.$i;
					?>
					<div class="<?php echo $class;?>">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $sidebar ) ) : ?><?php endif; ?>
					</div>
					<?php
				}
				?>
				</div><!--.first-footer-widgets-->
			<?php endif; ?>

			<?php if ( $mts_options['mts_footer_text_section'] || $mts_options['mts_footer_social_icon_section']  ) : ?>
				<div class="footer-info-section clearfix">
					<?php if ( $mts_options['mts_footer_text_section'] ) : ?>
						<div class="info-text">
							<?php if( !empty( $mts_options['mts_footer_text_title'] ) ) : ?>
								<div class="info-title"><?php echo $mts_options['mts_footer_text_title']; ?></div>
							<?php endif; ?>
							<?php if( !empty( $mts_options['mts_footer_text_content'] ) ) : ?>
								<div class="info-content"><?php echo $mts_options['mts_footer_text_content']; ?></div>
							<?php endif; ?>
						</div><!--.info-text-->
					<?php endif; ?>
					<?php if ( $mts_options['mts_footer_social_icon_section'] && !empty( $mts_options['mts_footer_social'] ) ) : ?>
						<div class="footer-social-icons">
							<?php if( $mts_options['mts_footer_title'] ) : ?>
								<div class="footer-title"><?php echo $mts_options['mts_footer_title']; ?></div>
							<?php endif; ?>
							<div class="footer-social">
								<?php foreach( $mts_options['mts_footer_social'] as $footer_icons ) : ?>
									<?php if( ! empty( $footer_icons['mts_footer_social_icon'] ) && isset( $footer_icons['mts_footer_social_icon'] ) && ! empty( $footer_icons['mts_footer_social_icon_link'] )) : ?>
										<a href="<?php print $footer_icons['mts_footer_social_icon_link'] ?>" class="footer-<?php print $footer_icons['mts_footer_social_icon'] ?>"><span class="fa fa-<?php print $footer_icons['mts_footer_social_icon'] ?>"></span></a>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div><!--.footer-social-icons-->
					<?php endif; ?>
				</div><!--.footer-info-section-->
			<?php endif; ?>

		</div><!--.container-->
		<div class="copyrights">
			<div class="container">
				<?php mts_copyrights_credit(); ?>
			</div><!--.container-->
		</div><!--.copyrights-->
	</footer><!--#site-footer-->
</div><!--.main-container-->
<?php mts_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>