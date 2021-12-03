jQuery(document).ready(function(){
	jQuery('.nhpopts-iconselect').select2({
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