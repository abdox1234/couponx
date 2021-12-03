<?php
defined('ABSPATH') or die;
global $url;

define('MTS_TYPOGRAPHY_DEFAULT_OPT', MTS_THEME_NAME.'_typography_default');
define('MTS_TYPOGRAPHY_COLLECTIONS_OPT', MTS_THEME_NAME.'_typography_collections');
$mts_typography_generate_previews = ( extension_loaded('gd') && function_exists('gd_info') && function_exists('imagettftext') );
define('MTS_TYPOGRAPHY_GENERATE_PREVIEWS', $mts_typography_generate_previews);

require_once('google-typography/google-typography.php');

if ( ! class_exists('NHP_Options') ){

	if(!defined('NHP_OPTIONS_DIR')){
		define('NHP_OPTIONS_DIR', trailingslashit(dirname(__FILE__)));
	}

	if(!defined('NHP_OPTIONS_URL')){
		if( file_exists(get_stylesheet_directory().'/options/options.php') ){
		define('NHP_OPTIONS_URL', trailingslashit(get_stylesheet_directory_uri()).'options/');
	}elseif( file_exists(get_template_directory() . '/options/options.php') ){
		define('NHP_OPTIONS_URL', trailingslashit(get_template_directory_uri()).'options/');
	}
}

class NHP_Options{

	public $framework_url = 'http://leemason.github.com/NHP-Theme-Options-Framework/';
	public $framework_version = '1.0.5';
	public $url = NHP_OPTIONS_URL;
	public $dir = NHP_OPTIONS_DIR;
	public $page = '';
	public $args = array();
	public $sections = array();
	public $extra_tabs = array();
	public $errors = array();
	public $warnings = array();
	public $options = array();

	/**
	 * Class Constructor. Defines the args for the theme options class
	 *
	 * @since NHP_Options 1.0
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function __construct($sections = array(), $args = array(), $extra_tabs = array()){

		$defaults = array();

		$defaults['opt_name'] = '';//must be defined by theme/plugin

		$defaults['menu_icon'] = NHP_OPTIONS_URL.'/img/menu_icon.png';
		$defaults['menu_title'] = __('Options', 'coupon' );
		$defaults['page_icon'] = 'icon-themes';
		$defaults['page_title'] = __('Options', 'coupon' );
		$defaults['page_slug'] = '_options';
		$defaults['page_cap'] = 'manage_options';
		$defaults['page_type'] = 'menu';
		$defaults['page_parent'] = '';
		$defaults['page_position'] = 100;

		$defaults['show_import_export'] = true;
		$defaults['show_typography'] = true;
		$defaults['show_translate'] = true;
		$defaults['show_child_theme_opts'] = true;
		$defaults['dev_mode'] = true;
		$defaults['stylesheet_override'] = false;

		$defaults['footer_credit'] = '';

		$defaults['help_tabs'] = array();
		$defaults['help_sidebar'] = '';

		//get args
		$this->args = wp_parse_args($args, $defaults);
		$this->args = apply_filters('nhp-opts-args', $this->args);
		$this->args = apply_filters('nhp-opts-args-'.$this->args['opt_name'], $this->args);

		//get sections
		$this->sections = apply_filters('nhp-opts-sections', $sections);
		$this->sections = apply_filters('nhp-opts-sections-'.$this->args['opt_name'], $this->sections);

		//get extra tabs
		$this->extra_tabs = apply_filters('nhp-opts-extra-tabs', $extra_tabs);
		$this->extra_tabs = apply_filters('nhp-opts-extra-tabs-'.$this->args['opt_name'], $this->extra_tabs);

		//set option with defaults
		add_action('init', array(&$this, '_set_default_options'));

		// Theme Options Update
		// Add new fields with default values
		add_action( 'admin_init', array($this, 'check_theme_options_version') );

		//options page
		add_action('admin_menu', array(&$this, '_options_page'));

		//register setting
		add_action('admin_init', array(&$this, '_register_setting'));

		//add the js for the error handling before the form
		add_action('nhp-opts-page-before-form', array(&$this, '_errors_js'), 1);

		//add the js for the warning handling before the form
		add_action('nhp-opts-page-before-form', array(&$this, '_warnings_js'), 2);

		//hook into the wp feeds for downloading the exported settings
		add_action('do_feed_nhpopts', array(&$this, '_download_options'), 1, 1);

		//child theme creation
		add_action('wp_ajax_mts_child_theme', array(&$this,'mts_child_theme'));
		add_action('wp_ajax_mts_list_child_themes', array(&$this,'mts_list_child_themes'));

		//get the options for use later on
		$this->options = get_option($this->args['opt_name']);

	}//function

	function check_theme_options_version() {
		$theme = wp_get_theme();
		$theme_version = $theme->parent() ? $theme->parent()->version : $theme->version;

		$options_version = get_option( $this->args['opt_name'].'_version', '0' );
		if (version_compare($theme_version, $options_version, '>')) {
			$this->update_theme_options($options_version, $theme_version);
			update_option( $this->args['opt_name'].'_version', $theme_version );
		}
	}

	/**
	 * Check for new theme options fields, add them with default values
	 *
	 */
	function update_theme_options( $from_version = 0, $to_version = 0 ) {
		$options = get_option( $this->args['opt_name'] );

		foreach ($this->sections as $k => $section) {
			if (isset($section['fields'])){
				foreach ($section['fields'] as $fieldk => $field) {
					$this->update_theme_options_field( $options, $field );
					if ( 'layout2' === $field['type'] ) {
						foreach ( $field['options'] as $opts ) {
							foreach ( $opts as $opt ) {
								if ( ! empty( $opt['subfields'] ) ) {
									foreach ( $opt['subfields'] as $subfield ) {
										$this->update_theme_options_field( $options, $subfield );
									}
								}
							}
						}
					}
				} // foreach fields
			}
		}// foreach sections
		update_option( $this->args['opt_name'], $options );
		$this->options = $options;
	}

	function update_theme_options_field( &$options, $field ) {
		if (!isset($options[$field['id']]) && isset($field['std'])) {
			$options[$field['id']] = $field['std'];
		}

		// Reset after specific version update
		/*if (isset($field['reset_at_version']) && version_compare($from_version, $field['reset_at_version'], '<')) {
			if (isset($field['std']))
				$options[$field['id']] = $field['std'];
			else
				$options[$field['id']] = '';
		}*/
	}

	/**
	 * ->get(); This is used to return and option value from the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function get($opt_name, $default = null){
		return (!empty($this->options[$opt_name])) ? $this->options[$opt_name] : $default;
	}//function

	/**
	 * ->set(); This is used to set an arbitrary option in the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param string $opt_name the name of the option being added
	 * @param mixed $value the value of the option being added
	 */
	function set($opt_name, $value) {
		$this->options[$opt_name] = $value;
		update_option($this->args['opt_name'], $this->options);
	}

	/**
	 * ->show(); This is used to echo and option value from the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function show($opt_name){
		$option = $this->get($opt_name);
		if(!is_array($option)){
			echo $option;
		}
	}//function



	/**
	 * Get default options into an array suitable for the settings API
	 *
	 * @since NHP_Options 1.0
	 *
	*/
	function _default_values(){

		$defaults = array();

		foreach($this->sections as $k => $section){

			if(isset($section['fields'])){

				foreach($section['fields'] as $fieldk => $field){

					if(!isset($field['std'])){$field['std'] = '';}

						$defaults[$field['id']] = $field['std'];

				}//foreach

			}//if

		}//foreach

		//fix for notice on first page load
		$defaults['last_tab'] = 0;

		return $defaults;

	}



	/**
	 * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
	 *
	 * @since NHP_Options 1.0
	 *
	*/
	function _set_default_options(){
		if(!get_option($this->args['opt_name'])){
			add_option($this->args['opt_name'], $this->_default_values());
		}
		$this->options = get_option($this->args['opt_name']);
	}//function


	/**
	 * Class Theme Options Page Function, creates main options page.
	 *
	 * @since NHP_Options 1.0
	*/
	function _options_page(){
		$this->page = add_theme_page(
						$this->args['page_title'],
						$this->args['menu_title'],
						$this->args['page_cap'],
						$this->args['page_slug'],
						array(&$this, '_options_page_html'),
						//$this->args['menu_icon'],
						$this->args['page_position']
					);

		add_action('admin_print_styles-'.$this->page, array(&$this, '_enqueue'));
		add_action('load-'.$this->page, array(&$this, '_load_page'));
	}//function


	/**
	 * enqueue styles/js for theme page
	 *
	 * @since NHP_Options 1.0
	*/
	function _enqueue(){

		wp_register_style(
				'nhp-opts-css',
				$this->url.'css/options.css',
				array(),
				MTS_THEME_VERSION,
				'all'
			);

		wp_register_style(
			'nhp-opts-jquery-ui-css',
			apply_filters('nhp-opts-ui-theme', $this->url.'css/aristo.css'),
			'',
			MTS_THEME_VERSION,
			'all'
		);

		wp_register_style(
			'font-awesome',
			get_template_directory_uri().'/css/font-awesome.min.css',
			'',
			MTS_THEME_VERSION,
			'all'
		);

		wp_register_style(
			'nhp-magnific-popup',
			get_template_directory_uri().'/css/magnific-popup.css',
			'',
			MTS_THEME_VERSION,
			'all'
		);

		if(false === $this->args['stylesheet_override']){
			wp_enqueue_style('nhp-magnific-popup');
			wp_enqueue_style('nhp-opts-css');
			wp_enqueue_style('font-awesome');
		}

		wp_register_script(
			'nhp-history-js',
			//$this->url.'js/history.js',
			get_template_directory_uri().'/js/history.js',
			array('jquery'),
			MTS_THEME_VERSION,
			true
		);

		wp_register_script(
			'nhp-magnific-popup-js',
			get_template_directory_uri().'/js/jquery.magnific-popup.min.js',
			array('jquery'),
			MTS_THEME_VERSION,
			true
		);

		wp_register_script(
			'nhp-opts-js',
			$this->url.'js/options.js',
			array('jquery', 'nhp-history-js', 'nhp-magnific-popup-js' ),
			MTS_THEME_VERSION,
			true
		);

		// pass $this->args['opt_name'] to js...
		wp_localize_script( 'nhp-opts-js', 'nhpopts', array(
			'opt_name' => $this->args['opt_name'],
			'reset_confirm' => __('Are you sure you want to reset ALL options to default?', 'coupon' ),
			'import_confirm' => __('Are you sure you want to import options? All current options will be lost.', 'coupon' ),
			'leave_page_confirm' => __('Settings have changed, you should save them! Are you sure you want to leave this page?', 'coupon' ),
			'child_theme_name_empty' => __('Please enter desired child theme name.', 'coupon' ),
			'import_done' => __('Importing proccess finished!', 'coupon' ),
			'import_fail' => __('Importing proccess failed! Please try again.', 'coupon' ),
			'remove_done' => __('Removal proccess finished!', 'coupon' ),
			'remove_fail' => __('Removal proccess failed! Please try again.', 'coupon' ),
			'reloading_page' => __('Reloading page...', 'coupon' ),
			'import_opt_confirm' => __('Are you sure you want to import demo options? All current options will be lost.', 'coupon' ),
			'import_widget_confirm' => __('Are you sure you want to import demo options and widgets? All current options will be lost and existing widgets deactivated.', 'coupon' ),
			'import_all_confirm' => __('Are you sure you want to import demo?', 'coupon' ),
			'remove_all_confirm' => __("Are you sure you want to remove demo options, content, menus and widgets? Please note that all modifications you've made to imported content will be lost", 'coupon' ),
		) );
		wp_enqueue_script('nhp-opts-js');

		do_action('nhp-opts-enqueue');

		do_action('nhp-opts-enqueue-'.$this->args['opt_name']);


		foreach($this->sections as $k => $section){

			if(isset($section['fields'])){

				foreach($section['fields'] as $fieldk => $field){

					if(isset($field['type'])){

						$field_class = 'NHP_Options_'.$field['type'];

						if(!class_exists($field_class)){
							require_once($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
						}//if

						if(class_exists($field_class) && method_exists($field_class, 'enqueue')){
							$enqueue = new $field_class(array(),'',$this);
							$enqueue->enqueue();
						}//if

					}//if type

				}//foreach

			}//if fields

		}//foreach


	}//function

	/**
	 * Download the options file, or display it
	 *
	 * @since NHP_Options 1.0.1
	*/
	function _download_options(){
		if(!isset($_GET['secret']) || $_GET['secret'] != md5(AUTH_KEY.SECURE_AUTH_KEY)){wp_die(__( 'Invalid Secret for options use', 'coupon' ));exit;}
		if(!isset($_GET['option'])){wp_die(__( 'No Option Defined', 'coupon' ));exit;}
		$backup_options = get_option($_GET['option']);
		$backup_options['nhp-opts-backup'] = '1';
		$content = '###'.serialize($backup_options).'###';


		if(isset($_GET['action']) && $_GET['action'] == 'download_options'){
			header('Content-Description: File Transfer');
			header('Content-type: application/txt');
			header('Content-Disposition: attachment; filename="'.$_GET['option'].'_options_'.date('d-m-Y').'.txt"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			echo $content;
			exit;
		}else{
			echo $content;
			exit;
		}
	}




	/**
	 * show page help
	 *
	 * @since NHP_Options 1.0
	*/
	function _load_page(){

		//do admin head action for this page
		add_action('admin_head', array(&$this, 'admin_head'));

		//do admin footer text hook
		add_filter('admin_footer_text', array(&$this, 'admin_footer_text'));

		$screen = get_current_screen();

		if(is_array($this->args['help_tabs'])){
			foreach($this->args['help_tabs'] as $tab){
				$screen->add_help_tab($tab);
			}//foreach
		}//if

		if($this->args['help_sidebar'] != ''){
			$screen->set_help_sidebar($this->args['help_sidebar']);
		}//if

		do_action('nhp-opts-load-page', $screen);

		do_action('nhp-opts-load-page-'.$this->args['opt_name'], $screen);

	}//function


	/**
	 * do action nhp-opts-admin-head for theme options page
	 *
	 * @since NHP_Options 1.0
	*/
	function admin_head(){

		do_action('nhp-opts-admin-head', $this);

		do_action('nhp-opts-admin-head-'.$this->args['opt_name'], $this);

	}


	function admin_footer_text($footer_text){
		return $this->args['footer_credit'];
	}//function




	/**
	 * Register Option for use
	 *
	 * @since NHP_Options 1.0
	*/
	function _register_setting(){

		register_setting($this->args['opt_name'].'_group', $this->args['opt_name'], array(&$this,'_validate_options'));

		foreach($this->sections as $k => $section){

			add_settings_section($k.'_section', $section['title'], array(&$this, '_section_desc'), $k.'_section_group');

			if(isset($section['fields'])){

				foreach($section['fields'] as $fieldk => $field){

					if(isset($field['title'])){

						$th = (isset($field['sub_desc'])) ? '<span class="field_title">'.$field['title'].'</span><span class="description">'.$field['sub_desc'].'</span>' : '<span class="field_title">'.$field['title'].'</span>';
					}else{
						$th = '';
					}

					add_settings_field($fieldk.'_field', $th, array(&$this,'_field_input'), $k.'_section_group', $k.'_section', $field); // checkbox

				}//foreach

			}//if(isset($section['fields'])){

		}//foreach

		do_action('nhp-opts-register-settings');

		do_action('nhp-opts-register-settings-'.$this->args['opt_name']);



	}//function



	/**
	 * Validate the Options options before insertion
	 *
	 * @since NHP_Options 1.0
	*/
	function _validate_options($plugin_options){

		// Google Fonts
		// stored in a separate option for backwards compatibility
		if (!empty($plugin_options['google_typography_collections'])) {
			// make sure not to save duplicates
			$collections = array();
			foreach ($plugin_options['google_typography_collections'] as $coll) {
				$selector = (!empty($coll['css_selectors']) ? $coll['css_selectors'] : '0');
				if (!isset($collections[$selector])){
					$collections[$selector] = $coll;
				}
			}
			$collections = array_values($collections);
			update_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT, $collections);
		}
		unset($plugin_options['google_typography_collections']);

		set_transient('nhp-opts-saved', '1', 1000 );

		if(!empty($plugin_options['import'])){

			if($plugin_options['import_code'] != ''){
				$import = $plugin_options['import_code'];
			}elseif($plugin_options['import_link'] != ''){
				$import = wp_remote_retrieve_body( wp_remote_get($plugin_options['import_link']) );
			}

			$imported_options = unserialize(trim($import,'# '));
			if(is_array($imported_options) && isset($imported_options['nhp-opts-backup']) && $imported_options['nhp-opts-backup'] == '1'){
				$imported_options['imported'] = 1;
				// Fonts
				// If no typography array provided, we don't touch the current setting
				if (!empty($imported_options['google_typography_collections'])) {
					update_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT, $imported_options['google_typography_collections']);
				}
				unset($imported_options['google_typography_collections']);

				return $imported_options;
			}


		}

		/**
		 * Set cron to remove expired coupons.
		 */
		if(isset($plugin_options['mts_remove_expire_coupons'])) {
			$mts_schedule_hook = 'mts_remove_exp_coupons_cron5';
			if($plugin_options['mts_remove_expire_coupons'] != $this->get('mts_remove_expire_coupons') || $plugin_options['mts_expire_coupon_frequency'] != $this->get('mts_expire_coupon_frequency')) {
				wp_clear_scheduled_hook($mts_schedule_hook);
			}
		}

		// reset settings
		if(!empty($plugin_options['defaults'])){
			$plugin_options = $this->_default_values();

			// Fonts
			delete_option(MTS_TYPOGRAPHY_DEFAULT_OPT);
			delete_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);

			return $plugin_options;
		}//if set defaults


		//validate fields (if needed)
		$plugin_options = $this->_validate_values($plugin_options, $this->options);

		if($this->errors){
			set_transient('nhp-opts-errors', $this->errors, 1000 );
		}//if errors

		if($this->warnings){
			set_transient('nhp-opts-warnings', $this->warnings, 1000 );
		}//if errors

		do_action('nhp-opts-options-validate', $plugin_options, $this->options);

		do_action('nhp-opts-options-validate-'.$this->args['opt_name'], $plugin_options, $this->options);

		// no need to store these
		unset($plugin_options['defaults']);
		unset($plugin_options['import']);
		unset($plugin_options['import_code']);
		unset($plugin_options['import_link']);

		return $plugin_options;

	}//function




	/**
	 * Validate values from options form (used in settings api validate function)
	 * calls the custom validation class for the field so authors can override with custom classes
	 *
	 * @since NHP_Options 1.0
	*/
	function _validate_values($plugin_options, $options){
		foreach($this->sections as $k => $section){

			if(isset($section['fields'])){

				foreach($section['fields'] as $fieldk => $field){
					$field['section_id'] = $k;

					if(isset($field['type']) && $field['type'] == 'multi_text'){continue;}//we cant validate this yet

					if(!isset($plugin_options[$field['id']]) || $plugin_options[$field['id']] == ''){
						continue;
					}

					//force validate of custom filed types

					if(isset($field['type']) && !isset($field['validate'])){
						if($field['type'] == 'color' || $field['type'] == 'color_gradient'){
							$field['validate'] = 'color';
						}elseif($field['type'] == 'date'){
							$field['validate'] = 'date';
						}
					}//if

					if(isset($field['validate'])){
						$validate = 'NHP_Validation_'.$field['validate'];

						if(!class_exists($validate)){
							require_once($this->dir.'validation/'.$field['validate'].'/validation_'.$field['validate'].'.php');
						}//if

						if(class_exists($validate)){
							$validation = new $validate($field, $plugin_options[$field['id']], $options[$field['id']]);
							$plugin_options[$field['id']] = $validation->value;
							if(isset($validation->error)){
								$this->errors[] = $validation->error;
							}
							if(isset($validation->warning)){
								$this->warnings[] = $validation->warning;
							}
							continue;
						}//if
					}//if


					if(isset($field['validate_callback']) && function_exists($field['validate_callback'])){

						$callbackvalues = call_user_func($field['validate_callback'], $field, $plugin_options[$field['id']], $options[$field['id']]);
						$plugin_options[$field['id']] = $callbackvalues['value'];
						if(isset($callbackvalues['error'])){
							$this->errors[] = $callbackvalues['error'];
						}//if
						if(isset($callbackvalues['warning'])){
							$this->warnings[] = $callbackvalues['warning'];
						}//if

					}//if


				}//foreach

			}//if(isset($section['fields'])){

		}//foreach
		return $plugin_options;
	}//function








	/**
	 * HTML OUTPUT.
	 *
	 * @since NHP_Options 1.0
	*/
	function _options_page_html(){
		$last_tab_value = isset($this->options['last_tab']) ? $this->options['last_tab'] : '';
		echo '<div class="wrap"><h2></h2>';
			echo (isset($this->args['intro_text']))?$this->args['intro_text']:'';

			do_action('nhp-opts-page-before-form');

			do_action('nhp-opts-page-before-form-'.$this->args['opt_name']);

			echo '<form method="post" action="options.php" enctype="multipart/form-data" id="nhp-opts-form-wrapper">';
				settings_fields($this->args['opt_name'].'_group');
				echo '<input type="hidden" id="last_tab" name="'.$this->args['opt_name'].'[last_tab]" value="'.$last_tab_value.'" />';

				echo '<div id="nhp-opts-header">';


				if ( ! MTS_THEME_WHITE_LABEL ) {
					echo '<a href="http://mythemeshop.com" id="optionpanellogo" class="logo" target="_blank"><img src="'.$this->url.'img/optionpanellogo.png" width="190" height="36" /></a>';
					echo '<span class="headtext">Welcome to your theme\'s mission control center.</span>';
				}
				if ( ! MTS_THEME_WHITE_LABEL ) {
					echo '<a href="https://community.mythemeshop.com/forum/7-theme-support/" class="docsupport" target="_blank"><i class="fa fa-medkit"></i> '.__('Support', 'coupon').'</a>';
				}
					echo '<div class="theme-options-search"><a href="#" class="theme-options-clear-search"><i class="dashicons dashicons-no"></i></a> <input type="text" id="search-theme-options" value="" placeholder="'.esc_attr__('Search Options', 'coupon').'"><a href="#" class="options-search-link"><i class="fa fa-search"></i><span> '.__('Search Options', 'coupon').'</span></a></div>';
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';

					if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-saved') == '1'){
						if(isset($this->options['imported']) && $this->options['imported'] == 1){
							echo '<div id="nhp-opts-imported"><strong>'.__('Settings Imported!', 'coupon' ).'</strong></div>';
						}else{
							echo '<div id="nhp-opts-save"><strong>'.__('Settings Saved!', 'coupon' ).'</strong></div>';
						}
						delete_transient('nhp-opts-saved');
					}
					echo '<div id="nhp-opts-save-warn"><strong>'.__('Settings have changed, you should save them!', 'coupon' ).'</strong></div>';
					echo '<div id="nhp-opts-field-errors"><strong><span></span> '.__('error(s) were found!', 'coupon' ).'</strong></div>';

					echo '<div id="nhp-opts-field-warnings"><strong><span></span> '.__('warning(s) were found!', 'coupon' ).'</strong></div>';

				echo '<div class="clear"></div><!--clearfix-->';

				echo '<div id="nhp-opts-sidebar">';
					echo '<ul id="nhp-opts-group-menu">';

						foreach($this->sections as $k => $section){
							// Accordion: tab 3-14 are homepage items
							if ($k == 5) {
								echo '<li id="accordion_section_group_li" class="nhp-opts-group-tab-link-li"><a href="javascript:void(0);" id="accordion_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="3" title="Homepage"><i class="fa fa-home"></i> <span class="section_title">Homepage</span></a></li>';
								echo '<div id="nhp-opts-homepage-accordion">';
							} else if($k == 22) {
								echo '<li id="affiliate_section_group_li" class="nhp-opts-group-tab-link-li"><a href="javascript:void(0);" id="affiliate_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="3" title="Affiliates"><i class="fa fa-cloud-download"></i> <span class="section_title">Affiliates</span></a></li>';
								echo '<div id="nhp-opts-affiliates-accordion">';
							}
							$icon = (!isset($section['icon']))?'<i class="fa fa-cogs"></i> ':'<i class="fa '.$section['icon'].'"></i> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'" title="'.$section['title'].'">'.$icon.'<span class="section_title">'.$section['title'].'</span></a>';
							echo '</li>';
							if ($k == 13 || $k == 25) {
								echo '</div>';
							}
						}

						/*foreach($this->sections as $k => $section){
							$icon = (!isset($section['icon']))?'<i class="fa fa-cogs"></i> ':'<i class="'.$section['icon'].'"></i> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'" title="'.$section['title'].'">'.$icon.'<span class="section_title">'.$section['title'].'</span></a>';
							echo '</li>';
						}*/



						do_action('nhp-opts-after-section-menu-items', $this);

						do_action('nhp-opts-after-section-menu-items-'.$this->args['opt_name'], $this);

						// Typography link
						if(true === $this->args['show_typography']){
							echo '<li id="typography_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="typography_default_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="typography_default"><i class="fa fa-text-width"></i> '.'<span class="section_title">'.__('Typography', 'coupon' ).'</span></a>';
							echo '</li>';

						}//if

						// Import/export link
						if(true === $this->args['show_import_export']){
							echo '<li id="import_export_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="import_export_default_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="import_export_default"><i class="fa fa-sign-in"></i> '.'<span class="section_title">'.__('Import / Export', 'coupon' ).'</span></a>';
							echo '</li>';

						}//if

						foreach($this->extra_tabs as $k => $section){
							$icon = (!isset($section['icon']))?'<i class="fa fa-cogs"></i> ':'<i class="'.$section['icon'].'"></i> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'" title="'.$section['title'].'">'.$icon.'<span class="section_title">'.$section['title'].'</span></a>';
							echo '</li>';
						}


						if(true === $this->args['dev_mode']){
							echo '<li id="dev_mode_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="dev_mode_default_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="dev_mode_default"><img src="'.$this->url.'img/glyphicons/glyphicons_195_circle_info.png" /> '.__('Dev Mode Info', 'coupon' ).'</a>';
							echo '</li>';
						}//if

					echo '</ul>';
				echo '</div>';

				echo '<div id="nhp-opts-main">';

					foreach($this->sections as $k => $section){
						echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab">';
							do_settings_sections($k.'_section_group');
						echo '</div>';
					}


					if(true === $this->args['show_import_export']){
						echo '<div id="import_export_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h2>'.__('Import / Export Options', 'coupon' ).'</h2>';

							// presets
							if (!empty($this->args['presets'])) {

								echo '<h4>'.__('Preset Options', 'coupon' ).'</h4>';
								echo '<div id="presets" class="nhp-opts-field-wrapper">';
								$nonce = wp_create_nonce( "mts_demo_importer" );
								$imported_demos  = get_option( MTS_THEME_NAME.'_imported_demos', array() );
								$demo_content_imported = get_option( MTS_THEME_NAME.'_demo_content_imported', false );
								foreach ( $this->args['presets'] as $key => $preset ) {
									echo '<div class="preset" data-nonce="'.$nonce.'" data-demo-id="'.esc_attr($key).'">';
										$imported_flag	= '<span class="demo_part_imported">'.__( '(imported)', 'coupon' ).'</span>';
										$options_imported = isset( $imported_demos['options'] ) && $key === $imported_demos['options'] ? ' '.$imported_flag : '';
										$content_imported = isset( $imported_demos['content'] ) && $key === $imported_demos['content'] ? ' '.$imported_flag : '';
										$widgets_imported = isset( $imported_demos['widgets'] ) && $key === $imported_demos['widgets'] ? ' '.$imported_flag : '';
										$all_imported	 = !empty($options_imported) && !empty($content_imported) && !empty($widgets_imported) ? ' '.$imported_flag : '';
										$disabled_button = ( $demo_content_imported ) ? ' disabled="disabled"' : '';
										if ( !empty( $all_imported ) ) {
											$disabled_button  = '';
										}

										echo '<h5>'.$preset['title'].$all_imported.'</h5>';
										if ( ! empty( $preset['demo'] ) ) echo '<a href="'.$preset['demo'].'" target="_blank" class="demo-link">'.__('View demo', 'coupon' ).'</a>';
										echo '<div class="preset-thumb-wrap">';
											if ( ! empty( $preset['demo'] ) ) echo '<a href="'.$preset['demo'].'" target="_blank" class="">';
												echo '<img src="'.$preset['thumbnail'].'" />';
											if ( ! empty( $preset['demo'] ) ) echo '</a>';
										echo '</div>';

										echo '<div class="preset-actions-wrap">';
											echo '<button class="button button-secondary import-demo-options-button"'.$disabled_button.'>'.__('Import Theme Options', 'coupon' ).'</button>';
											echo '<button class="button button-secondary import-demo-widgets-button"'.$disabled_button.'>'.__('Import Theme Options & Widgets', 'coupon' ).'</button>';
											if ( !empty( $all_imported ) ) {
												echo '<button class="button button-primary remove-demo-button" data-nonce="'.$nonce.'">'.__('Remove imported data', 'coupon' ).'</button>';
											} else {
												echo '<button class="button button-secondary import-demo-button"'.$disabled_button.'>'.__('Import Theme Options, Widgets & Content', 'coupon' ).'</button>';
											}
										echo '</div>';

									echo '</div>';
								}
								echo '</div>';

								// Overlay modal
								echo '<div id="importing-overlay" class="mfp-hide">';
									echo '<div id="importing-modal">';
										echo '<div id="importing-modal-header">';
											echo '<h2><span class="spinner is-active"></span>' . __( 'Processing, please wait...', 'coupon' ) . '</h2>';
										echo '</div>';
										echo '<div id="importing-modal-content">';
										echo '</div>';
										echo '<div id="importing-modal-footer">';
											echo '<span id="importing-modal-footer-info">'.__( 'Processing, please wait...', 'coupon' ).'</span>';
											echo '<button id="importing-modal-footer-button" class="button button-primary">'.__( 'Ok', 'coupon' ).'</button>';
										echo '</div>';
									echo '</div>';
								echo '</div>';

								echo '<div id="import_divide"></div>';
							}

							echo '<h4>'.__('Import Options', 'coupon' ).'</h4>';

							echo '<p><a href="#" id="nhp-opts-import-code-button" class="button-secondary">' . esc_html__( 'Import Code', 'coupon' ) . '</a></p>';

							echo '<div id="nhp-opts-import-code-wrapper">';

								echo '<div class="nhp-opts-section-desc">';

									echo '<p class="description" id="import-code-description">'.apply_filters('nhp-opts-import-file-description',__('Insert your backup code below and hit Import to restore your site options from a backup.', 'coupon' )).'</p>';

								echo '</div>';

								echo '<div class="nhp-opts-field-wrapper">';

									echo '<textarea id="import-code-value" name="'.$this->args['opt_name'].'[import_code]" class="large-text" rows="8"></textarea><br />';

									echo '<input type="submit" id="nhp-opts-import" name="'.$this->args['opt_name'].'[import]" class="button-primary" value="'.__('Import', 'coupon' ).'">';

								echo '</div>';

							echo '</div>';

							echo '<div id="import_divide"></div>';

							echo '<h4>'.__('Export Options', 'coupon' ).'</h4>';
							echo '<p><a href="#" id="nhp-opts-export-code-copy" class="button-secondary">'.__('Show Export Code', 'coupon' ).'</a></p>';
							echo '<div class="nhp-opts-section-desc">';
								//echo '<p class="description">'.apply_filters('nhp-opts-backup-description', __('Here, you can export your current theme options.  Keep this safe, as you can use it for backup in case of an emergency. You can also use this to restore settings on this site, or on any other site using this theme.  You can also copy the link to your settings, and duplicate it to another site, which is useful if you have a network of blogs that all need the same settings.', 'coupon' )).'</p>';
							echo '</div>';

								$backup_options = $this->options;
								// google typography
								$typography = get_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);
								if (!empty($typography)) {
									$backup_options['google_typography_collections'] = $typography;
								}

								$backup_options['nhp-opts-backup'] = '1';

								$encoded_options = '###'.serialize($backup_options).'###';
								echo '<div class="nhp-opts-field-wrapper">';
									echo '<textarea class="large-text" id="nhp-opts-export-code" rows="8">';print_r($encoded_options);echo '</textarea>';
								echo '</div>';
								echo '<input type="text" class="large-text" id="nhp-opts-export-link-value" value="'.add_query_arg(array('feed' => 'nhpopts', 'secret' => md5(AUTH_KEY.SECURE_AUTH_KEY), 'option' => $this->args['opt_name']), site_url()).'" />';

							echo '<div id="import_divide"></div>';

							echo '<h4>'.__('Import from CSV', 'coupon' ).'</h4>';
							?>
								<div class="csv-import-container">
									<p class="description">
										<strong><?php _e('CSV File Fields:', 'coupon'); ?></strong>
										<span>post_title, post_content, post_date, post_name, post_author, post_status, featured_image, mts_coupon_button_type, mts_coupon_code, mts_coupon_deal_URL, mts_coupon_expire, mts_coupon_extra_rewards, mts_coupon_featured_text, mts_coupon_people_used, tax-mts_coupon_categories, tax-mts_coupon_tag</span>
									</p>
									<div class="csv-import-choices">
										<table class="widefat coupons-csv-importers">
											<tbody>
												<tr class="alternate">
													<td class="cb"><input type="radio" id="coupon_csv_post_new" name="csv_import_choice" value="new" checked="checked"></td>
													<td><label for="coupon_csv_post_new"><?php _e('Rename item\'s name (slug) if item with name (slug) already exists', 'coupon'); ?></label></td>
												</tr>
												<tr>
													<td class="cb"><input type="radio" id="coupon_csv_post_update" name="csv_import_choice" value="update"></td>
													<td><label for="coupon_csv_post_update"><?php _e('Update old item\'s data if item with name (slug) already exists', 'coupon'); ?></label></td>
												</tr>
												<tr class="alternate">
													<td class="cb"><input type="radio" id="coupon_csv_post_ignore" name="csv_import_choice" value="ignore"></td>
													<td><label for="coupon_csv_post_ignore"><?php _e('Ignore item if item with name (slug) already exists', 'coupon'); ?></label></td>
												</tr>
											</tbody>
										</table>
									</div>
									<p class="csv-importer-wrapper">
										<input type="file" name="csv_file" id="csv_file" />
										<button class="button-primary" id="import-csv"><?php _e('Import from CSV', 'coupon'); ?></button>
									</p>

								</div>
							<?php
							echo '<div id="import_divide"></div>';

							// Child Theme
							if( true === $this->args['show_child_theme_opts'] && !is_child_theme() ) {

								echo '<h4>'.__('Child Theme', 'coupon' ).'</h4>';
								echo '<div class="nhp-opts-section-desc">';
									echo '<p class="description">'. __('To create child theme for current theme, enter desired name and hit "Create Child Theme" button', 'coupon' ).'</p>';
								echo '</div>';

								echo '<div class="nhp-opts-field-wrapper">';
									//echo '<label for="nhp-opts-child-name">'.__( 'Child theme name', 'coupon' ).'</label><br />';
									echo '<input type="text" id="nhp-opts-child-name" name="nhp-opts-child-name" value="" />';
									echo '<input type="submit" id="nhp-opts-child-button" name="nhp-opts-child-button" class="button-primary" value="'.__('Create Child Theme', 'coupon' ).'">';
								echo '</div>';

								echo '<div id="child-theme-list-wrap">';
									echo '<input type="hidden" id="mts_child_theme_nonce" value="' . wp_create_nonce( 'mts_child_theme' ) . '">';
									$this->mts_list_child_themes();
								echo '</div>';
							}


						echo '</div>';

					}



					if(true === $this->args['show_typography']){
						echo '<div id="typography_default_section_group'.'" class="nhp-opts-group-tab">';
							//echo '<h3>'.__('Theme Typography', 'coupon' ).'</h3>';

							$typography = new mtsGoogleTypography();
							$typography->options_ui();
						echo '</div>';
					}

					foreach($this->extra_tabs as $k => $tab){
						echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab">';
						echo '<h2>'.$tab['title'].'</h2>';
						echo '<div class="nhp-opts-section-desc">'.$tab['desc'].'</div>';
						call_user_func( $tab['callback'] );
						echo '</div>';
					}



					if(true === $this->args['dev_mode']){
						echo '<div id="dev_mode_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h2>'.__('Dev Mode Info', 'coupon' ).'</h2>';
							echo '<div class="nhp-opts-section-desc">';
							echo '<textarea class="large-text" rows="24">'.print_r($this, true).'</textarea>';
							echo '</div>';
						echo '</div>';
					}


					do_action('nhp-opts-after-section-items', $this);

					do_action('nhp-opts-after-section-items-'.$this->args['opt_name'], $this);

					echo '<div id="options-search-no-results">'.__('No options found, please refine your query.', 'coupon' ).'</div><!--search: no results-->';

					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';
				echo '<div class="clear"></div><!--clearfix-->';

				echo '<div id="nhp-opts-footer">';

					if(isset($this->args['share_icons'])){
						echo '<div id="nhp-opts-share">';
						foreach($this->args['share_icons'] as $link){
							echo '<a href="'.$link['link'].'" title="'.$link['title'].'" target="_blank"><i class="'.$link['img'].'"></i></a>';
						}
						echo '</div>';
					}


					if ( ! empty( $this->sections ) && count( $this->sections ) > 2 ) {
						echo '<input type="submit" name="'.$this->args['opt_name'].'[defaults]" value="'.__('Reset to Defaults', 'coupon' ).'" class="button-secondary" />';
						echo '<input type="submit" name="save" id="savechanges" value="'.__('Save Changes', 'coupon' ).'" class="button-primary" disabled="disabled" />';
					}
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';



			echo '</form>';

			// Floating buttons
			echo '<div id="nhp-opts-bottom"></div>';

			do_action('nhp-opts-page-after-form');

			do_action('nhp-opts-page-after-form-'.$this->args['opt_name']);

			echo '<div class="clear"></div><!--clearfix-->';
		echo '</div><!--wrap-->';

	}//function



	/**
	 * JS to display the errors on the page
	 *
	 * @since NHP_Options 1.0
	*/
	function _errors_js(){

		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-errors')){
				$errors = get_transient('nhp-opts-errors');
				$section_errors = array();
				foreach($errors as $error){
					$section_errors[$error['section_id']] = (isset($section_errors[$error['section_id']]))?$section_errors[$error['section_id']]:0;
					$section_errors[$error['section_id']]++;
				}


				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#nhp-opts-field-errors span").html("'.count($errors).'");';
						echo 'jQuery("#nhp-opts-field-errors").show();';

						foreach($section_errors as $sectionkey => $section_error){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-error\">'.$section_error.'</span>");';
						}

						foreach($errors as $error){
							echo 'jQuery("#'.$error['id'].'").addClass("nhp-opts-field-error");';
							echo 'jQuery("#'.$error['id'].'").closest("td").append("<span class=\"nhp-opts-th-error\">'.$error['msg'].'</span>");';
						}
					echo '});';
				echo '</script>';
				delete_transient('nhp-opts-errors');
			}

	}//function



	/**
	 * JS to display the warnings on the page
	 *
	 * @since NHP_Options 1.0.3
	*/
	function _warnings_js(){

		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-warnings')){
				$warnings = get_transient('nhp-opts-warnings');
				$section_warnings = array();
				foreach($warnings as $warning){
					$section_warnings[$warning['section_id']] = (isset($section_warnings[$warning['section_id']]))?$section_warnings[$warning['section_id']]:0;
					$section_warnings[$warning['section_id']]++;
				}


				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#nhp-opts-field-warnings span").html("'.count($warnings).'");';
						echo 'jQuery("#nhp-opts-field-warnings").show();';

						foreach($section_warnings as $sectionkey => $section_warning){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-warning\">'.$section_warning.'</span>");';
						}

						foreach($warnings as $warning){
							echo 'jQuery("#'.$warning['id'].'").addClass("nhp-opts-field-warning");';
							echo 'jQuery("#'.$warning['id'].'").closest("td").append("<span class=\"nhp-opts-th-warning\">'.$warning['msg'].'</span>");';
						}
					echo '});';
				echo '</script>';
				delete_transient('nhp-opts-warnings');
			}

	}//function



	/**
	 * Section HTML OUTPUT.
	 *
	 * @since NHP_Options 1.0
	*/
	function _section_desc($section){

		$id = rtrim($section['id'], '_section');

		if(isset($this->sections[$id]['desc']) && !empty($this->sections[$id]['desc'])) {
			echo '<div class="nhp-opts-section-desc">'.$this->sections[$id]['desc'].'</div>';
		}

	}//function




	/**
	 * Field HTML OUTPUT.
	 *
	 * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
	 *
	 * @since NHP_Options 1.0
	*/
	function _field_input($field, $group_id = '', $index = 0){


		if(isset($field['callback']) && function_exists($field['callback'])){
			$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
			do_action('nhp-opts-before-field', $field, $value);
			do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
			call_user_func($field['callback'], $field, $value);
			do_action('nhp-opts-after-field', $field, $value);
			do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
			return;
		}

		if(isset($field['type'])){

			$field_class = 'NHP_Options_'.$field['type'];

			if(class_exists($field_class)){
				require_once($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
			}//if

			if(class_exists($field_class)){
				$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
				if (!empty($group_id)) $value = (@isset($this->options[$group_id][$index][$field['id']]))?$this->options[$group_id][$index][$field['id']]:'';
				do_action('nhp-opts-before-field', $field, $value);
				do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
				$render = '';
				$render = new $field_class($field, $value, $this);
				$render->render();
				do_action('nhp-opts-after-field', $field, $value);
				do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
			}//if

		}//if $field['type']
		//if (!empty($group_id)) return $value;
	}//function

	/**
	 * Create child theme
	 */
	function mts_child_theme() {

		check_ajax_referer( 'mts_child_theme' );
		if ( ! current_user_can( 'install_themes' ) ) {
			die('0');
		}

		if ( is_child_theme() ) {

			return; // Return if this is child theme
		}

		$child_name = stripslashes($_POST['child_name']);

		if ( empty( $child_name ) ) {

			return;
		}

		$access_type = get_filesystem_method();
		if ( $access_type === 'direct' ) {

			/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
			$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );

			/* initialize the API */
			if ( ! WP_Filesystem( $creds ) ) {
				/* any problems and we exit */
				return false;
			}

			global $wp_filesystem;
			/* do our file manipulations below */
			$parent_theme = wp_get_theme();// current theme object
			$parent_stylesheet = $parent_theme->get_stylesheet();
			$parent_name	   = $parent_theme->name;
			$parent_dir_name   = $parent_theme->template;

			if ( !empty( $child_name ) ) {

				$child_theme_directory = trailingslashit( get_theme_root() ) . sanitize_file_name( strtolower( $child_name ) );

				if ( !$wp_filesystem->is_dir( $child_theme_directory ) ) {

					$wp_filesystem->mkdir( $child_theme_directory );
					$child_stylesheet = trailingslashit( $child_theme_directory ) . 'style.css';
					ob_start();
					require __DIR__ . '/child-theme/stylesheet.php';
					$child_stylesheet_contents = ob_get_clean();
					$wp_filesystem->put_contents( $child_stylesheet, $child_stylesheet_contents );

					$wp_filesystem->copy(
						__DIR__ . '/child-theme/functions.php',
						trailingslashit( $child_theme_directory ) . 'functions.php'
					);
					$wp_filesystem->copy(
						__DIR__ . '/child-theme/screenshot.png',
						trailingslashit( $child_theme_directory ) . 'screenshot.png'
					);
				}

			}

		} else {
			/* don't have direct write access. Prompt user with our notice */
			//add_action('admin_notice', array(&$this,'mts_filesystem_notice'));
		}
	}

	function mts_filesystem_notice() {
		echo '<div class="updated"><p>' . esc_html__( 'You do not have permission to do this.', 'coupon' ) . '</p></div>';
	}

	/**
	 * Get all child themes
	 */
	function mts_get_child_themes() {
		$child_themes = array();
		$current_theme = wp_get_theme();// current theme object
		$current_theme_dir_name   = $current_theme->template;

		$themes = wp_get_themes();// get all themes

		foreach ( $themes as $slug => $data ) { // check each theme

			if ( $data->template !== $data->stylesheet && $data->template === $current_theme_dir_name ) {

				$child_themes[ $slug ] = $data->name; // Generate array
			}
		}

		return $child_themes;
	}

	/**
	 * List all child themes
	 */
	function mts_list_child_themes() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'mts_child_theme' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}
		
		$child_themes_arr = $this->mts_get_child_themes();

		echo '<div class="nhp-opts-field-wrapper">';
		if ( !empty( $child_themes_arr ) ) {
			echo '<p class="description">' . __('Existing child themes for current theme:', 'coupon' ) . '</p>';
			echo '<ul class="child-theme-list">';
				foreach ( $child_themes_arr as $child_theme ) {
					echo '<li><mark>'.$child_theme.'</mark></li>';
				}
			echo '</ul>';
		} else {
			_e('No child themes found for current theme', 'coupon' );
		}
		echo '</div>';

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) die();
	}

}//class
}//if

