<?php
/**
 * Plugin Name: WP Shortcode by MyThemeShop
 * Plugin URI: http://mythemeshop.com/
 * Description: With the vast array of shortcodes, you can quickly and easily build content for your posts and pages and turbocharge your blogging experience.
 * Author: MyThemeShop
 * Version: 1.4.16
 * Author URI: http://mythemeshop.com/
 * MTS Product Type: Free
 *
 * @package WP_Shortcode
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WS_VERSION', '1.4.16' );

add_action( 'wp_enqueue_scripts', 'mts_wpshortcodes_scripts', 99 );
/**
 * Register Plugin Scritps.
 */
function mts_wpshortcodes_scripts() {
	wp_register_style( 'tipsy', plugins_url( 'css/tipsy.css', __FILE__ ), null, WS_VERSION );
	wp_register_style( 'mts_wpshortcodes', plugins_url( 'css/wp-shortcode.css', __FILE__ ), null, WS_VERSION );
	wp_register_script( 'tipsy', plugins_url( 'js/jquery.tipsy.js', __FILE__ ), null, array( 'jquery' ), WS_VERSION );
	wp_register_script( 'mts_wpshortcodes', plugins_url( 'js/wp-shortcode.js', __FILE__ ), null, array( 'jquery' ), null, WS_VERSION );
}

add_action(
	'admin_enqueue_scripts',
	function() {
		wp_enqueue_script( 'mts_wpshortcodes_admin', plugins_url( 'js/admin.js', __FILE__ ), null, array( 'jquery' ), WS_VERSION );
	}
);

/**
 * Enqueue Scripts.
 *
 * @param string $shortcode Shortcode.
 */
function mts_wps_enqueue_scripts( $shortcode = '' ) {
	if ( $shortcode == 'tooltip' ) {
		wp_enqueue_style( 'tipsy' );
		wp_enqueue_script( 'tipsy' );
	}
	wp_enqueue_style( 'mts_wpshortcodes' );
	if ( $shortcode == 'tooltip' || $shortcode == 'tabs' || $shortcode == 'toggle' ) {
		wp_enqueue_script( 'mts_wpshortcodes' );
	}
}
add_action( 'wps_enqueue_style', 'mts_wps_enqueue_scripts', 10, 1 );

/**
 * Load Textdomain.
 */
