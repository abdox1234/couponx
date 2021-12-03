<?php
/**
 * The template for displaying all pages.
 *
 * Other pages can use a different template by creating a file following any of these format:
 * - page-$slug.php
 * - page-$id.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
$mts_options = get_option(MTS_THEME_NAME);

get_header(); ?>

<div id="page" class="<?php mts_single_page_class(); ?>">
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
					<div class="single_page">
						<?php
						if ( $mts_options['mts_breadcrumb'] == '1' ) {
							mts_the_breadcrumb();
						}
						?>
						<header>
							<h1 class="title entry-title"><?php the_title(); ?></h1>
						</header>
						<div class="post-content box mark-links entry-content">
							<?php if (!empty($mts_options['mts_social_buttons_on_pages']) && isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] == 'top') mts_social_buttons(); ?>

							<?php the_content(); ?>
							<?php wp_link_pages(array('before' => '<div class="pagination">', 'after' => '</div>', 'link_before'  => '<span class="current"><span class="currenttext">', 'link_after' => '</span></span>', 'next_or_number' => 'next_and_number', 'nextpagelink' => __('Next', 'coupon' ), 'previouspagelink' => __('Previous', 'coupon' ), 'pagelink' => '%','echo' => 1 )); ?>
							
							<?php if (!empty($mts_options['mts_social_buttons_on_pages']) && isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] !== 'top') mts_social_buttons(); ?>
						</div><!--.post-content box mark-links-->
					</div>
				</div>
				<?php comments_template( '', true ); ?>
			<?php endwhile; ?>
		</div>
	</article>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>