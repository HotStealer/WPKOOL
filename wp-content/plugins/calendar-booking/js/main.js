jQuery( document ).ready( function( $ ) {

	function rgb2hex(rgb){
		var rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
		return (rgb && rgb.length === 4) ?
			("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
	}

	function cbsb_toast( message, type = 'info', duration = 4 ) {
		$( '#start-booking' ).append(
			'<div id="cbsb_toast" class="' + type + '">' +
			'<p class=\"pull-left\">' +
				message +
			'</p>' +
			'</div>'
		);
		$( '#cbsb_toast' ).fadeIn();
		window.setTimeout( function() {
			$( '#cbsb_toast' ).fadeOut();
			$( '#cbsb_toast' ).remove();
		}, duration * 1000 );
	}

	function cbsb_update_setting(name, value) {
		var data = {
			'action'    : 'cbsb_settings_update',
			'settings'   : [{ 'name' : name, 'value' : value }]
		}
		cbsb_send_settings( data );
	}

	function cbsb_update_settings( settings ) {
		var data = {
			'action'    : 'cbsb_settings_update',
			'settings'   : settings
		}
		cbsb_send_settings( data );
	}

	function cbsb_send_settings( data ) {
		$.post( ajaxurl, data, function( response ) {
			cbsb_toast( response.message, response.status, 5 );
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}
		} );
	}

	function cbsb_valid_url(str) {
		var a  = document.createElement('a');
		a.href = str;
		return (a.host);
	}

	$('#colorBgSelector').ColorPicker({
		color: '#000000',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorBgSelector').val(hex);
			$('#colorBgSelector div').css('backgroundColor', '#' + hex);
			return;
		},
		onSubmit: function (hsb, hex, rgb, el) {
			$(el).val(hex);
			this.color = hex;
			cbsb_update_setting( 'btn_bg_color', hex );
			return;
		}
	});

	$('#colorTextSelector').ColorPicker({
		color: '#ffffff',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorTextSelector').val(hex);
			$('#colorTextSelector div').css('backgroundColor', '#' + hex);
		},
		onSubmit: function (hsb, hex, rgb, el) {
			$(el).val(hex);
			cbsb_update_setting( 'btn_txt_color', hex );
			return;
		}
	});

	$('#class-thank-you-destination').blur( function() {
		var destination = $( '#class-thank-you-destination' ).val();
		if ( cbsb_valid_url( destination ) ) {
			cbsb_update_setting( 'class_destination', destination );
		} else {
			cbsb_toast( 'Invalid Class Destination URL', 'error', 5 );
		}
	});

	$('#service-thank-you-destination').blur( function() {
		var destination = $( '#service-thank-you-destination' ).val();
		if ( cbsb_valid_url( destination ) ) {
			cbsb_update_setting( 'service_destination', destination );
		} else {
			cbsb_toast( 'Invalid Service Destination URL', 'error', 5 );
		}
	});

	$('#booking-page-select').change(function () {
		var booking_page_id = $('#booking-page-select').val();
		var data = {
			'action'         : 'cbsb_update_booking_page',
			'booking_page'   : booking_page_id
		}
		if ( 'Select a page' != booking_page_id ) {
			$.post( ajaxurl, data, function( response ) {
				cbsb_toast( response.message, response.status, 5 );
				if ( response.reload ) {
					window.setTimeout( function() {
						window.location.reload();
					}, 1000 );
				}
			} );
		}
	});

	$('#booking-window').change(function () {
		var booking_window = $( '#booking-window' ).val();
		cbsb_update_setting( 'booking_window', booking_window );
	});

	$('#appointment-use-visitor-timezone').change(function () {
		var booking_window = $( '#appointment-use-visitor-timezone' ).val();
		cbsb_update_setting( 'appointment_use_visitor_timezone', booking_window );
	});

	$('#group-use-visitor-timezone').change(function () {
		var booking_window = $( '#group-use-visitor-timezone' ).val();
		cbsb_update_setting( 'group_use_visitor_timezone', booking_window );
	});

	$('#default-class-view').change(function () {
		var booking_window = $( '#default-class-view' ).val();
		cbsb_update_setting( 'default_class_view', booking_window );
	});

	$('#save_colors').click( function() {
		var colors = [
			{ 'name' : 'btn_txt_color', 'value' : $('#colorTextSelector').val() },
			{ 'name' : 'btn_bg_color', 'value' : $('#colorBgSelector').val() },
		];
		cbsb_update_settings(colors);
	});

	$('#cbsb-content-reset').click( function() {
		var data = {
			'action'         : 'cbsb_content_reset',
		}
		$.post( ajaxurl, data, function( response ) {
			cbsb_toast( response.message, response.status, 5 );
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload(true);
				}, 1000 );
			}
		} );
	})

	$('#show-progress').click(function() {
		cbsb_update_setting( 'show_progress', $(this).is(':checked') );
	});

	$('#show-sold-out-classes').click(function() {
		cbsb_update_setting( 'show_sold_out_classes', $(this).is(':checked') );
	});

	$('#show-room-details').click(function() {
		cbsb_update_setting( 'show_room_details', $(this).is(':checked') );
	});

	$('#show-remaining-availability').click(function() {
		cbsb_update_setting( 'show_remaining_availability', $(this).is(':checked') );
	});

	$('#endorse-us').click(function() {
		cbsb_update_setting( 'endorse_us', $(this).is(':checked') );
	});

	$('#additional-data').click(function() {
		cbsb_update_setting( 'allow_data_collection', $(this).is(':checked') );
	});

	$('#allow-booking').click(function() {
		cbsb_update_setting( 'disable_booking', $(this).is(':checked') );
	});

	$('#expedited-single-service').click(function() {
		cbsb_update_setting( 'expedited_single_service', $(this).is(':checked') );
	});

	$('#expedited-assign-provider').click(function() {
		cbsb_update_setting( 'automatic_provider', $(this).is(':checked') );
	});

	$('#expedited-single-service-type').click(function() {
		cbsb_update_setting( 'expedited_single_service_type', $(this).is(':checked') );
	});

	$('#expedited-qty-services').click(function() {
		cbsb_update_setting( 'expedited_qty_services', $(this).is(':checked') );
	});


	$('#disconnect_check').click( function() {
		if ( confirm( 'If you disconnect, all booking functionality will stop working until you reconnect with Start Booking.' ) == true ) {
			var data = {
				'action'               : 'cbsb_disconnect',
				'confirm_disconnect'   : 'true'
			}

			$.post( ajaxurl, data, function( response ) {
				cbsb_toast( response.message, response.status, 5 );
				if ( response.reload ) {
					window.setTimeout( function() {
						window.location.reload();
					}, 1000 );
				}
			} );
		}
	});

	$('#welcome a').click( function(){
		$('#welcome').hide();
		$('#connect').show();
	} );

	$('#setup-page').submit( function( e ) {
		e.preventDefault();

		var data = {
			'action' : 'cbsb_create_booking_page',
			'title'   : $( '#setup-page input[name="booking-title"]' ).val()
		}

		$.post( ajaxurl, data, function( response ) {
			cbsb_toast( response.message, response.status, 5 );
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}
		} );
	} );

	$('#setup-page a.no-action').click( function( e ) {
		e.preventDefault();

		var data = {
			'action'      : 'cbsb_create_booking_page',
			'no-action'   : true
		}

		$.post( ajaxurl, data, function( response ) {
			cbsb_toast( response.message, response.status, 5 );
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}
		} );
	} );

	$('#setup-key').submit( function( e ) {
		e.preventDefault();
		var email    = $('#setup-key input[name=email]').val();
		var password = $('#setup-key input[name=password]').val();
		var account  = $('#setup-key select[name=account]').val();
		var data = {
			'action'  : 'cbsb_auth_to_token',
			'email'   : email,
			'password': password,
		};

		if (account != null) {
			data.account_id = account;
		}

		$.post( ajaxurl, data, function( response ) {
			cbsb_toast( response.message, response.status, 5 );
			if ( typeof( response.accounts ) != 'undefined' && response.accounts !== null ) {
				$.each(response.accounts, function (index, value) {
					$( '#multi-account select' ).append( $( '<option/>', {
						value: value.account_key,
						text : value.account_name
					}));
				});
				$('#multi-account').show();
			}

			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}
		} );
	} );

	$( '#cbsb-content-form input' ).change( function() {
		var data = {
			'action'  : 'cbsb_update_copy',
			'content' : {},
		}

		$( '#cbsb-content-form input' ).each( function() {
			data[$(this).attr('name')] = $(this).val();
		} );

		$.post( ajaxurl, data, function( response ) {
			cbsb_toast( response.message, response.status, 5 );
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}
		} );
	} );
} );
