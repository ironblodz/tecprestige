/**
 * Quantity dropdown.
 */
(function ($) {
	'use strict';

	var QuantityDropdown = function( input, options ) {
		var self = this;

		self.options   = $.extend( {}, $.fn.quantityDropdown.defaults, options );
		self.$input    = input;
		self.$quantity = self.$input.closest( '.quantity' );
		self.$form     = self.$input.closest( 'form' );

		// Methods.
		self.init          = self.init.bind( self );
		self.destroy       = self.destroy.bind( self );
		self.refresh       = self.refresh.bind( self );
		self.scroll        = self.scroll.bind( self );
		self.increase      = self.increase.bind( self );
		self.openDropdown  = self.openDropdown.bind( self );
		self.closeDropdown = self.closeDropdown.bind( self );
		self.setValue      = self.setValue.bind( self );

		// Events.
		self.$quantity.on( 'click.quantity-dropdown', '.qty-dropdown .current', { quantityDropdown: self }, self.toggleDropdown );
		self.$quantity.on( 'click.quantity-dropdown', '.qty-options li', { quantityDropdown: self }, self.onSelect );
		self.$quantity.on( 'change.quantity-dropdown', 'input.qty', { quantityDropdown: self }, self.onQuantityChange );
		self.$quantity.on( 'mousewheel.quantity-dropdown', '.qty-options', { quantityDropdown: self }, self.onScroll );
		self.$quantity.on( 'swipeup.quantity-dropdown', '.qty-options', { quantityDropdown: self }, self.onSwipeUp );
		self.$quantity.on( 'swipedown.quantity-dropdown', '.qty-options', { quantityDropdown: self }, self.onSwipeDown );

		if ( self.$form.length ) {
			self.$form.on( 'found_variation.quantity-dropdown', self.onFoundVariation );
		}

		// Init.
		self.init();
	};

	QuantityDropdown.prototype.init = function() {
		var self = this;

		self.$list     = $( '<ul/>' );
		self.$dropdown = $( '<div class="qty-dropdown"/>' );
		self.current   = parseFloat( self.$input.val() );
		self.min       = parseFloat( self.$input.attr( 'min' ) );
		self.max       = parseFloat( self.$input.attr( 'max' ) );
		self.step      = parseFloat( self.$input.attr( 'step' ) );
		self.maxItems  = self.options.maxItems;
		self.infinite  = false;
		self.values    = [];
		self.visible   = [];

		// Correct input attributes.
		self.step = self.step ? self.step : 1;
		self.min = self.min ? self.min : 0;
		self.current = self.current ? self.current : self.min;

		if ( ! self.max ) {
			self.max = Math.max( self.maxItems, self.current );
			self.infinite = true;
		} else if ( self.max < self.maxItems ) {
			self.maxItems = self.max;
		}

		// Add all options.
		for ( var i = self.min; i <= self.max; i += self.step ) {
			if ( self.current == i ) {
				$( '<li class="active">' + i + '</li>' ).appendTo( self.$list );
			} else {
				$( '<li>' + i + '</li>' ).appendTo( self.$list );
			}

			self.values.push( i );
		}

		self.$dropdown
			.append( '<span class="current"><span class="value">' + self.current + '</span><span class="svg-icon icon-arrow-dropdown icon-smaller"><svg><use xlink:href="#arrow-dropdown"></use></svg></span></span>' )
			.append( self.$list )
			.appendTo( self.$quantity );

		self.$list.wrap( '<div class="qty-options"/>' );
		self.$quantity.addClass( 'quantity-dropdown' );
	};

	QuantityDropdown.prototype.destroy = function() {
		this.$quantity.off( '.quantity-dropdown' ).removeData( 'quantityDropdown' );
		this.$form.off( '.quantity-dropdown' );
		this.$quantity.removeData( 'quantityDropdown' );
		this.$quantity.removeClass( 'quantity-dropdown' );
		this.$quantity.find( '.qty-dropdown' ).remove();
		this.$dropdown.detach().remove();
	};

	QuantityDropdown.prototype.refresh = function() {
		var self = this;

		self.$quantity.find( '.qty-dropdown' ).detach().remove();
		self.$dropdown.detach().remove();
		self.init();
	};

	QuantityDropdown.prototype.toggleDropdown = function( event ) {
		event.preventDefault();

		var self = event.data.quantityDropdown;

		self.scroll();

		if ( self.$dropdown.hasClass( 'opened' ) ) {
			self.closeDropdown();
		} else {
			self.openDropdown();
		}
	};

	QuantityDropdown.prototype.openDropdown = function() {
		var self = this,
			$options = self.$dropdown.find( '.qty-options' ),
			height = self.$dropdown.find( '.current' ).outerHeight();

		$options.css( 'height', self.maxItems * height );
		$options.fadeIn();
		self.$dropdown.addClass( 'opened' );

		$( document ).on( 'click.quantity-dropdown', closeDropdownOnFocusOutside );
	};

	QuantityDropdown.prototype.closeDropdown = function() {
		this.$quantity.find( '.qty-options' ).fadeOut();
		this.$dropdown.removeClass( 'opened' );
	};

	QuantityDropdown.prototype.onSelect = function( event ) {
		event.preventDefault();

		var self = event.data.quantityDropdown;

		self.$input.val( parseFloat( event.target.innerText ) ).trigger( 'change' );

		self.closeDropdown();

		if ( typeof self.options.onChange === 'function' ) {
			self.options.onChange( self );
		}
	};

	QuantityDropdown.prototype.onQuantityChange = function( event ) {
		var self = event.data.quantityDropdown;

		self.current = parseFloat( event.target.value );
		self.$dropdown.find( '.current .value' ).text( self.current );
		self.$list.children( '.active' ).removeClass( 'active' );

		var $listItem = self.$list.children().filter( function() {
			return self.current === parseFloat( this.innerText );
		} );

		if ( $listItem.length ) {
			$listItem.addClass( 'active' );
		} else {
			self.scroll( self.current );
			self.$list.children().last().addClass( 'active' );
		}
	}

	QuantityDropdown.prototype.onScroll = function( event ) {
		if ( event.originalEvent.wheelDelta / 120 > 0 ) {
			event.data.quantityDropdown.scroll( 'up' );
		} else {
			event.data.quantityDropdown.increase(); // Scroll down.
		}

		return false;
	};

	QuantityDropdown.prototype.onSwipeUp = function( event ) {
		event.data.quantityDropdown.increase();
	};

	QuantityDropdown.prototype.onSwipeDown = function( event ) {
		event.data.quantityDropdown.scroll( 'up' );
	};

	QuantityDropdown.prototype.scroll = function( value ) {
		var self = this,
			height = self.$quantity.find( '.current' ).outerHeight(),
			minVisible = Math.min.apply( null, self.visible ),
			maxVisible = Math.max.apply( null, self.visible ),
			distance = 0;

		if ( 'up' === value ) {
			if ( minVisible <= self.min ) {
				return;
			}

			distance = - self.values.indexOf( minVisible - self.step ) * height;
			self.visible.pop();
			self.visible.unshift( minVisible - self.step );
		} else if ( 'down' === value ) {
			if ( maxVisible >= self.max ) {
				return;
			}

			distance = - self.values.indexOf( minVisible + self.step ) * height;
			self.visible.shift();
			self.visible.push( maxVisible + 1 );
		} else {
			var value = value ? value : self.current,
				index = self.values.indexOf( value ),
				middle = Math.floor( self.maxItems/2 );

			if ( index > middle ) {
				index = index - middle;
			} else {
				index = 0;
			}

			// Reset visible.
			self.visible = [];

			for ( var i = index; i < index + self.maxItems; i++ ) {
				if ( i in self.values ) {
					self.visible.push( self.values[i] );
				} else if ( self.infinite ) {
					self.max = self.max + self.step;
					self.values.push( self.max );
					self.visible.push( self.max );
					self.$list.append( '<li>' + self.max + '</li>' );
				}
			}

			distance = -index * height;
		}

		self.$list.css( 'transform', 'translate3d(0, ' + distance + 'px, 0)' );
	}

	QuantityDropdown.prototype.setValue = function( value ) {
		this.current = value;
		this.$input.val( value ).trigger( 'change' );
	}

	QuantityDropdown.prototype.increase = function() {
		var self = this,
			maxVisible = Math.max.apply( null, self.visible );

		// Append.
		if ( maxVisible >= self.max && self.infinite ) {
			self.max += self.step;
			self.values.push( self.max );

			self.$list.append( '<li>' + self.max + '</li>' );
		}

		self.scroll( 'down' );
	}

	QuantityDropdown.prototype.onFoundVariation = function( event ) {
		var $qty = $( event.target ).find( '.single_variation_wrap .quantity .qty' );

		setTimeout( function() {
			$qty.quantityDropdown( 'refresh' );
		}, 100 );
	};

	// Close quantity dropdown if click outside.
	$( document ).on( 'click.quantity-dropdown', closeDropdownOnFocusOutside );

	function closeDropdownOnFocusOutside( event ) {
		var $target = $( event.target ).closest( '.qty-dropdown' );

		if ( $target.length === 0 ) {
			$( '.qty-options' ).fadeOut();
			$( '.qty-dropdown.opened' ).removeClass( 'opened' );
			$( document ).off( 'click.quantity-dropdown' );
		} else {
			$( '.qty-dropdown.opened' ).not( $target ).removeClass( 'opened' ).find( '.qty-options' ).fadeOut();
		}
	}

	$.fn.quantityDropdown = function( option ) {
		var options = typeof option == 'object' && option;

		return this.each( function() {
			var $this = $( this ),
				$quantity = $this.closest( '.quantity' );

			if ( ! $quantity.length ) {
				return this;
			}

			var plugin = $quantity.data( 'quantityDropdown' );

			if ( ! plugin ) {
				plugin = new QuantityDropdown( $this, options );
				$quantity.data( 'quantityDropdown', plugin );
			}

			if ( typeof option == 'string' && typeof plugin[option] == 'function' ) {
				plugin[option]();
			}
		} );
	};

	$.fn.quantityDropdown.defaults = {
		maxItems: 5,
		onChange: null,
		onInit: null
	};
} )(jQuery);

