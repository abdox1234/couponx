/*
Plugin Name: WP Subscribe Pro
Plugin URI: http://mythemeshop.com/plugins/wp-subscribe-pro/
Description: WP Subscribe is a simple but powerful subscription plugin which supports MailChimp, Aweber and Feedburner.
Author: MyThemeShop
Author URI: http://mythemeshop.com/
*/

/* global wps_opts */
;( function( $ ) {

	var WpSubscriber = {

		init: function() {

			// Cache
			this.previewButtons = $('.wp-subscribe-preview-popup');
			this.popupOptions = $('#wp-subscribe-popup-options');

			// Init others
			this.colorPicker();
			this.popup();
			this.subscription();
			this.singlePost();
			this.misc();
		},

		singlePost: function() {

			$('#wp_subscribe_enable_single_post_form').change(function() {

				if ( $(this).is(':checked') ) {
					$('#wp-subscribe-single-options').slideDown();
				} else {
					$('#wp-subscribe-single-options').slideUp();
				}

			});

			$('#copy_options_popup_to_single').click(function( event ) {

				event.preventDefault();

				$('#wp-subscribe-single-options').find('input').each(function() {
					var $input = $(this);
					var $mapped = $('#'+this.id.replace('single_post', 'popup'));
					if ( $mapped.length && $mapped.prop('id') !== this.id ) {
						$input.val($mapped.val()).trigger('change');
					}
				});

				var service = $('#popup_form_service').val();
				$('#single_post_form_service option').each(function() {
					var $this = $(this);
					if ( service === $this.attr('value') ) {
						$this.prop('selected', true);
					}
					else {
						$this.prop('selected', false);
					}
				}).trigger('change');
			});
		},

		subscription: function() {

			var formLabels = $('._popup_form_labels_name_placeholder-wrapper'),
				postLabels = $('._single_post_form_labels_name_placeholder-wrapper');

			$('.services_dropdown').change(function() {

				var $this          = $(this),
					value          = $this.val(),
					parent         = $this.parent(),
					nameFields     = parent.siblings( '.wp_subscribe_include_name_wrapper' ),
					thankyouFields = parent.siblings( '.wp_subscribe_thanks_page' );

				parent.next().find('.wp_subscribe_account_details_'+$this.val()).show().siblings().hide();

				if ( 'feedburner' === value ) {
					nameFields.hide();

					if ( $this.closest('#wp-subscribe-single-options').length ) {
						postLabels.hide();
					}
					else {
						formLabels.hide();
					}

				} else {
					nameFields.show().find('input').trigger('change');
				}

				// Thanks Page option
		        if ( -1 < $.inArray( value, [ 'mailchimp', 'getresponse', 'mailerlite', 'benchmark', 'constantcontact', 'mailrelay', 'activecampaign' ] ) ) {
		        	thankyouFields.show();
		        } else {
		        	thankyouFields.hide();
		        }

			}).trigger('change');

			$('.thanks-page-field').change(function() {

				$(this).parent().siblings('.wp_subscribe_thanks_page_details').toggle( this.checked );

			}).trigger( 'change' );

			$('.wp_subscribe_include_name_wrapper input').change(function() {

				var $this = $(this);

				if ( $this.is(':checked') ) {
					if ($this.closest('#wp-subscribe-single-options').length) {
						postLabels.show();
					}
					else {
						formLabels.show();
					}
				} else {
					if ($this.closest('#wp-subscribe-single-options').length) {
						postLabels.hide();
					}
					else {
						formLabels.hide();
					}
				}

			}).trigger('change');

			// Get List Code
			$('.wps-get-list').on( 'click', function( event ) {
				event.preventDefault();

				var button  = $(this),
					select  = button.prev('select'),
					parent  = button.closest('.wps-account-details'),
					fields  = parent.find('input, textarea'),
					service = parent.data('service');

				var args = {};
				fields.each(function(){
					var f = $(this);

					if ( f.data( 'id' ) && f.data( 'id' ).length > 0 ) {
						var key = f.data( 'id' ).replace(service+'_', '').replace(service, '');
					args[key] = f.val();
					}
				});

				$.ajax({
					url: ajaxurl,
					method: 'post',
					data: {
						action: 'wps_get_service_list',
						service: service,
						args: args
					},

					success: function( response ) {

						if( response.success && response.lists ) {
							var sel = select.val();
							select.html( '<option value="none">Select List</option>' );
							$.each( response.lists, function( key, val ){
								select.append('<option value="'+ key +'">'+ val +'</option>');
							});
							select.val(sel);
						}
						else {
							console.log( response.error );
						}
					}
				});

			} );
		},

		colorPicker: function() {

			$('.wp-subscribe-color-select').wpColorPicker({
				change: _.throttle(function(event, ui) {
					$(this).trigger( 'colorchange', [ui.color.toString()] );
				}, 2000 )
			});

			$(document).on('click', '.wps-load-palette', function( event ) {

				event.preventDefault();

				var $this = $(this),
					palette = $this.closest('.single-palette');

				palette.find('input.wps-palette-color').each(function( i, el ) {

					var elem = $(el);

					$('#' + elem.attr('name') ).iris('color', elem.val() );
				});
			});

			$(document).on('click', '.wps-toggle-palettes', function( event ) {

				event.preventDefault();

				$(this).closest('.wps-colors-loader').find('.wps-palettes').slideToggle();
			});
		},

		popup: function() {

			var wps = this;

			wps.popupColor();
			wps.popupOpacity();
			wps.popupWidth();
			wps.popupPreview();

			$('#wp_subscribe_enable_popup').change(function() {

				if ( $(this).is(':checked') ) {

					wps.popupOptions.slideDown();
					$('.ifpopup').show();

				} else {

					wps.popupOptions.slideUp();
					$('.ifpopup').hide();

				}
			});

			$('.popup_content_field').change(function() {

				var value = $(this).val(),
					form = $('#wp-subscribe-form-options'),
					posts = $('#wp-subscribe-popup-posts-options'),
					custom = $('#wp-subscribe-custom-html-field');

				// Hide All
				form.hide();
				posts.hide();
				custom.hide();

				switch( value ) {

					case 'subscribe_form':
						form.show();
						break;

					case 'posts':
						posts.show();
						break;

					case 'custom_html':
						custom.show();
						break;
				}

				var $tab = $('#popup-content-tab');
				$tab.addClass('nav-tab-active');
				setTimeout(function() {
					$tab.removeClass('nav-tab-active');
				}, 200);
			});

			wps.firePopup( wps_opts.popup_removal_delay );

			$('#popup_animation_in').on('change', function() {
				wps.previewButtons.attr( 'data-animatein', $(this).val() );
			});

			$('#popup_animation_out').on('change', function() {

				var value = $(this).val();
				wps.previewButtons.attr( 'data-animateout', value );

				if (value === 'hinge') {
					wps.firePopup(2000);
				} else if (value === '0') {
					wps.firePopup(0);
				} else {
					wps.firePopup(800);
				}
			});
		},

		firePopup: function(removal_delay) {

			var wps = this;

			wps.previewButtons.magnificPopup({
			  type:'inline',
			  midClick: true,
			  removalDelay: removal_delay, //delay removal by X to allow out-animation
			  callbacks: {
				beforeOpen: function() {
				   this.st.mainClass = 'animated ' + this.st.el.attr( 'data-animatein' );
				},
				beforeClose: function() {
					var $wrap = this.wrap,
						$bg = $wrap.prev(),
						$mfp = $wrap.add($bg);

					$mfp.removeClass( this.st.el.attr( 'data-animatein' ) ).addClass( this.st.el.attr( 'data-animateout' ) );
				}
			  }
			});
		},

		popupPreview: function() {

			var wps = this;

			$('.popup_content_field, .wps-popup-content-options input, .wp-editor-area').on('change colorchange', function() {

				wps.previewButtons.addClass('disabled');

				var fields = $('#wp_subscribe_options_form').serialize() + '&action=preview_popup';
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'html',
					data: fields,

				}).done(function(response) {

					$('#wp_subscribe_popup').html(response);

				}).always(function() {

					wps.previewButtons.removeClass('disabled');

				});
			});
		},

		popupOpacity: function() {

			var changeOpacity = function( opacity ) {
			    $( '#overlay-style-opacity' ).html( '.mfp-bg.mfp-ready {opacity: ' + opacity + ';}' );
			};

			var input = $( '#wp_subscribe_overlay_opacity' ),
				slider = $( '#wp-subscribe-opacity-slider' );

			input.on('change', function() {
		    	var value = parseFloat( input.val() );
		    	if ( value < 0 ) {
		    		value = 0;
		    		input.val('0');
		    	} else if ( value > 1 ) {
		    		value = 1;
		    		input.val('1');
		    	}
		    	slider.slider( 'value', value );
				changeOpacity( value );
		    });

			slider.slider({
			    range: 'min',
			    value: input.val(),
			    step: 0.01,
			    min: 0,
			    max: 1,
			    slide: function(event, ui) {
			        input.val( ui.value );
					changeOpacity( ui.value );
			    }
			});
		},

		popupColor: function() {

			$('#wp_subscribe_options_colors_popup_overlay_color').on( 'colorchange', function( event, color ) {
				$('#overlay-style-color').html('.mfp-bg {background: ' + color + ';}');
			});
		},

		popupWidth: function() {

			var changeWidth = function( width ) {
			    $('#popup-style-width').html('#wp_subscribe_popup {width: ' + width + 'px;}');

			    var breakpoints = [300, 600, 900],
					popup = $('#wp_subscribe_popup');

				$.each(breakpoints, function(index, breakpoint) {
					 if (width < breakpoint) {
					 	popup.addClass( 'lt_' + breakpoint );
					 } else {
					 	popup.removeClass( 'lt_' + breakpoint );
					 }
				});
			};

			var input = $( '#wp_subscribe_popup_width' ),
				slider = $( '#wp-subscribe-popup-width-slider' );

			input.on('change', function() {
		    	var value = parseFloat( input.val() );

				if (value < 0) {
		    		value = 0;
		    		input.val('0');
		    	} else if (value > 1200) {
		    		value = 1200;
		    		input.val('1200');
		    	}

				slider.slider( 'value', value );
				changeWidth( value );
		    });

		    slider.slider({
			    range: 'min',
			    value: input.val(),
			    step: 10,
			    min: 200,
			    max: 1200,
			    slide: function(event, ui) {
			        input.val( ui.value );
					changeWidth( ui.value );
			    }
			});
		},

		misc: function() {

			$('#wp_subscribe_regenerate_cookie').click(function( event ) {

				event.preventDefault();

				$('#cookies-cleared').fadeIn();
				$('#cookiehash').val(new Date().getTime());

			});

			// Tabs
			var tabNav = $( '.wps-nav-tab-wrapper a' ),
				tabContent = $( ' > div ', '.wps-tabs-wrapper');

			tabNav.click(function( event ) {

				event.preventDefault();

				var $this = $(this);

				tabNav.removeClass( 'nav-tab-active' );
				$this.addClass( 'nav-tab-active' );

				tabContent.hide();
				tabContent.closest( $this.data('rel') ).show();

			});

			// Aweber Autorize Code
			$( 'button.aweber_authorization' ).on( 'click', function() {

				var $this= $( this ),
					parent = $this.parent(),
					code = parent.find( 'textarea' ).val().trim();

				if( '' === code ) {
					alert( 'No authorization code found.' );
					return;
				}

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'connect_aweber',
						aweber_code: code
					}

				}).done(function(response) {

					if ( response && ! response.success && response.error ) {
						alert( response.error );
						return;
					}

					var details = parent.parent();
					for( key in response.data ) {
						details.find( '[id$="_' + key + '"]' ).val( response.data[ key ] );
					}

					parent.hide();
					parent.next().show();
				});
			});

			// Disconnect Aweber
			$( 'a.aweber_disconnect' ).on( 'click', function() {
				var $this= $( this ),
					parent = $this.closest( '.alert-hint' );

				parent.hide();
				parent.prev().show();

				parent.parent().find( 'input[type="hidden"]' ).val( '' );
			});
		}
	};

	$( document ).ready( function() {
		WpSubscriber.init();
	} );

}( jQuery ) );
