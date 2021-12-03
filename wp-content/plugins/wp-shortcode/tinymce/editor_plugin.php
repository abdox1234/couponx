<?php
// look up for the path
require_once('mnm_config.php');
// check for rights
if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') )
	wp_die(__("You are not allowed to be here", "wp-shortcode"));
		global $wpdb;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php _e('Shortcode Panel', 'wp-shortcode'); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

		<script language="javascript" type="text/javascript" src="<?php echo includes_url(); ?>js/jquery/jquery.js"></script>
		<?php include('shortcodes.php'); ?>
	<script language="javascript" type="text/javascript" src="<?php echo plugin_dir_url(__FILE__); ?>tinymce.js"></script>
	<base target="_self" />
		<link rel="stylesheet" type="text/css" href="<?php echo  plugin_dir_url(__FILE__); ?>editor_plugin.css" media="all" />
</head>
<body id="link">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="mnm_tabs" action="#" id="mnmshortcode_form">
	<div>
		<!-- gallery panel -->
		<div id="mnmshortcode_panel" class="panel">
		<table border="0" cellpadding="4" cellspacing="0">
				 <tr>
						<td><label for="mnmshortcode_tag"><?php _e("Select Shortcode", 'wp-shortcode'); ?></label></td>
						<td><select id="mnmshortcode_tag" name="mnmshortcode_tag">
								<option value="0"><?php _e('Select Shortcode', 'wp-shortcode'); ?></option>
				<?php
					if(is_array($shortcodes)) {
						$i = 1;

						foreach ( $shortcodes as $mnm_shortcodekey => $short_code_value ) {
							echo '<option value="' . $mnm_shortcodekey . '" >' . (isset($short_code_value['label']) ? $short_code_value['label'] : $mnm_shortcodekey).'</option>' . "\n";
							$i++;
						}
					}
			?>
						</select></td>
					</tr>

				</table>
		</div>

	</div>


	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e('Cancel', 'wp-shortcode'); ?>" onClick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e('Insert', 'wp-shortcode'); ?>" onClick="mnmshortcodesubmit();" />
		</div>
	</div>
</form>
</body>
</html>
