jQuery.fn.exists = function(callback) {
  var args = [].slice.call(arguments, 1);
  if (this.length) {
	callback.call(this, args);
  }
  return this;
};

/*----------------------------------------------------
/* Show/hide Scroll to top
/*--------------------------------------------------*/
jQuery(document).ready(function($) {
	//move-to-top arrow
	jQuery("body").prepend("<a id='move-to-top' class='animate ' href='#blog'><i class='fa fa-angle-up'></i></a>");
	
	var scrollDes = 'html,body';  
	/*Opera does a strange thing if we use 'html' and 'body' together so my solution is to do the UA sniffing thing*/
	if(navigator.userAgent.match(/opera/i)){
		scrollDes = 'html';
	}
	//show ,hide
	jQuery(window).scroll(function () {
		if (jQuery(this).scrollTop() > 160) {
			jQuery('#move-to-top').addClass('filling').removeClass('hiding');
		} else {
			jQuery('#move-to-top').removeClass('filling').addClass('hiding');
		}
	});
});


/*----------------------------------------------------
/* Make all anchor links smooth scrolling
/*--------------------------------------------------*/
jQuery(document).ready(function($) {
 // scroll handler
  var scrollToAnchor = function( id, event ) {
	// grab the element to scroll to based on the name
	var elem = $("a[name='"+ id +"']");
	// if that didn't work, look for an element with our ID
	if ( typeof( elem.offset() ) === "undefined" ) {
	  elem = $("#"+id);
	}
	// if the destination element exists
	if ( typeof( elem.offset() ) !== "undefined" ) {
	  // cancel default event propagation
	  event.preventDefault();

	  // do the scroll
	  // also hide mobile menu
	  var scroll_to = elem.offset().top;
	  $('html, body').removeClass('mobile-menu-active').animate({
			  scrollTop: scroll_to
	  }, 600, 'swing', function() { if (scroll_to > 46) window.location.hash = id; } );
	}
  };
  // bind to click event
  $("a").click(function( event ) {
	// only do this if it's an anchor link
	var href = $(this).attr("href");
	if ( href && href.match("#") && href !== '#' && ! href.match(/^\#tab-/) ) {
	  // scroll to the location
	  var parts = href.split('#'),
		url = parts[0],
		target = parts[1];
	  if ((!url || url == window.location.href.split('#')[0]) && target)
		scrollToAnchor( target, event );
	}
  });
});

/*----------------------------------------------------
/* Responsive Navigation
/*--------------------------------------------------*/
if (mts_customscript.responsive && mts_customscript.nav_menu != 'none') {
	jQuery(document).ready(function($){
		$('#secondary-navigation').append('<div id="mobile-menu-overlay" />');
		// merge if two menus exist
		if (mts_customscript.nav_menu == 'both' && !$('.navigation.mobile-only').length) {
			$('.navigation').not('.mobile-menu-wrapper').find('.menu').clone().appendTo('.mobile-menu-wrapper').hide();
		}
	
		$('.toggle-mobile-menu').click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			$('body').toggleClass('mobile-menu-active');

			if ( $('body').hasClass('mobile-menu-active') ) {
				if ( $(document).height() > $(window).height() ) {
					var scrollTop = ( $('html').scrollTop() ) ? $('html').scrollTop() : $('body').scrollTop();
					$('html').addClass('noscroll').css( 'top', -scrollTop );
				}
				$('#mobile-menu-overlay').fadeIn();
			} else {
				var scrollTop = parseInt( $('html').css('top') );
				$('html').removeClass('noscroll');
				$('html,body').scrollTop( -scrollTop );
				$('#mobile-menu-overlay').fadeOut();
			}
		});
	}).on('click', function(event) {

		var $target = jQuery(event.target);
		if ( ( $target.hasClass("fa") && $target.parent().hasClass("toggle-caret") ) ||  $target.hasClass("toggle-caret") ) {// allow clicking on menu toggles
			return;
		}
		jQuery('body').removeClass('mobile-menu-active');
		jQuery('html').removeClass('noscroll');
		jQuery('#mobile-menu-overlay').fadeOut();
	});
}

/*----------------------------------------------------
/*  Dropdown menu
/* ------------------------------------------------- */
jQuery(document).ready(function($) {
	
	function mtsDropdownMenu() {
		var wWidth = $(window).width();
		if(wWidth > 865) {
			$('.navigation ul.sub-menu, .navigation ul.children').hide();
			var timer;
			var delay = 100;
			$('.navigation li').hover( 
			  function() {
				var $this = $(this);
				timer = setTimeout(function() {
					$this.children('ul.sub-menu, ul.children').slideDown('fast');
				}, delay);
				
			  },
			  function() {
				$(this).children('ul.sub-menu, ul.children').hide();
				clearTimeout(timer);
			  }
			);
		} else {
			$('.navigation li').unbind('hover');
			$('.navigation li.active > ul.sub-menu, .navigation li.active > ul.children').show();
		}
	}

	mtsDropdownMenu();

	$(window).resize(function() {
		mtsDropdownMenu();
	});
});

