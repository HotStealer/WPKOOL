<?php
if ( ! class_exists( 'CBSB_Api' ) ) {
	class CBSB_Api {

		function __construct() {
			global $wp_version;
			$this->api_base = CBSB_APP_URL . 'api/';
			$this->api_version = 'v1';
			$this->api = $this->api_base . $this->api_version . '/';
			$this->cache_prefix = 'cbsb_api_cache_';
			$this->connection = get_option( 'cbsb_connection' );
			$this->runtime_cache = array();
			$this->timing = null;
			$this->default_args = array(
				'user-agent'  => 'WP:BK/' . $wp_version . ':' . CBSB_VERSION . '; ' . home_url(),
				'blocking'    => true,
				'headers'     => array(
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer ' . $this->connection['token'],
					'Account'       => $this->connection['account'],
					'Content-Type'  => 'application/json',
				),
				'timeout'     => 10,
			);
		}
		private function get_key() {
			$args = func_get_args();
			return md5( serialize( $args ) );
		}

		private function format_response( $status, $value, $key = null ) {
			if ( 'success' === $status ) {
				$response = array(
					'status' => 'success',
					'data'   => $value,
					'timing' => $this->timing
				);
			} else {
				$response = array(
					'status' => 'error',
					'message' => $value,
					'timing' => $this->timing
				);
			}
			if ( ! is_null( $key ) && 'success' == $status ) {
				$this->runtime_cache[ $key ] = $response;
			}
			return $response;
		}

		public function clear_transients() {
			global $wpdb;
		    $sql = "SELECT `option_name` AS `name`
		            FROM  $wpdb->options
		            WHERE `option_name` LIKE '%transient_%'
		            ORDER BY `option_name`";
		    $options = $wpdb->get_results( $sql );
			foreach ( $options as $option ) {
				if ( false !== strpos( $option->name, $this->cache_prefix ) ) {
					delete_option( $option->name );
				}
			}
		}

		public function post( $action, $payload ) {
			if ( isset( $this->runtime_cache[ $this->get_key( 'post', $action, $payload ) ] ) ) {
				return $this->runtime_cache[ $this->get_key( 'post', $action, $payload ) ];
			}
			$additional_args = array( 'body' => json_encode( $payload ) );
			$args = wp_parse_args( $additional_args, $this->default_args );
			$endpoint = $this->api . $action . '/';
			$start_timing = time();
			$response = wp_remote_post( $endpoint, $args );
			$this->timing = time() - $start_timing;
			if ( ! is_wp_error( $response ) ) {
				$data = $response['body'];
				$data = json_decode( $data );
				$data = apply_filters( 'cbsb_clean_data_' . $action, $data );
				return $this->format_response( 'success', $data, $this->get_key( 'post', $action, $payload ) );
			} else {
				return $this->format_response( 'error', 'Unable to communicate with service API.' );
			}
		}

		public function get( $action, $payload = null, $use_cache = 300 ) {
			if ( isset( $this->runtime_cache[ $this->get_key( 'get', $action, $payload ) ] ) ) {
				return $this->runtime_cache[ $this->get_key( 'get', $action, $payload ) ];
			}
			$cache = $this->get_request_cache( md5( $action . serialize( $payload ) ) );
			if ( false !== $cache && $use_cache ) {
				return $this->format_response( 'success', $cache, $this->get_key( 'get', $action, $payload ) );
			} else {
				$additional_args = array( 'body' => $payload, 'timeout' => 60 );
				$args = wp_parse_args( $additional_args, $this->default_args );

				$endpoint = $this->api . $action . '/';
				if ( null !== $payload ) {
					$endpoint = $endpoint . '?' . http_build_query( $payload );
				}

				$start_timing = time();
				$response = wp_remote_get( $endpoint, $args );
				$this->timing = time() - $start_timing;

				if ( ! is_wp_error( $response ) ) {
					if ( isset( $response['body'] ) ) {
						$data = $response['body'];
						$data = json_decode( $data );
						$data = apply_filters( 'cbsb_clean_data_' . $action, $data );
						if ( $use_cache ) {
							$key = md5( $action . serialize( $payload ) );
							$this->set_request_cache( $key, $data, $use_cache );
						}
						return $this->format_response( 'success', $data, $this->get_key( 'post', $action, $payload ) );
					} else {
						return $this->format_response( 'error', 'Response had no body.' );
					}
				} else {
					return $this->format_response( 'error', 'Unable to communicate with service API.' );
				}
			}
		}

		public function get_request_cache( $key ) {
			return get_transient( $this->cache_prefix . $key );
		}

		public function set_request_cache( $key, $data, $duration = HOUR_IN_SECONDS ) {
			return set_transient( $this->cache_prefix . $key, $data, $duration );
		}
	}					
	$cbsb = new CBSB_Api;
}
