<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$base_url = get_site_url();

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$currModID = uniqid('bfisearch');

// get searchresult page...
$searchOnSell_page = get_post( bfi_get_page_id( 'searchonsell' ) );
$url_page_RealEstate = get_permalink( $searchOnSell_page->ID );

$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
$url_page_Resources = get_permalink( $searchAvailability_page->ID );

if(COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR==1){
?>
	<style type="text/css">
		.ui-datepicker-trigger.activeclass:after {
		  top: 95px !important;
		}
	</style>
<?php
}

$parsRealEstate = BFCHelper::getSearchOnSellParamsSession();
$parsResource = BFCHelper::getSearchParamsSession();

$searchtypetab = -1;

$contractTypeId = 0;
$searchType = "0";
$categoryIdRealEstate = 0;
$categoryIdResource = 0;
$merchantCategoryIdRealEstate = 0;
$merchantCategoryIdResource = 0;

$zoneId = 0;
$cityId = 0;
$zoneIds = '';
$pricemax = '';
$pricemin = '';
$areamin = '';
$areamax = '';
$points = '';
$roomsmin = '';
$roomsmax = '';
$bathsmin = '';
$bathsmax = '';
$services = '';
$isnewbuilding='';
$zoneIdsSplitted = '';
$bedroomsmin = '';
$bedroomsmax = '';
$checkoutspan = '+1 day';
$checkin = new DateTime();
$checkout = new DateTime();
$paxes = 2;
$paxages = array();
$masterTypeId = '';
$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$durationId = uniqid('duration');
$duration = 1;


if (!empty($parsRealEstate)){
	$contractTypeId = isset($parsRealEstate['contractTypeId']) ? $parsRealEstate['contractTypeId'] : 0;
	$categoryIdRealEstate = isset($parsRealEstate['unitCategoryId']) ? $parsRealEstate['unitCategoryId']: 0;

	$zoneId = isset($parsRealEstate['zoneId']) ? $parsRealEstate['zoneId'] :0;

	if(!empty($parsRealEstate['cityId'])){
		$cityId = $parsRealEstate['cityId'] ?: 0;
	}
	$searchType = isset($parsRealEstate['searchType']) ? $parsRealEstate['searchType'] : 0;
//	$searchtypetab = isset($parsRealEstate['searchtypetab']) ? $parsRealEstate['searchtypetab'] : -1;
	$searchtypetab = BFCHelper::getVar('searchtypetab',(isset($parsRealEstate['searchtypetab']) ? $parsRealEstate['searchtypetab'] : -1));

	if(!empty($parsRealEstate['zoneIds'])){
		$zoneIds = $parsRealEstate['zoneIds'];
		$zoneIdsSplitted = explode(",",$zoneIds);
	}
	$pricemax = isset($parsRealEstate['pricemax']) ? $parsRealEstate['pricemax']: null;
	$pricemin = isset($parsRealEstate['pricemin']) ? $parsRealEstate['pricemin']: null;
	$areamin = isset($parsRealEstate['areamin']) ? $parsRealEstate['areamin']: null;
	$areamax = isset($parsRealEstate['areamax']) ? $parsRealEstate['areamax']: null;
	$roomsmin = isset($parsRealEstate['roomsmin']) ? $parsRealEstate['roomsmin']: null;
	$roomsmax = isset($parsRealEstate['roomsmax']) ? $parsRealEstate['roomsmax']: null;
	$bathsmin = isset($parsRealEstate['bathsmin']) ? $parsRealEstate['bathsmin']: null;
	$bathsmax = isset($parsRealEstate['bathsmax']) ? $parsRealEstate['bathsmax']: null;
	$points = isset($parsRealEstate['points']) ? $parsRealEstate['points']: null;
	$services = isset($parsRealEstate['services']) ? $parsRealEstate['services']: null;
	if (isset($parsRealEstate['isnewbuilding']) && !empty($parsRealEstate['isnewbuilding']) && $parsRealEstate['isnewbuilding'] =="1") {
		$isnewbuilding = ' checked="checked"';
	}
	$bedroomsmin = isset($parsRealEstate['bedroomsmin']) ? $parsRealEstate['bedroomsmin']: null;
	$bedroomsmax = isset($parsRealEstate['bedroomsmax']) ? $parsRealEstate['bedroomsmax']: null;
}

if (!empty($parsResource)){
		
	$checkin = !empty($parsResource['checkin']) ? $parsResource['checkin'] : new DateTime();
	$checkout = !empty($parsResource['checkout']) ? $parsResource['checkout'] : new DateTime();
	
//	$searchtypetab = isset($parsResource['searchtypetab']) ? $parsResource['searchtypetab'] : -1;
//	$availabilitytype = isset($parsResource['availabilitytype']) ? $parsResource['availabilitytype'] : 1;
	$searchtypetab = BFCHelper::getVar('searchtypetab',(isset($parsResource['searchtypetab']) ? $parsResource['searchtypetab'] : -1));

	$zoneId = !empty($parsResource['zoneId']) ? $parsResource['zoneId'] :0;
	$paxes = !empty($parsResource['paxes']) ? $parsResource['paxes'] : 2;
	$paxages = !empty($parsResource['paxages'])? $parsResource['paxages'] :  array('18','18');
	$merchantCategoryIdResource = !empty($parsResource['merchantCategoryId'])? $parsResource['merchantCategoryId']: 0;
	$masterTypeId = !empty($parsResource['masterTypeId'])? $parsResource['masterTypeId']: 0;

	if (empty($parsResource['checkout'])){
		$checkout->modify($checkoutspan);
	}
}


$startDate =  new DateTime();
$startDate->setTime(0,0,0);
$checkin->setTime(0,0,0);
$checkout->setTime(0,0,0);

