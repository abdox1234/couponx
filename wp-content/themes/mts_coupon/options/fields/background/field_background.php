<?php
class NHP_Options_background extends NHP_Options{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	*/
	function __construct($field = array(), $value ='', $parent){

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();

	}//function


	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	*/
	function render(){

		$css_options = array(
			'repeat'	 => array(
				''		  => __('Default', 'coupon' ),
				'no-repeat' => __('No Repeat', 'coupon' ),
				'repeat'	=> __('Repeat All', 'coupon' ),
				'repeat-x'  => __('Repeat Horizontally', 'coupon' ),
				'repeat-y'  => __('Repeat Vertically', 'coupon' ),
				//'inherit'   => 'Inherit',
			),
			'attachment' => array(
				''		=> __('Default', 'coupon' ),
				'fixed'   => __('Fixed', 'coupon' ),
				'scroll'  => __('Scroll', 'coupon' ),
				//'inherit' => 'Inherit',
			),
			'position'   => array(
				''			  => __('Default', 'coupon' ),
				'left top'	  => __('Left Top', 'coupon' ),
				'left center'   => __('Left center', 'coupon' ),
				'left bottom'   => __('Left Bottom', 'coupon' ),
				'center top'	=> __('Center Top', 'coupon' ),
				'center center' => __('Center Center', 'coupon' ),
				'center bottom' => __('Center Bottom', 'coupon' ),
				'right top'	 => __('Right Top', 'coupon' ),
				'right center'  => __('Right center', 'coupon' ),
				'right bottom'  => __('Right Bottom', 'coupon' ),
			),
			'size'	   => array(
				''		=> __('Default', 'coupon' ),
				//'inherit' => 'Inherit',
				'cover'   => __('Cover', 'coupon' ),
				'contain' => __('Contain', 'coupon' ),
			),
			'parallax'   => array(
				'0' => __('Off', 'coupon' ),
				'1' => __('On', 'coupon' ),
			),
		);

		// Replace options if provided
		foreach ( $this->field['options'] as $key => $options ) {
			if ( array_key_exists( $key, $css_options ) && is_array( $options ) && ! empty( $options ) ) {
				$css_options[ $key ] = $options;
			}
		}

		$defaults = array(
			'color' => '',
			'use' => 'pattern',
			'image_upload' => '',
			'image_pattern' => 'nobg',
			'gradient' =>  array(
				'from'	  => '',
				'to'		=> '',
				'direction' => 'horizontal',
			),
			'repeat' => '',
			'attachment' => '',
			'position' => '',
			'size' => '',
			'parallax' => '0'
		);

		$defaults = isset( $this->field['std'] ) ? wp_parse_args( $this->field['std'], $defaults ) : $defaults;

		$this->value = wp_parse_args( $this->value, $defaults );

		//var_dump($this->value);

		echo '<div class="bg-opt-wrapper">';

		// Color
		if ( $this->field['options']['color'] !== false ) {

			echo '<div class="bg-opt-input-label">'.__( 'Background Color:', 'coupon' ).'</div>';
			echo '<input type="text" id="'.$this->field['id'].'_color" name="'.$this->args['opt_name'].'['.$this->field['id'].'][color]" value="'.$this->value['color'].'" class="popup-colorpicker" data-default-color="'.$defaults['color'].'" />';
		}

		// Tabs
		echo '<div class="bg-opt-input-label">'.__( 'Background Image:', 'coupon' ).'</div>';
		echo '<fieldset class="green buttonset buttonset-tabs ui-buttonset">';
			if ( $this->field['options']['image_pattern'] !== false ) {
				echo '<input type="radio" id="'.$this->field['id'].'_pattern" name="'.$this->args['opt_name'].'['.$this->field['id'].'][use]" class="nhp-opts-button" value="pattern" '.checked($this->value['use'], 'pattern', false).'/>';
				echo '<label id="nhp-opts-button" for="'.$this->field['id'].'_pattern" class="buttonset-tab ui-button ui-state-default ui-button-text-only"><span class="ui-button-text">'.__('Pattern', 'coupon' ).'</span></label>';
			}
			if ( $this->field['options']['image_upload'] !== false ) {
				echo '<input type="radio" id="'.$this->field['id'].'_upload" name="'.$this->args['opt_name'].'['.$this->field['id'].'][use]" class="nhp-opts-button" value="upload" '.checked($this->value['use'], 'upload', false).'/>';
				echo '<label id="nhp-opts-button" for="'.$this->field['id'].'_upload" class="buttonset-tab ui-button ui-state-default ui-button-text-only"><span class="ui-button-text">'.__('Upload', 'coupon' ).'</span></label>';
			}
			if ( $this->field['options']['gradient'] !== false ) {
				echo '<input type="radio" id="'.$this->field['id'].'_gradient" name="'.$this->args['opt_name'].'['.$this->field['id'].'][use]" class="nhp-opts-button" value="gradient" '.checked($this->value['use'], 'gradient', false).'/>';
				echo '<label id="nhp-opts-button" for="'.$this->field['id'].'_gradient" class="buttonset-tab ui-button ui-state-default ui-button-text-only"><span class="ui-button-text">'.__('Gradient', 'coupon' ).'</span></label>';
			}
		
		echo '</fieldset>';
		

		// Pattern
		if ( $this->field['options']['image_pattern'] !== false ) {
			echo '<div id="'.$this->field['id'].'_pattern_tab" class="buttonset-tab-content">';
			echo '<fieldset>';
			foreach ( $this->field['options']['image_pattern'] as $k => $v ) {

				$selected = (checked($this->value['image_pattern'], $k, false) != '')?' nhp-radio-img-selected':'';

				echo '<label class="nhp-radio-img'.$selected.' nhp-radio-img-'.$this->field['id'].'" for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options']['image_pattern'])).'">';
				echo '<input type="radio" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options']['image_pattern'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][image_pattern]" value="'.$k.'" '.checked($this->value['image_pattern'], $k, false).'/>';
				echo '<img src="'.$v['img'].'" onclick="jQuery:nhp_radio_img_select(\''.$this->field['id'].'_'.array_search($k,array_keys($this->field['options']['image_pattern'])).'\', \''.$this->field['id'].'\');" />';
				echo '</label>';
			}//foreach
			echo '</fieldset>';
			echo '</div>';
		}

		// Upload
		if ( $this->field['options']['image_upload'] !== false ) {

			echo '<div id="'.$this->field['id'].'_upload_tab" class="buttonset-tab-content">';
			echo '<fieldset>';
			echo '<input type="hidden" id="'.$this->field['id'].'_image_upload" name="'.$this->args['opt_name'].'['.$this->field['id'].'][image_upload]" value="'.$this->value['image_upload'].'" />';
			echo '<img class="nhp-opts-screenshot" id="nhp-opts-screenshot-'.$this->field['id'].'" src="'.$this->value['image_upload'].'" data-return="url" />';

			if($this->value['image_upload'] == ''){$remove = ' style="display:none;"';$upload = '';}else{$remove = '';$upload = ' style="display:none;"';}
			echo ' <a href="javascript:void(0);" class="nhp-opts-upload button-secondary"'.$upload.' rel-id="'.$this->field['id'].'_image_upload">'.__('Browse', 'coupon' ).'</a>';
			echo ' <a href="javascript:void(0);" class="nhp-opts-upload-remove"'.$remove.' rel-id="'.$this->field['id'].'_image_upload">'.__('Remove Upload', 'coupon' ).'</a>';
			echo '</fieldset>';

			// Selects
			echo '<div class="bg-upload-selects">';
			if ( $this->field['options']['repeat'] !== false ) {
				$array = $css_options['repeat'];
				echo '<div class="bg-upload-select">';
				echo '<label for="'.$this->field['id'].'_repeat" class="bg-opt-input-label">'.__( 'Background Repeat:', 'coupon' ).'</label>';
				echo '<select id="'.$this->field['id'].'_repeat" name="'.$this->args['opt_name'].'['.$this->field['id'].'][repeat]">';
				foreach ( $array as $k => $v ) {
					echo '<option value="' . $k . '"' . selected( $this->value['repeat'], $k, false ) . '>' . $v . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}
			if ( $this->field['options']['attachment'] !== false ) {
				$array = $css_options['attachment'];
				echo '<div class="bg-upload-select">';
				echo '<label for="'.$this->field['id'].'_attachment" class="bg-opt-input-label">'.__( 'Background Attachment:', 'coupon' ).'</label>';
				echo '<select id="'.$this->field['id'].'_attachment" name="'.$this->args['opt_name'].'['.$this->field['id'].'][attachment]">';
				foreach ( $array as $k => $v ) {
					echo '<option value="' . $k . '"' . selected( $this->value['attachment'], $k, false ) . '>' . $v . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}
			if ( $this->field['options']['position'] !== false ) {
				$array = $css_options['position'];
				echo '<div class="bg-upload-select">';
				echo '<label for="'.$this->field['id'].'_position" class="bg-opt-input-label">'.__( 'Background Position:', 'coupon' ).'</label>';
				echo '<select id="'.$this->field['id'].'_position" name="'.$this->args['opt_name'].'['.$this->field['id'].'][position]">';
				foreach ( $array as $k => $v ) {
					echo '<option value="' . $k . '"' . selected( $this->value['position'], $k, false ) . '>' . $v . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}
			if ( $this->field['options']['size'] !== false ) {
				$array = $css_options['size'];
				echo '<div class="bg-upload-select">';
				echo '<label for="'.$this->field['id'].'_size" class="bg-opt-input-label">'.__( 'Background Size:', 'coupon' ).'</label>';
				echo '<select id="'.$this->field['id'].'_size" name="'.$this->args['opt_name'].'['.$this->field['id'].'][size]">';
				foreach ( $array as $k => $v ) {
					echo '<option value="' . $k . '"' . selected( $this->value['size'], $k, false ) . '>' . $v . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}
			echo '</div>';

		echo '</div>';
		}

		// Gradient
		if ( $this->field['options']['gradient'] !== false ) {
			echo '<div id="'.$this->field['id'].'_gradient_tab" class="buttonset-tab-content">';
			echo '<div class="color-gradient-wrapper">';
			echo '<div class="color-gradient-step-wrapper">';
			echo '<div class="bg-opt-input-label">' . __('From:', 'coupon' ) . '</div><input type="text" id="'.$this->field['id'].'_gradient_from" name="'.$this->args['opt_name'].'['.$this->field['id'].'][gradient][from]" value="'.$this->value['gradient']['from'].'" class="popup-colorpicker" data-default-color="'.$defaults['gradient']['from'].'" style="width:70px;"/>';
			echo '</div>';
			echo '<div class="color-gradient-step-wrapper">';
			echo '<div class="bg-opt-input-label">' . __('To:', 'coupon' ) . '</div><input type="text" id="'.$this->field['id'].'_gradient_to" name="'.$this->args['opt_name'].'['.$this->field['id'].'][gradient][to]" value="'.$this->value['gradient']['to'].'" class="popup-colorpicker" data-default-color="'.$defaults['gradient']['to'].'" style="width:70px;"/>';
			echo '</div>';

			echo '<div class="color-gradient-direction-wrapper">';
			echo '<label for="'.$this->field['id'].'_gradient_direction" class="bg-opt-input-label">' . __('Direction:', 'coupon' ) . '</label>';
			echo '<select id="'.$this->field['id'].'_gradient_direction" name="'.$this->args['opt_name'].'['.$this->field['id'].'][gradient][direction]" class="color-gradient-direction">';
				echo '<option value="horizontal"' . selected( $this->value['gradient']['direction'], 'horizontal', false ) . '>' . __( 'Horizontal', 'coupon' ) . '</option>';
				echo '<option value="vertical"' . selected( $this->value['gradient']['direction'], 'vertical', false ) . '>' . __( 'Vertical', 'coupon' ) . '</option>';
			echo '</select>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		// Parallax
		if ( $this->field['options']['parallax'] !== false ) {
			$array = $css_options['parallax'];
			echo '<div class="bg-opt-input-label">'.__( 'Parallax Effect:', 'coupon' ).'</div>';
			echo '<fieldset class="buttonset parallax-buttonset ui-buttonset">';
				foreach( $array as $k => $v ) {
					echo '<input type="radio" id="'.$this->field['id'].'_parallax_'.array_search($k,array_keys($array)).'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][parallax]" class="nhp-opts-button" value="'.$k.'" '.checked($this->value['parallax'], $k, false).'/>';
					echo '<label id="nhp-opts-button" for="'.$this->field['id'].'_parallax_'.array_search($k,array_keys($array)).'" class="ui-button ui-state-default ui-button-text-only"><span class="ui-button-text">'.$v.'</span></label>';
					
				}//foreach
			echo '</fieldset>';
		}

		echo '</div>';

	}//function


	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0
	*/
	function enqueue(){

		// Styles & Scripts for reused fields
		$existing_fields = array( 'color', 'upload', 'radio_img' );

		foreach ( $existing_fields as $key => $field_type) {

			$field_class = 'NHP_Options_' . $field_type;

			if (!class_exists($field_class)) {

				$class_file = NHP_OPTIONS_DIR . 'fields/' . $field_type . '/field_' . $field_type . '.php';

				if ($class_file) {
					/** @noinspection PhpIncludeInspection */
					require_once($class_file);
				}
			}

			if (class_exists($field_class) && method_exists($field_class, 'enqueue')) {
				$enqueue = new $field_class(array(), '', $this);
				$enqueue->enqueue();
			}
		}

		// Styles & Scripts for this field
		wp_enqueue_style('nhp-opts-field-background-css', NHP_OPTIONS_URL.'fields/background/field_background.css');
		
		wp_enqueue_script(
			'nhp-opts-field-background-js', 
			NHP_OPTIONS_URL.'fields/background/field_background.js', 
			array('jquery', 'jquery-ui-core', 'jquery-ui-button'),
			MTS_THEME_VERSION,
			true
		);
	}// enqueue function

}//class
?>