<?php
/*
Based on: Google Typography
Plugin URI: http://projects.ericalli.com/google-typography/
Author: Eric Alli
Author URI: http://ericalli.com
*/

defined('ABSPATH') or die;

/**
 * GoogleTypography class
 *
 * @class GoogleTypography	The class that holds the entire Google Typography plugin
 */

class mtsGoogleTypography {

	/**
	 * @var $api_url	The google web font API URL
	 */
	protected $api_url = "https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCjae0lAeI-4JLvCgxJExjurC4whgoOigA";
	
	/**
	 * @var $fonts_url	The google web font URL
	 */
	protected $fonts_url = "//fonts.googleapis.com/css?family=";
	
	/**
	 * Constructor for the GoogleTypography class
	 *
	 * Sets up all the appropriate hooks and actions
	 * within the plugin.
	 *
	 * @uses register_uninstall_hook()
	 * @uses is_admin()
	 * @uses add_action()
	 *
	 */	
	function __construct() {
		register_activation_hook(__FILE__, array(&$this, 'get_all_fonts' ));
		
		if ( is_admin() ){
			//add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts'));
			add_action('wp_ajax_get_user_fonts',array(&$this,'ajax_get_user_fonts'));
			add_action('wp_ajax_save_user_fonts',array(&$this,'ajax_save_user_fonts'));
			add_action('wp_ajax_reset_user_fonts',array(&$this,'ajax_reset_user_fonts'));
			add_action('wp_ajax_get_google_fonts',array(&$this,'ajax_get_google_fonts'));
			add_action('wp_ajax_get_fonts',array(&$this,'ajax_get_fonts'));
			add_action('wp_ajax_get_google_font_variants',array(&$this,'ajax_get_google_font_variants'));

			if ( MTS_TYPOGRAPHY_GENERATE_PREVIEWS )
				add_action('admin_footer', array(&$this, 'generate_font_previews'));

		} else{
			add_action('wp_head',array(&$this,'build_frontend'));
		}
		
		$this->std_fonts = array(
			"Helvetica, Arial, sans-serif"							=> "Helvetica, Arial, sans-serif",
			"'Arial Black', Gadget, sans-serif"						=> "'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif"							=> "'Bookman Old Style', serif",
			"'Comic Sans MS', cursive"								=> "'Comic Sans MS', cursive",
			"Courier, monospace"									=> "Courier, monospace",
			"Garamond, serif"										=> "Garamond, serif",
			"Georgia, serif"										=> "Georgia, serif",
			"Impact, Charcoal, sans-serif"							=> "Impact, Charcoal, sans-serif",
			"'Lucida Console', Monaco, monospace"					=> "'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif"	=> "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif"					=> "'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif"					=> "'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif"  => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			"Tahoma,Geneva, sans-serif"								=> "Tahoma, Geneva, sans-serif",
			"'Times New Roman', Times,serif"						=> "'Times New Roman', Times, serif",
			"'Trebuchet MS', Helvetica, sans-serif"					=> "'Trebuchet MS', Helvetica, sans-serif",
			"Verdana, Geneva, sans-serif"							=> "Verdana, Geneva, sans-serif",	
		);
		$this->dir_path = get_template_directory().'/options/google-typography';
		$this->dir_url = get_template_directory_uri().'/options/google-typography';
	}
	public function has_preview_image($font) {
		$font_slug = preg_replace('/[^a-z0-9]/i', '', strtolower($font));
		$exists = file_exists($this->dir_path.'/images/fonts/png/'.$font_slug.'.png');
		if (!$exists) {
			$gfont = $this->multidimensional_search($this->get_all_fonts(), array('family' => $font));
			if (!$gfont || !is_array($gfont) || empty($gfont['subsets']) || !in_array('latin', $gfont['subsets'])) return false; // no preview needed

			$option = get_option('mts_typography_missing_previews', false);
			if (empty($option)) {
				$option = array();
			} elseif (is_string($option)) {
				$option = array($option);
			}

			if (!in_array($font, $option)) {
				$new = array_merge($option, array($font));
				update_option('mts_typography_missing_previews', $new);
			}
		}
		return $exists;
	}

