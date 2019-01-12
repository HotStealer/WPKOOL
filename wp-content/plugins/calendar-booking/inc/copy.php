<?php

function cbsb_get_default_copy() {
	$copy = array(
		'DateAndTimePage' => array(
			'heading'             => __( 'Select a Date and Time', 'calendar-booking' ),
			'subHeading'          => __( 'Available Times for', 'calendar-booking' ),
			'noAppointments'      => __( 'No appointments available on selected day.', 'calendar-booking' ),
			'lookingAppointments' => __( 'Looking for available appointments.', 'calendar-booking' ),
			'validation'          => array(
				'datePast'   => __( 'Selected date is in the past.', 'calendar-booking' ),
				'selectDate' => __( 'Please select a date and time.', 'calendar-booking' ),
			)
		),
		'DetailsPage' => array(
			'heading'       => __( 'Your Details', 'calendar-booking' ),
			'secureProfile' => __( 'Secure Profile Loaded from StartBooking.com', 'calendar-booking' ),
			'email'         => __( 'Email', 'calendar-booking' ),
			'firstName'     => __( 'First Name', 'calendar-booking' ),
			'lastName'      => __( 'Last Name', 'calendar-booking' ),
			'phoneNumber'   => __( 'Phone Number', 'calendar-booking' ),
			'cta'           => __( 'Continue', 'calendar-booking' ),
		),
		'ReviewPage' => array(
			'heading'  => __( 'Review & Confirm', 'calendar-booking' ),
			'name'     => __( 'Name', 'calendar-booking' ),
			'date'     => __( 'Date', 'calendar-booking' ),
			'time'     => __( 'Time', 'calendar-booking' ),
			'duration' => __( 'Duration', 'calendar-booking' ),
			'service'  => __( 'Service(s)', 'calendar-booking' ),
			'cta'      => __( 'Confirm Appointment', 'calendar-booking' ),
		),
		'ServiceTypesPage' => array(
			'headingServices'     => __( 'Select Services', 'calendar-booking' ),
			'secondaryServiceCTA' => __( 'Select Another Service', 'calendar-booking' ),
			'headingServiceType'  => __( 'Select a Service Type', 'calendar-booking' ),
			'loaderServiceType'   => __( 'Loading service types...', 'calendar-booking' ),
			'validation'          => array(
				'selectService'   => __( 'Please select a service to continue.', 'calendar-booking' ),
			),
		),
		'PaymentPage' => array(
			'heading'        => __( 'Summary', 'calendar-booking' ),
			'paymentHeading' => __( 'Payment Information', 'calendar-booking' ),
			'appointment'    => __( 'Appointment', 'calendar-booking' ),
			'service'        => __( 'Service', 'calendar-booking' ),
			'services'       => __( 'Services', 'calendar-booking' ),
			'totalAmountDue' => __( 'Total Amount Due', 'calendar-booking' ),
			'creditCard'     => __( 'Credit Card', 'calendar-booking' ),
			'monthYear'      => __( 'Month / Year', 'calendar-booking' ),
			'cvc'            => __( 'CVC', 'calendar-booking' ),
			'postalCode'     => __( 'Postal Code', 'calendar-booking' ),
			'cta'            => __( 'Pay Now', 'calendar-booking' ),
			'loading'        => __( 'Loading Secure Checkout', 'calendar-booking' ),
		),
		'ThanksPage' => array(
			'heading'     => __( 'Thank You!', 'calendar-booking' ),
			'message'     => __( 'We have confirmed your appointment on ', 'calendar-booking' ),
			'customer'    => __( 'Customer', 'calendar-booking' ),
			'appointment' => __( 'Appointment', 'calendar-booking' ),
			'address'     => __( 'Address', 'calendar-booking' ),
			'postMessage' => __( 'We look forward to seeing you!', 'calendar-booking' ),
			'cta'         => __( 'Done', 'calendar-booking' ),
		),
		'UnavailablePage' => array(
			'heading' => __( 'Booking Currently Unavailable', 'calendar-booking' ),
			'message' => __( 'Booking appointments is currently unavailable. Please check again later.', 'calendar-booking' ),
		),
		'common' => array(
			'Buttons' => array(
				'defaultCTA' => __( 'Continue', 'calendar-booking' ),
			),
			'ServiceBlock' => array(
				'price'    => __( 'Price', 'calendar-booking' ),
				'duration' => __( 'Duration', 'calendar-booking' ),
			),
			'ServiceType' => array(
				'services' => __( 'services', 'calendar-booking' ),
			)
		),
		'progress' => array(
			'services'    => __( 'Services', 'calendar-booking' ),
			'appointment' => __( 'Appointment', 'calendar-booking' ),
			'time'        => __( 'Time', 'calendar-booking' ),
			'details'     => __( 'Details', 'calendar-booking' ),
			'booked'      => __( 'Booked', 'calendar-booking' ),
		),
	);
	return cbsb_additional_step_content( $copy );
}

function cbsb_get_custom_copy() {
	return get_option( 'cbsb_custom_copy', array() );
}

function cbsb_get_copy() {
	$default_copy = cbsb_get_default_copy();
	$custom_copy = cbsb_get_custom_copy();
	$copy = cbsb_array_merge_recursive_simple( $default_copy, $custom_copy );
	return $copy;
}

function cbsb_additional_step_content( $copy ) {
	$copy['hooks'] = array();
	$locations = array( 'outside_before', 'inside_before', 'inside_after', 'outside_after' );
	$steps = array( 'services', 'service_types', 'appointment', 'details', 'thanks', 'loader' );
	foreach ( $steps as $step ) {
		foreach ( $locations as $location ) {
			$copy['hooks'][$step . '_' . $location] = apply_filters( 'cbsb_' . $step . '_' . $location, '' );
		}
	}
	return $copy;
}
