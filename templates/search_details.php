<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( "DONOTCACHEPAGE", true ); // Do not cache this page

$currentCartConfiguration = null;
$cartLocked = false;
$resetCart = 0;

$ProductAvailabilityType = 1;
$checkInDates = '';


$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;

$fromSearch =  BFCHelper::getVar('fromsearch','0');
$listNameAnalytics =  BFCHelper::getVar('lna','0');
if(empty( $listNameAnalytics )){
	$listNameAnalytics = 0;
}

$currLlistNameAnalytics = BFCHelper::$listNameAnalytics[$listNameAnalytics];

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
$formMethod = "POST";

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

$startDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDateByMerchantId($merchant->MerchantId),new DateTimeZone('UTC'));
$endDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getEndDateByMerchantId($merchant->MerchantId),new DateTimeZone('UTC'));
$startDate->setTime(0,0,0);
$endDate->setTime(0,0,0);
$aCheckInDates = array();

if(!empty($resourceId)){
	$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$ProductAvailabilityType = $resource->AvailabilityType;
	$checkInDates = BFCHelper::getCheckInDates($resource->ResourceId,$startDate);
		
	if(!empty( $checkInDates )){
		$aCheckInDates = explode(',',$checkInDates);
		$startDate=DateTime::createFromFormat('Ymd',$aCheckInDates[0],new DateTimeZone('UTC'));
		$startDate->setTime(0,0,0);
	}	
	$currUriresource  = $uri.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);
	$formRoute = $currUriresource .'/?task=getMerchantResources';
}else{
	$formRoute = $routeMerchant .'/?task=getMerchantResources';
}

//if($usessl){
//	$formRouteBook = str_replace( 'http:', 'https:', $formRouteBook );
//}

//$formOrderRouteBook = $formRouteBook ;

if($usessl){
	$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
}

$formOrderRouteBook = $url_cart_page;

$pars = BFCHelper::getSearchParamsSession();
if(!is_array($pars)){
	$pars = array();
}

$pars['extras'] = '';
/*--------------------------------------------*/

$eecstays = array();

$productCategory = BFCHelper::GetProductCategoryForSearch($language,1,$merchant->MerchantId); 

$refreshState = isset($pars['refreshcalc']);

$checkoutspan = '+1 day';
if ($ProductAvailabilityType== 0)
{
	$checkoutspan = '+0 day';
}



$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');

$paxes = 2;
$paxages = array();
$currentState ='';

$ratePlanId = '';
$pricetype = '';
$selectablePrices ='';
$packages ='';
$nrooms = 1;


