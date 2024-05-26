/*global konteData*/
var konte = konte || {};

(function( $ ) {
	'use strict';

	/**
	 * The main function to init the theme
	 */
	konte.init = function() {
		this.data = konteData || {};

		this.preloader();

		this.focusSearchField();
		this.instantSearch();
		this.ajaxSearch();
		this.toggleListDropdown();
		this.addWishlistCounter();
		this.toggleHamburgerScreen();
		this.toggleHamburgerMenu();
		this.toggleVerticalMenu();
		this.toggleOffCanvas();
		this.calculateMenuAccessibleArea();
		this.toggleModals();
		this.loadMorePosts();
		this.fetchPostTotalShare();

		this.featuredContentCarousel();
		this.postsSliderWidget();
		this.stickySidebar();

		this.closeTopbar();
		this.stickyHeader();
		this.pageHeaderHeight();
		this.scrollDown();

		this.closeWCMessages();
		this.productQuantityButtons();
		this.singleProductAjaxAddToCart();
		this.stickyAddToCart();
		this.cartPanel();
		this.reviewProduct();
		this.productLightBox();
		this.toggleProductTabs();
		this.productVariationSwatches();
		this.relatedProductsCarousel();
		this.productGalleryCarousel();
		this.productGalleryThumbnailCarousel();

		this.singleProductV1();
		this.singleProductV2();
		this.singleProductV3();
		this.singleProductV4();
		this.singleProductV5();
		this.singleProductV6();
		this.singleProductV7();

		this.formFieldFocus();
		this.loginTabs();
		this.loginPanel();
		this.loginPanelAuthenticate();

		this.productsMasonry();
		this.productsCarousel();
		this.productThumbnailsSlider();
		this.productThumbnailZoom();
		this.productQuickView();
		this.emptyQuickViewOnClose();
		this.autoCloseQuickView();
		this.loadMoreProducts();

		this.productsQuickSearch();
		this.productsTools();
		this.cartWidget();
		this.shopNotifications();

		this.pageTemplateSplit();
		this.videoBackground();

		this.pageTemplateFlexPosts();
		this.stickyScrollDown();
		this.stickySocials();
		this.openShareLinks();

		this.popup();

		this.fixVCRowsWithVerticalHeader();
		// this.responsiveVideos();
		this.mobileMenu();
		this.responsiveProductGallery();
		this.responsiveProductV3();

		this.portfolioMasonry();
		this.portfolioFilter();

		this.backToTop();
		this.lazyLoadImages();
		this.supportJetpackLazyLoadImagesOnProductGallery();

		this.updateCheckout();
		this.mainMenuTabs();

		$( document.body ).trigger( 'konte_initialized', this );
	};

	/**
	 * Update checkout after fragments updated
	 */
	konte.updateCheckout = function() {
		if ( ! $( document.body ).hasClass( 'woocommerce-checkout' ) ) {
			return;
		}

		$( document.body ).on( 'wc_fragments_loaded wc_fragments_refreshed', function() {
			$( document.body ).trigger( 'update_checkout' );
		});
	}

	/**
	 * Open quick links when focus on search field
	 */
	konte.focusSearchField = function() {
		$( '.header-search .search-field' ).on( 'focus', function() {
			var $quicklinks = $( this ).closest( '.header-search' ).find( '.quick-links' );

			if ( $quicklinks.length ) {
				$quicklinks.addClass( 'open' );
			}

			$( this ).addClass( 'focused' ).closest( '.header-search' ).addClass( 'input-focused' );
		} );

		$( document.body ).on( 'click', 'div', function( event ) {
			var $target = $( event.target );

			if ( $target.is( '.header-search' ) || $target.closest( '.header-search' ).length ) {
				return;
			}

			var $headerSearch = $( '.header-search' );

			$headerSearch.removeClass( 'input-focused' );
			$( '.quick-links', $headerSearch ).removeClass( 'open' );
			$( '.search-field', $headerSearch ).removeClass( 'focused' );
		} );
	};

	/**
	 * Instance search in the search modal.
	 */
	konte.instantSearch = function() {
		var xhr = null,
			term = '',
			searchCache = {},
			debounceTimeout = null,
			$modal = $( '#search-modal' ),
			$header = $modal.find( '.modal-header' ),
			$content = $modal.find( '.modal-content' ),
			$form = $modal.find( 'form' ),
			$search = $form.find( 'input.search-field' ),
			$result = $modal.find( '.search-result' ),
			$button = $result.find( '.view-more a' ),
			$quicklinks = $modal.find( '.quick-links' ),
			post_type = $modal.find( 'input[name=post_type]' ).val();

		if ( !$modal.length ) {
			return;
		}

		// Focus on the search field when search modal opened
		$( document.body ).on( 'konte_modal_opened', function( event, target ) {
			if ( target.is( '#search-modal' ) ) {
				$search.focus();
			}
		} );

		new PerfectScrollbar( $result.find( '.searched-items' ).get( 0 ), {
			suppressScrollX: true
		} );

		$modal.on( 'keyup', '.search-field', function( e ) {
			var valid = false;

			if ( typeof e.which === 'undefined' ) {
				valid = true;
			} else if ( typeof e.which === 'number' && e.which > 0 ) {
				valid = !e.ctrlKey && !e.metaKey && !e.altKey;
			}

			if ( !valid ) {
				return;
			}

			// if ( xhr ) {
			// 	xhr.abort();
			// }

			$quicklinks.fadeOut( 400 );

			if ( debounceTimeout ) {
				clearTimeout( debounceTimeout );
			}

			debounceTimeout = setTimeout( function() {
				search();
			}, 800 );
		} ).on( 'click', '.search-reset', function() {
			if ( xhr ) {
				xhr.abort();
			}

			$quicklinks.fadeIn( 400 );
			$modal.addClass( 'reset' );
			$result.fadeOut( function() {
				$modal.removeClass( 'searching searched search-found search-not-found invalid-length reset' );
				$content.removeAttr( 'style' );
			} );
		} ).on( 'focusout', '.search-field', function() {
			if ( $search.val().length < 2 ) {
				$quicklinks.fadeIn( 400 );
				$result.fadeOut( function() {
					$modal.removeClass( 'searching searched search-found search-not-found invalid-length' );
					$content.removeAttr( 'style' );
				} );
			}
		} );

		/**
		 * Private function for searching products
		 */
		function search() {
			var keyword = $search.val(),
				key = keyword;

			if ( term === keyword ) {
				return;
			}

			term = keyword;

			if ( keyword.length < 2 ) {
				$modal.removeClass( 'searched searching search-found search-not-found' );
				return;
			}

			var url = $form.attr( 'action' ) + '?' + $form.serialize();

			$( '.view-more-results', $result ).slideUp( 10 );
			$modal.removeClass( 'search-found search-not-found searched' ).addClass( 'searching' );

			if ( key in searchCache ) {
				showResult( searchCache[key] );
			} else {
				xhr = $.post( url,
					{
						search_columns: 6,
						konte_search_nonce: konte.data.product_search_nonce
					},
					function( response ) {
						if ( ! response ) {
							$modal.removeClass( 'searching' );
							return;
						}

						var $primary = $( '#primary', response );

						if ( 'product' === post_type ) {
							var $products = $( 'ul.products', $primary );

							if ( $products.length ) {
								// Cache
								searchCache[key] = {
									found: true,
									items: $products,
									url  : url
								};
							} else {
								// Cache
								searchCache[key] = {
									found: false,
									text : $( '.woocommerce-info', $primary ).text()
								};
							}
						} else {
							var $posts = $( '#main article', $primary );

							if ( $posts.length ) {
								$posts.addClass( 'col-md-4' );

								searchCache[key] = {
									found: true,
									items: $( '<div class="posts row" />' ).append( $posts ),
									url  : url
								};
							} else {
								searchCache[key] = {
									found: false,
									text : $( '.no-results .nothing-found-message', $primary ).text()
								};
							}
						}

						showResult( searchCache[key] );
				}, 'html' );
			}
		}

		/**
		 * Private function for showing the search result
		 *
		 * @param result
		 */
		function showResult( result ) {
			var extraClass = 'product' === post_type ? 'woocommerce' : '',
				$container = $result.find( '.searched-items' );

			$modal.removeClass( 'searching' );
			$content.css( 'top', $header.outerHeight() + 65 );

			if ( result.found ) {
				var grid = result.items.clone(),
					items = grid.children();

				$modal.addClass( 'search-found' );
				$button.attr( 'href', result.url );

				$container
					.addClass( extraClass )
					.html( grid )
					.append( $result.find( '.view-more' ).clone() );

				// Init zoom/slider thumbnail
				if ( 'product' === post_type ) {
					$( document.body ).trigger( 'konte_loaded_products', [items] );
				}

				// Add animation class
				for ( var index = 0; index < items.length; index++ ) {
					$( items[index] ).css( 'animation-delay', index * 100 + 'ms' );
				}

				items.addClass( 'animated konteFadeInUp' );
				$container.find( '.view-more' ).css( 'animation-delay', ++index * 100 + 'ms' ).addClass( 'animated konteFadeInUp' );
				$result.slideDown();
			} else {
				$modal.addClass( 'search-not-found' );

				$container.removeClass( extraClass ).html( $( '<div class="not-found" />' ).text( result.text ) );
				$button.attr( 'href', '#' );

				$result.slideDown();
			}

			$modal.addClass( 'searched' );
			$( document.body ).trigger( 'konte_lazy_load_images' );
		}
	};

	/**
	 * Ajax search for header search from.
	 */
	konte.ajaxSearch = function() {
		if ( ! konte.data.header_search_ajax ) {
			return;
		}

		var debounceTimeout = null,
			cachedResults   = {};

		// Create the results container.
		$( '<div class="header-search__results" />' ).insertAfter( '.header-search form' );

		$( '.header-search input[name="s"]' ).on( 'input', function() {
			var $input   = $( this ),
				$search  = $input.closest( '.header-search' ),
				$results = $search.find( '.header-search__results' ),
				term     = $input.val();

			if ( ! term ) {
				if ( debounceTimeout ) {
					clearTimeout( debounceTimeout );
				}

				$search.removeClass( 'ajax-loading' );
				$results.html( '' );
				$search.removeClass( 'show-results' );
				return;
			}

			if ( cachedResults[ term ] ) {
				$results.html( cachedResults[ term ] );
				$search.addClass( 'show-results' );
				return;
			}

			if ( debounceTimeout ) {
				clearTimeout( debounceTimeout );
			}

			debounceTimeout = setTimeout( function() {
				var data = $input.closest( 'form' ).serializeArray();

				data.push( {name: 'action', value: 'konte_header_search'} );

				$.ajax( {
					type      : 'POST',
					url       : konte.data.ajax_url,
					data      : data,
					beforeSend: function() {
						$search.addClass( 'ajax-loading' );
					},
					success: function( response ) {
						if ( ! response.success ) {
							return;
						}

						cachedResults[ term ] = response.data;

						if ( response.data ) {
							$results.html( response.data );
							$search.addClass( 'show-results' );
						} else {
							$results.html( '' );
							$search.removeClass( 'show-results' );
						}
					},
					complete: function() {
						$search.removeClass( 'ajax-loading' );
					},
					dataType: 'json'
				} );
			}, 800 );
		} );
	}

	/**
	 * Toggle list dropdown
	 */
	konte.toggleListDropdown = function() {

		$( document.body ).on( 'click', '.list-dropdown ul li', function() {
			var $selected = $( this );

			$selected.closest( '.list-dropdown' ).removeClass( 'open' ).find( '.current .selected' ).text( $selected.find( '.name' ).text() );
		} );

		if ( 'ontouchstart' in document.documentElement ) {
			$( document.body ).on( 'click', '.list-dropdown .current', function( event ) {
				event.preventDefault();

				$( this ).closest( '.list-dropdown' ).toggleClass( 'open' );
			} ).on( 'click', '.list-dropdown ul li', function() {
				var $selected = $( this );

				$selected.closest( '.list-dropdown' ).removeClass( 'open' ).find( '.current .selected' ).text( $selected.find( '.name' ).text() );
			} ).on( 'click', function( event ) {
				var $target = $( event.target );

				if ( $target.is( '.list-dropdown' ) || $target.closest( '.list-dropdown' ).length ) {
					return;
				}

				$( '.list-dropdown' ).removeClass( 'open' );
			} );
		}
	};

	/**
	 * Add wishlist counter to the Account navigation
	 */
	konte.addWishlistCounter = function() {
		$( '.woocommerce-MyAccount-navigation-link--wishlist, .account-link--wishlist' )
			.append( '<span class="counter wishlist-counter">' + konte.data.wishlist_count + '</span>' );
	};

	/**
	 * Calculate accessible area of main navigation's submenu
	 */
	konte.calculateMenuAccessibleArea = function() {
		$( '.main-navigation', '#masthead' ).each( function() {
			var $menu = $( this ),
				$firstItem = $menu.children( 'ul' ).children( 'li:first' ),
				accessibleGap = ( $firstItem.height() - $firstItem.children( 'a ' ).height() ) / 2;

			$menu.css( { '--submenu-offset': accessibleGap.toString() + 'px' } );
		} );
	};

	/**
	 * Toggle hamburger full-screen
	 */
	konte.toggleHamburgerScreen = function() {
		$( document.body ).on( 'click', '.header-hamburger', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				$screen = $( '#' + $el.data( 'target' ) );

			if ( !$screen.length ) {
				return;
			}

			var $menu = $( '#fullscreen-menu', $screen ),
				$widgets = $( '.filter-widgets', $screen ),
				$socials = $( '.social-icons', $screen ),
				$currency = $( '.currency', $screen ),
				$language = $( '.language', $screen ),
				step = 100,
				count = 0;

			// Add animation if enabled.
			if ( !$screen.hasClass( 'content-animation-none' ) ) {
				if ( $screen.hasClass( 'content-animation-fade' ) ) {
					step = 160;
				}

				if ( $menu.length && !$menu.data( 'delay' ) ) {
					$( '.menu > li', $menu ).each( function() {
						$( this ).css( 'animation-delay', count * step + 'ms' );
						count++;
					} );

					$menu.data( 'delay', count );
				} else if ( $widgets.length && !$widgets.data( 'delay' ) ) {
					$( '.widget', $widgets ).each( function() {
						$( this ).css( 'animation-delay', count * step + 'ms' );
						count++;
					} );

					$widgets.data( 'delay', count );
				}

				count = 1;
				if ( $socials.length && !$socials.data( 'delay' ) ) {
					$( '.menu > li', $socials ).each( function() {
						$( this ).css( 'animation-delay', count * step + 'ms' );
						count++;
					} );

					$socials.data( 'delay', count );
				}

				if ( $currency.length && !$currency.data( 'delay' ) ) {
					$currency.css( 'animation-delay', count * step + 'ms' );
					$currency.data( 'delay', count );
					count++;
				}

				if ( $language.length && !$language.data( 'delay' ) ) {
					$language.css( 'animation-delay', count * step + 'ms' );
					$language.data( 'delay', count );
					count++;
				}
			}

			$screen.fadeToggle( function() {
				$( '.hamburger-menu', $screen ).addClass( 'active' );
			} );
			$screen.addClass( 'open' );
		} ).on( 'click', '#hamburger-fullscreen .button-close', function( event ) {
			event.stopPropagation();

			var $el = $( this ),
				$screen = $( '#hamburger-fullscreen' );

			$el.removeClass( 'active' );
			$screen.removeClass( 'open' );

			setTimeout( function() {
				$screen.fadeOut();
			}, 420 );
		} );

		// Init scrollbar for full screen menu.
		if ( typeof PerfectScrollbar !== 'undefined' ) {
			var $hamburgerScreen = $( '#hamburger-fullscreen' );

			if ( $hamburgerScreen.length ) {
				new PerfectScrollbar( $( '.hamburger-screen-content', $hamburgerScreen ).get( 0 ) );
			}
		}
	};

	/**
	 * Toggle sub-menu inside hamburger screen
	 */
	konte.toggleHamburgerMenu = function() {
		var $menu = $( '#fullscreen-menu' );

		if ( !$menu.length ) {
			return;
		}

		// Open when click
		if ( $menu.hasClass( 'click-open' ) ) {
			$menu.on( 'click', '.menu li.menu-item-has-children > a', function( event ) {
				event.preventDefault();

				var $item = $( this ).parent();

				$item.toggleClass( 'active' ).siblings().removeClass( 'active' ).children( 'ul' ).removeClass( 'open' );
				$item.children( 'ul' ).toggleClass( 'open' );

				// If this is sub-menu item
				if ( $item.closest( 'ul' ).hasClass( 'sub-menu' ) ) {
					$item.children( 'ul' ).slideToggle();
					$item.siblings().find( 'ul' ).slideUp();
				}
			} );
		}
	};

	/**
	 * Toggle sub-menu of vertical header
	 */
	konte.toggleVerticalMenu = function() {
		var $menu = $( '.header-vertical .main-navigation' );

		if ( ! $menu.length ) {
			return;
		}

		var dropdown = $menu.children( '.menu' ).hasClass( 'nav-menu--submenu-slidedown' );

		// Open when click
		$menu.on( 'click', 'li.menu-item-has-children > a', function( event ) {
			var $item = $( this ).parent();

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' ).children( 'ul' ).removeClass( 'open' );
			$item.children( 'ul' ).toggleClass( 'open' );

			// If this is sub-menu item
			if ( dropdown || $item.closest( 'ul' ).hasClass( 'sub-menu' ) ) {
				$item.children( 'ul' ).slideToggle();
				$item.siblings().find( 'ul' ).slideUp();

				event.preventDefault();
			}
		} );
	};

	/**
	 * Toggle off-screen panels
	 */
	konte.toggleOffCanvas = function() {
		$( document.body ).on( 'click', '[data-toggle="off-canvas"]', function( event ) {
			var target = '#' + $( this ).data( 'target' );

			if ( $( target ).hasClass( 'open' ) ) {
				konte.closeOffCanvas( target );
			} else if ( konte.openOffCanvas( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.offscreen-panel .button-close, .offscreen-panel .backdrop', function( event ) {
			event.preventDefault();

			konte.closeOffCanvas( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				konte.closeOffCanvas();
			}
		} );
	};

	/**
	 * Open off canvas panel.
	 * @param string target Target selector.
	 */
	konte.openOffCanvas = function( target ) {
		var $target = $( target );

		if ( !$target.length ) {
			return false;
		}

		$target.fadeIn();
		$target.addClass( 'open' );

		$( document.body ).addClass( 'offcanvas-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'konte_off_canvas_opened', [$target] );

		return true;
	}

	/**
	 * Close off canvas panel.
	 * @param DOM target
	 */
	konte.closeOffCanvas = function( target ) {
		if ( !target ) {
			$( '.offscreen-panel' ).each( function() {
				var $panel = $( this );

				if ( ! $panel.hasClass( 'open' ) ) {
					return;
				}

				$panel.removeClass( 'open' ).fadeOut();
				$( document.body ).removeClass( $panel.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = $( target ).closest( '.offscreen-panel' );
			target.removeClass( 'open' ).fadeOut();

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );
		}

		$( document.body ).removeClass( 'offcanvas-opened' ).trigger( 'konte_off_canvas_closed', [target] );
	}

	/**
	 * Toggle modals.
	 */
	konte.toggleModals = function() {
		$( document.body ).on( 'click', '[data-toggle="modal"]', function( event ) {
			var target = '#' + $( this ).data( 'target' );

			if ( $( target ).hasClass( 'open' ) ) {
				konte.closeModal( target );
			} else if ( konte.openModal( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.modal .button-close, .modal .backdrop', function( event ) {
			event.preventDefault();

			konte.closeModal( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				konte.closeModal();
			}
		} );
	};

	/**
	 * Open a modal.
	 *
	 * @param string target
	 */
	konte.openModal = function( target ) {
		var $target = $( target );

		if ( !$target.length ) {
			return false;
		}

		$target.fadeIn();
		$target.addClass( 'open' );

		$( document.body ).addClass( 'modal-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'konte_modal_opened', [$target] );

		return true;
	}

	/**
	 * Close a modal.
	 *
	 * @param string target
	 */
	konte.closeModal = function( target ) {
		if ( !target ) {
			$( '.modal' ).removeClass( 'open' ).fadeOut();

			$( '.modal' ).each( function() {
				var $modal = $( this );

				if ( ! $modal.hasClass( 'open' ) ) {
					return;
				}

				$modal.removeClass( 'open' ).fadeOut();
				$( document.body ).removeClass( $modal.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = $( target ).closest( '.modal' );
			target.removeClass( 'open' ).fadeOut();

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );
		}

		$( document.body ).removeClass( 'modal-opened' ).trigger( 'konte_modal_closed', [target] );
	}

	/**
	 * Initialize the featured content carousel
	 */
	konte.featuredContentCarousel = function() {
		var $featured = $( '#featured-content-carousel' ),
			options = {
				rtl      : !! konte.data.rtl,
				prevArrow: '<span class="slick-prev slick-arrow svg-icon icon-left icon-small"><svg width="16" height="16"><use xlink:href="#left"></use></svg></span>',
				nextArrow: '<span class="slick-next slick-arrow svg-icon icon-right icon-small"><svg width="16" height="16"><use xlink:href="#right"></use></svg></span>'
			};

		if ( !$featured.length ) {
			return;
		}

		if ( $featured.hasClass( 'carousel' ) ) {
			options.infinite = false;
			options.slidesToShow = 3;
			options.slidesToScroll = 3;
			options.responsive = [
				{
					breakpoint: 991,
					settings  : {
						slidesToShow  : 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 767,
					settings  : {
						slidesToShow  : 1,
						slidesToScroll: 1
					}
				}
			];

			// Lazy load
			$featured.on( 'init reInit breakpoint', function( event, slick ) {
				slick.$slides.each( function() {
					loadBackgroundImage( this );
				} );
			} );
		} else {
			if ( 'fade' === $featured.data( 'effect' ) ) {
				options.fade = true;
			}

			// Lazy load
			$featured.on( 'beforeChange', function( event, slick, currentSlide, nextSlide ) {
				loadBackgroundImage( slick.$slides.get( nextSlide ) );
			} );

			$featured.on( 'init reInit breakpoint', function( event, slick ) {
				loadBackgroundImage( slick.$slides.get( slick.currentSlide ) );
			} );
		}

		$featured.slick( options );

		/**
		 * Load background image
		 */
		function loadBackgroundImage( el ) {
			var $el = $( el ).find( '.featured-item' );

			if ( $el.data( 'lazy' ) ) {
				$el.css( 'background-image', 'url(' + $el.data( 'lazy' ) + ')' );
			}

			$el.data( 'lazy_loaded', true ).removeClass( 'loading' );
		}
	};

	/**
	 * Ajax load more posts.
	 */
	konte.loadMorePosts = function() {
		if ( $( document.body ).hasClass( 'page-template-flex-posts' ) ) {
			return;
		}

		$( document.body ).on( 'click', '.navigation.next-posts-navigation a', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				$navigation = $el.closest( '.navigation' ),
				url = $el.attr( 'href' );

			if ( $el.hasClass( 'loading' ) ) {
				return;
			}

			$el.addClass( 'loading' );

			$.get( url, function( response ) {
				var $content = $( '#main', response ),
					$posts = $( '.hentry', $content ),
					$nav = $( '.next-posts-navigation', $content );

				$posts.each( function( index, post ) {
					$( post ).css( 'animation-delay', index * 100 + 'ms' );
				} );

				// Check if posts are wrapped or not.
				if ( $navigation.prev( '.posts-wrapper' ).length ) {
					$posts.appendTo( $navigation.prev( '.posts-wrapper' ) );
				} else {
					$posts.insertBefore( $navigation );
				}

				$posts.addClass( 'animated konteFadeInUp' );

				if ( $nav.length ) {
					$el.replaceWith( $( 'a', $nav ) );
				} else {
					$el.removeClass( 'loading' );
					$navigation.fadeOut();
				}

				if ( $navigation.hasClass( 'next-projects-navigation' ) ) {
					if ( konte.data.portfolio_nav_ajax_url_change ) {
						window.history.pushState( null, '', url );
					}
				} else if ( konte.data.blog_nav_ajax_url_change ) {
					window.history.pushState( null, '', url );
				}

				$( document.body ).trigger( 'konte_posts_loaded', [$posts, true] );
			} );
		} );
	};

	/**
	 * Fetch total share from ShareThis API.
	 */
	konte.fetchPostTotalShare = function() {
		$( '.total-shares.fetching:visible' ).each( function() {
			var $share = $( this ),
				post_id = $share.data( 'post_id' );

			if ( !post_id ) {
				return;
			}

			$.post( konte.data.ajax_url, {
				action : 'konte_get_total_shares',
				post_id: post_id,
				security: konte.data.share_nonce
			}, function( response ) {
				$share.removeClass( 'fetching' );

				if ( response.success ) {
					$( '.count', $share ).html( response.data );
				}
			} );
		} );
	};

	/**
	 * Initialize posts slider widget
	 */
	konte.postsSliderWidget = function() {
		$( '.posts-slider-widget .posts-slider' ).slick( {
			rtl           : !! konte.data.rtl,
			adaptiveHeight: true,
			autoplay      : true,
			autoplaySpeed : 3000,
			prevArrow     : '<span class="slick-prev slick-arrow svg-icon icon-left icon-small"><svg width="16" height="16"><use xlink:href="#left"></use></svg></span>',
			nextArrow     : '<span class="slick-next slick-arrow svg-icon icon-right icon-small"><svg width="16" height="16"><use xlink:href="#right"></use></svg></span>'
		} );
	};

	/**
	 * Make the sidebar sticky
	 */
	konte.stickySidebar = function() {
		if ( $.fn.stick_in_parent && $( window ).width() > 767 ) {
			$( '#secondary.sticky-sidebar' ).stick_in_parent();
		}

		// Recalculate after cart widget loaded via ajax.
		$( document.body ).on( 'wc_fragments_refreshed post-load konte_products_loaded', function() {
			$( document.body ).trigger( 'sticky_kit:recalc' );
		} );
	};

	/**
	 * Close the topbar
	 */
	konte.closeTopbar = function() {
		$( document.body ).on( 'click', '.close-topbar', function( event ) {
			event.preventDefault();

			$( '#topbar' ).slideUp();
		} );
	};

	/**
	 * Sticky header
	 */
	konte.stickyHeader = function () {
		if ( !konte.data.sticky_header || 'none' === konte.data.sticky_header ) {
			return;
		}

		var $window = $( window ),
			$header = $( '#masthead' ),
			$sticker = $header,
			$topbar = $( '#topbar' ),
			offset = 0;

		if ( $header.hasClass( 'header-v10' ) ) {
			$sticker = $header.find( '.header-main .header-right-items' );
		}

		if ( ! $sticker.length ) {
			return;
		}

		if ( $topbar.length ) {
			offset += $topbar.outerHeight();
		}

		if ( 'smart' === konte.data.sticky_header ) {
			offset += $topbar.length ? 0 : $sticker.outerHeight();

			var stickyHeader = new Headroom( $sticker.get(0), {
				offset: offset
			});

			stickyHeader.init();
		} else {
			sticky( offset );

			$window.on( 'scroll', function() {
				sticky( offset );
			} );
		}

		/**
		 * Private function for sticky header
		 */
		function sticky( topSpacing ) {
			if ( $sticker.hasClass( 'sticky--ignore' ) ) {
				return;
			}

			topSpacing = topSpacing ? topSpacing : 0;

			if ( $window.scrollTop() > topSpacing ) {
				$sticker.addClass( 'sticky' );
			} else {
				$sticker.removeClass( 'sticky' );
			}
		}
	};

	/**
	 * Correct the page header's height.
	 */
	konte.pageHeaderHeight = function() {
		var $pageHeader = $( '.page .page-header.full-height' );

		if ( !$pageHeader.length ) {
			return;
		}

		var $topbar = $( '#topbar' ),
			$header = $( '#masthead' ),
			height = $( window ).height();

		if ( $topbar.length ) {
			height -= $topbar.outerHeight();
		}

		if ( !$header.hasClass( 'transparent' ) ) {
			height -= $header.outerHeight();

			$pageHeader.css( 'marginTop', '-' + $( '#content' ).css( 'paddingTop' ) );
		}

		if ( $pageHeader.hasClass( 'title-front' ) ) {
			$pageHeader.height( height );
		} else {
			$pageHeader.find( '.entry-thumbnail' ).height( height );
		}
	};

	/**
	 * Scroll down when clicing on the anchor
	 */
	konte.scrollDown = function() {
		$( document.body ).on( 'click', '.scroll', function( event ) {
			event.preventDefault();

			var offset = $( this ).parent().next().offset().top;

			$( 'html, body' ).animate( {scrollTop: offset} );
		} );
	};

	/**
	 * Make WC message closeable.
	 */
	konte.closeWCMessages = function() {
		$( document.body ).on( 'click', '.woocommerce-message .close-message, .woocommerce-error .close-message, .woocommerce-info .close-message', function( event ) {
			event.preventDefault();

			$( this ).closest( 'div' ).fadeOut();
		} );

		$( document.body ).on( 'click', '.wc-block-components-notice-banner__close', function( event ) {
			event.preventDefault();

			$( this ).closest( '.wc-block-components-notice-banner' ).fadeOut();
		} );
	};

	/**
	 * Increase/decrease product quantity
	 */
	konte.productQuantityButtons = function() {
		$( document.body ).on( 'click', '.quantity .increase, .quantity .decrease', function( event ) {
			event.preventDefault();

			var $this = $( this ),
				$qty = $this.siblings( '.qty' ),
				current = parseFloat( $qty.val() ),
				min = parseFloat( $qty.attr( 'min' ) ),
				max = parseFloat( $qty.attr( 'max' ) ),
				step = parseFloat( $qty.attr( 'step' ) );

			current = current ? current : 0;
			min = min ? min : 0;
			max = max ? max : current + 1;
			step = step ? step : 1;

			if ( $this.hasClass( 'decrease' ) && current > min ) {
				$qty.val( current - step );
				$qty.trigger( 'change' );
			}
			if ( $this.hasClass( 'increase' ) && current < max ) {
				$qty.val( current + step );
				$qty.trigger( 'change' );
			}
		} );
	};

	/**
	 * Using Ajax for add to cart button on single product page.
	 */
	konte.singleProductAjaxAddToCart = function() {
		if ( '1' !== konte.data.product_ajax_addtocart ) {
			return;
		}

		$( document.body ).on( 'submit', 'form.cart', function( event ) {
			var $form = $( this );

			if ( $form.closest( 'div.product' ).hasClass( 'product-type-external' ) || $form.data( 'with_ajax' ) ) {
				return;
			}

			event.preventDefault();

			var	$button = $form.find( '.single_add_to_cart_button' ),
				formData = new FormData( this );

			// Prevent double-add by remove the param 'add-to-cart'.
			if ( formData.has( 'add-to-cart' ) ) {
				formData.delete( 'add-to-cart' );
			}

			// Then we will rename it in the ajax handler function.
			formData.append( 'konte-add-to-cart', $form.find( '[name="add-to-cart"]' ).val() );

			// Ajax.
			$.ajax( {
				url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'konte_ajax_add_to_cart' ),
				data: formData,
				method: 'POST',
				contentType: false,
				processData: false,
				beforeSend: function() {
					$button.removeClass( 'added' ).addClass( 'loading disabled' ).prop( 'disabled', true );
				},
				complete: function( response ) {
					$button.removeClass( 'loading disabled' ).addClass( 'added' ).prop( 'disabled', false );

					response = response.responseJSON;

					if ( ! response ) {
						return;
					}

					// Reload on error.
					if ( response.error && response.product_url ) {
						window.location = response.product_url;
						return;
					}

					// If success, trigger events.
					var $notices = $( response.fragments.notices_html ),
						isAdded = $notices.hasClass( 'woocommerce-message' ) || $notices.hasClass( 'is-success' );

					if ( isAdded ) {
						// Trigger the default event to refresh fragments.
						$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $button ] );

						// Trigger the fragment refresh event manually if necessary.
						if ( 'undefined' === typeof wc_add_to_cart_params ) {
							$( document.body ).trigger( 'wc_fragment_refresh' );
						}
					}

					// Display new notices (alert bar).
					if ( konte.data.cart_open_after_added !== '1' || ! isAdded ) {
						konte.displayAlertBar( response.fragments.notices_html );
					}

					// Redirect to cart option.
					if ( 'undefined' !== typeof wc_add_to_cart_params && wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {
						window.location = wc_add_to_cart_params.cart_url;
						return;
					}
				},
				error: function() {
					// Try to submit the form without ajax on errors.
					$form.data( 'with_ajax', false ).trigger( 'submit' );
				}
			} );
		} );
	};

	/**
	 * Display alert bar at bottom
	 *
	 * @param {String} message
	 * @param {Number} duration Set to 0 to permanently display the alert
	 */
	konte.displayAlertBar = function( message, duration ) {
		if ( ! message ) {
			return;
		}

		if ( typeof duration !== "number" ) {
			duration = 5000;
		}

		if ( ! konte.alertTimeout ) {
			konte.alertTimeout = null;
		}

		// Prepare the alert bar.
		var $alert = $( '#konte-woocommerce-alert' );

		if ( ! $alert.length ) {
			$alert = $( '<div id="konte-woocommerce-alert" class="konte-woocommerce-alert"></div>' );
			$( document.body ).append( $alert );
		}

		// Show.
		$alert.html( message );

		setTimeout( function() {
			$alert.addClass( 'active' );
		}, 100 );

		// Close alert bar on button click.
		$alert.one( 'click', '.close-message, .wc-block-components-notice-banner__close', function( e ) {
			e.preventDefault();

			$alert.removeClass( 'active' );
		} );

		// Auto close.
		if ( duration ) {
			// Auto hide the alert bar after 5 seconds.
			clearTimeout( konte.alertTimeout );

			konte.alertTimeout = setTimeout( function() {
				$alert.removeClass( 'active' );
			}, duration );
		}
	}

	/**
	 * Init sticky cart form.
	 *
	 * @todo Support bundled products.
	 */
	konte.stickyAddToCart = function() {
		var $sticky = $( '.sticky-cart-form' );

		if ( ! $sticky.length ) {
			return;
		}

		// Remove wishlist and share buttons.
		$sticky.find( '.add-to-wishlist-button, .wcboost-wishlist-button, .wcboost-products-compare-button, .product-share' ).remove();

		var $forms = $( 'form.cart', $sticky.closest( 'div.product' ) ),
			$originalForm = $forms.first(),
			$image = $sticky.find( '.sticky-cart-form__product-image img' ),
			$price = $sticky.find( '.sticky-cart-form__product-summary .price' ),
			$stickyHeader = $( '#masthead' ),
			stickyPosition = $sticky.data( 'position' ),
			offset = 0,
			doingSync = false;

		if ( $stickyHeader.hasClass( 'header-v10' ) ) {
			$stickyHeader = $stickyHeader.find( '.header-main .header-right-items' );
		}

		if ( konte.data.sticky_header && 'none' !== konte.data.sticky_header ) {
			offset = $stickyHeader.height();
		}

		// Handle add-to-cart variations of 2 forms.
		$forms
			.on( 'reset_data', function() {
				$image.attr( 'src', $image.data( 'o_src' ) );
				$price.show().siblings( '.variation-price, .stock' ).remove();
			} )
			.on( 'found_variation', function( event, variation ) {
				if ( variation.image && variation.image.gallery_thumbnail_src && variation.image.gallery_thumbnail_src.length > 1 ) {
					$image.attr( 'src', variation.image.gallery_thumbnail_src );
				}

				if ( variation.availability_html && variation.availability_html.length ) {
					$price.hide().siblings( '.stock, .variation-price' ).remove();
					$price.after( variation.availability_html );
				} else {
					$price.siblings( '.stock' ).remove();

					if ( variation.price_html && variation.price_html.length ) {
						$price.hide().siblings( '.variation-price' ).remove();
						$price.after( $( variation.price_html ).addClass( 'variation-price' ) );
					}
				}
			} )
			// Sync inputs' value.
			.on( 'change', ':input', function() {
				// Avoid infinite loop.
				if ( doingSync ) {
					return;
				}

				doingSync = true;

				var $currentForm = $( this ).closest( 'form.cart' ),
					$targetForm = $forms.not( $currentForm );

				$targetForm.find( ':input[name="' + this.name + '"]' ).val( this.value ).trigger( 'change' );

				doingSync = false;
			} );

		// Scroll to the grouped products form.
		$sticky.on( 'click', '.grouped_form .single_add_to_cart_button, .bundle_form .single_add_to_cart_button', function() {
			$( 'html, body' ).animate( { scrollTop: $originalForm.offset().top - offset }, 800 );

			return false;
		} ).on( 'click', '.sticky-cart-form__mobile-button', function( event ) {
			event.preventDefault();

			if ( this.dataset.product_type === 'simple' || this.dataset.product_type === 'grouped' || this.dataset.product_type === 'external' ) {
				$sticky.find( '.single_add_to_cart_button' ).trigger( 'click' );
			} else {
				$( 'html, body' ).animate( { scrollTop: $originalForm.offset().top - offset }, 800 );
			}
		} );

		// Handle the scroll event.
		var	lastKnownScrollPosition = window.scrollY,
			ticking = false,
			rAF = window.requestAnimationFrame ||
				window.webkitRequestAnimationFrame ||
				window.mozRequestAnimationFrame ||
				window.msRequestAnimationFrame ||
				window.oRequestAnimationFrame;

		var checkFormVisibility = function( position ) {
			if ( position >= ( $originalForm.offset().top + $originalForm.height() - offset ) ) {
				$sticky.attr( 'aria-hidden', false ).removeClass( 'sticky-cart-form--unpin' ).addClass( 'sticky-cart-form--pin' );
				document.body.classList.add( 'sticky-cart-form-pinned-' + stickyPosition );

				if ( 'top' === $sticky.data( 'position' ) && konte.data.sticky_header && 'none' !== konte.data.sticky_header ) {
					$stickyHeader.addClass( 'sticky--ignore' );
				}
			} else {
				$sticky.attr( 'aria-hidden', true ).removeClass( 'sticky-cart-form--pin' ).addClass( 'sticky-cart-form--unpin' );
				document.body.classList.remove( 'sticky-cart-form-pinned-' + stickyPosition );

				if ( 'top' === $sticky.data( 'position' ) && konte.data.sticky_header && 'none' !== konte.data.sticky_header ) {
					$stickyHeader.removeClass( 'sticky--ignore' );
				}
			}
		};

		// Check and toggle the sticky add-to-cart form on scroll.
		document.addEventListener( 'scroll', function() {
			lastKnownScrollPosition = window.scrollY;

			if ( ! ticking ) {
				rAF( function() {
					checkFormVisibility( lastKnownScrollPosition );
					ticking = false;
				} );

				ticking = true;
			}
		} );

		rAF( function() {
			checkFormVisibility( lastKnownScrollPosition );
			ticking = false;
		} );

		// Adds extra space to the bottom if the sticky add-to-cart form at bottom.
		$( '#colophon' ).css( 'margin-bottom', $sticky.outerHeight() );
	}

	/**
	 * Automactically open the cart panel after a product added to cart.
	 * Disable window scrollbar when cart panel opened.
	 */
	konte.cartPanel = function() {
		var $panel = $( '#cart-panel' );

		if ( !$panel.length ) {
			return;
		}

		// Scrollbar for cart panel.
		if ( typeof PerfectScrollbar !== 'undefined' ) {
			new PerfectScrollbar( $panel.find( '.panel-content' ).get( 0 ) );
		}

		if ( '1' === konte.data.cart_open_after_added ) {
			$( document.body ).on( 'added_to_cart', function() {
				if ( $( '#cart-panel' ).hasClass( 'open' ) ) {
					return;
				}

				konte.closeOffCanvas( false );
				// Queue for opening cart panel, to avoid removing class `.offcanvas-opened`.
				setTimeout( function() {
					konte.openOffCanvas( '#cart-panel' );
				}, 0 );
			} );
		}
	};

	/**
	 * Handle product reviews
	 */
	konte.reviewProduct = function() {
		setTimeout( function() {
			$( '#respond p.stars a' ).prepend( '<span class="svg-icon icon-star"><svg><use xlink:href="#star"></use></svg></span>' );
		}, 100 );

		$( document.body )
			.on( 'click', '.add-review', function( event ) {
				event.preventDefault();

				var $reviews = $( this ).closest( '#reviews' );

				$( '#review_form_wrapper', $reviews ).fadeIn();
				$( '#comments', $reviews ).fadeOut();
			} ).on( 'click', '.cancel-review a', function( event ) {
			event.preventDefault();

			var $reviews = $( this ).closest( '#reviews' );

			$( '#review_form_wrapper', $reviews ).fadeOut();
			$( '#comments', $reviews ).fadeIn();
		} );

		$( '#review_form' )
			.on( 'focus', 'input, textarea', function() {
				$( this ).parent().addClass( 'focused' );
			} )
			.on( 'blur', 'input, textarea', function() {
				if ( $( this ).val() === '' ) {
					$( this ).parent().removeClass( 'focused' );
				}
			} )
			.find( 'input, textarea' ).each( function() {
			if ( $( this ).val() !== '' ) {
				$( this ).parent().addClass( 'focused' );
			}
		} );
	};

	/**
	 * Toggle product tabs
	 */
	konte.toggleProductTabs = function() {
		var $product = $( 'div.product' );

		if ( $product.hasClass( 'layout-v6' ) || $product.hasClass( 'layout-v7' ) ) {
			return;
		}

		$( document.body )
			.off( 'click', '.woocommerce-tabs.panels-offscreen .wc-tabs li a, .woocommerce-tabs.panels-offscreen ul.tabs li a' )
			.on( 'click', '.woocommerce-tabs.panels-offscreen .wc-tabs li a', function( event ) {
				event.preventDefault();

				var $el = $( this ),
					$wrapper = $el.closest( '.wc-tabs-wrapper, .woocommerce-tabs' ),
					$tabs = $wrapper.find( '.wc-tabs, ul.tabs' ),
					$panels = $wrapper.find( '.panels' ),
					$target = $wrapper.find( $el.attr( 'href' ) );

				$tabs.find( 'li' ).removeClass( 'active' );
				$el.closest( 'li' ).addClass( 'active' );
				$panels.find( '.panel' ).show();
				$panels.fadeIn();
				$target.addClass( 'open' );

				$( document.body ).addClass( 'offcanvas-opened' );
			} )
			.on( 'click', '.woocommerce-tabs .backdrop, .woocommerce-tabs .button-close', function( event ) {
				event.preventDefault();

				var $wrapper = $( this ).closest( '.wc-tabs-wrapper, .woocommerce-tabs' ),
					$panels = $wrapper.find( '.panels' ),
					$opened = $panels.find( '.panel.open' );

				$opened.removeClass( 'open' );
				$wrapper.find( '.tabs' ).children( 'li' ).removeClass( 'active' );
				$panels.fadeOut();

				// Close the review form
				if ( $opened.is( '#tab-reviews' ) ) {
					$opened.find( '#review_form_wrapper' ).fadeOut();
					$opened.find( '#comments' ).fadeIn();
				}

				$( document.body ).removeClass( 'offcanvas-opened' );
			} )
			.on( 'keyup', function( e ) {
				if ( e.keyCode === 27 ) {
					var $wrapper = $( '.wc-tabs-wrapper, .woocommerce-tabs' ),
						$panels = $wrapper.find( '.panels' ),
						$opened = $panels.find( '.panel.open' );

					$opened.removeClass( 'open' );
					$wrapper.find( '.tabs' ).children( 'li' ).removeClass( 'active' );
					$panels.fadeOut();

					// Close the review form
					if ( $opened.is( '#tab-reviews' ) ) {
						$opened.find( '#review_form_wrapper' ).fadeOut();
						$opened.find( '#comments' ).fadeIn();
					}

					$( document.body ).removeClass( 'offcanvas-opened' );
				}
			} );

		// Remove active tab.
		$product.find( '.wc-tabs, ul.tabs' ).first().find( 'li.active' ).removeClass( 'active' );

		// Auto open targeted tab (by hash).
		if ( window.location.hash ) {
			var $tabLink = $product.find( '.wc-tabs, ul.tabs' ).first().find( 'li a[href="' + window.location.hash + '"]' );

			if ( $tabLink.length ) {
				$tabLink.trigger( 'click' );
			}
		}
	};

	/**
	 * Make product full width
	 */
	konte.productFullWidth = function( $product ) {
		var $window = $( window );

		// Set width of product
		changeProductWidth();

		$window.on( 'resize', function() {
			changeProductWidth();
		} );

		/**
		 * Change the product width
		 */
		function changeProductWidth() {
			var width = $window.width();

			width -= konte.getVerticalHeaderWidth();

			$product.width( width );

			if ( konte.data.rtl ) {
				$product.css( 'marginRight', -width/2 );
			} else {
				$product.css( 'marginLeft', -width/2 );
			}
		}
	};

	/**
	 * Open product image lightbox when click on the zoom image
	 */
	konte.productLightBox = function() {
		if ( typeof wc_single_product_params === 'undefined' || wc_single_product_params.photoswipe_enabled !== '1' ) {
			return;
		}

		$( '.woocommerce-product-gallery' ).on( 'click', '.zoomImg', function() {
			if ( wc_single_product_params.flexslider_enabled ) {
				$( this ).closest( '.woocommerce-product-gallery' ).children( '.woocommerce-product-gallery__trigger' ).trigger( 'click' );
			} else {
				$( this ).prev( 'a' ).trigger( 'click' );
			}
		} );
	};

	/**
	 * Set the product background similar to product gallery images
	 */
	konte.productBackgroundFromGallery = function( $product ) {
		if ( typeof BackgroundColorTheif == 'undefined' ) {
			return;
		}

		var $gallery = $product.find( '.woocommerce-product-gallery' ),
			$image = $gallery.find( '.wp-post-image' ),
			imageColor = new BackgroundColorTheif();

		// Change background base on main image.
		$image.one( 'load', function() {
			setTimeout( function() {
				changeProductBackground( $image.get( 0 ) );
			}, 100 );
		} ).each( function() {
			if ( this.complete ) {
				$( this ).trigger( 'load' );
			}
		} );

		// Change background when slider change.
		setTimeout( function() {
			var slider = $gallery.data( 'flexslider' );

			if ( !slider ) {
				return;
			}

			slider.vars.before = function( slider ) {
				setTimeout( function() {
					changeProductBackground( slider.slides.filter( '.flex-active-slide' ).find( 'a img' ).get( 0 ) );
				}, 150 );
			};
		}, 150 );

		// Support Jetpack images lazy loads.
		$gallery.on( 'jetpack-lazy-loaded-image', '.wp-post-image', function() {
			$( this ).one( 'load', function() {
				changeProductBackground( this );
			} );
		} );

		// Change background when variation changed
		$gallery.on( 'woocommerce_gallery_reset_slide_position', function() {
			changeProductBackground( $image.get( 0 ) );
		} );

		/**
		 * Change product backgound color
		 */
		function changeProductBackground( image ) {
			// Stop if this image is not loaded.
			if ( image.src === '' ) {
				return;
			}

			if ( image.classList.contains( 'jetpack-lazy-image' ) ) {
				if ( ! image.dataset['lazyLoaded'] ) {
					return;
				}
			}

			var rgb = imageColor.getBackGroundColor( image );
			$product.get( 0 ).style.backgroundColor = 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')';
		}
	}

	/**
	 * Add extra script for product variation swatches.
	 * This function will run after plugin swatches did.
	 */
	konte.productVariationSwatches = function() {
		$( document.body ).on( 'tawcvs_initialized', function() {
			var $form = $( '.variations_form' );

			konte.modifyVariationSwatches( $form );
		} );
	};

	/**
	 * Modify the product gallery carousel
	 */
	konte.productGalleryCarousel = function() {
		var $product = $( 'div.product' );

		if ( !$product.length ) {
			return;
		}

		var $gallery = $( '.woocommerce-product-gallery', $product );

		if ( ! $gallery.length ) {
			return;
		}

		setTimeout( function() {
			$gallery.each( function() {
				konte.addIndexToCarouselControlNav( this );
			} )
		}, 100 );
	};

	/**
	 * Appends a span to the product gallery carousel control nav
	 */
	konte.addIndexToCarouselControlNav = function( carousel ) {
		$( '.flex-control-nav > li', carousel ).each( function( index, el ) {
			$( el ).append( $( '<span class="flex-nav-index"/>' ).text( index + 1 ) );
		} );
	}

	/**
	 * Special functions for product layout v1.
	 * Set product background color for single product page.
	 * Set height of product summary equal to the gallery's.
	 */
	konte.singleProductV1 = function() {
		var $product = $( 'div.product.layout-v1' );

		if ( !$product.length ) {
			return;
		}

		// Make product fullwidth
		konte.productFullWidth( $product );

		// Set top padding of product
		$product.css( {paddingTop: $( '#masthead' ).height()} );

		// Change background color
		if ( !$product.hasClass( 'background-set' ) && konte.data.product_auto_background === '1' ) {
			konte.productBackgroundFromGallery( $product );
		}

		var $gallery = $( '.woocommerce-product-gallery', $product );

		// Fix slider's height on resize. Do not know why the default handler of Flexslider not working.
		$( window ).on( 'resize', function() {
			var slider = $gallery.data( 'flexslider' );

			if ( slider ) {
				setTimeout( function() {
					var setHeight = $( '.woocommerce-product-gallery__image:eq(0)', $gallery ).height();

					$gallery.height( setHeight );
				}, 100 );
			}

			// Update the top padding.
			$product.css( {paddingTop: $( '#masthead' ).height()} );
		} );
	};

	/**
	 * Special functions for product layout v2.
	 * Enable zooming for gallery thumbnails.
	 * Make the summary sticky.
	 */
	konte.singleProductV2 = function() {
		var $product = $( 'div.product.layout-v2' );

		if ( !$product.length ) {
			return;
		}

		var $window = $( window ),
			$summary = $product.find( '.summary' ),
			summarySticky = false;

		// Make product fullwidth
		konte.productFullWidth( $product );

		// Init zoom for product gallery images
		if ( '1' === konte.data.product_image_zoom ) {
			$product.find( '.woocommerce-product-gallery .woocommerce-product-gallery__image' ).each( function() {
				konte.zoomSingleProductImage( this );
			} );
		}

		// Sticky summary
		if ( $.fn.stick_in_parent && konte.data.product_sticky_summary && konte.data.product_summary_sticky_mode == 'advanced' ) {
			$summary.on( 'sticky_kit:bottom', function() {
				$( this ).parent().css( 'position', 'static' );
			} );

			stickySummary();

			$window.on( 'resize', stickySummary );
		}

		/**
		 * Sticky summary
		 */
		function stickySummary() {
			var  options = {};

			if ( konte.data.sticky_header === 'normal' ) {
				var offset = $('#masthead').height(),
					$topbar = $( '#topbar' );

				if ( $topbar.length ) {
					offset += $topbar.height();
				}

				options = {
					recalc_every: 1,
					offset_top: offset
				}
			}

			if ( $window.width() > 991 ) {
				if ( ! summarySticky ) {
					$summary.stick_in_parent( options );
				}
				summarySticky = true;
			} else {
				$summary.trigger( 'sticky_kit:detach' );
				summarySticky = false;
			}
		}
	}

	/**
	 * Special functions for product layout v3.
	 * Change the height of product gallery.
	 */
	konte.singleProductV3 = function() {
		var $product = $( 'div.product.layout-v3' );

		if ( !$product.length ) {
			return;
		}

		var $header = $( '#masthead' );

		// Set top padding of product
		$product.css( {paddingTop: $header.height()} );
		$( '.product-toolbar' ).css( {top: $header.height()} );

		// Change background color
		if ( !$product.hasClass( 'background-set' ) && konte.data.product_auto_background === '1' ) {
			konte.productBackgroundFromGallery( $product );
		}

		// Set gallery height
		$( '.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:eq(0) .wp-post-image' ).one( 'load', setGalleryHeight );

		setTimeout( function() {
			setGalleryHeight();
		}, 200 );

		$( window ).on( 'resize', setGalleryHeight );

		/**
		 * Set gallery height
		 */
		function setGalleryHeight() {
			var height = $( window ).height() - $header.outerHeight() - $( '#colophon' ).outerHeight() + 19,
				$topbar = $( '#topbar' ),
				$footer = $( '.footer-main' );

			if ( $topbar.length ) {
				height -= $topbar.outerHeight();
			}

			if ( $footer.length ) {
				height -= parseFloat( $footer.css( 'padding-top' ) );
			}

			$product.find( '.woocommerce-product-gallery' ).css( {
				maxHeight: height,
				height   : height
			} );
		}
	}

	/**
	 * Special functions for product layout v4.
	 */
	konte.singleProductV4 = function() {
		var $product = $( 'div.product.layout-v4' );

		if ( !$product.length ) {
			return;
		}

		// Quantity dropdown.
		if ( 'dropdown' === konte.data.product_quantity_input_style ) {
			$product.find( '.summary .quantity .qty' ).quantityDropdown();
		}
	}

	/**
	 * Special functions for product layout v5.
	 */
	konte.singleProductV5 = function() {
		var $product = $( 'div.product.layout-v5' ),
			$summary = $product.find( '.summary' ),
			$summaryInner = $summary.children( '.summary-inner' ),
			$window = $( window ),
			headerHeight = $( '#masthead' ).height(),
			summarySticky = false;

		if ( !$product.length ) {
			return;
		}

		// Make product fullwidth
		productWidth();

		$window.on( 'resize', function() {
			productWidth();
		} );

		// Set top padding of product
		$summary.css( {paddingTop: headerHeight} );
		$summaryInner.css( 'max-height', $window.height() - headerHeight );

		// Init zoom for product gallery thumbnails
		if ( konte.data.product_image_zoom ) {
			$product.find( '.product-gallery-thumbnails .woocommerce-product-gallery__image' ).each( function() {
				konte.zoomSingleProductImage( this );
			} );
		}

		// Sticky summary
		if ( $.fn.stick_in_parent && konte.data.product_sticky_summary && konte.data.product_summary_sticky_mode == 'advanced' ) {
			$summaryInner
				.on( 'sticky_kit:bottom', function() {
					$( this ).closest( '.product-gallery-summary' ).addClass( 'summary-sticky-bottom' );
				} )
				.on( 'sticky_kit:unbottom', function() {
					$( this ).closest( '.product-gallery-summary' ).removeClass( 'summary-sticky-bottom' );
				} );

			setTimeout( function() {
				stickySummary();
			}, 100 );

			$window.on( 'resize', stickySummary );
		}

		/**
		 * Set product width
		 */
		function productWidth() {
			var width = $window.width(),
				bonus = width > 1440 ? 60 : 0;

			width -= konte.getVerticalHeaderWidth();

			$product.width( width );

			if ( konte.data.rtl ) {
				$product.css( 'margin-right', -width / 2 );
				$summary.css( 'padding-left', width / 2 - $( '.konte-container' ).width() / 2 + bonus );
			} else {
				$product.css( 'margin-left', -width / 2 );
				$summary.css( 'padding-right', width / 2 - $( '.konte-container' ).width() / 2 + bonus );
			}
		}

		/**
		 * Sticky summary
		 */
		function stickySummary() {
			var options = { parent: $product.get(0) };

			if (konte.data.sticky_header === 'normal') {
				var offset = $('#masthead').height(),
					$topbar = $('#topbar');

				if ($topbar.length) {
					offset += $topbar.height();
				}

				options = {
					recalc_every: 1,
					offset_top: offset,
					parent: $product.get(0)
				}
			}

			if ( $window.width() > 991 ) {
				if ( ! summarySticky ) {
					$summaryInner.stick_in_parent( options );
				}
				summarySticky = true;
			} else {
				$summaryInner.trigger( 'sticky_kit:detach' );
				summarySticky = false;
			}
		}
	};

	/**
	 * Special functions for product layout v6.
	 */
	konte.singleProductV6 = function() {
		var $product = $( 'div.product.layout-v6' );

		if ( !$product.length ) {
			return;
		}
	};

	/**
	 * Special functions for product layout v7.
	 */
	konte.singleProductV7 = function() {
		var $product = $( 'div.product.layout-v7' );

		if ( !$product.length ) {
			return;
		}
	};

	/**
	 * Init the carousel for product gallery thumbnails
	 *
	 * @param {Object} options
	 */
	konte.productGalleryThumbnailCarousel = function( options ) {
		var $product = $( 'div.product' );

		if ( ! $product.length ) {
			return;
		}

		var $gallery = $( '.woocommerce-product-gallery', $product ),
			$thumbnails = $gallery.find( '.flex-control-thumbs' ),
			options = {
				rtl            : !! konte.data.rtl,
				vertical       : false,
				verticalSwiping: false,
				swipeToSlide   : true,
				touchThreshold : 100,
				infinite       : false,
				slidesToShow   : 4,
				slidesToScroll : 1,
				prevArrow      : '<span class="slick-prev slick-arrow svg-icon icon-left icon-small"><svg width="16" height="16"><use xlink:href="#left"></use></svg></span>',
				nextArrow      : '<span class="slick-next slick-arrow svg-icon icon-right icon-small"><svg width="16" height="16"><use xlink:href="#right"></use></svg></span>'
			};

		if ( $product.hasClass( 'layout-v4' ) ) {
			options.vertical = true,
			options.verticalSwiping = true;
		} else if ( $product.hasClass( 'layout-v6' ) ) {
			options.vertical = true,
			options.verticalSwiping = true;
		} else if ( $product.hasClass( 'layout-v7' ) ) {
			options.adaptiveHeight = true;
		}

		var isVertical = options.vertical ? true : false;

		if ( $gallery.hasClass( 'woocommerce-product-gallery--mobile-nav-dots' ) ) {
			options.responsive = [
				{
					breakpoint: 991,
					settings: 'unslick'
				}
			];
		} else if ( $gallery.hasClass( 'woocommerce-product-gallery--mobile-nav-thumbnails' ) ) {
			options.mobileFirst = true;
			options.responsive = [
				{
					breakpoint: 0,
					settings: {
						variableWidth  : true,
						vertical       : false,
						verticalSwiping: false,
					}
				}
			];

			var desktopOptions = {
				breakpoint: 992,
				settings: {}
			}

			if ( $gallery.hasClass( 'woocommerce-product-gallery--nav-thumbnails' ) ) {
				desktopOptions.settings = {
					vertical: options.vertical,
					verticalSwiping: options.verticalSwiping
				};

				delete options.vertical;
				delete options.verticalSwiping;
			} else {
				desktopOptions.settings = 'unslick';
			}

			options.responsive.push( desktopOptions );
		}

		var initGalleryThumbsCarousel = function() {
			var slidesToShow = 0;

			if ( isVertical ) {
				// On mobile vertical carousel is not used.
				if ( options.mobileFirst && window.innerWidth < 992 ) {
					slidesToShow = parseInt( $gallery.width() / ( $thumbnails.find( 'li:eq(0) > img' ).width() + 10 ) );
				} else {
					slidesToShow = parseInt( $gallery.height() / ( $thumbnails.find( 'li:eq(0) > img' ).height() ) );
				}
			} else {
				slidesToShow = parseInt( $gallery.width() / ( $thumbnails.find( 'li:eq(0)' ).width() + 10 ) );
			}

			if ( ! options.mobileFirst || window.innerWidth >= 992 || ! isVertical ) {
				options.slidesToShow = slidesToShow;
			}

			if ( ! $thumbnails.hasClass( 'slick-initialized' ) && slidesToShow && $thumbnails.children().length > slidesToShow ) {
				$thumbnails.slick( options );
			}
		};

		setTimeout( function() {
			// To make sure FlexSlider is initialized.
			if ( ! $thumbnails.length ) {
				$gallery = $( '.woocommerce-product-gallery', $product );
				$thumbnails = $gallery.find( '.flex-control-thumbs' );
			}

			var firstThumb = $thumbnails.children().first().find( 'img' ).get( 0 );

			if ( ! firstThumb ) {
				return;
			}

			if ( firstThumb.complete ) {
				initGalleryThumbsCarousel();
			} else {
				firstThumb.addEventListener( 'load', initGalleryThumbsCarousel );
			}
		}, 200 );

		$( window ).on( 'resize', function() {
			// With some product layouts, the gallery is replaced by a clone.
			// Have to update the variable accordingly to the replacement.
			// See more: konte.responsiveProductGallery()
			setTimeout( function() {
				if ( ! $.contains( document.documentElement, $thumbnails.get(0) ) ) {
					$gallery = $( '.woocommerce-product-gallery', $product );
					$thumbnails = $gallery.find( '.flex-control-thumbs' );
				}

				initGalleryThumbsCarousel();
			}, 200 );
		} );
	}

	/**
	 * Init slider for product gallery on mobile.
	 */
	konte.responsiveProductGallery = function() {
		if ( konte.data.product_gallery_slider || ! $.fn.wc_product_gallery ) {
			return;
		}

		var $window = $( window ),
			$product = $( '.woocommerce div.product' ),
			default_flexslider_enabled = false,
			default_flexslider_options = {};

		if ( ! $product.length ) {
			return;
		}

		var $gallery = $( '.woocommerce-product-gallery', $product ),
			$originalGallery = $gallery.clone(),
			sliderActive = $gallery.find( '.flex-viewport' ).length;

		$originalGallery.children( '.woocommerce-product-gallery__trigger' ).remove();

		// Turn off events then we init them again later.
		$originalGallery.off();

		if ( typeof wc_single_product_params !== undefined ) {
			default_flexslider_enabled = wc_single_product_params.flexslider_enabled;
			default_flexslider_options = wc_single_product_params.flexslider;
		}

		initProductGallery();
		$window.on( 'resize', initProductGallery );

		// Init product gallery
		function initProductGallery() {
			if ( $window.width() > 991 ) {
				if ( ! sliderActive ) {
					return;
				}

				if ( typeof wc_single_product_params !== undefined ) {
					wc_single_product_params.flexslider_enabled = default_flexslider_enabled;
					wc_single_product_params.flexslider = default_flexslider_options;
				}

				// Destroy is not supported at this moment.
				$gallery.replaceWith( $originalGallery.clone() );
				$gallery = $( '.woocommerce-product-gallery', $product );

				$gallery.each( function() {
					$( this ).wc_product_gallery();
				} );

				$( 'form.variations_form select', $product ).trigger( 'change' );

				// Init zoom for product gallery images
				if ( '1' === konte.data.product_image_zoom && $product.hasClass( 'layout-v2' ) ) {
					$gallery.find( '.woocommerce-product-gallery__image' ).each( function() {
						konte.zoomSingleProductImage( this );
					} );
				}

				sliderActive = false;
			} else {
				if ( sliderActive ) {
					return;
				}

				if ( typeof wc_single_product_params !== undefined ) {
					wc_single_product_params.flexslider_enabled = true;
					// wc_single_product_params.flexslider.controlNav = true;
				}

				$gallery.replaceWith( $originalGallery.clone() );
				$gallery = $( '.woocommerce-product-gallery', $product );

				// Support Jetpack lazy load.
				$( document.body ).trigger( 'konte_lazy_load_images' );

				setTimeout( function() {
					$gallery.each( function() {
						$( this ).wc_product_gallery();

						konte.addIndexToCarouselControlNav( this );
					} );
				}, 100 );

				$( 'form.variations_form select', $product ).trigger( 'change' );

				sliderActive = true;
			}
		}
	};

	/**
	 * Related & ppsell products carousel.
	 */
	konte.relatedProductsCarousel = function() {
		if ( typeof Swiper === 'undefined' ) {
			return;
		}

		var $related = $( '.products.related, .products.upsells' );

		if ( ! $related.length ) {
			return;
		}

		var $products = $related.find( 'ul.products' );

		$products.wrap( '<div class="konte-swiper-container swiper-container linked-products-carousel" style="opacity: 0;"></div>' );
		$products.after( '<div class="swiper-pagination"></div>' );
		$products.addClass( 'swiper-wrapper' );
		$products.find( 'li.product' ).addClass( 'swiper-slide' );

		var carousel = new Swiper( '.linked-products-carousel', {
			loop          : false,
			slidesPerView : 1,
			slidesPerGroup: 1,
			spaceBetween  : 40,
			speed         : 800,
			watchOverflow : true,
			pagination    : {
				el          : '.swiper-pagination',
				type        : 'bullets',
				clickable   : true,
				renderBullet: function( index, className ) {
					return '<span class="' + className + '"><span></span></span>';
				}
			},
			on            : {
				init: function() {
					this.$el.css( 'opacity', 1 );
				}
			},
			breakpoints   : {
				360: {
					spaceBetween  : 20,
					slidesPerView : 2,
					slidesPerGroup: 2
				},
				768: {
					spaceBetween  : 20,
					slidesPerView : 2,
					slidesPerGroup: 2
				},
				992 : {
					slidesPerView : 3,
					slidesPerGroup: 3
				},
				1200: {
					slidesPerView : 4,
					slidesPerGroup: 4
				}
			}
		} );
	};

	/**
	 * Zoom an image.
	 * Copy from WooCommerce single-product.js file.
	 */
	konte.zoomSingleProductImage = function( zoomTarget ) {
		if ( typeof wc_single_product_params == 'undefined' || !$.fn.zoom ) {
			return;
		}

		var $target = $( zoomTarget ),
			width = $target.width(),
			zoomEnabled = false;

		$target.each( function( index, target ) {
			var $image = $( target ).find( 'img' );

			if ( $image.data( 'large_image_width' ) > width ) {
				zoomEnabled = true;
				return false;
			}
		} );

		// Only zoom if the img is larger than its container.
		if ( zoomEnabled ) {
			var zoom_options = $.extend( {
				touch: false
			}, wc_single_product_params.zoom_options );

			if ( 'ontouchstart' in document.documentElement ) {
				zoom_options.on = 'click';
			}

			$target.trigger( 'zoom.destroy' );
			$target.zoom( zoom_options );
		}
	}

	/**
	 * Add class to .form-row when inputs are focused.
	 */
	konte.formFieldFocus = function() {
		$( document.body )
			.on( 'focus', '.form-row .input-text, .wpcf7-form-control', function() {
				$( this ).parent().addClass( 'focused' );

				if ( $( this ).is( '.wpcf7-form-control' ) ) {
					$( this ).closest( 'label' ).addClass( 'focused' );
				} else {
					$( this ).closest( '.form-row' ).addClass( 'focused' );
				}
			} )
			.on( 'blur', '.form-row  .input-text, .wpcf7-form-control', function() {
				if ( $( this ).val() === '' ) {
					if ( $( this ).is( '.wpcf7-form-control' ) ) {
						$( this ).closest( 'label' ).removeClass( 'focused' );
					} else {
						$( this ).closest( '.form-row' ).removeClass( 'focused' );
					}
				}
			} )
			.find( '.form-row .input-text, .wpcf7-form-control' ).each( function() {
				if ( $( this ).val() != '' ) {
					if ( $( this ).is( '.wpcf7-form-control' ) ) {
						$( this ).closest( 'label' ).addClass( 'focused' );
					} else {
						$( this ).closest( '.form-row' ).addClass( 'focused' );
					}
				}
			} );
	};

	/**
	 * Login & register tabs.
	 */
	konte.loginTabs = function() {
		$( document.body ).on( 'click', '.login-tabs-nav .tab-nav', function( event ) {
			event.preventDefault();

			var $tab = $( this ),
				$panels = $tab.parent().siblings( '.u-columns' );

			if ( $tab.hasClass( 'active' ) ) {
				return;
			}

			$tab.addClass( 'active' ).siblings().removeClass( 'active' );
			$panels.children().eq( $tab.index() ).addClass( 'active' ).siblings().removeClass( 'active' );
		} );
	};

	/**
	 * Toggle register/login form in the login panel.
	 */
	konte.loginPanel = function() {
		$( document.body )
			.on( 'click', '#login-panel .create-account', function( event ) {
				event.preventDefault();

				$( this ).closest( 'form.login' ).fadeOut( function() {
					$( this ).next( 'form.register' ).fadeIn();
				} );
			} ).on( 'click', '#login-panel a.login', function( event ) {
			event.preventDefault();

			$( this ).closest( 'form.register' ).fadeOut( function() {
				$( this ).prev( 'form.login' ).fadeIn();
			} );
		} ).on( 'click', '[data-toggle="off-canvas"][data-target="login-panel"]', function() {
			$( '#login-panel' ).find( 'form' ).hide().filter( '.login' ).show();
		} );
	};

	/**
	 * Ajax login before refresh page
	 */
	konte.loginPanelAuthenticate = function() {
		$( '#login-panel' ).on( 'submit', 'form.login', function authenticate( event ) {
			var username = $( 'input[name=username]', this ).val(),
				password = $( 'input[name=password]', this ).val(),
				remember = $( 'input[name=rememberme]', this ).is( ':checked' ),
				nonce = $( 'input[name=woocommerce-login-nonce]', this ).val(),
				$button = $( '[type=submit]', this ),
				$form = $( this ),
				$box = $form.next( '.woocommerce-error' );

			if ( ! username ) {
				$( 'input[name=username]', this ).focus();

				return false;
			}

			if ( ! password ) {
				$( 'input[name=password]', this ).focus();

				return false;
			}

			$form.find( '.woocommerce-error' ).remove();

			if ( $form.data( 'validated' ) ) {
				return true;
			}

			$button.html( '<span class="spinner"></span>' );

			if ( $box.length ) {
				$box.fadeOut();
			}

			$.post(
				konte.data.ajax_url,
				{
					action: 'konte_login_authenticate',
					security: nonce,
					username: username,
					password: password,
					remember: remember
				},
				function( response ) {
					if ( ! response.success ) {
						if ( ! $box.length ) {
							$box = $( '<div class="woocommerce-error" role="alert"/>' );

							$box.append( '<span class="svg-icon icon-error size-normal message-icon"><svg role="img"><use href="#error" xlink:href="#error"></use></svg></span>' )
								.append( '<ul class="error-message" />' )
								.append( '<span class="svg-icon icon-close size-normal close-message"><svg role="img"><use href="#close" xlink:href="#close"></use></svg></span>' );

							$box.hide().prependTo( $form );
						}

						$box.find( '.error-message' ).html( '<li>' + response.data + '</li>' );
						$box.fadeIn();
						$button.html( $button.attr( 'value' ) );
					} else {
						$form.data( 'validated', true ).submit();
						$button.html( $button.data( 'signed' ) );
					}
				}
			);

			event.preventDefault();
		} ).on( 'click', '.woocommerce-error .close-message', function() {
			// Remove the error message to fix the layout issue.
			$( this ).closest( '.woocommerce-error' ).fadeOut( function() {
				$( this ).remove();
			} );

			return false;
		} );
	};

	/**
	 * Initialize masonry layout for the shop page.
	 */
	konte.productsMasonry = function() {
		var $container = $( 'ul.products.layout-masonry' ),
			$window = $( window );

		if ( !$.fn.masonry || !$container.length ) {
			return;
		}

		$container.on( 'layoutComplete', layoutMasonry );

		initMansonry();

		$window.on( 'resize', initMansonry );

		$( document.body ).on( 'post-load konte_products_loaded', function( event, products, appended ) {
			if ( $window.width() < 992 ) {
				return;
			}

			if ( appended ) {
				$container.masonry( 'appended', products ).masonry();
			} else {
				$container = $( 'ul.products.layout-masonry' );

				$container.on( 'layoutComplete', layoutMasonry );

				initMansonry();
			}
		} );

		/**
		 * Init masonry layout
		 */
		function initMansonry() {
			if ( $container.children().length <= 1 ) {
				return;
			}

			if ( $window.width() < 992 ) {
				$container.each( function() {
					var $this = $( this );

					if ( $this.hasClass( 'masonry' ) ) {
						$this.masonry( 'destroy' );
					}
				} );
			} else {
				$container.each( function() {
					var $this = $( this ),
						options = {
							itemSelector:       'li.product',
							columnWidth:        'li.product:nth-child(2)',
							percentPosition:    true,
							transitionDuration: 0,
							isRTL:              !! konte.data.rtl
						};

					if ( ! $this.hasClass( 'masonry' ) ) {
						$this.masonry( options );

						$this.imagesLoaded().progress( function() {
							$this.masonry( 'layout' );
						} );
					}
				} );
			}
		}

		/**
		 * Change the masonry layout as the design.
		 */
		function layoutMasonry() {
			var prop = konte.data.rtl ? 'right' : 'left';

			$container
				.children( ':nth-child(10n+1), :nth-child(10n+6), :nth-child(10n+9)' )
				.css( prop, '0' )
				.end()
				.children( ':nth-child(10n+2), :nth-child(10n+4)' )
				.css( prop, '50%' )
				.end()
				.children( ':nth-child(10n+3), :nth-child(10n+5), :nth-child(10n+8), :nth-child(10n)' )
				.css( prop, '75%' )
				.end()
				.children( ':nth-child(10n+7)' )
				.css( prop, '25%' );
		}
	};

	/**
	 * Proudcts carousel layout.
	 */
	konte.productsCarousel = function() {
		var $container = $( '.products-carousel' );

		if ( !$container.length ) {
			return;
		}

		$container.find( 'ul.products li.product' ).addClass( 'swiper-slide' );

		var carousel = new Swiper( '.products-carousel', {
			loop         : false,
			slidesPerView: 1,
			spaceBetween : 40,
			scrollbar    : {
				el       : '.swiper-scrollbar',
				hide     : false,
				draggable: true
			},
			on           : {
				init: function() {
					$container.css( 'opacity', 1 );
				}
			},
			breakpoints  : {
				360 : {
					slidesPerView: 2
				},
				992 : {
					slidesPerView: 3
				},
				1200: {
					slidesPerView: 4
				}
			}
		} );

		var xhr;

		carousel.on( 'reachEnd', function() {
			var $nav = $( '.woocommerce-navigation.ajax-navigation' );

			if ( !$nav.length ) {
				return;
			}

			if ( xhr ) {
				return;
			}

			var loadingHolder = document.createElement( 'li' );

			$( loadingHolder )
				.addClass( 'swiper-slide loading-placeholder' )
				.css( {height: carousel.height - 140} )
				.append( '<span class="spinner"></span>' );

			carousel.appendSlide( loadingHolder );
			carousel.update();

			xhr = $.get( $nav.find( 'a' ).attr( 'href' ), function( response ) {
				var $content = $( '#main', response ),
					$list = $( 'ul.products', $content ),
					$products = $list.children(),
					$newNav = $( '.woocommerce-navigation.ajax-navigation', $content );

				if ( $newNav.length ) {
					$nav.find( 'a' ).replaceWith( $( 'a', $newNav ) );
				} else {
					$nav.fadeOut( function() {
						$nav.remove();
					} );
				}

				$( loadingHolder ).remove();
				$products.css( {opacity: 0} );

				carousel.appendSlide( $products.addClass( 'swiper-slide' ).get() );
				carousel.update();

				$products.animate( {opacity: 1} );
				xhr = false;

				$( document.body ).trigger( 'konte_products_loaded', [$products, true] );
			} );
		} );
	};

	/**
	 * Product thumbnails slider.
	 */
	konte.productThumbnailsSlider = function() {
		var options = {
			slidesToShow  : 1,
			slidesToScroll: 1,
			infinite      : false,
			lazyLoad      : 'ondemand',
			dots          : false,
			swipe         : false,
			rtl           : !! konte.data.rtl,
			prevArrow     : '<span class="slick-prev slick-arrow svg-icon icon-left icon-small"><svg width="16" height="16"><use xlink:href="#left"></use></svg></span>',
			nextArrow     : '<span class="slick-next slick-arrow svg-icon icon-right icon-small"><svg width="16" height="16"><use xlink:href="#right"></use></svg></span>'
		}

		if ( konte.data.rtl ) {
			$( '.product-thumbnails--slider' ).attr( 'dir', 'rtl' );
		}

		$( '.product-thumbnails--slider' ).slick( options );

		// Filter plugins.
		$( document.body ).on( 'post-load', function() {
			if ( konte.data.rtl ) {
				$( '.product-thumbnails--slider', 'ul.products.hover-slider' ).attr( 'dir', 'rtl' );
			}

			$( '.product-thumbnails--slider', 'ul.products.hover-slider' ).slick( options );
		} )

		// Ajax product loaded.
		$( document.body ).on( 'konte_products_loaded', function( event, products ) {
			if ( konte.data.rtl ) {
				$( '.product-thumbnails--slider', products ).attr( 'dir', 'rtl' );
			}

			$( '.product-thumbnails--slider', products ).slick( options );
		} );
	};

	/**
	 * Product thumbnail zoom.
	 */
	konte.productThumbnailZoom = function() {
		$( '.product-thumbnail-zoom' ).each( function() {
			var $el = $( this );

			$el.zoom( {
				url: $el.attr( 'data-zoom_image' )
			} );
		} );

		// Filter plugins.
		$( document.body ).on( 'post-load', function() {
			$( '.product-thumbnail-zoom', 'ul.products.hover-zoom' ).each( function() {
				var $el = $( this );

				$el.zoom( {
					url: $el.attr( 'data-zoom_image' )
				} );
			} );
		} );

		// Ajax product loaded.
		$( document.body ).on( 'konte_products_loaded', function( event, products ) {
			$( '.product-thumbnail-zoom', products ).each( function() {
				var $el = $( this );

				$el.zoom( {
					url: $el.attr( 'data-zoom_image' )
				} );
			} );
		} );
	};

	/**
	 * Quick view modal.
	 */
	konte.productQuickView = function() {
		$( document.body ).on( 'click', '.quick_view_button', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				product_id = $el.data( 'product_id' ),
				$target = $( '#' + $el.data( 'target' ) ),
				$container = $target.find( '.woocommerce' ),
				ajax_url = woocommerce_params ? woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'product_quick_view' ) : konte.data.ajax_url;

			$target.addClass( 'loading' );
			$container.find( '.product' ).html( '' );

			$.post(
				ajax_url,
				{
					action    : 'konte_get_product_quickview',
					product_id: product_id,
					security  : konte.data.product_quickview_nonce
				},
				function( response ) {
					$container.find( '.product' ).replaceWith( response.data );

					if ( response.success ) {
						update_quickview();

						if ( 'modal' == $el.data( 'toggle' ) ) {
							update_quickview_modal();
						} else {
							update_quickview_panel();
						}
					}

					$target.removeClass( 'loading' );

					$( document.body ).trigger( 'konte_product_quickview_loaded', [response, product_id, konte] );
				}
			).fail( function() {
				window.location.herf = $el.attr( 'href' );
			} );

			/**
			 * Update quick view common elements.
			 */
			function update_quickview() {
				var $product = $container.find( '.product' ),
					$gallery = $product.find( '.woocommerce-product-gallery' ),
					$variations = $product.find( '.variations_form' );

				// Prevent clicking on gallery image link.
				$gallery.on( 'click', '.woocommerce-product-gallery__image a', function( event ) {
					event.preventDefault();
				} );

				// Init flex slider.
				if ( $gallery.find( '.woocommerce-product-gallery__image' ).length > 1 ) {
					$gallery.flexslider( {
						selector      : '.woocommerce-product-gallery__wrapper > .woocommerce-product-gallery__image',
						animation     : 'slide',
						animationLoop : false,
						animationSpeed: 500,
						controlNav    : true,
						directionNav  : true,
						prevText      : '<span class="svg-icon icon-left icon-small"><svg width="16" height="16"><use xlink:href="#left"></use></svg></span>',
						nextText      : '<span class="svg-icon icon-right icon-small"><svg width="16" height="16"><use xlink:href="#right"></use></svg></span>',
						slideshow     : false,
						rtl           : !! konte.data.rtl,
						start         : function() {
							$gallery.css( 'opacity', 1 );
						},
					} );
				} else {
					$gallery.css( 'opacity', 1 );
				}

				// Make videos responsive.
				$product.find( '.summary iframe' ).wrap( '<figure class="wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper"/></div>' );

				// Variations form.
				if ( $variations.length ) {
					$variations.wc_variation_form().find( '.variations select:eq(0)' ).change();

					$variations.on( 'reset_image found_variation', function() {
						$gallery.flexslider( 0 );
					} );

					if ( $.fn.tawcvs_variation_swatches_form ) {
						$variations.tawcvs_variation_swatches_form();
						konte.modifyVariationSwatches( $variations );
					}

					$( document.body ).trigger( 'init_variation_swatches' );
				}
			}

			/**
			 * Update quick view modal's special elements.
			 */
			function update_quickview_modal() {
				var $product = $container.find( '.product' ),
					$gallery = $product.find( '.woocommerce-product-gallery' );

				// Force height for images
				$gallery.find( 'img' ).css( 'height', $product.outerHeight() );

				if ( konte.data.product_quickview_auto_background && !$product.hasClass( 'background-set' ) ) {
					$product.imagesLoaded( function() {
						konte.productBackgroundFromGallery( $product );
					} );
				}

				if ( typeof PerfectScrollbar !== 'undefined' ) {
					new PerfectScrollbar( $product.find( '.summary' ).get( 0 ) );
				}
			}

			/**
			 * Update quick view panel's special elements.
			 */
			function update_quickview_panel() {
				if ( typeof PerfectScrollbar !== 'undefined' ) {
					new PerfectScrollbar( $container.find( '.product' ).get( 0 ) );
				}
			}
		} );
	};

	/**
	 * Empty the quickview modal/panel on close to prevent sound of videos,
	 * or any unexpected effects.
	 */
	konte.emptyQuickViewOnClose = function() {
		$( document.body ).on( 'konte_off_canvas_closed konte_modal_closed', function( event, target ) {
			var $quickview = $( target );

			if ( ! $quickview.is( '#quick-view-modal' ) && ! $quickview.is( '#quick-view-panel' ) ) {
				return;
			}

			$quickview.find( '.product' ).html( '' );
		} );
	};

	/**
	 * Auto-close the quick-view on successful product add to cart.
	 */
	konte.autoCloseQuickView = function() {
		$( document.body ).on( 'added_to_cart', function() {
			if ( '1' === konte.data.product_quickview_auto_close || '1' === konte.data.cart_open_after_added ) {
				konte.closeOffCanvas( '#quick-view-modal' );
				konte.closeModal( '#quick-view-modal' );
			}
		} );
	};

	/**
	 * Modify variation swatches script.
	 *
	 * @param $form
	 */
	konte.modifyVariationSwatches = function( $form ) {
		// Remove class "swatches-support" if there is no swatches in this product
		var hasSwatches = false;

		if ( $form.find( '.tawcvs-swatches' ).length ) {
			hasSwatches = true;
		}

		if ( ! hasSwatches ) {
			$form.removeClass( 'swatches-support' );
		}

		// Change alert style
		$form.off( 'tawcvs_no_matching_variations' );
		$form.on( 'tawcvs_no_matching_variations', function( event ) {
			event.preventDefault();

			$form.find( '.woocommerce-variation.single_variation' ).show();
			if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
				$form.find( '.single_variation' ).stop( true, true ).slideDown().html( '<p class="invalid-variation-combination">' + wc_add_to_cart_variation_params.i18n_no_matching_variations_text + '</p>' );
			}
		} );
	};

	/**
	 * Ajax load more products.
	 */
	konte.loadMoreProducts = function() {
		// Handles click load more button.
		$( document.body ).on( 'click', '.woocommerce-navigation.ajax-navigation a', function( event ) {
			event.preventDefault();

			var $el = $( this );

			if ( $el.hasClass( 'loading' ) ) {
				return;
			}

			$el.addClass( 'loading' );

			loadProducts( $( this ) );
		} );

		// Infinite scroll.
		if ( $( document.body ).hasClass( 'woocommerce-nav-infinite' ) ) {
			var waiting = false,
				endScrollHandle;

			$( window ).on( 'scroll', function() {
				if ( waiting ) {
					return;
				}

				waiting = true;

				// clear previous scheduled endScrollHandle
				clearTimeout( endScrollHandle );

				infiniteScoll();

				setTimeout( function() {
					waiting = false;
				}, 100 );

				// schedule an extra execution of infiniteScoll() after 200ms
				// in case the scrolling stops in next 100ms.
				endScrollHandle = setTimeout( function() {
					waiting = false;
					infiniteScoll();
				}, 200 );
			} );
		}

		/**
		 * Infinite scroll handler.
		 */
		function infiniteScoll() {
			var $navigation = $( '.woocommerce-navigation.ajax-navigation' ),
				$button = $( 'a', $navigation );

			// When almost reach to the navigation.
			if ( konte.isVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
				$button.addClass( 'loading' );

				loadProducts( $button, function( respond ) {
					$button = $navigation.find( 'a' );
				} );
			}
		}

		/**
		 * Ajax load products.
		 *
		 * @param jQuery $el Button element.
		 * @param function callback The callback function.
		 */
		function loadProducts( $el, callback ) {
			var $nav = $el.closest( '.woocommerce-navigation' ),
				url = $el.attr( 'href' );

			$.get( url, function( response ) {
				var $content = $( '#main', response ),
					$list = $( 'ul.products', $content ),
					$products = $list.children(),
					$newNav = $( '.woocommerce-navigation.ajax-navigation', $content );

				$products.each( function( index, product ) {
					$( product ).css( 'animation-delay', index * 100 + 'ms' );
				} );

				$products.appendTo( $nav.siblings( 'ul.products' ) );
				$products.addClass( 'animated konteFadeInUp' );

				if ( $newNav.length ) {
					$el.replaceWith( $( 'a', $newNav ) );
				} else {
					$nav.fadeOut( function() {
						$nav.remove();
					} );
				}

				if ( 'function' === typeof callback ) {
					callback( response );
				}

				$( document.body ).trigger( 'konte_products_loaded', [$products, true] );

				if ( konte.data.shop_nav_ajax_url_change ) {
					window.history.pushState( null, '', url );
				}
			} );
		}
	};

	/**
	 * Check if an element is in view-port or not
	 *
	 * @param jQuery el Targe element to check.
	 * @return boolean
	 */
	konte.isVisible = function( el ) {
		if ( el instanceof jQuery ) {
			el = el[0];
		}

		if ( ! el ) {
			return false;
		}

		var rect = el.getBoundingClientRect();

		return rect.bottom > 0 &&
			rect.right > 0 &&
			rect.left < (window.innerWidth || document.documentElement.clientWidth) &&
			rect.top < (window.innerHeight || document.documentElement.clientHeight);
	};

	/**
	 * Proudcts quick search.
	 */
	konte.productsQuickSearch = function() {
		var $form = $( '.products-quick-search-form' );

		if ( $.fn.select2 ) {
			$form.find( 'select' ).select2( {
				dir                    : konte.data.rtl ? 'rtl' : 'ltr',
				width                  : 'auto',
				minimumResultsForSearch: -1,
				dropdownCssClass       : 'products-quick-search-options',
				dropdownParent		   : $( '.products-quick-search-form' )
			} ).on( 'change', function() {
				$form.addClass( 'changed' );
			} );
		}

		$form.find( 'select' ).on( 'change', function() {
			$form.addClass( 'changed' );
		} );

		$form.on( 'submit', function( event ) {
			event.preventDefault();

			var $container = $( 'ul.products.main-products' ),
				$inputs = $form.find( ':input:not(:checkbox):not(:button)' ),
				url = $form.attr( 'action' ),
				separator = url.indexOf( '?' ) !== -1 ? '&' : '?',
				params = $inputs.filter( function() {
					return $( this ).val() != 0;
				} ).serialize();

			if ( params ) {
				url += separator + params;
			}

			if ( $container.hasClass( 'layout-carousel' ) ) {
				window.location.href = url;
				return false;
			}

			if ( !$container.length ) {
				$container = $( '<ul class="products"/>' );
				$form.closest( '.products-toolbar' ).siblings( '.woocommerce-info' ).replaceWith( $container );
			}

			$form.addClass( 'filtering' );
			$form.find( 'select' ).prop( 'disabled', true );
			$container.addClass( 'loading' ).append( '<li class="loading-screen"><span class="spinner"></span></li>' );

			$( document.body ).trigger( 'konte_quick_search_products_before_send_request', $container );

			$.get( url, function( response ) {
				var $html = $( response ),
					$products = $html.find( '#main ul.products' ),
					$pagination = $container.next( 'nav' );

				if ( !$products.length ) {
					$products = $html.find( '#main .woocommerce-info' );
					$( '.products-tools' ).addClass( 'out' );

					$pagination.fadeOut();
					$container.replaceWith( $products );
				} else {
					var $nav = $products.next( 'nav' ),
						$order = $( 'form.woocommerce-ordering' );

					if ( $nav.length ) {
						if ( $pagination.length ) {
							$pagination.replaceWith( $nav ).fadeIn();
						} else {
							$container.after( $nav );
						}
					} else {
						$pagination.fadeOut();
					}

					// Modify the ordering form.
					$inputs.each( function() {
						var $input = $( this ),
							name = $input.attr( 'name' ),
							value = $input.val();

						if ( name === 'orderby' ) {
							return;
						}

						$order.find( 'input[name="' + name + '"]' ).remove();

						if ( value !== '' && value != 0 ) {
							$( '<input type="hidden" name="' + name + '">' ).val( value ).appendTo( $order );
						}
					} );

					// Replace the column selector.
					$( '.products-toolbar .columns-switcher' ).replaceWith( $html.find( '.products-toolbar .columns-switcher' ) );

					// Replace result count.
					$( '.products-toolbar .woocommerce-result-count' ).replaceWith( $html.find( '.products-toolbar .woocommerce-result-count' ) );

					// Replace tabs.
					$( '.products-toolbar .products-tabs' ).replaceWith( $html.find( '.products-toolbar .products-tabs' ) );

					// Show the toolbar.
					$( '.products-tools' ).removeClass( 'out' );

					$products.children().each( function( index, product ) {
						$( product ).css( 'animation-delay', index * 100 + 'ms' );
					} );

					$container.replaceWith( $products );

					$products.find( 'li.product' ).addClass( 'animated konteFadeInUp' );

					$( document.body ).trigger( 'konte_products_loaded', [$products.children(), false] );
				}

				$form.removeClass( 'changed filtering' );
				$form.find( 'select' ).prop( 'disabled', false );
				window.history.pushState( null, '', url );

				$( document.body ).trigger( 'konte_products_quick_search_request_success', [$products.children()] );
			} );
		} );
	};

	/**
	 * Handle products tools events.
	 */
	konte.productsTools = function() {
		var $window = $( window ),
			$tools = $( '.products-tools' );

		// Products ordering.
		if ( $.fn.select2 ) {
			$tools.find( '.woocommerce-ordering select' ).select2( {
				dir                    : konte.data.rtl ? 'rtl' : 'ltr',
				width                  : 'auto',
				minimumResultsForSearch: -1,
				dropdownCssClass       : 'products-ordering',
				dropdownParent         : $tools.find( '.woocommerce-ordering' )
			} );
		}

		// Products filter.
		var $filter = $tools.find( '#products-filter' );

		if ( $filter.length && $filter.hasClass( 'dropdown-panel' ) ) {
			setDropdownFilterPosition();

			$window.on( 'resize', setDropdownFilterPosition );
		}

		// Toggle products filter.
		$( document.body ).on( 'click', '.products-tools .toggle-filters[data-toggle="dropdown"]', function( event ) {
			event.preventDefault();

			$( $( this ).attr( 'href' ) ).fadeToggle( function() {
				$( this ).toggleClass( 'open' );
			} );
		} ).on( 'click', '.products-filter.dropdown-panel .button-close', function( event ) {
			event.preventDefault();

			$( this ).closest( '.products-filter' ).fadeOut( function() {
				$( this ).removeClass( 'open' );
			} );
		} );

		// Auto close filter when runing ajax.
		$( document.body ).on( 'konte_products_filter_before_send_request konte_products_filter_reseted', function() {
			if ( $filter.hasClass( 'offscreen-panel' ) ) {
				konte.closeOffCanvas( $filter );
			} else {
				$filter.fadeOut( function() {
					$filter.removeClass( 'open' );
				} );
			}
		} );

		// Remove filtered field;
		$( '.products-tools .products-filter-toggle' ).on( 'click', 'a.remove-filtered', function( event ) {
			event.preventDefault();

			var $el = $( this );

			$( '.products-filter__activated a.remove-filtered[data-name="' + $el.data( 'name' ) + '"][data-value="' + $el.data( 'value' ) + '"]', '#products-filter' ).trigger( 'click' );
			$el.remove();
		} );

		// Update filtered fields.
		if ( $filter.hasClass( 'dropdown-panel' ) ) {
			displayFilteredField( null, null );
			$( document.body ).on( 'konte_products_filter_widget_updated', displayFilteredField );
		}

		/**
		 * Display filtered fields.
		 */
		function displayFilteredField( event, form ) {
			var $widget = form ? $( form ).closest( '.products-filter-widget' ) : $( '.products-filter-widget', '#products-filter' );

			if ( ! $widget.length ) {
				return;
			}

			var $toggle = $( '.products-tools .products-filter-toggle' );

			$toggle.find( '.remove-filtered' ).remove();
			$widget.find( '.products-filter__activated a.remove-filtered' ).clone().appendTo( $toggle );
		}

		/**
		 * Set the correct position of dropdown filter.
		 */
		function setDropdownFilterPosition() {
			var $sidebar = $( '#secondary' ),
				windowWidth = $window.width();

			if ( windowWidth >= 992 ) {
				windowWidth -= konte.getVerticalHeaderWidth();
			}

			$filter.width( windowWidth );

			// Check if there is a sidebar
			var prop = 'margin-left';
			var value = -windowWidth / 2;

			if ( konte.data.rtl ) {
				prop = 'margin-right';
			}

			if ( windowWidth >= 992 ) {
				if ( $sidebar.length ) {
					if ( $( document.body ).hasClass( 'sidebar-left' ) ) {
						value = konte.data.rtl ? value + ($sidebar.outerWidth() / 2) : value - ($sidebar.outerWidth() / 2);
					} else {
						value = konte.data.rtl ? value - ($sidebar.outerWidth() / 2) : value + ($sidebar.outerWidth() / 2);
					}

					$filter.css( prop, value );
				} else {
					$filter.css( prop, value );
				}
			} else {
				$filter.css( prop, value );
			}
		}
	};

	/**
	 * Make the cart widget more flexible.
	 */
	konte.cartWidget = function() {
		if ( typeof woocommerce_params === 'undefined' ) {
			$( '.woocommerce-mini-cart-item .quantity .qty' ).prop( 'disabled', true );

			$( document.body ).on( 'wc_fragments_refreshed removed_from_cart', function() {
				$( '.woocommerce-mini-cart-item .quantity .qty' ).prop( 'disabled', true );
			} );

			return;
		}

		var options = {
			onChange: function( data ) {
				var $row = data.$quantity.closest( '.woocommerce-mini-cart-item' ),
					key = $row.find( 'a.remove' ).data( 'cart_item_key' ),
					nonce = $row.find( '.woocommerce-mini-cart-item__qty' ).data( 'nonce' ),
					ajax_url = woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'update_cart_item' );

				if ( $.fn.block ) {
					$row.block( {
						message   : null,
						overlayCSS: {
							opacity   : 0.6,
							background: '#fff'
						}
					} );
				}

				$.post(
					ajax_url, {
						cart_item_key: key,
						qty: data.current,
						security: nonce
					}, function( response ) {
						if ( !response || !response.fragments ) {
							return;
						}

						if ( $.fn.unblock ) {
							$row.unblock();
						}

						$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash] );

						// Trigger the fragment refresh event manually if necessary.
						if ( 'undefined' === typeof wc_add_to_cart_params ) {
							$( document.body ).trigger( 'wc_fragment_refresh' );
						}
					}, 'json' ).fail( function() {
						if ( $.fn.unblock ) {
							$row.unblock();
						}

						return;
					} );
			}
		};

		if ( $.fn.quantityDropdown ) {
			$( '.woocommerce-mini-cart-item .quantity .qty' ).quantityDropdown( options );

			$( document.body ).on( 'wc_fragments_refreshed removed_from_cart added_to_cart', function() {
				$( '.woocommerce-mini-cart-item .quantity .qty' ).quantityDropdown( options );
			} );
		}
	};

	/**
	 * Display a notification
	 */
	konte.shopNotifications = function() {
		if ( ! $.fn.notify ) {
			return;
		}

		$.notify.addStyle( 'konte', {
			html: '<div><span class="svg-icon icon-success size-normal message-icon"><svg role="img"><use href="#success" xlink:href="#success"></use></svg></span><span data-notify-text/></div>'
		});

		if ( konte.data.added_to_cart_notice ) {
			$( document.body ).on( 'added_to_cart', function() {
				$.notify( konte.data.added_to_cart_message, {
					position: konte.data.rtl ? 'top left' : 'top right',
					autoHideDelay: 2000,
					className: 'success',
					style: 'konte',
					showAnimation: 'fadeIn',
					hideAnimation: 'fadeOut'
				} );
			} );
		}

		if ( konte.data.added_to_wishlist_notice ) {
			$( document.body ).on( 'added_to_wishlist', function() {
				$.notify( konte.data.added_to_wishlist_message, {
					autoHideDelay: 2000,
					className: 'success',
					style: 'konte',
					showAnimation: 'fadeIn',
					hideAnimation: 'fadeOut'
				} );
			} );
		}
	};

	/**
	 * Scripts for page template "split.php"
	 */
	konte.pageTemplateSplit = function() {
		if ( !$( document.body ).hasClass( 'page-template-split' ) ) {
			return;
		}

		var $window = $( window ),
			$topbar = $( '#topbar' ),
			ps = null;

		// Scrollbar.
		if ( $window.width() >= 992 ) {
			ps = new PerfectScrollbar( $( '.split-page-content .entry-content' ).get( 0 ) );
		}

		// Full height.
		$window.on( 'resize', function() {
			$( '.split-page.page' ).height( fullHeight() );

			if ( $window.width() < 992 ) {
				if ( ps ) {
					ps.destroy();
				}

				ps = null;
			} else {
				ps = new PerfectScrollbar( $( '.split-page-content .entry-content' ).get( 0 ) );
			}
		} );

		$( '.split-page.page' ).height( fullHeight() );

		/**
		 * Get the windown height.
		 */
		function fullHeight() {
			var height = $window.height();

			if ( $topbar.length ) {
				height -= $topbar.outerHeight();
			}

			return height;
		}
	};

	/**
	 * Youtube video background.
	 */
	konte.videoBackground = function() {
		var $player = $( '.video-background' );

		if ( !$player.length ) {
			return;
		}

		var player = null,
			mute = $player.data( 'mute' ),
			$screen = $player.parent();

		// Load Youtube API
		if ( typeof YT === undefined ) {
			var tag = document.createElement( 'script' ),
				head = document.getElementsByTagName( 'head' )[0];

			tag.src = 'https://www.youtube.com/iframe_api';

			head.appendChild( tag );
		}

		window.onYouTubePlayerAPIReady = function() {
			player = new YT.Player( $player.attr( 'id' ), {
				videoId   : $player.data( 'video_id' ),
				events    : {
					'onReady'      : function( event ) {
						scaleVideo();
						$screen.find( '.video-background' ).css( 'opacity', 1 );

						if ( mute ) {
							event.target.mute();
						}
						event.target.playVideo();
					},
					'onStateChange': function( event ) {
						if ( event.data === YT.PlayerState.ENDED ) {
							event.target.playVideo();
						}
					}
				},
				playerVars: {
					autoplay      : 0,
					loop          : 1,
					autohide      : 1,
					modestbranding: 0,
					rel           : 0,
					showinfo      : 0,
					controls      : 0,
					disablekb     : 1,
					enablejsapi   : 0,
					iv_load_policy: 3
				}
			} );
		}

		$( window ).on( 'resize', scaleVideo );

		/**
		 * Scale video.
		 */
		function scaleVideo() {
			if ( ! player ) {
				return;
			}

			var width = $screen.width(),
				pWidth,
				height = $screen.height(),
				pHeight,
				ratio = 16 / 9,
				$iframe = $screen.find( '.video-background' );

			if ( width / ratio < height ) {
				pWidth = Math.ceil( height * ratio );
				player.setSize( pWidth, height );
				$iframe.css( {
					left: (width - pWidth) / 2,
					top : 0
				} );
			} else {
				pHeight = Math.ceil( width / ratio );
				player.setSize( width, pHeight );
				$iframe.css( {
					left: 0,
					top : (height - pHeight) / 2
				} );
			}
		}
	};

	/**
	 * Init masonry layout for template Flex Posts.
	 */
	konte.pageTemplateFlexPosts = function() {
		var $posts = $( '#flex-posts' ),
			$window = $( window );

		if ( !$posts.length || !$.fn.masonry ) {
			return;
		}

		// Init masonry layout
		var options = {
			itemSelector:       '.flex_post',
			percentPosition:    true,
			transitionDuration: 0,
			isRTL:              !! konte.data.rtl
		};

		// Add class left-item and right-item
		$posts.on( 'layoutComplete', function( event ) {
			var $items = $posts.children();

			if ( konte.data.rtl ) {
				$items.filter( function() {
					return parseInt( $( this ).get( 0 ).style.right ) === 0;
				} ).removeClass( 'left-item' ).addClass( 'right-item' );

				$items.filter( function() {
					return $( this ).get( 0 ).style.right === '50%';
				} ).removeClass( 'right-item' ).addClass( 'left-item' );
			} else {
				$items.filter( function() {
					return parseInt( $( this ).get( 0 ).style.left ) === 0;
				} ).removeClass( 'right-item' ).addClass( 'left-item' );

				$items.filter( function() {
					return $( this ).get( 0 ).style.left === '50%';
				} ).removeClass( 'left-item' ).addClass( 'right-item' );
			}
		} );

		initFlexPostsMasonry();

		$window.on( 'resize', initFlexPostsMasonry );

		// Support Jetpack lazy loads.
		var layoutHandle;

		$posts.on( 'jetpack-lazy-loaded-image', 'img', function() {
			if ( ! $posts.hasClass( 'masonry' ) ) {
				initFlexPostsMasonry();
			}

			clearTimeout( layoutHandle );

			layoutHandle = setTimeout( function() {
				$posts.masonry( 'layout' );
			}, 100 );
		} );

		// Ajax load more posts
		$( 'body.page-template-flex-posts' ).on( 'click', '.navigation.next-posts-navigation a', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				url = $el.attr( 'href' );

			if ( $el.hasClass( 'loading' ) ) {
				return;
			}

			$el.addClass( 'loading' );

			$.get( url, function( response ) {
				var $content = $( '#main', response ),
					$_posts = $( '.flex_post', $content ),
					$nav = $( '.next-posts-navigation', $content );

				$_posts.addClass( 'animated' ).appendTo( $posts );
				;
				$posts.masonry( 'appended', $_posts ).masonry();

				$_posts.imagesLoaded().progress( function() {
					$posts.masonry( 'layout' );
				} );

				if ( $nav.length ) {
					$el.replaceWith( $( 'a', $nav ) );
				} else {
					$el.removeClass( 'loading' ).closest( '.next-posts-navigation' ).fadeOut();
				}

				window.history.pushState( null, '', url );

				if ( typeof trigger !== undefined ) {
					$_posts.attr( 'data-scroll', '' );
					trigger.bind( $_posts.get() );
				}

				$( document.body ).trigger( 'konte_flex_posts_loaded', [$_posts] );
			} );
		} );

		if ( typeof ScrollTrigger !== undefined ) {
			$posts.children().attr( 'data-scroll', '' ).addClass( 'animated' );

			var trigger = new ScrollTrigger( {
				toggle: {
					visible: 'konteFadeInUp',
					hidden : ''
				},
				offset: {
					x: 0,
					y: -20
				},
				once  : true
			}, document.body, window );
		}

		/**
		 * Init flex posts masonry
		 */
		function initFlexPostsMasonry() {
			if ( $window.width() >= 992 ) {
				if ( $posts.hasClass( 'masonry' ) ) {
					return;
				}

				$posts.masonry( options );

				// Layout Masonry after each image loads.
				$posts.imagesLoaded().progress( function() {
					$posts.masonry( 'layout' );
				} );
			} else {
				if ( ! $posts.hasClass( 'masonry' ) ) {
					return;
				}

				$posts.masonry( 'destroy' );
			}
		}
	};

	/**
	 * Scroll down arrow sticky.
	 * This arrow appears on flex posts template and portfolio page.
	 */
	konte.stickyScrollDown = function() {
		var $window = $( window ),
			$arrow = $( '.sticky-scrolldown' ),
			waiting = false,
			endScrollHandle,
			offset = $( '#masthead' ).height();

		if ( !$arrow.length ) {
			return;
		}

		$arrow.on( 'click', function() {
			$( 'html, body' ).animate( {scrollTop: $window.scrollTop() + $window.height()}, 800 );
		} );

		toggleArrow();

		$window.on( 'scroll', function() {
			if ( waiting ) {
				return;
			}

			if ( $window.width() < 1440 ) {
				return;
			}

			waiting = true;

			// clear previous scheduled endScrollHandle
			clearTimeout( endScrollHandle );

			toggleArrow();

			setTimeout( function() {
				waiting = false;
			}, 100 );

			// schedule an extra execution of toggleArrow() after 200ms
			// in case the scrolling stops in next 100ms.
			endScrollHandle = setTimeout( function() {
				waiting = false;
				toggleArrow();
			}, 200 );
		} );

		/**
		 * Toggle social icons on footer.
		 */
		function toggleArrow() {
			if ( $window.scrollTop() >= offset ) {
				$arrow.fadeOut();
			} else {
				$arrow.fadeIn();
			}
		}
	};

	/**
	 * Sticky social icon on page template Flex Posts.
	 */
	konte.stickySocials = function() {
		var $footer = $( '#colophon' ),
			$socials = $( '#sticky-socials' ),
			$window = $( window ),
			waiting = false,
			endScrollHandle;

		if ( !$socials.length ) {
			return;
		}

		toggleSocials();

		$window.on( 'scroll', function() {
			if ( waiting ) {
				return;
			}

			if ( $window.width() < 1440 ) {
				return;
			}

			waiting = true;

			// clear previous scheduled endScrollHandle
			clearTimeout( endScrollHandle );

			toggleSocials();

			setTimeout( function() {
				waiting = false;
			}, 100 );

			// schedule an extra execution of toggleSocials() after 200ms
			// in case the scrolling stops in next 100ms.
			endScrollHandle = setTimeout( function() {
				waiting = false;
				toggleSocials();
			}, 200 );
		} );

		/**
		 * Toggle social icons on footer.
		 */
		function toggleSocials() {
			if ( $window.scrollTop() + $window.height() >= $footer.offset().top - 200 ) {
				$socials.fadeOut();
			} else {
				$socials.fadeIn();
			}
		}
	};

	/**
	 * Open share links in a new popup window
	 */
	konte.openShareLinks = function() {
		var $window = $( window );

		$( document.body ).on( 'click', '.social-share-link', function( event ) {
			if ( $window.width() <= 1024 ) {
				return;
			}

			event.preventDefault();

			popupShare( $( this ).attr( 'href' ), '', 500, 550 );
		} );

		/**
		 * Open new window at middle of screen
		 */
		function popupShare( url, title, w, h ) {
			// Fixes dual-screen position
			var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX,
				dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY,

				width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
				height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height,

				systemZoom = width / window.screen.availWidth,
				left = (width - w) / 2 / systemZoom + dualScreenLeft,
				top = (height - h) / 2 / systemZoom + dualScreenTop;

			var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w / systemZoom + ', height=' + h / systemZoom + ', top=' + top + ', left=' + left);

			// Puts focus on the newWindow
			if ( window.focus ) {
				newWindow.focus();
			}
		}
	};

	/**
	 * Fix full width row of WPB with vertical header.
	 */
	konte.fixVCRowsWithVerticalHeader = function() {
		if ( !$( document.body ).hasClass( 'header-vertical' ) ) {
			return;
		}

		$( document ).on( 'vc-full-width-row-single', function( event, data ) {
			var width = konte.getVerticalHeaderWidth();

			data.el.css( {
				width: data.width - width,
				left : data.offset + width
			} );

			if ( konte.data.rtl ) {
				data.el.css( {
					right: data.offset + width
				} );
			} else {
				data.el.css( {
					left: data.offset + width
				} );
			}
		} );
	};

	/**
	 * Get the width of vertical header.
	 */
	konte.getVerticalHeaderWidth = function() {
		var width = 0;

		if ( $( document.body ).hasClass( 'header-vertical' ) ) {
			var $header = $( '#masthead' );

			if ( $header.hasClass( 'header-v10' ) ) {
				$header = $header.find( '.header-main .header-left-items' );
			}

			if ( $header.is( ':visible' ) ) {
				width = $header.outerWidth();
			}

			return width;
		}

		return width;
	};

	/**
	 * Responsive video
	 */
	konte.responsiveVideos = function() {
		if ( !$.fn.fitVids ) {
			return;
		}

		$( '.entry-content' ).fitVids();
	};

	/**
	 * Open popup.
	 */
	konte.popup = function() {
		if ( ! konte.data.popup ) {
			return;
		}

		var days = parseInt( konte.data.popup_frequency ),
			delay = parseInt( konte.data.popup_visible_delay );

		if ( days > 0 && document.cookie.match( /^(.*;)?\s*konte_popup\s*=\s*[^;]+(.*)?$/ ) ) {
			return;
		}

		delay = Math.max( delay, 0 );
		delay = 'delay' === konte.data.popup_visible ? delay : 0;

		$( window ).on( 'load', function() {
			setTimeout( function() {
				konte.openModal( '#popup-modal' );
			}, delay * 1000 );
		} );

		$( document.body ).on( 'konte_modal_closed', function( event, modal ) {
			if ( !$( modal ).closest( '.modal' ).hasClass( 'popup-modal' ) ) {
				return;
			}

			var date = new Date(),
				value = date.getTime();

			date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );

			document.cookie = 'konte_popup=' + value + ';expires=' + date.toGMTString() + ';path=/';
		} );

		// Support close popup with a helper class.
		$( document.body ).on( 'click', '.popup-modal .close-popup-trigger', function() {
			konte.closeModal( '.popup-modal' );
		} );
	};

	/**
	 * Preloader.
	 */
	konte.preloader = function() {
		var $preloader = $( '#preloader' );

		if ( ! $preloader.length ) {
			return;
		}

		var ignorePreloader = false;

		$( document.body ).on( 'click', 'a[href^=mailto], a[href^=tel]', function() {
			ignorePreloader = true;
		} );

		$( window ).on( 'beforeunload', function( event ) {
			if ( ! ignorePreloader ) {
				$preloader.fadeIn();
			}

			ignorePreloader = false;
		} );

		setTimeout( function() {
			$preloader.fadeOut();
		}, 200 );

		window.onpageshow = function( event ) {
			if ( event.persisted ) {
				setTimeout( function() {
					$preloader.fadeOut();
				}, 200 );
			}
		};
	};

	/**
	 * Handle mobile menu toggle.
	 */
	konte.mobileMenu = function() {
		var $mobileMenu = $( '#mobile-menu' );

		$mobileMenu.find( '' )

		// Add class 'open' to current menu item.
		$mobileMenu.find( '.menu > .menu-item-has-children, .menu > li > ul > .menu-item-has-children' ).filter( function() {
			return $( this ).hasClass( 'current-menu-item' ) || $( this ).hasClass( 'current-menu-ancestor' );
		} ).addClass( 'open' );

		// Toggle submenu.
		$mobileMenu.on( 'click', '.menu-item-has-children > a', function( event ) {
			var $li = $( this ).parent();

			if ( $li.hasClass( 'open' ) && $li.hasClass( 'clicked' ) && '#' !== $( this ).attr( 'href' ) ) {
				return true;
			}

			event.stopPropagation();
			event.preventDefault();

			$li.addClass( 'clicked' );

			$li.toggleClass( 'open' ).children( 'ul' ).slideToggle();
			$li.siblings( '.open' ).removeClass( 'open clicked' ).children( 'ul' ).slideUp();
		} ).on( 'click', '.menu-item-has-children > .toggle', function( event ) {
			event.stopPropagation();
			event.preventDefault();

			var $li = $( this ).parent();

			$li.toggleClass( 'open' ).children( 'ul' ).slideToggle();
			$li.siblings( '.open' ).removeClass( 'open' ).children( 'ul' ).slideUp();
		} );

		// Close other panels.
		$mobileMenu.on( 'click', '[data-toggle="off-canvas"], [data-toggle="modal"]', function() {
			if ( 'mobile-menu' !== $( this ).data( 'target' ) ) {
				konte.closeModal();
				konte.closeOffCanvas();
			}
		} );
	};

	/**
	 * Responsive for single product v3.
	 */
	konte.responsiveProductV3 = function() {
		var $window = $( window ),
			$product = $( '.woocommerce div.product.layout-v3' );

		if ( !$product.length ) {
			return;
		}

		updateLayout();

		$window.on( 'resize', updateLayout );

		// Change tabs position
		function updateLayout() {
			if ( $window.width() < 1200 ) {
				$( '.woocommerce-product-cart form.cart', $product ).insertBefore( $( '.summary > .product_meta', $product ) );
			} else {
				$( '.summary > form.cart', $product ).appendTo( $( '.woocommerce-product-cart', $product ) );
			}
		}
	}

	/**
	 * Portfolio mansonry
	 */
	konte.portfolioMasonry = function() {
		var $portfolio = $( '.portfolio-projects--masonry' );

		if ( ! $portfolio.length || ! $.fn.masonry ) {
			return;
		}

		// Init masonry layout
		var $window = $( window ),
			options = {
				itemSelector:       '.portfolio',
				percentPosition:    true,
				transitionDuration: 0,
				isRTL:              !! konte.data.rtl
			};

		initPortfolioMasonry();

		$window.on( 'resize', initPortfolioMasonry );

		// Layout items when using ajax load.
		$( document.body ).on( 'konte_posts_loaded', function( event, $posts, append ) {
			if ( $window.width() < 767 ) {
				return;
			}

			if ( append ) {
				$portfolio.masonry( 'appended', $posts );
			} else {
				$portfolio.masonry( 'reloadItems' );
			}

			setTimeout( function() {
				$portfolio.masonry( 'layout' );
			}, $posts.length * 50 + 50 );

			$posts.imagesLoaded().progress( function() {
				$portfolio.masonry( 'layout' );
			} );
		} );

		// Support Jetpack lazy loads.
		var layoutHandle;

		$portfolio.on( 'jetpack-lazy-loaded-image', 'img', function() {
			if ( ! $portfolio.hasClass( 'masonry' ) ) {
				initPortfolioMasonry();
			}

			clearTimeout( layoutHandle );

			layoutHandle = setTimeout( function() {
				$portfolio.masonry( 'layout' );
			}, 100 );
		} );

		/**
		 * Init portfolio masonry
		 */
		function initPortfolioMasonry() {
			if ( $window.width() >= 767 ) {
				if ( $portfolio.hasClass( 'masonry' ) ) {
					$portfolio.masonry( 'layout' );
				}

				$portfolio.masonry( options );

				// Layout Masonry after each image loads.
				$portfolio.imagesLoaded().progress( function() {
					$portfolio.masonry( 'layout' );
				} );
			} else {
				if ( ! $portfolio.hasClass( 'masonry' ) ) {
					return;
				}

				$portfolio.masonry( 'destroy' );
			}
		}
	};

	/**
	 * Ajax filter portfolio
	 */
	konte.portfolioFilter = function() {
		var $filter = $( '.portfolio-filter' ),
			$portfolio = $( '.portfolio-projects' ),
			$nav = $portfolio.siblings( '.navigation' );

		if ( ! $filter.length || ! $portfolio.length ) {
			return;
		}

		$filter.on( 'click', 'a', function( event ) {
			event.preventDefault();

			var $link = $( this ),
				$active = $link.siblings( '.active' ),
				url = $link.attr( 'href' );

			if ( $link.hasClass( 'active' ) ) {
				return;
			}

			$link.addClass( 'active' );
			$active.removeClass( 'active' );

			$portfolio.fadeIn().addClass( 'loading' ).append( '<div class="loading-screen"><span class="spinner"></span></div>' );

			$.get( url, function( response ) {
				var $_portfolio = $( '.portfolio-projects', response ),
					$_projects = $( '.hentry', $_portfolio ),
					$_nav = $_portfolio.siblings( '.navigation' );

				$portfolio.html( '' ).removeClass( 'loading' );

				// If there is no project.
				if ( ! $_portfolio.length ) {
					$portfolio.fadeOut();
					$nav.fadeOut();

					return;
				}

				$_projects.each( function( index, project ) {
					$( project ).css( 'animation-delay', index * 100 + 'ms' );
				} );

				$portfolio.append( $_projects );

				$_projects.addClass( 'animated konteFadeInUp' );

				if ( $_nav.length ) {
					if ( $nav.length ) {
						$nav.html( $_nav.html() ).fadeIn();
					} else {
						$portfolio.after( $_nav );
						$nav = $_nav;
					}
				} else {
					$nav.fadeOut();
				}

				window.history.pushState( null, '', url );
				$( document.body ).trigger( 'konte_posts_loaded', [$_projects, false] );
			} ).fail( function() {
				$link.removeClass( 'active' );
				$active.addClass( 'active' );

				$portfolio.children( '.loading-screen' ).remove();
				$portfolio.removeClass( 'loading' );
			} );
		} );
	};

	/**
	 * Back to top icon
	 */
	konte.backToTop = function () {
		var $button = $( '#gotop' ),
			$window = $( window ),
			waiting = false,
			endScrollHandle;

		$button.on( 'click', function ( e ) {
			e.preventDefault();

			$( 'html, body' ).animate( {scrollTop: 0}, 800 );
		} );

		if ( $button.length ) {
			toggleButton();

			$window.on( 'scroll', function() {
				if ( waiting ) {
					return;
				}

				waiting = true;

				// clear previous scheduled endScrollHandle
				clearTimeout( endScrollHandle );

				toggleButton();

				setTimeout( function() {
					waiting = false;
				}, 100 );

				// schedule an extra execution of toggleArrow() after 200ms
				// in case the scrolling stops in next 100ms.
				endScrollHandle = setTimeout( function() {
					waiting = false;
					toggleButton();
				}, 200 );
			} );
		}

		/**
		 * Toggle the button when scrolling
		 */
		function toggleButton() {
			if ( $window.scrollTop() < $window.height() * 1.5 ) {
				$button.fadeOut();
			} else {
				$button.fadeIn();
			}
		}
	};

	/**
	 * Support lazy load events
	 */
	konte.lazyLoadImages = function() {
		var lazyLoadImagesEvent;

		$( document.body ).on( 'post-load konte_posts_loaded konte_products_loaded konte_products_quick_search_request_success konte_flex_posts_loaded konte_product_quickview_loaded konte_lazy_load_images', function() {
			try {
				lazyLoadImagesEvent = new Event( 'jetpack-lazy-images-load', {
					bubbles: true,
					cancelable: true
				} );
			} catch ( e ) {
				lazyLoadImagesEvent = document.createEvent( 'Event' )
				lazyLoadImagesEvent.initEvent( 'jetpack-lazy-images-load', true, true );
			}

			setTimeout( function() {
				document.body.dispatchEvent( lazyLoadImagesEvent );
			}, 100 );
		} );
	};

	/**
	 * Update the height of product gallery with Jetpack lazy loads.
	 */
	konte.supportJetpackLazyLoadImagesOnProductGallery = function() {
		$( 'div.product' ).each( function() {
			var $product = $( this );

			if ( $product.hasClass( 'layout-v2' ) || $product.hasClass( 'layout-v5' ) ) {
				return;
			}

			// Trigger resize event after main image loads to correct the gallery size.
			$( '.woocommerce-product-gallery', $product ).one( 'jetpack-lazy-loaded-image load', '.wp-post-image', function() {
				var $image = $( this );

				if ( $image ) {
					setTimeout( function() {
						var setHeight = $image.closest( '.woocommerce-product-gallery__image' ).height();
						var $viewport = $image.closest( '.flex-viewport' );

						if ( ! $viewport.length ) {
							$viewport = $image.closest( '.woocommerce-product-gallery' );
						}

						if ( setHeight && $viewport.length ) {
							$viewport.height( setHeight );
						}
					}, 100 );
				}
			} );
		} );

	}

	/**
	 * Support mega menu tabs.
	 */
	konte.mainMenuTabs = function() {
		$( '.main-navigation' ).on( 'click', '.menu-item--tab, .menu-item--tab a', function( event ) {
			event.preventDefault();
			event.stopPropagation();

			var $tab = $( this ).closest( '.menu-item--tab' );

			if ( $tab.hasClass( 'active' ) ) {
				return;
			}

			$tab.addClass( 'active' ).attr( 'aria-selected', 'true' ).siblings().removeClass( 'active' ).attr( 'aria-selected', 'false' );

			$tab.closest( '.menu-item-mega' ).find( '#' + $tab.attr( 'aria-controls' ) ).addClass( 'active' ).siblings().removeClass( 'active' );
		} );
	}

	/**
	 * Fire when document ready
	 */
	$( function() {
		konte.init();
	} );
})( jQuery );
