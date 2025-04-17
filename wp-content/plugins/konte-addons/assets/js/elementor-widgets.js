class KonteTabsHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				tab: '.konte-tab__title',
				panel: '.konte-tab__content'
			},
			classes: {
				active: 'konte-tab--active',
			},
			showFn: 'show',
			hideFn: 'hide',
			toggleSelf: false,
			autoExpand: true,
			hidePrevious: true
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$tabs: this.findElement( selectors.tab ),
			$panels: this.findElement( selectors.panel )
		};
	}

	activateDefaultTab() {
		const settings = this.getSettings();

		if ( ! settings.autoExpand || 'editor' === settings.autoExpand && ! this.isEdit ) {
			return;
		}

		const defaultActiveTab = this.getEditSettings( 'activeItemIndex' ) || 1,
			originalToggleMethods = {
				showFn: settings.showFn,
				hideFn: settings.hideFn
			};

		this.setSettings( {
			showFn: 'show',
			hideFn: 'hide'
		} );

		this.changeActiveTab( defaultActiveTab );

		this.setSettings( originalToggleMethods );
	}

	changeActiveTab( tabIndex ) {
		const settings = this.getSettings(),
			$tab = this.elements.$tabs.filter( '[data-tab="' + tabIndex + '"]' ),
			$panel = this.elements.$panels.filter( '[data-tab="' + tabIndex + '"]' ),
			isActive = $tab.hasClass( settings.classes.active );

		if ( ! settings.toggleSelf && isActive ) {
			return;
		}

		if ( ( settings.toggleSelf || ! isActive ) && settings.hidePrevious ) {
			this.elements.$tabs.removeClass( settings.classes.active );
			this.elements.$panels.removeClass( settings.classes.active )[settings.hideFn]();
		}

		if ( ! settings.hidePrevious && isActive ) {
			$tab.removeClass( settings.classes.active );
			$panel.removeClass( settings.classes.active )[settings.hideFn]();
		}

		if ( ! isActive ) {
			$tab.addClass( settings.classes.active );
			$panel.addClass( settings.classes.active )[settings.showFn]();
		}
	}

	bindEvents() {
		this.elements.$tabs.on( {
			keydown: ( event ) => {
				if ( 'Enter' !== event.key ) {
					return;
				}

				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
			},
			click: ( event ) => {
				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
			}
		} );
	}

	onInit() {
		super.onInit();

		this.activateDefaultTab();
	}
}

class KonteCircleChartHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				chart: '.konte-chart--elementor',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$chart: this.findElement( selectors.chart ),
		};
	}

	getCircleProgressOptions() {
		const settings = this.getElementSettings();

		return {
			startAngle: -Math.PI / 2,
			value: settings.value.size/100,
			size: settings.size,
			emptyFill: settings.empty_color ? settings.empty_color : '#E3E7E8',
			fill: {color: settings.color ? settings.color : '#161619'},
			thickness: settings.thickness
		};
	}

	onInit() {
		super.onInit();

		this.elements.$chart.circleProgress( this.getCircleProgressOptions() );

		elementorFrontend.waypoint( this.elements.$chart, () => {
			this.elements.$chart.circleProgress();
		} );
	}

	onElementChange( propertyName ) {
		if ( 'color' === propertyName || 'empty_color' === propertyName ) {
			this.elements.$chart.circleProgress(); // Redraw
		} else if ( 'size' === propertyName ) {
			this.elements.$chart.circleProgress( { size: this.getElementSettings( 'size' ) } );
		} else if ( 'label' === propertyName ) {
			// Do nothing.
		}
	}
}

class KonteCountDownHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				timer: '.timers',
				day: '.day',
				hour: '.hour',
				min: '.min',
				sec: '.secs',
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$timer: this.$element.find( selectors.timer ),
			$day: this.$element.find( selectors.day ),
			$hour: this.$element.find( selectors.hour ),
			$min: this.$element.find( selectors.min ),
			$sec: this.$element.find( selectors.sec ),
		};
	}

	updateCounter( event ) {
		let output = '';

		const day = event.strftime( '%D' );
		for ( let i = 0; i < day.length; i++ ) {
			output += '<span>' + day[i] + '</span>';
		}
		this.elements.$day.html( output );

		output = '';
		const hour = event.strftime( '%H' );
		for ( let i = 0; i < hour.length; i++ ) {
			output += '<span>' + hour[i] + '</span>';
		}
		this.elements.$hour.html( output );

		output = '';
		const minu = event.strftime( '%M' );
		for ( let i = 0; i < minu.length; i++ ) {
			output += '<span>' + minu[i] + '</span>';
		}
		this.elements.$min.html( output );

		output = '';
		const secs = event.strftime( '%S' );
		for ( let i = 0; i < secs.length; i++ ) {
			output += '<span>' + secs[i] + '</span>';
		}
		this.elements.$sec.html( output );
	}

	onInit() {
		super.onInit();

		const endDate = this.elements.$timer.data( 'date' );

		this.elements.$timer.countdown( endDate, ( event ) => this.updateCounter( event ) );
	}
}

class KonteSwiperCarouselHandler extends elementorModules.frontend.handlers.SwiperBase {
	getDefaultSettings() {
		return {
			selectors: {
				carousel: '.swiper-container',
				slideContent: '.swiper-slide',
			}
		}
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		const elements = {
			$swiperContainer: this.$element.find( selectors.carousel ),
		};

		elements.$slides = elements.$swiperContainer.find( selectors.slideContent );

		return elements;
	}

	getCarouselOptions() {
		const elementSettings = this.getElementSettings(),
				slidesToShow = parseInt(elementSettings.slides_to_show) || 3,
				slidesToScroll = parseInt(elementSettings.slides_to_scroll_mobile) || parseInt(elementSettings.slides_to_scroll_tablet) || parseInt(elementSettings.slides_to_scroll) || 1,
				isSingleSlide = 1 === slidesToShow,
				defaultLGDevicesSlidesCount = isSingleSlide ? 1 : 2,
				swiperOptions = this.getSettings( 'swiperOptions' ),
				elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

		let carouselSettings = {
			slidesPerView: slidesToShow,
			loop: 'yes' === elementSettings.infinite,
			speed: elementSettings.speed,
			handleElementorBreakpoints: true,
			breakpoints: {},
		};

		carouselSettings.breakpoints[ elementorBreakpoints.mobile.value ] = {
			slidesPerView: parseInt(elementSettings.slides_to_show_mobile) || 1,
			slidesPerGroup: parseInt(elementSettings.slides_to_scroll_mobile) || 1,
		};

		carouselSettings.breakpoints[ elementorBreakpoints.tablet.value ] = {
			slidesPerView: parseInt(elementSettings.slides_to_show_tablet) || defaultLGDevicesSlidesCount,
			slidesPerGroup: parseInt(elementSettings.slides_to_scroll_tablet) || 1,
		};

		if ( 'yes' === elementSettings.autoplay ) {
			carouselSettings.autoplay = {
				delay: elementSettings.autoplay_speed
			};
		}

		if ( elementSettings.image_spacing_custom ) {
			carouselSettings.spaceBetween = elementSettings.image_spacing_custom.size;
		}

		if ( ! isSingleSlide ) {
			carouselSettings.slidesPerGroup = slidesToScroll;
		}

		if ( 'arrows' == elementSettings.navigation || 'both' == elementSettings.navigation ) {
			carouselSettings.navigation = {
				prevEl: this.$element.find( '.konte-carousel-navigation--prev' ).get(0),
				nextEl: this.$element.find( '.konte-carousel-navigation--next' ).get(0),
			};
		}

		if ( 'dots' == elementSettings.navigation || 'both' == elementSettings.navigation ) {
			carouselSettings.pagination = {
				el: this.$element.find( '.konte-carousel__pagination' ).get(0),
				type: 'bullets',
				clickable: true,
				renderBullet: ( index, className ) => {
					return '<span class="' + className + '">' + (index + 1) + '</span>';
				},
			};
		}

		if ( swiperOptions ) {
			carouselSettings = _.extend( swiperOptions, carouselSettings );
		}

		if ( carouselSettings.slidesPerView === 'auto' ) {
			let slidesPerViewTablet = elementSettings.slides_to_show_tablet ? parseInt(elementSettings.slides_to_show_tablet) : 'auto';
			slidesPerViewTablet = 'auto' === slidesPerViewTablet ? slidesPerViewTablet : +slidesPerViewTablet;

			let slidesPerViewMobile = elementSettings.slides_to_show_mobile ? parseInt(elementSettings.slides_to_show_mobile) : slidesPerViewTablet;
			slidesPerViewMobile = 'auto' === slidesPerViewMobile ? slidesPerViewMobile : +slidesPerViewMobile;

			carouselSettings.breakpoints[ elementorBreakpoints.tablet.value ].slidesPerView = slidesPerViewTablet;
			carouselSettings.breakpoints[ elementorBreakpoints.mobile.value ].slidesPerView = slidesPerViewMobile;
		}

		if ( 'products' === this.getSettings( 'carouselContent' ) ) {
			const carouselEvents = {};

			if ( this.isEdit ) {
				carouselEvents.beforeInit = () => {
					jQuery( document.body ).trigger( 'konte_products_loaded', [this.elements.$slides, false] );
				};
			}

			if ( ! _.isEmpty( carouselEvents ) ) {
				carouselSettings.on = carouselEvents;
			}
		}

		return carouselSettings;
	}

