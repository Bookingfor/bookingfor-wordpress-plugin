<?php
/**
 * The Template for displaying all merchant list
 *
 *
 * @see 	   
 * @author 		Bookingfor
 * @package 	        Bookingfor/Templates
 * @version             2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( "DONOTCACHEPAGE", true ); // Do not cache this page

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url .= "/" .ICL_LANGUAGE_CODE;
		}
}
$cultureCode = strtolower(substr($language, 0, 2));


$currencyclass = bfi_get_currentCurrency();
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
$enablecoupon = COM_BOOKINGFORCONNECTOR_ENABLECOUPON;
$currPolicy = null;
$currPolicyId = null;
$totalOrder = 0;
$totalCouponDiscount = 0;
$checkAnalytics = true;
$listName = "Cart Page";			

$layout = get_query_var( 'bfi_layout', '' );
$itemType = 0;
if ($layout == _x('thanks', 'Page slug', 'bfi' )) {
	$listName = "Cart Page";
	$checkAnalytics = true;
	$itemType = 2;
}								
$analyticsEnabled = COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
add_action('wp_head', 'bfi_google_analytics_EEc', 10, 1);
do_action('wp_head', $listName);

if($checkAnalytics && $analyticsEnabled ) {
	$checkAnalytics = true;
	switch($itemType) {
		case 2:
			$orderid = 	BFCHelper::getVar('orderid');
			$traceOrder = BFCHelper::IsInCookieOrders($orderid);
			if (!$traceOrder) {
				BFCHelper::AddToCookieOrders($orderid);
			}
			$act = 	BFCHelper::getVar('act');
			if(!empty($orderid) && $act!="Contact" && !$traceOrder ){
//						if(!empty($orderid)){
				$order = BFCHelper::getSingleOrderFromService($orderid);
				$purchaseObject = new stdClass;
				$purchaseObject->id = "" . $order->OrderId;
				$purchaseObject->affiliation = "" . $order->Label;
				$purchaseObject->revenue = $order->TotalAmount;
				$purchaseObject->tax = 0.00;
				
				$allobjects = array();
				$allservices = array();
				$svcTotal = 0;

				$orderDetails = BFCHelper::GetOrderDetailsById($orderid,$language);
				if (!empty($orderDetails) && !empty($orderDetails->ResourcesString)) {
					$order_resource_summary = json_decode($orderDetails->ResourcesString);
					$merchantIdsArr = array_unique(array_map(function ($i) { return $i->MerchantId; }, $order_resource_summary));
					$merchantIds = implode(',',$merchantIdsArr);
					$merchantDetails = json_decode(BFCHelper::getMerchantsByIds($merchantIds,$language));

					foreach($order_resource_summary as $orderItem) {
						$currMerchant = null;
						foreach($merchantDetails as $merchantDetail) {
							if ($merchantDetail->MerchantId == $orderItem->MerchantId) {
								$currMerchant = $merchantDetail;
								break;
							}
						}								
						$brand = BFCHelper::string_sanitize($currMerchant->Name);
						$mainCategoryName = BFCHelper::string_sanitize($currMerchant->MainCategoryName);
						foreach($orderItem->Items as $currKey=>$res) {
							if ($currKey==0) {
								$mainObj = new stdClass;
								$mainObj->id = "" . $res->ResourceId . " - Resource";
								$mainObj->name = BFCHelper::string_sanitize($res->Name);
//										$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
								$mainObj->category = $mainCategoryName;
								$mainObj->brand = $brand;
								$mainObj->price = $res->TotalAmount;
								$mainObj->quantity = $res->Qt;
								$allobjects[] = $mainObj;
							}else{
								$svcObj = new stdClass;
								$svcObj->id = "" . $res->ResourceId . " - Service";
								$svcObj->name = BFCHelper::string_sanitize($res->Name);
								$svcObj->category = "Services";
								$svcObj->brand = $brand;
//										$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
								$svcObj->price = $res->TotalAmount;
								$svcObj->quantity = $res->Qt;
								$allobjects[] = $svcObj;
							}
						}

					}
//						$document->addScriptDeclaration('
//						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');	
			echo '<script type="text/javascript"><!--
			';
			echo ('callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');
			echo "//--></script>";
					
				}
				

				
			}

			break;
	}
}

?>
	<?php
		get_header();
		/**
		 * bookingfor_before_main_content hook.
		 */
		do_action( 'bookingfor_before_main_content' );
	?>
<?php 
$currCart = null;
$tmpUserId = BFCHelper::bfi_get_userId();
$currCart = BFCHelper::GetCartByExternalUser($tmpUserId, $language, true);
$currentCartsItems = BFCHelper::getSession('totalItems', 0, 'bfi-cart');
?>
<script type="text/javascript">
<!--
jQuery(".bfibadge").html('<?php echo ($currentCartsItems>0) ?$currentCartsItems:"";?>');	
//-->
</script>
<?php 

switch ( $layout) {
	case _x('thanks', 'Page slug', 'bfi' ):
		bfi_get_template("thanks.php"); 

	break;
	case _x('errors', 'Page slug', 'bfi' ):
		bfi_get_template("errors.php"); 
		$sendAnalytics = false;
	break;
}

