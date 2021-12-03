<?php

/**
 * The WP Subscribe widget class
 */

if( ! class_exists('wp_subscribe') ) :

class wp_subscribe extends WP_Widget {

	/**
	 * The Constructor
	 */
    public function __construct() {

        add_action( 'wp_enqueue_scripts', 					array( &$this, 'register_scripts' ) );
        add_action( 'admin_enqueue_scripts', 				array( &$this, 'enqueue_scripts' ) );
        add_action( 'customize_controls_enqueue_scripts',	array( &$this, 'enqueue_scripts' ) );

        // Widget settings
        $widget_ops = array(
			'classname'		=> 'wp_subscribe',
			'description'	=> esc_html__( 'Displays subscription form, supports FeedBurner, MailChimp & AWeber.', 'wp-subscribe' )
		);

        // Widget control settings
        $control_ops = array(
			'id_base' => 'wp_subscribe'
		);

        // Create the widget.
        parent::__construct(
			'wp_subscribe',
			esc_html__( 'WP Subscribe Widget', 'wp-subscribe' ),
			$widget_ops,
			$control_ops
		);
    }

	/**
	 * Get default values for widget
	 * @return array
	 */
	public function get_defaults() {

		return apply_filters( 'wp_subscribe_form_defaults', array(
			'service'            => 'feedburner',
            'include_name_field' => false,

            'title'             => esc_html__( 'Get more stuff', 'wp-subscribe' ),
            'text'              => esc_html__( 'Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'wp-subscribe' ),
            'email_placeholder' => esc_html__( 'Enter your email here', 'wp-subscribe' ),
            'consent_text'			=> esc_html__( 'I consent to my submitted data being collected via this form*', 'wp-subscribe' ),
            'name_placeholder'  => esc_html__( 'Enter your name here', 'wp-subscribe' ),
            'button_text'       => esc_html__( 'Sign Up Now', 'wp-subscribe' ),
            'success_message'   => esc_html__( 'Thank you for subscribing.', 'wp-subscribe' ),
            'error_message'     => esc_html__( 'Something went wrong.', 'wp-subscribe' ),
            'footer_text'       => esc_html__( 'we respect your privacy and take protecting it seriously', 'wp-subscribe' )
        ));
    }

	/**
	 * Register scripts and json to be used in plugin
	 * @return void
	 */
    function register_scripts() {

        wp_register_style( 'wp-subscribe', wps()->plugin_url() . '/assets/css/wp-subscribe-form.css' );
        wp_register_script( 'wp-subscribe', wps()->plugin_url() . '/assets/js/wp-subscribe-form.js', array( 'jquery' ) );

        wp_localize_script( 'wp-subscribe', 'wp_subscribe', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
    }

	/**
	 * Enqueue script for specific screens only
	 * @return void
	 */
    function enqueue_scripts() {
			$screen = get_current_screen();
			$current_filter = current_filter();
      if ( 'widgets' === $screen->id || 'customize_controls_enqueue_scripts' === $current_filter ) {
        wp_enqueue_style( 'wp-subscribe-options', wps()->plugin_url() . '/assets/css/wp-subscribe-options.css' );
      }
      wp_enqueue_script( 'wp-subscribe-admin', wps()->plugin_url() . '/assets/js/wp-subscribe-admin.js', array( 'jquery' ) );
    }

	/**
	 * Display widget
	 * @param  array $args
	 * @param  array $instance
	 * @return void
	 */
    function widget( $args, $instance ) {

		extract( $args );
        $instance = wp_parse_args( (array) $instance, $this->get_defaults() );

		$instance['before_widget'] = $before_widget;
		$instance['after_widget'] = $after_widget;
		$instance['widget_id'] = $this->id;
		$instance['form_type'] = 'widget';

		wps_the_form( $instance );
    }

	/**
	 * Update widget
	 *
	 * @param  array $new_instance
	 * @param  array $old_instance
	 *
	 * @return array
	 */
    function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
        $instance = array_merge( $instance, $new_instance );

        // Feedburner ID -- make sure the user didn't insert full url
        if( isset( $instance['feedburner_id'] ) && 0 === strpos( $instance['feedburner_id'], 'http' ) ) {
			$instance['feedburner_id'] = substr( $instance['feedburner_id'], strrpos( $instance['feedburner_id'], '/' ) + 1 );
		}

        return $instance;
    }

	/**
	 * Display widget form
	 *
	 * @param  array $instance
	 * @return void
	 */
    function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->get_defaults() );
		$services = wps_get_mailing_services('options');
    ?>
    	<div class="wp_subscribe_options_form">

