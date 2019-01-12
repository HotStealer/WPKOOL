<?php

function cbsb_auth_to_token() {
	if ( ! empty ( $_POST['email'] ) && ! empty( $_POST['password'] ) ) {
		$email = sanitize_email( $_POST['email'] );
		$password = sanitize_post( $_POST['password'] );
		$account_id = ( isset( $_POST['account_id'] ) ) ? sanitize_text_field( $_POST['account_id'] ) : false;
		$body = array_filter( array(
			'email'       => $email,
			'password'    => $password,
			'website'     => get_site_url(),
			'account_id'  => $account_id
		) );

		$http_response = wp_remote_post( CBSB_APP_URL . 'api/v1/initialize', array( 'timeout' => 20, 'body' => $body ) );
		$http_body = wp_remote_retrieve_body( $http_response );

		$json = json_decode( $http_body );

		if ( property_exists( $json, 'error' ) ) {
			$response = array( 'status' => 'error', 'message' => 'Invalid Authentication.', 'reload' => false );
		} else {
			if ( isset( $json->token ) ) {
				update_option( 'cbsb_connection', array( 'token' => $json->token, 'account' => $json->account ) );
				if ( false == get_option( 'cbsb_booking_page' ) ) {
					cbsb_create_booking_page( 'Book Now' );
				}
				update_option( 'cbsb_overview_step', 'overview' );
				$response = array( 'status' => 'success', 'message' => 'Connection Established.', 'reload' => true );
			} elseif ( isset( $json->select_account ) ) {
				$response = array( 'status' => 'info', 'message' => 'Select an Account.', 'accounts' => $json->select_account, 'reload' => false );
			} else {
				$response = array( 'status' => 'error', 'message' => 'Invalid response from StartBooking.com.', 'reload' => false );
			}
		}
	} else {
		$response = array( 'status' => 'error', 'message' => 'Email & Password Required.', 'reload' => false );
	}
	wp_send_json( $response );
}
add_action( 'wp_ajax_cbsb_auth_to_token', 'cbsb_auth_to_token' );

function cbsb_account() {
	$response = array(
		'plan'         => cbsb_get_plan(),
		'subscription' => cbsb_account_subscription(),
		'timezone'     => cbsb_get_account_timezone(),
		'location_type' => cbsb_get_account_location_type()
	);
	wp_send_json( $response );
}
add_action( 'wp_ajax_cbsb_account', 'cbsb_account' );
add_action( 'wp_ajax_nopriv_cbsb_account', 'cbsb_account' );

