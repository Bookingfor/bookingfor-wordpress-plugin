<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'BFI_paypalExpress' ) ) :
class BFI_paypalExpress{
	
	public $alias;
	public $separator;
	public $startSecretKey;
	public $paymentUrl;
	public $divisa = 'EUR';
	public $numord;
	public $email;	
	public $languageId;
	public $importo;	
	public $urlBack;
	public $url;
	public $isdonation;
	public $useproxy;
	public $urlproxy;
		
	public function __construct($config, $order, $language, $urlBack, $url, $debug = false, $donation = false)
	{
		$this->useproxy = COM_BOOKINGFORCONNECTOR_USEPROXY;
		$this->urlproxy = COM_BOOKINGFORCONNECTOR_URLPROXY;
		$paymentData = explode( '|', $config);  /*Username|Password|Signature|Separator */
		$this->Username = $paymentData[0];
		$this->Password = $paymentData[1];
		$this->Signature = $paymentData[2];
		$this->separator = $paymentData[3];
		$this->divisa = 'EUR';
		$this->numord = sprintf('B%s%s%s%s', rand(1, 9999) . $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->languageId = $this->getLanguage($language);
		$this->importo = (float)$order->DepositAmount;
		
		$this->paymentUrl = 'https://www.paypal.com/cgi-bin/webscr';
		$this->paymentUrlAPI = 'https://api-3t.paypal.com/nvp';
		$this->returnurl = $url;
		$this->urlBack = $urlBack;
		$this->method = "SetExpressCheckout";
		$this->version = "109.0";
		$this->paymentaction = "Sale";

		if ($debug){
			$this->paymentUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this->paymentUrlAPI = 'https://api-3t.sandbox.paypal.com/nvp';
		}
	}
	
	public function getLanguage($language) {
		
		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'IT';
				break;
			case 'en':
				return 'US';
				break;
			case 'de':
				return 'DE';			
				break;
			case 'es':
				return 'ES';
				break;
			case 'fr':
				return 'FR';
				break;
			default:
				return 'US';
				break;
		}
	}

	public function getUrl(){
		$nvp = array(
			'PAYMENTREQUEST_0_AMT'				=> number_format($this->importo, 2, '.', ''),
			'PAYMENTREQUEST_0_ITEMAMT'			=> number_format($this->importo, 2, '.', ''),
			'PAYMENTREQUEST_0_CURRENCYCODE'		=> $this->divisa ,
			'PAYMENTREQUEST_0_PAYMENTREQUESTID'	=> $this->numord ,
			'PAYMENTREQUEST_0_PAYMENTACTION'	=> 'Sale',
			'L_PAYMENTREQUEST_0_NAME0'			=> $this->numord ,
			'L_PAYMENTREQUEST_0_QTY0'			=> 1,
			'L_PAYMENTREQUEST_0_AMT0'			=> number_format($this->importo, 2, '.', ''),
			'LOCALECODE'						=> $this->languageId ,
			'EMAIL'								=> $this->email ,
			'RETURNURL'							=> $this->returnurl,
			'CANCELURL'							=> $this->urlBack,
			'METHOD'							=> 'SetExpressCheckout',
			'VERSION'							=> $this->version ,
			'PWD'								=>  $this->Password,
			'USER'								=> $this->Username,
			'SIGNATURE'							=> $this->Signature
		);

		$curl = curl_init();

			if($this->useproxy ==1 && !empty($this->urlproxy)){
				curl_setopt($curl, CURLOPT_PROXY, $this->urlproxy);
			}
		curl_setopt( $curl , CURLOPT_URL , $this->paymentUrlAPI );
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $curl , CURLOPT_POST , 1 );
		curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

		$response = urldecode( curl_exec( $curl ) );

		curl_close( $curl );

		$responseNvp = array();

		if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
			foreach ( $matches[ 'name' ] as $offset => $name ) {
				$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
			}
		}

		if ( isset( $responseNvp[ 'ACK' ] ) && $responseNvp[ 'ACK' ] == 'Success' ) {
			$paypalURL = $this->paymentUrl ;
			$query = array(
				'cmd'	=> '_express-checkout',
				'token'	=> $responseNvp[ 'TOKEN' ]
			);
			wp_redirect($paypalURL . '?' . http_build_query( $query ));
			exit;
			//header( 'Location: ' . $paypalURL . '?' . http_build_query( $query ) );
		} else {
			echo 'error';	
			echo "<pre>";
			echo $response;
			echo "</pre>";		
		}		
	}
}
endif;

