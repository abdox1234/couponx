var itemCount = 0;
var mtsGoogleTypography = function(container, collection, values){
	initialize = function(container, collection) {
		var container = jQuery(container);
		var collection = jQuery(collection);
		var preview = jQuery(".font_preview input", collection);
		itemCount++;
		collection.addClass('collection-'+itemCount);
		collection.data('itemIndex', itemCount);
		
		// Dropdown styles
		collection.find("select, input.font_family").each(function() {
			var css_class = 'typography_dropdown';
			if (jQuery(this).hasClass('font_family')) {
				css_class = "fontfamily_dropdown";
				var val = ''; 
				if (typeof values !== 'undefined') val = values.font_family;
				jQuery(this).val(val).select2({
					dropdownCssClass: css_class,
					placeholder: "Browse fonts",
					minimumInputLength: 0,
					ajax: {
						url: ajaxurl,
						quietMillis: 100,
						data: function (term, page) { // page is the one-based page number tracked by Select2
							var cur = jQuery(this).select2('data');
							if (cur) cur = cur.id;
							return {
								term: term, //search term
								page_limit: 25, // page size
								page: page,
								current: cur,
								action: "get_fonts",	
								_ajax_nonce: jQuery('#mts_fonts_nonce').val()
							};
						},
						results: function (data, page) {
							// notice we return the value of more so Select2 knows if more results can be loaded
							return {results: data.collections, more: data.more};
						}
					},
					formatResult: function(data) {
						var markup = '';
						var preview_class = ' no-preview';
						var style = '';
						if (data.has_preview) {
							preview_class = ' has-preview';
							style = ' style="background-image: url('+data.preview_url+');"';
						}

						markup += '<span class="'+data.css_class+preview_class+'"'+style+'>';
						markup += data.text;
						markup += '</span>'
						return markup;
					},
					formatSelection: function(data) {
						return data.text;
					},
					initSelection: function(element, callback) {
						return callback(values.stored);
					},
					escapeMarkup: function (m) { return m; }
				});
			} else if (jQuery(this).hasClass('font_variant')) {
				var $select = jQuery(this);
				if (typeof values != 'undefined' && typeof values.variants != 'undefined') {
					$select.find('option').remove();
					var variants = values.variants;
					jQuery.each(variants, function(index, val) {
						var is_selected = '';
						if (val == values.stored.selected_variant)
							is_selected = ' selected="selected"';
						$select.append('<option value="'+val+'" '+is_selected+'>'+val+'</option>');
					});
				}
				
				$select.select2({
					dropdownCssClass: css_class,
				});
			} else {
				jQuery(this).select2({
					dropdownCssClass: css_class,
				});
			}
			
		});


		// Colorpicker
		collection.find(".font_color").wpColorPicker({
			change: function(event, ui) {
				preview.css( 'color', ui.color.toString());
			}
		});
  
		// Font attributes
		collection.find(".font_family").on("change", function(e, variant) {
		
		 var google = false;
		 if (typeof e.added !== 'undefined' && e.added.googlefont)
		 	google = true;

		 
		 var autoprev = false;//jQuery('#mts_typography_autopreview').prop('checked');
		 if (typography_isloaded) autoprev = true;
		 previewFontFamily(jQuery(this).val(), collection, preview, variant, google, autoprev);

		 //if (google) {
		 //   collection.find(".backup_font+div").show();
		 //} else {
		 //   collection.find(".backup_font+div").hide();
		 //}
		});

		collection.find(".font_variant").on("change", function() { previewFontVariant(jQuery(this), preview); });
		collection.find(".font_size").change(function() { previewFontSize(jQuery(this), preview); });
		collection.find(".preview_color li a").on("click", function() { previewBackgroundColor(jQuery(this), collection); });
		collection.find('.collection_toggle_moreoptions').on('click', function(e) {
			e.preventDefault();
			collection.find('.collection_moreoptions').toggle();
		});

		collection.find('.collection_preview').on('click', function(e) {
			e.preventDefault();
			var google = false;
			if (!collection.find('.font_family').select2('val')) {
				collection.find('.font_family').select2('open');
				return true;
			}

			if (collection.find('.font_family').select2('data') && collection.find('.font_family').select2('data').googlefont)
				google = true;

			collection.find(".font_family").select2('val');
			previewFontFamily(collection.find(".font_family").select2('val'), collection, preview, collection.find(".font_variant").select2('val'), google, true);
		});
		
		collection.find(".additional_css").on("change", function() {
			var selector = '.collection-'+collection.data('itemIndex')+' .preview_text';
			collection.find('.additional-css').html(selector+' { '+jQuery(this).val()+' }');
		});
		
		// Save and delete
		// collection.find(".save_collection").on("click", function() { saveCollections(collection, container); });
		collection.find(".delete_collection").on("click", function() {
			if(confirm(googletypography.delete_confirm)) {
				collection.remove();
			}
		});
		
		collection.on("focus", "input, select, textarea", function(){ setCurrentCollection(container, collection); });
		
		collection.find(".wp-color-result").on("click", function(){ setCurrentCollection(container, collection); });
		
		if(values) {
			loadCollection(values, collection);
		}
  
	};
	
	setCurrentCollection = function(container, collection) {
		
		container.find(".collection").removeClass("current");
		
		collection.addClass("current");
		
	};

	previewFontFamily = function(font, collection, preview, variant, google, autopreview) {
		if (google) {
			getFontVariants(font, collection, variant, preview, autopreview);
		} else {
			preview.css('font-family', font).css("opacity", 1);
			var variants = collection.find("select.font_variant");
			variants.find("option").remove();
			variants
				.append('<option value="normal">normal</option>')
				.append('<option value="700">700</option>');
		}
		preview.parent().toggle(autopreview);
		collection.find('.collection_preview').toggle(!autopreview);
	};

	previewFontVariant = function(elem, preview) {
		preview.css('font-weight', jQuery(elem).val());
	};

	previewFontSize = function(elem, preview) {
		jQuery(preview).css('font-size', jQuery(elem).val());
	};

	previewBackgroundColor = function(elem, collection) {
  
		collection.find(".font_preview .preview_color li").removeClass("current");
		collection.find(".font_preview")
			.removeClass("dark light")
			.addClass(jQuery(elem).attr("class"));
			jQuery(elem).parent().addClass("current");
  
	};

	getFontVariants = function(font, collection, selected, preview, autopreview) {
		var variants = collection.find("select.font_variant");
  
		var variant_array = [];

		if (!typography_isloaded) {
			// initial setup: use saved data instead of request
			// in loadCollection
		} else {
			// request font variants
			jQuery.ajax({
				url: ajaxurl,
				data: {
					'action' : 'get_google_font_variants',
					'font_family' : font,
					'_ajax_nonce': jQuery('#mts_fonts_nonce').val()
				},
				success: function(data) {
					variants.find("option").remove();
					for (var i = 0; i < data.length; ++i) {
						var is_selected = "";
						if (selected == data[i]) {
							is_selected = "selected";
						}
						variants.append('<option value="'+data[i]+'" '+is_selected+'>'+data[i]+'</option>');
						variant_array.push(data[i]);
					}
					WebFont.load({
						google: {
							families: [font+':'+variant_array.join()]
						},
						loading: function() {
							preview.css("opacity", 0);
						},
						fontactive: function(family, desc) {
							preview.css('font-family', '"'+font+'"').css("opacity", 1);
						}
					});
					
					variants.trigger("change");
				}
			});
		}
	};

	saveCollections = function(collection, container, showLoading) {
  
		var collectionData = [];
		var i = 0;
  
		container.find(".collections .collection").each(function() {

			previewText		= jQuery(this).find(".preview_text").val();
			previewColor	= jQuery(this).find(".preview_color li.current a").attr("class");
			fontFamily		= jQuery(this).find(".font_family").val();
			fontVariant		= jQuery(this).find(".font_variant").val();
			fontSize		= jQuery(this).find(".font_size").val();
			fontColor		= jQuery(this).find(".font_color").val();
			cssSelectors	= jQuery(this).find(".css_selectors").val();
			additionalCSS	= jQuery(this).find(".additional_css").val();
			backupFont	  = jQuery(this).find(".backup_font").val();
			isDefault		= jQuery(this).attr("data-default");
	
			collectionData[i] = {
				uid: i + 1,
				preview_text: previewText,
				preview_color: previewColor,
				font_family: fontFamily,
				font_variant: fontVariant, 
				font_size: fontSize,
				font_color: fontColor,
				css_selectors: cssSelectors,
				backup_font: backupFont,
				additional_css: additionalCSS,
				default: isDefault
			};

			i++;
	
		});
		
		jQuery.ajax({
			url: ajaxurl, 
			method: 'post',
			data: {  'action' : 'save_user_fonts',  'collections' : collectionData, '_ajax_nonce': jQuery('#mts_fonts_nonce').val() },
			success: function(data) {
				
				if(showLoading != false) {
					collection.find(".save_collection").removeClass("saving").html("Save");
				}
				
			}
		});
	};
	
	loadCollection = function(values, collection) {

		collection.find(".preview_text").val(values.preview_text.replace("\\", ""));
		var title = values.preview_text.replace("\\", "");
		if (typeof values.collection_title !== 'undefined' && values.collection_title) {
			title = values.collection_title;
		}
		collection.find(".collection_title").val(title);
		
		if (values.preview_color)
			collection.find(".preview_color li a[class="+values.preview_color+"]").trigger("click");

		if(values.font_family) {
			//collection.find(".font_family option[value='"+values.font_family+"']")
			//	.attr("selected", "selected")
			//	.trigger("change", [values.font_variant]);
			collection.find(".font_family").val(values.font_family);//.trigger("change", [values.font_variant]);
		}
		
		// MTS Custom values: additional CSS and Backup font
		if (values.backup_font) {
			collection.find(".backup_font option[value='" + values.backup_font.replace(/'/g, "\\'") + "']")
				.attr("selected", "selected")
				.trigger("change");
		}
		if (values.additional_css) {
			var selector = '.collection-'+collection.data('itemIndex')+' .preview_text';
			collection.find(".additional_css").val(values.additional_css).trigger('change');
			//collection.find('.additional-css').html(selector+' { '+values.additional_css+' }');
		}
		// fontVariant		= jQuery(this).find(".font_variant").val();
		
		if (values.font_size)
			collection.find(".font_size option[value='"+values.font_size+"']")
				.attr("selected", "selected")
				.trigger("change");

		if (!typography_isloaded && typeof values.variants !== 'undefined') {
			var variant_select = collection.find("select.font_variant");
			variant_select.find("option").remove();
			jQuery.each(values.variants, function(i, val) {
				var is_selected = "";
				if (val == values.font_variant){
					is_selected = ' selected="selected"';
				}
				variant_select.append('<option value="'+val+'"'+is_selected+'>'+val+'</option>');
			});
			variant_select.trigger("change");
		} else if (values.font_variant) {
			collection.find(".font_variant option[value='"+values.font_variant+"']")
			.attr("selected", "selected")
			.trigger("change");
		}

		//collection.find(".font_variant option[value="+values.font_variant+"]")
		//	.attr("selected", "selected")
		//	.trigger("change");
		collection.find(".font_color")
			.val(values.font_color)
			.wpColorPicker('color', values.font_color);
		collection.find(".css_selectors").val(values.css_selectors);
		
		collection.attr("data-default", values.default);
		
	};

	initialize(container, collection);

}

var typography_isloaded = false;
// jQuery ready
jQuery(document).ready(function($) {

	var container = $("#google_typography");
	var template = container.find(".template").html();
	
	
	function initTypography() {
		typography_isloaded = true;
		$.ajax({
			url: ajaxurl, 
			data: {  'action' : 'get_user_fonts', '_ajax_nonce': jQuery('#mts_fonts_nonce').val() },
			beforeSend: function() {
				container.find(".loading").show();
				container.find(".collections").hide();
			},
			success: function(data) {
				if(data.collections.length == 0 || data.collections == false) {
					container.find(".loading").fadeOut("normal", function() {
						
					});
				} else {
					container.find(".loading").fadeOut("normal", function() {
						container.find(".collections").fadeIn();
					});
					for (var i=0;i<data.collections.length;i++) {
						var $new_coll = $(template).appendTo(".collections");
						new mtsGoogleTypography(container, $new_coll, data.collections[i], true);
					}
					
				}
			}
		});
	}
	
	// Load up
	if ($('#typography_default_section_group_li').hasClass('active') || jQuery('#last_tab').val() == 'typography_default') {
		initTypography();
	}
	$('#typography_default_section_group_li_a').click(function() {
		if (!typography_isloaded) {
			initTypography();
		}
	});
	
	// Remove preview text
	// Todo: get i18n string from php and use that
	container.on('focus', '.font_preview input', function() {
		var $this = $(this);
		if ($this.val() == 'Type in some text to preview...' && $this.data('init')) {
			$this.val('');
		} else {
			$this.data('init', true);
		}
	}).on('blur', '.font_preview input', function() {
		var $this = $(this);
		if ($this.val() == '') {
			$this.val('Type in some text to preview...');
		}
	});
	
	// Prevent submit by hitting enter
	container.on('keypress', 'input', function(event) { return event.keyCode != 13; });
	
	// Add a new collection
	$('#typography_default_section_group').find(".new_collection").on("click", function(e) { 
		var new_collection;
		new mtsGoogleTypography(container, new_collection = $(template).prependTo(".collections"));
		new_collection.find('.font_family').trigger('change');
		container.find(".collections").show();
		//container.find(".collections .collection:first .preview_text").focus();
		container.find(".collections .collection:first .collection_moreoptions").show();
	});
	
	// Reset collections
	$('#typography_default_section_group').find(".reset_collections").on("click", function() {
		if(confirm(googletypography.reset_confirm)) {
			$.ajax({
				url: ajaxurl, 
				method: 'post',
				data: {  'action' : 'reset_user_fonts', '_ajax_nonce': jQuery('#mts_fonts_nonce').val() },
				success: function(data) {
					if(data.success == true) {
						changeWarn = false;
						location.reload();
					}
				}
			});
		}
	});

	// prevent propagation of scroll event to parent
	jQuery(document).on('DOMMouseScroll mousewheel', '.select2-results', function(ev) {
		var $this = jQuery(this),
			scrollTop = this.scrollTop,
			scrollHeight = this.scrollHeight,
			height = $this.height(),
			delta = (ev.type == 'DOMMouseScroll' ?
				ev.originalEvent.detail * -40 :
				ev.originalEvent.wheelDelta),
			up = delta > 0;
	
		var prevent = function() {
			ev.stopPropagation();
			ev.preventDefault();
			ev.returnValue = false;
			return false;
		}
	
		if (!up && -delta > scrollHeight - height - scrollTop) {
			// Scrolling down, but this will take us past the bottom.
			$this.scrollTop(scrollHeight);
			return prevent();
		} else if (up && delta > scrollTop) {
			// Scrolling up, but this will take us past the top.
			$this.scrollTop(0);
			return prevent();
		}
	});
});