/*---------------------------------------------------
/*  Vertical menus toggles
/* -------------------------------------------------*/
jQuery(document).ready(function($) {

	$('.widget_nav_menu, .navigation .menu').addClass('toggle-menu');
	$('.toggle-menu ul.sub-menu, .toggle-menu ul.children').addClass('toggle-submenu');
	$('.toggle-menu ul.sub-menu').parent().addClass('toggle-menu-item-parent');

	$('.toggle-menu .toggle-menu-item-parent').append('<span class="toggle-caret"><i class="fa fa-plus"></i></span>');

	$('.toggle-caret').click(function(e) {
		e.preventDefault();
		$(this).parent().toggleClass('active').children('.toggle-submenu').slideToggle('fast');
	});
});

/*----------------------------------------------------
/* Social button scripts
/*---------------------------------------------------*/
jQuery(document).ready(function($){
	(function(d, s) {
	  var js, fjs = d.getElementsByTagName(s)[0], load = function(url, id) {
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.src = url; js.id = id;
		fjs.parentNode.insertBefore(js, fjs);
	  };
	jQuery('span.facebookbtn, span.facebooksharebtn, .facebook_like').exists(function() {
	  load('//connect.facebook.net/en_US/all.js#xfbml=1&version=v2.8', 'fbjssdk');
	});
	jQuery('span.gplusbtn').exists(function() {
	  load('https://apis.google.com/js/plusone.js', 'gplus1js');
	});
	jQuery('span.twitterbtn').exists(function() {
	  load('//platform.twitter.com/widgets.js', 'tweetjs');
	});
	jQuery('span.linkedinbtn').exists(function() {
	  load('//platform.linkedin.com/in.js', 'linkedinjs');
	});
	jQuery('span.pinbtn').exists(function() {
	  load('//assets.pinterest.com/js/pinit.js', 'pinterestjs');
	});
	}(document, 'script'));
});

/*----------------------------------------------------
/* Lazy load avatars
/*---------------------------------------------------*/
jQuery(document).ready(function($){
	var lazyloadAvatar = function(){
		$('.comment-author .avatar').each(function(){
			var distanceToTop = $(this).offset().top;
			var scroll = $(window).scrollTop();
			var windowHeight = $(window).height();
			var isVisible = distanceToTop - scroll < windowHeight;
			if( isVisible ){
				var hashedUrl = $(this).attr('data-src');
				if ( hashedUrl ) {
					$(this).attr('src',hashedUrl).removeClass('loading');
				}
			}
		});
	};
	if ( $('.comment-author .avatar').length > 0 ) {
		$('.comment-author .avatar').each(function(i,el){
			$(el).attr('data-src', el.src).removeAttr('src').addClass('loading');
		});
		$(function(){
			$(window).scroll(function(){
				lazyloadAvatar();
			});
		});
		lazyloadAvatar();
	}
});

/*----------------------------------------------------
/* HomePage Tabs
/*---------------------------------------------------*/
jQuery(document).ready(function($){
	$(".tabs-menu a").click(function(event) {
		event.preventDefault();
		$(this).parent().addClass("current");
		$(this).parent().siblings().removeClass("current");
		var tab = $(this).attr("href");
		$(".tabs-container .tab-content").not(tab).css("display", "none");
		$(tab).fadeIn().find('img').attr('src', function() { return $(this).data('src'); });
                
                // Get window coordinates before scrolling occurs to maintain the current scroll position. Used to avoid page-jump on FF & IE
                var x = window.pageXOffset,
                    y = window.pageYOffset;
                $(window).one('scroll', function () {
                    window.scrollTo(x, y);
                });
	});
});

jQuery(window).on('hashchange', function(e){
	//history.replaceState ("", document.title, e.originalEvent.oldURL);
});

/*----------------------------------------------------
/* Update the value of CMB2 field by 1
/*---------------------------------------------------*/
jQuery(document).ready(function($) {
	$(document).on( 'click', '.activate-button', function(e) {
		e.stopPropagation();
		var href = this.getAttribute('href');
		if (!href.length) {
			return;
		}
		window.open(href, '_blank');
		$.ajax({
			type: "POST",
			url: mts_customscript.ajax_url,
			data: {
				action: 'mts_activate_deal',
				id: $(this).attr('data-post-id')
			},
			beforeSend: function() {
				$(this).addClass('disabled').html(mts_customscript.deal_activate_loading);
			},
			success: function (data) {
				$(this)/*.removeClass('disabled')*/.html(mts_customscript.deal_activate_done); // maybe need to stay disabled
			},
			error: function() {
				alert('Something went wrong. Please try again.');
			}
		});
	});
});

