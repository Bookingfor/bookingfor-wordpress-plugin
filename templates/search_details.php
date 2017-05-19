<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR==1){
?>
<style type="text/css">
.ui-datepicker-trigger.activeclass:after {
  top: 35px !important;
}
</style>
<?php
}
$cartType = 1; //$merchant->CartType;
$currentCartConfiguration = null;

$ProductAvailabilityType = 1;
$checkInDates = '';


$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;


$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
$url_cart_page = get_permalink( $cartdetails_page->ID );

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
$routeInfoRequest = $routeMerchant . '/contact';

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );

$resourceName = "";

$uri = $url_resource_page;
$currUriresource  = $uri;
$formRoute= "";
$mrcAcceptanceCheckInHours=0;
$mrcAcceptanceCheckInMins=0;
$mrcAcceptanceCheckInSecs=1;
$mrcAcceptanceCheckOutHours=0;
$mrcAcceptanceCheckOutMins=0;
$mrcAcceptanceCheckOutSecs=1;
if(!empty($merchant->AcceptanceCheckIn) && !empty($merchant->AcceptanceCheckOut) && $merchant->AcceptanceCheckIn != "-" && $merchant->AcceptanceCheckOut != "-"){
	$tmpAcceptanceCheckIn=$merchant->AcceptanceCheckIn;
	$tmpAcceptanceCheckOut=$merchant->AcceptanceCheckOut;
	$tmpAcceptanceCheckIns = explode('-', $tmpAcceptanceCheckIn);
	$tmpAcceptanceCheckOuts = explode('-', $tmpAcceptanceCheckOut);
	list($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs) = explode(':',$tmpAcceptanceCheckIns[0].":1");
	list($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs) = explode(':',$tmpAcceptanceCheckOuts[0].":1");
}

$startDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDateByMerchantId($merchant->MerchantId));
$endDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getEndDateByMerchantId($merchant->MerchantId));

if(!empty($resourceId)){
	$resourceName = BFCHelper::getLanguage($resource->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$ProductAvailabilityType = $resource->AvailabilityType;
	$checkInDates = BFCHelper::getCheckInDates($resource->ResourceId,$startDate);
	$currUriresource  = $uri.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);
	$formRoute = $currUriresource .'/?task=getMerchantResources';
}else{
	$formRoute = $routeMerchant .'/?task=getMerchantResources';
}

//$defaultCurrency = BFCHelper::getDefaultCurrency();
//$currencyExchanges = BFCHelper::getCurrencyExchanges();
//
//echo "<pre>defaultCurrency";
//echo $defaultCurrency;
//echo "</pre>";
//
//echo "<pre>currencyExchanges";
//echo print_r($currencyExchanges);
//echo "</pre>";

//echo "<pre>this->defaultcurrency";
//echo bfi_get_currentCurrency();
//echo "</pre>";

//if($usessl){
//	$formRouteBook = str_replace( 'http:', 'https:', $formRouteBook );
//}

//$formOrderRouteBook = $formRouteBook ;

if($usessl){
	$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
}

$formOrderRouteBook = $url_cart_page;


$_SESSION['search.params']['extras'] = '';

$pars = BFCHelper::getSearchParamsSession();
/*--------------------------------------------*/

$eecstays = array();

$CartMultimerchantEnabled = BFCHelper::getCartMultimerchantEnabled(); 

$productCategory = BFCHelper::GetProductCategoryForSearch($language,1,$merchant->MerchantId); 

$refreshState = isset($_SESSION['search.params']['refreshcalc']);

$checkoutspan = '+1 day';
if ($ProductAvailabilityType== 0)
{
	$checkoutspan = '+0 day';
}



$checkin = new DateTime();
$checkout = new DateTime();

$paxes = 2;
$paxages = array();
$currentState ='';

$ratePlanId = '';
$pricetype = '';
$selectablePrices ='';
$packages ='';


if (!empty($pars)){

	$checkin = !empty($pars['checkin']) ? $pars['checkin'] : new DateTime();
	$checkout = !empty($pars['checkout']) ? $pars['checkout'] : new DateTime();

	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	if (empty($pars['checkout'])){
		$checkout->modify($checkoutspan); 
	}

	$currentState = isset($pars['state'])?$pars['state']:'';
	$pricetype = isset($params['pricetype']) ? $params['pricetype'] : BFCHelper::getVar('pricetype','');
	$ratePlanId = isset($params['rateplanId']) ? $params['rateplanId'] : $pricetype;
	$selectablePrices = isset($params['extras']) ? $params['extras'] : '';
	$packages = isset($params['packages']) ? $params['packages'] : '';
}

$variationPlanId = BFCHelper::getVar('variationPlanId','');
$variationPlanMinDuration = 0;
$variationPlanMaxDuration = 0;
$endDate2 = clone $endDate;

if(!empty($variationPlanId)){
	$offer = BFCHelper::getMerchantOfferFromService($variationPlanId, $language);
	if(isset($offer) && $offer->HasValidSearch) {
		$variationPlanMinDuration = $offer->MinDuration;
		$variationPlanMaxDuration = $offer->MaxDuration;
		$checkoutspan = '+'.$offer->MinDuration.' day';
		$dateparsed = BFCHelper::parseJsonDate($offer->FirstAvailableDate, 'Y-m-d');
		$startDate = DateTime::createFromFormat('Y-m-d',$dateparsed);
		$endDate2 = clone $startDate;
		$endDate2->modify('+'.$offer->MaxDuration.' day'); 
		
		if(empty(BFCHelper::getVar('refreshcalc',''))){
			$checkin = DateTime::createFromFormat('Y-m-d',$dateparsed);
			$checkout = clone $checkin;
			$checkout->modify($checkoutspan); 
		}
	}
}

$startDate2 = clone $startDate;
$startDate2->modify($checkoutspan);

