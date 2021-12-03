<?php

$shortcodes = array(
	"button-brown" => array(
		"self-closing" => false,
		"atts" => array(
			"url"      => "#",
			"target"   => "_self",
			"position" => "left"
		),
		"label" => __( "Brown Button", "wp-shortcode" ),
		"content" => __( "Button text", "wp-shortcode" ),
		"description" => __( "Inserts a brown button. Set target to <strong>_blank</strong> to open link in a new window.", "wp-shortcode" )
	),
	"button-blue" => array(
			"self-closing" => false,
			"atts" => array(
					"url" => "#",
					"target" => "_self",
					"position" => "left"
			),
			"label" => __( "Blue Button", "wp-shortcode" ),
			"content" => __( "Button text", "wp-shortcode" ),
			"description" => __( "Inserts a blue button. Set target to <strong>_blank</strong> to open link in a new window.", "wp-shortcode" )
	),
	"button-green" => array(
			"self-closing" => false,
			"atts" => array(
					"url" => "#",
					"target" => "_self",
					"position" => "left"
			),
			"label" => __( "Green Button", "wp-shortcode" ),
			"content" => __( "Button text", "wp-shortcode" ),
			"description" => __( "Inserts a green button. Set target to <strong>_blank</strong> to open link in a new window.", "wp-shortcode" )
	),
	"button-yellow" => array(
			"self-closing" => false,
			"atts" => array(
					"url" => "#",
					"target" => "_self",
					"position" => "left"
			),
			"label" => __( "Yellow Button", "wp-shortcode" ),
			"content" => __( "Button text", "wp-shortcode" ),
			"description" => __( "Inserts a yellow button. Set target to <strong>_blank</strong> to open link in a new window.", "wp-shortcode" )
	),
	"button-red" => array(
			"self-closing" => false,
			"atts" => array(
					"url" => "#",
					"target" => "_self",
					"position" => "left"
			),
			"label" => __( "Red Button", "wp-shortcode" ),
			"content" => __( "Button text", "wp-shortcode" ),
			"description" => __( "Inserts a red button. Set target to <strong>_blank</strong> to open link in a new window.", "wp-shortcode" )
	),
	"button-white" => array(
			"self-closing" => false,
			"atts" => array(
					"url" => "#",
					"target" => "_self",
					"position" => "left"
			),
			"label" => __( "White Button", "wp-shortcode" ),
			"content" => __( "Button text", "wp-shortcode" ),
			"description" => __( "Inserts a white button. Set target to <strong>_blank</strong> to open link in a new window.", "wp-shortcode" )
	),
	"alert-note" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Alert Note", "wp-shortcode" ),
			"content" => __( "Note text", "wp-shortcode" ),
			"description" => __( "Display a note.", "wp-shortcode" )
	),
	"alert-announce" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Alert Announce", "wp-shortcode" ),
			"content" => __( "Announce text", "wp-shortcode" ),
			"description" => __( "Display an announcement.", "wp-shortcode" )
	),
	"alert-success" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Alert Success", "wp-shortcode" ),
			"content" => __( "Success text", "wp-shortcode" ),
			"description" => __( "Display a success message.", "wp-shortcode" )
	),
	"alert-warning" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Alert Warning", "wp-shortcode" ),
			"content" => __( "Warning text", "wp-shortcode" ),
			"description" => __( "Display warning or error message.", "wp-shortcode" )
	),
	"youtube" => array(
			"self-closing" => true,
			"atts" => array(
					"id" => "#",
					"width" => "600",
					"height" => "340",
					"position" => "left"
			),
			"label" => __( "YouTube Video", "wp-shortcode" ),
			"content" => "",
			"description" => __( "Embed a Youtube Video", "wp-shortcode" )
	),
	"vimeo" => array(
			"self-closing" => true,
			"atts" => array(
					"id" => "#",
					"width" => "600",
					"height" => "340",
					"position" => "left"
			),
			"label" => __( "Vimeo Video", "wp-shortcode" ),
			"content" => "",
			"description" => __( "Embed a Vimeo Video.", "wp-shortcode" )
	),
	"googlemap" => array(
			"self-closing" => true,
			"atts" => array(
					"address" => "Libertyville, Illinois, USA",
					"width" => "600",
					"height" => "340",
					"position" => "left"
			),
			"label" => __( "Google Map", "wp-shortcode" ),
			"content" => "",
			"description" => __( "Embed a Google Map. Insert address or GPS location.", "wp-shortcode" )
	),
	"toggle" => array(
			"self-closing" => false,
			"atts" => array(
					"title" => "Toggle Title"
			),
			"label" => __( "Toggle", "wp-shortcode" ),
			"content" => __( "Insert Content Here", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Content will be shown after clicking on the toggle title.", "wp-shortcode" )
	),
	"tabs" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Tabs", "wp-shortcode" ),
			"content" => __( "[tab title=&quot;Tab 1 Title&quot;]Insert tab 1 content here[/tab]\n[tab title=&quot;Tab 2 Title&quot;]Insert tab 2 content here[/tab]\n[tab title=&quot;Tab 3 Title&quot;]Insert tab 3 content here[/tab]", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Display content in tabbed form.", "wp-shortcode" )
	),
	"divider" => array(
			"self-closing" => true,
			"atts" => array(),
			"label" => __( "Divider", "wp-shortcode" ),
			"content" => "Divider",
			"description" => __( "Simple horizontal divider.", "wp-shortcode" )
	),
	"divider_top" => array(
			"self-closing" => true,
			"atts" => array(),
			"label" => __( "Divider with link", "wp-shortcode" ),
			"content" => "Divider with link",
			"description" => __( "Divider with an anchor link to top of page.", "wp-shortcode" )
	),
	"clear" => array(
			"self-closing" => true,
			"atts" => array(),
			"label" => __( "Clear", "wp-shortcode" ),
			"content" => "",
			"description" => __( "Clear shortcode can be used to clear an element of its neighbors, no floating elements are allowed on the left or the right side.", "wp-shortcode" )
	),

	// Column Shortcodes

	"one_third" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Third", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_third_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Third (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"two_third" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Two Third", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"two_third_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Two Third (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_half" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Half", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_half_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Half (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_fourth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Fourth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_fourth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Fourth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"three_fourth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Three Fourth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"three_fourth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Three Fourth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_fifth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Fifth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_fifth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Fifth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"two_fifth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Two Fifth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"two_fifth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Two Fifth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"three_fifth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Three Fifth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"three_fifth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Three Fifth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"four_fifth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Four Fifth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"four_fifth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Four Fifth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_sixth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Sixth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"one_sixth_last" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "One Sixth (Last)", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"five_sixth" => array(
			"self-closing" => false,
			"atts" => array(),
			"label" => __( "Five Sixth", "wp-shortcode" ),
			"content" => __( "Column content", "wp-shortcode" ),
			"content_field" => "textarea",
			"description" => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"five_sixth_last" => array(
		"self-closing"  => false,
		"atts"          => array(),
		"label"         => __( "Five Sixth (Last)", "wp-shortcode" ),
		"content"       => __( "Column content", "wp-shortcode" ),
		"content_field" => "textarea",
		"description"   => __( "Use column shortcodes in conjunction with their <em>(Last)</em> version, eg. <br /><strong>One Third + One Third + One Third (Last)</strong>", "wp-shortcode" )
	),
	"tooltip" => array(
		"self-closing" => false,
		"atts"         => array(
			"content" => "Tooltip content",
			"gravity" => "n",
			"fade"    => "0"
		),
		"label" => __( "Tooltip", "wp-shortcode" ),
		"content" => __( "Trigger text", "wp-shortcode" ),
		"description" => __( "Add a tooltip that appears on hover. Possible values for direction(Cardinal) of bubble: nw | n | ne | w | e | sw | s | se", "wp-shortcode" )
	)
);

$shortcodes = apply_filters( 'mts_wp_shortcode_list', $shortcodes );
echo "<script type=\"text/javascript\">var shortcodes = ".json_encode(  $shortcodes  ).";</script>";

?>
