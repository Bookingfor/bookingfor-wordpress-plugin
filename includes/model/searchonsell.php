<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelSearchOnSell' ) ) :

class BookingForConnectorModelSearchOnSell
{
	private $urlSearch = null;
	private $urlSearchRandom = null;
	private $urlSearchCount = null;
	private $helper = null;
	private $currentOrdering = null;
	private $currentDirection = null;
	private $count = null;
	private $urlSearchLocationZone = null;
	private $urlCreateUserAlert = null;
	private $urlUnsubscribeUserAlert = null;
	private $urlCreateRequestOnSell = null;
	private $urlMasterTypologies = null;
	private $params = null;
	private $itemPerPage = null;
	private $ordering = null;
	private $direction = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlSearch = '/SearchOnsellSimple';
		$this->urlSearchRandom = '/SearchOnsellRandomSimple';
		$this->urlSearchCount = '/GetSearchOnsellCountSimple';
		$this->urlSearchLocationZone = '/GetLocationZoneOnsell';
		$this->urlCreateUserAlert = '/CreateUserAlert';
		$this->urlUnsubscribeUserAlert = '/DisableAlerts';
		$this->urlCreateRequestOnSell = '/CreateRequestOnSell';
		$this->urlMasterTypologies = '/GetMasterTypologies';
	}
	
	public function setItemPerPage($itemPerPage) {
		if(!empty($itemPerPage)){
			$this->itemPerPage = $itemPerPage;
		}
	}
	public function setOrdering($ordering) {
		if(!empty($ordering)){
			$this->ordering = $ordering;
		}
	}
	public function setDirection($direction) {
		if(!empty($direction)){
			$this->direction = $direction;
		}
	}
	public function getOrdering() {
		return $this->ordering;
	}
	public function getDirection() {
		return $this->direction;
	}

	public function getParam() {
		return $this->params;
	}

	public function setParam($param) {
		$this->params = $param ;
	}


	public function unsubscribeAlertOnSell($hash = NULL, $id = NULL) {
		$options = array(
				'path' => $this->urlUnsubscribeUserAlert,
				'data' => array(
					'hashedEmail' => BFCHelper::getQuotedString($hash),
					'alertId' => $id,
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		
		$alert = null;
		
		//$r = $this->helper->executeQuery($url);
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$alert = (int)$res->d->DisableAlerts;
			//$alert = $res->d->results ?: $res->d;
		}
		
		return $alert;
	}


	public function sendRequestOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processRequest = NULL) {
		$customers = array($customerData);
		$options = array(
				'path' => $this->urlCreateRequestOnSell,
				'data' => array(
					'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customers)),
					//'searchData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($searchData)),
					'searchData' => BFCHelper::getQuotedString($searchData),
					'merchantId' => $merchantId,
					'type' => BFCHelper::getQuotedString($type),
					'label' => BFCHelper::getQuotedString($label),
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'processRequest' => $processRequest,
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		
		$alert = null;
		
		//$r = $this->helper->executeQuery($url);
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			$alert = (int)$res->d->CreateRequestOnSell;
			//$alert = $res->d->results ?: $res->d;
		}
		
		return $alert;
	}

	public function setAlertOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processRequest = NULL, $enabled = NULL) {
		$options = array(
				'path' => $this->urlCreateUserAlert,
				'data' => array(
					'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
					'searchData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($searchData)),
					'merchantId' => $merchantId,
					'type' => BFCHelper::getQuotedString($type),
					'label' => BFCHelper::getQuotedString($label),
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'processUserAlert' => $processRequest,
					'enabled' => $enabled,
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		
		$alert = null;
		
		//$r = $this->helper->executeQuery($url);
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			$alert = (int)$res->d->CreateUserAlert;
			//$alert = $res->d->results ?: $res->d;
		}
		
		return $alert;
	}

	public function applyDefaultFilter(&$options) {
//		$params = $_SESSION['searchonsell.params'];
		$params = BFCHelper::getSearchOnSellParamsSession();
		
		$masterTypeId = "";
		if(!empty($params['masterTypeId'])){
			$masterTypeId = $params['masterTypeId'];
		}
		$unitCategoryId = $params['unitCategoryId'];
		$merchantId = isset($params['merchantId']) ? $params['merchantId'] : '';

		$contractTypeId = $params['contractTypeId'];

		$areamin = isset($params['areamin']) ? $params['areamin'] : '';
		$areamax = isset($params['areamax']) ? $params['areamax'] : '';
		$pricemin = isset($params['pricemin']) ? $params['pricemin'] : '';
		$pricemax = isset($params['pricemax']) ? $params['pricemax'] : '';
		$services = isset($params['services']) ? $params['services'] : '';
		
		$roomsmin = isset($params['roomsmin']) ? $params['roomsmin'] : '';
		$roomsmax = isset($params['roomsmax']) ? $params['roomsmax'] : '';
		$bedroomsmin = isset($params['bedroomsmin']) ? $params['bedroomsmin'] : '';
		$bedroomsmax = isset($params['bedroomsmax']) ? $params['bedroomsmax'] : '';
		$bathsmin = isset($params['bathsmin']) ? $params['bathsmin'] : '';
		$bathsmax = isset($params['bathsmax']) ? $params['bathsmax'] : '';
		
		$showReserved = isset($params['showReserved']) ? $params['showReserved'] : '';
		
		$searchType = isset($params['searchType']) ? $params['searchType'] : '';
				
		if($searchType == "0"){
			$cityId = $params['cityId'];
		} else {
			$cityId = -1000;
		}
		$isnewbuilding = isset($params['isnewbuilding']) ? $params['isnewbuilding'] : '';

		if (isset($isnewbuilding) && !empty($isnewbuilding)) {
			$options['data']['onlyNewBuild'] = $isnewbuilding;
		}
		
		if (isset($showReserved) && trim($showReserved)!=="" ) { //mon posso usare empty perchè lo "0" viene considerato come empty!!
			$options['data']['showReserved'] = $showReserved;
		}

		if (isset($services) && !empty($services)) {
			$options['data']['serviceList'] = '\'' . $services. '\'';
		}

		if (isset($areamin) && $areamin > 0) {
			$options['data']['minArea'] = $areamin;
		}
		if (isset($areamax) && $areamax > 0) {
			$options['data']['maxArea'] = $areamax;
		}
		if (isset($pricemin) && $pricemin > 0) {
			$options['data']['minPrice'] = $pricemin;
		}
		if (isset($pricemax) && $pricemax > 0) {
			$options['data']['maxPrice'] = $pricemax;
		}

		if (isset($roomsmin) && $roomsmin > 0) {
			$options['data']['minRooms'] = $roomsmin;
		}
		if (isset($roomsmax) && $roomsmax > 0) {
			$options['data']['maxRooms'] = $roomsmax;
		}
		if (isset($bedroomsmin) && $bedroomsmin > 0) {
			$options['data']['minBedRooms'] = $bedroomsmin;
		}
		if (isset($bedroomsmax) && $bedroomsmax > 0) {
			$options['data']['maxBedRooms'] = $bedroomsmax;
		}
		
		if (isset($bathsmin) && $bathsmin > 0) {
			$options['data']['minBaths'] = $bathsmin;
		}
		if (isset($bathsmax) && $bathsmax > 0) {
			$options['data']['maxBaths'] = $bathsmax;
		}

		if (!empty($cityId) && $cityId > -10 ) {
			$options['data']['cityId'] = $cityId;
		}
		$zoneId = $params['zoneId'];
		if (isset($zoneId) && $zoneId > 0) {
			$options['data']['zoneId'] = $zoneId;
		}

		$points = isset($params['points']) ? $params['points'] : '' ;
		if (isset($points) && $points !='' && $cityId < -1) {
			$options['data']['points'] = '\'' . $points. '\'';
		}

		$stateIds = $params['stateIds'];
		$regionIds = $params['regionIds'];
		$cityIds = $params['cityIds'];
		if (isset($params['locationzone']) ) {
			$locationzone = $params['locationzone'];
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
		if (isset($locationzone) && $locationzone !='' && $locationzone !='0') {
			$options['data']['zoneIds'] = '\''. $locationzone . '\'';
		}

		$zoneIds = isset($params['zoneIds']) ? $params['zoneIds'] : array();
		if (!empty($zoneIds) && !empty($cityId) ) {
			$options['data']['zoneIds'] = '\'' . $zoneIds. '\'';
		}
//		$zoneId = $params['zoneId'];
//		if (!empty($zoneId)) {
//			$options['data']['zoneId'] = '\'' . $zoneId. '\'';
//		}

		$cultureCode = isset($params['cultureCode']) ? $params['cultureCode'] : 'en-gb';
	
		$filter = '';
		
		if (isset($masterTypeId) && $masterTypeId > 0) {
			$options['data']['masterTypeId'] = $masterTypeId;
		}

		if (isset($unitCategoryId) && $unitCategoryId > 0) {
			$options['data']['unitCategoryId'] = $unitCategoryId;
		}
				
		if (isset($contractTypeId) && $contractTypeId > -1) {
			$options['data']['contractType'] = $contractTypeId;
		}

		if (isset($cultureCode) && $cultureCode !='') {
			$options['data']['cultureCode'] = '\'' . $cultureCode. '\'';
		}
		
		if (isset($merchantId) && $merchantId > 0) {
			//$options['data']['merchantid'] = $merchantId;
		}

		if ($filter!='')
			$options['data']['$filter'] = $filter;

		/*if (count($categoryIds) > 0)
			$options['data']['categoryIds'] = '\''.implode('|',$categoryIds).'\'';*/
	}
	

	public function getLocationZonesBySearch()
	{

//		$params = $_SESSION['searchonsell.params'];
		$params = BFCHelper::getSearchOnSellParamsSession();
		$results = null;
		$options = array(
			'path' => $this->urlSearchLocationZone,
			'data' => array(
				'$format' => 'json',
				)
			);
		
		$this->applyDefaultFilter($options);
		
		$options['data']['zoneIds'] = '';
		$zoneIds = isset($params['zoneIds']) ? $params['zoneIds'] : array();
		if (!empty($zoneIds)) {
			$options['data']['zoneIds'] = '\'' . $zoneIds. '\'';
		}

		$url = $this->helper->getQuery($options);

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$results = $res->d->results;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		if(!empty($results)){
			$results=str_ireplace("(","ZZZZ (",$results);
			natcasesort($results);
			$results=str_ireplace("ZZZZ (","(",$results);
		}

		}			
		
		return $results;
	}
	
	public function getSearchResults($start, $limit, $ordering = '', $direction = '', $ignorePagination = false, $jsonResult = false) {

		$this->currentOrdering = $ordering;
		$this->currentDirection = $direction;
		
//		$params = $_SESSION['searchonsell.params'];
		$params = BFCHelper::getSearchOnSellParamsSession();
		$searchid = isset($params['searchid']) ? $params['searchid'] : '';
		$seed = isset($params['searchseed']) ? $params['searchseed'] : '';
		$randomresult = isset($params['randomresult']) ? $params['randomresult'] : '';

		$results = null;
		$options = array(
			'path' => $this->urlSearch,
			'data' => array(
				'$format' => 'json',
//				'$select' => 'ResourceId,Name,MerchantId,MerchantName,Rooms,Area,ImageUrl,LogoUrl,MinPrice,XPos,YPos,LocationZone,IsReservedPrice,IsAddressVisible,AddedOn,IsHighlight,IsMapVisible,IsMapMarkerVisible,CategoryName,ContractType,LocationName,MerchantCategoryName',
				'topRresult' => 0,
				'lite' => 1
				)
			);
		
		if (! $ignorePagination && isset($start) && (isset($limit) && $limit > 0 )) {
			if (isset($start) && $start >= 0) {
//				$options['data']['$skip'] = $start;
				$options['data']['skip'] = $start;
			}
			
			if (isset($limit) && $limit > 0) {
//				$options['data']['$top'] = $limit;
				$options['data']['topRresult'] = $limit;
			}

			if (!empty($ordering)) {
//					switch (strtolower($ordering)) {
//					case 'price':
//						$orderby = 'MinPrice ' . $direction;
//						break;
//					case 'created':
//						$orderby = 'AddedOn ' . $direction;
//						break;
//					default:
//						$orderby = "IsShowcase desc, IsForeground desc, MinPrice, AddedOn  desc";
//				}
//				$options['data']['$orderby'] = $orderby;
				$options['data']['orderType'] = '\'' . $ordering."|".$direction . '\'';
			}
			
//		}else{
//			$options['data']['$select'] = 'ResourceId,XPos,YPos,IsMapVisible,IsMapMarkerVisible,Name';
//			$options['data']['$filter'] = 'XPos ne null and YPos ne null';			

		}

if (!empty($randomresult) && $randomresult == '1' && empty($ordering)){
	$options['path'] = $this->urlSearchRandom;
	$options['data']['seed'] = $seed;

	// for search random I need to remove all reference for odata request 
	unset($options['data']['skip']);
	unset($options['data']['topRresult']);
	unset($options['data']['orderType']);
}
		$this->applyDefaultFilter($options);

		$url = $this->helper->getQuery($options);

		$results = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$results = $res->d->results;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		}			
//		// saves parameters into session
//		BFCHelper::setSearchOnSellParamsSession($params);

//		echo "<pre>params";
//		echo print_r($params);
//		echo "</pre>";
//echo "<pre>";
//		echo print_r($results);
//		echo "</pre>";		
//		if (isset($ordering)) {
//			switch (strtolower($ordering)) {
//				case 'price':
//					usort($results, function($a,$b) use ( $ordering, $direction) {
//						return BFCHelper::orderBy($a->Resource, $b->Resource, 'MinPrice', $direction);
//					});
//					break;
//				case 'created':
//					usort($results, function($a,$b) use ( $ordering, $direction) {
//						return BFCHelper::orderBy($a->Resource, $b->Resource, 'Created', $direction);
//					});
//					break;
//				case 'distancefromsea':
//					usort($results, function($a,$b) use ( $ordering, $direction) {
//						return BFCHelper::orderBy($a->Resource, $b->Resource, 'DistanceFromSea', $direction);
//					});
//					break;
//				case 'distancefromcenter':
//					usort($results, function($a,$b) use ( $ordering, $direction) {
//						return BFCHelper::orderBy($a->Resource, $b->Resource, 'DistanceFromCenter', $direction);
//					});
//					break;
//			}
//		}
								
		//$this->count =  count($results);
		
//		if (! $ignorePagination && isset($start) && (isset($limit) && $limit > 0 )) {
//			$results = array_slice($results, $start, $limit);
//			$params = $this->getState('params');
//		}
		if($jsonResult)	{
			$arr = array();
			foreach($results as $result) {
				if(isset($result->XPos) && !empty($result->XPos) && ($result->IsMapVisible)  && ($result->IsMapMarkerVisible) ){
					$val= new StdClass;
					$val->Id = $result->ResourceId;
					$val->X = $result->XPos;
					$val->Y = $result->YPos;
					$val->Name = BFCHelper::getSlug($result->Name);
					$arr[] = $val;
				}
			}
			return json_encode($arr);
				
		}
		return $results;
	}

	public function getTotal()
	{

//		$params = $_SESSION['searchonsell.params'];
		$params = BFCHelper::getSearchOnSellParamsSession();
		$searchid = $params['searchid'];

		$results = null;
		$options = array(
			'path' => $this->urlSearchCount,
			'data' => array(
				'$format' => 'json',
				'topRresult' => 0,
				'lite' => 1
				)
			);
		
		$this->applyDefaultFilter($options);

		$url = $this->helper->getQuery($options);

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = (int)$res->d->GetSearchOnsellCountSimple;
		}
		return $count;
	}
	
	public function getMasterTypologiesFromService($onlyEnabled = true, $language='') {
		$options = array(
				'path' => $this->urlMasterTypologies,
				'data' => array(
					/*'$filter' => 'IsEnabled eq true',*/
					'typeId' => '2',
					'cultureCode' => $language,
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
		$session = JFactory::getSession();
		$typologies = $session->get('getMasterTypologiesonsell', null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($typologies==null) {
			$typologies = $this->getMasterTypologiesFromService($onlyEnabled);
			$session->set('getMasterTypologiesonsell', $typologies, 'com_bookingforconnector');
		}
		return $typologies;
	}

	public function populateState($ordering = NULL, $direction = NULL) {


		$curr_order = BFCHelper::getSession('filter_order', '', 'searchonsell');
		$filter_order = BFCHelper::getVar('filter_order','');
		if (!empty($filter_order)) {
			BFCHelper::setSession('filter_order', $filter_order, 'searchonsell');
			$curr_order = $filter_order;
		}
		$this->ordering = $curr_order;

		$curr_dir = BFCHelper::getSession('filter_order_Dir', '', 'searchonsell');
		$filter_order_Dir = BFCHelper::getVar('filter_order_Dir','');
		
		if (!empty($filter_order_Dir) ) {
			BFCHelper::setSession('filter_order_Dir', $filter_order_Dir, 'searchonsell');
			$curr_dir = $filter_order_Dir;
		}
		$this->direction = $curr_dir;		
		$searchid = BFCHelper::getVar('searchid');
//		$session = JFactory::getSession();
		
		if (BFCHelper::getVar('newsearch') ==1 || empty($searchid)) {
						
			$searchid =  uniqid('', true);
			$this->params  = array(
				'searchid' => $searchid,
				'unitCategoryId' => BFCHelper::getVar('unitCategoryId'),
				'merchantId' => BFCHelper::getVar('merchantId',0),
				'zoneId' => BFCHelper::getVar('zoneId',0),
				'cityId' => BFCHelper::getVar('cityId',0),
				'zoneIds' => BFCHelper::getVar('zoneIds'),
				'stateIds' => BFCHelper::getVar('stateIds'),
				'regionIds' => BFCHelper::getVar('regionIds'),
				'cityIds' => BFCHelper::getVar('cityIds'),
				'locationzone' => BFCHelper::getVar('locationzone'),
				'contractTypeId' => BFCHelper::getVar('contractTypeId',0),
				'areamin' => BFCHelper::getVar('areamin'),
				'areamax' => BFCHelper::getVar('areamax'),
				'cultureCode' => BFCHelper::getVar('cultureCode'),
				'pricemin' => BFCHelper::getVar('pricemin'),
				'pricemax' => BFCHelper::getVar('pricemax'),
				'roomsmin' => BFCHelper::getVar('roomsmin'),
				'roomsmax' => BFCHelper::getVar('roomsmax'),
				'bedroomsmin' => BFCHelper::getVar('bedroomsmin'),
				'bedroomsmax' => BFCHelper::getVar('bedroomsmax'),
				'bathsmin' => BFCHelper::getVar('bathsmin'),
				'bathsmax' => BFCHelper::getVar('bathsmax'),
				'services' => BFCHelper::getVar('servicesonsell'),
				'isnewbuilding' => BFCHelper::getVar('isnewbuilding'),
				'searchType' => BFCHelper::getVar('searchType'),
				'searchtypetab' => BFCHelper::getVar('searchtypetab'),
				'randomresult' => BFCHelper::getVar('randomresult'),
				'showReserved' => BFCHelper::getVar('showReserved'),
				'points' => BFCHelper::getVar('searchType')=="1" ? BFCHelper::getVar('points') : "",
				'searchseed' => BFCHelper::getVar('seed')

			);
//			$this->setState('params',$params );	
		// saves parameters into session
		BFCHelper::setSearchOnSellParamsSession($this->params );

		} else { // try to get params from session 
			$pars = BFCHelper::getSearchOnSellParamsSession();


			$this->params  = array(
				'searchid' => $searchid,
				'contractTypeId' => $pars['contractTypeId'],
				'unitCategoryId' => $pars['unitCategoryId'],
				'merchantId' => $pars['merchantId'],
				'zoneId' => BFCHelper::getVar('zoneId',0),
				'cityId' => (!empty($pars['cityId']))?$pars['cityId']:0,
				'zoneIds' => (!empty($pars['zoneIds']))?$pars['zoneIds']:0,
//				'locationzone' =>  BFCHelper::getVar('zoneId'),
				'areamin' => $pars['areamin'],
				'areamax' => $pars['areamax'],
				'cultureCode' => $pars['cultureCode'],
				'pricemin' => $pars['pricemin'],
				'pricemax' => $pars['pricemax'],
				'roomsmin' => $pars['roomsmin'],
				'roomsmax' => $pars['roomsmax'],
				'bedroomsmin' => $pars['bedroomsmin'],
				'bedroomsmax' => $pars['bedroomsmax'],
				'bathsmin' => $pars['bathsmin'],
				'bathsmax' => $pars['bathsmax'],
				'searchType' => $pars['searchType'],
				'points' => $pars['points'],
				'services' => $pars['services'],
				'isnewbuilding' => $pars['isnewbuilding'],
				'randomresult' => $pars['randomresult'],
				'showReserved' => $pars['showReserved'],
				'searchseed' => $pars['searchseed'],
				'filters' => BFCHelper::getVar('filters', ((!empty($pars['filters']))?$pars['filters']:''))
			);
//			$this->setState('params', $params);
		}

//		$filter_order = BFCHelper::getVar('filter_order');
//		$filter_order_Dir = BFCHelper::getVar('filter_order_Dir');
//		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems($ignorePagination = false, $jsonResult = false, $start=0)
	{
		 
		 
//		 $currpage = (get_query_var('page')) ? get_query_var('page') : 1;
			$page = bfi_get_current_page() ;
			$items = $this->getSearchResults(
			(absint($page)-1)*$this->itemPerPage,
			$this->itemPerPage,
			$this->ordering, 
			$this->direction,
			$ignorePagination,
			$jsonResult
		);
		return $items;
	}
}
endif;