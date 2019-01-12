<?php

function cbsb_clean_services( $services ) {
	$clean_services = new stdClass;
	if ( property_exists( $services, 'services' ) ) {
		$services = $services->services;
		$service_map = array();
		if ( is_array( $services ) ) {
			$clean_services->service_types = array();
			$clean_services->all_services = array();
			foreach ( $services as $service ) {
				if ( 'active' != $service->status || 1 != $service->schedule_online ) {
					continue;
				}
				$service->uid = $service->url_string;
				unset( $service->url_string );

				$service_map[ $service->uid ] = $service;
				$clean_services->all_services[ $service->uid ] = $service;
				foreach ( $service->types as $type ) {
					$type->uid = $type->url_string;

					if ( ! isset( $clean_services->service_types[ $type->uid ] ) ) {
						$clean_services->service_types[ $type->url_string ] = new stdClass();
					}

					$clean_services->service_types[ $type->uid ]->uid = $type->url_string;
					$clean_services->service_types[ $type->uid ]->name = $type->type;

					if ( property_exists( $clean_services->service_types[ $type->uid ], 'available_services' ) ) {
						$clean_services->service_types[ $type->uid ]->available_services[ $service->uid ] = $service;
					} else {
						$clean_services->service_types[ $type->uid ]->available_services = array();
						$clean_services->service_types[ $type->uid ]->available_services[ $service->uid ] = $service;
					}
				}
			}
			update_option( 'cbsb_service_map', $service_map );
		}
	}
	return $clean_services;
}
add_filter( 'cbsb_clean_data_services', 'cbsb_clean_services' );

function cbsb_clean_customer_validate( $customer ) {
	$clean_customer = new stdClass();
	if ( property_exists( $customer, 'valid' ) && true == $customer->valid ) {
		$clean_customer->cust_uid = ( isset( $customer->customer_url_string ) ) ? $customer->customer_url_string : null;
		$clean_customer->first_name = ( isset( $customer->first_name ) ) ? $customer->first_name : null;
		$clean_customer->last_name = ( isset( $customer->last_name ) ) ? $customer->last_name : null;
		$clean_customer->email = ( isset( $customer->email ) ) ? $customer->email : null;
		$clean_customer->phone = ( isset( $customer->cell_phone ) ) ? $customer->cell_phone : 'n/a';
	}
	return $clean_customer;
}
add_filter( 'cbsb_clean_data_customer/validate', 'cbsb_clean_customer_validate' );

function cbsb_clean_account_details( $details ) {
	if ( property_exists( $details, 'billing' ) && property_exists( $details->billing, 'subscriptions' ) ) {
		$initial_subscription = $details->billing->subscriptions[0]->plan;
		if ( strpos( strtolower( $initial_subscription ), 'business' ) !== false ) {
			$plan = 'business';
		} else if ( strpos( strtolower( $initial_subscription ), 'individual' ) !== false ) {
			$plan = 'individual';
		} else {
			$plan = 'free';
		}
		$details->plan = $plan;
		unset( $details->billing );
	}
	return $details;
}
add_filter( 'cbsb_clean_data_account/details', 'cbsb_clean_account_details' );

function cbsb_clean_openings( $openings ) {
	$account_timezone = cbsb_get_account_timezone();
	if ( is_object( $openings ) ) {
		if ( $openings->status == 'success' ) {
			$clean_appointments = array();
			$today = new DateTime("now", new DateTimeZone( $account_timezone ) );
			foreach ( $openings->bookable_appointments as $appointment ) {
				if ( $today->format('Y-m-d') == date_format( date_create( $appointment->start_time ), 'Y-m-d' ) ) {
					$start_time = strtotime( $appointment->start_time . ' ' . $account_timezone );
					$now = time();
					if ( $start_time > $now ) {
						$appointment->readable_time = date_format( date_create( $appointment->start_time ), 'g:i a' );
						$clean_appointments[ strtotime( $appointment->start_time ) ] = $appointment;
					}
				} else {
					$appointment->readable_time = date_format( date_create( $appointment->start_time ), 'g:i a' );
					$clean_appointments[ strtotime( $appointment->start_time ) ] = $appointment;
				}
			}
			$openings->bookable_appointments = array_values( $clean_appointments );
			$openings->count = count( $openings->bookable_appointments );
			return $openings;
		} else {
			$openings->bookable_appointments = array();
			return $openings;
		}
	}
}
add_filter( 'cbsb_clean_data_openings', 'cbsb_clean_openings' );

function cbsb_clean_customer_create( $customer ) {
	return $customer;
}
add_filter( 'cbsb_clean_data_customer/create', 'cbsb_clean_customer_create' );

