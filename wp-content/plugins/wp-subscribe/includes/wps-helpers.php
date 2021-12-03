<?php
/**
 * Helper Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// ---------------- PLUGIN HELPERS -----------------------------------

/**
 * Generate the subscription form
 * @return void
 */
function wps_the_form( $options = null ) {

	global $wp, $wp_subscribe_forms;

	// Options
	if ( null == $options ) {
		return;
	}

	// Enqueue script and styles
	wp_enqueue_style( 'wp-subscribe' );
	wp_enqueue_script( 'wp-subscribe' );

	$wp_subscribe_forms++;
	$service = wps_get_subscription_service( $options['service'] );
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
?>
	<?php if( isset( $options['before_widget'] ) ) : ?>
		<?php echo $options['before_widget'] ?>
	<?php else: ?>
		<div class="wp-subscribe-popup-form-wrapper">
	<?php endif; ?>

		<div id="wp-subscribe" class="wp-subscribe-wrap wp-subscribe wp-subscribe-<?php echo $wp_subscribe_forms ?>" data-thanks_page="<?php echo absint( isset( $options['thanks_page'] ) ? $options['thanks_page'] : 0 ) ?>" data-thanks_page_url="<?php echo isset( $options['thanks_page_url'] ) ? esc_url( $options['thanks_page_url'] ) : '' ?>" data-thanks_page_new_window="0">

			<h4 class="title"><?php echo wp_kses_post( $options['title'] )?></h4>

			<p class="text"><?php echo wp_kses_post( $options['text'] ) ?></p>

			<?php
			if ( method_exists( $service, 'the_form' ) ) :
				$service->the_form( $wp_subscribe_forms, $options );
			else :
				?>
				<form action="<?php echo esc_url( $current_url ); ?>" method="post" class="wp-subscribe-form wp-subscribe-<?php echo esc_attr( $options['service'] ); ?>" id="wp-subscribe-form-<?php echo esc_attr( $wp_subscribe_forms ); ?>">

					<?php if ( ! empty( $options['include_name_field'] ) ) : ?>
						<input class="regular-text name-field" type="text" name="name" placeholder="<?php echo esc_attr( $options['name_placeholder'] ); ?>" title="<?php echo esc_attr( $options['name_placeholder'] ); ?>" required>
					<?php endif; ?>

					<input class="regular-text email-field" type="email" name="email" placeholder="<?php echo esc_attr( $options['email_placeholder'] ); ?>" title="<?php echo esc_attr( $options['email_placeholder'] ); ?>" required>

					<input type="hidden" name="form_type" value="<?php echo esc_attr( $options['form_type'] ); ?>">

					<input type="hidden" name="service" value="<?php echo esc_attr( $options['service'] ); ?>">

					<input type="hidden" name="widget" value="<?php echo isset( $options['widget_id'] ) ? esc_attr( $options['widget_id'] ) : '0'; ?>">
					<?php if ( ! empty( $options['consent_text'] ) ) : ?>
						<div class="wps-consent-wrapper">
							<label for="consent-field">
								<input class="consent-field" id="consent-field" type="checkbox" name="consent" required>
								<?php _e( $options['consent_text'] ); ?>
							</label>
						</div>
					<?php endif; ?>
					<input class="submit" type="submit" name="submit" value="<?php echo esc_attr( $options['button_text'] ); ?>">

				</form>

			<?php endif; ?>

			<div class="wp-subscribe-loader">
				<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
					<path fill="#ffffff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
						<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/>
					</path>
				</svg>
			</div>

			<?php if( !empty( $options['success_message'] ) ) {
				printf( '<p class="thanks">%s</p>', wp_kses_post( $options['success_message'] ) );
			} ?>

			<?php if( !empty( $options['error_message'] ) ) {
				printf( '<p class="error">%s</p>', wp_kses_post( $options['error_message'] ) );
			} ?>

			<div class="clear"></div>

			<p class="footer-text"><?php echo $options['footer_text'];?></p>

		</div>

	<?php if( isset( $options['after_widget'] ) ) : ?>
		<?php echo $options['after_widget'] ?>
	<?php else: ?>
		</div><!-- /form-wrapper -->
	<?php endif; ?>

<?php
}

/**
 * Get widget setting by id
 * @param  int $widget_id
 * @return mixed
 */
