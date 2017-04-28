<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'BFI_wspayform' ) ) :
class BFI_wspayform{
	/*
	PAyment only in Kune
	
	*/
	public $shopID;
	public $separator;
	public $SecretKey;
	public $paymentUrl;
	public $numord;
	public $email;	
	public $languageId;
	public $importo;	
	public $importoForMac;	
	public $urlBack;
	public $url;
	public $suffixOrder;
	public $mac;
	public $redirectUrl;
	public $defaultcurrencyKn;
		
	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder, $overrideAmount=0, $debug = FALSE)
	{
		$paymentData = explode( '|', $config);  /*ShopID|Separator|SecretKey|PaymentUrl */
		$this->defaultcurrencyKn = 191;
		$this->shopID = $paymentData[0];
		$this->separator = $paymentData[1];
		$this->SecretKey = $paymentData[2];
		$this->paymentUrl = $paymentData[3];
		$this->numord = sprintf('B%s%s%s%s', rand(1, 9999) . $this->separator, $order->ExternalId, $this->separator,$order->OrderId);

		$defaultcurrency = bfi_get_defaultCurrency();
		if($defaultcurrency!=$this->defaultcurrencyKn){
			//try to convert
			$currencyExchanges = BFCHelper::getCurrencyExchanges();
			if (isset($currencyExchanges[$this->defaultcurrencyKn]) ) {
				$order->DepositAmount = $order->DepositAmount *$currencyExchanges[$this->defaultcurrencyKn];
				if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
					$overrideAmount= $overrideAmount*$currencyExchanges[$this->defaultcurrencyKn];
				}
			}
		}
		
		$this->importo =  number_format(($order->DepositAmount ), 2, ',', '');
//		$this->importoForMac =  intval($order->DepositAmount * 100 ) ;
		$this->importoForMac =  intval(number_format(($order->DepositAmount ), 2, '', '') ) ;
				
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->numord .= $this->separator . "R" . $suffixOrder;
			$this->importo =  number_format(($overrideAmount), 2, ',', '');
			$this->importoForMac =  intval($overrideAmount * 100) ;
		}
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->languageId = $this->getLanguage($language);
		$this->url = $url;
		$this->urlBack = $urlBack;
	
			
		if ($debug){
			$this->shopID = 'MYSHOP';
			$this->numord = 78;
			$this->importo =  '17,00';
			$this->importoForMac = '1700';
			$this->SecretKey = '3DfEO2B5Jjm4VC1Q3vEh';
		}
		
		$this->mac = $this->getMac();
//		$this->redirectUrl=getUrl();
	}
	
	public function getLanguage($language) {
		
		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'IT';
				break;
			case 'en':
				return 'EN';
				break;
			case 'de':
				return 'DE';			
				break;
			case 'hr':
				return 'HR';
				break;
			default:
				return 'EN';
				break;
		}
	}

	public function getMac(){
		
		$strMac =  $this->shopID.$this->SecretKey.$this->numord.$this->SecretKey.$this->importoForMac.$this->SecretKey;
				
		$calculatedMac = md5($strMac);
		
		return $calculatedMac;
	}
}
endif;

if ( ! class_exists( 'BFI_wspayformProcessor' ) ) :
class BFI_wspayformProcessor extends BFI_Payment{
	public $data;
	public $order;
	public $defaultcurrencyKn;

	public function __construct($order = false,$url = false, $debug = false)
	{
		$this->order = $order;		
		$this->defaultcurrencyKn = 191;
	}
	
	public function getResult($param = NULL) {
		$esito = BFCHelper::getVar('Success','');
		return (strtolower($esito)=='1');
		//return parent::getResult($param);;
	}
	public function getBankId($param = NULL) {
		$bankId = BFCHelper::getVar('ShoppingCartID','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	public function getAmount($param = NULL) {
		$amount = BFCHelper::getFloat('Amount',0);
		//converto in euro l'importo pagato
		$defaultcurrency = bfi_get_defaultCurrency();
		if($defaultcurrency!=191){
			//try to convert
			$currencyExchanges = BFCHelper::getCurrencyExchanges();
			if (isset($currencyExchanges[$this->defaultcurrencyKn]) ) {
				$amount = $amount / $currencyExchanges[$this->defaultcurrencyKn];
			}
		}

//		$amount =  number_format(($amount / COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}
	public function responseRedir($msg = '',$result='')
	{
		if (empty($msg)){
			$msg = "0";
		}
		if (empty($result)){
			$result = "0";
		}

//		$uri                    = JURI::getInstance();
//		$urlBase = $uri->toString(array('scheme', 'host', 'port'));
//		$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=setefi&payedOrderId=' . $msg . '&result=' . $result);
//	
//		echo 'redirect=' . $url;
//		jexit();
//		global $base_url;
//
//		$url = get_site_url().'/payment/?actionmode=setefi&payedOrderId='.$msg.'&result='.$result;

		$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
		$url_cart_page = get_permalink( $cartdetails_page->ID );

		$redirect = $url_cart_page .'/'. _x('thanks', 'Page slug', 'bfi' );
		$redirecterror = $url_cart_page .'/'. _x('errors', 'Page slug', 'bfi' );

		if($msg=="0" || $result=="0"){
			$redirect = $redirecterror;
		}else{
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}
			$act = "OrderPaid";
			$order = $this->order;
			$redirect = $redirect . 'act=' . $act  
			 . '&orderid=' . $order->OrderId 
			 . '&merchantid=' . $order->MerchantId 
			 . '&OrderType=' . $order->OrderType 
			 . '&OrderTypeId=' . $order->OrderTypeId 
			 . '&totalamount=' . ($order->TotalAmount *100)
//			 . '&startDate=' . $startDate->format('Y-m-d')
//			 . '&endDate=' . $endDate->format('Y-m-d')
//			 . '&numAdults=' . $numAdults
			;
		
		}
		header( 'Location: ' . $redirect  );
		exit();
	
	}

}
endif;


/*================= END WSPAYFORM ==================================*/
