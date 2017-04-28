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

$merchant = $resource->Merchant;
$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$routeThanks = $routeMerchant .'/'. _x('thanks', 'Page slug', 'bfi' );
$routeThanksKo = $routeMerchant .'/'. _x('errors', 'Page slug', 'bfi' );

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );
$resourceName = BFCHelper::getLanguage($resource->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
$route = $url_resource_page.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);

$routeback = $url_resource_page.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);
//if($isportal){
//	$routeback =  JURI::root();
//}


//var_dump($_POST);

$idrecaptcha = uniqid("bfirecaptcha");



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





//$sugrsid = 'suggestedstay'.$resource_id.$rateplan_id;
////echo "<br>suggestedstay";
////var_dump($_SESSION['search.params'][$sugrsid] );
//
//$totdis = 'totalDiscounted'.$resource_id.$rateplan_id;
//
//$_SESSION['search.params'][$totdis];

//var_dump($_SESSION['search.params'][$totdis] );

$currentState = "booking";


$cCCTypeList = array();
$minyear = date("y");
$maxyear = $minyear+5;

$formRoute = $base_url .'/bfi-api/v1/task?task=sendOrders'; 

$privacy = BFCHelper::GetPrivacy($language);
$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);
//$additionalPurpose = null;
$policy = BFCHelper::GetPolicy($resource->ResourceId, $language);

$pars = BFCHelper::getSearchParamsSession();
$deposit = 0;

$order = BFI()->currentOrder;
if(empty($order)){
	$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
	$order = BFCHelper::calculateOrder($OrderJson,$language);
	BFI()->currentOrder = $order;
}
//model.SearchModel = SiteUtility.GetSearchModel();
$resourceid = intval(BFCHelper::getVar("hdnResourceId"));

//echo "<pre>";
//echo print_r($order);
//echo "</pre>";
//
//die();