if ($checkin < $startDate){
	$checkin = clone $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

$checkout->setTime(0,0,0);
$checkin->setTime(0,0,0);

if(!empty(BFCHelper::getVar('refreshcalc',''))){
	BFCHelper::setSearchParamsSession($pars);
}
if ($checkin == $checkout){
    $checkout->modify($checkoutspan); 
}
if ($checkout < $checkin){
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}



$nad = 0;
$nch = 0;
$nse = 0;
$countPaxes = 0;
$maxchildrenAge = (int)BFCHelper::$defaultAdultsAge-1;

$nchs = array(null,null,null,null,null,null);

if (empty($paxages)){
	$nad = 2;
	$paxages = array(BFCHelper::$defaultAdultsAge, BFCHelper::$defaultAdultsAge);

}else{
	if(is_array($paxages)){
		$countPaxes = array_count_values($paxages);
		$nchs = array_values(array_filter($paxages, function($age) {
			if ($age < (int)BFCHelper::$defaultAdultsAge)
				return true;
			return false;
		}));
	}
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

$duration = 1;

if ($ProductAvailabilityType == 2)
{
	$currAvailCalHour = json_decode($model->GetCheckInDatesPerTimes($resourceId, $checkin, null));
	$AvailabilityTimePeriod = $currAvailCalHour;
	if (count($currAvailCalHour)>0)
	{
		$minuteStart = BFCHelper::ConvertIntTimeToMinutes($currAvailCalHour[0]->TimeMinStart);
		$minuteEnd = BFCHelper::ConvertIntTimeToMinutes($currAvailCalHour[0]->TimeMinEnd);
		$duration   = $minuteEnd - $minuteStart;
		$checkin->modify('+'.$minuteStart.' minutes'); 
	}
	$checkout = clone $checkin;
	$checkout = $checkout->modify($checkoutspan); 
} else if ($ProductAvailabilityType== 3)
{
	$checkout = clone $checkin;
	$checkout = $checkout->modify($checkoutspan); 
} else {
	$duration = $checkin->diff($checkout)->format('%a');
}

//if ($ProductAvailabilityType== 0)
//{
//	$duration +=1; 
//}

$dateStringCheckin =  $checkin->format('d/m/Y');
$dateStringCheckout =  $checkout->format('d/m/Y');


$dateStayCheckin = new DateTime();
$dateStayCheckout = new DateTime();


$totalPerson = $nad + $nch + $nse;

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$allStaysToView = array();


$alternativeDateToSearch = clone $startDate;
if ($checkin > $alternativeDateToSearch)
{
	$alternativeDateToSearch = clone $checkin;
}


$defaultRatePlan = null;

$fromSearch =  BFCHelper::getVar('fromsearch','0');

$allRatePlans = array();
if(!empty($fromSearch)){
	$allRatePlans = BFCHelper::GetRelatedResourceStays($merchant->MerchantId, $resourceId, $resourceId, $checkin,$duration,$paxages, $variationPlanId,$language);
}

if(!empty($resourceId) && is_array($allRatePlans) && count($allRatePlans)>0){
	$defaultRatePlans =  array_values(array_filter($allRatePlans, function($p) use ($resourceId) {return $p->ResourceId == $resourceId ;})); // c#: allRatePlans.Where(p => p.ResourceId == resId);
	usort($defaultRatePlans, "BFCHelper::bfi_sortResourcesRatePlans");
	if(is_array($defaultRatePlans)){
		$defaultRatePlan =  reset($defaultRatePlans);
	}
}

$calPrices = null;

$stayAvailability = 0;

$selPriceType = 0;
$selBookingType=0;

$tmpSearchModel = new stdClass;
$tmpSearchModel->FromDate = $checkin;
$tmpSearchModel->ToDate = $checkout;

foreach($allRatePlans as $p) {
	if (!empty($p->BookingType)) {
		$selBookingType = $p->BookingType;
		break;
	}
}

if(!empty($defaultRatePlan) && !empty($defaultRatePlan->RatePlanId) ){
	$selPriceType = $defaultRatePlan->RatePlanId;
}

$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
if(!empty($resource->ImageUrl)){
	$resourceImageUrl = BFCHelper::getImageUrlResized('resources', $resource->ImageUrl,'small');	
}

$showChildrenagesmsg = isset($_REQUEST['showmsgchildage']) ? $_REQUEST['showmsgchildage'] : 0;

$btnSearchclass=" not-active"; 
if(empty($fromSearch)){
	$btnSearchclass=""; 
}


$listDayTS = array();

?>

<?php 
//echo "<pre>";
//echo print_r($productCategory );
//echo "</pre>";

?>
<div id="calculator" class="ajaxReload">

<script type="text/javascript">
    var daysToEnable = [<?php echo $checkInDates?>];
    var unitId = '<?php echo $resourceId ?>';
    var checkOutDaysToEnable = [];
    var bfi_MaxQtSelectable = <?php echo COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE ?>;
	
</script>
<br />
<!-- form fields -->
<h4 class="titleform"><?php _e('Availability', 'bfi') ?>
<?php if($CartMultimerchantEnabled) { ?>
	<div class="bfi-pull-right"><a href="<?php echo $url_cart_page ?>" class="bookingfor-shopping-cart"><?php _e('Cart', 'bfi') ?></a></div>
	<div class="bfi-hide" id="bfimodalcart">
		<div class="bfi-title"><?php _e('Cart', 'bfi') ?></div>
		<div class="bfi-body"><!-- <?php _e('Add to cart', 'bfi') ?> --></div>
		<div class="bfi-footer">
			<div class="btn btn-secondary" onclick="jQuery('.bookingfor-shopping-cart').webuiPopover('destroy');"><?php _e('Continue shopping', 'bfi') ?></div>
			<a href="<?php echo $url_cart_page ?>" class="btn btn-primary">Checkout</a>
		</div>
	</div><!-- /.modal -->
<?php } ?>
<div class="bfi-clearfix"></div>
</h4>
<form id="calculatorForm" action="<?php echo $formRoute?>" method="POST" class="bfi_resource-calculatorForm bfi_resource-calculatorTable ">
	<div class="bfi-row bfi_resource-calculatorForm-mandatory nopadding">
			<div class="bfi-row nopadding">
				<div class="bfi-col-md-6">
					<div class="bfi-row bfi-flexalignend nopadding">
						<div class="bfi-col-md-5 bfi-col-xs-5" id="calcheckin">      

							<span class="fieldLabel"><?php echo _e('Check-in','bfi') ?>:</span>
							<div class="dateone lastdate dateone_div checking-container">
							<input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" readonly="readonly" />
							</div>
							<?php 
								$checkintext = '"<div class=\'buttoncalendar checkinBooking\'><div class=\'dateone day\'><span>'.$checkin->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.date_i18n('D',$checkin->getTimestamp()).'<br />'.date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y").'  </p></div></div>"';
							?>					
						</div>
						<div class="bfi-col-md-5 bfi-col-xs-5 <?php echo ($ProductAvailabilityType == 3 || $ProductAvailabilityType == 2)? "bfi-hide " : " "  ?>" id="calcheckout">
							<span class="fieldLabel"><?php echo _e('Check-out ','bfi') ?>:</span>
							<div class="lastdate dateone lastdatecheckout dateone_div checking-container ">
							<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" readonly="readonly"/>
							</div>
							<?php 
								$checkouttext = '"<div class=\'buttoncalendar checkoutBooking\'><div class=\'dateone day\'><span>'.$checkout->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.date_i18n('D',$checkout->getTimestamp()).'<br />'.date_i18n('M',$checkout->getTimestamp()).' '.$checkout->format("Y").'  </p></div></div>"';
							?>
						</div>
						<div class="bfi-col-md-2 bfi-col-xs-2 <?php echo ($ProductAvailabilityType == 3 || $ProductAvailabilityType == 2)? "bfi-hide " : " "  ?>">
							<div class="calendarnight" id="durationdays"><?php echo $duration ?></div><div class="calendarnightlabel"><?php echo $ProductAvailabilityType == 1 ? __('Nights' , 'bfi' ) : __('Days' , 'bfi' )  ?></div>
						</div>
					</div>
				</div>
				<div class="bfi-col-md-6">
					<div class="bfi_search-resources bfi-row nopadding">
						<div class="bfi-col-md-3 bfi-col-xs-4 bfi_resource-calculatorForm-adult">
							<label><?php echo _e('Adults ','bfi') ?>:</label><br />
							<select id="adultscalculator" name="adults" onchange="quoteCalculatorChanged();" class="inputmini">
								<?php
								foreach (range(1, 10) as $number) {
									?> <option value="<?php echo $number ?>" <?php selected( $nad, $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
						</div>
						<div class="bfi-col-md-3 bfi-col-xs-4 bfi_resource-calculatorForm-senior" >
							<label><?php echo _e('Seniors ','bfi') ?>:</label><br />
							<select id="seniorescalculator" name="seniors" onchange="quoteCalculatorChanged();" class="inputmini">
								<?php
								foreach (range(0, 10) as $number) {
									?> <option value="<?php echo $number ?>" <?php selected( $nse, $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
						</div>
						<div class="bfi-col-md-3 bfi-col-xs-4 bfi_resource-calculatorForm-children">
							<label><?php echo  _e('Children','bfi') ?>:</label><br />
							<select id="childrencalculator" name="children" onchange="quoteCalculatorChanged();" class="inputmini">
								<?php
								foreach (range(0, 4) as $number) {
									?> <option value="<?php echo $number ?>" <?php selected( $nch, $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
						</div>
						<div class="bfi-col-md-3 bfi-col-xs-4 ">
							<a href="javascript:calculateQuote()"id="calculateButton" class="calculateButton3 <?php echo $btnSearchclass ?>" ><?php echo _e('Search','bfi') ?> </a>
						</div>
					</div>
				</div>
			</div>
			<div class="<?php //echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> bfi_resource-calculatorForm-childrenages" style="display:none;">
				<span class="fieldLabel" style="display:inline"><?php  echo _e('Ages of children','bfi')  ?>:</span>
				<span class="fieldLabel" style="display:inline" id="bfi_lblchildrenagesatcalculator"><?php echo _e('on', 'bfi') . " " .$checkout->format("d"). " " . date_i18n('M',$checkout->getTimestamp()) . " " . $checkout->format("Y") ?></span><br />
				<select id="childages1" name="childages1" onchange="quoteCalculatorChanged();" class="inputmini" style="display: none;">
					<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
					<?php
					foreach (range(0, $maxchildrenAge) as $number) {
						?> <option value="<?php echo $number ?>" <?php echo ($nchs[0] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
					}
					?>
				</select>
				<select id="childages2" name="childages2" onchange="quoteCalculatorChanged();" class="inputmini" style="display: none;">
					<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
					<?php
					foreach (range(0, $maxchildrenAge) as $number) {
						?> <option value="<?php echo $number ?>" <?php echo ($nchs[1] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
					}
					?>
				</select>
				<select id="childages3" name="childages3" onchange="quoteCalculatorChanged();" class="inputmini" style="display: none;">
					<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
					<?php
					foreach (range(0, $maxchildrenAge) as $number) {
						?> <option value="<?php echo $number ?>" <?php echo ($nchs[2] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
					}
					?>
				</select>
				<select id="childages4" name="childages4" onchange="quoteCalculatorChanged();" class="inputmini" style="display: none;">
					<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
					<?php
					foreach (range(0, $maxchildrenAge) as $number) {
						?> <option value="<?php echo $number ?>" <?php echo ($nchs[3] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
					}
					?>
				</select>
				<select id="childages5" name="childages5" onchange="quoteCalculatorChanged();" class="inputmini" style="display: none;">
					<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
					<?php
					foreach (range(0, $maxchildrenAge) as $number) {
						?> <option value="<?php echo $number ?>" <?php echo ($nchs[4] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
					}
					?>
				</select>
			</div> 
			<span class="bfi-childmessage" id="bfi_lblchildrenagescalculator">&nbsp;</span>
	</div>	<!-- END bfi_resource-calculatorForm-mandatory -->

	<div class="clear"></div>
	<input name="calculate" type="hidden" value="true" />
	<input name="resourceId" type="hidden" value="<?php echo $resourceId?>" />
	<input name="pricetype" type="hidden" value="<?php echo $selPriceType ?>" />
	<input name="bookingType" type="hidden" value="<?php echo $selBookingType ?>" />
	<input name="variationPlanId" type="hidden" value="<?php echo $variationPlanId ?>" />
	<input name="state" type="hidden" value="<?php echo $currentState ?>" />
	<input name="extras[]" type="hidden" value="<?php echo $selectablePrices ?>" />
	<input name="refreshcalc" type="hidden" value="1" />
	<input name="fromsearch" type="hidden" value="1" />
	<input name="availabilitytype" id="productAvailabilityType" type="hidden" value="<?php echo $ProductAvailabilityType?>" />
	<input type="hidden" name="format" value="raw" />
	<input type="hidden" value="0" name="showmsgchildage" id="showmsgchildagecalculator"/>
	<div class="bfi-hide" id="bfi_childrenagesmsgcalculator">
		<div style="line-height:0; height:0;"></div>
		<div class="bfi-pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('#bfi_lblchildrenagescalculator').webuiPopover('destroy');"></i></div>
		<?php echo sprintf(__('We preset your children\'s ages to %s years old - but if you enter their actual ages, you might be able to find a better price.', 'bfi'),COM_BOOKINGFORCONNECTOR_CHILDRENSAGE) ?>
	</div>
</form>	
<!-- form fields end-->

<!-- RESULT -->	
<?php 
$showResult= " bfi-hide";
if(!empty($fromSearch)){
	$showResult= "";
} 
	
$resCount = 0;
$totalResCount = count($allRatePlans);

$loadScriptTimeSlot = false;
$loadScriptTimePeriod = false;

$allResourceId = array();
$allServiceIds = array();

if(is_array($allRatePlans) && count($allRatePlans)>0){
	$allResourceId = array_unique(array_map(function ($i) { return $i->ResourceId; }, $allRatePlans));
}

if(!empty($allResourceId)){
	$keyfirst = array_search($resourceId, $allResourceId);
	$tempfirst = array($keyfirst => $allResourceId[$keyfirst]);
	unset($allResourceId[$keyfirst]);
	$allResourceId = $tempfirst + $allResourceId;
}
?>

<div class="bfi-clearfix"></div>



<div class="bfi-hide">
		<div class="bfi-timeperiod-change" id="bfimodaltimeperiod">
				<div class="bfi-simplerow">
					<div class="bfi-buttoncalendar check-availibility-date bfi-text-center">
						<input id="bfimodaltimeperiodcheckin" type="text" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimePeriod" hidden = "hidden"   readonly="readonly" />
					</div>
				</div>	
				<div class="bfi-simplerow">
					<select class="bfi_input_select selectpickerTimePeriodStart" id="selectpickerTimePeriodStart" data-rateplanid="0"></select>
				</div>	
				<div class="bfi-simplerow">
					<select class="bfi_input_select selectpickerTimePeriodEnd" id="selectpickerTimePeriodEnd" data-rateplanid="0"></select>
				</div>	
				<div class="bfi-simplerow bfi-text-center">
					<a id="bfi-timeperiod-select" class="bfi-timeperiod-select bfi-text-center" onclick="bfi_selecttimeperiod(this)" data-rateplanid="0" data-resid="0"><?php _e('Select', 'bfi') ?></a>
				</div>	
		</div>
</div><!-- /.modal -->

<div class="bfi-hide">
		<div class="bfi-timeslot-change" id="bfimodaltimeslot">
				<div class="bfi-simplerow">
					<div class="bfi-buttoncalendar check-availibility-date bfi-text-center">
						<input id="bfimodaltimeslotcheckin" type="text" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimeSlot" hidden = "hidden"   readonly="readonly" />
					</div>
				</div>	
				<div class="bfi-simplerow ">
					<select class="bfi_input_select selectpickerTimeSlotRange" id="selectpickerTimeSlotRange" data-rateplanid="0"></select>
				</div>	
				<div class="bfi-simplerow bfi-text-center">
					<a id="bfi-timeslot-select" class="bfi-timeslot-select bfi-text-center" onclick="bfi_selecttimeslot(this)" data-rateplanid="0" data-resid="0"><?php _e('Select', 'bfi') ?></a>
				</div>	
		</div>
</div><!-- /.modal -->



<div class="bfi-result-list <?php echo $showResult ?> bfi-table-responsive">
		<table class="bfi-table bfi-table-bordered bfi-table-resources" style="margin-top: 20px;display: <?php echo $currentState=='optionalPackages' ? 'none' : 'block'; ?>;">
			<thead>
				<tr>
					<th><?php _e('Information', 'bfi') ?></th>
					<th><div><?php _e('Min', 'bfi') ?><br /><?php _e('Max', 'bfi') ?></div></th>
					<th ><div><?php _e('Price', 'bfi') ?></div></th>
					<th><div><?php _e('Options', 'bfi') ?></div></th>
					<th><div><?php _e('Qt.', 'bfi') ?></div></th>
					<th><div><?php _e('Confirm your reservation', 'bfi') ?></div></th>
				</tr>
			</thead>

			
			<tbody>
			<tr>
				<td colspan="5" style="padding:0;border:none;"></td>
				<td rowspan="400">
					<?php if ($cartType == 1 && $currentCartConfiguration != null) //////// && ($currentCartConfiguration as List<CartOrder>).Any(t => t.Resources.Any(r => r.MerchantId != Model.MerchantId)))
					{ ?>
						<?php _e("You can't acquire resources from another merchant.", 'bfi') ?>
					
					<?php } else{ ?>
							<div class="bfi-book-now">
								<div class="bfi-resource-total"><span></span> <?php _e('selected items', 'bfi') ?></div>
								<div class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?>" style="display:none;"></div>
								<div class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>" ></div>
								<div id="btnBookNow" class="bfi-item-secondary-more" data-formroute="<?php// echo $formRouteBook ?>" onclick="ChangeVariation(this);">
									<?php _e('Book Now', 'bfi') ?>
								</div>
								<div class="bfi-request-now" onclick="ChangeVariation(this);">
									<?php _e('Request Now', 'bfi') ?>
								</div>

							</div>
					<?php } ?>
				</td>
			</tr>

			<?php  if(!empty($resourceId) && !in_array($resourceId,$allResourceId)) {
				$currUriresource = $uri.$resourceId. '-' . BFCHelper::getSlug($resource->Name) . "?fromsearch=1";
			?>
			<tr>
				<td>
					<a class="bfi-resname" onclick="bfiGoToTop()" href="<?php echo $currUriresource ?>"><?php echo $resource->Name; ?></a>
<?php 
					$listServices = array();
					if(!empty($resource->ResServiceIdList)){
						$listServices = explode(",", $resource->ResServiceIdList);
						$allServiceIds = array_merge($allServiceIds, $listServices);
						?>
						<div class="bfisimpleservices" rel="<?php echo $res->ResServiceIdList ?>"></div>
						
						<?php
					}
					if(!empty($resource->TagsIdList)){
						?>
						<div class="bfiresourcegroups" rel="<?php echo $resource->TagsIdList?>"></div>
						<?php
					}					

$currVat = isset($resource->VATValue)?$resource->VATValue:"";					
$currTouristTaxValue = isset($resource->TouristTaxValue)?$resource->TouristTaxValue:0;				
?>
<?php if(!empty($currVat)) { ?>
	<div class="bfi-incuded"><strong><?php _e('Included', 'bfi') ?></strong> : <?php echo $currVat?> <?php _e('VAT', 'bfi') ?> </div>
<?php } ?>
<?php if(!empty($currTouristTaxValue)) { ?>
	<div class="bfi-notincuded"><strong><?php _e('Not included', 'bfi') ?></strong> : <span class="bfi_<?php echo $currencyclass ?>" ><?php echo BFCHelper::priceFormat($currTouristTaxValue) ?></span> <?php _e('City tax per person per night.', 'bfi') ?> </div>
<?php } ?>

				</td>
				<td>
				<?php if ($resource->MaxCapacityPaxes>0):?>
					<div class="bfi-icon-paxes">
						<i class="fa fa-user"></i> 
						<?php if ($resource->MaxCapacityPaxes==2){?>
						<i class="fa fa-user"></i> 
						<?php }?>
						<?php if ($resource->MaxCapacityPaxes>2){?>
							<?php echo ($resource->MinCapacityPaxes != $resource->MaxCapacityPaxes)? $resource->MinCapacityPaxes . "-" : "" ?><?php echo  $resource->MaxCapacityPaxes?>
						<?php }?>
					</div>
					<?php endif; ?>
				</td>
				<td colspan="3" style="vertical-align:middle;text-align:center;">
					<div class="errorbooking" id="errorbooking">
						<strong><?php _e('No results available for the submitted data', 'bfi') ?></strong>
						<!-- No disponibile -->
						<?php if($resource->MaxCapacityPaxes > 0 && ( $totalPerson > $resource->MaxCapacityPaxes || $totalPerson < $resource->MinCapacityPaxes )) :?><!-- Errore persone-->
							<br /><?php echo sprintf(__('Persons min:%1$d max:%2$d', 'bfi'), $resource->MinCapacityPaxes, $resource->MaxCapacityPaxes) ?>
						<?php endif;?>
					</div>
				</td>
			</tr>
			<?php if ($totalResCount > 0 ): ?>
				<tr><td colspan="5" class="bfi-nopad"><div class="bfi-otherresults"><?php echo sprintf(__('Other %1$d choise', 'bfi'), $totalResCount) ?></div> <?php _e('Find other great offers!', 'bfi') ?></td></tr>
			<?php endif; ?>

		<?php } ?>
<?php

$allSelectablePrices = array();
$allTimeSlotResourceId = array();
$allTimePeriodResourceId = array();
$reskey = -1;

foreach($allResourceId as $resId) {

	$reskey += 1;

	$resRateplans =  array_filter($allRatePlans, function($p) use ($resId) {return $p->ResourceId == $resId ;}); // c#: allRatePlans.Where(p => p.ResourceId == resId);

	usort($resRateplans, "BFCHelper::bfi_sortResourcesRatePlans");
	$res = array_values($resRateplans)[0];

	$IsBookable = 0;
	
	$isResourceBlock = $res->ResourceId == $resourceId;

	$IsBookable = $res->IsBookable;
	$showQuote = false;
			
	if (($res->Price > 0 && $res->Availability > 0) && ($res->MaxPaxes == 0 || ($totalPerson <= $res->MaxPaxes && $totalPerson >= $res->MinPaxes)) &&
		(($res->AvailabilityType == 3 || $res->AvailabilityType == 2) && BFCHelper::parseJsonDate($res->RatePlan->CheckIn) == $dateStringCheckin)
		|| 
		(($res->AvailabilityType == 0 || $res->AvailabilityType == 1) && BFCHelper::parseJsonDate($res->RatePlan->CheckIn) == $dateStringCheckin && BFCHelper::parseJsonDate($res->RatePlan->CheckOut) == $dateStringCheckout))		{
			$showQuote = true;
		}
	
//	$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
//	if(!empty($res->ImageUrl)){
//		$resourceImageUrl = BFCHelper::getImageUrlResized('resources', $res->ImageUrl,'small');	
//	}
	$currUriresource = $uri.$res->ResourceId . '-' . BFCHelper::getSlug($res->ResName) . "?fromsearch=1";

	$formRouteSingle = $currUriresource;

//	$priceTypes = array();
//	foreach($resRateplans as $p) {
////		if ($p->ResourceId == $resId) {
////			$res = $p;
//
//			$type = new stdClass;
//			$type->Type = $p->RateplanId;
//			$type->IsBase = $p->IsBase;
//			$type->Name = $p->RatePlan->Name;
//			$type->refid = $p->RatePlan->RefId;
//			$type->SortOrder = $p->RatePlan->SortOrder;
//			$priceTypes[] = $type;
////			break;
////		}
//	}
//
////	usort($priceTypes, "BFCHelper::bfi_sortRatePlans");
//
//
//	$classselect = (count($priceTypes) >1 && $res->AvailabilityType != 3)? '': 'bfi-hide';	

	$eecstay = new stdClass;
	$eecstay->id = "" . $res->ResourceId . " - Resource";
	$eecstay->name = "" . $res->ResName;
	$eecstay->category = $merchant->MainCategoryName;
	$eecstay->brand = $merchant->Name;
//	$eecstay->variant = $showQuote ? strtoupper($selRateName) : "NS";
	$eecstay->position = $reskey;
	if($isResourceBlock) {
		$eecmainstay = $eecstay;
	} else {
		$eecstays[] = $eecstay;
	}

		
//	$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->Name));
//	if($usessl){
//		$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->Name),true,1);
//	}
	
	$btnText = __('Request info', 'bfi');
	$btnClass = "";
	if ($IsBookable){
		$btnText = __('Book Now', 'bfi');
		$btnClass = "bfi-btn-bookable";
	}
	$formRouteBook = "";
	$nRowSpan = 1+count($resRateplans);
?>
			<tr >
				<td rowspan="<?php echo $nRowSpan ?>">
					<a  class="bfi-resname" href="<?php echo $formRouteSingle ?>" <?php echo ($resId == $resourceId)? 'onclick="bfiGoToTop()"' :  'target="_blank"' ; ?> ><?php echo $res->ResName; ?></a>
					<br />
								<?php
/*-----------scelta date e ore--------------------*/	
									if ($res->AvailabilityType == 2)
									{
										
										$currCheckIn = BFCHelper::parseJsonDateTime($res->RatePlan->CheckIn,'d/m/Y - H:i');
										$currCheckOut =BFCHelper::parseJsonDateTime($res->RatePlan->CheckOut,'d/m/Y - H:i');
										$currDiff = $currCheckOut->diff($currCheckIn);

										$loadScriptTimePeriod = true;

										$timeDuration = $currDiff->i + ($currDiff->h*60);

										array_push($allTimePeriodResourceId, $res->ResourceId);
									?>
										<div class="bfi-timeperiod bfi-cursor" id="bfi-timeperiod-<?php echo $res->ResourceId ?>" 
											data-resid="<?php echo $res->ResourceId ?>" 
											data-checkin="<?php echo $currCheckIn->format('Ymd') ?>" 
											data-checkintime="<?php echo $currCheckIn->format('YmdHis') ?>"
											data-timeminstart="<?php echo $currCheckIn->format('His') ?>"
											data-timeminend="<?php echo $currCheckOut->format('His') ?>"
											data-duration="<?php echo $timeDuration ?>"
										>
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
									if ($res->AvailabilityType == 3)
									{
										$loadScriptTimeSlot = true;
										array_push($allTimeSlotResourceId, $res->ResourceId);
										$currDatesTimeSlot =  json_decode(BFCHelper::GetCheckInDatesTimeSlot($resId,$alternativeDateToSearch));

										$listDayTS[$resId] = $currDatesTimeSlot;

										$currCheckIn = DateTime::createFromFormat('Ymd', $currDatesTimeSlot[0]->StartDate);
										$currCheckOut = clone $currCheckIn;
										$currCheckIn->setTime(0,0,0);
										$currCheckOut->setTime(0,0,0);
										$currCheckIn->add(new DateInterval('PT' . $currDatesTimeSlot[0]->TimeSlotStart . 'M'));
										$currCheckOut->add(new DateInterval('PT' . $currDatesTimeSlot[0]->TimeSlotEnd . 'M'));

										$currDiff = $currCheckOut->diff($currCheckIn);

										// overrides Availability by CheckInDatesTimeSlot
										$res->Availability = $currDatesTimeSlot[0]->Availability ;

									?>
										<div class="bfi-timeslot bfi-cursor" id="bfi-timeslot-<?php echo $res->ResourceId ?>" data-resid="<?php echo $res->ResourceId ?>" data-checkin="<?php echo $currCheckIn->format('Ymd') ?>" data-checkin-ext="<?php echo $currCheckIn->format('d/m/Y') ?>"
										data-timeslotid="<?php echo $currDatesTimeSlot[0]->ProductId ?>" data-timeslotstart="<?php echo $currDatesTimeSlot[0]->TimeSlotStart ?>" data-timeslotend="<?php echo $currDatesTimeSlot[0]->TimeSlotEnd ?>"
										>
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
					$listServices = array();
					if(!empty($res->ResServiceIdList)){
						$listServices = explode(",", $res->ResServiceIdList);
						$allServiceIds = array_merge($allServiceIds, $listServices);
						?>
						<div class="bfisimpleservices" rel="<?php echo $res->ResServiceIdList ?>"></div>
						<?php
					}
					if(!empty($res->TagsIdList)){
						?>
						<div class="bfiresourcegroups" rel="<?php echo $res->TagsIdList?>"></div>
						<?php
					}					

$currVat = $res->VATValue;				
$currTouristTaxValue = isset($res->TouristTaxValue)?$res->TouristTaxValue:0;				
?>
<br />
<?php if(!empty($currVat)) { ?>
	<div class="bfi-incuded"><strong><?php _e('Included', 'bfi') ?></strong> : <?php echo $currVat?> <?php _e('VAT', 'bfi') ?> </div>
<?php } ?>
<?php if(!empty($currTouristTaxValue)) { ?>
	<div class="bfi-notincuded"><strong><?php _e('Not included', 'bfi') ?></strong> : <span class="bfi_<?php echo $currencyclass ?>" ><?php echo BFCHelper::priceFormat($currTouristTaxValue) ?></span> <?php _e('City tax per person per night.', 'bfi') ?> </div>
<?php } ?>



				</td>
				<td colspan="4" style="padding:0;border:none;"></td>
			</tr>

<?php



	foreach($resRateplans as $rpKey => $currRateplan) {
		if(count(json_decode($currRateplan->RatePlan->CalculablePricesString))>0){
			$formRouteBook = "showSelectablePrices"; 
		}
		$availability = array();
		$startAvailability = 0;
		$selectedtAvailability = 0;
		if ($cartType == 0)
		{
			$startAvailability = 1;
		}
		for ($i = $startAvailability; $i <= min($res->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE); $i++)
		{
			array_push($availability, $i);
		}
		if ($cartType == 0 && count($availability)>0)
		{
			$selectedtAvailability = $availability[0];
		}else{
			if(empty($pricetype)){
				if ($res->ResourceId == $resourceId && count($availability) > 1 && $reskey==0 && $rpKey==0)
					{ 
						$selectedtAvailability = $availability[1];
					}
			}else{
				if ($res->ResourceId == $resourceId && count($availability) > 1 && $pricetype==$currRateplan->RatePlan->RatePlanId )
					{ 
						$selectedtAvailability = $availability[1];
					}
			}
		} 
		$IsBookable = $currRateplan->IsBookable;

		$SimpleDiscountIds = "";

		if(!empty($currRateplan->RatePlan->AllVariationsString)){
			$allVar = json_decode($currRateplan->RatePlan->AllVariationsString);
			$SimpleDiscountIds = implode(',',array_unique(array_map(function ($i) { return $i->VariationPlanId; }, $allVar)));
		}
		$currCheckIn = BFCHelper::parseJsonDateTime($currRateplan->RatePlan->CheckIn,'d/m/Y\TH:i:s');
		$currCheckOut =BFCHelper::parseJsonDateTime($currRateplan->RatePlan->CheckOut,'d/m/Y\TH:i:s');

if($currRateplan->AvailabilityType==0 || $currRateplan->AvailabilityType==1){
	$currCheckIn = BFCHelper::parseJsonDateTime($currRateplan->RatePlan->CheckIn,'d/m/Y\TH:i:s');
	$currCheckOut =BFCHelper::parseJsonDateTime($currRateplan->RatePlan->CheckOut,'d/m/Y\TH:i:s');
	$currCheckIn->setTime($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs);
	$currCheckOut->setTime($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs);

}

?>
			<tr id="data-id-<?php echo $currRateplan->ResourceId ?>-<?php echo $currRateplan->RatePlan->RatePlanId ?>" class="<?php echo $IsBookable?"bfi-bookable":"bfi-canberequested"; ?>">
				<td><!-- Min/Max -->
				<?php if ($currRateplan->MaxPaxes>0):?>
					<div class="bfi-icon-paxes">
						<i class="fa fa-user"></i> 
						<?php if ($currRateplan->MaxPaxes==2 && $currRateplan->MinPaxes==2){?>
						<i class="fa fa-user"></i> 
						<?php }?>
						<?php if ($currRateplan->MaxPaxes>2){?>
							<?php echo ($currRateplan->MinPaxes != $currRateplan->MaxPaxes)? $currRateplan->MinPaxes . "-" : "" ?><?php echo  $currRateplan->MaxPaxes ?>
						<?php }?>
					</div>
				<?php endif; ?>
				</td>

				<td style="text-align:center;"><!-- price -->
					<?php if( $currRateplan->Price> 0) :?><!-- disponibile -->
					 <div align="center">
						<div class="bfi-percent-discount" style="<?php echo ($currRateplan->PercentVariation < 0 ? " display:block" : "display:none"); ?>" rel="<?php echo $SimpleDiscountIds ?>" rel1="<?php echo $currRateplan->ResourceId ?>">
							<span class="bfi-percent"><?php echo $currRateplan->PercentVariation ?></span>% <i class="fa fa-question-circle bfi-cursor-helper" aria-hidden="true"></i>
						</div>
					</div>
					<div data-value="<?php echo $currRateplan->TotalPrice ?>" class="bfi-discounted-price bfi_<?php echo $currencyclass ?>" style="display:<?php echo ($currRateplan->Price < $currRateplan->TotalPrice)?"":"none"; ?>;"><?php echo BFCHelper::priceFormat($currRateplan->TotalPrice) ?></div>
					<div data-value="<?php echo $currRateplan->Price ?>" class="bfi-price  <?php echo ($currRateplan->Price < $currRateplan->TotalPrice ? "bfi-red" : "") ?> bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($currRateplan->Price) ?></div>
					
					<?php else:?>
						<strong><?php _e('No results available for the submitted data', 'bfi') ?></strong>
					<?php endif;?>
				</td>
				<td><!-- options -->
					<div style="position:relative;">
					<?php 
$policy = $currRateplan->RatePlan->Policy;
$policyId= 0;
$policyHelp = "";
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
	}
	if($policy->CanBeCanceled){
		$currTimeBefore = "";
		$currDateBefore = "";
		if(!empty( $policy->CanBeCanceledCurrentTime )){
				if(!empty( $policy->CancellationTime )){
					$currDatePolicyparsed = BFCHelper::parseJsonDate($res->RatePlan->CheckIn, 'Y-m-d');
					$currDatePolicy = DateTime::createFromFormat('Y-m-d',$currDatePolicyparsed);
					switch (true) {
						case strstr($policy->CancellationTime ,'d'):
							$currTimeBefore = sprintf(__(' %d day/s' ,'bfi'),rtrim($policy->CancellationTime,"d"));	
							$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"d") .' days'); 
							break;
						case strstr($policy->CancellationTime ,'h'):
							$currTimeBefore = sprintf(__(' %d hour/s' ,'bfi'),rtrim($policy->CancellationTime,"h"));
							$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"h") .' hours'); 
							break;
						case strstr($policy->CancellationTime ,'w'):
							$currTimeBefore = sprintf(__(' %d week/s' ,'bfi'),rtrim($policy->CancellationTime,"w"));
							$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"w") .' weeks'); 
							break;
						case strstr($policy->CancellationTime ,'m'):
							$currTimeBefore = sprintf(__(' %d month/s' ,'bfi'),rtrim($policy->CancellationTime,"m"));
							$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"m") .' months'); 
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
$currMerchantBookingTypes = array();
$prepayment = "";
$prepaymentHelp = "";

if(!empty( $currRateplan->RatePlan->MerchantBookingTypesString )){
	$currMerchantBookingTypes = json_decode($currRateplan->RatePlan->MerchantBookingTypesString);
	$currBookingTypeId = $currRateplan->RatePlan->MerchantBookingTypeId;
	$currMerchantBookingType = array_filter($currMerchantBookingTypes, function($bt) use($currBookingTypeId) {return $bt->BookingTypeId == $currBookingTypeId;});
	$currMerchantBookingType = array_values($currMerchantBookingType);
	if(count($currMerchantBookingType)>0){
		if($currMerchantBookingType[0]->PayOnArrival){
			$prepayment = __("Pay at the property – NO PREPAYMENT NEEDED", 'bfi');
			$prepaymentHelp = __("No prepayment is needed.", 'bfi');
		}
		if($currMerchantBookingType[0]->AcquireCreditCardData){
			$prepayment = "";
			if($currMerchantBookingType[0]->DepositRelativeValue=="100%"){
				$prepaymentHelp = __('You will be charged a prepayment of the total price at any time.', 'bfi');
			}else if(strpos($currMerchantBookingType[0]->DepositRelativeValue, '%') !== false  ) {
				$prepaymentHelp = sprintf(__('You will be charged a prepayment of %1$s of the total price at any time.', 'bfi'),$currMerchantBookingType[0]->DepositRelativeValue);
			}else{
				$prepaymentHelp = sprintf(__('You will be charged a prepayment of %1$s at any time.', 'bfi'),$currMerchantBookingType[0]->DepositRelativeValue);
			}
		}
	}
}




$allMeals = array();
$cssclassMeals = "bfi-meals-base";
$mealsHelp = "";
if($currRateplan->RatePlan->IncludedMeals >-1){
	$mealsHelp = __("There is no meal option with this room.", 'bfi');
	if ($currRateplan->RatePlan->IncludedMeals & bfi_Meal::Breakfast){
		$allMeals[]= __("Breakfast", 'bfi');
	}
	if ($currRateplan->RatePlan->IncludedMeals & bfi_Meal::Lunch){
		$allMeals[]= __("Lunch", 'bfi');
	}
	if ($currRateplan->RatePlan->IncludedMeals & bfi_Meal::Dinner){
		$allMeals[]= __("Dinner", 'bfi');
	}
	if ($currRateplan->RatePlan->IncludedMeals & bfi_Meal::AllInclusive){
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
				</td>
				<td>
					<select class="ddlrooms ddlrooms-<?php echo $currRateplan->ResourceId ?> ddlrooms-indipendent inputmini" 
					id="ddlrooms-<?php echo $currRateplan->ResourceId ?>-<?php echo $currRateplan->RatePlan->RatePlanId ?>" 
					onclick="bookingfor.checkMaxSelect(this);" 
					onchange="bookingfor.checkBookable(this);UpdateQuote();" 
					data-resid="<?php echo $currRateplan->ResourceId ?>" 
					data-ratePlanId="<?php echo $currRateplan->RatePlan->RatePlanId ?>"
					data-policyId="<?php echo $policyId ?>"
					data-price="<?php echo BFCHelper::priceFormat($currRateplan->Price,2,".","") ?>" 
					data-totalprice="<?php echo BFCHelper::priceFormat($currRateplan->TotalPrice,2,".","") ?>" 
					data-baseprice="<?php echo $currRateplan->Price ?>" 
					data-basetotalprice="<?php echo $currRateplan->TotalPrice ?>"
					data-allvariations="<?php echo $currRateplan->RatePlan->AllVariationsString ?>"
					data-percentvariation="<?php echo $currRateplan->RatePlan->PercentVariation ?>"
					data-availability="<?php echo $currRateplan->Availability ?>" 
					data-availabilitytype="<?php echo $currRateplan->AvailabilityType ?>"
					data-isbookable="<?php echo $IsBookable?"1":"0"; ?>" 
					data-checkin="<?php echo $currCheckIn->format('d/m/Y') ?>" 
					data-checkout="<?php echo $currCheckOut->format('d/m/Y') ?>"
					data-checkin-ext="<?php echo $currCheckIn->format('d/m/Y\TH:i:s') ?>" 
					data-checkout-ext="<?php echo $currCheckOut->format('d/m/Y\TH:i:s') ?>"
					data-includedmeals="<?php echo $currRateplan->RatePlan->IncludedMeals ?>" 
					data-touristtaxvalue="<?php echo $currRateplan->TouristTaxValue ?>" 
					data-vatvalue="<?php echo $currRateplan->VATValue ?>" 
					data-minpaxes="<?php echo $currRateplan->MinPaxes ?>" 
					data-maxpaxes="<?php echo $currRateplan->MaxPaxes ?>" 
					>
					<?php 
						foreach ($availability as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($selectedtAvailability== $number)?"selected":""; //selected( $selectedtAvailability, $number ); ?>><?php echo $number ?></option><?php
						}
					?>
					</select>
				</td>
			</tr>

<?php 
}
?>
		<?php
		if (count($allResourceId) > 1 && (($resId == $resourceId && $resCount > 1) || ($resId == $resourceId && $resCount == 0))): ?>
				<tr><td colspan="5" class="bfi-nopad"><div class="bfi-otherresults"><?php echo sprintf(__('Other %1$d choise', 'bfi'), (count($allResourceId)-1)) ?></div> <?php _e('Find other great offers!', 'bfi') ?></td></tr>
		<?php endif; ?>
	<?php 
		$resCount++;
 } 
 ?>
			</tbody>
		</table>
		<!-- end bfi-table-resources -->

<!-- Service -->
<script>
    var servicesAvailability=[];
</script>



<?php if(count($allResourceId)>0){ ?>
    <div class="div-selectableprice bfi-table-responsive" style="display:none;">

	<br /><?php  include(BFI()->plugin_path().'/templates/menu_small_booking.php');  ?>
<!-- 		<h4 class="titleform"><?php _e('Facility', 'bfi') ?></h4> -->
<?php 
	$countPrices = 0;
	foreach($allResourceId as $currResourceId) {
		$resRateplans =  array_filter($allRatePlans, function($p) use ($currResourceId) {return $p->ResourceId == $currResourceId ;}); // c#: allRatePlans.Where(p => p.ResourceId == resId);
		
		usort($resRateplans, "BFCHelper::bfi_sortResourcesRatePlans");
		$res = array_values($resRateplans)[0];

	foreach($resRateplans as $currRateplan) {
		$selectablePrices = json_decode($currRateplan->RatePlan->CalculablePricesString);
		if (count($selectablePrices) == 0)
		{
			continue; //don't display table skip to next
		}
	
		$SimpleDiscountIds = "";

		if(!empty($currRateplan->RatePlan->AllVariationsString)){
			$allVar = json_decode($currRateplan->RatePlan->AllVariationsString);
			$SimpleDiscountIds = implode(',',array_unique(array_map(function ($i) { return $i->VariationPlanId; }, $allVar)));
		}

?>
		<div id="services-room-1-<?php echo $currRateplan->ResourceId ?>-<?php echo $currRateplan->RatePlan->RatePlanId ?>" class="bfi-table-responsive" style="display:none;">
		<div class="bfi-resname-extra"><?php echo $currRateplan->ResName;?> <span class="bfi-meals bfi-meals-<?php echo $currRateplan->RatePlan->RefId ?>"><?php echo $currRateplan->RatePlan->Name ?></span></div>
		<!-- bfi-table-selectableprice -->
		<table class="bfi-table bfi-table-bordered bfi-table-resources bfi-table-selectableprice" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php _e('Information', 'bfi') ?></th>
					<th><div><?php _e('Min', 'bfi') ?><br /><?php _e('Max', 'bfi') ?></div></th>
					<th ><div><?php _e('Price', 'bfi') ?></div></th>
					<th><div><?php _e('Options', 'bfi') ?></div></th>
					<th><div><?php _e('Qt.', 'bfi') ?></div></th>
					<th><div><?php _e('Confirm your reservation', 'bfi') ?></div></th>
				</tr>
			</thead>
			<tbody>
<?php 
		foreach($selectablePrices as $selPrice) {

?>
				<tr class="data-sel-id-<?php echo $res->ResourceId ?>">
					<td >
					<?php echo $selPrice->Name; ?>
					<br />
					<?php
/*-----------scelta date e ore--------------------*/	
									if ($selPrice->AvailabilityType == 2)
									{
										$currCheckIn = DateTime::createFromFormat('Y-m-d\TH:i:s',$selPrice->CheckIn);
										$currCheckOut = DateTime::createFromFormat('Y-m-d\TH:i:s',$selPrice->CheckOut);
										$currDiff = $currCheckOut->diff($currCheckIn);

   
										$loadScriptTimePeriod = true;
										array_push($allTimePeriodResourceId, $selPrice->RelatedProductId );
//										$currCheckInString = date_i18n('D',$currCheckIn->getTimestamp()) ." " . $currCheckIn->format("d") ." " . date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y");
//										$currCheckOutString = date_i18n('D',$currCheckOut->getTimestamp()) ." " . $currCheckOut->format("d") ." " . date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y");
//										$currCheckInHour = $currCheckIn->format('H:i');
//										$currCheckOutHour = $currCheckOut->format('H:i');
//										$currDiffString = $currDiff->format('%h') ;

$currCheckInString = __('Select a period', 'bfi');
$currCheckOutString = "";
$currCheckInHour = "";
$currCheckOutHour = "";
$currDiffString = "-";

									?>
										<div class="bfi-timeperiod bfi-cursor" id="bfi-timeperiod-<?php echo $selPrice->RelatedProductId ?>" data-resid="<?php echo $selPrice->RelatedProductId ?>" data-checkin="<?php echo $currCheckIn->format('Ymd') ?>">
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-in', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckInString; ?></span> <span class="bfi-hide">-</span> <span class="bfi-time-checkin-hours bfi-hide"><?php echo $currCheckInHour; ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php _e('Check-out', 'bfi') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right bfi-hide"><span class="bfi-time-checkout"><?php  echo $currCheckOutString; ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOutHour; ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php _e('Total', 'bfi') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right bfi-hide"><span class="bfi-total-duration"><?php echo $currDiffString; ?></span> <?php _e('hours', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
/*-------------------------------*/	
									if ($selPrice->AvailabilityType == 3)
									{
										$loadScriptTimeSlot = true;
										array_push($allTimeSlotResourceId, $selPrice->RelatedProductId );
										$currDatesTimeSlot =  json_decode(BFCHelper::GetCheckInDatesTimeSlot($selPrice->RelatedProductId ,$alternativeDateToSearch));

										$listDayTS[$selPrice->RelatedProductId] = $currDatesTimeSlot;

										$currCheckIn = DateTime::createFromFormat('Ymd', $currDatesTimeSlot[0]->StartDate);
										$currCheckOut = clone $currCheckIn;
										$currCheckIn->setTime(0,0,0);
										$currCheckOut->setTime(0,0,0);
										$currCheckIn->add(new DateInterval('PT' . $currDatesTimeSlot[0]->TimeSlotStart . 'M'));
										$currCheckOut->add(new DateInterval('PT' . $currDatesTimeSlot[0]->TimeSlotEnd . 'M'));

										$currDiff = $currCheckOut->diff($currCheckIn);

										// overrides Availability by CheckInDatesTimeSlot
										$res->Availability = $currDatesTimeSlot[0]->Availability ;

									?>
										<div class="bfi-timeslot bfi-cursor" id="bfi-timeslot-<?php echo $selPrice->RelatedProductId?>" data-resid="<?php echo $selPrice->RelatedProductId?>" data-checkin="<?php echo $currCheckIn->format('Ymd') ?>"
										data-timeslotid="<?php echo $currDatesTimeSlot[0]->ProductId ?>" data-timeslotstart="<?php echo $currDatesTimeSlot[0]->TimeSlotStart ?>" data-timeslotend="<?php echo $currDatesTimeSlot[0]->TimeSlotEnd ?>"
										>
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

					</td>
					<td><!-- Min/Max -->
					</td>
					<td style="text-align:center;"><!-- price -->
						<div class="bfi-totalextrasselect" style="<?php echo ($selPrice->TotalDiscounted==0) ? "display:none;" : ""; ?>">
							<div align="center">
								<div class="bfi-percent-discount" style="<?php echo ($res->PercentVariation < 0 ? " display:block" : "display:none"); ?>" rel="<?php echo $SimpleDiscountIds ?>" rel1="<?php echo $res->ResourceId ?>">
									<span class="bfi-percent"><?php echo $res->PercentVariation ?></span>% <i class="fa fa-question-circle bfi-cursor-helper" aria-hidden="true"></i>
								</div>
							</div>
							<div data-value="<?php echo $selPrice->TotalAmount ?>" class="bfi-discounted-price bfi_<?php echo $currencyclass ?>" style="display:<?php echo ($selPrice->TotalDiscounted < $selPrice->TotalAmount)?"":"none"; ?>;"><?php echo BFCHelper::priceFormat($selPrice->TotalAmount) ?></div>
							<div data-value="<?php echo $selPrice->TotalDiscounted?>" class="bfi-price  <?php echo ($currRateplan->Price < $selPrice->TotalDiscounted ? "bfi-red" : "") ?> bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($selPrice->TotalDiscounted) ?></div>
						</div>
					</td>
					<td><!-- options -->

					</td>
					<td>
<?php 
			$availability = array();
			$startAvailability = 0;
			$clickFunction = "bfi_quoteCalculatorServiceChanged(this)";
			$startAvailability = $selPrice->MinQt != null ? (int)$selPrice->MinQt : 0;
			$endAvailability = $selPrice->MaxQt != null ? min((int)$selPrice->MaxQt, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE)  : COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE;
			for ($i = $startAvailability; $i <= $endAvailability; $i++)
			{
				array_push($availability, $i);
			}
			if ($selPrice->AvailabilityType == 2){
				$availability = array(0);
				$clickFunction ="bfi_updateQuoteService();";
			}


?>

						<script>
							servicesAvailability[<?php echo $selPrice->PriceId ?>] =<?php echo (!empty($selPrice->Availability)? min($selPrice->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE) : 0) ?> ;
						</script>
						<select class="ddlrooms ddlrooms-<?php echo $selPrice->RelatedProductId?> ddlextras inputmini" 
							onchange="<?php echo $clickFunction ?>" 
							data-maxvalue="<?php echo $selPrice->MaxQt ?>" 
							data-minvalue="<?php echo $selPrice->MinQt ?>" 
							data-resid="<?php echo $selPrice->RelatedProductId ?>" 
							data-rateplanid="<?php echo $currRateplan->RatePlan->RatePlanId ?>" 
							data-availabilityType="<?php echo $selPrice->AvailabilityType ?>" 
							data-bindingproductid="<?php echo $res->ResourceId ?>"
							data-baseprice="<?php echo $selPrice->TotalDiscounted ?>" 
							data-basetotalprice="<?php echo $selPrice->TotalAmount ?>"
							data-price="<?php echo BFCHelper::priceFormat($selPrice->TotalDiscounted ,2,".","") ?>" 
							data-totalprice="<?php echo BFCHelper::priceFormat($selPrice->TotalAmount ,2,".","") ?>" 
							>
							<?php 
								foreach ($availability as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($selPrice->CalculatedQt == $number)?"selected":""; //selected( $selectedtAvailability, $number ); ?>><?php echo $number ?></option><?php
								}
							?>
						</select>
					</td>
					<td>
					<?php if($countPrices==0){ ?>
							<div class="totalextrasstay bfi-book-now" style="display:none;">
								<div class="bfi-resource-total"><span></span> <?php _e('selected items', 'bfi') ?></div>
								<div class="bfi-extras-total"><span></span> <?php _e('selected services', 'bfi') ?></div> 
								<div class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?>" style="display:none;"></div>
								<div class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>" ></div>
								<div class="bfi-item-secondary-more" onclick="BookNow(this);">
									<?php _e('Book Now', 'bfi') ?>
								</div>
								<div class="bfi-request-now" onclick="BookNow(this);">
									<?php _e('Request Now', 'bfi') ?>
								</div>
							</div>
						<?php } ?>
					</td>
				</tr>
<?php 
$countPrices+=1;
		}//end foreach selPrices
?>

			</tbody>
		</table>
		<!-- end bfi-table-selectableprice -->
		</div>
<?php 
	}//end foreach div-selectableprice resRateplans

	}//end foreach div-selectableprice allResourceId
?>
    </div>
<?php 
 } //end if(!empty div-selectableprice
?>
</div>
<form action="<?php echo esc_url($formOrderRouteBook) ?>" id="frm-order" method="post"></form>
	
<script type="text/javascript">
var localeSetting = "<?php echo substr($language,0,2); ?>";
var productAvailabilityType = <?php echo $ProductAvailabilityType?>;
var allStays = <?php echo json_encode($allRatePlans) ?>; 
	
	function updateTitleBooking(classToAdd,classToRemove){
		jQuery("#ui-datepicker-div").addClass("notranslate");
		jQuery("#ui-datepicker-div").addClass(classToAdd);
		jQuery("#ui-datepicker-div").removeClass(classToRemove);
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));
		month1 = ('0' + d1[1]).slice(-2);
		month2 = ('0' + d2[1]).slice(-2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			month1 = from.toLocaleString(localeSetting, { month: "short" });              
			month2 = to.toLocaleString(localeSetting, { month: "short" });            
		}

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
//		var productAvailabilityType = jQuery('#productAvailabilityType').val();
		var strSummary = 'Check-in '+('0' + from.getDate()).slice(-2)+' '+ month1;
		var strSummaryDays = "(" +days+" <?php echo strtolower (__('Nights', 'bfi')) ?>)";
		if (productAvailabilityType == 0) {
			days += 1;
			strSummaryDays ="(" +days+" <?php echo strtolower (__('Days', 'bfi')) ?>)";
		}
		if (productAvailabilityType == 0 || productAvailabilityType == 1) {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
		}
		jQuery("#durationdays").html(days);

		jQuery('#ui-datepicker-div').attr('data-before',strSummary);
	
	}

	function printChangedDateBooking(date, elem) {
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(d1[2], d1[1]-1, d1[0]);
		var to   = new Date(d2[2], d2[1]-1, d2[0]);

		day1  = ('0' + from.getDate()).slice(-2), 
			
		month1 = from.toLocaleString(localeSetting, { month: "short" }),              
		year1 =  from.getFullYear(),
		weekday1 = from.toLocaleString(localeSetting, { weekday: "short" });

		day2  = ('0' + to.getDate()).slice(-2),  
		month2 = to.toLocaleString(localeSetting, { month: "short" }),              
		year2 =  to.getFullYear(),
		weekday2 = to.toLocaleString(localeSetting, { weekday: "short" });
		
		jQuery('.checkinBooking').find('.day span').html(day1);
		jQuery('.checkoutBooking').find('.day span').html(day2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			jQuery('.checkinBooking').find('.monthyear p').html(weekday1 + "<br />" + month1+" "+year1); 
			jQuery('.checkoutBooking').find('.monthyear p').html(weekday2 + "<br />" + month2+" "+year2);
			jQuery('#bfi_lblchildrenagesatcalculator').html("<?php echo strtolower (__('on', 'bfi')) ?> " + day2 + " " + month2 + " " + year2);
		} else {
			jQuery('.checkinBooking').find('.monthyear p').html(d1[1]+"/"+d1[2]);  
			jQuery('.checkoutBooking').find('.monthyear p').html(d2[1]+"/"+d2[2]);
			jQuery('#bfi_lblchildrenagesatcalculator').html("<?php echo strtolower (__('on', 'bfi')) ?> " + day2 + " " + d2[1] + " " + d2[2]);
		}

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		if (productAvailabilityType == 0) {
			days += 1;
		}
		jQuery("#durationdays").html(days);

	}

	function insertCheckinTitleBooking() {
		setTimeout(function() {updateTitleBooking("checkin","checkout")}, 1);
	}
	function insertCheckoutTitleBooking() {
		setTimeout(function() {updateTitleBooking("checkout","checkin")}, 1);
	}
	var calculator_checkin = null;
	var calculator_checkout = null;

	jQuery(function($) {
		
		UpdateQuote();

		jQuery('.bfi-options-help i').webuiPopover({trigger:'hover',placement:'right-bottom'});

		jQuery(".bfi-percent-discount").on("click", function (e) {
			e.preventDefault();
			var showdiscount = function (obj, text) {
								obj.find("i").first().switchClass("fa-spinner fa-spin","fa-question-circle")
								obj.webuiPopover({
									content : text,
									container: document.body,
									closeable:true,
									placement:'auto-bottom',
									dismissible:true,
									trigger:'manual',
									type:'html'
								});
								obj.webuiPopover('show');

            };
            var discountIds = jQuery(this).attr('rel');

			if (!bookingfor.offersLoaded.hasOwnProperty(discountIds)) {
				jQuery(this).find("i").first().switchClass("fa-question-circle","fa-spinner fa-spin")
                bookingfor.GetDiscountsInfo(discountIds,"<?php echo $language ?>", jQuery(this), showdiscount);

            } else {
				showdiscount(jQuery(this), bookingfor.offersLoaded[discountIds]);
            }
        });		
//		jQuery(".bfi-percent-discount").on("blur", function (e) {
//			jQuery(this).webuiPopover('hide');
//		});	
		jQuery(".bfi-percent-discount").focusout(function () {
			jQuery(this).webuiPopover('hide');
		});	

		calculator_checkin = function() { $("#<?php echo $checkinId; ?>").datepicker({
			numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
			,defaultDate: "+2d"
			,dateFormat: "dd/mm/yy"
			, minDate: '<?php echo $startDate->format('d/m/Y') ?>'
			, maxDate: '<?php echo $endDate->format('d/m/Y') ?>'
			, onSelect: function(date) {  
				jQuery(".ui-datepicker a").removeAttr("href"); 
				checkDateBooking<?php echo $checkinId; ?>(jQuery, jQuery(this), date); 
				if(productAvailabilityType ==2 || productAvailabilityType==3){
					calculateQuote();
				}else{
					dateCalculatorChanged();
					printChangedDateBooking(date, jQuery(this)); 
				}
			}
			, showOn: 'button'
			, beforeShowDay: function (date) {
				return closedBooking(date, 1, daysToEnable); 
				}
			, beforeShow: function(dateText, inst) {
				$('#ui-datepicker-div').addClass('notranslate');  
				jQuery(this).attr("readonly", true); 
				insertCheckinTitleBooking(); 
				}
			, onChangeMonthYear: function(dateText, inst) { 
				insertCheckinTitleBooking(); 
				}
          , buttonText: <?php echo $checkintext; ?>,
		})};
		calculator_checkin();
		calculator_checkout = function() { $("#<?php echo $checkoutId; ?>").datepicker({
			numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
			,defaultDate: "+2d"
			,dateFormat: "dd/mm/yy"
			, minDate: '<?php echo $startDate2->format('d/m/Y') ?>'
			, maxDate: '<?php echo $endDate2->format('d/m/Y') ?>'
			, onSelect: function(date) {  
				$(".ui-datepicker a").removeAttr("href"); 
				dateCalculatorChanged();
				printChangedDateBooking(date, jQuery(this)); 
				}
			, showOn: 'button'
			, beforeShowDay: function (date) {
				return closedBooking(date, 0, checkOutDaysToEnable); 
				}
			, beforeShow: function(dateText, inst) {
				$('#ui-datepicker-div').addClass('notranslate');  
				jQuery(this).attr("readonly", true); 
				insertCheckoutTitleBooking(); 
				}
			, onChangeMonthYear: function(dateText, inst) {
				insertCheckoutTitleBooking(); 
				}
          , buttonText: <?php echo $checkouttext; ?>,
		})};
		
		calculator_checkout();

//		checkDateBooking<?php echo $checkinId; ?>(jQuery, jQuery('#<?php echo $checkoutId?>'), jQuery('#<?php echo $checkoutId?>').datepicker("getDate")); 

		//fix Google Translator and datepicker
		$('.ui-datepicker').addClass('notranslate');

		$(".bfi_resource-calculatorForm-childrenages").hide();
		$(".bfi_resource-calculatorForm-childrenages select").hide();
		checkChildren(<?php echo $nch ?>,<?php echo $showChildrenagesmsg ?>);
		$(".bfi_resource-calculatorForm-children select#childrencalculator").change(function() {
			checkChildren($(this).val(),0);
		});

	});
	
	function closedBooking(date, offset, enableDays) {
	  var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	  var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
	  var strdate = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth()+1)).slice(-2) + "/" + date.getFullYear();
	  
	  var d1 = checkindate.split("/");
	  var d2 = checkoutdate.split("/");
	  var c = strdate.split("/");

	  var from = new Date(d1[2], d1[1]-1, d1[0]);
	  var to   = new Date(d2[2], d2[1]-1, d2[0]);
	  var check = new Date(c[2], c[1]-1, c[0]);
	if(productAvailabilityType ==2 || productAvailabilityType ==3){
		to = from;
	}

	  var dayenabled = false;
		var month = date.getMonth() + 1;
		var day = date.getDate();
		var year = date.getFullYear();
<?php if(!empty($resourceId)) { ?>
		var copyarray = jQuery.extend(true, [], enableDays);
		for (var i = 0; i < offset; i++)
			copyarray.pop();
		var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
		if (jQuery.inArray(Number(datereformat), copyarray) != -1) {
			dayenabled = true;
			//return [true, 'greenDay'];
		}
<?php }else{ ?>
		dayenabled = true;
<?php } ?>

	//	return [false, 'redDay'];


	  arr = [dayenabled, ''];  
	  if(check.getTime() == from.getTime()) {
	//  	console.log(from);
	//  console.log(to);
	//  console.log(check);
		arr = [dayenabled, 'date-start-selected', 'date-selected'];
	  }
	  if(check.getTime() == to.getTime()) {
	//  	console.log(from);
	//  console.log(to);
	//  console.log(check);
		arr = [dayenabled, 'date-end-selected', 'date-selected'];  
	  }
	  if(check > from && check < to) {
		arr = [dayenabled, 'date-selected', 'date-selected'];
	  }
	  return arr;
	}

	function checkChildren(nch,showMsg) {
		jQuery(".bfi_resource-calculatorForm-childrenages").hide();
		jQuery(".bfi_resource-calculatorForm-childrenages select").hide();
		if (nch > 0) {
			jQuery(".bfi_resource-calculatorForm-childrenages select").each(function(i) {
				if (i < nch) {
					var id=jQuery(this).attr('id');
					jQuery(this).css('display', 'inline-block');
				}
			});
			jQuery(".bfi_resource-calculatorForm-childrenages").show();
			if(showMsg===1) { 
				showpopovercalculator();
			}
		}
	}

</script>
<script type="text/javascript">
<!--
function getDisplayDate(date) {
	return date == null ? "" : bookingfor.pad(date.getDate(),2) + '/' + bookingfor.pad((date.getMonth() + 1),2) + '/' + date.getFullYear();
}

function enableSpecificDates(date, offset, enableDays) {
	var month = date.getMonth() + 1;
	var day = date.getDate();
	var year = date.getFullYear();
	var copyarray = jQuery.extend(true, [], enableDays);
	for (var i = 0; i < offset; i++)
		copyarray.pop();
	var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
	if (jQuery.inArray(Number(datereformat), copyarray) != -1) {
		return [true, 'greenDay'];
	}
	return [false, 'redDay'];
}

function onEnsureCheckOutDaysToEnableSuccess() {
	if (!checkOutDaysToEnable || checkOutDaysToEnable.length == 0) {
		jQuery("#calcheckout").unblock();
		return;
	}
	if (checkOutDaysToEnable[0] == 0)
	{
		jQuery("#calcheckout").unblock();
		return;
	}
//alert(checkOutDaysToEnable[0]);
//	var date = jQuery.datepicker.parseDate('yyyymmdd', checkOutDaysToEnable[0]);
	var strDate = '' + checkOutDaysToEnable[0]
	var date = new Date(strDate.substr(0,4),strDate.substr(4,2)-1,strDate.substr(6,2));

	jQuery('#<?php echo $checkoutId?>').datepicker("option", "minDate", date);
	var datetocheck = jQuery('#<?php echo $checkoutId?>').datepicker("getDate");
	//checkout.datepicker("option", "minDate", date);
	//var datetocheck = checkout.datepicker("getDate");
	if (!enableSpecificDates(datetocheck, 0, checkOutDaysToEnable)[0]) {
		jQuery("#<?php echo $checkoutId?>").val(getDisplayDate(date));
//		printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	}
//		printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	jQuery("#calcheckout").unblock();

//	if (raiseUpdate) {
//		btnClick().click();
//	}
}


function checkDateBooking<?php echo $checkinId?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);

	var offsetDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

<?php if(!empty($variationPlanMinDuration)) { ?>
	offsetDate.setDate(offsetDate.getDate() + <?php echo $variationPlanMinDuration-1 //night ?>);
<?php } ?>

	switch (productAvailabilityType) {
		case 0:
			$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
			if ($("#<?php echo $checkoutId?>").datepicker("getDate") < date) {
				$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
			}
		case 1:
			offsetDate.setDate(offsetDate.getDate() + 1);
			$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
			if ($("#<?php echo $checkoutId?>").datepicker("getDate") <= date) {
				$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(offsetDate.getFullYear(), offsetDate.getMonth(), offsetDate.getDate()));
			}
			break;
		case 3:
			offsetDate.setDate(offsetDate.getDate() + 1);
			$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
			$("#<?php echo $checkoutId?>").datepicker("option", "maxDate", offsetDate);
			$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(offsetDate.getFullYear(), offsetDate.getMonth(), offsetDate.getDate()));
			//printChangedDate();
			break;
	}

<?php if(!empty($variationPlanMaxDuration)) { ?>
	var offsetEndDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
	offsetEndDate.setDate(offsetEndDate.getDate() + <?php echo $variationPlanMaxDuration-1 //night ?>);
	$("#<?php echo $checkoutId?>").datepicker("option", "maxDate", offsetDate);
<?php } ?>

}
jQuery(document).ready(function() {

	checkDateBooking<?php echo $checkinId; ?>(jQuery, jQuery('#<?php echo $checkinId?>'), jQuery('#<?php echo $checkinId?>').datepicker({ dateFormat: "dd/mm/yy" }).val()); 

});


function quoteCalculatorChanged(callback) {
	jQuery('#bfi_lblchildrenagescalculator').webuiPopover("hide");
	jQuery('#resourceQuote').hide();
	jQuery('#resourceSummary').hide();
	jQuery('#errorbooking').hide();
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
		jQuery('#calculateButton').removeClass("not-active");
		jQuery('.bfi-result-list').hide();
	}else{
		jQuery('#calculateButton').addClass("not-active");
	}
//	jQuery('#calculateButton').show();
	/*calculateQuote();*/
}
function dateCalculatorChanged(callback) {
<?php if(!empty($resourceId)) { ?>
	getAjaxDate(callback)
<?php } ?>
	jQuery('#resourceQuote').hide();
	jQuery('#resourceSummary').hide();
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
		jQuery('#calculateButton').removeClass("not-active");
		jQuery('.bfi-result-list').hide();
	}else{
		jQuery('#calculateButton').addClass("not-active");
	}
//	jQuery('#calculateButton').show();
	/*calculateQuote();*/
}

function countMinAdults(){
	var minAdults = 0;
	var numAdults = new Number(jQuery('#adultscalculator').val() || 0);
	var numSeniores = new Number(jQuery('#seniorescalculator').val() || 0);
	minAdults = numAdults + numSeniores;
	return minAdults;
}


function calculateQuote() {
	jQuery('#bfi_lblchildrenagescalculator').webuiPopover("hide");
//		jQuery('#calculatorForm').attr("action","<?php echo $formRoute?>?format=calc&tmpl=component")
//		jQuery('#calculatorForm').submit();
	jQuery('#showmsgchildagecalculator').val(0);
	var numChildren = new Number(jQuery(".bfi_resource-calculatorForm-children select#childrencalculator").val());
	checkChildren(numChildren,0);
	jQuery(".bfi_resource-calculatorForm-childrenages select:visible option:selected").each(function(i) {
		if(jQuery(this).text()==""){
			jQuery('#showmsgchildagecalculator').val(1);
			return;
		}
	});

	jQuery('input[name="state"]','#calculatorForm').val('');
	jQuery('input[name="extras[]"]','#calculatorForm').val('');
	jQuery('.bfi-percent-discount').webuiPopover('destroy');
	jQuery('#calculatorForm').ajaxSubmit(getAjaxOptions());
}

function showpopovercalculator() {
		jQuery('#bfi_lblchildrenagescalculator').webuiPopover({
			content : jQuery("#bfi_childrenagesmsgcalculator").html(),
			container: document.body,
			cache: false,
			placement:"auto-bottom",
			maxWidth: "300px",
			type:'html'
		});
		jQuery('#bfi_lblchildrenagescalculator').webuiPopover("show");
}
jQuery(window).resize(function(){
	jQuery('#bfi_lblchildrenagescalculator').webuiPopover("hide");
});


function getAjaxDate(callback) {
	// prepare Options Object 
	var fromDate = jQuery('#<?php echo $checkinId?>').datepicker('getDate');
	var month = fromDate.getMonth() + 1;
	var day = fromDate.getDate();
	var year = fromDate.getFullYear();
	var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);	
	jQuery('#calcheckout').block({message: ''});
	var options = { 
	    url:        urlCheck + '?task=listDate&resourceId=' + unitId + '&checkin=' + datereformat + "&simple=1", 
	    dataType: 'text',
		success: function(data) { 
            checkOutDaysToEnable = data.split(',');
			for(var i=0; i<checkOutDaysToEnable.length; i++) { checkOutDaysToEnable[i] = +checkOutDaysToEnable[i]; } 
			onEnsureCheckOutDaysToEnableSuccess();
			if (callback) {
				callback;
			}
	    } 
	}; 
	jQuery.ajax(options);

	//return options;
	//form.ajaxForm(options);
}


function getAjaxOptions(callback) {
	// prepare Options Object 
	var options = { 
	    target:     '#calculator',
	    replaceTarget: true, 
	    url:        '<?php echo $formRoute?>', 
	    beforeSend: function() {
	    	jQuery('#calculator').block({
					message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>',
					css: {border: 'none'},
					overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
				});
		},
	    success: function() { 
			jQuery('#calculator').unblock();
			calculator_checkin();
			calculator_checkout();
			if (callback) {
				callback;
			}
	    } 
	}; 
	return options;
	//form.ajaxForm(options);
}

    var totalOrderPriceLoaded = false;
    var totalOrderPrice = 0;
    var totalOrderPriceWhitOutDiscount = 0;



//-->
</script>
<script type="text/javascript">
	
jQuery(document).ready(function() {

	jQuery("#calculatorForm .checking-container .ui-datepicker-trigger").click(function() {
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                if(jQuery("#calculatorForm .lastdatecheckout button.ui-datepicker-trigger").is(":visible")){
					jQuery("#calculatorForm .lastdate button.ui-datepicker-trigger").trigger("click");
				}
                jQuery("#calculatorForm .ui-datepicker-trigger").each(function() {
                    jQuery(this).addClass("activeclass");
                });
                jQuery("#calculatorForm .checking-container .ui-datepicker-trigger").removeClass("activeclass");
                jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
                jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
                jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
            }
        });
    })
    jQuery("#calculatorForm .ui-datepicker-trigger").click(function() {
        jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
        jQuery("#calculatorForm .ui-datepicker-trigger").each(function() {
            jQuery(this).removeClass("activeclass");
        });
        jQuery(this).addClass("activeclass");
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

    });
    jQuery("#ui-datepicker-div").click(function() {
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
    });
    jQuery("#calculatorForm").hover(function(){
        jQuery(".ui-datepicker-trigger").click(function() {
            jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
            jQuery(".ui-datepicker-trigger").each(function() {
                jQuery(this).removeClass("activeclass");
            });
            jQuery(this).addClass("activeclass");
            jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
            jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

        });
    });
});

<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1 ): ?>
jQuery(function($) {
	<?php if(isset($eecmainstay)): ?>
	callAnalyticsEEc("addProduct", [<?php echo json_encode($eecmainstay); ?>], "item");
	<?php endif; ?>
	<?php if(count($eecstays) > 0 && $currentState != 'optionalPackages'): ?>
	callAnalyticsEEc("addImpression", <?php echo json_encode($eecstays); ?>, "list", "Suggested Products");
	<?php endif; ?>
});
<?php endif; ?>
<?php if(isset($criteoConfig) && !empty($criteoConfig) && $criteoConfig->enabled): ?>
window.criteo_q = window.criteo_q || []; 
window.criteo_q.push( 
	{ event: "setAccount", account: <?php echo $criteoConfig->campaignid ?>}, 
	{ event: "setSiteType", type: "d" }, 
	{ event: "viewSearch", checkin_date: "<?php echo $checkin->format('d/m/Y') ?>", checkout_date: "<?php echo $checkout->format('d/m/Y') ?>"},
	{ event: "setEmail", email: "" }, 
	{ event: "viewItem", item: "<?php// echo $criteoConfig->merchants[0] ?>" }
);
<?php endif; ?>

</script>
<!-- --------------------------------------------------------------------------------------------------------------------------------------------------------- -->
<?php if($loadScriptTimePeriod || $loadScriptTimeSlot) { ?>
    <script>
        //TimeSlot
        var strAlternativeDateToSearch = "<?php echo $alternativeDateToSearch->format('d/m/Y') ?>";
        var strEndDate = "<?php echo $checkout->format('d/m/Y') ?>";
        var dateToUpdate = <?php echo $checkin->format('Ymd') ?>;
		jQuery(document).ready(function() {
			if(jQuery(".ui-dialog").length){
				jQuery(".ui-dialog").remove();
			}
		});

    </script>
<?php } ?>

<?php if($loadScriptTimePeriod) { 
	$listDayTP = array();
	$allTimePeriodResourceId = array_unique($allTimePeriodResourceId);
	foreach ($allTimePeriodResourceId as $resId) { 
		$listDayTP[$resId] = json_decode(BFCHelper::GetCheckInDatesPerTimes($resId,$alternativeDateToSearch,$duration+2));
	}
	?>
    <script>
        var txtSelectADay = "<?php _e('Please, select a day', 'bfi') ?>";
        var daysToEnableTimePeriod = <?php echo json_encode($listDayTP) ?>; 
//        var strbuttonTextTimePeriod = "<div class='buttoncalendar timePeriodli'><div class='dateone day'><span><?php echo $checkin->format("d") ?></span></div><div class='dateone daterwo monthyear'><p><?php echo date_i18n('D',$checkin->getTimestamp()) ?><br /><?php echo date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y") ?> </p></div></div>";
        var strbuttonTextTimePeriod = "<?php echo date_i18n('D',$checkin->getTimestamp()) ?> <?php echo $checkin->format("d") ?> <?php echo date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y") ?>";
        var urlGetCompleteRatePlansStay = urlCheck + '?task=getCompleteRateplansStay';
        var urlGetListCheckInDayPerTimes = urlCheck + '?task=getListCheckInDayPerTimes';

        var carttypeCorrector = <?php echo ($cartType == 0?1:0) ?>;
		var dialogTimeperiod;
		jQuery(document).ready(function() {
			initDatepickerTimePeriod();
			jQuery("#bfi-timeperiod-select").attr("data-resid",0);
//			jQuery(".bfi-timeperiod-change").dialog('destroy');
			dialogTimeperiod = jQuery("#bfimodaltimeperiod").dialog({
				title: "<?php _e('Change your details', 'bfi') ?>",
				autoOpen: false,
				modal: true,
				width: 'auto',
				maxWidth: "300px",
				close: function() {
				}
			});
			bfi_currTRselected = null;
			jQuery(".bfi-result-list").on("click",".bfi-timeperiod", function (e) {
//			debugger;
				var currResId = jQuery("#bfi-timeperiod-select").attr("data-resid");
				var newResId = jQuery(this).attr("data-resid");
				var newDate = jQuery(this).attr("data-checkin");
//				if(currResId!=newResId &&  bfi_currTRselected != jQuery(this).closest("tr")){
				if (bfi_currTRselected != jQuery(this).closest("tr")){
					bfi_currTRselected = jQuery(this).closest("tr");
					jQuery("#bfi-timeperiod-select").attr("data-resid", newResId);
					jQuery("#selectpickerTimePeriodStart").attr("data-resid", newResId);
					jQuery("#bfi-timeperiod-select").attr("data-checkin", newDate);
					jQuery("#bfimodaltimeperiodcheckin").attr("data-resid", newResId);
					jQuery("#bfimodaltimeperiodcheckin").datepicker("setDate", jQuery.datepicker.parseDate( "yymmdd", newDate) );
					dateTimePeriodChanged(jQuery("#bfimodaltimeperiodcheckin"),jQuery("#bfimodaltimeperiodcheckin").datepicker("getDate"))
//					updateTimePeriodRange(newDate, newResId, jQuery("#bfimodaltimeperiod"));
//					jQuery('.ui-datepicker-current-day').click();
//					var currCheckinhours = jQuery(this).find(".bfi-time-checkin-hours").first();
//					var currCheckouthours = jQuery(this).find(".bfi-time-checkout-hours").first();
//					jQuery("#selectpickerTimePeriodStart").val(currCheckinhours.html());
//					jQuery("#selectpickerTimePeriodEnd").val(currCheckinhours.html());
				}

//				var currMess = jQuery("#bfi-timeperiod-change-"+jQuery(this).attr("data-id")).clone(true,true);
//				jQuery("#bfi-timeperiod-source-"+jQuery(this).attr("data-id")).empty();
//				jQuery("#bfi-timeperiod-change-"+jQuery(this).attr("data-id"));
//				jQuery.blockUI({ message: currMess }); 
//				jQuery("#bfi-timeperiod-change-"+jQuery(this).attr("data-id")).show();
				dialogTimeperiod.dialog( "open" );

			 });
//            jQuery(".ChkAvailibilityFromDateTimePeriod:not(.extraprice)").each(function(){
//                updateTimePeriodRange(<?php echo $checkin->format('Ymd') ?>, jQuery(this).attr("data-id"), jQuery(this));
//            });
        });
    </script>
<?php } ?>

<?php if($loadScriptTimeSlot) { 
	$allTimeSlotResourceId = array_unique($allTimeSlotResourceId);
//	foreach ($allTimeSlotResourceId as $resId) { 
//		$listDayTS[$resId] = json_decode(BFCHelper::GetCheckInDatesTimeSlot($resId,$alternativeDateToSearch));
//	}

	?>
    <script>
        //TimeSlot
//        var strbuttonTextTimeSlot = "<div class='buttoncalendar timeSlotli'><div class='dateone day'><span><?php echo $checkin->format("d") ?></span></div><div class='dateone daterwo monthyear'><p><?php echo date_i18n('D',$checkin->getTimestamp()) ?><br /><?php echo date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y") ?> </p></div></div>";
        var strbuttonTextTimeSlot = "<?php echo date_i18n('D',$checkin->getTimestamp()) ?> <?php echo $checkin->format("d") ?> <?php echo date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y") ?>";
        var daysToEnableTimeSlot = <?php echo json_encode($listDayTS) ?>;
        var currTimeSlotDisp = {};
		var dialogTimeslot;
		jQuery(document).ready(function () {
			initDatepickerTimeSlot();
			jQuery("#bfi-timeslot-select").attr("data-resid",0);
			dialogTimeslot = jQuery("#bfimodaltimeslot").dialog({
				title: "<?php _e('Change your details', 'bfi') ?>",
				autoOpen: false,
				modal: true,
				width: 'auto',
				maxWidth: "300px",
				close: function() {
				}
			});
			jQuery(".bfi-timeslot").on("click", function (e) {
				var currResId = jQuery("#bfi-timeslot-select").attr("data-resid");
				var newResId = jQuery(this).attr("data-resid");
				var newDate = jQuery(this).attr("data-checkin");
				if(currResId!=newResId ){
					jQuery("#bfi-timeslot-select").attr("data-resid", newResId);
					jQuery("#selectpickerTimeSlotRange").attr("data-resid", newResId);
					jQuery("#bfi-timeslot-select").attr("data-checkin", newDate);
					jQuery("#bfimodaltimeslotcheckin").attr("data-resid", newResId);
					jQuery("#bfimodaltimeslotcheckin").datepicker("setDate", jQuery.datepicker.parseDate( "yymmdd", newDate) );
					dateTimeSlotChanged(jQuery("#bfimodaltimeslotcheckin"));
				}
				dialogTimeslot.dialog( "open" );

			 });

		});
    </script>

<?php } ?>