if ($checkin < $startDate){
	$checkin = $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

if ($checkin == $checkout){
	$checkout->modify($checkoutspan);
}



$duration = $checkin->diff($checkout);

$tablistSelected = ( ! empty( $instance['tablistSelected'] ) ) ? $instance['tablistSelected'] : array();

$tablistResources = array_intersect($tablistSelected,array(0,1,2));
$tablistRealEstate = array_intersect($tablistSelected, array(3));

if(!in_array($searchtypetab,$tablistSelected)){
	$searchtypetab = -1;
}

$groupbycondominium = ( ! empty( $instance['groupbycondominium'] ) ) ? esc_attr($instance['groupbycondominium']) : '0';
$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
$showLocation = ( ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
$showMapIcon = ( ! empty( $instance['showMapIcon'] ) ) ? esc_attr($instance['showMapIcon']) : '0';
$showAccomodations = ( ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';
$showDateRange = ( ! empty( $instance['showDateRange'] ) ) ? esc_attr($instance['showDateRange']) : '0';

$showAdult = ( ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '0';
$showChildren = ( ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '0';
$showSenior = ( ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
$showServices = ( ! empty( $instance['showServices'] ) ) ? esc_attr($instance['showServices']) : '0';
$showOnlineBooking = ( ! empty( $instance['showOnlineBooking'] ) ) ? esc_attr($instance['showOnlineBooking']) : '0';
$showMaxPrice = ( ! empty( $instance['showMaxPrice'] ) ) ? esc_attr($instance['showMaxPrice']) : '0';
$showMinFloor = ( ! empty( $instance['showMinFloor'] ) ) ? esc_attr($instance['showMinFloor']) : '0';
$showContract = ( ! empty( $instance['showContract'] ) ) ? esc_attr($instance['showContract']) : '0';

$showBedRooms = ( ! empty( $instance['showBedRooms'] ) ) ? esc_attr($instance['showBedRooms']) : '0';
$showRooms = ( ! empty( $instance['showRooms'] ) ) ? esc_attr($instance['showRooms']) : '0';
$showBaths = ( ! empty( $instance['showBaths'] ) ) ? esc_attr($instance['showBaths']) : '0';
$showOnlyNew = ( ! empty( $instance['showOnlyNew'] ) ) ? esc_attr($instance['showOnlyNew']) : '0';
$showServicesList = ( ! empty( $instance['showServicesList'] ) ) ? esc_attr($instance['showServicesList']) : '0';

$merchantCategoriesSelectedBooking = ( ! empty( $instance['merchantcategoriesbooking'] ) ) ? $instance['merchantcategoriesbooking'] : array();
$merchantCategoriesSelectedActivities = ( ! empty( $instance['merchantcategoriesactivities'] ) ) ? $instance['merchantcategoriesactivities'] : array();
$merchantCategoriesSelectedRealEstate = ( ! empty( $instance['merchantcategoriesrealestate'] ) ) ? $instance['merchantcategoriesrealestate'] : array();
$unitCategoriesSelectedBooking = ( ! empty( $instance['unitcategoriesbooking'] ) ) ? $instance['unitcategoriesbooking'] : array();
$unitCategoriesSelectedActivities = ( ! empty( $instance['unitcategoriesactivities'] ) ) ? $instance['unitcategoriesactivities'] : array();
$unitCategoriesSelectedRealEstate = ( ! empty( $instance['unitcategoriesrealestate'] ) ) ? $instance['unitcategoriesrealestate'] : array();


$merchantCategoriesResource = array();
$merchantCategoriesRealEstate = array();
$unitCategoriesResource = array();
$unitCategoriesRealEstate = array();

$listmerchantCategoriesResource = "";
$listmerchantCategoriesRealEstate = "";

$availabilityTypeList = array();
$availabilityTypeList['1'] = __('Nights', 'bfi');
$availabilityTypeList['0'] = __('Days', 'bfi');

$availabilityTypesSelectedBooking = ( ! empty( $instance['availabilitytypesbooking'] ) ) ? $instance['availabilitytypesbooking'] : array();
$availabilityTypesSelectedActivities = ( ! empty( $instance['availabilitytypesactivities'] ) ) ? $instance['availabilitytypesactivities'] : array();

$itemTypesSelectedBooking = ( ! empty( $instance['itemtypesbooking'] ) ) ? $instance['itemtypesbooking'] : array();
$itemTypesSelectedActivities = ( ! empty( $instance['itemtypesactivities'] ) ) ? $instance['itemtypesactivities'] : array();

$groupBySelectedBooking = ( ! empty( $instance['groupbybooking'] ) ) ? $instance['groupbybooking'] :  array(0);
$groupBySelectedActivities = ( ! empty( $instance['groupbyactivities'] ) ) ? $instance['groupbyactivities'] : array(0);


$tmpMerchantCategoryIdResource = (strpos($merchantCategoryIdResource, ',') !== FALSE )?"0":$merchantCategoryIdResource;
$tmpmasterTypeId = (strpos($masterTypeId, ',') !== FALSE )?"0":$masterTypeId;

if($showAccomodations){
	if(!empty($merchantCategoriesSelectedBooking) || !empty($merchantCategoriesSelectedActivities) || !empty($merchantCategoriesSelectedRealEstate) ){
//		$allMerchantCategories = BFCHelper::getMerchantCategories();
		$allMerchantCategories = BFCHelper::getMerchantCategoriesForRequest($language);

		if(!empty($merchantCategoriesSelectedBooking) || !empty($merchantCategoriesSelectedActivities) ){
			$listmerchantCategoriesResource = '<option value="0">'.__('All', 'bfi').'</option>';
		}
		if(!empty($merchantCategoriesSelectedRealEstate) ){
			$listmerchantCategoriesRealEstate = '<option value="0">'.__('All', 'bfi').'</option>';
		}
		if (!empty($allMerchantCategories))
		{
			foreach($allMerchantCategories as $merchantCategory)
			{
				if(in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedBooking) || in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedActivities)){
					$merchantCategoriesResource[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
					$listmerchantCategoriesResource .= '<option value="'.$merchantCategory->MerchantCategoryId.'" ' . ($merchantCategory->MerchantCategoryId== $tmpMerchantCategoryIdResource? 'selected':'' ).'>'.$merchantCategory->Name.'</option>';
				}
				if(in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedRealEstate)){
					$merchantCategoriesRealEstate[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
					$listmerchantCategoriesRealEstate .= '<option value="'.$merchantCategory->MerchantCategoryId.'" ' . ($merchantCategory->MerchantCategoryId== $merchantCategoryIdRealEstate? 'selected':'' ).'>'.$merchantCategory->Name.'</option>';
				}
			}
		}

	}

	$listunitCategoriesResource = "";
	if(!empty($unitCategoriesSelectedBooking) || !empty($unitCategoriesSelectedActivities)) {
		$allUnitCategories =  BFCHelper::GetProductCategoryForSearch($language,1);
		if (!empty($allUnitCategories))
		{
			$listunitCategoriesResource = '<option value="0">'.__('All', 'bfi').'</option>';
			foreach($allUnitCategories as $unitCategory)
			{
				if(in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedBooking) || in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedActivities)){
					$unitCategoriesResource[$unitCategory->ProductCategoryId] = $unitCategory->Name;
					$listunitCategoriesResource .= '<option value="'.$unitCategory->ProductCategoryId.'" ' . ($unitCategory->ProductCategoryId == $tmpmasterTypeId? 'selected':'' ).'>'.$unitCategory->Name.'</option>';
				}
			}
		}
	}


	$listunitCategoriesRealEstate = "";
	if(!empty($unitCategoriesSelectedRealEstate) ) {
		$allUnitCategoriesRealEstate =  BFCHelper::GetProductCategoryForSearch($language,2);
		if (!empty($allUnitCategoriesRealEstate))
		{
			$listunitCategoriesRealEstate = '<option value="0">'.__('All', 'bfi').'</option>';
			foreach($allUnitCategoriesRealEstate as $unitCategory)
			{
				if(in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedRealEstate)){
					$unitCategoriesResource[$unitCategory->ProductCategoryId] = $unitCategory->Name;
					$listunitCategoriesRealEstate .= '<option value="'.$unitCategory->ProductCategoryId.'" ' . ($unitCategory->ProductCategoryId == $categoryIdRealEstate? 'selected':'' ).'>'.$unitCategory->Name.'</option>';
				}
			}
		}
	}
}


//$masterTypologiesString = "";
//$merchantCategoriesString = "";
//if($showAccomodations){
//	$masterTypologies =  BFCHelper::GetProductCategoryForSearch($language,1);
//
//	if(!empty($masterTypologies)){
//		$masterTypologiesString = '<option value="0">'.__('Select', 'bfi').'</option>';
//		$masterTypologies = is_array($masterTypologies) ? $masterTypologies : array();
//		foreach ($masterTypologies as $mc) {
//			if($mc->ProductCategoryId == $masterTypeId){
//				$masterTypologiesString = $masterTypologiesString . '<option value="'.$mc->ProductCategoryId.'" selected>'.$mc->Name.'</option>';
//			}else{
//				$masterTypologiesString = $masterTypologiesString . '<option value="'.$mc->ProductCategoryId.'">'.$mc->Name.'</option>';
//			}
//		}
//	}
//
//	$merchantCategories = BFCHelper::getMerchantCategoriesForRequest($language);
//	
//	if(!empty($merchantCategories)){
//		$merchantCategoriesString = '<option value="0" >'.__('Select', 'bfi').'</option>'; 
//		$merchantCategories = is_array($merchantCategories) ? $merchantCategories : array();
//		foreach ($merchantCategories as $mc) {
//			if($mc->MerchantCategoryId == $merchantCategoryIdResource){
//				$merchantCategoriesString = $merchantCategoriesString . '<option value="'.$mc->MerchantCategoryId.'" selected>'.$mc->Name.'</option>';
//			}else{
//				$merchantCategoriesString = $merchantCategoriesString . '<option value="'.$mc->MerchantCategoryId.'">'.$mc->Name.'</option>';
//			}
//		}
//	}
//
//}





$blockmonths = '[14]';
$blockdays = '[7]';

if(!empty($instance['blockmonths']) && count($instance['blockmonths'])>0){
	$blockmonths = '[' . implode(',', $instance['blockmonths']) . ']';
}

if(!empty($instance['blockdays']) && count($instance['blockdays'])>0){
	$blockdays = '[' . implode(',', $instance['blockdays']) . ']';
}



if (!empty($services) ) {
	$filtersServices = explode(",", $services);
}

if (isset($filters)) {
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
	}

}

$listlocations="";
$zonesString="";
$listzoneIds = '';

if($showLocation){
	$locations = BFCHelper::getLocations();
	$listlocations = '<option value="0">'.__('All', 'bfi').'</option>';
	if(!empty($locations)){
		foreach ($locations as $lz) {
			if(empty($cityId) && $cityId != 0)
				$cityId = $lz->CityId;
			if($lz->CityId == $cityId){
				$listlocations .= '<option value="'.$lz->CityId.'" selected>'.$lz->Name.'</option>';
			}else{
				$listlocations .= '<option value="'.$lz->CityId.'">'.$lz->Name.'</option>';
			}
		}
	}
	if($showMapIcon){ 
		$listlocations .= '<option value="-1000" >'.__('Map', 'bfi').'</option>';
	}
	
	$locationZones = BFCHelper::getLocationZones();
	
	if(!empty($locationZones)){
		$zonesString = '<option value="0" selected>'.__('All', 'bfi').'</option>';
		foreach ($locationZones as $lz) {
			if(empty($zoneId) && $zoneId != 0){
				$zoneId = $lz->LocationZoneID;
			}
			if($lz->LocationZoneID == $zoneId){
				$zonesString = $zonesString . '<option value="'.$lz->LocationZoneID.'" selected>'.$lz->Name.'</option>';
			}else{
				$zonesString = $zonesString . '<option value="'.$lz->LocationZoneID.'">'.$lz->Name.'</option>';
			}

		}
	}
	if($cityId>=-1) {
		$zoneIdsList = BFCHelper::getLocationZonesByLocationId($cityId);
		if(!empty($zoneIdsList)){
			foreach ($zoneIdsList as $lz) {
				if(is_array($zoneIdsSplitted) && in_array($lz->GeographicZoneId,$zoneIdsSplitted)){
					$listzoneIds .= '<option value="'.$lz->GeographicZoneId.'" selected >'.$lz->Name.'</option>';
				}else{
					$listzoneIds .= '<option value="'.$lz->GeographicZoneId.'">'.$lz->Name.'</option>';
				}
			}
		}
	}

} //if($showLocation)
		
$listcontractType = '<option value="0" selected>'.__('On sale', 'bfi').'</option>';
$listcontractType .= '<option value="1">'.__('To rent', 'bfi').'</option>';

if($contractTypeId ==1 ){
	$listcontractType = '<option value="0">'.__('On sale', 'bfi').'</option>';
	$listcontractType .= '<option value="1" selected>'.__('To rent', 'bfi').'</option>';
}


$baths = array(
	'|' =>  __('All', 'bfi') ,
	'1|1' =>  __('1') ,
	'2|2' =>  __('2') ,
	'3|3' =>  __('3') ,
	'3|' =>  __('>3') 
);


//$show_direction = $params->get('show_direction');
//$show_title = $params->get('show_title');




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

$showChildrenagesmsg = isset($_REQUEST['showmsgchildage']) ? $_REQUEST['showmsgchildage'] : 0;

?>
<?php 
echo $before_widget;
// Check if title is set
if ( $title ) {
  echo $before_title . $title . $after_title;
}
$tabActive = "";

?>

<div class="mod_bookingforsearch" id="bfisearch<?php echo $currModID ?>" >
    <ul class="bfi-tabs" id="navbookingforsearch<?php echo $currModID ?>" style="<?php echo (count($tablistSelected)>1) ?"": "display:none" ?>">
		<?php if(in_array(0, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 0 ){
			$tabActive = "active";
			$searchtypetab = 0;
		}else{
			$tabActive = "";  
		}
		?>
		<li class="<?php //echo $tabActive ?>" data-searchtypeid="0">
            <a href="#bfisearch<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchResources">
                <i class="fa fa-suitcase" aria-hidden="true"></i><br />
                <?php _e('Booking', 'bfi') ?>
            </a>
        </li>
		<?php endif;  ?>
		<?php if(in_array(2, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 2 ){
			$tabActive = "active";
			$searchtypetab = 2;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="<?php //echo $tabActive ?>" data-searchtypeid="2">
            <a href="#bfisearch<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchTimeSlots">
                <i class="fa fa-calendar" aria-hidden="true"></i><br />
                <?php _e('Activities', 'bfi') ?>
            </a>
        </li>
		<?php endif;  ?>
		<?php if(in_array(3, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 3 ){
			$tabActive = "active";
			$searchtypetab = 3;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="<?php //echo $tabActive ?>" data-searchtypeid="3">
            <a href="#bfisearchselling<?php echo $currModID ?>" data-toggle="tab" aria-expanded="false" class="searchSelling">
                <i class="fa fa-home" aria-hidden="true"></i><br />
                <?php _e('Real Estate', 'bfi') ?>
            </a>
        </li>
		<?php endif;  ?>
    </ul>
    <div class="tab-content">
<?php if(!empty($tablistResources)): ?>
        <div id="bfisearch<?php echo $currModID ?>" class="tab-pane fade active in">
		<form action="<?php echo $url_page_Resources; ?>" method="get" id="searchform<?php echo $currModID ?>" class="bfi_form_<?php echo $showdirection?"horizontal":"vertical"; ?> ">
				<?php if(!empty($zonesString) && $showLocation){ ?>
					<div class="bfi_destination bfi_container">
						<label><?php _e('Destination', 'bfi') ?></label>
						<select id="locationzone" name="locationzone" class="inputtotal" data-live-search="true" data-width="99%">
						<?php echo $zonesString; ?>
						</select>
					</div>
				<?php } //$showLocation ?>
				<?php if(!empty($listunitCategoriesResource) && $showAccomodations){ ?>
					<div class="bfi_unitcategoriesresource bfi_container">
						<label><?php _e('Type', 'bfi') ?></label>
						<select id="masterTypeId<?php echo $currModID ?>" name="masterTypeId" class="inputtotal">
							<?php echo $listunitCategoriesResource; ?>
						</select>
					</div>
				<?php } //$showAccomodations ?>
				<?php if(!empty($listmerchantCategoriesResource) && $showAccomodations){ ?>
					<div class="bfi_merchantcategoriesresource bfi_container">
						<label><?php _e('Tipology', 'bfi') ?></label>
						<select id="merchantCategoryId<?php echo $currModID ?>" name="merchantCategoryId" onchange="checkSelSearch<?php echo $currModID ?>();" class="inputtotal hideRent">
							<?php echo $listmerchantCategoriesResource; ?>
						</select>
					</div>
				<?php } //$showAccomodations ?>
				<?php if($showDateRange){ ?>
				<div class="bfi_showdaterange bfi_container">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend ">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>5">
								<label><?php _e('Check-in' , 'bfi' ); ?></label>
								<div class="dateone lastdate dateone_div checking-container">
									<input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" />
								</div>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>5" id="divcheckoutsearch<?php echo $currModID ?>">
								<label><?php _e('Check-out' , 'bfi' ); ?></label>
								<div class="dateone lastdate lastdate_div">
									<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" />
								</div>
							</div>

							
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>2" id="divcalendarnightsearch<?php echo $currModID ?>">
								<div class="calendarnight" id="calendarnight<?php echo $durationId ?>"><?php echo $duration->format('%a') ?></div>
								
								<div class="calendarnightlabel">
								<select data-val="true" class="resbynight" id="calendarnightselect<?php echo $currModID ?>" name="AvailabilityTypeselected" disabled="disabled" style="display">
									<option value="1"><?php _e('Nights' , 'bfi' ); ?></option>
									<option value="0"><?php _e('Days' , 'bfi' ); ?></option>
								</select></div>
								<div class="bfi-clearboth"></div>
							</div>
					</div>
				</div>
				<?php } //$showDateRange ?>

				<?php if($showAdult){?>
					<div class="bfi_showperson bfi_container">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> form-group">
							<div class="bfi_showadult <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><!-- Adults -->
								<label><?php _e('Adults', 'bfi'); ?></label>
								<select name="adults" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display:inline-block !important;">
									<?php
									foreach (range(1, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nad == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php if($showSenior){?>
							<div class="bfi_showsenior <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><!-- Seniores -->
								<label><?php _e('Seniores', 'bfi'); ?></label>
								<select  name="seniores" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display:inline-block !important;">
									<?php
									foreach (range(0, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nse == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php }?>
						<?php if($showChildren){?>
							<div class="bfi_showchildren <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4" id="mod_bookingforsearch-children<?php echo $currModID ?>"  class="col-sm-4"><!-- n childrens -->
								<label><?php _e('Children', 'bfi'); ?></label>
								<select name="children" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display:inline-block !important;">
									<?php
									foreach (range(0, 4) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nch == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php }?>
						<?php if(!empty($services) && $showServices){?>
							<div class="bfi_showservices icons_right">
								<?php 
									foreach ($services as $service){
										$serviceActive ="";
										if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
											$serviceActive =" active";			
										}
								  ?>
										<a href="javascript: void(0);" class="btn btn-xs btnservices <?php echo $serviceActive ?> btnservices<?php echo $currModID ?>" rel="<?php echo $service->ServiceId ?>"  aria-pressed="false"><i class="fa <?php echo $service->IconSrc ?>" aria-hidden="true"></i></a>
								<?php
									  }
								  ?>				
							</div>
						<?php }?>
						</div>
					</div>
					<?php if($showChildren){?>
						<div class="mod_bookingforsearch-childrenages" style="display:none;"  id="mod_bookingforsearch-childrenages<?php echo $currModID ?>">
						
							<span ><?php _e('Age of children', 'bfi'); ?></span>
							<span id="bfi_lblchildrenagesat<?php echo $currModID ?>"><?php echo  _e('on', 'bfi') . " " .$checkout->format("d"). " " .$checkout->format("M"). " " . $checkout->format("Y") ?></span><br /><!-- Ages childrens -->	
							<div class="select_box">
							<select id="childages1" name="childages1" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[0] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<select id="childages2" name="childages2" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[1] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<?php echo $showdirection?"":"<br>"; ?> 
							<select id="childages3" name="childages3" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[2] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<select id="childages4" name="childages4" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[3] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<select id="childages5" name="childages5" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[4] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							</div>
							<span class="bfi-childmessage" id="bfi_lblchildrenages<?php echo $currModID ?>">&nbsp;</span>
							<div class="bfi-clearboth"></div>
							<br />
						</div>
					<?php }?>
				<?php } //$showAdult?>
	        <?php if($showOnlineBooking){ ?>
	            <div class="bfi_showonlinebooking bfi_container bfsearchfilter">
					<input type="checkbox" name="bookableonly" id="bookableonly<?php echo $currModID ?>" value="1"  <?php if(!empty($bookableonly)){ echo ' checked'; }   ?>/><?php _e('Show only online booking', 'bfi') ?>
				</div>
			<?php } ?>
			<div class="bfi-searchbutton-wrapper bfi_container" id="divBtnResource<?php echo $currModID ?>">
				<a  id="BtnResource<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);"><i class="fa fa-search" aria-hidden="true"></i> <?php _e('Search', 'bfi') ?></a>
			</div>
			<div class="bfi-clearboth"></div>
			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" name="onlystay" value="1">
			<input type="hidden" name="persons" value="<?php echo $nad + $nch?>" id="searchformpersons<?php echo $currModID ?>">
			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
			<input type="hidden" value="<?php echo $searchtypetab ?>" name="searchtypetab" id="searchtypetab<?php echo $currModID ?>" />
			<input type="hidden" value="0" name="showmsgchildage" id="showmsgchildage<?php echo $currModID ?>"/>
			<div class="bfi-hide" id="bfi_childrenagesmsg<?php echo $currModID ?>">
				<div style="line-height:0; height:0;"></div>
				<div class="bfi-pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover('destroy');"></i></div>
				<?php echo sprintf(__('We preset your children\'s ages to %s years old - but if you enter their actual ages, you might be able to find a better price.', 'bfi'),COM_BOOKINGFORCONNECTOR_CHILDRENSAGE) ?>
			</div>
			<input type="hidden" name="availabilitytype" class="resbynighthd" value="1" id="hdAvailabilityType<?php echo $checkoutId; ?>" /><br />
			<input type="hidden" name="itemtypes" class="itemtypeshd" value="0" id="hdItemTypes<?php echo $checkoutId; ?>" /><br />
			<input type="hidden" name="groupresulttype" class="groupresulttypehd" value="1" id="hdSearchGroupby<?php echo $checkoutId; ?>" /><br />

		</form>
				   
        </div>
<?php endif;  ?>
<?php if(!empty($tablistRealEstate)): ?>
		<div id="bfisearchselling<?php echo $currModID ?>" class="tab-pane fade <?php echo (empty($tablistResources)) ?"active in": "" ?>">
		<form action="<?php echo $url_page_RealEstate; ?>" method="get" id="searchformonsellunit<?php echo $currModID ?>" class=" ">			
			<div  id="searchBlock<?php echo $currModID ?>" class="bfi_form_<?php echo $showdirection?"horizontal":"vertical"; ?> <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<?php if($showContract){ ?>
				<div class="bfi_contracttypeid bfi_container" >
					<label><?php _e('Contract', 'bfi') ?></label>
					<select name="contractTypeId" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="inputtotal">
								<?php echo $listcontractType; ?>
					</select>
				</div><!--/span-->
				<?php } //$showContract ?>
				<?php if($showLocation){ ?>
				<div class="bfi_listlocations bfi_container">
					<label><?php _e('Province', 'bfi') ?></label>
					<select id="cityId<?php echo $currModID ?>" name="cityId" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="inputtotal">
						<?php echo $listlocations; ?>
					</select>
				</div>
				<div class="bfi_listlocations bfi_container">
					<div id="btnZones<?php echo $currModID ?>">
						<input type="radio" name="searchType" id="zoneSearch<?php echo $currModID ?>" value="0" <?php echo $searchType=="0"? "checked":""; ?> />
						<label for="zoneSearch<?php echo $currModID ?>" id="lblzoneSearch<?php echo $currModID ?>"  class="lblcheckzone"><?php _e('All province', 'bfi') ?></label>
					</div>
					<?php if($showMapIcon){ ?>
					<div onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)" >
						<input type="radio" name="searchType" id="mapSearch<?php echo $currModID ?>" value="1" <?php echo $searchType=="1"? "checked":""; ?> />
						<label id="lblmapSearch<?php echo $currModID ?>"  class="lblcheckzone"><?php _e('Map', 'bfi') ?></label>
					</div>
					<input id="zoneIds<?php echo $currModID ?>" name="zoneIds" type="hidden" value="<?php echo $zoneIds; ?>" />
					<?php } //$showMapIcon ?>
				</div><!--/span-->
				<?php } //$showLocation ?>
				<?php if(!empty($listunitCategoriesRealEstate) && $showAccomodations){ ?>
				<div class="bfi_unitCategoryId bfi_container">
					<label><?php _e('Type', 'bfi') ?></label>
					<select name="unitCategoryId" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="inputtotal">
						<?php echo $listunitCategoriesRealEstate; ?>
					</select>
				</div><!--/span-->
				<?php } //$listunitCategoriesRealEstate ?>
				<?php if($showMaxPrice){ ?>
				<div class="bfi_price bfi_container">
					<label><?php _e('Price', 'bfi') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="pricemin" type="text" placeholder="<?php _e('from', 'bfi') ?>" value="<?php echo $pricemin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="pricemax" type="text" placeholder="<?php _e('to', 'bfi') ?>" value="<?php echo $pricemax;?>"  class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showMaxPrice ?>
				<?php if($showMinFloor){ ?>
				<div class="bfi_floor_area  bfi_container">
					<label><?php _e('Floor area m&sup2;', 'bfi') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="areamin" type="text" placeholder="<?php _e('from', 'bfi') ?>" value="<?php echo $areamin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="areamax" type="text" placeholder="<?php _e('to', 'bfi') ?>" value="<?php echo $areamax;?>" class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showMinFloor ?>
				<?php if($showBedRooms){ ?>
				<div class="bfi_bedrooms  bfi_container">
					<label><?php _e('Bedrooms', 'bfi') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="bedroomsmin" type="text" placeholder="<?php _e('from', 'bfi') ?>" value="<?php echo $bedroomsmin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="bedroomsmax" type="text" placeholder="<?php _e('to', 'bfi') ?>" value="<?php echo $bedroomsmax;?>" class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showBedRooms ?>
				<?php if($showRooms){ ?>
				<div class="bfi_rooms  bfi_container">
					<label><?php _e('Rooms', 'bfi') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="roomsmin" type="text" placeholder="<?php _e('from', 'bfi') ?>" value="<?php echo $roomsmin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="roomsmax" type="text" placeholder="<?php _e('to', 'bfi') ?>" value="<?php echo $roomsmax;?>" class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showRooms ?>
				<?php if($showBaths){ ?>
				<div class="bfi_bathrooms  bfi_container">
					<label><?php _e('Bathrooms', 'bfi') ?></label>
					<select name="baths" onchange="changeBaths(this);" class="inputtotal">
					<?php foreach ($baths as $key => $value):?>
						<option value="<?php echo $key ?>" <?php selected( $bathsmin ."|". $bathsmax, $key ); ?>><?php echo $value ?></option>
					<?php endforeach; ?>
					</select>
					<input name="bathsmin" type="hidden" placeholder="<?php _e('from', 'bfi') ?>" value="<?php echo $bathsmin;?>" class="inputtext" > 
					<input name="bathsmax" type="hidden" placeholder="<?php _e('to', 'bfi') ?>" value="<?php echo $bathsmax;?>" class="inputtext" > 
				</div><!--/span-->
				<?php } //$showBaths ?>
				<?php if (isset($listServices) && $showServicesList) :?>
				<?php  $countServ=0;?>
				<div class="bfi_listservices  bfi_container">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<?php foreach ($listServices as $singleService):?>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<?php $checked = '';
								if (isset($filtersServices) &&  is_array($filtersServices) && in_array($singleService->ServiceId,$filtersServices)){
									$checked = ' checked="checked"';
								}
							?>
								<label class="checkbox"><input type="checkbox" name="services"  class="checkboxservices" value="<?php echo ($singleService->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($singleService->Name, $language) ?></label>
							</div>
						<?php  $countServ++;
						if($countServ%2==0):?>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">	
						<?php endif ?>

						<?php endforeach; ?>
					</div>
				</div><!--/span-->
				<?php endif ?>
				<?php if($showOnlyNew){ ?>
				<div class="bfi_isnewbuilding  bfi_container">  
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<label class="checkbox"><input type="checkbox" name="isnewbuilding" value="1" <?php echo $isnewbuilding ?> /><?php _e('Only new building', 'bfi') ?></label>
						</div>
					</div>
				</div><!--/span-->
				<?php } ?>

				<div id="searchButtonArea<?php echo $currModID ?>" class=" bfi_container">
					<div class="" id="divBtnRealEstate">
						&nbsp;<br />
						<a  id="BtnRealEstate<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);"  style="width:100%;"><i class="fa fa-search" aria-hidden="true"></i> <?php _e('Search', 'bfi') ?></a>
					</div>
				</div>

			</div><!--/span-->

			<div id="zonePopup<?php echo $currModID ?>" class="zone-dialog" style="width:100%">
				<div class="dialog-header">
					<div class="header-content">
						<?php _e('Select', 'bfi') ?>
						<div class="bfi-pull-right dialog-closer">x</div>
						<div class="bfi-clearfix"></div>
					</div>
				</div>
				<div class="dialog-container">
					<div class="dialog-content">
						<?php if ($cityId >= -1): ?>
						<select id="zoneIdsList<?php echo $currModID ?>" name="zoneIdsList" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="select90percent multiselect" multiple="multiple">
							<?php echo $listzoneIds ?>
						</select>				
						<?php endif ?>
					</div>
				</div>
			</div>
			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" value="3" name="searchtypetab" />
			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
			<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell<?php echo $currModID ?>" />
			<input type="hidden" name="availabilitytype" class="resbynighthd" value="1" id="hdAvailabilityType<?php echo $checkoutId; ?>" /><br />

		</form>
		</div>  <!-- role="tabpanel" -->
<?php endif;  ?>
    </div>
</div>

<?php echo $after_widget; ?>

<script type="text/javascript">
var $dialog;
var currentLocation=0;
$Lng = <?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSX?>;
$Lat = <?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSY?>;
$googlemapsapykey = '<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY?>';
$startzoom = <?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM?>;

var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
	//-->
	</script>

<?php if(!empty($tablistRealEstate)): ?>
<script type="text/javascript">


function checkSelSearchOnsell<?php echo $currModID ?>(){
	var vals=[];
	jQuery("#zonePopup<?php echo $currModID ?> .multiselect option:selected").each(function(i,selected){
		vals[i]=jQuery(this).val();
	});
	jQuery("#zoneIds<?php echo $currModID ?>").val(vals.toString());
	if(jQuery("#cityId<?php echo $currModID ?>").val()!= 0 && jQuery("#cityId<?php echo $currModID ?>").val()>=-1 && vals.length>0){
		jQuery("#lblzoneSearch<?php echo $currModID ?>").text(vals.length+" <?php _e('Destination', 'bfi') ?>");
	} else if(jQuery("#cityId<?php echo $currModID ?>").val()!= 0 && jQuery("#cityId<?php echo $currModID ?>").val()>=-1) {
		jQuery("#lblzoneSearch<?php echo $currModID ?>").text("<?php _e('Please select', 'bfi') ?>");
	} else {
		jQuery("#lblzoneSearch<?php echo $currModID ?>").text("<?php _e('All province', 'bfi') ?>");
	}
	currentLocation=parseInt(jQuery("#cityId<?php echo $currModID ?>").val());
	if(currentLocation<-1){
		jQuery('#btnZones<?php echo $currModID ?>').attr("onclick","");
		jQuery('#btnZones<?php echo $currModID ?>').attr("href","");
		if(jQuery("#points").val() ==""){
			openGoogleMapBFSSell();
		}
	} else if(currentLocation>=-1){
		jQuery('#btnZones<?php echo $currModID ?>').attr("onclick","javascript:openZonesPopup();");
		jQuery('#btnZones<?php echo $currModID ?>').attr("href","javascript:void(0);");
	}else{
		jQuery('#btnZones<?php echo $currModID ?>').attr("onclick","");
		jQuery('#btnZones<?php echo $currModID ?>').attr("href","");
	}
}

function openZonesPopup(){
	jQuery('#zonePopup<?php echo $currModID ?>').show();
}

function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

function changeBaths(currObj){
	var bathsselect = jQuery(currObj).val();
	var vals = bathsselect.split("|"); 
	var closestDiv = jQuery(currObj).closest("div");
	closestDiv.find("input[name='bathsmin']").first().val(vals[0]);
	closestDiv.find("input[name='bathsmax']").first().val(vals[1]);
}


function getBottomPosition(elm){
	return jQuery(window).height() - top - elm.height();
}

function resizeZoneTitle(){
	if(jQuery(window).width()>=600){
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').css("line-height",jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9').height()+"px");
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 label').css("line-height",jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9').height()+"px");
	} else{
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').css("line-height","");
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 label').css("line-height","");
	}
			jQuery("#zonePopup<?php echo $currModID ?>").css({
				left:jQuery("#searchBlock<?php echo $currModID ?>").offset().left-jQuery("#searchBlock<?php echo $currModID ?>").offset().left+15
			});
			
			if ((jQuery("#searchBlock<?php echo $currModID ?>").height()-jQuery(window).height())>0) {
				jQuery("#zonePopup<?php echo $currModID ?>").css({
					bottom:80+(jQuery("#searchBlock<?php echo $currModID ?>").height()-jQuery(window).height())
				});
			} else {
				jQuery("#zonePopup<?php echo $currModID ?>").css({
					height:"",
					bottom:80
				});
			}
			
}

jQuery(window).resize(function() {
	resizeZoneTitle();
});

jQuery(function($)
		{
			resizeZoneTitle();
			jQuery(".zone-dialog .dialog-closer").click(function(e){
				jQuery(this).closest(".zone-dialog").hide();
			});
			jQuery('.btn-radio input').click(function(e){
				//if($dialog==null){
					var searchBoxOffset=jQuery("#searchBlock<?php echo $currModID ?>").offset();
					var width=jQuery("#searchBlock<?php echo $currModID ?>").width();
					var height=(jQuery("#searchButtonArea<?php echo $currModID ?>").offset().top)-(searchBoxOffset.top);
//					console.log(searchBoxOffset.top);
			});
			jQuery('#BtnRealEstate<?php echo $currModID ?>').click(function(e) {
				e.preventDefault();
				jQuery("#searchformonsellunit<?php echo $currModID ?>").submit(); 
			});
			
			var searchBoxOffset=jQuery("#searchBlock<?php echo $currModID ?>").offset();
			var width=jQuery("#searchBlock<?php echo $currModID ?>").width();
			var height=(jQuery("#searchButtonArea<?php echo $currModID ?>").offset().top)-(searchBoxOffset.top);
			jQuery('#zonePopup<?php echo $currModID ?> .multiselect').multiSelectToCheckboxes();
			
			checkSelSearchOnsell<?php echo $currModID ?>();
			jQuery('.checkboxservices').on('click',function() {
				updateHiddenValue('.checkboxservices:checked','#servicesonsell<?php echo $currModID ?>')	
			});
			
			jQuery("#btnconfirm").click(function(e){
				jQuery("#lblmapSearch<?php echo $currModID ?>").text("<?php _e('Map', 'bfi') ?>");
			});
			
			
			jQuery("#cityId<?php echo $currModID ?>").change(function(){
				var searchBoxOffset=jQuery("#searchBlock<?php echo $currModID ?>").offset();
				var width=jQuery("#searchBlock<?php echo $currModID ?>").width();
				var height=(jQuery("#searchButtonArea<?php echo $currModID ?>").offset().top)-(searchBoxOffset.top);
				if(jQuery(this).val()>=-1){
					jQuery('#zonePopup<?php echo $currModID ?> .dialog-content').empty();
					var queryL = "task=getLocationZone&locationId=" + jQuery("#cityId<?php echo $currModID ?>").val();
					jQuery.post(urlCheck, queryL, function(result) {
							var select=jQuery("<select>");
							select.addClass("multiselect");
							select.attr('onchange','checkSelSearchOnsell<?php echo $currModID ?>();');
							select.attr("multiple","multiple");
							jQuery(result).each(function(i,itm){
								var opt=jQuery("<option>");
								opt.val(itm.LocationZoneID);
								opt.text(itm.Name);
								select.append(opt);
							});
							jQuery("#zonePopup<?php echo $currModID ?> .dialog-content").append(select);
							select.multiSelectToCheckboxes();

					},'json');
				} 
			}); 
			

			jQuery("#searchformonsellunit<?php echo $currModID ?>").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {


                        alert(validator.errorList[0].message);

                        validator.errorList[0].element.focus();
                    }
                },
		        rules:
		        {
		        	pricemin:
					{
		                required: false,
		                digits: true
		            },
		        	pricemax:
					{
		                required: false,
		                digits: true
		            },
		            areamin:
		            {
		                required: false,
		                digits: true
		            },
		            areamax:
		            {
		                required: false,
		                digits: true
		            },
		            roomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            roomsmax:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            }
		        },
		        messages:
		        {
		        	pricemin: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	pricemax: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	areamin: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	areamax: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	roomsmin: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	roomsmax: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	bedroomsmin: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		},
		        	bedroomsmax: {
		        		required:"<?php _e('Mandatory', 'bfi') ?>",
		        		digits:"<?php _e('Please enter a integer number', 'bfi') ?>"
		        		}
		        },
				highlight: function(label) {
			    },
			    success: function(label) {
					jQuery(label).remove();
			    },
				submitHandler: function(form) {
						msg1 = "<?php _e('We\'re processing your request', 'bfi') ?>";
						msg2 = "<?php _e('Please be patient', 'bfi') ?>";
						jQuery("#zonePopup<?php echo $currModID ?>").hide();

						bookingfor.waitBlockUI(msg1, msg2,img1); 
						jQuery("#BtnRealEstate<?php echo $currModID ?>").hide();
						form.submit();
				}

		    });

		});
		
		

jQuery.fn.multiselect = function() {
    jQuery(this).each(function() {
        var checkboxes = jQuery(this).find("input:checkbox");
        checkboxes.each(function() {
            var checkbox = jQuery(this);
            // Highlight pre-selected checkboxes
            if (checkbox.prop("checked"))
                checkbox.parent().addClass("multiselect-on");
 
            // Highlight checkboxes that the user selects
            checkbox.click(function() {
                if (checkbox.prop("checked"))
                    checkbox.parent().addClass("multiselect-on");
                else
                    checkbox.parent().removeClass("multiselect-on");
            });
        });
    });
};

var methods = {
        init: function() {
            var $ul = jQuery("<ul/>").insertAfter(this);
			$ul.addClass(jQuery(this).attr("class"));
            var baseId = "_" + jQuery(this).attr("id");
            jQuery(this).children("option").each(function(index) {
                var $option = jQuery(this);
                var id = baseId + index;
                var $li = jQuery("<li/>").appendTo($ul);
                var $label = jQuery("<label for='" + id + "' class='aligncheckbox' >" + $option.text() + "</label>").appendTo($li);
				var $checkbox = jQuery("<input type='checkbox' id='" + id + "'/>").prependTo($label).change(function() {
                    if (jQuery(this).is(":checked")) {
                        $option.attr("selected", "selected");
                    } else {
                        $option.removeAttr("selected");
                    }
					checkSelSearchOnsell<?php echo $currModID ?>();
                });
                if ($option.is(":selected")) {
                    $checkbox.attr("checked", "checked");
                }

//                $checkbox.after("<label for='" + id + "' style='display:inline;'>" + $option.text() + "</label>");
            });
            jQuery(this).hide();
        }
    };

    jQuery.fn.multiSelectToCheckboxes = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.multiSelectToCheckboxes');
        }

    };

	//-->
	</script>
	<div id="divBFSSell" style="width:100%; height:400px; display:none;">
		<div style="width:100%; height:50px; position:relative;">
			<?php _e('Draw area', 'bfi') ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>"> 
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<ul class="nav nav-pills">
						<li><a class="btn select-figure" id="btndrawpoligon" onclick="javascript: drawPoligon()"><?php _e('Area', 'bfi') ?></a></li>
						<li><a class="btn select-figure" id="btndrawcircle" onclick="javascript: drawCircle()"><?php _e('Circle', 'bfi') ?></a></li>
					</ul>				
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6 text-right">
					<input type="text" id="addresssearch" placeholder="<?php _e('Search', 'bfi') ?>" />
					<div id="btnCompleta" class="input-prepend input-append" style="display:none;">
						<a class="btn btn-delete" id="btndelete" href="javascript: void(0);" ><?php _e('Reset', 'bfi') ?></a>
						<a class="btn" id="btnconfirm" type="button" href="javascript: void(0);" ><?php _e('Submit', 'bfi') ?></a>
						<span class="add-on" id="spanArea"></span>
					</div>
				
				</div><!--/span-->
			</div>

		</div>
		<div id="map_canvasBFSSell" class="map_canvasBFSSell" style="width:100%; height:350px;"></div>
		<div class="map-tooltip"><strong><?php _e('Click on map and choose your area.', 'bfi') ?></strong></div>
</div>
<?php endif;  ?>

<script type="text/javascript">
<!--
var img1 = new Image(); 
var localeSetting = "<?php echo substr($language,0,2); ?>";
function insertNight<?php echo $currModID ?>(){
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);

		var resbynight = jQuery(jQuery('#<?php echo $checkinId; ?>')).closest("form").find(".resbynighthd").first();
		var resbynight_str = resbynight.val().split(",");
		if(resbynight_str.indexOf("0") !== -1 || resbynight_str.indexOf("1") !== -1 ){
			jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			jQuery('#calendarnightselect<?php echo $currModID ?>').val(1);
			if (jQuery(resbynight).val() == 0) {
				jQuery('#calendarnightselect<?php echo $currModID ?>').val(0);
				days += 1;
			}
			jQuery('#calendarnight<?php echo $durationId ?>').html(days);
		}


}
                