        	<!-- Hidden title field to prevent WP picking up Title Color field as widget title -->
        	<input type="hidden" value="" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>">

			<?php $this->field_select(array(
				'id'      => 'service',
				'name'    => 'service',
				'title'   => esc_html( 'Service:', 'wp-subscribe' ),
				'value'   => $instance['service'],
				'options' => $services,
				'class'   => 'services_dropdown'
			)); ?>

        	<div class="wp_subscribe_account_details">

				<?php foreach( $services as $service_id => $service_name ): ?>
					<div class="wps-account-details wp_subscribe_account_details_<?php echo esc_attr( $service_id ) ?>" data-service="<?php echo esc_attr( $service_id ) ?>" style="display: none;">
						<?php
							$service = wps_get_subscription_service( $service_id );
							$service->display_form( $instance, $this );
						?>
					</div><!-- /wp_subscribe_account_details_<?php echo esc_attr( $service_id ) ?> -->
				<?php endforeach; ?>

        	</div><!-- .wp_subscribe_account_details -->

	        <p class="wp_subscribe_include_name">

				<label for="<?php echo $this->get_field_id('include_name_field') ?>">
	            	<input type="hidden" name="<?php echo $this->get_field_name('include_name_field'); ?>" value="0">
	            	<input id="<?php echo $this->get_field_id('include_name_field'); ?>" type="checkbox" class="include-name-field" name="<?php echo $this->get_field_name('include_name_field'); ?>" value="1" <?php checked($instance['include_name_field']); ?>>
	            	<?php echo wp_kses_post( __( 'Include <strong>Name</strong> field', 'wp-subscribe' ) ) ?>
	        	</label>

			</p>

        	<h4 class="wp_subscribe_labels_header">
				<a class="wp-subscribe-toggle" href="#" rel="wp_subscribe_labels"><?php _e('Labels', 'wp-subscribe'); ?></a>
			</h4>

        	<div class="wp_subscribe_labels" style="display: none;">

				<?php

					$this->field_textarea(array(
						'id'    => 'title',
						'name'  => 'title',
						'title' => esc_html( 'Title', 'wp-subscribe' ),
						'value' => $instance['title']
					));

					$this->field_text(array(
						'id'    => 'text',
						'name'  => 'text',
						'title' => esc_html( 'Text', 'wp-subscribe' ),
						'value' => $instance['text']
					));

					$this->field_text(array(
						'id'    => 'name_placeholder',
						'name'  => 'name_placeholder',
						'title' => esc_html( 'Name Placeholder', 'wp-subscribe' ),
						'value' => $instance['name_placeholder']
					));

					$this->field_text(array(
						'id'    => 'email_placeholder',
						'name'  => 'email_placeholder',
						'title' => esc_html( 'Email Placeholder', 'wp-subscribe' ),
						'value' => $instance['email_placeholder']
					));

					$this->field_text(array(
						'id'    => 'consent_text',
						'name'  => 'consent_text',
						'title' => esc_html( 'Consent Label', 'wp-subscribe' ),
						'value' => $instance['consent_text']
					));

					$this->field_text(array(
						'id'    => 'button_text',
						'name'  => 'button_text',
						'title' => esc_html( 'Button Text', 'wp-subscribe' ),
						'value' => $instance['button_text']
					));

					$this->field_text(array(
						'id'    => 'success_message',
						'name'  => 'success_message',
						'title' => esc_html( 'Success Message', 'wp-subscribe' ),
						'value' => $instance['success_message']
					));

					$this->field_text(array(
						'id'    => 'error_message',
						'name'  => 'error_message',
						'title' => esc_html( 'Error Message', 'wp-subscribe' ),
						'value' => $instance['error_message']
					));

					$this->field_textarea(array(
						'id'    => 'footer_text',
						'name'  => 'footer_text',
						'title' => esc_html( 'Footer Text', 'wp-subscribe' ),
						'value' => $instance['footer_text']
					));
	        	?>