function cbsb_process_appointment() {

	if ( ! isset( $_POST ) ) { echo 'error'; }
	$response = array(
		'confirmed_appointments' => array(),
		'unconfirmed_appointments' => array(),
		'status' => null,
	);

	global $cbsb;

	$settings = cbsb_current_settings();

	//check if the customer id is available and if not create a new customer
	if ( ! isset( $_POST['cust_uid'] ) || $_POST['cust_uid'] == null ) {
		$new_customer = array(
			'email' => sanitize_email( $_POST['email'] ),
			'first_name' => sanitize_text_field( $_POST['first_name'] ),
			'last_name' => sanitize_text_field( $_POST['last_name'] ),
			'mobile_phone' => preg_replace( '~\D~', '', $_POST['phone'] ),
		);

		$response = $cbsb->post( 'customer/create', $new_customer );

		if ( 'success' ==  $response['status'] ) {
			$customer_details = $response['data'];
			if ( property_exists( $customer_details, 'customer' ) && property_exists( $customer_details->customer, 'url_string' ) ) {
				$cust_uid = $customer_details->customer->url_string;
			}
		}
	} else {
		$cust_uid = sanitize_text_field( $_POST['cust_uid'] );
	}

	if ( is_array( $_POST['service_appointment'] ) ) {
		$service_appointments = $_POST['service_appointment'];
	}

	$price = 0;
	$service_map = get_option( 'cbsb_service_map' );
	$service_url_strings = array();

	foreach ( $service_appointments as $k => $service ) {
		$service = json_decode( stripcslashes( $service ) );
		$service_url_strings[] = $service->service_url_string;
		$service_appointments[ $k ] = $service;
		$service = $service_map[ $service->service_url_string ];
		$price += $service->price;
	}

	$details = $cbsb->get( 'account/details' );

	if ( 'success' == $details['status'] ) {
		$details = $details['data'];
	}

	if (property_exists( $details, 'payments' ) && property_exists( $details->payments, 'requires_payment_to_book' ) ) {
		$requires_payment = $details->payments->requires_payment_to_book;
	} else {
		$requires_payment = false;
	}

	$allow_payment = array(
		(false !== strpos( get_option( 'siteurl' ), 'https:' ) ),
		(false !== strpos( $details->payments->payment_key, '_test' ) )
	);

	$allow_payment = ( array_search( true, $allow_payment ) ) ? true : false;

	if ( $requires_payment && $allow_payment ) {

		if ( ! isset( $_POST['token'] ) ) {
			wp_send_json( array(
				'status'     => 'error',
				'message'    => 'Unable to identify card. Please try another card.',
				'error_type' => 'toast',
			) );
		} else {
			$payment_args = array(
				'token'               => ( $_POST['token'] ) ? $_POST['token'] : null,
				'card'                => ( $_POST['card'] ) ? $_POST['card'] : null,
				'amount'              => $price,
				'customer_url_string' => $cust_uid,
				'services'            => $service_url_strings
			);
			$preauth = $cbsb->post( 'charge/create', $payment_args );
		}

		if ( ! $preauth['data']->authorized || is_null( $preauth['data']->authorized ) ) {
			wp_send_json( array(
				'status'  => 'error',
				'message' => 'Unable to authorize charge. Please try another card.',
				'error_type' => 'toast',
			) );
		} else {
			$payment_args['charge'] = $preauth['data']->charge;
		}
	}

	$hash_group = null;

	foreach ( $service_appointments as $service_appointment ) {

		if ( ! $service_appointment ) {
			continue;
		}

		$required_key = array( 'duration', 'service_url_string', 'room_url_string', 'start_time', 'user_url_string' );

		if ( ! is_object( $service_appointment ) ) {
			continue;
		}

		foreach ( $required_key as $key ) {
			if ( ! isset( $service_appointment->{ $required_value } ) ) {
				continue;
			}
		}

		$appointment = array(
			'requested_time'         => (string) $service_appointment->duration,
			'customer_url_string'    => $cust_uid,
			'service_url_strings'    => array( $service_appointment->service_url_string ),
			'selected_room'          => (string) $service_appointment->room_url_string,
			'date'                   => date_format( date_create( $service_appointment->start_time ), "Y-m-d" ),
			'appointment_start_time' => date_format( date_create( $service_appointment->start_time ), "Y-m-d H:i:00" ),
			'selected_user'          => (string) $service_appointment->user_url_string,
			'all_available_users'    => array( $service_appointment->user_url_string ),
			'hash_group'             => $hash_group,
		);

		$appointment = array_filter( $appointment );

		$appointment_response = $cbsb->post( 'appointment/create', $appointment );

		if ( 'success' == $appointment_response['status'] && property_exists( $appointment_response['data'], 'appointment_uid' ) ) {
			$appointment['appointment_uid'] = $appointment_response['data']->appointment_uid;
			if ( is_null( $hash_group ) && property_exists( $appointment_response['data'], 'hash_group' ) ) {
				$hash_group = $appointment_response['data']->hash_group;
			}
			$appointment['hash_group'] = $hash_group;
			$appointment['assigned_user'] = $appointment_response['data']->assigned_user;
			$response['customer'] = $appointment_response['data']->customer;
			$response['confirmed_appointments'][] = $appointment;
		} else {
			$response['unconfirmed_appointments'][] = $appointment;
		}
	}

	if ( empty( $response['unconfirmed_appointments'] ) &&
		count( (array) $service_appointments ) === count( $response['confirmed_appointments'] )
	) {
		if ( $requires_payment && $allow_payment ) {
			$payment_args['appointments'] = array_column( $response['confirmed_appointments'], 'appointment_uid' );
			$capture = $cbsb->post( 'charge/capture', $payment_args );
		}
		$response['status'] = 'success';
	} else if ( ! empty( $response['unconfirmed_appointments'] ) && ! empty( $response['confirmed_appointments'] ) ) {
		$response['status'] = 'partial';
	} else {
		$response['status'] = 'error';
		$response['error_type'] = 'blocking';
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_cbsb_process_appointment', 'cbsb_process_appointment' );
add_action( 'wp_ajax_nopriv_cbsb_process_appointment', 'cbsb_process_appointment' );

function cbsb_join_group() {
	global $cbsb;
	if ( ! isset( $_POST ) ) { echo 'error'; }
	$settings = cbsb_current_settings();

	if ( isset( $_POST['customers'] ) && isset( $_POST['cart'] ) ) {
		$request = array(
			'customers' => json_decode( str_replace('\"', '"', $_POST['customers'] ) ),
			'cart'      => json_decode( str_replace('\"', '"', $_POST['cart'] ) ),
			'token'     => ( isset( $_POST['token'] ) ) ? $_POST['token'] : null,
			'card'      => ( isset( $_POST['card'] ) ) ? $_POST['card'] : null,
		);
	}

	$price = ( property_exists( $request['cart'], 'price' ) ) ? $request['cart']->price: 0;

	$order = array(
		'schedule_url_string' => $request['cart']->schedule_url_string,
		'schedule_occurrence' => (int) $request['cart']->occurrence
	);

	//Assign first customer since multiple customers is not supported at this time
	$customer = $request['customers'][0];

	if ( ! isset( $customer->cust_uid ) ) {
		$new_customer = array(
			'email' => sanitize_email( $customer->email ),
			'first_name' => sanitize_text_field( $customer->first_name ),
			'last_name' => sanitize_text_field( $customer->last_name ),
			'mobile_phone' => preg_replace( '~\D~', '', $customer->phone ),
		);

		$customer_response = $cbsb->post( 'customer/create', $new_customer );

		if ( 'success' ==  $customer_response['status'] ) {
			$customer_details = $customer_response['data'];
			if ( property_exists( $customer_details, 'customer' ) && property_exists( $customer_details->customer, 'url_string' ) ) {
				$customer->cust_uid = $customer_details->customer->url_string;
			}
		}
	}

	$join_response = $cbsb->post( 'customers/' . $customer->cust_uid . '/schedules', $order, false);

	if ( isset( $join_response['status'] ) && 'success' == $join_response['status'] ) {
		$join_response = $join_response['data'];
		if ( isset( $join_response->data ) && $join_response->data ) {
			$customer->confirmed = $join_response->data;
		} else if ( property_exists( $join_response, 'data' ) && is_null( $join_response->data ) && property_exists( $join_response, 'message' ) ) {
			wp_send_json( array( 'status' => 'error', 'message' => $join_response->message, 'error_type' => 'blocking' ) );
		}
	}

	if ( property_exists( $customer, 'confirmed' ) ) {
		$response = array(
			'status' => 'success',
			'data' => array(
				'customers'     => array( $customer ),
				'requested'     => 1,
				'confirmed'     => 1
			)
		);
	} else {
		$response = array(
			'status'     => 'error',
			'message'    => 'Unable to confirm customer for class.',
			'error_type' => 'blocking'
		);
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_cbsb_join_group', 'cbsb_join_group' );
add_action( 'wp_ajax_nopriv_cbsb_join_group', 'cbsb_join_group' );

function cbsb_get_services() {
	global $cbsb;
	$services = $cbsb->get( 'services', null, false );
	if ( isset( $services['status'] ) && $services['status'] == 'success' ) {
		wp_send_json( $services['data'] );
	}
}
add_action( 'wp_ajax_cbsb_get_services', 'cbsb_get_services' );
add_action( 'wp_ajax_nopriv_cbsb_get_services', 'cbsb_get_services' );

function cbsb_get_openings() {
	$response = array( 'data' => null );
	if ( isset( $_POST['date'] ) && isset( $_POST['services'] ) && is_array( $_POST['services'] ) ) {
		$settings = cbsb_current_settings();
		if ( $settings['booking_window'] && (strtotime( $_POST['date'] ) > time() + $settings['booking_window'])) {
			$response['data']['status'] = 'success';
			$response['data']['bookable_appointments'] = array();
			$response['data']['info'][] = 'Appointment selected too far in future.';
		} else {
			$params = array(
				'date' => date_format( date_create( $_POST['date'] ), "Y-m-d" ),
				'services' => array_map( 'esc_attr', $_POST['services']) ,
			);
			if ( isset( $_POST['provider'] ) ) {
				$params['users'] = array( esc_attr( $_POST['provider'] ) );
			}
			global $cbsb;
			$response = $cbsb->get( 'openings', $params, false );
		}
	}
	wp_send_json( $response['data'] );
}
add_action( 'wp_ajax_cbsb_get_openings', 'cbsb_get_openings' );
add_action( 'wp_ajax_nopriv_cbsb_get_openings', 'cbsb_get_openings' );

function cbsb_get_customer() {
	global $cbsb;
	if ( isset( $_POST['email'] ) ) {
		$email = sanitize_email( $_POST['email'] );
		$customer = $cbsb->post( 'customer/validate', array( 'email'=> $email ), false );
		if ( isset( $customer['status'] ) && $customer['status'] == 'success' ) {
			if ( is_null( $customer['data']->email ) ) {
				$customer['data']->email = $email;
			}
			wp_send_json( $customer['data'] );
		}
	}
}
add_action( 'wp_ajax_cbsb_get_customer', 'cbsb_get_customer' );
add_action( 'wp_ajax_nopriv_cbsb_get_customer', 'cbsb_get_customer' );

function cbsb_get_calendar() {
	global $cbsb;
	$args = array(
		'class'  => ( isset( $_POST['group'] ) ) ? sanitize_key( $_POST['group'] ) : null,
		'user'   => ( isset( $_POST['user'] ) ) ? sanitize_key( $_POST['user'] ) : null,
	);

	if ( isset( $_POST['range_start'] ) && '' != $_POST['range_start'] ) {
		$range_start_parts = explode( ' ', $_POST['range_start'] );
		$range_start_parts_limit = array_slice( $range_start_parts, 0, 4, true );
		$args['range_start'] = date( 'Y-m-d',  strtotime( implode( ' ', $range_start_parts_limit ) ) );
	}
	if ( isset( $_POST['range_end'] ) && '' != $_POST['range_end'] ) {
		$range_end_parts = explode( ' ', $_POST['range_end'] );
		$range_end_parts_limit = array_slice( $range_end_parts, 0, 4, true );
		$args['range_end'] = date( 'Y-m-d',  strtotime( implode( ' ', $range_end_parts_limit ) ) );
	}

	if ( ! isset( $_POST['range_start'] ) && ! isset( $_POST['range_end'] ) ||
		'' == $_POST['range_start'] && '' == $_POST['range_end'] ) {
		$args['range_start'] = date( 'Y-m-d', time() );
		$args['range_end'] = date( 'Y-m-d', time() + WEEK_IN_SECONDS );
	}

	$calendar = $cbsb->get( 'calendar', array_filter( $args ), false );
	wp_send_json( $calendar );
}
add_action( 'wp_ajax_cbsb_get_calendar', 'cbsb_get_calendar' );
add_action( 'wp_ajax_nopriv_cbsb_get_calendar', 'cbsb_get_calendar' );

function cbsb_get_groups() {
	global $cbsb;
	$groups = $cbsb->get( 'classes', null, false );
	wp_send_json( $groups );
}
add_action( 'wp_ajax_cbsb_get_groups', 'cbsb_get_groups' );
add_action( 'wp_ajax_nopriv_cbsb_get_groups', 'cbsb_get_groups' );

function cbsb_app_connect() {
	$response = false;
	$single_use_token = get_option( 'cbsb_single_use_token' );
	if ( isset( $_POST['app_token'] ) && isset( $_POST['app_account_key'] ) && isset( $_POST['single_use_token'] ) && $_POST['single_use_token'] == $single_use_token ) {
		$response = add_option( 'cbsb_connection', array( 'token' => sanitize_text_field( $_POST['app_token'] ), 'account' => sanitize_text_field( $_POST['app_account_key'] ) ) );
		if ( $response ) {
			if ( false == get_option( 'cbsb_booking_page' ) ) {
				cbsb_create_booking_page( 'Book Now' );
			}
			update_option( 'cbsb_overview_step', 'overview' );
			delete_option( 'cbsb_single_use_token' );
		}
	}
	wp_send_json( (string) $response );
}
add_action( 'wp_ajax_cbsb_app_connect', 'cbsb_app_connect' );
add_action( 'wp_ajax_nopriv_cbsb_app_connect', 'cbsb_app_connect' );

function cbsb_settings_update() {
	if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
		global $cbsb;
		$cbsb->clear_transients();
		$response = array();

		$original_settings = cbsb_current_settings();
		$updated_settings = $original_settings;

		foreach ( $_POST['settings'] as $key => $value ) {
			$new_settings = array_map( 'sanitize_text_field', array( $_POST['settings'][ $key ]['name'] => $_POST['settings'][ $key ]['value'] ) );
			$updated_settings = wp_parse_args( $new_settings, $updated_settings );
		}

		if ( update_option( 'start_booking_settings', $updated_settings ) ) {
			$response['status'] = 'success';
			$response['message'] = 'Setting updated successfully.';
		} else {
			$response['status'] = 'error';
			$response['message'] = 'Unable to update settings.';
		}
		wp_send_json( $response );
	}
}
add_action( 'wp_ajax_cbsb_settings_update', 'cbsb_settings_update' );

function cbsb_update_booking_page() {
	if ( isset( $_POST['booking_page'] ) && is_numeric( $_POST['booking_page'] ) ) {
		if ( update_option( 'cbsb_booking_page', (int) $_POST['booking_page'] ) ) {
			$response['status'] = 'success';
			$response['message'] = 'Booking page updated successfully.';
			$response['reload'] = true;
		} else {
			$response['status'] = 'error';
			$response['message'] = 'Unable to update booking page.';
		}
		wp_send_json( $response );
	}
}
add_action( 'wp_ajax_cbsb_update_booking_page', 'cbsb_update_booking_page' );

function cbsb_content_reset() {
	if ( delete_option( 'cbsb_custom_copy' ) ) {
		$response = array(
			'status'   => 'success',
			'message'  => 'Successfully reset content.',
			'reload'   => true,
			'callback' => 'cbsb_reset_content',
		);
	} else {
		$response = array(
			'status' => 'error',
			'message' => 'Unable to reset content.'
		);
	}
	wp_send_json( $response );
}
add_action( 'wp_ajax_cbsb_content_reset', 'cbsb_content_reset' );

function cbsb_disconnect() {
	if ( isset( $_POST['confirm_disconnect'] ) && 'true' == $_POST['confirm_disconnect'] ) {
		global $cbsb;
		$cbsb->clear_transients();
		if ( delete_option( 'cbsb_connection' ) ) {
			$response = array(
				'status'  => 'success',
				'message' => 'Successfully disconnected.',
				'reload'  => true
			);
		} else {
			$response = array(
				'status' => 'error',
				'message' => 'Unable to remove connection token.'
			);
		}
		wp_send_json( $response );
	}
}
add_action( 'wp_ajax_cbsb_disconnect', 'cbsb_disconnect' );

function cbsb_array_filter_recursive( array $array, callable $callback = null ) {
    $array = is_callable( $callback ) ? array_filter( $array, $callback ) : array_filter( $array );
    foreach ( $array as &$value ) {
        if ( is_array( $value ) ) {
            $value = call_user_func( __FUNCTION__, $value, $callback );
        }
    }
    return array_filter( $array );
}

function cbsb_update_copy() {
	$copy = cbsb_get_default_copy();
	if ( isset( $_POST['content'] ) && is_array( $_POST['content'] ) ) {
		$custom_copy = cbsb_array_filter_recursive( $_POST['content'] );
		$custom_copy = cbsb_array_merge_recursive_simple( cbsb_get_custom_copy(), $custom_copy );
		$custom_copy = array_intersect_key( $custom_copy, $copy );
		foreach ( $custom_copy as $key => $content ) {
			$custom_copy[ $key ] = array_intersect_key( array_filter( $custom_copy[ $key ] ), $copy[ $key ] );
		}
		if ( update_option( 'cbsb_custom_copy', $custom_copy ) ) {
			$response = array(
				'status' => 'success',
				'message' => 'Content updated.'
			);
		} else {
			$response = array(
				'status' => 'error',
				'message' => 'Unable to update content.'
			);
		}
	} else {
		$response = array(
			'status' => 'error',
			'message' => 'Invalid content.'
		);
	}
	wp_send_json( $response );
}
add_action( 'wp_ajax_cbsb_update_copy', 'cbsb_update_copy' );

function cbsb_payment_skipped() {
	global $cbsb;
	$cbsb->get( 'charge/skipped' );
	wp_send_json( array(
		'status' => 'success',
		'message' => 'Skipped payment logged.'
	) );
}
add_action( 'wp_ajax_cbsb_payment_skipped', 'cbsb_payment_skipped' );
add_action( 'wp_ajax_nopriv_cbsb_payment_skipped', 'cbsb_payment_skipped' );