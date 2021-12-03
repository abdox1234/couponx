<?php

defined('ABSPATH') or die;

/*
 *
 * Require the framework class before doing anything else, so we can use the defined urls and dirs
 *
 */
require_once( dirname( __FILE__ ) . '/options/options.php' );
/*
 *
 * Add support tab
 *
 */
if ( ! defined('MTS_THEME_WHITE_LABEL') || ! MTS_THEME_WHITE_LABEL ) {
	require_once( dirname( __FILE__ ) . '/options/support.php' );
	$mts_options_tab_support = MTS_Options_Tab_Support::get_instance();
}
/*
 *
 * Custom function for filtering the sections array given by theme, good for child themes to override or add to the sections.
 * Simply include this function in the child themes functions.php file.
 *
 * NOTE: the defined constants for urls, and dir will NOT be available at this point in a child theme, so you must use
 * get_template_directory_uri() if you want to use any of the built in icons
 *
 */
function add_another_section($sections){

	//$sections = array();
	$sections[] = array(
		'title' => __('A Section added by hook', 'coupon' ),
		'desc' => '<p class="description">' . __('This is a section created by adding a filter to the sections array, great to allow child themes, to add/remove sections from the options.', 'coupon' ) . '</p>',
		//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
		//You dont have to though, leave it blank for default.
		'icon' => trailingslashit(get_template_directory_uri()).'options/img/glyphicons/glyphicons_062_attach.png',
		//Lets leave this as a blank section, no options just some intro text set above.
		'fields' => array()
	);

	return $sections;

}//function
//add_filter('nhp-opts-sections-twenty_eleven', 'add_another_section');


/*
 *
 * Custom function for filtering the args array given by theme, good for child themes to override or add to the args array.
 *
 */
function change_framework_args($args){

	//$args['dev_mode'] = false;

	return $args;

}//function
//add_filter('nhp-opts-args-twenty_eleven', 'change_framework_args');

/*
 * This is the meat of creating the options page
 *
 * Override some of the default values, uncomment the args and change the values
 * - no $args are required, but there there to be overridden if needed.
 *
 *
 */