if(empty($layout)){

$totalOrder = 0;

$listStayConfigurations = array();
$dateTimeNow =  new DateTime('UTC');



$cartEmpty=empty($currCart);
$cartConfig = null;
if(!$cartEmpty){
	$cartConfig = json_decode(($currCart->CartConfiguration));
	if(isset($cartConfig->Resources) && count($cartConfig->Resources)>0){
		$cartEmpty = false;
	}else{
		$cartEmpty = true;
	}

}

	if($cartEmpty){
		echo '<div class="bfi-content">'.__('Your Cart is empty! ', 'bfi').'</div>';
	}else{
$allResourceId = array();
$allServiceIds = array();
$allPolicyHelp = array();
$allResourceBookable = array();
$allResourceNoBookable = array();
$allPolicies = array();

		$modelMerchant = new BookingForConnectorModelMerchantDetails;
//		$modelResource = new BookingForConnectorModelResource;
		$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
		$url_merchant_page = get_permalink( $merchantdetails_page->ID );
		$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
		$url_resource_page = get_permalink( $accommodationdetails_page->ID );
		$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
		$url_cart_page = get_permalink( $cartdetails_page->ID );
		if($usessl){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}

		$cartId= isset($currCart->CartId)?$currCart->CartId:0;
//		$cartConfig = json_decode($currCart->CartConfiguration);

		$cCCTypeList = array("Visa","MasterCard");
		$minyear = date("y");
		$maxyear = $minyear+5;
		$formRoute = $base_url .'/bfi-api/v1/task?task=sendOrders'; 
		$formRouteDelete = $base_url .'/bfi-api/v1/task?task=DeleteFromCart'; 
		$formRouteaddDiscountCodes = $base_url .'/bfi-api/v1/task?task=addDiscountCodesToCart'; 

//		$privacy = BFCHelper::GetPrivacy($language);
//		$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);

		$policyId = $cartConfig->PolicyId;
//		$currPolicy =  BFCHelper::GetPolicyById($policyId, $language);

		$deposit = 0;
		$totalWithVariation = 0;
		$totalRequestedWithVariation = 0;
		$totalAmount = 0;	
		$totalQt = 0;	
		$listMerchantsCart = array();
		$listResourcesCart = array();
		$listResourceIdsToDelete = array();
		$listMerchantBookingTypes = array();
		$listPolicyIds = array();
		$listPolicyIdsBookable = array();
		$listPolicyIdsNoBookable = array();

		$now = new DateTime('UTC');
		$now->setTime(0,0,0);
		
		// cerco risorse scadute come checkin
		foreach ($cartConfig->Resources as $keyRes=>$resource) {
			$id = $resource->ResourceId;
			$merchantId = $resource->MerchantId;
			$listResourcesCart[] = $id;
			$tmpCheckinDate = new DateTime('UTC');
			if($cartId==0){
				$tmpCheckinDate = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->FromDate,new DateTimeZone('UTC'));
//				$tmpCheckinDate->setTime(0,0,1);
			}else{
				$tmpCheckinDate = new DateTime($resource->FromDate,new DateTimeZone('UTC'));
			}
			if($tmpCheckinDate < $now){
				if($cartId==0){
					unset($cartConfig->Resources[$keyRes]);  
				}else{
					$listResourceIdsToDelete[] = $resource->CartOrderId;
				}
			}
						
			if (isset($listMerchantsCart[$merchantId])) {
				$listMerchantsCart[$merchantId][] = $resource;
			} else {
				$listMerchantsCart[$merchantId] = array($resource);
			}
			
			if(!empty($resource->ExtraServices)) { 
				foreach($resource->ExtraServices as $sdetail) {					
					$listResourcesCart[] = $sdetail->PriceId;
				}
			}

		}
		if(count($listResourceIdsToDelete)>0){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$currCart = BFCHelper::DeleteFromCartByExternalUser($tmpUserId, $language, implode(",", $listResourceIdsToDelete));
			$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
			$url_cart_page = get_permalink( $cartdetails_page->ID );
			if($usessl){
				$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
			}
			wp_redirect($url_cart_page);
			exit;
		}
		if($cartId==0){
			BFCHelper::setSession('hdnOrderData', json_encode($cartConfig->Resources), 'bfi-cart');
		}

		$tmpResources =  json_decode(BFCHelper::GetResourcesByIds(implode(",", $listResourcesCart),$language));

				
		$resourceDetail = array();
		$merchantDetail = array();

		foreach ($tmpResources as $resource) {
			$resourceId = $resource->Resource->ResourceId;
			if (!isset($resourceDetail[$resourceId])) {
				$resourceDetail[$resourceId] = $resource->Resource;
			}
			$merchantId = $resource->Merchant->MerchantId;
			if (!isset($merchantDetail[$merchantId])) {
				$merchantDetail[$merchantId] = $resource->Merchant;
			}
		}

?>
<div class="bfi-content">
<div class="bfi-cart-title"><?php _e('Secure booking. We protect your information', 'bfi') ?></div>
						

	<?php bfi_get_template("menu_small_booking.php"); ?>
<script type="text/javascript">
<!--
	jQuery(function()
	{
		jQuery(".bfi-menu-booking a:eq(2)").removeClass(" bfi-alternative3");
	});
//-->
</script>
	<div class="bfi-border bfi-cart-title2"><?php _e('Your reservation includes', 'bfi') ?></div>
<div class="bfi-table-responsive">
		<table class="bfi-table bfi-table-bordered bfi-table-cart" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php _e('Information', 'bfi') ?></th>
					<th><div><?php _e('For', 'bfi') ?></div></th>
					<th><div><?php _e('Price', 'bfi') ?></div></th>
					<th><div><?php _e('Options', 'bfi') ?></div></th>
					<th><div><?php _e('Qt.', 'bfi') ?></div></th>
					<th><div><?php _e('Total price', 'bfi') ?></div></th>
				</tr>
			</thead>
<?php
	foreach ($listMerchantsCart as $merchant_id=>$merchantResources) // foreach $listMerchantsCart
	{
		$MerchantDetail = $merchantDetail[$merchant_id];  //$modelMerchant->getItem($merchant_id);	 
		$routeMerchant = $url_merchant_page . $MerchantDetail->MerchantId.'-'.BFI()->seoUrl($MerchantDetail->Name);
		$nRowSpan = 1;
		
		$hasSuperior = !empty($MerchantDetail->MrcRatingSubValue);
		$rating = (int)$MerchantDetail->MrcRating;
		if ($rating>9 )
		{
			$rating = $rating/10;
			$hasSuperior = ($MerchantDetail->MrcRating%10)>0;
		} 

		$mrcindirizzo = "";
		$mrccap = "";
		$mrccomune = "";
		$mrcstate = "";

		if (empty($MerchantDetail->AddressData)){
			$mrcindirizzo = isset($MerchantDetail->Address)?$MerchantDetail->Address:""; 
			$mrccap = isset($MerchantDetail->ZipCode)?$MerchantDetail->ZipCode:""; 
			$mrccomune = isset($MerchantDetail->CityName)?$MerchantDetail->CityName:""; 
			$mrcstate = isset($MerchantDetail->StateName)?$MerchantDetail->StateName:""; 
		}else{
			$addressData = isset($MerchantDetail->AddressData)?$MerchantDetail->AddressData:"";
			$mrcindirizzo = isset($addressData->Address)?$addressData->Address:""; 
			$mrccap = isset($addressData->ZipCode)?$addressData->ZipCode:""; 
			$mrccomune = isset($addressData->CityName)?$addressData->CityName:""; 
			$mrcstate = isset($addressData->StateName)?$addressData->StateName:"";
		}
		
		foreach ($merchantResources as $res )
		{
			$nRowSpan += 1;
			if(!empty($res->ExtraServices)) { 
				foreach($res->ExtraServices as $sdetail) {					
					$nRowSpan += 1;
				}
			}
		}
$mrcAcceptanceCheckInHours=0;
$mrcAcceptanceCheckInMins=0;
$mrcAcceptanceCheckInSecs=1;
$mrcAcceptanceCheckOutHours=0;
$mrcAcceptanceCheckOutMins=0;
$mrcAcceptanceCheckOutSecs=1;
if(!empty($MerchantDetail->AcceptanceCheckIn) && !empty($MerchantDetail->AcceptanceCheckOut) && $MerchantDetail->AcceptanceCheckIn != "-" && $MerchantDetail->AcceptanceCheckOut != "-"){
	$tmpAcceptanceCheckIn=$MerchantDetail->AcceptanceCheckIn;
	$tmpAcceptanceCheckOut=$MerchantDetail->AcceptanceCheckOut;
	$tmpAcceptanceCheckIns = explode('-', $tmpAcceptanceCheckIn);
	$tmpAcceptanceCheckOuts = explode('-', $tmpAcceptanceCheckOut);	
	$correctAcceptanceCheckIns = $tmpAcceptanceCheckIns[0];
	if(empty( $correctAcceptanceCheckIns )){
		$correctAcceptanceCheckIns = $tmpAcceptanceCheckIns[1];
	}
	if(empty( $correctAcceptanceCheckIns )){
		$correctAcceptanceCheckIns = "0:0";
	}
	$correctAcceptanceCheckOuts = $tmpAcceptanceCheckOuts[1];
	if(empty( $correctAcceptanceCheckOuts )){
		$correctAcceptanceCheckOuts = $tmpAcceptanceCheckOuts[0];
	}
	if(empty( $correctAcceptanceCheckOuts )){
		$correctAcceptanceCheckOuts = "0:0";
	}
	if (strpos($correctAcceptanceCheckIns, ":") === false) {
		$correctAcceptanceCheckIns .= ":0";
	}
	if (strpos($correctAcceptanceCheckOuts, ":") === false) {
		$correctAcceptanceCheckOuts .= ":0";
	}

	list($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs) = explode(':',$correctAcceptanceCheckIns.":1");
	list($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs) = explode(':',$correctAcceptanceCheckOuts.":1");
}

?>
			<tr >
				<td colspan="6" class="bfi-merchant-cart">
					<div class="bfi-item-title">
						<a href="<?php echo $isportal?$routeMerchant:"#";?>" ><?php echo $MerchantDetail->Name?></a>
						<span class="bfi-item-rating">
							<?php for($i = 0; $i < $rating; $i++) { ?>
								<i class="fa fa-star"></i>
							<?php } ?>	             
							<?php if ($hasSuperior) { ?>
								&nbsp;S
							<?php } ?>
						</span>
					</div>
					<br />
					<span class="street-address"><?php echo $mrcindirizzo ?></span>, <span class="postal-code "><?php echo $mrccap ?></span> <span class="locality"><?php echo $mrccomune ?></span> <span class="state">, <?php echo $mrcstate ?></span><br />

				</td>
			</tr>
			<?php 
			foreach ($merchantResources as $keyRes=>$res )
			{
				$nad = 0;
				$nch = 0;
				$nse = 0;
				$countPaxes = 0;

				if($cartId==0){
					$res->CartOrderId = $keyRes;  
				}
				$nchs = array(null,null,null,null,null,null);
					$paxages = $res->PaxAges;
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

				
				$countPaxes = $res->PaxNumber;

				$nchs = array_slice($nchs,0,$nch);
				$resource = $resourceDetail[$res->ResourceId];  //$modelMerchant->getItem($merchant_id);
				$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));											
											
				$routeResource = $url_resource_page . $resource->ResourceId .'-'.BFI()->seoUrl($resource->Name);
				
				$totalPricesExtraIncluded = 0;
				$totalAmountPricesExtraIncluded = 0;
				$pricesExtraIncluded = null;

				if(!empty( $res->PricesExtraIncluded )){
					$pricesExtraIncluded = json_decode($res->PricesExtraIncluded);
					foreach($pricesExtraIncluded as $sdetail) {					
						$totalPricesExtraIncluded +=$sdetail->TotalDiscounted;
						$totalAmountPricesExtraIncluded +=$sdetail->TotalAmount;
					}
				}

				$IsBookable = $res->IsBookable;
				if ($IsBookable) {
				    $allResourceBookable[] = $resource->Name;
				}else{
				    $allResourceNoBookable[] = $resource->Name;
				}
												
			?>
                                <tr>
                                    <td>
										<div class="bfi-resname">
											<a href="<?php echo $routeResource?>" target="_blank"><?php echo $resource->Name ?></a>
										</div>
										<?php if(!empty($resourceDescription)) { ?>
											<div class="bfi-description"><?php echo $resourceDescription ?></div>
											<br />
										<?php } ?>
										<div class="bfi-cart-person">
											<?php if ($nad > 0): ?><?php echo $nad ?> <?php _e('Adults', 'bfi') ?> <?php endif; ?>
											<?php if ($nse > 0): ?><?php if ($nad > 0): ?>, <?php endif; ?>
												<?php echo $nse ?> <?php _e('Seniores', 'bfi') ?>
											<?php endif; ?>
											<?php if ($nch > 0): ?>
												, <?php echo $nch ?> <?php _e('Children', 'bfi') ?> (<?php echo implode(" ".__('Years', 'bfi') .', ',$nchs) ?> <?php _e('Years', 'bfi') ?> )
											<?php endif; ?>
                                       </div>
								<?php																
								/*-----------checkin/checkout--------------------*/	
									if ($res->AvailabilityType == 0 )
									{
										$currCheckIn = new DateTime('UTC');
										$currCheckOut = new DateTime('UTC');
										if($cartId==0){
											$currCheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate,new DateTimeZone('UTC'));
											$currCheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->ToDate,new DateTimeZone('UTC'));

										}else{
											$currCheckIn = new DateTime($res->FromDate,new DateTimeZone('UTC'));
											$currCheckOut = new DateTime($res->ToDate,new DateTimeZone('UTC'));
											$currCheckIn->setTime($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs);
											$currCheckOut->setTime($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs);
										}										
										$currCheckInFull = clone $currCheckIn;
										$currCheckOutFull =clone $currCheckOut;
										$currCheckInFull->setTime(0,0,1);
										$currCheckOutFull->setTime(0,0,1);

										$currDiff = $currCheckOutFull->diff($currCheckInFull);
									?>
										<div class="bfi-timeperiod " >
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> <?php _e('from', 'bfi') ?> <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> <?php _e('until', 'bfi') ?> <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->d + 1; ?></span> <?php _e('days', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
									if ($res->AvailabilityType == 1 )
									{
										$currCheckIn = new DateTime('UTC');
										$currCheckOut = new DateTime('UTC');
										if($cartId==0){
											$currCheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate,new DateTimeZone('UTC'));
											$currCheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->ToDate,new DateTimeZone('UTC'));
										}else{
											$currCheckIn = new DateTime($res->FromDate,new DateTimeZone('UTC'));
											$currCheckOut = new DateTime($res->ToDate,new DateTimeZone('UTC'));
											$currCheckIn->setTime($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs);
											$currCheckOut->setTime($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs);
										}										

										$currCheckInFull = clone $currCheckIn;
										$currCheckOutFull =clone $currCheckOut;
										$currCheckInFull->setTime(0,0,1);
										$currCheckOutFull->setTime(0,0,1);

										$currDiff = $currCheckOutFull->diff($currCheckInFull);
									?>
										<div class="bfi-timeperiod " >
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> <?php _e('from', 'bfi') ?> <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> <?php _e('until', 'bfi') ?> <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->d; ?></span> <?php _e('nights', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
									if ($res->AvailabilityType == 2)
									{
										
										$currCheckIn = DateTime::createFromFormat("YmdHis", $res->CheckInTime,new DateTimeZone('UTC'));
										$currCheckOut = DateTime::createFromFormat("YmdHis", $res->CheckInTime,new DateTimeZone('UTC'));
										$currCheckOut->add(new DateInterval('PT' . $res->TimeDuration . 'M'));

										$currDiff = $currCheckOut->diff($currCheckIn);
										$timeDuration = $currDiff->h + round(($currDiff->i/60), 2);
									?>
										<div class="bfi-timeperiod " >
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDuration ?></span> <?php _e('hours', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
/*-------------------------------*/	
									if ($res->AvailabilityType == 3)
									{

										$currCheckIn = new DateTime($res->FromDate,new DateTimeZone('UTC'));										
										$currCheckOut = clone $currCheckIn;
										$currCheckIn->setTime(0,0,1);
										$currCheckOut->setTime(0,0,1);
										$currCheckIn->add(new DateInterval('PT' . $res->TimeSlotStart . 'M'));
										$currCheckOut->add(new DateInterval('PT' . $res->TimeSlotEnd . 'M'));

										$currDiff = $currCheckOut->diff($currCheckIn);

									?>
										<div class="bfi-timeslot ">
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php _e('hours', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}								

/*-------------------------------*/									
							?>
												<?php 
													$listServices = array();
													if(!empty($resource->ServiceIdList)){
														$listServices = explode(",", $resource->ServiceIdList);
														$allServiceIds = array_merge($allServiceIds, $listServices);
														?>
														<div class="bfisimpleservices" rel="<?php echo $resource->ServiceIdList ?>"></div>
														
														<?php
													}
													if(!empty($resource->TagsIdList)){
														?>
														<div class="bfiresourcegroups" rel="<?php echo $resource->TagsIdList?>"></div>
														<?php
													}					

												$currVat = isset($res->VATValue)?$res->VATValue:"";					
												$currTouristTaxValue = isset($res->TouristTaxValue)?$res->TouristTaxValue:0;				
												?>
												<?php if(!empty($currVat)) { ?>
													<div class="bfi-incuded"><strong><?php _e('Included', 'bfi') ?></strong> : <?php echo $currVat?> <?php _e('VAT', 'bfi') ?> </div>
												<?php } ?>
												<?php if(!empty($currTouristTaxValue)) { ?>
													<div class="bfi-notincuded"><strong><?php _e('Not included', 'bfi') ?></strong> : <span class="bfi_<?php echo $currencyclass ?>" ><?php echo BFCHelper::priceFormat($currTouristTaxValue) ?></span> <?php _e('City tax per person per night.', 'bfi') ?> </div>
												<?php } ?>
										
										
                                    </td>
									<td><!-- Min/Max -->
									<?php 
									if(!empty( $res->ComputedPaxes )){
										$computedPaxes = explode("|", $res->ComputedPaxes);
										$nadult =0;
										$nsenior =0;
										$nchild =0;
										
										foreach($computedPaxes as $computedPax) {
											$currComputedPax =  explode(":", $computedPax."::::");
											
											if ($currComputedPax[3] == "0") {
												$nadult += $currComputedPax[1];
											}
											if ($currComputedPax[3] == "1") {
												$nsenior += $currComputedPax[1];
											}
											if ($currComputedPax[3] == "2") {
												$nchild += $currComputedPax[1];
											}
										}

										if ($nadult>0) {
											?>
											<div class="bfi-icon-paxes">
												<i class="fa fa-user"></i> x <b><?php echo $nadult ?></b>
											<?php 
												if (($nsenior+$nchild)>0) {
													?>
													+ <br />
														<span class="bfi-redux"><i class="fa fa-user"></i></span> x <b><?php echo ($nsenior+$nchild) ?></b>
													<?php 
													
												}
											?>
											
											</div>
											
											<?php 
											
										}


									}else{
									?>
										
										<?php if ($res->MaxPaxes>0){?>
											<div class="bfi-icon-paxes">
												<i class="fa fa-user"></i> 
												<?php if ($res->MaxPaxes==2){?>
												<i class="fa fa-user"></i> 
												<?php }?>
												<?php if ($res->MaxPaxes>2){?>
													<?php echo ($res->MinPaxes != $res->MaxPaxes)? $res->MinPaxes . "-" : "" ?><?php echo  $res->MaxPaxes ?>
												<?php }?>
											</div>
										<?php } ?>
									<?php } ?>
									</td>
                                    <td class="text-nowrap"><!-- Unit price -->
                                        <?php if ($res->TotalDiscounted < $res->TotalAmount) { ?>
                                            <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalAmount - $totalAmountPricesExtraIncluded)/$res->SelectedQt); ?></span>
                                        <?php } ?>
                                        <?php if ($res->TotalDiscounted > 0) { ?>
                                            <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalDiscounted - $totalPricesExtraIncluded)/$res->SelectedQt); ?></span>

                                        <?php } ?>
                                    </td>
                                    <td class="text-nowrap">
