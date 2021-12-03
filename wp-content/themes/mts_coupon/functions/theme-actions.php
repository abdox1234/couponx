<?php
$mts_options = get_option(MTS_THEME_NAME);
if ( ! function_exists( 'mts_meta' ) ) {
	/**
	 * Display necessary tags in the <head> section.
	 */
	function mts_meta(){
		global $mts_options, $post;
		?>

		<?php if ( !empty( $mts_options['mts_favicon'] ) ) { ?>
			<link rel="icon" href="<?php echo esc_url( $mts_options['mts_favicon'] ); ?>" type="image/x-icon" />
		<?php } elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) { ?>
			<?php printf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( get_site_icon_url( 32 ) ) ); ?>
			<?php sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( get_site_icon_url( 192 ) ) ); ?>
		<?php } ?>

		<?php if ( !empty( $mts_options['mts_metro_icon'] ) ) { ?>
			<!-- IE10 Tile.-->
			<meta name="msapplication-TileColor" content="#FFFFFF">
			<meta name="msapplication-TileImage" content="<?php echo esc_url( $mts_options['mts_metro_icon'] ); ?>">
		<?php } elseif ( function_exists( 'has_site_icon' ) && has_site_icon( ) ) { ?>
			<?php printf( '<meta name="msapplication-TileImage" content="%s">', esc_url( get_site_icon_url( 270 ) ) ); ?>
		<?php } ?>

		<?php if ( !empty( $mts_options['mts_touch_icon'] ) ) { ?>
			<!--iOS/android/handheld specific -->
			<link rel="apple-touch-icon-precomposed" href="<?php echo esc_url( $mts_options['mts_touch_icon'] ); ?>" />
		<?php } elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) { ?>
			<?php printf( '<link rel="apple-touch-icon-precomposed" href="%s">', esc_url( get_site_icon_url( 180 ) ) ); ?>
		<?php } ?>

		<?php if ( ! empty( $mts_options['mts_responsive'] ) ) { ?>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<?php } ?>

		<?php if($mts_options['mts_prefetching'] == '1') { ?>
			<?php if (is_front_page()) { ?>
				<?php $my_query = new WP_Query('posts_per_page=1'); while ($my_query->have_posts()) : $my_query->the_post(); ?>
				<link rel="prefetch" href="<?php the_permalink(); ?>">
				<link rel="prerender" href="<?php the_permalink(); ?>">
				<?php endwhile; wp_reset_postdata(); ?>
			<?php } elseif (is_singular()) { ?>
				<link rel="prefetch" href="<?php echo esc_url( home_url() ); ?>">
				<link rel="prerender" href="<?php echo esc_url( home_url() ); ?>">
			<?php } ?>
		<?php } ?>
<?php
	}
}

if ( ! function_exists( 'mts_head' ) ){
	/**
	 * Display header code from Theme Options.
	 */
	function mts_head() {
		global $mts_options;
		if(isset($mts_options['mts_header_code'])) {
			echo $mts_options['mts_header_code'];
		}
	}
}
add_action('wp_head', 'mts_head');

if ( ! function_exists( 'mts_copyrights_credit' ) ) {
	/**
	 * Display the footer copyright.
	 */
	function mts_copyrights_credit() {
	global $mts_options;
?>
<!--start copyrights-->
<div class="row" id="copyright-note">
<?php $copyright_text = '<a href=" ' . esc_url( trailingslashit( home_url() ) ). '" title=" ' . get_bloginfo('description') . '">' . get_bloginfo('name') . '</a> Copyright &copy; ' . date("Y") . '.'; ?>
<span><?php echo apply_filters( 'mts_copyright_content', $copyright_text ); ?></span>
<div class="to-top"><?php echo $mts_options['mts_copyrights']; ?></div>
</div>
<!--end copyrights-->
<?php }
}

if ( ! function_exists( 'mts_footer' ) ) {
	/**
	 * Display the analytics code in the footer.
	 */
	function mts_footer() {
	global $mts_options;
?>
	<?php if ( isset( $mts_options['mts_analytics_code'] ) && $mts_options['mts_analytics_code'] != '') { ?>
	<!--start footer code-->
		<?php echo $mts_options['mts_analytics_code']; ?>
	<!--end footer code-->
	<?php }
	}
}

