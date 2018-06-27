<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelResource' ) ) :

class BookingForConnectorModelResource
{
	private $urlResource = null;
	private $urlUnit = null;
	private $urlUnits = null;
	private $urlUnitServices = null;
	private $urlPrice = null;
	private $urlStay = null;
	private $urlStayRatePlan = null;
	private $urlExtra = null;
	private $urlExtraRatePlan = null;
	private $urlPriceTypes = null;
	private $urlBookingTypes = null;
	private $urlBookingTypesRatePlan = null;
	private $helper = null;
	private $urlPackages = null;
	private $urlPackageStay = null;
	private $urlSupplement = null;
	private $urlDiscount = null;
	private $urlVariations = null;
	private $urlRatePlan = null;
	private $urlStartDate = null;
	private $urlStartDateByMerchantId = null;
	private $urlEndDate = null;
	private $urlEndDateByMerchantId = null;
	private $urlCheckInDates = null;
	private $urlCheckOutDates = null;
	private $urlPriceList = null;
	private $urlCheckAvailabilityCalendar = null;
	private $urlUnitCategories = null;
	private $urlRating = null;
	private $urlRatingCount = null;
	private $urlRatingAverage = null;
	private $urlDiscountVariationDetails = null;
	private $urlDiscountDetails = null;
	private $urlListDiscounts = null;
	private $urlListVariations = null;
	private $urlGetRatePlansByResourceId = null;
	private $urlGetPrivacy = null;
	private $urlSearchAllCalculate = null;
	private $resourceid = null;
	private $itemPerPage = 10;
	private $urlGetCheckInDatesPerTimes = null;
	private $urlGetCheckInDatesTimeSlot = null;
	private $urlGetListCheckInDayPerTimes = null;
	private $urlGetMostRestrictivePolicyByIds = null;
	private $urlGetPolicyById = null;
	private $urlGetPolicyByIds = null;

	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlResource = '/GetResourceById';// '/Resources(%d)';
		$this->urlUnitServices = '/Resources(%d)/Unit/Services';
		$this->urlUnit = '/Resources(%d)';
		$this->urlUnits = '/Resources';
		$this->urlStay = '/GetStay';
		$this->urlStayRatePlan = '/GetRatePlansStay';
		$this->urlCompleteStayRatePlan = '/GetCompleteRatePlansStay';
		$this->urlExtra = '/Resources(%d)/Typology/Extras';
		//$this->urlExtraRatePlan = '/RatePlans(%d)/Prices';
		$this->urlRatePlan = '/RatePlans(%d)';
		$this->urlExtraRatePlan = '/GetRatePlanCalculablePrices';
		$this->urlPackages = '/GetResourcePackages';
		$this->urlPackageStay = '/GetPackageStay';
		$this->urlPriceTypes = '/Resources(%d)/Merchant/PriceTypes';
		$this->urlPriceTypesRatePlan = '/Resources(%d)/Merchant/PriceTypes';
		//$this->urlBookingTypes = '/Resources(%d)/Merchant/MerchantBookingTypes';
		$this->urlBookingTypes = '/GetMerchantBookingTypes';
		$this->urlBookingTypesRatePlan = '/GetRatePlanStayMerchantBookingTypes';
		$this->urlPrice = '/GetRatePlanStayCalculatedPrices';
		$this->urlSupplement = '/GetRatePlanStaySupplement';
		$this->urlDiscount = '/GetRatePlanStayDiscount';
		$this->urlVariations = '/GetRatePlanStayVariations';
		$this->urlStartDate = '/GetStartDate';
		$this->urlStartDateByMerchantId = '/GetStartDateByMerchantId';
		$this->urlEndDate = '/GetEndDate';
		$this->urlEndDateByMerchantId = '/GetEndDateByMerchantId';
		$this->urlCheckInDates = '/GetCheckInDates';
		$this->urlCheckOutDates = '/GetCheckOutDateAvailabilitiesFromFirstUsefulDate';
		$this->urlPriceList = '/GetResourcePricelist';
		$this->urlCheckAvailabilityCalendar = '/CheckAvailabilityCalendar';
		$this->urlUnitCategories ='/ProductCategories'; // '/UnitCategories';
		$this->urlRating = '/GetReviews';
		$this->urlRatingCount = '/GetReviewsCount';
		$this->urlRatingAverage = '/GetResourceAverage';
		$this->urlDiscountVariationDetails = '/GetDiscountsByIds';
		$this->urlDiscountDetails = '/Discounts(%d)';
		$this->urlListDiscounts = '/Discounts';
		$this->urlListVariations = '/VariationPlans';
		$this->urlGetRatePlansByResourceId = '/GetRatePlansByResourceId';
		$this->urlGetPolicy = '/GetPolicy';
		$this->urlSearchAllCalculate = '/SearchAllNew';
		$this->urlGetCheckInDatesPerTimes = '/GetCheckInDatesPerTimes';
		$this->urlGetCheckInDatesTimeSlot = '/GetCheckInDatesTimeSlot';
		$this->urlGetRelatedResourceStays = '/GetRelatedResourceStaysAll';
		$this->urlGetListCheckInDayPerTimes = '/GetListCheckInDayPerTimes';
		$this->urlGetMostRestrictivePolicyByIds = '/GetMostRestrictivePolicyByIds';
		$this->urlGetPolicyById = '/GetPolicyById';
		$this->urlGetPolicyByIds = '/GetPolicyByIds';

	}

	public function setResourceId($resourceid) {
		if(!empty($resourceid)){
			$this->resourceid = $resourceid;
		}
	}
	public function setItemPerPage($itemPerPage) {
		if(!empty($itemPerPage)){
			$this->itemPerPage = $itemPerPage;
		}
	}

	public function applyDefaultFilter(&$options, $overrideFilters = array()) {
		$params = $_SESSION['search.params'];
		$checkoutspan = '+1 day';

		if (empty($params['checkin'])){
			$params['checkin'] = new DateTime('UTC');
		}
		if (empty($params['checkout'])){
			$params['checkout'] = new DateTime('UTC');
			$params['checkout']->modify($checkoutspan); 
		}
		if (empty($params['duration'])){
			$params['duration']= $params['checkin']->diff($params['checkout'])->days;
		}
		if (empty($params['paxes'])){
			$params['paxes'] = 2;
		}
		if (empty($params['paxages'])){
			$params['paxages'] = array(BFCHelper::$defaultAdultsAge,BFCHelper::$defaultAdultsAge);
		}
		$checkin = isset($overrideFilters) && isset($overrideFilters['checkin']) && !empty($overrideFilters['checkin']) ? $overrideFilters['checkin'] : $params['checkin'];
		$checkout = isset($overrideFilters) && isset($overrideFilters['checkout']) && !empty($overrideFilters['checkout']) ? $overrideFilters['checkout'] : $params['checkout'];
		$duration = isset($overrideFilters) && isset($overrideFilters['duration']) && !empty($overrideFilters['duration']) ? $overrideFilters['duration'] : $params['duration'];
		$persons = isset($overrideFilters) && isset($overrideFilters['paxes']) && !empty($overrideFilters['paxes']) ? $overrideFilters['paxes'] : $params['paxes'];
		$paxages = isset($overrideFilters) && isset($overrideFilters['paxages']) && !empty($overrideFilters['paxages']) ? $overrideFilters['paxages'] : $params['paxages'];
		$availabilityTypes = isset($overrideFilters) && isset($overrideFilters['availabilityTypes']) && !empty($overrideFilters['availabilityTypes']) ? $overrideFilters['availabilityTypes'] : isset($params['availabilityTypes'])?$params['availabilityTypes']:null;
		$filter = '';
		
		if (!empty($refid) or !empty($resourceName))  {
			$options['data']['calculate'] = 0;
			
			if (isset($refid) && $refid <> "" ) {
				$options['data']['refId'] = '\''.$refid.'\'';
			}
			if (isset($resourceName) && $resourceName <> "" ) {
				$options['data']['resourceName'] = '\''. $resourceName.'\'';
			}
		}else{
		
			$onlystay = true;

				
			$options['data']['calculate'] = $onlystay;

//			if (isset($params['availabilitytype']) ) {
//				$availabilityTypes = $params['availabilitytype'];
//				$options['data']['availabilityTypes'] = '\''. $availabilityTypes.'\'';
//			}

			if (isset($availabilityTypes) ) {
				$options['data']['availabilityTypes'] = '\''. $availabilityTypes.'\'';
			}
			
			

			if (isset($params['locationzone']) ) {
				$locationzone = $params['locationzone'];
			}
			if (isset($masterTypeId) && $masterTypeId > 0) {
				$options['data']['masterTypeId'] = $masterTypeId;
			}

			if (isset($merchantCategoryId) && $merchantCategoryId > 0) {
				$options['data']['merchantCategoryId'] = $merchantCategoryId;
			}
			
			if ((isset($checkin) && !empty($checkin) ) && (isset($duration) && $duration > 0)) {
				$options['data']['checkin'] = '\'' . $checkin->format('Ymd') . '\'';
				$options['data']['duration'] = $duration;
			}
			
			if (isset($persons) && $persons > 0) {
				$options['data']['paxes'] = $persons;
				if (isset($paxages)) {
					$options['data']['paxages'] = '\'' . implode('|',$paxages) . '\'';
				}else{
					$px = array_fill(0,$persons,BFCHelper::$defaultAdultsAge);
					$options['data']['paxages'] = '\'' . implode('|',$px) . '\'';
				}
			}
			
				$options['data']['pricetype'] = '\'' . 'rateplan' . '\'';

			if (isset($locationzone) && $locationzone > 0) {
				$options['data']['zoneId'] = $locationzone;
			}				
		}

		


		if (isset($cultureCode) && $cultureCode !='') {
			$options['data']['cultureCode'] = '\'' . $cultureCode. '\'';
		}
		
		if (isset($merchantId) && $merchantId > 0) {
			$options['data']['merchantid'] = $merchantId;
		}

		if ($filter!='')
			$options['data']['$filter'] = $filter;

	}
	
	public function getDiscountDetails($ids, $language='') {
		if ($ids == null) return null;
		if (empty($language)){
//			$language = JFactory::getLanguage()->getTag();
			$language = $GLOBALS['bfi_lang'];
		}		
		$urlDiscountDetails = $this->urlDiscountVariationDetails;
			$options = array(
					'path' => $urlDiscountDetails,
					'data' => array(
						'$format' => 'json',
						'ids' => BFCHelper::getQuotedString($ids),
						'cultureCode' => BFCHelper::getQuotedString($language)
					)
				);

		$url = $this->helper->getQuery($options);
		$discount = null;
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);						
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$discount = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$discount = json_encode($res->d);
			}
		}
		return $discount;
	}

	
