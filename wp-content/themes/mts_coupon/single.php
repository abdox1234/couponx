<?php
/**
 * The template for displaying all single posts.
 */
$mts_options = get_option(MTS_THEME_NAME);
get_header();

if( isset($mts_options['mts_carousel_pages']['blog-single']) == 1 ) : ?>
	<div id="inner">
		<?php get_template_part('home/section', 'carousel'); ?>
	</div>
<?php endif; ?>

<div id="page" class="<?php mts_single_page_class(); ?>">
	<?php
	if ( $mts_options['mts_breadcrumb'] == '1' ) {
		mts_the_breadcrumb();
	}
	?>

	<?php $header_animation = mts_get_post_header_effect(); ?>
	<?php if ( 'parallax' === $header_animation ) {?>
		<?php if (mts_get_thumbnail_url()) : ?>
			<div id="parallax" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>></div>
		<?php endif; ?>
	<?php } else if ( 'zoomout' === $header_animation ) {?>
		 <?php if (mts_get_thumbnail_url()) : ?>
			<div id="zoom-out-effect"><div id="zoom-out-bg" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>></div></div>
		<?php endif; ?>
	<?php } ?>

	<article class="<?php mts_article_class(); ?>">
		<div id="content_box" >
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>
					
					<?php
					// Single post parts ordering
					if ( isset( $mts_options['mts_single_post_layout'] ) && is_array( $mts_options['mts_single_post_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_single_post_layout'] ) ) {
						$single_post_parts = $mts_options['mts_single_post_layout']['enabled'];
					} else {
						$single_post_parts = array( 'content' => 'content', 'related' => 'related', 'author' => 'author' );
					}
					foreach( $single_post_parts as $part => $label ) { 
						switch ($part) {
							case 'content':
								?>
								<div class="single_post">
									<header>
										<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
										<?php mts_the_postinfo( 'single' ); ?>
									</header><!--.headline_area-->
									<div class="post-single-content box mark-links entry-content">
										<?php // Top Ad Code ?>
										<?php if ($mts_options['mts_posttop_adcode'] != '') { ?>
											<?php $toptime = $mts_options['mts_posttop_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$toptime day")), get_the_time("Y-m-d") ) >= 0) { ?>
												<div class="topad">
													<?php echo do_shortcode($mts_options['mts_posttop_adcode']); ?>
												</div>
											<?php } ?>
										<?php } ?>

										<?php // Top Social Share ?>
										<?php if (isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] == 'top') mts_social_buttons(); ?>

										<?php // Content ?>
										<div class="thecontent">
											<?php the_content(); ?>
										</div>

										<?php // Single Pagination ?>
										<?php wp_link_pages(array('before' => '<div class="pagination">', 'after' => '</div>', 'link_before'  => '<span class="current"><span class="currenttext">', 'link_after' => '</span></span>', 'next_or_number' => 'next_and_number', 'nextpagelink' => '<i class="fa fa-angle-right"></i>', 'previouspagelink' => '<i class="fa fa-angle-left"></i>', 'pagelink' => '%','echo' => 1 )); ?>

										<?php // Bottom Ad Code ?>
										<?php if ($mts_options['mts_postend_adcode'] != '') { ?>
											<?php $endtime = $mts_options['mts_postend_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$endtime day")), get_the_time("Y-m-d") ) >= 0) { ?>
												<div class="bottomad">
													<?php echo do_shortcode($mts_options['mts_postend_adcode']); ?>
												</div>
											<?php } ?>
										<?php } ?>

										<?php // Bottom Social Share ?>
										<?php if (isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] !== 'top') mts_social_buttons(); ?>
									</div><!--.post-single-content-->
								</div><!--.single_post-->
								<?php
							break;

							case 'tags':
								?>
								<?php mts_the_tags('<div class="tags"><span class="tagtext">'.__('Tags', 'coupon' ).':</span>',', ') ?>
								<?php
							break;

							case 'related':
								mts_related_posts();
							break;

							case 'author':
								?>
								<div class="postauthor">
									<h4><?php _e('About The Author', 'coupon' ); ?></h4>
									<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '100' );  } ?>
									<h5 class="vcard author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="fn"><?php the_author_meta( 'display_name' ); ?></a></h5>
									<p><?php the_author_meta('description') ?></p>
									<div class="author-posts">
										<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title=""><?php _e('Posts by ', 'myportfolio' ) . the_author_meta( 'display_name' ); ?></a>
									</div>
								</div>
								<?php
							break;
						}
					}
					?>
				</div><!--.g post-->
				<?php comments_template( '', true ); ?>
			<?php endwhile; /* end loop */ ?>
		</div>
	</article>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>
