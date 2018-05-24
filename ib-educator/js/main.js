window.IBEducator || (window.IBEducator = {});

(function($) {

	'use strict';

	var isTouch = Modernizr.touch;

	/**
	 * Init full width slider.
	 */
	function initFWSlider() {
		var showCaption = function(caption) {
			if (!caption.length) return;

			if (Modernizr.cssanimations) {
				caption.addClass('in');
			} else {
				caption.stop().css({display: 'block', opacity: 0}).animate({opacity: 1}, {duration: 300});
			}
		};

		var hideCaption = function(caption) {
			if (!caption.length) return;

			if (Modernizr.cssanimations) {
				caption.removeClass('in');
			} else {
				caption.stop().animate({opacity: 0}, {duration: 300, complete: function() {
					$(this).css('display', 'none');
				}});
			}
		};

		var sliders = $('.fw-slider'), slider, sliderArgs, autoScroll;

		if (sliders.length) {
			sliderArgs = {
				animation: 'fade',
				animationSpeed: 600,
				prevText: '',
				nextText: '',

				start: function(slider) {
					if (captions.length) {
						resizeCaptions();
					}

					showCaption(slider.slides.eq(slider.currentSlide).find('> .slide-caption'));
				},

				before: function(slider) {
					hideCaption(slider.slides.eq(slider.currentSlide).find('> .slide-caption'));
				},

				after: function(slider) {
					showCaption(slider.slides.eq(slider.currentSlide).find('> .slide-caption'));
				}
			};

			for (var i = 0; i < sliders.length; ++i) {
				slider = sliders.eq(i);
				autoScroll = parseInt(slider.data('autoscroll'), 10);

				if (!isNaN(autoScroll) && autoScroll) {
					sliderArgs.slideshow = true;
					sliderArgs.slideshowSpeed = autoScroll * 1000;
				} else {
					sliderArgs.slideshow = false;
				}

				slider.flexslider(sliderArgs);
			}

			var captions = $('div.slide-caption');
			var resizeCaptions = function() {
				var caption, flexslider;

				for (var i = 0; i < captions.length; ++i) {
					caption = captions.eq(i);
					flexslider = caption.closest('.flexslider');
					caption.css({
						top: (flexslider.height() - caption.height()) * .5
					});
				}
			};

			if (captions.length) {
				resizeCaptions();
				$(window).smartresize(resizeCaptions);

				if (!Modernizr.cssanimations) {
					captions.css('display', 'none');
				}
			}
		}
	}

	/**
	 * Init flexslider.
	 */
	function initFlexslider() {
		var sliders = $('.flexslider:not(.fw-slider)'), slider, sliderArgs, autoScroll;

		if (sliders.length) {
			sliderArgs = {
				animation: 'fade',
				animationSpeed: 600,
				prevText: '',
				nextText: '',
			};

			for (var i = 0; i < sliders.length; ++i) {
				slider = sliders.eq(i);
				autoScroll = parseInt(slider.data('autoscroll'), 10);

				if (!isNaN(autoScroll) && autoScroll) {
					sliderArgs.slideshow = true;
					sliderArgs.slideshowSpeed = autoScroll * 1000;
				} else {
					sliderArgs.slideshow = false;
				}

				slider.flexslider(sliderArgs);
			}
		}
	}

	/**
	 * Setup main navigation.
	 */
	function initMainNav() {
		var open = function(li, selector) {
			var nav = li.find(selector);
			nav.stop().css({display: 'block', opacity: 0}).animate({opacity: 1}, {duration: 200});
		};

		var close = function(li, selector) {
			var nav = li.find(selector);
			nav.stop().animate({opacity: 0}, {duration: 200, complete: function() {
				this.style.display = 'none';
			}});
		};

		$('#main-nav > .menu a').on('click', function(e) {
			var href = this.getAttribute('href');
			if (href === '' || href === '#') {
				e.preventDefault();
			}
		});

		$('#main-nav > .menu li.menu-item-has-children').each(function() {
			var jThis = $(this);

			jThis.hover(function() {
				open($(this), '> .sub-menu');
			}, function() {
				close($(this), '> .sub-menu');
			});
		});

		$('#user-nav').hover(function() {
			open($(this), '> .menu');
		}, function() {
			close($(this), '> .menu');
		});
	}

	/**
	 * Setup manin navigation for mobile devices.
	 */
	function initMobileNav() {
		var menu = $('<div id="mobile-nav">');
		var inner = $('<div>');
		var body = $('body');

		// Close mobile navigation.
		var close = function() {
			if (Modernizr.csstransitions && Modernizr.csstransforms) {
				menu.one(IBEducator.transitionEnd(), function() {
					this.style.display = 'none';
					body.css({overflow: 'auto', width: 'auto'});
				});
				
				pageOverlay.one(IBEducator.transitionEnd(), function() {
					this.style.display = 'none';
				});

				menu.removeClass('open');
				pageOverlay.removeClass('open');
			} else {
				menu.removeClass('open');
				pageOverlay.removeClass('open');
				menu.css({display: 'none', 'marginLeft': 0});
				pageOverlay.css('display', 'none');
				body.css({overflow: 'auto', width: 'auto'});
			}
		};

		// Add user menu.
		var userNav = $('#user-nav').clone();
		if (userNav.length) {
			userNav.removeAttr('id').addClass('user-nav');
			inner.append(userNav);
		}

		// Add main nav.
		var mobileNav = $('#main-nav > ul').clone();
		mobileNav.find('li.menu-item-has-children').each(function() {
			var trigger = $(this).find('> a');

			trigger.append('<span class="submenu-trigger"></span>');

			trigger.on('click', function(e) {
				e.preventDefault();
				$(this).parent().toggleClass('open');
			});
		});
		inner.append(mobileNav.removeAttr('id'));
		
		// Add auth nav.
		var authNav = $('#auth-nav').clone();
		if (authNav.length) {
			authNav.removeAttr('id').addClass('auth-nav');
			authNav.find('a.button').removeClass('button');
			inner.append(authNav);
		}

		// Add search.
		inner.append($('#header-search > form').clone());

		// Add close button.
		var closeButton = $('<a id="close-mobile-nav" href="#">&times;</a>');
		closeButton.on('click', function(e) {
			e.preventDefault();
			close();
		});
		inner.append(closeButton);

		menu.append(inner);
		body.append(menu);

		// Add overlay.
		var pageOverlay = $('<div id="page-overlay"></div>');
		pageOverlay.on('click', function(e) {
			e.preventDefault();
			close();
		});
		body.append(pageOverlay);

		// Add menu trigger.
		var menuTrigger = $('<a id="mobile-nav-trigger" href="#"><span class="bar-1"></span><span class="bar-2"></span><span class="bar-3"></span></a>');
		
		menuTrigger.on('click', function(e) {
			e.preventDefault();
			
			// Hide overflow on body to prevent scrolling.
			var bodyWidth = body.width();
			body.css({overflow: 'hidden', width: bodyWidth + 'px'});

			// Show page overlay.
			pageOverlay.css('display', 'block').get(0).offsetWidth;
			pageOverlay.addClass('open');

			// Show main nav.
			menu.css({display: 'block'}).get(0).offsetWidth;
			menu.addClass('open');
			
			if (!Modernizr.csstransforms) {
				menu.css('marginLeft', '-' + menu.outerWidth() + 'px');
			}
		});

		$('#header-container > .container').append(menuTrigger);
	}

	/**
	 * Initialize courses carousel.
	 */
	function initCoursesCarousel() {
		$('div.courses-carousel, div.posts-carousel').owlCarousel({
			items: 3,
			itemsCustom: [
				[0, 1],
				[640, 2],
				[960, 3]
			],
			pagination: true
		});
	}

	/**
	 * Initialize lecturers carousel.
	 */
	function initLecturersCarousel() {
		$('div.lecturers-carousel').owlCarousel({
			itemsCustom: [
				[0, 1],
				[640, 2],
				[960, 3]
			],
			pagination: true
		});
	}

	/**
	 * Initialize header search.
	 */
	function headerSearch() {
		var container = $('#header-search');

		container.find('> button').on('click', function(e) {
			e.preventDefault();
			var container = $(this).parent();
			var form = container.find('> form');

			if (container.hasClass('open')) {
				$('#main-nav').fadeIn(200);
				container.removeClass('open');
				form.fadeOut(200);
			} else {
				$('#main-nav').fadeOut(200);
				container.addClass('open');
				form.fadeIn(200);
				form.find('input[type="text"]').focus();
			}
		});
	}

	/**
	 * Initialize share links menu.
	 */
	function initShareLinksMenu() {
		var open = function(menu) {
			menu.stop().css({display: 'block', opacity: 0}).animate({opacity: 1}, {duration: 200});
		};

		var close = function(menu) {
			menu.stop().animate({opacity: 0}, {duration: 200, complete: function() {
				$(this).css('display', 'none');
			}});
		};

		if ( isTouch ) {
			$('.share-links-menu > a').on('click', function(e) {
				e.preventDefault();
				var menu = $(this).parent().find('> ul');

				if (menu.is(':visible')) {
					close(menu);
				} else {
					open(menu);
				}
			});
		} else {
			$('.share-links-menu').hover(function() {
				open($(this).find('> ul'));
			}, function() {
				close($(this).find('> ul'));
			});
		}
	}

	/**
	 * Fixed header.
	 */
	var IBFixedHeader = {
		ticking: false,
		lastScrollY: 0,

		init: function() {
			this.header = $('#page-header');

			// If fixed header disabled, return.
			if (!this.header.hasClass('fixed-header')) {
				return;
			}

			this.inner = $('#page-header-inner');
			this.initHeight = 94;//this.header.height();
			this.fixedHeight = 60;
			this.lineHeightItems = $('#main-nav > .menu > li > a, #auth-nav .auth-nav-login, #auth-nav .auth-nav-register, #user-nav .user-menu-name, #main-logo > a');
			this.heightItems = $('#page-header-inner, #header-search > button');
			this.win = $(window);
			this.minTop = 0;
			this.initTop = 0;
			this.toolbarHeight = 0;
			this.logo = $('#main-logo img');
			this.logoMaxHeight = parseInt(this.logo.css('max-height'), 10);

			var body = $('body');

			if (body.hasClass('has-toolbar')) {
				this.initTop += 45;
				this.toolbarHeight = 45;
			}

			if (body.hasClass('admin-bar')) {
				this.initTop += 32;
				this.minTop = 32;
			}

			this.win.on('scroll', function() {
				IBFixedHeader.onScroll();
			});

			this.win.smartresize(function() {
				IBFixedHeader.onResize();
			});

			this.lastScrollY = this.win.scrollTop();
			IBFixedHeader.onResize();
		},

		onScroll: function() {
			this.lastScrollY = this.win.scrollTop();

			if (!this.ticking) {
				requestAnimationFrame(function() {
					IBFixedHeader.update();
				});
				this.ticking = true;
			}
		},

		update: function() {
			this.ticking = false;

			if (window.innerWidth < 980) {
				return;
			}

			var scrollY = this.lastScrollY,
				innerTop = 0,
				newHeight = 0;

			// Calculate header "top" css.
			innerTop = this.initTop - scrollY;
			if (innerTop < this.minTop) innerTop = this.minTop;
			this.inner.css('top', innerTop + 'px');

			// Adjust header height.
			newHeight = this.initHeight - scrollY + this.toolbarHeight;

			if (newHeight < this.fixedHeight) {
				newHeight = this.fixedHeight;
			} else if (newHeight > this.initHeight) {
				newHeight = this.initHeight;
			}

			if (this.logoMaxHeight > 50) { // leave some vertical space for the logo
				var logoMaxHeight = this.logoMaxHeight - scrollY + this.toolbarHeight;

				if (logoMaxHeight < 50) logoMaxHeight = 50;
				else if (logoMaxHeight > this.logoMaxHeight) logoMaxHeight = this.logoMaxHeight;

				this.logo.css('maxHeight', logoMaxHeight + 'px');
			}

			this.lineHeightItems.css('lineHeight', newHeight + 'px');
			this.heightItems.css('height', newHeight + 'px');
		},

		onResize: function() {
			if (window.innerWidth < 980) {
				this.inner.attr('style', '');
				this.lineHeightItems.attr('style', '');
				this.heightItems.attr('style', '');
			} else {
				this.update();
			}
		}
	};

	/**
	 * Drop downs.
	 */
	function initDropDowns() {
		var open = function(container) {
			var menu = container.find('> ul');
			container.addClass('open');
			menu.stop().css({display: 'block', opacity: 0}).animate({opacity: 1}, {duration: 200})
		};

		var close = function(container) {
			var menu = container.find('> ul');
			container.removeClass('open');
			menu.stop().animate({opacity: 0}, {duration: 200, complete: function() {
				$(this).css({display: 'none'});
			}})
		};

		if (isTouch) {
			$('.drop-down > a').on('click', function(e) {
				e.preventDefault();
				var container = $(this).parent();

				if (container.hasClass('open')) {
					close(container);
				} else {
					open(container);
				}
			});
		} else {
			$('.drop-down').hover(function() {
				open($(this));
			}, function() {
				close($(this));
			});
		}
	}

	// Login and register forms.
	if ($.validator) {
		$.validator.addMethod('wpusername', function(value, element) {
			return /^[a-z0-9_\-.*@ ]+$/i.test(value);
		});

		$('#loginform').validate({
			errorPlacement: function() {},
			rules: {
				log: {
					required: true,
					wpusername: true
				},
				pwd: 'required',
			}
		});

		$('#registerform').validate({
			errorPlacement: function() {},
			rules: {
				user_login: 'wpusername'
			}
		});
	}

	// Main nav.
	initMainNav();

	// Mobile nav.
	initMobileNav();

	// Fixed header.
	IBFixedHeader.init();

	// Document ready.
	$(document).on('ready', function() {
		// Header search.
		headerSearch();

		// Share links.
		initShareLinksMenu();

		// Flexslider.
		initFlexslider();

		// Magnific popup for WP gallery.
		if (eduThemeObj.disableLightbox === "0") {
			$('.gallery').each(function() {
				$(this).magnificPopup({
					type: 'image',
					image: {
						titleSrc: function(item) {
							return item.el.find('> img').attr('alt');
						}
					},
					delegate: 'a',
					gallery: {
						enabled: true
					}
				});
			});

			// Magnific popup for other images.
			$('a').each(function(i) {
				if (!/\.(jpg|jpeg|png)$/i.test(this.href)) return;
				
				// Popup for the gallery items is already added, so skip these items.
				if (this.parentNode.className.indexOf('gallery-icon') !== -1) return;

				$(this).magnificPopup({
					type: 'image'
				});
			});
		}

		// Drop down (course categories).
		initDropDowns();

		// Compact lessons.
		$('article.lesson-compact').each(function() {
			IBEducator.toggleLesson(this);
		});

		// Payment form.
		var paymentRadios = IBEducator.customRadio($('ul.ib-edu-payment-method input[type="radio"]'));

		// Quiz form.
		$('.ib-edu-answers').each(function(i, answer) {
			var customRadios = IBEducator.customRadio($('input[type="radio"]', answer));
		});

		// Back to top.
		$('#back-to-top').on('click', function(e) {
			e.preventDefault();
			$('html, body').stop().animate({scrollTop: 0}, {duration: 600});
		});
	});

	// Window load.
	$(window).on('load', function() {
		// Main slideshow.
		initFWSlider();

		// Courses carousel.
		initCoursesCarousel();

		// Lecturers carousel.
		initLecturersCarousel();
	});

})(jQuery);