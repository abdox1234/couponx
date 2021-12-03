(function( $ ) {
	$(function() {

	function couponIconSelect() {
		$('#widgets-right select.coupon-iconselect').each(function(){
			$(this).select2({
				formatResult: function(state) {
					if (!state.id) return state.text; // optgroup
					return '<i class="fa fa-' + state.id + '"></i>&nbsp;&nbsp;' + state.text;
				},
				formatSelection: function(state) {
					if (!state.id) return state.text; // optgroup
					return '<i class="fa fa-' + state.id + '"></i>&nbsp;&nbsp;' + state.text;
				},
				escapeMarkup: function(m) { return m; }
			});
		});
	}

	couponIconSelect();

	$(document).on('widget-added widget-updated', function(e) {
		couponIconSelect();
	});

	});
})( jQuery );