<?php
/**
 * Version 0.0.3
 *
 * This file is just an example you can copy it to your theme and modify it to fit your own needs.
 * Watch the paths though.
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( !class_exists( 'Radium_Theme_Demo_Data_Importer' ) ) {

	require_once( dirname( __FILE__ ) . '/importer/radium-importer.php' ); //load admin theme data importer

	class Radium_Theme_Demo_Data_Importer extends Radium_Theme_Importer {

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.1
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Set the key to be used to store theme options
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $theme_option_name	   = MTS_THEME_NAME;

		/**
		 * Set name of the theme options file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $theme_options_file_name = 'theme_options.txt';

		/**
		 * Set name of the widgets json file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $widgets_file_name	   = 'widgets.json';

		/**
		 * Set name of the content file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $content_demo_file_name  = 'content.xml';

		/**
		 * Holds a copy of the widget settings
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $widget_import_results;

		/**
		 * Constructor. Hooks all interactions to initialize the class.
		 *
		 * @since 0.0.1
		 */
		public function __construct( $demo_id, $import_parts ) {

			$this->demo_id = $demo_id;
			$this->import_parts = $import_parts;

			$this->demo_files_path = dirname(__FILE__) . '/demo-files/'; //can

			self::$instance = $this;
			parent::__construct();

		}

		/**
		 * Add menus - the menus listed here largely depend on the ones registered in the theme
		 *
		 * @since 0.0.1
		 */
		public function set_demo_menus(){

			global $mts_presets;
			$new_nav_menu_locations = array();

			if ( isset( $mts_presets[ $this->demo_id ]['menus'] ) ) {
				foreach ( $mts_presets[ $this->demo_id ]['menus'] as $location => $menu_name ) {
					if ( !empty( $menu_name ) ) {
						$menu_term = get_term_by('name', $menu_name, 'nav_menu');
						if ( $menu_term ) {
							$new_nav_menu_locations[ $location ] = $menu_term->term_id;
						}
					} else {
						$new_nav_menu_locations[ $location ] = '';
					}
				}
			}

			if ( !empty( $new_nav_menu_locations ) ) {
				set_theme_mod( 'nav_menu_locations', $new_nav_menu_locations );
				mts_chunk_output( __( 'Imported Menus Activated!', 'coupon' ), 'h4' );
			}
		}

		/**
		 * Update wp or other third party options
		 *
		 */
		public function set_external_options() {

			global $mts_presets;

			if ( isset( $mts_presets[ $this->demo_id ]['options'] ) ) {

				foreach ( $mts_presets[ $this->demo_id ]['options'] as $option => $value ) {

					if ( 'page_on_front' === $option ) {

						$imported_posts_opt = get_option( MTS_THEME_NAME.'_imported_posts', array() );
						if ( array_key_exists( $value, $imported_posts_opt ) ) {

							update_option( $option, $imported_posts_opt[ $value ] );
						}

					} else {

						update_option( $option, $value );
					}
				}
			}
		}
	}
}