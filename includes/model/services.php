<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelServices' ) ) :

class BookingForConnectorModelServices
{
	private $urlServices = null;
	private $urlGetService = null;
	private $urlServicesCount = null;
	private $servicesCount = 0;
	private $urlServicesbyid = null;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlGetService = '/Services(%d)';
		$this->urlServices = '/Services';
		$this->urlServicesCount = '/Services/$count/';
		$this->urlServicesbyid = '/GetServicesByIds';
	}
	
	public function applyDefaultFilter(&$options) {

	}
	
	
//	public function getservicesFromService($start, $limit, $ordering, $direction) {
	public function getServicesFromService($start, $limit) {// with randor order is not possible to otrder by another field
		$options = array(
				'path' => $this->urlServices,
				'data' => array(
					'$filter' => 'Enabled eq true',
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
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlServicesCount,
				'data' => array(
					'$filter' => 'Enabled eq true',
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = $res->d->GetServicesCount;
		}

		return $count;
	}
	
	public function getServicesByIds($listsId,$language='') {// with randor order is not possible to otrder by another field
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
		$options = array(
				'path' => $this->urlServicesbyid,
				'data' => array(
					'$format' => 'json',
					'ids' =>  '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language)
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

	public function getServiceFromService($serviceid)
	{
		$params = $this->getState('params');
	
		$data = array(
				'$format' => 'json'
		);
		
		$options = array(
				'path' => sprintf($this->urlGetService, $serviceid),
				'data' => $data
		);
		
		$url = $this->helper->getQuery($options);
		
		$service= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$service = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$service = $res->d->results;
			}elseif(!empty($res->d)){
				$service = $res->d;
			}

		}
		
		
		return $service;
	}
		
	protected function populateState($ordering = NULL, $direction = NULL) {
		$filter_order = BFCHelper::getCmd('filter_order','Order');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');		
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

		$items = $this->getServicesFromService(
			$this->getStart(), 
			$this->getState('list.limit'), 
			$this->getState('list.ordering', 'Order'), 
			$this->getState('list.direction', 'asc')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
endif;