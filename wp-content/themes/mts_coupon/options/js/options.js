var changeWarn = false;
jQuery(document).ready(function($){
	var History = window.History;
	function loadTabs(){
		jQuery('.nhp-opts-group-tab').hide();
		jQuery('.nhp-opts-group-tab-link-li').removeClass('active');
		if(jQuery('#last_tab').val() == ''){
			jQuery('.nhp-opts-group-tab:first').slideDown('fast');
			jQuery('#nhp-opts-group-menu li:first').addClass('active');
		}else{
			tabid = jQuery('#last_tab').val();
			jQuery('#'+tabid+'_section_group').slideDown('fast');
			jQuery('#'+tabid+'_section_group_li').addClass('active');
			if (jQuery('#'+tabid+'_section_group_li').closest('#nhp-opts-homepage-accordion').length)
				jQuery('#nhp-opts-homepage-accordion').show();
			if (jQuery('#'+tabid+'_section_group_li').closest('#nhp-opts-blog-accordion').length)
				jQuery('#nhp-opts-blog-accordion').show();
			if (jQuery('#'+tabid+'_section_group_li').closest('#nhp-opts-affiliates-accordion').length)
				jQuery('#nhp-opts-affiliates-accordion').show();
		}
	}
	jQuery('.nhp-opts-group-tab-link-a').click(function(){
		clear_search();
		if (this.id == 'accordion_section_group_li_a') {
		   jQuery('#nhp-opts-homepage-accordion').slideToggle();
		   return false;
		}
		if (this.id == 'accordion_section_group_li_a_2') {
		   jQuery('#nhp-opts-blog-accordion').slideToggle();
		   return false;
		}
		if (this.id == 'affiliate_section_group_li_a') {
		   	jQuery('#nhp-opts-affiliates-accordion').slideToggle();
		   	return false;
		}
		var $this = jQuery(this);
		if ($this.parent().hasClass('active'))
			return false;
		var relid = jQuery(this).attr('data-rel');
		
		jQuery('.nhp-opts-group-tab').hide();
		jQuery('#'+relid+'_section_group').fadeIn(400);

		jQuery('.nhp-opts-group-tab-link-li').removeClass('active');
		$this.parent().addClass('active');

		//jQuery("html, body").animate({ scrollTop: jQuery('#nhp-opts-header').offset().top - 48 }, 500);

		History.pushState( {tab:relid}, document.title, "themes.php?page=theme_options&tab="+relid );

		return false;
	});

	History.Adapter.bind( window,'load statechange', function( event ){
		var State = History.getState();
		var tab = State.data.tab;

		if ( typeof tab !== 'undefined' ) {
			jQuery('#'+tab+'_section_group_li_a').click();
		} else {
			loadTabs();
		}
	});
	jQuery('#nhp-opts-form-wrapper').submit(function(){
		var currentTabID = jQuery(this).find('#nhp-opts-group-menu li.active a').attr('data-rel');
		jQuery('#last_tab').val(currentTabID);
	});
	

	if(jQuery('#nhp-opts-save').is(':visible')){
		jQuery('#nhp-opts-save').delay(4000).slideUp('slow');
	}
	function addTypographyData(container, form) {
		var i = 0;
		container.find(".collections .collection").each(function() {
			form.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][preview_text]', jQuery(this).find(".preview_text").val())
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][preview_color]', jQuery(this).find(".preview_color li.current a").attr("class"))
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][font_family]', jQuery(this).find(".font_family").select2('val'))
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][font_variant]', jQuery(this).find(".font_variant").select2('val'))
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][font_size]', jQuery(this).find(".font_size").select2('val'))
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][font_color]', jQuery(this).find(".font_color").val())
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][css_selectors]', jQuery(this).find(".css_selectors").val())
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][additional_css]', jQuery(this).find(".additional_css").val())
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][backup_font]', jQuery(this).find(".backup_font").select2('val'))
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][default]', jQuery(this).attr("data-default"))
				.addHiddenField(nhpopts.opt_name+'[google_typography_collections]['+i+'][collection_title]', jQuery(this).find(".collection_title").val());
			
			i++;
		});
	}
	
	if(jQuery('#nhp-opts-imported').is(':visible')){
		jQuery('#nhp-opts-imported').delay(4000).slideUp('slow');
	}
	
	jQuery('#nhp-opts-footer').find('#savechanges').click(function(e) {
		// add typography data via hidden fields before submitting
		if (typography_isloaded) {
			addTypographyData(jQuery('#google_typography'),jQuery('#nhp-opts-form-wrapper'));
		}
		// AJAX save
		/*
		jQuery('#savechanges').prop('disabled', true).after('<div class="spinner" id="ajax-saving"></div>');
		changeWarn = false;
		var $form = jQuery('#nhp-opts-form-wrapper');
		jQuery.post( $form.attr('action'), $form.serialize() ).done(function() {
			jQuery('#ajax-saving').remove();
			jQuery('#savechanges').prop('disabled', false).after('<div id="ajax-saved">Settings saved!</div>');
			setTimeout(function() { jQuery('#ajax-saved').fadeOut('slow', function() { jQuery('#ajax-saved').remove(); }); }, 2000);
		});
		return false;
		*/
		// AJAX save end
	});

	jQuery('input, textarea, select').change(function(){
		if (!changeWarn) {
		  //jQuery('#nhp-opts-save-warn').slideDown('slow');
		  changeWarn = true;
		}
	});

	jQuery('#nhp-opts-form-wrapper').submit(function() {
		changeWarn = false;
	});

	window.onbeforeunload = confirmExit;
	function confirmExit() {
		if (changeWarn) {
			return nhpopts.leave_page_confirm;
		}
	}
	
	jQuery('#nhp-opts-import-code-button').click(function(e){
		e.preventDefault();
		jQuery('#nhp-opts-import-code-wrapper').toggle().find('#import-code-value').val('');
	});

	jQuery('#nhp-opts-export-code-copy').click(function(e){
		e.preventDefault();
		jQuery('#nhp-opts-export-code').toggle().select();
	});
	
	// Presets
	function scrollImportLogToBottom(){
		var element = document.getElementById("importing-modal-content");
		element.scrollTop = element.scrollHeight;
	}
	jQuery('#presets .preset .import-demo-button, #presets .preset .import-demo-widgets-button, #presets .preset .import-demo-options-button').on('click', function(e) {
		e.preventDefault();

		var $this = jQuery(this);
		var $parent = $this.closest('.preset');
		var confirmText = nhpopts.import_opt_confirm;
		if ( $this.hasClass('import-demo-button') ) {
			confirmText = nhpopts.import_all_confirm;
		}
		if ( $this.hasClass('import-demo-widgets-button') ) {
			confirmText = nhpopts.import_widget_confirm;
		}

		var result = confirm( confirmText );
		if ( result ) {

			var data = {};
			data.action = "mts_install_demo";
			data.demo_import_id = $parent.attr("data-demo-id");
			data.nonce = $parent.attr("data-nonce");
			data.demo_import_options = '1';
			data.demo_import_content = '0';
			data.demo_import_widgets = '0';

			if ( $this.hasClass('import-demo-button') ) {
				data.demo_import_content = '1';
				data.demo_import_widgets = '1';
			}

			if ( $this.hasClass('import-demo-widgets-button') ) {
				data.demo_import_widgets = '1';
			}

			$this.magnificPopup({
				items: {
					src: '#importing-overlay',
					type: 'inline'
				},
				modal: true
			}).magnificPopup('open');

			var last_response_len = false;
			jQuery.ajax( ajaxurl, {
				data: data,
				xhrFields: {
					onprogress: function(e) {

						var this_response, response = e.currentTarget.response;
						if(last_response_len === false) {

							this_response = response;
							last_response_len = response.length;

						} else {

							this_response = response.substring(last_response_len);
							last_response_len = response.length;
						}

						jQuery('#importing-modal-content').append(this_response);
						scrollImportLogToBottom();
					}
				}
			})
			.done(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.import_done);
				jQuery('#importing-modal-footer-info').text(nhpopts.import_done);
				jQuery('#importing-modal-footer-button').show();
			})
			.fail(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.import_fail);
				jQuery('#importing-modal-footer-info').text(nhpopts.import_fail);
				jQuery('#importing-modal-footer-button').show();
			});
		}

		return false;
	});

	jQuery('.remove-demo-button').on('click', function(e) {
		e.preventDefault();

		var result = confirm( nhpopts.remove_all_confirm );
		if ( result ) {

			var $this = jQuery(this);
			var data = {};
			data.action = "mts_install_demo";
			data.mts_remove_demos = "1";
			data.nonce = $this.attr("data-nonce");

			$this.magnificPopup({
				items: {
					src: '#importing-overlay',
					type: 'inline'
				},
				modal: true
			}).magnificPopup('open');

			var last_response_len = false;
			jQuery.ajax( ajaxurl, {
				data: data,
				xhrFields: {
					onprogress: function(e) {

						var this_response, response = e.currentTarget.response;
						if(last_response_len === false) {

							this_response = response;
							last_response_len = response.length;

						} else {

							this_response = response.substring(last_response_len);
							last_response_len = response.length;
						}

						jQuery('#importing-modal-content').append(this_response);
						scrollImportLogToBottom();
					}
				}
			})
			.done(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.remove_done);
				jQuery('#importing-modal-footer-info').text(nhpopts.remove_done);
				jQuery('#importing-modal-footer-button').show();
			})
			.fail(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.remove_fail);
				jQuery('#importing-modal-footer-info').text(nhpopts.remove_fail);
				jQuery('#importing-modal-footer-button').show();
			});
		}

		return false;
	});

	function mtsRemoveURLParameter(url, parameter) {

		//prefer to use l.search if you have a location/link object
		var urlparts= url.split('?');
		if ( urlparts.length >= 2 ) {

			var prefix= encodeURIComponent(parameter)+'=';
			var pars= urlparts[1].split(/[&;]/g);

			//reverse iteration as may be destructive
			for (var i= pars.length; i-- > 0;) {
				//idiom for string.startsWith
				if (pars[i].lastIndexOf(prefix, 0) !== -1) {
					pars.splice(i, 1);
				}
			}

			url= urlparts[0]+'?'+pars.join('&');
			return url;

		} else {

			return url;
		}
	}
	
	jQuery('#importing-modal-footer-button').on('click', function(e) {
		e.preventDefault();
		jQuery(this).prop('disabled', true ).text(nhpopts.reloading_page);
		var a = mtsRemoveURLParameter( window.location.href , 'tab' );
		window.location.href = a;
	});

	
	// Confirm import
	jQuery('#nhp-opts-import').click(function() {
		return confirm(nhpopts.import_confirm);
	});
	
	// Confirm reset
	jQuery('input[name="'+nhpopts.opt_name+'[defaults]"]').click(function() {
		return confirm(nhpopts.reset_confirm);
	});
	
	// Disallow submission by enter key
	jQuery('#nhp-opts-form-wrapper').find('input').keydown(function(event){
		if ( event.keyCode == 13 ){
			event.preventDefault();
		}
	});
	
	// Floating footer
	var $footer = jQuery('#nhp-opts-footer');
	var $bottom = jQuery('#nhp-opts-bottom');
	
	
	$footer.addClass('floating');
	jQuery(document).on('scroll', function(){
		if ($bottom.isOnScreen()) {
			$footer.removeClass('floating');
		} else {
			$footer.addClass('floating');
		}
	});
	if ($bottom.isOnScreen()) {
		$footer.removeClass('floating');
	}
	
	// Needs JS sizing when position:fixed
	var footer_padding = $footer.innerWidth() - $footer.width();
	function resizeFloatingElements() {
		var w = jQuery('#nhp-opts-form-wrapper').width();
		$footer.width(w - footer_padding);
	}
	resizeFloatingElements();
	
	var resizeTimer;
	jQuery(window).resize(function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(resizeFloatingElements, 100);
	});

	// Child theme creator
	jQuery('#nhp-opts-child-button').on('click', function(e) {
		e.preventDefault();
		var elem		 = jQuery(this),
			childName	= jQuery('#nhp-opts-child-name'),
			childNameVal = childName.val();

		if ( '' === childNameVal ) {
			alert( nhpopts.child_theme_name_empty );
		} else {
			jQuery.ajax({
				url: ajaxurl, 
				method: 'post',
				data: {
                    'action': 'mts_child_theme',
                    'child_name': childNameVal,
                    '_ajax_nonce': $('#mts_child_theme_nonce').val()
                },
				beforeSend: function() {
					elem.prop('disabled', true);
					childName.prop('disabled', true);
				},
				success: function(data) {
					elem.prop('disabled', false);
					childName.val('').prop('disabled', false);
					mtsUpdateChildThemesList();
				}
			});
		}
	});
	// Refresh the existing child themes list
	function mtsUpdateChildThemesList() {
        jQuery.post( ajaxurl, { action: 'mts_list_child_themes', '_ajax_nonce': $('#mts_child_theme_nonce').val() }, function( response ) {
            if ( response ) {
                jQuery('#child-theme-list-wrap').html( response );
            }
        });
    }
	
	$('#search-theme-options').keydown(function(e) {
		if (e.keyCode == 27) {
			clear_search( true );
			$(this).val('').blur();
			return false;
		}
	}).keyup(function (e) {
		var value = $(this).val();
		if ( value == '' ) {
			clear_search( true );
			return false;
		}
		if (e.keyCode == 13) {
			e.preventDefault();
			search_options( value );
			return false;
		}
		// search_options( value );
	}).blur(function(event) {
		var value = $(this).val();
		if ( value == '' ) {
			$(this).closest('div').removeClass('active');
		}
	});
	$('.options-search-link').click(function(event) {
		event.preventDefault();
		if ($(this).closest('div').hasClass('active')) {
			var query = $(this).closest('div').addClass('active').find('input').val();
			search_options( query );
		}
		$(this).closest('div').addClass('active').find('input').focus();
	});
	$('.theme-options-clear-search').click(function(event) {
		event.preventDefault();
		clear_search( true );
		$(this).siblings('input').val('').trigger('blur');
	});
	$('#nhp-opts-header .docsupport').click(function(event){
		event.preventDefault();
		$('.nhp-opts-group-tab-link-a[data-rel=support]').trigger('click');
	});

	// Reverse jQuery plugin
	jQuery.fn.reverse = [].reverse;

	var searched = false;
	function search_options( query ) {
		searched = true;
		// de-select tabs
		$('.nhp-opts-group-tab-link-li.active').removeClass('active');
		$('.nhp-opts-info-field').hide();
		query = query.toLowerCase();
		var noresults = true;
		$('#nhp-opts-main').children('div').hide().each(function(index, el) {
			if (this.id == 'typography_default_section_group') {
				return true;
			}
			var $contents = $(this);

			//var $title = $contents.find('h2');
			//var $desc = $contents.find('.nhp-opts-section-desc');
			
			$contents.children('table').children('tbody').children('tr').each(function(index, el) {
				var $row = $(this);
				$row.hide();
				if ($row.find('th').text().toLowerCase().indexOf( query ) !== -1) {
					$contents.show();
					$row.show();
					noresults = false;
				}

			}).reverse().each(function(index, el) {
				if ($(this).is(':visible') && $(this).find('.buttonset-hide').length) {
					$(this).find('#nhp-opts-button-show-below').each(function(event){
						if (jQuery(this).hasClass('ui-state-active')) {
							var num = jQuery(this).parent().data('hide');
							jQuery(this).closest('tr').nextAll('tr:lt('+num+')').show();
						} else {
							var num = jQuery(this).parent().data('hide');
							jQuery(this).closest('tr').nextAll('tr:lt('+num+')').hide();
						}
					});
				}
			});
		});
		if (noresults) {
			$('#options-search-no-results').show();
		}
	}

	function clear_search( clicktab ) {
		if ( ! searched ) {
			return false;
		}
		$('#nhp-opts-main').children().each(function(index, el) {
			$(this).find('tr').show().reverse().each(function(index, el) {
				if ($(this).find('.buttonset-hide').length) {
					$(this).find('#nhp-opts-button-show-below').each(function(event){
						if (jQuery(this).hasClass('ui-state-active')) {
							var num = jQuery(this).parent().data('hide');
							jQuery(this).closest('tr').nextAll('tr:lt('+num+')').show();
						} else {
							var num = jQuery(this).parent().data('hide');
							jQuery(this).closest('tr').nextAll('tr:lt('+num+')').hide();
						}
					});
				}
			});
		});
		$('.nhp-opts-info-field').show();
		if ( clicktab ) {
			$('#nhp-opts-main').children().hide().first().show();
			$('#0_section_group_li').addClass('active');
		} else {
			$('#search-theme-options').val('').parent().removeClass('active');
		}
		searched = false;
	}

	jQuery('#import-csv').on('click', function(e) {
		e.preventDefault();
		var formData = new FormData();
		var file_obj = jQuery('#csv_file');
		var files = file_obj[0].files[0];
		if( files != undefined ) {
			jQuery('.csv-import-container').addClass('in');
			var import_action = jQuery("input[name='csv_import_choice']:checked"). val();
			formData.append("files", files);
			formData.append("action", 'coupon_csv_import');
			formData.append("csv_import_choice", import_action);

		   jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: formData,
				cache: false,
				contentType:false,
				processData:false,
				success: function(data) {
					jQuery('.csv-import-container').removeClass('in');
					jQuery('#csv_file').val('');
					alert('Coupons imported!');
				}
			});
		} else {
			alert('Please add CSV file.');
		}

		return false;
	});

	jQuery('#mts_linkshare_network').on('change', function(e){
		var id = jQuery(this).val();
	 	jQuery.ajax({
			url: ajaxurl, 
			method: 'post',
			data: {
				'action' : 'mts_get_linksharedata',
				'network' : id
			},
			success: function(data) {
				jQuery('#mts_linkshare_category').html(data.category);
				jQuery('#mts_linkshare_promotiontype').html(data.promotion);
			}
		});
	});

});

jQuery(window).on('load', function() {
	jQuery('#savechanges').prop('disabled', false);
});

jQuery.fn.isOnScreen = function(){

	var win = jQuery(window);

	var viewport = {
		top : win.scrollTop(),
		left : win.scrollLeft()
	};
	viewport.right = viewport.left + win.width();
	viewport.bottom = viewport.top + win.height();

	var bounds = this.offset();
	bounds.right = bounds.left + this.outerWidth();
	bounds.bottom = bounds.top + this.outerHeight();

	return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

};

jQuery.fn.addHiddenField = function(name, value) {
	this.each(function () {
		var elem_id = name.replace(/\W/g, '-').replace(/--+/g, '-').replace(/(^-|-$)/, '');
		if (jQuery('#'+elem_id).length) {
			// elem exists, change value
			jQuery('#'+elem_id).val(value);
		} else {
			// elem doesn't exist, create
			var input = jQuery("<input>").attr("type", "hidden").attr("id", elem_id).attr("name", name).val(value);
			jQuery(this).append(jQuery(input));
		}
		
	});
	return this;
};

var fnDelay = (function(){
  var timer = 0;
  return function(callback, ms){
	clearTimeout (timer);
	timer = setTimeout(callback, ms);
  };
})();