function insertCheckinTitle<?php echo $currModID ?>() {
	setTimeout(function() {
		jQuery("#ui-datepicker-div").addClass("checkin");
		jQuery("#ui-datepicker-div").removeClass("checkout");
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));
		month1 = ('0' + d1[1]).slice(-2);
		month2 = ('0' + d2[1]).slice(-2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			month1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });              
			month2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });            
		}

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		var strSummary = 'Check-in '+('0' + from.getDate()).slice(-2)+' '+ month1;
		
		var resbynight = jQuery(jQuery('#<?php echo $checkinId; ?>')).closest("form").find(".resbynighthd").first();
		var strSummaryDays = "(" +days+" <?php echo strtolower (__('Nights', 'bfi')) ?>)";
		if (jQuery(resbynight).val() == 0) {
			days += 1;
			strSummaryDays ="(" +days+" <?php echo strtolower (__('Days', 'bfi')) ?>)";
		}
		var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.active a[data-toggle="tab"]').first();
		var target = jQuery(currTab).attr("class");
        if (target == "searchResources" || target == "searchServices") {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
        }
		jQuery('#ui-datepicker-div').attr('data-before',strSummary);

//		jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "short" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "short" })+' '+d2[2]+' (soggiorno di '+days+' notti)');
	}, 1);
		
}
function insertCheckoutTitle<?php echo $currModID ?>() {
	setTimeout(function() {
		jQuery("#ui-datepicker-div").addClass("checkout");
		jQuery("#ui-datepicker-div").removeClass("checkin");
		var resbynight = jQuery(jQuery('#<?php echo $checkinId; ?>')).closest("form").find(".resbynighthd").first();
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));
		month1 = ('0' + d1[1]).slice(-2);
		month2 = ('0' + d2[1]).slice(-2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			month1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });              
			month2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });            
		}

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		var strSummary = 'Check-in '+('0' + from.getDate()).slice(-2)+' '+ month1;
		var strSummaryDays = "(" +days+" <?php echo strtolower (__('Nights', 'bfi')) ?>)";
		if (jQuery(resbynight).val() == 0) {
			days += 1;
			strSummaryDays ="(" +days+" <?php echo strtolower (__('Days', 'bfi')) ?>)";
		}
		var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.active a[data-toggle="tab"]').first();
		var target = jQuery(currTab).attr("class");
        if (target == "searchResources" || target == "searchServices") {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
        }
		jQuery('#ui-datepicker-div').attr('data-before',strSummary);
