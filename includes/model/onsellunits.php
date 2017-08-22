<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelOnSellUnits' ) ) :

class BookingForConnectorModelOnSellUnits
{
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $resourcesCount = 0;
	private $urlGetResourcesByIds = null;
	private $urlResourceonsells = null;
	private $urlCategoryPriceMqAverages = null;
	private $urlAvailableLocationsAverages = null;
	private $urlGetPriceAverages = null;
	private $urlGetPriceHistory = null;
	private $urlLatestResources = null;
	private $urlLatestResourcesCount = null;
	private $urlLastLocationZoneOnsell = null;
	private $urlServices = null;
	private $urlGetPriceMqAverageLastYear = null;
	private $TypeId = 2; // default value for product on sell
	private $params = null;
	private $itemPerPage = null;
	private $ordering = null;
	private $direction = null;

	
	private $helper = null;
	
	public function __construct($config = array())
	{
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_API_KEY);
		$this->urlResources = '/ResourceonsellView';
		$this->urlResourcesCount = '/ResourceonsellView';
		$this->resourcesCount = 0;
		$this->urlGetResourcesByIds = '/GetResourceonsellsByIdsSimple';
		$this->urlResourceonsells = '/ResourceonsellView';
		$this->urlCategoryPriceMqAverages = '/GetCategoryPriceAverages';
		$this->urlAvailableLocationsAverages = '/GetAvailableLocations';
		$this->urlGetPriceAverages = '/GetPriceAverages';
		$this->urlGetPriceHistory = '/GetPriceHistory';
		$this->urlLatestResources = '/GetLastOnSellUnits';
		$this->urlLatestResourcesCount = '/GetLastOnSellUnitsCount';
		$this->urlLastLocationZoneOnsell = '/GetLastLocationZoneOnsell';
		$this->urlServices = '/GetServicesForSearch';
		$this->urlGetPriceMqAverageLastYear = '/GetLastYearHistoryMqByMonth';
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
		
		$masterTypologyId = $params['masterTypeId'];
		
		$filter = '';
		// get only enabled merchants because disabled are of no use
//		$this->helper->addFilter($filter, 'Enabled eq true', 'and');
//		$this->helper->addFilter($filter, 'Merchant/Enabled eq true', 'and');

		if (isset($masterTypologyId) && $masterTypologyId > 0) {
			$this->helper->addFilter($filter, 'MasterTypologyId eq ' . $masterTypologyId, 'and');
		}
		
