<?php
/**
 * Contains the query functions for Bookingfor which alter the front-end post queries and loops
 *
 * @class 		BFI_Controller
 * @version             2.0.5
 * @package		
 * @category	        Class
 * @author 		Bookingfor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Controller' ) ) :
/**
 * BFI_Controller Class.
 */
class BFI_Controller {

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 * @access public
	 */
	private $formlabel = null;
	public function __construct() {
		$this->formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	}
	
	public function handle_request(){ 
 		global $wp; 
		
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] :null ;

		if(!empty($task)){
			if (method_exists($this, $task)){
				$message = $this->$task();
				$simple = isset($_REQUEST['simple']) ? $_REQUEST['simple'] :null ;
				if(!empty($simple)){
					$this->send_text_response($message);  
				}else{
					$this->send_json_response($message);  
				}

			}
		}else{
 			$this->send_response('Method not allowed'); 
		}
 	} 

	protected function send_response($msg){ 
 		$response['message'] = $msg; 
// 		header('content-type: application/json; charset=utf-8'); 
 	    echo json_encode($response)."\n"; 
 	    exit; 
 	} 
	protected function send_json_response($msg){ 
// 		header('content-type: application/json; charset=utf-8'); 
 	    echo $msg."\n"; 
 	    exit; 
 	} 
	protected function send_text_response($msg){ 
// 		header('content-type: text/plain; charset=utf-8'); 
 	    echo $msg."\n"; 
 	    exit; 
 	} 

	protected function searchjson(){
		bfi_setSessionFromSubmittedData();
		$model = new BookingForConnectorModelSearch;
		$items = $model->getItems(true,true);
		echo $items;	
	}
	protected function searchonselljson(){
		$model = new BookingForConnectorModelSearchOnSell;
		$items = $model->getItems(true,true);
		echo $items;	
	}
	
	protected function getMerchantGroups(){
		$return = BFCHelper::getTags("","1"); //getTags
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo json_encode($return);      

	}

	protected function getResourceGroups(){
		$return = BFCHelper::getTags("","4"); //getTags
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo json_encode($return);      

	}

	protected function getDiscountDetails(){
		$ids=$_REQUEST['discountId'];
		$language=$_REQUEST['language'];
		$return = BFCHelper::getDiscountDetails($ids,$language);
		echo $return;	
	}

	protected function GetPhoneByMerchantId(){
		$merchantId = isset($_REQUEST['merchantid']) ? $_REQUEST['merchantid'] :0 ;
		$number = isset($_REQUEST['n']) ? $_REQUEST['n'] : '' ;
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetPhoneByMerchantId($merchantId,$language,$number);
		echo $return;      
	}
	
	function GetServicesByIds(){
		$listsId=isset($_REQUEST['ids']) ? $_REQUEST['ids'] : '' ;  
		$language= isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetServicesByIds($listsId,$language);
		echo json_encode($return);      	
	}
	function GetResourcesByIds(){
		$listsId=isset($_REQUEST['resourcesId']) ? $_REQUEST['resourcesId'] : '' ;  
		$language= isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetResourcesByIds($listsId,$language);
		echo $return;      	
	}
	function GetMerchantsByIds(){
		$listsId=BFCHelper::getVar('merchantsId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getMerchantsByIds($listsId,$language);
		echo $return;      
	
	}
	function GetCondominiumsByIds(){
		$listsId=BFCHelper::getVar('ids');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetCondominiumsByIds($listsId,$language);
		echo $return;      
	
	}
	
	function GetResourcesOnSellByIds(){
		$listsId=BFCHelper::getVar('resourcesId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetResourcesOnSellByIds($listsId,$language);
		echo $return;      	
	}

	function sendRating(){
		global $user;
		$redirect= $_POST['Redirect'];
		$redirecterror =  $_POST['Redirecterror']; 
		$name= $_POST['name'];
		$city= $_POST['city'];
		$typologyid= $_POST['typologyid'];
		$nation= $_POST['nation']; // BFCHelper::getVar('nation');
		$email= $_POST['email'];
		$value1=$_POST['hfvalue1'];
		$value2= $_POST['hfvalue2'];
		$value3= $_POST['hfvalue3'];
		$value4= $_POST['hfvalue4'];
		$value5= $_POST['hfvalue5'];
		$totale= $_POST['hftotale'];
		$pregi= $_POST['pregi'];
		$difetti= $_POST['difetti'];
		$merchantId= $_POST['merchantid'];
		$label= $_POST['label'];
		$user = $user;
		$cultureCode = $GLOBALS['bfi_lang'];
		$userId=null;
		if (!empty($user) && $user->uid != 0) {
			$userId=$user->uid ;
		}
		$checkin= $_POST['checkin'];
		$resourceId= $_POST['resourceId'];
		$hashorder= $_POST['hashorder'];
		$orderId = null;
		if (empty($resourceId)){
			$resourceId = null;
		}
		if (!empty($hashorder)){
			$orderId = BFCHelper::decrypt($hashorder);
			if (!is_numeric($orderId))
			{
				$orderId = null;
			}
		}

		$return = BFCHelper::setRating($name, $city, $typologyid, $email, $nation, $merchantId,$value1, $value2, $value3, $value4, $value5, $totale, $pregi, $difetti, $userId, $cultureCode, $checkin, $resourceId, $orderId, $label);	
		if ($return < 1){
			$return ="";
			$redirect = $redirecterror;
		}
		if ($return >0 && !empty($redirect)){
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}
			$redirect = $redirect . 'act=Rating';
		}		
//		if ($return < 1){
//			set_transient( 'temporary_message', __( 'There was some issue posting your review. Please try back later.' ), 60*60*12 );
//			$redirect = $redirecterror;
//		}
//		else {
//			set_transient( 'temporary_message', __( 'Your review was succesfully posted.' ), 60*60*12 );		
//		}
		wp_redirect($redirect);
		exit;
	}

	function sendOrder(){
		$formData = $_POST['form'];
		if(empty($formData)){
		}
	BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
	BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');

// 		if(!is_user_logged_in()) {
//                  $email_address = $formData['Email'];
//                  $password = $formData['Password'];
//                  if( null == username_exists( $email_address ) ) {
//                    $user_id = wp_create_user( $email_address, $password, $email_address );
//                    wp_update_user(
//                      array(
//                        'ID'          =>    $user_id,
//                        'nickname'    =>    $email_address
//                      )
//                    );
//                    $user = new WP_User( $user_id );
//                    wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );
//                    $creds = array( 'user_login' =>  $email_address, 'user_password' => $password, 'remember' => 0 );
//                    $user = wp_signon( $creds, false );
//                    if ( is_wp_error($user) ): echo $user->get_error_message(); endif;
//                    wp_set_current_user($user->ID);
//                  }
//                }
		$customer = BFCHelper::getCustomerData($formData);

		$suggestedStay = json_decode(stripslashes($formData['staysuggested']));
		$req = json_decode(stripslashes($formData['stayrequest']), true);

		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];

		$isgateway = $formData['isgateway'];

		$otherData = "paxages:". str_replace("]", "" ,str_replace("[", "" , $req['paxages'] ))
					."|"."checkin_eta_hour:".$formData['checkin_eta_hour'];
		$ccdata = null;
		if (BFCHelper::canAcquireCCData($formData)) { 
			$ccdata = json_encode(BFCHelper::getCCardData($formData));
			$ccdata = BFCHelper::encrypt($ccdata);
			}
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, $ccdata);
		$orderData['pricetype'] = $req['pricetype'];
		$orderData['label'] = $formData['label'];
		$orderData['checkin_eta_hour'] = $formData['checkin_eta_hour'];

		$processOrder = null;
		if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$processOrder=false;
		}

		$order = BFCHelper::setOrder(
                $orderData['customerData'], 
                $orderData['suggestedStay'], 
                $orderData['creditCardData'], 
                $orderData['otherNoteData'], 
                $orderData['merchantId'], 
                $orderData['orderType'], 
                $orderData['userNotes'], 
                $orderData['label'], 
                $orderData['cultureCode'], 
				$processOrder,
				$orderData['pricetype']
                );

		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		if (!empty($order)){
			if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$payment_page = get_post( bfi_get_page_id( 'payment' ) );
			$url_payment_page = get_permalink( $payment_page->ID );

//			$redirect = $url_payment_page .'?orderId=' . $order->OrderId;
			$redirect = $url_payment_page .'/' . $order->OrderId;

			}else{
				$numAdults = 0;
				$persons= explode("|", $suggestedStay->Paxes);
				foreach($persons as $person) {
					$totper = explode(":", $person);
					$numAdults += (int)$totper[1];
				}

				$act = "OrderResource";
				if(!empty($order->OrderType) && strtolower($order->OrderType) =="b"){
					$act = "QuoteRequest";
				}

				$startDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->StartDate,'Y-m-d'));
				$endDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->EndDate,'Y-m-d'));
				
				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}

				$redirect = $redirect . 'act=' . $act  
				 . '&orderid=' . $order->OrderId 
				 . '&merchantid=' . $order->MerchantId 
				 . '&OrderType=' . $order->OrderType 
				 . '&OrderTypeId=' . $order->OrderTypeId 
				 . '&totalamount=' . ($order->TotalAmount *100)
				 . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				 . '&numAdults=' . $numAdults
				;
			}
