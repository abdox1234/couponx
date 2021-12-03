<?php
class MTS_CJ {
	public $cjurl = "https://%s.api.cj.com/%s/%s";

	public $timeout = 10;
	/**
	 * API Key for authenticating requests
	 *
	 * @var string
	 */
	protected $api_key;
	/**
	 * Curl handle
	 *
	 * @var resource
	 */
	protected $curl;
	protected $website_id;
	/**
	 * The Commission Junction API Client is completely self contained with it's own API key.
	 * The cURL resource used for the actual querying can be overidden in the contstructor for
	 * testing or performance tweaks, or via the setCurl() method.
	 *
	 * @param string $api_key API Key
	 */
	public function __construct( $curl = null ) {
		global $mts_options;
		if(isset($mts_options['mts_cj_developer_key']) && isset($mts_options['mts_cj_web_id'])) {
			$this->api_key = $mts_options['mts_cj_developer_key'];
			$this->website_id = $mts_options['mts_cj_web_id'];
			$this->setCurl( $curl );
		}
	}

	public function linkSearch( $params = array() ) {
		$params['website-id'] = $this->website_id;
		return $this->Api( 'link-search', 'link-search', $params);
	}

	public function categories() {
		$categories = array('Accessories','Air','Apparel','Art','Art/Photo/Music','Astrology','Auction','Audio Books','Automotive','Autumn','Babies','Back to School','Banking/Trading','Bath & Body','Beauty','Bed & Bath','Betting/Gaming','Black Friday/Cyber Monday','Blogs','Books','Books/Media','Broadband','Business','Business-to-Business','Buying and Selling','Camping and Hiking','Car','Careers','Cars & Trucks','Charitable Organizations','Children',"Children's",'Christmas','Classifieds','Clothing/Apparel','Collectibles','Collectibles and Memorabilia','College','Commercial','Communities','Computer & Electronics','Computer HW','Computer Support','Computer SW','Construction','Consumer Electronics','Cosmetics','Credit Cards','Credit Reporting and Repair','Department Stores','Department Stores/Malls','Discounts','Domain Registrations','E-commerce Solutions/Providers','Easter','Education','Electronic Games','Electronic Toys','Email Marketing','Employment','Energy Saving','Entertainment','Equipment','Events','Events','Exercise & Health','Family',"Father's Day",'Financial Services','Flowers','Food & Drinks','Fragrance','Fundraising','Furniture','Games','Games & Toys','Garden','Gifts','Gifts & Flowers','Golf','Gourmet','Green','Greeting Cards','Groceries','Guides','Halloween','Handbags','Health and Wellness','Health Food','Home & Garden','Home Appliances','Hotel','Insurance','Internet Service Providers','Investment','Jewelry','Kitchen','Languages','Legal','Luggage','Magazines','Malls','Marketing','Matchmaking','Memorabilia',"Men's",'Military','Mobile Entertainment','Mortgage Loans',"Mother's Day",'Motorcycles','Music','Network Marketing',"New Year's Resolution",'New/Used Goods','News','Non-Profit','Nutritional Supplements','Office','Online Services','Online/Wireless','Outdoors','Parts & Accessories','Party Goods','Peripherals','Personal Insurance','Personal Loans','Pets','Pharmaceuticals','Phone Card Services','Photo','Productivity Tools','Professional','Professional Sports Organizations','Real Estate','Real Estate Services','Recreation & Leisure','Recycling','Rentals','Restaurants','Search Engine','Seasonal','Self Help','Services','Shoes','Sports','Sports & Fitness','Spring','Summer','Summer Sports','Tax Season','Tax Services','Teens','Telecommunications','Telephone Services','Television','Tobacco','Tools and Supplies','Toys','Travel','Utilities','Vacation',"Valentine's Day",'Videos/Movies','Virtual Malls','Vision Care','Water Sports','Web Design','Web Hosting/Servers','Web Tools','Weddings','Weight Loss','Wellness','Wine & Spirits','Winter','Winter Sports',"Women's");

		asort($categories);
		return array_unique( $categories );
	}

