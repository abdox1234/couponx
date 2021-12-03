<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Coupon App
	Version: 1.0

-----------------------------------------------------------------------------------*/
if( ! class_exists( 'mts_coupon_app_widget' ) ){
	class mts_coupon_app_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
		 		'mts_coupon_app_widget',
				sprintf( __('%sCoupon App', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Show App with content and icon.', 'coupon' ) )
			);
		}

	 	public function form( $instance ) {
			$defaults = array(
				'app_small' => 'Available for',
				'app_large' => 'Android',
				'app_icon' => 'android',
				'app_url' => '#',
				'app_small2' => 'Available for',
				'app_large2' => 'App Store',
				'app_icon2' => 'apple',
				'app_url2' => '#',
			);
			$instance = wp_parse_args((array) $instance, $defaults);
			$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
			$app_small = isset( $instance[ 'app_small' ] ) ? $instance[ 'app_small' ] : 'Available for';
			$app_large = isset( $instance[ 'app_large' ] ) ? $instance[ 'app_large' ] : 'Android';
			$app_icon = isset( $instance[ 'app_icon' ] ) ? $instance[ 'app_icon' ] : 'android';
			$app_url = isset( $instance[ 'app_url' ] ) ? $instance[ 'app_url' ] : '#';

			$app_small2 = isset( $instance[ 'app_small2' ] ) ? $instance[ 'app_small2' ] : 'Available for';
			$app_large2 = isset( $instance[ 'app_large2' ] ) ? $instance[ 'app_large2' ] : 'App Store';
			$app_icon2 = isset( $instance[ 'app_icon2' ] ) ? $instance[ 'app_icon2' ] : 'apple';
			$app_url2 = isset( $instance[ 'app_url2' ] ) ? $instance[ 'app_url2' ] : '#';
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'coupon' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
			   <label for="<?php echo $this->get_field_id( 'app_small' ); ?>"><?php _e( 'App Small Text:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'app_small' ); ?>" name="<?php echo $this->get_field_name( 'app_small' ); ?>" type="text" value="<?php echo esc_attr( $app_small ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'app_large' ); ?>"><?php _e( 'App Large Text:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'app_large' ); ?>" name="<?php echo $this->get_field_name( 'app_large' ); ?>" type="text" value="<?php echo esc_attr( $app_large ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'app_icon' ); ?>"><?php _e( 'App Icon:', 'coupon' ); ?></label>
				<?php
				$fa_icons = mts_get_icons();

				echo '<select class="coupon-iconselect" id="'.$this->get_field_id( 'app_icon' ).'" name="'.$this->get_field_name( 'app_icon' ).'" style="width: 100%;">';
				echo '<option value="" '.selected($app_icon, '', false).'>'.__('No Icon', 'coupon' ).'</option>';
				foreach ( $fa_icons as $icon_category => $icons ) {
					echo '<optgroup label="'.$icon_category.'">';
					foreach ($icons as $icon) {
						echo '<option value="'.$icon.'" '.selected( $app_icon, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
					}
					echo '</optgroup>';
				}

				echo '</select>';
				?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'app_url' ); ?>"><?php _e( 'App URL:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'app_url' ); ?>" name="<?php echo $this->get_field_name( 'app_url' ); ?>" type="text" value="<?php echo esc_attr( $app_url ); ?>" />
			</p>

			<!-- Second Section -->
			<p>
			   <label for="<?php echo $this->get_field_id( 'app_small2' ); ?>"><?php _e( 'App Small Text2:', 'coupon' ); ?></label>
			   <input class="widefat" id="<?php echo $this->get_field_id( 'app_small2' ); ?>" name="<?php echo $this->get_field_name( 'app_small2' ); ?>" type="text" value="<?php echo esc_attr( $app_small2 ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'app_large2' ); ?>"><?php _e( 'App Large Text2:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'app_large2' ); ?>" name="<?php echo $this->get_field_name( 'app_large2' ); ?>" type="text" value="<?php echo esc_attr( $app_large2 ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'app_icon2' ); ?>"><?php _e( 'App Icon2:', 'coupon' ); ?></label>
				<?php
				$fa_icons = mts_get_icons();

				echo '<select class="coupon-iconselect" id="'.$this->get_field_id( 'app_icon2' ).'" name="'.$this->get_field_name( 'app_icon2' ).'" style="width: 100%;">';
				echo '<option value="" '.selected($app_icon2, '', false).'>'.__('No Icon', 'coupon' ).'</option>';
				foreach ( $fa_icons as $icon_category => $icons ) {
					echo '<optgroup label="'.$icon_category.'">';
					foreach ($icons as $icon) {
						echo '<option value="'.$icon.'" '.selected( $app_icon2, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
					}
					echo '</optgroup>';
				}

				echo '</select>';
				?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'app_url2' ); ?>"><?php _e( 'App URL2:', 'coupon' ); ?></label>
			   	<input class="widefat" id="<?php echo $this->get_field_id( 'app_url2' ); ?>" name="<?php echo $this->get_field_name( 'app_url2' ); ?>" type="text" value="<?php echo esc_attr( $app_url2 ); ?>" />
			</p>

			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['app_small'] = $new_instance['app_small'];
			$instance['app_large'] = $new_instance['app_large'];
			$instance['app_icon'] = $new_instance['app_icon'];
			$instance['app_url'] = $new_instance['app_url'];
			$instance['app_small2'] = $new_instance['app_small2'];
			$instance['app_large2'] = $new_instance['app_large2'];
			$instance['app_icon2'] = $new_instance['app_icon2'];
			$instance['app_url2'] = $new_instance['app_url2'];
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$app_small = $instance['app_small'];
			$app_large = $instance['app_large'];
			$app_icon = $instance['app_icon'];
			$app_url = $instance['app_url'];
			$app_small2 = $instance['app_small2'];
			$app_large2 = $instance['app_large2'];
			$app_icon2 = $instance['app_icon2'];
			$app_url2 = $instance['app_url2'];

			$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'horizontal-small').'"', $before_widget); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			echo self::get_app_data( $app_small, $app_large, $app_icon, $app_url, $app_small2, $app_large2, $app_icon2, $app_url2 );
			echo $after_widget;
		}

		public function get_app_data( $app_small, $app_large, $app_icon, $app_url, $app_small2, $app_large2, $app_icon2, $app_url2  ) {

			echo '<ul class="popular-posts coupon-app">'; ?>

				<li class="post-box">
					<a class="app-container clearfix" href="<?php echo $app_url; ?>">
						<div class="app-icon">
							<?php echo '<i class="fa fa-'.$app_icon.'"></i>'; ?>
						</div>
						<div class="app-right">
							<div class="app-small">
								<?php echo $app_small; ?>
							</div>
							<div class="app-large">
								<?php echo $app_large; ?>
							</div>
						</div>
					</a>
				</li>

				<li class="post-box">
					<a class="app-container clearfix" href="<?php echo $app_url2; ?>">
						<div class="app-icon">
							<?php echo '<i class="fa fa-'.$app_icon2.'"></i>'; ?>
						</div>
						<div class="app-right">
							<div class="app-small">
								<?php echo $app_small2; ?>
							</div>
							<div class="app-large">
								<?php echo $app_large2; ?>
							</div>
						</div>
					</a>
				</li>

			<?php echo '</ul>'."\r\n";
		}

	}
}
// add admin scripts
add_action('admin_enqueue_scripts', 'mts_coupon_app_widget_script');
function mts_coupon_app_widget_script() {
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
add_action( 'widgets_init', 'mts_register_coupon_app_widget' );
function mts_register_coupon_app_widget() {
	register_widget( 'mts_coupon_app_widget' );
}