	async onInit( ...args ) {
		super.onInit( ...args );

		if ( ! this.elements.$swiperContainer.length || 2 > this.elements.$slides.length ) {
			return;
		}

		const Swiper = elementorFrontend.utils.swiper;

		this.swiper = await new Swiper( this.elements.$swiperContainer, this.getCarouselOptions() );

		this.elements.$swiperContainer.data( 'swiper', this.swiper );

		if ( 'yes' === this.getElementSettings( 'pause_on_hover' ) ) {
			this.togglePauseOnHover( true );
		}
	}

	onElementChange( propertyName ) {
		switch ( propertyName ) {
			case 'pause_on_hover':
				const pauseable = this.getElementSettings( 'pause_on_hover' );

				this.togglePauseOnHover( 'yes' === pauseable );
				break;

			case 'autoplay_speed':
				this.swiper.params.autoplay.delay = this.getElementSettings( 'autoplay_speed' );
				this.swiper.update();
				break;

			case 'speed':
				this.swiper.params.speed = this.getElementSettings( 'speed' );
				this.swiper.update();
				break;

			case 'image_spacing_custom':
				this.swiper.params.spaceBetween = this.getElementSettings( 'image_spacing_custom' ).size || 0;
				this.swiper.update();
				break;
		}
	}

	onEditSettingsChange( propertyName ) {
		if ( 'activeItemIndex' === propertyName ) {
			this.swiper.slideToLoop( this.getEditSettings( 'activeItemIndex' ) - 1 );
		}
	}
}

class KonteTestimonialSlideshowHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.konte-testimonial-slideshow',
				images: '.konte-testimonial-slideshow__photos',
				imageSlide: '.konte-testimonial-slideshow__photo',
				testimonials: '.konte-testimonial-slideshow__quotes',
				testimonialSlide: '.konte-testimonial-slideshow__quote',
			}
		}
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		const elements = {
			$container: this.findElement( selectors.container ),
			$imagesContainer: this.findElement( selectors.images ),
			$testimonialsContainer: this.findElement( selectors.testimonials ),
		};

		elements.$imageSlides = elements.$imagesContainer.find( selectors.imageSlide );
		elements.$testimonialSlide = elements.$testimonialsContainer.find( selectors.testimonialSlide );

		return elements;
	}

	getCarouselOptions( container ) {
		const elementSettings = this.getElementSettings(),
			swiperOptions = this.getSettings( 'swiperOptions' );

		let carouselSettings = {
			slidesPerView: 1,
			slidesPerGroup: 1,
			loop: 'yes' === elementSettings.infinite,
			speed: elementSettings.speed,
		};

		if ( 'yes' === elementSettings.autoplay ) {
			carouselSettings.autoplay = {
				delay: elementSettings.autoplay_speed
			};
		}

		if ( 'testimonials' === container ) {
			const el = this.findElement( '.konte-carousel__pagination' ).get(0);

			carouselSettings.pagination = {
				el: el,
				type: 'bullets',
				clickable: true,
				renderBullet: ( index, className ) => {
					return '<span class="' + className + '">' + (index + 1) + '</span>';
				},
			};
			carouselSettings.spaceBetween = 20;
		} else {
			carouselSettings.effect = 'fade';
			carouselSettings.fadeEffect = {
				crossFade: true
			};
		}

		if ( swiperOptions ) {
			carouselSettings = _.extend( carouselSettings, swiperOptions );
		}

		return carouselSettings;
	}

	async onInit( ...args ) {
		super.onInit( ...args );

		if ( ! this.elements.$testimonialsContainer.length || 2 > this.elements.$testimonialSlide.length ) {
			return;
		}

		const Swiper = elementorFrontend.utils.swiper;

		this.imageSwiper = await new Swiper( this.elements.$imagesContainer, this.getCarouselOptions() );
		this.testimonialSwiper = await new Swiper( this.elements.$testimonialsContainer, this.getCarouselOptions( 'testimonials' ) );

		this.elements.$imagesContainer.data( 'swiper', this.imageSwiper );
		this.elements.$testimonialsContainer.data( 'swiper', this.testimonialSwiper );

		this.syncSlideshow();

		if ( 'yes' === this.getElementSettings( 'pause_on_hover' ) ) {
			this.togglePauseOnHover( true );
		}
	}

	syncSlideshow() {
		this.testimonialSwiper.on( 'slideChange', () => {
			this.imageSwiper.slideTo( this.testimonialSwiper.activeIndex );
		} );

		this.imageSwiper.on( 'slideChange', () => {
			this.testimonialSwiper.slideTo( this.imageSwiper.activeIndex );
		} );
	}

	togglePauseOnHover( toggleOn ) {
		if ( toggleOn ) {
			this.elements.$container.on( {
				mouseenter: () => {
					this.imageSwiper.autoplay.stop();
					this.testimonialSwiper.autoplay.stop();
				},
				mouseleave: () => {
					this.imageSwiper.autoplay.start();
					this.testimonialSwiper.autoplay.start();
				},
			} );
		} else {
			this.elements.$container.off( 'mouseenter mouseleave' );
		}
	}

	onElementChange( propertyName ) {
		switch ( propertyName ) {
			case 'pause_on_hover':
				const pauseable = this.getElementSettings( 'pause_on_hover' );

				this.togglePauseOnHover( 'yes' === pauseable );
				break;

			case 'autoplay_speed':
				this.imageSwiper.params.autoplay.delay = this.getElementSettings( 'autoplay_speed' );
				this.testimonialSwiper.params.autoplay.delay = this.getElementSettings( 'autoplay_speed' );
				this.imageSwiper.update();
				this.testimonialSwiper.update();
				break;

			case 'speed':
				this.imageSwiper.params.speed = this.getElementSettings( 'speed' );
				this.testimonialSwiper.params.speed = this.getElementSettings( 'speed' );
				this.imageSwiper.update();
				this.testimonialSwiper.update();
				break;
		}
	}

	onEditSettingsChange( propertyName ) {
		if ( 'activeItemIndex' === propertyName ) {
			this.testimonialSwiper.slideToLoop( this.getEditSettings( 'activeItemIndex' ) - 1 );
			this.imageSwiper.slideToLoop( this.getEditSettings( 'activeItemIndex' ) - 1 );
		}
	}
}

class KonteProductsTabsHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				tab: '.konte-tabs__nav li',
				panel: '.konte-tabs__panel',
				products: 'ul.products',
			},
			carousel: false,
			swiperOptions: {},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$tabs: this.findElement( selectors.tab ),
			$panels: this.findElement( selectors.panel ),
			$products: this.findElement( selectors.products ),
		};
	}

	getCarouselOptions( tabIndex ) {
		const elementSettings = this.getElementSettings(),
				slidesToShow = +elementSettings.slides_to_show || 3,
				slidesToScroll = +elementSettings.slides_to_scroll_mobile || +elementSettings.slides_to_scroll_tablet || +elementSettings.slides_to_scroll || 1,
				isSingleSlide = 1 === slidesToShow,
				defaultLGDevicesSlidesCount = isSingleSlide ? 1 : 2,
				swiperOptions = this.getSettings( 'swiperOptions' ),
				elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
				$panel = this.getPanel( tabIndex );

		let carouselSettings = {
			slidesPerView: slidesToShow,
			loop: 'yes' === elementSettings.infinite,
			speed: elementSettings.speed,
			handleElementorBreakpoints: true,
			wrapperClass: 'products',
			slideClass: 'product',
			breakpoints: {},
		};

		carouselSettings.breakpoints[ elementorBreakpoints.mobile.value ] = {
			slidesPerView: +elementSettings.slides_to_show_mobile || 1,
			slidesPerGroup: +elementSettings.slides_to_scroll_mobile || 1,
		};

		carouselSettings.breakpoints[ elementorBreakpoints.tablet.value ] = {
			slidesPerView: +elementSettings.slides_to_show_tablet || defaultLGDevicesSlidesCount,
			slidesPerGroup: +elementSettings.slides_to_scroll_tablet || 1,
		};

		if ( 'yes' === elementSettings.autoplay ) {
			carouselSettings.autoplay = {
				delay: elementSettings.autoplay_speed
			};
		}

		if ( elementSettings.image_spacing_custom ) {
			carouselSettings.spaceBetween = elementSettings.image_spacing_custom.size;
		}

		if ( ! isSingleSlide ) {
			carouselSettings.slidesPerGroup = slidesToScroll;
		}

		if ( 'arrows' == elementSettings.navigation || 'both' == elementSettings.navigation ) {
			carouselSettings.navigation = {
				prevEl: $panel.find( '.konte-carousel-navigation--prev' ).get(0),
				nextEl: $panel.find( '.konte-carousel-navigation--next' ).get(0),
			};
		}

		if ( 'dots' == elementSettings.navigation || 'both' == elementSettings.navigation ) {
			carouselSettings.pagination = {
				el: $panel.find( '.konte-carousel__pagination' ).get(0),
				type: 'bullets',
				clickable: true,
				renderBullet: ( index, className ) => {
					return '<span class="' + className + '">' + (index + 1) + '</span>';
				},
			};
		}

		if ( swiperOptions ) {
			carouselSettings = _.extend( carouselSettings, swiperOptions );
		}

		if ( 'products' === this.getSettings( 'carouselContent' ) ) {
			const carouselEvents = {};

			if ( this.isEdit ) {
				carouselEvents.beforeInit = () => {
					jQuery( document.body ).trigger( 'konte_products_loaded', [this.elements.$slides, false] );
				};
			}

			if ( ! _.isEmpty( carouselEvents ) ) {
				carouselSettings.on = carouselEvents;
			}
		}

		return carouselSettings;
	}

	activateDefaultTab() {
		const settings = this.getSettings(),
			defaultActiveTab = this.getEditSettings( 'activeItemIndex' ) || 1;

		if ( this.isEdit ) {
			jQuery( document.body ).trigger( 'konte_products_loaded', [this.elements.$products.find( 'li.product' ), false] );
		}

		this.changeActiveTab( defaultActiveTab );

		if ( settings.carousel ) {
			this.initCarousel( defaultActiveTab );
		}
	}

	changeActiveTab( tabIndex ) {
		if ( this.isActiveTab( tabIndex ) ) {
			return;
		}

		const $tab = this.getTab( tabIndex ),
			$panel = this.getPanel( tabIndex );

		$tab.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );

		if ( $panel.length ) {
			$panel.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
		} else {
			this.loadNewPanel( tabIndex );
		}
	}

	isActiveTab( tabIndex ) {
		return this.getTab( tabIndex ).hasClass( 'active' );
	}

	hasTabPanel( tabIndex ) {
		return this.getPanel( tabIndex ).length;
	}

	getTab( tabIndex ) {
		return this.elements.$tabs.filter( '[data-target="' + tabIndex + '"]' );
	}

	getPanel( tabIndex ) {
		return this.elements.$panels.filter( '[data-panel="' + tabIndex + '"]' );
	}

	loadNewPanel( tabIndex ) {
		if ( this.hasTabPanel( tabIndex ) ) {
			return;
		}

		const settings = this.getSettings(),
			elementSettings = this.getElementSettings(),
			isEdit = this.isEdit,
			$tab = this.elements.$tabs.filter( '[data-target="' + tabIndex + '"]' ),
			$panelsContainer = this.elements.$panels.first().parent(),
			atts = $tab.data( 'atts' ),
			ajax_url = wc_add_to_cart_params ? wc_add_to_cart_params.wc_ajax_url.toString().replace(  '%%endpoint%%', 'konte_get_products_tab' ) : konteData.ajax_url;

		if ( ! atts ) {
			return;
		}

		$panelsContainer.addClass( 'loading' );

		jQuery.post( ajax_url, {
			action: 'konte_get_products_tab',
			atts  : atts,
			carousel: ! settings.carousel ? 0 : {
				dots: 'both' == elementSettings.navigation || 'dots' === elementSettings.navigation,
				arrows: 'both' == elementSettings.navigation || 'arrows' === elementSettings.navigation ? elementSettings.arrow_type : false,
			},
		}, ( response ) => {
			if ( !response.success ) {
				$panelsContainer.removeClass( 'loading' );
				return;
			}

			const $newPanel = this.elements.$panels.first().clone();

			$newPanel.html( response.data );
			$newPanel.attr( 'data-panel', tabIndex );
			$newPanel.addClass( 'active' );
			$newPanel.appendTo( $panelsContainer );
			$newPanel.siblings( '.active' ).removeClass( 'active' );

			this.elements.$panels = this.elements.$panels.add( $newPanel );

			if ( settings.carousel ) {
				if ( isEdit ) {
					jQuery( document.body ).trigger( 'konte_products_loaded', [$newPanel.find( 'li.product' ), false] );
				}

				this.initCarousel( tabIndex );
			}

			if ( ! isEdit ) {
				jQuery( document.body ).trigger( 'konte_products_loaded', [$newPanel.find( 'li.product' ), false] );
			}

			setTimeout( () => {
				$panelsContainer.removeClass( 'loading' );
			}, 500 );
		} );
	}

	bindEvents() {
		this.elements.$tabs.on( {
			click: ( event ) => {
				event.preventDefault();

				this.changeActiveTab( event.currentTarget.getAttribute( 'data-target' ) );
			}
		} );
	}

	onInit( ...args ) {
		super.onInit( ...args );

		this.activateDefaultTab();
	}

	async initCarousel( tabIndex ) {
		const Swiper = elementorFrontend.utils.swiper;
		const $carousel = this.getPanel( tabIndex ).find( '.swiper-container' );

		const swiperInstance = await new Swiper( $carousel, this.getCarouselOptions( tabIndex ) );

		$carousel.data( 'swiper', swiperInstance );

		if ( 'yes' === this.getElementSettings( 'pause_on_hover' ) ) {
			this.togglePauseOnHover( tabIndex, true );
		}
	}

	togglePauseOnHover( tabIndex, toggleOn ) {
		const $carousel = this.getPanel( tabIndex ).find( '.swiper-container' );

		if ( toggleOn ) {
			const swiper = $carousel.data( 'swiper' );

			$carousel.on( {
				mouseenter: () => {
					swiper.autoplay.stop();
				},
				mouseleave: () => {
					swiper.autoplay.start();
				}
			} );
		} else {
			$carousel.off( 'mouseenter mouseleave' );
		}
	}

	onElementChange( propertyName ) {
		switch ( propertyName ) {
			case 'pause_on_hover':
				const pauseable = this.getElementSettings( 'pause_on_hover' );

				this.elements.$panels.each( ( index, panel ) => {
					this.togglePauseOnHover( panel.getAttribute( 'data-panel' ), 'yes' === pauseable );
				} );
				break;

			case 'autoplay_speed':
				this.elements.$panels.each( ( index, panel ) => {
					const swiper = this.getPanel( panel.getAttribute( 'data-panel' ) ).find( '.swiper-container' ).data( 'swiper' );

					swiper.params.autoplay.delay = this.getElementSettings( 'autoplay_speed' );
					swiper.update();
				} );
				break;

			case 'speed':
				this.elements.$panels.each( ( index, panel ) => {
					const swiper = this.getPanel( panel.getAttribute( 'data-panel' ) ).find( '.swiper-container' ).data( 'swiper' );

					swiper.params.speed = this.getElementSettings( 'speed' );
					swiper.update();
				} );
				break;
		}
	}
}

class KonteGoogleMapHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				map: '.konte-google-map',
				markers: '.konte-google-map__markers'
			}
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$map: this.$element.find( selectors.map ),
			$markers: this.$element.find( selectors.markers )
		};
	}

	hasLocation( address ) {
		this.locations = this.locations || [];
		address = address.trim();

		let found = this.locations.filter( location => location.address === address );

		if ( ! found.length ) {
			return false;
		}

		return found[0].location;
	}

	getLocation( address ) {
		this.locations = this.locations || [];
		address = address.trim();

		if ( ! address ) {
			return false;
		}

		let location = this.hasLocation( address );

		if ( location ) {
			return location.location;
		}

		return new Promise( (resolve, reject) => {
			const geocoder = new google.maps.Geocoder;

			geocoder.geocode( { address: address }, (results, status) => {
				if ( status === 'OK' ) {
					if ( results[0] ) {
						this.locations.push( {
							address: address,
							location: results[0].geometry.location
						} );

						resolve( results[0].geometry.location );
					} else {
						reject( 'No address found' );
					}
				} else {
					reject( status );
				}
			} )
		} );
	}

	getMapStyleOption() {
		let styles = [];

		switch ( this.getElementSettings( 'color' ) ) {
			case 'grey':
				styles = [{
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
					styles = [{
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
					break;

			case 'inverse':
				styles = [{
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
				styles = [{
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
		}

		return styles;
	}

	async getMapOptions() {
		const settings = this.getElementSettings();
		const location = this.elements.$map.data( 'location' );
		const options = {
			scrollwheel      : false,
			navigationControl: true,
			mapTypeControl   : false,
			scaleControl     : false,
			streetViewControl: false,
			draggable        : true,
			mapTypeId        : google.maps.MapTypeId.ROADMAP,
			zoom             : settings.zoom.size
		};

		if ( location ) {
			options.center = location;
		} else {
			let latlng = settings.latlng.split( ',' ).map( parseFloat );

			if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
				options.center = {
					lat: latlng[0],
					lng: latlng[1]
				};
			}
		}

		if ( ! options.center ) {
			options.center = await this.getLocation( settings.address );
		}

		let styles = this.getMapStyleOption( this.getElementSettings( 'color' ) );

		if ( styles ) {
			options.styles = styles;
		}

		return options;
	}

	async initMap() {
		if ( ! this.elements.$map.length ) {
			return;
		}

		if ( this.map ) {
			return;
		}

		this.map = new google.maps.Map( this.elements.$map.get( 0 ), await this.getMapOptions() );
	}

	async setMapLocation() {
		if ( ! this.isEdit ) {
			return;
		}

		if ( ! this.elements.$map.length ) {
			return;
		}

		if ( typeof this.map === 'undefined' ) {
			return;
		}

		const settings = this.getElementSettings();
		let location = {};
		let latlng = settings.latlng.split( ',' ).map( parseFloat );

		if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
			location = {
				lat: latlng[0],
				lng: latlng[1]
			};
		} else {
			location = await this.getLocation( settings.address );
		}

		if ( location ) {
			this.map.setCenter( location );
		}
	}

	clearMarkers() {
		if ( this.markers ) {
			for ( let i in this.markers ) {
				this.markers[i].setMap( null );
			}
		}

		this.markers = [];
	}

	async updateLocationList() {
		if ( ! this.elements.$markers.length ) {
			return;
		}

		const addresses = {
			name: [],
			latlng: []
		};

		this.elements.$markers.children().each( ( index, marker ) => {
			let data = JSON.parse( marker.dataset.marker );
			let address = data.address;

			if ( ! address ) {
				return;
			}

			if ( this.hasLocation( address ) ) {
				return;
			}

			let latlng = data.latlng.split( ',' ).map( parseFloat );

			if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
				return;
			}

			addresses.name.push( address );
			addresses.latlng.push( this.getLocation( address ) );
		} );

		await Promise.all( addresses.latlng ).then( coordinates => {
			for ( let i in coordinates ) {
				if ( ! this.hasLocation( addresses.name[i] ) ) {
					this.locations.push( {
						address: addresses.name[i],
						location: coordinates[i]
					} );
				}
			}
		} ).catch( error => {
			console.warn( error );
		} );
	}

	async updateMarkers() {
		if ( typeof this.map === 'undefined' ) {
			return;
		}

		if ( ! this.elements.$markers.length ) {
			return;
		}

		// Reset all markers.
		this.clearMarkers();

		// Update locations.
		await this.updateLocationList();

		this.elements.$markers.children().each( ( index, marker ) => {
			let data = JSON.parse( marker.dataset.marker );
			let markerOptions = {
				map: this.map,
				animation: google.maps.Animation.DROP
			}

			if ( data.icon.url ) {
				markerOptions.icon = data.icon.url;
			}

			let latlng = data.latlng.split( ',' ).map( parseFloat );

			if ( latlng.length > 1 && ! Number.isNaN( latlng[0] ) && ! Number.isNaN( latlng[1] ) ) {
				markerOptions.position = {
					lat: latlng[0],
					lng: latlng[1]
				};
			} else if ( data.address ) {
				markerOptions.position = this.hasLocation( data.address )
			} else {
				return;
			}

			let mapMarker = new google.maps.Marker( markerOptions );

			if ( marker.innerHTML ) {
				let infoWindow = new google.maps.InfoWindow( {
					content: '<div class="konte-google-map__info info_content">' + marker.innerHTML + '</div>'
				} );

				mapMarker.addListener( 'click', () => {
					infoWindow.open( this.map, mapMarker );
				} );
			}

			this.markers.push( mapMarker );
		} );
	}

	async onInit() {
		super.onInit();

		try {
			await this.initMap();
			await this.setMapLocation();
			this.updateMarkers();
		} catch ( error ) {
			console.warn( error );
		}
	}

	async onElementChange( propertyName ) {
		if ( 'address' === propertyName || 'latlng' === propertyName ) {
			clearTimeout( this.timerAddressChange );
			this.timerAddressChange = setTimeout( () => {
				this.setMapLocation();
			}, 1000 );
		}

		if ( 'zoom' === propertyName ) {
			let zoom = this.getElementSettings( 'zoom' );

			this.map.setZoom( zoom.size );
		}

		if ( 'color' === propertyName ) {
			this.map.setOptions( {
				styles: this.getMapStyleOption()
			} );
		}
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteTabsHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-accordion.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteTabsHandler, {
			$element: $element,
			showFn: 'slideDown',
			hideFn: 'slideUp',
			autoExpand: false,
			toggleSelf: true,
			selectors: {
				tab: '.konte-accordion__title',
				panel: '.konte-accordion__content'
			}
		} );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-chart.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteCircleChartHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-countdown.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteCountDownHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-countdown-banner.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteCountDownHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-testimonial-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-testimonial-slideshow.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteTestimonialSlideshowHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-posts-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, {
			$element: $element,
			swiperOptions: {
				spaceBetween: 40,
			}
		} );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-products-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, {
			$element: $element,
			selectors: {
				carousel: '.konte-product-carousel',
				slideContent: '.product',
			},
			carouselContent: 'products',
			swiperOptions: {
				wrapperClass: 'products',
				slideClass: 'product',
			},
		} );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-products-carousel-2.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, {
			$element: $element,
			carouselContent: 'products',
			swiperOptions: {
				slidesPerView: 'auto',
			},
		} );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-products-tabs.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteProductsTabsHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-products-tabs-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteProductsTabsHandler, {
			$element: $element,
			carousel: true
		} );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-google-map.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteGoogleMapHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-instagram-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-team-member-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, {
			$element: $element,
			swiperOptions: {
				spaceBetween: 40,
			}
		} );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/konte-banner-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( KonteSwiperCarouselHandler, { $element: $element } );
	} );
} );
