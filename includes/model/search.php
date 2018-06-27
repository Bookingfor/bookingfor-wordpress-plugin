<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelSearch' ) ) {

class BookingForConnectorModelSearch
{
	private $urlSearch = null;
	private $urlSearchResult = null;
	private $urlMasterTypologies = null;
	private $urlGetOtherAvailability = null;

	private $helper = null;
	private $currentOrdering = null;
	private $currentDirection = null;
	private $count = null;
	private $availableCount = null;

	private $currentData = null;
	private $params = null;
	private $itemPerPage = null;
	private $direction = null;

	public function __construct($config = array())
	{
		$this->helper = new wsQueryHelper(null, null);
		$this->urlMasterTypologies = '/GetMasterTypologies';
		$this->urlSearchResult = '/SearchResult';
		$this->urlSearch = '/SearchAllLiteNew';
		$this->urlGetAlternativeDates = '/GetAlternativeDates';
	}

	public function setItemPerPage($itemPerPage) {
		if(!empty($itemPerPage)){
			$this->itemPerPage = $itemPerPage;
		}
	}
	public function setOrdering($ordering) {
		if(!empty($ordering)){
			$this->currentOrdering = $ordering;
		}
	}
	public function setDirection($direction) {
		if(!empty($direction)){
			$this->currentDirection = $direction;
		}
	}
	public function getOrdering() {
		return $this->currentOrdering;
	}
	public function getDirection() {
		return $this->currentDirection;
	}

	public function getParam() {
		return $this->params;
	}

	public function setParam($param) {
		$this->params = $param ;
	}