//			$urlredirpayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
//			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
		}
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
//		$app->close();
		wp_redirect($redirect);
		exit;

	}

	function sendOffer(){
		$formData = $_POST['form'];

		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;

		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$paxages = BFCHelper::getStayParam('paxages');

		// create otherData (string)
		$otherData = 'offerId:'.$formData['offerId']."|"		
					."persone:".$formData['persons']."|"
					."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione')."|"
					."paxages:". implode(',',$paxages)."|"
					."checkin_eta_hour:".$formData['checkin_eta_hour'];
		
		// create SuggestedStay
		$startDate = null;
		$endDate = null;
		if (!empty($formData['CheckIn']) && !empty($formData['CheckOut'])) {
				$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn']);
				$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut']);
					$sStay = array(
								'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
								'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO'),
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "CheckIn:" . DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d') . "|" ."CheckOut:" . DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d') . "|" . "UnitId:" . $formData['resourceId'];
		}else{
			if (!empty($formData['resourceId']))  {
					$sStay = array(
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "UnitId:" . $formData['resourceId'];
				}
		}
		
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;



		$return = BFCHelper::setOrder(
					$orderData['customerData'], 
					$orderData['suggestedStay'], 
					$orderData['creditCardData'], 
					$orderData['otherNoteData'],
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'], 
					true,
					null
					);	

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}
		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactPackage&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				;
		}
