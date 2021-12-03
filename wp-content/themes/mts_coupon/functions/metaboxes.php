<?php

/**
 * Add a "Sidebar" selection metabox.
 */
function mts_add_sidebar_metabox() {
	$screens = array('post', 'page', 'coupons');
	foreach ($screens as $screen) {
		add_meta_box(
			'mts_sidebar_metabox',
			__('Sidebar', 'coupon' ),
			'mts_inner_sidebar_metabox',
			$screen,
			'side',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'mts_add_sidebar_metabox');


/**
 * Print the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function mts_inner_sidebar_metabox($post) {
	global $wp_registered_sidebars;
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field('mts_inner_sidebar_metabox', 'mts_inner_sidebar_metabox_nonce');
	
	/*
	* Use get_post_meta() to retrieve an existing value
	* from the database and use the value for the form.
	*/
	$custom_sidebar = get_post_meta( $post->ID, '_mts_custom_sidebar', true );
	$sidebar_location = get_post_meta( $post->ID, '_mts_sidebar_location', true );

	// Select custom sidebar from dropdown
	echo '<select name="mts_custom_sidebar" id="mts_custom_sidebar" style="margin-bottom: 10px;">';
	echo '<option value="" '.selected('', $custom_sidebar).'>-- '.__('Default', 'coupon' ).' --</option>';
	
	// Exclude built-in sidebars
	$hidden_sidebars = array('sidebar', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'sidebar-coupons', 'sidebar-single-coupon', 'widget-subscribe');	

	foreach ($wp_registered_sidebars as $sidebar) {
		if (!in_array($sidebar['id'], $hidden_sidebars) && substr($sidebar['id'], 0, 15) != 'sidebar_coupon_') {
			echo '<option value="'.esc_attr($sidebar['id']).'" '.selected($sidebar['id'], $custom_sidebar, false).'>'.$sidebar['name'].'</option>';
		}
	}
	if ( get_post_type( $post ) != 'coupons' )
		echo '<option value="mts_nosidebar" '.selected('mts_nosidebar', $custom_sidebar).'>-- '.__('No sidebar --', 'coupon' ).'</option>';
	echo '</select><br />';
	
	// Select single layout (left/right sidebar)
	echo '<div class="mts_sidebar_location_fields">';
	echo '<label for="mts_sidebar_location_default" style="display: inline-block; margin-right: 20px;"><input type="radio" name="mts_sidebar_location" id="mts_sidebar_location_default" value=""'.checked('', $sidebar_location, false).'>'.__('Default side', 'coupon' ).'</label>';
	echo '<label for="mts_sidebar_location_left" style="display: inline-block; margin-right: 20px;"><input type="radio" name="mts_sidebar_location" id="mts_sidebar_location_left" value="left"'.checked('left', $sidebar_location, false).'>'.__('Left', 'coupon' ).'</label>';
	echo '<label for="mts_sidebar_location_right" style="display: inline-block; margin-right: 20px;"><input type="radio" name="mts_sidebar_location" id="mts_sidebar_location_right" value="right"'.checked('right', $sidebar_location, false).'>'.__('Right', 'coupon' ).'</label>';
	echo '</div>';
	
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			function mts_toggle_sidebar_location_fields() {
				$('.mts_sidebar_location_fields').toggle(($('#mts_custom_sidebar').val() != 'mts_nosidebar'));
			}
			mts_toggle_sidebar_location_fields();
			$('#mts_custom_sidebar').change(function() {
				mts_toggle_sidebar_location_fields();
			});
		});
	</script>
	<?php
	//debug
	//global $wp_meta_boxes;
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return int
 */
function mts_save_custom_sidebar( $post_id ) {
	
	/*
	* We need to verify this came from our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['mts_inner_sidebar_metabox_nonce'] ) )
	return $post_id;
	
	$nonce = $_POST['mts_inner_sidebar_metabox_nonce'];
	
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'mts_inner_sidebar_metabox' ) )
	  return $post_id;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
	
	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
	
	if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	
	} else {
	
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	
	/* OK, its safe for us to save the data now. */
	
	// Sanitize user input.
	$sidebar_name = sanitize_text_field( $_POST['mts_custom_sidebar'] );
	$sidebar_location = sanitize_text_field( $_POST['mts_sidebar_location'] );
	
	// Update the meta field in the database.
	update_post_meta( $post_id, '_mts_custom_sidebar', $sidebar_name );
	update_post_meta( $post_id, '_mts_sidebar_location', $sidebar_location );
}
add_action( 'save_post', 'mts_save_custom_sidebar' );