	        </div><!-- .wp_subscribe_labels -->

    	</div><!-- .wp_subscribe_options_form -->
    <?php
    }

	// -------------------------- FIELD HELPRES ----------------------

    public function field_textarea( $args = array() ) {

		extract( $args );
		?>
        <p class="wp-subscribe-label-field wp-subscribe-<?php echo $id; ?>-field">
            <label for="<?php echo $this->get_field_id($id) ?>">
                <?php echo $title ?>
            </label>

            <textarea class="widefat" id="<?php echo $this->get_field_id($id) ?>" name="<?php echo $this->get_field_name($id) ?>"><?php echo esc_textarea( $value ) ?></textarea>
        </p>

        <?php
    }

	public function field_text( $args = array() ) {

		extract( $args );
		?>
        <div class="wp-subscribe-label-field wp-subscribe-<?php echo $id; ?>-field">
            <label for="<?php echo $this->get_field_id( $id ) ?>">
                <?php echo esc_html( $title ) ?>
            </label>

			<div class="wps-input-wrapper">

				<?php wps_field_text(array(
					'id'	=> $this->get_field_id( $id ),
					'name'	=> $this->get_field_name( $id ),
					'value'	=> $value,
					'data_id' => $id
				)) ?>

				<?php if( isset( $link ) ) {
					printf( ' <a target="_blank" href="%s" class="button">%s</a>', esc_url( $link ), esc_html__( 'Click here', 'wp-subscribe' ) );
				} ?>

				<?php if( isset( $desc ) ) {
					printf( '<span class="wps-desc">%s</span>', wp_kses_post( $desc ) );
				} ?>

			</div>

        </div>
        <?php
    }

	public function field_hidden( $args = array() ) {

		extract( $args );

		wps_field_hidden(array(
			'id'	=> $this->get_field_id( $id ),
			'name'	=> $this->get_field_name( $id ),
			'value'	=> $value,
			'data_id' => $id
		));
    }

	public function field_raw( $args = array() ) {

		call_user_func_array( $args['content'], array( $args['value'] ) );
    }

	public function field_checkbox( $args = array() ) {

		extract( $args );
		?>
		<div class="wp-subscribe-<?php echo $id; ?>-field">

			<label for="<?php echo $this->get_field_id( $id ) ?>">

				<input type="hidden" name="<?php echo $this->get_field_name( $id ) ?>" value="0" data-id="<?php echo $this->get_field_id( $id ) ?>">

				<input type="checkbox" id="<?php echo $this->get_field_id( $id ) ?>" name="<?php echo $this->get_field_name( $id ) ?>" value="1"<?php checked( $value ) ?> data-id="<?php echo $id ?>">

				<?php echo esc_html($title) ?>

			</label>

		</div>
		<?php
	}

    public function field_select( $args = array() ) {

		$options = array();
		extract( $args );
        ?>

        <div class="wp-subscribe-label-field wp-subscribe-<?php echo $id ?>-field">
            <label for="<?php echo $this->get_field_id( $id ) ?>">
                <?php echo esc_html( $title ) ?>
            </label>

			<div class="wps-input-wrapper">
				<?php wps_field_select(array(
					'id'	=> $this->get_field_id( $id ),
					'name'	=> $this->get_field_name( $id ),
					'value'	=> $value,
					'options' => $options,
					'class' => 'widefat list-selectbox'
				)) ?>

				<?php if( isset( $is_list ) && $is_list ) {
					printf( ' <button class="button wps-get-list">%s</button>', esc_html__( 'Get list', 'wp-subscribe' ) );
				} ?>

				<?php if( isset( $link ) ) {
					printf( ' <a target="_blank" href="%s" class="button">%s</a>', esc_url( $link ), esc_html__( 'Click here', 'wp-subscribe' ) );
				} ?>

				<?php if( isset( $desc ) ) {
					printf( '<span class="wps-desc">%s</span>', wp_kses_post( $desc ) );
				} ?>

			</div>

        </div>

        <?php
    }
}

/**
 * Register widget
 * @return void
 */
add_action( 'widgets_init', 'wps_register_widget' );
function wps_register_widget() {
    register_widget( 'wp_subscribe' );
}

endif;
