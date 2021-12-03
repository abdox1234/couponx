<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Author Posts
	Version: 2.0.1

-----------------------------------------------------------------------------------*/
if( ! class_exists( 'mts_author_posts_widget' ) ){
	class mts_author_posts_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'mts_author_posts_widget',
				sprintf( __('%sAuthor Posts', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Display other posts written by the author of the current post. Will appear on single posts only.', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'title_length' => 4,
				'comment_num' => 1,
				'date' => 1,
				'show_thumb7' => 1,
				'box_layout' => 'horizontal-small',
				'show_excerpt' => 0,
				'excerpt_length' => 10
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Author Posts', 'coupon' );
			$title_length = isset( $instance[ 'title_length' ] ) ? intval( $instance[ 'title_length' ] ) : 4;
			$qty = isset( $instance[ 'qty' ] ) ? esc_attr( $instance[ 'qty' ] ) : 5;
			$comment_num = isset( $instance[ 'comment_num' ] ) ? esc_attr( $instance[ 'comment_num' ] ) : 1;
			$date = isset( $instance[ 'date' ] ) ? esc_attr( $instance[ 'date' ] ) : 1;
			$show_thumb7 = isset( $instance[ 'show_thumb7' ] ) ? esc_attr( $instance[ 'show_thumb7' ] ) : 1;
			$box_layout = $instance['box_layout'];
			$show_excerpt = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
			$excerpt_length = isset( $instance[ 'excerpt_length' ] ) ? intval( $instance[ 'excerpt_length' ] ) : 10;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'qty' ); ?>"><?php _e( 'Number of Posts to show', 'coupon' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'qty' ); ?>" name="<?php echo $this->get_field_name( 'qty' ); ?>" type="number" min="1" step="1" value="<?php echo $qty; ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'title_length' ); ?>"><?php _e( 'Title Length:', 'coupon' ); ?>
			   <input id="<?php echo $this->get_field_id( 'title_length' ); ?>" name="<?php echo $this->get_field_name( 'title_length' ); ?>" type="number" min="1" step="1" value="<?php echo $title_length; ?>" />
			   </label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("show_thumb7"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_thumb7"); ?>" name="<?php echo $this->get_field_name("show_thumb7"); ?>" value="1" <?php if (isset($instance['show_thumb7'])) { checked( 1, $instance['show_thumb7'], true ); } ?> />
					<?php _e( 'Show Thumbnails', 'coupon' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('box_layout'); ?>"><?php _e('Posts layout:', 'coupon' ); ?></label>
				<select id="<?php echo $this->get_field_id('box_layout'); ?>" name="<?php echo $this->get_field_name('box_layout'); ?>">
					<option value="horizontal-small" <?php selected($box_layout, 'horizontal-small', true); ?>><?php _e('Horizontal', 'coupon' ); ?></option>
					<option value="vertical-small" <?php selected($box_layout, 'vertical-small', true); ?>><?php _e('Vertical', 'coupon' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("date"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("date"); ?>" name="<?php echo $this->get_field_name("date"); ?>" value="1" <?php checked( 1, $instance['date'], true ); ?> />
					<?php _e( 'Show post date', 'coupon' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("comment_num"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("comment_num"); ?>" name="<?php echo $this->get_field_name("comment_num"); ?>" value="1" <?php checked( 1, $instance['comment_num'], true ); ?> />
					<?php _e( 'Show number of comments', 'coupon' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("show_excerpt"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>" value="1" <?php checked( 1, $instance['show_excerpt'], true ); ?> />
					<?php _e( 'Show excerpt', 'coupon' ); ?>
				</label>
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length:', 'coupon' ); ?>
			   <input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" step="1" value="<?php echo $excerpt_length; ?>" />
			   </label>
		   </p>

			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['qty'] = intval( $new_instance['qty'] );
			$instance['title_length'] = intval( $new_instance['title_length'] );
			$instance['comment_num'] = intval( $new_instance['comment_num'] );
			$instance['date'] = intval( $new_instance['date'] );
			$instance['show_thumb7'] = intval( $new_instance['show_thumb7'] );
			$instance['box_layout'] = $new_instance['box_layout'];
			$instance['show_excerpt'] = intval( $new_instance['show_excerpt'] );
			$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$title_length = $instance['title_length'];
			$comment_num = $instance['comment_num'];
			$date = $instance['date'];
			$qty = (int) $instance['qty'];
			$show_thumb7 = (int) $instance['show_thumb7'];
			$box_layout = isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small';
			$show_excerpt = $instance['show_excerpt'];
			$excerpt_length = $instance['excerpt_length'];

			if(is_singular()){
				$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
				echo $before_widget;
				if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
				echo self::get_author_posts( $qty, $title_length, $comment_num, $date, $show_thumb7, $box_layout, $show_excerpt, $excerpt_length );
				echo $after_widget;
			}
		}

		public function get_author_posts( $qty, $title_length, $comment_num, $date, $show_thumb7, $box_layout, $show_excerpt, $excerpt_length ) {

			$no_image = ( $show_thumb7 ) ? '' : ' no-thumb';

			if ( 'horizontal-small' === $box_layout ) {
				$thumbnail	 = 'widgetthumb';
				$open_li_item  = '<li class="post-box horizontal-small horizontal-container'.$no_image.'"><div class="horizontal-container-inner">';
				$close_li_item = '</div></li>';
			} else {
				$thumbnail	 = 'widgetfull';
				$open_li_item  = '<li class="post-box vertical-small'.$no_image.'">';
				$close_li_item = '</li>';
			}

			$thePostID = get_the_ID();
			$author    = get_the_author_meta('ID');
			$posts = new WP_Query(
				array(
					'author' => $author,
					'post__not_in' => array($thePostID),
					'posts_per_page' => $qty,
					'orderby' => 'rand'
					));

			echo '<ul class="author-posts-widget">';
			while ( $posts->have_posts() ) { $posts->the_post(); ?>
				<?php echo $open_li_item; ?>
					<?php if ( $show_thumb7 == 1 ) : ?>
					<div class="post-img">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
							<?php if ( has_post_thumbnail() ) { ?>
								<?php the_post_thumbnail( 'coupon-' . $thumbnail, array( 'title' => '' ) ); ?>
							<?php } else { ?>
								<img class="wp-post-image" src="<?php echo get_template_directory_uri() . '/images/nothumb-coupon-' . $thumbnail . '.png'; ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"/>
							<?php } ?>
						</a>
					</div>
					<?php endif; ?>
					<div class="post-data">
						<div class="post-data-container">
							<div class="post-title">
								<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo esc_html( mts_truncate( get_the_title(), $title_length, 'words' ) ); ?></a>
							</div>
							<?php if ( $date == 1 || $comment_num == 1 ) : ?>
							<div class="post-info">
								<?php if ( $date == 1 ) : ?>
									<span class="thetime updated"><i class="fa fa-clock-o"></i> <?php the_time( get_option( 'date_format' ) ); ?></span>
								<?php endif; ?>
								<?php if ( $comment_num == 1 ) : ?>
									<span class="thecomment"><i class="fa fa-comments"></i> <?php echo comments_number('0','1','%');?></span>
								<?php endif; ?>
							</div><!--.post-info-->
							<?php endif; ?>
							<?php if ( $show_excerpt == 1 ) : ?>
							<div class="post-excerpt">
								<?php echo mts_excerpt($excerpt_length); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				<?php echo $close_li_item; ?>
			<?php }
			echo '</ul>'."\r\n";
			wp_reset_postdata();
		}
	}
}

// Register widget
add_action( 'widgets_init', 'mts_register_author_posts_widget' );
function mts_register_author_posts_widget() {
	register_widget( 'mts_author_posts_widget' );
}