//		echo json_encode($return);      
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
		wp_redirect($redirect);
		exit;

	}

	function getLocationZone(){
		$model = new BookingForConnectorModelMerchants;
		$items = $model->getItemsJson(true);
		echo $items;	
	}

	function listDate(){
		$resourceId = $_REQUEST['resourceId'];
		$ci = $_REQUEST['checkin'];
		$checkin = DateTime::createFromFormat('Ymd',$ci);
		$return = BFCHelper::getCheckOutDates($resourceId ,$checkin);
		echo $return;      
	}

	function getCompleteRateplansStay(){
//		$language=$_REQUEST['language'];
		$resourceId = isset($_REQUEST['resourceId'])?$_REQUEST['resourceId']:null;
		$ratePlanId=isset($_REQUEST['pricetype'])?$_REQUEST['pricetype']:null;
		$variationPlanId=isset($_REQUEST['variationPlanId'])?$_REQUEST['variationPlanId']:null;
		$selectablePrices=BFCHelper::getStayParam('extras');
		
		$pricetype=isset($_REQUEST['pricetype'])?$_REQUEST['pricetype']:null;

		$selectablePrices=isset($_REQUEST['selectableprices'])?$_REQUEST['selectableprices']:$selectablePrices;
		
		$availabilitytype=isset($_REQUEST['availabilitytype'])?$_REQUEST['availabilitytype']:null; 

		$checkIn =  BFCHelper::getStayParam('checkin',null);
		$duration  =  BFCHelper::getStayParam('duration');
		if ($availabilitytype == 0 || $availabilitytype ==1 ) // product TimePeriod
		{
			$checkIn->setTime(0,0,0);
		}
		if ($availabilitytype ==2 ) // product TimePeriod
		{
			$duration = isset($_REQUEST['duration'])?$_REQUEST['duration']:null; 
			$checkIn = DateTime::createFromFormat("YmdHis", $_REQUEST['CheckInTime']);
		}
		
		
		if(!isset($duration)){
			$duration =  BFCHelper::$defaultDaysSpan;
		}
		$packages  =  BFCHelper::getStayParam('packages');
		$paxages =  BFCHelper::getStayParam('paxages',null);
		$return = null;
		$return = BFCHelper::GetCompleteRatePlansStayWP($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId,null,null);
		
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
	}

	function getListCheckInDayPerTimes(){
//		$language=$_REQUEST['language'];
		$resourceId = isset($_REQUEST['resourceId'])?$_REQUEST['resourceId']:null;
		$fromDate=isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:null;
		$limitTotDays=isset($_REQUEST['limitTotDays'])?$_REQUEST['limitTotDays']:null;

		$return = null;
		$return = BFCHelper::GetListCheckInDayPerTimes($resourceId,$fromDate,$limitTotDays);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
	}

	function updateCCdataOrder(){
		$formData = $_POST['form'];
		if(empty($formData)){
		}
		$ccdata = null;
		$ccdata = json_encode(BFCHelper::getCCardData($formData));
		$ccdata = BFCHelper::encrypt($ccdata);

		$orderId = BFCHelper::getVar('OrderId');
								
		$order = BFCHelper::updateCCdata(
				$orderId,        
				$ccdata, 
				null
                );								
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		wp_redirect($redirect);
		exit;
	}

	function sendOnSellrequest(){
		$formData = $_POST['form'];

		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$otherData = array();
		if (!empty($formData['resourceId']))  {
				$sStay = array(
							'UnitId' => $formData['resourceId']
						);

				$suggestedStay = new stdClass(); 
				foreach ($sStay as $key => $value) 
				{ 
					$suggestedStay->$key = $value; 
				}
				$otherData["UnitId:"] = "UnitId:" . $formData['resourceId'];
			}
		if (!empty($formData['pageurl']))  {
				$otherData["pageurl:"] = "pageurl:" . $formData['pageurl'];
		}
		if (!empty($formData['title']))  {
				$otherData["title:"] = "title:" . $formData['title'];
		}
		if (!empty($formData['resourceId']))  {
				$otherData["onsellunitid:"] = "onsellunitid:" . $formData['resourceId'];
		}
		if (!empty($formData['accettazione']))  {
				$otherData["accettazione:"] = "accettazione:" . BFCHelper::getOptionsFromSelect($formData,'accettazione');
		}
		
				

		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, implode("|",$otherData), null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}
		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactSale&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				;
		}
