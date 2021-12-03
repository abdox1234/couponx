jQuery(document).ready(function($){
	jQuery('.nhpopts-cats_multi_select').select2({
		
	});
});
jQuery(document).on("click",".select_all_cats",function(event){
	var $this = jQuery(event.target);
	var $select = $this.closest('td').find('select');
	if ( ! $this.data('checked') ) {
		$select.find("option").prop("selected","selected");// Select All Options
		$select.trigger("change");// Trigger change select2
		$this.val('Clear all');
		$this.data('checked', 1);
	} else {
		$select.find("option").removeAttr("selected");
		$select.trigger("change");// Trigger change select2
		$this.val('Select all');
		$this.data('checked', 0);
	}
});