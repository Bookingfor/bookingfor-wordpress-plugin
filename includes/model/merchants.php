<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
 if ( ! class_exists( 'BookingForConnectorModelMerchants' ) ) :
 
class BookingForConnectorModelMerchants
{
	private $urlMerchants = null;
	private $urlMerchantsCount = null;
	private $merchantsCount = 0;
	private $urlMerchantTypes = null;
	private $urlMerchantCategories = null;
	private $urlMerchantCategory = null;
	private $urlMerchantGroups = null;
	private $urlAllMerchants = null;
	private $urlAllMerchantsCount = null;
	private $urlLocationZones = null;
	private $urlLocations = null;
	private $urlGetMerchantsByIds = null;
	private $urlCreateMerchantAndUser = null;
	private $urlMerchantCategoriesRequest = null;
	private $urlGetServicesByMerchantsCategoryId = null;
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
		$this->urlMerchants = '/GetMerchantsByCategoryIds';
		$this->urlMerchantsCount = '/GetMerchantsByCategoryIds/$count';
		$this->merchantsCount = 0;
		$this->urlMerchantTypes = '/MerchantTypes';		
		$this->urlMerchantCategories = '/GetMerchantsCategory';
		$this->urlMerchantCategory = '/MerchantCategories(%d)';
		$this->urlMerchantGroups = '/MerchantGroups';
		$this->urlAllMerchants = '/Merchants';
		$this->urlAllMerchantsCount = '/Merchants/$count';
		$this->urlLocations = '/GeographicZones';//'/Cities'; //'/Locations';
		$this->urlLocationZones = '/GeographicZones';//'/LocationZones';
		$this->urlGetMerchantsByIds = '/GetMerchantsByIdsExt';
		$this->urlCreateMerchantAndUser = '/CreateMerchantAndUser';
		$this->urlMerchantCategoriesRequest = '/GetMerchantsCategoryForRequest';
		$this->urlGetServicesByMerchantsCategoryId = '/GetServicesByMerchantsCategoryId';
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
		$startswith = $params['startswith'];
		$typeId = $params['typeId'];
		$rating = $params['rating'];
		$categoryIds = $params['categoryId'];
		$cityids = $params['cityids'];
		
		$filter = '';
		// get only enabled merchants because disabled are of no use
		//$this->helper->addFilter($filter, 'Enabled eq true', 'and');

//		if (isset($typeId) && $typeId > 0) {
//			$this->helper->addFilter(
//				$filter, 
//				'MerchantTypeId eq ' . $typeId, 
//				'and'
//			);
//		}
		
		if (isset($rating) && $rating > 0) {
			$this->helper->addFilter(
				$filter, 
				'Rating eq ' . $rating, 
				'and'
			);
		}

		if ($filter!='')
			$options['data']['$filter'] = $filter;

		if (!empty($categoryIds)){
			$strCategoryIds = "";
			if(is_array($categoryIds)){
				$strCategoryIds = implode('|',$categoryIds);
			}else{
				$strCategoryIds = $categoryIds;
			}
			$options['data']['categoryIds'] = '\''.str_replace(",","|",$strCategoryIds).'\'';
		}
		if (count($cityids) > 0){
			$options['data']['cityids'] = '\''.implode(',',$cityids).'\'';
		}