//		jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "long" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "long" })+' '+d2[2]+' (soggiorno di '+days+' notti)');
	}, 1);
}

function closed<?php echo $currModID ?>(date) {
	var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
	var strDate = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth()+1)).slice(-2) + "/" + date.getFullYear();

	var d1 = checkindate.split("/");
	var d2 = checkoutdate.split("/");
	var c = strDate.split("/");

	var from = new Date(d1[2], d1[1]-1, d1[0]);
	var to   = new Date(d2[2], d2[1]-1, d2[0]);
	var check = new Date(c[2], c[1]-1, c[0]);
	var daysToDisable = <?php echo $blockdays;?>;
	var monthsToDisable = <?php echo $blockmonths;?>;
	var day = date.getDay();
	var dayEnabled = true
	if (jQuery.inArray(day, daysToDisable) != -1) {
		dayEnabled = false;
	}

	var month = date.getMonth()+1;
	if (jQuery.inArray(month, monthsToDisable) != -1) {
		dayEnabled = false;
	}

	arr = [dayEnabled, ''];  
	if(check.getTime() == from.getTime()) {

		arr = [dayEnabled, 'date-start-selected', 'date-selected'];
	}
	if(check.getTime() == to.getTime()) {

		arr = [dayEnabled, 'date-end-selected', 'date-selected'];  
	}
	if(check > from && check < to) {
		arr = [dayEnabled, 'date-selected', 'date-selected'];
	}
	return arr;
}