/**
 * Add "Post Template" selection meta box
 */
function mts_add_posttemplate_metabox() {
	add_meta_box(
		'mts_posttemplate_metabox',		 // id
		__('Template', 'coupon' ),	  // title
		'mts_inner_posttemplate_metabox',   // callback
		'post',							 // post_type
		'side',							 // context (normal, advanced, side)
		'high'							  // priority (high, core, default, low)
	);
}
//add_action('add_meta_boxes', 'mts_add_posttemplate_metabox');


/**
 * Print the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function mts_inner_posttemplate_metabox($post) {
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field('mts_inner_posttemplate_metabox', 'mts_inner_posttemplate_metabox_nonce');
	
	/*
	* Use get_post_meta() to retrieve an existing value
	* from the database and use the value for the form.
	*/
	$posttemplate = get_post_meta( $post->ID, '_mts_posttemplate', true );

	// Select post template
	echo '<select name="mts_posttemplate" style="margin-bottom: 10px;">';
	echo '<option value="" '.selected('', $posttemplate).'>'.__('Default Post Template', 'coupon' ).'</option>';
	echo '<option value="parallax" '.selected('parallax', $posttemplate).'>'.__('Parallax Template', 'coupon' ).'</option>';
	echo '<option value="zoomout" '.selected('zoomout', $posttemplate).'>'.__('Zoom Out Effect Template', 'coupon' ).'</option>';
	echo '</select><br />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return int
 */
function mts_save_posttemplate( $post_id ) {
	
	/*
	* We need to verify this came from our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['mts_inner_posttemplate_metabox_nonce'] ) )
	return $post_id;
	
	$nonce = $_POST['mts_inner_posttemplate_metabox_nonce'];
	
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'mts_inner_posttemplate_metabox' ) )
	  return $post_id;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
	
	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
	
	if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	
	} else {
	
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	
	/* OK, its safe for us to save the data now. */
	
	// Sanitize user input.
	$posttemplate = sanitize_text_field( $_POST['mts_posttemplate'] );
	
	// Update the meta field in the database.
	update_post_meta( $post_id, '_mts_posttemplate', $posttemplate );
}
add_action( 'save_post', 'mts_save_posttemplate' );

// Related function: mts_get_posttemplate( $single_template ) in functions.php

/**
 * Add "Page Header Animation" metabox.
 */
