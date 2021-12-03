<?php
class MTS_Affiliates_Coupons {

	public function __construct() {
		$this->includes();
		$this->hooks();
	}

	function includes() {
		require_once( get_theme_file_path( 'functions/affiliates/linkshare.php' ) );
		require_once( get_theme_file_path( 'functions/affiliates/cj.php' ) );
		require_once( get_theme_file_path( 'functions/affiliates/tradedoubler.php' ) );
		require_once( get_theme_file_path( 'functions/affiliates/admitad.php' ) );
	}

	public function hooks() {
		add_action('init', array($this, 'mts_affiliate_cron'));
		add_action('mts_linkshare_import', array($this, 'mts_linkshare_import_callback'));
		add_action('mts_cj_import', array($this, 'mts_cj_import_callback'));
		add_action('mts_tradedoubler_import', array($this, 'mts_tradedoubler_import_callback'));
		add_action('mts_admitad_import', array($this, 'mts_admitad_import_callback'));
	}
	public function mts_affiliate_cron() {
		global $mts_options;
		if ( ! wp_next_scheduled( 'mts_cj_import' ) ) {
			$interval = (isset($mts_options['mts_cj_frequency']) && !empty($mts_options['mts_cj_frequency'])) ? $mts_options['mts_cj_frequency'] : 'daily';
			wp_schedule_event( time(), $interval, 'mts_cj_import' );
		}

		//Linkshare Schedule Import
		if ( ! wp_next_scheduled( 'mts_linkshare_import' ) ) {
			$interval = (isset($mts_options['mts_linkshare_frequency']) && !empty($mts_options['mts_linkshare_frequency'])) ? $mts_options['mts_linkshare_frequency'] : 'daily';
			wp_schedule_event( time(), $interval, 'mts_linkshare_import' );
		}

		//TradeDoubler Schedule Import
		if ( ! wp_next_scheduled( 'mts_tradedoubler_import' ) ) {
			$interval = (isset($mts_options['mts_tradedoubler_frequency']) && !empty($mts_options['mts_tradedoubler_frequency'])) ? $mts_options['mts_tradedoubler_frequency'] : 'daily';
			wp_schedule_event( time(), $interval, 'mts_tradedoubler_import' );
		}

		//Admitad Schedule Import
		if ( ! wp_next_scheduled( 'mts_admitad_import' ) ) {
			$interval = (isset($mts_options['mts_admitad_frequency']) && !empty($mts_options['mts_admitad_frequency'])) ? $mts_options['mts_admitad_frequency'] : 'daily';
			wp_schedule_event( time(), $interval, 'mts_admitad_import' );
		}
	}

