<?php 

function cbsb_sc_flow() {
	add_action( 'wp_footer', 'cbsb_fe_styles' );
	add_action( 'wp_footer', 'cbsb_fe_script' );
	$markup = '
	<div id="startbooking-flow">
		<div id="startbooking-appointment-flow">
			' . cdsb_get_application_loader() . '
		</div>
	</div>';
	return $markup;
	
}
add_shortcode( 'startbooking', 'cbsb_sc_flow' );

function cbsb_sc_book_items( $atts, $content = 'Book Now' ) {
	$booking_page_id = get_option( 'cbsb_booking_page' );

	if ( false === get_option( 'cbsb_connection' ) ) {
		return '<p>Unable to display quick book link because StartBooking is not connected.';
	}

	if ( isset( $_GET['in_page_book'] ) && $_GET['in_page_book'] ) {
		return cbsb_sc_flow();
	}

	$default_atts = array(
		'services' => array(),
	);
	if ( isset( $atts['services'] ) ) {
		$atts['services'] = explode( ',', $atts['services'] );
	}

	$atts = wp_parse_args( $atts, $default_atts );

	if ( is_numeric( $booking_page_id ) ) {
		$booking_url = get_permalink( $booking_page_id );
		$href = $booking_url . '?' . http_build_query( array('add_service' => $atts['services'] ) );
	} else {
		$args = array(
			'in_page_book' => true
		);
		$booking_url = get_permalink( get_the_ID() );
		$booking_url = add_query_arg( $args, $booking_url );
		$href = $booking_url . '?' . http_build_query( array('add_service' => $atts['services'] ) ) . '#appointment-page';
	}
	return "<a href=" . $href . ">" . $content . "</a>";
}
add_shortcode( 'startbooking_cta', 'cbsb_sc_book_items' );

function cbsb_sc_class_flow($atts = array(), $content = null) {
	add_action( 'wp_footer', 'cbsb_fe_styles' );
	add_action( 'wp_footer', 'cbsb_fe_script' );
	$markup = '
	<div id="startbooking-flow">
		<div id="startbooking-class-flow">
			' . cdsb_get_application_loader() . '
		</div>
	</div>';
	return $markup;
}
add_shortcode( 'startbooking_classes', 'cbsb_sc_class_flow' );