<!-- options  -->									
					<div style="position:relative;">
					<?php 

$currPolicy = json_decode($res->PolicyValue);

$policy = $currPolicy;
$policyId= 0;
$policyHelp = "";
if(!empty( $policy )){

	$currValue = $policy->CancellationBaseValue;
	$policyId= $policy->PolicyId;
	$listPolicyIds[] = $policyId;
//	if($IsBookable){
//		$listPolicyIdsBookable[] = $policyId;
//
//	}else{
//		$listPolicyIdsNoBookable[] = $policyId;
//	}

	switch (true) {
		case strstr($policy->CancellationBaseValue ,'%'):
			$currValue = $policy->CancellationBaseValue;
			break;
		case strstr($policy->CancellationBaseValue ,'d'):
			$currValue = rtrim($policy->CancellationBaseValue,"d") .' '. __('days', 'bfi');
			break;
		case strstr($policy->CancellationBaseValue ,'n'):
			$currValue = rtrim($policy->CancellationBaseValue,"n") .' '. __('days', 'bfi');
			break;
		default:
			$currValue = '<span class="bfi_' . $currencyclass .'">'. BFCHelper::priceFormat($policy->CancellationBaseValue) .'</span>' ;
	}
	$currValuebefore = $policy->CancellationValue;
	switch (true) {
		case strstr($policy->CancellationValue ,'%'):
			$currValuebefore = $policy->CancellationValue;
			break;
		case strstr($policy->CancellationValue ,'d'):
			$currValuebefore = rtrim($policy->CancellationValue,"d") .' '. __('days', 'bfi');
			break;
		case strstr($policy->CancellationValue ,'n'):
			$currValuebefore = rtrim($policy->CancellationValue,"n") .' '. __('days', 'bfi');
			break;
		default:
			$currValuebefore = '<span class="bfi_' . $currencyclass .'">'. BFCHelper::priceFormat($policy->CancellationValue) .'</span>' ;
	}
	if($policy->CanBeCanceled){
		$currTimeBefore = "";
		$currDateBefore = "";
		$currDatePolicy =  new DateTime('UTC');
		if($cartId==0){
			$currDatePolicy = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate,new DateTimeZone('UTC'));
		}else{
			$currDatePolicy = new DateTime($res->FromDate,new DateTimeZone('UTC'));
		}										
		if(!empty( $policy->CancellationTime )){		
			switch (true) {
				case strstr($policy->CancellationTime ,'d'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"d") .' days'); 
					break;
				case strstr($policy->CancellationTime ,'h'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"h") .' hours'); 
					break;
				case strstr($policy->CancellationTime ,'w'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"w") .' weeks'); 
					break;
				case strstr($policy->CancellationTime ,'m'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"m") .' months'); 
					break;
			}
		}
		if($currDatePolicy > $dateTimeNow){
				if(!empty( $policy->CancellationTime )){					
					switch (true) {
						case strstr($policy->CancellationTime ,'d'):
							$currTimeBefore = rtrim($policy->CancellationTime,"d") .' '. __('days', 'bfi');
							break;
						case strstr($policy->CancellationTime ,'h'):
							$currTimeBefore = rtrim($policy->CancellationTime,"h") .' '. __('hours', 'bfi');	
							break;
						case strstr($policy->CancellationTime ,'w'):
							$currTimeBefore = rtrim($policy->CancellationTime,"w") .' '. __('weeks', 'bfi');	
							break;
						case strstr($policy->CancellationTime ,'m'):
							$currTimeBefore = rtrim($policy->CancellationTime,"m") .' '. __('months', 'bfi');
							break;
					}
				}

				if($policy->CancellationValue=="0" || $policy->CancellationValue=="0%"){
					?>
					<div class="bfi-policy-green"><?php _e('Cancellation FREE', 'bfi') ?>
					<?php 
					if(!empty( $policy->CancellationTime )){
						echo '<br />'.__('until', 'bfi') ;
						echo ' '.$currDatePolicy->format("d").' '.date_i18n('M',$currDatePolicy->getTimestamp()).' '.$currDatePolicy->format("Y");
						$policyHelp = sprintf(__('You may cancel free of charge until %1$s before arrival. You will be charged %2$s if you cancel in the %1$s before arrival.', 'bfi'),$currTimeBefore,$currValue);
					}
					?>
					</div>
					<?php 

					
				}else{
				if($policy->CancellationBaseValue=="0%" || $policy->CancellationBaseValue=="0"){
					?>
					<div class="bfi-policy-green"><?php _e('Cancellation FREE', 'bfi') ?></div>
					<?php 
					$policyHelp = __('You may cancel free of charge until arrival.', 'bfi');
				}else{
					?>
					<div class="bfi-policy-blue"><?php _e('Special conditions', 'bfi') ?></div>
					<?php 
					$policyHelp = sprintf(__('You may cancel with a charge of %3$s  until %1$s before arrival. You will be charged %2$s if you cancel in the %1$s before arrival.', 'bfi'),$currTimeBefore,$currValue,$currValuebefore);
				}
				}

			
		}else{
				if($policy->CancellationBaseValue=="0%" || $policy->CancellationBaseValue=="0"){
					?>
					<div class="bfi-policy-green"><?php _e('Cancellation FREE', 'bfi') ?></div>
					<?php 
					$policyHelp = __('You may cancel free of charge until arrival.', 'bfi');
				}else{
					?>
					<div class="bfi-policy-blue"><?php _e('Special conditions', 'bfi') ?></div>
					<?php 
					$policyHelp = sprintf(__('You will be charged %1$s if you cancel before arrival.', 'bfi'),$currValue);
				}
		}
				
	}else{ 
		// no refundable
		?>
			<div class="bfi-policy-none"><?php _e('Non refundable', 'bfi') ?></div>
		<?php 
		$policyHelp = sprintf(__('You will be charged all if you cancel before arrival.', 'bfi'));
	
	}
}
if(!empty($policyHelp)){
	$allPolicyHelp[] = $resource->Name . ": " . $policyHelp;
}
$allPolicies[] = array(
	'merchantid' =>$MerchantDetail->MerchantId,
	'merchantname' =>$MerchantDetail->Name,
	'resourcename' =>$resource->Name ,
	'policyHelp' =>$policyHelp,
	'policyid' =>$policyId,
	);

//$currMerchantBookingTypes = array();
$prepayment = "";
$prepaymentHelp = "";
//
//if(!empty( $policy->MerchantBookingTypesString )){
//	$currMerchantBookingTypes = json_decode($policy->MerchantBookingTypesString);
//	$currBookingTypeId = $currRateplan->RatePlan->MerchantBookingTypeId;
//	$currMerchantBookingType = array_filter($currMerchantBookingTypes, function($bt) use($currBookingTypeId) {return $bt->BookingTypeId == $currBookingTypeId;});
//	if(count($currMerchantBookingType)>0){
//		if($currMerchantBookingType[0]->PayOnArrival){
//			$prepayment = __("Pay at the property – NO PREPAYMENT NEEDED", 'bfi');
//			$prepaymentHelp = __("No prepayment is needed.", 'bfi');
//		}
//		if($currMerchantBookingType[0]->AcquireCreditCardData){
//			$prepayment = "";
//			if($currMerchantBookingType[0]->DepositRelativeValue=="100%"){
//				$prepaymentHelp = __('You will be charged a prepayment of the total price at any time.', 'bfi');
//			}else if(strpos($currMerchantBookingType[0]->DepositRelativeValue, '%') !== false  ) {
//				$prepaymentHelp = sprintf(__('You will be charged a prepayment of %1$s of the total price at any time.', 'bfi'),$currMerchantBookingType[0]->DepositRelativeValue);
//			}else{
//				$prepaymentHelp = sprintf(__('You will be charged a prepayment of %1$s at any time.', 'bfi'),$currMerchantBookingType[0]->DepositRelativeValue);
//			}
//		}
//	}
//}
$allMeals = array();
$cssclassMeals = "bfi-meals-base";
$mealsHelp = "";
if($res->IncludedMeals >-1){
	$mealsHelp = __("There is no meal option with this room.", 'bfi');
	if ($res->IncludedMeals & bfi_Meal::Breakfast){
		$allMeals[]= __("Breakfast", 'bfi');
	}
	if ($res->IncludedMeals & bfi_Meal::Lunch){
		$allMeals[]= __("Lunch", 'bfi');
	}
	if ($res->IncludedMeals & bfi_Meal::Dinner){
		$allMeals[]= __("Dinner", 'bfi');
	}
	if ($res->IncludedMeals & bfi_Meal::AllInclusive){
		$allMeals[]= __("All Inclusive", 'bfi');
	}
	if(in_array(__("Breakfast", 'bfi'), $allMeals)){
		$cssclassMeals = "bfi-meals-bb";
	}
	if(in_array(__("Lunch", 'bfi'), $allMeals) || in_array(__("Dinner", 'bfi'), $allMeals) || in_array(__("All Inclusive", 'bfi'), $allMeals)  ){
		$cssclassMeals = "bfi-meals-fb";
	}
	if(count($allMeals)>0){
		$mealsHelp = implode(", ",$allMeals). " " . __('included', 'bfi');
	}
	if(count($allMeals)==2){
		$mealsHelp = implode(" & ",$allMeals). " " . __('included', 'bfi');
	}
}
?>
<?php if(!empty($prepayment)) { ?>
						<div class="bfi-prepayment"><?php echo $prepayment ?></div>
<?php } ?>

						<div class="bfi-meals <?php echo $cssclassMeals?>"><?php echo $mealsHelp ?></div>
						<div class="bfi-options-help">
							<i class="fa fa-question-circle bfi-cursor-helper" aria-hidden="true"></i>
							<div class="webui-popover-content">
							   <div class="bfi-options-popover">
							   <?php if(!empty($mealsHelp)) { ?>
								   <p><b><?php _e('Meals', 'bfi') ?>:</b> <?php echo $mealsHelp; ?></p>
							   <?php } ?>
							   <p><b><?php _e('Cancellation', 'bfi') ?>:</b> <?php echo $policyHelp; ?></p>
							   <?php if(!empty($prepaymentHelp)) { ?>
								   <p><b><?php _e('Prepayment', 'bfi') ?>:</b> <?php echo $prepaymentHelp; ?></p>
							   <?php } ?>
							   </div>
							</div>
						</div>
						<?php if(!$IsBookable) { ?>
							<div class="bfi-bookingenquiry"><?php _e('Booking Enquiry', 'bfi') ?></div>
						<?php } ?>
					</div>