	//Admitad CRON
	function mts_admitad_import_callback() {
		try {
			global $mts_options;
			$params = array();
			$limit = ( isset($mts_options['mts_admitad_total']) && !empty($mts_options['mts_admitad_total']) ) ? $mts_options['mts_admitad_total'] : '';
			$category_id = ( isset($mts_options['mts_admitad_category']) && !empty($mts_options['mts_admitad_category']) ) ? $mts_options['mts_admitad_category'] : '';
			$campaign_id = ( isset($mts_options['mts_admitad_campaign']) && !empty($mts_options['mts_admitad_campaign']) ) ? $mts_options['mts_admitad_campaign'] : '';
			$region = ( isset($mts_options['mts_admitad_region']) && !empty($mts_options['mts_admitad_region']) ) ? $mts_options['mts_admitad_region'] : '';

			if($category_id) $params['category'] = $category_id;
			if($campaign_id) $params['campaign'] = $campaign_id;
			if($region) $params['region'] = $region;
			$params['limit'] = $limit;

			$admitad = new MTS_Admitad();
			$coupons = $admitad->couponSearch( apply_filters('mts_admitad_parameters', $params) );

			if(!empty($coupons) && isset($coupons['results']) && !empty($coupons['results'])) {
				$coupons = $coupons['results'];

				foreach($coupons as $coupon) {
					$coupon_id = $this->insert_coupon($coupon['name']);

					if($coupon_id) {
						$coupon_data = array();
						$coupon_data['mts_coupon_deal_URL'] = $coupon['goto_link'];
						if(isset($coupon['date_end'])) {
							$coupon_data['mts_coupon_expire'] = date('m/j/Y', strtotime($coupon['date_end']));
							$coupon_data['mts_coupon_expire_time'] = date('h:i A', strtotime($coupon['date_end']));
						}
						if(isset($coupon['promocode']) && !empty($coupon['promocode'])) {
							$button_type = 'coupon';
							$coupon_data['mts_coupon_code'] = $coupon['promocode'];
						} else {
							$button_type = 'deal';
						}
						$coupon_data['mts_coupon_button_type'] = $button_type;
						$coupon_data['mts_coupon_featured_text'] = $coupon['description'];

						$this->update_coupon_meta($coupon_id, $coupon_data);
						update_post_meta( $coupon_id, 'mts_admitad_alldata', $coupon );

						if(isset($mts_options['mts_admitad_create_category']) && $mts_options['mts_admitad_create_category'] && isset($coupon['categories']) && !empty($coupon['categories'])) {
							$cats = array();
							$categories = $coupon['categories'];
							foreach($coupon['categories'] as $coupon_category) {
								$cats[] = $coupon_category['name'];
							}
							$this->create_coupon_category($coupon_id, $cats, 'mts_coupon_categories');
						}

						// IMAGE
						// $image = $coupon['image'];
					}
				}
			}
		} catch (\Exception $ex) {
			// echo $ex->getMessage();
		}
	}

	//CJ CRON
	function mts_cj_import_callback() {
		try {
			global $mts_options;
			$params = array();
			$params['promotion-type'] = 'coupon';
			$params['records-per-page'] = (isset($mts_options['mts_cj_total']) && !empty($mts_options['mts_cj_total']) ) ? $mts_options['mts_cj_total'] : '';
			if(isset($mts_options['mts_cj_cat']) && !empty($mts_options['mts_cj_cat'])) {
				$params['category'] = $mts_options['mts_cj_cat'];
			}
			if(isset($mts_options['mts_cj_lang']) && !empty($mts_options['mts_cj_lang'])) {
				$params['language'] = $mts_options['mts_cj_lang'];
			}
			if(isset($mts_options['mts_cj_keywords']) && !empty($mts_options['mts_cj_keywords'])) {
				$params['keywords'] = $mts_options['mts_cj_keywords'];
			}
			$cj = new MTS_CJ();
			$coupons = $cj->linkSearch( apply_filters('mts_cj_parameters', $params) );

			if(!empty($coupons) && isset($coupons['links'])) {

				foreach($coupons['links']['link'] as $coupon) {
					if($coupons['links']['@attributes']['records-returned'] == 1) {
						$coupon = $coupons['links']['link'];
					}

					$coupon_id = $this->insert_coupon($coupon['link-name']);

					if($coupon_id) {
						$coupon_data = array();
						$coupon_data['mts_coupon_deal_URL'] = $coupon['destination'];
						if(isset($coupon['promotion-end-date']) && !empty($coupon['promotion-end-date'])) {
							$coupon_data['mts_coupon_expire'] = date('m/j/Y', strtotime($coupon['promotion-end-date']));
							$coupon_data['mts_coupon_expire_time'] = date('h:i A', strtotime($coupon['promotion-end-date']));
						}
						if(isset($coupon['coupon-code']) && !empty($coupon['coupon-code']) && $coupon['coupon-code'] != 'No Code Required') {
							$button_type = 'coupon';
							$coupon_data['mts_coupon_code'] = $coupon['coupon-code'];
						} else {
							$button_type = 'deal';
						}
						$coupon_data['mts_coupon_button_type'] = $button_type;

						$this->update_coupon_meta($coupon_id, $coupon_data);
						update_post_meta( $coupon_id, 'mts_cj_alldata', $coupon );

						if(isset($mts_options['mts_cj_create_category']) && $mts_options['mts_cj_create_category']) {
							$categories = $coupon['category'];
							$this->create_coupon_category($coupon_id, $categories, 'mts_coupon_categories');
						}
					}
					if($coupons['links']['@attributes']['records-returned'] == 1) {
						break;
					}
				}
			}
		} catch (\Exception $ex) {
			// echo $ex->getMessage();
		}
	}

