<?php
class MTS_TradeDoubler {
	public $url = "http://api.tradedoubler.com/%s/%s";

	/**
	 * API Key for authenticating requests
	 *
	 * @var string
	 */
	protected $token;
	/**
	 * The Commission Junction API Client is completely self contained with it's own API key.
	 * The cURL resource used for the actual querying can be overidden in the contstructor for
	 * testing or performance tweaks, or via the setCurl() method.
	 *
	 * @param string $api_key API Key
	 */
	public function __construct() {
		global $mts_options;
		if(isset($mts_options['mts_tradedoubler_token']) && !empty($mts_options['mts_tradedoubler_token'])) {
			$this->token = $mts_options['mts_tradedoubler_token'];
		}
	}

	public function couponSearch( $params = array() ) {
		$data = array();
		$data[] = 'vouchers.json';
		foreach($params as $key => $param) {
			$data[] = $key.'='.$param;
		}
		return $this->Api(implode(';', $data));
	}

	public function Api( $resource, $version = '1.0' ) {

		if( empty( $this->token ) ) {
			$this->log_error( 'Token is not set, check your theme options Tradoubler setting and try again.');
			return;
		}
		$url = sprintf( $this->url, $version, $resource) . '?token='.$this->token;

		$result = file_get_contents( $url );

		$matches = array();
		preg_match( '#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches );

		switch( $matches[1] ) {
			case 200:
				return (array)json_decode($result, true);
			break;

			case 401:
				throw new \Exception( 'Unauthorized!' );
			break;

			default:
				throw new \Exception( 'Unexpected.' );
			break;
		}
	}
	public function log_error( $log ) {
        if ( true === WP_DEBUG || true == WP_DEBUG_LOG ) {
            if( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ));
            } else {
                error_log($log);
            }
        }
  }
}