if ( ! class_exists( 'BFI_paypalExpressProcessor' ) ) :
class BFI_paypalExpressProcessor extends BFI_Payment{
	public $data;
	public $order;
	public $useproxy;
	public $urlproxy;

	public function __construct($order = false,$url = "", $debug = false, $data = [])
	{
		$this->order = $order;
		$this->data = $data;
		$this->useproxy = COM_BOOKINGFORCONNECTOR_USEPROXY;
		$this->urlproxy = COM_BOOKINGFORCONNECTOR_URLPROXY;
	}
	
	public function getBankId($param = NULL) {
		$bankId = BFCHelper::getVar('trackid','');
		return $bankId ;
	}
	
	public function getAmount($param = NULL) {
		return number_format($this->order->DepositAmount, 2, '.', '');
	}

	public function getResult($param = NULL,$debug = false) {
		$this->paymentUrl = 'https://www.paypal.com/cgi-bin/webscr';
		$this->paymentUrlAPI = 'https://api-3t.paypal.com/nvp';
		$this->method = "DoExpressCheckoutPayment";
		$this->version = "109.0";
		$this->paymentaction = "Sale";
		$this->divisa = 'EUR';

		if ($debug){
			$this->paymentUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this->paymentUrlAPI = 'https://api-3t.sandbox.paypal.com/nvp';
		}
		$this->Username = $this->data[0];
		$this->Password = $this->data[1];
		$this->Signature = $this->data[2];
		$this->separator = $this->data[3];
		if(isset($_GET["token"]) && isset($_GET["PayerID"])){
		$nvp = array(
			'PAYMENTREQUEST_0_AMT'				=> number_format($this->order->DepositAmount, 2, '.', ''),
			'PAYMENTREQUEST_0_ITEMAMT'				=> number_format($this->order->DepositAmount, 2, '.', ''),
			'PAYMENTREQUEST_0_CURRENCYCODE'		 => $this->divisa ,
			'PAYMENTREQUEST_0_PAYMENTREQUESTID'		 => $this->order->OrderId ,
			'PAYMENTREQUEST_0_PAYMENTACTION'	 => 'Sale',
			'TOKEN'							=> $_GET["token"] ,
			'PAYERID'							=> $_GET["PayerID"] ,
			'METHOD'							=> $this->method,
			'VERSION'							=> $this->version ,
			'PWD'								=>  $this->Password,
			'USER'								=> $this->Username,
			'SIGNATURE'							=> $this->Signature
		);

		$curl = curl_init();

			if($this->useproxy ==1 && !empty($this->urlproxy)){
				curl_setopt($curl, CURLOPT_PROXY, $this->urlproxy);
			}
		curl_setopt( $curl , CURLOPT_URL , $this->paymentUrlAPI );
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $curl , CURLOPT_POST , 1 );
		curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

		$response = urldecode( curl_exec( $curl ) );

		curl_close( $curl );

		$responseNvp = array();

		if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
			foreach ( $matches[ 'name' ] as $offset => $name ) {
				$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
			}
		}

		if ( isset( $responseNvp[ 'ACK' ] ) && ($responseNvp[ 'ACK' ] == 'Success' || $responseNvp[ 'ACK' ] == 'SuccessWithWarning') ) {
			if ( isset( $responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] ) && ($responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] == 'Completed' ||$responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] == 'Pending' ) ) {
				return true;
			} else {
				return false;
			}		
		} else {
			return false;
	    }		
     }
	}

	public function responseRedir($msg = '',$result=false)
	{
		if (empty($msg)){
			$msg = "0";
		}
		global $base_url;

		$url = get_site_url().'/payment/?actionmode=orderpaid&payedOrderId='.$msg.'&result='.$result;
		if(!$result){
			  $url = get_site_url().'/payment/?actionmode=cancel&payedOrderId='.$msg.'&result='.$result;
		}
		header( 'Location: ' . $url  );
		exit();
	
	}
}
endif;