<script type="text/javascript">
<!--
	var CartMultimerchantEnabled = <?php echo $CartMultimerchantEnabled  ? "true" : "false" ?>;
	function BookNow() {
//       debugger;
		var sendtocart = 0;

		var Order = { Resources: [], SearchModel: {}, TotalAmount: 0, TotalDiscountedAmount: 0 };
		Order.SearchModel = jQuery('#calculatorForm').serializeObject();
		Order.SearchModel.MerchantId = <?php echo $merchant->MerchantId ?>;
		Order.SearchModel.AdultCount = new Number(Order.SearchModel.adults || 0);
		Order.SearchModel.ChildrenCount = new Number(Order.SearchModel.children || 0);
		Order.SearchModel.SeniorCount = new Number(Order.SearchModel.seniors || 0);
		Order.SearchModel.ChildAges = [Order.SearchModel.childages1,Order.SearchModel.childages2,Order.SearchModel.childages3,Order.SearchModel.childages4,Order.SearchModel.childages5];
		currPaxNumber = Order.SearchModel.AdultCount + Order.SearchModel.ChildrenCount + Order.SearchModel.SeniorCount ;
		currPaxAges = new Array();
		for (i=0;i<Order.SearchModel.AdultCount ; i++)	
		{
			currPaxAges.push(<?php echo COM_BOOKINGFORCONNECTOR_ADULTSAGE ?>);
		}
		for (i=0;i<Order.SearchModel.SeniorCount  ; i++)	
		{
			currPaxAges.push(<?php echo COM_BOOKINGFORCONNECTOR_SENIORESAGE ?>);
		}
		for (i=0;i<Order.SearchModel.ChildrenCount ; i++)	
		{
			currPaxAges.push(Order.SearchModel.ChildAges[i]);
		}
		
		var FirstResourceId = 0;
		var currPolicy = [];
		jQuery(".ddlrooms-indipendent ").each(function(index,ddlroom){
			var currResId = jQuery(this).attr('data-resid');
			var currRateplanId = new Number(jQuery(this).attr('data-ratePlanId') || 0);
			var currQtSelected = jQuery(this).val();
			var currAvailabilityType = new Number(jQuery(this).attr('data-availabilitytype') || 1);

			if( currQtSelected > 0){
				sendtocart = Number(jQuery(this).attr("data-isbookable")||0);
				for (var i = 1; i <= currQtSelected; i++) {
					currPolicy.push(new Number(jQuery(this).attr('data-policyId') || 0));
					var currResourceRequest = {
						ResourceId: new Number(currResId || 0),
						FromDate: (sendtocart==0)?jQuery(this).attr('data-checkin-ext'):jQuery(this).attr('data-checkin'),
						ToDate: (sendtocart==0)?jQuery(this).attr('data-checkout-ext'):jQuery(this).attr('data-checkout'),
						PolicyId: new Number(jQuery(this).attr('data-policyId') || 0),
						PaxNumber:currPaxNumber,
						PaxAges: currPaxAges,
						IncludedMeals: jQuery(this).attr('data-includedmeals'),
						TouristTaxValue: jQuery(this).attr('data-touristtaxvalue'),
						VATValue: jQuery(this).attr('data-vatvalue'),
						MerchantId: <?php echo $merchant->MerchantId ?>,
						RatePlanId: currRateplanId,
						AvailabilityType:currAvailabilityType,
						SelectedQt: 1,
						TotalDiscounted:  jQuery(this).attr('data-baseprice'),
						TotalAmount:  jQuery(this).attr('data-basetotalprice'),
						AllVariations:   jQuery(this).attr('data-allvariations'),
						PercentVariation:   jQuery(this).attr('data-percentvariation'),
						MinPaxes:   jQuery(this).attr('data-minpaxes'),
						MaxPaxes:   jQuery(this).attr('data-maxpaxes'),
						ExtraServices: []
					};

					if(currAvailabilityType==2){
						var currTr = jQuery("#bfi-timeperiod-"+currResId);
						currResourceRequest.TimeMinStart = currTr.attr("data-timeminstart");
						currResourceRequest.TimeMinEnd = currTr.attr("data-timeminend");
						currResourceRequest.CheckInTime = currTr.attr("data-checkintime");
						currResourceRequest.TimeDuration = currTr.attr("data-duration");
					}
					if(currAvailabilityType==3){
						var currTr = jQuery("#bfi-timeslot-"+currResId);
						currResourceRequest.FromDate = (sendtocart==0)?currTr.attr('data-checkin'):currTr.attr('data-checkin-ext');
						currResourceRequest.TimeSlotId = currTr.attr("data-timeslotid");
						currResourceRequest.TimeSlotStart = currTr.attr("data-timeslotstart");
						currResourceRequest.TimeSlotEnd = currTr.attr("data-timeslotend");
					}

					//--------recupero extras....
					
					jQuery("#services-room-" + i + "-" + currResId + "-" + currRateplanId).find(".ddlrooms").each( function( index, element ){
						var currValue = jQuery(this).val();
						var currPriceId = jQuery(this).attr("data-resid");
						var currPriceAvailabilityType = jQuery(this).attr("data-availabilityType");
						if(currValue!="0"){
							var extraValue = currPriceId + ":" + currValue;
							if(currPriceAvailabilityType =="2"){
								var currSelectData = jQuery(this).closest("tr").find(".bfi-timeperiod").first();				
								extraValue += ":" + currSelectData.attr("data-checkin") + currSelectData.attr("data-timeminstart") + ":" + currSelectData.attr("data-duration") + "::::"
							}
							if(currPriceAvailabilityType =="3"){
								var currSelectData = jQuery(this).closest("tr").find(".bfi-timeslot").first();	
								extraValue += ":::" + currSelectData.attr("data-timeslotid")  + ":" + currSelectData.attr("data-timeslotstart") + ":" + currSelectData.attr("data-timeslotend") + ":" + currSelectData.attr("data-checkin") + "::::"
							}

							var currExtraService = {
								Value:extraValue,
								PriceId: currPriceId,
								CalculatedQt: currValue,
								ResourceId: currResId, 
								TotalDiscounted: (sendtocart==0)?parseFloat(jQuery(this).attr('data-baseprice'))*currValue:jQuery(this).attr('data-baseprice'), 
								TotalAmount:  (sendtocart==0)?parseFloat(jQuery(this).attr('data-basetotalprice'))*currValue:jQuery(this).attr('data-basetotalprice'), 
							}
							if(currPriceAvailabilityType==2){
								var currTr = jQuery("#bfi-timeperiod-"+currPriceId);
								currExtraService.TimeMinStart = currTr.attr("data-timeminstart");
								currExtraService.TimeMinEnd = currTr.attr("data-timeminend");
								currExtraService.CheckInTime = currTr.attr("data-checkintime");
								currExtraService.TimeDuration = currTr.attr("data-duration");
							}
							if(currPriceAvailabilityType==3){
								var currTr = jQuery("#bfi-timeslot-"+currPriceId);
								currExtraService.TimeSlotId = currTr.attr("data-timeslotid");
								currExtraService.TimeSlotStart = currTr.attr("data-timeslotstart");
								currExtraService.TimeSlotEnd = currTr.attr("data-timeslotend");
								var currDateint =  currTr.attr("data-checkin");
								currExtraService.TimeSlotDate = currDateint.substr(6,2) + "/" + currDateint.substr(4,2) + "/" + currDateint.substr(0,4);
							}

							currResourceRequest.ExtraServices.push(currExtraService);

//							currResourceRequest.ExtraServices.push({
//								Value:extraValue,
//								PriceId: currPriceId,
//								CalculatedQt: currValue,
//								ResourceId: currResId, 
//								TotalDiscounted:  jQuery(this).attr('data-baseprice'),
//								TotalAmount:  jQuery(this).attr('data-basetotalprice'),
//							});
						}
					});

					Order.Resources.push(currResourceRequest);

				}

			}
		});

        if (Order.Resources.length > 0) {
			FirstResourceId = Order.Resources[0].ResourceId;
//			Order.SearchModel = jQuery('#calculatorForm').serializeObject();
//			Order.SearchModel.MerchantId = <?php echo $merchant->MerchantId ?>;
//			Order.SearchModel.AdultCount = Order.SearchModel.adults;
//			Order.SearchModel.ChildrenCount = Order.SearchModel.children;
//			Order.SearchModel.SeniorCount = Order.SearchModel.seniors;
//			Order.SearchModel.ChildAges = [Order.SearchModel.childages1,Order.SearchModel.childages2,Order.SearchModel.childages3,Order.SearchModel.childages4,Order.SearchModel.childages5];

			jQuery('#frm-order').html('');
			
//			if (CartMultimerchantEnabled && jQuery('.bookingfor-shopping-cart').length )
			if(sendtocart==1)
			{
				jQuery('#frm-order').prepend('<input id=\"hdnOrderDataCart\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order.Resources) + "'" + '\>');
				jQuery('#frm-order').prepend('<input id=\"hdnBookingType\" name=\"hdnBookingType\" type=\"hidden\" value=' + "'" + jQuery('input[name="bookingType"]').val() + "'" + '\>');
				bookingfor.addToCart(jQuery("#divcalculator"));
			}else{
				jQuery('#frm-order').prepend('<input id=\"hdnOrderData\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order.Resources) + "'" + '\>');
				jQuery('#frm-order').prepend('<input id=\"hdnPolicyIds\" name=\"hdnPolicyIds\" type=\"hidden\" value=' + "'" + currPolicy.join(",") + "'" + '\>');
				bookingfor.waitBlockUI();
				jQuery('#frm-order').submit();
			}
		}else{
			alert("Error, You must select a quantity!")
		}

            //jQuery('#frm-order > #hdnOrderData').remove();
}


var bfisrv = [];
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
var listServiceIds = "<?php echo implode(",", $allServiceIds) ?>";
var bfisrvloaded=false;
var resGrp = [];
var loadedResGrp=false;

function getAjaxInformationsResGrp(){
	if (!loadedResGrp)
	{
		loadedResGrp=true;
		var queryMG = "task=getResourceGroups";
		jQuery.post(urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(JSON.parse(data) || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name);
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
			jQuery.post(urlCheck, querySrv, function(data) {
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
}

jQuery(document).ready(function () {
	getAjaxInformationsSrv();
	getAjaxInformationsResGrp();
});

//-->
</script>
</div>