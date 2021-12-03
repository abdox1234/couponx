jQuery(document).on('click', '.mts-notice-dismiss', function(e){
	e.preventDefault();
	jQuery(this).parent().remove();
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: {
			action: 'mts_dismiss_plugin_notice',
			dismiss: jQuery(this).data('ignore')
		}
	});
	return false;
});