<?php
class NHP_Options_coupon_cat_multi_checkbox extends NHP_Options{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0
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
	 * @since NHP_Options 1.0
	*/
	function render(){

		$class = (isset($this->field['class']))?$this->field['class']:'regular-text';

		echo '<fieldset>';
		$args = array( 'hide_empty' => 0 );

		// $coupon_categories = get_terms( 'mts_coupon_categories', $args);
		$coupon_categories = get_terms( array(
			'taxonomy'   => 'mts_coupon_categories',
			'hide_empty' => false,
		) );

		$categories = array();
		foreach ( $coupon_categories as $term_i => $term ) {
			$categories[ $term->term_id ] = $term->name;
		}
		foreach ( $categories as $k => $v ) {
			$k_val = '';
			if ( ! empty( $this->value[ $k ] ) ) {
				$k_val = $this->value[ $k ];
				// $this->value[$k] = $this->value[$k] ? $this->value[$k] : '';
			}

			echo '<label for="'.$this->field['id'].'_'.array_search($k,array_keys($categories)).'">';
			echo '<input type="checkbox" id="'.$this->field['id'].'_'.array_search($k,array_keys($categories)).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']['.$k.']" '.$class.' value="1" '.checked($k_val, '1', false).'/>';
			echo ' '.$v.'</label><br/>';

		}//foreach

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<span class="description">'.$this->field['desc'].'</span>':'';

		echo '</fieldset>';

	}//function

}//class
?>
