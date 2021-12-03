<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Popular Offers
	Version: 1.0

-----------------------------------------------------------------------------------*/
if( ! class_exists( 'mts_coupon_popular_posts_widget' ) ){
	class mts_coupon_popular_posts_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'mts_coupon_popular_posts_widget',
				sprintf( __('%sPopular Offers', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Displays Popular Coupons & Deals.', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'title_length' => 7,
				'date' => 0,
				'days' => 30,
				'show_title' => 1,
				'box_layout' => 'horizontal-small',
				'show_excerpt' => 0,
				'excerpt_length' => 10,
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Popular Offers', 'coupon' );
			$title_length = isset( $instance[ 'title_length' ] ) ? intval( $instance[ 'title_length' ] ) : 7;
			$qty = isset( $instance[ 'qty' ] ) ? intval( $instance[ 'qty' ] ) : 5;
			$date = isset( $instance[ 'date' ] ) ? intval( $instance[ 'date' ] ) : 1;
			$days = isset( $instance[ 'days' ] ) ? intval( $instance[ 'days' ] ) : 30;
			$show_title = isset( $instance[ 'show_title' ] ) ? intval( $instance[ 'show_title' ] ) : 1;
			$box_layout = $instance['box_layout'];
			$show_excerpt = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
			$excerpt_length = isset( $instance[ 'excerpt_length' ] ) ? intval( $instance[ 'excerpt_length' ] ) : 10;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'days' ); ?>"><?php _e( 'Popular limit (days):', 'coupon' ); ?>
			   <input id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $days ); ?>" />
			   </label>
		   </p>

			<p>
				<label for="<?php echo $this->get_field_id( 'qty' ); ?>"><?php _e( 'Number of Posts to show', 'coupon' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'qty' ); ?>" name="<?php echo $this->get_field_name( 'qty' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $qty ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'title_length' ); ?>"><?php _e( 'Title Length:', 'coupon' ); ?>
			   <input id="<?php echo $this->get_field_id( 'title_length' ); ?>" name="<?php echo $this->get_field_name( 'title_length' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $title_length ); ?>" />
			   </label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("show_title"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_title"); ?>" name="<?php echo $this->get_field_name("show_title"); ?>" value="1" <?php if (isset($instance['show_title'])) { checked( 1, $instance['show_title'], true ); } ?> />
					<?php _e( 'Show Offer Title', 'coupon' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('box_layout'); ?>"><?php _e('Title layout:', 'coupon' ); ?></label>
				<select id="<?php echo $this->get_field_id('box_layout'); ?>" name="<?php echo $this->get_field_name('box_layout'); ?>">
					<option value="horizontal-small" <?php selected($box_layout, 'horizontal-small', true); ?>><?php _e('Horizontal', 'coupon' ); ?></option>
					<option value="vertical-small" <?php selected($box_layout, 'vertical-small', true); ?>><?php _e('Vertical', 'coupon' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("date"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("date"); ?>" name="<?php echo $this->get_field_name("date"); ?>" value="1" <?php if (isset($instance['date'])) { checked( 1, $instance['date'], true ); } ?> />
					<?php _e( 'Show Coupon Date', 'coupon' ); ?>
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
			   <input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $excerpt_length ); ?>" />
			   </label>
		   </p>
			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['qty'] = intval( $new_instance['qty'] );
			$instance['title_length'] = intval( $new_instance['title_length'] );
			$instance['date'] = intval( $new_instance['date'] );
			$instance['days'] = intval( $new_instance['days'] );
			$instance['show_title'] = intval( $new_instance['show_title'] );
			$instance['box_layout'] = $new_instance['box_layout'];
			$instance['show_excerpt'] = intval( $new_instance['show_excerpt'] );
			$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$title_length = $instance['title_length'];
			$date = $instance['date'];
			$days = $instance['days'];
			$qty = (int) $instance['qty'];
			$show_title = (int) $instance['show_title'];
			$box_layout = isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small';
			$show_excerpt = $instance['show_excerpt'];
			$excerpt_length = $instance['excerpt_length'];

			$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			echo self::get_popular_posts( $qty, $title_length, $date, $days, $show_title, $box_layout, $show_excerpt, $excerpt_length );
			echo $after_widget;
		}

		public function get_popular_posts( $qty, $title_length, $date, $days, $show_title, $box_layout, $show_excerpt, $excerpt_length ) {

			$no_image = ( $show_title ) ? '' : ' no-thumb';

			if ( 'horizontal-small' === $box_layout ) {
				$thumbnail	 = 'widgetthumb';
				$open_li_item  = '<li class="post-box horizontal-small horizontal-container'.$no_image.'"><div class="horizontal-container-inner">';
				$close_li_item = '</div></li>';
			} else {
				$class = 'coupon-image-full';
				$open_li_item  = '<li class="post-box vertical-small'.$no_image.'">';
				$close_li_item = '</li>';
			}

			$popular_days = array();
			if ( $days ) {
				$popular_days = array(
					//set date ranges
					'after' => "$days day ago",
					'before' => 'today',
					//allow exact matches to be returned
					'inclusive' => true,
				);
			}

			global $post;

			$popular = new WP_Query( array(
				'post_type' => 'coupons',
				'ignore_sticky_posts' => 1,
				'meta_key' => 'mts_coupon_people_used',
				'orderby'   => 'meta_value_num',
				'posts_per_page' => $qty,
				'date_query' => $popular_days) );

			echo '<ul class="popular-posts coupon-popular-posts">';

			while ( $popular->have_posts() ) { $popular->the_post(); ?>
				<?php echo $open_li_item; ?>
					<?php if ( $show_title == 1 ) :
						mts_coupon_thumb( 'coupon-image' );
					endif; ?>
					<div class="post-data">
						<div class="post-data-container">
							<div class="post-title">
								<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo esc_html( mts_truncate( get_the_title(), $title_length, 'words' ) ); ?></a>
							</div>
							<?php if ( $date == 1 ) : ?>
							<div class="post-info">
								<?php if ( $date == 1 ) : ?>
									<span class="thetime updated"><i class="fa fa-clock-o"></i> <?php the_time( get_option( 'date_format' ) ); ?></span>
								<?php endif; ?>
							</div> <!--end .post-info-->
							<?php endif; ?>
							<?php if ( $show_excerpt == 1 ) : ?>
							<div class="post-excerpt">
								<?php echo mts_excerpt( $excerpt_length ); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				<?php echo $close_li_item; ?>
			<?php }
			wp_reset_postdata();
			echo '</ul>'."\r\n";
		}

	}
}
// Register widget
add_action( 'widgets_init', 'mts_register_coupon_popular_posts_widget' );
function mts_register_coupon_popular_posts_widget() {
	register_widget( 'mts_coupon_popular_posts_widget' );
}
