<?php
class NHP_Options_navbars_select extends NHP_Options {

	private $field;
	private $value;

	public function __construct( $field, $value, $parent ) {
		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {
		global $mts_options;

		$class = isset( $this->field['class'] ) ? ' ' . $this->field['class'] : '';

		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" class="nhp-opts-navbar-select' . $class . '" >';
		echo '<option value="" ' . selected( '', $this->value, false ) . '>-- ' . __( 'Default', 'coupon' ) . ' --</option>';

		if ( isset( $mts_options['mts_custom_navbars'] ) && is_array( $mts_options['mts_custom_navbars'] ) ) {
			foreach ( $mts_options['mts_custom_navbars'] as $navmenu ) {
				echo '<option value="' . esc_attr( $navmenu['mts_custom_navbar_name'] ) . '" '. selected( $navmenu['mts_custom_navbar_name'], $this->value, false ) . '>' . esc_html( $navmenu['mts_custom_navbar_name'] ) . '</option>';
			}
		}

		echo '</select>';
	}

}