function printChangedDate<?php echo $currModID ?>(date, elem) {
	var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

	var d1 = checkindate.split("/");
	var d2 = checkoutdate.split("/");

	var from = new Date(d1[2], d1[1]-1, d1[0]);
	var to   = new Date(d2[2], d2[1]-1, d2[0]);

	day1  = ('0' + from.getDate()).slice(-2),  
	month1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" }),              
	year1 =  from.getFullYear(),
	weekday1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { weekday: "short" });

	day2  = ('0' + to.getDate()).slice(-2),  
	month2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" }),              
	year2 =  to.getFullYear(),
	weekday2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { weekday: "short" });

	jQuery('.checkinli<?php echo $currModID ?>').find('.day span').html(day1);
	jQuery('.checkoutli<?php echo $currModID ?>').find('.day span').html(day2);
	if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
		jQuery('.checkinli<?php echo $currModID ?>').find('.monthyear p').html(weekday1 + "<br />" + month1+" "+year1); 
		jQuery('.checkoutli<?php echo $currModID ?>').find('.monthyear p').html(weekday2 + "<br />" + month2+" "+year2);
		jQuery('#bfi_lblchildrenagesat<?php echo $currModID ?>').html("<?php echo strtolower (__('on', 'bfi')) ?> " + day2 + " " + month2 + " " + year2);
	} else {
		jQuery('.checkinli<?php echo $currModID ?>').find('.monthyear p').html(d1[1]+"/"+d1[2]);  
		jQuery('.checkoutli<?php echo $currModID ?>').find('.monthyear p').html(d2[1]+"/"+d2[2]);
		jQuery('#bfi_lblchildrenagesat<?php echo $currModID ?>').html("<?php echo strtolower (__('on', 'bfi')) ?> " + day2 + " " + d2[1] + " " + d2[2]);
	}
}

