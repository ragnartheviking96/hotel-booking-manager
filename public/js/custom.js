(function ($) {
	"use strict";

	jQuery(document).ready(function ($) {
		//One page nav 
			$('.main_menu a').bind('click', function (event) {
				var $anchor = $(this);
				$('html').stop().animate({
					scrollTop: $($anchor.attr('href')).offset().top - 0
				}, 1000);
				event.preventDefault();
			});
		
		//sticky menu
		$(window).on('scroll', function () {

			if ($(this).scrollTop() > 100) {
				$('.position_top').addClass('sticky');
			} else {
				$('.position_top').removeClass('sticky');
			}
		});

		//Preloader js{ 
			$('.preloader').fadeOut();
			$('.status-mes').delay(350).fadeOut('slow'); 
		
		
		/*------------------------------------
		 search option
		------------------------------------- */ 
			$('.search-option').hide();
			$(".main-search").on('click', function(){
				$('.search-option').animate({
					height:'toggle',
				});
			});
			
		// Buy-cart option  
		$('.search-option').hide();
		$(".main-search").on('click', function(){
			$('.search-option').animate({
				height:'toggle',
			});
		});
		
		
		/* BACK TO TOP BUTTON */
		$(window).scroll(function () {
			if ($(this).scrollTop() > 300) {
				$('.scrollToTop').fadeIn();
			} else {
				$('.scrollToTop').fadeOut();
			}
		});
		//Click event to scroll to top
		$('.scrollToTop').click(function () {
			$('html, body').animate({
				scrollTop: 0
			}, 800);
			return false;
		});

		//.mobile menu
		$('.main_menu').slicknav({
			label: '',
			prependTo: '#mobilenav'
		});

		// slider carousel  
		$('.whbmt_home_slider').owlCarousel({
			loop: true,
			items: 1,
			dots: false,
			animateOut: 'fadeOut',
            animateIn: 'fadeIn',
			nav: true,
			active: true,
            smartSpeed: 1000,
            autoplay: 5000,
			navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
			responsive: {
				0: {
					items: 1,
					nav: false,
				},
				600: {
					items: 1,
					nav: false,
				},
				768: {
					items: 1,
				},
				1000: {
					items: 1
				}
			}
		});

		// Cartbox-popup
		$('#whbmt_cart_box_popup').hide();
		$(".buy_now_btn").on('click', function(){
			$('#whbmt_cart_box_popup').animate({
				height:'toggle',
			});
		});
		// search option
			$('.search-option').hide();
			$(".main_search").on('click', function(){
				$('.search-option').animate({
					height:'toggle',
				});
			});
		// slider blog_slider carousel  
		$('.whbmt_blog_carousel').owlCarousel({
			loop: true,
			margin: 30,
			autoplay: false,
			autoplayTimeout: 2500,
			autoplaySpeed: 2000,
			nav: true,
			items: 3,
			dots: false,
			navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
			responsive: {
				0: {
					items: 1,
					nav: false
				},
				768: {
					items: 2,
					nav: false
				},
				991: {
					items: 2,
					nav: false
				},
				1000: {
					items: 3
				}
			}
		});

		$('.whbmt_property_carousel').owlCarousel({
			loop: true,
			margin: 30,
			autoplay: false,
			autoplayTimeout: 2500,
			autoplaySpeed: 2000,
			nav: true,
			items: 3,
			dots: false,
			navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
			responsive: {
				0: {
					items: 1,
					nav: false
				},
				768: {
					items: 2,
					nav: false
				},
				991: {
					items: 2,
					nav: false
				},
				1000: {
					items: 3
				}
			}
		});

		$('.whbmt_room_list_carousel').owlCarousel({
			loop: true,
			margin: 30,
			autoplay: false,
			autoplayTimeout: 2500,
			autoplaySpeed: 2000,
			nav: true,
			items: 3,
			dots: false,
			navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
			responsive: {
				0: {
					items: 1,
					nav: false
				},
				768: {
					items: 2,
					nav: false
				},
				991: {
					items: 2,
					nav: false
				},
				1000: {
					items: 5
				}
			}
		});
		//  filter js
		$(".whbmt_top_bar .whbmt_top_bar_tag .bg_tab").on('click', function () {
			$(".whbmt_top_bar .whbmt_top_bar_tag .bg_tab").removeClass("active");
			$(this).addClass("active");

		});
		//  datepicker js
		$( "#datepicker" ).datepicker();
		$( "#datepickerit" ).datepicker();
	});

})(jQuery);