jQuery(document).ready(function(){
	jQuery('.buttonset').buttonset();
});
jQuery(document).ready(function($){
	$('.buttonset-tabs').each(function(){
		var $this = $(this),
			checkedID = $this.find('input[type=radio]:checked').attr('id'),
			activeTabID = '#'+checkedID+'_tab';

		$this.closest('.bg-opt-wrapper').find( activeTabID ).addClass('active-tab');
	});

	$('.buttonset-tab').on( 'click', function() {
		var $this = $(this),
			clickedInputID = $this.prev().attr('id'),
			clickedTabID = '#'+clickedInputID+'_tab';

		$this.closest('.bg-opt-wrapper').find('.active-tab').removeClass('active-tab');
		$this.closest('.bg-opt-wrapper').find( clickedTabID ).addClass('active-tab');
	});
});