	public function applyDefaultFilter(&$options) {
		$params = BFCHelper::getSearchParamsSession();

		$searchid = !empty($params['searchid']) ? $params['searchid'] : uniqid('', true);
		$masterTypeId = $params['masterTypeId'];
		$checkin = $params['checkin'];
		$checkout = $params['checkout'];
		$duration = $params['duration'];
		$persons = $params['paxes'];
		$merchantCategoryId = $params['merchantCategoryId'];
		$paxages = $params['paxages'];
		$merchantId = $params['merchantId'];
		$tags = isset($params['tags'])?$params['tags']:"";
		$searchtypetab = $params['searchtypetab'];
		$stateIds = $params['stateIds'];
		$regionIds = $params['regionIds'];
		$cityIds = $params['cityIds'];
		$merchantIds = isset($params['merchantIds']) ? $params['merchantIds'] : '';
		$merchantTagIds = $params['merchantTagIds'];
		$productTagIds = $params['productTagIds'];
		
		$availabilitytype = $params['availabilitytype'];
		$itemtypes = $params['itemtypes'];
		$groupresulttype = $params['groupresulttype'];

		$cultureCode = $params['cultureCode'];
		
		$filters = $params['filters'];
//				$filtersselected = BFCHelper::getFilterSearchParamsSession();
		if(empty($filters)){
			$filters = BFCHelper::getFilterSearchParamsSession();		
		}
		$resourceName = $params['resourceName'].'';
		$refid = $params['refid'].'';

		$variationPlanIds = isset($params['variationPlanIds']) ? $params['variationPlanIds'] : '';

		if (!empty($refid) or !empty($resourceName))  {
			$options['data']['calculate'] = 0;
			$options['data']['checkAvailability'] = 0;
			
			if (isset($refid) && $refid <> "" ) {
				$options['data']['refId'] = '\''.$refid.'\'';
			}
			if (isset($resourceName) && $resourceName <> "" ) {
				$options['data']['resourceName'] = '\''. $resourceName.'\'';
			}
		}else{
		
			$onlystay = $params['onlystay'];
			$options['data']['calculate'] = $onlystay;

//			$options['data']['checkAvailability'] = $onlystay;
			$bookableonly = $params['bookableonly'];
			if(empty($onlystay )){
				$options['data']['checkAvailability'] = 0;
			}
			if(!empty($bookableonly )){
				$options['data']['calculate'] = $bookableonly;
				$options['data']['checkAvailability'] = $bookableonly;
			}
			
			if (isset($params['locationzone']) ) {
				$locationzone = $params['locationzone'];
			}
			if (isset($masterTypeId) && $masterTypeId > 0) {
				$options['data']['masterTypeIds'] = '\'' .$masterTypeId.'\'';
			}

			if (!empty($merchantCategoryId) && $merchantCategoryId > 0) {
				$currmerchantCategoryId = array();
				if(!is_array($merchantCategoryId)){
					$merchantCategoryId = urldecode ($merchantCategoryId);
					$merchantCategoryId = explode(',',$merchantCategoryId);
				}
				$currmerchantCategoryId = array_values(array_filter($merchantCategoryId, function($a) {
					if (is_int($a))
						return true;
					return false;
				}));
				$merchantCategoryId = implode(',',$currmerchantCategoryId);
				$options['data']['merchantCategoryIds'] = '\'' .$merchantCategoryId.'\'';
			}			
			if (!empty($variationPlanIds)) {
				$options['data']['variationPlanIds'] = '\'' .$variationPlanIds.'\'';
			}
			
			if(empty($duration)){
				$duration = 0;
			}
			if ((isset($checkin))) {
				$options['data']['checkin'] = '\'' . $checkin->format('Ymd') . '\'';
				$options['data']['duration'] = $duration;
			}

			if (isset($availabilitytype) ) {
				$options['data']['availabilityTypes'] = '\'' .$availabilitytype .'\'';
			}
			if (isset($itemtypes) ) {
				$options['data']['itemTypeIds'] = '\'' .$itemtypes .'\'';
			}

			if (isset($groupresulttype) ) {
				$options['data']['groupResultType'] = $groupresulttype;
//				if ($groupresulttype==1 || $groupresulttype==2) { //onbly for merchants 
					$options['data']['getBestGroupResult'] = 1;
//				}
			}

			$points = isset($params['points']) ? $params['points'] : '' ;
			if (isset($points) && $points !='') {
				$options['data']['points'] = '\'' . $points. '\'';
			}

			if (isset($persons) && $persons > 0) {
				$options['data']['paxes'] = $persons;
				if (isset($paxages)) {
					$options['data']['paxages'] = '\'' . implode('|',$paxages) . '\'';
					// ciclo per aggiungere i dati
					$newpaxages = array();
					foreach ($paxages as $age) {
						if ($age >= BFCHelper::$defaultAdultsAge) {
							if ($age >= BFCHelper::$defaultSenioresAge) {
								array_push($newpaxages, $age.":".bfiAgeType::$Seniors);
							} else {
								array_push($newpaxages, $age.":".bfiAgeType::$Adult);
							}
						} else {
							array_push($newpaxages, $age.":".bfiAgeType::$Reduced);
						}
					}

					$options['data']['paxages'] = '\'' . implode('|',$newpaxages) . '\'';
				}else{
					$px = array_fill(0,$persons,BFCHelper::$defaultAdultsAge.":".bfiAgeType::$Adult);
					$options['data']['paxages'] = '\'' . implode('|',$px) . '\'';
				}
			}
			
//				$options['data']['pricetype'] = '\'' . 'rateplan' . '\'';

			if (isset($locationzone) && $locationzone !='' && $locationzone !='0') {
				$options['data']['zoneIds'] = '\''. $locationzone . '\'';
			}
			
			if (!empty($tags)) {
				$options['data']['tagids'] = '\'' . $tags . '\'';
			}				
		}


		if (isset($cultureCode) && $cultureCode !='') {
			$options['data']['cultureCode'] = '\'' . $cultureCode. '\'';
		}
		if (isset($searchid) && $searchid !='') {
			$options['data']['searchid'] = '\'' . $searchid. '\'';
		}

		if (isset($merchantId) && $merchantId > 0) {
			$options['data']['merchantid'] = $merchantId;
		}

		if (isset($stateIds) && $stateIds !='') {
			$options['data']['stateIds'] = '\'' . $stateIds. '\'';
		}

		if (isset($regionIds) && $regionIds !='') {
			$options['data']['regionIds'] = '\'' . $regionIds. '\'';
		}

		if (isset($cityIds) && $cityIds !='') {
			$options['data']['cityIds'] = '\'' . $cityIds. '\'';
		}

		if (isset($merchantIds) && $merchantIds !='') {
			$options['data']['merchantsList'] = '\'' . $merchantIds. '\'';
		}

		if (isset($merchantTagIds) && $merchantTagIds !='') {
			$options['data']['merchantTagsIds'] = '\'' . $merchantTagIds. '\'';
		}

		if (isset($productTagIds) && $productTagIds !='') {
			$options['data']['tagids'] = '\'' . $productTagIds. '\'';
		}



		if (!empty($this->currentOrdering )) {
			$options['data']['orderby'] = '\'' . $this->currentOrdering . '\'';
			$options['data']['ordertype'] = '\'' . $this->currentDirection . '\'';
		}

//filters[price]:200;
//filters[resourcescategories]:6
//filters[rating]:0
//filters[avg]:0
//filters[meals]:
//filters[merchantsservices]:
//filters[resourcesservices]:
//filters[zones]:
//filters[bookingtypes]:
//filters[offers]:
//filters[tags]:
//filters[rooms]:
//filters[paymodes]:			
		if(!empty( $filters )){
			if(!empty( $filters['price'] )){
				$options['data']['priceRange'] = BFCHelper::getQuotedString($filters['price']) ;
			}
			if(!empty( $filters['resourcescategories'] )){
				$options['data']['masterTypeIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['resourcescategories'])) ;
			}
			if(!empty( $filters['rating'] )){
				if (isset($groupresulttype) ) {
					if ($groupresulttype==1 ) { //onbly for merchants 
						$options['data']['mrcRatingIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['rating'])) ;
					}else{
						$options['data']['resRatingIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['rating'])) ;
					}
				}else{
					$options['data']['ratingIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['rating'])) ;
				}
			}
			if(!empty( $filters['avg'] )){
				if (isset($groupresulttype) ) {
					if ($groupresulttype==1 ) { //onbly for merchants 
						$options['data']['mrcAvgs'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['avg'])) ;
					}else{
						$options['data']['resAvgs'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['avg'])) ;
					}
				}else{
					$options['data']['resAvgs'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['avg'])) ;
				}
			}
			if(!empty( $filters['meals'] )){
				$options['data']['includedMeals'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['meals'])) ;
			}
			if(!empty( $filters['merchantsservices'] )){
				$options['data']['merchantServiceIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['merchantsservices'])) ;
			}
			if(!empty( $filters['resourcesservices'] )){
				$options['data']['resourceServiceIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['resourcesservices'])) ;
			}
			if(!empty( $filters['zones'] )){
				$options['data']['zoneIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['zones'])) ;
			}
			if(!empty( $filters['bookingtypes'] )){
				$options['data']['requirePaymentsOnly'] = 1 ;
			}
			if(!empty( $filters['offers'] )){
				$options['data']['discountedPriceOnly'] = 1 ;
			}
			if(!empty( $filters['tags'] )){
				$options['data']['tagids'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['tags'])) ;
			}
			if(!empty( $filters['rooms'] )){
				$options['data']['bedRooms'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['rooms'])) ;
			}
			if(!empty( $filters['paymodes'] )){
				if (strpos($filters['paymodes'],"freecancellation")!== FALSE) {
					$options['data']['freeCancellation'] = 1 ;
				}
				if (strpos($filters['paymodes'],"freepayment")!== FALSE) {
					$options['data']['payOnArrival'] = 1 ;
				}
				if (strpos($filters['paymodes'],"freecc")!== FALSE) {
					$options['data']['freeDeposit'] = 1 ;
				}
			}
			if(!empty( $filters['checkAvailability'] )){
				$options['data']['checkAvailability'] = 1 ;
			}
		}

		
//		if ($filters!='')
//			$options['data']['$filter'] = $filter;
	}

	public function GetAlternativeDates($checkin, $duration, $paxes, $paxages, $merchantId, $condominiumId, $resourceId, $cultureCode, $points, $userid, $tagids,
			$merchantsList, $availabilityTypes, $itemTypeIds, $domainLabel, $merchantCategoryIds = null, $masterTypeIds = null, $merchantTagsIds = null
		){

		if (empty($cultureCode)){
//			$cultureCode = JFactory::getLanguage()->getTag();
			$cultureCode = $GLOBALS['bfi_lang'];
		}
		$options = array(
			'path' => $this->urlGetAlternativeDates,
			'data' => array(
				'$format' => 'json',
				'cultureCode' => BFCHelper::getQuotedString($cultureCode),
			)
		);
		if(empty($duration)){
			$duration = 0;
		}
		if ((isset($checkin))) {
			$options['data']['checkin'] = '\'' . $checkin . '\'';
			$options['data']['duration'] = $duration;
		}
		if ((!empty($paxes))) {
			$options['data']['paxes'] = $paxes;
		}
		if ((!empty($paxages))) {
			$options['data']['paxages'] = '\'' . $paxages . '\'';
		}
		if ((!empty($merchantId))) {
			$options['data']['merchantId'] = $merchantId;
		}
		if ((!empty($condominiumId))) {
			$options['data']['condominiumId'] = $condominiumId;
		}
		if ((!empty($resourceId))) {
			$options['data']['resourceId'] = $resourceId;
		}
		if ((!empty($points))) {
			$options['data']['points'] = '\'' . $points . '\'';
		}
		if ((!empty($userid))) {
			$options['data']['userid'] = '\'' . $userid . '\'';
		}
		if ((!empty($tagids))) {
			$options['data']['tagids'] = '\'' . $tagids . '\'';
		}
		if ((!empty($merchantsList))) {
			$options['data']['merchantsList'] = '\'' . $merchantsList . '\'';
		}
		if ((isset($availabilityTypes) && $availabilityTypes!='')) {
			$options['data']['availabilityTypes'] = '\'' . $availabilityTypes . '\'';
		}
		if ((isset($itemTypeIds) && $itemTypeIds!='')) {
			$options['data']['itemTypeIds'] = '\'' . $itemTypeIds . '\'';
		}
		if ((!empty($domainLabel))) {
			$options['data']['domainLabel'] = '\'' . $domainLabel . '\'';
		}
		if ((!empty($merchantCategoryIds))) {
			$options['data']['merchantCategoryIds'] = '\'' . $merchantCategoryIds . '\'';
		}
		if ((!empty($masterTypeIds))) {
			$options['data']['masterTypeIds'] = '\'' . $masterTypeIds . '\'';
		}
		if ((!empty($merchantTagsIds))) {
			$options['data']['merchantTagsIds'] = '\'' . $merchantTagsIds . '\'';
		}		
		$url = $this->helper->getQuery($options);
		
		$alternativeDates = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$typologies = $res->d->results ?: $res->d;
			if (!empty($res->d->GetAlternativeDates)){
				$alternativeDates = $res->d->GetAlternativeDates;
			}elseif(!empty($res->d)){
				$alternativeDates = $res->d;
			}
		}
		
		return $alternativeDates;

	}

