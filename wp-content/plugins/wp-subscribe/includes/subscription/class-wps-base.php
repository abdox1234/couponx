<?php
/**
 * A class for subscription services
 */
abstract class WPS_Subscription_Base {

	/**
	 * To hold the configuration
	 * @var array
	 */
	public $config;

	/**
	 * Hold Service Options
	 * @var array
	 */
	public $options;

	/**
	 * The Constructor
	 * @param array $config [description]
	 */
	public function __construct( $config = array() ) {

		$this->config = $config;
	}

	/**
	 * Is a valid email address
	 * @param  string  $email
	 * @return boolean
	 */
	public function is_email( $email ) {
        return filter_var( $email, FILTER_VALIDATE_EMAIL );
    }

	/**
	 * Check for single optin method
	 * @return boolean
	 */
	public function has_single_optin() {
        return in_array( 'quick', $this->config['modes'] );
    }

	/**
	 * Get identity fullname
	 * @param  array $identity
	 * @return string
	 */
	public function get_fullname( $identity ) {

        if ( !empty( $identity['name'] ) && !empty( $identity['family'] ) ) {
			return $identity['name'] . ' ' . $identity['family'];
		}

		if ( !empty( $identity['name'] ) ) {
			return $identity['name'];
		}

		if ( !empty( $identity['family'] ) ) {
			return $identity['family'];
		}

		if( !empty( $identity['display_name'] ) ) {
			return $identity['display_name'];
		}

		return '';
	}

	public function get_fields() {
		return array();
	}

	public function get_options( $data ) {

		$options = array();
		$new_options = array();

		if( 'widget' === $data['form_type'] && $data['widget'] ) {
			$options = wps_get_widget_settings( $data['widget'] );
		}
		else {
			$options = wps_get_options();
			$options = $options["{$data['form_type']}_form_options"];
		}

		foreach( $this->get_fields() as $key => $item ) {

			if( isset( $options[ $key ] ) ) {
				$new_key = str_replace( "{$data['service']}_", '', $key );
				$new_key = str_replace( "{$data['service']}", '', $new_key );
				$new_options[$new_key] = $options[ $key ];
			}
		}

		return $new_options;
	}

	public function display_form() {

		$args     = func_get_args();
		$instance = array_shift($args);
		$widget   = array_shift($args);
		$fields   = $this->get_fields( $instance );
		$this->instance = $instance;

		foreach( $fields as $id => $field ) {
			$func = is_null( $widget ) ? 'wps_field_' . $field['type'] : 'field_' . $field['type'];
			$field['value'] = isset( $instance[$id] ) ? $instance[$id] : '';

			if( is_null( $widget ) && function_exists( $func ) ) {
				$arguments = array_merge( array( $field ), $args );
				call_user_func_array( $func, $arguments );
			}

			if( !is_null( $widget ) && method_exists( $widget, $func ) ) {
				$arguments = array_merge( array( $field ), $args );
				call_user_func_array( array( $widget, $func ), $arguments );
			}
		}
	}

	public function the_name_field( $name ) {

		if( !empty( $this->options['include_name_field'] ) ) {
			printf( '<input class="regular-text name-field" type="text" name="%s" placeholder="%s" required>', esc_attr( $name ), esc_attr( $this->options['name_placeholder'] ) );
		}
	}

	public function the_email_field( $name ) {

		printf( '<input class="regular-text email-field" type="email" name="%s" placeholder="%s" required>', esc_attr( $name ), esc_attr( $this->options['email_placeholder'] ) );
	}

	public function the_submit_button() {

		printf( '<input class="submit" type="submit" name="submit" value="%s">', esc_attr( $this->options['button_text'] ) );
	}
}