function setup_framework_options(){
	$args = array();

	//Set it to dev mode to view the class settings/info in the form - default is false
	$args['dev_mode'] = false;
	//Remove the default stylesheet? make sure you enqueue another one all the page will look whack!
	//$args['stylesheet_override'] = true;

	//Add HTML before the form
	//$args['intro_text'] = __('<p>This is the HTML which can be displayed before the form, it isnt required, but more info is always better. Anything goes in terms of markup here, any HTML.</p>', 'coupon' );

	if ( ! MTS_THEME_WHITE_LABEL ) {
		//Setup custom links in the footer for share icons
		$args['share_icons']['twitter'] = array(
			'link' => 'http://twitter.com/mythemeshopteam',
			'title' => __( 'Follow Us on Twitter', 'coupon' ),
			'img' => 'fa fa-twitter-square'
		);
		$args['share_icons']['facebook'] = array(
			'link' => 'http://www.facebook.com/mythemeshop',
			'title' => __( 'Like us on Facebook', 'coupon' ),
			'img' => 'fa fa-facebook-square'
		);
	}

	//Choose to disable the import/export feature
	//$args['show_import_export'] = false;

	//Choose a custom option name for your theme options, the default is the theme name in lowercase with spaces replaced by underscores
	$args['opt_name'] = MTS_THEME_NAME;

	//Custom menu icon
	//$args['menu_icon'] = '';

	//Custom menu title for options page - default is "Options"
	$args['menu_title'] = __('Theme Options', 'coupon' );

	//Custom Page Title for options page - default is "Options"
	$args['page_title'] = __('Theme Options', 'coupon' );

	//Custom page slug for options page (wp-admin/themes.php?page=***) - default is "nhp_theme_options"
	$args['page_slug'] = 'theme_options';

	//Custom page capability - default is set to "manage_options"
	//$args['page_cap'] = 'manage_options';

	//page type - "menu" (adds a top menu section) or "submenu" (adds a submenu) - default is set to "menu"
	//$args['page_type'] = 'submenu';

	//parent menu - default is set to "themes.php" (Appearance)
	//the list of available parent menus is available here: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	//$args['page_parent'] = 'themes.php';

	//custom page location - default 100 - must be unique or will override other items
	$args['page_position'] = 62;

	//Custom page icon class (used to override the page icon next to heading)
	//$args['page_icon'] = 'icon-themes';

	if ( ! MTS_THEME_WHITE_LABEL ) {
		//Set ANY custom page help tabs - displayed using the new help tab API, show in order of definition
		$args['help_tabs'][] = array(
			'id' => 'nhp-opts-1',
			'title' => __('Support', 'coupon' ),
			'content' => '<p>' . sprintf( __('If you are facing any problem with our theme or theme option panel, head over to our %s.', 'coupon' ), '<a href="http://community.mythemeshop.com/">'. __( 'Support Forums', 'coupon' ) . '</a>' ) . '</p>'
		);
		$args['help_tabs'][] = array(
			'id' => 'nhp-opts-2',
			'title' => __('Earn Money', 'coupon' ),
			'content' => '<p>' . sprintf( __('Earn 70%% commision on every sale by refering your friends and readers. Join our %s.', 'coupon' ), '<a href="http://mythemeshop.com/affiliate-program/">' . __( 'Affiliate Program', 'coupon' ) . '</a>' ) . '</p>'
		);
	}

	//Set the Help Sidebar for the options page - no sidebar by default
	//$args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'coupon' );

	$mts_patterns = array(
		'nobg' => array('img' => NHP_OPTIONS_URL.'img/patterns/nobg.png'),
		'pattern0' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern0.png'),
		'pattern1' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern1.png'),
		'pattern2' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern2.png'),
		'pattern3' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern3.png'),
		'pattern4' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern4.png'),
		'pattern5' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern5.png'),
		'pattern6' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern6.png'),
		'pattern7' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern7.png'),
		'pattern8' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern8.png'),
		'pattern9' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern9.png'),
		'pattern10' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern10.png'),
		'pattern11' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern11.png'),
		'pattern12' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern12.png'),
		'pattern13' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern13.png'),
		'pattern14' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern14.png'),
		'pattern15' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern15.png'),
		'pattern16' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern16.png'),
		'pattern17' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern17.png'),
		'pattern18' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern18.png'),
		'pattern19' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern19.png'),
		'pattern20' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern20.png'),
		'pattern21' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern21.png'),
		'pattern22' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern22.png'),
		'pattern23' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern23.png'),
		'pattern24' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern24.png'),
		'pattern25' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern25.png'),
		'pattern26' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern26.png'),
		'pattern27' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern27.png'),
		'pattern28' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern28.png'),
		'pattern29' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern29.png'),
		'pattern30' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern30.png'),
		'pattern31' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern31.png'),
		'pattern32' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern32.png'),
		'pattern33' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern33.png'),
		'pattern34' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern34.png'),
		'pattern35' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern35.png'),
		'pattern36' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern36.png'),
		'pattern37' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern37.png'),
		'hbg' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg.png'),
		'hbg2' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg2.png'),
		'hbg3' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg3.png'),
		'hbg4' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg4.png'),
		'hbg5' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg5.png'),
		'hbg6' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg6.png'),
		'hbg7' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg7.png'),
		'hbg8' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg8.png'),
		'hbg9' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg9.png'),
		'hbg10' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg10.png'),
		'hbg11' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg11.png'),
		'hbg12' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg12.png'),
		'hbg13' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg13.png'),
		'hbg14' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg14.png'),
		'hbg15' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg15.png'),
		'hbg16' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg16.png'),
		'hbg17' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg17.png'),
		'hbg18' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg18.png'),
		'hbg19' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg19.png'),
		'hbg20' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg20.png'),
		'hbg21' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg21.png'),
		'hbg22' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg22.png'),
		'hbg23' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg23.png'),
		'hbg24' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg24.png'),
		'hbg25' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg25.png')
	);

	$sections = array();

	$sections[] = array(
		'icon' => 'fa fa-cogs',
		'title' => __('General Settings', 'coupon' ),
		'desc' => '<p class="description">' . __('This tab contains common setting options which will be applied to the whole theme.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_logo',
				'type' => 'upload',
				'title' => __('Logo Image', 'coupon' ),
				'sub_desc' => __('Upload your logo using the Upload Button or insert image URL.', 'coupon' )
			),
			array(
				'id' => 'mts_favicon',
				'type' => 'upload',
				'title' => __('Favicon', 'coupon' ),
				'sub_desc' => sprintf( __('Upload a %s image that will represent your website\'s favicon.', 'coupon' ), '<strong>32 x 32 px</strong>' )
			),
			array(
				'id' => 'mts_touch_icon',
				'type' => 'upload',
				'title' => __('Touch icon', 'coupon' ),
				'sub_desc' => sprintf( __('Upload a %s image that will represent your website\'s touch icon for iOS 2.0+ and Android 2.1+ devices.', 'coupon' ), '<strong>152 x 152 px</strong>' )
			),
			array(
				'id' => 'mts_metro_icon',
				'type' => 'upload',
				'title' => __('Metro icon', 'coupon' ),
				'sub_desc' => sprintf( __('Upload a %s image that will represent your website\'s IE 10 Metro tile icon.', 'coupon' ), '<strong>144 x 144 px</strong>' )
			),
			array(
				'id' => 'mts_twitter_username',
				'type' => 'text',
				'title' => __('Twitter Username', 'coupon' ),
				'sub_desc' => __('Enter your Username here.', 'coupon' ),
			),
			array(
				'id' => 'mts_feedburner',
				'type' => 'text',
				'title' => __('FeedBurner URL', 'coupon' ),
				'sub_desc' => sprintf( __('Enter your FeedBurner\'s URL here, ex: %s and your main feed (http://example.com/feed) will get redirected to the FeedBurner ID entered here.)', 'coupon' ), '<strong>http://feeds.feedburner.com/mythemeshop</strong>' ),
				'validate' => 'url'
			),
			array(
				'id' => 'mts_header_code',
				'type' => 'textarea',
				'title' => __('Header Code', 'coupon' ),
				'sub_desc' => wp_kses( __('Enter the code which you need to place <strong>before closing &lt;/head&gt; tag</strong>. (ex: Google Webmaster Tools verification, Bing Webmaster Center, BuySellAds Script, Alexa verification etc.)', 'coupon' ), array( 'strong' => array() ) )
			),
			array(
				'id' => 'mts_analytics_code',
				'type' => 'textarea',
				'title' => __('Footer Code', 'coupon' ),
				'sub_desc' => wp_kses( __('Enter the codes which you need to place in your footer. <strong>(ex: Google Analytics, Clicky, STATCOUNTER, Woopra, Histats, etc.)</strong>.', 'coupon' ), array( 'strong' => array() ) )
			),
			array(
				'id' => 'mts_ajax_search',
				'type' => 'button_set',
				'title' => __('AJAX Quick search', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Enable or disable search results appearing instantly below the search form', 'coupon' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_responsive',
				'type' => 'button_set',
				'title' => __('Responsiveness', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('MyThemeShop themes are responsive, which means they adapt to tablet and mobile devices, ensuring that your content is always displayed beautifully no matter what device visitors are using. Enable or disable responsiveness using this option.', 'coupon' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_rtl',
				'type' => 'button_set',
				'title' => __('Right To Left Language Support', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Enable this option for right-to-left sites.', 'coupon' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_shop_products',
				'type' => 'text',
				'title' => __('No. of Products', 'coupon' ),
				'sub_desc' => __('Enter the total number of products which you want to show on shop page (WooCommerce plugin must be enabled).', 'coupon' ),
				'validate' => 'numeric',
				'std' => '9',
				'class' => 'small-text'
			),
		)
	);
	$sections[] = array(
		'icon' => 'fa fa-bolt',
		'title' => __('Performance', 'coupon' ),
		'desc' => '<p class="description">' . __('This tab contains performance-related options which can help speed up your website.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_prefetching',
				'type' => 'button_set',
				'title' => __('Prefetching', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Enable or disable prefetching. If user is on homepage, then single page will load faster and if user is on single page, homepage will load faster in modern browsers.', 'coupon' ),
				'std' => '0'
			),
			array(
				'id'       => 'mts_lazy_load',
				'type'     => 'button_set_hide_below',
				'title'    => __('Theme\'s Lazy Loading', 'coupon' ),
				'options'  => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Delay loading of images outside of viewport, until user scrolls to them.', 'coupon' ),
				'std'      => '0',
				'args'     => array('hide' => 2)
				),
				array(
					'id' => 'mts_lazy_load_thumbs',
					'type' => 'button_set',
					'title' => __('Lazy load featured images', 'coupon' ),
					'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
					'sub_desc' => __('Enable or disable Lazy load of featured images across site.', 'coupon' ),
					'std' => '0'
				),
				array(
					'id' => 'mts_lazy_load_content',
					'type' => 'button_set',
					'title' => __('Lazy load post content images', 'coupon' ),
					'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
					'sub_desc' => __('Enable or disable Lazy load of images inside post/page content.', 'coupon' ),
					'std' => '0'
			),
			array(
				'id' => 'mts_async_js',
				'type' => 'button_set',
				'title' => __('Async JavaScript', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => sprintf( __('Add %s attribute to script tags to improve page download speed.', 'coupon' ), '<code>async</code>' ),
				'std' => '1',
			),
			array(
				'id' => 'mts_remove_ver_params',
				'type' => 'button_set',
				'title' => __('Remove ver parameters', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => sprintf( __('Remove %s parameter from CSS and JS file calls. It may improve speed in some browsers which do not cache files having the parameter.', 'coupon' ), '<code>ver</code>' ),
				'std' => '1',
			),
			array(
				'id' => 'mts_optimize_wc',
				'type' => 'button_set',
				'title' => __('Optimize WooCommerce scripts', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Load WooCommerce scripts and styles only on WooCommerce pages (WooCommerce plugin must be enabled).', 'coupon' ),
				'std' => '1'
			),

			array(
				'id' => 'mts_remove_expire_coupons',
				'type' => 'button_set_hide_below',
				'title' => __('Delete Expired Coupons', 'coupon' ),
				'options' => array( '0' => __( 'Disable', 'coupon' ), '1' => __( 'Enable', 'coupon' ) ),
				'sub_desc' => __('Remove Expired Coupons', 'coupon' ),
				'std' => '0',

				'args' => array('hide' => 1)
			),

			array(
				'id' => 'mts_expire_coupon_frequency',
				'type' => 'radio',
				'title' => __('Cron Frequency', 'coupon' ),
				'sub_desc' => __('Select cron frequency to remove expired coupons.', 'coupon' ),
				'options' => array(
					'hourly'=> __('Hourly', 'coupon' ),
					'twicedaily' => __('Twice Daily', 'coupon' ),
					'daily' => __( 'Daily', 'coupon' )
				),
				'std' => 'daily',

			),

			'cache_message' => array(
				'id' => 'mts_cache_message',
				'type' => 'info',
				'title' => __('Use Cache', 'coupon' ),
				// Translators: %1$s = popup link to W3 Total Cache, %2$s = popup link to WP Super Cache
				'desc' => sprintf(
					__('A cache plugin can increase page download speed dramatically. We recommend using %1$s or %2$s.', 'coupon' ),
					'<a href="https://community.mythemeshop.com/tutorials/article/8-make-your-website-load-faster-using-w3-total-cache-plugin/" target="_blank" title="W3 Total Cache">W3 Total Cache</a>',
					'<a href="'.admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-super-cache&TB_iframe=true&width=772&height=574' ).'" class="thickbox" title="WP Super Cache">WP Super Cache</a>'
				),
			),
		)
	);

	// Hide cache message on multisite or if a chache plugin is active already
	if ( is_multisite() || strstr( join( ';', get_option( 'active_plugins' ) ), 'cache' ) ) {
		unset( $sections[1]['fields']['cache_message'] );
	}

	$sections[] = array(
		'icon' => 'fa fa-adjust',
		'title' => __('Styling Options', 'coupon' ),
		'desc' => '<p class="description">' . __('Control the visual appearance of your theme, such as colors, layout and patterns, from here.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_color_scheme',
				'type' => 'color',
				'title' => __('Color Scheme', 'coupon' ),
				'sub_desc' => __('The theme comes with unlimited color schemes for your theme\'s styling.', 'coupon' ),
				'std' => '#e73931'
			),
			array(
				'id' => 'mts_layout',
				'type' => 'radio_img',
				'title' => __('Layout Style', 'coupon' ),
				'sub_desc' => wp_kses( __('Choose the <strong>default sidebar position</strong> for your site. The position of the sidebar for individual posts can be set in the post editor.', 'coupon' ), array( 'strong' => array() ) ),
				'options' => array(
					'sclayout' => array('img' => NHP_OPTIONS_URL.'img/layouts/sc.png'),
					'cslayout' => array('img' => NHP_OPTIONS_URL.'img/layouts/cs.png')
				),
				'std' => 'sclayout'
			),
			array(
				'id' => 'mts_background',
				'type' => 'background',
				'title' => __('Site Background', 'coupon' ),
				'sub_desc' => __('Set background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#ffffff',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_custom_css',
				'type' => 'textarea',
				'title' => __('Custom CSS', 'coupon' ),
				'sub_desc' => __('You can enter custom CSS code here to further customize your theme. This will override the default CSS used on your site.', 'coupon' )
			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-credit-card',
		'title' => __('Header', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of header section.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_topbar_background',
				'type' => 'background',
				'title' => __('Top Bar&#39;s Background', 'coupon' ),
				'sub_desc' => __('Set Top Bar&#39;s background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#28202e',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_header_search',
				'type' => 'button_set_hide_below',
				'title' => __('Show Header Search Form', 'coupon'),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Use this button to Show or Hide <strong>Header Search Form</strong>.', 'coupon'),
				'std' => '1'
			),
			array(
				'id' => 'mts_header_search_placeholder',
				'type' => 'text',
				'title' => __('Header Search Placeholder', 'coupon'),
				'sub_desc' => __('Change the placeholder text for the search field.', 'coupon'),
				'std' => __('Search for eBay, Amazon, Pizza etc.', 'coupon' )
			),
			array(
				'id' => 'mts_header_login',
				'type' => 'button_set',
				'title' => __('Show Header Login and Registration Form', 'coupon'),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Use this button to Show or Hide <strong>Header Login and Registration Form</strong>.', 'coupon'),
				'std' => '1'
			),
			array(
				'id' => 'mts_navigation_background',
				'type' => 'background',
				'title' => __('Navigation Background', 'coupon' ),
				'sub_desc' => __('Set Navigation background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#f2f2f2',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_sticky_nav',
				'type' => 'button_set',
				'title' => __('Floating Navigation Menu', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => sprintf( __('Use this button to enable %s.', 'coupon' ), '<strong>' . __('Floating Navigation Menu', 'coupon' ) . '</strong>' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_show_primary_nav',
				'type' => 'button_set',
				'title' => __('Show Menu', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => sprintf( __('Use this button to enable %s.', 'coupon' ), '<strong>' . __( 'Navigation Menu', 'coupon' ) . '</strong>' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_header_section2',
				'type' => 'button_set',
				'title' => __('Show Logo', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => wp_kses( __('Use this button to Show or Hide the <strong>Logo</strong> completely.', 'coupon' ), array( 'strong' => array() ) ),
				'std' => '1'
			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-table',
		'title' => __('Footer', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of Footer section.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_first_footer',
				'type' => 'button_set_hide_below',
				'title' => __('Footer Widgets', 'coupon' ),
				'sub_desc' => __('Enable or disable footer widgets with this option.', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'std' => '0',
				'args' => array('hide' => 2)
			),
			array(
				'id' => 'mts_footer_background',
				'type' => 'background',
				'title' => __('Footer Background', 'coupon' ),
				'sub_desc' => __('Set footer background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#28202e',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_first_footer_num',
				'type' => 'button_set',
				'class' => 'green',
				'title' => __('Footer Widget Columns', 'coupon' ),
				'sub_desc' => wp_kses( __('Choose the number of widget columns in the <strong>footer</strong>', 'coupon' ), array( 'strong' => array() ) ),
				'options' => array(
					'3' => __( '3 Widgets', 'coupon' ),
					'4' => __( '4 Widgets', 'coupon' ),
				),
				'std' => '4'
			),
			array(
				'id' => 'mts_footer_text_section',
				'type' => 'button_set_hide_below',
				'title' => __('Extra Footer Text', 'coupon'),
				'sub_desc' => __('You can add extra footer text section from here.', 'coupon'),
				'options' => array('0' => 'Off','1' => 'On'),
				'std' => '1',
				'args' => array('hide' => 2)
			),
			array(
				'id' => 'mts_footer_text_title',
				'type' => 'text',
				'title' => __('Footer Text Title', 'coupon' ),
				'sub_desc' => __('Enter your Footer text title here.', 'coupon' ),
				'std' => 'Vestibulum elementum convallis porttitor'
			),
			array(
				'id' => 'mts_footer_text_content',
				'type' => 'textarea',
				'title' => __('Footer Text Content', 'coupon' ),
				'sub_desc' => __('Enter your Footer text Content here.', 'coupon' ),
				'std' => 'Ut placerat consequat diam, sed placerat justo sagittis nec. Suspendisse tempor efficitur dolor at tempor. Donec commodo, orci, dui nibh imperdiet neque, sed volutpat orci sem nec est.'
			),
			array(
				'id' => 'mts_footer_social_icon_section',
				'type' => 'button_set_hide_below',
				'title' => __('Footer Social Icons', 'coupon'),
				'sub_desc' => __('Add footer social icons from here.', 'coupon'),
				'options' => array('0' => 'Off','1' => 'On'),
				'std' => '1',
				'args' => array('hide' => 2)
			),
			array(
				'id' => 'mts_footer_title',
				'type' => 'text',
				'title' => __('Footer Social icon Title', 'coupon' ),
				'sub_desc' => __('Enter your Footer Social icon section title here.', 'coupon' ),
				'std' => 'Follow Us on'
			),
			array(
				'id' => 'mts_footer_social',
				'title' => __('Footer Social Icons', 'coupon'),
				'sub_desc' => __( 'Add Social Media icons in Footer section.', 'coupon' ),
				'type' => 'group',
				'groupname' => __('new icon', 'coupon'), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_footer_social_icon_title',
						'type' => 'text',
						'title' => __('Title', 'coupon'),
						),
					array(
						'id' => 'mts_footer_social_icon',
						'type' => 'icon_select',
						'title' => __('Icon', 'coupon')
						),
					array(
						'id' => 'mts_footer_social_icon_hcolor',
						'type' => 'color',
						'title' => __('Hover Color', 'coupon')
						),
					array(
						'id' => 'mts_footer_social_icon_link',
						'type' => 'text',
						'title' => __('URL', 'coupon'),
						),
				),
				'std' => array(
					'facebook' => array(
						'group_title' => 'Facebook',
						'group_sort' => '1',
						'mts_footer_social_icon_title' => 'Facebook',
						'mts_footer_social_icon' => 'facebook',
						'mts_footer_social_icon_hcolor' => '#5d82d1',
						'mts_footer_social_icon_link' => '#',
					),
					'twitter' => array(
						'group_title' => 'Twitter',
						'group_sort' => '2',
						'mts_footer_social_icon_title' => 'Twitter',
						'mts_footer_social_icon' => 'twitter',
						'mts_footer_social_icon_hcolor' => '#40bff5',
						'mts_footer_social_icon_link' => '#',
					),
					'googleplus' => array(
						'group_title' => 'Google Plus',
						'group_sort' => '3',
						'mts_footer_social_icon_title' => 'Google Plus',
						'mts_footer_social_icon' => 'google-plus',
						'mts_footer_social_icon_hcolor' => '#eb5e4c',
						'mts_footer_social_icon_link' => '#',
					),
					'instagram' => array(
						'group_title' => 'Instagram',
						'group_sort' => '4',
						'mts_footer_social_icon_title' => 'Instagram',
						'mts_footer_social_icon' => 'instagram',
						'mts_footer_social_icon_hcolor' => '#91653f',
						'mts_footer_social_icon_link' => '#',
					),
					'pinterest' => array(
						'group_title' => 'Pinterest',
						'group_sort' => '5',
						'mts_footer_social_icon_title' => 'Pinterest',
						'mts_footer_social_icon' => 'pinterest-p',
						'mts_footer_social_icon_hcolor' => '#e13138',
						'mts_footer_social_icon_link' => '#',
					),
					'rss' => array(
						'group_title' => 'RSS',
						'group_sort' => '6',
						'mts_footer_social_icon_title' => 'RSS',
						'mts_footer_social_icon' => 'rss',
						'mts_footer_social_icon_hcolor' => '#ef922f',
						'mts_footer_social_icon_link' => '#',
					)
				)
			),
			array(
				'id' => 'mts_copyrights_background',
				'type' => 'background',
				'title' => __('Copyright&#39;s Background', 'coupon' ),
				'sub_desc' => __('Set Copyright&#39;s background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#1d1721',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_copyrights',
				'type' => 'textarea',
				'title' => __('Copyrights Text', 'coupon' ),
				'sub_desc' => __( 'You can change or remove our link from footer and use your own custom text.', 'coupon' ) . ( MTS_THEME_WHITE_LABEL ? '' : wp_kses( __('(You can also use your affiliate link to <strong>earn 70% of sales</strong>. Ex: <a href="https://mythemeshop.com/go/aff/aff" target="_blank">https://mythemeshop.com/?ref=username</a>)', 'coupon' ), array( 'strong' => array(), 'a' => array( 'href' => array(), 'target' => array() ) ) ) ),
				'std' => MTS_THEME_WHITE_LABEL ? null : sprintf( __( 'Theme by %s', 'coupon' ), '<a href="http://mythemeshop.com/" rel="nofollow">MyThemeShop</a>' )
			),
		)
	);

	/* ==========================================================================
	Tab 1 - Homepage Layout Manager
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Layout', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the homepage sections.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id'	=> 'mts_homepage_layout',
				'type'	=> 'layout',
				'title'   => __( 'Homepage Layout Manager', 'coupon' ),
				'sub_desc'	=> __( 'Organize how you want the layout to appear on the homepage', 'coupon' ),
				'options' => array(
					'enabled'  => array(
						'carousel'	 => __( 'Carousel', 'coupon' ),
						'slider'	 => __( 'Slider', 'coupon' ),
						'tabs' => __( 'Tabs', 'coupon' ),
						'socialicons'   => __( 'Social Icons', 'coupon' ),
						'stores'   => __( 'Stores', 'coupon' ),
						'signup'   => __( 'Sign Up', 'coupon' ),
					),
					'disabled' => array(
						'subscribe'   => __( 'Subscribe', 'coupon' ),
						'blog'  	=> __('Blog Feed', 'coupon'),
					)
				),
				'std' => array(
					'enabled'  => array(
						'carousel'	 => __( 'Carousel', 'coupon' ),
						'slider'	 => __( 'Slider', 'coupon' ),
						'tabs' => __( 'Tabs', 'coupon' ),
						'socialicons'   => __( 'Social Icons', 'coupon' ),
						'stores'   => __( 'Popular Stores', 'coupon' ),
						'signup'   => __( 'Sign Up', 'coupon' ),
					),
					'disabled' => array(
						'subscribe'   => __( 'Subscribe', 'coupon' ),
						'blog'  	=> __('Blog Feed', 'coupon'),
					)
				),

			),
		)
	);

	/* ==========================================================================
	Tab 2 - Carousel
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Carousel', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the Top Carousel.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_carousel_title',
				'type' => 'text',
				'title' => __('Carousel Section Title', 'coupon' ),
				'sub_desc' => __('Enter your Carousel section title here.', 'coupon' ),
				'std' => "Today's Offers and Coupons"
			),
			array(
				'id'		=> 'mts_custom_carousel',
				'type'	  => 'group',
				'title'	 => __('Carousel Items', 'coupon' ),
				'sub_desc'  => __('With this option you can set up a carousel with custom images and links.', 'coupon' ),
				'groupname' => __('Carousel', 'coupon' ), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_custom_carousel_title',
						'type' => 'text',
						'title' => __('Title', 'coupon' ),
						'sub_desc' => __('Title of the carousel. Will not appear anywhere in the frontend.', 'coupon' ),
					),
					array(
						'id' => 'mts_custom_carousel_description',
						'type' => 'text',
						'title' => __('Description', 'coupon' ),
						'sub_desc' => __('This text will appear below carousel image.', 'coupon' ),
					),
					array(
						'id' => 'mts_custom_carousel_image',
						'type' => 'upload',
						'title' => __('Image', 'coupon' ),
						'sub_desc' => __('Upload or select an image. Recommended image height: 80px', 'coupon' ),
						'return' => 'url'
					),
					array(
						'id' => 'mts_custom_carousel_border',
						'type' => 'color',
						'title' => __('Carousel item border color', 'coupon' ),
						'sub_desc' => __('Change Your border color from here.', 'coupon' ),
						'std' => '#d5d1d8'
					),
					array(
						'id' => 'mts_custom_carousel_background',
						'type' => 'color',
						'title' => __('Carousel item background color', 'coupon' ),
						'sub_desc' => __('Change Your item background color from here.', 'coupon' ),
						'std' => '#ffffff'
					),
					array(
						'id' => 'mts_custom_carousel_text',
						'type' => 'color',
						'title' => __('Carousel item text color', 'coupon' ),
						'sub_desc' => __('Change Your carousel text color from here.', 'coupon' ),
						'std' => '#33244a'
					),
					array(
						'id' => 'mts_custom_carousel_link',
						'type' => 'text',
						'title' => __('Link', 'coupon' ),
						'sub_desc' => __('Insert a link URL for the carousel', 'coupon' ),
						'std' => '#'
					),
				),
			),
			array(
				'id' => 'mts_carousel_pages',
				'type' => 'multi_checkbox',
				'title' => __('Show Carousel on Other Pages', 'coupon' ),
				'sub_desc' => __('You can enable carousel on other pages from here.', 'coupon' ),
				'options' => array(
					'coupon-archive' => __( 'Coupon Archive', 'coupon' ),
					'coupon-single' => __( 'Single Coupon Page', 'coupon' ),
					'blog-page' => __( 'Blog Page', 'coupon' ),
					'blog-single' => __( 'Single Blog Post', 'coupon' )
				),
				'std' => array('coupon-archive' => '1', 'coupon-single' => '1', 'blog-page' => '1', 'blog-single' => '1')
			),
		)
	);

	/* ==========================================================================
	Tab 3 - Slider
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Slider', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control homepage slider.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_custom_slider',
				'type' => 'group',
				'title' => __('Slider', 'coupon' ),
				'sub_desc' => __('With this option you can set up a slider with custom image and text.', 'coupon' ),
				'groupname' => __('Slide', 'coupon' ), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_custom_slider_title',
						'type' => 'text',
						'title' => __('Title', 'coupon' ),
						'sub_desc' => __('Title of the slide', 'coupon' ),
					),
					array(
						'id' => 'mts_custom_slider_image',
						'type' => 'upload',
						'title' => __('Image', 'coupon' ),
						'sub_desc' => __('Upload or select an image for this slide. Minimum size: 1180x355px', 'coupon' ),
						'return' => 'id'
					),
					array('id' => 'mts_custom_slider_link',
						'type' => 'text',
						'title' => __('Link', 'coupon' ),
						'sub_desc' => __('Insert a link URL for the slide', 'coupon' ),
						'std' => '#'
					),
				),
			),
			array(
				'id' => 'mts_slider_animation',
				'type' => 'button_set',
				'title' => __('Slider Animation', 'coupon' ),
				'options' => array('0' => __('Slide','coupon'),'1' => __('Fade', 'coupon' )),
				'sub_desc' => __('Set the animation effect for slider.', 'coupon' ),
				'std' => '0',
				'class' => 'green'
			),
			array(
				'id' => 'mts_slider_background',
				'type' => 'background',
				'title' => __('Background for Slider Text', 'coupon' ),
				'sub_desc' => __('Set background color, pattern or image for slider text from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#28202e',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		)
	);

	/* ==========================================================================
	Tab 4 - Tabs
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Category Tabs', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control homepage Coupon Category Tabs.', 'coupon' ) . '</p>',
		'fields' => array(

			array(
				'id' => 'mts_tab_cat_select',
				'title' => __('Coupon Category Tabs', 'coupon'),
				'sub_desc' => sprintf( __('Add a new Tab to select any Coupon Category. You can manage Coupon Posts from %s.', 'coupon' ), '<a href="edit.php?post_type=coupons"><b>' . __( 'here', 'coupon' ) . '</b></a>' ),
				'type' => 'group',
				'groupname' => __('Category Tab', 'coupon'), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_tab_cat',
						'type' => 'cats_select_custompost',
						'title' => __('Select Category', 'coupon'),
						'tax' => 'mts_coupon_categories',
						'args' => array( 'hide_empty' => false, 'include_latest' => __( 'Latest Coupons', 'coupon' ) ),
					),
				),
				'std' => array()
			),
			array(
				'id' => 'mts_tab_postsnum',
				'type' => 'text',
				'class' => 'small-text',
				'title' => __('Number of posts', 'coupon' ),
				'sub_desc' => __('Enter the number of posts to show in all Tabs.', 'coupon' ),
				'std' => '8',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_tabs_background',
				'type' => 'background',
				'title' => __('Background', 'coupon' ),
				'sub_desc' => __('Set background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#ffffff',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_home_expired_coupons',
				'type' => 'button_set',
				'title' => __('Exclude Expired Coupons', 'coupon') ,
				'options' => array(
					'0' => __('Off', 'coupon'),
					'1' => __('On', 'coupon'),
				),
				'sub_desc' => __('Show or hide epxired coupons on home page category tabs with this option.', 'coupon'),
				'std' => '0',
			),
		),
	);

	/* ==========================================================================
	Tab 5 - Social Icons Section
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Social Icons', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control Social Icons Section.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_social_icons_background',
				'type' => 'background',
				'title' => __('Section Background', 'coupon' ),
				'sub_desc' => __('Set background color, pattern or image for Social Icons section from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#28202e',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_social_icons_title',
				'type' => 'text',
				'title' => __('Section Title', 'coupon' ),
				'sub_desc' => __('Add section title here.', 'coupon' ),
				'std' => 'Fastest Growing Coupons & Deals Company'
			),
			array(
				'id' => 'mts_social_icons_title_color',
				'type' => 'color',
				'title' => __('Section Title Color', 'coupon' ),
				'sub_desc' => __('Set color for the section title.', 'coupon' ),
				'std' => '#dfdedf'
			),
			array(
				'id' => 'mts_social_icons',
				'title' => __('Social Icons', 'coupon'),
				'sub_desc' => __( 'Add Social Media icons in this section.', 'coupon' ),
				'type' => 'group',
				'groupname' => __('Social Icon', 'coupon'), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_social_icon_title',
						'type' => 'text',
						'title' => __('Title', 'coupon'),
					),
					array(
						'id' => 'mts_social_icon',
						'type' => 'icon_select',
						'title' => __('Icon', 'coupon')
					),
					array(
						'id' => 'mts_social_icon_hcolor',
						'type' => 'color',
						'title' => __('Hover Color', 'coupon')
					),
					array(
						'id' => 'mts_social_icon_link',
						'type' => 'text',
						'title' => __('URL', 'coupon'),
					),
				),
				'std' => array(
					'facebook' => array(
						'group_title' => 'Facebook',
						'group_sort' => '1',
						'mts_social_icon_title' => 'Facebook',
						'mts_social_icon' => 'facebook',
						'mts_social_icon_hcolor' => '#5d82d1',
						'mts_social_icon_link' => '#',
					),
					'twitter' => array(
						'group_title' => 'Twitter',
						'group_sort' => '2',
						'mts_social_icon_title' => 'Twitter',
						'mts_social_icon' => 'twitter',
						'mts_social_icon_hcolor' => '#40bff5',
						'mts_social_icon_link' => '#',
					),
					'googleplus' => array(
						'group_title' => 'Google Plus',
						'group_sort' => '3',
						'mts_social_icon_title' => 'Google Plus',
						'mts_social_icon' => 'google-plus',
						'mts_social_icon_hcolor' => '#eb5e4c',
						'mts_social_icon_link' => '#',
					),
					'instagram' => array(
						'group_title' => 'Instagram',
						'group_sort' => '4',
						'mts_social_icon_title' => 'Instagram',
						'mts_social_icon' => 'instagram',
						'mts_social_icon_hcolor' => '#91653f',
						'mts_social_icon_link' => '#',
					),
					'pinterest' => array(
						'group_title' => 'Pinterest',
						'group_sort' => '5',
						'mts_social_icon_title' => 'Pinterest',
						'mts_social_icon' => 'pinterest-p',
						'mts_social_icon_hcolor' => '#e13138',
						'mts_social_icon_link' => '#',
					),
					'rss' => array(
						'group_title' => 'RSS',
						'group_sort' => '6',
						'mts_social_icon_title' => 'RSS',
						'mts_social_icon' => 'rss',
						'mts_social_icon_hcolor' => '#ef922f',
						'mts_social_icon_link' => '#',
					)
				)
			),
		),
	);

	/* ==========================================================================
	Tab 6 - Popular Stores
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Popular Stores', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control Popular Stores section.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_store_title',
				'type' => 'text',
				'title' => __('Store Section Title', 'coupon' ),
				'sub_desc' => __('Enter your Store section title here.', 'coupon' ),
				'std' => 'Popular Stores'
			),
			array(
				'id'		=> 'mts_store_group',
				'type'	  => 'group',
				'title'	 => __('Store Itmes', 'coupon' ),
				'sub_desc'  => __('Add Popular Store items here.', 'coupon' ),
				'groupname' => __('Store', 'coupon' ), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_store_item_title',
						'type' => 'text',
						'title' => __('Store Name', 'coupon' ),
						'sub_desc' => __('Add Store name here, will be not used anywhere.', 'coupon' ),
					),
					array(
						'id' => 'mts_store_item_image',
						'type' => 'upload',
						'title' => __('Logo Image', 'coupon' ),
						'sub_desc' => __('Upload or select an image. Recommended image height: 80px', 'coupon' ),
						'return' => 'url'
					),
					array(
						'id' => 'mts_store_item_hover_text',
						'type' => 'text',
						'title' => __('Hover Text', 'coupon' ),
						'sub_desc' => __('This text will appear when user mouseover the store image.', 'coupon' ),
					),
					array(
						'id' => 'mts_store_item_border',
						'type' => 'color',
						'title' => __('Store Border Color', 'coupon' ),
						'sub_desc' => __('Change Your border color from here.', 'coupon' ),
						'std' => '#d5d1d8'
					),
					array(
						'id' => 'mts_store_item_hover_bg',
						'type' => 'color',
						'title' => __('Store Hover Background Color', 'coupon' ),
						'sub_desc' => __('Change background color on hover', 'coupon' ),
						'std' => '#28202e'
					),
					array(
						'id' => 'mts_store_item_link',
						'type' => 'text',
						'title' => __('Link', 'coupon' ),
						'sub_desc' => __('Insert a link URL for the Store', 'coupon' ),
						'std' => '#'
					),
				),
			),
			array(
				'id' => 'mts_store_background',
				'type' => 'background',
				'title' => __('Background', 'coupon' ),
				'sub_desc' => __('Set store background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#ffffff',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		)
	);

	/* ==========================================================================
	Tab 7 - Subscribe Section
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Subscribe', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control Subscribe section.', 'coupon' ) . '<span style="color: red;"> ' . sprintf(__('This section will not work without %s plugin.','coupon'), '<a href="https://wordpress.org/plugins/wp-subscribe/" target="_blank">WP Subscribe Free</a> or <a href="https://mythemeshop.com/plugins/wp-subscribe-pro/" target="_blank">WP Subscribe Pro</a>' ) . '</span></p>',
		'fields' => array(
			array(
				'id' => 'mts_subscribe_background',
				'type' => 'background',
				'title' => __('Subscribe Background', 'coupon' ),
				'sub_desc' => __('Set Subscribe background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#28202e',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		)
	);

	/* ==========================================================================
	Tab 8 - Sign Up
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Sign Up', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control Sign Up.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_signup_background',
				'type' => 'background',
				'title' => __('Sign Up Section Background', 'coupon' ),
				'sub_desc' => __('Set Sign Up Section&#39;s background color, pattern or image from here.', 'coupon' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#ffffff',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_signup_title',
				'type' => 'textarea',
				'title' => __('Sign Up Title', 'coupon' ),
				'sub_desc' => __('Enter your Sign Up section title here.', 'coupon' ),
				'std' => 'Signup to start earning EXTRA cashback with every transaction you make.<br />Exclusive offers and cashbacks are available only after Signup.'
			),
			array(
				'id' => 'mts_signup_title_color',
				'type' => 'color',
				'title' => __('Sign Up Title Color', 'coupon' ),
				'sub_desc' => __('The theme comes with unlimited color schemes for your theme\'s styling.', 'coupon' ),
				'std' => '#33244a'
			),
			array(
				'id' => 'mts_signup_button',
				'type' => 'button_set_hide_below',
				'title' => __('Sign Up Button', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => wp_kses( __('<strong>Enable or Disable</strong> Sign Up button.', 'coupon' ), array( 'strong' => array() ) ),
				'std' => '1',
				'args' => array('hide' => 3)
			),
			array(
				'id' => 'mts_signup_button_background',
				'type' => 'color',
				'title' => __('Sign Up Button Background Color', 'coupon' ),
				'sub_desc' => __('The theme comes with unlimited color schemes for your theme\'s styling.', 'coupon' ),
				'std' => '#e73931'
			),
			array(
				'id' => 'mts_signup_button_text',
				'type' => 'text',
				'title' => __('Sign Up Button text', 'coupon' ),
				'sub_desc' => __('Enter text here.', 'coupon' ),
				'std' => 'Sign up for Free'
			),
			array(
				'id' => 'mts_signup_button_url',
				'type' => 'text',
				'title' => __('Sign Up Button URL', 'coupon' ),
				'sub_desc' => __('Enter URL here.', 'coupon' ),
				'std' => '#'
			),
		)
	);

	/* ==========================================================================
	Tab 9 - Latest Posts
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Blog Feed', 'coupon'),
		'desc' => __('Control blog settings from here.', 'coupon'),
		'fields' => array(
			array(
				'id' => 'mts_homepage_blog_title',
				'type' => 'text',
				'title' => __( 'Title', 'coupon' ),
				'sub_desc' => __( 'Section Title', 'coupon' ),
				'std' => __('Blog Feed','coupon'),

			),
			array(
				'id' => 'mts_home_blog_col',
				'type' => 'button_set',
				'title' => __('No of Columns', 'coupon'),
				'options' => array('0' => '3 Columns','1' => '4 Columns'),
				'sub_desc' => __('Choose column layout.', 'coupon'),
				'std' => '1',
				'class' => 'green',

			),
			array(
				'id' => 'mts_blog_count_home',
				'type' => 'text',
				'title' => __('No. of Posts - Homepage', 'coupon'),
				'sub_desc' => __('Enter the total number of blog posts you want to show on homepage.', 'coupon'),
				'validate' => 'numeric',
				'std' => '4',
				'class' => 'small-text',

			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-scissors',
		'title' => __('Coupon Archive', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the Coupon Archive Page.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_coupon_cat_slug',
				'type' => 'text',
				'title' => __('Coupon Category Slug', 'coupon' ),
				'sub_desc' => __( 'Coupon category slug to use in URL.', 'coupon' ) . '<br>' . sprintf( __( 'Please visit %s if this value is changed.', 'coupon' ), '<a target="_blank" href="' . admin_url( 'options-permalink.php' ) . '">Settings -> Permalinks</a>' ),
				'std' => 'coupons-category',

			),
			array(
				'id' => 'mts_coupon_tag_slug',
				'type' => 'text',
				'title' => __('Coupon Tag Slug', 'coupon' ),
				'sub_desc' => __( 'Coupon tag slug to use in URL.', 'coupon' ) . '<br>' . sprintf( __( 'Please visit %s if this value is changed.', 'coupon' ), '<a target="_blank" href="' . admin_url( 'options-permalink.php' ) . '">Settings -> Permalinks</a>' ),
				'std' => 'coupons-tag',

			),
			array(
				'id' => 'mts_top_coupon_title',
				'type' => 'text',
				'title' => __('Page Title', 'coupon' ),
				'sub_desc' => __('Set default coupon archive tile from here. On category pages, category name will appear before this title.', 'coupon' ),
				'std' => 'Offers and Coupons',
			),
			array(
				'id' => 'mts_top_coupon_description',
				'type' => 'textarea',
				'title' => __('Small description', 'coupon' ),
				'sub_desc' => __('Small text will appear below archive page title.', 'coupon' ),
				'std' => '202 Offers <span class="color">+ Upto 10% Extra Rewards</span>',
			),
			array(
				'id' => 'mts_coupon_postsnum',
				'type' => 'text',
				'class' => 'small-text',
				'title' => __('Number of Coupons', 'coupon' ),
				'sub_desc' =>  __('Enter the number of coupons to show in the Coupon Archive page.', 'coupon' ),
				'std' => '6',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_coupon_archive_widgets_enabled',
				'type' => 'button_set_hide_below',
				'title' => __('Coupon Category Sidebars', 'coupon'),
				'options' => array(
					'0' => __('Single', 'coupon'),
					'1' => __('Per Category', 'coupon'),
				),
				'sub_desc' => __('Create separate sidebars for each product Coupons category with this option.', 'coupon'),
				'std' => '0',
				'class' => 'green',
				'args' => array('hide' => 1)
			),
			array(
				'id' => 'mts_coupon_archive_widgets',
				'type' => 'coupon_cat_multi_checkbox',
				'title' => __('Create Coupon Category Sidebars', 'coupon'),
				'sub_desc' => __('Create separate category sidebars for archive pages of Coupons.', 'coupon'),
				'options' => array(),
				'std' => array()
			),

			array(
				'id' => 'coupon_archive_tag_widgets_enabled',
				'type' => 'button_set_hide_below',
				'title' => __('Coupon Tag Sidebars', 'coupon'),
				'options' => array(
					'0' => __('Single', 'coupon'),
					'1' => __('Per Tag', 'coupon'),
				),
				'sub_desc' => __('Create separate sidebars for each product Coupons tag with this option.', 'coupon'),
				'std' => '0',
				'class' => 'green',
				'args' => array('hide' => 1)
			),
			array(
				'id' => 'coupon_archive_tag_widgets',
				'type' => 'coupon_tag_multi_checkbox',
				'title' => __('Create Coupon Tag Sidebars', 'coupon'),
				'sub_desc' => __('Create separate tag sidebars for archive pages of Coupons.', 'coupon'),
				'options' => array(),
				'std' => array()
			),

			array(
				'id' => 'mts_coupon_exclude_expired',
				'type' => 'button_set',
				'title' => __('Exclude Expired Coupons', 'coupon') ,
				'options' => array(
					'0' => __('Off', 'coupon'),
					'1' => __('On', 'coupon'),
				),
				'sub_desc' => __('Setting this option to "On" will hide expired coupons from search results and coupon archive pages.', 'coupon'),
				'std' => '0',
			),
			array(
				'id' => 'mts_coupon_thumbnail',
				'type' => 'button_set',
				'title' => __('Coupon Thumbnail', 'coupon') ,
				'options' => array(
					'text' => __('Text', 'coupon'),
					'image' => __('Image', 'coupon'),
				),
				'sub_desc' => wp_kses( __('Setting this option to "image" will show the coupon\'s featured image instead of its featured text. Recommended Size <strong>146X146px</strong>.', 'coupon' ), array( 'strong' => array() ) ),
				'std' => 'text',
			),
			array(
				'id'	 => 'mts_coupon_headline_meta_info',
				'type'	 => 'layout',
				'title'	=> __('Coupon Archive Post Meta Info', 'coupon' ),
				'sub_desc' => __('Organize how you want the post meta info to appear on the Coupon Archive', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'used'	 => __('People Used', 'coupon' ),
						'author'   => __('Author Name', 'coupon' ),
						//'comment'  => __('Comment Count', 'coupon' ),
						'expire'  => __('Expiring Date', 'coupon' )
					),
					'disabled' => array()
				),
				'std'  => array(
					'enabled'  => array(
						'used'	 => __('People Used', 'coupon' ),
						'author'   => __('Author Name', 'coupon' ),
						//'comment'  => __('Comment Count', 'coupon' ),
						'expire'  => __('Expiring Date', 'coupon' )
					),
					'disabled' => array()
				)
			),
			array(
				'id' => 'mts_coupon_pagenavigation_type',
				'type' => 'radio',
				'title' => __('Pagination Type - Coupons Archive', 'coupon'),
				'sub_desc' => __('Select pagination type for Coupon archive.', 'coupon'),
				'options' => array(
					'0'=> __('Default (Next / Previous)', 'coupon'),
					'1' => __('Numbered (1 2 3 4...)', 'coupon'),
					'2' => __( 'AJAX (Load More Button)', 'coupon' ),
					'3' => __( 'AJAX (Auto Infinite Scroll)', 'coupon' )
				),
				'std' => '1'
			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-file-text',
		'title' => __('Coupon Single', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the appearance and functionality of your Coupon single posts page.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_single_coupon_slug',
				'type' => 'text',
				'title' => __('Single Coupon Slug', 'coupon' ),
				'sub_desc' => __( 'Coupon slug to use in URL.', 'coupon' ) . '<br>' . sprintf( __( 'Please visit %s if this value is changed.', 'coupon' ), '<a target="_blank" href="' . admin_url( 'options-permalink.php' ) . '">Settings -> Permalinks</a>' ),
				'std' => 'coupons',

			),
			array(
				'id'	 => 'mts_coupon_single_post_layout',
				'type'	 => 'layout2',
				'title'	=> __('Coupon Single Post Layout', 'coupon' ),
				'sub_desc' => __('Customize the look of coupon single posts', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'coupon-content'   => array(
							'label' 	=> __('Post Content', 'coupon' ),
							'subfields'	=> array()
						),
						'coupon-recent'   => array(
							'label' 	=> __('Recent Offers', 'coupon' ),
							'subfields'	=> array(
								array(
									'id' => 'mts_coupon_recent_postsnum',
									'type' => 'text',
									'class' => 'small-text',
									'title' => __('Number of coupon recent offers', 'coupon' ) ,
									'sub_desc' => __('Enter the number of posts to show in the coupon recent offers section.', 'coupon' ) ,
									'std' => '4',
									'args' => array(
										'type' => 'number'
									)
								),

							)
						),
						'coupon-related'   => array(
							'label' 	=> __('Related Offers', 'coupon' ),
							'subfields'	=> array(
								array(
									'id' => 'mts_coupon_related_postsnum',
									'type' => 'text',
									'class' => 'small-text',
									'title' => __('Number of coupon related offers', 'coupon' ) ,
									'sub_desc' => __('Enter the number of posts to show in the coupon related offers section.', 'coupon' ) ,
									'std' => '3',
									'args' => array(
										'type' => 'number'
									)
								),

							)
						),
						'coupon-author'   => array(
							'label' 	=> __('Author Box', 'coupon' ),
							'subfields'	=> array(
							)
						),
					),
					'disabled' => array(
						'coupon-subscribe'   => array(
							'label' 	=> __('Subscribe Box', 'coupon' ),
							'subfields'	=> array(
							)
						),
						'coupon-tags'   => array(
							'label' 	=> __('Tags', 'coupon' ),
							'subfields'	=> array(
							)
						),
					)
				)
			),
			array(
				'id'	 => 'mts_coupon-single_headline_meta_info',
				'type'	 => 'layout',
				'title'	=> __('Coupon Single Post Meta Info', 'coupon' ),
				'sub_desc' => __('Organize how you want the post meta info to appear on the Coupon single page.', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'used'	 => __('People Used', 'coupon' ),
						'author'   => __('Author Name', 'coupon' ),
						//'comment'  => __('Comment Count', 'coupon' ),
						'expire'  => __('Expiring Date', 'coupon' )
					),
					'disabled' => array()
				),
				'std'  => array(
					'enabled'  => array(
						'used'	 => __('People Used', 'coupon' ),
						'author'   => __('Author Name', 'coupon' ),
						//'comment'  => __('Comment Count', 'coupon' ),
						'expire'  => __('Expiring Date', 'coupon' )
					),
					'disabled' => array()
				)
			),
			array(
				'id' => 'mts_coupon_button_action',
				'type' => 'button_set',
				'title' => __('"Show Coupon" Button Action', 'coupon') ,
				'options' => array(
					'popup' => __('Pop-up', 'coupon'),
					'popunder' => __('Pop-under', 'coupon'),
				) ,
				'sub_desc' => __('Choose how <strong>Show Coupon</strong> button opens the coupon link. The <strong>Activate Deal</strong> buttons will always open deal in new tab.', 'coupon'),
				'std' => 'popup',
				'class' => 'green',
			),
			array(
				'id' => 'mts_coupon_popup_subscribe',
				'type' => 'button_set',
				'title' => __('Subscribe Box in Popup', 'coupon') ,
				'options' => array(
					'0' => __('Off', 'coupon'),
					'1' => __('On', 'coupon'),
				),
				'sub_desc' => __('Show WP Subscribe widget in the coupon popup on your site.', 'coupon'),
				'std' => '0',
			),
			array(
				'id'   => 'mts_popup_social_buttons',
				'type' => 'layout',
				'title'	=> __('Social Media Buttons in Popup', 'coupon' ),
				'sub_desc' => __('Organize how you want the social sharing buttons to appear in the coupon popup on your site.', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'facebookshare'   => __('Facebook Share', 'coupon' ),
						'twitter'   => __('Twitter', 'coupon' ),
						'gplus' => __('Google Plus', 'coupon' ),
						'pinterest' => __('Pinterest', 'coupon' ),
					),
					'disabled' => array(
						'linkedin'  => __('LinkedIn', 'coupon' ),
						'stumble'   => __('StumbleUpon', 'coupon' ),
					)
				),
				'std'  => array(
					'enabled'  => array(
						'facebookshare'   => __('Facebook Share', 'coupon' ),
						'twitter'   => __('Twitter', 'coupon' ),
						'gplus' => __('Google Plus', 'coupon' ),
						'pinterest' => __('Pinterest', 'coupon' ),
					),
					'disabled' => array(
						'linkedin'  => __('LinkedIn', 'coupon' ),
						'stumble'   => __('StumbleUpon', 'coupon' ),
					)
				),

			),

		)
	);

	$sections[] = array(
		'icon' => 'fa fa-list',
		'title' => __('Blog', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of the blog page.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_featured_categories',
				'type' => 'group',
				'title'	 => __('Featured Categories', 'coupon' ),
				'sub_desc'  => __('Select categories appearing on the blog page.', 'coupon' ),
				'groupname' => __('Section', 'coupon' ), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_featured_category',
						'type' => 'cats_select',
						'title' => __('Category', 'coupon' ),
						'sub_desc' => __('Select a category or the latest posts for this section', 'coupon' ),
						'std' => 'latest',
						'args' => array('include_latest' => 1, 'hide_empty' => 0),
					),
					array(
						'id' => 'mts_featured_category_postsnum',
						'type' => 'text',
						'class' => 'small-text',
						'title' => __('Number of posts', 'coupon' ),
						'sub_desc' => sprintf(__('Enter the number of posts to show in this section.<br/><strong>For Latest Posts</strong>, this setting will be ignored, and number set in <a href="%s" target="_blank">Settings&nbsp;&gt;&nbsp;Reading</a> will be used instead.', 'coupon' ), admin_url('options-reading.php')),
						'std' => '4',
						'args' => array('type' => 'number')
					),
				),
				'std' => array(
					'1' => array(
						'group_title' => '',
						'group_sort' => '1',
						'mts_featured_category' => 'latest',
						'mts_featured_category_postsnum' => 4,
					)
				)
			),
			array(
				'id' => 'mts_pagenavigation_type',
				'type' => 'radio',
				'title' => __('Pagination Type', 'coupon' ),
				'sub_desc' => __('Select pagination type.', 'coupon' ),
				'options' => array(
					'0'=> __('Default (Next / Previous)', 'coupon' ),
					'1' => __('Numbered (1 2 3 4...)', 'coupon' ),
					'2' => __( 'AJAX (Load More Button)', 'coupon' ),
					'3' => __( 'AJAX (Auto Infinite Scroll)', 'coupon' )
				),
				'std' => '1'
			),
			array(
				'id' => 'mts_full_posts',
				'type' => 'button_set',
				'title' => __('Posts on blog pages', 'coupon' ),
				'options' => array('0' => __('Excerpts','coupon'),'1' => __('Full posts', 'coupon' )),
				'sub_desc' => __('Show post excerpts or full posts on the blog and other blog archive pages.', 'coupon' ),
				'std' => '0',
				'class' => 'green'
			),
			array(
				'id'	 => 'mts_blog_headline_meta_info',
				'type'	 => 'layout',
				'title'	=> __('Blog Post Meta Info', 'coupon' ),
				'sub_desc' => __('Organize how you want the post meta info to appear on the blog page', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'date'	 => __('Date', 'coupon' ),
						'author'   => __('Author Name', 'coupon' ),
						'comment'  => __('Comment Count', 'coupon' ),
						'category' => __('Categories', 'coupon' )
					),
					'disabled' => array()
				),
				'std'  => array(
					'enabled'  => array(
						'date'	 => __('Date', 'coupon' ),
						'author'   => __('Author Name', 'coupon' ),
						'comment'  => __('Comment Count', 'coupon' ),
						'category' => __('Categories', 'coupon' )
					),
					'disabled' => array()
				)
			),
			array(
				'id' => 'mts_date_layout',
				'type' => 'radio_img',
				'title' => __('Date Formate Style', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Choose the date style for your Blog Posts. You can manage second style from %s.', 'coupon' ), '<a href="options-general.php"><b>' . __( 'here', 'coupon' ) . '</b></a>' ) . '<br></p>',
				'options' => array(
					'datebig' => array('img' => NHP_OPTIONS_URL.'img/layouts/date-big.png'),
					'datesmall' => array('img' => NHP_OPTIONS_URL.'img/layouts/date-default.png')
				),
				'std' => 'datebig'
			),
			array(
				'id' => 'mts_lightbox',
				'type' => 'button_set',
				'title' => __('Lightbox', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('A lightbox is a stylized pop-up that allows your visitors to view larger versions of images without leaving the current page. You can enable or disable the lightbox here.', 'coupon' ),
				'std' => '0'
			),
		)
	);
	$sections[] = array(
		'icon' => 'fa fa-file-text',
		'title' => __('Single Posts', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the appearance and functionality of your single posts page.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id'	 => 'mts_single_post_layout',
				'type'	 => 'layout2',
				'title'	=> __('Single Post Layout', 'coupon' ),
				'sub_desc' => __('Customize the look of single posts', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'content'   => array(
							'label' 	=> __('Post Content', 'coupon' ),
							'subfields'	=> array()
						),
						'related'   => array(
							'label' 	=> __('Related Posts', 'coupon' ),
							'subfields'	=> array(
								array(
									'id' => 'mts_related_posts_taxonomy',
									'type' => 'button_set',
									'title' => __('Related Posts Taxonomy', 'coupon' ) ,
									'options' => array(
										'tags' => __( 'Tags', 'coupon' ),
										'categories' => __( 'Categories', 'coupon' )
									) ,
									'class' => 'green',
									'sub_desc' => __('Related Posts based on tags or categories.', 'coupon' ) ,
									'std' => 'categories'
								),
								array(
									'id' => 'mts_related_postsnum',
									'type' => 'text',
									'class' => 'small-text',
									'title' => __('Number of related posts', 'coupon' ) ,
									'sub_desc' => __('Enter the number of posts to show in the related posts section.', 'coupon' ) ,
									'std' => '4',
									'args' => array(
										'type' => 'number'
									)
								),
							)
						),
						'author'   => array(
							'label' 	=> __('Author Box', 'coupon' ),
							'subfields'	=> array()
						),
					),
					'disabled' => array(
						'tags'   => array(
							'label' 	=> __('Tags', 'coupon' ),
							'subfields'	=> array(
							)
						),
					)
				)
			),
			array(
				'id'	 => 'mts_single_headline_meta_info',
				'type'	 => 'layout',
				'title'	=> __('Meta Info to Show', 'coupon' ),
				'sub_desc' => __('Organize how you want the post meta info to appear', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'author' => __('Author Name', 'coupon' ),
						'comment'  => __('Comment Count', 'coupon' ),
						'category' => __('Categories', 'coupon' ),
						'date' => __('Date', 'coupon' )
					),
					'disabled' => array()
				),
				'std'  => array(
					'enabled'  => array(
						'author' => __('Author Name', 'coupon' ),
						'comment'  => __('Comment Count', 'coupon' ),
						'category' => __('Categories', 'coupon' ),
						'date' => __('Date', 'coupon' )
					),
					'disabled' => array()
				)
			),
			array(
				'id' => 'mts_breadcrumb',
				'type' => 'button_set',
				'title' => __('Breadcrumbs', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Breadcrumbs are a great way to make your site more user-friendly. You can enable them by checking this box.', 'coupon' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_author_comment',
				'type' => 'button_set',
				'title' => __('Highlight Author Comment', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Use this button to highlight author comments.', 'coupon' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_comment_date',
				'type' => 'button_set',
				'title' => __('Date in Comments', 'coupon' ),
				'options' => array( '0' => __( 'Off', 'coupon' ), '1' => __( 'On', 'coupon' ) ),
				'sub_desc' => __('Use this button to show the date for comments.', 'coupon' ),
				'std' => '1'
			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-group',
		'title' => __('Social Buttons', 'coupon' ),
		'desc' => '<p class="description">' . __('Enable or disable social sharing buttons on single posts using these buttons.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_social_button_layout',
				'type' => 'radio_img',
				'title' => __('Social Sharing Buttons Layout', 'coupon' ),
				'sub_desc' => wp_kses( __('Choose default <strong>social sharing buttons</strong> layout or modern <strong>social sharing buttons</strong> layout for your site. ', 'coupon' ), array( 'strong' => array() ) ),
				'options' => array(
					'default' => array('img' => NHP_OPTIONS_URL.'img/layouts/default-social.jpg'),
					'modern' => array('img' => NHP_OPTIONS_URL.'img/layouts/modern-social.jpg')
				),
				'std' => 'default',

			),
			array(
				'id' => 'mts_social_button_position',
				'type' => 'button_set',
				'title' => __('Social Sharing Buttons Position', 'coupon' ),
				'options' => array('top' => __('Above Content', 'coupon' ), 'bottom' => __('Below Content', 'coupon' ), 'floating' => __('Floating', 'coupon' )),
				'sub_desc' => __('Choose position for Social Sharing Buttons. Floating buttons will only appear on screens larger than 1470px.', 'coupon' ),
				'std' => 'floating',
				'class' => 'green'
			),
			array(
				'id' => 'mts_social_buttons_on_pages',
				'type' => 'button_set',
				'title' => __('Social Sharing Buttons on Pages', 'coupon' ),
				'options' => array('0' => __('Off', 'coupon' ), '1' => __('On', 'coupon' )),
				'sub_desc' => __('Enable the sharing buttons for pages too, not just posts.', 'coupon' ),
				'std' => '0',
			),
			array(
				'id'   => 'mts_social_buttons',
				'type' => 'layout',
				'title'	=> __('Social Media Buttons', 'coupon' ),
				'sub_desc' => __('Organize how you want the social sharing buttons to appear on single posts', 'coupon' ),
				'options'  => array(
					'enabled'  => array(
						'facebookshare'   => __('Facebook Share', 'coupon' ),
						'facebook'  => __('Facebook Like', 'coupon' ),
						'twitter'   => __('Twitter', 'coupon' ),
						'gplus' => __('Google Plus', 'coupon' ),
						'pinterest' => __('Pinterest', 'coupon' ),
					),
					'disabled' => array(
						'linkedin'  => __('LinkedIn', 'coupon' ),
						'stumble'   => __('StumbleUpon', 'coupon' ),
						'reddit'   => __('Reddit', 'coupon' ),
					)
				),
				'std'  => array(
					'enabled'  => array(
						'facebookshare'   => __('Facebook Share', 'coupon' ),
						'facebook'  => __('Facebook Like', 'coupon' ),
						'twitter'   => __('Twitter', 'coupon' ),
						'gplus' => __('Google Plus', 'coupon' ),
						'pinterest' => __('Pinterest', 'coupon' ),
					),
					'disabled' => array(
						'linkedin'  => __('LinkedIn', 'coupon' ),
						'stumble'   => __('StumbleUpon', 'coupon' ),
						'reddit'   => __('Reddit', 'coupon' ),
					)
				),

			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-bar-chart-o',
		'title' => __('Ad Management', 'coupon' ),
		'desc' => '<p class="description">' . __('Now, ad management is easy with our options panel. You can control everything from here, without using separate plugins.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_posttop_adcode',
				'type' => 'textarea',
				'title' => __('Below Post Title', 'coupon' ),
				'sub_desc' => __('Paste your Adsense, BSA or other ad code here to show ads below your article title on single posts.', 'coupon' )
			),
			array(
				'id' => 'mts_posttop_adcode_time',
				'type' => 'text',
				'title' => __('Show After X Days', 'coupon' ),
				'sub_desc' => __('Enter the number of days after which you want to show the Below Post Title Ad. Enter 0 to disable this feature.', 'coupon' ),
				'validate' => 'numeric',
				'std' => '0',
				'class' => 'small-text',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_postend_adcode',
				'type' => 'textarea',
				'title' => __('Below Post Content', 'coupon' ),
				'sub_desc' => __('Paste your Adsense, BSA or other ad code here to show ads below the post content on single posts.', 'coupon' )
			),
			array(
				'id' => 'mts_postend_adcode_time',
				'type' => 'text',
				'title' => __('Show After X Days', 'coupon' ),
				'sub_desc' => __('Enter the number of days after which you want to show the Below Post Title Ad. Enter 0 to disable this feature.', 'coupon' ),
				'validate' => 'numeric',
				'std' => '0',
				'class' => 'small-text',
				'args' => array('type' => 'number')
			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-columns',
		'title' => __('Sidebars', 'coupon' ),
		'desc' => '<p class="description">' . __('Now you have full control over the sidebars. Here you can manage sidebars and select one for each section of your site, or select a custom sidebar on a per-post basis in the post editor.', 'coupon' ) . '<br></p>',
		'fields' => array(
			array(
				'id' => 'mts_custom_sidebars',
				'type'  => 'group', //doesn't need to be called for callback fields
				'title' => __('Custom Sidebars', 'coupon' ),
				'sub_desc'  => wp_kses( __('Add custom sidebars. <strong style="font-weight: 800;">You need to save the changes to use the sidebars in the dropdowns below.</strong><br />You can add content to the sidebars in Appearance &gt; Widgets.', 'coupon' ), array( 'strong' => array(), 'br' => array() ) ),
				'groupname' => __('Sidebar', 'coupon' ), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_custom_sidebar_name',
						'type' => 'text',
						'title' => __('Name', 'coupon' ),
						'sub_desc' => __('Example: Homepage Sidebar', 'coupon' )
					),
					array(
						'id' => 'mts_custom_sidebar_id',
						'type' => 'text',
						'title' => __('ID', 'coupon' ),
						'sub_desc' => __('Enter a unique ID for the sidebar. Use only alphanumeric characters, underscores (_) and dashes (-), eg. "sidebar-home"', 'coupon' ),
						'std' => 'sidebar-'
					),
				),
			),
			array(
				'id' => 'mts_sidebar_for_home',
				'type' => 'sidebars_select',
				'title' => __('Homepage', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the homepage.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array( 'sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_post',
				'type' => 'sidebars_select',
				'title' => __('Single Post', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the single posts. If a post has a custom sidebar set, it will override this.', 'coupon' ),
				'args' => array('exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_page',
				'type' => 'sidebars_select',
				'title' => __('Single Page', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the single pages. If a page has a custom sidebar set, it will override this.', 'coupon' ),
				'args' => array('exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_coupons',
				'type' => 'sidebars_select',
				'title' => __('Single Coupons', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the single coupon pages. If a post has a custom sidebar set, it will override this.', 'coupon' ),
				'args' => array('exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_coupons_archive',
				'type' => 'sidebars_select',
				'title' => __('Coupons Archive', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the coupon archives. It will be applied to the latest coupons archive and the coupon category and tag archives.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_archive',
				'type' => 'sidebars_select',
				'title' => __('Archive', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the archives. Specific archive sidebars will override this setting (see below).', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_category',
				'type' => 'sidebars_select',
				'title' => __('Category Archive', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the category archives.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_tag',
				'type' => 'sidebars_select',
				'title' => __('Tag Archive', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the tag archives.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_date',
				'type' => 'sidebars_select',
				'title' => __('Date Archive', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the date archives.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_author',
				'type' => 'sidebars_select',
				'title' => __('Author Archive', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the author archives.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_search',
				'type' => 'sidebars_select',
				'title' => __('Search', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the search results.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_notfound',
				'type' => 'sidebars_select',
				'title' => __('404 Error', 'coupon' ),
				'sub_desc' => __('Select a sidebar for the 404 Not found pages.', 'coupon' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_shop',
				'type' => 'sidebars_select',
				'title' => __('Shop Pages', 'coupon' ),
				'sub_desc' => wp_kses( __('Select a sidebar for Shop main page and product archive pages (WooCommerce plugin must be enabled). Default is <strong>Shop Page Sidebar</strong>.', 'coupon' ), array( 'strong' => array() ) ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => 'shop-sidebar'
			),
			array(
				'id' => 'mts_sidebar_for_product',
				'type' => 'sidebars_select',
				'title' => __('Single Product', 'coupon' ),
				'sub_desc' => wp_kses( __('Select a sidebar for single products (WooCommerce plugin must be enabled). Default is <strong>Single Product Sidebar</strong>.', 'coupon' ), array( 'strong' => array() ) ),
				'args' => array('allow_nosidebar' => false, 'exclude' => array('sidebar-coupons-*', 'sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe')),
				'std' => 'product-sidebar'
			),
		),
	);

	$sections[] = array(
		'icon' => 'fa fa-list-alt',
		'title' => __('Navigation', 'coupon' ),
		'desc' => '<p class="description"><div class="controls">' . sprintf( __('Navigation settings can now be modified from the %s.', 'coupon' ), '<a href="nav-menus.php"><b>' . __( 'Menus Section', 'coupon' ) . '</b></a>' ) . '<br></div></p>'
	);


	/* ==========================================================================
	Tab 1 - Commission Junction
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Commission Junction', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the settings to import coupons from Commission Junction.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_cj_developer_key',
				'type' => 'textarea',
				'title' => __('Developer Key / Personal Access Token', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Signup for a free %s.', 'coupon' ), '<a href="//developers.cj.com/" target="_blank"><b>' . __( 'CJ Affiliate developer account', 'coupon' ) . '</b></a>' ) . '<br>' . sprintf( __('The developer key will be deprecated soon, get personal access token here in %s.', 'coupon' ), '<a href="//developers.cj.com/account/personal-access-tokens" target="_blank"><b>' . __( 'CJ developer account', 'coupon' ) . '</b></a>' ) .'</p>',

			),
			array(
				'id' => 'mts_cj_web_id',
				'type' => 'text',
				'title' => __('Website ID', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Signup for a free %s.', 'coupon' ), '<a href="//signup.cj.com/member/signup/publisher" target="_blank"><b>' . __( 'CJ Affiliate publisher account', 'coupon' ) . '</b></a>' ) . '<br></p>',

			),
			array(
				'id' => 'mts_cj_total',
				'type' => 'text',
				'title' => __('Coupons Per Page', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'How many number of coupons to import?' ).'</p>',

			),
			array(
				'id' => 'mts_cj_advertiser_status',
				'type' => 'select',
				'title' => __('Advertiser Status', 'coupon' ),
				'options' => array(
					''	=> __('Both', 'coupon'),
					'joined' => __( 'Joined', 'coupon' ),
					'notjoined' => __( 'Not Joined', 'coupon' ),
				),
				'std' => 'joined',

			),
			array(
				'id' => 'mts_cj_keywords',
				'type' => 'textarea',
				'title' => __('Keywords', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'Restrict search results based on keywords found in the advertiser\'s name, the product name or the product description' ).'</p>',

			),

			array(
				'id' => 'mts_cj_cat',
				'type' => 'select',
				'title' => __('Categories', 'coupon' ),
				'options' => mts_cj_categories(),
				'class' => 'widefat',
				'sub_desc' => '<p class="description">'.__( 'Restrict search results based on the categories' ).'</p>',

			),

			array(
				'id' => 'mts_cj_lang',
				'type' => 'select',
				'title' => __('Languages', 'coupon' ),
				'options' => mts_cj_languages(),
				'class' => 'widefat',
				'sub_desc' => '<p class="description">'.__( 'Restrict search results based on the languages' ).'</p>',

			),
			array(
				'id' => 'mts_cj_create_category',
				'type' => 'button_set',
				'title' => __('Create Category', 'coupon' ),
				'options' => array( '0' => __( 'No', 'coupon' ), '1' => __( 'Yes', 'coupon' ) ),
				'std' => '0',

			),
			array(
				'id' => 'mts_cj_frequency',
				'type' => 'select',
				'title' => __('Schedule Posting', 'coupon' ),
				'options' => apply_filters('mts_cj_frequency',
					array(
						'hourly'	=> __('Hourly', 'coupon'),
						'twicedaily' => __( 'Twice Daily', 'coupon' ),
						'Daily' => __( 'Daily', 'coupon' ),
					)),
				'std' => 'hourly',

			),
		)
	);

	/* ==========================================================================
	Tab 2 - LinkShare
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('LinkShare', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the settings to import coupons from LinkShare.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_linkshare_api',
				'type' => 'textarea',
				'title' => __('API Token', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Signup for a free %s.', 'coupon' ), '<a href="//developers.cj.com/" target="_blank"><b>' . __( 'LinkShare Publisher account', 'coupon' ) . '</b></a>' ) . '<br></p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_username',
				'type' => 'text',
				'title' => __('Linkshare Username', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'LinkShare Username', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_password',
				'type' => 'text',
				'title' => __('Linkshare Password', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'LinkShare Affiliate Password', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_siteid',
				'type' => 'text',
				'title' => __('Site ID', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'LinkShare Site ID', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_total',
				'type' => 'text',
				'title' => __('Coupons Per Page', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'How many number of coupons to import?' ).'</p>',
				'std' => '10',

			),
			array(
				'id' => 'mts_linkshare_network',
				'type' => 'select',
				'title' => __('Network', 'coupon' ),
				'options' => mts_linkshare_network('network'),
				'class' => 'widefat',
				'sub_desc' => '<p class="description">'.__( 'Filter by one or more networks', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_category',
				'type' => 'multi_select',
				'title' => __('Category', 'coupon' ),
				'options' => mts_linkshare_network('category'),
				'class' => 'widefat',
				'sub_desc' => '<p class="description">'.__( 'Restrict search results based on the languages' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_promotiontype',
				'type' => 'multi_select',
				'title' => __('Promotion Type', 'coupon' ),
				'options' => mts_linkshare_network('promotiontype'),
				'class' => 'widefat',
				'sub_desc' => '<p class="description">'.__( 'Restrict search results based on the languages' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_linkshare_create_category',
				'type' => 'button_set',
				'title' => __('Create Category', 'coupon' ),
				'options' => array( '0' => __( 'No', 'coupon' ), '1' => __( 'Yes', 'coupon' ) ),
				'std' => '1',

			),
			array(
				'id' => 'mts_linkshare_create_tags',
				'type' => 'button_set',
				'title' => __('Create Tags', 'coupon' ),
				'options' => array( '0' => __( 'No', 'coupon' ), '1' => __( 'Yes', 'coupon' ) ),
				'std' => '1',

			),
			array(
				'id' => 'mts_linkshare_frequency',
				'type' => 'select',
				'title' => __('Schedule Posting', 'coupon' ),
				'options' => apply_filters('mts_linkshare_frequency',
					array(
						'hourly'	=> __('Hourly', 'coupon'),
						'twicedaily' => __( 'Twice Daily', 'coupon' ),
						'Daily' => __( 'Daily', 'coupon' ),
					)),
				'std' => 'hourly',

			),
		)
	);

	/* ==========================================================================
	Tab 3 - TradeDoubler
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('TradeDoubler', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the settings to import coupons from TradeDoubler.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_tradedoubler_token',
				'type' => 'textarea',
				'title' => __('Token', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Signup for a free %s.', 'coupon' ), '<a href="//publisher.tradedoubler.com/public/aSignup.action?language=en&country=GB" target="_blank"><b>' . __( 'TradeDoubler Publisher account', 'coupon' ) . '</b></a>' ) . '<br></p>',
				'std' => '',

			),
			array(
				'id' => 'mts_tradedoubler_voucher',
				'type' => 'multi_select',
				'title' => __('Voucher type ID', 'coupon' ),
				'options' => array('' => __('Select Voucher', 'coupon'), '1' => 'Voucher', '2' => 'Discount', '3' => 'Free article', '4' => 'Free shipping', '5' => 'Raffle'),
				'class' => 'widefat',
				'sub_desc' => '<p class="description">'.__( 'Primary key of the voucher.', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_tradedoubler_programId',
				'type' => 'text',
				'title' => __('ProgramId', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'Primary key of the program the voucher corresponds to', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_tradedoubler_keywords',
				'type' => 'text',
				'title' => __('Keywords', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'Matches title, description, short description and code.', 'coupon' ).'</p>',
				'std' => '',

			),
			array(
				'id' => 'mts_tradedoubler_site_specific',
				'type' => 'button_set',
				'title' => __('Site specific', 'coupon' ),
				'options' => array( '0' => __( 'False', 'coupon' ), '1' => __( 'True', 'coupon' ) ),
				'sub_desc' => __('Set to True if you only want to get your exclusive voucher codes.', 'coupon' ),
				'std' => '0',

			),
			array(
				'id' => 'mts_tradedoubler_language',
				'type' => 'text',
				'title' => __('Language ID', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Enter an %s code to filter on language.', 'coupon' ), '<a href="//www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank"><b>' . __( 'ISO 639-1', 'coupon' ) . '</b></a>' ) . '<br></p>',
				'std' => '',

			),
			array(
				'id' => 'mts_tradedoubler_total',
				'type' => 'text',
				'title' => __('Coupons Per Page', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'How many number of coupons to import?' ).'</p>',
				'std' => '10',

			),
			array(
				'id' => 'mts_tradedoubler_frequency',
				'type' => 'select',
				'title' => __('Schedule Posting', 'coupon' ),
				'options' => apply_filters('mts_tradedoubler_frequency',
					array(
						'hourly'	=> __('Hourly', 'coupon'),
						'twicedaily' => __( 'Twice Daily', 'coupon' ),
						'Daily' => __( 'Daily', 'coupon' ),
					)),
				'std' => 'hourly',

			),
		)
	);

	/* ==========================================================================
	Tab 4 - Admitad
	========================================================================== */
	$sections[] = array(
		'icon' => '',
		'title' => __('Admitad', 'coupon' ),
		'desc' => '<p class="description">' . __('From here, you can control the settings to import coupons from Admitad.', 'coupon' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_admitad_username',
				'type' => 'text',
				'title' => __('Username', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Signup for a free %s.', 'coupon' ), '<a href="//www.admitad.com/in/webmaster/registration/" target="_blank"><b>' . __( 'Admitad Publisher account', 'coupon' ) . '</b></a>' ) . '<br></p>',
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_password',
				'type' => 'text',
				'title' => __('Password', 'coupon' ),
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_clientid',
				'type' => 'text',
				'title' => __('Client ID', 'coupon' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Admitad %s.', 'coupon' ), '<a href="//www.admitad.com/in/webmaster/account/settings/credentials/" target="_blank"><b>' . __( 'ClientID', 'coupon' ) . '</b></a>' ) . '<br></p>',
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_clientsecret',
				'type' => 'text',
				'title' => __('Client Secret Key', 'coupon' ),
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_websiteid',
				'type' => 'text',
				'title' => __('Website ID', 'coupon' ),
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_campaign',
				'type' => 'text',
				'title' => __('Campaign ID', 'coupon' ),
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_category',
				'type' => 'text',
				'title' => __('Category ID', 'coupon' ),
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_region',
				'type' => 'text',
				'title' => __('Region', 'coupon' ),
				'std' => '',

			),
			array(
				'id' => 'mts_admitad_create_category',
				'type' => 'button_set',
				'title' => __('Create Category', 'coupon' ),
				'options' => array( '0' => __( 'No', 'coupon' ), '1' => __( 'Yes', 'coupon' ) ),
				'std' => '1',

			),
			array(
				'id' => 'mts_admitad_total',
				'type' => 'text',
				'title' => __('Coupons Per Page', 'coupon' ),
				'sub_desc' => '<p class="description">'.__( 'How many number of coupons to import?' ).'</p>',
				'std' => '10',

			),
			array(
				'id' => 'mts_admitad_frequency',
				'type' => 'select',
				'title' => __('Schedule Posting', 'coupon' ),
				'options' => apply_filters('mts_tradedoubler_frequency',
					array(
						'hourly'	=> __('Hourly', 'coupon'),
						'twicedaily' => __( 'Twice Daily', 'coupon' ),
						'Daily' => __( 'Daily', 'coupon' ),
					)),
				'std' => 'hourly',

			),
		)
	);

	$tabs = array();

	$args['presets'] = array();
	$args['show_translate'] = false;
	include('theme-presets.php');

	global $NHP_Options;
	$NHP_Options = new NHP_Options($sections, $args, $tabs);

} //function

add_action('init', 'setup_framework_options', 0);

/*
 *
 * Custom function for the callback referenced above
 *
 */
function my_custom_field($field, $value){
	print_r($field);
	print_r($value);

}//function

/*
 *
 * Custom function for the callback validation referenced above
 *
 */
function validate_callback_function($field, $value, $existing_value){

	$error = false;
	$value =  'just testing';
	$return['value'] = $value;
	if($error == true){
		$return['error'] = $field;
	}
	return $return;

}//function

/*--------------------------------------------------------------------
 *
 * Default Font Settings
 *
 --------------------------------------------------------------------*/
if(function_exists('mts_register_typography')) {
	mts_register_typography( array(
		'logo_font' => array(
			'preview_text' => __( 'Text Logo Font', 'coupon' ),
			'preview_color' => 'dark',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '24px',
			'font_color' => '#ffffff',
			'css_selectors' => '.text-logo a'
		),
		'navigation_font' => array(
			'preview_text' => __( 'Navigation Font', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '14px',
			'font_color' => '#33244a',
			'additional_css' => 'text-transform: uppercase;',
			'css_selectors' => '.menu li, .menu li a'
		),
		'home_title_font' => array(
			'preview_text' => __( 'Home Section Title', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_size' => '30px',
			'font_variant' => 'normal',
			'font_color' => '#33244a',
			'css_selectors' => '.featured-category-title'
		),
		'signup_title_font' => array(
			'preview_text' => __( 'Blog Page Post Title', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_size' => '24px',
			'font_variant' => 'normal',
			'font_color' => '#33244a',
			'css_selectors' => '.latestPost .title a'
		),
		'single_title_font' => array(
			'preview_text' => __( 'Single Article Title', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_size' => '30px',
			'font_variant' => 'normal',
			'font_color' => '#33244a',
			'css_selectors' => '.single-title, .related-posts h4, #respond h4, .total-comments, .postauthor h4, .coupon-related-posts h4'
		),
		'content_font' => array(
			'preview_text' => __( 'Content Font', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_size' => '14px',
			'font_variant' => 'normal',
			'font_color' => '#33244a',
			'css_selectors' => 'body'
		),
		'sidebar_title_font' => array(
			'preview_text' => __( 'Sidebar Title Font', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '18px',
			'font_color' => '#28202e',
			'additional_css' => 'text-transform: uppercase;',
			'css_selectors' => '.widget h3'
		),
		'sidebar_font' => array(
			'preview_text' => __( 'Sidebar Font', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '14px',
			'font_color' => '#33244a',
			'css_selectors' => '#sidebar .widget'
		),
		'sidebar_url_font' => array(
			'preview_text' => __( 'Sidebar URL Font', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '16px',
			'font_color' => '#33244a',
			'css_selectors' => '#sidebar .post-title, .related-posts .latestPost .title a, #sidebar .entry-title a'
		),
		'footer_title_font' => array(
			'preview_text' => __( 'Footer Title Font', 'coupon' ),
			'preview_color' => 'dark',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '18px',
			'font_color' => '#e0dfe0',
			'css_selectors' => '.footer-widgets h3, .footer-widgets h3 a'
		),
		'footer_widget_font' => array(
			'preview_text' => __( 'Footer Widget Font', 'coupon' ),
			'preview_color' => 'dark',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '14px',
			'font_color' => '#aba9ac',
			'css_selectors' => '.footer-widgets, .footer-widgets a, .footer-widgets .widget_nav_menu a, .footer-widgets .wpt_widget_content a, .footer-widgets .wp_review_tab_widget_content a, #copyright-note, #site-footer .entry-title a, #site-footer .merchant-rewards'
		),
		'h1_headline' => array(
			'preview_text' => __( 'Content H1', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '36px',
			'font_color' => '#33244a',
			'css_selectors' => 'h1'
		),
		'h2_headline' => array(
			'preview_text' => __( 'Content H2', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '30px',
			'font_color' => '#33244a',
			'css_selectors' => 'h2'
		),
		'h3_headline' => array(
			'preview_text' => __( 'Content H3', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '28px',
			'font_color' => '#33244a',
			'css_selectors' => 'h3'
		),
		'h4_headline' => array(
			'preview_text' => __( 'Content H4', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '26px',
			'font_color' => '#33244a',
			'css_selectors' => 'h4'
		),
		'h5_headline' => array(
			'preview_text' => __( 'Content H5', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '24px',
			'font_color' => '#33244a',
			'css_selectors' => 'h5'
		),
		'h6_headline' => array(
			'preview_text' => __( 'Content H6', 'coupon' ),
			'preview_color' => 'light',
			'font_family' => 'Rubik',
			'font_variant' => 'normal',
			'font_size' => '22px',
			'font_color' => '#33244a',
			'css_selectors' => 'h6'
		)
	));
}

if(!function_exists('mts_cj_languages')) {
	function mts_cj_languages() {
		$mts_options = get_option( MTS_THEME_NAME, array() );
		if ( empty( $mts_options ) ) {
			return array();
		}

		if(isset($mts_options['mts_cj_languages']) && !empty($mts_options['mts_cj_languages']) ) {
			$languages = $mts_options['mts_cj_languages'];
		} else {
			$cj = new MTS_CJ();
			$languages = $cj->languages();
			$mts_options['mts_cj_languages'] = $languages;
			update_option(MTS_THEME_NAME, $mts_options);
		}

		$lang_data = array();
		if(!empty($languages)) {
			$lang_data[''] = __('All', 'coupon');
			foreach($languages as $key => $language) {
				$lang_data[$key] = $language;
			}
		}
		return apply_filters('mts_cj_languages', $lang_data);
	}
}

if(!function_exists('mts_cj_categories')) {
	function mts_cj_categories() {
		$mts_options = get_option( MTS_THEME_NAME, array() );
		if ( empty( $mts_options ) ) {
			return array();
		}

		if(isset($mts_options['mts_cj_categories']) && !empty($mts_options['mts_cj_categories']) ) {
			$categories = $mts_options['mts_cj_categories'];
		} else {
			$cj = new MTS_CJ();
			$categories = $cj->categories();
			$mts_options['mts_cj_categories'] = $categories;
			update_option(MTS_THEME_NAME, $mts_options);
		}
		$cat_data = array();
		if(!empty($categories)) {
			$cat_data[''] = __('All', 'coupon');
			foreach($categories as $key => $category) {
				$cat_data[$category] = $category;
			}
		}
		return $cat_data;
	}
}

if(!function_exists('mts_linkshare_network')) {
	function mts_linkshare_network($type = 'network', $network_id = 1) {
		$mts_options = get_option( MTS_THEME_NAME, array() );
		if ( empty( $mts_options ) ) {
			return array();
		}

		$data = array();
		if($network_id != 1 && isset($mts_options['mts_linkshare_network']) && !empty($mts_options['mts_linkshare_network'])) {
			$network_id = $mts_options['mts_linkshare_network'];
		} else {
			$network_id = '';
		}
		if(isset($mts_options['mts_linkshare_data']) && !empty($mts_options['mts_linkshare_data'])) {
			$data = $mts_options['mts_linkshare_data'];
		} else {
			try{
				$client = new RakuteAPI();
				$parameters = ['promocat'=>1];
				$products = $client->productSearch($parameters);
				if(!is_wp_error($products)) {
					$networks = isset( $products['network'] ) ? (array) $products['network'] : array();
					$network_data = array();
					$network_data['network']['all'] = __('All', 'coupon');
					foreach($networks as $network) {
						$network_id = $network['@attributes']['id'];
						$network_data['network'][$network_id] = $network['name'];
						$network_data[$network_id]['category']['all'] = __('All', 'coupon');
						$network_data[$network_id]['category'] = $network['categories']['category'];
						$network_data[$network_id]['promotiontype']['all'] = __('All', 'coupon');
						$network_data[$network_id]['promotiontype'] = $network['promotiontypes']['promotiontype'];
						$network_data[$network_id]['name'] = $network['name'];
					}
					$mts_options['mts_linkshare_data'] = $network_data;
					update_option(MTS_THEME_NAME, $mts_options);
					$data = $network_data;
				}
			} catch (\Exception $ex) {
				// echo $ex->getMessage();
				return array();
			}
		}
		if($type == 'network') {
			return $data[$type];
		} else {
			if( is_array( $data ) && isset( $data[0] ) ){
					return $data[$network_id][$type];
			}
			return array('all' => 'ALL');
		}

	}

}

function mts_get_linksharedata_callback() {
	$network_id = $_POST['network'];
	$category_data = $promotion_data = '';
	$categories = mts_linkshare_network('category', $network_id);
	$promotiontypes = mts_linkshare_network('promotiontype', $network_id);
	ob_start();

	if(!empty($categories)) {
		$category_data .= '<option value="">'.__('All', 'coupon').'</option>';
		foreach($categories as $key => $category) {
			$category_data .= '<option value="'.$key.'">'.$category.'</option>';
		}
	}

	if(!empty($promotiontypes)) {
		$promotion_data .= '<option value="">'.__('All', 'coupon').'</option>';
		foreach($promotiontypes as $key => $promotiontype) {
			$promotion_data .= '<option value="'.$key.'">'.$promotiontype.'</option>';
		}
	}

	wp_send_json( array( 'category' => $category_data, 'promotion' => $promotion_data ) );
	die();
}

add_action('wp_ajax_mts_get_linksharedata', 'mts_get_linksharedata_callback');
