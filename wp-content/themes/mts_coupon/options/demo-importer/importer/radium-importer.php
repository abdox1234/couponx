<?php

/**
 * Class Radium_Theme_Importer
 *
 * This class provides the capability to import demo content as well as import widgets and WordPress menus
 *
 * @since 0.0.2
 *
 * @category RadiumFramework
 * @package  NewsCore WP
 * @author   Franklin M Gitonga
 * @link	 http://radiumthemes.com/
 *
 */

 // Exit if accessed directly
 if ( !defined( 'ABSPATH' ) ) exit;

 // Don't duplicate me!
 if ( !class_exists( 'Radium_Theme_Importer' ) ) {

	class Radium_Theme_Importer {

		public $demo_id;
		public $import_parts;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $theme_options_file;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $widgets;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $content_demo;

		/**
		 * Flag imported to prevent duplicates
		 *
		 * @since 0.0.3
		 *
		 * @var array
		 */
		//public $flag_as_imported = array( 'content' => false, 'menus' => false, 'options' => false, 'widgets' =>false );
		//public $flag_as_imported = array();

		/**
		 * imported sections to prevent duplicates
		 *
		 * @since 0.0.3
		 *
		 * @var array
		 */
		//public $imported_demos = array();

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Constructor. Hooks all interactions to initialize the class.
		 *
		 * @since 0.0.2
		 */
		public function __construct() {

			self::$instance = $this;

			$this->theme_options_file = $this->demo_files_path . $this->demo_id . '/' . $this->theme_options_file_name;
			$this->widgets			= $this->demo_files_path . $this->demo_id . '/' . $this->widgets_file_name;
			$this->content_demo	   = $this->demo_files_path . $this->demo_id . '/' . $this->content_demo_file_name;

			add_filter( 'add_post_metadata', array( $this, 'check_previous_meta' ), 10, 5 );

	  		$this->process();
		}
		 

		/**
		 * Avoids adding duplicate meta causing arrays in arrays from WP_importer
		 *
		 * @param null	$continue
		 * @param unknown $post_id
		 * @param unknown $meta_key
		 * @param unknown $meta_value
		 * @param unknown $unique
		 *
		 * @since 0.0.2
		 *
		 * @return
		 */
		public function check_previous_meta( $continue, $post_id, $meta_key, $meta_value, $unique ) {

			$old_value = get_metadata( 'post', $post_id, $meta_key );

			if ( count( $old_value ) == 1 ) {

				if ( $old_value[0] === $meta_value ) {

					return false;

				} elseif ( $old_value[0] !== $meta_value ) {

					update_post_meta( $post_id, $meta_key, $meta_value );
					return false;
				}
			}
		}

		public function process() {
			if ( 'mts_remove_demos' == $this->demo_id ) {
				$this->remove_imports();
			} else {
				$this->process_imports();
			}
		}

		/**
		 * Process all imports
		 *
		 * @params $content
		 * @params $options
		 * @params $options
		 * @params $widgets
		 *
		 * @since 0.0.3
		 *
		 * @return null
		 */
		public function process_imports() {

			$imported_demos = get_option( MTS_THEME_NAME.'_imported_demos', array() );

			if ( $this->import_parts['content'] && !empty( $this->content_demo ) && is_file( $this->content_demo ) ) {
				$this->set_demo_data( $this->content_demo );
				$imported_demos['content'] = $this->demo_id;
			}

			$demo_content_imported = get_option( MTS_THEME_NAME.'_demo_content_imported', false );
			if ( $demo_content_imported ) {
				add_filter( 'radium_theme_import_theme_options', array( $this, 'mts_correct_imported_options' ) );
				add_filter( 'radium_theme_import_widget_settings', array( $this, 'mts_correct_imported_widget' ), 10, 2 );
			}

			if ( $this->import_parts['options'] && !empty( $this->theme_options_file ) && is_file( $this->theme_options_file ) ) {
				$this->backup_options();
				$this->set_demo_theme_options( $this->theme_options_file );
				$imported_demos['options'] = $this->demo_id;
			}

			if ( $demo_content_imported ) {
				if ( $this->import_parts['menus'] ) {
					$this->set_demo_menus();
				}

				$this->set_external_options();

				if ( $this->import_parts['widgets'] && !empty( $this->widgets ) && is_file( $this->widgets ) ) {
					$this->backup_widgets();
					$this->process_widget_import_file( $this->widgets );
					$imported_demos['widgets'] = $this->demo_id;
				}
			}

			update_option( MTS_THEME_NAME.'_imported_demos', $imported_demos );

			do_action( 'radium_import_end');
		}

		/**
		 * add_widget_to_sidebar Import sidebars
		 * @param  string $sidebar_slug	Sidebar slug to add widget
		 * @param  string $widget_slug	 Widget slug
		 * @param  string $count_mod	   position in sidebar
		 * @param  array  $widget_settings widget settings
		 *
		 * @since 0.0.2
		 *
		 * @return null
		 */
		public function add_widget_to_sidebar($sidebar_slug, $widget_slug, $count_mod, $widget_settings = array()){

			$sidebars_widgets = get_option('sidebars_widgets');

			if(!isset($sidebars_widgets[$sidebar_slug]))
			   $sidebars_widgets[$sidebar_slug] = array('_multiwidget' => 1);

			$newWidget = get_option('widget_'.$widget_slug);

			if(!is_array($newWidget))
				$newWidget = array();

			$count = count($newWidget)+1+$count_mod;
			$sidebars_widgets[$sidebar_slug][] = $widget_slug.'-'.$count;

			$newWidget[$count] = $widget_settings;

			update_option('sidebars_widgets', $sidebars_widgets);
			update_option('widget_'.$widget_slug, $newWidget);
		}

		public function set_demo_data( $file ) {

			if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

			require_once ABSPATH . 'wp-admin/includes/import.php';

			$importer_error = false;

			if ( !class_exists( 'WP_Importer' ) ) {

				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ){

					require_once($class_wp_importer);

				} else {

					$importer_error = true;
				}
			}

			if ( !class_exists( 'WP_Import' ) ) {

				$class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';

				if ( file_exists( $class_wp_import ) )
					require_once($class_wp_import);
				else
					$importer_error = true;
			}

			if ( $importer_error ) {

				mts_chunk_output( __( 'Error on import.', 'coupon' ), 'h4' );
				die();

			} else {

				if(!is_file( $file )){

					//echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually ";
					mts_chunk_output(__( "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually.", 'coupon' ) );

				} else {

				   	$wp_import = new WP_Import();
				   	$wp_import->fetch_attachments = true;
				   	$wp_import->import( $file );
			 	}
			}

			do_action( 'radium_importer_after_theme_content_import');
		}

		public function set_demo_menus() { }

		public function set_external_options() { }

		public function set_demo_theme_options( $file ) {

			// File exists?
			if ( ! file_exists( $file ) ) {
				wp_die(
					__( 'Theme options Import file could not be found. Please try again.', 'coupon' ),
					'',
					array( 'back_link' => true )
				);
			}

			// Get file contents and decode
			$data = file_get_contents( $file );
			$data = trim( $data, '# ' );

			$data = $this->normalizeBreaks( $data );

			$data = $this->fix_serialized( $data );

			if ( $this->isSerialized( $data ) ) {
				$data = unserialize( $data );
				// Only if there is data
				if ( !empty( $data ) || is_array( $data ) ) {
				 	
				 	mts_chunk_output( __('Importing options...', 'coupon' ), 'h4' );
					// Hook before import
					$data = apply_filters( 'radium_theme_import_theme_options', $data );

					update_option( $this->theme_option_name, $data );

					mts_chunk_output( __('Options imported!', 'coupon' ), 'h4' );
					
				} else {

					mts_chunk_output( __( 'No Options to import.', 'coupon' ), 'h4' );
				}
			} else {
				mts_chunk_output( __( 'Something is wrong with options data. Please contact the support.', 'coupon' ), 'h4' );
			}
		}

		public function fix_str_length($matches) {
			$string = $matches[2];
			$right_length = strlen($string); // yes, strlen even for UTF-8 characters, PHP wants the mem size, not the char count
			return 's:' . $right_length . ':"' . $string . '";';
		}
		public function fix_serialized($string) {
			// securities
			if ( !preg_match('/^[aOs]:/', $string) ) return $string;
			if ( @unserialize($string) !== false ) return $string;
			$string = preg_replace("%\n%", "", $string);
			// doublequote exploding
			$data = preg_replace('%";%', "µµµ", $string);
			$tab = explode("µµµ", $data);
			$new_data = '';
			foreach ($tab as $line) {
			    $new_data .= preg_replace_callback('%\bs:(\d+):"(.*)%', array( $this, 'fix_str_length' ), $line);
			}
			return $new_data;
		}

		/**
		 * Check/Validate import code.
		 *
		 * http://php.net/manual/en/function.unserialize.php#85097
		 */
		public function isSerialized( $str ) {

			return ( $str == serialize( false ) || @unserialize( $str ) !== false );
		}

		public function normalizeBreaks($text, $breaktype = "\r\n") {

			return preg_replace('/(\r\n|\r|\n)/ms', $breaktype, $text);
			//return preg_replace('~\R~u', $breaktype, $text);
		}

		/**
		 * Available widgets
		 *
		 * Gather site's widgets into array with ID base, name, etc.
		 * Used by export and import functions.
		 *
		 * @since 0.0.2
		 *
		 * @global array $wp_registered_widget_updates
		 * @return array Widget information
		 */
		function available_widgets() {

			global $wp_registered_widget_controls;

			$widget_controls = $wp_registered_widget_controls;

			$available_widgets = array();

			foreach ( $widget_controls as $widget ) {

				if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

					$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
					$available_widgets[$widget['id_base']]['name'] = $widget['name'];
				}
			}

			return apply_filters( 'radium_theme_import_widget_available_widgets', $available_widgets );
		}


		/**
		 * Process import file
		 *
		 * This parses a file and triggers importation of its widgets.
		 *
		 * @since 0.0.2
		 *
		 * @param string $file Path to .wie file uploaded
		 * @global string $widget_import_results
		 */
		function process_widget_import_file( $file ) {

			$this->make_widgets_inactive();

			// File exists?
			if ( ! file_exists( $file ) ) {
				wp_die(
					__( 'Widget Import file could not be found. Please try again.', 'coupon' ),
					'',
					array( 'back_link' => true )
				);
			}

			// Get file contents and decode
			$data = file_get_contents( $file );
			$data = json_decode( $data );

			// Delete import file
			//unlink( $file );

			// Import the widget data
			// Make results available for display on import/export page
			$this->widget_import_results = $this->import_widgets( $data );
			mts_chunk_output( __( 'Importing widgets...', 'coupon' ), 'h4' );
			foreach ( $this->widget_import_results as $sidebar_key => $sidebar_data ) {
				mts_chunk_output( $sidebar_data['name'], 'strong' );
				foreach ( $sidebar_data['widgets'] as $widget_key => $widget_data ) {

					mts_chunk_output( $widget_data['name'].': '.$widget_data['title'].' - '.$widget_data['message']);
				}
			}
			mts_chunk_output( __( 'Widgets imported!', 'coupon' ), 'h4' );
		}


		/**
		 * Import widget JSON data
		 *
		 * @since 0.0.2
		 * @global array $wp_registered_sidebars
		 * @param object $data JSON widget data from .json file
		 * @return array Results array
		 */
		public function import_widgets( $data ) {

			// Make sure that footer and custom sidebars from options panel are registered, so dirty...
			do_action( 'widgets_init' );

			global $wp_registered_sidebars;

			// Have valid data?
			// If no data or could not decode
			if ( empty( $data ) || ! is_object( $data ) ) {
				return;
			}

			// Hook before import
			$data = apply_filters( 'radium_theme_import_widget_data', $data );

			// Get all available widgets site supports
			$available_widgets = $this->available_widgets();

			// Get all existing widget instances
			$widget_instances = array();
			foreach ( $available_widgets as $widget_data ) {
				$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
			}

			// Begin results
			$results = array();

			// Loop import data's sidebars
			foreach ( $data as $sidebar_id => $widgets ) {

				// Skip inactive widgets
				// (should not be in export file)
				if ( 'wp_inactive_widgets' == $sidebar_id ) {
					continue;
				}

				// Check if sidebar is available on this site
				// Otherwise add widgets to inactive, and say so
				if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
					$sidebar_available = true;
					$use_sidebar_id = $sidebar_id;
					$sidebar_message_type = 'success';
					$sidebar_message = '';
				} else {
					$sidebar_available = false;
					$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
					$sidebar_message_type = 'error';
					$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'coupon' );
				}

				// Result for sidebar
				$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
				$results[$sidebar_id]['message_type'] = $sidebar_message_type;
				$results[$sidebar_id]['message'] = $sidebar_message;
				$results[$sidebar_id]['widgets'] = array();

				// Loop widgets
				foreach ( $widgets as $widget_instance_id => $widget ) {

					$fail = false;

					// Get id_base (remove -# from end) and instance ID number
					$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
					$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

					// Does site support this widget?
					if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
						$fail = true;
						$widget_message_type = 'error';
						$widget_message = __( 'Site does not support widget', 'coupon' ); // explain why widget not imported
					}

					// Filter to modify settings before import
					// Do before identical check because changes may make it identical to end result (such as URL replacements)
					$widget = apply_filters( 'radium_theme_import_widget_settings', $widget, $widget_instance_id );

					// Does widget with identical settings already exist in same sidebar?
					if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

						// Get existing widgets in this sidebar
						$sidebars_widgets = get_option( 'sidebars_widgets' );
						$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

						// Loop widgets with ID base
						$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
						foreach ( $single_widget_instances as $check_id => $check_widget ) {

							// Is widget in same sidebar and has identical settings?
							if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

								$fail = true;
								$widget_message_type = 'warning';
								$widget_message = __( 'Widget already exists', 'coupon' ); // explain why widget not imported

								break;
							}
						}
					}

					// No failure
					if ( ! $fail ) {

						// Add widget instance
						$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
						$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
						$single_widget_instances[] = json_decode( json_encode( $widget ), true ); // add it

						// Get the key it was given
						end( $single_widget_instances );
						$new_instance_id_number = key( $single_widget_instances );

						// If key is 0, make it 1
						// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
						if ( '0' === strval( $new_instance_id_number ) ) {
							$new_instance_id_number = 1;
							$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
							unset( $single_widget_instances[0] );
						}

						// Move _multiwidget to end of array for uniformity
						if ( isset( $single_widget_instances['_multiwidget'] ) ) {
							$multiwidget = $single_widget_instances['_multiwidget'];
							unset( $single_widget_instances['_multiwidget'] );
							$single_widget_instances['_multiwidget'] = $multiwidget;
						}

						// Update option with new widget
						update_option( 'widget_' . $id_base, $single_widget_instances );

						// Assign widget instance to sidebar
						$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
						$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
						$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
						update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

						// Success message
						if ( $sidebar_available ) {
							$widget_message_type = 'success';
							$widget_message = __( 'Imported', 'coupon' );
						} else {
							$widget_message_type = 'warning';
							$widget_message = __( 'Imported to Inactive', 'coupon' );
						}
					}

					// Result for widget instance
					$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
					$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = property_exists( $widget, 'title' ) && !empty( $widget->title ) ? $widget->title : __( 'No Title', 'coupon' ); // show "No Title" if widget instance is untitled
					$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
					$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;
				}
			}

			// Hook after import
			do_action( 'radium_theme_import_widget_after_import' );

			// Return results
			return apply_filters( 'radium_theme_import_widget_results', $results );
		}

		/**
		 * Move all widgets to Inactive Widgets sidebar
		 */
		public function make_widgets_inactive() {

			global $wp_registered_sidebars;
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			$widgets_to_move  = array();

			mts_chunk_output( __( 'Deactivating current widgets...', 'coupon' ), 'h4' );

			if ( $sidebars_widgets && is_array( $sidebars_widgets ) && !empty( $sidebars_widgets ) ) {
				foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
					if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) && is_array( $widgets ) ) {
						$widgets_to_move = $widgets_to_move + $widgets;
						unset( $sidebars_widgets[ $sidebar_id ] );
					}
				}

				if ( !empty( $widgets_to_move ) ) {
					$wp_inactive_widgets = isset( $sidebars_widgets['wp_inactive_widgets'] ) ? $sidebars_widgets['wp_inactive_widgets'] : array();
					$sidebars_widgets['wp_inactive_widgets'] = $wp_inactive_widgets + $widgets_to_move;
					update_option( 'sidebars_widgets', $sidebars_widgets );
					mts_chunk_output( __( 'Widgets deactivated.', 'coupon' ), 'h4' );
				} else {
					mts_chunk_output( __( 'No widgets to deactivate.', 'coupon' ), 'h4' );
				}
			}
		}

		/**
		 * Backup all widgets before first import
		 */
		public function backup_widgets() {

			$imported_demos = get_option( MTS_THEME_NAME.'_imported_demos', array() );
			if ( !isset( $imported_demos['widgets'] ) ) {
				$sidebars_widgets = get_option( 'sidebars_widgets' );
				if ( $sidebars_widgets ) {
					update_option( MTS_THEME_NAME.'_import_sidebars_widgets_backup', $sidebars_widgets );
					mts_chunk_output( __( 'Created sidebars backup.', 'coupon' ), 'h4' );
				}
			}
		}

		/**
		 * Backup all options before first import
		 */
		public function backup_options() {

			$imported_demos = get_option( MTS_THEME_NAME.'_imported_demos', array() );
			if ( !isset( $imported_demos['options'] ) ) {
				$mts_options = get_option( MTS_THEME_NAME );
				if ( $mts_options ) {
					update_option( MTS_THEME_NAME.'_import_options_backup', $mts_options );
					mts_chunk_output( __( 'Created options backup.', 'coupon' ), 'h4' );
				}
			}
		}

		/**
		 * Restore all widgets stored before first import
		 */
		public function restore_widgets() {

			$sidebars = get_option( MTS_THEME_NAME.'_import_sidebars_widgets_backup' );
			if ( $sidebars ) {
				update_option( 'sidebars_widgets', $sidebars );
				mts_chunk_output( __( 'Sidebars restored.', 'coupon' ), 'h4' );
			}
		}

		/**
		 * Restore all options stored before first import
		 */
		public function restore_options() {

			$options = get_option( MTS_THEME_NAME.'_import_options_backup' );
			if ( $options ) {
				update_option( MTS_THEME_NAME, $options );
				mts_chunk_output( __( 'Options restored.', 'coupon' ), 'h4' );
			}
		}

		/**
		 * Remove all assigned menus from locations
		 */
		public function clear_menu_locations() {

			$menus = get_theme_mod( 'nav_menu_locations' );
			if ( $menus ) {
				$new_nav_menu_locations = array();
				foreach ( $menus as $location => $menu_id ) {
					$new_nav_menu_locations[ $location ] = '';
				}
				set_theme_mod( 'nav_menu_locations', $new_nav_menu_locations );
				mts_chunk_output( __( 'Menu Locations cleared.', 'coupon' ), 'h4' );
			}
		}

		/**
		 * Restore everything
		 */
		public function remove_imports() {

			if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

			require_once ABSPATH . 'wp-admin/includes/import.php';

			$importer_error = false;

			if ( !class_exists( 'WP_Importer' ) ) {

				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ){

					require_once($class_wp_importer);

				} else {

					$importer_error = true;
				}
			}

			if ( !class_exists( 'WP_Import' ) ) {

				$class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';

				if ( file_exists( $class_wp_import ) )
					require_once($class_wp_import);
				else
					$importer_error = true;
			}

			if ( $importer_error ) {

				//die("Error on import");
				mts_chunk_output( __( 'Error on import.', 'coupon' ), 'h4' );

			} else {
				$wp_import = new WP_Import();
				$max_execution_time = ini_get('max_execution_time');
				ini_set('max_execution_time', -1);
				$wp_import->remove_imported_content();
				ini_set('max_execution_time', $max_execution_time);
			}

			$this->restore_widgets();
			$this->restore_options();
			$this->clear_menu_locations();

			delete_option( MTS_THEME_NAME.'_import_sidebars_widgets_backup' );
			delete_option( MTS_THEME_NAME.'_import_options_backup' );
			delete_option( MTS_THEME_NAME.'_imported_terms' );
			delete_option( MTS_THEME_NAME.'_imported_posts' );
			delete_option( MTS_THEME_NAME.'_imported_images' );
			delete_option( MTS_THEME_NAME.'_imported_demos' );
			delete_option( MTS_THEME_NAME.'_demo_content_imported' );
		}

		/**
		 * Fix wrong cat, term, post, attachment, etc IDs and attachment URLs in options before import
		 */
		public function mts_correct_imported_options( $opt ) {

			$imported_terms_opt = get_option( MTS_THEME_NAME.'_imported_terms', array() );
			$imported_posts_opt = get_option( MTS_THEME_NAME.'_imported_posts', array() );
			$mts_imported_images = get_option( MTS_THEME_NAME.'_imported_images', array() );
			$fields_arr = get_option( MTS_THEME_NAME.'_fix_fields_after_import', array() );

			$data['fieds'] = $fields_arr;
			$data['terms'] = $imported_terms_opt;
			$data['posts'] = $imported_posts_opt;
			$data['image_urls'] = $mts_imported_images;

			array_walk( $opt, array( $this, 'mts_correct_single_import_option' ), $data );

			return $opt;
		}

		/**
		 * Fix wrong cat, term, post, attachment, etc IDs and attachment URLs in single option before import
		 */
		public function mts_correct_single_import_option( &$item, $key, $data ) {

			if ( ! array_key_exists( $key, $data['fieds'] ) ) {

				// If theme wants to fix some option type that is not in base theme
				$item = apply_filters( 'mts_correct_single_import_option', $item, $key, $data );

			} else {

				switch ( $data['fieds'][ $key ] ) {

					case 'cats_multi_select':

						$new_item = array();
						foreach ( $item as $term_id ) {

							if ( array_key_exists( $term_id, $data['terms']['category'] ) ) {

								$new_item[] = $data['terms']['category'][ $term_id ];

							} else {

								$new_item[] = $term_id;
							}
						}

						$item = $new_item;

					break;

					case 'tags_multi_select':

						$new_item = array();
						foreach ( $item as $term_id ) {

							if ( array_key_exists( $term_id, $data['terms']['post_tag'] ) ) {

								$new_item[] = $data['terms']['post_tag'][ $term_id ];

							} else {

								$new_item[] = $term_id;
							}
						}

						$item = $new_item;

					break;

					case 'cats_select':

						$new_item = $item;
						if ( is_numeric( $item ) ) {

							if ( array_key_exists( $item, $data['terms']['category'] ) ) {

								$new_item = $data['terms']['category'][ $item ];

							} else {

								$new_item = $item;
							}
						}

						$item = $new_item;

					break;

					case 'tags_select':

						$new_item = $item;
						if ( is_numeric( $item ) ) {

							if ( array_key_exists( $item, $data['terms']['post_tag'] ) ) {

								$new_item = $data['terms']['post_tag'][ $item ];

							} else {

								$new_item = $item;
							}
						}

						$item = $new_item;

					break;

					case 'pages_multi_select':
					case 'posts_multi_select':

						$new_item = array();
						foreach ( $item as $term_id ) {

							if ( array_key_exists( $term_id, $data['posts'] ) ) {

								$new_item[] = $data['posts'][ $term_id ];

							} else {

								$new_item[] = $term_id;
							}
						}

						$item = $new_item;

					break;

					case 'pages_select':
					case 'posts_select':

						if ( array_key_exists( $item, $data['posts'] ) ) {

							$new_item = $data['posts'][ $item ];

						} else {

							$new_item = $item;
						}

						$item = $new_item;

					break;

					case 'upload':

						if ( is_numeric( $item ) ) {

							if ( array_key_exists( $item, $data['posts'] ) ) {

								$new_item = $data['posts'][ $item ];

							} else {

								$new_item = $item;
							}

						} else {

							if ( array_key_exists( $item, $data['image_urls'] ) ) {

								$new_item = $data['image_urls'][ $item ];

							} else {

								$new_item = $item;
							}
						}
 
						$item = $new_item;

					break;

					default:

						$item = $item;

					break;
				}
			}
		}

		/**
		 * Fix wrong cat, term, post, attachment, etc IDs and attachment URLs in widget before import
		 */
		public function mts_correct_imported_widget( $widget, $widget_instance_id ) {

			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
			$widgets_to_fix = apply_filters(
				'mts_correct_imported_widgets',
				array(
					'single_category_posts_widget',
					'mts_post_slider_widget',
					'nav_menu',
				)
			);

			if ( !in_array( $id_base, $widgets_to_fix ) ) {

				return $widget;
			}

			$imported_terms_opt = get_option( MTS_THEME_NAME.'_imported_terms', array() );
			//$imported_posts_opt = get_option( MTS_THEME_NAME.'_imported_posts', array() );
			//$mts_imported_images = get_option( MTS_THEME_NAME.'_imported_images', array() );

			switch ( $id_base ) {

				case 'single_category_posts_widget':

					if ( !empty( $widget->cat ) && array_key_exists( $widget->cat, $imported_terms_opt['category'] ) ) {

						$widget->cat = $imported_terms_opt['category'][ $widget->cat ];
					}

				break;

				case 'mts_post_slider_widget':

					if ( !empty( $widget->cat ) ) {

						$new_cats = array();
						foreach ( $widget->cat as $cat ) {

							if ( array_key_exists( $cat, $imported_terms_opt['category'] ) ) {

								$new_cats[] = $imported_terms_opt['category'][ $cat ];
							}
						}
						$widget->cat = $new_cats;
					}

				break;

				case 'nav_menu':

					if ( !empty( $widget->nav_menu ) && array_key_exists( $widget->nav_menu, $imported_terms_opt['nav_menu'] ) ) {

						$widget->nav_menu = $imported_terms_opt['nav_menu'][ $widget->nav_menu ];
					}

				break;
				
				default:

					// If theme wants to fix some widget that is not in base theme
					$widget = apply_filters( 'mts_correct_imported_widget', $widget, $id_base, $widget_instance_id );

				break;
			}

			return $widget;
		}
		
	}//class

}//function_exists