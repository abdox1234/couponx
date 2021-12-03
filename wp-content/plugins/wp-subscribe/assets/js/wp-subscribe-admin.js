/*
Plugin Name: WP Subscribe Pro
Plugin URI: http://mythemeshop.com/plugins/wp-subscribe-pro/
Description: WP Subscribe is a simple but powerful subscription plugin which supports MailChimp, Aweber and Feedburner.
Author: MyThemeShop
Author URI: http://mythemeshop.com/
*/

( function( $ ){

	// color picker
	function initColorPicker( widget ) {

		var colorPickers = widget.find( '.wp-subscribe-color-select' );

		if ( colorPickers.length > 0 ) {
			colorPickers.wpColorPicker();
		}

		// and services dropdown
		widget.find('.wp-subscribe-service-field select').change(function() {

	        var $this = $(this);

			widget.find('.wp_subscribe_account_details_'+$this.val()).show().siblings('div').hide();
	        widget.find('.wp_subscribe_account_details').slideDown();

			// Include "name" field
	        if ($this.val() === 'feedburner') {
	        	widget.find('.wp_subscribe_include_name, .wp-subscribe-name_placeholder-field').hide();
	        } else {
	        	widget.find('.wp_subscribe_include_name').show().find('input').trigger('change');
	        }

			// Thanks Page option
	        if ($this.val() === 'mailchimp' || $this.val() === 'getresponse' || $this.val() === 'mailerlite' || $this.val() === 'benchmark' || $this.val() === 'constantcontact' || $this.val() === 'mailrelay' || $this.val() === 'activecampaign' ) {
	        	widget.find('.wp_subscribe_thanks_page').show();
	        } else {
	        	widget.find('.wp_subscribe_thanks_page').hide();
	        }
	    }).trigger('change');

	    widget.find('.wp_subscribe_include_name input').change(function() {
	    	if ($(this).is(':checked')) {
	    		$('.wp-subscribe-name_placeholder-field').show();
	    	} else {
	    		$('.wp-subscribe-name_placeholder-field').hide();
	    	}
	    }).trigger('change');

	    widget.find('.thanks-page-field').change(function() {
	    	if ($(this).is(':checked')) {
	    		$('.wp_subscribe_thanks_page_details').show();
	    	} else {
	    		$('.wp_subscribe_thanks_page_details').hide();
	    	}
	    }).trigger('change');
	}

	function onFormUpdate( event, widget ) {
		initColorPicker( widget );
	}

	$( document ).on( 'widget-added widget-updated', onFormUpdate );

	$( document ).ready( function() {
		$( '#widgets-right .widget:has(.wp-subscribe-service-field select)' ).each( function () {
			initColorPicker( $( this ) );
		});
	} );

	// Get List Code
	$( document ).on( 'click', '.wps-get-list', function( event ) {
		event.preventDefault();

		var button = $(this),
			select = button.prev('select'),
			parent = button.closest('.wps-account-details'),
			fields = parent.find('input, textarea'),
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

	// Aweber Autorize Code
	$( document ).on( 'click', 'button.aweber_authorization', function() {

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
	$( document ).on( 'click', 'a.aweber_disconnect', function() {
		var $this= $( this ),
			parent = $this.closest( '.alert-hint' );

		parent.hide();
		parent.prev().show();

		parent.parent().find( 'input[type="hidden"]' ).val( '' );
	});

	// slideToggle
	$(document).on('click', function(e) {
	    var $this = jQuery(e.target);
	    var $widget = $this.closest('.wp_subscribe_options_form');
	    if ($widget.length) {
	        if ($this.is('.wp-subscribe-toggle')) {
	            e.preventDefault();
	            var $related = $widget.find('.'+$this.attr('rel'));
	            $related.slideToggle();
	        }
	    }
	});

	/*
		Load Palettes
	 */
	$(document).on('click', '.wps-load-palette', function(e) {
		var $this = $(this),
			$palette = $this.closest('.single-palette');

		$palette.find('input.wps-palette-color').each(function(i, el) {
			$('#'+$(el).attr('name')).iris('color', $(el).val());
		});

		e.preventDefault();
	});
	$(document).on('click', '.wps-toggle-palettes', function(e) {
		$(this).closest('.wps-colors-loader').find('.wps-palettes').slideToggle();
		e.preventDefault();
	});

	jQuery(document).on('click', '.wpsubscribe-dismiss-notice', function(e){
		e.preventDefault();
		jQuery(this).parent().remove();
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: 'mts_dismiss_wpsubscribe_notice',
				dismiss: jQuery(this).data('ignore')
			}
		});
		return false;
	});

}( jQuery ) );