// Last item in the breadcrumbs
if ( ! function_exists( 'get_itemprop_3' ) ) {
	function get_itemprop_3( $title = '', $position = '2' ) {
		echo '<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		echo '<span itemprop="name">' . $title . '</span>';
		echo '<meta itemprop="position" content="' . $position . '" />';
		echo '</div>';
	}
}
if ( ! function_exists( 'mts_the_breadcrumb' ) ) {
	/**
	 * Display the breadcrumbs.
	 */
	function mts_the_breadcrumb() {
		if ( is_front_page() ) {
				return;
		}
		if ( function_exists( 'rank_math_the_breadcrumbs' ) && RankMath\Helper::get_settings( 'general.breadcrumbs' ) ) {
			rank_math_the_breadcrumbs();
			return;
		}
		$seperator = '<div><i class="fa fa-angle-right"></i></div>';
		echo '<div class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">';
		echo '<div itemprop="itemListElement" itemscope
	      itemtype="https://schema.org/ListItem" class="root"><a href="';
		echo esc_url( home_url() );
		echo '" itemprop="item"><span itemprop="name">' . esc_html__( 'Home', 'sociallyviral' );
		echo '</span><meta itemprop="position" content="1" /></a></div>' . $seperator;
		if ( is_single() ) {
			$categories = get_the_category();
			if ( $categories ) {
				$level         = 0;
				$hierarchy_arr = array();
				foreach ( $categories as $cat ) {
					$anc       = get_ancestors( $cat->term_id, 'category' );
					$count_anc = count( $anc );
					if ( 0 < $count_anc && $level < $count_anc ) {
						$level         = $count_anc;
						$hierarchy_arr = array_reverse( $anc );
						array_push( $hierarchy_arr, $cat->term_id );
					}
				}
				if ( empty( $hierarchy_arr ) ) {
					$category = $categories[0];
					echo '<div itemprop="itemListElement" itemscope
				      itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $category->name ) . '</span><meta itemprop="position" content="2" /></a></div>' . $seperator;
				} else {
					foreach ( $hierarchy_arr as $cat_id ) {
						$category = get_term_by( 'id', $cat_id, 'category' );
						echo '<div itemprop="itemListElement" itemscope
					      itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $category->name ) . '</span><meta itemprop="position" content="2" /></a></div>' . $seperator;
					}
				}
				get_itemprop_3( get_the_title(), '3' );
			} else {
				get_itemprop_3( get_the_title() );
			}
		} elseif ( is_page() ) {
			$parent_id = wp_get_post_parent_id( get_the_ID() );
			if ( $parent_id ) {
				$breadcrumbs = array();
				while ( $parent_id ) {
					$page          = get_page( $parent_id );
					$breadcrumbs[] = '<div itemprop="itemListElement" itemscope
				      itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_permalink( $page->ID ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title( $page->ID ) ) . '</span><meta itemprop="position" content="2" /></a></div>' . $seperator;
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse( $breadcrumbs );
				foreach ( $breadcrumbs as $crumb ) { echo $crumb; }
				get_itemprop_3( get_the_title(), 3 );
			} else {
				get_itemprop_3( get_the_title() );
			}
		} elseif ( is_category() ) {
			global $wp_query;
			$cat_obj       = $wp_query->get_queried_object();
			$this_cat_id   = $cat_obj->term_id;
			$hierarchy_arr = get_ancestors( $this_cat_id, 'category' );
			if ( $hierarchy_arr ) {
				$hierarchy_arr = array_reverse( $hierarchy_arr );
				foreach ( $hierarchy_arr as $cat_id ) {
					$category = get_term_by( 'id', $cat_id, 'category' );
					echo '<div itemprop="itemListElement" itemscope
				      itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $category->name ) . '</span><meta itemprop="position" content="2" /></a></div>' . $seperator;
				}
			}
			get_itemprop_3( single_cat_title( '', false ) );
		} elseif ( is_author() ) {
			if ( get_query_var( 'author_name' ) ) :
				$curauth = get_user_by( 'slug', get_query_var( 'author_name' ) );
			else :
				$curauth = get_userdata( get_query_var( 'author' ) );
			endif;
			get_itemprop_3( esc_html( $curauth->nickname ) );
		} elseif ( is_search() ) {
			get_itemprop_3( get_search_query() );
		} elseif ( is_tag() ) {
			get_itemprop_3( single_tag_title( '', false ) );
		}
		echo '</div>';
	}
}
if ( ! function_exists( 'mts_the_category' ) ) {
/**
 * Display schema-compliant the_category()
 *
 * @param string $separator
 */
	function mts_the_category( $separator = ', ' ) {
		$categories = get_the_category();
		$count = count($categories);
		foreach ( $categories as $i => $category ) {
			echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . sprintf( __( "View all posts in %s", 'coupon' ), esc_attr( $category->name ) ) . '">' . esc_html( $category->name ).'</a>';
			if ( $i < $count - 1 )
				echo $separator;
		}
	}
}
if ( ! function_exists( 'mts_the_tags' ) ) {
/**
 * Display schema-compliant the_tags()
 *
 * @param string $before
 * @param string $sep
 * @param string $after
 */
	function mts_the_tags($before = '', $sep = ', ', $after = '</div>', $tax = 'post_tag') {
		if ( empty( $before ) ) {
			$before = '<div class="tags border-bottom">'.__('Tags: ', 'coupon' );
		}
		$tags = get_the_terms( get_the_ID(), $tax );
		if (empty( $tags ) || is_wp_error( $tags ) ) {
			return;
		}
		$tag_links = array();
		foreach ($tags as $tag) {
			$link = get_term_link($tag->term_id, $tax );
			$tag_links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $tag->name . '</a>';
		}
		echo $before.join($sep, $tag_links).$after;
	}
}
if (!function_exists('mts_pagination')) {
	/**
	 * Display the pagination.
	 *
	 * @param string $pages
	 * @param int $range
	 */
	function mts_pagination( $pages = '', $range = 3, $pagenavigation_type = 'mts_pagenavigation_type' ) {
		$mts_options = get_option(MTS_THEME_NAME);
		if (isset($mts_options[$pagenavigation_type]) && $mts_options[$pagenavigation_type] == '1' ) { // numeric pagination
			the_posts_pagination( array(
				'mid_size' => 3,
				'prev_text' => '<i class="fa fa-angle-left"></i>',
				'next_text' => '<i class="fa fa-angle-right"></i>',
			) );
		} else { // traditional or ajax pagination
			?>
			<div class="pagination pagination-previous-next">
			<ul>
				<li class="nav-previous"><?php next_posts_link( '<i class="fa fa-angle-left"></i>' ); ?></li>
				<li class="nav-next"><?php previous_posts_link( '<i class="fa fa-angle-right"></i>' ); ?></li>
			</ul>
			</div>
			<?php
		}
	}
}

