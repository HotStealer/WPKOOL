<?php
global $cbsb;
$services = $cbsb->get( 'services', null, false );
$current_settings = cbsb_current_settings();
?>
<div class="container-fluid">
	<div class="row header">
		<img src="<?php echo CBSB_BASE_URL . 'images/startbooking-logo.svg'; ?>" />
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Booking Pages', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Appointments', 'calendar-booking' ); ?></label>
						<div class="col-md-7">
							<select id="booking-page-select">
								<option><?php _e( 'Select a page', 'calendar-booking' ); ?></option>
								<?php
								$current_booking_page = (int) get_option( 'cbsb_booking_page' );
								$pages = get_pages();
								$shortcode_present = false;
								foreach ( $pages as $page ) {
									echo '<option value="' . $page->ID . '" ' . selected( $page->ID, $current_booking_page, false ) . '>' . $page->post_title . '</option>';
									if ( $current_booking_page == $page->ID ) {
										$shortcode_present = ( false === strpos( $page->post_content, '[startbooking]' ) ) ? false : true;
										$post_id = $page->ID;
									}
								}
								?>
							</select>
							<?php
							if ( $current_booking_page && ! $shortcode_present ) {
								echo '<p style="color:red;"><small>' . __( 'The selected page does not have the required "Full Flow" embed.', 'calendar-booking' ) . '<br/><a href="https://www.startbooking.com/knowledge-base/assigning-a-booking-page-in-wordpress/">' . __( 'Learn More', 'calendar-booking' ) . '</a></small></p><br/>';
							} else {
								echo '<a href="' . get_permalink( $post_id ) . '" target="_blank">' . __( 'View Page', 'calendar-booking' ) . '</a>';
							}
							?>
							<p class="tip"><?php _e( 'This is the page where we have added the <code>[startbooking]</code> shortcode. Customers will be able to book appointments from this page.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<?php if ( cbsb_get_plan() != 'free' ) { ?>
				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Classes', 'calendar-booking' ); ?></label>
						<div class="col-md-7">
							<p class="tip"><?php _e( 'To display your classes, add <code>[startbooking_classes]</code> to any post or page.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>
				<?php } ?>

			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Embed Options', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Appointments', 'calendar-booking' ); ?></label>
						<div class="col-md-7">
							<code>[startbooking]</code>
							<p class="tip"><?php _e( 'This shortcode is best used on a page or post and will provide your users with a list of your available services and allow them to book an appointment.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Quick Checkout', 'calendar-booking' ); ?></label>
						<div class="col-md-7">
							<code>[startbooking_cta services="4K59oyjEP" ]<?php _e( 'Button Text', 'calendar-booking' ); ?> [/startbooking_cta]</code>
							<p class="tip"><?php _e( 'This shortcode is best used when you want the user to go straight to selecting a date and time with a predefined service.', 'calendar-booking' ); ?></p>
							<p><strong><?php _e( 'Options' ); ?></strong></p>
							<p><code><?php _e( 'Button Text' ); ?></code> - <?php _e( 'Text that you want on the button.', 'calendar-booking' ); ?></p>
							<p><code><?php _e( 'services' ); ?></code> - <?php _e( 'ID of the service you want the user to checkout with.', 'calendar-booking' ); ?></p>
							<table>
								<tr>
									<th><?php _e( 'Service Name', 'calendar-booking' ); ?></th>
									<th style="text-align:right;">ID</th>
									<th style="text-align:right;"><?php _e( 'Actions', 'calendar-booking' ); ?></th>
								</tr>
								<?php
								$displayed_services = array();
								foreach ( $services['data']->all_services as $service ) {
									if ( ! in_array( $service->uid, $displayed_services ) ) {
										echo "<tr><td>" . $service->name . "</td><td>" . $service->uid .
											 "</td><td style='text-align:right;'>" .

											 "<a target='_blank' href='" . CBSB_APP_URL . "service/" . $service->uid . "/edit'>Edit</a>" .
											  " | <a target='_blank' href='" . CBSB_APP_URL . "service/" . $service->uid . "/'>View</a>" .

											 "</td><td></tr>";
										$displayed_services[] = $service->uid;
									}
								}
								?>
							</table>

						</div>
					</div>
				</div>

				<?php if ( cbsb_get_plan() != 'free' ) { ?>
				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Classes', 'calendar-booking' ); ?></label>
						<div class="col-md-7">
							<code>[startbooking_classes]</code>
							<p class="tip"><?php _e( 'This shortcode allows your customers to book classes from a calendar or list view with filtering and date range selection. Additional settings for the class booking flow can be found below.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>
				<?php } ?>

			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Display Options', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-3 control-label"><?php _e( 'Button Background Color', 'calendar-booking' ); ?></label>
						<div class="col-md-6">
							<div id="colorBgSelector">
								<div style="background-color: #<?php echo $current_settings['btn_bg_color']; ?>"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-3 control-label"><?php _e( 'Button Text Color', 'calendar-booking' ); ?></label>
						<div class="col-md-6">
							<div id="colorTextSelector">
								<div style="background-color: #<?php echo $current_settings['btn_txt_color']; ?>"></div>
							</div>
						</div>
						<div class="col-md-3">
							<a class="btn btn-primary pull-right" id="save_colors"><?php _e( 'Update Colors', 'calendar-booking' ); ?></a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Expedited Booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Single Service Type', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="expedited-single-service-type" type="checkbox" name="expedited-single-service-type" <?php checked( $current_settings['expedited_single_service_type'], 'true', true ); ?>/> <?php _e( 'Skip straight to the service list if there is only one service type.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Low Qty of Services', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="expedited-qty-services" type="checkbox" name="expedited-qty-services" <?php checked( $current_settings['expedited_qty_services'], 'true', true ); ?>/> <?php _e( 'Skip straight to the service list if there is less the five services.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Single Service', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="expedited-single-service" type="checkbox" name="expedited-single-service" <?php checked( $current_settings['expedited_single_service'], 'true', true ); ?>/> <?php _e( 'Skip straight to the calendar if there is only one service available.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Assign Provider', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="expedited-assign-provider" type="checkbox" name="expedited-assign-provider" <?php checked( $current_settings['automatic_provider'], 'true', true ); ?>/> <?php _e( 'Automatically assign the first available provider.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Content Customizations', 'calendar-booking' ); ?>
					<?php $content = cbsb_get_copy(); ?>
					<button class="btn btn-link pull-right" id="cbsb-content-reset">Reset</button>
				</div>

				<div class="panel-body">
					<form id="cbsb-content-form">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="service-tab" data-toggle="tab" href="#services-content" role="tab" aria-controls="services" aria-selected="true"><?php _e( 'Services', 'calendar-booking' ); ?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="appointment-tab" data-toggle="tab" href="#appointment-content" role="tab" aria-controls="appointment" aria-selected="false"><?php _e( 'Appointment', 'calendar-booking' ); ?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="details-tab" data-toggle="tab" href="#details-content" role="tab" aria-controls="details" aria-selected="false"><?php _e( 'Details', 'calendar-booking' ); ?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments-content" role="tab" aria-controls="payments" aria-selected="false"><?php _e( 'Payments', 'calendar-booking' ); ?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="success-tab" data-toggle="tab" href="#success-content" role="tab" aria-controls="success" aria-selected="false"><?php _e( 'Success', 'calendar-booking' ); ?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="general-tab" data-toggle="tab" href="#general-content" role="tab" aria-controls="general" aria-selected="false"><?php _e( 'General', 'calendar-booking' ); ?></a>
							</li>
						</ul>
						

						<div class="tab-content">
							<div class="tab-pane active" id="services-content" role="tabpanel" aria-labelledby="services-tab">
								<div class="panel-body">
									<div class="form-group col-md-8">
										<div class="row">
											<label for="content[ServiceTypesPage][headingServices]" class="col-md-6 control-label"><?php _e( 'Services Heading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ServiceTypesPage][headingServices]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ServiceTypesPage']['headingServices'] );?>" />
										</div>
										<br/>
										<div class="row">
											<label for="content[ServiceTypesPage][headingServiceType]" class="col-md-6 control-label"><?php _e( 'Service Type Heading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ServiceTypesPage][headingServiceType]" class="col-md-6"  placeholder="<?php esc_attr_e( $content['ServiceTypesPage']['headingServiceType'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[ServiceTypesPage][secondaryServiceCTA]" class="col-md-6 control-label"><?php _e( 'Additional Service', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ServiceTypesPage][secondaryServiceCTA]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ServiceTypesPage']['secondaryServiceCTA'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[ServiceTypesPage][loaderServiceType]" class="col-md-6 control-label"><?php _e( 'Loading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ServiceTypesPage][loaderServiceType]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ServiceTypesPage']['loaderServiceType'] );?>"/>
										</div>

									</div>
									<div class="form-group col-md-4">
										<img src="<?php echo CBSB_BASE_URL . 'images/screenshot-service-type.png'; ?>" style="max-width:100%" />
										<br/>
										<img src="<?php echo CBSB_BASE_URL . 'images/screenshot-services.png'; ?>" style="max-width:100%" />
									</div>
								</div>
							</div>
							<div class="tab-pane" id="appointment-content" role="tabpanel" aria-labelledby="appointment-tab">
								<div class="panel-body">
									<div class="form-group col-md-8">
										<div class="row">
											<label for="content[DateAndTimePage][heading]" class="col-md-6 control-label"><?php _e( 'Find Appointment Heading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DateAndTimePage][heading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DateAndTimePage']['heading'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DateAndTimePage][subHeading]" class="col-md-6 control-label"><?php _e( 'Sub-heading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DateAndTimePage][subHeading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DateAndTimePage']['subHeading'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DateAndTimePage][noAppointments]" class="col-md-6 control-label"><?php _e( 'No Appointments', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DateAndTimePage][noAppointments]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DateAndTimePage']['noAppointments'] );?>" />
										</div>
										<br/>
										<div class="row">
											<label for="content[DateAndTimePage][lookingAppointments]" class="col-md-6 control-label"><?php _e( 'Looking for Appointments', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DateAndTimePage][lookingAppointments]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DateAndTimePage']['lookingAppointments'] );?>"/>
										</div>

									</div>
									<div class="form-group col-md-4">
										<img src="<?php echo CBSB_BASE_URL . 'images/screenshot-appointment-select.png'; ?>" style="max-width:100%" />
									</div>
								</div>
							</div>
							<div class="tab-pane" id="details-content" role="tabpanel" aria-labelledby="details-tab">
								<div class="panel-body">
									<div class="form-group col-md-8">
										<div class="row">
											<label for="content[DetailsPage][heading]" class="col-md-6 control-label"><?php _e( 'Detail Heading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DetailsPage][heading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DetailsPage']['heading'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DetailsPage][email]" class="col-md-6 control-label"><?php _e( 'Email', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DetailsPage][email]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DetailsPage']['email'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DetailsPage][firstName]" class="col-md-6 control-label"><?php _e( 'First Name', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DetailsPage][firstName]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DetailsPage']['firstName'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DetailsPage][lastName]" class="col-md-6 control-label"><?php _e( 'Last Name', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DetailsPage][lastName]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DetailsPage']['lastName'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DetailsPage][phoneNumber]" class="col-md-6 control-label"><?php _e( 'Phone Number', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DetailsPage][phoneNumber]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DetailsPage']['phoneNumber'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[DetailsPage][cta]" class="col-md-6 control-label"><?php _e( 'Button Call To Action', 'calendar-booking' ); ?></label>
											<input type="text" name="content[DetailsPage][cta]" class="col-md-6" placeholder="<?php esc_attr_e( $content['DetailsPage']['cta'] );?>"/>
										</div>

									</div>
									<div class="form-group col-md-4">
										<img src="<?php echo CBSB_BASE_URL . 'images/screenshot-details.png'; ?>" style="max-width:100%" />
										<br/>
										<img src="<?php echo CBSB_BASE_URL . 'images/screenshot-details-profile.png'; ?>" style="max-width:100%" />
									</div>
								</div>
							</div>
							<div class="tab-pane" id="payments-content" role="tabpanel" aria-labelledby="payments-tab">
								<div class="panel-body">
									<div class="form-group col-md-8">
										<div class="row">
											<label for="content[PaymentPage][heading]" class="col-md-6 control-label"><?php _e( 'Summary', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][heading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['heading'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[PaymentPage][paymentHeading]" class="col-md-6 control-label"><?php _e( 'Payment Information', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][paymentHeading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['paymentHeading'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[PaymentPage][appointment]" class="col-md-6 control-label"><?php _e( 'Appointment', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][appointment]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['appointment'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[PaymentPage][service]" class="col-md-6 control-label"><?php _e( 'Service', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][service]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['service'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[PaymentPage][services]" class="col-md-6 control-label"><?php _e( 'Services', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][services]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['services'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[PaymentPage][totalAmountDue]" class="col-md-6 control-label"><?php _e( 'Total Amount Due', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][totalAmountDue]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['totalAmountDue'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[PaymentPage][creditCard]" class="col-md-6 control-label"><?php _e( 'Credit Card', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][creditCard]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['creditCard'] );?>"/>
										</div>
										<div class="row">
											<label for="content[PaymentPage][monthYear]" class="col-md-6 control-label"><?php _e( 'Month / Year', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][monthYear]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['monthYear'] );?>"/>
										</div>
										<div class="row">
											<label for="content[PaymentPage][cvc]" class="col-md-6 control-label"><?php _e( 'CVC', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][cvc]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['cvc'] );?>"/>
										</div>
										<div class="row">
											<label for="content[PaymentPage][postalCode]" class="col-md-6 control-label"><?php _e( 'Postal Code', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][postalCode]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['postalCode'] );?>"/>
										</div>
										<div class="row">
											<label for="content[PaymentPage][cta]" class="col-md-6 control-label"><?php _e( 'Call to Action', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][cta]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['cta'] );?>"/>
										</div>
										<div class="row">
											<label for="content[PaymentPage][loading]" class="col-md-6 control-label"><?php _e( 'Loading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[PaymentPage][loading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['PaymentPage']['loading'] );?>"/>
										</div>
									</div>
									<div class="form-group col-md-4">

									</div>
								</div>
							</div>
							<div class="tab-pane" id="success-content" role="tabpanel" aria-labelledby="success-tab">
								<div class="panel-body">
									<div class="form-group col-md-8">
										<div class="row">
											<label for="content[ThanksPage][heading]" class="col-md-6 control-label"><?php _e( 'Heading', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ThanksPage][heading]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ThanksPage']['heading'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[ThanksPage][message]" class="col-md-6 control-label"><?php _e( 'Confirmed Message', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ThanksPage][message]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ThanksPage']['message'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[ThanksPage][postMessage]" class="col-md-6 control-label"><?php _e( 'Post Detail Message', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ThanksPage][postMessage]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ThanksPage']['postMessage'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[ThanksPage][cta]" class="col-md-6 control-label"><?php _e( 'Finish Button Text', 'calendar-booking' ); ?></label>
											<input type="text" name="content[ThanksPage][cta]" class="col-md-6" placeholder="<?php esc_attr_e( $content['ThanksPage']['cta'] );?>"/>
										</div>

									</div>
									<div class="form-group col-md-4">
										<img src="<?php echo CBSB_BASE_URL . 'images/screenshot-thanks.png'; ?>" style="max-width:100%" />
									</div>
								</div>
							</div>
							<div class="tab-pane" id="general-content" role="tabpanel" aria-labelledby="general-tab">
								<div class="panel-body">
									<div class="form-group col-md-8">
										<div class="row">
											<label for="content[common][Buttons][defaultCTA]" class="col-md-6 control-label"><?php _e( 'Default Button Copy', 'calendar-booking' ); ?></label>
											<input type="text" name="content[common][Buttons][defaultCTA]" class="col-md-6" placeholder="<?php esc_attr_e( $content['common']['Buttons']['defaultCTA'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[common][ServiceBlock][price]" class="col-md-6 control-label"><?php _e( 'Price', 'calendar-booking' ); ?></label>
											<input type="text" name="content[common][ServiceBlock][price]" class="col-md-6" placeholder="<?php esc_attr_e( $content['common']['ServiceBlock']['price'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[common][ServiceBlock][duration]" class="col-md-6 control-label"><?php _e( 'Duration', 'calendar-booking' ); ?></label>
											<input type="text" name="content[common][ServiceBlock][duration]" class="col-md-6" placeholder="<?php esc_attr_e( $content['common']['ServiceBlock']['duration'] );?>"/>
										</div>
										<br/>
										<div class="row">
											<label for="content[common][ServiceType][services]" class="col-md-6 control-label"><?php _e( 'Services', 'calendar-booking' ); ?></label>
											<input type="text" name="content[common][ServiceType][services]" class="col-md-6" placeholder="<?php esc_attr_e( $content['common']['ServiceType']['services'] );?>"/>
										</div>

									</div>
									<div class="form-group col-md-4">
										
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Appointment Options', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Show Progress', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="show-progress" type="checkbox" name="show-progress" <?php checked( $current_settings['show_progress'], 'true', true ); ?>/> <?php _e( 'Show progress of booking flow above the booking steps.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Display Timezone', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><?php _e( 'Set which timezone available appointments should display.', 'calendar-booking' ); ?></p>
							<select name="appointment-use-visitor-timezone" id="appointment-use-visitor-timezone">
								<option value="true" <?php selected( $current_settings['appointment_use_visitor_timezone'], 'true' ); ?>>Visitor</option>
								<option value="false" <?php selected( $current_settings['appointment_use_visitor_timezone'], 'false' ); ?>>Account</option>
							</select>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Booking Window', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><?php _e( 'Set a limit for how far in advance a customer can book an appointment.', 'calendar-booking' ); ?></p>
							<select name="booking-window" id="booking-window">
								<option value="0" <?php selected( $current_settings['booking_window'], 0 ); ?>>No Limit</option>
								<option value="<?php echo MONTH_IN_SECONDS; ?>" <?php selected( $current_settings['booking_window'], MONTH_IN_SECONDS ); ?>>
									1 <?php _e( 'Month', 'calendar-booking' ); ?>
								</option>
								<option value="<?php echo MONTH_IN_SECONDS * 2; ?>" <?php selected( $current_settings['booking_window'], MONTH_IN_SECONDS * 2 ); ?>>2 <?php _e( 'Months', 'calendar-booking' ); ?></option>
								<option value="<?php echo MONTH_IN_SECONDS * 3; ?>" <?php selected( $current_settings['booking_window'], MONTH_IN_SECONDS * 3 ); ?>>3 <?php _e( 'Months', 'calendar-booking' ); ?></option>
								<option value="<?php echo MONTH_IN_SECONDS * 6; ?>" <?php selected( $current_settings['booking_window'], MONTH_IN_SECONDS * 6 ); ?>>6 <?php _e( 'Months', 'calendar-booking' ); ?></option>
								<option value="<?php echo YEAR_IN_SECONDS; ?>" <?php selected( $current_settings['booking_window'], YEAR_IN_SECONDS ); ?>>1 <?php _e( 'Year', 'calendar-booking' ); ?></option>
							</select>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<?php if ( cbsb_get_plan() != 'free' ) { ?>
	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Class Options', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Default View', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><?php _e( 'Select the default view for displaying classes. Users can toggle at anytime.', 'calendar-booking' ); ?></p>
							<select name="default-class-view" id="default-class-view">
								<option value="list" <?php selected( $current_settings['default_class_view'], 'list' ); ?>>List</option>
								<option value="calendar" <?php selected( $current_settings['default_class_view'], 'calendar' ); ?>>Calendar</option>
							</select>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Display Timezone', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><?php _e( 'Set which timezone class times should display.', 'calendar-booking' ); ?></p>
							<select name="group-use-visitor-timezone" id="group-use-visitor-timezone">
								<option value="true" <?php selected( $current_settings['group_use_visitor_timezone'], 'true' ); ?>>Visitor</option>
								<option value="false" <?php selected( $current_settings['group_use_visitor_timezone'], 'false' ); ?>>Account</option>
							</select>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Sold Out Classes', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="show-sold-out-classes" type="checkbox" name="show-sold-out-classes" <?php checked( $current_settings['show_sold_out_classes'], 'true', true ); ?>/> <?php _e( 'Show sold out classes when viewing classes on the booking flow.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Show Room Name', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="show-room-details" type="checkbox" name="show-room-details" <?php checked( $current_settings['show_room_details'], 'true', true ); ?>/> <?php _e( 'Show room name on detailed views.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Show Remaining Availability', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="show-remaining-availability" type="checkbox" name="show-remaining-availability" <?php checked( $current_settings['show_remaining_availability'], 'true', true ); ?>/> <?php _e( 'Show available capacity for each class.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Additional Options', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Endorse Us', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="endorse-us" type="checkbox" name="endorse-us" <?php checked( $current_settings['endorse_us'], 'true', true ); ?>/> <?php _e( 'Display a small powered by Start Booking at the end of checkout to help support Start Booking.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Additional Data', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="additional-data" type="checkbox" name="additional-data" <?php checked( $current_settings['allow_data_collection'], 'true', true ); ?>/> <?php _e( 'Empower the Start Booking team to make data driven decisions by sharing basic information with us. This does not contain any confidential or sensitive information.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Appointment Redirect', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><?php _e( 'Redirect customers to a url after they book their appointment.' ); ?></p>
							<input type="text" name="service-thank-you-destination" id="service-thank-you-destination" placeholder="https://example.com" value="<?php echo esc_url( $current_settings['service_destination'], array('http', 'https') ); ?>" />
						</div>
					</div>
				</div>
				<?php if ( cbsb_get_plan() != 'free' ) { ?>
				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Class Redirect', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><?php _e( 'Redirect customers to a url after they join a class.' ); ?></p>
							<input type="text" name="class-thank-you-destination" id="class-thank-you-destination" placeholder="https://example.com" value="<?php echo ( $current_settings['class_destination'] ); ?>" />
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Connection', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Disconnect from Start Booking', 'calendar-booking' ); ?></label>
						<div class="col-md-9">
							<a class="btn btn-secondary" id="disconnect_check"><?php _e( 'Disconnect', 'calendar-booking' ); ?></a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="row start-booking-settings">
		<div class="col col-md-8 xs-col-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php _e( 'Disable', 'calendar-booking' ); ?>
				</div>

				<div class="panel-body">
					<div class="form-group">
						<label for="name" class="col-md-2 control-label"><?php _e( 'Disable Booking', 'calendar-booking' ); ?></label>
						<div class="col-md-10">
							<p class="tip"><input id="allow-booking" type="checkbox" name="booking-title" <?php checked( $current_settings['disable_booking'], 'true', true ); ?>/> <?php _e( 'Check this if you need to temporarily disable booking on this site.', 'calendar-booking' ); ?></p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
