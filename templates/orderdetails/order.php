<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$sitename = get_bloginfo();


$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$ssllogo = COM_BOOKINGFORCONNECTOR_SSLLOGO;
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
$base_url = get_site_url(null,'', $usessl ? "https" : null);

$cCCTypeList = [];
$minyear = date("y");
$maxyear = $minyear+5;

$model = new BookingForConnectorModelMerchantDetails;
$merchant = $model->getItem($order->MerchantId);	 

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$routeThanks = $routeMerchant .'/'. _x('thanks', 'Page slug', 'bfi' );
$routeThanksKo = $routeMerchant .'/'. _x('errors', 'Page slug', 'bfi' );

$payment_page = get_post( bfi_get_page_id( 'payment' ) );
$url_payment_page = get_permalink( $payment_page->ID );

$urlPayment = $url_payment_page .'/'.$order->OrderId; //JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
$urlOtherPayment = $url_payment_page .'/'.$order->OrderId; //JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
$urlCrew = ''; // JRoute::_('index.php?view=crew&orderId=' . $order->OrderId);

//$formRoute = "index.php?option=com_bookingforconnector&task=updateCCdataOrder"; 
$formRoute = $base_url .'/bfi-api/v1/task?task=updateCCdataOrder'; 

$dateCheckin = BFCHelper::parseJsonDate($order->StartDate);
$dateCheckout = BFCHelper::parseJsonDate($order->EndDate);

$firstName = BFCHelper::getItem($order->CustomerData, 'nome');
$lastName = BFCHelper::getItem($order->CustomerData, 'cognome');
$email = BFCHelper::getItem($order->CustomerData, 'email');
$nation = BFCHelper::getItem($order->CustomerData, 'stato');
$culture = BFCHelper::getItem($order->CustomerData, 'lingua');
$address = BFCHelper::getItem($order->CustomerData, 'indirizzo');
$city = BFCHelper::getItem($order->CustomerData, 'citta');
$postalCode = BFCHelper::getItem($order->CustomerData, 'cap');
$province = BFCHelper::getItem($order->CustomerData, 'provincia');
$phone = BFCHelper::getItem($order->CustomerData, 'telefono');
$ArchivedAsSpam = $order->ArchivedAsSpam;
$orderDetails =  BFCHelper::GetOrderDetailsById($order->OrderId, $language);

//echo "<pre>";
//echo print_r($orderDetails);
//echo "</pre>";

if(empty($order->DepositAmount)){
	$order->DepositAmount = $order->TotalAmount;
}

?>
	<?php if ($ArchivedAsSpam) :?>
		<div class="bfi-alert bfi-alert-danger">
			<strong><?php _e('Rejected order', 'bfi') ?></strong>
		</div>
	<?php endif; ?>
