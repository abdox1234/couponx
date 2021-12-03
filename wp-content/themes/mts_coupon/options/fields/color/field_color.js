jQuery(document).ready(function(){
	/*
	 * NHP_Options_color function
	 * Adds farbtastic to color elements
	 * 
	 * * Updated to use Iris color picker * *
	 * 
	 */
	jQuery('input.popup-colorpicker').each(function() {
	   if (!jQuery(this).parents('.nhp-opts-dummy').length) {
		   jQuery(this).wpColorPicker();
	   }
	});
	
});