function checkDate<?php echo $checkinId; ?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate());
	jQuery("#<?php echo $checkoutId; ?>").datepicker("option", "minDate", d);
}

function checkSelSearch<?php echo $currModID ?>() {
	var sel = jQuery("#merchantCategoryId<?php echo $currModID ?>")
	if (sel.val()==="0")
	{
		sel.addClass("mod_bookingforsearcherror");
	}else{
		sel.removeClass("mod_bookingforsearcherror");
	}
}

function checkChildrenSearch<?php echo $currModID ?>(nch,showMsg) {
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").hide();
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").hide();
	if (nch > 0) {
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").each(function(i) {
			if (i < nch) {
				var id=jQuery(this).attr('id');
				jQuery(this).css('display', 'inline-block');
//				jQuery(this).show();
			}
		});
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").show();
		if(showMsg===1) { 
			showpopover<?php echo $currModID ?>();
		}
	}
//	if (jQuery.prototype.masonry){
//		jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
//	}

}
jQuery(function() {
//	jQuery("#navbookingforsearch<?php echo $currModID ?> li[data-searchtypeid=<?php echo $searchtypetab ?>] a[data-toggle=tab]").tab("show");
	jQuery("#bfisearch<?php echo $currModID ?>").tabs();
	var index = jQuery('#bfisearch<?php echo $currModID ?> li[data-searchtypeid="<?php echo $searchtypetab ?>"] a').parent().index();
	jQuery("#bfisearch<?php echo $currModID ?>").tabs("option", "active", index);
	


	jQuery("#<?php echo $checkinId; ?>").datepicker({
		defaultDate: "+2d"
		,dateFormat: "dd/mm/yy"
		, numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
		, minDate: '+0d'
		, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); insertNight<?php echo $currModID ?>() }
		, beforeShow: function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckinTitle<?php echo $currModID ?>(); }
		, onChangeMonthYear: function(dateText, inst) { insertCheckinTitle<?php echo $currModID ?>(); }
		, showOn: "button"
		, beforeShowDay: closed<?php echo $currModID ?>
		, buttonText: "<div class='buttoncalendar checkinli<?php echo $currModID; ?> checkinli_div'><div class='dateone day '><span><?php echo $checkin->format("d") ;?></span></div><div class='dateone daterwo monthyear first_monthyear'><p><?php echo date_i18n('D',$checkin->getTimestamp());?><br /><?php echo date_i18n('M',$checkin->getTimestamp());?> <?php echo $checkin->format("Y"); ?> </p></div></div>"
		, onSelect: function(date) { checkDate<?php echo $checkinId; ?>(jQuery, jQuery(this), date); printChangedDate<?php echo $currModID ?>(date, jQuery(this)); }
		, firstDay: 1
	});
	jQuery("#<?php echo $checkoutId; ?>").datepicker({
		defaultDate: "+2d"
		,dateFormat: "dd/mm/yy"
		, numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
		, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); insertNight<?php echo $currModID ?>();  }
		, beforeShow: function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckoutTitle<?php echo $currModID ?>(); }
		, onSelect: function(date) { printChangedDate<?php echo $currModID ?>(date, jQuery(this)); }
		, onChangeMonthYear: function(dateText, inst) { insertCheckoutTitle<?php echo $currModID ?>(); }
		, minDate: '+0d'
		, showOn: "button"
		, beforeShowDay: closed<?php echo $currModID ?>, buttonText: "<div class='buttoncalendar checkoutli<?php echo $currModID; ?> checkoutli_div'><div class='dateone day lastdate'><span><?php echo $checkout->format("d"); ?></span></div><div class='dateone daterwo monthyear last_monthyear'><p><?php echo date_i18n('D',$checkout->getTimestamp());?><br /><?php echo date_i18n('M',$checkout->getTimestamp());?> <?php echo $checkout->format("Y"); ?> </p></div></div>"
		, firstDay: 1
	});

	jQuery('#BtnResource<?php echo $currModID ?>').click(function(e) {
		e.preventDefault();
		jQuery("#searchform<?php echo $currModID ?>").submit(); 
	});
	jQuery("#searchform<?php echo $currModID ?>").validate(
	{
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				validator.errorList[0].element.focus();
			}
		},
