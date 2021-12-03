<?php
class NHP_Options_upload extends NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0
	*/
	function __construct($field = array(), $value ='', $parent = ''){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		$this->return_field = (isset($this->field['return']) && $this->field['return'] == 'id') ? 'id' : 'url';
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since NHP_Options 1.0
	*/
	function render(){
		
		$class = (isset($this->field['class']))?$this->field['class']:'regular-text';

		echo '<input type="hidden" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="'.$this->value.'" class="'.$class.'" />';
		if($this->return_field == 'id'){
			$img_url = wp_get_attachment_url( $this->value );
			echo '<img class="nhp-opts-screenshot" id="nhp-opts-screenshot-'.$this->field['id'].'" src="'.$img_url.'" data-return="id" />';
		} else {
			echo '<img class="nhp-opts-screenshot" id="nhp-opts-screenshot-'.$this->field['id'].'" src="'.$this->value.'" data-return="url" />';
		}
		
		if($this->value == ''){$remove = ' style="display:none;"';$upload = '';}else{$remove = '';$upload = ' style="display:none;"';}
		echo ' <a href="javascript:void(0);" class="nhp-opts-upload button-secondary"'.$upload.' rel-id="'.$this->field['id'].'">'.__('Browse', 'coupon' ).'</a>';
		echo ' <a href="javascript:void(0);" class="nhp-opts-upload-remove"'.$remove.' rel-id="'.$this->field['id'].'">'.__('Remove Upload', 'coupon' ).'</a>';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><br/><span class="description">'.$this->field['desc'].'</span>':'';
		
	}//function
	
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0
	*/
	function enqueue(){

		wp_enqueue_media();
		
		wp_enqueue_script(
			'nhp-opts-field-upload-js', 
			NHP_OPTIONS_URL.'fields/upload/field_upload.js', 
			array('jquery'),
			MTS_THEME_VERSION,
			true
		);
		
		wp_localize_script('nhp-opts-field-upload-js', 'nhp_upload', array(
			'url' => $this->url.'fields/upload/blank.png',
			'title' => __( 'Select Image', 'coupon' ),
			'buttonText' => __( 'Select Image', 'coupon' )
		));
		
	}//function
	
}//class
?>