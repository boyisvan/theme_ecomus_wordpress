jQuery(document).ready(function ($) {
	"use strict";

	$( '#custom_badges_bg' ).not( '[id*="__i__"]' ).wpColorPicker({
		change: function(e, ui) {
			$(e.target).val(ui.color.toString());
			$(e.target).trigger('change');
		},
		clear: function(e, ui) {
			$(e.target).trigger('change');
		}
	});

	$( '#custom_badges_color' ).not( '[id*="__i__"]' ).wpColorPicker({
		change: function(e, ui) {
			$(e.target).val(ui.color.toString());
			$(e.target).trigger('change');
		},
		clear: function(e, ui) {
			$(e.target).trigger('change');
		}
	});

	$( '#variable_product_options' ).on( 'reload', function() {
		block();
		var postID = $('#post_ID').val();
		$.ajax({
			url     : ajaxurl,
			dataType: 'json',
			method  : 'post',
			data    : {
				action : 'ecomus_wc_product_attributes',
				post_id: postID
			},
			success : function (response) {
				$('#ecomus-product-attributes').html(response.data);
				unblock();
			}
		});
	});

	/**
	 * Block edit screen
	 */
	function block() {
		if ( ! $.fn.block ) {
			return;
		}

		$( '#woocommerce-product-data' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	}

	/**
	 * Unblock edit screen
	 */
	function unblock() {
		if ( ! $.fn.unblock ) {
			return;
		}

		$( '#woocommerce-product-data' ).unblock();
	}

});