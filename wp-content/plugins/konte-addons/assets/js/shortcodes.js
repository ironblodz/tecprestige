jQuery( document ).ready( function( $ ) {
	"use strict";

	/**
	 * Google Maps.
	 */
	$( '.konte-map' ).each( function() {
		var $map = $( this ),
			latitude = $map.data( 'lat' ),
			longitude = $map.data( 'lng' ),
			zoom = $map.data( 'zoom' ),
			marker_icon = $map.data( 'marker' ),
			info = $map.html();

		var mapOptions = {
			zoom             : zoom,
			disableDefaultUI : true,
			scrollwheel      : false,
			navigationControl: true,
			mapTypeControl   : false,
			scaleControl     : false,
			draggable        : true,
			center           : new google.maps.LatLng( latitude, longitude ),
			mapTypeId        : google.maps.MapTypeId.ROADMAP
		};

		switch ( $map.data( 'color' ) ) {
			case 'grey':
				mapOptions.styles = [{
					"featureType": "water",
					"elementType": "geometry",
					"stylers"    : [{"color": "#e9e9e9"}, {"lightness": 17}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f5f5f5"}, {"lightness": 20}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 17}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 29}, {"weight": 0.2}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 18}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f5f5f5"}, {"lightness": 21}]
				}, {
					"featureType": "poi.park",
					"elementType": "geometry",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 21}]
				}, {
					"elementType": "labels.text.stroke",
					"stylers"    : [{"visibility": "on"}, {"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"elementType": "labels.text.fill",
					"stylers"    : [{"saturation": 36}, {"color": "#333333"}, {"lightness": 40}]
				}, {"elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f2f2f2"}, {"lightness": 19}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 20}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 17}, {"weight": 1.2}]
				}];
				break;

			case 'black':
				mapOptions.styles = [{
					"featureType": "all",
					"elementType": "labels.text.fill",
					"stylers"    : [{"saturation": 36}, {"color": "#000000"}, {"lightness": 40}]
				}, {
					"featureType": "all",
					"elementType": "labels.text.stroke",
					"stylers"    : [{"visibility": "on"}, {"color": "#000000"}, {"lightness": 16}]
				}, {
					"featureType": "all",
					"elementType": "labels.icon",
					"stylers"    : [{"visibility": "off"}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#000000"}, {"lightness": 20}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#000000"}, {"lightness": 17}, {"weight": 1.2}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 20}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 21}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#000000"}, {"lightness": 17}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#000000"}, {"lightness": 29}, {"weight": 0.2}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 18}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 16}]
				}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 19}]
				}, {
					"featureType": "water",
					"elementType": "geometry",
					"stylers"    : [{"color": "#000000"}, {"lightness": 17}]
				}];
				break;

			case 'vista-blue':
				mapOptions.styles = [{
					"featureType": "water",
					"elementType": "geometry",
					"stylers"    : [{"color": "#a0d6d1"}, {"lightness": 17}]
				}, {
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 20}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 17}]
				}, {
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 29}, {"weight": 0.2}]
				}, {
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers"    : [{"color": "#dedede"}, {"lightness": 18}]
				}, {
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers"    : [{"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"featureType": "poi",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f1f1f1"}, {"lightness": 21}]
				}, {
					"elementType": "labels.text.stroke",
					"stylers"    : [{"visibility": "on"}, {"color": "#ffffff"}, {"lightness": 16}]
				}, {
					"elementType": "labels.text.fill",
					"stylers"    : [{"saturation": 36}, {"color": "#333333"}, {"lightness": 40}]
				}, {"elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {
					"featureType": "transit",
					"elementType": "geometry",
					"stylers"    : [{"color": "#f2f2f2"}, {"lightness": 19}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 20}]
				}, {
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers"    : [{"color": "#fefefe"}, {"lightness": 17}, {"weight": 1.2}]
				}];
				break;

			case 'blue':
				mapOptions.styles = [{
					"featureType": "administrative",
					"elementType": "labels.text.fill",
					"stylers": [
						{
							"color": "#444444"
						}
					]
				}, {
					"featureType": "landscape",
					"elementType": "all",
					"stylers": [
						{
							"color": "#f2f2f2"
						}
					]
				}, {
					"featureType": "poi",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				}, {
					"featureType": "road",
					"elementType": "all",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 45
						}
					]
				}, {
					"featureType": "road.highway",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "simplified"
						}
					]
				},
				{
					"featureType": "road.arterial",
					"elementType": "labels.icon",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "transit",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				}, {
					"featureType": "water",
					"elementType": "all",
					"stylers": [
						{
							"color": "#b2d6eb"
						},
						{
							"visibility": "on"
						}
					]
				} ];
		}

		var map = new google.maps.Map( this, mapOptions );

		if ( marker_icon ) {
			var marker = new google.maps.Marker( {
				position : new google.maps.LatLng( latitude, longitude ),
				map      : map,
				icon     : marker_icon,
				animation: google.maps.Animation.DROP
			} );
		}

		if ( info ) {
			var infoWindow = new google.maps.InfoWindow( {
				content: '<div class="info_content">' + info + '</div>'
			} );

			if ( typeof marker === undefined ) {
				var marker = new google.maps.Marker( {
					position : new google.maps.LatLng( latitude, longitude ),
					map      : map,
					animation: google.maps.Animation.DROP
				} );
			}

			marker.addListener( 'click', function () {
				infoWindow.open( map, marker );
			} );
		}
	} );

	/**
	 * Carousel.
	 */
	$( '.konte-carousel:not(.konte-carousel--elementor)' )
		.on( 'init', function( event, slick ) {
			if ( slick.$slider.hasClass( 'konte-carousel--show-index' ) ) {
				slick.$slides.each( function( index, $slide ) {
					var number = ++index < 10 ? '0' + index.toString() : index.toString();
					$( '<span class="konte-dash konte-carousel__slide-index"><span class="konte-dash__line text-default"></span><span class="konte-carousel__slide-index-number">' + number + '</span></span>' ).appendTo( $slide );
				} );

				setTimeout( function() {
					slick.$slider.addClass( 'indexs-initialized' );
				}, 500 );
			}

			// Have to use setTimeout due to the bug of slick.
			setTimeout( function () {
				slick.$slider.slick('slickSetOption', 'responsive', [
					{
						breakpoint: 1199,
						settings: {
							slidesToShow: Math.min( slick.options.slidesToShow, 2 ),
							slidesToScroll: 1
						}
					},
					{
						breakpoint: 767,
						settings: {
							centerMode: true,
							slidesToShow: 1,
							slidesToScroll: 1
						}
					}
				], true );
			}, 1 );
		} )
		.slick( {
			rtl: $( document.body ).hasClass( 'rtl' ),
			prevArrow: '<button type="button" class="slick-prev konte-carousel__arrow"><span class="svg-icon icon-left icon-small"><svg><use xlink:href="#left"></use></svg></span></button>',
			nextArrow: '<button type="button" class="slick-next konte-carousel__arrow"><span class="svg-icon icon-right icon-small"><svg><use xlink:href="#right"></use></svg></span></button>'
		} );

	/**
	 * Chart.
	 */
	$( '.konte-chart:not(.konte-chart--elementor)' ).circleProgress( {
		emptyFill : 'rgba(227,231,232,1)',
		startAngle: -Math.PI / 2
	} );

	/**
	 * Post Carousel.
	 */
	$( '.konte-post-carousel:not(.konte-post-carousel--elementor)' ).slick( {
		rtl: $( document.body ).hasClass( 'rtl' ),
		responsive: [
			{
				breakpoint: 1199,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				}
			}
		]
	} );

	/**
	 * Close message box.
	 */
	$( document.body ).on( 'click', '.konte-message-box .close', function ( e ) {
		e.preventDefault();

		$( this ).parent().fadeOut();
	} );

	/**
	 * Edit vc_section stretch.
	 */
	$( document ).on( 'vc-full-width-row-single', function( event, section ) {
		if ( ! section.el.hasClass( 'section_stretch_side' ) ) {
			return;
		}

		if ( section.el.hasClass( 'section_stretch_left' ) ) {
			section.el.css( {
				paddingRight: 0,
				width: section.width + section.offset
			} );
		}

		if ( section.el.hasClass( 'section_stretch_left_no_padding' ) ) {
			section.el.css( {
				paddingRight: 0,
				paddingLeft: 0,
				width: section.width + section.offset
			} );
		}

		if ( section.el.hasClass( 'section_stretch_right' ) ) {
			section.el.css( {
				left: 'auto',
				paddingLeft: 0,
				width: section.width + section.offset
			} );
		}

		if ( section.el.hasClass( 'section_stretch_right_no_padding' ) ) {
			section.el.css( {
				left: 'auto',
				paddingleft: 0,
				paddingRight: 0,
				width: section.width + section.offset
			} );
		}
	} );

	/**
	 * Testimonial carousel.
	 */
	$( '.konte-testimonial-carousel__photos' ).slick( {
		rtl: $( document.body ).hasClass( 'rtl' ),
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true
	} );

	/**
	 * Testimonial carousel.
	 */
	$( '.konte-testimonial-carousel__testimonials' ).slick( {
		rtl: $( document.body ).hasClass( 'rtl' ),
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		dots: true
	} );

	/**
	 *  Countdown
	 */
	$( '.konte-countdown:not(.konte-countdown--elementor)' ).each( function () {
		var $el = $( this ),
			$timers = $el.find( '.timers' ),
			output = '';

		$timers.countdown( $timers.data( 'date' ), function ( event ) {
			output = '';
			var day = event.strftime( '%D' );
			for ( var i = 0; i < day.length; i++ ) {
				output += '<span>' + day[i] + '</span>';
			}
			$timers.find( '.day' ).html( output );

			output = '';
			var hour = event.strftime( '%H' );
			for ( i = 0; i < hour.length; i++ ) {
				output += '<span>' + hour[i] + '</span>';
			}
			$timers.find( '.hour' ).html( output );

			output = '';
			var minu = event.strftime( '%M' );
			for ( i = 0; i < minu.length; i++ ) {
				output += '<span>' + minu[i] + '</span>';
			}
			$timers.find( '.min' ).html( output );

			output = '';
			var secs = event.strftime( '%S' );
			for ( i = 0; i < secs.length; i++ ) {
				output += '<span>' + secs[i] + '</span>';
			}
			$timers.find( '.secs' ).html( output );
		} );
	} );

	/**
	 * Product Carousel.
	 */
	$( '.konte-product-carousel:not(.konte-product-carousel--elementor)' ).each( function() {
		var $carousel = $( this );

		initProductCarousel( $carousel );
	} );

	/**
	 * Init product carousel
	 */
	function initProductCarousel( $carousel ) {
		var $products = $carousel.find( 'ul.products' ),
			itemsCount = $( 'li.product', $products ).length,
			options = $carousel.data('slick'),
			navStyle = $carousel.data('nav_style'),
			navText = {};

		if ('angle' === navStyle) {
			navText.prev = '<button type="button" class="slick-prev product-carousel-arrow"><span class="svg-icon icon-left"><svg><use xlink:href="#left"></use></svg></span></button>';
			navText.next = '<button type="button" class="slick-next product-carousel-arrow"><span class="svg-icon icon-right"><svg><use xlink:href="#right"></use></svg></span></button>';
		} else {
			navText.prev = '<button type="button" class="slick-prev product-carousel-arrow"><span class="svg-icon icon-arrow-left"><svg><use xlink:href="#arrow-left"></use></svg></span></button>';
			navText.next = '<button type="button" class="slick-next product-carousel-arrow"><span class="svg-icon icon-arrow-left"><svg><use xlink:href="#arrow-left"></use></svg></span></button>';
		}

		options = $.extend(options, {
			dots: true,
			arrows: true,
			speed: 400,
			rtl: $(document.body).hasClass('rtl'),
			prevArrow: navText.prev,
			nextArrow: navText.next,
			responsive: [
				{
					breakpoint: 1199,
					settings: {
						slidesToShow: Math.min( 3, options.slidesToShow ),
						slidesToScroll: Math.min( 3, options.slidesToShow ),
						dots: ( Math.min( 3, options.slidesToShow ) < itemsCount )
					}
				},
				{
					breakpoint: 991,
					settings: {
						slidesToShow: Math.min( 2, options.slidesToShow ),
						slidesToScroll: Math.min( 2, options.slidesToShow ),
						dots: ( Math.min( 2, options.slidesToShow ) < itemsCount )
					}
				},
				{
					breakpoint: 320,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						dots: ( 1 < itemsCount )
					}
				}
			]
		});

		$products.css( 'opacity', 0 )
			.attr( 'dir', $(document.body).hasClass('rtl') ? 'rtl' : '' )
			.css( 'opacity', 0 )
			.on( 'init', function ( event ) {
				$( event.target ).css( 'opacity', 1 );
			} )
			.slick( options );
	}

	/**
	 * Products Masonry
	 */
	var konteMansonryActive = false;
	$( '.konte-product-masonry:not(.konte-product-masonry--elementor) ul.products' )
		.on( 'layoutComplete', function( event ) {
			var $container = $( event.currentTarget );

			if ( $container.closest( '.konte-product-masonry' ).hasClass( 'has-heading' ) ) {
				var top = $container.children( ':nth-child(5)' ).css( 'top' );
				$container.children( ':nth-child(4)' ).css( 'top', top );
			}
		} );

	initProductMansonry();

	$( window ).on( 'resize', initProductMansonry );

	/**
	 * Init product masonry
	 */
	function initProductMansonry() {
		if ( $( window ).width() < 992 ) {
			if ( konteMansonryActive ) {
				$( '.konte-product-masonry:not(.konte-product-masonry--elementor) ul.products' ).masonry( 'destroy' );
			}

			konteMansonryActive = false;
		} else if ( ! konteMansonryActive ) {
			$( '.konte-product-masonry:not(.konte-product-masonry--elementor) ul.products' ).masonry( {
				itemSelector: 'li.product',
				columnWidth: 'li.product',
				percentPosition: true,
				transitionDuration: 0,
				originLeft: ! $( document.body ).hasClass( 'rtl' )
			} )
			.imagesLoaded( function( instance ) {
				$( instance.elements ).masonry( 'layout' );
			} );

			konteMansonryActive = true;
		}
	}


	/**
	 * Tabs
	 */
	$( '.konte-tabs:not( .konte-tabs--elementor )' ).on( 'click', '.konte-tabs__nav li', function() {
		var $tab = $( this ),
			index = $tab.data( 'target' ),
			$panels = $tab.closest( '.konte-tabs' ).find( '.konte-tabs__panels' ),
			$panel = $panels.find( '.konte-tabs__panel[data-panel="' + index + '"]' );

		if ( $tab.hasClass( 'active' ) ) {
			return;
		}

		$tab.addClass( 'active' ).siblings( 'li.active' ).removeClass( 'active' );

		if ( $panel.length ) {
			$panel.addClass( 'active' ).siblings( '.konte-tabs__panel.active' ).removeClass( 'active' );
		}
	} );

	/**
	 * Product tabs
	 *
	 * Uses tab switching of .konte-tabs.
	 * In this part, we only handle ajax request to load products dynamically
	 */
	$( '.konte-product-tabs:not( .konte-product-tabs--elementor )' ).on( 'click', '.konte-tabs__nav li', function() {
		var $tab = $( this ),
			atts = $tab.data( 'atts' ),
			index = $tab.data( 'target' ),
			$panels = $tab.parent().next( '.konte-tabs__panels' ),
			$panel = $panels.find( '.konte-tabs__panel[data-panel="' + index + '"]' ),
			ajax_url = wc_add_to_cart_params ? wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'konte_products_shortcode') : konteData.ajax_url;

		if ( $panel.length ) {
			return;
		}

		if ( !atts ) {
			return;
		}

		$panels.addClass( 'loading' );

		$.post( ajax_url, {
			action: 'konte_products_shortcode',
			atts  : atts
		}, function( response ) {
			if ( !response.success ) {
				$panels.removeClass( 'loading' );
				return;
			}

			var $newPanel = $panels.children( '.konte-tabs__panel' ).first().clone();

			$newPanel.html( response.data );
			$newPanel.attr( 'data-panel', index );
			$newPanel.addClass( 'active' );
			$newPanel.appendTo( $panels );
			$newPanel.siblings( '.konte-tabs__panel.active' ).removeClass( 'active' );


			if ( $tab.closest( '.konte-product-tabs' ).hasClass( 'tabs-carousel' ) ) {
				initProductCarousel( $newPanel );
			}

			$(document.body).trigger('konte_products_loaded', [$newPanel.find( 'li.product' ), false]);

			setTimeout( function() {
				$panels.removeClass( 'loading' );
			}, 700 );
		} ).fail( function() {
			window.location.herf = $tab.data( 'href' );
		} );;
	} );

	/**
	 * Product carousel 2
	 */
	$( '.konte-product-carousel2:not( .konte-product-carousel2--elementor )' ).each( function() {
		var $carousel = $( this );

		var options = $carousel.data('slick'),
			navText = {};

		navText.prev = '<button type="button" class="slick-prev"><span class="svg-icon icon-left"><svg><use xlink:href="#left"></use></svg></span></button>';
		navText.next = '<button type="button" class="slick-next"><span class="svg-icon icon-right"><svg><use xlink:href="#right"></use></svg></span></button>';

		options = $.extend( options, {
			dots: false,
			arrows: true,
			speed: 400,
			slidesToShow: 1,
			slidesToScroll: 1,
			rtl: $(document.body).hasClass('rtl'),
			prevArrow: navText.prev,
			nextArrow: navText.next,
			variableWidth: true,
			touchThreshold: 10,
			responsive: [
				{
					breakpoint: 1279,
					settings: {
						slidesToShow: 1
					}
				}
			]
		});

		$carousel.find( 'ul.products' )
			.attr( 'dir', $(document.body).hasClass('rtl') ? 'rlt' : '' )
			.css( 'opacity', 0 )
			.on( 'init', function ( event ) {
				$( event.target ).css( 'opacity', 1 );
			} )
			.slick( options );
	} );

	/**
	 * Instagram Carousel
	 */
	$( '.konte-instagram-carousel:not(.konte-instagram-carousel--elementor)' ).each( function() {
		var $carousel = $( this );
		var options = $carousel.data( 'slick' );

		options.prevArrow = '<button type="button" class="slick-prev konte-instagram-carousel__arrow"><span class="svg-icon icon-left icon-small"><svg><use xlink:href="#left"></use></svg></span></button>';
		options.nextArrow = '<button type="button" class="slick-next konte-instagram-carousel__arrow"><span class="svg-icon icon-right icon-small"><svg><use xlink:href="#right"></use></svg></span></button>';

		options = $.extend( options, {
			appendDots: $carousel,
			responsive: [
				{
					breakpoint: 1200,
					settings: {
						slidesToShow : Math.min( 5, options.slidesToShow ),
						slidesToScroll : Math.min( 5, options.slidesToScroll ),
						arrows: false
					}
				},
				{
					breakpoint: 992,
					settings: {
						slidesToShow : Math.min( 3, options.slidesToShow ),
						slidesToScroll : Math.min( 3, options.slidesToScroll ),
						arrows: false
					}
				},
				{
					breakpoint: 768,
					settings: {
						slidesToShow : Math.min( 2, options.slidesToShow ),
						slidesToScroll : Math.min( 2, options.slidesToScroll ),
						arrows: false
					}
				}
			]
		} );

		$carousel.find( '.konte-instagram__list' ).slick( options );
	})
} );