if (!function_exists('mts_related_posts')) {
	/**
	 * Display the related posts.
	 */
	function mts_related_posts() {
		$post_id = get_the_ID();
		$mts_options = get_option(MTS_THEME_NAME);
		//if(!empty($mts_options['mts_related_posts'])) { ?>
			<!-- Start Related Posts -->
			<?php
			$empty_taxonomy = false;
			if (empty($mts_options['mts_related_posts_taxonomy']) || $mts_options['mts_related_posts_taxonomy'] == 'tags') {
				// related posts based on tags
				$tags = get_the_tags($post_id);
				if (empty($tags)) {
					$empty_taxonomy = true;
				} else {
					$tag_ids = array();
					foreach($tags as $individual_tag) {
						$tag_ids[] = $individual_tag->term_id;
					}
					$args = array( 'tag__in' => $tag_ids,
						'post__not_in' => array($post_id),
						'posts_per_page' => isset( $mts_options['mts_related_postsnum'] ) ? $mts_options['mts_related_postsnum'] : 3,
						'ignore_sticky_posts' => 1,
						'orderby' => 'rand'
					);
				}
			 } else {
				// related posts based on categories
				$categories = get_the_category($post_id);
				if (empty($categories)) {
					$empty_taxonomy = true;
				} else {
					$category_ids = array();
					foreach($categories as $individual_category)
						$category_ids[] = $individual_category->term_id;
					$args = array( 'category__in' => $category_ids,
						'post__not_in' => array($post_id),
						'posts_per_page' => $mts_options['mts_related_postsnum'],
						'ignore_sticky_posts' => 1,
						'orderby' => 'rand'
					);
				}
			 }
			if (!$empty_taxonomy) {
			$my_query = new WP_Query( apply_filters( 'mts_related_posts_query_args', $args, $mts_options['mts_related_posts_taxonomy'] ) ); if( $my_query->have_posts() ) {
				echo '<div class="related-posts">';
				echo '<h4>'.__('Related Posts', 'coupon' ).'</h4>';
				echo '<div class="clear">';
				$posts_per_row = 2;
				$j = 0;
				while( $my_query->have_posts() ) { $my_query->the_post(); ?>
				<article class="latestPost excerpt  <?php echo (++$j % $posts_per_row == 0) ? 'last' : ''; ?>">
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" id="featured-thumbnail">
						<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail('coupon-widgetthumb',array('title' => '')); echo '</div>'; ?>
					</a>
					<header>
						<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
					</header>
					<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'related-post-review-wrapper'); ?>
				</article><!--.post.excerpt-->
				<?php } echo '</div></div>'; }} wp_reset_postdata(); ?>
			<!-- .related-posts -->
		<?php //}
	}
}