<div id="my-account-tabs">
  <ul>
    <li><a href="#tabs-1"><i class="fa fa-user" aria-hidden="true"></i> <?php _e('Personal Informations', 'bfi') ?></a></li>
    <li><a href="#tabs-2"><i class="fa fa-briefcase" aria-hidden="true"></i> <?php _e('Reservations', 'bfi') ?></a></li>
  </ul>
	<div id="tabs-1" class="tabcontainer">
		<div class="social-networks personal-networks">
			<h3><?php _e('Personal Informations', 'bfi') ?></h3>
			<h5><?php _e('Your profile', 'bfi') ?></h5>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Name', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $firstName;?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Surname', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $lastName;?>
				</div>	
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Email', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $email;?>
				</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Phone', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $phone;?>
				</div>
				
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Address', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $address;?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('City', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $city;?>
				</div>	
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Country', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $nation;?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('State/Province', 'bfi') ?>
				</div>	
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $province;?>
				</div>	
			</div>	
		</div>	
	</div>	
	<div id="tabs-2" class="tabcontainer">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Booking reference', 'bfi') ?>
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $order->OrderId;?>
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('External order ID', 'bfi') ?>
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $order->ExternalId;?>
			</div>	
		</div>	
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Deposit total', 'bfi') ?>
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $order->DepositAmount ;?>
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Total', 'bfi') ?>
			</div>	
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php echo $order->TotalAmount;?>
			</div>	
		</div>	
	<?php if ($ArchivedAsSpam) :?>
				<div class="bfi-alert bfi-alert-danger">
					<strong><?php _e('Rejected order', 'bfi') ?></strong>
				</div>
	<?php else: ?>
				<?php _e('Status', 'bfi') ?>:<br />
				<?php if ($order->Status == 1 && $order->DepositAmount >0):?>
					<?php _e('Order sent to bank', 'bfi') ?>
					<a href="<?php echo $urlPayment ?>" class="btn "><?php _e('Pay', 'bfi') ?></a>
				<?php endif;?>
				<?php if (($order->Status == 0)  && $order->DepositAmount >0):?>
					<?php _e('Waiting to confirm', 'bfi') ?>
				<?php endif;?>
				<?php if (( $order->Status == 16 || $order->Status == 4)  && $order->DepositAmount >0):?>
					<?php _e('Waiting to confirm', 'bfi') ?>
					<a href="<?php echo $urlPayment ?>" class="btn "><?php _e('Pay', 'bfi') ?></a>
				<?php endif;?>
				<?php if ($order->Status == 7 && $order->DepositAmount >0):?>
					<?php _e('Payment attempt was not successful. Try again', 'bfi') ?>
					<a href="<?php echo $urlPayment ?>" class="btn "><?php _e('Pay', 'bfi') ?></a>
				<?php endif;?>
				<?php if ($order->Status == 3):?>
					<?php _e('Rejected order', 'bfi') ?>
				<?php endif;?>
				<?php if ($order->Status == 5):?>
					<?php _e('Payed order', 'bfi') ?>
				<?php endif;?>
				<?php if ($order->Status == 20):?>
					<?php _e('Order by credit card guarantee', 'bfi') ?><br />
					<?php 
						$ccDecr1=BFCHelper::decrypt($order->CCdata);
						$ccDecr= substr($ccDecr1, 0, strrpos($ccDecr1,">")+1);
						$cc = new StdClass();
						if(!empty($ccDecr)){
							$cc->Type = BFCHelper::getItem($ccDecr, 'tipo','cc');
							$cc->Name = BFCHelper::getItem($ccDecr, 'nome','cc');
							$cc->Number = BFCHelper::getItem($ccDecr, 'numero','cc');
							$cc->ExpiryMonth = BFCHelper::getItem($ccDecr, 'expmon','cc');
							$cc->ExpiryYear = BFCHelper::getItem($ccDecr, 'expyear','cc');
	//						$cc = json_decode(substr($ccDecr, 0, strrpos($ccDecr,"}")+1));
						}

					?>
						<?php _e('Credit card details', 'bfi') ?><br /><br />
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php _e('Type', 'bfi') ?>: </span>
								<?php echo $cc->Type; ?>
							</div><!--/span-->
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php _e('Holder', 'bfi') ?>: </span>
								<?php echo $cc->Name; ?>
							</div><!--/span-->
						</div>
						
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php _e('Number', 'bfi') ?>: </span>
								<?php 
