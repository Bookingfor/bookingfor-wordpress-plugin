<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$dateCheckin = DateTime::createFromFormat('d/m/Y', BFCHelper::parseJsonDate($order->CheckIn));
	$dateCheckout = DateTime::createFromFormat('d/m/Y', BFCHelper::parseJsonDate($order->CheckOut));
	$order_resource_summary = json_decode($order->ResourcesString);
	
	$adult_count = 0;
	$child_count = 0;
	$child_ages = array();
	$resource_names = array();
	$accomodations = array();


	foreach($order_resource_summary as $order_resource_item) {
		$paxagesarray = explode(',', $order_resource_item->PaxAges);
		foreach($paxagesarray as $paxage) {
			if($paxage > 17) {
				$adult_count++;
			}
			else {
				$child_count++;
				$child_ages[] = $paxage.__(' Years', 'bfi');
			}
		}
	}
	$child_ages = implode(', ', $child_ages);

	$TimeSlotId = $order_resource_summary[0]->TimeSlotId;
	$TimeSlotStart = $order_resource_summary[0]->TimeSlotStart;
	$TimeSlotEnd = $order_resource_summary[0]->TimeSlotEnd;

	$ShowTimeSlot = !empty($TimeSlotId);

	$resource_names = array();
	$mrcName = '';
	$accomodation_total = 0;
	$merchantId = "";

	foreach($order_resource_summary as $order_resource_item) {
		if(empty($mrcName)){
			$mrcName = $order_resource_item->MrcName;
		}
		if(empty($merchantId)){
			$merchantId = $order_resource_item->MerchantId;
		}
         $resource_names[] = $order_resource_item->ResName;
         $accomodation_total += $order_resource_item->TotalPrice;
     }
     $resource_names = implode('<br />', $resource_names);


?>
	  
	  <!--reservation-bookig-detail-->
      <div class="reservation-bookig-detail" id="booking-detail-form">
         <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> booking-header">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 headline">
               <?php _e('Booking Detail', 'bfi') ?>
            </div>
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 backlist">
               <a href="#" class="btn btn-primary"><?php _e('Back to list', 'bfi') ?> <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
            </div>
         </div>
         <!--booking-header-->
         <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 booking-filled-detail">
            <div class="top_headings-title">
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 headline">
                  <?php print $mrcName; ?> <span class="bookingformerchantdetails-rating StarRating-<?php echo $merchantId; ?>"></span>	  
               </div>
               <div class="check-out-title">
                  <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 checkout-box ">
                    <i class="fa fa-calendar" aria-hidden="true"></i> <?php _e('Check-in', 'bfi') ?> <?php print date_i18n('D',$dateCheckin->getTimestamp()); ?> <?php print $dateCheckin->format('d'); ?> 
					<?php if($ShowTimeSlot) {
						$startHour = new DateTime("2000-01-01 0:0:00.1"); 
						$endHour = new DateTime("2000-01-01 0:0:00.1"); 
						$startHour->add(new DateInterval('PT' . $TimeSlotStart . 'M'));
						$endHour->add(new DateInterval('PT' . $TimeSlotEnd . 'M'));
					?>
                      <?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>
					<?php } 
					if (!empty($order_resource_summary[0]->CheckInTime) && !empty($order_resource_summary[0]->TimeDuration))
					{
						$startHour = DateTime::createFromFormat("YmdHis", $order_resource_summary[0]->CheckInTime);
						$endHour = DateTime::createFromFormat("YmdHis", $order_resource_summary[0]->CheckInTime);
						$endHour->add(new DateInterval('PT' . $order_resource_summary[0]->TimeDuration . 'M'));
					?>
						(<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
					<?php 
					} 
					?>				

					<?php print date_i18n('M',$dateCheckin->getTimestamp()); ?> 
					<?php print $dateCheckin->format('Y'); ?>
                  </div>
			<?php if(!$ShowTimeSlot && empty($order_resource_summary[0]->CheckInTime)) : ?>
                  <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6  checkout-box">
                     <i class="fa fa-calendar" aria-hidden="true"></i> <?php _e('Check-out', 'bfi') ?> <?php print date_i18n('D',$dateCheckout->getTimestamp()); ?> 
					 <?php print $dateCheckout->format('d'); ?> 
					 <?php print date_i18n('M',$dateCheckout->getTimestamp()); ?> 
					 <?php print $dateCheckout->format('Y'); ?>
                  </div>
			<?php endif;  ?>
               </div>
               <div class="checkout-box-details">
                  <i class="fa fa-user" aria-hidden="true"></i> <?php print $adult_count; ?> <?php _e('adults', 'bfi') ?>
                  <?php if($child_count > 0) { print ' , '.$child_count.' '.__('children', 'bfi').' ('.$child_ages.')'; } ?>
               </div>
            </div>
			<div class="bfi-clearboth">
            <div style="padding:2%;">
				<?php 	foreach($order_resource_summary as $order_resource_item) {
				$accomodations = json_decode($order_resource_item->DetailsString);
								
				?>
				<h4><?php echo $order_resource_item->ResName; ?></h4>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
					<?php foreach($accomodations as $accomodation) { ?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9"><?php echo $accomodation->Name; ?></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"> € <?php echo $accomodation->TotalPrice; ?></div>
					<?php } ?>
				</div>
				<br />
				<?php } ?>
			</div>
            <div class="total-heading-title">
               <h4><?php _e('Total Price', 'bfi') ?>:</h4>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9 total_content-title button-total">
					<!-- <button class="btn btn-default">		 Offer%</button> -->
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 total_content-title"> € <?php print $accomodation_total; ?></div>
				</div>
            </div>
         </div>
         <!-- booking-filled-detail -->
         <!-- guest-filled-detail --start-->
         <!--<div class="guest-main-block">
            <div class="guest-heading-title">
               <h4>Total Price:</h4>
            </div>
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 guest_content-left">
               Guest n°1:
            </div>
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9 guest_content-right"><a href="#">Alessandra Viaro</a></div>
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 guest_content-left">
               Guest n°1:
            </div>
            <div class="guest-form-details">
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 guest_input-left">
                  <div class="form-group">
                     <label for="usr">Name:</label>
                     <input type="text" class="form-control" id="name">
                  </div>
               </div>
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 guest_input-right">
                  <div class="form-group">
                     <label for="usr">Date of birth:</label>
                     <input type="text" class="form-control" id="country">
                  </div>
               </div>
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 guest_input-left">
                  <div class="form-group">
                     <label for="usr">Surname:</label>
                     <input type="text" class="form-control" id="usr">
                  </div>
               </div>
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 guest_input-right">
                  <div class="form-group">
                     <label for="usr">Birth place:</label>
                     <input type="text" class="form-control" id="usr">
                  </div>
               </div>
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 guest_input-left">
                  <div class="form-group">
                     <label for="usr">Gender:</label>
                     <input type="text" class="form-control" id="gender">
                  </div>
               </div>
               <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 guest_input-right">
                  <div class="form-group">
                     <label for="usr">Country:</label>
                     <input type="text" class="form-control" id="country">
                  </div>
               </div>
            </div>
         </div>-->
         <!-- guest-filled-detail--end -->
      </div>
      <!-- reservation-bookig-detail -->