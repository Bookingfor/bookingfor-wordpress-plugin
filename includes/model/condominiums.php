<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelCondominiums' ) ) :

class BookingForConnectorModelCondominiums
{
	private $urlCondominiums = null;
	private $urlCondominiumsbyids = null;
	private $urlCondominiumsCount = null;
	private $urlCondominiumsbyid = null;
	private $condominiumsCount = 0;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlCondominiums = '/GetCondominiums';
		$this->urlCondominiumsbyids = '/GetCondominiumsByIds';
		$this->urlCondominiumsCount = '/GetCondominiumsCount';
		$this->urlCondominiumsbyid = '/GetCondominiumById';
	}
	
	public function applyDefaultFilter(&$options) {

	}
	
	
//	public function getCondominiumsFromService($start, $limit, $ordering, $direction) {
	public function getCondominiumsFromService($start, $limit) {// with randor order is not possible to otrder by another field
		$options = array(
				'path' => $this->urlCondominiums,
				'data' => array(
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
		
		$condominiums = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$condominiums = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$condominiums = $res->d->results;
			}elseif(!empty($res->d)){
				$condominiums = $res->d;
			}

		}

		return $condominiums;
	}
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlCondominiumsCount,
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
			$count = $res->d->GetCondominiumsCount;
		}

		return $count;
	}
	
	public function getCondominiumsByIds($listsId,$language='') {// with randor order is not possible to otrder by another field
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
		$options = array(
				'path' => $this->urlCondominiumsbyids,
				'data' => array(
					'$format' => 'json',
					'ids' =>  '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language)
				)
			);
 
		$url = $this->helper->getQuery($options);
		
		$condominiums = null;
		
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$condominiums = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$condominiums = json_encode($res->d);
			}
		}

		return $condominiums;
	}

	public function getCondominiumFromService($condominiumId,$language='')
	{
//		$params = $this->getState('params');
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
	
		$data = array(
				'$format' => 'json',
				'id' =>  $condominiumId,
				'cultureCode' => BFCHelper::getQuotedString($language)
		);
		
		$options = array(
				'path' => $this->urlCondominiumsbyid,
				'data' => $data
		);
		
		$url = $this->helper->getQuery($options);
		
		$condominium= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$condominium = $res->d->results->GetCondominiumById;
			}elseif(!empty($res->d)){
				$condominium = $res->d->GetCondominiumById;
			}

		}
		if(!empty($condominium)){
			$condominium->Merchant=BFCHelper::getMerchantFromServicebyId($condominium->MerchantId);
		}
		
		
		return $condominium;
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

		$items = $this->getCondominiumsFromService(
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