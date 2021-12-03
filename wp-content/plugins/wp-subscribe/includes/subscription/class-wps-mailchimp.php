<?php
/**
 * MailChimp Subscription
 */

class WPS_Subscription_MailChimp extends WPS_Subscription_Base {

	public function init( $api_key ) {

		require_once 'libs/mailchimp.php';
		return new MailChimp( $api_key );
	}

	public function get_lists( $api_key ) {

		$mailchimp = $this->init( $api_key );
		$result = $mailchimp->get('lists');

		if( $mailchimp->getLastError() ) {
			throw new Exception( $mailchimp->getLastError() );
		}

		$lists = array();
		foreach( $result['lists'] as $list ) {
			$lists[ $list['id'] ] = $list['name'];
		}

		return $lists;
	}

    public function subscribe( $identity, $options ) {

		$vars = array();
        if ( !empty( $identity['name'] ) ) {
			$vars['FNAME'] = $identity['name'];
			$vars['MERGE1'] = $identity['name'];
		}

		$mailchimp = $this->init( $options['api_key'] );
		$subscriber_hash = $mailchimp->subscriberHash($identity['email']);
		$mailchimp->put( 'lists/'. $options['list_id'] .'/members/' . $subscriber_hash, [
            'email_address'	=> $identity['email'],
			'merge_fields'	=> empty( $vars ) ? new stdClass : $vars,
            'status'		=> $options['double_optin'] ? 'pending' : 'subscribed'
        ]);

		if( $mailchimp->getLastError() ) {
			throw new Exception( $mailchimp->getLastError() );
		}

        return array(
			'status' => 'subscribed'
		);
	}

	public function get_fields() {

		$fields = array(
			'mailchimp_api_key' => array(
				'id'    => 'mailchimp_api_key',
				'name'  => 'mailchimp_api_key',
				'type'  => 'text',
				'title' => esc_html__( 'MailChimp API URL', 'wp-subscribe' ),
				'desc'  => esc_html__( 'The API key of your MailChimp account.', 'wp-subscribe' ),
				'link'  => 'http://kb.mailchimp.com/integrations/api-integrations/about-api-keys#Finding-or-generating-your-API-key',
			),

			'mailchimp_list_id' => array(
				'id'    => 'mailchimp_list_id',
				'name'  => 'mailchimp_list_id',
				'type'  => 'select',
				'title' => esc_html__( 'MailChimp List', 'wp-subscribe' ),
				'options' => array( 'none' => esc_html__( 'Select List', 'wp-subscribe' ) ) + wps_get_service_list('mailchimp'),
				'is_list'  => true
			),

			'mailchimp_double_optin' => array(
				'id'    => 'mailchimp_double_optin',
				'name'  => 'mailchimp_double_optin',
				'type'  => 'checkbox',
				'title' => esc_html__( 'Send double opt-in notification', 'wp-subscribe' )
			)
		);

		return $fields;
	}
}