function cbsb_clean_users( $users ) {
	$clean_users = array();
	foreach ($users as $user) {
		$clean_users[ $user->url_string ] = new stdClass();
		$clean_users[ $user->url_string ]->fullname = $user->fullname;
		$clean_users[ $user->url_string ]->url_string = $user->url_string;
	}
	return array_values( $clean_users );
}
add_filter( 'cbsb_clean_data_users', 'cbsb_clean_users' );

function cbsb_clean_services_providers( $usersServices ) {
	$clean = array();
	foreach ($usersServices as $user) {
		$clean[ $user->url_string ] = new stdClass();
		$clean[ $user->url_string ]->fullname = $user->first_name . ' ' . $user->last_name;
		$clean[ $user->url_string ]->url_string = $user->url_string;
		$clean[ $user->url_string ]->services = $user->services;
	}
	return array_values( $clean );
}
add_filter( 'cbsb_clean_data_services/providers', 'cbsb_clean_services_providers' );


function cbsb_clean_calendar( $calendar ) {
	$settings = cbsb_current_settings();
	$clean_calendar = array();
	if ( is_object( $calendar ) ) {
		if ( $calendar->message == 'Success' ) {
			$clean_classes = array();
			foreach ( $calendar->data as $class ) {
				$conditions = array(
					( true == $class->schedule_online ),
					( false == $class->cancelled ),
					( ! ( 'false' == $settings['show_remaining_availability'] && $class->meta->customers->available == 0 ) )
				);

				if ( in_array( false, $conditions ) ) {
					continue;
				}

				$duration = ( strtotime( $class->end ) - strtotime( $class->start ) ) / 60;
				$clean_class = array(
					'title'                 => $class->title,
					'start'                 => date_format( date_create( $class->start ), "Y-m-d H:i:00" ),
					'end'                   => date_format( date_create( $class->end ), "Y-m-d H:i:00" ),
					'duration'              => $duration,
					'is_all_day'            => $class->meta->is_all_day,
					'schedule_url_string'   => $class->schedule_url_string,
					'occurrence'            => $class->occurrence,
					'name'                  => $class->title,
					'price'                 => $class->meta->price,
					'class'                 => $class->meta->class,
					'user'                  => $class->meta->user,
					'room'                  => $class->meta->room,
					'availability'          => $class->meta->customers,
				);

				if ( ! isset( $clean_classes[ strtotime( $class->start ) ] ) ) {
					$clean_classes[ strtotime( $class->start ) ] = $clean_class;
				} else {
					for ( $i = 1; $i > 0; $i++ ) {
						if ( ! isset( $clean_classes[ strtotime( $class->start ) + $i ] ) ) {
							$clean_classes[ strtotime( $class->start ) + $i ] = $clean_class;
							$i = -1;
						}
					}
				}
			}
			ksort( $clean_classes );
			return array_values( $clean_classes );
		}
	}
	return array();
}
add_filter( 'cbsb_clean_data_calendar', 'cbsb_clean_calendar' );

function cbsb_clean_groups( $groups ) {
	if ( is_object( $groups ) ) {
		if ( $groups->message == 'Success' ) {
			$clean_groups = array();
			foreach ( $groups->data as $group ) {
				$clean_group = array(
					'url_string'  => $group->url_string,
					'name'        => $group->name,
					'description' => $group->description,
					'color'       => str_replace( '#', '', $group->color ),
				);
				$clean_groups[] = $clean_group;
			}
			return $clean_groups;
		}
	}
	return array();
}
add_filter( 'cbsb_clean_data_classes', 'cbsb_clean_groups' );

function cbsb_clean_appointment_create( $appointment ) {
	$clean_appointment = new stdClass();
	$clean_appointment->status = $appointment->status;

	if ( property_exists( $appointment, 'appointment' ) ) {
		$clean_appointment->appointment_uid = $appointment->appointment->url_string;
		$clean_appointment->hash_group = $appointment->appointment->hash_group;
		$clean_appointment->customer = $appointment->customer;
		$clean_appointment->assigned_user = $appointment->assigned_user;
	} else {
		$clean_appointment->error_msg = 'Unable to create appointment.';
	}
	return $clean_appointment;
}
add_filter( 'cbsb_clean_data_appointment/create', 'cbsb_clean_appointment_create' );

function cbsb_clean_charge_create( $charge ) {
	return $charge;
}
add_filter( 'cbsb_clean_data_charge/create', 'cbsb_clean_charge_create' );

function cbsb_clean_charge_capture( $charge ) {
	return $charge;
}
add_filter( 'cbsb_clean_data_charge/capture', 'cbsb_clean_charge_capture' );