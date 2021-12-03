jQuery(document).ready(function($) {
	/**	Sorter (Layout Manager) */
	$('.nhp-opts-sorter-alt').each(function() {
		var id = $(this).attr('id');
		$('#' + id).find('.sortlist-alt').sortable({
			items: '> .sortee-alt',
			handle: '.sortee-header',
			placeholder: 'placeholder',
			connectWith: '.sortlist_' + id,
			opacity: 0.6,
			update: function() {
				$(this).find('.position-alt').each(function() {
					var listID = $(this).parent().attr('id');
					var parentID = $(this).parent().parent().attr('id');
					parentID = parentID.replace(id + '_', '');
					var optionID = $(this).parent().parent().parent().attr('id');
					var fieldName = $(this).attr('name').replace(/\[.*\]/, '');
					$(this).prop("name", fieldName + '[' + optionID + '][' + parentID + '][' + listID + ']');
				});
			},
			start: function(event, ui) {
				if ( ui.item.find('.sortee-content').css('display') === 'block' ) {
					ui.item.css('height', '40px');
				}
				$(this).sortable('refreshPositions');
			}
		});
	});

	$('.sortee-has-content .sortee-header').prepend( "<span class='ui-icon ui-icon-plus sortee-toggle'></span>");
	
	$('.sortee-has-content .sortee-header').on('click', function () {
		$(this).next('.sortee-content').slideToggle();
		$(this).find('.sortee-toggle').toggleClass( "ui-icon-minus ui-icon-plus" );
	});

	$('.nhp-opts-sortee-toggle-close').on('click', function () {
		$(this).closest('.sortee-alt').find('.sortee-header').click();
	});
});