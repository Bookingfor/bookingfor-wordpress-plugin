<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelMerchantDetails' ) ) :

class BookingForConnectorModelMerchantDetails
{
	private $urlMerchant = null;
	private $urlMerchantResources = null;
	private $urlMerchantResourcesCount = null;
	private $helper = null;
	private $urlMerchantOnSellUnits = null;
	private $urlMerchantOnSellUnit = null;
	private $urlMerchantOnSellUnitsCount = null;
	private $urlMerchantRating = null;
	private $urlMerchantRatingCount = null;
	private $urlMerchantRatingAverage = null;
	private $urlMerchantMerchantGroups = null;
	private $urlMerchantCounter = null;
	private $urlMerchantOffers = null;
	private $urlMerchantOffer = null;
	private $urlMerchantOffersCount = null;
//	private $urlMerchantPackages = null;
//	private $urlMerchantPackagesCount = null;
//	private $urlMerchantPackage = null;
	private $merchantid = null;
	private $itemPerPage = 10;

	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlMerchant = '/GetMerchantsById';
		$this->urlMerchantResources = '/GetMerchantResources';
		$this->urlMerchantResourcesCount = '/GetMerchantResourcesCount';
		$this->urlMerchantOnSellUnits = '/GetResourceonsellsByMerchantId';
		$this->urlMerchantOnSellUnitsCount = '/GetResourceonsellsByMerchantIdCount';
		$this->urlMerchantOnSellUnit = '/ResourceonsellView(%d)';
		$this->urlMerchantRatingAverage = '/GetMerchantAverage';
		$this->urlMerchantMerchantGroups = '/GetMerchantMerchantGroups';
		$this->urlMerchantCounter = '/MerchantCounter';

//		$this->urlMerchantRating = '/RatingsView';
//		$this->urlMerchantRatingCount = '/RatingsView/$count';
		$this->urlMerchantRating = '/GetReviews';
		$this->urlMerchantRatingCount = '/GetReviewsCount';

		$this->urlMerchantOffers = '/GetVariationPlans';
		$this->urlMerchantOffersCount = '/GetVariationPlansCount';
		$this->urlMerchantOffer = '/GetVariationPlanById';
		
//		$this->urlMerchantPackages = '/GetPackages';
//		$this->urlMerchantPackagesCount = '/GetPackagesCount';
//		$this->urlMerchantPackage = '/GetPackageById';
}


	public function setMerchantId($merchantId) {
		if(!empty($merchantId)){
			$this->merchantid = $merchantId;
		}
	}
	public function setItemPerPage($itemPerPage) {
		if(!empty($itemPerPage)){
			$this->itemPerPage = $itemPerPage;
		}
	}


	protected function populateState($ordering = NULL, $direction = NULL) {
//		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseed', rand(), 'com_bookingforconnector');
		if (!$session->has('searchseed','com_bookingforconnector')) {
			BFCHelper::setSession('searchseed', $searchseed, 'com_bookingforconnector');
		}

		$this->setState('params', array(
			'merchantId' => JRequest::getInt('merchantId'),
			'offerId' => JRequest::getInt('offerId'),
            'onSellUnitId' => JRequest::getInt('onsellunitid'),
			'searchseed' => $searchseed,
			'filters' => JRequest::getVar('filters')
		));

		return parent::populateState($ordering, $direction);
	}

	public function getMerchantFromServicebyId($merchantId) {

		return $this->getMerchantFromService($merchantId);

//		$merchanturlMerchantId = $this->urlMerchant;
//
//		if (isset($merchantId) && $merchantId!= ""){
//			$merchanturlMerchantId = sprintf($this->urlMerchant."(%d)", $merchantId);
//		}
//
//		$options = array(
//				'path' => $merchanturlMerchantId,
//				'data' => array(
//					'$filter' => 'Enabled eq true',
//					'$format' => 'json'
////					,'$expand' => 'MerchantType'
//				)
//		);
//
//		$url = $this->helper->getQuery($options);
//
//		$merchant = null;
//
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
////			$merchant = $res->d->results ?: $res->d;
//			if (!empty($res->d->results)){
//				$merchant = $res->d->results;
//			}elseif(!empty($res->d)){
//				$merchant = $res->d;
//			}
//		}
//
//		/*switch ($layout) {
//			case 'resources':
//				$merchant->resources = $this->getItems();
//				break;
//			default:
//				break;
//		}*/
//
//		return $merchant;
	}



	public function getMerchantFromService($merchantId='') {
		
		if(empty($merchantId)){
//			$params = $this->getState('params');
//			$merchantId = $params['merchantId'];
			return null;
		}

//		$layout = $params['layout'];
		
//		$merchanturlMerchantId = $this->urlMerchant;

//		if (isset($merchantId) && $merchantId!= ""){
//			$merchanturlMerchantId = sprintf($this->urlMerchant."(%d)", $merchantId);
//		}
//
		$cultureCode = $GLOBALS['bfi_lang'];

		$sessionkey = 'merchant.' . $merchantId . $cultureCode ;			
		$merchant = null;
		$merchant = BFCHelper::getSession($sessionkey); //$_SESSION[$sessionkey];

		if ($merchant == null) {

			$options = array(
					'path' => $this->urlMerchant,
					'data' => array(
						'id' => $merchantId,
						'cultureCode' => BFCHelper::getQuotedString($cultureCode),
						'$format' => 'json'
					)
			);
			
			$url = $this->helper->getQuery($options);

			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetMerchantsById)){
					$merchant = $res->d->GetMerchantsById;
				}elseif(!empty($res->d)){
					$merchant = $res->d;
				}
			}
			BFCHelper::setSession($sessionkey, $merchant);
		}
		
		return $merchant;
	}