///*----------------------------*/
//$stayrequest =  array();
//
//$stayrequest['resourceId'] =  $_SESSION['search.params']['resourceId'] = $_POST['resourceId'];
//$stayrequest['checkin'] =  $_SESSION['search.params']['checkin']->format('d/m/Y');
//$stayrequest['checkout'] =  $_SESSION['search.params']['checkout']->format('d/m/Y');
//$stayrequest['duration'] =  $_SESSION['search.params']['duration'];
//$stayrequest['paxages'] =  '['.implode(',', $_SESSION['search.params']['paxages']).']';
////$stayrequest['paxages'] =  $_SESSION['search.params']['paxages'];
//$stayrequest['extras'] = '';
//
//if(!empty($_SESSION['search.params']['extras'])){
//	$stayrequest['extras'] =  $_SESSION['search.params']['extras'];
//}
////$stayrequest['extras'] =  $_SESSION['search.params']['extras'];
//
//$stayrequest['packages'] =   '';
//if(!empty($_SESSION['search.params']['packages'])){
//	$stayrequest['packages'] =  $_SESSION['search.params']['packages'];
//}
//
//$stayrequest['pricetype'] =   '';
//if(!empty($_SESSION['search.params']['pricetype'])){
//	$stayrequest['pricetype'] =  $_SESSION['search.params']['pricetype'] = $_POST['pricetype'];
//}
//$stayrequest['rateplanId'] =   '';
//if(!empty($_SESSION['search.params']['rateplanId'])){
//	$stayrequest['rateplanId'] =  $_SESSION['search.params']['rateplanId'];
//}
//
//$stayrequest['variationPlanId'] =   '';
//if(!empty($_SESSION['search.params']['variationPlanId'])){
//	$stayrequest['variationPlanId'] =  $_SESSION['search.params']['variationPlanId'];
//}
//
//$stayrequest['state'] = 'booking';
//$stayrequest['gotCalculator'] = false;
//
//$nad = 0;
//$nch = 0;
//$nse = 0;
//$countPaxes = 0;
//$nchs = array(null,null,null,null,null,null);
//
//setlocale(LC_TIME, $language);
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
//array_push($nchs, null,null,null,null,null,null);
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
//$stayrequest = htmlspecialchars(json_encode($stayrequest), ENT_COMPAT, 'UTF-8');
//
////'resourceId' => $_SESSION['search.params']['resourceId'], 'checkin' => $_SESSION['search.params']['checkin'], 'checkout' =>$_SESSION['search.params']['checkout'] , 'duration' => $_SESSION['search.params']['duration'], 'paxages' => '['.implode(',', $_SESSION['search.params']['paxages']).']', 'extras' => $_SESSION['search.params']['extras'], 'packages' => $_SESSION['search.params']['packages'], 'pricetype' => $_SESSION['search.params']['pricetype'], 'rateplanId' => $_SESSION['search.params']['rateplanId'], 'state' => 'booking' , 'variationPlanId' => $_SESSION['search.params']['variationPlanId'], 'gotCalculator' => false);
//
////$staysuggested= htmlspecialchars($_SESSION['search.params'][$sugrsid], ENT_COMPAT, 'UTF-8');
//$staysuggested='';
//
//$completestay = $stay;
//
//if(!empty($completestay->SuggestedStay)){
//	$stay = $completestay->SuggestedStay;
//}
//
//				$totalWithVariation = $totalDiscounted + 0; // force int cast
//				$selVariationId = isset($stayrequest['variationPlanId'])?$stayrequest['variationPlanId']:'';
//				$selVariation=null;
//				if ($selVariationId != '') {
//					$totalWithVariation = $total;
//					foreach ($stay->Variations as $variation) {
//						if($variation->VariationPlanId == $selVariationId) {
//							$selVariation = $variation;
//							$totalWithVariation += (float)$variation->TotalAmount + (float)$variation->TotalPackagesAmount;
//							break;
//						}
//					}
//				}		
//		$newAllStays = array();
//
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
//			$newAllStays[] = clone($rs);
//		}
//
//if(!empty($stay)){
//
//	$currstay = $stay;
//	$currstay->DiscountedPrice = $totalWithVariation;
//	$currstay->RatePlanStay = $completestay;
//	unset($currstay->RatePlanStay->SuggestedStay);
//	$currstay->CalculatedPricesDetails = $completestay->CalculatedPricesDetails;
//	$currstay->Variations = $selVariation;
//	$currstay->DiscountVariation = $completestay->DiscountVariation;
//	$currstay->SupplementVariation = $completestay->SupplementVariation;
//	$staysuggested = htmlspecialchars(json_encode($currstay), ENT_COMPAT, 'UTF-8');
//	
////	echo "<pre>";
////	echo $staysuggested;
////	echo "</pre>";
//	
//}
////$bookingTypes = $this->MerchantBookingTypes;


$bookingTypes = $model->GetMerchantBookingTypeList($order->SearchModel, $resourceid, $language);
$bookingTypedefault ="";
//$bookingTypesDesc ="";
$bookingTypesoptions = array();
$bookingTypesValues = array();
$bookingTypeFrpmForm = isset($_REQUEST['bookingType'])?$_REQUEST['bookingType']:"";
//$totalWithVariation = $order->TotalDiscountedAmount;


