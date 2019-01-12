<?php
/*
Menu
*/
function cbsb_main_menu() {
	$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgODkuMSA4OC43MiI+IAogIDxkZWZzPgogICAgPHN0eWxlPgogICAgICBzdmd7CiAgICAgIAlwYWRkaW5nOiAxMCU7CiAgICAgIH0KICAgICAgLmEgewogICAgICAgIGZpbGw6IHVybCgjYSk7CiAgICAgIH0KCiAgICAgIC5iIHsKICAgICAgICBmaWxsOiB1cmwoI2IpOwogICAgICB9CiAgICA8L3N0eWxlPgogICAgPGxpbmVhckdyYWRpZW50IGlkPSJhIiB4MT0iNDAuMiIgeTE9IjQ4LjAyIiB4Mj0iNDAuMiIgeTI9Ijg2LjQ2IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CiAgICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiNmZmZmZmYiLz4KICAgIDwvbGluZWFyR3JhZGllbnQ+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImIiIHgxPSI0MC4yIiB5MT0iMzQuMjUiIHgyPSI0MC4yIiB5Mj0iLTEuMDQiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KICAgICAgPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjZmZmZmZmIi8+CiAgICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPHRpdGxlPnN0YXJ0LWJvb2tpbmctaWNvbjwvdGl0bGU+CiAgPGc+CiAgICA8cGF0aCBjbGFzcz0iYSIgZD0iTTU4LjYxLDc5LjkzaDBsLTQyLjg1LS4wOGExNS4xNCwxNS4xNCwwLDAsMSwwLTMwLjI4SDU4LjY3YTkuMSw5LjEsMCwxLDEsMCwxOC4xOWgtNDZWNjEuNzFoNDZhMywzLDAsMCwwLDMtMy4wNywzLDMsMCwwLDAtMy0zSDE1Ljc1YTkuMDksOS4wOSwwLDAsMCwwLDE4LjE4bDQyLjg1LjA4aDBhMTUuMTQsMTUuMTQsMCwwLDAsLjA2LTMwLjI4bC0xOC41NC0uMDcsMC02LjA1LDE4LjU0LjA3QTIxLjI0LDIxLjI0LDAsMCwxLDc5LjgxLDU4Ljc0YTIxLjIsMjEuMiwwLDAsMS0yMS4yLDIxLjE5WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuNiAtMS4wNykiLz4KICAgIDxwYXRoIGNsYXNzPSJiIiBkPSJNNDAuMjYsNDMuNTNsLTE4LjU0LS4wN2EyMS4yLDIxLjIsMCwwLDEsLjA3LTQyLjM5aC4wNWw0Mi44NS4wOWExNS4xNCwxNS4xNCwwLDAsMSwwLDMwLjI4SDIxLjc0YTkuMTMsOS4xMywwLDAsMS05LjEtOSw5LjEsOS4xLDAsMCwxLDkuMS05LjE2aDQ2VjE5LjNoLTQ2YTMsMywwLDAsMC0yLjE2LjksMywzLDAsMCwwLDIuMTYsNS4xOUg2NC42NmE5LjA5LDkuMDksMCwwLDAsMC0xOC4xOEwyMS44Miw3LjEyaDBhMTUuMTUsMTUuMTUsMCwwLDAtLjA1LDMwLjI5bDE4LjU0LjA3WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuNiAtMS4wNykiLz4KICA8L2c+Cjwvc3ZnPgo=';
	add_menu_page( 'Booking', 'Booking', 'manage_options', 'cbsb-overview', 'cbsb_overview_page', $icon, 61 );
}
add_action( 'admin_menu', 'cbsb_main_menu' );

function cbsb_load_content( $content, $requires_connection = true ) {
	if ( true == $requires_connection && false === get_option( 'cbsb_connection' ) ) {
		return include( CBSB_BASE_DIR . 'content/setup.php' );
	}
	if ( file_exists( CBSB_BASE_DIR . 'content/' . $content . '.php' )  ) {
		return include( CBSB_BASE_DIR . 'content/' . $content . '.php' );
	} else {
		return include( CBSB_BASE_DIR . 'content/404.php' );
	}
}

function cbsb_overview_page() {
	$cbsb_step = get_option( 'cbsb_overview_step', 'setup' );
	$cbsb_step = apply_filters( 'cbsb_overview_step', $cbsb_step );
	echo "<div id='start-booking'>";
	cbsb_load_content( $cbsb_step );
	echo "</div>";
}

function cbsb_skip_booking_step( $cbsb_step ) {
	if ( 'setup-booking-page' == $cbsb_step && false !== get_option( 'cbsb_booking_page' ) ) {
		update_option( 'cbsb_overview_step', 'overview' );
		$cbsb_step = 'overview';
	}
	return $cbsb_step;
}
add_filter( 'cbsb_overview_step', 'cbsb_skip_booking_step' );