function mts_wpshortcodes_load_textdomain() {
	load_plugin_textdomain( 'wp-shortcode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'mts_wpshortcodes_load_textdomain' );

// hide built-in shortcodes button for MTS themes.
add_action( 'admin_head', 'mts_wpshortcodes_theme_compatibility' );

/**
 * Add theme compatibility.
 */
function mts_wpshortcodes_theme_compatibility() {
	echo '<style type="text/css">#content_mnmpanel {display: none;}</style>';
}

// Include TinyMCE.
require 'tinymce/tinymce.php';

// override theme shortcodes.
add_action( 'after_setup_theme', 'mts_wpshortcodes_add' );

/**
 * Add Shortcodes.
 */
function mts_wpshortcodes_add() {
	remove_shortcode( 'button-brown' );
	add_shortcode( 'button-brown', 'mts_button_brown' );
	remove_shortcode( 'button-blue' );
	add_shortcode( 'button-blue', 'mts_button_blue' );
	remove_shortcode( 'button-green' );
	add_shortcode( 'button-green', 'mts_button_green' );
	remove_shortcode( 'button-red' );
	add_shortcode( 'button-red', 'mts_button_red' );
	remove_shortcode( 'button-white' );
	add_shortcode( 'button-white', 'mts_button_white' );
	remove_shortcode( 'button-yellow' );
	add_shortcode( 'button-yellow', 'mts_button_yellow' );
	remove_shortcode( 'alert-note' );
	add_shortcode( 'alert-note', 'mts_alert_note' );
	remove_shortcode( 'alert-announce' );
	add_shortcode( 'alert-announce', 'mts_alert_announce' );
	remove_shortcode( 'alert-success' );
	add_shortcode( 'alert-success', 'mts_alert_success' );
	remove_shortcode( 'alert-warning' );
	add_shortcode( 'alert-warning', 'mts_alert_warning' );
	remove_shortcode( 'one_third' );
	add_shortcode( 'one_third', 'mts_one_third' );
	remove_shortcode( 'one_third_last' );
	add_shortcode( 'one_third_last', 'mts_one_third_last' );
	remove_shortcode( 'two_third' );
	add_shortcode( 'two_third', 'mts_two_third' );
	remove_shortcode( 'two_third_last' );
	add_shortcode( 'two_third_last', 'mts_two_third_last' );
	remove_shortcode( 'one_half' );
	add_shortcode( 'one_half', 'mts_one_half' );
	remove_shortcode( 'one_half_last' );
	add_shortcode( 'one_half_last', 'mts_one_half_last' );
	remove_shortcode( 'one_fourth' );
	add_shortcode( 'one_fourth', 'mts_one_fourth' );
	remove_shortcode( 'one_fourth_last' );
	add_shortcode( 'one_fourth_last', 'mts_one_fourth_last' );
	remove_shortcode( 'three_fourth' );
	add_shortcode( 'three_fourth', 'mts_three_fourth' );
	remove_shortcode( 'three_fourth_last' );
	add_shortcode( 'three_fourth_last', 'mts_three_fourth_last' );
	remove_shortcode( 'one_fifth' );
	add_shortcode( 'one_fifth', 'mts_one_fifth' );
	remove_shortcode( 'one_fifth_last' );
	add_shortcode( 'one_fifth_last', 'mts_one_fifth_last' );
	remove_shortcode( 'two_fifth' );
	add_shortcode( 'two_fifth', 'mts_two_fifth' );
	remove_shortcode( 'two_fifth_last' );
	add_shortcode( 'two_fifth_last', 'mts_two_fifth_last' );
	remove_shortcode( 'three_fifth' );
	add_shortcode( 'three_fifth', 'mts_three_fifth' );
	remove_shortcode( 'three_fifth_last' );
	add_shortcode( 'three_fifth_last', 'mts_three_fifth_last' );
	remove_shortcode( 'four_fifth' );
	add_shortcode( 'four_fifth', 'mts_four_fifth' );
	remove_shortcode( 'four_fifth_last' );
	add_shortcode( 'four_fifth_last', 'mts_four_fifth_last' );
	remove_shortcode( 'one_sixth' );
	add_shortcode( 'one_sixth', 'mts_one_sixth' );
	remove_shortcode( 'one_sixth_last' );
	add_shortcode( 'one_sixth_last', 'mts_one_sixth_last' );
	remove_shortcode( 'five_sixth' );
	add_shortcode( 'five_sixth', 'mts_five_sixth' );
	remove_shortcode( 'five_sixth_last' );
	add_shortcode( 'five_sixth_last', 'mts_five_sixth_last' );
	remove_shortcode( 'youtube' );
	add_shortcode( 'youtube', 'mts_youtube_video' );
	remove_shortcode( 'vimeo' );
	add_shortcode( 'vimeo', 'mts_vimeo_video' );
	remove_shortcode( 'googlemap' );
	add_shortcode( 'googlemap', 'mts_googleMaps' );
	remove_shortcode( 'tabs' );
	add_shortcode( 'tabs', 'mts_tabs' );
	remove_shortcode( 'toggle' );
	add_shortcode( 'toggle', 'mts_toggle' );
	remove_shortcode( 'divider' );
	add_shortcode( 'divider', 'mts_divider' );
	remove_shortcode( 'divider_top' );
	add_shortcode( 'divider_top', 'mts_divider_top' );
	remove_shortcode( 'clear' );
	add_shortcode( 'clear', 'mts_clear' );
	add_shortcode( 'tooltip', 'mts_tooltip' );
}


/**
 * Brown Button.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_button_brown( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'url'      => '#',
		'target'   => '_self',
		'position' => 'left',
		'rel'      => '',
	),
	$atts));

	$out = "<a href=\"" . esc_url( $url ) . "\" target=\"" . esc_attr( $target ) . "\" class=\"buttons btn_brown " . sanitize_html_class( $position ) . "\"" . ( $rel? " rel=\"" . esc_attr( $rel ) . "\"" : "" ) . "><span class=\"left\">" . do_shortcode( $content ) . "</span></a>";

	if ( $position == 'center' ) {
		$out = '<div class="button-center">' . $out . '</div>';
	}

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Blue Button.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_button_blue( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'url'      => '#',
				'target'   => '_self',
				'position' => 'left',
				'rel'      => '',
			),
			$atts
		)
	);

	$out = "<a href=\"" . esc_url( $url ) . "\" target=\"" . esc_attr( $target ) . "\" class=\"buttons btn_blue " . sanitize_html_class( $position ) . "\"" . ( $rel? " rel=\"" . esc_attr( $rel ) . "\"" : "" ) . "><span class=\"left\">" . do_shortcode( $content ) . "</span></a>";

	if ( $position == 'center' ) {
		$out = '<div class="button-center">' . $out . '</div>';
	}

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Green Button.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_button_green( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'url'      => '#',
				'target'   => '_self',
				'position' => 'left',
				'rel'      => '',
			),
			$atts
		)
	);

	$out = "<a href=\"" . esc_url( $url ) . "\" target=\"" . esc_attr( $target ) . "\" class=\"buttons btn_green " . sanitize_html_class( $position ) . "\"" . ( $rel? " rel=\"" . esc_attr( $rel ) . "\"" : "" ) . "><span class=\"left\">" . do_shortcode( $content ) . "</span></a>";

	if ( $position == 'center' ) {
			$out = '<div class="button-center">' . $out . '</div>';
	}

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Red Button.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_button_red( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'url'      => '#',
				'target'   => '_self',
				'position' => 'left',
				'rel'      => '',
			),
			$atts
		)
	);

	$out = "<a href=\"" . esc_url( $url ) . "\" target=\"" . esc_attr( $target ) . "\" class=\"buttons btn_red " . sanitize_html_class( $position ) . "\"" . ( $rel? " rel=\"" . esc_attr( $rel ) . "\"" : "" ) . "><span class=\"left\">" . do_shortcode( $content ) . "</span></a>";

	if ( $position == 'center' ) {
			$out = '<div class="button-center">' . $out . '</div>';
	}

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * White Button.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_button_white( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'url'      => '#',
				'target'   => '_self',
				'position' => 'left',
				'rel'      => '',
			),
			$atts
		)
	);

	$out = "<a href=\"" . esc_url( $url ) . "\" target=\"" . esc_attr( $target ) . "\" class=\"buttons btn_white " . sanitize_html_class( $position ) . "\"" . ( $rel? " rel=\"" . esc_attr( $rel ) . "\"" : "" ) . "><span class=\"left\">" . do_shortcode( $content ) . "</span></a>";

	if ( $position == 'center' ) {
		$out = '<div class="button-center">' . $out . '</div>';
	}

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Yellow Button.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_button_yellow( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'url'      => '#',
				'target'   => '_self',
				'position' => 'left',
				'rel'      => '',
			),
			$atts
		)
	);

	$out = "<a href=\"" . esc_url( $url ) . "\" target=\"" . esc_attr( $target ) . "\" class=\"buttons btn_yellow " . sanitize_html_class( $position ) . "\"" . ( $rel? " rel=\"" . esc_attr( $rel ) . "\"" : "" ) . "><span class=\"left\">" . do_shortcode( $content ) . "</span></a>";

	if ( $position == 'center' ) {
			$out = '<div class="button-center">' . $out . '</div>';
	}

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Alter Note.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_alert_note( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'style' => 'note',
			),
			$atts
		)
	);
	$out = "<div class=\"message_box note\"><p>" . do_shortcode( $content ) . "</p></div>";

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Alter announce.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_alert_announce( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'style' => 'announce',
			),
			$atts
		)
	);

	$out = "<div class=\"message_box announce\"><p>" . do_shortcode( $content ) . "</p></div>";

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Alter Success.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_alert_success( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'style' => 'success',
			),
			$atts
		)
	);

	$out = "<div class=\"message_box success\"><p>" . do_shortcode( $content ) . "</p></div>";

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Alter Warning.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_alert_warning( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'style' => 'warning',
			),
			$atts
		)
	);

	$out = "<div class=\"message_box warning\"><p>" . do_shortcode( $content ) . "</p></div>";

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_third( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_third">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_third_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_third column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_two_third( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="two_third">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_two_third_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="two_third column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_half( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_half">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_half_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_half column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_fourth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_fourth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_fourth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_fourth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_three_fourth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="three_fourth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_three_fourth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="three_fourth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_fifth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_fifth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_fifth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_fifth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_two_fifth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="two_fifth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_two_fifth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="two_fifth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_three_fifth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="three_fifth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_three_fifth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="three_fifth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_four_fifth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="four_fifth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_four_fifth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="four_fifth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_sixth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_sixth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_one_sixth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="one_sixth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_five_sixth( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="five_sixth">' . do_shortcode( $content ) . '</div>';
}

/**
 * Column.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_five_sixth_last( $atts, $content = null ) {
	do_action( 'wps_enqueue_style' );
	return '<div class="five_sixth column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}

/**
 * YouTube Video.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_youtube_video( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'id'       => '',
				'width'    => '600',
				'height'   => '340',
				'position' => 'left',
			),
			$atts
		)
	);

	$out = "<div class=\"youtube-video " . sanitize_html_class( $position ) . "\"><iframe width=\"" . esc_attr( $width ) . "\" height=\"" . esc_attr( $height ) . "\" src=\"//www.youtube.com/embed/" . esc_attr( $id ) . "?rel=0\" frameborder=\"0\" allowfullscreen></iframe></div>";

	do_action( 'wps_enqueue_style' );
	return $out;
}

/**
 * Vimeo Video.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_vimeo_video( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'id'       => '',
				'width'    => '600',
				'height'   => '340',
				'position' => 'left',
			),
			$atts
		)
	);

	$out = "<div class=\"vimeo-video " . sanitize_html_class( $position ) . "\"><iframe width=\"" . esc_attr( $width ) . "\" height=\"" . esc_attr( $height ) . "\" src=\"//player.vimeo.com/video/" . esc_attr( $id ) . "?title=0&amp;byline=0&amp;portrait=0\" frameborder=\"0\" allowfullscreen></iframe></div>";

	do_action( 'wps_enqueue_style' );

	return $out;
}

/**
 * Google Maps Shortcode.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_googleMaps( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'width'    => '640',
				'height'   => '480',
				'address'  => '',
				'src'      => '', // for backwards compatibility.
				'position' => 'left',
			),
			$atts
		)
	);

	if ( ! empty( $src ) ) {
		$out = "<div class=\"googlemaps " . sanitize_html_class( $position ) . "\"><iframe width=\"" . esc_attr( $width ) . "\" height=\"" . esc_attr( $height ) . "\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"".esc_url( $src ) . "&output=embed\"></iframe></div>";
	} else {
		$out = "<div class=\"googlemaps " . sanitize_html_class( $position ) . "\"><iframe width=\"" . esc_attr( $width ) . "\" height=\"" . esc_attr( $height ) . "\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"//maps.google.com/maps?q=".urlencode( $address ) . "&output=embed\"></iframe></div>";
	}

	do_action( 'wps_enqueue_style' );

	return $out;
}

/**
 * Tabs Shortcode.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_tabs( $atts, $content = null ) {

	if ( ! preg_match_all( "/(.?)\[(tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/tab\])?(.?)/s", $content, $matches ) ) {
		return do_shortcode( $content );
	} else {
		for ( $i = 0; $i < count( $matches[0]); $i++ ) {
			$matches[3][ $i ] = shortcode_atts(
				array( 'title' => __( 'Untitled', 'wp-shortcode' ) ),
				shortcode_parse_atts( $matches[3][ $i ] )
			);
			$tabid[ $i ]      = 'tab-' . $i . '-' . str_replace( '%', '', strtolower( sanitize_title( $matches[3][ $i ]['title'] ) ) );
		}

		$tabnav = '<ul class="wps_tabs">';

		for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
			$tabnav .= '<li><a href="#" data-tab="' . $tabid[ $i ] . '">' . $matches[3][ $i ]['title'] . '</a></li>';
		}
		$tabnav .= '</ul>';

		$tabcontent = '<div class="tab_container">';

		for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
			$tabcontent .= '<div id="' . $tabid[ $i ] . '" class="tab_content clearfix">' . do_shortcode( trim( $matches[5][ $i ] ) ) . '</div>';
		}
		$tabcontent .= '</div>';

		do_action( 'wps_enqueue_style', 'tabs' );
		return '<div class="tab_widget wp_shortcodes_tabs">' . $tabnav . $tabcontent . '</div><div class="clear"></div>';
	}

}
add_filter( 'no_texturize_shortcodes', 'no_texturize_tabs' );

/**
 * Don't texturize Tabs.
 *
 * @param array $shortcodes Shortcodes.
 */
function no_texturize_tabs( $shortcodes ) {
	$shortcodes[] = 'tabs';
	return $shortcodes;
}

/**
 * Toggle Shortcode.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_toggle( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title' => __( 'Toggle Title', 'wp-shortcode' ),
			),
			$atts
		)
	);

	do_action( 'wps_enqueue_style', 'toggle' );
	return '<div class="toggle clearfix wp_shortcodes_toggle"><div class="wps_togglet"><span>' . wp_kses_post( $title ) . '</span></div><div class="togglec clearfix">' . do_shortcode( trim( $content ) ) . '</div></div><div class="clear"></div>';
}

/**
 * Simple Divider Shortcode.
 *
 * @param  array $atts    Attributes.
 */
function mts_divider( $atts ) {
		do_action( 'wps_enqueue_style' );
		return '<div class="divider"></div>';
}

/**
 * Divider with Anchor Link Shortcode.
 *
 * @param  array $atts    Attributes.
 */
function mts_divider_top( $atts ) {
		do_action( 'wps_enqueue_style' );
		return '<div class="top-of-page"><a href="#top">' . __( 'Back to Top', 'wp-shortcode' ) . '</a></div>';
}

/**
 * Clear Shortcode.
 *
 * @param  array $atts    Attributes.
 */
function mts_clear( $atts ) {
		do_action( 'wps_enqueue_style' );
		return '<div class="clear"></div>';
}


/**
 * Tooltip Shortcode.
 *
 * @param  array $atts    Attributes.
 * @param  mix   $content Content.
 */
function mts_tooltip( $atts, $content ) {
	$atts = shortcode_atts(
		array(
			'content' => '',
			'gravity' => 'n',
			'fade'    => '0',
		),
		$atts
	);

	do_action( 'wps_enqueue_style', 'tooltip' );
	return '<span class="wp_shortcodes_tooltip" title="' . esc_attr( $atts['content'] ) . '" data-gravity="' . esc_attr( $atts['gravity'] ) . '" data-fade="' . esc_attr( $atts['fade'] ) . '">' . $content . '</span>';
}

register_activation_hook( __FILE__, 'wp_shortcode_activation' );

if ( ! function_exists( 'wp_shortcode_activation' ) ) {
	/**
	 * Active the plugin.
	 */
	function wp_shortcode_activation() {
		/* Loads activation functions */
		update_option( 'wp_shortcode_activated', time() );
	}
}

add_action( 'admin_notices', 'wp_shortcode_admin_notice' );
/**
 * Display Notice
 */
function wp_shortcode_admin_notice() {
	global $current_user;
	$user_id = $current_user->ID;
	/**
	 * Check that the user hasn't already clicked to ignore the message
	 * Only show the notice 2 days after plugin activation
	 */
	if ( ! get_user_meta( $user_id, 'wp_shortcode_ignore_notice' ) && time() >= ( get_option( 'wp_shortcode_activated', 0 ) + ( 2 * 24 * 60 * 60 ) ) ) {
		echo '<div class="updated notice-info wp-shortcode-notice" id="wpshortcode-notice" style="position:relative;">';
		printf( __( '<p>Like WP Shortcode plugin? You will LOVE <a target="_blank" href="https://mythemeshop.com/plugins/wp-shortcode-pro/?utm_source=WP+Shortcode&utm_medium=Notification+Link&utm_content=WP+Shortcode+Pro+LP&utm_campaign=WordPressOrg"><strong>WP Shortcode Pro!</strong></a></p><a class="notice-dismiss mts-notice-dismiss" data-ignore="0" href="%1$s"></a>', 'wp-shortcode' ), '?wp_shortcode_admin_notice_ignore=0' );
		echo '</div>';
	}
	/**
	 * Other notice appears right after activating
	 * And it gets hidden after showing 3 times
	 */
	if ( ! get_user_meta( $user_id, 'wp_shortcode_ignore_notice_2' ) && get_option( 'wp_shortcode_notice_views', 0 ) < 3 && get_option( 'wp_shortcode_activated', 0 ) ) {
		$views = get_option( 'wp_shortcode_notice_views', 0 );
		update_option( 'wp_shortcode_notice_views', ( $views + 1 ) );
		echo '<div class="updated notice-info wp-shortcode-notice" id="wpshortcode-notice2" style="position:relative;">';
		echo '<p>';
		esc_attr_e( 'Thank you for trying WP Shortcode. We hope you will like it.', 'wp-shortcode' );
		echo '</p><a class="notice-dismiss mts-notice-dismiss" data-ignore="1" href="?wp_shortcode_admin_notice_ignore=0"></a>';
		echo '</div>';
	}
}

add_action(
	'wp_ajax_mts_dismiss_plugin_notice',
	function() {
		global $current_user;
		$user_id = $current_user->ID;
		/* If user clicks to ignore the notice, add that to their user meta */
		if ( isset( $_POST['dismiss'] ) ) {
			if ( '0' === $_POST['dismiss'] ) {
				add_user_meta( $user_id, 'wp_shortcode_ignore_notice', '1', true );
			} elseif ( '1' === $_POST['dismiss'] ) {
				add_user_meta( $user_id, 'wp_shortcode_ignore_notice_2', '1', true );
			}
		}
	}
);
