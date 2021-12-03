jQuery(document).ready(function($) {
	$('#mtscontact_form').submit(function(e) {
		e.preventDefault();
		var $form = $(this);
		if($form.find('#mtscontact_consent').length > 0 && $form.find('#mtscontact_consent:checked').length == 0) {
			alert('Pleae check the consent checkbox if you want to proceed.');
			$form.find('#mtscontact_consent').focus();
			return false;
		}
		$form.addClass('loading');
		$.post(mtscontact.ajaxurl, $form.serialize(), function(data) {
			$form.removeClass('loading');
			if (data == 'success') {
				$form.remove();
				$('#mtscontact_success').show();
			} else {
				$('.mtscontact_error').remove();
				$.each(data, function(i, v) {
					if ($('#mtscontact_'+i).length) {
						$('#mtscontact_'+i).after('<div class="mtscontact_error">'+v+'</div>');
					} else {
						$form.prepend('<div class="mtscontact_error">'+v+'</div>');
					}
				});
			}
		}, 'json');
	});
});