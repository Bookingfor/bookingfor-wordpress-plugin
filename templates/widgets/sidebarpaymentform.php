<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
$base_url = get_site_url(null,'', $usessl ? "https" : null);

$merchant = $resource->Merchant;
$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );
$resourceName = BFCHelper::getLanguage($resource->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
$route = $url_resource_page.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);

$currencyclass = bfi_get_currentCurrency();

if(empty(BFI()->currentOrder)){	
	$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
	$order = BFCHelper::calculateOrder($OrderJson,$language);
	BFI()->currentOrder = $order;
}

$OrderDetail = BFI()->currentOrder;

$showCheckout = false;
if (count($OrderDetail->Resources) > 0)
{
	$AnyResources = array_filter($OrderDetail->Resources, function($o) {
								return $o->TimeSlotId == null || $o->CheckInTime == null;
							});
	$showCheckout = count($AnyResources) > 0 ;
}

//
//$extras ="";
//	if (isset($_POST["extras"]))
//	{
////		$extras = $_POST["extras"];
//		$extras = implode('|',
//			array_filter($_POST["extras"], function($var) {
//					$vals = explode(':', $var);
//					if (count($vals) < 2 || $vals[1] == '') return false;
//					return true;
//				})
//			);
//	} 
//	
//	$_SESSION['search.params']['checkin'] = DateTime::createFromFormat('d/m/Y', $_POST['checkin']);
//	$_SESSION['search.params']['checkout'] = DateTime::createFromFormat('d/m/Y', $_POST['checkout']);
//	$interval = $_SESSION['search.params']['checkout']->diff($_SESSION['search.params']['checkin']);
//	$_SESSION['search.params']['duration'] = $interval->d;
//	$_SESSION['search.params']['paxages'] = BFCHelper::calculate_paxages($_POST, $_POST['adults'], $_POST['children'], $_POST['seniors']);
//	$_SESSION['search.params']['paxes'] = $_POST['adults'] + $_POST['children'] + $_POST['seniors'];
//	$_SESSION['search.params']['pricetype'] = $_POST['pricetype'];
//	$_SESSION['search.params']['extras'] = $extras;
////	var_dump($_SESSION['search.params']['extras']);
//	$_SESSION['search.params']['state'] = $_POST['state'];
//
//
//$rateplan_id = $_SESSION['search.params']['pricetype'] = $_POST['pricetype'] ;
//
//
//$pars = BFCHelper::getSearchParamsSession();
//
//$model= new BookingForConnectorModelResource;
//$resource_id = get_query_var( 'resource_id', 0 );
//	$model->setResourceId($resource_id);
//
//			$allstays = $model->getStay($language,null,true);
////			$allstays = $model->getStay(isset($this->params['refreshcalc']));
//			
//			$stay = null;
//			
//		if(is_array($allstays) && (!isset($pars['pricetype']) || empty($pars['pricetype']))) {
//				$selStay = array_values(array_filter($allstays, function($st) {
//					return $st->SuggestedStay->Available && $st->TotalAmount > 0;
//				}));
//				if(count($selStay) > 0) {
//					$pars['pricetype'] = $selStay[0]->RatePlanId;
//				}
//			}
//			
//			if(is_array($allstays) && isset($pars['pricetype'])) {
//				$selStay = array_values(array_filter($allstays, function($st) use ($pars) {
//					return $pars['pricetype'] == $st->RatePlanId;
//				}));
//				if(count($selStay) > 0) {
//					$stay = $selStay[0];
//				}
//			}
//				$total = 0;
//				$totalDiscounted = 0;
//				$totalWithVariation = 0;
//
//				$newAllStays = array();
//		foreach ($allstays as $rs) {
//			//$rs = $ratePlanStay;
//			
////			echo "<pre>";
////			echo print_r($rs);
////			echo "</pre>";
//			
//			if ($rs != null) {
//				$rs->CalculatedPricesDetails = json_decode($rs->CalculatedPricesString);
//				$rs->SelectablePrices = json_decode($rs->CalculablePricesString);
//				$rs->CalculatedPackages = json_decode($rs->PackagesString);
//				$rs->DiscountVariation = null;
//				
//				// only here
//				$rs->IsBase = $rs->IsBase;
//				$rs->BookingType =0;
//				if(!empty($rs->SuggestedStay)){
//					$rs->BookingType = $rs->SuggestedStay->BookingType;
//				}
//
//				if(!empty($rs->Discount)){
//					$rs->DiscountVariation = $rs->Discount;
//
//				}
//				$rs->SupplementVariation =null;
//				if(!empty($rs->Supplement)){
//					$rs->SupplementVariation = $rs->Supplement;
//				}
//					
//				$allVar = json_decode($rs->AllVariationsString);
//				$rs->Variations= [];
//				$rs->SimpleDiscountIds = [];
//				
//				if(!empty($allVar)){
//					foreach ($allVar as $currVar) {
//						$rs->Variations[] = $currVar;
//						$rs->SimpleDiscountIds[] = $currVar->VariationPlanId;
//						/*if(empty($currVar->IsExclusive)){
//						}*/
//					}
//				}
//			}
//			$newAllStays[] = $rs;
//		}
//				if(!empty($stay)){
//					$resource->Availability = $stay->SuggestedStay->Availability;
//					$stay->CalculatedPricesDetails = json_decode($stay->CalculatedPricesString);
//					$stay->SelectablePrices = json_decode($stay->CalculablePricesString);
//					$stay->CalculatedPackages = json_decode($stay->PackagesString);
//					$stay->DiscountVariation = null;
//
//					// only here
//					$resource->BookingType = $stay->SuggestedStay->BookingType;
//
//					if(!empty($stay->Discount)){
//						$stay->DiscountVariation = $stay->Discount;
//
//					}
//					$stay->SupplementVariation =null;
//					if(!empty($stay->Supplement)){
//						$stay->SupplementVariation = $stay->Supplement;
//					}
//						
//					$allVar = json_decode($stay->AllVariationsString);
//					$stay->Variations= [];
//					$stay->SimpleDiscountIds = [];
//					foreach ($allVar as $currVar) {
//							$stay->Variations[] = $currVar;
//							$stay->SimpleDiscountIds[] = $currVar->VariationPlanId;
//						/*if(empty($currVar->IsExclusive)){
//						}*/
//					}
//					if(isset($stay->BookingTypes)) {
//						$item->MerchantBookingTypes  = $stay->BookingTypes;
//					}
//					if(isset($stay->SelectablePrices)) {
//						$Extras =  $stay->SelectablePrices;
//					}
//					$suggestedStay = $stay->SuggestedStay;
//
//					if(!empty($suggestedStay->TotalPrice)){
//						$total = (float)$suggestedStay->TotalPrice;
//					}
//					if(!empty($suggestedStay->DiscountedPrice)){
//						$totalDiscounted = (float)$suggestedStay->DiscountedPrice;
//					}
//					$totalWithVariation = $totalDiscounted;
//					//TODO: cosa farne dei pacchetti nel calcolo singolo della risorsa?
//					/*
//					if(!empty($stay->CalculatedPackages)){
//						foreach ($stay->CalculatedPackages as $pkg) {
//							//$totalDiscounted = $totalDiscounted + $pkg->SuggestedStay->DiscountedPrice;
//							//$total = $total + $pkg->SuggestedStay->DiscountedPrice;
//							
//							
//							$totalDiscounted = $totalDiscounted + $pkg->DiscountedAmount;
//							$total = $total + $pkg->DiscountedAmount;
//						}
//					}
//					*/
////					if(!empty($stay->Variations)){
////						foreach ($stay->Variations as $var) {
////							$var->TotalPackagesAmount = 0;
////							foreach ($stay->CalculatedPackages as $pkg) {
////								foreach($pkg->Variations as $pvar) {
////									if($pvar->VariationPlanId == $var->VariationPlanId){
////										$var->TotalPackagesAmount +=  $pvar->TotalAmount;
////										break;
////									}
////								}
////							}
////						}
////					}
//				}
//
///*--------------------------------------------------------------------------------------------------------------------------------------------------------------*/
//$deposit = 0;
//
//
//
$nad = $OrderDetail->SearchModel->adults;
$nch = $OrderDetail->SearchModel->children;
$nse =  $OrderDetail->SearchModel->seniors;
$countPaxes = $nad + $nch + $nse;
$nchs = array($OrderDetail->SearchModel->childages1,$OrderDetail->SearchModel->childages2,$OrderDetail->SearchModel->childages3,$OrderDetail->SearchModel->childages4,$OrderDetail->SearchModel->childages5,null);
//$nchs = array_filter($nchs);
$nchs = array_slice($nchs,0,$nch);

