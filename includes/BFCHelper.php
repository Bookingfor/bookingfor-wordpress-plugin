<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'bfi_Meal' ) ) {
	class bfi_Meal {     
		const Breakfast = 1;
		const Lunch = 2;
		const Dinner = 4;
		const AllInclusive = 8;
		const BreakfastLunch = 3;
		const BreakfastDinner = 5;
		const LunchDinner = 6;
		const BreakfastLunchDinner = 7;

		// etc. }
	}
}
if ( ! class_exists( 'bfiAgeType' ) ) {
	
	class bfiAgeType {     
		public static $Adult = 0;
		public static $Seniors = 1;
		public static $Reduced = 2;
	

		// etc. }
	}
}

if ( ! class_exists( 'BFCHelper' ) ) {

	class BFCHelper {
		public static $defaultFallbackCode = 'en-gb';
		private static $sessionSeachParamKey = 'searchparams';
		private static $image_basePath = COM_BOOKINGFORCONNECTOR_BASEIMGURL;
		private static $image_basePathCDN = COM_BOOKINGFORCONNECTOR_IMGURL_CDN;
		private static $searchResults = array();
		private static $currentState = array();
		private static $defaultCheckMode = 5;
		private static $favouriteCookieName = "BFFavourites";
		private static $ordersCookieName = "BFOrders";

		private static $TwoFactorCookieName = "2faHSTDenabledWP";
		private static $TwoFactorAuthenticationDeviceExpiration = 30;
		private static $TwoFactorPrefixClaimName = "TwoFactor.DeviceCode.";

			
		public static $currencyCode = array(
			978 => 'EUR',
			191 => 'HRK',
			840 => 'USD',
			392 => 'JPY',
			124 => 'CAD',
			36 => 'AUD',
			643 => 'RUB',
			200 => 'CZK',
			702 => 'SGD',
			826 => 'GBP',
		);
		public static $listNameAnalytics = array(
			0 => 'Direct access',
			1 => 'Merchants Group List',
			2 => 'Resources Group List',
			3 => 'Resources Search List',
			4 => 'Merchants List',
			5 => 'Resources List',
			6 => 'Offers List',
			7 => 'Sales Resources List',
			8 => 'Sales Resources Search List',
		);
		private static $image_paths = array(
			'merchant' => '/merchants/',
			'resources' => '/products/unita/',
			'offers' => '/packages/',
			'services' => '/servizi/',
			'merchantgroup' => '/merchantgroups/',
			'tag' => '/tags/',
			'onsellunits' => '/products/unitavendita/',
			'condominium' => '/products/condominio/',
			'variationplans' => '/variationplans/',
			'prices' => '/prices/'
		);

		private static $image_path_resized = array(
			'merchant_list'						=> '148x148',
			'merchant_list_default'				=> '148x148',
			'resource_list_default'				=> '148x148',
			'onsellunit_list_default'			=> '148x148',
			'resource_list_default_logo'		=> '148x148',
			'resource_list_merchant_logo'		=> '200x70',
			'merchant_logo'						=> '200x70',
			'merchant_logo_small'				=> '65x65',
			'merchant_logo_small_top'			=> '250x90',
			'merchant_logo_small_rapidview'		=> '200x70',
			'condominium_list_default'			=> '148x148',
			'offer_list_default'				=> '148x148',
			'resource_service'					=> '24x24',
			'resource_planimetry'				=> '400x250',
			'merchant_gallery_full'				=> '500x375',
			'merchant_mono_full'				=> '770x545',
			'merchant_gallery_thumb'			=> '85x85',
			'resource_gallery_full'				=> '692x450',
			'resource_mono_full'				=> '640x450',
			'resource_gallery_thumb'			=> '85x85',
			'resource_gallery_full_rapidview'	=> '416x290',
			'resource_gallery_thumb_rapidview'	=> '80x53',
			'resource_mono_full_rapidview'		=> '416x290',
			'resource_gallery_default_logo'		=> '100x100',
			'onsellunit_gallery_full'			=> '550x300',
			'onsellunit_mono_full'				=> '550x300',
			'onsellunit_default_logo'			=> '250x250',
			'onsellunit_gallery_thumb'			=> '85x85',
			'onsellunit_map_default'			=> '85x85',
			'onsellunit_showcase'				=> '180x180',
			'onsellunit_gallery'				=> '106x67',
			'condominium_map_default'			=> '85x85',
			'merchant_merchantgroup'			=> '40x40',
			'resource_search_grid'			=> '380x215',
			'merchant_resource_grid' => '380x215',
			'small' => '201x113',
			'medium' => '380x215',
			'big' => '820x460',
			'logomedium' => '148x148',
			'logobig' => '170x95',
			'tag24' => '24x24'
		);
		
		private static $image_resizes = array(
			'merchant_list' => 'width=100&bgcolor=FFFFFF',
			'merchant_logo' => 'width=200&bgcolor=FFFFFF',
			'merchant_logo_small' => 'width=65&height=65&bgcolor=FFFFFF',
			'merchant_logo_small_top' => 'width=250&height=90&bgcolor=FFFFFF',
			'merchant_logo_small_rapidview' => 'width=180&height=65&bgcolor=FFFFFF',
			'resource_list_default' => 'width=148&height=148&mode=crop&anchor=middlecente&bgcolor=FFFFFF',
			'onsellunit_list_default' => 'width=148&height=148&mode=crop&anchor=middlecenter&bgcolor=FFFFFF',
			'resource_list_default_logo' => 'width=148&height=148&bgcolor=FFFFFF',
			'resource_list_merchant_logo' =>  'width=200&height=70&bgcolor=FFFFFF',
			'merchant_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
			'condominium_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
			'offer_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
			'resource_service' => 'width=24&height=24',
			'resource_planimetry' => 'width=400&height=250&mode=pad&anchor=middlecenter',
			'merchant_gallery_full' => 'width=500&height=375&mode=pad&anchor=middlecenter',
			'merchant_mono_full' => 'width=770&height=545&mode=crop&anchor=middlecenter&scale=both',
			'merchant_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
			'resource_gallery_full' => 'width=692&height=450&mode=pad&anchor=middlecenter&ext=.jpg',
			'resource_mono_full' => 'width=640&height=450&mode=pad&anchor=middlecenter&scale=both',
			'resource_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
			'resource_gallery_full_rapidview' => 'w=416&h=290&mode=crop&anchor=middlecenter&ext=.jpg',
			'resource_gallery_thumb_rapidview' => 'width=80&height=53&mode=crop&anchor=middlecenter',
			'resource_mono_full_rapidview' => 'w=416&h=290&mode=crop&anchor=middlecenter&ext=.jpg',
			'resource_gallery_default_logo' => 'w=100&h=100&mode=pad&anchor=middlecenter&ext=.jpg',
			'onsellunit_gallery_full' => 'w=550&h=300&bgcolor=EDEDED&mode=pad&anchor=middlecenter&ext=.jpg',
			'onsellunit_mono_full' => 'width=550&height=300&mode=crop&anchor=middlecenter&scale=both',
			'onsellunit_default_logo' => 'width=250&height=250&bgcolor=FFFFFF',
			'onsellunit_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
			'onsellunit_map_default' => 'width=85&height=85&bgcolor=FFFFFF',
			'onsellunit_showcase' => 'width=180&height=180&bgcolor=FFFFFF&mode=crop&anchor=middlecenter',
			'onsellunit_gallery' => 'width=106&height=67&bgcolor=FFFFFF',
			'condominium_map_default' => 'width=85&height=85&bgcolor=FFFFFF',
			'merchant_merchantgroup' => 'width=40&height=40',
			'small' => 'width=201&height=113&mode=crop&anchor=middlecenter',
			'medium' => 'width=380&height=215&mode=crop&anchor=middlecenter',
			'big' => 'width=820&height=460&mode=crop&anchor=middlecenter',
			'logomedium' => 'width=148&height=148&anchor=middlecenter&bgcolor=FFFFFF',
			'logobig' => 'width=170&height=95&anchor=middlecenter&bgcolor=FFFFFF',
			'tag24' => 'width=24&height=24'
		);
		
		public static $daySpan = '+7 day';
		public static $defaultDaysSpan = '+7 days';
		public static $defaultDuration = 7;
		public static $defaultAdultsAge = COM_BOOKINGFORCONNECTOR_ADULTSAGE;
		public static $defaultChildrensAge = COM_BOOKINGFORCONNECTOR_CHILDRENSAGE;
		public static $defaultAdultsQt = COM_BOOKINGFORCONNECTOR_ADULTSQT;
		public static $defaultSenioresAge = COM_BOOKINGFORCONNECTOR_SENIORESAGE;
		public static $onsellunitDaysToBeNew = 120;
		
		//public static $typologiesMerchantResults = array(1,6);
		public static function isUnderHTTPS() {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' );
		}
		
		public static function isMerchantAnonymous($id) {
			if (defined('COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE')) {
				$aAnon = explode(",",COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE);
				return in_array($id, $aAnon);
			}	
			return false;
		}
		
		public static function showMerchantRatingByCategoryId($id) {
			if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_SHOW_RATING_MERCHANT')) {
				$aAnon = explode(",",COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_SHOW_RATING_MERCHANT);
				return in_array($id, $aAnon);
			}	
			return false;
		}
		
		public static function getCategoryMerchantResults($language='') {
			$groupOnSearch = array();
			$merchantCategories = BFCHelper::getMerchantCategories($language);
			if(!empty($merchantCategories)){
			$groupOnSearch = array_unique(array_map(function ($i) { 
				if($i->GroupOnSearch){
					return $i->MerchantCategoryId;
				}
				return 0; 
				}, $merchantCategories));	
			}
			return $groupOnSearch;
		}
			
		public static function getTypologiesMerchantResults() {
			if (self::isMerchantBehaviour()) {
				return array();
			}	
			return array(1,6);
		}
		public static function getAddressDataByMerchant($id) {
			if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_RESOURCE_ADDRESSDATA_BY_MERCHANT')) {
				$aAnon = explode(",",COM_BOOKINGFORCONNECTOR_MERCHANTCATEGORIES_RESOURCE_ADDRESSDATA_BY_MERCHANT);
				return in_array($id, $aAnon);
			}	
			return false;
		}

		public static function isMerchantBehaviour() {
			if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTBEHAVIOUR')) {
				if (COM_BOOKINGFORCONNECTOR_MERCHANTBEHAVIOUR) {
					return true;
				}
			}	
			return false;
		}
			
		public static function setSearchResult($searchid, $value) {
			if ($value == null) {
				if (array_key_exists($searchid, self::$searchResults)) {
					unset(self::$searchResults[$searchid]);
				}
			} else
			{
				self::$searchResults[$searchid] = $value;
			}
		}
		
		public static function getSearchResult($searchid) {
			if (array_key_exists($searchid, self::$searchResults)) {
				return self::$searchResults[$searchid];
			}
			return null;
		}
		
		public static function getItem($xml, $itemName, $itemContext = null) {
			if ($xml==null || $itemName == null) return '';
				$currErrLev = error_reporting();
				error_reporting(0);
			try {
				$xdoc = new SimpleXmlElement($xml);
				if (isset($itemContext)) $xdoc= $xdoc->$itemContext;
				$item = $xdoc->$itemName;

			} catch (Exception $e) {
				// maybe it's not a well formed XML?
				return $itemName;
			}
				error_reporting($currErrLev);
			return $item;
		}

		public static function priceFormat($number, $decimal=2,$sep1=',',$sep2='.') {
			if(empty($number)){
				$number =0;
			}
			//conversion valuta;
			$defaultcurrency = bfi_get_defaultCurrency();
			$currentcurrency = bfi_get_currentCurrency();

			if($defaultcurrency!=$currentcurrency){
				//try to convert
				$currencyExchanges = BFCHelper::getCurrencyExchanges();
				if (isset($currencyExchanges[$currentcurrency]) ) {
					$number = $number*$currencyExchanges[$currentcurrency];
				}
			}
			return number_format($number, $decimal, $sep1, $sep2);
		}
		
	/* -------------------------------- */

		public static function getCartMultimerchantEnabled() {
	//		$model = new BookingForConnectorModelPortal;
	//		return $model->getCartMultimerchantEnabled();
			return true;
		}
		public static function GetPrivacy($language) {
			$model = new BookingForConnectorModelPortal;
			return $model->getPrivacy($language);
		}

		public static function getCurrencyExchanges() {
			$model = new BookingForConnectorModelPortal;
			return $model->getCurrencyExchanges();
		}

		public static function getDefaultCurrency() {
			$model = new BookingForConnectorModelPortal;
			return $model->getDefaultCurrency();
		}


		
		public static function GetAdditionalPurpose($language) {
			$model = new BookingForConnectorModelPortal;
			return $model->getAdditionalPurpose($language);
		}
		public static function GetPhoneByMerchantId($merchantId,$language) {
			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getPhoneByMerchantId($merchantId,$language);
		}

		public static function GetProductCategoryForSearch($language='', $typeId = 1,$merchantid=0) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelPortal;
			return $model->getProductCategoryForSearch($language, $typeId,$merchantid);
		}

	//	public static function GetFaxByMerchantId($merchantId,$language) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
	//		return $model->GetFaxByMerchantId($merchantId,$language);
	//	}

	//	public static function setCounterByMerchantId($merchantId = null, $what='', $language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
	//		return $model->setCounterByMerchantId($merchantId, $what, $language);
	//	}
	//	public static function setCounterByResourceId($resourceId = null, $what='', $language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnit', 'BookingForConnectorModel');
	//		return $model->setCounterByResourceId($resourceId, $what, $language);
	//	}

	/* -------------------------------- */
	//	public static function getMerchant() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
	//		return $model->getItem();
	//	}
		public static function getMerchantFromServicebyId($merchantId) {
			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantFromServicebyId($merchantId);
		}
		public static function getMerchantOfferFromService($offerId, $language='') {
			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantOfferFromService($offerId, $language);
		}

		public static function getCondominiumFromServicebyId($resourceId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Condominium', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelCondominiums;
			return $model->getCondominiumFromService($resourceId);
		}

	//	public static function getRatingByMerchantId($merchantId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('MerchantDetails', 'BookingForConnectorModel');
	//		return $model->getMerchantRatingAverageFromService($merchantId);
	//	}
	//	public static function getRatingsByOrderId($orderId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
	//		return $model->getRatingsByOrderIdFromService($orderId);
	//	}
	//	public static function getTotalRatingsByOrderId($orderId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Ratings', 'BookingForConnectorModel');
	//		return $model->getTotalRatingsByOrderId($orderId);
	//	}	
		public static function getResourceRatingAverage($merchantId, $resourceId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getRatingAverageFromService($merchantId, $resourceId);
		}
		public static function getResourceRating($start = 0, $limit=5, $resourceId=0) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getRatingsFromService($start, $limit, $resourceId);
		}

		public static function getMerchantGroupsByMerchantId($merchantId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantGroupsByMerchantIdFromService($merchantId);
		}
		

	//	public static function getUnitCategories() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getUnitCategories();
	//	}
		
		public static function getLocationZones() {
			$model = new BookingForConnectorModelMerchants;
			return $model->getLocationZones();
		}
		
		public static function getLocationZonesByLocationId($locationId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelMerchants;
			return $model->getLocationZones($locationId);
		}

		public static function getLocationZonesBySearch() {
			$model = new BookingForConnectorModelSearchOnSell;
			return $model->getLocationZonesBySearch();
		}
	//	public static function getLastLocationZoneOnsell() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getLastLocationZoneOnsell();
	//	}

		public static function getLocations() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelMerchants;
			return $model->getLocations();
		}
	//	public static function getLocationById($locationId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
	//		return $model->getLocationById($locationId);
	//	}

	//	public static function getMerchantTypes() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
	//		return $model->getMerchantTypes();
	//	}
		
		public static function getMasterTypologies($onlyEnabled = true) {
			$model = new BookingForConnectorModelSearch;
			return $model->getMasterTypologies($onlyEnabled);
		}
		public static function GetAlternativeDates($checkin, $duration, $paxes, $paxages, $merchantId, $condominiumId, $resourceId, $cultureCode, $points, $userid, $tagids, $merchantsList, $availabilityTypes, $itemTypeIds, $domainLabel, $merchantCategoryIds = null, $masterTypeIds = null, $merchantTagsIds = null) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelSearch;
			return $model->GetAlternativeDates($checkin, $duration, $paxes, $paxages, $merchantId, $condominiumId, $resourceId, $cultureCode, $points, $userid, $tagids, $merchantsList, $availabilityTypes, $itemTypeIds, $domainLabel, $merchantCategoryIds, $masterTypeIds, $merchantTagsIds);
		}
		
		public static function getMerchantGroups() {
			$model = new BookingForConnectorModelMerchants;
			return $model->getMerchantGroups();
		}
		
	//	public static function getResource() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getItem();
	//	}
	//	public static function getOnSellUnit() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnit', 'BookingForConnectorModel');
	//		return $model->getItem();
	//	}
		
	//	public static function getResourceByMasterTypologyId($masterTypologyId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
	//		$mystate=$model->getState();
	//		$model->setState('params', array(
	//			'masterTypeId' => $masterTypologyId
	//		));
	//		return $model->getItems();
	//	}
		
	//	public static function getResourceModel() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model;
	//	}
		
		public static function getMerchantCategories($language='') {
		  $model = new BookingForConnectorModelMerchants;
			return $model->getMerchantCategories($language);
		}
		public static function getMerchantCategoriesForRequest($language='') {
		  $model = new BookingForConnectorModelMerchants;
			return $model->getMerchantCategoriesForRequest($language);
		}

	//	public static function getMerchantCategory($merchanCategoryId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
	//		return $model->getMerchantCategory($merchanCategoryId);
	//	}
		
		public static function getServicesByMerchantsCategoryId($merchantCategoryId,$language='') {
			$model = new BookingForConnectorModelMerchants;
			return $model->getServicesByMerchantsCategoryId($merchantCategoryId,$language);
		}

		public static function GetPolicy($resourcesId,$language='') {
			$model = new BookingForConnectorModelResource;
			return $model->GetPolicy($resourcesId,$language);
		}
		public static function GetPolicyById($policId,$language='') {
			$model = new BookingForConnectorModelResource;
			return $model->GetPolicyById($policId,$language);
		}

		public static function GetResourcesByIds($listsId,$language='') {
		  $model = new BookingForConnectorModelResources;
			return $model->GetResourcesByIds($listsId,$language);
		}
		public static function GetResourcesById($id,$language='') {
		  $model = new BookingForConnectorModelResource;
			return $model->getItem($id);
		}

		public static function GetAlternateResources($start, $limit, $ordering = null, $direction = null, $merchantid = null,  $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null, $language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid, $ignorePagination, false, $excludedResources, $requiredOffers, $overrideFilters, $language);
		}


		public static function getDiscountDetails($discountId, $hasRateplans) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getDiscountDetails($discountId,$hasRateplans);
		}
	//	public static function GetDiscountsByResourceId($resourcesId,$hasRateplans) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->GetDiscountsByResourceId($resourcesId,$hasRateplans);
	//	}

	//	public static function getRateplanDetails($rateplanId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getRateplanSimpleDetails($rateplanId);
	//	}
		public static function GetResourcesOnSellByIds($listsId,$language='') {
			$model = new BookingForConnectorModelOnSellUnits;
			return $model->GetResourcesByIds($listsId,$language);
		}
		public static function GetServicesByIds($listsId,$language='') {
			$model = new BookingForConnectorModelServices;
			return $model->getServicesByIds($listsId,$language);
		}

		public static function getServicesForSearchOnSell($language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOnSellUnits;
			return $model->getServicesForSearchOnSell($language);
		}

		public static function getServicesForSearch($language='') {
			$model = new BookingForConnectorModelResources;
			return $model->getServicesForSearch($language);
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
	//		return $model->getServicesForSearch($language);
		}

	//	public static function getResourcesOnSellShowcase($language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getResourcesOnSellShowcase($language);
	//	}

	//	public static function getResourcesOnSellGallery($language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getResourcesOnSellGallery($language);
	//	}
		
	//	public static function getAvailableLocationsAverages($language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getAvailableLocationsAverages($language);
	//	}

	//	public static function getCategoryPriceMqAverages($language='',$locationid) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getCategoryPriceMqAverages($language,$locationid);
	//	}
		
	//	public static function getPriceAverages($language='',$locationid,$unitcategoryid) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getPriceAverages($language,$locationid,$unitcategoryid);
	//	}

	//	public static function getPriceHistory($language='',$locationid,$unitcategoryid) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getPriceHistory($language,$locationid,$unitcategoryid);
	//	}

	//	public static function getPriceMqAverageLastYear($language='',$locationid,$unitcategoryid ,$contracttype = 0) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
	//		return $model->getPriceMqAverageLastYear($language,$locationid,$unitcategoryid, $contracttype);
	//	}

		public static function getMerchantsByIds($listsId, $language = '') {
			$model = new BookingForConnectorModelMerchants;
			return $model->getMerchantsByIds($listsId, $language);
		}

		public static function GetCondominiumsByIds($listsId,$language='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Condominiums', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelCondominiums;
			return $model->GetCondominiumsByIds($listsId,$language);
		}

		public static function getMerchantsSearch($text,$start,$limit,$order,$direction) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelMerchants;
			return $model->getMerchantsForSearch($text,$start,$limit,$order,$direction);
		}

	//	public static function getResourcesSearch($text,$start,$limit,$order,$direction) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
	//		return $model->getResourcesForSearch($text,$start,$limit,$order,$direction);
	//	}	

		public static function getTags($language='',$categoryIds='') {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelTags;
			return $model->getTags($language,$categoryIds,null,null);
		}

		public static function getMerchantsExt($tagids, $start = null, $limit = null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Tag', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelTags;
			return $model->getMerchantsExt($tagids, $start, $limit);
		}

		public static function prepareOrderData($formData, $customerData=null, $suggestedStay=null, $otherData=null, $creditCardData=null) {
			if ($formData == null) {
				$formData = $_POST['form'];
			}
				
			$userNotes = $formData['note'];
			$cultureCode = $formData['cultureCode'];
			$merchantId = $formData['merchantId'];
			$orderType = $formData['orderType'];
			$label = $formData['label'];
			$customerDatas = array($customerData);
			$bt = array();
			if(!empty($formData['bookingType']) &&  strpos($formData['bookingType'].'',':') !== false ){
				$bt = explode(':',$formData['bookingType'].'');
			}
			if(!isset($suggestedStay)){
				$suggestedStay = new stdClass; 
			}
			array_push($bt, null,null);

			if(isset($bt[0])){
				$suggestedStay->MerchantBookingTypeId = $bt[0];
			}
					
			$orderData = array(
					'customerData' => $customerDatas,
					'suggestedStay' =>$suggestedStay,
					'creditCardData' => $creditCardData,
					'otherNoteData' => $otherData,
					'merchantId' => $merchantId,
					'orderType' => $orderType,
					'userNotes' => $userNotes,
					'label' => $label,
					'cultureCode' => $cultureCode
					);

			return $orderData;
		}

		public static function getSingleOrderFromService($orderId = 0) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->getSingleOrderFromService($orderId);
		}	
		
	//	public static function getOrderMerchantPayment($order) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
	//		return $model->getOrderMerchantPayment($order);
	//	}	
		public static function getMerchantPaymentData($bookingTypeId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelPayment;
			return $model->getMerchantPaymentData($bookingTypeId);
		}	
		
		public static function GetOrderDetailsById($orderId,$culturecode='') {
			$model = new BookingForConnectorModelOrders;
			return $model->GetOrderDetailsById($orderId,$culturecode);
		}
		public static function setOrder($customerData = NULL, $suggestedStay = NULL, $creditCardData = NULL, $otherNoteData = NULL, $merchantId = NULL, $orderType = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processOrder = NULL, $priceType, $merchantBookingTypeId = NULL, $policyId = NULL) {
			$model = new BookingForConnectorModelOrders;
			return $model->setOrder($customerData, $suggestedStay, $creditCardData, $otherNoteData, $merchantId, $orderType, $userNotes, $label, $cultureCode, $processOrder, $priceType,$merchantBookingTypeId, $policyId);
		}
		
		public static function setOrderStatus($orderId = NULL, $status = NULL, $sendEmails = false, $setAvailability = false, $paymentData = NULL)  {
			$model = new BookingForConnectorModelOrders;
			return $model->setOrderStatus($orderId, $status, $sendEmails, $setAvailability, $paymentData);
		}
		
		public static function updateCCdata($orderId, $creditCardData = NULL, $processOrder = NULL) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->updateCCdata($orderId, $creditCardData, $processOrder);
		}

	//	public static function getOrderPayments($start,$limit,$orderid) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
	//		return $model->getOrderPayments($start,$limit,$orderid);
	//	}

	//	public static function getTotalOrderPayments($orderId = NULL)  {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
	//		return $model->getTotalOrderPayments($orderId);
	//	}
		public static function setOrderPayment($orderId = NULL, $status = 0, $sendEmails = false,$amount = 0,$bankId, $paymentData = NULL, $cultureCode = NULL, $processOrder = NULL)  {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Payment', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelPayment;
			return $model->setOrderPayment($orderId, $status, $sendEmails, $amount,$bankId, $paymentData, $cultureCode, $processOrder);
		}
		public static function setInfoRequest($customerData = NULL, $suggestedStay = NULL, $otherNoteData = NULL, $merchantId = NULL, $type = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processInfoRequest = NULL) {
			$model = new BookingForConnectorModelInfoRequests;
			return $model->setInfoRequest($customerData, $suggestedStay, $otherNoteData, $merchantId, $type, $userNotes, $label, $cultureCode, $processInfoRequest);
		}

	//	public static function setAlertOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processAlert = NULL, $enabled = NULL) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
	//		return $model->setAlertOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processAlert, $enabled);
	//	}
	//	public static function sendRequestOnSell($customerData = NULL, $searchData = NULL, $merchantId = NULL, $type = NULL, $label = NULL, $cultureCode = NULL, $processRequest = NULL) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
	//		return $model->sendRequestOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processRequest);
	//	}
		
	//	public static function unsubscribeAlertOnSell($hash = NULL, $id = NULL)  {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
	//		return $model->unsubscribeAlertOnSell($hash, $id);
	//	}

	//	public static function setMerchantAndUser($customerData = NULL, $password = NULL, $merchantType = 0, $merchantCategory = 0, $company = NULL, $userPhone = NULL, $webSite = NULL) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
	//		return $model->setMerchantAndUser($customerData, $password, $merchantType, $merchantCategory, $company, $userPhone, $webSite);
	//	}

	//	public static function sendNLPRequest($email = NULL, $cultureCode = NULL, $firstname = NULL, $lastname = NULL, $IDcategoria = NULL, $phone = NULL, $address = NULL, $nation = NULL, $reqUrlReg = NULL,$denominazione= NULL, $referer = NULL) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('NewsLetterPlus', 'BookingForConnectorModel');
	//		return $model->sendRequest($email, $cultureCode, $firstname, $lastname, $IDcategoria, $phone, $address, $nation, $reqUrlReg,$denominazione= NULL, $referer);
	//	}

		public static function getCountAllResourcesOnSell() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('OnSellUnits', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOnSellUnits;
			return $model->getAllResources();
		}

	//	public static function getStartDate() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getStartDateFromService();
	//	}
		
	//	public static function getEndDate() {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getEndDateFromService();
	//	}

		public static function getStartDateByMerchantId($merchantId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getStartDateByMerchantId($merchantId);
		}
		
		public static function getEndDateByMerchantId($merchantId) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getEndDateByMerchantId($merchantId);
		}

		public static function getCheckInDates($resourceId = null,$ci = null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getCheckInDatesFromService($resourceId ,$ci);
		}
		public static function GetCheckInDatesPerTimes($resourceId = null,$ci = null, $limitTotDays = 0) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->GetCheckInDatesPerTimes($resourceId ,$ci, $limitTotDays);
		}
		public static function GetListCheckInDayPerTimes($resourceId = null,$ci = null, $limitTotDays = 0) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->GetListCheckInDayPerTimes($resourceId , $ci, $limitTotDays);
		}
		
		public static function GetCheckInDatesTimeSlot($resourceId = null,$ci = null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->GetCheckInDatesTimeSlot($resourceId ,$ci);
		}


		public static function getCheckOutDates($resourceId = null,$checkIn = null,$checkOut = null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getCheckOutDatesFromService($resourceId ,$checkIn,$checkOut);
		}
		
		public static function GetMostRestrictivePolicyByIds($policyIds, $cultureCode, $stayConfiguration ='', $priceValue=null, $days=null) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->GetMostRestrictivePolicyByIds($policyIds, $cultureCode, $stayConfiguration, $priceValue, $days);
		}

		public static function GetPolicyByIds($policyIds, $cultureCode) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//			$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->GetPolicyByIds($policyIds, $cultureCode);
		}

	//	public static function getCheckAvailabilityCalendar($resourceId = null,$checkIn= null,$checkOut= null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getCheckAvailabilityCalendarFromService($resourceId,$checkIn,$checkOut);
	//	}
		
		//Please attention it's another method into another model, this is for list, not for single
	//	public static function getCheckAvailabilityCalendarFromlList($resourcesId = null,$checkIn= null,$checkOut= null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resources', 'BookingForConnectorModel');
	//		return $model->getCheckAvailabilityCalendarFromService($resourcesId,$checkIn,$checkOut);
	//	}

		//Please attention it's another method into another model, this is for list, not for single
	//	public static function getStayFromParameter($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$extras='',$packages,$pricetype='',$rateplanId=null,$variationPlanId=null,$hasRateplans=null ) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
	//		return $model->getStayFromServiceFromParameter($resourceId,$checkIn,$duration,$paxages,$extras,$packages,$pricetype,$rateplanId,$variationPlanId,$hasRateplans);
	//	}

		public static function getCompleteRateplansStayFromParameter($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$selectablePrices='',$packages,$pricetype='',$rateplanId=null,$variationPlanId=null ) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelResource;
			return $model->getCompleteRateplansStayFromParameter($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$rateplanId,$variationPlanId);
		}
		
		public static function GetCompleteRatePlansStayWP($resourceId = null,$checkIn = null,$duration = 1,$paxages = '',$selectablePrices='',$packages,$pricetype='',$rateplanId=null,$variationPlanId=null,$language="",$merchantBookingTypeId = "", $getAllResults=false ) {
			$model = new BookingForConnectorModelResource;
			return $model->GetCompleteRatePlansStayWP($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$rateplanId,$variationPlanId,$language,$merchantBookingTypeId, $getAllResults);
		}
		
		public static function GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language="",$condominiumId=0 ){
			$model = new BookingForConnectorModelResource;
			return $model->GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language,$condominiumId );
		}

		public static function setRating(
				$name = NULL, 
				$city = NULL, 
				$typologyid = NULL, 
				$email = NULL, 
				$nation = NULL, 
				$merchantId = NULL,
				$value1= NULL, 
				$value2= NULL, 
				$value3= NULL, 
				$value4= NULL, 
				$value5= NULL, 
				$totale = NULL, 
				$pregi =NULL, 
				$difetti =NULL, 
				$userId = NULL,
				$cultureCode = NULL,
				$checkin= NULL, 
				$resourceId= NULL, 
				$orderId= NULL, 
				$label = NULL, 
				$otherData = NULL
			) {

			$model = new BookingForConnectorModelRatings;
			return $model->setRating($name, $city, $typologyid, $email, $nation, $merchantId,$value1, $value2, $value3, $value4, $value5, $totale, $pregi, $difetti, $userId, $cultureCode,$checkin, $resourceId, $orderId, $label, $otherData);
		}


		public static function getCriteoConfiguration($pagetype = 0, $merchantsList = array(), $orderId = null) {
	//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//		$model = JModelLegacy::getInstance('Criteo', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelCriteo;
			return $model->getCriteoConfiguration($pagetype, $merchantsList,$orderId);
		}


		public static function getSlug($string) {
			$s = array();
			$r = array();
			$s[0] = "/\&/";
			$r[0] = "and";
			$s[1] = '/[^a-z0-9-]/';
			$r[1] = '-';
			$s[2] = '/-+/';
			$r[2] = '-';
			$string = preg_replace( $s, $r, strtolower( trim( $string ) ) );
			return $string;
		}

		
		public static function getLanguage($xml, $langCode, $fallbackCode = 'en-gb', $opts = array() ) {
			if (!isset($xml)) {
				return '';
			}
			$retVal = $xml;
			if (strpos($xml,'<languages>') !== false) {
				if ($fallbackCode == null || !isset($fallbackCode)) {
					$fallbackCode = self::$defaultFallbackCode;
				}
				$langCode = strtolower($langCode);
				$fallbackCode = strtolower($fallbackCode);
				if (strlen ($langCode) > 2) {
					$langCode = substr($langCode,0,2);
				}
				if (strlen ($fallbackCode) > 2) {
					$fallbackCode = substr($fallbackCode,0,2);
				}
				$xml = self::stripInvalidXml($xml);
				$xdoc = new SimpleXmlElement($xml);
				$item = $xdoc->xpath("language [@code='" . $langCode . "']");
				$result = '';
				$retVal = '';
				if(!empty($item)){
					$result = (string)$item[0];
				}
				if (($result == '') && $fallbackCode != '') {
					$item = $xdoc->xpath("language [@code='" . $fallbackCode . "']");
				}
				if(!empty($item)){
					$retVal = (string)$item[0];
				}
				//$retVal = (string)$item[0];
			}

			if (isset($opts) && count($opts) > 0) {
				foreach ($opts as $key => $opt) {
					switch (strtolower($key)) {
						case 'ln2br':
							$retVal = nl2br($retVal, true);
							break;
						case 'htmlencode':
							$retVal = htmlentities($retVal, ENT_COMPAT);
							break;
						case 'striptags':
							$retVal = strip_tags($retVal, "<br><br/>");
							break;
						case 'nomore1br':
							$retVal = preg_replace("/\n+/", "\n", $retVal);
							break;
						case 'nobr':
							$retVal = preg_replace("/\n+/", " ", $retVal);
							break;
						case 'bbcode':
							$search = array (
								'~\[b\](.*?)\[/b\]~s',
								'~\[i\](.*?)\[/i\]~s',
								'~\[u\](.*?)\[/u\]~s',
								'~\[s\](.*?)\[/s\]~s',
								'~\[ul\](.*?)\[/ul\]~s',
								'~\[li\](.*?)\[/li\]~s',
								'~\[ol\](.*?)\[/ol\]~s',
								'~\[size=(.*?)\](.*?)\[/size\]~s',
								'/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is'
							);
							$replace = array (
								'<b>$1</b>',
								'<i>$1</i>',
								'<u>$1</u>',
								'<s>$1</s>',
								'<ul>$1</ul>',
								'<li>$1</li>',
								'<ol>$1</ol>',
								'<font size="$1">$2</font>',
								''
							);
							$retVal = preg_replace($search, $replace, $retVal); // cleen for br

							break;
						default:
							break;
					}
				}
			}

			return $retVal;
		}
	/**
	 * Removes invalid XML
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 */
		public static function stripInvalidXml($value)
	{
		$ret = "";
		$current;
		if (empty($value)) 
		{
			return $ret;
		}

		$length = strlen($value);
		for ($i=0; $i < $length; $i++)
		{
			$current = ord($value{$i});
			if (($current == 0x9) ||
				($current == 0xA) ||
				($current == 0xD) ||
				(($current >= 0x20) && ($current <= 0xD7FF)) ||
				(($current >= 0xE000) && ($current <= 0xFFFD)) ||
				(($current >= 0x10000) && ($current <= 0x10FFFF)))
			{
				$ret .= chr($current);
			}
			else
			{
				$ret .= " ";
			}
		}
		return $ret;
	}	
		public static function getQuotedString($str){
			if (isset($str) && $str!=null){
				return '\'' . $str . '\'';	
				//return '\'' . str_replace('%27', '\'', $str) . '\'';	
			}	
			return null;
		}
		
		public static function getJsonEncodeString($str){
			if (isset($str) && $str!=null){
				return json_encode($str);
			}
			return null;
			
		}
			
		public static function parseJsonDate($date, $format = 'd/m/Y') { 
			date_default_timezone_set('UTC');
			//preg_match( '/([\d]{13})/', $date, $matches); 
			preg_match( '/(\-?)([\d]{9,})/', $date, $matches);
			// Match the time stamp (microtime) and the timezone offset (may be + or -)     
			$formatDate = 'd/m/Y';
			if (isset($format) && $format!=""){
				$formatDate = $format;
			}
			$date = date($formatDate, $matches[1].$matches[2]/1000 ); // convert to seconds from microseconds      
			return $date;
		}

		public static function parseJsonDateTime($date, $format = 'd/m/Y') { 
			date_default_timezone_set('UTC');
			return DateTime::createFromFormat($format, BFCHelper::parseJsonDate($date,$format),new DateTimeZone('UTC'));
		}

		public static function parseArrayList($stringList, $fistDelimiter = ';', $secondDelimiter = '|'){
			$a = array();
			if(!empty($stringList)){
			foreach (explode($fistDelimiter, $stringList) as $aa) {
				list ($cKey, $cValue) = explode($secondDelimiter, $aa, 2);
				$a[$cKey] = $cValue;
			}
			}
			return $a;
		}
		
		public static function getDefaultCheckMode() {
			return self::$defaultCheckMode;
		}
		
		public static function getImagePath($type) {
			return self::$image_paths[$type];
		}
		
		public static function getImageUrlResized($type, $path = null, $resizedpath = null ) {
			if ($path == '' || $path===null)
				return '';
			$finalPath = self::$image_basePathCDN . COM_BOOKINGFORCONNECTOR_IMGURL;
			if (isset($type) && isset(self::$image_paths[$type])) {
				$finalPath .= self::$image_paths[$type] ;
				if (!empty($resizedpath)) {
						$pathfilename = basename($path);
						if (isset(self::$image_path_resized[$resizedpath])) {
							$path = str_replace($pathfilename, self::$image_path_resized[$resizedpath] . "/".$pathfilename ,$path);
						} else {
							$path = str_replace($pathfilename, $resizedpath . "/".$pathfilename ,$path);
						}
				}
				$finalPath .= $path;
			}
					
			return $finalPath;
		}

		public static function getImageUrl($type, $path = null, $resizepars = null ) {
			if ($path == '' || $path===null)
				return '';
			$finalPath = self::$image_basePath;
			if (isset($type) && isset(self::$image_paths[$type])) {
				$finalPath .= self::$image_paths[$type] . $path;
				if (isset($resizepars)) {
					// resize params manually added
					if (is_array($resizepars)) {
						$params = '';
						foreach ($resizepars as $param) {
							if ($params=='') 
								$params .= '?';
							else
								$params .= '&';
							$params .= $param;
						}
						if ($params!='') {
							$finalPath .= $params;
						}
					} else { // resize params as predefined configuration
						if (isset(self::$image_resizes[$resizepars])) {
							$finalPath .= '?' . self::$image_resizes[$resizepars];
						}
					}
				}
			}
			
			return $finalPath;
		}
		
		public static function getDefaultParam($param) {
			switch (strtolower($param)) {
				case 'checkin':
					return DateTime::createFromFormat('d/m/Y',self::getStartDate(),new DateTimeZone('UTC'));
					//return new DateTime('UTC');
					break;
				case 'checkout':
					$co = DateTime::createFromFormat('d/m/Y',self::getStartDate(),new DateTimeZone('UTC'));
					//$co = new DateTime('UTC');
					return $co->modify(self::$defaultDaysSpan);
					break;
				case 'duration':
					return self::$defaultDuration;
					break;
				case 'extras':
					return '';
					break;
				case 'paxages':
					return '';
					break;
				case 'pricetype':
					return '';
					break;
				default:
					break;
			}
		}
		
		/* http://blog.amnuts.com/2011/04/08/sorting-an-array-of-objects-by-one-or-more-object-property/
		 * 
		 * Sort an array of objects.
		 * 
		 * You can pass in one or more properties on which to sort.  If a
		 * string is supplied as the sole property, or if you specify a
		 * property without a sort order then the sorting will be ascending.
		 * 
		 * If the key of an array is an array, then it will sorted down to that
		 * level of node.
		 * 
		 * Example usages:
		 * 
		 * osort($items, 'size');
		 * osort($items, array('size', array('time' => SORT_DESC, 'user' => SORT_ASC));
		 * osort($items, array('size', array('user', 'forname'))
		 * 
		 * @param array $array
		 * @param string|array $properties
		 * 
		 */
		public static function osort(&$array, $properties) {
			if (is_string($properties)) {
				$properties = array($properties => SORT_ASC);
			}
			uasort($array, function($a, $b) use ($properties) {
				foreach($properties as $k => $v) {
					if (is_int($k)) {
						$k = $v;
						$v = SORT_ASC;
					}
					$collapse = function($node, $props) {
						if (is_array($props)) {
							foreach ($props as $prop) {
								$node = (!isset($node->$prop)) ? null : $node->$prop;
							}
							return $node;
						}else {
							return (!isset($node->$props)) ? null : $node->$props;
						}
					};
					$aProp = $collapse($a, $k);
					$bProp = $collapse($b, $k);
					if ($aProp != $bProp) {
						return ($v == SORT_ASC)
							? strnatcasecmp($aProp, $bProp)
							: strnatcasecmp($bProp, $aProp);
					}
				}
				return 0;
			});
		}
		
		public static function getCookie($cookieName, $defaultValue=null) {
//			$app = JFactory::getApplication();
//			$cookieValue = $app->input->cookie->get($cookieName, $defaultValue);
			$cookieValue = $_COOKIE[$cookieName];
			return $cookieValue;
		}

		public static function SetTwoFactorCookie($id) {
			$expire=time()+60*60*24*self::$TwoFactorAuthenticationDeviceExpiration;
			$ok = setcookie(self::$TwoFactorCookieName, $id, $expire,SITECOOKIEPATH, COOKIE_DOMAIN);
		}
		public static function GetTwoFactorCookie() {
			$twofactorCookie = BFCHelper::getCookie(self::$TwoFactorCookieName);
			return $twofactorCookie;
		}
		public static function DeleteTwoFactorCookie() {
			setcookie( self::$TwoFactorCookieName, '', 0,SITECOOKIEPATH, COOKIE_DOMAIN);
			unset( $_COOKIE[self::$TwoFactorCookieName] );
		}


		public static function getLoginTwoFactor($email, $password, $twoFactorAuthCode,$deviceCodeAuthCode) {
	//J->			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//J->			$model = JModelLegacy::getInstance('Portal', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelPortal;
			return $model->getLoginTwoFactor($email, $password, $twoFactorAuthCode,$deviceCodeAuthCode);
		}	
		public static function AddToFavourites($id) {
			$expire=time()+60*60*24*30;
			$counter = 1;
			$listFav = (string) $id;
			$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
			if (isset($varCook))
			{
				$arr= explode(",", $varCook);
				if ( !self::IsInFavourites($id)){
					array_push($arr, $id);
				}
				$arr = array_filter( $arr );
				$counter = count($arr);
				$listFav = implode(",", $arr);
			}
			$config = JFactory::getConfig();
			$cookie_domain = $config->get('cookie_domain', '');
			$cookie_path = $config->get('cookie_path', '/');
			$ok = setcookie(self::$favouriteCookieName, $listFav, $expire, $cookie_path, '');
			//setcookie(self::$favouriteCookieName, $listFav, $expire, $cookie_path, $cookie_domain);
			return $counter;
		}
		
		public static function RemoveFromFavourites($id) {
			$expire=time()+60*60*24*30;
			$listFav = (string) $id;
			$counter = 0;
			$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
			if (isset($varCook))
			{
				$arr= explode(",", $varCook);
				if(($key = array_search($id, $arr)) !== false) {
					unset($arr[$key]);
				}
				$arr = array_filter( $arr );
				$counter = count($arr);
				$listFav = implode(",", $arr);
			}
			$config = JFactory::getConfig();
			$cookie_domain = $config->get('cookie_domain', '');
			$cookie_path = $config->get('cookie_path', '/');
			setcookie(self::$favouriteCookieName, $listFav, $expire, $cookie_path, '');
			//setcookie(self::$favouriteCookieName, $listFav, $expire);
			return $counter;
		}
		
		public static function IsInFavourites($id) {
			$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
			if (isset($varCook))
			{
				$arr= explode(",", $varCook);
				return in_array($id, $arr);
			}
			return false;
		}
		
		public static function CountFavourites() {
			$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
			if (isset($varCook))
			{
				$arr= explode(",", $varCook);
				return count(array_filter($arr));
			}
			return 0;
		}
		
		public static function GetFavourites() {
			$varCook = BFCHelper::getCookie(self::$favouriteCookieName);
			if (isset($varCook))
			{
				$arr= explode(",", $varCook);
				return $arr;
			}
			return null;
		}

	//for analytics
			public static function AddToCookieOrders($id) {
				$expire=time()+60*60*24*30;
				$counter = 1;
				$lisTordersCookie = (string) $id;
				$varCook = BFCHelper::getCookie(self::$ordersCookieName);				
				if (isset($varCook))
				{
					$arr= explode("_", $varCook);
					if ( !self::IsInCookieOrders($id)){
						array_push($arr, (string)$id);
					}
					$arr = array_filter( $arr );
					$counter = count($arr);
					$lisTordersCookie = (string)implode("_", $arr);				
				}
//				$config = JFactory::getConfig();
//				$cookie_domain = $config->get('cookie_domain', '');
//				$cookie_path = $config->get('cookie_path', '/');
//				$ok = setcookie(self::$ordersCookieName, $lisTordersCookie, $expire, $cookie_path, '');
				$ok = setcookie(self::$ordersCookieName, $lisTordersCookie, $expire);
				return $counter;
			}
					
			public static function IsInCookieOrders($id) {
				$varCook = BFCHelper::getCookie(self::$ordersCookieName);
				
				
				if (isset($varCook))
				{
					$arr= explode("_", $varCook);
					return in_array($id, $arr);
				}
				return false;
			}

		public static function setSearchOnSellParamsSession($params) {
			$sessionkey = 'searchonsell.params';
	//		$_SESSION[$sessionkey] = $params;
			$pars = self::setSession($sessionkey, $params, 'com_bookingforconnector'); // $_SESSION[$sessionkey];
		}
		
		public static function getSearchOnSellParamsSession() {
			$sessionkey = 'searchonsell.params';
	//		$session = JFactory::getSession();
			$pars = self::getSession($sessionkey, '', 'com_bookingforconnector'); // $_SESSION[$sessionkey];
			return $pars;
		}
		
		public static function setSearchMerchantParamsSession($params) {
			$sessionkey = 'searchmerchant.params';
			$pars = array();
			$pars['merchantCategoryId'] = !empty($params['merchantCategoryId']) ? $params['merchantCategoryId']: 0;
			if(isset($params['searchid'])){
				$pars['searchid'] = $params['searchid'];
			}
			if(isset($params['newsearch'])){
				$pars['newsearch'] = $params['newsearch'];
			}
			if(isset($params['points'])){
				$pars['points'] = $params['points'];
			}
			$pars['locationzone'] = !empty($params['locationzone']) ? $params['locationzone']: "";
			$pars['locationzones'] = !empty($params['locationzones']) ? $params['locationzones']: "";
			$pars['stateIds'] = !empty($params['stateIds']) ? $params['stateIds']: "";
			$pars['regionIds'] = !empty($params['regionIds']) ? $params['regionIds']: "";
			$pars['cityIds'] = !empty($params['cityIds']) ? $params['cityIds']: "";
			$pars['zoneIds'] = !empty($params['zoneIds']) ? $params['zoneIds']: "";
			$pars['cultureCode'] = !empty($params['cultureCode']) ? $params['cultureCode']: "";
			$pars['merchantTagIds'] = !empty($params['merchantTagIds']) ? $params['merchantTagIds']:"";
			$pars['tags'] = !empty($params['tags']) ? $params['tags']:"";
			$pars['rating'] = !empty($params['rating']) ? $params['rating']:"";
			$pars['filters'] = !empty($params['filters']) ? $params['filters']: "";
			self::setSession($sessionkey, $pars, 'com_bookingforconnector'); 
		}
		public static function getSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.params';
			$pars = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $pars;
		}
		public static function setFilterSearchMerchantParamsSession($paramsfilters) {
			$sessionkey = 'searchmerchant.filterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getFilterSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.filterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}

		public static function setEnabledFilterSearchMerchantParamsSession($paramsfilters) {
			$sessionkey = 'searchmerchant.enabledfilterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getEnabledFilterSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.enabledfilterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}
		public static function setFirstFilterSearchMerchantParamsSession($paramsfilters) {
			$sessionkey = 'searchmerchant.firstfilterparams';
			self::setSession($sessionkey, $paramsfilters, 'com_bookingforconnector'); 
		}
		
		public static function getFirstFilterSearchMerchantParamsSession() {
			$sessionkey = 'searchmerchant.firstfilterparams';
			$paramsfilters = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			return $paramsfilters;
		}

		public static function setSearchParamsSession($params) {
			$sessionkey = 'search.params';
			$pars = array();
			if(isset($params['checkin'])){
				$pars['checkin'] = $params['checkin'];
			}
			if(isset($params['checkout'])){
				$pars['checkout'] = $params['checkout'];
			}
			if(isset($params['duration'])){
				$pars['duration'] = $params['duration'];
			}
			if(isset($params['paxes'])){
				$pars['paxes'] = $params['paxes'];
			}
			if(isset($params['paxages'])){
				$pars['paxages'] = $params['paxages'];
			}
			
			$pars['onlystay'] = !empty($params['onlystay']) ? $params['onlystay']: 1;
			$pars['searchtypetab'] = !empty($params['searchtypetab']) ? $params['searchtypetab']: "0";
			$pars['masterTypeId'] = !empty($params['masterTypeId']) ? $params['masterTypeId']: "0";
			$pars['merchantResults'] = !empty($params['merchantResults']) ? $params['merchantResults']: 0;
			$pars['merchantCategoryId'] = !empty($params['merchantCategoryId']) ? $params['merchantCategoryId']: 0;
			$pars['zoneId'] = !empty($params['zoneId']) ? $params['zoneId']: 0;
			$pars['cityId'] = !empty($params['cityId']) ? $params['cityId']: 0;
			$pars['locationzone'] = !empty($params['locationzone']) ? $params['locationzone']: "";
			$pars['locationzones'] = !empty($params['locationzones']) ? $params['locationzones']: "";
			$pars['zoneIds'] = !empty($params['zoneIds']) ? $params['zoneIds']: "";
			$pars['cultureCode'] = !empty($params['cultureCode']) ? $params['cultureCode']: "";
			$pars['filters'] = !empty($params['filters']) ? $params['filters']: "";
			$pars['resourceName'] = !empty($params['resourceName']) ? $params['resourceName']: "";
			$pars['refid'] = !empty($params['refid']) ? $params['refid']: "";
			$pars['pricerange'] = !empty($params['pricerange']) ? $params['pricerange']: 0;
			$pars['bookableonly'] = !empty($params['bookableonly']) ? $params['bookableonly']: 0;
			$pars['condominiumsResults'] = !empty($params['condominiumsResults']) ? $params['condominiumsResults']: 0;
			$pars['productTagIds'] = !empty($params['productTagIds']) ? $params['productTagIds']:"";
			$pars['merchantTagIds'] = !empty($params['merchantTagIds']) ? $params['merchantTagIds']:"";
			$pars['merchantIds'] = !empty($params['merchantIds']) ? $params['merchantIds']:"";

			$pars['variationPlanIds'] = !empty($params['variationPlanIds']) ? $params['variationPlanIds']:"";

			if(isset($params['merchantId'])){
				$pars['merchantId'] = $params['merchantId'];
			}
			if(!empty($params['availabilitytype'])){
				$pars['availabilitytype'] = $params['availabilitytype'];
			}
			if(isset($params['itemtypes'])){
				$pars['itemtypes'] = $params['itemtypes'];
			}
			if(isset($params['groupresulttype'])){
				$pars['groupresulttype'] = $params['groupresulttype'];
			}
			if(isset($params['searchid'])){
				$pars['searchid'] = $params['searchid'];
			}
			if(isset($params['newsearch'])){
				$pars['newsearch'] = $params['newsearch'];
			}
			if(isset($params['stateIds'])){
				$pars['stateIds'] = $params['stateIds'];
			}
			if(isset($params['regionIds'])){
				$pars['regionIds'] = $params['regionIds'];
			}
			if(isset($params['cityIds'])){
				$pars['cityIds'] = $params['cityIds'];
			}
			if(isset($params['points'])){
				$pars['points'] = $params['points'];
			}						
			self::setSession($sessionkey, $pars, 'com_bookingforconnector'); 

		}
		
		public static function getSearchParamsSession() {
			$sessionkey = 'search.params';
			$pars = self::getSession($sessionkey, '', 'com_bookingforconnector'); 
			$pars = unserialize(serialize($pars));
			return $pars;
		}
		
		public static function setFilterSearchParamsSession($paramsfilters) {
			$sessionkey = 'search.filterparams';
			$_SESSION[$sessionkey] = $paramsfilters;
		}
		
		public static function getFilterSearchParamsSession() {
			$sessionkey = 'search.filterparams';
			$paramsfilters = isset($_SESSION[$sessionkey]) ? $_SESSION[$sessionkey] : array();
			$paramsfilters = unserialize(serialize($paramsfilters));
			return $paramsfilters;
		}

		public static function setEnabledFilterSearchParamsSession($paramsfilters) {
			$sessionkey = 'search.enabledfilterparams';
			$_SESSION[$sessionkey] = $paramsfilters;
		}
		
		public static function getEnabledFilterSearchParamsSession() {
			$sessionkey = 'search.enabledfilterparams';
			$paramsfilters = isset($_SESSION[$sessionkey]) ? $_SESSION[$sessionkey] : array() ;
			return $paramsfilters;
		}
		public static function setFirstFilterSearchParamsSession($paramsfilters) {
			$sessionkey = 'search.firstfilterparams';
			$_SESSION[$sessionkey] = $paramsfilters;
		}
		
		public static function getFirstFilterSearchParamsSession() {
			$sessionkey = 'search.firstfilterparams';
			$paramsfilters = isset($_SESSION[$sessionkey]) ? $_SESSION[$sessionkey] : array() ;
			return $paramsfilters;
		}

		public static function setState($stateObj, $key, $namespace = null) {
			if (isset($namespace)) {
				$key = $namespace . '.' . $key;
			}
			self::$currentState[$key] = $stateObj;
		}

		public static function getState($key, $namespace = null) {
			if (isset($namespace)) {
				$key = $namespace . '.' . $key;
			}
			if (isset(self::$currentState[$key])) {
				return self::$currentState[$key];
			}
			return null;
		}
		
		public static function orderBy($a, $b, $ordering, $direction) {
			return ($a->$ordering < $b->$ordering) ? 
			(
				($direction == 'desc') 
					? 1 
					: -1
			) : 
			(
				($a->$ordering > $b->$ordering) 
					?	(
							($direction == 'desc') 
								? -1 
								: 1
						) 
					: 0
			);
		}
		
		public static function orderByStay($a, $b, $direction) {
			return ($a->Resources[0]->TotalPrice < $b->Resources[0]->TotalPrice) ? 
			(
				($direction == 'desc') 
					? 1 
					: -1
			) : 
			(
				($a->Resources[0]->TotalPrice > $b->Resources[0]->TotalPrice) 
					?	(
							($direction == 'desc') 
								? -1 
								: 1
						) 
					: 0
			);
		}
		
		
		public static function orderByDiscount($a, $b, $direction) {
			return ($a->Resources[0]->TotalPrice - $a->Resources[0]->Price < $b->Resources[0]->TotalPrice - $b->Resources[0]->Price) ? 
			(
				($direction == 'desc') 
					? 1 
					: -1
			) : 
			(
				($a->Resources[0]->TotalPrice - $a->Resources[0]->Price > $b->Resources[0]->TotalPrice - $b->Resources[0]->Price) 
					?	(
							($direction == 'desc') 
								? -1 
								: 1
						) 
					: 0
			);	
		}
			public static function orderBySingleDiscount($a, $b, $direction) {
			return ($a->TotalPrice - $a->Price < $b->TotalPrice - $b->Price) ? 
			(
				($direction == 'desc') 
					? 1 
					: -1
			) : 
			(
				($a->TotalPrice - $a->Price > $b->TotalPrice - $b->Price) 
					?	(
							($direction == 'desc') 
								? -1 
								: 1
						) 
					: 0
			);	
		}
		
		
		public static function getStayParam($param, $default= null) {
			date_default_timezone_set('UTC');
			$pars = self::getSearchParamsSession();

			switch (strtolower($param)) {
				case 'checkin':
					$strCheckin = isset($_REQUEST['checkin']) ? $_REQUEST['checkin'] :null ;  
					if (($strCheckin == null || $strCheckin == '') && (isset($pars['checkin']) && $pars['checkin'] != null && $pars['checkin'] != '')) {
						return clone $pars['checkin'];
					}
					$checkin = DateTime::createFromFormat('d/m/Y',$strCheckin,new DateTimeZone('UTC'));
					if ($checkin===false && isset($default)) {
						$checkin = $default;
					}
					return $checkin;
					break;
				case 'checkout':
					$strCheckout =  isset($_REQUEST['checkout']) ? $_REQUEST['checkout'] :null ;  
					if (($strCheckout == null || $strCheckout == '') && (isset($pars['checkout']) && $pars['checkout'] != null && $pars['checkout'] != '')) {
						return clone $pars['checkout'];
					}
					$checkout = DateTime::createFromFormat('d/m/Y',$strCheckout,new DateTimeZone('UTC'));
					if ($checkout===false && isset($default)) {
						$checkout = $default;
					}
					return $checkout;
					break;
				case 'duration':
					$ci = self::getStayParam('checkin', new DateTime('UTC'));
					$dco = new DateTime('UTC');
					$co = self::getStayParam('checkout', $dco->modify('+7 days'));
					$interval = $co->diff($ci);
					return $interval->d;
					break;
				case 'extras':
					$extraVar =  isset($_REQUEST['extras']) ? $_REQUEST['extras'] : '';
					$extras = "";
					if (!empty($extraVar)){
					$extras = implode('|',
						array_filter($extraVar, function($var) {
								$vals = explode(':', $var);
								if (count($vals) < 2 || $vals[1] == '') return false;
								return true;
							})
						);
					}
					return $extras;
					break;
				case 'packages':
					$packagesVar = isset($_REQUEST['packages']) ? $_REQUEST['packages'] : '';
					$packages = "";
					if (!empty($packagesVar)){
					$packages = implode('|',
						array_filter($packagesVar, function($var) {
								$vals = explode(':', $var);
								if (count($vals) < 3 || $vals[2] == 0 || $vals[1] == 0) return false;
								return true;
							})
						);
					}
					return $packages;
					break;				
				case 'selectedprices':
					$extras = implode('|',
						array_filter($_REQUEST['extras'], function($var) {
							$vals = explode(':', $var);
							if (count($vals) < 2 || $vals[1] == '') return false;
							return true;
						})
					);
					return $extras;
					break;
				case 'paxages':
					$adults = isset($_REQUEST['adults']) ? $_REQUEST['adults'] : self::$defaultAdultsQt;
					$children = isset($_REQUEST['children']) ? $_REQUEST['children'] : 0;
					$seniores = isset($_REQUEST['seniores']) ? $_REQUEST['seniores'] : 0;
					if (($adults == null || $adults == '') && ($children == null || $children == '') && (isset($pars['paxages']) && $pars['paxages'] != null && $pars['paxages'] != '')) {
						return array_slice($pars['paxages'],0);
					}
					$strAges = array();
					for ($i = 0; $i < $adults; $i++) {
						$strAges[] = self::$defaultAdultsAge;
					}
					for ($i = 0; $i < $seniores; $i++) {
						$strAges[] = self::$defaultSenioresAge;
					}
					if ($children > 0) {
						for ($i = 0;$i < $children; $i++) {
							$age =$_REQUEST['childages'.($i+1)];
							if ($age < self::$defaultAdultsAge) {
								$strAges[] = $age;
							}
						}
					}
					return $strAges;
					break;
				case 'pricetype':
					return isset($_REQUEST['pricetype']) ? $_REQUEST['pricetype'] : '';
					break;
				case 'pricerange':
					return isset($_REQUEST['pricerange']) ? $_REQUEST['pricerange'] : 0;
					break;
				case 'rateplanid':
					return isset($_REQUEST['pricetype']) ? $_REQUEST['pricetype'] : 0;  
					break;
				case 'variationplanid':
					return isset($_REQUEST['variationPlanId']) ? $_REQUEST['variationPlanId'] : '';
					break;				
				case 'state':
					return isset($_REQUEST['state']) ? $_REQUEST['state'] : '';
				default:
					break;
			}
		}
		
		public static function convertTotal($x){
			switch($x){
				case $x < 3:
					$y = 0;
					break;
				case $x < 4:
					$y = 1;
					break;
				case $x < 5:
					$y = 2;
					break;
				case $x <= 5.5:
					$y = 3;
					break;
				case $x < 6:
					$y = 4;
					break;
				case $x < 7:
					$y = 5;
					break;
				case $x < 8:
					$y = 6;
					break;
				case $x <= 8.5:
					$y = 7;
					break;
				case $x < 9:
					$y = 8;
					break;
				case $x < 10:
					$y = 9;
					break;
				case $x == 10:
					$y = 10;
					break;
				default:
					$y = 4;
					break;
			}
			return $y;
		}

//		public static function encrypt($string,$key=null) {
//			if(empty($key)){
//				$key = 'WZgfdUps';
//			}
//			$key = str_pad($key, 24, "\0"); 
//			$cipher_alg = MCRYPT_TRIPLEDES;
//		
//			$iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
//			 
//	 
//			$encrypted_string = mcrypt_encrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
//			return base64_encode($encrypted_string).$key;
//		}
//		
//	   public static function decrypt($string,$urldecode = false,$key=null) {
//				if ($urldecode) {
//					$string = urldecode($string);
//				}
//				
//				$string = base64_decode($string);
//	 
//				//key 
//				if(empty($key)){
//					$key = 'WZgfdUps';
//				}
//				$key = str_pad($key, 24, "\0"); 
//				 
//	 
//				$cipher_alg = MCRYPT_TRIPLEDES;
//	 
//				$iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
//				 
//	 
//				$decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
//				return trim($decrypted_string);
//		}
	public static function encryptSupported()
	{
		$cryptoVersion= 0;

		if (function_exists('mcrypt_create_iv') && function_exists('mcrypt_get_iv_size') && function_exists('mcrypt_encrypt') && function_exists('mcrypt_decrypt'))
		{
			$cryptoVersion= 1;
		}
		if (function_exists('openssl_random_pseudo_bytes') && function_exists('openssl_cipher_iv_length') && function_exists('openssl_encrypt') && function_exists('openssl_decrypt'))
		{
			$cryptoVersion= 2;
		}

		return $cryptoVersion;
	}

	// OPENSSL
	// - funzione di criptazione/decriptazione basato su una chiave
	public static function encryptOpenSll($string,$key=null) {
		$cipher = 'AES-256-CBC';
		// Must be exact 32 chars (256 bit)
		$password = substr(hash('sha256', $key, true), 0, 32);
		// IV must be exact 16 chars (128 bit)
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		$encrypted_string = openssl_encrypt($string, $cipher, $password, OPENSSL_RAW_DATA, $iv);
		return base64_encode($encrypted_string);
	}
	public static function decryptOpenSll($string,$urldecode = false,$key=null) {
		if ($urldecode) {
			$string = urldecode($string);
		}
		$string = base64_decode($string);
		$cipher = 'AES-256-CBC';
		// Must be exact 32 chars (256 bit)
		$password = substr(hash('sha256', $key, true), 0, 32);
		// IV must be exact 16 chars (128 bit)
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		$encrypted_string = openssl_decrypt($string, $cipher, $password, OPENSSL_RAW_DATA, $iv);
		return $encrypted_string;
	}

	// MCRYPT
	// - funzione di criptazione/decriptazione basato su una chiave
		public static function encryptMcrypt($string,$key=null) {
			//Key 
			if(empty($key)){
				$key = COM_BOOKINGFORCONNECTOR_KEY;
			}
			
			$key = str_pad($key, 24, "\0");  
					 
			//Encryption
			$cipher_alg = MCRYPT_TRIPLEDES;
		
			$iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
			 
	 
			$encrypted_string = mcrypt_encrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
			return base64_encode($encrypted_string).$key;
	//        return $encrypted_string;
		}
		
	   public static function decryptMcrypt($string,$urldecode = false,$key=null) {
				if ($urldecode) {
					$string = urldecode($string);
				}
				
				$string = base64_decode($string);
	 
				//key 
				if(empty($key)){
					$key = COM_BOOKINGFORCONNECTOR_KEY;
				}
				$key = str_pad($key, 24, "\0");               
	 
				$cipher_alg = MCRYPT_TRIPLEDES;
	 
				$iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
				 
	 
				$decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
				return trim($decrypted_string);
		}


	public static function encrypt($string,$key=null) {
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==1) {
			return self::encryptMcrypt($string);
		}
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==2) {
			return self::encryptOpenSll($string,$key);
		}
		return null;
	}
	public static function decrypt($string,$urldecode = false,$key=null) {
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==1) {
			return self::decryptMcrypt($string,$urldecode);
		}
		if (COM_BOOKINGFORCONNECTOR_CRYPTOVERSION==2) {
			return self::decryptOpenSll($string,$urldecode,$key);
		}
		return null;
	}


		public static function getOrderMerchantPaymentId($order) {
			if(!empty($order)){
				$bookingTypeId = self::getItem($order->NotesData, 'bookingTypeId');
				if ($bookingTypeId!=''){
					return $bookingTypeId;
				}
				$bookingTypeId = self::getItem($order->NotesData, 'merchantBookingTypeId');
				if ($bookingTypeId!=''){
					return $bookingTypeId;
				}
			}
			return null;
		}

		public static function calculate_paxages($post, $adults = NULL, $children = NULL, $seniores = NULL) {
			$seniores = isset($seniores) ? $seniores : 0;
			$adults = isset($adults) ? $adults : BFCHelper::$defaultAdultsQt;
			$children = isset($children) ? $children : 0;
			$strAges = array();
			for ($i = 0; $i < $adults; $i++) {
			  $strAges[] = BFCHelper::$defaultAdultsAge;
			}
			for ($i = 0; $i < $seniores; $i++) {
			  $strAges[] = BFCHelper::$defaultSenioresAge;
			}
			if ($children > 0) {
			  for ($i = 0;$i < $children; $i++) {
				$age = $post['childages'.($i+1)];
				if($age == NULL) {
				  $age = 0;
				}
				 if ($age < BFCHelper::$defaultAdultsAge) {
				   $strAges[] = $age;
				 }
			  }
			}
			return $strAges;
		}
		public static function getCustomerData($formData) {
			if ($formData == null) {
				$formData = $_POST['form'];
			}

					
					$Firstname = isset($formData['Name'])?$formData['Name']:''; //
					$Lastname = isset($formData['Surname'])?$formData['Surname']:''; // => $formData['Surname'],
					$Email = isset($formData['Email'])?$formData['Email']:''; // => $formData['Email'],
					$Address = isset($formData['Address'])?$formData['Address']:''; // => $formData['Address'],
					$Zip = isset($formData['Cap'])?$formData['Cap']:''; // => $formData['Cap'],
					$City = isset($formData['City'])?$formData['City']:''; // => $formData['City'],
					$Country = isset($formData['Provincia'])?$formData['Provincia']:''; // => $formData['Provincia'],
					$Nation = isset($formData['Nation'])?self::getOptionsFromSelect($formData, 'Nation'):''; // => self::getOptionsFromSelect($formData, 'Nation'),
					$Phone = isset($formData['Phone'])?$formData['Phone']:''; // => $formData['Phone'],
					$Fax = isset($formData['Fax'])?$formData['Fax']:''; // => $formData['Fax'],
					$VatCode = isset($formData['VatCode'])?$formData['VatCode']:''; // => $formData['VatCode'],
					$Culture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
					$UserCulture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
					$Culture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$Culture; // => self::getOptionsFromSelect($formData, 'Culture'),
					$UserCulture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$UserCulture; // => self::getOptionsFromSelect($formData, 'Culture'),
					$gender = isset($formData['Gender'])?self::getOptionsFromSelect($formData, 'Gender'):'';

			$customerData = array(
					'Firstname' => $Firstname,
					'Lastname' => $Lastname,
					'Email' => $Email,
					'Address' => $Address,
					'Zip' => $Zip,
					'City' => $City,
					'Country' => $Country,
					'Nation' => $Nation,
					'Phone' => $Phone,
					'Fax' => $Fax,
					'VatCode' => $VatCode,
					'Culture' => $Culture,
					'UserCulture' => $UserCulture,
					'BirthDate' => isset($formData['Birthday']) ? DateTime::createFromFormat('d/m/Y', $formData['Birthday'],new DateTimeZone('UTC'))->format("Y-m-d"): null,
					'Gender' => $gender,
			);
					
			return $customerData;
		}
		public static function canAcquireCCData($formData) {
			if ($formData == null) {
				$formData = $_POST['form'];
			}		
			
			if(!empty($formData['bookingType'])){
				$bt = $formData['bookingType'];
				if (is_array($bt)) { // if is an array (because it is sent using a select)
					$bt = $bt[0]; // keep only the first value
				}
				if ($bt != '') { // need to check for acquire cc data
					$btData = explode(':',$bt); // data is sent like 'ID:acquireccdata' -> '9:1' or '9:0' or '9:' (where zero is replaced by an empty char)
					if (count($btData) > 1) { // we have more than one value so data sent is correct
						if ($btData[1] != '') { // need to set mandatory for field credit card prefixed with 'cc_' (or other supplied prefix)
							return true;
						}
					}
				}
			}
			return false;
		}
		
		public static function getCCardData($formData) {
			if ($formData == null) {
				$formData = $_POST['form'];
			}
			
				if(isset($formData['cc_numero']) && !empty($formData['cc_numero'])) {
					$ccData = array(
							'Type' => self::getOptionsFromSelect($formData,'cc_circuito'),
							'TypeId' => self::getOptionsFromSelect($formData,'cc_circuito'),
							'Number' => $formData['cc_numero'],
							'Name' => $formData['cc_titolare'],
							'ExpiryMonth' => $formData['cc_mese'],
							'ExpiryYear' => $formData['cc_anno']
					);
					
					return $ccData;
				}
				return null;
		}

		public static function ConvertIntTimeToDate($timeMinEnd)
		{
				$returnDateTime = new DateTime(1, 1, 1);
				if ($timeMinEnd > 0)
				{
					$hour = $timeMinEnd / 10000;
					$minute = ($timeMinEnd - hour * 10000) / 100;
					$returnDateTime->modify('+{$hour} hours');
					$returnDateTime->modify('+{$minute} minutes');
				}
				return $returnDateTime;
		}
		public static function ConvertIntTimeToMinutes($timeMin)
		{
				$returnMinute =0;
				if ($timeMin > 0)
				{
					$hour = $timeMin / 10000;
					$minute = ($timeMin - $hour * 10000) / 100;
					$returnMinute = $hour* 60 + $minute;
				}
				return $returnMinute;
		}

		public static function shorten_string($string, $amount)
		{
			 if(strlen($string) > $amount)
			{
				$string = trim(substr($string, 0, $amount))."...";
			}
			return $string;
		}

		public static function getVar($string, $defaultValue=null) {
			return isset($_REQUEST[$string]) ? $_REQUEST[$string] : $defaultValue;
		}
		public static function getFloat($string, $defaultValue=null) {
			
			$jinput = isset($_REQUEST[$string]) ? str_replace(",", ".", $_REQUEST[$string]) : $defaultValue;
			
			return floatval($jinput);
		}
		public static function getOptionsFromSelect($formData, $str){
			if ($formData == null) {
				$formData = $_POST['form'];
			}

			$aStr = isset($formData[$str])?$formData[$str]:null;
			if(isset($aStr))
			{
				if (!is_array($aStr)) return $aStr;
				$nStr = count($aStr);
				if ($nStr==1){
					return $aStr[0];
				}else
				{
					return implode($aStr, ',');
				}
			}
			return '';
		}

		public static function getSession($string, $defaultValue=null, $prefix ='') {
			if(empty(COM_BOOKINGFORCONNECTOR_ENABLECACHE)) return null;
			return isset($_SESSION[COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY.$prefix.$string]) ? $_SESSION[COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY.$prefix.$string] : $defaultValue;
		}
		public static function setSession($string, $value=null, $prefix ='') {
			$_SESSION[COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY.$prefix.$string] = $value;
		}

		public static function pushStay($arr, $resourceid, $resStay, $defaultResource = null) {
			$selected = array_values(array_filter($arr, function($itm) use ($resourceid) {
				return $itm->ResourceId == $resourceid;
			}));
			$index = 0;
			if(count($selected) == 0) {
				$obj = new stdClass();
				$obj->ResourceId = $resourceid;
				
				if(isset($defaultResource) && $defaultResource->ResourceId == $resourceid) {
					$obj->MinCapacityPaxes = $defaultResource->MinCapacityPaxes;
					$obj->MaxCapacityPaxes = $defaultResource->MaxCapacityPaxes;
					$obj->Name = $defaultResource->Name;
					$obj->ImageUrl = $defaultResource->ImageUrl;
					$obj->Availability = $defaultResource->Availability;
					$obj->AvailabilityType = $defaultResource->AvailabilityType;
					$obj->Policy = $resStay->Policy;
				} else {
					$obj->MinCapacityPaxes = $resStay->MinCapacityPaxes;
					$obj->MaxCapacityPaxes = $resStay->MaxCapacityPaxes;
					$obj->Availability = $resStay->Availability;
					$obj->AvailabilityType = $resStay->AvailabilityType;
					$obj->Name = $resStay->ResName;
					$obj->ImageUrl = $resStay->ImageUrl;
					$obj->Policy = $resStay->Policy;
					$obj->TimeLength = $resStay->TimeLength;
				}
				$obj->RatePlans = array();
				//$obj->Policy = $completestay->Policy;
				//$obj->Description = $singleRateplan->Description;
				$arr[] = $obj;
				$index = count($arr) - 1;
			} else {
				$index = array_search($selected[0], $arr);
				//$obj = $selected[0];
			}

			$rt = new stdClass();
			$rt->RatePlanId = $resStay->RatePlanId;
			$rt->Name = $resStay->Name;	
			$rt->RatePlanRefId = isset($resStay->RefId) ? $resStay->RefId : "";	
			$rt->PercentVariation = $resStay->PercentVariation;	
			
			$rt->TotalPrice=0;
			$rt->TotalPriceString ="";
			$rt->Days=0;
			$rt->BookingType=$resStay->BookingType;
			$rt->IsBookable=$resStay->IsBookable;
			$rt->CheckIn = BFCHelper::parseJsonDate($resStay->CheckIn); 
			$rt->CheckOut= BFCHelper::parseJsonDate($resStay->CheckOut);

			$rt->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
			$rt->SelectablePrices = $resStay->SelectablePrices;
			$rt->Variations = $resStay->Variations;
			$rt->SimpleDiscountIds = implode(',', $resStay->SimpleDiscountIds);	
			if(!empty($resStay->SuggestedStay->DiscountedPrice)){
				$rt->TotalPrice = (float)$resStay->SuggestedStay->TotalPrice;
				$rt->TotalPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->TotalPrice);
				$rt->Days = $resStay->SuggestedStay->Days;
				$rt->DiscountedPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->DiscountedPrice);
				$rt->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
			}
			
			$arr[$index]->RatePlans[] = $rt;
			
			return $arr;
		}

		public static function ParsePriceParameter($str)
			{
				$array = explode(':',$str);
				$newarray = array(
					"PriceId" => intval($array[0]),
					"ProductId" => intval($array[0]),
					"Quantity" =>intval($array[1]),
					"CheckInDateTime" => count($array) > 2 && !empty($array[2]) ? DateTime::createFromFormat("YmdHis", $array[2],new DateTimeZone('UTC')) : null,
					"PeriodDuration" => count($array) > 3 && !empty($array[3]) ? intval($array[3]) : 0,
					"TimeSlotId" => count($array) > 4 && !empty($array[4]) ? intval($array[4]) : 0,
					"TimeSlotStart" => count($array) > 5 && !empty($array[5]) ? intval($array[5]) : 0,
					"TimeSlotEnd" => count($array) > 6 && !empty($array[6]) ? intval($array[6]) : 0,
					"TimeSlotDate" => count($array) > 7 && !empty($array[7]) ? DateTime::createFromFormat("Ymd", $array[7],new DateTimeZone('UTC')) : null,
					"CheckInDate" => count($array) > 8 && !empty($array[8]) ? DateTime::createFromFormat("Ymd", $array[8],new DateTimeZone('UTC')) : null,
					"CheckOutDate" => count($array) > 9 && !empty($array[9]) ? DateTime::createFromFormat("Ymd", $array[9],new DateTimeZone('UTC')) : null,
					"Configuration" => $str
				);
				return $newarray;
			}
		
		public static function GetPriceParameters($selectablePrices)
			{
				$priceParameters = array();
				if (empty($selectablePrices)) {
					return $priceParameters;
				}
				$priceParametersArray = explode('|', $selectablePrices);
				if(!empty($priceParametersArray)){
					foreach ($priceParametersArray as $s)
					{
						array_push($priceParameters, BFCHelper::ParsePriceParameter($s));
					}
				}
				return $priceParameters;
			}
			
			public static function calculateOrder($OrderJson,$language,$bookingType = "") {
				$orderModel = json_decode($OrderJson);
				$order = new StdClass;
				$DateTimeMinValue = new DateTime('UTC');
				$DateTimeMinValue->setDate(1, 1, 1);

				$orderModel->SearchModel->FromDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkin,new DateTimeZone('UTC'));
				$orderModel->SearchModel->ToDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkout,new DateTimeZone('UTC'));
				$orderModel->SearchModel->FromDate->setTime(0,0,0);
				$orderModel->SearchModel->ToDate->setTime(0,0,0);


				if ($orderModel->Resources != null && count($orderModel->Resources) > 0 && $orderModel->SearchModel->FromDate != $DateTimeMinValue)
				{
					$order->Resources = array();
					$resourceDetail = null;
					
					foreach ($orderModel->Resources as $resource)
					{
						$resourceDetail = BFCHelper::GetResourcesById($resource->ResourceId);
						$order->MerchantId = $resourceDetail->MerchantId;
						$services = "";
						
						$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
						if(!empty($servicesArray)){
							$services = implode("|",$servicesArray);
						}
						$currservices = BFCHelper::GetPriceParameters($services);
						$selectablePrices = array_filter($currservices, function($t) {return $t["Quantity"] > 0;});
										
						$currModel = clone $orderModel;
						$currModel->SearchModel->MerchantId = $resourceDetail->MerchantId;
						$currModel->SearchModel->ProductAvailabilityType = $resourceDetail->AvailabilityType;
						$duration = 1;

						if ($resourceDetail->AvailabilityType== 2)
						{
							$duration = $resource->TimeDuration;
							$currModel->SearchModel->FromDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
												  
							$currModel->SearchModel->ToDate->modify('+1 day');
						}
						if ($resourceDetail->AvailabilityType== 3)
						{
							$currModel->SearchModel->ToDate = clone $currModel->SearchModel->FromDate;
							$currModel->SearchModel->ToDate->modify('+1 day');
						}
						if($resourceDetail->AvailabilityType != 3 && $resourceDetail->AvailabilityType != 2){
							$duration = $currModel->SearchModel->ToDate->diff($currModel->SearchModel->FromDate)->format('%a');
						}

						if ($resourceDetail->AvailabilityType == 0)
						{
							$duration +=1; 
						}

						$paxages = array();
						for ($i=0;$i<$currModel->SearchModel->AdultCount ; $i++)	
						{
							array_push($paxages, COM_BOOKINGFORCONNECTOR_ADULTSAGE);
						}
						for ($i=0;$i<$currModel->SearchModel->SeniorCount ; $i++)	
						{
							array_push($paxages, COM_BOOKINGFORCONNECTOR_SENIORESAGE);
						}
						$nchsarray = array($currModel->SearchModel->childages1,$currModel->SearchModel->childages2,$currModel->SearchModel->childages3,$currModel->SearchModel->childages4,$currModel->SearchModel->childages5,null);
						for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
						{
							array_push($paxages, $nchsarray[$i]);
						}
	//					$paxages = implode("|",$paxages);
											
						$packages =null;
						$pricetype = !empty($resource->RatePlanId)?$resource->RatePlanId:"";
						$ratePlanId = $pricetype;
						$variationPlanId = "";

						$listRatePlans = BFCHelper::GetCompleteRatePlansStayWP($resource->ResourceId,$currModel->SearchModel->FromDate,$duration,$paxages,$services,$packages,$pricetype,$ratePlanId,$variationPlanId,$language, $bookingType, true);
						if (!empty($listRatePlans) && is_array($listRatePlans)){
							$listRatePlans = array_filter($listRatePlans, function($l)  {return ($l->TotalAmount>0 && !empty($l->SuggestedStay)  && $l->SuggestedStay->Available ) ;});
							
							if (!empty($resource->RatePlanId))
							{
								$listRatePlans =  array_filter($listRatePlans, function($l) use ($resource) {return $l->RatePlanId == $resource->RatePlanId ;}); // c#: allRatePlans.Where(p => p.ResourceId == resId);
							}
							else
							{							
								$listRatePlansGrouped = array();
								$tmpLlistRatePlansGrouped = array();
								foreach ($listRatePlans as $data) {
									$id = $data->SuggestedStay->BookingType;
									if (isset($listRatePlansGrouped[$id])) {
										$listRatePlansGrouped[$id][] = $data;
									} else {
										$listRatePlansGrouped[$id] = array($data);
									}
								}
								foreach ($listRatePlansGrouped as $ratePlansGrouped) {
									usort($ratePlansGrouped, "BFCHelper::bfi_sortRatePlans");
									$tmpLlistRatePlansGrouped[] = reset($ratePlansGrouped);
								}

								$listRatePlans = $tmpLlistRatePlansGrouped;


								$selRatePlan = reset($listRatePlans);

							}
							foreach ($listRatePlans as $selRatePlan)
							{

								if (!empty($selRatePlan))
								{
									
									//$order->BookingType = $selRatePlan->SuggestedStay->BookingType;
									for ($i = 0; $i < $resource->SelectedQt; $i++)
									{
										$lstExtraServices = array();
										$lstPriceSimpleResult = json_decode($selRatePlan->CalculatedPricesString); 
										$lstPriceSimpleResult = array_filter($lstPriceSimpleResult, function($c) use ($resource) {return $c->RelatedProductId != $resource->ResourceId ;});
										$lstPriceSimpleResultGrouped = array();
										foreach ($lstPriceSimpleResult as $data) {
										  $id = $data->RelatedProductId;
										  if (isset($lstPriceSimpleResultGrouped[$id])) {
											 $lstPriceSimpleResultGrouped[$id][] = $data;
										  } else {
											 $lstPriceSimpleResultGrouped[$id] = array($data);
										  }
										}
										foreach ($lstPriceSimpleResultGrouped as $pricesKey => $prices ){
											$resInfo = reset($prices);

											$resInfoRequest = current(array_filter($selectablePrices, function($c) use ($pricesKey) {return $c["ProductId"] == $pricesKey;}));

											$CalculatedQt = 0;
											$TotalAmount = 0;
											$TotalDiscounted = 0;
											foreach ($prices as $item) {
												$CalculatedQt += $item->CalculatedQt;
												$TotalAmount += $item->TotalAmount;
												$TotalDiscounted += $item->TotalDiscounted;
											}
											$currSelectedService = new StdClass;                        
											$currSelectedService->PriceId = $pricesKey;
											$currSelectedService->CalculatedQt = $CalculatedQt;
											$currSelectedService->ResourceId = $pricesKey;
											$currSelectedService->Name = $resInfo->Name;
											$currSelectedService->TotalAmount = $TotalAmount;
											$currSelectedService->TotalDiscounted = $TotalDiscounted;
											$currSelectedService->TimeSlotDate = !empty($resInfoRequest["TimeSlotDate"]) ? $resInfoRequest["TimeSlotDate"]->format('d/m/Y') : ""; //$resInfoRequest["TimeSlotDate"];
											$currSelectedService->TimeSlotStart = $resInfoRequest["TimeSlotStart"];
											$currSelectedService->TimeSlotEnd = $resInfoRequest["TimeSlotEnd"];
											$currSelectedService->TimeSlotId = $resInfoRequest["TimeSlotId"];
											$currSelectedService->CheckInTime = !empty($resInfoRequest["CheckInDateTime"]) ? $resInfoRequest["CheckInDateTime"]->format('YmdHis') : "";
											$currSelectedService->TimeDuration = !empty($resInfoRequest["PeriodDuration"]) ? $resInfoRequest["PeriodDuration"] : "";

											array_push($lstExtraServices, $currSelectedService);
										}
										
										$calPricesResources = json_decode($selRatePlan->CalculatedPricesString);
										$calPricesResources = array_filter($calPricesResources, function($c) use ($resource) {return $c->RelatedProductId == $resource->ResourceId ;});

	//									$calPricesResources = array_filter($calPricesResources, function($c) {
	//										return $c->Tag == "person" || $c->Tag == "default" || $c->Tag == "" || $c->Tag== "timeslot" || $c->Tag == "timeperiod" ;
	//									});
																																	
										$calPricesResourcesTotalAmount = 0;
										$calPricesResourcesTotalDiscounted = 0;
										foreach ($calPricesResources as $item) {
											$calPricesResourcesTotalAmount += $item->TotalAmount;
											$calPricesResourcesTotalDiscounted += $item->TotalDiscounted;
										}
										$AllVariations = "";

										if(!empty($selRatePlan->AllVariationsString)){
											$allVariationPlanId = array_unique(array_map(function ($i) { return $i->VariationPlanId; }, json_decode($selRatePlan->AllVariationsString)));
											$AllVariations = implode(",",$allVariationPlanId);

										}


										$SelectedResource = new StdClass;
										$SelectedResource->ResourceId = $resource->ResourceId;
										$SelectedResource->MerchantId = $resource->MerchantId;
										$SelectedResource->RatePlanId = $resource->RatePlanId;
										$SelectedResource->SelectedQt = $resource->SelectedQt;
										$SelectedResource->TimeSlotId = isset($resource->TimeSlotId)?$resource->TimeSlotId:null;
										$SelectedResource->TimeSlotStart = isset($resource->TimeSlotStart)?$resource->TimeSlotStart:null;
										$SelectedResource->TimeSlotEnd = isset($resource->TimeSlotEnd)?$resource->TimeSlotEnd:null;
										$SelectedResource->CheckInTime = (isset($selRatePlan->SuggestedStay->CheckIn) )?BFCHelper::parseJsonDate($selRatePlan->SuggestedStay->CheckIn,'YmdHis'):$currModel->SearchModel->FromDate->format('YmdHis');
										$SelectedResource->TimeDuration = isset($resource->TimeDuration)?$resource->TimeDuration:null;
										$SelectedResource->Name = $resourceDetail->Name;
										$SelectedResource->BookingType = $selRatePlan->SuggestedStay->BookingType;
										$SelectedResource->AvailabilityType = $resourceDetail->AvailabilityType;
										$SelectedResource->TotalAmount = $calPricesResourcesTotalAmount;
										$SelectedResource->TotalDiscounted = $calPricesResourcesTotalDiscounted;
										$SelectedResource->ExtraServices = $lstExtraServices;
										$SelectedResource->ExtraServicesValue = $services;
										$SelectedResource->RatePlanName = $selRatePlan->Name;
										$SelectedResource->PercentVariation = $selRatePlan->PercentVariation;
										$SelectedResource->AllVariations = $AllVariations;
										$SelectedResource->PolicyId = 0;
										if(isset($selRatePlan->Policy) && !empty($selRatePlan->Policy->PolicyId) ){
											$SelectedResource->PolicyId = $selRatePlan->Policy->PolicyId;
										}
										array_push($order->Resources, $SelectedResource);

									}

								}
							}
						}
						$order->TotalAmount = 0;
						$order->TotalDiscountedAmount = 0;
						foreach ($order->Resources as $resource)
						{
							$order->TotalAmount += $resource->TotalAmount;
							$order->TotalDiscountedAmount += $resource->TotalDiscounted ;
							foreach ($resource->ExtraServices as $item) {
								$order->TotalAmount  += $item->TotalAmount;
								$order->TotalDiscountedAmount  += $item->TotalDiscounted;
							}

						}
						$order->SearchModel = $orderModel->SearchModel;

				}
				}

				return $order;
		}
			public static function CreateOrder($OrderJson,$language,$bookingType = "") {
	//			$totalModel = json_decode(stripslashes($OrderJson));


				$orderModel = json_decode(stripslashes($OrderJson));
				$lstOrderStay = array();
				$DateTimeMinValue = new DateTime('UTC');
				$DateTimeMinValue->setDate(1, 1, 1);

	//            foreach ($totalModel as $orderModel)
	//            {
					
	//			if(isset($orderModel->SearchModel->checkin)){
	//				$orderModel->SearchModel->FromDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkin);
	//			}else{
	//				$orderModel->SearchModel->FromDate = new DateTime($orderModel->SearchModel->FromDate );
	//			}
	//			if(isset($orderModel->SearchModel->checkout)){
	//				$orderModel->SearchModel->ToDate = DateTime::createFromFormat('d/m/Y', $orderModel->SearchModel->checkout);
	//			}else{
	//				$orderModel->SearchModel->ToDate = new DateTime($orderModel->SearchModel->ToDate );
	//			}
	//
	//			$orderModel->SearchModel->FromDate->setTime(0,0,0);
	//			$orderModel->SearchModel->ToDate->setTime(0,0,0);

	//				echo "<pre>";
	//				echo print_r($orderModel->SearchModel);
	//				echo "</pre>";
	//				die();

	//            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 && $orderModel->SearchModel->FromDate != $DateTimeMinValue)
				if ($orderModel->Resources != null && count($orderModel->Resources) > 0 )
				{
	//                $resourceDetail = null;
					foreach ($orderModel->Resources as $resource)
					{
	//                    $resourceDetail = BFCHelper::GetResourcesById($resource->ResourceId);
	//                    $order->MerchantId = $resourceDetail->MerchantId;
						
						$fromCart= !empty($resource->CartOrderId)?1:0;
						$services="";
						if(isset($resource->ExtraServicesValue)){
							$services = $resource->ExtraServicesValue;
						}
						if(isset($resource->ExtraServices)){
							$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
							if(!empty($servicesArray)){
								$services = implode("|",$servicesArray);
							}
						}

	//					$services = isset($resource->ExtraServicesValue)?$resource->ExtraServicesValue:(isset($resource->ExtraServices)?json_encode($resource->ExtraServices):"");
						
	//					$servicesArray = array_map(function ($i) { return $i->Value; },array_filter($resource->ExtraServices, function($t) use ($resource) {return $t->ResourceId == $resource->ResourceId;}));
	//					if(!empty($servicesArray)){
	//						$services = implode("|",$servicesArray);
	//					}
	//					$currservices = BFCHelper::GetPriceParameters($services);
	//                    $selectablePrices = array_filter($currservices, function($t) {return $t["Quantity"] > 0;});
										
	//					$currModel = clone $orderModel;
						$currModel = new stdClass;
						$currModel->SearchModel = new stdClass;
						if($fromCart==0){
							$currModel->SearchModel->FromDate  = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->FromDate,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate  = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->ToDate,new DateTimeZone('UTC'));
						}else{
							$currModel->SearchModel->FromDate = new DateTime($resource->FromDate,new DateTimeZone('UTC') );
							$currModel->SearchModel->ToDate = new DateTime($resource->ToDate,new DateTimeZone('UTC') );
						}
						$currModel->SearchModel->FromDate->setTime(0,0,0);
						$currModel->SearchModel->ToDate->setTime(0,0,0);
						$currModel->SearchModel->MerchantId = $resource->MerchantId;
						$currModel->SearchModel->ProductAvailabilityType = $resource->AvailabilityType;
						$duration = 1;

						if ($resource->AvailabilityType== 2)
						{
							$duration = $resource->TimeDuration;
							$currModel->SearchModel->FromDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate = DateTime::createFromFormat("YmdHis", $resource->CheckInTime,new DateTimeZone('UTC'));
							$currModel->SearchModel->ToDate->modify('+1 day');
						}
						if ($resource->AvailabilityType== 3)
						{
							$currModel->SearchModel->ToDate = clone $currModel->SearchModel->FromDate;
							$currModel->SearchModel->ToDate->modify('+1 day');
						}
						if($resource->AvailabilityType != 3 && $resource->AvailabilityType != 2){
							$duration = $currModel->SearchModel->ToDate->diff($currModel->SearchModel->FromDate)->format('%a');
						}
						$paxages = $resource->PaxAges;
						if ($duration ==0 && $resource->AvailabilityType ==0) {
						    $duration = 1;
						}

	//					$paxages = array();
	//					for ($i=0;$i<$currModel->SearchModel->AdultCount ; $i++)	
	//					{
	//						array_push($paxages, COM_BOOKINGFORCONNECTOR_ADULTSAGE);
	//					}
	//					for ($i=0;$i<$currModel->SearchModel->SeniorCount ; $i++)	
	//					{
	//						array_push($paxages, COM_BOOKINGFORCONNECTOR_SENIORESAGE);
	//					}
	////					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
	//////					$paxages = implode("|",$paxages);
	//					$nchsarray = array($currModel->SearchModel->childages1,$currModel->SearchModel->childages2,$currModel->SearchModel->childages3,$currModel->SearchModel->childages4,$currModel->SearchModel->childages5,null);
	//					for ($i=0;$i<$currModel->SearchModel->ChildrenCount ; $i++)	
	//					{
	//						array_push($paxages, $nchsarray[$i]);
	//					}  										
											
						$packages =null;
						$pricetype = !empty($resource->RatePlanId)?$resource->RatePlanId:"";
						$ratePlanId = $pricetype;
						$variationPlanId = "";
						

						$stay = BFCHelper::GetCompleteRatePlansStayWP($resource->ResourceId,$currModel->SearchModel->FromDate,$duration,$paxages,$services,$packages,$pricetype,$ratePlanId,$variationPlanId,$language, $bookingType , false);
						if (!empty($stay) && is_array($stay)){
							$stay = reset($stay);
						}
						if (!empty($stay) && !empty($stay->SuggestedStay))
						{
							for ($i = 0; $i < $resource->SelectedQt; $i++)
							{						
								$order = new StdClass;

								$order->Availability = $stay->SuggestedStay->Availability;
								$order->Available = $stay->SuggestedStay->Available;
								$order->MerchantBookingTypeId = intVal($bookingType);
								$order->CheckIn = $stay->SuggestedStay->CheckIn;
								$order->CheckOut = $stay->SuggestedStay->CheckOut;
								$order->Days = $stay->SuggestedStay->Days;
								$order->DiscountDescription = $stay->SuggestedStay->DiscountDescription;
								$order->DiscountId = $stay->SuggestedStay->DiscountId;
								$order->MerchantId = $resource->MerchantId;
								$order->Extras = $stay->SuggestedStay->Extras;
								$order->ExtrasDiscount = $stay->SuggestedStay->ExtrasDiscount;
								$order->HolidayDiscount = $stay->SuggestedStay->HolidayDiscount;
								$order->HolidayPrice = $stay->SuggestedStay->HolidayPrice;
								$order->IsOffer = $stay->SuggestedStay->IsOffer;
								$order->Paxes = $stay->SuggestedStay->Paxes;
								$order->PaxesDiscount = $stay->SuggestedStay->PaxesDiscount;
								$order->PaxesPrice = $stay->SuggestedStay->PaxesPrice;
								$order->TotalDiscount = $stay->SuggestedStay->TotalDiscount;
								$order->TotalPrice = $stay->SuggestedStay->TotalPrice;
								$order->UnitId = $stay->SuggestedStay->UnitId;
								$order->DiscountedPrice = $stay->SuggestedStay->DiscountedPrice;
								$order->RatePlanStay = $stay;
								$order->CalculatedPricesDetails = json_decode($stay->CalculatedPricesString);
								//$order->SelectablePrices = json_decode($stay->CalculablePricesString);
								//$order->CalculatedPackages = json_decode($stay->PackagesString);
								//$order->MerchantBookingTypesString = json_decode($stay->MerchantBookingTypesString);
								$order->Variations = json_decode($stay->AllVariationsString);
								$order->DiscountVariation = !empty($stay->Discount) ? $stay->Discount : null;
								$order->SupplementVariation = !empty($stay->Supplement) ? $stay->Supplement : null;
								$order->TimeSlotId =  isset($resource->TimeSlotId)?$resource->TimeSlotId:"";
								$order->TimeSlotStart = isset($resource->TimeSlotStart)?$resource->TimeSlotStart:"";
								$order->TimeSlotEnd = isset($resource->TimeSlotEnd)?$resource->TimeSlotEnd:"";
								$order->CheckInTime = isset($resource->CheckInTime)?$resource->CheckInTime:"";
								$order->TimeDuration = isset($resource->TimeDuration)?$resource->TimeDuration:"";
								$order->ServiceConfiguration = $services;
								$order->PolicyId = 0;							

								if(isset($stay->Policy) && !empty($stay->Policy->PolicyId)) {
									$order->PolicyId = $stay->Policy->PolicyId;
								}
								if(isset($stay->Policy) && !empty($stay->Policy->PolicyId)) {
									$order->PolicyId = $stay->Policy->PolicyId;
								}
								
								unset($order->RatePlanStay->CalculatedPricesString);
								unset($order->RatePlanStay->CalculablePricesString);
								unset($order->RatePlanStay->PackagesString);
								unset($order->RatePlanStay->MerchantBookingTypesString);
								unset($order->RatePlanStay->Policy);
								unset($order->RatePlanStay->SuggestedStay);
								unset($order->RatePlanStay->AllVariationsString);
								
								foreach($order->CalculatedPricesDetails as $pr) {
									unset($pr->OriginalDays);
									unset($pr->Days);
									unset($pr->Variations);
								}
	//							if($fromCart==0){
	//								$order->CheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->FromDate);
	//								$order->CheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->ToDate);
	//							}
															
								array_push($lstOrderStay, $order);
							}						
						}
					}
							
				}
	//		}
							
				return $lstOrderStay;
		}

		public static function AddToCart($tmpUserId, $language, $OrderJson, $ResetCart) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//JML>		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->AddToCart($tmpUserId, $language, $OrderJson, $ResetCart);
		}	

		public static function AddToCartByExternalUser($tmpUserId, $language, $OrderJson, $ResetCart) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//JML>		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->AddToCartByExternalUser($tmpUserId, $language, $OrderJson, $ResetCart);
		}	
		public static function DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//JML>		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId);
		}	
		public static function AddDiscountCodesCartByExternalUser($tmpUserId, $language, $bficoupons) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//JML>		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->AddDiscountCodesCartByExternalUser($tmpUserId, $language, $bficoupons);
		}	
		public static function GetCartByExternalUser($tmpUserId, $language, $includeDetails = true) {
//			JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
	//JML>		$model = JModelLegacy::getInstance('Orders', 'BookingForConnectorModel');
			$model = new BookingForConnectorModelOrders;
			return $model->GetCartByExternalUser($tmpUserId, $language, $includeDetails);
		}	
		public static function  bfi_get_userId() {
			$tmpUserId = BFCHelper::getSession('tmpUserId', null , 'com_bookingforconnector');
			if(empty($tmpUserId)){
				$uid = get_current_user_id();
				$user = get_user_by('id', $uid);
				if (!empty($user->ID)) {
					$tmpUserId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
				}
				if(empty($tmpUserId)){
					$tmpUserId = uniqid($_SERVER["SERVER_NAME"]);
				}
				BFCHelper::setSession('tmpUserId', $tmpUserId , 'com_bookingforconnector');
			}

			return $tmpUserId;
		}

		public static function bfi_sortOrder($a, $b)
		{
			return $a->SortOrder - $b->SortOrder;
		}
		public static function bfi_sortResourcesRatePlans($a, $b)
		{
			return $a->RatePlan->TotalDiscounted - $b->RatePlan->TotalDiscounted;
	//		return $a->RatePlan->SortOrder - $b->RatePlan->SortOrder;
		}
		public static function bfi_returnFilterCount($a, $b, $offset)
		{
			$currA = intval($a);
			$currB = $currA;
			if(isset($b[$offset])){
				$currB = intval($b[$offset]);
			}
	//		if($currA>$currB){
	//			return  "+" . ($currA - $currB);
	//		}

			return $currB;
		}

		public static function string_sanitize($s) {
			$result = preg_replace("/[^a-zA-Z0-9\s]+/", "", html_entity_decode($s, ENT_QUOTES));
			return $result;
		}

		public static function bfi_get_clientdata() {
			$ipClient = BFCHelper::bfi_get_client_ip();
			$ipServer = $_SERVER['SERVER_ADDR'];
			$uaClient = $_SERVER['HTTP_USER_AGENT'];
			$RequestTime = $_SERVER['REQUEST_TIME'];
			$Referer = $_SERVER['HTTP_REFERER'];
			$clientdata =
				"ipClient:" . str_replace( ":", "_", $ipClient) ."|".
				"ipServer:" . str_replace( ":", "_", $ipServer) ."|".
				"uaClient:" . str_replace( "|", "_", str_replace( ":", "_", $uaClient)) ."|".
				"Referer:" . str_replace( "|", "_", str_replace( ":", "_", $Referer)) ."|".
				"RequestTime:" . $RequestTime;
			return $clientdata;
		}
		public static function bfi_get_client_ip() {
			$ipaddress = '';
			if (isset($_SERVER['HTTP_CLIENT_IP']))
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_X_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			else if(isset($_SERVER['REMOTE_ADDR']))
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			else
				$ipaddress = 'UNKNOWN';
		 
			return $ipaddress;
		}
	}
}