<!-- end options  -->									

									</td>
                                    <td>
										<?php echo $res->SelectedQt ?>

									</td>
                                    <td class="text-nowrap">
                                        <?php if ($res->TotalDiscounted < $res->TotalAmount) { ?>
                                            <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalAmount - $totalAmountPricesExtraIncluded)); ?></span>
                                        <?php } ?>
                                        <?php if ($res->TotalDiscounted > 0) { ?>
                                            <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalDiscounted - $totalPricesExtraIncluded)); ?></span>

                                        <?php } ?>
											<form action="<?php echo $formRouteDelete ?>" method="POST" style="display: inline-block;" class="bfi-cartform-delete">
											<input type="hidden" name="bfi_CartOrderId"  value="<?php echo $res->CartOrderId ?>" />
											<input type="hidden" name="bfi_cartId"  value="<?php echo $cartId ?>" />
											<input type="hidden" name="bficurrRes"  value="<?php echo htmlspecialchars(json_encode($res), ENT_COMPAT, 'UTF-8')?>" />
											<button class="bfi-btn-delete" data-title="Delete" type="submit" name="remove_order" value="delete">x</button></form>
									</td>
                                </tr>


								<?php if(!empty($res->PricesExtraIncluded)) { 
									foreach($pricesExtraIncluded as $sdetail) {
										$sdetailName = $sdetail->Name;
										$sdetailName = substr($sdetailName, 0, strrpos($sdetailName, ' - '));
										$sdetailName = str_replace("$$", "'", $sdetailName);
								 ?>	
                                        <tr class="bfi-cart-extra">
                                            <td>
													<div class="bfi-item-title">
														<?php echo  $sdetailName?>
													</div>
													<?php 
													if (!empty($sdetail->CheckInTime) && !empty($sdetail->TimeDuration) && $sdetail->TimeDuration>0)
													{
														$currCheckIn = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime,new DateTimeZone('UTC'));
														$currCheckOut = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime,new DateTimeZone('UTC'));
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
														$currDiff = $currCheckOut->diff($currCheckIn);
														$timeDuration = $currDiff->h + ($currDiff->i/60);;
//														$startHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
													?>
														<div class="bfi-timeperiod " >
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDuration ?></span> <?php _e('hours', 'bfi') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}
													if (isset($sdetail->TimeSlotId) && $sdetail->TimeSlotId > 0)
													{									
														$currCheckIn = new DateTime('UTC'); 
														if($cartId==0){
															$currCheckIn = DateTime::createFromFormat('d/m/Y', $sdetail->TimeSlotDate,new DateTimeZone('UTC'));
														}else{
															$currCheckIn = new DateTime($sdetail->TimeSlotDate,new DateTimeZone('UTC')); 
														}
														$currCheckOut = clone $currCheckIn;
														$currCheckIn->setTime(0,0,1);
														$currCheckOut->setTime(0,0,1);
														$currCheckIn->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));

														$currDiff = $currCheckOut->diff($currCheckIn);

//														$TimeSlotDate = new DateTime($sdetail->TimeSlotDate); 
//														$startHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$endHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$startHour->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));
													?>
														<div class="bfi-timeslot ">
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php _e('hours', 'bfi') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}

													?>
                                            </td>
                                            <td><!-- paxes --></td>
                                            <td class="text-nowrap"><!-- Unit price -->
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount/$sdetail->CalculatedQt);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted/$sdetail->CalculatedQt );?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-nowrap"> </td>
                                            <td>
												<?php echo $sdetail->CalculatedQt ?>
											</td>
                                            <td class="text-nowrap">
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted );?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                            <?php 
                                    } // foreach $svc
                                } // if res->ExtraServices
								 ?>	
								<?php if(!empty($res->ExtraServices)) { 
									foreach($res->ExtraServices as $sdetail) {					
									$resourceExtraService = $resourceDetail[$sdetail->PriceId]; 
								 ?>	
                                        <tr class="bfi-cart-extra">
                                            <td>
													<div class="bfi-item-title">
														<?php echo  $resourceExtraService->Name ?>
													</div>
													<?php 
													if (!empty($sdetail->CheckInTime) && !empty($sdetail->TimeDuration) && $sdetail->TimeDuration>0)
													{
														$currCheckIn = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime,new DateTimeZone('UTC'));
														$currCheckOut = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime,new DateTimeZone('UTC'));
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
														$currDiff = $currCheckOut->diff($currCheckIn);
														$timeDuration = $currDiff->h + ($currDiff->i/60);
//														$startHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
													?>
														<div class="bfi-timeperiod " >
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo  $timeDuration ?></span> <?php _e('hours', 'bfi') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}
													if (isset($sdetail->TimeSlotId) && $sdetail->TimeSlotId > 0)
													{									
														$currCheckIn = new DateTime('UTC'); 
														if($cartId==0){
															$currCheckIn = DateTime::createFromFormat('d/m/Y', $sdetail->TimeSlotDate,new DateTimeZone('UTC'));
														}else{
															$currCheckIn = new DateTime($sdetail->TimeSlotDate,new DateTimeZone('UTC')); 
														}
														$currCheckOut = clone $currCheckIn;
														$currCheckIn->setTime(0,0,1);
														$currCheckOut->setTime(0,0,1);
														$currCheckIn->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));

														$currDiff = $currCheckOut->diff($currCheckIn);

//														$TimeSlotDate = new DateTime($sdetail->TimeSlotDate); 
//														$startHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$endHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$startHour->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));
													?>
														<div class="bfi-timeslot ">
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo date_i18n('D',$currCheckIn->getTimestamp()) ?> <?php echo $currCheckIn->format("d") ?> <?php echo date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo date_i18n('D',$currCheckOut->getTimestamp()) ?> <?php echo $currCheckOut->format("d") ?> <?php echo date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php _e('hours', 'bfi') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}

													?>
                                            </td>
                                            <td><!-- paxes --></td>
                                            <td class="text-nowrap"><!-- Unit price -->
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount/$sdetail->CalculatedQt);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted/$sdetail->CalculatedQt );?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-nowrap"> </td>
                                            <td>
												<?php echo $sdetail->CalculatedQt ?>
											</td>
                                            <td class="text-nowrap">
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted );?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                            <?php 
								$totalWithVariation +=$sdetail->TotalDiscounted ;
								
								if(!$IsBookable) {
									$totalRequestedWithVariation +=$sdetail->TotalDiscounted ;
								}

                                    } // foreach $svc
                                } // if res->ExtraServices
							$totalWithVariation +=$res->TotalDiscounted ;

							if(!$IsBookable) {
								$totalRequestedWithVariation +=$res->TotalDiscounted ;
							}
							if ($IsBookable && !empty($res->DiscountCodes)) {
							    foreach($res->DiscountCodes as $discountCode){
									$totalRequestedWithVariation +=$discountCode->Value ;
								} 
							}


$currStayConfiguration = array("productid"=>$res->ResourceId,"price"=>$res->TotalAmount,"start"=>$currCheckIn->format("Y-m-d H:i:s"),"end"=> $currCheckOut->format("Y-m-d H:i:s"));

$listStayConfigurations[] = $currStayConfiguration;
                            }
                            ?>
<?php 

						
//		} // foreach $itm


	} // foreach $listMerchantsCart

//$totalRequest = BFCHelper::priceFormat($totalWithVariation);
$totalRequest = $totalWithVariation - $totalRequestedWithVariation;

?>
		</table>
	</div>	

<?php

$allCoupon = null;
$allDiscountCodes = array();
$countCoupon = 0;
if($enablecoupon && isset($cartConfig->DiscountCodes) && count($cartConfig->DiscountCodes)>0) {
	$allCoupon = $cartConfig->DiscountCodes;
	$countCoupon=count($allCoupon);
	foreach ($allCoupon as $singeCoupon) {
		$totalCouponDiscount += $singeCoupon->Value;
		$allDiscountCodes[] =  $singeCoupon->Code;
	}
} 
if($enablecoupon){
$textbtnCoupon = __('Apply', 'bfi');
if($countCoupon>0){
	$textbtnCoupon = '<i class="fa fa-plus-circle" aria-hidden="true"></i> ' . __('Add', 'bfi');	
}

?>
	<div class="bfi-content bfi-border bfi-cart-coupon"><a name="bficoupon"></a>
	<form method="post" action="<?php echo $formRouteaddDiscountCodes ?>" class="form-validate bfi-nomargin"  >
		<input type="hidden" name="bfilanguage"  value="<?php echo $language ?>" />
		<span class="bfi-simple-label"><?php _e('Gift cards or Promotional codes', 'bfi') ?></span>	
		<input type="text" name="bficoupons"  id="bficoupons" size="25" style="width:auto;display:inline-block;" placeholder="<?php _e('Enter code', 'bfi') ?>"  aria-required="true" required title="<?php _e('Mandatory', 'bfi') ?>"/>
		<button class="bfi-btn bfi-alternative" data-title="Add" type="submit" name="addcoupon" value="" id="bfiaddcoupon"><?php echo $textbtnCoupon ?></button>
	</form>
	</div>	
<?php
} 
?>
		
	
	<div class="bfi-content bfi-border bfi-text-right bfi-pad0-10">
	<?php 
	if($enablecoupon && isset($cartConfig->DiscountCodes) && count($cartConfig->DiscountCodes)>0) {
		foreach ($allCoupon as $singeCoupon) { 
	?>
	<span class="bfi-coupon-details"><?php _e('Discount', 'bfi') ?> <?php echo $singeCoupon->Name ?> - <span class="text-nowrap bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($singeCoupon->Value);?></span></span>
		<div class="webui-popover-content">
			<div class="bfi-options-popover">
				<strong><?php echo $singeCoupon->MerchantName ?></strong><br />
				<?php echo BFCHelper::getLanguage($singeCoupon->Description , $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ; ?>
			</div>
		</div><br />
	<?php 
		}
	}
	?>
	
		<span class="text-nowrap bfi-summary-body-resourceprice-total"><?php _e('Total', 'bfi') ?></span>	
		<?php if ($totalCouponDiscount>0) { ?>
			<span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($totalWithVariation)); ?></span>
		<?php } ?>

		<span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($totalWithVariation - $totalCouponDiscount);?></span>	
	</div>	

<br />

<?php
//---------------------FORM