/*----------------------------------------------------
/* Magnific Popup
/*---------------------------------------------------*/
jQuery(window).on('load', function() {
	// $(function () {
		var selector = '.activate-modal';
		if ( mts_customscript.coupon_button_action == 'popunder' ) {
			selector = '.activate-modal:not(.show-coupon-button)';
		}
		var $selector = jQuery(selector);

		jQuery(document).on('click', '.popup-modal-dismiss', function (e) {
			e.preventDefault();
			jQuery.magnificPopup.close();
		});

		if ( ! $selector.length ) {
			return;
		}
		$selector.magnificPopup({
			type: 'inline',
			preloader: false,
			modal: true
		});
		jQuery(".post a[href$='.jpg'],.post a[href$='.jpeg'],.post a[href$='.gif'],.post a[href$='.png']").magnificPopup({
			type: 'image',
			disableOn: function() {
				if( jQuery(window).width() < 600 ) {
					return false;
				}
				return true;
			}
		});
	// });
});


/*----------------------------------------------------
/* Coupon Pop-under
/*---------------------------------------------------*/
if ( mts_customscript.coupon_button_action == 'popunder' ) {
	jQuery(window).on('load', function() {

		function post_in_new_tab(url, params) {
			var f = jQuery("<form target='_blank' method='POST' style='display:none;'></form>").attr({
				action: url
			}).appendTo(document.body);

			for (var i in params) {
				if (params.hasOwnProperty(i)) {
					jQuery('<input type="hidden" />').attr({
						name: i,
						value: params[i]
					}).appendTo(f);
				}
			}
			f.submit();
			f.remove();
		}

		jQuery('#content_box').on('click', '.show-coupon-button', function(event) {

			var $this = jQuery(this);
			var modalnum = $this.data('mfp-src').substr(16);
			var thelink = $this.attr('href');
			if (!thelink.length && !$this.hasClass('deal-button')) {
				jQuery.magnificPopup.open({
					items: {
								src: '#activate-modal-'+modalnum
						},
					type: 'inline',
					preloader: false,
					modal: true
				});
				$this.css({width: '0px', overflow: 'hidden'}).next('.code-button-bg').css('text-align', 'center');

				return;
			}
			post_in_new_tab('', {show_deal: modalnum});
			setTimeout(function() { window.location.href = thelink; }, 100);
			return false;
		});
		if ( mts_customscript.launch_popup && mts_customscript.launch_popup != '0' ) {
			jQuery.magnificPopup.open({
				items: {
					src: '#activate-modal-'+mts_customscript.launch_popup
				},
				type: 'inline',
				preloader: false,
				modal: true
			});
		}
	});
} else {
	jQuery('#content_box').on('click', '.show-coupon-button', function(event) {
		jQuery(this).css({width: '0px', overflow: 'hidden'}).next('.code-button-bg').css('text-align', 'center');
	});
}

// Copy Coupon Code
jQuery(document).ready(function($) {
	var clipboard = new Clipboard('.coupon-code button');
	clipboard.on('success', function(e) {
		e.clearSelection();
	});

	$('.coupon-code button').on('click', function() {
		var coupon_wrap = $(this).prev('span'),
			org_text = $(coupon_wrap).text();
		$(coupon_wrap).text(mts_customscript.copied_msg);
		setTimeout(function() { $(coupon_wrap).text(org_text); }, 1000);
	});

	$('.print-coupon-code').on('click', function(e){
		e.preventDefault();
		var content = $(this).parents('.white-popup-block').find('.coupon-code-wrapper img')[0].outerHTML;
		var mywindow = window.open('', 'Print Coupon', 'height=600,width=800');
		mywindow.document.write('<html><head><title>Print</title>');
		mywindow.document.write('</head><body >');
		mywindow.document.write(content);
		mywindow.document.write('</body></html>');

		mywindow.document.close();
		mywindow.focus()
		mywindow.print();
		mywindow.close();
		return false;
	});

	$('.cp-actions a').on('click', function(e){
		if(! $(this).hasClass('coupon_deal_URL')) {
			e.preventDefault();
			var $this = $(this),
				post_id = $this.parent().data('post-id'),
				value = $this.data('value');
			$.ajax({
				type: "POST",
				url: mts_customscript.ajax_url,
				data: {
					action: 'mts_cp_action',
					id: $this.parent().data('post-id'),
					value: $this.data('value'),
				},
				success: function (data) {
					if(data) {
						if(value == 'worked') {
							$this.parent().find('a[data-value="not-worked"]').removeClass('active');
						} else if(value == 'not-worked') {
							$this.parent().find('a[data-value="worked"]').removeClass('active');
						}
						$this.addClass('active');
					} else {
						$this.removeClass('active');
					}
				},
				error: function() {
					alert('Something went wrong. Please try again.');
				}
			});
			return false;
		}
		
	});

	$('.cp-share-box .open-cp-sharebox').on('click', function(e){
		e.preventDefault();
		$(this).next().show();
		return false;
	});

	$('.cp-share-box .close-share-box').on('click', function(e){
		e.preventDefault();
		$(this).parent().hide();
		return false;
	});

});