<?php
class RakuteAPI {
	public $domain = "https://api.rakutenmarketing.com/%s/%s";
	/**
	 * Curl handle
	 *
	 * @var resource
	 */
	protected $curl;
	/**
	 * API Key for authenticating requests
	 *
	 * @var string
	 */
	protected $api_key, $username, $pwd, $scope;
	/**
	 * The Commission Junction API Client is completely self contained with it's own API key.
	 * The cURL resource used for the actual querying can be overidden in the contstructor for
	 * testing or performance tweaks, or via the setCurl() method.
	 *
	 * @param string $api_key API Key
	 * @param null|resource $curl Manually provided cURL handle
	 */
	public function __construct($curl = null) {
		global $mts_options;
		$this->api_key = isset($mts_options['mts_linkshare_api']) ? $mts_options['mts_linkshare_api'] : '';
		$this->username = isset($mts_options['mts_linkshare_username']) ? $mts_options['mts_linkshare_username'] : '';
		$this->pwd = isset($mts_options['mts_linkshare_password']) ? $mts_options['mts_linkshare_password'] : '';
		$this->scope = isset($mts_options['mts_linkshare_siteid']) ? $mts_options['mts_linkshare_siteid'] : '';
		if ($curl) $this->setCurl($curl);
	}

	/**
	 * Convenience method to access Product Catalog Search Service
	 *
	 * @param array $parameters GET request parameters to be appended to the url
	 * @return array Commission Junction API response, converted to a PHP array
	 * @throws Exception on cURL failure or http status code greater than or equal to 400
	 */
	public function productSearch(array $parameters = array()) {
		if ( ! $this->api_key ) {
			return array();
		}
		return $this->api("coupon", "coupon", $parameters);
	}

	public function getToken() {
		if ( ! $this->api_key ) {
			return array();
		}
		return $this->apiToken("token", "token", $parameters = array());
	}

	/**
	 * Convenience method to access Commission Detail Service
	 *
	 * @param array $parameters GET request parameters to be appended to the url
	 * @return array Commission Junction API response, converted to a PHP array
	 * @throws Exception on cURL failure or http status code greater than or equal to 400
	 */
	private function commissionDetailLookup(array $parameters = array()) {
		throw new Exception("Not implemented");
	}
	/**
	 * Generic method to fire API requests at Commission Junctions servers
	 *
	 * @param string $subdomain The subomdain portion of the REST API url
	 * @param string $resource The resource portion of the REST API url (e.g. /v2/RESOURCE)
	 * @param array $parameters GET request parameters to be appended to the url
	 * @param string $version The version portion of the REST API url, defaults to v2
	 * @return array Commission Junction API response, converted to a PHP array
	 * @throws Exception on cURL failure or http status code greater than or equal to 400
	 */
	public function api($subdomain, $resource, array $parameters = array(), $version = '1.0') {
		$ch = $this->getCurl();
		$url = sprintf($this->domain, $subdomain, $version, $resource);

		$ac_token = $this->getToken();
		$ac_token = 'Bearer '.$ac_token->access_token;

		if (!empty($parameters))
			$url .= "?" . http_build_query($parameters);
		curl_setopt_array($ch, array(
			CURLOPT_URL  => $url,
			CURLOPT_HTTPHEADER => array(
				'Accept: application/xml',
				'authorization: ' . $ac_token,
			)
		));
		$body = curl_exec($ch);
		$errno = curl_errno($ch);
		if ($errno !== 0) {
			$this->log_error( 'Error connecting to Linkshare: ' .  $errno .' '. curl_error( $ch ) . ' '.  $errno );
			return;
			//throw new Exception(sprintf("Error connecting to Linkshare: [%s] %s", $errno, curl_error($ch)), $errno);
		}

		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_status >= 400) {
			$this->log_error('LinkShare Error' . $http_status .' '. strip_tags( $body ) .' '. $http_status );
			return;
			//throw new Exception(sprintf("LinkShare Error [%s] %s", $http_status, strip_tags($body)), $http_status);
		}

		return (array) simplexml_load_string( $body );
	}

	public function apiToken($subdomain, $resource, array $parameters = array(), $version = '1.0') {
		$data = array("grant_type" => "password", "username" => $this->username,'password'=> $this->pwd,'scope'=> $this->scope);
		$data_string = http_build_query($data);
		$ch = curl_init('https://api.rakutenmarketing.com/token');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: */*',
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: '.$this->api_key,
			));
		$body = curl_exec($ch);
		$errno = curl_errno($ch);
		if ($errno !== 0) {
			$this->log_error( 'Error connecting to LinkShare Token : ' . $errno .' ' . curl_error($ch) .' '. $errno  );
			return;
			//throw new Exception(sprintf("Error connecting to LinkShare Token : [%s] %s", $errno, curl_error($ch)), $errno);
		}

		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_status >= 400) {
				$this->log_error( 'LinkShare Error Token : ' . $http_status .' ' . $body .' '. $http_status  );
				return;
			  //throw new Exception(sprintf("LinkShare Error Token  [%s] %s", $http_status, strip_tags($body)), $http_status);
		}
		return json_decode($body);
	}
	/**
	 * @param resource $curl
	 */
	public function setCurl($curl) {
		$this->curl = $curl;
	}
	/**
	 * @return resource
	 */
	public function getCurl() {
		if (!is_resource($this->curl)) {
			$this->curl = curl_init();
			curl_setopt_array($this->curl, array(
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_MAXREDIRS      => 1,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT        => 30,
			));
		}
		return $this->curl;
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