	public function languages() {
		$languages = array( 'ab' => 'Abkhaz', 'aa' => 'Afar', 'af' => 'Afrikaans', 'ak' => 'Akan', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese', 'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan', 'ay' => 'Aymara', 'az' => 'Azerbaijani', 'bm' => 'Bambara', 'ba' => 'Bashkir', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali', 'bh' => 'Bihari', 'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian', 'my' => 'Burmese', 'ca' => 'Catalan', 'ch' => 'Chamorro', 'ce' => 'Chechen', 'ny' => 'Chichewa', 'zh' => 'Chinese', 'cv' => 'Chuvash', 'kw' => 'Cornish', 'co' => 'Corsican', 'cr' => 'Cree', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'dv' => 'Divehi', 'nl' => 'Dutch', 'dz' => 'Dzongkha', 'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe', 'fo' => 'Faroese', 'fj' => 'Fijian', 'fi' => 'Finnish', 'fr' => 'French', 'ff' => 'Fula', 'gl' => 'Galician', 'ka' => 'Georgian', 'de' => 'German', 'el' => 'Greek', 'gn' => 'Guarani', 'gu' => 'Gujarati', 'ht' => 'Haitian', 'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi', 'ho' => 'Hiri motu', 'hu' => 'Hungarian', 'ia' => 'Interlingua', 'id' => 'Indonesian', 'ie' => 'Interlingue', 'ga' => 'Irish', 'ig' => 'Igbo', 'ik' => 'Inupiaq', 'io' => 'Ido', 'is' => 'Icelandic', 'it' => 'Italian', 'iu' => 'Inuktitut', 'ja' => 'Japanese','jv' => 'Javanese', 'kl' => 'Kalaallisut', 'kn' => 'Kannada', 'kr' => 'Kanuri', 'ks' => 'Kashmiri', 'kk' => 'Kazakh', 'km' => 'Khmer', 'ki' => 'Kikuyu', 'rw' => 'Kinyarwanda', 'ky' => 'Kyrgyz', 'kv' => 'Komi', 'kg' => 'Kongo','ko' => 'Korean', 'ku' => 'Kurdish', 'kj' => 'Kwanyama', 'la' => 'Latin', 'lb' => 'Luxembourgish', 'lg' => 'Ganda', 'li' => 'Limburgish', 'ln' => 'Lingala', 'lo' => 'Lao', 'lt' => 'Lithuanian', 'lu' => 'Luba-katanga', 'lv' => 'Latvian', 'gv' => 'Manx', 'mk' => 'Macedonian', 'mg' => 'Malagasy', 'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'mi' => 'Maori', 'mr' => 'Marathi', 'mh' => 'Marshallese', 'mn' => 'Mongolian', 'na' => 'Nauru', 'nv' => 'Navajo', 'nd' => 'Northern ndebele', 'ne' => 'Nepali', 'ng' => 'Ndonga', 'nb' => 'Norwegian Bokmal', 'nn' => 'Norwegian nynorsk', 'no' => 'Norwegian', 'ii' => 'Nuosu', 'nr' => 'Southern ndebele', 'oc' => 'Occitan', 'oj' => 'Ojibwe', 'cu' => 'Old church slavonic', 'om' => 'Oromo', 'or' => 'Oriya', 'os' => 'Ossetian', 'pa' => 'Panjabi', 'pi' => 'Pali', 'fa' => 'Persian', 'pl' => 'Polish', 'ps' => 'Pashto', 'pt' => 'Portuguese', 'qu' => 'Quechua', 'rm' => 'Romansh', 'rn' => 'Kirundi', 'ro' => 'Romanian', 'ru' => 'Russian', 'sa' => 'Sanskrit', 'sc' => 'Sardinian', 'sd' => 'Sindhi', 'se' => 'Northern sami', 'sm' => 'Samoan', 'sg' => 'Sango', 'sr' => 'Serbian', 'gd' => 'Scottish gaelic', 'sn' => 'Shona', 'si' => 'Sinhala', 'sk' => 'Slovak', 'sl' => 'Slovene', 'so' => 'Somali', 'st' => 'Southern sotho', 'es' => 'Spanish', 'su' => 'Sundanese', 'sw' => 'Swahili', 'ss' => 'Swati', 'sv' => 'Swedish', 'ta' => 'Tamil', 'te' => 'Telugu', 'tg' => 'Tajik', 'th' => 'Thai', 'ti' => 'Tigrinya', 'bo' => 'Tibetan standard', 'tk' => 'Turkmen', 'tl' => 'Tagalog', 'tn' => 'Tswana', 'to' => 'Tonga', 'tr' => 'Turkish', 'ts' => 'Tsonga', 'tt' => 'Tatar', 'tw' => 'Twi', 'ty' => 'Tahitian', 'ug' => 'Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volapuk', 'wa' => 'Walloon', 'cy' => 'Welsh', 'wo' => 'Wolof', 'fy' => 'Western frisian', 'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'za' => 'Zhuang', 'zu' => 'Zulu');

		asort($languages);
		return array_unique ( $languages );
	}

	public function linktypes() {
		$types = $this->Api( 'support-services', 'link-types' );
		if(!$types) return false;
		$types = $types['link-types']['link-type'];
		asort($types);
		return array_unique ( $types );
	}

	public function Api( $subdomain, $resource, $params = array(), $version = 'v2' ) {

		if( empty( $this->api_key ) ) {
			return array();
		}
		$ch = $this->getCurl();
		$url = sprintf( $this->cjurl, $subdomain, $version, $resource ) . ( !empty( $params ) ? '?' . http_build_query( $params ) : '' );
		$ac_token = 'Bearer '.$this->api_key;
		curl_setopt_array($ch, array(
			CURLOPT_URL  => $url,
			CURLOPT_HTTPHEADER => array(
				'Accept: application/xml',
				'Authorization: ' . $ac_token,
			)
		));
		$body = curl_exec($ch);
		$errno = curl_errno($ch);
		if ($errno !== 0) {
			$this->log_error( 'Error connecting to Commission Junction: ' . 'Error ' . $errno .' '. curl_error($ch) );
			//throw new Exception(sprintf("Error connecting to Commission Junction: [%s] %s", $errno, curl_error($ch)), $errno);
			return;
		}

		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_status >= 400) {
			$this->log_error( 'CJ Authorization Error: ' . 'Error ' . $http_status . ' ' . strip_tags( $body ));
			//throw new Exception(sprintf("CJ Authorization Error [%s] %s", $http_status, strip_tags($body)), $http_status);
			return;
		}
		$results = json_decode( json_encode( (array) simplexml_load_string( $body ) ), true );
		curl_close($ch);
		return $results;
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
				CURLOPT_CONNECTTIMEOUT => $this->timeout,
				CURLOPT_TIMEOUT        => 60,
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
