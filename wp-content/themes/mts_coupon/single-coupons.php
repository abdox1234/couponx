<?php
$mts_options = get_option(MTS_THEME_NAME);
get_header();

$img = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'full' );

if( isset($mts_options['mts_carousel_pages']['coupon-single']) == 1 ) : ?>
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

	<article class="<?php mts_article_class(); ?>">
		<div id="content_box" class="coupon-single">
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>

					<?php $k = 0;
					$coupon_extra_rewards = get_post_meta( get_the_ID(), 'mts_coupon_extra_rewards', true );
					$coupon_type = get_post_meta( get_the_ID(), 'mts_coupon_button_type', true );
					$coupon_deal_URL = get_post_meta( get_the_ID(), 'mts_coupon_deal_URL', true );
					$coupon_code = get_post_meta( get_the_ID(), 'mts_coupon_code', true );
					$coupon_open_in = 'new';//get_post_meta( get_the_ID(), 'mts_coupon_open', true );

					// Single post parts ordering
					if ( isset( $mts_options['mts_coupon_single_post_layout'] ) && is_array( $mts_options['mts_coupon_single_post_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_coupon_single_post_layout'] ) ) {
						$single_post_parts = $mts_options['mts_coupon_single_post_layout']['enabled'];
					} else {
						$single_post_parts = array( 'coupon-content' => 'coupon-content', 'coupon-recent' => 'coupon-recent', 'coupon-related' => 'coupon-related', 'coupon-author' => 'coupon-author', 'coupon-subscribe' => 'coupon-subscribe' );
					}
					foreach( $single_post_parts as $part => $label ) {
						switch ($part) {
							case 'coupon-content':
								?>
								<div class="single_post">
									<?php echo mts_get_coupon_featured_wrapper(get_the_ID()); ?>
									<div class="right-content">
										<header>
											<h2 class="title front-view-title"><?php the_title(); ?></h2>
											<?php if( !empty( $coupon_extra_rewards ) ) : ?>
												<div class="coupon_extra_rewards"><?php echo $coupon_extra_rewards; ?></div>
											<?php endif; ?>
											<?php mts_the_postinfo('coupon-single'); ?>
										</header>
										<div class="front-view-content">
											<?php the_content(); ?>
										</div>
									</div>
								</div><!--.single_post-->
								<?php
							break;

							case 'coupon-recent':
								mts_coupon_recent_posts();
							break;

							case 'coupon-related':
								mts_coupon_related_posts();
							break;

							case 'coupon-author':
								?>
								<div class="postauthor coupon-postauthor">
									<h4><?php _e('About The Author', 'coupon' ); ?></h4>
									<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '100' );  } ?>
									<h5 class="vcard author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="fn"><?php the_author_meta( 'display_name' ); ?></a></h5>
									<p><?php the_author_meta('description') ?></p>
									<div class="author-posts">
										<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title=""><?php _e('Offers by ', 'myportfolio' ) . the_author_meta( 'display_name' ); ?></a>
									</div>
								</div>
								<?php
							break;

							case 'coupon-subscribe':
								if ( is_active_sidebar( 'widget-subscribe' ) ) { ?>
								<div class="subscribe-container coupon-subscribe">
									<div class="container clearfix">
										<?php dynamic_sidebar( 'widget-subscribe' ); ?>
									</div>
								</div><!-- .container -->
								<?php
								}
							break;

							case 'coupon-tags':
								?>
								<?php mts_the_tags('<div class="tags"><span class="tagtext">'.__('Tags', 'coupon' ).':</span>',', ','</div>', 'mts_coupon_tag') ?>
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