//	$current_user = wp_get_current_user();
	$current_user = BFCHelper::getSession('bfiUser',null, 'bfi-User');

	if ($current_user==null) {
		$current_user = new stdClass;
		$current_user->CustomerId = 0; 
		$current_user->Name = ""; 
		$current_user->Surname = ""; 
		$current_user->Email = ""; 
		$current_user->Phone = "+39"; 
		$current_user->VATNumber = ""; 
		$current_user->MainAddress = new stdClass;
		$current_user->MainAddress->Address = ""; 
		$current_user->MainAddress->Country = $cultureCode; 
		$current_user->MainAddress->City = ""; 
		$current_user->MainAddress->ZipCode = ""; 
		$current_user->MainAddress->Province = ""; 	    
	}
	$sitename = get_bloginfo();

	$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
	$ssllogo = COM_BOOKINGFORCONNECTOR_SSLLOGO;
	$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	$idrecaptcha = uniqid("bfirecaptcha");
	$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	$tmpSearchModel = new stdClass;
	$tmpSearchModel->FromDate = new DateTime('UTC');
	$tmpSearchModel->ToDate = new DateTime('UTC');
	

//	$routeThanks = $routeMerchant .'/'. _x('thanks', 'Page slug', 'bfi' );
//	$routeThanksKo = $routeMerchant .'/'. _x('errors', 'Page slug', 'bfi' );
	$routeThanks = $url_cart_page .'/'. _x('thanks', 'Page slug', 'bfi' );
	$routeThanksKo = $url_cart_page .'/'. _x('errors', 'Page slug', 'bfi' );

$routePrivacy = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PRIVACYURL);
$routeTermsofuse = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL);

$infoSendBtn = sprintf(__('Choosing <b>Send</b> means that you agree to <a href="%3$s" target="_blank">Terms of use</a> of %1$s and <a href="%2$s" target="_blank">privacy and cookies statement.</a>.' ,'bfi'),$sitename,$routePrivacy,$routeTermsofuse);


$listPolicyIdsstr = implode(",",$listPolicyIds);
$currPolicies = BFCHelper::GetPolicyByIds($listPolicyIdsstr, $language);
$currPoliciesNoBookable = array();
$currPoliciesBookable = array();
$currPoliciesDescriptions="";

if(!empty( $currPolicies )){
	$currPoliciesNoBookable = array_filter($currPolicies, function ($currPolicy) {
		return (!$currPolicy->RequirePayment);
	});
	$currPoliciesBookable = array_filter($currPolicies, function ($currPolicy) {
		return ($currPolicy->RequirePayment);
	});
	foreach ($currPolicies as $currentPolicy ) {
		$currPoliciesDescriptions .= $currentPolicy->Description;
	}

}



$bookingTypes = null;
$showCC = false;
if(count($currPoliciesNoBookable) >0 ){
	foreach ($currPoliciesNoBookable as $currPolicyNoBookable) {
		if(strrpos($currPolicyNoBookable->MerchantBookingTypesString,'"AcquireCreditCardData":true')>0){
			$showCC = true;
			break;
		}
	}
}

//$firstCCRequiredBookingTypeIds=[];
if(!empty($currPoliciesBookable) && count($currPoliciesBookable) == 1){
	$currPolicy = array_shift($currPoliciesBookable);
	$bookingTypes = json_decode($currPolicy->MerchantBookingTypesString);
}
if(Count($currPoliciesBookable) >1){
	$allMerchantBookingTypes = (array_map(function ($i) { return json_decode($i->MerchantBookingTypesString); }, $currPoliciesBookable));
	$allMerchantBookingTypes = array_map('unserialize', array_unique(array_map('serialize', $allMerchantBookingTypes)));	
	if(Count($allMerchantBookingTypes) == 1){
		$bookingTypes = array_shift($allMerchantBookingTypes);
	} else{		
		$bookingTypeIds = array();
		foreach($allMerchantBookingTypes as $merchantBookingType){
			 usort($merchantBookingType, "BFCHelper::bfi_sortOrder");
			 $bookingTypeIds[] = array_unique(array_map(function ($i) { return $i->BookingTypeId; }, $merchantBookingType));
			 $firstMBT = reset($merchantBookingType); 
			 if ($firstMBT->AcquireCreditCardData) {
				$showCC = true;	
//				$firstCCRequiredBookingTypeIds[]=$firstMBT->BookingTypeId;
			 }
		}
		$availableBookingTypeIds = call_user_func_array('array_intersect', $bookingTypeIds);
		if(!empty($availableBookingTypeIds)){
			$allbookingTypes = array();
			foreach ($allMerchantBookingTypes as $merchantBookingTypes ) {
				foreach ($merchantBookingTypes as $merchantBookingType ) {
					$bookingTypeId = $merchantBookingType->BookingTypeId;
					if (!isset($allbookingTypes[$bookingTypeId])) {
						$allbookingTypes[$bookingTypeId] = $merchantBookingType;
					}
				}
			}
			$bookingTypes = array_filter($allbookingTypes, function ($merchantBookingType) use ($availableBookingTypeIds) {
				return in_array($merchantBookingType->BookingTypeId,$availableBookingTypeIds) ;
			});
		}
	}
 
}

$bookingTypedefault ="";
$bookingTypesoptions = array();
$bookingTypesValues = array();
$bookingTypeFrpmForm = isset($_REQUEST['bookingType'])?$_REQUEST['bookingType']:"";
$bookingTypeIddefault = 0;

if(!empty($bookingTypes)){
	$bookingTypesDescArray = array();
	foreach($bookingTypes as $bt)
	{
		$currDesc =  BFCHelper::getLanguage($bt->Name, $language) . "<div class='bfi-ccdescr'>" . BFCHelper::getLanguage($bt->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) . "</div>";
		if($bt->AcquireCreditCardData && !empty($bt->Data)){

			$ccimgages = explode("|", $bt->Data);
			$cCCTypeList = array();
			$currDesc .= "<div class='bfi-ccimages'>";
			foreach($ccimgages as $ccimgage){
				$currDesc .= '<i class="fa fa-cc-' . strtolower($ccimgage) . '" title="'. $ccimgage .'"></i>&nbsp;&nbsp;';
				$cCCTypeList[$ccimgage] = $ccimgage; // JHTML::_('select.option', $ccimgage, $ccimgage);
			}
			$currDesc .= "</div>";
 		}
//		if($bt->AcquireCreditCardData==1 && !BFCHelper::isUnderHTTPS() ){
//			continue;
//		}

		$bookingTypesoptions[$bt->BookingTypeId.":".$bt->AcquireCreditCardData] =  $currDesc;//  JHTML::_('select.option', $bt->BookingTypeId.":".$bt->AcquireCreditCardData, $currDesc );
		$calculatedBookingType = $bt;
		$calculatedBookingType->Deposit = 0;
		
		if(isset($calculatedBookingType->DepositRelativeValue) && !empty($calculatedBookingType->DepositRelativeValue)) {
			if($calculatedBookingType->DepositRelativeValue!='0' && $calculatedBookingType->DepositRelativeValue!='0%' && $calculatedBookingType->DepositRelativeValue!='100%')
			{
				if (strpos($calculatedBookingType->DepositRelativeValue,'%') !== false) {
					$calculatedBookingType->Deposit = (float)str_replace("%","",$calculatedBookingType->DepositRelativeValue) *(float) $totalRequest/100;
				}else{
					$calculatedBookingType->Deposit = $calculatedBookingType->DepositRelativeValue;
				}
			}
			if($calculatedBookingType->DepositRelativeValue==='100%'){
				$calculatedBookingType->Deposit = $totalRequest;
			}
		}

		$bookingTypesValues[$bt->BookingTypeId] = $calculatedBookingType;

		if($bt->IsDefault == true ){
			$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
			$deposit = $calculatedBookingType->Deposit;
			$bookingTypeIddefault = $bt->BookingTypeId;
		}

//		$bookingTypesDescArray[] = BFCHelper::getLanguage($bt->Description, $language);;
	}
//	$bookingTypesDesc = implode("|",$bookingTypesDescArray);

	if(empty($bookingTypedefault)){
		$bt = array_values($bookingTypesValues)[0];
		$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
		$deposit = $bt->Deposit;
		$bookingTypeIddefault = $bt->BookingTypeId;

	}

	if(!empty($bookingTypeFrpmForm)){
			if (array_key_exists($bookingTypeFrpmForm, $bookingTypesValues)) {
				$bt = $bookingTypesValues[$bookingTypeFrpmForm];
				$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
				$deposit = $bt->Deposit;
				$bookingTypeIddefault = $bt->BookingTypeId;
			}
	}

}

?>
<div class="bfi-payment-form bfi-form-field">
<div class="bf-title-book">
	<?php if($current_user->CustomerId <1) { ?>
	<a href="javascript:bfishowlogin()" class="bfi-btn bfi-alternative bfi-pull-right " ><?php _e('Log in to book faster', 'bfi') ?>
	  <span><i id="bfiarrowlogindisplay" class="fa fa-angle-right"></i></span>
	</a>
	<?php } ?>
	<?php _e('Enter your details', 'bfi') ?>
</div>
<div id="bfiLoginModule" style="display:<?php echo ($current_user->CustomerId>0)?"":"none"; ?>;">
	<?php bfi_get_template("widgets/login.php"); ?>