//	public function getDiscountDetails($discountId, $hasRateplans) {
//
//		
//		if ($discountId == null) return null;
//		$urlDiscountDetails = $this->urlDiscountDetails;
//		if ($hasRateplans) {
//			$urlDiscountDetails = $this->urlDiscountVariationDetails;
//		}
//			$options = array(
//					'path' => sprintf($urlDiscountDetails, $discountId),
//					'data' => array(
//						'$format' => 'json'
//					)
//				);
//
//		$url = $this->helper->getQuery($options);
//		$discount = null;
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
//
//						
//			//$resource = $res->d->results ?: $res->d;
//			if (!empty($res->d->results)){
//				$discount = json_encode($res->d->results);
//			}elseif(!empty($res->d)){
//				$discount = json_encode($res->d);
//			}
//		}
//
//		return $discount;
//	}
	

	public function GetCheckInDatesPerTimes($resourcesId, $checkin, $limitTotDays = 0) {
		if ($resourcesId == null) return null;
		$urlGetCheckInDatesPerTimes =  $this->urlGetCheckInDatesPerTimes; 
		$options = array(
				'path' => $urlGetCheckInDatesPerTimes,
				'data' => array(
					'$format' => 'json',
					'resourceId' => $resourcesId,
					'checkin' => '\'' . $checkin->format('Ymd') . '\''
				)
			);
		if(!empty($limitTotDays)){
			$options['data']['limitTotDays'] = $limitTotDays;
		}
		$simpleTimePeriod = null;
		$url = $this->helper->getQuery($options);
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);

						
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$simpleTimePeriod = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$simpleTimePeriod = json_encode($res->d);
			}
		}

		return $simpleTimePeriod;
	}

	public function GetListCheckInDayPerTimes($resourcesId, $checkin, $limitTotDays = 0) {
		if ($resourcesId == null) return null;
		$urlGetCheckInDatesPerTimes =  $this->urlGetListCheckInDayPerTimes; 
		$options = array(
				'path' => $urlGetCheckInDatesPerTimes,
				'data' => array(
					'$format' => 'json',
					'resourceId' => $resourcesId,
					'checkin' => '\'' . $checkin . '\''
				)
			);
		if(!empty($limitTotDays)){
			$options['data']['limitTotDays'] = $limitTotDays;
		}
		$simpleTimePeriod = null;
		$url = $this->helper->getQuery($options);
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);

						
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$simpleTimePeriod = ($res->d->results);
			}elseif(!empty($res->d)){
				$simpleTimePeriod = ($res->d);
			}
		}

		return $simpleTimePeriod;
	}

	public function GetCheckInDatesTimeSlot($resourcesId, $checkin) {
		if ($resourcesId == null) return null;
		$urlGetCheckInDatesPerTimes =  $this->urlGetCheckInDatesTimeSlot; 
		$options = array(
				'path' => $urlGetCheckInDatesPerTimes,
				'data' => array(
					'$format' => 'json',
					'resourceId' => $resourcesId,
					'checkin' => '\'' . $checkin->format('Ymd') . '\''
				)
			);
		$simpleTimeSlot = null;
		$url = $this->helper->getQuery($options);
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);

						
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$simpleTimeSlot = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$simpleTimeSlot = json_encode($res->d);
			}
		}

		return $simpleTimeSlot;
	}

	public function GetDiscountsByResourceId($resourcesId, $hasRateplans) {

		
		if ($resourcesId == null) return null;
		$urlDiscounts =  $this->urlListDiscounts; 
		$filter =  "Enabled eq true and substringof(',".$resourcesId.",', concat(concat(',',MerchantTypology),',') ) eq true and ((soloSulSito eq false) or (soloSulSito eq null) ) and EndDate ge datetime'".date("Y-m-d")."T00:00:00' " /* Tags eq \'extra\' and */;
			$options = array(
					'path' => $urlDiscounts,
					'data' => array(
						'$format' => 'json',
						'$filter' => $filter
					)
				);
		
		if ($hasRateplans) {
			//$urlDiscounts = $this->urlListVariations;
			$urlDiscounts = $this->urlGetRatePlansByResourceId;
			//$filter =  "Enabled eq true and VariationPlanType eq 'discount' and RatePlans/any(r: r/Units/any(u: u/UnitId  eq ".$resourcesId.")) and (ActivationEndDate ge datetime'".date("Y-m-d")."T00:00:00'  or ActivationEndDate eq null) " /* Tags eq \'extra\' and */;
			//$filter =  " RatePlans/any(r: r/Units/any(u: u/UnitId  eq ".$resourcesId.")) " /* Tags eq \'extra\' and */;
			$filter ='';
			$options = array(
					'path' => $urlDiscounts,
					'data' => array(
						'$format' => 'json',
						'resourceId' => $resourcesId,
						'$expand' => 'VariationPlans',
						'$filter' => $filter
					)
				);
		}
//		if ($hasRateplans) {
//			$options['data']['$expand'] = 'RatePlans/VariationPlans ';
//
//		}
//MerchantTypology
//		((data_fine >= GETDATE()) or ( data_fine is null) ) AND (Abilitato = 1) 
//			AND IDmerchant = @IDmerchant
//			AND (solosulsito is NULL or solosulsito = @solosulsito)
//			AND ','+tipologiemerchant+',' like '%,'+@tipologiaMerchant+',%'
// ResourcesId=3353,3356,3359,3371,3386,3439,3479,3488,3494,3500,3502,4135,4201,4212,7053,8341,8342

		$url = $this->helper->getQuery($options);
		$discounts = null;
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);

						
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$discounts = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$discounts = json_encode($res->d);
			}
		}

		return $discounts;
	}

	public function getResourceFromService($resource_id) {
		$resourceId = $resource_id;
		$resourceIdRef = $resource_id;
//		if (empty($language)){
//			$language = JFactory::getLanguage()->getTag();
			$language = $GLOBALS['bfi_lang'];
//		}
		$options = array(
				'path' => $this->urlResource, // sprintf($this->urlResource, $resourceId),
				'data' => array(
					'$format' => 'json',
					//'expand' => 'Merchant',
					'id' => $resourceId,
					'cultureCode' => BFCHelper::getQuotedString($language)
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$resource = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->GetResourceById)){
				$resource = $res->d->GetResourceById;
			}elseif(!empty($res->d)){
				$resource = $res->d;
			}
		}
