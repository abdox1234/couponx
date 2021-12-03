<?php
/*-----------------------------------------------------------------------------------*/
/*	Do not remove these lines, sky will fall on your head.
/*-----------------------------------------------------------------------------------*/
define( 'MTS_THEME_NAME', 'coupon' );
define( 'MTS_THEME_VERSION', '2.2.2' );

require_once( get_theme_file_path( 'theme-options.php' ) );
if ( ! isset( $content_width ) ) {
	$content_width = 866; //article content width without padding
}

/*-----------------------------------------------------------------------------------*/
/*	Load Options
/*-----------------------------------------------------------------------------------*/
$mts_options = get_option( MTS_THEME_NAME );

/**
 * Register supported theme features, image sizes and nav menus.
 * Also loads translated strings.
 */
function mts_after_setup_theme() {
	if ( ! defined( 'MTS_THEME_WHITE_LABEL' ) ) {
		define( 'MTS_THEME_WHITE_LABEL', false );
	}

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );

	load_theme_textdomain( 'coupon', get_template_directory().'/lang' );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 676, 390, true );
	add_image_size( 'coupon-featured', 676, 390, true ); //featured
	add_image_size( 'coupon-featured-thumb', 146, 146, true ); //FeaturedThumb
	add_image_size( 'coupon-widgetthumb', 80, 80, true ); //widget
	add_image_size( 'coupon-widgetfull', 237, 175, true ); //sidebar full width
	add_image_size( 'coupon-slider', 1180, 355, true ); //slider

	add_action( 'init', 'coupon_wp_review_thumb_size', 11 );

	function coupon_wp_review_thumb_size() {
		add_image_size( 'wp_review_small', 80, 80, true );
		add_image_size( 'wp_review_large', 237, 175, true );
	}

	register_nav_menus( array(
	  'primary' => __( 'Primary', 'coupon' ),
	  'mobile' => __( 'Mobile', 'coupon' )
	) );

	if ( mts_is_wc_active() ) {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action('after_setup_theme', 'mts_after_setup_theme' );

/*-----------------------------------------------------------------------------------*/
/*  Create Custom Post Types
/*-----------------------------------------------------------------------------------*/
function mts_posttype_register() {
	$mts_options = get_option( MTS_THEME_NAME );

	$rewrite_coupon_slug     = ( isset( $mts_options['mts_single_coupon_slug'] ) && ! empty( $mts_options['mts_single_coupon_slug'] ) ) ? $mts_options['mts_single_coupon_slug'] : 'coupons';
	$rewrite_coupon_cat_slug = ( isset( $mts_options['mts_coupon_cat_slug'] ) && ! empty( $mts_options['mts_coupon_cat_slug'] ) ) ? $mts_options['mts_coupon_cat_slug'] : 'coupons-category';
	$rewrite_coupon_tag_slug = ( isset( $mts_options['mts_coupon_tag_slug'] ) && ! empty( $mts_options['mts_coupon_tag_slug'] ) ) ? $mts_options['mts_coupon_tag_slug'] : 'coupons-tag';
	//Coupon Post type
	$args = array(
		'label'              => __('Coupons', 'coupon'),
		'singular_label'     => __('Coupon', 'coupon'),
		'public'             => true,
		'show_ui'            => true,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'rewrite'            => false,
		'publicly_queryable' => true,
		'query_var'          => true,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-editor-insertmore',
		'has_archive'        => true,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'rewrite'            => array( 'slug' => $rewrite_coupon_slug ), // Permalinks format
	);

	register_post_type( 'coupons', $args );

	register_taxonomy(
		'mts_coupon_categories',
		'coupons',
		array(
			'show_admin_column' => true,
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => $rewrite_coupon_cat_slug
			),
			'update_count_callback' => 'mts_update_coupons_terms_count'
		)
	);

	register_taxonomy(
		'mts_coupon_tag',
		'coupons',
		array(
			'show_admin_column' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => $rewrite_coupon_tag_slug
			),
			'update_count_callback' => 'mts_update_coupons_terms_count'
		)
	);
}
add_action('init', 'mts_posttype_register');

function mts_update_coupons_terms_count( $terms, $taxonomy ) {
	global $wpdb;
	foreach ( (array) $terms as $term ) {
		$query = new WP_Query(
            array(
                'post_type' => 'coupons',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy->name,
						'field'    => 'term_id',
						'terms'    => array( $term ),
					)
				),
				'meta_query' => array(
					'relation' => 'or',
			        array(
			              'key' => 'mts_coupon_expired',
			              'compare' => 'NOT EXISTS',
			        ),
			        array(
			              'key' => 'mts_coupon_expired',
			              'value' => '0',
			              'type' => 'numeric'
			        )
			  	)
            )
        );
		$count = $query->found_posts;
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
	}
}

// "expired-coupon" body class
add_filter('body_class', 'mts_expired_coupon_body_class', 10, 1);
function mts_expired_coupon_body_class( $classes ) {
	if ( ! is_singular( 'coupons' ) ) {
		return $classes;
	}

	$classes = array_merge( $classes, mts_expired_coupon_class() );

	return $classes;
}

function mts_expired_coupon_class( $post_id = null ) {
	$classes = array();
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	$coupon_expiry_date = get_post_meta( $post_id, 'mts_coupon_expire' );
	if ( ! $coupon_expiry_date ) {
		return $classes;
	}

	$coupon_expired = get_post_meta( $post_id, 'mts_coupon_expired' );
	if ( $coupon_expired ) {
		$classes[] = 'expired-coupon';
		return $classes;
	}
	if( mts_is_date( $coupon_expiry_date[0] ) ){
		$now = new DateTime(current_time('mysql'));
		$ref = new DateTime($coupon_expiry_date[0]);
		$diff = $now->diff($ref);

		if ( $diff->invert ) {
			$classes[] = 'expired-coupon';
			update_post_meta( $post_id, 'mts_coupon_expired', '1' );
		} else {
			if ( $diff->days <= 3 ) {
				$classes[] = 'expiring-soon';
			}
		}
	} else {
		$classes[] = 'invalid-date';
	}
	return $classes;
}

// Allow HTML Tags in category description
remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_kses_data' );

/*
 * Disable theme updates from WordPress.org theme repository.
 * Check if MTS Connect plugin already does this.
 */
if ( !class_exists('mts_connection') ) {
	/**
	 * If wrong updates are already shown, delete transient so that we can run our workaround
	 */
	function mts_hide_themes_plugins() {
		if ( !is_admin() ) return;
		if ( false === get_site_transient( 'mts_wp_org_check_disabled' ) ) { // run only once
			delete_site_transient('update_themes' );
			delete_site_transient('update_plugins' );
			add_action('current_screen', 'mts_remove_themes_plugins_from_update' );
		}
	}
	add_action('init', 'mts_hide_themes_plugins');
	/**
	 * Hide mts themes/plugins.
	 *
	 * @param WP_Screen $screen
	 */
	function mts_remove_themes_plugins_from_update( $screen ) {
		$run_on_screens = array( 'themes', 'themes-network', 'plugins', 'plugins-network', 'update-core', 'network-update-core' );
		if ( in_array( $screen->base, $run_on_screens ) ) {
			//Themes
			if ( $themes_transient = get_site_transient( 'update_themes' ) ) {
				if ( property_exists( $themes_transient, 'response' ) && is_array( $themes_transient->response ) ) {
					foreach ( $themes_transient->response as $key => $value ) {
						$theme = wp_get_theme( $value['theme'] );
						$theme_uri = $theme->get( 'ThemeURI' );
						if ( 0 !== strpos( $theme_uri, 'mythemeshop.com' ) ) {
							unset( $themes_transient->response[$key] );
						}
					}
					set_site_transient( 'update_themes', $themes_transient );
				}
			}
			//Plugins
			if ( $plugins_transient = get_site_transient( 'update_plugins' ) ) {
				if ( property_exists( $plugins_transient, 'response' ) && is_array( $plugins_transient->response ) ) {
					foreach ( $plugins_transient->response as $key => $value ) {
						$plugin = get_plugin_data( WP_PLUGIN_DIR.'/'.$key, false, false );
						$plugin_uri = $plugin['PluginURI'];
						if ( 0 !== strpos( $plugin_uri, 'mythemeshop.com' ) ) {
							unset( $plugins_transient->response[$key] );
						}
					}
					set_site_transient( 'update_plugins', $plugins_transient );
				}
			}
			set_site_transient( 'mts_wp_org_check_disabled', time() );
		}
	}
	/**
	 * Delete `mts_wp_org_check_disabled` transient.
	 */
	function mts_clear_check_transient(){
		delete_site_transient( 'mts_wp_org_check_disabled');
	}
	add_action( 'load-themes.php', 'mts_clear_check_transient' );
	add_action( 'load-plugins.php', 'mts_clear_check_transient' );
	add_action( 'upgrader_process_complete', 'mts_clear_check_transient' );
}

// Disable auto-updating the theme.
function mts_disable_auto_update_theme( $update, $item ) {
	if ( isset( $item->slug ) && $item->slug == MTS_THEME_NAME ) {
		return false;
	}
	return $update;
}
add_filter( 'auto_update_theme', 'mts_disable_auto_update_theme', 10, 2 );

/**
 * Disable Google Typography plugin
 */
function mts_deactivate_google_typography_plugin() {
	if ( in_array( 'google-typography/google-typography.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		deactivate_plugins( 'google-typography/google-typography.php' );
	}
}
add_action( 'admin_init', 'mts_deactivate_google_typography_plugin' );

/**
 * Determines whether the WooCommerce plugin is active or not.
 * @return bool
 */
function mts_is_wc_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * MTS icons for use in nav menus and icon select option.
 *
 * @return array
 */
function mts_get_icons() {
	$icons = array(
		__( 'Web Application Icons', 'coupon' ) => array(
			'adjust', 'american-sign-language-interpreting', 'anchor', 'archive', 'area-chart', 'arrows', 'arrows-h', 'arrows-v', 'assistive-listening-systems', 'asterisk', 'at', 'audio-description', 'balance-scale', 'ban', 'bar-chart', 'barcode', 'bars', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'blind', 'bluetooth', 'bluetooth-b', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'braille', 'briefcase', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'cart-arrow-down', 'cart-plus', 'cc', 'certificate', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'child', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clock-o', 'clone', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'coffee', 'cog', 'cogs', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'copyright', 'creative-commons', 'credit-card', 'credit-card-alt', 'crop', 'crosshairs', 'cube', 'cubes', 'cutlery', 'database', 'deaf', 'desktop', 'diamond', 'dot-circle-o', 'download', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-o', 'envelope-square', 'eraser', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'fax', 'female', 'fighter-jet', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-pdf-o', 'file-powerpoint-o', 'file-video-o', 'file-word-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'flag', 'flag-checkered', 'flag-o', 'flask', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gift', 'glass', 'globe', 'graduation-cap', 'hand-lizard-o', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hashtag', 'hdd-o', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hourglass', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'i-cursor', 'inbox', 'industry', 'info', 'info-circle', 'key', 'keyboard-o', 'language', 'laptop', 'leaf', 'lemon-o', 'level-down', 'level-up', 'life-ring', 'lightbulb-o', 'line-chart', 'location-arrow', 'lock', 'low-vision', 'magic', 'magnet', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'meh-o', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mobile', 'money', 'moon-o', 'motorcycle', 'mouse-pointer', 'music', 'coupon-o', 'object-group', 'object-ungroup', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paw', 'pencil', 'pencil-square', 'pencil-square-o', 'percent', 'phone', 'phone-square', 'picture-o', 'pie-chart', 'plane', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'power-off', 'print', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'question-circle-o', 'quote-left', 'quote-right', 'random', 'recycle', 'refresh', 'registered', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'rss', 'rss-square', 'search', 'search-minus', 'search-plus', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shopping-bag', 'shopping-basket', 'shopping-cart', 'sign-in', 'sign-language', 'sign-out', 'signal', 'sitemap', 'sliders', 'smile-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'space-shuttle', 'spinner', 'spoon', 'square', 'square-o', 'star', 'star-half', 'star-half-o', 'star-o', 'sticky-note', 'sticky-note-o', 'street-view', 'suitcase', 'sun-o', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'television', 'terminal', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'tint', 'toggle-off', 'toggle-on', 'trademark', 'trash', 'trash-o', 'tree', 'trophy', 'truck', 'tty', 'umbrella', 'universal-access', 'university', 'unlock', 'unlock-alt', 'upload', 'user', 'user-plus', 'user-secret', 'user-times', 'users', 'video-camera', 'volume-control-phone', 'volume-down', 'volume-off', 'volume-up', 'wheelchair', 'wheelchair-alt', 'wifi', 'wrench'
		),
		__( 'Accessibility Icons', 'coupon' ) => array(
			'american-sign-language-interpreting', 'assistive-listening-systems', 'audio-description', 'blind', 'braille', 'cc', 'deaf', 'low-vision', 'question-circle-o', 'sign-language', 'tty', 'universal-access', 'volume-control-phone', 'wheelchair', 'wheelchair-alt'
		),
		__( 'Hand Icons', 'coupon' ) => array(
			'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up'
		),
		__( 'Transportation Icons', 'coupon' ) => array(
			'ambulance', 'bicycle', 'bus', 'car', 'fighter-jet', 'motorcycle', 'plane', 'rocket', 'ship', 'space-shuttle', 'subway', 'taxi', 'train', 'truck', 'wheelchair'
		),
		__( 'Gender Icons', 'coupon' ) => array(
			'genderless', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mercury', 'neuter', 'transgender', 'transgender-alt', 'venus', 'venus-double', 'venus-mars'
		),
		__( 'File Type Icons', 'coupon' ) => array(
			'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-o', 'file-pdf-o', 'file-powerpoint-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o'
		),
		__( 'Spinner Icons', 'coupon' ) => array(
			'circle-o-notch', 'cog', 'refresh', 'spinner'
		),
		__( 'Form Control Icons', 'coupon' ) => array(
			'check-square', 'check-square-o', 'circle', 'circle-o', 'dot-circle-o', 'minus-square', 'minus-square-o', 'plus-square', 'plus-square-o', 'square', 'square-o'
		),
		__( 'Payment Icons', 'coupon' ) => array(
			'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'credit-card', 'credit-card-alt', 'google-wallet', 'paypal'
		),
		__( 'Chart Icons', 'coupon' ) => array(
			'area-chart', 'bar-chart', 'line-chart', 'pie-chart'
		),
		__( 'Currency Icons', 'coupon' ) => array(
			'btc', 'eur', 'gbp', 'gg', 'gg-circle', 'ils', 'inr', 'jpy', 'krw', 'money', 'rub', 'try', 'usd'
		),
		__( 'Text Editor Icons', 'coupon' ) => array(
			'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'chain-broken', 'clipboard', 'columns', 'eraser', 'file', 'file-o', 'file-text', 'file-text-o', 'files-o', 'floppy-o', 'font', 'header', 'indent', 'italic', 'link', 'list', 'list-alt', 'list-ol', 'list-ul', 'outdent', 'paperclip', 'paragraph', 'repeat', 'scissors', 'strikethrough', 'subscript', 'superscript', 'table', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'underline', 'undo'
		),
		__( 'Directional Icons', 'coupon' ) => array(
			'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'exchange', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up'
		),
		__( 'Video Player Icons', 'coupon' ) => array(
			'arrows-alt', 'backward', 'compress', 'eject', 'expand', 'fast-backward', 'fast-forward', 'forward', 'pause', 'pause-circle', 'pause-circle-o', 'play', 'play-circle', 'play-circle-o', 'random', 'step-backward', 'step-forward', 'stop', 'stop-circle', 'stop-circle-o', 'youtube-play'
		),
		__( 'Brand Icons', 'coupon' ) => array(
			'500px', 'adn', 'amazon', 'android', 'angellist', 'apple', 'behance', 'behance-square', 'bitbucket', 'bitbucket-square', 'black-tie', 'bluetooth', 'bluetooth-b', 'btc', 'buysellads', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'chrome', 'codepen', 'codiepie', 'connectdevelop', 'contao', 'css3', 'dashcube', 'delicious', 'deviantart', 'digg', 'dribbble', 'dropbox', 'drupal', 'edge', 'empire', 'envira', 'expeditedssl', 'facebook', 'facebook-official', 'facebook-square', 'firefox', 'first-order', 'flickr', 'font-awesome', 'fonticons', 'fort-awesome', 'forumbee', 'foursquare', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'gitlab', 'glide', 'glide-g', 'google', 'google-plus', 'google-plus-official', 'google-plus-square', 'google-wallet', 'gratipay', 'hacker-news', 'houzz', 'html5', 'instagram', 'internet-explorer', 'ioxhost', 'joomla', 'jsfiddle', 'lastfm', 'lastfm-square', 'leanpub', 'linkedin', 'linkedin-square', 'linux', 'maxcdn', 'meanpath', 'medium', 'mixcloud', 'modx', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'pagelines', 'paypal', 'pied-piper', 'pied-piper-alt', 'pied-piper-pp', 'pinterest', 'pinterest-p', 'pinterest-square', 'product-hunt', 'qq', 'rebel', 'reddit', 'reddit-alien', 'reddit-square', 'renren', 'safari', 'scribd', 'sellsy', 'share-alt', 'share-alt-square', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'skype', 'slack', 'slideshare', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'soundcloud', 'spotify', 'stack-exchange', 'stack-overflow', 'steam', 'steam-square', 'stumbleupon', 'stumbleupon-circle', 'tencent-weibo', 'themeisle', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'usb', 'viacoin', 'viadeo', 'viadeo-square', 'vimeo', 'vimeo-square', 'vine', 'vk', 'weibo', 'weixin', 'whatsapp', 'wikipedia-w', 'windows', 'wordpress', 'wpbeginner', 'wpforms', 'xing', 'xing-square', 'y-combinator', 'yahoo', 'yelp', 'yoast', 'youtube', 'youtube-play', 'youtube-square'
		),
		__( 'Medical Icons', 'coupon' ) => array(
			'ambulance', 'h-square', 'heart', 'heart-o', 'heartbeat', 'hospital-o', 'medkit', 'plus-square', 'stethoscope', 'user-md', 'wheelchair'
		)
	);

	return $icons;
}


/**
 * Get the current post's thumbnail URL.
 *
 * @param string $size
 *
 * @return string
 */
if( !function_exists('mts_get_thumbnail_url')){
	function mts_get_thumbnail_url( $size = 'full' ) {
		$post_id = get_the_ID() ;
		if (has_post_thumbnail( $post_id ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
			return $image[0];
		}

		// use first attached image
		$images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
		if (!empty($images)) {
			$image = reset($images);
			$image_data = wp_get_attachment_image_src( $image->ID, $size );
			return $image_data[0];
		}

		// use no preview fallback
		if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) ) {
			return get_template_directory_uri().'/images/nothumb-'.$size.'.png';
		}

		return '';
	}
}
/**
 * Create and show column for featured image in post & coupon item list admin page.
 * @param $post_ID
 *
 * @return string url
 */