//		echo json_encode($return);      
//		$app = JFactory::getApplication();
//		if (empty($redirect)){
//			echo json_encode($return);      
//		}else{
//			$app->redirect($redirect, false);
//		}
//		$app->close();
		wp_redirect($redirect);
		exit;

	}
	function sendInforequest(){
		$formData = $_POST['form'];
//		JPluginHelper::importPlugin('captcha');
//		$dispatcher = JDispatcher::getInstance();
//		$result = $dispatcher->trigger('onCheckAnswer',$formData['recaptcha_response_field']);
//		if(!$result[0]){
//			die('Invalid Captcha Code');
//		}else{


		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		// create otherData (string)
				$otherData = "persone:".BFCHelper::getOptionsFromSelect($formData,'Totpersons')."|"
					."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione');
		// create SuggestedStay
		$startDate = null;
		$endDate = null;
				if (!empty($formData['CheckIn']) && !empty($formData['CheckOut'])) {
				$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn']);
				$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut']);
					$sStay = array(
								'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
								'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO'),
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "CheckIn:" . DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d') . "|" ."CheckOut:" . DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d') . "|" . "UnitId:" . $formData['resourceId'];
				}else{
			if (!empty($formData['resourceId']))  {
					$sStay = array(
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "UnitId:" . $formData['resourceId'];
				}
			}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	
//}

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}else{
			
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}

			$redirect = $redirect . 'act=ContactResource&orderid=' . $return->OrderId 
					 . '&merchantid=' . $return->MerchantId 
					 . '&OrderType=' . $return->OrderType 
					 . '&OrderTypeId=' . $return->OrderTypeId
					 . '&RequestType=' . $return->RequestType
					 . '&numAdults=' . $numAdults
					;
				if (!empty($startDate)){
					$redirect = $redirect . '&startDate=' . $startDate->format('Y-m-d')
					 . '&endDate=' . $endDate->format('Y-m-d')
					;
				}
		}