function mts_add_postheader_metabox() {
	$screens = array('post', 'page');
	foreach ($screens as $screen) {
		add_meta_box(
			'mts_postheader_metabox',
			__('Header Animation', 'coupon' ),
			'mts_inner_postheader_metabox',
			$screen,
			'side',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'mts_add_postheader_metabox');


/**
 * Print the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function mts_inner_postheader_metabox($post) {
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field('mts_inner_postheader_metabox', 'mts_inner_postheader_metabox_nonce');
	
	/*
	* Use get_post_meta() to retrieve an existing value
	* from the database and use the value for the form.
	*/
	$postheader = get_post_meta( $post->ID, '_mts_postheader', true );

	// Select post header effect
	echo '<select name="mts_postheader" style="margin-bottom: 10px;">';
	echo '<option value="" '.selected('', $postheader).'>'.__('None', 'coupon' ).'</option>';
	echo '<option value="parallax" '.selected('parallax', $postheader).'>'.__('Parallax Effect', 'coupon' ).'</option>';
	echo '<option value="zoomout" '.selected('zoomout', $postheader).'>'.__('Zoom Out Effect', 'coupon' ).'</option>';
	echo '</select><br />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return int
 *
 * @see mts_get_post_header_effect
 */
function mts_save_postheader( $post_id ) {
	
	/*
	* We need to verify this came from our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['mts_inner_postheader_metabox_nonce'] ) )
	return $post_id;
	
	$nonce = $_POST['mts_inner_postheader_metabox_nonce'];
	
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'mts_inner_postheader_metabox' ) )
	  return $post_id;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
	
	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
	
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	
	} else {
	
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}
	
	/* OK, its safe for us to save the data now. */
	
	// Sanitize user input.
	$postheader = sanitize_text_field( $_POST['mts_postheader'] );
	
	// Update the meta field in the database.
	update_post_meta( $post_id, '_mts_postheader', $postheader );
}
add_action( 'save_post', 'mts_save_postheader' );


/**
 * Include and setup custom metaboxes and fields for Coupons post type.
 *
 * @category coupon
 * @package  coupon
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link	 https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_init', 'mts_register_coupon_single_metabox' );
/**
 * Hook in and add a metabox to demonstrate repeatable grouped fields
 */
function mts_register_coupon_single_metabox() {
	$prefix = 'mts_';

	/**
	 * Repeatable Field Groups
	 */
	$cmb = new_cmb2_box( array(
		'id'			=> $prefix . 'coupon_info',
		'title'		 => __( 'Custom Coupon Info', 'coupon' ),
		'object_types'  => array( 'coupons', ), // Post type
		'context'	  => 'normal',
		'priority'	 => 'high',
		'show_names'   => true, // Show field names on the left
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_featured_text',
		'name' => __( 'Featured Text', 'coupon' ),
		'desc' => __( 'This text will show as Featured Image', 'coupon' ),
		'type' => 'text',
		'default' => 'Great Deal',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_extra_rewards',
		'name' => __( 'Extra Rewards', 'coupon' ),
		'desc' => __( 'This text will show as custom meta in post.', 'coupon' ),
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_expire',
		'name' => __( 'Select Coupon Expiry Date', 'coupon' ),
		'type' => 'text_date',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_expire_time',
		'name' => __( 'Select Coupon Expiry Time', 'coupon' ),
		'type' => 'text_time',
		'default' => '12:00 AM'
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_people_used',
		'name' => __( 'People Used', 'coupon' ),
		'desc' => __( 'This text will show as custom meta in post.', 'coupon' ),
		'type' => 'text',
		'default' => '1',
	) );

	$cmb->add_field( array(
		'id'	  => $prefix . 'coupon_button_type',
		'name'	=> __( 'Promotion Type', 'coupon' ),
		'desc'	=> __( 'Select your promotion type.', 'coupon' ),
		'type'	=> 'radio_inline',
		'default' => 'coupon',
		'options' => array(
			'deal' => __( 'Deal', 'coupon' ),
			'coupon' => __( 'Coupon', 'coupon' ),
		),
	) );
/*
	$cmb->add_field( array(
		'id'	  => $prefix . 'coupon_open',
		'name'	=> __( 'New Window', 'coupon' ),
		'desc'	=> __( 'Open deal/coupon in the current tab or a new tab.', 'coupon' ),
		'type'	=> 'select',
		'default' => 'new',
		'options' => array(
			'new' => __( 'New tab', 'coupon' ),
			'current' => __( 'Current tab', 'coupon' )
		),
	) );
*/
	$cmb->add_field( array(
		'id'		 => $prefix . 'coupon_deal_URL',
		'name'	   => __( 'Deal URL', 'coupon' ),
		'type'	   => 'text_url',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_code',
		'name' => __( 'Coupon Code', 'coupon' ),
		'desc' => __( 'Enter the coupon code here if available', 'coupon' ),
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coupon_code_image',
		'name' => __( 'Coupon Code Image', 'coupon' ),
		'desc' => __( 'Upload the coupon code image here if available', 'coupon' ),
		'type'    => 'file',
		'options' => array(
			'url' => false,
		),
		'text'    => array(
			'add_upload_file_text' => __('Add coupon image', 'coupon')
		),
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
			),
		),
		'preview_size' => 'large', 
	) );
}

// add admin scripts
add_action('admin_enqueue_scripts', 'mts_coupon_code_field');
function mts_coupon_code_field() {
	$screen = get_current_screen();
	$screen_id = $screen->id;

	if ( 'coupons' == $screen_id ) {
		wp_enqueue_script(
			'coupon_code_field',
			get_template_directory_uri() . '/js/coupon_code_field.js',
			array( 'jquery' ),
			MTS_THEME_VERSION,
			true
		);
	}
}

add_action( 'save_post', 'mts_check_coupon_expiry', 11 );
function mts_check_coupon_expiry( $post_id ) {
	$coupon_expiry_date = get_post_meta( $post_id, 'mts_coupon_expire' );
	if ( ! $coupon_expiry_date ) {
		delete_post_meta( $post_id, 'mts_coupon_expired' );
		return;
	}
	$now = new DateTime(current_time('mysql'));
	$ref = new DateTime($coupon_expiry_date[0]);
	$diff = $now->diff($ref);

	if ( $diff->invert ) {
		update_post_meta( $post_id, 'mts_coupon_expired', '1' );
	} else {
		delete_post_meta( $post_id, 'mts_coupon_expired' );
	}
}