if (!empty($pars)){

	$checkin = !empty($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
	$checkout = !empty($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');

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


if(empty( $condominiumId )){
	$condominiumId = BFCHelper::getVar('condominiumId',0);
}

$variationPlanId = (isset($currvariationPlanId))?$currvariationPlanId:BFCHelper::getVar('variationPlanId','');
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
		$dateparsedend = BFCHelper::parseJsonDate($offer->LastAvailableDate, 'Y-m-d');
		
		$startDate = DateTime::createFromFormat('Y-m-d',$dateparsed,new DateTimeZone('UTC'));
		$endDate = DateTime::createFromFormat('Y-m-d',$dateparsedend,new DateTimeZone('UTC'));
		$endDate2 = clone $endDate;
//		$endDate2->modify('+'.$offer->MaxDuration.' day'); 
		
		if(empty(BFCHelper::getVar('refreshcalc',''))){
			$checkin = DateTime::createFromFormat('Y-m-d',$dateparsed,new DateTimeZone('UTC'));
			$checkout = clone $checkin;
			$checkout->modify($checkoutspan); 
		}
	}
}

$startDate2 = clone $startDate;
$startDate2->modify($checkoutspan);

if (($checkin < $startDate) || (!empty( $aCheckInDates ) && !in_array($checkin->format('Ymd'),$aCheckInDates)) ){
	$checkin = clone $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

$checkout->setTime(0,0,0);
$checkin->setTime(0,0,0);

if(!empty(BFCHelper::getVar('refreshcalc',''))){
	BFCHelper::setSearchParamsSession($pars);
}
//if ($checkin == $checkout){
//    $checkout->modify($checkoutspan); 
//}
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
	$currAvailCalHour = json_decode(BFCHelper::GetCheckInDatesPerTimes($resourceId, $checkin, null));
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


$dateStayCheckin = new DateTime('UTC');
$dateStayCheckout = new DateTime('UTC');


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


$allRatePlans = array();
if(!empty($fromSearch)){
	$allRatePlans = BFCHelper::GetRelatedResourceStays($merchant->MerchantId, $resourceId, $resourceId, $checkin,$duration,$paxages, $variationPlanId,$language, $condominiumId);
}

if(!empty($resourceId) && is_array($allRatePlans) && count($allRatePlans)>0){
	$defaultRatePlans =  array_values(array_filter($allRatePlans, function($p) use ($resourceId) {return $p->ResourceId == $resourceId ;})); // c#: allRatePlans.Where(p => p.ResourceId == resId);
	usort($defaultRatePlans, "BFCHelper::bfi_sortResourcesRatePlans");
	
	//$allRatePlans = array_slice($allRatePlans, 0, 5);
	$allRatePlans = array_merge($allRatePlans, $defaultRatePlans);
	$allRatePlans = array_unique($allRatePlans, SORT_REGULAR);
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

//$btnSearchclass=" bfi-not-active"; 
//if(empty($fromSearch)){
//	$btnSearchclass=""; 
//}

$btnSearchclass=""; 

$listDayTS = array();
$currentCartsItems = BFCHelper::getSession('totalItems', 0, 'bfi-cart');

?>

<div id="calculator" class="ajaxReload">

<script type="text/javascript">
    var daysToEnable = [<?php echo $checkInDates?>];
    var unitId = '<?php echo $resourceId ?>';
    var checkOutDaysToEnable = [];
    var bfi_MaxQtSelectable = <?php echo COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE ?>;
	var localeSetting = "<?php echo substr($language,0,2); ?>";
	
</script>
<br />
<!-- form fields -->
<h4 class="bfi-titleform"><?php _e('Availability', 'bfi') ?>
	<div class="bfi-pull-right"><a href="<?php echo $url_cart_page ?>" class="bfi-shopping-cart"><i class="fa fa-shopping-cart "></i> <span class="bfibadge" style="<?php echo (COM_BOOKINGFORCONNECTOR_SHOWBADGE) ?"":"display:none"; ?>"><?php echo ($currentCartsItems>0) ?$currentCartsItems:"";?></span><?php _e('Cart', 'bfi') ?></a></div>
	<div class="bfi-hide bfimodalcart">
		<div class="bfi-title"><?php _e('Cart', 'bfi') ?></div>
		<div class="bfi-body"><!-- <?php _e('Add to cart', 'bfi') ?> --></div>
		<div class="bfi-footer">
			<span class="bfi-btn bfi-alternative" onclick="jQuery('.bfi-shopping-cart').webuiPopover('destroy');"><?php _e('Continue shopping', 'bfi') ?></span>
			<span onclick="javascript:window.location.assign('<?php echo $url_cart_page ?>')" class="bfi-btn">Checkout</span>
		</div>
	</div><!-- /.modal -->
<div class="bfi-clearfix"></div>
</h4>
<?php 
if(!empty($variationPlanId)){
//	$uri  = 'index.php?option=com_bookingforconnector&view=search';
//	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//	$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//	if ($itemId<>0){
//		$uri = 'index.php?Itemid='.$itemId ;
//	}
//	$formRoute = JRoute::_($uri);
	$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
	$formRoute = get_permalink( $searchAvailability_page->ID );
	$formMethod = "GET";
}
?>
<form id="bfi-calculatorForm" action="<?php echo $formRoute?>" method="<?php echo $formMethod?>" class="bfi_resource-calculatorForm bfi_resource-calculatorTable ">
	<div class="bfi-row bfi_resource-calculatorForm-mandatory nopadding">
			<div class="bfi-row nopadding">
				<div class="bfi-col-md-7">
					<div class="bfi-row nopadding">
						<div class="bfi-col-md-6 bfi-col-xs-6" id="calcheckin">      

							<label><?php echo _e('Check-in','bfi') ?></label>
							<div class="bfi-datepicker">
							<input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" readonly="readonly" />
							</div>
						</div>
						<div class="bfi-col-md-6 bfi-col-xs-6 <?php echo ($ProductAvailabilityType == 3 || $ProductAvailabilityType == 2)? "bfi-hide " : " "  ?>" id="calcheckout">
							<label><?php echo _e('Check-out ','bfi') ?></label>
							<div class="bfi-datepicker">
							<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" readonly="readonly"/>
							</div>
							<div class="<?php echo ($ProductAvailabilityType == 3 || $ProductAvailabilityType == 2 || empty($resourceId))? "bfi-hide " : " "  ?>">
								&nbsp;(<span class="calendarnight" id="durationdays"><?php echo $duration ?></span> <span class="calendarnightlabel"><?php echo $ProductAvailabilityType == 1 ? __('Nights' , 'bfi' ) : __('Days' , 'bfi' )  ?></span>)
							</div>
						</div>
					</div>
				</div>
				<div class="bfi-col-md-5">
					<div class="bfi-row nopadding">
						<div class="bfi-col-md-9 bfi-col-xs-8 ">
				<div class="bfi-row">
					<div class="bfi-col-md-4 bfi-col-xs-4 bfi_resource-calculatorForm-adult">
						<label><?php echo _e('Adults ','bfi') ?>:</label><br />
						<select id="adultscalculator" name="adultssel" onchange="quoteCalculatorChanged();" class="">
							<?php
							foreach (range(1, 10) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nad == $number)?"selected":""; ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
					</div>
					<div class="bfi-col-md-4 bfi-col-xs-4 bfi_resource-calculatorForm-senior" >
						<label><?php echo _e('Seniors ','bfi') ?>:</label><br />
						<select id="seniorescalculator" name="senioressel" onchange="quoteCalculatorChanged();" class="">
							<?php
							foreach (range(0, 10) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nse == $number)?"selected":""; ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
					</div>
					<div class="bfi-col-md-4 bfi-col-xs-4 bfi_resource-calculatorForm-children">
						<label><?php echo  _e('Children','bfi') ?>:</label><br />
						<select id="childrencalculator" name="childrensel" onchange="quoteCalculatorChanged();" class="">
							<?php
							foreach (range(0, 4) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nch == $number)?"selected":""; ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="bfi_resource-calculatorForm-childrenages" style="display:none;">
					<span class="fieldLabel" style="display:inline"><?php  echo _e('Ages of children','bfi')  ?>:</span>
					<span class="fieldLabel" style="display:inline" id="bfi_lblchildrenagesatcalculator"><?php echo _e('on', 'bfi') . " " .$checkout->format("d"). " " . date_i18n('M',$checkout->getTimestamp()) . " " . $checkout->format("Y") ?></span><br />
					<select id="childages1" name="childages1sel" onchange="quoteCalculatorChanged();" class="bfi-inputmini" style="display: none;">
						<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
						<?php
						foreach (range(0, $maxchildrenAge) as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($nchs[0] != null && $nchs[0] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
						}
						?>
					</select>
					<select id="childages2" name="childages2sel" onchange="quoteCalculatorChanged();" class="bfi-inputmini" style="display: none;">
						<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
						<?php
						foreach (range(0, $maxchildrenAge) as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($nchs[1] != null && $nchs[1] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
						}
						?>
					</select>
					<select id="childages3" name="childages3sel" onchange="quoteCalculatorChanged();" class="bfi-inputmini" style="display: none;">
						<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
						<?php
						foreach (range(0, $maxchildrenAge) as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($nchs[2] != null && $nchs[2] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
						}
						?>
					</select>
					<select id="childages4" name="childages4sel" onchange="quoteCalculatorChanged();" class="bfi-inputmini" style="display: none;">
						<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
						<?php
						foreach (range(0, $maxchildrenAge) as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($nchs[3] != null && $nchs[3] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
						}
						?>
					</select>
					<select id="childages5" name="childages5sel" onchange="quoteCalculatorChanged();" class="bfi-inputmini" style="display: none;">
						<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
						<?php
						foreach (range(0, $maxchildrenAge) as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($nchs[4] != null && $nchs[4] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
						}
						?>
					</select>
				</div> 
							<span class="bfi-childmessage" id="bfi_lblchildrenagescalculator">&nbsp;</span>
						</div>
						<div class="bfi-col-md-3 bfi-col-xs-4 ">
							<a href="javascript:calculateQuote()"id="calculateButton" class="calculateButton3 bfi-btn <?php echo $btnSearchclass ?>" ><?php echo _e('Search','bfi') ?> </a>
						</div>
					</div>
				</div>
			</div>
			<div id="bfishowpersoncalculator" style="display:none;" >
							<div class="fieldLabel"><?php _e('Guest', 'bfi'); ?></div>
							<div class="bfi-showperson-text-calculator bfi-container">
								<span id="bfi-room-info-calculator" class="bfi-comma bfi-hide"><span><?php echo $nrooms ?></span> <?php _e('Resource', 'bfi'); ?></span>
								<span id="bfi-adult-info-calculator" class="bfi-comma"><span><?php echo $nad ?></span> <?php _e('Adults', 'bfi'); ?></span>
								<span id="bfi-senior-info-calculator" class="bfi-comma"><span><?php echo $nse ?></span> <?php _e('Seniores', 'bfi'); ?></span>
								<span id="bfi-child-info-calculator" class="bfi-comma"><span><?php echo $nch ?></span> <?php _e('Children', 'bfi'); ?></span>
							</div>
			</div>

	</div>	<!-- END bfi_resource-calculatorForm-mandatory -->
	<input name="onlystay" type="hidden" value="1" />
	<input name="newsearch" type="hidden" value="1" />
	<input name="groupresulttype" type="hidden" value="0" />
	<input name="calculate" type="hidden" value="true" />
	<input name="resourceId" type="hidden" value="<?php echo $resourceId?>" />
	
	<input type="hidden" name="persons" value="<?php echo $nad + $nse + $nch?>" id="searchformpersons-calculator" />
	<input type="hidden" name="adults" value="<?php echo $nad?>" id="searchformpersonsadult-calculator">
	<input type="hidden" name="seniores" value="<?php echo $nse?>" id="searchformpersonssenior-calculator">
	<input type="hidden" name="children" value="<?php echo $nch?>" id="searchformpersonschild-calculator">
	<input type="hidden" name="childages1" value="<?php echo $nchs[0]?>" id="searchformpersonschild1-calculator">
	<input type="hidden" name="childages2" value="<?php echo $nchs[1]?>" id="searchformpersonschild2-calculator">
	<input type="hidden" name="childages3" value="<?php echo $nchs[2]?>" id="searchformpersonschild3-calculator">
	<input type="hidden" name="childages4" value="<?php echo $nchs[3]?>" id="searchformpersonschild4-calculator">
	<input type="hidden" name="childages5" value="<?php echo $nchs[4]?>" id="searchformpersonschild5-calculator">

	<input name="pricetype" type="hidden" value="<?php echo $selPriceType ?>" />
	<input name="bookingType" type="hidden" value="<?php echo $selBookingType ?>" />
	<input name="variationPlanId" type="hidden" value="<?php echo $variationPlanId ?>" />
	<input name="condominiumId" type="hidden" value="<?php echo $condominiumId ?>" />
	<input name="state" type="hidden" value="<?php echo $currentState ?>" />
	<input name="extras[]" type="hidden" value="<?php echo $selectablePrices ?>" />
	<input name="refreshcalc" type="hidden" value="1" />
	<input name="fromsearch" type="hidden" value="1" />
	<input name="lna" type="hidden" value="<?php echo $listNameAnalytics ?>" />

	<input name="availabilitytype" id="productAvailabilityType" type="hidden" value="<?php echo $ProductAvailabilityType?>" />
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
	$totalResCount = count((array)$allRatePlans);	


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
						<input id="bfimodaltimeperiodcheckin" type="hidden" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimePeriod" hidden = "hidden"   readonly="readonly" />
					</div>
				</div>	
				<div class="bfi-simplerow">
					<select class="bfi_input_select selectpickerTimePeriodStart" id="selectpickerTimePeriodStart" data-rateplanid="0"></select>
				</div>	
				<div class="bfi-simplerow">
					<select class="bfi_input_select selectpickerTimePeriodEnd" id="selectpickerTimePeriodEnd" data-rateplanid="0"></select>
				</div>	
				<div class="bfi-simplerow bfi-text-center">
					<a id="bfi-timeperiod-select" class="bfi-btn" onclick="bfi_selecttimeperiod(this)" data-rateplanid="0" data-resid="0"><?php _e('Select', 'bfi') ?></a>
				</div>	
		</div>
</div><!-- /.modal -->

<div class="bfi-hide">
		<div class="bfi-timeslot-change" id="bfimodaltimeslot">
				<div class="bfi-simplerow">
					<div class="bfi-buttoncalendar check-availibility-date bfi-text-center">
						<input id="bfimodaltimeslotcheckin" type="hidden" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimeSlot" hidden = "hidden"   readonly="readonly" />
					</div>
				</div>	
				<div class="bfi-simplerow ">
					<select class="bfi_input_select selectpickerTimeSlotRange" id="selectpickerTimeSlotRange" data-rateplanid="0"></select>
				</div>	
				<div class="bfi-simplerow bfi-text-center">
					<a id="bfi-timeslot-select" class="bfi-btn" onclick="bfi_selecttimeslot(this)" data-rateplanid="0" data-resid="0" data-sourceid="0"><?php _e('Select', 'bfi') ?></a>
				</div>	
		</div>
</div><!-- /.modal -->
<?php 
if(!empty($fromSearch) && empty($allResourceId) && empty($resourceId)){
?>

					<div class="errorbooking" id="errorbooking">
						<strong><?php _e('No results available for the submitted data', 'bfi') ?></strong>
						<!-- No disponibile -->
					</div>
<?php 
$showResult= " bfi-hide";
					}
?>


<div class="bfi-result-list <?php echo $showResult ?> bfi-table-responsive">
<script>
    var pricesExtraIncluded=[];
</script>
		<table class="bfi-table bfi-table-bordered bfi-table-resources bfi-table-resources-sticked" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php _e('Information', 'bfi') ?></th>
					<th><div><!-- <?php _e('For', 'bfi') ?> --></div></th>
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
					<?php if ($cartLocked)//////// && ($currentCartConfiguration as List<CartOrder>).Any(t => t.Resources.Any(r => r.MerchantId != Model.MerchantId)))
					{ ?>
						<?php _e("You can't acquire resources from another merchant.", 'bfi') ?>
	<div><a href="<?php echo $url_cart_page ?>"><?php _e("My Cart", 'bfi') ?></a></div>
					
					<?php } else{ ?>
							<div class="bfi-book-now">
								<div class="bfi-resource-total"><span></span> <?php _e('selected items', 'bfi') ?></div>
								<div class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?>" style="display:none;"></div>
								<div class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>" ></div>
								<div id="btnBookNow" class="bfi-btn bfi-btn-book-now" data-formroute="<?php// echo $formRouteBook ?>" onclick="ChangeVariation(this);">
									<?php _e('Book Now', 'bfi') ?>
								</div>
								<div class="bfi-btn bfi-alternative bfi-request-now" onclick="ChangeVariation(this);">
									<?php _e('Request Now', 'bfi') ?>
								</div>

							</div>
					<?php } ?>
				</td>
			</tr>

			<?php  if(!empty($resourceId) && !in_array($resourceId,$allResourceId)) {
				$currUriresource = $uri.$resourceId. '-' . BFCHelper::getSlug($resource->Name) . "?fromsearch=1&lna=".$listNameAnalytics;
				$resourceNameTrack =  BFCHelper::string_sanitize($resource->Name);
				$merchantNameTrack =  BFCHelper::string_sanitize($merchant->Name);
				$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MainCategoryName);

			?>
			<tr>
				<td class="bfi-firstcol bfi-firstcol-selected">
					<a class="bfi-resname eectrack" onclick="bfiGoToTop()" href="<?php echo $currUriresource ?>" data-type="Resource" data-id="<?php echo $resource->ResourceId?>" data-index="0" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $resource->Name; ?></a>
<div class="bfi-clearfix"></div>
<?php 
			if(!empty($resource->ImageUrl)){
				$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'small');
?>
<a class="bfi-link-searchdetails" onclick="bfiGoToTop()" href="<?php echo $currUriresource ?>"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-searchdetails" /></a>
<div class="bfi-clearfix"></div>
<?php 
			}
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
				<?php if (isset($resource->MaxCapacityPaxes) && $resource->MaxCapacityPaxes>0):?>
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
						<?php if(isset($resource->MaxCapacityPaxes) && $resource->MaxCapacityPaxes > 0 && ( $totalPerson > $resource->MaxCapacityPaxes || $totalPerson < $resource->MinCapacityPaxes )) :?><!-- Errore persone-->
							<br /><?php echo sprintf(__('Persons min:%1$d max:%2$d', 'bfi'), $resource->MinCapacityPaxes, $resource->MaxCapacityPaxes) ?>
						<?php endif;?>
					</div>
				</td>
			</tr>
			<?php if (!empty($allResourceId)) { ?>
				<tr><td colspan="5" class="bfi-otherresults-box"><div class="bfi-otherresults"><?php echo sprintf(__('Other %1$d choise', 'bfi'), $totalResCount) ?></div> <?php _e('Find other great offers!', 'bfi') ?></td></tr>
			<?php } ?>

		<?php } ?>
<?php

$allSelectablePrices = array();
$allTimeSlotResourceId = array();
$allTimePeriodResourceId = array();
$reskey = -1;

foreach($allResourceId as $resId) {

	$reskey += 1;
	$currKey = $reskey;
	if(!empty($resourceId) && !in_array($resourceId,$allResourceId)) {
		$currKey += 1;
	}
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
	$currUriresource = $uri.$res->ResourceId . '-' . BFCHelper::getSlug($res->ResName) . "?fromsearch=1&lna=".$listNameAnalytics;

	$formRouteSingle = $currUriresource;

	$resourceNameTrack =  BFCHelper::string_sanitize($res->ResName);
	$merchantNameTrack =  BFCHelper::string_sanitize($merchant->Name);
	$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MainCategoryName);

	$eecstay = new stdClass;
	$eecstay->id = "" . $res->ResourceId . " - Resource";
	$eecstay->name = "" . $resourceNameTrack;
	$eecstay->category = $merchantCategoryNameTrack;
	$eecstay->brand = $merchantCategoryNameTrack;
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
	$btnClass = "bfi-alternative";
	if ($IsBookable){
		$btnText = __('Book Now', 'bfi');
		$btnClass = "";
	}
	$formRouteBook = "";
	$nRowSpan = 1+count($resRateplans);
?>
			<tr >
				<td rowspan="<?php echo $nRowSpan ?>" class="bfi-firstcol <?php echo ($resId == $resourceId)? '  bfi-firstcol-selected' :  '' ; ?>">
					<a  class="bfi-resname eectrack" href="<?php echo $formRouteSingle ?>" <?php echo ($resId == $resourceId)? 'onclick="bfiGoToTop()"' :  'target="_blank"' ; ?> data-type="Resource" data-id="<?php echo $res->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $res->ResName; ?></a>
<div class="bfi-clearfix"></div>
<?php 
			if(!empty($res->ImageUrl)){
				$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$res->ImageUrl, 'small');
?>
<a  class="bfi-link-searchdetails" href="<?php echo $formRouteSingle ?>" <?php echo ($resId == $resourceId)? 'onclick="bfiGoToTop()"' :  'target="_blank"' ; ?> ><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-searchdetails" /></a>
<div class="bfi-clearfix"></div>
								<?php
			}
/*-----------scelta date e ore--------------------*/	

									if (($res->AvailabilityType == 0 || $res->AvailabilityType == 1) && $res->Availability < 2)
									{
										?>
									  <span class="bfi-availability-low"><?php echo sprintf(__('Only %d available' , 'bfi'),$res->Availability) ?></span>
									<?php 
									}

									if ($res->AvailabilityType == 2)
									{
										
										$currCheckIn = BFCHelper::parseJsonDateTime($res->RatePlan->CheckIn,'d/m/Y - H:i');
										$currCheckOut =BFCHelper::parseJsonDateTime($res->RatePlan->CheckOut,'d/m/Y - H:i');
										$currDiff = $currCheckOut->diff($currCheckIn);

										$loadScriptTimePeriod = true;

										$timeDurationview = $currDiff->h + round(($currDiff->i/60), 2);
										$timeDuration = abs((new DateTime('UTC'))->setTimeStamp(0)->add($currDiff)->getTimeStamp() / 60); 										

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
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDurationview  ?></span> <?php _e('hours', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
/*-------------------------------*/	
									if ($res->AvailabilityType == 3)
									{
										$loadScriptTimeSlot = true;
										$currDatesTimeSlot = array();
										
										if(!array_key_exists($resId, $allTimeSlotResourceId)){
											array_push($allTimeSlotResourceId, $res->ResourceId);
										}
										
										if(!array_key_exists($resId, $listDayTS)){
											$currDatesTimeSlot =  json_decode(BFCHelper::GetCheckInDatesTimeSlot($resId,$alternativeDateToSearch));
											$listDayTS[$resId] = $currDatesTimeSlot;
										}else{
											$currDatesTimeSlot =  $listDayTS[$resId];
										}

										$currCheckIn = DateTime::createFromFormat('Ymd', $currDatesTimeSlot[0]->StartDate,new DateTimeZone('UTC'));
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

//Calcolo

	foreach($resRateplans as $rpKey => $currRateplan) {
		
		$currSelectablePrices = json_decode($currRateplan->RatePlan->CalculablePricesString);
		$currSelectablePricesExtra = array_filter($currSelectablePrices, function($currSelectablePrice) {
			return $currSelectablePrice->Tag == "extrarequested";
		});
		$currSelectablePricesExtraIds= array_filter(array_map(function ($currSelectablePrice) { 
				if($currSelectablePrice->Tag == "extrarequested"){
					return $currSelectablePrice->PriceId; 
				}
			}, $currSelectablePricesExtra));
		
		$currCalculatedPrices = json_decode($currRateplan->RatePlan->CalculatedPricesString);
		$currCalculatedPricesExtra = array_filter($currCalculatedPrices, function($currCalculatedPrice) use ($currSelectablePricesExtraIds) {
			if(!in_array( $currCalculatedPrice->RelatedProductId,$currSelectablePricesExtraIds) && $currCalculatedPrice->Tag == "extrarequested"){
				return true;
			}
		});
			
		if(count($currSelectablePrices)>0){
			$formRouteBook = "showSelectablePrices"; 
		}
		$availability = array();
		$startAvailability = 0;
		$selectedtAvailability = 0;
		for ($i = $startAvailability; $i <= min($res->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE); $i++)
		{
			array_push($availability, $i);
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
				<?php if ($currRateplan->MaxPaxes>0){?>
					<?php 
					if(!empty( $currRateplan->RatePlan->SuggestedStay ) && !empty( $currRateplan->RatePlan->SuggestedStay->ComputedPaxes )){
						$computedPaxes = explode("|", $currRateplan->RatePlan->SuggestedStay->ComputedPaxes);
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
						<?php if ($currRateplan->MaxPaxes>0){?>
						<div class="bfi-icon-paxes">
							<i class="fa fa-user"></i> 
							<?php if ($currRateplan->MaxPaxes==2 && $currRateplan->MinPaxes==2){?>
							<i class="fa fa-user"></i> 
							<?php }?>
							<?php if ($currRateplan->MaxPaxes>2){?>
								<?php echo ($currRateplan->MinPaxes != $currRateplan->MaxPaxes)? $currRateplan->MinPaxes . "-" : "" ?><?php echo  $currRateplan->MaxPaxes ?>
							<?php }?>
						</div>
						<?php }?>
					<?php } ?>
				<?php } ?>
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
					$currDatePolicy = DateTime::createFromFormat('Y-m-d',$currDatePolicyparsed,new DateTimeZone('UTC'));
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
if($currRateplan->ItemTypeId==1){
	$currRateplan->RatePlan->IncludedMeals = -1;
}
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
<?php
				$currratePlanName =  BFCHelper::string_sanitize($currRateplan->RatePlan->Name);

?>
					<select class="ddlrooms ddlrooms-<?php echo $currRateplan->ResourceId ?> ddlrooms-indipendent" 
					id="ddlrooms-<?php echo $currRateplan->ResourceId ?>-<?php echo $currRateplan->RatePlan->RatePlanId ?>" 
					onclick="bookingfor.checkMaxSelect(this);" 
					onchange="bookingfor.checkBookable(this);UpdateQuote();" 
					data-resid="<?php echo $currRateplan->ResourceId ?>" 
					data-name="<?php echo $resourceNameTrack ?>"
					data-lna="<?php echo $currLlistNameAnalytics ?>"
					data-brand="<?php echo $merchantNameTrack ?>"
					data-category="<?php echo $merchantCategoryNameTrack ?>"
					data-sourceid="<?php echo $currRateplan->ResourceId ?>"
					data-ratePlanId="<?php echo $currRateplan->RatePlan->RatePlanId ?>"
					data-ratePlanTypeId="<?php echo $currRateplan->RatePlan->RatePlanTypeId ?>"
					data-ratePlanName="<?php echo $currratePlanName ?>"
					data-policyId="<?php echo $policyId ?>"
					data-policy=<?php echo json_encode($policy) ?>
					data-price="<?php echo BFCHelper::priceFormat($currRateplan->Price,2,".","") ?>" 
					data-totalprice="<?php echo BFCHelper::priceFormat($currRateplan->TotalPrice,2,".","") ?>" 
					data-baseprice="<?php echo $currRateplan->Price ?>" 
					data-basetotalprice="<?php echo $currRateplan->TotalPrice ?>"
					data-allvariations='<?php echo  str_replace("&", "e",  str_replace("'", "", $currRateplan->RatePlan->AllVariationsString)) ?>'
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
					data-resetCart="<?php echo $resetCart ?>" 
					data-computedpaxes="<?php echo (!empty( $currRateplan->RatePlan->SuggestedStay ) && !empty( $currRateplan->RatePlan->SuggestedStay->ComputedPaxes ))?$currRateplan->RatePlan->SuggestedStay->ComputedPaxes:":::::::" ?>" 
					>
					<?php 
						foreach ($availability as $number) {
							?> <option value="<?php echo $number ?>" <?php echo ($selectedtAvailability== $number)?"selected":""; //selected( $selectedtAvailability, $number ); ?>><?php echo $number ?></option><?php
						}
					?>
					</select>
<script type="text/javascript">
<!--
					pricesExtraIncluded[<?php echo $currRateplan->RatePlan->RatePlanId ?>] =<?php echo json_encode((object)$currCalculatedPricesExtra) ?> ;	
//-->
</script>
				</td>
			</tr>

<?php 
}
?>
		<?php
		if (count($allResourceId) > 1 && (($resId == $resourceId && $resCount > 1) || ($resId == $resourceId && $resCount == 0))): ?>
				<tr><td colspan="5" class="bfi-otherresults-box"><div class="bfi-otherresults"><?php echo sprintf(__('Other %1$d choise', 'bfi'), (count($allResourceId)-1)) ?></div> <?php _e('Find other great offers!', 'bfi') ?></td></tr>
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

	<br /><?php  bfi_get_template("menu_small_booking.php");  ?>

<table class="bfi-table bfi-table-bordered bfi-table-resources bfi-table-selectableprice bfi-table-selectableprice-container bfi-table-resources-sticked" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php _e('Do you want add more?', 'bfi') ?></th>
					<th><div><?php _e('Confirm your reservation', 'bfi') ?></div></th>
				</tr>
			</thead>
		<tr>
			<td class="bfi-nopad">
					

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

		$currUriresource = $uri.$currRateplan->ResourceId . '-' . BFCHelper::getSlug($currRateplan->ResName) . "?fromsearch=1&lna=".$listNameAnalytics;
		$merchantNameTrack =  BFCHelper::string_sanitize($merchant->Name);
		$resourceNameTrack =  BFCHelper::string_sanitize($currRateplan->ResName);
				$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MainCategoryName);
?>
					

		<div id="services-room-1-<?php echo $currRateplan->ResourceId ?>-<?php echo $currRateplan->RatePlan->RatePlanId ?>" class="bfi-table-responsive" style="display:none;">
		<div class="bfi-resname-extra"><a  class="bfi-resname eectrack" href="<?php echo $currUriresource ?>" target="_blank" data-type="Resource" data-id="<?php echo $currRateplan->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $currRateplan->ResName; ?></a></div>
		<div class="bfi-clearfix"></div>
		<?php  if(!empty($currRateplan->ImageUrl)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$currRateplan->ImageUrl, 'small');
		?>
		<a  class="bfi-link-searchdetails" href="<?php echo $currUriresource ?>" target="_blank"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-searchdetails" /></a>
		<div class="bfi-clearfix"></div>
		<?php } ?>
		<!-- bfi-table-selectableprice -->
		<table class="bfi-table bfi-table-bordered bfi-table-resources bfi-table-selectableprice" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php _e('Information', 'bfi') ?></th>
					<th><div><!-- <?php _e('For', 'bfi') ?> --></div></th>
					<th ><div><?php _e('Price', 'bfi') ?></div></th>
					<th><div><?php _e('Options', 'bfi') ?></div></th>
					<th><div><?php _e('Qt.', 'bfi') ?></div></th>
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
										$currCheckIn = DateTime::createFromFormat('Y-m-d\TH:i:s',$selPrice->CheckIn,new DateTimeZone('UTC'));
										$currCheckOut = DateTime::createFromFormat('Y-m-d\TH:i:s',$selPrice->CheckOut,new DateTimeZone('UTC'));
										$currDiff = $currCheckOut->diff($currCheckIn);

   
										$loadScriptTimePeriod = true;
										
										$timeDurationview = $currDiff->h + round(($currDiff->i/60), 2);
										$timeDuration = abs((new DateTime('UTC'))->setTimeStamp(0)->add($currDiff)->getTimeStamp() / 60); 										

										array_push($allTimePeriodResourceId, $selPrice->RelatedProductId );
//										$currCheckInString = date_i18n('D',$currCheckIn->getTimestamp()) ." " . $currCheckIn->format("d") ." " . date_i18n('M',$currCheckIn->getTimestamp()).' '.$currCheckIn->format("Y");
//										$currCheckOutString = date_i18n('D',$currCheckOut->getTimestamp()) ." " . $currCheckOut->format("d") ." " . date_i18n('M',$currCheckOut->getTimestamp()).' '.$currCheckOut->format("Y");
//										$currCheckInHour = $currCheckIn->format('H:i');
//										$currCheckOutHour = $currCheckOut->format('H:i');
//										$currDiffString = $currDiff->format('%h') ;

//$currCheckInString = __('Select a period', 'bfi');
//$currCheckOutString = "";
//$currCheckInHour = "";
//$currCheckOutHour = "";
//$currDiffString = "-";
//
									?>
										<div class="bfi-timeperiod bfi-cursor" id="bfi-timeperiod-<?php echo $selPrice->RelatedProductId ?>" 
											data-resid="<?php echo $selPrice->RelatedProductId ?>" 
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
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDurationview  ?></span> <?php _e('hours', 'bfi') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
/*-------------------------------*/	
									if ($selPrice->AvailabilityType == 3)
									{
										$loadScriptTimeSlot = true;
										$currDatesTimeSlot = array();
										
										if(!array_key_exists($selPrice->RelatedProductId , $allTimeSlotResourceId)){
											array_push($allTimeSlotResourceId, $selPrice->RelatedProductId );
										}
										
										if(!array_key_exists($selPrice->RelatedProductId , $listDayTS)){
											$currDatesTimeSlot =  json_decode(BFCHelper::GetCheckInDatesTimeSlot($selPrice->RelatedProductId ,$alternativeDateToSearch));
											$listDayTS[$selPrice->RelatedProductId ] = $currDatesTimeSlot;
										}else{
											$currDatesTimeSlot =  $listDayTS[$selPrice->RelatedProductId ];
										}

										

//
//										array_push($allTimeSlotResourceId, $selPrice->RelatedProductId );
//										$currDatesTimeSlot =  json_decode(BFCHelper::GetCheckInDatesTimeSlot($selPrice->RelatedProductId ,$alternativeDateToSearch));

										$listDayTS[$selPrice->RelatedProductId] = $currDatesTimeSlot;

										$currCheckIn = DateTime::createFromFormat('Ymd', $currDatesTimeSlot[0]->StartDate,new DateTimeZone('UTC'));
										$currCheckOut = clone $currCheckIn;
										$currCheckIn->setTime(0,0,0);
										$currCheckOut->setTime(0,0,0);
										$currCheckIn->add(new DateInterval('PT' . $currDatesTimeSlot[0]->TimeSlotStart . 'M'));
										$currCheckOut->add(new DateInterval('PT' . $currDatesTimeSlot[0]->TimeSlotEnd . 'M'));

										$currDiff = $currCheckOut->diff($currCheckIn);

										// overrides Availability by CheckInDatesTimeSlot
										$res->Availability = $currDatesTimeSlot[0]->Availability ;

									?>
										<div class="bfi-timeslot bfi-cursor" data-sourceid="services-room-1-<?php echo $currRateplan->ResourceId ?>-<?php echo $currRateplan->RatePlan->RatePlanId ?>" data-resid="<?php echo $selPrice->RelatedProductId?>" data-checkin="<?php echo $currCheckIn->format('Ymd') ?>"
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
					<td>
						<!-- Min/Max -->
						<?php if (isset($selPrice->CalculationType) && !empty($selPrice->CalculationType)){?>
							<?php 
								if ($nad>0) {
									?>
									<div class="bfi-icon-paxes">
										<i class="fa fa-user"></i> x <b><?php echo $nad ?></b>
									<?php 
										if (($nse+$nch)>0) {
											?>
											+ <br />
												<span class="bfi-redux"><i class="fa fa-user"></i></span> x <b><?php echo ($nse+$nch) ?></b>
											<?php 
											
										}
									?>
									</div>
									<?php 
								}
							?>
						<?php } ?>
					</td>
					<td style="text-align:center;"><!-- price -->
						<?php
						$percentVariation = $selPrice->TotalAmount > 0 ? (int)((($selPrice->TotalDiscounted - $selPrice->TotalAmount) * 100) / $selPrice->TotalAmount) : 0;
						?>
						<div class="bfi-totalextrasselect" style="<?php echo ($selPrice->TotalDiscounted==0) ? "display:none;" : ""; ?>">
							<div align="center">
								<div class="bfi-percent-discount" style="<?php echo ($percentVariation < 0 ? " display:block" : "display:none"); ?>" rel="<?php echo $SimpleDiscountIds ?>" rel1="<?php echo $res->ResourceId ?>">
									<span class="bfi-percent"><?php echo $percentVariation ?></span>% <i class="fa fa-question-circle bfi-cursor-helper" aria-hidden="true"></i>
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
//			if ($selPrice->AvailabilityType == 2){
//				$availability = array(0);
//				$clickFunction ="bfi_updateQuoteService();";
//			}

				$extraNameTrack =  BFCHelper::string_sanitize($selPrice->Name);
				$currratePlanName =  BFCHelper::string_sanitize($currRateplan->RatePlan->Name);
?>

						<script>
							servicesAvailability[<?php echo $selPrice->PriceId ?>] =<?php echo (!empty($selPrice->Availability)? min($selPrice->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE) : 0) ?> ;
						</script>
						<select class="ddlrooms ddlrooms-<?php echo $selPrice->RelatedProductId?> ddlextras inputmini" 
							onchange="<?php echo $clickFunction ?>" 
							data-maxvalue="<?php echo $selPrice->MaxQt ?>" 
							data-minvalue="<?php echo $selPrice->MinQt ?>" 
							data-priceid="<?php echo $selPrice->PriceId ?>"
							data-name="<?php echo $extraNameTrack ?>"
							data-lna="<?php echo $currLlistNameAnalytics ?>"
							data-brand="<?php echo $merchantNameTrack ?>"
							data-category="<?php echo $merchantCategoryNameTrack ?>"
							data-resourcename="<?php echo $resourceNameTrack ?>"
							data-resid="<?php echo $selPrice->RelatedProductId ?>"
							data-sourceid="<?php echo $selPrice->RelatedProductId ?>"
							data-rateplanid="<?php echo $currRateplan->RatePlan->RatePlanId ?>" 
							data-rateplanname="<?php echo $currratePlanName?>" 
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
			</td>
			<td >
				<div class="totalextrasstay bfi-book-now" style="display:none;">
					<div class="bfi-resource-total"><span></span> <?php _e('selected items', 'bfi') ?></div>
					<div class="bfi-extras-total"><span></span> <?php _e('selected services', 'bfi') ?></div> 
					<div class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?>" style="display:none;"></div>
					<div class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>" ></div>
					<div class="bfi-btn bfi-btn-book-now" onclick="bookingfor.BookNow(this);">
						<?php _e('Book Now', 'bfi') ?>
					</div>
					<div class="bfi-btn bfi-alternative bfi-request-now" onclick="bookingfor.BookNow(this);">
						<?php _e('Request Now', 'bfi') ?>
					</div>
				</div>
			</td>
		</tr>
</table>
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
	
	function updateTitleBooking(classToAdd,classToRemove,title){
		jQuery("#ui-datepicker-div").addClass("notranslate");
		jQuery("#ui-datepicker-div").addClass(classToAdd);
		jQuery("#ui-datepicker-div").removeClass(classToRemove);

		jQuery("#ui-datepicker-div div.bfi-title-arrow").remove();
		jQuery("#ui-datepicker-div").prepend( "<div class=\"bfi-title-arrow\">"+title+"</div>" );

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
		<?php if(empty($resourceId)) { ?>
			strSummaryDays = "";
		<?php } ?>

		if (productAvailabilityType == 0 || productAvailabilityType == 1) {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
		}
		jQuery("#durationdays").html(days);

		jQuery('#ui-datepicker-div').attr('data-before',strSummary);
	
	}

	function bfi_printChangedDateBooking() {
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(d1[2], d1[1]-1, d1[0]);
		var to   = new Date(d2[2], d2[1]-1, d2[0]);

		day1  = ('0' + from.getDate()).slice(-2), 
			
		month1 = from.toLocaleString(localeSetting, { month: "long" }),              
		year1 =  from.getFullYear(),
		weekday1 = from.toLocaleString(localeSetting, { weekday: "long" });

		day2  = ('0' + to.getDate()).slice(-2),  
		month2 = to.toLocaleString(localeSetting, { month: "long" }),              
		year2 =  to.getFullYear(),
		weekday2 = to.toLocaleString(localeSetting, { weekday: "long" });

		var btnTextCheckin = "<span class='bfi-weekdayname'>"+weekday1+" </span>"+day1+" "+month1+"<span class='bfi-year'> "+year1+"</span>";
		var btnTextCheckout = "<span class='bfi-weekdayname'>"+weekday2+" </span>"+day2+" "+month2+"<span class='bfi-year'> "+year2+"</span>";
		var btnTextChildrenagesat = "<?php echo strtolower (__('the', 'bfi')) ?> " + day2 + " " + month2 + " " + year2;
		
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			btnTextCheckin = "<span class='bfi-weekdayname'>"+weekday1+" </span>"+day1+" "+month1+"<span class='bfi-year'> "+year1+"</span>";
			btnTextCheckout = "<span class='bfi-weekdayname'>"+weekday2+" </span>"+day2+" "+month2+"<span class='bfi-year'> "+year2+"</span>";
			btnTextChildrenagesat = "<?php echo strtolower (__('the', 'bfi')) ?> " + day2 + " " + month2 + " " + year2;
		} else {
			btnTextCheckin = "<span class='bfi-weekdayname'>"+weekday1+" </span>"+day1+"/"+d1[1]+"<span class='bfi-year'>/"+d1[2]+"</span>";
			btnTextCheckout = "<span class='bfi-weekdayname'>"+weekday2+" </span>"+day2+"/"+d2[1]+"<span class='bfi-year'>/"+d2[2]+"</span>";
			btnTextChildrenagesat = "<?php echo strtolower (__('the', 'bfi')) ?> " +  day2 + " " + d2[1] + " " + d2[2];
		}
		jQuery('.checkinBooking').html(btnTextCheckin);
		jQuery('.checkoutBooking').html(btnTextCheckout);
		jQuery('#bfi_lblchildrenagesatcalculator').html(btnTextChildrenagesat);
		
		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		if (productAvailabilityType == 0) {
			days += 1;
		}
		jQuery("#durationdays").html(days);

	}
	function insertCheckinTitleBooking() {
		setTimeout(function() {updateTitleBooking("bfi-checkin","bfi-checkout","Checkin")}, 1);
	}
	function insertCheckoutTitleBooking() {
		setTimeout(function() {updateTitleBooking("bfi-checkout","bfi-checkin","Checkout")}, 1);
	}
	var calculator_checkin = null;
	var calculator_checkout = null;

	jQuery(function($) {
		
		UpdateQuote();

		jQuery('.bfi-options-help i').webuiPopover({trigger:'hover',placement:'right-bottom',style:'bfi-webuipopover'});

		jQuery(".bfi-percent-discount").on("click", function (e) {
			e.preventDefault();
			var bfi_wuiP_width= 400;
			if(jQuery(window).width()<bfi_wuiP_width){
				bfi_wuiP_width = jQuery(window).width()*0.7;
			}
			var showdiscount = function (obj, text) {
								obj.find("i").first().switchClass("fa-spinner fa-spin","fa-question-circle")
								obj.webuiPopover({
									content : text,
									container: document.body,
									closeable:true,
									placement:'auto-bottom',
									dismissible:true,
									trigger:'manual',
									type:'html',
									width : bfi_wuiP_width,
									style:'bfi-webuipopover'
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
//					printChangedDateBooking(date, jQuery(this)); 
					bfi_printChangedDateBooking();
				}
			}
			, showOn: 'button'
			, beforeShowDay: function (date) {
				return closedBooking(date, 1, daysToEnable); 
				}
			, beforeShow: function(dateText, inst) {
				jQuery('#ui-datepicker-div').addClass('notranslate');  
				jQuery(inst.dpDiv).addClass('bfi-calendar');
				jQuery(this).attr("readonly", true); 
				insertCheckinTitleBooking(); 
				}
			, onChangeMonthYear: function(dateText, inst) { 
				insertCheckinTitleBooking(); 
				}
			, buttonText: "<div class='checkinBooking'><span class='bfi-weekdayname'><?php echo date_i18n('l',$checkin->getTimestamp());?> </span><?php echo $checkin->format("d") ;?> <?php echo date_i18n('F',$checkin->getTimestamp());?><span class='bfi-year'> <?php echo $checkin->format("Y"); ?></span></div>"

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
				//printChangedDateBooking(date, jQuery(this)); 
				bfi_printChangedDateBooking();
				}
			, showOn: 'button'
			, beforeShowDay: function (date) {
				return closedBooking(date, 0, checkOutDaysToEnable); 
				}
			, beforeShow: function(dateText, inst) {
				jQuery('#ui-datepicker-div').addClass('notranslate');  
				jQuery(inst.dpDiv).addClass('bfi-calendar');
				jQuery(this).attr("readonly", true); 
				insertCheckoutTitleBooking(); 
				}
			, onChangeMonthYear: function(dateText, inst) {
				insertCheckoutTitleBooking(); 
				}
			, buttonText: "<div class='checkoutBooking'><span class='bfi-weekdayname'><?php echo date_i18n('l',$checkout->getTimestamp());?> </span><?php echo $checkout->format("d") ;?> <?php echo date_i18n('F',$checkout->getTimestamp());?><span class='bfi-year'> <?php echo $checkout->format("Y"); ?></span></div>"
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

		var dayEnabled = false;
		var month = date.getMonth() + 1;
		var day = date.getDate();
		var year = date.getFullYear();
<?php if(!empty($resourceId)) { ?>
		var copyarray = jQuery.extend(true, [], enableDays);
		for (var i = 0; i < offset; i++)
			copyarray.pop();
		var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
		if (jQuery.inArray(Number(datereformat), copyarray) != -1) {
			dayEnabled = true;
			//return [true, 'greenDay'];
		}
<?php }else{ ?>
		dayEnabled = true;
<?php } ?>

	//	return [false, 'redDay'];

		var holydayTitle = "";
		var holydayCss = "";
		
		var currDay =  ("0" + date.getDate()).slice(-2) + "" + ("0" + (date.getMonth()+1)).slice(-2);
		var currIdxHoliday = jQuery.inArray(currDay, bookingfor.holydays);
		if (currIdxHoliday != -1) {
			holydayTitle = bookingfor.holydaysTitle[currIdxHoliday];
			holydayCss = "bfi-date-holidays ";
		}
		currDay =  ("0" + date.getDate()).slice(-2) + "" + ("0" + (date.getMonth()+1)).slice(-2) + date.getFullYear();
		currIdxHoliday = jQuery.inArray(currDay, bookingfor.holydays);
		if (currIdxHoliday != -1) {
			holydayTitle = bookingfor.holydaysTitle[currIdxHoliday];
			holydayCss = "bfi-date-holidays ";
		}

		arr = [dayEnabled, holydayCss, holydayTitle];  
		if(check.getTime() == from.getTime()) {
			arr = [dayEnabled, holydayCss + ' date-start-selected', holydayTitle ];
		}
		if(check.getTime() == to.getTime()) {
			arr = [dayEnabled, holydayCss + ' date-end-selected', holydayTitle];  
		}
		if(check > from && check < to) {
			arr = [dayEnabled, holydayCss + ' date-selected', holydayTitle];
		}
	  return arr;
	}

	function checkChildren(nch,showMsg) {
//		debugger;
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
			if(showMsg==1) { 
				setTimeout(showpopovercalculator(), 1);
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
//	printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	bfi_printChangedDateBooking();
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
	offsetDate.setDate(offsetDate.getDate() + <?php echo $variationPlanMinDuration ?>);
<?php } ?>

	switch (productAvailabilityType) {
		case 0:
			$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
			if ($("#<?php echo $checkoutId?>").datepicker("getDate") < date) {
				$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
			}
			break;
		case 1:
		<?php if(!empty($resourceId)) { ?>
			offsetDate.setDate(offsetDate.getDate() + 1);
		<?php } ?>
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
	offsetEndDate.setDate(offsetEndDate.getDate() + <?php echo $variationPlanMaxDuration //night ?>);
	$("#<?php echo $checkoutId?>").datepicker("option", "maxDate", offsetEndDate);
<?php } ?>

}
jQuery(document).ready(function() {

	checkDateBooking<?php echo $checkinId; ?>(jQuery, jQuery('#<?php echo $checkinId?>'), jQuery('#<?php echo $checkinId?>').datepicker({ dateFormat: "dd/mm/yy" }).val()); 
	<?php if(!empty($resourceId)) { ?>
		getAjaxDate()
	<?php }else{ ?>
		bfi_printChangedDateBooking();
	<?php } ?>

});


function quoteCalculatorChanged(callback) {
//	debugger;
	jQuery('#bfi_lblchildrenagescalculator').webuiPopover('destroy');
	jQuery('#resourceQuote').hide();
	jQuery('#resourceSummary').hide();
	jQuery('#errorbooking').hide();
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
//		jQuery('#calculateButton').removeClass("bfi-not-active");
		jQuery('.bfi-result-list').hide();
	}else{
//		jQuery('#calculateButton').addClass("bfi-not-active");
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
//		jQuery('#calculateButton').removeClass("bfi-not-active");
		jQuery('.bfi-result-list').hide();
	}else{
//		jQuery('#calculateButton').addClass("bfi-not-active");
	}
//	jQuery('#calculateButton').show();
	/*calculateQuote();*/
}

function countMinAdults(){
//	debugger;
	var minAdults = 0;
	var numAdults = new Number(jQuery('#adultscalculator').val() || 0);
	var numSeniores = new Number(jQuery('#seniorescalculator').val() || 0);
	var numChildren = new Number(jQuery("#childrencalculator").val() || 0);
	
	jQuery('#bfi-adult-info-calculator span').html(numAdults);
	jQuery('#bfi-senior-info-calculator span').html(numSeniores);
	jQuery('#bfi-child-info-calculator span').html(numChildren);
	
	jQuery('#searchformpersonsadult-calculator').val(numAdults);
	jQuery('#searchformpersonssenior-calculator').val(numSeniores);
	jQuery('#searchformpersonschild-calculator').val(numChildren);
	
	jQuery(".bfi_resource-calculatorForm-childrenages select").each(function(i) {
		jQuery('#searchformpersonschild'+(i+1)+'-calculator').val(jQuery(this).val());
	});

	jQuery('#searchformpersons-calculator').val(numAdults + numChildren + numSeniores);
	
	
	minAdults = numAdults + numSeniores;
	return minAdults;
}


function calculateQuote() {
//	debugger;
	jQuery('#bfi_lblchildrenagescalculator').webuiPopover('destroy');
//		jQuery('#bfi-calculatorForm').attr("action","<?php echo $formRoute?>?format=calc&tmpl=component")
//		jQuery('#bfi-calculatorForm').submit();
	jQuery('#showmsgchildagecalculator').val(0);
	var numChildren = new Number(jQuery(".bfi_resource-calculatorForm-children select#childrencalculator").val());
	checkChildren(numChildren,0);
	jQuery(".bfi_resource-calculatorForm-childrenages select:visible option:selected").each(function(i) {
		if(jQuery(this).text()==""){
			jQuery('#showmsgchildagecalculator').val(1);
			return;
		}
	});

	jQuery('input[name="state"]','#bfi-calculatorForm').val('');
	jQuery('input[name="extras[]"]','#bfi-calculatorForm').val('');
	jQuery('.bfi-percent-discount').webuiPopover('destroy');
<?php 
if(!empty($variationPlanId)){
?>
	jQuery('#bfi-calculatorForm').submit();
<?php 
}else{
?>
	jQuery('#bfi-calculatorForm').ajaxSubmit(getAjaxOptions());
<?php 
}
?>

}

function showpopovercalculator() {
		jQuery('#bfi_lblchildrenagescalculator').webuiPopover({
			content : jQuery("#bfi_childrenagesmsgcalculator").html(),
			container: document.body,
			cache: false,
			placement:"auto-bottom",
			maxWidth: "300px",
			type:'html',
			style:'bfi-webuipopover'
		});
		jQuery('#bfi_lblchildrenagescalculator').webuiPopover("show");
}
jQuery(window).resize(function(){
	jQuery('#bfi_lblchildrenagescalculator').webuiPopover('destroy');
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
	    url:        bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + 'task=listDate&resourceId=' + unitId + '&checkin=' + datereformat + "&simple=1", 
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

	jQuery("#bfi-calculatorForm .checking-container .ui-datepicker-trigger").click(function() {
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                if(jQuery("#bfi-calculatorForm .lastdatecheckout button.ui-datepicker-trigger").is(":visible")){
					jQuery("#bfi-calculatorForm .lastdate button.ui-datepicker-trigger").trigger("click");
				}
                jQuery("#bfi-calculatorForm .ui-datepicker-trigger").each(function() {
                    jQuery(this).addClass("activeclass");
                });
                jQuery("#bfi-calculatorForm .checking-container .ui-datepicker-trigger").removeClass("activeclass");
                jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
                jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
                jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
            }
        });
    })
    jQuery("#bfi-calculatorForm .ui-datepicker-trigger").click(function() {
        jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
        jQuery("#bfi-calculatorForm .ui-datepicker-trigger").each(function() {
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
    jQuery("#bfi-calculatorForm").hover(function(){
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
//	callAnalyticsEEc("addImpression", <?php echo json_encode($eecstays); ?>, "list", "Suggested Products");
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
        var strbuttonTextTimePeriod = "<?php echo date_i18n('D',$checkin->getTimestamp()) ?> <?php echo $checkin->format("d") ?> <?php echo date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y") ?>";
        var urlGetCompleteRatePlansStay = bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + 'task=getCompleteRateplansStay';
        var urlGetListCheckInDayPerTimes = bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + 'task=getListCheckInDayPerTimes';

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
				dialogClass: 'bfi-dialog bfi-dialog-timeperiod',
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
        var strbuttonTextTimeSlot = "<?php echo date_i18n('D',$checkin->getTimestamp()) ?> <?php echo $checkin->format("d") ?> <?php echo date_i18n('M',$checkin->getTimestamp()).' '.$checkin->format("Y") ?>";
        var daysToEnableTimeSlot = <?php echo json_encode($listDayTS) ?>;
        var currTimeSlotDisp = {};
		var dialogTimeslot;
		jQuery(document).ready(function () {
			initDatepickerTimeSlot();
			jQuery("#bfi-timeslot-select").attr("data-resid",0);
			jQuery("#bfi-timeslot-select").attr("data-sourceid",0);
			dialogTimeslot = jQuery("#bfimodaltimeslot").dialog({
				title: "<?php _e('Change your details', 'bfi') ?>",
				autoOpen: false,
				modal: true,
				width: 'auto',
				maxWidth: "300px",
				dialogClass: 'bfi-dialog bfi-dialog-timeslot',
				close: function() {
				}
			});
			bfi_currTRselected = null;
			jQuery(".bfi-result-list").on("click", ".bfi-timeslot", function (e) {

				var currSourceId = jQuery("#bfi-timeslot-select").attr("data-sourceid");
				var newSourceId = jQuery(this).attr("data-sourceid");
				
				var currResId = jQuery("#bfi-timeslot-select").attr("data-resid");
				var newResId = jQuery(this).attr("data-resid");
				var newDate = jQuery(this).attr("data-checkin");
				
//				if(currSourceId!=newSourceId ){
				if (bfi_currTRselected != jQuery(this).closest("tr")){
					bfi_currTRselected = jQuery(this).closest("tr");
					jQuery("#bfi-timeslot-select").attr("data-sourceid", newSourceId);
					jQuery("#bfi-timeslot-select").attr("data-resid", newResId);
					jQuery("#selectpickerTimeSlotRange").attr("data-resid", newResId);
					jQuery("#selectpickerTimeSlotRange").attr("data-sourceid", newSourceId);
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
var bfi_currMerchantId = <?php echo $merchant->MerchantId ?>;
var bfi_currAdultsAge = <?php echo COM_BOOKINGFORCONNECTOR_ADULTSAGE ?>;
var bfi_currSenioresAge = <?php echo COM_BOOKINGFORCONNECTOR_SENIORESAGE ?>;

var bfisrv = [];
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
var listServiceIds = "<?php echo implode(",", $allServiceIds) ?>";
var bfisrvloaded=false;
var resGrp = [];
var loadedResGrp=false;
var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
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

jQuery(document).ready(function () {
	getAjaxInformationsSrv();
	getAjaxInformationsResGrp();
//    								jQuery('.bfi-showperson-text-calculator').webuiPopover({
//									content : jQuery( "#bfishowpersoncalculator" ).html(),
//									container: document.body,
//									closeable:true,
//									placement:'auto-bottom',
//									dismissible:true,
//									type:'html',
//									style:'bfi-webuipopover'
//								});
//
	
	jQuery('.bfi-showperson-text-calculator').on('click', function (event, ui) {
//debugger;
		if (!!jQuery.uniform){
			jQuery.uniform.restore(jQuery("#bfishowpersoncalculator select"));
		}
		var clone = jQuery("#bfishowpersoncalculator").clone().attr('id', 'dialogIdClone');
		var original = jQuery("#bfishowpersoncalculator");
			var selects = jQuery(original).find("select");
			jQuery(selects).each(function(i) {
				var select = this;
//				jQuery(clone).find("select").eq(i).val(jQuery(select).val());
				jQuery(clone).find("select").eq(i).prop('selectedIndex', jQuery(select).prop('selectedIndex'));
			});				

		jQuery(original).html("");
		jQuery(clone).dialog({
			title: "<?php _e('Guest', 'bfi'); ?>",
			height: 'auto',
			width:'auto',
			modal: true,
			resizable: true,
			position:{
				my: "center top", 
				at: "center bottom",
				of: jQuery(this)
			},
			dialogClass: 'bfi-dialog bfi-guest',
			clickOutside: true,
			clickOutsideTrigger: ".bfi-showperson-text-calculator",
			open: function( event, ui ) {
					jQuery("#childrencalculator").change(function() {
						jQuery('#showmsgchildagecalculator').val(0);
						jQuery(".bfi_resource-calculatorForm-childrenages select:visible option:selected").each(function(i) {
							if(jQuery(this).text()==""){
								jQuery('#showmsgchildagecalculator').val(1);
								return;
							}
						});
						checkChildren(jQuery(this).val(),new Number(jQuery('#showmsgchildagecalculator').val()));

					});
			},
			close: function(){
//				debugger;
				jQuery(original).html(jQuery(clone).html());
				var selects = jQuery(clone).find("select");
				jQuery(selects).each(function(i) {
					var select = this;
					jQuery(original).find("select").eq(i).val(jQuery(select).val());
				});				
				jQuery(clone).remove();
			}
		});
    })

});

//-->
</script>
</div>