		if ($filter!='')
			$options['data']['$filter'] = $filter;

	}


	public  function GetResourcesByIds($listsId,$language='') {
		$options = array(
				'path' => $this->urlGetResourcesByIds,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'ids' => '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json',
					//'$select' => 'Area,Rooms,Description,MerchantId,MerchantTypeId,ResourceId,MerchantName,LocationName,AddressData,ImageUrl,Logo,XGooglePos,YGooglePos,LocationZone,IsNewBuilding,ForegroundExpiration,HighlightExpiration,ShowcaseExpiration,PriceVariation,IsReservedPrice,Created,IsAddressVisible,IsForeground'
					//'$select' => 'Area,Rooms,Description,MerchantId,ResourceId,MerchantName,LocationName,Address,ImageUrl,LogoUrl,XPos,YPos,LocationZone,IsNewBuilding,IsReservedPrice,IsAddressVisible,IsShowcase,IsForeground,AddedOn,IsHighlight,MainMerchantCategoryId'

				)
			);
		$url = $this->helper->getQuery($options);
	
		$resources = null;
	
		$r = $this->helper->executeQuery($url,"POST");
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


	public  function getServicesForSearchOnSell($language='') {
		$options = array(
				'path' => $this->urlServices,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'typeId' => 2
//					'$filter' => 'Enabled eq true and IsForSell eq true and IsForSearchSell eq true ',
//					'$filter' => 'Enabled eq true and Filterable eq true ',
//					'$orderby' => 'Order',
//					'$select' => 'ServiceId,Name'

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
	
		return $services;
	}


	public  function getResourcesOnSellGallery($language='') {
		$options = array(
				'path' => $this->urlResourceonsells,
				'data' => array(
					/*'$skip' => $start,*/
					'$top' => 3,
					'$format' => 'json',
					//'$filter' => 'Enabled eq true and ShowcaseExpiration ge datetime\'' .date("Y-m-d") . '\' ',
					'$filter' => 'Enabled eq true',
					//'$orderby' => 'ShowcaseExpiration desc',
					'$select' => 'MerchantId,ResourceId,MerchantName,LocationName,ImageUrl,LocationZone,CategoryName,Name'

				)
			);

		$url = $this->helper->getQuery($options);
	
		$resources = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
			
				$db   = JFactory::getDBO();
				$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
				$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
				$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
///		$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

			foreach( $resources as $resource ) {
				$resourceName = BFCHelper::getLanguage($resource->Name, $language);
				if ($itemId<>0)
					$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
				else
					$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
				
				$resource->Url =  $route;

			}
			try {				
				shuffle($resources);
			} catch (Exception $e) {
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	
		return $resources;
	}

	public  function getResourcesOnSellShowcase($language='') {
		$options = array(
				'path' => $this->urlResourceonsells,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json',
					//'$filter' => 'Enabled eq true and ShowcaseExpiration ge datetime\'' .date("Y-m-d") . '\' ',
					'$filter' => 'Enabled eq true and IsShowcaseHP eq true ',
					//'$orderby' => 'ShowcaseExpiration desc',
					'$select' => 'MerchantId,ResourceId,MerchantName,LocationName,ImageUrl,LocationZone,CategoryName,Name'

				)
			);

		$url = $this->helper->getQuery($options);
	
		$resources = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
			
				$db   = JFactory::getDBO();
				$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
				$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
				$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
///		$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

			foreach( $resources as $resource ) {
				$resourceName = BFCHelper::getLanguage($resource->Name, $language);
				if ($itemId<>0)
					$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
				else
					$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
	
				$resource->Url =  $route;

			}
			try {				
				shuffle($resources);
			} catch (Exception $e) {
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	
		return $resources;
	}

	public  function getCategoryPriceMqAverages($language='',$locationid) {
		$options = array(
				'path' => $this->urlCategoryPriceMqAverages,
				'data' => array(
					'$format' => 'json',
					'start' => '',
					'end' => '',
					'locationzone' => '',
					/*'$skip' => $start,
					'$top' => $limit,*/
					//'$filter' => 'Enabled eq true and ShowcaseExpiration ge datetime\'' .date("Y-m-d") . '\' ',
					//'$orderby' => 'ShowcaseExpiration desc',
					//'$select' => 'UnitCategoryName,LocationAndUnitCategoryAverage,UnitCategoryAverage'

				)
			);
		if (isset($locationid) && $locationid <> 0) {
			$options['data']['locationid'] = $locationid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}

		$url = $this->helper->getQuery($options);
	
		$resources = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
///		$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

			if (!empty($resources)){
			foreach( $resources as $resource ) {
				$resourceName = BFCHelper::getLanguage($resource->Name, $language);
				$resource->Url =  JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
		//				LocationID
						if (!empty($resource->CityId)){
							$resource->LocationId = $resource->CityId;
						}
		//				UnitCategoryID
						if (!empty($resource->CategoryId)){
							$resource->UnitCategoryID = $resource->CategoryId;
						}
		//				UnitCategoryName
						if (!empty($resource->CategoryName)){
							$resource->UnitCategoryName = $resource->CategoryName;
						}
		//				LocationZone
						if (!empty($resource->ZoneName)){
							$resource->LocationZone = $resource->ZoneName;
						}
		//				LocationAndUnitCategoryAverage
						if (!empty($resource->ZoneCategoryAverage)){
							$resource->LocationAndUnitCategoryAverage = $resource->ZoneCategoryAverage;
						}
		//				UnitCategoryAverage
						if (!empty($resource->CategoryAverage)){
							$resource->UnitCategoryAverage = $resource->CategoryAverage;
						}
				}
			}
		}

	
		return $resources;
	}
	

	public  function getAvailableLocationsAverages($language='') {
		$options = array(
				'path' => $this->urlAvailableLocationsAverages,
				'data' => array(
					'$format' => 'json',
					'start' => '',
					'end' => '',
					'$orderby' => 'CityWeight asc,Name asc'
					/*'$orderby' => 'Weight desc,Name asc',			*/
					/*'$skip' => $start,
					'$top' => $limit,*/
					//'$filter' => 'Enabled eq true and ShowcaseExpiration ge datetime\'' .date("Y-m-d") . '\' ',
					//'$orderby' => 'ShowcaseExpiration desc',
					//'$select' => 'UnitCategoryName,LocationAndUnitCategoryAverage,UnitCategoryAverage'

				)
			);
		$url = $this->helper->getQuery($options);
	
		$locations = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$locations = $res->d->results;
			}elseif(!empty($res->d)){
				$locations = $res->d;
			}
			
			if (!empty($locations)){
				foreach( $locations as $location ) {
					if (!empty($location->CityId)){
						$location->LocationID = $location->CityId;
					}
	//				LocationZone
					if (!empty($location->ZoneName)){
						$location->LocationZone = $location->ZoneName;
					}
				}
			}

///		$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

//			foreach( $locations as $location ) {
//				$resourceName = BFCHelper::getLanguage($location->Name, $language);
//			}
		}
	
		return $locations;
	}
	public  function getPriceAverages($language='',$locationid ,$unitcategoryid) {
//		$formatDate = 'Ymd';
//		$startDate = new DateTime(); // returns 09/15/2007
//		$endDate = new DateTime(); // returns 09/15/2007
//		$endDate->modify('-1 month'); 

		$options = array(
				'path' => $this->urlGetPriceAverages,
				'data' => array(
					'$format' => 'json',
//					'start' => '\'' . $startDate->format($formatDate) . '\'',
//					'end' => '\'' . $endDate->format($formatDate) . '\'',
					'contracttype' => 0,
					'stattype' => 1,
					//'$orderby' => 'Weight desc,Name asc',			
					/*'$skip' => $start,
					'$top' => $limit,*/
					//'$filter' => 'Enabled eq true and ShowcaseExpiration ge datetime\'' .date("Y-m-d") . '\' ',
					//'$orderby' => 'ShowcaseExpiration desc',
					//'$select' => 'UnitCategoryName,LocationAndUnitCategoryAverage,UnitCategoryAverage'

				)
			);
		if (isset($locationid) && $locationid > 0) {
			$options['data']['locationid'] = $locationid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}
		if (isset($locationid) && $locationid > 0) {
			$options['data']['unitcategoryid'] = $unitcategoryid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}
		$url = $this->helper->getQuery($options);
	
		$priceAverage = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$priceAverage = $res->d->results;
			}elseif(!empty($res->d)){
				$priceAverage = $res->d;
			}

			if (!empty($priceAverage)){
				foreach( $priceAverage as $resource ) {
		//				LocationID
						if (!empty($resource->CityId)){
							$resource->LocationId = $resource->CityId;
						}
		//				UnitCategoryID
						if (!empty($resource->CategoryId)){
							$resource->UnitCategoryID = $resource->CategoryId;
						}
		//				UnitCategoryName
						if (!empty($resource->CategoryName)){
							$resource->UnitCategoryName = $resource->CategoryName;
						}
		//				LocationZone
						if (!empty($resource->ZoneName)){
							$resource->LocationZone = $resource->ZoneName;
						}
		//				LocationAndUnitCategoryAverage
						if (!empty($resource->ZoneCategoryAverage)){
							$resource->LocationAndUnitCategoryAverage = $resource->ZoneCategoryAverage;
						}

				}
			}