/**
 * Ajax demo importer action
 */
add_action('wp_ajax_mts_install_demo', 'mts_install_demo');
if ( !function_exists('mts_install_demo') ) {
	function mts_install_demo() {
		check_ajax_referer( 'mts_demo_importer', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}

		require_once dirname( __FILE__ ) .'/demo-importer/flushed-ajax.php';
		die();
	}
}

	/**
	 * Ajax csv import
	 */
	function coupon_csv_import() {
		if( !empty( $_FILES['files'] ) && UPLOAD_ERR_OK === $_FILES['files']['error'] ) {
			$row = $total_import_count = $total_update_count = $total_ignore_count = 0;
			$post_key_data = $post_taxonomies_data = $post_meta_data = array();
			if (($handle = fopen($_FILES['files']['tmp_name'], "r")) !== FALSE) {
			   while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				   if( $row > 0 ) {
				   		$temp_data = array();
						$num = count($data);
						for ($c=0; $c < $num; $c++) {
							//String Conversation with UTF-8
							$data[$c] = coupon_string_conversion( $data[$c] );
							$pos = strpos( $post_key_data[$c], 'tax-' );
							if($pos !== false) {
								$taxonomy = strstr( $post_key_data[$c], '-' );
								$taxonomy = trim( $taxonomy, '-' );
								$post_taxonomies_data[$taxonomy] = $data[$c];
							} else {
								$pos = strpos( $post_key_data[$c], 'mts_coupon' );
								if( $pos !== false && !empty($data[$c])) {
									$post_meta = trim( $post_key_data[$c], '-' );
									$post_meta_data[$post_meta] = $data[$c];
								}
							}
							if ($pos === false) {
								switch ( $post_key_data[$c] ) {
									case 'post_status':
										$post_status = coupon_check_post_status( $data[$c] );
										$temp_data[$post_key_data[$c]] = $post_status;
										break;
									case 'post_author':
										$post_author = coupon_check_post_author( $data[$c] );
										$temp_data[$post_key_data[$c]] = $post_author;
										break;
									case 'post_date':
										$post_date = coupon_check_post_date( $data[$c] );
										$temp_data[$post_key_data[$c]] = $post_date;
										break;
									case 'featured_image':
										$post_image = coupon_check_post_image( $data[$c] );
										$temp_data[$post_key_data[$c]] = $post_image;
										break;
									default:
										$temp_data[$post_key_data[$c]] = $data[$c];
										break;
								}
							}
						}
						$temp_data['post_type'] = $post_type_name = 'coupons';
						/* Post Insert / Update / Ignore functionaly start */
						if( isset( $_POST['csv_import_choice'] ) ) {
							$current_post_data = $temp_data;
							if( isset( $temp_data['featured_image'] ) ) {
								unset( $temp_data['featured_image'] );
							}
							$current_post_id = 0;
							if( $_POST['csv_import_choice'] == 'new' ) {
								$current_post_id = wp_insert_post( $temp_data );
								$total_import_count++;
							} else if( $_POST['csv_import_choice'] == 'update' ) {
								$current_post_id = coupon_check_post_by_slug( $current_post_data['post_name'], $post_type_name );
								if( !empty( $current_post_id ) ) {
									$temp_data['ID'] = $current_post_id;
									wp_update_post( $temp_data );
									$total_update_count++;
								} else {
									$current_post_id = wp_insert_post( $temp_data );
									$total_import_count++;
								}
							} else if( $_POST['csv_import_choice'] == 'ignore' ) {
								$current_post_id = coupon_check_post_by_slug( $current_post_data['post_name'], $post_type_name );
								if( empty( $current_post_id ) ) {
									$current_post_id = wp_insert_post( $temp_data );
									$total_import_count++;
								} else {
									$current_post_id = '';
									$total_ignore_count++;
								}
							}
							if( !empty( $current_post_id ) ) {
								//set post featured image
								if( !empty( $current_post_data['featured_image'] ) ) {
									set_post_thumbnail( $current_post_id, $current_post_data['featured_image'] );
								}
								//set post terms
								if( !empty( $post_meta_data ) && is_array( $post_meta_data ) ) {
									foreach ( $post_meta_data as $post_meta_key => $post_meta ) {
										update_post_meta($current_post_id, $post_meta_key, $post_meta);
									}
								}
								 //set post terms
								if( !empty( $post_taxonomies_data ) && is_array( $post_taxonomies_data ) ) {
									foreach ( $post_taxonomies_data as $post_taxonomy_key => $post_terms ) {
										if( !empty( $post_terms ) ) {
											$post_terms = trim( $post_terms );
											$post_terms = explode(',', $post_terms);
											//set terms
											wp_set_object_terms( $current_post_id, $post_terms, $post_taxonomy_key );
										}
									}
								}

							}
						} /* Post Insert / Update / Ignore functionaly end */
					} else {
						$num = count($data);
						for ($c=0; $c < $num; $c++) {
							$post_key_data[$c] = $data[$c];
						}

					}
					$row++;
			   }
			}
		}
		die();
	}

	add_action('wp_ajax_coupon_csv_import', 'coupon_csv_import');

