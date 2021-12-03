<?php
class NHP_Options_sidebars_select extends NHP_Options{	
	
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
		global $wp_registered_sidebars;
		
		$class = (isset($this->field['class'])) ? ' '.$this->field['class'] : '';
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" class="nhp-opts-sidebar-select'.$class.'" >';
		
		$args = isset($this->field['args']) ? wp_parse_args($this->field['args'], array()) : array();
			
		$sidebars = $wp_registered_sidebars;
		
		$hidden_sidebars = (isset($args['exclude']) && is_array($args['exclude'])) ? $args['exclude'] : array();
		$allow_nosidebar = (!isset($args['allow_nosidebar']) || $args['allow_nosidebar']) ? true : false; // true by deault
		
		$exclude_patterns = array();
		foreach ($hidden_sidebars as $k => $sidebar) {
			if ( strpos($sidebar, '*') ) {
				$exclude_patterns[] = '['.str_replace('\\*', '.+', preg_quote($sidebar)).']';
				unset($hidden_sidebars[$k]);
			}
		}

		// default
		echo '<option value="" '.selected('', $this->value, false).'>-- '.__('Default', 'coupon' ).' --</option>';
		
		foreach ($sidebars as $sidebar) {
		  if (!in_array($sidebar['id'], $hidden_sidebars)) {
		  	$continue = false;
		  	foreach ($exclude_patterns as $pattern) {
		  		if ( @preg_match( $pattern, $sidebar['id'] ) ) {
		  			$continue = true;
		  			break;
		  		}
		  	}
		  	if ( $continue )
		  		continue;

			echo '<option value="'.esc_attr($sidebar['id']).'" '.selected($sidebar['id'], $this->value, false).'>'.$sidebar['name'].'</option>';
		  }
		}
		
		// nosidebar
		if ($allow_nosidebar) 
			echo '<option value="mts_nosidebar" '.selected('mts_nosidebar', $this->value, false).'>-- '.__('No Sidebar', 'coupon' ).' --</option>';
		
		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
		
	}//function
	
}//class
?>