//		echo json_encode($return);      
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
//		$app->close();
		wp_redirect($redirect);
		exit;

	}
	function sendContact(){
		$formData = $_POST['form'];
//		$checkrecaptcha = true;
//		JPluginHelper::importPlugin('captcha');
//		$dispatcher = JDispatcher::getInstance();
//		if (!empty($formData['recaptcha_response_field'])) {
//			$result = $dispatcher->trigger('onCheckAnswer',$formData['recaptcha_response_field']);
//			if(!$result[0]){
//				$checkrecaptcha = false;
//				//die('Invalid Captcha Code');
//			}
//		}
//		if($checkrecaptcha){
		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		// create otherData (string)
		$numAdults = BFCHelper::getOptionsFromSelect($formData,'Totpersons');
		
		$otherData = "persone:".$numAdults."|"
			."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione');
		// create SuggestedStay
		$startDate = null;
		$endDate = null;

		if ($formData['CheckIn'] != null && $formData['CheckOut'] != null) {
			
			$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn']);
			$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut']);
			
			$sStay = array(
						'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
						'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO')
					);

			$suggestedStay = new stdClass(); 
			foreach ($sStay as $key => $value) 
			{ 
				$suggestedStay->$key = $value; 
			}
			$otherData .= "|" . "CheckIn:" . $startDate->format('Y-m-d') ."|" ."CheckOut:" . $endDate->format('Y-m-d');
		}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	
		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}

		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactMerchant&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				 . '&numAdults=' . $numAdults
				;
			if (!empty($startDate)){
				$redirect = $redirect . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				;
			}
		}

//		echo json_encode($return);      
//		$app = JFactory::getApplication();
////		$app->redirect($redirect, false);
//		if (empty($redirect)){
//			echo json_encode($return);      
//		}else{
//			$app->redirect($redirect, false);
//		}
//		$app->close();
//		}
		wp_redirect($redirect);
		exit;
	}

	//new send orders...
	function sendOrders(){
		$formData = $_POST['form'];
		if(empty($formData)){
		}
		
//		//Creazione utente se non esistente
//		if(!is_user_logged_in()) {
//		  $email_address = $formData['Email'];
//		  $password = $formData['Password'];
//		  if( null == username_exists( $email_address ) ) {
//			$user_id = wp_create_user( $email_address, $password, $email_address );
//			wp_update_user(
//			  array(
//				'ID'          =>    $user_id,
//				'nickname'    =>    $email_address
//			  )
//			);
//			$user = new WP_User( $user_id );
//			wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );
//			$creds = array( 'user_login' =>  $email_address, 'user_password' => $password, 'remember' => 0 );
//			$user = wp_signon( $creds, false );
//			if ( is_wp_error($user) ): echo $user->get_error_message(); endif;
//			wp_set_current_user($user->ID);
//		  }
//		}

		$customer = BFCHelper::getCustomerData($formData);

		$userNotes = $formData['note'];
		$cultureCode = $formData['cultureCode'];
		$merchantId = $formData['merchantId'];
		$orderType = $formData['orderType'];
		$label = $formData['label'];
		$OrderJson = $formData['hdnOrderData'];
		$bookingTypeSelected = $formData['bookingtypeselected'];

		$suggestedStays =  BFCHelper::CreateOrder($OrderJson,$cultureCode,$bookingTypeSelected);

		$listCartorderid = array();
		// recupero tutti i cartorderid per la cancellazione del carrello
			$orderModel = json_decode(stripslashes($OrderJson));
            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 )
            {
                foreach ($orderModel->Resources as $resource)
                {
					if(!empty($resource->CartOrderId)){
						$listCartorderid[] = $resource->CartOrderId;
					}
				}
			}
		$listCartorderidstr = implode(",",$listCartorderid);
		