/**
 * Support swipe event.
 */
(function ($) {
	function is_touch_device() {
		var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
		var mq = function (query) {
			return window.matchMedia(query).matches;
		}

		if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
			return true;
		}

		// include the 'heartz' as a way to have a non matching MQ to help terminate the join
		// https://git.io/vznFH
		var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
		return mq(query);
	}

	// initializes touch and scroll events
	var supportTouch = is_touch_device(),
		touchStartEvent = supportTouch ? "touchstart" : "mousedown",
		touchStopEvent = supportTouch ? "touchend" : "mouseup",
		touchMoveEvent = supportTouch ? "touchmove" : "mousemove";

	// handles swipeup and swipedown
	$.event.special.swipeupdown = {
		setup: function () {
			var thisObject = this;
			var $this = $(thisObject);

			$this.bind(touchStartEvent, function (event) {
				var data = event.originalEvent.touches ?
					event.originalEvent.touches[0] :
					event,
					start = {
						time: (new Date).getTime(),
						coords: [data.pageX, data.pageY],
						origin: $(event.target)
					},
					stop;

				function moveHandler(event) {
					if (!start) {
						return;
					}

					var data = event.originalEvent.touches ?
						event.originalEvent.touches[0] :
						event;
					stop = {
						time: (new Date).getTime(),
						coords: [data.pageX, data.pageY]
					};

					// prevent scrolling
					if (Math.abs(start.coords[1] - stop.coords[1]) > 10) {
						event.preventDefault();
					}
				}

				$this
					.bind(touchMoveEvent, moveHandler)
					.one(touchStopEvent, function (event) {
						$this.unbind(touchMoveEvent, moveHandler);
						if (start && stop) {
							if (stop.time - start.time < 1000 &&
								Math.abs(start.coords[1] - stop.coords[1]) > 30 &&
								Math.abs(start.coords[0] - stop.coords[0]) < 75) {
								start.origin
									.trigger("swipeupdown")
									.trigger(start.coords[1] > stop.coords[1] ? "swipeup" : "swipedown");
							}
						}
						start = stop = undefined;
					});
			});
		}
	};

	//Adds the events to the jQuery events special collection
	$.each({
		swipedown: "swipeupdown",
		swipeup: "swipeupdown"
	}, function (event, sourceEvent) {
		$.event.special[event] = {
			setup: function () {
				$(this).bind(sourceEvent, $.noop);
			}
		};
		//Adds new events shortcuts
		$.fn[event] = function (fn) {
			return fn ? this.bind(event, fn) : this.trigger(event);
		};
	});
})(jQuery);