	public function getSearchResults($start, $limit, $ordering, $direction, $ignorePagination = false, $jsonResult = false) {

		$this->currentOrdering = $ordering;
		$this->currentDirection = $direction;

		$params = BFCHelper::getSearchParamsSession();
				
		$searchid = isset($params['searchid']) ? $params['searchid'] : '';
		$newsearch = isset($params['newsearch']) ? $params['newsearch'] : '0';
//		$pricerange = $params['pricerange'];
		$merchantResults = $params['merchantResults'];
		$condominiumsResults = $params['condominiumsResults'];
		$sessionkey = 'search.' . $searchid . '.results';			

		//$session = JFactory::getSession();
		$results = $this->currentData;

		if($newsearch == "1"){
			BFCHelper::setFilterSearchParamsSession(null);
		}else{
			$filtersselected = BFCHelper::getVar('filters', null);
			if ($filtersselected == null) { //provo a recuperarli dalla sessione...
				$filtersselected = BFCHelper::getFilterSearchParamsSession();
			}
			BFCHelper::setFilterSearchParamsSession($filtersselected);
		}
			

		if ($results == null) {
			$options = array(
				'path' => $this->urlSearch,
				'data' => array(
						'$format' => 'json',
						'topRresult' => 0,
						'lite' => 1
				)
			);
			
			if(!$ignorePagination){
				if (isset($start) && $start >= 0) {
					$options['data']['skip'] = $start;
				}
				
				if (isset($limit) && $limit > 0) {
					$options['data']['topRresult'] = $limit;
				}
			}

			$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
			if($currUser!=null && !empty($currUser->CustomerId)) {
								$options['data']['userid'] = '\'' . $currUser->CustomerId . '\'';
			}

			$this->applyDefaultFilter($options);

			$url = $this->helper->getQuery($options);

			$results = null;

			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->SearchAllLiteNew)){
					$results = $res->d->SearchAllLiteNew;
				}elseif(!empty($res->d)){
					$results = $res->d;
				}
			}

						
			$filtersenabled = array();
			if(!empty($results)){
				$filtersenabled = json_decode($results->FiltersString);
				$params['merchantResults'] = ($results->GroupResultType==1);
				$params['condominiumsResults'] = ($results->GroupResultType==2);
				$merchantResults = $params['merchantResults'];
				$condominiumsResults = $params['condominiumsResults'];
			}
			BFCHelper::setSearchParamsSession($params);
			if($newsearch == "1"){
				BFCHelper::setFirstFilterSearchParamsSession($filtersenabled);
			}
			BFCHelper::setEnabledFilterSearchParamsSession($filtersenabled);
		}
		$resultsItems = null;

		if(isset($results->ItemsCount)){
			$this->count = $results->ItemsCount;
			$this->availableCount = $results->AvailableItemsCount;
			$resultsItems = json_decode($results->ItemsString);
		}
				
		if($jsonResult && !empty($resultsItems))	{
			$arr = array();

			foreach($resultsItems as $result) {
				$val= new StdClass;
				
				if ($merchantResults) {
					$val->MerchantId = $result->MerchantId; 
					$val->XGooglePos = $result->MrcLat;
					$val->YGooglePos = $result->MrcLng;
					$val->MerchantName = BFCHelper::string_sanitize($result->MrcName);
				}
				elseif ($condominiumsResults){
					$val->Resource = new StdClass;
					$val->Resource->CondominiumId = $result->CondominiumId;
					$val->Resource->ResourceId = $result->ResourceId;
					$val->Resource->XGooglePos = $result->ResLat;
					$val->Resource->YGooglePos = $result->ResLng;
					$val->Resource->ResourceName = BFCHelper::string_sanitize($result->ResName);
					$val->Resource->Price = $result->Price;
				}
				else { 
					$val->Resource = new StdClass;
					$val->Resource->ResourceId = $result->ResourceId;
					$val->Resource->XGooglePos = $result->ResLat;
					$val->Resource->YGooglePos = $result->ResLng;
					$val->Resource->ResourceName = BFCHelper::string_sanitize($result->ResName);
					$val->Resource->Price = $result->Price;
				}
				$arr[] = $val;
			}
			
			return json_encode($arr);
				
		}
		return $resultsItems;

	}
	

	public function getTotal()
	{
		if ($this->count !== null){
			return $this->count;
		}
		else{
			$this->retrieveItems();
			return $this->count;
		}

	}
	public function getTotalAvailable()
	{
		if ($this->availableCount !== null){
			return $this->availableCount;
		}
		else{
			$this->retrieveItems();
			return $this->availableCount;
		}
	}

	public function getMasterTypologiesFromService($onlyEnabled = true, $language='') {
		$options = array(
				'path' => $this->urlMasterTypologies,
				'data' => array(
					/*'$filter' => 'IsEnabled eq true',*/
					'typeId' => '1',
					'cultureCode' =>  BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);

		if ($onlyEnabled) {
//			$options['data']['$filter'] = 'Enabled eq true';
			$options['data']['isEnable'] = 'true';
		}

		$url = $this->helper->getQuery($options);

		$typologies = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$typologies = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$typologies = $res->d->results;
			}elseif(!empty($res->d)){
				$typologies = $res->d;
			}
		}

		return $typologies;
	}

	public function getMasterTypologies($onlyEnabled = true, $language='') {
	  $typologies = $this->getMasterTypologiesFromService($onlyEnabled);
     return $typologies;
	}
        
	public function getItems($ignorePagination = false, $jsonResult = false, $start = 0, $count = 20) {
		if ($this->currentData !== null){
			return $this->currentData;
		}
		else{
//			$start = $this->getState('list.start'); 
//			$count = $this->getState('list.limit');
			$this->retrieveItems($ignorePagination, $jsonResult, $start, $count);
		}
		return $this->currentData;
	}

	public function retrieveItems($ignorePagination = false, $jsonResult = false, $start = 0, $count = 20) {
		if(!empty($_REQUEST['filter_order']) ){
			$items = $this->getSearchResults(
				$start,
				$count,
				$_REQUEST['filter_order'],
				$_REQUEST['filter_order_Dir'],
				$ignorePagination,
				$jsonResult
			);
		} else {
			$items = $this->getSearchResults(
				$start,
				$count,
				'',
				'',
				$ignorePagination,
				$jsonResult
			);
		}
		$this->currentData = $items;
	}
	
	public function SearchResult($term, $language, $limit, $onlyLocations=0) {
		if(empty( $onlyLocations)){
			$onlyLocations = 0;
		}
		
		$options = array(
			'path' => $this->urlSearchResult,
			'data' => array(
					'$format' => 'json',
					'term' => BFCHelper::getQuotedString($term),
					'onlyLocations' => $onlyLocations,
					'cultureCode' =>  BFCHelper::getQuotedString($language),
					'top' => 0
			)
		);
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}

		$url = $this->helper->getQuery($options);

		$results = array();

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->SearchResult)){
				$results = $res->d->SearchResult;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		}
		
		return $results;
	}
}
}