//Risorse in vendita
	public function getMerchantOnSellUnitsFromService($start, $limit, $merchantId) {
		$options = array(
				'path' => $this->urlMerchantOnSellUnits,
				'data' => array(
					'merchantid' =>  $merchantId ,
					'$format' => 'json'
				)
		);

		if (isset($start) && $start > 0) {
			$options['data']['skip'] = $start;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
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
		}

		return $resources;
	}

	public function getMerchantOnSellUnitFromService() {
		$params = $this->getState('params');

		$onSellUnitId = $params['onSellUnitId'];

		$options = array(
				'path' => sprintf($this->urlMerchantOnSellUnit, $onSellUnitId),
				'data' => array(
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);

		$onsellunit = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$onsellunit = $res->d->results ?: $res->d;
		}

		return $onsellunit;
	}

	public function getItemsOnSellUnit()
	{
		return $this->getItems('onsellunits');
	}

	public function getOnSellUnit()
	{
		return $this->getItems('onsellunit');
	}

	public function getItem($merchantId = '') {
		if($merchantId != '') {
		  $item = $this->getMerchantFromService($merchantId);
		}
		else {
		  $item = $this->getMerchantFromService();
		}
		return $item;
	}

	public function getMerchantResourcesFromService($start, $limit, $merchantId = NULL) {
      $merchantId = $merchantId != NULL ? $merchantId : $params['merchantId'];

		$seed = isset($_SESSION['search.params']['searchseed']) ? $_SESSION['search.params']['searchseed'] : '';
			$cultureCode = $GLOBALS['bfi_lang'];

		$options = array(
				'path' => $this->urlMerchantResources,
				'data' => array(
					'merchantId' => $merchantId,
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'seed' => $seed,
					'$format' => 'json'
				)
		);

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
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
		}

		return $resources;
	}

	public function getMerchantGroupsByMerchantIdFromService($merchantId = null) {
		$params = $this->getState('params');

		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}

		$options = array(
				'path' => $this->urlMerchantMerchantGroups,
				'data' => array(
					'merchantId' => $merchantId,
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);
		
		$merchantGroups = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$merchantGroups = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$merchantGroups = $res->d->results;
			}elseif(!empty($res->d)){
				$merchantGroups = $res->d;
			}
		}

		return $merchantGroups;
	}
	public function getPhoneByMerchantId($merchantId = null,$language='',$number='') {
		if ($merchantId==null) {
			$merchantId = $_SESSION['search.params']['merchantId'];
		}

		$options = array(
				'path' => $this->urlMerchantCounter,
				'data' => array(
					'merchantId' => $merchantId,
					'what' => '\'phone'.$number.'\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);

		$res = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$resReturn = $res->d;
			}
			if (!empty($resReturn)){
				$res = $resReturn->MerchantCounter;
			}
		}

		return $res;
	}

	public function GetFaxByMerchantId($merchantId = null,$language='') {
		$params = $this->getState('params');

		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}

		$options = array(
				'path' => $this->urlMerchantCounter,
				'data' => array(
					'merchantId' => $merchantId,
					'what' => '\'fax\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);

		$res = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$resReturn = $res->d;
			}
			if (!empty($resReturn)){
				$res = $resReturn->MerchantCounter;
			}
		}

		return $res;
	}

	public function setCounterByMerchantId($merchantId = null, $what='', $language='') {
		$params = $this->getState('params');

		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}

		$options = array(
				'path' => $this->urlMerchantCounter,
				'data' => array(
					'merchantId' => $merchantId,
					'what' => '\''.$what.'\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);

		$res = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$resReturn = $res->d;
			}
			if (!empty($resReturn)){
				$res = $resReturn->MerchantCounter;
			}
		}

		return $res;
	}

	public function getMerchantRatingAverageFromService($merchantId = null) {
		
		if ($merchantId==null) {
			$merchantId = $_SESSION['search.params']['merchantId'];
		}

		$options = array(
				'path' => $this->urlMerchantRatingAverage,
				'data' => array(
					'merchantId' => $merchantId,
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);

		$ratings = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$resRatings = null;
			if (!empty($res->d->results)){
				$resRatings = $res->d->results;
			}elseif(!empty($res->d)){
				$resRatings = $res->d;
			}
			if (!empty($resRatings)){
				$ratings = $resRatings->GetMerchantAverage;
			}
		}

		return $ratings;
	}

	//CHANGED
	public function getMerchantRatingsFromService($start, $limit, $merchantId = null, $language='') {
		if ($merchantId==null) {
			$merchantId = $_SESSION['search.params']['merchantId'];
		}
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];
		}

		$options = array(
				'path' => $this->urlMerchantRating,
				'data' => array(
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);

		if (isset($start) && $start > 0) {
			$options['data']['skip'] = $start;
		}else{
			$options['data']['skip'] = 0;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		if (isset($merchantId) && $merchantId > 0) {
			$options['data']['MerchantId'] = $merchantId;
		}

		$filters = isset($_SESSION['ratings']['filters']) ? $_SESSION['ratings']['filters'] : array();


		if ($filters != null && $filters['typologyid'] != null && $filters['typologyid']!= "0") {
			$options['data']['tipologyId'] = $filters['typologyid'];
		}

		$url = $this->helper->getQuery($options);

		$ratings = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {

			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ratings = $res->d->results;
			}elseif(!empty($res->d)){
				$ratings = $res->d;
			}
		}
		return $ratings;
	}

	public function getMerchantRatingsFromService_OLD($start, $limit, $merchantId = null) {
		$params = $this->getState('params');

		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}

		$options = array(
				'path' => $this->urlMerchantRating,
				'data' => array(
					'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$orderby' => 'CreationDate desc',
					'$format' => 'json'
				)
		);

		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}

		$filters = $params['filters'];

		// typologyid filtering
		if ($filters != null && $filters['typologyid'] != null && $filters['typologyid']!= "0") {
			$options['data']['$filter'] .= ' and TypologyId eq ' .$filters['typologyid'];
		}

		$url = $this->helper->getQuery($options);

		$ratings = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$ratings = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$ratings = $res->d->results;
			}elseif(!empty($res->d)){
				$ratings = $res->d;
			}
		}

