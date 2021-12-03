<?php

/**
 * Group field type. Ported from Redux.
 * Can only group simple fields like select, input, etc. - without JS
 * 
 */

class NHP_Options_group extends NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->parent = $parent;
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
		if (empty($this->value) || !is_array($this->value)) {
			$this->value = array(
				array(
					'group_title' => __('New', 'coupon' ).' '.$this->field['groupname'],
					'group_sort' => '0',
				)
			);
		}
		$groups = $this->value;
		   
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].' ':'';
		
		echo '<div class="nhpoptions-group">';
		echo '<input type="hidden" class="nhp-opts-dummy-group-count" id="nhp-opts-dummy-' . $this->field['id'] . '-count" name="nhp-opts-dummy-' . $this->field['id'] . '-count" value="' . count($groups) . '" />';
		echo '<div id="nhp-opts-groups-accordion">';

		// Create dummy content for the adding new ones
		echo '<div class="nhp-opts-groups-accordion-group nhp-opts-dummy" style="display:none" id="nhp-opts-dummy-' . $this->field['id'] . '"><h3><span class="nhp-opts-groups-header">' . __("New ", 'coupon') . $this->field['groupname'] . '</span></h3>';
		echo '<div>';//according content open
			
		echo '<table style="margin-top: 0;" class="nhp-opts-groups-accordion nhp-opts-group form-table no-border">';	
		echo '<fieldset><input type="hidden" id="' . $this->field['id'] . '_group-title" data-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@][group_title]" value="" class="regular-text group-title" /></fieldset>';
		echo '<input type="hidden" class="group-sort" data-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@][group_sort]" id="' . $this->field['id'] . '-group_sort" value="" />';
		
		$the_id = $this->field['id'];
		$x = 0;
		$field_is_title = true;
			foreach ($this->field['subfields'] as $field) {
				//we will enqueue all CSS/JS for sub fields if it wasn't enqueued
				$this->enqueue_dependencies($field['type']);

				echo '<tr><td>';
				if(isset($field['class']))
					$field['class'] .= " group";
				else
					$field['class'] = " group";

				if (!empty($field['title']))
					echo '<h4>' . $field['title'] . '</h4>';
					
				if (!empty($field['sub_desc']))
					echo '<span class="description">' . $field['sub_desc'] . '</span>';
				
				$value = empty($this->options[$field['id']][0]) ? "" : $this->options[$field['id']][0];

				ob_start();
				$val = $this->_field_input($field, $the_id, $x);

				$content = ob_get_contents();

				//adding sorting number to the name of each fields in group
				$name = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
				$content = str_replace($name,$this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@]['.$field['id'].']', $content);
				// remove the name property. asigned by the controller, create new data-name property for js
				$content = str_replace('name=', 'data-name=', $content);

				if(($field['type'] === "text") && ($field_is_title)) {
					//$content = str_replace('>', 'data-title="true" />', $content);
					$content = str_replace('value=""', 'value="'.( isset( $field['value'] ) ? $field['value'] : '' ).'"', $content);
					$field_is_title = false;
				}

				//we should add $sort to id to fix problem with select field
				$content = str_replace(' id="'.$field['id'].'-select"', ' id="'.$field['id'].'-select-'.'dummy'.'"', $content);

				//add $sort to id to fix problem with radio field
				if ($field['type'] == 'radio') {
					$content = str_replace('label for="'.$field['id'], 'label for="'.$field['id'].'_dummy', $content);
					$content = str_replace('type="radio" id="'.$field['id'], 'type="radio" id="'.$field['id'].'_dummy', $content);
				}
				if ($field['type'] == 'checkbox') {
					$std = isset( $field['std'] ) ? $field['std'] : '0';
					$content = str_replace('type="checkbox"', 'type="checkbox" data-std="'.$std.'"', $content);
				}
				//add $sort to id to fix problem with upload field
				$content = str_replace('type="hidden" id="'.$field['id'].'"', 'type="hidden" id="'.$field['id'].'-dummy-'.$x.'"', $content);
				$content = str_replace('rel-id="'.$field['id'].'"', 'rel-id="'.$field['id'].'-dummy-'.$x.'"', $content);
				
				//$_field = apply_filters('nhp-opts-support-group',$content, $field, 0);
				$_field = $content;
				
				ob_end_clean();
				echo $_field;
				
				echo '</td></tr>';
			}
			echo '</table>';
			echo '<a href="javascript:void(0);" class="button button-secondary nhp-opts-groups-close">' . __('OK', 'coupon' ). '</a>';
			echo '<a href="javascript:void(0);" class="button deletion nhp-opts-groups-remove">' . __('Delete', 'coupon' ).' '. $this->field['groupname']. '</a>';
			echo '</div></div>';

		// Create real groups
		$x = 0;
		if (empty($groups[0]) || count($groups[0]) > 2) { // check if only default fields are present, don't display that
		foreach ($groups as $k => $group) {
			echo '<div class="nhp-opts-groups-accordion-group"><h3><span class="nhp-opts-groups-header">' . $group['group_title'] . '</span></h3>';
			echo '<div>';//according content open
			
			echo '<table style="margin-top: 0;" class="nhp-opts-groups-accordion nhp-opts-group form-table no-border">';
			
			//echo '<h4>' . __('Group Title', 'coupon' ) . '</h4>';
			echo '<fieldset><input type="hidden" id="' . $this->field['id'] . '-group_title_' . $x . '" name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][' . $x . '][group_title]" value="' . esc_attr($group['group_title']) . '" class="regular-text group-title" /></fieldset>';
			echo '<input type="hidden" class="group-sort" name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][' . $x . '][group_sort]" id="' . $this->field['id'] . '-group_sort_' . $x . '" value="' . $group['group_sort'] . '" />';
			
			$field_is_title = true;

			foreach ($this->field['subfields'] as $field) {
				//we will enqueue all CSS/JS for sub fields if it wasn't enqueued
				$this->enqueue_dependencies($field['type']);
				
				echo '<tr><td>';
				if(isset($field['class']))
					$field['class'] .= " group";
				else
					$field['class'] = " group";

				if (!empty($field['title']))
					echo '<h4>' . $field['title'] . '</h4>';
				
				if (!empty($field['sub_desc']))
					echo '<span class="description">' . $field['sub_desc'] . '</span>';
				if (isset($group[$field['id']]) && !empty($group[$field['id']])) {
						$value = $group[$field['id']];		   
				}
				
				$value = empty($value) ? "" : $value;

				ob_start();
				$this->_field_input($field, $the_id, $k);
				//if (isset($this->options[$field['id']]) && !empty($this->options[$field['id']]) && is_array($this->options[$field['id']])) {
					//	$value = next($this->options[$field['id']]);
				//}

				$content = ob_get_contents();

				//adding sorting number to the name of each fields in group
				$name = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
				$content = str_replace($name, $this->parent->args['opt_name'] . '[' . $this->field['id'] . ']['.$x.']['.$field['id'].']', $content);

				//we should add $sort to id to fix problem with select field
				$content = str_replace(' id="'.$field['id'].'-select"', ' id="'.$field['id'].'-select-'.$x.'"', $content);

				if ($field['type'] == 'radio') {
					$content = str_replace('label for="'.$field['id'], 'label for="'.$field['id'].'_'.$x, $content);
					$content = str_replace('type="radio" id="'.$field['id'], 'type="radio" id="'.$field['id'].'_'.$x, $content);
				}
				
				//add $sort to id to fix problem with upload field
				$content = str_replace('type="hidden" id="'.$field['id'].'"', 'type="hidden" id="'.$field['id'].'-'.$x.'"', $content);
				$content = str_replace('rel-id="'.$field['id'].'"', 'rel-id="'.$field['id'].'-'.$x.'"', $content);
				
				
				if(($field['type'] === "text") && ($field_is_title)) {
					//$content = str_replace('>', 'data-title="true" />', $content);
					$content = str_replace('value=""', 'value="'.( isset( $field['value'] ) ? $field['value'] : '' ).'"', $content);
					$field_is_title = false;
				}
				
				//$_field = apply_filters('nhp-opts-support-group',$content, $field, $x);
				$_field = $content;
				
				ob_end_clean();
				echo $_field;
				
				echo '</td></tr>';
			}
			echo '</table>';
			echo '<a href="javascript:void(0);" class="button button-secondary nhp-opts-groups-close">' . __('OK', 'coupon' ). '</a>';
			echo '<a href="javascript:void(0);" class="button deletion nhp-opts-groups-remove">' . __('Delete', 'coupon' ).' '.$this->field['groupname']. '</a>';
			echo '</div></div>';
			$x++;
		}
		}
		echo '</div><a href="javascript:void(0);" class="button nhp-opts-groups-add button-secondary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][group_title][]">' . __('Add', 'coupon' ) .' '.$this->field['groupname']. '</a><br/>';

		echo '</div>';
			
	}// render function
	
	// From Redux
	// Not sure what this is for
	function support_multi($content, $field, $sort) {
		//convert name
		$name = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
		$content = str_replace($name, $name . '[' . $sort . ']', $content);
		//we should add $sort to id to fix problem with select field
		$content = str_replace(' id="'.$field['id'].'-select"', ' id="'.$field['id'].'-select-'.$sort.'"', $content);
		return $content;
	}
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0
	*/
	function enqueue(){
		
		wp_enqueue_style('nhp-opts-field-group-css', NHP_OPTIONS_URL.'fields/group/field_group.css');
		
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-sortable');
		
		wp_enqueue_script(
			'nhp-opts-field-group-js', 
			NHP_OPTIONS_URL.'fields/group/field_group.js', 
			array('jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-sortable'),
			MTS_THEME_VERSION,
			true
		);

		
	}// enqueue function
	
	function enqueue_dependencies($field_type) {
		$field_class = 'NHP_Options_' . $field_type;

		if (!class_exists($field_class)) {
			//$class_file = apply_filters('nhp-opts-typeclass-load', NHP_Options::$_dir . 'inc/fields/' . $field_type . '/field_' . $field_type . '.php', $field_class);
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
	} // enqueue dependencies function
	
}//class
?>