/*------------[ Coupon Recent Posts ]-------------*/
if (!function_exists('mts_coupon_recent_posts')) {
	/**
	 * Display the Recent posts.
	 */
	function mts_coupon_recent_posts() {
		$post_id = get_the_ID();
		$mts_options = get_option(MTS_THEME_NAME);
		$postsnum = 2;
		if ( !empty($mts_options['mts_coupon_recent_postsnum'])) {
			$postsnum = $mts_options['mts_coupon_recent_postsnum'];
		}
		$args = array(
			'post_type' => 'coupons',
			'posts_per_page' => $postsnum,
			'orderby' => 'date',
			'order' => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows' => true,
			'post_status' => 'publish',
			'post__not_in' => array($post_id),
		);
		$my_query = new WP_Query( $args ); if( $my_query->have_posts() ) {
			echo '<div class="related-posts coupon-recent-posts">';
			echo '<h4>'.__('Recent Offers', 'coupon' ).'</h4>';
			echo '<div class="clear">';
			$posts_per_row = 2;
			$j = 0;
			while( $my_query->have_posts() ) { $my_query->the_post(); ?>
			<article class="latestPost excerpt  <?php echo (++$j % $posts_per_row == 0) ? 'last' : ''; ?>">
				<?php mts_coupon_thumb( 'coupon-image' ); ?>
				<header>
					<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
				</header>
			</article><!--.post.excerpt-->
		<?php } echo '</div></div>'; } wp_reset_postdata();
	}
}

/*------------[ Coupon Related Posts ]-------------*/
if (!function_exists('mts_coupon_related_posts')) {
	/**
	 * Display the Coupon related posts.
	 */
	function mts_coupon_related_posts() {
		$post_id = get_the_ID();
		$mts_options = get_option(MTS_THEME_NAME);
		$terms = wp_get_object_terms( $post_id,  'mts_coupon_categories' );
		if ( empty($terms) || !is_array($terms) ) {
			return;
		}
		$args = array(
			'post_type' => 'coupons',
			'posts_per_page' => $mts_options['mts_coupon_related_postsnum'],
			'orderby' => 'rand',
			'tax_query' => array(
				array(
					'taxonomy' => 'mts_coupon_categories',
					'field' => 'slug',
					'terms' => $terms[0]->slug,
				)
			),
			'post__not_in' => array ($post_id),
		);

		$my_query = new WP_Query( $args ); if( $my_query->have_posts() ) {
			$coupon_button_type = get_post_meta( get_the_ID(), 'mts_coupon_button_type', 1 );
			echo '<div class="coupon-related-posts clearfix">';
			echo '<h4>'.__('Related Offers', 'coupon' ).'</h4>';
			echo '<div class="clear">';
			$posts_per_row = 3;
			$j = 0;
			while( $my_query->have_posts() ) { $my_query->the_post(); ?>
			<article class="latestPost excerpt coupon-related-post <?php echo (++$j % $posts_per_row == 0) ? 'last' : ''; ?>">
				<?php if( has_post_thumbnail() ) : ?>
					<div class="tab-post-logo">
						<img src="<?php the_post_thumbnail_url('full'); ?>" width="auto" height="45" alt="<?php the_title(); ?>">
					</div>
				<?php endif; ?>
				<p class="tab-text"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
				<?php if( $coupon_button_type == 'deal' ) : ?>
					<a href="<?php the_permalink(); ?>" class="dashed-button"><?php _e('Get this Offer', 'coupon'); ?></a>
				<?php elseif( $coupon_button_type == 'coupon' ) : ?>
					<a href="<?php the_permalink(); ?>" class="dashed-button"><?php _e('Show Coupon Code', 'coupon'); ?></a>
				<?php endif; ?>
			</article><!--.post.excerpt-->
		<?php } echo '</div></div>'; } wp_reset_postdata();
	}
}

