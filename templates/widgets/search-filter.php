<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(BFI()->isSearchPage()){

$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$currencyclass = bfi_get_currentCurrency();

//$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
//$formAction = get_permalink( $searchAvailability_page->ID );

$paymodes_text = array('freecancellation' => __('Free cancellation', 'bfi'),
						'freepayment' => __('No prepayment', 'bfi'),   
						'freecc' => __('Book without credit card', 'bfi')                                 
					);
$meals_text = array('ai' => __('All inclusive', 'bfi'),
						'fb' => __('Full board', 'bfi'),
						'hb' => __('Half board', 'bfi'),   
						'bb' => __('Breakfast included', 'bfi')                                 
					);
$rating_text = array('null' => __('Unrated', 'bfi'),
						'0' => __('Unrated', 'bfi'),
						'1' => __('1 star', 'bfi'),   
						'2' => __('2 stars', 'bfi'),
						'3' => __('3 stars', 'bfi'),
						'4' => __('4 stars', 'bfi'),
						'5' => __('5 stars', 'bfi'),  
						'6' => __('6 stars', 'bfi'),
						'7' => __('7 stars', 'bfi'),  
						'8' => __('8 stars', 'bfi'), 
						'9' => __('9 stars', 'bfi'),  
						'10' => __('10 stars', 'bfi'),                                 
					);
$avg_text = array('-1' => __('Unrated', 'bfi'),
						'0' => __('Very poor', 'bfi'),
						'1' => __('Poor', 'bfi'),   
						'2' => __('Disappointing', 'bfi'),
						'3' => __('Fair', 'bfi'),
						'4' => __('Okay', 'bfi'),
						'5' => __('Pleasant', 'bfi'),  
						'6' => __('Good', 'bfi'),
						'7' => __('Very good', 'bfi'),  
						'8' => __('Fabulous', 'bfi'), 
						'9' => __('Exceptional', 'bfi'),  
						'10' => __('Exceptional', 'bfi'),                                 
					);


$formAction = (isset($_SERVER['HTTPS']) ? "https" : "http") . ':' ."//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$page = bfi_get_current_page() ;
if(!empty($page)){
	$formAction = str_replace('/page/'.$page."/","/",$formAction);
}

$formAction = filter_input( INPUT_GET, 'newsearch' )
       ? remove_query_arg( 'newsearch', $formAction )
       : $formAction;

//$locationZones = BFCHelper::getLocationZones();
//$masterTypologies = BFCHelper::getMasterTypologies();
//$merchantGroups = BFCHelper::getTags($language,"1,4");
bfi_setSessionFromSubmittedData();

$pars = BFCHelper::getSearchParamsSession();

$newsearch = isset($pars['newsearch']) ? $pars['newsearch'] : '0';


if (empty($GLOBALS['bfSearched'])) {
	 if($newsearch == "1"){
		BFCHelper::setFilterSearchParamsSession(null);
		$searchmodel = new BookingForConnectorModelSearch;
		$items =  array();
		$total = 0;
		$totalAvailable = 0;
		$currSorting = "";
		$filterinsession = null;
		$start = 0;
		if (isset($pars['checkin']) && isset($pars['checkout'])){
			$now = new DateTime('UTC');
			$now->setTime(0,0,0);
			$checkin = isset($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
			$checkout = isset($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');
			$availabilitytype = isset($pars['availabilitytype']) ? $pars['availabilitytype'] : "1";
			
			$availabilitytype = explode(",",$availabilitytype);
			if (($checkin == $checkout && (!in_array("0",$availabilitytype) && !in_array("2",$availabilitytype)&& !in_array("3",$availabilitytype) ) ) || $checkin->diff($checkout)->format("%a") <0 || $checkin < $now ){
				$nodata = true;
			}else{
				$filterinsession = BFCHelper::getFilterSearchParamsSession();
				$items = $searchmodel->getItems(false, false, $start,COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
				
				$items = is_array($items) ? $items : array();
						
				$total=$searchmodel->getTotal();
				$totalAvailable=$searchmodel->getTotalAvailable();
				$currSorting=$searchmodel->getOrdering() . "|" . $searchmodel->getDirection();
			}

		}
		$GLOBALS['bfSearchedItems'] = $items;
		$GLOBALS['bfSearchedItemsTotal'] = $total;
		$GLOBALS['bfSearchedItemsTotalAvailable'] = $totalAvailable;
		$GLOBALS['bfSearchedItemsCurrSorting'] = $currSorting;
		$GLOBALS['bfSearched'] = 1;
	}else{
		$filtersselected = BFCHelper::getVar('filters', null);
		if ($filtersselected == null) { //provo a recuperarli dalla sessione...
			$filtersselected = BFCHelper::getFilterSearchParamsSession();
		}
		BFCHelper::setFilterSearchParamsSession($filtersselected);
	}
}

$filtersSelected = BFCHelper::getFilterSearchParamsSession();

$masterTypeId = isset($pars['masterTypeId']) ? $pars['masterTypeId'] : '';
$merchantCategoryId = isset($pars['merchantCategoryId']) ? $pars['merchantCategoryId'] : '';

//if (!empty($merchantCategoryId)) {
//	$services  =  BFCHelper::getServicesByMerchantsCategoryId($merchantCategoryId, $language);
//}else{
	$services  =  BFCHelper::getServicesForSearch($language);
//}

$duration = 1;
if (empty($duration)) {
	$duration =1;
}

$searchid = isset($_GET['searchid']) ? $_GET['searchid'] : '';
$searchtypetab = isset($_GET['searchtypetab']) ? $_GET['searchtypetab'] : '';;
$isMerchantResults = !empty($pars['merchantResults']) ? $pars['merchantResults']: 0;

$filtersMerchantsServices = array();
$filtersMerchantsTags = array();
$filtersMerchantsZones = array();
$filtersMerchantsRating = array();
$filtersMerchantsAvg = array();
$filtersResourcesCategories = array();
$filtersResourcesServices = array();
$filtersResourcesTags = array();
$filtersResourcesZones = array();
$filtersResourcesAvg = array();
$filtersResourcesRating = array();
$filtersMeals = array();
$filtersPrice = array();
$filtersPaymodes = array();

$filtersRooms = array();
$filtersBookingTypes= array();
$filtersBookingTypes[1] = __('Show only online booking', 'bfi');
$filtersOffers = array();
$filtersOffers[1] = __('Smart offer', 'bfi');


//i possibili filtri passati dalla ricersa
$filterscount = BFCHelper::getEnabledFilterSearchParamsSession();
$firstFilters = BFCHelper::getFirstFilterSearchParamsSession();
$filtersCheckAvailability = array();
$filtersCheckAvailability[1] = __('Show only available resources', 'bfi') ;

foreach ($firstFilters as $filter){
	switch ($filter->Name) {
		case 'mrcservices':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsServices[$item->Id] = $item;
				}
			}
			break; 
		case 'mrctags':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsTags[$item->Id] = $item;
				}
			}
			break; 
		case 'mrczones':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsZones[$item->Id] = $item;
				}
			}
			break; 
		case 'mrcrating':
			if(!empty( $filter->Items )){
				$allItems = $filter->Items;
				usort($allItems, function($a, $b)
				{
					return strcmp($b->Id,$a->Id);
				});
				foreach ($allItems as $item ) {
					$rating = intval($item->Name);
					if (!isset($item->Sup)) {
						$item->Sup = "";
					}
					if ($rating>9 )
					{
						if(($rating%10)>0){
							$item->Sup = "S";
						}
						$rating = $rating/10;
						$item->Name = intval($rating);
					}
				   $filtersMerchantsRating[$item->Id] = $item;
				}
			}
			break; 
		case 'mrcavg':
			if(isset( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsAvg[$item->Id] = $item;
				}
			}
			break; 
		case 'typology':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesCategories[$item->Id] = $item;
				}
			}
			break; 
		case 'resservices':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesServices[$item->Id] = $item;
				}
			}
			break; 
		case 'restags':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesTags[$item->Id] = $item;
				}
			}
			break; 
		case 'reszones':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesZones[$item->Id] = $item;
				}
			}
			break; 
		case 'resavg':
			if(isset( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesAvg[$item->Id] = $item;
				}
			}
			break; 
		case 'resrating':
			if(!empty( $filter->Items )){
				$allItems = $filter->Items;
				usort($allItems, function($a, $b)
				{
					return strcmp($b->Id,$a->Id);
				});
				foreach ($allItems as $item ) {
					$rating = intval($item->Name);
					if (!isset($item->Sup)) {
						$item->Sup = "";
					}
					if ($rating>9 )
					{
						if(($rating%10)>0){
							$item->Sup = "S";
						}
						$rating = $rating/10;
						$item->Name = intval($rating);
					}
					$item->Name = $rating;
				   $filtersResourcesRating[$item->Id] = $item;
				}
			}
			break; 
		case 'meals':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMeals[$item->Id] = $item;
				}
			}
			break; 
		case 'price':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersPrice[$item->Id] = $item;
				}
			}
			break; 
		case 'paymodes':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersPaymodes[$item->Id] = $item;
				}
			}
			break; 
		case 'rooms':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersRooms[$item->Id] = $item;
				}
			}
			break; 
	} 
}

