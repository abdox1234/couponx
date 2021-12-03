<?php
class NHP_Options_icon_select extends NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0.1
	*/
	function __construct($field = array(), $value ='', $parent){

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		$this->icons = mts_get_icons();
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since NHP_Options 1.0.1
	*/
	function render(){
		
		// class
		$class = 'class="nhpopts-iconselect '.(isset($this->field['class']) ? $this->field['class'] : '').'"';
		
		// subset
		if (!empty($this->field['subset']) && isset($this->icons[$this->field['subset']])) {
		  $subset = $this->field['subset'];
		  $this->icons = array($this->icons[$subset]);
		}
		
		// allow empty
		$allow_empty = true; // default
		if (isset($this->field['allow_empty']) && ($this->field['allow_empty'] == false || $this->field['allow_empty'] == 'false')) {
			$allow_empty = false;
		}
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.' style="width: 100%; max-width: 240px;">';
		if ($allow_empty)
			echo '<option value=""'.selected($this->value, '', false).'>'.__('No Icon', 'coupon' ).'</option>';
		foreach ( $this->icons as $icon_category => $icons ) {
			if (!isset($subset))
				echo '<optgroup label="'.$icon_category.'">';
			foreach ($icons as $icon) {
				echo '<option value="'.$icon.'"'.selected($this->value, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
			}
			echo '</optgroup>';
		}

		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
		
	}//function
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0
	*/
	function enqueue(){
		
		wp_enqueue_script(
			'select2', 
			NHP_OPTIONS_URL.'js/select2.min.js', 
			array('jquery'),
			MTS_THEME_VERSION,
			true
		);
		wp_enqueue_script(
			'nhp-opts-field-icon_select-js', 
			NHP_OPTIONS_URL.'fields/icon_select/field_icon_select.js', 
			array('jquery', 'select2'),
			MTS_THEME_VERSION,
			true
		);
		wp_enqueue_style(
			'select2', 
			NHP_OPTIONS_URL.'css/select2.css', 
			array(),
			MTS_THEME_VERSION,
			'all'
		);

		
	}//function
}//class
?>