	//LinkShare Cron
	function mts_linkshare_import_callback() {
		try {
			global $mts_options;
			$parameters = array();
			if(isset($mts_options['mts_linkshare_network']) && !empty($mts_options['mts_linkshare_network']) && $mts_options['mts_linkshare_network'] != 'all') {
				$parameters['network'] = $mts_options['mts_linkshare_network'];
			}

			if(isset($mts_options['mts_linkshare_category']) && !empty($mts_options['mts_linkshare_category'])) {
				$parameters['category'] = implode('|', $mts_options['mts_linkshare_category']);
			}

			if(isset($mts_options['mts_linkshare_promotiontype']) && !empty($mts_options['mts_linkshare_promotiontype'])) {
				$parameters['promotiontype'] = implode('|', $mts_options['mts_linkshare_promotiontype']);
			}

			if(isset($mts_options['mts_linkshare_total']) && !empty($mts_options['mts_linkshare_total'])) {
				$parameters['resultsperpage'] = $mts_options['mts_linkshare_total'];
			}

			if(isset($mts_options['mts_linkshare_api']) && !empty($mts_options['mts_linkshare_api'])) {
				$client = new RakuteAPI();
				$coupons = $client->productSearch($parameters);

				if(isset($coupons['link']) && !empty($coupons['link'])) {

					foreach($coupons['link'] as $coupon) {
						if($coupons['TotalMatches'] == $coupons['TotalPages']) {
							$coupon = $coupons['link'];
						}

						$coupon_id = $this->insert_coupon($coupon['offerdescription']);

						if($coupon_id) {
							$coupon_data = array();
							$coupon_data['mts_coupon_deal_URL'] = $coupon['clickurl'];
							if(isset($coupon['offerenddate'])) {
								$coupon_data['mts_coupon_expire'] = date('m/j/Y', strtotime($coupon['offerenddate']));
								$coupon_data['mts_coupon_expire_time'] = date('h:i A', strtotime($coupon['offerenddate']));
							}
							if(isset($coupon['couponcode'])) {
								$button_type = 'coupon';
								$coupon_data['mts_coupon_code'] = $coupon['couponcode'];
							} else {
								$button_type = 'deal';
							}
							$coupon_data['mts_coupon_button_type'] = $button_type;

							if(isset($coupon['couponrestriction'])) {
								$coupon_data['mts_coupon_extra_rewards'] = $coupon['couponrestriction'];
							}

							$this->update_coupon_meta($coupon_id, $coupon_data);
							update_post_meta( $coupon_id, 'mts_linkshare_alldata', $coupon );

							if(isset($mts_options['mts_linkshare_create_category']) && $mts_options['mts_linkshare_create_category']) {
								$categories = $coupon['categories']['category'];
								$this->create_coupon_category($coupon_id, $categories, 'mts_coupon_categories' );
							}
							if(isset($mts_options['mts_linkshare_create_tags']) && $mts_options['mts_linkshare_create_tags']) {
								$promotion_types = $coupon['promotiontypes']['promotiontype'];
								$this->create_coupon_category($coupon_id, $promotion_types, 'mts_coupon_tag' );
							}
						}
						if($coupons['TotalMatches'] == $coupons['TotalPages']) {
							break;
						}
					}

				}
			}
			} catch (\Exception $ex) {
				// echo $ex->getMessage();
			}

	}

