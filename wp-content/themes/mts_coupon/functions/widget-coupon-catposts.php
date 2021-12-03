<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Coupon Category Offeres
	Version: 1.0
	
-----------------------------------------------------------------------------------*/
if( ! class_exists( 'single_coupon_category_posts_widget' ) ){
	class single_coupon_category_posts_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'single_coupon_category_posts_widget',
				sprintf( __('%sCoupon Category Offers', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Display the most recent offers from a single category', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'title_length' => 12,
				'date' => 0,
				'show_title' => 1,
				'show_excerpt' => 0,
				'excerpt_length' => 10
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Coupon Category', 'coupon' );
			$title_length = isset( $instance[ 'title_length' ] ) ? intval( $instance[ 'title_length' ] ) : 7;
			$cat = isset( $instance[ 'cat' ] ) ? intval( $instance[ 'cat' ] ) : 0;
			$qty = isset( $instance[ 'qty' ] ) ? intval( $instance[ 'qty' ] ) : 5;
			$date = isset( $instance[ 'date' ] ) ? intval( $instance[ 'date' ] ) : 1;
			$show_title = isset( $instance[ 'show_title' ] ) ? intval( $instance[ 'show_title' ] ) : 1;
			$show_excerpt = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
			$excerpt_length = isset( $instance[ 'excerpt_length' ] ) ? intval( $instance[ 'excerpt_length' ] ) : 10;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Category:', 'coupon' ); ?></label>
				<?php wp_dropdown_categories( Array(
							'orderby'			=> 'ID', 
							'order'			  => 'ASC',
							'show_count'		 => 1,
							'hide_empty'		 => 1,
							'hide_if_empty'	  => true,
							'echo'			   => 1,
							'selected'		   => $cat,
							'hierarchical'	   => 1, 
							'name'			   => $this->get_field_name( 'cat' ),
							'id'				 => $this->get_field_id( 'cat' ),
							'taxonomy'		   => 'mts_coupon_categories',
						) ); ?>
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
				<label for="<?php echo $this->get_field_id("date"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("date"); ?>" name="<?php echo $this->get_field_name("date"); ?>" value="1" <?php checked( 1, $instance['date'], true ); ?> />
					<?php _e( 'Show Coupon Date', 'coupon' ); ?>
				</label>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id("show_excerpt"); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>" value="1" <?php checked( 1, $instance['show_excerpt'], true ); ?> />
					<?php _e( 'Show Excerpt', 'coupon' ); ?>
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
			$instance['cat'] = intval( $new_instance['cat'] );
			$instance['title_length'] = intval( $new_instance['title_length'] );
			$instance['qty'] = intval( $new_instance['qty'] );
			$instance['date'] = intval( $new_instance['date'] );
			$instance['show_title'] = intval( $new_instance['show_title'] );
			$instance['show_excerpt'] = intval( $new_instance['show_excerpt'] );
			$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$cat = $instance['cat'];
			$title_length = $instance['title_length'];
			$date = $instance['date'];
			$qty = (int) $instance['qty'];
			$show_title = (int) $instance['show_title'];
			$show_excerpt = $instance['show_excerpt'];
			$excerpt_length = $instance['excerpt_length'];

			$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			echo self::get_cat_posts( $cat, $title_length, $qty, $date, $show_title, $show_excerpt, $excerpt_length );
			echo $after_widget;
		}

		public function get_cat_posts( $cat, $title_length, $qty, $date, $show_title, $show_excerpt, $excerpt_length ) {
			
			$no_image = ( $show_title ) ? '' : ' no-thumb';

					$args = array(
						'post_type' => 'coupons',
						'posts_per_page' => $qty,
						'orderby' => 'date',
						'order' => 'DESC',
						'tax_query' => array(
							array(
								'taxonomy' => 'mts_coupon_categories',
								'terms' => $cat,
							)
						)										 
					);
					$posts = new WP_Query($args);

			echo '<ul class="category-posts coupon-category-posts">';
			
			while ( $posts->have_posts() ) { $posts->the_post(); ?>
				<li class="post-box">
					<?php if ( $show_title == 1 ) :

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
							</div><!--.post-info-->
							<?php endif; ?>
							<?php if ( $show_excerpt == 1 ) : ?>
							<div class="post-excerpt">
								<?php echo mts_excerpt( $excerpt_length ); ?>
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
add_action( 'widgets_init', 'register_single_coupon_category_posts_widget' );
function register_single_coupon_category_posts_widget() {
	register_widget( 'single_coupon_category_posts_widget' );
}