/*------------[ Post Meta Info ]-------------*/
if ( ! function_exists('mts_the_postinfo' ) ) {
	/**
	 * Display the post info block.
	 *
	 * @param string $section
	 */
	function mts_the_postinfo( $section = 'blog' ) {
		$mts_options = get_option( MTS_THEME_NAME );

		if ( in_array( $section, array('blog', 'single') ) ) {

			$opt_key = 'mts_'.$section.'_headline_meta_info';

			if ( isset( $mts_options[ $opt_key ] ) && is_array( $mts_options[ $opt_key ] ) && array_key_exists( 'enabled', $mts_options[ $opt_key ] ) && $section != 'coupon' ) {
				$headline_meta_info = $mts_options[ $opt_key ]['enabled'];
			} else {
				$headline_meta_info = array();
			}

			if ( ! empty( $headline_meta_info ) ) { ?>
				<div class="post-info">
					<?php foreach( $headline_meta_info as $key => $meta ) { mts_the_postinfo_item( $key ); } ?>
				</div>
			<?php }

		} else {

			$coupon_opt_key = 'mts_'.$section.'_headline_meta_info';

			// Coupon Post info
			if ( isset( $mts_options[ $coupon_opt_key ] ) && is_array( $mts_options[ $coupon_opt_key ] ) && array_key_exists( 'enabled', $mts_options[ $coupon_opt_key ] ) && $section != 'home' && 'coupons' == get_post_type() ) {
				$coupon_meta_info = $mts_options[ $coupon_opt_key ]['enabled'];
			} else {
				$coupon_meta_info = array();
			}

			if ( ! empty( $coupon_meta_info ) ) { ?>
				<div class="post-info">
					<?php foreach( $coupon_meta_info as $key => $meta ) { mts_coupon_postinfo_item( $key ); } ?>
				</div>
			<?php
			}
		}

		if ( empty($headline_meta_info) && empty($coupon_meta_info) ) {
			return false;
		} else {
			return true;
		}
	}
}
if ( ! function_exists('mts_the_postinfo_item' ) ) {
	/**
	 * Display information of an item.
	 * @param $item
	 */
	function mts_the_postinfo_item( $item, $is_homeposts = '' ) {
		$mts_options = get_option( MTS_THEME_NAME );
		switch ( $item ) {
			case 'author':
			?>
				<span class="theauthor"><?php _e('Posted by ', 'coupon' ); ?><span><?php the_author_posts_link(); ?></span></span>
			<?php
			break;
			case 'date':
			$date_layout = '';
			if($is_homeposts && isset( $mts_options['mts_lp_date_layout'])) {
				$date_layout = $mts_options['mts_lp_date_layout'];
			} elseif(isset( $mts_options['mts_date_layout'])) {
				$date_layout = $mts_options['mts_date_layout'];
			}

			if( $date_layout == 'datebig' && !is_single() ) : ?>
				<span class="thetime date updated">
					<div class="day"><?php echo get_the_time('d'); ?></div>
					<span class="month"><?php echo get_the_time('F') . ','; ?></span>
					<span class="year"><?php echo get_the_time('Y'); ?></span>
				</span>
			<?php else : ?>
				<span class="thetime date updated"><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
			<?php endif; ?>
			<?php
			break;
			case 'category':
			?>
				<span class="thecategory"><?php mts_the_category(', ') ?></span>
			<?php
			break;
			case 'comment':
			?>
				<span class="thecomment"><a href="<?php echo esc_url( get_comments_link() ); ?>" itemprop="interactionCount"><?php comments_number(__('No Responses', 'coupon' ), __('One Response', 'coupon' ),  __('% Comments', 'coupon' ) );?></a></span>
			<?php
			break;
		}
	}
}

if ( ! function_exists('mts_coupon_postinfo_item' ) ) {
	/**
	 * Display information of an item.
	 * @param $item
	 */
	function mts_coupon_postinfo_item( $item ) {
		$mts_options = get_option( MTS_THEME_NAME );
		$coupon_expiry_date = get_post_field( 'mts_coupon_expire', get_the_ID() );
		$coupon_expiry_time = get_post_field( 'mts_coupon_expire_time', get_the_ID() );
		$coupon_people_used = get_post_meta( get_the_ID(), 'mts_coupon_people_used', true );
		switch ( $item ) {
			case 'used':
			?>
				<?php if( isset( $coupon_people_used ) && !empty( $coupon_people_used ) ) : ?>
					<span class="peopleused"><?php printf(__(' %d People Used This', 'coupon'), $coupon_people_used); ?></span>
				<?php endif; ?>
			<?php
			break;
			case 'author':
			?>
				<span class="theauthor"><?php _e('Posted by ', 'coupon' ); ?><span><?php the_author_posts_link(); ?></span></span>
			<?php
			break;
			case 'comment':
			?>
				<span class="thecomment"><a href="<?php echo esc_url( get_comments_link() ); ?>" itemprop="interactionCount"><?php comments_number(__('No Responses', 'coupon' ), __('One Response', 'coupon' ),  __('% Comments', 'coupon' ) );?></a></span>
			<?php
			break;
			case 'expire':
			?>
				<?php if( !empty($coupon_expiry_date) ) : ?>
					<span class="expirydate">
					<?php
						$expiry_date = get_gmt_from_date($coupon_expiry_date . ' ' . $coupon_expiry_time);
						$diff = date_create()->diff(date_create($expiry_date));

						if ( $diff->invert ) {
							if ( $diff->days == 1 ) {
								_e('Expired 1 day ago', 'coupon');
							} elseif ( $diff->days != 0 ) {
								printf(__('Expired %d days ago', 'coupon'), $diff->days);
							} else {
								printf(__('Expired %d hours ago', 'coupon'), $diff->h);
							}
						} else {
							if ( $diff->days == 1 ) {
								_e('Expiring in 1 day', 'coupon');
							} elseif ( $diff->days != 0 ) {
								printf(__('Expiring in %d days', 'coupon'), $diff->days);
							} else {
								printf(__('Expiring in %d hours', 'coupon'), $diff->h);
							}
						}

					?>
					</span>
				<?php endif; ?>
			<?php
			break;
		}
	}
}

