<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelResources' ) ) :
 

class BookingForConnectorModelResources
{
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $resourcesCount = 0;
	private $urlMasterTypologies = null;
	private $urlCheckAvailabilityCalendar = null;
	private $urlGetResourcesByIds = null;
	private $urlServices = null;
	private $TypeId = 1; // default value for product booking
	private $params = null;
	private $itemPerPage = null;
	private $ordering = null;
	private $direction = null;
	
	private $helper = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlResources = '/GetResources';
		$this->urlResourcesCount = '/GetResourcesCount';
		$this->resourcesCount = 0;
		$this->urlMasterTypologies = '/GetMasterTypologies';
		$this->urlCheckAvailabilityCalendar = '/CheckAvailabilityCalendarByIdList';
		$this->urlGetResourcesByIds = '/GetResourcesByIds';
		$this->urlServices = '/GetServicesForSearch';
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

	public function applyDefaultFilter(&$options) {
		$params = $this->params;
		
		$categories = $params['categories'];
		if (!empty($categories)) {
			$options['data']['productcategories'] =  BFCHelper::getQuotedString($categories);
		}
		
		$condominiumid = $params['parentProductId'];
		if (!empty($condominiumid)) {
			$options['data']['parentProductId'] =  $condominiumid;
		}

//		$filter = '';
//		// get only enabled merchants because disabled are of no use
//		$this->helper->addFilter($filter, 'Enabled eq true', 'and');
//
//		if (isset($masterTypologyId) && $masterTypologyId > 0) {
//			$this->helper->addFilter($filter, 'MasterTypologyId eq ' . $masterTypologyId, 'and');
//		}
//		
//		if ($filter!='')
//			$options['data']['$filter'] = $filter;

	}

	public  function getServicesForSearch($language='') {
		$sessionkey= 'getServicesForSearch'.$language;
		$services = BFCHelper::getSession($sessionkey);
		if ($services==null) {		
		
		
		$options = array(
				'path' => $this->urlServices,
				'data' => array(
					'$format' => 'json',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'typeId' => 1
				)
			);

		$url = $this->helper->getQuery($options);
	
		$services = null;
	
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$services = $res->d->results;
			}elseif(!empty($res->d)){
				$services = $res->d;
			}
		}
			BFCHelper::setSession($sessionkey, $services);
		}
	
		return $services;
	}


	public function getCheckAvailabilityCalendarFromService($resourcesId = null,$checkIn= null,$checkOut= null) {
		$resultCheck = '';
		if ($resourcesId==null || $checkIn ==null  || $checkOut ==null ) {
			return $resultCheck;
		}
		if ($checkIn==null) {
			$defaultDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDate(),new DateTimeZone('UTC'));
			$checkIn =  BFCHelper::getStayParam('checkin', $defaultDate);
		}
		if ($checkOut==null) {
			$checkOut =   BFCHelper::getStayParam('checkout', $checkIn->modify(BFCHelper::$defaultDaysSpan));
		}
		//calcolo le settimane necessarie

		//$ci = $params['checkin'];
		$options = array(
				'path' => $this->urlCheckAvailabilityCalendar,
				'data' => array(
					'resourcesId' => BFCHelper::getQuotedString($resourcesId) ,
					'checkin' => '\'' . $checkIn->format('Ymd') . '\'',
					'checkout' => '\'' . $checkOut->format('Ymd') . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$checkDate = $res->d->results ?: $res->d;
			$resultCheck = $checkDate->CheckAvailabilityCalendarByIdList;
		}
		
		return $resultCheck;
	}

	public function getMasterTypologiesFromService($onlyEnabled = true) {
		$options = array(
				'path' => $this->urlMasterTypologies,
				'data' => array(
					/*'$filter' => 'IsEnabled eq true',*/
					'$format' => 'json'
				)
			);
			
		if ($onlyEnabled) {
			$options['data']['$filter'] = 'IsEnabled eq true';
		}
		
		$url = $this->helper->getQuery($options);
		
		$typologies = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$typologies = $res->d->results ?: $res->d;
		}
		
		return $typologies;
	}

	public function getMasterTypologies($onlyEnabled = true) {
//		$session = JFactory::getSession();
		$typologies = BFCHelper::getSession('getMasterTypologies', null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($typologies==null) {
			$typologies = $this->getMasterTypologiesFromService($onlyEnabled);
			BFCHelper::setSession('getMasterTypologies', $typologies, 'com_bookingforconnector');
		}
		return $typologies;
	}

	public  function GetResourcesByIds($listsId,$language='') {
		$options = array(
				'path' => $this->urlGetResourcesByIds,
				'data' => array(
					'ids' => '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		$resources = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$resources = json_encode($res->d);
			}
		}
	
		return $resources;
	}


	public function getResourcesFromService($start, $limit, $ordering, $direction) {// with random order is not possible to order by another field

		$params = $this->params;
		$seed = $params['searchseed'];
		$cultureCode = $GLOBALS['bfi_lang'];

		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'seed' => $seed,
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
		

//		// adding other ordering to allow grouping
//		$options['data']['$orderby'] = 'Weight desc';
//		if (isset($ordering)) {
//			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
//		}
		
		$url = $this->helper->getQuery($options);
		
		$resources = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$resources = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
		}

		return $resources;
	}
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcesCount,
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = 0;
			$count = (int)$res->d->GetResourcesCount;
//			$count = (int)$r;
		}

		return $count;
	}
	
	public function populateState($ordering = NULL, $direction = NULL) {
//		$filter_order = JRequest::getCmd('filter_order','Name');
//		$filter_order_Dir = JRequest::getCmd('filter_order_Dir','asc');

//		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseed', rand(), 'com_bookingforconnector');
//		if (!$session->has('searchseed','com_bookingforconnector')) {
		if ($searchseed ==null) {
			BFCHelper::setSession('searchseed', $searchseed, 'com_bookingforconnector');
		}
 
		$this->params = array(
			'categories' => BFCHelper::getVar('categories'),
			'searchseed' => $searchseed
		);
		
//		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems()
	{
//		// Get a storage key.
//		$store = $this->getStoreId();
//
//		// Try to load the data from internal storage.
//		if (isset($this->cache[$store]))
//		{
//			return $this->cache[$store];
//		}

		$page = bfi_get_current_page() ;
		
		$items = $this->getResourcesFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					$this->ordering,
					$this->direction
		);

//		// Add the items to the internal cache.
//		$this->cache[$store] = $items;

//		return $this->cache[$store];
		return $items;
	}

	public function getResourcesForSearch($text, $start, $limit, $ordering, $direction) {
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}
		
		//$this->applyDefaultFilter($options);
		
		$filter = '';

		// get only enabled merchants because disabled are of no use
		$this->helper->addFilter($filter, 'Enabled eq true', 'and');

		if (isset($text)) {
			$this->helper->addFilter(
				$filter, 
				'substringof(\'' . $text . '\',Name) eq true', 
				'and'
			);
		}
				
		if ($filter!='')
			$options['data']['$filter'] = $filter;

		// adding other ordering to allow grouping
		$options['data']['$orderby'] = 'Rating desc';
		if (isset($ordering)) {
			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
		}
		
		$url = $this->helper->getQuery($options);
		
		$resources = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$resources = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
		}

		return $resources;
	}	
}
endif;