<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Coupon Brand Info
	Version: 1.0

-----------------------------------------------------------------------------------*/
if( ! class_exists( 'mts_coupon_brand_info_widget' ) ){
	class mts_coupon_brand_info_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'mts_coupon_brand_info_widget',
				sprintf( __('%sCoupon Brand Info', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Show Brand image with URL.', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'brand_imgurl' => '',
				'brand_rewards' => 'Upto 10% Extra Rewards',
				'brand_button_text' => 'Shop at Ebay',
				'brand_url' => '#',
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
			$brand_imgurl = isset( $instance[ 'brand_imgurl' ] ) ? $instance[ 'brand_imgurl' ] : '';
			$brand_rewards = isset( $instance[ 'brand_rewards' ] ) ? $instance[ 'brand_rewards' ] : 'Upto 10% Extra Rewards';
			$brand_button_text = isset( $instance[ 'brand_button_text' ] ) ? $instance[ 'brand_button_text' ] : 'Shop at Ebay';
			$brand_url = isset( $instance[ 'brand_url' ] ) ? $instance[ 'brand_url' ] : '#';
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'brand_imgurl' ); ?>"><?php _e( 'Brand Image URL:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'brand_imgurl' ); ?>" name="<?php echo $this->get_field_name( 'brand_imgurl' ); ?>" type="text" value="<?php echo esc_attr( $brand_imgurl ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'brand_rewards' ); ?>"><?php _e( 'Brand Text:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'brand_rewards' ); ?>" name="<?php echo $this->get_field_name( 'brand_rewards' ); ?>" type="text" value="<?php echo esc_attr( $brand_rewards ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'brand_button_text' ); ?>"><?php _e( 'Brand Button Text:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'brand_button_text' ); ?>" name="<?php echo $this->get_field_name( 'brand_button_text' ); ?>" type="text" value="<?php echo esc_attr( $brand_button_text ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'brand_url' ); ?>"><?php _e( 'Brand URL:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'brand_url' ); ?>" name="<?php echo $this->get_field_name( 'brand_url' ); ?>" type="text" value="<?php echo esc_attr( $brand_url ); ?>" />
			</p>

			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['brand_imgurl'] = $new_instance['brand_imgurl'];
			$instance['brand_rewards'] = $new_instance['brand_rewards'];
			$instance['brand_button_text'] = $new_instance['brand_button_text'];
			$instance['brand_url'] = $new_instance['brand_url'];
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$brand_imgurl = $instance['brand_imgurl'];
			$brand_rewards = $instance['brand_rewards'];
			$brand_button_text = $instance['brand_button_text'];
			$brand_url = $instance['brand_url'];

			$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			echo self::get_archive_brand_data( $brand_imgurl, $brand_rewards, $brand_button_text, $brand_url );
			echo $after_widget;
		}

		public function get_archive_brand_data( $brand_imgurl, $brand_rewards, $brand_button_text, $brand_url ) {

			echo '<ul class="popular-posts">'; ?>
				<li class="post-box">
					<?php if( !empty($brand_imgurl) ) : ?>
						<div class="brand-image">
							<img src="<?php echo $brand_imgurl; ?>">
						</div>
					<?php endif; ?>
					<div class="brand-footer">
						<div class="brand-rewards"><?php echo $brand_rewards; ?></div>
						<a href="<?php echo $brand_url; ?>" class="widget-button"><?php echo $brand_button_text; ?></a>
					</div>
				</li>
			<?php echo '</ul>'."\r\n";
		}
	}
}
// Register widget
add_action( 'widgets_init', 'mts_register_coupon_brand_info_widget' );
function mts_register_coupon_brand_info_widget() {
	register_widget( 'mts_coupon_brand_info_widget' );
}
