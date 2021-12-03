<?php
/**
 * Plugin options functions.
 */

/**
 * Get mailing services
 *
 * @use filter	wp_subscribe_mailing_services
 * @return array
 */
function wps_get_mailing_services( $type = 'raw' ) {

	$services = array(

        'aweber' => array(
            'title'       => esc_html__( 'Aweber', 'wp-subscribe' ),
            'description' => esc_html__( 'Adds subscribers to your Aweber account.', 'wp-subscribe' ),
            'class'       => 'WPS_Subscription_Aweber'
        ),

		'feedburner' => array(
            'title'       => esc_html__( 'FeedBurner', 'wp-subscribe' ),
            'description' => esc_html__( 'Adds subscribers to your FeedBurner account.', 'wp-subscribe' ),
            'class'       => 'WPS_Subscription_FeedBurner'
        ),

		'mailchimp' => array(
            'title'       => esc_html__( 'MailChimp', 'wp-subscribe' ),
            'description' => esc_html__( 'Adds subscribers to your MailChimp account.', 'wp-subscribe' ),
			'class'       => 'WPS_Subscription_MailChimp'
        )
	);

	$services = apply_filters( 'wp_subscribe_mailing_services', $services );

	if( 'options' === $type ) {
		return wp_list_pluck( $services, 'title' );
	}

	return $services;
}
