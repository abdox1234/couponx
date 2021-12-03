function mnmshortcodesubmit() {
	var tagtext;
		var mnm_shortcodeid = document.getElementById('mnmshortcode_tag').value;
		if (mnm_shortcodeid == 0) {
				tinyMCEPopup.close();
				return;
		}
		if (typeof shortcodes[mnm_shortcodeid] != 'undefined') {
				tagtext = "["+mnm_shortcodeid + ' ';
				jQuery.each(shortcodes[mnm_shortcodeid]['atts'], function(index, item) {
						tagtext += index + '="' + jQuery('#shortcode_att_'+index).val() + '" ';
				});

				tagtext = tagtext.trim() + "]";
				if (!shortcodes[mnm_shortcodeid]['self-closing']) {
						tagtext += jQuery('#shortcode_content').val() + "[/" + mnm_shortcodeid + "]";
				}
		} else {
				tagtext="["+mnm_shortcodeid + "]Insert your content here[/" + mnm_shortcodeid + "]";
		}

		if(window.tinyMCE) {
				if (window.tinyMCE.execInstanceCommand === undefined) {
						// tinyMCE 4
						tinyMCEPopup.editor.insertContent(tagtext);
				} else {
						// tinyMCE 3
						window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
				}
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}

// document ready
jQuery(function($) {
		$('#mnmshortcode_panel').append('<table id="mnmshortcode_atts" border="0" cellpadding="4" cellspacing="0"></table>');
		tinyMCEPopup.resizeToInnerSize();
		$('#mnmshortcode_tag').change(function() {
				var mnm_shortcodeid = $(this).val();
				var $atts_table = $('#mnmshortcode_atts');
				$atts_table.empty();
				// build form
				if (typeof shortcodes[mnm_shortcodeid] != 'undefined') {
						var html = '';

						if (shortcodes[mnm_shortcodeid]['description']) {
								html += '<tr><td class="mnmshortcode_description" colspan="2">'+shortcodes[mnm_shortcodeid]['description']+'</td></tr>';
						}
						$.each(shortcodes[mnm_shortcodeid]['atts'], function(index, item) {
								html += '<tr class="mnmshortcode_att_name"><td>'+index+'</td><td><input type="text" name="shortcode_att_'+index+'" id="shortcode_att_'+index+'" value="'+item+'" /></td></tr>';
						});

						if (!shortcodes[mnm_shortcodeid]['self-closing']) {
								if (shortcodes[mnm_shortcodeid]['content_field'] == undefined) {
										shortcodes[mnm_shortcodeid]['content_field'] = 'input';
								}
								switch (shortcodes[mnm_shortcodeid]['content_field']) {
										case 'input':
												html += '<tr class="mnmshortcode_content"><td>Content</td><td><input type="text" name="shortcode_content" id="shortcode_content" value="'+shortcodes[mnm_shortcodeid]['content']+'" /></td></tr>';
												break;

										case 'textarea':
												html += '<tr class="mnmshortcode_content"><td>Content</td><td><textarea name="shortcode_content" id="shortcode_content">'+shortcodes[mnm_shortcodeid]['content']+'</textarea></td></tr>';
												break;

										default:
												html += '<tr class="mnmshortcode_content"><td>Content</td><td><input type="text" name="shortcode_content" id="shortcode_content" value="'+shortcodes[mnm_shortcodeid]['content']+'" /></td></tr>';
												break;
								}

						}
						$atts_table.append(html);
						$('.mnmshortcode_att_name input, .mnmshortcode_content input').each(function() {
								var $this = $(this);
								$this.data('defaultVal', $this.val())
								.css('color', '#777777')
								.focus(function() {
										if ($this.val() == $this.data('defaultVal')) {
												$this.val('').css('color', '#000000');
										}
								});
						});
				}
				tinyMCEPopup.execCommand( 'mcewpspanel_resize', false, { height : $('#mnmshortcode_form').height() } );
				tinyMCEPopup.resizeToInnerSize();
		});

		// Resize onLoad
		tinyMCEPopup.execCommand( 'mcewpspanel_resize', false, { height : 50 } );
		tinyMCEPopup.resizeToInnerSize();
});
