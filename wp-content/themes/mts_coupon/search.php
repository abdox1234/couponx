<?php
/**
 * The template for displaying search results pages.
 */
$mts_options = get_option(MTS_THEME_NAME);
get_header(); ?>

<div id="page">
	<div class="article">
		<div id="content_box">
			<h1 class="postsby">
				<span><?php _e("Search Results for:", 'coupon' ); ?></span> <?php the_search_query(); ?>
			</h1>
			<?php $j = 0; if (have_posts()) : while (have_posts()) : the_post(); ?>
				<article class="latestPost excerpt  <?php echo (++$j % 3 == 0) ? 'last' : ''; ?>">
					<?php mts_archive_post(); ?>
				</article><!--.post excerpt-->
			<?php endwhile; else: ?>
				<div class="no-results">
					<h2><?php _e('We apologize for any inconvenience, please hit back on your browser or use the search form below.', 'coupon' ); ?></h2>
					<?php get_search_form(); ?>
				</div><!--noResults-->
			<?php endif; ?>

			<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
				<?php mts_pagination(); ?>
			<?php } ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>