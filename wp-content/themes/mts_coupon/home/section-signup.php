<?php $mts_options = get_option(MTS_THEME_NAME);
if ( !empty( $mts_options['mts_signup_title']) || $mts_options['mts_signup_button'] == 1 ) { ?>

	<section id="signup" class="signup clearfix">
		<div class="container">
			<?php if( $mts_options['mts_signup_title'] ) : ?>
				<div class="signup-title"><?php echo $mts_options['mts_signup_title']; ?></div>
			<?php endif; ?>
			<?php if( $mts_options['mts_signup_button'] == 1 && !empty( $mts_options['mts_signup_button_text'] ) ) : ?>
				<div class="signup-button">
					<?php if( $mts_options['mts_signup_button_url'] ) : ?>
					<a href="<?php echo $mts_options['mts_signup_button_url']; ?>" style="background-color: <?php echo $mts_options['mts_signup_button_background']; ?>">
					<?php endif; ?>
						<?php echo $mts_options['mts_signup_button_text']; ?>						
					<?php if( $mts_options['mts_signup_button_url'] ) : ?>
					</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

<?php } ?>