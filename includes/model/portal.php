<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * BookingForConnectorModelOrders Model
 */
if ( ! class_exists( 'BookingForConnectorModelPortal' ) ) :

class BookingForConnectorModelPortal
{
		
	private $urlGetPrivacy = null;
	private $urlGetAdditionalPurpose = null;
	private $urlGetProductCategoryForSearch = null;
	private $urlGetCartmultimerchantenabled = null;
	private $urlGetDefaultCurrency = null;
	private $urlGetCurrencyExchanges = null;

	private $urlGetLoginTwoFactor = null;
	private $urlGetTwoFactorUniqueNumber = null;

	private $helper = null;

	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlGetPrivacy = '/GetPrivacy';
		$this->urlGetAdditionalPurpose = '/GetAdditionalPurpose';
		$this->urlGetProductCategoryForSearch = '/GetProductCategoryForSearch';
		$this->urlGetCartmultimerchantenabled = '/HasMultiMerchantCart';
		$this->urlGetDefaultCurrency = '/GetDefaultCurrency';
		$this->urlGetCurrencyExchanges = '/GetCurrencyExchanges';
		$this->urlGetLoginTwoFactor = '/LoginTwoFactor';
		$this->urlGetTwoFactorUniqueNumber = '/GetTwoFactorUniqueNumber';
	}

	public function getLoginTwoFactor($email, $password, $twoFactorAuthCode='',$deviceCodeAuthCode='') {
		$cultureCode = $GLOBALS['bfi_lang'];
		$data = array(
				'email' => BFCHelper::getQuotedString($email),
				'password' => BFCHelper::getQuotedString($password),
				'twoFactorAuthCode' => BFCHelper::getQuotedString($twoFactorAuthCode),
				'$format' => 'json'
		);

		$options = array(
				'path' => $this->urlGetLoginTwoFactor,
				'data' => $data
		);

		if (!empty($deviceCodeAuthCode)) {
			$options['data']['deviceCodeAuthCode'] = BFCHelper::getQuotedString($deviceCodeAuthCode);
		    
		}

		$url = $this->helper->getQuery($options);
	
		$results= "0";
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (isset($res->d->LoginTwoFactor)){
				$results = strval($res->d->LoginTwoFactor);
			}elseif(isset($res->d)){
				$results = strval($res->d);
			}
		}
		$strUser ="";
		
		if (strrpos($results, "-1:{")  === false ) {
			if (strrpos($results, "4:{") === false  ) {
			}else{
				$uniqueNumber = self::GetTwoFactorUniqueNumber($email);
				if(!empty( $uniqueNumber )){
					BFCHelper::SetTwoFactorCookie($uniqueNumber);
					$strUser = stripslashes(substr($results, 2));
					$results = "-1";
				}
			}
			if (strrpos($results, "6:{") === false  ) {
			}else{
				$strUser = stripslashes(substr($results, 2));
				$results = "-1";
			}
		}else{
			$strUser = stripslashes(substr($results, 3));
			$results = "-1";
		}
		if(!empty( $strUser )){
			$currUser = json_decode($strUser);
			BFCHelper::setSession('bfiUser', $currUser, 'bfi-User');
		}
	
		
