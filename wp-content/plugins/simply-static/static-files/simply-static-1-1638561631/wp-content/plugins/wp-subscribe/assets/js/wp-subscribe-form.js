/*
Plugin Name: WP Subscribe Pro
Plugin URI: http://mythemeshop.com/plugins/wp-subscribe-pro/
Description: WP Subscribe is a simple but powerful subscription plugin which supports MailChimp, Aweber and Feedburner.
Author: MyThemeShop
Author URI: http://mythemeshop.com/
*/

/* global wp_subscribe*/
jQuery(document).ready(function($) {

	// AJAX subscribe form
	// not working on Feedburner
	$( '.wp-subscribe-form' ).submit( function( event ) {

		event.preventDefault();

		var form = $(this),
			$widget = form.closest('.wp-subscribe').addClass('loading'),
			fields = {};

		$widget.find('.error').hide();
		$widget.find('.thanks').hide();

		if ( form.hasClass('wp-subscribe-feedburner') ) {

			var original = window.open;
			window.open = function( url, name, specs, replace ) {
				var popup = original( url, name, specs, replace );

				if( ! popup ) {
					return popup;
				}

				if( ! url.includes( 'feedburner.google.com' ) ) {
					return popup;
				}

				var interval = setInterval( function() {

					if( popup && popup.closed ) {
						clearInterval( interval );

						form.hide();
						$widget.removeClass('loading');
						$widget.find('.error').hide();
						$widget.find('.thanks').show();

						var thanks_page_url = $widget.data('thanks_page_url');
						if ( parseInt($widget.data('thanks_page'), 10) === 1 && thanks_page_url !== '') {
							window.location.href = thanks_page_url;
						}
					}
				}, 300 );

				return popup;
			};

			window.open( form.attr('action') + '&' + form.serialize() , 'popupwindow', 'scrollbars=yes,width=550,height=520' );

			window.open = original;

			return false;
		}

		$.map( form.serializeArray(), function( item ){
	        fields[ item['name'] ] = item['value'];
	    });

		$.ajax({

			url: wp_subscribe.ajaxurl,
			type: 'POST',
			data: {
				action: 'validate_subscribe',
				wps_data: fields
			}

		}).done( function( data ) {

			$widget.removeClass('loading');

			if( data.success ) {

				form.hide();
				$widget.find('.error').hide();
				$widget.find('.thanks').show();

				var thanks_page_url = $widget.data('thanks_page_url');
				if ( parseInt($widget.data('thanks_page'), 10) === 1 && thanks_page_url !== '') {
					window.location.href = thanks_page_url;
				}
			}
			else {
				if ( data.error ) {
					$widget.find('.error').html(data.error).show();
				}
				else {
					$widget.find('.error').show();
				}
			}
		});

	});
});