//		$suggestedStay = json_decode(stripslashes($formData['staysuggested']));
//		$req = json_decode(stripslashes($formData['stayrequest']), true);

		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$isgateway = $formData['isgateway'];


//		$otherData = "paxages:". str_replace("]", "" ,str_replace("[", "" , $req['paxages'] ))
//					."|"."checkin_eta_hour:".$formData['checkin_eta_hour'];
		$otherData = "checkin_eta_hour:".$formData['checkin_eta_hour'];
//		$customerDatas = array($customerData);

		$ccdata = null;
		if (BFCHelper::canAcquireCCData($formData)) { 
			$ccdata = json_encode(BFCHelper::getCCardData($formData));
			$ccdata = BFCHelper::encrypt($ccdata);
			}

		$orderData = array(
				'customerData' =>  array($customer),
				'suggestedStay' =>$suggestedStays,
				'creditCardData' => $ccdata,
				'otherNoteData' => $otherData,
				'merchantId' => $merchantId,
				'orderType' => $orderType,
				'userNotes' => $userNotes,
				'label' => $label,
				'cultureCode' => $cultureCode
				);

//		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, $ccdata);
		$orderData['pricetype'] = "";
		if(isset($formData['pricetype'])){
			$orderData['pricetype'] = $formData['pricetype'];
		}
		$orderData['label'] = $formData['label'];
		$orderData['checkin_eta_hour'] = $formData['checkin_eta_hour'];
		$orderData['merchantBookingTypeId'] = $formData['bookingtypeselected'];
		$orderData['policyId'] = $formData['policyId'];

		$processOrder = null;
		if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$processOrder=false;
		}


		$order = BFCHelper::setOrder(
                $orderData['customerData'], 
                $orderData['suggestedStay'], 
                $orderData['creditCardData'], 
                $orderData['otherNoteData'], 
                $orderData['merchantId'], 
                $orderData['orderType'], 
                $orderData['userNotes'], 
                $orderData['label'], 
                $orderData['cultureCode'], 
				$processOrder,
				$orderData['pricetype'],
				$orderData['merchantBookingTypeId'],
				$orderData['policyId']
                );

		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		if (!empty($order)){

			// cancello il carrello
			BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
			BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
			if(!empty($listCartorderidstr)){
				$tmpUserId = bfi_get_userId();
				$model = new BookingForConnectorModelOrders;
				$currCart = $model->DeleteFromCartByExternalUser($tmpUserId, $cultureCode, $listCartorderidstr);
			}	

			
			if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$payment_page = get_post( bfi_get_page_id( 'payment' ) );
			$url_payment_page = get_permalink( $payment_page->ID );

//			$redirect = $url_payment_page .'?orderId=' . $order->OrderId;
			$redirect = $url_payment_page .'/' . $order->OrderId;

			}else{
				$numAdults = 0;
				if(isset($suggestedStays->Paxes)){
					$persons= explode("|", $suggestedStays->Paxes);
					foreach($persons as $person) {
						$totper = explode(":", $person);
						$numAdults += (int)$totper[1];
					}
				}

				$act = "OrderResource";
				if(!empty($order->OrderType) && strtolower($order->OrderType) =="b"){
					$act = "QuoteRequest";
				}

				$startDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->StartDate,'Y-m-d'));
				$endDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->EndDate,'Y-m-d'));
				
				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}

				$redirect = $redirect . 'act=' . $act  
				 . '&orderid=' . $order->OrderId 
				 . '&merchantid=' . $order->MerchantId 
				 . '&OrderType=' . $order->OrderType 
				 . '&OrderTypeId=' . $order->OrderTypeId 
				 . '&totalamount=' . ($order->TotalAmount *100)
				 . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				 . (!empty($numAdults)?'&numAdults=' . $numAdults:"")
				;
			}