</div>
<form method="post" id="bfi-resourcedetailsrequest" class="form-validate" action="<?php echo $formRoute; ?>">
	<div class="bfi-mailalertform">
		<div class="bfi-row">
			<div class="bfi-col-md-6">
			<div class="bfi-clearfix">
				<label><?php _e('Name', 'bfi'); ?> *</label>
				<input type="text" value="<?php echo $current_user->Name ; ?>" size="50" name="form[Name]" id="Name" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div class="bfi-clearfix" >
				<label><?php _e('Surname', 'bfi'); ?> *</label>
				<input type="text" value="<?php echo $current_user->Surname ; ?>" size="50" name="form[Surname]" id="Surname" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div >
				<label><?php _e('Email', 'bfi'); ?> *</label>
				<input type="email" value="<?php echo $current_user->Email; ?>" size="50" name="form[Email]" id="formemail" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div >
				<label><?php _e('Reenter email', 'bfi'); ?> *</label>
				<input type="email" value="<?php echo $current_user->Email; ?>" size="50" name="form[EmailConfirm]" id="formemailconfirm" required equalTo="#formemail" title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			
						
			<div class="inputaddress" style="display:;">
				<div >
					<label><?php _e('Address', 'bfi'); ?> </label>
					<input type="text" value="<?php echo $current_user->MainAddress->Address; ?>" size="50" name="form[Address]" id="Address"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
				<div >
					<label><?php _e('Postal Code', 'bfi'); ?> </label>
					<input type="text" value="<?php echo $current_user->MainAddress->ZipCode; ?>" size="20" name="form[Cap]" id="Cap"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
				<div >
					<label><?php _e('Country', 'bfi'); ?> </label>
						<select id="formNation" name="form[Nation]" class="bfi_input_select width90percent">
							<option value="AR" <?php if(strtolower($current_user->MainAddress->Country) == "ar") {echo "selected";}?> >Argentina</option>
							<option value="AM" <?php if(strtolower($current_user->MainAddress->Country) == "am") {echo "selected";}?> >Armenia</option>
							<option value="AU" <?php if(strtolower($current_user->MainAddress->Country) == "au") {echo "selected";}?> >Australia</option>
							<option value="AZ" <?php if(strtolower($current_user->MainAddress->Country) == "az") {echo "selected";}?> >Azerbaigian</option>
							<option value="BE" <?php if(strtolower($current_user->MainAddress->Country) == "be") {echo "selected";}?> >Belgium</option>
							<option value="BY" <?php if(strtolower($current_user->MainAddress->Country) == "by") {echo "selected";}?> >Bielorussia</option>
							<option value="BA" <?php if(strtolower($current_user->MainAddress->Country) == "ba") {echo "selected";}?> >Bosnia-Erzegovina</option>
							<option value="BR" <?php if(strtolower($current_user->MainAddress->Country) == "br") {echo "selected";}?> >Brazil</option>
							<option value="BG" <?php if(strtolower($current_user->MainAddress->Country) == "bg") {echo "selected";}?> >Bulgaria</option>
							<option value="CA" <?php if(strtolower($current_user->MainAddress->Country) == "ca") {echo "selected";}?> >Canada</option>
							<option value="CN" <?php if(strtolower($current_user->MainAddress->Country) == "cn") {echo "selected";}?> >China</option>
							<option value="HR" <?php if(strtolower($current_user->MainAddress->Country) == "hr") {echo "selected";}?> >Croatia</option>
							<option value="CY" <?php if(strtolower($current_user->MainAddress->Country) == "cy") {echo "selected";}?> >Cyprus</option>
							<option value="CZ" <?php if(strtolower($current_user->MainAddress->Country) == "cz") {echo "selected";}?> >Czech Republic</option>
							<option value="DK" <?php if(strtolower($current_user->MainAddress->Country) == "dk") {echo "selected";}?> >Denmark</option>
							<option value="DE" <?php if(strtolower($current_user->MainAddress->Country) == "de") {echo "selected";}?> >Deutschland</option>
							<option value="EG" <?php if(strtolower($current_user->MainAddress->Country) == "eg") {echo "selected";}?> >Egipt</option>
							<option value="EE" <?php if(strtolower($current_user->MainAddress->Country) == "ee") {echo "selected";}?> >Estonia</option>
							<option value="FI" <?php if(strtolower($current_user->MainAddress->Country) == "fi") {echo "selected";}?> >Finland</option>
							<option value="FR" <?php if(strtolower($current_user->MainAddress->Country) == "fr") {echo "selected";}?> >France</option>
							<option value="GE" <?php if(strtolower($current_user->MainAddress->Country) == "ge") {echo "selected";}?> >Georgia</option>
							<option value="EN" <?php if(strtolower($current_user->MainAddress->Country) == "en") {echo "selected";}?> >Great Britain</option>
							<option value="GR" <?php if(strtolower($current_user->MainAddress->Country) == "gr") {echo "selected";}?> >Greece</option>
							<option value="HU" <?php if(strtolower($current_user->MainAddress->Country) == "hu") {echo "selected";}?> >Hungary</option>
							<option value="IS" <?php if(strtolower($current_user->MainAddress->Country) == "is") {echo "selected";}?> >Iceland</option>
							<option value="IN" <?php if(strtolower($current_user->MainAddress->Country) == "in") {echo "selected";}?> >Indian</option>
							<option value="IE" <?php if(strtolower($current_user->MainAddress->Country) == "ie") {echo "selected";}?> >Ireland</option>
							<option value="IL" <?php if(strtolower($current_user->MainAddress->Country) == "il") {echo "selected";}?> >Israel</option>
							<option value="IT" <?php if(strtolower($current_user->MainAddress->Country) == "it") {echo "selected";}?> >Italia</option>
							<option value="JP" <?php if(strtolower($current_user->MainAddress->Country) == "jp") {echo "selected";}?> >Japan</option>
							<option value="LV" <?php if(strtolower($current_user->MainAddress->Country) == "lv") {echo "selected";}?> >Latvia</option>
							<option value="LI" <?php if(strtolower($current_user->MainAddress->Country) == "li") {echo "selected";}?> >Liechtenstein</option>
							<option value="LT" <?php if(strtolower($current_user->MainAddress->Country) == "lt") {echo "selected";}?> >Lithuania</option>
							<option value="LU" <?php if(strtolower($current_user->MainAddress->Country) == "lu") {echo "selected";}?> >Luxembourg</option>
							<option value="MK" <?php if(strtolower($current_user->MainAddress->Country) == "mk") {echo "selected";}?> >Macedonia</option>
							<option value="MT" <?php if(strtolower($current_user->MainAddress->Country) == "mt") {echo "selected";}?> >Malt</option>
							<option value="MX" <?php if(strtolower($current_user->MainAddress->Country) == "mx") {echo "selected";}?> >Mexico</option>
							<option value="MD" <?php if(strtolower($current_user->MainAddress->Country) == "md") {echo "selected";}?> >Moldavia</option>
							<option value="NL" <?php if(strtolower($current_user->MainAddress->Country) == "nl") {echo "selected";}?> >Netherlands</option>
							<option value="NZ" <?php if(strtolower($current_user->MainAddress->Country) == "nz") {echo "selected";}?> >New Zealand</option>
							<option value="NO" <?php if(strtolower($current_user->MainAddress->Country) == "no") {echo "selected";}?> >Norvay</option>
							<option value="AT" <?php if(strtolower($current_user->MainAddress->Country) == "at") {echo "selected";}?> >Österreich</option>
							<option value="PL" <?php if(strtolower($current_user->MainAddress->Country) == "pl") {echo "selected";}?> >Poland</option>
							<option value="PT" <?php if(strtolower($current_user->MainAddress->Country) == "pt") {echo "selected";}?> >Portugal</option>
							<option value="RO" <?php if(strtolower($current_user->MainAddress->Country) == "ro") {echo "selected";}?> >Romania</option>
							<option value="SM" <?php if(strtolower($current_user->MainAddress->Country) == "sm") {echo "selected";}?> >San Marino</option>
							<option value="SK" <?php if(strtolower($current_user->MainAddress->Country) == "sk") {echo "selected";}?> >Slovakia</option>
							<option value="SI" <?php if(strtolower($current_user->MainAddress->Country) == "si") {echo "selected";}?> >Slovenia</option>
							<option value="ZA" <?php if(strtolower($current_user->MainAddress->Country) == "za") {echo "selected";}?> >South Africa</option>
							<option value="KR" <?php if(strtolower($current_user->MainAddress->Country) == "kr") {echo "selected";}?> >South Korea</option>
							<option value="ES" <?php if(strtolower($current_user->MainAddress->Country) == "es") {echo "selected";}?> >Spain</option>
							<option value="SE" <?php if(strtolower($current_user->MainAddress->Country) == "se") {echo "selected";}?> >Sweden</option>
							<option value="CH" <?php if(strtolower($current_user->MainAddress->Country) == "ch") {echo "selected";}?> >Switzerland</option>
							<option value="TJ" <?php if(strtolower($current_user->MainAddress->Country) == "tj") {echo "selected";}?> >Tagikistan</option>
							<option value="TR" <?php if(strtolower($current_user->MainAddress->Country) == "tr") {echo "selected";}?> >Turkey</option>
							<option value="TM" <?php if(strtolower($current_user->MainAddress->Country) == "tm") {echo "selected";}?> >Turkmenistan</option>
							<option value="US" <?php if(strtolower($current_user->MainAddress->Country) == "us") {echo "selected";}?> >USA</option>
							<option value="UA" <?php if(strtolower($current_user->MainAddress->Country) == "ua") {echo "selected";}?> >Ukraine</option>
							<option value="UZ" <?php if(strtolower($current_user->MainAddress->Country) == "uz") {echo "selected";}?> >Uzbekistan</option>
							<option value="VE" <?php if(strtolower($current_user->MainAddress->Country) == "ve") {echo "selected";}?> >Venezuela</option>
						</select>
				</div><!--/span-->
				<div class="bfi-vatcode-required">
					<label><?php _e('Fiscal code', 'bfi'); ?></label>
					<input type="text" value="<?php echo $current_user->VATNumber; ?>" size="20" name="form[VatCode]" id="VatCode" class="vatCode" title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
			</div>
	    </div>
	    <div class="bfi-col-md-6">
	         <div >
              <label><?php _e('Note', 'bfi'); ?></label>
              <textarea name="form[note]" class="bfi-col-md-12" style="height:104px;" ></textarea>    
            </div>
			<div >
				<label><?php _e('Phone', 'bfi'); ?> *</label>
				<input type="text" value="<?php echo $current_user->Phone; ?>" data-rule-minlength="4" size="20" name="form[Phone]" id="Phone" required  title="<?php _e('This field is required.', 'bfi') ?>" data-msg-minlength="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div >
				<label><?php _e('Your estimated time of arrival', 'bfi') ?></label>
				<select name="form[checkin_eta_hour]" class="bfi_input_select" >
					<option value="N.D."><?php _e('I don\'t know', 'bfi') ?></option>
					<option value="00.00 - 01.00">00:00 - 01:00</option>
					<option value="01.00 - 02.00">01:00 - 02:00</option>
					<option value="02.00 - 03.00">02:00 - 03:00</option>
					<option value="03.00 - 04.00">03:00 - 04:00</option>
					<option value="04.00 - 05.00">04:00 - 05:00</option>
					<option value="05.00 - 06.00">05:00 - 06:00</option>
					<option value="06.00 - 07.00">06:00 - 07:00</option>
					<option value="07.00 - 08.00">07:00 - 08:00</option>
					<option value="08.00 - 09.00">08:00 - 09:00</option>
					<option value="09.00 - 10.00">09:00 - 10:00</option>
					<option value="10.00 - 11.00">10:00 - 11:00</option>
					<option value="11.00 - 12.00">11:00 - 12:00</option>
					<option value="12.00 - 13.00">12:00 - 13:00</option>
					<option value="13.00 - 14.00">13:00 - 14:00</option>
					<option value="14.00 - 15.00">14:00 - 15:00</option>
					<option value="15.00 - 16.00">15:00 - 16:00</option>
					<option value="16.00 - 17.00">16:00 - 17:00</option>
					<option value="17.00 - 18.00">17:00 - 18:00</option>
					<option value="18.00 - 19.00">18:00 - 19:00</option>
					<option value="19.00 - 20.00">19:00 - 20:00</option>
					<option value="20.00 - 21.00">20:00 - 21:00</option>
					<option value="21.00 - 22.00">21:00 - 22:00</option>
					<option value="22.00 - 23.00">22:00 - 23:00</option>
					<option value="23.00 - 00.00">23:00 - 00:00</option>
					<!-- <option value="00:00 - 01:00 (del giorno dopo)">00:00 - 01:00 (del giorno dopo)</option>
					<option value="01:00 - 02:00 (del giorno dopo)">01:00 - 02:00 (del giorno dopo)</option> -->
				</select>
			</div><!--/span-->
			<div class="bfi-hide">
				<label><?php _e('Password', 'bfi') ?> *</label>
				<input type="password" value="<?php echo $current_user->Email; ?>" size="50" name="form[Password]" id="Password"   title="">
			</div><!--/span-->
				<div >
					<label><?php _e('City', 'bfi'); ?> </label>
					<input type="text" value="<?php echo $current_user->MainAddress->City ; ?>" size="50" name="form[City]" id="City"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
				<div >
					<label><?php _e('Province', 'bfi'); ?> </label>
					<input type="text" value="<?php echo $current_user->MainAddress->Province ; ?>" size="20" name="form[Provincia]" id="Provincia"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
		</div>
	</div>
<!-- VIEW_ORDER_PAYMENTMETHOD -->
		<div class="bfi-paymentoptions" style="display:none;" id="bfi-bookingTypesContainer">
			<h2><?php _e('Payment method', 'bfi') ?></h2>
			<p><?php _e('Please choose a payment method', 'bfi') ?>:
				<?php echo implode(", ", $allResourceBookable) ?>	
			</p>
			<?php  foreach ($bookingTypesoptions as $key => $value) { ?>
				<label for="form[bookingType]<?php echo $key ?>" id="form[bookingType]<?php echo $key ?>-lbl" class="radio">	
					<input type="radio" name="form[bookingType]" id="form[bookingType]<?php echo $key ?>" value="<?php echo $key ?>" <?php echo $bookingTypedefault == $key ? 'checked="checked"' : "";  ?>  ><?php echo $value ?>
				</label>
			<?php } ?>
		</div>
		<div class="bfi-paymentoptions" id="bfi-bookingTypesDescriptionContainer">
			<h2 id="bookingTypeTitle"></h2>
			<span id="bookingTypeDesc"></span>
			<div id="totaldepositrequested" class="bfi-pad0-10" style="display:none;">
				<span class="text-nowrap bfi-summary-body-resourceprice-total"><?php _e('Deposit', 'bfi') ?></span>	
				<span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"  id="totaldeposit"></span>	
			</div>	
		</div>
		<div class="bfi-clearfix"></div>

