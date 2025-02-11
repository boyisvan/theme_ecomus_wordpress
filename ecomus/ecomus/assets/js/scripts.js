(function ($) {
	'use strict';

	var ecomus = ecomus || {};

	ecomus.init = function () {
		ecomus.$body   = $(document.body),
		ecomus.$window = $(window),
		ecomus.$header = $('#site-header');

		this.toggleOffCanvas();
		this.toggleModals();
		this.togglePopover();
		this.sidebarPanel();
		this.toggleSearchModal();
		this.formFieldFocus();

		// Login Popup
		this.loginPopup();
		this.loginModalAuthenticate();

		// Topbar
		this.topbarSlides();

		//Header
		this.hamburgerToggleMenuItem();
		this.hamburgerToggleSubMenuItem();
		this.currencyLanguage();
		this.headerCampaignBar();
		this.stickyHeader();

		// Footer
		this.footerCollapsible();

		// Blog Page
		this.postsRelated();

		// Footer

		// Product Card
		this.productCardHoverSlider();
		this.productCardHoverZoom();
		this.productAttribute();
		this.productQuantityNumber();
		this.productQuickView();
		this.productQuickAdd();

		// add to cart AJAX
		this.addToCartLoopAjax();

		// Filter
		this.productFilterAjax();

		// Cart
		this.openMiniCartPanel();

		this.changeQuantitySingleProduct();
		this.productVariation();

		// Mini Cart
		this.updateQuantityAuto();
		this.productsRecommendedCarousel();
		ecomus.$body.on('added_to_cart removed_from_cart wc_fragments_refreshed', function () {
			ecomus.productsRecommendedCarousel();
		});

		// Product Notification
		this.addedToWishlistNotice();
		this.addedToCompareNotice();

		this.copyLink();

		// Back to top
		this.backToTop();

		this.toggleProductCategoriesWidget();
		this.dropdownProductCategoriesSidebar();

		// Tooltip
		this.tooltip();
	};

	/**
	 * Toggle off-screen panels
	 */
	ecomus.toggleOffCanvas = function() {
		$( document.body ).on( 'click', '[data-toggle="off-canvas"]', function( event ) {
			var target = '#' + $( this ).data( 'target' );

			if ( $( target ).hasClass( 'offscreen-panel--open' ) ) {
				ecomus.closeOffCanvas( target );
			} else if ( ecomus.openOffCanvas( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.offscreen-panel .panel__button-close, .offscreen-panel .panel__backdrop, .offscreen-panel .sidebar__button-close, .offscreen-panel .sidebar__backdrop', function( event ) {
			event.preventDefault();

			ecomus.closeOffCanvas( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				ecomus.closeOffCanvas();
			}
		} );
	};

	/**
	 * Open off canvas panel.
	 * @param string target Target selector.
	 */
	ecomus.openOffCanvas = function( target ) {
		var $target = $( target );

		if ( !$target.length ) {
			return false;
		}

		var widthScrollBar = window.innerWidth - $('#page').width();
		if( $('#page').width() < 767 ) {
			widthScrollBar = 0;
		}
		$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});

		$target.fadeIn();
		$target.addClass( 'offscreen-panel--open' );

		$( document.body ).addClass( 'offcanvas-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'ecomus_off_canvas_opened', [$target] );

		return true;
	}

	/**
	 * Close off canvas panel.
	 * @param DOM target
	 */
	ecomus.closeOffCanvas = function( target ) {
		if ( !target ) {
			$( '.offscreen-panel' ).each( function() {
				var $panel = $( this );

				if ( ! $panel.hasClass( 'offscreen-panel--open' ) ) {
					return;
				}

				$panel.removeClass( 'offscreen-panel--open' ).fadeOut();
				$( document.body ).removeClass( $panel.attr( 'id' ) + '-opened' );

				if( $panel.hasClass( 'modal-above-panel' ) ) {
					$panel.removeClass( 'modal-above-panel' );
				}
			} );
		} else {
			target = $( target ).closest( '.offscreen-panel' );
			target.removeClass( 'offscreen-panel--open' ).fadeOut();

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );

			if( target.hasClass( 'modal-above-panel' ) ) {
				target.removeClass( 'modal-above-panel' );
			}
		}

		$(document.body).removeAttr('style');

		$( document.body ).removeClass( 'offcanvas-opened' ).trigger( 'ecomus_off_canvas_closed', [target] );
	}

	/**
	 * Toggle modals.
	 */
	ecomus.toggleModals = function() {
		$( document.body ).on( 'click', '[data-toggle="modal"]', function( event ) {
			if( $( this ).data( 'modal' ) == 'no' ) {
				return;
			}

			var target = '#' + $( this ).data( 'target' );

			if ( $( target ).hasClass( 'modal--open' ) ) {
				ecomus.closeModal( target );
			} else if ( ecomus.openModal( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.modal .modal__button-close, .modal .modal__backdrop', function( event ) {
			event.preventDefault();

			ecomus.closeModal( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				ecomus.closeModal();
			}
		} );
	};

	/**
	 * Open a modal.
	 *
	 * @param string target
	 */
	ecomus.openModal = function( target ) {
		var $target = $( target );

		$target = $target.length ? $target : $('.modal[data-id="' + target + '"]' );
		if ( !$target.length ) {
			var target = target.replace( '#', '');
			$target = $('.modal[data-id="' + target + '"]' );
		}

		if ( !$target.length ) {
			return false;
		}

		var widthScrollBar = window.innerWidth - $('#page').width();
		if( $('#page').width() < 767 ) {
			widthScrollBar = 0;
		}
		$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});

		$target.fadeIn();
		$target.addClass( 'modal--open' );

		$( document.body ).addClass( 'modal-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'ecomus_modal_opened', [$target] );

		return true;
	}

	/**
	 * Close a modal.
	 *
	 * @param string target
	 */
	ecomus.closeModal = function( target ) {
		if ( !target ) {
			$( '.modal' ).removeClass( 'modal--open' ).fadeOut();

			$( '.modal' ).each( function() {
				var $modal = $( this );

				if ( ! $modal.hasClass( 'modal--open' ) ) {
					return;
				}

				$modal.removeClass( 'modal--open' ).fadeOut();
				$( document.body ).removeClass( $modal.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = $( target ).closest( '.modal' );
			target.removeClass( 'modal--open' ).fadeOut();

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );
		}

		$(document.body).removeAttr('style');

		$( document.body ).removeClass( 'modal-opened' ).trigger( 'ecomus_modal_closed', [target] );
	}

	/**
	 * Toggle modals.
	 */
	ecomus.togglePopover = function() {
		$( document.body ).on( 'click', '[data-toggle="popover"]', function( event ) {
			var target = '#' + $( this ).data( 'target' );

			if( $( this ).data( 'device' ) == 'mobile' ) {
				if( ecomus.$window.width() > 767 ) {
					return;
				}
			}

			if ( $( target ).hasClass( 'popover--open' ) ) {
				ecomus.closePopover( target );
			} else if ( ecomus.openPopover( target ) ) {
				event.preventDefault();
			}
		} ).on( 'click', '.popover .popover__button-close, .popover .popover__backdrop', function( event ) {
			event.preventDefault();

			ecomus.closePopover( this );
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				ecomus.closePopover();
			}
		} );
	};

	/**
	 * Open a popover.
	 *
	 * @param string target
	 */
	ecomus.openPopover = function( target ) {
		var $target = $( target );

		$target = $target.length ? $target : $('.popover[data-id="' + target + '"]' );
		if ( !$target.length ) {
			var target = target.replace( '#', '');
			$target = $('.popover[data-id="' + target + '"]' );
		}

		if ( !$target.length ) {
			return false;
		}

		var widthScrollBar = window.innerWidth - $('#page').width();
		if( $('#page').width() < 767 ) {
			widthScrollBar = 0;
		}
		$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});

		$target.addClass( 'popover--open' );

		$( document.body ).addClass( 'popover-opened ' + $target.attr( 'id' ) + '-opened' ).trigger( 'ecomus_popover_opened', [$target] );

		return true;
	}

	/**
	 * Close a popover.
	 *
	 * @param string target
	 */
	ecomus.closePopover = function( target ) {
		if ( !target ) {
			$( '.popover' ).removeClass( 'popover--open' ).fadeOut();

			$( '.popover' ).each( function() {
				var $popover = $( this );

				if ( ! $popover.hasClass( 'popover--open' ) ) {
					return;
				}

				$popover.removeClass( 'popover--open' );
				$( document.body ).removeClass( $popover.attr( 'id' ) + '-opened' );
			} );
		} else {
			target = $( target ).closest( '.popover' );
			target.removeClass( 'popover--open' );

			$( document.body ).removeClass( target.attr( 'id' ) + '-opened' );
		}

		$(document.body).removeAttr('style');

		$( document.body ).removeClass( 'popover-opened' ).trigger( 'ecomus_popover_closed', [target] );
	}

	ecomus.toggleSearchModal = function() {
		var $selector = $('#search-modal');

        if ($selector.length < 1 || ! $selector.hasClass('search-type-popup')) {
            return;
        }

		ecomus.$window.on('resize', function () {
            if ( ecomus.$window.width() < 1200 ) {
                $selector.removeClass('search-type-popup').addClass('search-type-sidebar');
				if( $selector.find( '.header-search__products--slider' ).length > 0 && $selector.find( '.header-search__products--slider' ).get(0).swiper ) {
					$selector.find( '.header-search__products--slider' ).get(0).swiper.destroy();
				}
            } else {
                $selector.removeClass('search-type-sidebar').addClass('search-type-popup');
				if( $selector.find( '.header-search__products--slider' ).length > 0 && $selector.find( '.header-search__products--slider' ).get(0).swiper == null ) {
					var $slider = $selector.find( '.header-search__products--slider' );
					new Swiper( $slider.get(0), search_swiper_options( $slider ) );
				} else {
					if( $selector.find( '.header-search__products--slider' ).length < 1 ) {
						search_swiper();
					}
				}
            }

        }).trigger('resize');

		function search_swiper_options( $slider ) {
			var $productThumbnail = $slider.find( '.product-thumbnail' );

			var options = {
				loop: false,
				autoplay: false,
				speed: 800,
				watchSlidesVisibility: true,
				watchOverflow: true,
				navigation: {
					nextEl: $slider.find('.swiper-button-next:not(.ecomus-product-card-swiper-button)').get(0),
					prevEl: $slider.find('.swiper-button-prev:not(.ecomus-product-card-swiper-button)').get(0),
				},
				on: {
					init: function () {
						this.$el.css('opacity', 1);
					},
					resize: function () {
						var self = this;

						if( $productThumbnail.length > 0 ) {
							$productThumbnail.imagesLoaded(function () {
								var	heightThumbnails = $productThumbnail.outerHeight(),
									top = ( heightThumbnails / 2 ) + 'px';

								$(self.navigation.$nextEl).css({'--em-arrow-top': top});
								$(self.navigation.$prevEl).css({'--em-arrow-top': top});
							});
						}
					}
				},
				spaceBetween: $slider.data('spacing'),
				breakpoints: {
					300: {
						slidesPerView: 2,
						slidesPerGroup: 2,
						spaceBetween: 15,
					},
					768: {
						slidesPerView: 3,
						spaceBetween: $slider.data('spacing'),
					},
					1200: {
						slidesPerView: 4,
					},
				}
			};

			return options;
		}

		function search_swiper() {
			if( ecomusData.header_search_products && ecomusData.header_search_product_limit > 4 ) {
				$selector.find( '.header-search__products ul.products' ).wrap( '<div class="header-search__products--slider swiper" data-spacing="30"></div>' );
				$selector.find( '.header-search__products ul.products' ).addClass( 'swiper-wrapper' );
				$selector.find( '.header-search__products ul.products' ).after('<span class="ecomus-svg-icon em-button-light ecomus-swiper-button swiper-button swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 11L0 5.5L5.5 0L6.47625 0.97625L1.9525 5.5L6.47625 10.0238L5.5 11Z" fill="currentColor"/></svg></span>');
				$selector.find( '.header-search__products ul.products' ).after('<span class="ecomus-svg-icon em-button-light ecomus-swiper-button swiper-button swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 11L7 5.5L1.5 0L0.52375 0.97625L5.0475 5.5L0.52375 10.0238L1.5 11Z" fill="currentColor"/></svg></span>');
				$selector.find( '.header-search__products ul.products li.product' ).addClass('swiper-slide');

				var $slider = $selector.find( '.header-search__products--slider' );

				new Swiper($slider.get(0), search_swiper_options( $slider ));
			}
		}
	}

	ecomus.sidebarPanel = function () {
        var $selector = $('.blog #mobile-sidebar-panel'),
			$selectorCatalog = $('.ecomus-catalog-page #mobile-sidebar-panel');

        ecomus.$window.on('resize', function () {
            if (ecomus.$window.width() > 1024) {
                if ( $selector.length > 0 && $selector.hasClass('offscreen-panel') ) {
                    $selector.removeClass('offscreen-panel').removeAttr('style');
                }

                if ( $selectorCatalog.length > 0 && $selectorCatalog.hasClass('offscreen-panel')) {
                    $selectorCatalog.removeClass('offscreen-panel').removeAttr('style');
                }
            } else {
				if( $selector.length > 0 ) {
                	$selector.addClass('offscreen-panel');
				}

				if ( $selectorCatalog.length > 0 ) {
                	$selectorCatalog.addClass('offscreen-panel');
				}
            }

        }).trigger('resize');
    };

	ecomus.postsRelated = function () {
		if( ! $('.ecomus-posts-related').length ) {
			return;
		}

		var $selector = $('.ecomus-posts-related'),
			$element = $selector.find('.swiper'),
			options = {
				loop: false,
				autoplay: false,
				speed: 800,
				watchOverflow: true,
				spaceBetween: $selector.data('spacing'),
				pagination: {
					el: $selector.find('.swiper-pagination').get(0),
					type: 'bullets',
					clickable: true
				},
				breakpoints: {
					0: {
						slidesPerView: 1,
						slidesPerGroup: 1
					},

					767: {
						slidesPerView: 2,
						slidesPerGroup: 2
					},

					992: {
						slidesPerView: 3,
						slidesPerGroup: 3
					},

				}
			};

		$selector.find('.em-post-grid').addClass('swiper-slide');

		new Swiper($element.get(0), options);
	};

	/**
	 * Footer Collapsible
	 *
	 * @param string target
	 */
	ecomus.footerCollapsible = function () {
		$('.footer-widgets-area .wp-block-group__inner-container').each(function(){
			if( $(this).find('.wp-block-heading').length ) {
				$(this).find('.wp-block-heading').addClass('em-widget-heading');
				$(this).find('.wp-block-heading').append('<span class="em-collapse-icon"></span>');
				$(this).find('.wp-block-heading').nextAll().wrapAll('<div class="em-widget-group"/>');
			}
		});

		var $dropdown = $('.footer-widgets-area .wp-block-group__inner-container .em-widget-group'),
			$title = $('.footer-widgets-area .wp-block-group__inner-container .wp-block-heading');

		ecomus.$window.on('resize', function () {
			if (ecomus.$window.width() < 768) {
				$title.addClass('clicked');
				$dropdown.addClass('dropdown');
			} else {
				$title.removeClass('clicked');
				$dropdown.removeClass('dropdown').removeAttr('style');
			}

		}).trigger('resize');

		$('.footer-widgets-area .wp-block-group__inner-container .wp-block-heading').on('click', function (e) {
			e.preventDefault();
			if (! $(this).next().hasClass('dropdown')) {
				return;
			}
			$(this).next('.dropdown').stop().slideToggle();
			$(this).toggleClass('active');
			return false;
		});
	}

	/**
	 * Product thumbnail slider
	 */
	ecomus.productCardHoverSlider = function () {
		var $selector = ecomus.$body.find('ul.products li.product .product-thumbnails--slider'),
			options = {
				observer: true,
				observeParents: true,
				loop: false,
				autoplay: false,
				speed: 800,
				watchOverflow: true,
				lazy: true,
				breakpoints: {}
			};

		$selector.find('.woocommerce-loop-product__link').addClass('swiper-slide');

		setTimeout(function () {
			$selector.each(function () {
				options.navigation = {
					nextEl: $(this).find('.swiper-button-next').get(0),
					prevEl: $(this).find('.swiper-button-prev').get(0),
				}

				new Swiper($(this).get(0), options);
			});
		}, 100);
	};

	/**
	 * Product thumbnail zoom.
	 */
	ecomus.productCardHoverZoom = function () {
		if ( typeof ecomusData.product_card_hover === 'undefined' || typeof Drift === 'undefined' ) {
			return;
		}

		if (ecomusData.product_card_hover !== 'zoom') {
			return;
		}

		var $seletor = ecomus.$body.find('ul.products li.product .product-thumbnail-zoom');

		var options = {
			containInline: true,
			zoomFactor: 2,
		};

		$seletor.each(function () {
			var $this = $(this),
				$image = $this.find( 'img' ),
				imageUrl = $this.data( 'zoom' );

			options.paneContainer = $this.get(0);

			$image.attr( 'data-zoom', imageUrl );

			new Drift( $image.get(0), options );
		});
	};

	// Product Attribute
	ecomus.productAttribute = function () {
		ecomus.$body.on('mouseover', '.product-variation-item', function (e) {
            e.preventDefault();
			if( $(this).hasClass('selected') ) {
				return;
			}
			var $thumbnails = $(this).closest('.product-inner').find('.product-thumbnail');

            $thumbnails.addClass('hover-swatch');

			if( $thumbnails.find('.product-thumbnails--slider').length > 0 && $thumbnails.find('.product-thumbnails--slider').get(0).swiper ) {
				$thumbnails.find('.product-thumbnails--slider').get(0).swiper.slideTo(0);
			}

			$(this).siblings('.product-variation-item--attrs').removeClass('selected');
            $(this).addClass('selected');
            var variations= $(this).data('product_variations'),
                $mainImages = $thumbnails.find('.woocommerce-LoopProduct-link').first(),
                $image = $mainImages.find('img').first();

            $mainImages.addClass('image-loading');

			if ( variations && variations.img_src !== 'undefined' && variations.img_src ) {
            	$image.attr('src', variations.img_src);
			}
            if ( variations && variations.img_srcset !== 'undefined' && variations.img_srcset ) {
                $image.attr('srcset', variations.img_srcset);
            }
            if ( variations && variations.img_original !== 'undefined' && variations.img_original ) {
                $image.closest('a' ).attr('data-zoom', variations.img_original);
                $image.attr('data-zoom', variations.img_original);
            }

            $image.load(function () {
                $mainImages.removeClass('image-loading');
            });

        }).on('mouseout', '.product-variation-item', function (e) {
            e.preventDefault();
			$(this).closest('.product-inner').find('.product-thumbnail').removeClass('hover-swatch');
        });
    };

	/**
	 * Add class to .form-row when inputs are focused.
	 */
	ecomus.formFieldFocus = function() {
		$( '.woocommerce-account' )
		.on( 'keyup focus change', '.woocommerce-form-row .input-text, .woocommerce-form-row input[type=text]', function() {
			$( this ).closest( '.woocommerce-form-row' ).addClass( 'focused' );
		} )
		.on( 'blur','.woocommerce-form-row .input-text, .woocommerce-form-row input[type=text]', function() {
			if ( $( this ).val() === '' ) {
				$( this ).closest( '.woocommerce-form-row' ).removeClass( 'focused' );
			}
		} )
		.find('.woocommerce-form-row').each( function () {
			var $input = $(this).find( '.input-text, input[type=text]' );
			if ( $input.val() !== '') {
				$( this ).addClass( 'focused' );
			}

			setTimeout(function() {
				if ($input.css('border-color') === 'rgb(0, 0, 0)') {
					$input.closest('.woocommerce-form-row').addClass('focused');
				}
			}, 100);

		})
		.on('click', '.showlogin', function() {
			$( this ).closest( '.woocommerce' ).find( '.em-button-login-mode' ).trigger('click');
		});

	};

	/**
	 * Login Popup
	 */
	ecomus.loginPopup = function() {
		var $modal = $( '#login-modal' );

		$modal
		.on( 'click', '.em-button-register-mode', function(e) {
			e.preventDefault();
			$modal.find('.woocommerce-customer-login').removeClass('active');
			$modal.find('.woocommerce-customer-register').addClass('active');
		} )
		.on( 'click', '.em-button-login-mode', function(e) {
			e.preventDefault();
			$modal.find('.woocommerce-customer-login').addClass('active');
			$modal.find('.woocommerce-customer-register').removeClass('active');
		} );

	}

	/**
	 * Ajax login before refresh page
	 */
	ecomus.loginModalAuthenticate = function () {
		var $modal = $( '#login-modal' ),
			xhr = null;
		$modal.on( 'submit', 'form.login', function authenticate( event ) {
			var remember = $( 'input[name=rememberme]', this ).is( ':checked' ),
				nonce = $( 'input[name=woocommerce-login-nonce]', this ).val();

			var formData = {
				action: 'login_modal_authenticate',
				security: nonce,
				remember: remember
			};

			getLoginAJAX( this, formData, event );
		});

		$modal.on( 'submit', 'form.register', function authenticate( event ) {
			var nonce = $( 'input[name=woocommerce-register-nonce]', this ).val();

			var formData = {
				action: 'register_modal_authenticate',
				security: nonce,
			};

			getLoginAJAX( this, formData, event );
		});

		function getLoginAJAX( form, formData, event ) {
			var username = $( 'input[name=username]', form ).val(),
				password = $( 'input[name=password]', form ).val(),
				email = $( 'input[name=email]', form ).val(),
				$button = $( '[type=submit]', form ),
				$form = $( form );

			if ($form.find('input[name=username]').length) {
				if( !username ) {
					$form.find('input[name=username]').focus();
					return false;
				} else {
					formData.username = username;
				}

			}

			if ($form.find('input[name=email]').length) {
				if( !email ) {
					$form.find('input[name=email]').focus();
					return false;
				} else {
					formData.email = email;
				}
			}

			if ($form.find('input[name=password]').length) {
				if( ! password ) {
					$form.find('input[name=password]').focus();
					return false;
				} else {
					formData.password = password;
				}

			}
			if ( $form.data( 'validated' ) ) {
				return true;
			}

			if (xhr) {
				xhr.abort();
			}

			$modal.find('.login-modal-notices').remove();
			$button.addClass('loading');
			xhr = $.post(
				ecomusData.admin_ajax_url,
				formData,
				function (response) {
					if ( ! response.success ) {
						var $notice = '<div class="login-modal-notices woocommerce-error">' + response.data + '</div>';
						$modal.find('.modal__content').append( $notice );
						$button.removeClass('loading');
					} else {
						var $notice = '<div class="login-modal-notices woocommerce-info">' + response.data + '</div>';
						$modal.find('.modal__content').append( $notice );
						$button.removeClass('loading');
						setTimeout( function() {
							$form.data( 'validated', true ).trigger('submit');
						}, 1500 );
					}
				}
			);

			event.preventDefault();
		};
	};

	/**
	 * Change product quantity
	 */
	ecomus.productQuantityNumber = function () {
		ecomus.$body.on('click', '.ecomus-qty-button', function (e) {
			e.preventDefault();

			var $this = $(this),
				$qty = $this.siblings('.qty'),
				current = 0,
				min = parseFloat($qty.attr('min')),
				max = parseFloat($qty.attr('max')),
				step = parseFloat($qty.attr('step'));

			if ($qty.val() !== '') {
				current = parseFloat($qty.val());
			} else if ($qty.attr('placeholder') !== '') {
				current = parseFloat($qty.attr('placeholder'))
			}

			min = min ? min : 0;
			max = max ? max : current + 1;

			if ($this.hasClass('decrease') && current > min) {
				$qty.val(current - step);
				$qty.trigger('change');
			}
			if ($this.hasClass('increase') && current < max) {
				$qty.val(current + step);
				$qty.trigger('change');
			}
		});
	};

	/**
	 * Quick view modal.
	 */
	ecomus.productQuickView = function() {
		$( document.body ).on( 'click', '.ecomus-quickview-button', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				product_id = $el.data( 'id' ),
				$target = $( '#' + $el.data( 'target' ) ),
				$container = $target.find( '.woocommerce' ),
				ajax_url = ecomusData.ajax_url.toString().replace('%%endpoint%%', 'product_quick_view'),
				slider = null;

			if( $el.closest( '.offscreen-panel' ).length > 0 ) {
				$el.closest( '.offscreen-panel' ).addClass( 'modal-above-panel' );
			}

			$target.addClass( 'loading' );

			$el.addClass( 'loading' );
			$container.find( '.product-quickview' ).html( '' );
			ecomus.progressBar();

			$.post(
				ajax_url,
				{
					action    : 'ecomus_get_product_quickview',
					product_id: product_id,
					security  : ecomusData.product_quickview_nonce
				},
				function( response ) {
					$container.find( '.product-quickview' ).replaceWith( response.data );

					ecomusMore( $( '.product-quickview' ).find( '.short-description__content' ) );

					if ( response.success ) {
						ecomus.progressBar( false );

						update_quickview();
					}

					$el.removeClass( 'loading' );

					$target.removeClass( 'loading' );
					if( ! $target.hasClass( 'modal--open' ) ) {
						ecomus.openModal( $target );
					}

					if( $target.find( '.size-guide-button' ).length == 1 ) {
						$target.find( '.size-guide-button' ).attr( 'data-modal', 'no' );
					}

					ecomus.$body.trigger( 'ecomus_product_quick_view_loaded' );

					if ( $container.find('.ecomus-countdown').length > 0) {
						$(document.body).trigger('ecomus_countdown', [$('.ecomus-countdown')]);
					}
				}
			).fail( function() {
				window.location.herf = $el.attr( 'href' );
			} );

			/**
			 * Update quick view common elements.
			 */
			function update_quickview() {
				var $product = $container.find( '.product-quickview' ),
					$gallery = $product.find( '.woocommerce-product-gallery' ),
					$variations = $product.find( '.variations_form' );

				update_product_gallery();
				$gallery.on( 'ecomus_update_product_gallery_on_quickview', function(){
					update_product_gallery();
				});

				// Variations form.
				if (typeof wc_add_to_cart_variation_params !== 'undefined') {

					$variations.each(function () {
						ecomus.productVariation();
						$(this).wc_variation_form();
					});
				}

				$( document.body ).trigger( 'init_variation_swatches');
			}

			/**
			 * Update quick view common elements.
			 */
			function update_product_gallery() {
				var $product = $container.find( '.product-quickview' ),
					$gallery = $product.find( '.woocommerce-product-gallery' ),
					$slider = $gallery.find( '.woocommerce-product-gallery__wrapper' );

				// Prevent clicking on gallery image link.
				$gallery.on( 'click', '.woocommerce-product-gallery__image a', function( event ) {
					event.preventDefault();
				} );

				// Init swiper slider.
				if ( $slider.find( '.woocommerce-product-gallery__image' ).length > 1 ) {
					$slider.addClass('woocommerce-product-gallery__slider swiper');
					$slider.wrapInner('<div class="swiper-wrapper"></div>');
					$slider.find('.swiper-wrapper').after('<span class="ecomus-svg-icon em-button-light ecomus-swiper-button swiper-button swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 11L0 5.5L5.5 0L6.47625 0.97625L1.9525 5.5L6.47625 10.0238L5.5 11Z" fill="currentColor"/></svg></span>');
					$slider.find('.swiper-wrapper').after('<span class="ecomus-svg-icon em-button-light ecomus-swiper-button swiper-button swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 11L7 5.5L1.5 0L0.52375 0.97625L5.0475 5.5L0.52375 10.0238L1.5 11Z" fill="currentColor"/></svg></span>');
					$slider.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

					var options = {
						loop: false,
						autoplay: false,
						speed: 800,
						watchOverflow: true,
						autoHeight: true,
						navigation: {
							nextEl: $slider.find('.swiper-button-next').get(0),
							prevEl: $slider.find('.swiper-button-prev').get(0),
						},
						on: {
							init: function () {
								$gallery.css('opacity', 1);
							}
						},
					};

					slider = new Swiper($slider.get(0), options);
				} else {
					$gallery.css( 'opacity', 1 );
				}
			}

			function ecomusMore ( $selector ) {
				var $line = ecomusData.product_description_lines,
					$height = parseInt( $selector.css( 'line-height' ) ) * $line;

				$selector.each( function () {
					var $currentHeight = $(this).outerHeight();

					if( $currentHeight < $height ) {
						$(this).siblings( '.short-description__more' ).addClass( 'hidden' );
					}
				});

				$( document.body ).on( 'click', '.short-description__more', function(e) {
					e.preventDefault();

					var $settings = $(this).data( 'settings' ),
						$more     = $settings.more,
						$less     = $settings.less;

					if( $(this).hasClass( 'less' ) ) {
						$(this).removeClass( 'less' );
						$(this).text( $more );
						$(this).siblings( '.short-description__content' ).removeAttr( 'style' );
					} else {
						$(this).addClass( 'less' );
						$(this).text( $less );
						$(this).siblings( '.short-description__content' ).css( '-webkit-line-clamp', 'inherit' );
					}
				});
			}
		});

		$( document.body ).on( 'click', '.size-guide-button', function( event ) {
			event.preventDefault();

			var $selector = $(this).closest( '.quick-view-modal' );

			if( $selector.length !== 1 ) {
				return;
			}

			if( ! $selector.hasClass( 'modal--open' ) ) {
				return;
			}

			ecomus.openModal( $selector.find( '.size-guide-modal' ) );

			$selector.addClass( 'size-guide-modal--open' );

		}).on( 'click', '.modal .modal__button-close, .modal .modal__backdrop', function( event ) {
			event.preventDefault();

			var $selector = $(this).closest( '.quick-view-modal' );

			if( $selector.length !== 1 ) {
				return;
			}

			if( ! $selector.hasClass( 'size-guide-modal--open' ) ) {
				return;
			}

			ecomus.closeModal( $selector.find( '.size-guide-modal' ) );
			ecomus.openModal( $selector );

			$selector.removeClass( 'size-guide-modal--open' );

		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				$( '.quick-view-modal' ).removeClass( 'size-guide-modal--open' );
			}
		} );;
	}

	/**
	 * Quick add modal.
	 */
	ecomus.productQuickAdd = function() {
		ecomus.$body.on( 'click', '.product_type_variable[data-target="quick-add-modal"]', function( event ) {
			event.preventDefault();

			var $el = $( this ),
				product_id = $el.data( 'product_id' ),
				$target = $( '#' + $el.data( 'target' ) ),
				$container = $target.find( '.woocommerce' ),
				ajax_url = ecomusData.ajax_url.toString().replace( '%%endpoint%%', 'product_quick_add' );

			$target.addClass( 'loading' );

			$el.addClass( 'loading' );
			$container.find( '.product-quickadd' ).html( '' );
			ecomus.progressBar();

			$.post(
				ajax_url,
				{
					action    : 'ecomus_get_product_quickadd',
					product_id: product_id,
					security  : ecomusData.product_quickadd_nonce
				},
				function( response ) {
					$container.find( '.product-quickadd' ).replaceWith( response.data );

					if ( response.success ) {
						ecomus.progressBar(false);
						update_quickadd();
					}

					$el.removeClass( 'loading' );

					$target.removeClass( 'loading' );

					ecomus.$body.trigger( 'ecomus_product_quick_add_loaded' );
				}
			).fail( function() {
				window.location.herf = $el.attr( 'href' );
			} );

			/**
			 * Update quick add common elements.
			 */
			function update_quickadd() {
				var $product = $container.find( '.product-quickadd' ),
					$variations = $product.find( '.variations_form' );

				// Variations form.
				if (typeof wc_add_to_cart_variation_params !== 'undefined') {

					$variations.each(function () {
						ecomus.productVariation();
						$(this).wc_variation_form();
					});
				}

				$( document.body ).trigger( 'init_variation_swatches');
			}
		});
	}

	ecomus.productFilterAjax = function() {
		$(document.body).on('ecomus_products_filter_before_send_request', function () {
            ecomus.progressBar();
		});

		$(document.body).on( 'ecomus_products_filter_request_success', function () {
			ecomus.progressBar(false);
		});
	}

	ecomus.addToCartLoopAjax = function() {
		var $add_to_cart = false;
		$(document.body).on('adding_to_cart', function () {
            ecomus.progressBar();
			$add_to_cart = true;
		});

		$(document.body).on( 'added_to_cart wc_fragments_refreshed', function () {
			if( $add_to_cart ) {
				ecomus.progressBar(false);
				$add_to_cart = false;
			}
		});
	}

	/**
	 * Open Mini Cart
	 */
	ecomus.openMiniCartPanel = function () {
		if (typeof ecomusData.added_to_cart_notice === 'undefined') {
			return;
		}

		if (ecomusData.added_to_cart_notice.added_to_cart_notice_layout !== 'mini') {
			return;
		}

		var status = false;
		$(document.body).on('adding_to_cart', function () {
            status = true;
		});

		$(document.body).on( 'added_to_cart wc_fragments_refreshed', function () {
			if( status ) {
				ecomus.openOffCanvas( '#cart-panel' );
			}
		});
	};

	/**
	 * Change quantity single product
	 */
	ecomus.changeQuantitySingleProduct = function () {
        $( document.body ).on( 'change', 'input.qty', function() {
			if( $(this).closest( '.entry-summary').find('.dynamic-pricing-discounts').length > 0 ) {
				return;
			}

			var qty    = $(this).val(),
			    $price = $(this).closest( 'form.cart' ).find( '.single_add_to_cart_button .price' ),
			    price  = $price.attr( 'data-price' );

			if( ! price ) {
				return;
			}

			$price.html( ecomus.formatPrice( parseFloat( qty * price ) ) );
		});
	};

	/**
	 * Product Variation
	 */
	ecomus.productVariation = function () {
		var $buttonATC  = $( 'div.product .entry-summary > .variations_form .woocommerce-variation-add-to-cart .single_add_to_cart_button' ),
			$buttontext    = $buttonATC.find( '.text' ).html(),
			$divide        = $buttonATC.find( '.divide' ),
			$price         = $buttonATC.find( '.price' ),
			$productTitle  = $( 'div.product .entry-summary > .product_title' ),
			$productBadges = $( 'div.product .entry-summary > .woocommerce-badges--single' ),
			$productPrice  = $( 'div.product .entry-summary .ecomus-product-price .price' ),
			$productStock  = $( 'div.product .entry-summary .ecomus-product-price .ecomus-product-availability' ),
			defaultPrice   = $productPrice.html(),
			defaultStock   = $productStock.html();

		$('div.product .entry-summary > .variations_form').on( 'found_variation', function (event, $variation) {
			$divide.removeClass('hidden');
			$price.removeClass('hidden');
			var display_price      = $variation.display_price,
			    variation_id       = $variation.variation_id,
			    $price_html        = $variation.price_html,
			    $stock_html        = $variation.availability_html,
			    $form              = $(this).closest( '.variations_form' ),
			    $qty               = $(this).closest( 'form.cart' ).find( '.quantity input.qty' ),
			    qty                = $qty.val(),
				$variation_buttontext = $form.find( '.em-addtocart-text-single-product--variable[data-variation_id = "'+ variation_id + '"]' ).text(),
				$variation_badges     = $form.find( '.em-badges-single-product--variable[data-variation_id="'+ variation_id + '"]' ).html();

			if( $price_html == '' ) {
				$price_html = $productPrice.html();
			} else {
				$productPrice.html( $price_html );
			}

			$price.attr( 'data-price', display_price );
			$price.html( ecomus.formatPrice( parseFloat( qty * display_price ) ) );

			if( $stock_html != '' ) {
				$productStock.html( $stock_html );
			}

			ecomus.variations_image_update($variation, $form);

			if( $variation_buttontext ) {
				$buttonATC.find( '.text' ).html( $variation_buttontext );
			} else {
				$buttonATC.find( '.text' ).html( $buttontext );
			}

			if( $productStock.find( '.stock' ).hasClass( 'hidden' ) ) {
				$productStock.find( '.stock' ).removeClass( 'hidden' );
			}

			$productTitle.siblings( '.woocommerce-badges--single' ).remove();
			if( $variation_badges ) {
				$productTitle.after( $variation_badges );
				$productStock.find( '.stock' ).addClass( 'hidden' );
			}
		});

		$('div.product .entry-summary > .variations_form').on( 'reset_data', function () {
			var $form = $(this).closest( '.variations_form' );
			$divide.addClass('hidden');
			$price.addClass('hidden');
			$productPrice.html( defaultPrice);
			$productStock.html( defaultStock);
			ecomus.variations_image_reset($form);
			$buttonATC.find( '.text' ).html( $buttontext );

			if( $productStock.find( '.stock' ).hasClass( 'hidden' ) ) {
				$productStock.find( '.stock' ).removeClass( 'hidden' );
			}

			$productTitle.siblings( '.woocommerce-badges--single' ).remove();
			if( $productBadges ) {
				$productTitle.after( $productBadges );
			}
		});
	}

	/**
	 * Sets product images for the chosen variation
	 */
	ecomus.variations_image_update = function( variation, $form ) {
		var $product          = $form.closest( '.product' ),
			$product_gallery  = $product.find( '.woocommerce-product-gallery' ),
			$gallery_wrapper = $product_gallery.find( '.woocommerce-product-gallery__wrapper' ),
			$gallery_nav      = $product.find( '.ecomus-product-gallery-thumbnails' ),
			$gallery_img      = $gallery_nav.find( '.woocommerce-product-gallery__image:eq(0) img' ),
			$product_img_wrap = $product_gallery
				.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' )
				.eq( 0 ),
			$product_img      = $product_img_wrap.find( '.wp-post-image' ),
			$product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

		if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
			// See if the gallery has an image with the same original src as the image we want to switch to.
			var galleryHasImage = $gallery_nav.find( '.woocommerce-product-gallery__image[data-thumb="' + variation.image.gallery_thumbnail_src + '"]' ).length > 0;

			// If the gallery has the image, reset the images. We'll scroll to the correct one.
			if ( galleryHasImage ) {
				ecomus.variations_image_reset($form);
			}
			if( ecomus.$window.width() > 768 && $product_gallery.hasClass( 'woocommerce-product-gallery--grid' ) ) {
				if( ! ecomus.setVariationImageToGalleryGrid($gallery_wrapper, $gallery_nav, $form, variation) ) {
					return false;
				}
			} else if( $gallery_wrapper.hasClass('swiper') ) {
				if( ! ecomus.setVariationImageToGallerySwiper($gallery_wrapper, $gallery_nav, $form, variation) ) {
					return false;
				}
			}

			$product_img.wc_set_variation_attr( 'src', variation.image.src );
			$product_img.wc_set_variation_attr( 'height', variation.image.src_h );
			$product_img.wc_set_variation_attr( 'width', variation.image.src_w );
			$product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
			$product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
			$product_img.wc_set_variation_attr( 'title', variation.image.title );
			$product_img.wc_set_variation_attr( 'data-caption', variation.image.caption );
			$product_img.wc_set_variation_attr( 'alt', variation.image.alt );
			$product_img.wc_set_variation_attr( 'data-src', variation.image.full_src );
			$product_img.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
			$product_img.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
			$product_img.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
			$product_img_wrap.wc_set_variation_attr( 'data-thumb', variation.image.src );

			$gallery_img.wc_set_variation_attr( 'src', variation.image.gallery_thumbnail_src );
			$gallery_img.wc_set_variation_attr( 'srcset', variation.image.gallery_thumbnail_src );
			$gallery_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
			$gallery_img.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
			$gallery_img.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
			$gallery_img.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
			$gallery_img.wc_set_variation_attr( 'title', variation.image.title );
			$gallery_img.wc_set_variation_attr( 'data-caption', variation.image.caption );
			$gallery_img.wc_set_variation_attr( 'alt', variation.image.alt );
			$gallery_img.wc_set_variation_attr( 'data-src', variation.image.full_src );

			$product_link.wc_set_variation_attr( 'href', variation.image.full_src );
		} else {
			ecomus.variations_image_reset($form);
		}

	};

	ecomus.setVariationImageToGalleryGrid = function($gallery_wrapper, $gallery_nav, $form, variation) {
		var slideToImage = $gallery_wrapper.find( '.woocommerce-product-gallery__image[data-thumb="' + variation.image.gallery_thumbnail_src + '"]' );
		if ( slideToImage.length > 0 && ! $( slideToImage ).hasClass('swiper-slide-active')) {
			$('html, body').animate({
				scrollTop: $( slideToImage ).offset().top
			}, 300);

			$form.attr( 'current-image', variation.image_id );
			return false;
		}

		if( ecomus.$window.scrollTop() > $gallery_wrapper.offset().top ) {
			$('html, body').animate({
				scrollTop: $gallery_wrapper.offset().top
			}, 300);
		}

		return true;

	}

	ecomus.setVariationImageToGallerySwiper = function($gallery_wrapper, $gallery_nav, $form, variation) {
		var slideToImage = $gallery_nav.find( '.woocommerce-product-gallery__image[data-thumb="' + variation.image.gallery_thumbnail_src + '"]' );
		if ( slideToImage.length > 0 ) {
			var index = $gallery_nav.find( '.woocommerce-product-gallery__image').index( slideToImage );

			$gallery_wrapper.get(0).swiper.slideTo(index);
			$form.attr( 'current-image', variation.image_id );
			return false;
		}

		$gallery_wrapper.get(0).swiper.slideTo(0);

		return true;
	}


	/**
	 * Reset main image to defaults.
	 */
	ecomus.variations_image_reset = function($form) {
		var $product         = $form.closest( '.product' ),
		    $product_gallery = $product.find( '.woocommerce-product-gallery' ),
		    $gallery_wrapper = $product_gallery.find( '.woocommerce-product-gallery__wrapper' ),
		    $gallery_nav     = $product.find( '.ecomus-product-gallery-thumbnails' ),
		    $gallery_img     = $gallery_nav.find( '.woocommerce-product-gallery__image:eq(0) img' ),
			$product_img_wrap = $product_gallery
				.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' )
				.eq( 0 ),
			$product_img      = $product_img_wrap.find( '.wp-post-image' ),
			$product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

		if( $gallery_wrapper.hasClass('swiper') ) {
			$gallery_wrapper.get(0).swiper.slideTo(0);
		}

		$product_img.wc_reset_variation_attr( 'src' );
		$product_img.wc_reset_variation_attr( 'width' );
		$product_img.wc_reset_variation_attr( 'height' );
		$product_img.wc_reset_variation_attr( 'srcset' );
		$product_img.wc_reset_variation_attr( 'sizes' );
		$product_img.wc_reset_variation_attr( 'title' );
		$product_img.wc_reset_variation_attr( 'data-caption' );
		$product_img.wc_reset_variation_attr( 'alt' );
		$product_img.wc_reset_variation_attr( 'data-src' );
		$product_img.wc_reset_variation_attr( 'data-large_image' );
		$product_img.wc_reset_variation_attr( 'data-large_image_width' );
		$product_img.wc_reset_variation_attr( 'data-large_image_height' );
		$product_img_wrap.wc_reset_variation_attr( 'data-thumb' );

		$gallery_img.wc_reset_variation_attr( 'src' );
		$gallery_img.wc_reset_variation_attr( 'srcset' );
		$gallery_img.wc_reset_variation_attr( 'sizes' );
		$gallery_img.wc_reset_variation_attr( 'data-large_image_width' );
		$gallery_img.wc_reset_variation_attr( 'data-large_image_height');
		$gallery_img.wc_reset_variation_attr( 'data-large_image' );
		$gallery_img.wc_reset_variation_attr( 'title' );
		$gallery_img.wc_reset_variation_attr( 'data-caption' );
		$gallery_img.wc_reset_variation_attr( 'alt' );
		$gallery_img.wc_reset_variation_attr( 'data-src' );

		$product_link.wc_reset_variation_attr( 'href' );
	};

	ecomus.updateQuantityAuto = function() {
		var debounceTimeout = null;
		$( document.body ).on( 'change', '.woocommerce-mini-cart .qty', function() {
			var $this = $(this);
			if ( debounceTimeout ) {
				clearTimeout( debounceTimeout );
			}

			debounceTimeout = setTimeout( function() {
				ecomus.updateCartAJAX( $this );
			}, 500 );

		} );
	};

	ecomus.updateCartAJAX = function ($qty) {
		var $row = $qty.closest('.woocommerce-mini-cart-item'),
			$cart_item = $qty.closest('.widget_shopping_cart_content').find('.woocommerce-mini-cart-item'),
			key = $row.find('a.remove').data('cart_item_key'),
			nonce = $row.find('.woocommerce-mini-cart-item__qty').data('nonce'),
			ajax_url = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_cart_item');

		if ($.fn.block) {
			$row.block({
				message: null,
				overlayCSS: {
					opacity: 0.6,
					background: '#fff'
				}
			});
		}

		$.post(
			ajax_url, {
				cart_item_key: key,
				qty: $qty.val(),
				cart_item_length: $cart_item.length,
				security: nonce
			}, function (response) {
				if (!response || !response.fragments) {
					return;
				}

				if ($.fn.unblock) {
					$row.unblock();
				}

				$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, $row] );

			}).fail(function () {
			if ($.fn.unblock) {
				$row.unblock();
			}

			return;
		});
	};

	/**
	 * Mini Cart - Producty Recommended Carousel
	 */
	ecomus.productsRecommendedCarousel = function() {
		setTimeout(function() {
			var $selector = $('.ecomus-mini-products-recommended');

			if( ! $selector.length ) {
				return;
			}

			var $element = $selector.find('.swiper'),
				options = {
					loop: false,
					autoplay: false,
					speed: 800,
					watchOverflow: true,
					spaceBetween: 30,
					slidesPerView: 1,
					pagination: {
						el: $selector.find('.swiper-pagination').get(0),
						type: 'bullets',
						clickable: true
					},
					breakpoints: {}
				};

			$selector.find('.woocommerce-loop-product').addClass('swiper-slide');


			new Swiper($element.get(0), options);
		}, 1);

	}

	ecomus.addedToWishlistNotice = function () {
		if ( typeof ecomusData.added_to_wishlist_notice === 'undefined' || ! $.fn.notify ) {
			return;
		}

		ecomus.$body.on('added_to_wishlist', function (e, $el_wrap) {
			var content = $el_wrap.data('product_title');
			getaddedToWishlistNotice(content);
			return false;
		});

		function getaddedToWishlistNotice($content) {
			$content += ' ' + ecomusData.added_to_wishlist_notice.added_to_wishlist_text;
			$content += '<a href="' + ecomusData.added_to_wishlist_notice.wishlist_view_link + '" class="btn-button">' + ecomusData.added_to_wishlist_notice.wishlist_view_text + '</a>';

			var $checkIcon = '<span class="ecomus-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>',
				$closeIcon = '<span class="ecomus-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="#A0A0A0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

			$.notify.addStyle('ecomus', {
				html: '<div>' + $checkIcon + $content + $closeIcon + '</div>'
			});

			$.notify('&nbsp', {
				autoHideDelay: ecomusData.added_to_wishlist_notice.wishlist_notice_auto_hide,
				className: 'success',
				style: 'ecomus',
				showAnimation: 'fadeIn',
				hideAnimation: 'fadeOut'
			});
		}
	}

	ecomus.addedToCompareNotice = function () {
		if ( typeof ecomusData.added_to_compare_notice === 'undefined' || ! $.fn.notify ) {
			return;
		}

		ecomus.$body.on( 'added_to_compare', function (e, $el_wrap) {
			var content = $el_wrap.data('product_title');
			getaddedToCompareNotice(content);
			return false;
		});

		function getaddedToCompareNotice($content) {
			$content += ' ' + ecomusData.added_to_compare_notice.added_to_compare_text;
			$content += '<a href="' + ecomusData.added_to_compare_notice.compare_view_link + '" class="btn-button">' + ecomusData.added_to_compare_notice.compare_view_text + '</a>';

			var $checkIcon = '<span class="ecomus-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>',
				$closeIcon = '<span class="ecomus-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="#A0A0A0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

			$.notify.addStyle('ecomus', {
				html: '<div>' + $checkIcon + $content + $closeIcon + '</div>'
			});

			$.notify('&nbsp', {
				autoHideDelay: ecomusData.added_to_compare_notice.compare_notice_auto_hide,
				className: 'success',
				style: 'ecomus',
				showAnimation: 'fadeIn',
				hideAnimation: 'fadeOut'
			});
		}
	}

	/**
     * Copy link
     */
    ecomus.copyLink = function () {
        $( '.ecomus-copylink__button' ).on( 'click', function(e) {
            e.preventDefault();
            var $button = $(this).closest('form').find('.ecomus-copylink__link');
            $button.select();
            document.execCommand('copy');
        });
    }

	/**
     * Progressbar
     */
    ecomus.progressBar = function ( show = true ) {
		if ( ! show ) {
			ecomus.$body.find( '.em-progress-bar' ).css( 'transform', 'scaleX(1)' );

			setTimeout( function() {
				ecomus.$body.find( '.em-progress-bar' ).css( 'display', 'none' );
				ecomus.$body.find( '.em-progress-bar' ).css( 'transform', 'scaleX(0)' );
			}, 600);

			setTimeout( function() {
				ecomus.$body.find( '.em-progress-bar' ).removeAttr('style');
			}, 700);
		} else {
			ecomus.$body.find( '.em-progress-bar' ).css( 'transform', 'scaleX(0.8)' );
		}
    }

	/**
     * Currencies
     */
	ecomus.currencyLanguage = function () {
		if( ! $('.ecomus-currency-language').length ) {
			return;
		}

		$( document.body ).on( 'click', '.ecomus-currency-language .current', function( event ) {
			if (! $(this).next().hasClass('currency-dropdown')) {
				return;
			}

			var width = $(this).outerWidth() / 2,
				spacing = $(this).offset().left,
				dropdownWidth = $(this).siblings('.currency-dropdown').width() / 2;

			if ( spacing <= dropdownWidth ) {
				width = dropdownWidth - spacing + 10;
			}

			$(this).next('.currency-dropdown').css( 'left', width );

			if ( $( document.body ).hasClass('rtl') ) {
				spacing = $(window).width() - ( spacing + $(this).outerWidth() );

				if ( spacing <= dropdownWidth ) {
					width = dropdownWidth - spacing + 10;
				}

				$(this).next('.currency-dropdown').css({
					'right': width,
					'left': 'auto'
				 });
			}

			if ( $(this).hasClass('active') ) {
				$(this).removeClass('active');
				$(this).next('.currency-dropdown').removeClass('active');
			} else {
				$('.ecomus-currency-language .current').removeClass('active');
				$('.ecomus-currency-language .currency-dropdown').removeClass('active');

				$(this).next('.currency-dropdown').stop().toggleClass('active');
				$(this).toggleClass('active');
			}
		} ).on( 'keyup', function ( e ) {
			if ( e.keyCode === 27 ) {
				$(this).removeClass('active');
				$('.ecomus-currency-language .current').removeClass('active');
				$('.ecomus-currency-language .currency-dropdown').removeClass('active');
			}
		} ).on( 'click', function( event ) {
			var $target = $( event.target );

			if ( $target.is( '.ecomus-currency-language .current' ) ) {
				return;
			}

			if ( $target.is( '.ecomus-currency-language .preferences-menu__item-child' ) ) {
				return;
			}

			$('.ecomus-currency-language .current').removeClass('active');
			$('.ecomus-currency-language .currency-dropdown').removeClass('active');
		} );

		$(document.body).on( 'ecomus_popover_opened', function () {
			$('.ecomus-currency-language .current').removeClass('active');
			$('.ecomus-currency-language .currency-dropdown').removeClass('active');
		});
    }

	/**
     * Click dropdown menu in hamburger menu
     */
	ecomus.hamburgerToggleMenuItem = function () {
		var $menu  = $('#mobile-menu-panel, #mobile-shop-panel'),
		    $click = 'ul.menu li.menu-item-has-children > a';

		if( ecomusData.header_mobile_menu_open_primary_submenus_on == 'icon' ) {
			$menu.find( 'ul.menu li.menu-item-has-children > a').append( '<span class="toggle-menu-children"></span>' );
			$click = 'ul.menu li.menu-item-has-children > a .toggle-menu-children';
		}

		$menu.on( 'click', $click, function( e ) {
			e.preventDefault();

			var $item = $( this ).closest('li.menu-item-has-children');

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' );

			// If this is sub-menu item
			$item.children( 'ul.sub-menu' ).slideToggle('fast');
			$item.siblings().find( 'ul.sub-menu' ).slideUp('fast');
		});
	}

	/**
     * Click dropdown sub menu item in hamburger menu
     */
	ecomus.hamburgerToggleSubMenuItem = function () {
		var $menu  = $('#mobile-menu-panel'),
		    $click = '.mega-menu .menu-item--widget-heading';

		if( ecomusData.header_mobile_menu_open_primary_submenus_on == 'icon' ) {
			$menu.find( '.mega-menu .menu-item--widget-heading > *' ).append( '<span class="toggle-menu-children"></span>' );
			$click = '.mega-menu .menu-item--widget-heading > * .toggle-menu-children';
		}

		$menu.on( 'click', $click, function( e ) {
			e.preventDefault();

			var $selector = $( this ).closest( '.menu-item--widget-heading' );

			if ( $selector.hasClass('active') ) {
				$selector.removeClass( 'active' );
				$selector.nextAll('.menu-item:not(.menu-item--widget-heading)').slideUp('fast');
			} else {
				$('.mega-menu .menu-item--widget-heading').removeClass('active');
				$('.mega-menu .mega-menu-sub-item').slideUp('fast');

				$selector.addClass( 'active' );
				$selector.nextUntil('.menu-item--widget-heading').slideDown('fast');
			}
		});

		$('#mobile-menu-panel').find('.mega-menu .mega-menu__column .menu-item--widget-heading').nextAll('.menu-item:not(.menu-item--widget-heading)').addClass('mega-menu-sub-item').hide();
	}

	/**
     * Header campaign bar
     */
	ecomus.headerCampaignBar = function () {
		var $selector = $('#campaign-bar');

		if( ! $selector.length ) {
			return;
		}

		$selector.on( 'click', '.campaign-bar__close', function( e ) {
			e.preventDefault();

			$(this).closest('.campaign-bar').slideUp();
		});

		if ( $selector.hasClass('campaign-bar-type--marquee') ) {
			this.headerCampaignBarMarquee();
		}

		if ( $selector.hasClass('campaign-bar-type--slides') ) {
			this.headerCampaignBarSlides();
		}
	}

	/**
     * Header campaign bar type Marquee
     */
	ecomus.headerCampaignBarMarquee = function () {
		var $selector = $('#campaign-bar'),
			$inner = $selector.find('.campaign-bar__container'),
			$items = $selector.find('.campaign-bar__items'),
			amount = ( parseInt( Math.ceil( jQuery( window ).width() / $items.outerWidth( true ) ) ) || 0 ) + 1,
			dataSpeed = $inner.data('speed'),
			speed = 1 / parseFloat( dataSpeed ) * ( $items.outerWidth( true ) / 350 );

			$inner.css( '--em-campaign-speed', speed + 's' );

		for ( let i = 1; i <= amount; i++ ) {
			var item = $items.clone();

			item.addClass( 'campaign-bar__items--duplicate em-absolute' );
			item.css( '--em-campaign-index', i.toString() );

			item.appendTo( $inner );
		}
	}

	/**
     * Header campaign bar type Slides
     */
	ecomus.headerCampaignBarSlides = function () {
		var $selector = $('#campaign-bar'),
			$items = $selector.find('.campaign-bar__items');

		$items.after('<span class="ecomus-svg-icon ecomus-swiper-button swiper-button swiper-button-text swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 11L0 5.5L5.5 0L6.47625 0.97625L1.9525 5.5L6.47625 10.0238L5.5 11Z" fill="currentColor"/></svg></span>');
		$items.after('<span class="ecomus-svg-icon ecomus-swiper-button swiper-button swiper-button-text swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 11L7 5.5L1.5 0L0.52375 0.97625L5.0475 5.5L0.52375 10.0238L1.5 11Z" fill="currentColor"/></svg></span>');

		var $element = $selector.find('.swiper'),
			options = {
				loop: true,
				autoplay: true,
				speed: 1000,
				watchOverflow: true,
				navigation: {
					nextEl: $element.find('.swiper-button-next').get(0),
					prevEl: $element.find('.swiper-button-prev').get(0),
				},
			};

		new Swiper($element.get(0), options);
	}

	/**
	 * Sticky header
	 */
	ecomus.stickyHeader = function () {
		if( ecomusData.header_sticky ) {
			sticky( ecomus.$header.find('.site-header__desktop'), ecomus.$header.find('.site-header__section.ecomus-header-sticky') );
		}

		if( ecomusData.header_mobile_sticky ) {
			sticky( ecomus.$header.find('.site-header__mobile'), ecomus.$header.find('.site-header__section.ecomus-header-mobile-sticky') );
		}

		function sticky( $headerSticky, $headerSection ) {
			var header    = ecomus.$header.outerHeight(true),
				hBody     = ecomus.$body.outerHeight(true),
				campaign  = $('#campaign-bar').is(":visible") ? $('#campaign-bar').height() : 0,
				topbar    = $('#topbar').is(":visible") ? $('#topbar').height() : 0,
				scrollTop = header + campaign + topbar + 200,
				heightHeader = ecomus.$header.find('.site-header__desktop').length ? ecomus.$header.find('.site-header__desktop').outerHeight() : 0,
				heightHeaderMobile = ecomus.$header.find('.site-header__mobile').length ? ecomus.$header.find('.site-header__mobile').outerHeight() : 0;

			if ( 'up' === ecomusData.header_sticky_on ) {
				if( $headerSticky.length ) {
					var stickyHeader = new Headroom( $headerSticky.get(0), {
						offset: scrollTop
					});

					stickyHeader.init();
				}
			}

			ecomus.$window.on('scroll', function () {
				var scroll = ecomus.$window.scrollTop();

				if (hBody <= scrollTop + ecomus.$window.height()) {
					return;
				}

				if (scroll > scrollTop) {
					if (ecomus.$window.width() > 768) {
						ecomus.$header.css('height', heightHeader);
					} else {
						ecomus.$header.css('height', heightHeaderMobile);
					}

					if ( 'up' !== ecomusData.header_sticky_on ) {
						$headerSection.addClass('minimized');
					}

				} else {
					ecomus.$header.removeAttr('style');

					if ( 'up' !== ecomusData.header_sticky_on ) {
						$headerSection.removeClass('minimized');
					}
				}
			});
		}
	};

	/**
     * Topbar slides item
     */
	ecomus.topbarSlides = function () {
		var $selector = $('#topbar-slides');

		if( ! $selector.length ) {
			return;
		}

		var $element = $selector.find('.swiper'),
			options = {
				loop: true,
				autoplay: true,
				speed: 800,
				watchOverflow: true,
				spaceBetween: 30,
			};

		new Swiper($element.get(0), options);
	}

	/**
	 * Back to top icon
	 */
	ecomus.backToTop = function () {
		var $scrollTop = $('#gotop');

		ecomus.$window.on('scroll', function () {
			if ( ecomus.$window.scrollTop() > 100 ) {
				$scrollTop.addClass('show-scroll');
			} else {
				$scrollTop.removeClass('show-scroll');
			}

			let scrollTop2    = ecomus.$window.scrollTop(),
			    docHeight     = ecomus.$body.outerHeight(),
			    winHeight     = $(window).height(),
			    scrollPercent = scrollTop2 / (docHeight - winHeight),
			    degrees       = scrollPercent * 360;

			$scrollTop.css( '--cricle-degrees', degrees + 'deg' );
		});

		ecomus.$body.on('click', '#gotop', function (e) {
			e.preventDefault();

			$('html, body').animate({scrollTop: 0}, 800);
		});
	};


	/**
	 * Dropdown product categories sidebar
	 *
	 * @return boolean
	 */
	ecomus.toggleProductCategoriesWidget = function() {
		var $widget = $( '.widget_product_categories' );

		$widget.find('li.cat-parent').each( function() {
			if ( $(this).find('ul.children').length > 0 ) {
				$(this).append('<span class="em-product-cat-item-toggle"></span>');
			}
		});

		$widget.on( 'click', 'li.cat-parent > .em-product-cat-item-toggle', function (e) {
			e.preventDefault();

			var $item = $( this ).closest('li.cat-parent');

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' );

			// If this is sub-menu item
			$item.children( 'ul.children' ).slideToggle();

        });

		$('.catalog-sidebar .wp-block-group').each(function(){
			if( $(this).find('.wp-block-heading').length ) {
				$(this).find('.wp-block-heading').addClass('em-widget-heading clicked');
				$(this).find('.wp-block-heading').append('<span class="em-collapse-icon"></span>');
				$(this).find('.wp-block-heading').nextAll().wrapAll('<div class="em-widget-group dropdown"/>');
			}
		});

		$('.catalog-sidebar .widget').each(function(){
			if( $(this).find('.widget-title').length ) {
				$(this).find('.widget-title').addClass('em-widget-heading clicked');
				$(this).find('.widget-title').append('<span class="em-collapse-icon"></span>');
				$(this).find('.widget-title').nextAll().wrapAll('<div class="em-widget-group dropdown"/>');
			}
		});

		$('.catalog-sidebar .em-widget-heading').on('click', function (e) {
			e.preventDefault();
			if (! $(this).next().hasClass('dropdown')) {
				return;
			}
			$(this).next('.dropdown').stop().slideToggle();
			$(this).toggleClass('active');
			return false;
		});
	};

	/**
	 * Dropdown product categories sidebar
	 *
	 * @return boolean
	 */
	ecomus.dropdownProductCategoriesSidebar = function( el ) {
		var $widget = $( '.wp-block-woocommerce-product-categories' ),
			$categoriesList = $widget.find('.wc-block-product-categories-list');

		if ( ! $widget.hasClass('is-list') ) {
			return;
		}

		$widget.addClass('em-product-categories-widget');

		$categoriesList.addClass('em-product-categories-dropdown');
		$categoriesList.closest('.wc-block-product-categories-list-item').addClass('em-product-categories-has-children');
		$categoriesList.closest('.wc-block-product-categories-list-item').append('<span class="em-product-categories-toggler" aria-hidden="true"></span>');

		$widget.on( 'click', '.em-product-categories-has-children > .em-product-categories-toggler', function (e) {
			e.preventDefault();

			var $item = $( this ).closest('.em-product-categories-has-children');

			$item.toggleClass( 'active' ).siblings().removeClass( 'active' );

			// If this is sub-menu item
			$item.children( '.em-product-categories-dropdown' ).slideToggle();
			$item.siblings().find( '.em-product-categories-dropdown' ).slideUp();

        });
	};

	ecomus.formatPrice = function( $number ) {
		var currency       = ecomusData.currency_symbol,
			thousand       = ecomusData.thousand_sep,
			decimal        = ecomusData.decimal_sep,
			price_decimals = ecomusData.price_decimals,
			currency_pos   = ecomusData.currency_pos,
			n              = $number;

		if ( parseInt(price_decimals) > 0 ) {
			$number = $number.toFixed(price_decimals) + '';
			var x = $number.split('.');
			var x1 = x[0],
				x2 = x.length > 1 ? decimal + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + thousand + '$2');
			}

			n = x1 + x2
		}

		switch (currency_pos) {
			case 'left' :
				return currency + n;
				break;
			case 'right' :
				return n + currency;
				break;
			case 'left_space' :
				return currency + ' ' + n;
				break;
			case 'right_space' :
				return n + ' ' + currency;
				break;
		}
	}

	/**
	 * Tooltip
	 */
	ecomus.tooltip = function () {
		ecomus.$body.on( 'mouseenter', '.em-tooltip', function (e) {
			if( ecomus.$body.find('.em-tooltip--data').length > 0 ) {
				$( '.em-tooltip--data' ).remove();
			}

			if( $(this).hasClass( 'loading') ) {
				$( '.em-tooltip--data' ).remove();
				return;
			}

			var $tooltip = $( '<div class="em-tooltip--data em-absolute"></div>' ),
				self = $(this),
				time = 200;

			$tooltip.appendTo( 'body' );

			$tooltip.attr( 'data-tooltip', $(this).data( 'tooltip' ) );

			if( $(this).data( 'tooltip_added' ) ) {
				$tooltip.attr( 'data-tooltip_added', $(this).data( 'tooltip_added' ) );
			}

			if( $(this).hasClass( 'added' ) ) {
				$tooltip.addClass( 'added' );
			}

			if( self.hasClass( 'product-loop-button' ) && ! self.closest( '.product-featured-icons' ).hasClass( 'product-featured-icons--single-product' ) ) {
				time = 500;
			}

			$tooltip.fadeIn( time, function() {
				var position = self.offset(),
					height = self.outerHeight(),
					tooltipWidth = $('.em-tooltip--data').outerWidth(),
					tooltipHeight = $('.em-tooltip--data').outerHeight(),
					top = ( position.top - tooltipHeight ) + height + 'px',
					left = position.left + self.outerWidth() / 2 - tooltipWidth / 2 + 'px';

				if( self.data( 'tooltip_position' ) ) {
					$tooltip.addClass( 'bottom' );
				} else {
					$tooltip.addClass( 'top' );
					top = position.top - tooltipHeight - 7 + 'px';

					if( self.closest( '.product-featured-icons--second' ).length > 0 ) {
						if( self.closest( '.product-featured-icons--second' ).hasClass( 'product-featured-icons--right' ) ) {
							$tooltip.addClass( 'left' );
							left = position.left - tooltipWidth - 7 + 'px';
						} else {
							$tooltip.addClass( 'right' );
							left = ( position.left + self.outerWidth() - 7 ) + 'px';
						}

						top = position.top + self.outerHeight() / 2 - tooltipHeight / 2 + 'px';
					}
				}

				$tooltip.css( {
					top: top,
					left: left,
				});
			});
		}).on( 'mouseleave', '.em-tooltip', function (e) {
			$( '.em-tooltip--data' ).remove();
		});
	}

	/**
	 * Document ready
	 */
	$(function () {
		ecomus.init();
	});

})(jQuery);