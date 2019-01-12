<?php
$user = wp_get_current_user();
$single_use_token = wp_generate_password();
update_option( 'cbsb_single_use_token', $single_use_token );
?>

<div class="container-fluid row">
	<div id="welcome" class="col-md-8 col-md-offset-2 col-sm-6 col-sm-offset-3">
		<div class="row header text-center">
			<img src="<?php echo CBSB_BASE_URL . 'images/startbooking-logo.svg'; ?>" />
		</div>
		<br/>
		<div class="panel panel-default">
			<p class="intro"><?php _e( 'Please connect to <a href="https://www.startbooking.com">Start Booking</a> or create a new account today. <br />This process will connect the Start Booking App with your website for amazing online booking.', 'calendar-booking' ); ?></p>
			<div class="text-center onboarding-cta-row">
				<form method="GET" action="<?php echo CBSB_APP_URL; ?>register">
					<button type="submit" class="btn btn-primary btn-large btn-external"><?php _e( 'Get Started', 'calendar-booking' ); ?> <span class="dashicons dashicons-external"></span></button>
					<span class="double-cta-or"><?php _e( 'Or', 'calendar-booking' ); ?></span>
					<a href="#" class="btn btn-secondary btn-large"><?php _e( 'Connect My Account', 'calendar-booking' ); ?></a>
					<input type="hidden" name="first_name" value="<?php esc_attr_e( $user->user_firstname ); ?>"/>
					<input type="hidden" name="last_name" value="<?php esc_attr_e( $user->user_lastname ); ?>"/>
					<input type="hidden" name="email_name" value="<?php esc_attr_e( $user->user_email ); ?>"/>
					<input type="hidden" name="single_use_token" value="<?php esc_attr_e( $single_use_token ); ?>"/>
					<input type="hidden" name="utm_source" value="wp_plugin" />
					<input type="hidden" name="utm_medium" value="start_free_trial_button" />
					<input type="hidden" name="utm_campaign" value="free_trial" />
					<input type="hidden" name="notification_endpoint" value="<?php esc_attr_e( admin_url( 'admin-ajax.php' ) ); ?>" />
				</form>
			</div>
		</div>
	</div>

	<div id="connect" class="col-md-6 col-md-offset-3">
		<div class="row header text-center">
			<img src="<?php echo CBSB_BASE_URL . 'images/startbooking-logo.svg'; ?>" />
		</div>
		<br/>
		<div class="panel">
			<p class="intro spacing-sides"><?php _e( 'Enter your <a href="https://www.startbooking.com">Start Booking</a> email and password and we will automatically connect your WordPress site with the Start Booking App.', 'calendar-booking' ); ?></p>

			<form method="POST" id="setup-key">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3 col-md-offset-1">
							<label for="email" class="col-md-2 col-md-offset-1 control-label"><?php _e( 'Email', 'calendar-booking' ); ?></label>
						</div>
						<div class="col-md-7">
							<input type="text" name="email" class="form-control" placeholder="email" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-3 col-md-offset-1">
							<label for="password" class="col-md-2 col-md-offset-1 control-label"><?php _e( 'Password', 'calendar-booking' ); ?></label>
						</div>
						<div class="col-md-7">
							<input type="password" class="form-control" name="password" placeholder="password" />
						</div>
					</div>
				</div>

				<div class="form-group" id="multi-account">
					<div class="row">
						<div class="col-md-3 col-md-offset-1">
							<label for="account" class="col-md-2 col-md-offset-1 control-label"><?php _e( 'Account', 'calendar-booking' ); ?></label>
						</div>
						<div class="col-md-7">
							<select name="account"></select>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="text-center onboarding-cta-row">
						<a href="<?php echo esc_url( add_query_arg( array( 'page'=> 'cbsb-overview' ), admin_url( 'admin.php' ) ) ); ?>" class="btn btn-text btn-large"><?php _e( 'Back', 'calendar-booking' ); ?></a>
						<button class="btn btn-primary btn-large" type="submit"><?php _e( 'Connect', 'calendar-booking' ); ?></button>
					</div>
				</div>
				<div class="text-center spacing-bottom">
					<?php
						$url_args = array(
							'utm_source'   => 'wp_plugin',
							'utm_medium'   => 'get_started_link',
							'utm_campaign' => 'free_trial'
						);
					?>
					<small><?php _e( 'Don\'t have a StartBooking.com account?', 'calendar-booking' ); ?> <a href="<?php echo add_query_arg( $url_args, 'https://www.startbooking.com/register' ); ?>"><?php _e( 'Get Started', 'calendar-booking' ); ?></a></small>
				</div>
			</form>

		</div>
	</div>
</div>
