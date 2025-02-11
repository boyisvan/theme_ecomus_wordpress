(function ($) {
    'use strict';

    var ecomus = ecomus || {};
    ecomus.init = function () {
        ecomus.$body = $(document.body),
            ecomus.$window = $(window),
            ecomus.$header = $('#site-header');

        // Catalog
        this.topCategories();
        this.scrollFilterSidebar();
		this.productsFilterActivated();
        this.changeCatalogElementsFiltered();

        this.catalogOrderBy();
        this.loadMoreProducts();
    };

    // Top Categories
    ecomus.topCategories = function () {
		if (typeof Swiper === 'undefined') {
			return;
		}

		var $selector = $( '.catalog-top-categories' );

		var options = {
				observer: true,
    			observeParents: true,
                loop: false,
				autoplay: false,
                watchOverflow: true,
				spaceBetween: $selector.data('spacing'),
				navigation: {
					nextEl: $selector.find( '.swiper-button-next' ).get(0),
					prevEl: $selector.find('.swiper-button-prev' ).get(0),
				},
                pagination: {
					el: $selector.find('.swiper-pagination').get(0),
					type: 'bullets',
					clickable: true
				},
                breakpoints: {
					0: {
						slidesPerView: 2,
						slidesPerGroup: 2,
                        spaceBetween: 15,
					},
					768: {
						slidesPerView: 3,
						slidesPerGroup: 3,
						spaceBetween: $selector.data('spacing'),
					},
					1200: {
						slidesPerView: 5,
						slidesPerGroup: 1
					},

				}
			};

		new Swiper( $selector.get(0), options );
	};

    ecomus.scrollFilterSidebar = function () {
        ecomus.$body.on('ecomus_products_filter_before_send_request', function () {
            var $offset = 0;

            if( ! $("#ecomus-shop-content").length ) {
                return;
            }

            if( $('.catalog-toolbar').length ) {
                $offset += $('.catalog-toolbar').outerHeight();
            }

            $('html,body').stop().animate({
                scrollTop: $("#ecomus-shop-content").offset().top - $offset
            },
            '300',
            function () {});
        });
    };

	ecomus.productsFilterActivated = function () {
        var $primaryFilter = $( '.catalog-toolbar__filters-actived' ),
        	$activeFilters = $( '.catalog-toolbar__active-filters' ),
			$panelFilter = $( '.filter-sidebar-panel' ),
            $widgetFilter = $panelFilter.find( '.products-filter__activated-items' ),
			$removeAll = '<a href="#" class="remove-filtered remove-filtered-all">' + $primaryFilter.data( 'clear-text' ) + '</a>';

        	if( $widgetFilter.html() && $widgetFilter.html().trim() ) {
				$primaryFilter.html('');
				$primaryFilter.removeClass( 'active' );
				$primaryFilter.prepend( $widgetFilter.html() + $removeAll );
				$primaryFilter.addClass( 'active' );
				$activeFilters.addClass( 'actived' );
			}

        ecomus.$body.on( 'ecomus_products_filter_widget_updated', function (e, form) {
            var $panel = $(form).closest('.filter-sidebar-panel'),
				$widgetNewFilter = $panel.find('.products-filter__activated-items');

				if( $widgetNewFilter.html() && $widgetNewFilter.html().trim() ) {
					$primaryFilter.removeClass('hidden');
					$primaryFilter.html('');
					$primaryFilter.removeClass( 'active' );
					$primaryFilter.prepend( $widgetNewFilter.html() + $removeAll );
					$primaryFilter.addClass( 'active' );
					$activeFilters.addClass( 'actived' );
				}
        });

        $primaryFilter.on( 'click', '.remove-filtered', function (e) {
            var value = $(this).data( 'value' ),
				$widgetNewsFilter = $panelFilter.find('.products-filter__activated-items');

            if ( value !== 'undefined' ) {
                $(this).remove();
                $panelFilter.find( ".remove-filtered[data-value='" + value + "']" ).trigger( 'click' );
            }

			if( ! $widgetNewsFilter.html() || ! $widgetNewsFilter.html().trim() ) {
				$primaryFilter.html('');
				$primaryFilter.removeClass( 'active' );
				$activeFilters.removeClass( 'actived' );
			}

            return false;
        });

		$primaryFilter.on( 'click', '.remove-filtered-all', function (e) {
			e.preventDefault();
			$primaryFilter.html('');
			$primaryFilter.removeClass( 'active' );
			$activeFilters.removeClass( 'actived' );
			$panelFilter.find( '.products-filter__button .reset-button' ).trigger( 'click' );
			$('#site-content .woocommerce-notices-wrapper').fadeOut();
		});
    };

    ecomus.changeCatalogElementsFiltered = function () {
		ecomus.$body.on('ecomus_products_filter_before_send_request', function (e, response) {
			$('#filter-sidebar-panel').find( '.panel__button-close' ).trigger( 'click' );
        } );

        ecomus.$body.on('ecomus_products_filter_request_success', function (e, response) {
            var $html = $(response),
                $page_header = ecomus.$body.find('#page-header'),
                $top_categories = ecomus.$body.find('.catalog-top-categories'),
                $result_count = ecomus.$body.find('.catalog-toolbar__result-count'),
                $countdown_badges = ecomus.$body.find('.ecomus-badges-sale__countdown .ecomus-countdown'),
				$navigation = ecomus.$body.find( '.woocommerce-pagination' ),
				$sidebar = ecomus.$body.find( '.catalog-sidebar' ),
				$toolbar_view = ecomus.$body.find( '.ecomus-toolbar-view' ),
				$activeFilters = ecomus.$body.find( '.catalog-toolbar__active-filters' ),
				$notiProductFound = ecomus.$body.find( '.woocommerce-info');

            if( $html.find('#page-header').length ) {
                $page_header.replaceWith( $html.find( '#page-header' ) );
            }

			if ( $html.find( '.catalog-top-categories' ).length ) {
                $top_categories.replaceWith( $html.find( '.catalog-top-categories' ) );
				ecomus.topCategories();
            }

			if ( $html.find( '.catalog-toolbar__result-count' ).length ) {
                $result_count.replaceWith( $html.find( '.catalog-toolbar__result-count' ) );
            } else {
				$result_count.find( '.count' ).replaceWith( '<span class="count">0</span>' );
			}

			if ( $html.find( '.ecomus-badges-sale__countdown' ).length ) {
				$countdown_badges.ecomus_countdown();
            }

			if ( $html.find( '.woocommerce-pagination' ).length ) {
                $navigation.replaceWith( $html.find( '.woocommerce-pagination' ) );
            } else {
                $navigation.addClass('hidden');
            }

			if( $html.find('.catalog-sidebar').length ) {
                $sidebar.replaceWith( $html.find( '.catalog-sidebar' ) );
            }

			if ( $html.find( '.ecomus-toolbar-view' ).length ) {
				$toolbar_view.replaceWith( $html.find( '.ecomus-toolbar-view' ) );
			}

			if ( $html.find( '.em-button-no-products-found' ).length ) {
				$notiProductFound.append( $html.find( '.em-button-no-products-found' ) );
			}

			$activeFilters.removeClass('hidden');
        });
    };

    ecomus.catalogOrderBy = function () {
		var $selector = $('#mobile-orderby-popover'),
			$orderForm = $('.catalog-toolbar__item .woocommerce-ordering');

		if ( $.fn.select2 ) {
			$orderForm.find( 'select' ).select2( {
				width                  : 'auto',
				minimumResultsForSearch: -1,
				dropdownCssClass       : 'products-ordering',
				dropdownParent         : $orderForm
			} );
		}

		$selector.find('.mobile-orderby-list').on('click', 'a', function (e) {
            e.preventDefault();

			var value = $(this).data('id');

			// Click selectd item popup order list
			$selector.find('.mobile-orderby-list .selected').removeClass('selected');
			$(this).addClass( 'selected' );

			// Select content form order
			$orderForm.find('option:selected').attr("selected", false);
			$orderForm.find('option[value='+ value +']').attr("selected", "selected");

			$orderForm.trigger( 'submit' );
        });

		// Active Item
		var activeVal = $orderForm.find('option:selected').val();

		$selector.find('.mobile-orderby-list a[data-id='+ activeVal +']').addClass('selected');

    };

     /**
	 * Ajax load more products.
	 */
	ecomus.loadMoreProducts = function() {
		// Infinite scroll.
		if ( $( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--infinite' ) ) {
			var waiting = false,
				endScrollHandle;

			$( window ).on( 'scroll', function() {
				if ( waiting ) {
					return;
				}

				waiting = true;

				clearTimeout( endScrollHandle );

				infiniteScoll();

				setTimeout( function() {
					waiting = false;
				}, 100 );

				endScrollHandle = setTimeout( function() {
					waiting = false;
					infiniteScoll();
				}, 200 );
			});

		}

		function infiniteScoll() {
			var $navigation = $( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
				$button = $( '.woocommerce-pagination-button', $navigation );

			if ( ecomus.isVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
                $button.addClass( 'loading' );

				loadProducts( $button, function( respond ) {
					$button = $navigation.find( '.woocommerce-pagination-button' );
				});
			}
		}

		//Load More
		if ( $( '.woocommerce-pagination' ).hasClass( 'woocommerce-pagination--loadmore' ) ) {
			ecomus.$body.on( 'click', '.woocommerce-pagination.woocommerce-pagination--loadmore .woocommerce-pagination-button', function (event) {
				event.preventDefault();
				loadMore();
			});
		}

		function loadMore() {
			var $navigation = $( '.woocommerce-pagination.woocommerce-pagination--ajax' ),
				$button = $( '.woocommerce-pagination-button', $navigation );

			if ( ecomus.isVisible( $navigation ) && $button.length && !$button.hasClass( 'loading' ) ) {
                $button.addClass( 'loading' );

				loadProducts( $button, function( respond ) {
					$button = $navigation.find( '.woocommerce-pagination-button' );
				});
			}
		}

		/**
		 * Ajax load products.
		 *
		 * @param jQuery $el Button element.
		 * @param function callback The callback function.
		 */
		function loadProducts( $el, callback ) {
			var $nav = $el.closest( '.woocommerce-pagination' ),
				url = $el.attr( 'href' );

			$.get( url, function( response ) {
				var $content = $( '#main', response ),
					$list = $( 'ul.products', $content ),
					$products = $list.children(),
					$newNav = $( '.woocommerce-pagination.woocommerce-pagination--ajax', $content );

				$products.addClass( 'em-fadeinup em-animated' );

				let delay = 0.5;
				$products.each( function( i, product ) {
					jQuery(product).css( '--em-fadeinup-delay', delay + 's' );
					delay = delay + 0.1;
				});

				$products.appendTo( $nav.parent().find( 'ul.products' ) );

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

				ecomus.$body.trigger( 'ecomus_products_loaded', [$products, true] );

				if( $products.hasClass( 'em-animated' ) ) {
					setTimeout( function() {
						$products.removeClass( 'em-animated' );
					}, 10 );
				}
				$el.removeClass( 'loading' );

				if ( ecomusData.shop_nav_ajax_url_change ) {
					window.history.pushState( null, '', url );
				}
			});
		}
	};

    /**
	 * Check if an element is in view-port or not
	 *
	 * @param jQuery el Targe element to check.
	 * @return boolean
	 */
	ecomus.isVisible = function( el ) {
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
     * Document ready
     */
    $(function () {
        ecomus.init();
    });

})(jQuery);