//		//raggruppo e conto tutte le recensioni
//		$resultGrouped = array();
//		foreach ($ratings as $val) {
//			if (isset($resultGrouped[$val->TypologyId])){
//				$resultGrouped[$val->TypologyId]++;
//			}else{
//				//$prev_value = array('value' => $val->TypologyId, 'amount' => 0);
//				$resultGrouped[$val->TypologyId] = 1;
//			}
//		}
//		$this->setState('rating.resultGrouped', $resultGrouped);

		//$ratings = $this->filterRatingResults($ratings);
		return $ratings;
	}

	private function filterRatingResults($results) {
		$params = $this->getState('params');
		$filters = $params['filters'];
		if ($filters == null) return $results;

		// typologyid filtering
		if ($filters['typologyid'] != null) {
			$typologyid = (int)$filters['typologyid'];
			if ($typologyid > 0) {
				$results = array_filter($results, function($result) use ($typologyid) {
					return $result->TypologyId == $typologyid;
				});
			}
		}

		return $results;
	}

//	public function getMerchantPackagesFromService($start, $limit, $language = null) {
////		$params = $this->getState('params');
//		
//		
//		if ($language==null) {
//			$language = $GLOBALS['bfi_lang'];
//		}
//		
//		$options = array(
//				'path' => $this->urlMerchantPackages,
//				'data' => array(
//					'merchantId' => $this->merchantid,
//					'cultureCode' => BFCHelper::getQuotedString($language),
//					/*'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
//					'$expand' => 'Photos',
//					'$skip' => $start,
//					'$top' => $limit,*/
//					'$format' => 'json'
//				)
//		);
//		/*
//		if (isset($start) && $start > 0) {
//			$options['data']['$skip'] = $start;
//		}
//		
//		if (isset($limit) && $limit > 0) {
//			$options['data']['$top'] = $limit;
//		}		
//		*/
//		$url = $this->helper->getQuery($options);
//		
//		$ret = null;
//		
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
////			$ret = $res->d->results ?: $res->d;
//			if (!empty($res->d->results)){
//				$ret = $res->d->results;
//			}elseif(!empty($res->d)){
//				$ret = $res->d;
//			}
//		}
//
//		return $ret;
//	}	
//	
//	public function getMerchantPackageFromService($packageId=0 , $language = null) {
////		$params = $this->getState('params');
////		
////		$packageId = $params['packageid'];
//		
//		if ($language==null) {
////			$language = BFCHelper::getState('merchantdetails', 'language');
//			$language = $GLOBALS['bfi_lang'];
//		}
//		
//		$options = array(
//				'path' => $this->urlMerchantPackage,
//				'data' => array(
//					'id' => $packageId,
//					'cultureCode' => BFCHelper::getQuotedString($language),
//					'$format' => 'json'
//				)
//		);
//		
//		$url = $this->helper->getQuery($options);
//		
//		$ret = null;
//		
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
////			$ret = $res->d->results ?: $res->d;
//			if (!empty($res->d->GetPackageById)){
//				$ret = $res->d->GetPackageById;
//			}elseif(!empty($res->d)){
//				$ret = $res->d;
//			}
//		}
//
//		return $ret;
//	}	

	public function getMerchantOffersFromService($start, $limit, $language = null) {

		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
		$options = array(
				'path' => $this->urlMerchantOffers,
				'data' => array(
					'merchantId' => $this->merchantid,
					'cultureCode' => BFCHelper::getQuotedString($language),
					/*'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					'$expand' => 'Photos',
					'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
				)
		);
		/*
		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}		
		*/
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

	public function getMerchantOfferFromService($offerId=0 , $language = null) {
//		$params = $this->getState('params');
//		
//		$offerId = $params['offerId'];
		
		if ($language==null) {
//			$language = BFCHelper::getState('merchantdetails', 'language');
			$language = $GLOBALS['bfi_lang'];

		}
		
		$options = array(
				'path' => $this->urlMerchantOffer,
				'data' => array(
					'id' => $offerId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$offer = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$offer = $res->d->results ?: $res->d;
			if (!empty($res->d->GetVariationPlanById)){
				$offer = $res->d->GetVariationPlanById;
			}elseif(!empty($res->d)){
				$offer = $res->d;
			}
		}

		return $offer;
	}	

	public function getItemsRating($merchant_id)
	{
		return $this->getItems('ratings', '', $merchant_id);
	}

	public function getRating()
	{
		return $this->getItems('ratings');
	}

	public function getItemsOffer()
	{
		return $this->getItems('offers');
	}

	public function getOffer()
	{
		return $this->getItems('offer');
	}

	public function getItemsResourcesAjax()
	{
		return $this->getItems('resourcesajax');
	}

	public function getItems($type = '', $ext_data = 0, $merchant_id = '')
	{
		$page = bfi_get_current_page() ;

		switch($type) {
//			case 'packages':
//				$items = $this->getMerchantPackagesFromService(
//					(absint($page)-1)*$this->itemPerPage,
//					$this->itemPerPage,
//					null
//				);
//				break;
//			case 'package':
//				$items = $this->getMerchantPackageFromService();
//				break;
			case 'offers':
				   $items = $this->getMerchantOffersFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					null
				);
				break;
			case 'offer':
				$items = $this->getMerchantOfferFromService();
				break;
			case 'onsellunits':
				$items = $this->getMerchantOnSellUnitsFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					$merchant_id
				);
				break;
			case 'onsellunit':
				$items = $this->getMerchantOnSellUnitFromService();
				break;
			case 'ratings':
				$items = $this->getMerchantRatingsFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					$merchant_id
				);
				break;
			case 'resourcesajax':
				$items = $this->getMerchantResourcesFromService(
					0,
					20,
					$ext_data
				);
				break;
			case '':
			default:
				$items = $this->getMerchantResourcesFromService(
					(absint($page)-1)*$this->itemPerPage,
					$this->itemPerPage,
					$merchant_id
				);
				break;
		}


		return $items;
	}

	public function getTotal($type = '')
	{
		switch($type) {
//			case 'packages':
//				return $this->getTotalPackages();
//				break;
			case 'offers':
				return $this->getTotalOffers();
				break;
			case 'onsellunits':
				return $this->getTotalOnSellUnits($this->merchantid);
				break;
			case 'ratings':
				return $this->getTotalRatings();
				break;
			case '':
			default:
				return $this->getTotalResources();
		}
	}

	public function getTotalOnSellUnits($merchantId)
	{
		if(empty($merchantId)){
			$merchantId=$this->merchantid;
		}
		$options = array(
				'path' => $this->urlMerchantOnSellUnitsCount,
				'data' => array(
					'$format' => 'json',
					'merchantid' =>  $merchantId
			)
		);

		$url = $this->helper->getQuery($options);

		$count = null;

		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$count = (int)$r;
//		}
		if (isset($r)) {
			$res = json_decode($r);
			$count = (int)$res->d->GetResourceonsellsByMerchantIdCount;
		}

		return $count;
	}

	//CHANGED
	public function getTotalRatings()
	{
		$options = array(
				'path' => $this->urlMerchantRatingCount,
				'data' => array(
					'$format' => 'json',
					'MerchantId' =>  $this->merchantid 
			)
		);
		$filters = isset($_SESSION['ratings']['filters']) ? $_SESSION['ratings']['filters'] : array();
		if ($filters != null && $filters['typologyid'] != null && $filters['typologyid']!= "0") {
			$options['data']['tipologyId'] = $filters['typologyid'];
		}

		$url = $this->helper->getQuery($options);

		$count = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
			$res = json_decode($r);
			$count = (int)$res->d->GetReviewsCount;
		}

		return $count;
	}

	//old
	public function getTotalRatings_OLD()
	{
		$options = array(
				'path' => $this->urlMerchantRatingCount,
				'data' => array(
					'$filter' => 'MerchantId eq ' . BFCHelper::getInt('merchantId') . ' and Enabled eq true'
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

	public function getTotalOffers()
	{
		$options = array(
				'path' => $this->urlMerchantOffersCount,
				'data' => array(
					'merchantId' => $this->merchantid ,
					'$format' => 'json'
//					'$filter' => 'MerchantId eq ' . $this->merchantid . '  and Enabled eq true  and Viewable eq true '
			)
		);

		$url = $this->helper->getQuery($options);

		$count = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
//			$count = (int)$r;
			$res = json_decode($r);
			$count = (int)$res->d->GetVariationPlansCount;
		}

		return $count;
	}

//	public function getTotalPackages()
//	{
//		$options = array(
//				'path' => $this->urlMerchantPackagesCount,
//				'data' => array(
//					'merchantId' => $this->merchantid,
//					'$format' => 'json'
//			)
//		);
//		
//		$url = $this->helper->getQuery($options);
//		
//		$count = null;
//		
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$count = (int)$r;
//			$res = json_decode($r);
//			$count = (int)$res->d->GetPackagesCount;
//		}
//		
//		return $count;
//	}


	public function getTotalResources()
	{
		$count = null;				
		if(!empty($this->merchantid)){
			$options = array(
					'path' => $this->urlMerchantResourcesCount,
					'data' => array(
						'$format' => 'json',
						'merchantId' => $this->merchantid
				)
			);

			$url = $this->helper->getQuery($options);


			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				$count = (int)$res->d->GetMerchantResourcesCount;
			}
		}

		return $count;
	}
	public function getTotalResourcesAjax()
	{
		$options = array(
				'path' => $this->urlMerchantResourcesCount,
				'data' => array(
					'$format' => 'json',
					'merchantId' => BFCHelper::getInt('merchantId')
			)
		);

		$url = $this->helper->getQuery($options);

		$count = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = (int)$res->d->GetMerchantResourcesCount;
		}

		return $count;
	}
	public function getStart($type = '')
	{
//		$store = $this->getStoreId('getstart'.$type);
//
//		// Try to load the data from internal storage.
//		if (isset($this->cache[$store]))
//		{
//			return $this->cache[$store];
//		}

//		$start = $this->getState('list.start');
//		$limit = $this->getState('list.limit');
		$start =  isset($_REQUEST['limitstart']) ? $_REQUEST['limitstart'] :0 ;  
		$limit = 20;
		$total = $this->getTotal($type);
		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}
	function getPaginationRatings()
	{
		return $this->getPagination('ratings');
	}
	function getPaginationOnSellUnits()
	{
		return $this->getPagination('onsellunits');
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