function wps_get_widget_settings( $widget_id ) {

	$options = array();
	global $wp_registered_widgets;

    if ( isset( $wp_registered_widgets ) && isset( $wp_registered_widgets[$widget_id] ) ) {

        $widget = $wp_registered_widgets[$widget_id];
        $settings = $widget['callback'][0]->get_settings();

        if ( isset( $settings[$widget['params'][0]['number']] ) ) {
            $options = $settings[$widget['params'][0]['number']];
        }
    }

    return $options;
}


// ---------------- STRING HELPERS ---------------------------------

/**
 * Check if the string begins with the given value
 *
 * @param  string	$needle   The sub-string to search for
 * @param  string	$haystack The string to search
 *
 * @return bool
 */
function wps_str_start_with( $needle, $haystack ) {
	return substr_compare( $haystack, $needle, 0, strlen( $needle ) ) === 0;
}

/**
 * Check if the string contains the given value
 *
 * @param  string	$needle   The sub-string to search for
 * @param  string	$haystack The string to search
 *
 * @return bool
 */
function wps_str_contains( $needle, $haystack ) {
	return strpos( $haystack, $needle ) !== false;
}


// ---------------- HTML HELPERS ---------------------------------

/**
 * Output select field html
 *
 * @param  array  $args
 *
 * @return void
 */
function wps_field_select( $args = array() ) {

	extract( wp_parse_args( $args, array(
		'class' => 'widefat'
	) ) );
	?>
	<select class="<?php echo esc_attr( $class ) ?>" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>">

		<?php foreach ( $options as $key => $text ) : ?>
			<option value="<?php echo esc_attr( $key ) ?>"<?php selected( $key, $value ) ?>>
				<?php echo esc_html( $text ) ?>
			</option>
		<?php endforeach ?>
	</select>
	<?php
}

/**
 * Output text field html
 *
 * @param  array  $args
 *
 * @return void
 */
function wps_field_text( $args = array() ) {

	extract( wp_parse_args( $args, array(
		'class' => 'widefat'
	) ) );
	?>
	<input class="<?php echo esc_attr( $class ) ?>" id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>" type="text" value="<?php echo esc_attr( $value ) ?>"<?php if( isset( $data_id ) ) { printf( 'data-id="%s"', $data_id ); } ?>>
	<?php
}

/**
 * Output hidden field html
 *
 * @param  array  $args
 *
 * @return void
 */
function wps_field_hidden( $args = array() ) {

	extract( $args );
	?>
	<input id="<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $name ) ?>" type="hidden" value="<?php echo esc_attr( $value ) ?>"<?php if( isset( $data_id ) ) { printf( 'data-id="%s"', $data_id ); } ?>>
	<?php
}

/**
 * Get animation select
 * @param  string $id
 * @param  string $name
 * @return void
 */