//									echo 'xxxx-xxxx-xxxx-' . substr($cc->Number, -4) ; 
									echo $cc->Number;
							 ?>
							</div><!--/span-->
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php _e('Valid until', 'bfi') ?>: </span>
								<?php echo $cc->ExpiryMonth; ?>/<?php echo $cc->ExpiryYear; ?>
							</div><!--/span-->
						</div>

						
						<a href="javascript:changeCC();" class="btn "><?php _e('Change data', 'bfi') ?></a>
						<?php endif;?>
	<?php endif; ?>
		<div class="reservation-list-wrapper">
		<table class="bfi-table bfi-table-bordered bfi-table-cart" style="margin-top: 20px;">
			<?php 
				$order_resource_summary = json_decode($orderDetails->ResourcesString);

			//	echo "<pre>";
			//	echo print_r($order_resource_summary);
			//	echo "</pre>";
					
				foreach($order_resource_summary as $orderItem) {
											$nad = 0;
											$nch = 0;
											$nse = 0;
											$countPaxes = 0;

											$nchs = array(null,null,null,null,null,null);
												$paxages = $orderItem->PaxAges;
												if(is_array($paxages)){
													$countPaxes = array_count_values($paxages);
													$nchs = array_values(array_filter($paxages, function($age) {
														if ($age < (int)BFCHelper::$defaultAdultsAge)
															return true;
														return false;
													}));
												}
											array_push($nchs, null,null,null,null,null,null);
											if($countPaxes>0){
												foreach ($countPaxes as $key => $count) {
													if ($key >= BFCHelper::$defaultAdultsAge) {
														if ($key >= BFCHelper::$defaultSenioresAge) {
															$nse += $count;
														} else {
															$nad += $count;
														}
													} else {
														$nch += $count;
													}
												}
											}
				foreach($orderItem->Items as $res) {
																																										
			?>
					<tr>
						<td><?php _e('Accommodation', 'bfi') ?></td>
						<td><?php echo $res->Name ;?></td>
					</tr>
					<tr>
						<td><?php _e('Period', 'bfi') ?></td>
						<td>
											<?php
												if ($res->AvailabilityType == 0 )
												{
													$currCheckIn = new DateTime();
													$currCheckOut = new DateTime();
														$currCheckIn = new DateTime($res->CheckIn);
														$currCheckOut = new DateTime($res->CheckOut);
													$currDiff = $currCheckOut->diff($currCheckIn);
												?>
													<div class="bfi-timeperiod " >
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-in', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-out', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Total', 'bfi') ?>:
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->d + 1; ?></span> <?php _e('days', 'bfi') ?>
															</div>	
														</div>	
													</div>
											<?php
												}
												if ($res->AvailabilityType == 1 )
												{
													$currCheckIn = new DateTime();
													$currCheckOut = new DateTime();
														$currCheckIn = new DateTime($res->CheckIn);
														$currCheckOut = new DateTime($res->CheckOut);

													$currDiff = $currCheckOut->diff($currCheckIn);
												?>
													<div class="bfi-timeperiod " >
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-in', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-out', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Total', 'bfi') ?>:
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->d; ?></span> <?php _e('nights', 'bfi') ?>
															</div>	
														</div>	
													</div>
											<?php
												}
												if ($res->AvailabilityType == 2)
												{
													
													$currCheckIn = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
													$currCheckOut = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
													$currCheckOut->add(new DateInterval('PT' . $res->TimeDuration . 'M'));
													$currDiff = $currCheckOut->diff($currCheckIn);
													$timeDuration = $currDiff->i + ($currDiff->h*60);
												?>
													<div class="bfi-timeperiod " >
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-in', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-out', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Total', 'bfi') ?>:
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php _e('hours', 'bfi') ?>
															</div>	
														</div>	
													</div>
											<?php
												}
			/*-------------------------------*/	
												if ($res->AvailabilityType == 3)
												{

													$currCheckIn = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
													$currCheckOut = clone $currCheckIn;
													$currCheckIn->setTime(0,0,1);
													$currCheckOut->setTime(0,0,1);
													$currCheckIn->add(new DateInterval('PT' . $res->TimeSlotStart . 'M'));
													$currCheckOut->add(new DateInterval('PT' . $res->TimeSlotEnd . 'M'));

													$currDiff = $currCheckOut->diff($currCheckIn);

												?>
													<div class="bfi-timeslot ">
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-in', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?> ">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 bfi-title"><?php _e('Check-out', 'bfi') ?>
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
															</div>	
														</div>	
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>3 "><?php _e('Total', 'bfi') ?>:
															</div>	
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php _e('hours', 'bfi') ?>
															</div>	
														</div>	
													</div>
											<?php
												}								

			/*-------------------------------*/									
										?>
						</td>
					</tr>
			<?php 
				}		
				?>
					<tr>
						<td><?php _e('People', 'bfi') ?></td>
						<td><?php if (!$ArchivedAsSpam) :?>
													<div class="bfi-cart-person">
														<?php if ($nad > 0): ?><?php echo $nad ?> <?php _e('Adults', 'bfi') ?> <?php endif; ?>
														<?php if ($nse > 0): ?><?php if ($nad > 0): ?>, <?php endif; ?>
															<?php echo $nse ?> <?php _e('Seniores', 'bfi') ?>
														<?php endif; ?>
														<?php if ($nch > 0): ?>
															, <?php echo $nch ?> <?php _e('Children', 'bfi') ?> (<?php echo implode(" ".__('Years', 'bfi') .', ',$nchs) ?> <?php _e('Years', 'bfi') ?> )
														<?php endif; ?>
												   </div>
							<?php endif; ?>
						</td>
					</tr>
			<?php 
				}		
				?>
				</table>
		</div>	
	</div>	
