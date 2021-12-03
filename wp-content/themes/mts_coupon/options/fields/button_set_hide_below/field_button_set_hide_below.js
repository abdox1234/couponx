jQuery(document).ready(function(){
	jQuery('.buttonset').buttonset();
});

jQuery(document).ready(function(){
	jQuery('.form-table').delegate("#nhp-opts-button-hide-below", "click", function(){
		var num = jQuery(this).parent().data('hide');
		jQuery(this).closest('tr').nextAll('tr:lt('+num+')').hide('fast');
	});
	jQuery('.form-table').delegate("#nhp-opts-button-show-below", "click", function(){
		var num = jQuery(this).parent().data('hide');
		jQuery(this).closest('tr').nextAll('tr:lt('+num+')').show('fast');
	});
	
	jQuery('.buttonset-hide #nhp-opts-button-show-below').each(function(){
		if(!jQuery(this).hasClass('ui-state-active')){
			var num = jQuery(this).parent().data('hide');
			jQuery(this).closest('tr').nextAll('tr:lt('+num+')').hide('fast');
		}
	});
	jQuery('.buttonset-hide #nhp-opts-button-show-below').each(function(event){
		
		if(jQuery(this).hasClass('ui-state-active')){
			  var num = jQuery(this).parent().data('hide');
			jQuery(this).closest('tr').nextAll('tr:lt('+num+')').show('fast');
		}
		
	});
	
	jQuery('.form-table').delegate(".ui-buttonset .ui-button", "click", function(event){
		//jQuery("html, body").animate({ scrollTop: jQuery(this).parent(".ui-buttonset").offset().top - 100 }, 600);
	});

});