//		rules:
//		{
//			merchantCategoryId: {
//				notEqual: "0"
//				},
//			masterTypeId: {
//				notEqual: "0"
//				},
//		},
//		messages:
//		{
//			merchantCategoryId: {
//				notEqual:"<?php _e('Mandatory', 'bfi') ?>",
//				},
//			masterTypeId: {
//				notEqual:"<?php _e('Mandatory', 'bfi') ?>",
//				},
//		},
		highlight: function(label) {
		},
		success: function(label) {
			jQuery(label).remove();
		},
		submitHandler: function(form) {
				msg1 = "<?php _e('We\'re processing your request', 'bfi') ?>";
				msg2 = "<?php _e('Please be patient', 'bfi') ?>";
				bookingfor.waitBlockUI(msg1, msg2,img1); 
				jQuery("#BtnResource<?php echo $currModID ?>").hide();
				form.submit();
		}

	});
	showhideCategories<?php echo $currModID ?>();
	insertNight<?php echo $currModID ?>();
	checkChildrenSearch<?php echo $currModID ?>(<?php echo $nch ?>,<?php echo $showChildrenagesmsg ?>);
	jQuery("#mod_bookingforsearch-children<?php echo $currModID ?> select[name='children']").change(function() {
		checkChildrenSearch<?php echo $currModID ?>(jQuery(this).val(),0);
	});
	jQuery(".btnservices<?php echo $currModID ?>").click(function(e) {
		e.preventDefault();
		jQuery(this).toggleClass("active");
		var active_keys = [];
		active_keys = jQuery(".btnservices.active.btnservices<?php echo $currModID ?>").map(function(index, value){
			return jQuery(value).attr('rel');
		});
		 jQuery("#filtersServicesSearch<?php echo $currModID ?>").val(jQuery.unique(active_keys.toArray()).join(","));
	});
});

