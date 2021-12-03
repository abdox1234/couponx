jQuery(document).ready(function() {
	function isScrolledTo(elem,top) {
		var docViewTop = jQuery(window).scrollTop(); //num of pixels hidden above current screen
		var docViewBottom = docViewTop + jQuery(window).height();

		var elemTop = jQuery(elem).offset().top - top; //num of pixels above the elem
		var elemBottom = elemTop + jQuery(elem).height();

		return ((elemTop <= docViewTop));
	}

	function stickThatMenu(sticky,catcher,top) {
		if(isScrolledTo(sticky,top)) {
			sticky.addClass('sticky-navigation-active');
			catcher.height(sticky.height());
		} 
		var stopHeight = catcher.offset().top;
		if ( stopHeight > sticky.offset().top) {
			sticky.removeClass('sticky-navigation-active');
			catcher.height(0);
		}
	}

	var catcher = jQuery('#catcher'),
		sticky  = jQuery('.sticky-navigation'),
		bodyTop = jQuery('body').offset().top;

	if ( sticky.length ) {
	
		jQuery(window).scroll(function() {
			stickThatMenu(sticky,catcher,bodyTop);
		});
		jQuery(window).resize(function() {
			stickThatMenu(sticky,catcher,bodyTop);
		});
	}
});