///		$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

//			foreach( $locations as $location ) {
//				$resourceName = BFCHelper::getLanguage($location->Name, $language);
//			}
		}
	
		return $priceAverage;
	}


	public function getPriceMqAverageLastYear($language='',$locationid ,$unitcategoryid ,$contracttype = 0) {
		$options = array(
				'path' => $this->urlGetPriceMqAverageLastYear,
				'data' => array(
					'$format' => 'json',
					'contracttype' => $contracttype
				)
			);
		if (isset($locationid) && $locationid > 0) {
			$options['data']['locationid'] = $locationid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}
		if (isset($unitcategoryid) && $unitcategoryid > 0) {
			$options['data']['unitcategoryid'] = $unitcategoryid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}
		$url = $this->helper->getQuery($options);
	
		$priceMqAverageLastYear = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$priceMqAverageLastYear = $res->d->results;
			}elseif(!empty($res->d)){
				$priceMqAverageLastYear = $res->d;
			}
		}
			
		return $priceMqAverageLastYear;
	}

	public  function getPriceHistory($language='',$locationid ,$unitcategoryid) {
//		$formatDate = 'Ymd';
//		$startDate = new DateTime(); // returns 09/15/2007
//		$endDate = new DateTime(); // returns 09/15/2007
//		$endDate->modify('-1 month'); 

		$options = array(
				'path' => $this->urlGetPriceHistory,
				'data' => array(
					'$format' => 'json',
//					'start' => '\'' . $startDate->format($formatDate) . '\'',
//					'end' => '\'' . $endDate->format($formatDate) . '\'',
					'contracttype' => 0,
					'stattype' => 1,
					//'$orderby' => 'Weight desc,Name asc',			
					/*'$skip' => $start,
					'$top' => $limit,*/
					//'$filter' => 'Enabled eq true and ShowcaseExpiration ge datetime\'' .date("Y-m-d") . '\' ',
					//'$orderby' => 'ShowcaseExpiration desc',
					//'$select' => 'UnitCategoryName,LocationAndUnitCategoryAverage,UnitCategoryAverage'

				)
			);
		if (isset($locationid) && $locationid > 0) {
			$options['data']['locationid'] = $locationid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}
		if (isset($unitcategoryid) && $unitcategoryid > 0) {
			$options['data']['unitcategoryid'] = $unitcategoryid . "L"; // se è un Long il dato deve essere passato come stringa $locationid;
		}
		$url = $this->helper->getQuery($options);
	
		$priceHistory = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$priceHistory = $res->d->results;
			}elseif(!empty($res->d)){
				$priceHistory = $res->d;
			}
			if (!empty($priceHistory)){
				foreach( $priceHistory as $resource ) {
		//				LocationID
						if (!empty($resource->CityId)){
							$resource->LocationId = $resource->CityId;
						}
		//				UnitCategoryID
						if (!empty($resource->CategoryId)){
							$resource->UnitCategoryID = $resource->CategoryId;
						}
		//				UnitCategoryName
						if (!empty($resource->CategoryName)){
							$resource->UnitCategoryName = $resource->CategoryName;
						}
		//				LocationZone
						if (!empty($resource->ZoneName)){
							$resource->LocationZone = $resource->ZoneName;
						}
		//				LocationAndUnitCategoryAverage
						if (!empty($resource->ZoneCategoryAverage)){
							$resource->LocationAndUnitCategoryAverage = $resource->ZoneCategoryAverage;
						}
		//				UnitCategoryAverage
						if (!empty($resource->CategoryAverage)){
							$resource->UnitCategoryAverage = $resource->CategoryAverage;
						}
				}
			}
