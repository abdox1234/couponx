<?php
class NHP_Options_cats_multi_select extends NHP_Options{	
	
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
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since NHP_Options 1.0.1
	*/
	function render(){
		
		$class = (isset($this->field['class']))?$this->field['class']:'';
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" class="nhpopts-cats_multi_select '.$class.'" multiple="multiple" style="width: 100%; max-width: 240px;" data-placeholder="'.__( 'Select categories', 'coupon' ).'">';

		$args = empty($this->field['args']) ? array() : wp_parse_args($this->field['args'], array());
		
		$cats = get_categories($args);
		foreach ( $cats as $cat ) {
			$selected = (is_array($this->value) && in_array($cat->term_id, $this->value))?' selected="selected"':'';
			echo '<option value="'.$cat->term_id.'"'.$selected.'>'.$cat->name.'</option>';
		}

		echo '</select>';
		echo '<input type="button" id="'.$this->field['id'].'-selectall" value="'.__('Select All', 'coupon' ).'" class="button button-secondary select_all_cats">';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><span class="description">'.$this->field['desc'].'</span>':'';
		
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
			'nhp-opts-field-cats_multi_select-js', 
			NHP_OPTIONS_URL.'fields/cats_multi_select/field_cats_multi_select.js', 
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