		//passo sempre il dato anche quando è vuoto altrimenti non mi passa nessun valore
		$options['data']['startswith'] = '\'' . $startswith . '\'';
	}
	
	public function setMerchantAndUser($customerData = NULL, $password = NULL, $merchantType = 0, $merchantCategory = 0, $company = NULL, $userPhone = NULL, $webSite = NULL) {
		$options = array(
				'path' => $this->urlCreateMerchantAndUser,
				'data' => array(
					'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
					'password' => BFCHelper::getQuotedString($password),
					'company' => BFCHelper::getQuotedString($company),
					'userPhone' => BFCHelper::getQuotedString($userPhone),
					'webSite' => BFCHelper::getQuotedString($webSite),
					'merchantType' => $merchantType,
					'merchantCategory' => $merchantCategory,
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		
		$userId = -1;
		
		//$r = $this->helper->executeQuery($url);
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			$tmpuserId = $res->d->results ?: $res->d;
			$userId = $tmpuserId->CreateMerchantAndUser;
		}
		
		return $userId;
	}

//	public function getMerchantTypes() {
//		$options = array(
//				'path' => $this->urlMerchantTypes,
//				'data' => array(
//					'$filter' => 'Enabled eq true',
//					'$format' => 'json'
//				)
//			);
//		$url = $this->helper->getQuery($options);
//		
//		$types = null;
//		
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
////			$types = $res->d->results ?: $res->d;
//			if (!empty($res->d->results)){
//				$types = $res->d->results;
//			}elseif(!empty($res->d)){
//				$types = $res->d;
//			}
//		}
//		
//		return $types;
//
//	}

	public function getLocationZonesFromService($locationId = NULL) {
		$data=array(
					'$select' => 'GeographicZoneId,Name,Order',			
					'$orderby' => 'Name',			
					'$format' => 'json'
				);
		if(!empty($locationId)) {
			$data['$filter']="CityId eq " . $locationId;
		}
		$options = array(
				'path' => $this->urlLocationZones,
				'data' => $data
			);
		$url = $this->helper->getQuery($options);
		
		$locationZones = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$locationZones = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$locationZones = $res->d->results;
			}elseif(!empty($res->d)){
				$locationZones = $res->d;
			}
			if (!empty($locationZones)){
				foreach( $locationZones as $resource ) {
		//				LocationID
						if (!empty($resource->CityId)){
							$resource->LocationId = $resource->CityId;
						}
						if (!empty($resource->GeographicZoneId)){
							$resource->LocationZoneID = $resource->GeographicZoneId;
						}
						if (!empty($resource->Order)){
							$resource->Weight = $resource->Order;
						}
						
				}
			}
		}
		
		return $locationZones;

	}

	public function getLocationZones($locationId = NULL,$jsonResult = false) {
//		$session = JFactory::getSession();
		$strlocationId = "";
		if(isset($locationId)){
			$strlocationId = $locationId;
		}
		$locationZones = BFCHelper::getSession('getLocationZones' . $strlocationId, null , 'com_bookingforconnector');
		if ($locationZones==null) {
			$locationZones = $this->getLocationZonesFromService($strlocationId);
			BFCHelper::setSession('getLocationZones' . $strlocationId, $locationZones, 'com_bookingforconnector');
//		} else {
//			$locationZones = $this->getLocationZonesFromService($locationId);
		}
		if($jsonResult)	{
			$arr = array();
			if (!empty($locationZones)){
				foreach($locationZones as $result) {
					if (!empty($result->GeographicZoneId)){
						$result->LocationZoneID = $resource->GeographicZoneId;
					}
					if(isset($result->GeographicZoneId) && !empty($result->Name) && isset($result->Order)){
						$val= new StdClass;
						$val->LocationZoneID = $result->GeographicZoneId ;
						$val->Name = $result->Name;
						$val->Weight = $result->Order;
						$arr[] = $val;
					}
				}
			}
			return json_encode($arr);
		}
		return $locationZones;
	}
	
	public function getLocationsFromService() {
		$options = array(
				'path' => $this->urlLocations,
				'data' => array(
					//'$filter' => 'Enabled eq true',
//					'$orderby' => 'Weight desc,Name',			
					'$select' => 'CityId,Name',
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		
		$locations = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$locationZones = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$locations = $res->d->results;
			}elseif(!empty($res->d)){
				$locations = $res->d;
			}
		}
		
		return $locations;

	}

	public function getLocations() {
//		$session = JFactory::getSession();
		$locations = BFCHelper::getSession('getPortalLocations', null , 'com_bookingforconnector');
//		$locations=null;
		if ($locations==null) {
			$locations = $this->getLocationsFromService();
			BFCHelper::setSession('getPortalLocations', $locations, 'com_bookingforconnector');
		}
		return $locations;
	}

	public function getLocationById($id) {
		$options = array(
				'path' => $this->urlLocations . "(" . $id . "L)",
				'data' => array(
					//'$orderby' => 'Weight desc,Name',			
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		
		$locations = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$locationZones = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$locations = $res->d->results;
			}elseif(!empty($res->d)){
				$locations = $res->d;
			}
		}
		
		return $locations;

	}

	public function getMerchantCategoriesFromService() {
		
		$options = array(
				'path' => $this->urlMerchantCategories,
				'data' => array(
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$categoriesFromService = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$categories = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$categoriesFromService = $res->d->results;
			}elseif(!empty($res->d)){
				$categoriesFromService = $res->d;
			}
		}
	
		return $categoriesFromService;
	}
	
	public function getMerchantCategories() {
//		$session = JFactory::getSession();
		$categories = BFCHelper::getSession('getMerchantCategories', null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($categories==null) {
			$categories = $this->getMerchantCategoriesFromService();
			BFCHelper::setSession('getMerchantCategories', $categories, 'com_bookingforconnector');
		}
		return $categories;
	}
	
//	public function getMerchantCategoriesForRequest($language='') {
//		$session = JFactory::getSession();
//		$categories = BFCHelper::getSession('getMerchantCategoriesForRequest', null , 'com_bookingforconnector');
////		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
//		if ($categories==null) {
//			$options = array(
//					'path' => $this->urlMerchantCategories,
//					'data' => array(
//							'$filter' => 'Enabled eq true and IsForRequest eq true ',
//							'$format' => 'json'
//					)
//			);
//			$url = $this->helper->getQuery($options);
//		
//			$categoriesFromService = null;
//		
//			$r = $this->helper->executeQuery($url);
//			if (isset($r)) {
//				$res = json_decode($r);
//				//$categoriesFromService = $res->d->results ?: $res->d;
//				if (!empty($res->d->results)){
//					$categoriesFromService = $res->d->results;
//				}elseif(!empty($res->d)){
//					$categoriesFromService = $res->d;
//				}
//			}
//			$categories=array();
//			if (!empty($categoriesFromService)){
//				foreach( $categoriesFromService as $category) {
//					$newCat = new StdClass;
//					$newCat->MerchantCategoryId =  $category->MerchantCategoryId;
//					$newCat->Name = BFCHelper::getLanguage($category->Name, $language);
//
//	//				$newCat = array(
//	//					'MerchantCategoryId' => $category->MerchantCategoryId,
//	//					'Name' => BFCHelper::getLanguage($category->Name, $language)
//	//					);
//					$categories[]=$newCat;
//				}
//
//				BFCHelper::setSession('getMerchantCategoriesForRequest', $categories, 'com_bookingforconnector');
//			}
//		}
//		return $categories;
//	}

	public function getMerchantCategoriesForRequest($language='') {
//		$session = JFactory::getSession();
		$categories = isset($_SESSION['getMerchantCategoriesForRequest'.$language])?$_SESSION['getMerchantCategoriesForRequest'.$language]:null;// BFCHelper::getSession('getMerchantCategoriesForRequest'.$language, null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($categories==null) {
			$options = array(
					'path' => $this->urlMerchantCategoriesRequest,
					'data' => array(
//							'$filter' => 'Enabled eq true and IsForRequest eq true ',
//							'$orderby' => 'Order asc ',
							'cultureCode' => BFCHelper::getQuotedString($language),
							'$format' => 'json'
					)
			);
			$url = $this->helper->getQuery($options);
		
			$categoriesFromService = null;
		
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				//$categoriesFromService = $res->d->results ?: $res->d;
				if (!empty($res->d->results)){
					$categoriesFromService = $res->d->results;
				}elseif(!empty($res->d)){
					$categoriesFromService = $res->d;
				}
			}
			$categories=$categoriesFromService;
//			$categories=array();
			if (!empty($categoriesFromService)){
//				foreach( $categoriesFromService as $category) {
//					$newCat = new StdClass;
//					$newCat->MerchantCategoryId =  $category->MerchantCategoryId;
//					$newCat->Name = BFCHelper::getLanguage($category->Name, $language);
//
//	//				$newCat = array(
//	//					'MerchantCategoryId' => $category->MerchantCategoryId,
//	//					'Name' => BFCHelper::getLanguage($category->Name, $language)
//	//					);
//					$categories[]=$newCat;
//				}
				$_SESSION['getMerchantCategoriesForRequest'.$language]=$categories;
//				BFCHelper::setSession('getMerchantCategoriesForRequest'.$language, $categories, 'com_bookingforconnector');
			}
		}
		return $categories;
	}

	public function getMerchantCategory($merchanCategoryId) {
		$options = array(
				'path' => sprintf($this->urlMerchantCategory, $merchanCategoryId),
				'data' => array(
						'$filter' => 'Enabled eq true',
						'$expand' => 'Services',
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$categories = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$categories = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$categories = $res->d->results;
			}elseif(!empty($res->d)){
				$categories = $res->d;
			}
		}
	
		return $categories;
	}

	public function getServicesByMerchantsCategoryId($merchantCategoryId,$language='') {
//		$session = JFactory::getSession();
		$services = BFCHelper::getSession('getServicesByMerchantsCategoryId'.$language.$merchantCategoryId, null , 'com_bookingforconnector');			
		if ($services==null) {		
			$options = array(
					'path' => $this->urlGetServicesByMerchantsCategoryId,
					'data' => array(
							'merchantCategoryId' => $merchantCategoryId,
							'cultureCode' => BFCHelper::getQuotedString($language),
							'$format' => 'json'
					)
			);
			$url = $this->helper->getQuery($options);
		
			$services = null;
		
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$services = $res->d->results;
				}elseif(!empty($res->d)){
					$services = $res->d;
				}
			}
			BFCHelper::setSession('getServicesByMerchantsCategoryId'.$language.$merchantCategoryId, $services, 'com_bookingforconnector');
		}
		return $services;
	}

	public function getMerchantGroupsFromService() {
		$options = array(
				'path' => $this->urlMerchantGroups,
				'data' => array(
						'$filter' => 'Enabled eq true',
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$categories = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$categories = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$categories = $res->d->results;
			}elseif(!empty($res->d)){
				$categories = $res->d;
			}
		}
	
		return $categories;
	}
	
	public function getMerchantGroups() {
//		$session = JFactory::getSession();
		$categories = BFCHelper::getSession('getMerchantGroups', null , 'com_bookingforconnector');
		if ($categories==null) {
			$categories = $this->getMerchantGroupsFromService();
			BFCHelper::setSession('getMerchantGroups', $categories, 'com_bookingforconnector');
		}
		return $categories;
	}

	public  function getMerchantsByIds($listsId,$language='') {
		$options = array(
				'path' => $this->urlGetMerchantsByIds,
				'data' => array(
					'ids' => '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
	
		$merchants = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$merchants = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$merchants = json_encode($res->d);
			}
		}
	
		return $merchants;
	}

		public function getMerchantByCategoryId($merchanCategoryId) {// with random order is not possible to order by another field

		$options = array(
				'path' => $this->urlMerchants,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
//					,'$select' => 'MerchantId,Name,Rating'
				)
			);
		
		$options['data']['categoryIds'] = '\''.$merchanCategoryId .'\'';
		$startswith ="";
		$options['data']['startswith'] = '\'' . $startswith . '\'';

		$url = $this->helper->getQuery($options);

		$merchants = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$merchants = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$merchants = $res->d->results;
			}elseif(!empty($res->d)){
				$merchants = $res->d;
			}
			if(!empty($merchants)){
				shuffle($merchants);
			}

		}

		return $merchants;
	}
	public function getMerchantsFromService($start, $limit, $ordering, $direction) {// with random order is not possible to order by another field

		$params = $this->params;
		$seed = $params['searchseed'];

		$options = array(
				'path' => $this->urlMerchants,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'seed' => $seed,
					'$format' => 'json'
//					,'$select' => 'MerchantId,Name,Rating'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
		

		// adding other ordering to allow grouping
		//$options['data']['$orderby'] = 'Rating desc';
		if (isset($ordering) && !empty($ordering)) {
			$options['data']['$orderby'] =  $ordering . ' ' . strtolower($direction);
		}
		
		$url = $this->helper->getQuery($options);

		$merchants = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$merchants = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$merchants = $res->d->results;
			}elseif(!empty($res->d)){
				$merchants = $res->d;
			}
//			if(!empty($merchants) && empty($ordering)){
//				shuffle($merchants);
//			}

		}

		return $merchants;
	}
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlMerchantsCount,
				'data' => array()
			);
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
		}

		return $count;
	}
	
	public function populateState($ordering = NULL, $direction = NULL) {
//		$filter_order = JRequest::getCmd('filter_order');
//		$filter_order_Dir = JRequest::getCmd('filter_order_Dir');

//		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseed', rand(), 'com_bookingforconnector');
//		if (!$session->has('searchseed','com_bookingforconnector')) {
		if (empty($_SESSION['com_bookingforconnector'.'searchseed'])) {
			BFCHelper::setSession('searchseed', $searchseed, 'com_bookingforconnector');
		}
//	$filter_order = BFCHelper::getVar('filter_order','');
//	if(!empty($filter_order)){
//		$model->setOrdering($filter_order);
//	}
//	
//	$filter_order_Dir = BFCHelper::getVar('filter_order_Dir','');
//	
//	if(!empty($filter_order_Dir)){
//		$model->setDirection($filter_order_Dir);
//	}

		$curr_order = BFCHelper::getSession('filter_order', '', 'com_bookingforconnector_merchantlist');
		$filter_order = BFCHelper::getVar('filter_order','');
		if (!empty($filter_order)) {
			BFCHelper::setSession('filter_order', $filter_order, 'com_bookingforconnector_merchantlist');
			$curr_order = $filter_order;
		}
		$this->ordering = $curr_order;

		$curr_dir = BFCHelper::getSession('filter_order_Dir', '', 'com_bookingforconnector_merchantlist');
		$filter_order_Dir = BFCHelper::getVar('filter_order_Dir','');
		
		if (!empty($filter_order_Dir) ) {
			BFCHelper::setSession('filter_order_Dir', $filter_order_Dir, 'com_bookingforconnector_merchantlist');
			$curr_dir = $filter_order_Dir;
		}
		$this->direction = $curr_dir;		
		$this->params = array(
			'typeId' => BFCHelper::getVar('typeId'),
			'startswith' => BFCHelper::getVar('startswith',''),
//			'show_rating' => BFCHelper::getVar('show_rating','1'),
//			'default_display' => BFCHelper::getVar('default_display','0'),
			'categoryId' => BFCHelper::getVar('categoryId'),
			'rating' => BFCHelper::getVar('rating'),
			'cityids' => BFCHelper::getVar('cityids'),
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
		$items = $this->getMerchantsFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					$this->ordering,
					$this->direction
		);

//		// Add the items to the internal cache.
//		$this->cache[$store] = $items;
//
//		return $this->cache[$store];
		return $items;
	}
	
	
	public function getItemsJson($jsonResult=false)
	{
		// Get a storage key.
		$items = $this->getLocationZones(
			((int)BFCHelper::getVar('locationId','0')),
			$jsonResult
		);

		// Add the items to the internal cache.
		//$this->cache[$store] = $items;

		//return $this->cache[$store];
		return $items;
	}

	public function getMerchantsForSearch($text, $start, $limit, $ordering, $direction) {
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlAllMerchants,
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
		
		$merchants = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$merchants = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$merchants = $res->d->results;
			}elseif(!empty($res->d)){
				$merchants = $res->d;
			}
		}

		return $merchants;
	}
	
}
endif;