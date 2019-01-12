<?php

class StartBooking_Hours_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 
			'description' => __( 'Start Booking Location Hours', 'calendar-booking' ),
		);
		parent::__construct( false, __( 'Start Booking Location Hours', 'calendar-booking' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		global $cbsb;
		$details = $cbsb->get( 'account/details' );

		echo $args['before_widget'];
		echo $args['before_title'] . $instance['title'] . $args['after_title'];
		if ( 'success' == $details['status'] && isset( $details['data'] ) && property_exists( $details['data'], 'location_hours' ) ) {
			$hours = $details['data']->location_hours;

			foreach ( $hours as $hour ) {
				echo '<div class="hour-row" style="border-bottom:1px solid #ccc;padding: 3px 10px; font-size: 1.2em;text-align:right;">';
				echo '<span style="float:left;">' . $hour->day . '</span>';
				if ( 'open' == $hour->day_type ) {
					echo date('g:ia', strtotime( $hour->open_time ) ) . ' - ' . date('g:ia', strtotime( $hour->close_time ) );
				} else {
					_e( 'Closed', 'calendar-booking' );
				}
				echo '</div>';
			}
		}
		echo $args['after_widget'];
	}

	function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? $instance['title'] : null;
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title', 'calendar-booking' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><?php _e( 'Location hours are loaded from your account profile at StartBooking.com.', 'calendar-booking' ); ?> <a href="<?php esc_attr_e( CBSB_APP_URL . 'account' ); ?>"><?php _e( 'Manage Hours', 'calendar-booking' ); ?></a></p>
		<?php
	}
}

class StartBooking_Address_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'description' => __( 'Start Booking Location Address', 'calendar-booking' ),
		);
		parent::__construct( false, __( 'Start Booking Location Address', 'calendar-booking' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		global $cbsb;
		$details = $cbsb->get( 'account/details' );
		echo $args['before_widget'];
		echo $args['before_title'] . $instance['title'] . $args['after_title'];
		if ( 'success' == $details['status'] && isset( $details['data'] ) && property_exists( $details['data'], 'location' ) ) {
			$address = $details['data']->location;
			$full_address = $address->street_1 . ' ' . $address->street_2 . ' ' . $address->city . ' ' . $address->state . ' ' . $address->zip;
			$map_settings = array(
				'center'  => $full_address,
				'zoom'    => $instance['zoom'] + 13,
				'size'    => '400x400',
				'maptype' => $instance['type'],
				'markers' => 'color:' . $instance['marker'] . '|label:' . $details['data']->account . '|' . $full_address,
				'key'     => 'AIzaSyByjPpi_OHvEMK4cmRddeYPFaj_hhynufE',
			);

			$img_url = add_query_arg( $map_settings, 'https://maps.googleapis.com/maps/api/staticmap' );
			echo '<a href="https://www.google.com/maps/place/' . urlencode( $full_address ) . '" target="_blank">';
			echo '<img src="' . $img_url . '" alt="' . $details['data']->account . ' Address" style="width:100%;max-width:400px;"/>';
			echo '</a>';
			echo '<div style="text-align:center;">';
			echo '<a href="https://www.google.com/maps/place/' . urlencode( $full_address ) . '" target="_blank">';
			echo ( ! is_null( $address->street_1 ) ) ? $address->street_1 . '<br/>' : '';
			echo ( ! is_null( $address->street_2 ) ) ? $address->street_2 . '<br/>' : '';
			echo $address->city . ', ' . strtoupper( $address->state ) . ' ' . $address->zip . '<br/>';
			echo '</a>';
			echo '</div>';
		}
		echo $args['after_widget'];
	}

	function form( $instance ) {
		$defaults = array(
			'title'  => null,
			'marker' => 'red',
			'zoom'   => 4,
			'type'   => 'roadmap',
		);

		$instance = wp_parse_args( $instance, $defaults );

		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title', 'calendar-booking' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'type' ); ?>"><?php _e( 'Map Type', 'calendar-booking' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'type' ); ?>">
				<option value="roadmap" <?php selected( 'roadmap', $instance['type'], true ); ?>><?php _e( 'Road Map', 'calendar-booking' ); ?></option>
				<option value="satellite" <?php selected( 'satellite', $instance['type'], true ); ?>><?php _e( 'Satellite', 'calendar-booking' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'zoom' ); ?>"><?php _e( 'Map Zoom', 'calendar-booking' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'zoom' ); ?>">
				<option value="0" <?php selected( '0', $instance['zoom'], true ); ?>><?php _e( 'No Zoom', 'calendar-booking' ); ?></option>
				<option value="1" <?php selected( '1', $instance['zoom'], true ); ?>><?php _e( 'Level 1', 'calendar-booking' ); ?></option>
				<option value="2" <?php selected( '2', $instance['zoom'], true ); ?>><?php _e( 'Level 2', 'calendar-booking' ); ?></option>
				<option value="3" <?php selected( '3', $instance['zoom'], true ); ?>><?php _e( 'Level 3', 'calendar-booking' ); ?></option>
				<option value="4" <?php selected( '4', $instance['zoom'], true ); ?>><?php _e( 'Level 4', 'calendar-booking' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'marker' ); ?>"><?php _e( 'Marker Color', 'calendar-booking' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'marker' ); ?>">
				<option value="red" <?php selected( 'red', $instance['marker'], true ); ?>><?php _e( 'Red', 'calendar-booking' ); ?></option>
				<option value="blue" <?php selected( 'blue', $instance['marker'], true ); ?>><?php _e( 'Blue', 'calendar-booking' ); ?></option>
				<option value="green" <?php selected( 'green', $instance['marker'], true ); ?>><?php _e( 'Green', 'calendar-booking' ); ?></option>
			</select>
		</p>

		<p><?php _e( 'Location address is loaded from your account profile at StartBooking.com.', 'calendar-booking' ); ?> <a href="<?php esc_attr_e( CBSB_APP_URL . 'account' ); ?>"><?php _e( 'Update Address', 'calendar-booking' ); ?></a></p>
		<?php
	}
}

function sb_register_widgets() {
	register_widget( 'StartBooking_Hours_Widget' );
	register_widget( 'StartBooking_Address_Widget' );
}

add_action( 'widgets_init', 'sb_register_widgets' );