//		if (!isset($resource)) {
//			$options = array(
//					'path' => sprintf($this->urlUnit, $resourceId),
//					'data' => array(
//						'$format' => 'json',
//						'$filter' => 'Enabled eq true' /* Tags eq \'extra\' and */,
//						'$expand' => 'Typology'
//					)
//				);
//			$url = $this->helper->getQuery($options);
//			$r = $this->helper->executeQuery($url);
//			$unit = null;

//			if (isset($r)) { // se non trovo la risorsa cerco l'unità figlia
//				$res = json_decode($r);
////				$unit = $res->d->results ?: $res->d;
//				if (!empty($res->d->results)){
//					$unit = $res->d->results;
//				}elseif(!empty($res->d)){
//					$unit = $res->d;
//				}
//				$resourceIdRef = $unit->Typology->RefUnit;
//				$options = array(
//						'path' => sprintf($this->urlResource, $resourceIdRef),
//						'data' => array(
//							'$format' => 'json',
//							'$expand' => 'Merchant'
//						)
//					);
//				$url = $this->helper->getQuery($options);
//				$r = $this->helper->executeQuery($url);
//				if (isset($r)) {
//					$res = json_decode($r);
////					$resource = $res->d->results ?: $res->d;
//					if (!empty($res->d->results)){
//						$resource = $res->d->results;
//					}elseif(!empty($res->d)){
//						$resource = $res->d;
//					}
//				}
//				if (isset($resource)) {
//					$resource->ReferenceName = $resource->Name;
//					$resource->Name = $unit->Name;
//					$resource->ImageUrl = $unit->ImageUrl;
//					$resource->ImageData = $unit->ImageData;
//					$resource->XAltGooglePos = $unit->XAltGooglePos;
//					$resource->YAltGooglePos = $unit->YAltGooglePos;
//					$resource->IsReference = false;
//					$resource->TypeRef = $resource->TypeRef;
//				}
//			}
//		}else{
//			$resource->ReferenceName = $resource->Name;
//			$resource->IsReference = true;
//		}
		if(!empty($resource)){
		$resource->Merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
		}
		return $resource;
	}

	public function getUnitRelatedResourceFromService() {
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		$resourceIdRef = $params['resourceId'];
		$options = array(
				'path' => sprintf($this->urlResource, $resourceId),
				'data' => array(
					'$format' => 'json',
					'$expand' => 'Merchant'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$resource = null;
		$units = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$resource = $res->d->results;
			}elseif(!empty($res->d)){
				$resource = $res->d;
			}
		}
		if (isset($resource)) {
			$options = array(
					'path' => $this->urlUnits,
					'data' => array(
						'$format' => 'json',
						'$filter' => 'Enabled eq true and TypologyId eq ' . $resource->TypologyId /* Tags eq \'extra\' and */
					)
				);
			$url = $this->helper->getQuery($options);
			$r = $this->helper->executeQuery($url);

			if (isset($r)) {
				$res = json_decode($r);
//				$units = $res->d->results ?: $res->d;
				if (!empty($res->d->results)){
					$units = $res->d->results;
				}elseif(!empty($res->d)){
					$units = $res->d;
				}
			}
		}

		return $units;
	}
	

	public function getResourceRatePlanFacilieties($resource, $ratePlanStay) {
		if ($ratePlanStay == null) return null;
		
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		$hasRateplans = 1;// $params['hasRateplans'];
		if ($hasRateplans) {
		//if (BFCHelper::isRatePlanStay()) {
			
			//$resource->Extras =  $this->getExtrasFromServiceRatePlan($ratePlanStay);
			//$resource->PriceTypes =  $this->getPriceTypesFromServiceRatePlan($ratePlanStay);
			//$resource->Packages = $this->getPackagesFromServiceRatePlan($ratePlanStay);
			//$resource->MerchantBookingTypes =  $this->getMerchantBookingTypesFromServiceRatePlan($resource, $ratePlanStay);
		}

		return $resource;
	}
	
	/*public function getMerchantBookingTypesFromServiceRatePlan($resource, $ratePlanStay) {
		if ($ratePlanStay == null) return null;
		if ($resource == null) return null;
		$params = $this->getState('params');
		$variationPlanId = $params['variationPlanId'];
			
		$options = array(
				'path' => $this->urlBookingTypesRatePlan,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					'paxages' => '\'' . implode('|',$px) . '\'',
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);

		$packages = null;
		
		$r = $this->helper->executeQuery($url);
		
		if (isset($r)) {
			$res = json_decode($r);
			$packages = $res->d->results ?: $res->d;
		}
		return $packages;
	}*/
	
	public function getPackagesFromServiceRatePlan($ratePlanStay) {
		if ($ratePlanStay == null) return null;
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		$ci = $params['checkin'];
		$du = $params['duration'];
		$px = $params['paxages'];
			
		$options = array(
				'path' => $this->urlPackages,
				'data' => array(
					'$expand' => 'RatePlan/Prices',
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					'paxages' => '\'' . implode('|',$px) . '\'',
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);

		$packages = null;
		
		$r = $this->helper->executeQuery($url);
		
		if (isset($r)) {
			$res = json_decode($r);
//			$packages = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$packages = $res->d->results;
			}elseif(!empty($res->d)){
				$packages = $res->d;
			}
		}
		return $packages;
	}
	
	public function getExtrasFromServiceRatePlan($ratePlanStay) {
		if ($ratePlanStay == null || empty($ratePlanStay->RatePlanStay) ) return null;
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		$ci = $params['checkin'];
		$du = $params['duration'];
		$px = $params['paxages'];
								
		$options = array(
				//'path' => sprintf($this->urlExtraRatePlan, $ratePlanStay->RatePlanStay->RatePlanId),
				'path' => $this->urlExtraRatePlan,
				
				'data' => array(
					'$filter' => 'IsSelectable eq true and Enabled eq true' /* Tags eq \'extra\' and */,
					'ratePlanId' => $ratePlanStay->RatePlanStay->RatePlanId,
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					'paxages' => '\'' . implode('|',$px) . '\'',
					'$format' => 'json'
				)
			);
		
			
		$url = $this->helper->getQuery($options);
		
		$extras = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$extraPrices =$res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$extraPrices = $res->d->results;
			}elseif(!empty($res->d)){
				$extraPrices = $res->d;
			}
			if(!empty($extraPrices)){
			foreach ($extraPrices as $extrap) {
				$extra = new stdClass;
				$extra->MinNumber = $extrap->MinQt;
				$extra->MaxNumber = $extrap->MaxQt;
				$extra->ExtraId = $extrap->PriceId;
				$extra->Name = $extrap->Name;
				$extra->DefaultQt = $extrap->DefaultQt;
					//$extra->Tags = $extrap->Tags;
				$extras[] = $extra; 
			}
		}
		}

		return $extras;
	}
	
	public function getPriceTypesFromServiceRatePlan($ratePlanStay) {
		$types = null;
		foreach ($ratePlanStay as $ratePlan) {
			$type = new stdClass;
			$type->Type = $ratePlan->RatePlanId;
			$type->Name = $ratePlan->Name;
			$type->Description = $ratePlan->Description;
			$type->Tags = $ratePlan->Tags;

			$types[] = $type;
		}
		return $types;
	}
	
	public function getMerchantBookingTypesFromService() {
		$params = $_SESSION['search.params'];
//		$resourceId = $params['resourceId'];
		$resourceId =  isset($params['resourceId'])? $params['resourceId'] : 0;
		if(empty($resourceId)){
			$resourceId = $this->resourceid;
		}
		$checkoutspan = '+1 day';
		if (empty($params['checkin'])){
			$params['checkin'] = new DateTime('UTC');
		}
		if (empty($params['checkout'])){
			$params['checkout'] = new DateTime('UTC');
			$params['checkout']->modify($checkoutspan); 
		}
		$checkIn = $params['checkin'];
		$checkOut = $params['checkout'];
//		$cultureCode =  'en-gb';
		$cultureCode = $GLOBALS['bfi_lang'];

		$options = array(
			'path' => $this->urlBookingTypes,
			'data' => array(
					'resourceId' => $resourceId,
					'$format' => 'json'
				)
			);
		if(!empty($cultureCode)){
			$options['data']['cultureCode'] = '\'' . $cultureCode . '\'';
		}
		if (!empty($checkIn) && !empty($checkOut)) {
			$options['data']['checkin'] = '\'' . $checkIn->format('Ymd') . '\'';
			$options['data']['checkout'] = '\'' . $checkOut->format('Ymd') . '\'';
		}
		
		$url = $this->helper->getQuery($options);
		$types = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$types = $res->d->results;
			}elseif(!empty($res->d)){
				$types = $res->d;
			}
		}
		return $types;
	}

	public function GetPolicyById($policyId, $cultureCode) {
		if(empty($cultureCode)){
			$cultureCode = $GLOBALS['bfi_lang'];
		}
		$options = array(
			'path' => $this->urlGetPolicyById,
			'data' => array(
					'policyId' => $policyId,
					'cultureCode' => '\'' . $cultureCode . '\'',
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		$types = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->GetPolicyById)){
				$types = $res->d->GetPolicyById;
			}elseif(!empty($res->d)){
				$types = $res->d;
			}
		}
		return $types;
	}
	public function GetPolicyByIds($ids, $cultureCode) {
		if(empty($cultureCode)){
			$cultureCode = $GLOBALS['bfi_lang'];
		}
		$options = array(
			'path' => $this->urlGetPolicyByIds,
			'data' => array(
					'ids' => '\'' . $ids. '\'',
					'cultureCode' => '\'' . $cultureCode . '\'',
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		$types = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->GetPolicyByIds)){
				$types = $res->d->GetPolicyByIds;
			}elseif(!empty($res->d)){
				$types = $res->d;
			}
		}
		return $types;
	}


	public function GetMostRestrictivePolicyByIds($policyIds, $cultureCode, $stayConfiguration ='', $priceValue=null, $days=null) {
		if(empty($cultureCode)){
			$cultureCode = $GLOBALS['bfi_lang'];
		}
		$options = array(
			'path' => $this->urlGetMostRestrictivePolicyByIds,
			'data' => array(
					'policyIds' => '\'' .$policyIds. '\'',
					'cultureCode' => '\'' . $cultureCode . '\'',
					'$format' => 'json'
				)
			);
		if (!empty($stayConfiguration) ) {
			$options['data']['stayConfiguration'] = '\'' . $stayConfiguration . '\'';
		}
		if (!empty($priceValue) ) {
			$options['data']['priceValue'] = $priceValue;
		}
		if (!empty($days) ) {
			$options['data']['days'] = $days;
		}
		$url = $this->helper->getQuery($options);
		$types = null;
		
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->GetMostRestrictivePolicyByIds)){
				$types = $res->d->GetMostRestrictivePolicyByIds;
			}elseif(!empty($res->d)){
				$types = $res->d;
			}
		}
		return $types;
	}

	public function GetMerchantBookingTypeList($SearchModel, $resourceId, $cultureCode) {
		if(empty($cultureCode)){
			$cultureCode = $GLOBALS['bfi_lang'];
		}
		$checkIn = $SearchModel->FromDate;
		$checkOut = $SearchModel->ToDate;
		$options = array(
			'path' => $this->urlBookingTypes,
			'data' => array(
					'resourceId' => $resourceId,
					'cultureCode' => '\'' . $cultureCode . '\'',
					'$format' => 'json'
				)
			);
		if (!empty($checkIn) && !empty($checkOut)) {
			$options['data']['checkin'] = '\'' . $checkIn->format('Ymd') . '\'';
			$options['data']['checkout'] = '\'' . $checkOut->format('Ymd') . '\'';
		}
		$url = $this->helper->getQuery($options);
		$types = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$types = $res->d->results;
			}elseif(!empty($res->d)){
				$types = $res->d;
			}
		}
		return $types;
	}

	public function getPriceTypesFromService() {
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
				
		$options = array(
				'path' => sprintf($this->urlPriceTypes, $resourceId),
				'data' => array(
					'$filter' => 'Enabled eq true',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		$types = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$types = $res->d->results;
			}elseif(!empty($res->d)){
				$types = $res->d;
			}
		}
		return $types;
	}

	public function getExtrasFromService() {
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
				
		$options = array(
				'path' => sprintf($this->urlExtra, $resourceId),
				'data' => array(
					'$format' => 'json',
					'$filter' => 'IsEnabled eq true' /* Tags eq \'extra\' and */,
				)
			);
		
		$url = $this->helper->getQuery($options);
		$extras = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$extras = $res->d->results;
			}elseif(!empty($res->d)){
				$extras = $res->d;
			}
		}

		return $extras;
	}
	
	public function getRateplanFromService($ratePlanId) {
			
		$options = array(
				'path' => sprintf($this->urlRatePlan, $ratePlanId),
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		$rateplan = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$rateplan = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$rateplan = $res->d->results;
			}elseif(!empty($res->d)){
				$rateplan = $res->d;
			}
		}

		return $rateplan;
	}
	
	public function getRateplanSimpleDetails($ratePlanId) {
			
		$filter =  "Enabled eq true ";
		$options = array(
				'path' => sprintf($this->urlRatePlan, $ratePlanId),
				'data' => array(
					'$format' => 'json',
					'$select' => 'RatePlanId,Name,Description',
					'$filter' => $filter
				)
			);
		
		$url = $this->helper->getQuery($options);
		$rateplan = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$rateplan = $res->d->results;
			}elseif(!empty($res->d)){
				$rateplan = $res->d;
			}
		}

		return $rateplan;
	}

	public function GetRelatedResourceStays($merchantId,$relatedProductid,$excludedIds,$checkin,$duration,$paxages,$variationPlanId,$language="",$condominiumId ) {				
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

		$options = array(
				'path' =>  $this->urlGetRelatedResourceStays,
				'data' => array(
					'getAllResults' => 1,
					'merchantId' => $merchantId,
					'lite' => 1,
					'calculate' => 1,
					'topRresult' => 5,
					'skip' => 0,
					'relatedProductid' => $relatedProductid,
					'excludedIds' => '\'' . $excludedIds . '\'',
//					'pricetype' => 'rateplan',
//					'checkin' => '\'' . $checkin->format('YmdHis') . '\'',
					'checkin' => '\'' . $checkin->format('Ymd') . '\'',
					'duration' => $duration,
//					'paxages' => '\'' . implode('|',$paxages) . '\'',
					'paxages' => '\'' . implode('|',$newpaxages) . '\'',
					'paxes' => count($paxages),
					'getRelatedProducts' => 0,
					'$format' => 'json'
				)
			);
		if (!empty($language)) {
			$options['data']['cultureCode'] = '\'' . $language . '\'';
		}
		if (!empty($variationPlanId)) {
			$options['data']['variationPlanIds'] = '\'' .$variationPlanId . '\'';
		}
		if (!empty($condominiumId)) {
			$options['data']['condominiumId'] = $condominiumId ;
		}

		$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
		if($currUser!=null && !empty($currUser->CustomerId)) {
			$options['data']['userid'] = '\'' . $currUser->CustomerId . '\'';
		}

		$url = $this->helper->getQuery($options);
		
		$lstResult = new stdClass;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$lstResult = $res->d->results;
			}elseif(!empty($res->d)){
				$lstResult = $res->d;
			}

		}
		return $lstResult;
	}

	public function GetCompleteRatePlansStayWP($resourceId,$checkin,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId,$language="",$merchantBookingTypeId = "", $getAllResults=false ) {
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
		$options = array(
				'path' =>  $this->urlCompleteStayRatePlan,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $checkin->format('YmdHis') . '\'',
					'duration' => $duration,
//					'paxages' => '\'' . implode('|',$paxages) . '\'',
					'paxages' => '\'' . implode('|',$newpaxages) . '\'',
					'$format' => 'json'
				)
			);
		if (!empty($language)) {
			$options['data']['language'] = '\'' . $language . '\'';
		}
		if (!empty($variationPlanId)) {
			$options['data']['variationPlanId'] = $variationPlanId;
		}
		if (!empty($ratePlanId)) {
			$options['data']['ratePlanId'] = $ratePlanId;
		}
		
		if(!empty($selectablePrices)){
			$options['data']['selectablePrices'] = '\'' . $selectablePrices . '\'';
		}
		if(!empty($merchantBookingTypeId)){
			$options['data']['merchantBookingTypeId'] = $merchantBookingTypeId;
		}
		if(!empty($getAllResults)){
			$options['data']['exploded'] = $getAllResults ? 1: 0;
		}

		$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
		if($currUser!=null && !empty($currUser->CustomerId)) {
			$options['data']['userid'] = '\'' . $currUser->CustomerId . '\'';
		}
			
		$url = $this->helper->getQuery($options);
		
		$ratePlansStay = new stdClass;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ratePlansStay = $res->d->results;
			}elseif(!empty($res->d)){
				$ratePlansStay = $res->d;
			}

		}
		return $ratePlansStay;
	}	

	
	
	
	public function getCompleteRateplansStayFromParameter($resourceId,$checkin,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId,$language="",$availabilitytype=1 ) {
		$checkIn = $checkin->format('YmdHis');
			
		if ($availabilitytype == 0 || $availabilitytype ==1 ) // product TimePeriod
		{
			$checkIn = $checkin->format('Ymd');
		}

		$options = array(
				'path' =>  $this->urlCompleteStayRatePlan,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $checkIn . '\'',
					'duration' => $duration,
					'paxages' => '\'' . implode('|',$paxages) . '\'',
					'$format' => 'json'
				)
			);
		if (!empty($language)) {
			$options['data']['language'] = '\'' . $language . '\'';
		}
		if (!empty($variationPlanId)) {
			$options['data']['variationPlanId'] = $variationPlanId;
		}
		if (!empty($ratePlanId)) {
			$options['data']['ratePlanId'] = $ratePlanId;
		}
		
		if(!empty($selectablePrices)){
			$options['data']['selectablePrices'] = '\'' . $selectablePrices . '\'';
		}

			
		$url = $this->helper->getQuery($options);
		
		$stay = new stdClass;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
				$foundStay = false;
				$ratePlans =  array();
				if (!empty($res->d)) {
					foreach ($res->d as $ratePlanStay) {
						$rs = $ratePlanStay;
						if (!$foundStay && $ratePlanStay != null && $ratePlanStay->SuggestedStay != null) {
							$rs->CalculatedPricesDetails = json_decode($ratePlanStay->CalculatedPricesString);
							$rs->SelectablePrices = json_decode($ratePlanStay->CalculablePricesString);
							$rs->CalculatedPackages = json_decode($ratePlanStay->PackagesString);
							$rs->DiscountVariation = null;
							if(!empty($ratePlanStay->Discount)){
								$rs->DiscountVariation = $ratePlanStay->Discount;

							}
							$rs->SupplementVariation =null;
							if(!empty($ratePlanStay->Supplement)){
								$rs->SupplementVariation = $ratePlanStay->Supplement;
							}
								
							$allVar = json_decode($ratePlanStay->AllVariationsString);
							$rs->Variations= [];
							foreach ($allVar as $currVar) {
								$rs->Variations[] = $currVar;
							}
							
							$foundStay = true;
						}
						$ratePlans[] = $rs;
					}
				}
				$stay = $ratePlans;
			}
		return $stay;
	}	

	public function getStayFromServiceFromParameter($resourceId,$ci,$du,$px,$ex,$pkgs,$pt,$rpId,$vpId,$hasRateplans ) {
			$options = array(
				'path' =>  $hasRateplans ? $this->urlStayRatePlan : $this->urlStay, //  BFCHelper::isRatePlanStay() ? $this->urlStayRatePlan : $this->urlStay,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					'paxages' => '\'' . implode('|',$px) . '\'',
					'$format' => 'json'
				)
			);

		if ($hasRateplans) {
			$options['data']['ratePlanId'] = $rpId;
			$options['data']['selectablePrices'] = '\'' . $ex . '\'';
		} else {
			$options['data']['extras'] = '\'' . $ex . '\'';
			$options['data']['priceType'] = '\'' . $pt . '\'';
		}
			
		$url = $this->helper->getQuery($options);
		
		$stay = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			if ($hasRateplans) {			
				$foundStay = false;
				$ratePlans =  array();
				foreach ($res->d as $ratePlanStay) {
					$ratePlans[] = $ratePlanStay;
					if (!$foundStay && $ratePlanStay != null && $ratePlanStay->SuggestedStay != null) {
						$stay = $ratePlanStay->SuggestedStay;
						$stay->RatePlanStay = $ratePlanStay;
						unset($stay->RatePlanStay->SuggestedStay); // remove for encoding json. prevent php5.5 JSON_ERROR_RECURSION
						$foundStay = true;
					}
				}
				$stay->RatePlans = $ratePlans;
			} else {
				$stay = $res->d->GetStay;	
			}
		}
		
		return $stay;
	}	

	
	public function getStayFromService($language='',$exploded = false) {
		$params = $_SESSION['search.params'];

		$resourceId =  isset($params['resourceId'])? $params['resourceId'] : 0;
		if(empty($resourceId)){
			$resourceId = $this->resourceid;
		}
		$ci = !empty($params['checkin'])? $params['checkin'] : new DateTime('UTC');
		if(new DateTime('UTC') >$ci){
			$ci =  new DateTime('UTC');
		}

		$du = !empty($params['duration'])? $params['duration'] : 7;
		$px = isset($params['paxages'])? $params['paxages'] :  array('18','18');
		$ex = isset($params['extras']) ? $params['extras'] : '';
		$pkgs = isset($params['packages']) ? $params['packages'] : '';
		$pt = isset($params['pricetype']) ? $params['pricetype'] : BFCHelper::getVar('pricetype','');
		$rpId = isset($params['rateplanId']) ? $params['rateplanId'] : $pt;
		$vpId = isset($params['variationPlanId']) ? $params['variationPlanId'] : '';
		$hasRateplans = 1;
		$options = array(
				'path' =>  $this->urlCompleteStayRatePlan, //$hasRateplans ? $this->urlStayRatePlan : $this->urlStay, //  BFCHelper::isRatePlanStay() ? $this->urlStayRatePlan : $this->urlStay,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					'paxages' => '\'' . implode('|',$px) . '\'',
					'$format' => 'json'
				)
			);
		if (!empty($language)) {
			$options['data']['language'] = '\'' . $language . '\'';
		}
		if($exploded){
			$options['data']['exploded'] = $exploded;
		}
		if (!empty($vpId)) {
			$options['data']['variationPlanId'] = $vpId;
		}

		$options['data']['ratePlanId'] = $rpId;
		if(!empty($ex)){
			$options['data']['selectablePrices'] = '\'' . $ex . '\'';
		}
			
		$url = $this->helper->getQuery($options);
		
		$stay = new stdClass;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if ($hasRateplans) {			
				$foundStay = false;
				$ratePlans =  array();
				if (!empty($res->d)) {
					foreach ($res->d as $ratePlanStay) {
						$rs = $ratePlanStay;
						if (!$foundStay && $ratePlanStay != null && $ratePlanStay->SuggestedStay != null) {
							$rs->CalculatedPricesDetails = json_decode($ratePlanStay->CalculatedPricesString);
							$rs->SelectablePrices = json_decode($ratePlanStay->CalculablePricesString);
							$rs->CalculatedPackages = json_decode($ratePlanStay->PackagesString);
							$rs->DiscountVariation = $ratePlanStay->Discount;
							$rs->SupplementVariation = $ratePlanStay->Supplement;
							$allVar = json_decode($ratePlanStay->AllVariationsString);
							$rs->Variations= [];
							foreach ($allVar as $currVar) {
								//if(empty($currVar->IsExclusive)){
								//if($currVar->IsExclusive){
									$rs->Variations[] = $currVar;
								//}
							}
							$foundStay = true;
						}
						$ratePlans[] = $rs;
					}
				}
				$stay = $ratePlans;
			} else {
				if (!empty($res->d->GetStay)){
				$stay = $res->d->GetStay;	
			}
			}
		}
		
		
		return $stay;
	}	
	
	public function getRatePlanStayBookingTypes($ratePlanStay, $variationPlanId){
		$options = array(
				'path' => $this->urlBookingTypesRatePlan,
				'data' => array(
					'ratePlanStayId' => '\'' . $ratePlanStay->RatePlanStayId . '\'',
					'variationPlanId' => $variationPlanId,
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);
		
		$result = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$result = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$result = $res->d->results;
			}elseif(!empty($res->d)){
				$result = $res->d;
			}
		}
		
		return $result;
	}
	
	public function getRatePlanStaySupplement($ratePlanStay){
		$options = array(
				'path' => $this->urlSupplement,
				'data' => array(
					'ratePlanStayId' => '\'' . $ratePlanStay->RatePlanStayId . '\'',
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);
		
		$result = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$result = $res->d->GetRatePlanStaySupplement;
			if (!empty($res->d->GetRatePlanStaySupplement)){
			$result = $res->d->GetRatePlanStaySupplement;
		}
		}
		
		return $result;
	}

	public function getRatePlanStayVariations($ratePlanStay){
		$options = array(
				'path' => $this->urlVariations,
				'data' => array(
					'ratePlanStayId' => '\'' . $ratePlanStay->RatePlanStayId . '\'',
					'$filter' => 'IsExclusive eq false',
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);
		
		$result = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$result = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$result = $res->d->results;
			}elseif(!empty($res->d)){
				$result = $res->d;
			}
		}
		
		return $result;
	}	
	
	public function getRatePlanStayDiscount($ratePlanStay){
		$options = array(
				'path' => $this->urlDiscount,
				'data' => array(
					'ratePlanStayId' => '\'' . $ratePlanStay->RatePlanStayId . '\'',
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);
		
		$result = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$result = $res->d->GetRatePlanStayDiscount;
			if (!empty($res->d->GetRatePlanStayDiscount)){
			$result = $res->d->GetRatePlanStayDiscount;
		}
		}
		
		return $result;
	}
	
	public function getCalculatedPackages($pkgs, $variationPlanId) {
		$results = array();
		$packages = array();
		if ($pkgs!=null && $pkgs!='') {
			foreach (explode('|',$pkgs) as $p) {
				$val = explode(':',$p);
				$key = $val[0];
				if (!array_key_exists($key, $packages)) {
					$packages[$key] = array();
				}
				$packages[$key][] = $val[1] . ':' . $val[2];
			}
			foreach($packages as $pkgId => $pack) {
	
				$params = $this->getState('params');
				$resourceId = $params['resourceId'];
				$ci = $params['checkin'];
				$du = $params['duration'];
				$px = $params['paxages'];
				
				$options = array(
						'path' => $this->urlPackageStay,
						'data' => array(
							'packageId' => $pkgId,
							'resourceId' => $resourceId,
							'checkin' => '\'' . $ci->format('Ymd') . '\'',
							'duration' => $du,
							'paxages' => '\'' . implode('|',$px) . '\'',
							'selectablePrices' => '\'' . implode('|',$pack) . '\'',
							'$format' => 'json'
						)
					);
					
				$url = $this->helper->getQuery($options);
				
				$r = $this->helper->executeQuery($url);
				if (isset($r)) {
					$res = json_decode($r);
					$pkg = $res->d->GetPackageStay;
					if ($pkg != null) {
						$pkg->PackageId = $pkgId;
						$pkg->CalculatedPricesDetails = $this->getCalculatedPricesDetails($pkg);
						$pkg->Variations = $this->getRatePlanStayVariations($pkg, $vpId);
						$results[] = $pkg;
					}
				}
			}
		}
		return $results;
	}
	
	public function getCalculatedPricesDetails($ratePlanStay) {
		$options = array(
				'path' => $this->urlPrice,
				'data' => array(
					'ratePlanStayId' => '\'' . $ratePlanStay->RatePlanStayId . '\'',
					'$format' => 'json'
				)
			);
			
		$url = $this->helper->getQuery($options);
		
		$calculatedPrices = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$calculatedPrices = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$calculatedPrices = $res->d->results;
			}elseif(!empty($res->d)){
				$calculatedPrices = $res->d;
			}
		}
		
		return $calculatedPrices;
	}

	
	public function getResourcePriceListFromService($resourceId = null,$year=null) {
		$params = $this->getState('params');
		if ($resourceId==null) {
			$resourceId = $params['resourceId'];
		}
		if ($year==null) {
			$now = new DateTime('UTC');
			$year = $now->format('Y');
		}
				
		$options = array(
				'path' => $this->urlPriceList,
				'data' => array(
					'resourceId' => $resourceId,
					'year' => $year,
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$priceList = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$priceList = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$priceList = $res->d->results;
			}elseif(!empty($res->d)){
				$priceList = $res->d;
			}
		}
		
		return $priceList;
	}

	public function getResourceServicesFromService($resourceId = null) {
		$params = $this->getState('params');
		if ($resourceId==null) {
			$resourceId = $params['resourceId'];
		}
				
		$options = array(
				'path' => sprintf($this->urlUnitServices, $resourceId),
				'data' => array(
					'$filter' => 'Enabled eq true',
					'$format' => 'json',
					'orderby' => 'IsDefault asc'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$services = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$services = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$services = $res->d->results;
			}elseif(!empty($res->d)){
				$services = $res->d;
			}
		}
		
		return $services;
	}

	public function getStartDateFromService() {
		$options = array(
				'path' => $this->urlStartDate,
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$formatDate = 'd/m/Y';
		$startDate = date($formatDate); // returns 09/15/2007

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$dateReturn = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$dateReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$dateReturn = $res->d;
			}
			if (!empty($dateReturn)){
			$dateparsed = BFCHelper::parseJsonDate($dateReturn->GetStartDate,"");
			//if ($dateparsed>$startDate) $startDate = $dateparsed;
			$d1 =DateTime::createFromFormat('d/m/Y',$dateparsed,new DateTimeZone('UTC'));
			$d2 =DateTime::createFromFormat('d/m/Y',$startDate,new DateTimeZone('UTC'));
			if ($d1>$d2) {
				$startDate = $dateparsed;
			}
			}

		}
		
		return $startDate;
	}

		
	public function getEndDateFromService() {
		$options = array(
				'path' => $this->urlEndDate,
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$formatDate = 'd/m/Y';
		$endDate = date($formatDate); // returns 09/15/2007

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$dateReturn = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$dateReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$dateReturn = $res->d;
			}
			if(!empty($dateReturn)){
				$endDate = BFCHelper::parseJsonDate($dateReturn->GetEndDate,$formatDate);
			}
		}
		
		return $endDate;
	}

	public function getStartDateByMerchantId($merchantId = 0) {

		date_default_timezone_set('UTC');		
		return date("d/m/Y");

////		return true; //TODO: da fare
//		$startDateByMerchantId = BFCHelper::getSession('getStartDateByMerchantId_'.$merchantId, null , 'com_bookingforconnector');
//		if ($startDateByMerchantId==null) {
//			$startDateByMerchantId = $this->getStartDateByMerchantIdFromService($merchantId);
//			BFCHelper::setSession('getStartDateByMerchantId_'.$merchantId, $startDateByMerchantId, 'com_bookingforconnector');
//		}
//		return $startDateByMerchantId;
	}

	public function getStartDateByMerchantIdFromService($merchantId = 0) {
		$options = array(
				'path' => $this->urlStartDateByMerchantId,
				'data' => array(
					'$format' => 'json',
					'merchantId' => $merchantId
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$formatDate = 'd/m/Y';
		$startDate = date($formatDate); // returns 09/15/2007

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$dateReturn = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$dateReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$dateReturn = $res->d;
			}
			$dateparsed = BFCHelper::parseJsonDate($dateReturn->GetStartDateByMerchantId,"");
			//if ($dateparsed>$startDate) $startDate = $dateparsed;
			$d1 =DateTime::createFromFormat('d/m/Y',$dateparsed,new DateTimeZone('UTC'));
			$d2 =DateTime::createFromFormat('d/m/Y',$startDate,new DateTimeZone('UTC'));
			if ($d1>$d2) {
				$startDate = $dateparsed;
			}

		}
		
		return $startDate;
	}

		
	public function getEndDateByMerchantId($merchantId = 0) {
		date_default_timezone_set('UTC');		
		return date("d/m/Y", strtotime('+1 years'));

////		return true; //TODO: da fare
//		$endDateByMerchantId = BFCHelper::getSession('getEndDateByMerchantId'.$merchantId, null , 'com_bookingforconnector');
//		if ($endDateByMerchantId==null) {
//			$endDateByMerchantId = $this->getEndDateByMerchantIdFromService($merchantId);
//			BFCHelper::setSession('getEndDateByMerchantId'.$merchantId, $endDateByMerchantId, 'com_bookingforconnector');
//		}
//		return $endDateByMerchantId;
	}

	public function getEndDateByMerchantIdFromService($merchantId = 0) {
		$options = array(
				'path' => $this->urlEndDateByMerchantId,
				'data' => array(
					'$format' => 'json',
					'merchantId' => $merchantId
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$formatDate = 'd/m/Y';
		$endDate = date($formatDate); // returns 09/15/2007

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$dateReturn = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$dateReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$dateReturn = $res->d;
			}
				$endDate = BFCHelper::parseJsonDate($dateReturn->GetEndDateByMerchantId,$formatDate);
			}

		return $endDate;
	}
	
	public function getCheckInDatesFromService($resourceId = null,$ci= null) {
//		if ($resourceId==null) {
//			$params = $this->getState('params');
//			$resourceId = $params['resourceId'];
//		}
		if ($ci==null) {
			$ci =  new DateTime('UTC');
		}
		//$ci = $params['checkin'];
		$options = array(
				'path' => $this->urlCheckInDates,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$listDate = null;

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$listDate = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$listDate = $res->d->results;
			}elseif(!empty($res->d)){
				$listDate = $res->d;
			}
			if (!empty($listDate)){
				$listDate = implode(',',$listDate);
			}
		}
		
		return $listDate;
	}
	
	public function getCheckOutDatesFromService($resourceId = null,$ci= null) {
		//$params = $this->getState('params');
		if ($resourceId==null) {
			$params = $_SESSION['search.params'];
			$resourceId = $params['resourceId'];
		}
		if ($ci==null) {
			$ci =  new DateTime('UTC');
		}
		//$ci = $params['checkin'];
		$options = array(
				'path' => $this->urlCheckOutDates,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$listDate = '';

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$listDate = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$listDate = $res->d->results;
			}elseif(!empty($res->d)){
				$listDate = $res->d;
			}
			if(!empty($listDate)){
				$listDate = implode(',',$listDate);
			}
		}
		
		return $listDate;
	}

	public function getCheckAvailabilityCalendarFromService($resourceId = null,$checkIn= null,$checkOut= null) {
		if ($resourceId==null || $checkIn ==null  || $checkOut ==null ) {
			$params = $this->getState('params');
		}
		if ($resourceId==null) {
			$resourceId = $params['resourceId'];
		}
		if ($checkIn==null) {
			//$defaultDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDate());
			$checkIn =  BFCHelper::getStayParam('checkin', DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDate(),new DateTimeZone('UTC')));
		}
		if ($checkOut==null) {
			$checkOut =   BFCHelper::getStayParam('checkout', $checkIn->modify(BFCHelper::$defaultDaysSpan));
		}
		//calcolo le settimane necessarie

		//$ci = $params['checkin'];
		$options = array(
				'path' => $this->urlCheckAvailabilityCalendar,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $checkIn->format('Ymd') . '\'',
					'checkout' => '\'' . $checkOut->format('Ymd') . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$resultCheck = false;

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$checkDate = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$checkDate = $res->d->results;
			}elseif(!empty($res->d)){
				$checkDate = $res->d;
			}
			$resultCheck = $checkDate->CheckAvailabilityCalendar;
		}
		
		return $resultCheck;
	}
	
		
	public function getUnitCategoriesFromService() {
		
		$options = array(
				'path' => $this->urlUnitCategories,
				'data' => array(
						'$select' => 'ProductCategoryId,Name,ParentCategoryId',
						'$filter' => 'Enabled eq true',
						'$orderby' => 'Order asc',
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
	
	public function getUnitCategories() {
//		$session = JFactory::getSession();
		$categories = BFCHelper::getSession('getUnitCategories', null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($categories==null) {
			$categories = $this->getUnitCategoriesFromService();
			BFCHelper::setSession('getUnitCategories', $categories, 'com_bookingforconnector');
		}
		return $categories;
	}

	protected function populateState($ordering = NULL, $direction = NULL) {
		//$ci = clone BFCHelper::getStayParam('checkin', new DateTime('UTC'));

		//recupero la prima data disponibile per la risorsa se riesco altrimenti recupero la prima data disponibile
		$resourceId = BFCHelper::getInt('resourceId');
		if(!empty($resourceId)){
			$dates = $this->getCheckInDatesFromService($resourceId,null);
			if (($pos = strpos($dates, ','))!==false)
				$dates = explode(",",$dates);
			
			if (is_array($dates)){
				$tmpDate1 = array_values($dates);
				$tmpDate = array_shift($tmpDate1);
				$defaultDate = DateTime::createFromFormat('Ymd',$tmpDate,new DateTimeZone('UTC'));
//				$defaultDate = DateTime::createFromFormat('Ymd',array_shift(array_values($dates)));
			}elseif($dates != ''){
				$defaultDate = DateTime::createFromFormat('Ymd',$dates,new DateTimeZone('UTC'));
			}
		}
		if (!isset($defaultDate)){
			$defaultDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDate(),new DateTimeZone('UTC'));
		}


		if(new DateTime('UTC') >$defaultDate){
			$defaultDate =  new DateTime('UTC');
		}
		$ci = clone BFCHelper::getStayParam('checkin', $defaultDate);
		$defaultRequest =  array(
			'resourceId' => BFCHelper::getInt('resourceId'),
			'checkin' => BFCHelper::getStayParam('checkin', $defaultDate),
			'checkout' => BFCHelper::getStayParam('checkout', $ci->modify(BFCHelper::$defaultDaysSpan)),
			'duration' => BFCHelper::getStayParam('duration'),
			'paxages' => BFCHelper::getStayParam('paxages'),
			'extras' => BFCHelper::getStayParam('extras'),
			'packages' => BFCHelper::getStayParam('packages'),
			'pricetype' => BFCHelper::getStayParam('pricetype'),
			'rateplanId' => BFCHelper::getStayParam('rateplanId'),
			'variationPlanId' => BFCHelper::getStayParam('variationPlanId'),
			'state' => BFCHelper::getStayParam('state'),
			'gotCalculator' => BFCHelper::getBool('calculate'),
			'paxes' => count(BFCHelper::getStayParam('paxages'))
		);
		
//		echo "<pre>defaultRequest";
//		echo print_r($defaultRequest);
//		echo "</pre>";
				
		$stayrequest = BFCHelper::getVar('stayrequest');
		
		// support for rsforms!
		if ($stayrequest == null || $stayrequest == '') {
			$form = BFCHelper::getVar('form');
			$stayrequest = htmlspecialchars_decode($form['stayrequest'], ENT_COMPAT);
		}

		if ($stayrequest != null && $stayrequest != '') {
			try {
				$params = json_decode($stayrequest);
				$stayCheckin = DateTime::createFromFormat('d/m/Y',$params->checkin,new DateTimeZone('UTC'));
				if(new DateTime('UTC') <$stayCheckin){
				$defaultRequest = array(
					'resourceId' => $params->resourceId,
					'checkin' => DateTime::createFromFormat('d/m/Y',$params->checkin,new DateTimeZone('UTC')),
					'checkout' => DateTime::createFromFormat('d/m/Y',$params->checkout,new DateTimeZone('UTC')),
					'duration' => $params->duration,
					'paxages' => $params->paxages,
					'extras' => $params->extras,
					'packages' => $params->packages,
					'pricetype' => $params->pricetype,
					'rateplanId' => $params->rateplanId,
					'variationPlanId' => $params->variationPlanId,
					'state' => $params->state,
					'gotCalculator' => false,
					'fromExtForm' => true,
						'hasRateplans' => false,
						'paxes' => count($params->paxages)
					);
				}

			} catch (Exception $e) {
				
			}
		}

		//echo var_dump($defaultRequest);die();
		$this->setState('params', $defaultRequest);

		return parent::populateState();
	}
	
	public function getItem($resource_id) {
		$item = $this->getResourceFromService($resource_id);
		return $item;
	}
	
	public function getStay($language='',$forceGet = false,$exploded = false){
//		$rsidg = $rsid ;
	  $stay = $this->getStayFromService($language,$exploded);
	  return $stay;
	}

/*----------rating--------------*/
	public function getItems($type = '') {
		
		switch($type) {
			case 'ratings':
				$items = $this->getRatingsFromService(
					$this->getStart($type),
					$this->getState('list.limit')
				);
				break;
			default:
				break;
		}
				
		return $items;
	}
	


	public function getItemsRating() 
	{
		return $this->getItems('ratings');
	}
	
	public function getRating() 
	{
		return $this->getItems('ratings');
	}	


	public function getTotal($type = '')
	{
		switch($type) {
			case 'ratings':
				return $this->getTotalRatings();
				break;
			case '':
			default:
				return 0;
		}
	}	

	public function getTotalRatings()
	{
		$options = array(
				'path' => $this->urlRatingCount,
				'data' => array(
					'ResourceId' =>  BFCHelper::getInt('resourceId') 
			)
		);
		
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

	public function getRatingAverageFromService($merchantId = null,$resourceId = null) {
//		$params = $this->getState('params');
//		$params = $_SESSION['search.params'];
//		
//		if ($merchantId==null) {
//			$merchantId = $params['merchantId'];
//			$resourceId = $params['resourceId'];
//		}
		
		$options = array(
				'path' => $this->urlRatingAverage,
				'data' => array(
					'merchantId' => $merchantId,
					'resourceId' => $resourceId,
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$ratings = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$resRatings = $res->d->results ?: $res->d;
			$resRatings = null;
			if (!empty($res->d->results)){
				$resRatings = $res->d->results;
			}elseif(!empty($res->d)){
				$resRatings = $res->d;
			}
			if (!empty($resRatings)){
				$ratings = $resRatings->GetResourceAverage;
			}
		}

		return $ratings;
	}	

	public function getRatingsFromService($start, $limit, $resourceId = null) {

		if ($resourceId==null) {
			$resourceId = $_SESSION['search.params']['resourceId'];
		}
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];
		}
		
		$options = array(
				'path' => $this->urlRating,
				'data' => array(
//					'$filter' => 'ResourceId eq ' . $resourceId . ' and Enabled eq true',
//					'$orderby' => 'CreationDate desc',
//					'$format' => 'json'
					'Culturecode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		if (isset($start) && $start > 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}		
		if (isset($resourceId) && $resourceId > 0) {
			$options['data']['ResourceId'] = $resourceId;
		}		
		
		$filters = null;
		if (isset($_SESSION['params.rating']['filters'])) {
		$filters = $_SESSION['params.rating']['filters'];
		}

		if ($filters != null && $filters['typologyid'] != null && $filters['typologyid']!= "0") {
			$options['data']['$filter'] .= ' and TypologyId eq ' .$filters['typologyid'];
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

	private function filterRatingResults($results) {
		$params = $_SESSION['search.params'];
//		$params = $this->getState('params');
		$filters = $params['filters'];
		if ($filters == null) return $results;

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

	function getPaginationRatings()
	{	
		return $this->getPagination('ratings');
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

	public function getPolicy($resourcesId, $language='') {
		$options = array(
				'path' => $this->urlGetPolicy,
				'data' => array(
					'resourceId' => $resourcesId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results->GetPolicy;
			}elseif(!empty($res->d)){

				$return = $res->d->GetPolicy;
			}
		}
		return $return;
	}	

	public function getSearchResults($start, $limit, $ordering, $direction, $merchantid, $condominiumid = null, $ignorePagination = false, $jsonResult = false, $excludedResources = array(), $requiredOffers = array(), $overrideFilters = null,$language='') {

		$this->currentOrdering = $ordering;
		$this->currentDirection = $direction;
		
		$searchid = '';
		$newsearch = true;
		$language = $GLOBALS['bfi_lang'];
		
		$results = null;
		if ($results == null) {

				$filterData = array (
						'$format' => 'json',
						'getAllResults' => 1,
						'merchantId' => $merchantid,
						'cultureCode' => BFCHelper::getQuotedString($language),
//						'calculate' => 1, // spostato nell'applicazione dei filtri altrimenti mi calcola i prezzi anche se non voglio
						'lite' => 1
				);
				if(!$ignorePagination && isset($limit) && $limit > 0) {
					$filterData['topRresult'] = $limit;
				}
//				if(!$ignorePagination && isset($start) && $start > 0) {
				if(!$ignorePagination && isset($start)) {
					$filterData['skip'] = $start;
				}
				if(isset($condominiumid)) {
					$filterData['condominiumId'] = $condominiumid;
				}
				if(!empty($excludedResources)){
					if(is_array($excludedResources) ) {
						$filterData['excludedIds'] = BFCHelper::getQuotedString(implode(',', $excludedResources));
					}else{
						$filterData['excludedIds'] =  BFCHelper::getQuotedString($excludedResources);
					}
				}
				if(!empty($requiredOffers)){
					if(is_array($requiredOffers) ) {
						$filterData['variationPlanIds'] = BFCHelper::getQuotedString(implode(',', $requiredOffers));
					}else{
						$filterData['variationPlanIds'] = BFCHelper::getQuotedString($requiredOffers);
					}
				}
			$options = array(
				'path' => $this->urlSearchAllCalculate,
				'data' => $filterData
			);
			$this->applyDefaultFilter($options, $overrideFilters);
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
//				try {				
//					if (!empty($results)) {
//						shuffle($results);
//					}
//				} catch (Exception $e) {
//					//echo 'Caught exception: ',  $e->getMessage(), "\n";
//				}
			}
			
		$onlystay = '1';
		if(!empty($results) && $onlystay =='1'){
				$results = array_filter($results, function($result) {
					return $result->Price >0;
				});
		}

		}

				return $results;

		//return $jsonResult ? json_encode($results) : $results;
	}

}
endif;