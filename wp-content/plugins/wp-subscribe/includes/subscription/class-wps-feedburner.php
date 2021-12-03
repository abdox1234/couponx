<?php
/**
 * FeedBurner Subscription
 */

class WPS_Subscription_FeedBurner extends WPS_Subscription_Base {

    public function the_form( $id, $options ) {
		?>

		<form action="https://feedburner.google.com/fb/a/mailverify?uri=<?php echo $options['feedburner_id'] ?>" method="post" class="wp-subscribe-form wp-subscribe-feedburner" id="wp-subscribe-form-<?php echo $id ?>" target="popupwindow">

			<input class="regular-text email-field" type="email" name="email" placeholder="<?php echo esc_attr( $options['email_placeholder'] ) ?>" required>

			<input type="hidden" name="uri" value="<?php echo $options['feedburner_id'] ?>">

			<input type="hidden" name="loc" value="en_US">

			<input type="hidden" name="form_type" value="<?php echo $options['form_type'] ?>">

			<input type="hidden" name="service" value="<?php echo $options['service'] ?>">

			<input type="hidden" name="widget" value="<?php echo isset( $options['widget_id'] ) ? $options['widget_id'] : '0'; ?>">
			<?php if( !empty( $options['consent_text'] ) ) : ?>
				<div class="wps-consent-wrapper">
					<label for="consent-field">
						<input class="consent-field" id="consent-field" type="checkbox" name="consent" required />
						<?php _e( $options['consent_text'] ) ?>
					</label>
				</div>
			<?php endif; ?>
			<input class="submit" type="submit" name="submit" value="<?php echo esc_attr( $options['button_text'] ) ?>">

		</form>

		<?php
	}

	public function get_fields() {

		$fields = array(
			'feedburner_id' => array(
				'id'    => 'feedburner_id',
				'name'  => 'feedburner_id',
				'type'  => 'text',
				'title' => esc_html__( 'Feedburner ID', 'wp-subscribe' ),
			)
		);

		return $fields;
	}
}
