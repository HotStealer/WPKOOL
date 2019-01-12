<?php
/*
Manage/Load CSS/JS
*/

function cbsb_admin_main_js() {
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) ) {
		wp_enqueue_style( 'cbsb-admin-bootstrap-css', CBSB_BASE_URL . 'css/bootstrap.min.css' );
		wp_enqueue_style( 'cbsb-admin-main-css', CBSB_BASE_URL . 'css/main.css' );
		wp_enqueue_style( 'cbsb-admin-colorpicker-style', CBSB_BASE_URL . 'css/colorpicker.css' );
		wp_enqueue_script( 'cbsb-admin-bootstrap-js', CBSB_BASE_URL . 'js/bootstrap.min.js', 'jquery' );
		wp_enqueue_script( 'cbsb-admin-main-js', CBSB_BASE_URL . 'js/main.js', 'jquery' );
		wp_enqueue_script( 'cbsb-admin-colorpicker', CBSB_BASE_URL . 'js/colorpicker.js', 'jquery' );
	}
}
add_action( 'admin_init', 'cbsb_admin_main_js' );

function cbsb_fe_script() {
	global $cbsb;
	wp_register_script( 'cbsb-react-js', CBSB_BASE_URL . 'js/main.af9b22ea.js' );

	$initial_state =  new stdClass();
	$initial_state->step = 'services';
	$initial_state->default_cart = array();

	$settings = cbsb_current_settings();

	$initial_classes_state = new stdClass();
	$initial_classes_state->groupFilter = new stdClass();
	$initial_classes_state->cart = new stdClass();
	$initial_classes_state->customers = array();
	$initial_classes_state->step = ( $settings['default_class_view'] == 'list' ) ? 1 : 2;

	if (is_user_logged_in() && current_user_can( 'administrator' ) ) {
		$isAdmin = 'true';
	} else {
		$isAdmin = null;
	}

	$wp_data = array(
		'baseUrl'      => admin_url( 'admin-ajax.php' ),
		'endPoints'    => array(
			'processAppointment'    => 'cbsb_proccess_appointment',
			'getServices'           => 'cbsb_get_services',
			'getServiceDates'       => 'cbsb_get_service_dates',
			'getAvailabilityByDate' => 'cbsb_get_availablility_by_date',
		),
		'initialState'        => $initial_state,
		'initialClassesState' => $initial_classes_state,
		'settings'     => $settings,
		'mixpanelKey'  => 'eb7a78544eed4e6a20e481834df14d18',
		'appointmentStepOrder' => array(
			50  => 'services',
			100 => 'provider',
			150 => 'time',
			200 => 'details',
			250 => 'confirmation'
		),
		'skipSteps'    => array(),
		'isAdmin'   => $isAdmin
	);

	if ( $settings['automatic_provider'] == 'true' ) {
		$wp_data['skipSteps'][] = 'provider';
	}

	$wp_data = apply_filters( 'cbsb_react_fe', $wp_data );

	$wp_data['appointmentStepOrder'] = array_values( $wp_data['appointmentStepOrder'] );
	$wp_data['skipSteps'] = array_unique( $wp_data['skipSteps'] );
	$wp_data['progressSteps'] = array_values( array_diff( $wp_data['appointmentStepOrder'], $wp_data['skipSteps'] ) );

	wp_localize_script( 'cbsb-react-js', 'cbsbData', $wp_data );

	$details = $cbsb->get( 'account/details' );

	if ( 'success' == $details['status'] ) {
		$details = $details['data'];
	}

	if (property_exists( $details, 'payments' ) && property_exists( $details->payments, 'requires_payment_to_book' ) ) {
		$requires_payment = $details->payments->requires_payment_to_book;
	} else {
		$requires_payment = false;
	}

	if ( $requires_payment ) {
		wp_enqueue_script( 'cbsb-stripe-v3', 'https://js.stripe.com/v3/' );
	}
	wp_enqueue_script( 'cbsb-react-js' );
}

function cbsb_fe_styles() {
	wp_enqueue_style( 'start-booking-react', CBSB_BASE_URL . 'css/main.82da4d21.css' );

	wp_enqueue_style( 'startbooking-flow', CBSB_BASE_URL . 'css/booking-flow-layout.css' );
	$settings = cbsb_current_settings();
	$background_brightness = cbsb_get_brightness( $settings['btn_bg_color'] );
	if ($background_brightness < 130 ){
		$default_text = '#ffffff';
	} else {
		$default_text = '#5e5e5e';
	}
	echo '<style type="text/css">
		#startbooking-flow .DayPicker-Day--today{
			color: #' . $settings['btn_bg_color'] . ';
			background-color:#fcf8e3;
		}
		#startbooking-flow .DayPicker-Day--selected{
			color: ' . $default_text . ';
		}
		$startbooking-flow .DayPicker-Day--disabled {
			background-color: rgba(128,128,128,0.5);
			opacity: 0.5;
		}
		#startbooking-flow .DayPicker-Day--selected,
		#startbooking-flow .sb-button-wrap .sb-primary-action button,
		#startbooking-flow button.sb-styled-button,
		.sb-primary-action button {
			background-color: #' . $settings['btn_bg_color'] . ';
			color: #' . $settings['btn_txt_color'] . ';
			border: 0px;
		}
		#startbooking-flow .sb-button-wrap .sb-primary-action button:hover,
		#startbooking-flow button.sb-styled-button:hover,
		.sb-primary-action button:hover{
			opacity: .75;
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-icon {
			border-color: #' . $settings['btn_bg_color'] . ';
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-icon>.rc-steps-icon {
			color: #' . $settings['btn_bg_color'] . ';
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-icon>.rc-steps-icon .rc-steps-icon-dot {
			background: #' . $settings['btn_bg_color'] . ';
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-title:after,
		#startbooking-flow .rc-steps-item-process .rc-steps-item-icon > .rc-steps-icon .rc-steps-icon-dot,
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-tail:after {
			background-color: #' . $settings['btn_bg_color'] . ';
		}
	</style>' ."\r\n";
}