//		if ($results == 4) {
//		    $uniqueNumber = self::GetTwoFactorUniqueNumber($email);
//			if(!empty( $uniqueNumber )){
//				BFCHelper::SetTwoFactorCookie($uniqueNumber);
//				$results = -1;
//			}
//
//		}	
		return $results;
	}

	public function GetTwoFactorUniqueNumber($email="") {
		$data = array(
				'email' => BFCHelper::getQuotedString($email),
				'$format' => 'json'
		);
		$options = array(
				'path' => $this->urlGetTwoFactorUniqueNumber,
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
	
		$results= "0";
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->GetTwoFactorUniqueNumber)){
				$results = $res->d->GetTwoFactorUniqueNumber;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		}
		return $results;
	}




	public function getCurrencyExchanges() {
		$results = BFCHelper::getSession('getCurrencyExchanges', null , 'com_bookingforconnector');
//		$results=null;
		if ($results==null) {
			$results = $this->getCurrencyExchangesFromService();
			BFCHelper::setSession('getCurrencyExchanges', $results, 'com_bookingforconnector');
		}
		return $results;
	}
	
	public function getCurrencyExchangesFromService() {		
		$options = array(
				'path' => $this->urlGetCurrencyExchanges,
				'data' => array(
					'$format' => 'json',
					'getDefaultOnly' => 'false'
			
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results;
			}elseif(!empty($res->d)){

				$return = $res->d;
			}
		}
		if(!empty($return)){
			$aCurrencyExchanges = array();
			foreach ($return as $key => $CurrencyExchange ) {
				$aCurrencyExchanges[$CurrencyExchange->ToCurrencyCode] = $CurrencyExchange->ConversionValue;
			}
			return $aCurrencyExchanges;
		}
		return $return;
	}

	public function getDefaultCurrency() {
		$results = BFCHelper::getSession('getDefaultCurrency', null , 'com_bookingforconnector');
		if ($results==null) {
			$results = $this->getDefaultCurrencyFromService();
			BFCHelper::setSession('getDefaultCurrency', $results, 'com_bookingforconnector');
		}
		return $results;
	}
	
	public function getDefaultCurrencyFromService() {		
		$options = array(
				'path' => $this->urlGetDefaultCurrency,
				'data' => array(
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results->GetDefaultCurrency;
			}elseif(!empty($res->d)){

				$return = $res->d->GetDefaultCurrency;
			}
		}
		return $return;
	}

	public function getProductCategoryForSearchFromService($language='', $typeId = 1,$merchantid=0) {
		$options = array(
				'path' => $this->urlGetProductCategoryForSearch,
				'data' => array(
					'typeId' => $typeId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);

//		if(!empty( $merchantid )){
//			$options['data']['merchantId'] = $merchantid;
//			
//		}
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results;
			}elseif(!empty($res->d)){

				$return = $res->d;
			}
		}
		return $return;
	}

	public function getProductCategoryForSearch($language='', $typeId = 1,$merchantid=0) {
//		$session = JFactory::getSession();
		$results = BFCHelper::getSession('getProductCategoryForSearch'.$language.$typeId.$merchantid, null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($results==null) {
			$results = $this->getProductCategoryForSearchFromService($language, $typeId,$merchantid);
			BFCHelper::setSession('getProductCategoryForSearch'.$language.$typeId.$merchantid, $results, 'com_bookingforconnector');
		}
		return $results;
	}

	public function getAdditionalPurpose($language='') {
		$results = BFCHelper::getSession('getAdditionalPurpose'.$language, null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($results==null) {
			$results = $this->getAdditionalPurposeFromService($language);
			BFCHelper::setSession('getAdditionalPurpose'.$language, $results, 'com_bookingforconnector');
		}
		return $results;
//		$additionalPurpose = $this->getAdditionalPurposeFromService($language);
//		return $additionalPurpose;
	}

	public function getAdditionalPurposeFromService($language='') {		
		$options = array(
				'path' => $this->urlGetAdditionalPurpose,
				'data' => array(
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
				$return = $res->d->results->GetAdditionalPurpose;
			}elseif(!empty($res->d)){

				$return = $res->d->GetAdditionalPurpose;
			}
		}
		return $return;
	}

	public function getPrivacy($language='') {
		$privacy = BFCHelper::getSession('getPrivacy'.$language, null , 'com_bookingforconnector');
		if ($privacy==null) {
			$privacy = $this->getPrivacyFromService($language);
			BFCHelper::setSession('getPrivacy'.$language, $privacy, 'com_bookingforconnector');
		}
		return $privacy;
	}

	public function getPrivacyFromService($language='') {		
		$options = array(
				'path' => $this->urlGetPrivacy,
				'data' => array(
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
				$return = $res->d->results->GetPrivacy;
			}elseif(!empty($res->d)){

				$return = $res->d->GetPrivacy;
			}
		}
		return $return;
	}

	public function getCartMultimerchantEnabled() {
//		return true; //TODO: da fare
		$cartmultimerchantenabled = BFCHelper::getSession('cartmultimerchantenabled', null , 'com_bookingforconnector');
		if ($cartmultimerchantenabled==null) {
			$cartmultimerchantenabled = $this->getCartmultimerchantenabledFromService();
			BFCHelper::setSession('cartmultimerchantenabled', $cartmultimerchantenabled, 'com_bookingforconnector');
		}
		return $cartmultimerchantenabled;
	}

	public function getCartmultimerchantenabledFromService($language='') {		
		$options = array(
				'path' => $this->urlGetCartmultimerchantenabled,
				'data' => array(
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results->HasMultiMerchantCart;
			}elseif(!empty($res->d)){

				$return = $res->d->HasMultiMerchantCart;
			}
		}
		return $return;
	}
	
	protected function populateState($ordering = NULL, $direction = NULL) {
		
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