//
setlocale(LC_TIME, $language);
//
//$paxages = $_SESSION['search.params']['paxages'];
//
//if (empty($paxages)){
//	$nad = $minperson;
//}else{
//	if(is_array($paxages)){
//		$countPaxes = array_count_values($paxages);
//		$nchs = array_values(array_filter($paxages, function($age) {
//			if ($age < (int)BFCHelper::$defaultAdultsAge)
//				return true;
//			return false;
//		}));
//	}
//}
////array_push($nchs, null,null,null,null,null,null);
//
//if($countPaxes>0){
//	foreach ($countPaxes as $key => $count) {
//		if ($key >= BFCHelper::$defaultAdultsAge) {
//			if ($key >= BFCHelper::$defaultSenioresAge) {
//				$nse += $count;
//			} else {
//				$nad += $count;
//			}
//		} else {
//			$nch += $count;
//		}
//	}
//}
//
//$completestay = $stay;
//if(!empty($completestay->SuggestedStay)){
//	$stay = $completestay->SuggestedStay;
//}
//$allStaysToView = array();
//$allstaysuggested= array(); //used in form to get all data 
//
//
//$hasResourceStay = false;
//
//if(isset($newAllStays)) {
//	$hasResourceStay = true;
//	foreach($newAllStays as $rst) {
//
//		$allStaysToView = pushStay($allStaysToView, $resource->ResourceId, $rst, $resource,$allstaysuggested);
//	}
//}
//
//$stayAvailability = 0;
//$DiscountedPrice=0;
//if(!empty($stay)){
//	$stayAvailability = $stay->Availability;
//	$DiscountedPrice = (float)$stay->DiscountedPrice;
//}
//
//$showQuote = ($totalDiscounted > 0 && $stayAvailability > 0);
//
//$bookingTypeFrpmForm = $_REQUEST['bookingType'];
//
//$bookingTypedefault=0;
//$bookingTypesValues = array();
//$bookingTypes = $model->getMerchantBookingTypesFromService();
//
//if(!empty($bookingTypes)){
//	foreach($bookingTypes as $bt)
//	{
//
//		$calculatedBookingType = $bt;
//		$bookingTypesValues[$bt->BookingTypeId] = $calculatedBookingType;
//
//		if($bt->IsDefault == true ){
//			$bookingTypedefault = $bt->BookingTypeId;
//		}
//
//	}
//
//	if(empty($bookingTypedefault)){
//		$bt = array_values($bookingTypesValues)[0]; 
//		$bookingTypedefault = $bt->BookingTypeId;
//	}
//}
//
//if(!empty($bookingTypeFrpmForm)){
//		if (array_key_exists($bookingTypeFrpmForm, $bookingTypesValues)) {
//			$bookingTypedefault = $bookingTypeFrpmForm;
//		}
//}
//
//$selPriceTypeObj = null;
//if(isset($completestay->CalculatedPricesDetails)){
//	$calPrices = $completestay->CalculatedPricesDetails;
//}
//
//if(!empty($completestay)){
//	$selPriceType = $completestay->RatePlanId;
//	$selPriceTags = $completestay->Tags;
//}
//
//if(empty($priceTypes)){
//$priceTypes = [];
//}
//
//$priceTypes = array_filter($priceTypes, function($priceType) use ($selPriceTags) {
//	return $priceType->Tags == $selPriceTags;
//});
//
//if (count($priceTypes) > 0){
//	foreach ($priceTypes as $pt) {
//		if ($pt->Type != $selPriceType)  { 
//			continue;
//		}
//		$selPriceTypeObj = $pt;
//		break;
//	}
//}
?>
<!-- Summary dx -->
	<div class="bf-summary">
		<div class="bf-summary-logo"><a href="<?php echo $routeMerchant?>"><img  src="<?php echo $merchantLogo; ?>"></a></div>
