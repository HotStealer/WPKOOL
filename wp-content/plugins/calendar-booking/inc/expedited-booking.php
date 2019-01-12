<?php
if ( ! class_exists( 'CBSB_Expedited_Booking' ) ) {
	class CBSB_Expedited_Booking {

		function __construct() {
			$this->hook = 'cbsb_react_fe';
			$this->actions = array( 'qty_services', 'single_service_type', 'single_service' );
			$this->settings = cbsb_current_settings();
			$this->hooks();
		}
		
		function hooks() {
			foreach ( $this->actions as $action ) {
				if ( 'true' == $this->check_setting( 'expedited_' . $action ) ) {
					add_filter( $this->hook, array( $this, $action ), 10, 1 );
				}
			}
			add_filter( $this->hook, array( $this, 'select_single_service' ), 15, 1 );
		}

		function services() {
			global $cbsb;
			return $cbsb->get( 'services', null, false );
		}

		function check_setting( $name ) {
			if ( isset( $this->settings[ $name ] ) ) {
				$response = $this->settings[ $name ];
			} else {
				$response = null;
			}
			return $response;
		}

		function select_single_service( $wp_data ) {
			$services = $this->services();
			if ( isset( $services['status'] ) && 'success' == $services['status'] ) {
				if ( isset( $wp_data['initialState'] ) && isset( $services['data'] ) &&	property_exists( $services['data'], 'all_services' ) ) {
					if ( count( $services['data']->all_services ) == 1 ) {
						$wp_data['initialState']->service_types = $services['data']->service_types;
						$wp_data['initialState']->available_services = $services['data']->all_services;
						$wp_data['initialState']->all_services = $services['data']->all_services;
						$wp_data['initialState']->service_names = array();
						$key = key( $services['data']->all_services );
						$wp_data['initialState']->total_duration = $services['data']->all_services[ $key ]->duration;
						$wp_data['initialState']->service_names[] = $services['data']->all_services[ $key ]->name;
						$wp_data['initialState']->services[] = $services['data']->all_services[ $key ]->uid;
						$wp_data['initialState']->default_cart = array( $services['data']->all_services[ $key ] );
					}
				}
			}
			return $wp_data;
		}

		function single_service( $wp_data ) {
			$services = $this->services();
			if ( isset( $services['status'] ) && 'success' == $services['status'] ) {
				if ( isset( $wp_data['initialState'] ) && isset( $services['data'] ) &&	property_exists( $services['data'], 'all_services' ) ) {
					if ( count( $services['data']->all_services ) == 1 ) {
						$wp_data['skipSteps'][] = 'services';
					}
				}
			}
			return $wp_data;
		}

		function single_service_type( $wp_data ) {
			$services = $this->services();
			if ( isset( $services['status'] ) && 'success' == $services['status'] ) {
				if ( isset( $wp_data['initialState'] ) && isset( $services['data'] ) &&	property_exists( $services['data'], 'all_services' ) && count( $services['data']->service_types ) == 1 ) {
					$wp_data['initialState']->service_types = array( 'all' => $services['data']->all_services );
					$wp_data['initialState']->available_services = (array) $services['data']->all_services;
					$wp_data['initialState']->all_services = (array) $services['data']->all_services;
					$wp_data['initialState']->serviceTypeView = key( $services['data']->service_types );
					$wp_data['initialState']->typeView = key( $services['data']->service_types );
					$wp_data['initialState']->overrideView = 'all';
				}
			}
			return $wp_data;
		}

		function qty_services( $wp_data ) {
			$services = $this->services();

			if ( isset( $services['status'] ) && 'success' == $services['status'] ) {
				if ( isset( $wp_data['initialState'] ) && isset( $services['data'] ) &&	property_exists( $services['data'], 'all_services' ) && count( $services['data']->all_services ) < 5 ) {
					$wp_data['initialState']->service_types = array( 'all' => $services['data']->all_services );
					$wp_data['initialState']->available_services = (array) $services['data']->all_services;
					$wp_data['initialState']->all_services = (array) $services['data']->all_services;
					$wp_data['initialState']->serviceTypeView = 'all';
					$wp_data['initialState']->overrideView = 'all';
				}
			}
			return $wp_data;
		}
	}
	new CBSB_Expedited_Booking;
}