	//TradeDoubler Cron
	function mts_tradedoubler_import_callback() {
		global $mts_options;
		$params = array();
		if(isset($mts_options['mts_tradedoubler_voucher']) && !empty($mts_options['mts_tradedoubler_voucher'])) {
			$params['voucherTypeId'] = implode( ';voucherTypeId=', $mts_options['mts_tradedoubler_voucher'] );
		}
		if(isset($mts_options['mts_tradedoubler_programId']) && !empty($mts_options['mts_tradedoubler_programId'])) {
			$params['programId'] = $mts_options['mts_tradedoubler_programId'];
		}
		if(isset($mts_options['mts_tradedoubler_keywords']) && !empty($mts_options['mts_tradedoubler_keywords'])) {
			$params['keywords'] = $mts_options['mts_tradedoubler_keywords'];
		}
		if(isset($mts_options['mts_tradedoubler_site_specific']) && !empty($mts_options['mts_tradedoubler_site_specific'])) {
			$params['siteSpecific'] = $mts_options['mts_tradedoubler_site_specific'];
		}
		if(isset($mts_options['mts_tradedoubler_language']) && !empty($mts_options['mts_tradedoubler_language'])) {
			$params['languageId'] = $mts_options['mts_tradedoubler_language'];
		}
		if(isset($mts_options['mts_tradedoubler_total']) && !empty($mts_options['mts_tradedoubler_total'])) {
			$params['pageSize'] = $mts_options['mts_tradedoubler_total'];
		}
		$td = new MTS_TradeDoubler();
		$coupons = $td->couponSearch( apply_filters('mts_tradedoubler_parameters', $params) );

		if(!empty($coupons)) {

			foreach($coupons as $coupon) {

				$coupon_id = $this->insert_coupon($coupon['title'], $coupon['description']);

				if($coupon_id) {
					$coupon_data = array();
					$coupon_data['mts_coupon_deal_URL'] = $coupon['defaultTrackUri'];
					if(isset($coupon['endDate'])) {
						$coupon_data['mts_coupon_expire'] = date('m/j/Y', $coupon['endDate']);
						$coupon_data['mts_coupon_expire_time'] = date('h:i A', $coupon['endDate']);
					}
					if(isset($coupon['code']) && !empty($coupon['code'])) {
						$button_type = 'coupon';
						$coupon_data['mts_coupon_code'] = $coupon['code'];
					} else {
						$button_type = 'deal';
					}
					$coupon_data['mts_coupon_button_type'] = $button_type;

					if(isset($coupon['shortDescription'])) {
						$coupon_data['mts_coupon_featured_text'] = $coupon['shortDescription'];
					}

					//IMAGE
					// $image = $coupon['logoPath'];

					$this->update_coupon_meta($coupon_id, $coupon_data);
					update_post_meta( $coupon_id, 'mts_tradedoubler_alldata', $coupon );
				}
			}
		}
	}

	public function insert_coupon($title, $content = '') {
		if ( ! function_exists( 'post_exists' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/post.php' );
		}

		$title = sanitize_text_field($title);
		if(post_exists($title)) return;

		$coupon_id = wp_insert_post(array(
			'post_type'=>'coupons',
			'post_status' => 'publish',
			'post_title' => $title,
			'post_content' => $content
		));

		return $coupon_id;
	}

	public function update_coupon_meta($coupon_id, $coupon_data) {
		foreach($coupon_data as $key => $value) {
			update_post_meta( $coupon_id, $key, esc_html($value) );
		}
	}

	public function create_coupon_category( $coupon_id, $categories, $taxonomy ) {
		if(!empty($categories)) {
			$term_data = array();
			if(is_array($categories)) {
				foreach($categories as $category) {
					$category = sanitize_text_field($category);
					$term = term_exists( $category, $taxonomy );
					if(!$term) {
						$term = wp_insert_term($category, $taxonomy);
					}
					$term_data[] = $term['term_id'];
				}
			} else {
				$category = sanitize_text_field($categories);
				$term = term_exists( $category, $taxonomy );

				if(!$term) {
					$term = wp_insert_term($category, $taxonomy);
				}
				$term_data[] = $term['term_id'];

			}
			wp_set_post_terms( $coupon_id, $term_data, $taxonomy );
		}
	}
}

new MTS_Affiliates_Coupons;
