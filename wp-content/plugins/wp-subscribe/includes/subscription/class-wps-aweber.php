<?php
/**
 * Aweber Subscription
 */
class WPS_Subscription_Aweber extends WPS_Subscription_Base {

	/**
	 * Aweber Credentials
	 * @var mixed
	 */
	public $credentials;

	/**
	 * API Key
	 * @var string
	 */
	public $api_key;

	/**
	 * Credential option key
	 * @return string
	 */
	private $key = 'mts_wps_awerber_credentials';

	public function init() {

		if( !class_exists( 'AWeberAPI' ) ) {
			require_once 'libs/aweber_api/aweber.php';
		}

		$credentials = $this->get_credentials();

        if( empty( $credentials['consumer_key'] ) || empty( $credentials['consumer_secret'] ) ) {
			throw new Exception ('Aweber is not connected.');
		}

        if( empty( $credentials['account_id'] ) ) {
			throw new Exception ('The Aweber Account ID is not set.');
		}

        $api = new AWeberAPI( $credentials['consumer_key'], $credentials['consumer_secret'] );

        return $api;
	}

	public function get_credentials() {

		if( !empty( $this->credentials ) ) {
			return $this->credentials;
		}

		$credentials = array_filter( $credentials );

		if ( empty( $credentials ) ) {
			$credentials = get_option( $this->key );
		}

		$this->credentials = empty( $credentials ) ? null : $credentials;

		return $this->credentials;
	}

	public function connect( $api_key = '' ) {

	    // if the auth code is empty, show the error
	    if ( empty( $api_key ) ) {
	        throw new Exception( esc_html__( 'Unable to connect to Aweber. The Authorization Code is empty.', 'wp-subscribe' ) );
	    }

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once dirname( __FILE__ ) . '/libs/aweber_api/aweber.php';
		}

		list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = AWeberAPI::getDataFromAweberID( $api_key );

		if ( empty( $consumer_key ) || empty( $consumer_secret ) || empty( $access_key ) || empty( $access_secret ) ) {
            throw new Exception( esc_html__('Unable to connect your Aweber Account. The Authorization Code is incorrect.', 'wp-subscribe' ) );
        }

	    $aweber = new AWeberAPI( $consumer_key, $consumer_secret );
        $account = $aweber->getAccount( $access_key, $access_secret );

		$credentials = array(
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'access_key' => $access_key,
            'access_secret' => $access_secret,
            'account_id' => $account->id
        );

		update_option( $this->key, $credentials );

		return $credentials;
	}

	public function get_account() {

		$aweber = $this->init();
		$credentials = $this->get_credentials();

		if( empty( $credentials['access_key'] ) || empty( $credentials['access_secret'] ) ) {
			throw new Exception ('[init]: Aweber is not connected.');
		}

        return $aweber->getAccount( $credentials['access_key'], $credentials['access_secret'] );
	}

	public function get_lists() {

		$this->credentials = end( func_get_args() );
		$account = $this->get_account();

        $lists = array();
        foreach( $account->lists->data['entries'] as $list ) {
            $lists[ $list['id'] ] = $list['name'];
        }

		return $lists;
	}

    public function subscribe( $identity, $options ) {

		try {
			$this->credentials = $options;
			$account = $this->get_account();

			$list_url = "/accounts/{$account->id}/lists/{$options['list_id']}/subscribers";
			$list = $account->loadFromUrl($list_url);

			$name = $this->get_fullname( $identity );
			$params = array(
				'name' => $name,
				'email' => $identity['email'],
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'ad_tracking' => 'mythemeshop'
			);

			$list->create( $params );

			return array(
				'status' => 'subscribed'
			);
		}
		catch( Exception $e ) {

			// already waiting confirmation:
            // "Subscriber already subscribed and has not confirmed."
            if ( strpos( $e->getMessage(), 'has not confirmed' ) ) {
                return array( 'status' => 'pending' );
            }

            // already waiting confirmation:
            // "Subscriber already subscribed."
            if ( strpos( $e->getMessage(), 'already subscribed' ) ) {
                return array( 'status' => 'pending' );
            }

			throw new Exception ( '[subscribe]: ' . $e->getMessage() );
		}
	}

	public function get_fields() {

		$fields = array(

			'aweber_raw' => array(
				'id'    => 'aweber_raw',
				'name'  => 'aweber_raw',
				'type'  => 'raw',
				'content' => array( $this, 'raw_content' )
			),

			'aweber_consumer_key' => array(
				'id'    => 'aweber_consumer_key',
				'name'  => 'aweber_consumer_key',
				'type'  => 'hidden',
			),
			'aweber_consumer_secret' => array(
				'id'    => 'aweber_consumer_secret',
				'name'  => 'aweber_consumer_secret',
				'type'  => 'hidden',
			),
			'aweber_access_key' => array(
				'id'    => 'aweber_access_key',
				'name'  => 'aweber_access_key',
				'type'  => 'hidden',
			),
			'aweber_access_secret' => array(
				'id'    => 'aweber_access_secret',
				'name'  => 'aweber_access_secret',
				'type'  => 'hidden',
			),
			'aweber_account_id' => array(
				'id'    => 'aweber_account_id',
				'name'  => 'aweber_account_id',
				'type'  => 'hidden',
			),

			'aweber_list_id' => array(
				'id'    => 'aweber_list_id',
				'name'  => 'aweber_list_id',
				'type'  => 'select',
				'title' => esc_html__( 'AWeber List', 'wp-subscribe' ),
				'options' => array( 'none' => esc_html__( 'Select List', 'wp-subscribe' ) ) + wps_get_service_list( 'aweber' ),
				'is_list'  => true
			),
		);

		return $fields;
	}

	public function raw_content() {
		$instance = $this->instance;

		?>
		<div class="aweber_authorization_area mb30<?php echo ! empty( $instance['aweber_access_key'] ) ? ' hidden' : '' ?>">
			<strong><?php esc_html_e( 'To connect your Aweber account:', 'content-locker' ) ?></strong>
			<br />
			<ul>
				<li><?php printf( wp_kses_post( __( '<span>1.</span> <a href="%s" target="_blank">Click here</a> <span>to open the authorization page and log in.</span>', 'content-locker' ) ), 'https://auth.aweber.com/1.0/oauth/authorize_app/1afc783e' ) ?></li>
				<li><?php echo wp_kses_post( __( '<span>2.</span> Copy and paste the authorization code in the field below.', 'content-locker' ) ) ?></li>
			</ul>

			<textarea rows="4" cols="80"></textarea>
			<br />
			<button type="button" class="button-primary aweber_authorization">Authorize</button>
		</div>
		<div class="alert alert-hint mb30 <?php echo empty( $instance['aweber_access_key'] ) ? ' hidden' : '' ?>">
			<p>
				<strong><?php _e( 'Your Aweber Account is connected.', 'content-locker' ) ?></strong>
				<?php echo wp_kses_post( __( '<a href="#" class="aweber_disconnect">Click here</a> <span>to disconnect.</span>', 'content-locker' ) ) ?>
			</p>
		</div>
		<?php
	}
}