	public static function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new mtsGoogleTypography();
		}

		return $instance;
	}
	
	/**
	 * Initialize admin menu
	 *
	 * @uses add_theme_page()
	 * @uses add_filter()
	 * @uses add_action()
	 *
	 */
	function admin_menu() {
		global $plugin_screen;
		
		$plugin_screen = add_theme_page( 'Theme Typography', 'Theme Typography', 'manage_options', 'typography', array(&$this, 'options_ui'));
		
		add_filter('plugin_action_links', array(&$this, 'plugin_link'), 10, 2);
		add_action('load-'.$plugin_screen, array(&$this, 'help_tab'));
	}
	
	function help_tab() {
		global $plugin_screen;
		
		$screen = get_current_screen();

		if ($screen->id != $plugin_screen)
			return;
		
		$adding_title				= __('Adding A Collection', 'coupon' );
		$adding_content				= '<p>'.__('To add a new font for use on your site. Click the "Add New" button on the top left of the page near the "Google Typography" title.', 'coupon' ).'</p>';
		$adding_content				.= '<p>'.__('Once added, a new font row will appear on the page below. Next you can continue to customize your font (more info in the "Customizing" help tab).', 'coupon' ).'</p>';
		//$adding_content			 .= '<p><a href="https://vimeo.com/67957799" target="_blank">'.__('Watch The Video Tutorial &rarr;', 'coupon' ).'</a></p>';
		$customizing_title 		= __('Customizing', 'coupon' );
		$customizing_content	= '<p>'.__('Customizing fonts is easy; after adding a new font row you can then customize the following font attributes:', 'coupon' ).'</p>';
		$customizing_content .= '<ul><li><b>'.__('Preview Text', 'coupon' ).'</b> - '.__('Used for live previewing your changes. This text does not appear anywhere on your website.', 'coupon' ).'</li><li><b>Preview Background Color</b> - Allows you to swap between light and dark backgrounds when previewing this font.</li><li><b>'.__('Font Family', 'coupon' ).'</b> - '.__('The font family to use for this font. Choose from a real-time list of all available Google Fonts.', 'coupon' ).'</li><li><b>'.__('Font Variant', 'coupon' ).'</b> - '.__('The variant to use for this font. Note: Each font has it\'s own variant options.', 'coupon' ).'</li><li><b>'.__('Font Size', 'coupon' ).'</b> - '.__('The size you would like this font to be.', 'coupon' ).'</li><li><b>'.__('Font Color', 'coupon' ).'</b> - '.__('The color you\'d like to use for this font.', 'coupon' ).'</li><li><b>'.__('CSS Selectors', 'coupon' ).'</b> - '.__('The HTML tags or CSS selectors you\'d like this font to apply to (more info in the "CSS Selectors" help tab). You can specify multiple selectors separated by comma\'s. Ex: h1, #some_id, .some_class', 'coupon' ).'</li></ul>';
		$selectors_title 			= __('CSS Selectors', 'coupon' );
		$selectors_content		= '<p>' . __('CSS Selectors are used to hook your font rows into your actual website. Once you\'ve added, customized, and defined CSS selectors for your fonts, Google Typography will automatically insert all the necessary CSS into your website.', 'coupon' ) . '</p>';
		$selectors_content		= '<p>' . __('Here are some examples of the selectors you can use:', 'coupon' ) . '</p>';
		$selectors_content	 .= '<ul><li><b>'.__('IDs', 'coupon' ).':</b> '.__('#selector', 'coupon' ).'</li><li><b>'.__('Classes:', 'coupon' ).':</b> '.__('.selector', 'coupon' ).'</li><li><b>'.__('HTML Tags', 'coupon' ).':</b> '.__('span', 'coupon' ).'</ul>';
		$selectors_content	 .= '<p><b>'.__('Example', 'coupon' ).':</b> '.__('#selector span.date', 'coupon' ).'</p>';
		
		$screen->add_help_tab(array(
			'id'	=> 'adding',
			'title'	=> $adding_title,
			'content'	=> $adding_content
		));
		
		$screen->add_help_tab(array(
			'id'	=> 'customizing',
			'title'	=> $customizing_title,
			'content'	=> $customizing_content
		));
		
		$screen->add_help_tab(array(
			'id'	=> 'selectors',
			'title'	=> $selectors_title,
			'content'	=> $selectors_content
		));

	}
	
	/**
	 * Initialize plugin options link
	 *
	 */
	function plugin_link($links, $file) {
		if ( $file == 'google-typography/google-typography.php' ) {
			$links['settings'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'themes.php?page=typography' ), __( 'Settings', 'coupon' ) );
		}
		return $links;
	}
	
	/**
	 * Build the frontend CSS to apply to wp_head()
	 *
	 * @uses get_option()
	 * @uses GoogleTypography::stringify_fonts()
	 *
	 */
	function build_frontend() {
		
		$collections = get_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);
		$mts_options = get_option(MTS_THEME_NAME);
		
		$import_fonts = array();
		$font_styles = '';
		$uses_google_fonts = false;
		
		if($collections) {
			$frontend = '';
			foreach($collections as $collection){
			
				if(isset($collection['css_selectors']) && $collection['css_selectors'] != "" && isset($collection['font_variant'])) {
					
					$collection['font_family'] = stripslashes($collection['font_family']);
					// is it a Google font?
					if (in_array($collection['font_family'], $this->std_fonts)) {
						$google = false;
					} else {
						$google = true;
						$uses_google_fonts = true;
					}
					
					if ($google) {
						array_push($import_fonts, array('font_family' => $collection['font_family'], 'font_variant' => $collection['font_variant']));
					}
					
					$font_styles .= $collection['css_selectors'] . ' { ';
					
					if ($google) {
						$font_styles .= "font-family: '" . $collection['font_family'] . "'" . (empty($collection['backup_font']) ? '' : ', '.$collection['backup_font']) .'; ';
					} else {
						// quotes not needed for standard fonts
						$font_styles .= 'font-family: ' . $collection['font_family'] . '; ';
					}
					
					$font_styles .= 'font-weight: ' . $collection['font_variant'] . '; ';
					$font_styles .= 'font-size: ' . $collection['font_size'] . '; ';
					$font_styles .= 'color: ' . $collection['font_color'] . ';';
					$font_styles .= (empty($collection['additional_css']) ? '' : $collection['additional_css']);
					$font_styles .= " }\n";
			
				}
			}
			
			if ($uses_google_fonts) {
				$subsets = (empty($mts_options['mts_typography_sets']) ? array('latin') : $mts_options['mts_typography_sets']);
				
				$frontend .= '<link href="' . $this->fonts_url . $this->stringify_fonts($import_fonts) .'&amp;subset='.join(',', $subsets).'" rel="stylesheet" type="text/css">';
			}
			
			$frontend .= "\n<style type=\"text/css\">\n";
			$frontend .= $font_styles;
			$frontend .= "</style>\n";
		
			echo $frontend;
		
		}
		
	}

	/**
	 * Concatenate fonts into a format that Google likes
	 *
	 * @uses array_map()
	 * @uses implode()
	 * @return String of fonts and their associated weights
	 *
	 */
	function stringify_fonts($array) {
		
		$array = array_map('unserialize', array_unique(array_map('serialize', $array)));
		
		$fonts = array();
		
		foreach($array as $font){
			$parts = '';
			
			$parts .= str_replace(" ", "+", $font['font_family']);
			if(isset($font['font_variant']) && $font['font_variant'] != '') {
				$parts .= ':' . $font['font_variant'];
			}
			
			$fonts[] = $parts;
		}
		
		return implode('|', $fonts);
	}
	
	/**
	 * Build the admin settings UI
	 *
	 * @uses GoogleTypography::get_fonts()
	 *
	 */
	function options_ui() {
		
		$std_fonts = $this->std_fonts;
		$mts_options = get_option(MTS_THEME_NAME);
		
		$title						= __('Theme Typography', 'coupon' );
		$loading					= __('Loading Your Collections', 'coupon' );
		$add_new					= __('Add New Collection', 'coupon' );
		$reset						= __('Reset Collections', 'coupon' );
		$preview_text   			= __('Type in some text to preview...', 'coupon' );
		$collection_title 			= __('New Collection', 'coupon' );
		//$this->preview_text = $preview_text; // to pass it to JS
		$preview_hint   			= __('Preview Background Color', 'coupon' );
		//$font_family_title  	= __('Font family...', 'coupon' );
		$std_font_family_title  	= __('Standard Fonts', 'coupon' );
		$google_font_family_title  	= __('Google Fonts', 'coupon' );
		$font_variant_title 		= __('Variant...', 'coupon' );
		$font_size_title 			= __('Size...', 'coupon' );
		$backup_font_title			= __('Fallback:', 'coupon' );
		$css_selectors_title		= __('CSS Selectors:', 'coupon' );
		$additional_css_title		= __('Additional CSS:', 'coupon' );
		$delete_button_text			= __('Delete', 'coupon' );
		$save_button_text			= __('Save', 'coupon' );
		
		$welcome_title 				= __('Theme Typography', 'coupon' );
		$welcome_subtitle			= __('Get started in 3 steps.', 'coupon' );//.'<a href="https://vimeo.com/67957799" target="_blank">'.__('Watch the video tutorial &#x2192;', 'coupon' ).'</a>';
		$step_1_title 				= __('1. Pick A Font', 'coupon' );
		$step_1_desc				= __('Choose from any of the 600+ Google Fonts.', 'coupon' );
		$step_2_title 				= __('2. Customize It', 'coupon' );
		$step_2_desc				= __('Pick a size, variant, color and more.', 'coupon' );
		$step_3_title 				= __('3. Attach It', 'coupon' );
		$step_3_desc				= __('Attach your font to any CSS selector(s).', 'coupon' );
		$year						= date("Y");
		$themename = MTS_THEME_NAME;
		
		$character_sets_title = __('Character sets', 'coupon' );
		$character_sets_desc = __('Choose the character sets you wish to include. Please note that not all sets are available for all fonts.', 'coupon' );
		
		if ( empty($mts_options['mts_typography_sets']) || ! is_array( $mts_options['mts_typography_sets'] ) )
			$mts_options['mts_typography_sets'] = array('latin'); // default

		$character_sets = array(
			'latin' 		=> __('Latin', 'coupon' ),
			'latin-ext' 	=> __('Latin Extended', 'coupon' ),
			'cyrillic' 		=> __('Cyrillic', 'coupon' ),
			'cyrillic-ext'	=> __('Cyrillic Extended', 'coupon' ),
			'greek'			=> __('Greek', 'coupon' ),
			'greek-ext'		=> __('Greek Extended', 'coupon' ),
			'vietnamese'	=> __('Vietnamese', 'coupon' ),
			'khmer'			=> __('Khmer', 'coupon' ),
			'devanagari'	=> __('Devanagari', 'coupon' ),
		);
		
		// following could be done with CSS
		$add_new = str_replace(' ', '&nbsp;', $add_new);
		$reset = str_replace(' ', '&nbsp;', $reset);
		
		$fonts = $this->get_all_fonts();
		$font_families = "";
		$std_font_families = "";
		$font_families_css = array();
		if (is_array($fonts)) {
			foreach ($fonts as $font) {
				$font_family = $font['family'];
				$font_class = preg_replace('/[^a-z0-9]/i', '', strtolower($font['family']));
				if ($this->has_preview_image($font_family)) {
					$font_families_css[] = $font_class;
					$font_class .= ' has-preview';
				}
				$font_families .= "<option value=\"$font_family\" data-google=\"true\" class=\"$font_class\">$font_family</option>";
			}
		}
		foreach ($std_fonts as $font_name => $font_value) {
		  $std_font_families .= "<option value=\"$font_value\">$font_name</option>";
		}
		
		$numbers = "";
		foreach (range(10, 120) as $number) {
			$numbers .= "<option value=\"{$number}px\">{$number}px</option>";
		}
		
		if(get_option(MTS_TYPOGRAPHY_DEFAULT_OPT)) {
			$reset_link = '<a href="javascript:;" class="add-new-h2 reset_collections">'.$reset.'</a>';
		} else { $reset_link = ''; }
		echo '<input type="hidden" id="mts_fonts_nonce" value="' . wp_create_nonce( 'mts_fonts' ) . '">';
		echo <<<EOT
			<h3>
				$title
				<a href="javascript:;" class="add-new-h2 new_collection">$add_new</a>
				$reset_link
			</h3>
			<div class="nhp-opts-section-desc">
				<p class="description">
					From here, you can control the fonts used on your site. 
					You can choose from 17 standard font sets, or from the
					<a href="http://www.google.com/fonts" target="_blank">Google Fonts Library</a> containing 600+ fonts.
				</p>
			</div>
			
			<div id="google_typography" class="wrap">
							
				<div class="loading">
					<span class="spin"></span>
					<!-- <h2>$loading</h2> -->
				</div>
				
				<div class="template">
				
					<div class="collection">
					
						<div class="font_preview" style="display: none;">
							<input type="text" class="preview_text" value="$preview_text" />
							<ul class="preview_color" title="$preview_hint">
								<li><a href="javascript:;" class="light"></a></li>
								<li><a href="javascript:;" class="dark"></a></li>
							</ul>
						</div>
						
						<div class="font_options">
							<div class="left_col">
								<div class="collection_title_wrapper">
									<a href="#" class="button button-secondary collection_preview"><i class="fa fa-eye" style="margin-right: 2px;"></i> Preview</a>
									<input type="text" class="collection_title" value="$collection_title" />
								</div>
								<input type="hidden" class="font_family" />
									
								<select class="font_variant">
									<option value="">$font_variant_title</option>
								</select>
								<select class="font_size mts-font-size">
									<option value="">$font_size_title</option>
									$numbers
								</select>
								<input type="text" value="#222222" class="font_color" />
								<a href="#" class="button button-secondary collection_toggle_moreoptions"><i class="fa fa-cog" style="margin-right: 2px;"></i> More</a>
								
								<div class="collection_moreoptions" style="display: none;">
									<label>$css_selectors_title <input type="text" placeholder="h1, h2 &gt; a, .title" class="css_selectors" /></label>
									<label>$additional_css_title <input type="text" placeholder="text-decoration: underline;" class="additional_css" /></label>
									<label>$backup_font_title <select class="backup_font">
										<option value="">Choose one</option>
										$std_font_families
									</select></label>
								</div>
							</div>
							
							<div class="right_col">
								<a href="javascript:;" class="delete_collection dashicons dashicons-dismiss"></a>
								
							</div>
							
							<div class="clear"></div>
							
						</div>
						<!-- Additional CSS -->
						<style type="text/css" class="additional-css"></style>
					</div>
					
				</div>
				
				<div class="collections"></div>
			</div>	
EOT;
			// settings
			$typography_settings = '<table class="form-table">
				<tbody>
				<tr>
					<th scope="row">
						<span class="field_title">'.$character_sets_title.'</span>
						<span class="description">'.$character_sets_desc.'</span>
					</th>
					<td>
						<fieldset>';
							foreach ($character_sets as $val => $name) {
								$typography_settings .= '<label for="mts_typography_sets_'.$val.'">
													<input type="checkbox" id="mts_typography_sets_'.$val.'" name="'.$themename.'[mts_typography_sets][]" regular-text value="'.$val.'" '.checked( in_array( $val, $mts_options['mts_typography_sets'] ), true, false ).'/> 
								'.$name.' ('.$val.')
								</label><br/>';
							}
						$typography_settings .= '</fieldset>
					</td>
				</tr>
				</tbody>
			</table>';
		echo $typography_settings;
	}
	
	/**
	 * Function for retrieving user font collections
	 *
	 *
	 * @uses get_option()
	 * @uses json_encode()
	 * @return JSON object with all user fonts
	 *
	 */
	function ajax_get_user_fonts() {

		check_ajax_referer( 'mts_fonts' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}
		
		$collections = get_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);
		
		$retrieved = $collections ? true : false;
		
		// associative array won't work
		if (is_array($collections)) {
			$collections = array_values($collections);
			foreach ($collections as $i => $coll) {
				if (empty($coll['variants'])) {
					$collections[$i]['variants'] = $this->get_google_font_variants($coll['font_family']);
				}
				$collections[$i]['stored'] = $this->get_font($coll['font_family']); // additional data for select2
				$collections[$i]['stored']['selected_variant'] = $coll['font_variant'] ? $coll['font_variant'] : 'normal';
			}
		} else {
			$collections = array();
		}
		$response = json_encode( array( 'success' => $retrieved, 'collections' => $collections ) );
		
		
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
		
	}

	function get_fonts($term = '', $page = 1, $fonts_per_page = 1, $current_selection = false) {
		$fonts = $this->get_all_fonts();
		if ($term) {
			$fonts_t = array();
			foreach ($fonts as $font) {
				if (stripos($font['family'], $term) !== false)
					$fonts_t[] = $font;
			}
			$fonts = $fonts_t;
		}

		$std_font_families = array();
		if ($page == 1) {
			// push std fonts
			
			$c = 0;
			foreach ($this->std_fonts as $font_name => $font_value) {
				$font_class = preg_replace('/[^a-z0-9-]/', '', str_replace(',', '-', strtolower($font_name)));
				$std_font_families[$c] = array(
					'id' => $font_value, 
					'text' => $font_name, 
					'css_class' => 'std_font '.$font_class, 
					'has_preview' => false,
					'googlefont' => false,
					'onpage' => 1 // standard fonts are always on p 1
				);
				if ($current_selection == $font_value) {
					$current = $std_font_families[$c];
				}
				$c++;
			}
			// search term filter
			if ($term) {
				$fonts_t = array();
				foreach ($std_font_families as $i => $font) {
					//echo "stripos({$font[text]}, $term) = ".(stripos($font['text'], $term) ? 'yes ' : 'no ');
					if (stripos($font['text'], stripslashes($term)) !== false) {
						$fonts_t[] = $font;
					}
				}
				$std_font_families = $fonts_t;

			}
		}
		$fonts_count = count($fonts);
		$max_page = ceil($fonts_count / $fonts_per_page);
		if ($page > $max_page) $page = $max_page;
		if ($page < 1) $page = 1;

		if (empty($current)) {
			$font_t = $this->multidimensional_search($fonts, array('family' => $current_selection));
			if ($font_t) {
				$current = $font_t;
				$font_class = preg_replace('/[^a-z0-9]/i', '', strtolower($current['family']));
				if ($this->has_preview_image($current['family'])) {
					$current['has_preview'] = true;
					$current['preview_url'] = $this->dir_url.'/images/fonts/png/'.$font_class.'.png';
				} else {
					$current['has_preview'] = false;
				}
				$current['css_class'] =  $font_class;
				$current['text'] =  $current['family'];
				$current['id'] =  $current['family'];
				$current['googlefont'] = true;
				$current['onpage'] =  $page;
			}
		}

		$fonts = array_slice($fonts, ($page-1) * $fonts_per_page, $fonts_per_page);
		foreach ($fonts as $i => $font) {
			$font_class = preg_replace('/[^a-z0-9]/i', '', strtolower($font['family']));
			if ($this->has_preview_image($font['family'])) {
				$fonts[$i]['has_preview'] = true;
				$fonts[$i]['preview_url'] = $this->dir_url.'/images/fonts/png/'.$font_class.'.png';
			} else {
				$fonts[$i]['has_preview'] = false;
			}
			$fonts[$i]['css_class'] =  $font_class;
			$fonts[$i]['text'] =  $font['family'];
			$fonts[$i]['id'] =  $font['family'];
			$fonts[$i]['googlefont'] =  true;
			$fonts[$i]['onpage'] =  $page;
		}
		if ($page == 1) {
			$fonts_t = array_merge($std_font_families, $fonts);
			$fonts = $fonts_t;
			// if current font is not on page 1, put it on top to show
			if (!$term && !empty($current) && $current['googlefont'] && $fonts_per_page != 1  && !$this->multidimensional_search($fonts, array('family' => $current_selection))) {
				$fonts = array($current) + $fonts;
			}
		}
		return array('collections' => $fonts, 'max_page' => $max_page);
	}
	function get_font($font) {
		$font_arr = $this->get_fonts($font);
		return $font_arr['collections'][0];
	}
	function ajax_get_fonts() {
		check_ajax_referer( 'mts_fonts' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}
		$term = (!empty($_GET['term']) ? $_GET['term'] : false);
		$current_selection = (!empty($_GET['current']) ? $_GET['current'] : false);
		$page = (!empty($_GET['page']) ? abs($_GET['page']) : 1);
		$fonts_per_page = (!empty($_GET['page_limit']) ? abs($_GET['page_limit']) : 10);
		$fonts = $this->get_fonts($term, $page, $fonts_per_page, $current_selection);
		echo json_encode(array('collections' => $fonts['collections'], 'more' => ($page < $fonts['max_page'])));
		exit;
	}
	
	/**
	 * Function for saving user font collections
	 *
	 *
	 * @uses update_option()
	 * @uses json_encode()
	 * @return JSON object with all user fonts
	 *
	 */
	function ajax_save_user_fonts() {

		check_ajax_referer( 'mts_fonts' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}
		
		$collections = $_REQUEST['collections'];
		
		$collections = update_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT, $collections);
		
		$response = json_encode( array( 'success' => true, 'collections' => $collections ) );
		
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
		
	}
	
	/**
	 * Function for resetting user font collections
	 *
	 *
	 * @uses delete_option()
	 * @uses json_encode()
	 * @return JSON object with all user fonts
	 *
	 */
	function ajax_reset_user_fonts() {

		check_ajax_referer( 'mts_fonts' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}
		
		delete_option(MTS_TYPOGRAPHY_DEFAULT_OPT);
		delete_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);
		
		$response = json_encode( array( 'success' => true ) );
		
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
		
	}
	
	/**
	 * AJAX function for retrieving fonts from Google
	 *
	 *
	 * @uses GoogleTypography::multidimensional_search()
	 * @uses header()
	 * @return JSON object with font data
	 *
	 */
	function ajax_get_google_fonts() {

		check_ajax_referer( 'mts_fonts' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		}

		$fonts = $this->get_all_fonts();
		
		header("Content-Type: application/json");
		echo json_encode($fonts);
		
		exit;
	}
	
	/**
	 * AJAX function for retrieving font variants
	 *
	 *
	 * @uses GoogleTypography::multidimensional_search()
	 * @uses header()
	 * @return JSON object with font data
	 *
	 */
	function ajax_get_google_font_variants() {

		check_ajax_referer( 'mts_fonts' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die('0');
		} 
		
		$font_family = $_GET['font_family'];

		$result = $this->get_google_font_variants($font_family);
		header("Content-Type: application/json");
		echo json_encode($result);
		
		exit;
	}
	function get_google_font_variants($font_family) { 
		$fonts = $this->get_all_fonts();
		$font = $this->multidimensional_search($fonts, array('family' => $font_family));
		if (empty($font))
			$font = array('variants' => array('normal', '700'));
		return $font['variants'];
	}

	/**
	 * @var array Display attributes for the preview image and font
	 */
	var $preview_attributes = array(
		'font_size' => 28,
		'left_margin' => 3,
		'width' => 400,
		'height' => 64,
		'background_color' => array( 255, 255, 255 ),
		'font_color' => array( 0, 0, 0 ),
	);

	/**
	 * Create PNG of font name written with font TTF.
	 */
	public function generate_image($font) {
		$font_slug = preg_replace('/[^a-z0-9]/i', '', strtolower($font));
		if (file_exists($this->dir_path.'/images/fonts/png/'.$font_slug.'.png')) return;

		$width = $height = $font_size = $left_margin = $background_color = $font_color = false;
		extract( $this->preview_attributes, EXTR_IF_EXISTS );
		
		// Text Mask
		$mask = imageCreate($width, $height);

		$background = imageColorAllocate($mask, $background_color[0], $background_color[1], $background_color[2]);
		$foreground = imageColorAllocate($mask, $font_color[0], $font_color[1], $font_color[2]);

		$fonts = $this->get_all_fonts();
		$font = $this->multidimensional_search($fonts, array('family' => $font));

		$ttf_path = $this->maybe_get_remote_ttf($font);
		if ( !$ttf_path || !file_exists( $ttf_path ) ) {
			error_log( 'MyThemeShop/Typography: Could not load $ttf_path: ' . $ttf_path );
			return;
		}

		// Text
		$y = $this->get_centered_y_coordinate( $font_size, $ttf_path, $font['family'] );
		imagettftext($mask, $font_size, 0, $left_margin, $y, $foreground, $ttf_path, $font['family']);

		// White fill
		$white = imageCreate($width, $height);
		$background = imageColorAllocate($white, $background_color[0], $background_color[1], $background_color[2]);

		// Image
		$image = imagecreatetruecolor($width, $height);
		imagesavealpha( $image, true );
		imagefill( $image, 0, 0, imagecolorallocatealpha( $image, 0, 0, 0, 127 ) );

		// Apply Mask to Image
		for( $x = 0; $x < $width; $x++ ) {
	  for( $y = 0; $y < $height; $y++ ) {
		$alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
		$alpha = 127 - floor( $alpha[ 'red' ] / 2 );
		$color = imagecolorsforindex( $white, imagecolorat( $white, $x, $y ) );
		imagesetpixel( $image, $x, $y, imagecolorallocatealpha( $image, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
	  }
	}

		ob_start();
		imagePNG($image);
		$image = ob_get_clean();

		$this->save_image( $image, $font );

		// header("Content-type: image/png");
		// echo $image;
	}

	/**
	 * Save preview image file.
	 */
	public function save_image( $image, $font ) {
		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem; WP_Filesystem();

		$dir = $this->dir_path.'/images/fonts/png';

		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			error_log( "MyThemeShop/Typography: Please check permissions. Could not create directory $dir" );
			return;
		}

		$font_slug = preg_replace('/[^a-z0-9]/i', '', strtolower($font['family']));
		$image_file = $wp_filesystem->put_contents( $this->dir_path.'/images/fonts/png/'.$font_slug.'.png', $image, FS_CHMOD_FILE ); // predefined mode settings for WP files

		if ( !$image_file ) {
			error_log( "MyThemeShop/Typography: Please check permissions. Could not write image to $dir" );
			return;
		}
	}

	/**
	 * Calculate y-coordinate for centering text vertically.
	 * 
	 * @link http://stackoverflow.com/a/15001168
	 * @return int  y-coordinate
	 */
	public function get_centered_y_coordinate( $fontsize, $font, $text ) {
		$dims = imagettfbbox($fontsize, 0, $font, $text);

		$ascent = abs($dims[7]);
		$descent = abs($dims[1]);

		// $width = abs( $dims[0] ) + abs( $dims[2] );

		$height = $ascent + $descent;
		$image_height = $this->preview_attributes['height'];

		$y = ( ( $image_height/2 ) - ( $height/2 ) ) + $ascent;

		return $y;
	}

	function generate_font_previews() {
		// foreach missing images option: generate image
		// delete missing images option
		$option = get_option('mts_typography_missing_previews', false);
		if (empty($option)) {
			$option = array();
		} elseif (is_string($option)) {
			$option = array($option);
		}
		$c = 0;
		foreach ($option as $i => $font) {
			if ($c >= 3) return true; // stop after 3 loops
			$this->generate_image($font);
			update_option('mts_typography_missing_previews', array_diff($option, array($font)));
			$c++;
		}
	}

	/**
	 * @return string path to the cached or downloaded TTF file
	 */
	public function maybe_get_remote_ttf($font) {
		$font_slug = preg_replace('/[^a-z0-9]/i', '', strtolower($font['family']));
		//$ttf_path = $this->dir_path.'/images/fonts/png/'.$font_slug.'.png';
		$ttf_path = $this->dir_path.'/ttf/'.$font_slug.'.ttf';
		if ( file_exists( $ttf_path ) ) {
			return $ttf_path;
		} else {
			return $this->get_remote_ttf($font);
		}
	}

	/**
	 * @return string path to the cached TTF file received from remote request.
	 */
	public function get_remote_ttf($font) {
		$font_slug = preg_replace('/[^a-z0-9]/i', '', strtolower($font['family']));
		// Load filesystem
		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem;
		WP_Filesystem();

		if ( ! defined('FS_CHMOD_FILE') ) {
			define('FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
		}

		// Create cache directory
		$dir = $this->dir_path.'/ttf';
		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			error_log( "MyThemeShop/Typography: Please check permissions. Could not create directory $dir." );
			return false;
		}
		$ttf_url = $font['file'];
		// Cache remote TTF to filesystem
		$file_content = $this->get_remote_ttf_contents($ttf_url);
		if (!$file_content) {
			return false;
		}
		$ttf_file_path = $wp_filesystem->put_contents(
			$this->dir_path.'/ttf/'.$font_slug.'.ttf',
			$file_content,
			FS_CHMOD_FILE // predefined mode settings for WP files
		);

		// Check file saved
		if ( !$ttf_file_path ) {
			error_log( "MyThemeShop/Typography: Please check permissions. Could not write font to $dir." );
			return false;
		}
		
		return $this->dir_path.'/ttf/'.$font_slug.'.ttf';
	}

	/**
	 * @return binary The active variant's TTF file contents
	 */
	public function get_remote_ttf_contents($ttf_url) {

		if ( empty( $ttf_url ) ) {
			error_log( 'MyThemeShop/Typography: Font URL not set.' );
			return false;
		}
		
		$response = wp_remote_get( $ttf_url );

		if ( is_a( $response, 'WP_Error') ) {
			error_log( "MyThemeShop/Typography: Attempt to get remote font returned an error.<br/>$ttf_url" );
			return false;
		}

		return $response['body'];
	}
	
	/**
	 * Function for retrieving and saving fonts from Google
	 *
	 *
	 * @uses get_transient()
	 * @uses set_transient()
	 * @uses wp_remote_get()
	 * @uses wp_remote_retrieve_body()
	 * @uses json_decode()
	 * @return JSON object with font data
	 *
	 */
	function get_all_fonts() {
		$fonts = get_transient( 'google_typography_fonts' );	

		if (false === $fonts)	{

			$request = wp_remote_get($this->api_url, array( 'sslverify' => false ));

			if( is_wp_error( $request ) ) {

			   $error_message = $request->get_error_message();
			
			   echo "Something went wrong: $error_message";

			} else {
				
				$json = wp_remote_retrieve_body($request);

				$data = json_decode($json, TRUE);

				$items = $data['items'];
				
				$i = 0;
				
				foreach ($items as $item) {
					
					$i++;
					
					$variants = array();
					foreach ($item['variants'] as $variant) {
						if(!stripos($variant, "italic") && $variant != "italic") {
							if($variant == "regular") {
								$variants[] = "normal";
							} else {
								$variants[] = $variant;
							}
						}
					}

					if (array_key_exists('regular', $item['files'])) {
						$ttf = $item['files']['regular'];
					} else {
						$ttf = reset($item['files']);
					}
					
					$fonts[] = array('uid' => $i, 'family' => $item['family'], 'subsets' => $item['subsets'], 'variants' => $variants, 'file' => $ttf);

				}
				
				set_transient( 'google_typography_fonts', $fonts, 60 * 60 * 24 );

			}

		}

		return $fonts;
	}
	
	/**
	 * Function for searching array of fonts
	 *
	 *
	 * @return JSON object with font data
	 *
	 */
	function multidimensional_search($parents, $searched) {
	  if (empty($searched) || empty($parents)) {
		return false;
	  }

	  foreach($parents as $key => $value) {
		$exists = true;
		foreach ($searched as $skey => $svalue) {
		  $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
		}
		if($exists){ return $parents[$key]; }
	  }

	  return false;
	}
	
	/**
	 * Enqueue admin styles and scripts
	 *
	 *
	 * @uses wp_register_script()
	 * @uses wp_enqueue_script()
	 * @uses wp_register_style()
	 * @uses wp_enqueue_style()
	 *
	 */
	function admin_scripts() {

		$screen = get_current_screen();

		if ( 'appearance_page_theme_options' !== $screen->id ) {
			return;
		}
		
		//Javascripts
		wp_register_script('google-webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.4.2/webfont.js', false);
		wp_register_script('google-typography', get_template_directory_uri() .'/options/google-typography/javascripts/google-typography.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker'), '1.1');
		wp_localize_script('google-typography', 'googletypography', array(
				'reset_confirm' => __("Are you sure you want to revert to the theme's default collections? Note: You will lose any custom collections you've created.", 'coupon' ),
				'delete_confirm' => __("Are you sure you want to delete this collection?", 'coupon' )
			));
			//wp_register_script('chosen2', get_template_directory_uri() .'/options/google-typography/javascripts/jquery.chosen.js', array('jquery'));
		wp_register_script( 'select2', get_template_directory_uri() .'/options/js/select2.min.js', array('jquery'), null, true );
		wp_enqueue_script('google-webfont');
		wp_enqueue_script('google-typography');
		//wp_enqueue_script('chosen2'); 
		wp_enqueue_script('select2');						
		
		// Stylesheets
		wp_register_style('google-typography', get_template_directory_uri() .'/options/google-typography/stylesheets/google-typography.css', false, '1.1');
		//wp_register_style('chosen', get_template_directory_uri() .'/options/google-typography/stylesheets/chosen.css', false, '1.0.0');
		wp_register_style('select2', get_template_directory_uri() .'/options/css/select2.css');
		wp_register_style('google-font', '//fonts.googleapis.com/css?family=Lato:300,400');
		wp_enqueue_style('google-typography');
		//wp_enqueue_style('chosen');
		wp_enqueue_style('select2');
		wp_enqueue_style('google-font');
		wp_enqueue_style('wp-color-picker');
	}
}

mtsGoogleTypography::init();

/**
 * Function for registering default typography collections 
 *
 *
 * @uses get_option()
 * @uses update_option()
 *
 */
function mts_register_typography($collections) {

	if(!get_option(MTS_TYPOGRAPHY_DEFAULT_OPT)) {

		$defaults = array();
		delete_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);

		foreach($collections as $key => $collection) {
			array_push($defaults, 
				array_merge(
					array('default' => true), 
					$collection
				)
			);
		}
 
		update_option(MTS_TYPOGRAPHY_DEFAULT_OPT, true);
		update_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT, $defaults);

	} 
}