<div style="display:none;" id="bfi-ccInformations" class="borderbottom paymentoptions">
		<h2><?php _e('Credit card details', 'bfi') ?></h2>
<?php if($showCC) { ?>
		<div><?php _e('Warranty Request for', 'bfi') ?>: 
		<?php echo implode(", ", $allResourceNoBookable) ?>
		</div>
<?php } ?>
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php _e('Type', 'bfi') ?> </label>
					<select id="formcc_circuito" name="form[cc_circuito]" class="bfi_input_select">
						<?php 
							foreach($cCCTypeList as $ccCard) {
								?><option value="<?php echo $ccCard ?>"><?php echo $ccCard ?></option><?php 
							}
						?> 
					</select>
			</div>
			<div class="bfi-col-md-6">
				<label><?php _e('Holder', 'bfi') ?> </label>
				<input type="text" value="" size="50" name="form[cc_titolare]" id="cc_titolare" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div>
		</div>
		<div class="bfi-row bfi-payment-form">
			<div class="bfi-col-md-6">
				<label><?php _e('Number', 'bfi') ?> </label>
				<input type="text" value="" size="50" maxlength="50" name="form[cc_numero]" id="cc_numero" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div>
			<div class="bfi-col-md-6">
				<label><?php _e('Valid until', 'bfi') ?></label>
				<div class="bfi-ccdateinput">
						<span><?php _e('Month (MM)', 'bfi') ?></span> <span><input type="text" value="" size="2" maxlength="2" name="form[cc_mese]" id="cc_mese" required  title="<?php _e('This field is required.', 'bfi') ?>"></span>
						/
						<span><input type="text" value="" size="2" maxlength="2" name="form[cc_anno]" id="cc_anno" required  title="<?php _e('This field is required.', 'bfi') ?>"></span> <span><?php _e('Year (YY)', 'bfi') ?></span>
				</div><!--/span-->
			</div>
		</div>
		<br />
		<div class="bfi-row ">   
			  <div class="bfi-col-md-2">
				 <?php echo $ssllogo ?>
			  </div>
		</div>

</div>	
<?php
if(!empty($currPolicy)) {

$policyHelp = "";
$policy = $currPolicy;
if(!empty( $policy )){
	$currValue = $policy->CancellationBaseValue;
	$policyId= $policy->PolicyId;

	switch (true) {
		case strstr($policy->CancellationBaseValue ,'%'):
			$currValue = $policy->CancellationBaseValue;
			break;
		case strstr($policy->CancellationBaseValue ,'d'):
			$currValue = sprintf(__(' %d day/s' ,'bfi'),rtrim($policy->CancellationBaseValue,"d"));
			break;
		case strstr($policy->CancellationBaseValue ,'n'):
			$currValue = sprintf(__(' %d day/s' ,'bfi'),rtrim($policy->CancellationBaseValue,"n"));
			break;
	}
	$currValuebefore = $policy->CancellationValue;
	switch (true) {
		case strstr($policy->CancellationValue ,'%'):
			$currValuebefore = $policy->CancellationValue;
			break;
		case strstr($policy->CancellationValue ,'d'):
			$currValuebefore = sprintf(__(' %d day/s' ,'bfi'),rtrim($policy->CancellationValue,"d"));
			break;
		case strstr($policy->CancellationValue ,'n'):
			$currValuebefore = sprintf(__(' %d day/s' ,'bfi'),rtrim($policy->CancellationValue,"n"));
			break;
		default:
			$currValuebefore = '<span class="bfi_' . $currencyclass .'">'. BFCHelper::priceFormat($policy->CancellationValue) .'</span>' ;
	}
}
?>
			<div class=" bfi-checkbox-wrapper">
					<input name="form[accettazionepolicy]" class="checkbox" id="agreepolicy" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php _e('Mandatory', 'bfi') ?>">
					<label class="bfi-shownextelement"><?php _e('I agree to the conditions', 'bfi') ?></label>
					<div class="bfi-policies">
<?php
	$currMerchantName="";
	foreach ($allPolicies as  $key => $currSinglePolicy) { 
		$currPolicyDescription = "";
		foreach ($currPolicies as $currPolicy) { 
			if($currPolicy->PolicyId == $currSinglePolicy['policyid']){
				$currPolicyDescription = $currPolicy->Description;
				 break;
			}
		}
		if ($currSinglePolicy['merchantname']!=$currMerchantName) {
		    $currMerchantName = $currSinglePolicy['merchantname'];
			?>
			<div class="bfi-merchantname"><?php echo $currMerchantName ?></div>
			<?php 
		}
		?>
				<div class="bfi-resourcename"><?php echo $key+1 ?>) <?php echo $currSinglePolicy['resourcename'] ?>:</div>
				<p>
				<?php echo $currSinglePolicy['policyHelp'] ?><br />
				<?php echo $currPolicyDescription?><br />

				</p>

		<?php 
	}

					?>
<textarea name="form[policy]" class="bfi-col-md-12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php
if (count($allPolicyHelp)>0) {
	foreach ($allPolicyHelp as $key => $value) { 
		echo ($key+1) . ") " . $value . "\r\n";
	}
}
?>

<?php echo $currPoliciesDescriptions; ?></textarea>
		</div>
		<div class="bfi-clearfix"></div>
		<?php } ?>
		<div class=" bfi-checkbox-wrapper">
			<input name="form[optinemail]" id="optinemail" type="checkbox">
			<label for="optinemail"><?php echo sprintf(__('Send me promotional emails from %1$s', 'bfi'),$sitename) ?></label>
		</div>

		<?php bfi_display_captcha($idrecaptcha);  ?>
<div id="recaptcha-error-<?php echo $idrecaptcha ?>" style="display:none"><?php _e('Mandatory', 'bfi') ?></div>

		<input type="hidden" id="actionform" name="actionform" value="<?php echo $formlabel ?>" />
		<input type="hidden" name="form[merchantId]" value="" /> 
		<input type="hidden" id="orderType" name="form[orderType]" value="a" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language; ?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="label" name="form[label]" value="<?php echo $formlabel ?>">
		<input type="hidden" id="resourceId" name="form[resourceId]" value="" /> 
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks; ?>">
		<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
		<input type="hidden" id="stayrequest" name="form[stayrequest]" value="<?php //echo $stayrequest ?>">
		<input type="hidden" id="staysuggested" name="form[staysuggested]" value="<?php //echo $staysuggested ?>">
		<input type="hidden" id="isgateway" name="form[isgateway]" value="0" />
		<input type="hidden" name="form[hdnOrderData]" id="hdnOrderData" value='<?php echo $currCart->CartConfiguration ?>' />
		<input type="hidden" name="form[hdnOrderDataCart]" id="hdnOrderDataCart" value='<?php echo $currCart->CartConfiguration ?>' />
		<input type="hidden" name="form[bookingtypeselected]" id="bookingtypeselected" value='<?php echo $bookingTypeIddefault ?>' />
		<input type="hidden" id="CartId" name="form[CartId]" value="<?php echo isset($currCart->CartId)?$currCart->CartId:''; ?>">
		<input type="hidden" id="policyId" name="form[policyId]" value="<?php echo $currPolicyId?>">

		</div>

		<div class="bfi-row bfi-footer-book" >
			<div class="bfi-col-md-10">
			<?php echo $infoSendBtn ?>
			</div>
			<div class="bfi-col-md-2 bfi_footer-send"><button type="submit" id="btnbfFormSubmit" class="bfi-btn" style="display:none;"><?php _e('Send', 'bfi') ?></button></div>
		</div>

<?php
$selectedSystemType = array_values(array_filter($bookingTypesValues, function($bt) use($bookingTypedefault) {return $bt->BookingTypeId == $bookingTypedefault;}));
$currSelectedSystemType = 0;
if(!empty( $selectedSystemType )){
	$currSelectedSystemType = $selectedSystemType[0]->PaymentSystemRefId;
}
?>
<script type="text/javascript">
<!--
var bookingTypesValues = null;

var completeStay = <?php echo $currCart->CartConfiguration; ?>;
var bfiMerchants = <?php echo json_encode($merchantDetail) ?>;
var selectedSystemType = "<?php echo $currSelectedSystemType; ?>";
var allCartItems = [];
var bfiAllDiscountCodes = <?php echo json_encode($allDiscountCodes) ?>;

