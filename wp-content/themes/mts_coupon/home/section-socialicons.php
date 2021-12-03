<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<section id="promote" class="promote clearfix">
	<div class="container">
		<?php if( $mts_options['mts_social_icons_title'] ) : ?>
			<h3 class="promote-title" style="color:<?php echo $mts_options['mts_social_icons_title_color']; ?>"><?php echo $mts_options['mts_social_icons_title']; ?></h3>
		<?php endif; ?>
		<div class="promote-social">
			<?php foreach( $mts_options['mts_social_icons'] as $social_icon ) : ?>
				<?php if( ! empty( $social_icon['mts_social_icon'] ) && isset( $social_icon['mts_social_icon'] ) && ! empty( $social_icon['mts_social_icon_link'] )) : ?>
				<a href="<?php print $social_icon['mts_social_icon_link'] ?>" class="promote-<?php print $social_icon['mts_social_icon'] ?>"><span class="fa fa-<?php print $social_icon['mts_social_icon'] ?>"></span></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>