<!-- Summary header -->
		<div class="bf-summary-header">
			<div class="bf-summary-title"><i class="fa fa-suitcase"></i>&nbsp;<?php _e('Your holiday', 'bfi') ?></div>
			<div class="bf1-summary-header-name"><a href="<?php echo $routeMerchant?>"><?php echo  $merchant->Name?></a></div>
			<div class="bf-summary-header-rating">
			<?php for($i = 0; $i < $merchant->Rating ; $i++) { ?>
			  <i class="fa fa-star"></i>
			<?php } ?>
			</div>

			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-calendar" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11"><?php _e('Check-in', 'bfi') ?>: <?php echo $OrderDetail->SearchModel->FromDate->format('d/m/Y') ?></div>
			</div>
			<?php  if ($showCheckout) : ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-calendar" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11"><?php _e('Check-out', 'bfi') ?>: <?php echo $OrderDetail->SearchModel->ToDate->format('d/m/Y') ?></div>
			</div>
			<?php  endif ?>

			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-user" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11">	
					<?php echo $nad ?> <?php _e('Adults', 'bfi') ?>
					<?php if ($nse > 0): ?><?php if ($nad > 0): ?>, <?php endif; ?>
						<?php echo $nse ?> <?php _e('Seniores', 'bfi') ?>
					<?php endif; ?>
					<?php if ($nch > 0): ?>
						, <?php echo $nch ?> <?php _e('Children', 'bfi') ?> (<?php echo implode(" ".__('Years', 'bfi') .', ',$nchs) ?> <?php _e('Years', 'bfi') ?> )
					<?php endif; ?>
				</div>
			</div>
		</div>