</div>	

				<?php if ($order->Status == 20 && !$ArchivedAsSpam):?>
<?php 

		$currentbookingTypeId= BFCHelper::getOrderMerchantPaymentId($order);
		$data = BFCHelper::getMerchantPaymentData($currentbookingTypeId);
		if(!empty($data)){
			$cCCTypeList = array();
			$datas = explode("|", $data->Data);
			if (is_array($datas)){
				foreach ($datas as $singleData) {
					$cCCTypeList[$singleData] = $singleData;
				}
			}else{
				$cCCTypeList[$datas] = $datas;
			}
 		}
?>

					<form method="post" id="ccInformations" action="<?php echo $formRoute; ?>" style="display:none;" class="form-validate mailalertform borderbottom paymentoptions">
							<h2><?php _e('Credit card details', 'bfi') ?></h2>
										<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_resource-payment-form">   
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php _e('Type', 'bfi') ?> </label>
												<select id="formcc_circuito" name="form[cc_circuito]" class="bfi_input_select">
													<?php 
														foreach($cCCTypeList as $ccCard) {
															?><option value="<?php echo $ccCard ?>"><?php echo $ccCard ?></option><?php 
														}
													?> 
												</select>
											</div><!--/span-->
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php _e('Holder', 'bfi') ?> </label>
												<input type="text" value="" size="50" name="form[cc_titolare]" id="cc_titolare" required  title="<?php _e('This field is required.', 'bfi') ?>">
											</div><!--/span-->
										</div>
										
										<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_resource-payment-form">   
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php _e('Number', 'bfi') ?> </label>
												<input type="text" value="" size="50" maxlength="50" name="form[cc_numero]" id="cc_numero" required  title="<?php _e('This field is required.', 'bfi') ?>">
											</div><!--/span-->
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php _e('Valid until', 'bfi') ?></label>
												<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
													<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
														<?php _e('Month (MM)', 'bfi') ?>
													</div><!--/span-->
													<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 ccdateinput">
														<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5"><input type="text" value="" size="2" maxlength="2" name="form[cc_mese]" id="cc_mese" required  title="<?php _e('This field is required.', 'bfi') ?>"></div>
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 " style="text-align:center;" >/</div>
															<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5"><input type="text" value="" size="2" maxlength="2" name="form[cc_anno]" id="cc_anno" required  title="<?php _e('This field is required.', 'bfi') ?>"></div>
														</div>
													</div><!--/span-->
													<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
														<?php _e('Year (YY)', 'bfi') ?>
													</div><!--/span-->
												</div><!--/row-->
											</div>
										</div>
									
									<br />
									<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">   
										  <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
											 <?php echo $ssllogo ?>
										  </div>
										  <!-- <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
											  <?php echo sprintf(__('%1s will not charge anything to your credit card. Your credit card details are only requested in order to guarantee your booking.', 'bfi'),$sitename); ?>
										  </div> -->
									</div>
							<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
							<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
							<input type="hidden" name="OrderId" value="<?php echo $order->OrderId; ?>" />
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> bfi_footer-book" >
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10"></div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 bfi_footer-send"><button type="submit" id="btnbfFormSubmit" ><?php _e('Send', 'bfi') ?></button></div>
							</div>