if (!function_exists('mts_social_buttons')) {
	/**
	 * Display the social sharing buttons.
	 */
	function mts_social_buttons() {
		$mts_options = get_option( MTS_THEME_NAME );
		$buttons = array();

		if ( isset( $mts_options['mts_social_buttons'] ) && is_array( $mts_options['mts_social_buttons'] ) && array_key_exists( 'enabled', $mts_options['mts_social_buttons'] ) ) {
			$buttons = $mts_options['mts_social_buttons']['enabled'];
		}

		if ( ! empty( $buttons ) && isset( $mts_options['mts_social_button_layout'] ) ) {
			if( $mts_options['mts_social_button_layout'] == 'modern' ) { ?>
				<div class="shareit <?php echo $mts_options['mts_social_button_position']; ?>">
					<?php foreach( $buttons as $key => $button ) { mts_social_modern_button( $key ); } ?>
				</div>
			<?php }	else { ?>
				<div class="shareit <?php echo $mts_options['mts_social_button_position']; ?>">
					<?php foreach( $buttons as $key => $button ) { mts_social_button( $key ); } ?>
				</div>
			<?php }
		}
	}
}

if ( ! function_exists('mts_social_button' ) ) {
	/**
	 * Display network-independent sharing buttons.
	 *
	 * @param $button
	 */
	function mts_social_button( $button ) {
		$mts_options = get_option( MTS_THEME_NAME );
		switch ( $button ) {
			case 'facebookshare':
			?>
				<!-- Facebook Share-->
				<span class="share-item facebooksharebtn">
					<div class="fb-share-button" data-layout="button_count"></div>
				</span>
			<?php
			break;
			case 'twitter':
			?>
				<!-- Twitter -->
				<span class="share-item twitterbtn">
					<a href="https://twitter.com/share" class="twitter-share-button" data-via="<?php echo esc_attr( $mts_options['mts_twitter_username'] ); ?>"><?php esc_html_e( 'Tweet', 'coupon' ); ?></a>
				</span>
			<?php
			break;
			case 'gplus':
			?>
				<!-- GPlus -->
				<span class="share-item gplusbtn">
					<g:plusone size="medium"></g:plusone>
				</span>
			<?php
			break;
			case 'facebook':
			?>
				<!-- Facebook -->
				<span class="share-item facebookbtn">
					<div id="fb-root"></div>
					<div class="fb-like" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div>
				</span>
			<?php
			break;
			case 'pinterest':
			?>
				<!-- Pinterest -->
				<span class="share-item pinbtn">
					<a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'large' ); echo $thumb['0']; ?>&description=<?php the_title(); ?>" class="pin-it-button" count-layout="horizontal"><?php esc_html_e( 'Pin It', 'coupon' ); ?></a>
				</span>
			<?php
			break;
			case 'linkedin':
			?>
				<!--Linkedin -->
				<span class="share-item linkedinbtn">
					<script type="IN/Share" data-url="<?php echo esc_url( get_the_permalink() ); ?>"></script>
				</span>
			<?php
			break;
			case 'stumble':
			?>
				<!-- Stumble -->
				<span class="share-item stumblebtn">
					<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumble" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="stumble-icon"><i class="fa fa-stumbleupon"></i></span><span class="stumble-text"><?php _e('Share', 'coupon'); ?></span></a>
				</span>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<span class="share-item reddit">
					<a href="//www.reddit.com/submit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"> <img src="<?php echo get_template_directory_uri().'/images/reddit.png' ?>" alt=<?php _e( 'submit to reddit', 'coupon' ); ?> border="0" /></a>
				</span>
			<?php
			break;
		}
	}
}