if( !function_exists('mts_get_featured_image')){
	function mts_get_featured_image($post_ID) {
		$post_thumbnail_id = get_post_thumbnail_id($post_ID);
		if ($post_thumbnail_id) {
			if ( get_post_type() == 'coupons') {
				$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'full');
			} else {
				$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'coupon-widgetthumb');
			}
			return $post_thumbnail_img[0];
		}
	}
}
/**
 * Adds a `Featured Image` column header in the item list admin page.
 *
 * @param array $defaults
 *
 * @return array
 */
function mts_columns_head($defaults) {
	if (get_post_type() == 'post' || get_post_type() == 'coupons') {
		$defaults['featured_image'] = __('Featured Image', 'coupon' );
	}

	return $defaults;
}
add_filter('manage_posts_columns', 'mts_columns_head');

/**
 * Adds a `Featured Image` column row value in the item list admin page.
 *
 * @param string $column_name The name of the column to display.
 * @param int $post_ID The ID of the current post.
 */
function mts_columns_content($column_name, $post_ID) {
	if ($column_name == 'featured_image') {
		$post_featured_image = mts_get_featured_image($post_ID);
		if ($post_featured_image) {
			echo '<img height="40" src="' . esc_url( $post_featured_image ) . '" />';
		}
	}
}
add_action('manage_posts_custom_column', 'mts_columns_content', 10, 2);

/**
 * Admin styles
 */
function mts_columns_css() {
	echo '<style type="text/css">.posts .column-featured_image img { max-width: 100%; height: auto }</style>';
}
add_action( 'admin_print_styles', 'mts_columns_css' );

/**
 * Change the HTML markup of the post thumbnail.
 *
 * @param string $html
 * @param int $post_id
 * @param string $post_image_id
 * @param int $size
 * @param string $attr
 *
 * @return string
 */
function mts_post_image_html( $html, $post_id, $post_image_id, $size, $attr ) {
	if ( has_post_thumbnail( $post_id ) || 'shop_thumbnail' === $size )
		return $html;

	// use first attached image
	$images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
	if (!empty($images)) {
		$image = reset($images);
		return wp_get_attachment_image( $image->ID, $size, false, $attr );
	}

	// use no preview fallback
	if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) ) {
		$placeholder = get_template_directory_uri().'/images/nothumb-'.$size.'.png';
		$mts_options = get_option( MTS_THEME_NAME );
		if ( ! empty( $mts_options['mts_lazy_load'] ) && ! empty( $mts_options['mts_lazy_load_thumbs'] ) ) {
			$placeholder_src = '';
			$layzr_attr = ' data-layzr="'.esc_attr( $placeholder ).'"';
		} else {
			$placeholder_src = $placeholder;
			$layzr_attr = '';
		}

		$placeholder_classs = 'attachment-'.$size.' wp-post-image';
		return '<img src="'.esc_url( $placeholder_src ).'" class="'.esc_attr( $placeholder_classs ).'" alt="'.esc_attr( get_the_title() ).'"'.$layzr_attr.'>';
	}

	return '';
}
add_filter( 'post_thumbnail_html', 'mts_post_image_html', 10, 5 );

/**
 * Remove Lazy Load from core.
 *
 * @param boolean $default Image.
 *
 */
function disable_template_image_lazy_loading( $default ) {
	$mts_options = get_option( MTS_THEME_NAME );
	if ( ! empty( $mts_options['mts_lazy_load'] ) ) {
		return false;
	}
	return $default;
}
add_filter( 'wp_lazy_loading_enabled', 'disable_template_image_lazy_loading', 10, 1 );

/**
 * Add data-layzr attribute to featured image ( for lazy load )
 *
 * @param array $attr
 * @param WP_Post $attachment
 * @param string|array $size
 *
 * @return array
 */