jQuery(function($)
		{

			jQuery('#bfiaddcoupon').on("click", function(e){
				var currCode= jQuery("#bficoupons").val();
				if(bfiAllDiscountCodes.length>0 && jQuery.inArray(currCode, bfiAllDiscountCodes) !== -1){
					alert("<?php _e('Code already used', 'bfi') ?>");
					jQuery("#bficoupons").val('');
					return false;
				}
				return true;
			});
			
			jQuery('.bfi-cartform-delete').on("submit", function(e){
				e.preventDefault();
					var conf = confirm('<?php _e('Are you sure?', 'bfi') ?>');
					if (conf)
					{
					<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1){ ?>
						var currForm = jQuery(this);	
						var currRes= jQuery.parseJSON(currForm.find("input[name=bficurrRes]").val());
						var allDelItems = [ {
										"id": currRes.ResourceId + " - Resource",
										"name": currRes.Name ,
										"category": bfiMerchants[currRes.MerchantId].MainCategoryName,
										"brand": bfiMerchants[currRes.MerchantId].Name,
										"price": currRes.TotalDiscounted,
										"quantity": currRes.SelectedQt,
										"variant": currRes.RatePlanName.toUpperCase(),
									}];
						if(currRes.ExtraServices.length>0){
							for (i in currRes.ExtraServices) {
								currRes.ExtraServices[i].category = bfiMerchants[currRes.MerchantId].MainCategoryName;
								currRes.ExtraServices[i].brand = bfiMerchants[currRes.MerchantId].Name;
								currRes.ExtraServices[i].variant = currRes.RatePlanName.toUpperCase();
							}
							var allDelSrvItems = jQuery.makeArray(jQuery.map(currRes.ExtraServices, function(elm) {
											return {
												"id": elm.PriceId + " - Service",
												"name": elm.Name ,
												"category": elm.category,
												"brand": elm.brand,
												"price": elm.TotalDiscounted,
												"quantity": elm.CalculatedQt,
												"variant": elm.variant,
											};
										}));
							jQuery.merge( allDelItems, allDelSrvItems );
						}
						callAnalyticsEEc("addProduct", allDelItems, "removefromcart", "", {
							"step": 1
						},
						"Add to Cart"
					);

					<?php } ?>
						 this.submit();
					//form.submit();
					}
					return conf;
				});

			jQuery('.bfi-options-help i').webuiPopover({trigger:'hover',placement:'left-bottom',style:'bfi-webuipopover'});
			jQuery('.bfi-coupon-details').webuiPopover({trigger:'hover',placement:'left-bottom',style:'bfi-webuipopover'});

			var allItems = jQuery.makeArray(jQuery.map(completeStay.Resources, function(elm) {
							return {
								"id": elm.ResourceId + " - Resource",
								"name": elm.Name ,
								"category": bfiMerchants[elm.MerchantId].MainCategoryName,
								"brand": bfiMerchants[elm.MerchantId].Name,
								"price": elm.TotalDiscounted,
								"quantity": elm.SelectedQt,
								"variant": elm.RatePlanName.toUpperCase(),
							};
						}));
			var allSrvItems = jQuery.makeArray(jQuery.map(jQuery.map(jQuery.grep(completeStay.Resources, function(res) {
				return res.ExtraServices.length>0;
			}), function(resserv) {
							for (i in resserv.ExtraServices) {
								resserv.ExtraServices[i].category = bfiMerchants[resserv.MerchantId].MainCategoryName;
								resserv.ExtraServices[i].brand = bfiMerchants[resserv.MerchantId].Name;
								resserv.ExtraServices[i].variant = resserv.RatePlanName.toUpperCase();
							}
							return resserv.ExtraServices;
			}), function(elm) {
							return {
								"id": elm.PriceId + " - Service",
								"name": elm.Name ,
								"category": elm.category,
								"brand": elm.brand,
								"price": elm.TotalDiscounted,
								"quantity": elm.CalculatedQt,
								"variant": elm.variant,
							};
						}));
			allCartItems = jQuery.merge( jQuery.merge( [], allItems ), allSrvItems );

//			var svcTotal = 0;
//			var allItems = jQuery.makeArray(jQuery.map(jQuery.grep(completeStay.Resources, function(svc) {
//				return svc.Tag == "ExtraServices";
//			}), function(svc) {
//				svcTotal += svc.TotalDiscounted;
//				return {
//					"id": "" + svc.PriceId + " - Service",
//					"name": svc.Name,
//					"category": "Services",
//					"brand": "<?php //echo $merchant->Name?>",
//					"price": (svc.TotalDiscounted / svc.CalculatedQt).toFixed(2),
//					"quantity": svc.CalculatedQt
//				};
//			}));
//			/*
//			jQuery.each(allItems, function(svc) {
//				svcTotal += prc.TotalDiscounted;
//			});
//			*/
//			allItems.push({
//				"id": "<?php //echo $resource->ResourceId?> - Resource",
//				"name": "<?php //echo $resource->Name?>",
//				"category": "<?php //echo $resource->MerchantCategoryName?>",
//				"brand": "<?php //echo $merchant->Name?>",
//				"variant": completeStay.RefId ? completeStay.RefId.toUpperCase() : "",
//				"price": completeStay.TotalDiscounted - svcTotal,
//				"quantity": 1
//			});
			
			<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
				callAnalyticsEEc("addProduct", allCartItems, "checkout", "", {
					"step": 1
				});
			<?php endif;?>

			jQuery("#btnbfFormSubmit").show();


			$(".bfi-shownextelement").click(function(){
				$(this).next().toggle();
			});
			
			<?php if(!empty($bookingTypesValues)) { ?>
			bookingTypesValues = <?php echo json_encode($bookingTypesValues) ?>;// don't use quotes
			<?php } ?>
			$("#bfi-resourcedetailsrequest").validate(
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
					},
//					"form[ConfirmEmail]": {
//					  email: true,
//					  required: true,
//					  equalTo: "form[Email]"
//					},
				},
		        messages:
		        {
		        	"form[VatCode]" : {
		        		required: "<?php _e('Mandatory', 'bfi') ?>",
		        		vatCode: "<?php _e('Please enter a valid code', 'bfi') ?>"
						},
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
//				errorPlacement: function(error, element) {
//					// Append error within linked label
//					$( element )
//						.closest( "form" )
//							.find( "label[for='" + element.attr( "id" ) + "']" )
//								.append( error );
//				},
//				errorElement: "span",
				errorClass: "bfi-error",
				highlight: function(label) {
			    	$(label).removeClass('bfi-error').addClass('bfi-error');
			    	$(label).closest('.control-group').removeClass('bfi-error').addClass('bfi-error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
					$(label).remove();
//					$(label).hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {
					var $form = $(form);
					if($form.valid()){
						if (typeof grecaptcha === 'object') {
							var response = grecaptcha.getResponse(window.bfirecaptcha['<?php echo $idrecaptcha ?>']);
							//recaptcha failed validation
							if(response.length == 0) {
								$('#recaptcha-error-<?php echo $idrecaptcha ?>').show();
								return false;
							}
							//recaptcha passed validation
							else {
								$('#recaptcha-error-<?php echo $idrecaptcha ?>').hide();
							}					 
						}
						bookingfor.waitBlockUI();
//						jQuery.blockUI({message: ''});
						if ($form.data('submitted') === true) {
							 return false;
						} else {
							// Mark it so that the next submit can be ignored
							$form.data('submitted', true);
							
							<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
							callAnalyticsEEc("addProduct", allCartItems, "checkout", "", {
								"step": 2,
							});
							
							callAnalyticsEEc("addProduct", allCartItems, "checkout_option", "", {
								"step": 2,
								"option": selectedSystemType
							});
							<?php endif; ?>
							form.submit();
						}					}

				}

			});
			$("input[name='form[bookingType]']").change(function(){
				var currentSelected = $(this).val().split(':')[0];
				selectedSystemType = Object.keys(bookingTypesValues).indexOf(currentSelected) > -1 ? bookingTypesValues[currentSelected].PaymentSystemRefId : "";
				checkBT();
			});
			var bookingTypeVal= $("input[name='form[bookingType]']");
			var container = $('#bfi-bookingTypesContainer');
			if(bookingTypeVal.length>0 && container.length>0){
					container.show();
			}
			function checkBT(){
					var ccInfo = $('#bfi-ccInformations');
					if (ccInfo.length>0) {
						try
						{
							var currCC = $("input[name='form[bookingType]']:checked");
							if (!currCC.length) {
								currCC = $("input[name='BookingType']")[0];
								$(currCC).prop("checked", true);
							}
							if ($(currCC).length>0)
							{
								var cc = $(currCC).val();
								var ccVal = cc.split(":");
								var reqCC = ccVal[1];
								if (reqCC || <?php echo ($showCC) ?"true":"false";
								 ?>) { 
									ccInfo.show();
								} else {
									ccInfo.hide();
								}
								var idBT = ccVal[0];
								$("#bookingtypeselected").val(idBT);

								$.each(bookingTypesValues, function(key, value) {
									if(idBT == value.BookingTypeId){
	//									$("#bookingTypeTitle").html(value.Name);
	//									$("#bookingTypeDesc").html(value.Description);
										if(value.Deposit!=null && value.Deposit!='0'){
											
											$("#totaldepositrequested").show();
											$("#totaldeposit").html(bookingfor.priceFormat(value.Deposit, 2, '.', ''));

	//										$("#bf-summary-footer-deposit").show();
	//										$("#footer-deposit").html(value.Deposit);

											$("#isgateway").val("0");
											if(value.DeferredPayment=='0' || value.DeferredPayment==false){
												$("#isgateway").val(value.IsGateway ? "1" : "0");
											}
											return false;
										}else{
											$("#isgateway").val("0");
											$("#totaldepositrequested").hide();
										}
									}
								});	
							}else{
								if (<?php echo ($showCC) ?"true":"false"; ?>) { 
									ccInfo.show();
								} else {
									ccInfo.hide();
								}
							}
						}
						catch (err)
						{
							alert(err)
						}
					}
			}
			function checkVatRequired(){
				if (jQuery("#formNation").val() == "IT")
				{
					jQuery(".bfi-vatcode-required").show();
				}else{
					jQuery(".bfi-vatcode-required").hide();
				}
			}
			jQuery("#formNation").change(function(){ checkVatRequired();});
			checkBT();
			checkVatRequired();
		});

var bfisrv = [];
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
var listServiceIds = "<?php echo implode(",", $allServiceIds) ?>";
var bfisrvloaded=false;
var resGrp = [];
var loadedResGrp=false;
var shortenOption = {
		moreText: "<?php _e('Read More', 'bfi'); ?>",
		lessText: "<?php _e('Read Less', 'bfi'); ?>",
		showChars: '150'
};

function getAjaxInformationsResGrp(){
	if (!loadedResGrp)
	{
		loadedResGrp=true;
		var queryMG = "task=getResourceGroups";
		jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(JSON.parse(data) || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name,bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);
							/*--------getName----*/
							resGrp[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
						} else {
							if (val.IconSrc != null && val.IconSrc != '') {
								resGrp[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
							}
						}
					});	
					bfiUpdateInfoResGrp();
				}
				jQuery('[data-toggle="tooltip"]').tooltip({
					position : { my: 'center bottom', at: 'center top-10' },
					tooltipClass: 'bfi-tooltip bfi-tooltip-top '
				}); 

		},'json');
	}
}
function bfiUpdateInfoResGrp(){
	jQuery(".bfiresourcegroups").each(function(){
		var currList = jQuery(this).attr("rel");
		if (currList!= null && currList!= '')
		{
			var srvlist = currList.split(',');
			var srvArr = [];
			jQuery.each(srvlist, function(key, srvid) {
				if(typeof resGrp[srvid] !== 'undefined' ){
					srvArr.push(resGrp[srvid]);
				}
			});
			jQuery(this).html(srvArr.join(" "));
		}

	});
}


function getAjaxInformationsSrv(){
	if (!bfisrvloaded)
	{
		bfisrvloaded=true;
		if(listServiceIds!=""){
			var querySrv = "task=GetServicesByIds&ids=" + listServiceIds + "&language=<?php echo $language ?>";
			jQuery.post(bfi_variable.bfi_urlCheck, querySrv, function(data) {
				if(data!=null){
					jQuery.each(data, function(key, val) {
						bfisrv[val.ServiceId] = val.Name ;
					});	
					bfiUpdateInfo();
				}
			},'json');
		}
	}
}

function bfiUpdateInfo(){
	jQuery(".bfisimpleservices").each(function(){
		var currList = jQuery(this).attr("rel");
		if (currList!= null && currList!= '')
		{
			var srvlist = currList.split(',');
			var srvArr = [];
			jQuery.each(srvlist, function(key, srvid) {
				if(typeof bfisrv[srvid] !== 'undefined' ){
					srvArr.push(bfisrv[srvid]);
				}
			});
			jQuery(this).html(srvArr.join(", "));
		}
	});
	jQuery(".bfisimpleservices").shorten(shortenOption);
}

function bfishowlogin(){
	jQuery("#bfiLoginModule").toggle();
	if (jQuery("#bfiLoginModule").css('display') != 'none')
	{
		jQuery("#bfiarrowlogindisplay").removeClass("fa-angle-right");
		jQuery("#bfiarrowlogindisplay").addClass("fa-angle-down");
	}else{
		jQuery("#bfiarrowlogindisplay").addClass("fa-angle-right");
		jQuery("#bfiarrowlogindisplay").removeClass("fa-angle-down");
	}

}
jQuery(document).ready(function () {
	jQuery(".bfi-description").shorten(shortenOption);
	getAjaxInformationsSrv();
	getAjaxInformationsResGrp();

});
	//-->
	jQuery(window).load(function() {
		if (!!jQuery.uniform){
		jQuery.uniform.restore(jQuery('#bfi-resourcedetailsrequest input[type="checkbox"]'));
			jQuery.uniform.restore(jQuery("#bfi-resourcedetailsrequest select"));
		}
	});


	</script>	
</form>
</div>		
</div>		
		<?php 
		
		}
//}else{
//	echo __('Cart Not enabled! ', 'bfi');
}

?>
	<?php
		/**
		 * bookingfor_after_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'bookingfor_after_main_content' );
	?>

	<?php
		/**
		 * bookingfor_sidebar hook.
		 *
		 * @hooked bookingfor_get_sidebar - 10
		 */
//		do_action( 'bookingfor_sidebar' );
	?>

<?php get_footer( ); ?>