function wps_get_animations( $id = '', $name = '', $value = '' ) {

	$animations = array(
		'0' => esc_html__( 'No Animation', 'wp-subscribe' ),
		esc_html__( 'Attention Seekers', 'wp-subscribe' ) => array(
			'bounce'     => esc_html__( 'bounce', 'wp-subscribe' ),
			'flash'      => esc_html__( 'flash', 'wp-subscribe' ),
			'pulse'      => esc_html__( 'pulse', 'wp-subscribe' ),
			'rubberBand' => esc_html__( 'rubberBand', 'wp-subscribe' ),
			'shake'      => esc_html__( 'shake', 'wp-subscribe' ),
			'swing'      => esc_html__( 'swing', 'wp-subscribe' ),
			'tada'       => esc_html__( 'tada', 'wp-subscribe' ),
			'wobble'     => esc_html__( 'wobble', 'wp-subscribe' ),
		),
		esc_html__( 'Bouncing Entrances', 'wp-subscribe' ) => array(
			'bounceIn'      => esc_html__( 'bounceIn', 'wp-subscribe' ),
			'bounceInDown'  => esc_html__( 'bounceInDown', 'wp-subscribe' ),
			'bounceInLeft'  => esc_html__( 'bounceInLeft', 'wp-subscribe' ),
			'bounceInRight' => esc_html__( 'bounceInRight', 'wp-subscribe' ),
			'bounceInUp'    => esc_html__( 'bounceInUp', 'wp-subscribe' ),
		),
		esc_html__( 'Fading Entrances', 'wp-subscribe' ) => array(
			'fadeIn'         => esc_html__( 'fadeIn', 'wp-subscribe' ),
			'fadeInDown'     => esc_html__( 'fadeInDown', 'wp-subscribe' ),
			'fadeInDownBig'  => esc_html__( 'fadeInDownBig', 'wp-subscribe' ),
			'fadeInLeft'     => esc_html__( 'fadeInLeft', 'wp-subscribe' ),
			'fadeInLeftBig'  => esc_html__( 'fadeInLeftBig', 'wp-subscribe' ),
			'fadeInRight'    => esc_html__( 'fadeInRight', 'wp-subscribe' ),
			'fadeInRightBig' => esc_html__( 'fadeInRightBig', 'wp-subscribe' ),
			'fadeInUp'       => esc_html__( 'fadeInUp', 'wp-subscribe' ),
			'fadeInUpBig'    => esc_html__( 'fadeInUpBig', 'wp-subscribe' ),
		),
		esc_html__( 'Flippers', 'wp-subscribe' ) => array(
			'flipInX' => esc_html__( 'flipInX', 'wp-subscribe' ),
			'flipInY' => esc_html__( 'flipInY', 'wp-subscribe' ),
		),
		esc_html__( 'Lightspeed', 'wp-subscribe' ) => array(
			'lightSpeedIn' => esc_html__( 'lightSpeedIn', 'wp-subscribe' ),
		),
		esc_html__( 'Rotating Entrances', 'wp-subscribe' ) => array(
			'rotateIn'          => esc_html__( 'rotateIn', 'wp-subscribe' ),
			'rotateInDownLeft'  => esc_html__( 'rotateInDownLeft', 'wp-subscribe' ),
			'rotateInDownRight' => esc_html__( 'rotateInDownRight', 'wp-subscribe' ),
			'rotateInUpLeft'    => esc_html__( 'rotateInUpLeft', 'wp-subscribe' ),
			'rotateInUpRight'   => esc_html__( 'rotateInUpRight', 'wp-subscribe' ),
		),
		esc_html__( 'Specials', 'wp-subscribe' ) => array(
			'rollIn' => esc_html__( 'rollIn', 'wp-subscribe' ),
		),
		esc_html__( 'Zoom Entrances', 'wp-subscribe' ) => array(
			'zoomIn'      => esc_html__( 'zoomIn', 'wp-subscribe' ),
			'zoomInDown'  => esc_html__( 'zoomInDown', 'wp-subscribe' ),
			'zoomInLeft'  => esc_html__( 'zoomInLeft', 'wp-subscribe' ),
			'zoomInRight' => esc_html__( 'zoomInRight', 'wp-subscribe' ),
			'zoomInUp'    => esc_html__( 'zoomInUp', 'wp-subscribe' ),
		)
	);

	printf( '<select id="%1$s" name="%2$s">', $id, $name );
		wps_print_select_options( $animations, $value );
	echo '</select>';
}

function wps_print_select_options( $options, $value ) {

	foreach( $options as $key => $text ) {

		if( is_array( $text ) ) {
			printf( '<optgroup label="%s">', $key );
				wps_print_select_options( $text, $value );
			echo '</optgroup>';
		}
		else {
			printf(
				'<option value="%1$s"%3$s>%2$s</option>',
				$key, $text,
				selected( $value, $key, false )
			);
		}
	}
}

// ---------------- SERVICE HELPERS ---------------------------------

/**
 * Get subscription service info
 *
 * @param  string	$id
 * @return string
 */
function wps_get_subscription_info( $id ) {

	$services = wps_get_mailing_services();

	return isset( $services[$id] ) ? $services[$id] : null;
}

/**
 * Get subscription service class instance
 *
 * @param  string 	$id
 * @return object
 */
function wps_get_subscription_service( $id ) {

	$info = wps_get_subscription_info( $id );

	if( is_null( $info ) ) {
		return;
	}

	return new $info['class']( $info );
}

/**
 * Get service list stored in db as trasient
 *
 * @param  string 	$name
 * @return array
 */
function wps_get_service_list( $name = '' ) {

	if( !$name ) {
		return;
	}

	$list = get_option( 'mts_wps_'. $name . '_lists' );

	return empty( $list ) ? array() : $list;
}
