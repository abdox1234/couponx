<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Latest Offers
	Version: 1.0

-----------------------------------------------------------------------------------*/
if( ! class_exists( 'mts_coupon_recent_posts_widget' ) ){
	class mts_coupon_recent_posts_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'mts_coupon_recent_posts_widget',
				sprintf( __('%sLatest Offers', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Display the most recent coupons & deals from all categories', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'title_length' => 12,
				'date' => 0,
				'offer_title' => 1,
				'show_excerpt' => 0,
				'excerpt_length' => 10
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Latest Coupons', 'coupon' );
			$qty = isset( $instance[ 'qty' ] ) ? esc_attr( $instance[ 'qty' ] ) : 5;
			$title_length = isset( $instance[ 'title_length' ] ) ? intval( $instance[ 'title_length' ] ) : 7;
			$show_excerpt = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
			$date = isset( $instance[ 'date' ] ) ? esc_attr( $instance[ 'date' ] ) : 1;
			$excerpt_length = isset( $instance[ 'excerpt_length' ] ) ? intval( $instance[ 'excerpt_length' ] ) : 10;
			$offer_title = isset( $instance[ 'offer_title' ] ) ? esc_attr( $instance[ 'offer_title' ] ) : 1;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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
				<label for="<?php echo $this->get_field_id("offer_title"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("offer_title"); ?>" name="<?php echo $this->get_field_name("offer_title"); ?>" value="1" <?php if (isset($instance['offer_title'])) { checked( 1, $instance['offer_title'], true ); } ?> />
					<?php _e( 'Show Offer Title', 'coupon' ); ?>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id("date"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("date"); ?>" name="<?php echo $this->get_field_name("date"); ?>" value="1" <?php checked( 1, $instance['date'], true ); ?> />
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
			$instance['offer_title'] = intval( $new_instance['offer_title'] );
			$instance['show_excerpt'] = isset( $new_instance['show_excerpt'] ) ? intval( $new_instance['show_excerpt'] ) : 0;
			$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$title_length = $instance['title_length'];
			$date = $instance['date'];
			$qty = (int) $instance['qty'];
			$offer_title = (int) $instance['offer_title'];
			$show_excerpt = $instance['show_excerpt'];
			$excerpt_length = $instance['excerpt_length'];

			$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			echo self::get_cat_posts( $qty, $title_length, $date, $offer_title, $show_excerpt, $excerpt_length );
			echo $after_widget;
		}

		public function get_cat_posts( $qty, $title_length, $date, $offer_title, $show_excerpt, $excerpt_length ) {

			$no_image = ( $offer_title ) ? '' : ' no-thumb';

			$posts = new WP_Query( array(
				'post_type' => 'coupons',
				'orderby' => 'date',
				'order' => 'DESC',
				'posts_per_page' => $qty,
				'ignore_sticky_posts' => true,
				'no_found_rows' => true,
				'post_status' => 'publish',
			) );

			echo '<ul class="advanced-recent-posts coupon-advanced-recent-posts">';

			while ( $posts->have_posts() ) { $posts->the_post(); ?>
				<li class="post-box">
					<?php if ( $offer_title == 1 ) :

						$coupon_featured_text = get_post_meta( get_the_ID(), 'mts_coupon_featured_text', true );
						if( !empty( $coupon_featured_text ) ) : ?>
							<a class="coupon-image <?php echo isset($class) ? $class : ''; ?>" href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
								<div class="coupon-featuredtext">
									<?php echo $coupon_featured_text; ?>
								</div>
							</a>
						<?php else : ?>
							<a class="coupon-image <?php echo isset($class) ? $class : ''; ?>" href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
								<div class="coupon-featuredtext">
									<?php _e('Great Deal', 'coupon'); ?>
								</div>
							</a>
						<?php endif;

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
							</div> <!--.post-info-->
							<?php endif; ?>
							<?php if ( $show_excerpt == 1 ) : ?>
							<div class="post-excerpt">
								<?php echo mts_excerpt($excerpt_length); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php }
			wp_reset_postdata();
			echo '</ul>'."\r\n";
		}

	}
}
// Register widget
add_action( 'widgets_init', 'mts_register_coupon_recent_posts_widget' );
function mts_register_coupon_recent_posts_widget() {
	register_widget( 'mts_coupon_recent_posts_widget' );
}