/**
 * Stream content
 */
function mts_chunk_output( $output = '', $tag = "p" ) {
	if ( !empty( $tag ) ) {
		echo '<'.$tag.'>'. $output . '</'.$tag.'>';
	} else {
		echo $output;
	}

	flush();
	ob_flush();
}

/**
 * Get options which might need to be modified after importing from demo ( options that are storing IDs - cats, tags, terms, attachments, pots, pages, etc )
 * Updated on each load
 */
add_action( 'nhp-opts-after-field', 'mts_fields_to_fix_after_import', 10, 2 );
function mts_fields_to_fix_after_import( $field, $value ) {

	$fields_arr = get_option( MTS_THEME_NAME.'_fix_fields_after_import', array() );

	if ( isset( $field['type'] ) ) {

		$field_types_to_store = array(
			'cats_multi_select',
			'cats_select',
			'tags_multi_select',
			'tags_select',
			'pages_multi_select',
			'pages_select',
			'posts_multi_select',
			'posts_select',
			'upload',
		);

		if ( in_array( $field['type'], $field_types_to_store ) ) {

			$fields_arr[ $field['id'] ] = $field['type'];
		}

		update_option( MTS_THEME_NAME.'_fix_fields_after_import', $fields_arr );
	}
}

/**
 * String Conversation with UTF-8
 * Version 1.1.0
 */