function countPersone<?php echo $currModID ?>() {
	jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover("hide");
	var numAdults = new Number(jQuery("#searchform<?php echo $currModID ?> select[name='adults']").val() || 0);
	var numSeniores = new Number(jQuery("#searchform<?php echo $currModID ?> select[name='seniores']").val() || 0);
	var numChildren = new Number(jQuery("#mod_bookingforsearch-children<?php echo $currModID ?> select[name='children']").val() || 0);
	checkChildrenSearch<?php echo $currModID ?>(numChildren,0);
	jQuery('#searchformpersons<?php echo $currModID ?>').val(numAdults + numChildren + numSeniores);
	jQuery('#showmsgchildage<?php echo $currModID ?>').val(0);
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select:visible option:selected").each(function(i) {
		if(jQuery(this).text()==""){
			jQuery('#showmsgchildage<?php echo $currModID ?>').val(1);
			return;
		}
	});
}

function quoteChanged<?php echo $currModID ?>() {
	countPersone<?php echo $currModID ?>();
}
function showpopover<?php echo $currModID ?>() {
		jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover({
			content : jQuery("#bfi_childrenagesmsg<?php echo $currModID ?>").html(),
			container: document.body,
			cache: false,
			placement:"auto-bottom",
			maxWidth: "300px",
			type:'html'
		});
		jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover("show");
}
jQuery(window).resize(function(){
	jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover("hide");
});

function showhideCategories<?php echo $currModID ?>() {
//	var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.active a[data-toggle="tab"]').first();
	var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.ui-tabs-active a[data-toggle="tab"]').first();
    var target = jQuery(currTab).attr("class");

	var merchantCategoriesResource =  <?php echo json_encode($merchantCategoriesResource) ?> ;

	var merchantCategoriesSelectedBooking = [<?php echo implode(',', $merchantCategoriesSelectedBooking) ?>];
    var merchantCategoriesSelectedActivities = [<?php echo implode(',', $merchantCategoriesSelectedActivities) ?>];

	var unitCategoriesResource = <?php echo json_encode($unitCategoriesResource) ?>;
	
	var unitCategoriesSelectedBooking = [<?php echo implode(',', $unitCategoriesSelectedBooking) ?>];
    var unitCategoriesSelectedActivities = [<?php echo implode(',', $unitCategoriesSelectedActivities) ?>];
	
	var currentMerchantCategoriesSelected = jQuery("#merchantCategoryId<?php echo $currModID ?>").val()?jQuery("#merchantCategoryId<?php echo $currModID ?>").val():0;
	var currentUnitCategoriesSelected = jQuery("#masterTypeId<?php echo $currModID ?>").val()?jQuery("#masterTypeId<?php echo $currModID ?>").val():0;

	jQuery("#merchantCategoryId<?php echo $currModID ?>").val(0);
	jQuery("#masterTypeId<?php echo $currModID ?>").val(0);
	
	var currMerchantCategory = jQuery("#merchantCategoryId<?php echo $currModID ?>");
	currMerchantCategory.find('option:gt(0)').remove().end();
	var currUnitCategory = jQuery("#masterTypeId<?php echo $currModID ?>");
	currUnitCategory.find('option:gt(0)').remove().end();

	var resbynight = jQuery(jQuery(currTab).attr("href")).find(".resbynighthd").first();
	var availabilityTypesSelectedBooking = '<?php echo implode(',', $availabilityTypesSelectedBooking) ?>';
	var availabilityTypesSelectedActivities = '<?php echo implode(',', $availabilityTypesSelectedActivities) ?>';
//	var currentavailabilityTypesSelected = resbynight.val()?resbynight.val():1;

	var itemTypes = jQuery(jQuery(currTab).attr("href")).find(".itemtypeshd").first();
	var itemTypesSelectedBooking = '<?php echo implode(',', $itemTypesSelectedBooking) ?>';
	var itemTypesSelectedActivities = '<?php echo implode(',', $itemTypesSelectedActivities) ?>';
	var currentitemTypesSelected = itemTypes.val()?itemTypes.val():0;

	var groupResultType = jQuery(jQuery(currTab).attr("href")).find(".groupresulttypehd").first();
	var groupBySelectedBooking = '<?php echo implode(',', $groupBySelectedBooking) ?>';
	var groupBySelectedActivities = '<?php echo implode(',', $groupBySelectedActivities) ?>';
	var currentgroupResultTypeSelected = groupResultType.val()?groupResultType.val():1;

	if (currTab.hasClass("searchResources")) {		
//		jQuery("#divcheckoutsearch<?php echo $currModID ?>").css("display", "inline-block");
		if(availabilityTypesSelectedBooking.length>0){
			resbynight.val(availabilityTypesSelectedBooking);
			if((availabilityTypesSelectedBooking =="0" || availabilityTypesSelectedBooking =="1" || availabilityTypesSelectedBooking =="0,1" ) ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			}
		}


		jQuery("#searchtypetab<?php echo $currModID ?>").val("0");
		var d = jQuery('#<?php echo $checkinId; ?>').datepicker('getDate');
		if (jQuery(resbynight).val() == 1) {
			d.setDate(d.getDate() + 1);
		}
		jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "minDate", d);
		jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "maxDate", Infinity);
		if (jQuery('#<?php echo $checkoutId; ?>').datepicker("getDate") <= d) {
			jQuery('#<?php echo $checkoutId; ?>').datepicker("setDate", Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
		}

		if(itemTypesSelectedBooking.length>0){
			itemTypes.val(itemTypesSelectedBooking);
		}
		if(groupBySelectedBooking.length>0){
			groupResultType.val(groupBySelectedBooking);
		}
		if(merchantCategoriesSelectedBooking.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedBooking.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedBooking[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedBooking[i]));
            }
			currMerchantCategory.find('option:eq(0)').val('<?php echo implode(',', $merchantCategoriesSelectedBooking) ?>');

		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
			currMerchantCategory.find('option:eq(0)').val(0);
		}
		if(unitCategoriesSelectedBooking.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedBooking.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedBooking[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedBooking[i]));
            }
			currUnitCategory.find('option:eq(0)').val('<?php echo implode(',', $unitCategoriesSelectedBooking) ?>');
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
			currUnitCategory.find('option:eq(0)').val(0);
		}
		if(jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedBooking) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedBooking) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	if (currTab.hasClass("searchTimeSlots")) {
//		jQuery("#divcheckoutsearch<?php echo $currModID ?>").css("display", "none");
		if(availabilityTypesSelectedActivities.length>0){
			resbynight.val(availabilityTypesSelectedActivities);
			if((availabilityTypesSelectedActivities =="0" || availabilityTypesSelectedActivities =="1"  || availabilityTypesSelectedActivities =="0,1" ) ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			}
		}
	   jQuery("#searchtypetab<?php echo $currModID ?>").val("2");

		if(itemTypesSelectedActivities.length>0){
			itemTypes.val(itemTypesSelectedActivities);
		}
		if(groupBySelectedActivities.length>0){
			groupResultType.val(groupBySelectedActivities);
		}
		if(merchantCategoriesSelectedActivities.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedActivities.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedActivities[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedActivities[i]));
            }
			currMerchantCategory.find('option:eq(0)').val('<?php echo implode(',', $merchantCategoriesSelectedActivities) ?>');
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
			currMerchantCategory.find('option:eq(0)').val(0);
		}
		if(unitCategoriesSelectedActivities.length>1){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedActivities.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedActivities[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedActivities[i]));
            }
			currUnitCategory.find('option:eq(0)').val('<?php echo implode(',', $unitCategoriesSelectedActivities) ?>');
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
			currUnitCategory.find('option:eq(0)').val(0);
		}
		if(jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedActivities) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedActivities) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	
	jQuery("#divcalendarnightsearch<?php echo $currModID ?>").hide();
	var resbynight_str = resbynight.val().split(",");
	if(resbynight_str.indexOf("0") !== -1 || resbynight_str.indexOf("1") !== -1 ){
		jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
	}

	if (currTab.hasClass("searchSelling")) {
		jQuery("#searchtypetab<?php echo $currModID ?>").val("3");
	}
}


    jQuery('#bfisearch<?php echo $currModID ?>').on('tabsactivate', function (event, ui) {
		insertNight<?php echo $currModID ?>();
		showhideCategories<?php echo $currModID ?>();
    })
 
//-->
</script>
