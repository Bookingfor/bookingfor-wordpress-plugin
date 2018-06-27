<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelOrders Model
 */
if ( ! class_exists( 'BookingForConnectorModelRatings' ) ) :

class BookingForConnectorModelRatings
{
	private $urlCreateRating = null;
	private $urlRatingView = null;
	private $urlRatingViewCount = null;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlCreateRating = '/CreateRating';
		$this->urlRatingView = '/RatingsView';
		$this->urlRatingViewCount = '/RatingsView/$count';
		$this->urlAllRatingCount = '/Ratings/$count';
	}
	
	public function setRating(
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
		$options = array(
				'path' => $this->urlCreateRating,
				'data' => array(
					'name' => BFCHelper::getQuotedString($name),
					'city' => BFCHelper::getQuotedString($city),
					'typologyid' => $typologyid,
					'email' => BFCHelper::getQuotedString($email),
					'nation' => BFCHelper::getQuotedString($nation),
					'merchantId' => $merchantId,
					'value1' => $value1,
					'value2' => $value2,
					'value3' => $value3,
					'value4' => $value4,
					'value5' => $value5,
					'total' => $totale,
					'notesData' => BFCHelper::getQuotedString($pregi),
					'notesData1' => BFCHelper::getQuotedString($difetti),
					'userId' => $userId,
					'orderId' => $orderId,
					'resourceId' => $resourceId,
					'checkin' => BFCHelper::getQuotedString($checkin),
					'label' => BFCHelper::getQuotedString($label),
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'processRating' => 1,
					'$format' => 'json'
				)
			);

		if(!empty($otherData)){
			$options['data']['otherData'] = BFCHelper::getQuotedString($otherData);
		}

		$url = $this->helper->getQuery($options);

	
		$ratingId = 0;
		
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);

			if (!empty($res->d->results)){
				$ratingId = $res->d->results->CreateRating;
			}elseif(!empty($res->d)){
				$ratingId = $res->d->CreateRating;
			}
		}
		
		return $ratingId;
	}

	public function getRatingsByOrderIdFromService($orderId = null) {
		
		if ($orderId==null) {
			return null;
		}
		
		$options = array(
				'path' => $this->urlRatingView,
				'data' => array(
					'$filter' => 'OrderId eq ' . $orderId ,
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
				)
		);
		
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
		
		return $ratings;
	}	

	public function getTotalRatingsByOrderId($orderId = null)
	{
		if ($orderId==null) {
			return 0;
		}
		
		$options = array(
				'path' => $this->urlAllRatingCount,
				'data' => array(
					'$filter' => 'OrderId eq ' . $orderId
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
	
	protected function populateState($ordering = NULL, $direction = NULL) {
//		$filter_order = BFCHelper::getCmd('filter_order','Name');
//		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');
// 
//		$this->setState('params', array(
//			'typeId' => BFCHelper::getInt('typeId'),
//			'categoryId' => BFCHelper::getVar('categoryId')
//		));
		
		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		return $this->cache[$store];
	}
}
endif;