function coupon_string_conversion($string){
    if( !preg_match( '%(?:
       [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
       |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
       |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
       |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
       |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
       |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
       )+%xs', $string ) ) {

        return utf8_encode($string);
    } else {
        return $string;
    }
}
/**
 * Get all post status
 */
function coupon_get_all_status() {
    $all_status = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash');
    return $all_status;
}
/**
 * Check post status and return valid post status
 */
function coupon_check_post_status( $status = 'draft' ) {
    $all_status = coupon_get_all_status();
    if( !empty( $status ) && in_array( $status, $all_status ) ) {
        return $status;
    } else {
        return 'draft';
    }
}
/**
 * Check post author and return valid post author
 */
function coupon_check_post_author( $author ) {
    global $user_ID;
    if( !empty( $author ) && !intval( $author ) ) {
        $user = get_user_by( 'login', $author );
        return $user->ID;
    } else {
        return $user_ID;
    }
}
/**
 * Check post date and return valid post date
 */
function coupon_check_post_date( $post_date = '' ) {

    if( !empty( $post_date ) ) {
		$timestamp = strtotime( $post_date );
		if ( ! $timestamp ) {
			$timestamp = time();
		}
        $post_date = date( 'Y-m-d H:i:s', $timestamp );
	}
    return $post_date;
}
/**
 * Check post by post slug and return valid post id
 */
function coupon_check_post_by_slug( $post_slug, $post_type = 'any' ) {

    if( !empty( $post_slug ) ) {
        $args = array(
			'post_type'     => $post_type,
			'name'          => sanitize_title($post_slug),
			'posts_per_page'=> -1,
			'fields'        => 'ids'
		);
        $post_ids = get_posts( $args );
        return !empty( $post_ids ) && !empty( $post_ids['0'] ) ? $post_ids['0'] : 0;
    } else {
        return 0;
    }
}
/**
 * Check post image and return valid post image
 */
function coupon_check_post_image( $image ) {
    if( !empty( $image ) ) {
		global $wpdb;
		$query = $wpdb->prepare( "select post_id from {$wpdb->postmeta} where meta_key='_coupon_import_url' and meta_value = %s", $image );
		$attachment_id = intval($wpdb->get_var($query));
		if( ! $attachment_id && coupon_check_image_file( $image ) ) {
			if (!function_exists('media_handle_upload')) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			// Download file to temp location
			$tmp = download_url($image);
			// Set variables for storage, fix file filename for query strings.
			preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $image, $matches);
			$file_array['name'] = basename($matches[0]);
			$file_array['tmp_name'] = $tmp;
			// If error storing temporarily, unlink
			if (is_wp_error($tmp)) {
				@unlink($file_array['tmp_name']);
				$file_array['tmp_name'] = '';
				return false;
			}
			$desc = $file_array['name'];
			$attachment_id = media_handle_sideload(
				$file_array,
				'',
				$desc,
				array( 'meta_input' => array(
					'_coupon_import_url' => $image,
				) )
			);
			// If error storing permanently, unlink
			if (is_wp_error($attachment_id)) {
				@unlink($file_array['tmp_name']);
				return false;
			}
		}
		return $attachment_id;
    }
	return false;
}
function coupon_check_image_file( $file ) {
	$check = false;
	$filetype = wp_check_filetype( $file );
	$valid_exts = array( 'jpg', 'jpeg', 'gif', 'png', 'jpe' );
	if ( in_array( strtolower( $filetype['ext'] ), $valid_exts ) ) {
		$check = true;
	}
	return $check;
}
/**
 * Check comment status and return valid comment status
 */
function coupon_check_comment_status( $status = 'closed' ) {

    if( !empty( $status ) && in_array( $status, array( 'open', 'closed' ) ) ) {
        return $status;
    } else {
        return 'closed';
    }
}
/**
 * Check ping status and return valid ping status
 */
function coupon_check_ping_status( $status = 'closed' ) {

    if( !empty( $status ) && in_array( $status, array( 'open', 'closed' ) ) ) {
        return $status;
    } else {
        return 'closed';
    }
}