//			$urlredirpayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
//			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
		}
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
//		$app->close();
		wp_redirect($redirect);
		exit;

	}
	
	function addToCart(){
		//clear session data from request
		BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
		BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
		$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = null;
		if(!empty($OrderJson)){
//			$recalculateOrder=BFCHelper::getVar("recalculateOrder");
//			if ($recalculateOrder == "1") {
//				$bookingType = stripslashes(BFCHelper::getVar("hdnBookingType"));
//				$currorder = BFCHelper::calculateOrder($OrderJson,$language,$bookingType);
//				$currorder->SearchModel->FromDate = $currorder->SearchModel->FromDate->format('d/m/Y');
//				$currorder->SearchModel->ToDate = $currorder->SearchModel->ToDate->format('d/m/Y');
//				$OrderJson = stripslashes(json_encode( $currorder ));
//			}			
			$tmpUserId = bfi_get_userId();
			$model = new BookingForConnectorModelOrders;
			$currCart = $model->AddToCartByExternalUser($tmpUserId, $language, $OrderJson);
			if(!empty($currCart)){
				$return = json_encode($currCart);
			}
		}
		echo $return;      
	}
	function DeleteFromCart(){
		$return = null;
		$CartOrderId = stripslashes(BFCHelper::getVar("bfi_CartOrderId"));
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
		$url_cart_page = get_permalink( $cartdetails_page->ID );
		$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
		if($usessl){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}

		if(!empty($CartOrderId)){
			$tmpUserId = bfi_get_userId();
			$model = new BookingForConnectorModelOrders;
			$currCart = $model->DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId);
			wp_redirect($url_cart_page);
			exit;

//			if(!empty($currCart)){
//				$return = json_encode($currCart);
//			}
		}else{
			$resources = BFCHelper::getSession('hdnOrderData', '', 'bfi-cart');
			if(!empty($resources)){
				$resources = json_decode($resources,true);
				unset($resources[$CartOrderId]);
				$resources = array_values($resources);
				$currResourcesStr = json_encode($resources);
				BFCHelper::setSession('hdnOrderData', $currResourcesStr, 'bfi-cart');
			}
			wp_redirect($url_cart_page);
			exit;
		
		}
		$base_url = get_site_url();
		if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
				global $sitepress;
				if($sitepress->get_current_language() != $sitepress->get_default_language()){
					$base_url = "/" .ICL_LANGUAGE_CODE;
				}
		}
		wp_redirect($base_url);
		exit;
	}

	public function SearchByText() {
		$return = '[]';
		$term = stripslashes(BFCHelper::getVar("bfi_term"));
		$maxresults = stripslashes(BFCHelper::getVar("bfi_maxresults"));
		$onlyLocations = stripslashes(BFCHelper::getVar("bfi_onlyLocations"));
		if(!isset($maxresults) || empty($maxresults)) {
			$maxresults = 5;
		} else {
			$maxresults = (int)$maxresults;
		}
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		if(!empty($term)) {
			$model = new BookingForConnectorModelSearch;
			$results = $model->SearchResult($term, $language, $maxresults, $onlyLocations);
			if(!empty($results)){
				$return = json_encode($results);
			}
		}
		echo $return;
	}
}
endif;