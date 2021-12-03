<?php
// make sure to not include translations
$args['presets']['default'] = array(
	'title' => 'Default',
	'demo' => 'http://demo.mythemeshop.com/coupon/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/default/thumb.jpg',
	'menus' => array( 'primary' => 'Menu', 'mobile' => '' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 3 ),
);

$args['presets']['coupon-2'] = array(
	'title' => 'Demo 2',
	'demo' => 'http://demo.mythemeshop.com/coupon-2/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/coupon-2/thumb.jpg',
	'menus' => array( 'primary' => 'Menu', 'mobile' => '' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 3 ),
);

$args['presets']['coupon-3'] = array(
	'title' => 'Demo 3',
	'demo' => 'http://demo.mythemeshop.com/coupon-3/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/coupon-3/thumb.jpg',
	'menus' => array( 'primary' => 'Menu', 'mobile' => '' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 4 ),
);

$args['presets']['coupon-4'] = array(
	'title' => 'Demo 4',
	'demo' => 'http://demo.mythemeshop.com/coupon-4/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/coupon-4/thumb.jpg',
	'menus' => array( 'primary' => 'Menu', 'mobile' => '' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 3 ),
);

$args['presets']['coupon-5'] = array(
	'title' => 'Demo 5',
	'demo' => 'http://demo.mythemeshop.com/coupon-5/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/coupon-5/thumb.jpg',
	'menus' => array( 'primary' => 'Menu', 'mobile' => '' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 4 ),
);

global $mts_presets;
$mts_presets = $args['presets'];
