<div class="container-fluid row">
	<div id="booking-page" class="col-md-5 col-md-offset-3">
		<h1 class="text-center onboarding-heading"><?php _e( 'Connect to StartBooking.com', 'calendar-booking' ); ?></h1>
		<div class="panel">
			<div class="text-center">
				<p class="intro"><?php _e( 'Do you want us to create a booking page for you?', 'calendar-booking' ); ?></p>
			</div>
			<form method="POST" id="setup-page">
				<div class="form-group">
					<div class="row">
						<div class="col-md-2 col-md-offset-1">
							<label for="booking-title" class="col-md-2 col-md-offset-1 control-label"><?php _e( 'Page Title', 'calendar-booking' ); ?></label>
						</div>
						<div class="col-md-7">
							<input type="text" class="form-control" name="booking-title" value="<?php _e( 'Book Now', 'calendar-booking' ); ?>" />
						</div>
					</div>
				</div>
				<div class="form-group" style="margin-top:25px;">
					<div class="text-center onboarding-cta-row">
						<a class="no-action" style="margin-right: 10px;"><?php _e( 'No, I\'ll do it later', 'calendar-booking' ); ?></a>
						<input class="btn btn-primary btn-medium" type="submit" value="<?php _e( 'Yes, create page', 'calendar-booking' ); ?>" />
					</div>
				</div>
			</form>
		</div>
	</div>
</div>