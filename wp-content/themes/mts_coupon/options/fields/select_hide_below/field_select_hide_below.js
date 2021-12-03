jQuery(document).ready(function(){
	
	jQuery('.nhp-opts-select-hide-below').each(function(){
		if(jQuery('option:selected',this).attr('data-allow') == 'false'){
			jQuery(this).closest('tr').next('tr').hide();
		}
	});
	
	jQuery('.nhp-opts-select-hide-below').change(function(){
		var option = jQuery('option:selected', this),
			num = jQuery(this).data('hide');

		if(option.attr('data-allow') == 'false'){
			
			if(jQuery(this).closest('tr').nextAll('tr:lt('+num+')').is(':visible')){
				jQuery(this).closest('tr').nextAll('tr:lt('+num+')').fadeOut('slow');
			}
			
		}else{

			if(jQuery(this).closest('tr').nextAll('tr:lt('+num+')').is(':hidden')){
				jQuery(this).closest('tr').nextAll('tr:lt('+num+')').fadeIn('slow');
			}
			
		}
	});
	
});