<!-- Summary header -->
<?php if (count($OrderDetail->Resources)> 0):?>			
<?php 	

	$rdetailgrouped = array();
	foreach ($OrderDetail->Resources as $data) {
		$id = $data->BookingType;
		if (isset($rdetailgrouped[$id])) {
			$rdetailgrouped[$id][] = $data;
		} else {
			$rdetailgrouped[$id] = array($data);
		}
	}

	foreach ($rdetailgrouped as $key=>$rdetailgroup) // foreach $rdetail
	{
?>
<?php 	
	$resCount = 0;
	$totalAmount = 0;
	$totalDiscounted = 0;
	
	foreach ($rdetailgroup as $rdetail) // foreach $rdetail
	{
?>
		<div class="bf-bBookingType bf-bBookingType<?php echo $key ?>" style="display:none;">
			<div class="bf-summary-body" >
				<div class="bf-summary-body-title"><?php _e('Accommodation', 'bfi') ?>:</div>
				<div class="bf-summary-body-resourcename">
					<?php echo $rdetail->Name ?>
							<?php if ($rdetail->TimeSlotId > 0)
							{
								$startHour = new DateTime("2000-01-01 0:0:00.1"); 
								$endHour = new DateTime("2000-01-01 0:0:00.1"); 
								$startHour->add(new DateInterval('PT' . $rdetail->TimeSlotStart . 'M'));
								$endHour->add(new DateInterval('PT' . $rdetail->TimeSlotEnd . 'M'));

							?>
								(<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
							<?php 
							}
							if ($rdetail->RatePlanId > 0 && !($rdetail->AvailabilityType == 2 || $rdetail->AvailabilityType == 3))
							{ ?>
								<br />
							   <?php _e('Meals', 'bfi') ?>: <?php echo BFCHelper::getLanguage($rdetail->RatePlanName, $language) ?>
							<?php 
							}
							if (!empty($rdetail->CheckInTime) && !empty($rdetail->TimeDuration))
							{
								$startHour = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
								$endHour = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
								$endHour->add(new DateInterval('PT' . $rdetail->TimeDuration . 'M'));
							?>
								(<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
							<?php 
							}
							?>
				</div>
				<div class="bf-summary-body-resourceprice">
					<?php 
					if($rdetail->PercentVariation){ ?>
						<div class="specialoffer variationlabel " rel="<?php echo $rdetail->AllVariations ?>" rel1="<?php echo  $rdetail->ResourceId ?>" >
							<span class="variationlabel_percent"><?php echo $rdetail->PercentVariation ?></span>% <?php _e('Offer', 'bfi') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
						</div>
					<?php 
					}
					if($rdetail->TotalDiscounted < $rdetail->TotalAmount){ ?>
						<span class="bf-summary-body-resourceprice-total-strike bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($rdetail->TotalAmount); ?></span>
					<?php 
					}
					if($rdetail->TotalDiscounted > 0){ ?>
						<span class="bf-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($rdetail->TotalDiscounted); ?></span>
					<?php 
					}
					?>
				</div>
				<div class="divoffers" id="divoffers<?php echo  $rdetail->ResourceId ?>_<?php echo $resCount ?>" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
						<span class="sr-only">Loading...</span>
				</div>
				<?php if(!empty($rdetail->ExtraServices)){ ?>
					<hr />
					<div class="bf-summary-body-title"><?php _e('Facility', 'bfi') ?>:</div>
					<?php foreach($rdetail->ExtraServices as $sdetail):?>						
							<div class="bf-summary-body-resourcename">
								<strong><?php echo $sdetail->Name ?> (<?php echo $sdetail->CalculatedQt ?>)</strong>
								<?php 
								if ($sdetail->TimeSlotId > 0)
								{									
									$TimeSlotDate = DateTime::createFromFormat('d/m/Y', $sdetail->TimeSlotDate);
									$startHour = new DateTime("2000-01-01 0:0:00.1"); 
									$endHour = new DateTime("2000-01-01 0:0:00.1"); 
									$startHour->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
									$endHour->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));
								?>
									<?php echo $TimeSlotDate->format('d/m/Y') ?> (<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
								<?php 
								}
								if (!empty($sdetail->CheckInTime) && !empty($sdetail->TimeDuration) && $sdetail->TimeDuration>0)
								{
									$startHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
									$endHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
									$endHour->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
								?>
									<?php echo $startHour->format('d/m/Y') ?> (<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
								<?php 
								}
								?>
							</div>
							<div class="bf-summary-body-resourceprice">
								<?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
									<span class="bf-summary-body-resourceprice-total-strike bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($sdetail->TotalAmount);?></span>
								<?php } ?>
								<span class="bf-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted );?></span>
							<?php 
							$totalAmount += $sdetail->TotalAmount;
							$totalDiscounted += $sdetail->TotalDiscounted;
							?>
							</div>
					<?php endforeach;?>
				<?php } ?>
			</div>
		</div>
		<div class="bf-summary-footer">
			<div class="bf-summary-footer-title"><?php _e('Total price', 'bfi') ?>:</div>
			<?php if($rdetail->TotalAmount > $rdetail->TotalDiscounted) { ?>
				<div class="bfi_merchantdetails-resource-stay-price originalquote">
					<span class="com_bookingforconnector_resourcelist_strikethrough notvars">
						<span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight bfi_<?php echo $currencyclass ?>"> <?php echo  BFCHelper::priceFormat($rdetail->TotalAmount) ?></span>
					</span>
				</div>
			<?php } ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 bf-summary-footer-totalprice bfi_<?php echo $currencyclass ?>">
					<?php echo BFCHelper::priceFormat($rdetail->TotalDiscounted); ?>
				</div>
			</div>
		</div>
<?php 			
		$totalAmount += $rdetail->TotalAmount;
		$totalDiscounted += $rdetail->TotalDiscounted;
		}// end foreach $rdetail
?>
		<div class="bf-summary-footer bf-summary-footer<?php echo $key ?>">
			<div class="bf-summary-footer-title"><?php _e('Total price', 'bfi') ?>:</div>
			<?php if($totalAmount> $totalDiscounted) { ?>
				<div class="bfi_merchantdetails-resource-stay-price originalquote">
					<span class="com_bookingforconnector_resourcelist_strikethrough notvars">
						<span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight bfi_<?php echo $currencyclass ?>"> <?php echo  BFCHelper::priceFormat($totalAmount) ?></span>
					</span>
				</div>
			<?php } ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 bf-summary-footer-totalprice bfi_<?php echo $currencyclass ?>">
					<?php echo BFCHelper::priceFormat($totalDiscounted); ?>
				</div>
			</div>

			<div id="totaldepositrequested<?php echo $key ?>" class="bf-summary-footer-deposit" style="margin-top:5px;display:none;">
				<div class="bf-summary-footer-title"><?php _e('Deposit', 'bfi') ?>:</div>
				<div class="bf-summary-footer-totalprice bfi_<?php echo $currencyclass ?>" id="footer-deposit<?php echo $key ?>"></div>
			</div>
		</div>
<?php 			
		}// end foreach $BookingType
?>
	<?php endif;?>		
</div>

<script type="text/javascript">
<!--
	var offersLoaded = [];

	jQuery(function($)
		{
			// bind resize 
			if (jQuery.prototype.smartresize){
				$(window).smartresize(function(){
					$(".ja-content").removeClass().addClass("ja-content <?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>12");
				});
			}

			jQuery(".variationlabel").on("click", function(){
				var show = function(resourceId, text){
							jQuery("#divoffers"+resourceId).empty();
							//jQuery("#divoffers"+resourceId).removeClass("com_bookingforconnector_loading");
							jQuery("#divoffers"+resourceId).html(text);
				};
				var discountIds = jQuery(this).attr('rel'); 
				var resourceId = jQuery(this).attr('rel1'); 
		//		jQuery("#divoffers"+resourceId).slideToggle( "slow" );
				if(jQuery("#divoffers"+resourceId).is(":visible")){
					jQuery("#divoffers"+resourceId).slideUp("slow");
					jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
				  } else {
					jQuery("#divoffers"+resourceId).slideDown("slow");
					jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
				  }

				//if (jQuery.inArray(discountIds,offersLoaded)===-1)
				if(!offersLoaded.hasOwnProperty(discountIds))
				{
					getDiscountsAjaxInformations(discountIds,resourceId,show);
					//offersLoaded.push(discountIds);
				}else{
					show(resourceId,offersLoaded[discountIds]);
					//jQuery("#divoffers"+resourceId).html(offersLoaded[discountIds]);
				}
			});
		});

	function getDiscountsAjaxInformations(discountIds,obj, fn){
		if (cultureCode.length>1)
		{
			cultureCode = cultureCode.substring(0, 2).toLowerCase();
		}
		if (defaultcultureCode.length>1)
		{
			defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
		}

		var query = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
				$html = '';
				jQuery.each(data || [], function(key, val) {
					var name = val.Name;
					var descr = val.Description;
					name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
					$html += '<p class="title">' + name + '</p>';
					descr = bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(descr) + "</p>").text());
					$html += '<p class="description ">' + descr + '</p>';
				});
				offersLoaded[discountIds] = $html;
				fn(obj,$html);
				//jQuery(obj).html($html);
		},'json');
}

//-->
</script>