</form>
<script type="text/javascript">
<!--
function changeCC(){
	jQuery("#ccInformations").show();
};
jQuery(function($){
			$("#ccInformations").validate(
		    {
				rules: {
					"form[cc_mese]": {
					  required: true,
					  range: [1, 12]
					},
					"form[cc_anno]": {
					  required: true,
					  range: [<?php echo $minyear ?>, <?php echo $maxyear ?>]
					},
					"form[cc_numero]": {
					  required: true,
					  creditcard: true
					}
				},
		        messages:
		        {
		        	"form[cc_mese]": "<?php _e('Mandatory', 'bfi') ?>",
		        	"form[cc_anno]": "<?php _e('Mandatory', 'bfi') ?>",
		        	"form[cc_numero]": "<?php _e('Mandatory', 'bfi') ?>",
		        },

				invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        //errorPlacement: function(error, element) { //just nothing, empty  },
				highlight: function(label) {
			    	//$(label).removeClass('error').addClass('error');
			    	//$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
					$(label).remove();
//					$(label).hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {
					if (typeof grecaptcha === 'object') {
						var response = grecaptcha.getResponse();
						//recaptcha failed validation
						if(response.length == 0) {
							$('#recaptcha-error').show();
							return false;
						}
						//recaptcha passed validation
						else {
							$('#recaptcha-error').hide();
						}					 
					}
					 jQuery.blockUI();
					 form.submit();
				}

			});
	});
	
//-->
</script>
				<?php endif;?>

	<?php if ($order->Status == 5 && false):?>
	<!-- ulteriori pagamenti -->
		 <h3><?php _e('Other payments', 'bfi') ?></h3>
<?php
$orderPayments = BFCHelper::getOrderPayments(0,0,$order->OrderId);
?>
	<?php if (count($orderPayments)>0):?>
	<table class="bfi-table bfi-table-striped">
		<tr>
			<td><?php _e('Date', 'bfi') ?></td>
			<td><?php _e('Amount', 'bfi') ?></td>
			<td><?php _e('Status', 'bfi') ?></td>
		</tr>

		<?php foreach($orderPayments as $orderPayment): ?>
		<?php 
		$datePaymentDate = BFCHelper::parseJsonDate($orderPayment->PaymentDate );
		?>
		<tr>
			<td><?php echo  $datePaymentDate?></td>
			<td><span class=" bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($orderPayment->Value);  ?></span>  </td>
			<td>
				<?php if ($orderPayment->Status == 7):?>
					<?php _e('Payment refused', 'bfi') ?>
				<?php endif;?>
				<?php if ($orderPayment->Status == 5):?>
					<?php _e('Payed order', 'bfi') ?>
				<?php endif;?>
		</tr>
		
		<?php endforeach?>
	</table>	
	<?php endif;?>
				
				<form action="<?php echo $urlOtherPayment?>"  method="post" class="form-inline" style="margin-bottom:0" id="otherpayment">
					<input type="text" name="overrideAmount" id="overrideAmount" value="" placeholder="0.00" />
					<input type="hidden" name="actionmode" value="orderpayment" />
					<input type="hidden" name="OrderId" value="<?php echo $order->OrderId; ?>" />
					<input type="submit" class="btn btn-primary" value="<?php _e('New payment', 'bfi') ?>" />
				</form>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#otherpayment").validate(
		    {
		        rules:
		        {
		        	overrideAmount: {
						required: true,
						TwoDecimal: true,
						MinDecimal: true
						}
		        },
		        messages:
		        {
		        	overrideAmount: {
						required:"<?php _e('It \'requires a further amount for payments', 'bfi') ?>",
						MinDecimal:"<?php _e('It \'requires a further amount for payments', 'bfi') ?>",
						TwoDecimal: "<?php _e('Please insert a correct amount, it should be this format: xxx.xx (please use dot for deciaml prices)', 'bfi') ?>"
						}
				},
		        highlight: function(label) {
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.text('ok!').addClass('valid')
			    		.closest('.control-group').removeClass('error').addClass('success');
			    }
		    });
			//$("#overrideAmount").mask("9999?.99",{placeholder:"0"});   
		});

</script>	
	<?php endif;?>