///		$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

//			foreach( $locations as $location ) {
//				$resourceName = BFCHelper::getLanguage($location->Name, $language);
//			}
		}
	
		return $priceHistory;
	}

	public function getResourcesFromService($start, $limit, $ordering, $direction) {// with random order is not possible to order by another field

		$params = $this->params;

		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,
					'seed' => $seed,*/
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
		

//		// adding other ordering to allow grouping
//		$options['data']['$orderby'] = "IsShowcase desc, IsForeground desc, Created  desc";
//		$options['data']['$orderby'] = 'AddedOn desc';

		if (isset($this->ordering)) {
			$options['data']['$orderby'] =  $this->ordering . ' ' . strtolower($this->direction);
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

	public function getResourcesFromFavorites() {// with random order is not possible to order by another field
		$resources = null;
		if ( BFCHelper::CountFavourites()>0){ // only if there are unit in favorites 
			$params = $this->params;

			$options = array(
					'path' => $this->urlResources,
					'data' => array(
						/*'$skip' => $start,
						'$top' => $limit,*/
						//'seed' => $seed,
						'$format' => 'json'
					)
				);
			
			$this->applyDefaultFilter($options);


			$filterFav = '';
			$tmpFav = BFCHelper::GetFavourites();

			foreach( $tmpFav as $key => $value ) {
				if( !empty( $tmpFav[ $key ] ) ){
					$this->helper->addFilter($filterFav, '(ResourceId eq '.$tmpFav[ $key ].')', 'or');
				}
			}
			if ($filterFav!='') {
				if($options['data']['$filter']!='') 
				{
					$options['data']['$filter'] .= " and (" . $filterFav . ")";
				}
			}
			// get only enabled merchants because disabled are of no use

	//		// adding other ordering to allow grouping
	//		$options['data']['$orderby'] = 'Created desc';
			$options['data']['$orderby'] = "IsShowcase desc, IsForeground desc, AddedOn  desc";

	//		if (isset($ordering)) {
	//			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
	//		}
			
			$url = $this->helper->getQuery($options);
			
			
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
//				$resources = $res->d->results ?: $res->d;
				if (!empty($res->d->results)){
					$resources = $res->d->results;
				}elseif(!empty($res->d)){
					$resources = $res->d;
				}
			}
		}
		return $resources;
	}

	public function getMapResourcesFromService($jsonResult = false) {// with random order is not possible to order by another field

		$params = $this->params;

		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'seed' => $seed,
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
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
		if($jsonResult)	{
			$arr = array();
			foreach($resources as $result) {
				if(isset($result->XPos) && !empty($result->XPos) && ($result->IsMapVisible)  && ($result->IsMapMarkerVisible) ){
					$val= new StdClass;
					$val->Id = $result->ResourceId ;
					$val->X = $result->XPos;
					$val->Y = $result->YPos;
					$arr[] = $val;
				}
			}
			return json_encode($arr);
				
		}

		return $resources;
	}


	public function getAllResources()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcesCount,
				'data' => array(
						'$format' => 'json',
						'$inlinecount' => 'allpages',
						'$filter' => 'Enabled eq true and TypeId eq ' . $this->TypeId 
				)
			);
						
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
		}

		return $count;
	}

	public function getTotalResources()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcesCount,
				'data' => array(
						'$format' => 'json',
						'$inlinecount' => 'allpages',
						'$filter' => 'Enabled eq true and TypeId eq ' . $this->TypeId 
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = 0;
			if (isset($res->d->__count)){
				$count = (int)$res->d->__count;
			}elseif(isset($res->d)){
				$count = (int)$res->d;
			}
//			$count = (int)$r;
		}

		return $count;
	}


	public function getLastLocationZoneOnsell()
	{
//		$params = $this->params;
//		$searchid = $params['searchid'];
//				
//		$session = JFactory::getSession();
		//$session->clear();

		$results = null;
		$options = array(
			'path' => $this->urlLastLocationZoneOnsell,
			'data' => array(
				'$format' => 'json',
				)
			);
		
//		$this->applyDefaultFilter($options);
//		$options['data']['locationZone'] = '';

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
//			$tmpLocationZone = array_unique(array_map(function ($i) { return $i->LocationZone; }, $results));
//			return $tmpLocationZone;
		$results=str_ireplace("(","ZZZZ (",$results);
		natcasesort($results);
		$results=str_ireplace("ZZZZ (","(",$results);


		}			
		
		return $results;
	}

	public function getLatestResourcesFromService($start, $limit, $ordering, $direction) {// with random order is not possible to order by another field

		$params = $this->params;

		$options = array(
				'path' => $this->urlLatestResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'topRresult' => 0,
					'lite' => 0,
//					'seed' => $seed,
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);		
		$locationzones = $params['locationzones'];
		if (isset($locationzones) && $locationzones !='') {
			$options['data']['locationZone'] = '\'' . $locationzones. '\'';
		}


//		// adding other ordering to allow grouping
		$options['data']['$orderby'] = 'AddedOn desc';
		if (isset($ordering)) {
			$options['data']['$orderby'] =  $ordering . ' ' . strtolower($direction);
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

	public function getTotalLatest()
	{
		$params = $this->params;
		$options = array(
				'path' => $this->urlLatestResourcesCount,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
//					'topRresult' => 0,
//					'lite' => 0,
//					'seed' => $seed,
					'$format' => 'json'
				)
		);
//		$this->applyDefaultFilter($options);
		$locationzones = $params['locationzones'];
		if (isset($locationzones) && $locationzones !='') {
			$options['data']['locationZone'] = '\'' . $locationzones. '\'';
		}

		$url = $this->helper->getQuery($options);
		$count = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = (int)$res->d->GetLastOnSellUnitsCount;
//			$count = (int)$r;
		}
		return $count;
	}

	
	public function populateState($ordering = NULL, $direction = NULL) {
//		$filter_order = JRequest::getCmd('filter_order','AddedOn');
//		$filter_order_Dir = JRequest::getCmd('filter_order_Dir','desc');
//		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseed', rand(), 'com_bookingforconnector');
//		if (!$session->has('searchseed','com_bookingforconnector')) {
		if ($searchseed ==null) {
			BFCHelper::setSession('searchseed', $searchseed, 'com_bookingforconnector');
		}
 
		$this->params  = array(
			'locationzones' => BFCHelper::getVar('locationzones'),
			'masterTypeId' => BFCHelper::getVar('masterTypeId')
		);
		
//		$input = JFactory::getApplication()->input;
//		$show_latest = $input->get( 'show_latest' );  // show_latest
//		if ($show_latest) {
//			$this->params['show_latest'] = $show_latest;
//		}
		
//		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItemsLatest()
	{
		return $this->getItems('latest');
	}

	public function getItemsFavorites()
	{
		return $this->getItems('favorites');
	}

	public function getItems($type = '')
	{
		// Get a storage key.
//		$store = $this->getStoreId();

		// Try to load the data from internal storage.
//		if (isset($this->cache[$store]))
//		{
//			return $this->cache[$store];
//		}
		$page = bfi_get_current_page() ;
		switch($type) {
			case 'latest':
				$items = $this->getLatestResourcesFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					$this->ordering,
					$this->direction
				);
				break;
			case 'favorites':
				$items = $this->getResourcesFromFavorites();
				break;
			default:
					$items = $this->getResourcesFromService(
						(absint($page)-1)*$this->itemPerPage,
						$this->itemPerPage,
						$this->ordering,
						$this->direction
					);
				break;
		}

		// Add the items to the internal cache.
//		$this->cache[$store] = $items;
//
//		return $this->cache[$store];
		return $items;
	}

	public function getStart($type = '')
	{
		$store = $this->getStoreId('getstart'.$type);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$total = $this->getTotal($type);
		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}

	public function getTotal($type = '')
	{
		switch($type) {
			case 'latest':
				return $this->getTotalLatest();
				break;
			case '':
			default:
				return $this->getTotalResources();
				break;
		}
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

	function getPaginationLatest()
	{	
		return $this->getPagination('latest');
	}
	function getPagination($type = '')
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal($type), $this->getState('list.start'), $this->getState('list.limit') );
		}
		return $this->_pagination;
	}

}
endif;