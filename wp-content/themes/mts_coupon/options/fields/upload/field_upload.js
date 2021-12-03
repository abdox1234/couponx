jQuery(function($){

	/*
	 *
	 * NHP_Options_upload function
	 * Adds media upload functionality to the page
	 *
	 */
	 
	var file_frame, image_data, relid, $context;

	file_frame = wp.media.frames.file_frame = wp.media({
		title: nhp_upload.title,
		button: { text: nhp_upload.buttonText },
		multiple: false
	});

	file_frame.on('select', function() {
		var $field = $('#'+relid, $context);
		var $img = $field.next();
		image_data = file_frame.state().get( 'selection' ).first().toJSON();
		$field.val($img.data('return') === 'id' ? image_data.id : image_data.url);
		$img.attr('src', image_data.url);
		$field.next().next().hide();
		$field.next().next().next().show();
	});


	$("img[src='']").attr("src", nhp_upload.url);
	
	$('#nhp-opts-form-wrapper').on('click', '.nhp-opts-upload', function(e) {
		var $self = $(this);
		e.preventDefault();
		$context = $self.parent();
		relid = $self.attr('rel-id');
		file_frame.open();
	});

	$('#nhp-opts-form-wrapper').on('click', '.nhp-opts-upload-remove', function(){
		var relid = $(this).attr('rel-id');
		$('#nhp-opts-screenshot-'+relid).attr('src', nhp_upload.url);
		$('#'+relid).val('');
		$('.nhp-opts-upload[rel-id='+relid+']').show();
		$(this).hide();
		$(this).prev().prev().attr("src", nhp_upload.url);
	});
});