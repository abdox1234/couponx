jQuery(document).ready(function($){
	// prevent accordion open when draggin in FF
	var dragged = false;
	$('div[id=nhp-opts-groups-accordion] > div > h3').click(function(event) {
		if (dragged) {
			event.stopImmediatePropagation();
			event.preventDefault();
			dragged = false;
		}
	});
	
	$('.nhp-opts-groups-accordion-group')
		.each(function () {
			if (!$(this).is('.nhp-opts-dummy')) {
				var $header = $('h3 > span', this);
				var groupHeader = '';
				// Get group title from first field value
				// Or first SELECT OPTION text
				var $first_field = $('textarea, input[type=text], select', this).first();
				if (typeof $first_field == 'undefined') {
					groupHeader = '';
				} else if ($first_field.is('select')) {
					groupHeader = $first_field.find('option:selected').text();
				} else {
					groupHeader = $first_field.val();
				}
				
				if (groupHeader.length > 0) {
					groupHeader = groupHeader.substring(0,32);
				} else {
					// First field empty - get dummy title
					groupHeader = $(this).siblings('.nhp-opts-dummy').find('.nhp-opts-groups-header').text();
				}
				
				$header.text(groupHeader);
			}
		});
	
	$("div[id=nhp-opts-groups-accordion]")
		.accordion({
			header: "> div > h3",
			collapsible: true,
			active: false,
			heightStyle: "content",
			icons: {
				"header": "ui-icon-plus",
				"activeHeader": "ui-icon-minus"
			},
			activate: function(event, ui) {
				// Refresh title
				var $first_field = ui.oldPanel.find('textarea, input[type=text], select').first();
				if (typeof $first_field == 'undefined') {
					groupHeader = '';
				} else if ($first_field.is('select')) {
					groupHeader = $first_field.find('option:selected').text();
				} else {
					groupHeader = $first_field.val();
				}
				if (typeof groupHeader != 'undefined' && groupHeader.length > 0) {					
					groupHeader = groupHeader.substring(0,32);
					ui.oldHeader.find('.nhp-opts-groups-header').text(groupHeader);					   
				}
							 
			}
		})
		.sortable({
			axis: "y",
			handle: "h3",
			stop: function (event, ui) {
				// IE doesn't register the blur when sorting
				// so trigger focusout handlers to remove .ui-state-focus
				ui.item.children("h3").triggerHandler("focusout");
				var inputs = $('input.group-sort');
				inputs.each(function(idx) {
					$(this).val(idx);
				});
				
				// prevent accordion open when dragging in FF
				dragged = true;
				setTimeout(function() {
					dragged = false;
				}, 100);
			}
		});
		
		
		$('.nhp-opts-groups-remove').on('click', function () {
			//redux_change($(this));
			$(this).parent().find('input[type="text"]').val('');
			$(this).parent().find('input[type="hidden"]').val('');
			$(this).parent().parent().slideUp('medium', function () {
				$(this).remove();
			});
		});
		$('.nhp-opts-groups-close').on('click', function () {
			var $group = $(this).closest('.nhp-opts-groups-accordion-group');
			$group.find('h3').click();//.find('.nhp-opts-groups-header').text(title);
		});
		
		
		$('.nhp-opts-groups-add').click(function () {
			var newGroup = $(this).prev().find('.nhp-opts-dummy').clone(true).show();
			var groupCounter = $(this).parent().find('.nhp-opts-dummy-group-count');
			// Count # of groups
			var groupCount = groupCounter.val();
			// Update the groupCounter
			groupCounter.val(parseInt(groupCount)+1 );
			// REMOVE var groupCount1 = groupCount*1 + 1;
			

			//$(newGroup).find('h3').text('').append('<span class="redux-groups-header">New Group</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>');
			$(this).prev().append(newGroup);

			// Remove dummy classes from newGroup
			$(newGroup).removeClass("nhp-opts-dummy");

			// Deal with radio input
			$(newGroup).find('input[type="radio"]').each(function(index, el) {
				var $this = $(this);
				var attr_name = $this.data('name');
				var attr_id = $this.attr('id');

				if (typeof attr_id !== 'undefined' && attr_id !== false) {
					$this.attr("id", $this.attr("id").replace("dummy", groupCount) );
				}
				if (typeof attr_name !== 'undefined' && attr_name !== false) {
					$this.attr("name", attr_name.replace("@", groupCount) );
				}
				var label = $this.parent('label');
				label.attr('for', label.attr('for').replace("dummy", groupCount) );
			});

			// Other inputs
			$(newGroup).find('input[type="text"], input[type="number"], input[type="hidden"], textarea , select, input[type="checkbox"]').each(function(){
				var $this = $(this);
				var attr_name = $this.data('name');
				var attr_id = $this.attr('id');
				var std_val = $this.data('std');
				// For some browsers, `attr` is undefined; for others,
				// `attr` is false.  Check for both.
				if (typeof attr_id !== 'undefined' && attr_id !== false) {
					$this.attr("id", $this.attr("id").replace("@", groupCount) );
				}
				if (typeof attr_name !== 'undefined' && attr_name !== false) {
					$this.attr("name", attr_name.replace("@", groupCount) );
				}

				if($this.prop("tagName") == 'SELECT') {
					//we clean select2 first
					$(newGroup).find('.select2-container').remove();
					$(newGroup).find('select').removeClass('select2-offscreen');
					
					// std
					$(newGroup).find('option[value="'+std_val+'"]').prop('selected', true);
				} else if ( $this.is(':checkbox') ) {
					$this.prop( 'checked', '1' == std_val );
				} else {
					$this.val(std_val);
				}
				if ($this.hasClass('popup-colorpicker')) {
					$this.wpColorPicker();
				}
				if ($this.hasClass('nhpopts-iconselect')) {
					$this.show().select2({
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
				}
				if ($this.hasClass('nhpopts-cats_multi_select')) {
					$this.select2();
				}
				
				if ($this.hasClass('group-sort')){
					$this.val(groupCount);
				}
			});
		   $(newGroup).find('h3').click();
		});
		
		// Fix "upload" field type issue
		$('.nhp-opts-dummy .nhp-opts-upload-remove').click();
});