if ( ! function_exists('mts_social_modern_button' ) ) {
	/**
	 * Display network-independent sharing buttons.
	 *
	 * @param $button
	 */
	function mts_social_modern_button( $button ) {
		$mts_options = get_option( MTS_THEME_NAME );
		global $post;
		if( is_single() ){
			$imgUrl = $img = '';
			if ( has_post_thumbnail( $post->ID ) ){
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'coupon-featuredfull' );
				$imgUrl = $img[0];
			}
		}
		switch ( $button ) {
			case 'facebookshare':
			?>
				<!-- Facebook -->
				<span class="modern-share-item modern-facebooksharebtn">
					<a href="//www.facebook.com/share.php?m2w&s=100&p[url]=<?php echo urlencode(get_permalink()); ?>&p[images][0]=<?php echo urlencode($imgUrl[0]); ?>&p[title]=<?php echo urlencode(get_the_title()); ?>&u=<?php echo urlencode( get_permalink() ); ?>&t=<?php echo urlencode( get_the_title() ); ?>" class="facebook" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-facebook"></i><?php _e('Share', 'coupon'); ?></a>
				</span>
			<?php
			break;
			case 'twitter':
			?>
				<!-- Twitter -->
				<span class="modern-share-item modern-twitterbutton">
					<?php $via = '';
					if( $mts_options['mts_twitter_username'] ) {
						$via = '&via='. $mts_options['mts_twitter_username'];
					} ?>
					<a href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode(get_permalink()); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo urlencode(get_permalink()); ?><?php echo $via; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-twitter"></i> <?php _e('Tweet', 'coupon'); ?></a>
				</span>
			<?php
			break;
			case 'gplus':
			?>
				<!-- GPlus -->
				<span class="modern-share-item modern-gplusbtn">
					<!-- <g:plusone size="medium"></g:plusone> -->
					<a href="//plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" class="google-plus" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-google-plus"></i><?php _e('Share', 'coupon'); ?></a>
				</span>
			<?php
			break;
			case 'facebook':
			?>
				<!-- Facebook -->
				<span class="modern-share-item facebookbtn">
					<div id="fb-root"></div>
					<div class="fb-like" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div>
				</span>
			<?php
			break;
			case 'pinterest':
				global $post;
			?>
				<!-- Pinterest -->
				<?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
				<span class="modern-share-item modern-pinbtn">
					<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php the_title(); ?>" class="pinterest" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-pinterest-p"></i><?php _e('Pin it', 'coupon'); ?></a>
				</span>
			<?php
			break;
			case 'linkedin':
			?>
				<!--Linkedin -->
				<span class="modern-share-item modern-linkedinbtn">
					<a href="//www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo get_the_title(); ?>&source=<?php echo 'url'; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-linkedin"></i><?php _e('Share', 'coupon'); ?></a>
				</span>
			<?php
			break;
			case 'stumble':
			?>
				<!-- Stumble -->
				<span class="modern-share-item modern-stumblebtn">
					<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumble" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-stumbleupon"></i><?php _e('Stumble', 'coupon'); ?></a>
				</span>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<span class="modern-share-item modern-reddit">
					<a href="//www.reddit.com/submit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-reddit-alien"></i><?php _e('Reddit', 'coupon'); ?></a>
				</span>
			<?php
			break;
		}
	}
}

if ( ! function_exists( 'mts_article_class' ) ) {
	/**
	 * Custom `<article>` class name.
	 */
	function mts_article_class() {
		$mts_options = get_option( MTS_THEME_NAME );
		$class = 'article';

		// sidebar or full width
		if ( mts_custom_sidebar() == 'mts_nosidebar' ) {
			$class = 'ss-full-width';
		}

		echo $class;
	}
}

if ( ! function_exists( 'mts_single_page_class' ) ) {
	/**
	 * Custom `#page` class name.
	 */
	function mts_single_page_class() {
		$class = '';

		if ( is_single() || is_page() ) {

			$class = 'single';

			$header_animation = mts_get_post_header_effect();
			if ( !empty( $header_animation )) $class .= ' '.$header_animation;
		}

		echo $class;
	}
}

if ( ! function_exists( 'mts_archive_post' ) ) {
	/**
	 * Display a post of specific layout.
	 *
	 * @param string $layout
	 */
	function mts_archive_post( $layout = '' ) {

		$mts_options = get_option(MTS_THEME_NAME);

		if ( isset( $mts_options[ 'mts_blog_headline_meta_info' ] ) && is_array( $mts_options[ 'mts_blog_headline_meta_info' ] ) && array_key_exists( 'enabled', $mts_options[ 'mts_blog_headline_meta_info' ] ) ) {
			$headline_meta_info = $mts_options[ 'mts_blog_headline_meta_info' ]['enabled'];
		} else {
			$headline_meta_info = array();
		}
		mts_the_postinfo();
		?>
		<div class="right-content<?php echo ( !empty( $headline_meta_info ) ? '' : ' no-postinfo' ); ?>">
			<?php if(has_post_thumbnail()) {
				if( empty( $headline_meta_info ) ) {
					$thumb_size = 'coupon-slider';
				} else {
					$thumb_size = 'coupon-featured';
				} ?>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="post-image post-image-left">
					<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail($thumb_size,array('title' => '')); echo '</div>'; ?>
					<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
				</a>
			<?php } ?>
			<header>
				<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
			</header>
			<?php if (empty($mts_options['mts_full_posts'])) : ?>
				<div class="front-view-content">
					<?php echo mts_excerpt(29); ?>
				</div>
			<?php else : ?>
				<div class="front-view-content full-post">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php }
}
if( !function_exists( 'mts_coupon_thumb' ) ){
	function mts_coupon_thumb( $link_class = '' ) {
		global $mts_options;
		$coupon_featured_text = get_post_meta( get_the_ID(), 'mts_coupon_featured_text', true );
		if ( isset( $mts_options['mts_coupon_thumbnail'] ) && 'image' === $mts_options['mts_coupon_thumbnail'] && has_post_thumbnail() ) {
			$image_size = apply_filters( 'mts_get_featured_image_size', 'coupon-featured-thumb' );
			?>
			<a class="<?php echo esc_attr( $link_class ); ?>" href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
				<div class="coupon-featured coupon-featured-image">
					<?php the_post_thumbnail( $image_size ,array('title' => '')); ?>
				</div>
			</a>
		<?php } else {
			if( !empty( $coupon_featured_text ) ) { ?>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
					<div class="coupon-featured">
						<?php echo $coupon_featured_text; ?>
					</div>
				</a>
			<?php } else { ?>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
					<div class="coupon-featured">
						<?php _e('Great Deal', 'coupon'); ?>
					</div>
				</a>
			<?php }
		}
	}
}

