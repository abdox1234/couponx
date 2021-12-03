<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Website Stats
	Version: 1.0

-----------------------------------------------------------------------------------*/
if( ! class_exists( 'mts_site_stats_widget' ) ){
	class mts_site_stats_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'mts_site_stats_widget',
				sprintf( __('%sSite Stats', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Show website statistics using this widget.', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'stats_number' => '21,390',
				'stats_text' => 'Coupons redeemed',
				'stats_icon' => 'tag',
				'stats_number2' => '1,401',
				'stats_text2' => 'Coupons & Deals for you',
				'stats_icon2' => 'shopping-bag',
				'stats_number3' => '50,000+',
				'stats_text3' => 'Happy Users',
				'stats_icon3' => 'user',
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
			$stats_number = isset( $instance[ 'stats_number' ] ) ? $instance[ 'stats_number' ] : '21,390';
			$stats_text = isset( $instance[ 'stats_text' ] ) ? $instance[ 'stats_text' ] : 'Coupons redeemed';
			$stats_icon = isset( $instance[ 'stats_icon' ] ) ? $instance[ 'stats_icon' ] : 'tag';
			$stats_number2 = isset( $instance[ 'stats_number2' ] ) ? $instance[ 'stats_number2' ] : '1,401';
			$stats_text2 = isset( $instance[ 'stats_text2' ] ) ? $instance[ 'stats_text2' ] : 'Coupons & Deals for you';
			$stats_icon2 = isset( $instance[ 'stats_icon2' ] ) ? $instance[ 'stats_icon2' ] : 'shopping-bag';
			$stats_number3 = isset( $instance[ 'stats_number3' ] ) ? $instance[ 'stats_number3' ] : '50,000+';
			$stats_text3 = isset( $instance[ 'stats_text3' ] ) ? $instance[ 'stats_text3' ] : 'Happy Users';
			$stats_icon3 = isset( $instance[ 'stats_icon3' ] ) ? $instance[ 'stats_icon3' ] : 'user';
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'stats_number' ); ?>"><?php _e( 'Stats number:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'stats_number' ); ?>" name="<?php echo $this->get_field_name( 'stats_number' ); ?>" type="text" value="<?php echo esc_attr( $stats_number ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'stats_text' ); ?>"><?php _e( 'Stats text:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'stats_text' ); ?>" name="<?php echo $this->get_field_name( 'stats_text' ); ?>" type="text" value="<?php echo esc_attr( $stats_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'stats_icon' ); ?>"><?php _e( 'Stats icon:', 'coupon' ); ?></label>
				<?php
				$fa_icons = mts_get_icons();

				echo '<select class="coupon-iconselect" id="'.$this->get_field_id( 'stats_icon' ).'" name="'.$this->get_field_name( 'stats_icon' ).'" style="width: 100%; max-width: 240px;">';
				echo '<option value="" '.selected($stats_icon, '', false).'>'.__('No Icon', 'coupon' ).'</option>';
				foreach ( $fa_icons as $icon_category => $icons ) {
					echo '<optgroup label="'.$icon_category.'">';
					foreach ($icons as $icon) {
						echo '<option value="'.$icon.'" '.selected( $stats_icon, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
					}
					echo '</optgroup>';
				}

				echo '</select>';
				?>
			</p>

			<!-- Second Section -->
			<p>
			   <label for="<?php echo $this->get_field_id( 'stats_number2' ); ?>"><?php _e( 'Stats number 2:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'stats_number2' ); ?>" name="<?php echo $this->get_field_name( 'stats_number2' ); ?>" type="text" value="<?php echo esc_attr( $stats_number2 ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'stats_text2' ); ?>"><?php _e( 'Stats text 2:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'stats_text2' ); ?>" name="<?php echo $this->get_field_name( 'stats_text2' ); ?>" type="text" value="<?php echo esc_attr( $stats_text2 ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'stats_icon2' ); ?>"><?php _e( 'Stats icon 2:', 'coupon' ); ?></label>
				<?php
				$fa_icons = mts_get_icons();

				echo '<select class="coupon-iconselect" id="'.$this->get_field_id( 'stats_icon2' ).'" name="'.$this->get_field_name( 'stats_icon2' ).'" style="width: 100%; max-width: 240px;">';
				echo '<option value="" '.selected($stats_icon2, '', false).'>'.__('No Icon', 'coupon' ).'</option>';
				foreach ( $fa_icons as $icon_category => $icons ) {
					echo '<optgroup label="'.$icon_category.'">';
					foreach ($icons as $icon) {
						echo '<option value="'.$icon.'" '.selected( $stats_icon2, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
					}
					echo '</optgroup>';
				}

				echo '</select>';
				?>
			</p>

			<!-- Third Section -->
			<p>
			   <label for="<?php echo $this->get_field_id( 'stats_number3' ); ?>"><?php _e( 'Stats number 3:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'stats_number3' ); ?>" name="<?php echo $this->get_field_name( 'stats_number3' ); ?>" type="text" value="<?php echo esc_attr( $stats_number3 ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'stats_text3' ); ?>"><?php _e( 'Stats text 3:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'stats_text3' ); ?>" name="<?php echo $this->get_field_name( 'stats_text3' ); ?>" type="text" value="<?php echo esc_attr( $stats_text3 ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'stats_icon3' ); ?>"><?php _e( 'Stats icon 3:', 'coupon' ); ?></label>
				<?php
				$fa_icons = mts_get_icons();

				echo '<select class="coupon-iconselect" id="'.$this->get_field_id( 'stats_icon3' ).'" name="'.$this->get_field_name( 'stats_icon3' ).'" style="width: 100%; max-width: 240px;">';
				echo '<option value="" '.selected($stats_icon3, '', false).'>'.__('No Icon', 'coupon' ).'</option>';
				foreach ( $fa_icons as $icon_category => $icons ) {
					echo '<optgroup label="'.$icon_category.'">';
					foreach ($icons as $icon) {
						echo '<option value="'.$icon.'" '.selected( $stats_icon3, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
					}
					echo '</optgroup>';
				}

				echo '</select>';
				?>
			</p>

			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['stats_number'] = $new_instance['stats_number'];
			$instance['stats_text'] = $new_instance['stats_text'];
			$instance['stats_icon'] = $new_instance['stats_icon'];
			$instance['stats_number2'] = $new_instance['stats_number2'];
			$instance['stats_text2'] = $new_instance['stats_text2'];
			$instance['stats_icon2'] = $new_instance['stats_icon2'];
			$instance['stats_number3'] = $new_instance['stats_number3'];
			$instance['stats_text3'] = $new_instance['stats_text3'];
			$instance['stats_icon3'] = $new_instance['stats_icon3'];
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$stats_number = $instance['stats_number'];
			$stats_text = $instance['stats_text'];
			$stats_icon = $instance['stats_icon'];
			$stats_number2 = $instance['stats_number2'];
			$stats_text2 = $instance['stats_text2'];
			$stats_icon2 = $instance['stats_icon2'];
			$stats_number3 = $instance['stats_number3'];
			$stats_text3 = $instance['stats_text3'];
			$stats_icon3 = $instance['stats_icon3'];

			$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			echo self::get_stats_data( $stats_number, $stats_text, $stats_icon, $stats_number2, $stats_text2, $stats_icon2, $stats_number3, $stats_text3, $stats_icon3 );
			echo $after_widget;
		}

		public function get_stats_data( $stats_number, $stats_text, $stats_icon, $stats_number2, $stats_text2, $stats_icon2, $stats_number3, $stats_text3, $stats_icon3 ) {

			echo '<ul class="popular-posts stats-widget">'; ?>

				<li class="post-box">
					<div class="stats-icon">
						<?php echo '<i class="fa fa-'.$stats_icon.'"></i>'; ?>
					</div>
					<div class="stats-right">
						<div class="stats-number">
							<?php echo $stats_number; ?>
						</div>
						<div class="stats-text">
							<?php echo $stats_text; ?>
						</div>
					</div>
				</li>

				<li class="post-box">
					<div class="stats-icon">
						<?php echo '<i class="fa fa-'.$stats_icon2.'"></i>'; ?>
					</div>
					<div class="stats-right">
						<div class="stats-number">
							<?php echo $stats_number2; ?>
						</div>
						<div class="stats-text">
							<?php echo $stats_text2; ?>
						</div>
					</div>
				</li>

				<li class="post-box">
					<div class="stats-icon">
						<?php echo '<i class="fa fa-'.$stats_icon3.'"></i>'; ?>
					</div>
					<div class="stats-right">
						<div class="stats-number">
							<?php echo $stats_number3; ?>
						</div>
						<div class="stats-text">
							<?php echo $stats_text3; ?>
						</div>
					</div>
				</li>

			<?php echo '</ul>'."\r\n";
		}

	}
}
// add admin scripts
add_action('admin_enqueue_scripts', 'mts_site_stats_widget_script');
function mts_site_stats_widget_script() {
	$screen = get_current_screen();
	$screen_id = $screen->id;

	if ( 'widgets' == $screen_id ) {
		wp_enqueue_script(
			'select2',
			NHP_OPTIONS_URL.'js/select2.min.js',
			array('jquery'),
			MTS_THEME_VERSION,
			true
		);
		wp_enqueue_script(
			'coupon_showcase_widget',
			get_template_directory_uri() . '/js/coupon_showcase_widget.js',
			array( 'select2' ),
			MTS_THEME_VERSION,
			true
		);
		wp_enqueue_style(
			'select2',
			NHP_OPTIONS_URL.'css/select2.css',
			array(),
			MTS_THEME_VERSION,
			'all'
		);
		wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css' );
	}
}
// Register widget
add_action( 'widgets_init', 'mts_register_site_stats_widget' );
function mts_register_site_stats_widget() {
	register_widget( 'mts_site_stats_widget' );
}