if(!empty($bookingTypes)){
	$bookingTypesDescArray = array();
	foreach($bookingTypes as $bt)
	{
		$currDesc = BFCHelper::getLanguage($bt->Name, $language) . "<div class='ccdescr'>" . BFCHelper::getLanguage($bt->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) . "</div>";
		if($bt->AcquireCreditCardData && !empty($bt->Data)){

			$ccimgages = explode("|", $bt->Data);
			$cCCTypeList = array();
			$currDesc .= "<div class='ccimages'>";
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
		
		if(isset($calculatedBookingType->Value) && !empty($calculatedBookingType->Value)) {

			$totalWithVariation = 0;
			foreach ($order->Resources as $data) {
				$orderBookingTypeId = $data->BookingType;
				if ($orderBookingTypeId == $bt->BookingTypeId) {
					$totalWithVariation += $data->TotalDiscounted;
				}
			}

			if($calculatedBookingType->Value!='0' && $calculatedBookingType->Value!='0%' && $calculatedBookingType->Value!='100%')
			{
				if (strpos($calculatedBookingType->Value,'%') !== false) {
					$calculatedBookingType->Deposit = (float)str_replace("%","",$calculatedBookingType->Value) *(float) $totalWithVariation/100;
				}else{
					$calculatedBookingType->Deposit = $calculatedBookingType->Value;
				}
			}
			if($calculatedBookingType->Value==='100%'){
				$calculatedBookingType->Deposit = $totalWithVariation;
			}
		}

		$bookingTypesValues[$bt->BookingTypeId] = $calculatedBookingType;

		if($bt->IsDefault == true ){
			$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
			$deposit = $calculatedBookingType->Deposit;
		}

//		$bookingTypesDescArray[] = BFCHelper::getLanguage($bt->Description, $language);;
	}
//	$bookingTypesDesc = implode("|",$bookingTypesDescArray);
	if(empty($bookingTypedefault)){
		$bt = array_values($bookingTypesValues)[0];
		$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
		$deposit = $bt->Deposit;
	}

	if(!empty($bookingTypeFrpmForm)){
			if (array_key_exists($bookingTypeFrpmForm, $bookingTypesValues)) {
				$bt = $bookingTypesValues[$bookingTypeFrpmForm];
				$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
				$deposit = $bt->Deposit;
			}
	}

}
//
//$allStaysToView = array();
//$allstaysuggested= array(); //used in form to get all data 
//
//function pushStay($arr, $resourceid, $resStay, $defaultResource = null,&$staysuggesteds) {
//	$selected = array_values(array_filter($arr, function($itm) use ($resourceid) {
//		return $itm->ResourceId == $resourceid;
//	}));
//	$index = 0;
//	if(count($selected) == 0) {
//		$obj = new stdClass();
//		$obj->ResourceId = $resourceid;
//		if(isset($defaultResource) && $defaultResource->ResourceId == $resourceid) {
//			$obj->MinCapacityPaxes = $defaultResource->MinCapacityPaxes;
//			$obj->MaxCapacityPaxes = $defaultResource->MaxCapacityPaxes;
//			$obj->Name = $defaultResource->Name;
//			$obj->ImageUrl = $defaultResource->ImageUrl;
//			$obj->Availability = !empty($defaultResource->Availability)?$defaultResource->Availability:0;
//			$obj->Policy = $resStay->Policy;
//		} else {
//			$obj->MinCapacityPaxes = $resStay->MinCapacityPaxes;
//			$obj->MaxCapacityPaxes = $resStay->MaxCapacityPaxes;
//			$obj->Availability = $resStay->Availability;
//			$obj->Name = $resStay->ResName;
//			$obj->ImageUrl = $resStay->ImageUrl;
//			$obj->Policy = $resStay->Policy;
//		}
//		$obj->RatePlans = array();
//		//$obj->Policy = $completestay->Policy;
//		//$obj->Description = $singleRateplan->Description;
//		$arr[] = $obj;
//		$index = count($arr) - 1;
//	} else {
//		$index = array_search($selected[0], $arr);
//		//$obj = $selected[0];
//	}
//
//	$rt = new stdClass();
//	$rt->RatePlanId = $resStay->RatePlanId;
//	$rt->Name = $resStay->Name;	
//	$rt->PercentVariation = $resStay->PercentVariation;	
//	
//	$rt->TotalPrice=0;
//	$rt->TotalPriceString ="";
//	$rt->Days=0;
//	$rt->BookingType=$resStay->BookingType;
//	$rt->IsBase=$resStay->IsBase;
//
//	$rt->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
//	$rt->SelectablePrices = $resStay->SelectablePrices;
//	$rt->Variations = $resStay->Variations;
//	$rt->SimpleDiscountIds = implode(',', $resStay->SimpleDiscountIds);	
//	if(!empty($resStay->SuggestedStay->DiscountedPrice)){
//		$rt->TotalPrice = (float)$resStay->SuggestedStay->TotalPrice;
//		$rt->TotalPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->TotalPrice);
//		$rt->Days = $resStay->SuggestedStay->Days;
//		$rt->DiscountedPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->DiscountedPrice);
//		$rt->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
//	}
////	$rt->SuggestedStay = $resStay->SuggestedStay;
//	$rt->originalStay = $resStay;
//	$rt->DiscountVariation = $resStay->DiscountVariation;
//	$rt->SupplementVariation = $resStay->SupplementVariation;
//
//	$arr[$index]->RatePlans[] = $rt;
//
//	$currstaysuggested = "";
//	if(!empty($resStay->SuggestedStay)){
//
//		$tmpcurrstay = $resStay->SuggestedStay;
//		$tmpcurrstay->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
//		$tmpcurrstay->RatePlanStay = $resStay;
//		unset($tmpcurrstay->RatePlanStay->SuggestedStay);
//		$tmpcurrstay->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
//	//	$tmpcurrstay->Variations = $selVariation;
//		$tmpcurrstay->DiscountVariation = $resStay->DiscountVariation;
//		$tmpcurrstay->SupplementVariation = $resStay->SupplementVariation;
//		$currstaysuggested = htmlspecialchars(json_encode($tmpcurrstay), ENT_COMPAT, 'UTF-8');
//	}
//	//echo "<pre>currstaysuggested:<br />";
//	//echo print_r($currstaysuggested);
//	//echo "</pre>";
//	$staysuggesteds[$resStay->BookingType] = $currstaysuggested;
//
//	return $arr;
//}
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
//
////foreach($this->allstays as $rst) {
////	$allStaysToView = pushStay($allStaysToView, $rst->ResourceId, $rst);
////}
//
//
//$stayAvailability = 0;
//$DiscountedPrice=0;
//if(!empty($stay)){
//	$stayAvailability = $stay->Availability;
//	$DiscountedPrice = (float)$stay->DiscountedPrice;
//}
//
//$showQuote = ($totalDiscounted > 0 && $stayAvailability > 0);


?>
	<?php if (!empty($order) && !empty($order->Resources)):?>

<div class="com_bookingforconnector_resource-payment-form">
<div class="bf-title-book"><?php _e('Enter your details', 'bfi') ?></div>
<form method="post" id="resourcedetailsrequest" class="form-validate" action="<?php echo $formRoute; ?>">
	<div class="mailalertform">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<div >
				<label><?php _e('Name', 'bfi'); ?> *</label>
				<input type="text" value="<?php echo $current_user->user_login ; ?>" size="50" name="form[Name]" id="Name" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div >
				<label><?php _e('Surname', 'bfi'); ?> *</label>
				<input type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div >
				<label><?php _e('Email', 'bfi'); ?> *</label>
				<input type="email" value="<?php echo $current_user->user_email; ?>" size="50" name="form[Email]" id="formemail" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			<div >
				<label><?php _e('Reenter email', 'bfi'); ?> *</label>
				<input type="email" value="<?php echo $current_user->user_email; ?>" size="50" name="form[EmailConfirm]" id="formemailconfirm" required equalTo="#formemail" title="<?php _e('This field is required.', 'bfi') ?>">
			</div><!--/span-->
			
						
			<div class="inputaddress" style="display:;">
				<div >
					<label><?php _e('Address', 'bfi'); ?> </label>
					<input type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
				<div >
					<label><?php _e('Postal Code', 'bfi'); ?> </label>
					<input type="text" value="" size="20" name="form[Cap]" id="Cap"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
				<div >
					<label><?php _e('Country', 'bfi'); ?> </label>
						<select id="formNation" name="form[Nation]" class="bfi_input_select width90percent">
							<option value="AR">Argentina</option>
							<option value="AM">Armenia</option>
							<option value="AU">Australia</option>
							<option value="AZ">Azerbaigian</option>
							<option value="BE">Belgium</option>
							<option value="BY">Bielorussia</option>
							<option value="BA">Bosnia-Erzegovina</option>
							<option value="BR">Brazil</option>
							<option value="BG">Bulgaria</option>
							<option value="CA">Canada</option>
							<option value="CN">China</option>
							<option value="HR">Croatia</option>
							<option value="CY">Cyprus</option>
							<option value="CZ">Czech Republic</option>
							<option value="DK">Denmark</option>
							<option value="DE" <?php if($language == "de-DE") {echo "selected";}?>>Deutschland</option>
							<option value="EG">Egipt</option>
							<option value="EE">Estonia</option>
							<option value="FI">Finland</option>
							<option value="FR" <?php if($language == "fr-FR") {echo "selected";}?>>France</option>
							<option value="GE">Georgia</option>
							<option value="EN" <?php if($language == "en-GB") {echo "selected";}?>>Great Britain</option>
							<option value="GR" <?php if($language == "el-GR") {echo "selected";}?>>Greece</option>
							<option value="HU">Hungary</option>
							<option value="IS">Iceland</option>
							<option value="IN">Indian</option>
							<option value="IE">Ireland</option>
							<option value="IL">Israel</option>
							<option value="IT" <?php if($language == "it-IT") {echo "selected";}?>>Italia</option>
							<option value="JP">Japan</option>
							<option value="LV">Latvia</option>
							<option value="LI">Liechtenstein</option>
							<option value="LT">Lithuania</option>
							<option value="LU">Luxembourg</option>
							<option value="MK">Macedonia</option>
							<option value="MT">Malt</option>
							<option value="MX">Mexico</option>
							<option value="MD">Moldavia</option>
							<option value="NL">Netherlands</option>
							<option value="NZ">New Zealand</option>
							<option value="NO">Norvay</option>
							<option value="AT">Österreich</option>
							<option value="PL" <?php if($language == "pl-PL") {echo "selected";}?>>Poland</option>
							<option value="PT">Portugal</option>
							<option value="RO">Romania</option>
							<option value="SM">San Marino</option>
							<option value="SK">Slovakia</option>
							<option value="SI">Slovenia</option>
							<option value="ZA">South Africa</option>
							<option value="KR">South Korea</option>
							<option value="ES" <?php if($language == "es-ES") {echo "selected";}?>>Spain</option>
							<option value="SE">Sweden</option>
							<option value="CH">Switzerland</option>
							<option value="TJ">Tagikistan</option>
							<option value="TR">Turkey</option>
							<option value="TM">Turkmenistan</option>
							<option value="US" <?php if($language == "en-US") {echo "selected";}?>>USA</option>
							<option value="UA">Ukraine</option>
							<option value="UZ">Uzbekistan</option>
							<option value="VE">Venezuela</option>
						</select>
				</div><!--/span-->
			</div>
	    </div>
	    <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
	         <div >
              <label><?php _e('Note', 'bfi'); ?></label>
              <textarea name="form[note]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:104px;" ></textarea>    
            </div>
			<div >
				<label><?php _e('Phone', 'bfi'); ?> *</label>
				<input type="text" value="" size="20" name="form[Phone]" id="Phone" required  title="<?php _e('This field is required.', 'bfi') ?>" style="width:100%;">
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
					<option value="07.00 - 08.007">07:00 - 08:00</option>
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
				<input type="password" value="<?php echo $current_user->user_email; ?>" size="50" name="form[Password]" id="Password"   title="">
			</div><!--/span-->
				<div >
					<label><?php _e('City', 'bfi'); ?> </label>
					<input type="text" value="" size="50" name="form[City]" id="City"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
				<div >
					<label><?php _e('Province', 'bfi'); ?> </label>
					<input type="text" value="" size="20" name="form[Provincia]" id="Provincia"   title="<?php _e('This field is required.', 'bfi') ?>">
				</div><!--/span-->
		</div>
	</div>
<!-- VIEW_ORDER_PAYMENTMETHOD -->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 paymentoptions" style="display:none;" id="bookingTypesContainer">
			<h2><?php _e('Payment method', 'bfi') ?></h2>
			<p><?php _e('Please choose a payment method', 'bfi') ?></p>
			<?php  foreach ($bookingTypesoptions as $key => $value) { ?>
				<label for="form[bookingType]<?php echo $key ?>" id="form[bookingType]<?php echo $key ?>-lbl" class="radio">	
					<input type="radio" name="form[bookingType]" id="form[bookingType]<?php echo $key ?>" value="<?php echo $key ?>" <?php echo $bookingTypedefault == $key ? 'checked="checked"' : "";  ?>  ><?php echo $value ?><div class="ccdescr"></div>
				</label>
			<?php } ?>
		</div>
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 paymentoptions" id="bookingTypesDescriptionContainer">
			<h2 id="bookingTypeTitle"></h2>
			<span id="bookingTypeDesc"></span>
		</div>
	</div>
 		<!--<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>" id="totaldepositrequested">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12">
              <br>
              <?php _e('Total deposit : €', 'bfi'); ?>
			  <span class="totaldeposit" id="totaldeposit"><?php echo BFCHelper::priceFormat($deposit); ?></span> 
            </div>
        </div> -->
		<div class="clear"></div>

	


<div style="display:none;" id="ccInformations" class="borderbottom">
		<h2><?php _e('Credit card details', 'bfi') ?></h2>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php _e('Type', 'bfi') ?> </label>
					<select id="formcc_circuito" name="form[cc_circuito]" class="bfi_input_select">
						<?php 
							foreach($cCCTypeList as $ccCard) {
								?><option value="<?php echo $ccCard ?>"><?php echo $ccCard ?></option><?php 
							}
						?> 
					</select>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php _e('Holder', 'bfi') ?> </label>
				<input type="text" value="" size="50" name="form[cc_titolare]" id="cc_titolare" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php _e('Number', 'bfi') ?> </label>
				<input type="text" value="" size="50" maxlength="50" name="form[cc_numero]" id="cc_numero" required  title="<?php _e('This field is required.', 'bfi') ?>">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<label><?php _e('Valid until', 'bfi') ?></label>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
						<?php _e('Month (MM)', 'bfi') ?>
					</div><!--/span-->
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 ccdateinput">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
							<input type="text" value="" size="2" maxlength="2" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5" name="form[cc_mese]" id="cc_mese" required  title="<?php _e('This field is required.', 'bfi') ?>">
							<span class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 " style="text-align:center;" >/</span>
							<input type="text" value="" size="2" maxlength="2" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5" name="form[cc_anno]" id="cc_anno" required  title="<?php _e('This field is required.', 'bfi') ?>">
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

</div>	

<?php 
if($merchant->AcceptanceCheckIn != "-" && $merchant->AcceptanceCheckOut != "-" && !empty($merchant->OtherDetails) ){
?>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 infomerchant" >
				<h2><?php _e('Good to know', 'bfi') ?></h2>
				<br />
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> borderbottom padding20px0">
					<?php if($merchant->AcceptanceCheckIn != "-"){ ?>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><b><?php _e('Check-in', 'bfi') ?></b>&nbsp;</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><?php echo $merchant->AcceptanceCheckIn ?></div>
					<?php } ?>
					<?php if($merchant->AcceptanceCheckOut != "-"){ ?>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><b><?php _e('Check-out', 'bfi') ?></b>&nbsp;</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><?php echo $merchant->AcceptanceCheckOut ?></div>
					<?php } ?>
				</div>
				<?php if(!empty($merchant->OtherDetails) ){ ?>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> borderbottom padding20px0">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><b><?php _e('Info', 'bfi') ?></b></div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11 applyshorten"><?php echo BFCHelper::getLanguage($merchant->OtherDetails, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'))  ?></div>
				</div>
					<?php } ?>
			</div>
		</div>
<?php 
}
?>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
				<input name="form[accettazionepolicy]" class="checkbox" id="agreepolicy" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php _e('Mandatory', 'bfi') ?>">
				<label class="shownextelement"><?php _e('I agree to the conditions', 'bfi') ?></label>
				<textarea name="form[policy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $policy ?></textarea>
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
					<input name="form[accettazione]" class="checkbox" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php _e('Mandatory', 'bfi') ?>">
					<label class="shownextelement"><?php _e('I accept personal data treatment', 'bfi') ?></label>
					<textarea name="form[privacy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $privacy ?></textarea>    
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="display:<?php echo empty($additionalPurpose)?"none":"";?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
				<input name="form[accettazioneadditionalPurpose]" class="checkbox" id="agreeadditionalPurpose" aria-invalid="true" aria-required="true" required type="checkbox" title="<?php _e('Mandatory', 'bfi') ?>">
				<label class="shownextelement"><?php _e('I accept additional purposes', 'bfi') ?></label>
				<textarea name="form[additionalPurpose]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $additionalPurpose ?></textarea>    
			</div>
		</div>

		<?php bfi_display_captcha($idrecaptcha);  ?>
<div id="recaptcha-error-<?php echo $idrecaptcha ?>" style="display:none"><?php _e('Mandatory', 'bfi') ?></div>

		<input type="hidden" id="actionform" name="actionform" value="<?php echo $formlabel ?>" />
		<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId; ?>" /> 
		<input type="hidden" id="orderType" name="form[orderType]" value="a">
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language; ?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="<?php echo $formlabel ?>">
		<input type="hidden" id="resourceId" name="form[resourceId]" value="<?php echo $resource->ResourceId; ?>" /> 
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks; ?>">
		<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
		<input type="hidden" id="stayrequest" name="form[stayrequest]" value="<?php //echo $stayrequest ?>">
		<input type="hidden" id="staysuggested" name="form[staysuggested]" value="<?php //echo $staysuggested ?>">
		<input type="hidden" id="isgateway" name="form[isgateway]" value="0" />
		<input type="hidden" name="form[hdnOrderData]" id="hdnOrderData" value='[<?php echo json_encode($order) ?>]' />
		<input type="hidden" name="form[hdnOrderDataCart]" id="hdnOrderDataCart" value='<?php echo json_encode($order) ?>' />
		<input type="hidden" name="form[bookingtypeselected]" id="bookingtypeselected" value='<?php echo json_encode($order) ?>' />

		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> bf-footer-book" >
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10 " style="padding-top: 10px !important; padding-left: 20px !important;"><?php _e('Almost done', 'bfi') ?></div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 "><button type="submit" id="btnbfFormSubmit" style="display:none;"><?php _e('Send', 'bfi') ?></button></div>
		</div>

<?php
$selectedSystemType = array_values(array_filter($bookingTypesValues, function($bt) use($bookingTypedefault) {return $bt->BookingTypeId == $bookingTypedefault;}))
?>
<?php 
	$rdetailgrouped = array();
	foreach ($order->Resources as $data) {
		$id = $data->BookingType;
		if (isset($rdetailgrouped[$id])) {
			$rdetailgrouped[$id][] = $data;
		} else {
			$rdetailgrouped[$id] = array($data);
		}
	}
	foreach ($rdetailgrouped as $key=>$rdetailgroup) // foreach $rdetail
	{
		$tmpOrder = new stdClass;
		$tmpOrder->Resources = $rdetailgroup;
		$tmpOrder->SearchModel = $order->SearchModel;
?>
		<input type="hidden" id="tmpstaysuggested<?php echo $key ?>" value='[<?php echo json_encode($tmpOrder);?>]' />
<?php 

	}

?>

<script type="text/javascript">
<!--
var bookingTypesValues = null;

var completeStay = <?php echo json_encode($order); ?>;
var selectedSystemType = "<?php echo $selectedSystemType[0]->PaymentSystemRefId; ?>";
jQuery(function($)
		{
			var svcTotal = 0;
			var allItems = jQuery.makeArray(jQuery.map(jQuery.grep(completeStay.Resources, function(svc) {
				return svc.Tag == "ExtraServices";
			}), function(svc) {
				svcTotal += svc.TotalDiscounted;
				return {
					"id": "" + svc.PriceId + " - Service",
					"name": svc.Name,
					"category": "Services",
					"brand": "<?php echo $merchant->Name?>",
					"price": (svc.TotalDiscounted / svc.CalculatedQt).toFixed(2),
					"quantity": svc.CalculatedQt
				};
			}));
			/*
			jQuery.each(allItems, function(svc) {
				svcTotal += prc.TotalDiscounted;
			});
			*/
			allItems.push({
				"id": "<?php echo $resource->ResourceId?> - Resource",
				"name": <?php echo json_encode($resource->Name)?>,
				"category": "<?php echo $resource->MerchantCategoryName?>",
				"brand":  <?php echo json_encode($merchant->Name)?>,
				"variant": completeStay.RefId ? completeStay.RefId.toUpperCase() : "",
				"price": completeStay.TotalDiscounted - svcTotal,
				"quantity": 1
			});
			
			<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
				callAnalyticsEEc("addProduct", allItems, "checkout", "", {
					"step": 1
				});
			<?php endif;?>

			jQuery("#btnbfFormSubmit").show();


			$(".shownextelement").click(function(){
				$(this).next().toggle();
			});
			
			<?php if(!empty($bookingTypesValues)) : ?>
			bookingTypesValues = <?php echo json_encode($bookingTypesValues) ?>;// don't use quotes
			<?php endif; ?>
			$("#resourcedetailsrequest").validate(
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
				highlight: function(label) {
			    	$(label).removeClass('error').addClass('error');
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
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
					jQuery.blockUI({message: ''});
					if ($form.data('submitted') === true) {
						 return false;
					} else {
						// Mark it so that the next submit can be ignored
						$form.data('submitted', true);
						var svcTotal = 0;
						
						<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
						callAnalyticsEEc("addProduct", allItems, "checkout", "", {
							"step": 2,
						});
						
						callAnalyticsEEc("addProduct", allItems, "checkout_option", "", {
							"step": 2,
							"option": selectedSystemType
						});
						<?php endif; ?>
						form.submit();
					}
				}

			});
			$("input[name='form[bookingType]']").change(function(){
				var currentSelected = $(this).val().split(':')[0];
				selectedSystemType = Object.keys(bookingTypesValues).indexOf(currentSelected) > -1 ? bookingTypesValues[currentSelected].PaymentSystemRefId : "";
				checkBT();
			});
			var bookingTypeVal= $("input[name='form[bookingType]']");
			var container = $('#bookingTypesContainer');
			if(bookingTypeVal.length>1 && container.length>0){
					container.show();
			}
			function checkBT(){
					var ccInfo = $('#ccInformations');
					if (ccInfo.length>0) {
						try
						{
							var currCC = $("input[name='form[bookingType]']:checked");
							if (!currCC.length) {
								currCC = $("input[name='BookingType']")[0];
								$(currCC).prop("checked", true);
							}
							var cc = $(currCC).val();
							var ccVal = cc.split(":");
							var reqCC = ccVal[1];
							if (reqCC) { 
								ccInfo.show();
							} else {
								ccInfo.hide();
							}
							var idBT = ccVal[0];

							$("#bookingtypeselected").val(idBT);

							$.each(bookingTypesValues, function(key, value) {
								if(idBT == value.BookingTypeId){
									$("#bookingTypeTitle").html(value.Name);
									$("#bookingTypeDesc").html(value.Description);
									if(value.Deposit!=null && value.Deposit!='0'){
										
										$("#totaldepositrequested"+idBT).show();
										$("#footer-deposit"+idBT).html(value.Deposit);

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
//										$("#bf-summary-footer-deposit").hide();
									}
								}
							});

							$(".bf-bBookingType").hide();
							$(".bf-summary-footer").hide();
							$(".bf-bBookingType"+idBT).show();
							$(".bf-summary-footer"+idBT).show();
							$("#hdnOrderData").val($("#tmpstaysuggested"+idBT).val());
//							$("#staysuggested").val($("#tmpstaysuggested"+idBT).val());

							
						}
						catch (err)
						{
						}

					}
			}
			checkBT();
	var shortenOption = {
		moreText: "+ <?php _e('Details', 'bfi') ?>",
		lessText: " - <?php _e('Details', 'bfi') ?>",
		showChars: '250'
	};
	jQuery(".applyshorten").shorten(shortenOption);
		});

	//-->
	</script>	
</form>

</div>
			<br /><br />
	<?php else:?>		
		<div class="errorbooking" id="errorbooking">
			<strong><?php _e('No results available for the submitted data', 'bfi') ?></strong><br />
			<a href="<?php echo $routeback ?>">back</a>
		</div>
	<?php endif;?>		