function mts_theme_action( $action = null ) {
    update_option( 'mts__thl', '1' );
    update_option( 'mts__pl', '1' );
}

function mts_theme_activation( $oldtheme_name = null, $oldtheme = null ) { RETURN;
    // Check for Connect plugin version > 1.4
    if ( class_exists('mts_connection') && defined('MTS_CONNECT_ACTIVE') && MTS_CONNECT_ACTIVE ) {
        return;
    }
     $plugin_path = 'mythemeshop-connect/mythemeshop-connect.php';

    // Check if plugin exists
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    if ( ! array_key_exists( $plugin_path, $plugins ) ) {
        // auto-install it
        include_once( ABSPATH . 'wp-admin/includes/misc.php' );
        include_once( ABSPATH . 'wp-admin/includes/file.php' );
        include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
        include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        $skin     = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $plugin_file = 'https://www.mythemeshop.com/mythemeshop-connect.zip';
        $result = $upgrader->install( $plugin_file );
        // If install fails then revert to previous theme
        if ( is_null( $result ) || is_wp_error( $result ) || is_wp_error( $skin->result ) ) {
            switch_theme( $oldtheme->stylesheet );
            return false;
        }
    } else {
        // Plugin is already installed, check version
        $ver = isset( $plugins[$plugin_path]['Version'] ) ? $plugins[$plugin_path]['Version'] : '1.0';
         if ( version_compare( $ver, '2.0.5' ) === -1 ) {
            include_once( ABSPATH . 'wp-admin/includes/misc.php' );
            include_once( ABSPATH . 'wp-admin/includes/file.php' );
            include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
            $skin     = new Automatic_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );

            add_filter( 'pre_site_transient_update_plugins',  'mts_inject_connect_repo', 10, 2 );
            $result = $upgrader->upgrade( $plugin_path );
            remove_filter( 'pre_site_transient_update_plugins', 'mts_inject_connect_repo' );

            // If update fails then revert to previous theme
            if ( is_null( $result ) || is_wp_error( $result ) || is_wp_error( $skin->result ) ) {
                switch_theme( $oldtheme->stylesheet );
                return false;
            }
        }
    }
    $activate = activate_plugin( $plugin_path );
}

function mts_inject_connect_repo( $pre, $transient ) {
    $plugin_file = 'https://www.mythemeshop.com/mythemeshop-connect.zip';

    $return = new stdClass();
    $return->response = array();
    $return->response['mythemeshop-connect/mythemeshop-connect.php'] = new stdClass();
    $return->response['mythemeshop-connect/mythemeshop-connect.php']->package = $plugin_file;

    return $return;
}

add_action( 'wp_loaded', 'mts_maybe_set_constants' );
function mts_maybe_set_constants() {
    if ( ! defined( 'MTS_THEME_S' ) ) {
        mts_set_theme_constants();
    }
}

add_action( 'init', 'mts_nhp_sections_override', -11 );
function mts_nhp_sections_override() {
    define( 'MTS_THEME_INIT', 1 ); RETURN;
    if ( class_exists('mts_connection') && defined('MTS_CONNECT_ACTIVE') && MTS_CONNECT_ACTIVE ) {
        return;
    }
    if ( ! get_option( MTS_THEME_NAME, false ) ) {
    	return;
    }
    add_filter( 'nhp-opts-sections', '__return_empty_array' );
    add_filter( 'nhp-opts-sections', 'mts_nhp_section_placeholder' );
    add_filter( 'nhp-opts-args', 'mts_nhp_opts_override' );
    add_filter( 'nhp-opts-extra-tabs', '__return_empty_array', 11, 1 );
}

function mts_nhp_section_placeholder( $sections ) {
    $sections[] = array(
        'icon' => 'fa fa-cogs',
        'title' => __('Not Connected', 'coupon' ),
        'desc' => '<p class="description">' . __('You will find all the theme options here after connecting with your MyThemeShop account.', 'coupon' ) . '</p>',
        'fields' => array()
    );
    return $sections;
}

function mts_nhp_opts_override( $opts ) {
    $opts['show_import_export'] = false;
    $opts['show_typography'] = false;
    $opts['show_translate'] = false;
    $opts['show_child_theme_opts'] = false;
    $opts['last_tab'] = 0;

    return $opts;
}