$filtersMerchantsServicesCount = array();
$filtersMerchantsTagsCount = array();
$filtersMerchantsZonesCount = array();
$filtersMerchantsRatingCount = array();
$filtersMerchantsAvgCount = array();
$filtersResourcesCategoriesCount = array();
$filtersResourcesServicesCount = array();
$filtersResourcesTagsCount = array();
$filtersResourcesZonesCount = array();
$filtersResourcesAvgCount = array();
$filtersResourcesRatingCount = array();
$filtersMealsCount = array();
$filtersPriceCount = array();
$filtersPaymodesCount = array();

$filtersRoomsCount = array();

foreach ($filterscount as $filter){
	switch ($filter->Name) {
		case 'mrcservices':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsServicesCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'mrctags':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsTagsCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'mrczones':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsZonesCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'mrcrating':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsRatingCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'mrcavg':
			if(isset( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMerchantsAvgCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'typology':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesCategoriesCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'resservices':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesServicesCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'restags':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesTagsCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'reszones':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesZonesCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'resavg':
			if(isset( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesAvgCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'resrating':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersResourcesRatingCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'meals':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersMealsCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'price':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersPriceCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'paymodes':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersPaymodesCount[$item->Id] = $item->Count;
				}
			}
			break; 
		case 'rooms':
			if(!empty( $filter->Items )){
				foreach ($filter->Items as $item ) {
				   $filtersRoomsCount[$item->Id] = $item->Count;
				}
			}
			break; 
	} 
}


$filtersPriceValue = "";
$filtersResourcesCategoriesValue = "";
$filtersRatingValue = "";
$filtersAvgValue = "";
$filtersMealsValue = "";
$filtersMerchantsServicesValue = "";
$filtersResourcesServicesValue = "";
$filtersZonesValue = "";
$filtersBookingTypesValue = "";
$filtersOffersValue = "";
$filtersTagsValue = "";
$filtersRoomsValue = "";
$filtersPaymodesValue = "";
$filtersCheckAvailabilityValue = "";

if (isset($filtersSelected)) {
	$filtersPriceValue = ! empty( $filtersSelected[ 'price' ] ) ? $filtersSelected[ 'price' ] : "";
	$filtersResourcesCategoriesValue = ! empty( $filtersSelected[ 'resourcescategories' ] ) ? $filtersSelected[ 'resourcescategories' ] : "";
	$filtersRatingValue = ! empty( $filtersSelected[ 'rating' ] ) ? $filtersSelected[ 'rating' ] : "";
	$filtersAvgValue =  isset( $filtersSelected[ 'avg' ] ) ? $filtersSelected[ 'avg' ] : "";
	$filtersMealsValue = ! empty( $filtersSelected[ 'meals' ] ) ? $filtersSelected[ 'meals' ] : "";
	$filtersMerchantsServicesValue = ! empty( $filtersSelected[ 'merchantsservices' ] ) ? $filtersSelected[ 'merchantsservices' ] : "";
	$filtersResourcesServicesValue = ! empty( $filtersSelected[ 'resourcesservices' ] ) ? $filtersSelected[ 'resourcesservices' ] : "";
	$filtersZonesValue = ! empty( $filtersSelected[ 'zones' ] ) ? $filtersSelected[ 'zones' ] : "";
	$filtersBookingTypesValue = ! empty( $filtersSelected[ 'bookingtypes' ] ) ? $filtersSelected[ 'bookingtypes' ] : "";
	$filtersOffersValue = ! empty( $filtersSelected[ 'offers' ] ) ? $filtersSelected[ 'offers' ] : "";
	$filtersCheckAvailabilityValue = ! empty( $filtersSelected[ 'checkAvailability' ] ) ? $filtersSelected[ 'checkAvailability' ] : "";
	$filtersTagsValue = ! empty( $filtersSelected[ 'tags' ] ) ? $filtersSelected[ 'tags' ] : "";
	$filtersRoomsValue = ! empty( $filtersSelected[ 'rooms' ] ) ? $filtersSelected[ 'rooms' ] : "";
	$filtersPaymodesValue = ! empty( $filtersSelected[ 'paymodes' ] ) ? $filtersSelected[ 'paymodes' ] : "";
}

$filtersRating = array();
$filtersAvg = array();
$filtersZones = array();
$filtersTags = array();
$filtersRatingCount = array();
$filtersAvgCount = array();
$filtersZonesCount = array();
$filtersTagsCount = array();


if (isset($isMerchantResults) && $isMerchantResults){
	$filtersRating = $filtersMerchantsRating;
	$filtersAvg = $filtersMerchantsAvg;
	$filtersZones = $filtersMerchantsZones;
	$filtersTags = $filtersMerchantsTags;
	$filtersRatingCount = $filtersMerchantsRatingCount;
	$filtersAvgCount = $filtersMerchantsAvgCount;
	$filtersZonesCount = $filtersMerchantsZonesCount;
	$filtersTagsCount = $filtersMerchantsTagsCount;
}else{
	$filtersRating = $filtersResourcesRating;
	$filtersAvg = $filtersResourcesAvg;
	$filtersZones = $filtersResourcesZones;
	$filtersTags = $filtersResourcesTags;
	$filtersRatingCount = $filtersResourcesRatingCount;
	$filtersAvgCount = $filtersResourcesAvgCount;
	$filtersZonesCount = $filtersResourcesZonesCount;
	$filtersTagsCount = $filtersResourcesTagsCount;
}
$minvaluetoshow=1;
$currFilterOrder = "";
$currFilterOrderDirection = "";
if (!empty($currSorting) &&strpos($currSorting, '|') !== false) {
	$acurrSorting = explode('|',$currSorting);
	$currFilterOrder = $acurrSorting[0];
	$currFilterOrderDirection = $acurrSorting[1];
}

echo $before_widget;
?>
<div class="bfi-searchfilter">
<h3><?php _e('Filter by', 'bfi'); ?></h3>
<form action="<?php echo $formAction; ?>" method="post" id="searchformfilter" name="searchformfilter" >
	<input type="hidden" value="<?php echo $searchid ?>" name="searchid">
	<input type="hidden" value="0" name="limitstart">
	<input type="hidden" value="0" name="newsearch">
	<input type="hidden" value="<?php echo $searchtypetab ?>" name="searchtypetab">
	<input type="hidden" name="filter_order" class="filterOrder" id="filter_order_filter" value="<?php echo $currFilterOrder ?>">
	<input type="hidden"  name="filter_order_Dir" class= "filterOrderDirection"id="filter_order_Dir_filter" value="<?php echo $currFilterOrderDirection ?>">
<div id="bfi-filtertoggle">
	<?php if (isset($filtersPrice) &&  is_array($filtersPrice) && count($filtersPrice)>$minvaluetoshow ) { 
		//invert order filter price:
		$filtersPrice = array_reverse($filtersPrice);
	
	$filtersValueArr = explode ("|",$filtersPriceValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Budget', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersPrice as $itemId => $item){?>
<?php if($item->Count>0) { ?>

					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="price" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label bfi_<?php echo $currencyclass ?>"><?php 
						$currItem = explode(";",$itemId);
						echo (!empty( $currItem[0] ))? BFCHelper::priceFormat($currItem[0]) :BFCHelper::priceFormat(0);
						echo (!empty( $currItem[1] ))? " - <span class=' bfi_".$currencyclass."' >" . BFCHelper::priceFormat($currItem[1])."</span>" :"+";
					?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersPriceCount, $itemId) ?></span>
					</a>
<?php } ?>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersResourcesCategories) &&  is_array($filtersResourcesCategories) && count($filtersResourcesCategories)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersResourcesCategoriesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Tipology', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersResourcesCategories as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="resourcescategories" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersResourcesCategoriesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersRating) &&  is_array($filtersRating) && count($filtersRating)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersRatingValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Star rating', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersRating as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="rating" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $rating_text[$item->Name] ?> <?php echo $item->Sup ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersRatingCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if (isset($filtersAvg) &&  is_array($filtersAvg) && count($filtersAvg)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersAvgValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Review score', 'bfi') ?> </div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersAvg as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="avg" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $avg_text[$item->Name] ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersAvgCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersMeals) &&  is_array($filtersMeals) && count($filtersMeals)>$minvaluetoshow)  { 
	$filtersValueArr = explode ("|",$filtersMealsValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Meals', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersMeals as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="meals" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $meals_text[$item->Name] ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersMealsCount, $itemId)  ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersMerchantsServices) &&  is_array($filtersMerchantsServices) && count($filtersMerchantsServices)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersMerchantsServicesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Property facility', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersMerchantsServices as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="merchantsservices" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersMerchantsServicesCount, $itemId)  ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersResourcesServices) &&  is_array($filtersResourcesServices) && count($filtersResourcesServices)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersResourcesServicesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Facility', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersResourcesServices as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="resourcesservices" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersResourcesServicesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if (isset($filtersZones) &&  is_array($filtersZones) && count($filtersZones)>$minvaluetoshow)  { 
	$filtersValueArr = explode ("|",$filtersZonesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Destination', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersZones as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="zones" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersZonesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersBookingTypes) &&  is_array($filtersBookingTypes)) { 
	$filtersValueArr = explode ("|",$filtersBookingTypesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Booking type', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersBookingTypes as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="bookingtypes" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersOffers) &&  is_array($filtersOffers)) { 
	$filtersValueArr = explode ("|",$filtersOffersValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Offer', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersOffers as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="offers" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersCheckAvailability) &&  is_array($filtersCheckAvailability)) { 
	$filtersValueArr = explode ("|",$filtersCheckAvailabilityValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Availability', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersCheckAvailability as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="checkAvailability" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersTags) &&  is_array($filtersTags) && count($filtersTags)>0) { 
	$filtersValueArr = explode ("|",$filtersTagsValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Tags', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersTags as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="tags" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersTagsCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersRooms) &&  is_array($filtersRooms) && count($filtersRooms)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersRoomsValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Rooms', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersRooms as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="rooms" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersRoomsCount, $itemId)  ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersPaymodes) &&  is_array($filtersPaymodes)&& count($filtersPaymodes)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersPaymodesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Book with ease ', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersPaymodes as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="paymodes" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $paymodes_text[$item->Name] ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersPaymodesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	
</div>
<div class="bfi-clearfix"></div>
	<input type="hidden" name="filters[price]" id="filtersPriceHidden" value="<?php echo $filtersPriceValue ?>"/>
	<input type="hidden" name="filters[resourcescategories]" id="filtersResourcesCategoriesHidden" value="<?php echo $filtersResourcesCategoriesValue ?>" />
	<input type="hidden" name="filters[rating]" id="filtersRatingsHidden" value="<?php echo $filtersRatingValue ?>" />
	<input type="hidden" name="filters[avg]" id="filtersAvgHidden" value="<?php echo $filtersAvgValue ?>" />
	<input type="hidden" name="filters[meals]" id="filtersMealsHidden" value="<?php echo $filtersMealsValue ?>" />
	<input type="hidden" name="filters[merchantsservices]" id="filtersMerchantsServicesHidden" value="<?php echo $filtersMerchantsServicesValue ?>" />
	<input type="hidden" name="filters[resourcesservices]" id="filtersResourcesServicesHidden" value="<?php echo $filtersResourcesServicesValue ?>" />
	<input type="hidden" name="filters[zones]" id="filtersZonesHidden" value="<?php echo $filtersZonesValue ?>" />
	<input type="hidden" name="filters[bookingtypes]" id="filtersBookingTypesHidden" value="<?php echo $filtersBookingTypesValue ?>" />
	<input type="hidden" name="filters[offers]" id="filtersOffersHidden" value="<?php echo $filtersOffersValue ?>" />
	<input type="hidden" name="filters[tags]" id="filtersTagsHidden" value="<?php echo $filtersTagsValue ?>" />
	<input type="hidden" name="filters[rooms]" id="filtersRoomsHidden" value="<?php echo $filtersRoomsValue ?>" />
	<input type="hidden" name="filters[paymodes]" id="filtersPaymodesHidden" value="<?php echo $filtersPaymodesValue ?>" />
	<input type="hidden" name="filters[checkAvailability]" id="filtersCheckAvailabilityHidden" value="<?php echo $filtersCheckAvailabilityValue ?>" />
</form>
</div>
<br />
<?php if(BFI()->isSearchPage() && !empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
<div class="bfi-maps-static">
	<span class="bfi-showmap"><?php _e('Show map', 'bfi') ?></span>
	<img alt="Map" src="https://maps.google.com/maps/api/staticmap?center=<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSY?>,<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSX?>&amp;zoom=11&amp;size=400x250&key=<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY ?>&" style="max-width: 100%;" />
</div>
<?php } ?>


<script type="text/javascript">
ajaxFormAction = '<?php echo $formAction; ?>' + '';

function bfi_applyfilterdata(){ 		

	jQuery("#bfi-filtertoggle .bfi-option-title ").click(function(){
		jQuery(this).toggleClass("bfi-option-active");
		jQuery(this).next("div").stop('true','true').slideToggle("normal",function() {
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
		});
	});

	jQuery('.bfi-filter-label').bind('mouseenter', function(){
		var $this = jQuery(this);
		var divWidthBefore = $this.width();
		$this.css('width','auto');
		$this.css('white-space','nowrap');
		var divWidth = $this.width();
		$this.width(divWidthBefore+1);
		$this.css('white-space','normal');
		if(divWidthBefore< divWidth && !$this.attr('title') ){
			$this.attr('title', $this.text());
			$this.tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
			});
			$this.tooltip("open");
		}
	});
	
	jQuery('#searchformfilter').submit(function( event ) {
				try
				{
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
				jQuery("input[name^='filters\\[']").val("");

				jQuery("a.bfi-filter-active").each(function(){
					currValue = jQuery(this).attr("rel");
					currHiddenInput = jQuery("input[name='filters\\["+jQuery(this).attr("rel1")+"\\]']");
					currHiddenInput.val(currHiddenInput.val() + "|" + currValue);
				});
				jQuery("input[name^='filters\\[']").each(function(){
					jQuery(this).val(jQuery(this).val().substr(1));
				});
				jQuery('body').block({
					message:"",
						overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
				});
		});

			jQuery('.bfi-filteroptions a').on('click',function() {
<?php 
if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1){
?>
//				currValue = jQuery(this).attr("rel");
				currValue = jQuery(this).find(".bfi-filter-label").first().text(); 
				listname = jQuery(this).attr("rel1");
				currAction = jQuery(this).hasClass("bfi-filter-active")? "Remove":"Add";
				callAnalyticsEEc("", "", listname + "|" + currValue, null, currAction, "Search Filters");
<?php 
}
?>
				jQuery(this).toggleClass("bfi-filter-active");
				jQuery(this).closest('form').submit();
			});
	
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
}

jQuery(document).ready(function() {
	bfi_applyfilterdata();
});  


</script>
<?php echo $after_widget; ?>
<div class="bfi-clearfix"></div>
<?php } ?>
<?php bfi_get_template("widgets/search-filter-merchants.php"); ?>