function mts_image_lazy_load_attr( $attr, $attachment, $size ) {
	if ( is_admin() || is_feed() ) return $attr;
	if ( is_single() && function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) return $attr;
	$mts_options = get_option( MTS_THEME_NAME );
	if ( ! empty( $mts_options['mts_lazy_load'] ) && ! empty( $mts_options['mts_lazy_load_thumbs'] ) ) {
		$attr['data-layzr'] = $attr['src'];
		$attr['src'] = '';
		if ( isset( $attr['srcset'] ) ) {
			$attr['data-layzr-srcset'] = $attr['srcset'];
			$attr['srcset'] = '';
		}
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'mts_image_lazy_load_attr', 10, 3 );

/**
 * Add data-layzr attribute to post content images ( for lazy load )
 *
 * @param string $content
 *
 * @return string
 */

function mts_content_image_lazy_load_attr( $content ) {
	$mts_options = get_option( MTS_THEME_NAME );
	if ( is_single() && function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return $content;
	}
	if ( ! empty( $mts_options['mts_lazy_load'] )
		 && ! empty( $mts_options['mts_lazy_load_content'] )
		 && ! empty( $content ) ) {
		$content = preg_replace_callback(
			'/<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/',
			'mts_content_image_lazy_load_attr_callback',
			$content
		);
	}

	return $content;
}
add_filter('the_content', 'mts_content_image_lazy_load_attr');

/**
 * Callback to move src to data-src and replace it with a 1x1 tranparent image.
 *
 * @param $matches
 *
 * @return string
 */
function mts_content_image_lazy_load_attr_callback( $matches ) {
	$transparent_img = 'data:image/gif,GIF89a%01%00%01%00%80%00%00%00%00%00%FF%FF%FF%21%F9%04%01%00%00%00%00%2C%00%00%00%00%01%00%01%00%00%02%01D%00%3B';
	if ( preg_match( '/ data-lazy=[\'"]false[\'"]/', $matches[0] ) ) {
		return '<img ' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '>';
	} else {
		return '<img ' . $matches[1] . 'src="' . $transparent_img . '" data-layzr="' . $matches[2] . '"' . str_replace( 'srcset=', 'data-layzr-srcset=', $matches[3]). '>';
	}
}

/**
 * Enable Widgetized sidebar and Footer
 */
function mts_register_sidebars() {
	$mts_options = get_option( MTS_THEME_NAME );

	// Default sidebar
	register_sidebar( array(
		'name' => __('Sidebar', 'coupon'),
		'description'   => __( 'Default sidebar.', 'coupon' ),
		'id' => 'sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Coupon Archive
	register_sidebar(array(
		'name' => __('Sidebar - Coupons Archive', 'coupon'),
		'description'   => __( 'Default sidebar appears on Coupons archive.', 'coupon' ),
		'id' => 'sidebar-coupons',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	// Single Coupon Page
	register_sidebar(array(
		'name' => __('Sidebar - Single Coupon Page', 'coupon'),
		'description'   => __( 'This sidebar appears on Single Coupon page.', 'coupon' ),
		'id' => 'sidebar-single-coupon',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	// Subscribe Widget
	register_sidebar(array(
		'name' => __('Subscribe Widget Area', 'coupon'),
		'description'   => __( 'Set up a WP Subscribe widget in this widget area to display the subscription box throughout your site.', 'coupon' ),
		'id' => 'widget-subscribe',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	// Header Ad sidebar
	register_sidebar(array(
		'name' => __('Header Ad', 'coupon'),
		'description'   => __( '728x90 Ad Area', 'coupon' ),
		'id' => 'widget-header',
		'before_widget' => '<div id="%1$s" class="widget-header">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	// Footer widget areas
	if ( !empty( $mts_options['mts_first_footer'] )) {
		if ( empty( $mts_options['mts_first_footer_num'] )) $mts_options['mts_first_footer_num'] = 4;
		register_sidebars( $mts_options['mts_first_footer_num'], array(
			'name' => __( 'Footer %d', 'coupon' ),
			'description'   => __( 'Appears at the top of the footer.', 'coupon' ),
			'id' => 'footer-first',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}

	// Custom sidebars
	if ( !empty( $mts_options['mts_custom_sidebars'] ) && is_array( $mts_options['mts_custom_sidebars'] )) {
		foreach( $mts_options['mts_custom_sidebars'] as $sidebar ) {
			if ( !empty( $sidebar['mts_custom_sidebar_id'] ) && !empty( $sidebar['mts_custom_sidebar_id'] ) && $sidebar['mts_custom_sidebar_id'] != 'sidebar-' ) {
				register_sidebar( array( 'name' => ''.$sidebar['mts_custom_sidebar_name'].'', 'id' => ''.sanitize_title( strtolower( $sidebar['mts_custom_sidebar_id'] )).'', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ));
			}
		}
	}

	if ( mts_is_wc_active() ) {
		// Register WooCommerce Shop and Single Product Sidebar
		register_sidebar( array(
			'name' => __('Shop Page Sidebar', 'coupon' ),
			'description'   => __( 'Appears on Shop main page and product archive pages.', 'coupon' ),
			'id' => 'shop-sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
		register_sidebar( array(
			'name' => __('Single Product Sidebar', 'coupon' ),
			'description'   => __( 'Appears on single product pages.', 'coupon' ),
			'id' => 'product-sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'mts_register_sidebars' );

function mts_register_extra_sidebars() {
	$mts_options = get_option( MTS_THEME_NAME );
	if (!empty($mts_options['mts_coupon_archive_widgets_enabled']) && !empty($mts_options['mts_coupon_archive_widgets'])) {
		foreach( $mts_options['mts_coupon_archive_widgets'] as $category_id => $v ) {
			$term = get_term($category_id, 'mts_coupon_categories');
			if ( $term ) {
				register_sidebar( array(
					'name' => __('Sidebar - ','coupon').$term->name.__(' Archive','coupon'),
					'description'   => __( 'Appears on specific Coupons category archive.', 'coupon' ),
					'id' => 'sidebar-coupons-'.$category_id,
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h3>',
					'after_title' => '</h3>'
				) );
			}
		}
	}
}
add_action( 'init', 'mts_register_extra_sidebars', 10 ); // can't run this on 'widgets_init' :/

function register_extra_tag_sidebars() {
	$mts_options = get_option( MTS_THEME_NAME );
	if (!empty($mts_options['coupon_archive_tag_widgets_enabled']) && !empty($mts_options['coupon_archive_tag_widgets'])) {
		foreach( $mts_options['coupon_archive_tag_widgets'] as $tag_id => $v ) {
			$term = get_term($tag_id, 'mts_coupon_tag');
			if ( $term ) {
				register_sidebar( array(
					'name' => __('Sidebar - ','coupon').$term->name.__(' Tag Archive','coupon'),
					'description'   => __( 'Appears on specific Coupons tag archive.', 'coupon' ),
					'id' => 'sidebar-coupons-'.$tag_id,
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h3>',
					'after_title' => '</h3>'
				) );
			}
		}
	}
}
add_action( 'init', 'register_extra_tag_sidebars', 10 ); // can't run this on 'widgets_init' :/

/**
 * Retrieve the ID of the sidebar to use on the active page.
 *
 * @return string
 */
function mts_custom_sidebar() {
	$mts_options = get_option( MTS_THEME_NAME );

	// Default sidebar.
	$sidebar = 'sidebar';

	if ( is_home() && !empty( $mts_options['mts_sidebar_for_home'] )) $sidebar = $mts_options['mts_sidebar_for_home'];
	if ( is_single() && !empty( $mts_options['mts_sidebar_for_post'] )) $sidebar = $mts_options['mts_sidebar_for_post'];
	if ( is_page() && !empty( $mts_options['mts_sidebar_for_page'] )) $sidebar = $mts_options['mts_sidebar_for_page'];

	// Archives.
	if ( is_archive() && !empty( $mts_options['mts_sidebar_for_archive'] )) $sidebar = $mts_options['mts_sidebar_for_archive'];
	if ( is_category() && !empty( $mts_options['mts_sidebar_for_category'] )) $sidebar = $mts_options['mts_sidebar_for_category'];
	if ( is_tag() && !empty( $mts_options['mts_sidebar_for_tag'] )) $sidebar = $mts_options['mts_sidebar_for_tag'];
	if ( is_date() && !empty( $mts_options['mts_sidebar_for_date'] )) $sidebar = $mts_options['mts_sidebar_for_date'];
	if ( is_author() && !empty( $mts_options['mts_sidebar_for_author'] )) $sidebar = $mts_options['mts_sidebar_for_author'];

	// Other.
	if ( is_search() && !empty( $mts_options['mts_sidebar_for_search'] )) $sidebar = $mts_options['mts_sidebar_for_search'];
	if ( is_404() && !empty( $mts_options['mts_sidebar_for_notfound'] )) $sidebar = $mts_options['mts_sidebar_for_notfound'];

	// Coupon Archive
	if ( is_tax('mts_coupon_categories') ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		if ( is_active_sidebar('sidebar-coupons-'.$term->term_id) ) $sidebar = 'sidebar-coupons-'.$term->term_id;
		elseif ( is_active_sidebar('sidebar-coupons') ) $sidebar = 'sidebar-coupons';

		if (!empty( $mts_options['mts_sidebar_for_coupons_archive'] )) $sidebar = $mts_options['mts_sidebar_for_coupons_archive'];
	} elseif(is_post_type_archive('coupons') || is_tax('mts_coupon_tag') ) {
		$sidebar = 'sidebar-coupons';
		if (!empty( $mts_options['mts_sidebar_for_coupons_archive'] )) $sidebar = $mts_options['mts_sidebar_for_coupons_archive'];
	}

	if ( is_tax('mts_coupon_tag') ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		if ( is_active_sidebar('sidebar-coupons-'.$term->term_id) ) $sidebar = 'sidebar-coupons-'.$term->term_id;
		elseif ( is_active_sidebar('sidebar-coupons') ) $sidebar = 'sidebar-coupons';
	}

	// Single Coupon Page
	if (is_singular('coupons')) {
		$sidebar = 'sidebar-single-coupon';
		if (!empty( $mts_options['mts_sidebar_for_coupons'] )) $sidebar = $mts_options['mts_sidebar_for_coupons'];
	}

	// Woocommerce.
	if ( mts_is_wc_active() ) {
		if ( is_shop() || is_product_taxonomy() ) {
			$sidebar = 'shop-sidebar';
			if ( !empty( $mts_options['mts_sidebar_for_shop'] )) {
				$sidebar = $mts_options['mts_sidebar_for_shop'];
			}
		}
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$sidebar = 'product-sidebar';
			if ( !empty( $mts_options['mts_sidebar_for_product'] )) {
				$sidebar = $mts_options['mts_sidebar_for_product'];
			}
		}
	}

	// Page/post specific custom sidebar-
	if ( is_page() || is_single() ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
		$custom = get_post_meta( get_the_ID(), '_mts_custom_sidebar', true );
		if ( !empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	// Posts page
	if ( is_home() && ! is_front_page() && 'page' == get_option( 'show_on_front' ) ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
		$custom = get_post_meta( get_option( 'page_for_posts' ), '_mts_custom_sidebar', true );
		if ( !empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	return apply_filters( 'mts_custom_sidebar', $sidebar );
}

/*-----------------------------------------------------------------------------------*/
/*  Load Widgets, Actions and Libraries
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget.
include_once( get_theme_file_path( "functions/widget-ad125.php" ) );

// Add the 300x250 Ad Block Custom Widget.
include_once( get_theme_file_path( "functions/widget-ad300.php" ) );

// Add the 728x90 Ad Block Custom Widget.
include_once( get_theme_file_path( "functions/widget-ad728.php" ) );

// Add the Latest Tweets Custom Widget.
include_once( get_theme_file_path( "functions/widget-tweets.php" ) );

// Add Recent Posts Widget.
include_once( get_theme_file_path( "functions/widget-recentposts.php" ) );

// Add Related Posts Widget.
include_once( get_theme_file_path( "functions/widget-relatedposts.php" ) );

// Add Author Posts Widget.
include_once( get_theme_file_path( "functions/widget-authorposts.php" ) );

// Add Popular Posts Widget.
include_once( get_theme_file_path( "functions/widget-popular.php" ) );

// Add Facebook Like box Widget.
include_once( get_theme_file_path( "functions/widget-fblikebox.php" ) );

// Add Social Profile Widget.
include_once( get_theme_file_path( "functions/widget-social.php" ) );

// Add Category Posts Widget.
include_once( get_theme_file_path( "functions/widget-catposts.php" ) );

// Add Category Posts Widget.
include_once( get_theme_file_path( "functions/widget-postslider.php" ) );

// Add Adcode Widget.
include_once( get_theme_file_path( "functions/widget-adcode.php" ) );

// Add Welcome message.
include_once( get_theme_file_path( "functions/welcome-message.php" ) );

// Template Functions.
include_once( get_theme_file_path( "functions/theme-actions.php" ) );

// Post/page editor meta boxes.
include_once( get_theme_file_path( "functions/metaboxes.php" ) );

// TGM Plugin Activation.
include_once( get_theme_file_path( "functions/plugin-activation.php" ) );

// AJAX Contact Form - `mts_contact_form()`.
include_once( get_theme_file_path( 'functions/contact-form.php' ) );

// Custom menu walker.
include_once( get_theme_file_path( 'functions/nav-menu.php' ) );

// Rank Math SEO.
include_once( get_theme_file_path( 'functions/rank-math-notice.php' ) );

// Coupon Widgets Add Coupon Popular Posts Widget.
include_once( get_theme_file_path( 'functions/widget-coupon-popularposts.php' ) );

// Add Coupon Recent Posts Widget.
include_once( get_theme_file_path( 'functions/widget-coupon-recentposts.php' ) );

// Add Coupon Category Posts Widget.
include_once( get_theme_file_path( 'functions/widget-coupon-catposts.php' ) );

// Add Coupon Categories Widget.
include_once( get_theme_file_path( 'functions/widget-coupon-categories.php' ) );

// Add Site Stats Widget.
include_once( get_theme_file_path( 'functions/widget-site-stats.php' ) );

// Add Coupon App Widget.
include_once( get_theme_file_path( 'functions/widget-coupon-app.php' ) );

// Add Brand Info Widget.
include_once( get_theme_file_path( 'functions/widget-coupon-brand-info.php' ) );

/*-----------------------------------------------------------------------------------*/
/* RTL
/*-----------------------------------------------------------------------------------*/
if ( ! empty( $mts_options['mts_rtl'] ) ) {
	/**
	 * RTL language support
	 *
	 * @see mts_load_footer_scripts()
	 */
	function mts_rtl() {
		if ( is_admin() ) {
			return;
		}
		global $wp_locale, $wp_styles;
		$wp_locale->text_direction = 'rtl';
		if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
			$wp_styles = new WP_Styles();
			$wp_styles->text_direction = 'rtl';
		}
	}
	add_action( 'init', 'mts_rtl' );
}

/**
 * Replace `no-js` with `js` from the body's class name.
 */
function mts_nojs_js_class() {
	echo '<script type="text/javascript">document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/,\'js\' );</script>';
}
add_action( 'wp_head', 'mts_nojs_js_class', 1 );

/**
 * Enqueue .js files.
 */
function mts_add_scripts() {
	$mts_options = get_option( MTS_THEME_NAME );

	wp_enqueue_script( 'jquery' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script( 'customscript', get_template_directory_uri() . '/js/customscript.js', array( 'jquery' ), MTS_THEME_VERSION, true );
	if ( ! empty( $mts_options['mts_show_primary_nav'] ) && ! empty( $mts_options['mts_show_secondary_nav'] ) ) {
		$nav_menu = 'both';
	} else {
		$nav_menu = 'none';

		if ( ! empty( $mts_options['mts_show_primary_nav'] ) ) {
			$nav_menu = 'primary';
		} elseif ( ! empty( $mts_options['mts_show_secondary_nav'] ) ) {
			$nav_menu = 'secondary';
		}
	}
	wp_localize_script(
		'customscript',
		'mts_customscript',
		array(
			'responsive' => ( empty( $mts_options['mts_responsive'] ) ? false : true ),
			'nav_menu' => $nav_menu,
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'deal_url' => get_post_meta( get_the_ID(), 'mts_coupon_deal_URL', 1 ),
			'deal_activate_loading' => __( 'Loading', 'coupon' ),
			'deal_activate_done' => __( 'Activated', 'coupon' ),
			'coupon_button_action' => ( isset( $mts_options['mts_coupon_button_action'] ) ? $mts_options['mts_coupon_button_action'] : 'popup' ),
			'launch_popup' => (!empty($_POST['show_deal']) ? (int) $_POST['show_deal'] : 0),
			'copied_msg' => __( 'Code Copied!', 'coupon' ),
		)
	);
	wp_enqueue_script( 'customscript' );

	// Slider
	wp_register_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array(), null, true);
	wp_localize_script('owl-carousel', 'slideropts', array('rtl_support' => $mts_options['mts_rtl'], 'fade_effect' => $mts_options['mts_slider_animation']));
	wp_enqueue_script ('owl-carousel');

	// Animated single post/page header
	if ( is_singular() ) {
		$header_animation = mts_get_post_header_effect();
		if ( 'parallax' == $header_animation ) {
			wp_enqueue_script( 'jquery-parallax', get_template_directory_uri() . '/js/parallax.js', array( 'jquery' ) );
		} else if ( 'zoomout' == $header_animation ) {
			wp_enqueue_script( 'jquery-zoomout', get_template_directory_uri() . '/js/zoomout.js', array( 'jquery' ) );
		}
	}

	wp_enqueue_script('clipboardjs', get_template_directory_uri() . '/js/clipboard.min.js', array(), null, true);

	//Lightbox
	if ( $mts_options['mts_lightbox'] || is_page_template( 'page-blog.php' ) || get_post_type() == 'coupons' || is_page_template('page-coupons-home.php') ) {
		wp_enqueue_script( 'magnificPopup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), false, true );
	}

	//Sticky Nav
	if ( ! empty( $mts_options['mts_sticky_nav'] ) ) {
		wp_enqueue_script( 'StickyNav', get_template_directory_uri() . '/js/sticky.js', array( 'jquery' ), false, true );
	}

	// Lazy Load
	if ( ! empty( $mts_options['mts_lazy_load'] ) ) {
		if ( ! empty( $mts_options['mts_lazy_load_thumbs'] ) || ( ! empty( $mts_options['mts_lazy_load_content'] ) && is_singular() ) ) {
			wp_enqueue_script( 'layzr', get_template_directory_uri() . '/js/layzr.min.js', array( 'jquery' ), false, true );
		}
	}

	// Ajax Load More and Search Results
	wp_register_script( 'mts_ajax', get_template_directory_uri() . '/js/ajax.js', true );

	if ( is_tax('mts_coupon_categories') || is_tax('mts_coupon_tag') || is_post_type_archive('coupons') || is_page_template('page-coupons-home.php') ) {
		$ajax_pagination = isset($mts_options['mts_coupon_pagenavigation_type']) && !empty( $mts_options['mts_coupon_pagenavigation_type'] ) && $mts_options['mts_coupon_pagenavigation_type'] >= 2;
		$pagenavigation_type = 'mts_coupon_pagenavigation_type';
	} else {
		$ajax_pagination = isset($mts_options['mts_pagenavigation_type']) && !empty( $mts_options['mts_pagenavigation_type'] ) && $mts_options['mts_pagenavigation_type'] >= 2 && ( !is_singular() || is_page_template('page-blog.php') );
		$pagenavigation_type = 'mts_pagenavigation_type';
	}

	if ( $ajax_pagination ) {
		wp_enqueue_script( 'mts_ajax' );

		wp_enqueue_script( 'historyjs', get_template_directory_uri() . '/js/history.js' );

		// Add parameters for the JS
		global $wp_query;
        $max = $wp_query->max_num_pages;
        $paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
        if ( $max == 0 ) {
        	if ( is_tax('mts_coupon_categories') || is_tax('mts_coupon_tag') || is_post_type_archive('coupons') || is_page_template('page-coupons-home.php') ) {
        		$my_query = new WP_Query(
	                array(
	                    'post_type' => 'coupons',
						'post_status' => 'publish',
						'paged' => $paged,
						'orderby' => 'post_date',
						'coupons_template' => 1,
	                )
	            );
        	} else {
	            $my_query = new WP_Query(
	                array(
	                    'post_type' => 'post',
	                    'post_status' => 'publish',
	                    'paged' => $paged,
	                    'ignore_sticky_posts'=> 1
	                )
	            );
	        }
            $max = $my_query->max_num_pages;
            wp_reset_postdata();
        }
        $autoload = ( $mts_options[$pagenavigation_type] == 3 );
		wp_localize_script(
			'mts_ajax',
			'mts_ajax_loadposts',
			array(
				'startPage' => $paged,
				'maxPages' => $max,
				'nextLink' => next_posts( $max, false ),
				'autoLoad' => $autoload,
				'i18n_loadmore' => __( 'Load more', 'coupon' ),
				'i18n_loading' => __('Loading...', 'coupon' ),
				'i18n_nomore' => __( 'No more posts.', 'coupon' )
			 )
		);
	}
	if ( ! empty( $mts_options['mts_ajax_search'] ) ) {
		wp_enqueue_script( 'mts_ajax' );
		wp_localize_script(
			'mts_ajax',
			'mts_ajax_search',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
				'ajax_search' => '1'
			 )
		);
	}

}
add_action( 'wp_enqueue_scripts', 'mts_add_scripts' );

/**
 * Load CSS files.
 */
function mts_enqueue_css() {
	global $post;
	$mts_options = get_option( MTS_THEME_NAME );

	wp_enqueue_style( 'coupon-stylesheet', get_stylesheet_uri() );

	// Slider ( also enqueued in slider widget )
	if( !empty( $mts_options['mts_custom_slider'] ) || !empty( $mts_options['mts_custom_carousel'] ) || !empty( $mts_options['mts_blog_carousel'] ) || !empty( $mts_options['mts_coupon_archive_carousel'] ) || !empty( $mts_options['mts_single_carousel'] ) ) {
		wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), null);
	}

	$handle = 'coupon-stylesheet';

	// RTL
	if ( ! empty( $mts_options['mts_rtl'] ) ) {
		wp_enqueue_style( 'mts_rtl', get_template_directory_uri() . '/css/rtl.css', array( $handle ) );
	}

	// Responsive
	if ( ! empty( $mts_options['mts_responsive'] ) ) {
		wp_enqueue_style( 'responsive', get_template_directory_uri() . '/css/responsive.css', array( $handle ) );
	}

	// WooCommerce
	if ( mts_is_wc_active() ) {
		if ( empty( $mts_options['mts_optimize_wc'] ) || ( ! empty( $mts_options['mts_optimize_wc'] ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) ) {
			wp_enqueue_style( 'woocommerce', get_template_directory_uri() . '/css/woocommerce2.css' );
			$handle = 'woocommerce';
		}
	}

	// Lightbox
	if ( $mts_options['mts_lightbox'] || is_page_template( 'page-blog.php' ) || get_post_type() == 'coupons' || is_page_template('page-coupons-home.php') ) {
		wp_enqueue_style( 'magnificPopup', get_template_directory_uri() . '/css/magnific-popup.css' );
	}

	// Font Awesome
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css' );

	$mts_sclayout = '';
	$mts_shareit_left = '';
	$mts_shareit_right = '';
	$mts_author = '';
	$mts_header_section = '';
	$mts_social_icons = '';
	$mts_footer_icons = '';
	$home_coupon_categories = '';
	$carousel_style = '';
	$store_style = '';
	$social_icons_title = '';
	$mts_carousel_color = '';
	$mts_sidebar_location = '';

	if ( is_page() || is_single() ) {
		$mts_sidebar_location = get_post_meta( get_the_ID(), '_mts_sidebar_location', true );
	}
	if ( $mts_sidebar_location != 'left' && ( $mts_options['mts_layout'] == 'cslayout' || $mts_sidebar_location == 'right' )) {
		$mts_sclayout = '.article { float: left;}
		.sidebar.c-4-12 { float: right; }';
		if( isset( $mts_options['mts_social_button_position'] ) && $mts_options['mts_social_button_position'] == 'floating' ) {
			$mts_shareit_right = '.shareit { margin: 0; border-left: 0; margin-left: -95px; }';
		}
	}
	if ( empty( $mts_options['mts_header_section2'] ) ) {
		$mts_header_section = '.logo-wrap, .widget-header { display: none; }
		.navigation { border-top: 0; }
		#header { min-height: 47px; }';
	}
	if ( isset( $mts_options['mts_social_button_position'] ) && $mts_options['mts_social_button_position'] == 'floating' ) {
		$mts_shareit_left = '.shareit { top: 282px; right: auto; width: 90px; position: fixed; padding: 5px; border:none; border-right: 0; margin: 0 0 0 875px; } .share-item {margin: 2px;} .ss-full-width .shareit { margin: 0 0 0 1190px; }';
	}
	if ( ! empty( $mts_options['mts_author_comment'] ) ) {
		$mts_author = '.comment.bypostauthor > .comment-list .fn:after { content: "'.__( 'Author', 'coupon' ).'"; padding: 1px 10px; background: #28202e; color: #FFF; margin: 0 5px; }';
	}

	if ( !empty( $mts_options['mts_social_icons'] ) ) {
		foreach( $mts_options['mts_social_icons'] as $social_icon ) :
			$icons = $social_icon['mts_social_icon'];
			$hcolor = $social_icon['mts_social_icon_hcolor'];
			$mts_social_icons .= '.promote-social a.promote-' . $icons . ':hover { background:' . $hcolor . '; border-color:' . $hcolor . ';} ';
		endforeach;
	}

	if ( !empty( $mts_options['mts_footer_social'] ) ) {
		foreach( $mts_options['mts_footer_social'] as $footer_icon ) :
			$icons = $footer_icon['mts_footer_social_icon'];
			$hcolor = $footer_icon['mts_footer_social_icon_hcolor'];
			$mts_social_icons .= '.footer-social a.footer-'. $icons.':hover { background:' . $hcolor . '; border-color:' . $hcolor . ';} ';
		endforeach;
	}

	if ( !empty( $mts_options['mts_custom_carousel'] ) ) {
		$count = 0;
		foreach( $mts_options['mts_custom_carousel'] as $slide ) :
			$bordercolor = $slide['mts_custom_carousel_border'];
			$bgcolor = $slide['mts_custom_carousel_background'];
			$textcolor = $slide['mts_custom_carousel_text'];
			$mts_carousel_color .= '.coupon-carousel.owl-carousel .owl-item-carousel.count-' . ++$count . ' { border-color:' . $bordercolor . '; background:' . $bgcolor . ';} .coupon-carousel.owl-carousel .owl-item-carousel.count-' . $count . ' .slide-title { color:' .$textcolor. ';} .coupon-carousel.owl-carousel .owl-item-carousel:after{ color: ' . $bordercolor . '}';
		endforeach;
	}

	if( !empty( $mts_options['mts_store_group'] ) ) {
		$i = 1;
		foreach ( $mts_options['mts_store_group'] as $section ) :
			//Color
			$bg_color = $section['mts_store_item_hover_bg'];
			$border_color = $section['mts_store_item_border'];
			$rgb = mts_hextorgb($bg_color);
			//Image
			$img = $section['mts_store_item_image'];
			if( $img ) {
				$home_coupon_categories .= '.popular-store li.popular-cat-'.$i.' .cat-img{ background: url('. $img .') no-repeat center center; background-size: contain; } ';
			}

			if( $bg_color ){
				$home_coupon_categories .= '.popular-store li.popular-cat-'.$i.':hover .cat-caption { background:rgba( '. $rgb .' , 0.8)} .popular-store li.popular-cat-'.$i.':hover { border-color: '.$bg_color.'}';
			}

			$home_coupon_categories .= '.popular-store li.popular-cat-'.$i.' { border-color: '.$border_color.'}';

			$i++;
		endforeach;
	}

	if( !isset( $mts_options['mts_carousel_title'] ) && empty( $mts_options['mts_carousel_title'] ) ) {
		$carousel_style = '.coupon-carousel-container { margin-top: 10px; }
		.coupon-carousel-container .owl-prev, .coupon-carousel-container .owl-next { top: 50%; }
		.coupon-carousel-container .owl-prev { right: auto; left: 0; }';
	}

	if( !isset( $mts_options['mts_store_title'] ) && empty( $mts_options['mts_store_title'] ) ) {
		$store_style = '.popular-store { margin-top: 10px; }';
	}

	if( empty( $mts_options['mts_social_icons_title'] ) ) {
		$social_icons_title = '.promote { padding: 50px 0 50px 0; }';
	}
	$popup_button_color = '';
 	if(function_exists('wps_get_options')) {
 		$subscribe_options = wps_get_options();
 		if(isset($subscribe_options['popup_form_colors']) && isset($subscribe_options['popup_form_colors']['button_text_color'])) {
 			$popup_button_color = '#wp-subscribe .submit {color:'.$subscribe_options['popup_form_colors']['button_text_color'].' !important;}';
 		}
 	}
	$mts_bg = mts_get_background_styles( 'mts_background' );
	$mts_navigation_background = mts_get_background_styles( 'mts_navigation_background' );
	$mts_topbar_background = mts_get_background_styles( 'mts_topbar_background' );
	$mts_slider_background = mts_get_background_styles( 'mts_slider_background' );
	$mts_tabs_background = mts_get_background_styles( 'mts_tabs_background' );
	$mts_social_icons_background = mts_get_background_styles( 'mts_social_icons_background' );
	$mts_subscribe_background = mts_get_background_styles( 'mts_subscribe_background' );
	$mts_signup_background = mts_get_background_styles( 'mts_signup_background' );
	$mts_footer_background = mts_get_background_styles( 'mts_footer_background' );
	$mts_copyrights_background = mts_get_background_styles( 'mts_copyrights_background' );
	$dark_color = '#'.mts_darken_color($mts_options['mts_color_scheme'],12);
	$darken_color = '#'.mts_darken_color($mts_options['mts_color_scheme'],19);
	$mts_custom_css = '';
	if( isset( $mts_options['mts_custom_css'] ) && !empty( $mts_options['mts_custom_css'] ) ) {
		$mts_custom_css = $mts_options['mts_custom_css'];
	}
	$custom_css = "
		body {{$mts_bg}}
		.promote {{$mts_social_icons_background}}
		.signup {{$mts_signup_background}}
		#slider-nav {{$mts_slider_background}}
		section#tabs {{$mts_tabs_background}}
		#header {{$mts_topbar_background}}
		#primary-navigation, .navigation ul ul li, .navigation.mobile-menu-wrapper {{$mts_navigation_background}}
		.subscribe-container {{$mts_subscribe_background}}
		#site-footer {{$mts_footer_background}}
		.copyrights {{$mts_copyrights_background}}

		.copyrights a, .single_post a:not(.wp-block-button__link):not(.wp-block-file__button), #site-footer .textwidget a, .textwidget a, .pnavigation2 a, #sidebar a:hover, .copyrights a:hover, #site-footer .widget li a:hover, #site-footer .widget li:hover > a, .widget li:hover > .toggle-caret, #sidebar .widget li:hover > a, #sidebar .widget_categories li > a:hover, .related-posts a:hover, .title a:hover, .post-info a:hover, .comm, #tabber .inside li a:hover, .readMore a:hover, .fn a, a, a:hover, .article .featured-category-title a:hover, .latestPost .title a:hover, .related-posts .latestPost .title a:hover, #header .header-login a:hover, .coupon-carousel.owl-carousel .owl-item-carousel:hover:after, .latestPost .post-info .thetime .day, .widget .coupon-featuredtext, .coupon_extra_rewards, .color, .featured-category-title span, .total-comments span, .footer-widgets .widget_archive li:hover:before, .footer-widgets .widget_categories li:hover:before, .footer-widgets .widget_pages li:hover:before, .footer-widgets .widget_meta li:hover:before, .footer-widgets .widget_recent_comments li:hover:before, .footer-widgets .widget_recent_entries li:hover:before, .footer-widgets .widget_nav_menu li:hover:before { color:{$mts_options['mts_color_scheme']}; }

		.pace .pace-progress, #mobile-menu-wrapper ul li a:hover, .ball-pulse > div, .navigation ul li:hover > a:before, .navigation ul .current-menu-item > a:before, .owl-prev, .owl-next, #move-to-top, .signup-button a, .postauthor .author-posts, #commentform input#submit, .contact-form input[type=\"submit\"], .deal-button, .deal-button.show-coupon-button, .widget-button, .coupon_deal_URL, .woocommerce-product-search input[type='submit'], input[type='submit'], .woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce #content nav.woocommerce-pagination ul li span.current, .woocommerce-page #content nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus, .pagination  .nav-previous a:hover, .pagination .nav-next a:hover, .currenttext, .pagination a:hover, .single .pagination a:hover .currenttext, .page-numbers.current, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .reply a, #sidebar .wpt_widget_content #tags-tab-content ul li a:hover, #commentform input#submit, .contactform #submit, #searchform .fa-search, #tabber ul.tabs li a.selected, .tagcloud a:hover, .navigation ul .sfHover a, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce .bypostauthor:after, #searchsubmit, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .footer-widgets .wpt_widget_content .tab_title.selected a, .footer-widgets .wp_review_tab_widget_content .tab_title.selected a, .woocommerce-account .woocommerce-MyAccount-navigation li.is-active, .woocommerce-product-search button[type='submit'], .woocommerce .woocommerce-widget-layered-nav-dropdown__submit { background-color:{$mts_options['mts_color_scheme']}; color: #fff!important; }

		.wpmm-megamenu-showing.wpmm-light-scheme, .subscribe-container #wp-subscribe input.submit { background-color:{$mts_options['mts_color_scheme']}!important; }

		.coupon-carousel.owl-carousel .owl-item-carousel[class*='count-']:hover, #slider-nav .slider-nav-item.active, .tabs-container .tabs-menu li.current, .tab-post:hover .dashed-button, .coupon-related-post:hover .dashed-button, .dashed-button:hover, .coupon-featured:hover, .woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce #content nav.woocommerce-pagination ul li span.current, .woocommerce-page #content nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus, .pagination  .nav-previous a:hover, .pagination .nav-next a:hover, .pagination a:hover, .currenttext, .pagination a:hover, .single .pagination a:hover .currenttext, .page-numbers.current, input[type='submit'] { border-color:{$mts_options['mts_color_scheme']}; }

		.signup .signup-title { color:{$mts_options['mts_signup_title_color']}; }

		.deal-button.show-coupon-button { border-color: {$dark_color}!important; }
		.code-button-bg { background: {$darken_color}; }

		{$mts_sclayout}
		{$mts_shareit_left}
		{$mts_shareit_right}
		{$mts_author}
		{$mts_header_section}
		{$mts_social_icons}
		{$mts_footer_icons}
		{$home_coupon_categories}
		{$carousel_style}
		{$store_style}
		{$social_icons_title}
		{$mts_carousel_color}
		{$popup_button_color}
		{$mts_custom_css}
			";
	wp_add_inline_style( $handle, $custom_css );
}
add_action( 'wp_enqueue_scripts', 'mts_enqueue_css', 99 );

/**
 * Wrap videos in .responsive-video div
 *
 * @param $html
 * @param $url
 * @param $attr
 *
 * @return string
 */
function mts_responsive_video( $html, $url, $attr ) {

	// Only video embeds
	$video_providers = array(
		'youtube',
		'vimeo',
		'dailymotion',
		'wordpress.tv',
		'vine.co',
		'animoto',
		'blip.tv',
		'collegehumor.com',
		'funnyordie.com',
		'hulu.com',
		'revision3.com',
		'ted.com',
	);

	// Allow user to wrap other embeds
	$providers = apply_filters('mts_responsive_video', $video_providers );

	foreach ( $providers as $provider ) {
		if ( strstr($url, $provider) ) {
			$html = '<div class="flex-video flex-video-' . sanitize_html_class( $provider ) . '">' . $html . '</div>';
			break;// Break if video found
		}
	}

	return $html;
}
add_filter( 'embed_oembed_html', 'mts_responsive_video', 99, 3 );

if ( ! function_exists( 'mts_comments' ) ) {
	/**
	 * Custom comments template.
	 * @param $comment
	 * @param $args
	 * @param $depth
	 */
	function mts_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		$mts_options = get_option( MTS_THEME_NAME ); ?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			<?php
			switch( $comment->comment_type ) :
				case 'pingback':
				case 'trackback': ?>
					<div id="comment-<?php comment_ID(); ?>" class="comment-list">
						<div class="comment-author vcard">
							<?php _e('Pingback:','coupon'); ?> <?php comment_author_link(); ?>
							<?php if ( ! empty( $mts_options['mts_comment_date'] ) ) { ?>
								<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
							<?php } ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'coupon' ), '  ', '' ) ?>
							</span>
						</div>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'coupon' ) ?></em>
							<br />
						<?php endif; ?>
					</div>
				<?php
					break;

				default: ?>
					<div id="comment-<?php comment_ID(); ?>" class="comment-list" itemscope itemtype="http://schema.org/UserComments">
						<div class="comment-author vcard">
							<?php echo get_avatar( $comment->comment_author_email, 100 ); ?>
							<?php printf( '<span class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person"><span itemprop="name">%s</span></span>', get_comment_author_link() ) ?>
							<?php if ( ! empty( $mts_options['mts_comment_date'] ) ) { ?>
								<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
							<?php } ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'coupon' ), '  ', '' ) ?>
							</span>
						</div>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'coupon' ) ?></em>
							<br />
						<?php endif; ?>
						<div class="commentmetadata">
							<div class="commenttext" itemprop="commentText">
								<?php comment_text() ?>
							</div>
							<div class="reply">
								<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => 'Reply <i class="fa fa-angle-right"></i>' )) ) ?>
							</div>
						</div>
					</div>
				<?php
				   break;
			 endswitch; ?>
		<!-- WP adds </li> -->
	<?php }
}

/**
 * Increase excerpt length to 100.
 *
 * @param $length
 *
 * @return int
 */
function mts_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'mts_excerpt_length', 20 );

/**
 * Remove [...] and shortcodes
 *
 * @param $output
 *
 * @return string
 */
function mts_custom_excerpt( $output ) {
  return preg_replace( '/\[[^\]]*]/', '', $output );
}
add_filter( 'get_the_excerpt', 'mts_custom_excerpt' );

/**
 * Truncate string to x letters/words.
 *
 * @param $str
 * @param int $length
 * @param string $units
 * @param string $ellipsis
 *
 * @return string
 */
function mts_truncate( $str, $length = 40, $units = 'letters', $ellipsis = '&nbsp;&hellip;' ) {
	if ( $units == 'letters' ) {
		if ( mb_strlen( $str ) > $length ) {
			return mb_substr( $str, 0, $length ) . $ellipsis;
		} else {
			return $str;
		}
	} else {
		return wp_trim_words( $str, $length, $ellipsis );
	}
}

if ( ! function_exists( 'mts_excerpt' ) ) {
	/**
	 * Get HTML-escaped excerpt up to the specified length.
	 *
	 * @param int $limit
	 *
	 * @return string
	 */
	function mts_excerpt( $limit = 40 ) {
	  return esc_html( mts_truncate( get_the_excerpt(), $limit, 'words' ) );
	}
}

/**
 * Change the "read more..." link to "".
 * @param $more_link
 * @param $more_link_text
 *
 * @return string
 */
function mts_remove_more_link( $more_link, $more_link_text ) {
	return '';
}
add_filter( 'the_content_more_link', 'mts_remove_more_link', 10, 2 );

if ( ! function_exists( 'mts_post_has_moretag' ) ) {
	/**
	 * Shorthand function to check for more tag in post.
	 *
	 * @return bool|int
	 */
	function mts_post_has_moretag() {
		$post = get_post();
		return preg_match( '/<!--more(.*?)?-->/', $post->post_content );
	}
}

if ( ! function_exists( 'mts_readmore' ) ) {
	/**
	 * Display a "read more" link.
	 */
	function mts_readmore() {
		?>
		<div class="readMore">
			<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
				<?php _e( 'Read More', 'coupon' ); ?>
			</a>
		</div>
		<?php
	}
}

/**
 * Exclude trackbacks from the comment count.
 *
 * @param $count
 *
 * @return int
 */
function mts_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$comments = get_comments( 'status=approve&post_id=' . $id );
		$comments_by_type = separate_comments( $comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}
add_filter( 'get_comments_number', 'mts_comment_count', 0 );

/**
 * Add `has_thumb` to the post's class name if it has a thumbnail.
 *
 * @param $classes
 *
 * @return array
 */
function has_thumb_class( $classes ) {
	if( has_post_thumbnail( get_the_ID() ) ) { $classes[] = 'has_thumb'; }
		return $classes;
}
add_filter( 'post_class', 'has_thumb_class' );

/*-----------------------------------------------------------------------------------*/
/* Add the title tag for compability with older WP versions.
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function theme_slug_render_title() { ?>
	   <title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php }
	add_action( 'wp_head', 'theme_slug_render_title' );
}

/**
 * Handle AJAX search queries.
 */
if( ! function_exists( 'ajax_mts_search' ) ) {
	function ajax_mts_search() {
		$query = $_REQUEST['q']; // It goes through esc_sql() in WP_Query
		$args = array( 's' => $query, 'posts_per_page' => 3, 'post_status' => 'publish');
		if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] !== '') {
			$args['post_type'] = $_REQUEST['post_type'];
		}
		$search_query = new WP_Query($args);
		$search_count = $search_query->found_posts;
		if ( !empty( $query ) && $search_query->have_posts() ) :
			//echo '<h5>Results for: '. $query.'</h5>';
			echo '<ul class="ajax-search-results">';
			while ( $search_query->have_posts() ) : $search_query->the_post();
				?><li>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>">
						<?php if ( has_post_thumbnail() ) { ?>
							<?php the_post_thumbnail( 'coupon-widgetthumb', array( 'title' => '' ) ); ?>
						<?php } else { ?>
							<img class="wp-post-image" src="<?php echo get_template_directory_uri() . '/images/nothumb-widgetthumb.png'; ?>" alt="<?php echo esc_attr( mts_truncate( get_the_title(), 30 ) ); ?>"/>
						<?php } ?>
						<?php echo mts_truncate( get_the_title(), 30 ); ?>
					</a>
					<div class="meta">
						<span class="thetime"><?php the_time( 'F j, Y' ); ?></span>
					</div> <!-- / .meta -->
				</li>
				<?php
			endwhile;
			echo '</ul>';
			$all_results_url = add_query_arg( array( 'post_type' => 'coupons', 's' => $query), get_site_url() );
			echo '<div class="ajax-search-meta"><span class="results-count">'.$search_count.' '.__( 'Results', 'coupon' ).'</span><a href="'.esc_url( $all_results_url ).'" class="results-link">'.__('Show all results.', 'coupon' ).'</a></div>';
		else:
			echo '<div class="no-results">'.__( 'No results found.', 'coupon' ).'</div>';
		endif;
		wp_reset_postdata();
		exit; // required for AJAX in WP
	}
}
if( !empty( $mts_options['mts_ajax_search'] )) {
	add_action( 'wp_ajax_mts_search', 'ajax_mts_search' );
	add_action( 'wp_ajax_nopriv_mts_search', 'ajax_mts_search' );
}

/**
 *  Filters that allow shortcodes in Text Widgets
 */
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content_rss', 'do_shortcode' );

if ( isset($mts_options['mts_feedburner']) && trim( $mts_options['mts_feedburner'] ) !== '' ) {
	/**
	 * Redirect feed to FeedBurner if a FeedBurner URL has been set.
	 */
	function mts_rss_feed_redirect() {
		$mts_options = get_option( MTS_THEME_NAME );
		global $feed;
		$new_feed = $mts_options['mts_feedburner'];
		if ( !is_feed() ) {
				return;
		}
		if ( preg_match( '/feedburner/i', $_SERVER['HTTP_USER_AGENT'] )){
				return;
		}
		if ( $feed != 'comments-rss2' ) {
				if ( function_exists( 'status_header' )) status_header( 302 );
				header( "Location:" . $new_feed );
				header( "HTTP/1.1 302 Temporary Redirect" );
				exit();
		}
	}
	add_action( 'template_redirect', 'mts_rss_feed_redirect' );
}

/**
 * Single Post Pagination - Numbers + Previous/Next.
 *
 * @param $args
 *
 * @return mixed
 */
function mts_wp_link_pages_args( $args ) {
	global $page, $numpages, $more, $pagenow;
	if ( $args['next_or_number'] != 'next_and_number' ) {
		return $args;
	}

	$args['next_or_number'] = 'number';

	if ( !$more ) {
		return $args;
	}

	if( $page-1 ) {
		$args['before'] .= _wp_link_page( $page-1 )
						. $args['link_before']. $args['previouspagelink'] . $args['link_after'] . '</a>';
	}

	if ( $page<$numpages ) {
		$args['after'] = _wp_link_page( $page+1 )
						 . $args['link_before'] . $args['nextpagelink'] . $args['link_after'] . '</a>'
						 . $args['after'];
	}

	return $args;
}
add_filter( 'wp_link_pages_args', 'mts_wp_link_pages_args' );

/**
 * Remove hentry class from pages
 *
 * @param $classes
 *
 * @return array
 */
function mts_remove_hentry( $classes ) {
	$classes = array_diff( $classes, array( 'hentry' ) );
	return $classes;
}
add_filter( 'post_class','mts_remove_hentry' );

/*-----------------------------------------------------------------------------------*/
/* WooCommerce
/*-----------------------------------------------------------------------------------*/
if ( mts_is_wc_active() ) {
	if ( !function_exists( 'mts_loop_columns' )) {
		/**
		 * Change number or products per row to 3
		 *
		 * @return int
		 */
		function mts_loop_columns() {
			return 3; // 3 products per row
		}
	}
	add_filter( 'loop_shop_columns', 'mts_loop_columns' );

	/**
	 * Redefine woocommerce_output_related_products()
	 */
	if( ! function_exists( 'woocommerce_output_related_products' ) ) {
		function woocommerce_output_related_products() {
			$args = array(
				'posts_per_page' => 3,
				'columns' => 3,
			);
			woocommerce_related_products($args); // Display 3 products in rows of 1
		}
	}

	global $pagenow;
	if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
		/**
		 * Define WooCommerce image sizes.
		 */
		function mts_woocommerce_image_dimensions() {
			$catalog = array(
				'width' 	=> '267',	// px
				'height'	=> '365',	// px
				'crop'		=> 1 		// true
			);
			$single = array(
				'width' 	=> '416',	// px
				'height'	=> '567',	// px
				'crop'		=> 1 		// true
			);
			$thumbnail = array(
				'width' 	=> '95',	// px
				'height'	=> '95',	// px
				'crop'		=> 0 		// false
			);
			// Image sizes
			update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
			update_option( 'shop_single_image_size', $single ); 		// Single product image
			update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
		}
		add_action( 'init', 'mts_woocommerce_image_dimensions', 1 );
	}


	/**
	 * Change the number of product thumbnails to show per row to 4.
	 *
	 * @return int
	 */
	function mts_thumb_cols() {
	 return 4; // .last class applied to every 4th thumbnail
	}
	add_filter( 'woocommerce_product_thumbnails_columns', 'mts_thumb_cols' );

	/**
	 * Change the number of WooCommerce products to show per page.
	 *
	 * @return mixed
	 */
	function mts_products_per_page() {
		$mts_options = get_option( MTS_THEME_NAME );
		return $mts_options['mts_shop_products'];
	}
	add_filter( 'loop_shop_per_page', 'mts_products_per_page', 20 );

	/**
	 * Optimize WooCommerce Scripts
	 * Updated for WooCommerce 2.0+
	 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
	 */
	function mts_child_manage_woocommerce_styles() {
		//remove generator meta tag
		remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

		//first check that woo exists to prevent fatal errors
		if ( function_exists( 'is_woocommerce' ) ) {
			//dequeue scripts and styles
			if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
				wp_dequeue_style( 'woocommerce-layout' );
				wp_dequeue_style( 'woocommerce-smallscreen' );
				wp_dequeue_style( 'woocommerce-general' );
				wp_dequeue_style( 'wc-bto-styles' ); //Composites Styles
				wp_dequeue_script( 'wc-add-to-cart' );
				wp_dequeue_script( 'wc-cart-fragments' );
				wp_dequeue_script( 'woocommerce' );
				wp_dequeue_script( 'jquery-blockui' );
				wp_dequeue_script( 'jquery-placeholder' );
			}
		}
	}
	if ( ! empty( $mts_options['mts_optimize_wc'] ) ) {
		add_action( 'wp_enqueue_scripts', 'mts_child_manage_woocommerce_styles', 99 );
	}

	// Remove WooCommerce generator tag.
	remove_action('wp_head', 'wc_generator_tag');
}

/**
 * Add <!-- next-page --> button to tinymce.
 *
 * @param $mce_buttons
 *
 * @return array
 */
function mts_wysiwyg_editor( $mce_buttons ) {
   $pos = array_search( 'wp_more', $mce_buttons, true );
   if ( $pos !== false ) {
	   $tmp_buttons = array_slice( $mce_buttons, 0, $pos+1 );
	   $tmp_buttons[] = 'wp_page';
	   $mce_buttons = array_merge( $tmp_buttons, array_slice( $mce_buttons, $pos+1 ));
   }
   return $mce_buttons;
}
add_filter( 'mce_buttons', 'mts_wysiwyg_editor' );

/**
 * Get Post header animation.
 *
 * @return string
 */
function mts_get_post_header_effect() {
	$postheader_effect = get_post_meta( get_the_ID(), '_mts_postheader', true );

	return $postheader_effect;
}

/**
 * Add Custom Gravatar Support.
 *
 * @param $avatar_defaults
 *
 * @return mixed
 */
function mts_custom_gravatar( $avatar_defaults ) {
	$mts_avatar = get_template_directory_uri() . '/images/gravatar.png';
	$avatar_defaults[$mts_avatar] = __( 'Custom Gravatar ( /images/gravatar.png )', 'coupon' );
	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'mts_custom_gravatar' );

/**
 * Add `.primary-navigation` the WP Mega Menu's
 * @param $selector
 *
 * @return string
 */
function mts_megamenu_parent_element( $selector ) {
	return '#primary-navigation .container';
}
add_filter( 'wpmm_container_selector', 'mts_megamenu_parent_element' );

/**
 * Change the image size of WP Mega Menu's thumbnails.
 *
 * @param $thumbnail_html
 * @param $post_id
 *
 * @return string
 */
if( ! function_exists( 'mts_megamenu_thumbnails' ) ) {
	function mts_megamenu_thumbnails( $thumbnail_html, $post_id ) {
		$thumbnail_html = '<div class="wpmm-thumbnail">';
		$thumbnail_html .= '<a title="'.get_the_title( $post_id ).'" href="'.get_permalink( $post_id ).'">';
		if(has_post_thumbnail($post_id)):
			$thumbnail_html .= get_the_post_thumbnail($post_id, 'coupon-widgetfull', array('title' => ''));
		else:
			$thumbnail_html .= '<img src="'.get_template_directory_uri().'/images/nothumb-coupon-widgetfull.png" alt="'.__('No Preview', 'coupon').'"  class="wp-post-image" />';
		endif;
		$thumbnail_html .= '</a>';

		// WP Review
		$thumbnail_html .= (function_exists('wp_review_show_total') ? wp_review_show_total(false) : '');

		$thumbnail_html .= '</div>';

		return $thumbnail_html;
	}
}
add_filter( 'wpmm_thumbnail_html', 'mts_megamenu_thumbnails', 10, 2 );

/*-----------------------------------------------------------------------------------*/
/*  WP Review Support
/*-----------------------------------------------------------------------------------*/

/**
 * Set default colors for new reviews.
 *
 * @param $colors
 *
 * @return array
 */
function mts_new_default_review_colors( $colors ) {
	$colors = array(
		'color' => '#FFCA00',
		'fontcolor' => '#fff',
		'bgcolor1' => '#151515',
		'bgcolor2' => '#151515',
		'bordercolor' => '#151515'
	);
  return $colors;
}
add_filter( 'wp_review_default_colors', 'mts_new_default_review_colors' );

/**
 * Set default location for new reviews.
 *
 * @param $position
 *
 * @return string
 */
function mts_new_default_review_location( $position ) {
  $position = 'top';
  return $position;
}
add_filter( 'wp_review_default_location', 'mts_new_default_review_location' );

/**
 * Thumbnail Upscale
 *  Enables upscaling of thumbnails for small media attachments,
 *  to make sure it fits into it's supposed location.
 *
 * @param $default
 * @param $orig_w
 * @param $orig_h
 * @param $new_w
 * @param $new_h
 * @param $crop
 *
 * @return array|null
 */
function mts_image_crop_dimensions( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ) {

	if( !$crop || ($orig_w == 512 && $orig_h == 512) )
		return null; // let the wordpress default function handle this

	$aspect_ratio = $orig_w / $orig_h;
	$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

	$crop_w = round( $new_w / $size_ratio );
	$crop_h = round( $new_h / $size_ratio );

	$s_x = floor( ( $orig_w - $crop_w ) / 2 );
	$s_y = floor( ( $orig_h - $crop_h ) / 2 );

	return array( 0, 0, ( int ) $s_x, ( int ) $s_y, ( int ) $new_w, ( int ) $new_h, ( int ) $crop_w, ( int ) $crop_h );
}
add_filter( 'image_resize_dimensions', 'mts_image_crop_dimensions', 10, 6 );

/*-----------------------------------------------------------------------------------*/
/* Post view count
/* AJAX is used to support caching plugins - it is possible to disable with filter
/* It is also possible to exclude admins with another filter
/*-----------------------------------------------------------------------------------*/

/**
 * Append JS to content for AJAX call on single.
 *
 * @param $content
 *
 * @return string
 */
function mts_view_count_js( $content ) {
	$id = get_the_ID();
	$use_ajax = apply_filters( 'mts_view_count_cache_support', true );

	$exclude_admins = apply_filters( 'mts_view_count_exclude_admins', false ); // pass in true or a user capability
	if ($exclude_admins === true) {
		$exclude_admins = 'edit_posts';
	}
	if ($exclude_admins && current_user_can( $exclude_admins )) {
		return $content; // do not count post views here
	}

	if (is_single()) {
		if ($use_ajax) {
			// enqueue jquery
			wp_enqueue_script( 'jquery' );

			$url = admin_url( 'admin-ajax.php' );
			$content .= "
			<script type=\"text/javascript\">
			jQuery(document).ready(function($) {
				$.post('".esc_js($url)."', {action: 'mts_view_count', id: '".esc_js($id)."'});
			});
			</script>";
		}

		if (!$use_ajax) {
			mts_update_view_count($id);
		}
	}

	return $content;
}

/**
 * Call mts_update_view_count on AJAX.
 */
function mts_ajax_mts_view_count() {
	// do count
	$post_id = absint( $_POST['id'] );
	mts_update_view_count( $post_id );
	exit();
}
add_action('wp_ajax_mts_view_count', 'mts_ajax_mts_view_count');
add_action('wp_ajax_nopriv_mts_view_count','mts_ajax_mts_view_count');

/**
 * Update the view count of a post.
 *
 * @param $post_id
 */
function mts_update_view_count( $post_id ) {
	$count = get_post_meta( $post_id, '_mts_view_count', true );
	update_post_meta( $post_id, '_mts_view_count', ++$count );

	do_action( 'mts_view_count_after_update', $post_id, $count );

	return $count;
}

/**
 * Call mts_activate_deal on AJAX.
 */
function mts_activate_deal() {
	// do count
	$post_id = absint( $_POST['id'] );
	$count = get_post_meta( $post_id, 'mts_coupon_people_used', true );
	if ( empty( $count ) ) $count = 0;
	update_post_meta( $post_id, 'mts_coupon_people_used', ++$count );
	exit();
}
add_action('wp_ajax_mts_activate_deal', 'mts_activate_deal');
add_action('wp_ajax_nopriv_mts_activate_deal','mts_activate_deal');

/**
 * Convert color format from HEX to HSL.
 * @param $color
 *
 * @return array
 */
function mts_hex_to_hsl( $color ){

	// Sanity check
	$color = mts_check_hex_color($color);

	// Convert HEX to DEC
	$R = hexdec($color[0].$color[1]);
	$G = hexdec($color[2].$color[3]);
	$B = hexdec($color[4].$color[5]);

	$HSL = array();

	$var_R = ($R / 255);
	$var_G = ($G / 255);
	$var_B = ($B / 255);

	$var_Min = min($var_R, $var_G, $var_B);
	$var_Max = max($var_R, $var_G, $var_B);
	$del_Max = $var_Max - $var_Min;

	$L = ($var_Max + $var_Min)/2;

	if ($del_Max == 0) {
		$H = 0;
		$S = 0;
	} else {
		if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
		else			$S = $del_Max / ( 2 - $var_Max - $var_Min );

		$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

		if	  ($var_R == $var_Max) $H = $del_B - $del_G;
		else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
		else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

		if ($H<0) $H++;
		if ($H>1) $H--;
	}

	$HSL['H'] = ($H*360);
	$HSL['S'] = $S;
	$HSL['L'] = $L;

	return $HSL;
}

/**
 * Convert color format from HSL to HEX.
 *
 * @param array $hsl
 *
 * @return string
 */
function mts_hsl_to_hex( $hsl = array() ){

	list($H,$S,$L) = array( $hsl['H']/360,$hsl['S'],$hsl['L'] );

	if( $S == 0 ) {
		$r = $L * 255;
		$g = $L * 255;
		$b = $L * 255;
	} else {

		if($L<0.5) {
			$var_2 = $L*(1+$S);
		} else {
			$var_2 = ($L+$S) - ($S*$L);
		}

		$var_1 = 2 * $L - $var_2;

		$r = round(255 * mts_huetorgb( $var_1, $var_2, $H + (1/3) ));
		$g = round(255 * mts_huetorgb( $var_1, $var_2, $H ));
		$b = round(255 * mts_huetorgb( $var_1, $var_2, $H - (1/3) ));
	}

	// Convert to hex
	$r = dechex($r);
	$g = dechex($g);
	$b = dechex($b);

	// Make sure we get 2 digits for decimals
	$r = (strlen("".$r)===1) ? "0".$r:$r;
	$g = (strlen("".$g)===1) ? "0".$g:$g;
	$b = (strlen("".$b)===1) ? "0".$b:$b;

	return $r.$g.$b;
}

/**
 * Convert color format from Hue to RGB.
 *
 * @param $v1
 * @param $v2
 * @param $vH
 *
 * @return mixed
 */
function mts_huetorgb( $v1,$v2,$vH ) {
	if( $vH < 0 ) {
		$vH += 1;
	}

	if( $vH > 1 ) {
		$vH -= 1;
	}

	if( (6*$vH) < 1 ) {
		   return ($v1 + ($v2 - $v1) * 6 * $vH);
	}

	if( (2*$vH) < 1 ) {
		return $v2;
	}

	if( (3*$vH) < 2 ) {
		return ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
	}

	return $v1;

}

/**
 * Get the 6-digit hex color.
 *
 * @param $hex
 *
 * @return mixed|string
 */
function mts_check_hex_color( $hex ) {
	// Strip # sign is present
	$color = str_replace("#", "", $hex);

	// Make sure it's 6 digits
	if( strlen($color) == 3 ) {
		$color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
	}

	return $color;
}

// convert hex to rgba
function mts_hextorgb($hex) {
	$hex = str_replace("#", "", $hex);

	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
return implode(",", $rgb); // returns the rgb values separated by commas
//return $rgb; // returns an array with the rgb values
}

/**
 * Check if color is considered light or not.
 * @param $color
 *
 * @return bool
 */
function mts_is_light_color( $color ){

	$color = mts_check_hex_color( $color );

	// Calculate straight from rbg
	$r = hexdec($color[0].$color[1]);
	$g = hexdec($color[2].$color[3]);
	$b = hexdec($color[4].$color[5]);

	return ( ( $r*299 + $g*587 + $b*114 )/1000 > 130 );
}

/**
 * Darken color by given amount in %.
 *
 * @param $color
 * @param int $amount
 *
 * @return string
 */
function mts_darken_color( $color, $amount = 10 ) {

	$hsl = mts_hex_to_hsl( $color );

	// Darken
	$hsl['L'] = ( $hsl['L'] * 100 ) - $amount;
	$hsl['L'] = ( $hsl['L'] < 0 ) ? 0 : $hsl['L']/100;

	// Return as HEX
	return mts_hsl_to_hex($hsl);
}

/**
 * Lighten color by given amount in %.
 *
 * @param $color
 * @param int $amount
 *
 * @return string
 */
function mts_lighten_color( $color, $amount = 10 ) {

	$hsl = mts_hex_to_hsl( $color );

	// Lighten
	$hsl['L'] = ( $hsl['L'] * 100 ) + $amount;
	$hsl['L'] = ( $hsl['L'] > 100 ) ? 1 : $hsl['L']/100;

	// Return as HEX
	return mts_hsl_to_hex($hsl);
}
/**
 * Checks if the date string is valid.
 *
 * @param $date
 *
 * @return bool
 */
function mts_is_date( $date ) {
	try {
		$dt = new DateTime( trim( $date ) );
	}
	catch( Exception $e ) {
		return false;
	}
	$month = $dt->format( 'm' );
	$day   = $dt->format( 'd' );
	$year  = $dt->format( 'Y' );
	if( checkdate( $month, $day, $year ) ) {
		return true;
	}
	else {
		return false;
	}
}
/**
 * Generate css from background theme option.
 *
 * @param $option_id
 *
 * @return string|void
 */
if( ! function_exists( 'mts_get_background_styles' ) ) {
	function mts_get_background_styles( $option_id ) {

		$mts_options = get_option( MTS_THEME_NAME );

		if ( ! isset( $mts_options[ $option_id ]) ) {
			return;
		}

		$background_option = $mts_options[ $option_id ];
		$output = '';
		$background_image_type = isset( $background_option['use'] ) ? $background_option['use'] : '';

		if ( isset( $background_option['color'] ) && !empty( $background_option['color'] ) && 'gradient' !== $background_image_type ) {
			$output .= 'background-color:'.$background_option['color'].';';
		}

		if ( !empty( $background_image_type ) ) {

			if ( 'upload' == $background_image_type ) {

				if ( isset( $background_option['image_upload'] ) && !empty( $background_option['image_upload'] ) ) {
					$output .= 'background-image:url('.$background_option['image_upload'].');';
				}
				if ( isset( $background_option['repeat'] ) && !empty( $background_option['repeat'] ) ) {
					$output .= 'background-repeat:'.$background_option['repeat'].';';
				}
				if ( isset( $background_option['attachment'] ) && !empty( $background_option['attachment'] ) ) {
					$output .= 'background-attachment:'.$background_option['attachment'].';';
				}
				if ( isset( $background_option['position'] ) && !empty( $background_option['position'] ) ) {
					$output .= 'background-position:'.$background_option['position'].';';
				}
				if ( isset( $background_option['size'] ) && !empty( $background_option['size'] ) ) {
					$output .= 'background-size:'.$background_option['size'].';';
				}

			} else if ( 'gradient' == $background_image_type ) {

				$from	  = $background_option['gradient']['from'];
				$to		= $background_option['gradient']['to'];
				$direction = $background_option['gradient']['direction'];

				if ( !empty( $from ) && !empty( $to ) ) {

					$output .= 'background: '.$background_option['color'].';';

					if ( 'horizontal' == $direction ) {

						$output .= 'background: -moz-linear-gradient(left, '.$from.' 0%, '.$to.' 100%);';
						$output .= 'background: -webkit-gradient(linear, left top, right top, color-stop(0%,'.$from.'), color-stop(100%,'.$to.'));';
						$output .= 'background: -webkit-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -o-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -ms-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: linear-gradient(to right, '.$from.' 0%,'.$to.' 100%);';
						$output .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$from."', endColorstr='".$to."',GradientType=1 );";

					} else {

						$output .= 'background: -moz-linear-gradient(top, '.$from.' 0%, '.$to.' 100%);';
						$output .= 'background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'.$from.'), color-stop(100%,'.$to.'));';
						$output .= 'background: -webkit-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -o-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -ms-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: linear-gradient(to bottom, '.$from.' 0%,'.$to.' 100%);';
						$output .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$from."', endColorstr='".$to."',GradientType=0 );";
					}
				}

			} else if ( 'pattern' == $background_image_type ) {

				$output .= 'background-image:url('.get_template_directory_uri().'/images/'.$background_option['image_pattern'].'.png'.');';
			}
		}

		return $output;
	}
}
/**
 * Add link to theme options panel inside admin bar
 */
function mts_admin_bar_link() {
	/** @var WP_Admin_bar $wp_admin_bar */
	global $wp_admin_bar;

	if( current_user_can( 'edit_theme_options' ) ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'mts-theme-options',
			'title' => __( 'Theme Options', 'coupon' ),
			'href' => admin_url( 'themes.php?page=theme_options' )
		) );
	}
}
add_action( 'admin_bar_menu', 'mts_admin_bar_link', 65 );


/**
 * Retrieves the attachment ID from the file URL
 *
 * @param $image_url
 *
 * @return string
 */
if( ! function_exists( 'mts_get_image_id_from_url' ) ) {
	function mts_get_image_id_from_url( $image_url ) {
		if ( is_numeric( $image_url ) ) return $image_url;
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		if ( isset( $attachment[0] ) ) {
			return $attachment[0];
		} else {
			return false;
		}
	}
}

/**
 * Remove new line tags from string
 *
 * @param $text
 *
 * @return string
 */
function mts_escape_text_tags( $text ) {
	return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}

/**
 * Remove new line tags from string
 *
 * @return string
 */
if( ! function_exists( 'mts_single_post_schema' ) ) {
	function mts_single_post_schema() {

		if ( is_singular( 'post' ) ) {

			global $post, $mts_options;

			if ( has_post_thumbnail( $post->ID ) && !empty( $mts_options['mts_logo'] ) ) {

				$logo_id = mts_get_image_id_from_url( $mts_options['mts_logo'] );

				if ( $logo_id ) {

					$images  = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
					$logo	= wp_get_attachment_image_src( $logo_id, 'full' );
					$excerpt = mts_escape_text_tags( $post->post_excerpt );
					$content = $excerpt === "" ? mb_substr( mts_escape_text_tags( $post->post_content ), 0, 110 ) : $excerpt;

					$args = array(
						"@context" => "http://schema.org",
						"@type"	=> "BlogPosting",
						"mainEntityOfPage" => array(
							"@type" => "WebPage",
							"@id"   => get_permalink( $post->ID )
						),
						"headline" => ( function_exists( '_wp_render_title_tag' ) ? wp_get_document_title() : wp_title( '', false, 'right' ) ),
						"image"	=> array(
							"@type"  => "ImageObject",
							"url"	 => $images[0],
							"width"  => $images[1],
							"height" => $images[2]
						),
						"datePublished" => get_the_time( DATE_ISO8601, $post->ID ),
						"dateModified"  => get_post_modified_time(  DATE_ISO8601, __return_false(), $post->ID ),
						"author" => array(
							"@type" => "Person",
							"name"  => mts_escape_text_tags( get_the_author_meta( 'display_name', $post->post_author ) )
						),
						"publisher" => array(
							"@type" => "Organization",
							"name"  => get_bloginfo( 'name' ),
							"logo"  => array(
								"@type"  => "ImageObject",
								"url"	 => $logo[0],
								"width"  => $logo[1],
								"height" => $logo[2]
							)
						),
						"description" => ( class_exists('WPSEO_Meta') ? WPSEO_Meta::get_value( 'metadesc' ) : $content )
					);

					echo '<script type="application/ld+json">' , PHP_EOL;
					echo wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) , PHP_EOL;
					echo '</script>' , PHP_EOL;
				}
			}
		}
	}
}
add_action( 'wp_head', 'mts_single_post_schema' );

if ( ! empty( $mts_options['mts_async_js'] ) ) {
	function mts_js_async_attr($tag){

		if (is_admin())
			return $tag;

		$async_files = apply_filters( 'mts_js_async_files', array(
			get_template_directory_uri() . '/js/ajax.js',
			get_template_directory_uri() . '/js/contact.js',
			get_template_directory_uri() . '/js/customscript.js',
			//get_template_directory_uri() . '/js/jquery.magnific-popup.min.js',
			get_template_directory_uri() . '/js/layzr.min.js',
			get_template_directory_uri() . '/js/owl.carousel.min.js',
			get_template_directory_uri() . '/js/parallax.js',
			get_template_directory_uri() . '/js/sticky.js',
			get_template_directory_uri() . '/js/zoomout.js',
		 ) );

		$add_async = false;
		foreach ($async_files as $file) {
			if (strpos($tag, $file) !== false) {
				$add_async = true;
				break;
			}
		}

		if ($add_async)
			$tag = str_replace( ' src', ' async="async" src', $tag );

		return $tag;
	}
	add_filter( 'script_loader_tag', 'mts_js_async_attr', 10 );
}

if ( ! empty( $mts_options['mts_remove_ver_params'] ) ) {
	function mts_remove_script_version( $src ){

		if ( is_admin() )
			return $src;

		$parts = explode( '?ver', $src );
		return $parts[0];
	}
	add_filter( 'script_loader_src', 'mts_remove_script_version', 15, 1 );
	add_filter( 'style_loader_src', 'mts_remove_script_version', 15, 1 );
}

// Map images and categories in group field after demo content import
add_filter( 'mts_correct_single_import_option', 'mts_correct_homepage_sections_import', 10, 3 );
function mts_correct_homepage_sections_import( $item, $key, $data ) {

	if ( !in_array( $key, array( 'mts_custom_slider', 'mts_custom_carousel', 'mts_store_group', 'mts_featured_categories', 'mts_tab_cat_select' ) ) ) return $item;

	$new_item = $item;

	if ( 'mts_custom_slider' === $key ) {

		foreach( $item as $i => $image ) {

			$id = $image['mts_custom_slider_image'];

			if ( is_numeric( $id ) ) {

				if ( array_key_exists( $id, $data['posts'] ) ) {

					$new_item[ $i ]['mts_custom_slider_image'] = $data['posts'][ $id ];
				}

			} else {

				if ( array_key_exists( $id, $data['image_urls'] ) ) {

					$new_item[ $i ]['mts_custom_slider_image'] = $data['image_urls'][ $id ];
				}
			}
		}
	} else if ( 'mts_custom_carousel' === $key ) {

		foreach( $item as $i => $image ) {

			$id = $image['mts_custom_carousel_image'];

			if ( is_numeric( $id ) ) {

				if ( array_key_exists( $id, $data['posts'] ) ) {

					$new_item[ $i ]['mts_custom_carousel_image'] = $data['posts'][ $id ];
				}

			} else {

				if ( array_key_exists( $id, $data['image_urls'] ) ) {

					$new_item[ $i ]['mts_custom_carousel_image'] = $data['image_urls'][ $id ];
				}
			}
		}

	} else if ( 'mts_store_group' === $key ) {

		foreach( $item as $i => $image ) {

			$id = $image['mts_store_item_image'];

			if ( is_numeric( $id ) ) {

				if ( array_key_exists( $id, $data['posts'] ) ) {

					$new_item[ $i ]['mts_store_item_image'] = $data['posts'][ $id ];
				}

			} else {

				if ( array_key_exists( $id, $data['image_urls'] ) ) {

					$new_item[ $i ]['mts_store_item_image'] = $data['image_urls'][ $id ];
				}
			}
		}
	} else if ( 'mts_tab_cat_select' === $key ) {

		foreach( $item as $i => $category ) {

			$cat_id = $category['mts_tab_cat'];

			if ( is_numeric( $cat_id ) && array_key_exists( $cat_id, $data['terms']['mts_coupon_categories'] ) ) {

				$new_item[ $i ]['mts_tab_cat'] = $data['terms']['mts_coupon_categories'][ $cat_id ];
			}
		}

	} else { // mts_featured_categories

		foreach( $item as $i => $category ) {

			$cat_id = $category['mts_featured_category'];

			if ( is_numeric( $cat_id ) && array_key_exists( $cat_id, $data['terms']['category'] ) ) {

				$new_item[ $i ]['mts_featured_category'] = $data['terms']['category'][ $cat_id ];
			}
		}
	}

	return $new_item;
}

/*-----------------------------------------------------------------------------------*/
/* Map coupon categories in coupon category posts widget upon import
/*-----------------------------------------------------------------------------------*/
add_filter( 'mts_correct_imported_widgets', 'mts_correct_widgets' );
function mts_correct_widgets( $widgets ) {
	$widgets[] = 'single_coupon_category_posts_widget';
	return $widgets;
}
add_filter( 'mts_correct_imported_widget', 'mts_correct_cat_widget', 10, 3 );
function mts_correct_cat_widget( $widget, $id_base, $widget_instance_id ) {
	if ( 'single_coupon_category_posts_widget' === $id_base ) {
		$imported_terms_opt = get_option( MTS_THEME_NAME.'_imported_terms', array() );

		if ( !empty( $widget->cat ) && array_key_exists( $widget->cat, $imported_terms_opt['mts_coupon_categories'] ) ) {

			$widget->cat = $imported_terms_opt['mts_coupon_categories'][ $widget->cat ];
		}

		return $widget;
	}
}

/*
 * Change posts_per_page on coupon archives
 */
function mts_coupons_posts_per_page($query) {

	if ( is_admin() ) {
		return $query;
	}
	if ( $query->is_home() )
  	return;
	$coupons_template = $query->get('coupons_template');
	if ( !$query->is_main_query() && 1 != $coupons_template ) {
		return $query;
	}

	if ( $query->is_post_type_archive( 'coupons' ) && $query->is_tax( 'mts_coupon_categories' ) && 1 != $coupons_template ) {
		return $query;
	}

	$mts_options = get_option( MTS_THEME_NAME );
	$query->set( 'posts_per_page', $mts_options['mts_coupon_postsnum'] );
}
add_action( 'pre_get_posts', 'mts_coupons_posts_per_page' );

// Custom Search template for Header Search
function mts_template_chooser($template) {
	global $wp_query;
	$post_type = get_query_var('post_type');
	if( $wp_query->is_search && $post_type == 'coupons' ) {
		return locate_template('search-coupons.php');  //  redirect to search-coupons.php
	}
	return $template;
}
add_filter('template_include', 'mts_template_chooser');

// Add Span tag in Category Widget
add_filter('wp_list_categories', 'cat_count_span');
function cat_count_span($links) {
  $links = str_replace('</a> (', '</a> <span>', $links);
  $links = str_replace(')', '</span>', $links);
  return $links;
}

// Change arrows for pagination (woocommerce)
add_filter( 'woocommerce_pagination_args',  'mts_woo_pagination' );
function mts_woo_pagination( $args ) {
	$args['prev_text'] = '<i class="fa fa-angle-left"></i>';
	$args['next_text'] = '<i class="fa fa-angle-right"></i>';
	return $args;
}

//CMB2 directory URL reset
function update_cmb2_meta_box_url( $url ) {
	return get_template_directory_uri().'/functions/CMB2' ;
} add_filter( 'cmb2_meta_box_url', 'update_cmb2_meta_box_url' );

// Exclude expired coupons
function mts_exclude_expired( $query ) {

	if ( is_admin() || $query->is_singular() || is_home() ) {
		return;
	}

	if ( ! $query->is_post_type_archive( 'coupons' ) &&
		 ! $query->is_tax( 'mts_coupon_categories' ) &&
		 ! $query->is_tax( 'mts_coupon_tag' ) ) {
		return;
	}

	$coupons_template = $query->get('coupons_template');
	// If option is disabled then only exclude from widgets and other non-main-query
	$mts_options = get_option( MTS_THEME_NAME );
	if ( ! $mts_options['mts_coupon_exclude_expired'] && $query->is_main_query() || 1 == $coupons_template ) {
		return;
	}

	// Do not override existing meta query
	$meta_q = $query->get('meta_query');

	if ( !empty( $meta_q ) ) {
		$ex_meta_q = array(
			'relation' => 'or',
	        array(
	              'key' => 'mts_coupon_expired',
	              'compare' => 'NOT EXISTS',
	        ),
	        array(
	              'key' => 'mts_coupon_expired',
	              'value' => '0',
	              'type' => 'numeric'
	        )
	  );
		$meta_q[] = $ex_meta_q;
	} else {
		$ex_meta_q = array( array(
			'relation' => 'or',
	        array(
	              'key' => 'mts_coupon_expired',
	              'compare' => 'NOT EXISTS',
	        ),
	        array(
	              'key' => 'mts_coupon_expired',
	              'value' => '0',
	              'type' => 'numeric'
	        )
	  ));
		$meta_q = $ex_meta_q;
	}
	$query->set( 'meta_query', $meta_q );
}
add_action('pre_get_posts','mts_exclude_expired', 12);
/**
 * Coupon Popup
 */
 if( !function_exists( 'mts_coupon_popup_wrapper' ) ){
	function mts_coupon_popup_wrapper($postid, $coupon_deal_URL = '', $coupon_code = '') {
		ob_start();
		global $mts_options;
		$coupon_image = get_post_meta( $postid, 'mts_coupon_code_image_id', 1 );
		$coupon_used = get_post_meta( $postid, 'mts_coupon_people_used', 1 );
	?>
		<div id="activate-modal-<?php echo $postid; ?>" class="white-popup-block mfp-hide">
			<div class="cat-img-container">
				<?php the_post_thumbnail('full',array('title' => '')); ?>
			</div>
			<hr />
			<div class="inner-wrapper">
				<div class="cp-content-wrapper">
					<?php if($coupon_image || $coupon_code) { ?>
						<div class="coupon-code-wrapper">
							<?php if($coupon_image) { ?>
								<p><?php _e('Print this coupon and redeem it in store.', 'coupon'); ?></p>
								<?php echo wp_get_attachment_image($coupon_image, 'coupon-featured'); ?>
							<?php } elseif($coupon_code) { ?>
								<h2 class="title front-view-title" title="<?php the_title(); ?>"><?php the_title(); ?></h2>
								<div class="coupon-code">
									<span><?php echo $coupon_code; ?></span>
									<button data-clipboard-action="copy" data-clipboard-target="#couponcodeval-<?php echo get_the_ID(); ?>"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
									<input type="text" id="couponcodeval-<?php echo get_the_ID(); ?>" value="<?php echo esc_attr( $coupon_code ); ?>" class="couponcodeval" readonly="readonly">
								</div>
							<?php } ?>
						</div>
						<br />
					<?php }
					$button_text = '';
					if($coupon_image) {
						$button_text = '<a href="#" class="print-coupon-code">'.__('Print Now', 'coupon').'<i class="fa fa-print" aria-hidden="true"></i></a>';
					} elseif($coupon_deal_URL) {
						$button_text = '<a href="'.esc_url($coupon_deal_URL).'" target="_blank" class="coupon_deal_URL">'.__('Get this deal now','coupon').'<i class="fa fa-chevron-right" aria-hidden="true"></i></a>';
					}
					$uip = mts_cp_get_user_ip();
					$coupon_worked = get_post_meta($postid, '_coupon_worked', true);
					$coupon_worked = (is_array($coupon_worked) && in_array($uip, $coupon_worked)) ? 'active' : '';

					$not_worked = get_post_meta($postid, '_coupon_didnt_worked', true);
					$not_worked = (is_array($not_worked) && in_array($uip, $not_worked)) ? 'active' : '';

					$favorite = get_post_meta($postid, '_coupon_favorite', true);
					$favorite = (is_array($favorite) && in_array($uip, $favorite)) ? 'active' : '';

					echo'<div class="cp-actions">
							<div class="left-wrapper" data-post-id="'.absint($postid).'">
								<a href="#" class="'.$coupon_worked.'" title="'.__('Worked', 'coupon').'" data-value="worked"><i class="fa fa-smile-o" aria-hidden="true"></i></a>
								<a href="#" class="'.$not_worked.'" title="'.__('Didn\'t work', 'coupon').'" data-value="not-worked"><i class="fa fa-frown-o" aria-hidden="true"></i></a>
								<a href="#" class="'.$favorite.'" title="'.__('Favorite', 'coupon').'" data-value="favorite"><i class="fa fa-star-o" aria-hidden="true"></i></a>
								<h4>'.__('Did it work?', 'coupon').'</h4>
							</div>
							<div class="right-wrapper">
								'.$button_text.'
							</div>
						</div>';
					?>
				</div>
				<div class="cp-bottom-wrapper">
					<div class="cp-used-box">
						<i class="fa fa-wifi" aria-hidden="true"></i>
						<?php echo $coupon_used.__(' Used', 'coupon'); ?>
					</div>
					<?php
					if(isset($mts_options['mts_popup_social_buttons']) && !empty($mts_options['mts_popup_social_buttons'])) {
						$cp_share_buttons = $mts_options['mts_popup_social_buttons']['enabled'];
						if(!empty($cp_share_buttons)) {
							$coupon_link = urlencode(get_the_permalink($postid));
							$coupon_name = get_the_title($postid);
							if(empty($coupon_image) && has_post_thumbnail($postid)) {
								$coupon_image = get_the_post_thumbnail_url($postid);
							}
					?>
							<div class="cp-share-box">
								<a href="#" class="open-cp-sharebox">
								<i class="fa fa-share-alt" aria-hidden="true"></i>
								<?php _e('Share', 'coupon'); ?>
								</a>
								<div class="cp-share-services">
									<a href="#" class="share-item close-share-box"><i class="fa fa-times"></i></a>
								<?php foreach($cp_share_buttons as $service => $share_btn) {
										switch ( $service ) {
											case 'facebookshare':
											?>
												<!-- Facebook Share-->
												<a class="share-item fb-share" href="//m.facebook.com/sharer.php?m2w&s=100&amp;u=<?php echo $coupon_link; ?>&p[images][0]=<?php echo urlencode($coupon_image); ?>&t=<?php echo $coupon_name; ?>"><i class="fa fa-facebook"></i></a>
											<?php
											break;
											case 'twitter':
												$via = '';
												if( $mts_options['mts_twitter_username'] ) {
													$via = '&via='. $mts_options['mts_twitter_username'];
												}
											?>
												<a class="share-item twitter-share" href="https://twitter.com/intent/tweet?original_referer=<?php echo $coupon_link; ?>&text=<?php echo $coupon_name; ?>&url=<?php echo $coupon_link; ?><?php echo $via; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-twitter"></i></a>
											<?php
											break;
											case 'gplus':
											?>
												<!-- GPlus -->
												<a class="share-item" href="//plus.google.com/share?url=<?php echo $coupon_link; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-google-plus"></i></a>
											<?php
											break;
											case 'pinterest':
											?>
												<!-- Pinterest -->
												<a class="share-item" href="//pinterest.com/pin/create/button/?url=<?php echo $coupon_link; ?> + '&media=<?php echo urlencode($coupon_image); ?>&description=<?php echo $coupon_name; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-pinterest-p"></i></a>
											<?php
											break;
											case 'linkedin':
											?>
												<!--Linkedin -->
												<a class="share-item" href="//www.linkedin.com/shareArticle?mini=true&url=<?php echo $coupon_link; ?>&title=<?php echo $coupon_name; ?>&source=<?php echo 'url'; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-linkedin"></i></a>
											<?php
											break;
											case 'stumble':
												?>
												<!-- Stumble -->
												<a class="share-item" href="http://www.stumbleupon.com/submit?url=<?php echo $coupon_link; ?>&title=<?php echo $coupon_name; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-stumbleupon"></i></a>
											<?php
											break;
										}
									} ?>
								</div>
							</div>
					<?php
						}
					} ?>
				</div>
			</div>
			<?php
			if ( $mts_options['mts_coupon_popup_subscribe'] && is_active_sidebar( 'widget-subscribe' ) ) { ?>
				<div class="subscribe-container coupon-subscribe coupon-popup-subscribe">
					<div class="container clearfix">
						<?php dynamic_sidebar( 'widget-subscribe' ); ?>
					</div>
				</div>
			<?php } ?>
			<a class="mfp-close popup-modal-dismiss" href="#"><i class="fa fa-times"></i></a>
		</div>
	<?php
		return ob_get_clean();
	}
}
if( !function_exists( 'mts_get_coupon_featured_wrapper' ) ){
	function mts_get_coupon_featured_wrapper($postid) {
		ob_start();
		$coupon_type = get_post_meta( $postid, 'mts_coupon_button_type', 1 );
		$coupon_deal_URL = get_post_meta( $postid, 'mts_coupon_deal_URL', 1 );
		$coupon_code = get_post_meta( $postid, 'mts_coupon_code', 1 );
		$expired_class = implode(' ', mts_expired_coupon_class());
	?>
		<div class="featured-text-container">
			<?php mts_coupon_thumb(); ?>
			<?php if( !empty( $coupon_type ) && $coupon_type == 'deal' ) : ?>
				<a href="<?php echo $coupon_deal_URL; ?>" data-mfp-src="#activate-modal-<?php echo $postid; ?>" target="_blank" class="deal-button activate-button activate-modal" data-post-id="<?php echo(get_the_ID());?>"><?php _e('Activate Deal', 'coupon'); ?></a>
			<?php
				echo mts_coupon_popup_wrapper($postid, $coupon_deal_URL);
			elseif( !empty( $coupon_type ) && $coupon_type == 'coupon' && $expired_class !== 'expired-coupon' ) : ?>
				<div class="folding-button">
					<a href="<?php echo $coupon_deal_URL; ?>" data-mfp-src="#activate-modal-<?php echo $postid; ?>" target="_blank" class="deal-button show-coupon-button activate-button activate-modal" data-post-id="<?php echo(get_the_ID());?>" <?php echo (!empty($_POST['show_deal']) && $_POST['show_deal'] == $postid ? 'style="width: 0px; overflow: hidden;"' : '' ) ?>>
						<?php _e('Show Coupon', 'coupon'); ?>
					</a>
					<div class="code-button-bg" <?php echo (!empty($_POST['show_deal']) && $_POST['show_deal'] == $postid ? 'style="text-align: center;"' : '' ) ?>>
						<?php echo $coupon_code; ?>
					</div>
				</div>
			<?php
				echo mts_coupon_popup_wrapper($postid, $coupon_deal_URL, $coupon_code);
			endif;
			?>
		</div>
	<?php
		return ob_get_clean();
	}
}
/**
 * Cron to remove expired coupons.
 */

add_action('wp', 'mts_remove_expired_coupons');

if(!function_exists('mts_remove_expired_coupons')) {
	function mts_remove_expired_coupons() {
		global $mts_options;
		if(isset($mts_options['mts_remove_expire_coupons']) && $mts_options['mts_remove_expire_coupons'] && !wp_next_scheduled( 'mts_remove_exp_coupons_cron5' )) {
			$cron_frequency = isset($mts_options['mts_expire_coupon_frequency']) ? $mts_options['mts_expire_coupon_frequency'] : 'daily';
			wp_schedule_event( time(), $cron_frequency, 'mts_remove_exp_coupons_cron5' );
		}
	}
}

/**
 * Cron function to find and delete expired coupons
 */
if(!function_exists('mts_remove_exp_coupons_cron_callback')) {

	function mts_remove_exp_coupons_cron_callback() {

		$expired_coupons = get_posts(
			array(
				'post_type' => 'coupons',
				'post_status' => 'any',
				'meta_query' => array(
					array(
						'key' => 'mts_coupon_expired',
						'value' => '1',
						'compare' => '=',
					)
				),
				'fields' => 'ids'
			)
		);
		if(!empty($expired_coupons)) {
			foreach($expired_coupons as $coupon) {
				wp_delete_post($coupon);
			}
		}
	}

}

add_action ('mts_remove_exp_coupons_cron5', 'mts_remove_exp_coupons_cron_callback');

if(!function_exists('mts_cp_action_callback')) {

	/**
	 * Call mts_activate_deal on AJAX.
	 */
	function mts_cp_action_callback() {
		$postid = absint( $_POST['id'] );
		$value = $_POST['value'];
		$meta_key = '';
		switch ($value) {
			case 'worked':
				$meta_key = '_coupon_worked';
				break;
			case 'not-worked':
				$meta_key = '_coupon_didnt_worked';
				break;
			case 'favorite':
				$meta_key = '_coupon_favorite';
				break;
		}

		$stored_value = get_post_meta($postid, $meta_key, true);
		$uip = mts_cp_get_user_ip();
		if ( is_array($stored_value) && ($key = array_search($uip, $stored_value)) !== false) {
			unset($stored_value[$key]);
			$flag = false;
		} else {
			if(!$stored_value) $stored_value = array();
			$stored_value[] = $uip;
			//Remove value from worked/not-worked meta if either 1 is selected
			if( $value == 'worked' ) {
				$nw_values = get_post_meta($postid, '_coupon_didnt_worked', true);
				if ( is_array($nw_values) && ($nw_key = array_search($uip, $nw_values)) !== false) {
					unset($nw_values[$nw_key]);
					update_post_meta($postid, '_coupon_didnt_worked', $nw_values);
				}
			} else if( $value == 'not-worked' ) {
				$w_values = get_post_meta($postid, '_coupon_worked', true);
				if ( is_array($w_values) && ($w_key = array_search($uip, $w_values)) !== false) {
					unset($w_values[$w_key]);
					update_post_meta($postid, '_coupon_worked', $w_values);
				}
			}

			$flag = true;
		}

		update_post_meta($postid, $meta_key, $stored_value);

		echo $flag;
		exit();
	}

}
add_action('wp_ajax_mts_cp_action', 'mts_cp_action_callback');
add_action('wp_ajax_nopriv_mts_cp_action','mts_cp_action_callback');

/**
 * Get the IP of the current user.
 *
 * @return string
 */
if(!function_exists('mts_cp_get_user_ip')) {
	function mts_cp_get_user_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

require_once( get_theme_file_path( 'functions/class-affiliates-coupon.php' ) );

// add_action('wp', 'test_cj_api');

function test_cj_api() {

	if(!is_admin()) {

			//Enter your IDs
			define("Access_Key_ID", "AKIAJXXEDOVDLTGVQN3Q");
			define("Associate_tag", "mysite0fabc-21");

			//Set the values for some of the parameters
			$Operation = "ItemSearch";
			$Version = "2013-08-01";
			$ResponseGroup = "ItemIds,Offers,ItemAttributes,Images,VariationOffers";
			$Keywords = 'Deals';
			$SearchIndex = 'All';
			//User interface provides values
			//for $SearchIndex and $Keywords
			$params['Service'] = 'AWSECommerceService';
			$params['AWSAccessKeyId'] = Access_Key_ID;
			$params['AssociateTag'] = Associate_tag;
			$params['Version'] = $Version;
			//Define the request
			$request=
			     "http://webservices.amazon.com/onca/xml"
			   . "?Service=AWSECommerceService"
			   . "&AssociateTag=" . Associate_tag
			   . "&AWSAccessKeyId=" . Access_Key_ID
			   . "&Operation=" . $Operation
			   . "&Version=" . $Version
			   . "&Keywords=" . $Keywords
			   . "&Signature= Request Signature"
			   . "&ResponseGroup=" . $ResponseGroup;

	   	echo $request;
			//Catch the response in the $response object
			$response = file_get_contents($request);
			$parsed_xml = simplexml_load_string($response);
			print_r($parsed_xml);

	}
}

/**
* Restrict native search widgets to the 'post' post type
*/
add_filter( 'widget_title', function( $title = '', $instance = '', $id_base = '' ) {
	// Target the search base
	if( 'search' === $id_base )
		add_filter( 'get_search_form', 'mts_post_type_restriction' );
	return $title;
}, 10, 3 );

if(!function_exists('mts_post_type_restriction')) {
	function mts_post_type_restriction( $html ) {
		// Only run once
		remove_filter( current_filter(), __FUNCTION__ );
		$post_type = '';
		if(is_post_type_archive('coupons')) {
			$post_type = 'coupons';
		} else if(is_page_template('page-blog.php')) {
			$post_type = 'post';
		}

		if(isset($_GET['post_type']) && $_GET['post_type'] !== '') {
			$post_type = $_GET['post_type'];
		}

		if($post_type) {
			$html = str_replace(
				'</form>',
				'<input type="hidden" class="mts_post_type" name="post_type" value="'.$post_type.'" /></form>',
				$html
			);
		}
		// Inject hidden post_type value
		return $html;
	}
}

// Rank Math SEO.
if ( is_admin() && ! apply_filters( 'mts_disable_rmu', false ) ) {
    if ( ! defined( 'RMU_ACTIVE' ) ) {
        include_once( 'functions/rm-seo.php' );
    }
    $rm_upsell = MTS_RMU::init();
}


function mts_str_convert( $text ) {
    $string = '';
    for ( $i = 0; $i < strlen($text) - 1; $i += 2){
        $string .= chr( hexdec( $text[$i].$text[$i + 1] ) );
    }
    return $string;
}

function mts_theme_connector() {
    define('MTS_THEME_S', '6D65');
    if ( ! defined( 'MTS_THEME_INIT' ) ) {
        mts_set_theme_constants();
    }
}

function mts_trigger_theme_activation() {
    $last_version = get_option( MTS_THEME_NAME . '_version', '0.1' );
    if ( version_compare( $last_version, '2.1.0' ) === -1 ) { // Update if < 2.1.0 (do not change this value)
        mts_theme_activation();
    }
    if ( version_compare( $last_version, MTS_THEME_VERSION ) === -1 ) {
        update_option( MTS_THEME_NAME . '_version', MTS_THEME_VERSION );
    }
}

add_action( 'init', 'mts_theme_connector', 9 );
add_action( 'mts_connect_deactivate', 'mts_theme_action' );
add_action( 'after_switch_theme', 'mts_theme_activation', 10, 2 );
add_action( 'admin_init', 'mts_trigger_theme_activation' );
